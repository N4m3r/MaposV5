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
        'Aberto' => ['label' => 'Aberto', 'color' => 'secondary', 'icon' => 'bx-folder-open'],
        'Orçamento' => ['label' => 'Orçamento', 'color' => 'info', 'icon' => 'bx-calculator'],
        'Aprovado' => ['label' => 'Aprovado', 'color' => 'success', 'icon' => 'bx-check'],
        'Em Andamento' => ['label' => 'Em Andamento', 'color' => 'primary', 'icon' => 'bx-cog'],
        'Aguardando Peças' => ['label' => 'Aguardando Peças', 'color' => 'warning', 'icon' => 'bx-box'],
        'Pronto' => ['label' => 'Pronto', 'color' => 'info', 'icon' => 'bx-clock'],
        'Finalizado' => ['label' => 'Finalizado', 'color' => 'success', 'icon' => 'bx-check-circle'],
        'Cancelado' => ['label' => 'Cancelado', 'color' => 'danger', 'icon' => 'bx-x']
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

    /**
     * API: retorna cards do kanban agrupados por coluna (status).
     * GET /index.php/kanban/api_cards
     * Resposta: { success: true, data: { columns: { Status: [cards] } } }
     */
    public function api_cards()
    {
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(400);
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Requer AJAX']));
            return;
        }

        try {
            $boards = $this->getBoardsData(array_keys($this->columns));
            $columns = [];

            foreach ($boards as $key => $board) {
                $columns[$key] = [];
                foreach ($board['items'] ?? [] as $os) {
                    $columns[$key][] = [
                        'id' => (int) ($os->idOs ?? $os->id ?? 0),
                        'os_id' => (int) ($os->idOs ?? $os->id ?? 0),
                        'titulo' => $os->descricaoProduto ?? $os->descricao ?? 'OS #' . ($os->idOs ?? $os->id ?? '?'),
                        'cliente' => $os->nomeCliente ?? 'Cliente',
                        'status' => $key,
                        'data_inicio' => $os->dataInicial ?? null,
                        'valor' => isset($os->valorTotal) ? (float) $os->valorTotal : 0,
                        'prioridade' => strtolower($os->prioridade ?? 'normal'),
                    ];
                }
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'data' => ['columns' => $columns],
                ]));
        } catch (Exception $e) {
            log_message('error', 'Kanban::api_cards erro: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'data' => ['columns' => array_fill_keys(array_keys($this->columns), [])],
                    'error' => $e->getMessage(),
                ]));
        }
    }

    /**
     * API: move um card entre colunas.
     * POST /index.php/kanban/api_move
     * Body JSON: { card_id, from, to }
     */
    public function api_move()
    {
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(400);
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Requer AJAX']));
            return;
        }

        $raw = $this->input->raw_input_stream;
        $payload = json_decode($raw, true);

        $cardId = (int) ($payload['card_id'] ?? 0);
        $fromStatus = $payload['from'] ?? '';
        $toStatus = $payload['to'] ?? '';

        if ($cardId <= 0 || empty($toStatus)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'error' => 'Parametros invalidos']));
            return;
        }

        // Mapeia status do frontend para o status interno do Mapos
        $statusMap = [
            'Aberto' => 'Aberto',
            'Orcamento' => 'Orcamento',
            'Aprovado' => 'Aprovado',
            'Em Andamento' => 'Em Andamento',
            'Aguardando Pecas' => 'Aguardando Pecas',
            'Pronto' => 'Pronto',
            'Finalizado' => 'Finalizado',
            'Cancelado' => 'Cancelado',
        ];

        $statusInterno = $statusMap[$toStatus] ?? $toStatus;

        try {
            $this->db->where('idOs', $cardId);
            $updated = $this->db->update('os', [
                'status' => $statusInterno,
                'dataUpdate' => date('Y-m-d H:i:s'),
            ]);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => (bool) $updated,
                    'data' => (bool) $updated,
                ]));
        } catch (Exception $e) {
            log_message('error', 'Kanban::api_move erro: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'error' => 'Erro interno']));
        }
    }
}
