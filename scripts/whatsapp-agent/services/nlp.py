import re
import logging
from typing import Tuple, Optional

from services.user_profile import get_perfil

logger = logging.getLogger(__name__)

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
        'criar ordem de servico', 'criar ordem de serviço',
        'nova ordem', 'abrir ordem',
        'abri uma os', 'quero uma os', 'preciso de uma os',
        'fazer uma os', 'gerar uma os',
        'os para', 'os pra',
        'ordem de serviço', 'ordem de servico',
    ],
    'ajuda': [
        'oi', 'ola', 'ajuda', 'menu', 'comandos', 'o que voce faz',
        'help', 'como usar', 'opcoes', 'bom dia', 'boa tarde', 'boa noite',
        'hey', 'eai', 'fala'
    ],
    'sair': [
        'sair', '#sair', 'encerrar', 'tchau', 'bye', 'obrigado', 'valeu',
        'ades', 'ate mais', 'ate logo', 'fim'
    ],
    'total_os_abertas': [
        'total os abertas', 'quantas os abertas', 'os em aberto'
    ],
    # === NOVOS COMANDOS DE RELATORIO ===
    'relatorio_financeiro': [
        'relatorio financeiro', 'resumo financeiro', 'financeiro',
        'receitas e despesas', 'lucro', 'balanco', 'fluxo de caixa',
        'financeiro do mes', 'financeiro do dia'
    ],
    'relatorio_vendas': [
        'relatorio de vendas', 'vendas do mes', 'vendas do periodo',
        'resumo vendas', 'relatorio vendas'
    ],
    'relatorio_estoque': [
        'relatorio de estoque', 'estoque', 'produtos em estoque',
        'estoque baixo', 'alerta de estoque', 'estoque atual'
    ],
    'relatorio_produtividade': [
        'produtividade', 'produtividade dos tecnicos', 'desempenho',
        'performance tecnicos', 'relatorio de produtividade', 'tecnicos'
    ],
    'relatorio_clientes_top': [
        'top clientes', 'melhores clientes', 'clientes que mais trazem',
        'clientes mais frequentes', 'ranking clientes'
    ],
    'relatorio_os_periodo': [
        'relatorio os periodo', 'os do mes', 'os da semana',
        'os por periodo', 'os do periodo', 'relatorio mensal'
    ],
    'relatorio_atrasados': [
        'relatorio atrasados', 'clientes em atraso', 'quem esta atrasado',
        'resumo atrasados'
    ],
    'os_finalizadas_mes': [
        'os finalizadas', 'os finalizadas mes', 'os concluidas',
        'cobranca mes', 'cobranca', 'relatorio cobranca',
        'quantas os finalizadas', 'os faturadas', 'faturamento mes',
        'os fechadas', 'servicos concluidos', 'cobrar clientes',
        'os do mes finalizadas', 'relatorio cobranca mes'
    ],
    'alterar_status_os': [
        'alterar status', 'mudar status', 'alterar status da os',
        'mudar status da os', 'atualizar os', 'status da os', 'mudar status os',
        'alterar os', 'atualizar status', 'andamento os', 'finalizar os',
        'concluir os', 'cancelar os', 'aprovar os', 'em andamento'
    ],
    'checkin_tecnico': [
        'cheguei', 'checkin', 'cheguei no cliente', 'inicio atendimento',
        'comecei atendimento', 'estou no local', 'iniciar os',
        'cheguei na os'
    ],
    'checkout_tecnico': [
        'saindo', 'checkout', 'fim atendimento', 'terminei atendimento',
        'finalizando atendimento', 'saindo do cliente', 'concluir atendimento'
    ],
}

# Regex para extrair numeros de OS
OS_NUMBER_RE = re.compile(r'#?\s*(\d+)')


def classificar(texto: str) -> Tuple[str, Optional[dict]]:
    """
    Classifica o texto em um comando conhecido (fallback regex).
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
    if 'financeiro' in palavras or 'financeira' in palavras:
        return 'relatorio_financeiro', {}
    if 'vendas' in palavras:
        return 'relatorio_vendas', {}
    if 'estoque' in palavras:
        return 'relatorio_estoque', {}
    if 'produtividade' in palavras or 'tecnico' in palavras:
        return 'relatorio_produtividade', {}
    if 'criar' in palavras or 'nova' in palavras or 'abrir' in palavras or 'abri' in palavras:
        return 'criar_os', {}
    if 'ordem' in palavras and ('serviço' in palavras or 'servico' in palavras):
        return 'criar_os', {}
    if 'status' in palavras and any(w in palavras for w in ['alterar', 'mudar', 'atualizar', 'trocar']):
        return 'alterar_status_os', {}
    if 'finalizar' in palavras or 'concluir' in palavras or 'cancelar' in palavras:
        # Se mencionou OS, provavelmente quer alterar status
        if 'os' in palavras:
            return 'alterar_status_os', {}
    if 'cheguei' in palavras or 'checkin' in palavras or 'inicio' in palavras:
        return 'checkin_tecnico', {}
    if 'cobranca' in palavras or 'faturamento' in palavras or 'cobrar' in palavras:
        return 'os_finalizadas_mes', {}
    if 'finalizada' in palavras or 'finalizadas' in palavras or 'concluidas' in palavras or 'fechadas' in palavras:
        if 'os' in palavras or 'mes' in palavras:
            return 'os_finalizadas_mes', {}
    if 'saindo' in palavras or 'checkout' in palavras or 'terminei' in palavras or 'fim atendimento' in texto_limpo:
        return 'checkout_tecnico', {}

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
        cliente_match = re.search(r'(?:para|cliente)\s+([\w\s]+?)(?:,|\s+defeito|$)', texto)
        if cliente_match:
            params['cliente_nome'] = cliente_match.group(1).strip()

        defeito_match = re.search(r'(?:defeito|problema)\s*[:\-]?\s*(.+?)(?:,|$)', texto)
        if defeito_match:
            params['defeito'] = defeito_match.group(1).strip()

    return params


def _fmt_moeda(valor) -> str:
    """Formata valor monetario de forma compacta para WhatsApp."""
    v = float(valor or 0)
    if v >= 1_000_000:
        return f"R$ {v/1_000_000:.1f}M"
    if v >= 1_000:
        return f"R$ {v/1_000:.1f}K"
    return f"R$ {v:,.2f}".replace(',', '_').replace('.', ',').replace('_', '.')


def _fmt_status_emoji(status: str) -> str:
    """Retorna emoji correspondente ao status da OS."""
    s = (status or '').lower()
    if 'aberto' in s:
        return '🟡'
    if 'andamento' in s:
        return '🔵'
    if 'aguard' in s or 'peca' in s or 'peça' in s:
        return '🟠'
    if 'finalizado' in s or 'conclu' in s:
        return '🟢'
    if 'cancelado' in s:
        return '🔴'
    if 'faturado' in s:
        return '✅'
    if 'aprovado' in s:
        return '👍'
    if 'orcamento' in s or 'orçamento' in s:
        return '📋'
    return '⚪'


def _fmt_data(data: str) -> str:
    """Formata data para formato brasileiro amigavel."""
    if not data:
        return '-'
    d = str(data).strip()
    # Se ja esta em formato dd/mm/yyyy
    if '/' in d:
        return d
    # Se esta em formato yyyy-mm-dd
    if '-' in d and len(d) >= 10:
        partes = d[:10].split('-')
        if len(partes) == 3:
            return f"{partes[2]}/{partes[1]}/{partes[3][:2] if len(partes[2]) > 2 else partes[2]}"
    return d


def formatar_resposta(comando: str, dados: dict, usuario: dict = None) -> str:
    """Formata resposta otimizada para WhatsApp: concisa, escaneavel, visual."""
    nome = usuario.get('nome', 'Cliente') if usuario else 'Cliente'
    primeiro_nome = nome.split()[0] if nome else 'Cliente'

    perfil = get_perfil(usuario)

    # ===== AJUDA =====
    if comando == 'ajuda':
        if perfil == 'cliente':
            return (
                f"Ola {primeiro_nome}! 👋 *JJ Ferreiras*\n\n"
                "Como posso ajudar?\n\n"
                "📋 *status da minha os*\n"
                "→ Veja suas ordens de servico\n\n"
                "💰 *quanto devo*\n"
                "→ Consultar valor em aberto\n\n"
                "🔍 *detalhes da OS* + numero\n"
                "→ Ver detalhes de uma OS"
            )
        elif perfil == 'tecnico':
            return (
                f"Ola {primeiro_nome}! 🔧 *JJ Ferreiras*\n\n"
                "Comandos disponiveis:\n\n"
                "📋 *minhas os de hoje* — Suas OS do dia\n"
                "🔍 *detalhes da OS 42* — Detalhes de uma OS\n"
                "📊 *relatorio de os* — Resumo do dia\n"
                "⚠️ *os atrasadas* — Servicos em atraso\n"
                "👷 *produtividade* — Desempenho da equipe\n"
                "📍 *cheguei na OS 42* — Check-in\n"
                "🚪 *saindo da OS 42* — Check-out"
            )
        else:
            return (
                f"Ola {primeiro_nome}! ⚙️ *JJ Ferreiras*\n\n"
                "━━━━ *Operacional* ━━━━\n"
                "📊 *relatorio de os* — Resumo do dia\n"
                "⚠️ *os atrasadas* — Servicos em atraso\n"
                "📋 *total os abertas* — Quantidade em aberto\n"
                "🔍 *detalhes da OS 42* — Ver OS especifica\n"
                "📝 *criar os* — Nova ordem de servico\n"
                "🔄 *alterar status* — Mudar status de OS\n\n"
                "━━━━ *Financeiro* ━━━━\n"
                "📈 *relatorio financeiro* — Receitas e despesas\n"
                "📊 *relatorio vendas* — Vendas do periodo\n"
                "📄 *cobrancas vencidas* — Cobrancas atrasadas\n"
                "💰 *vendas pendentes* — Nao faturadas\n\n"
                "━━━━ *Gestao* ━━━━\n"
                "📦 *relatorio estoque* — Produtos e alertas\n"
                "👷 *produtividade* — Desempenho dos tecnicos\n"
                "🏆 *top clientes* — Clientes que mais trazem\n"
                "📅 *os do mes* — OS por periodo\n"
                "📋 *relatorio atrasados* — Clientes em atraso\n"
                "💰 *os finalizadas* — OS concluidas para cobranca\n\n"
                "📍 *cheguei na OS 42* — Check-in\n"
                "🚪 *saindo da OS 42* — Check-out"
            )

    # ===== STATUS OS =====
    elif comando == 'status_os':
        oss = dados.get('oss', [])
        if not oss:
            return f"{primeiro_nome}, voce nao tem ordens de servico registradas. 📋"

        linhas = []
        for i, os in enumerate(oss[:8], 1):
            emoji = _fmt_status_emoji(os.get('status', ''))
            equip = os.get('descricaoProduto') or 'Sem equipamento'
            linha = f"{emoji} *#{os['idOs']}* {os['status']}\n   {equip}"
            if os.get('dataFinal'):
                linha += f" · Prev: {_fmt_data(os['dataFinal'])}"
            linhas.append(linha)

        msg = f"📋 *Suas OS* — {primeiro_nome}\n\n" + "\n\n".join(linhas)
        if len(oss) > 8:
            msg += f"\n\n... +{len(oss) - 8} outras"
        return msg

    # ===== QUANTO DEVO =====
    elif comando == 'quanto_devo':
        total = dados.get('total', 0)
        if total <= 0:
            return f"{primeiro_nome}, voce esta em dia! ✅ Nenhum valor em aberto."
        return f"{primeiro_nome}, voce tem *{_fmt_moeda(total)}* em aberto.\n\nEntre em contato para regularizar."

    # ===== DETALHES OS =====
    elif comando == 'detalhes_os':
        os = dados.get('os')
        if not os:
            return "❌ OS nao encontrada. Verifique o numero e tente novamente."
        emoji = _fmt_status_emoji(os.get('status', ''))
        msg = (
            f"{emoji} *OS #{os['idOs']}* — {os['status']}\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"👤 Cliente: *{os['nomeCliente']}*\n"
            f"🔧 Equipamento: {os.get('descricaoProduto') or 'Nao informado'}\n"
            f"📝 Defeito: {os.get('defeito') or 'Nao informado'}\n"
        )
        if os.get('observacoes'):
            msg += f"📌 Obs: {os['observacoes']}\n"
        if os.get('laudoTecnico'):
            msg += f"📋 Laudo: {os['laudoTecnico']}\n"
        if os.get('dataInicial'):
            msg += f"📅 Abertura: {_fmt_data(os['dataInicial'])}\n"
        if os.get('dataFinal'):
            msg += f"⏰ Previsao: {_fmt_data(os['dataFinal'])}\n"
        return msg

    # ===== MINHAS OS HOJE =====
    elif comando == 'minhas_os_hoje':
        oss = dados.get('oss', [])
        if not oss:
            return f"🎉 {primeiro_nome}, nenhuma OS atribuida para hoje!"

        linhas = []
        for os in oss[:8]:
            emoji = _fmt_status_emoji(os.get('status', ''))
            equip = os.get('descricaoProduto') or 'Sem equipamento'
            linhas.append(f"{emoji} *#{os['idOs']}* — {os['status']}\n   {os.get('nomeCliente', '')} · {equip}")

        msg = f"📋 *OS de Hoje* — {primeiro_nome}\n\n" + "\n\n".join(linhas)
        if len(oss) > 8:
            msg += f"\n\n... +{len(oss) - 8} outras"
        return msg

    # ===== RELATORIO OS =====
    elif comando == 'relatorio_os':
        resumo = dados.get('resumo', [])
        if not resumo:
            return "Nenhuma OS registrada hoje."

        total = 0
        linhas = []
        for r in resumo:
            emoji = _fmt_status_emoji(r.get('status', ''))
            linhas.append(f"{emoji} {r['status']}: *{r['quantidade']}*")
            total += r['quantidade']
        msg = f"📊 *Resumo de OS*\n\n" + "\n".join(linhas) + f"\n\nTotal: *{total}*"
        return msg

    # ===== OS ATRASADAS =====
    elif comando == 'os_atrasadas':
        oss = dados.get('oss', [])
        if not oss:
            return "✅ Nenhuma OS em atraso!"

        linhas = []
        for os in oss[:8]:
            equip = os.get('descricaoProduto') or 'Sem equipamento'
            linhas.append(
                f"🔴 *#{os['idOs']}* {os['status']}\n"
                f"   {os['nomeCliente']} · {equip}\n"
                f"   Prev: {_fmt_data(os.get('dataFinal'))}"
            )
        msg = f"⚠️ *OS em Atraso* ({len(oss)})\n\n" + "\n\n".join(linhas)
        if len(oss) > 8:
            msg += f"\n\n... +{len(oss) - 8} outras"
        return msg

    # ===== VENDAS PENDENTES =====
    elif comando == 'vendas_pendentes':
        vendas = dados.get('vendas', [])
        if not vendas:
            return "✅ Nenhuma venda pendente!"

        linhas = []
        for v in vendas[:8]:
            linhas.append(f"📦 *#{v['idVendas']}* {v['nomeCliente']} — {_fmt_moeda(v.get('valorTotal', 0))}")
        msg = f"💰 *Vendas Pendentes* ({len(vendas)})\n\n" + "\n".join(linhas)
        if len(vendas) > 8:
            msg += f"\n\n... +{len(vendas) - 8} outras"
        return msg

    # ===== COBRANCAS VENCIDAS =====
    elif comando == 'cobrancas_vencidas':
        cobs = dados.get('cobrancas', [])
        if not cobs:
            return "✅ Nenhuma cobranca vencida!"

        linhas = []
        for c in cobs[:8]:
            desc = c.get('descricao') or c.get('nomeCliente', 'Sem descricao')
            linhas.append(
                f"📄 *#{c['idCobranca']}* {desc}\n"
                f"   {_fmt_moeda(c['valor'])} · Venc: {_fmt_data(c.get('data_vencimento'))}"
            )
        msg = f"⚠️ *Cobrancas Vencidas* ({len(cobs)})\n\n" + "\n\n".join(linhas)
        if len(cobs) > 8:
            msg += f"\n\n... +{len(cobs) - 8} outras"
        return msg

    # ===== TOTAL OS ABERTAS =====
    elif comando == 'total_os_abertas':
        total = dados.get('total', 0)
        return f"📋 Total de OS em aberto: *{total}*"

    # ===== SAIR =====
    elif comando == 'sair':
        return f"Ate logo, {primeiro_nome}! 👋\n\nQuando precisar, e so mandar uma mensagem.\nDigite *ajuda* para ver as opcoes."

    # ===== CRIAR OS / ALTERAR STATUS / CHECKIN / CHECKOUT =====
    elif comando == 'criar_os':
        return dados.get('mensagem', 'Comando de criar OS recebido.')
    elif comando == 'alterar_status_os':
        return dados.get('mensagem', 'Comando de alterar status recebido.')
    elif comando == 'checkin_tecnico':
        return dados.get('mensagem', 'Check-in registrado!')
    elif comando == 'checkout_tecnico':
        return dados.get('mensagem', 'Check-out registrado!')

    # ===== RELATORIO FINANCEIRO =====
    elif comando == 'relatorio_financeiro':
        r = dados.get('resumo', {})
        if not r:
            return "Nenhum dado financeiro encontrado no periodo."

        receita = float(r.get('total_receita', 0) or 0)
        despesa = float(r.get('total_despesa', 0) or 0)
        lucro = float(r.get('lucro', 0) or 0)
        lucro_emoji = '📈' if lucro >= 0 else '📉'

        msg = (
            f"📈 *Relatorio Financeiro*\n"
            f"{_fmt_data(dados.get('periodo', {}).get('inicio', ''))} a "
            f"{_fmt_data(dados.get('periodo', {}).get('fim', ''))}\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"💰 Receitas: {_fmt_moeda(receita)}\n"
            f"💸 Despesas: {_fmt_moeda(despesa)}\n"
            f"{lucro_emoji} *Lucro: {_fmt_moeda(lucro)}*\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"⏳ A receber: {_fmt_moeda(r.get('a_receber', 0))}\n"
            f"✅ Recebido: {_fmt_moeda(r.get('recebido', 0))}\n"
            f"📋 OS no periodo: {r.get('total_os_periodo', 0)}"
        )
        por_status = r.get('os_por_status', {})
        if por_status:
            msg += "\n\n📊 *Por status:* " + ' · '.join(f"{k}: {v}" for k, v in por_status.items())

        top = dados.get('top_clientes', [])
        if top:
            msg += "\n\n🏆 *Top Clientes:*"
            for i, c in enumerate(top[:5], 1):
                msg += f"\n{i}. {c['nomeCliente']} — {_fmt_moeda(c.get('total', 0))}"

        if dados.get('pdf_url'):
            msg += "\n\n📄 PDF gerado! Aguarde o envio."
        return msg

    # ===== RELATORIO VENDAS =====
    elif comando == 'relatorio_vendas':
        r = dados.get('resumo', {})
        if not r:
            return "Nenhuma venda encontrada no periodo."

        msg = (
            f"📊 *Relatorio de Vendas*\n"
            f"{_fmt_data(dados.get('periodo', {}).get('inicio', ''))} a "
            f"{_fmt_data(dados.get('periodo', {}).get('fim', ''))}\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"📦 Total de vendas: *{r.get('total_vendas', 0)}*\n"
            f"💰 Valor total: {_fmt_moeda(r.get('valor_total', 0))}\n"
            f"📊 Ticket medio: {_fmt_moeda(r.get('ticket_medio', 0))}\n"
            f"✅ Faturadas: {r.get('faturadas', 0)} · ⏳ Pendentes: {r.get('pendentes', 0)}"
        )
        if dados.get('pdf_url'):
            msg += "\n\n📄 PDF gerado! Aguarde o envio."
        return msg

    # ===== RELATORIO ESTOQUE =====
    elif comando == 'relatorio_estoque':
        r = dados.get('resumo', {})
        if not r:
            return "Nenhum dado de estoque encontrado."

        baixo = int(r.get('baixo_minimo', 0) or 0)
        alerta_emoji = '⚠️' if baixo > 0 else '✅'
        msg = (
            f"📦 *Relatorio de Estoque*\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"📋 Total de produtos: {r.get('total_produtos', 0)}\n"
            f"💰 Valor em estoque: {_fmt_moeda(r.get('valor_estoque', 0))}\n"
            f"{alerta_emoji} Abaixo do minimo: *{baixo}*"
        )
        alertas = dados.get('alertas', [])
        if alertas:
            msg += "\n\n⚠️ *Estoque Critico:*"
            for a in alertas[:6]:
                estoque = a.get('estoque', 0)
                minimo = a.get('estoqueMinimo', 0)
                msg += f"\n• {a['descricao']}: {estoque} (min: {minimo})"

        if dados.get('pdf_url'):
            msg += "\n\n📄 PDF gerado! Aguarde o envio."
        return msg

    # ===== RELATORIO PRODUTIVIDADE =====
    elif comando == 'relatorio_produtividade':
        r = dados.get('resumo', {})
        tecnicos = dados.get('tecnicos', [])
        if not tecnicos:
            return "Nenhum dado de produtividade encontrado."

        msg = (
            f"👷 *Produtividade da Equipe*\n"
            f"{_fmt_data(dados.get('periodo', {}).get('inicio', ''))} a "
            f"{_fmt_data(dados.get('periodo', {}).get('fim', ''))}\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"📋 Total de OS: {r.get('total_os', 0)}\n"
            f"💰 Valor total: {_fmt_moeda(r.get('total_valor', 0))}\n\n"
        )
        for t in tecnicos[:8]:
            msg += (
                f"👷 {t['nome']}\n"
                f"   {t.get('total_os', 0)} OS · ✅ {t.get('finalizadas', 0)} concluidas · "
                f"{_fmt_moeda(t.get('valor_total', 0))}\n"
            )
        if dados.get('pdf_url'):
            msg += "\n📄 PDF gerado! Aguarde o envio."
        return msg

    # ===== RELATORIO CLIENTES TOP =====
    elif comando == 'relatorio_clientes_top':
        r = dados.get('resumo', {})
        clientes = dados.get('clientes', [])
        if not clientes:
            return "Nenhum cliente encontrado com OS."

        msg = (
            f"🏆 *Top Clientes*\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"Clientes com OS: {r.get('total_clientes', 0)} · Total de OS: {r.get('total_os', 0)}\n\n"
        )
        for i, c in enumerate(clientes[:10], 1):
            medal = ['🥇', '🥈', '🥉'][i-1] if i <= 3 else f"{i}."
            msg += f"{medal} {c['nomeCliente']} — {c.get('total_os', 0)} OS · {_fmt_moeda(c.get('valor_total', 0))}\n"

        if dados.get('pdf_url'):
            msg += "\n\n📄 PDF gerado! Aguarde o envio."
        return msg

    # ===== RELATORIO OS PERIODO =====
    elif comando == 'relatorio_os_periodo':
        r = dados.get('resumo', {})
        oss = dados.get('os', [])
        if not oss and not r:
            return "Nenhuma OS encontrada no periodo."

        msg = (
            f"📅 *OS por Periodo*\n"
            f"{_fmt_data(dados.get('periodo', {}).get('inicio', ''))} a "
            f"{_fmt_data(dados.get('periodo', {}).get('fim', ''))}\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"📋 Total: *{r.get('total_os', 0)}* OS\n"
            f"💰 Valor: {_fmt_moeda(r.get('total_valor', 0))}\n"
            f"📊 Media: {_fmt_moeda(r.get('media_valor', 0))} por OS\n"
        )
        por_status = r.get('por_status', {})
        if por_status:
            msg += "\n📊 *Por status:*\n"
            for status, qtd in por_status.items():
                emoji = _fmt_status_emoji(status)
                msg += f"{emoji} {status}: *{qtd}*\n"

        if dados.get('pdf_url'):
            msg += "\n📄 PDF gerado! Aguarde o envio."
        return msg

    # ===== RELATORIO ATRASADOS =====
    elif comando == 'relatorio_atrasados':
        r = dados.get('resumo', {})
        top = dados.get('top_atrasados', [])
        if not r:
            return "✅ Nenhuma OS em atraso!"

        total_atrasadas = r.get('total_os_atrasadas', 0)
        msg = (
            f"⚠️ *Clientes em Atraso*\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"Total de OS atrasadas: *{total_atrasadas}*\n"
        )
        por_status = r.get('por_status', {})
        if por_status:
            msg += "\n"
            for status, qtd in por_status.items():
                msg += f"{_fmt_status_emoji(status)} {status}: {qtd}\n"

        if top:
            msg += "\n🏆 *Mais atrasados:*"
            for c in top[:5]:
                msg += f"\n• {c['nomeCliente']} — {c.get('qtd_atrasadas', 0)} OS (desde {_fmt_data(c.get('mais_antiga'))})"

        if dados.get('pdf_url'):
            msg += "\n\n📄 PDF gerado! Aguarde o envio."
        return msg

    # ===== OS FINALIZADAS / COBRANCA =====
    elif comando == 'os_finalizadas_mes':
        r = dados.get('resumo', {})
        por_cliente = dados.get('por_cliente', [])
        oss = dados.get('os', [])
        if r.get('total_os', 0) == 0:
            return "✅ Nenhuma OS finalizada no periodo."

        msg = (
            f"💰 *OS Finalizadas — Cobranca*\n"
            f"{_fmt_data(dados.get('periodo', {}).get('inicio', ''))} a "
            f"{_fmt_data(dados.get('periodo', {}).get('fim', ''))}\n"
            f"━━━━━━━━━━━━━━━━━\n"
            f"📋 Total: *{r.get('total_os', 0)}* OS\n"
            f"💰 Valor total: {_fmt_moeda(r.get('total_valor', 0))}\n"
            f"👥 Clientes: {r.get('total_clientes', 0)}"
        )
        if por_cliente:
            # Montar lista de OS por cliente
            os_por_cliente = {}
            for o in oss:
                cid = o.get('idClientes')
                if cid not in os_por_cliente:
                    os_por_cliente[cid] = []
                os_por_cliente[cid].append(o)

            msg += "\n\n📊 *Por cliente:*"
            for c in por_cliente[:10]:
                nums = os_por_cliente.get(c['idClientes'], [])
                nums_str = ', '.join(f"#{o['idOs']}" for o in nums)
                msg += f"\n• {c['nomeCliente']}: {c['qtd_os']} OS · {_fmt_moeda(c['total_valor'])}"
                msg += f"\n  OS: {nums_str}"

        if dados.get('pdf_url'):
            msg += "\n\n📄 PDF gerado! Aguarde o envio."
        return msg

    # ===== DESCONHECIDO =====
    elif comando == 'desconhecido':
        return (
            f"Desculpe {primeiro_nome}, nao entendi. 🤔\n\n"
            "Tente um destes comandos:\n"
            "• *status da minha os*\n"
            "• *quanto devo*\n"
            "• *relatorio financeiro*\n"
            "• *relatorio vendas*\n"
            "• *relatorio estoque*\n"
            "• *produtividade*\n"
            "• *os finalizadas* — cobranca\n"
            "• *ajuda* — ver todos os comandos"
        )

    return "Comando processado."