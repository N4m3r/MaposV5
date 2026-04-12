<?php
/**
 * Webhooks Controller - API v2
 * Endpoints para gerenciamento de webhooks
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';
require_once APPPATH . 'libraries/Webhooks/WebhookManager.php';

use Libraries\Webhooks\WebhookManager;

class WebhooksController extends BaseController
{
    private WebhookManager $webhookManager;

    public function __construct()
    {
        parent::__construct();
        $this->webhookManager = new WebhookManager();
    }

    /**
     * GET /api/v2/webhooks
     * Lista todos os webhooks
     */
    public function index(): void
    {
        $this->checkPermission('configuracoes');

        $webhooks = $this->db->get('webhooks')->result();

        // Adiciona estatísticas
        foreach ($webhooks as &$webhook) {
            $webhook->events = json_decode($webhook->events, true);
            $webhook->stats = $this->webhookManager->getStats($webhook->id);
        }

        $this->success($webhooks);
    }

    /**
     * POST /api/v2/webhooks
     * Cria um novo webhook
     */
    public function store(): void
    {
        $this->checkPermission('configuracoes');

        $data = $this->getJsonInput();

        // Validação
        if (empty($data['url']) || empty($data['events'])) {
            $this->validationError(['url' => 'URL é obrigatório', 'events' => 'Events é obrigatório']);
            return;
        }

        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            $this->validationError(['url' => 'URL inválida']);
            return;
        }

        $id = $this->webhookManager->register([
            'url' => $data['url'],
            'events' => is_array($data['events']) ? $data['events'] : [$data['events']],
            'secret' => $data['secret'] ?? null,
            'description' => $data['description'] ?? '',
            'active' => $data['active'] ?? 1
        ]);

        $this->created(['id' => $id, 'message' => 'Webhook criado com sucesso']);
    }

    /**
     * DELETE /api/v2/webhooks/{id}
     * Remove um webhook
     */
    public function delete(int $id = 0): void
    {
        $this->checkPermission('configuracoes');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $this->db->where('id', $id);
        $this->db->delete('webhooks');

        $this->deleted('Webhook removido com sucesso');
    }

    /**
     * POST /api/v2/webhooks/test
     * Testa um webhook
     */
    public function test(): void
    {
        $this->checkPermission('configuracoes');

        $data = $this->getJsonInput();

        if (empty($data['url'])) {
            $this->validationError(['url' => 'URL é obrigatório']);
            return;
        }

        // Envia evento de teste
        $result = $this->webhookManager->trigger('test', [
            'message' => 'Teste de webhook',
            'timestamp' => time()
        ]);

        $this->success([
            'test_results' => $result,
            'message' => 'Teste executado'
        ]);
    }

    /**
     * GET /api/v2/webhooks/events
     * Lista eventos disponíveis
     */
    public function events(): void
    {
        $events = [
            ['name' => 'os.created', 'description' => 'Ordem de serviço criada'],
            ['name' => 'os.updated', 'description' => 'Ordem de serviço atualizada'],
            ['name' => 'os.status_changed', 'description' => 'Status da OS alterado'],
            ['name' => 'os.deleted', 'description' => 'Ordem de serviço removida'],
            ['name' => 'venda.created', 'description' => 'Venda criada'],
            ['name' => 'venda.completed', 'description' => 'Venda finalizada'],
            ['name' => 'cliente.created', 'description' => 'Cliente cadastrado'],
            ['name' => 'cliente.updated', 'description' => 'Cliente atualizado'],
            ['name' => 'cobranca.created', 'description' => 'Cobrança criada'],
            ['name' => 'cobranca.paid', 'description' => 'Cobrança paga'],
            ['name' => '*', 'description' => 'Todos os eventos']
        ];

        $this->success($events);
    }
}
