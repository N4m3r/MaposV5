<?php
/**
 * Migration: Adicionar n_dps à tabela os_nfse_emitida
 * Para controle sequencial do número da DPS
 */

class Migration_add_n_dps_to_os_nfse_emitida extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('os_nfse_emitida')) {
            if (!$this->db->field_exists('n_dps', 'os_nfse_emitida')) {
                $this->db->query("ALTER TABLE `os_nfse_emitida` ADD COLUMN `n_dps` VARCHAR(15) NULL COMMENT 'Número da DPS (sequencial)' AFTER `numero_nfse`");
                log_message('info', 'Coluna os_nfse_emitida.n_dps adicionada');
            }
            if (!$this->db->field_exists('serie_dps', 'os_nfse_emitida')) {
                $this->db->query("ALTER TABLE `os_nfse_emitida` ADD COLUMN `serie_dps` VARCHAR(5) NOT NULL DEFAULT '1' COMMENT 'Série da DPS' AFTER `n_dps`");
                log_message('info', 'Coluna os_nfse_emitida.serie_dps adicionada');
            }
        }
    }

    public function down()
    {
        if ($this->db->table_exists('os_nfse_emitida')) {
            if ($this->db->field_exists('n_dps', 'os_nfse_emitida')) {
                $this->db->query("ALTER TABLE `os_nfse_emitida` DROP COLUMN `n_dps`");
            }
            if ($this->db->field_exists('serie_dps', 'os_nfse_emitida')) {
                $this->db->query("ALTER TABLE `os_nfse_emitida` DROP COLUMN `serie_dps`");
            }
        }
    }
}
