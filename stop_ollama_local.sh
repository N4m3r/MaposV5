#!/bin/bash
# =============================================================================
# Para e remove o container Ollama local (ja nao e mais necessario)
# Execute no servidor apos confirmar que Ollama Cloud esta funcionando
# =============================================================================

echo "=========================================="
echo "Parando Ollama local (liberando RAM)"
echo "=========================================="

# Tenta encontrar e parar o container Ollama
OLLAMA_CONTAINER=$(docker ps -a --format "{{.Names}}" | grep -i ollama | head -1)

if [ -n "$OLLAMA_CONTAINER" ]; then
    echo "Container encontrado: $OLLAMA_CONTAINER"
    docker stop "$OLLAMA_CONTAINER" 2>/dev/null || true
    docker rm "$OLLAMA_CONTAINER" 2>/dev/null || true
    echo "Ollama local removido."
else
    echo "Nenhum container Ollama encontrado."
fi

# Verifica uso de memoria apos parar
echo ""
echo "Uso de memoria atual:"
free -h 2>/dev/null || cat /proc/meminfo | head -3

echo ""
echo "=========================================="
echo "Concluido. Ollama Cloud (kimi-k2.6:cloud)"
echo "esta em uso via https://ollama.com"
echo "=========================================="
