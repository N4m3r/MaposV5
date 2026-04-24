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

        // Processar itens JSON
        $itens_json = $this->input->post('itens_json');
        $itens = [];

        if ($itens_json) {
            $itens_decodificados = json_decode($itens_json, true);
            if (is_array($itens_decodificados) && count($itens_decodificados) > 0) {
                $itens = $itens_decodificados;
            }
        }

        // Se não veio via JSON, tenta pegar do formato antigo (compatibilidade)
        if (empty($itens) && $this->input->post('itens')) {
            $itens_enviados = $this->input->post('itens');
            if (is_array($itens_enviados)) {
                foreach ($itens_enviados as $i => $item) {
                    if (!empty($item['descricao'])) {
                        $itens[] = [
                            'id' => $i + 1,
                            'descricao' => $item['descricao'],
                            'hint' => $item['hint'] ?? '',
                            'ordem' => $i + 1
                        ];
                    }
                }
            }
        }

        // Validar se tem pelo menos 1 item
        if (empty($itens)) {
            $this->session->set_flashdata('error', 'Adicione pelo menos um item ao checklist!');
            redirect('tecnicos_admin/checklists');
        }

        $dados = [
            'nome_template' => $this->input->post('nome_template'),
            'tipo_os' => $this->input->post('tipo_os'),
            'tipo_servico' => $this->input->post('tipo_servico'),
            'itens' => json_encode($itens),
            'ativo' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('tec_checklist_template', $dados)) {
            $this->session->set_flashdata('success', 'Template de checklist criado com sucesso! (' . count($itens) . ' itens)');
        } else {
            $error = $this->db->error();
            $this->session->set_flashdata('error', 'Erro ao criar template: ' . ($error['message'] ?? 'Erro desconhecido'));
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
     * Buscar itens do checklist via AJAX (para modal de execução)
     */
    public function get_checklist_itens($id = null)
    {
        header('Content-Type: application/json');

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID não informado']);
            return;
        }

        $this->db->where('id', $id);
        $this->db->where('ativo', 1);
        $checklist = $this->db->get('tec_checklist_template')->row();

        if (!$checklist) {
            echo json_encode(['success' => false, 'message' => 'Checklist não encontrado']);
            return;
        }

        // Decodificar itens
        $itens = [];
        if (isset($checklist->itens)) {
            if (is_string($checklist->itens)) {
                $itens = json_decode($checklist->itens, true) ?: [];
            } elseif (is_array($checklist->itens)) {
                $itens = $checklist->itens;
            }
        }

        echo json_encode([
            'success' => true,
            'checklist' => [
                'id' => $checklist->id,
                'nome' => $checklist->nome_template,
                'tipo_os' => $checklist->tipo_os,
                'tipo_servico' => $checklist->tipo_servico
            ],
            'itens' => $itens
        ]);
    }

    /**
     * Salvar execução do checklist (uso pelo técnico)
     */
    public function salvar_execucao_checklist()
    {
        header('Content-Type: application/json');

        $dados = json_decode(file_get_contents('php://input'), true);

        if (!$dados || !isset($dados['checklist_id'])) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        // Aqui você pode salvar no banco a execução do checklist
        // Ex: tabela os_checklist_execucao

        echo json_encode([
            'success' => true,
            'message' => 'Checklist salvo com sucesso!',
            'data' => $dados
        ]);
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

        // Filtrar apenas etapas com datas definidas
        $etapasTodas = $this->obras_model->getEtapas($id);
        $this->data['etapas'] = array_filter($etapasTodas, function($etapa) {
            return !empty($etapa->data_inicio_prevista) && !empty($etapa->data_fim_prevista);
        });

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
                    'data_inicio_contrato' => $this->input->post('data_inicio'),
                    'data_fim_prevista' => $this->input->post('data_previsao_fim'),
                    'observacoes' => $this->input->post('descricao'),
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
        $this->load->model('clientes_model');

        $obra_id = $this->input->get('obra_id');
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

        // Buscar o cliente da obra para obter o CNPJ
        $cliente = $this->clientes_model->getById($obra->cliente_id);
        if (!$cliente || empty($cliente->documento)) {
            echo json_encode(['success' => false, 'message' => 'Cliente da obra não possui CNPJ cadastrado']);
            return;
        }

        // Limpar o CNPJ para busca (remover pontuação)
        $cnpj_limpo = preg_replace('/[^0-9]/', '', $cliente->documento);

        // Buscar todos os clientes com o mesmo CNPJ
        $this->db->where('REPLACE(REPLACE(REPLACE(documento, ".", ""), "/", ""), "-", "") =', $cnpj_limpo);
        $clientes_query = $this->db->get('clientes');
        $clientes_ids = [];
        if ($clientes_query) {
            foreach ($clientes_query->result() as $c) {
                $clientes_ids[] = $c->idClientes;
            }
        }

        // Se não encontrou clientes pelo CNPJ, usar o cliente_id da obra
        if (empty($clientes_ids)) {
            $clientes_ids = [$obra->cliente_id];
        }

        // Buscar OS dos clientes com o mesmo CNPJ que não estão vinculadas a nenhuma obra
        $this->db->select('os.idOs, os.dataInicial, os.dataFinal, os.status, c.nomeCliente, c.documento, os.obra_id');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->where_in('os.clientes_id', $clientes_ids);
        $this->db->where('(os.obra_id IS NULL OR os.obra_id = 0)');

        if ($termo) {
            $this->db->like('os.idOs', $termo);
        }

        $this->db->order_by('os.dataInicial', 'DESC');
        $this->db->limit(20);
        $query = $this->db->get();
        $os = $query ? $query->result() : [];

        // Buscar total de OS do cliente (para depuração)
        $this->db->where_in('clientes_id', $clientes_ids);
        $total_os_cliente = $this->db->count_all_results('os');

        echo json_encode([
            'success' => true,
            'os' => $os,
            'cnpj' => $cliente->documento,
            'cliente_nome' => $cliente->nomeCliente,
            'clientes_ids' => $clientes_ids,
            'total_os_cliente' => $total_os_cliente,
            'obra_cliente_id' => $obra->cliente_id
        ]);
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
        $mensagem = 'Ordem de Serviço vinculada com sucesso!';
        if (!empty($os->obra_id) && $os->obra_id != $obra_id) {
            // Buscar nome da obra anterior
            $obraAnterior = $this->obras_model->getById($os->obra_id);
            $nomeObraAnterior = $obraAnterior ? $obraAnterior->nome : 'outra obra';
            $mensagem = 'OS movida de "' . $nomeObraAnterior . '" para esta obra com sucesso!';
        }

        // Vincular OS à obra (ou mover de outra obra)
        $this->db->where('idOs', $os_id);
        $this->db->update('os', ['obra_id' => $obra_id]);

        $this->session->set_flashdata('success', $mensagem);
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

    /**
     * Visão de Execução das Obras - Dashboard simplificado
     */
    public function execucao_obras()
    {
        $this->load->model('obras_model');
        $this->load->model('os_model');

        // Buscar todas as obras ativas com seus dados
        $this->db->select('o.*, c.nomeCliente as cliente_nome, c.documento as cliente_documento');
        $this->db->from('obras o');
        $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
        $this->db->where_in('o.status', ['Contratada', 'EmExecucao', 'Paralisada', 'Orcamentacao']);
        $this->db->order_by('o.percentual_concluido', 'DESC');
        $obras = $this->db->get()->result();

        // Enriquecer dados das obras
        foreach ($obras as $obra) {
            // Contar OS vinculadas
            $this->db->where('obra_id', $obra->id);
            $obra->total_os = $this->db->count_all_results('os');

            // Contar etapas
            $obra->total_etapas = $this->obras_model->getEtapas($obra->id) ? count($this->obras_model->getEtapas($obra->id)) : 0;

            // Contar equipe
            $obra->total_equipe = $this->obras_model->getEquipe($obra->id) ? count($this->obras_model->getEquipe($obra->id)) : 0;

            // Buscar OS recentes da obra
            $this->db->select('os.idOs, os.status, os.dataInicial, c.nomeCliente');
            $this->db->from('os');
            $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
            $this->db->where('os.obra_id', $obra->id);
            $this->db->order_by('os.dataInicial', 'DESC');
            $this->db->limit(5);
            $obra->os_recentes = $this->db->get()->result();
        }

        $this->data['obras'] = $obras;
        $this->data['view'] = 'tecnicos_admin/execucao_obras';
        return $this->layout();
    }

    /**
     * Editar etapa da obra
     */
    public function editar_etapa($etapa_id)
    {
        $this->load->model('obras_model');

        // Buscar a etapa
        $this->db->where('id', $etapa_id);
        $etapa = $this->db->get('obra_etapas')->row();

        if (!$etapa) {
            echo json_encode(['success' => false, 'message' => 'Etapa não encontrada']);
            return;
        }

        if ($this->input->post()) {
            $dados = [
                'nome' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'status' => $this->input->post('status'),
                'data_inicio_prevista' => $this->input->post('data_inicio_prevista'),
                'data_fim_prevista' => $this->input->post('data_fim_prevista'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $etapa_id);
            if ($this->db->update('obra_etapas', $dados)) {
                // Se mudou o status, atualizar progresso
                if ($this->input->post('status') === 'concluida') {
                    $this->obras_model->atualizarProgresso($etapa->obra_id);
                }

                $this->session->set_flashdata('success', 'Etapa atualizada com sucesso!');
            } else {
                $this->session->set_flashdata('error', 'Erro ao atualizar etapa.');
            }

            redirect('tecnicos_admin/ver_obra/' . $etapa->obra_id);
        }
    }

    /**
     * Atualizar status da etapa via AJAX
     */
    public function atualizar_status_etapa()
    {
        $etapa_id = $this->input->post('etapa_id');
        $status = $this->input->post('status');

        if (!$etapa_id || !$status) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }

        // Buscar a etapa
        $this->db->where('id', $etapa_id);
        $etapa = $this->db->get('obra_etapas')->row();

        if (!$etapa) {
            echo json_encode(['success' => false, 'message' => 'Etapa não encontrada']);
            return;
        }

        $dados = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Se marcou como concluída, registrar data
        if ($status === 'concluida') {
            $dados['data_fim_real'] = date('Y-m-d');
        }

        $this->db->where('id', $etapa_id);
        if ($this->db->update('obra_etapas', $dados)) {
            // Atualizar progresso da obra
            $this->load->model('obras_model');
            $progresso = $this->obras_model->atualizarProgresso($etapa->obra_id);

            echo json_encode([
                'success' => true,
                'message' => 'Status atualizado',
                'progresso' => $progresso,
                'etapa_id' => $etapa_id,
                'status' => $status
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar']);
        }
    }

    /**
     * Adicionar comentário/atividade na obra
     */
    public function adicionar_comentario()
    {
        $obra_id = $this->input->post('obra_id');
        $tipo = $this->input->post('tipo') ?: 'comentario';
        $descricao = $this->input->post('descricao');

        if (!$obra_id || !$descricao) {
            $this->session->set_flashdata('error', 'Dados incompletos.');
            redirect('tecnicos_admin/ver_obra/' . $obra_id);
            return;
        }

        $dados = [
            'obra_id' => $obra_id,
            'usuario_id' => $this->session->userdata('id_admin'),
            'usuario_nome' => $this->session->userdata('nome_admin'),
            'tipo' => $tipo,
            'descricao' => $descricao,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('obra_atividades', $dados)) {
            $this->session->set_flashdata('success', 'Registro adicionado com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao adicionar registro.');
        }

        redirect('tecnicos_admin/ver_obra/' . $obra_id);
    }

    /**
     * Buscar atividades da obra (Timeline)
     */
    public function buscar_atividades_obra($obra_id)
    {
        header('Content-Type: application/json');

        if (!$obra_id) {
            echo json_encode(['success' => false, 'atividades' => []]);
            return;
        }

        try {
            // Verificar se tabela existe, se não, criar
            if (!$this->db->table_exists('obra_atividades')) {
                $this->criarTabelaAtividades();
            }

            // Buscar comentários/atividades
            $atividades = [];
            if ($this->db->table_exists('obra_atividades')) {
                $this->db->where('obra_id', $obra_id);
                $this->db->order_by('created_at', 'DESC');
                $query = $this->db->get('obra_atividades');
                $atividades = $query ? $query->result() : [];
            }

            // Buscar etapas concluídas recentemente
            $etapas = [];
            if ($this->db->table_exists('obra_etapas')) {
                $this->db->where('obra_id', $obra_id);
                $this->db->where('status', 'concluida');
                $this->db->order_by('data_fim_real', 'DESC');
                $this->db->limit(10);
                $query = $this->db->get('obra_etapas');
                $etapas = $query ? $query->result() : [];
            }

            echo json_encode([
                'success' => true,
                'atividades' => $atividades,
                'etapas_concluidas' => $etapas
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'atividades' => [],
                'etapas_concluidas' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Criar tabela de atividades se não existir
     */
    private function criarTabelaAtividades()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS obra_atividades (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                usuario_id INT NOT NULL,
                usuario_nome VARCHAR(255) NOT NULL,
                tipo VARCHAR(50) DEFAULT 'comentario',
                descricao TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_obra_id (obra_id),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $this->db->query($sql);
        } catch (Exception $e) {
            // Silenciar erro
        }
    }

    /**
     * Área do Técnico - Obras atribuídas a ele
     */
    public function minhas_obras_tecnico()
    {
        $tecnico_id = $this->session->userdata('id_admin');

        $this->load->model('obras_model');

        // Buscar obras onde o técnico está na equipe
        $this->db->select('o.*, c.nomeCliente as cliente_nome');
        $this->db->from('obras o');
        $this->db->join('obra_equipe oe', 'oe.obra_id = o.id');
        $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
        $this->db->where('oe.tecnico_id', $tecnico_id);
        $this->db->where('oe.ativo', 1);
        $this->db->where_in('o.status', ['Contratada', 'EmExecucao']);
        $this->db->group_by('o.id');
        $obras = $this->db->get()->result();

        // Enriquecer dados
        foreach ($obras as $obra) {
            $obra->minhas_os = $this->db->where(['obra_id' => $obra->id, 'tecnico_responsavel' => $tecnico_id])->count_all_results('os');
            $obra->etapas_pendentes = $this->db->where(['obra_id' => $obra->id, 'status !=' => 'concluida'])->count_all_results('obra_etapas');
        }

        $this->data['obras'] = $obras;
        $this->data['view'] = 'tecnicos_admin/tecnico_obras';
        return $this->layout();
    }

    /**
     * Técnico - Visualizar e executar etapas da obra
     */
    public function tecnico_executar_obra($obra_id)
    {
        $tecnico_id = $this->session->userdata('id_admin');

        $this->load->model('obras_model');

        // Verificar se técnico está na equipe
        $this->db->where(['obra_id' => $obra_id, 'tecnico_id' => $tecnico_id, 'ativo' => 1]);
        if (!$this->db->get('obra_equipe')->row()) {
            $this->session->set_flashdata('error', 'Você não está alocado nesta obra.');
            redirect('tecnicos_admin/minhas_obras_tecnico');
        }

        $obra = $this->obras_model->getById($obra_id);
        if (!$obra) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('tecnicos_admin/minhas_obras_tecnico');
        }

        // Buscar etapas da obra (filtrar apenas com datas definidas)
        $etapasTodas = $this->obras_model->getEtapas($obra_id);
        $etapas = array_filter($etapasTodas, function($etapa) {
            return !empty($etapa->data_inicio_prevista) && !empty($etapa->data_fim_prevista);
        });

        // Buscar minhas OS na obra
        $this->db->select('os.idOs, os.status, os.dataInicial, c.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->where('os.obra_id', $obra_id);
        $this->db->where('os.tecnico_responsavel', $tecnico_id);
        $minhas_os = $this->db->get()->result();

        $this->data['obra'] = $obra;
        $this->data['etapas'] = $etapas;
        $this->data['minhas_os'] = $minhas_os;
        $this->data['view'] = 'tecnicos_admin/tecnico_executar_obra';
        return $this->layout();
    }

    /**
     * Técnico - Atualizar progresso de etapa
     */
    public function tecnico_atualizar_etapa()
    {
        $etapa_id = $this->input->post('etapa_id');
        $percentual = $this->input->post('percentual');
        $observacao = $this->input->post('observacao');
        $tecnico_id = $this->session->userdata('id_admin');

        if (!$etapa_id || $percentual === null) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }

        // Verificar/criar tabela
        if (!$this->db->table_exists('obra_etapa_progresso')) {
            $this->criarTabelaEtapaProgresso();
        }

        // Registrar progresso
        $dados = [
            'etapa_id' => $etapa_id,
            'tecnico_id' => $tecnico_id,
            'percentual_concluido' => $percentual,
            'observacao' => $observacao,
            'data_registro' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('obra_etapa_progresso', $dados)) {
            // Se completou 100%, marcar como concluída
            if ($percentual >= 100) {
                $this->db->where('id', $etapa_id);
                $this->db->update('obra_etapas', [
                    'status' => 'concluida',
                    'percentual_concluido' => 100,
                    'data_fim_real' => date('Y-m-d')
                ]);
            } else {
                $this->db->where('id', $etapa_id);
                $this->db->update('obra_etapas', [
                    'status' => 'em_andamento',
                    'percentual_concluido' => $percentual
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Progresso atualizado!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao registrar']);
        }
    }

    /**
     * Buscar OS disponíveis para vincular (TODAS as OS do sistema)
     */
    public function buscar_os_disponiveis_simples()
    {
        header('Content-Type: application/json');
        $termo = $this->input->get('termo');

        try {
            $this->db->select('os.idOs, os.dataInicial, os.dataFinal, os.status, os.valorTotal, os.garantia, os.obra_id, c.nomeCliente, c.documento, c.telefone as cliente_telefone, o.nome as obra_vinculada');
            $this->db->from('os');
            $this->db->join('clientes c', 'c.idClientes = os.clientes_id', 'left');
            $this->db->join('obras o', 'o.id = os.obra_id', 'left');

            if ($termo) {
                $this->db->group_start();
                $this->db->like('os.idOs', $termo);
                $this->db->or_like('c.nomeCliente', $termo);
                $this->db->or_like('c.documento', $termo);
                $this->db->group_end();
            }

            $this->db->order_by('os.obra_id', 'ASC');
            $this->db->order_by('os.dataInicial', 'DESC');
            $this->db->limit(100);
            $query = $this->db->get();

            if (!$query) {
                $error = $this->db->error();
                echo json_encode(['success' => false, 'message' => 'Erro na query', 'error' => $error]);
                return;
            }

            $os = $query->result();

            // Debug: verificar se há OS no sistema
            if (empty($os)) {
                $totalOS = $this->db->count_all('os');
                echo json_encode([
                    'success' => true,
                    'os' => [],
                    'total' => 0,
                    'debug' => 'Nenhuma OS encontrada na busca. Total na tabela OS: ' . $totalOS
                ]);
                return;
            }

            // Adicionar flag indicando se já está vinculada
            foreach ($os as &$item) {
                $item->ja_vinculada = !empty($item->obra_id) && $item->obra_id > 0;
            }

            echo json_encode(['success' => true, 'os' => $os, 'total' => count($os)]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Remover técnico da equipe
     */
    public function remover_tecnico_equipe()
    {
        $obra_id = $this->input->post('obra_id');
        $tecnico_id = $this->input->post('tecnico_id');

        if (!$obra_id || !$tecnico_id) {
            $this->session->set_flashdata('error', 'Dados incompletos.');
            redirect('tecnicos_admin/ver_obra/' . $obra_id);
            return;
        }

        $this->db->where(['obra_id' => $obra_id, 'tecnico_id' => $tecnico_id]);
        if ($this->db->update('obra_equipe', ['ativo' => 0, 'data_saida' => date('Y-m-d')])) {
            $this->session->set_flashdata('success', 'Técnico removido da equipe!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao remover técnico.');
        }

        redirect('tecnicos_admin/ver_obra/' . $obra_id);
    }

    /**
     * Excluir etapa
     */
    public function excluir_etapa($etapa_id)
    {
        // Buscar a etapa
        $this->db->where('id', $etapa_id);
        $etapa = $this->db->get('obra_etapas')->row();

        if (!$etapa) {
            $this->session->set_flashdata('error', 'Etapa não encontrada.');
            redirect('tecnicos_admin/obras');
            return;
        }

        $obra_id = $etapa->obra_id;

        $this->db->where('id', $etapa_id);
        if ($this->db->delete('obra_etapas')) {
            // Atualizar progresso
            $this->load->model('obras_model');
            $this->obras_model->atualizarProgresso($obra_id);

            $this->session->set_flashdata('success', 'Etapa excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir etapa.');
        }

        redirect('tecnicos_admin/ver_obra/' . $obra_id);
    }

    /**
     * API - Dados da obra para gráficos
     */
    public function api_dados_obra($obra_id)
    {
        header('Content-Type: application/json');

        if (!$obra_id) {
            echo json_encode(['success' => false]);
            return;
        }

        $this->load->model('obras_model');

        // Estatísticas
        $etapas = $this->obras_model->getEtapas($obra_id);
        $total_etapas = count($etapas);
        $etapas_concluidas = 0;
        $etapas_andamento = 0;

        foreach ($etapas as $etapa) {
            if ($etapa->status === 'concluida') $etapas_concluidas++;
            elseif ($etapa->status === 'em_andamento') $etapas_andamento++;
        }

        // OS por status
        $this->db->where('obra_id', $obra_id);
        $os_list = $this->db->get('os')->result();
        $os_por_status = [];
        foreach ($os_list as $os) {
            $os_por_status[$os->status] = ($os_por_status[$os->status] ?? 0) + 1;
        }

        // Progresso ao longo do tempo
        $progresso_historico = [];
        if ($this->db->table_exists('obra_etapa_progresso')) {
            $this->db->where('obra_id', $obra_id);
            $this->db->order_by('data_registro', 'ASC');
            $query = $this->db->get('obra_etapa_progresso');
            $progresso_historico = $query ? $query->result() : [];
        }

        echo json_encode([
            'success' => true,
            'etapas' => [
                'total' => $total_etapas,
                'concluidas' => $etapas_concluidas,
                'andamento' => $etapas_andamento,
                'pendentes' => $total_etapas - $etapas_concluidas - $etapas_andamento
            ],
            'os_por_status' => $os_por_status,
            'progresso_historico' => $progresso_historico
        ]);
    }

    /**
     * Buscar OS disponíveis simplificado - apenas por obra
     */
    public function buscar_os_por_obra()
    {
        header('Content-Type: application/json');
        $obra_id = $this->input->get('obra_id');

        if (!$obra_id) {
            echo json_encode(['success' => false, 'message' => 'Obra não informada']);
            return;
        }

        try {
            // Buscar TODAS as OS, incluindo info se já está vinculada a outra obra
            $this->db->select('os.idOs, os.dataInicial, os.dataFinal, os.status, os.obra_id, c.nomeCliente, c.documento, o.nome as obra_vinculada');
            $this->db->from('os');
            $this->db->join('clientes c', 'c.idClientes = os.clientes_id', 'left');
            $this->db->join('obras o', 'o.id = os.obra_id', 'left');
            $this->db->order_by('os.obra_id', 'ASC');
            $this->db->order_by('os.dataInicial', 'DESC');
            $this->db->limit(100);
            $query = $this->db->get();

            if (!$query) {
                $error = $this->db->error();
                echo json_encode(['success' => false, 'message' => 'Erro na consulta', 'error' => $error]);
                return;
            }

            $os = $query->result();

            // Debug: verificar se há OS no sistema
            if (empty($os)) {
                // Verificar se a tabela os tem registros
                $totalOS = $this->db->count_all('os');
                echo json_encode([
                    'success' => true,
                    'os' => [],
                    'total' => 0,
                    'debug' => 'Nenhuma OS encontrada. Total na tabela: ' . $totalOS
                ]);
                return;
            }

            // Adicionar flag indicando se já está vinculada
            foreach ($os as &$item) {
                $item->ja_vinculada = !empty($item->obra_id) && $item->obra_id > 0;
                $item->nome_obra_vinculada = $item->obra_vinculada ?? null;
            }

            echo json_encode(['success' => true, 'os' => $os, 'total' => count($os)]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Buscar dados de uma etapa específica (AJAX)
     */
    public function buscar_etapa($etapa_id)
    {
        header('Content-Type: application/json');

        $this->db->where('id', $etapa_id);
        $etapa = $this->db->get('obra_etapas')->row();

        if ($etapa) {
            echo json_encode(['success' => true, 'etapa' => $etapa]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Etapa não encontrada']);
        }
    }

    /**
     * Buscar técnicos disponíveis para alocar na obra (AJAX)
     */
    public function buscar_tecnicos_disponiveis()
    {
        header('Content-Type: application/json');

        try {
            // Usar o Tecnicos_model para buscar técnicos (mesma lógica de tecnicos())
            $tecnicos = $this->tecnicos_model->getAll();

            // Se não encontrou pelo model, busca todos os usuários ativos
            if (empty($tecnicos)) {
                $this->db->select('idUsuarios, nome, email, telefone, nivel_tecnico, especialidades, status');
                $this->db->where('status', 1);
                $this->db->order_by('nome', 'ASC');
                $query = $this->db->get('usuarios');
                $tecnicos = $query ? $query->result() : [];
            }

            // Se ainda não encontrou, busca todos sem filtro
            if (empty($tecnicos)) {
                $this->db->select('idUsuarios, nome, email, telefone, nivel_tecnico, especialidades, status');
                $this->db->order_by('nome', 'ASC');
                $query = $this->db->get('usuarios');
                $tecnicos = $query ? $query->result() : [];
            }

            echo json_encode([
                'success' => true,
                'tecnicos' => $tecnicos,
                'total' => count($tecnicos),
                'debug' => 'Usando tecnicos_model-getAll()'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao buscar técnicos: ' . $e->getMessage(),
                'tecnicos' => [],
                'total' => 0
            ]);
        }
    }

    /**
     * Criar tabela de progresso de etapas se não existir
     */
    private function criarTabelaEtapaProgresso()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS obra_etapa_progresso (
                id INT AUTO_INCREMENT PRIMARY KEY,
                etapa_id INT NOT NULL,
                tecnico_id INT NOT NULL,
                percentual_concluido INT DEFAULT 0,
                observacao TEXT,
                data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_etapa_id (etapa_id),
                INDEX idx_tecnico_id (tecnico_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $this->db->query($sql);
        } catch (Exception $e) {
            // Silenciar erro
        }
    }

    // ============================================
    // SISTEMA DE TAREFAS DE EXECUÇÃO DAS OBRAS
    // ============================================

    /**
     * Gerenciar tarefas de uma obra
     */
    public function tarefas_obra($obra_id)
    {
        $this->load->model('obras_model');

        $obra = $this->obras_model->getById($obra_id);
        if (!$obra) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('tecnicos_admin/obras');
        }

        // Buscar tarefas da obra
        $this->db->select('t.*, u.nome as tecnico_nome');
        $this->db->from('obra_tarefas t');
        $this->db->join('usuarios u', 'u.idUsuarios = t.tecnico_id', 'left');
        $this->db->where('t.obra_id', $obra_id);
        $this->db->order_by('t.prioridade DESC, t.data_fim_prevista ASC');
        $tarefas = $this->db->get()->result();

        // Buscar técnicos disponíveis
        $this->db->where('is_tecnico', 1);
        $this->db->where('status', 1);
        $tecnicos = $this->db->get('usuarios')->result();

        // Estatísticas
        $stats = [
            'total' => count($tarefas),
            'pendentes' => count(array_filter($tarefas, function($t) { return $t->status === 'pendente'; })),
            'em_andamento' => count(array_filter($tarefas, function($t) { return $t->status === 'em_andamento'; })),
            'concluidas' => count(array_filter($tarefas, function($t) { return $t->status === 'concluida'; })),
        ];

        $this->data['obra'] = $obra;
        $this->data['tarefas'] = $tarefas;
        $this->data['tecnicos'] = $tecnicos;
        $this->data['stats'] = $stats;
        $this->data['view'] = 'tecnicos_admin/tarefas_obra';
        return $this->layout();
    }

    /**
     * Salvar nova tarefa (AJAX ou form)
     */
    public function salvar_tarefa()
    {
        $obra_id = $this->input->post('obra_id');

        if (!$obra_id) {
            $this->session->set_flashdata('error', 'Obra não informada.');
            redirect('tecnicos_admin/obras');
        }

        $this->form_validation->set_rules('titulo', 'Título', 'required|trim');
        $this->form_validation->set_rules('tecnico_id', 'Técnico', 'required');
        $this->form_validation->set_rules('data_fim_prevista', 'Data de Término', 'required');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('tecnicos_admin/tarefas_obra/' . $obra_id);
        }

        // Verificar se tabela existe
        if (!$this->db->table_exists('obra_tarefas')) {
            $this->criarTabelaTarefas();
        }

        $dados = [
            'obra_id' => $obra_id,
            'tecnico_id' => $this->input->post('tecnico_id'),
            'titulo' => $this->input->post('titulo'),
            'descricao' => $this->input->post('descricao'),
            'prioridade' => $this->input->post('prioridade') ?: 'normal',
            'status' => $this->input->post('status') ?: 'pendente',
            'data_inicio' => $this->input->post('data_inicio') ?: date('Y-m-d'),
            'data_fim_prevista' => $this->input->post('data_fim_prevista'),
            'criado_por' => $this->session->userdata('id_admin'),
            'created_at' => date('Y-m-d H:i:s'),
            'percentual_concluido' => 0
        ];

        $tarefa_id = $this->input->post('tarefa_id');

        if ($tarefa_id) {
            // Atualizar
            $this->db->where('id', $tarefa_id);
            $this->db->update('obra_tarefas', $dados);
            $this->session->set_flashdata('success', 'Tarefa atualizada com sucesso!');
        } else {
            // Inserir
            $this->db->insert('obra_tarefas', $dados);
            $this->session->set_flashdata('success', 'Tarefa criada e atribuída ao técnico!');
        }

        redirect('tecnicos_admin/tarefas_obra/' . $obra_id);
    }

    /**
     * Atualizar status da tarefa (AJAX)
     */
    public function atualizar_status_tarefa()
    {
        header('Content-Type: application/json');

        $tarefa_id = $this->input->post('tarefa_id');
        $status = $this->input->post('status');
        $percentual = $this->input->post('percentual');
        $observacao = $this->input->post('observacao');
        $horas_trabalhadas = $this->input->post('horas_trabalhadas');

        if (!$tarefa_id || !$status) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }

        // Buscar tarefa atual
        $this->db->where('id', $tarefa_id);
        $tarefa = $this->db->get('obra_tarefas')->row();

        if (!$tarefa) {
            echo json_encode(['success' => false, 'message' => 'Tarefa não encontrada']);
            return;
        }

        $dados = [
            'status' => $status,
            'percentual_concluido' => $percentual ?: 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'concluida') {
            $dados['data_fim_real'] = date('Y-m-d');
            $dados['percentual_concluido'] = 100;
        }

        $this->db->where('id', $tarefa_id);
        if ($this->db->update('obra_tarefas', $dados)) {
            // Registrar no histórico
            $this->registrarHistoricoTarefa($tarefa_id, $tarefa->status, $status, $tarefa->percentual_concluido, $percentual, $observacao, $horas_trabalhadas);

            echo json_encode(['success' => true, 'message' => 'Status atualizado!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar']);
        }
    }

    /**
     * Registrar histórico da tarefa
     */
    private function registrarHistoricoTarefa($tarefa_id, $status_anterior, $status_novo, $percentual_anterior, $percentual_novo, $descricao = '', $horas = 0)
    {
        if (!$this->db->table_exists('obra_tarefas_historico')) {
            return;
        }

        $dados = [
            'tarefa_id' => $tarefa_id,
            'tecnico_id' => $this->session->userdata('id_admin'),
            'tipo' => 'atualizacao_status',
            'descricao' => $descricao ?: "Status alterado de '{$status_anterior}' para '{$status_novo}'",
            'percentual_anterior' => $percentual_anterior ?: 0,
            'percentual_novo' => $percentual_novo ?: 0,
            'horas_trabalhadas' => $horas ?: 0,
            'data_registro' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('obra_tarefas_historico', $dados);
    }

    /**
     * Excluir tarefa
     */
    public function excluir_tarefa($tarefa_id)
    {
        $this->db->where('id', $tarefa_id);
        $tarefa = $this->db->get('obra_tarefas')->row();

        if (!$tarefa) {
            $this->session->set_flashdata('error', 'Tarefa não encontrada.');
            redirect('tecnicos_admin/obras');
        }

        $this->db->where('id', $tarefa_id);
        if ($this->db->delete('obra_tarefas')) {
            $this->session->set_flashdata('success', 'Tarefa excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir tarefa.');
        }

        redirect('tecnicos_admin/tarefas_obra/' . $tarefa->obra_id);
    }

    /**
     * Buscar tarefas do técnico (para portal do técnico)
     */
    public function buscar_tarefas_tecnico($tecnico_id = null)
    {
        header('Content-Type: application/json');

        if (!$tecnico_id) {
            $tecnico_id = $this->session->userdata('id_admin');
        }

        $obra_id = $this->input->get('obra_id');
        $status = $this->input->get('status');

        $this->db->select('t.*, o.nome as obra_nome, o.codigo as obra_codigo, c.nomeCliente as cliente_nome');
        $this->db->from('obra_tarefas t');
        $this->db->join('obras o', 'o.id = t.obra_id');
        $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
        $this->db->where('t.tecnico_id', $tecnico_id);

        if ($obra_id) {
            $this->db->where('t.obra_id', $obra_id);
        }

        if ($status) {
            $this->db->where('t.status', $status);
        } else {
            $this->db->where_in('t.status', ['pendente', 'em_andamento']);
        }

        $this->db->order_by('t.prioridade DESC, t.data_fim_prevista ASC');
        $tarefas = $this->db->get()->result();

        echo json_encode(['success' => true, 'tarefas' => $tarefas]);
    }

    /**
     * Ver detalhes da tarefa
     */
    public function ver_tarefa($tarefa_id)
    {
        header('Content-Type: application/json');

        $this->db->select('t.*, u.nome as tecnico_nome, o.nome as obra_nome, o.codigo as obra_codigo');
        $this->db->from('obra_tarefas t');
        $this->db->join('usuarios u', 'u.idUsuarios = t.tecnico_id', 'left');
        $this->db->join('obras o', 'o.id = t.obra_id');
        $this->db->where('t.id', $tarefa_id);
        $tarefa = $this->db->get()->row();

        if (!$tarefa) {
            echo json_encode(['success' => false, 'message' => 'Tarefa não encontrada']);
            return;
        }

        // Buscar histórico
        $this->db->where('tarefa_id', $tarefa_id);
        $this->db->order_by('data_registro', 'DESC');
        $historico = $this->db->get('obra_tarefas_historico')->result();

        echo json_encode(['success' => true, 'tarefa' => $tarefa, 'historico' => $historico]);
    }

    /**
     * Relatório de atividades do dia
     */
    public function relatorio_atividades_dia()
    {
        $data = $this->input->get('data') ?: date('Y-m-d');
        $tecnico_id = $this->input->get('tecnico_id');
        $obra_id = $this->input->get('obra_id');

        // Buscar atividades do histórico
        $this->db->select('h.*, t.titulo as tarefa_titulo, o.nome as obra_nome, o.codigo as obra_codigo, u.nome as tecnico_nome');
        $this->db->from('obra_tarefas_historico h');
        $this->db->join('obra_tarefas t', 't.id = h.tarefa_id');
        $this->db->join('obras o', 'o.id = t.obra_id');
        $this->db->join('usuarios u', 'u.idUsuarios = h.tecnico_id', 'left');
        $this->db->where('DATE(h.data_registro)', $data);

        if ($tecnico_id) {
            $this->db->where('h.tecnico_id', $tecnico_id);
        }

        if ($obra_id) {
            $this->db->where('t.obra_id', $obra_id);
        }

        $this->db->order_by('h.data_registro', 'DESC');
        $atividades = $this->db->get()->result();

        // Calcular total de horas
        $total_horas = array_sum(array_map(function($a) { return $a->horas_trabalhadas; }, $atividades));

        // Buscar técnicos para filtro
        $this->db->where('is_tecnico', 1);
        $this->db->where('status', 1);
        $tecnicos = $this->db->get('usuarios')->result();

        // Buscar obras para filtro
        $this->db->where_in('status', ['Contratada', 'EmExecucao']);
        $obras = $this->db->get('obras')->result();

        $this->data['atividades'] = $atividades;
        $this->data['total_horas'] = $total_horas;
        $this->data['data'] = $data;
        $this->data['tecnicos'] = $tecnicos;
        $this->data['obras'] = $obras;
        $this->data['tecnico_id'] = $tecnico_id;
        $this->data['obra_id'] = $obra_id;
        $this->data['view'] = 'tecnicos_admin/relatorio_atividades_dia';
        return $this->layout();
    }

    /**
     * Criar tabela de tarefas se não existir
     */
    private function criarTabelaTarefas()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS obra_tarefas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                tecnico_id INT,
                titulo VARCHAR(255) NOT NULL,
                descricao TEXT,
                status ENUM('pendente', 'em_andamento', 'pausada', 'concluida', 'cancelada') DEFAULT 'pendente',
                prioridade ENUM('baixa', 'normal', 'alta', 'urgente') DEFAULT 'normal',
                data_inicio DATE,
                data_fim_prevista DATE,
                data_fim_real DATE,
                percentual_concluido INT DEFAULT 0,
                observacoes TEXT,
                criado_por INT,
                created_at DATETIME,
                updated_at DATETIME,
                INDEX idx_obra_id (obra_id),
                INDEX idx_tecnico_id (tecnico_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $this->db->query($sql);

            $sql2 = "CREATE TABLE IF NOT EXISTS obra_tarefas_historico (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tarefa_id INT NOT NULL,
                tecnico_id INT,
                tipo VARCHAR(50) NOT NULL,
                descricao TEXT NOT NULL,
                percentual_anterior INT DEFAULT 0,
                percentual_novo INT DEFAULT 0,
                horas_trabalhadas DECIMAL(5,2) DEFAULT 0.00,
                data_registro DATETIME,
                localizacao_lat DECIMAL(10,8),
                localizacao_lng DECIMAL(11,8),
                INDEX idx_tarefa_id (tarefa_id),
                INDEX idx_tecnico_id (tecnico_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $this->db->query($sql2);
        } catch (Exception $e) {
            // Silenciar erro
        }
    }

    /**
     * API - Atualizar progresso da tarefa pelo técnico
     */
    public function api_atualizar_tarefa_tecnico()
    {
        header('Content-Type: application/json');

        $tarefa_id = $this->input->post('tarefa_id');
        $percentual = $this->input->post('percentual');
        $observacao = $this->input->post('observacao');
        $horas_trabalhadas = $this->input->post('horas_trabalhadas');
        $lat = $this->input->post('latitude');
        $lng = $this->input->post('longitude');

        if (!$tarefa_id || $percentual === null) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }

        $tecnico_id = $this->session->userdata('id_admin');

        // Verificar se tarefa existe e pertence ao técnico
        $this->db->where('id', $tarefa_id);
        $tarefa = $this->db->get('obra_tarefas')->row();

        if (!$tarefa) {
            echo json_encode(['success' => false, 'message' => 'Tarefa nao encontrada']);
            return;
        }

        if ($tarefa->technico_id != $tecnico_id) {
            echo json_encode(['success' => false, 'message' => 'Tarefa não atribuída a você']);
            return;
        }

        // Determinar novo status
        $status = $tarefa->status;
        if ($percentual == 0) {
            $status = 'pendente';
        } elseif ($percentual >= 100) {
            $status = 'concluida';
            $percentual = 100;
        } elseif ($percentual > 0) {
            $status = 'em_andamento';
        }

        $dados = [
            'percentual_concluido' => $percentual,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'concluida') {
            $dados['data_fim_real'] = date('Y-m-d');
        }

        $this->db->where('id', $tarefa_id);
        if ($this->db->update('obra_tarefas', $dados)) {
            // Registrar histórico
            $dados_historico = [
                'tarefa_id' => $tarefa_id,
                'tecnico_id' => $tecnico_id,
                'tipo' => 'atualizacao_progresso',
                'descricao' => $observacao ?: 'Progresso atualizado pelo técnico',
                'percentual_anterior' => $tarefa->percentual_concluido ?: 0,
                'percentual_novo' => $percentual,
                'horas_trabalhadas' => $horas_trabalhadas ?: 0,
                'data_registro' => date('Y-m-d H:i:s'),
                'localizacao_lat' => $lat,
                'localizacao_lng' => $lng
            ];
            $this->db->insert('obra_tarefas_historico', $dados_historico);

            echo json_encode([
                'success' => true,
                'message' => 'Tarefa atualizada com sucesso!',
                'status' => $status,
                'percentual' => $percentual
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar tarefa']);
        }
    }
}
