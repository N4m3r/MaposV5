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
     * CLI: Testar envio de email
     * Uso: php index.php email cli_testar [email]
     */
    public function cli_testar($email = '')
    {
        if (!$this->input->is_cli_request()) {
            show_error('Acesso negado');
            return;
        }

        if (empty($email)) {
            $email = $this->input->get('email') ?? 'thailer.alfaia@gmail.com';
        }

        echo "Testando envio de email para: {$email}\n";

        try {
            $rendered = $this->templates->render('boas_vindas', [
                'cliente_nome' => 'Usuario de Teste',
                'cliente_email' => $email,
                'data_atual' => date('d/m/Y'),
                'sistema_url' => base_url(),
            ]);

            $id = $this->queue->enqueue([
                'to' => $email,
                'to_name' => 'Usuario de Teste',
                'subject' => 'Email de Teste - Sistema MAPOS',
                'body_html' => $rendered['html'],
                'body_text' => $rendered['text'] ?? strip_tags($rendered['html']),
                'template' => 'boas_vindas',
                'priority' => 1,
            ]);

            if ($id > 0) {
                echo "✓ Email enfileirado com sucesso! ID: {$id}\n";

                // Tenta enviar imediatamente via SMTP
                $smtp = new \Libraries\Email\SmtpPool();
                $emails = $this->queue->process(1);
                if (!empty($emails)) {
                    $results = $smtp->sendBatch($emails);
                    foreach ($results as $eid => $result) {
                        if ($result['success']) {
                            $this->queue->markAsSent($eid, $result['message_id'] ?? '');
                            echo "✓ Email {$eid} enviado com sucesso!\n";
                        } else {
                            $this->queue->markAsFailed($eid, $result['error'] ?? 'Unknown');
                            echo "✗ Falha ao enviar email {$eid}: " . ($result['error'] ?? 'Unknown') . "\n";
                        }
                    }
                } else {
                    echo "! Nenhum email pendente para processar (pode estar na fila).\n";
                }
            } else {
                echo "✗ Email bloqueado pela blacklist ou falha ao enfileirar.\n";
            }
        } catch (\Exception $e) {
            echo "✗ Erro: " . $e->getMessage() . "\n";
        }
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

    /**
     * Configuracoes de notificacoes por email
     */
    public function configuracoes()
    {
        $this->load->model('mapos_model');

        $configs = [
            'email_notif_os_criada' => true,
            'email_notif_os_editada' => true,
            'email_notif_venda' => true,
            'email_notif_cobranca' => true,
            'email_notif_obra_nova' => true,
            'email_notif_obra_concluida' => true,
            'email_notif_atividade_atrasada' => true,
            'email_notif_impedimento' => true,
            'email_automatico_v5' => true,
            'email_queue_interval' => '60',
            'email_batch_size' => '3',
            'email_smtp_host' => '',
            'email_smtp_port' => '587',
            'email_smtp_user' => '',
            'email_smtp_pass' => '',
            'email_smtp_crypto' => 'tls',
            'email_from' => '',
            'email_from_name' => 'Sistema',
            'email_cc_default' => '',
            'email_bcc_default' => '',
            'email_template_os_criada' => 'os_nova',
            'email_template_os_editada' => 'os_atualizada',
            'email_template_venda' => 'venda_realizada',
            'email_template_cobranca' => 'cobranca',
            'email_template_obra_nova' => 'obra_nova',
            'email_template_obra_concluida' => 'obra_concluida',
            'email_blacklist' => '',
        ];

        $this->db->where_in('config', array_keys($configs));
        $query = $this->db->get('configuracoes');
        if ($query) {
            foreach ($query->result() as $row) {
                if (in_array($row->config, ['email_queue_interval', 'email_batch_size', 'email_smtp_port'])) {
                    $configs[$row->config] = $row->valor;
                } elseif (in_array($row->config, [
                    'email_cc_default', 'email_bcc_default',
                    'email_template_os_criada', 'email_template_os_editada',
                    'email_template_venda', 'email_template_cobranca',
                    'email_template_obra_nova', 'email_template_obra_concluida',
                    'email_blacklist'
                ])) {
                    $configs[$row->config] = $row->valor;
                } else {
                    $configs[$row->config] = $this->_parseBool($row->valor);
                }
            }
        }

        // Se SMTP nao configurado no banco, carrega do .env
        if (empty($configs['email_smtp_host'])) {
            $configs['email_smtp_host'] = $_ENV['EMAIL_SMTP_HOST'] ?? '';
        }
        if (empty($configs['email_smtp_port'])) {
            $configs['email_smtp_port'] = $_ENV['EMAIL_SMTP_PORT'] ?? '587';
        }
        if (empty($configs['email_smtp_user'])) {
            $configs['email_smtp_user'] = $_ENV['EMAIL_SMTP_USER'] ?? '';
        }
        if (empty($configs['email_smtp_pass'])) {
            $configs['email_smtp_pass'] = $_ENV['EMAIL_SMTP_PASS'] ?? '';
        }
        if (empty($configs['email_smtp_crypto'])) {
            $configs['email_smtp_crypto'] = $_ENV['EMAIL_SMTP_CRYPTO'] ?? 'tls';
        }
        if (empty($configs['email_from'])) {
            $configs['email_from'] = $_ENV['EMAIL_FROM'] ?? ($this->data['config']['email'] ?? '');
        }
        if (empty($configs['email_from_name'])) {
            $configs['email_from_name'] = $_ENV['EMAIL_FROM_NAME'] ?? ($this->data['config']['nome'] ?? 'Sistema');
        }

        $this->data['configs'] = $configs;
        $this->data['templates'] = $this->templates->listTemplates();
        $this->data['menuFerramentasV5'] = true;
        $this->data['menuEmailQueue'] = true;
        $this->data['view'] = 'emails/configuracoes';

        return $this->layout();
    }

    /**
     * Salvar configuracoes de notificacoes
     */
    public function salvar_configuracoes()
    {
        if (!$this->input->post()) {
            $this->session->set_flashdata('error', 'Dados incompletos.');
            redirect('email/configuracoes');
            return;
        }

        $configsBool = [
            'email_notif_os_criada',
            'email_notif_os_editada',
            'email_notif_venda',
            'email_notif_cobranca',
            'email_notif_obra_nova',
            'email_notif_obra_concluida',
            'email_notif_atividade_atrasada',
            'email_notif_impedimento',
            'email_automatico_v5',
        ];

        foreach ($configsBool as $key) {
            $valor = $this->input->post($key) ? '1' : '0';
            $this->db->replace('configuracoes', ['config' => $key, 'valor' => $valor]);
        }

        // Configs numericas/texto
        $configsText = [
            'email_queue_interval' => $this->input->post('email_queue_interval') ?? '60',
            'email_batch_size' => $this->input->post('email_batch_size') ?? '3',
            'email_smtp_host' => $this->input->post('email_smtp_host') ?? '',
            'email_smtp_port' => $this->input->post('email_smtp_port') ?? '587',
            'email_smtp_user' => $this->input->post('email_smtp_user') ?? '',
            'email_smtp_pass' => $this->input->post('email_smtp_pass') ?? '',
            'email_smtp_crypto' => $this->input->post('email_smtp_crypto') ?? 'tls',
            'email_from' => $this->input->post('email_from') ?? '',
            'email_from_name' => $this->input->post('email_from_name') ?? 'Sistema',
            'email_cc_default' => $this->input->post('email_cc_default') ?? '',
            'email_bcc_default' => $this->input->post('email_bcc_default') ?? '',
            'email_template_os_criada' => $this->input->post('email_template_os_criada') ?? 'os_nova',
            'email_template_os_editada' => $this->input->post('email_template_os_editada') ?? 'os_atualizada',
            'email_template_venda' => $this->input->post('email_template_venda') ?? 'venda_realizada',
            'email_template_cobranca' => $this->input->post('email_template_cobranca') ?? 'cobranca',
            'email_template_obra_nova' => $this->input->post('email_template_obra_nova') ?? 'obra_nova',
            'email_template_obra_concluida' => $this->input->post('email_template_obra_concluida') ?? 'obra_concluida',
            'email_blacklist' => $this->input->post('email_blacklist') ?? '',
        ];

        foreach ($configsText as $key => $valor) {
            $this->db->replace('configuracoes', ['config' => $key, 'valor' => $valor]);
        }

        $this->session->set_flashdata('success', 'Configuracoes salvas com sucesso!');
        redirect('email/configuracoes');
    }

    /**
     * Enviar email de teste
     */
    public function testar_envio()
    {
        $emailTeste = $this->input->post('email_teste');
        if (empty($emailTeste) || !filter_var($emailTeste, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Email de teste invalido.');
            redirect('email/configuracoes');
            return;
        }

        try {
            $rendered = $this->templates->render('boas_vindas', [
                'cliente_nome' => 'Usuario de Teste',
                'cliente_email' => $emailTeste,
                'data_atual' => date('d/m/Y'),
                'sistema_url' => base_url(),
            ]);

            $queue = new EmailQueue();
            $id = $queue->enqueue([
                'to' => $emailTeste,
                'to_name' => 'Usuario de Teste',
                'subject' => 'Email de Teste - Sistema MAPOS',
                'body_html' => $rendered['html'],
                'body_text' => $rendered['text'] ?? strip_tags($rendered['html']),
                'template' => 'boas_vindas',
                'priority' => 1,
            ]);

            if ($id > 0) {
                $this->session->set_flashdata('success', 'Email de teste enfileirado com sucesso! ID: ' . $id);
            } else {
                $this->session->set_flashdata('error', 'Email bloqueado pela blacklist ou falha ao enfileirar.');
            }
        } catch (\Exception $e) {
            $this->session->set_flashdata('error', 'Erro ao enviar email de teste: ' . $e->getMessage());
        }

        redirect('email/configuracoes');
    }

    /**
     * Log de envios de email
     */
    public function logs()
    {
        $status = $this->input->get('status') ?? '';
        $page = (int) ($this->input->get('page') ?? 1);
        if ($page < 1) {
            $page = 1;
        }
        $limit = 25;
        $offset = ($page - 1) * $limit;

        $this->data['logs'] = $this->queue->getLogs($limit, $offset, $status);
        $total = $this->queue->countLogs($status);
        $this->data['total'] = $total;
        $this->data['page'] = $page;
        $this->data['total_pages'] = (int) ceil($total / $limit);
        $this->data['status_filter'] = $status;
        $this->data['menuFerramentasV5'] = true;
        $this->data['menuEmailQueue'] = true;
        $this->data['view'] = 'emails/logs';

        return $this->layout();
    }

    /**
     * Reenviar email falho
     */
    public function reenviar($id = 0)
    {
        if (!$id) {
            $id = (int) ($this->uri->segment(3) ?? 0);
        }

        // Se for requisicao AJAX, retorna JSON
        if ($this->input->is_ajax_request()) {
            $email = $this->queue->find($id);
            if (!$email) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Email nao encontrado']));
                return;
            }

            if ($email->status !== 'failed') {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Somente emails com falha podem ser reenviados']));
                return;
            }

            $this->db->where('id', $id);
            $this->db->update('email_queue', [
                'status' => 'pending',
                'error_message' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Email reenviado para a fila',
                    'email_id' => $id,
                ]));
            return;
        }

        // Requisicao normal
        $this->db->where('id', $id);
        $this->db->where('status', 'failed');
        $this->db->update('email_queue', [
            'status' => 'pending',
            'error_message' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->session->set_flashdata('success', 'Email reenviado para a fila de processamento.');
        redirect('email/logs');
    }

    private function _parseBool($val)
    {
        return in_array($val, ['1', 'true', 'on', 'yes'], true);
    }
}
