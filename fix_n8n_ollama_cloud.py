import json
import subprocess
import sys

OLLAMA_API_KEY = sys.argv[1]
CLOUD_URL = "https://ollama.com/api/generate"
CLOUD_MODEL = "kimi-k2.6:cloud"

LOCAL_PATTERNS = [
    "localhost:11434",
    "127.0.0.1:11434",
    "192.168.100.238:11434",
    "172.18.0.1:11434",
    "ollama:11434",
    ":11434/api/generate"
]

OLD_MODELS = ["hermes3:8b", "llama3.2:3b", "llama3.2", "mistral", "qwen2.5", "gemma"]

def export_workflow(wf_id, path):
    subprocess.run(["docker", "exec", "n8n", "n8n", "export:workflow", f"--id={wf_id}", "--pretty", f"--output={path}"], check=True)

def import_workflow(path):
    subprocess.run(["docker", "exec", "n8n", "n8n", "import:workflow", f"--input={path}"], check=True)

def read_workflow(path):
    result = subprocess.run(["docker", "exec", "n8n", "cat", path], capture_output=True, text=True, check=True)
    return json.loads(result.stdout)

def write_workflow(path, data):
    content = json.dumps(data, indent=2, ensure_ascii=False)
    subprocess.run(["docker", "exec", "n8n", "sh", "-c", f"cat > {path} <<'PYEOF'\n{content}\nPYEOF"], check=True)

def is_local_ollama(url):
    return any(p in url for p in LOCAL_PATTERNS)

def update_node(node):
    params = node.get("parameters", {})
    url = params.get("url", "")
    body = params.get("jsonBody", "") or params.get("body", "")

    if not is_local_ollama(url):
        return False

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
    for old_model in OLD_MODELS:
        if old_model in new_body:
            new_body = new_body.replace(old_model, CLOUD_MODEL)
            print(f"    Modelo atualizado: {old_model} -> {CLOUD_MODEL}")

    if "jsonBody" in params:
        params["jsonBody"] = new_body
    elif "body" in params:
        params["body"] = new_body

    return True

workflow_ids = ["agente-ia-consultas-01", "agente-ia-orcamento-audio-01"]

updated_any = False
for wf_id in workflow_ids:
    print(f"\n--- Processando workflow: {wf_id} ---")
    tmp_path = f"/tmp/{wf_id}_fix.json"

    export_workflow(wf_id, tmp_path)
    data = read_workflow(tmp_path)
    # n8n export format is a list of workflows
    if isinstance(data, list) and len(data) > 0:
        wf = data[0]
    else:
        wf = data

    modified = False
    for node in wf.get("nodes", []):
        if update_node(node):
            modified = True

    if modified:
        write_workflow(tmp_path, data)
        import_workflow(tmp_path)
        print(f"  OK: Workflow {wf_id} atualizado.")
        updated_any = True
    else:
        print(f"  Nenhum node Ollama local encontrado.")

print("\n==========================================")
if updated_any:
    print("Atualizacao concluida!")
else:
    print("Nenhum workflow foi modificado.")
print("==========================================")
