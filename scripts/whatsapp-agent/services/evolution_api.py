import requests
import config

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
