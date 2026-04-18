<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller: Usuários do Portal do Cliente
 * Gerencia usuários que acessam a área do cliente
 */
class Usuarioscliente extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('usuarios_cliente_model');
        $this->load->model('clientes_model');
    }

    /**
     * Listagem de usuários
     */
    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente')) {
            $this->session->set_flashdata('error', 'Sem permissão para visualizar usuários do cliente.');
            redirect(base_url());
        }

        $this->data['usuarios'] = $this->usuarios_cliente_model->getAll();
        $this->data['view'] = 'usuarios_cliente/listar';

        return $this->layout();
    }

    /**
     * Adicionar usuário
     */
    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuariosCliente')) {
            $this->session->set_flashdata('error', 'Sem permissão para adicionar usuários.');
            redirect('usuarioscliente');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[usuarios_cliente.email]');
        $this->form_validation->set_rules('senha', 'Senha', 'required|min_length[6]');
        $this->form_validation->set_rules('confirmar_senha', 'Confirmar Senha', 'required|matches[senha]');

        if ($this->form_validation->run() == false) {
            $this->data['clientes'] = $this->clientes_model->get('clientes', 'idClientes, nomeCliente, documento, fornecedor', '', 0, 0);
            $this->data['permissoes_padrao'] = $this->usuarios_cliente_model->getPermissoesPadrao();
            $this->data['view'] = 'usuarios_cliente/adicionar';
            return $this->layout();
        }

        // Dados do usuário
        $data = [
            'nome' => $this->input->post('nome'),
            'email' => $this->input->post('email'),
            'senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
            'telefone' => $this->input->post('telefone'),
            'ativo' => $this->input->post('ativo') ? 1 : 0,
        ];

        // Vincular a um cliente existente (opcional)
        if ($this->input->post('cliente_id')) {
            $data['cliente_id'] = $this->input->post('cliente_id');
        }

        $usuario_id = $this->usuarios_cliente_model->add($data);

        if ($usuario_id) {
            // Vincular CNPJs
            $cnpjs = $this->input->post('cnpjs');
            $cnpjs_razao = $this->input->post('cnpjs_razao');
            if (!empty($cnpjs) && is_array($cnpjs)) {
                foreach ($cnpjs as $index => $cnpj) {
                    if (!empty($cnpj)) {
                        $razao = isset($cnpjs_razao[$index]) ? $cnpjs_razao[$index] : null;
                        $this->usuarios_cliente_model->addCnpj($usuario_id, $cnpj, $razao);
                    }
                }
            }

            // Salvar permissões
            $permissoes = $this->input->post('permissoes');
            if (!empty($permissoes) && is_array($permissoes)) {
                foreach ($permissoes as $chave => $valor) {
                    $this->usuarios_cliente_model->setPermissao($usuario_id, $chave, $valor ? true : false);
                }
            } else {
                // Aplicar permissões padrão
                $this->usuarios_cliente_model->aplicarPermissoesPadrao($usuario_id);
            }

            $this->session->set_flashdata('success', 'Usuário do cliente adicionado com sucesso!');
            log_info('Adicionou usuário do cliente: ' . $data['email']);
        } else {
            $this->session->set_flashdata('error', 'Erro ao adicionar usuário.');
        }

        redirect('usuarioscliente');
    }

    /**
     * Editar usuário
     */
    public function editar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eUsuariosCliente')) {
            $this->session->set_flashdata('error', 'Sem permissão para editar usuários.');
            redirect('usuarioscliente');
        }

        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID inválido.');
            redirect('usuarioscliente');
        }

        $usuario = $this->usuarios_cliente_model->getById($id);
        if (!$usuario) {
            $this->session->set_flashdata('error', 'Usuário não encontrado.');
            redirect('usuarioscliente');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        // Validação de senha apenas se preenchida
        if ($this->input->post('senha')) {
            $this->form_validation->set_rules('senha', 'Senha', 'min_length[6]');
            $this->form_validation->set_rules('confirmar_senha', 'Confirmar Senha', 'matches[senha]');
        }

        if ($this->form_validation->run() == false) {
            $this->data['usuario'] = $usuario;
            $this->data['clientes'] = $this->clientes_model->get('clientes', 'idClientes, nomeCliente, documento, fornecedor', '', 0, 0);
            $this->data['cnpjs'] = $this->usuarios_cliente_model->getCnpjs($id);
            $this->data['permissoes'] = $this->usuarios_cliente_model->getAllPermissoes($id);
            $this->data['permissoes_padrao'] = $this->usuarios_cliente_model->getPermissoesPadrao();
            $this->data['view'] = 'usuarios_cliente/editar';
            return $this->layout();
        }

        // Dados do usuário
        $data = [
            'nome' => $this->input->post('nome'),
            'email' => $this->input->post('email'),
            'telefone' => $this->input->post('telefone'),
            'ativo' => $this->input->post('ativo') ? 1 : 0,
        ];

        if ($this->input->post('cliente_id')) {
            $data['cliente_id'] = $this->input->post('cliente_id');
        } else {
            $data['cliente_id'] = null;
        }

        // Atualizar senha apenas se preenchida
        if ($this->input->post('senha')) {
            $data['senha'] = password_hash($this->input->post('senha'), PASSWORD_DEFAULT);
        }

        $this->usuarios_cliente_model->update($id, $data);

        // Atualizar CNPJs (remove todos e adiciona novamente)
        $this->db->where('usuario_cliente_id', $id);
        $this->db->delete('usuarios_cliente_cnpjs');

        $cnpjs = $this->input->post('cnpjs');
        $cnpjs_razao = $this->input->post('cnpjs_razao');
        if (!empty($cnpjs) && is_array($cnpjs)) {
            foreach ($cnpjs as $index => $cnpj) {
                if (!empty($cnpj)) {
                    $razao = isset($cnpjs_razao[$index]) ? $cnpjs_razao[$index] : null;
                    $this->usuarios_cliente_model->addCnpj($id, $cnpj, $razao);
                }
            }
        }

        // Atualizar permissões
        $permissoes = $this->input->post('permissoes');
        if (!empty($permissoes) && is_array($permissoes)) {
            foreach ($permissoes as $chave => $valor) {
                $this->usuarios_cliente_model->setPermissao($id, $chave, $valor ? true : false);
            }
        }

        $this->session->set_flashdata('success', 'Usuário atualizado com sucesso!');
        log_info('Editou usuário do cliente: ' . $data['email']);
        redirect('usuarioscliente');
    }

    /**
     * Excluir usuário
     */
    public function excluir($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dUsuariosCliente')) {
            $this->session->set_flashdata('error', 'Sem permissão para remover usuários.');
            redirect('usuarioscliente');
        }

        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID inválido.');
            redirect('usuarioscliente');
        }

        $usuario = $this->usuarios_cliente_model->getById($id);
        if (!$usuario) {
            $this->session->set_flashdata('error', 'Usuário não encontrado.');
            redirect('usuarioscliente');
        }

        $this->usuarios_cliente_model->delete($id);

        $this->session->set_flashdata('success', 'Usuário removido com sucesso!');
        log_info('Removeu usuário do cliente: ' . $usuario->email);
        redirect('usuarioscliente');
    }

    /**
     * Visualizar detalhes do usuário
     */
    public function visualizar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('usuarioscliente');
        }

        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID inválido.');
            redirect('usuarioscliente');
        }

        $usuario = $this->usuarios_cliente_model->getById($id);
        if (!$usuario) {
            $this->session->set_flashdata('error', 'Usuário não encontrado.');
            redirect('usuarioscliente');
        }

        // Carregar OS vinculadas
        $os = $this->usuarios_cliente_model->getOsByCnpjs($id);
        $stats = $this->usuarios_cliente_model->countOsByStatus($id);

        $this->data['usuario'] = $usuario;
        $this->data['cnpjs'] = $this->usuarios_cliente_model->getCnpjs($id);
        $this->data['permissoes'] = $this->usuarios_cliente_model->getAllPermissoes($id);
        $this->data['os'] = $os;
        $this->data['stats'] = $stats;
        $this->data['view'] = 'usuarios_cliente/visualizar';

        return $this->layout();
    }

    /**
     * API: Buscar CNPJ na ReceitaWS
     */
    public function api_consultar_cnpj()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $cnpj = $this->input->get('cnpj');
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            echo json_encode(['error' => 'CNPJ inválido']);
            return;
        }

        // Consulta na ReceitaWS
        $url = "https://www.receitaws.com.br/v1/cnpj/{$cnpj}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['status']) && $data['status'] === 'ERROR') {
            echo json_encode(['error' => $data['message'] ?? 'CNPJ não encontrado']);
        } else {
            echo json_encode([
                'success' => true,
                'data' => [
                    'nome' => $data['nome'] ?? '',
                    'fantasia' => $data['fantasia'] ?? '',
                    'cnpj' => $data['cnpj'] ?? $cnpj,
                ]
            ]);
        }
    }

    /**
     * Ativar usuário
     */
    public function ativar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eUsuariosCliente')) {
            $this->session->set_flashdata('error', 'Sem permissão para ativar usuários.');
            redirect('usuarioscliente');
        }

        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID inválido.');
            redirect('usuarioscliente');
        }

        $this->usuarios_cliente_model->update($id, ['ativo' => 1]);
        $this->session->set_flashdata('success', 'Usuário ativado com sucesso!');
        redirect('usuarioscliente');
    }

    /**
     * Desativar usuário
     */
    public function desativar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eUsuariosCliente')) {
            $this->session->set_flashdata('error', 'Sem permissão para desativar usuários.');
            redirect('usuarioscliente');
        }

        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID inválido.');
            redirect('usuarioscliente');
        }

        $this->usuarios_cliente_model->update($id, ['ativo' => 0]);
        $this->session->set_flashdata('success', 'Usuário desativado com sucesso!');
        redirect('usuarioscliente');
    }

    /**
     * AJAX: Adicionar CNPJ ao usuário
     */
    public function adicionar_cnpj($usuario_id = null)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!$usuario_id || !is_numeric($usuario_id)) {
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        $cnpj = $this->input->post('cnpj');
        $razao_social = $this->input->post('razao_social');

        if (empty($cnpj)) {
            echo json_encode(['error' => 'CNPJ é obrigatório']);
            return;
        }

        $result = $this->usuarios_cliente_model->addCnpj($usuario_id, $cnpj, $razao_social);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'CNPJ adicionado com sucesso!']);
        } else {
            echo json_encode(['error' => 'Erro ao adicionar CNPJ']);
        }
    }

    /**
     * AJAX: Remover CNPJ do usuário
     */
    public function remover_cnpj($usuario_id = null, $cnpj = null)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!$usuario_id || !is_numeric($usuario_id) || empty($cnpj)) {
            echo json_encode(['error' => 'Parâmetros inválidos']);
            return;
        }

        $result = $this->usuarios_cliente_model->removeCnpj($usuario_id, urldecode($cnpj));

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'CNPJ removido com sucesso!']);
        } else {
            echo json_encode(['error' => 'Erro ao remover CNPJ']);
        }
    }

    /**
     * AJAX: Listar CNPJs do usuário
     */
    public function get_cnpjs($usuario_id = null)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!$usuario_id || !is_numeric($usuario_id)) {
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        $cnpjs = $this->usuarios_cliente_model->getCnpjs($usuario_id);
        echo json_encode(['success' => true, 'data' => $cnpjs]);
    }
}
