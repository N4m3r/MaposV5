<?php

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class UsuariosController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('usuarios_model');
    }

    public function index(): void
    {
        $pagination = $this->getPaginationParams();
        $search = $this->input->get('search');

        if ($search) {
            $this->db->like('nome', $search);
            $this->db->or_like('email', $search);
            $data = $this->db->get('usuarios')->result();
            $total = count($data);
        } else {
            $data = $this->usuarios_model->get($pagination['per_page'], $pagination['offset']);
            $total = $this->db->count_all_results('usuarios');
        }

        // Remove senhas do resultado
        foreach ($data as $u) {
            unset($u->senha);
        }

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

        $usuario = $this->usuarios_model->getById($id);

        if (!$usuario) {
            $this->notFound('Usuario');
            return;
        }

        unset($usuario->senha);
        $this->success($usuario);
    }

    public function store(): void
    {
        $this->checkPermission('usuarios_criar');

        $data = $this->getJsonInput();

        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if (!$this->form_validation->run()) {
            $this->validationError([validation_errors()]);
            return;
        }

        $insertData = [
            'nome' => $data['nome'],
            'rg' => $data['rg'] ?? '',
            'cpf' => $data['cpf'] ?? '',
            'cep' => $data['cep'] ?? '',
            'rua' => $data['rua'] ?? '',
            'numero' => $data['numero'] ?? '',
            'bairro' => $data['bairro'] ?? '',
            'cidade' => $data['cidade'] ?? '',
            'estado' => $data['estado'] ?? '',
            'email' => $data['email'],
            'senha' => password_hash($data['senha'] ?? '123456', PASSWORD_DEFAULT),
            'telefone' => $data['telefone'] ?? '',
            'celular' => $data['celular'] ?? '',
            'dataExpiracao' => $data['dataExpiracao'] ?? null,
            'situacao' => $data['situacao'] ?? 1,
            'permissoes_id' => $data['permissoes_id'] ?? 1,
            'dataCadastro' => date('Y-m-d'),
        ];

        if ($this->usuarios_model->add('usuarios', $insertData)) {
            $lastUser = $this->db->order_by('idUsuarios', 'DESC')->limit(1)->get('usuarios')->row();
            unset($lastUser->senha);
            $this->created($lastUser);
        } else {
            $this->error('Erro ao criar usuario', 500);
        }
    }

    public function update(int $id = 0): void
    {
        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        // Permite auto-edicao sem permissao especial
        $isSelfEdit = isset($this->currentUser->sub) && $this->currentUser->sub == $id;
        if (!$isSelfEdit) {
            $this->checkPermission('usuarios_editar');
        }

        $usuario = $this->usuarios_model->getById($id);
        if (!$usuario) {
            $this->notFound('Usuario');
            return;
        }

        // Super admin nao pode ser desativado
        if ($id == 1 && isset($data['situacao']) && $data['situacao'] == 0) {
            $this->error('O usuario super admin nao pode ser desativado', 400);
            return;
        }

        $data = $this->getJsonInput();

        $updateData = [
            'nome' => $data['nome'] ?? $usuario->nome,
            'rg' => $data['rg'] ?? $usuario->rg,
            'cpf' => $data['cpf'] ?? $usuario->cpf,
            'cep' => $data['cep'] ?? $usuario->cep,
            'rua' => $data['rua'] ?? $usuario->rua,
            'numero' => $data['numero'] ?? $usuario->numero,
            'bairro' => $data['bairro'] ?? $usuario->bairro,
            'cidade' => $data['cidade'] ?? $usuario->cidade,
            'estado' => $data['estado'] ?? $usuario->estado,
            'email' => $data['email'] ?? $usuario->email,
            'telefone' => $data['telefone'] ?? $usuario->telefone,
            'celular' => $data['celular'] ?? $usuario->celular,
            'dataExpiracao' => $data['dataExpiracao'] ?? $usuario->dataExpiracao,
            'situacao' => $data['situacao'] ?? $usuario->situacao,
            'permissoes_id' => $data['permissoes_id'] ?? $usuario->permissoes_id,
        ];

        if (!empty($data['senha'])) {
            $updateData['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }

        if ($this->usuarios_model->edit('usuarios', $updateData, 'idUsuarios', $id)) {
            $updated = $this->usuarios_model->getById($id);
            unset($updated->senha);
            $this->updated($updated);
        } else {
            $this->error('Erro ao atualizar usuario', 500);
        }
    }

    public function delete(int $id = 0): void
    {
        $this->checkPermission('usuarios_excluir');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        if ($id == 1) {
            $this->error('O usuario super admin nao pode ser deletado', 400);
            return;
        }

        // Nao pode deletar a si mesmo
        if (isset($this->currentUser->sub) && $this->currentUser->sub == $id) {
            $this->forbidden('Voce nao pode excluir seu proprio usuario');
            return;
        }

        $usuario = $this->usuarios_model->getById($id);
        if (!$usuario) {
            $this->notFound('Usuario');
            return;
        }

        $this->usuarios_model->delete('usuarios', 'idUsuarios', $id);
        $this->deleted('Usuario removido com sucesso');
    }

    public function conta(): void
    {
        $userId = $this->currentUser->sub ?? 0;
        if (!$userId) {
            $this->unauthorized('Token invalido');
            return;
        }

        $usuario = $this->usuarios_model->getById($userId);
        if (!$usuario) {
            $this->notFound('Usuario');
            return;
        }

        unset($usuario->senha);

        if (!empty($usuario->url_image_user)) {
            $usuario->url_image_user = base_url() . 'assets/userImage/' . $usuario->url_image_user;
        }

        $this->success($usuario);
    }
}