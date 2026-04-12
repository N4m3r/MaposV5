<?php
/**
 * Produtos Controller - API v2
 * Endpoints para gerenciamento de Produtos
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class ProdutosController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('produtos_model');
    }

    /**
     * GET /api/v2/produtos
     */
    public function index(): void
    {
        $pagination = $this->getPaginationParams();
        $filters = $this->applyFilters(['tipo', 'estoque_minimo', 'ativo']);
        $search = $this->input->get('search');

        $cacheKey = "produtos_{$pagination['page']}_" . md5(json_encode($filters) . $search);

        $result = $this->cache->remember($cacheKey, function() use ($pagination, $filters, $search) {
            if ($search) {
                $this->produtos_model->db->like('descricao', $search);
                $this->produtos_model->db->or_like('codDeBarra', $search);
            }

            foreach ($filters as $field => $value) {
                $this->produtos_model->db->where($field, $value);
            }

            $data = $this->produtos_model->getAll();

            foreach ($filters as $field => $value) {
                $this->produtos_model->db->where($field, $value);
            }
            $total = $this->produtos_model->db->count_all_results('produtos');

            return ['data' => $data, 'total' => $total];
        }, 300);

        $result['page'] = $pagination['page'];
        $result['per_page'] = $pagination['per_page'];
        $result['total_pages'] = (int) ceil($result['total'] / $pagination['per_page']);

        $this->success($result);
    }

    /**
     * GET /api/v2/produtos/{id}
     */
    public function show(int $id = 0): void
    {
        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $produto = $this->produtos_model->getById($id);

        if (!$produto) {
            $this->notFound('Produto');
            return;
        }

        $this->success($produto);
    }

    /**
     * POST /api/v2/produtos
     */
    public function store(): void
    {
        $this->checkPermission('produtos_criar');

        $data = $this->getJsonInput();

        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('descricao', 'Descrição', 'required');
        $this->form_validation->set_rules('precoVenda', 'Preço de Venda', 'required|numeric');

        if (!$this->form_validation->run()) {
            $this->validationError([validation_errors()]);
            return;
        }

        $id = $this->produtos_model->insert($data);

        if ($id) {
            $this->clearCache('produtos_*');
            $this->created(['id' => $id, 'message' => 'Produto criado com sucesso']);
        } else {
            $this->error('Erro ao criar produto', 500);
        }
    }

    /**
     * PUT /api/v2/produtos/{id}
     */
    public function update(int $id = 0): void
    {
        $this->checkPermission('produtos_editar');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $produto = $this->produtos_model->getById($id);
        if (!$produto) {
            $this->notFound('Produto');
            return;
        }

        $data = $this->getJsonInput();
        unset($data['idProdutos']);

        $success = $this->produtos_model->update($id, $data);

        if ($success) {
            $this->clearCache('produtos_*');
            $this->updated(['message' => 'Produto atualizado com sucesso']);
        } else {
            $this->error('Erro ao atualizar produto', 500);
        }
    }

    /**
     * DELETE /api/v2/produtos/{id}
     */
    public function delete(int $id = 0): void
    {
        $this->checkPermission('produtos_excluir');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $produto = $this->produtos_model->getById($id);
        if (!$produto) {
            $this->notFound('Produto');
            return;
        }

        $success = $this->produtos_model->delete($id);

        if ($success) {
            $this->clearCache('produtos_*');
            $this->deleted('Produto removido com sucesso');
        } else {
            $this->error('Erro ao remover produto', 500);
        }
    }

    /**
     * GET /api/v2/produtos/estoque/baixo
     * Retorna produtos com estoque baixo
     */
    public function estoqueBaixo(): void
    {
        $this->produtos_model->db->where('estoque < estoqueMinimo', null, false);
        $produtos = $this->produtos_model->getAll();

        $this->success([
            'data' => $produtos,
            'total' => count($produtos)
        ]);
    }
}
