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

        // Verifica login (admin ou tecnico)
        $is_admin = $this->session->userdata('logado');
        // Verifica sessão de técnico: nova (tec_logado) ou legada (logged_in + tec_id)
        $is_tecnico = $this->session->userdata('tec_id') &&
                      ($this->session->userdata('tec_logado') || $this->session->userdata('logged_in'));

        if (!$is_admin && !$is_tecnico) {
            // Se for requisição AJAX, retorna JSON em vez de redirecionar
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
                exit;
            }
            redirect('login');
        }

        // Flag para identificar se é acesso via portal do técnico
        $this->is_portal_tecnico = $is_tecnico;
    }

    /**
     * Dashboard do Técnico - Tela principal
     */
    public function index()
    {
        // Detecta se é acesso via portal do técnico ou admin
        $is_portal_tecnico = $this->is_portal_tecnico;
        $tecnico_id = $is_portal_tecnico
            ? $this->session->userdata('tec_id')
            : $this->session->userdata('idAdmin');

        // Verifica se é técnico ou admin
        $this->data['is_admin'] = !$is_portal_tecnico && $this->session->userdata('permissao') == 1;

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
        // Detecta se é acesso via portal do técnico
        $is_portal_tecnico = $this->is_portal_tecnico;
        $tecnico_id = $is_portal_tecnico
            ? $this->session->userdata('tec_id')
            : $this->session->userdata('idAdmin');

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
     * Recebe obra_atividade_id via GET para vincular com atividade planejada
     */
    public function wizard_obra($obra_id = null, $etapa_id = null)
    {
        // Se for acesso via portal do técnico, redireciona para o controller Tecnicos
        // que tem o layout moderno e integrado com o portal
        if ($this->is_portal_tecnico) {
            $obra_atividade_id = $this->input->get('obra_atividade_id');
            $url = 'tecnicos/wizard_obra/' . $obra_id;
            if ($etapa_id) {
                $url .= '/' . $etapa_id;
            }
            if ($obra_atividade_id) {
                $url .= '?obra_atividade_id=' . $obra_atividade_id;
            }
            redirect($url);
        }

        // Obtém ID do técnico (admin ou portal)
        $tecnico_id = $this->session->userdata('idAdmin');

        // Captura o ID da atividade de obra vinculada (se vier via GET)
        $obra_atividade_id = $this->input->get('obra_atividade_id');

        // Verifica se já tem atividade em andamento
        $atividade_andamento = $this->atividades->getAtividadeEmAndamento($tecnico_id);

        // Se tem atividade em andamento, verifica se é da mesma obra
        if ($atividade_andamento && $atividade_andamento->obra_id != $obra_id) {
            $this->session->set_flashdata('error', 'Você já tem uma atividade em andamento. Finalize-a primeiro.');
            if ($this->is_portal_tecnico) {
                redirect('tecnicos/executar_obra/' . $atividade_andamento->obra_id);
            } else {
                redirect('obras_tecnico/obra/' . $atividade_andamento->obra_id);
            }
        }

        if (!$obra_id) {
            $this->session->set_flashdata('error', 'Selecione uma obra para iniciar.');
            if ($this->is_portal_tecnico) {
                redirect('tecnicos/minhas_obras');
            } else {
                redirect('obras_tecnico');
            }
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
            if ($is_portal_tecnico) {
                redirect('tecnicos/minhas_obras');
            } else {
                redirect('obras_tecnico');
            }
        }

        // Carrega etapa se especificada
        $this->data['etapa'] = null;
        if ($etapa_id) {
            $this->data['etapa'] = $this->obras_model->getEtapaById($etapa_id);
        }

        // Carrega etapas para seleção
        $this->data['etapas'] = $this->obras_model->getEtapas($obra_id);

        // Carrega dados da atividade de obra vinculada (se houver)
        $this->data['obra_atividade'] = null;
        if ($obra_atividade_id) {
            $this->load->model('obra_atividades_model');
            $this->data['obra_atividade'] = $this->obra_atividades_model->getById($obra_atividade_id);
            $this->data['obra_atividade_id'] = $obra_atividade_id;
        }

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

        // Flag para a view saber se é portal do técnico
        $this->data['is_portal_tecnico'] = $this->is_portal_tecnico;

        // Define se está na área do técnico para o layout
        $this->data['is_area_tecnico'] = $this->is_portal_tecnico;
        $this->data['menuObras'] = 'active';

        // Flag para usar o menu do portal do técnico (sidebar correto)
        $this->data['use_menu_portal_tecnico'] = true;

        // Usa o método layout() do MY_Controller que carrega: topo -> menu -> conteudo -> rodape
        // O conteudo.php abre o <div id="content"> que tem margin-left: 240px para o sidebar
        $this->data['view'] = 'atividades/wizard_obra';
        $this->layout();
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

        // Detecta se é acesso via portal do técnico
        $is_portal_tecnico = $this->is_portal_tecnico;
        $tecnico_id = $is_portal_tecnico
            ? $this->session->userdata('tec_id')
            : $this->session->userdata('idAdmin');

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
        // Desabilitar exibição de erros para não quebrar o JSON
        error_reporting(0);
        ini_set('display_errors', 0);

        try {
            header('Content-Type: application/json');

            $tecnico_id = $this->is_portal_tecnico
                ? $this->session->userdata('tec_id')
                : $this->session->userdata('idAdmin');

            if (!$tecnico_id) {
                echo json_encode(['success' => false, 'message' => 'Sessão inválida. Faça login novamente.']);
                return;
            }

            $atividade_id = $this->input->post('atividade_id');
            $obra_id = $this->input->post('obra_id');
            $motivo = $this->input->post('motivo');
            $observacao = $this->input->post('observacao');

            // Se não recebeu atividade_id, busca a atividade em andamento na obra
            if (!$atividade_id && $obra_id) {
                $atividade = $this->atividades->getAtividadeEmAndamentoNaObra($tecnico_id, $obra_id);
                if ($atividade) {
                    $atividade_id = $atividade->idAtividade ?? $atividade->id ?? null;
                }
            }

            if (!$atividade_id) {
                echo json_encode(['success' => false, 'message' => 'ID da atividade não informado.']);
                return;
            }

            // Verifica se a atividade pertence ao técnico
            $atividade = $this->atividades->getById($atividade_id);
            if (!$atividade || $atividade->tecnico_id != $tecnico_id) {
                echo json_encode(['success' => false, 'message' => 'Atividade não encontrada ou não pertence a você.']);
                return;
            }

            $result = $this->atividades->pausar($atividade_id, $motivo, $observacao);

            echo json_encode([
                'success' => (bool) $result,
                'message' => $result ? 'Atividade pausada.' : 'Erro ao pausar atividade.'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erro em pausar: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Retoma uma atividade pausada
     */
    public function retomar()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $tecnico_id = $this->is_portal_tecnico
            ? $this->session->userdata('tec_id')
            : $this->session->userdata('idAdmin');

        $atividade_id = $this->input->post('atividade_id');

        if (!$atividade_id) {
            echo json_encode(['success' => false, 'message' => 'ID da atividade não informado.']);
            return;
        }

        // Verifica se a atividade pertence ao técnico
        $atividade = $this->atividades->getById($atividade_id);
        if (!$atividade || $atividade->tecnico_id != $tecnico_id) {
            echo json_encode(['success' => false, 'message' => 'Atividade não encontrada ou não pertence a você.']);
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

        // Detecta se é acesso via portal do técnico
        $is_portal_tecnico = $this->is_portal_tecnico;
        $tecnico_id = $is_portal_tecnico
            ? $this->session->userdata('tec_id')
            : $this->session->userdata('idAdmin');
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
     * Teste de conexão AJAX - método simples para verificar se o controller está respondendo
     */
    public function teste_ajax()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Controller respondendo',
            'session_tec_id' => $this->session->userdata('tec_id'),
            'is_portal_tecnico' => $this->is_portal_tecnico,
            'post_received' => $this->input->post()
        ]);
        exit;
    }

    /**
     * Check-in em Obra (início do trabalho com etapa obrigatória)
     * Versão para wizard de atendimento do portal do técnico
     */
    public function checkin_obra()
    {
        // Desabilitar exibição de erros para não quebrar o JSON
        error_reporting(0);
        ini_set('display_errors', 0);

        try {
            header('Content-Type: application/json');

            // Verificar sessão primeiro
            $tecnico_id = $this->is_portal_tecnico
                ? $this->session->userdata('tec_id')
                : $this->session->userdata('idAdmin');

            if (!$tecnico_id) {
                echo json_encode(['success' => false, 'message' => 'Sessão inválida. Faça login novamente.']);
                return;
            }

            $obra_id = $this->input->post('obra_id');
            $etapa_id = $this->input->post('etapa_id');
            $atividade_id = $this->input->post('atividade_id');

            // Validação básica
            if (!$obra_id || !$etapa_id) {
                echo json_encode(['success' => false, 'message' => 'Obra e etapa são obrigatórios.', 'post' => $this->input->post()]);
                return;
            }

            // Verifica se já tem atividade em andamento
            if ($this->atividades->hasAtividadeEmAndamento($tecnico_id)) {
                echo json_encode(['success' => false, 'message' => 'Já existe uma atividade em andamento.']);
                return;
            }

            // Verifica se técnico tem acesso à obra
            $this->load->model('obras_model');
            if (!$this->obras_model->tecnicoEstaNaEquipe($obra_id, $tecnico_id)) {
                echo json_encode(['success' => false, 'message' => 'Você não tem acesso a esta obra.']);
                return;
            }

            // Busca o primeiro tipo de atividade disponível ou usa um padrão
            $tipo_id = $this->input->post('tipo_id');
            if (!$tipo_id) {
                $tipos = $this->atividades_tipos->listar(['ativo' => 1], 1);
                $tipo_id = $tipos[0]->idTipo ?? 1;
            }

            $tipo = $this->atividades_tipos->getById($tipo_id);

            // Verifica se é impedimento
            $tipo_execucao = $this->input->post('tipo_execucao');
            $is_impedimento = ($tipo_execucao === 'impedimento');

            // Recebe título ou usa descrição como título
            $titulo = $this->input->post('titulo');
            $descricao = $this->input->post('descricao') ?? 'Atividade registrada via wizard';

            $dados = [
                'obra_id' => $obra_id,
                'etapa_id' => $etapa_id,
                'tecnico_id' => $tecnico_id,
                'tipo_id' => $tipo_id,
                'tipo_atividade' => $tipo->nome ?? 'Atividade na Obra',
                'categoria' => $tipo->categoria ?? 'geral',
                'titulo' => $titulo ?: ($tipo->nome ?? 'Atividade'),
                'descricao' => $is_impedimento ? 'Impedimento: ' . ($this->input->post('justificativa') ?? '') : $descricao,
                'equipamento' => $this->input->post('equipamento'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'observacoes' => $this->input->post('observacoes') ?? $this->input->post('justificativa'),
            ];

            // Se for impedimento, atualiza status
            if ($is_impedimento) {
                $dados['impedimento'] = 1;
                $dados['motivo_impedimento'] = $this->input->post('justificativa');
                $dados['tipo_impedimento'] = 'outro';
            }

            // Vincula a atividade planejada se informada
            if ($atividade_id && $atividade_id != 0) {
                $dados['obra_atividade_id'] = $atividade_id;
            }

            // Processa foto se enviada
            if (!empty($_FILES['foto']['tmp_name'])) {
                $foto = $this->upload_foto('foto');
                if ($foto) {
                    $dados['foto_checkin'] = $foto;
                }
            }

            // Também processa foto em base64
            $foto_base64 = $this->input->post('foto_base64');
            if ($foto_base64) {
                $foto_path = $this->_salvar_foto_base64($foto_base64, 'checkin_obra');
                if ($foto_path) {
                    $dados['foto_checkin'] = $foto_path;
                }
            }

            $atividade_realizada_id = $this->atividades->iniciar($dados);

            if ($atividade_realizada_id) {
                // Se for impedimento, já finaliza a atividade
                if ($is_impedimento) {
                    $this->atividades->finalizar($atividade_realizada_id, [
                        'concluida' => 0,
                        'observacoes' => 'Impedimento registrado: ' . $this->input->post('justificativa')
                    ]);

                    // Atualiza também a atividade planejada
                    if ($atividade_id && $atividade_id != 0) {
                        $this->load->model('obra_atividades_model');
                        $this->obra_atividades_model->update($atividade_id, [
                            'status' => 'pausada',
                            'impedimento' => 1,
                            'motivo_impedimento' => $this->input->post('justificativa')
                        ]);
                    }
                }

                echo json_encode([
                    'success' => true,
                    'atividade_id' => $atividade_realizada_id,
                    'message' => $is_impedimento ? 'Impedimento registrado com sucesso!' : 'Check-in realizado com sucesso!'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao realizar check-in.']);
            }
        } catch (Exception $e) {
            log_message('error', 'Erro em checkin_obra: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Registrar observação/progresso durante execução
     */
    public function registrar_observacao()
    {
        // Desabilitar exibição de erros para não quebrar o JSON
        error_reporting(0);
        ini_set('display_errors', 0);

        try {
            header('Content-Type: application/json');

            $tecnico_id = $this->is_portal_tecnico
                ? $this->session->userdata('tec_id')
                : $this->session->userdata('idAdmin');

            if (!$tecnico_id) {
                echo json_encode(['success' => false, 'message' => 'Sessão inválida. Faça login novamente.']);
                return;
            }

            $obra_id = $this->input->post('obra_id');
            $atividade_id = $this->input->post('atividade_id');
            $observacao = $this->input->post('observacao');

            if (!$observacao) {
                echo json_encode(['success' => false, 'message' => 'Observação não informada.']);
                return;
            }

            // Busca atividade em andamento na obra
            $atividade = $this->atividades->getAtividadeEmAndamentoNaObra($tecnico_id, $obra_id);

            if (!$atividade) {
                // Tenta buscar pela ID específica
                $atividade = $this->atividades->getById($atividade_id);
                if (!$atividade || $atividade->tecnico_id != $tecnico_id) {
                    echo json_encode(['success' => false, 'message' => 'Atividade não encontrada ou não pertence a você.']);
                    return;
                }
            }

            // Atualiza observações
            $observacoes_atual = $atividade->observacoes ?? '';
            $nova_observacao = $observacoes_atual ? $observacoes_atual . "\n\n[" . date('d/m/Y H:i') . "] " . $observacao : "[" . date('d/m/Y H:i') . "] " . $observacao;

            $result = $this->atividades->atualizar($atividade->idAtividade, [
                'observacoes' => $nova_observacao
            ]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Observação registrada com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao registrar observação.']);
            }
        } catch (Exception $e) {
            log_message('error', 'Erro em registrar_observacao: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper para salvar foto base64
     */
    private function _salvar_foto_base64($base64_string, $prefixo = 'foto')
    {
        // Remove o prefixo data:image/...;base64,
        if (strpos($base64_string, 'base64,') !== false) {
            $base64_string = explode('base64,', $base64_string)[1];
        }

        $dados = base64_decode($base64_string);
        if (!$dados) {
            return null;
        }

        $pasta = './assets/atividades/fotos/';
        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }

        $nome_arquivo = $prefixo . '_' . uniqid() . '.jpg';
        $caminho = $pasta . $nome_arquivo;

        if (file_put_contents($caminho, $dados)) {
            return 'assets/atividades/fotos/' . $nome_arquivo;
        }

        return null;
    }

    /**
     * Finaliza atividade em obra (com atualização de etapa)
     */
    public function finalizar_atividade_obra()
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
            // Atualiza o progresso da etapa automaticamente
            $atividade = $this->atividades->getById($atividade_id);
            if ($atividade && $atividade->etapa_id) {
                $this->atualizarProgressoEtapa($atividade->etapa_id, $atividade->obra_id);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Atividade finalizada com sucesso!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao finalizar atividade.']);
        }
    }

    /**
     * Atualiza o progresso da etapa baseado nas atividades concluídas
     */
    private function atualizarProgressoEtapa($etapa_id, $obra_id)
    {
        // Obtém estatísticas da etapa
        $estatisticas = $this->atividades->getEstatisticasPorEtapa($etapa_id);

        // Calcula progresso baseado em atividades concluídas
        $progresso = $estatisticas['progresso_percentual'];

        // Atualiza a etapa
        $this->load->model('obras_model');
        $this->obras_model->atualizarEtapa($etapa_id, [
            'progresso_real' => $progresso,
            'status' => $progresso >= 100 ? 'Concluida' : 'EmAndamento'
        ]);

        return true;
    }

    /**
     * Check-out - Finaliza trabalho na obra
     * Versão para wizard de atendimento do portal do técnico
     */
    public function checkout_obra()
    {
        // Desabilitar exibição de erros para não quebrar o JSON
        error_reporting(0);
        ini_set('display_errors', 0);

        try {
            header('Content-Type: application/json');

            $tecnico_id = $this->is_portal_tecnico
                ? $this->session->userdata('tec_id')
                : $this->session->userdata('idAdmin');

            if (!$tecnico_id) {
                echo json_encode(['success' => false, 'message' => 'Sessão inválida. Faça login novamente.']);
                return;
            }

            $obra_id = $this->input->post('obra_id');
            $atividade_id = $this->input->post('atividade_id');

        // Busca atividade em andamento do técnico na obra
        $atividade = $this->atividades->getAtividadeEmAndamentoNaObra($tecnico_id, $obra_id);

        // Se não encontrou, tenta pela ID fornecida
        if (!$atividade && $atividade_id) {
            $atividade = $this->atividades->getById($atividade_id);
            if ($atividade && $atividade->tecnico_id != $tecnico_id) {
                $atividade = null;
            }
        }

        if (!$atividade) {
            echo json_encode(['success' => false, 'message' => 'Nenhuma atividade em andamento nesta obra.']);
            return;
        }

        // Processa status das atividades (checkboxes do checkout)
        $status_atividades = $this->input->post('status_atividades');
        if ($status_atividades) {
            $status_array = json_decode($status_atividades, true);
            if (is_array($status_array)) {
                $this->load->model('obra_atividades_model');
                foreach ($status_array as $item) {
                    if (isset($item['id']) && isset($item['status'])) {
                        $status = $item['status'];
                        if ($status === 'concluida') {
                            $this->obra_atividades_model->update($item['id'], [
                                'status' => 'concluida',
                                'percentual_concluido' => 100
                            ]);
                        } elseif ($status === 'pendente') {
                            $this->obra_atividades_model->update($item['id'], [
                                'status' => 'agendada'
                            ]);
                        } elseif ($status === 'nao_realizada') {
                            $this->obra_atividades_model->update($item['id'], [
                                'status' => 'cancelada'
                            ]);
                        }
                    }
                }
            }
        }

        $dados = [
            'concluida' => 1,
            'observacoes' => $this->input->post('observacoes') ?? $this->input->post('resumo_final'),
            'problemas_encontrados' => $this->input->post('pendencias'),
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
        ];

        // Processa assinatura do cliente
        if ($this->input->post('assinatura_cliente_saida')) {
            $dados['assinatura_cliente'] = $this->input->post('assinatura_cliente_saida');
        }

        // Processa foto de saída se enviada
        if (!empty($_FILES['foto_saida']['tmp_name'])) {
            $foto = $this->upload_foto('foto_saida');
            if ($foto) {
                $dados['foto_checkout'] = $foto;
            }
        }

        // Também processa foto em base64
        $foto_base64 = $this->input->post('foto_base64');
        if ($foto_base64) {
            $foto_path = $this->_salvar_foto_base64($foto_base64, 'checkout_obra');
            if ($foto_path) {
                $dados['foto_checkout'] = $foto_path;
            }
        }

        $result = $this->atividades->finalizar($atividade->idAtividade, $dados);

        if ($result) {
            // Atualiza o progresso da etapa
            if ($atividade->etapa_id) {
                $this->atualizarProgressoEtapa($atividade->etapa_id, $obra_id);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Trabalho finalizado com sucesso!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao finalizar trabalho.']);
        }
        } catch (Exception $e) {
            log_message('error', 'Erro em checkout_obra: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Vincula atividade realizada a uma atividade planejada da etapa
     */
    public function vincular_atividade_planejada()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('atividade_realizada_id', 'Atividade Realizada', 'required|integer');
        $this->form_validation->set_rules('obra_atividade_id', 'Atividade Planejada', 'required|integer');
        $this->form_validation->set_rules('etapa_id', 'Etapa', 'required|integer');
        $this->form_validation->set_rules('obra_id', 'Obra', 'required|integer');

        if ($this->form_validation->run() == false) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $tecnico_id = $this->session->userdata('idAdmin');

        $result = $this->atividades->vincularAtividadePlanejada(
            $this->input->post('atividade_realizada_id'),
            $this->input->post('obra_atividade_id'),
            $this->input->post('etapa_id'),
            $this->input->post('obra_id'),
            $tecnico_id
        );

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Atividade vinculada com sucesso!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao vincular atividade.']);
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

        // Detecta se é acesso via portal do técnico
        $is_portal_tecnico = $this->is_portal_tecnico;
        $tecnico_id = $is_portal_tecnico
            ? $this->session->userdata('tec_id')
            : $this->session->userdata('idAdmin');

        $atividade = $this->atividades->getById($this->input->post('atividade_id'));

        $dados = [
            'os_id' => $atividade->os_id,
            'tecnico_id' => $tecnico_id,
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
     * Adiciona atividade em uma obra (para wizard obra)
     */
    public function adicionar_atividade_obra()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('atividades');
        }

        $tecnico_id = $this->is_portal_tecnico
            ? $this->session->userdata('tec_id')
            : $this->session->userdata('idAdmin');

        $obra_id = $this->input->post('obra_id');
        $tipo_id = $this->input->post('tipo_id');
        $descricao = $this->input->post('descricao');

        if (!$obra_id || !$tipo_id) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
            return;
        }

        $tipo = $this->atividades_tipos->getById($tipo_id);

        $dados = [
            'obra_id' => $obra_id,
            'tecnico_id' => $tecnico_id,
            'tipo_id' => $tipo_id,
            'tipo_atividade' => $tipo ? $tipo->nome : 'Atividade',
            'categoria' => $tipo ? $tipo->categoria : 'geral',
            'descricao' => $descricao,
            'hora_inicio' => date('Y-m-d H:i:s'),
            'status' => 'em_andamento',
        ];

        $result = $this->atividades->iniciarNaObra($obra_id, $this->input->post('etapa_id'), $dados);

        echo json_encode([
            'success' => (bool) $result,
            'message' => $result ? 'Atividade adicionada.' : 'Erro ao adicionar atividade.',
            'atividade_id' => $result
        ]);
    }

    /**
     * Adiciona foto à atividade em obra (para wizard obra)
     */
    public function adicionar_foto_obra()
    {
        // Desabilitar exibição de erros para não quebrar o JSON
        error_reporting(0);
        ini_set('display_errors', 0);

        try {
            header('Content-Type: application/json');

            if (!$this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
                return;
            }

            $tecnico_id = $this->is_portal_tecnico
                ? $this->session->userdata('tec_id')
                : $this->session->userdata('idAdmin');

            if (!$tecnico_id) {
                echo json_encode(['success' => false, 'message' => 'Sessão inválida. Faça login novamente.']);
                return;
            }

            $obra_id = $this->input->post('obra_id');
            $descricao = $this->input->post('descricao');

            if (!$obra_id) {
                echo json_encode(['success' => false, 'message' => 'Obra não informada.']);
                return;
            }

            // Busca atividade em andamento na obra
            $atividade = $this->atividades->getAtividadeEmAndamentoNaObra($tecnico_id, $obra_id);

            if (!$atividade) {
                echo json_encode(['success' => false, 'message' => 'Nenhuma atividade em andamento.']);
                return;
            }

            $dados = [
                'os_id' => $atividade->os_id ?? 0,
                'tecnico_id' => $tecnico_id,
                'descricao' => $descricao,
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

            $result = $this->atividades->adicionarFoto($atividade->idAtividade, $dados);

            echo json_encode([
                'success' => (bool) $result,
                'message' => $result ? 'Foto adicionada.' : 'Erro ao adicionar foto.'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erro em adicionar_foto_obra: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
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
            if (!mkdir($config['upload_path'], 0755, true)) {
                log_message('error', 'Falha ao criar diretório: ' . $config['upload_path']);
                return null;
            }
        }

        // Verificar permissões do diretório
        if (!is_writable($config['upload_path'])) {
            log_message('error', 'Diretório sem permissão de escrita: ' . $config['upload_path']);
            return null;
        }

        $this->load->library('upload', $config);

        if ($this->upload->do_upload($input_name)) {
            $data = $this->upload->data();
            log_message('info', 'Foto upload: ' . $data['file_name']);
            return $data['file_name'];
        }

        $error = $this->upload->display_errors('', '');
        log_message('error', 'Erro upload foto: ' . $error);
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
