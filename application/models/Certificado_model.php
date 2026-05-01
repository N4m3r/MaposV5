<?php
/**
 * Model: Certificado Digital
 * Gerencia certificados A1/A3 e integração com webservices da Receita Federal
 */
class Certificado_model extends CI_Model
{
    private $certPath;
    private $senha;
    private $tempDir;

    public function __construct()
    {
        parent::__construct();
        $this->certPath = APPPATH . 'private/certificados/';
        $this->tempDir = APPPATH . 'private/temp/';

        // Criar diretórios se não existirem
        if (!is_dir($this->certPath)) {
            mkdir($this->certPath, 0755, true);
        }
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    // ==================== GERENCIAMENTO DO CERTIFICADO ====================

    /**
     * Obtém certificado ativo
     */
    public function getCertificadoAtivo()
    {
        $this->db->where('ativo', 1);
        $this->db->order_by('id', 'DESC');
        return $this->db->get('certificado_digital')->row();
    }

    /**
     * Salva novo certificado
     */
    public function salvarCertificado($dados, $arquivo = null)
    {
        // Validar senha
        if (empty($dados['senha'])) {
            return ['error' => 'Senha do certificado é obrigatória'];
        }

        // Validar CNPJ
        $cnpjLimpo = preg_replace('/[^0-9]/', '', $dados['cnpj'] ?? '');
        if (strlen($cnpjLimpo) !== 14) {
            return ['error' => 'CNPJ inválido. Informe um CNPJ com 14 dígitos.'];
        }
        $dados['cnpj'] = $cnpjLimpo;

        // Criptografar senha
        $senhaCriptografada = $this->criptografarSenha($dados['senha']);
        if ($senhaCriptografada === false) {
            return ['error' => 'Erro ao criptografar senha do certificado. Verifique a configuração encryption_key.'];
        }
        $dados['senha'] = $senhaCriptografada;

        // Se tem arquivo (A1), processar
        if ($arquivo && !empty($arquivo['tmp_name'])) {
            $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $extPermitidas = ['pfx', 'p12'];
            if (!in_array($ext, $extPermitidas)) {
                return ['error' => 'Formato de arquivo não permitido. Use .pfx ou .p12'];
            }

            $hash = hash_file('sha256', $arquivo['tmp_name']);
            $nomeArquivo = $dados['cnpj'] . '_' . date('YmdHis') . '.' . $ext;
            $caminhoCompleto = $this->certPath . $nomeArquivo;

            // Garantir que o diretório existe
            if (!is_dir($this->certPath)) {
                if (!mkdir($this->certPath, 0755, true)) {
                    return ['error' => 'Erro ao criar diretório de certificados: ' . $this->certPath];
                }
            }

            // Mover arquivo
            if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
                // Verificar integridade
                $senhaDescriptografada = $this->descriptografarSenha($dados['senha']);
                if (!$this->verificarCertificado($caminhoCompleto, $senhaDescriptografada)) {
                    @unlink($caminhoCompleto);
                    return ['error' => 'Certificado inválido ou senha incorreta. Não foi possível ler o arquivo .pfx/.p12.'];
                }

                $dados['arquivo_caminho'] = $caminhoCompleto;
                $dados['arquivo_hash'] = $hash;
                $dados['tipo'] = 'A1';

                // Extrair dados do certificado
                $infoCert = $this->extrairInfoCertificado($caminhoCompleto, $senhaDescriptografada);
                if ($infoCert) {
                    $dados['data_validade'] = $infoCert['validade'];
                    $dados['data_emissao'] = $infoCert['emissao'];
                    $dados['emissor'] = $infoCert['emissor'];
                    $dados['serial_number'] = $infoCert['serial'];
                    $dados['razao_social'] = $dados['razao_social'] ?: $infoCert['dono'];

                    log_message('error', 'Certificado upload: Emissor=' . $infoCert['emissor'] . ' | Dono=' . $infoCert['dono'] . ' | CNPJCert=' . ($infoCert['cnpj_certificado'] ?? 'N/A') . ' | Validade=' . $infoCert['validade']);

                    // Alertar se CNPJ do certificado difere do informado
                    if (!empty($infoCert['cnpj_certificado']) && $infoCert['cnpj_certificado'] !== $dados['cnpj']) {
                        log_message('error', 'Certificado upload ALERTA: CNPJ do certificado (' . $infoCert['cnpj_certificado'] . ') difere do CNPJ informado (' . $dados['cnpj'] . ')');
                    }
                } else {
                    @unlink($caminhoCompleto);
                    return ['error' => 'Não foi possível extrair informações do certificado. O arquivo pode estar corrompido.'];
                }
            } else {
                return ['error' => 'Erro ao salvar arquivo do certificado no servidor'];
            }
        } else {
            // A3 - Configuração manual
            $dados['tipo'] = 'A3';
        }

        // Desativar certificados anteriores do mesmo CNPJ
        $this->db->where('cnpj', $dados['cnpj']);
        $this->db->update('certificado_digital', ['ativo' => 0]);

        // Inserir novo
        $dados['ativo'] = 1;
        $dados['created_at'] = date('Y-m-d H:i:s');
        $dados['updated_at'] = date('Y-m-d H:i:s');

        // Só incluir 'ambiente' se a coluna existir no banco
        if ($this->db->field_exists('ambiente', 'certificado_digital')) {
            $dados['ambiente'] = $dados['ambiente'] ?? 'homologacao';
        } else {
            unset($dados['ambiente']);
        }

        if ($this->db->insert('certificado_digital', $dados)) {
            return ['success' => true, 'id' => $this->db->insert_id()];
        }

        return ['error' => 'Erro ao salvar no banco de dados'];
    }

    /**
     * Verifica se certificado é válido
     */
    private function verificarCertificado($caminho, $senha)
    {
        try {
            $pfx = file_get_contents($caminho);
            $certs = [];
            if (!openssl_pkcs12_read($pfx, $certs, $senha)) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Extrai informações do certificado
     * Inclui emissor (de múltiplas fontes), CNPJ do certificado e validade
     */
    private function extrairInfoCertificado($caminho, $senha)
    {
        try {
            $pfx = file_get_contents($caminho);
            $certs = [];

            if (!openssl_pkcs12_read($pfx, $certs, $senha)) {
                $sslError = openssl_error_string();
                log_message('error', 'Certificado_model: Erro ao ler PKCS12 em extrairInfoCertificado: ' . $sslError);
                return false;
            }

            $cert = openssl_x509_parse($certs['cert']);
            if (!$cert) {
                log_message('error', 'Certificado_model: openssl_x509_parse falhou');
                return false;
            }

            // Extrair emissor de múltiplas fontes possíveis
            $issuer = $cert['issuer'] ?? [];
            $emissor = 'Desconhecido';
            if (!empty($issuer['O'])) {
                $emissor = $issuer['O'];
            } elseif (!empty($issuer['CN'])) {
                $emissor = $issuer['CN'];
            } elseif (!empty($issuer['OU'])) {
                $emissor = $issuer['OU'];
            } elseif (!empty($issuer['L'])) {
                $emissor = $issuer['L'];
            }

            // Extrair CNPJ do certificado (do subject)
            $subject = $cert['subject'] ?? [];
            $dono = $subject['CN'] ?? '';
            $cnpjCertificado = '';

            // Tentar extrair CNPJ do CN (ex: "CNPJ: 12345678000195" ou "NOME: 12345678000195")
            if (preg_match('/(\d{14})/', $dono, $m)) {
                $cnpjCertificado = $m[1];
            }
            // Também pode estar em x500UniqueIdentifier ou unstructuredName
            if (empty($cnpjCertificado) && !empty($subject['x500UniqueIdentifier'])) {
                $cnpjCertificado = preg_replace('/\D/', '', $subject['x500UniqueIdentifier']);
            }
            if (empty($cnpjCertificado) && !empty($subject['unstructuredName'])) {
                $cnpjCertificado = preg_replace('/\D/', '', $subject['unstructuredName']);
            }

            // Extrair extended key usage para diagnóstico
            $eku = $cert['extensions']['extendedKeyUsage'] ?? 'N/A';
            $hasClientAuth = false;
            if (!empty($eku)) {
                if (strpos($eku, 'Client Authentication') !== false || strpos($eku, '1.3.6.1.5.5.7.3.2') !== false) {
                    $hasClientAuth = true;
                }
            }

            log_message('error', 'Certificado_model [DIAGNOSTICO]: Emissor=' . $emissor . ' | Dono=' . $dono . ' | CNPJCert=' . ($cnpjCertificado ?: 'N/A') . ' | EKU_ClientAuth=' . ($hasClientAuth ? 'SIM' : 'NAO'));

            return [
                'validade' => date('Y-m-d H:i:s', $cert['validTo_time_t']),
                'emissao' => date('Y-m-d H:i:s', $cert['validFrom_time_t']),
                'emissor' => $emissor,
                'serial' => $cert['serialNumber'] ?? '',
                'dono' => $dono,
                'cnpj_certificado' => $cnpjCertificado,
                'has_client_auth' => $hasClientAuth,
                'subject' => $subject,
                'issuer' => $issuer,
            ];
        } catch (Exception $e) {
            log_message('error', 'Certificado_model: Exception em extrairInfoCertificado: ' . $e->getMessage());
            return false;
        }
    }

    // ==================== CONSULTAS À RECEITA FEDERAL ====================

    /**
     * Verifica se um arquivo PEM é válido (contém ao menos um certificado decodificável)
     */
    private function isValidPemFile($path)
    {
        if (empty($path) || !file_exists($path) || filesize($path) === 0) {
            return false;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            return false;
        }
        // Procura blocos PEM
        if (strpos($content, '-----BEGIN CERTIFICATE-----') === false) {
            return false;
        }
        // Valida que o primeiro certificado é parseável pelo OpenSSL
        if (!preg_match('/-----BEGIN CERTIFICATE-----(.+?)-----END CERTIFICATE-----/s', $content, $matches)) {
            return false;
        }
        $certPem = "-----BEGIN CERTIFICATE-----\n" . trim($matches[1]) . "\n-----END CERTIFICATE-----";
        $cert = @openssl_x509_read($certPem);
        if (!$cert) {
            return false;
        }
        return true;
    }

    /**
     * Baixa a cadeia ICP-Brasil atualizada do repositório oficial
     */
    private function baixarCadeiaIcpBrasil()
    {
        $destino = FCPATH . 'assets/certs/ac-icp-brasil.pem';
        $urls = [
            'https://acraiz.icpbrasil.gov.br/credenciadas/CertificadosAC-ICP-Brasil/ACcompactado.zip',
        ];
        $tempZip = sys_get_temp_dir() . '/icpbrasil_cadeia_' . time() . '.zip';

        foreach ($urls as $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $zipData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200 && !empty($zipData)) {
                file_put_contents($tempZip, $zipData);
                $zip = new ZipArchive();
                if ($zip->open($tempZip) === true) {
                    $dir = dirname($destino);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    // Extrair todos os .crt/.pem para o diretório
                    $certContent = '';
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $fileName = $zip->getNameIndex($i);
                        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        if (in_array($ext, ['crt', 'pem', 'cer'])) {
                            $certData = $zip->getFromIndex($i);
                            if ($certData) {
                                $certContent .= trim($certData) . "\n\n";
                            }
                        }
                    }
                    $zip->close();
                    @unlink($tempZip);
                    if (!empty($certContent)) {
                        file_put_contents($destino, $certContent);
                        log_message('error', 'Certificado_model: Cadeia ICP-Brasil baixada e salva em ' . $destino);
                        return true;
                    }
                }
                @unlink($tempZip);
            }
        }
        return false;
    }

    /**
     * Consulta CNPJ na Receita Federal
     * Extrai dados cadastrais e regime tributário
     */
    public function consultarCNPJ($cnpj)
    {
        $certificado = $this->getCertificadoAtivo();
        if (!$certificado) {
            return ['error' => 'Nenhum certificado configurado'];
        }

        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        $url = "https://www.receitaws.com.br/v1/cnpj/" . $cnpj;

        try {
            $result = $this->executarGetCurl($url);
            $response = $result['response'];
            $httpCode = $result['httpCode'];

            // Fallback para BrasilAPI se houver falha de conexão
            if (($response === false || $result['curlErrno'] !== 0 || $httpCode == 0) && $httpCode != 200) {
                $urlFallback = "https://brasilapi.com.br/api/cnpj/v1/" . $cnpj;
                $result = $this->executarGetCurl($urlFallback);
                $response = $result['response'];
                $httpCode = $result['httpCode'];

                if (($response === false || $result['curlErrno'] !== 0 || $httpCode == 0) && $httpCode != 200) {
                    $errorDetail = !empty($result['curlError']) ? $result['curlError'] : ('HTTP ' . $httpCode);
                    $this->registrarConsulta($certificado->id, 'CNPJ', false, null, $errorDetail);
                    return ['error' => 'Não foi possível consultar o CNPJ. Erro de conexão: ' . $errorDetail . '. Verifique se o servidor tem acesso à internet e se o certificado SSL está atualizado.'];
                }
            }

            if ($httpCode != 200 || empty($response)) {
                if ($httpCode == 429) {
                    return ['error' => 'Limite de requisições atingido. Aguarde 1 minuto e tente novamente.'];
                }
                $this->registrarConsulta($certificado->id, 'CNPJ', false, null, 'HTTP ' . $httpCode);
                return ['error' => 'CNPJ não encontrado ou serviço indisponível (HTTP ' . $httpCode . ')'];
            }

            $dados = json_decode($response, true);

            if (!$dados || (isset($dados['status']) && $dados['status'] == 'error')) {
                return ['error' => $dados['message'] ?? 'CNPJ não encontrado'];
            }

            // Normaliza dados se vieram do BrasilAPI
            $dados = $this->normalizarDadosCNPJ($dados);

            // Atualizar dados do certificado com os dados do CNPJ
            $updateData = ['updated_at' => date('Y-m-d H:i:s')];
            if (!empty($dados['nome'])) $updateData['razao_social'] = $dados['nome'];
            if (!empty($dados['fantasia'])) $updateData['nome_fantasia'] = $dados['fantasia'];

            $this->db->where('id', $certificado->id);
            $this->db->update('certificado_digital', $updateData);

            // Registrar consulta
            $this->registrarConsulta($certificado->id, 'CNPJ', true, $dados);

            return [
                'success' => true,
                'data' => $dados
            ];

        } catch (Exception $e) {
            $this->registrarConsulta($certificado->id, 'CNPJ', false, null, $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Consulta Simples Nacional via API Receita
     * Verifica regime tributário e extrai dados do certificado
     */
    public function consultarSimplesNacional($cnpj)
    {
        $certificado = $this->getCertificadoAtivo();
        if (!$certificado) {
            return ['error' => 'Nenhum certificado configurado'];
        }

        // API da Receita para consulta CNPJ (inclui dados do Simples)
        $url = "https://www.receitaws.com.br/v1/cnpj/" . preg_replace('/[^0-9]/', '', $cnpj);

        try {
            $result = $this->executarGetCurl($url);
            $response = $result['response'];
            $httpCode = $result['httpCode'];

            // Fallback para BrasilAPI se houver falha de conexão
            if (($response === false || $result['curlErrno'] !== 0 || $httpCode == 0) && $httpCode != 200) {
                $urlFallback = "https://brasilapi.com.br/api/cnpj/v1/" . preg_replace('/[^0-9]/', '', $cnpj);
                $result = $this->executarGetCurl($urlFallback);
                $response = $result['response'];
                $httpCode = $result['httpCode'];

                if (($response === false || $result['curlErrno'] !== 0 || $httpCode == 0) && $httpCode != 200) {
                    $errorDetail = !empty($result['curlError']) ? $result['curlError'] : ('HTTP ' . $httpCode);
                    $this->registrarConsulta($certificado->id, 'SIMPLES_NACIONAL', false, null, $errorDetail);
                    return ['error' => 'Não foi possível consultar o CNPJ. Erro de conexão: ' . $errorDetail . '. Verifique se o servidor tem acesso à internet e se o certificado SSL está atualizado.'];
                }
            }

            if ($httpCode != 200 || empty($response)) {
                $errorMsg = $httpCode == 429 ? 'Limite de requisições atingido. Aguarde 1 minuto e tente novamente.' : 'Não foi possível consultar o CNPJ (HTTP ' . $httpCode . ')';
                $this->registrarConsulta($certificado->id, 'SIMPLES_NACIONAL', false, null, $errorMsg);
                return ['error' => $errorMsg];
            }

            $dados = json_decode($response, true);

            if (!$dados || isset($dados['status']) && $dados['status'] == 'error') {
                $errorMsg = $dados['message'] ?? 'CNPJ não encontrado na Receita Federal';
                $this->registrarConsulta($certificado->id, 'SIMPLES_NACIONAL', false, null, $errorMsg);
                return ['error' => $errorMsg];
            }

            // Normaliza dados se vieram do BrasilAPI
            $dados = $this->normalizarDadosCNPJ($dados);

            // ReceitaWS retorna "Sim"/"Nao"/"SIM"/"NAO"/true/false — normalizar
            $opcaoSimples = $dados['opcao_pelo_simples'] ?? null;
            $opcaoMei = $dados['opcao_pelo_mei'] ?? null;

            // Converter para booleano de forma robusta
            $isOptanteSimples = $this->normalizarBooleano($opcaoSimples);
            $isMei = $this->normalizarBooleano($opcaoMei);

            // Extrair dados do certificado (informações do CNPJ)
            $razaoSocial = $dados['nome'] ?? ($certificado->razao_social ?? '');
            $cnaePrincipal = $dados['cnae_fiscal'] ?? '';
            $cnaeDescricao = $dados['cnae_fiscal_descricao'] ?? ($dados['atividade_principal'][0]['text'] ?? '');
            $porte = $dados['porte'] ?? '';
            $situacao = $dados['situacao'] ?? '';
            $naturezaJuridica = $dados['natureza_juridica'] ?? '';

            // Extrair dados do Simples Nacional
            $simplesData = [
                'optante_simples' => $isOptanteSimples,
                'data_opcao' => $dados['data_opcao_pelo_simples'] ?? null,
                'data_exclusao' => $dados['data_exclusao_do_simples'] ?? null,
                'simei' => $isMei,
                'cnae_descricao' => $cnaeDescricao,
                'cnae_codigo' => $cnaePrincipal,
                'razao_social' => $razaoSocial,
                'porte' => $porte,
                'situacao' => $situacao,
                'natureza_juridica' => $naturezaJuridica,
            ];

            // Sempre opera como Simples Nacional (mesmo se API não detectar)
            $anexoDetectado = $this->identificarAnexo($cnaeDescricao);
            if ($isOptanteSimples) {
                $simplesData['anexo_sugerido'] = $anexoDetectado;
                $simplesData['regime'] = 'simples_nacional';
            } elseif ($isMei) {
                $simplesData['anexo_sugerido'] = 'MEI';
                $simplesData['regime'] = 'simples_nacional';
            } else {
                $simplesData['anexo_sugerido'] = $anexoDetectado;
                $simplesData['regime'] = 'simples_nacional';
            }

            // Atualizar dados do certificado com informações do CNPJ
            $this->db->where('id', $certificado->id);
            $this->db->update('certificado_digital', [
                'razao_social' => $razaoSocial,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->registrarConsulta($certificado->id, 'SIMPLES_NACIONAL', true, $simplesData);

            return [
                'success' => true,
                'data' => $simplesData
            ];

        } catch (Exception $e) {
            $this->registrarConsulta($certificado->id, 'SIMPLES_NACIONAL', false, null, $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Normaliza valores booleanos da API ReceitaWS
     * ReceitaWS pode retornar: true, false, "Sim", "Nao", "SIM", "NAO", "Yes", "No", null
     */
    private function normalizarBooleano($valor)
    {
        if (is_bool($valor)) {
            return $valor;
        }
        if ($valor === null || $valor === '') {
            return false;
        }
        if (is_string($valor)) {
            $v = strtolower(trim($valor));
            return in_array($v, ['sim', 's', 'yes', 'y', 'true', '1']);
        }
        return (bool)$valor;
    }

    /**
     * Identifica anexo do Simples baseado na atividade (CNAE)
     */
    private function identificarAnexo($atividade)
    {
        $atividade = strtolower($atividade);

        // Anexo III - Serviços
        $servicos = [
            'serviço', 'manutenção', 'reparo', 'assistência técnica',
            'consultoria', 'desenvolvimento', 'software', 'informática'
        ];

        // Anexo IV - Construção
        $construcao = [
            'construção', 'obra', 'edificação', 'instalação',
            'serviço de construção'
        ];

        // Anexo V - Comércio e Indústria
        $comercio = [
            'comércio', 'venda', 'revenda', 'varejo', 'atacado',
            'loja', 'supermercado', 'material', 'construção',
            'materiais de construção', 'depósito', 'depósito de material'
        ];

        // Verificar Comércio primeiro (prioridade)
        foreach ($comercio as $termo) {
            if (strpos($atividade, $termo) !== false) return 'V';
        }

        foreach ($construcao as $termo) {
            if (strpos($atividade, $termo) !== false) return 'IV';
        }

        foreach ($servicos as $termo) {
            if (strpos($atividade, $termo) !== false) return 'III';
        }

        return 'III'; // Padrão para serviços
    }

    /**
     * Executa requisição GET via cURL com logging detalhado de erros
     */
    private function executarGetCurl($url, $useSslVerify = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $useSslVerify);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $useSslVerify ? 2 : 0);
        // Não usa CA customizado para APIs públicas — o CA do sistema é suficiente
        // e evita erros se o arquivo ac-icp-brasil.pem estiver corrompido
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlErrno !== 0) {
            log_message('error', "Certificado_model: cURL falhou para {$url}. Errno={$curlErrno} Error={$curlError} HTTP={$httpCode}");
        }

        return [
            'response' => $response,
            'httpCode' => $httpCode,
            'curlErrno' => $curlErrno,
            'curlError' => $curlError,
        ];
    }

    /**
     * Normaliza dados de CNPJ entre diferentes APIs (ReceitaWS / BrasilAPI)
     */
    private function normalizarDadosCNPJ($dados)
    {
        if (!is_array($dados)) {
            return $dados;
        }

        // BrasilAPI usa nomes de campos diferentes
        if (isset($dados['razao_social']) && !isset($dados['nome'])) {
            $dados['nome'] = $dados['razao_social'];
        }
        if (isset($dados['nome_fantasia']) && !isset($dados['fantasia'])) {
            $dados['fantasia'] = $dados['nome_fantasia'];
        }
        if (isset($dados['descricao_situacao_cadastral']) && !isset($dados['situacao'])) {
            $dados['situacao'] = $dados['descricao_situacao_cadastral'];
        }
        // Garante que campos esperados existam
        $camposPadrao = [
            'nome', 'fantasia', 'cnae_fiscal', 'cnae_fiscal_descricao',
            'porte', 'situacao', 'natureza_juridica',
            'opcao_pelo_simples', 'data_opcao_pelo_simples',
            'data_exclusao_do_simples', 'opcao_pelo_mei'
        ];
        foreach ($camposPadrao as $campo) {
            if (!isset($dados[$campo])) {
                $dados[$campo] = null;
            }
        }
        return $dados;
    }

    /**
     * Sincroniza alíquotas com base nos dados da Receita
     */
    public function sincronizarAliquotas($cnpj = null)
    {
        if (!$cnpj) {
            $certificado = $this->getCertificadoAtivo();
            if (!$certificado) {
                return ['error' => 'Nenhum certificado configurado'];
            }
            $cnpj = $certificado->cnpj;
        }

        $resultado = $this->consultarSimplesNacional($cnpj);

        if (isset($resultado['error'])) {
            return $resultado;
        }

        $dados = $resultado['data'];

        // Sempre força Simples Nacional — independente do retorno da API externa
        $anexoSugerido = $dados['anexo_sugerido'] ?: $this->identificarAnexo($dados['cnae_descricao'] ?? '');
        if (!$anexoSugerido) {
            $anexoSugerido = 'III';
        }

        $config = [
            'optante_simples' => true,
            'regime' => 'simples_nacional',
            'anexo_sugerido' => $anexoSugerido,
            'data_opcao' => $dados['data_opcao'],
            'simei' => $dados['simei'] ?? false
        ];

        return [
            'success' => true,
            'configuracao' => $config,
            'mensagem' => 'Configurado para Simples Nacional — Anexo ' . $config['anexo_sugerido'] . '.'
        ];
    }

    // ==================== INTEGRAÇÃO NFS-e ====================

    /**
     * Consulta NFS-e emitidas no período
     * Integração com prefeituras via webservice
     */
    public function consultarNFSePeriodo($data_inicio, $data_fim, $certificado_id = null)
    {
        if (!$certificado_id) {
            $cert = $this->getCertificadoAtivo();
            if (!$cert) return ['error' => 'Nenhum certificado'];
            $certificado_id = $cert->id;
        }

        // Aqui seria implementada a integração real com a prefeitura
        // Por enquanto, simulamos com dados do sistema

        // Buscar notas já importadas
        $this->db->where('certificado_id', $certificado_id);
        $this->db->where('data_emissao >=', $data_inicio);
        $this->db->where('data_emissao <=', $data_fim);
        $notas = $this->db->get('certificado_nfe_importada')->result();

        return [
            'success' => true,
            'total_notas' => count($notas),
            'notas' => $notas
        ];
    }

    /**
     * Importa NFS-e manualmente (XML ou dados)
     */
    public function importarNFSe($dados, $certificado_id = null)
    {
        if (!$certificado_id) {
            $cert = $this->getCertificadoAtivo();
            if (!$cert) return ['error' => 'Nenhum certificado'];
            $certificado_id = $cert->id;
        }

        if (!$this->db->table_exists('certificado_nfe_importada')) {
            log_message('error', '[Certificado_model] Tabela certificado_nfe_importada nao existe');
            return ['error' => 'Tabela de notas importadas não existe. Execute as migrations.'];
        }

        // Verificar se já existe
        $this->db->where('chave_acesso', $dados['chave_acesso']);
        if ($this->db->get('certificado_nfe_importada')->num_rows() > 0) {
            return ['error' => 'Nota fiscal já importada'];
        }

        $insert = [
            'certificado_id' => $certificado_id,
            'chave_acesso' => $dados['chave_acesso'],
            'numero' => $dados['numero'],
            'serie' => $dados['serie'] ?? '1',
            'data_emissao' => $dados['data_emissao'],
            'data_importacao' => date('Y-m-d H:i:s'),
            'cnpj_destinatario' => preg_replace('/[^0-9]/', '', $dados['cnpj_destinatario'] ?? ''),
            'valor_total' => $dados['valor_total'],
            'valor_impostos' => $dados['valor_impostos'] ?? 0,
            'situacao' => $dados['situacao'] ?? 'Autorizada',
            'dados_xml' => isset($dados['xml']) ? $dados['xml'] : json_encode($dados)
        ];

        if ($this->db->insert('certificado_nfe_importada', $insert)) {
            return ['success' => true, 'id' => $this->db->insert_id()];
        }

        return ['error' => 'Erro ao importar nota'];
    }

    // ==================== UTILITÁRIOS ====================

    /**
     * Registra consulta no log
     */
    private function registrarConsulta($certificado_id, $tipo, $sucesso, $dados = null, $erro = null)
    {
        $this->db->insert('certificado_consultas', [
            'certificado_id' => $certificado_id,
            'tipo_consulta' => $tipo,
            'data_consulta' => date('Y-m-d H:i:s'),
            'sucesso' => $sucesso ? 1 : 0,
            'dados_retorno' => $dados ? json_encode($dados) : null,
            'erro' => $erro
        ]);

        // Atualizar último acesso no certificado
        $this->db->where('id', $certificado_id);
        $this->db->update('certificado_digital', [
            'ultimo_acesso' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Criptografa senha do certificado
     */
    private function criptografarSenha($senha)
    {
        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($senha, 'AES-256-CBC', $this->getChaveCriptografia(), OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) {
            return false;
        }
        // Armazenar IV + ciphertext em base64
        return base64_encode($iv . $ciphertext);
    }

    /**
     * Descriptografa senha (público para uso externo)
     */
    public function descriptografarSenha($senhaCriptografada)
    {
        $data = base64_decode($senhaCriptografada);
        if ($data === false || strlen($data) < 16) {
            return false;
        }
        $iv = substr($data, 0, 16);
        $ciphertext = substr($data, 16);
        return openssl_decrypt($ciphertext, 'AES-256-CBC', $this->getChaveCriptografia(), OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Chave de criptografia (deve ser configurada no arquivo de config)
     */
    private function getChaveCriptografia()
    {
        $key = $this->config->item('encryption_key');
        if (empty($key)) {
            log_message('error', 'Certificado_model: encryption_key não configurada. Configure em application/config/config.php');
            return 'mapos_cert_key_2024'; // fallback apenas para não quebrar
        }
        return $key;
    }

    /**
     * Verifica dias para expiração
     */
    public function verificarValidade()
    {
        $cert = $this->getCertificadoAtivo();
        if (!$cert) return ['dias' => 0, 'expirado' => true];

        $hoje = new DateTime();
        $validade = new DateTime($cert->data_validade);
        $diferenca = $hoje->diff($validade);

        return [
            'dias' => $diferenca->days,
            'expirado' => $diferenca->invert,
            'data_validade' => $cert->data_validade,
            'alerta' => $diferenca->days <= 30 && !$diferenca->invert
        ];
    }

    /**
     * Obtém todas as consultas realizadas
     */
    public function getConsultas($certificado_id = null, $limite = 50)
    {
        $this->db->select('cc.*, cd.cnpj, cd.razao_social');
        $this->db->from('certificado_consultas cc');
        $this->db->join('certificado_digital cd', 'cd.id = cc.certificado_id');

        if ($certificado_id) {
            $this->db->where('cc.certificado_id', $certificado_id);
        }

        $this->db->order_by('cc.data_consulta', 'DESC');
        $this->db->limit($limite);

        return $this->db->get()->result();
    }

    // ==================== EXTRAÇÃO PEM PARA NFS-e NACIONAL ====================

    /**
     * Extrai certificado e chave privada no formato PEM para uso com mTLS
     * Necessário para comunicação com a API NFS-e Nacional
     *
     * @param int|null $id ID do certificado (null = ativo)
     * @return array|false ['cert' => caminho_pem, 'key' => caminho_key, 'senha' => senha_descriptografada]
     *                      ou false em caso de erro
     */
    public function extrairCertificadoPem($id = null)
    {
        if ($id) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('ativo', 1);
            $this->db->order_by('id', 'DESC');
        }
        $certificado = $this->db->get('certificado_digital')->row();

        if (!$certificado) {
            log_message('error', 'NFS-e Nacional: Nenhum certificado encontrado');
            return false;
        }

        if (!isset($certificado->arquivo_caminho) || !file_exists($certificado->arquivo_caminho)) {
            log_message('error', 'NFS-e Nacional: Arquivo do certificado não encontrado: ' . ($certificado->arquivo_caminho ?? 'vazio'));
            return false;
        }

        // Descriptografar senha
        $senha = $this->descriptografarSenha($certificado->senha);
        if ($senha === false) {
            log_message('error', 'NFS-e Nacional: Falha ao descriptografar senha do certificado. encryption_key pode ter sido alterada.');
            return ['error' => 'Nao foi possivel descriptografar a senha do certificado. A chave de criptografia do sistema pode ter sido alterada apos o cadastro do certificado. Reenvie o certificado.'];
        }

        // Ler conteúdo do .pfx
        $pfxContent = file_get_contents($certificado->arquivo_caminho);
        if ($pfxContent === false) {
            log_message('error', 'NFS-e Nacional: Erro ao ler arquivo .pfx');
            return false;
        }

        // Extrair certificado e chave privada do PKCS12
        $certs = [];
        if (!openssl_pkcs12_read($pfxContent, $certs, $senha)) {
            $sslError = openssl_error_string();
            log_message('error', 'NFS-e Nacional: Erro ao ler PKCS12: ' . $sslError . ' | Senha fornecida (tamanho): ' . strlen($senha) . ' | Arquivo: ' . $certificado->arquivo_caminho);
            return ['error' => 'Senha do arquivo .pfx incorreta ou arquivo corrompido. Detalhe: ' . $sslError];
        }

        // Verificar se tem certificado e chave
        if (empty($certs['cert']) || empty($certs['pkey'])) {
            log_message('error', 'NFS-e Nacional: Certificado ou chave privada não encontrados no PKCS12');
            return false;
        }

        // Criar diretório temporário se não existir
        $tempDir = $this->tempDir;
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Gerar nomes de arquivo únicos
        $prefix = 'nfse_' . $certificado->id . '_' . time();

        $certPemPath = $tempDir . $prefix . '_cert.pem';
        $keyPemPath = $tempDir . $prefix . '_key.pem';

        // Salvar certificado PEM
        if (file_put_contents($certPemPath, $certs['cert']) === false) {
            log_message('error', 'NFS-e Nacional: Erro ao salvar certificado PEM');
            return false;
        }

        // Salvar chave privada PEM (sem senha para cURL mTLS)
        $keyPem = $certs['pkey'];
        if (file_put_contents($keyPemPath, $keyPem) === false) {
            @unlink($certPemPath);
            log_message('error', 'NFS-e Nacional: Erro ao salvar chave privada PEM');
            return false;
        }

        // Verificar validade e propriedades do certificado
        $certInfo = openssl_x509_parse($certs['cert']);
        if ($certInfo) {
            $validade = $certInfo['validTo_time_t'];
            if ($validade < time()) {
                @unlink($certPemPath);
                @unlink($keyPemPath);
                log_message('error', 'NFS-e Nacional: Certificado expirado em ' . date('Y-m-d H:i:s', $validade));
                return ['error' => 'Certificado digital expirado em ' . date('d/m/Y', $validade)];
            }

            // Log detalhado do certificado para diagnóstico
            $subject = $certInfo['subject'] ?? [];
            $issuer = $certInfo['issuer'] ?? [];
            $subjectCN = $subject['CN'] ?? 'N/A';
            $issuerO = $issuer['O'] ?? 'N/A';
            $issuerCN = $issuer['CN'] ?? 'N/A';
            $keyUsage = $certInfo['extensions']['keyUsage'] ?? 'N/A';
            $extKeyUsage = $certInfo['extensions']['extendedKeyUsage'] ?? 'N/A';

            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Certificado Subject CN=' . $subjectCN);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Certificado Issuer O=' . $issuerO . ' CN=' . $issuerCN);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Certificado KeyUsage=' . $keyUsage);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Certificado ExtendedKeyUsage=' . $extKeyUsage);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Certificado Validade=' . date('Y-m-d', $certInfo['validFrom_time_t'] ?? 0) . ' a ' . date('Y-m-d', $validade));
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Certificado Serial=' . ($certInfo['serialNumber'] ?? 'N/A'));

            // Validar Extended Key Usage para Client Authentication
            $hasClientAuth = false;
            if (!empty($extKeyUsage)) {
                if (strpos($extKeyUsage, 'TLS Web Client Authentication') !== false ||
                    strpos($extKeyUsage, 'Client Authentication') !== false ||
                    strpos($extKeyUsage, '1.3.6.1.5.5.7.3.2') !== false) {
                    $hasClientAuth = true;
                }
            }
            if (!$hasClientAuth) {
                log_message('error', 'NFS-e Nacional [ALERTA]: Certificado NÃO possui Extended Key Usage "Client Authentication". O SEFIN Nacional pode rejeitar com E4007.');
            } else {
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Certificado possui Client Authentication OK');
            }
        } else {
            log_message('error', 'NFS-e Nacional [ALERTA]: Não foi possível fazer parse do certificado extraído do .pfx');
        }

        // Converter chave privada PKCS#8 para PKCS#1 se necessário
        // Algumas versões do cURL/OpenSSL têm problemas com PKCS#8
        $keyPem = $certs['pkey'];
        if (strpos($keyPem, '-----BEGIN PRIVATE KEY-----') !== false) {
            $convertedKey = $this->converterPkcs8ParaPkcs1($keyPem);
            if ($convertedKey !== false) {
                $keyPem = $convertedKey;
                file_put_contents($keyPemPath, $keyPem);
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Chave privada convertida de PKCS#8 para PKCS#1');
            }
        }

        // Extrair cadeia completa (certificados extras no PKCS12)
        if (!empty($certs['extracerts'])) {
            $chainPem = '';
            foreach ($certs['extracerts'] as $extraCert) {
                $chainPem .= $extraCert . "\n";
            }
            // Anexar cadeia ao certificado PEM
            file_put_contents($certPemPath, "\n" . $chainPem, FILE_APPEND);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Cadeia de certificados adicionada. ' . count($certs['extracerts']) . ' certificado(s) intermediário(s)');
        }

        // Verificar se o arquivo cert.pem contém pelo menos 2 certificados (end-entity + cadeia)
        // Se não tiver, tentar baixar/build cadeia ICP-Brasil
        $certFileContent = file_get_contents($certPemPath);
        $certCount = substr_count($certFileContent, '-----BEGIN CERTIFICATE-----');
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Total de certificados no arquivo PEM=' . $certCount);

        if ($certCount < 2) {
            log_message('error', 'NFS-e Nacional [ALERTA]: Arquivo cert.pem contém apenas 1 certificado. A cadeia pode estar incompleta. O SEFIN Nacional pode rejeitar com E4007.');
        } else {
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Arquivo cert.pem contém ' . $certCount . ' certificados (end-entity + cadeia)');
        }

        // Validar que certificado e chave formam um par válido
        $pairValid = openssl_x509_check_private_key($certs['cert'], $keyPem);
        if ($pairValid === false) {
            log_message('error', 'NFS-e Nacional [ALERTA]: Certificado e chave privada NÃO formam um par válido segundo openssl_x509_check_private_key');
        } else {
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Par certificado/chave validado com sucesso');
        }

        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Certificado PEM extraído com sucesso. CNPJ: ' . $certificado->cnpj . ' | Cert=' . $certPemPath . ' | Key=' . $keyPemPath);

        return [
            'cert' => $certPemPath,
            'key' => $keyPemPath,
            'senha' => $senha,
            'cnpj' => preg_replace('/\D/', '', $certificado->cnpj),
            'razao_social' => $certificado->razao_social ?? '',
            'ambiente' => $certificado->ambiente ?? 'homologacao',
            'validade' => $certInfo ? date('Y-m-d H:i:s', $certInfo['validTo_time_t']) : null,
        ];
    }

    /**
     * Limpa arquivos PEM temporários após uso
     *
     * @param array $pemPaths Array retornado por extrairCertificadoPem
     */
    public function limparPemTemporarios($pemPaths)
    {
        if (!$pemPaths) return;

        if (!empty($pemPaths['cert']) && file_exists($pemPaths['cert'])) {
            @unlink($pemPaths['cert']);
        }
        if (!empty($pemPaths['key']) && file_exists($pemPaths['key'])) {
            @unlink($pemPaths['key']);
        }
    }

    /**
     * Converte chave privada PKCS#8 para PKCS#1
     * Algumas versões do cURL/OpenSSL têm problemas com PKCS#8
     *
     * @param string $pkcs8Pem Chave privada em formato PKCS#8 PEM
     * @return string|false Chave em PKCS#1 ou false em caso de erro
     */
    private function converterPkcs8ParaPkcs1($pkcs8Pem)
    {
        $privKey = openssl_pkey_get_private($pkcs8Pem);
        if (!$privKey) {
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Falha ao ler chave PKCS#8 para conversão');
            return false;
        }

        $success = openssl_pkey_export($privKey, $pkcs1Key);
        if (!$success || empty($pkcs1Key)) {
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Falha ao exportar chave para PKCS#1');
            return false;
        }

        return $pkcs1Key;
    }
}
