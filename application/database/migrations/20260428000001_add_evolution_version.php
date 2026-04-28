<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Adiciona coluna evolution_version à tabela notificacoes_config
 * Corrige erro: Unknown column 'evolution_version' in 'SET'
 */
class Migration_Add_evolution_version_to_notificacoes_config extends CI_Migration
{
    public function up()
    {
        // Adiciona coluna evolution_version se nao existir
        if (!$this->db->field_exists('evolution_version', 'notificacoes_config')) {
            $this->db->query("ALTER TABLE `notificacoes_config`
                ADD COLUMN `evolution_version` ENUM('v1','v2','go') DEFAULT 'v2'
                AFTER `evolution_instance`");
            log_message('info', 'Migration: coluna evolution_version adicionada a notificacoes_config');
        }

        // Garante que o registro padrao exista
        $this->db->query("INSERT INTO `notificacoes_config` (`id`, `whatsapp_provedor`, `whatsapp_ativo`, `evolution_instance`, `evolution_version`)
            VALUES (1, 'desativado', 0, 'mapos', 'v2')
            ON DUPLICATE KEY UPDATE id=id");
    }

    public function down()
    {
        if ($this->db->field_exists('evolution_version', 'notificacoes_config')) {
            $this->db->query("ALTER TABLE `notificacoes_config` DROP COLUMN `evolution_version`");
        }
    }
}
