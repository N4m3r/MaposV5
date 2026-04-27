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
            'regime' => $this->impostos_model->getConfig('IMPOSTO_REGIME_TRIBUTARIO') ?: 'simples_nacional',
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

                    if (($sincronizacao['regime'] ?? '') === 'lucro_presumido') {
                        // Lucro Presumido — não usa anexo/faixa do Simples
                        $this->impostos_model->setConfig('IMPOSTO_REGIME_TRIBUTARIO', 'lucro_presumido');
                        $this->impostos_model->setConfig('IMPOSTO_ANEXO_PADRAO', null);
                        $this->impostos_model->setConfig('IMPOSTO_FAIXA_ATUAL', null);
                        $this->impostos_model->setConfig('IMPOSTO_RETENCAO_AUTOMATICA', '1');
                        $this->impostos_model->setConfig('IMPOSTO_DRE_INTEGRACAO', '1');

                        log_info('Regime Lucro Presumido detectado para CNPJ: ' . $dados['cnpj']);

                        $this->session->set_flashdata('success', 'Certificado configurado com sucesso! Regime tributário: Lucro Presumido. As alíquotas de retenção foram configuradas automaticamente.');
                    } else {
                        // Simples Nacional
                        $this->impostos_model->setConfig('IMPOSTO_REGIME_TRIBUTARIO', 'simples_nacional');
                        $this->impostos_model->setConfig('IMPOSTO_ANEXO_PADRAO', $sincronizacao['configuracao']['anexo_sugerido']);
                        $this->impostos_model->setConfig('IMPOSTO_FAIXA_ATUAL', '1');
                        $this->impostos_model->setConfig('IMPOSTO_RETENCAO_AUTOMATICA', '1');
                        $this->impostos_model->setConfig('IMPOSTO_DRE_INTEGRACAO', '1');

                        log_info('Configuração de impostos automática aplicada para CNPJ: ' . $dados['cnpj']);

                        $this->session->set_flashdata('success', 'Certificado configurado com sucesso! Regime: Simples Nacional — Anexo ' . $sincronizacao['configuracao']['anexo_sugerido'] . '. Verifique em Impostos > Configurações.');
                    }
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
            // Aplicar configurações automaticamente
            $this->load->model('impostos_model');

            if (($resultado['regime'] ?? '') === 'lucro_presumido') {
                $this->impostos_model->setConfig('IMPOSTO_REGIME_TRIBUTARIO', 'lucro_presumido');
                $this->impostos_model->setConfig('IMPOSTO_ANEXO_PADRAO', null);
                $this->impostos_model->setConfig('IMPOSTO_FAIXA_ATUAL', null);
                $this->impostos_model->setConfig('IMPOSTO_RETENCAO_AUTOMATICA', '1');
                $this->impostos_model->setConfig('IMPOSTO_DRE_INTEGRACAO', '1');

                $this->session->set_flashdata('success', 'Alíquotas sincronizadas! Regime detectado: Lucro Presumido. Alíquotas de retenção configuradas automaticamente.');
            } else {
                $this->impostos_model->setConfig('IMPOSTO_REGIME_TRIBUTARIO', 'simples_nacional');
                $this->impostos_model->setConfig('IMPOSTO_ANEXO_PADRAO', $resultado['configuracao']['anexo_sugerido']);
                $this->impostos_model->setConfig('IMPOSTO_FAIXA_ATUAL', '1');
                $this->impostos_model->setConfig('IMPOSTO_RETENCAO_AUTOMATICA', '1');
                $this->impostos_model->setConfig('IMPOSTO_DRE_INTEGRACAO', '1');

                $this->session->set_flashdata('success', 'Alíquotas sincronizadas e configuradas automaticamente! Anexo identificado: ' . $resultado['configuracao']['anexo_sugerido']);
            }

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
     * Importação de NFS-e
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
            // Processar upload de XML
            if (isset($_FILES['xml_nfse']) && $_FILES['xml_nfse']['tmp_name']) {
                // Validar extensão e MIME type
                $ext = strtolower(pathinfo($_FILES['xml_nfse']['name'], PATHINFO_EXTENSION));
                if ($ext !== 'xml') {
                    $this->session->set_flashdata('error', 'Arquivo deve ser XML.');
                    redirect('certificado/importar_nfse');
                }

                // Proteger contra XXE
                libxml_disable_entity_loader(true);
                $xml = simplexml_load_file($_FILES['xml_nfse']['tmp_name'], 'SimpleXMLElement', LIBXML_NONET);

                if ($xml) {
                    $dados = [
                        'chave_acesso' => (string)$xml->Nfse->Numero,
                        'numero' => (string)$xml->Nfse->Numero,
                        'data_emissao' => date('Y-m-d H:i:s', strtotime((string)$xml->Nfse->DataEmissao)),
                        'valor_total' => (float)$xml->Nfse->ValoresNfse->ValorServicos,
                        'valor_impostos' => (float)$xml->Nfse->ValoresNfse->ValorIss,
                        'situacao' => 'Autorizada',
                        'xml' => file_get_contents($_FILES['xml_nfse']['tmp_name'])
                    ];

                    $resultado = $this->certificado_model->importarNFSe($dados, $cert->id);

                    if (isset($resultado['success'])) {
                        $this->session->set_flashdata('success', 'NFS-e importada com sucesso!');

                        // Criar lançamento no sistema de impostos
                        $this->impostos_model->reterImpostos([
                            'valor_bruto' => $dados['valor_total'],
                            'cliente_id' => 0, // Sistema
                            'nota_fiscal' => $dados['numero'],
                            'data_competencia' => date('Y-m-01', strtotime($dados['data_emissao']))
                        ]);
                    } else {
                        $this->session->set_flashdata('error', $resultado['error']);
                    }
                } else {
                    $this->session->set_flashdata('error', 'Arquivo XML inválido.');
                }
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
