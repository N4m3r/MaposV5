<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller de Portal do Técnico para Obras
 *
 * Gerencia atividades diárias, check-ins, fotos e impedimentos
 */
class Obras_tecnico extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('obras_model');
        $this->load->model('obra_atividades_model');
        $this->load->model('obra_checkins_model');
        $this->load->model('usuarios_model');

        // Verificar se usuário está logado
        if (!$this->session->userdata('logado')) {
            redirect('login');
        }

        // Verificar se é técnico
        $this->data['usuario'] = $this->session->userdata('user');
        $this->data['tecnico_id'] = $this->session->userdata('id');
    }

    // ============================================
    // VIEWS
    // ============================================

    /**
     * Listar minhas obras
     */
    public function minhasObras()
    {
        $tecnico_id = $this->data['tecnico_id'];

        // Buscar obras onde o técnico está na equipe
        $this->data['obras'] = $this->obras_model->getObrasPorTecnico($tecnico_id);

        // Atividades de hoje
        $this->data['atividades_hoje'] = $this->obra_atividades_model->getAtividadesDoDia($tecnico_id);

        $this->load->view('obras_tecnico/minhas_obras', $this->data);
    }

    /**
     * Dashboard da obra para o técnico
     */
    public function obra($id)
    {
        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('obras_tecnico/minhasObras');
        }

        $tecnico_id = $this->data['tecnico_id'];

        // Verificar se técnico tem acesso a esta obra
        if (!$this->obras_model->tecnicoEstaNaEquipe($id, $tecnico_id)) {
            $this->session->set_flashdata('error', 'Você não tem acesso a esta obra.');
            redirect('obras_tecnico/minhasObras');
        }

        $this->data['obra'] = $this->obras_model->getById($id);
        $this->data['etapas'] = $this->obras_model->getEtapas($id);

        // Atividades do técnico nesta obra (sistema antigo)
        $this->data['minhas_atividades'] = $this->obra_atividades_model->getByObra($id, [
            'tecnico_id' => $tecnico_id
        ], 20);

        // Atividades de hoje
        $this->data['atividades_hoje'] = $this->obra_atividades_model->getAtividadesDoDia($tecnico_id);

        // Verifica se há atividade em andamento no novo sistema (Hora Início/Fim)
        if (file_exists(APPPATH . 'models/Atividades_model.php')) {
            $this->load->model('Atividades_model', 'atividades_novo');
            $this->data['atividade_novo_andamento'] = $this->atividades_novo->getAtividadeEmAndamentoNaObra($tecnico_id, $id);
        } else {
            $this->data['atividade_novo_andamento'] = null;
        }

        $this->load->view('obras_tecnico/obra_dashboard', $this->data);
    }

    /**
     * Visualizar atividade
     */
    public function atividade($id)
    {
        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras_tecnico/minhasObras');
        }

        $tecnico_id = $this->data['tecnico_id'];

        $this->data['atividade'] = $this->obra_atividades_model->getById($id);

        if (!$this->data['atividade']) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras_tecnico/minhasObras');
        }

        // Verificar se técnico tem acesso
        if ($this->data['atividade']->tecnico_id != $tecnico_id) {
            $this->session->set_flashdata('error', 'Você não tem permissão para esta atividade.');
            redirect('obras_tecnico/minhasObras');
        }

        $this->data['obra'] = $this->obras_model->getById($this->data['atividade']->obra_id);
        $this->data['historico'] = $this->obra_atividades_model->getHistorico($id);
        $this->data['checkins'] = $this->obra_checkins_model->getByAtividade($id);

        // Verificar se está em check-in
        $this->data['esta_checked_in'] = $this->obra_checkins_model->estaCheckedIn($id, $tecnico_id);

        // Calcular tempo trabalhado
        $this->data['tempo_trabalhado'] = $this->obra_checkins_model->calcularTempoTrabalhado($id);

        $this->load->view('obras_tecnico/atividade_execucao', $this->data);
    }

    // ============================================
    // AÇÕES DE ATIVIDADE
    // ============================================

    /**
     * Iniciar atividade
     */
    public function iniciarAtividade()
    {
        $atividade_id = $this->input->post('atividade_id');
        $tecnico_id = $this->data['tecnico_id'];

        if (!$atividade_id) {
            $this->session->set_flashdata('error', 'Atividade não informada.');
            redirect('obras_tecnico/minhasObras');
        }

        $atividade = $this->obra_atividades_model->getById($atividade_id);
        if (!$atividade) {
            $this->session->set_flashdata('error', 'Atividade não encontrada.');
            redirect('obras_tecnico/minhasObras');
        }

        $dados = [
            'hora_inicio' => date('H:i:s'),
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
            'foto' => $this->input->post('foto_url')
        ];

        if ($this->obra_atividades_model->iniciarAtividade($atividade_id, $tecnico_id, $dados)) {
            // Registrar check-in
            $this->obra_checkins_model->registrarCheckin($atividade_id, $tecnico_id, $dados);

            $this->session->set_flashdata('success', 'Atividade iniciada com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao iniciar atividade.');
        }

        redirect('obras_tecnico/atividade/' . $atividade_id);
    }

    /**
     * Pausar atividade
     */
    public function pausarAtividade()
    {
        $atividade_id = $this->input->post('atividade_id');
        $tecnico_id = $this->data['tecnico_id'];
        $motivo = $this->input->post('motivo');

        if (!$atividade_id) {
            $this->session->set_flashdata('error', 'Atividade não informada.');
            redirect('obras_tecnico/minhasObras');
        }

        $horas = $this->obra_checkins_model->calcularTempoTrabalhado($atividade_id);

        $dados = [
            'motivo' => $motivo,
            'horas' => $horas
        ];

        if ($this->obra_atividades_model->pausarAtividade($atividade_id, $tecnico_id, $dados)) {
            // Registrar pausa
            $this->obra_checkins_model->registrarPausa($atividade_id, $tecnico_id, $dados);

            $this->session->set_flashdata('success', 'Atividade pausada!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao pausar atividade.');
        }

        redirect('obras_tecnico/atividade/' . $atividade_id);
    }

    /**
     * Retomar atividade
     */
    public function retomarAtividade()
    {
        $atividade_id = $this->input->post('atividade_id');
        $tecnico_id = $this->data['tecnico_id'];

        if (!$atividade_id) {
            $this->session->set_flashdata('error', 'Atividade não informada.');
            redirect('obras_tecnico/minhasObras');
        }

        $dados = [
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude')
        ];

        if ($this->obra_atividades_model->retomarAtividade($atividade_id, $tecnico_id, $dados)) {
            // Registrar retorno
            $this->obra_checkins_model->registrarRetorno($atividade_id, $tecnico_id, $dados);

            $this->session->set_flashdata('success', 'Atividade retomada!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao retomar atividade.');
        }

        redirect('obras_tecnico/atividade/' . $atividade_id);
    }

    /**
     * Finalizar atividade
     */
    public function finalizarAtividade()
    {
        $atividade_id = $this->input->post('atividade_id');
        $tecnico_id = $this->data['tecnico_id'];

        if (!$atividade_id) {
            $this->session->set_flashdata('error', 'Atividade não informada.');
            redirect('obras_tecnico/minhasObras');
        }

        $horas = $this->obra_checkins_model->calcularTempoTrabalhado($atividade_id);

        $dados = [
            'hora_fim' => date('H:i:s'),
            'horas_trabalhadas' => $horas,
            'percentual' => $this->input->post('percentual') ?? 100,
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
            'fotos' => $this->input->post('fotos') ?? []
        ];

        if ($this->obra_atividades_model->finalizarAtividade($atividade_id, $tecnico_id, $dados)) {
            // Registrar check-out
            $this->obra_checkins_model->registrarCheckout($atividade_id, $tecnico_id, $dados);

            $this->session->set_flashdata('success', 'Atividade finalizada com sucesso!');
            redirect('obras_tecnico/obra/' . $this->obra_atividades_model->getById($atividade_id)->obra_id);
        } else {
            $this->session->set_flashdata('error', 'Erro ao finalizar atividade.');
            redirect('obras_tecnico/atividade/' . $atividade_id);
        }
    }

    /**
     * Registrar impedimento
     */
    public function registrarImpedimento()
    {
        $atividade_id = $this->input->post('atividade_id');
        $tecnico_id = $this->data['tecnico_id'];

        if (!$atividade_id) {
            $this->session->set_flashdata('error', 'Atividade não informada.');
            redirect('obras_tecnico/minhasObras');
        }

        $dados = [
            'tipo' => $this->input->post('tipo_impedimento'),
            'descricao' => $this->input->post('descricao')
        ];

        if ($this->obra_atividades_model->registrarImpedimento($atividade_id, $tecnico_id, $dados)) {
            $this->session->set_flashdata('success', 'Impedimento registrado! Gestor será notificado.');
        } else {
            $this->session->set_flashdata('error', 'Erro ao registrar impedimento.');
        }

        redirect('obras_tecnico/atividade/' . $atividade_id);
    }

    // ============================================
    // CHECK-IN/CHECK-OUT
    // ============================================

    /**
     * Registrar check-in via AJAX
     */
    public function registrarCheckin()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $atividade_id = $this->input->post('atividade_id');
        $tecnico_id = $this->data['tecnico_id'];

        $dados = [
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
            'endereco' => $this->input->post('endereco'),
            'foto' => $this->input->post('foto_url'),
            'observacao' => $this->input->post('observacao')
        ];

        $checkin_id = $this->obra_checkins_model->registrarCheckin($atividade_id, $tecnico_id, $dados);

        echo json_encode([
            'success' => $checkin_id !== false,
            'checkin_id' => $checkin_id
        ]);
    }

    /**
     * Registrar check-out via AJAX
     */
    public function registrarCheckout()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $atividade_id = $this->input->post('atividade_id');
        $tecnico_id = $this->data['tecnico_id'];

        $dados = [
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
            'endereco' => $this->input->post('endereco'),
            'foto' => $this->input->post('foto_url'),
            'observacao' => $this->input->post('observacao')
        ];

        $checkout_id = $this->obra_checkins_model->registrarCheckout($atividade_id, $tecnico_id, $dados);

        echo json_encode([
            'success' => $checkout_id !== false,
            'checkout_id' => $checkout_id
        ]);
    }

    // ============================================
    // FOTOS
    // ============================================

    /**
     * Upload de foto
     */
    public function uploadFoto()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $atividade_id = $this->input->post('atividade_id');
        $tipo = $this->input->post('tipo'); // checkin, atividade, checkout, impedimento

        // Configuração de upload
        $config['upload_path'] = './assets/uploads/obras/' . date('Y/m');
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 10240; // 10MB
        $config['encrypt_name'] = true;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('foto')) {
            $upload_data = $this->upload->data();
            $foto_url = 'assets/uploads/obras/' . date('Y/m') . '/' . $upload_data['file_name'];

            // Atualizar atividade com a foto
            $atividade = $this->obra_atividades_model->getById($atividade_id);
            if ($atividade) {
                $campo_foto = 'fotos_' . $tipo;
                $fotos = json_decode($atividade->$campo_foto ?? '[]', true) ?? [];
                $fotos[] = $foto_url;

                $this->obra_atividades_model->update($atividade_id, [
                    $campo_foto => json_encode($fotos)
                ]);
            }

            echo json_encode([
                'success' => true,
                'foto_url' => $foto_url,
                'message' => 'Foto enviada com sucesso!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $this->upload->display_errors('', '')
            ]);
        }
    }

    /**
     * Listar fotos da atividade
     */
    public function listarFotos($atividade_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $atividade = $this->obra_atividades_model->getById($atividade_id);

        if (!$atividade) {
            echo json_encode(['success' => false, 'message' => 'Atividade não encontrada']);
            return;
        }

        $fotos = [
            'checkin' => json_decode($atividade->fotos_checkin ?? '[]', true) ?? [],
            'atividade' => json_decode($atividade->fotos_atividade ?? '[]', true) ?? [],
            'checkout' => json_decode($atividade->fotos_checkout ?? '[]', true) ?? []
        ];

        echo json_encode([
            'success' => true,
            'fotos' => $fotos
        ]);
    }

    // ============================================
    // RELATÓRIOS
    // ============================================

    /**
     * Relatório diário do técnico
     */
    public function relatorioDiario($obra_id = null)
    {
        $tecnico_id = $this->data['tecnico_id'];
        $data = $this->input->get('data') ?: date('Y-m-d');

        $this->data['data_relatorio'] = $data;
        $this->data['atividades'] = $this->obra_atividades_model->getAtividadesDoDia($tecnico_id, $data);

        // Calcular estatísticas do dia
        $total_horas = 0;
        $atividades_concluidas = 0;

        foreach ($this->data['atividades'] as $atividade) {
            $total_horas += $atividade->horas_trabalhadas ?? 0;
            if ($atividade->status == 'concluida') {
                $atividades_concluidas++;
            }
        }

        $this->data['total_horas'] = $total_horas;
        $this->data['atividades_concluidas'] = $atividades_concluidas;
        $this->data['total_atividades'] = count($this->data['atividades']);

        $this->load->view('obras_tecnico/relatorio_diario', $this->data);
    }

    // ============================================
    // API MOBILE
    // ============================================

    /**
     * API: Buscar atividades do técnico
     */
    public function api_getAtividades()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $tecnico_id = $this->data['tecnico_id'];
        $data = $this->input->get('data') ?: date('Y-m-d');

        $atividades = $this->obra_atividades_model->getAtividadesDoDia($tecnico_id, $data);

        echo json_encode([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * API: Registrar ação na atividade
     */
    public function api_registrarAcao()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $acao = $this->input->post('acao'); // iniciar, pausar, retomar, finalizar, impedimento
        $atividade_id = $this->input->post('atividade_id');
        $tecnico_id = $this->data['tecnico_id'];

        $resultado = false;
        $mensagem = '';

        switch ($acao) {
            case 'iniciar':
                $dados = [
                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude')
                ];
                $resultado = $this->obra_atividades_model->iniciarAtividade($atividade_id, $tecnico_id, $dados);
                if ($resultado) {
                    $this->obra_checkins_model->registrarCheckin($atividade_id, $tecnico_id, $dados);
                    $mensagem = 'Atividade iniciada';
                }
                break;

            case 'pausar':
                $horas = $this->obra_checkins_model->calcularTempoTrabalhado($atividade_id);
                $resultado = $this->obra_atividades_model->pausarAtividade($atividade_id, $tecnico_id, [
                    'motivo' => $this->input->post('motivo'),
                    'horas' => $horas
                ]);
                if ($resultado) {
                    $this->obra_checkins_model->registrarPausa($atividade_id, $tecnico_id);
                    $mensagem = 'Atividade pausada';
                }
                break;

            case 'retomar':
                $dados = [
                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude')
                ];
                $resultado = $this->obra_atividades_model->retomarAtividade($atividade_id, $tecnico_id, $dados);
                if ($resultado) {
                    $this->obra_checkins_model->registrarRetorno($atividade_id, $tecnico_id, $dados);
                    $mensagem = 'Atividade retomada';
                }
                break;

            case 'finalizar':
                $horas = $this->obra_checkins_model->calcularTempoTrabalhado($atividade_id);
                $dados = [
                    'horas_trabalhadas' => $horas,
                    'percentual' => $this->input->post('percentual') ?? 100,
                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude')
                ];
                $resultado = $this->obra_atividades_model->finalizarAtividade($atividade_id, $tecnico_id, $dados);
                if ($resultado) {
                    $this->obra_checkins_model->registrarCheckout($atividade_id, $tecnico_id, $dados);
                    $mensagem = 'Atividade finalizada';
                }
                break;

            case 'impedimento':
                $resultado = $this->obra_atividades_model->registrarImpedimento($atividade_id, $tecnico_id, [
                    'tipo' => $this->input->post('tipo'),
                    'descricao' => $this->input->post('descricao')
                ]);
                $mensagem = 'Impedimento registrado';
                break;
        }

        echo json_encode([
            'success' => $resultado,
            'message' => $mensagem
        ]);
    }

    /**
     * API: Buscar dados da obra para mobile
     */
    public function api_getObra($obra_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $tecnico_id = $this->data['tecnico_id'];

        // Verificar se técnico tem acesso
        if (!$this->obras_model->tecnicoEstaNaEquipe($obra_id, $tecnico_id)) {
            echo json_encode(['success' => false, 'message' => 'Sem acesso']);
            return;
        }

        $obra = $this->obras_model->getById($obra_id);
        $etapas = $this->obras_model->getEtapas($obra_id);

        echo json_encode([
            'success' => true,
            'obra' => $obra,
            'etapas' => $etapas
        ]);
    }
}
