<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller: Certificado Digital
 * Gerencia certificados A1/A3 e integração fiscal
 */
class Certificado extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('certificado_model');
        $this->load->model('impostos_model');
        $this->load->helper(['date', 'currency']);
    }

    /**
     * Dashboard do Certificado
     */
    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        $certificado = $this->certificado_model->getCertificadoAtivo();
        $validade = $this->certificado_model->verificarValidade();
        $consultas = $this->certificado_model->getConsultas($certificado ? $certificado->id : null, 10);

        // Verificar configuração de impostos
        $this->load->model('impostos_model');
        $configImpostos = [
            'anexo' => $this->impostos_model->getConfig('IMPOSTO_ANEXO_PADRAO') ?: null,
            'faixa' => $this->impostos_model->getConfig('IMPOSTO_FAIXA_ATUAL') ?: null,
            'retencao_automatica' => $this->impostos_model->getConfig('IMPOSTO_RETENCAO_AUTOMATICA') == '1',
            'dre_integracao' => $this->impostos_model->getConfig('IMPOSTO_DRE_INTEGRACAO') == '1',
            'regime' => 'simples_nacional',
        ];

        $this->data['certificado'] = $certificado;
        $this->data['validade'] = $validade;
        $this->data['consultas'] = $consultas;
        $this->data['config_impostos'] = $configImpostos;
        $this->data['view'] = 'certificado/dashboard';
        return $this->layout();
    }

    /**
     * Configuração do Certificado
     */
    public function configurar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado');
        }

        if ($this->input->post()) {
            $dados = [
                'cnpj' => preg_replace('/[^0-9]/', '', $this->input->post('cnpj')),
                'razao_social' => $this->input->post('razao_social'),
                'nome_fantasia' => $this->input->post('nome_fantasia'),
                'senha' => $this->input->post('senha'),
                'ambiente' => $this->input->post('ambiente') ?: 'homologacao',
            ];

            // Upload do arquivo se for A1
            $arquivo = null;
            if ($this->input->post('tipo') == 'A1' && isset($_FILES['certificado']) && $_FILES['certificado']['tmp_name']) {
                $ext = strtolower(pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION));
                $extPermitidas = ['pfx', 'p12'];
                if (!in_array($ext, $extPermitidas)) {
                    $this->session->set_flashdata('error', 'O certificado deve estar no formato .pfx ou .p12.');
                    redirect('certificado/configurar');
                }
                if ($_FILES['certificado']['size'] > 2 * 1024 * 1024) {
                    $this->session->set_flashdata('error', 'O certificado não pode ultrapassar 2MB.');
                    redirect('certificado/configurar');
                }
                $arquivo = $_FILES['certificado'];
            }

            $resultado = $this->certificado_model->salvarCertificado($dados, $arquivo);

            if (isset($resultado['success'])) {
                // Tentar sincronizar alíquotas automaticamente
                $sincronizacao = $this->certificado_model->sincronizarAliquotas($dados['cnpj']);

                if (isset($sincronizacao['success'])) {
                    // Configurar impostos automaticamente
                    $this->load->model('impostos_model');

                    // Simples Nacional (único regime suportado)
                    $this->impostos_model->setConfig('IMPOSTO_REGIME_TRIBUTARIO', 'simples_nacional');
                    $this->impostos_model->setConfig('IMPOSTO_ANEXO_PADRAO', $sincronizacao['configuracao']['anexo_sugerido']);
                    $this->impostos_model->setConfig('IMPOSTO_FAIXA_ATUAL', '1');
                    $this->impostos_model->setConfig('IMPOSTO_RETENCAO_AUTOMATICA', '1');
                    $this->impostos_model->setConfig('IMPOSTO_DRE_INTEGRACAO', '1');

                    log_info('Configuração de impostos automática aplicada para CNPJ: ' . $dados['cnpj']);

                    $this->session->set_flashdata('success', 'Certificado configurado com sucesso! Regime: Simples Nacional — Anexo ' . $sincronizacao['configuracao']['anexo_sugerido'] . '. Verifique em Impostos > Configurações.');
                } else {
                    $this->session->set_flashdata('success', 'Certificado configurado com sucesso! Configure manualmente os impostos em Impostos > Configurações.');
                    $this->session->set_flashdata('info', 'Não foi possível detectar automaticamente as alíquotas: ' . ($sincronizacao['error'] ?? 'Tente novamente mais tarde.'));
                }

                redirect('certificado');
            } else {
                $this->session->set_flashdata('error', $resultado['error']);
            }
        }

        $this->data['certificado'] = $this->certificado_model->getCertificadoAtivo();
        $this->data['view'] = 'certificado/configurar';
        return $this->layout();
    }

    /**
     * Consulta CNPJ na Receita
     */
    public function consultar_cnpj()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado');
        }

        $cnpj = $this->input->get('cnpj');
        if (!$cnpj) {
            $this->session->set_flashdata('error', 'CNPJ não informado.');
            redirect('certificado');
        }

        $resultado = $this->certificado_model->consultarCNPJ($cnpj);

        if (isset($resultado['success'])) {
            $this->session->set_flashdata('success', 'Consulta realizada! Verifique os dados.');
            $this->session->set_userdata('consulta_cnpj', $resultado['data']);
        } else {
            $this->session->set_flashdata('error', $resultado['error']);
        }

        redirect('certificado');
    }

    /**
     * Sincroniza alíquotas com Receita
     */
    public function sincronizar_aliquotas()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado');
        }

        $resultado = $this->certificado_model->sincronizarAliquotas();

        if (isset($resultado['success'])) {
            // Aplicar configurações automaticamente (Simples Nacional)
            $this->load->model('impostos_model');

            $this->impostos_model->setConfig('IMPOSTO_REGIME_TRIBUTARIO', 'simples_nacional');
            $this->impostos_model->setConfig('IMPOSTO_ANEXO_PADRAO', $resultado['configuracao']['anexo_sugerido']);
            $this->impostos_model->setConfig('IMPOSTO_FAIXA_ATUAL', '1');
            $this->impostos_model->setConfig('IMPOSTO_RETENCAO_AUTOMATICA', '1');
            $this->impostos_model->setConfig('IMPOSTO_DRE_INTEGRACAO', '1');

            $this->session->set_flashdata('success', 'Alíquotas sincronizadas e configuradas automaticamente! Anexo identificado: ' . $resultado['configuracao']['anexo_sugerido']);

            log_info('Alíquotas sincronizadas manualmente pelo usuário: ' . $this->session->userdata('nome'));
        } else {
            $this->session->set_flashdata('error', $resultado['error']);
        }

        redirect('certificado');
    }

    /**
     * Consulta Simples Nacional
     */
    public function consultar_simples()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado');
        }

        $cnpj = $this->input->get('cnpj');
        if (!$cnpj) {
            $cert = $this->certificado_model->getCertificadoAtivo();
            if ($cert) {
                $cnpj = $cert->cnpj;
            } else {
                $this->session->set_flashdata('error', 'Nenhum CNPJ configurado.');
                redirect('certificado');
            }
        }

        $resultado = $this->certificado_model->consultarSimplesNacional($cnpj);

        if (isset($resultado['success'])) {
            $this->session->set_flashdata('success', 'Consulta ao Simples Nacional realizada!');
            $this->session->set_userdata('consulta_simples', $resultado['data']);
        } else {
            $this->session->set_flashdata('error', $resultado['error']);
        }

        redirect('certificado');
    }

    /**
     * Listar todas as NFS-e importadas
     */
    public function nfse()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect(base_url());
        }

        $this->load->library('pagination');

        $config['base_url'] = site_url('certificado/nfse');
        $config['total_rows'] = $this->db->count_all('certificado_nfe_importada');
        $config['per_page'] = 20;
        $config['page_query_string'] = true;

        $this->pagination->initialize($config);

        $this->db->order_by('data_emissao', 'DESC');
        $this->data['notas'] = $this->db->get('certificado_nfe_importada', $config['per_page'], $this->input->get('per_page'))->result();

        $this->data['view'] = 'certificado/listar_nfse';
        return $this->layout();
    }

    /**
     * Importação de NFS-e via XML
     * Suporta NFS-e Nacional (DPS), ABRASF e padrões municipais
     */
    public function importar_nfse()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado');
        }

        $cert = $this->certificado_model->getCertificadoAtivo();
        if (!$cert) {
            $this->session->set_flashdata('error', 'Configure um certificado primeiro.');
            redirect('certificado/configurar');
        }

        if ($this->input->post()) {
            log_message('debug', '[Certificado importar_nfse] POST recebido. FILES=' . json_encode($_FILES));

            if (isset($_FILES['xml_nfse']) && !empty($_FILES['xml_nfse']['tmp_name'])) {
                $ext = strtolower(pathinfo($_FILES['xml_nfse']['name'], PATHINFO_EXTENSION));
                if ($ext !== 'xml') {
                    log_message('error', '[Certificado importar_nfse] Extensao invalida: ' . $ext);
                    $this->session->set_flashdata('error', 'Arquivo deve ser XML (.xml)');
                    redirect('certificado/importar_nfse');
                }

                $xmlContent = file_get_contents($_FILES['xml_nfse']['tmp_name']);
                if (empty($xmlContent)) {
                    log_message('error', '[Certificado importar_nfse] Arquivo XML vazio');
                    $this->session->set_flashdata('error', 'Arquivo XML vazio.');
                    redirect('certificado/importar_nfse');
                }

                log_message('debug', '[Certificado importar_nfse] XML tamanho=' . strlen($xmlContent));

                libxml_use_internal_errors(true);
                $dom = new DOMDocument();
                if (!@$dom->loadXML($xmlContent)) {
                    $erros = libxml_get_errors();
                    $msgs = [];
                    foreach ($erros as $e) {
                        $msgs[] = trim($e->message) . ' (linha ' . $e->line . ')';
                    }
                    libxml_clear_errors();
                    log_message('error', '[Certificado importar_nfse] XML mal formado: ' . implode('; ', $msgs));
                    $this->session->set_flashdata('error', 'Arquivo XML inválido ou mal formado.');
                    redirect('certificado/importar_nfse');
                }

                // Extrair dados com suporte a múltiplos formatos
                $dadosExtraidos = $this->extrairDadosXmlNfse($dom);
                log_message('debug', '[Certificado importar_nfse] Dados extraidos=' . json_encode($dadosExtraidos));

                // Validar dados mínimos
                if (empty($dadosExtraidos['numero_nfse']) && empty($dadosExtraidos['chave_acesso'])) {
                    log_message('error', '[Certificado importar_nfse] Numero ou chave nao encontrados no XML');
                    $this->session->set_flashdata('error', 'Não foi possível identificar o número da nota no XML. Verifique se o arquivo é uma NFS-e válida.');
                    redirect('certificado/importar_nfse');
                }

                // Usar número como chave fallback
                $chave = $dadosExtraidos['chave_acesso'] ?: ('MANUAL_' . $dadosExtraidos['numero_nfse']);
                $numero = $dadosExtraidos['numero_nfse'] ?: $chave;
                $dataEmissao = $dadosExtraidos['data_emissao'] ?: date('Y-m-d H:i:s');
                $valorTotal = $dadosExtraidos['valor_servicos'] > 0 ? $dadosExtraidos['valor_servicos'] : 0;
                $valorImpostos = $dadosExtraidos['valor_deducoes'] > 0 ? $dadosExtraidos['valor_deducoes'] : 0;

                $dados = [
                    'chave_acesso' => (string)$chave,
                    'numero' => (string)$numero,
                    'data_emissao' => $dataEmissao,
                    'valor_total' => $valorTotal,
                    'valor_impostos' => $valorImpostos,
                    'situacao' => 'Autorizada',
                    'xml' => $xmlContent
                ];

                log_message('debug', '[Certificado importar_nfse] Chamando importarNFSe com chave=' . $chave . ' numero=' . $numero);
                $resultado = $this->certificado_model->importarNFSe($dados, $cert->id);

                if (isset($resultado['success'])) {
                    log_message('info', '[Certificado importar_nfse] Sucesso ID=' . ($resultado['id'] ?? '?'));
                    $this->session->set_flashdata('success', 'NFS-e #' . $numero . ' importada com sucesso! Valor: R$ ' . number_format($valorTotal, 2, ',', '.'));

                    // Criar lançamento no sistema de impostos
                    $this->impostos_model->reterImpostos([
                        'valor_bruto' => $valorTotal,
                        'cliente_id' => 0,
                        'nota_fiscal' => $numero,
                        'data_competencia' => date('Y-m-01', strtotime($dataEmissao))
                    ]);
                } else {
                    log_message('error', '[Certificado importar_nfse] Erro do model: ' . ($resultado['error'] ?? 'desconhecido'));
                    $this->session->set_flashdata('error', $resultado['error'] ?? 'Erro ao importar nota.');
                }
            } else {
                log_message('error', '[Certificado importar_nfse] Nenhum arquivo enviado. FILES=' . json_encode($_FILES));
                $this->session->set_flashdata('error', 'Nenhum arquivo XML enviado.');
            }
        }

        // Buscar notas já importadas
        $this->data['notas'] = $this->certificado_model->consultarNFSePeriodo(
            date('Y-m-01'),
            date('Y-m-t')
        );

        $this->data['view'] = 'certificado/importar_nfse';
        return $this->layout();
    }

    /**
     * Extrair dados básicos de um XML de NFS-e
     * Suporta NFS-e Nacional (DPS) e padrões municipais
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

        // NFS-e Nacional: nNFSe
        $nNFSe = $dom->getElementsByTagName('nNFSe')->item(0);
        if ($nNFSe) {
            $dados['numero_nfse'] = trim($nNFSe->nodeValue);
        }

        // ABRASF / municipal: Numero
        if (!$dados['numero_nfse']) {
            $numNode = $dom->getElementsByTagName('Numero')->item(0);
            if ($numNode) {
                $dados['numero_nfse'] = trim($numNode->nodeValue);
            }
        }

        // Chave de acesso (Nacional)
        $chave = $dom->getElementsByTagName('chaveAcesso')->item(0);
        if (!$chave) {
            $chave = $dom->getElementsByTagName('ChaveAcesso')->item(0);
        }
        if (!$chave) {
            $chave = $dom->getElementsByTagName('ChaveAcessoNfse')->item(0);
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
     * API para consultas AJAX
     */
    public function api_consulta()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Sem permissão.']);
            return;
        }

        $acao = $this->input->get('acao');
        $cnpj = $this->input->get('cnpj');

        switch ($acao) {
            case 'cnpj':
                $resultado = $this->certificado_model->consultarCNPJ($cnpj);
                break;
            case 'simples':
                $resultado = $this->certificado_model->consultarSimplesNacional($cnpj);
                break;
            case 'sincronizar':
                $resultado = $this->certificado_model->sincronizarAliquotas($cnpj);
                break;
            default:
                $resultado = ['error' => 'Ação inválida'];
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    /**
     * Vincular NFS-e a uma OS
     */
    public function vincular_nfse_os()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCertificado')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Sem permissão.']);
            return;
        }

        $nfse_id = $this->input->post('nfse_id');
        $os_id = $this->input->post('os_id');

        if (!$nfse_id || !$os_id) {
            echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
            return;
        }

        $this->load->model('os_model');
        $resultado = $this->os_model->vincularNfseOs($nfse_id, $os_id);

        if ($resultado) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao vincular']);
        }
    }

    /**
     * Listar NFS-e disponíveis para vincular
     */
    public function listar_nfse_disponiveis()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) {
            header('Content-Type: application/json');
            echo json_encode(['nfse' => [], 'error' => 'Sem permissão.']);
            return;
        }

        $this->db->where('os_id IS NULL');
        $this->db->order_by('data_importacao', 'DESC');
        $nfse = $this->db->get('certificado_nfe_importada')->result();

        echo json_encode(['nfse' => $nfse]);
    }

    /**
     * Força configuração como Simples Nacional (quando API não detecta)
     */
    public function forcar_simples_nacional()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado');
        }

        $anexo = $this->input->post('anexo_forcado') ?: 'III';

        $this->load->model('impostos_model');
        $this->impostos_model->setConfig('IMPOSTO_REGIME_TRIBUTARIO', 'simples_nacional');
        $this->impostos_model->setConfig('IMPOSTO_ANEXO_PADRAO', $anexo);
        $this->impostos_model->setConfig('IMPOSTO_FAIXA_ATUAL', '1');
        $this->impostos_model->setConfig('IMPOSTO_RETENCAO_AUTOMATICA', '1');
        $this->impostos_model->setConfig('IMPOSTO_DRE_INTEGRACAO', '1');

        $this->session->set_flashdata('success', 'Configurado manualmente como Simples Nacional — Anexo ' . $anexo . '.');
        redirect('certificado');
    }

    /**
     * Remove certificado
     */
    public function remover($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado');
        }

        // Desativar em vez de excluir
        $this->db->where('id', $id);
        $this->db->update('certificado_digital', ['ativo' => 0]);

        $this->session->set_flashdata('success', 'Certificado removido.');
        redirect('certificado');
    }
}
