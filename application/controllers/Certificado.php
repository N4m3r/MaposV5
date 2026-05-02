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
        log_message('debug', '[Certificado importar_nfse] ========== INICIO ========== METHOD=' . $_SERVER['REQUEST_METHOD']);

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cCertificado')) {
            log_message('error', '[Certificado importar_nfse] BLOQUEADO: sem permissao');
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado');
        }

        $cert = $this->certificado_model->getCertificadoAtivo();
        if (!$cert) {
            log_message('error', '[Certificado importar_nfse] BLOQUEADO: nenhum certificado ativo');
            $this->session->set_flashdata('error', 'Configure um certificado primeiro.');
            redirect('certificado/configurar');
        }
        log_message('debug', '[Certificado importar_nfse] Certificado ativo OK. id=' . $cert->id);

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
        log_message('debug', '[Certificado importar_nfse] Notas carregadas=' . (is_array($this->data['notas']) ? count($this->data['notas']) : 'nao_array'));

        // Carregar lista de OSs para vinculação
        $this->db->select('os.idOs, clientes.nomeCliente, os.dataInicial');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->order_by('os.idOs', 'DESC');
        $this->db->limit(300);
        $this->data['oss'] = $this->db->get()->result();
        log_message('debug', '[Certificado importar_nfse] OSs carregadas=' . count($this->data['oss']));

        $this->data['view'] = 'certificado/importar_nfse';
        log_message('debug', '[Certificado importar_nfse] ========== RENDERIZANDO VIEW ==========');
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
            log_message('debug', '[extrairDadosXmlNfse] nNFSe encontrado=' . $dados['numero_nfse']);
        } else {
            log_message('debug', '[extrairDadosXmlNfse] nNFSe NAO encontrado');
        }

        // ABRASF / municipal: Numero
        if (!$dados['numero_nfse']) {
            $numNode = $dom->getElementsByTagName('Numero')->item(0);
            if ($numNode) {
                $dados['numero_nfse'] = trim($numNode->nodeValue);
                log_message('debug', '[extrairDadosXmlNfse] Numero encontrado=' . $dados['numero_nfse']);
            } else {
                log_message('debug', '[extrairDadosXmlNfse] Numero NAO encontrado');
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
            log_message('debug', '[extrairDadosXmlNfse] chaveAcesso encontrado=' . $dados['chave_acesso']);
        } else {
            log_message('debug', '[extrairDadosXmlNfse] chaveAcesso NAO encontrado');
        }

        // Código de verificação
        $codVerif = $dom->getElementsByTagName('codigoVerificacao')->item(0);
        if (!$codVerif) {
            $codVerif = $dom->getElementsByTagName('CodigoVerificacao')->item(0);
        }
        if ($codVerif) {
            $dados['codigo_verificacao'] = trim($codVerif->nodeValue);
            log_message('debug', '[extrairDadosXmlNfse] codigoVerificacao encontrado=' . $dados['codigo_verificacao']);
        } else {
            log_message('debug', '[extrairDadosXmlNfse] codigoVerificacao NAO encontrado');
        }

        // Data de emissão
        $dhEmi = $dom->getElementsByTagName('dhEmi')->item(0);
        if (!$dhEmi) {
            $dhEmi = $dom->getElementsByTagName('DataEmissao')->item(0);
        }
        if ($dhEmi) {
            $dt = trim($dhEmi->nodeValue);
            if (strlen($dt) >= 10) {
                $dados['data_emissao'] = substr($dt, 0, 10) . ' ' . (substr($dt, 11, 8) ?: '00:00:00');
            }
            log_message('debug', '[extrairDadosXmlNfse] dhEmi/DataEmissao encontrado=' . $dt . ' normalizado=' . $dados['data_emissao']);
        } else {
            log_message('debug', '[extrairDadosXmlNfse] dhEmi/DataEmissao NAO encontrado');
        }

        // Valor dos serviços
        $vServ = $dom->getElementsByTagName('vServ')->item(0);
        if (!$vServ) {
            $vServ = $dom->getElementsByTagName('ValorServicos')->item(0);
        }
        if ($vServ) {
            $dados['valor_servicos'] = floatval(str_replace(',', '.', trim($vServ->nodeValue)));
            log_message('debug', '[extrairDadosXmlNfse] vServ/ValorServicos encontrado=' . $dados['valor_servicos']);
        } else {
            log_message('debug', '[extrairDadosXmlNfse] vServ/ValorServicos NAO encontrado');
        }

        // Valor líquido
        $vLiq = $dom->getElementsByTagName('ValorLiquidoNfse')->item(0);
        if (!$vLiq) {
            $vLiq = $dom->getElementsByTagName('vLiq')->item(0);
        }
        if ($vLiq) {
            $dados['valor_liquido'] = floatval(str_replace(',', '.', trim($vLiq->nodeValue)));
            log_message('debug', '[extrairDadosXmlNfse] vLiq/ValorLiquidoNfse encontrado=' . $dados['valor_liquido']);
        } else {
            log_message('debug', '[extrairDadosXmlNfse] vLiq/ValorLiquidoNfse NAO encontrado');
        }

        // Deduções
        $vDed = $dom->getElementsByTagName('vDed')->item(0);
        if (!$vDed) {
            $vDed = $dom->getElementsByTagName('ValorDeducoes')->item(0);
        }
        if ($vDed) {
            $dados['valor_deducoes'] = floatval(str_replace(',', '.', trim($vDed->nodeValue)));
            log_message('debug', '[extrairDadosXmlNfse] vDed/ValorDeducoes encontrado=' . $dados['valor_deducoes']);
        } else {
            log_message('debug', '[extrairDadosXmlNfse] vDed/ValorDeducoes NAO encontrado');
        }

        log_message('debug', '[extrairDadosXmlNfse] ========== RESULTADO ==========' . json_encode($dados));
        return $dados;
    }

    /**
     * AJAX: preview de importação de NFSe via XML
     * Recebe arquivo XML, extrai dados e retorna JSON para preview
     */
    public function preview_importar_ajax()
    {
        log_message('debug', '[Certificado preview_importar_ajax] ========== INICIO ==========');
        log_message('debug', '[Certificado preview_importar_ajax] REQUEST_METHOD=' . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . ' CONTENT_TYPE=' . ($_SERVER['CONTENT_TYPE'] ?? 'N/A'));
        log_message('debug', '[Certificado preview_importar_ajax] is_ajax_request=' . ($this->input->is_ajax_request() ? 'sim' : 'nao') . ' HTTP_X_REQUESTED_WITH=' . ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? 'nao_setado'));
        log_message('debug', '[Certificado preview_importar_ajax] POST=' . json_encode($this->input->post()) . ' FILES=' . json_encode(array_keys($_FILES)));

        if (!$this->input->is_ajax_request()) {
            log_message('error', '[Certificado preview_importar_ajax] BLOQUEADO: nao e requisicao AJAX');
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Requisicao AJAX invalida.']);
            return;
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cCertificado')) {
            log_message('error', '[Certificado preview_importar_ajax] BLOQUEADO: sem permissao cCertificado. Permissao=' . $this->session->userdata('permissao'));
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }
        log_message('debug', '[Certificado preview_importar_ajax] Permissao OK');

        if (!isset($_FILES['xml_nfse']) || empty($_FILES['xml_nfse']['tmp_name'])) {
            log_message('error', '[Certificado preview_importar_ajax] BLOQUEADO: nenhum arquivo enviado. FILES=' . json_encode($_FILES));
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado.']);
            return;
        }
        log_message('debug', '[Certificado preview_importar_ajax] Arquivo recebido. name=' . $_FILES['xml_nfse']['name'] . ' size=' . $_FILES['xml_nfse']['size'] . ' tmp=' . $_FILES['xml_nfse']['tmp_name']);

        $file = $_FILES['xml_nfse'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'xml') {
            log_message('error', '[Certificado preview_importar_ajax] BLOQUEADO: extensao invalida=' . $ext);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Arquivo deve ser XML (.xml)']);
            return;
        }

        $xmlContent = file_get_contents($file['tmp_name']);
        if (empty($xmlContent)) {
            log_message('error', '[Certificado preview_importar_ajax] BLOQUEADO: arquivo vazio apos file_get_contents');
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Arquivo XML vazio.']);
            return;
        }
        log_message('debug', '[Certificado preview_importar_ajax] XML lido. tamanho=' . strlen($xmlContent) . ' primeiros_200_chars=' . substr($xmlContent, 0, 200));

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        if (!@$dom->loadXML($xmlContent)) {
            $erros = libxml_get_errors();
            $msgs = [];
            foreach ($erros as $e) {
                $msgs[] = trim($e->message) . ' (linha ' . $e->line . ')';
            }
            libxml_clear_errors();
            log_message('error', '[Certificado preview_importar_ajax] BLOQUEADO: XML mal formado. erros=' . implode('; ', $msgs));
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'XML inválido: ' . implode('; ', $msgs)]);
            return;
        }
        log_message('debug', '[Certificado preview_importar_ajax] XML parseado com sucesso');

        $dadosExtraidos = $this->extrairDadosXmlNfse($dom);
        log_message('debug', '[Certificado preview_importar_ajax] Dados extraidos=' . json_encode($dadosExtraidos));

        if (empty($dadosExtraidos['numero_nfse']) && empty($dadosExtraidos['chave_acesso'])) {
            log_message('error', '[Certificado preview_importar_ajax] BLOQUEADO: numero e chave vazios. dados=' . json_encode($dadosExtraidos));
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Não foi possível identificar dados da nota no XML. Verifique se o arquivo é uma NFS-e válida.']);
            return;
        }

        log_message('debug', '[Certificado preview_importar_ajax] ========== SUCESSO ========== numero=' . $dadosExtraidos['numero_nfse'] . ' chave=' . $dadosExtraidos['chave_acesso']);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'numero_nfse' => $dadosExtraidos['numero_nfse'],
            'chave_acesso' => $dadosExtraidos['chave_acesso'],
            'codigo_verificacao' => $dadosExtraidos['codigo_verificacao'],
            'data_emissao' => $dadosExtraidos['data_emissao'],
            'valor_servicos' => $dadosExtraidos['valor_servicos'],
            'valor_liquido' => $dadosExtraidos['valor_liquido'],
            'xml_base64' => base64_encode($xmlContent)
        ]);
    }

    /**
     * Salvar importação de NFSe com vínculo a OS
     */
    public function salvar_importacao()
    {
        log_message('debug', '[Certificado salvar_importacao] ========== INICIO ==========');
        log_message('debug', '[Certificado salvar_importacao] REQUEST_METHOD=' . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . ' POST=' . json_encode($this->input->post()));

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cCertificado')) {
            log_message('error', '[Certificado salvar_importacao] BLOQUEADO: sem permissao. Permissao=' . $this->session->userdata('permissao'));
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado/importar_nfse');
        }

        $os_id = $this->input->post('os_id');
        $xmlBase64 = $this->input->post('xml_base64');
        log_message('debug', '[Certificado salvar_importacao] os_id=' . ($os_id ?: 'vazio') . ' xml_base64_length=' . strlen($xmlBase64 ?: ''));

        if (empty($xmlBase64)) {
            log_message('error', '[Certificado salvar_importacao] BLOQUEADO: xml_base64 vazio');
            $this->session->set_flashdata('error', 'Dados do XML não recebidos.');
            redirect('certificado/importar_nfse');
        }

        $xmlContent = base64_decode($xmlBase64);
        if (empty($xmlContent)) {
            log_message('error', '[Certificado salvar_importacao] BLOQUEADO: base64_decode retornou vazio');
            $this->session->set_flashdata('error', 'XML inválido ou corrompido.');
            redirect('certificado/importar_nfse');
        }
        log_message('debug', '[Certificado salvar_importacao] XML decodificado. tamanho=' . strlen($xmlContent) . ' primeiros_200_chars=' . substr($xmlContent, 0, 200));

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        if (!@$dom->loadXML($xmlContent)) {
            libxml_clear_errors();
            log_message('error', '[Certificado salvar_importacao] BLOQUEADO: XML mal formado apos decode');
            $this->session->set_flashdata('error', 'XML inválido ou mal formado.');
            redirect('certificado/importar_nfse');
        }

        $dadosExtraidos = $this->extrairDadosXmlNfse($dom);
        log_message('debug', '[Certificado salvar_importacao] Dados extraidos=' . json_encode($dadosExtraidos));

        if (empty($dadosExtraidos['numero_nfse']) && empty($dadosExtraidos['chave_acesso'])) {
            log_message('error', '[Certificado salvar_importacao] BLOQUEADO: numero e chave vazios');
            $this->session->set_flashdata('error', 'Não foi possível identificar o número da nota no XML.');
            redirect('certificado/importar_nfse');
        }

        $cert = $this->certificado_model->getCertificadoAtivo();
        $certId = $cert ? $cert->id : null;
        log_message('debug', '[Certificado salvar_importacao] certId=' . ($certId ?: 'null'));

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
            'xml' => $xmlContent,
            'os_id' => $os_id ?: null
        ];

        log_message('debug', '[Certificado salvar_importacao] Chamando importarNFSe. numero=' . $numero . ' os_id=' . ($os_id ?: 'null') . ' chave=' . $chave);
        $resultado = $this->certificado_model->importarNFSe($dados, $certId);
        log_message('debug', '[Certificado salvar_importacao] Resultado model=' . json_encode($resultado));

        if (isset($resultado['success'])) {
            log_message('info', '[Certificado salvar_importacao] ========== SUCESSO ========== id=' . ($resultado['id'] ?? '?'));
            $this->session->set_flashdata('success', 'NFS-e #' . $numero . ' importada com sucesso!' . ($os_id ? ' Vinculada à OS #' . $os_id . '.' : ''));

            if ($valorTotal > 0) {
                $this->impostos_model->reterImpostos([
                    'valor_bruto' => $valorTotal,
                    'cliente_id' => 0,
                    'nota_fiscal' => $numero,
                    'data_competencia' => date('Y-m-01', strtotime($dataEmissao))
                ]);
            }
        } else {
            log_message('error', '[Certificado salvar_importacao] ========== ERRO ========== msg=' . ($resultado['error'] ?? 'desconhecido'));
            $this->session->set_flashdata('error', $resultado['error'] ?? 'Erro ao importar nota.');
        }

        redirect('certificado/importar_nfse');
    }

    /**
     * Download do XML de uma NFS-e importada (do banco de dados)
     */
    public function download_xml($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) {
            $this->session->set_flashdata('error', 'Sem permissão.');
            redirect('certificado');
        }

        $nota = $this->db->where('id', $id)->get('certificado_nfe_importada')->row();
        if (!$nota || empty($nota->dados_xml)) {
            show_error('XML não encontrado.', 404);
            return;
        }

        $filename = 'nfse_' . ($nota->numero ?: $nota->id) . '.xml';
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($nota->dados_xml));
        echo $nota->dados_xml;
        exit;
    }

    /**
     * Visualizar XML de uma NFS-e importada no navegador (sem forcar download)
     */
    public function visualizar_xml($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) {
            show_error('Sem permissao.', 403);
            return;
        }

        $nota = $this->db->where('id', $id)->get('certificado_nfe_importada')->row();
        if (!$nota || empty($nota->dados_xml)) {
            show_error('XML nao encontrado.', 404);
            return;
        }

        header('Content-Type: text/xml; charset=utf-8');
        echo $nota->dados_xml;
        exit;
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
