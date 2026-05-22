import requests
import logging
import config
import tempfile
import os

logger = logging.getLogger(__name__)


def transcrever_audio(audio_url: str = None, audio_file: str = None, linguagem: str = "pt") -> dict:
    """
    Transcreve audio usando Whisper ASR.
    Aceita URL de audio ou caminho de arquivo local.
    Retorna: {"texto": "...", "sucesso": True/False, "erro": "..."}
    """
    whisper_url = config.WHISPER_URL.rstrip('/')

    try:
        if audio_file and os.path.exists(audio_file):
            return _transcrever_arquivo(audio_file, whisper_url, linguagem)
        elif audio_url:
            return _transcrever_url(audio_url, whisper_url, linguagem)
        else:
            return {"texto": "", "sucesso": False, "erro": "Nenhum audio fornecido"}

    except Exception as e:
        logger.error(f"Erro na transcricao: {e}")
        return {"texto": "", "sucesso": False, "erro": str(e)}


def _transcrever_arquivo(file_path: str, whisper_url: str, linguagem: str) -> dict:
    """Transcreve arquivo de audio local."""
    endpoint = f"{whisper_url}/asr"

    try:
        with open(file_path, 'rb') as f:
            files = {'audio_file': (os.path.basename(file_path), f)}
            data = {
                'language': linguagem,
                'task': 'transcribe'
            }
            resp = requests.post(endpoint, files=files, data=data, timeout=60)
            resp.raise_for_status()

            # Whisper ASR pode retornar JSON ou texto puro
            content_type = resp.headers.get('content-type', '')
            if 'application/json' in content_type:
                result = resp.json()
                texto = result.get('text', '').strip()
                if not texto:
                    texto = result.get('transcription', '').strip()
            else:
                # Resposta em texto puro (padrao do whisper-asr-webservice)
                texto = resp.text.strip()

            return {"texto": texto, "sucesso": bool(texto), "erro": ""}

    except requests.exceptions.ConnectionError:
        logger.error(f"Whisper indisponivel em {whisper_url}")
        return {"texto": "", "sucesso": False, "erro": "Servico de transcricao indisponivel"}
    except requests.exceptions.Timeout:
        logger.error("Timeout na transcricao Whisper")
        return {"texto": "", "sucesso": False, "erro": "Timeout na transcricao"}
    except Exception as e:
        logger.error(f"Erro ao transcrever arquivo: {e}")
        return {"texto": "", "sucesso": False, "erro": str(e)}


def _transcrever_url(audio_url: str, whisper_url: str, linguagem: str) -> dict:
    """Baixa audio da URL e transcreve."""
    try:
        resp = requests.get(audio_url, timeout=30)
        resp.raise_for_status()

        content_type = resp.headers.get('content-type', '')
        ext = _detectar_extensao(content_type, audio_url)

        with tempfile.NamedTemporaryFile(suffix=ext, delete=False) as tmp:
            tmp.write(resp.content)
            tmp_path = tmp.name

        try:
            return _transcrever_arquivo(tmp_path, whisper_url, linguagem)
        finally:
            os.unlink(tmp_path)

    except Exception as e:
        logger.error(f"Erro ao baixar/transcrever audio de URL: {e}")
        return {"texto": "", "sucesso": False, "erro": str(e)}


def _detectar_extensao(content_type: str, url: str) -> str:
    """Detecta extensao do arquivo de audio."""
    if 'ogg' in content_type or '.ogg' in url:
        return '.ogg'
    if 'mp3' in content_type or '.mp3' in url:
        return '.mp3'
    if 'wav' in content_type or '.wav' in url:
        return '.wav'
    if 'm4a' in content_type or '.m4a' in url:
        return '.m4a'
    if 'webm' in content_type or '.webm' in url:
        return '.webm'
    return '.ogg'