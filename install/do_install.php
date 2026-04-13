<?php

ini_set('max_execution_time', 300); //300 seconds

$settings_file = __DIR__ . DIRECTORY_SEPARATOR . 'settings.json';

if (! file_exists($settings_file)) {
    exit('Arquivo de configuração não encontrado!');
} else {
    $contents = file_get_contents($settings_file);
    $settings = json_decode($contents, true);
}

if (! empty($_POST)) {
    $host = $_POST['host'];
    $dbuser = $_POST['dbuser'];
    $dbpassword = $_POST['dbpassword'];
    $dbname = $_POST['dbname'];

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $login_password = $_POST['password'] ? $_POST['password'] : '';
    $base_url = $_POST['base_url'];

    //check required fields
    if (! ($host && $dbuser && $dbname && $full_name && $email && $login_password && $base_url)) {
        echo json_encode(['success' => false, 'message' => 'Por favor insira todos os campos.']);
        exit();
    }

    //check for valid email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        echo json_encode(['success' => false, 'message' => 'Por favor insira um email válido.']);
        exit();
    }

    //check for valid database connection
    try {
        $mysqli = @new mysqli($host, $dbuser, $dbpassword, $dbname);

        if (mysqli_connect_errno()) {
            echo json_encode(['success' => false, 'message' => $mysqli->connect_error]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }

    //all input seems to be ok. check required fiels
    if (! is_file($settings['database_file'])) {
        echo json_encode(['success' => false, 'message' => 'O arquivo ../banco.sql não foi encontrado na pasta de instalação!']);
        exit();
    }

    /*
     * check the db config file
     * if db already configured, we'll assume that the installation has completed
     */
    $is_installed = file_exists('..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env');

    if ($is_installed) {
        echo json_encode(['success' => false, 'message' => 'Parece que este aplicativo já está instalado! Você não pode reinstalá-lo novamente.']);
        exit();
    }

    //start installation
    $sql = file_get_contents($settings['database_file']);

    //set admin information to database
    $now = date('Y-m-d H:i:s');
    $sql = str_replace('admin_name', $full_name, $sql);
    $sql = str_replace('admin_email', $email, $sql);
    $sql = str_replace('admin_password', password_hash($login_password, PASSWORD_DEFAULT), $sql);
    $sql = str_replace('admin_created_at', $now, $sql);

    //create tables in datbase
    $mysqli->multi_query($sql);
    do {
    } while (mysqli_more_results($mysqli) && mysqli_next_result($mysqli));

    // ============================================
    // CRIAR TABELAS ADICIONAIS V5 (DRE, IMPOSTOS, CERTIFICADO)
    // Executa após o banco base para garantir integridade
    // ============================================
    $mysqli->query("SET FOREIGN_KEY_CHECKS = 0");

    // Tabela: dre_contas (estrutura completa)
    $mysqli->query("CREATE TABLE IF NOT EXISTS `dre_contas` (
      `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `codigo` VARCHAR(20) NOT NULL,
      `nome` VARCHAR(255) NOT NULL,
      `tipo` ENUM('RECEITA', 'CUSTO', 'DESPESA', 'IMPOSTO', 'TRANSFERENCIA') NOT NULL,
      `grupo` ENUM('RECEITA_BRUTA', 'DEDUCOES', 'RECEITA_LIQUIDA', 'CUSTO', 'LUCRO_BRUTO', 'DESPESA_OPERACIONAL', 'LUCRO_OPERACIONAL', 'OUTRAS_RECEITAS', 'OUTRAS_DESPESAS', 'RESULTADO_FINANCEIRO', 'LUCRO_ANTES_IR', 'IMPOSTO_RENDA', 'LUCRO_LIQUIDO') NOT NULL,
      `ordem` INT(11) DEFAULT 0,
      `conta_pai_id` INT(11) UNSIGNED NULL,
      `nivel` INT(2) DEFAULT 1,
      `sinal` ENUM('POSITIVO', 'NEGATIVO') DEFAULT 'POSITIVO',
      `ativo` TINYINT(1) DEFAULT 1,
      `created_at` DATETIME NULL,
      `updated_at` DATETIME NULL,
      INDEX `idx_codigo` (`codigo`),
      INDEX `idx_tipo` (`tipo`),
      INDEX `idx_ativo` (`ativo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tabela: dre_lancamentos
    $mysqli->query("CREATE TABLE IF NOT EXISTS `dre_lancamentos` (
      `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `conta_id` INT(11) UNSIGNED NOT NULL,
      `data` DATE NOT NULL,
      `valor` DECIMAL(15,2) DEFAULT '0.00',
      `tipo_movimento` ENUM('DEBITO', 'CREDITO') NOT NULL,
      `descricao` TEXT NULL,
      `documento` VARCHAR(50) NULL,
      `os_id` INT(11) UNSIGNED NULL,
      `venda_id` INT(11) UNSIGNED NULL,
      `lancamento_id` INT(11) UNSIGNED NULL,
      `usuarios_id` INT(11) UNSIGNED NOT NULL,
      `created_at` DATETIME NULL,
      `updated_at` DATETIME NULL,
      INDEX `idx_conta_id` (`conta_id`),
      INDEX `idx_data` (`data`),
      INDEX `idx_os_id` (`os_id`),
      INDEX `idx_venda_id` (`venda_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tabela: dre_config
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

    // Tabela: impostos_config
    $mysqli->query("CREATE TABLE IF NOT EXISTS `impostos_config` (
      `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `anexo` ENUM('I', 'II', 'III', 'IV', 'V') NOT NULL,
      `faixa` INT(2) NOT NULL,
      `aliquota_nominal` DECIMAL(5,2) NOT NULL,
      `irpj` DECIMAL(5,2) DEFAULT '0.00',
      `csll` DECIMAL(5,2) DEFAULT '0.00',
      `cofins` DECIMAL(5,2) DEFAULT '0.00',
      `pis` DECIMAL(5,2) DEFAULT '0.00',
      `cpp` DECIMAL(5,2) DEFAULT '0.00',
      `iss` DECIMAL(5,2) DEFAULT '0.00',
      `outros` DECIMAL(5,2) DEFAULT '0.00',
      `atividade_principal` VARCHAR(255) NULL,
      `ativo` TINYINT(1) DEFAULT 1,
      `created_at` DATETIME NULL,
      INDEX `idx_anexo` (`anexo`),
      INDEX `idx_faixa` (`faixa`),
      INDEX `idx_ativo` (`ativo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tabela: impostos_retidos
    $mysqli->query("CREATE TABLE IF NOT EXISTS `impostos_retidos` (
      `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `cobranca_id` INT(11) UNSIGNED NULL,
      `os_id` INT(11) UNSIGNED NULL,
      `venda_id` INT(11) UNSIGNED NULL,
      `cliente_id` INT(11) UNSIGNED NOT NULL,
      `valor_bruto` DECIMAL(15,2) NOT NULL,
      `valor_liquido` DECIMAL(15,2) NOT NULL,
      `aliquota_aplicada` DECIMAL(5,2) NOT NULL,
      `irpj_valor` DECIMAL(15,2) DEFAULT '0.00',
      `csll_valor` DECIMAL(15,2) DEFAULT '0.00',
      `cofins_valor` DECIMAL(15,2) DEFAULT '0.00',
      `pis_valor` DECIMAL(15,2) DEFAULT '0.00',
      `iss_valor` DECIMAL(15,2) DEFAULT '0.00',
      `total_impostos` DECIMAL(15,2) DEFAULT '0.00',
      `data_competencia` DATE NOT NULL,
      `data_retencao` DATETIME NOT NULL,
      `nota_fiscal` VARCHAR(50) NULL,
      `status` ENUM('Retido', 'Recolhido', 'Estornado') DEFAULT 'Retido',
      `observacao` TEXT NULL,
      `usuarios_id` INT(11) UNSIGNED NOT NULL,
      `dre_lancamento_id` INT(11) UNSIGNED NULL,
      `created_at` DATETIME NULL,
      `updated_at` DATETIME NULL,
      INDEX `idx_cobranca_id` (`cobranca_id`),
      INDEX `idx_os_id` (`os_id`),
      INDEX `idx_venda_id` (`venda_id`),
      INDEX `idx_cliente_id` (`cliente_id`),
      INDEX `idx_data_competencia` (`data_competencia`),
      INDEX `idx_status` (`status`)
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

    // ============================================
    // INSERIR DADOS INICIAIS DRE
    // ============================================
    $result = $mysqli->query("SELECT COUNT(*) as total FROM dre_contas WHERE codigo = '1'");
    $row = $result->fetch_assoc();
    if ($row['total'] == 0) {
        $now = date('Y-m-d H:i:s');
        $contas_dre = [
            // RECEITA BRUTA
            ["1", "RECEITA BRUTA", "RECEITA", "RECEITA_BRUTA", 10, null, 1, "POSITIVO", 1, $now, $now],
            ["1.1", "Receita de Serviços", "RECEITA", "RECEITA_BRUTA", 11, null, 2, "POSITIVO", 1, $now, $now],
            ["1.2", "Receita de Vendas", "RECEITA", "RECEITA_BRUTA", 12, null, 2, "POSITIVO", 1, $now, $now],
            ["1.3", "Outras Receitas Operacionais", "RECEITA", "RECEITA_BRUTA", 13, null, 2, "POSITIVO", 1, $now, $now],
            // DEDUÇÕES
            ["2", "(-) DEDUÇÕES DA RECEITA", "IMPOSTO", "DEDUCOES", 20, null, 1, "NEGATIVO", 1, $now, $now],
            ["2.1", "Impostos Sobre Vendas", "IMPOSTO", "DEDUCOES", 21, null, 2, "NEGATIVO", 1, $now, $now],
            ["2.2", "Devoluções e Abatimentos", "RECEITA", "DEDUCOES", 22, null, 2, "NEGATIVO", 1, $now, $now],
            // RECEITA LÍQUIDA
            ["3", "= RECEITA LÍQUIDA", "TRANSFERENCIA", "RECEITA_LIQUIDA", 30, null, 1, "POSITIVO", 1, $now, $now],
            // CUSTOS
            ["4", "(-) CUSTO DOS SERVIÇOS/PRODUTOS", "CUSTO", "CUSTO", 40, null, 1, "NEGATIVO", 1, $now, $now],
            ["4.1", "Materiais Utilizados", "CUSTO", "CUSTO", 41, null, 2, "NEGATIVO", 1, $now, $now],
            ["4.2", "Mão de Obra Direta", "CUSTO", "CUSTO", 42, null, 2, "NEGATIVO", 1, $now, $now],
            ["4.3", "Custos Operacionais Diretos", "CUSTO", "CUSTO", 43, null, 2, "NEGATIVO", 1, $now, $now],
            // LUCRO BRUTO
            ["5", "= LUCRO BRUTO", "TRANSFERENCIA", "LUCRO_BRUTO", 50, null, 1, "POSITIVO", 1, $now, $now],
            // DESPESAS OPERACIONAIS
            ["6", "(-) DESPESAS OPERACIONAIS", "DESPESA", "DESPESA_OPERACIONAL", 60, null, 1, "NEGATIVO", 1, $now, $now],
            ["6.1", "Despesas Administrativas", "DESPESA", "DESPESA_OPERACIONAL", 61, null, 2, "NEGATIVO", 1, $now, $now],
            ["6.1.1", "Salários e Encargos Administrativos", "DESPESA", "DESPESA_OPERACIONAL", 611, null, 3, "NEGATIVO", 1, $now, $now],
            ["6.1.2", "Aluguel", "DESPESA", "DESPESA_OPERACIONAL", 612, null, 3, "NEGATIVO", 1, $now, $now],
            ["6.1.3", "Contas de Consumo", "DESPESA", "DESPESA_OPERACIONAL", 613, null, 3, "NEGATIVO", 1, $now, $now],
            ["6.1.4", "Material de Escritório", "DESPESA", "DESPESA_OPERACIONAL", 614, null, 3, "NEGATIVO", 1, $now, $now],
            ["6.1.5", "Honorários Profissionais", "DESPESA", "DESPESA_OPERACIONAL", 615, null, 3, "NEGATIVO", 1, $now, $now],
            ["6.1.6", "Outras Despesas Administrativas", "DESPESA", "DESPESA_OPERACIONAL", 619, null, 3, "NEGATIVO", 1, $now, $now],
            ["6.2", "Despesas com Vendas", "DESPESA", "DESPESA_OPERACIONAL", 62, null, 2, "NEGATIVO", 1, $now, $now],
            ["6.2.1", "Comissões", "DESPESA", "DESPESA_OPERACIONAL", 621, null, 3, "NEGATIVO", 1, $now, $now],
            ["6.2.2", "Propaganda e Publicidade", "DESPESA", "DESPESA_OPERACIONAL", 622, null, 3, "NEGATIVO", 1, $now, $now],
            // LUCRO OPERACIONAL
            ["7", "= LUCRO/PREJUÍZO OPERACIONAL", "TRANSFERENCIA", "LUCRO_OPERACIONAL", 70, null, 1, "POSITIVO", 1, $now, $now],
            // RESULTADO FINANCEIRO
            ["8", "RESULTADO FINANCEIRO", "TRANSFERENCIA", "RESULTADO_FINANCEIRO", 80, null, 1, "POSITIVO", 1, $now, $now],
            ["8.1", "Receitas Financeiras", "RECEITA", "OUTRAS_RECEITAS", 81, null, 2, "POSITIVO", 1, $now, $now],
            ["8.1.1", "Juros Recebidos", "RECEITA", "OUTRAS_RECEITAS", 811, null, 3, "POSITIVO", 1, $now, $now],
            ["8.1.2", "Descontos Obtidos", "RECEITA", "OUTRAS_RECEITAS", 812, null, 3, "POSITIVO", 1, $now, $now],
            ["8.2", "Despesas Financeiras", "DESPESA", "OUTRAS_DESPESAS", 82, null, 2, "NEGATIVO", 1, $now, $now],
            ["8.2.1", "Juros Pagos", "DESPESA", "OUTRAS_DESPESAS", 821, null, 3, "NEGATIVO", 1, $now, $now],
            ["8.2.2", "Descontos Concedidos", "DESPESA", "OUTRAS_DESPESAS", 822, null, 3, "NEGATIVO", 1, $now, $now],
            ["8.2.3", "Tarifas Bancárias", "DESPESA", "OUTRAS_DESPESAS", 823, null, 3, "NEGATIVO", 1, $now, $now],
            // LUCRO ANTES DO IR
            ["9", "= LUCRO/PREJUÍZO ANTES DO IR", "TRANSFERENCIA", "LUCRO_ANTES_IR", 90, null, 1, "POSITIVO", 1, $now, $now],
            // IMPOSTO DE RENDA
            ["10", "(-) IMPOSTO DE RENDA E CONTRIBUIÇÕES", "IMPOSTO", "IMPOSTO_RENDA", 100, null, 1, "NEGATIVO", 1, $now, $now],
            ["10.1", "IRPJ", "IMPOSTO", "IMPOSTO_RENDA", 101, null, 2, "NEGATIVO", 1, $now, $now],
            ["10.2", "CSLL", "IMPOSTO", "IMPOSTO_RENDA", 102, null, 2, "NEGATIVO", 1, $now, $now],
            ["10.3", "PIS/COFINS", "IMPOSTO", "IMPOSTO_RENDA", 103, null, 2, "NEGATIVO", 1, $now, $now],
            // LUCRO LÍQUIDO
            ["11", "= LUCRO/PREJUÍZO LÍQUIDO DO EXERCÍCIO", "TRANSFERENCIA", "LUCRO_LIQUIDO", 110, null, 1, "POSITIVO", 1, $now, $now],
        ];

        $stmt = $mysqli->prepare("INSERT INTO dre_contas (codigo, nome, tipo, grupo, ordem, conta_pai_id, nivel, sinal, ativo, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($contas_dre as $conta) {
            $stmt->bind_param("ssssiiissss", $conta[0], $conta[1], $conta[2], $conta[3], $conta[4], $conta[5], $conta[6], $conta[7], $conta[8], $conta[9], $conta[10]);
            $stmt->execute();
        }
        $stmt->close();
    }

    // ============================================
    // INSERIR DADOS INICIAIS IMPOSTOS
    // ============================================
    $result = $mysqli->query("SELECT COUNT(*) as total FROM impostos_config WHERE anexo = 'III'");
    $row = $result->fetch_assoc();
    if ($row['total'] == 0) {
        $now = date('Y-m-d H:i:s');

        // Anexo III - Serviços
        $anexo3 = [
            ['III', 1, 6.00, 4.00, 3.50, 12.82, 2.78, 43.40, 33.50, 'Prestação de serviços em geral (Anexo III)', 1, $now],
            ['III', 2, 11.20, 4.00, 3.50, 14.05, 3.05, 38.99, 32.41, 'Prestação de serviços em geral (Anexo III)', 1, $now],
            ['III', 3, 13.50, 4.00, 3.50, 13.64, 2.96, 37.62, 32.28, 'Prestação de serviços em geral (Anexo III)', 1, $now],
            ['III', 4, 16.00, 4.00, 3.50, 13.26, 2.87, 35.13, 31.24, 'Prestação de serviços em geral (Anexo III)', 1, $now],
            ['III', 5, 21.00, 4.00, 3.50, 12.82, 2.78, 34.23, 30.67, 'Prestação de serviços em geral (Anexo III)', 1, $now],
        ];

        $stmt = $mysqli->prepare("INSERT INTO impostos_config (anexo, faixa, aliquota_nominal, irpj, csll, cofins, pis, cpp, iss, atividade_principal, ativo, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($anexo3 as $f) {
            $stmt->bind_param("sidddddddsis", $f[0], $f[1], $f[2], $f[3], $f[4], $f[5], $f[6], $f[7], $f[8], $f[9], $f[10], $f[11]);
            $stmt->execute();
        }

        // Anexo IV - Construção
        $anexo4 = [
            ['IV', 1, 4.50, 0.00, 15.74, 14.68, 3.19, 41.50, 31.00, 'Construção e serviços com ISS próprio (Anexo IV)', 1, $now],
            ['IV', 2, 9.00, 0.00, 15.74, 14.68, 3.19, 41.50, 24.89, 'Construção e serviços com ISS próprio (Anexo IV)', 1, $now],
            ['IV', 3, 13.50, 0.00, 15.74, 14.68, 3.19, 42.09, 20.80, 'Construção e serviços com ISS próprio (Anexo IV)', 1, $now],
            ['IV', 4, 17.00, 1.00, 14.74, 13.73, 2.98, 39.40, 24.15, 'Construção e serviços com ISS próprio (Anexo IV)', 1, $now],
            ['IV', 5, 21.00, 1.00, 14.74, 13.73, 2.98, 38.48, 23.07, 'Construção e serviços com ISS próprio (Anexo IV)', 1, $now],
        ];

        foreach ($anexo4 as $f) {
            $stmt->bind_param("sidddddddsis", $f[0], $f[1], $f[2], $f[3], $f[4], $f[5], $f[6], $f[7], $f[8], $f[9], $f[10], $f[11]);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Configurações de impostos
    $result = $mysqli->query("SELECT COUNT(*) as total FROM config_sistema_impostos WHERE chave = 'IMPOSTO_ANEXO_PADRAO'");
    $row = $result->fetch_assoc();
    if ($row['total'] == 0) {
        $configs = [
            ['IMPOSTO_ANEXO_PADRAO', 'III', 'Anexo do Simples Nacional padrão para a empresa'],
            ['IMPOSTO_FAIXA_ATUAL', '1', 'Faixa de faturamento atual (1-5)'],
            ['IMPOSTO_RETENCAO_AUTOMATICA', '1', 'Habilitar retenção automática em novos boletos (1=Sim, 0=Não)'],
            ['IMPOSTO_DRE_INTEGRACAO', '1', 'Integrar retenções automaticamente com DRE (1=Sim, 0=Não)'],
            ['IMPOSTO_ISS_MUNICIPAL', '5.00', 'Alíquota de ISS municipal para cálculo isolado (%)'],
        ];

        $stmt = $mysqli->prepare("INSERT INTO config_sistema_impostos (chave, valor, descricao) VALUES (?, ?, ?)");
        foreach ($configs as $c) {
            $stmt->bind_param("sss", $c[0], $c[1], $c[2]);
            $stmt->execute();
        }
        $stmt->close();
    }

    $mysqli->query("SET FOREIGN_KEY_CHECKS = 1");

    // ============================================
    // CRIAR PERMISSÕES PROGRAMATICAMENTE (V5)
    // Isso evita erros de serialização no SQL
    // ============================================

    // Array completo de permissões V5 - Administrador tem acesso total a todas as funcionalidades
    $permissoes_array = [
        // Clientes
        'aCliente' => '1', 'eCliente' => '1', 'dCliente' => '1', 'vCliente' => '1',
        // Produtos
        'aProduto' => '1', 'eProduto' => '1', 'dProduto' => '1', 'vProduto' => '1',
        // Serviços
        'aServico' => '1', 'eServico' => '1', 'dServico' => '1', 'vServico' => '1',
        // Ordens de Serviço
        'aOs' => '1', 'eOs' => '1', 'dOs' => '1', 'vOs' => '1',
        // Permissões de Técnico na OS
        'vBtnAtendimento' => '1', 'vTecnicoOS' => '1', 'eTecnicoCheckin' => '1',
        'eTecnicoCheckout' => '1', 'eTecnicoFotos' => '1',
        // Vendas
        'aVenda' => '1', 'eVenda' => '1', 'dVenda' => '1', 'vVenda' => '1',
        // Garantias
        'aGarantia' => '1', 'eGarantia' => '1', 'dGarantia' => '1', 'vGarantia' => '1',
        // Lançamentos/Financeiro
        'aLancamento' => '1', 'eLancamento' => '1', 'dLancamento' => '1', 'vLancamento' => '1',
        // Pagamentos
        'aPagamento' => '1', 'ePagamento' => '1', 'dPagamento' => '1', 'vPagamento' => '1',
        // Arquivos/Anexos
        'aArquivo' => '1', 'eArquivo' => '1', 'dArquivo' => '1', 'vArquivo' => '1',
        // Categorias
        'categoria_d' => '1', 'categoria_v' => '1', 'categoria_a' => '1', 'categoria_e' => '1',
        'vCategoria' => '1',
        // Cobranças
        'aCobranca' => '1', 'eCobranca' => '1', 'dCobranca' => '1', 'vCobranca' => '1',
        // Configurações
        'aConfiguracao' => '1', 'eConfiguracao' => '1', 'dConfiguracao' => '1', 'vConfiguracao' => '1',
        // Emitente
        'aEmitente' => '1', 'eEmitente' => '1', 'dEmitente' => '1', 'vEmitente' => '1',
        // Permissões
        'aPermissao' => '1', 'ePermissao' => '1', 'dPermissao' => '1', 'vPermissao' => '1',
        // Auditoria
        'aAuditoria' => '1', 'eAuditoria' => '1', 'dAuditoria' => '1', 'vAuditoria' => '1',
        // Emails
        'aEmail' => '1', 'eEmail' => '1', 'dEmail' => '1', 'vEmail' => '1',
        // Relatórios
        'rContas' => '1', 'rFinanceiro' => '1', 'rProdutos' => '1', 'rServicos' => '1',
        'rVendas' => '1', 'rOs' => '1', 'rClientes' => '1', 'rCliente' => '1',
        'rProduto' => '1', 'rServico' => '1',
        // Controles de Sistema (cada um permite acesso a uma seção administrativa)
        'cUsuario' => '1', 'cEmitente' => '1', 'cPermissao' => '1', 'cBackup' => '1',
        'cAuditoria' => '1', 'cEmail' => '1', 'cSistema' => '1', 'cDocOs' => '1',
        // Novas Funcionalidades V5
        'vCertificado' => '1', 'vImpostos' => '1', 'vDRE' => '1',
        'vWebhooks' => '1', 'vRelatorioAtendimentos' => '1',
        // Usuários Cliente (Multi-CNPJ)
        'vUsuariosCliente' => '1', 'cUsuariosCliente' => '1',
        'eUsuariosCliente' => '1', 'dUsuariosCliente' => '1',
        'cPermUsuariosCliente' => '1',
        // Dashboard e Relatórios Avançados
        'vDashboard' => '1', 'vRelatorioCompleto' => '1', 'vExportarDados' => '1'
    ];

    $permissoes_serializado = serialize($permissoes_array);
    $data_atual = date('Y-m-d');

    // Inserir permissão de Administrador
    $stmt = $mysqli->prepare("INSERT INTO permissoes (idPermissao, nome, permissoes, situacao, data) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE permissoes = ?");
    $idPermissao = 1;
    $nome = 'Administrador';
    $situacao = 1;
    $stmt->bind_param("ississ", $idPermissao, $nome, $permissoes_serializado, $situacao, $data_atual, $permissoes_serializado);
    $stmt->execute();
    $stmt->close();

    $mysqli->close();
    // database created

    $env_file_path = '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env.example';
    $env_file = file_get_contents($env_file_path);

    // set the database config file
    $env_file = str_replace('enter_db_hostname', $host, $env_file);
    $env_file = str_replace('enter_db_username', $dbuser, $env_file);
    $env_file = str_replace('enter_db_password', $dbpassword, $env_file);
    $env_file = str_replace('enter_db_name', $dbname, $env_file);

    // set random enter_encryption_key
    $encryption_key = substr(md5(rand()), 0, 15);
    $env_file = str_replace('enter_encryption_key', $encryption_key, $env_file);
    $env_file = str_replace('enter_baseurl', $base_url, $env_file);

    // set random enter_jwt_key
    $env_file = str_replace('enter_jwt_key', base64_encode(openssl_random_pseudo_bytes(32)), $env_file);
    $env_file = str_replace('enter_token_expire_time', $_POST['enter_token_expire_time'], $env_file);
    $env_file = str_replace('enter_api_enabled', (string) $_POST['enter_api_enabled'], $env_file);

    // set the environment = production
    $env_file = str_replace('pre_installation', 'production', $env_file);

    if (file_put_contents('..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env', $env_file)) {
        echo json_encode(['success' => true, 'message' => 'Instalação bem sucedida.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar arquivo env.']);
    }

    exit();
}
