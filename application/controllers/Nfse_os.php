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
            'valor_deducoes' => $this->input->post('valor_deducoes') ?? 0
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
            'gateway' => $this->input->post('gateway') ?? null
        ];

        // Gerar boleto
        $resultado = $this->boleto_os_model->gerar($os_id, $nfse_id, $config);

        if (isset($resultado['success'])) {
            // Se tem gateway configurado, processar
            if ($config['gateway']) {
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

            echo json_encode([
                'success' => true,
                'valor_bruto' => $valor,
                'valor_liquido' => $valor - ($calculo['valor_total_impostos'] ?? 0),
                'impostos' => $calculo
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

        // Calcular impostos
        $calculo = $this->impostos_model->calcularImpostos($valor_servicos);
        $valor_liquido = $valor_servicos - $calculo['valor_total_impostos'] - $valor_deducoes;

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

        // Enviar email
        $this->load->library('email');
        $this->email->from($this->config->item('email_smtp_user'), 'Sistema MAP-OS');
        $this->email->to($os->email);
        $this->email->subject('Boleto OS #' . $boleto->os_id);

        $mensagem = "Olá {$os->nomeCliente},\n\n";
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
}
