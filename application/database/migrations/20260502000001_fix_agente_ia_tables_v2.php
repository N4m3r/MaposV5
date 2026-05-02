<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration Fix_agente_ia_tables_v2
 * Corrige divergencias entre migration anterior e database/sql/banco.sql
 * - Adiciona tabelas faltantes: whatsapp_integracao
 * - Corrige tabela agente_ia_configuracoes para usar 'grupo' e 'sensivel'
 * - Adiciona indices faltantes
 * - Atualiza ENUM do agente_ia_logs_conversa para 'recebido/enviado/sistema/erro'
 */
class Migration_Fix_agente_ia_tables_v2 extends CI_Migration
{
    public function up()
    {
        // ================================================================
        // 1. TABELA: whatsapp_integracao (faltava na migration anterior)
        //    Vinculo de numeros WhatsApp com clientes ou usuarios
        // ================================================================
        $this->db->query("CREATE TABLE IF NOT EXISTS `whatsapp_integracao` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `numero_telefone` VARCHAR(20) NOT NULL COMMENT 'Numero com DDD e DDI',
            `clientes_id` INT(11) NULL DEFAULT NULL COMMENT 'Vinculo com cliente',
            `usuarios_id` INT(11) NULL DEFAULT NULL COMMENT 'Vinculo com usuario interno',
            `situacao` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
            `ultima_interacao` DATETIME NULL DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_numero_telefone` (`numero_telefone`),
            INDEX `idx_clientes_id` (`clientes_id`),
            INDEX `idx_usuarios_id` (`usuarios_id`),
            INDEX `idx_situacao` (`situacao`),
            INDEX `idx_numero_situacao` (`numero_telefone`, `situacao`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        COMMENT='Vinculo de numeros WhatsApp com clientes ou usuarios'");

        // ================================================================
        // 2. CORRIGE: agente_ia_configuracoes
        //    A migration anterior usou 'categoria' em vez de 'grupo'
        //    e nao tinha o campo 'sensivel'
        // ================================================================
        if ($this->db->table_exists('agente_ia_configuracoes')) {
            // Verifica se as colunas corretas existem
            $colunas = $this->db->list_fields('agente_ia_configuracoes');

            // Se existe 'categoria' mas nao 'grupo', renomeia
            if (in_array('categoria', $colunas) && !in_array('grupo', $colunas)) {
                $this->db->query("ALTER TABLE `agente_ia_configuracoes`
                    CHANGE COLUMN `categoria` `grupo` VARCHAR(50) NOT NULL DEFAULT 'geral'");
            }

            // Se nao existe 'sensivel', adiciona
            if (!in_array('sensivel', $colunas)) {
                $this->db->query("ALTER TABLE `agente_ia_configuracoes`
                    ADD COLUMN `sensivel` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=ocultar em logs' AFTER `grupo`");
            }

            // Atualiza seeds com configuracoes padrao corretas
            $this->db->query("INSERT INTO `agente_ia_configuracoes`
                (`chave`, `valor`, `descricao`, `grupo`, `sensivel`) VALUES
                ('evolution_url', 'http://192.168.100.238:8091', 'URL da API Evolution (evolution-go)', 'evolution', 0),
                ('evolution_apikey', '', 'API Key da Evolution API', 'evolution', 1),
                ('evolution_instance', '', 'Nome da instancia WhatsApp', 'evolution', 0),
                ('evolution_enabled', '0', 'Ativar integracao Evolution', 'evolution', 0),
                ('n8n_webhook_url', '', 'URL do webhook do n8n', 'n8n', 0),
                ('n8n_apikey', '', 'API Key do n8n', 'n8n', 1),
                ('n8n_enabled', '0', 'Ativar integracao n8n', 'n8n', 0),
                ('llm_provider', 'openrouter', 'Provedor LLM', 'llm', 0),
                ('llm_apikey', '', 'API Key do provedor LLM', 'llm', 1),
                ('llm_model', 'google/gemini-2.5-flash-preview', 'Modelo LLM padrao', 'llm', 0),
                ('llm_system_prompt', 'Voce e um assistente virtual desta empresa. Seja educado, breve e objetivo.', 'Prompt de sistema', 'llm', 0),
                ('llm_enabled', '0', 'Ativar processamento LLM', 'llm', 0),
                ('agente_max_tokens', '4096', 'Maximo de tokens por resposta', 'llm', 0),
                ('agente_timeout', '30', 'Timeout segundos', 'llm', 0),
                ('numero_whatsapp_principal', '', 'Numero principal do atendimento', 'geral', 0),
                ('mensagem_boas_vindas', 'Ola! Sou o assistente virtual. Em que posso ajudar?', 'Mensagem de boas-vindas', 'geral', 0),
                ('horario_atendimento_inicio', '08:00', 'Inicio atendimento automatico', 'geral', 0),
                ('horario_atendimento_fim', '18:00', 'Fim atendimento automatico', 'geral', 0),
                ('auto_responder_fora_horario', '1', 'Auto-responder fora do horario', 'geral', 0)
                ON DUPLICATE KEY UPDATE `valor` = VALUES(`valor`), `grupo` = VALUES(`grupo`), `sensivel` = VALUES(`sensivel`)");
        }

        // ================================================================
        // 3. CORRIGE: agente_ia_logs_conversa
        //    Enum da migration anterior usava 'entrada/saida', mas o banco.sql
        //    usa 'recebido/enviado'
        // ================================================================
        if ($this->db->table_exists('agente_ia_logs_conversa')) {
            $colunas = $this->db->list_fields('agente_ia_logs_conversa');

            // Adiciona campos faltantes: usuarios_id, clientes_id, acao_executada
            if (!in_array('usuarios_id', $colunas)) {
                $this->db->query("ALTER TABLE `agente_ia_logs_conversa`
                    ADD COLUMN `usuarios_id` INT(11) NULL DEFAULT NULL AFTER `numero_telefone`");
            }
            if (!in_array('clientes_id', $colunas)) {
                $this->db->query("ALTER TABLE `agente_ia_logs_conversa`
                    ADD COLUMN `clientes_id` INT(11) NULL DEFAULT NULL AFTER `usuarios_id`");
            }
            if (!in_array('acao_executada', $colunas)) {
                $this->db->query("ALTER TABLE `agente_ia_logs_conversa`
                    ADD COLUMN `acao_executada` VARCHAR(100) NULL AFTER `intencao_detectada`");
            }

            // Corrige ENUM se necessario
            $this->db->query("ALTER TABLE `agente_ia_logs_conversa`
                MODIFY COLUMN `tipo` ENUM('recebido','enviado','sistema','erro')
                NOT NULL DEFAULT 'recebido'");
        }

        // ================================================================
        // 4. CORRIGE: agente_ia_permissoes
        //    Adiciona 'desconhecido' ao ENUM se necessario
        // ================================================================
        if ($this->db->table_exists('agente_ia_permissoes')) {
            // Verifica se existem colunas created_at/updated_at
            $colunas = $this->db->list_fields('agente_ia_permissoes');

            if (!in_array('created_at', $colunas)) {
                $this->db->query("ALTER TABLE `agente_ia_permissoes`
                    ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP");
            }
            if (!in_array('updated_at', $colunas)) {
                $this->db->query("ALTER TABLE `agente_ia_permissoes`
                    ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
            }

            // Indices faltantes
            $this->db->query("ALTER TABLE `agente_ia_permissoes`
                ADD INDEX `idx_acao` (`acao`),
                ADD INDEX `idx_ativo` (`ativo`)");
        }

        // ================================================================
        // 5. CORRIGE: agente_ia_autorizacoes
        //    Adiciona indices faltantes
        // ================================================================
        if ($this->db->table_exists('agente_ia_autorizacoes')) {
            // Adiciona coluna observacoes se necessario
            $colunas = $this->db->list_fields('agente_ia_autorizacoes');
            if (!in_array('observacoes', $colunas)) {
                $this->db->query("ALTER TABLE `agente_ia_autorizacoes`
                    ADD COLUMN `observacoes` TEXT NULL AFTER `resultado_json`");
            }

            // Indices faltantes
            try {
                $this->db->query("ALTER TABLE `agente_ia_autorizacoes`
                    ADD INDEX `idx_expires_at` (`expires_at`),
                    ADD INDEX `idx_acao_status` (`acao`, `status`),
                    ADD INDEX `idx_usuarios_id` (`usuarios_id`),
                    ADD INDEX `idx_clientes_id` (`clientes_id`)");
            } catch (Exception $e) {
                // Indices podem ja existir
            }
        }
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `whatsapp_integracao`");
    }
}
