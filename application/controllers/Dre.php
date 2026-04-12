<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller: DRE (Demonstração do Resultado do Exercício)
 * Sistema de contabilidade completo
 */
class Dre extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('id_admin')) {
            redirect('login');
        }

        $this->load->model('dre_model');
        $this->load->model('financeiro_model');
        $this->load->helper(['date', 'currency']);
    }

    /**
     * Dashboard principal do DRE
     */
    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar o DRE.');
            redirect(base_url());
        }

        // Período padrão: mês atual
        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');

        // Gerar DRE
        $dre = $this->dre_model->gerarDRE($data_inicio, $data_fim, true);

        // Indicadores
        $indicadores = $this->dre_model->getIndicadores($data_inicio, $data_fim);

        // Evolução mensal (últimos 6 meses)
        $evolucao = $this->dre_model->getEvolucaoMensal(6);

        $this->data['results'] = [
            'dre' => $dre,
            'indicadores' => $indicadores,
            'evolucao' => $evolucao,
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
        ];

        $this->data['view'] = 'dre/dashboard';
        $this->load->view('tema/topo', $this->data);
        $this->load->view($this->data['view'], $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Relatório DRE completo (impressão)
     */
    public function relatorio()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vDRERelatorio')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');
        $comparativo = $this->input->get('comparativo') ? true : false;

        $dre = $this->dre_model->gerarDRE($data_inicio, $data_fim, $comparativo);
        $indicadores = $this->dre_model->getIndicadores($data_inicio, $data_fim);

        $this->data['results'] = [
            'dre' => $dre,
            'indicadores' => $indicadores,
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'comparativo' => $comparativo,
        ];

        $this->data['view'] = 'dre/relatorio';
        $this->load->view('tema/topo', $this->data);
        $this->load->view($this->data['view'], $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Gestão do Plano de Contas
     */
    public function contas()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cDREConta')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $this->data['contas'] = $this->dre_model->getContas();
        $this->data['view'] = 'dre/contas';

        $this->load->view('tema/topo', $this->data);
        $this->load->view($this->data['view'], $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Formulário para adicionar/editar conta
     */
    public function conta_form($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cDREConta')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $conta = $id ? $this->dre_model->getContaById($id) : null;

        $this->data['conta'] = $conta;
        $this->data['contas_pai'] = $this->dre_model->getContas(1);
        $this->data['view'] = 'dre/conta_form';

        $this->load->view('tema/topo', $this->data);
        $this->load->view($this->data['view'], $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Salvar conta (insert/update)
     */
    public function conta_salvar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cDREConta')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $id = $this->input->post('id');
        $dados = [
            'codigo' => $this->input->post('codigo'),
            'nome' => $this->input->post('nome'),
            'tipo' => $this->input->post('tipo'),
            'grupo' => $this->input->post('grupo'),
            'ordem' => $this->input->post('ordem'),
            'conta_pai_id' => $this->input->post('conta_pai_id') ?: null,
            'nivel' => $this->input->post('nivel'),
            'sinal' => $this->input->post('sinal'),
            'ativo' => $this->input->post('ativo') ? 1 : 0,
        ];

        if ($id) {
            $this->dre_model->atualizarConta($id, $dados);
            $this->session->set_flashdata('success', 'Conta atualizada com sucesso!');
        } else {
            $this->dre_model->adicionarConta($dados);
            $this->session->set_flashdata('success', 'Conta adicionada com sucesso!');
        }

        redirect('dre/contas');
    }

    /**
     * Excluir conta
     */
    public function conta_excluir($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dDREConta')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $this->dre_model->excluirConta($id);
        $this->session->set_flashdata('success', 'Conta removida com sucesso!');
        redirect('dre/contas');
    }

    /**
     * Lançamentos Contábeis
     */
    public function lancamentos()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vDRELancamento')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');

        $this->data['lancamentos'] = $this->dre_model->getLancamentos($data_inicio, $data_fim);
        $this->data['contas'] = $this->dre_model->getContas(1);
        $this->data['data_inicio'] = $data_inicio;
        $this->data['data_fim'] = $data_fim;
        $this->data['view'] = 'dre/lancamentos';

        $this->load->view('tema/topo', $this->data);
        $this->load->view($this->data['view'], $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Formulário de lançamento
     */
    public function lancamento_form($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cDRELancamento')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $lancamento = null;
        if ($id) {
            $lancamentos = $this->dre_model->getLancamentos('1900-01-01', '2099-12-31');
            foreach ($lancamentos as $l) {
                if ($l->id == $id) {
                    $lancamento = $l;
                    break;
                }
            }
        }

        $this->data['lancamento'] = $lancamento;
        $this->data['contas'] = $this->dre_model->getContas(1);
        $this->data['view'] = 'dre/lancamento_form';

        $this->load->view('tema/topo', $this->data);
        $this->load->view($this->data['view'], $this->data);
        $this->load->view('tema/rodape');
    }

    /**
     * Salvar lançamento
     */
    public function lancamento_salvar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cDRELancamento')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $id = $this->input->post('id');
        $dados = [
            'conta_id' => $this->input->post('conta_id'),
            'data' => $this->input->post('data'),
            'valor' => str_replace(',', '.', str_replace('.', '', $this->input->post('valor'))),
            'tipo_movimento' => $this->input->post('tipo_movimento'),
            'descricao' => $this->input->post('descricao'),
            'documento' => $this->input->post('documento'),
        ];

        if ($id) {
            $this->dre_model->atualizarLancamento($id, $dados);
            $this->session->set_flashdata('success', 'Lançamento atualizado!');
        } else {
            $this->dre_model->adicionarLancamento($dados);
            $this->session->set_flashdata('success', 'Lançamento adicionado!');
        }

        redirect('dre/lancamentos');
    }

    /**
     * Excluir lançamento
     */
    public function lancamento_excluir($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dDRELancamento')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $this->dre_model->excluirLancamento($id);
        $this->session->set_flashdata('success', 'Lançamento removido!');
        redirect('dre/lancamentos');
    }

    /**
     * Integração automática de dados
     */
    public function integrar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cDREIntegracao')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $data_inicio = $this->input->post('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->post('data_fim') ?: date('Y-m-t');

        $inseridos = $this->dre_model->integrarDadosAutomaticos($data_inicio, $data_fim);

        $this->session->set_flashdata('success', "Integração concluída! {$inseridos} registros importados.");
        redirect('dre');
    }

    /**
     * Exportar DRE
     */
    public function exportar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vDREExportar')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');
        $formato = $this->input->get('formato') ?: 'csv';

        $csv = $this->dre_model->exportarDRE($data_inicio, $data_fim, $formato);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=DRE_' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');
        foreach ($csv as $linha) {
            fputcsv($output, $linha);
        }
        fclose($output);
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
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');
        $tipo = $this->input->get('tipo') ?: 'dre';

        $response = ['success' => true, 'data' => null];

        switch ($tipo) {
            case 'dre':
                $response['data'] = $this->dre_model->gerarDRE($data_inicio, $data_fim);
                break;
            case 'indicadores':
                $response['data'] = $this->dre_model->getIndicadores($data_inicio, $data_fim);
                break;
            case 'evolucao':
                $response['data'] = $this->dre_model->getEvolucaoMensal(12);
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Análise Vertical e Horizontal
     */
    public function analise()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vDREAnalise')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('dre');
        }

        $ano = $this->input->get('ano') ?: date('Y');

        // Gerar DRE para cada mês do ano
        $meses = [];
        for ($i = 1; $i <= 12; $i++) {
            $data_inicio = sprintf('%s-%02d-01', $ano, $i);
            $data_fim = date('Y-m-t', strtotime($data_inicio));

            $dre = $this->dre_model->gerarDRE($data_inicio, $data_fim);
            $meses[$i] = [
                'mes' => $i,
                'mes_nome' => strftime('%B', strtotime($data_inicio)),
                'dre' => $dre
            ];
        }

        $this->data['meses'] = $meses;
        $this->data['ano'] = $ano;
        $this->data['view'] = 'dre/analise';

        $this->load->view('tema/topo', $this->data);
        $this->load->view($this->data['view'], $this->data);
        $this->load->view('tema/rodape');
    }
}
