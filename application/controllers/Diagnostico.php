<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller de Diagnóstico do Sistema
 */
class Diagnostico extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Página principal de diagnóstico
     */
    public function index()
    {
        echo '<!DOCTYPE html>';
        echo '<html><head>';
        echo '<title>Diagnóstico do Sistema</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }';
        echo '.container { max-width: 1200px; margin: 0 auto; }';
        echo '.card { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }';
        echo '.success { background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 10px; border-radius: 4px; }';
        echo '.error { background: #f8d7da; border: 1px solid #dc3545; color: #721c24; padding: 10px; border-radius: 4px; }';
        echo '.warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 10px; border-radius: 4px; }';
        echo 'table { width: 100%; border-collapse: collapse; margin: 10px 0; }';
        echo 'th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }';
        echo 'th { background: #f0f0f0; }';
        echo '.btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }';
        echo '.btn-success { background: #28a745; }';
        echo '.btn-danger { background: #dc3545; }';
        echo '.btn-warning { background: #ffc107; color: #000; }';
        echo 'h1, h2 { color: #333; }';
        echo 'pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }';
        echo '</style>';
        echo '</head><body>';
        echo '<div class="container">';
        echo '<h1>🔧 Diagnóstico do Sistema</h1>';

        $this->diagnosticarTabelas();
        $this->diagnosticarDados();

        echo '</div></body></html>';
    }

    /**
     * Diagnóstico das tabelas
     */
    private function diagnosticarTabelas()
    {
        echo '<div class="card">';
        echo '<h2>📋 Verificação de Tabelas</h2>';

        $tabelas = [
            'obras' => 'Tabela principal de obras',
            'obra_etapas' => 'Etapas das obras',
            'obra_atividades' => 'Atividades das obras',
            'obra_equipe' => 'Equipe das obras',
            'obra_checkins' => 'Checkins das atividades',
            'obra_atividades_historico' => 'Histórico de atividades',
            'usuarios' => 'Usuários do sistema',
            'clientes' => 'Clientes'
        ];

        echo '<table>';
        echo '<tr><th>Tabela</th><th>Descrição</th><th>Status</th><th>Ações</th></tr>';

        foreach ($tabelas as $tabela => $descricao) {
            $existe = $this->db->table_exists($tabela);
            $status = $existe ?
                '<span class="success">✅ Existe</span>' :
                '<span class="error">❌ Não existe</span>';

            echo '<tr>';
            echo '<td>' . $tabela . '</td>';
            echo '<td>' . $descricao . '</td>';
            echo '<td>' . $status . '</td>';
            echo '<td>';
            if ($existe) {
                echo '<a href="' . site_url('diagnostico/estrutura/' . $tabela) . '" class="btn">Ver Estrutura</a>';
            } else {
                echo '<a href="' . site_url('diagnostico/criar/' . $tabela) . '" class="btn btn-success">Criar</a>';
            }
            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</div>';
    }

    /**
     * Diagnóstico dos dados
     */
    private function diagnosticarDados()
    {
        echo '<div class="card">';
        echo '<h2>📊 Resumo de Dados</h2>';

        // Contar registros
        $contagens = [];

        if ($this->db->table_exists('obras')) {
            $contagens['Obras'] = $this->db->count_all('obras');
        }
        if ($this->db->table_exists('obra_etapas')) {
            $contagens['Etapas'] = $this->db->count_all('obra_etapas');
        }
        if ($this->db->table_exists('obra_atividades')) {
            $contagens['Atividades'] = $this->db->count_all('obra_atividades');
        }
        if ($this->db->table_exists('usuarios')) {
            $contagens['Usuários'] = $this->db->count_all('usuarios');
        }
        if ($this->db->table_exists('clientes')) {
            $contagens['Clientes'] = $this->db->count_all('clientes');
        }

        echo '<table>';
        echo '<tr><th>Entidade</th><th>Total de Registros</th></tr>';
        foreach ($contagens as $entidade => $total) {
            echo '<tr><td>' . $entidade . '</td><td>' . $total . '</td></tr>';
        }
        echo '</table>';

        // Verificar problemas
        echo '<h3>⚠️ Verificação de Problemas</h3>';

        $problemas = [];

        // Verificar atividades sem etapa
        if ($this->db->table_exists('obra_atividades') && $this->db->table_exists('obra_etapas')) {
            if ($this->db->field_exists('etapa_id', 'obra_atividades')) {
                $this->db->where('etapa_id IS NULL OR etapa_id =', 0);
                $atividades_sem_etapa = $this->db->count_all_results('obra_atividades');
                if ($atividades_sem_etapa > 0) {
                    $problemas[] = $atividades_sem_etapa . ' atividade(s) sem etapa associada';
                }
            }
        }

        // Verificar atividades sem técnico
        if ($this->db->table_exists('obra_atividades')) {
            $colunas = $this->db->list_fields('obra_atividades');
            $campo_tecnico = in_array('tecnico_id', $colunas) ? 'tecnico_id' : 'usuario_id';
            if (in_array($campo_tecnico, $colunas)) {
                $this->db->where($campo_tecnico . ' IS NULL OR ' . $campo_tecnico . ' =', 0);
                $atividades_sem_tecnico = $this->db->count_all_results('obra_atividades');
                if ($atividades_sem_tecnico > 0) {
                    $problemas[] = $atividades_sem_tecnico . ' atividade(s) sem técnico associado';
                }
            }
        }

        if (empty($problemas)) {
            echo '<p class="success">✅ Nenhum problema encontrado!</p>';
        } else {
            echo '<ul>';
            foreach ($problemas as $problema) {
                echo '<li class="warning">⚠️ ' . $problema . '</li>';
            }
            echo '</ul>';
        }

        echo '<div style="margin-top: 20px;">';
        echo '<a href="' . site_url('diagnostico/migrarTudo') . '" class="btn btn-success">🚀 Executar Todas as Migrações</a>';
        echo '<a href="' . site_url('diagnostico/limparAtividades') . '" class="btn btn-danger" onclick="return confirm(\'Tem certeza? Isso excluirá TODAS as atividades!\')">🗑️ Limpar Todas as Atividades</a>';
        echo '</div>';

        echo '</div>';
    }

    /**
     * Ver estrutura de uma tabela
     */
    public function estrutura($tabela)
    {
        echo '<!DOCTYPE html>';
        echo '<html><head>';
        echo '<title>Estrutura: ' . $tabela . '</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }';
        echo '.container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }';
        echo 'table { width: 100%; border-collapse: collapse; margin: 10px 0; }';
        echo 'th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }';
        echo 'th { background: #f0f0f0; }';
        echo '.btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }';
        echo '</style>';
        echo '</head><body>';
        echo '<div class="container">';
        echo '<h1>📋 Estrutura da Tabela: ' . $tabela . '</h1>';
        echo '<a href="' . site_url('diagnostico') . '" class="btn">← Voltar</a>';
        echo '<hr>';

        if (!$this->db->table_exists($tabela)) {
            echo '<p style="color: red;">Tabela não existe!</p>';
        } else {
            $campos = $this->db->field_data($tabela);

            echo '<table>';
            echo '<tr><th>Campo</th><th>Tipo</th><th>Tamanho</th><th>Nulo</th><th>Padrão</th></tr>';

            foreach ($campos as $campo) {
                echo '<tr>';
                echo '<td><strong>' . $campo->name . '</strong></td>';
                echo '<td>' . $campo->type . '</td>';
                echo '<td>' . ($campo->max_length ?? 'N/A') . '</td>';
                echo '<td>' . ($campo->nullable ? 'Sim' : 'Não') . '</td>';
                echo '<td>' . ($campo->default ?? 'NULL') . '</td>';
                echo '</tr>';
            }

            echo '</table>';

            // Mostrar índices
            echo '<h2>Índices</h2>';
            $indices = $this->db->query('SHOW INDEX FROM ' . $tabela)->result();

            if (!empty($indices)) {
                echo '<table>';
                echo '<tr><th>Nome</th><th>Coluna</th><th>Único</th><th>Tipo</th></tr>';
                foreach ($indices as $indice) {
                    echo '<tr>';
                    echo '<td>' . $indice->Key_name . '</td>';
                    echo '<td>' . $indice->Column_name . '</td>';
                    echo '<td>' . ($indice->Non_unique ? 'Não' : 'Sim') . '</td>';
                    echo '<td>' . $indice->Index_type . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        }

        echo '</div></body></html>';
    }

    /**
     * Criar tabela
     */
    public function criar($tabela)
    {
        echo '<!DOCTYPE html>';
        echo '<html><head>';
        echo '<title>Criar: ' . $tabela . '</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }';
        echo '.container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }';
        echo '.success { color: #28a745; }';
        echo '.error { color: #dc3545; }';
        echo '</style>';
        echo '</head><body>';
        echo '<div class="container">';
        echo '<h1>Criar Tabela: ' . $tabela . '</h1>';
        echo '<a href="' . site_url('diagnostico') . '">← Voltar</a>';
        echo '<hr>';

        if ($this->db->table_exists($tabela)) {
            echo '<p class="success">Tabela já existe!</p>';
        } else {
            $sql = $this->getSqlCriacao($tabela);

            if ($sql) {
                try {
                    $this->db->query($sql);
                    echo '<p class="success">✅ Tabela criada com sucesso!</p>';
                } catch (Exception $e) {
                    echo '<p class="error">❌ Erro: ' . $e->getMessage() . '</p>';
                    echo '<pre>' . $sql . '</pre>';
                }
            } else {
                echo '<p class="error">SQL de criação não definido para esta tabela.</p>';
            }
        }

        echo '</div></body></html>';
    }

    /**
     * Migrar todas as tabelas
     */
    public function migrarTudo()
    {
        echo '<!DOCTYPE html>';
        echo '<html><head>';
        echo '<title>Migração Completa</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }';
        echo '.container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }';
        echo '.success { background: #d4edda; color: #155724; padding: 10px; margin: 5px 0; border-radius: 4px; }';
        echo '.error { background: #f8d7da; color: #721c24; padding: 10px; margin: 5px 0; border-radius: 4px; }';
        echo '.warning { background: #fff3cd; color: #856404; padding: 10px; margin: 5px 0; border-radius: 4px; }';
        echo 'ul { list-style: none; padding: 0; }';
        echo '</style>';
        echo '</head><body>';
        echo '<div class="container">';
        echo '<h1>🚀 Migração Completa</h1>';
        echo '<a href="' . site_url('diagnostico') . '">← Voltar</a>';
        echo '<hr>';
        echo '<ul>';

        // Criar tabelas se não existirem
        $tabelas_para_criar = ['obra_etapas', 'obra_atividades', 'obra_equipe', 'obra_checkins', 'obra_atividades_historico'];

        foreach ($tabelas_para_criar as $tabela) {
            if (!$this->db->table_exists($tabela)) {
                $sql = $this->getSqlCriacao($tabela);
                if ($sql) {
                    try {
                        $this->db->query($sql);
                        echo '<li class="success">✅ Tabela ' . $tabela . ' criada</li>';
                    } catch (Exception $e) {
                        echo '<li class="error">❌ Erro ao criar ' . $tabela . ': ' . $e->getMessage() . '</li>';
                    }
                }
            } else {
                echo '<li class="warning">⚠️ Tabela ' . $tabela . ' já existe</li>';
            }
        }

        // Adicionar colunas faltantes
        $this->adicionarColunasFaltantes();

        echo '</ul>';
        echo '<p><a href="' . site_url('diagnostico') . '" style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">Atualizar Diagnóstico</a></p>';
        echo '</div></body></html>';
    }

    /**
     * Limpar todas as atividades
     */
    public function limparAtividades()
    {
        echo '<!DOCTYPE html>';
        echo '<html><head>';
        echo '<title>Limpar Atividades</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }';
        echo '.container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }';
        echo '</style>';
        echo '</head><body>';
        echo '<div class="container">';
        echo '<h1>🗑️ Limpar Atividades</h1>';
        echo '<a href="' . site_url('diagnostico') . '">← Voltar</a>';
        echo '<hr>';

        if ($this->db->table_exists('obra_atividades')) {
            // Contar antes de excluir
            $total = $this->db->count_all('obra_atividades');

            // Excluir todos os registros
            $this->db->empty_table('obra_atividades');

            echo '<p style="color: #28a745;">' . $total . ' atividade(s) excluída(s) com sucesso!</p>';
        } else {
            echo '<p style="color: #dc3545;">Tabela obra_atividades não existe!</p>';
        }

        echo '</div></body></html>';
    }

    /**
     * Adicionar colunas faltantes
     */
    private function adicionarColunasFaltantes()
    {
        echo '<li style="background: #e3f2fd; padding: 10px; margin: 5px 0; border-radius: 4px;"><strong>Adicionando colunas faltantes...</strong></li>';

        // obra_atividades
        if ($this->db->table_exists('obra_atividades')) {
            $colunas = $this->db->list_fields('obra_atividades');

            $colunas_para_adicionar = [
                ['nome' => 'titulo', 'sql' => "ALTER TABLE obra_atividades ADD COLUMN titulo VARCHAR(255) AFTER obra_id"],
                ['nome' => 'etapa_id', 'sql' => "ALTER TABLE obra_atividades ADD COLUMN etapa_id INT NULL AFTER titulo"],
                ['nome' => 'tecnico_id', 'sql' => "ALTER TABLE obra_atividades ADD COLUMN tecnico_id INT NULL AFTER etapa_id"],
                ['nome' => 'status', 'sql' => "ALTER TABLE obra_atividades ADD COLUMN status VARCHAR(50) DEFAULT 'agendada' AFTER tipo"],
                ['nome' => 'data_atividade', 'sql' => "ALTER TABLE obra_atividades ADD COLUMN data_atividade DATE AFTER status"],
                ['nome' => 'percentual_concluido', 'sql' => "ALTER TABLE obra_atividades ADD COLUMN percentual_concluido INT DEFAULT 0 AFTER data_atividade"],
                ['nome' => 'visivel_cliente', 'sql' => "ALTER TABLE obra_atividades ADD COLUMN visivel_cliente TINYINT(1) DEFAULT 1 AFTER percentual_concluido"],
                ['nome' => 'updated_at', 'sql' => "ALTER TABLE obra_atividades ADD COLUMN updated_at DATETIME AFTER created_at"]
            ];

            foreach ($colunas_para_adicionar as $coluna) {
                if (!in_array($coluna['nome'], $colunas)) {
                    try {
                        $this->db->query($coluna['sql']);
                        echo '<li class="success">✅ Coluna ' . $coluna['nome'] . ' adicionada em obra_atividades</li>';
                    } catch (Exception $e) {
                        echo '<li class="error">❌ Erro ao adicionar ' . $coluna['nome'] . ': ' . $e->getMessage() . '</li>';
                    }
                }
            }
        }

        // obra_etapas
        if ($this->db->table_exists('obra_etapas')) {
            $colunas = $this->db->list_fields('obra_etapas');

            $colunas_etapas = [
                ['nome' => 'percentual_concluido', 'sql' => "ALTER TABLE obra_etapas ADD COLUMN percentual_concluido INT DEFAULT 0 AFTER status"],
                ['nome' => 'ativo', 'sql' => "ALTER TABLE obra_etapas ADD COLUMN ativo TINYINT(1) DEFAULT 1 AFTER percentual_concluido"],
                ['nome' => 'created_at', 'sql' => "ALTER TABLE obra_etapas ADD COLUMN created_at DATETIME"],
                ['nome' => 'updated_at', 'sql' => "ALTER TABLE obra_etapas ADD COLUMN updated_at DATETIME"]
            ];

            foreach ($colunas_etapas as $coluna) {
                if (!in_array($coluna['nome'], $colunas)) {
                    try {
                        $this->db->query($coluna['sql']);
                        echo '<li class="success">✅ Coluna ' . $coluna['nome'] . ' adicionada em obra_etapas</li>';
                    } catch (Exception $e) {
                        echo '<li class="error">❌ Erro ao adicionar ' . $coluna['nome'] . ': ' . $e->getMessage() . '</li>';
                    }
                }
            }
        }
    }

    /**
     * SQL de criação das tabelas
     */
    private function getSqlCriacao($tabela)
    {
        $sqls = [
            'obra_etapas' => "CREATE TABLE IF NOT EXISTS obra_etapas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                numero_etapa INT DEFAULT 1,
                nome VARCHAR(255) NOT NULL,
                descricao TEXT,
                data_inicio_prevista DATE,
                data_fim_prevista DATE,
                percentual_concluido INT DEFAULT 0,
                status VARCHAR(50) DEFAULT 'pendente',
                ativo TINYINT(1) DEFAULT 1,
                created_at DATETIME,
                updated_at DATETIME,
                INDEX idx_obra (obra_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            'obra_atividades' => "CREATE TABLE IF NOT EXISTS obra_atividades (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                etapa_id INT,
                tecnico_id INT,
                titulo VARCHAR(255),
                descricao TEXT,
                tipo VARCHAR(50) DEFAULT 'trabalho',
                status VARCHAR(50) DEFAULT 'agendada',
                data_atividade DATE,
                percentual_concluido INT DEFAULT 0,
                visivel_cliente TINYINT(1) DEFAULT 1,
                hora_inicio TIME,
                hora_fim TIME,
                horas_trabalhadas DECIMAL(5,2),
                impedimento TINYINT(1) DEFAULT 0,
                motivo_impedimento TEXT,
                tipo_impedimento VARCHAR(50),
                checkin_lat DECIMAL(10,8),
                checkin_lng DECIMAL(11,8),
                checkout_lat DECIMAL(10,8),
                checkout_lng DECIMAL(11,8),
                ativo TINYINT(1) DEFAULT 1,
                created_at DATETIME,
                updated_at DATETIME,
                INDEX idx_obra (obra_id),
                INDEX idx_etapa (etapa_id),
                INDEX idx_tecnico (tecnico_id),
                INDEX idx_data (data_atividade)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            'obra_equipe' => "CREATE TABLE IF NOT EXISTS obra_equipe (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                usuario_id INT NOT NULL,
                funcao VARCHAR(100),
                data_entrada DATE,
                ativo TINYINT(1) DEFAULT 1,
                created_at DATETIME,
                INDEX idx_obra (obra_id),
                INDEX idx_usuario (usuario_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            'obra_checkins' => "CREATE TABLE IF NOT EXISTS obra_checkins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                atividade_id INT NOT NULL,
                usuario_id INT NOT NULL,
                tipo VARCHAR(50),
                latitude DECIMAL(10,8),
                longitude DECIMAL(11,8),
                foto VARCHAR(255),
                observacao TEXT,
                created_at DATETIME,
                INDEX idx_atividade (atividade_id),
                INDEX idx_usuario (usuario_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            'obra_atividades_historico' => "CREATE TABLE IF NOT EXISTS obra_atividades_historico (
                id INT AUTO_INCREMENT PRIMARY KEY,
                atividade_id INT NOT NULL,
                tecnico_id INT,
                tipo_alteracao VARCHAR(50),
                descricao TEXT,
                percentual_anterior INT,
                percentual_novo INT,
                horas_trabalhadas DECIMAL(5,2),
                localizacao_lat DECIMAL(10,8),
                localizacao_lng DECIMAL(11,8),
                created_at DATETIME,
                INDEX idx_atividade (atividade_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];

        return $sqls[$tabela] ?? null;
    }
}
