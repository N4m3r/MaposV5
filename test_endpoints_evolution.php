<?php
/**
 * Script de teste completo dos endpoints Evolution GO
 * Execute no servidor: php test_endpoints_evolution.php
 */

// Configuracao
$BASE_URL = 'https://evo.jj-ferreiras.com.br';
$APIKEY = '7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2';
$INSTANCE = 'Mapos';
$INSTANCE_TOKEN = '9e907a2a-1b06-4812-badb-02e5205df9f7';

$RESULTADOS = [];

function testEndpoint($nome, $method, $path, $headers = [], $data = null, $expectedCodes = [200, 201]) {
    global $BASE_URL, $RESULTADOS;

    $url = rtrim($BASE_URL, '/') . $path;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_HEADER, true);

    $defaultHeaders = ['Accept: application/json'];
    if ($headers) {
        $defaultHeaders = array_merge($defaultHeaders, $headers);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $defaultHeaders);

    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($data) ? $data : json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    $body = substr($response, $headerSize);
    $bodyPreview = substr($body, 0, 200);

    $success = in_array($httpCode, $expectedCodes);
    $RESULTADOS[] = [
        'nome' => $nome,
        'url' => $url,
        'method' => $method,
        'http_code' => $httpCode,
        'error' => $error,
        'body_preview' => $bodyPreview,
        'success' => $success
    ];

    $status = $success ? '✓ OK' : '✗ FALHA';
    echo sprintf("%-45s | HTTP %3d | %s\n", $nome, $httpCode, $status);
    if (!$success && $bodyPreview) {
        echo "  -> Body: " . str_replace(["\n", "\r"], ' ', $bodyPreview) . "\n";
    }
}

echo "=========================================\n";
echo "TESTE COMPLETO - ENDPOINTS EVOLUTION GO\n";
echo "URL Base: $BASE_URL\n";
echo "API Key: " . substr($APIKEY, 0, 8) . "...\n";
echo "Instancia: $INSTANCE\n";
echo "=========================================\n\n";

echo "--- ENDPOINTS INSTANCE (com apikey) ---\n";
testEndpoint('GET /instance/all', 'GET', '/instance/all', ['apikey: ' . $APIKEY]);
testEndpoint('GET /instance/get/Mapos', 'GET', '/instance/get/' . urlencode($INSTANCE), ['apikey: ' . $APIKEY]);
testEndpoint('GET /instance/status', 'GET', '/instance/status', ['apikey: ' . $APIKEY]);
testEndpoint('GET /instance/qr?instanceId=Mapos', 'GET', '/instance/qr?instanceId=' . urlencode($INSTANCE), ['apikey: ' . $APIKEY]);

echo "\n--- ENDPOINTS INSTANCE (com instance token) ---\n";
testEndpoint('GET /instance/all (token)', 'GET', '/instance/all', ['apikey: ' . $INSTANCE_TOKEN]);
testEndpoint('GET /instance/get/Mapos (token)', 'GET', '/instance/get/' . urlencode($INSTANCE), ['apikey: ' . $INSTANCE_TOKEN]);
testEndpoint('GET /instance/status (token)', 'GET', '/instance/status', ['apikey: ' . $INSTANCE_TOKEN]);

echo "\n--- ENDPOINTS INSTANCE (POST) ---\n";
testEndpoint('POST /instance/connect', 'POST', '/instance/connect', ['apikey: ' . $APIKEY, 'Content-Type: application/json'], ['instanceName' => $INSTANCE]);
testEndpoint('POST /instance/disconnect', 'POST', '/instance/disconnect', ['apikey: ' . $APIKEY, 'Content-Type: application/json'], ['instanceName' => $INSTANCE]);
testEndpoint('POST /instance/reconnect', 'POST', '/instance/reconnect', ['apikey: ' . $APIKEY, 'Content-Type: application/json'], ['instanceName' => $INSTANCE]);
testEndpoint('POST /instance/pair', 'POST', '/instance/pair', ['apikey: ' . $APIKEY, 'Content-Type: application/json'], ['instanceName' => $INSTANCE]);
testEndpoint('POST /instance/create', 'POST', '/instance/create', ['apikey: ' . $APIKEY, 'Content-Type: application/json'], ['instanceName' => $INSTANCE, 'token' => $INSTANCE_TOKEN]);

echo "\n--- ENDPOINTS SEND MESSAGE ---\n";
testEndpoint('POST /send/text', 'POST', '/send/text', ['apikey: ' . $APIKEY, 'Content-Type: application/json'], [
    'number' => '5511999999999',
    'text' => 'Teste de envio MAPOS',
    'options' => ['delay' => 1200]
]);

echo "\n--- VARIACOES DE AUTH (usando /instance/all) ---\n";
$authTests = [
    ['apikey: ' . $APIKEY, 'header_apikey_lowercase'],
    ['Apikey: ' . $APIKEY, 'header_Apikey_capitalize'],
    ['APIKEY: ' . $APIKEY, 'header_APIKEY_uppercase'],
    ['x-api-key: ' . $APIKEY, 'header_x-api-key'],
    ['X-Api-Key: ' . $APIKEY, 'header_X-Api-Key'],
    ['Authorization: Bearer ' . $APIKEY, 'auth_bearer'],
    ['Authorization: Apikey ' . $APIKEY, 'auth_apikey'],
    ['token: ' . $APIKEY, 'header_token'],
    ['x-auth-token: ' . $APIKEY, 'header_x-auth-token'],
    ['access_token: ' . $APIKEY, 'header_access_token'],
];
foreach ($authTests as $test) {
    testEndpoint($test[1], 'GET', '/instance/all', [$test[0]]);
}

echo "\n--- VARIACOES DE PATH BASE ---\n";
$pathTests = [
    '/api/instance/all',
    '/v1/instance/all',
    '/v2/instance/all',
    '/evo/instance/all',
    '/whatsapp/instance/all',
    '/bot/instance/all',
    '/api/v1/instance/all',
    '/api/v2/instance/all',
];
foreach ($pathTests as $path) {
    testEndpoint('GET ' . $path, 'GET', $path, ['apikey: ' . $APIKEY]);
}

echo "\n--- ENDPOINTS RAIZ E SWAGGER ---\n";
testEndpoint('GET /', 'GET', '/', [], null, [200, 301, 302]);
testEndpoint('GET /swagger', 'GET', '/swagger', [], null, [200, 301, 302]);
testEndpoint('GET /swagger/index.html', 'GET', '/swagger/index.html', [], null, [200, 301, 302]);
testEndpoint('GET /swagger/doc.json', 'GET', '/swagger/doc.json', [], null, [200, 301, 302]);
testEndpoint('GET /api/swagger.json', 'GET', '/api/swagger.json', [], null, [200, 301, 302]);

echo "\n--- TESTES COM QUERY STRING ---\n";
testEndpoint('GET /instance/all?apikey=...', 'GET', '/instance/all?apikey=' . urlencode($APIKEY), []);
testEndpoint('GET /instance/get/Mapos?apikey=...', 'GET', '/instance/get/' . urlencode($INSTANCE) . '?apikey=' . urlencode($APIKEY), []);

echo "\n=========================================\n";
echo "RESUMO DOS TESTES\n";
echo "=========================================\n";

$total = count($RESULTADOS);
$sucessos = count(array_filter($RESULTADOS, fn($r) => $r['success']));
$falhas = $total - $sucessos;

echo "Total de testes: $total\n";
echo "Sucessos (HTTP 200/201): $sucessos\n";
echo "Falhas: $falhas\n";

echo "\n--- Endpoints que FUNCIONARAM (HTTP 200/201) ---\n";
foreach ($RESULTADOS as $r) {
    if ($r['success']) {
        echo "✓ " . $r['nome'] . " -> " . $r['url'] . "\n";
    }
}

if ($falhas > 0) {
    echo "\n--- Endpoints que FALHARAM ---\n";
    foreach ($RESULTADOS as $r) {
        if (!$r['success']) {
            echo "✗ " . $r['nome'] . " -> HTTP " . $r['http_code'];
            if ($r['body_preview']) echo " | " . substr($r['body_preview'], 0, 80);
            echo "\n";
        }
    }
}

// Salvar resultado em JSON
file_put_contents('test_resultado.json', json_encode($RESULTADOS, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nResultado completo salvo em: test_resultado.json\n";
