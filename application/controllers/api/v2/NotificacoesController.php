<?php
/**
 * NotificacoesController - API v2
 * Endpoints para templates de notificacao e logs (usado pelo n8n / Agente IA)
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class NotificacoesController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * GET /api/v2/notificacoes/template
     *
     * Query:
     *   evento (string) tipo do evento ex: os.created, cliente.novo
     *   ativo  (int)    0 ou 1 — default 1
     *
     * Retorna template de mensagem para o evento solicitado.
     */
    public function template_get()
    {
        $evento = $this->input->get('evento');
        $ativo  = (int) ($this->input->get('ativo') ?? 1);

        if (!$evento) {
            return $this->error('Parametro obrigatorio: evento', 400);
        }

        // Templates padrao por evento
        $templates = [
            'os.created' => [
                'evento'   => 'os.created',
                'template' => 'Ola {{cliente_nome}}! Sua OS #{{os_id}} foi criada com sucesso.',
                'canal'    => 'whatsapp',
            ],
            'os.updated' => [
                'evento'   => 'os.updated',
                'template' => 'Ola {{cliente_nome}}! Sua OS #{{os_id}} foi atualizada. Status: {{status}}.',
                'canal'    => 'whatsapp',
            ],
            'os.finalizada' => [
                'evento'   => 'os.finalizada',
                'template' => 'Ola {{cliente_nome}}! Sua OS #{{os_id}} foi finalizada. Agradecemos a preferencia!',
                'canal'    => 'whatsapp',
            ],
            'cliente.novo' => [
                'evento'   => 'cliente.novo',
                'template' => 'Bem-vindo {{cliente_nome}}! Seu cadastro foi realizado com sucesso.',
                'canal'    => 'whatsapp',
            ],
            'cobranca.paid' => [
                'evento'   => 'cobranca.paid',
                'template' => 'Ola {{cliente_nome}}! Recebemos seu pagamento referente a OS #{{os_id}}. Obrigado!',
                'canal'    => 'whatsapp',
            ],
            'cobranca.vencendo' => [
                'evento'   => 'cobranca.vencendo',
                'template' => 'Ola {{cliente_nome}}! Passando para lembrar que existe uma cobranca da OS #{{os_id}} com vencimento em {{data_vencimento}}.',
                'canal'    => 'whatsapp',
            ],
            'webhook.test' => [
                'evento'   => 'webhook.test',
                'template' => 'Teste de webhook funcionando! Evento: {{evento}}',
                'canal'    => 'whatsapp',
            ],
        ];

        $template = $templates[$evento] ?? null;

        if (!$template) {
            return $this->success([
                'evento'   => $evento,
                'template' => 'Ola {{cliente_nome}}! Atualizacao sobre OS #{{os_id}}.',
                'canal'    => 'whatsapp',
                'fallback' => true,
            ]);
        }

        return $this->success([
            'evento'   => $template['evento'],
            'template' => $template['template'],
            'canal'    => $template['canal'],
            'ativo'    => $ativo,
        ]);
    }

    /**
     * POST /api/v2/notificacoes/log
     *
     * Body:
     *   tipo         (string)
     *   destinatario (string) numero ou email
     *   mensagem     (string)
     *   status       (string) enviado|erro|lido
     *   os_id        (int, opcional)
     *
     * Registra log de notificacao enviada.
     */
    public function log_post()
    {
        $tipo         = $this->input->post('tipo');
        $destinatario = $this->input->post('destinatario');
        $mensagem     = $this->input->post('mensagem');
        $status       = $this->input->post('status') ?: 'enviado';
        $osId         = (int) ($this->input->post('os_id') ?: 0);

        if (!$tipo || !$destinatario) {
            return $this->error('Campos obrigatorios: tipo, destinatario', 400);
        }

        // Se existir tabela de logs de notificacao, insere; senao, insere no agente_ia_logs_conversa
        $tabela = $this->db->table_exists('notificacoes_log') ? 'notificacoes_log' : 'agente_ia_logs_conversa';

        $this->db->insert($tabela, [
            'numero_telefone'    => $destinatario,
            'tipo'               => 'sistema',
            'mensagem'           => $mensagem,
            'intencao_detectada' => $tipo,
            'metadados_json'     => json_encode([
                'destinatario' => $destinatario,
                'status_envio' => $status,
                'os_id'        => $osId,
            ]),
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        return $this->success([
            'log_id' => $this->db->insert_id(),
            'status' => 'registrado'
        ]);
    }
}
