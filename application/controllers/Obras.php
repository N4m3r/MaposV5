<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller de Gestão de Obras (Área Administrativa)
 */
class Obras extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('obras_model');
        $this->load->model('obra_atividades_model');

        // Carregar models opcionais apenas se existirem
        if (file_exists(APPPATH . 'models/obra_checkins_model.php')) {
            $this->load->model('obra_checkins_model');
        }
        if (file_exists(APPPATH . 'models/obra_cliente_model.php')) {
            $this->load->model('obra_cliente_model');
        }

        $this->data['menuObras'] = 'Obras';
    }

    // ============================================
    // CRUD BÁSICO
    // ============================================

    /**
     * Listar obras
     */
    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->load->library('pagination');
        $this->load->model('mapos_model');

        // Filtros
        $filtros = [];
        $status = $this->input->get('status');
        $cliente = $this->input->get('cliente');

        if ($status) {
            $filtros['status'] = $status;
        }
        if ($cliente) {
            $filtros['cliente_id'] = $cliente;
        }

        // Configuração de paginação
        $config['base_url'] = site_url('obras/gerenciar');
        $config['total_rows'] = count($this->obras_model->getAll($filtros));
        $config['per_page'] = $this->data['configuration']['per_page'] ?? 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';

        $this->pagination->initialize($config);

        $this->data['results'] = $this->obras_model->getAll($filtros, $config['per_page'], $this->uri->segment(3));
        $this->data['view'] = 'obras/obras_list';

        return $this->layout();
    }

    /**
     * Adicionar obra
     */
    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar obras.');
            redirect('obras');
        }

        $this->load->library('form_validation');
        $this->load->model('clientes_model');
        $this->load->model('usuarios_model');

        $this->data['clientes'] = $this->clientes_model->getAll();
        $this->data['tecnicos'] = $this->usuarios_model->getAll();

        if ($this->input->post()) {
            $this->form_validation->set_rules('nome', 'Nome', 'required|trim');
            $this->form_validation->set_rules('cliente_id', 'Cliente', 'required|numeric');

            if ($this->form_validation->run() == TRUE) {
                // Converter valor do contrato de formato brasileiro para formato do banco
                $valor_contrato = $this->input->post('valor_contrato');
                if (!empty($valor_contrato)) {
                    // Remove pontos de milhar e substitui vírgula por ponto
                    $valor_contrato = str_replace('.', '', $valor_contrato);
                    $valor_contrato = str_replace(',', '.', $valor_contrato);
                } else {
                    $valor_contrato = null;
                }

                $dados = [
                    'nome' => $this->input->post('nome'),
                    'cliente_id' => $this->input->post('cliente_id'),
                    'tipo_obra' => $this->input->post('tipo_obra') ?? 'Outro',
                    'endereco' => $this->input->post('endereco'),
                    'bairro' => $this->input->post('bairro'),
                    'cidade' => $this->input->post('cidade'),
                    'estado' => $this->input->post('estado'),
                    'cep' => $this->input->post('cep'),
                    'data_inicio_contrato' => $this->input->post('data_inicio'),
                    'data_fim_prevista' => $this->input->post('data_previsao_fim'),
                    'observacoes' => $this->input->post('observacoes'),
                    'valor_contrato' => $valor_contrato,
                    'gestor_id' => $this->input->post('gestor_id'),
                    'responsavel_tecnico_id' => $this->input->post('responsavel_tecnico_id'),
                    'status' => 'Prospeccao',
                ];

                $obra_id = $this->obras_model->add($dados);

                if ($obra_id) {
                    log_info('Obra adicionada. ID: ' . $obra_id);
                    $this->session->set_flashdata('success', 'Obra adicionada com sucesso!');
                    redirect('obras/visualizar/' . $obra_id);
                } else {
                    $this->session->set_flashdata('error', 'Erro ao adicionar obra.');
                }
            }
        }

        $this->data['view'] = 'obras/obra_form';
        return $this->layout();
    }

    /**
     * Editar obra
     */
    public function editar($id)
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar obras.');
            redirect('obras');
        }

        $this->load->library('form_validation');
        $this->load->model('clientes_model');
        $this->load->model('usuarios_model');

        $obra_id = $this->uri->segment(3);
        $this->data['result'] = $this->obras_model->getByIdCompleto($obra_id);

        if (!$this->data['result']) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        $this->data['clientes'] = $this->clientes_model->getAll();
        $this->data['tecnicos'] = $this->usuarios_model->getAll();

        if ($this->input->post()) {
            $this->form_validation->set_rules('nome', 'Nome', 'required|trim');

            if ($this->form_validation->run() == TRUE) {
                // Converter valor do contrato de formato brasileiro para formato do banco
                $valor_contrato = $this->input->post('valor_contrato');
                if (!empty($valor_contrato)) {
                    // Remove pontos de milhar e substitui vírgula por ponto
                    $valor_contrato = str_replace('.', '', $valor_contrato);
                    $valor_contrato = str_replace(',', '.', $valor_contrato);
                } else {
                    $valor_contrato = null;
                }

                $dados = [
                    'nome' => $this->input->post('nome'),
                    'tipo_obra' => $this->input->post('tipo_obra'),
                    'endereco' => $this->input->post('endereco'),
                    'bairro' => $this->input->post('bairro'),
                    'cidade' => $this->input->post('cidade'),
                    'estado' => $this->input->post('estado'),
                    'cep' => $this->input->post('cep'),
                    'data_inicio_contrato' => $this->input->post('data_inicio'),
                    'data_fim_prevista' => $this->input->post('data_previsao_fim'),
                    'observacoes' => $this->input->post('observacoes'),
                    'status' => $this->input->post('status'),
                    'valor_contrato' => $valor_contrato,
                    'gestor_id' => $this->input->post('gestor_id'),
                    'responsavel_tecnico_id' => $this->input->post('responsavel_tecnico_id'),
                ];

                // Apenas incluir visivel_cliente se o campo existir no POST
                if ($this->input->post('visivel_cliente') !== null) {
                    $dados['visivel_cliente'] = $this->input->post('visivel_cliente') ? 1 : 0;
                }

                // Log para debug
                log_message('debug', 'Editar obra ID ' . $obra_id . ' - Dados: ' . print_r($dados, true));

                if ($this->obras_model->update($obra_id, $dados)) {
                    log_info('Obra atualizada. ID: ' . $obra_id);
                    $this->session->set_flashdata('success', 'Obra atualizada com sucesso!');
                    redirect('obras/visualizar/' . $obra_id);
                } else {
                    $this->session->set_flashdata('error', 'Erro ao atualizar obra.');
                }
            }
        }

        $this->data['view'] = 'obras/obra_form';
        return $this->layout();
    }

    /**
     * Visualizar obra (Dashboard)
     */
    public function visualizar($id = null)
    {
        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        $this->load->model('mapos_model');
        $this->load->model('usuarios_model');

        $this->data['obra'] = $this->obras_model->getById($id);

        if (!$this->data['obra']) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        $this->data['etapas'] = $this->obras_model->getEtapasComEstatisticas($id);
        $this->data['equipe'] = $this->obras_model->getEquipe($id);
        $this->data['atividades_recentes'] = $this->obra_atividades_model->getByObra($id, [], 10);

        $this->data['view'] = 'obras/obra_view';
        return $this->layout();
    }

    /**
     * Excluir obra
     */
    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir obras.');
            redirect('obras');
        }

        $id = $this->input->post('id');

        if ($id && is_numeric($id)) {
            if ($this->obras_model->delete($id)) {
                log_info('Obra excluída. ID: ' . $id);
                $this->session->set_flashdata('success', 'Obra excluída com sucesso!');
            } else {
                $this->session->set_flashdata('error', 'Erro ao excluir obra.');
            }
        }

        redirect('obras');
    }

    // ============================================
    // ETAPAS
    // ============================================

    /**
     * Listar etapas da obra
     */
    public function etapas($obra_id)
    {
        if (!$obra_id || !is_numeric($obra_id)) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        $this->data['obra'] = $this->obras_model->getById($obra_id);
        $this->data['etapas'] = $this->obras_model->getEtapas($obra_id);
        $this->data['view'] = 'obras/etapas_list';

        return $this->layout();
    }

    /**
     * Adicionar etapa
     */
    public function adicionarEtapa()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')) {
            $this->session->set_flashdata('error', 'Sem permissão para adicionar etapas.');
            redirect('obras');
        }

        $obra_id = $this->input->post('obra_id');
        $nome = $this->input->post('nome');

        if (!$obra_id) {
            $this->session->set_flashdata('error', 'ID da obra não informado.');
            redirect('obras');
        }

        if (!$nome) {
            $this->session->set_flashdata('error', 'Nome da etapa é obrigatório.');
            redirect('obras/etapas/' . $obra_id);
        }

        $dados = [
            'obra_id' => $obra_id,
            'numero_etapa' => $this->input->post('numero_etapa') ?: 1,
            'nome' => $nome,
            'descricao' => $this->input->post('descricao'),
            'especialidade' => $this->input->post('especialidade'),
            'data_inicio_prevista' => $this->input->post('data_inicio_prevista'),
            'data_fim_prevista' => $this->input->post('data_fim_prevista'),
        ];

        $etapa_id = $this->obras_model->adicionarEtapa($obra_id, $dados);

        if ($etapa_id) {
            $this->session->set_flashdata('success', 'Etapa "' . $nome . '" adicionada com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao adicionar etapa. Verifique os dados e tente novamente.');
        }

        redirect('obras/etapas/' . $obra_id);
    }

    // ============================================
    // EQUIPE
    // ============================================

    /**
     * Gerenciar equipe da obra
     */
    public function equipe($obra_id)
    {
        if (!$obra_id || !is_numeric($obra_id)) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        $this->load->model('usuarios_model');

        $this->data['obra'] = $this->obras_model->getById($obra_id);
        $this->data['equipe'] = $this->obras_model->getEquipe($obra_id);
        $this->data['tecnicos'] = $this->usuarios_model->getAll();
        $this->data['view'] = 'obras/equipe';

        return $this->layout();
    }

    /**
     * Adicionar técnico à equipe
     */
    public function adicionarTecnico()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('obras');
        }

        $obra_id = $this->input->post('obra_id');
        $tecnico_id = $this->input->post('tecnico_id');
        $funcao = $this->input->post('funcao') ?: 'Técnico';

        if (!$obra_id || !$tecnico_id) {
            $this->session->set_flashdata('error', 'Dados incompletos.');
            redirect('obras/equipe/' . $obra_id);
        }

        // Verificar se técnico já está na equipe
        if ($this->obras_model->tecnicoNaEquipe($obra_id, $tecnico_id)) {
            $this->session->set_flashdata('error', 'Técnico já está na equipe desta obra.');
            redirect('obras/equipe/' . $obra_id);
        }

        if ($this->obras_model->adicionarTecnicoEquipe($obra_id, $tecnico_id, $funcao)) {
            $this->session->set_flashdata('success', 'Técnico adicionado à equipe com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao adicionar técnico à equipe.');
        }

        redirect('obras/equipe/' . $obra_id);
    }

    /**
     * Remover técnico da equipe
     */
    public function removerTecnico($equipe_id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('obras');
        }

        if (!$equipe_id || !is_numeric($equipe_id)) {
            $this->session->set_flashdata('error', 'Registro não encontrado.');
            redirect('obras');
        }

        // Buscar informação da equipe para redirecionar após remover
        $this->db->where('id', $equipe_id);
        $query = $this->db->get('obra_equipe');
        $equipe = $query ? $query->row() : null;

        if ($this->obras_model->removerTecnicoEquipe($equipe_id)) {
            $this->session->set_flashdata('success', 'Técnico removido da equipe com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao remover técnico da equipe.');
        }

        redirect('obras/equipe/' . ($equipe ? $equipe->obra_id : 'gerenciar'));
    }

    // ============================================
    // ATIVIDADES
    // ============================================

    /**
     * Listar atividades da obra
     */
    public function atividades($obra_id)
    {
        if (!$obra_id || !is_numeric($obra_id)) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        $this->load->model('usuarios_model');

        $this->data['obra'] = $this->obras_model->getById($obra_id);

        if (!$this->data['obra']) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        // Log para debug
        log_message('debug', 'Atividades - Obra ID: ' . $obra_id);
        log_message('debug', 'Atividades - Tabela existe: ' . ($this->db->table_exists('obra_atividades') ? 'Sim' : 'Nao'));

        $this->data['atividades'] = $this->obra_atividades_model->getByObra($obra_id, [], 50);

        // Log do resultado
        log_message('debug', 'Atividades - Total encontradas: ' . count($this->data['atividades']));

        $this->data['tecnicos'] = $this->usuarios_model->getAll();
        $this->data['etapas'] = $this->obras_model->getEtapas($obra_id);
        $this->data['view'] = 'obras/atividades_list';

        return $this->layout();
    }

    /**
     * Adicionar atividade
     */
    public function adicionarAtividade()
    {
        $obra_id = $this->input->post('obra_id');

        // Debug: verificar se tabela existe
        if (!$this->db->table_exists('obra_atividades')) {
            log_message('error', 'Tabela obra_atividades nao existe');
            $this->session->set_flashdata('error', 'Erro: Tabela de atividades nao existe. Execute as migracoes.');
            redirect('obras/atividades/' . $obra_id);
            return;
        }

        $dados = [
            'obra_id' => $obra_id,
            'etapa_id' => $this->input->post('etapa_id') ?: null,
            'tecnico_id' => $this->input->post('tecnico_id') ?: null,
            'data_atividade' => $this->input->post('data_atividade'),
            'titulo' => $this->input->post('titulo'),
            'descricao' => $this->input->post('descricao'),
            'tipo' => $this->input->post('tipo') ?: 'trabalho',
            'visivel_cliente' => $this->input->post('visivel_cliente') ? 1 : 0,
        ];

        // Validar dados obrigatorios
        if (empty($dados['titulo'])) {
            $this->session->set_flashdata('error', 'Erro: Titulo da atividade e obrigatorio.');
            redirect('obras/atividades/' . $obra_id);
            return;
        }

        if (empty($dados['data_atividade'])) {
            $this->session->set_flashdata('error', 'Erro: Data da atividade e obrigatoria.');
            redirect('obras/atividades/' . $obra_id);
            return;
        }

        $atividade_id = $this->obra_atividades_model->add($dados);

        if ($atividade_id) {
            $this->session->set_flashdata('success', 'Atividade adicionada com sucesso!');
        } else {
            $error = $this->db->error();
            log_message('error', 'Erro ao adicionar atividade: ' . print_r($error, true));
            $this->session->set_flashdata('error', 'Erro ao adicionar atividade: ' . ($error['message'] ?? 'Erro desconhecido'));
        }

        redirect('obras/atividades/' . $obra_id);
    }

    /**
     * Verificar atividades no banco (debug)
     */
    public function verificarAtividades($obra_id)
    {
        if (!$obra_id || !is_numeric($obra_id)) {
            echo 'Obra ID inválido';
            return;
        }

        echo '<h2>Verificação de Atividades - Obra ID: ' . $obra_id . '</h2>';
        echo '<hr>';

        // Verificar se tabela existe
        $tabela_existe = $this->db->table_exists('obra_atividades');
        echo '<p><strong>Tabela obra_atividades existe:</strong> ' . ($tabela_existe ? 'SIM' : 'NÃO') . '</p>';

        if (!$tabela_existe) {
            echo '<p style="color: red;">A tabela não existe! Crie as tabelas primeiro.</p>';
            echo '<a href="' . site_url('diagnostico/obras') . '">Ir para Diagnóstico</a>';
            return;
        }

        // Mostrar estrutura da tabela
        echo '<h3>Estrutura da tabela:</h3>';
        echo '<pre>';
        $fields = $this->db->list_fields('obra_atividades');
        print_r($fields);
        echo '</pre>';

        // Contar total de atividades
        $total = $this->db->where('obra_id', $obra_id)->count_all_results('obra_atividades');
        echo '<p><strong>Total de atividades nesta obra:</strong> ' . $total . '</p>';

        // Query direta
        $query = $this->db->where('obra_id', $obra_id)->get('obra_atividades');
        echo '<p><strong>Query executada:</strong> ' . $this->db->last_query() . '</p>';

        if ($total > 0) {
            echo '<h3>Atividades encontradas:</h3>';
            echo '<table border="1" cellpadding="10">';
            echo '<tr><th>ID</th><th>Título</th><th>Data</th><th>Status</th><th>Tipo</th></tr>';
            foreach ($query->result() as $ativ) {
                echo '<tr>';
                echo '<td>' . ($ativ->id ?? 'N/A') . '</td>';
                echo '<td>' . ($ativ->titulo ?? $ativ->descricao ?? 'Sem título') . '</td>';
                echo '<td>' . ($ativ->data_atividade ?? 'N/A') . '</td>';
                echo '<td>' . ($ativ->status ?? 'N/A') . '</td>';
                echo '<td>' . ($ativ->tipo ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p style="color: orange;">Nenhuma atividade encontrada para esta obra.</p>';
        }

        echo '<hr>';
        echo '<a href="' . site_url('obras/atividades/' . $obra_id) . '" class="btn">Voltar para Atividades</a>';
    }

    /**
     * Wizard - Salvar etapa e atividades
     */
    public function salvarWizard($obra_id)
    {
        if (!$obra_id || !is_numeric($obra_id)) {
            $this->session->set_flashdata('error', 'Obra invalida.');
            redirect('obras');
        }

        // Verificar se tabelas existem
        if (!$this->db->table_exists('obra_etapas')) {
            $this->session->set_flashdata('error', 'Tabela obra_etapas nao existe. Execute o diagnostico primeiro.');
            redirect('obras/visualizar/' . $obra_id);
        }

        $this->db->trans_start();

        try {
            // 1. Salvar a etapa
            $etapa_data = [
                'obra_id' => $obra_id,
                'numero_etapa' => $this->input->post('etapa_numero'),
                'nome' => $this->input->post('etapa_nome'),
                'descricao' => $this->input->post('etapa_descricao'),
                'data_inicio_prevista' => $this->input->post('etapa_data_inicio') ?: null,
                'data_fim_prevista' => $this->input->post('etapa_data_fim') ?: null,
                'status' => 'pendente',
                'ativo' => 1
            ];

            $this->db->insert('obra_etapas', $etapa_data);
            $etapa_id = $this->db->insert_id();

            if (!$etapa_id) {
                throw new Exception('Erro ao criar etapa');
            }

            // 2. Salvar atividades (se houver)
            $atividades = $this->input->post('atividades');
            $total_atividades = 0;

            if (!empty($atividades) && is_array($atividades)) {
                foreach ($atividades as $atividade) {
                    if (!empty($atividade['titulo'])) {
                        $ativ_data = [
                            'obra_id' => $obra_id,
                            'etapa_id' => $etapa_id,
                            'titulo' => $atividade['titulo'],
                            'tipo' => $atividade['tipo'] ?? 'trabalho',
                            'status' => 'agendada',
                            'data_atividade' => date('Y-m-d'),
                            'ativo' => 1
                        ];

                        $this->db->insert('obra_atividades', $ativ_data);
                        $total_atividades++;
                    }
                }
            }

            $this->db->trans_complete();

            $this->session->set_flashdata('success',
                'Etapa criada com sucesso! ' .
                ($total_atividades > 0 ? "{$total_atividades} atividade(s) adicionada(s)." : '')
            );

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Erro no wizard: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Erro ao salvar: ' . $e->getMessage());
        }

        redirect('obras/visualizar/' . $obra_id);
    }

    /**
     * Visualizar atividade
     */
    public function visualizarAtividade($atividade_id)
    {
        if (!$atividade_id || !is_numeric($atividade_id)) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras');
        }

        $this->data['atividade'] = $this->obra_atividades_model->getById($atividade_id);

        if (!$this->data['atividade']) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras');
        }

        $this->data['obra'] = $this->obras_model->getById($this->data['atividade']->obra_id);
        $this->data['historico'] = $this->obra_atividades_model->getHistorico($atividade_id);
        $this->data['checkins'] = $this->obra_checkins_model->getByAtividade($atividade_id);
        $this->data['view'] = 'obras/atividade_view';

        return $this->layout();
    }

    // ============================================
    // RELATÓRIOS
    // ============================================

    /**
     * Relatório de progresso
     */
    public function relatorioProgresso($obra_id)
    {
        if (!$obra_id || !is_numeric($obra_id)) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        $this->load->model('mapos_model');

        $this->data['obra'] = $this->obras_model->getByIdCompleto($obra_id);
        $this->data['etapas'] = $this->obras_model->getEtapasComEstatisticas($obra_id);
        $this->data['estatisticas'] = $this->obra_atividades_model->getEstatisticas($obra_id);
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['view'] = 'obras/relatorios/progresso';

        return $this->layout();
    }

    /**
     * Relatório diário
     */
    public function relatorioDiario($obra_id)
    {
        if (!$obra_id || !is_numeric($obra_id)) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras');
        }

        $data = $this->input->get('data') ?: date('Y-m-d');

        $this->data['obra'] = $this->obras_model->getById($obra_id);
        $this->data['data_relatorio'] = $data;
        $this->data['atividades'] = $this->obra_atividades_model->getByObra($obra_id, [
            'data_inicio' => $data,
            'data_fim' => $data
        ]);
        $this->data['view'] = 'obras/relatorios/diario';

        return $this->layout();
    }

    // ============================================
    // API AJAX
    // ============================================

    /**
     * API: Atualizar progresso da obra
     */
    public function api_atualizarProgresso($obra_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $progresso = $this->obras_model->atualizarProgresso($obra_id);

        echo json_encode([
            'success' => $progresso !== false,
            'progresso' => $progresso
        ]);
    }

    /**
     * API: Dados para gráfico
     */
    public function api_dadosGrafico($obra_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $etapas = $this->obras_model->getEtapas($obra_id);
        $dados = [];

        foreach ($etapas as $etapa) {
            $dados[] = [
                'nome' => $etapa->nome,
                'progresso' => $etapa->percentual_concluido
            ];
        }

        echo json_encode($dados);
    }

    /**
     * API: Buscar dados do cliente
     */
    public function api_getCliente($cliente_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->model('clientes_model');
        $cliente = $this->clientes_model->getById($cliente_id);

        if ($cliente) {
            echo json_encode([
                'success' => true,
                'cliente' => [
                    'id' => $cliente->idClientes,
                    'nome' => $cliente->nomeCliente,
                    'documento' => $cliente->documento,
                    'endereco' => $cliente->rua,
                    'numero' => $cliente->numero,
                    'bairro' => $cliente->bairro,
                    'cidade' => $cliente->cidade,
                    'estado' => $cliente->estado,
                    'cep' => $cliente->cep
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cliente não encontrado']);
        }
    }
}
