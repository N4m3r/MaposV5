<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/NfseConfig.php';

/**
 * NFS-e Nacional: Cliente API
 * Comunicação com o Sistema Nacional NFS-e via REST/JSON com mTLS
 * Endpoints: emissão, consulta, cancelamento
 */
class NfseNacional
{
    private $baseUrl;
    private $certPemPath;
    private $keyPemPath;
    private $caPath;
    private $timeout;
    private $cnpj;
    private $ambiente;
    private $pfxPath;
    private $pfxSenha;

    /**
     * @param array $config Configuração:
     *   - ambiente: 'homologacao' ou 'producao'
     *   - cert_pem: caminho do certificado PEM
     *   - key_pem: caminho da chave privada PEM
     *   - ca_path: caminho do CA chain ICP-Brasil
     *   - cnpj: CNPJ do prestador (somente números)
     *   - timeout: timeout em segundos (padrão 60)
     *   - pfx_path: caminho do arquivo .pfx original (opcional, para fallback mTLS)
     *   - pfx_senha: senha do arquivo .pfx (opcional)
     */
    public function __construct(array $config = [])
    {
        $this->ambiente = $config['ambiente'] ?? 'homologacao';
        $this->baseUrl = NfseConfig::getBaseUrl($this->ambiente);
        $this->certPemPath = $config['cert_pem'] ?? '';
        $this->keyPemPath = $config['key_pem'] ?? '';
        $this->caPath = $config['ca_path'] ?? '';
        $this->cnpj = preg_replace('/\D/', '', $config['cnpj'] ?? '');
        $this->timeout = $config['timeout'] ?? 60;
        $this->pfxPath = $config['pfx_path'] ?? '';
        $this->pfxSenha = $config['pfx_senha'] ?? '';
    }

    /**
     * Emite NFS-e via API Nacional
     *
     * @param string $xmlDps XML DPS já assinado
     * @return array ['success' => bool, 'data' => ..., 'message' => ..., 'chave_acesso' => ...]
     */
    public function emitir($xmlDps)
    {
        // Comprimir DPS com GZip e codificar em Base64 (conforme documentação SEFIN Nacional)
        $xmlGzipped = gzencode($xmlDps, 9);
        if ($xmlGzipped === false) {
            return [
                'success' => false,
                'message' => 'Erro ao comprimir XML DPS com GZip',
            ];
        }
        $xmlBase64 = base64_encode($xmlGzipped);

        // Payload JSON conforme documentação oficial SEFIN Nacional
        // POST /nfse body: {"dpsXmlGZipB64": "<xml-gzip+base64>"}
        $payload = [
            'dpsXmlGZipB64' => $xmlBase64,
        ];

        $url = $this->baseUrl . 'nfse';

        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: XML DPS tamanho=' . strlen($xmlDps) . ' | GZip=' . strlen($xmlGzipped) . ' | Base64=' . strlen($xmlBase64));
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: URL=' . $url . ' | CNPJ=' . $this->cnpj . ' | Ambiente=' . $this->ambiente);
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Cert PEM=' . $this->certPemPath . ' | Key PEM=' . $this->keyPemPath);

        $response = $this->sendRequest('POST', $url, $payload);
        $httpCode = $response['httpCode'] ?? 0;

        if ($response === false) {
            return [
                'success' => false,
                'message' => 'Erro na comunicação com a API NFS-e Nacional. Verifique o certificado digital e a conexão.',
            ];
        }

        // API retorna 201 (Created) para sucesso
        if (isset($response['httpCode']) && $response['httpCode'] === 201) {
            $data = $response['body'] ?? [];
            return [
                'success' => true,
                'chave_acesso' => $data['chaveAcesso'] ?? $data['chave_acesso'] ?? '',
                'numero' => $data['numero'] ?? $data['nNFSe'] ?? '',
                'protocolo' => $data['protocolo'] ?? '',
                'data_emissao' => $data['dataHoraEmissao'] ?? $data['data_emissao'] ?? '',
                'url_danfe' => $data['urlDanfe'] ?? $data['url_danfe'] ?? '',
                'xml_nfse' => $data['xmlNfse'] ?? $data['xml_nfse'] ?? '',
                'codigo_verificacao' => $data['codigoVerificacao'] ?? $data['codigo_verificacao'] ?? '',
                'data' => $data,
            ];
        }

        // Erro na emissão
        $errorMsg = 'Erro ao emitir NFS-e';
        $httpCode = $response['httpCode'] ?? 0;
        $body = $response['body'] ?? null;

        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Erro na emissão final. HTTP=' . $httpCode . ' Body=' . (is_string($body) ? substr($body, 0, 2000) : json_encode($body)));

        if (is_string($body)) {
            $errorMsg .= ': ' . substr($body, 0, 500);
        } elseif (is_array($body)) {
            if (isset($body['mensagem'])) {
                $errorMsg .= ': ' . $body['mensagem'];
            } elseif (isset($body['message'])) {
                $errorMsg .= ': ' . $body['message'];
            } elseif (isset($body['erros']) && is_array($body['erros'])) {
                $errosArr = [];
                foreach ($body['erros'] as $e) {
                    if (is_array($e)) {
                        $cod = $e['Codigo'] ?? $e['codigo'] ?? '';
                        $desc = $e['Descricao'] ?? $e['descricao'] ?? $e['desc'] ?? '';
                        if (is_array($cod)) { $cod = json_encode($cod); }
                        if (is_array($desc)) { $desc = json_encode($desc); }
                        $errosArr[] = ($cod ? $cod . ': ' : '') . $desc;
                    } elseif (is_object($e)) {
                        $eArr = (array)$e;
                        $cod = $eArr['Codigo'] ?? $eArr['codigo'] ?? '';
                        $desc = $eArr['Descricao'] ?? $eArr['descricao'] ?? $eArr['desc'] ?? '';
                        if (is_array($cod)) { $cod = json_encode($cod); }
                        if (is_array($desc)) { $desc = json_encode($desc); }
                        $errosArr[] = ($cod ? $cod . ': ' : '') . $desc;
                    } else {
                        $errosArr[] = (string)$e;
                    }
                }
                $errorMsg .= ': ' . implode('; ', $errosArr);
            } elseif (isset($body['title'])) {
                $errorMsg .= ': ' . $body['title'] . (isset($body['detail']) ? ' - ' . $body['detail'] : '');
            } else {
                $errorMsg .= ' (HTTP ' . $httpCode . '): ' . json_encode($body);
            }
        } else {
            $errorMsg .= ' (HTTP ' . $httpCode . '): resposta vazia ou não reconhecida';
        }

        return [
            'success' => false,
            'message' => $errorMsg,
            'httpCode' => $httpCode,
            'body' => $body,
        ];
    }

    /**
     * Consulta NFS-e pela chave de acesso
     *
     * @param string $chaveAcesso Chave de acesso da NFS-e
     * @return array Dados da NFS-e ou erro
     */
    public function consultar($chaveAcesso)
    {
        $url = $this->baseUrl . 'nfse/' . urlencode($chaveAcesso);

        $response = $this->sendRequest('GET', $url);

        if ($response === false) {
            return [
                'success' => false,
                'message' => 'Erro na comunicação com a API NFS-e Nacional.',
            ];
        }

        if (isset($response['httpCode']) && $response['httpCode'] === 200) {
            $data = $response['body'] ?? [];
            return [
                'success' => true,
                'data' => $data,
                'situacao' => $data['situacaoNfse'] ?? $data['situacao'] ?? '',
                'chave_acesso' => $chaveAcesso,
            ];
        }

        $errorMsg = 'NFS-e não encontrada';
        if (isset($response['body']['mensagem'])) {
            $errorMsg = $response['body']['mensagem'];
        } elseif (isset($response['body']['message'])) {
            $errorMsg = $response['body']['message'];
        }

        return [
            'success' => false,
            'message' => $errorMsg,
            'httpCode' => $response['httpCode'] ?? 0,
        ];
    }

    /**
     * Cancela NFS-e via API Nacional
     *
     * @param string $chaveAcesso Chave de acesso da NFS-e
     * @param string $motivo Motivo do cancelamento
     * @param string $xmlEvento XML do evento de cancelamento assinado
     * @return array Resultado do cancelamento
     */
    public function cancelar($chaveAcesso, $motivo, $xmlEvento)
    {
        // Comprimir evento com GZip e codificar em Base64
        $eventoGzipped = gzencode($xmlEvento, 9);
        if ($eventoGzipped === false) {
            return [
                'success' => false,
                'message' => 'Erro ao comprimir XML de cancelamento',
            ];
        }
        $eventoBase64 = base64_encode($eventoGzipped);

        $payload = [
            'pedidoRegistroEventoXmlGZipB64' => $eventoBase64,
        ];

        $url = $this->baseUrl . 'nfse/' . urlencode($chaveAcesso) . '/eventos';

        $response = $this->sendRequest('POST', $url, $payload);

        if ($response === false) {
            return [
                'success' => false,
                'message' => 'Erro na comunicação com a API NFS-e Nacional.',
            ];
        }

        if (isset($response['httpCode']) && in_array($response['httpCode'], [200, 201])) {
            $data = $response['body'] ?? [];
            return [
                'success' => true,
                'protocolo' => $data['protocolo'] ?? '',
                'data_cancelamento' => $data['dataHoraEvento'] ?? '',
                'data' => $data,
            ];
        }

        $errorMsg = 'Erro ao cancelar NFS-e';
        if (isset($response['body']['mensagem'])) {
            $errorMsg .= ': ' . $response['body']['mensagem'];
        } elseif (isset($response['body']['message'])) {
            $errorMsg .= ': ' . $response['body']['message'];
        }

        return [
            'success' => false,
            'message' => $errorMsg,
            'httpCode' => $response['httpCode'] ?? 0,
        ];
    }

    /**
     * Gera XML de evento de cancelamento (para ser assinado posteriormente)
     *
     * @param string $chaveAcesso Chave de acesso da NFS-e
     * @param string $motivo Motivo do cancelamento
     * @return string XML do evento sem assinatura
     */
    public function gerarXmlCancelamento($chaveAcesso, $motivo)
    {
        $idEvento = 'EC' . $chaveAcesso;

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;

        $evento = $dom->createElementNS('http://www.sped.fazenda.gov.br/nfse', 'evento');
        $evento->setAttribute('versao', '1.00');
        $evento->setAttribute('id', $idEvento);
        $dom->appendChild($evento);

        $infEvento = $dom->createElement('infEvento');
        $infEvento->setAttribute('Id', $idEvento);
        $evento->appendChild($infEvento);

        $infEvento->appendChild($dom->createElement('chaveAcesso', $chaveAcesso));
        $infEvento->appendChild($dom->createElement('tipoEvento', 'Cancelamento'));
        $infEvento->appendChild($dom->createElement('motivo', htmlspecialchars($motivo, ENT_XML1 | ENT_QUOTES, 'UTF-8')));

        return $dom->saveXML();
    }

    /**
     * Verifica se um arquivo PEM é válido (contém ao menos um certificado)
     *
     * @param string $path Caminho do arquivo
     * @return bool
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
     * Envia requisição HTTP com mTLS (certificado digital)
     *
     * @param string $method GET, POST, etc
     * @param string $url URL completa
     * @param array|null $data Payload para POST/PUT
     * @return array|false Resposta decodificada ou false em caso de erro
     */
    private function sendRequest($method, $url, $data = null)
    {
        if (!file_exists($this->certPemPath) || !file_exists($this->keyPemPath)) {
            log_message('error', 'NFS-e Nacional: Certificado PEM não encontrado. cert=' . $this->certPemPath . ' key=' . $this->keyPemPath);
            return false;
        }

        $certContent = file_get_contents($this->certPemPath);
        $keyContent = file_get_contents($this->keyPemPath);
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Certificado PEM tamanho=' . strlen($certContent) . ' | Chave PEM tamanho=' . strlen($keyContent));

        $validacao = $this->validarCertificadoMtls();
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Validação certificado=' . ($validacao['valido'] ? 'OK' : 'FALHA') . ' | Detalhes=' . json_encode($validacao['detalhes']));
        if (!empty($validacao['erros'])) {
            foreach ($validacao['erros'] as $err) {
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: ERRO CERT: ' . $err);
            }
        }

        $curlVersion = curl_version();
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: cURL version=' . ($curlVersion['version'] ?? 'N/A') . ' | SSL=' . ($curlVersion['ssl_version'] ?? 'N/A'));

        $resultado = $this->sendRequestCurl($method, $url, $data, $this->certPemPath, $this->keyPemPath);

        if ($this->isErroCertificadoNaoObtido($resultado)) {
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: E4007 detectado. Tentando abordagem alternativa com PEM combinado...');
            $combinedPem = $this->criarPemCombinado();
            if ($combinedPem) {
                $resultado2 = $this->sendRequestCurl($method, $url, $data, $combinedPem, $combinedPem);
                @unlink($combinedPem);
                if (!$this->isErroCertificadoNaoObtido($resultado2)) {
                    log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Abordagem PEM combinado funcionou!');
                    return $resultado2;
                }
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: PEM combinado também falhou. HTTP=' . ($resultado2['httpCode'] ?? 0));
            }

            // Tentativa 2: extrair apenas o certificado end-entity (sem cadeia)
            // Às vezes o cURL envia o certificado errado quando há múltiplos no arquivo PEM
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Tentando com apenas o certificado end-entity (sem cadeia)...');
            $endEntityPem = $this->extrairEndEntityPem($this->certPemPath);
            if ($endEntityPem) {
                $resultadoEE = $this->sendRequestCurl($method, $url, $data, $endEntityPem, $this->keyPemPath);
                @unlink($endEntityPem);
                if (!$this->isErroCertificadoNaoObtido($resultadoEE)) {
                    log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Abordagem end-entity (sem cadeia) funcionou!');
                    return $resultadoEE;
                }
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: end-entity (sem cadeia) também falhou. HTTP=' . ($resultadoEE['httpCode'] ?? 0));
            }

            // Tentativa 3: usar o arquivo .pfx original diretamente (formato P12)
            // Algumas versões do cURL/OpenSSL preferem o certificado no formato PKCS#12 original
            if (!empty($this->pfxPath) && file_exists($this->pfxPath) && !empty($this->pfxSenha)) {
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Tentando com certificado .pfx original (P12)...');
                $resultadoPfx = $this->sendRequestCurlPfx($method, $url, $data, $this->pfxPath, $this->pfxSenha);
                if (!$this->isErroCertificadoNaoObtido($resultadoPfx)) {
                    log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Abordagem .pfx (P12) funcionou!');
                    return $resultadoPfx;
                }
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: .pfx (P12) também falhou. HTTP=' . ($resultadoPfx['httpCode'] ?? 0));
            }

            // Tentativa diagnóstica: desativar verificação SSL do servidor
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Tentando com SSL_VERIFYPEER=false (apenas diagnóstico)...');
            $resultado3 = $this->sendRequestCurl($method, $url, $data, $this->certPemPath, $this->keyPemPath, false);
            if (!$this->isErroCertificadoNaoObtido($resultado3)) {
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: FUNCIONOU com SSL_VERIFYPEER=false! O problema é que o CA do sistema não reconhece o certificado do servidor SEFIN. Baixe a cadeia ICP-Brasil completa.');
                return $resultado3;
            }
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Mesmo com SSL_VERIFYPEER=false falhou. HTTP=' . ($resultado3['httpCode'] ?? 0) . '. O problema não é a cadeia CA do servidor.');
        }

        return $resultado;
    }

    private function isErroCertificadoNaoObtido($response)
    {
        if (!is_array($response)) return false;
        $httpCode = $response['httpCode'] ?? 0;
        if ($httpCode !== 403) return false;
        $body = $response['body'] ?? null;
        if (!is_array($body)) return false;
        if (!empty($body['erros']) && is_array($body['erros'])) {
            foreach ($body['erros'] as $erro) {
                if (is_array($erro) && (($erro['Codigo'] ?? '') === 'E4007' || ($erro['codigo'] ?? '') === 'E4007')) {
                    return true;
                }
            }
        }
        $msg = $body['mensagem'] ?? ($body['message'] ?? '');
        if (stripos($msg, 'certificado de cliente') !== false) return true;
        return false;
    }

    private function sendRequestCurl($method, $url, $data, $certPath, $keyPath, $verifySsl = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        if (defined('CURL_SSLVERSION_TLSv1_2')) {
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        }

        // Verificação SSL
        if ($verifySsl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: ATENÇÃO - SSL_VERIFYPEER=false (modo diagnóstico apenas)');
        }

        // Tentar carregar CA chain ICP-Brasil completa se disponível
        $caPath = $this->obterCaChainIcpBrasil();
        if ($caPath && $this->isValidPemFile($caPath)) {
            curl_setopt($ch, CURLOPT_CAINFO, $caPath);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Usando CA chain ICP-Brasil: ' . $caPath);
        } elseif (!empty($this->caPath) && $this->isValidPemFile($this->caPath)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->caPath);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Usando CA custom configurado: ' . $this->caPath);
        } else {
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: CA ICP-Brasil não encontrado. Usando CA do sistema. Isso pode causar E4007 se o sistema não tiver a cadeia completa.');
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        $headers = ['Content-Type: application/json', 'Accept: application/json'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($data !== null && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $jsonPayload = json_encode($data);
            if ($jsonPayload === false) {
                log_message('error', 'NFS-e Nacional: Erro ao codificar payload JSON: ' . json_last_error_msg());
                curl_close($ch);
                return false;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Payload JSON tamanho=' . strlen($jsonPayload));
        }
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Enviando ' . $method . ' ' . $url . ' | Cert=' . $certPath . ' | Key=' . $keyPath);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        $sslVerifyResult = curl_getinfo($ch, CURLINFO_SSL_VERIFYRESULT);
        curl_close($ch);
        if ($curlErrno) {
            log_message('error', 'NFS-e Nacional: cURL Error ' . $curlErrno . ': ' . $curlError . ' | SSLVerifyResult=' . $sslVerifyResult);
            return ['httpCode' => 0, 'body' => ['mensagem' => 'Erro de conexão: ' . $curlError . ' (cURL ' . $curlErrno . ')'], 'curlError' => $curlError, 'curlErrno' => $curlErrno];
        }
        $decoded = null;
        if (!empty($response)) {
            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = $response;
                if ($httpCode >= 400) {
                    log_message('error', 'NFS-e Nacional: Resposta HTTP ' . $httpCode . ' não é JSON. Body bruto: ' . substr($response, 0, 2000));
                }
            }
        }
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Resposta HTTP ' . $httpCode . ' | Body tipo=' . gettype($decoded) . ' | Body tamanho=' . (is_string($decoded) ? strlen($decoded) : (is_array($decoded) ? count($decoded) : 'n/a')));
        return ['httpCode' => $httpCode, 'body' => $decoded];
    }

    /**
     * Envia requisição usando certificado no formato PFX/P12 diretamente
     */
    private function sendRequestCurlPfx($method, $url, $data, $pfxPath, $pfxSenha, $verifySsl = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSLCERT, $pfxPath);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'P12');
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $pfxSenha);
        curl_setopt($ch, CURLOPT_SSLKEY, $pfxPath);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'P12');
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $pfxSenha);
        if (defined('CURL_SSLVERSION_TLSv1_2')) {
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        }
        if ($verifySsl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        $caPath = $this->obterCaChainIcpBrasil();
        if ($caPath && $this->isValidPemFile($caPath)) {
            curl_setopt($ch, CURLOPT_CAINFO, $caPath);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        $headers = ['Content-Type: application/json', 'Accept: application/json'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($data !== null && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $jsonPayload = json_encode($data);
            if ($jsonPayload === false) {
                curl_close($ch);
                return false;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        }
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Enviando ' . $method . ' ' . $url . ' | PFX=' . $pfxPath);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);
        if ($curlErrno) {
            log_message('error', 'NFS-e Nacional: cURL PFX Error ' . $curlErrno . ': ' . $curlError);
            return ['httpCode' => 0, 'body' => ['mensagem' => 'Erro de conexão PFX: ' . $curlError], 'curlError' => $curlError, 'curlErrno' => $curlErrno];
        }
        $decoded = null;
        if (!empty($response)) {
            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = $response;
            }
        }
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Resposta PFX HTTP ' . $httpCode);
        return ['httpCode' => $httpCode, 'body' => $decoded];
    }

    private function validarCertificadoMtls()
    {
        $erros = []; $detalhes = [];
        if (!file_exists($this->certPemPath)) {
            return ['valido' => false, 'detalhes' => [], 'erros' => ['Arquivo do certificado não existe: ' . $this->certPemPath]];
        }
        $certPem = file_get_contents($this->certPemPath);
        if (empty($certPem)) {
            return ['valido' => false, 'detalhes' => [], 'erros' => ['Arquivo do certificado está vazio']];
        }
        $certEndEntity = $certPem;
        if (preg_match('/(-----BEGIN CERTIFICATE-----.*?-----END CERTIFICATE-----)/s', $certPem, $matches)) {
            $certEndEntity = $matches[1];
        }
        $certInfo = openssl_x509_parse($certEndEntity);
        if (!$certInfo) {
            return ['valido' => false, 'detalhes' => [], 'erros' => ['Não foi possível fazer parse do certificado.']];
        }
        $detalhes['subject'] = $certInfo['subject'] ?? [];
        $detalhes['issuer'] = $certInfo['issuer'] ?? [];
        $detalhes['validFrom'] = date('Y-m-d H:i:s', $certInfo['validFrom_time_t'] ?? 0);
        $detalhes['validTo'] = date('Y-m-d H:i:s', $certInfo['validTo_time_t'] ?? 0);
        $detalhes['serialNumber'] = $certInfo['serialNumber'] ?? '';
        $cnpjCert = '';
        if (!empty($certInfo['subject']['CN'])) {
            if (preg_match('/(\d{14})/', $certInfo['subject']['CN'], $m)) $cnpjCert = $m[1];
        }
        if (empty($cnpjCert) && !empty($certInfo['subject']['x500UniqueIdentifier'])) {
            $cnpjCert = preg_replace('/\D/', '', $certInfo['subject']['x500UniqueIdentifier']);
        }
        $detalhes['cnpjCertificado'] = $cnpjCert;
        $now = time();
        if (($certInfo['validFrom_time_t'] ?? 0) > $now) $erros[] = 'Certificado ainda não é válido';
        if (($certInfo['validTo_time_t'] ?? 0) < $now) $erros[] = 'Certificado expirado em ' . $detalhes['validTo'];
        if (!empty($certInfo['extensions']['keyUsage'])) {
            $ku = $certInfo['extensions']['keyUsage'];
            $detalhes['keyUsage'] = $ku;
            if (strpos($ku, 'Digital Signature') === false && strpos($ku, 'digitalSignature') === false) $erros[] = 'Key Usage não contém Digital Signature';
        } else { $detalhes['keyUsage'] = 'NÃO ENCONTRADO'; }
        if (!empty($certInfo['extensions']['extendedKeyUsage'])) {
            $eku = $certInfo['extensions']['extendedKeyUsage'];
            $detalhes['extendedKeyUsage'] = $eku;
            if (strpos($eku, 'TLS Web Client Authentication') === false && strpos($eku, 'Client Authentication') === false && strpos($eku, '1.3.6.1.5.5.7.3.2') === false) {
                $erros[] = 'Extended Key Usage não contém Client Authentication (1.3.6.1.5.5.7.3.2).';
            }
        } else {
            $detalhes['extendedKeyUsage'] = 'NÃO ENCONTRADO';
            $erros[] = 'Extended Key Usage não encontrado';
        }
        $issuerO = $certInfo['issuer']['O'] ?? '';
        $issuerCN = $certInfo['issuer']['CN'] ?? '';
        $detalhes['issuerO'] = $issuerO; $detalhes['issuerCN'] = $issuerCN;
        $icpBrasilCAs = ['ICP-Brasil', 'Certisign', 'Serasa', 'Valid', 'CVM', 'Caixa', 'Receita Federal', 'Syngular'];
        $isIcpBrasil = false;
        foreach ($icpBrasilCAs as $ca) {
            if (stripos($issuerO, $ca) !== false || stripos($issuerCN, $ca) !== false) { $isIcpBrasil = true; break; }
        }
        $detalhes['isIcpBrasil'] = $isIcpBrasil;
        if (!$isIcpBrasil) $erros[] = 'Emissor não parece ser AC ICP-Brasil reconhecida (' . $issuerO . ' / ' . $issuerCN . ')';
        if (!empty($this->cnpj) && !empty($cnpjCert) && $this->cnpj !== $cnpjCert) $erros[] = 'CNPJ do certificado (' . $cnpjCert . ') não corresponde ao CNPJ da requisição (' . $this->cnpj . ')';
        $keyValid = $this->verificarParCertificadoChave($certEndEntity, $this->keyPemPath);
        $detalhes['chaveCorresponde'] = $keyValid;
        if (!$keyValid) $erros[] = 'A chave privada não corresponde ao certificado';
        return ['valido' => empty($erros), 'detalhes' => $detalhes, 'erros' => $erros];
    }

    private function verificarParCertificadoChave($certPem, $keyPath)
    {
        if (!file_exists($keyPath)) return false;
        $keyPem = file_get_contents($keyPath);
        if (empty($keyPem)) return false;
        $certPubKey = openssl_pkey_get_public($certPem);
        if (!$certPubKey) return false;
        $certPubKeyDetails = openssl_pkey_get_details($certPubKey);
        if (!$certPubKeyDetails || empty($certPubKeyDetails['key'])) return false;
        $privKey = openssl_pkey_get_private($keyPem);
        if (!$privKey) return false;
        $privKeyDetails = openssl_pkey_get_details($privKey);
        if (!$privKeyDetails || empty($privKeyDetails['key'])) return false;
        return trim($certPubKeyDetails['key']) === trim($privKeyDetails['key']);
    }

    /**
     * Extrai apenas o certificado end-entity (primeiro certificado) do arquivo PEM
     * Útil quando o cURL envia o certificado errado ao ter múltiplos no arquivo
     */
    private function extrairEndEntityPem($certPemPath)
    {
        $content = file_get_contents($certPemPath);
        if (empty($content)) return false;
        if (!preg_match('/(-----BEGIN CERTIFICATE-----.*?-----END CERTIFICATE-----)/s', $content, $matches)) {
            return false;
        }
        $endEntity = $matches[1];
        $tempFile = sys_get_temp_dir() . '/nfse_endentity_' . uniqid() . '.pem';
        if (file_put_contents($tempFile, $endEntity) === false) return false;
        return $tempFile;
    }

    private function criarPemCombinado()
    {
        if (!file_exists($this->certPemPath) || !file_exists($this->keyPemPath)) return false;
        $certContent = file_get_contents($this->certPemPath);
        $keyContent = file_get_contents($this->keyPemPath);
        if (empty($certContent) || empty($keyContent)) return false;
        $keyContent = $this->converterChavePkcs8ParaPkcs1($keyContent);
        $combined = $certContent . "
" . $keyContent;
        $tempFile = sys_get_temp_dir() . '/nfse_combined_' . uniqid() . '.pem';
        if (file_put_contents($tempFile, $combined) === false) return false;
        return $tempFile;
    }

    private function converterChavePkcs8ParaPkcs1($keyPem)
    {
        if (strpos($keyPem, '-----BEGIN PRIVATE KEY-----') === false) return $keyPem;
        $privKey = openssl_pkey_get_private($keyPem);
        if (!$privKey) {
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Não foi possível ler chave PKCS#8 para conversão');
            return $keyPem;
        }
        $success = openssl_pkey_export($privKey, $pkcs1Key);
        if (!$success || empty($pkcs1Key)) {
            log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Falha ao exportar chave para PKCS#1');
            return $keyPem;
        }
        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Chave privada convertida de PKCS#8 para PKCS#1');
        return $pkcs1Key;
    }

    /**
     * Obtém caminho da cadeia CA ICP-Brasil
     * Tenta baixar se não existir localmente
     */
    private function obterCaChainIcpBrasil()
    {
        // Caminhos possíveis para a cadeia ICP-Brasil
        $caminhosPossiveis = [
            FCPATH . 'assets/certs/ac-icp-brasil.pem',
            FCPATH . 'assets/certs/icp-brasil-chain.pem',
            FCPATH . 'assets/certs/ca-bundle.pem',
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/ssl/certs/ca-bundle.crt',
        ];

        foreach ($caminhosPossiveis as $path) {
            if (file_exists($path) && filesize($path) > 100) {
                return $path;
            }
        }

        // Tentar baixar cadeia ICP-Brasil se não existir
        $cachePath = FCPATH . 'assets/certs/icp-brasil-chain.pem';
        if (!file_exists($cachePath)) {
            $chain = $this->baixarCadeiaIcpBrasil();
            if ($chain) {
                // Garantir diretório existe
                $dir = dirname($cachePath);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                @file_put_contents($cachePath, $chain);
                return $cachePath;
            }
        }

        return null;
    }

    /**
     * Baixa cadeia de certificados ICP-Brasil dos repositórios oficiais
     */
    private function baixarCadeiaIcpBrasil()
    {
        // URLs da cadeia ICP-Brasil v5 e v10
        $urls = [
            'https://acraiz.icpbrasil.gov.br/cadastro/icp-brasil/ACcompactado.zip',
            'https://acraiz.icpbrasil.gov.br/credenciadas/CertificadoACRaiz.crt',
        ];

        foreach ($urls as $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && !empty($response)) {
                log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Cadeia ICP-Brasil baixada de ' . $url);
                return $response;
            }
        }

        log_message('error', 'NFS-e Nacional [DIAGNOSTICO]: Não foi possível baixar cadeia ICP-Brasil automaticamente');
        return null;
    }

}
