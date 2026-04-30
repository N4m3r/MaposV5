<?php
/**
 * Teste específico: POST com certificado PEM contra SEFIN Nacional
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== TESTE POST mTLS SEFIN NACIONAL ===\n\n";

$pfxPath = __DIR__ . '/JJ TECNOLOGIAS LTDA 2025.pfx';
$senha = trim(file_get_contents(__DIR__ . '/senha.txt'));

$pfxContent = file_get_contents($pfxPath);
$certs = [];
if (!openssl_pkcs12_read($pfxContent, $certs, $senha)) {
    echo "Falha ao ler PFX\n";
    exit(1);
}

// Salvar PEM separado
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

$url = 'https://sefin.producaorestrita.nfse.gov.br/API/SefinNacional/nfse';
$payload = [
    'cpfCnpj' => '54518217000117',
    'dps' => [
        'xml' => base64_encode(gzencode('<?xml version="1.0" encoding="UTF-8"?><DPS/>')),
    ],
];

echo "Testando POST com PEM separado...\n";
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
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrno = curl_errno($ch);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "cURL Errno: $curlErrno\n";
echo "cURL Error: $curlError\n";
echo "Resposta:\n$response\n\n";

// Teste com VERIFYPEER=true
echo "Testando POST com SSL_VERIFYPEER=true...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrno2 = curl_errno($ch);
$curlError2 = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode2\n";
echo "cURL Errno: $curlErrno2\n";
echo "cURL Error: $curlError2\n";
echo "Resposta:\n$response2\n\n";

// Teste com apenas end-entity (sem cadeia)
$certPathEE = $tempDir . 'test_cert_ee.pem';
$certInfo = openssl_x509_parse($certs['cert']);
file_put_contents($certPathEE, $certs['cert']);

echo "Testando POST com apenas end-entity (sem cadeia)...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSLCERT, $certPathEE);
curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response3 = curl_exec($ch);
$httpCode3 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrno3 = curl_errno($ch);
$curlError3 = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode3\n";
echo "cURL Errno: $curlErrno3\n";
echo "cURL Error: $curlError3\n";
echo "Resposta:\n$response3\n\n";

// Limpar
@unlink($certPath);
@unlink($keyPath);
@unlink($certPathEE);

echo "=== FIM ===\n";
