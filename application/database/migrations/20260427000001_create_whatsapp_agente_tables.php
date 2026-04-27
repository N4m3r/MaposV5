<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_whatsapp_agente_tables extends CI_Migration
{
    public function up()
    {
        // Tabela de vinculo numero <-> cliente/tecnico
        $this->db->query("CREATE TABLE IF NOT EXISTS `whatsapp_integracao` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `numero_telefone` VARCHAR(20) NOT NULL COMMENT 'Formato: 559292150107',
            `tipo_vinculo` ENUM('cliente','tecnico','admin','desconhecido') NOT NULL DEFAULT 'desconhecido',
            `clientes_id` INT(11) NULL,
            `usuarios_id` INT(11) NULL,
            `situacao` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
            `ultima_interacao` DATETIME NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_numero` (`numero_telefone`),
            FOREIGN KEY (`clientes_id`) REFERENCES `clientes`(`idClientes`) ON DELETE SET NULL,
            FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios`(`idUsuarios`) ON DELETE SET NULL,
            INDEX `idx_situacao` (`situacao`),
            INDEX `idx_tipo` (`tipo_vinculo`),
            INDEX `idx_cliente` (`clientes_id`),
            INDEX `idx_usuario` (`usuarios_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Tabela de log de interacoes
        $this->db->query("CREATE TABLE IF NOT EXISTS `whatsapp_log_interacoes` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `numero_telefone` VARCHAR(20) NOT NULL,
            `tipo_mensagem` ENUM('texto','audio','imagem','documento') NOT NULL DEFAULT 'texto',
            `direcao` ENUM('entrada','saida') NOT NULL,
            `conteudo` TEXT NULL,
            `intencao_detectada` VARCHAR(100) NULL,
            `os_id` INT(11) NULL,
            `status` ENUM('recebido','respondido','erro') DEFAULT 'recebido',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_numero` (`numero_telefone`),
            INDEX `idx_created` (`created_at`),
            INDEX `idx_os` (`os_id`),
            INDEX `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `whatsapp_log_interacoes`");
        $this->db->query("DROP TABLE IF EXISTS `whatsapp_integracao`");
    }
}
