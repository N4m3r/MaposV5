import requests
import tempfile
import os
import logging
import config

logger = logging.getLogger(__name__)


class EvolutionAPI:
    def __init__(self):
        self.base_url = config.EVOLUTION_URL
        self.api_key = config.EVOLUTION_API_KEY
        self.instance = config.EVOLUTION_INSTANCE
        self.headers = {
            'apikey': self.api_key,
            'Content-Type': 'application/json'
        }

    def enviar_texto(self, numero: str, mensagem: str, delay: int = 1200):
        """Envia mensagem de texto via Evolution Go"""
        url = f"{self.base_url}/send/text"
        payload = {
            'number': numero,
            'text': mensagem,
            'delay': delay
        }
        try:
            resp = requests.post(url, headers=self.headers, json=payload, timeout=30)
            data = resp.json()
            return {
                'success': resp.status_code == 200,
                'status_code': resp.status_code,
                'data': data
            }
        except Exception as e:
            return {'success': False, 'error': str(e)}

    def enviar_documento(self, numero: str, file_path: str, caption: str = ''):
        """Envia documento (PDF) via Evolution Go"""
        url = f"{self.base_url}/send/media"
        try:
            with open(file_path, 'rb') as f:
                files = {'file': f}
                data = {
                    'number': numero,
                    'caption': caption,
                    'mediatype': 'document'
                }
                resp = requests.post(url, headers={'apikey': self.api_key}, files=files, data=data, timeout=60)
                return {
                    'success': resp.status_code == 200,
                    'status_code': resp.status_code,
                    'data': resp.json() if resp.text else {}
                }
        except Exception as e:
            return {'success': False, 'error': str(e)}

    def baixar_midia(self, message_key: str = None, media_key: str = None, msg_id: str = None) -> dict:
        """
        Baixa midia (audio/imagem) do WhatsApp via Evolution API.
        Retorna dict com 'file_path' do arquivo temporario ou 'error'.
        """
        # Tentar endpoint de download de midia
        endpoints = [
            f"{self.base_url}/chat/getBase64FromMediaMessage/{self.instance}",
            f"{self.base_url}/message/mediaByUrl/{self.instance}",
        ]

        # Metodo 1: getBase64FromMediaMessage
        payload = {}
        if message_key:
            payload["message"] = {"key": message_key} if isinstance(message_key, str) else message_key
        if msg_id:
            payload["message"] = {"key": {"id": msg_id}}

        url = f"{self.base_url}/chat/getBase64FromMediaMessage/{self.instance}"
        try:
            resp = requests.post(url, headers=self.headers, json=payload, timeout=60)
            if resp.status_code == 200:
                data = resp.json()
                base64_data = data.get('base64', data.get('data', {}))
                content_type = data.get('mimetype', 'audio/ogg')

                if base64_data:
                    import base64
                    ext = '.ogg' if 'ogg' in content_type else '.mp3' if 'mp3' in content_type else '.wav'
                    tmp = tempfile.NamedTemporaryFile(suffix=ext, delete=False)
                    tmp.write(base64.b64decode(base64_data))
                    tmp.close()
                    return {'success': True, 'file_path': tmp.name, 'content_type': content_type}
        except Exception as e:
            logger.warning(f"Metodo getBase64FromMediaMessage falhou: {e}")

        # Metodo 2: Se tem URL direta, baixar
        return {'success': False, 'error': 'Nao foi possivel baixar a midia'}

    def enviar_botoes(self, numero: str, title: str, description: str,
                      buttons: list, footer: str = ''):
        """Envia mensagem com botões interativos (max 3 reply buttons).
        buttons: [{'type':'reply','displayText':'...','id':'...'}]
        """
        url = f"{self.base_url}/send/button"
        payload = {
            'number': numero,
            'title': title,
            'description': description,
            'footer': footer or 'JJ Ferreiras',
            'buttons': buttons,
            'delay': 1200
        }
        try:
            resp = requests.post(url, headers=self.headers, json=payload, timeout=30)
            data = resp.json() if resp.text else {}
            return {
                'success': resp.status_code == 200,
                'status_code': resp.status_code,
                'data': data
            }
        except Exception as e:
            logger.error(f"Erro ao enviar botoes: {e}")
            return {'success': False, 'error': str(e)}

    def enviar_lista(self, numero: str, title: str, description: str,
                     button_text: str, sections: list, footer: str = ''):
        """Envia menu de lista interativo com seções e itens.
        sections: [{'title':'Seção','rows':[{'title':'Item','description':'desc','rowId':'cmd'}]}]
        """
        url = f"{self.base_url}/send/list"
        payload = {
            'number': numero,
            'title': title,
            'description': description,
            'buttonText': button_text or 'Ver opcoes',
            'footerText': footer or 'JJ Ferreiras',
            'sections': sections,
            'delay': 1200
        }
        try:
            resp = requests.post(url, headers=self.headers, json=payload, timeout=30)
            data = resp.json() if resp.text else {}
            return {
                'success': resp.status_code == 200,
                'status_code': resp.status_code,
                'data': data
            }
        except Exception as e:
            logger.error(f"Erro ao enviar lista: {e}")
            return {'success': False, 'error': str(e)}

    def baixar_audio_url(self, url: str) -> dict:
        """Baixa audio de URL direta."""
        try:
            resp = requests.get(url, headers={'apikey': self.api_key}, timeout=60)
            resp.raise_for_status()

            content_type = resp.headers.get('content-type', 'audio/ogg')
            if 'ogg' in content_type:
                ext = '.ogg'
            elif 'mp3' in content_type:
                ext = '.mp3'
            elif 'wav' in content_type:
                ext = '.wav'
            else:
                ext = '.ogg'

            tmp = tempfile.NamedTemporaryFile(suffix=ext, delete=False)
            tmp.write(resp.content)
            tmp.close()
            return {'success': True, 'file_path': tmp.name, 'content_type': content_type}

        except Exception as e:
            logger.error(f"Erro ao baixar audio: {e}")
            return {'success': False, 'error': str(e)}