<?php
/**
 * Vendas Controller - API v2
 * Endpoints para gerenciamento de Vendas
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';
require_once APPPATH . 'Repositories/VendaRepository.php';

use Repositories\VendaRepository;
use Libraries\Scheduler\AutoEvents;

class VendasController extends BaseController
{
    private VendaRepository $repository;
    private AutoEvents $autoEvents;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new VendaRepository();
        $this->autoEvents = new AutoEvents();
    }

    /**
     * GET /api/v2/vendas
     */
    public function index(): void
    {
        $pagination = $this->getPaginationParams();
        $filters = $this->applyFilters(['idClientes', 'status', 'dataVenda']);

        $cacheKey = "vendas_{$pagination['page']}_" . md5(json_encode($filters));

        $result = $this->cache->remember($cacheKey, function() use ($pagination, $filters) {
            $data = $this->repository->findAll($filters, $pagination['per_page'], $pagination['offset']);
            $total = $this->repository->count($filters);

            return ['data' => $data, 'total' => $total];
        }, 300);

        $result['page'] = $pagination['page'];
        $result['per_page'] = $pagination['per_page'];
        $result['total_pages'] = (int) ceil($result['total'] / $pagination['per_page']);

        $this->success($result);
    }

    /**
     * GET /api/v2/vendas/{id}
     */
    public function show(int $id = 0): void
    {
        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $venda = $this->repository->find($id);

        if (!$venda) {
            $this->notFound('Venda');
            return;
        }

        // Carrega produtos
        $ci = \u0026get_instance();
        $ci->load->model('vendas_model');
        $venda->produtos = $ci->vendas_model->getProdutos($id);

        $this->success($venda);
    }

    /**
     * POST /api/v2/vendas
     */
    public function store(): void
    {
        $this->checkPermission('vendas_criar');

        $data = $this->getJsonInput();

        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('idClientes', 'Cliente', 'required|integer');
        $this->form_validation->set_rules('produtos', 'Produtos', 'required');

        if (!$this->form_validation->run()) {
            $this->validationError([validation_errors()]);
            return;
        }

        $ci = \u0026get_instance();
        $ci->load->model('vendas_model');

        $id = $ci->vendas_model->insert($data);

        if ($id) {
            // Agenda follow-up
            $cliente = $this->db->where('idClientes', $data['idClientes'])->get('clientes')->row();
            if ($cliente && !empty($cliente->email)) {
                $this->autoEvents->scheduleFollowUpVenda(
                    $id,
                    date('Y-m-d'),
                    $cliente->email
                );
            }

            $this->clearCache('vendas_*');
            $this->created(['id' => $id, 'message' => 'Venda criada com sucesso']);
        } else {
            $this->error('Erro ao criar venda', 500);
        }
    }

    /**
     * PUT /api/v2/vendas/{id}
     */
    public function update(int $id = 0): void
    {
        $this->checkPermission('vendas_editar');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $venda = $this->repository->find($id);
        if (!$venda) {
            $this->notFound('Venda');
            return;
        }

        $data = $this->getJsonInput();
        unset($data['idVendas']);

        $ci = \u0026get_instance();
        $ci->load->model('vendas_model');
        $success = $ci->vendas_model->update($id, $data);

        if ($success) {
            $this->clearCache('vendas_*');
            $this->updated(['message' => 'Venda atualizada com sucesso']);
        } else {
            $this->error('Erro ao atualizar venda', 500);
        }
    }

    /**
     * DELETE /api/v2/vendas/{id}
     */
    public function delete(int $id = 0): void
    {
        $this->checkPermission('vendas_excluir');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $venda = $this->repository->find($id);
        if (!$venda) {
            $this->notFound('Venda');
            return;
        }

        $ci = \u0026get_instance();
        $ci->load->model('vendas_model');
        $success = $ci->vendas_model->delete($id);

        if ($success) {
            $this->clearCache('vendas_*');
            $this->deleted('Venda removida com sucesso');
        } else {
            $this->error('Erro ao remover venda', 500);
        }
    }

    /**
     * GET /api/v2/vendas/totais
     * Retorna totais por período
     */
    public function totais(): void
    {
        $inicio = $this->input->get('inicio') ?: date('Y-m-01');
        $fim = $this->input->get('fim') ?: date('Y-m-t');

        $total = $this->repository->getTotalVendas($inicio, $fim);
        $vendas = $this->repository->findByPeriod($inicio, $fim);

        $this->success([
            'periodo' => [
                'inicio' => $inicio,
                'fim' => $fim
            ],
            'total_vendas' => $total,
            'quantidade' => count($vendas)
        ]);
    }
}
