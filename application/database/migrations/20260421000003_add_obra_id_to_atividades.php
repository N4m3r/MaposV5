<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration para integrar Sistema de Atividades com Obras
 * Adiciona campo obra_id para vincular atividades a obras
 */
class Migration_Add_obra_id_to_atividades extends CI_Migration
{
    public function up()
    {
        // Adiciona campo obra_id na tabela os_atividades
        $this->db->query("ALTER TABLE `os_atividades`
            ADD COLUMN IF NOT EXISTS `obra_id` INT(11) NULL AFTER `os_id`,
            ADD INDEX IF NOT EXISTS `idx_obra` (`obra_id`)");

        // Tabela de vinculação entre etapas de obra e tipos de atividades
        $this->db->query("CREATE TABLE IF NOT EXISTS `obra_etapa_atividades_tipos` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `etapa_id` INT(11) NOT NULL,
            `obra_id` INT(11) NOT NULL,
            `tipo_atividade_id` INT(11) UNSIGNED NOT NULL,
            `ordem` INT DEFAULT 0,
            `obrigatorio` TINYINT(1) DEFAULT 0,
            `duracao_estimada` INT COMMENT 'minutos estimados',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_etapa` (`etapa_id`),
            INDEX `idx_obra` (`obra_id`),
            CONSTRAINT `fk_etapa_atividade` FOREIGN KEY (`etapa_id`)
                REFERENCES `obra_etapas`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_tipo_atividade_etapa` FOREIGN KEY (`tipo_atividade_id`)
                REFERENCES `atividades_tipos`(`idTipo`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela para fotos das atividades de obra (vinculadas ao novo sistema)
        $this->db->query("CREATE TABLE IF NOT EXISTS `obra_atividades_fotos` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `atividade_id` INT(11) UNSIGNED NOT NULL,
            `obra_id` INT(11) NOT NULL,
            `etapa_id` INT(11) NULL,
            `caminho_arquivo` VARCHAR(255) NOT NULL,
            `tipo` ENUM('checkin', 'execucao', 'checkout', 'problema') DEFAULT 'execucao',
            `descricao` VARCHAR(255),
            `latitude` DECIMAL(10,8),
            `longitude` DECIMAL(11,8),
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_atividade_foto` (`atividade_id`),
            INDEX `idx_obra_foto` (`obra_id`),
            CONSTRAINT `fk_foto_atividade_obra` FOREIGN KEY (`atividade_id`)
                REFERENCES `os_atividades`(`idAtividade`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        echo "Integração criada com sucesso!\n";
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `obra_etapa_atividades_tipos`");
        $this->db->query("DROP TABLE IF EXISTS `obra_atividades_fotos`");
        $this->db->query("ALTER TABLE `os_atividades` DROP COLUMN IF EXISTS `obra_id`");
    }
}
