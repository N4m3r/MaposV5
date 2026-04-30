<?php
/**
 * Script de diagnóstico mTLS SEFIN Nacional
 * Testa o certificado real contra o ambiente de homologação
 * Resultado: salva em certificado/diagnostico_mtls.log
 */

// Não requer BASEPATH - script standalone
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== DIAGNÓSTICO mTLS SEFIN NACIONAL ===\n\n";

echo "=== DIAGNÓSTICO mTLS SEFIN NACIONAL ===\n\n";

// Caminhos
$pfxPath = __DIR__ . '/JJ TECNOLOGIAS LTDA 2025.pfx';
$senhaPath = __DIR__ . '/senha.txt';
$logPath = __DIR__ . '/diagnostico_mtls.log';

if (!file_exists($pfxPath)) {
    echo "ERRO: Certificado .pfx não encontrado em: $pfxPath\n";
    exit(1);
}
if (!file_exists($senhaPath)) {
    echo "ERRO: Arquivo de senha não encontrado em: $senhaPath\n";
    exit(1);
}

$senha = trim(file_get_contents($senhaPath));
echo "1. Senha lida (tamanho=" . strlen($senha) . ")\n";

// Extrair certificado
$pfxContent = file_get_contents($pfxPath);
echo "2. Arquivo .pfx lido (tamanho=" . strlen($pfxContent) . ")\n";

$certs = [];
if (!openssl_pkcs12_read($pfxContent, $certs, $senha)) {
    echo "ERRO: Falha ao ler PKCS12 com a senha fornecida\n";
    echo "openssl_error_string(): " . openssl_error_string() . "\n";
    exit(1);
}

echo "3. PKCS12 lido com sucesso\n";
echo "   - Certificado presente: " . (isset($certs['cert']) ? 'SIM' : 'NAO') . "\n";
echo "   - Chave privada presente: " . (isset($certs['pkey']) ? 'SIM' : 'NAO') . "\n";
echo "   - Certificados extras: " . (isset($certs['extracerts']) ? count($certs['extracerts']) : 0) . "\n";

// Parse do certificado
$certInfo = openssl_x509_parse($certs['cert']);
if (!$certInfo) {
    echo "ERRO: Falha ao parsear certificado\n";
    exit(1);
}

echo "\n4. INFORMAÇÕES DO CERTIFICADO:\n";
echo "   Subject CN: " . ($certInfo['subject']['CN'] ?? 'N/A') . "\n";
echo "   Issuer O: " . ($certInfo['issuer']['O'] ?? 'N/A') . "\n";
echo "   Issuer CN: " . ($certInfo['issuer']['CN'] ?? 'N/A') . "\n";
echo "   Serial: " . ($certInfo['serialNumber'] ?? 'N/A') . "\n";
echo "   Validade: " . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']) . " até " . date('Y-m-d H:i:s', $certInfo['validTo_time_t']) . "\n";
echo "   KeyUsage: " . ($certInfo['extensions']['keyUsage'] ?? 'N/A') . "\n";
echo "   ExtendedKeyUsage: " . ($certInfo['extensions']['extendedKeyUsage'] ?? 'N/A') . "\n";

$hasClientAuth = false;
if (!empty($certInfo['extensions']['extendedKeyUsage'])) {
    $eku = $certInfo['extensions']['extendedKeyUsage'];
    if (strpos($eku, 'TLS Web Client Authentication') !== false ||
        strpos($eku, 'Client Authentication') !== false ||
        strpos($eku, '1.3.6.1.5.5.7.3.2') !== false) {
        $hasClientAuth = true;
    }
}
echo "   Possui ClientAuth: " . ($hasClientAuth ? 'SIM' : 'NAO') . "\n";

// Salvar PEM temporários
$tempDir = sys_get_temp_dir() . '/';
$certPemPath = $tempDir . 'diag_cert.pem';
$keyPemPath = $tempDir . 'diag_key.pem';

$keyPem = $certs['pkey'];
if (strpos($keyPem, '-----BEGIN PRIVATE KEY-----') !== false) {
    $privKey = openssl_pkey_get_private($keyPem);
    if ($privKey) {
        openssl_pkey_export($privKey, $pkcs1Key);
        if (!empty($pkcs1Key)) {
            $keyPem = $pkcs1Key;
            echo "   Chave convertida de PKCS#8 para PKCS#1\n";
        }
    }
}

file_put_contents($certPemPath, $certs['cert']);
file_put_contents($keyPemPath, $keyPem);

// Adicionar cadeia se houver
if (!empty($certs['extracerts'])) {
    foreach ($certs['extracerts'] as $extra) {
        file_put_contents($certPemPath, "\n" . $extra, FILE_APPEND);
    }
}

echo "\n5. PEMs salvos:\n";
echo "   Cert: $certPemPath (" . filesize($certPemPath) . " bytes)\n";
echo "   Key:  $keyPemPath (" . filesize($keyPemPath) . " bytes)\n";

// Validar par certificado/chave
$pairValid = openssl_x509_check_private_key($certs['cert'], $keyPem);
echo "   Par válido: " . ($pairValid ? 'SIM' : 'NAO') . "\n";

// TESTE 1: cURL direto
$url = 'https://sefin.producaorestrita.nfse.gov.br/API/SefinNacional/nfse';
echo "\n6. TESTE 1: cURL direto com certificado PEM\n";
echo "   URL: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSLCERT, $certPemPath);
curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
curl_setopt($ch, CURLOPT_SSLKEY, $keyPemPath);
curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrno = curl_errno($ch);
$curlError = curl_error($ch);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";
echo "   cURL Errno: $curlErrno\n";
echo "   cURL Error: $curlError\n";
echo "   Resposta (primeiros 500 chars): " . substr($response, 0, 500) . "\n";

// Se retornou 403 ou E4007, tentar POST
if ($httpCode == 403 || strpos($response, 'E4007') !== false) {
    echo "\n7. TESTE 2: POST com payload mínimo\n";

    $payload = [
        'cpfCnpj' => '54518217000117',
        'dps' => [
            'xml' => base64_encode(gzencode('<teste/>')),
        ],
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSLCERT, $certPemPath);
    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, $keyPemPath);
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response2 = curl_exec($ch);
    $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErrno2 = curl_errno($ch);
    $curlError2 = curl_error($ch);
    curl_close($ch);

    echo "   HTTP Code: $httpCode2\n";
    echo "   cURL Errno: $curlErrno2\n";
    echo "   cURL Error: $curlError2\n";
    echo "   Resposta (primeiros 1000 chars): " . substr($response2, 0, 1000) . "\n";
}

// TESTE 3: cURL com PFX direto
echo "\n8. TESTE 3: cURL com certificado PFX direto\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSLCERT, $pfxPath);
curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'P12');
curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $senha);
curl_setopt($ch, CURLOPT_SSLKEY, $pfxPath);
curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'P12');
curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $senha);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload ?? ['teste' => true]));

$response3 = curl_exec($ch);
$httpCode3 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrno3 = curl_errno($ch);
$curlError3 = curl_error($ch);
curl_close($ch);

echo "   HTTP Code: $httpCode3\n";
echo "   cURL Errno: $curlErrno3\n";
echo "   cURL Error: $curlError3\n";
echo "   Resposta (primeiros 1000 chars): " . substr($response3, 0, 1000) . "\n";

// Salvar log completo
$log = [
    'data' => date('Y-m-d H:i:s'),
    'certificado' => [
        'subject' => $certInfo['subject'] ?? [],
        'issuer' => $certInfo['issuer'] ?? [],
        'serial' => $certInfo['serialNumber'] ?? '',
        'validade' => [
            'de' => date('Y-m-d H:i:s', $certInfo['validFrom_time_t']),
            'ate' => date('Y-m-d H:i:s', $certInfo['validTo_time_t']),
        ],
        'keyUsage' => $certInfo['extensions']['keyUsage'] ?? 'N/A',
        'extendedKeyUsage' => $certInfo['extensions']['extendedKeyUsage'] ?? 'N/A',
        'hasClientAuth' => $hasClientAuth,
        'pairValid' => $pairValid,
    ],
    'teste1_pem_get' => [
        'httpCode' => $httpCode,
        'curlErrno' => $curlErrno,
        'curlError' => $curlError,
        'responsePreview' => substr($response ?? '', 0, 500),
    ],
    'teste2_pem_post' => [
        'httpCode' => $httpCode2 ?? null,
        'curlErrno' => $curlErrno2 ?? null,
        'curlError' => $curlError2 ?? null,
        'responsePreview' => substr($response2 ?? '', 0, 1000),
    ],
    'teste3_pfx_post' => [
        'httpCode' => $httpCode3,
        'curlErrno' => $curlErrno3,
        'curlError' => $curlError3,
        'responsePreview' => substr($response3 ?? '', 0, 1000),
    ],
];

file_put_contents($logPath, json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\n9. Log salvo em: $logPath\n";

// Limpar temporários
@unlink($certPemPath);
@unlink($keyPemPath);

echo "\n=== FIM DO DIAGNÓSTICO ===\n";
