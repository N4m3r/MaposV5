<?php

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class DashboardController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mapos_model');
    }

    public function index(): void
    {
        $result = [
            'countOs' => $this->mapos_model->count('os'),
            'clientes' => $this->mapos_model->count('clientes'),
            'produtos' => $this->mapos_model->count('produtos'),
            'servicos' => $this->mapos_model->count('servicos'),
            'garantias' => $this->mapos_model->count('garantias'),
            'vendas' => $this->mapos_model->count('vendas'),
        ];

        $permissions = $this->currentUser->permissions ?? [];
        $hasOsPerm = in_array('*', $permissions) || in_array('vOs', $permissions);

        if ($hasOsPerm) {
            $result['osAbertas'] = $this->mapos_model->getOsAbertas();
            $result['osAndamento'] = $this->mapos_model->getOsAndamento();
            $result['estoqueBaixo'] = $this->mapos_model->getProdutosMinimo();
        }

        $this->success($result);
    }

    public function calendario(): void
    {
        $this->load->model('os_model');

        $start = $this->input->get('start') ?: date('Y-m-01');
        $end = $this->input->get('end') ?: date('Y-m-t');
        $status = $this->input->get('status') ?: null;

        $allOs = $this->mapos_model->calendario($start, $end, $status);

        $events = array_map(function ($os) {
            $cores = [
                'Aberto' => '#00cd00',
                'Negociacao' => '#AEB404',
                'Em Andamento' => '#436eee',
                'Orcamento' => '#CDB380',
                'Cancelado' => '#CD0000',
                'Finalizado' => '#256',
                'Faturado' => '#B266FF',
                'Aguardando Pecas' => '#FF7F00',
            ];
            $cor = $cores[$os->status] ?? '#E0E4CC';

            return [
                'title' => "OS: {$os->idOs}, Cliente: {$os->nomeCliente}",
                'start' => $os->dataFinal,
                'end' => $os->dataFinal,
                'color' => $cor,
                'extendedProps' => [
                    'id' => $os->idOs,
                    'cliente' => $os->nomeCliente,
                    'dataInicial' => date('d/m/Y', strtotime($os->dataInicial)),
                    'dataFinal' => date('d/m/Y', strtotime($os->dataFinal)),
                    'status' => $os->status,
                ],
            ];
        }, $allOs);

        $this->success([
            'start' => $start,
            'end' => $end,
            'events' => $events,
            'total' => count($events),
        ]);
    }

    public function emitente(): void
    {
        $result = [
            'appName' => $this->getConfig('app_name'),
            'emitente' => $this->mapos_model->getEmitente() ?: false,
        ];

        $this->success($result);
    }

    public function audit(): void
    {
        $this->checkPermission('cAuditoria');

        $pagination = $this->getPaginationParams();

        $this->load->model('Audit_model');
        $logs = $this->Audit_model->get('logs', '*', '', $pagination['per_page'], $pagination['offset']);
        $total = $this->db->count_all_results('logs');

        $this->paginated($logs, [
            'total' => $total,
            'page' => $pagination['page'],
            'per_page' => $pagination['per_page'],
            'total_pages' => (int) ceil($total / $pagination['per_page']),
        ]);
    }

    private function getConfig(string $key)
    {
        $this->db->where('config_key', $key);
        $row = $this->db->get('config', 1)->row();
        return $row ? $row->config_value : null;
    }
}