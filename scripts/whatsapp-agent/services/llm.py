import json
import requests
import logging
import config
from services import nlp

logger = logging.getLogger(__name__)

SYSTEM_PROMPT = """Voce e o assistente virtual da JJ Ferreiras, uma empresa de assistencia tecnica.
Seu trabalho e classificar a intencao do cliente e extrair entidades da mensagem.

Intencoes possiveis:
- status_os: Cliente quer saber o status de suas ordens de servico
- detalhes_os: Cliente quer detalhes de uma OS especifica (precisa numero)
- quanto_devo: Cliente quer saber valor em aberto/divida
- minhas_os_hoje: Tecnico quer ver suas OS do dia
- relatorio_os: Admin quer resumo de OS do dia
- os_atrasadas: Admin quer ver OS em atraso
- vendas_pendentes: Admin quer vendas nao faturadas
- cobrancas_vencidas: Admin quer cobrancas vencidas
- criar_os: Cliente quer criar/abrir nova OS
- total_os_abertas: Admin quer total de OS em aberto
- ajuda: Cliente cumprimenta, pede ajuda ou quer ver o menu de opcoes
- sair: Cliente quer encerrar a conversa, se despedir ou agradecer
- relatorio_financeiro: Admin quer resumo financeiro (receitas, despesas, lucro)
- relatorio_vendas: Admin quer relatorio de vendas do periodo
- relatorio_estoque: Admin quer relatorio de estoque (produtos e alertas)
- relatorio_produtividade: Admin quer produtividade dos tecnicos
- relatorio_clientes_top: Admin quer ranking dos melhores clientes
- relatorio_os_periodo: Admin quer OS por periodo/mes
- relatorio_atrasados: Admin quer relatorio detalhado de atrasados
- alterar_status_os: Usuario quer alterar/mudar o status de uma OS (precisa numero da OS e novo status)

IMPORTANTE: Qualquer saudacao (oi, ola, bom dia, boa tarde, hey, etc.) DEVE ser classificada como "ajuda" para mostrar o menu de opcoes.
Qualquer despedida (tchau, obrigado, valeu, ate mais, sair, #sair) DEVE ser classificada como "sair".

Responda APENAS com JSON valido, sem markdown:
{"intencao": "nome_da_intencao", "entidades": {"os_id": null, "cliente_nome": null, "defeito": null, "valor": null, "periodo": null}}

Exemplos:
"como esta minha os?" -> {"intencao": "status_os", "entidades": {"os_id": null}}
"quero saber da os 45" -> {"intencao": "detalhes_os", "entidades": {"os_id": 45}}
"to devendo quanto?" -> {"intencao": "quanto_devo", "entidades": {}}
"oi" -> {"intencao": "ajuda", "entidades": {}}
"bom dia" -> {"intencao": "ajuda", "entidades": {}}
"menu" -> {"intencao": "ajuda", "entidades": {}}
"tchau" -> {"intencao": "sair", "entidades": {}}
"obrigado" -> {"intencao": "sair", "entidades": {}}
"#sair" -> {"intencao": "sair", "entidades": {}}
"preciso abrir uma os pro cliente Joao, defeito nao liga" -> {"intencao": "criar_os", "entidades": {"cliente_nome": "Joao", "defeito": "nao liga"}}
"relatorio financeiro" -> {"intencao": "relatorio_financeiro", "entidades": {}}
"vendas do mes" -> {"intencao": "relatorio_vendas", "entidades": {}}
"como esta o estoque" -> {"intencao": "relatorio_estoque", "entidades": {}}
"produtividade dos tecnicos" -> {"intencao": "relatorio_produtividade", "entidades": {}}
"quais os melhores clientes" -> {"intencao": "relatorio_clientes_top", "entidades": {}}
"os do mes" -> {"intencao": "relatorio_os_periodo", "entidades": {}}
"quem esta atrasado" -> {"intencao": "relatorio_atrasados", "entidades": {}}
"mudar status da os 15 para Finalizado" -> {"intencao": "alterar_status_os", "entidades": {"os_id": 15, "novo_status": "Finalizado"}}
"alterar status da os 23" -> {"intencao": "alterar_status_os", "entidades": {"os_id": 23}}
"finalizar os 10" -> {"intencao": "alterar_status_os", "entidades": {"os_id": 10, "novo_status": "Finalizado"}}
"cancelar os 55" -> {"intencao": "alterar_status_os", "entidades": {"os_id": 55, "novo_status": "Cancelado"}}
"cheguei na os 12" -> {"intencao": "checkin_tecnico", "entidades": {"os_id": 12}}
"checkin os 8" -> {"intencao": "checkin_tecnico", "entidades": {"os_id": 8}}
"saindo da os 12" -> {"intencao": "checkout_tecnico", "entidades": {"os_id": 12}}
"terminei atendimento os 5" -> {"intencao": "checkout_tecnico", "entidades": {"os_id": 5}}"""


def classificar_com_llm(texto: str) -> dict:
    """Classifica intencao usando LLM. Retorna dict com intencao e entidades."""
    provider = config.LLM_PROVIDER.lower().strip()

    if provider == 'ollama':
        return _classificar_ollama(texto)
    elif provider == 'openai':
        return _classificar_openai(texto)
    elif provider == 'anthropic':
        return _classificar_anthropic(texto)

    return None


def _classificar_ollama(texto: str) -> dict:
    """Classifica usando Ollama (local ou cloud)."""
    url = f"{config.OLLAMA_URL.rstrip('/')}/api/generate"
    model = config.LLM_MODEL

    payload = {
        "model": model,
        "prompt": f"{SYSTEM_PROMPT}\n\nMensagem do usuario: {texto}",
        "stream": False,
        "options": {
            "temperature": 0.1,
            "num_predict": 200
        }
    }

    headers = {"Content-Type": "application/json"}

    if config.OLLAMA_API_KEY:
        headers["Authorization"] = f"Bearer {config.OLLAMA_API_KEY}"

    try:
        resp = requests.post(url, json=payload, headers=headers, timeout=60)
        resp.raise_for_status()
        data = resp.json()

        response_text = data.get("response", "").strip()

        result = _parse_llm_response(response_text)
        if result:
            logger.info(f"LLM classificou: {texto!r} -> {result}")
            return result

        logger.warning(f"LLM retornou JSON invalido: {response_text!r}")
        return None

    except requests.exceptions.Timeout:
        logger.error("Timeout ao chamar Ollama")
        return None
    except requests.exceptions.ConnectionError:
        logger.error("Ollama indisponivel (conexao recusada)")
        return None
    except Exception as e:
        logger.error(f"Erro ao chamar Ollama: {e}")
        return None


def _classificar_openai(texto: str) -> dict:
    """Classifica usando OpenAI-compatible API (cloud providers: Ollama cloud, OpenAI, Groq, Together, etc.)."""
    url = f"{config.LLM_CLOUD_URL}/chat/completions"
    model = config.LLM_CLOUD_MODEL or config.LLM_MODEL

    payload = {
        "model": model,
        "messages": [
            {"role": "system", "content": SYSTEM_PROMPT},
            {"role": "user", "content": texto}
        ],
        "temperature": 0.1,
        "max_tokens": 200
    }

    headers = {"Content-Type": "application/json"}
    if config.LLM_CLOUD_API_KEY:
        headers["Authorization"] = f"Bearer {config.LLM_CLOUD_API_KEY}"

    try:
        resp = requests.post(url, json=payload, headers=headers, timeout=30)
        resp.raise_for_status()
        data = resp.json()

        response_text = data.get("choices", [{}])[0].get("message", {}).get("content", "").strip()

        result = _parse_llm_response(response_text)
        if result:
            logger.info(f"Cloud LLM classificou: {texto!r} -> {result}")
            return result

        logger.warning(f"Cloud LLM retornou JSON invalido: {response_text!r}")
        return None

    except requests.exceptions.Timeout:
        logger.error("Timeout ao chamar Cloud LLM")
        return None
    except requests.exceptions.ConnectionError:
        logger.error("Cloud LLM indisponivel (conexao recusada)")
        return None
    except Exception as e:
        logger.error(f"Erro ao chamar Cloud LLM: {e}")
        return None


def _classificar_anthropic(texto: str) -> dict:
    """Classifica usando Anthropic API (Claude)."""
    url = "https://api.anthropic.com/v1/messages"
    model = config.LLM_CLOUD_MODEL or 'claude-haiku-4-5-20251001'

    payload = {
        "model": model,
        "max_tokens": 200,
        "messages": [
            {"role": "user", "content": f"{SYSTEM_PROMPT}\n\nMensagem do usuario: {texto}"}
        ]
    }

    headers = {
        "Content-Type": "application/json",
        "anthropic-version": "2023-06-01",
    }
    if config.LLM_CLOUD_API_KEY:
        headers["x-api-key"] = config.LLM_CLOUD_API_KEY
    elif config.AGENT_API_KEY:
        headers["x-api-key"] = config.AGENT_API_KEY

    try:
        resp = requests.post(url, json=payload, headers=headers, timeout=30)
        resp.raise_for_status()
        data = resp.json()

        response_text = ""
        for block in data.get("content", []):
            if block.get("type") == "text":
                response_text += block.get("text", "")

        result = _parse_llm_response(response_text.strip())
        if result:
            logger.info(f"Anthropic classificou: {texto!r} -> {result}")
            return result

        logger.warning(f"Anthropic retornou JSON invalido: {response_text!r}")
        return None

    except requests.exceptions.Timeout:
        logger.error("Timeout ao chamar Anthropic API")
        return None
    except requests.exceptions.ConnectionError:
        logger.error("Anthropic API indisponivel (conexao recusada)")
        return None
    except Exception as e:
        logger.error(f"Erro ao chamar Anthropic: {e}")
        return None


def _parse_llm_response(response: str) -> dict:
    """Extrai JSON da resposta do LLM, tolerando markdown e texto extra."""
    if not response:
        return None

    # Tentar parse direto
    try:
        return _validate_classification(json.loads(response))
    except json.JSONDecodeError:
        pass

    # Tentar extrair JSON de dentro de markdown code block
    import re
    json_match = re.search(r'```(?:json)?\s*(\{.*?\})\s*```', response, re.DOTALL)
    if json_match:
        try:
            return _validate_classification(json.loads(json_match.group(1)))
        except json.JSONDecodeError:
            pass

    # Tentar encontrar primeiro { ... } na string
    brace_match = re.search(r'\{[^{}]*\}', response, re.DOTALL)
    if brace_match:
        try:
            return _validate_classification(json.loads(brace_match.group(0)))
        except json.JSONDecodeError:
            pass

    # Fallback: se o LLM respondeu em texto natural, tentar extrair intencao
    return _extract_intent_from_text(response)


def _extract_intent_from_text(response: str) -> dict | None:
    """Quando o LLM retorna texto natural em vez de JSON, tenta extrair a intencao."""
    if not response:
        return None

    texto = response.lower().strip()

    # Mapeamento de palavras-chave para intencoes
    intencoes_keywords = {
        'criar_os': ['criar os', 'nova os', 'abrir os', 'cadastrar os',
                      'criar ordem', 'abrir ordem', 'nova ordem',
                      'ordem de serviço', 'ordem de servico',
                      'abri uma os', 'quero uma os', 'preciso de uma os'],
        'status_os': ['status os', 'minha os', 'os aberta', 'como esta minha os'],
        'detalhes_os': ['detalhes da os', 'informações da os', 'ver os'],
        'quanto_devo': ['quanto devo', 'divida', 'valor em aberto'],
        'relatorio_financeiro': ['relatorio financeiro', 'financeiro', 'receitas', 'despesas'],
        'relatorio_vendas': ['relatorio vendas', 'vendas do'],
        'relatorio_estoque': ['relatorio estoque', 'estoque'],
        'alterar_status_os': ['alterar status', 'mudar status', 'finalizar os', 'cancelar os'],
        'ajuda': ['ajuda', 'menu', 'opcoes', 'o que voce faz'],
        'sair': ['sair', 'tchau', 'obrigado', 'encerrar'],
    }

    for intencao, keywords in intencoes_keywords.items():
        for kw in keywords:
            if kw in texto:
                return {'intencao': intencao, 'entidades': {}}

    return None


def _validate_classification(data: dict) -> dict:
    """Valida e normaliza a classificacao retornada pelo LLM."""
    intencao = data.get("intencao", "desconhecido")
    entidades = data.get("entidades", {})

    intencoes_validas = {
        'status_os', 'detalhes_os', 'quanto_devo', 'minhas_os_hoje',
        'relatorio_os', 'os_atrasadas', 'vendas_pendentes',
        'cobrancas_vencidas', 'criar_os', 'total_os_abertas',
        'ajuda', 'sair', 'desconhecido',
        'relatorio_financeiro', 'relatorio_vendas', 'relatorio_estoque',
        'relatorio_produtividade', 'relatorio_clientes_top',
        'relatorio_os_periodo', 'relatorio_atrasados',
        'alterar_status_os', 'checkin_tecnico', 'checkout_tecnico'
    }

    if intencao not in intencoes_validas:
        intencao = 'desconhecido'

    if entidades is None:
        entidades = {}

    # Normalizar os_id para int se presente
    if 'os_id' in entidades and entidades['os_id'] is not None:
        try:
            entidades['os_id'] = int(entidades['os_id'])
        except (ValueError, TypeError):
            entidades['os_id'] = None

    return {"intencao": intencao, "entidades": entidades}


OS_AUDIO_PROMPT = """Voce e um assistente de abertura de Ordens de Servico da JJ Ferreiras.
O usuario enviou um audio que foi transcrito. Sua tarefa e extrair as informacoes relevantes
para a etapa ATUAL do fluxo de criacao de OS.

Etapa atual: {etapa}
Dados ja informados: {dados_atuais}

Transcricao do audio: "{texto_audio}"

Regras por etapa:
- "cliente": extraia o nome do cliente, CNPJ, ou "Loja X"
- "defeito": extraia a descricao completa do defeito/problema relatado
- "equipamento": extraia o nome do equipamento ou produto mencionado. Se o usuario nao souber ou nao mencionar, retorne "pular"
- "produto_servico": identifique se o usuario quer adicionar "produto", "servico" ou "pular"
- "buscar_item": extraia APENAS o nome do produto ou servico para buscar no catalogo (ex: "camera Intelbras", "instalacao de camera")
- "quantidade_item": extraia APENAS o numero inteiro da quantidade (ex: "dois" -> 2, "tres" -> 3)
- "valor": extraia APENAS o valor numerico em reais (ex: "cento e cinquenta" -> 150.00, "trezentos e vinte" -> 320.00)
- "confirmar": identifique se o usuario quer "confirmar", "cancelar" ou "corrigir"
- Se o audio contem informacoes de MULTIPLAS etapas (ex: cliente + defeito + equipamento juntos), extraia TUDO nos dados_extras

Exemplos:
Audio: "quero criar uma OS pra Loja 36, o defeito e que a TV nao liga"
-> {{"texto_processado": "Loja 36", "etapa_detectada": "cliente", "dados_extras": {{"cliente_nome": "Loja 36", "defeito": "TV nao liga"}}}}

Audio: "camera Intelbras modelo 1120"
-> {{"texto_processado": "camera Intelbras modelo 1120", "etapa_detectada": "buscar_item", "dados_extras": {{"tipo_item": "produto", "nome_busca": "camera Intelbras 1120"}}}}

Audio: "servico de instalacao de camera"
-> {{"texto_processado": "servico de instalacao de camera", "etapa_detectada": "buscar_item", "dados_extras": {{"tipo_item": "servico", "nome_busca": "instalacao de camera"}}}}

Audio: "quero um produto"
-> {{"texto_processado": "produto", "etapa_detectada": "produto_servico", "dados_extras": {{"tipo_item": "produto"}}}}

Audio: "quero adicionar um servico"
-> {{{"texto_processado": "servico", "etapa_detectada": "produto_servico", "dados_extras": {{"tipo_item": "servico"}}}}

Audio: "equipamento e um DVR Intelbras de 4 canais"
-> {{{"texto_processado": "DVR Intelbras de 4 canais", "etapa_detectada": "equipamento", "dados_extras": {{"descricao": "DVR Intelbras de 4 canais"}}}}

Audio: "o valor e trezentos e cinquenta"
-> {{{"texto_processado": "350.00", "etapa_detectada": "valor", "dados_extras": {{"valor": 350.00}}}}

Audio: "quantidade 6"
-> {{{"texto_processado": "6", "etapa_detectada": "quantidade_item", "dados_extras": {{"quantidade": 6}}}}

Audio: "confirmo, pode criar"
-> {{{"texto_processado": "confirmar", "etapa_detectada": "confirmar", "dados_extras": {{}}}}

Responda APENAS com JSON valido, sem markdown:
{{{{"texto_processado": "texto limpo e relevante para a etapa atual", "etapa_detectada": "nome_da_etapa", "dados_extras": {{{{"cliente_nome": null, "defeito": null, "descricao": null, "tipo_item": null, "nome_busca": null, "valor": null, "quantidade": null}}}}}}}}

Preencha apenas os campos que forem detectados no audio. Os demais devem ser null."""


def interpretar_audio_os(texto_audio: str, etapa: str, dados_atuais: dict) -> dict:
    """Interpreta audio transcrito no contexto da criacao de OS.
    Retorna dict com texto_processado, etapa_detectada e dados_extras."""
    prompt = OS_AUDIO_PROMPT.format(
        etapa=etapa,
        dados_atuais=json.dumps(dados_atuais, ensure_ascii=False, default=str),
        texto_audio=texto_audio
    )

    provider = config.LLM_PROVIDER.lower().strip()
    if provider == 'ollama':
        return _interpretar_ollama(prompt)
    elif provider in ('openai', 'anthropic'):
        return _interpretar_openai(prompt)
    return None


def _interpretar_ollama(prompt: str) -> dict:
    """Interpreta audio usando Ollama."""
    url = f"{config.OLLAMA_URL.rstrip('/')}/api/generate"
    model = config.LLM_MODEL
    payload = {
        "model": model,
        "prompt": prompt,
        "stream": False,
        "options": {"temperature": 0.1, "num_predict": 300}
    }
    headers = {"Content-Type": "application/json"}
    if config.OLLAMA_API_KEY:
        headers["Authorization"] = f"Bearer {config.OLLAMA_API_KEY}"
    try:
        resp = requests.post(url, json=payload, headers=headers, timeout=30)
        resp.raise_for_status()
        response_text = resp.json().get("response", "").strip()
        return _parse_audio_response(response_text)
    except Exception as e:
        logger.error(f"Erro Ollama interpretar audio: {e}")
        return None


def _interpretar_openai(prompt: str) -> dict:
    """Interpreta audio usando OpenAI-compatible API."""
    url = f"{config.LLM_CLOUD_URL}/chat/completions"
    model = config.LLM_CLOUD_MODEL or config.LLM_MODEL
    payload = {
        "model": model,
        "messages": [
            {"role": "system", "content": prompt}
        ],
        "temperature": 0.1,
        "max_tokens": 300
    }
    headers = {"Content-Type": "application/json"}
    if config.LLM_CLOUD_API_KEY:
        headers["Authorization"] = f"Bearer {config.LLM_CLOUD_API_KEY}"
    try:
        resp = requests.post(url, json=payload, headers=headers, timeout=30)
        resp.raise_for_status()
        response_text = resp.json().get("choices", [{}])[0].get("message", {}).get("content", "").strip()
        return _parse_audio_response(response_text)
    except Exception as e:
        logger.error(f"Erro Cloud LLM interpretar audio: {e}")
        return None


def _parse_audio_response(response: str) -> dict:
    """Extrai JSON da resposta de interpretacao de audio."""
    import re as _re
    if not response:
        return None
    # Tentar parse direto
    try:
        return json.loads(response)
    except json.JSONDecodeError:
        pass
    # Extrair JSON de markdown (greedy para capturar JSON aninhado)
    json_match = _re.search(r'```(?:json)?\s*(\{.+\})\s*```', response, _re.DOTALL)
    if json_match:
        try:
            return json.loads(json_match.group(1))
        except json.JSONDecodeError:
            pass
    # Encontrar JSON simples { ... }
    brace_match = _re.search(r'\{[^{}]*\}', response, _re.DOTALL)
    if brace_match:
        try:
            return json.loads(brace_match.group(0))
        except json.JSONDecodeError:
            pass
    # Tentar JSON aninhado (dados_extras contem { })
    brace_match = _re.search(r'\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}', response, _re.DOTALL)
    if brace_match:
        try:
            return json.loads(brace_match.group(0))
        except json.JSONDecodeError:
            pass
    return None


OS_FULL_EXTRACTION_PROMPT = """Voce e um assistente da JJ Ferreiras (assistencia tecnica). O usuario enviou um audio querendo criar uma Ordem de Servico.

Transcricao: "{texto_audio}"

Extraia TODAS as informacoes da OS desse audio. Responda APENAS com JSON valido, sem markdown:

{{
  "cliente": "nome ou CNPJ ou Loja X mencionado, ou null se nao mencionado",
  "defeito": "descricao do defeito/problema relatado, ou null",
  "equipamento": "equipamento/produto mencionado, ou null",
  "tipo_item": "produto ou servico ou null",
  "nome_item": "nome do produto ou servico do catalogo mencionado, ou null",
  "quantidade": numero inteiro ou null,
  "valor": numero decimal (ex: 350.00) ou null,
  "confirma": true se o usuario confirmou, false caso contrario
}}

Regras:
- "Loja 36" → cliente = "Loja 36"
- "Nova Era loja 57" → cliente = "Loja 57"
- Se mencionar CNPJ, coloque o numero completo
- "camera Intelbras" → tipo_item = "produto", nome_item = "camera Intelbras"
- "instalacao de camera" → tipo_item = "servico", nome_item = "instalacao de camera"
- "6 passagens de ponto" → quantidade = 6, equipamento = "passagens de ponto"
- "valor de 350" ou "trezentos e cinquenta" → valor = 350.00
- Se o audio e vago ou incompleto, preencha apenas o que foi mencionado, o resto null

Exemplos:
"criar OS pra Loja 36, defeito TV nao liga" → {{"cliente":"Loja 36","defeito":"TV nao liga","equipamento":null,"tipo_item":null,"nome_item":null,"quantidade":null,"valor":null,"confirma":false}}
"OS para Joao Silva, defeito DVR nao grava, equipamento DVR Intelbras 4 canais, servico de instalacao, valor 350" → {{"cliente":"Joao Silva","defeito":"DVR nao grava","equipamento":"DVR Intelbras 4 canais","tipo_item":"servico","nome_item":"instalacao","quantidade":null,"valor":350.00,"confirma":false}}
"criar OS, cliente Nova Era loja 12, o sistema de camera ta com defeito, quero 3 camera Intelbras 1120, valor 500" → {{"cliente":"Loja 12","defeito":"sistema de camera com defeito","equipamento":null,"tipo_item":"produto","nome_item":"camera Intelbras 1120","quantidade":3,"valor":500.00,"confirma":false}}"""


def extrair_dados_os_audio(texto_audio: str) -> dict:
    """Extrai todos os dados de uma OS de um unico audio transcrito.
    Retorna dict com cliente, defeito, equipamento, tipo_item, nome_item, quantidade, valor, confirma."""
    prompt = OS_FULL_EXTRACTION_PROMPT.format(texto_audio=texto_audio)

    provider = config.LLM_PROVIDER.lower().strip()
    if provider == 'ollama':
        result = _interpretar_ollama(prompt)
    elif provider in ('openai', 'anthropic'):
        result = _interpretar_openai(prompt)
    else:
        return None

    if result and isinstance(result, dict):
        # Garantir tipos corretos
        if result.get('quantidade') is not None:
            try:
                result['quantidade'] = int(result['quantidade'])
            except (ValueError, TypeError):
                result['quantidade'] = None
        if result.get('valor') is not None:
            try:
                result['valor'] = float(result['valor'])
            except (ValueError, TypeError):
                result['valor'] = None
        if result.get('confirma') is not None:
            result['confirma'] = bool(result['confirma'])
        return result

    return None