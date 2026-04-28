#!/bin/bash
# =============================================================================
# Script de Instalacao Completo - Agente IA WhatsApp para MapOS
# =============================================================================
# Execute este script no seu servidor Linux como root:
#
#   curl -fsSL URL_DO_SCRIPT | sudo bash
#
# OU copie este arquivo para o servidor e execute:
#
#   sudo bash setup_agente.sh
#
# =============================================================================

set -e

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

AGENT_DIR="/opt/whatsapp-agent"
SERVICE_NAME="whatsapp-agent"
PYTHON_CMD=""

# =============================================================================
# FUNCOES
# =============================================================================

log() {
    echo -e "${GREEN}[$(date +%H:%M:%S)]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[AVISO]${NC} $1"
}

error() {
    echo -e "${RED}[ERRO]${NC} $1"
}

info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

# =============================================================================
# 1. VERIFICAR ROOT
# =============================================================================
if [ "$EUID" -ne 0 ]; then
    error "Execute este script como root: sudo bash setup_agente.sh"
    exit 1
fi

log "Iniciando instalacao do Agente IA WhatsApp para MapOS..."

# =============================================================================
# 2. DETECTAR PYTHON
# =============================================================================
if command -v python3 &> /dev/null; then
    PYTHON_CMD="python3"
    PYTHON_VERSION=$($PYTHON_CMD --version 2>&1 | awk '{print $2}')
    log "Python detectado: $PYTHON_VERSION"
else
    error "Python3 nao encontrado. Instalando..."
    if command -v apt-get &> /dev/null; then
        apt-get update && apt-get install -y python3 python3-venv python3-pip
    elif command -v yum &> /dev/null; then
        yum install -y python3 python3-venv python3-pip
    elif command -v dnf &> /dev/null; then
        dnf install -y python3 python3-venv python3-pip
    else
        error "Gerenciador de pacotes nao suportado. Instale python3 manualmente."
        exit 1
    fi
    PYTHON_CMD="python3"
fi

# Verificar versao minima (3.9+)
PYTHON_MAJOR=$($PYTHON_CMD -c "import sys; print(sys.version_info.major)")
PYTHON_MINOR=$($PYTHON_CMD -c "import sys; print(sys.version_info.minor)")
if [ "$PYTHON_MAJOR" -lt 3 ] || ([ "$PYTHON_MAJOR" -eq 3 ] && [ "$PYTHON_MINOR" -lt 9 ]); then
    error "Python 3.9+ e necessario. Versao atual: $PYTHON_MAJOR.$PYTHON_MINOR"
    exit 1
fi

# =============================================================================
# 3. CRIAR DIRETORIO
# =============================================================================
log "Criando diretorio do agente em $AGENT_DIR..."
mkdir -p "$AGENT_DIR/services"
cd "$AGENT_DIR"

# =============================================================================
# 4. CRIAR ARQUIVOS
# =============================================================================
log "Criando arquivos do agente..."

# --- .env ---
cat > "$AGENT_DIR/.env" << 'ENVEOF'
# =============================================================================
# Configuracao do Agente IA WhatsApp para MapOS
# =============================================================================
# PREENCHA OS DADOS ABAIXO ANTES DE INICIAR O SERVICO
# =============================================================================

# === Banco de Dados MySQL (mesmo do MapOS) ===
MYSQL_HOST=mysql.jj-ferreiras.com.br
MYSQL_PORT=3306
MYSQL_DB=jjferreiras03
MYSQL_USER=jjferreiras03
MYSQL_PASS=93982740tT

# === Evolution Go (SaaS) ===
EVOLUTION_URL=https://evo.jj-ferreiras.com.br
# Use a API Key global ou o token da instancia
EVOLUTION_API_KEY=7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2
EVOLUTION_INSTANCE=Mapos

# === Agente ===
AGENT_PORT=8000
# IMPORTANTE: Mude esta chave para algo seguro!
AGENT_API_KEY=jjferreiras-agente-ia-2024
AGENT_URL=http://localhost:8000

# === MapOS ===
MAPOS_URL=https://jj-ferreiras.com.br/mapos3
MAPOS_API_KEY=jjferreiras-agente-ia-2024

# === LLM (deixe comentado para usar regex/template sem LLM - CUSTO ZERO) ===
# LLM_PROVIDER=ollama
# OLLAMA_URL=http://localhost:11434
# LLM_MODEL=llama3.2:3b

# === Modo Debug ===
DEBUG=false
ENVEOF

# --- requirements.txt ---
cat > "$AGENT_DIR/requirements.txt" << 'REQEOF'
fastapi==0.115.0
uvicorn==0.32.0
pymysql==1.1.1
sqlalchemy==2.0.36
python-dotenv==1.0.1
requests==2.32.3
python-multipart==0.0.12
REQEOF

# --- config.py ---
cat > "$AGENT_DIR/config.py" << 'PYEOF'
import os
from dotenv import load_dotenv

load_dotenv()

# Banco de Dados
MYSQL_HOST = os.getenv('MYSQL_HOST', 'localhost')
MYSQL_PORT = int(os.getenv('MYSQL_PORT', 3306))
MYSQL_DB = os.getenv('MYSQL_DB', 'mapos')
MYSQL_USER = os.getenv('MYSQL_USER', 'root')
MYSQL_PASS = os.getenv('MYSQL_PASS', '')

# Evolution Go
EVOLUTION_URL = os.getenv('EVOLUTION_URL', 'http://localhost:8080').rstrip('/')
EVOLUTION_API_KEY = os.getenv('EVOLUTION_API_KEY', '')
EVOLUTION_INSTANCE = os.getenv('EVOLUTION_INSTANCE', 'mapos')

# Agente
AGENT_PORT = int(os.getenv('AGENT_PORT', 8000))
AGENT_API_KEY = os.getenv('AGENT_API_KEY', '')
AGENT_URL = os.getenv('AGENT_URL', f'http://localhost:{AGENT_PORT}')

# MapOS
MAPOS_URL = os.getenv('MAPOS_URL', '').rstrip('/')
MAPOS_API_KEY = os.getenv('MAPOS_API_KEY', '')

# LLM (opcional)
LLM_PROVIDER = os.getenv('LLM_PROVIDER', '')
OLLAMA_URL = os.getenv('OLLAMA_URL', 'http://localhost:11434').rstrip('/')
LLM_MODEL = os.getenv('LLM_MODEL', 'llama3.2:3b')

# Debug
DEBUG = os.getenv('DEBUG', 'false').lower() == 'true'

# Construir URL de conexao MySQL para SQLAlchemy
DATABASE_URL = f"mysql+pymysql://{MYSQL_USER}:{MYSQL_PASS}@{MYSQL_HOST}:{MYSQL_PORT}/{MYSQL_DB}?charset=utf8mb4"
PYEOF

# --- database.py ---
cat > "$AGENT_DIR/database.py" << 'PYEOF'
from sqlalchemy import create_engine, text
from sqlalchemy.pool import QueuePool
import config

engine = create_engine(
    config.DATABASE_URL,
    poolclass=QueuePool,
    pool_size=5,
    max_overflow=10,
    pool_pre_ping=True,
    echo=config.DEBUG
)

def get_connection():
    return engine.connect()

def execute_query(sql, params=None):
    with get_connection() as conn:
        result = conn.execute(text(sql), params or {})
        rows = result.mappings().all()
        return [dict(row) for row in rows]

def execute_scalar(sql, params=None):
    with get_connection() as conn:
        result = conn.execute(text(sql), params or {})
        row = result.fetchone()
        if row:
            return row[0]
        return None

def execute_insert(sql, params=None):
    with get_connection() as conn:
        result = conn.execute(text(sql), params or {})
        conn.commit()
        return result.lastrowid

def execute_update(sql, params=None):
    with get_connection() as conn:
        result = conn.execute(text(sql), params or {})
        conn.commit()
        return result.rowcount
PYEOF

# --- services/__init__.py ---
cat > "$AGENT_DIR/services/__init__.py" << 'PYEOF'
# Services package
PYEOF

# --- services/evolution_api.py ---
cat > "$AGENT_DIR/services/evolution_api.py" << 'PYEOF'
import requests
import config

class EvolutionAPI:
    def __init__(self):
        self.base_url = config.EVOLUTION_URL
        self.api_key = config.EVOLUTION_API_KEY
        self.instance = config.EVOLUTION_INSTANCE
        self.headers = {
            'apikey': self.api_key,
            'Content-Type': 'application/json'
        }

    def enviar_texto(self, numero: str, mensagem: str, delay: int = 1200):
        url = f"{self.base_url}/send/text"
        payload = {
            'number': numero,
            'text': mensagem,
            'delay': delay
        }
        try:
            resp = requests.post(url, headers=self.headers, json=payload, timeout=30)
            data = resp.json()
            return {
                'success': resp.status_code == 200,
                'status_code': resp.status_code,
                'data': data
            }
        except Exception as e:
            return {'success': False, 'error': str(e)}

    def enviar_documento(self, numero: str, file_path: str, caption: str = ''):
        url = f"{self.base_url}/send/media"
        try:
            with open(file_path, 'rb') as f:
                files = {'file': f}
                data = {
                    'number': numero,
                    'caption': caption,
                    'mediatype': 'document'
                }
                resp = requests.post(url, headers={'apikey': self.api_key}, files=files, data=data, timeout=60)
                return {
                    'success': resp.status_code == 200,
                    'status_code': resp.status_code,
                    'data': resp.json() if resp.text else {}
                }
        except Exception as e:
            return {'success': False, 'error': str(e)}
PYEOF

# --- services/mapos_queries.py ---
cat > "$AGENT_DIR/services/mapos_queries.py" << 'PYEOF'
from database import execute_query, execute_scalar

class MaposQueries:

    def buscar_cliente_por_numero(self, numero: str):
        sql = """
            SELECT c.idClientes, c.nomeCliente, c.celular, c.email,
                   c.rua, c.numero, c.bairro, c.cidade, c.estado
            FROM clientes c
            JOIN whatsapp_integracao w ON w.clientes_id = c.idClientes
            WHERE w.numero_telefone = :numero AND w.situacao = 1
            LIMIT 1
        """
        rows = execute_query(sql, {'numero': numero})
        return rows[0] if rows else None

    def buscar_cliente_por_nome(self, nome: str):
        sql = """
            SELECT idClientes, nomeCliente, celular, email
            FROM clientes
            WHERE nomeCliente LIKE :nome
            LIMIT 5
        """
        return execute_query(sql, {'nome': f'%{nome}%'})

    def total_em_aberto_cliente(self, cliente_id: int):
        sql = """
            SELECT COALESCE(SUM(valor), 0) as total
            FROM cobrancas
            WHERE clientes_id = :cliente_id AND baixado = 0
        """
        return execute_scalar(sql, {'cliente_id': cliente_id}) or 0

    def listar_os_cliente(self, cliente_id: int, limite: int = 10):
        sql = """
            SELECT o.idOs, o.dataInicial, o.dataFinal, o.garantia,
                   o.descricaoProduto, o.defeito, o.status,
                   o.observacoes, o.laudoTecnico
            FROM os o
            WHERE o.clientes_id = :cliente_id
            ORDER BY o.idOs DESC
            LIMIT :limite
        """
        return execute_query(sql, {'cliente_id': cliente_id, 'limite': limite})

    def buscar_os(self, os_id: int):
        sql = """
            SELECT o.*, c.nomeCliente, c.celular, u.nome as tecnico_nome
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            LEFT JOIN usuarios u ON u.idUsuarios = o.usuarios_id
            WHERE o.idOs = :os_id
            LIMIT 1
        """
        rows = execute_query(sql, {'os_id': os_id})
        return rows[0] if rows else None

    def listar_os_tecnico(self, usuario_id: int, data: str = None):
        sql = """
            SELECT o.idOs, o.dataInicial, o.dataFinal, o.descricaoProduto,
                   o.defeito, o.status, c.nomeCliente
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            WHERE o.usuarios_id = :usuario_id
        """
        params = {'usuario_id': usuario_id}
        if data:
            sql += " AND DATE(o.dataInicial) = :data"
            params['data'] = data
        sql += " ORDER BY o.idOs DESC"
        return execute_query(sql, params)

    def resumo_os_dia(self, data: str = None):
        if not data:
            sql = """
                SELECT status, COUNT(*) as quantidade
                FROM os
                WHERE DATE(dataInicial) = CURDATE()
                GROUP BY status
            """
            return execute_query(sql)
        else:
            sql = """
                SELECT status, COUNT(*) as quantidade
                FROM os
                WHERE DATE(dataInicial) = :data
                GROUP BY status
            """
            return execute_query(sql, {'data': data})

    def os_atrasadas(self):
        sql = """
            SELECT o.idOs, o.dataInicial, o.dataFinal, o.descricaoProduto,
                   o.defeito, o.status, c.nomeCliente, c.celular
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            WHERE o.status NOT IN ('Finalizado', 'Cancelado', 'Faturado')
              AND o.dataFinal < CURDATE()
            ORDER BY o.dataFinal ASC
            LIMIT 20
        """
        return execute_query(sql)

    def buscar_usuario_por_numero(self, numero: str):
        sql = """
            SELECT u.idUsuarios, u.nome, u.celular, u.email, p.nome as permissao_nome
            FROM usuarios u
            JOIN permissoes p ON p.idPermissao = u.permissoes_id
            JOIN whatsapp_integracao w ON w.usuarios_id = u.idUsuarios
            WHERE w.numero_telefone = :numero AND w.situacao = 1
            LIMIT 1
        """
        rows = execute_query(sql, {'numero': numero})
        return rows[0] if rows else None

    def total_os_abertas(self):
        sql = "SELECT COUNT(*) as total FROM os WHERE status NOT IN ('Finalizado', 'Cancelado')"
        return execute_scalar(sql) or 0

    def vendas_pendentes(self, limite: int = 20):
        sql = """
            SELECT v.idVendas, v.data, v.valorTotal, v.faturado,
                   c.nomeCliente, c.celular
            FROM vendas v
            JOIN clientes c ON c.idClientes = v.clientes_id
            WHERE v.faturado = 0
            ORDER BY v.idVendas DESC
            LIMIT :limite
        """
        return execute_query(sql, {'limite': limite})

    def cobrancas_vencidas(self, limite: int = 20):
        sql = """
            SELECT cb.idCobranca, cb.descricao, cb.valor, cb.data_vencimento,
                   cb.baixado, cb.data_pagamento, c.nomeCliente
            FROM cobrancas cb
            JOIN clientes c ON c.idClientes = cb.clientes_id
            WHERE cb.baixado = 0 AND cb.data_vencimento < CURDATE()
            ORDER BY cb.data_vencimento ASC
            LIMIT :limite
        """
        return execute_query(sql, {'limite': limite})
PYEOF

# --- services/nlp.py ---
cat > "$AGENT_DIR/services/nlp.py" << 'PYEOF'
import re
from typing import Tuple, Optional

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

OS_NUMBER_RE = re.compile(r'#?\s*(\d+)')


def classificar(texto: str) -> Tuple[str, Optional[dict]]:
    texto_lower = texto.lower().strip()
    texto_limpo = re.sub(r'[^\w\s]', ' ', texto_lower)
    texto_limpo = re.sub(r'\s+', ' ', texto_limpo).strip()

    for comando, palavras_chave in COMANDOS.items():
        for chave in palavras_chave:
            if chave in texto_lower or chave in texto_limpo:
                params = extrair_parametros(texto_lower, comando)
                return comando, params

    palavras = set(texto_limpo.split())
    if 'os' in palavras and any(w in palavras for w in ['status', 'minha', 'aberta']):
        return 'status_os', {}
    if 'devo' in palavras or 'divida' in palavras:
        return 'quanto_devo', {}
    if 'ajuda' in palavras or 'help' in palavras:
        return 'ajuda', {}

    return 'desconhecido', {'texto_original': texto}


def extrair_parametros(texto: str, comando: str) -> dict:
    params = {}
    match = OS_NUMBER_RE.search(texto)
    if match:
        params['os_id'] = int(match.group(1))

    if comando == 'criar_os':
        cliente_match = re.search(r'(?:para|cliente)\s+([\w\s]+?)(?:,|\s+defeito|$)', texto)
        if cliente_match:
            params['cliente_nome'] = cliente_match.group(1).strip()
        defeito_match = re.search(r'(?:defeito|problema)\s*[:\-]?\s*(.+?)(?:,|$)', texto)
        if defeito_match:
            params['defeito'] = defeito_match.group(1).strip()

    return params


def formatar_resposta(comando: str, dados: dict, usuario: dict = None) -> str:
    tipo = usuario.get('tipo_vinculo', 'desconhecido') if usuario else 'desconhecido'
    nome = usuario.get('nome', 'Cliente') if usuario else 'Cliente'

    if comando == 'ajuda':
        if tipo == 'cliente':
            return f"""Ola {nome}! \n\nSou o assistente virtual da JJ Ferreiras. Posso te ajudar com:\n\n*status da minha os* - Ver suas ordens de servico\n*quanto devo* - Valor em aberto\n\nEm breve mais comandos!\n"""
        elif tipo == 'tecnico':
            return f"""Ola {nome}! \n\nComandos disponiveis:\n\n*minhas os de hoje* - Suas ordens do dia\n*relatorio de os* - Resumo do dia\n\nMais funcoes em breve!\n"""
        else:
            return f"""Ola {nome}! \n\nComandos de admin:\n\n*relatorio de os* - Total do dia\n*os atrasadas* - Servicos em atraso\n*vendas pendentes* - Vendas nao faturadas\n*total os abertas* - Quantidade em aberto\n\nMais funcoes em breve!\n"""

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
            return f"Ola {nome}! Voce nao tem valores em aberto. "
        return f"Ola {nome}!\n\nVoce tem *R$ {total:.2f}* em aberto.\n\nEntre em contato para regularizar."

    elif comando == 'detalhes_os':
        os = dados.get('os')
        if not os:
            return "OS nao encontrada. Verifique o numero e tente novamente."
        return f"""*Detalhes da OS #{os['idOs']}*\n\nCliente: {os['nomeCliente']}\nEquipamento: {os['descricaoProduto'] or 'Nao informado'}\nDefeito: {os['defeito'] or 'Nao informado'}\nStatus: {os['status']}\nObservacoes: {os['observacoes'] or 'Nenhuma'}\nLaudo: {os['laudoTecnico'] or 'Nenhum'}\n"""

    elif comando == 'minhas_os_hoje':
        oss = dados.get('oss', [])
        if not oss:
            return f"Ola {nome}! Voce nao tem OS atribuidas para hoje. "
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
            msg += f" {r['status']}: {r['quantidade']}\n"
            total += r['quantidade']
        msg += f"\n*Total: {total}*"
        return msg

    elif comando == 'os_atrasadas':
        oss = dados.get('oss', [])
        if not oss:
            return "Nenhuma OS em atraso! "
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
            return "Nenhuma venda pendente! "
        msg = f"*Vendas Pendentes ({len(vendas)})*\n\n"
        for v in vendas[:10]:
            msg += f"*Venda #{v['idVendas']}*\n"
            msg += f"Cliente: {v['nomeCliente']}\n"
            msg += f"Valor: R$ {v['valorTotal']:.2f}\n\n"
        return msg

    elif comando == 'cobrancas_vencidas':
        cobs = dados.get('cobrancas', [])
        if not cobs:
            return "Nenhuma cobranca vencida! "
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
        return f"""Desculpe {nome}, nao entendi seu comando. \n\nTente um destes:\n status da minha os\n quanto devo\n ajuda\n\nOu entre em contato com nossa equipe.\n"""

    return "Comando processado."
PYEOF

# --- main.py ---
cat > "$AGENT_DIR/main.py" << 'PYEOF'
from fastapi import FastAPI, Request, Header, HTTPException
from fastapi.responses import JSONResponse
import json
import config

from database import execute_query, execute_insert
from services.evolution_api import EvolutionAPI
from services.mapos_queries import MaposQueries
from services import nlp

app = FastAPI(title="Agente IA WhatsApp - MapOS", version="1.0.0")

evo = EvolutionAPI()
queries = MaposQueries()


def extrair_numero(payload: dict) -> str:
    try:
        data = payload.get('data', {})
        key = data.get('key', {})
        remote_jid = key.get('remoteJid', '')
        if remote_jid:
            return remote_jid.split('@')[0]
        message = data.get('message', {})
        if 'key' in message:
            remote_jid = message['key'].get('remoteJid', '')
            if remote_jid:
                return remote_jid.split('@')[0]
        sender = data.get('sender', '') or data.get('senderJid', '')
        if sender:
            return sender.split('@')[0]
        return ''
    except Exception:
        return ''


def extrair_mensagem(payload: dict) -> str:
    try:
        data = payload.get('data', {})
        if 'message' in data:
            msg = data['message']
            if 'conversation' in msg:
                return msg['conversation']
            if 'extendedTextMessage' in msg:
                return msg['extendedTextMessage'].get('text', '')
        if 'body' in data:
            return data['body']
        return ''
    except Exception:
        return ''


def limpar_numero(numero: str) -> str:
    numero = ''.join(filter(str.isdigit, numero))
    if len(numero) == 11 or len(numero) == 10:
        numero = '55' + numero
    return numero


def identificar_usuario(numero: str):
    numero = limpar_numero(numero)
    sql = """
        SELECT w.*,
               c.nomeCliente as nome_cliente,
               u.nome as nome_usuario,
               p.nome as permissao_nome
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
        tipo = row.get('tipo_vinculo', 'desconhecido')
        nome = row.get('nome_cliente') or row.get('nome_usuario') or 'Usuario'
        return {
            'tipo': tipo,
            'tipo_vinculo': tipo,
            'clientes_id': row.get('clientes_id'),
            'usuarios_id': row.get('usuarios_id'),
            'nome': nome,
            'numero': numero
        }
    return None


def registrar_log(numero: str, direcao: str, conteudo: str, intencao: str = None, status: str = 'recebido'):
    try:
        sql = """
            INSERT INTO whatsapp_log_interacoes
            (numero_telefone, tipo_mensagem, direcao, conteudo, intencao_detectada, status)
            VALUES (:numero, 'texto', :direcao, :conteudo, :intencao, :status)
        """
        execute_insert(sql, {
            'numero': numero,
            'direcao': direcao,
            'conteudo': conteudo[:1000],
            'intencao': intencao,
            'status': status
        })
    except Exception as e:
        if config.DEBUG:
            print(f"Erro ao registrar log: {e}")


@app.get("/health")
async def health():
    try:
        result = execute_query("SELECT 1 as ok")
        db_ok = bool(result and result[0].get('ok') == 1)
    except Exception:
        db_ok = False
    return {
        "status": "ok",
        "database": "online" if db_ok else "offline",
        "version": "1.0.0"
    }


@app.post("/webhook/evolution")
async def webhook_evolution(request: Request, x_api_key: str = Header(None)):
    if x_api_key != config.AGENT_API_KEY:
        raise HTTPException(status_code=401, detail="Unauthorized")

    try:
        payload = await request.json()
    except Exception:
        payload = {}

    if config.DEBUG:
        print(f"[WEBHOOK] Payload: {json.dumps(payload, indent=2, ensure_ascii=False)}")

    numero = extrair_numero(payload)
    mensagem = extrair_mensagem(payload)

    if not numero or not mensagem:
        return {"status": "ignorado", "motivo": "sem numero ou mensagem"}

    numero = limpar_numero(numero)

    # Ignorar mensagens do proprio bot
    meu_numero = config.EVOLUTION_INSTANCE or ''
    if numero in [meu_numero, '']:
        return {"status": "ignorado", "motivo": "mensagem propria"}

    registrar_log(numero, 'entrada', mensagem)
    usuario = identificar_usuario(numero)

    if not usuario:
        resposta = "Ola! Seu numero nao esta vinculado ao nosso sistema.\n\nEntre em contato com nossa equipe para cadastrar seu WhatsApp."
        registrar_log(numero, 'saida', resposta, 'nao_cadastrado', 'respondido')
        evo.enviar_texto(numero, resposta)
        return {"status": "nao_cadastrado"}

    # Atualizar ultima interacao
    execute_update(
        "UPDATE whatsapp_integracao SET ultima_interacao = NOW() WHERE numero_telefone = :numero",
        {'numero': numero}
    )

    comando, params = nlp.classificar(mensagem)
    dados = {}

    if comando == 'status_os':
        if usuario.get('clientes_id'):
            oss = queries.listar_os_cliente(usuario['clientes_id'])
            dados = {'oss': oss}
        elif usuario.get('usuarios_id'):
            oss = queries.listar_os_tecnico(usuario['usuarios_id'])
            dados = {'oss': oss}
        else:
            dados = {'oss': []}

    elif comando == 'detalhes_os':
        os_id = params.get('os_id')
        if os_id:
            os = queries.buscar_os(os_id)
            dados = {'os': os}
        else:
            if usuario.get('clientes_id'):
                oss = queries.listar_os_cliente(usuario['clientes_id'], 1)
                dados = {'os': oss[0] if oss else None}
            elif usuario.get('usuarios_id'):
                oss = queries.listar_os_tecnico(usuario['usuarios_id'])
                dados = {'os': oss[0] if oss else None}
            else:
                dados = {'os': None}

    elif comando == 'quanto_devo':
        if usuario.get('clientes_id'):
            total = queries.total_em_aberto_cliente(usuario['clientes_id'])
            dados = {'total': total}
        else:
            dados = {'total': 0}

    elif comando == 'minhas_os_hoje':
        if usuario.get('usuarios_id'):
            from datetime import date
            hoje = date.today().isoformat()
            oss = queries.listar_os_tecnico(usuario['usuarios_id'], hoje)
            dados = {'oss': oss}
        else:
            dados = {'oss': []}

    elif comando == 'relatorio_os':
        resumo = queries.resumo_os_dia()
        dados = {'resumo': resumo}

    elif comando == 'os_atrasadas':
        oss = queries.os_atrasadas()
        dados = {'oss': oss}

    elif comando == 'vendas_pendentes':
        vendas = queries.vendas_pendentes()
        dados = {'vendas': vendas}

    elif comando == 'cobrancas_vencidas':
        cobs = queries.cobrancas_vencidas()
        dados = {'cobrancas': cobs}

    elif comando == 'total_os_abertas':
        total = queries.total_os_abertas()
        dados = {'total': total}

    elif comando == 'criar_os':
        dados = {
            'mensagem': f"{usuario['nome']}, para criar uma OS acesse o painel do MapOS.\n\nEm breve voce podera criar pelo WhatsApp tambem!"
        }

    resposta = nlp.formatar_resposta(comando, dados, usuario)
    resultado = evo.enviar_texto(numero, resposta)

    status_envio = 'respondido' if resultado.get('success') else 'erro'
    registrar_log(numero, 'saida', resposta, comando, status_envio)

    return {
        "status": "ok",
        "comando": comando,
        "numero": numero,
        "envio_success": resultado.get('success')
    }


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=config.AGENT_PORT)
PYEOF

log "Arquivos criados com sucesso!"

# =============================================================================
# 5. CRIAR AMBIENTE VIRTUAL E INSTALAR DEPENDENCIAS
# =============================================================================
log "Criando ambiente virtual Python..."
$PYTHON_CMD -m venv "$AGENT_DIR/venv"
source "$AGENT_DIR/venv/bin/activate"

log "Instalando dependencias Python..."
pip install --upgrade pip -q
pip install -r "$AGENT_DIR/requirements.txt" -q

# =============================================================================
# 6. CRIAR SERVICO SYSTEMD
# =============================================================================
log "Criando servico systemd..."
cat > /etc/systemd/system/${SERVICE_NAME}.service << 'SVCEOF'
[Unit]
Description=Agente IA WhatsApp - MapOS
After=network.target

[Service]
Type=simple
User=root
WorkingDirectory=/opt/whatsapp-agent
Environment=PYTHONPATH=/opt/whatsapp-agent
ExecStart=/opt/whatsapp-agent/venv/bin/uvicorn main:app --host 0.0.0.0 --port 8000
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
SVCEOF

systemctl daemon-reload
systemctl enable $SERVICE_NAME

# =============================================================================
# 7. LIBERAR PORTA NO FIREWALL
# =============================================================================
log "Liberando porta 8000 no firewall..."
if command -v ufw &> /dev/null; then
    ufw allow 8000/tcp || true
elif command -v firewall-cmd &> /dev/null; then
    firewall-cmd --permanent --add-port=8000/tcp || true
    firewall-cmd --reload || true
elif command -v iptables &> /dev/null; then
    iptables -I INPUT -p tcp --dport 8000 -j ACCEPT || true
fi

# =============================================================================
# 8. INICIAR SERVICO
# =============================================================================
log "Iniciando servico..."
systemctl start $SERVICE_NAME
sleep 3

# =============================================================================
# 9. TESTAR HEALTH CHECK
# =============================================================================
log "Testando health check..."
HEALTH=$(curl -s http://localhost:8000/health || echo '{"status":"erro"}')
echo "Resposta: $HEALTH"

# =============================================================================
# 10. RESUMO
# =============================================================================
echo ""
echo "============================================================================="
echo -e "${GREEN}         INSTALACAO CONCLUIDA COM SUCESSO!${NC}"
echo "============================================================================="
echo ""
echo " Diretorio: $AGENT_DIR"
echo " Porta: 8000"
echo " API Key: $(grep AGENT_API_KEY $AGENT_DIR/.env | head -1)"
echo ""
echo " COMANDOS UTEIS:"
echo "   sudo systemctl status $SERVICE_NAME   # Ver status"
echo "   sudo systemctl restart $SERVICE_NAME   # Reiniciar"
echo "   sudo journalctl -u $SERVICE_NAME -f   # Ver logs"
echo "   curl http://localhost:8000/health      # Testar"
echo ""
echo " PROXIMO PASSO:"
echo "   Configure o webhook no Evolution Go:"
echo "   URL: http://$(hostname -I | awk '{print $1}'):8000/webhook/evolution"
echo "   Header: x-api-key: SUA_CHAVE_DO_AGENTE"
echo "   Evento: messages.upsert"
echo ""
echo "============================================================================="
