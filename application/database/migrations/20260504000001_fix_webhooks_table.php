<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Fix_webhooks_table
 * Corrige inconsistencia de colunas na tabela webhooks:
 * - Renomeia is_active -> active
 * - Adiciona description (faltava)
 *
 * Data: 2026-05-04
 */
class Migration_Fix_webhooks_table extends CI_Migration
{
    public function up()
    {
        // 1. Verifica se tabela existe
        if (!$this->db->table_exists('webhooks')) {
            return;
        }

        $fields = $this->db->list_fields('webhooks');

        // 2. Renomeia is_active -> active
        if (in_array('is_active', $fields) && !in_array('active', $fields)) {
            $this->db->query("ALTER TABLE `webhooks` CHANGE `is_active` `active` TINYINT(1) NOT NULL DEFAULT 1");
        }

        // 3. Adiciona description se nao existir
        if (!in_array('description', $fields)) {
            $this->db->query("ALTER TABLE `webhooks` ADD `description` VARCHAR(255) NULL AFTER `secret`");
        }
    }

    public function down()
    {
        if (!$this->db->table_exists('webhooks')) {
            return;
        }

        $fields = $this->db->list_fields('webhooks');

        if (in_array('active', $fields) && !in_array('is_active', $fields)) {
            $this->db->query("ALTER TABLE `webhooks` CHANGE `active` `is_active` TINYINT(1) NOT NULL DEFAULT 1");
        }

        if (in_array('description', $fields)) {
            $this->db->query("ALTER TABLE `webhooks` DROP COLUMN `description`");
        }
    }
}
