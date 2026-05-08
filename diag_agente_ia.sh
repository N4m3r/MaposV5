#!/bin/bash
# Diagnostico Agente IA - Evolution + n8n + Ollama
# Execute no servidor: bash diag_agente_ia.sh
# Cole o output aqui

echo "=========================================="
echo "DIAGNOSTICO AGENTE IA"
echo "Data: $(date)"
echo "=========================================="

# 1. Sistema
echo -e "\n--- SISTEMA ---"
hostname
uptime
whoami

# 2. Servicos - Evolution API
echo -e "\n--- EVOLUTION API (WhatsApp) ---"
if command -v docker &> /dev/null; then
    docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep -i evolution || echo "Nenhum container 'evolution' encontrado"
    docker ps --format "table {{.Names}}\t{{.Status}}" | grep -i whatsapp || echo "Nenhum container 'whatsapp' encontrado"
    docker ps --format "table {{.Names}}\t{{.Status}}" | head -20
else
    echo "Docker nao instalado"
    systemctl status evolution 2>/dev/null || echo "Servico evolution nao encontrado no systemd"
fi

# Testa API Evolution local
echo -e "\n--- Teste API Evolution (localhost) ---"
curl -s http://localhost:8080/instance/fetchInstances 2>/dev/null | head -c 200 || echo "Evolution nao responde em localhost:8080"
curl -s http://localhost:3000/instance/fetchInstances 2>/dev/null | head -c 200 || echo "Evolution nao responde em localhost:3000"

# 3. Servicos - n8n
echo -e "\n--- N8N ---"
if command -v docker &> /dev/null; then
    docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep -i n8n || echo "Nenhum container 'n8n' encontrado"
fi

curl -s http://localhost:5678/rest/workflows 2>/dev/null | head -c 300 || echo "n8n nao responde em localhost:5678"

# 4. Servicos - Ollama
echo -e "\n--- OLLAMA ---"
if command -v docker &> /dev/null; then
    docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep -i ollama || echo "Nenhum container 'ollama' encontrado"
fi

curl -s http://localhost:11434/api/tags 2>/dev/null | head -c 300 || echo "Ollama nao responde em localhost:11434"

# 5. Teste conectividade API v2 MapOS (producao)
echo -e "\n--- CONECTIVIDADE API MAPOS (producao) ---"
curl -s -o /dev/null -w "HTTP: %{http_code} | Tempo: %{time_total}s" \
    -H "X-API-Key: t4AZOtKkYyTlFHrYaORx33AsfFmwP6Ja/H1yJKPiV4Q=" \
    "https://jj-ferreiras.com.br/MaposV5/api/v2/health" 2>/dev/null
echo ""

# 6. Logs - Docker
echo -e "\n--- LOGS RECENTES (ultimas 20 linhas) ---"
if command -v docker &> /dev/null; then
    # Tenta encontrar containers por nome
    EVOLUTION=$(docker ps --format "{{.Names}}" | grep -i evolution | head -1)
    N8N=$(docker ps --format "{{.Names}}" | grep -i n8n | head -1)
    OLLAMA=$(docker ps --format "{{.Names}}" | grep -i ollama | head -1)

    if [ -n "$EVOLUTION" ]; then
        echo -e "\n>> Evolution ($EVOLUTION):"
        docker logs --tail 20 "$EVOLUTION" 2>&1 | tail -20
    fi

    if [ -n "$N8N" ]; then
        echo -e "\n>> n8n ($N8N):"
        docker logs --tail 20 "$N8N" 2>&1 | tail -20
    fi

    if [ -n "$OLLAMA" ]; then
        echo -e "\n>> Ollama ($OLLAMA):"
        docker logs --tail 20 "$OLLAMA" 2>&1 | tail -20
    fi
fi

# 7. Verificar webhooks/configuracoes
echo -e "\n--- VERIFICACAO WEBHOOKS/CONFIGS ---"
# Verifica se existe algum arquivo de config do n8n
ls -la ~/ 2>/dev/null | grep -E "n8n|workflow|webhook" || true
ls -la /opt/ 2>/dev/null | grep -E "n8n|evolution|ollama" || true
ls -la /var/lib/docker/volumes/ 2>/dev/null | head -10 || true

# 8. Rede - IPs locais
echo -e "\n--- REDE LOCAL ---"
ip addr 2>/dev/null | grep "inet " | head -5

# 9. Portas abertas
echo -e "\n--- PORTAS ABERTAS ---"
command -v ss &> /dev/null && ss -tlnp | grep -E "8080|3000|5678|11434|22" || netstat -tlnp 2>/dev/null | grep -E "8080|3000|5678|11434|22" || echo "ss/netstat nao disponivel"

echo -e "\n=========================================="
echo "FIM DO DIAGNOSTICO"
echo "=========================================="
