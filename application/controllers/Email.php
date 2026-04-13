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

    /**
     * Editar template de email
     */
    public function editar_template($template = '')
    {
        if (empty($template)) {
            $template = $this->uri->segment(3) ?? '';
        }

        if (empty($template)) {
            show_error('Template não especificado');
            return;
        }

        // Garante que o template exista
        $this->templates->createTemplateIfNotExists($template);

        $this->data['template_name'] = $template;
        $this->data['template_content'] = $this->templates->getTemplateContent($template) ?? '';
        $this->data['available_tags'] = $this->templates->getAvailableTags();
        $this->data['menuFerramentasV5'] = true;
        $this->data['menuEmailQueue'] = true;
        $this->data['view'] = 'emails/editar_template';

        return $this->layout();
    }

    /**
     * Salvar template editado
     */
    public function salvar_template()
    {
        $template = $this->input->post('template');
        $content = $this->input->post('content');

        if (empty($template) || $content === null) {
            $this->session->set_flashdata('error', 'Dados incompletos');
            redirect('emails');
            return;
        }

        try {
            $this->templates->saveTemplate($template, $content);
            $this->session->set_flashdata('success', 'Template "' . ucfirst(str_replace('_', ' ', $template)) . '" salvo com sucesso!');
        } catch (\Exception $e) {
            $this->session->set_flashdata('error', 'Erro ao salvar template: ' . $e->getMessage());
        }

        redirect('emails/editar_template/' . $template);
    }

    /**
     * Preview de template com dados de exemplo
     */
    public function preview($template = '')
    {
        if (empty($template)) {
            $template = $this->uri->segment(3) ?? '';
        }

        if (empty($template)) {
            show_404();
            return;
        }

        // Dados de exemplo para preview
        $sampleData = [
            'titulo' => 'Preview do Template',
            'cliente_nome' => 'João da Silva',
            'cliente_email' => 'joao@exemplo.com',
            'cliente_telefone' => '(11) 98765-4321',
            'cliente_celular' => '(11) 91234-5678',
            'cliente_endereco' => 'Rua Exemplo, 123 - São Paulo/SP',
            'cliente_documento' => '123.456.789-00',
            'os_id' => '1234',
            'os_titulo' => 'Manutenção de Computador',
            'os_descricao' => 'Troca de memória RAM e formatação',
            'os_status' => 'Em Andamento',
            'os_data_criacao' => date('d/m/Y'),
            'os_data_vencimento' => date('d/m/Y', strtotime('+7 days')),
            'os_valor_total' => '1.250,00',
            'os_link_visualizar' => base_url('os/visualizar/1234'),
            'venda_id' => '5678',
            'venda_data' => date('d/m/Y'),
            'venda_valor_total' => '2.500,00',
            'venda_status' => 'Finalizada',
            'venda_link_visualizar' => base_url('vendas/visualizar/5678'),
            'usuario_nome' => 'Admin Sistema',
            'usuario_email' => 'admin@sistema.com',
            'empresa_nome' => 'Minha Empresa LTDA',
            'empresa_telefone' => '(11) 3333-4444',
            'empresa_email' => 'contato@empresa.com',
            'empresa_endereco' => 'Av. Principal, 1000 - Centro',
            'data_atual' => date('d/m/Y'),
            'hora_atual' => date('H:i'),
            'sistema_url' => base_url(),
            'ano_atual' => date('Y'),
            'cobranca_descricao' => 'Fatura Mensal - Abril/2026',
            'cobranca_valor' => '450,00',
            'cobranca_data_vencimento' => date('d/m/Y', strtotime('+5 days')),
            'cobranca_dias_atraso' => '0',
            'cobranca_link_pagamento' => base_url('pagamento/fatura/123'),
            'mensagem' => 'Esta é uma mensagem de exemplo para o preview do template.',
            'conteudo' => 'Conteúdo personalizado de exemplo.',
            'destinatario' => 'João da Silva',
            'link' => base_url('cliente/painel'),
        ];

        $html = $this->templates->preview($template, $sampleData);

        echo $html;
    }

    /**
     * AJAX: Listar tags disponíveis
     */
    public function api_tags()
    {
        $tags = $this->templates->getAvailableTags();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'tags' => $tags
            ]));
    }
}
