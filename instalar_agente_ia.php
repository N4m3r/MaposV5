<?php
/**
 * Instalador COMPLETO das tabelas do Agente IA
 * Acesse via navegador: https://seu-dominio.com/instalar_agente_ia.php
 * Depois delete este arquivo!
 */

require_once 'index.php';

$CI = & get_instance();
$CI->load->database();

function runSql($CI, $sql, $descricao = '') {
    echo "<div style='font-family:monospace;font-size:12px;padding:3px 0;'>";
    echo "<b>" . ($descricao ?: substr($sql, 0, 50)) . "</b> ... ";
    if ($CI->db->query($sql)) {
        echo "<span style='color:#00b894'>OK</span>";
    } else {
        $erro = $CI->db->error();
        echo "<span style='color:#e74c3c'>ERRO: " . $erro['message'] . "</span>";
    }
    echo "</div>";
}

$tabelas = [
    'agente_ia_autorizacoes',
    'agente_ia_permissoes',
    'agente_ia_configuracoes',
    'agente_ia_logs_conversa',
    'whatsapp_integracao',
];

$faltantes = [];
foreach ($tabelas as $t) {
    if (!$CI->db->table_exists($t)) {
        $faltantes[] = $t;
    }
}

$ok = true;

if (count($faltantes) > 0) {
    $CI->db->trans_start();

    // 1. agente_ia_autorizacoes
    if (in_array('agente_ia_autorizacoes', $faltantes)) {
        runSql($CI, "CREATE TABLE IF NOT EXISTS `agente_ia_autorizacoes` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `token` VARCHAR(64) NOT NULL,
            `numero_telefone` VARCHAR(20) NOT NULL,
            `usuarios_id` INT(11) NULL,
            `clientes_id` INT(11) NULL,
            `acao` VARCHAR(100) NOT NULL,
            `dados_json` JSON NULL,
            `nivel_criticidade` TINYINT(1) NOT NULL DEFAULT 1,
            `status` ENUM('pendente','aprovada','rejeitada','expirada','executada') DEFAULT 'pendente',
            `metodo_autorizacao` ENUM('whatsapp','email','painel') DEFAULT 'whatsapp',
            `resposta_usuario` VARCHAR(255) NULL,
            `ip_autorizacao` VARCHAR(45) NULL,
            `user_agent` VARCHAR(255) NULL,
            `executado_por` ENUM('agente_ia','usuario') DEFAULT 'agente_ia',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `expires_at` DATETIME NOT NULL,
            `executed_at` DATETIME NULL,
            `resultado_json` JSON NULL,
            `observacoes` TEXT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_token` (`token`),
            INDEX `idx_status` (`status`),
            INDEX `idx_numero` (`numero_telefone`),
            INDEX `idx_created_at` (`created_at`),
            INDEX `idx_expires_at` (`expires_at`),
            INDEX `idx_acao_status` (`acao`, `status`),
            INDEX `idx_usuarios_id` (`usuarios_id`),
            INDEX `idx_clientes_id` (`clientes_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", 'agente_ia_autorizacoes');
    }

    // 2. agente_ia_permissoes
    if (in_array('agente_ia_permissoes', $faltantes)) {
        runSql($CI, "CREATE TABLE IF NOT EXISTS `agente_ia_permissoes` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `perfil` ENUM('cliente','tecnico','admin','financeiro','vendedor','desconhecido') NOT NULL DEFAULT 'desconhecido',
            `acao` VARCHAR(100) NOT NULL,
            `nivel_maximo_automatico` TINYINT(1) NOT NULL DEFAULT 1,
            `requer_2fa` TINYINT(1) DEFAULT 0,
            `horario_permitido_inicio` TIME DEFAULT '00:00:00',
            `horario_permitido_fim` TIME DEFAULT '23:59:59',
            `ativo` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_perfil_acao` (`perfil`,`acao`),
            INDEX `idx_perfil` (`perfil`),
            INDEX `idx_acao` (`acao`),
            INDEX `idx_ativo` (`ativo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", 'agente_ia_permissoes');

        runSql($CI, "INSERT IGNORE INTO `agente_ia_permissoes` (`perfil`, `acao`, `nivel_maximo_automatico`, `requer_2fa`) VALUES
            ('cliente', 'consultar_status_os', 1, 0),
            ('cliente', 'consultar_divida', 1, 0),
            ('cliente', 'consultar_cliente', 1, 0),
            ('cliente', 'consultar_estoque', 1, 0),
            ('cliente', 'solicitar_orcamento', 2, 0),
            ('tecnico', 'consultar_minhas_os', 1, 0),
            ('tecnico', 'consultar_status_os', 1, 0),
            ('tecnico', 'atualizar_status_os', 3, 0),
            ('tecnico', 'registrar_atividade', 3, 0),
            ('tecnico', 'criar_os', 3, 0),
            ('admin', 'consultar_status_os', 1, 0),
            ('admin', 'consultar_cliente', 1, 0),
            ('admin', 'consultar_estoque', 1, 0),
            ('admin', 'consultar_lancamentos', 1, 0),
            ('admin', 'criar_os', 3, 0),
            ('admin', 'atualizar_status_os', 3, 0),
            ('admin', 'registrar_atividade', 3, 0),
            ('admin', 'aprovar_orcamento', 4, 1),
            ('admin', 'gerar_cobranca', 4, 1),
            ('admin', 'emitir_nfse', 5, 1),
            ('financeiro', 'consultar_lancamentos', 1, 0),
            ('financeiro', 'consultar_divida', 1, 0),
            ('financeiro', 'gerar_boleto', 4, 1),
            ('financeiro', 'gerar_cobranca', 4, 1),
            ('vendedor', 'consultar_cliente', 1, 0),
            ('vendedor', 'consultar_estoque', 1, 0),
            ('vendedor', 'solicitar_orcamento', 2, 0),
            ('desconhecido', 'consultar_status_os', 1, 0)
            ON DUPLICATE KEY UPDATE `nivel_maximo_automatico`=VALUES(`nivel_maximo_automatico`), `requer_2fa`=VALUES(`requer_2fa`), `ativo`=1", 'Seed permissoes');
    }

    // 3. agente_ia_configuracoes
    if (in_array('agente_ia_configuracoes', $faltantes)) {
        runSql($CI, "CREATE TABLE IF NOT EXISTS `agente_ia_configuracoes` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `chave` VARCHAR(100) NOT NULL,
            `valor` TEXT NULL,
            `descricao` VARCHAR(255) NULL,
            `grupo` VARCHAR(50) NOT NULL DEFAULT 'geral',
            `sensivel` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=ocultar em logs',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_chave` (`chave`),
            INDEX `idx_grupo` (`grupo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", 'agente_ia_configuracoes');

        $padroes = [
            ['evolution_url', 'http://192.168.100.238:8091', 'URL da API Evolution (evolution-go)', 'evolution', 0],
            ['evolution_apikey', '', 'API Key da Evolution API', 'evolution', 1],
            ['evolution_instance', '', 'Nome da instancia WhatsApp', 'evolution', 0],
            ['evolution_enabled', '0', 'Ativar integracao Evolution', 'evolution', 0],
            ['n8n_webhook_url', '', 'URL do webhook do n8n', 'n8n', 0],
            ['n8n_apikey', '', 'API Key do n8n', 'n8n', 1],
            ['n8n_enabled', '0', 'Ativar integracao n8n', 'n8n', 0],
            ['llm_provider', 'openrouter', 'Provedor LLM (openrouter, openai, groq)', 'llm', 0],
            ['llm_apikey', '', 'API Key do provedor LLM', 'llm', 1],
            ['llm_model', 'google/gemini-2.5-flash-preview', 'Modelo LLM padrao', 'llm', 0],
            ['llm_system_prompt', 'Voce e um assistente virtual desta empresa. Seja educado, breve e objetivo.', 'Prompt de sistema do agente', 'llm', 0],
            ['llm_enabled', '0', 'Ativar processamento LLM', 'llm', 0],
            ['agente_max_tokens', '4096', 'Maximo de tokens por resposta', 'llm', 0],
            ['agente_timeout', '30', 'Timeout segundos para resposta da IA', 'llm', 0],
            ['numero_whatsapp_principal', '', 'Numero principal do atendimento', 'geral', 0],
            ['mensagem_boas_vindas', 'Ola! Sou o assistente virtual. Em que posso ajudar?', 'Mensagem de boas-vindas', 'geral', 0],
            ['horario_atendimento_inicio', '08:00', 'Inicio do atendimento automatico', 'geral', 0],
            ['horario_atendimento_fim', '18:00', 'Fim do atendimento automatico', 'geral', 0],
            ['auto_responder_fora_horario', '1', 'Responder automaticamente fora do horario', 'geral', 0],
            ['autorizacao_tempo_minutos', '15', 'Tempo de expiracao dos tokens de autorizacao', 'autorizacao', 0],
            ['autorizacao_max_tentativas', '3', 'Maximo de tentativas de resposta por token', 'autorizacao', 0],
            ['rate_limit_minutos', '5', 'Janela de rate limit por numero/acao (minutos)', 'autorizacao', 0],
            ['notificacao_admin_email', '', 'E-mail para notificacoes criticas do agente', 'notificacao', 0],
        ];

        foreach ($padroes as $p) {
            runSql($CI, "INSERT INTO `agente_ia_configuracoes` (`chave`, `valor`, `descricao`, `grupo`, `sensivel`)
                VALUES ('{$p[0]}', '{$p[1]}', '{$p[2]}', '{$p[3]}', {$p[4]})
                ON DUPLICATE KEY UPDATE `valor`=`valo}r`", 'Seed: ' . $p[0]);
        }
    }

    // 4. agente_ia_logs_conversa
    if (in_array('agente_ia_logs_conversa', $faltantes)) {
        runSql($CI, "CREATE TABLE IF NOT EXISTS `agente_ia_logs_conversa` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `numero_telefone` VARCHAR(20) NOT NULL,
            `usuarios_id` INT(11) NULL,
            `clientes_id` INT(11) NULL,
            `tipo` ENUM('recebido','enviado','sistema','erro') NOT NULL DEFAULT 'recebido',
            `mensagem` TEXT NULL,
            `intencao_detectada` VARCHAR(100) NULL,
            `acao_executada` VARCHAR(100) NULL,
            `metadados_json` JSON NULL,
            `ip_origem` VARCHAR(45) NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_numero` (`numero_telefone`),
            INDEX `idx_tipo` (`tipo`),
            INDEX `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", 'agente_ia_logs_conversa');
    }

    // 5. whatsapp_integracao
    if (in_array('whatsapp_integracao', $faltantes)) {
        runSql($CI, "CREATE TABLE IF NOT EXISTS `whatsapp_integracao` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `numero_telefone` VARCHAR(20) NOT NULL,
            `clientes_id` INT(11) NULL DEFAULT NULL,
            `usuarios_id` INT(11) NULL DEFAULT NULL,
            `situacao` TINYINT(1) NOT NULL DEFAULT 1,
            `ultima_interacao` DATETIME NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_numero_telefone` (`numero_telefone`),
            INDEX `idx_clientes_id` (`clientes_id`),
            INDEX `idx_usuarios_id` (`usuarios_id`),
            INDEX `idx_situacao` (`situacao`),
            INDEX `idx_numero_situacao` (`numero_telefone`, `situacao`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        COMMENT='Vinculo de numeros WhatsApp com clientes ou usuarios'", 'whatsapp_integracao');
    }

    $CI->db->trans_complete();
    $ok = $CI->db->trans_status();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Instalador Agente IA - MapOS</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; max-width: 900px; margin: 0 auto; background: #f5f6fa; }
        h1 { color: #2d3436; }
        .ok { color: #00b894; font-weight: bold; }
        .erro { color: #e74c3c; font-weight: bold; }
        .card { background: #fff; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .btn { padding: 12px 24px; background: #00b894; color: #fff; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 10px; }
        code { background: #eee; padding: 2px 6px; border-radius: 3px; }
        .tabela-ok { background: #d4edda; padding: 8px 12px; border-radius: 4px; margin: 5px 0; }
        .tabela-falta { background: #f8d7da; padding: 8px 12px; border-radius: 4px; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Instalador - Agente IA (MapOS)</h1>

    <div class="card">
        <h3>Diagnostico</h3>
        <?php foreach ($tabelas as $t): ?>
            <?php if ($CI->db->table_exists($t)): ?>
                <div class="tabela-ok">Tabela <code><?php echo $t; ?></code> existe</div>
            <?php else: ?>
                <div class="tabela-falta">Tabela <code><?php echo $t; ?></code> <b>FALTA</b></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <?php if (count($faltantes) > 0): ?>
    <div class="card">
        <h3>Criando tabelas faltantes...</h3>
        <?php if ($ok): ?>
            <p class="ok"><b>Instalacao concluida com sucesso!</b></p>
            <p><a href="<?php echo site_url('agente_ia'); ?>" class="btn">Ir para Painel Agente IA</a></p>
            <p style="color:#e74c3c"><b>IMPORTANTE: Delete o arquivo <code>instalar_agente_ia.php</code> do servidor agora!</b></p>
        <?php else: ?>
            <p class="erro">Falha na instalacao. Verifique os erros acima.</p>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="card">
        <p class="ok"><b>Todas as tabelas do Agente IA ja existem!</b></p>
        <p><a href="<?php echo site_url('agente_ia'); ?>" class="btn">Ir para Painel Agente IA</a></p>
    </div>
    <?php endif; ?>
</body>
</html>
