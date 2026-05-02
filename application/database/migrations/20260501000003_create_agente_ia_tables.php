<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_agente_ia_tables extends CI_Migration
{
    public function up()
    {
        // ========================================================
        // 1. TABELA: agente_ia_autorizacoes
        //    Controle de autorizacoes do agente IA
        // ========================================================
        $this->db->query("CREATE TABLE IF NOT EXISTS `agente_ia_autorizacoes` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `token` VARCHAR(64) NOT NULL COMMENT 'Token unico da autorizacao',
            `numero_telefone` VARCHAR(20) NOT NULL,
            `usuarios_id` INT(11) NULL,
            `clientes_id` INT(11) NULL,
            `acao` VARCHAR(100) NOT NULL COMMENT 'ex: criar_os, aprovar_orcamento',
            `dados_json` JSON NULL COMMENT 'Parametros da acao',
            `nivel_criticidade` TINYINT(1) NOT NULL DEFAULT 1,
            `status` ENUM('pendente','aprovada','rejeitada','expirada','executada') DEFAULT 'pendente',
            `metodo_autorizacao` ENUM('whatsapp','email','painel') DEFAULT 'whatsapp',
            `resposta_usuario` VARCHAR(255) NULL COMMENT 'Texto da resposta do usuario',
            `ip_autorizacao` VARCHAR(45) NULL,
            `user_agent` VARCHAR(255) NULL,
            `executado_por` ENUM('agente_ia','usuario') DEFAULT 'agente_ia',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `expires_at` DATETIME NOT NULL COMMENT 'Validade do token (ex: 15 min)',
            `executed_at` DATETIME NULL,
            `resultado_json` JSON NULL COMMENT 'Retorno da execucao',
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_token` (`token`),
            INDEX `idx_status` (`status`),
            INDEX `idx_numero` (`numero_telefone`),
            INDEX `idx_created_at` (`created_at`),
            FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios`(`idUsuarios`) ON DELETE SET NULL,
            FOREIGN KEY (`clientes_id`) REFERENCES `clientes`(`idClientes`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ========================================================
        // 2. TABELA: agente_ia_permissoes
        //    Permissoes do agente por perfil
        // ========================================================
        $this->db->query("CREATE TABLE IF NOT EXISTS `agente_ia_permissoes` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `perfil` ENUM('cliente','tecnico','admin','financeiro','vendedor') NOT NULL,
            `acao` VARCHAR(100) NOT NULL,
            `nivel_maximo_automatico` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Ate qual nivel o agente pode executar sem pedir',
            `requer_2fa` TINYINT(1) DEFAULT 0 COMMENT 'Requer segundo fator (codigo por email)',
            `horario_permitido_inicio` TIME DEFAULT '00:00:00',
            `horario_permitido_fim` TIME DEFAULT '23:59:59',
            `ativo` TINYINT(1) DEFAULT 1,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_perfil_acao` (`perfil`,`acao`),
            INDEX `idx_perfil` (`perfil`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ========================================================
        // 3. TABELA: agente_ia_logs_conversa
        //    Log de conversas e auditoria do agente
        // ========================================================
        $this->db->query("CREATE TABLE IF NOT EXISTS `agente_ia_logs_conversa` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `numero_telefone` VARCHAR(20) NULL,
            `tipo` ENUM('entrada','saida','sistema','erro') DEFAULT 'entrada',
            `mensagem` TEXT NULL,
            `intencao_detectada` VARCHAR(100) NULL,
            `os_id` INT(11) NULL,
            `metadados_json` JSON NULL,
            `ip_origem` VARCHAR(45) NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_numero` (`numero_telefone`),
            INDEX `idx_tipo` (`tipo`),
            INDEX `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ========================================================
        // 4. SEED: permissoes padrao
        // ========================================================
        $this->db->query("INSERT IGNORE INTO `agente_ia_permissoes` (`perfil`, `acao`, `nivel_maximo_automatico`, `requer_2fa`) VALUES
            ('cliente', 'consultar_status_os', 1, 0),
            ('cliente', 'consultar_divida', 1, 0),
            ('cliente', 'solicitar_orcamento', 2, 0),
            ('tecnico', 'consultar_minhas_os', 1, 0),
            ('tecnico', 'atualizar_status_os', 3, 0),
            ('tecnico', 'registrar_atividade', 3, 0),
            ('admin', 'criar_os', 3, 0),
            ('admin', 'aprovar_orcamento', 4, 1),
            ('admin', 'gerar_cobranca', 4, 1),
            ('admin', 'emitir_nfse', 5, 1),
            ('financeiro', 'consultar_lancamentos', 1, 0),
            ('financeiro', 'gerar_boleto', 4, 1)");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `agente_ia_permissoes`");
        $this->db->query("DROP TABLE IF EXISTS `agente_ia_autorizacoes`");
    }
}
