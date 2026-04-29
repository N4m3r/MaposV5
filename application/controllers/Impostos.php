<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller: Impostos Simples Nacional
 * Gerencia configurações e relatórios de impostos retidos
 */
class Impostos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('impostos_model');
        $this->load->model('dre_model');
        $this->load->helper(['date', 'currency']);
    }

    /**
     * Dashboard de Impostos
     */
    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        // Período padrão: mês atual
        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');

        $relatorio = $this->impostos_model->getRelatorioImpostosDRE($data_inicio, $data_fim);

        $this->data['results'] = array_merge($relatorio, [
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
        ]);

        $this->data['view'] = 'impostos/dashboard';
        return $this->layout();
    }

    /**
     * Configurações de Impostos
     */
    public function configuracoes()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cImpostosConfig')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('impostos');
        }

        // Preenchimento automático via certificado digital
        $certificado_info = null;
        if (file_exists(APPPATH . 'models/Certificado_model.php')) {
            $this->load->model('certificado_model');
            $certificado = $this->certificado_model->getCertificadoAtivo();
            if ($certificado) {
                $certificado_info = [
                    'razao_social' => $certificado->razao_social ?? '',
                    'cnpj' => $certificado->cnpj ?? '',
                    'tipo' => $certificado->tipo ?? 'A1',
                    'validade' => $certificado->data_validade ?? '',
                    'anexo_sugerido' => null,
                ];

                // Buscar última consulta do Simples Nacional
                if ($this->db->table_exists('certificado_consultas')) {
                    $this->db->where('certificado_id', $certificado->id);
                    $this->db->where('tipo_consulta', 'SIMPLES_NACIONAL');
                    $this->db->where('sucesso', 1);
                    $this->db->order_by('data_consulta', 'DESC');
                    $consulta = $this->db->get('certificado_consultas', 1)->row();
                    if ($consulta && $consulta->dados_retorno) {
                        $dados = json_decode($consulta->dados_retorno, true);
                        if ($dados) {
                            $certificado_info['anexo_sugerido'] = $dados['anexo_sugerido'] ?? null;
                            $certificado_info['optante_simples'] = $dados['optante_simples'] ?? null;
                            $certificado_info['cnae_codigo'] = $dados['cnae_codigo'] ?? '';
                            $certificado_info['cnae_descricao'] = $dados['cnae_descricao'] ?? '';
                            $certificado_info['data_consulta'] = $consulta->data_consulta ?? '';
                        }
                    }
                }
            }
        }

        if ($this->input->post()) {
            $this->impostos_model->setConfig('IMPOSTO_ANEXO_PADRAO', $this->input->post('anexo_padrao'));
            $this->impostos_model->setConfig('IMPOSTO_FAIXA_ATUAL', $this->input->post('faixa_atual'));
            $this->impostos_model->setConfig('IMPOSTO_RETENCAO_AUTOMATICA', $this->input->post('retencao_automatica') ? '1' : '0');
            $this->impostos_model->setConfig('IMPOSTO_DRE_INTEGRACAO', $this->input->post('dre_integracao') ? '1' : '0');
            $this->impostos_model->setConfig('IMPOSTO_ISS_MUNICIPAL', $this->input->post('iss_municipal'));
            $this->impostos_model->setConfig('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL', $this->input->post('codigo_tributacao_nacional'));
            $this->impostos_model->setConfig('IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL', $this->input->post('codigo_tributacao_municipal'));
            $this->impostos_model->setConfig('IMPOSTO_DESCRICAO_SERVICO', $this->input->post('descricao_servico'));

            $this->session->set_flashdata('success', 'Configurações atualizadas!');
            redirect('impostos/configuracoes');
        }

        $this->data['configs'] = [
            'anexo_padrao' => $this->impostos_model->getConfig('IMPOSTO_ANEXO_PADRAO') ?: 'III',
            'faixa_atual' => $this->impostos_model->getConfig('IMPOSTO_FAIXA_ATUAL') ?: '1',
            'retencao_automatica' => $this->impostos_model->getConfig('IMPOSTO_RETENCAO_AUTOMATICA') == '1',
            'dre_integracao' => $this->impostos_model->getConfig('IMPOSTO_DRE_INTEGRACAO') == '1',
            'iss_municipal' => $this->impostos_model->getConfig('IMPOSTO_ISS_MUNICIPAL') ?: '5.00',
            'codigo_tributacao_nacional' => $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL') ?: '010701',
            'codigo_tributacao_municipal' => $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL') ?: '100',
            'descricao_servico' => $this->impostos_model->getConfig('IMPOSTO_DESCRICAO_SERVICO') ?: 'Suporte técnico em informática, inclusive instalação, configuração e manutenção de programas de computação e bancos de dados.',
        ];

        $this->data['anexos'] = [
            'III' => 'Anexo III - Serviços em Geral',
            'IV' => 'Anexo IV - Construção e Serviços com ISS Próprio',
            'V' => 'Anexo V - Comércio e Indústria',
        ];

        $this->data['aliquotas_iii'] = $this->impostos_model->getAliquotasAnexo('III');
        $this->data['aliquotas_iv'] = $this->impostos_model->getAliquotasAnexo('IV');
        $this->data['aliquotas_v'] = $this->impostos_model->getAliquotasAnexo('V');
        $this->data['certificado_info'] = $certificado_info;

        $this->data['view'] = 'impostos/configuracoes';
        return $this->layout();
    }

    /**
     * Busca dados do certificado digital via AJAX
     */
    public function buscar_certificado()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        header('Content-Type: application/json; charset=utf-8');

        if (!file_exists(APPPATH . 'models/Certificado_model.php')) {
            echo json_encode(['success' => false, 'message' => 'Módulo de certificado digital não instalado']);
            return;
        }

        $this->load->model('certificado_model');
        $certificado = $this->certificado_model->getCertificadoAtivo();

        if (!$certificado) {
            echo json_encode(['success' => false, 'message' => 'Nenhum certificado digital configurado. Cadastre um certificado em Certificado Digital.']);
            return;
        }

        // Buscar dados da última consulta do Simples Nacional
        $dados_certificado = [
            'razao_social' => $certificado->razao_social ?? '',
            'cnpj' => $certificado->cnpj ?? '',
            'tipo' => $certificado->tipo ?? 'A1',
            'anexo_sugerido' => null,
            'optante_simples' => null,
            'cnae_codigo' => '',
            'cnae_descricao' => '',
        ];

        if ($this->db->table_exists('certificado_consultas')) {
            $this->db->where('certificado_id', $certificado->id);
            $this->db->where('tipo_consulta', 'SIMPLES_NACIONAL');
            $this->db->where('sucesso', 1);
            $this->db->order_by('data_consulta', 'DESC');
            $consulta = $this->db->get('certificado_consultas', 1)->row();

            if ($consulta && $consulta->dados_retorno) {
                $dados = json_decode($consulta->dados_retorno, true);
                if ($dados) {
                    $dados_certificado['anexo_sugerido'] = $dados['anexo_sugerido'] ?? null;
                    $dados_certificado['optante_simples'] = $dados['optante_simples'] ?? null;
                    $dados_certificado['cnae_codigo'] = $dados['cnae_codigo'] ?? '';
                    $dados_certificado['cnae_descricao'] = $dados['cnae_descricao'] ?? '';
                }
            }
        }

        echo json_encode(['success' => true, 'certificado' => $dados_certificado]);
    }

    /**
     * Simulador de Impostos
     */
    public function simulador()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('impostos');
        }

        $resultado = null;

        if ($this->input->post()) {
            $valor_bruto = floatval(str_replace(',', '.', str_replace('.', '', $this->input->post('valor_bruto'))));
            $anexo = $this->input->post('anexo');
            $faixa = $this->input->post('faixa');

            $resultado = $this->impostos_model->calcularImpostos($valor_bruto, $anexo, $faixa);
            $resultado['valor_bruto'] = $valor_bruto;
        }

        $this->data['resultado'] = $resultado;
        $this->data['anexos'] = [
            'III' => 'Anexo III - Serviços em Geral',
            'IV' => 'Anexo IV - Construção',
            'V' => 'Anexo V - Comércio e Indústria',
        ];
        $this->data['aliquotas_iii'] = $this->impostos_model->getAliquotasAnexo('III');
        $this->data['aliquotas_iv'] = $this->impostos_model->getAliquotasAnexo('IV');
        $this->data['view'] = 'impostos/simulador';
        return $this->layout();
    }

    /**
     * Relatório detalhado
     */
    public function relatorio()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostosRelatorio')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('impostos');
        }

        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');

        $relatorio = $this->impostos_model->getRelatorioImpostosDRE($data_inicio, $data_fim);

        $this->data['results'] = array_merge($relatorio, [
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
        ]);

        $this->data['view'] = 'impostos/relatorio';
        return $this->layout();
    }

    /**
     * Exportar relatório
     */
    public function exportar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostosExportar')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('impostos');
        }

        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');

        $csv = $this->impostos_model->exportarRelatorio($data_inicio, $data_fim);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=impostos_' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');
        foreach ($csv as $linha) {
            fputcsv($output, $linha);
        }
        fclose($output);
    }

    /**
     * API JSON para cálculo de impostos
     * Usada na geração de boletos
     */
    public function api_calcular()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $valor_bruto = floatval($this->input->post('valor'));
        $anexo = $this->input->post('anexo') ?: null;
        $faixa = $this->input->post('faixa') ?: null;

        $calculos = $this->impostos_model->calcularImpostos($valor_bruto, $anexo, $faixa);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $calculos
        ]);
    }

    /**
     * Lista todas as retenções
     */
    public function retencoes()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('impostos');
        }

        $data_inicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ?: date('Y-m-t');

        $this->data['retencoes'] = $this->impostos_model->getRetencoes($data_inicio, $data_fim);
        $this->data['totais'] = $this->impostos_model->getTotaisImpostos($data_inicio, $data_fim);
        $this->data['data_inicio'] = $data_inicio;
        $this->data['data_fim'] = $data_fim;
        $this->data['view'] = 'impostos/retencoes';
        return $this->layout();
    }

    /**
     * Atualiza status de uma retenção
     */
    public function atualizar_status($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eImpostos')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('impostos');
        }

        $status = $this->input->post('status');
        $observacao = $this->input->post('observacao');

        $this->impostos_model->atualizarStatusRetencao($id, $status, $observacao);

        $this->session->set_flashdata('success', 'Status atualizado!');
        redirect('impostos/retencoes');
    }
}
