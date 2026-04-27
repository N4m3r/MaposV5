<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: NFS-e para OS
 * Gerencia emissão e controle de notas fiscais de serviço vinculadas a ordens de serviço
 */

class Nfse_os extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('nfse_emitida_model');
        $this->load->model('boleto_os_model');
        $this->load->model('os_model');
        $this->load->model('impostos_model');
        $this->load->helper(['date', 'currency']);
    }

    /**
     * Dashboard de NFS-e
     */
    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão para acessar NFS-e.');
            redirect(base_url());
        }

        // Resumo
        $this->data['resumo_nfse'] = $this->nfse_emitida_model->getResumo('mes_atual');
        $this->data['resumo_boleto'] = $this->boleto_os_model->getResumo('mes_atual');

        // Boletos vencidos
        $this->data['vencidos'] = $this->boleto_os_model->getVencidos();

        // Verificar boletos vencidos
        $this->boleto_os_model->verificarVencidos();

        $this->data['view'] = 'nfse_os/dashboard';
        return $this->layout();
    }

    /**
     * Emitir NFS-e para uma OS
     */
    public function emitir($os_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cNFSe')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['error' => 'Sem permissão']);
                return;
            }
            $this->session->set_flashdata('error', 'Sem permissão para emitir NFS-e.');
            redirect('os');
        }

        if (!$os_id) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['error' => 'OS não informada']);
                return;
            }
            redirect('os');
        }

        // Verificar se OS existe
        $os = $this->os_model->getById($os_id);
        if (!$os) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['error' => 'OS não encontrada']);
                return;
            }
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('os');
        }

        // Dados do POST
        $dados = [
            'valor_servicos' => $this->input->post('valor_servicos') ?? $os->valorTotal,
            'valor_deducoes' => $this->input->post('valor_deducoes') ?? 0,
            'descricao_servico' => $this->input->post('descricao_servico') ?? '',
            'regime_tributario' => $this->input->post('regime_tributario') ?? 'simples_nacional',
            'valor_das' => $this->input->post('valor_das') ?? null,
            'retem_iss' => $this->input->post('retem_iss') ? 1 : 0,
            'retem_irrf' => $this->input->post('retem_irrf') ? 1 : 0,
            'retem_pis' => $this->input->post('retem_pis') ? 1 : 0,
            'retem_cofins' => $this->input->post('retem_cofins') ? 1 : 0,
            'retem_csll' => $this->input->post('retem_csll') ? 1 : 0,
            'valor_retencao_iss' => $this->input->post('valor_retencao_iss') ?? 0,
            'valor_retencao_irrf' => $this->input->post('valor_retencao_irrf') ?? 0,
            'valor_retencao_pis' => $this->input->post('valor_retencao_pis') ?? 0,
            'valor_retencao_cofins' => $this->input->post('valor_retencao_cofins') ?? 0,
            'valor_retencao_csll' => $this->input->post('valor_retencao_csll') ?? 0,
            'valor_total_retencao' => $this->input->post('valor_total_retencao') ?? 0,
            'competencia' => $this->input->post('competencia') ?: date('Y-m-01'),
        ];

        // Ambiente (homologação/produção) do certificado ativo
        $this->load->model('certificado_model');
        $certificado = $this->certificado_model->getCertificadoAtivo();
        $dados['ambiente'] = $certificado->ambiente ?? 'homologacao';

        // Emitir NFSe
        $resultado = $this->nfse_emitida_model->emitir($os_id, $dados);

        if (isset($resultado['success'])) {
            // Retornar JSON se for AJAX
            if ($this->input->is_ajax_request()) {
                echo json_encode($resultado);
                return;
            }

            $this->session->set_flashdata('success', $resultado['message']);

            // Perguntar se deseja gerar boleto
            if ($this->input->post('gerar_boleto')) {
                redirect('nfse_os/gerar_boleto/' . $os_id . '/' . $resultado['nfse_id']);
            }
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode($resultado);
                return;
            }
            $this->session->set_flashdata('error', $resultado['error']);
        }

        redirect('os/visualizar/' . $os_id);
    }

    /**
     * Gerar boleto para OS
     */
    public function gerar_boleto($os_id = null, $nfse_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cBoletoOS')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['error' => 'Sem permissão']);
                return;
            }
            $this->session->set_flashdata('error', 'Sem permissão para gerar boleto.');
            redirect('os');
        }

        if (!$os_id) {
            redirect('os');
        }

        // Configurações
        $config = [
            'data_vencimento' => $this->input->post('data_vencimento') ?? date('Y-m-d', strtotime('+5 days')),
            'instrucoes' => $this->input->post('instrucoes') ?? 'Pagável em qualquer banco até o vencimento.',
            'gateway' => $this->input->post('gateway') ?? null,
            'valor_integral' => $this->input->post('valor_integral') ? 1 : 0
        ];

        // Gerar boleto
        $resultado = $this->boleto_os_model->gerar($os_id, $nfse_id, $config);

        if (isset($resultado['success'])) {
            // Se tem gateway configurado, processar (whitelist para evitar path traversal)
            $gatewaysPermitidos = ['asaas', 'mercado_pago', 'pagseguro', 'gerencianet'];
            if ($config['gateway'] && in_array($config['gateway'], $gatewaysPermitidos)) {
                $this->load->library("Gateways/{$config['gateway']}", null, 'PaymentGateway');
                try {
                    $gateway_result = $this->PaymentGateway->gerarBoletoOS(
                        $resultado['boleto_id'],
                        $resultado['valor_liquido']
                    );

                    if (isset($gateway_result['success'])) {
                        $this->boleto_os_model->confirmarEmissao($resultado['boleto_id'], $gateway_result);
                        $resultado['gateway'] = true;
                        $resultado['linha_digitavel'] = $gateway_result['linha_digitavel'] ?? null;
                        $resultado['link_pdf'] = $gateway_result['link_pdf'] ?? null;
                    }
                } catch (Exception $e) {
                    log_error('Erro ao gerar boleto no gateway: ' . $e->getMessage());
                }
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode($resultado);
                return;
            }

            $this->session->set_flashdata('success', $resultado['message']);
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode($resultado);
                return;
            }
            $this->session->set_flashdata('error', $resultado['error']);
        }

        redirect('os/visualizar/' . $os_id);
    }

    /**
     * Calcular impostos para preview
     */
    public function calcular_impostos()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Verificar permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        try {
            $valor = floatval($this->input->post('valor') ?: $this->input->get('valor'));
            if ($valor <= 0) {
                echo json_encode(['success' => false, 'message' => 'Valor inválido: ' . $valor]);
                return;
            }

            // Verificar se o model está carregado
            if (!isset($this->impostos_model) || !is_object($this->impostos_model)) {
                log_message('error', 'NFSe: impostos_model nao esta carregado');
                echo json_encode(['success' => false, 'message' => 'Modelo de impostos nao carregado. Contate o administrador.']);
                return;
            }

            $calculo = $this->impostos_model->calcularImpostos($valor);

            if (!$calculo) {
                log_message('error', 'NFSe: calcularImpostos retornou false para valor=' . $valor);
                echo json_encode([
                    'success' => false,
                    'message' => 'Configuração tributária não encontrada. Execute a migration SQL ou configure os impostos em Configurações do Sistema.'
                ]);
                return;
            }

            // Detectar regime tributário
            $regime = $this->impostos_model->getConfiguracaoTributacao();
            $regime_tributario = $regime['regime'] ?? 'simples_nacional';

            // Calcular DAS se Simples Nacional
            $valor_das = null;
            if ($regime_tributario === 'simples_nacional') {
                $valor_das = $calculo['valor_total_impostos'] ?? ($valor * 0.06);
            }

            // Calcular retenções se solicitado
            $retencoes = [];
            $retem = [
                'iss' => $this->input->post('retem_iss') ? true : false,
                'irrf' => $this->input->post('retem_irrf') ? true : false,
                'pis' => $this->input->post('retem_pis') ? true : false,
                'cofins' => $this->input->post('retem_cofins') ? true : false,
                'csll' => $this->input->post('retem_csll') ? true : false,
            ];
            if (array_filter($retem)) {
                $retencoes = $this->impostos_model->calcularRetencoes($valor, $retem);
            }

            // Valor líquido da NFS-e: valor bruto (impostos do prestador não reduzem)
            // Para Simples Nacional, o DAS é pago mensalmente pelo prestador
            $valor_liquido_nfse = $valor;
            if (!empty($retencoes['valor_total_retencao'])) {
                $valor_liquido_nfse -= floatval($retencoes['valor_total_retencao']);
            }

            echo json_encode([
                'success' => true,
                'valor_bruto' => $valor,
                'valor_liquido' => $valor_liquido_nfse,
                'impostos' => $calculo,
                'regime_tributario' => $regime_tributario,
                'valor_das' => $valor_das,
                'retencoes' => $retencoes,
            ]);
        } catch (Exception $e) {
            log_message('error', 'NFSe calcular_impostos exception: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Pré-visualização da NFS-e em PDF
     * Gera PDF com dados do prestador, tomador, impostos e QR Code PIX
     */
    public function preview($os_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        if (!$os_id) {
            redirect('os');
        }

        $os = $this->os_model->getById($os_id);
        if (!$os) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('os');
        }

        // Parâmetros via GET (preview antes da emissão)
        $valor_servicos = floatval($this->input->get('valor_servicos')) ?: floatval($os->valorTotal ?? 0);
        $valor_deducoes = floatval($this->input->get('valor_deducoes')) ?: 0;
        $descricao_servico = $this->input->get('descricao_servico') ?: '';

        // Calcular impostos (para exibição apenas — não reduzem valor da NFS-e)
        $calculo = $this->impostos_model->calcularImpostos($valor_servicos);
        $valor_liquido = $valor_servicos - $valor_deducoes;

        // Emitente
        $this->load->model('mapos_model');
        $emitente = $this->mapos_model->getEmitente();

        // Tributação
        $tributacao = $this->impostos_model->getConfiguracaoTributacao();

        // Ambiente do certificado ativo
        $this->load->model('certificado_model');
        $certificado = $this->certificado_model->getCertificadoAtivo();
        $ambiente = $certificado->ambiente ?? 'homologacao';

        // QR Code PIX (valor líquido)
        $pix_key = $this->data['configuration']['pix_key'] ?? '';
        $qrCodePix = null;
        $chaveFormatada = '';
        if (!empty($pix_key) && !empty($emitente) && $valor_liquido > 0) {
            $qrCodePix = $this->os_model->getQrCodeCustom($valor_liquido, $os_id, $pix_key, $emitente);
            $chaveFormatada = $this->formatarChavePix($pix_key);
        }

        // Dados para a view
        $data = [
            'os' => $os,
            'emitente' => $emitente,
            'tributacao' => $tributacao,
            'valor_servicos' => $valor_servicos,
            'valor_deducoes' => $valor_deducoes,
            'valor_liquido' => $valor_liquido,
            'impostos' => $calculo,
            'descricao_servico' => $descricao_servico ?: $tributacao['descricao_servico'],
            'qrCodePix' => $qrCodePix,
            'chaveFormatada' => $chaveFormatada,
            'is_preview' => true,
            'ambiente' => $ambiente,
        ];

        $this->load->helper('mpdf');
        $html = $this->load->view('nfse_os/preview_nfse', $data, true);
        pdf_create($html, 'NFSe_Preview_OS_' . $os_id, true);
    }

    /**
     * Imprimir NFS-e já emitida em PDF
     * Gera PDF com dados do prestador, tomador, impostos e QR Code PIX
     */
    public function imprimir_nfse($nfse_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        if (!$nfse_id) {
            redirect('nfse_os');
        }

        $nfse = $this->nfse_emitida_model->getById($nfse_id);
        if (!$nfse) {
            $this->session->set_flashdata('error', 'NFS-e não encontrada.');
            redirect('nfse_os');
        }

        $os = $this->os_model->getById($nfse->os_id);
        if (!$os) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('nfse_os');
        }

        // Emitente
        $this->load->model('mapos_model');
        $emitente = $this->mapos_model->getEmitente();

        // Tributação
        $tributacao = $this->impostos_model->getConfiguracaoTributacao();

        // Ambiente do certificado ativo
        $this->load->model('certificado_model');
        $certificado = $this->certificado_model->getCertificadoAtivo();
        $ambiente = $certificado->ambiente ?? 'homologacao';

        // Impostos da NFS-e emitida
        $impostos = [
            'iss' => $nfse->valor_iss ?? 0,
            'pis' => $nfse->valor_pis ?? 0,
            'cofins' => $nfse->valor_cofins ?? 0,
            'irrf' => $nfse->valor_irrf ?? 0,
            'csll' => $nfse->valor_csll ?? 0,
            'inss' => $nfse->valor_inss ?? 0,
            'valor_total_impostos' => $nfse->valor_total_impostos ?? 0,
        ];

        // QR Code PIX (valor líquido)
        $pix_key = $this->data['configuration']['pix_key'] ?? '';
        $qrCodePix = null;
        $chaveFormatada = '';
        if (!empty($pix_key) && !empty($emitente) && $nfse->valor_liquido > 0) {
            $qrCodePix = $this->os_model->getQrCodeCustom($nfse->valor_liquido, $nfse->os_id, $pix_key, $emitente);
            $chaveFormatada = $this->formatarChavePix($pix_key);
        }

        $data = [
            'os' => $os,
            'emitente' => $emitente,
            'tributacao' => $tributacao,
            'valor_servicos' => $nfse->valor_servicos ?? 0,
            'valor_deducoes' => $nfse->valor_deducoes ?? 0,
            'valor_liquido' => $nfse->valor_liquido ?? 0,
            'impostos' => $impostos,
            'descricao_servico' => $tributacao['descricao_servico'],
            'qrCodePix' => $qrCodePix,
            'chaveFormatada' => $chaveFormatada,
            'is_preview' => false,
            'nfse_numero' => $nfse->numero_nfse ?? null,
            'ambiente' => $ambiente,
        ];

        $this->load->helper('mpdf');
        $html = $this->load->view('nfse_os/preview_nfse', $data, true);
        pdf_create($html, 'NFSe_' . ($nfse->numero_nfse ?? $nfse_id) . '_OS_' . $nfse->os_id, true);
    }

    /**
     * Pré-visualização do Boleto em PDF (folha A4)
     * Gera um documento com layout de boleto impresso
     */
    public function preview_boleto($boleto_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        if (!$boleto_id) {
            redirect('nfse_os');
        }

        $boleto = $this->boleto_os_model->getById($boleto_id);
        if (!$boleto) {
            $this->session->set_flashdata('error', 'Boleto não encontrado.');
            redirect('nfse_os');
        }

        $os = $this->os_model->getById($boleto->os_id);
        if (!$os) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('nfse_os');
        }

        // Emitente (cedente)
        $this->load->model('mapos_model');
        $emitente = $this->mapos_model->getEmitente();

        // Dados da NFSe vinculada
        $nfse = null;
        if ($boleto->nfse_id) {
            $nfse = $this->nfse_emitida_model->getById($boleto->nfse_id);
        }

        $data = [
            'boleto' => $boleto,
            'os' => $os,
            'emitente' => $emitente,
            'nfse' => $nfse,
            'is_preview' => true,
        ];

        $this->load->helper('mpdf');
        $html = $this->load->view('nfse_os/preview_boleto', $data, true);
        pdf_create($html, 'Boleto_OS_' . $boleto->os_id . '_' . ($boleto->nosso_numero ?? $boleto_id), true);
    }

    /**
     * Formatar chave PIX para exibição
     */
    private function formatarChavePix($chave)
    {
        $chave = preg_replace('/\D/', '', $chave);
        if (strlen($chave) == 14) {
            return substr($chave, 0, 2) . '.' . substr($chave, 2, 3) . '.' . substr($chave, 5, 3) . '/' . substr($chave, 8, 4) . '-' . substr($chave, 12, 2);
        }
        if (strlen($chave) == 11) {
            return substr($chave, 0, 3) . '.' . substr($chave, 3, 3) . '.' . substr($chave, 6, 3) . '-' . substr($chave, 9, 2);
        }
        return $chave ?: $this->data['configuration']['pix_key'] ?? '';
    }

    /**
     * Visualizar NFS-e
     */
    public function visualizar($nfse_id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        $nfse = $this->nfse_emitida_model->getById($nfse_id);
        if (!$nfse) {
            $this->session->set_flashdata('error', 'NFS-e não encontrada.');
            redirect('nfse_os');
        }

        $this->data['nfse'] = $nfse;
        $this->data['os'] = $this->os_model->getById($nfse->os_id);
        $this->data['boleto'] = $nfse->cobranca_id ? $this->boleto_os_model->getById($nfse->cobranca_id) : null;
        $this->data['view'] = 'nfse_os/visualizar';

        return $this->layout();
    }

    /**
     * Cancelar NFS-e
     */
    public function cancelar_nfse($nfse_id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('nfse_os');
        }

        $motivo = $this->input->post('motivo') ?? 'Cancelamento solicitado pelo usuário';
        $resultado = $this->nfse_emitida_model->cancelar($nfse_id, $motivo);

        if (isset($resultado['success'])) {
            $this->session->set_flashdata('success', 'NFS-e cancelada com sucesso.');
        } else {
            $this->session->set_flashdata('error', $resultado['error']);
        }

        redirect('nfse_os');
    }

    /**
     * Cancelar Boleto
     */
    public function cancelar_boleto($boleto_id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eBoletoOS')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('nfse_os');
        }

        $resultado = $this->boleto_os_model->cancelar($boleto_id);

        if (isset($resultado['success'])) {
            $this->session->set_flashdata('success', 'Boleto cancelado com sucesso.');
        } else {
            $this->session->set_flashdata('error', $resultado['error']);
        }

        redirect('nfse_os');
    }

    /**
     * Registrar pagamento manual
     */
    public function registrar_pagamento($boleto_id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eBoletoOS')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('nfse_os');
        }

        $dados = [
            'data_pagamento' => $this->input->post('data_pagamento') ?? date('Y-m-d'),
            'valor_pago' => $this->input->post('valor_pago'),
            'multa' => $this->input->post('multa') ?? 0,
            'juros' => $this->input->post('juros') ?? 0
        ];

        $resultado = $this->boleto_os_model->registrarPagamento($boleto_id, $dados);

        if (isset($resultado['success'])) {
            $this->session->set_flashdata('success', 'Pagamento registrado com sucesso.');
        } else {
            $this->session->set_flashdata('error', $resultado['error']);
        }

        redirect('nfse_os');
    }

    /**
     * Relatório de NFS-e e Boletos
     */
    public function relatorio()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'rNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        // Filtros
        $filtros = [
            'data_inicio' => $this->input->get('data_inicio') ?: date('Y-m-01'),
            'data_fim' => $this->input->get('data_fim') ?: date('Y-m-t'),
            'status_nfse' => $this->input->get('status_nfse') ?: null,
            'status_boleto' => $this->input->get('status_boleto') ?: null
        ];

        // Buscar dados
        $this->data['nfse_lista'] = $this->nfse_emitida_model->listar($filtros, 100, 0);
        $this->data['boletos_lista'] = $this->boleto_os_model->listar($filtros, 100, 0);
        $this->data['vencidos'] = $this->boleto_os_model->getVencidos();

        // Estatísticas
        $this->data['total_nfse_emitida'] = array_sum(array_column(
            array_filter($this->data['nfse_lista'], function($n) { return $n->situacao == 'Emitida'; }),
            'valor_liquido'
        ));

        $this->data['total_boleto_pago'] = array_sum(array_column(
            array_filter($this->data['boletos_lista'], function($b) { return $b->status == 'Pago'; }),
            'valor_liquido'
        ));

        $this->data['filtros'] = $filtros;
        $this->data['view'] = 'nfse_os/relatorio';

        return $this->layout();
    }

    /**
     * API para retornar dados de NFSe e Boleto de uma OS
     */
    public function api_get_os_dados($os_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        $nfse = $this->nfse_emitida_model->getByOsId($os_id);
        $boleto = $this->boleto_os_model->getAtivoByOsId($os_id);
        $historico_nfse = $this->nfse_emitida_model->getAllByOsId($os_id);
        $historico_boleto = $this->boleto_os_model->getAllByOsId($os_id);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'nfse' => $nfse,
            'boleto' => $boleto,
            'historico_nfse' => $historico_nfse,
            'historico_boleto' => $historico_boleto
        ]);
    }

    /**
     * Enviar boleto por email
     */
    public function enviar_boleto_email($boleto_id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eBoletoOS')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('nfse_os');
        }

        $boleto = $this->boleto_os_model->getById($boleto_id);
        if (!$boleto) {
            $this->session->set_flashdata('error', 'Boleto não encontrado.');
            redirect('nfse_os');
        }

        $os = $this->os_model->getById($boleto->os_id);

        if (empty($os->email) || !filter_var($os->email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Cliente não possui e-mail válido cadastrado.');
            redirect('os/visualizar/' . $boleto->os_id);
        }

        // Enviar email
        $this->load->library('email');
        $this->email->from($this->config->item('email_smtp_user'), 'Sistema MAP-OS');
        $this->email->to($os->email);
        $this->email->subject('Boleto OS #' . $boleto->os_id);

        $nomeCliente = htmlspecialchars($os->nomeCliente ?? 'Cliente', ENT_QUOTES, 'UTF-8');
        $mensagem = "Olá {$nomeCliente},\n\n";
        $mensagem .= "Segue o boleto referente à OS #{$boleto->os_id}.\n\n";
        $mensagem .= "Valor: R$ " . number_format($boleto->valor_liquido, 2, ',', '.') . "\n";
        $mensagem .= "Vencimento: " . date('d/m/Y', strtotime($boleto->data_vencimento)) . "\n";
        $mensagem .= "Linha Digitável: {$boleto->linha_digitavel}\n\n";
        $mensagem .= "Caso já tenha efetuado o pagamento, desconsidere este email.\n\n";
        $mensagem .= "Atenciosamente,\nEquipe MAP-OS";

        $this->email->message($mensagem);

        if ($boleto->pdf_path && file_exists($boleto->pdf_path)) {
            $this->email->attach($boleto->pdf_path);
        }

        if ($this->email->send()) {
            $this->session->set_flashdata('success', 'Boleto enviado por email com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Erro ao enviar email.');
        }

        redirect('os/visualizar/' . $boleto->os_id);
    }

    // ==================== API NFS-e NACIONAL ====================

    /**
     * Emitir NFS-e via API Nacional (Sistema Nacional NFS-e)
     * Chamado via AJAX pelo wizard
     */
    public function emitir_nfse_api($os_id = null)
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cNFSe')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão para emitir NFS-e.']);
            return;
        }

        if (!$os_id) {
            echo json_encode(['success' => false, 'message' => 'OS não informada.']);
            return;
        }

        // Buscar OS
        $os = $this->os_model->getById($os_id);
        if (!$os) {
            echo json_encode(['success' => false, 'message' => 'OS não encontrada.']);
            return;
        }

        // Carregar certificado digital
        $this->load->model('certificado_model');
        $pemPaths = $this->certificado_model->extrairCertificadoPem();

        if (!$pemPaths || isset($pemPaths['error'])) {
            $msg = isset($pemPaths['error']) ? $pemPaths['error'] : 'Certificado digital não configurado. Cadastre um certificado em Certificado Digital.';
            echo json_encode(['success' => false, 'message' => $msg]);
            return;
        }

        try {
            // Carregar config
            $this->config->load('nfse_nacional', true);
            $nfseConfig = $this->config->item('nfse_nacional');

            // Instanciar bibliotecas NFS-e Nacional
            $this->load->library('Nfse/NfseConfig');
            $this->load->library('Nfse/DpsXmlBuilder');
            $this->load->library('Nfse/XmlSigner');
            $this->load->library('Nfse/NfseNacional');

            // Carregar dados do emitente
            $this->load->model('mapos_model');
            $emitente = $this->mapos_model->getEmitente();

            // Montar dados do prestador
            $prestador = [
                'cnpj' => $pemPaths['cnpj'],
                'razao_social' => $pemPaths['razao_social'] ?? ($emitente->nome ?? ''),
                'im' => $emitente->im ?? '',
                'cnae' => $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL') ?: '010701',
                'email' => $emitente->email ?? '',
                'telefone' => $emitente->telefone ?? '',
                'endereco' => [
                    'logradouro' => $emitente->rua ?? '',
                    'numero' => $emitente->numero ?? '',
                    'complemento' => $emitente->complemento ?? '',
                    'bairro' => $emitente->bairro ?? '',
                    'codigo_municipio' => $nfseConfig['nfse_codigo_municipio'] ?? '1302603',
                    'uf' => 'AM',
                    'cep' => preg_replace('/\D/', '', $emitente->cep ?? ''),
                ],
            ];

            // Montar dados do tomador (cliente da OS)
            $this->load->model('clientes_model');
            $cliente = $this->clientes_model->getById($os->clientes_id ?? 0);

            $tomador = [];
            if ($cliente) {
                $cpfCnpj = preg_replace('/\D/', '', $cliente->cpfCnpj ?? ($cliente->cnpj ?? ''));
                $tomador = [
                    'cpf_cnpj' => $cpfCnpj,
                    'razao_social' => $cliente->nomeCliente ?? '',
                    'email' => $cliente->email ?? '',
                    'telefone' => $cliente->telefone ?? '',
                    'endereco' => [
                        'logradouro' => $cliente->rua ?? '',
                        'numero' => $cliente->numero ?? '',
                        'complemento' => $cliente->complemento ?? '',
                        'bairro' => $cliente->bairro ?? '',
                        'codigo_municipio' => $cliente->codigo_municipio_ibge ?? '1302603',
                        'uf' => $cliente->estado ?? 'AM',
                        'cep' => preg_replace('/\D/', '', $cliente->cep ?? ''),
                    ],
                ];
            }

            // Montar dados do serviço
            $valorServicos = floatval($this->input->post('valor_servicos') ?: $os->valorTotal);
            $valorDeducoes = floatval($this->input->post('valor_deducoes') ?: 0);
            $descricaoServico = $this->input->post('descricao_servico') ?: ($this->impostos_model->getConfig('IMPOSTO_DESCRICAO_SERVICO') ?: 'Serviços de informática');

            // Calcular impostos
            $calculo = $this->impostos_model->calcularImpostos($valorServicos);

            $servico = [
                'descricao' => $descricaoServico,
                'cnae' => $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL') ?: '010701',
                'codigo_tributacao_nacional' => $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL') ?: '010701',
                'codigo_tributacao_municipal' => $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL') ?: '100',
                'valor_servicos' => $valorServicos,
                'valor_deducoes' => $valorDeducoes,
                'valor_iss' => $calculo['iss'] ?? 0,
                'aliquota_iss' => floatval($this->impostos_model->getConfig('IMPOSTO_ISS_MUNICIPAL') ?: 5.00),
                'valor_pis' => $calculo['pis'] ?? 0,
                'valor_cofins' => $calculo['cofins'] ?? 0,
                'valor_irrf' => $calculo['irpj'] ?? 0,
                'valor_csll' => $calculo['csll'] ?? 0,
                'valor_inss' => $calculo['inss'] ?? 0,
                'valor_liquido' => $valorServicos - $valorDeducoes,
                'iss_retido' => $this->input->post('retem_iss') ? true : false,
                'pis_retido' => $this->input->post('retem_pis') ? true : false,
                'cofins_retido' => $this->input->post('retem_cofins') ? true : false,
                'irrf_retido' => $this->input->post('retem_irrf') ? true : false,
                'csll_retido' => $this->input->post('retem_csll') ? true : false,
                'inss_retido' => false,
            ];

            // Valores de retenção
            if ($servico['iss_retido']) {
                $servico['valor_iss_retido'] = floatval($this->input->post('valor_retencao_iss') ?: 0);
            }

            // Tributacao
            $regimeTributario = $this->input->post('regime_tributario') ?: ($this->impostos_model->getConfig('IMPOSTO_REGIME_TRIBUTARIO') ?: 'simples_nacional');
            $tributacao = [
                'natureza_operacao' => $nfseConfig['nfse_natureza_operacao'] ?? '1',
                'optante_simples' => ($regimeTributario === 'simples_nacional'),
                'regime_especial' => $nfseConfig['nfse_regime_especial'] ?? '0',
                'incentivador_cultural' => $nfseConfig['nfse_incentivador_cultural'] ?? '0',
                'aliquota_iss' => floatval($this->impostos_model->getConfig('IMPOSTO_ISS_MUNICIPAL') ?: 5.00),
            ];

            // Competência
            $competencia = $this->input->post('competencia') ?: date('Y-m-d');

            // Gerar XML DPS
            $xmlBuilder = new DpsXmlBuilder([
                'codigo_municipio' => $nfseConfig['nfse_codigo_municipio'] ?? '1302603',
                'codigo_uf' => $nfseConfig['nfse_codigo_uf'] ?? '13',
            ]);

            $dadosDps = [
                'prestador' => $prestador,
                'tomador' => $tomador,
                'servico' => $servico,
                'tributacao' => $tributacao,
                'competencia' => $competencia,
            ];

            $xmlDps = $xmlBuilder->gerarDps($dadosDps);

            if (!$xmlDps) {
                echo json_encode(['success' => false, 'message' => 'Erro ao gerar XML DPS.']);
                $this->certificado_model->limparPemTemporarios($pemPaths);
                return;
            }

            // Assinar XML DPS
            $xmlSigner = new XmlSigner();
            $xmlAssinado = $xmlSigner->assinarXml($xmlDps, $pemPaths['cert'], $pemPaths['key']);

            if (!$xmlAssinado) {
                echo json_encode(['success' => false, 'message' => 'Erro ao assinar XML DPS. Verifique o certificado digital.']);
                $this->certificado_model->limparPemTemporarios($pemPaths);
                return;
            }

            // Enviar para API Nacional
            $nfseApi = new NfseNacional([
                'ambiente' => $pemPaths['ambiente'] ?? 'homologacao',
                'cert_pem' => $pemPaths['cert'],
                'key_pem' => $pemPaths['key'],
                'ca_path' => $nfseConfig['nfse_ca_path'] ?? FCPATH . 'assets/certs/ac-icp-brasil.pem',
                'cnpj' => $pemPaths['cnpj'],
                'timeout' => $nfseConfig['nfse_timeout'] ?? 60,
            ]);

            $resultado = $nfseApi->emitir($xmlAssinado);

            // Limpar arquivos PEM temporários
            $this->certificado_model->limparPemTemporarios($pemPaths);

            if ($resultado['success']) {
                // Salvar NFS-e no banco
                // Calcular valores de retenção para salvar no banco
                $retencoesPost = [
                    'iss' => $this->input->post('retem_iss') ? true : false,
                    'irrf' => $this->input->post('retem_irrf') ? true : false,
                    'pis' => $this->input->post('retem_pis') ? true : false,
                    'cofins' => $this->input->post('retem_cofins') ? true : false,
                    'csll' => $this->input->post('retem_csll') ? true : false,
                ];
                $retencoesCalculadas = $this->impostos_model->calcularRetencoes($valorServicos, $retencoesPost);

                $dadosNfse = [
                    'valor_servicos' => $valorServicos,
                    'valor_deducoes' => $valorDeducoes,
                    'valor_liquido' => $servico['valor_liquido'],
                    'descricao_servico' => $descricaoServico,
                    'regime_tributario' => $this->input->post('regime_tributario') ?: 'simples_nacional',
                    'competencia' => $competencia,
                    'retem_iss' => $servico['iss_retido'] ? 1 : 0,
                    'retem_irrf' => $servico['irrf_retido'] ? 1 : 0,
                    'retem_pis' => $servico['pis_retido'] ? 1 : 0,
                    'retem_cofins' => $servico['cofins_retido'] ? 1 : 0,
                    'retem_csll' => $servico['csll_retido'] ? 1 : 0,
                    'valor_retencao_iss' => $retencoesCalculadas['valor_retencao_iss'] ?? 0,
                    'valor_retencao_irrf' => $retencoesCalculadas['valor_retencao_irrf'] ?? 0,
                    'valor_retencao_pis' => $retencoesCalculadas['valor_retencao_pis'] ?? 0,
                    'valor_retencao_cofins' => $retencoesCalculadas['valor_retencao_cofins'] ?? 0,
                    'valor_retencao_csll' => $retencoesCalculadas['valor_retencao_csll'] ?? 0,
                    'valor_total_retencao' => $retencoesCalculadas['valor_total_retencao'] ?? 0,
                    'ambiente' => $pemPaths['ambiente'] ?? 'homologacao',
                ];

                $emitirResult = $this->nfse_emitida_model->emitir($os_id, $dadosNfse);

                if (isset($emitirResult['success']) && $emitirResult['success']) {
                    // Confirmar emissão com dados da API
                    $dadosConfirmar = [
                        'numero' => $resultado['numero'] ?? '',
                        'chave' => $resultado['chave_acesso'] ?? '',
                        'codigo_verificacao' => $resultado['codigo_verificacao'] ?? '',
                        'protocolo' => $resultado['protocolo'] ?? '',
                        'link_impressao' => $resultado['url_danfe'] ?? '',
                        'xml_path' => null,
                    ];

                    $this->nfse_emitida_model->confirmarEmissaoApi($emitirResult['nfse_id'], $dadosConfirmar, $xmlAssinado, $resultado['xml_nfse'] ?? '');
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'NFS-e emitida com sucesso via API Nacional!',
                    'chave_acesso' => $resultado['chave_acesso'] ?? '',
                    'numero' => $resultado['numero'] ?? '',
                    'protocolo' => $resultado['protocolo'] ?? '',
                    'url_danfe' => $resultado['url_danfe'] ?? '',
                    'ambiente' => $pemPaths['ambiente'] ?? 'homologacao',
                    'nfse_id' => $emitirResult['nfse_id'] ?? null,
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $resultado['message'] ?? 'Erro ao emitir NFS-e na API Nacional.',
                    'httpCode' => $resultado['httpCode'] ?? 0,
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'NFS-e Nacional emitir_nfse_api exception: ' . $e->getMessage());
            // Limpar PEM temporários em caso de erro
            if (isset($pemPaths)) {
                $this->certificado_model->limparPemTemporarios($pemPaths);
            }
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancelar NFS-e via API Nacional
     */
    public function cancelar_nfse_api($nfse_id = null)
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNFSe')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        if (!$nfse_id) {
            echo json_encode(['success' => false, 'message' => 'ID da NFS-e não informado.']);
            return;
        }

        // Buscar NFS-e no banco
        $nfse = $this->nfse_emitida_model->getById($nfse_id);
        if (!$nfse) {
            echo json_encode(['success' => false, 'message' => 'NFS-e não encontrada.']);
            return;
        }

        if (empty($nfse->chave_acesso)) {
            echo json_encode(['success' => false, 'message' => 'NFS-e não possui chave de acesso. Não foi emitida via API Nacional.']);
            return;
        }

        if ($nfse->situacao === 'Cancelada') {
            echo json_encode(['success' => false, 'message' => 'NFS-e já está cancelada.']);
            return;
        }

        $motivo = $this->input->post('motivo') ?: 'Cancelamento solicitado pelo prestador';

        // Carregar certificado
        $this->load->model('certificado_model');
        $pemPaths = $this->certificado_model->extrairCertificadoPem();

        if (!$pemPaths || isset($pemPaths['error'])) {
            $msg = isset($pemPaths['error']) ? $pemPaths['error'] : 'Certificado digital não configurado.';
            echo json_encode(['success' => false, 'message' => $msg]);
            return;
        }

        try {
            // Config e instanciar API
            $this->config->load('nfse_nacional', true);
            $nfseConfig = $this->config->item('nfse_nacional');

            $this->load->library('Nfse/NfseNacional');
            $this->load->library('Nfse/XmlSigner');

            $nfseApi = new NfseNacional([
                'ambiente' => $nfse->ambiente ?? 'homologacao',
                'cert_pem' => $pemPaths['cert'],
                'key_pem' => $pemPaths['key'],
                'ca_path' => $nfseConfig['nfse_ca_path'] ?? FCPATH . 'assets/certs/ac-icp-brasil.pem',
                'cnpj' => $pemPaths['cnpj'],
                'timeout' => $nfseConfig['nfse_timeout'] ?? 60,
            ]);

            // Gerar XML de cancelamento
            $xmlEvento = $nfseApi->gerarXmlCancelamento($nfse->chave_acesso, $motivo);

            // Assinar evento
            $xmlSigner = new XmlSigner();
            $xmlAssinado = $xmlSigner->assinarEventoCancelamento($xmlEvento, $pemPaths['cert'], $pemPaths['key']);

            if (!$xmlAssinado) {
                echo json_encode(['success' => false, 'message' => 'Erro ao assinar XML de cancelamento.']);
                $this->certificado_model->limparPemTemporarios($pemPaths);
                return;
            }

            // Enviar cancelamento
            $resultado = $nfseApi->cancelar($nfse->chave_acesso, $motivo, $xmlAssinado);

            // Limpar PEM temporários
            $this->certificado_model->limparPemTemporarios($pemPaths);

            if ($resultado['success']) {
                // Atualizar no banco
                $this->nfse_emitida_model->registrarCancelamentoApi($nfse_id, $motivo, $resultado['data_cancelamento'] ?? date('Y-m-d H:i:s'));

                echo json_encode([
                    'success' => true,
                    'message' => 'NFS-e cancelada com sucesso na API Nacional.',
                    'protocolo' => $resultado['protocolo'] ?? '',
                    'data_cancelamento' => $resultado['data_cancelamento'] ?? '',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $resultado['message'] ?? 'Erro ao cancelar NFS-e na API Nacional.',
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'NFS-e Nacional cancelar_nfse_api exception: ' . $e->getMessage());
            if (isset($pemPaths)) {
                $this->certificado_model->limparPemTemporarios($pemPaths);
            }
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Consultar NFS-e via API Nacional
     */
    public function consultar_nfse($nfse_id = null)
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        $nfse_id = intval($nfse_id);
        if (!$nfse_id) {
            echo json_encode(['success' => false, 'message' => 'ID da NFS-e não informado.']);
            return;
        }

        $nfse = $this->nfse_emitida_model->getById($nfse_id);
        if (!$nfse) {
            echo json_encode(['success' => false, 'message' => 'NFS-e não encontrada.']);
            return;
        }

        if (empty($nfse->chave_acesso)) {
            echo json_encode(['success' => false, 'message' => 'NFS-e não possui chave de acesso.']);
            return;
        }

        // Carregar certificado
        $this->load->model('certificado_model');
        $pemPaths = $this->certificado_model->extrairCertificadoPem();

        if (!$pemPaths || isset($pemPaths['error'])) {
            $msg = isset($pemPaths['error']) ? $pemPaths['error'] : 'Certificado digital não configurado.';
            echo json_encode(['success' => false, 'message' => $msg]);
            return;
        }

        try {
            $this->config->load('nfse_nacional', true);
            $nfseConfig = $this->config->item('nfse_nacional');

            $this->load->library('Nfse/NfseNacional');

            $nfseApi = new NfseNacional([
                'ambiente' => $nfse->ambiente ?? 'homologacao',
                'cert_pem' => $pemPaths['cert'],
                'key_pem' => $pemPaths['key'],
                'ca_path' => $nfseConfig['nfse_ca_path'] ?? FCPATH . 'assets/certs/ac-icp-brasil.pem',
                'cnpj' => $pemPaths['cnpj'],
                'timeout' => $nfseConfig['nfse_timeout'] ?? 60,
            ]);

            $resultado = $nfseApi->consultar($nfse->chave_acesso);

            // Limpar PEM
            $this->certificado_model->limparPemTemporarios($pemPaths);

            echo json_encode($resultado);

        } catch (Exception $e) {
            log_message('error', 'NFS-e Nacional consultar_nfse exception: ' . $e->getMessage());
            if (isset($pemPaths)) {
                $this->certificado_model->limparPemTemporarios($pemPaths);
            }
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }
}
