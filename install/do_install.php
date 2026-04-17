<?php

// ============================================
// SISTEMA DE LOGS DETALHADO
// ============================================
require_once __DIR__ . DIRECTORY_SEPARATOR . 'InstallLogger.php';

$log = new InstallLogger();

// Garantir que erros não quebrem o JSON de resposta
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_error.log');
ini_set('max_execution_time', 300);

// Capturar todos os erros e exceções
set_error_handler(function($errno, $errstr, $errfile, $errline) use ($log) {
    $log->error("Erro PHP capturado [{$errno}]", [
        'msg' => $errstr,
        'file' => $errfile,
        'line' => $errline,
    ]);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erro [' . $errno . ']: ' . $errstr . ' em ' . basename($errfile) . ':' . $errline,
        'step' => 0,
        'log_file' => basename($log->getLogFile()),
    ]);
    exit();
});

set_exception_handler(function($e) use ($log) {
    $log->error("Exceção capturada", [
        'msg' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Exceção: ' . $e->getMessage() . ' em ' . basename($e->getFile()) . ':' . $e->getLine(),
        'step' => 0,
        'log_file' => basename($log->getLogFile()),
    ]);
    exit();
});

// Capturar erros fatais
register_shutdown_function(function() use ($log) {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $log->error("ERRO FATAL", [
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line'],
        ]);
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Erro fatal: ' . $error['message'] . ' em ' . basename($error['file']) . ':' . $error['line'],
                'step' => 0,
                'log_file' => basename($log->getLogFile()),
            ]);
        }
    }
});

header('Content-Type: application/json');

// ============================================
// INÍCIO - Registrar ambiente
// ============================================
$log->info("=== INÍCIO DA INSTALAÇÃO ===");
$log->info("Session ID: " . $log->getSessionId());
$log->logServerEnvironment();
$log->logFilePermissions([
    'install_dir' => __DIR__,
    'application_dir' => realpath(__DIR__ . '/..') . '/application',
    'env_example' => realpath(__DIR__ . '/..') . '/application/.env.example',
    'database_sql' => realpath(__DIR__ . '/..') . '/database/sql/banco.sql',
    'updates_dir' => realpath(__DIR__ . '/..') . '/updates',
]);

// ============================================
// ETAPA 0 - Verificar settings.json e POST
// ============================================
$log->stepStart(0, 'Verificação Inicial');

$log->info("Verificando settings.json");
$settings_file = __DIR__ . DIRECTORY_SEPARATOR . 'settings.json';

if (! file_exists($settings_file)) {
    $log->error("settings.json não encontrado", ['path' => $settings_file]);
    echo json_encode([
        'success' => false,
        'message' => 'Arquivo de configuração não encontrado! Crie o arquivo install/settings.json',
        'step' => 0,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(0, false);
    exit();
}

$contents = file_get_contents($settings_file);
$settings = json_decode($contents, true);
if ($settings === null) {
    $log->error("settings.json corrompido", [
        'json_error' => json_last_error_msg(),
        'content_preview' => mb_substr($contents, 0, 200),
    ]);
    echo json_encode([
        'success' => false,
        'message' => 'Arquivo settings.json está corrompido! Erro: ' . json_last_error_msg(),
        'step' => 0,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(0, false);
    exit();
}

$log->debug("settings.json carregado", $settings);

if (empty($_POST)) {
    $log->warn("Requisição sem dados POST");
    echo json_encode([
        'success' => false,
        'message' => 'Requisição inválida. Este endpoint deve ser chamado via POST com os dados de instalação.',
        'step' => 0,
    ]);
    $log->stepEnd(0, false);
    exit();
}

$log->logPostData($_POST);
$log->stepEnd(0, true);

// ============================================
// ETAPA 1 - Validação dos campos
// ============================================
$log->stepStart(1, 'Validação dos Campos');

$host = $_POST['host'] ?? '';
$dbuser = $_POST['dbuser'] ?? '';
$dbpassword = $_POST['dbpassword'] ?? '';
$dbname = $_POST['dbname'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$login_password = $_POST['password'] ?? '';
$base_url = $_POST['base_url'] ?? '';
$api_enabled = $_POST['enter_api_enabled'] ?? 'true';
$token_expire_time = $_POST['enter_token_expire_time'] ?? '3600';

$log->debug("Campos recebidos", [
    'host' => $host ?: '(vazio)',
    'dbuser' => $dbuser ?: '(vazio)',
    'dbpassword' => $dbpassword !== '' ? '********' : '(vazio)',
    'dbname' => $dbname ?: '(vazio)',
    'full_name' => $full_name ?: '(vazio)',
    'email' => $email ?: '(vazio)',
    'login_password' => $login_password !== '' ? '********' : '(vazio)',
    'base_url' => $base_url ?: '(vazio)',
    'api_enabled' => $api_enabled,
    'token_expire_time' => $token_expire_time,
]);

// Validar campos obrigatórios (dbpassword pode ser vazio)
$missingFields = [];
if (empty($host)) $missingFields[] = 'host';
if (empty($dbuser)) $missingFields[] = 'dbuser';
if (empty($dbname)) $missingFields[] = 'dbname';
if (empty($full_name)) $missingFields[] = 'full_name';
if (empty($email)) $missingFields[] = 'email';
if (empty($login_password)) $missingFields[] = 'password';
if (empty($base_url)) $missingFields[] = 'base_url';

if (!empty($missingFields)) {
    $log->error("Campos obrigatórios faltando", ['campos_faltando' => $missingFields]);
    echo json_encode([
        'success' => false,
        'message' => 'Por favor insira todos os campos. Faltando: ' . implode(', ', $missingFields),
        'step' => 1,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(1, false);
    exit();
}

// Validar email
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $log->error("Email inválido", ['email' => $email]);
    echo json_encode([
        'success' => false,
        'message' => 'Por favor insira um email válido.',
        'step' => 1,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(1, false);
    exit();
}

// Validar URL base
if (!filter_var($base_url, FILTER_VALIDATE_URL) && !preg_match('#^https?://.+#', $base_url)) {
    $log->warn("URL base pode ser inválida", ['base_url' => $base_url]);
}

$log->info("Todos os campos validados com sucesso");
$log->stepEnd(1, true);

// ============================================
// ETAPA 2 - Conexão com o Banco de Dados
// ============================================
$log->stepStart(2, 'Conexão com o Banco de Dados');

$log->info("Tentando conectar ao MySQL", ['host' => $host, 'dbname' => $dbname, 'dbuser' => $dbuser]);

try {
    $mysqli = @new mysqli($host, $dbuser, $dbpassword, $dbname);

    if (mysqli_connect_errno()) {
        $connectError = $mysqli->connect_error;
        $log->error("Falha na conexão MySQL", [
            'errno' => mysqli_connect_errno(),
            'error' => $connectError,
            'host' => $host,
            'dbname' => $dbname,
            'dbuser' => $dbuser,
        ]);
        echo json_encode([
            'success' => false,
            'message' => 'Erro de conexão com o banco de dados: ' . $connectError,
            'step' => 2,
            'log_file' => basename($log->getLogFile()),
        ]);
        $log->stepEnd(2, false);
        exit();
    }

    $log->logMySqlConnection($mysqli, $host, $dbname);
    $log->info("Conexão MySQL estabelecida com sucesso");

} catch (Exception $e) {
    $log->error("Exceção na conexão MySQL", [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
    ]);
    echo json_encode([
        'success' => false,
        'message' => 'Exceção na conexão: ' . $e->getMessage(),
        'step' => 2,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(2, false);
    exit();
}

// Verificar arquivo SQL
$log->debug("Verificando arquivo banco.sql", ['path' => $settings['database_file']]);
if (! is_file($settings['database_file'])) {
    $log->error("banco.sql não encontrado", ['path' => $settings['database_file']]);
    echo json_encode([
        'success' => false,
        'message' => 'O arquivo banco.sql não foi encontrado! Caminho esperado: ' . $settings['database_file'],
        'step' => 2,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(2, false);
    exit();
}

$sqlFileSize = filesize($settings['database_file']);
$log->info("Arquivo banco.sql encontrado", ['size_bytes' => $sqlFileSize, 'size_kb' => round($sqlFileSize / 1024, 1)]);

// Verificar se já está instalado
$is_installed = file_exists('..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env');
if ($is_installed) {
    $log->error("Sistema já está instalado (.env existe)");
    echo json_encode([
        'success' => false,
        'message' => 'Parece que este aplicativo já está instalado! Você não pode reinstalá-lo novamente.',
        'step' => 2,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(2, false);
    exit();
}

$log->stepEnd(2, true);

// ============================================
// ETAPA 3 - Execução do banco.sql
// ============================================
$log->stepStart(3, 'Criação das Tabelas Base (banco.sql)');

$log->info("Lendo arquivo banco.sql");
$sql = file_get_contents($settings['database_file']);

if ($sql === false || empty($sql)) {
    $log->error("Falha ao ler banco.sql", ['size' => $sqlFileSize]);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao ler o arquivo banco.sql.',
        'step' => 3,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(3, false);
    exit();
}

$log->debug("banco.sql lido", ['length' => mb_strlen($sql), 'size_kb' => round($sqlFileSize / 1024, 1)]);

// Substituir placeholders do admin
$now = date('Y-m-d H:i:s');
$adminPasswordHash = password_hash($login_password, PASSWORD_DEFAULT);

$replacements = [
    'admin_name' => $full_name,
    'admin_email' => $email,
    'admin_password' => $adminPasswordHash,
    'admin_created_at' => $now,
];

$log->info("Substituindo placeholders no SQL", [
    'admin_name' => $full_name,
    'admin_email' => $email,
    'admin_password' => '(hash gerado)',
    'admin_created_at' => $now,
]);

foreach ($replacements as $placeholder => $value) {
    $count = 0;
    $sql = str_replace($placeholder, $value, $sql, $count);
    $log->debug("Placeholder '{$placeholder}' substituído", ['ocorrências' => $count]);
}

// Verificar hash da senha
if (!password_verify($login_password, $adminPasswordHash)) {
    $log->error("Falha na verificação do hash de senha do admin");
} else {
    $log->debug("Hash de senha do admin verificado com sucesso");
}

// Executar banco.sql via multi_query
$log->info("Executando banco.sql via multi_query");

if (!$mysqli->multi_query($sql)) {
    $log->error("Falha no multi_query inicial", [
        'errno' => $mysqli->errno,
        'error' => $mysqli->error,
    ]);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao executar banco.sql: ' . $mysqli->error,
        'step' => 3,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(3, false);
    exit();
}

// Processar todas as queries e registrar erros individuais
$query_num = 0;
$errors = [];
do {
    $query_num++;
    if ($mysqli->errno) {
        $errors[] = [
            'query_num' => $query_num,
            'errno' => $mysqli->errno,
            'error' => $mysqli->error,
        ];
        $log->warn("Erro na query #{$query_num}", ['errno' => $mysqli->errno, 'error' => $mysqli->error]);
    }
    if ($result = $mysqli->store_result()) {
        $result->free();
    }
} while ($mysqli->more_results() && $mysqli->next_result());

$log->debug("multi_query processado", ['total_queries' => $query_num, 'erros' => count($errors)]);

// Verificar se houve erro final
if ($mysqli->errno) {
    $log->error("Erro final após multi_query", ['errno' => $mysqli->errno, 'error' => $mysqli->error]);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao executar banco.sql: ' . $mysqli->error . ' (query #' . $query_num . ')',
        'step' => 3,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(3, false);
    exit();
}

// Listar tabelas criadas
$tables_result = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $tables_result->fetch_array(MYSQLI_NUM)) {
    $tables[] = $row[0];
}
$log->logMultiQueryResult($query_num, $errors, $tables);

$expected_tables = ['ci_sessions', 'clientes', 'usuarios', 'permissoes', 'os', 'produtos', 'servicos', 'lancamentos'];
$missing_tables = array_diff($expected_tables, $tables);
if (!empty($missing_tables)) {
    $log->error("Tabelas esperadas não foram criadas", ['faltando' => array_values($missing_tables)]);
}

$log->stepEnd(3, true);

// ============================================
// ETAPA 4 - Tabelas Adicionais V5
// ============================================
$log->stepStart(4, 'Criação das Tabelas Adicionais V5');

$mysqli->query("SET FOREIGN_KEY_CHECKS = 0");
$log->debug("FOREIGN_KEY_CHECKS desativado temporariamente");

// Verificar tabela 'os' existe (necessária para FKs)
$result = $mysqli->query("SHOW TABLES LIKE 'os'");
if ($result->num_rows == 0) {
    $log->error("Tabela 'os' não existe após banco.sql");
    echo json_encode([
        'success' => false,
        'message' => 'Erro crítico: Tabela os não foi criada pelo banco.sql',
        'step' => 4,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(4, false);
    exit();
}
$result->free();
$log->debug("Tabela 'os' confirmada");

// Verificar coluna idOs
$result = $mysqli->query("SHOW COLUMNS FROM `os` LIKE 'idOs'");
if ($result->num_rows == 0) {
    $log->error("Coluna idOs não existe na tabela os");
    echo json_encode([
        'success' => false,
        'message' => 'Erro: Coluna idOs não existe na tabela os',
        'step' => 4,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(4, false);
    exit();
}
$col_info = $result->fetch_assoc();
$log->debug("Coluna idOs verificada", ['tipo' => $col_info['Type']]);
$result->free();

// --- Tabela: dre_config ---
$log->debug("Criando tabela dre_config");
$mysqli->query("CREATE TABLE IF NOT EXISTS `dre_config` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tipo` ENUM('MAPEAMENTO_OS', 'MAPEAMENTO_VENDA', 'MAPEAMENTO_LANCAMENTO', 'CONFIG') NOT NULL,
  `origem_tabela` VARCHAR(50) NULL,
  `origem_campo` VARCHAR(50) NULL,
  `conta_dre_id` INT(11) UNSIGNED NOT NULL,
  `condicao` TEXT NULL,
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NULL,
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_conta_dre_id` (`conta_dre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$log->logQueryResult('CREATE dre_config', !$mysqli->errno, null, $mysqli->errno ? $mysqli->error : null);

// --- Tabela: config_sistema_impostos ---
$log->debug("Criando tabela config_sistema_impostos");
$mysqli->query("CREATE TABLE IF NOT EXISTS `config_sistema_impostos` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `chave` VARCHAR(100) NOT NULL UNIQUE,
  `valor` TEXT NULL,
  `descricao` VARCHAR(255) NULL,
  `updated_at` DATETIME NULL,
  INDEX `idx_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$log->logQueryResult('CREATE config_sistema_impostos', !$mysqli->errno, null, $mysqli->errno ? $mysqli->error : null);

// --- Tabela: certificado_digital ---
$log->debug("Criando tabela certificado_digital");
$mysqli->query("CREATE TABLE IF NOT EXISTS `certificado_digital` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tipo` ENUM('A1', 'A3') DEFAULT 'A1',
  `cnpj` VARCHAR(14) NOT NULL,
  `razao_social` VARCHAR(255) NULL,
  `nome_fantasia` VARCHAR(255) NULL,
  `arquivo_caminho` VARCHAR(500) NULL,
  `arquivo_hash` VARCHAR(255) NULL,
  `senha` TEXT NULL,
  `data_validade` DATETIME NULL,
  `data_emissao` DATETIME NULL,
  `emissor` VARCHAR(100) NULL,
  `serial_number` VARCHAR(100) NULL,
  `ativo` TINYINT(1) DEFAULT 0,
  `ultimo_acesso` DATETIME NULL,
  `ultimo_erro` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  INDEX `idx_cnpj` (`cnpj`),
  INDEX `idx_ativo` (`ativo`),
  INDEX `idx_data_validade` (`data_validade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$log->logQueryResult('CREATE certificado_digital', !$mysqli->errno, null, $mysqli->errno ? $mysqli->error : null);

// --- Tabela: certificado_consultas ---
$log->debug("Criando tabela certificado_consultas");
$mysqli->query("CREATE TABLE IF NOT EXISTS `certificado_consultas` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `certificado_id` INT(11) UNSIGNED NOT NULL,
  `tipo_consulta` ENUM('CNPJ', 'SIMPLES_NACIONAL', 'NFE', 'NFSE', 'SITUACAO_CADASTRO') NOT NULL,
  `data_consulta` DATETIME NOT NULL,
  `sucesso` TINYINT(1) DEFAULT 0,
  `dados_retorno` LONGTEXT NULL,
  `erro` TEXT NULL,
  INDEX `idx_certificado_id` (`certificado_id`),
  INDEX `idx_tipo_consulta` (`tipo_consulta`),
  INDEX `idx_data_consulta` (`data_consulta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$log->logQueryResult('CREATE certificado_consultas', !$mysqli->errno, null, $mysqli->errno ? $mysqli->error : null);

// --- Tabela: certificado_nfe_importada ---
$log->debug("Criando tabela certificado_nfe_importada");
$mysqli->query("CREATE TABLE IF NOT EXISTS `certificado_nfe_importada` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `certificado_id` INT(11) UNSIGNED NOT NULL,
  `chave_acesso` VARCHAR(44) NOT NULL,
  `numero` VARCHAR(20) NULL,
  `serie` VARCHAR(10) NULL,
  `data_emissao` DATETIME NULL,
  `data_importacao` DATETIME NULL,
  `cnpj_destinatario` VARCHAR(14) NULL,
  `valor_total` DECIMAL(15,2) NULL,
  `valor_impostos` DECIMAL(15,2) NULL,
  `xml_path` VARCHAR(500) NULL,
  `situacao` ENUM('Autorizada', 'Cancelada', 'Denegada', 'Inutilizada') DEFAULT 'Autorizada',
  `imposto_integrado` TINYINT(1) DEFAULT 0,
  `dados_xml` LONGTEXT NULL,
  INDEX `idx_certificado_id` (`certificado_id`),
  INDEX `idx_chave_acesso` (`chave_acesso`),
  INDEX `idx_cnpj_destinatario` (`cnpj_destinatario`),
  INDEX `idx_situacao` (`situacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$log->logQueryResult('CREATE certificado_nfe_importada', !$mysqli->errno, null, $mysqli->errno ? $mysqli->error : null);

// NOTA: As tabelas os_tecnico_atribuicao, os_nfse_emitida e os_boleto_emitido
// já são criadas pelo banco.sql, não precisam ser recriadas aqui.
$log->debug("Tabelas os_tecnico_atribuicao, os_nfse_emitida e os_boleto_emitido já criadas pelo banco.sql");

$mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
$log->debug("FOREIGN_KEY_CHECKS reativado");

// Listar todas as tabelas após as adições
$tables_result2 = $mysqli->query("SHOW TABLES");
$tables2 = [];
while ($row = $tables_result2->fetch_array(MYSQLI_NUM)) {
    $tables2[] = $row[0];
}
$log->info("Tabelas após etapa 4", ['total' => count($tables2), 'novas' => array_diff($tables2, $tables)]);

$log->stepEnd(4, true);

// ============================================
// ETAPA 5 - Inserção de Dados DRE
// ============================================
$log->stepStart(5, 'Inserção de Dados Iniciais DRE');

// Verificar se dre_contas já tem dados (inseridos pelo banco.sql)
$result = $mysqli->query("SELECT COUNT(*) as total FROM dre_contas");
$row = $result->fetch_assoc();
$log->debug("Registros em dre_contas", ['total' => $row['total']]);
$result->free();

// Verificar se impostos_config já tem dados
$result = $mysqli->query("SELECT COUNT(*) as total FROM impostos_config");
$row = $result->fetch_assoc();
$log->debug("Registros em impostos_config", ['total' => $row['total']]);
$result->free();

$log->stepEnd(5, true);

// ============================================
// ETAPA 6 - Configuração de Impostos
// ============================================
$log->stepStart(6, 'Configuração de Impostos');

$result = $mysqli->query("SELECT COUNT(*) as total FROM config_sistema_impostos WHERE chave = 'IMPOSTO_ANEXO_PADRAO'");
$row = $result->fetch_assoc();
$log->debug("Verificando config_sistema_impostos", ['total_existente' => $row['total']]);
$result->free();

if ($row['total'] == 0) {
    $log->info("Inserindo configurações de impostos padrão");
    $configs = [
        ['IMPOSTO_ANEXO_PADRAO', 'III', 'Anexo do Simples Nacional padrão para a empresa'],
        ['IMPOSTO_FAIXA_ATUAL', '1', 'Faixa de faturamento atual (1-5)'],
        ['IMPOSTO_RETENCAO_AUTOMATICA', '1', 'Habilitar retenção automática em novos boletos (1=Sim, 0=Não)'],
        ['IMPOSTO_DRE_INTEGRACAO', '1', 'Integrar retenções automaticamente com DRE (1=Sim, 0=Não)'],
        ['IMPOSTO_ISS_MUNICIPAL', '5.00', 'Alíquota de ISS municipal para cálculo isolado (%)'],
        ['IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL', '010701', 'Código de Tributação Nacional (LC 116/2003)'],
        ['IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL', '100', 'Código de Tributação Municipal'],
        ['IMPOSTO_DESCRICAO_SERVICO', 'Suporte técnico em informática, inclusive instalação, configuração e manutenção de programas de computação e bancos de dados.', 'Descrição do serviço para NFS-e'],
    ];

    $stmt = $mysqli->prepare("INSERT INTO config_sistema_impostos (chave, valor, descricao) VALUES (?, ?, ?)");
    if (!$stmt) {
        $log->error("Falha ao preparar INSERT config_sistema_impostos", ['error' => $mysqli->error]);
    } else {
        $inserted = 0;
        foreach ($configs as $c) {
            $stmt->bind_param("sss", $c[0], $c[1], $c[2]);
            if ($stmt->execute()) {
                $inserted++;
            } else {
                $log->warn("Falha ao inserir config", ['chave' => $c[0], 'error' => $stmt->error]);
            }
        }
        $stmt->close();
        $log->info("Configurações de impostos inseridas", ['total_inseridas' => $inserted]);
    }
} else {
    $log->info("Configurações de impostos já existem, pulando inserção");
}

$log->stepEnd(6, true);

// ============================================
// ETAPA 7 - Permissões do Administrador
// ============================================
$log->stepStart(7, 'Permissões do Administrador');

$permissoes_array = [
    'aCliente' => '1', 'eCliente' => '1', 'dCliente' => '1', 'vCliente' => '1',
    'aProduto' => '1', 'eProduto' => '1', 'dProduto' => '1', 'vProduto' => '1',
    'aServico' => '1', 'eServico' => '1', 'dServico' => '1', 'vServico' => '1',
    'aOs' => '1', 'eOs' => '1', 'dOs' => '1', 'vOs' => '1',
    'vBtnAtendimento' => '1', 'vTecnicoOS' => '1', 'eTecnicoCheckin' => '1',
    'eTecnicoCheckout' => '1', 'eTecnicoFotos' => '1', 'vTecnicoDashboard' => '1',
    'aVenda' => '1', 'eVenda' => '1', 'dVenda' => '1', 'vVenda' => '1',
    'aGarantia' => '1', 'eGarantia' => '1', 'dGarantia' => '1', 'vGarantia' => '1',
    'aLancamento' => '1', 'eLancamento' => '1', 'dLancamento' => '1', 'vLancamento' => '1',
    'aPagamento' => '1', 'ePagamento' => '1', 'dPagamento' => '1', 'vPagamento' => '1',
    'aArquivo' => '1', 'eArquivo' => '1', 'dArquivo' => '1', 'vArquivo' => '1',
    'categoria_d' => '1', 'categoria_v' => '1', 'categoria_a' => '1', 'categoria_e' => '1',
    'vCategoria' => '1',
    'aCobranca' => '1', 'eCobranca' => '1', 'dCobranca' => '1', 'vCobranca' => '1',
    'aConfiguracao' => '1', 'eConfiguracao' => '1', 'dConfiguracao' => '1', 'vConfiguracao' => '1',
    'aEmitente' => '1', 'eEmitente' => '1', 'dEmitente' => '1', 'vEmitente' => '1',
    'aPermissao' => '1', 'ePermissao' => '1', 'dPermissao' => '1', 'vPermissao' => '1',
    'aAuditoria' => '1', 'eAuditoria' => '1', 'dAuditoria' => '1', 'vAuditoria' => '1',
    'aEmail' => '1', 'eEmail' => '1', 'dEmail' => '1', 'vEmail' => '1',
    'rContas' => '1', 'rFinanceiro' => '1', 'rProdutos' => '1', 'rServicos' => '1',
    'rVendas' => '1', 'rOs' => '1', 'rClientes' => '1', 'rCliente' => '1',
    'rProduto' => '1', 'rServico' => '1',
    'cUsuario' => '1', 'cEmitente' => '1', 'cPermissao' => '1', 'cBackup' => '1',
    'cAuditoria' => '1', 'cEmail' => '1', 'cSistema' => '1', 'cDocOs' => '1',
    // Permissões Certificado Digital
    'vCertificado' => '1', 'cCertificado' => '1', 'eCertificado' => '1', 'dCertificado' => '1',
    // Permissões Impostos Simples Nacional
    'vImpostos' => '1', 'cImpostosConfig' => '1', 'eImpostos' => '1',
    'vImpostosRelatorio' => '1', 'vImpostosExportar' => '1',
    // Permissões DRE
    'vDRE' => '1', 'vDRERelatorio' => '1', 'cDREConta' => '1', 'dDREConta' => '1',
    'cDRELancamento' => '1', 'dDRELancamento' => '1', 'vDRELancamento' => '1',
    'cDREIntegracao' => '1', 'vDREExportar' => '1', 'vDREAnalise' => '1',
    'vWebhooks' => '1',
    // Relatório de Atendimentos
    'vRelatorioAtendimentos' => '1',
    'vUsuariosCliente' => '1', 'cUsuariosCliente' => '1',
    'eUsuariosCliente' => '1', 'dUsuariosCliente' => '1',
    'cPermUsuariosCliente' => '1',
    'vDashboard' => '1', 'vRelatorioCompleto' => '1', 'vExportarDados' => '1',
    // Permissões Relatório de Técnicos
    'vRelatorioTecnicos' => '1',
    // Permissões NFSe e Boletos vinculados à OS
    'vNFSe' => '1', 'cNFSe' => '1', 'eNFSe' => '1',
    'vBoletoOS' => '1', 'cBoletoOS' => '1', 'eBoletoOS' => '1',
    'rNFSe' => '1'
];

$permissoes_serializado = serialize($permissoes_array);
$data_atual = date('Y-m-d');

$log->debug("Inserindo permissões do administrador", [
    'total_permissoes' => count($permissoes_array),
    'serialized_length' => mb_strlen($permissoes_serializado),
]);

$stmt = $mysqli->prepare("INSERT INTO permissoes (idPermissao, nome, permissoes, situacao, data) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE permissoes = ?");
if (!$stmt) {
    $log->error("Falha ao preparar INSERT permissoes", ['error' => $mysqli->error]);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao criar permissões: ' . $mysqli->error,
        'step' => 7,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(7, false);
    exit();
}

$idPermissao = 1;
$nome = 'Administrador';
$situacao = 1;
$stmt->bind_param("ississ", $idPermissao, $nome, $permissoes_serializado, $situacao, $data_atual, $permissoes_serializado);

if (!$stmt->execute()) {
    $log->error("Falha ao inserir permissões", ['error' => $stmt->error]);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao inserir permissões: ' . $stmt->error,
        'step' => 7,
        'log_file' => basename($log->getLogFile()),
    ]);
    $stmt->close();
    $log->stepEnd(7, false);
    exit();
}

$affectedRows = $stmt->affected_rows;
$log->info("Permissões inseridas/atualizadas", ['affected_rows' => $affectedRows]);
$stmt->close();

// Verificar se o usuário admin foi criado
$result = $mysqli->query("SELECT idUsuarios, nome, email FROM usuarios WHERE idUsuarios = 1");
if ($result && $row = $result->fetch_assoc()) {
    $log->info("Usuário admin verificado", ['id' => $row['idUsuarios'], 'nome' => $row['nome'], 'email' => $row['email']]);
} else {
    $log->warn("Usuário admin não encontrado após banco.sql", ['error' => $mysqli->error]);
}

$log->info("Fechando conexão com banco de dados");
$mysqli->close();

$log->stepEnd(7, true);

// ============================================
// ETAPA 8 - Criação do arquivo .env
// ============================================
$log->stepStart(8, 'Criação do Arquivo .env');

$env_file_path = '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env.example';
$env_output_path = '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env';

$log->logFilePermissions([
    'env_example' => $env_file_path,
    'env_output_dir' => dirname($env_output_path),
]);

if (!file_exists($env_file_path)) {
    $log->error(".env.example não encontrado", ['path' => $env_file_path]);
    echo json_encode([
        'success' => false,
        'message' => 'Arquivo .env.example não encontrado.',
        'step' => 8,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(8, false);
    exit();
}

if (!is_readable($env_file_path)) {
    $log->error(".env.example não pode ser lido", ['path' => $env_file_path]);
    echo json_encode([
        'success' => false,
        'message' => 'Arquivo .env.example não pode ser lido.',
        'step' => 8,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(8, false);
    exit();
}

$env_file = file_get_contents($env_file_path);
if ($env_file === false) {
    $log->error("Falha ao ler .env.example");
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao ler o arquivo .env.example',
        'step' => 8,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(8, false);
    exit();
}

$log->debug(".env.example lido", ['size_bytes' => mb_strlen($env_file)]);

$env_dir = dirname($env_output_path);
if (!is_writable($env_dir)) {
    $log->error("Diretório application/ não é gravável", ['path' => $env_dir]);
    echo json_encode([
        'success' => false,
        'message' => 'Diretório application/ não é gravável. Permissões: ' . substr(sprintf('%o', @fileperms($env_dir)), -4),
        'step' => 8,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(8, false);
    exit();
}

// Gerar chave de criptografia
$encryption_key = substr(md5(rand()), 0, 15);
$log->debug("Chave de criptografia gerada");

// Gerar chave JWT
if (function_exists('openssl_random_pseudo_bytes')) {
    $jwt_key = base64_encode(openssl_random_pseudo_bytes(32));
    $log->debug("Chave JWT gerada via OpenSSL");
} else {
    $jwt_key = base64_encode(md5(uniqid(mt_rand(), true)) . md5(uniqid(mt_rand(), true)));
    $log->warn("Chave JWT gerada via fallback (md5) - OpenSSL não disponível");
}

// Realizar substituições no .env
$envReplacements = [
    'enter_db_hostname' => $host,
    'enter_db_username' => $dbuser,
    'enter_db_password' => $dbpassword,
    'enter_db_name' => $dbname,
    'enter_encryption_key' => $encryption_key,
    'enter_baseurl' => $base_url,
    'enter_jwt_key' => $jwt_key,
    'enter_token_expire_time' => $token_expire_time,
    'enter_api_enabled' => $api_enabled,
    'pre_installation' => 'production',
];

$log->info("Substituindo placeholders no .env", ['replacements' => array_keys($envReplacements)]);

foreach ($envReplacements as $placeholder => $value) {
    $count = 0;
    $env_file = str_replace($placeholder, $value, $env_file, $count);
    if ($count === 0 && $placeholder !== 'pre_installation') {
        $log->warn("Placeholder '{$placeholder}' não encontrado no .env.example");
    }
}

// Escrever .env
$log->info("Escrevendo arquivo .env", ['path' => $env_output_path]);
$result = file_put_contents($env_output_path, $env_file);

if ($result === false) {
    $log->error("Falha ao escrever .env", [
        'path' => $env_output_path,
        'dir_writable' => is_writable($env_dir),
    ]);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao criar arquivo .env. Verifique as permissões do diretório application/.',
        'step' => 8,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(8, false);
    exit();
}

$log->logEnvCreation($env_output_path, $result, $envReplacements);

if (!file_exists($env_output_path) || filesize($env_output_path) === 0) {
    $log->error(".env criado mas está vazio ou inacessível", [
        'exists' => file_exists($env_output_path),
        'size' => file_exists($env_output_path) ? filesize($env_output_path) : 0,
    ]);
    echo json_encode([
        'success' => false,
        'message' => 'Arquivo .env foi criado mas está vazio.',
        'step' => 8,
        'log_file' => basename($log->getLogFile()),
    ]);
    $log->stepEnd(8, false);
    exit();
}

// Verificar substituições críticas no .env gerado
$envCheck = file_get_contents($env_output_path);
$missingPlaceholders = [];
foreach (['enter_db_hostname', 'enter_db_username', 'enter_db_name', 'enter_baseurl', 'pre_installation'] as $check) {
    if (strpos($envCheck, $check) !== false) {
        $missingPlaceholders[] = $check;
    }
}
if (!empty($missingPlaceholders)) {
    $log->warn("Placeholders não substituídos permaneceram no .env", ['placeholders' => $missingPlaceholders]);
}

// Verificar se APP_ENVIRONMENT foi alterado para production
if (strpos($envCheck, 'APP_ENVIRONMENT="production"') === false) {
    $log->error("APP_ENVIRONMENT não foi alterado para production no .env");
} else {
    $log->debug("APP_ENVIRONMENT = production verificado no .env");
}

$log->stepEnd(8, true);

// ============================================
// RESULTADO FINAL
// ============================================
$log->logFinalResult(true, 'Instalação concluída com sucesso!', [
    'total_tabelas' => count($tables2),
    'base_url' => $base_url,
]);

echo json_encode([
    'success' => true,
    'message' => 'Instalação bem sucedida!',
    'percent' => 100,
    'step' => 8,
    'log_file' => basename($log->getLogFile()),
]);
exit();