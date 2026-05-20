"""
Descriptografia de midia do WhatsApp (formato .enc).
O WhatsApp criptografa midias com AES-256-CBC usando uma media key
derivada via HKDF-SHA256.
"""
import base64
import hashlib
import hmac
import logging
import os
import tempfile
import requests

logger = logging.getLogger(__name__)


def decrypt_whatsapp_media(enc_data: bytes, media_key_b64: str) -> bytes:
    """
    Descriptografa midia criptografada do WhatsApp.

    Processo:
    1. Decodifica a media key de base64
    2. Deriva IV, cipher key e MAC key via HKDF-SHA256
    3. Verifica o MAC (ultimos 10 bytes do arquivo)
    4. Descriptografa com AES-256-CBC
    5. Remove padding PKCS7
    """
    from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
    from cryptography.hazmat.primitives import padding
    from cryptography.hazmat.backends import default_backend
    import hmac as hmac_module

    # Decodificar media key
    media_key = base64.b64decode(media_key_b64)

    # Derivar chaves via HKDF-SHA256
    # WhatsApp usa info="WhatsApp Media Keys" para derivar 112 bytes
    expanded = _hkdf_sha256(media_key, length=112, info=b"WhatsApp Media Keys")

    iv = expanded[:16]
    cipher_key = expanded[16:48]
    mac_key = expanded[48:80]

    # O arquivo .enc tem: dados_criptografados + MAC (10 bytes truncados)
    # O MAC real e HMAC-SHA256 truncado para 10 bytes
    if len(enc_data) < 10:
        raise ValueError("Arquivo criptografado muito pequeno")

    mac = enc_data[-10:]
    encrypted = enc_data[:-10]

    # Verificar MAC
    expected_mac = hmac_module.new(mac_key, iv + encrypted, hashlib.sha256).digest()[:10]
    if not hmac_module.compare_digest(mac, expected_mac):
        logger.warning("MAC verification failed - tentando descriptografar mesmo assim")

    # Descriptografar com AES-256-CBC
    cipher = Cipher(algorithms.AES(cipher_key), modes.CBC(iv), backend=default_backend())
    decryptor = cipher.decryptor()
    decrypted = decryptor.update(encrypted) + decryptor.finalize()

    # Remover padding PKCS7
    pad_len = decrypted[-1]
    if pad_len > 0 and pad_len <= 16:
        decrypted = decrypted[:-pad_len]

    return decrypted


def _hkdf_sha256(key: bytes, length: int, info: bytes = b"") -> bytes:
    """HKDF-SHA256 (RFC 5869) simplificado."""
    # Extract
    prk = hmac.new(b"\x00" * 32, key, hashlib.sha256).digest()

    # Expand
    hash_len = 32
    n = (length + hash_len - 1) // hash_len
    okm = b""
    t = b""
    for i in range(1, n + 1):
        t = hmac.new(prk, t + info + bytes([i]), hashlib.sha256).digest()
        okm += t

    return okm[:length]


def download_and_decrypt_audio(url: str, media_key_b64: str, timeout: int = 30) -> dict:
    """
    Baixa e descriptografa audio do WhatsApp.
    Retorna dict com 'file_path' do arquivo temporario descriptografado ou 'error'.
    """
    try:
        # Baixar arquivo .enc
        logger.info(f"Baixando audio criptografado de: {url[:80]}...")
        resp = requests.get(url, timeout=timeout)
        resp.raise_for_status()
        enc_data = resp.content
        logger.info(f"Audio baixado: {len(enc_data)} bytes")

        # Descriptografar
        dec_data = decrypt_whatsapp_media(enc_data, media_key_b64)
        logger.info(f"Audio descriptografado: {len(dec_data)} bytes")

        # Salvar como arquivo temporario .ogg
        ext = '.ogg'
        tmp = tempfile.NamedTemporaryFile(suffix=ext, delete=False)
        tmp.write(dec_data)
        tmp.close()

        return {'success': True, 'file_path': tmp.name, 'size': len(dec_data)}

    except ImportError:
        logger.error("Biblioteca cryptography nao instalada. Instale com: pip install cryptography")
        return {'success': False, 'error': 'Biblioteca cryptography nao instalada'}
    except Exception as e:
        logger.error(f"Erro ao baixar/descriptografar audio: {e}")
        return {'success': False, 'error': str(e)}