<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller de Diagnostico
 * Testa todas as funcionalidades do sistema
 */
class Diagnostico extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Pagina principal de diagnostico
     */
    public function index()
    {
        $this->load->view('diagnostico_view');
    }

    /**
     * Verificar tabelas do sistema de obras
     */
    public function obras()
    {
        $data = [];

        // Verificar tabelas
        $tabelas = ['obras', 'obra_equipe', 'obra_etapas', 'obra_atividades', 'obra_checkins', 'obra_diario'];
        foreach ($tabelas as $tabela) {
            $data['tabelas'][$tabela] = $this->db->table_exists($tabela);
        }

        // Verificar dados
        if ($data['tabelas']['obras']) {
            $data['total_obras'] = $this->db->where('ativo', 1)->count_all_results('obras');
        }

        if ($data['tabelas']['obra_equipe']) {
            $data['total_equipe'] = $this->db->where('ativo', 1)->count_all_results('obra_equipe');
        }

        if ($data['tabelas']['obra_etapas']) {
            $data['total_etapas'] = $this->db->count_all_results('obra_etapas');
        }

        if ($data['tabelas']['obra_atividades']) {
            $data['total_atividades'] = $this->db->count_all_results('obra_atividades');
        }

        $this->load->view('diagnostico_obras_view', $data);
    }

    /**
     * Criar tabelas que nao existem
     */
    public function criar_tabelas()
    {
        // Tabela obra_equipe
        if (!$this->db->table_exists('obra_equipe')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS obra_equipe (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                tecnico_id INT NOT NULL,
                funcao VARCHAR(50) DEFAULT 'Tecnico',
                data_entrada DATE NOT NULL,
                data_saida DATE DEFAULT NULL,
                ativo TINYINT(1) DEFAULT 1,
                observacoes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_obra_tecnico (obra_id, tecnico_id),
                INDEX idx_tecnico (tecnico_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        // Tabela obra_etapas
        if (!$this->db->table_exists('obra_etapas')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS obra_etapas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                numero_etapa INT DEFAULT 1,
                nome VARCHAR(255) NOT NULL,
                descricao TEXT,
                especialidade VARCHAR(100),
                data_inicio_prevista DATE,
                data_fim_prevista DATE,
                data_conclusao DATE,
                status VARCHAR(50) DEFAULT 'pendente',
                percentual_concluido INT DEFAULT 0,
                visivel_cliente TINYINT(1) DEFAULT 1,
                ativo TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_obra_id (obra_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        // Tabela obra_atividades
        if (!$this->db->table_exists('obra_atividades')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS obra_atividades (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                etapa_id INT,
                tecnico_id INT,
                titulo VARCHAR(255),
                descricao TEXT,
                tipo VARCHAR(50) DEFAULT 'execucao',
                status VARCHAR(50) DEFAULT 'agendada',
                percentual_concluido INT DEFAULT 0,
                data_atividade DATE NOT NULL,
                hora_inicio TIME,
                hora_fim TIME,
                fotos TEXT,
                visivel_cliente TINYINT(1) DEFAULT 1,
                ativo TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_obra_id (obra_id),
                INDEX idx_tecnico_id (tecnico_id),
                INDEX idx_data (data_atividade)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        // Tabela obra_checkins
        if (!$this->db->table_exists('obra_checkins')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS obra_checkins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                etapa_id INT,
                tecnico_id INT NOT NULL,
                check_in DATETIME NOT NULL,
                check_out DATETIME,
                latitude_in DECIMAL(10, 8),
                longitude_in DECIMAL(11, 8),
                latitude_out DECIMAL(10, 8),
                longitude_out DECIMAL(11, 8),
                foto_in VARCHAR(255),
                foto_out VARCHAR(255),
                observacao_in TEXT,
                observacao_out TEXT,
                atividades_realizadas TEXT,
                horas_trabalhadas DECIMAL(5,2),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_obra_tecnico (obra_id, tecnico_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        // Tabela obra_diario
        if (!$this->db->table_exists('obra_diario')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS obra_diario (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                tecnico_id INT NOT NULL,
                data DATE NOT NULL,
                hora_inicio TIME,
                hora_fim TIME,
                atividade_realizada TEXT,
                fotos_json TEXT,
                observacoes TEXT,
                etapa_id INT,
                clima VARCHAR(50),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_obra_data (obra_id, data)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        $this->session->set_flashdata('success', 'Tabelas criadas com sucesso!');
        redirect('diagnostico/obras');
    }
}
