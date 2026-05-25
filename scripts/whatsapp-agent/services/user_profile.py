"""
Modulo de perfil de usuario e menus interativos.

Extrai de main.py as funcoes de identificacao, autorizacao
(eh_admin, eh_admin_ou_tecnico, eh_cliente) e construcao/envio
de menus interativos via Evolution API.
"""
import logging

from database import execute_query, execute_insert
import config
from services.result import Result

# Dependencias injetadas via init()
evo = None
queries = None
sessions = None
logger = logging.getLogger(__name__)

# Numero do administrador (acesso total) -- lido do .env
ADMIN_NUMERO = config.ADMIN_NUMERO

# Mapeamento de permissoes_id para perfil de acesso
PERMISSOES_MAP = {
    1: 'admin',        # Administrador
    2: 'tecnico',      # Tecnico
    3: 'financeiro',   # Financeiro
    4: 'vendedor',     # Vendedor
    5: 'cliente',      # Cliente
    6: 'cliente',      # Cliente secundario
}


def init(evo_api, mapos_queries, session_store):
    """Inicializa dependencias do modulo.

    Args:
        evo_api: instancia de EvolutionAPI
        mapos_queries: instancia de MaposQueries
        session_store: instancia de SessionStore
    """
    global evo, queries, sessions
    evo = evo_api
    queries = mapos_queries
    sessions = session_store


# ---- Autorizacao ----

def eh_admin(usuario: dict) -> bool:
    """Verifica se o usuario e administrador (permissoes_id = 1 ou numero admin)."""
    if not usuario:
        return False
    numero = usuario.get('numero', '')
    if numero == ADMIN_NUMERO:
        return True
    return usuario.get('permissoes_id') == 1 or usuario.get('tipo') in ('admin', 'Administrador')


def eh_admin_ou_tecnico(usuario: dict) -> bool:
    """Verifica se o usuario e admin ou tecnico (pode acessar relatorios e alterar OS)."""
    if not usuario:
        return False
    numero = usuario.get('numero', '')
    if numero == ADMIN_NUMERO:
        return True
    pid = usuario.get('permissoes_id')
    if pid and int(pid) in (1, 2):
        return True
    return usuario.get('tipo') in ('admin', 'tecnico', 'Administrador', 'Tecnico')


def eh_cliente(usuario: dict) -> bool:
    """Verifica se o usuario e cliente (so pode ver seus proprios dados)."""
    if not usuario:
        return False
    return usuario.get('tipo') == 'cliente' or (
        usuario.get('permissoes_id') and int(usuario.get('permissoes_id', 0)) in (5, 6)
    )


# ---- Identificacao e auto-registro ----

def limpar_numero(numero: str) -> str:
    """Remove nao-digitos e adiciona prefixo 55 se necessario."""
    numero = ''.join(filter(str.isdigit, numero))
    if len(numero) == 11 or len(numero) == 10:
        numero = '55' + numero
    return numero


def identificar_usuario(numero: str):
    """Identifica se o numero pertence a um admin, tecnico ou cliente.
    Permissoes derivadas do permissoes_id do usuarios, nao do campo tipo_vinculo.
    Admin numero 92992150107 sempre tem acesso total.
    """
    numero = limpar_numero(numero)

    # Admin master - acesso total independente do cadastro
    if numero == ADMIN_NUMERO:
        # Buscar dados do admin no banco se existir
        sql_admin = """
            SELECT u.idUsuarios, u.nome, u.celular, u.permissoes_id,
                   p.nome as permissao_nome
            FROM usuarios u
            LEFT JOIN permissoes p ON p.idPermissao = u.permissoes_id
            WHERE u.permissoes_id = 1 AND u.situacao = 1
            LIMIT 1
        """
        admin_rows = execute_query(sql_admin)
        if admin_rows:
            admin = admin_rows[0]
            return {
                'tipo': 'admin',
                'tipo_vinculo': 'admin',
                'clientes_id': None,
                'usuarios_id': admin.get('idUsuarios'),
                'permissoes_id': 1,
                'nome': admin.get('nome', 'Administrador'),
                'numero': numero
            }
        # Fallback: admin nao encontrado no banco, mas numero reconhecido
        return {
            'tipo': 'admin',
            'tipo_vinculo': 'admin',
            'clientes_id': None,
            'usuarios_id': None,
            'permissoes_id': 1,
            'nome': 'Administrador',
            'numero': numero
        }

    # Buscar na tabela whatsapp_integracao com JOIN completo
    sql = """
        SELECT w.*,
               c.idClientes as cli_id, c.nomeCliente as nome_cliente, c.celular as cli_celular,
               u.idUsuarios as usr_id, u.nome as nome_usuario, u.celular as usr_celular,
               u.permissoes_id, p.nome as permissao_nome
        FROM whatsapp_integracao w
        LEFT JOIN clientes c ON c.idClientes = w.clientes_id
        LEFT JOIN usuarios u ON u.idUsuarios = w.usuarios_id
        LEFT JOIN permissoes p ON p.idPermissao = u.permissoes_id
        WHERE w.numero_telefone = :numero AND w.situacao = 1
        LIMIT 1
    """
    rows = execute_query(sql, {'numero': numero})

    if rows:
        row = rows[0]
        permissoes_id = row.get('permissoes_id')
        usuarios_id = row.get('usr_id') or row.get('usuarios_id')
        clientes_id = row.get('cli_id') or row.get('clientes_id')

        # Se tem usuario do sistema, determinar tipo pelo permissoes_id
        if usuarios_id and permissoes_id:
            perfil = PERMISSOES_MAP.get(int(permissoes_id), 'desconhecido')
            nome = row.get('nome_usuario') or row.get('nome') or 'Usuario'

            # Admin tambem herda acesso de cliente se tiver clientes_id
            return {
                'tipo': perfil,
                'tipo_vinculo': perfil,
                'clientes_id': clientes_id if perfil == 'cliente' else None,
                'usuarios_id': usuarios_id,
                'permissoes_id': int(permissoes_id),
                'nome': nome,
                'numero': numero
            }

        # Se so tem cliente vinculado
        if clientes_id:
            return {
                'tipo': 'cliente',
                'tipo_vinculo': 'cliente',
                'clientes_id': clientes_id,
                'usuarios_id': None,
                'permissoes_id': None,
                'nome': row.get('nome_cliente') or row.get('nomeCliente') or 'Cliente',
                'numero': numero
            }

        # Tem registro mas sem vinculacao clara - usar tipo_vinculo antigo
        tipo_vinculo = row.get('tipo_vinculo', 'desconhecido')
        return {
            'tipo': tipo_vinculo,
            'tipo_vinculo': tipo_vinculo,
            'clientes_id': row.get('clientes_id'),
            'usuarios_id': row.get('usuarios_id'),
            'permissoes_id': None,
            'nome': row.get('nome_usuario') or row.get('nome_cliente') or row.get('nome') or 'Usuario',
            'numero': numero
        }

    # Segunda tentativa: buscar numero diretamente na tabela usuarios (celular)
    sql_usr = """
        SELECT u.idUsuarios, u.nome, u.celular, u.permissoes_id,
               p.nome as permissao_nome
        FROM usuarios u
        LEFT JOIN permissoes p ON p.idPermissao = u.permissoes_id
        WHERE REPLACE(REPLACE(REPLACE(u.celular, '(', ''), ')', ''), '-', '') LIKE :busca
           OR u.celular = :celular
           OR CONCAT('55', u.celular) = :numero
        LIMIT 1
    """
    usr_rows = execute_query(sql_usr, {
        'busca': f'%{numero[-11:]}%',
        'celular': numero[-11:] if len(numero) > 10 else numero,
        'numero': numero
    })
    if usr_rows:
        usr = usr_rows[0]
        perm_id = int(usr.get('permissoes_id', 0) or 0)
        perfil = PERMISSOES_MAP.get(perm_id, 'desconhecido')

        # Cadastrar automaticamente na whatsapp_integracao
        try:
            execute_insert("""
                INSERT INTO whatsapp_integracao (numero_telefone, usuarios_id, tipo_vinculo, situacao)
                VALUES (:numero, :usuarios_id, :tipo, 1)
                ON DUPLICATE KEY UPDATE usuarios_id = :usuarios_id, tipo_vinculo = :tipo, situacao = 1
            """, {
                'numero': numero,
                'usuarios_id': usr['idUsuarios'],
                'tipo': perfil,
            })
        except Exception as e:
            logger.warning(f"Auto-cadastro whatsapp_integracao: {e}")

        return {
            'tipo': perfil,
            'tipo_vinculo': perfil,
            'clientes_id': None,
            'usuarios_id': usr['idUsuarios'],
            'permissoes_id': perm_id,
            'nome': usr.get('nome', 'Usuario'),
            'numero': numero
        }

    # Terceira tentativa: buscar numero na tabela clientes (celular)
    sql_cli = """
        SELECT idClientes, nomeCliente, celular
        FROM clientes
        WHERE REPLACE(REPLACE(REPLACE(celular, '(', ''), ')', ''), '-', '') LIKE :busca
           OR celular = :celular
           OR CONCAT('55', celular) = :numero
        LIMIT 1
    """
    cli_rows = execute_query(sql_cli, {
        'busca': f'%{numero[-11:]}%',
        'celular': numero[-11:] if len(numero) > 10 else numero,
        'numero': numero
    })
    if cli_rows:
        cli = cli_rows[0]

        # Cadastrar automaticamente na whatsapp_integracao
        try:
            execute_insert("""
                INSERT INTO whatsapp_integracao (numero_telefone, clientes_id, tipo_vinculo, situacao)
                VALUES (:numero, :clientes_id, 'cliente', 1)
                ON DUPLICATE KEY UPDATE clientes_id = :clientes_id, tipo_vinculo = 'cliente', situacao = 1
            """, {
                'numero': numero,
                'clientes_id': cli['idClientes'],
            })
        except Exception as e:
            logger.warning(f"Auto-cadastro cliente whatsapp_integracao: {e}")

        return {
            'tipo': 'cliente',
            'tipo_vinculo': 'cliente',
            'clientes_id': cli['idClientes'],
            'usuarios_id': None,
            'permissoes_id': None,
            'nome': cli.get('nomeCliente', 'Cliente'),
            'numero': numero
        }

    return None


# ---- Menus interativos ----

def _menu_lista(usuario: dict) -> dict:
    """Constroi o menu interativo de lista conforme o perfil do usuario."""
    tipo = usuario.get('tipo_vinculo', 'desconhecido') if usuario else 'desconhecido'
    perm_id = usuario.get('permissoes_id') if usuario else None
    nome = usuario.get('nome', 'Cliente') if usuario else 'Cliente'
    primeiro_nome = nome.split()[0] if nome else 'Cliente'

    # Determinar perfil
    if perm_id and int(perm_id) == 1:
        perfil = 'admin'
    elif perm_id and int(perm_id) == 2:
        perfil = 'tecnico'
    elif tipo == 'cliente' or (perm_id and int(perm_id) in (5, 6)):
        perfil = 'cliente'
    elif tipo in ('admin', 'Administrador'):
        perfil = 'admin'
    elif tipo in ('tecnico', 'Tecnico'):
        perfil = 'tecnico'
    elif tipo == 'financeiro':
        perfil = 'admin'
    elif tipo == 'vendedor':
        perfil = 'tecnico'
    else:
        perfil = tipo

    if perfil == 'cliente':
        sections = [{
            'title': '📋 Meus Dados',
            'rows': [
                {'title': '📋 Status das minhas OS', 'description': 'Ver suas ordens de servico', 'rowId': 'status_os'},
                {'title': '💰 Quanto devo', 'description': 'Consultar valor em aberto', 'rowId': 'quanto_devo'},
                {'title': '🔍 Detalhes da OS', 'description': 'Ver detalhes de uma OS pelo numero', 'rowId': 'detalhes_os'},
            ]
        }]
    elif perfil == 'tecnico':
        sections = [
            {
                'title': '📋 Minhas OS',
                'rows': [
                    {'title': '📋 OS de hoje', 'description': 'Suas ordens de servico do dia', 'rowId': 'minhas_os_hoje'},
                    {'title': '🔍 Detalhes da OS', 'description': 'Ver detalhes de uma OS', 'rowId': 'detalhes_os'},
                    {'title': '📊 Relatorio de OS', 'description': 'Resumo de OS do dia', 'rowId': 'relatorio_os'},
                    {'title': '⚠️ OS atrasadas', 'description': 'Servicos em atraso', 'rowId': 'os_atrasadas'},
                    {'title': '👷 Produtividade', 'description': 'Desempenho da equipe', 'rowId': 'relatorio_produtividade'},
                ]
            },
            {
                'title': '📍 Atendimento',
                'rows': [
                    {'title': '📍 Cheguei na OS', 'description': 'Check-in no atendimento', 'rowId': 'checkin_tecnico'},
                    {'title': '🚪 Saindo da OS', 'description': 'Check-out do atendimento', 'rowId': 'checkout_tecnico'},
                ]
            }
        ]
    else:
        # Admin
        sections = [
            {
                'title': '📋 Operacional',
                'rows': [
                    {'title': '📊 Relatorio de OS', 'description': 'Resumo de OS do dia', 'rowId': 'relatorio_os'},
                    {'title': '⚠️ OS atrasadas', 'description': 'Servicos em atraso', 'rowId': 'os_atrasadas'},
                    {'title': '📋 Total OS abertas', 'description': 'Quantidade em aberto', 'rowId': 'total_os_abertas'},
                    {'title': '🔍 Detalhes da OS', 'description': 'Ver OS especifica pelo numero', 'rowId': 'detalhes_os'},
                ]
            },
            {
                'title': '💰 Financeiro',
                'rows': [
                    {'title': '📈 Relatorio financeiro', 'description': 'Receitas, despesas e lucro', 'rowId': 'relatorio_financeiro'},
                    {'title': '📊 Relatorio vendas', 'description': 'Vendas do periodo', 'rowId': 'relatorio_vendas'},
                    {'title': '📄 Cobrancas vencidas', 'description': 'Cobrancas atrasadas', 'rowId': 'cobrancas_vencidas'},
                    {'title': '💰 Vendas pendentes', 'description': 'Vendas nao faturadas', 'rowId': 'vendas_pendentes'},
                    {'title': '💸 OS finalizadas', 'description': 'OS concluidas para cobranca', 'rowId': 'os_finalizadas_mes'},
                ]
            },
            {
                'title': '📦 Gestao',
                'rows': [
                    {'title': '📦 Relatorio estoque', 'description': 'Produtos e alertas', 'rowId': 'relatorio_estoque'},
                    {'title': '👷 Produtividade', 'description': 'Desempenho dos tecnicos', 'rowId': 'relatorio_produtividade'},
                    {'title': '🏆 Top clientes', 'description': 'Clientes que mais trazem', 'rowId': 'relatorio_clientes_top'},
                    {'title': '📅 OS do mes', 'description': 'OS por periodo', 'rowId': 'relatorio_os_periodo'},
                    {'title': '📋 Relatorio atrasados', 'description': 'Clientes com OS em atraso', 'rowId': 'relatorio_atrasados'},
                ]
            },
            {
                'title': '📝 Acoes',
                'rows': [
                    {'title': '📝 Criar OS', 'description': 'Abrir nova ordem de servico', 'rowId': 'criar_os'},
                    {'title': '🔄 Alterar status', 'description': 'Mudar status de uma OS', 'rowId': 'alterar_status_os'},
                    {'title': '📍 Cheguei na OS', 'description': 'Check-in no atendimento', 'rowId': 'checkin_tecnico'},
                    {'title': '🚪 Saindo da OS', 'description': 'Check-out do atendimento', 'rowId': 'checkout_tecnico'},
                ]
            },
        ]

    return {
        'numero': usuario.get('numero', '') if usuario else '',
        'title': f'Ola {primeiro_nome}! JJ Ferreiras',
        'description': 'Escolha uma opcao no menu abaixo:',
        'buttonText': 'Ver opcoes',
        'sections': sections,
        'footer': 'JJ Ferreiras'
    }


def _enviar_menu_interativo(numero: str, usuario: dict) -> dict:
    """Envia o menu interativo (lista) conforme o perfil do usuario."""
    menu = _menu_lista(usuario)
    resultado = evo.enviar_lista(
        numero=numero,
        title=menu['title'],
        description=menu['description'],
        button_text=menu['buttonText'],
        sections=menu['sections'],
        footer=menu.get('footer', '')
    )
    return resultado


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