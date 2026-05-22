"""
Descriptografia de midia do WhatsApp (formato .enc).
O WhatsApp criptografa midias com AES-256-CBC usando uma media key
derivada via HKDF-SHA256.

Info string varia por tipo de midia:
- Audio: "WhatsApp Audio Keys"
- Image: "WhatsApp Image Keys"
- Video: "WhatsApp Video Keys"
- Document: "WhatsApp Document Keys"
"""
import base64
import hashlib
import hmac
import logging
import tempfile
import requests

logger = logging.getLogger(__name__)

# Info strings do HKDF por tipo de midia
MEDIA_TYPE_INFO = {
    'audio': b'WhatsApp Audio Keys',
    'image': b'WhatsApp Image Keys',
    'video': b'WhatsApp Video Keys',
    'document': b'WhatsApp Document Keys',
    'sticker': b'WhatsApp Image Keys',
}


def decrypt_whatsapp_media(enc_data: bytes, media_key_b64: str, media_type: str = 'audio') -> bytes:
    """
    Descriptografa midia criptografada do WhatsApp.

    Processo:
    1. Decodifica a media key de base64
    2. Deriva IV, cipher key e MAC key via HKDF-SHA256 com info especifico por tipo
    3. Verifica o MAC (ultimos 10 bytes do arquivo)
    4. Descriptografa com AES-256-CBC
    5. Remove padding PKCS7
    """
    from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
    from cryptography.hazmat.backends import default_backend
    import hmac as hmac_module

    # Decodificar media key
    media_key = base64.b64decode(media_key_b64)

    # Selecionar info string baseado no tipo de midia
    info = MEDIA_TYPE_INFO.get(media_type, b'WhatsApp Audio Keys')

    # Derivar chaves via HKDF-SHA256
    expanded = _hkdf_sha256(media_key, length=112, info=info)

    iv = expanded[:16]
    cipher_key = expanded[16:48]
    mac_key = expanded[48:80]

    # O arquivo .enc tem: dados_criptografados + MAC (10 bytes truncados)
    if len(enc_data) < 10:
        raise ValueError("Arquivo criptografado muito pequeno")

    mac = enc_data[-10:]
    encrypted = enc_data[:-10]

    # Verificar MAC
    expected_mac = hmac_module.new(mac_key, iv + encrypted, hashlib.sha256).digest()[:10]
    mac_ok = hmac_module.compare_digest(mac, expected_mac)
    if not mac_ok:
        logger.warning("MAC verification failed - tentando descriptografar mesmo assim")

    # Descriptografar com AES-256-CBC
    cipher = Cipher(algorithms.AES(cipher_key), modes.CBC(iv), backend=default_backend())
    decryptor = cipher.decryptor()
    decrypted = decryptor.update(encrypted) + decryptor.finalize()

    # Remover padding PKCS7
    pad_len = decrypted[-1]
    if 0 < pad_len <= 16 and all(b == pad_len for b in decrypted[-pad_len:]):
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

        # Descriptografar usando mediaKey com info "WhatsApp Audio Keys"
        dec_data = decrypt_whatsapp_media(enc_data, media_key_b64, media_type='audio')
        logger.info(f"Audio descriptografado: {len(dec_data)} bytes")

        # Verificar se o arquivo comeca com OGG header (Opus)
        is_valid_ogg = dec_data[:4] == b'OggS'
        if is_valid_ogg:
            logger.info("Audio descriptografado com sucesso - formato OGG valido")
        else:
            logger.warning(f"Audio descriptografado pode estar corrompido - header: {dec_data[:4].hex()}")

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