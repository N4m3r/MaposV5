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
