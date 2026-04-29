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
        // Criptografar senha
        $dados['senha'] = $this->criptografarSenha($dados['senha']);

        // Se tem arquivo (A1), processar
        if ($arquivo && $arquivo['tmp_name']) {
            $hash = hash_file('sha256', $arquivo['tmp_name']);
            $nomeArquivo = $dados['cnpj'] . '_' . date('YmdHis') . '.pfx';
            $caminhoCompleto = $this->certPath . $nomeArquivo;

            // Mover arquivo
            if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
                // Verificar integridade
                if (!$this->verificarCertificado($caminhoCompleto, $this->descriptografarSenha($dados['senha']))) {
                    unlink($caminhoCompleto);
                    return ['error' => 'Certificado inválido ou senha incorreta'];
                }

                $dados['arquivo_caminho'] = $caminhoCompleto;
                $dados['arquivo_hash'] = $hash;
                $dados['tipo'] = 'A1';

                // Extrair dados do certificado
                $infoCert = $this->extrairInfoCertificado($caminhoCompleto, $this->descriptografarSenha($dados['senha']));
                if ($infoCert) {
                    $dados['data_validade'] = $infoCert['validade'];
                    $dados['data_emissao'] = $infoCert['emissao'];
                    $dados['emissor'] = $infoCert['emissor'];
                    $dados['serial_number'] = $infoCert['serial'];
                }
            } else {
                return ['error' => 'Erro ao salvar arquivo do certificado'];
            }
        } else {
            // A3 - Configuração manual
            $dados['tipo'] = 'A3';
        }

        // Desativar certificados anteriores
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
     */
    private function extrairInfoCertificado($caminho, $senha)
    {
        try {
            $pfx = file_get_contents($caminho);
            $certs = [];

            if (!openssl_pkcs12_read($pfx, $certs, $senha)) {
                return false;
            }

            $cert = openssl_x509_parse($certs['cert']);

            return [
                'validade' => date('Y-m-d H:i:s', $cert['validTo_time_t']),
                'emissao' => date('Y-m-d H:i:s', $cert['validFrom_time_t']),
                'emissor' => $cert['issuer']['O'] ?? 'Desconhecido',
                'serial' => $cert['serialNumber'],
                'dono' => $cert['subject']['CN'] ?? '',
            ];
        } catch (Exception $e) {
            return false;
        }
    }

    // ==================== CONSULTAS À RECEITA FEDERAL ====================

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
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $caPath = FCPATH . 'assets/certs/ac-icp-brasil.pem';
            if (!empty($caPath) && file_exists($caPath) && filesize($caPath) > 0) {
                curl_setopt($ch, CURLOPT_CAINFO, $caPath);
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200 && !empty($response)) {
                $dados = json_decode($response, true);

                if (!$dados || (isset($dados['status']) && $dados['status'] == 'error')) {
                    return ['error' => $dados['message'] ?? 'CNPJ não encontrado'];
                }

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
            }

            if ($httpCode == 429) {
                return ['error' => 'Limite de requisições atingido. Aguarde 1 minuto e tente novamente.'];
            }

            return ['error' => 'CNPJ não encontrado ou serviço indisponível (HTTP ' . $httpCode . ')'];

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
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $caPath = FCPATH . 'assets/certs/ac-icp-brasil.pem';
            if (!empty($caPath) && file_exists($caPath) && filesize($caPath) > 0) {
                curl_setopt($ch, CURLOPT_CAINFO, $caPath);
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

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
     * Descriptografa senha
     */
    private function descriptografarSenha($senhaCriptografada)
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

        // Verificar validade do certificado
        $certInfo = openssl_x509_parse($certs['cert']);
        if ($certInfo) {
            $validade = $certInfo['validTo_time_t'];
            if ($validade < time()) {
                @unlink($certPemPath);
                @unlink($keyPemPath);
                log_message('error', 'NFS-e Nacional: Certificado expirado em ' . date('Y-m-d H:i:s', $validade));
                return ['error' => 'Certificado digital expirado em ' . date('d/m/Y', $validade)];
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
        }

        log_message('info', 'NFS-e Nacional: Certificado PEM extraído com sucesso. CNPJ: ' . $certificado->cnpj);

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
}
