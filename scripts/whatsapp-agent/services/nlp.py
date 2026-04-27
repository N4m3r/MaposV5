import re
from typing import Tuple, Optional

# Dicionario de comandos conhecidos
COMANDOS = {
    'status_os': [
        'status da minha os', 'minha os', 'como esta minha os', 'os aberta',
        'minhas os', 'ordem de servico', 'status os', 'minhas ordens'
    ],
    'detalhes_os': [
        'detalhes da os', 'detalhe os', 'os numero', 'os #', 'informacoes da os',
        'dados da os', 'consultar os'
    ],
    'quanto_devo': [
        'quanto devo', 'quanto eu devo', 'minha divida', 'faturas em aberto',
        'contas em aberto', 'valor em aberto', 'quanto preciso pagar'
    ],
    'minhas_os_hoje': [
        'minhas os de hoje', 'os de hoje', 'minhas ordens hoje',
        'ordens de hoje', 'os atribuidas hoje', 'meu dia'
    ],
    'relatorio_os': [
        'relatorio de os', 'os do dia', 'quantas os hoje',
        'resumo de os', 'total de os', 'os abertas hoje'
    ],
    'os_atrasadas': [
        'os atrasadas', 'atrasadas', 'os em atraso', 'ordens atrasadas',
        'servicos atrasados'
    ],
    'vendas_pendentes': [
        'vendas pendentes', 'vendas nao faturadas', 'vendas em aberto'
    ],
    'cobrancas_vencidas': [
        'cobrancas vencidas', 'cobrancas atrasadas', 'contas vencidas'
    ],
    'criar_os': [
        'criar os', 'nova os', 'abrir os', 'cadastrar os',
        'criar ordem de servico', 'nova ordem'
    ],
    'ajuda': [
        'oi', 'ola', 'ajuda', 'menu', 'comandos', 'o que voce faz',
        'help', 'como usar', 'opcoes'
    ],
    'total_os_abertas': [
        'total os abertas', 'quantas os abertas', 'os em aberto'
    ],
}

# Regex para extrair numeros de OS
OS_NUMBER_RE = re.compile(r'#?\s*(\d+)')


def classificar(texto: str) -> Tuple[str, Optional[dict]]:
    """
    Classifica o texto em um comando conhecido.
    Retorna: (comando, parametros)
    """
    texto_lower = texto.lower().strip()

    # Remover pontuacao
    texto_limpo = re.sub(r'[^\w\s]', ' ', texto_lower)
    texto_limpo = re.sub(r'\s+', ' ', texto_limpo).strip()

    # Verificar cada comando
    for comando, palavras_chave in COMANDOS.items():
        for chave in palavras_chave:
            if chave in texto_lower or chave in texto_limpo:
                params = extrair_parametros(texto_lower, comando)
                return comando, params

    # Fallback: tentar detectar por palavras soltas
    palavras = set(texto_limpo.split())
    if 'os' in palavras and any(w in palavras for w in ['status', 'minha', 'aberta']):
        return 'status_os', {}
    if 'devo' in palavras or 'divida' in palavras:
        return 'quanto_devo', {}
    if 'ajuda' in palavras or 'help' in palavras:
        return 'ajuda', {}

    return 'desconhecido', {'texto_original': texto}


def extrair_parametros(texto: str, comando: str) -> dict:
    """Extrai parametros especificos do texto"""
    params = {}

    # Extrair numero de OS (se houver)
    match = OS_NUMBER_RE.search(texto)
    if match:
        params['os_id'] = int(match.group(1))

    # Extrair nome de cliente (para criar OS)
    if comando == 'criar_os':
        # Tenta extrair "para [nome]" ou "cliente [nome]"
        cliente_match = re.search(r'(?:para|cliente)\s+([\w\s]+?)(?:,|\s+defeito|$)', texto)
        if cliente_match:
            params['cliente_nome'] = cliente_match.group(1).strip()

        defeito_match = re.search(r'(?:defeito|problema)\s*[:\-]?\s*(.+?)(?:,|$)', texto)
        if defeito_match:
            params['defeito'] = defeito_match.group(1).strip()

    return params


def formatar_resposta(comando: str, dados: dict, usuario: dict = None) -> str:
    """Formata resposta para o usuario com base no comando"""
    tipo = usuario.get('tipo_vinculo', 'desconhecido') if usuario else 'desconhecido'
    nome = usuario.get('nome', 'Cliente') if usuario else 'Cliente'

    if comando == 'ajuda':
        if tipo == 'cliente':
            return f"""Ola {nome}! 👋

Eu sou o assistente virtual da JJ Ferreiras. Posso te ajudar com:

* status da minha os* - Ver suas ordens de servico
* quanto devo* - Valor em aberto

Em breve mais comandos!
"""
        elif tipo == 'tecnico':
            return f"""Ola {nome}! 🔧

Comandos disponiveis:

* minhas os de hoje* - Suas ordens do dia
* relatorio de os* - Resumo do dia

Mais funcoes em breve!
"""
        else:
            return f"""Ola {nome}! ⚙️

Comandos de admin:

* relatorio de os* - Total do dia
* os atrasadas* - Servicos em atraso
* vendas pendentes* - Vendas nao faturadas
* total os abertas* - Quantidade em aberto

Mais funcoes em breve!
"""

    elif comando == 'status_os':
        oss = dados.get('oss', [])
        if not oss:
            return f"Ola {nome}! Voce nao tem ordens de servico registradas."

        msg = f"Ola {nome}! Aqui estao suas OS:\n\n"
        for os in oss:
            msg += f"*OS #{os['idOs']}* - {os['status']}\n"
            msg += f"Equipamento: {os['descricaoProduto'] or 'Nao informado'}\n"
            if os.get('dataFinal'):
                msg += f"Previsao: {os['dataFinal']}\n"
            msg += "\n"
        return msg

    elif comando == 'quanto_devo':
        total = dados.get('total', 0)
        if total <= 0:
            return f"Ola {nome}! Voce nao tem valores em aberto. ✅"
        return f"Ola {nome}!\n\nVoce tem *R$ {total:.2f}* em aberto.\n\nEntre em contato para regularizar."

    elif comando == 'detalhes_os':
        os = dados.get('os')
        if not os:
            return "OS nao encontrada. Verifique o numero e tente novamente."
        msg = f"""*Detalhes da OS #{os['idOs']}*

Cliente: {os['nomeCliente']}
Equipamento: {os['descricaoProduto'] or 'Nao informado'}
Defeito: {os['defeito'] or 'Nao informado'}
Status: {os['status']}
Observacoes: {os['observacoes'] or 'Nenhuma'}
Laudo: {os['laudoTecnico'] or 'Nenhum'}
"""
        return msg

    elif comando == 'minhas_os_hoje':
        oss = dados.get('oss', [])
        if not oss:
            return f"Ola {nome}! Voce nao tem OS atribuidas para hoje. 🎉"

        msg = f"Suas OS de hoje:\n\n"
        for os in oss:
            msg += f"*OS #{os['idOs']}* - {os['status']}\n"
            msg += f"Cliente: {os['nomeCliente']}\n"
            msg += f"Equipamento: {os['descricaoProduto'] or 'Nao informado'}\n\n"
        return msg

    elif comando == 'relatorio_os':
        resumo = dados.get('resumo', [])
        if not resumo:
            return "Nenhuma OS registrada hoje."

        msg = "*Resumo de OS*\n\n"
        total = 0
        for r in resumo:
            msg += f"• {r['status']}: {r['quantidade']}\n"
            total += r['quantidade']
        msg += f"\n*Total: {total}*"
        return msg

    elif comando == 'os_atrasadas':
        oss = dados.get('oss', [])
        if not oss:
            return "Nenhuma OS em atraso! ✅"

        msg = f"*OS em Atraso ({len(oss)})*\n\n"
        for os in oss[:10]:
            msg += f"*OS #{os['idOs']}*\n"
            msg += f"Cliente: {os['nomeCliente']}\n"
            msg += f"Status: {os['status']}\n"
            msg += f"Equipamento: {os['descricaoProduto'] or 'Nao informado'}\n"
            msg += f"Previsao: {os['dataFinal']}\n\n"
        if len(oss) > 10:
            msg += f"... e mais {len(oss) - 10} OS."
        return msg

    elif comando == 'vendas_pendentes':
        vendas = dados.get('vendas', [])
        if not vendas:
            return "Nenhuma venda pendente! ✅"

        msg = f"*Vendas Pendentes ({len(vendas)})*\n\n"
        for v in vendas[:10]:
            msg += f"*Venda #{v['idVendas']}*\n"
            msg += f"Cliente: {v['nomeCliente']}\n"
            msg += f"Valor: R$ {v['valorTotal']:.2f}\n\n"
        return msg

    elif comando == 'cobrancas_vencidas':
        cobs = dados.get('cobrancas', [])
        if not cobs:
            return "Nenhuma cobranca vencida! ✅"

        msg = f"*Cobrancas Vencidas ({len(cobs)})*\n\n"
        for c in cobs[:10]:
            msg += f"*#{c['idCobranca']}* - {c['descricao'] or 'Sem descricao'}\n"
            msg += f"Cliente: {c['nomeCliente']}\n"
            msg += f"Valor: R$ {c['valor']:.2f}\n"
            msg += f"Vencimento: {c['data_vencimento']}\n\n"
        return msg

    elif comando == 'total_os_abertas':
        total = dados.get('total', 0)
        return f"Total de OS em aberto: *{total}*"

    elif comando == 'criar_os':
        return dados.get('mensagem', 'Comando de criar OS recebido.')

    elif comando == 'desconhecido':
        return f"""Desculpe {nome}, nao entendi seu comando. 🤔

Tente um destes:
• status da minha os
• quanto devo
• ajuda

Ou entre em contato com nossa equipe.
"""

    return "Comando processado."
