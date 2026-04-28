<?php
/**
 * Hook: Bootstrap de Tabelas de Notificações
 * Verifica se as tabelas de notificações existem e executa a migration automaticamente
 * para tornar a integração Evolution API nativa (plug-and-play).
 */

if (!function_exists('check_notificacoes_tables')) {
    function check_notificacoes_tables()
    {
        $CI = &get_instance();
        $CI->load->database();

        $tabelas = [
            'notificacoes_config',
            'notificacoes_templates',
            'notificacoes_log',
            'notificacoes_agendadas',
            'clientes_notificacoes_consent',
        ];

        $faltaAlguma = false;
        foreach ($tabelas as $tabela) {
            if (!$CI->db->table_exists($tabela)) {
                $faltaAlguma = true;
                break;
            }
        }

        if ($faltaAlguma) {
            log_message('info', '[Bootstrap] Tabelas de notificações não encontradas. Executando migration...');

            // Carrega a migration
            $CI->load->library('migration');
            $CI->migration->set_namespace('');

            // Encontra o arquivo de migration de notificações
            $migrationFile = null;
            $migrationsPath = APPPATH . 'database/migrations/';
            if (is_dir($migrationsPath)) {
                $files = glob($migrationsPath . '*notificacoes*.php');
                if (!empty($files)) {
                    // Ordena para pegar o mais recente
                    sort($files);
                    $migrationFile = end($files);
                }
            }

            if ($migrationFile) {
                require_once $migrationFile;

                // Extrai o nome da classe a partir do filename
                $filename = basename($migrationFile, '.php');
                // Padrão: YYYYMMDDHHMMSS_nome_classe
                $parts = explode('_', $filename, 2);
                $className = 'Migration_' . $parts[1];

                if (class_exists($className)) {
                    $migration = new $className();
                    if (method_exists($migration, 'up')) {
                        try {
                            $migration->up();
                            log_message('info', '[Bootstrap] Migration ' . $className . ' executada com sucesso.');
                        } catch (Exception $e) {
                            log_message('error', '[Bootstrap] Erro ao executar migration: ' . $e->getMessage());
                        }
                    }
                }
            } else {
                // Fallback: cria tabelas manualmente se migration não for encontrada
                _criar_tabelas_notificacoes_fallback($CI);
            }
        }

        // Verifica se a coluna evolution_version existe na tabela notificacoes_config
        // (pode ter sido criada sem ela em versões anteriores)
        if ($CI->db->table_exists('notificacoes_config')) {
            if (!$CI->db->field_exists('evolution_version', 'notificacoes_config')) {
                log_message('info', '[Bootstrap] Adicionando coluna evolution_version a notificacoes_config');
                try {
                    $CI->db->query("ALTER TABLE `notificacoes_config`
                        ADD COLUMN `evolution_version` ENUM('v1','v2','go') DEFAULT 'v2'
                        AFTER `evolution_instance`");
                    log_message('info', '[Bootstrap] Coluna evolution_version adicionada com sucesso.');
                } catch (Exception $e) {
                    log_message('error', '[Bootstrap] Erro ao adicionar coluna evolution_version: ' . $e->getMessage());
                }
            }
        }
    }
}

if (!function_exists('_criar_tabelas_notificacoes_fallback')) {
    function _criar_tabelas_notificacoes_fallback($CI)
    {
        log_message('info', '[Bootstrap] Executando criação fallback de tabelas de notificações.');

        $CI->db->query("CREATE TABLE IF NOT EXISTS `notificacoes_config` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `whatsapp_provedor` ENUM('evolution', 'meta_api', 'z_api', 'desativado') DEFAULT 'desativado',
            `whatsapp_ativo` TINYINT(1) DEFAULT 0,
            `evolution_url` VARCHAR(255) DEFAULT NULL,
            `evolution_apikey` VARCHAR(255) DEFAULT NULL,
            `evolution_instance` VARCHAR(100) DEFAULT 'mapos',
            `evolution_version` ENUM('v1','v2','go') DEFAULT 'v2',
            `evolution_estado` VARCHAR(50) DEFAULT 'desconectado',
            `meta_phone_number_id` VARCHAR(50) DEFAULT NULL,
            `meta_access_token` TEXT DEFAULT NULL,
            `meta_webhook_verify_token` VARCHAR(100) DEFAULT NULL,
            `z_api_url` VARCHAR(255) DEFAULT NULL,
            `z_api_token` VARCHAR(255) DEFAULT NULL,
            `notificacao_os_criada` TINYINT(1) DEFAULT 1,
            `notificacao_os_atualizada` TINYINT(1) DEFAULT 1,
            `notificacao_os_pronta` TINYINT(1) DEFAULT 1,
            `notificacao_os_orcamento` TINYINT(1) DEFAULT 1,
            `notificacao_venda_realizada` TINYINT(1) DEFAULT 1,
            `notificacao_cobranca_gerada` TINYINT(1) DEFAULT 1,
            `notificacao_cobranca_vencimento` TINYINT(1) DEFAULT 1,
            `notificacao_lembrete_aniversario` TINYINT(1) DEFAULT 0,
            `horario_envio_inicio` TIME DEFAULT '08:00:00',
            `horario_envio_fim` TIME DEFAULT '18:00:00',
            `enviar_fim_semana` TINYINT(1) DEFAULT 0,
            `respeitar_horario` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Verifica se a coluna evolution_version existe (pode ter sido criada sem ela)
        if (!$CI->db->field_exists('evolution_version', 'notificacoes_config')) {
            $CI->db->query("ALTER TABLE `notificacoes_config`
                ADD COLUMN `evolution_version` ENUM('v1','v2','go') DEFAULT 'v2'
                AFTER `evolution_instance`");
            log_message('info', '[Bootstrap] Coluna evolution_version adicionada a notificacoes_config');
        }

        $CI->db->query("INSERT INTO `notificacoes_config` (`id`, `whatsapp_provedor`, `whatsapp_ativo`)
            VALUES (1, 'desativado', 0)
            ON DUPLICATE KEY UPDATE id=id");

        $CI->db->query("CREATE TABLE IF NOT EXISTS `notificacoes_templates` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `chave` VARCHAR(50) NOT NULL UNIQUE,
            `nome` VARCHAR(100) NOT NULL,
            `descricao` TEXT DEFAULT NULL,
            `categoria` ENUM('os', 'venda', 'cobranca', 'marketing', 'sistema') DEFAULT 'os',
            `canal` ENUM('whatsapp', 'email', 'sms', 'todos') DEFAULT 'whatsapp',
            `assunto` VARCHAR(255) DEFAULT NULL,
            `mensagem` TEXT NOT NULL,
            `variaveis` TEXT DEFAULT NULL,
            `ativo` TINYINT(1) DEFAULT 1,
            `e_marketing` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_chave` (`chave`),
            INDEX `idx_categoria` (`categoria`),
            INDEX `idx_ativo` (`ativo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $CI->db->query("CREATE TABLE IF NOT EXISTS `notificacoes_log` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `cliente_id` INT(11) DEFAULT NULL,
            `telefone` VARCHAR(20) DEFAULT NULL,
            `email` VARCHAR(255) DEFAULT NULL,
            `template_chave` VARCHAR(50) DEFAULT NULL,
            `assunto` VARCHAR(255) DEFAULT NULL,
            `mensagem` TEXT DEFAULT NULL,
            `mensagem_processada` TEXT DEFAULT NULL,
            `status` ENUM('pendente', 'enviando', 'enviado', 'entregue', 'falha', 'lido', 'cancelado') DEFAULT 'pendente',
            `canal` ENUM('whatsapp', 'email', 'sms') DEFAULT 'whatsapp',
            `provedor` VARCHAR(50) DEFAULT NULL,
            `external_id` VARCHAR(255) DEFAULT NULL,
            `resposta_api` TEXT DEFAULT NULL,
            `erro` TEXT DEFAULT NULL,
            `os_id` INT(11) DEFAULT NULL,
            `venda_id` INT(11) DEFAULT NULL,
            `tentativas` INT(3) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `sent_at` TIMESTAMP NULL,
            `delivered_at` TIMESTAMP NULL,
            `read_at` TIMESTAMP NULL,
            PRIMARY KEY (`id`),
            INDEX `idx_status` (`status`),
            INDEX `idx_cliente` (`cliente_id`),
            INDEX `idx_os` (`os_id`),
            INDEX `idx_venda` (`venda_id`),
            INDEX `idx_created` (`created_at`),
            INDEX `idx_canal_status` (`canal`, `status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $CI->db->query("CREATE TABLE IF NOT EXISTS `notificacoes_agendadas` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `data_hora_envio` DATETIME NOT NULL,
            `data_hora_enviado` DATETIME DEFAULT NULL,
            `cliente_id` INT(11) DEFAULT NULL,
            `telefone` VARCHAR(20) DEFAULT NULL,
            `email` VARCHAR(255) DEFAULT NULL,
            `template_chave` VARCHAR(50) DEFAULT NULL,
            `variaveis` TEXT DEFAULT NULL,
            `status` ENUM('agendada', 'enviada', 'cancelada', 'falha') DEFAULT 'agendada',
            `os_id` INT(11) DEFAULT NULL,
            `venda_id` INT(11) DEFAULT NULL,
            `origem` VARCHAR(50) DEFAULT 'sistema',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_data_envio` (`data_hora_envio`),
            INDEX `idx_status` (`status`),
            INDEX `idx_cliente` (`cliente_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $CI->db->query("CREATE TABLE IF NOT EXISTS `clientes_notificacoes_consent` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `cliente_id` INT(11) NOT NULL,
            `whatsapp` TINYINT(1) DEFAULT 1,
            `email` TINYINT(1) DEFAULT 1,
            `sms` TINYINT(1) DEFAULT 0,
            `marketing` TINYINT(1) DEFAULT 0,
            `data_consentimento` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `ip_consentimento` VARCHAR(45) DEFAULT NULL,
            `user_agent` TEXT DEFAULT NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_cliente` (`cliente_id`),
            INDEX `idx_whatsapp` (`whatsapp`),
            INDEX `idx_marketing` (`marketing`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Templates padrão
        $templates = [
            [
                'chave' => 'os_criada',
                'nome' => 'OS Criada',
                'descricao' => 'Notificação enviada quando uma nova OS é criada',
                'categoria' => 'os',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 👋\n\nSua Ordem de Serviço #{os_id} foi registrada em nosso sistema.\n\n📋 *Equipamento:* {equipamento}\n📝 *Defeito:* {defeito}\n📅 *Previsão:* {data_previsao}\n\nAcompanhe o status pelo link:\n{link_consulta}\n\nObrigado pela preferência! 🤝",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'os_id' => 'Número da OS',
                    'equipamento' => 'Descrição do equipamento',
                    'defeito' => 'Defeito informado',
                    'data_previsao' => 'Data prevista para conclusão',
                    'link_consulta' => 'Link para consulta pública da OS',
                    'emitente_nome' => 'Nome da empresa emitente',
                    'emitente_telefone' => 'Telefone da empresa'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'os_atualizada',
                'nome' => 'OS Atualizada',
                'descricao' => 'Notificação enviada quando o status da OS é alterado',
                'categoria' => 'os',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 📋\n\nHouve uma atualização na sua OS #{os_id}.\n\n📊 *Status:* {status_atual}\n📋 {equipamento}\n\nPara mais informações, entre em contato conosco.\n\n{emitente_nome}\n📞 {emitente_telefone}",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'os_id' => 'Número da OS',
                    'status_atual' => 'Novo status da OS',
                    'status_anterior' => 'Status anterior',
                    'equipamento' => 'Descrição do equipamento',
                    'emitente_nome' => 'Nome da empresa',
                    'emitente_telefone' => 'Telefone da empresa'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'os_pronta',
                'nome' => 'OS Pronta',
                'descricao' => 'Notificação enviada quando a OS é finalizada',
                'categoria' => 'os',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 🎉\n\nSua Ordem de Serviço #{os_id} está *PRONTA*! ✅\n\n📋 {equipamento}\n💰 *Valor:* R$ {valor_total}\n\n📍 *Retirada em:*\n{emitente_endereco}\n⏰ *Funcionamento:* {emitente_horario}\n\nDúvidas? Responda aqui ou ligue {emitente_telefone}",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'os_id' => 'Número da OS',
                    'equipamento' => 'Descrição do equipamento',
                    'valor_total' => 'Valor total da OS',
                    'emitente_nome' => 'Nome da empresa',
                    'emitente_endereco' => 'Endereço da empresa',
                    'emitente_horario' => 'Horário de funcionamento',
                    'emitente_telefone' => 'Telefone da empresa'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'cobranca_gerada',
                'nome' => 'Cobrança Gerada',
                'descricao' => 'Notificação de cobrança/boleto gerado',
                'categoria' => 'cobranca',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 💳\n\nSua cobrança foi gerada:\n\n*Referente a:* {referente}\n💰 *Valor:* R$ {valor}\n📅 *Vencimento:* {data_vencimento}\n\n💳 *Pagar agora:* {link_pagamento}\n\nApós o pagamento, envie o comprovante aqui! ✅",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'referente' => 'Referência da cobrança (OS/Venda)',
                    'valor' => 'Valor da cobrança',
                    'data_vencimento' => 'Data de vencimento',
                    'link_pagamento' => 'Link para pagamento'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'venda_realizada',
                'nome' => 'Venda Realizada',
                'descricao' => 'Confirmação de venda para o cliente',
                'categoria' => 'venda',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 🛒\n\nSua compra foi registrada com sucesso!\n\n*Venda #{venda_id}*\n💰 *Valor:* R$ {valor_total}\n📅 *Data:* {data_venda}\n\nAgradecemos sua preferência! 💙",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'venda_id' => 'Número da venda',
                    'valor_total' => 'Valor total da venda',
                    'data_venda' => 'Data da venda'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ]
        ];

        foreach ($templates as $t) {
            $CI->db->replace('notificacoes_templates', $t);
        }

        // Adiciona coluna celular_verificado se não existir
        if (!$CI->db->field_exists('celular_verificado', 'clientes')) {
            $CI->db->query("ALTER TABLE `clientes` ADD COLUMN `celular_verificado` TINYINT(1) DEFAULT 0 AFTER `celular`");
        }

        log_message('info', '[Bootstrap] Tabelas de notificações criadas via fallback.');
    }
}
