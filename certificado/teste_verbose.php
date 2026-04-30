<?php
/**
 * Teste com cURL VERBOSE para ver handshake TLS detalhado
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== TESTE VERBOSE mTLS ===\n\n";

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

$url = 'https://sefin.producaorestrita.nfse.gov.br/API/SefinNacional/nfse';

// Teste 1: GET com verbose
echo "=== TESTE 1: GET com VERBOSE ===\n";
$verboseFile = $tempDir . 'curl_verbose.log';
$fp = fopen($verboseFile, 'w');

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
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $fp);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
fclose($fp);

echo "HTTP Code: $httpCode\n";
echo "cURL Verbose Log:\n";
echo file_get_contents($verboseFile);
echo "\n\n";

// Teste 2: POST com verbose
echo "=== TESTE 2: POST com VERBOSE ===\n";
$verboseFile2 = $tempDir . 'curl_verbose_post.log';
$fp2 = fopen($verboseFile2, 'w');

$payload = json_encode([
    'cpfCnpj' => '54518217000117',
    'dps' => ['xml' => base64_encode(gzencode('<teste/>'))],
]);

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
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $fp2);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
fclose($fp2);

echo "HTTP Code: $httpCode2\n";
echo "cURL Verbose Log:\n";
echo file_get_contents($verboseFile2);
echo "\n\n";

// Limpar
@unlink($certPath);
@unlink($keyPath);
@unlink($verboseFile);
@unlink($verboseFile2);

echo "=== FIM ===\n";
