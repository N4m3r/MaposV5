<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_notificacoes_table extends CI_Migration
{
    public function up()
    {
        // Tabela de notificações internas do sistema
        $this->db->query("CREATE TABLE IF NOT EXISTS `notificacoes` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `usuario_id` INT(11) NOT NULL,
            `tipo_usuario` VARCHAR(20) DEFAULT 'admin',
            `titulo` VARCHAR(200) NOT NULL,
            `mensagem` TEXT NOT NULL,
            `url` VARCHAR(500) NULL,
            `icone` VARCHAR(50) DEFAULT 'bx-bell',
            `tipo` VARCHAR(30) DEFAULT 'info',
            `lida` TINYINT(1) DEFAULT 0,
            `data_notificacao` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `idx_usuario_tipo_lida` (`usuario_id`, `tipo_usuario`, `lida`),
            INDEX `idx_data` (`data_notificacao`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `notificacoes`");
    }
}
