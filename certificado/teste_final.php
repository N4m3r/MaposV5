<?php
/**
 * Teste final com URL correto
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== TESTE FINAL COM URL CORRETO ===\n\n";

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
$certPath = $tempDir . 'test_cert_final.pem';
$keyPath = $tempDir . 'test_key_final.pem';
file_put_contents($certPath, $certPem);
if (!empty($certs['extracerts'])) {
    foreach ($certs['extracerts'] as $extra) {
        file_put_contents($certPath, "\n" . $extra, FILE_APPEND);
    }
}
file_put_contents($keyPath, $keyPem);

$url = 'https://sefin.producaorestrita.nfse.gov.br/SefinNacional/nfse';
$payload = json_encode([
    'cpfCnpj' => '54518217000117',
    'dps' => ['xml' => base64_encode(gzencode('<?xml version="1.0" encoding="UTF-8"?><DPS/>'))],
]);

echo "URL: $url\n";
echo "Payload (primeiros 200 chars): " . substr($payload, 0, 200) . "\n\n";

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
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrno = curl_errno($ch);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "cURL Errno: $curlErrno\n";
echo "cURL Error: $curlError\n";
echo "Resposta:\n$response\n\n";

$decoded = json_decode($response, true);
if (isset($decoded['erros']) && is_array($decoded['erros'])) {
    foreach ($decoded['erros'] as $erro) {
        $cod = $erro['Codigo'] ?? $erro['codigo'] ?? 'N/A';
        $desc = $erro['Descricao'] ?? $erro['descricao'] ?? 'N/A';
        echo "Erro: $cod - $desc\n";
    }
}

// Limpar
@unlink($certPath);
@unlink($keyPath);

echo "\n=== FIM ===\n";
