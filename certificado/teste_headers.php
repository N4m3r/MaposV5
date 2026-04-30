<?php
/**
 * Teste para verificar se há header específico ou endpoint alternativo
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== TESTE HEADERS E ENDPOINTS ===\n\n";

$pfxPath = __DIR__ . '/JJ TECNOLOGIAS LTDA 2025.pfx';
$senha = trim(file_get_contents(__DIR__ . '/senha.txt'));

$pfxContent = file_get_contents($pfxPath);
$certs = [];
openssl_pkcs12_read($pfxContent, $certs, $senha);

$certPem = $certs['cert'];
$keyPem = $certs['pkey'];

if (strpos($keyPem, '-----BEGIN PRIVATE KEY-----') !== false) {
    $privKey = openssl_pkey_get_private($keyPem);
    if ($privKey) {
        openssl_pkey_export($privKey, $pkcs1Key);
        if (!empty($pkcs1Key)) $keyPem = $pkcs1Key;
    }
}

$tempDir = sys_get_temp_dir() . '/';
$certPath = $tempDir . 'test_cert.pem';
$keyPath = $tempDir . 'test_key.pem';
file_put_contents($certPath, $certPem);
if (!empty($certs['extracerts'])) {
    foreach ($certs['extracerts'] as $extra) {
        file_put_contents($certPath, "\n" . $extra, FILE_APPEND);
    }
}
file_put_contents($keyPath, $keyPem);

$urls = [
    'https://sefin.producaorestrita.nfse.gov.br/API/SefinNacional/nfse',
    'https://sefin.producaorestrita.nfse.gov.br/API/SefinNacional/',
    'https://sefin.producaorestrita.nfse.gov.br/SefinNacional/nfse',
];

$headersOptions = [
    ['Content-Type: application/json'],
    ['Content-Type: application/json', 'Accept: application/json'],
    ['Content-Type: application/json', 'Accept: application/json', 'X-Ambiente: homologacao'],
    ['Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer teste'],
];

$payload = json_encode([
    'cpfCnpj' => '54518217000117',
    'dps' => ['xml' => base64_encode(gzencode('<teste/>'))],
]);

foreach ($urls as $i => $url) {
    foreach ($headersOptions as $j => $headers) {
        echo "--- Teste URL[$i] + Headers[$j] ---\n";
        echo "URL: $url\n";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        echo "HTTP: $httpCode | Errno: $curlErrno\n";
        echo "Resposta: " . substr($response, 0, 300) . "\n\n";
    }
}

// Limpar
@unlink($certPath);
@unlink($keyPath);

echo "=== FIM ===\n";
