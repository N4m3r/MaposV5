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

        // Dados do POST (?: trata string vazia, ?? so trata null)
        $dados = [
            'valor_servicos' => $this->normalizarValorMonetario($this->input->post('valor_servicos') ?: $os->valorTotal),
            'valor_deducoes' => $this->normalizarValorMonetario($this->input->post('valor_deducoes') ?: 0),
            'descricao_servico' => $this->input->post('descricao_servico') ?: '',
            'regime_tributario' => 'simples_nacional',
            'valor_das' => $this->normalizarValorMonetario($this->input->post('valor_das') ?: null),
            'retem_iss' => $this->input->post('retem_iss') ? 1 : 0,
            'retem_irrf' => $this->input->post('retem_irrf') ? 1 : 0,
            'retem_pis' => $this->input->post('retem_pis') ? 1 : 0,
            'retem_cofins' => $this->input->post('retem_cofins') ? 1 : 0,
            'retem_csll' => $this->input->post('retem_csll') ? 1 : 0,
            'valor_retencao_iss' => $this->normalizarValorMonetario($this->input->post('valor_retencao_iss') ?: 0),
            'valor_retencao_irrf' => $this->normalizarValorMonetario($this->input->post('valor_retencao_irrf') ?: 0),
            'valor_retencao_pis' => $this->normalizarValorMonetario($this->input->post('valor_retencao_pis') ?: 0),
            'valor_retencao_cofins' => $this->normalizarValorMonetario($this->input->post('valor_retencao_cofins') ?: 0),
            'valor_retencao_csll' => $this->normalizarValorMonetario($this->input->post('valor_retencao_csll') ?: 0),
            'valor_total_retencao' => $this->normalizarValorMonetario($this->input->post('valor_total_retencao') ?: 0),
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
            'data_vencimento' => $this->input->post('data_vencimento') ?: date('Y-m-d', strtotime('+5 days')),
            'instrucoes' => $this->input->post('instrucoes') ?: 'Pagável em qualquer banco até o vencimento.',
            'gateway' => $this->input->post('gateway') ?: null,
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
                    log_message('error', 'Erro ao gerar boleto no gateway: ' . $e->getMessage());
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
            $rawValor = $this->input->post('valor') ?: $this->input->get('valor');
            $valor = $this->normalizarValorMonetario($rawValor);

            log_message('debug', 'NFSe calcular_impostos: raw=' . var_export($rawValor, true) . ' normalizado=' . $valor);

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

            log_message('debug', 'NFSe calcular_impostos: calcularImpostos retornou=' . var_export($calculo, true));

            if ($calculo === false || !is_array($calculo)) {
                log_message('error', 'NFSe: calcularImpostos retornou false/invalido para valor=' . $valor);
                echo json_encode([
                    'success' => false,
                    'message' => 'Configuração tributária não encontrada. Execute a migration SQL ou configure os impostos em Configurações do Sistema.'
                ]);
                return;
            }

            // Calcular DAS (Simples Nacional)
            $valor_das = $calculo['valor_total_impostos'] ?? ($valor * 0.06);

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
                'regime_tributario' => 'simples_nacional',
                'valor_das' => $valor_das,
                'retencoes' => $retencoes,
            ]);
        } catch (Exception $e) {
            log_message('error', 'NFSe calcular_impostos exception: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Página de importação de NFS-e via XML
     * Interface moderna com preview antes de salvar
     */
    public function importar($os_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        $this->data['os_id'] = $os_id;
        $this->data['os'] = $os_id ? $this->os_model->getById($os_id) : null;
        $this->data['view'] = 'nfse_os/importar';
        return $this->layout();
    }

    /**
     * Recebe XML via AJAX e retorna preview dos dados extraídos
     */
    public function preview_importar_ajax()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cNFSe')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        if (empty($_FILES['xml_nfse']['tmp_name']) || $_FILES['xml_nfse']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado.']);
            return;
        }

        $xmlContent = file_get_contents($_FILES['xml_nfse']['tmp_name']);
        if (empty($xmlContent)) {
            echo json_encode(['success' => false, 'message' => 'Arquivo vazio.']);
            return;
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        if (!@$dom->loadXML($xmlContent)) {
            echo json_encode(['success' => false, 'message' => 'XML inválido.']);
            return;
        }

        $dados = $this->extrairDadosXmlNfse($dom);
        $dados['success'] = true;
        $dados['xml_base64'] = base64_encode($xmlContent);

        echo json_encode($dados);
    }

    /**
     * Salvar XML importado no banco após confirmação do preview
     */
    public function salvar_importacao()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        $os_id = $this->input->post('os_id');
        $xmlBase64 = $this->input->post('xml_base64');

        if (!$os_id || !$xmlBase64) {
            $this->session->set_flashdata('error', 'Dados incompletos.');
            redirect('os/visualizar/' . $os_id);
        }

        $xmlContent = base64_decode($xmlBase64);
        if (empty($xmlContent)) {
            $this->session->set_flashdata('error', 'XML inválido.');
            redirect('os/visualizar/' . $os_id);
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        if (!@$dom->loadXML($xmlContent)) {
            $this->session->set_flashdata('error', 'XML inválido.');
            redirect('os/visualizar/' . $os_id);
        }

        $os = $this->os_model->getById($os_id);
        if (!$os) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('os');
        }

        $dadosExtraidos = $this->extrairDadosXmlNfse($dom);
        $valorServicos = $dadosExtraidos['valor_servicos'] > 0 ? $dadosExtraidos['valor_servicos'] : ($os->valorTotal ?? 0);
        $this->load->model('impostos_model');
        $calculo = $this->impostos_model->calcularImpostos($valorServicos);

        $nfse_data = [
            'os_id' => $os_id,
            'data_emissao' => $dadosExtraidos['data_emissao'] ?: date('Y-m-d H:i:s'),
            'numero_nfse' => $dadosExtraidos['numero_nfse'] ?: null,
            'chave_acesso' => $dadosExtraidos['chave_acesso'] ?: null,
            'protocolo' => $dadosExtraidos['protocolo'] ?: null,
            'codigo_verificacao' => $dadosExtraidos['codigo_verificacao'] ?: null,
            'valor_servicos' => $valorServicos,
            'valor_deducoes' => $dadosExtraidos['valor_deducoes'] ?: 0,
            'valor_liquido' => $dadosExtraidos['valor_liquido'] > 0 ? $dadosExtraidos['valor_liquido'] : $valorServicos,
            'regime_tributario' => 'simples_nacional',
            'valor_das' => $calculo['valor_total_impostos'] ?? 0,
            'aliquota_iss' => $calculo['aliquota_iss'] ?? 0,
            'valor_iss' => $calculo['iss'] ?? 0,
            'valor_inss' => $calculo['inss'] ?? 0,
            'valor_irrf' => $calculo['irrf'] ?? 0,
            'valor_csll' => $calculo['csll'] ?? 0,
            'valor_pis' => $calculo['pis'] ?? 0,
            'valor_cofins' => $calculo['cofins'] ?? 0,
            'valor_total_impostos' => $calculo['valor_total_impostos'] ?? 0,
            'situacao' => 'Emitida',
            'ambiente' => 'producao',
            'emitido_por' => $this->session->userdata('idUsuarios'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->db->field_exists('xml_nfse', 'os_nfse_emitida')) {
            $nfse_data['xml_nfse'] = $xmlContent;
        }
        if ($this->db->field_exists('xml_dps', 'os_nfse_emitida')) {
            $nfse_data['xml_dps'] = $xmlContent;
        }

        if ($this->db->insert('os_nfse_emitida', $nfse_data)) {
            $nfse_id = $this->db->insert_id();
            $this->db->where('idOs', $os_id);
            $this->db->update('os', ['nfse_status' => 'Emitida', 'valor_com_impostos' => $nfse_data['valor_liquido']]);
            $this->session->set_flashdata('success', 'NFS-e importada com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar NFS-e.');
        }

        redirect('os/visualizar/' . $os_id);
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
        $valor_servicos = $this->normalizarValorMonetario($this->input->get('valor_servicos')) ?: $this->normalizarValorMonetario($os->valorTotal ?? 0);
        $valor_deducoes = $this->normalizarValorMonetario($this->input->get('valor_deducoes')) ?: 0;
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

        // Logo do emitente (caminho absoluto para mPDF)
        $logoPath = null;
        if (!empty($emitente->url_logo)) {
            $logoRelative = str_replace(base_url(), '', $emitente->url_logo);
            $logoAbsolute = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $logoRelative);
            if (file_exists($logoAbsolute)) {
                $logoPath = $logoAbsolute;
            }
        }
        if (!$logoPath && file_exists(FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png')) {
            $logoPath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png';
        }

        // Dados da NFSe vinculada (emitida ou importada)
        $nfse = null;
        if ($boleto->nfse_id) {
            $nfse = $this->nfse_emitida_model->getById($boleto->nfse_id);
            if (!$nfse && $this->db->table_exists('certificado_nfe_importada')) {
                $nfse = $this->db->where('id', $boleto->nfse_id)->get('certificado_nfe_importada')->row();
            }
        }

        $data = [
            'boleto' => $boleto,
            'os' => $os,
            'emitente' => $emitente,
            'nfse' => $nfse,
            'is_preview' => true,
            'logo_path' => $logoPath,
        ];

        $this->load->helper('mpdf');
        $html = $this->load->view('nfse_os/preview_boleto', $data, true);
        pdf_create($html, 'Boleto_OS_' . $boleto->os_id . '_' . ($boleto->nosso_numero ?? $boleto_id), true);
    }

    /**
     * Pre-visualizacao do Boleto antes da emissao (preview sem salvar)
     */
    public function preview_boleto_emissao($os_id = null, $nfse_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissao.');
            redirect(base_url());
        }

        if (!$os_id || !$nfse_id) {
            redirect('os');
        }

        $os = $this->os_model->getById($os_id);
        if (!$os) {
            $this->session->set_flashdata('error', 'OS nao encontrada.');
            redirect('os');
        }

        $nfse = $this->nfse_emitida_model->getById($nfse_id);
        if (!$nfse && $this->db->table_exists('certificado_nfe_importada')) {
            $nfse = $this->db->where('id', $nfse_id)->get('certificado_nfe_importada')->row();
        }
        if (!$nfse) {
            $this->session->set_flashdata('error', 'NFS-e nao encontrada.');
            redirect('os');
        }

        // Dados do preview via GET (vem do formulario da aba boleto)
        $data_vencimento = $this->input->get('data_vencimento') ?: date('Y-m-d', strtotime('+5 days'));
        $instrucoes = $this->input->get('instrucoes') ?: 'Pagavel em qualquer banco ate o vencimento.';

        // Emitente
        $this->load->model('mapos_model');
        $emitente = $this->mapos_model->getEmitente();

        // Logo do emitente (absoluto para mPDF)
        $logoUrl = null;
        if (!empty($emitente->url_logo)) {
            $logoRelative = str_replace(base_url(), '', $emitente->url_logo);
            $logoAbsolute = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $logoRelative);
            if (file_exists($logoAbsolute)) {
                $logoUrl = base_url($logoRelative);
            }
        }
        if (!$logoUrl && file_exists(FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png')) {
            $logoUrl = base_url('assets/img/logo.png');
        }

        // Montar objeto boleto temporario em memoria
        $boleto = new stdClass();
        $boleto->sacado_nome = $os->nomeCliente ?? 'Sacado';
        $boleto->sacado_documento = $os->documento ?? '';
        $boleto->sacado_endereco = trim(($os->rua ?? '') . ', ' . ($os->numero ?? '') . ' - ' . ($os->bairro ?? ''));
        $boleto->data_vencimento = $data_vencimento;
        $boleto->data_emissao = date('Y-m-d');
        $boleto->valor_liquido = $nfse->valor_liquido ?? $nfse->valor_servicos ?? $nfse->valor_total ?? 0;
        $boleto->valor_original = $nfse->valor_servicos ?? $nfse->valor_total ?? 0;
        $boleto->nosso_numero = 'PREVIEW';
        $boleto->id = 0;
        $boleto->linha_digitavel = '';
        $boleto->codigo_barras = '';
        $boleto->instrucoes = $instrucoes;

        $data = [
            'boleto' => $boleto,
            'os' => $os,
            'emitente' => $emitente,
            'nfse' => $nfse,
            'is_preview' => true,
            'logo_url' => $logoUrl,
        ];

        $this->load->helper('mpdf');
        $html = $this->load->view('nfse_os/preview_boleto', $data, true);
        pdf_create($html, 'Boleto_Preview_OS_' . $os_id, true);
    }

    /**
     * Formatar chave PIX para exibição
     */
    private function formatarChavePix($chave)
    {
        $chave = trim($chave);
        if (empty($chave)) {
            return $this->data['configuration']['pix_key'] ?? '';
        }

        // Email
        if (filter_var($chave, FILTER_VALIDATE_EMAIL)) {
            return $chave;
        }

        // Telefone (comeca com + ou tem apenas digitos e length >= 11)
        $digits = preg_replace('/\D/', '', $chave);
        if (strlen($digits) >= 11 && strlen($digits) <= 14) {
            if (strlen($digits) == 13 && strpos($chave, '+') === 0) {
                // +55XXYYYYYYYYY
                return '+' . substr($digits, 0, 2) . ' (' . substr($digits, 2, 2) . ') ' . substr($digits, 4, 5) . '-' . substr($digits, 9);
            }
            if (strlen($digits) == 11) {
                return '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 5) . '-' . substr($digits, 7);
            }
            if (strlen($digits) == 12) {
                return '+' . substr($digits, 0, 2) . ' (' . substr($digits, 2, 2) . ') ' . substr($digits, 4, 4) . '-' . substr($digits, 8);
            }
            return $chave;
        }

        // CPF
        if (strlen($digits) == 11) {
            return substr($digits, 0, 3) . '.' . substr($digits, 3, 3) . '.' . substr($digits, 6, 3) . '-' . substr($digits, 9, 2);
        }

        // CNPJ
        if (strlen($digits) == 14) {
            return substr($digits, 0, 2) . '.' . substr($digits, 2, 3) . '.' . substr($digits, 5, 3) . '/' . substr($digits, 8, 4) . '-' . substr($digits, 12, 2);
        }

        // Chave aleatoria (UUID) ou outro formato — retorna como está
        return $chave;
    }

    /**
     * Normaliza valor monetário em formato brasileiro (1.234,56 → 1234.56)
     */
    private function normalizarValorMonetario($valor)
    {
        if (empty($valor) || is_numeric($valor)) {
            return floatval($valor);
        }
        $valor = trim($valor);
        if (strpos($valor, ',') !== false) {
            // Assume formato brasileiro: remove pontos de milhar, troca vírgula por ponto
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        }
        return floatval($valor);
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

        $motivo = $this->input->post('motivo') ?: 'Cancelamento solicitado pelo usuário';
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
            'data_pagamento' => $this->input->post('data_pagamento') ?: date('Y-m-d'),
            'valor_pago' => $this->input->post('valor_pago'),
            'multa' => $this->input->post('multa') ?: 0,
            'juros' => $this->input->post('juros') ?: 0
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

        // Enfileirar email via sistema V5
        require_once APPPATH . 'libraries/Email/EmailQueue.php';
        $queue = new \Libraries\Email\EmailQueue();

        $nomeCliente = htmlspecialchars($os->nomeCliente ?? 'Cliente', ENT_QUOTES, 'UTF-8');
        $mensagem = "Olá {$nomeCliente},\n\n";
        $mensagem .= "Segue o boleto referente à OS #{$boleto->os_id}.\n\n";
        $mensagem .= "Valor: R$ " . number_format($boleto->valor_liquido, 2, ',', '.') . "\n";
        $mensagem .= "Vencimento: " . date('d/m/Y', strtotime($boleto->data_vencimento)) . "\n";
        $mensagem .= "Linha Digitável: {$boleto->linha_digitavel}\n\n";
        $mensagem .= "Caso já tenha efetuado o pagamento, desconsidere este email.\n\n";
        $mensagem .= "Atenciosamente,\nEquipe MAP-OS";

        $enqueueData = [
            'to' => $os->email,
            'subject' => 'Boleto OS #' . $boleto->os_id,
            'body_text' => $mensagem,
            'priority' => 2,
        ];

        if ($boleto->pdf_path && file_exists($boleto->pdf_path)) {
            $enqueueData['attachments'] = [$boleto->pdf_path];
        }

        $id = $queue->enqueue($enqueueData);
        if ($id > 0) {
            $this->session->set_flashdata('success', 'Boleto enfileirado para envio por email.');
        } else {
            $this->session->set_flashdata('error', 'Erro ao enfileirar email do boleto.');
        }

        redirect('os/visualizar/' . $boleto->os_id);
    }

    /**
     * Retorna caminho válido do CA ou null se inválido/inexistente
     */
    private function getValidCaPath($nfseConfig)
    {
        $caPath = $nfseConfig['nfse_ca_path'] ?? '';
        if (!empty($caPath) && file_exists($caPath) && filesize($caPath) > 0) {
            return $caPath;
        }
        return null;
    }

    /**
     * Monta configuração da API NFS-e Nacional incluindo fallback .pfx
     */
    private function montarConfigApiNfse($pemPaths, $nfseConfig, $certAtivo = null)
    {
        $caPath = $this->getValidCaPath($nfseConfig);
        $configApi = [
            'ambiente' => $pemPaths['ambiente'] ?? 'homologacao',
            'cert_pem' => $pemPaths['cert'],
            'key_pem' => $pemPaths['key'],
            'cnpj' => $pemPaths['cnpj'],
            'timeout' => $nfseConfig['nfse_timeout'] ?? 60,
        ];
        if ($caPath) {
            $configApi['ca_path'] = $caPath;
        }
        if ($certAtivo && !empty($certAtivo->arquivo_caminho) && file_exists($certAtivo->arquivo_caminho)) {
            $configApi['pfx_path'] = $certAtivo->arquivo_caminho;
            $senhaPfx = $this->certificado_model->descriptografarSenha($certAtivo->senha);
            if ($senhaPfx !== false) {
                $configApi['pfx_senha'] = $senhaPfx;
            }
        }
        return $configApi;
    }

    // ==================== API NFS-e NACIONAL ====================

    /**
     * Retorna o próximo número sequencial da DPS formatado com 15 dígitos
     * O schema SEFIN exige nDPS de 15 dígitos sem zeros à esquerda no XML,
     * e o Id do infDPS deve ter exatamente 45 caracteres (nDPS com 15 dígitos).
     * Formato: 1 + número sequencial preenchido com zeros até 14 dígitos = 15 total.
     * Ex: 1 → 100000000000001, 2 → 100000000000002
     */
    private function getProximoNumeroDps($serie = '1')
    {
        try {
            $baseNumero = 1;

            if ($this->db->table_exists('os_nfse_emitida') && $this->db->field_exists('n_dps', 'os_nfse_emitida')) {
                $serieEscapada = $this->db->escape($serie);
                $sql = "SELECT MAX(CAST(n_dps AS UNSIGNED)) as max_n_dps FROM os_nfse_emitida WHERE n_dps IS NOT NULL AND n_dps != '' AND serie_dps = {$serieEscapada}";
                $query = $this->db->query($sql);
                $maxNDps = intval($query->row()->max_n_dps ?? 0);
                if ($maxNDps > 0) {
                    $baseNumero = $maxNDps + 1;
                } else {
                    // Nenhuma nota local: começar do 63 (próximo após última nota do portal: nDPS 62)
                    $baseNumero = 63;
                }
            } else {
                $baseNumero = 63;
            }

            return $this->formatarNDps($baseNumero);
        } catch (Exception $e) {
            log_message('error', 'NFS-e Nacional: Erro ao obter próximo n_dps: ' . $e->getMessage());
            return $this->formatarNDps(63);
        }
    }

    /**
     * Retorna número da DPS como string (sem formatação artificial)
     * O schema TSNumDPS exige ^[1-9]\d{0,14}$ e o portal envia números sequenciais simples.
     */
    private function formatarNDps($numero)
    {
        return (string)intval($numero);
    }

    /**
     * Emitir NFS-e via API Nacional (Sistema Nacional NFS-e)
     * Chamado via AJAX pelo wizard
     */
    public function emitir_nfse_api($os_id = null)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
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

            // Verificar se existe certificado ativo no banco
            $certAtivo = $this->certificado_model->getCertificadoAtivo();
            if (!$certAtivo) {
                echo json_encode(['success' => false, 'message' => 'Nenhum certificado digital ativo encontrado. Cadastre um certificado em Certificado Digital > Configurar.']);
                return;
            }

            // Verificar se o arquivo existe
            $arquivoCaminho = $certAtivo->arquivo_caminho ?? '';
            if (empty($arquivoCaminho)) {
                echo json_encode(['success' => false, 'message' => 'Certificado cadastrado mas caminho do arquivo nao esta preenchido. Reenvie o arquivo .pfx em Certificado Digital > Configurar.']);
                return;
            }
            if (!file_exists($arquivoCaminho)) {
                echo json_encode(['success' => false, 'message' => 'Arquivo do certificado nao encontrado no servidor: ' . $arquivoCaminho . '. Reenvie o arquivo .pfx em Certificado Digital > Configurar.']);
                return;
            }

            $pemPaths = $this->certificado_model->extrairCertificadoPem();

            if (!$pemPaths || isset($pemPaths['error'])) {
                $msg = isset($pemPaths['error']) ? $pemPaths['error'] : 'Erro ao extrair certificado PEM. Verifique se a senha do arquivo .pfx esta correta.';
                echo json_encode(['success' => false, 'message' => $msg]);
                return;
            }

            // Carregar config (sem segundo parametro true para mergear no array principal de configs)
            $this->config->load('nfse_nacional');
            $nfseConfig = [
                'nfse_ambiente' => $this->config->item('nfse_ambiente'),
                'nfse_urls' => $this->config->item('nfse_urls'),
                'nfse_codigo_municipio' => $this->config->item('nfse_codigo_municipio'),
                'nfse_codigo_uf' => $this->config->item('nfse_codigo_uf'),
                'nfse_versao_dps' => $this->config->item('nfse_versao_dps'),
                'nfse_timeout' => $this->config->item('nfse_timeout'),
                'nfse_ca_path' => $this->config->item('nfse_ca_path'),
                'nfse_temp_path' => $this->config->item('nfse_temp_path'),
                'nfse_natureza_operacao' => $this->config->item('nfse_natureza_operacao'),
                'nfse_serie_dps' => $this->config->item('nfse_serie_dps'),
                'nfse_optante_simples' => $this->config->item('nfse_optante_simples'),
                'nfse_regime_especial' => $this->config->item('nfse_regime_especial'),
                'nfse_incentivador_cultural' => $this->config->item('nfse_incentivador_cultural'),
                'nfse_responsavel_retencao' => $this->config->item('nfse_responsavel_retencao'),
            ];

            // Instanciar bibliotecas NFS-e Nacional
            $this->load->library('Nfse/NfseConfig');
            $this->load->library('Nfse/DpsXmlBuilder');
            $this->load->library('Nfse/XmlSigner');
            // NfseNacional é instanciada manualmente com new mais abaixo (linha ~1050)

            // Carregar dados do emitente
            $this->load->model('mapos_model');
            $emitente = $this->mapos_model->getEmitente();

            // Montar dados do prestador
            $imPrestador = ($emitente ? preg_replace('/\D/', '', $emitente->inscricao_municipal ?? '') : '');
            if (empty($imPrestador)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Inscrição Municipal do emitente (prestador) não está cadastrada. Cadastre a IM em Configurações > Emitente antes de emitir a NFS-e.',
                ]);
                $this->certificado_model->limparPemTemporarios($pemPaths);
                return;
            }

            $prestador = [
                'cnpj' => $pemPaths['cnpj'] ?? '',
                'razao_social' => $pemPaths['razao_social'] ?? ($emitente->nome ?? ''),
                'im' => $imPrestador,
                'ie' => ($emitente ? ($emitente->inscricao_estadual ?? '') : ''),
                'cnae' => $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL') ?: '010701',
                'email' => ($emitente ? ($emitente->email ?? '') : ''),
                'telefone' => ($emitente ? ($emitente->telefone ?? '') : ''),
                'endereco' => [
                    'logradouro' => ($emitente ? ($emitente->rua ?? '') : ''),
                    'numero' => ($emitente ? ($emitente->numero ?? '') : ''),
                    'complemento' => ($emitente ? ($emitente->complemento ?? '') : ''),
                    'bairro' => ($emitente ? ($emitente->bairro ?? '') : ''),
                    'codigo_municipio' => $nfseConfig['nfse_codigo_municipio'] ?? '1302603',
                    'uf' => 'AM',
                    'cep' => ($emitente ? preg_replace('/\D/', '', $emitente->cep ?? '') : ''),
                ],
            ];

            // Montar dados do tomador (cliente da OS)
            $this->load->model('clientes_model');
            $cliente = $this->clientes_model->getById($os->clientes_id ?? 0);

            $tomador = [];
            if ($cliente) {
                $cpfCnpj = preg_replace('/\D/', '', $cliente->documento ?? '');
                $tomador = [
                    'cpf_cnpj' => $cpfCnpj,
                    'im' => $cliente->inscricao_municipal ?? '',
                    'ie' => $cliente->inscricao_estadual ?? '',
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
            $valorServicos = $this->normalizarValorMonetario($this->input->post('valor_servicos') ?: $os->valorTotal);
            $valorDeducoes = $this->normalizarValorMonetario($this->input->post('valor_deducoes') ?: 0);
            $descricaoServico = $this->input->post('descricao_servico') ?: ($this->impostos_model->getConfig('IMPOSTO_DESCRICAO_SERVICO') ?: 'Serviços de informática');

            // Calcular impostos
            $calculo = $this->impostos_model->calcularImpostos($valorServicos);

            if ($calculo === false) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Configuração tributária não encontrada. Configure os impostos em Configurações do Sistema antes de emitir a NFS-e.'
                ]);
                $this->certificado_model->limparPemTemporarios($pemPaths);
                return;
            }

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
            $tributacao = [
                'natureza_operacao' => $nfseConfig['nfse_natureza_operacao'] ?? '1',
                'optante_simples' => $nfseConfig['nfse_optante_simples'] ?? true,
                'op_simp_nac' => $nfseConfig['nfse_op_simp_nac'] ?? '3',
                'regime_especial' => $nfseConfig['nfse_regime_especial'] ?? '0',
                'incentivador_cultural' => $nfseConfig['nfse_incentivador_cultural'] ?? '0',
                'aliquota_iss' => floatval($this->impostos_model->getConfig('IMPOSTO_ISS_MUNICIPAL') ?: 5.00),
                'aliquota_nominal' => $calculo['aliquota_nominal'] ?? 6.00,
                'reg_ap_trib_sn' => '2', // 2=Enquadramento nas alíquotas dos Anexos I, II, III, IV ou V
            ];

            // Competência
            $competencia = $this->input->post('competencia') ?: date('Y-m-d');

            // Gerar XML DPS
            $xmlBuilder = new DpsXmlBuilder([
                'codigo_municipio' => $nfseConfig['nfse_codigo_municipio'] ?? '1302603',
                'codigo_uf' => $nfseConfig['nfse_codigo_uf'] ?? '13',
            ]);

            // Obter próximo número sequencial da DPS
            $serieDps = $nfseConfig['nfse_serie_dps'] ?? '1';
            $proximoNDps = $this->getProximoNumeroDps($serieDps);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Série DPS=' . $serieDps . ' | Próximo n_dps=' . $proximoNDps);

            $dadosDps = [
                'prestador' => $prestador,
                'tomador' => $tomador,
                'servico' => $servico,
                'tributacao' => $tributacao,
                'competencia' => $competencia,
                'ambiente' => $pemPaths['ambiente'] ?? 'homologacao',
                'serie' => $serieDps,
                'n_dps' => $proximoNDps,
            ];

            $xmlDps = $xmlBuilder->gerarDps($dadosDps);

            if (!$xmlDps) {
                echo json_encode(['success' => false, 'message' => 'Erro ao gerar XML DPS.']);
                $this->certificado_model->limparPemTemporarios($pemPaths);
                return;
            }

            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: XML DPS gerado (primeiros 3000 chars): ' . substr($xmlDps, 0, 3000));

            // Assinar XML DPS
            $xmlSigner = new XmlSigner();
            $xmlAssinado = $xmlSigner->assinarXml($xmlDps, $pemPaths['cert'], $pemPaths['key']);

            if (!$xmlAssinado) {
                echo json_encode(['success' => false, 'message' => 'Erro ao assinar XML DPS. Verifique o certificado digital.']);
                $this->certificado_model->limparPemTemporarios($pemPaths);
                return;
            }

            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: XML DPS assinado (primeiros 4000 chars): ' . substr($xmlAssinado, 0, 4000));

            // Validar XML assinado (well-formed)
            $domCheck = new DOMDocument('1.0', 'UTF-8');
            if (!@$domCheck->loadXML($xmlAssinado)) {
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: XML assinado é MAL FORMADO!');
            } else {
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: XML assinado está bem formado.');
            }

            // Enviar para API Nacional
            $configApi = $this->montarConfigApiNfse($pemPaths, $nfseConfig, $certAtivo);
            require_once APPPATH . 'libraries/Nfse/NfseNacional.php';
            $nfseApi = new NfseNacional($configApi);

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
                    'regime_tributario' => 'simples_nacional',
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
                    'n_dps' => $proximoNDps,
                    'serie_dps' => $serieDps,
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

        } catch (Throwable $e) {
            log_message('error', 'NFS-e Nacional emitir_nfse_api exception: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ':' . $e->getLine());
            // Limpar PEM temporários em caso de erro
            if (isset($pemPaths) && is_array($pemPaths)) {
                $this->certificado_model->limparPemTemporarios($pemPaths);
            }
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Download XML DPS de NFS-e (direto do banco de dados)
     */
    public function download_xml($nfse_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        if (!$nfse_id) {
            $this->session->set_flashdata('error', 'ID da NFS-e não informado.');
            redirect(base_url());
        }

        $nfse = $this->nfse_emitida_model->getById($nfse_id);
        if (!$nfse) {
            $this->session->set_flashdata('error', 'NFS-e não encontrada.');
            redirect(base_url());
        }

        $xmlContent = '';

        // 1) Tentar xml_nfse (XML de retorno da API — mais completo)
        if (!empty($nfse->xml_nfse)) {
            $xmlContent = $nfse->xml_nfse;
        }
        // 2) Fallback para xml_dps (XML DPS assinado enviado)
        elseif (!empty($nfse->xml_dps)) {
            $xmlContent = $nfse->xml_dps;
        }

        if (empty($xmlContent)) {
            $this->session->set_flashdata('error', 'XML da NFS-e não disponível no banco de dados.');
            redirect(base_url());
        }

        $fileName = 'NFSe_' . ($nfse->numero_nfse ?: $nfse_id) . '_OS_' . ($nfse->os_id ?: '') . '.xml';

        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($xmlContent));
        echo $xmlContent;
        exit;
    }

    /**
     * Reprocessar XMLs de NFS-e já emitidas
     * Percorre notas com xml_dps no banco e xml_path vazio,
     * salva o XML em disco e atualiza o registro para download.
     */
    public function reprocessar_xmls_nfse()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        $this->load->model('nfse_emitida_model');

        $uploadDir = FCPATH . 'assets/uploads/nfse/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Buscar todas as NFSe com xml_dps preenchido e xml_path vazio/null
        $this->db->where('xml_dps IS NOT NULL');
        $this->db->where("xml_dps !=", '');
        $this->db->group_start();
        $this->db->where('xml_path IS NULL');
        $this->db->or_where("xml_path", '');
        $this->db->group_end();
        $query = $this->db->get('os_nfse_emitida');
        $notas = $query ? $query->result() : [];

        $atualizadas = 0;
        $erros = 0;

        foreach ($notas as $nfse) {
            $identificador = $nfse->chave_acesso ?: $nfse->id;
            $xmlFileName = 'nfse_' . $identificador . '.xml';
            $xmlFullPath = $uploadDir . $xmlFileName;

            $xmlContent = $nfse->xml_dps;

            if (file_put_contents($xmlFullPath, $xmlContent) !== false) {
                $this->db->where('id', $nfse->id);
                $this->db->update('os_nfse_emitida', [
                    'xml_path' => 'assets/uploads/nfse/' . $xmlFileName,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $atualizadas++;
            } else {
                $erros++;
                log_message('error', 'NFS-e reprocessar_xmls: falha ao salvar XML em disco para NFSe ID=' . $nfse->id);
            }
        }

        $this->session->set_flashdata('success',
            'Reprocessamento concluído. ' . $atualizadas . ' XML(s) salvo(s) em disco. ' . ($erros > 0 ? $erros . ' erro(s).' : '')
        );
        redirect('nfse_os');
    }

    /**
     * Importar XML de NFS-e externa e vincular a uma OS
     * Permite salvar notas emitidas fora do sistema (ex: portal do contribuinte)
     */
    public function importar_xml_os($os_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cNFSe')) {
            $this->session->set_flashdata('error', 'Sem permissão para importar NFS-e.');
            redirect('os/visualizar/' . $os_id);
        }

        if (!$os_id || !is_numeric($os_id)) {
            $this->session->set_flashdata('error', 'OS não informada.');
            redirect('os');
        }

        // Verificar se OS existe
        $os = $this->os_model->getById($os_id);
        if (!$os) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('os');
        }

        // Verificar upload
        if (empty($_FILES['xml_nfse']['tmp_name']) || $_FILES['xml_nfse']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('error', 'Nenhum arquivo XML enviado ou erro no upload.');
            redirect('os/visualizar/' . $os_id);
        }

        $xmlContent = file_get_contents($_FILES['xml_nfse']['tmp_name']);
        if (empty($xmlContent)) {
            $this->session->set_flashdata('error', 'Arquivo XML vazio ou inválido.');
            redirect('os/visualizar/' . $os_id);
        }

        // Validar se é XML bem formado
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        if (!@$dom->loadXML($xmlContent)) {
            $this->session->set_flashdata('error', 'O arquivo enviado não é um XML válido.');
            redirect('os/visualizar/' . $os_id);
        }

        // Tentar extrair dados básicos do XML (NFS-e Nacional ou padrão municipal)
        $dadosExtraidos = $this->extrairDadosXmlNfse($dom);

        // Calcular impostos com base no valor extraído
        $valorServicos = $dadosExtraidos['valor_servicos'] > 0 ? $dadosExtraidos['valor_servicos'] : ($os->valorTotal ?? 0);
        $this->load->model('impostos_model');
        $calculo = $this->impostos_model->calcularImpostos($valorServicos);

        // Dados para inserção
        $nfse_data = [
            'os_id' => $os_id,
            'data_emissao' => $dadosExtraidos['data_emissao'] ?: date('Y-m-d H:i:s'),
            'numero_nfse' => $dadosExtraidos['numero_nfse'] ?: null,
            'chave_acesso' => $dadosExtraidos['chave_acesso'] ?: null,
            'protocolo' => $dadosExtraidos['protocolo'] ?: null,
            'codigo_verificacao' => $dadosExtraidos['codigo_verificacao'] ?: null,
            'valor_servicos' => $valorServicos,
            'valor_deducoes' => $dadosExtraidos['valor_deducoes'] ?: 0,
            'valor_liquido' => $dadosExtraidos['valor_liquido'] > 0 ? $dadosExtraidos['valor_liquido'] : $valorServicos,
            'regime_tributario' => 'simples_nacional',
            'valor_das' => $calculo['valor_total_impostos'] ?? 0,
            'aliquota_iss' => $calculo['aliquota_iss'] ?? 0,
            'valor_iss' => $calculo['iss'] ?? 0,
            'valor_inss' => $calculo['inss'] ?? 0,
            'valor_irrf' => $calculo['irrf'] ?? 0,
            'valor_csll' => $calculo['csll'] ?? 0,
            'valor_pis' => $calculo['pis'] ?? 0,
            'valor_cofins' => $calculo['cofins'] ?? 0,
            'valor_total_impostos' => $calculo['valor_total_impostos'] ?? 0,
            'situacao' => 'Emitida',
            'ambiente' => 'producao',
            'emitido_por' => $this->session->userdata('idUsuarios'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Salvar XML no banco
        if ($this->db->field_exists('xml_nfse', 'os_nfse_emitida')) {
            $nfse_data['xml_nfse'] = $xmlContent;
        }
        if ($this->db->field_exists('xml_dps', 'os_nfse_emitida')) {
            $nfse_data['xml_dps'] = $xmlContent;
        }

        if ($this->db->insert('os_nfse_emitida', $nfse_data)) {
            $nfse_id = $this->db->insert_id();

            // Atualizar status da OS
            $this->db->where('idOs', $os_id);
            $this->db->update('os', [
                'nfse_status' => 'Emitida',
                'valor_com_impostos' => $nfse_data['valor_liquido']
            ]);

            // Integrar com DRE
            $this->load->model('dre_model');
            $this->dre_model->integrarNFSe($nfse_id, $nfse_data);

            log_info('NFS-e importada via XML para OS #' . $os_id . ' - NFSe ID: ' . $nfse_id);
            $this->session->set_flashdata('success', 'NFS-e importada com sucesso e vinculada à OS #' . $os_id . '.');
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar NFS-e importada no banco de dados.');
        }

        redirect('os/visualizar/' . $os_id);
    }

    /**
     * Extrair dados básicos de um XML de NFS-e
     * Suporta NFS-e Nacional (DPS) e padrões municipais (INFSE, etc)
     */
    private function extrairDadosXmlNfse(DOMDocument $dom)
    {
        $dados = [
            'numero_nfse' => null,
            'chave_acesso' => null,
            'protocolo' => null,
            'codigo_verificacao' => null,
            'data_emissao' => null,
            'valor_servicos' => 0,
            'valor_deducoes' => 0,
            'valor_liquido' => 0,
        ];

        $xpath = new DOMXPath($dom);

        // Tentar namespaces comuns
        $namespaces = [
            'http://www.sped.fazenda.gov.br/nfse',
            'http://www.abrasf.org.br/ABRASF/arquivos/nfse.xsd',
            'http://www.ginfes.com.br/servico_consultar_nfse_resposta',
            '',
        ];

        // NFS-e Nacional: tag <nNFSe> ou <DPS> com atributos
        $nNFSe = $dom->getElementsByTagName('nNFSe')->item(0);
        if ($nNFSe) {
            $dados['numero_nfse'] = trim($nNFSe->nodeValue);
        }

        // Chave de acesso (Nacional)
        $chave = $dom->getElementsByTagName('chaveAcesso')->item(0);
        if (!$chave) {
            $chave = $dom->getElementsByTagName('ChaveAcesso')->item(0);
        }
        if ($chave) {
            $dados['chave_acesso'] = trim($chave->nodeValue);
        }

        // Código de verificação
        $codVerif = $dom->getElementsByTagName('codigoVerificacao')->item(0);
        if (!$codVerif) {
            $codVerif = $dom->getElementsByTagName('CodigoVerificacao')->item(0);
        }
        if (!$codVerif) {
            $codVerif = $dom->getElementsByTagName('CodigoVerificacao')->item(0);
        }
        if ($codVerif) {
            $dados['codigo_verificacao'] = trim($codVerif->nodeValue);
        }

        // Data de emissão
        $dhEmi = $dom->getElementsByTagName('dhEmi')->item(0);
        if (!$dhEmi) {
            $dhEmi = $dom->getElementsByTagName('DataEmissao')->item(0);
        }
        if (!$dhEmi) {
            $dhEmi = $dom->getElementsByTagName('DataEmissao')->item(0);
        }
        if ($dhEmi) {
            $dt = trim($dhEmi->nodeValue);
            if (strlen($dt) >= 10) {
                $dados['data_emissao'] = substr($dt, 0, 10) . ' ' . (substr($dt, 11, 8) ?: '00:00:00');
            }
        }

        // Valor dos serviços
        $vServ = $dom->getElementsByTagName('vServ')->item(0);
        if (!$vServ) {
            $vServ = $dom->getElementsByTagName('ValorServicos')->item(0);
        }
        if (!$vServ) {
            $vServ = $dom->getElementsByTagName('ValorServicos')->item(0);
        }
        if ($vServ) {
            $dados['valor_servicos'] = floatval(str_replace(',', '.', trim($vServ->nodeValue)));
        }

        // Valor líquido
        $vLiq = $dom->getElementsByTagName('ValorLiquidoNfse')->item(0);
        if (!$vLiq) {
            $vLiq = $dom->getElementsByTagName('vLiq')->item(0);
        }
        if ($vLiq) {
            $dados['valor_liquido'] = floatval(str_replace(',', '.', trim($vLiq->nodeValue)));
        }

        // Deduções
        $vDed = $dom->getElementsByTagName('vDed')->item(0);
        if (!$vDed) {
            $vDed = $dom->getElementsByTagName('ValorDeducoes')->item(0);
        }
        if ($vDed) {
            $dados['valor_deducoes'] = floatval(str_replace(',', '.', trim($vDed->nodeValue)));
        }

        return $dados;
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
            $this->config->load('nfse_nacional');
            $nfseConfig = [
                'nfse_ambiente' => $this->config->item('nfse_ambiente'),
                'nfse_urls' => $this->config->item('nfse_urls'),
                'nfse_codigo_municipio' => $this->config->item('nfse_codigo_municipio'),
                'nfse_codigo_uf' => $this->config->item('nfse_codigo_uf'),
                'nfse_versao_dps' => $this->config->item('nfse_versao_dps'),
                'nfse_timeout' => $this->config->item('nfse_timeout'),
                'nfse_ca_path' => $this->config->item('nfse_ca_path'),
                'nfse_temp_path' => $this->config->item('nfse_temp_path'),
                'nfse_natureza_operacao' => $this->config->item('nfse_natureza_operacao'),
                'nfse_serie_dps' => $this->config->item('nfse_serie_dps'),
                'nfse_optante_simples' => $this->config->item('nfse_optante_simples'),
                'nfse_regime_especial' => $this->config->item('nfse_regime_especial'),
                'nfse_incentivador_cultural' => $this->config->item('nfse_incentivador_cultural'),
                'nfse_responsavel_retencao' => $this->config->item('nfse_responsavel_retencao'),
            ];

            $this->load->library('Nfse/XmlSigner');

            require_once APPPATH . 'libraries/Nfse/NfseNacional.php';
            $certAtivo = $this->certificado_model->getCertificadoAtivo();
            $configApi = $this->montarConfigApiNfse($pemPaths, $nfseConfig, $certAtivo);
            $configApi['ambiente'] = $nfse->ambiente ?? 'homologacao';
            $nfseApi = new NfseNacional($configApi);

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
            $this->config->load('nfse_nacional');
            $nfseConfig = [
                'nfse_ambiente' => $this->config->item('nfse_ambiente'),
                'nfse_urls' => $this->config->item('nfse_urls'),
                'nfse_codigo_municipio' => $this->config->item('nfse_codigo_municipio'),
                'nfse_codigo_uf' => $this->config->item('nfse_codigo_uf'),
                'nfse_versao_dps' => $this->config->item('nfse_versao_dps'),
                'nfse_timeout' => $this->config->item('nfse_timeout'),
                'nfse_ca_path' => $this->config->item('nfse_ca_path'),
                'nfse_temp_path' => $this->config->item('nfse_temp_path'),
                'nfse_natureza_operacao' => $this->config->item('nfse_natureza_operacao'),
                'nfse_serie_dps' => $this->config->item('nfse_serie_dps'),
                'nfse_optante_simples' => $this->config->item('nfse_optante_simples'),
                'nfse_regime_especial' => $this->config->item('nfse_regime_especial'),
                'nfse_incentivador_cultural' => $this->config->item('nfse_incentivador_cultural'),
                'nfse_responsavel_retencao' => $this->config->item('nfse_responsavel_retencao'),
            ];

            require_once APPPATH . 'libraries/Nfse/NfseNacional.php';
            $certAtivo = $this->certificado_model->getCertificadoAtivo();
            $configApi = $this->montarConfigApiNfse($pemPaths, $nfseConfig, $certAtivo);
            $configApi['ambiente'] = $nfse->ambiente ?? 'homologacao';
            $nfseApi = new NfseNacional($configApi);

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
