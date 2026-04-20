-- =============================================
-- DIAGNÓSTICO COMPLETO DO SISTEMA DE OBRAS
-- =============================================
-- Execute este script no MySQL para verificar a estrutura do banco

-- 1. Verificar se as tabelas existem
SHOW TABLES LIKE 'obra%';

-- 2. Estrutura da tabela obras
DESCRIBE obras;

-- 3. Estrutura da tabela obra_equipe
DESCRIBE obra_equipe;

-- 4. Estrutura da tabela obra_etapas
DESCRIBE obra_etapas;

-- 5. Estrutura da tabela obra_atividades
DESCRIBE obra_atividades;

-- 6. Verificar registros de obras
SELECT id, nome, cliente_id, status, percentual_concluido, ativo, created_at
FROM obras
WHERE ativo = 1
ORDER BY created_at DESC
LIMIT 10;

-- 7. Verificar equipes
SELECT oe.id, oe.obra_id, oe.tecnico_id, u.nome as tecnico_nome, oe.funcao, oe.ativo
FROM obra_equipe oe
LEFT JOIN usuarios u ON u.idUsuarios = oe.tecnico_id
WHERE oe.ativo = 1
LIMIT 10;

-- 8. Verificar etapas
SELECT id, obra_id, nome, status, percentual_concluido
FROM obra_etapas
LIMIT 10;

-- 9. Verificar atividades
SELECT id, obra_id, tecnico_id, descricao, tipo, data_atividade, created_at
FROM obra_atividades
WHERE ativo = 1
ORDER BY created_at DESC
LIMIT 10;

-- 10. Contar registros
SELECT
    (SELECT COUNT(*) FROM obras WHERE ativo = 1) as total_obras,
    (SELECT COUNT(*) FROM obra_equipe WHERE ativo = 1) as total_equipe,
    (SELECT COUNT(*) FROM obra_etapas) as total_etapas,
    (SELECT COUNT(*) FROM obra_atividades WHERE ativo = 1) as total_atividades;

-- =============================================
-- CORREÇÕES COMUNS
-- =============================================

-- Se a tabela obra_equipe não existir, criar:
/*
CREATE TABLE IF NOT EXISTS obra_equipe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    tecnico_id INT NOT NULL,
    funcao VARCHAR(50) DEFAULT 'Técnico',
    data_entrada DATE NOT NULL,
    data_saida DATE DEFAULT NULL,
    ativo TINYINT(1) DEFAULT 1,
    observacoes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_obra_tecnico (obra_id, tecnico_id),
    INDEX idx_tecnico (tecnico_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

-- Se a tabela obra_atividades não existir, criar:
/*
CREATE TABLE IF NOT EXISTS obra_atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    etapa_id INT,
    tecnico_id INT NOT NULL,
    titulo VARCHAR(255),
    descricao TEXT,
    tipo VARCHAR(50) DEFAULT 'execucao',
    status VARCHAR(50) DEFAULT 'iniciada',
    percentual_concluido INT DEFAULT 0,
    data_atividade DATE NOT NULL,
    hora_inicio TIME,
    hora_fim TIME,
    fotos TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1,
    INDEX idx_obra_id (obra_id),
    INDEX idx_tecnico_id (tecnico_id),
    INDEX idx_data (data_atividade)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

-- Se a tabela obra_etapas não existir, criar:
/*
CREATE TABLE IF NOT EXISTS obra_etapas (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

-- Verificar permissões de técnicos
-- O técnico precisa ter is_tecnico = 1 na tabela usuarios
SELECT idUsuarios, nome, is_tecnico, nivel_tecnico
FROM usuarios
WHERE is_tecnico = 1
LIMIT 10;
