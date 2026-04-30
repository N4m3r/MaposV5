<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . 'libraries/Webhooks/WebhookManager.php';

use Libraries\Webhooks\WebhookManager;

class Cobrancas extends MY_Controller
{
    private WebhookManager $webhookManager;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('cobrancas_model');
        $this->data['menuCobrancas'] = 'financeiro';
        $this->webhookManager = new WebhookManager();
    }

    public function index()
    {
        $this->cobrancas();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aCobranca')) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['message' => 'Você não tem permissão para adicionar cobrança!']));
        }

        $this->load->library('form_validation');
        if ($this->form_validation->run('cobrancas') == false) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => validation_errors()]));
        } else {
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $formaPagamento = $this->input->post('forma_pagamento');
            $gatewayDePagamento = $this->input->post('gateway_de_pagamento');

            $this->load->model('Os_model');
            $this->load->model('vendas_model');
            $cobranca = $tipo === 'os'
                ? $this->Os_model->getCobrancas($this->input->post('id'))
                : $this->vendas_model->getCobrancas($this->input->post('id'));
            if ($cobranca) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['message' => 'Já existe cobrança!']));
            }

            $this->load->library("Gateways/$gatewayDePagamento", null, 'PaymentGateway');

            try {
                $cobranca = $this->PaymentGateway->gerarCobranca(
                    $id,
                    $tipo,
                    $formaPagamento
                );

                // Gatilho webhook: cobrança criada
                $this->webhookManager->trigger('cobranca.created', [
                    'id' => $cobranca['idCobranca'] ?? $id,
                    'tipo' => $tipo,
                    'referencia_id' => $id,
                    'forma_pagamento' => $formaPagamento,
                    'gateway' => $gatewayDePagamento,
                    'status' => $cobranca['status'] ?? 'PENDING',
                ]);

                // Enfileirar email de cobrança via sistema V5
                try {
                    require_once APPPATH . 'libraries/Email/EmailQueue.php';
                    require_once APPPATH . 'libraries/Email/TemplateEngine.php';

                    $this->load->model('clientes_model');
                    $this->load->model('mapos_model');
                    $emitente = $this->mapo_model->getEmitente();

                    $clienteId = null;
                    $clienteEmail = null;
                    $clienteNome = null;
                    $descricao = '';
                    $valor = '0,00';
                    $vencimento = '';

                    if ($tipo === 'os') {
                        $this->load->model('os_model');
                        $os = $this->os_model->getById($id);
                        if ($os) {
                            $clienteId = $os->clientes_id;
                            $clienteEmail = $os->email ?? null;
                            $clienteNome = $os->nomeCliente ?? null;
                            $descricao = 'OS #' . $id;
                            $valor = number_format(($os->totalProdutos ?? 0) + ($os->totalServicos ?? 0), 2, ',', '.');
                        }
                    } else {
                        $this->load->model('vendas_model');
                        $venda = $this->vendas_model->getById($id);
                        if ($venda) {
                            $clienteId = $venda->clientes_id;
                            $clienteEmail = $venda->email ?? null;
                            $clienteNome = $venda->nomeCliente ?? null;
                            $descricao = 'Venda #' . $id;
                            $valor = number_format($venda->valorTotal ?? 0, 2, ',', '.');
                        }
                    }

                    if ($clienteId && empty($clienteEmail)) {
                        $cliente = $this->clientes_model->getById($clienteId);
                        if ($cliente) {
                            $clienteEmail = $cliente->email;
                            $clienteNome = $cliente->nomeCliente;
                        }
                    }

                    if (!empty($clienteEmail)) {
                        $queue = new \Libraries\Email\EmailQueue();
                        $templates = new \Libraries\Email\TemplateEngine();

                        // Template configurado
                        $this->db->where('config', 'email_template_cobranca');
                        $templateRow = $this->db->get('configuracoes')->row();
                        $template = $templateRow && !empty($templateRow->valor) ? $templateRow->valor : 'cobranca';

                        // CC/BCC padrao
                        $cc = [];
                        $bcc = [];
                        $this->db->where('config', 'email_cc_default');
                        $rowCc = $this->db->get('configuracoes')->row();
                        if ($rowCc && !empty($rowCc->valor)) {
                            $cc = array_map('trim', explode(',', $rowCc->valor));
                        }
                        $this->db->where('config', 'email_bcc_default');
                        $rowBcc = $this->db->get('configuracoes')->row();
                        if ($rowBcc && !empty($rowBcc->valor)) {
                            $bcc = array_map('trim', explode(',', $rowBcc->valor));
                        }

                        $templateData = [
                            'cliente_nome' => $clienteNome ?? '',
                            'cliente_email' => $clienteEmail ?? '',
                            'cobranca_descricao' => $descricao,
                            'cobranca_valor' => $valor,
                            'cobranca_data_vencimento' => $vencimento,
                            'cobranca_dias_atraso' => '0',
                            'cobranca_link_pagamento' => base_url('cobrancas/visualizar/' . ($cobranca['idCobranca'] ?? $id)),
                            'empresa_nome' => $emitente->nome ?? '',
                        ];

                        $rendered = $templates->render($template, $templateData);

                        $enqueueData = [
                            'to' => $clienteEmail,
                            'to_name' => $clienteNome ?? '',
                            'subject' => 'Cobrança Gerada - ' . $descricao,
                            'body_html' => $rendered['html'],
                            'body_text' => $rendered['text'] ?? strip_tags($rendered['html']),
                            'template' => $template,
                            'template_data' => $templateData,
                            'priority' => 2,
                        ];
                        if (!empty($cc)) {
                            $enqueueData['cc'] = $cc;
                        }
                        if (!empty($bcc)) {
                            $enqueueData['bcc'] = $bcc;
                        }

                        $queue->enqueue($enqueueData);

                        // Agenda lembretes de vencimento
                        try {
                            require_once APPPATH . 'libraries/Scheduler/AutoEvents.php';
                            $autoEvents = new \Libraries\Scheduler\AutoEvents();
                            if (!empty($vencimento)) {
                                $autoEvents->scheduleCobrancaVencendo($cobranca['idCobranca'] ?? $id, $vencimento, $clienteEmail);
                                $autoEvents->scheduleCobrancaVencida($cobranca['idCobranca'] ?? $id, $vencimento, $clienteEmail);
                            }
                        } catch (\Exception $e) {
                            log_message('error', '[AutoEvents] Erro ao agendar lembretes cobranca: ' . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', '[Cobrancas] Erro ao enfileirar email V5: ' . $e->getMessage());
                }

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode($cobranca));
            } catch (\Exception $e) {
                $expMsg = $e->getMessage();
                if ($expMsg == 'unauthorized: Must provide your access_token to proceed' || $expMsg == 'Unauthorized') {
                    $expMsg = 'Por favor configurar os dados da API em Config/payment_gatways.php';
                }

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode(['message' => $expMsg]));
            }
        }
    }

    public function cobrancas()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar cobrancas.');
            redirect(base_url());
        }

        $this->load->library('pagination');
        $this->load->config('payment_gateways');

        $this->data['configuration']['base_url'] = site_url('cobrancas/cobrancas/');
        $this->data['configuration']['total_rows'] = $this->cobrancas_model->count('cobrancas');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->cobrancas_model->get('cobrancas', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'cobrancas/cobrancas';

        return $this->layout();
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir cobranças');
            redirect(site_url('cobrancas/cobrancas/'));
        }
        try {
            $this->cobrancas_model->cancelarPagamento($this->input->post('excluir_id'));

            if ($this->cobrancas_model->delete('cobrancas', 'idCobranca', $this->input->post('excluir_id')) == true) {
                log_info('Removeu uma cobrança. ID' . $this->input->post('excluir_id'));
                $this->session->set_flashdata('success', 'Cobrança excluida com sucesso!');
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect(site_url('cobrancas/cobrancas/'));
    }

    public function atualizar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para atualizar cobrança.');
            redirect(base_url());
        }
        try {
            $this->load->model('cobrancas_model');
            $this->cobrancas_model->atualizarStatus($this->uri->segment(3));
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect(site_url('cobrancas/cobrancas/'));
    }

    public function confirmarPagamento()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para confirmar pagamento da cobrança.');
            redirect(base_url());
        }
        try {
            $this->load->model('cobrancas_model');
            $idCobranca = $this->input->post('confirma_id');
            $this->cobrancas_model->confirmarPagamento($idCobranca);

            // Gatilho webhook: cobrança paga
            $this->webhookManager->trigger('cobranca.paid', [
                'id' => $idCobranca,
                'status' => 'RECEIVED',
            ]);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect(site_url('cobrancas/cobrancas/'));
    }

    public function cancelar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para cancelar cobrança.');
            redirect(base_url());
        }
        try {
            $this->load->model('cobrancas_model');
            $this->cobrancas_model->cancelarPagamento($this->input->post('cancela_id'));
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }
        redirect(site_url('cobrancas/cobrancas/'));
    }

    public function visualizar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('cobrancas');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar cobranças.');
            redirect(base_url());
        }
        $this->load->model('cobrancas_model');
        $this->load->config('payment_gateways');

        $this->data['result'] = $this->cobrancas_model->getById($this->uri->segment(3));
        if ($this->data['result'] == null) {
            $this->session->set_flashdata('error', 'Cobrança não encontrada.');
            redirect(site_url('cobrancas/'));
        }

        $this->data['view'] = 'cobrancas/visualizarCobranca';

        return $this->layout();
    }

    public function enviarEmail()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('cobrancas');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar cobranças.');
            redirect(base_url());
        }

        $this->load->model('cobrancas_model');
        $this->cobrancas_model->enviarEmail($this->uri->segment(3));
        $this->session->set_flashdata('success', 'Email adicionado na fila.');

        redirect(site_url('cobrancas/cobrancas/'));
    }
}
