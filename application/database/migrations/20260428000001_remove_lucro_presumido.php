<?php
/**
 * Migration: Remove Lucro Presumido do banco de dados
 *
 * Atualiza ENUMs das colunas de regime tributario para permitir
 * apenas 'simples_nacional', removendo 'lucro_presumido' e 'lucro_real'.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Remove_lucro_presumido extends CI_Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

        // Atualizar registros existentes para simples_nacional
        if ($this->db->table_exists('os_nfse_emitida')) {
            if ($this->db->field_exists('regime_tributario', 'os_nfse_emitida')) {
                $this->db->query("UPDATE `os_nfse_emitida` SET `regime_tributario` = 'simples_nacional' WHERE `regime_tributario` != 'simples_nacional'");
                $this->db->query("ALTER TABLE `os_nfse_emitida` MODIFY `regime_tributario` ENUM('simples_nacional') NOT NULL DEFAULT 'simples_nacional' COMMENT 'Regime tributario'");
            }
        }

        if ($this->db->table_exists('impostos_config')) {
            if ($this->db->field_exists('tipo_regime', 'impostos_config')) {
                $this->db->query("UPDATE `impostos_config` SET `tipo_regime` = 'simples_nacional' WHERE `tipo_regime` != 'simples_nacional'");
                $this->db->query("ALTER TABLE `impostos_config` MODIFY `tipo_regime` ENUM('simples_nacional') DEFAULT 'simples_nacional'");
            }
        }

        // Atualizar config padrao
        if ($this->db->table_exists('config_sistema_impostos')) {
            $this->db->where('chave', 'IMPOSTO_REGIME_TRIBUTARIO');
            $this->db->update('config_sistema_impostos', ['valor' => 'simples_nacional', 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        log_message('info', 'Migration Remove Lucro Presumido executada com sucesso.');
    }

    public function down()
    {
        log_message('info', 'Migration Remove Lucro Presumido: rollback desabilitado.');
    }
}
