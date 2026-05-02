<?php
/**
 * Agente_ia
 * Controller do painel administrativo do Agente IA no MapOS.
 *
 * Rotas:
 *   GET  /agente_ia              -> dashboard
 *   GET  /agente_ia/autorizacoes -> lista de autorizacoes
 *   POST /agente_ia/responder    -> aprovar/rejeitar via painel
 *   GET  /agente_ia/permissoes   -> painel de permissoes
 *   POST /agente_ia/salvar_permissoes -> salvar permissoes editadas
 *   GET  /agente_ia/logs         -> historico de conversas
 */

class Agente_ia extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vAgenteIA')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para acessar o painel do Agente IA.');
            redirect(base_url());
        }

        $this->load->model('agente_ia_autorizacoes_model', 'autModel');
        $this->load->model('agente_ia_permissoes_model', 'permModel');
        $this->load->model('agente_ia_configuracoes_model', 'configModel');
    }

    private function verificaConfiguracao(): void
    {
        if (!$this->db->table_exists('agente_ia_configuracoes')) {
            $this->session->set_flashdata('error', 'Tabela de configuracoes do Agente IA nao existe. Execute a migration.');
            redirect('agente_ia');
        }
    }

    // =======================================================================
    // DASHBOARD
    // =======================================================================
    public function index()
    {
        $this->data['title'] = 'Agente IA - Dashboard';

        $this->data['stats'] = [
            'aut_pendentes'      => $this->autModel->contarPorStatus('pendente'),
            'aut_executadas'     => $this->autModel->contarPorStatus('executada'),
            'aut_expiradas'      => $this->autModel->contarPorStatus('expirada'),
            'interacoes_hoje'    => $this->interacoesHoje(),
            'numeros_vinculados' => $this->db->where('situacao', 1)->count_all_results('whatsapp_integracao'),
            'taxa_aprovacao'     => $this->getTaxaAprovacao(),
        ];

        $pendentes = $this->autModel->listar(['status' => 'pendente'], 1, 10);
        $this->data['pendentes'] = $pendentes['items'];
        $this->data['ultimosLogs'] = $this->ultimosLogs(8);
        $this->data['view'] = 'agente_ia/dashboard';

        return $this->layout();
    }

    // =======================================================================
    // LISTA DE AUTORIZACOES
    // =======================================================================
    public function autorizacoes($status = '')
    {
        $this->data['title'] = 'Autorizacoes do Agente IA';

        $filtro = [];
        $filtroStatus = $this->input->get('status') ?: '';
        if ($filtroStatus) {
            $filtro['status'] = $filtroStatus;
        }
        if ($this->input->get('numero')) {
            $filtro['numero'] = preg_replace('/[^0-9]/', '', $this->input->get('numero'));
        }

        $page    = (int) ($this->input->get('page') ?: 1);
        $perPage = (int) ($this->input->get('per_page') ?: 20);

        $result = $this->autModel->listar($filtro, $page, $perPage);

        $this->data['autorizacoes'] = $result['items'];
        $this->data['total']        = $result['total'];
        $this->data['page']         = $page;
        $this->data['totalPages']    = (int) ceil($result['total'] / $perPage);
        $this->data['filtroStatus']  = $filtroStatus;
        $this->data['view']          = 'agente_ia/autorizacoes_pendentes';

        return $this->layout();
    }

    // =======================================================================
    // RESPONDER AUTORIZACAO
    // =======================================================================
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

        if (strtotime($aut['expires_at']) < time()) {
            $this->autModel->atualizarStatus($id, 'expirada');
            $this->session->set_flashdata('error', 'Autorizacao expirada.');
            redirect('agente_ia/autorizacoes');
            return;
        }

        $novoStatus = ($resposta === 'aprovar') ? 'aprovada' : 'rejeitada';
        $this->autModel->atualizarStatus($id, $novoStatus, [
            'executado_por'    => 'usuario',
            'executed_at'      => date('Y-m-d H:i:s'),
            'resposta_usuario' => 'Respondido pelo painel admin',
            'ip_autorizacao'   => $this->input->ip_address()
        ]);

        $this->session->set_flashdata('success', 'Autorizacao ' . ($resposta === 'aprovar' ? 'aprovada' : 'rejeitada') . ' com sucesso.');
        redirect('agente_ia/autorizacoes?status=' . $novoStatus);
    }

    // =======================================================================
    // CONFIGURACOES GERAIS
    // =======================================================================
    public function configuracoes()
    {
        $this->verificaConfiguracao();
        $this->data['title'] = 'Configuracoes do Agente IA';
        $this->data['configs'] = $this->configModel->listar();
        $this->data['view'] = 'agente_ia/configuracoes';
        return $this->layout();
    }

    public function salvar_configuracoes()
    {
        $this->verificaConfiguracao();
        $configs = $this->input->post('configs');
        if (!$configs || !is_array($configs)) {
            $this->session->set_flashdata('error', 'Nenhuma configuracao para salvar.');
            redirect('agente_ia/configuracoes');
        }
        $atualizados = $this->configModel->salvarMultiplos($configs);
        $this->session->set_flashdata('success', $atualizados . ' configuracao(s) salva(s).');
        redirect('agente_ia/configuracoes');
    }

    // =======================================================================
    // PERMISSOES
    // =======================================================================
    public function permissoes()
    {
        $this->data['title']      = 'Permissoes do Agente IA';
        $this->data['permissoes'] = $this->permModel->listar();
        $this->data['view']      = 'agente_ia/permissoes_perfil';

        return $this->layout();
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

    // =======================================================================
    // LOGS
    // =======================================================================
    public function logs()
    {
        $this->data['title'] = 'Logs de Conversa - Agente IA';

        if (!$this->db->table_exists('agente_ia_logs_conversa')) {
            $this->data['logs']       = [];
            $this->data['totalPages'] = 0;
            $this->data['page']       = 1;
            $this->data['view']       = 'agente_ia/logs_conversa';
            return $this->layout();
        }

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

        $page    = (int) ($this->input->get('page') ?: 1);
        $perPage = (int) ($this->input->get('per_page') ?: 25);
        $offset  = ($page - 1) * $perPage;

        $where = [];
        if (!empty($filtro['numero']))      $where['numero_telefone'] = $filtro['numero'];
        if (!empty($filtro['tipo']))        $where['tipo']            = $filtro['tipo'];
        if (!empty($filtro['data_inicio'])) $where['created_at >=']  = $filtro['data_inicio'];
        if (!empty($filtro['data_fim']))    $where['created_at <=']  = $filtro['data_fim'];

        $this->db->where($where);
        $total = (int) $this->db->count_all_results('agente_ia_logs_conversa');

        $this->db->where($where);
        $query = $this->db
            ->order_by('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get('agente_ia_logs_conversa');

        $this->data['logs']       = $query ? $query->result_array() : [];
        $this->data['totalPages'] = (int) ceil($total / $perPage);
        $this->data['page']       = $page;
        $this->data['view']       = 'agente_ia/logs_conversa';

        return $this->layout();
    }

    // =======================================================================
    // UTILITARIOS INTERNOS
    // =======================================================================

    private function interacoesHoje(): int
    {
        if (!$this->db->table_exists('agente_ia_logs_conversa')) {
            return 0;
        }
        $hoje = date('Y-m-d');
        return (int) $this->db
            ->where('DATE(created_at)', $hoje)
            ->count_all_results('agente_ia_logs_conversa');
    }

    private function ultimosLogs(int $limite): array
    {
        if (!$this->db->table_exists('agente_ia_logs_conversa')) {
            return [];
        }
        $query = $this->db
            ->order_by('created_at', 'DESC')
            ->limit($limite)
            ->get('agente_ia_logs_conversa');
        return $query ? $query->result_array() : [];
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
