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
class Tecnicos_admin extends MY_Controller
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
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar esta área.');
            redirect('mapos');
        }

        $this->data['menuTecnicosAdmin'] = 'TecnicosAdmin';
    }

    /**
     * Dashboard administrativo
     */
    public function index()
    {
        $this->data['total_tecnicos'] = $this->tecnicos_model->count();
        $this->data['os_hoje'] = $this->tec_os_model->getOsDoDia(null);
        $this->data['execucoes_mes'] = $this->tec_os_model->getEstatisticasExecucao(null, 'mes');
        $this->data['view'] = 'tecnicos_admin/dashboard';

        return $this->layout();
    }

    /**
     * Listar técnicos
     */
    public function tecnicos()
    {
        $this->data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->data['view'] = 'tecnicos_admin/tecnicos_list';

        return $this->layout();
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
            $this->data['view'] = 'tecnicos_admin/tecnico_form';
            return $this->layout();
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
                'is_tecnico' => 1,
            ];

            if ($this->tecnicos_model->add($dados)) {
                $this->session->set_flashdata('success', 'Técnico cadastrado com sucesso!');
                redirect('tecnicos_admin/tecnicos');
            } else {
                $this->session->set_flashdata('error', 'Erro ao cadastrar técnico.');
                $this->data['view'] = 'tecnicos_admin/tecnico_form';
                return $this->layout();
            }
        }
    }

    /**
     * Editar técnico
     */
    public function editar_tecnico($id)
    {
        $this->data['tecnico'] = $this->tecnicos_model->getById($id);

        if (!$this->data['tecnico']) {
            $this->session->set_flashdata('error', 'Técnico não encontrado.');
            redirect('tecnicos_admin/tecnicos');
        }

        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');

        if ($this->form_validation->run() === false) {
            $this->data['view'] = 'tecnicos_admin/tecnico_form';
            return $this->layout();
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
                $this->data['view'] = 'tecnicos_admin/tecnico_form';
                return $this->layout();
            }
        }
    }

    /**
     * Visualizar detalhes do técnico
     */
    public function ver_tecnico($id)
    {
        $this->data['tecnico'] = $this->tecnicos_model->getById($id);

        if (!$this->data['tecnico']) {
            $this->session->set_flashdata('error', 'Técnico não encontrado.');
            redirect('tecnicos_admin/tecnicos');
        }

        $this->data['estatisticas'] = $this->tecnicos_model->getEstatisticas($id, 'mes');
        $this->data['os_recentes'] = $this->tec_os_model->getOsPorTecnico($id, 'todos');
        $this->data['estoque'] = $this->tecnicos_model->getEstoqueVeiculo($id);
        $this->data['view'] = 'tecnicos_admin/tecnico_view';

        return $this->layout();
    }

    /**
     * Catálogo de serviços
     */
    public function servicos_catalogo()
    {
        $this->data['servicos'] = $this->tec_os_model->getServicosCatalogo();
        $this->data['view'] = 'tecnicos_admin/servicos_catalogo';
        return $this->layout();
    }

    /**
     * Adicionar serviço ao catálogo (redireciona para controller de servicos)
     */
    public function adicionar_servico()
    {
        redirect('servicos/adicionar');
    }

    /**
     * Checklists templates
     */
    public function checklists()
    {
        $this->data['checklists'] = $this->tec_os_model->getChecklistTemplates();
        $this->data['view'] = 'tecnicos_admin/checklists';
        return $this->layout();
    }

    /**
     * Relatórios
     */
    public function relatorios()
    {
        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');

        $this->data['data_inicio'] = $data_inicio;
        $this->data['data_fim'] = $data_fim;
        $this->data['tecnicos'] = $this->tecnicos_model->getAll();

        // Estatísticas agregadas
        $this->db->select('COUNT(*) as total_os, AVG(tempo_total_horas) as media_tempo');
        $this->db->where('data_checkin >=', $data_inicio);
        $this->db->where('data_checkin <=', $data_fim . ' 23:59:59');
        $this->data['estatisticas'] = $this->db->get('tec_os_execucao')->row();
        $this->data['view'] = 'tecnicos_admin/relatorios';

        return $this->layout();
    }

    /**
     * Salvar novo template de checklist
     */
    public function salvar_checklist()
    {
        $this->form_validation->set_rules('nome_template', 'Nome do Template', 'required|trim');
        $this->form_validation->set_rules('tipo_os', 'Tipo de OS', 'required');
        $this->form_validation->set_rules('tipo_servico', 'Tipo de Serviço', 'required');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('tecnicos_admin/checklists');
        }

        $dados = [
            'nome_template' => $this->input->post('nome_template'),
            'tipo_os' => $this->input->post('tipo_os'),
            'tipo_servico' => $this->input->post('tipo_servico'),
            'itens' => json_encode([]),
            'ativo' => 1,
        ];

        if ($this->db->insert('tec_checklist_template', $dados)) {
            $this->session->set_flashdata('success', 'Template de checklist criado com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao criar template.');
        }

        redirect('tecnicos_admin/checklists');
    }

    /**
     * Excluir template de checklist
     */
    public function excluir_checklist($id)
    {
        $this->db->where('id', $id);
        if ($this->db->update('tec_checklist_template', ['ativo' => 0])) {
            $this->session->set_flashdata('success', 'Template excluído com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir template.');
        }

        redirect('tecnicos_admin/checklists');
    }

    /**
     * Gestão de Obras
     */
    public function obras()
    {
        $this->load->model('obras_model');
        $this->data['obras'] = $this->obras_model->getAll();
        $this->data['view'] = 'tecnicos_admin/obras_list';
        return $this->layout();
    }

    /**
     * Estoque de técnico
     */
    public function estoque_tecnico($tecnico_id)
    {
        $this->data['tecnico'] = $this->tecnicos_model->getById($tecnico_id);
        $this->data['estoque'] = $this->tecnicos_model->getEstoqueVeiculo($tecnico_id);
        $this->data['produtos'] = $this->db->get('produtos')->result();
        $this->data['view'] = 'tecnicos_admin/estoque_tecnico';
        return $this->layout();
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
        $this->data['tecnico_id'] = $tecnico_id;
        $this->data['data'] = $this->input->get('data') ?: date('Y-m-d');

        if ($tecnico_id) {
            $this->data['tecnico'] = $this->tecnicos_model->getById($tecnico_id);
            $this->data['rotas'] = $this->tecnicos_model->getRotas($tecnico_id, $this->data['data']);
        }

        $this->data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->data['view'] = 'tecnicos_admin/rotas';
        return $this->layout();
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
