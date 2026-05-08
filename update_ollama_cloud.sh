#!/bin/bash
# =============================================================================
# Atualiza workflows n8n de Ollama local para Ollama Cloud (kimi-k2.6:cloud)
# Execute no servidor: bash update_ollama_cloud.sh <OLLAMA_API_KEY>
# =============================================================================

set -e

OLLAMA_API_KEY="${1:-$OLLAMA_API_KEY}"

if [ -z "$OLLAMA_API_KEY" ]; then
    echo "ERRO: Forneca a OLLAMA_API_KEY como argumento ou variavel de ambiente."
    echo "Uso: bash update_ollama_cloud.sh SUA_CHAVE_OLLAMA"
    exit 1
fi

N8N_URL="http://localhost:5678"
CLOUD_URL="https://ollama.com/api/generate"
CLOUD_MODEL="kimi-k2.6:cloud"

echo "=========================================="
echo "Atualizando workflows n8n para Ollama Cloud"
echo "Modelo: $CLOUD_MODEL"
echo "=========================================="

# Verifica se Python3 esta disponivel
if ! command -v python3 &> /dev/null; then
    echo "ERRO: python3 nao encontrado. Instale python3 para continuar."
    exit 1
fi

# Cria script Python temporario
TMP_SCRIPT=$(mktemp /tmp/update_n8n_ollama.XXXXXX.py)

cat > "$TMP_SCRIPT" << 'PYEOF'
import json
import sys
import urllib.request
import urllib.error

OLLAMA_API_KEY = sys.argv[1]
N8N_URL = sys.argv[2]
CLOUD_URL = sys.argv[3]
CLOUD_MODEL = sys.argv[4]

def api_call(path, method="GET", data=None):
    url = f"{N8N_URL}{path}"
    headers = {"Content-Type": "application/json"}
    if data:
        data = json.dumps(data).encode("utf-8")
    req = urllib.request.Request(url, data=data, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req) as resp:
            return json.loads(resp.read().decode("utf-8"))
    except urllib.error.HTTPError as e:
        print(f"  HTTP Error {e.code}: {e.read().decode('utf-8')}")
        return None
    except Exception as e:
        print(f"  Erro: {e}")
        return None

print("\n--- Listando workflows ---")
workflows = api_call("/rest/workflows")
if not workflows or "data" not in workflows:
    print("ERRO: Nao foi possivel listar workflows. Verifique se o n8n esta rodando.")
    sys.exit(1)

targets = []
for wf in workflows["data"]:
    name = wf.get("name", "")
    if any(k in name.lower() for k in ["agente ia", "orcamento", "consulta"]):
        targets.append(wf)
        print(f"  Encontrado: {name} (id={wf['id']})")

if not targets:
    print("AVISO: Nenhum workflow relevante encontrado. Buscando todos...")
    targets = workflows["data"]

updated_any = False

for wf_summary in targets:
    wf_id = wf_summary["id"]
    wf_name = wf_summary.get("name", wf_id)
    print(f"\n--- Processando: {wf_name} ---")

    wf = api_call(f"/rest/workflows/{wf_id}")
    if not wf:
        continue

    nodes = wf.get("nodes", [])
    modified = False

    for node in nodes:
        params = node.get("parameters", {})
        url = params.get("url", "")
        body = params.get("jsonBody", "") or params.get("body", "")

        # Detecta referencias a Ollama local
        local_patterns = [
            "localhost:11434",
            "127.0.0.1:11434",
            "192.168.100.238:11434",
            "172.18.0.1:11434",
            "ollama:11434",
            ":11434/api/generate"
        ]

        is_local_ollama = any(p in url for p in local_patterns)

        if is_local_ollama:
            print(f"  Node '{node.get('name', node['id'])}' usa Ollama local: {url}")
            params["url"] = CLOUD_URL

            # Adiciona header de autorizacao
            params["sendHeaders"] = True
            if "headerParameters" not in params:
                params["headerParameters"] = {"parameters": []}

            # Remove Authorization antigo se existir
            params["headerParameters"]["parameters"] = [
                p for p in params["headerParameters"]["parameters"]
                if p.get("name", "").lower() != "authorization"
            ]

            params["headerParameters"]["parameters"].append({
                "name": "Authorization",
                "value": f"Bearer {OLLAMA_API_KEY}"
            })

            # Atualiza modelo no corpo
            old_body = str(body)
            new_body = old_body
            for old_model in ["hermes3:8b", "llama3.2:3b", "llama3.2", "mistral", "qwen2.5"]:
                if old_model in new_body:
                    new_body = new_body.replace(old_model, CLOUD_MODEL)
                    print(f"    Modelo atualizado: {old_model} -> {CLOUD_MODEL}")

            if "jsonBody" in params:
                params["jsonBody"] = new_body
            elif "body" in params:
                params["body"] = new_body

            modified = True

    if modified:
        print(f"  Salvando workflow {wf_name}...")
        result = api_call(f"/rest/workflows/{wf_id}", method="PUT", data=wf)
        if result:
            print(f"  OK: Workflow atualizado com sucesso.")
            updated_any = True
        else:
            print(f"  ERRO: Falha ao salvar workflow.")
    else:
        print(f"  Nenhum node Ollama local encontrado neste workflow.")

print("\n==========================================")
if updated_any:
    print("Atualizacao concluida! Reinicie o n8n se necessario.")
else:
    print("Nenhum workflow foi modificado.")
print("==========================================")
PYEOF

python3 "$TMP_SCRIPT" "$OLLAMA_API_KEY" "$N8N_URL" "$CLOUD_URL" "$CLOUD_MODEL"
rm -f "$TMP_SCRIPT"
