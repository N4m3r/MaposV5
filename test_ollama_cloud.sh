#!/bin/bash
# =============================================================================
# Testa a conexao com Ollama Cloud
# Execute no servidor: bash test_ollama_cloud.sh <OLLAMA_API_KEY>
# =============================================================================

OLLAMA_API_KEY="${1:-$OLLAMA_API_KEY}"

if [ -z "$OLLAMA_API_KEY" ]; then
    echo "ERRO: Forneca a OLLAMA_API_KEY."
    echo "Uso: bash test_ollama_cloud.sh SUA_CHAVE"
    exit 1
fi

echo "=========================================="
echo "Testando Ollama Cloud"
echo "=========================================="

# Teste 1: API Generate
echo -e "\n--- Teste /api/generate ---"
curl -s https://ollama.com/api/generate \
  -H "Authorization: Bearer $OLLAMA_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"model": "kimi-k2.6:cloud", "prompt": "Responda apenas OK", "stream": false}' \
  | head -c 300

echo -e "\n\n--- Teste /api/chat ---"
curl -s https://ollama.com/api/chat \
  -H "Authorization: Bearer $OLLAMA_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"model": "kimi-k2.6:cloud", "messages": [{"role": "user", "content": "Responda apenas OK"}], "stream": false}' \
  | head -c 300

echo -e "\n\n=========================================="
echo "Teste concluido."
echo "=========================================="
