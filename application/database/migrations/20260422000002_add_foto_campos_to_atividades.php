<?php

/**
 * Migração para adicionar campos de foto na tabela os_atividades
 * Data: 2026-04-22
 */
class Migration_Add_foto_campos_to_atividades extends CI_Migration
{
    public function up()
    {
        // Adicionar campo foto_checkin
        if (!$this->db->field_exists('foto_checkin', 'os_atividades')) {
            $this->db->query("ALTER TABLE `os_atividades`
                ADD COLUMN `foto_checkin` VARCHAR(255) NULL COMMENT 'caminho da foto de entrada' AFTER `assinatura_tecnico`");
        }

        // Adicionar campo foto_checkout
        if (!$this->db->field_exists('foto_checkout', 'os_atividades')) {
            $this->db->query("ALTER TABLE `os_atividades`
                ADD COLUMN `foto_checkout` VARCHAR(255) NULL COMMENT 'caminho da foto de saída' AFTER `foto_checkin`");
        }

        echo "Campos foto_checkin e foto_checkout adicionados com sucesso!\n";
    }

    public function down()
    {
        // Remover campos adicionados
        if ($this->db->field_exists('foto_checkin', 'os_atividades')) {
            $this->db->query("ALTER TABLE `os_atividades` DROP COLUMN `foto_checkin`");
        }

        if ($this->db->field_exists('foto_checkout', 'os_atividades')) {
            $this->db->query("ALTER TABLE `os_atividades` DROP COLUMN `foto_checkout`");
        }
    }
}
