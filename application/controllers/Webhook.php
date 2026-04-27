<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . 'libraries/Webhooks/WebhookManager.php';

use Libraries\Webhooks\WebhookManager;

/**
 * Webhook Controller para receber notificações do Banco Cora
 *
 * Endpoint para callbacks de pagamento (Boleto + PIX)
 *
 * @link https://developers.cora.com.br/reference/webhooks
 */
class Webhook extends CI_Controller
{
    private WebhookManager $webhookManager;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('cobrancas_model');
        $this->load->model('clientes_model');
        $this->load->model('financeiro_model');
        $this->load->config('payment_gateways');
        $this->webhookManager = new WebhookManager();
    }

    /**
     * Recebe notificações de pagamento do Banco Cora
     *
     * URL: /index.php/webhook/cora
     *
     * @return void
     */
    public function cora()
    {
        // Obtém o payload JSON enviado pelo Cora
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (empty($data)) {
            $this->responseError('Payload inválido', 400);
            return;
        }

        // Log da requisição para debugging
        log_info('Webhook Cora recebido: ' . $input);

        // Verifica se é uma notificação válida
        if (!isset($data['event'])) {
            $this->responseError('Evento não informado', 400);
            return;
        }

        // Processa o evento
        try {
            switch ($data['event']) {
                case 'invoice.paid':
                    $this->processInvoicePaid($data['data'] ?? []);
                    break;

                case 'invoice.cancelled':
                    $this->processInvoiceCancelled($data['data'] ?? []);
                    break;

                case 'invoice.overdue':
                    $this->processInvoiceOverdue($data['data'] ?? []);
                    break;

                case 'invoice.opened':
                    $this->processInvoiceOpened($data['data'] ?? []);
                    break;

                case 'invoice.in_payment':
                    $this->processInvoiceInPayment($data['data'] ?? []);
                    break;

                default:
                    log_info('Evento webhook Cora não processado: ' . $data['event']);
                    break;
            }
        } catch (Exception $e) {
            log_info('Erro ao processar webhook Cora: ' . $e->getMessage());
            $this->responseError('Erro interno', 500);
            return;
        }

        // Retorna sucesso
        $this->responseSuccess(['message' => 'Webhook processado']);
    }

    /**
     * Processa pagamento confirmado
     *
     * @param array $data Dados do invoice
     * @return void
     */
    private function processInvoicePaid($data)
    {
        if (empty($data['id'])) {
            log_info('Webhook Cora: ID do invoice não encontrado');
            return;
        }

        $chargeId = $data['id'];
        $paidAt = $data['paid_at'] ?? date('Y-m-d H:i:s');
        $amount = $data['total_amount'] ?? null;

        // Busca cobrança pelo charge_id
        $this->db->where('charge_id', $chargeId);
        $this->db->where('payment_gateway', 'Cora');
        $query = $this->db->get('cobrancas');
        $cobranca = $query->row();

        if (!$cobranca) {
            log_info('Webhook Cora: Cobrança não encontrada para charge_id: ' . $chargeId);
            return;
        }

        // Verifica se já não está paga
        if ($cobranca->status === 'RECEIVED') {
            log_info('Webhook Cora: Cobrança #' . $cobranca->idCobranca . ' já está paga');
            return;
        }

        // Atualiza status para RECEIVED
        $updateData = [
            'status' => 'RECEIVED',
            'updated_at' => date('Y-m-d H:i:s'),
            'paid_at' => date('Y-m-d H:i:s', strtotime($paidAt)),
        ];

        // Se tiver valor na notificação, salva
        if ($amount) {
            $updateData['total_paid'] = $amount / 100; // Converte centavos para reais
        }

        $this->cobrancas_model->edit(
            'cobrancas',
            $updateData,
            'idCobranca',
            $cobranca->idCobranca
        );

        log_info('Webhook Cora: Pagamento confirmado para cobrança #' . $cobranca->idCobranca);

        // Gatilho webhook: cobrança paga
        $this->webhookManager->trigger('cobranca.paid', [
            'id' => $cobranca->idCobranca,
            'charge_id' => $chargeId,
            'status' => 'RECEIVED',
            'gateway' => 'Cora',
        ]);

        // Dar baixa no lançamento financeiro
        $this->darBaixaLancamento($cobranca);
    }

    /**
     * Processa cobrança aberta/gerada
     *
     * @param array $data Dados do invoice
     * @return void
     */
    private function processInvoiceOpened($data)
    {
        if (empty($data['id'])) {
            return;
        }

        $chargeId = $data['id'];

        $this->db->where('charge_id', $chargeId);
        $this->db->where('payment_gateway', 'Cora');
        $query = $this->db->get('cobrancas');
        $cobranca = $query->row();

        if (!$cobranca) {
            return;
        }

        $this->cobrancas_model->edit(
            'cobrancas',
            [
                'status' => 'PENDING',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            'idCobranca',
            $cobranca->idCobranca
        );

        log_info('Webhook Cora: Cobrança #' . $cobranca->idCobranca . ' está aberta');
    }

    /**
     * Processa cobrança em pagamento (processamento)
     *
     * @param array $data Dados do invoice
     * @return void
     */
    private function processInvoiceInPayment($data)
    {
        if (empty($data['id'])) {
            return;
        }

        $chargeId = $data['id'];

        $this->db->where('charge_id', $chargeId);
        $this->db->where('payment_gateway', 'Cora');
        $query = $this->db->get('cobrancas');
        $cobranca = $query->row();

        if (!$cobranca) {
            return;
        }

        $this->cobrancas_model->edit(
            'cobrancas',
            [
                'status' => 'CONFIRMED',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            'idCobranca',
            $cobranca->idCobranca
        );

        log_info('Webhook Cora: Cobrança #' . $cobranca->idCobranca . ' em processamento');
    }

    /**
     * Processa cancelamento de invoice
     *
     * @param array $data Dados do invoice
     * @return void
     */
    private function processInvoiceCancelled($data)
    {
        if (empty($data['id'])) {
            return;
        }

        $chargeId = $data['id'];

        $this->db->where('charge_id', $chargeId);
        $this->db->where('payment_gateway', 'Cora');
        $query = $this->db->get('cobrancas');
        $cobranca = $query->row();

        if (!$cobranca) {
            return;
        }

        $this->cobrancas_model->edit(
            'cobrancas',
            [
                'status' => 'CANCELLED',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            'idCobranca',
            $cobranca->idCobranca
        );

        log_info('Webhook Cora: Cobrança cancelada #' . $cobranca->idCobranca);
    }

    /**
     * Processa vencimento de invoice
     *
     * @param array $data Dados do invoice
     * @return void
     */
    private function processInvoiceOverdue($data)
    {
        if (empty($data['id'])) {
            return;
        }

        $chargeId = $data['id'];

        $this->db->where('charge_id', $chargeId);
        $this->db->where('payment_gateway', 'Cora');
        $query = $this->db->get('cobrancas');
        $cobranca = $query->row();

        if (!$cobranca) {
            return;
        }

        $this->cobrancas_model->edit(
            'cobrancas',
            [
                'status' => 'OVERDUE',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            'idCobranca',
            $cobranca->idCobranca
        );

        log_info('Webhook Cora: Cobrança vencida #' . $cobranca->idCobranca);

        // Gatilho webhook: cobrança vencida
        $this->webhookManager->trigger('cobranca.overdue', [
            'id' => $cobranca->idCobranca,
            'charge_id' => $chargeId,
            'status' => 'OVERDUE',
            'gateway' => 'Cora',
        ]);
    }

    /**
     * Dá baixa no lançamento financeiro vinculado
     *
     * @param object $cobranca Dados da cobrança
     * @return void
     */
    private function darBaixaLancamento($cobranca)
    {
        try {
            // Busca lançamento vinculado à OS ou Venda
            $lancamento = null;

            if ($cobranca->os_id) {
                $this->db->where('os_id', $cobranca->os_id);
                $this->db->where('tipo', 'receita');
                $this->db->where('baixado', 0);
                $query = $this->db->get('lancamentos');
                $lancamento = $query->row();
            } elseif ($cobranca->vendas_id) {
                $this->db->where('vendas_id', $cobranca->vendas_id);
                $this->db->where('tipo', 'receita');
                $this->db->where('baixado', 0);
                $query = $this->db->get('lancamentos');
                $lancamento = $query->row();
            }

            if ($lancamento) {
                // Atualiza lançamento como baixado
                $data = [
                    'baixado' => 1,
                    'data_pagamento' => date('Y-m-d'),
                ];

                $this->financeiro_model->edit(
                    'lancamentos',
                    $data,
                    'idLancamentos',
                    $lancamento->idLancamentos
                );

                log_info('Baixa automática no lançamento #' . $lancamento->idLancamentos . ' via webhook Cora');
            }
        } catch (Exception $e) {
            log_info('Erro ao dar baixa no lançamento: ' . $e->getMessage());
        }
    }

    /**
     * Retorna resposta de sucesso
     *
     * @param array $data Dados da resposta
     * @return void
     */
    private function responseSuccess($data)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($data));
    }

    /**
     * Retorna resposta de erro
     *
     * @param string $message Mensagem de erro
     * @param int $code Código HTTP
     * @return void
     */
    private function responseError($message, $code = 400)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($code)
            ->set_output(json_encode(['error' => $message]));
    }
}
