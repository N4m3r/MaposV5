<?php
/**
 * Script de Deploy para Kinghost
 * Acesse: https://jj-ferreiras.com.br/MaposV5/deploy_server.php?key=deploy2025mapos
 *
 * Este script:
 * 1. Remove BOM UTF-8 de todos os PHP files
 * 2. Executa as migracoes SQL necessarias
 * 3. Verifica permissoes de diretorios
 * 4. Exibe diagnostico do sistema
 */

// Seguranca - chave de acesso
$secret_key = 'deploy2025mapos';
if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    http_response_code(403);
    die('Acesso negado');
}

echo "<html><head><title>Deploy MaposV5</title><style>body{font-family:monospace;background:#1a1a2e;color:#eee;padding:20px}h1{color:#0f0}h2{color:#0af}.ok{color:#0f0}.erro{color:#f00}.aviso{color:#ff0}pre{background:#16213e;padding:10px;border-radius:5px;overflow-x:auto}</style></head><body>";
echo "<h1>🚀 Deploy MaposV5 - Kinghost</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

$base_path = dirname(__FILE__);
$changes_made = 0;
$errors = [];

// =============================================
// 1. REMOVER BOM UTF-8 DE TODOS OS PHP FILES
// =============================================
echo "<h2>1. Remocao de BOM UTF-8</h2>";

$php_files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base_path, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$bom_count = 0;
$bom_files = [];

foreach ($php_files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filepath = $file->getRealPath();
        // Pular este proprio script
        if (basename($filepath) === 'deploy_server.php' || basename($filepath) === 'fix_bom_server.php') {
            continue;
        }

        $content = file_get_contents($filepath);
        if (strlen($content) >= 3 && substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $bom_files[] = str_replace($base_path . '/', '', $filepath);
            $new_content = substr($content, 3);
            if (file_put_contents($filepath, $new_content) !== false) {
                $bom_count++;
            } else {
                $errors[] = "Falha ao escrever: " . basename($filepath);
            }
        }
    }
}

if ($bom_count > 0) {
    echo "<p class='ok'>✅ BOM removido de <strong>$bom_count</strong> arquivos:</p>";
    echo "<pre>" . implode("\n", array_slice($bom_files, 0, 30)) . "</pre>";
    if (count($bom_files) > 30) {
        echo "<p>... e mais " . (count($bom_files) - 30) . " arquivos</p>";
    }
    $changes_made += $bom_count;
} else {
    echo "<p class='ok'>✅ Nenhum arquivo com BOM encontrado.</p>";
}

// =============================================
// 2. EXECUTAR MIGRACOES SQL
// =============================================
echo "<h2>2. Migracoes do Banco de Dados</h2>";

// Carregar configuracoes do database.php
$db_config_path = $base_path . '/application/config/database.php';
$db_config = file_get_contents($db_config_path);

// Extrair configuracoes
preg_match("/['\"]hostname['\"]]\s*=>\s*['\"]([^'\"]+)['\"]/", $db_config, $host_match);
preg_match("/['\"]username['\"]]\s*=>\s*['\"]([^'\"]+)['\"]/", $db_config, $user_match);
preg_match("/['\"]password['\"]]\s*=>\s*['\"]([^'\"]+)['\"]/", $db_config, $pass_match);
preg_match("/['\"]database['\"]]\s*=>\s*['\"]([^'\"]+)['\"]/", $db_config, $db_match);

$db_host = $host_match[1] ?? 'localhost';
$db_user = $user_match[1] ?? '';
$db_pass = $pass_match[1] ?? '';
$db_name = $db_match[1] ?? '';

echo "<p>Conectando ao banco: <strong>$db_name</strong> em <strong>$db_host</strong>...</p>";

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<p class='erro'>❌ Erro de conexao: " . $mysqli->connect_error . "</p>";
    $errors[] = "DB connection failed: " . $mysqli->connect_error;
} else {
    echo "<p class='ok'>✅ Conectado ao banco de dados.</p>";

    // Verificar e criar tabela notificacoes_config
    $tables_to_create = [
        "CREATE TABLE IF NOT EXISTS `notificacoes_config` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `whatsapp_ativo` tinyint(1) DEFAULT 0,
            `whatsapp_provedor` varchar(50) DEFAULT 'evolution',
            `whatsapp_api_url` varchar(255) DEFAULT NULL,
            `whatsapp_api_key` varchar(255) DEFAULT NULL,
            `whatsapp_instance` varchar(100) DEFAULT NULL,
            `whatsapp_numero_padrao` varchar(20) DEFAULT NULL,
            `email_ativo` tinyint(1) DEFAULT 1,
            `notificar_os_criada` tinyint(1) DEFAULT 1,
            `notificar_os_atualizada` tinyint(1) DEFAULT 1,
            `notificar_venda_realizada` tinyint(1) DEFAULT 1,
            `notificar_cobranca_gerada` tinyint(1) DEFAULT 1,
            `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
            `data_atualizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS `notificacoes_templates` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `chave` varchar(100) NOT NULL,
            `nome` varchar(255) NOT NULL,
            `conteudo` text NOT NULL,
            `canal` enum('whatsapp','email','ambos') DEFAULT 'whatsapp',
            `ativo` tinyint(1) DEFAULT 1,
            `categoria` varchar(50) DEFAULT 'sistema',
            `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
            `data_atualizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `chave` (`chave`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS `notificacoes_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `template_id` int(11) DEFAULT NULL,
            `destinatario` varchar(255) NOT NULL,
            `canal` enum('whatsapp','email') DEFAULT 'whatsapp',
            `status` enum('pendente','enviado','falhou') DEFAULT 'pendente',
            `mensagem` text DEFAULT NULL,
            `erro` text DEFAULT NULL,
            `tentativas` int(11) DEFAULT 0,
            `data_envio` datetime DEFAULT NULL,
            `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_status` (`status`),
            KEY `idx_destinatario` (`destinatario`),
            KEY `idx_data` (`data_criacao`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ];

    foreach ($tables_to_create as $sql) {
        if ($mysqli->query($sql)) {
            echo "<p class='ok'>✅ Tabela criada/verificada com sucesso.</p>";
        } else {
            echo "<p class='aviso'>⚠️ SQL: " . $mysqli->error . "</p>";
        }
    }

    // Inserir templates padrao se nao existirem
    $default_templates = [
        ['os_criada', 'OS Criada', 'Olá! Uma nova OS #{id_os} foi criada para o cliente {cliente}. Status: {status}', 'whatsapp', 'os'],
        ['os_atualizada', 'OS Atualizada', 'A OS #{id_os} foi atualizada. Novo status: {status}', 'whatsapp', 'os'],
        ['venda_realizada', 'Venda Realizada', 'Venda #{id_venda} realizada para {cliente}. Valor: R$ {valor}', 'whatsapp', 'vendas'],
        ['cobranca_gerada', 'Cobrança Gerada', 'Nova cobrança gerada: R$ {valor} com vencimento em {vencimento}', 'whatsapp', 'financeiro'],
    ];

    foreach ($default_templates as $tpl) {
        $check = $mysqli->query("SELECT id FROM notificacoes_templates WHERE chave = '" . $tpl[0] . "'");
        if ($check && $check->num_rows == 0) {
            $stmt = $mysqli->prepare("INSERT INTO notificacoes_templates (chave, nome, conteudo, canal, categoria) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $tpl[0], $tpl[1], $tpl[2], $tpl[3], $tpl[4]);
            $stmt->execute();
            echo "<p class='ok'>✅ Template '{$tpl[1]}' inserido.</p>";
        }
    }

    // Inserir config padrao se nao existir
    $check_config = $mysqli->query("SELECT id FROM notificacoes_config LIMIT 1");
    if ($check_config && $check_config->num_rows == 0) {
        $mysqli->query("INSERT INTO notificacoes_config (whatsapp_ativo, email_ativo) VALUES (0, 1)");
        echo "<p class='ok'>✅ Config padrao de notificacoes inserida.</p>";
    }

    // Verificar/adicionar permissao cConfiguracao
    $check_perm = $mysqli->query("SELECT idPermissao FROM permissoes WHERE permissoes LIKE '%cConfiguracao%' OR permissoes LIKE '%\"cConfiguracao\"%' LIMIT 1");
    if ($check_perm && $check_perm->num_rows > 0) {
        $row = $check_perm->fetch_assoc();
        $idPerm = $row['idPermissao'];
        $perm_data = $mysqli->query("SELECT permissoes FROM permissoes WHERE idPermissao = $idPerm")->fetch_assoc();
        $perm_string = $perm_data['permissoes'];

        // Verificar se ja tem cConfiguracao
        if (strpos($perm_string, 'cConfiguracao') === false) {
            $new_perm = rtrim($perm_string, ',') . ',cConfiguracao';
            $mysqli->query("UPDATE permissoes SET permissoes = '" . $mysqli->real_escape_string($new_perm) . "' WHERE idPermissao = $idPerm");
            echo "<p class='ok'>✅ Permissao cConfiguracao adicionada ao perfil admin (id=$idPerm).</p>";
        } else {
            echo "<p class='ok'>✅ Permissao cConfiguracao ja existe.</p>";
        }
    } else {
        echo "<p class='aviso'>⚠️ Nao foi possivel adicionar permissao cConfiguracao automaticamente.</p>";
    }

    // Verificar colunas da tabela permissoes (para JSON migration)
    $columns_result = $mysqli->query("SHOW COLUMNS FROM permissoes");
    $has_permissions_json = false;
    while ($col = $columns_result->fetch_assoc()) {
        if ($col['Field'] === 'permissions_json') {
            $has_permissions_json = true;
        }
    }

    echo "<p>Coluna permissions_json: " . ($has_permissions_json ? 'SIM' : 'NAO') . "</p>";

    $mysqli->close();
}

// =============================================
// 3. VERIFICAR PERMISSOES DE DIRETORIOS
// =============================================
echo "<h2>3. Permissoes de Diretorios</h2>";

$critical_dirs = [
    'application/logs',
    'application/cache',
    'assets/uploads',
    'assets/arquivos',
    'backups',
    'application/database/migrations'
];

foreach ($critical_dirs as $dir) {
    $full_path = $base_path . '/' . $dir;
    if (is_dir($full_path)) {
        if (is_writable($full_path)) {
            echo "<p class='ok'>✅ $dir - gravavel</p>";
        } else {
            echo "<p class='erro'>❌ $dir - NAO gravavel! Tentando corrigir...</p>";
            @chmod($full_path, 0755);
            echo "<p>" . (is_writable($full_path) ? "<span class='ok'>✅ Corrigido!</span>" : "<span class='erro'>❌ Ainda sem permissao.</span>") . "</p>";
        }
    } else {
        echo "<p class='aviso'>⚠️ $dir - diretorio nao existe, criando...</p>";
        @mkdir($full_path, 0755, true);
    }
}

// =============================================
// 4. VERIFICAR ARQUIVOS CRITICOS
// =============================================
echo "<h2>4. Verificacao de Arquivos Criticos</h2>";

$critical_files = [
    'application/controllers/NotificacoesConfig.php' => 'Controller de Notificacoes',
    'application/controllers/Certificado.php' => 'Controller de Certificado',
    'application/controllers/Impostos.php' => 'Controller de Impostos',
    'application/controllers/Dre.php' => 'Controller de DRE',
    'application/controllers/Atividades.php' => 'Controller de Atividades',
    'application/controllers/Nfse_os.php' => 'Controller de NFS-e',
    'application/controllers/Usuarioscliente.php' => 'Controller de Usuarios Cliente',
    'application/controllers/Webhooks.php' => 'Controller de Webhooks',
    'application/controllers/Email.php' => 'Controller de Email',
    'application/controllers/Migrate.php' => 'Controller de Migracoes',
    'application/controllers/Agente_ia.php' => 'Controller de Agente IA',
    'application/models/Notificacoes_config_model.php' => 'Model Notificacoes Config',
    'application/models/Notificacoes_templates_model.php' => 'Model Notificacoes Templates',
    'application/models/Notificacoes_log_model.php' => 'Model Notificacoes Log',
    'application/libraries/WhatsAppService.php' => 'Library WhatsApp Service',
    'application/helpers/notificacoes_helper.php' => 'Helper Notificacoes',
    'application/views/tema/menu.php' => 'Menu Sidebar',
    'application/views/notificacoes/configuracoes.php' => 'View Notificacoes Config',
    'application/views/notificacoes/templates.php' => 'View Notificacoes Templates',
    'application/views/notificacoes/logs.php' => 'View Notificacoes Logs',
    'application/views/impostos/relatorio.php' => 'View Impostos Relatorio',
    'application/views/nfse_os/visualizar.php' => 'View NFS-e Visualizar',
    'application/views/notificacoes/estatisticas.php' => 'View Notificacoes Estatisticas',
    'application/config/routes.php' => 'Rotas do Sistema',
];

foreach ($critical_files as $file => $desc) {
    $full_path = $base_path . '/' . $file;
    if (file_exists($full_path)) {
        $size = filesize($full_path);
        echo "<p class='ok'>✅ $desc ($file) - {$size} bytes</p>";
    } else {
        echo "<p class='erro'>❌ $desc ($file) - AUSENTE!</p>";
        $errors[] = "Arquivo ausente: $file";
    }
}

// =============================================
// 5. VERIFICACAO DE ROTAS
// =============================================
echo "<h2>5. Verificacao de Rotas Criticas</h2>";

$routes_check = [
    '/notificacoes/configuracoes' => 'Notificacoes Config',
    '/notificacoes/templates' => 'Notificacoes Templates',
    '/notificacoes/logs' => 'Notificacoes Logs',
    '/certificado' => 'Certificado Digital',
    '/certificado/configurar' => 'Certificado Config',
    '/impostos' => 'Impostos Dashboard',
    '/impostos/configuracoes' => 'Impostos Config',
    '/dre' => 'DRE Dashboard',
    '/dre/contas' => 'DRE Plano de Contas',
    '/usuarioscliente' => 'Usuarios Cliente',
    '/atividades' => 'Atividades Dashboard',
    '/atividades/relatorio' => 'Atividades Relatorio',
    '/nfse_os' => 'NFSe Dashboard',
    '/webhooks' => 'Webhooks',
    '/emails/dashboard' => 'Fila de Emails',
    '/migrate' => 'Migracoes DB',
    '/api/docs' => 'API v2 Docs',
    '/agente_ia' => 'Agente IA',
];

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . str_replace('/deploy_server.php', '', $_SERVER['SCRIPT_NAME']);

echo "<p>Base URL detectada: <strong>$base_url</strong></p>";

foreach ($routes_check as $route => $desc) {
    echo "<p>🔗 <a href='$base_url$route' target='_blank'>$desc</a> - $route</p>";
}

// =============================================
// RESUMO
// =============================================
echo "<h2>📋 Resumo do Deploy</h2>";
echo "<p>Arquivos com BOM corrigidos: <strong>$bom_count</strong></p>";
echo "<p>Erros encontrados: <strong>" . count($errors) . "</strong></p>";

if (count($errors) > 0) {
    echo "<pre class='erro'>" . implode("\n", $errors) . "</pre>";
}

echo "<p class='ok'><strong>✅ Deploy concluido!</strong></p>";
echo "<p>⚠️ <strong>IMPORTANTE:</strong> Delete este arquivo (deploy_server.php) apos o deploy por seguranca!</p>";
echo "<p><a href='$base_url'>Voltar ao sistema</a> | <a href='$base_url/mapos'>Painel</a></p>";

echo "</body></html>";