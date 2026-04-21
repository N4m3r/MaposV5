<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Integração Atividades (Hora Início/Fim) com Etapas da Obra
 *
 * Adiciona campo etapa_id em os_atividades e cria tabela de vínculo
 * entre atividades registradas e atividades planejadas
 */
class Migration_Integrar_atividades_etapas extends CI_Migration
{
    public function up()
    {
        // Verifica se a tabela os_atividades existe
        if (!$this->db->table_exists('os_atividades')) {
            echo "Tabela os_atividades não existe. Crie o sistema de atividades primeiro.\n";
            return false;
        }

        // Adiciona campo etapa_id em os_atividades (se não existir)
        $campos_existentes = $this->db->field_exists('etapa_id', 'os_atividades');
        if (!$campos_existentes) {
            $this->db->query("ALTER TABLE `os_atividades`
                ADD COLUMN `etapa_id` INT(11) NULL AFTER `obra_id`,
                ADD INDEX `idx_etapa` (`etapa_id`)");
            echo "✓ Campo etapa_id adicionado em os_atividades\n";
        } else {
            echo "= Campo etapa_id já existe\n";
        }

        // Adiciona campo obra_atividade_id (vincula com atividade planejada)
        $campos_existentes = $this->db->field_exists('obra_atividade_id', 'os_atividades');
        if (!$campos_existentes) {
            $this->db->query("ALTER TABLE `os_atividades`
                ADD COLUMN `obra_atividade_id` INT(11) NULL AFTER `etapa_id`,
                ADD INDEX `idx_obra_atividade` (`obra_atividade_id`)");
            echo "✓ Campo obra_atividade_id adicionado em os_atividades\n";
        } else {
            echo "= Campo obra_atividade_id já existe\n";
        }

        // Cria tabela de vínculo entre atividade registrada e etapa
        if (!$this->db->table_exists('obra_atividades_vinculo')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `obra_atividades_vinculo` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `atividade_realizada_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID da os_atividades',
                `obra_atividade_id` INT(11) NULL COMMENT 'ID da obra_atividades (planejada)',
                `etapa_id` INT(11) NOT NULL,
                `obra_id` INT(11) NOT NULL,
                `tecnico_id` INT(11) NOT NULL,
                `data_vinculo` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_atividade_realizada` (`atividade_realizada_id`),
                INDEX `idx_etapa_vinculo` (`etapa_id`),
                INDEX `idx_obra_vinculo` (`obra_id`),
                CONSTRAINT `fk_vinculo_atividade` FOREIGN KEY (`atividade_realizada_id`)
                    REFERENCES `os_atividades`(`idAtividade`) ON DELETE CASCADE,
                CONSTRAINT `fk_vinculo_etapa` FOREIGN KEY (`etapa_id`)
                    REFERENCES `obra_etapas`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
            echo "✓ Tabela obra_atividades_vinculo criada\n";
        } else {
            echo "= Tabela obra_atividades_vinculo já existe\n";
        }

        // Adiciona campo progresso_real em obra_etapas (para controle real vs planejado)
        if ($this->db->table_exists('obra_etapas')) {
            $campos_existentes = $this->db->field_exists('progresso_real', 'obra_etapas');
            if (!$campos_existentes) {
                $this->db->query("ALTER TABLE `obra_etapas`
                    ADD COLUMN `progresso_real` INT(3) DEFAULT 0 COMMENT 'Progresso baseado nas atividades registradas'");
                echo "✓ Campo progresso_real adicionado em obra_etapas\n";
            } else {
                echo "= Campo progresso_real já existe\n";
            }
        }

        echo "\n✅ Integração de Atividades com Etapas concluída!\n";
        return true;
    }

    public function down()
    {
        // Remove campos adicionados
        if ($this->db->field_exists('etapa_id', 'os_atividades')) {
            $this->db->query("ALTER TABLE `os_atividades` DROP COLUMN `etapa_id`");
        }
        if ($this->db->field_exists('obra_atividade_id', 'os_atividades')) {
            $this->db->query("ALTER TABLE `os_atividades` DROP COLUMN `obra_atividade_id`");
        }
        if ($this->db->field_exists('progresso_real', 'obra_etapas')) {
            $this->db->query("ALTER TABLE `obra_etapas` DROP COLUMN `progresso_real`");
        }

        // Remove tabela de vínculo
        $this->db->query("DROP TABLE IF EXISTS `obra_atividades_vinculo`");

        echo "Rollback concluído.\n";
    }
}
