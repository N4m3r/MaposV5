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
LLM_PROVIDER = os.getenv('LLM_PROVIDER', '')  # 'ollama', 'anthropic', ''
OLLAMA_URL = os.getenv('OLLAMA_URL', 'http://localhost:11434').rstrip('/')
LLM_MODEL = os.getenv('LLM_MODEL', 'llama3.2:3b')

# Debug
DEBUG = os.getenv('DEBUG', 'false').lower() == 'true'

# Construir URL de conexao MySQL para SQLAlchemy
DATABASE_URL = f"mysql+pymysql://{MYSQL_USER}:{MYSQL_PASS}@{MYSQL_HOST}:{MYSQL_PORT}/{MYSQL_DB}?charset=utf8mb4"
