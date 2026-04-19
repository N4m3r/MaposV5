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
        if (!$obra_id) {
            echo json_encode(['success' => false, 'atividades' => []]);
            return;
        }

        // Buscar comentários/atividades
        $this->db->where('obra_id', $obra_id);
        $this->db->order_by('created_at', 'DESC');
        $atividades = $this->db->get('obra_atividades')->result();

        // Buscar etapas concluídas recentemente
        $this->db->where('obra_id', $obra_id);
        $this->db->where('status', 'concluida');
        $this->db->order_by('data_fim_real', 'DESC');
        $this->db->limit(10);
        $etapas = $this->db->get('obra_etapas')->result();

        echo json_encode([
            'success' => true,
            'atividades' => $atividades,
            'etapas_concluidas' => $etapas
        ]);
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

        // Buscar etapas da obra
        $etapas = $this->obras_model->getEtapas($obra_id);

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
     * Buscar OS disponíveis para vincular (todas as OS sem obra)
     */
    public function buscar_os_disponiveis_simples()
    {
        $termo = $this->input->get('termo');

        $this->db->select('os.idOs, os.dataInicial, os.status, c.nomeCliente, c.documento');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->where('(os.obra_id IS NULL OR os.obra_id = 0)');

        if ($termo) {
            $this->db->like('os.idOs', $termo);
            $this->db->or_like('c.nomeCliente', $termo);
        }

        $this->db->order_by('os.dataInicial', 'DESC');
        $this->db->limit(50);
        $query = $this->db->get();
        $os = $query ? $query->result() : [];

        echo json_encode(['success' => true, 'os' => $os]);
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
        $this->db->where('obra_id', $obra_id);
        $this->db->order_by('data_registro', 'ASC');
        $progresso_historico = $this->db->get('obra_etapa_progresso')->result();

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
        return $this->layout();
    }

    /**
     * Buscar OS disponíveis simplificado - apenas por obra
     */
    public function buscar_os_por_obra()
    {
        $this->load->model('os_model');
        $obra_id = $this->input->get('obra_id');

        if (!$obra_id) {
            echo json_encode(['success' => false, 'message' => 'Obra não informada']);
            return;
        }

        // Buscar OS que não estão vinculadas a nenhuma obra
        $this->db->select('os.idOs, os.dataInicial, os.dataFinal, os.status, c.nomeCliente, c.documento');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->where('(os.obra_id IS NULL OR os.obra_id = 0)');
        $this->db->order_by('os.dataInicial', 'DESC');
        $this->db->limit(50);
        $query = $this->db->get();
        $os = $query ? $query->result() : [];

        echo json_encode(['success' => true, 'os' => $os, 'total' => count($os)]);
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
}
