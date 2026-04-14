<?php
/**
 * Migration: Create Checklist Tables
 * Cria tabelas para checklist técnico e acompanhamento
 */

require_once 'MigrationRunner.php';

class CreateChecklistTables extends Migration
{
    public function up()
    {
        // Tabela de tipos de checklist (modelos)
        $this->db->query("CREATE TABLE IF NOT EXISTS checklist_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            categoria VARCHAR(50) DEFAULT 'geral',
            ativo TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Tabela de itens do checklist template
        $this->db->query("CREATE TABLE IF NOT EXISTS checklist_template_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            template_id INT NOT NULL,
            ordem INT DEFAULT 0,
            descricao VARCHAR(255) NOT NULL,
            tipo VARCHAR(20) DEFAULT 'checkbox',
            obrigatorio TINYINT(1) DEFAULT 0,
            opcoes TEXT NULL,
            FOREIGN KEY (template_id) REFERENCES checklist_templates(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Tabela de checklist por OS
        $this->db->query("CREATE TABLE IF NOT EXISTS os_checklist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            os_id INT NOT NULL,
            template_id INT NULL,
            item_id INT NOT NULL,
            descricao VARCHAR(255) NOT NULL,
            status ENUM('pendente', 'ok', 'nao_aplicavel', 'com_problema') DEFAULT 'pendente',
            observacao TEXT NULL,
            evidencia_foto VARCHAR(255) NULL,
            verificado_por INT NULL,
            verificado_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_os (os_id),
            INDEX idx_status (status),
            FOREIGN KEY (os_id) REFERENCES os(idOs) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Tabela de timeline de atividades
        $this->db->query("CREATE TABLE IF NOT EXISTS os_timeline (
            id INT AUTO_INCREMENT PRIMARY KEY,
            os_id INT NOT NULL,
            tipo VARCHAR(50) NOT NULL,
            titulo VARCHAR(255) NOT NULL,
            descricao TEXT NULL,
            usuario_id INT NULL,
            usuario_nome VARCHAR(100) NULL,
            metadata JSON NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_os (os_id),
            INDEX idx_tipo (tipo),
            INDEX idx_created (created_at),
            FOREIGN KEY (os_id) REFERENCES os(idOs) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Tabela de peças/insumos utilizados
        $this->db->query("CREATE TABLE IF NOT EXISTS os_pecas_utilizadas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            os_id INT NOT NULL,
            produto_id INT NULL,
            nome_peca VARCHAR(255) NOT NULL,
            codigo VARCHAR(100) NULL,
            quantidade DECIMAL(10,2) DEFAULT 1,
            valor_unitario DECIMAL(10,2) DEFAULT 0,
            valor_total DECIMAL(10,2) DEFAULT 0,
            tipo ENUM('produto', 'servico', 'insumo', 'outro') DEFAULT 'produto',
            instalado_por INT NULL,
            instalado_at TIMESTAMP NULL,
            garantia_dias INT DEFAULT 0,
            observacao TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_os (os_id),
            INDEX idx_produto (produto_id),
            FOREIGN KEY (os_id) REFERENCES os(idOs) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Tabela de etapas do atendimento
        $this->db->query("CREATE TABLE IF NOT EXISTS os_etapas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            os_id INT NOT NULL,
            etapa VARCHAR(50) NOT NULL,
            status ENUM('pendente', 'em_andamento', 'concluida', 'cancelada') DEFAULT 'pendente',
            ordem INT DEFAULT 0,
            tempo_estimado_minutos INT NULL,
            tempo_real_minutos INT NULL,
            iniciado_at TIMESTAMP NULL,
            concluido_at TIMESTAMP NULL,
            responsavel_id INT NULL,
            observacao TEXT NULL,
            checklist JSON NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_os (os_id),
            INDEX idx_etapa (etapa),
            INDEX idx_status (status),
            FOREIGN KEY (os_id) REFERENCES os(idOs) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Tabela de competências/especialidades do técnico
        $this->db->query("CREATE TABLE IF NOT EXISTS tecnico_competencias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            competencia VARCHAR(100) NOT NULL,
            nivel ENUM('basico', 'intermediario', 'avancado', 'especialista') DEFAULT 'basico',
            certificado VARCHAR(255) NULL,
            validade_certificado DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uk_usuario_competencia (usuario_id, competencia),
            FOREIGN KEY (usuario_id) REFERENCES usuarios(idUsuarios) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Tabela de avaliações do técnico
        $this->db->query("CREATE TABLE IF NOT EXISTS tecnico_avaliacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            os_id INT NOT NULL,
            tecnico_id INT NOT NULL,
            cliente_id INT NOT NULL,
            nota_geral INT CHECK (nota_geral BETWEEN 1 AND 5),
            nota_atendimento INT CHECK (nota_atendimento BETWEEN 1 AND 5),
            nota_solucao INT CHECK (nota_solucao BETWEEN 1 AND 5),
            nota_tempo INT CHECK (nota_tempo BETWEEN 1 AND 5),
            comentario TEXT NULL,
            avaliado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (os_id) REFERENCES os(idOs) ON DELETE CASCADE,
            FOREIGN KEY (tecnico_id) REFERENCES usuarios(idUsuarios) ON DELETE CASCADE,
            FOREIGN KEY (cliente_id) REFERENCES clientes(idClientes) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Inserir templates de checklist padrão
        $this->insertDefaultTemplates();

        echo "✓ Tabelas de checklist criadas com sucesso!\n";
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS tecnico_avaliacoes");
        $this->db->query("DROP TABLE IF EXISTS tecnico_competencias");
        $this->db->query("DROP TABLE IF EXISTS os_etapas");
        $this->db->query("DROP TABLE IF EXISTS os_pecas_utilizadas");
        $this->db->query("DROP TABLE IF EXISTS os_timeline");
        $this->db->query("DROP TABLE IF EXISTS os_checklist");
        $this->db->query("DROP TABLE IF EXISTS checklist_template_items");
        $this->db->query("DROP TABLE IF EXISTS checklist_templates");

        echo "✓ Tabelas de checklist removidas!\n";
    }

    private function insertDefaultTemplates()
    {
        // Template: Manutenção Preventiva
        $this->db->query("INSERT INTO checklist_templates (nome, descricao, categoria) VALUES
            ('Manutenção Preventiva', 'Checklist padrão para manutenções preventivas', 'preventiva')");
        $template1 = $this->db->insert_id();

        $items1 = [
            ['Verificar sinais de desgaste visível', 1, 1],
            ['Limpar componentes internos', 2, 1],
            ['Lubrificar partes móveis', 3, 1],
            ['Verificar conexões elétricas', 4, 1],
            ['Testar funcionamento geral', 5, 1],
            ['Registrar horímetro/tacógrafo', 6, 0],
            ['Tirar foto do equipamento', 7, 1]
        ];
        foreach ($items1 as $item) {
            $this->db->query("INSERT INTO checklist_template_items (template_id, descricao, ordem, obrigatorio)
                VALUES ($template1, '{$item[0]}', {$item[1]}, {$item[2]})");
        }

        // Template: Reparo Corretivo
        $this->db->query("INSERT INTO checklist_templates (nome, descricao, categoria) VALUES
            ('Reparo Corretivo', 'Checklist para reparos e correções', 'corretiva')");
        $template2 = $this->db->insert_id();

        $items2 = [
            ['Diagnosticar problema relatado', 1, 1],
            ['Identificar causa raiz', 2, 1],
            ['Orçar peças necessárias', 3, 1],
            ['Aprovação do cliente', 4, 1],
            ['Executar reparo', 5, 1],
            ['Testar após reparo', 6, 1],
            ['Orientar cliente sobre uso', 7, 0],
            ['Coletar assinatura do cliente', 8, 1]
        ];
        foreach ($items2 as $item) {
            $this->db->query("INSERT INTO checklist_template_items (template_id, descricao, ordem, obrigatorio)
                VALUES ($template2, '{$item[0]}', {$item[1]}, {$item[2]})");
        }

        // Template: Instalação
        $this->db->query("INSERT INTO checklist_templates (nome, descricao, categoria) VALUES
            ('Instalação', 'Checklist para instalações de novos equipamentos', 'instalacao')");
        $template3 = $this->db->insert_id();

        $items3 = [
            ['Verificar local da instalação', 1, 1],
            ['Conferir integridade do equipamento', 2, 1],
            ['Verificar acessórios/consumíveis', 3, 1],
            ['Instalar conforme manual', 4, 1],
            ['Configurar parâmetros', 5, 1],
            ['Testar todas as funções', 6, 1],
            ['Treinar usuário', 7, 1],
            ['Deixar manual e garantia', 8, 1]
        ];
        foreach ($items3 as $item) {
            $this->db->query("INSERT INTO checklist_template_items (template_id, descricao, ordem, obrigatorio)
                VALUES ($template3, '{$item[0]}', {$item[1]}, {$item[2]})");
        }

        echo "✓ Templates de checklist inseridos!\n";
    }
}
