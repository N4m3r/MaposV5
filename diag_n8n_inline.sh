#!/bin/bash
echo "=========================================="
echo "DIAGNOSTICO N8N - AGENTE IA"
echo "Data: $(date)"
echo "=========================================="

echo -e "\n--- CONTAINER N8N ---"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep -i n8n || echo "Container n8n nao encontrado"

echo -e "\n--- LOGS N8N (ultimas 50 linhas) ---"
N8N=$(docker ps --format "{{.Names}}" | grep -i n8n | head -1)
if [ -n "$N8N" ]; then docker logs --tail 50 "$N8N" 2>&1 | tail -50; else echo "Container n8n nao encontrado"; fi

echo -e "\n--- TESTE API N8N ---"
curl -s http://localhost:5678/rest/workflows 2>/dev/null | python3 -m json.tool 2>/dev/null | head -60 || echo "n8n nao responde ou python3 nao disponivel"

echo -e "\n--- WORKFLOWS ATIVOS ---"
curl -s http://localhost:5678/rest/workflows 2>/dev/null | grep -E '"id"|"name"|"active"' | head -40 || echo "Nao foi possivel listar workflows"

echo -e "\n--- EXECUCOES RECENTES COM ERRO ---"
curl -s "http://localhost:5678/rest/executions?filter={\"status\":[\"error\"]}&limit=10" 2>/dev/null | python3 -m json.tool 2>/dev/null | head -80 || echo "Nao foi possivel listar execucoes"

echo -e "\n--- TESTE API MAPOS (producao) ---"
curl -s -o /dev/null -w "HTTP: %{http_code} | Tempo: %{time_total}s" -H "X-API-Key: t4AZOtKkYyTlFHrYaORx33AsfFmwP6Ja/H1yJKPiV4Q=" "https://jj-ferreiras.com.br/MaposV5/api/v2/health" 2>/dev/null
echo ""

echo -e "\n=========================================="
echo "FIM DO DIAGNOSTICO"
echo "=========================================="