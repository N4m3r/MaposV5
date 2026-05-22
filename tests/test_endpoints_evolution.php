<?php
/**
 * Script de teste completo dos endpoints Evolution GO
 * Execute no servidor: php test_endpoints_evolution.php
 *
 * IMPORTANTE: Configure as variaveis de ambiente antes de executar:
 *   export EVO_BASE_URL='https://evo.jj-ferreiras.com.br'
 *   export EVO_API_KEY='sua-api-key'
 *   export EVO_INSTANCE='Mapos'
 *   export EVO_INSTANCE_TOKEN='seu-instance-token'
 */

// Configuracao - ler de variaveis de ambiente, NUNCA hardcoded
$BASE_URL = getenv('EVO_BASE_URL') ?: '';
$APIKEY = getenv('EVO_API_KEY') ?: '';
$INSTANCE = getenv('EVO_INSTANCE') ?: 'Mapos';
$INSTANCE_TOKEN = getenv('EVO_INSTANCE_TOKEN') ?: '';

if (empty($BASE_URL) || empty($APIKEY)) {
    echo "ERRO: Configure as variaveis de ambiente antes de executar:\n";
    echo "  export EVO_BASE_URL='https://seu-servidor.com'\n";
    echo "  export EVO_API_KEY='sua-api-key'\n";
    echo "  export EVO_INSTANCE='Mapos'\n";
    echo "  export EVO_INSTANCE_TOKEN='seu-token'\n";
    exit(1);
}

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
    $headerSize = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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

echo "\n=========================================\n";
echo "RESUMO DOS TESTES\n";
echo "=========================================\n";

$total = count($RESULTADOS);
$sucessos = count(array_filter($RESULTADOS, fn($r) => $r['success']));
$falhas = $total - $sucessos;

echo "Total de testes: $total\n";
echo "Sucessos (HTTP 200/201): $sucessos\n";
echo "Falhas: $falhas\n";

// Salvar resultado em JSON (sem expor credenciais)
$safeResultados = array_map(function($r) {
    unset($r['url']);
    return $r;
}, $RESULTADOS);
file_put_contents('test_resultado.json', json_encode($safeResultados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nResultado completo salvo em: test_resultado.json\n";