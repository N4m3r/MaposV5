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

IMPORTANTE: Qualquer saudacao (oi, ola, bom dia, boa tarde, hey, etc.) DEVE ser classificada como "ajuda" para mostrar o menu de opcoes.
Qualquer despedida (tchau, obrigado, valeu, ate mais, sair, #sair) DEVE ser classificada como "sair".

Responda APENAS com JSON valido, sem markdown:
{"intencao": "nome_da_intencao", "entidades": {"os_id": null, "cliente_nome": null, "defeito": null, "valor": null}}

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
"preciso abrir uma os pro cliente Joao, defeito nao liga" -> {"intencao": "criar_os", "entidades": {"cliente_nome": "Joao", "defeito": "nao liga"}}"""


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
    """Classifica usando Anthropic API (reserva futura)."""
    # TODO: implementar quando necessario
    return None


def _parse_llm_response(response: str) -> dict:
    """Extrai JSON da resposta do LLM, tolerando markdown e texto extra."""
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
    brace_match = re.search(r'\{[^{}]*\}', response)
    if brace_match:
        try:
            return _validate_classification(json.loads(brace_match.group(0)))
        except json.JSONDecodeError:
            pass

    return None


def _validate_classification(data: dict) -> dict:
    """Valida e normaliza a classificacao retornada pelo LLM."""
    intencao = data.get("intencao", "desconhecido")
    entidades = data.get("entidades", {})

    intencoes_validas = {
        'status_os', 'detalhes_os', 'quanto_devo', 'minhas_os_hoje',
        'relatorio_os', 'os_atrasadas', 'vendas_pendentes',
        'cobrancas_vencidas', 'criar_os', 'total_os_abertas',
        'ajuda', 'sair', 'desconhecido'
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


def classificar(texto: str) -> tuple:
    """Classifica intencao: tenta LLM primeiro, fallback para regex."""
    resultado = classificar_com_llm(texto)

    if resultado and resultado.get('intencao') != 'desconhecido':
        intencao = resultado['intencao']
        entidades = resultado.get('entidades', {})

        if intencao == 'saudacao':
            intencao = 'ajuda'

        return intencao, entidades

    logger.info(f"LLM falhou ou retornou desconhecido, usando regex para: {texto!r}")
    return nlp.classificar(texto)