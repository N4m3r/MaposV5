<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Relatório de Performance dos Técnicos
 *
 * Dashboard completo com:
 * - Média de horas trabalhadas por técnico/dia
 * - Quantidade de OS realizadas
 * - Gráficos e métricas de produtividade
 * - Projeções e KPIs
 */

class Relatoriotecnicos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('id_admin')) {
            redirect('login');
        }

        // Carregar models
        $this->load->model('relatoriotecnicos_model');
        $this->load->model('tecnico_model');
        $this->load->model('usuarios_model');
        $this->load->model('os_model');

        // Helpers
        $this->load->helper('date');
    }

    /**
     * Dashboard principal de performance
     */
    public function index()
    {
        // Verifica permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioTecnicos')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar relatórios de técnicos.');
            redirect(base_url());
        }

        // Parâmetros de filtro
        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-d');
        $tecnico_id = $this->input->get('tecnico_id') ?: null;

        // Dados para a view
        $dados = [
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'tecnico_id' => $tecnico_id,
            'tecnicos' => $this->usuarios_model->getAll(),

            // KPIs Gerais
            'kpi_geral' => $this->relatoriotecnicos_model->getKPIsGerais($data_inicio, $data_fim),

            // Performance por técnico
            'performance_tecnicos' => $this->relatoriotecnicos_model->getPerformanceTecnicos($data_inicio, $data_fim),

            // Horas trabalhadas por dia
            'horas_por_dia' => $this->relatoriotecnicos_model->getHorasTrabalhadasPorDia($data_inicio, $data_fim, $tecnico_id),

            // OS por técnico
            'os_por_tecnico' => $this->relatoriotecnicos_model->getOSPorTecnico($data_inicio, $data_fim),

            // Produtividade diária
            'produtividade_diaria' => $this->relatoriotecnicos_model->getProdutividadeDiaria($data_inicio, $data_fim),

            // Eficiência e ranking
            'ranking' => $this->relatoriotecnicos_model->getRankingTecnicos($data_inicio, $data_fim),

            // Projeções
            'projecoes' => $this->relatoriotecnicos_model->getProjecoes($data_inicio, $data_fim),

            // Comparação mensal
            'comparativo_mensal' => $this->relatoriotecnicos_model->getComparativoMensal(6),
        ];

        $this->data['results'] = $dados;
        $this->data['view'] = 'relatoriotecnicos/dashboard';

        $this->load->view('tema/topo', $this->data);
        $this->load->view($this->data['view'], $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * API JSON para gráficos
     */
    public function api_dados()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-d');
        $tipo = $this->input->get('tipo') ?: 'geral';

        $dados = [];

        switch ($tipo) {
            case 'horas_diarias':
                $dados = $this->relatoriotecnicos_model->getHorasTrabalhadasPorDia($data_inicio, $data_fim);
                break;
            case 'os_tecnico':
                $dados = $this->relatoriotecnicos_model->getOSPorTecnico($data_inicio, $data_fim);
                break;
            case 'eficiencia':
                $dados = $this->relatoriotecnicos_model->getEficienciaTecnicos($data_inicio, $data_fim);
                break;
            case 'timeline':
                $dados = $this->relatoriotecnicos_model->getTimelineAtendimentos($data_inicio, $data_fim);
                break;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $dados]);
    }

    /**
     * Exportar relatório em CSV
     */
    public function exportar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vExportarDados')) {
            $this->session->set_flashdata('error', 'Sem permissão para exportar.');
            redirect('relatoriotecnicos');
        }

        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-d');

        $dados = $this->relatoriotecnicos_model->getPerformanceTecnicos($data_inicio, $data_fim);

        // Configurar headers para download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=relatorio_performance_' . date('Y-m-d') . '.csv');

        // Criar output
        $output = fopen('php://output', 'w');

        // Cabeçalho
        fputcsv($output, [
            'Técnico',
            'Total OS',
            'OS Finalizadas',
            'Horas Trabalhadas',
            'Média Horas/OS',
            'Dias Trabalhados',
            'Média OS/Dia',
            'Eficiência %'
        ]);

        // Dados
        foreach ($dados as $linha) {
            fputcsv($output, [
                $linha->tecnico,
                $linha->total_os,
                $linha->os_finalizadas,
                $linha->horas_trabalhadas,
                $linha->media_horas_os,
                $linha->dias_trabalhados,
                $linha->media_os_dia,
                $linha->eficiencia
            ]);
        }

        fclose($output);
    }

    /**
     * Relatório detalhado por técnico
     */
    public function detalhe($tecnico_id = null)
    {
        if (!$tecnico_id) {
            redirect('relatoriotecnicos');
        }

        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-d');

        $tecnico = $this->usuarios_model->getById($tecnico_id);
        if (!$tecnico) {
            show_404();
        }

        $dados = [
            'tecnico' => $tecnico,
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'performance' => $this->relatoriotecnicos_model->getPerformanceTecnico($tecnico_id, $data_inicio, $data_fim),
            'evolucao_diaria' => $this->relatoriotecnicos_model->getEvolucaoDiariaTecnico($tecnico_id, $data_inicio, $data_fim),
            'atividades' => $this->relatoriotecnicos_model->getAtividadesDetalhadas($tecnico_id, $data_inicio, $data_fim),
        ];

        $this->data['results'] = $dados;
        $this->data['view'] = 'relatoriotecnicos/detalhe';

        $this->load->view('tema/topo', $this->data);
        $this->load->view($this->data['view'], $this->data);
        $this->load->view('tema/rodape');
    }
}
