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
            // Buscar permissão de técnico ou criar uma permissão padrão
            $permissao = $this->db->where('nome', 'Técnico')->get('permissoes')->row();
            if (!$permissao) {
                // Usar permissão de funcionário como fallback
                $permissao = $this->db->where('nome', 'Funcionario')->get('permissoes')->row();
            }
            $permissao_id = $permissao ? $permissao->idPermissao : 2; // Fallback para ID 2

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
                'permissoes_id' => $permissao_id,
            ];

            $resultado = $this->tecnicos_model->add($dados);
            if ($resultado) {
                $this->session->set_flashdata('success', 'Técnico cadastrado com sucesso!');
                redirect('tecnicos_admin/tecnicos');
            } else {
                $error = $this->db->error();
                $mensagem_erro = 'Erro ao cadastrar técnico.';
                if (isset($error['message'])) {
                    $mensagem_erro .= ' Detalhes: ' . $error['message'];
                }
                $this->session->set_flashdata('error', $mensagem_erro);
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
     * Adicionar nova obra
     */
    public function adicionar_obra()
    {
        $this->form_validation->set_rules('nome', 'Nome da Obra', 'required|trim');
        $this->form_validation->set_rules('cliente_id', 'Cliente', 'required');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('tecnicos_admin/obras');
        }

        $dados = [
            'nome' => $this->input->post('nome'),
            'codigo' => $this->input->post('codigo') ?? 'OB-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'cliente_id' => $this->input->post('cliente_id'),
            'tipo_obra' => $this->input->post('tipo_obra') ?? 'Outro',
            'endereco' => $this->input->post('endereco'),
            'data_inicio' => $this->input->post('data_inicio') ?: date('Y-m-d'),
            'data_previsao_fim' => $this->input->post('data_previsao_fim'),
            'descricao' => $this->input->post('descricao'),
            'status' => 'planejamento',
            'percentual_concluido' => 0,
        ];

        $this->load->model('obras_model');
        if ($obra_id = $this->obras_model->add($dados)) {
            $this->session->set_flashdata('success', 'Obra cadastrada com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao cadastrar obra.');
        }

        redirect('tecnicos_admin/obras');
    }

    /**
     * Visualizar obra
     */
    public function ver_obra($id)
    {
        $this->load->model('obras_model');
        $this->load->model('os_model');

        $obra = $this->obras_model->getById($id);

        if (!$obra) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('tecnicos_admin/obras');
        }

        // Buscar OS vinculadas à obra
        $this->db->select('os.idOs, os.dataInicial, os.dataFinal, os.status, os.garantia, os.valorTotal, c.nomeCliente, u.nome as responsavel');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->join('usuarios u', 'u.idUsuarios = os.usuarios_id', 'left');
        $this->db->where('os.obra_id', $id);
        $this->db->order_by('os.dataInicial', 'DESC');
        $query = $this->db->get();
        $os_vinculadas = $query ? $query->result() : [];

        // Buscar técnicos para o modal de alocação
        $this->db->where('status', 1);
        $tecnicos_query = $this->db->get('usuarios');
        $tecnicos = $tecnicos_query ? $tecnicos_query->result() : [];

        $this->data['obra'] = $obra;
        $this->data['etapas'] = $this->obras_model->getEtapas($id);
        $this->data['equipe'] = $this->obras_model->getEquipe($id);
        $this->data['diario'] = $this->obras_model->getDiario($id);
        $this->data['os_vinculadas'] = $os_vinculadas;
        $this->data['tecnicos'] = $tecnicos;
        $this->data['view'] = 'tecnicos_admin/obra_view';
        return $this->layout();
    }

    /**
     * Editar obra
     */
    public function editar_obra($id)
    {
        $this->load->model('obras_model');
        $obra = $this->obras_model->getById($id);

        if (!$obra) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('tecnicos_admin/obras');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nome', 'Nome', 'required|trim');

            if ($this->form_validation->run()) {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'codigo' => $this->input->post('codigo'),
                    'cliente_id' => $this->input->post('cliente_id'),
                    'tipo_obra' => $this->input->post('tipo_obra'),
                    'endereco' => $this->input->post('endereco'),
                    'data_inicio' => $this->input->post('data_inicio'),
                    'data_previsao_fim' => $this->input->post('data_previsao_fim'),
                    'descricao' => $this->input->post('descricao'),
                    'status' => $this->input->post('status'),
                ];

                if ($this->obras_model->update($id, $dados)) {
                    $this->session->set_flashdata('success', 'Obra atualizada com sucesso!');
                    redirect('tecnicos_admin/obras');
                } else {
                    $this->session->set_flashdata('error', 'Erro ao atualizar obra.');
                }
            }
        }

        $this->data['obra'] = $obra;

        // Carregar informações do cliente atual da obra
        if ($obra->cliente_id) {
            $this->load->model('clientes_model');
            $this->data['cliente_atual'] = $this->clientes_model->getById($obra->cliente_id);
        } else {
            $this->data['cliente_atual'] = null;
        }

        // Carregar lista de clientes ativos
        $this->db->order_by('nomeCliente', 'ASC');
        $clientes_query = $this->db->get('clientes');
        $this->data['clientes'] = $clientes_query ? $clientes_query->result() : [];

        $this->data['view'] = 'tecnicos_admin/obra_form';
        return $this->layout();
    }

    /**
     * Excluir obra
     */
    public function excluir_obra($id)
    {
        $this->load->model('obras_model');
        if ($this->obras_model->delete($id)) {
            $this->session->set_flashdata('success', 'Obra excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir obra.');
        }

        redirect('tecnicos_admin/obras');
    }

    /**
     * Estoque de técnico
     */
    public function estoque_tecnico($tecnico_id)
    {
        $this->data['tecnico'] = $this->tecnicos_model->getById($tecnico_id);
        $this->data['estoque'] = $this->tecnicos_model->getEstoqueVeiculo($tecnico_id);
        $produtos_query = $this->db->get('produtos');
        $this->data['produtos'] = $produtos_query ? $produtos_query->result() : [];
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
        $os_por_dia_query = $this->db->get('tec_os_execucao');
        $os_por_dia = $os_por_dia_query ? $os_por_dia_query->result() : [];

        // OS por técnico
        $this->db->select('tecnico_id, COUNT(*) as total');
        $this->db->where('data_checkin >=', $inicio);
        $this->db->group_by('tecnico_id');
        $os_por_tecnico_query = $this->db->get('tec_os_execucao');
        $os_por_tecnico = $os_por_tecnico_query ? $os_por_tecnico_query->result() : [];

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

    /**
     * Adicionar etapa à obra
     */
    public function adicionar_etapa()
    {
        $obra_id = $this->input->post('obra_id');

        if (!$obra_id) {
            $this->session->set_flashdata('error', 'ID da obra não informado.');
            redirect('tecnicos_admin/obras');
        }

        $this->load->model('obras_model');

        $dados = [
            'nome' => $this->input->post('nome'),
            'descricao' => $this->input->post('descricao'),
            'data_inicio_prevista' => $this->input->post('data_inicio_prevista') ?: null,
            'data_fim_prevista' => $this->input->post('data_fim_prevista') ?: null,
        ];

        if ($this->obras_model->adicionarEtapa($obra_id, $dados)) {
            $this->session->set_flashdata('success', 'Etapa adicionada com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao adicionar etapa.');
        }

        redirect('tecnicos_admin/ver_obra/' . $obra_id);
    }

    /**
     * Alocar técnico à obra
     */
    public function alocar_tecnico()
    {
        $obra_id = $this->input->post('obra_id');

        if (!$obra_id) {
            $this->session->set_flashdata('error', 'ID da obra não informado.');
            redirect('tecnicos_admin/obras');
        }

        $this->load->model('obras_model');

        $dados = [
            'obra_id' => $obra_id,
            'tecnico_id' => $this->input->post('tecnico_id'),
            'funcao' => $this->input->post('funcao') ?: 'Técnico',
            'nivel_tecnico' => $this->input->post('nivel_tecnico') ?: null,
            'data_alocacao' => date('Y-m-d H:i:s')
        ];

        if ($this->obras_model->alocarTecnico($dados)) {
            $this->session->set_flashdata('success', 'Técnico alocado com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao alocar técnico.');
        }

        redirect('tecnicos_admin/ver_obra/' . $obra_id);
    }

    /**
     * Salvar materiais da obra
     */
    public function salvar_materiais()
    {
        $obra_id = $this->input->post('obra_id');

        if (!$obra_id) {
            $this->session->set_flashdata('error', 'ID da obra não informado.');
            redirect('tecnicos_admin/obras');
        }

        $this->load->model('obras_model');

        $materiais = $this->input->post('materiais');

        if (!empty($materiais) && is_array($materiais)) {
            $salvos = 0;
            foreach ($materiais as $material) {
                if (!empty($material['nome'])) {
                    $dados = [
                        'obra_id' => $obra_id,
                        'nome' => $material['nome'],
                        'quantidade' => $material['quantidade'] ?: 1,
                        'observacao' => $material['observacao'] ?: null,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->obras_model->adicionarMaterial($dados)) {
                        $salvos++;
                    }
                }
            }

            if ($salvos > 0) {
                $this->session->set_flashdata('success', $salvos . ' material(is) salvo(s) com sucesso!');
            } else {
                $this->session->set_flashdata('error', 'Erro ao salvar materiais.');
            }
        } else {
            $this->session->set_flashdata('error', 'Nenhum material informado.');
        }

        redirect('tecnicos_admin/ver_obra/' . $obra_id);
    }

    /**
     * Buscar OS disponíveis para vincular à obra
     */
    public function buscar_os_disponiveis()
    {
        $this->load->model('os_model');
        $this->load->model('obras_model');

        $obra_id = $this->input->get('obra_id');
        $cliente_id = $this->input->get('cliente_id');
        $termo = $this->input->get('termo');

        if (!$obra_id) {
            echo json_encode(['success' => false, 'message' => 'Obra não informada']);
            return;
        }

        // Buscar obra para verificar o cliente
        $obra = $this->obras_model->getById($obra_id);
        if (!$obra) {
            echo json_encode(['success' => false, 'message' => 'Obra não encontrada']);
            return;
        }

        // Buscar OS do cliente que não estão vinculadas a nenhuma obra
        $this->db->select('os.idOs, os.dataInicial, os.dataFinal, os.status, c.nomeCliente, os.obra_id');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->where('os.clientes_id', $obra->cliente_id);
        $this->db->where('(os.obra_id IS NULL OR os.obra_id = 0)');

        if ($termo) {
            $this->db->like('os.idOs', $termo);
        }

        $this->db->order_by('os.dataInicial', 'DESC');
        $this->db->limit(20);
        $query = $this->db->get();
        $os = $query ? $query->result() : [];

        echo json_encode(['success' => true, 'os' => $os]);
    }

    /**
     * Vincular OS à obra
     */
    public function vincular_os_obra()
    {
        $obra_id = $this->input->post('obra_id');
        $os_id = $this->input->post('os_id');

        if (!$obra_id || !$os_id) {
            $this->session->set_flashdata('error', 'Dados incompletos.');
            redirect('tecnicos_admin/obras');
        }

        $this->load->model('os_model');
        $this->load->model('obras_model');

        // Verificar se a obra existe
        $obra = $this->obras_model->getById($obra_id);
        if (!$obra) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('tecnicos_admin/obras');
        }

        // Verificar se a OS existe
        $os = $this->os_model->getById($os_id);
        if (!$os) {
            $this->session->set_flashdata('error', 'Ordem de Serviço não encontrada.');
            redirect('tecnicos_admin/ver_obra/' . $obra_id);
        }

        // Verificar se OS já está vinculada a outra obra
        if (!empty($os->obra_id) && $os->obra_id != $obra_id) {
            $this->session->set_flashdata('error', 'Esta OS já está vinculada a outra obra.');
            redirect('tecnicos_admin/ver_obra/' . $obra_id);
        }

        // Vincular OS à obra
        $this->db->where('idOs', $os_id);
        $this->db->update('os', ['obra_id' => $obra_id]);

        $this->session->set_flashdata('success', 'Ordem de Serviço vinculada com sucesso!');
        redirect('tecnicos_admin/ver_obra/' . $obra_id);
    }

    /**
     * Desvincular OS da obra
     */
    public function desvincular_os_obra()
    {
        $obra_id = $this->input->post('obra_id');
        $os_id = $this->input->post('os_id');

        if (!$obra_id || !$os_id) {
            $this->session->set_flashdata('error', 'Dados incompletos.');
            redirect('tecnicos_admin/obras');
        }

        $this->load->model('os_model');

        // Desvincular OS (setar obra_id como NULL)
        $this->db->where('idOs', $os_id);
        $this->db->update('os', ['obra_id' => null]);

        $this->session->set_flashdata('success', 'Ordem de Serviço desvinculada com sucesso!');
        redirect('tecnicos_admin/ver_obra/' . $obra_id);
    }
}
