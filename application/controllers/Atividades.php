<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller para Sistema de Atividades de Técnicos
 * Gerencia check-in, registro de atividades e check-out
 */
class Atividades extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Atividades_model', 'atividades');
        $this->load->model('Atividades_tipos_model', 'atividades_tipos');
        $this->load->model('Os_model');
        $this->load->model('Usuarios_model');
        $this->load->helper('atividades');

        // Verifica login
        if (!$this->session->userdata('logado')) {
            redirect('login');
        }
    }

    /**
     * Dashboard do Técnico - Tela principal
     */
    public function index()
    {
        $tecnico_id = $this->session->userdata('idAdmin');

        // Verifica se é técnico ou admin
        $this->data['is_admin'] = $this->session->userdata('permissao') == 1;

        // Atividade em andamento
        $this->data['atividade_em_andamento'] = $this->atividades->getAtividadeEmAndamento($tecnico_id);

        // Resumo do dia
        $this->data['resumo_dia'] = $this->atividades->getResumoDia($tecnico_id);

        // Estatísticas do mês
        $this->data['estatisticas'] = $this->atividades->getEstatisticasTecnico($tecnico_id);

        // OS do dia para o técnico
        $this->load->model('Tec_os_model');
        $this->data['os_hoje'] = $this->Tec_os_model->getOsDoDia($tecnico_id);

        $this->data['title'] = 'Área do Técnico - Atividades';
        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('atividades/dashboard', $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Wizard de Atividades - Interface principal
     */
    public function wizard($os_id = null)
    {
        $tecnico_id = $this->session->userdata('idAdmin');

        // Verifica se já tem atividade em andamento
        $atividade_andamento = $this->atividades->getAtividadeEmAndamento($tecnico_id);

        if ($atividade_andamento && $atividade_andamento->os_id != $os_id) {
            $this->session->set_flashdata('error', 'Você já tem uma atividade em andamento na OS #' . $atividade_andamento->os_id);
            redirect('atividades/wizard/' . $atividade_andamento->os_id);
        }

        if (!$os_id) {
            $this->session->set_flashdata('error', 'Selecione uma OS para iniciar.');
            redirect('atividades/selecionar_os');
        }

        // Carrega dados da OS
        $this->data['os'] = $this->Os_model->getById($os_id);
        if (!$this->data['os']) {
            show_404();
        }

        // Dados para o wizard
        $this->data['atividade_em_andamento'] = $atividade_andamento;
        $this->data['atividades_lista'] = $this->atividades->listarPorOS($os_id);
        $this->data['tipos_atividades'] = $this->atividades_tipos->listarPorCategoria();
        $this->data['checkin_realizado'] = count($this->data['atividades_lista']) > 0;

        $this->data['title'] = 'Wizard de Atividades - OS #' . $os_id;
        $this->data['os_id'] = $os_id;
        $this->data['obra_id'] = null;
        $this->data['etapa_id'] = null;
        $this->data['modo'] = 'os';

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('atividades/wizard', $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Wizard de Atividades para Obras - Integração com sistema de obras
     */
    public function wizard_obra($obra_id = null, $etapa_id = null)
    {
        $tecnico_id = $this->session->userdata('idAdmin');

        // Verifica se já tem atividade em andamento
        $atividade_andamento = $this->atividades->getAtividadeEmAndamento($tecnico_id);

        if ($atividade_andamento && $atividade_andamento->obra_id != $obra_id) {
            $this->session->set_flashdata('error', 'Você já tem uma atividade em andamento. Finalize-a primeiro.');
            redirect('obras_tecnico/obra/' . $atividade_andamento->obra_id);
        }

        if (!$obra_id) {
            $this->session->set_flashdata('error', 'Selecione uma obra para iniciar.');
            redirect('obras_tecnico');
        }

        // Carrega dados da obra
        $this->load->model('obras_model');
        $this->data['obra'] = $this->obras_model->getById($obra_id);
        if (!$this->data['obra']) {
            show_404();
        }

        // Verifica se técnico tem acesso
        if (!$this->obras_model->tecnicoEstaNaEquipe($obra_id, $tecnico_id)) {
            $this->session->set_flashdata('error', 'Você não tem acesso a esta obra.');
            redirect('obras_tecnico');
        }

        // Carrega etapa se especificada
        $this->data['etapa'] = null;
        if ($etapa_id) {
            $this->data['etapa'] = $this->obras_model->getEtapaById($etapa_id);
        }

        // Carrega etapas para seleção
        $this->data['etapas'] = $this->obras_model->getEtapas($obra_id);

        // Dados para o wizard
        $this->data['atividade_em_andamento'] = $atividade_andamento;
        $this->data['atividades_lista'] = $this->atividades->listarPorObra($obra_id);
        $this->data['tipos_atividades'] = $this->atividades_tipos->listarPorCategoria();
        $this->data['checkin_realizado'] = count($this->data['atividades_lista']) > 0;

        $this->data['title'] = 'Wizard de Atividades - Obra: ' . $this->data['obra']->nome;
        $this->data['obra_id'] = $obra_id;
        $this->data['etapa_id'] = $etapa_id;
        $this->data['os_id'] = null;
        $this->data['modo'] = 'obra';

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('atividades/wizard_obra', $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Seleção de OS para iniciar atendimento
     */
    public function selecionar_os()
    {
        $tecnico_id = $this->session->userdata('idAdmin');

        // Carrega OS designadas para o técnico
        $this->load->model('Tec_os_model');
        $this->data['os_pendentes'] = $this->Tec_os_model->getOsPendentes($tecnico_id);
        $this->data['os_hoje'] = $this->Tec_os_model->getOsDoDia($tecnico_id);

        // Verifica atividade em andamento
        $this->data['atividade_andamento'] = $this->atividades->getAtividadeEmAndamento($tecnico_id);

        $this->data['title'] = 'Selecionar Ordem de Serviço';

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('atividades/selecionar_os', $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Realiza check-in (início do atendimento)
     */
    public function checkin()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('os_id', 'OS', 'required|integer');
        $this->form_validation->set_rules('tipo_id', 'Tipo de Atividade', 'required|integer');

        if ($this->form_validation->run() == false) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $tecnico_id = $this->session->userdata('idAdmin');

        // Verifica se já tem atividade em andamento
        if ($this->atividades->hasAtividadeEmAndamento($tecnico_id)) {
            echo json_encode(['success' => false, 'message' => 'Já existe uma atividade em andamento.']);
            return;
        }

        $tipo = $this->atividades_tipos->getById($this->input->post('tipo_id'));

        $dados = [
            'os_id' => $this->input->post('os_id'),
            'tecnico_id' => $tecnico_id,
            'tipo_id' => $this->input->post('tipo_id'),
            'tipo_atividade' => $tipo->nome ?? 'Atendimento Técnico',
            'categoria' => $tipo->categoria ?? 'geral',
            'descricao' => $this->input->post('descricao') ?? 'Check-in - Início do atendimento',
            'equipamento' => $this->input->post('equipamento'),
            'local_instalacao' => $this->input->post('local_instalacao'),
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
            'observacoes' => $this->input->post('observacoes'),
        ];

        // Processa foto se enviada
        if (!empty($_FILES['foto']['tmp_name'])) {
            $foto = $this->upload_foto('foto');
            if ($foto) {
                $dados['foto_checkin'] = $foto;
            }
        }

        // Processa assinatura se enviada
        if ($this->input->post('assinatura_cliente')) {
            $dados['assinatura_cliente'] = $this->input->post('assinatura_cliente');
        }

        $atividade_id = $this->atividades->iniciar($dados);

        if ($atividade_id) {
            echo json_encode([
                'success' => true,
                'atividade_id' => $atividade_id,
                'message' => 'Check-in realizado com sucesso!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao realizar check-in.']);
        }
    }

    /**
     * Inicia nova atividade
     */
    public function iniciar_atividade()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('os_id', 'OS', 'required|integer');
        $this->form_validation->set_rules('tipo_id', 'Tipo de Atividade', 'required|integer');

        if ($this->form_validation->run() == false) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $tecnico_id = $this->session->userdata('idAdmin');

        // Verifica se já tem atividade em andamento
        if ($this->atividades->hasAtividadeEmAndamento($tecnico_id)) {
            echo json_encode(['success' => false, 'message' => 'Finalize a atividade atual antes de iniciar uma nova.']);
            return;
        }

        $tipo = $this->atividades_tipos->getById($this->input->post('tipo_id'));

        $dados = [
            'os_id' => $this->input->post('os_id'),
            'tecnico_id' => $tecnico_id,
            'tipo_id' => $this->input->post('tipo_id'),
            'tipo_atividade' => $tipo->nome ?? 'Atividade Técnica',
            'categoria' => $tipo->categoria ?? 'geral',
            'descricao' => $this->input->post('descricao'),
            'equipamento' => $this->input->post('equipamento'),
            'local_instalacao' => $this->input->post('local_instalacao'),
            'prioridade' => $this->input->post('prioridade') ?? 'normal',
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
        ];

        $atividade_id = $this->atividades->iniciar($dados);

        if ($atividade_id) {
            echo json_encode([
                'success' => true,
                'atividade_id' => $atividade_id,
                'message' => 'Atividade iniciada com sucesso!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao iniciar atividade.']);
        }
    }

    /**
     * Pausa uma atividade
     */
    public function pausar()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $atividade_id = $this->input->post('atividade_id');
        $motivo = $this->input->post('motivo');
        $observacao = $this->input->post('observacao');

        if (!$atividade_id) {
            echo json_encode(['success' => false, 'message' => 'ID da atividade não informado.']);
            return;
        }

        $result = $this->atividades->pausar($atividade_id, $motivo, $observacao);

        echo json_encode([
            'success' => (bool) $result,
            'message' => $result ? 'Atividade pausada.' : 'Erro ao pausar atividade.'
        ]);
    }

    /**
     * Retoma uma atividade pausada
     */
    public function retomar()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $atividade_id = $this->input->post('atividade_id');

        if (!$atividade_id) {
            echo json_encode(['success' => false, 'message' => 'ID da atividade não informado.']);
            return;
        }

        $result = $this->atividades->retomar($atividade_id);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Atividade retomada.' : 'Erro ao retomar atividade.'
        ]);
    }

    /**
     * Finaliza uma atividade
     */
    public function finalizar_atividade()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('atividade_id', 'Atividade', 'required|integer');
        $this->form_validation->set_rules('concluida', 'Status', 'required|in_list[1,0]');

        if ($this->form_validation->run() == false) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $atividade_id = $this->input->post('atividade_id');
        $concluida = $this->input->post('concluida');

        $dados = [
            'concluida' => $concluida,
            'problemas_encontrados' => $this->input->post('problemas_encontrados'),
            'solucao_aplicada' => $this->input->post('solucao_aplicada'),
            'motivo_nao_concluida' => $concluida == '0' ? $this->input->post('motivo_nao_concluida') : null,
            'observacoes' => $this->input->post('observacoes_final'),
        ];

        // Processa assinatura se enviada
        if ($this->input->post('assinatura_tecnico')) {
            $dados['assinatura_tecnico'] = $this->input->post('assinatura_tecnico');
        }

        $result = $this->atividades->finalizar($atividade_id, $dados);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Atividade finalizada com sucesso!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao finalizar atividade.']);
        }
    }

    /**
     * Check-out - Finaliza atendimento completo
     */
    public function checkout()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $tecnico_id = $this->session->userdata('idAdmin');
        $os_id = $this->input->post('os_id');

        // Busca atividade em andamento
        $atividade = $this->atividades->getAtividadeEmAndamento($tecnico_id);

        if (!$atividade) {
            echo json_encode(['success' => false, 'message' => 'Nenhuma atividade em andamento.']);
            return;
        }

        $dados = [
            'concluida' => $this->input->post('concluida') ?? 1,
            'observacoes' => $this->input->post('resumo_final'),
            'problemas_encontrados' => $this->input->post('pendencias'),
        ];

        // Processa assinatura do cliente
        if ($this->input->post('assinatura_cliente_saida')) {
            $dados['assinatura_cliente'] = $this->input->post('assinatura_cliente_saida');
        }

        $result = $this->atividades->finalizar($atividade->idAtividade, $dados);

        if ($result) {
            // Atualiza OS para status adequado
            $this->load->model('Os_model');
            $this->Os_model->edit('os', ['status' => 'Finalizado'], 'idOs', $os_id);

            echo json_encode([
                'success' => true,
                'message' => 'Atendimento finalizado com sucesso!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao finalizar atendimento.']);
        }
    }

    /**
     * Adiciona material à atividade
     */
    public function adicionar_material()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('atividade_id', 'Atividade', 'required|integer');
        $this->form_validation->set_rules('produto_id', 'Produto', 'required|integer');
        $this->form_validation->set_rules('quantidade', 'Quantidade', 'required|numeric');

        if ($this->form_validation->run() == false) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $dados = [
            'produto_id' => $this->input->post('produto_id'),
            'nome_produto' => $this->input->post('nome_produto'),
            'quantidade' => $this->input->post('quantidade'),
            'unidade' => $this->input->post('unidade') ?? 'un',
            'observacao' => $this->input->post('observacao'),
        ];

        $result = $this->atividades->adicionarMaterial($this->input->post('atividade_id'), $dados);

        echo json_encode([
            'success' => (bool) $result,
            'message' => $result ? 'Material adicionado.' : 'Erro ao adicionar material.'
        ]);
    }

    /**
     * Adiciona foto à atividade
     */
    public function adicionar_foto()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('atividade_id', 'Atividade', 'required|integer');

        if ($this->form_validation->run() == false) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $atividade = $this->atividades->getById($this->input->post('atividade_id'));

        $dados = [
            'os_id' => $atividade->os_id,
            'tecnico_id' => $this->session->userdata('idAdmin'),
            'descricao' => $this->input->post('descricao'),
            'tipo_foto' => $this->input->post('tipo_foto') ?? 'execucao',
            'etapa' => $this->input->post('etapa') ?? 'durante',
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
        ];

        // Processa foto
        if (!empty($_FILES['foto']['tmp_name'])) {
            $foto_path = $this->upload_foto_arquivo('foto');
            if ($foto_path) {
                $dados['caminho_arquivo'] = $foto_path;
            }
        }

        // Ou foto em base64
        if ($this->input->post('foto_base64')) {
            $dados['foto_base64'] = $this->input->post('foto_base64');
        }

        $result = $this->atividades->adicionarFoto($this->input->post('atividade_id'), $dados);

        echo json_encode([
            'success' => (bool) $result,
            'message' => $result ? 'Foto adicionada.' : 'Erro ao adicionar foto.'
        ]);
    }

    /**
     * Lista atividades da OS (AJAX)
     */
    public function listar_por_os($os_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $atividades = $this->atividades->listarPorOS($os_id);

        echo json_encode([
            'success' => true,
            'atividades' => $atividades
        ]);
    }

    /**
     * Detalhes de uma atividade (AJAX)
     */
    public function detalhes($atividade_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $atividade = $this->atividades->getByIdCompleto($atividade_id);

        if ($atividade) {
            echo json_encode([
                'success' => true,
                'atividade' => $atividade
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Atividade não encontrada.']);
        }
    }

    /**
     * Histórico de atividades do técnico
     */
    public function historico()
    {
        $tecnico_id = $this->session->userdata('idAdmin');

        $filtros = [
            'data_inicio' => $this->input->get('data_inicio') ?? date('Y-m-01'),
            'data_fim' => $this->input->get('data_fim') ?? date('Y-m-t'),
        ];

        $this->data['atividades'] = $this->atividades->listarPorTecnico($tecnico_id, $filtros);
        $this->data['estatisticas'] = $this->atividades->getEstatisticasTecnico($tecnico_id, $filtros['data_inicio'], $filtros['data_fim']);
        $this->data['filtros'] = $filtros;

        $this->data['title'] = 'Histórico de Atividades';

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('atividades/historico', $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Relatório de atividades (Admin)
     */
    public function relatorio()
    {
        // Apenas admin
        if ($this->session->userdata('permissao') != 1) {
            redirect('atividades');
        }

        $this->load->model('Usuarios_model');

        $filtros = [];
        if ($this->input->get('data_inicio')) {
            $filtros['data_inicio'] = $this->input->get('data_inicio');
        }
        if ($this->input->get('data_fim')) {
            $filtros['data_fim'] = $this->input->get('data_fim');
        }
        if ($this->input->get('tecnico_id')) {
            $filtros['tecnico_id'] = $this->input->get('tecnico_id');
        }

        $this->data['tecnicos'] = $this->Usuarios_model->getAll();
        $this->data['atividades'] = $this->atividades->gerarRelatorio($filtros);
        $this->data['filtros'] = $filtros;

        $this->data['title'] = 'Relatório de Atividades';

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('atividades/relatorio', $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Obtém tipos de atividades por categoria (AJAX)
     */
    public function get_tipos_por_categoria($categoria)
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $tipos = $this->atividades_tipos->listar(['categoria' => $categoria]);

        echo json_encode([
            'success' => true,
            'tipos' => $tipos
        ]);
    }

    /**
     * Helper para upload de foto
     */
    private function upload_foto($input_name)
    {
        $config['upload_path'] = './assets/atividades/fotos/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 5120; // 5MB
        $config['encrypt_name'] = true;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }

        $this->load->library('upload', $config);

        if ($this->upload->do_upload($input_name)) {
            return $this->upload->data('file_name');
        }

        return null;
    }

    /**
     * Helper para upload de arquivo de foto
     */
    private function upload_foto_arquivo($input_name)
    {
        return $this->upload_foto($input_name);
    }
}
