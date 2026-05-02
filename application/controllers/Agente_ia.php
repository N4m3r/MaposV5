<?php
/**
 * Agente_ia
 * Controller do painel administrativo do Agente IA no MapOS.
 *
 * Rotas (nao API - rotas normais do painel admin):
 *   GET  /agente_ia              -> dashboard
 *   GET  /agente_ia/autorizacoes -> lista de autorizacoes (status opcional)
 *   POST /agente_ia/responder    -> aprovar/rejeitar via painel
 *   GET  /agente_ia/permissoes   -> painel de permissoes
 *   POST /agente_ia/salvar_permissoes -> salvar permissoes editadas
 *   GET  /agente_ia/logs         -> historico de conversas
 */

class Agente_ia extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Verifica permissao do agente IA (vAgenteIA = visualizar, cAgenteIA = configura)
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vAgenteIA')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar o painel do Agente IA.');
            redirect(base_url());
        }

        $this->load->model('agente_ia_autorizacoes_model', 'autModel');
        $this->load->model('agente_ia_permissoes_model', 'permModel');
    }

    // ========================================================================
    // DASHBOARD
    // ========================================================================
    public function index()
    {
        $data = [];
        $data['title'] = 'Agente IA - Dashboard';

        // Metricas
        $data['stats'] = [
            'aut_pendentes'   => $this->autModel->contarPorStatus('pendente'),
            'aut_executadas'  => $this->autModel->contarPorStatus('executada'),
            'aut_expiradas'   => $this->autModel->contarPorStatus('expirada'),
            'interacoes_hoje' => $this->interacoesHoje(),
            'numeros_vinculados' => $this->db->where('situacao', 1)->count_all_results('whatsapp_integracao'),
            'taxa_aprovacao'  => $this->getTaxaAprovacao(),
        ];

        // Pendentes recentes
        $pendentes = $this->autModel->listar(['status' => 'pendente'], 1, 10);
        $data['pendentes'] = $pendentes['items'];

        // Ultimos logs
        $data['ultimosLogs'] = $this->ultimosLogs(8);

        // View padrao do MapOS
        $data['view'] = 'agente_ia/dashboard';
        $this->load->view('tema/topo', $data);
        $this->load->view('tema/conteudo', $data);
    }

    // ========================================================================
    // LISTA DE AUTORIZACOES
    // ========================================================================
    public function autorizacoes($status = '')
    {
        $data = [];
        $data['title'] = 'Autorizacoes do Agente IA';

        $filtro = [];
        $filtroStatus = $this->input->get('status') ?: '';
        if ($filtroStatus) {
            $filtro['status'] = $filtroStatus;
        }
        if ($this->input->get('numero')) {
            $filtro['numero'] = preg_replace('/[^0-9]/', '', $this->input->get('numero'));
        }

        $page = (int) ($this->input->get('page') ?: 1);
        $perPage = (int) ($this->input->get('per_page') ?: 20);

        $result = $this->autModel->listar($filtro, $page, $perPage);

        $data['autorizacoes']   = $result['items'];
        $data['total']          = $result['total'];
        $data['page']           = $page;
        $data['totalPages']    = (int) ceil($result['total'] / $perPage);
        $data['filtroStatus']    = $filtroStatus;

        $data['view'] = 'agente_ia/autorizacoes_pendentes';
        $this->load->view('tema/topo', $data);
        $this->load->view('tema/conteudo', $data);
    }

    // ========================================================================
    // RESPONDER AUTORIZACAO (APROVAR / REJEITAR)
    // ========================================================================
    public function responder()
    {
        $id       = (int) $this->input->post('autorizacao_id');
        $resposta = strtolower(trim($this->input->post('resposta') ?: ''));

        if (!$id || !in_array($resposta, ['aprovar', 'rejeitar'])) {
            $this->session->set_flashdata('error', 'Dados invalidos.');
            redirect('agente_ia/autorizacoes');
            return;
        }

        $aut = $this->autModel->buscarPorId($id);
        if (!$aut || $aut['status'] !== 'pendente') {
            $this->session->set_flashdata('error', 'Autorizacao nao encontrada ou ja respondida.');
            redirect('agente_ia/autorizacoes');
            return;
        }

        // Verifica expiracao
        if (strtotime($aut['expires_at']) < time()) {
            $this->autModel->atualizarStatus($id, 'expirada');
            $this->session->set_flashdata('error', 'Autorizacao expirada.');
            redirect('agente_ia/autorizacoes');
            return;
        }

        // Se for aprovar, aqui poderia acionar o webhook para o n8n executar
        // Por enquanto marca como aprovada e guarda resultado
        $novoStatus = ($resposta === 'aprovar') ? 'aprovada' : 'rejeitada';
        $this->autModel->atualizarStatus($id, $novoStatus, [
            'executado_por'    => 'usuario',
            'executed_at'      => date('Y-m-d H:i:s'),
            'resposta_usuario' => 'Respondido pelo painel admin',
            'ip_autorizacao'   => $this->input->ip_address()
        ]);

        $this->session->set_flashdata('success', 'Autorizacao ' . ($resposta === 'aprovar' ? 'aprovada' : 'rejeitada') . ' com sucesso.');

        // Se aprovou, redireciona para pagina que permite executar
        if ($resposta === 'aprovar') {
            redirect('agente_ia/autorizacoes?status=aprovada');
        } else {
            redirect('agente_ia/autorizacoes?status=rejeitada');
        }
    }

    // ========================================================================
    // PERMISSOES
    // ========================================================================
    public function permissoes()
    {
        $data = [];
        $data['title'] = 'Permissoes do Agente IA';
        $data['permissoes'] = $this->permModel->listar();
        $data['view'] = 'agente_ia/permissoes_perfil';
        $this->load->view('tema/topo', $data);
        $this->load->view('tema/conteudo', $data);
    }

    public function salvar_permissoes()
    {
        $perms = $this->input->post('perms');
        if (!is_array($perms)) {
            $this->session->set_flashdata('error', 'Nenhuma permissao para salvar.');
            redirect('agente_ia/permissoes');
            return;
        }

        $atualizados = 0;
        foreach ($perms as $id => $val) {
            $dados = [];
            if (isset($val['nivel_maximo_automatico'])) {
                $dados['nivel_maximo_automatico'] = max(1, min(5, (int)$val['nivel_maximo_automatico']));
            }
            $dados['requer_2fa'] = !empty($val['requer_2fa']) ? 1 : 0;

            if (!empty($dados)) {
                $this->permModel->atualizarPermissao((int)$id, $dados);
                $atualizados++;
            }
        }

        $this->session->set_flashdata('success', $atualizados . ' permissao(s) atualizada(s).');
        redirect('agente_ia/permissoes');
    }

    // ========================================================================
    // LOGS
    // ========================================================================
    public function logs()
    {
        $data = [];
        $data['title'] = 'Logs de Conversa - Agente IA';

        $filtro = [];
        $numero = $this->input->get('numero');
        $tipo   = $this->input->get('tipo');
        $dataF  = $this->input->get('data');

        if ($numero) {
            $filtro['numero'] = preg_replace('/[^0-9]/', '', $numero);
        }
        if ($tipo) {
            $filtro['tipo'] = $tipo;
        }
        if ($dataF) {
            $filtro['data_inicio'] = $dataF . ' 00:00:00';
            $filtro['data_fim']    = $dataF . ' 23:59:59';
        }

        $page = (int) ($this->input->get('page') ?: 1);
        $perPage = (int) ($this->input->get('per_page') ?: 25);

        // Query manual em vez de usar o model (o model de autorizacoes nao cobre logs)
        $offset = ($page - 1) * $perPage;
        $this->db->from('agente_ia_logs_conversa');
        if (!empty($filtro['numero'])) {
            $this->db->where('numero_telefone', $filtro['numero']);
        }
        if (!empty($filtro['tipo'])) {
            $this->db->where('tipo', $filtro['tipo']);
        }
        if (!empty($filtro['data_inicio'])) {
            $this->db->where('created_at >=', $filtro['data_inicio']);
            $this->db->where('created_at <=', $filtro['data_fim']);
        }
        $countBuilder = clone $this->db;
        $total = $countBuilder->count_all_results();

        $this->db->from('agente_ia_logs_conversa');
        if (!empty($filtro['numero'])) $this->db->where('numero_telefone', $filtro['numero']);
        if (!empty($filtro['tipo'])) $this->db->where('tipo', $filtro['tipo']);
        if (!empty($filtro['data_inicio'])) {
            $this->db->where('created_at >=', $filtro['data_inicio']);
            $this->db->where('created_at <=', $filtro['data_fim']);
        }
        $data['logs'] = $this->db->order_by('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->result_array();

        $data['totalPages'] = (int) ceil($total / $perPage);
        $data['page']       = $page;
        $data['view']       = 'agente_ia/logs_conversa';
        $this->load->view('tema/topo', $data);
        $this->load->view('tema/conteudo', $data);
    }

    // ========================================================================
    // UTILITARIOS INTERNOS
    // ========================================================================

    private function interacoesHoje(): int
    {
        $hoje = date('Y-m-d');
        return $this->db
            ->where('DATE(created_at)', $hoje)
            ->count_all_results('agente_ia_logs_conversa');
    }

    private function ultimosLogs(int $limite): array
    {
        return $this->db
            ->order_by('created_at', 'DESC')
            ->limit($limite)
            ->get('agente_ia_logs_conversa')
            ->result_array();
    }

    private function getTaxaAprovacao(): string
    {
        $totalDecididas = $this->autModel->contarPorStatus('aprovada') + $this->autModel->contarPorStatus('rejeitada');
        if ($totalDecididas === 0) {
            return '0%';
        }
        $aprovadas = $this->autModel->contarPorStatus('aprovada');
        return round(($aprovadas / $totalDecididas) * 100) . '%';
    }
}
