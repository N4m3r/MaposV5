<?php
/**
 * Webhooks Controller
 * Gerenciamento de webhooks e integrações
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Webhooks extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logado')) {
            redirect('login');
        }

        $this->load->model('webhooks_model', '', true);
    }

    /**
     * Listar webhooks
     */
    public function index()
    {
        $this->data['webhooks'] = $this->webhooks_model->getAll();
        $this->data['logs'] = $this->webhooks_model->getRecentLogs(10);
        $this->data['menuFerramentasV5'] = true;
        $this->data['menuWebhooks'] = true;
        $this->data['view'] = 'webhooks/index';

        return $this->layout();
    }

    /**
     * Criar webhook
     */
    public function create()
    {
        if (!$this->input->post()) {
            $this->data['events'] = $this->getAvailableEvents();
            $this->data['menuFerramentasV5'] = true;
            $this->data['menuWebhooks'] = true;
            $this->data['view'] = 'webhooks/create';
            return $this->layout();
        }

        $data = [
            'name' => $this->input->post('name'),
            'url' => $this->input->post('url'),
            'events' => json_encode($this->input->post('events') ?? []),
            'secret' => $this->input->post('secret') ?: bin2hex(random_bytes(16)),
            'active' => $this->input->post('active') ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->webhooks_model->insert($data);
        $this->session->set_flashdata('success', 'Webhook criado com sucesso!');
        redirect('webhooks');
    }

    /**
     * Editar webhook
     */
    public function edit($id)
    {
        if (!$this->input->post()) {
            $webhook = $this->webhooks_model->getById($id);
            if (!$webhook) {
                show_404();
            }

            $this->data['webhook'] = $webhook;
            $this->data['events'] = $this->getAvailableEvents();
            $this->data['menuFerramentasV5'] = true;
            $this->data['menuWebhooks'] = true;
            $this->data['view'] = 'webhooks/edit';
            return $this->layout();
        }

        $data = [
            'name' => $this->input->post('name'),
            'url' => $this->input->post('url'),
            'events' => json_encode($this->input->post('events') ?? []),
            'active' => $this->input->post('active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->webhooks_model->update($id, $data);
        $this->session->set_flashdata('success', 'Webhook atualizado com sucesso!');
        redirect('webhooks');
    }

    /**
     * Excluir webhook
     */
    public function delete($id)
    {
        $this->webhooks_model->delete($id);
        $this->session->set_flashdata('success', 'Webhook excluído com sucesso!');
        redirect('webhooks');
    }

    /**
     * Testar webhook
     */
    public function test($id)
    {
        $webhook = $this->webhooks_model->getById($id);
        if (!$webhook) {
            $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['error' => 'Webhook não encontrado']));
            return;
        }

        // Envia evento de teste
        $payload = [
            'event' => 'test',
            'data' => ['message' => 'Teste de webhook', 'timestamp' => date('c')]
        ];

        $result = $this->sendWebhook($webhook, $payload);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * Ver logs de um webhook
     */
    public function logs($id)
    {
        $webhook = $this->webhooks_model->getById($id);
        $logs = $this->webhooks_model->getLogs($id, 50);

        $this->data['webhook'] = $webhook;
        $this->data['logs'] = $logs;
        $this->data['menuFerramentasV5'] = true;
        $this->data['menuWebhooks'] = true;
        $this->data['view'] = 'webhooks/logs';

        return $this->layout();
    }

    /**
     * Eventos disponíveis
     */
    private function getAvailableEvents()
    {
        return [
            'os.created' => 'OS - Criada',
            'os.updated' => 'OS - Atualizada',
            'os.status_changed' => 'OS - Status Alterado',
            'os.finished' => 'OS - Finalizada',
            'cliente.created' => 'Cliente - Criado',
            'cliente.updated' => 'Cliente - Atualizado',
            'venda.created' => 'Venda - Criada',
            'venda.paid' => 'Venda - Paga',
            'cobranca.created' => 'Cobrança - Criada',
            'cobranca.paid' => 'Cobrança - Paga',
            'cobranca.overdue' => 'Cobrança - Vencida',
            'produto.low_stock' => 'Produto - Estoque Baixo'
        ];
    }

    /**
     * Enviar webhook
     */
    private function sendWebhook($webhook, $payload)
    {
        $secret = $webhook->secret;
        $signature = hash_hmac('sha256', json_encode($payload), $secret);

        $ch = curl_init($webhook->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Webhook-Signature: ' . $signature,
            'X-Webhook-ID: ' . $webhook->id,
            'User-Agent: MAPOS-Webhook/1.0'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Log do envio
        $logData = [
            'webhook_id' => $webhook->id,
            'event' => $payload['event'],
            'payload' => json_encode($payload),
            'response' => $response,
            'http_code' => $httpCode,
            'success' => $httpCode >= 200 && $httpCode < 300 ? 1 : 0,
            'error' => $error ?: null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->webhooks_model->insertLog($logData);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error ?: null
        ];
    }

    /**
     * Documentação de Webhooks
     */
    public function docs()
    {
        $this->data['menuFerramentasV5'] = true;
        $this->data['menuWebhooksDocs'] = true;
        $this->load->view('api/webhooks_docs', $this->data);
    }
}
