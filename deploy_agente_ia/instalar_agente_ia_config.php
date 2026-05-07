<?php
/**
 * Instalador da tabela agente_ia_configuracoes
 * Acesse via navegador: https://seu-dominio.com/instalar_agente_ia_config.php
 * Depois delete este arquivo!
 */

// Carrega o CodeIgniter
require_once 'index.php';

// Use o banco do CI
$CI = & get_instance();
$CI->load->database();

function runSql($CI, $sql) {
    if ($CI->db->query($sql)) {
        echo "OK: " . substr($sql, 0, 60) . "...<br>";
        return true;
    } else {
        $erro = $CI->db->error();
        echo "ERRO: " . $erro['message'] . "<br>";
        return false;
    }
}

?'>
<!DOCTYPE html>
<html>
<head>
    <title>Instalador Agente IA - Configuracoes</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; max-width: 800px; margin: 0 auto; }
        h1 { color: #00b894; }
        .ok { color: #00b894; }
        .erro { color: #e74c3c; }
        .card { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .btn { padding: 10px 20px; background: #00b894; color: #fff; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        code { background: #eee; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Instalador - Tabela de Configuracoes do Agente IA</h1>

    <div class="card">
        <p>Este script cria a tabela <code>agente_ia_configuracoes</code> e insere os valores padrao.</p>
    </div>

    <?php
    $CI->db->trans_start();

    // 1. Criar tabela
    $sql = "CREATE TABLE IF NOT EXISTS `agente_ia_configuracoes` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Configuracoes do agente IA (URLs, tokens, etc.)';";
    runSql($CI, $sql);

    // 2. Inserir padroes
    $padroes = [
        ['evolution_url', 'http://192.168.100.238:8091', 'URL da API Evolution (evolution-go)', 'evolution', 0],
        ['evolution_apikey', '', 'API Key da Evolution API', 'evolution', 1],
        ['evolution_instance', '', 'Nome da instancia WhatsApp', 'evolution', 0],
        ['evolution_enabled', '0', 'Ativar integracao Evolution', 'evolution', 0],
        ['n8n_webhook_url', '', 'URL do webhook do n8n (resposta aprovada)', 'n8n', 0],
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
    ];

    foreach ($padroes as $p) {
        $sql = "INSERT INTO `agente_ia_configuracoes` (`chave`, `valor`, `descricao`, `grupo`, `sensivel`)
                VALUES ('{$p[0]}', '{$p[1]}', '{$p[2]}', '{$p[3]}', {$p[4]})
                ON DUPLICATE KEY UPDATE `valor` = `valor`;";
        runSql($CI, $sql);
    }

    $CI->db->trans_complete();

    if ($CI->db->trans_status() === FALSE) {
        echo "<hr><p class='erro'>Falha na instalacao. Verifique os erros acima.</p>";
    } else {
        echo "<hr><p class='ok'><b>Instalacao concluida com sucesso!</b></p>";
        echo "<p><a href='" . site_url('agente_ia/configuracoes') . "' class='btn'>Ir para Configuracoes</a></p>";
        echo "<p style='color:#e74c3c'><b>IMPORTANTE: Delete o arquivo <code>instalar_agente_ia_config.php</code> do servidor agora!</b></p>";
    }
    ?'>

</body>
</html>
