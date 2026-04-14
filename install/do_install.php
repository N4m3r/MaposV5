<?php

// Garantir que erros não quebrem o JSON de resposta
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/install_error.log');
ini_set('max_execution_time', 300);

// Função para log de debug
function install_log($message, $data = null) {
    $log_file = __DIR__ . '/install_debug.log';
    $line = date('Y-m-d H:i:s') . ' - ' . $message;
    if ($data !== null) {
        $line .= ': ' . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    file_put_contents($log_file, $line . "\n", FILE_APPEND);
}

// Capturar todos os erros e exceções
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    install_log("ERRO [$errno]", ['msg' => $errstr, 'file' => $errfile, 'line' => $errline]);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erro [' . $errno . ']: ' . $errstr . ' em ' . $errfile . ':' . $errline,
        'step' => 0
    ]);
    exit();
});

set_exception_handler(function($e) {
    install_log("EXCEÇÃO", ['msg' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Exceção: ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine(),
        'step' => 0
    ]);
    exit();
});

// Capturar erros fatais
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        install_log("ERRO FATAL", $error);
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erro fatal: ' . $error['message'] . ' em ' . $error['file'] . ':' . $error['line'],
            'step' => 0
        ]);
        exit();
    }
});

header('Content-Type: application/json');

install_log("Início da requisição", ['method' => $_SERVER['REQUEST_METHOD'], 'post_keys' => array_keys($_POST)]);

install_log("Verificando settings.json");
$settings_file = __DIR__ . DIRECTORY_SEPARATOR . 'settings.json';

if (! file_exists($settings_file)) {
    echo json_encode([
        'success' => false,
        'message' => 'Arquivo de configuração não encontrado! Crie o arquivo install/settings.json',
        'step' => 0,
        'debug' => ['arquivo_esperado' => $settings_file]
    ]);
    exit();
} else {
    $contents = file_get_contents($settings_file);
    $settings = json_decode($contents, true);
    if ($settings === null) {
        echo json_encode([
            'success' => false,
            'message' => 'Arquivo settings.json está corrompido! Erro: ' . json_last_error_msg(),
            'step' => 0
        ]);
        exit();
    }
}

// Se não houver POST, retornar erro informativo (não deixar saída vazia)
if (empty($_POST)) {
    echo json_encode([
        'success' => false,
        'message' => 'Requisição inválida. Este endpoint deve ser chamado via POST com os dados de instalação.',
        'step' => 0
    ]);
    exit();
}

install_log("Recebendo dados POST", ['host' => $_POST['host'] ?? 'não definido', 'dbname' => $_POST['dbname'] ?? 'não definido']);

$host = $_POST['host'];
$dbuser = $_POST['dbuser'];
$dbpassword = $_POST['dbpassword'];
$dbname = $_POST['dbname'];

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $login_password = $_POST['password'] ? $_POST['password'] : '';
    $base_url = $_POST['base_url'];

    install_log("Validando campos obrigatórios");
    //check required fields
    if (! ($host && $dbuser && $dbname && $full_name && $email && $login_password && $base_url)) {
        echo json_encode(['success' => false, 'message' => 'Por favor insira todos os campos.', 'step' => 1]);
        exit();
    }

    //check for valid email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        echo json_encode(['success' => false, 'message' => 'Por favor insira um email válido.', 'step' => 1]);
        exit();
    }

    //check for valid database connection
    try {
        install_log("Conectando ao banco de dados", ['host' => $host, 'dbname' => $dbname]);
        $mysqli = @new mysqli($host, $dbuser, $dbpassword, $dbname);

        if (mysqli_connect_errno()) {
            echo json_encode(['success' => false, 'message' => $mysqli->connect_error, 'step' => 2]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage(), 'step' => 2]);
        exit();
    }

    //check required files
    if (! is_file($settings['database_file'])) {
        echo json_encode(['success' => false, 'message' => 'O arquivo ../banco.sql não foi encontrado na pasta de instalação!', 'step' => 2]);
        exit();
    }

    /*
     * check the db config file
     * if db already configured, we'll assume that the installation has completed
     */
    $is_installed = file_exists('..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env');

    if ($is_installed) {
        echo json_encode(['success' => false, 'message' => 'Parece que este aplicativo já está instalado! Você não pode reinstalá-lo novamente.', 'step' => 2]);
        exit();
    }

    // ============================================
    // Criação das Tabelas Base (via banco.sql)
    // ============================================
    $sql = file_get_contents($settings['database_file']);

    //set admin information to database
    $now = date('Y-m-d H:i:s');
    $sql = str_replace('admin_name', $full_name, $sql);
    $sql = str_replace('admin_email', $email, $sql);
    $sql = str_replace('admin_password', password_hash($login_password, PASSWORD_DEFAULT), $sql);
    $sql = str_replace('admin_created_at', $now, $sql);

    //create tables in database
    install_log("Executando multi_query do banco.sql");
    $mysqli->multi_query($sql);
    do {
    } while (mysqli_more_results($mysqli) && mysqli_next_result($mysqli));
    install_log("banco.sql executado com sucesso");

    // ============================================
    // Criar Tabelas Adicionais (que não estão no banco.sql)
    // ============================================
    $mysqli->query("SET FOREIGN_KEY_CHECKS = 0");

    // Tabela: dre_config (tabela de configuração do DRE)
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tabela: config_sistema_impostos
    $mysqli->query("CREATE TABLE IF NOT EXISTS `config_sistema_impostos` (
      `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `chave` VARCHAR(100) NOT NULL UNIQUE,
      `valor` TEXT NULL,
      `descricao` VARCHAR(255) NULL,
      `updated_at` DATETIME NULL,
      INDEX `idx_chave` (`chave`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Inserir configurações de impostos padrão
    $result = $mysqli->query("SELECT COUNT(*) as total FROM config_sistema_impostos WHERE chave = 'IMPOSTO_ANEXO_PADRAO'");
    $row = $result->fetch_assoc();
    if ($row['total'] == 0) {
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
        foreach ($configs as $c) {
            $stmt->bind_param("sss", $c[0], $c[1], $c[2]);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Tabela: certificado_digital
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tabela: certificado_consultas
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tabela: certificado_nfe_importada
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tabela: os_tecnico_atribuicao (histórico de atribuições de técnicos às OS)
    $mysqli->query("CREATE TABLE IF NOT EXISTS `os_tecnico_atribuicao` (
      `idAtribuicao` INT(11) NOT NULL AUTO_INCREMENT,
      `os_id` INT(11) NOT NULL,
      `tecnico_id` INT(11) NOT NULL COMMENT 'ID do tecnico atribuido',
      `atribuido_por` INT(11) NOT NULL COMMENT 'ID do usuario que fez a atribuicao',
      `data_atribuicao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `data_remocao` DATETIME NULL,
      `motivo_remocao` TEXT NULL,
      `observacao` TEXT NULL,
      PRIMARY KEY (`idAtribuicao`),
      INDEX `idx_os_id` (`os_id`),
      INDEX `idx_tecnico_id` (`tecnico_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Adicionar coluna tecnico_responsavel na tabela os se não existir
    $result = $mysqli->query("SHOW COLUMNS FROM `os` LIKE 'tecnico_responsavel'");
    if ($result->num_rows == 0) {
        $mysqli->query("ALTER TABLE `os` ADD COLUMN `tecnico_responsavel` INT(11) NULL COMMENT 'ID do usuario tecnico responsavel pela OS'");
        $mysqli->query("ALTER TABLE `os` ADD INDEX `idx_tecnico_responsavel` (`tecnico_responsavel`)");
    }

    // Adicionar colunas de NFSe e Boleto na tabela os se não existirem
    $result = $mysqli->query("SHOW COLUMNS FROM `os` LIKE 'nfse_status'");
    if ($result->num_rows == 0) {
        $mysqli->query("ALTER TABLE `os` ADD COLUMN `nfse_status` ENUM('Pendente', 'Emitida', 'Cancelada') NOT NULL DEFAULT 'Pendente' COMMENT 'Status da NFS-e vinculada' AFTER `status`");
        $mysqli->query("ALTER TABLE `os` ADD COLUMN `boleto_status` ENUM('Pendente', 'Emitido', 'Pago', 'Vencido', 'Cancelado') NOT NULL DEFAULT 'Pendente' COMMENT 'Status do boleto vinculado' AFTER `nfse_status`");
        $mysqli->query("ALTER TABLE `os` ADD COLUMN `data_vencimento_boleto` DATE NULL DEFAULT NULL COMMENT 'Data de vencimento do boleto' AFTER `boleto_status`");
        $mysqli->query("ALTER TABLE `os` ADD COLUMN `valor_com_impostos` DECIMAL(15, 2) NULL DEFAULT NULL COMMENT 'Valor liquido apos deducao de impostos' AFTER `valor_desconto`");
    }

    // Tabela: os_nfse_emitida (Notas fiscais de serviço emitidas)
    $mysqli->query("CREATE TABLE IF NOT EXISTS `os_nfse_emitida` (
      `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `os_id` INT(11) NOT NULL COMMENT 'ID da OS vinculada',
      `numero_nfse` VARCHAR(20) NULL COMMENT 'Número da NFS-e',
      `chave_acesso` VARCHAR(50) NULL,
      `data_emissao` DATETIME NULL,
      `valor_servicos` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_deducoes` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_liquido` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `aliquota_iss` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
      `valor_iss` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_inss` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_irrf` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_csll` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_pis` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_cofins` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_total_impostos` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `situacao` ENUM('Pendente', 'Emitida', 'Cancelada', 'Substituida') NOT NULL DEFAULT 'Pendente',
      `codigo_verificacao` VARCHAR(20) NULL,
      `link_impressao` VARCHAR(500) NULL,
      `xml_path` VARCHAR(500) NULL,
      `protocolo` VARCHAR(50) NULL,
      `mensagem_retorno` TEXT NULL,
      `cobranca_id` INT(11) NULL COMMENT 'ID da cobrança/boleto vinculado',
      `emitido_por` INT(11) NULL,
      `created_at` DATETIME NULL,
      `updated_at` DATETIME NULL,
      INDEX `idx_os_id` (`os_id`),
      INDEX `idx_numero_nfse` (`numero_nfse`),
      CONSTRAINT `fk_nfse_emitida_os`
        FOREIGN KEY (`os_id`)
        REFERENCES `os` (`idOs`)
        ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tabela: os_boleto_emitido (Boletos gerados para OS)
    $mysqli->query("CREATE TABLE IF NOT EXISTS `os_boleto_emitido` (
      `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `os_id` INT(11) NOT NULL,
      `nfse_id` INT(11) NULL COMMENT 'ID da NFS-e vinculada',
      `nosso_numero` VARCHAR(50) NULL,
      `linha_digitavel` VARCHAR(60) NULL,
      `codigo_barras` VARCHAR(44) NULL,
      `data_emissao` DATE NULL,
      `data_vencimento` DATE NULL,
      `data_pagamento` DATE NULL,
      `valor_original` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_desconto_impostos` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor descontado dos impostos (NFSe)',
      `valor_liquido` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `valor_pago` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `multa` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `juros` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
      `status` ENUM('Pendente', 'Emitido', 'Pago', 'Vencido', 'Cancelado') NOT NULL DEFAULT 'Pendente',
      `instrucoes` TEXT NULL,
      `sacado_nome` VARCHAR(255) NULL,
      `sacado_documento` VARCHAR(20) NULL,
      `sacado_endereco` VARCHAR(500) NULL,
      `pdf_path` VARCHAR(500) NULL,
      `remessa_id` INT(11) NULL,
      `retorno_id` INT(11) NULL,
      `gateway` VARCHAR(50) NULL COMMENT 'Gateway de pagamento usado',
      `gateway_transaction_id` VARCHAR(100) NULL,
      `created_at` DATETIME NULL,
      `updated_at` DATETIME NULL,
      INDEX `idx_os_id` (`os_id`),
      INDEX `idx_nfse_id` (`nfse_id`),
      INDEX `idx_status` (`status`),
      CONSTRAINT `fk_boleto_emitido_os`
        FOREIGN KEY (`os_id`)
        REFERENCES `os` (`idOs`)
        ON DELETE CASCADE,
      CONSTRAINT `fk_boleto_emitido_nfse`
        FOREIGN KEY (`nfse_id`)
        REFERENCES `os_nfse_emitida` (`id`)
        ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $mysqli->query("SET FOREIGN_KEY_CHECKS = 1");

    install_log("Criando permissões do administrador");
    // ============================================
    // Criar Permissões do Administrador
    // ============================================
    $permissoes_array = [
        'aCliente' => '1', 'eCliente' => '1', 'dCliente' => '1', 'vCliente' => '1',
        'aProduto' => '1', 'eProduto' => '1', 'dProduto' => '1', 'vProduto' => '1',
        'aServico' => '1', 'eServico' => '1', 'dServico' => '1', 'vServico' => '1',
        'aOs' => '1', 'eOs' => '1', 'dOs' => '1', 'vOs' => '1',
        'vBtnAtendimento' => '1', 'vTecnicoOS' => '1', 'eTecnicoCheckin' => '1',
        'eTecnicoCheckout' => '1', 'eTecnicoFotos' => '1',
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
        // Permissões NFSe e Boletos vinculados à OS
        'vNFSe' => '1', 'cNFSe' => '1', 'eNFSe' => '1',
        'vBoletoOS' => '1', 'cBoletoOS' => '1', 'eBoletoOS' => '1',
        'rNFSe' => '1'
    ];

    $permissoes_serializado = serialize($permissoes_array);
    $data_atual = date('Y-m-d');

    $stmt = $mysqli->prepare("INSERT INTO permissoes (idPermissao, nome, permissoes, situacao, data) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE permissoes = ?");
    $idPermissao = 1;
    $nome = 'Administrador';
    $situacao = 1;
    $stmt->bind_param("ississ", $idPermissao, $nome, $permissoes_serializado, $situacao, $data_atual, $permissoes_serializado);
    $stmt->execute();
    $stmt->close();

    install_log("Fechando conexão com banco");
    $mysqli->close();

    install_log("Criando arquivo .env");
    // ============================================
    // Criar Arquivo .env
    // ============================================
    $env_file_path = '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env.example';
    $env_output_path = '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env';

    if (!file_exists($env_file_path)) {
        echo json_encode(['success' => false, 'message' => 'Arquivo .env.example não encontrado.', 'step' => 8]);
        exit();
    }

    if (!is_readable($env_file_path)) {
        echo json_encode(['success' => false, 'message' => 'Arquivo .env.example não pode ser lido.', 'step' => 8]);
        exit();
    }

    $env_file = file_get_contents($env_file_path);

    if ($env_file === false) {
        echo json_encode(['success' => false, 'message' => 'Erro ao ler o arquivo .env.example', 'step' => 8]);
        exit();
    }

    $env_dir = dirname($env_output_path);
    if (!is_writable($env_dir)) {
        echo json_encode(['success' => false, 'message' => 'Diretório application/ não é gravável.', 'step' => 8]);
        exit();
    }

    $env_file = str_replace('enter_db_hostname', $host, $env_file);
    $env_file = str_replace('enter_db_username', $dbuser, $env_file);
    $env_file = str_replace('enter_db_password', $dbpassword, $env_file);
    $env_file = str_replace('enter_db_name', $dbname, $env_file);

    $encryption_key = substr(md5(rand()), 0, 15);
    $env_file = str_replace('enter_encryption_key', $encryption_key, $env_file);
    $env_file = str_replace('enter_baseurl', $base_url, $env_file);

    // Gerar chave JWT - usar openssl se disponível, senão fallback para mt_rand
    if (function_exists('openssl_random_pseudo_bytes')) {
        $jwt_key = base64_encode(openssl_random_pseudo_bytes(32));
    } else {
        $jwt_key = base64_encode(md5(uniqid(mt_rand(), true)) . md5(uniqid(mt_rand(), true)));
    }
    $env_file = str_replace('enter_jwt_key', $jwt_key, $env_file);
    $env_file = str_replace('enter_token_expire_time', '3600', $env_file);
    $env_file = str_replace('enter_api_enabled', 'true', $env_file);
    $env_file = str_replace('pre_installation', 'production', $env_file);

    $result = file_put_contents($env_output_path, $env_file);

    if ($result === false) {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar arquivo .env.', 'step' => 8]);
        exit();
    }

    if (!file_exists($env_output_path) || filesize($env_output_path) === 0) {
        echo json_encode(['success' => false, 'message' => 'Arquivo .env foi criado mas está vazio.', 'step' => 8]);
        exit();
    }

    install_log("Instalação concluída com sucesso");
    echo json_encode(['success' => true, 'message' => 'Instalação bem sucedida!', 'percent' => 100, 'step' => 8]);
    exit();
