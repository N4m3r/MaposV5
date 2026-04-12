<?php
/**
 * OS Controller - API v2
 * Endpoints para gerenciamento de Ordens de Serviço
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';
require_once APPPATH . 'Repositories/OsRepository.php';

use Repositories\OsRepository;
use Libraries\Scheduler\AutoEvents;

class OsController extends BaseController
{
    private OsRepository $repository;
    private AutoEvents $autoEvents;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new OsRepository();
        $this->autoEvents = new AutoEvents();
    }

    /**
     * GET /api/v2/os
     * Lista todas as OS
     */
    public function index(): void
    {
        $pagination = $this->getPaginationParams();
        $filters = $this->applyFilters(['status', 'idClientes', 'dataInicial', 'dataFinal']);

        $cacheKey = "os_{$pagination['page']}_" . md5(json_encode($filters));

        $result = $this->cache->remember($cacheKey, function() use ($pagination, $filters) {
            $data = $this->repository->findAll($filters, $pagination['per_page'], $pagination['offset']);
            $total = $this->repository->count($filters);

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
     * GET /api/v2/os/{id}
     * Retorna uma OS específica
     */
    public function show(int $id = 0): void
    {
        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $os = $this->repository->find($id);

        if (!$os) {
            $this->notFound('Ordem de Serviço');
            return;
        }

        // Carrega produtos e serviços
        $ci = \u0026get_instance();
        $ci->load->model('os_model');
        $os->produtos = $ci->os_model->getProdutos($id);
        $os->servicos = $ci->os_model->getServicos($id);

        $this->success($os);
    }

    /**
     * POST /api/v2/os
     * Cria uma nova OS
     */
    public function store(): void
    {
        $this->checkPermission('os_criar');

        $data = $this->getJsonInput();

        // Validação
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('idClientes', 'Cliente', 'required|integer');
        $this->form_validation->set_rules('descricaoProduto', 'Descrição', 'required');

        if (!$this->form_validation->run()) {
            $this->validationError([validation_errors()]);
            return;
        }

        $ci = \u0026get_instance();
        $ci->load->model('os_model');

        $id = $ci->os_model->insert($data);

        if ($id) {
            // Agenda evento de lembrete
            if (!empty($data['dataFinal']) && !empty($data['emailCliente'])) {
                $this->autoEvents->scheduleOsVencendo(
                    $id,
                    $data['dataFinal'],
                    $data['emailCliente']
                );
            }

            $this->clearCache('os_*');
            $this->created(['id' => $id, 'message' => 'OS criada com sucesso']);
        } else {
            $this->error('Erro ao criar OS', 500);
        }
    }

    /**
     * PUT /api/v2/os/{id}
     * Atualiza uma OS
     */
    public function update(int $id = 0): void
    {
        $this->checkPermission('os_editar');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $os = $this->repository->find($id);
        if (!$os) {
            $this->notFound('Ordem de Serviço');
            return;
        }

        $data = $this->getJsonInput();
        unset($data['idOs']);

        $ci = \u0026get_instance();
        $ci->load->model('os_model');

        $success = $ci->os_model->update($id, $data);

        if ($success) {
            $this->clearCache('os_*');
            $this->updated(['message' => 'OS atualizada com sucesso']);
        } else {
            $this->error('Erro ao atualizar OS', 500);
        }
    }

    /**
     * PATCH /api/v2/os/{id}/status
     * Atualiza status da OS
     */
    public function updateStatus(int $id = 0): void
    {
        $this->checkPermission('os_editar');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $data = $this->getJsonInput();
        $status = $data['status'] ?? null;

        if (!$status) {
            $this->validationError(['status' => 'Status é obrigatório']);
            return;
        }

        $ci = \u0026get_instance();
        $ci->load->model('os_model');
        $success = $ci->os_model->update($id, ['status' => $status]);

        if ($success) {
            $this->clearCache('os_*');
            $this->updated(['message' => 'Status atualizado com sucesso']);
        } else {
            $this->error('Erro ao atualizar status', 500);
        }
    }

    /**
     * DELETE /api/v2/os/{id}
     * Remove uma OS
     */
    public function delete(int $id = 0): void
    {
        $this->checkPermission('os_excluir');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $os = $this->repository->find($id);
        if (!$os) {
            $this->notFound('Ordem de Serviço');
            return;
        }

        $ci = \u0026get_instance();
        $ci->load->model('os_model');
        $success = $ci->os_model->delete($id);

        if ($success) {
            $this->clearCache('os_*');
            $this->deleted('OS removida com sucesso');
        } else {
            $this->error('Erro ao remover OS', 500);
        }
    }

    /**
     * GET /api/v2/os/vencendo
     * Retorna OS prestes a vencer
     */
    public function vencendo(): void
    {
        $dias = (int) $this->input->get('dias') ?: 2;
        $os = $this->repository->findOsVencendo($dias);

        $this->success([
            'data' => $os,
            'total' => count($os),
            'dias_para_vencer' => $dias
        ]);
    }

    /**
     * GET /api/v2/os/atrasadas
     * Retorna OS atrasadas
     */
    public function atrasadas(): void
    {
        $os = $this->repository->findOsAtrasadas();

        $this->success([
            'data' => $os,
            'total' => count($os)
        ]);
    }
}
