<?php
/**
 * Email Controller
 * Controller para gerenciamento de emails e eventos
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Carrega autoloader para classes com namespace
require_once APPPATH . 'helpers/autoload_helper.php';

use Libraries\Email\EmailQueue;
use Libraries\Email\TemplateEngine;
use Libraries\Email\EmailTracker;
use Libraries\Scheduler\EventScheduler;

class Email extends MY_Controller
{
    private EmailQueue $queue;
    private TemplateEngine $templates;
    private EmailTracker $tracker;
    private EventScheduler $scheduler;

    public function __construct()
    {
        parent::__construct();

        $this->queue = new EmailQueue();
        $this->templates = new TemplateEngine();
        $this->tracker = new EmailTracker();
        $this->scheduler = new EventScheduler();

        // Verifica login (exceto para tracking e CLI)
        $method = $this->router->method;
        if (!$this->input->is_cli_request() && !in_array($method, ['track', 'click'])) {
            if (!$this->session->userdata('logado')) {
                redirect('login');
            }
        }
    }

    /**
     * Dashboard de emails
     */
    public function index()
    {
        try {
            $this->data['stats'] = $this->queue->getStats();
            $this->data['templates'] = $this->templates->listTemplates();
            $this->data['db_error'] = false;
        } catch (\Exception $e) {
            // Tabela não existe ou outro erro de banco
            $this->data['stats'] = ['pending' => 0, 'processing' => 0, 'sent' => 0, 'failed' => 0, 'cancelled' => 0];
            $this->data['templates'] = [];
            $this->data['db_error'] = true;
            $this->data['db_error_message'] = 'Tabelas de email não encontradas. Execute as migrations: php application/database/migrations/run_migrations.php';
        }

        $this->data['menuFerramentasV5'] = true;
        $this->data['menuEmailQueue'] = true;
        $this->data['view'] = 'emails/dashboard';

        return $this->layout();
    }

    /**
     * Dashboard de emails (alias)
     */
    public function dashboard()
    {
        $this->index();
    }

    /**
     * Endpoint para pixel de tracking
     */
    public function track($trackingId = '')
    {
        if (empty($trackingId)) {
            $trackingId = $this->uri->segment(3) ?? '';
        }

        if ($trackingId) {
            $this->tracker->trackOpen($trackingId);
        }

        // Retorna imagem 1x1 transparente (GIF)
        header('Content-Type: image/gif');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
    }

    /**
     * Endpoint para cliques rastreados
     */
    public function click($trackingId = '')
    {
        if (empty($trackingId)) {
            $trackingId = $this->uri->segment(3) ?? '';
        }

        $url = $this->input->get('url');
        if ($trackingId && $url) {
            $this->tracker->trackClick($trackingId, $url);
            redirect($url);
        }
        show_404();
    }

    /**
     * API: Enviar email
     */
    public function api_send()
    {
        if (!$this->input->post()) {
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'No data provided']));
            return;
        }

        $data = $this->input->post();

        // Validação
        if (empty($data['to']) || empty($data['subject']) || empty($data['template'])) {
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Required fields missing']));
            return;
        }

        try {
            // Renderiza template se necessário
            if (!empty($data['template_data'])) {
                $rendered = $this->templates->render($data['template'], $data['template_data'] ?? []);
                $data['body_html'] = $rendered['html'];
                $data['body_text'] = $rendered['text'];
            }

            // Adiciona à fila
            $id = $this->queue->enqueue([
                'to' => $data['to'],
                'to_name' => $data['to_name'] ?? null,
                'subject' => $data['subject'],
                'body_html' => $data['body_html'] ?? null,
                'body_text' => $data['body_text'] ?? null,
                'template' => $data['template'],
                'template_data' => $data['template_data'] ?? [],
                'priority' => $data['priority'] ?? 3
            ]);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'email_id' => $id,
                    'message' => 'Email adicionado à fila'
                ]));
        } catch (\Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_output(json_encode(['error' => $e->getMessage()]));
        }
    }

    /**
     * API: Agendar email
     */
    public function api_schedule()
    {
        $data = $this->input->post();

        if (empty($data['to']) || empty($data['subject']) || empty($data['template']) || empty($data['scheduled_at'])) {
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Required fields missing']));
            return;
        }

        try {
            $id = $this->queue->schedule([
                'to' => $data['to'],
                'to_name' => $data['to_name'] ?? null,
                'subject' => $data['subject'],
                'template' => $data['template'],
                'template_data' => $data['template_data'] ?? [],
                'priority' => $data['priority'] ?? 3
            ], $data['scheduled_at']);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'email_id' => $id,
                    'scheduled_at' => $data['scheduled_at']
                ]));
        } catch (\Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_output(json_encode(['error' => $e->getMessage()]));
        }
    }

    /**
     * API: Estatísticas
     */
    public function api_stats()
    {
        $stats = $this->queue->getStats();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'stats' => $stats,
                'timestamp' => date('Y-m-d H:i:s')
            ]));
    }

    /**
     * API: Cancelar email
     */
    public function api_cancel($id = 0)
    {
        if (!$id) {
            $id = (int) $this->uri->segment(3) ?? 0;
        }

        $success = $this->queue->cancel($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => $success,
                'message' => $success ? 'Email cancelado' : 'Não foi possível cancelar'
            ]));
    }

    /**
     * CLI: Processar fila de emails
     */
    public function cli_process()
    {
        if (!$this->input->is_cli_request()) {
            show_error('Acesso negado');
            return;
        }

        $limit = $this->input->get('limit') ?? 50;
        $emails = $this->queue->process((int)$limit);

        if (empty($emails)) {
            echo "Nenhum email para processar.\n";
            return;
        }

        // Carrega SMTP Pool
        $smtp = new \Libraries\Email\SmtpPool();
        $results = $smtp->sendBatch($emails);

        foreach ($results as $id => $result) {
            if ($result['success']) {
                $this->queue->markAsSent($id, $result['message_id'] ?? '');
                echo "✓ Email {$id} enviado\n";
            } else {
                $this->queue->markAsFailed($id, $result['error'] ?? 'Unknown error');
                echo "✗ Email {$id} falhou: " . ($result['error'] ?? 'Unknown error') . "\n";
            }
        }
    }

    /**
     * CLI: Processar eventos agendados
     */
    public function cli_events()
    {
        if (!$this->input->is_cli_request()) {
            show_error('Acesso negado');
            return;
        }

        $this->scheduler->process();
        echo "Eventos processados.\n";
    }

    /**
     * CLI: Retry de emails falhados
     */
    public function cli_retry()
    {
        if (!$this->input->is_cli_request()) {
            show_error('Acesso negado');
            return;
        }

        $this->db->where('status', 'failed');
        $this->db->update('email_queue', ['status' => 'pending']);

        echo "Emails falhos reprocessados.\n";
    }

    /**
     * CLI: Limpar emails antigos
     */
    public function cli_cleanup()
    {
        if (!$this->input->is_cli_request()) {
            show_error('Acesso negado');
            return;
        }

        $dataLimite = date('Y-m-d', strtotime('-90 days'));
        $this->db->where('created_at <', $dataLimite);
        $this->db->delete('email_queue');

        echo "Emails antigos limpos.\n";
    }

    // Métodos legados para compatibilidade
    public function process()
    {
        $this->cli_process();
    }

    public function retry()
    {
        $this->cli_retry();
    }
}
