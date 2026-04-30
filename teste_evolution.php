<?php
/**
 * Script de diagnostico Evolution API
 * Acesse via: https://seu-mapos.com.br/teste_evolution.php
 * Ou execute via CLI: php teste_evolution.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'application/config/database.php';

echo "=== Diagnostico Evolution API ===\n\n";

// 1. Testa conexao com a URL externa
$url = 'https://evo.jj-ferreiras.com.br/instance/all';
$apikey = '7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2';

echo "1. Testando URL EXTERNA ($url)\n";
echo "   Headers: apikey=" . substr($apikey, 0, 8) . "...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json, text/plain, */*',
    'Accept-Language: pt-BR,pt;q=0.9',
    'Cache-Control: no-cache',
    'Pragma: no-cache',
    'apikey: ' . $apikey,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "   HTTP Code: $httpCode\n";
echo "   CURL Error: " . ($error ?: 'Nenhum') . "\n";
echo "   Final URL: $finalUrl\n";
echo "   Response Headers:\n";
foreach (explode("\n", $headers) as $h) {
    $h = trim($h);
    if ($h) echo "      $h\n";
}
echo "   Body (primeiros 500 chars):\n";
echo "      " . substr($body, 0, 500) . "\n";
echo "\n";

// 2. Testa localhost:8091
$localUrl = 'http://127.0.0.1:8091/instance/all';
echo "2. Testando URL LOCAL ($localUrl)\n";

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $localUrl);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_TIMEOUT, 5);
curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 3);
curl_setopt($ch2, CURLOPT_HEADER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['apikey: ' . $apikey]);

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$error2 = curl_error($ch2);
curl_close($ch2);

echo "   HTTP Code: $httpCode2\n";
echo "   CURL Error: " . ($error2 ?: 'Nenhum') . "\n";
echo "   Body: " . substr($response2, 0, 200) . "\n";
echo "\n";

// 3. Verifica DNS
echo "3. Resolucao DNS\n";
$ip = gethostbyname('evo.jj-ferreiras.com.br');
echo "   evo.jj-ferreiras.com.br resolve para: $ip\n";
echo "   IP local do servidor: " . $_SERVER['SERVER_ADDR'] ?? 'N/A' . "\n";
echo "\n";

// 4. Verifica portas abertas locais
echo "4. Portas abertas localmente (ss -tlnp | grep -E '8080|8090|8091|3000'):\n";
$ports = shell_exec('ss -tlnp 2>/dev/null | grep -E "(8080|8090|8091|3000)" || netstat -tlnp 2>/dev/null | grep -E "(8080|8090|8091|3000)" || echo "Comando nao disponivel"');
echo "   " . ($ports ?: "Nenhuma porta encontrada ou comando indisponivel") . "\n";
echo "\n";

// 5. Verifica processos Evolution
echo "5. Processos Evolution rodando:\n";
$processes = shell_exec('ps aux | grep -i evolution | grep -v grep || echo "Nenhum processo encontrado"');
echo "   " . ($processes ?: "Nenhum") . "\n";
echo "\n";

// 6. Verifica containers Docker
echo "6. Containers Docker:\n";
$docker = shell_exec('docker ps 2>/dev/null | grep -i evolution || echo "Docker nao disponivel ou sem containers evolution"');
echo "   " . ($docker ?: "Nenhum") . "\n";
echo "\n";

echo "=== Fim do Diagnostico ===\n";
echo "\nSOLUCAO SUGERIDA:\n";
if ($httpCode === 404) {
    echo "- O Cloudflare/Nginx esta redirecionando a requisicao.\n";
    echo "- Verifique se o Cloudflare Access (Zero Trust) esta ativo para evo.jj-ferreiras.com.br\n";
    echo "- No painel Cloudflare: Acesse Zero Trust > Access > Applications\n";
    echo "- Adicione uma regra de bypass para o IP do servidor: " . ($_SERVER['SERVER_ADDR'] ?? 'DESCONHECIDO') . "\n";
    echo "- Ou adicione um Service Token e configure no cURL.\n";
} elseif ($httpCode === 401) {
    echo "- API Key incorreta. Verifique no painel da Evolution API.\n";
} elseif ($httpCode === 200) {
    echo "- A URL externa funciona! O problema pode ser so no codigo.\n";
} elseif ($error2 && strpos($error2, 'Connection refused') !== false) {
    echo "- A Evolution API NAO esta rodando em localhost:8091.\n";
    echo "- Verifique se o container/servico esta rodando em outra porta.\n";
}
