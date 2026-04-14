<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller Administrativo de Gestão de Técnicos
 *
 * Área administrativa para:
 * - Cadastro e gerenciamento de técnicos
 * - Catálogo de serviços
 * - Checklists templates
 * - Relatórios e estatísticas
 * - Gestão de obras
 */
class Tecnicos_admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tecnicos_model');
        $this->load->model('tec_os_model');
        $this->load->model('mapos_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('form');

        // Verificar permissão de administrador
        if (!$this->session->userdata('logado')) {
            redirect('login');
        }

        // Verificar se é admin ou tem permissão
        $permissao = $this->mapOS_model->hasPermission('tec_admin');
        if (!$permissao) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar esta área.');
            redirect('mapos');
        }
    }

    /**
     * Dashboard administrativo
     */
    public function index()
    {
        $data['total_tecnicos'] = $this->tecnicos_model->count();
        $data['os_hoje'] = $this->tec_os_model->getOsDoDia(null); // Todas as OS de hoje
        $data['execucoes_mes'] = $this->tec_os_model->getEstatisticasExecucao(null, 'mes');

        $this->load->view('tecnicos_admin/dashboard', $data);
    }

    /**
     * Listar técnicos
     */
    public function tecnicos()
    {
        $data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->load->view('tecnicos_admin/tecnicos_list', $data);
    }

    /**
     * Cadastrar novo técnico
     */
    public function adicionar_tecnico()
    {
        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|is_unique[usuarios.email]');
        $this->form_validation->set_rules('senha', 'Senha', 'required|min_length[6]');
        $this->form_validation->set_rules('nivel_tecnico', 'Nível', 'required');

        if ($this->form_validation->run() === false) {
            $this->load->view('tecnicos_admin/tecnico_form');
        } else {
            $dados = [
                'nome' => $this->input->post('nome'),
                'email' => $this->input->post('email'),
                'senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                'telefone' => $this->input->post('telefone'),
                'cpf' => $this->input->post('cpf'),
                'nivel_tecnico' => $this->input->post('nivel_tecnico'),
                'especialidades' => $this->input->post('especialidades'),
                'veiculo_placa' => $this->input->post('veiculo_placa'),
                'veiculo_tipo' => $this->input->post('veiculo_tipo'),
                'coordenadas_base_lat' => $this->input->post('coordenadas_base_lat'),
                'coordenadas_base_lng' => $this->input->post('coordenadas_base_lng'),
                'raio_atuacao_km' => $this->input->post('raio_atuacao_km') ?: 0,
                'plantao_24h' => $this->input->post('plantao_24h') ? 1 : 0,
            ];

            if ($this->tecnicos_model->add($dados)) {
                $this->session->set_flashdata('success', 'Técnico cadastrado com sucesso!');
                redirect('tecnicos_admin/tecnicos');
            } else {
                $this->session->set_flashdata('error', 'Erro ao cadastrar técnico.');
                $this->load->view('tecnicos_admin/tecnico_form');
            }
        }
    }

    /**
     * Editar técnico
     */
    public function editar_tecnico($id)
    {
        $data['tecnico'] = $this->tecnicos_model->getById($id);

        if (!$data['tecnico']) {
            $this->session->set_flashdata('error', 'Técnico não encontrado.');
            redirect('tecnicos_admin/tecnicos');
        }

        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');

        if ($this->form_validation->run() === false) {
            $this->load->view('tecnicos_admin/tecnico_form', $data);
        } else {
            $dados = [
                'nome' => $this->input->post('nome'),
                'telefone' => $this->input->post('telefone'),
                'nivel_tecnico' => $this->input->post('nivel_tecnico'),
                'especialidades' => $this->input->post('especialidades'),
                'veiculo_placa' => $this->input->post('veiculo_placa'),
                'veiculo_tipo' => $this->input->post('veiculo_tipo'),
                'raio_atuacao_km' => $this->input->post('raio_atuacao_km'),
                'plantao_24h' => $this->input->post('plantao_24h') ? 1 : 0,
            ];

            if ($this->input->post('senha')) {
                $dados['senha'] = password_hash($this->input->post('senha'), PASSWORD_DEFAULT);
            }

            if ($this->tecnicos_model->update($id, $dados)) {
                $this->session->set_flashdata('success', 'Técnico atualizado com sucesso!');
                redirect('tecnicos_admin/tecnicos');
            } else {
                $this->session->set_flashdata('error', 'Erro ao atualizar técnico.');
                $this->load->view('tecnicos_admin/tecnico_form', $data);
            }
        }
    }

    /**
     * Visualizar detalhes do técnico
     */
    public function ver_tecnico($id)
    {
        $data['tecnico'] = $this->tecnicos_model->getById($id);

        if (!$data['tecnico']) {
            $this->session->set_flashdata('error', 'Técnico não encontrado.');
            redirect('tecnicos_admin/tecnicos');
        }

        $data['estatisticas'] = $this->tecnicos_model->getEstatisticas($id, 'mes');
        $data['os_recentes'] = $this->tec_os_model->getOsPorTecnico($id, 'todos');
        $data['estoque'] = $this->tecnicos_model->getEstoqueVeiculo($id);

        $this->load->view('tecnicos_admin/tecnico_view', $data);
    }

    /**
     * Catálogo de serviços
     */
    public function servicos_catalogo()
    {
        $data['servicos'] = $this->tec_os_model->getServicosCatalogo();
        $this->load->view('tecnicos_admin/servicos_catalogo', $data);
    }

    /**
     * Adicionar serviço ao catálogo
     */
    public function adicionar_servico()
    {
        $this->form_validation->set_rules('codigo', 'Código', 'required');
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('tipo', 'Tipo', 'required');

        if ($this->form_validation->run() === false) {
            $data['tipos'] = ['INS', 'MP', 'MC', 'CT', 'TR', 'UP', 'URG'];
            $this->load->view('tecnicos_admin/servico_form', $data);
        } else {
            $dados = [
                'codigo' => $this->input->post('codigo'),
                'nome' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'tipo' => $this->input->post('tipo'),
                'categoria' => $this->input->post('categoria'),
                'tempo_estimado_horas' => $this->input->post('tempo_estimado_horas'),
                'checklist_padrao' => $this->preparar_checklist($this->input->post('checklist')),
            ];

            if ($this->tec_os_model->adicionarServicoCatalogo($dados)) {
                $this->session->set_flashdata('success', 'Serviço adicionado ao catálogo!');
                redirect('tecnicos_admin/servicos_catalogo');
            } else {
                $this->session->set_flashdata('error', 'Erro ao adicionar serviço.');
                redirect('tecnicos_admin/adicionar_servico');
            }
        }
    }

    /**
     * Checklists templates
     */
    public function checklists()
    {
        $data['checklists'] = $this->tec_os_model->getChecklistTemplates();
        $this->load->view('tecnicos_admin/checklists', $data);
    }

    /**
     * Relatórios
     */
    public function relatorios()
    {
        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');

        $data['data_inicio'] = $data_inicio;
        $data['data_fim'] = $data_fim;
        $data['tecnicos'] = $this->tecnicos_model->getAll();

        // Estatísticas agregadas
        $this->db->select('COUNT(*) as total_os, AVG(tempo_total_horas) as media_tempo');
        $this->db->where('data_checkin >=', $data_inicio);
        $this->db->where('data_checkin <=', $data_fim . ' 23:59:59');
        $data['estatisticas'] = $this->db->get('tec_os_execucao')->row();

        $this->load->view('tecnicos_admin/relatorios', $data);
    }

    /**
     * Gestão de Obras
     */
    public function obras()
    {
        $this->load->model('obras_model');
        $data['obras'] = $this->obras_model->getAll();
        $this->load->view('tecnicos_admin/obras_list', $data);
    }

    /**
     * Estoque de técnico
     */
    public function estoque_tecnico($tecnico_id)
    {
        $data['tecnico'] = $this->tecnicos_model->getById($tecnico_id);
        $data['estoque'] = $this->tecnicos_model->getEstoqueVeiculo($tecnico_id);
        $data['produtos'] = $this->db->get('produtos')->result(); // Produtos disponíveis

        $this->load->view('tecnicos_admin/estoque_tecnico', $data);
    }

    /**
     * Adicionar item ao estoque do técnico
     */
    public function adicionar_estoque()
    {
        $tecnico_id = $this->input->post('tecnico_id');
        $produto_id = $this->input->post('produto_id');
        $quantidade = $this->input->post('quantidade');

        if ($this->tecnicos_model->atualizarEstoque($tecnico_id, $produto_id, $quantidade, 'entrada', 'Adicionado pelo administrador')) {
            $this->session->set_flashdata('success', 'Estoque atualizado!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao atualizar estoque.');
        }

        redirect('tecnicos_admin/estoque_tecnico/' . $tecnico_id);
    }

    /**
     * Rastreamento de rotas
     */
    public function rotas($tecnico_id = null)
    {
        $data['tecnico_id'] = $tecnico_id;
        $data['data'] = $this->input->get('data') ?: date('Y-m-d');

        if ($tecnico_id) {
            $data['tecnico'] = $this->tecnicos_model->getById($tecnico_id);
            $data['rotas'] = $this->tecnicos_model->getRotas($tecnico_id, $data['data']);
        }

        $data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->load->view('tecnicos_admin/rotas', $data);
    }

    /**
     * API - Dados para gráficos
     */
    public function api_dados_dashboard()
    {
        header('Content-Type: application/json');

        $periodo = $this->input->get('periodo') ?: 'mes';

        switch ($periodo) {
            case 'semana':
                $inicio = date('Y-m-d', strtotime('-7 days'));
                break;
            case 'mes':
                $inicio = date('Y-m-01');
                break;
            default:
                $inicio = date('Y-m-d', strtotime('-30 days'));
        }

        // OS por dia
        $this->db->select('DATE(data_checkin) as dia, COUNT(*) as total');
        $this->db->where('data_checkin >=', $inicio);
        $this->db->group_by('DATE(data_checkin)');
        $this->db->order_by('dia', 'ASC');
        $os_por_dia = $this->db->get('tec_os_execucao')->result();

        // OS por técnico
        $this->db->select('tecnico_id, COUNT(*) as total');
        $this->db->where('data_checkin >=', $inicio);
        $this->db->group_by('tecnico_id');
        $os_por_tecnico = $this->db->get('tec_os_execucao')->result();

        echo json_encode([
            'os_por_dia' => $os_por_dia,
            'os_por_tecnico' => $os_por_tecnico,
        ]);
    }

    /**
     * Preparar dados do checklist
     */
    private function preparar_checklist($checklist)
    {
        if (!$checklist || !is_array($checklist)) {
            return null;
        }

        $itens = [];
        foreach ($checklist as $item) {
            if (!empty($item['descricao'])) {
                $itens[] = [
                    'descricao' => $item['descricao'],
                    'tipo' => $item['tipo'] ?? 'checkbox',
                    'obrigatorio' => isset($item['obrigatorio']) ? true : false,
                ];
            }
        }

        return $itens;
    }
}
