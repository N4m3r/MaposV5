<?php
/**
 * Diagnostico Completo - MapOS + Evolution API
 * Coloque na raiz do projeto e acesse pelo navegador
 */

ob_start();

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Diagnostico Evolution</title>";
echo "<style>body{font-family:sans-serif;padding:20px;} h2{color:#333;border-bottom:2px solid #007bff;padding-bottom:5px;} .ok{color:green;font-weight:bold;} .err{color:red;font-weight:bold;} .warn{color:orange;font-weight:bold;} pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow:auto;} td,th{padding:8px;border:1px solid #ddd;} table{border-collapse:collapse;width:100%;margin:10px 0;} th{background:#007bff;color:#fff;}</style></head><body>";
echo "<h1>Diagnostico Completo: MapOS + Evolution API</h1><hr>";

$erros = [];
$avisos = [];

// ============================================
// 1. VERIFICAR ARQUIVOS EXISTENTES
// ============================================
echo "<h2>1. Verificacao de Arquivos</h2>";
$files = [
    'application/controllers/NotificacoesConfig.php',
    'application/models/Notificacoes_config_model.php',
    'application/Services/WhatsAppService.php',
    'application/views/notificacoes/configuracoes.php',
    'application/config/routes.php',
    'application/hooks/check_notificacoes_tables.php',
    'application/helpers/autoload_helper.php',
    'application/helpers/notificacoes_helper.php',
];
foreach ($files as $file) {
    $exists = file_exists($file);
    echo ($exists ? '<span class="ok">✓</span>' : '<span class="err">✗</span>') . " $file<br>";
    if (!$exists) $erros[] = "Arquivo nao encontrado: $file";
}

// ============================================
// 2. VERIFICAR SINTAXE PHP
// ============================================
echo "<h2>2. Sintaxe dos Arquivos PHP</h2>";
$phpFiles = [
    'application/controllers/NotificacoesConfig.php',
    'application/models/Notificacoes_config_model.php',
    'application/Services/WhatsAppService.php',
];
foreach ($phpFiles as $file) {
    if (file_exists($file)) {
        exec("php -l $file 2>&1", $output, $return);
        echo "<strong>$file:</strong> ";
        if ($return === 0) {
            echo "<span class='ok'>OK</span><br>";
        } else {
            echo "<span class='err'>ERRO: " . implode(' ', $output) . "</span><br>";
            $erros[] = "Erro de sintaxe em $file";
        }
    }
}

// ============================================
// 3. VERIFICAR ROTAS
// ============================================
echo "<h2>3. Rotas Configuradas</h2>";
if (file_exists('application/config/routes.php')) {
    $routesContent = file_get_contents('application/config/routes.php');
    $rotasEsperadas = [
        'notificacoes/configuracoes',
        'notificacoes/obter-qr',
        'notificacoes/verificar-status',
        'notificacoes/desconectar',
        'notificacoes/testar-envio',
    ];
    foreach ($rotasEsperadas as $rota) {
        if (strpos($routesContent, "'$rota'") !== false || strpos($routesContent, '"' . $rota . '"') !== false) {
            echo "<span class='ok'>✓</span> Rota '$rota' configurada<br>";
        } else {
            echo "<span class='warn'>⚠</span> Rota '$rota' NAO encontrada em routes.php (pode funcionar sem rota explicita)<br>";
            $avisos[] = "Rota $rota nao explicita em routes.php";
        }
    }
}

// ============================================
// 4. CONEXAO COM BANCO DE DADOS
// ============================================
echo "<h2>4. Conexao com Banco de Dados</h2>";
$dbOk = false;
$configBanco = null;
if (file_exists('application/config/database.php')) {
    if (!defined('BASEPATH')) define('BASEPATH', true);
    if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'production');
    require_once 'application/config/database.php';
    $dbConfig = $db['default'];
    $mysqli = null;
    try {
        $mysqli = new mysqli($dbConfig['hostname'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);
    } catch (Exception $e) {
        echo "<span class='err'>✗ Erro na conexao: " . $e->getMessage() . "</span><br>";
        $erros[] = "Erro conexao banco: " . $e->getMessage();
        echo "<span class='warn'>⚠ Verifique se o banco esta configurado corretamente em application/config/database.php</span><br>";
    }

    if ($mysqli && !$mysqli->connect_error) {
        echo "<span class='ok'>✓</span> Conectado ao banco: " . $dbConfig['database'] . "<br>";
        $dbOk = true;

        // Verificar tabelas
        $tabelas = ['notificacoes_config', 'notificacoes_templates', 'notificacoes_log'];
        foreach ($tabelas as $tabela) {
            $result = $mysqli->query("SHOW TABLES LIKE '$tabela'");
            if ($result && $result->num_rows > 0) {
                echo "  <span class='ok'>✓</span> Tabela '$tabela' existe<br>";
            } else {
                echo "  <span class='err'>✗</span> Tabela '$tabela' NAO existe<br>";
                $erros[] = "Tabela $tabela nao existe";
            }
        }

        // Verificar colunas especificas
        if ($dbOk) {
            $res = $mysqli->query("SHOW COLUMNS FROM `notificacoes_config` LIKE 'evolution_instance_token'");
            if ($res && $res->num_rows > 0) {
                echo "  <span class='ok'>✓</span> Coluna 'evolution_instance_token' existe<br>";
            } else {
                echo "  <span class='err'>✗</span> Coluna 'evolution_instance_token' NAO existe<br>";
                $erros[] = "Coluna evolution_instance_token nao existe";
            }

            $res = $mysqli->query("SHOW COLUMNS FROM `notificacoes_config` LIKE 'evolution_version'");
            if ($res && $res->num_rows > 0) {
                echo "  <span class='ok'>✓</span> Coluna 'evolution_version' existe<br>";
            } else {
                echo "  <span class='err'>✗</span> Coluna 'evolution_version' NAO existe<br>";
                $erros[] = "Coluna evolution_version nao existe";
            }
        }

        // Verificar configuracoes salvas
        if ($dbOk) {
            $res = $mysqli->query("SELECT * FROM `notificacoes_config` WHERE id = 1");
            if ($res && $res->num_rows > 0) {
                $configBanco = $res->fetch_assoc();
                echo "  <span class='ok'>✓</span> Registro id=1 encontrado em notificacoes_config<br>";
                echo "  <pre>";
                echo "whatsapp_provedor: " . ($configBanco['whatsapp_provedor'] ?? 'NULL') . "\n";
                echo "whatsapp_ativo: " . ($configBanco['whatsapp_ativo'] ?? 'NULL') . "\n";
                echo "evolution_url: " . ($configBanco['evolution_url'] ?? 'NULL') . "\n";
                echo "evolution_apikey: " . (empty($configBanco['evolution_apikey']) ? 'VAZIO' : substr($configBanco['evolution_apikey'], 0, 12) . '...') . "\n";
                echo "evolution_instance: " . ($configBanco['evolution_instance'] ?? 'NULL') . "\n";
                echo "evolution_instance_token: " . (empty($configBanco['evolution_instance_token']) ? 'VAZIO' : substr($configBanco['evolution_instance_token'], 0, 12) . '...') . "\n";
                echo "evolution_estado: " . ($configBanco['evolution_estado'] ?? 'NULL') . "\n";
                echo "</pre>";

                if (empty($configBanco['evolution_url']) || empty($configBanco['evolution_apikey'])) {
                    $erros[] = "Configuracoes Evolution nao preenchidas no banco";
                }
                if ($configBanco['whatsapp_ativo'] != 1) {
                    $avisos[] = "whatsapp_ativo = 0 no banco (pode impedir verificacao de status)";
                }
                if ($configBanco['whatsapp_provedor'] != 'evolution') {
                    $avisos[] = "whatsapp_provedor != 'evolution' no banco";
                }
            } else {
                echo "  <span class='err'>✗</span> Registro id=1 NAO encontrado em notificacoes_config<br>";
                $erros[] = "Registro id=1 nao existe em notificacoes_config";
            }
        }

        $mysqli->close();
    }
}

// ============================================
// 5. TESTES CURL DIRETOS NA EVOLUTION API
// ============================================
echo "<h2>5. Testes Diretos na Evolution API</h2>";

$evolution_url = $configBanco['evolution_url'] ?? 'https://evo.jj-ferreiras.com.br';
$evolution_apikey = $configBanco['evolution_apikey'] ?? '';
$evolution_instance = $configBanco['evolution_instance'] ?? 'Mapos';
$evolution_instance_token = $configBanco['evolution_instance_token'] ?? '';

function curlTeste($url, $headers = [], $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($ch, CURLOPT_HEADER, true);
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(['Accept: application/json'], $headers));
    }
    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($data) ? $data : json_encode($data));
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    return [
        'http_code' => $httpCode,
        'error' => $error,
        'body' => substr($response, $headerSize),
    ];
}

// Teste 1: /instance/all com API Key global
$resp = curlTeste(rtrim($evolution_url, '/') . '/instance/all', ['apikey: ' . $evolution_apikey]);
echo "<h4>5.1 GET /instance/all (API Key global)</h4>";
echo "HTTP: " . $resp['http_code'] . " | Error: " . ($resp['error'] ?: 'Nenhum') . "<br>";
if ($resp['http_code'] == 200) {
    echo "<span class='ok'>✓ OK</span><br>";
    $data = json_decode($resp['body'], true);
    $instances = $data['data'] ?? [];
    echo "Instancias encontradas: " . count($instances) . "<br>";
    $instanceData = null;
    foreach ($instances as $inst) {
        if (strcasecmp($inst['name'] ?? '', $evolution_instance) === 0) {
            $instanceData = $inst;
            break;
        }
    }
    if ($instanceData) {
        echo "<span class='ok'>✓</span> Instancia '$evolution_instance' encontrada!<br>";
        echo "Connected: " . ($instanceData['connected'] ? '<span class="ok">true</span>' : '<span class="err">false</span>') . "<br>";
        echo "Token: " . (!empty($instanceData['token']) ? substr($instanceData['token'], 0, 12) . '...' : '<span class="err">VAZIO</span>') . "<br>";
        if (empty($instanceData['token'])) {
            $erros[] = "Instancia encontrada mas sem token";
        }
        if ($instanceData['connected'] !== true) {
            $avisos[] = "Instancia encontrada mas nao esta connected=true";
        }
    } else {
        echo "<span class='err'>✗ Instancia '$evolution_instance' NAO encontrada!</span><br>";
        $erros[] = "Instancia $evolution_instance nao encontrada em /instance/all";
    }
} else {
    echo "<span class='err'>✗ FALHA</span><br>";
    echo "<pre>" . htmlspecialchars(substr($resp['body'], 0, 300)) . "</pre>";
    $erros[] = "/instance/all retornou HTTP " . $resp['http_code'];
}

// Teste 2: /instance/status com token da instancia
$instanceToken = $instanceData['token'] ?? $evolution_instance_token;
if ($instanceToken) {
    $resp = curlTeste(rtrim($evolution_url, '/') . '/instance/status', ['apikey: ' . $instanceToken]);
    echo "<h4>5.2 GET /instance/status (Token da instancia)</h4>";
    echo "HTTP: " . $resp['http_code'] . " | Error: " . ($resp['error'] ?: 'Nenhum') . "<br>";
    if ($resp['http_code'] == 200) {
        echo "<span class='ok'>✓ OK</span><br>";
        $data = json_decode($resp['body'], true);
        echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo "<span class='err'>✗ FALHA</span><br>";
        echo "<pre>" . htmlspecialchars(substr($resp['body'], 0, 300)) . "</pre>";
        $erros[] = "/instance/status retornou HTTP " . $resp['http_code'];
    }
} else {
    echo "<h4>5.2 GET /instance/status (Token da instancia)</h4>";
    echo "<span class='warn'>⚠ Pulado - token da instancia nao disponivel</span><br>";
}

// Teste 3: /send/text com token da instancia
if ($instanceToken) {
    $resp = curlTeste(
        rtrim($evolution_url, '/') . '/send/text',
        ['apikey: ' . $instanceToken, 'Content-Type: application/json'],
        'POST',
        ['number' => '5511999999999', 'text' => 'Teste diagnostico MAPOS', 'options' => ['delay' => 1200]]
    );
    echo "<h4>5.3 POST /send/text (Token da instancia)</h4>";
    echo "HTTP: " . $resp['http_code'] . " | Error: " . ($resp['error'] ?: 'Nenhum') . "<br>";
    if ($resp['http_code'] == 200) {
        echo "<span class='ok'>✓ OK - Mensagem enviada (ou simulada)</span><br>";
    } elseif ($resp['http_code'] == 500 && strpos($resp['body'], 'not registered') !== false) {
        echo "<span class='ok'>✓ OK - Endpoint funciona (numero de teste nao existe no WhatsApp, o que e esperado)</span><br>";
    } else {
        echo "<span class='err'>✗ FALHA</span><br>";
        echo "<pre>" . htmlspecialchars(substr($resp['body'], 0, 300)) . "</pre>";
        $avisos[] = "/send/text retornou HTTP " . $resp['http_code'];
    }
} else {
    echo "<h4>5.3 POST /send/text (Token da instancia)</h4>";
    echo "<span class='warn'>⚠ Pulado - token da instancia nao disponivel</span><br>";
}

// Teste 4: /instance/disconnect (nao executa de verdade, so testa)
echo "<h4>5.4 POST /instance/disconnect (Token da instancia - NAO EXECUTADO)</h4>";
echo "<span class='warn'>⚠ Teste omitido para nao desconectar a instancia</span><br>";

// ============================================
// 6. RESUMO
// ============================================
echo "<h2>6. Resumo</h2>";
if (count($erros) === 0 && count($avisos) === 0) {
    echo "<div style='background:#d4edda;color:#155724;padding:15px;border-radius:5px;'><strong>TUDO OK!</strong><br>A integracao com a Evolution API esta configurada e funcionando corretamente.</div>";
} else {
    if (count($erros) > 0) {
        echo "<div style='background:#f8d7da;color:#721c24;padding:15px;border-radius:5px;margin-bottom:10px;'>";
        echo "<strong>ERROS ENCONTRADOS (" . count($erros) . "):</strong><ul>";
        foreach ($erros as $e) echo "<li>$e</li>";
        echo "</ul></div>";
    }
    if (count($avisos) > 0) {
        echo "<div style='background:#fff3cd;color:#856404;padding:15px;border-radius:5px;'>";
        echo "<strong>AVISOS (" . count($avisos) . "):</strong><ul>";
        foreach ($avisos as $a) echo "<li>$a</li>";
        echo "</ul></div>";
    }
}

echo "<hr><p><strong>Proximos passos:</strong></p><ul>";
echo "<li>Se tudo estiver OK, acesse: <a href='index.php/notificacoes/configuracoes'>Configuracoes de Notificacoes</a></li>";
echo "<li>Se houver erros de banco, execute o SQL em <code>application/database/migrations/notificacoes_config.sql</code></li>";
echo "<li>Se houver erros de API, verifique URL e API Key nas configuracoes</li>";
echo "</ul>";

$content = ob_get_clean();
echo $content;

// Salvar resultado
file_put_contents('diagnostico_evolution_resultado.html', $content);
echo "<hr><p>Resultado salvo em: <strong>diagnostico_evolution_resultado.html</strong></p>";
echo "</body></html>";
