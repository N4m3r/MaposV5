<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration para criar tabelas de configuracao do sistema de obras
 */
class Migration_Create_obras_config_tables extends CI_Migration
{
    public function up()
    {
        // Tabela de tipos de obra
        $this->db->query("CREATE TABLE IF NOT EXISTS `obra_tipos` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `descricao` TEXT,
            `cor` VARCHAR(7) DEFAULT '#3498db',
            `icone` VARCHAR(50) DEFAULT 'bx-building',
            `ativo` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_obra_tipos_nome` (`nome`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de status de obra
        $this->db->query("CREATE TABLE IF NOT EXISTS `obra_status` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `descricao` TEXT,
            `cor` VARCHAR(7) DEFAULT '#3498db',
            `icone` VARCHAR(50) DEFAULT 'bx-flag',
            `ordem` INT DEFAULT 1,
            `finalizado` TINYINT(1) DEFAULT 0,
            `ativo` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_obra_status_nome` (`nome`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de especialidades
        $this->db->query("CREATE TABLE IF NOT EXISTS `obra_especialidades` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `descricao` TEXT,
            `cor` VARCHAR(7) DEFAULT '#3498db',
            `icone` VARCHAR(50) DEFAULT 'bx-hard-hat',
            `ativo` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_obra_especialidades_nome` (`nome`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de funcoes da equipe
        $this->db->query("CREATE TABLE IF NOT EXISTS `obra_funcoes` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `descricao` TEXT,
            `nivel` VARCHAR(20) DEFAULT 'baixo',
            `ativo` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_obra_funcoes_nome` (`nome`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de status de atividade
        $this->db->query("CREATE TABLE IF NOT EXISTS `atividade_status` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `descricao` TEXT,
            `cor` VARCHAR(7) DEFAULT '#3498db',
            `icone` VARCHAR(50) DEFAULT 'bx-calendar',
            `fluxo` VARCHAR(30) DEFAULT 'normal',
            `ordem` INT DEFAULT 1,
            `ativo` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_atividade_status_nome` (`nome`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Inserir dados padrao
        $this->inserirDadosPadrao();
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `atividade_status`");
        $this->db->query("DROP TABLE IF EXISTS `obra_funcoes`");
        $this->db->query("DROP TABLE IF EXISTS `obra_especialidades`");
        $this->db->query("DROP TABLE IF EXISTS `obra_status`");
        $this->db->query("DROP TABLE IF EXISTS `obra_tipos`");
    }

    private function inserirDadosPadrao()
    {
        // Tipos de obra padrao
        $tiposObra = [
            ['nome' => 'Reforma', 'descricao' => 'Reformas e renovacoes', 'cor' => '#3498db', 'icone' => 'bx-brush'],
            ['nome' => 'Construcao', 'descricao' => 'Obras novas', 'cor' => '#27ae60', 'icone' => 'bx-building'],
            ['nome' => 'Manutencao', 'descricao' => 'Manutencoes corretivas', 'cor' => '#f39c12', 'icone' => 'bx-wrench'],
            ['nome' => 'Instalacao', 'descricao' => 'Instalacoes diversas', 'cor' => '#9b59b6', 'icone' => 'bx-plug'],
        ];
        foreach ($tiposObra as $tipo) {
            $this->db->insert('obra_tipos', $tipo);
        }

        // Status de obra padrao
        $statusObra = [
            ['nome' => 'Prospeccao', 'descricao' => 'Obra em fase inicial/orcamento', 'cor' => '#95a5a6', 'icone' => 'bx-search', 'ordem' => 1, 'finalizado' => 0],
            ['nome' => 'Em Andamento', 'descricao' => 'Obra em execucao', 'cor' => '#3498db', 'icone' => 'bx-play-circle', 'ordem' => 2, 'finalizado' => 0],
            ['nome' => 'Concluida', 'descricao' => 'Obra finalizada', 'cor' => '#27ae60', 'icone' => 'bx-check-circle', 'ordem' => 3, 'finalizado' => 1],
        ];
        foreach ($statusObra as $status) {
            $this->db->insert('obra_status', $status);
        }

        // Especialidades padrao
        $especialidades = [
            ['nome' => 'Eletrica', 'descricao' => 'Instalacoes eletricas', 'cor' => '#f1c40f', 'icone' => 'bx-bolt-circle'],
            ['nome' => 'Hidraulica', 'descricao' => 'Instalacoes hidraulicas', 'cor' => '#3498db', 'icone' => 'bx-water'],
            ['nome' => 'Acabamento', 'descricao' => 'Pintura, revestimentos, pisos', 'cor' => '#e74c3c', 'icone' => 'bx-brush'],
            ['nome' => 'Estrutura', 'descricao' => 'Alvenaria, concreto, fundacao', 'cor' => '#95a5a6', 'icone' => 'bx-building'],
        ];
        foreach ($especialidades as $esp) {
            $this->db->insert('obra_especialidades', $esp);
        }

        // Funcoes padrao
        $funcoes = [
            ['nome' => 'Engenheiro Responsavel', 'descricao' => 'Responsavel tecnico pela obra', 'nivel' => 'alto'],
            ['nome' => 'Mestre de Obras', 'descricao' => 'Coordenacao da equipe', 'nivel' => 'medio'],
            ['nome' => 'Auxiliar', 'descricao' => 'Apoio nas atividades', 'nivel' => 'baixo'],
        ];
        foreach ($funcoes as $funcao) {
            $this->db->insert('obra_funcoes', $funcao);
        }

        // Status de atividade padrao
        $statusAtividade = [
            ['nome' => 'Agendada', 'descricao' => 'Atividade agendada', 'cor' => '#95a5a6', 'icone' => 'bx-calendar', 'fluxo' => 'inicial', 'ordem' => 1],
            ['nome' => 'Iniciada', 'descricao' => 'Atividade em execucao', 'cor' => '#3498db', 'icone' => 'bx-play-circle', 'fluxo' => 'normal', 'ordem' => 2],
            ['nome' => 'Pausada', 'descricao' => 'Atividade pausada', 'cor' => '#f39c12', 'icone' => 'bx-pause-circle', 'fluxo' => 'pausa', 'ordem' => 3],
            ['nome' => 'Concluida', 'descricao' => 'Atividade finalizada', 'cor' => '#27ae60', 'icone' => 'bx-check-circle', 'fluxo' => 'final', 'ordem' => 4],
            ['nome' => 'Cancelada', 'descricao' => 'Atividade cancelada', 'cor' => '#e74c3c', 'icone' => 'bx-x-circle', 'fluxo' => 'final', 'ordem' => 5],
        ];
        foreach ($statusAtividade as $status) {
            $this->db->insert('atividade_status', $status);
        }
    }
}
