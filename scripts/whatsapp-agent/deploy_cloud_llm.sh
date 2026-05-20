#!/bin/bash
# Deploy: Atualiza WhatsApp Agent com suporte a Cloud LLM (Ollama Cloud GLM-5)
# Execute no servidor: bash deploy_cloud_llm.sh

set -e

AGENT_DIR="/opt/whatsapp-agent"

echo "=== Deploy WhatsApp Agent - Cloud LLM ==="

# 1. Backup do .env atual
echo "[1/4] Backup do .env..."
cp "$AGENT_DIR/.env" "$AGENT_DIR/.env.bak.$(date +%Y%m%d%H%M%S)"

# 2. Atualizar o .env com configuração do Cloud LLM
echo "[2/4] Configurando Cloud LLM no .env..."

# Alterar LLM_PROVIDER para openai
sed -i 's/^LLM_PROVIDER=.*/LLM_PROVIDER=openai/' "$AGENT_DIR/.env"

# Remover linhas antigas de cloud se existirem
sed -i '/^LLM_CLOUD_URL=/d' "$AGENT_DIR/.env"
sed -i '/^LLM_CLOUD_API_KEY=/d' "$AGENT_DIR/.env"
sed -i '/^LLM_CLOUD_MODEL=/d' "$AGENT_DIR/.env"

# Adicionar configuração do Cloud LLM após a linha LLM_MODEL
sed -i "/^LLM_MODEL=/a\\
\\
# === Cloud LLM (OpenAI-compatible API) ===\\
LLM_CLOUD_URL=https://ollama.com/v1\\
LLM_CLOUD_API_KEY=f6e136e710bb460fa8f20d408d3761d8.64JLJDts4v5fIYV111gLvtqp\\
LLM_CLOUD_MODEL=glm-5" "$AGENT_DIR/.env"

echo "  LLM_PROVIDER=openai"
echo "  LLM_CLOUD_URL=https://ollama.com/v1"
echo "  LLM_CLOUD_MODEL=glm-5"

# 3. Copiar os arquivos atualizados
echo "[3/4] Copiando arquivos atualizados..."
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

for f in config.py services/llm.py main.py services/nlp.py; do
    if [ -f "$SCRIPT_DIR/$f" ]; then
        cp "$SCRIPT_DIR/$f" "$AGENT_DIR/$f"
        echo "  Atualizado: $f"
    fi
done

# 4. Reiniciar o serviço
echo "[4/4] Reiniciando o serviço..."
sudo systemctl restart whatsapp-agent
sleep 2

# Verificar status
if sudo systemctl is-active --quiet whatsapp-agent; then
    echo ""
    echo "=== Deploy concluido com sucesso! ==="
    echo "Servico whatsapp-agent esta rodando."
    echo ""
    echo "Teste com: curl http://localhost:8000/health"
    echo ""
    echo "Para testar o Cloud LLM, envie 'oi' pelo WhatsApp."
else
    echo ""
    echo "=== ERRO: Servico nao subiu! ==="
    echo "Verifique os logs: sudo journalctl -u whatsapp-agent -n 50"
    echo ""
    echo "Para restaurar .env anterior:"
    ls -t "$AGENT_DIR"/.env.bak.* | head -1 | xargs -I{} cp {} "$AGENT_DIR/.env"
fi