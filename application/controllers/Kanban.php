<?php
/**
 * Kanban Controller
 * Board Kanban para gerenciamento visual de Ordens de Serviço
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Kanban extends MY_Controller
{
    private $columns = [
        'Aberto' => ['label' => 'Aberto', 'color' => 'secondary', 'icon' => 'fa-folder-open'],
        'Orçamento' => ['label' => 'Orçamento', 'color' => 'info', 'icon' => 'fa-calculator'],
        'Aprovado' => ['label' => 'Aprovado', 'color' => 'success', 'icon' => 'fa-check'],
        'Em Andamento' => ['label' => 'Em Andamento', 'color' => 'primary', 'icon' => 'fa-cogs'],
        'Aguardando Peças' => ['label' => 'Aguardando Peças', 'color' => 'warning', 'icon' => 'fa-box'],
        'Pronto' => ['label' => 'Pronto', 'color' => 'info', 'icon' => 'fa-clock'],
        'Finalizado' => ['label' => 'Finalizado', 'color' => 'success', 'icon' => 'fa-check-circle'],
        'Cancelado' => ['label' => 'Cancelado', 'color' => 'danger', 'icon' => 'fa-times']
    ];

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logado')) {
            redirect('login');
        }

        $this->load->model('os_model');
        $this->load->model('clientes_model');
    }

    /**
     * Visualização principal do Kanban
     */
    public function index()
    {
        $statusFilter = $this->input->get('status') ?: array_keys($this->columns);
        $tecnicoId = $this->input->get('tecnico') ?: null;
        $dataInicio = $this->input->get('data_inicio') ?: null;
        $dataFim = $this->input->get('data_fim') ?: null;

        $this->data['columns'] = $this->columns;
        $this->data['boards'] = $this->getBoardsData($statusFilter, $tecnicoId, $dataInicio, $dataFim);
        $this->data['tecnicos'] = $this->getTecnicos();
        $this->data['total_os'] = $this->os_model->db->count_all_results('os');
        $this->data['filters'] = [
            'status' => $statusFilter,
            'tecnico' => $tecnicoId,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ];
        $this->data['menuKanban'] = true;
        $this->data['view'] = 'kanban/board';

        return $this->layout();
    }

    /**
     * API: Retorna dados do Kanban em JSON
     */
    public function api_get()
    {
        $this->output->set_content_type('application/json');

        $status = $this->input->get('status');
        $boards = $this->getBoardsData($status ? [$status] : array_keys($this->columns));

        $this->output->set_output(json_encode([
            'success' => true,
            'data' => $boards
        ]));
    }

    /**
     * API: Atualiza status da OS (drag and drop)
     */
    public function api_update_status()
    {
        $this->output->set_content_type('application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $osId = $data['os_id'] ?? null;
        $newStatus = $data['status'] ?? null;

        if (!$osId || !$newStatus) {
            $this->output->set_output(json_encode([
                'success' => false,
                'error' => 'Dados incompletos'
            ]));
            return;
        }

        // Verifica se o status é válido
        if (!isset($this->columns[$newStatus])) {
            $this->output->set_output(json_encode([
                'success' => false,
                'error' => 'Status inválido'
            ]));
            return;
        }

        $result = $this->os_model->update($osId, ['status' => $newStatus]);

        // Registra no log
        if ($result) {
            $ci = &get_instance();
            $ci->load->model('Audit_model');
            $ci->Audit_model->addLog([
                'acao' => 'status_change',
                'tabela' => 'os',
                'id_registro' => $osId,
                'detalhes' => "Status alterado para: {$newStatus}",
                'ip' => $this->input->ip_address()
            ]);
        }

        $this->output->set_output(json_encode([
            'success' => (bool) $result,
            'message' => $result ? 'Status atualizado' : 'Erro ao atualizar'
        ]));
    }

    /**
     * Obtém dados dos boards
     */
    private function getBoardsData($statusFilter, $tecnicoId = null, $dataInicio = null, $dataFim = null)
    {
        $boards = [];

        foreach ($this->columns as $status => $config) {
            if (!in_array($status, $statusFilter)) {
                continue;
            }

            // Monta where conditions
            $where = ['status' => $status];

            if ($tecnicoId) {
                $where['usuarios_id'] = $tecnicoId;
            }

            if ($dataInicio) {
                $where['de'] = $dataInicio;
            }

            if ($dataFim) {
                $where['ate'] = $dataFim;
            }

            // Usa getOs para buscar com filtros
            $ordens = $this->os_model->getOs('os', '*', $where, 0, 0);

            // Enriquece com dados do cliente
            foreach ($ordens as &$os) {
                $cliente = $this->clientes_model->getById($os->idClientes);
                $os->nomeCliente = $cliente->nomeCliente ?? 'N/A';
                $os->telefone = $cliente->telefone ?? '';
                $os->corPrioridade = $this->getPrioridadeColor($os->prioridade ?? 'normal');
            }

            $boards[$status] = [
                'id' => $status,
                'title' => $config['label'],
                'color' => $config['color'],
                'icon' => $config['icon'],
                'items' => $ordens,
                'count' => count($ordens)
            ];
        }

        return $boards;
    }

    /**
     * Retorna cor baseada na prioridade
     */
    private function getPrioridadeColor($prioridade)
    {
        $map = [
            'baixa' => 'success',
            'normal' => 'info',
            'alta' => 'warning',
            'urgente' => 'danger',
            'critica' => 'dark'
        ];

        return $map[strtolower($prioridade)] ?? 'secondary';
    }

    /**
     * Obtém lista de técnicos
     */
    private function getTecnicos()
    {
        $this->db->where('situacao', 1);
        return $this->db->get('usuarios')->result();
    }

    /**
     * Imprime visualização do Kanban
     */
    public function print()
    {
        $data = [
            'columns' => $this->columns,
            'boards' => $this->getBoardsData(array_keys($this->columns)),
            'data' => date('d/m/Y H:i:s')
        ];

        $this->load->view('kanban/print', $data);
    }
}
