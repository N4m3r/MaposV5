<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

    /**
     * @param array $config Configuração:
     *   - ambiente: 'homologacao' ou 'producao'
     *   - cert_pem: caminho do certificado PEM
     *   - key_pem: caminho da chave privada PEM
     *   - ca_path: caminho do CA chain ICP-Brasil
     *   - cnpj: CNPJ do prestador (somente números)
     *   - timeout: timeout em segundos (padrão 60)
     */
    public function __construct(array $config)
    {
        $this->ambiente = $config['ambiente'] ?? 'homologacao';
        $this->baseUrl = NfseConfig::getBaseUrl($this->ambiente);
        $this->certPemPath = $config['cert_pem'] ?? '';
        $this->keyPemPath = $config['key_pem'] ?? '';
        $this->caPath = $config['ca_path'] ?? FCPATH . 'assets/certs/ac-icp-brasil.pem';
        $this->cnpj = preg_replace('/\D/', '', $config['cnpj'] ?? '');
        $this->timeout = $config['timeout'] ?? 60;
    }

    /**
     * Emite NFS-e via API Nacional
     *
     * @param string $xmlDps XML DPS já assinado
     * @return array ['success' => bool, 'data' => ..., 'message' => ..., 'chave_acesso' => ...]
     */
    public function emitir($xmlDps)
    {
        // Comprimir DPS com GZip e codificar em Base64
        $xmlGzipped = gzencode($xmlDps, 9);
        if ($xmlGzipped === false) {
            return [
                'success' => false,
                'message' => 'Erro ao comprimir XML DPS com GZip',
            ];
        }
        $xmlBase64 = base64_encode($xmlGzipped);

        // Montar payload JSON conforme especificação da API Nacional
        $payload = [
            'cpfCnpj' => $this->cnpj,
            'dps' => [
                'xml' => $xmlBase64,
            ],
        ];

        $url = $this->baseUrl . 'nfse';

        $response = $this->sendRequest('POST', $url, $payload);

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
        if (isset($response['body'])) {
            $body = $response['body'];
            if (is_string($body)) {
                $errorMsg .= ': ' . $body;
            } elseif (isset($body['mensagem'])) {
                $errorMsg .= ': ' . $body['mensagem'];
            } elseif (isset($body['message'])) {
                $errorMsg .= ': ' . $body['message'];
            } elseif (isset($body['erros'])) {
                $erros = is_array($body['erros']) ? implode('; ', $body['erros']) : $body['erros'];
                $errorMsg .= ': ' . $erros;
            }
        }

        return [
            'success' => false,
            'message' => $errorMsg,
            'httpCode' => $response['httpCode'] ?? 0,
            'body' => $response['body'] ?? null,
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
            'cpfCnpj' => $this->cnpj,
            'tipoEvento' => 'Cancelamento',
            'motivo' => substr($motivo, 0, 255),
            'xmlEvento' => $eventoBase64,
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

        $ch = curl_init();

        // Configuração mTLS
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_SSLCERT, $this->certPemPath);
        curl_setopt($ch, CURLOPT_SSLKEY, $this->keyPemPath);

        // CA chain para verificação do servidor
        if (file_exists($this->caPath)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->caPath);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        } else {
            // Em homologação, pode ser necessário desabilitar verificação
            if ($this->ambiente === 'homologacao') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                log_message('info', 'NFS-e Nacional: Verificação SSL desabilitada (homologação)');
            }
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // Headers
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Payload
        if ($data !== null && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // Log da requisição
        log_message('info', 'NFS-e Nacional: ' . $method . ' ' . $url);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);

        curl_close($ch);

        // Log de erro cURL
        if ($curlErrno) {
            log_message('error', 'NFS-e Nacional: cURL Error ' . $curlErrno . ': ' . $curlError);
            return [
                'httpCode' => 0,
                'body' => ['mensagem' => 'Erro de conexão: ' . $curlError . ' (cURL ' . $curlErrno . ')'],
                'curlError' => $curlError,
                'curlErrno' => $curlErrno,
            ];
        }

        // Decodificar resposta
        $decoded = null;
        if (!empty($response)) {
            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Resposta não é JSON — pode ser XML da NFS-e
                $decoded = $response;
            }
        }

        log_message('info', 'NFS-e Nacional: Resposta HTTP ' . $httpCode);

        return [
            'httpCode' => $httpCode,
            'body' => $decoded,
        ];
    }
}