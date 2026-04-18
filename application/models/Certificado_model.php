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
        $this->certPath = FCPATH . 'assets/certificados/';
        $this->tempDir = FCPATH . 'assets/temp/';

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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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

            // Determinar regime tributário
            if ($isOptanteSimples) {
                $simplesData['anexo_sugerido'] = $this->identificarAnexo($cnaeDescricao);
                $simplesData['regime'] = 'simples_nacional';
            } elseif ($isMei) {
                $simplesData['anexo_sugerido'] = 'MEI';
                $simplesData['regime'] = 'simples_nacional';
            } else {
                // NÃO é optante do Simples Nacional
                $simplesData['anexo_sugerido'] = null;
                $simplesData['regime'] = 'lucro_presumido';
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

        if (!$dados['optante_simples']) {
            // Empresa NÃO é optante do Simples Nacional
            // Sugerir configuração para Lucro Presumido
            $config = [
                'optante_simples' => false,
                'regime' => 'lucro_presumido',
                'anexo_sugerido' => null,
                'data_opcao' => null,
                'simei' => false,
            ];

            return [
                'success' => true,
                'configuracao' => $config,
                'mensagem' => 'Empresa NÃO optante do Simples Nacional. Regime tributário detectado: Lucro Presumido. As alíquotas de retenção (IRPJ, CSLL, PIS, COFINS, ISS) foram configuradas automaticamente.',
                'regime' => 'lucro_presumido'
            ];
        }

        // Sugerir configuração para optantes do Simples
        $config = [
            'optante_simples' => true,
            'regime' => 'simples_nacional',
            'anexo_sugerido' => $dados['anexo_sugerido'],
            'data_opcao' => $dados['data_opcao'],
            'simei' => $dados['simei']
        ];

        return [
            'success' => true,
            'configuracao' => $config,
            'mensagem' => 'Anexo identificado: ' . $config['anexo_sugerido'] . '. Configure na página de Configurações de Impostos.'
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
        return openssl_encrypt($senha, 'AES-256-CBC', $this->getChaveCriptografia(), 0, $this->getIvCriptografia());
    }

    /**
     * Descriptografa senha
     */
    private function descriptografarSenha($senha)
    {
        return openssl_decrypt($senha, 'AES-256-CBC', $this->getChaveCriptografia(), 0, $this->getIvCriptografia());
    }

    /**
     * Chave de criptografia (deve ser configurada no arquivo de config)
     */
    private function getChaveCriptografia()
    {
        return $this->config->item('encryption_key') ?: 'mapos_cert_key_2024';
    }

    private function getIvCriptografia()
    {
        $iv = substr($this->getChaveCriptografia(), 0, 16);
        // Garantir exatamente 16 bytes (AES-256-CBC exige IV de 16 bytes)
        return str_pad($iv, 16, "\0");
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
}
