<?php
/**
 * WhatsappController - API v2
 * Recebe webhooks da Evolution API (mensagens recebidas) e processa/logs
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class WhatsappController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * POST /api/v2/webhooks/evolution
     *
     * Recebe webhook da Evolution API quando chega mensagem.
     * Body padrao Evolution v2:
     *   data:
     *     key:
     *       remoteJid: "5511999999999@s.whatsapp.net"
     *     message:
     *       conversation: "texto da mensagem"
     *     messageTimestamp: 1234567890
     *
     * Retorna 200 imediatamente para nao reenviar.
     */
    public function evolution_post()
    {
        $input = $this->getJsonInput();
        $body  = $input['data'] ?? $input;

        $key      = $body['key'] ?? [];
        $message  = $body['message'] ?? [];
        $pushName = $body['pushName'] ?? 'Desconhecido';

        $remoteJid = $key['remoteJid'] ?? '';
        $numero    = $this->extrairNumero($remoteJid);
        $texto     = $message['conversation'] ?? ($message['extendedTextMessage']['text'] ?? '');
        $tipoMsg   = empty($texto) && !empty($message['audioMessage']) ? 'audio' : 'texto';

        if (!$numero || !$texto) {
            return $this->success(['status' => 'ignored', 'motivo' => 'Sem numero ou texto']);
        }

        // Loga conversa
        $this->db->insert('agente_ia_logs_conversa', [
            'numero_telefone'    => $numero,
            'tipo'               => 'recebido',
            'mensagem'           => $texto,
            'intencao_detectada' => null,
            'metadados_json'     => json_encode([
                'pushName'   => $pushName,
                'tipo_msg'   => $tipoMsg,
                'remote_jid' => $remoteJid,
            ]),
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        // TODO: encaminhar para n8n webhook se configurado
        $n8nWebhook = $this->getN8nWebhook();
        if ($n8nWebhook) {
            $this->encaminharN8n($n8nWebhook, [
                'numero'   => $numero,
                'texto'    => $texto,
                'pushName' => $pushName,
                'tipo'     => $tipoMsg,
            ]);
        }

        return $this->success(['status' => 'recebido', 'log_id' => $this->db->insert_id()]);
    }

    /**
     * POST /api/v2/webhooks/evolution/status
     *
     * Recebe atualizacoes de status (enviado, entregue, lido)
     */
    public function evolution_status_post()
    {
        $input = $this->getJsonInput();

        $this->db->insert('agente_ia_logs_conversa', [
            'numero_telefone'    => $this->extrairNumero($input['data']['key']['remoteJid'] ?? ''),
            'tipo'               => 'sistema',
            'mensagem'           => json_encode($input),
            'intencao_detectada' => 'status_update',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        return $this->success(['status' => 'logged']);
    }

    // ========================================================================
    // UTIL
    // ========================================================================

    private function extrairNumero(string $remoteJid): string
    {
        // Formato: 5511999999999@s.whatsapp.net
        $numero = preg_replace('/[^0-9]/', '', $remoteJid);
        return $numero;
    }

    private function getN8nWebhook(): string
    {
        if (!$this->db->table_exists('agente_ia_configuracoes')) {
            return '';
        }
        $row = $this->db
            ->where('chave', 'n8n_webhook_url')
            ->get('agente_ia_configuracoes')
            ->row();
        return $row ? ($row->valor ?? '') : '';
    }

    private function encaminharN8n(string $url, array $dados): void
    {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            log_message('error', '[WhatsappController] Falha ao encaminhar para n8n: ' . $e->getMessage());
        }
    }

    protected function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }
}
