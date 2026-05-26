<?php

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class ServicosController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('servicos_model');
    }

    public function index(): void
    {
        $pagination = $this->getPaginationParams();
        $search = $this->input->get('search');

        if ($search) {
            $this->db->like('nome', $search);
            $this->db->or_like('descricao', $search);
        }

        $data = $this->servicos_model->get('servicos', '*', '', $pagination['per_page'], $pagination['offset']);
        $total = $this->db->count_all_results('servicos');

        $this->paginated($data, [
            'total' => $total,
            'page' => $pagination['page'],
            'per_page' => $pagination['per_page'],
            'total_pages' => (int) ceil($total / $pagination['per_page']),
        ]);
    }

    public function show(int $id = 0): void
    {
        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $servico = $this->servicos_model->getById($id);

        if (!$servico) {
            $this->notFound('Servico');
            return;
        }

        $this->success($servico);
    }

    public function store(): void
    {
        $this->checkPermission('servicos_criar');

        $data = $this->getJsonInput();

        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('preco', 'Preco', 'required');

        if (!$this->form_validation->run()) {
            $this->validationError([validation_errors()]);
            return;
        }

        $preco = str_replace(',', '', $data['preco'] ?? '0');
        $insertData = [
            'nome' => $data['nome'],
            'descricao' => $data['descricao'] ?? '',
            'preco' => $preco,
        ];

        if ($this->servicos_model->add('servicos', $insertData)) {
            $this->created(['message' => 'Servico criado com sucesso']);
        } else {
            $this->error('Erro ao criar servico', 500);
        }
    }

    public function update(int $id = 0): void
    {
        $this->checkPermission('servicos_editar');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $servico = $this->servicos_model->getById($id);
        if (!$servico) {
            $this->notFound('Servico');
            return;
        }

        $data = $this->getJsonInput();

        if (empty($data['nome']) || empty($data['preco'])) {
            $this->validationError(['nome e preco sao obrigatorios']);
            return;
        }

        $preco = str_replace(',', '', $data['preco'] ?? '0');
        $updateData = [
            'nome' => $data['nome'],
            'descricao' => $data['descricao'] ?? '',
            'preco' => $preco,
        ];

        if ($this->servicos_model->edit('servicos', $updateData, 'idServicos', $id)) {
            $this->updated(['message' => 'Servico atualizado com sucesso']);
        } else {
            $this->error('Erro ao atualizar servico', 500);
        }
    }

    public function delete(int $id = 0): void
    {
        $this->checkPermission('servicos_excluir');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $servico = $this->servicos_model->getById($id);
        if (!$servico) {
            $this->notFound('Servico');
            return;
        }

        $this->servicos_model->delete('servicos_os', 'servicos_id', $id);

        if ($this->servicos_model->delete('servicos', 'idServicos', $id)) {
            $this->deleted('Servico removido com sucesso');
        } else {
            $this->error('Erro ao remover servico', 500);
        }
    }
}