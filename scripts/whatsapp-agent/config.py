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

# n8n (repassar mensagens para o n8n apos processar)
N8N_WEBHOOK_URL = os.getenv('N8N_WEBHOOK_URL', 'https://n8n.jj-ferreiras.com.br/webhook/recebimento-whatsapp').rstrip('/')

# LLM (opcional - vazio = regex)
LLM_PROVIDER = os.getenv('LLM_PROVIDER', '')  # 'ollama', 'openai', 'anthropic', ''
OLLAMA_URL = os.getenv('OLLAMA_URL', 'http://localhost:11434').rstrip('/')
OLLAMA_API_KEY = os.getenv('OLLAMA_API_KEY', '')
LLM_MODEL = os.getenv('LLM_MODEL', 'hermes3:8b')

# Cloud LLM (OpenAI-compatible API - Ollama cloud, OpenAI, Groq, Together, etc.)
LLM_CLOUD_URL = os.getenv('LLM_CLOUD_URL', '').rstrip('/')  # e.g. https://api.openai.com/v1 or https://xxx.ollama.com/v1
LLM_CLOUD_API_KEY = os.getenv('LLM_CLOUD_API_KEY', '')
LLM_CLOUD_MODEL = os.getenv('LLM_CLOUD_MODEL', '')  # e.g. glm-5.1, gpt-4o-mini, etc.

# Whisper ASR
WHISPER_URL = os.getenv('WHISPER_URL', 'http://localhost:9001').rstrip('/')
WHISPER_LANGUAGE = os.getenv('WHISPER_LANGUAGE', 'pt')

# Debug
DEBUG = os.getenv('DEBUG', 'false').lower() == 'true'

# Log
LOG_LEVEL = os.getenv('LOG_LEVEL', 'INFO').upper()

# Construir URL de conexao MySQL para SQLAlchemy
DATABASE_URL = f"mysql+pymysql://{MYSQL_USER}:{MYSQL_PASS}@{MYSQL_HOST}:{MYSQL_PORT}/{MYSQL_DB}?charset=utf8mb4"