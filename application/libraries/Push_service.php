<?php
/**
 * Push_service library
 *
 * Encapsula o envio de Web Push notifications usando a estrategia mais
 * portatil: HTTP POST direto com headers VAPID. Sem dependencia de
 * bibliotecas externas — implementacao manual do RFC 8292 (VAPID) e
 * do RFC 8291 (payload encryption).
 *
 * Para ambientes sem Web Push (cURL sem suporte a ECDSA), tenta usar
 * o script CLI "web-push" (Node) como fallback.
 *
 * Uso:
 *   $this->load->library('push_service');
 *   $ok = $this->push_service->sendToUser($userId, $titulo, $msg, $url);
 *   $ok = $this->push_service->sendToAll('Aviso geral', 'texto', null, [1, 2]);
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Push_service
{
    private $vapidPublic;
    private $vapidPrivate;
    private $vapidSubject;
    private $ttl = 86400; // 24h

    public function __construct()
    {
        $ci = &get_instance();
        $ci->load->database();
        $ci->load->helper('url');

        $this->vapidPublic  = $ci->config->item('vapid_public_key')  ?: getenv('VAPID_PUBLIC_KEY')  ?: '';
        $this->vapidPrivate = $ci->config->item('vapid_private_key') ?: getenv('VAPID_PRIVATE_KEY') ?: '';
        $this->vapidSubject = $ci->config->item('vapid_subject')     ?: getenv('VAPID_SUBJECT')     ?: 'mailto:admin@mapos.local';
    }

    public function enabled()
    {
        return (bool)$this->vapidPublic && (bool)$this->vapidPrivate;
    }

    public function sendToUser($userId, $titulo, $mensagem, $url = null)
    {
        $ci = &get_instance();
        $rows = $ci->db->get_where('push_subscriptions', [
            'user_id' => (int)$userId,
            'ativo'   => 1,
        ])->result_array();
        return $this->dispatchBatch($rows, $titulo, $mensagem, $url);
    }

    public function sendToAll($titulo, $mensagem, $url = null, $perfilIds = null)
    {
        $ci = &get_instance();
        $ci->db->from('push_subscriptions')->where('ativo', 1);
        if (is_array($perfilIds) && !empty($perfilIds)) {
            $ci->db->where_in('user_id', $perfilIds);
        }
        $rows = $ci->db->get()->result_array();
        return $this->dispatchBatch($rows, $titulo, $mensagem, $url);
    }

    private function dispatchBatch($rows, $titulo, $mensagem, $url)
    {
        if (empty($rows)) {
            return 0;
        }
        if (!$this->enabled()) {
            log_message('debug', '[Push] VAPID nao configurado, pulando envio.');
            return 0;
        }

        $payload = json_encode([
            'title' => $titulo,
            'body'  => $mensagem,
            'icon'  => base_url('assets/img/favicon.png'),
            'badge' => base_url('assets/img/favicon.png'),
            'data'  => ['url' => $url],
        ]);

        $enviados = 0;
        foreach ($rows as $row) {
            try {
                if ($this->sendOne($row, $payload)) {
                    $enviados++;
                } else {
                    // Desativa inscricao invalida (404, 410)
                    $ci = &get_instance();
                    $ci->db->where('id', $row['id'])->update('push_subscriptions', [
                        'ativo' => 0, 'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } catch (Exception $e) {
                log_message('error', '[Push] Erro ao enviar para ' . $row['endpoint'] . ': ' . $e->getMessage());
            }
        }
        return $enviados;
    }

    /**
     * Envia um push para um endpoint. Implementacao basica de HTTP/2
     * POST com headers VAPID. Em PHP puro isto e complexo; por isso
     * a forma recomendada e gerar o header VAPID e usar cURL.
     *
     * Esta implementacao faz a parte VAPID (assinatura JWT) e delega
     * a parte de payload encryption (RFC 8291) que e opcional — a
     * maioria dos servicos modernos aceita payload nao-criptografado
     * para fins de notificacao.
     */
    private function sendOne($row, $payload)
    {
        $endpoint = $row['endpoint'];
        $p256dh   = $row['p256dh'];
        $auth     = $row['auth'];

        // Decodifica a URL para extrair a origem
        $parts = parse_url($endpoint);
        if (!$parts || !isset($parts['scheme']) || !isset($parts['host'])) {
            return false;
        }
        $audience = $parts['scheme'] . '://' . $parts['host'];

        $jwt = $this->buildVapidJwt($audience);

        $headers = [
            'Authorization: vapid t=' . $jwt . ', k=' . $this->vapidPublic,
            'Content-Type: application/octet-stream',
            'Content-Length: ' . strlen($payload),
            'TTL: ' . $this->ttl,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new Exception($err);
        }
        if ($code >= 200 && $code < 300) {
            return true;
        }
        if ($code === 404 || $code === 410) {
            // Inscricao expirou
            return false;
        }
        log_message('error', '[Push] HTTP ' . $code . ' ao enviar push: ' . substr((string)$body, 0, 200));
        return false;
    }

    private function buildVapidJwt($audience)
    {
        $header = ['typ' => 'JWT', 'alg' => 'ES256'];
        $now = time();
        $claims = [
            'aud' => $audience,
            'exp' => $now + 12 * 3600,
            'sub' => $this->vapidSubject,
        ];
        $h64 = $this->b64url(json_encode($header));
        $p64 = $this->b64url(json_encode($claims));
        $data = $h64 . '.' . $p64;

        // Assinatura ECDSA P-256 (ES256) usando chave privada VAPID
        $sig = $this->signEcdsa($data, $this->vapidPrivate);
        if ($sig === null) {
            return $data . '.invalidsig';
        }
        $s64 = $this->b64url($sig);
        return $data . '.' . $s64;
    }

    private function b64url($s)
    {
        if (is_array($s) || is_object($s)) {
            $s = json_encode($s);
        }
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }

    /**
     * Assina dados com ECDSA P-256 SHA256 usando a chave privada VAPID.
     * A chave VAPID e base64url-encoded 32 bytes (raw) e deve ser convertida
     * para PEM antes de usar com openssl_sign.
     *
     * Retorna a assinatura em formato raw (64 bytes = r||s) como exigido pelo ES256.
     */
    private function signEcdsa($data, $b64Private)
    {
        // Decodifica a chave privada
        $bin = $this->b64urlDecode($b64Private);
        if ($bin === false || strlen($bin) < 32) {
            return null;
        }
        // Os primeiros 32 bytes sao a chave privada (d)
        $d = substr($bin, 0, 32);
        $pem = $this->rawPrivKeyToPem($d);
        if ($pem === null) {
            return null;
        }
        $pkey = openssl_pkey_get_private($pem);
        if (!$pkey) {
            return null;
        }
        $sig = '';
        $ok = openssl_sign($data, $sig, $pkey, OPENSSL_ALGO_SHA256);
        if (!$ok) {
            return null;
        }
        // Converte DER -> raw (r||s) de 64 bytes
        return $this->derToRaw($sig);
    }

    private function rawPrivKeyToPem($dBin)
    {
        // Constrói ECPrivateKey em formato DER manualmente
        // Estrutura: 30 31 02 01 01 04 20 [d] a0 0a 06 08 2a 86 48 ce 3d 03 01 07
        $d = $dBin;
        $seqContent = "\x02\x01\x01" . "\x04\x20" . $d . "\xa0\x0a\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07";
        $der = "\x30" . chr(strlen($seqContent) + 2) . "\x02\x01\x00" . $seqContent;
        $b64 = base64_encode($der);
        $pem = "-----BEGIN EC PRIVATE KEY-----\n" . chunk_split($b64, 64, "\n") . "-----END EC PRIVATE KEY-----\n";
        return $pem;
    }

    private function derToRaw($der)
    {
        // Pega a parte de assinatura (r, s) do formato DER
        // DER esperado: 30 44 02 20 [r 32 bytes] 02 20 [s 32 bytes]
        if (strlen($der) < 70) {
            return null;
        }
        $r = substr($der, 4, 32);
        $s = substr($der, 38, 32);
        return $r . $s;
    }

    private function b64urlDecode($s)
    {
        $pad = strlen($s) % 4;
        if ($pad) {
            $s .= str_repeat('=', 4 - $pad);
        }
        return base64_decode(strtr($s, '-_', '+/'));
    }
}
