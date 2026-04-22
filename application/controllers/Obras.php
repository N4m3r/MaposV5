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

        // Atividades do novo sistema (Hora Início/Fim)
        $this->load->model('Atividades_model', 'atividades_novas');
        $this->data['atividades_sistema'] = $this->atividades_novas->listarPorObra($id, [], 20);

        // Estatísticas das atividades do novo sistema
        $this->data['estatisticas_atividades'] = $this->atividades_novas->getEstatisticasPorObra($id);

        // Tipos de atividades para iniciar trabalho
        $this->load->model('Atividades_tipos_model');
        $this->data['tipos_atividades'] = $this->Atividades_tipos_model->listarPorCategoria();

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

    /**
     * Editar etapa
     */
    public function editarEtapa($etapa_id)
    {
        if (!$etapa_id || !is_numeric($etapa_id)) {
            $this->session->set_flashdata('error', 'ID da etapa inválido.');
            redirect('obras');
            return;
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')) {
            $this->session->set_flashdata('error', 'Sem permissão para editar etapas.');
            redirect('obras');
            return;
        }

        // Buscar etapa
        $etapa = $this->obras_model->getEtapaById($etapa_id);
        if (!$etapa) {
            $this->session->set_flashdata('error', 'Etapa não encontrada.');
            redirect('obras');
            return;
        }

        $obra_id = $etapa->obra_id;

        // Processar formulário
        if ($this->input->post()) {
            $dados = [
                'numero_etapa' => $this->input->post('numero_etapa') ?: 1,
                'nome' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'especialidade' => $this->input->post('especialidade'),
                'data_inicio_prevista' => $this->input->post('data_inicio_prevista'),
                'data_fim_prevista' => $this->input->post('data_fim_prevista'),
                'status' => $this->input->post('status') ?: 'NaoIniciada',
            ];

            if (!$dados['nome']) {
                $this->session->set_flashdata('error', 'Nome da etapa é obrigatório.');
                redirect('obras/editarEtapa/' . $etapa_id);
                return;
            }

            $result = $this->obras_model->atualizarEtapa($etapa_id, $dados);

            if ($result) {
                $this->session->set_flashdata('success', 'Etapa atualizada com sucesso!');
                redirect('obras/etapas/' . $obra_id);
            } else {
                $this->session->set_flashdata('error', 'Erro ao atualizar etapa.');
                redirect('obras/editarEtapa/' . $etapa_id);
            }
            return;
        }

        $this->data['obra'] = $this->obras_model->getById($obra_id);
        $this->data['etapa'] = $etapa;
        $this->data['etapas'] = $this->obras_model->getEtapas($obra_id);
        $this->data['view'] = 'obras/etapa_edit';

        return $this->layout();
    }

    /**
     * Excluir etapa
     */
    public function excluirEtapa($etapa_id)
    {
        if (!$etapa_id || !is_numeric($etapa_id)) {
            $this->session->set_flashdata('error', 'ID da etapa inválido.');
            redirect('obras');
            return;
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')) {
            $this->session->set_flashdata('error', 'Sem permissão para excluir etapas.');
            redirect('obras');
            return;
        }

        // Buscar etapa para obter obra_id antes de excluir
        $etapa = $this->obras_model->getEtapaById($etapa_id);
        if (!$etapa) {
            $this->session->set_flashdata('error', 'Etapa não encontrada.');
            redirect('obras');
            return;
        }

        $obra_id = $etapa->obra_id;

        $result = $this->obras_model->excluirEtapa($etapa_id);

        if ($result) {
            $this->session->set_flashdata('success', 'Etapa excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir etapa.');
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

        // Atividades agendadas do sistema antigo (com joins para técnico e etapa)
        $this->data['atividades'] = $this->obra_atividades_model->getByObra($obra_id);

        // Carrega dados do sistema de registro de atividades (Hora Início/Fim) se disponível
        if (file_exists(APPPATH . 'models/Atividades_model.php')) {
            $this->load->model('Atividades_model', 'atividades_sistema');
            $this->load->model('Atividades_tipos_model');

            // Atividades registradas com Hora Início/Fim
            $this->data['atividades_registradas'] = $this->atividades_sistema->listarPorObra($obra_id, [], 50);

            // Estatísticas das atividades registradas
            $this->data['estatisticas_registro'] = $this->atividades_sistema->getEstatisticasPorObra($obra_id);

            // Tipos de atividades para o formulário de registro (lista plana)
            $this->data['tipos_atividades'] = $this->Atividades_tipos_model->listar([], true);
        } else {
            $this->data['atividades_registradas'] = [];
            $this->data['estatisticas_registro'] = null;
            $this->data['tipos_atividades'] = [];
        }

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

        // Debug: verificar POST recebido
        log_message('debug', 'adicionarAtividade - POST etapa_id: ' . var_export($this->input->post('etapa_id'), true));

        $dados = [
            'obra_id' => $obra_id,
            'etapa_id' => $this->input->post('etapa_id') ? (int)$this->input->post('etapa_id') : null,
            'tecnico_id' => $this->input->post('tecnico_id') ? (int)$this->input->post('tecnico_id') : null,
            'data_atividade' => $this->input->post('data_atividade'),
            'titulo' => $this->input->post('titulo'),
            'descricao' => $this->input->post('descricao'),
            'tipo' => $this->input->post('tipo') ?: 'trabalho',
            'visivel_cliente' => $this->input->post('visivel_cliente') ? 1 : 0,
        ];

        log_message('debug', 'adicionarAtividade - Dados preparados: ' . print_r($dados, true));

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
     * Excluir atividade
     */
    public function excluirAtividade($atividade_id)
    {
        log_message('debug', 'excluirAtividade chamado com ID: ' . var_export($atividade_id, true));

        if (!$atividade_id || !is_numeric($atividade_id)) {
            log_message('error', 'excluirAtividade - ID inválido: ' . var_export($atividade_id, true));
            $this->session->set_flashdata('error', 'ID da atividade inválido.');
            redirect('obras');
            return;
        }

        // DEBUG: Verificar permissão
        $permissao = $this->session->userdata('permissao');
        log_message('debug', 'excluirAtividade - Permissão do usuário: ' . var_export($permissao, true));

        // Buscar atividade para obter obra_id (para redirecionamento)
        $atividade = $this->obra_atividades_model->getById($atividade_id);
        log_message('debug', 'excluirAtividade - Atividade encontrada: ' . ($atividade ? 'SIM' : 'NÃO'));

        if (!$atividade) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras');
            return;
        }

        $obra_id = $atividade->obra_id;
        log_message('debug', 'excluirAtividade - Obra ID: ' . $obra_id);

        // Excluir atividade
        $result = $this->obra_atividades_model->delete($atividade_id);
        log_message('debug', 'excluirAtividade - Resultado do delete: ' . ($result ? 'SUCESSO' : 'FALHA'));

        if ($result) {
            $this->session->set_flashdata('success', 'Atividade excluída com sucesso!');
        } else {
            $error = $this->db->error();
            log_message('error', 'excluirAtividade - Erro DB: ' . print_r($error, true));
            $this->session->set_flashdata('error', 'Erro ao excluir atividade.');
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

        // DEBUG: Verificar colunas específicas
        echo '<div style="background: #fff3cd; border: 2px solid #ffc107; padding: 15px; margin: 15px 0;">';
        echo '<strong>DEBUG:</strong> Verificando colunas...';
        echo '<ul>';
        echo '<li>tecnico_id existe: ' . (in_array('tecnico_id', $fields) ? 'SIM' : 'NÃO') . '</li>';
        echo '<li>usuario_id existe: ' . (in_array('usuario_id', $fields) ? 'SIM' : 'NÃO') . '</li>';
        echo '<li>etapa_id existe: ' . (in_array('etapa_id', $fields) ? 'SIM' : 'NÃO') . '</li>';
        echo '<li>titulo existe: ' . (in_array('titulo', $fields) ? 'SIM' : 'NÃO') . '</li>';
        echo '<li>status existe: ' . (in_array('status', $fields) ? 'SIM' : 'NÃO') . '</li>';
        echo '<li>data_atividade existe: ' . (in_array('data_atividade', $fields) ? 'SIM' : 'NÃO') . '</li>';
        echo '</ul>';
        echo '</div>';

        // Query direta primeiro (igual ao método antigo)
        echo '<h3>Query Direta (sem model):</h3>';
        $query_direta = $this->db->where('obra_id', $obra_id)->get('obra_atividades');
        $result_direto = $query_direta ? $query_direta->result() : [];
        echo '<p>Total via query direta: ' . count($result_direto) . '</p>';
        echo '<pre style="background: #e3f2fd; padding: 10px;">';
        print_r($result_direto);
        echo '</pre>';

        // Agora usar o model
        echo '<hr>';
        echo '<h3>Via Model (com joins):</h3>';

        // Capturar possíveis erros
        try {
            $atividades = $this->obra_atividades_model->getByObra($obra_id);
            $total = count($atividades);
            echo '<p><strong>Total via model:</strong> ' . $total . '</p>';

            if ($total > 0) {
                echo '<h4>Primeira atividade (detalhada):</h4>';
                echo '<pre style="background: #d4edda; padding: 10px;">';
                print_r($atividades[0]);
                echo '</pre>';

                echo '<h4>Campos disponíveis:</h4>';
                echo '<ul>';
                foreach ($atividades[0] as $key => $value) {
                    echo '<li>' . $key . ' = ' . (is_string($value) || is_numeric($value) ? $value : json_encode($value)) . '</li>';
                }
                echo '</ul>';
            }
        } catch (Exception $e) {
            echo '<p style="color: red;"><strong>ERRO:</strong> ' . $e->getMessage() . '</p>';
            $atividades = [];
            $total = 0;
        }

        echo '<p><strong>Total de atividades nesta obra:</strong> ' . $total . '</p>';
        echo '<p><strong>Query executada:</strong> ' . $this->db->last_query() . '</p>';

        if ($total > 0) {
            echo '<h3>Atividades encontradas:</h3>';
            echo '<table border="1" cellpadding="10" style="border-collapse: collapse;">';
            echo '<tr style="background: #f0f0f0;">';
            echo '<th>ID</th>';
            echo '<th>Título</th>';
            echo '<th>Descrição</th>';
            echo '<th>Data</th>';
            echo '<th>Status</th>';
            echo '<th>Tipo</th>';
            echo '<th>Técnico</th>';
            echo '<th>Etapa</th>';
            echo '<th>Progresso</th>';
            echo '<th>Visível</th>';
            echo '</tr>';
            foreach ($atividades as $ativ) {
                $tecnico = $ativ->tecnico_nome ?? ($ativ->usuario_nome ?? 'N/A');
                $etapa = ($ativ->etapa_nome ?? '') . (($ativ->numero_etapa ?? '') ? ' #' . $ativ->numero_etapa : '');
                $etapa = trim($etapa) ?: 'N/A';
                $titulo = $ativ->titulo ?? $ativ->descricao ?? 'Sem título';
                $data = $ativ->data_atividade ?? ($ativ->created_at ? date('Y-m-d', strtotime($ativ->created_at)) : 'N/A');

                echo '<tr>';
                echo '<td>' . ($ativ->id ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($titulo) . '</td>';
                echo '<td>' . htmlspecialchars($ativ->descricao ?? '') . '</td>';
                echo '<td>' . $data . '</td>';
                echo '<td>' . ($ativ->status ?? 'agendada') . '</td>';
                echo '<td>' . ($ativ->tipo ?? 'trabalho') . '</td>';
                echo '<td>' . htmlspecialchars($tecnico) . '</td>';
                echo '<td>' . htmlspecialchars($etapa) . '</td>';
                echo '<td>' . ($ativ->percentual_concluido ?? 0) . '%</td>';
                echo '<td>' . (($ativ->visivel_cliente ?? 1) ? 'Sim' : 'Não') . '</td>';
                echo '</tr>';
            }
            echo '</table>';

            // Também mostrar dados brutos
            echo '<h3>Dados completos (raw):</h3>';
            echo '<pre style="background: #f5f5f5; padding: 15px; overflow-x: auto;">';
            print_r($atividades);
            echo '</pre>';
        } else {
            echo '<p style="color: orange;">Nenhuma atividade encontrada para esta obra.</p>';
        }

        echo '<hr>';

        // Verificar se há atividades em outras obras
        echo '<h3>Verificação em Outras Obras</h3>';
        $this->db->select('obra_id, COUNT(*) as total');
        $this->db->group_by('obra_id');
        $query = $this->db->get('obra_atividades');
        $atividades_por_obra = $query->result();

        if (!empty($atividades_por_obra)) {
            echo '<table border="1" cellpadding="10" style="border-collapse: collapse;">';
            echo '<tr><th>Obra ID</th><th>Total Atividades</th><th>Ação</th></tr>';
            foreach ($atividades_por_obra as $row) {
                echo '<tr>';
                echo '<td>' . $row->obra_id . '</td>';
                echo '<td>' . $row->total . '</td>';
                echo '<td><a href="' . site_url('obras/verificarAtividades/' . $row->obra_id) . '">Verificar</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p style="color: orange;">Nenhuma atividade encontrada em NENHUMA obra.</p>';
        }

        echo '<hr>';
        echo '<div style="display: flex; gap: 10px; flex-wrap: wrap;">';
        echo '<a href="' . site_url('obras/atividades/' . $obra_id) . '" class="btn">Voltar para Atividades</a>';
        echo '<a href="' . site_url('obras/criarAtividadeTeste/' . $obra_id) . '" class="btn" style="background: #17a2b8; color: white; padding: 5px 10px; text-decoration: none;">Criar Atividade de Teste</a>';
        echo '<a href="' . site_url('diagnostico') . '" class="btn" style="background: #ffc107; color: #000; padding: 5px 10px; text-decoration: none;">Diagnóstico</a>';
        echo '</div>';
    }

    /**
     * Criar atividade de teste
     */
    public function criarAtividadeTeste($obra_id)
    {
        if (!$obra_id || !is_numeric($obra_id)) {
            echo 'Obra ID inválido';
            return;
        }

        // Verificar se tabela existe
        if (!$this->db->table_exists('obra_atividades')) {
            echo '<p style="color: red;">Tabela não existe!</p>';
            return;
        }

        $dados = [
            'obra_id' => $obra_id,
            'titulo' => 'Atividade de Teste ' . date('H:i:s'),
            'descricao' => 'Esta é uma atividade de teste criada automaticamente',
            'tipo' => 'trabalho',
            'status' => 'agendada',
            'data_atividade' => date('Y-m-d'),
            'percentual_concluido' => 0,
            'visivel_cliente' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Verificar campos existentes
        $colunas = $this->db->list_fields('obra_atividades');
        $dados_filtrados = [];
        foreach ($dados as $campo => $valor) {
            if (in_array($campo, $colunas)) {
                $dados_filtrados[$campo] = $valor;
            }
        }

        $this->db->insert('obra_atividades', $dados_filtrados);
        $id = $this->db->insert_id();

        if ($id) {
            echo '<p style="color: green;">✅ Atividade de teste criada com sucesso! ID: ' . $id . '</p>';
            echo '<a href="' . site_url('obras/verificarAtividades/' . $obra_id) . '">Verificar Atividades</a>';
        } else {
            echo '<p style="color: red;">❌ Erro ao criar atividade</p>';
        }
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

        // Log dos dados recebidos
        log_message('debug', 'salvarWizard - POST data: ' . print_r($this->input->post(), true));

        $this->db->trans_start();

        try {
            // Verificar campos da tabela obra_etapas
            $campos_etapas = $this->db->list_fields('obra_etapas');
            log_message('debug', 'salvarWizard - Campos obra_etapas: ' . implode(', ', $campos_etapas));

            // 1. Salvar a etapa - apenas com campos que existem
            $etapa_data = ['obra_id' => $obra_id];

            if (in_array('numero_etapa', $campos_etapas)) {
                $etapa_data['numero_etapa'] = $this->input->post('etapa_numero') ?: 1;
            }
            if (in_array('nome', $campos_etapas)) {
                $etapa_data['nome'] = $this->input->post('etapa_nome') ?: 'Nova Etapa';
            }
            if (in_array('descricao', $campos_etapas)) {
                $etapa_data['descricao'] = $this->input->post('etapa_descricao') ?: '';
            }
            if (in_array('data_inicio_prevista', $campos_etapas)) {
                $etapa_data['data_inicio_prevista'] = $this->input->post('etapa_data_inicio') ?: null;
            }
            if (in_array('data_fim_prevista', $campos_etapas)) {
                $etapa_data['data_fim_prevista'] = $this->input->post('etapa_data_fim') ?: null;
            }
            if (in_array('status', $campos_etapas)) {
                $etapa_data['status'] = 'pendente';
            }
            if (in_array('ativo', $campos_etapas)) {
                $etapa_data['ativo'] = 1;
            }
            if (in_array('created_at', $campos_etapas)) {
                $etapa_data['created_at'] = date('Y-m-d H:i:s');
            }
            if (in_array('updated_at', $campos_etapas)) {
                $etapa_data['updated_at'] = date('Y-m-d H:i:s');
            }

            log_message('debug', 'salvarWizard - Dados etapa: ' . print_r($etapa_data, true));

            $this->db->insert('obra_etapas', $etapa_data);

            // Verificar erro no insert
            $error = $this->db->error();
            if ($error['code'] != 0) {
                throw new Exception('Erro SQL ao criar etapa: ' . $error['message']);
            }

            $etapa_id = $this->db->insert_id();

            if (!$etapa_id) {
                throw new Exception('Erro ao criar etapa: insert_id retornou vazio');
            }

            log_message('debug', 'salvarWizard - Etapa criada com ID: ' . $etapa_id);

            // 2. Salvar atividades (se houver)
            $atividades = $this->input->post('atividades');
            $total_atividades = 0;

            if (!empty($atividades) && is_array($atividades)) {
                // Verificar campos da tabela obra_atividades
                $campos_atividades = $this->db->list_fields('obra_atividades');
                log_message('debug', 'salvarWizard - Campos obra_atividades: ' . implode(', ', $campos_atividades));

                foreach ($atividades as $atividade) {
                    if (!empty($atividade['titulo'])) {
                        $ativ_data = ['obra_id' => $obra_id];

                        if (in_array('etapa_id', $campos_atividades)) {
                            $ativ_data['etapa_id'] = $etapa_id;
                        }
                        if (in_array('titulo', $campos_atividades)) {
                            $ativ_data['titulo'] = $atividade['titulo'];
                        } elseif (in_array('descricao', $campos_atividades)) {
                            $ativ_data['descricao'] = $atividade['titulo'];
                        }
                        if (in_array('tipo', $campos_atividades)) {
                            $ativ_data['tipo'] = $atividade['tipo'] ?? 'trabalho';
                        }
                        if (in_array('status', $campos_atividades)) {
                            $ativ_data['status'] = 'agendada';
                        }
                        if (in_array('data_atividade', $campos_atividades)) {
                            $ativ_data['data_atividade'] = date('Y-m-d');
                        }
                        if (in_array('ativo', $campos_atividades)) {
                            $ativ_data['ativo'] = 1;
                        }
                        if (in_array('created_at', $campos_atividades)) {
                            $ativ_data['created_at'] = date('Y-m-d H:i:s');
                        }

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
     * Editar atividade
     */
    public function editarAtividade($atividade_id)
    {
        if (!$atividade_id || !is_numeric($atividade_id)) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')) {
            $this->session->set_flashdata('error', 'Sem permissão para editar atividades.');
            redirect('obras');
        }

        $this->load->model('usuarios_model');

        $this->data['atividade'] = $this->obra_atividades_model->getById($atividade_id);

        if (!$this->data['atividade']) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras');
        }

        $obra_id = $this->data['atividade']->obra_id;

        // Carregar dados para o formulário
        $this->data['obra'] = $this->obras_model->getById($obra_id);
        $this->data['tecnicos'] = $this->usuarios_model->getAll();
        $this->data['etapas'] = $this->obras_model->getEtapas($obra_id);

        // Processar formulário
        if ($this->input->post()) {
            log_message('debug', 'editarAtividade - POST recebido: ' . print_r($this->input->post(), true));

            $dados = [
                'titulo' => $this->input->post('titulo'),
                'descricao' => $this->input->post('descricao'),
                'data_atividade' => $this->input->post('data_atividade'),
                'tipo' => $this->input->post('tipo') ?: 'trabalho',
                'status' => $this->input->post('status') ?: 'agendada',
                'tecnico_id' => $this->input->post('tecnico_id') ? (int)$this->input->post('tecnico_id') : null,
                'etapa_id' => $this->input->post('etapa_id') ? (int)$this->input->post('etapa_id') : null,
                'percentual_concluido' => (int)($this->input->post('percentual_concluido') ?: 0),
                'visivel_cliente' => $this->input->post('visivel_cliente') ? 1 : 0,
            ];

            log_message('debug', 'editarAtividade - Dados preparados: ' . print_r($dados, true));

            $result = $this->obra_atividades_model->update($atividade_id, $dados);
            log_message('debug', 'editarAtividade - Resultado update: ' . ($result ? 'true' : 'false'));

            if ($result) {
                $this->session->set_flashdata('success', 'Atividade atualizada com sucesso!');
                redirect('obras/visualizarAtividade/' . $atividade_id);
            } else {
                $error = $this->db->error();
                log_message('error', 'editarAtividade - Erro DB: ' . print_r($error, true));
                $this->session->set_flashdata('error', 'Erro ao atualizar atividade: ' . ($error['message'] ?? 'Erro desconhecido'));
            }
        }

        $this->data['view'] = 'obras/atividade_edit';
        return $this->layout();
    }

    /**
     * Migrar tabela de atividades - adicionar colunas faltantes
     */
    public function migrarAtividades()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) {
            echo 'Sem permissão';
            return;
        }

        echo '<h2>Migração - Tabela obra_atividades</h2>';
        echo '<hr>';

        // Verificar colunas existentes
        $colunas_existentes = $this->db->list_fields('obra_atividades');
        echo '<p><strong>Colunas atuais:</strong> ' . implode(', ', $colunas_existentes) . '</p>';

        // Colunas a adicionar
        $colunas_para_adicionar = [
            'titulo' => "VARCHAR(255) AFTER obra_id",
            'etapa_id' => "INT NULL AFTER titulo",
            'tecnico_id' => "INT NULL AFTER etapa_id",
            'status' => "VARCHAR(50) DEFAULT 'agendada' AFTER tipo",
            'data_atividade' => "DATE AFTER status",
            'percentual_concluido' => "INT DEFAULT 0 AFTER data_atividade",
            'visivel_cliente' => "TINYINT(1) DEFAULT 1 AFTER percentual_concluido",
            'updated_at' => "DATETIME AFTER created_at"
        ];

        $adicionadas = [];
        $existentes = [];

        foreach ($colunas_para_adicionar as $coluna => $definicao) {
            if (!in_array($coluna, $colunas_existentes)) {
                $sql = "ALTER TABLE obra_atividades ADD COLUMN {$coluna} {$definicao}";
                if ($this->db->query($sql)) {
                    $adicionadas[] = $coluna;
                }
            } else {
                $existentes[] = $coluna;
            }
        }

        echo '<h3>Resultado:</h3>';
        if (!empty($adicionadas)) {
            echo '<p style="color: green;"><strong>Colunas adicionadas:</strong> ' . implode(', ', $adicionadas) . '</p>';
        }
        if (!empty($existentes)) {
            echo '<p style="color: blue;"><strong>Já existiam:</strong> ' . implode(', ', $existentes) . '</p>';
        }

        // Atualizar registros existentes - copiar descricao para titulo
        if (in_array('descricao', $colunas_existentes) && in_array('titulo', $adicionadas)) {
            $this->db->query("UPDATE obra_atividades SET titulo = SUBSTRING(descricao, 1, 255) WHERE titulo IS NULL OR titulo = ''");
            echo '<p style="color: orange;">Registros atualizados: titulo copiado de descricao</p>';
        }

        // Atualizar registros existentes - data_atividade = created_at
        if (in_array('data_atividade', $adicionadas) && in_array('created_at', $colunas_existentes)) {
            $this->db->query("UPDATE obra_atividades SET data_atividade = DATE(created_at) WHERE data_atividade IS NULL");
            echo '<p style="color: orange;">Registros atualizados: data_atividade definida</p>';
        }

        echo '<hr>';
        echo '<a href="' . site_url('obras/atividades/3') . '">Voltar para Atividades</a>';
    }

    /**
     * Visualizar atividade
     */
    public function visualizarAtividade($atividade_id = null)
    {
        // Pegar ID do parâmetro ou do URI
        if (!$atividade_id) {
            $atividade_id = $this->uri->segment(3);
        }

        if (!$atividade_id || !is_numeric($atividade_id)) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras');
        }

        $this->data['atividade'] = $this->obra_atividades_model->getById($atividade_id);

        if (!$this->data['atividade']) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras');
        }

        // Carregar checkins model sob demanda se não estiver carregado
        if (!isset($this->obra_checkins_model)) {
            $this->load->model('obra_checkins_model');
        }

        // Carregar Atividades_model para buscar execução real do wizard
        $this->load->model('Atividades_model', 'atividades');

        $this->data['obra'] = $this->obras_model->getById($this->data['atividade']->obra_id);
        $this->data['historico'] = $this->obra_atividades_model->getHistorico($atividade_id);
        $this->data['checkins'] = $this->obra_checkins_model->getByAtividade($atividade_id);

        // Buscar atividade real (execução do wizard) vinculada a esta atividade planejada
        // A vinculação é feita pelo campo obra_atividade_id na tabela os_atividades
        $this->db->where('obra_atividade_id', $atividade_id);
        $this->db->order_by('idAtividade', 'DESC');
        $query = $this->db->get('os_atividades');
        $atividade_real = $query ? $query->row() : null;

        if ($atividade_real) {
            // Buscar dados completos da atividade real (incluindo observações)
            $this->data['atividade_real'] = $this->atividades->getByIdCompleto($atividade_real->idAtividade);

            // Mesclar observações da atividade real com a atividade planejada
            if (!empty($this->data['atividade_real']->observacoes)) {
                $this->data['atividade']->observacoes = $this->data['atividade_real']->observacoes;
            }

            // Mesclar horários de início e fim da atividade real
            if (!empty($this->data['atividade_real']->hora_inicio)) {
                $this->data['atividade']->hora_inicio = $this->data['atividade_real']->hora_inicio;
            }
            if (!empty($this->data['atividade_real']->hora_fim)) {
                $this->data['atividade']->hora_fim = $this->data['atividade_real']->hora_fim;
            }

            // Calcular horas trabalhadas
            if (!empty($this->data['atividade_real']->duracao_minutos)) {
                $this->data['atividade']->horas_trabalhadas = round($this->data['atividade_real']->duracao_minutos / 60, 2);
            } elseif (!empty($this->data['atividade_real']->hora_inicio) && !empty($this->data['atividade_real']->hora_fim)) {
                $inicio = strtotime($this->data['atividade_real']->hora_inicio);
                $fim = strtotime($this->data['atividade_real']->hora_fim);
                $duracao_segundos = $fim - $inicio;
                $this->data['atividade']->horas_trabalhadas = round($duracao_segundos / 3600, 2);
            }

            // Buscar checkins da atividade real também
            $checkins_real = $this->obra_checkins_model->getByAtividade($atividade_real->idAtividade);
            if (!empty($checkins_real)) {
                // Mesclar checkins
                $this->data['checkins'] = array_merge($this->data['checkins'], $checkins_real);
            }

            // Buscar fotos da atividade real
            $this->data['fotos_atividade'] = $this->atividades->getFotos($atividade_real->idAtividade);
        } else {
            $this->data['fotos_atividade'] = [];
        }

        $this->data['view'] = 'obras/atividade_view';

        return $this->layout();
    }

    /**
     * Atualizar status de uma atividade
     */
    public function atualizarStatusAtividade($atividade_id = null)
    {
        if (!$atividade_id || !is_numeric($atividade_id)) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras');
        }

        $novo_status = $this->input->post('novo_status');
        $observacao = $this->input->post('observacao_status');

        // Validar status permitidos
        $status_permitidos = ['agendada', 'iniciada', 'pausada', 'concluida', 'cancelada'];
        if (!in_array($novo_status, $status_permitidos)) {
            $this->session->set_flashdata('error', 'Status inválido.');
            redirect('obras/visualizarAtividade/' . $atividade_id);
        }

        // Buscar atividade
        $atividade = $this->obra_atividades_model->getById($atividade_id);
        if (!$atividade) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras');
        }

        // Preparar dados para atualização
        $dados = ['status' => $novo_status];

        // Se estiver reabrindo (concluida/cancelada -> agendada/iniciada), limpar datas de conclusão
        if (in_array($novo_status, ['agendada', 'iniciada']) && in_array($atividade->status, ['concluida', 'cancelada'])) {
            $dados['data_conclusao'] = null;
            $dados['percentual_concluido'] = 0;
            $this->session->set_flashdata('success', 'Atividade reaberta com sucesso!');
        } else {
            // Registrar no histórico
            $status_labels = [
                'agendada' => 'Agendada',
                'iniciada' => 'Em Execução',
                'pausada' => 'Pausada',
                'concluida' => 'Concluída',
                'cancelada' => 'Cancelada'
            ];

            $descricao = 'Status alterado para: ' . ($status_labels[$novo_status] ?? $novo_status);
            if ($observacao) {
                $descricao .= '. Observação: ' . $observacao;
            }

            $this->obra_atividades_model->adicionarHistorico($atividade_id, 'status_alterado', $descricao);
            $this->session->set_flashdata('success', 'Status atualizado com sucesso!');
        }

        // Atualizar atividade
        $result = $this->obra_atividades_model->update($atividade_id, $dados);

        if (!$result) {
            $this->session->set_flashdata('error', 'Erro ao atualizar status.');
        }

        redirect('obras/visualizarAtividade/' . $atividade_id);
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
