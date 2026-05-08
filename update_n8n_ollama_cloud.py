import json
import sys
import urllib.request
import urllib.error
import base64

OLLAMA_API_KEY = sys.argv[1]
N8N_USER = sys.argv[2]
N8N_PASS = sys.argv[3]
N8N_URL = sys.argv[4]
CLOUD_URL = sys.argv[5]
CLOUD_MODEL = sys.argv[6]

AUTH_HEADER = "Basic " + base64.b64encode(f"{N8N_USER}:{N8N_PASS}".encode()).decode()

def api_call(path, method="GET", data=None):
    url = f"{N8N_URL}{path}"
    headers = {"Content-Type": "application/json", "Authorization": AUTH_HEADER}
    if data:
        data = json.dumps(data).encode("utf-8")
    req = urllib.request.Request(url, data=data, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req, timeout=30) as resp:
            return json.loads(resp.read().decode("utf-8"))
    except urllib.error.HTTPError as e:
        body = e.read().decode("utf-8")
        print(f"  HTTP Error {e.code}: {body[:200]}")
        return None
    except Exception as e:
        print(f"  Erro: {e}")
        return None

print("\n--- Listando workflows ---")
workflows = api_call("/rest/workflows")
if not workflows or "data" not in workflows:
    print("ERRO: Nao foi possivel listar workflows.")
    sys.exit(1)

targets = []
for wf in workflows["data"]:
    name = wf.get("name", "")
    if any(k in name.lower() for k in ["agente ia", "orcamento", "consulta", "audio", "relatorio", "cobranca", "satisfacao", "webhook"]):
        targets.append(wf)
        print(f"  Encontrado: {name} (id={wf['id']})")

if not targets:
    print("AVISO: Nenhum workflow relevante encontrado. Buscando todos ativos...")
    targets = [wf for wf in workflows["data"] if wf.get("active", False)]

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

        local_patterns = [
            "localhost:11434", "127.0.0.1:11434", "192.168.100.238:11434",
            "172.18.0.1:11434", "ollama:11434", ":11434/api/generate"
        ]
        is_local_ollama = any(p in url for p in local_patterns)

        if is_local_ollama:
            print(f"  Node '{node.get('name', node['id'])}' usa Ollama local: {url}")
            params["url"] = CLOUD_URL
            params["sendHeaders"] = True
            if "headerParameters" not in params:
                params["headerParameters"] = {"parameters": []}
            params["headerParameters"]["parameters"] = [
                p for p in params["headerParameters"]["parameters"]
                if p.get("name", "").lower() != "authorization"
            ]
            params["headerParameters"]["parameters"].append({
                "name": "Authorization",
                "value": f"Bearer {OLLAMA_API_KEY}"
            })
            old_body = str(body)
            new_body = old_body
            for old_model in ["hermes3:8b", "llama3.2:3b", "llama3.2", "mistral", "qwen2.5", "gemma"]:
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
        print(f"  Nenhum node Ollama local encontrado.")

print("\n==========================================")
if updated_any:
    print("Atualizacao concluida!")
else:
    print("Nenhum workflow foi modificado.")
print("==========================================")
