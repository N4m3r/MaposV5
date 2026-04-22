<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration para Sistema de Registro de Atividades de Técnicos
 * Cria tabelas para controle detalhado de atividades durante atendimentos
 */
class Migration_Create_atividades_tables extends CI_Migration
{
    public function up()
    {
        // Tabela de tipos de atividades
        $this->db->query("CREATE TABLE IF NOT EXISTS `atividades_tipos` (
            `idTipo` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(50) NOT NULL,
            `descricao` TEXT,
            `categoria` VARCHAR(30) NOT NULL COMMENT 'rede, cftv, seguranca, eletrica, infra',
            `cor` VARCHAR(7) DEFAULT '#007bff',
            `icone` VARCHAR(30) DEFAULT 'bx-wrench',
            `duracao_estimada` INT COMMENT 'duração estimada em minutos',
            `requer_material` TINYINT(1) DEFAULT 1,
            `requer_foto` TINYINT(1) DEFAULT 0,
            `ordem` INT DEFAULT 0,
            `padrao` TINYINT(1) DEFAULT 0 COMMENT '1=sistema, 0=customizado',
            `ativo` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`idTipo`),
            INDEX `idx_categoria` (`categoria`),
            INDEX `idx_ativo` (`ativo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de atividades realizadas
        $this->db->query("CREATE TABLE IF NOT EXISTS `os_atividades` (
            `idAtividade` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `os_id` INT(11) NOT NULL,
            `checkin_id` INT(11),
            `tecnico_id` INT(11) NOT NULL,
            `tipo_id` INT(11) UNSIGNED,
            `tipo_atividade` VARCHAR(50) NOT NULL,
            `categoria` VARCHAR(30) NOT NULL,
            `descricao` TEXT,
            `equipamento` VARCHAR(100),
            `local_instalacao` VARCHAR(200),
            `hora_inicio` DATETIME NOT NULL,
            `hora_fim` DATETIME,
            `duracao_minutos` INT,
            `pausado_em` DATETIME,
            `status` ENUM('em_andamento', 'finalizada', 'pausada', 'cancelada') DEFAULT 'em_andamento',
            `prioridade` ENUM('baixa', 'normal', 'alta', 'urgente') DEFAULT 'normal',
            `observacoes` TEXT,
            `problemas_encontrados` TEXT,
            `solucao_aplicada` TEXT,
            `concluida` TINYINT(1) DEFAULT 0,
            `motivo_nao_concluida` TEXT,
            `assinatura_cliente` TEXT COMMENT 'base64 da assinatura',
            `assinatura_tecnico` TEXT COMMENT 'base64 da assinatura',
            `foto_checkin` VARCHAR(255) COMMENT 'caminho da foto de entrada',
            `foto_checkout` VARCHAR(255) COMMENT 'caminho da foto de saída',
            `latitude` DECIMAL(10,8),
            `longitude` DECIMAL(11,8),
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`idAtividade`),
            INDEX `idx_os` (`os_id`),
            INDEX `idx_tecnico` (`tecnico_id`),
            INDEX `idx_status` (`status`),
            INDEX `idx_hora_inicio` (`hora_inicio`),
            INDEX `idx_categoria` (`categoria`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de materiais utilizados em cada atividade
        $this->db->query("CREATE TABLE IF NOT EXISTS `atividades_materiais` (
            `idMaterialUso` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `atividade_id` INT(11) UNSIGNED NOT NULL,
            `produto_id` INT(11),
            `nome_produto` VARCHAR(100) NOT NULL,
            `quantidade` DECIMAL(10,2) NOT NULL,
            `unidade` VARCHAR(10) DEFAULT 'un',
            `preco_unitario` DECIMAL(10,2),
            `observacao` VARCHAR(255),
            `baixou_estoque` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`idMaterialUso`),
            INDEX `idx_atividade` (`atividade_id`),
            INDEX `idx_produto` (`produto_id`),
            CONSTRAINT `fk_material_atividade` FOREIGN KEY (`atividade_id`) REFERENCES `os_atividades`(`idAtividade`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de fotos das atividades
        $this->db->query("CREATE TABLE IF NOT EXISTS `atividades_fotos` (
            `idFoto` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `atividade_id` INT(11) UNSIGNED NOT NULL,
            `os_id` INT(11) NOT NULL,
            `tecnico_id` INT(11) NOT NULL,
            `foto_base64` LONGTEXT,
            `caminho_arquivo` VARCHAR(255),
            `tipo_foto` VARCHAR(30) DEFAULT 'execucao' COMMENT 'chegada, execucao, conclusao',
            `descricao` VARCHAR(255),
            `etapa` VARCHAR(20) DEFAULT 'durante',
            `latitude` DECIMAL(10,8),
            `longitude` DECIMAL(11,8),
            `data_hora` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`idFoto`),
            INDEX `idx_atividade_foto` (`atividade_id`),
            INDEX `idx_os_foto` (`os_id`),
            CONSTRAINT `fk_foto_atividade` FOREIGN KEY (`atividade_id`) REFERENCES `os_atividades`(`idAtividade`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de checklist de atividades
        $this->db->query("CREATE TABLE IF NOT EXISTS `atividades_checklist` (
            `idChecklistItem` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `atividade_id` INT(11) UNSIGNED NOT NULL,
            `descricao` VARCHAR(255) NOT NULL,
            `obrigatorio` TINYINT(1) DEFAULT 0,
            `concluido` TINYINT(1) DEFAULT 0,
            `observacao` TEXT,
            `ordem` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`idChecklistItem`),
            INDEX `idx_atividade_check` (`atividade_id`),
            CONSTRAINT `fk_checklist_atividade` FOREIGN KEY (`atividade_id`) REFERENCES `os_atividades`(`idAtividade`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de pausas nas atividades
        $this->db->query("CREATE TABLE IF NOT EXISTS `atividades_pausas` (
            `idPausa` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `atividade_id` INT(11) UNSIGNED NOT NULL,
            `pausa_inicio` DATETIME NOT NULL,
            `pausa_fim` DATETIME,
            `motivo` VARCHAR(100),
            `observacao` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`idPausa`),
            INDEX `idx_atividade_pausa` (`atividade_id`),
            CONSTRAINT `fk_pausa_atividade` FOREIGN KEY (`atividade_id`) REFERENCES `os_atividades`(`idAtividade`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insere tipos de atividades padrão para empresa de tecnologia
        $this->inserirTiposAtividades();
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `atividades_pausas`");
        $this->db->query("DROP TABLE IF EXISTS `atividades_checklist`");
        $this->db->query("DROP TABLE IF EXISTS `atividades_fotos`");
        $this->db->query("DROP TABLE IF EXISTS `atividades_materiais`");
        $this->db->query("DROP TABLE IF EXISTS `os_atividades`");
        $this->db->query("DROP TABLE IF EXISTS `atividades_tipos`");
    }

    private function inserirTiposAtividades()
    {
        $tipos = [
            // REDE ESTRUTURADA
            ['nome' => 'Passagem de Cabo de Rede', 'categoria' => 'rede', 'cor' => '#007bff', 'icone' => 'bx-cable', 'duracao_estimada' => 30, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Crimpagem de Conectores', 'categoria' => 'rede', 'cor' => '#0069d9', 'icone' => 'bx-plug', 'duracao_estimada' => 15, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Instalação de Patch Panel', 'categoria' => 'rede', 'cor' => '#0056b3', 'icone' => 'bx-server', 'duracao_estimada' => 45, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Configuração de Switch', 'categoria' => 'rede', 'cor' => '#004494', 'icone' => 'bx-network-chart', 'duracao_estimada' => 30, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Teste de Conectividade', 'categoria' => 'rede', 'cor' => '#28a745', 'icone' => 'bx-check-circle', 'duracao_estimada' => 15, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Organização de Rack', 'categoria' => 'rede', 'cor' => '#20c997', 'icone' => 'bx-list-check', 'duracao_estimada' => 60, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Certificação de Cabo', 'categoria' => 'rede', 'cor' => '#17a2b8', 'icone' => 'bx-certification', 'duracao_estimada' => 20, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Montagem de Rack', 'categoria' => 'rede', 'cor' => '#138496', 'icone' => 'bx-customize', 'duracao_estimada' => 90, 'requer_material' => 1, 'padrao' => 1],

            // CFTV IP
            ['nome' => 'Instalação de Câmera IP', 'categoria' => 'cftv', 'cor' => '#dc3545', 'icone' => 'bx-camera', 'duracao_estimada' => 60, 'requer_material' => 1, 'requer_foto' => 1, 'padrao' => 1],
            ['nome' => 'Instalação de DVR/NVR', 'categoria' => 'cftv', 'cor' => '#c82333', 'icone' => 'bx-hdd', 'duracao_estimada' => 45, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Configuração do Gravador', 'categoria' => 'cftv', 'cor' => '#bd2130', 'icone' => 'bx-cog', 'duracao_estimada' => 30, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Configuração App Cliente', 'categoria' => 'cftv', 'cor' => '#b21f2d', 'icone' => 'bx-mobile', 'duracao_estimada' => 15, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Teste de Imagem', 'categoria' => 'cftv', 'cor' => '#28a745', 'icone' => 'bx-video', 'duracao_estimada' => 10, 'requer_material' => 0, 'requer_foto' => 1, 'padrao' => 1],
            ['nome' => 'Ajuste de Posição', 'categoria' => 'cftv', 'cor' => '#20c997', 'icone' => 'bx-move', 'duracao_estimada' => 15, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Limpeza de Lente', 'categoria' => 'cftv', 'cor' => '#6c757d', 'icone' => 'bx-brush', 'duracao_estimada' => 5, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Troca de Câmera', 'categoria' => 'cftv', 'cor' => '#e74c3c', 'icone' => 'bx-refresh', 'duracao_estimada' => 30, 'requer_material' => 1, 'requer_foto' => 1, 'padrao' => 1],
            ['nome' => 'Manutenção Preventiva', 'categoria' => 'cftv', 'cor' => '#f39c12', 'icone' => 'bx-wrench', 'duracao_estimada' => 45, 'requer_material' => 0, 'padrao' => 1],

            // SISTEMAS DE SEGURANÇA
            ['nome' => 'Instalação de Sensor', 'categoria' => 'seguranca', 'cor' => '#6f42c1', 'icone' => 'bx-radar', 'duracao_estimada' => 30, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Instalação de Teclado', 'categoria' => 'seguranca', 'cor' => '#5a32a3', 'icone' => 'bx-keyboard', 'duracao_estimada' => 25, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Instalação de Fechadura', 'categoria' => 'seguranca', 'cor' => '#4e2a8d', 'icone' => 'bx-lock', 'duracao_estimada' => 45, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Instalação de Leitor', 'categoria' => 'seguranca', 'cor' => '#432477', 'icone' => 'bx-id-card', 'duracao_estimada' => 30, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Configuração Central Alarme', 'categoria' => 'seguranca', 'cor' => '#3d216e', 'icone' => 'bx-bell', 'duracao_estimada' => 40, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Programação de Controles', 'categoria' => 'seguranca', 'cor' => '#361e62', 'icone' => 'bx-remote', 'duracao_estimada' => 20, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Teste de Sensores', 'categoria' => 'seguranca', 'cor' => '#28a745', 'icone' => 'bx-test-tube', 'duracao_estimada' => 15, 'requer_material' => 0, 'padrao' => 1],

            // INFRAESTRUTURA
            ['nome' => 'Instalação de Eletroduto', 'categoria' => 'infra', 'cor' => '#fd7e14', 'icone' => 'bx-minus-front', 'duracao_estimada' => 60, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Instalação de Eletrocalha', 'categoria' => 'infra', 'cor' => '#e56b1f', 'icone' => 'bx-minus-back', 'duracao_estimada' => 90, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Instalação de Tomada/Ponto', 'categoria' => 'infra', 'cor' => '#e74c3c', 'icone' => 'bx-outlet', 'duracao_estimada' => 20, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Instalação de Quadro', 'categoria' => 'infra', 'cor' => '#c0392b', 'icone' => 'bx-grid', 'duracao_estimada' => 60, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Instalação de UPS/Nobreak', 'categoria' => 'infra', 'cor' => '#f1c40f', 'icone' => 'bx-battery', 'duracao_estimada' => 30, 'requer_material' => 1, 'padrao' => 1],

            // INTERNET/REDES
            ['nome' => 'Instalação de Roteador', 'categoria' => 'internet', 'cor' => '#3498db', 'icone' => 'bx-wifi', 'duracao_estimada' => 20, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Configuração de WiFi', 'categoria' => 'internet', 'cor' => '#2980b9', 'icone' => 'bx-wifi-1', 'duracao_estimada' => 15, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Configuração de VPN', 'categoria' => 'internet', 'cor' => '#1abc9c', 'icone' => 'bx-shield', 'duracao_estimada' => 30, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Configuração de Firewall', 'categoria' => 'internet', 'cor' => '#16a085', 'icone' => 'bx-shield-alt', 'duracao_estimada' => 45, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Diagnóstico de Rede', 'categoria' => 'internet', 'cor' => '#9b59b6', 'icone' => 'bx-pulse', 'duracao_estimada' => 30, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Ajuste de Link Internet', 'categoria' => 'internet', 'cor' => '#8e44ad', 'icone' => 'bx-globe', 'duracao_estimada' => 25, 'requer_material' => 0, 'padrao' => 1],

            // SERVIÇOS GERAIS
            ['nome' => 'Atendimento Preventiva', 'categoria' => 'geral', 'cor' => '#95a5a6', 'icone' => 'bx-calendar-check', 'duracao_estimada' => 120, 'requer_material' => 0, 'padrao' => 1],
            ['nome' => 'Correção de Defeito', 'categoria' => 'geral', 'cor' => '#e67e22', 'icone' => 'bx-error', 'duracao_estimada' => 60, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Troca de Equipamento', 'categoria' => 'geral', 'cor' => '#d35400', 'icone' => 'bx-refresh', 'duracao_estimada' => 30, 'requer_material' => 1, 'padrao' => 1],
            ['nome' => 'Orientação ao Cliente', 'categoria' => 'geral', 'cor' => '#27ae60', 'icone' => 'bx-user-voice', 'duracao_estimada' => 15, 'requer_material' => 0, 'padrao' => 1],
        ];

        foreach ($tipos as $i => $tipo) {
            $tipo['ordem'] = $i;
            $this->db->insert('atividades_tipos', $tipo);
        }
    }
}
