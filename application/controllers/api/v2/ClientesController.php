<?php
/**
 * Clientes Controller - API v2
 * Endpoints para gerenciamento de clientes
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';
require_once APPPATH . 'Repositories/ClienteRepository.php';

use Repositories\ClienteRepository;

class ClientesController extends BaseController
{
    private ClienteRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new ClienteRepository();
    }

    /**
     * GET /api/v2/clientes
     * Lista todos os clientes
     */
    public function index(): void
    {
        $pagination = $this->getPaginationParams();
        $filters = $this->applyFilters(['nomeCliente', 'documento', 'email', 'status']);
        $search = $this->input->get('search');

        $cacheKey = "clientes_{$pagination['page']}_{$pagination['per_page']}_" . md5(json_encode($filters) . $search);

        $result = $this->cache->remember($cacheKey, function() use ($pagination, $filters, $search) {
            if ($search) {
                $data = $this->repository->search($search);
                $total = count($data);
            } else {
                $data = $this->repository->findAll($filters, $pagination['per_page'], $pagination['offset']);
                $total = $this->repository->count($filters);
            }

            return [
                'data' => $data,
                'total' => $total
            ];
        }, 300);

        $result['page'] = $pagination['page'];
        $result['per_page'] = $pagination['per_page'];
        $result['total_pages'] = (int) ceil($result['total'] / $pagination['per_page']);

        $this->success($result);
    }

    /**
     * GET /api/v2/clientes/{id}
     * Retorna um cliente específico
     */
    public function show(int $id = 0): void
    {
        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $cacheKey = "cliente_{$id}";

        $cliente = $this->cache->remember($cacheKey, function() use ($id) {
            return $this->repository->find($id);
        }, 600);

        if (!$cliente) {
            $this->notFound('Cliente');
            return;
        }

        $this->success($cliente);
    }

    /**
     * POST /api/v2/clientes
     * Cria um novo cliente
     */
    public function store(): void
    {
        $this->checkPermission('clientes_criar');

        $data = $this->getJsonInput();

        // Validação
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('nomeCliente', 'Nome', 'required|min_length[3]');
        $this->form_validation->set_rules('documento', 'Documento', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('telefone', 'Telefone', 'required');

        if (!$this->form_validation->run()) {
            $this->validationError([validation_errors()]);
            return;
        }

        // Remove ID se enviado
        unset($data['idClientes']);

        // Limpa documento
        $data['documento'] = preg_replace('/[^0-9]/', '', $data['documento']);

        $id = $this->repository->create($data);

        if ($id) {
            $this->clearCache('clientes_*');
            $this->created(['id' => $id, 'message' => 'Cliente criado com sucesso']);
        } else {
            $this->error('Erro ao criar cliente', 500);
        }
    }

    /**
     * PUT /api/v2/clientes/{id}
     * Atualiza um cliente
     */
    public function update(int $id = 0): void
    {
        $this->checkPermission('clientes_editar');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $cliente = $this->repository->find($id);
        if (!$cliente) {
            $this->notFound('Cliente');
            return;
        }

        $data = $this->getJsonInput();

        // Não permite alterar ID
        unset($data['idClientes']);

        // Limpa documento se enviado
        if (isset($data['documento'])) {
            $data['documento'] = preg_replace('/[^0-9]/', '', $data['documento']);
        }

        $success = $this->repository->update($id, $data);

        if ($success) {
            $this->clearCache('clientes_*');
            $this->updated(['message' => 'Cliente atualizado com sucesso']);
        } else {
            $this->error('Erro ao atualizar cliente', 500);
        }
    }

    /**
     * DELETE /api/v2/clientes/{id}
     * Remove um cliente
     */
    public function delete(int $id = 0): void
    {
        $this->checkPermission('clientes_excluir');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $cliente = $this->repository->find($id);
        if (!$cliente) {
            $this->notFound('Cliente');
            return;
        }

        $success = $this->repository->delete($id);

        if ($success) {
            $this->clearCache('clientes_*');
            $this->deleted('Cliente removido com sucesso');
        } else {
            $this->error('Erro ao remover cliente', 500);
        }
    }

    /**
     * GET /api/v2/clientes/aniversariantes
     * Retorna clientes que fazem aniversário hoje
     */
    public function aniversariantes(): void
    {
        $clientes = $this->repository->findAniversariantes();

        $this->success([
            'data' => $clientes,
            'total' => count($clientes)
        ]);
    }

    /**
     * GET /api/v2/clientes/{id}/os
     * Retorna OS do cliente
     */
    public function ordens(int $id = 0): void
    {
        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $ci = \u0026get_instance();
        $ci->load->model('os_model');
        $ci->os_model->db->where('idClientes', $id);
        $ordens = $ci->os_model->getAll();

        $this->success([
            'data' => $ordens,
            'total' => count($ordens)
        ]);
    }
}
