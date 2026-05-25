"""
Modulo de criacao interativa de OS (Ordem de Servico).

Extrai do main.py a logica de:
- Avanco de etapas da sessao de OS
- Processamento interativo passo-a-passo
- Criacao completa via audio
- Criacao via API do MapOS

Dependencias injetadas via init() para evitar imports circulares.
"""

import json
import re
import logging
import requests as http_requests

import config
from database import execute_insert, execute_update
from services.evolution_api import EvolutionAPI
from services.mapos_queries import MaposQueries
from services.session_store import SessionStore
from services.result import Result
from services import nlp
from services.nlp import _fmt_status_emoji
from services.llm import extrair_dados_os_audio, interpretar_audio_os

# --- Dependencias injetadas pelo main.py via init() ---
evo: EvolutionAPI | None = None
queries: MaposQueries | None = None
sessions: SessionStore | None = None
logger = logging.getLogger(__name__)

_sessao_lock = None  # threading.Lock, setado via init()

PERMISSOES_MAP = {
    1: 'admin',        # Administrador
    2: 'tecnico',      # Tecnico
    3: 'financeiro',   # Financeiro
    4: 'vendedor',     # Vendedor
    5: 'cliente',      # Cliente
    6: 'cliente',      # Cliente secundario
}


def init(evolution_api: EvolutionAPI, mapos_queries: MaposQueries,
         session_store: SessionStore, lock):
    """Inicializa as dependencias do modulo.

    Deve ser chamado pelo main.py apos criar as instancias.
    """
    global evo, queries, sessions, _sessao_lock
    evo = evolution_api
    queries = mapos_queries
    sessions = session_store
    _sessao_lock = lock


# ========== HELPERS DE SESSAO ==========

def _criar_sessao_os(numero: str, etapa: str, dados: dict, clientes: list = None):
    """Cria sessao de OS persistida no banco."""
    sessions.set_os_session(numero, etapa, dados, clientes)


def _get_sessao_os(numero: str) -> dict | None:
    """Recupera sessao de OS do banco."""
    return sessions.get_os_session(numero)


def _del_sessao_os(numero: str):
    """Remove sessao de OS do banco."""
    sessions.del_os_session(numero)


def _enviar_botoes_confirmacao(numero: str, title: str, description: str,
                                opcoes: list = None, footer: str = ''):
    """Envia botoes interativos de confirmacao.
    opcoes: lista de dicts [{'displayText': '...', 'id': '...'}] (max 3)
    Default: Confirmar / Cancelar
    """
    if not opcoes:
        opcoes = [
            {'displayText': '✅ Confirmar', 'id': 'CONFIRMAR'},
            {'displayText': '❌ Cancelar', 'id': 'CANCELAR'},
        ]
    buttons = [{'type': 'reply', 'displayText': o['displayText'], 'id': o['id']} for o in opcoes[:3]]
    return evo.enviar_botoes(numero, title, description, buttons, footer)


def eh_admin(usuario: dict) -> bool:
    """Verifica se o usuario e administrador (permissoes_id = 1 ou numero admin)."""
    if not usuario:
        return False
    numero = usuario.get('numero', '')
    if numero == config.ADMIN_NUMERO:
        return True
    return usuario.get('permissoes_id') == 1 or usuario.get('tipo') in ('admin', 'Administrador')


# ========== AVANCO DE ETAPAS ==========

def _avancar_etapa_os(numero: str, dados: dict, etapa_atual: str) -> str | None:
    """Verifica dados ja preenchidos e avanca para a proxima etapa vazia.
    Retorna a resposta se avancou automaticamente, ou None se precisa interagir com o usuario."""
    # Se defeito ja esta preenchido, pular para equipamento
    if etapa_atual == 'defeito' and dados.get('defeito'):
        # Verificar se equipamento tambem esta preenchido
        if dados.get('descricao') and dados['descricao'] != 'Nao especificado':
            # Tem defeito e equipamento — pular para produto/servico ou confirmar
            if dados.get('itens') or dados.get('_item_selecionado'):
                # Ja tem item, ir para valor ou confirmar
                if dados.get('valor_total') is not None:
                    # Tem tudo, ir para confirmar
                    dados_avancados = dict(dados)
                    _criar_sessao_os(numero, 'confirmar', dados_avancados)
                    resumo = (
                        f"👤 Cliente: *{dados.get('cliente_nome', 'Nao informado')}*\n"
                        f"🔧 Defeito: *{dados.get('defeito', 'Nao informado')}*\n"
                        f"📦 Equipamento: *{dados.get('descricao', 'Nao informado')}*"
                    )
                    if dados.get('_item_selecionado'):
                        resumo += f"\n📋 Item: *{dados['_item_selecionado']}*"
                    if dados.get('valor_total'):
                        resumo += f"\n💰 Valor: *R$ {dados['valor_total']:.2f}*"
                    _enviar_botoes_confirmacao(
                        numero, title='📝 Confirmar OS', description=resumo,
                        opcoes=[
                            {'displayText': '✅ Confirmar', 'id': 'CONFIRMAR'},
                            {'displayText': '❌ Cancelar', 'id': 'CANCELAR'},
                            {'displayText': '✏️ Corrigir cliente', 'id': 'CORRIGIR'},
                        ], footer='JJ Ferreiras'
                    )
                    return ''
                # Tem defeito, equipamento e item mas sem valor — pular para produto_servico
                dados_avancados = dict(dados)
                dados_avancados['defeito'] = dados.get('defeito', '')
                dados_avancados['descricao'] = dados.get('descricao', '')
                _criar_sessao_os(numero, 'produto_servico', dados_avancados)
                evo.enviar_botoes(numero,
                    title='📦 Produto/Servico',
                    description='Deseja adicionar um produto ou servico do catalogo?',
                    buttons=[
                        {'type': 'reply', 'displayText': '📦 Produto', 'id': 'TIPO_PRODUTO'},
                        {'type': 'reply', 'displayText': '🔧 Servico', 'id': 'TIPO_SERVICO'},
                        {'type': 'reply', 'displayText': '⏭ Pular', 'id': 'PULAR_ITEM'},
                    ], footer='JJ Ferreiras'
                )
                return '🎤 Dados ja preenchidos do audio!\nDeseja adicionar um *produto* ou *servico* do catalogo?\nDigite *produto*, *servico* ou *pular*.'
            # Tem defeito e equipamento mas sem item
            dados_avancados = dict(dados)
            dados_avancados['defeito'] = dados.get('defeito', '')
            dados_avancados['descricao'] = dados.get('descricao', '')
            _criar_sessao_os(numero, 'produto_servico', dados_avancados)
            evo.enviar_botoes(numero,
                title='📦 Produto/Servico',
                description='Deseja adicionar um produto ou servico do catalogo?',
                buttons=[
                    {'type': 'reply', 'displayText': '📦 Produto', 'id': 'TIPO_PRODUTO'},
                    {'type': 'reply', 'displayText': '🔧 Servico', 'id': 'TIPO_SERVICO'},
                    {'type': 'reply', 'displayText': '⏭ Pular', 'id': 'PULAR_ITEM'},
                ], footer='JJ Ferreiras'
            )
            return '🎤 Dados preenchidos do audio!\nDeseja adicionar um *produto* ou *servico*?\nDigite *produto*, *servico* ou *pular*.'

        # Tem defeito mas nao equipamento — pular para equipamento
        dados_avancados = dict(dados)
        dados_avancados['defeito'] = dados.get('defeito', '')
        _criar_sessao_os(numero, 'equipamento', dados_avancados)
        evo.enviar_botoes(numero,
            title=f'Defeito: {dados["defeito"][:50]}',
            description='Informe o equipamento ou produto:',
            buttons=[
                {'type': 'reply', 'displayText': '⏭ Pular equipamento', 'id': 'PULAR_EQUIP'},
            ], footer='JJ Ferreiras'
        )
        return f"✅ Defeito preenchido: *{dados['defeito']}*\n🔧 Informe o *equipamento/produto* (ou *pular*):"

    return None


# ========== CRIACAO COMPLETA VIA AUDIO ==========

def criar_os_completa_via_audio(numero: str, texto_audio: str, usuario: dict) -> str | None:
    """Tenta criar OS completa a partir de um unico audio usando LLM para extrair todos os dados.
    Retorna a mensagem de resposta ou None se nao conseguiu extrair dados suficientes."""
    if not config.LLM_PROVIDER:
        return None

    if not eh_admin(usuario):
        return "🔒 Apenas administradores podem criar OS."

    try:
        dados_os = extrair_dados_os_audio(texto_audio)
        if not dados_os:
            logger.info(f"Nao foi possivel extrair dados OS do audio: {texto_audio!r}")
            return None

        logger.info(f"Dados extraidos do audio: {dados_os}")
    except Exception as e:
        logger.warning(f"Erro ao extrair dados OS do audio: {e}")
        return None

    # Verificar se temos dados minimos (pelo menos cliente OU defeito)
    cliente_nome = dados_os.get('cliente')
    defeito = dados_os.get('defeito')
    equipamento = dados_os.get('equipamento')
    tipo_item = dados_os.get('tipo_item')
    nome_item = dados_os.get('nome_item')
    quantidade = dados_os.get('quantidade')
    valor = dados_os.get('valor')

    if not cliente_nome and not defeito:
        return None

    # Buscar cliente
    cliente_id = None
    cliente_final = None
    if cliente_nome:
        clientes = queries.buscar_cliente_por_nome(cliente_nome)
        if len(clientes) == 1:
            cliente_id = clientes[0]['idClientes']
            cliente_final = clientes[0].get('nomeExibicao', clientes[0]['nomeCliente'])
        elif len(clientes) > 1:
            # Multiplos clientes — nao conseguimos resolver automaticamente
            # Iniciar fluxo interativo com os dados que temos
            _criar_sessao_os(numero, 'escolher_cliente_lista', {
                'defeito': defeito or '',
                'descricao': equipamento or '',
            }, clientes[:10])
            sections = [{
                'title': f'{len(clientes)} clientes encontrados',
                'rows': [
                    {'title': c.get('nomeExibicao', c['nomeCliente']),
                     'description': f'ID: {c["idClientes"]}',
                     'rowId': f'cliente_{c["idClientes"]}'}
                    for c in clientes[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o cliente', 'Qual cliente deseja?', 'Escolher', sections)
            return f"🎤 Encontrei {len(clientes)} clientes para *\"{cliente_nome}\"*.\nSelecione o cliente correto:"
        else:
            # Cliente nao encontrado — iniciar fluxo pedindo o cliente
            _criar_sessao_os(numero, 'cliente', {
                'defeito': defeito or '',
                'descricao': equipamento or '',
            })
            return f"🎤 Nao encontrei o cliente *\"{cliente_nome}\"*.\n\nQual o nome, CNPJ ou Loja do cliente?"

    # Se nao tem cliente e o usuario e cliente, usar o proprio ID
    if not cliente_id and usuario.get('tipo') == 'cliente' and usuario.get('clientes_id'):
        cliente_id = usuario['clientes_id']
        cliente_final = usuario['nome']

    if not cliente_id:
        _criar_sessao_os(numero, 'cliente', {
            'defeito': defeito or '',
            'descricao': equipamento or '',
        })
        return "🎤 Qual o *nome do cliente* para a OS?"

    # Buscar produto/servico no catalogo se mencionado
    itens = []
    item_selecionado = None
    if nome_item and tipo_item:
        if tipo_item == 'produto':
            produtos = queries.buscar_produtos(nome_item, limite=1)
            if produtos:
                p = produtos[0]
                preco = float(p.get('precoVenda', 0) or 0)
                qtd = quantidade or 1
                itens = [{
                    'tipo': 'produto',
                    'id': p['idProdutos'],
                    'descricao': p['descricao'],
                    'preco': preco,
                    'quantidade': qtd,
                }]
                item_selecionado = f"{p['descricao']} x{qtd} — R$ {preco * qtd:.2f}"
        elif tipo_item == 'servico':
            servicos = queries.buscar_servicos(nome_item, limite=1)
            if servicos:
                s = servicos[0]
                preco = float(s.get('preco', 0) or 0)
                qtd = quantidade or 1
                itens = [{
                    'tipo': 'servico',
                    'id': s['idServicos'],
                    'descricao': s['nome'],
                    'preco': preco,
                    'quantidade': qtd,
                }]
                item_selecionado = f"{s['nome']} x{qtd} — R$ {preco * qtd:.2f}"

    # Usar valor informado ou calcular do item
    if valor is None and itens:
        valor = sum(i['preco'] * i['quantidade'] for i in itens)

    # Criar a OS
    usuario_id = usuario.get('usuarios_id') or 1
    try:
        resultado = criar_os_via_api(
            cliente_id=cliente_id,
            descricao=equipamento or 'Nao especificado',
            defeito=defeito or '',
            usuario_id=usuario_id,
            valor_total=valor,
            itens=itens if itens else None,
        )

        if resultado and resultado.get('sucesso') is not False:
            os_id = resultado.get('os_id') or resultado
            os_data = resultado.get('os') or queries.buscar_os(os_id)

            resposta = f"""✅ *OS #{os_id} criada com sucesso!*

━━━━━━━━━━━━━━━━━
👤 Cliente: {os_data.get('nomeCliente', cliente_final or '')}
🔧 Defeito: {os_data.get('defeito', defeito or '')}
📦 Equipamento: {os_data.get('descricaoProduto', equipamento or 'Nao especificado')}"""
            if item_selecionado:
                resposta += f"\n📋 Item: *{item_selecionado}*"
            if valor:
                resposta += f"\n💰 Valor: *R$ {valor:.2f}*"
            resposta += f"""
📋 Status: {os_data.get('status', 'Aberto')}
📅 Data: {os_data.get('dataInicial', 'Hoje')}
━━━━━━━━━━━━━━━━━

Digite *detalhes da OS {os_id}* para acompanhar."""

            # Tentar gerar e enviar PDF da OS
            mapos_url = config.MAPOS_URL.rstrip('/')
            api_key = config.MAPOS_API_KEY
            if mapos_url and api_key:
                try:
                    pdf_url = f"{mapos_url}/api/v2/relatorios/exportar"
                    resp = http_requests.post(pdf_url, data={
                        'tipo': 'os_periodo',
                        'formato': 'pdf',
                    }, headers={
                        'X-API-KEY': api_key,
                    }, timeout=30)
                    if resp.status_code == 200:
                        data_resp = resp.json()
                        if data_resp.get('success'):
                            download_url = data_resp.get('data', {}).get('download_url', '')
                            if download_url:
                                _enviar_pdf_whatsapp(numero, download_url, f'OS #{os_id}')
                except Exception as e:
                    logger.warning(f"Erro ao gerar PDF da OS: {e}")

            return resposta
        else:
            return "❌ Erro ao criar OS. Tente novamente ou digite *ajuda*."

    except Exception as e:
        logger.error(f"Erro ao criar OS via audio: {e}")
        return f"❌ Erro ao criar OS: {str(e)}\n\nTente novamente ou digite *ajuda*."


def _enviar_pdf_whatsapp(numero: str, pdf_url: str, caption: str = 'Relatorio'):
    """Baixa o PDF e envia via WhatsApp."""
    if not pdf_url:
        return Result.fail('URL vazia')

    try:
        # Baixar o PDF
        resp = http_requests.get(pdf_url, timeout=60)
        if resp.status_code != 200:
            logger.error(f"Erro ao baixar PDF: status={resp.status_code}")
            return Result.fail(f'HTTP {resp.status_code}')

        # Salvar temporariamente
        import tempfile
        import os
        tmp = tempfile.NamedTemporaryFile(suffix='.pdf', delete=False)
        tmp.write(resp.content)
        tmp.close()

        # Enviar via Evolution API
        try:
            result = evo.enviar_documento(numero, tmp.name, caption)
        finally:
            try:
                os.unlink(tmp.name)
            except Exception:
                pass

        return result
    except Exception as e:
        logger.error(f"Erro ao enviar PDF: {e}")
        return Result.fail(str(e))


# ========== CRIACAO INTERATIVA DE OS ==========

def processar_criacao_os(numero: str, texto: str, usuario: dict) -> str:
    """Processa o fluxo interativo de criacao de OS passo a passo."""

    # Verificar se ha sessao ativa
    with _sessao_lock:
        sessao = sessions.get_os_session(numero)
        if sessao and '_clientes' in sessao.get('dados', {}):
            sessao['clientes'] = sessao['dados'].pop('_clientes')

    # Comando para cancelar
    texto_limpo = texto.lower().strip()
    if texto_limpo in ('cancelar', 'cancela', 'desistir', 'abandonar', '#cancelar'):
        sessions.del_os_session(numero)
        return "❌ Criacao de OS cancelada.\n\nDigite *ajuda* para ver as opcoes."

    # Se nao ha sessao, iniciar fluxo
    if not sessao:
        # Buscar clientes que correspondem ao texto (se informou nome)
        cliente_nome = None
        cliente_id = None
        defeito = None
        descricao = None

        # Tentar extrair dados da mensagem inicial
        params = nlp.extrair_parametros(texto, 'criar_os')

        # Extracao inteligente de cliente e defeito da mensagem completa
        _texto_lower = texto.lower().strip()
        import re as _re

        # Verificar se mencionou "loja X" para clientes multi-filial
        loja_num = None
        loja_match = _re.search(r'loja\s*(\d+)', _texto_lower)
        if not loja_match:
            loja_match = _re.search(r'(?:nova\s*era|patio\s*gourmet)\s+(\d+)', _texto_lower)
        if loja_match:
            loja_num = int(loja_match.group(1))

        # Tentar extrair nome do cliente de forma mais ampla
        if not params.get('cliente_nome'):
            # Padroes: "criar os para joao silva", "os para maria", "nova os cliente pedro"
            cliente_match = _re.search(
                r'(?:para|cliente|pra|do|da)\s+([\w\sáàãâéèêíïóòõôúùûçñ]+?)(?:\s*[-,;]|\s+defeito|\s+problema|\s+com\s|$)',
                _texto_lower
            )
            if cliente_match:
                nome_extraido = cliente_match.group(1).strip()
                # Limpar palavras de ligacao
                nome_limpo = _re.sub(r'\b(os|para|cliente|nova|nova ordem|ordem|servico|criar|abrir|cadastrar)\b', '', nome_extraido).strip()
                if len(nome_limpo) >= 3:
                    params['cliente_nome'] = nome_limpo

        # Tentar extrair defeito de forma mais ampla se nao achou
        if not params.get('defeito'):
            defeito_match = _re.search(
                r'(?:defeito|problema|queixa|reclamacao|assunto)\s*[:\-]?\s*(.+?)(?:,|$)',
                _texto_lower
            )
            if defeito_match:
                params['defeito'] = defeito_match.group(1).strip()

        if params.get('cliente_nome'):
            cliente_nome = params['cliente_nome']
            # Buscar cliente no banco
            clientes = queries.buscar_cliente_por_nome(cliente_nome)
            if len(clientes) == 1:
                cliente_id = clientes[0]['idClientes']
                cliente_nome = clientes[0]['nomeCliente']
            elif len(clientes) > 1 and loja_num:
                # Para multi-filial, buscar loja especifica usando _extrair_loja_cnpj
                for c in clientes:
                    loja = queries._extrair_loja_cnpj(c.get('documento', '') or '')
                    if loja == f'Loja {loja_num}':
                        cliente_id = c['idClientes']
                        cliente_nome = c['nomeExibicao'] if c.get('nomeExibicao') else c['nomeCliente']
                        break
            elif len(clientes) > 1:
                # Multiplos clientes - enviar lista interativa
                sections = [{
                    'title': f'{len(clientes)} clientes encontrados',
                    'rows': [
                        {'title': c.get('nomeExibicao', c['nomeCliente']),
                         'description': f'ID: {c["idClientes"]}',
                         'rowId': f'cliente_{c["idClientes"]}'}
                        for c in clientes[:10]
                    ]
                }]
                evo.enviar_lista(numero, 'Selecione o cliente', 'Qual cliente deseja?', 'Escolher', sections)
                # Salvar clientes na sessao mapeados por id
                clientes_map = {str(c['idClientes']): c for c in clientes[:10]}
                _criar_sessao_os(numero, 'escolher_cliente_lista', {'defeito': params.get('defeito', '')}, clientes[:10])
                sessao_temp = sessions.get_os_session(numero)
                if sessao_temp:
                    sessao_temp['dados']['_clientes_map'] = {k: {'idClientes': v['idClientes'], 'nomeCliente': v['nomeCliente']} for k, v in clientes_map.items()}
                    sessions.set_os_session(numero, sessao_temp['etapa'], sessao_temp['dados'], sessao_temp.get('clientes'))
                return ''

        if params.get('defeito'):
            defeito = params['defeito']

        # Se ja temos o cliente, ir direto para defeito/equipamento
        if cliente_id:
            _criar_sessao_os(numero, 'defeito', {
                'cliente_id': cliente_id,
                'cliente_nome': cliente_nome,
                'defeito': defeito or '',
                'descricao': '',
            })
            if defeito:
                # Ja tem cliente e defeito, pular para equipamento
                _criar_sessao_os(numero, 'equipamento', {
                    'cliente_id': cliente_id,
                    'cliente_nome': cliente_nome,
                    'defeito': defeito,
                    'descricao': '',
                })
                return f"""✅ Cliente: *{cliente_nome}*
🔧 Defeito: *{defeito}*

Informe o *equipamento/produto* (ou digite *pular*):"""
            return f"""✅ Cliente: *{cliente_nome}*

📝 Descreva o *defeito* ou problema relatado:"""

        # Se o usuario e cliente, usar o proprio ID
        if usuario.get('tipo') == 'cliente' and usuario.get('clientes_id'):
            _criar_sessao_os(numero, 'defeito', {
                'cliente_id': usuario['clientes_id'],
                'cliente_nome': usuario['nome'],
                'defeito': '',
                'descricao': '',
            })
            return f"""✅ {usuario['nome']}, vou criar uma OS para voce.

📝 Descreva o *defeito* ou problema:"""

        # Pedir nome do cliente
        _criar_sessao_os(numero, 'cliente', {
            'defeito': defeito or '',
            'descricao': '',
        })
        return """📝 *Nova Ordem de Servico*

Qual o *nome do cliente*?
(Digite o nome, CNPJ, ID ou *Loja X*)"""

    # Processar etapa da sessao
    etapa = sessao['etapa']
    dados = sessao.get('dados', {})

    if etapa == 'escolher_cliente_lista':
        # Usuario escolheu da lista interativa (rowId = cliente_ID)
        clientes = sessao.get('clientes', [])
        cliente_encontrado = None

        # rowId vem como "cliente_42" ou o proprio numero
        texto_limpo_id = texto_limpo.replace('cliente_', '').strip()
        for c in clientes:
            if str(c['idClientes']) == texto_limpo_id:
                cliente_encontrado = c
                break

        if cliente_encontrado:
            dados['cliente_id'] = cliente_encontrado['idClientes']
            dados['cliente_nome'] = cliente_encontrado['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], sessao.get('clientes'))
            # Verificar se defeito/equipamento ja estao preenchidos
            pulou = _avancar_etapa_os(numero, dados, 'defeito')
            if pulou is not None:
                return pulou
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""

        # Fallback: tentar como indice numerico
        try:
            idx = int(texto_limpo) - 1
            if 0 <= idx < len(clientes):
                cliente_encontrado = clientes[idx]
        except (ValueError, IndexError):
            pass

        if cliente_encontrado:
            dados['cliente_id'] = cliente_encontrado['idClientes']
            dados['cliente_nome'] = cliente_encontrado['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], sessao.get('clientes'))
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""

        # Tentar buscar pelo nome digitado
        clientes_busca = queries.buscar_cliente_por_nome(texto.strip())
        if len(clientes_busca) == 1:
            dados['cliente_id'] = clientes_busca[0]['idClientes']
            dados['cliente_nome'] = clientes_busca[0]['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""

        return "❌ Cliente nao encontrado.\n\nDigite o *nome*, *CNPJ/CPF*, *ID* ou *Loja X*.\nExemplo: *Nova Era*, *04240370005701*, *Loja 57*\n\nOu *cancelar* para desistir."

    if etapa == 'escolher_cliente':
        # Usuario escolheu um cliente da lista numerada
        try:
            idx = int(texto_limpo) - 1
            clientes = sessao.get('clientes', [])
            if 0 <= idx < len(clientes):
                dados['cliente_id'] = clientes[idx]['idClientes']
                dados['cliente_nome'] = clientes[idx]['nomeCliente']
                sessao['etapa'] = 'defeito'
                _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], sessao.get('clientes'))
                return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""
        except (ValueError, IndexError):
            pass

        # Tentar buscar pelo nome digitado
        clientes = queries.buscar_cliente_por_nome(texto.strip())
        if len(clientes) == 1:
            dados['cliente_id'] = clientes[0]['idClientes']
            dados['cliente_nome'] = clientes[0]['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], sessao.get('clientes'))
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""
        elif len(clientes) > 1:
            # Enviar lista interativa
            sections = [{
                'title': f'{len(clientes)} clientes encontrados',
                'rows': [
                    {'title': c.get('nomeExibicao', c['nomeCliente']),
                     'description': f'ID: {c["idClientes"]}',
                     'rowId': f'cliente_{c["idClientes"]}'}
                    for c in clientes[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o cliente', 'Qual cliente deseja?', 'Escolher', sections)
            sessao['clientes'] = clientes[:10]
            sessao['etapa'] = 'escolher_cliente_lista'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], clientes[:10])
            return ''

        return "❌ Cliente nao encontrado.\n\nDigite o *nome*, *CNPJ/CPF*, *ID* ou *Loja X*.\nExemplo: *Nova Era*, *04240370005701*, *Loja 57*\n\nOu *cancelar* para desistir."

    elif etapa == 'cliente':
        # Buscar cliente pelo nome informado
        clientes = queries.buscar_cliente_por_nome(texto.strip())
        if len(clientes) == 1:
            dados['cliente_id'] = clientes[0]['idClientes']
            dados['cliente_nome'] = clientes[0]['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            # Verificar se defeito/equipamento ja estao preenchidos (ex: audio)
            pulou = _avancar_etapa_os(numero, dados, 'defeito')
            if pulou is not None:
                return pulou
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""
        elif len(clientes) > 1:
            # Enviar lista interativa
            sections = [{
                'title': f'{len(clientes)} clientes encontrados',
                'rows': [
                    {'title': c.get('nomeExibicao', c['nomeCliente']),
                     'description': f'ID: {c["idClientes"]}',
                     'rowId': f'cliente_{c["idClientes"]}'}
                    for c in clientes[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o cliente', 'Qual cliente deseja?', 'Escolher', sections)
            sessao['etapa'] = 'escolher_cliente_lista'
            sessao['clientes'] = clientes[:10]
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], clientes[:10])
            return ''
        else:
            return "❌ Cliente nao encontrado.\n\nDigite o *nome*, *CNPJ/CPF*, *ID* ou *Loja X*.\nExemplo: *Nova Era*, *04240370005701*, *Loja 57*\n\nOu *cancelar* para desistir."

    elif etapa == 'defeito':
        dados['defeito'] = texto.strip()
        sessao['etapa'] = 'equipamento'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
        # Enviar botoes para equipamento (pular ou informar)
        evo.enviar_botoes(numero,
            title=f'Defeito: {dados["defeito"][:50]}',
            description='Informe o equipamento ou produto:',
            buttons=[
                {'type': 'reply', 'displayText': '⏭ Pular equipamento', 'id': 'PULAR_EQUIP'},
            ],
            footer='JJ Ferreiras'
        )
        return '🔧 Informe o *equipamento/produto* (ou digite *pular*):'

    elif etapa == 'equipamento':
        if texto_limpo in ('pular', 'nao sei', 'nenhum', '-', 'pular_equip'):
            dados['descricao'] = 'Nao especificado'
        else:
            dados['descricao'] = texto.strip()

        sessao['etapa'] = 'produto_servico'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])

        # Enviar botoes para produto/servico
        evo.enviar_botoes(numero,
            title='📦 Produto/Servico',
            description='Deseja adicionar um produto ou servico do catalogo?',
            buttons=[
                {'type': 'reply', 'displayText': '📦 Produto', 'id': 'TIPO_PRODUTO'},
                {'type': 'reply', 'displayText': '🔧 Servico', 'id': 'TIPO_SERVICO'},
                {'type': 'reply', 'displayText': '⏭ Pular', 'id': 'PULAR_ITEM'},
            ],
            footer='JJ Ferreiras'
        )
        return 'Deseja adicionar um *produto* ou *servico* do catalogo?\nDigite *produto*, *servico* ou *pular*.'

    elif etapa == 'produto_servico':
        # Escolha do tipo: produto ou servico
        if texto_limpo in ('pular', 'nao', 'nenhum', 'pular_item'):
            # Pular item, ir direto para valor total
            dados['itens'] = []
            sessao['etapa'] = 'valor'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            evo.enviar_botoes(numero,
                title='💰 Valor da OS',
                description='Informe o valor total ou pule:',
                buttons=[
                    {'type': 'reply', 'displayText': '⏭ Sem valor', 'id': 'PULAR_VALOR'},
                ],
                footer='JJ Ferreiras'
            )
            return '💰 Informe o *valor total* da OS (ex: 150.00) ou digite *pular*:'

        elif texto_limpo in ('produto', 'produtos', 'tipo_produto', 'item'):
            dados['_tipo_item'] = 'produto'
            sessao['etapa'] = 'buscar_item'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return '🔍 Digite o *nome do produto* para buscar no catalogo:'

        elif texto_limpo in ('servico', 'servicos', 'tipo_servico'):
            dados['_tipo_item'] = 'servico'
            sessao['etapa'] = 'buscar_item'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return '🔍 Digite o *nome do servico* para buscar no catalogo:'

        else:
            # Tentar detectar automaticamente
            sessao['etapa'] = 'buscar_item'
            dados['_tipo_item'] = 'produto'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return '🔍 Digite o *nome do produto ou servico* para buscar no catalogo:'

    elif etapa == 'buscar_item':
        tipo_item = dados.get('_tipo_item', 'produto')
        termo = texto.strip()

        if tipo_item == 'produto':
            itens = queries.buscar_produtos(termo, limite=10)
            if not itens:
                return f"❌ Nenhum produto encontrado para *\"{termo}\"*.\n\nTente outro nome ou digite *pular* para prosseguir sem item."
            if len(itens) == 1:
                item = itens[0]
                preco = float(item['precoVenda'] or 0)
                dados['itens'] = [{
                    'tipo': 'produto',
                    'id': item['idProdutos'],
                    'descricao': item['descricao'],
                    'preco': preco,
                    'quantidade': 1,
                }]
                dados['_item_selecionado'] = f"📦 {item['descricao']} — R$ {preco:.2f}"
                sessao['etapa'] = 'quantidade_item'
                _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
                return f"""✅ Produto: *{item['descricao']}*
💰 Preco: *R$ {preco:.2f}*

Digite a *quantidade* (ou *1*):"""
            # Multiplos produtos - enviar lista interativa
            sections = [{
                'title': f'{len(itens)} produtos encontrados',
                'rows': [
                    {'title': f"{item['descricao'][:40]}",
                     'description': f"R$ {float(item['precoVenda'] or 0):.2f}",
                     'rowId': f"prod_{item['idProdutos']}"}
                    for item in itens[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o produto', 'Escolha um produto:', 'Escolher', sections)
            dados['_tipo_item'] = 'produto'
            sessao['etapa'] = 'selecionar_item'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return ''

        else:  # servico
            itens = queries.buscar_servicos(termo, limite=10)
            if not itens:
                return f"❌ Nenhum servico encontrado para *\"{termo}\"*.\n\nTente outro nome ou digite *pular* para prosseguir sem item."
            if len(itens) == 1:
                item = itens[0]
                preco = float(item['preco'] or 0)
                dados['itens'] = [{
                    'tipo': 'servico',
                    'id': item['idServicos'],
                    'descricao': item['nome'],
                    'preco': preco,
                    'quantidade': 1,
                }]
                dados['_item_selecionado'] = f"🔧 {item['nome']} — R$ {preco:.2f}"
                sessao['etapa'] = 'quantidade_item'
                _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
                return f"""✅ Servico: *{item['nome']}*
💰 Preco: *R$ {preco:.2f}*

Digite a *quantidade* (ou *1*):"""
            # Multiplos servicos - enviar lista interativa
            sections = [{
                'title': f'{len(itens)} servicos encontrados',
                'rows': [
                    {'title': f"{item['nome'][:40]}",
                     'description': f"R$ {float(item['preco'] or 0):.2f}",
                     'rowId': f"serv_{item['idServicos']}"}
                    for item in itens[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o servico', 'Escolha um servico:', 'Escolher', sections)
            dados['_tipo_item'] = 'servico'
            sessao['etapa'] = 'selecionar_item'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return ''

    elif etapa == 'selecionar_item':
        # Usuario selecionou item da lista interativa
        tipo_item = dados.get('_tipo_item', 'produto')
        item_encontrado = None

        # Parsear rowId: prod_42 ou serv_7
        if texto_limpo.startswith('prod_'):
            prod_id = int(texto_limpo.replace('prod_', ''))
            item_encontrado = queries.buscar_produto_por_id(prod_id)
            if item_encontrado:
                tipo_item = 'produto'
        elif texto_limpo.startswith('serv_'):
            serv_id = int(texto_limpo.replace('serv_', ''))
            item_encontrado = queries.buscar_servico_por_id(serv_id)
            if item_encontrado:
                tipo_item = 'servico'
        else:
            # Fallback: tentar busca por texto
            if tipo_item == 'produto':
                itens = queries.buscar_produtos(texto.strip(), limite=1)
                item_encontrado = itens[0] if itens else None
            else:
                itens = queries.buscar_servicos(texto.strip(), limite=1)
                item_encontrado = itens[0] if itens else None

        if not item_encontrado:
            return "❌ Item nao encontrado.\n\nTente novamente ou digite *pular* para prosseguir sem item."

        if tipo_item == 'produto':
            preco = float(item_encontrado.get('precoVenda', 0) or 0)
            dados['itens'] = [{
                'tipo': 'produto',
                'id': item_encontrado['idProdutos'],
                'descricao': item_encontrado['descricao'],
                'preco': preco,
                'quantidade': 1,
            }]
            dados['_item_selecionado'] = f"📦 {item_encontrado['descricao']} — R$ {preco:.2f}"
        else:
            preco = float(item_encontrado.get('preco', 0) or 0)
            dados['itens'] = [{
                'tipo': 'servico',
                'id': item_encontrado['idServicos'],
                'descricao': item_encontrado['nome'],
                'preco': preco,
                'quantidade': 1,
            }]
            dados['_item_selecionado'] = f"🔧 {item_encontrado['nome']} — R$ {preco:.2f}"

        sessao['etapa'] = 'quantidade_item'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
        return f"""✅ Selecionado: *{dados['_item_selecionado']}*

Digite a *quantidade* (ou *1*):"""

    elif etapa == 'quantidade_item':
        # Receber quantidade do item
        try:
            qtd = int(texto_limpo)
            if qtd < 1:
                qtd = 1
        except ValueError:
            qtd = 1

        subtotal = 0
        if dados.get('itens'):
            dados['itens'][0]['quantidade'] = qtd
            item = dados['itens'][0]
            subtotal = round(qtd * item['preco'], 2)
            dados['_item_selecionado'] = f"{item['descricao']} x{qtd} — R$ {subtotal:.2f}"

        # Ir para etapa de valor total
        sessao['etapa'] = 'valor'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
        evo.enviar_botoes(numero,
            title='💰 Valor da OS',
            description=f'Subtotal: R$ {subtotal:.2f}\nInforme o valor total ou pule:',
            buttons=[
                {'type': 'reply', 'displayText': f'💰 Usar R$ {subtotal:.2f}', 'id': f'VALOR_{subtotal:.2f}'},
                {'type': 'reply', 'displayText': '⏭ Sem valor', 'id': 'PULAR_VALOR'},
            ],
            footer='JJ Ferreiras'
        )
        return f'💰 Informe o *valor total* da OS ou digite *pular* para prosseguir sem valor:'

    elif etapa == 'valor':
        # Receber valor total da OS
        valor_total = None

        # Parsear valor: "150.00", "R$ 150", "pular", "VALOR_150.00"
        if texto_limpo.startswith('valor_'):
            try:
                valor_total = float(texto_limpo.replace('valor_', '').strip())
            except ValueError:
                pass
        elif texto_limpo in ('pular', 'nao', 'nenhum', 'pular_valor', 'sem valor'):
            valor_total = None
        else:
            # Tentar extrair numero do texto
            valor_str = re.sub(r'[R$\s]', '', texto_limpo).replace(',', '.')
            try:
                valor_total = float(valor_str)
                if valor_total < 0:
                    valor_total = None
            except ValueError:
                return "❌ Valor invalido.\n\nDigite o valor (ex: *150.00*) ou *pular* para prosseguir sem valor."

        dados['valor_total'] = valor_total
        sessao['etapa'] = 'confirmar'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])

        # Montar resumo para confirmacao
        resumo_texto = (
            f"👤 Cliente: *{dados.get('cliente_nome', 'Nao informado')}*\n"
            f"🔧 Defeito: *{dados.get('defeito', 'Nao informado')}*\n"
            f"📦 Equipamento: *{dados.get('descricao', 'Nao informado')}*"
        )
        if dados.get('_item_selecionado'):
            resumo_texto += f"\n📋 Item: *{dados['_item_selecionado']}*"
        if dados.get('valor_total'):
            resumo_texto += f"\n💰 Valor: *R$ {dados['valor_total']:.2f}*"

        _enviar_botoes_confirmacao(
            numero,
            title='📝 Confirmar OS',
            description=resumo_texto,
            opcoes=[
                {'displayText': '✅ Confirmar', 'id': 'CONFIRMAR'},
                {'displayText': '❌ Cancelar', 'id': 'CANCELAR'},
                {'displayText': '✏️ Corrigir cliente', 'id': 'CORRIGIR'},
            ],
            footer='JJ Ferreiras'
        )
        return ''

    elif etapa == 'confirmar':
        if texto_limpo in ('confirmar', 'confirmo', 'sim', 'confirma', 'ok', 'criar'):
            # Criar a OS via API do MapOS (com fallback para SQL direto)
            usuario_id = usuario.get('usuarios_id') or 1
            try:
                resultado = criar_os_via_api(
                    cliente_id=dados.get('cliente_id'),
                    descricao=dados.get('descricao', 'Nao especificado'),
                    defeito=dados.get('defeito', ''),
                    usuario_id=usuario_id,
                    valor_total=dados.get('valor_total'),
                    itens=dados.get('itens'),
                )

                if resultado and resultado.get('sucesso') is not False:
                    os_id = resultado.get('os_id') or resultado
                    os_data = resultado.get('os') or queries.buscar_os(os_id)

                    # Limpar sessao apos sucesso
                    _del_sessao_os(numero)

                    resposta = f"""✅ *OS #{os_id} criada com sucesso!*

━━━━━━━━━━━━━━━━━
👤 Cliente: {os_data.get('nomeCliente', dados.get('cliente_nome', ''))}
🔧 Defeito: {os_data.get('defeito', dados.get('defeito', ''))}
📦 Equipamento: {os_data.get('descricaoProduto', dados.get('descricao', ''))}"""
                    if dados.get('_item_selecionado'):
                        resposta += f"\n📋 Item: *{dados['_item_selecionado']}*"
                    if dados.get('valor_total'):
                        resposta += f"\n💰 Valor: *R$ {dados['valor_total']:.2f}*"
                    resposta += f"""
📋 Status: {os_data.get('status', 'Aberto')}
📅 Data: {os_data.get('dataInicial', 'Hoje')}
━━━━━━━━━━━━━━━━━

Digite *detalhes da OS {os_id}* para acompanhar."""

                    # Tentar gerar e enviar PDF da OS
                    mapos_url = config.MAPOS_URL.rstrip('/')
                    api_key = config.MAPOS_API_KEY
                    if mapos_url and api_key:
                        try:
                            pdf_url = f"{mapos_url}/api/v2/relatorios/exportar"
                            resp = http_requests.post(pdf_url, data={
                                'tipo': 'os_periodo',
                                'formato': 'pdf',
                            }, headers={
                                'X-API-KEY': api_key,
                            }, timeout=30)

                            if resp.status_code == 200:
                                data_resp = resp.json()
                                if data_resp.get('success'):
                                    download_url = data_resp.get('data', {}).get('download_url', '')
                                    if download_url:
                                        _enviar_pdf_whatsapp(numero, download_url, f'OS #{os_id}')
                        except Exception as e:
                            logger.warning(f"Erro ao gerar PDF da OS: {e}")

                    return resposta
                else:
                    _del_sessao_os(numero)
                    return "❌ Erro ao criar OS. Tente novamente ou digite *ajuda*."

            except Exception as e:
                logger.error(f"Erro ao criar OS: {e}")
                _del_sessao_os(numero)
                return f"❌ Erro ao criar OS: {str(e)}\n\nTente novamente ou digite *ajuda*."

        elif texto_limpo in ('nao', 'corrigir', 'alterar', 'editar', 'corrigir cliente'):
            sessao['etapa'] = 'cliente'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return "🔄 Vamos corrigir. Qual o *nome do cliente*?"

        else:
            return "Digite *CONFIRMAR* para criar a OS ou *CANCELAR* para desistir."

    # Fallback - limpar sessao
    _del_sessao_os(numero)
    return "❌ Fluxo de criacao encerrado.\n\nDigite *ajuda* para ver as opcoes."


# ========== CRIACAO VIA MAPOS API ==========

def criar_os_via_api(cliente_id: int, descricao: str = '', defeito: str = '',
                     usuario_id: int = None, observacoes: str = '', status: str = 'Aberto',
                     valor_total: float = None, itens: list = None) -> dict:
    """Cria OS via API do MapOS (unifica com o fluxo PHP)."""
    mapos_url = config.MAPOS_URL.rstrip('/')
    api_key = config.MAPOS_API_KEY

    if not mapos_url or not api_key:
        logger.warning("MAPOS_URL ou MAPOS_API_KEY nao configurados — criando via SQL direto")
        return queries.criar_os(
            cliente_id=cliente_id, descricao=descricao, defeito=defeito,
            usuario_id=usuario_id or 1, observacoes=observacoes, status=status,
            valor_total=valor_total, itens=itens
        )

    url = f"{mapos_url}/api/v2/acoes/executar"
    payload_dados = {
        'clientes_id': cliente_id,
        'descricaoProduto': descricao or 'Nao especificado',
        'defeito': defeito or '',
        'usuarios_id': usuario_id or 1,
        'status': status,
        'observacoes': observacoes or '',
    }
    if valor_total is not None:
        payload_dados['valorTotal'] = valor_total
    if itens:
        payload_dados['itens'] = itens

    payload = {
        'acao': 'criar_os',
        'dados': json.dumps(payload_dados),
    }
    try:
        resp = http_requests.post(url, data=payload, headers={'X-API-KEY': api_key}, timeout=15)
        if resp.status_code == 200:
            data = resp.json()
            if data.get('success'):
                resultado = data.get('data', {}).get('resultado', {})
                os_id = resultado.get('os_id')
                if os_id:
                    os_data = queries.buscar_os(os_id)
                    return {
                        'sucesso': True,
                        'os_id': os_id,
                        'os': os_data,
                        'via_api': True
                    }
        logger.warning(f"Criacao OS via API falhou (status {resp.status_code}), tentando SQL direto")
    except Exception as e:
        logger.warning(f"Excecao ao criar OS via API: {e}, tentando SQL direto")

    # Fallback: SQL direto
    return queries.criar_os(
        cliente_id=cliente_id, descricao=descricao, defeito=defeito,
        usuario_id=usuario_id or 1, observacoes=observacoes, status=status,
        valor_total=valor_total, itens=itens
    )