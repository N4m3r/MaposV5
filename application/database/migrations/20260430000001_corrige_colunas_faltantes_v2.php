<?php
/**
 * Migration: Corrige colunas faltantes em tabelas ja existentes
 *
 * Adiciona:
 *  - certificado_nfe_importada.os_id
 *  - notificacoes.tipo_usuario
 *  - email_queue.scheduled_at
 *
 * Segura para executar multiplas vezes.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Corrige_colunas_faltantes_v2 extends CI_Migration
{
    public function up()
    {
        // 1. certificado_nfe_importada.os_id
        if ($this->db->table_exists('certificado_nfe_importada') && !$this->db->field_exists('os_id', 'certificado_nfe_importada')) {
            $this->db->query("ALTER TABLE `certificado_nfe_importada` ADD COLUMN `os_id` INT(11) UNSIGNED NULL AFTER `dados_xml`");
            log_message('info', 'Coluna certificado_nfe_importada.os_id adicionada');
        }

        // 2. notificacoes.tipo_usuario
        if ($this->db->table_exists('notificacoes') && !$this->db->field_exists('tipo_usuario', 'notificacoes')) {
            $this->db->query("ALTER TABLE `notificacoes` ADD COLUMN `tipo_usuario` VARCHAR(50) NULL AFTER `usuario_id`");
            log_message('info', 'Coluna notificacoes.tipo_usuario adicionada');
        }

        // 3. email_queue.scheduled_at
        if ($this->db->table_exists('email_queue') && !$this->db->field_exists('scheduled_at', 'email_queue')) {
            $this->db->query("ALTER TABLE `email_queue` ADD COLUMN `scheduled_at` DATETIME NULL AFTER `status`");
            log_message('info', 'Coluna email_queue.scheduled_at adicionada');
        }

        // 4. certificado_digital.ambiente
        if ($this->db->table_exists('certificado_digital') && !$this->db->field_exists('ambiente', 'certificado_digital')) {
            $this->db->query("ALTER TABLE `certificado_digital` ADD COLUMN `ambiente` ENUM('homologacao','producao') DEFAULT 'homologacao' AFTER `ativo`");
            log_message('info', 'Coluna certificado_digital.ambiente adicionada');
        }

        log_message('info', 'Migration corrige_colunas_faltantes_v2 executada com sucesso.');
    }

    public function down()
    {
        log_message('info', 'Migration corrige_colunas_faltantes_v2: rollback desabilitado.');
    }
}
