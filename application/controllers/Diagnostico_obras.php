<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller de Diagnóstico para Obras
 *
 * Testa todas as funcionalidades do sistema de obras
 */
class Diagnostico_obras extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('obras_model');
        $this->load->model('obra_atividades_model');
    }

    /**
     * Página principal de diagnóstico
     */
    public function index()
    {
        echo '<h1>Diagnóstico do Sistema de Obras</h1>';
        echo '<hr>';

        $this->testarTabelas();
        $this->testarObras();
        $this->testarEquipes();
        $this->testarEtapas();
        $this->testarAtividades();
        $this->testarTecnicos();

        echo '<hr>';
        echo '<h2>Resumo</h2>';
        echo '<p>Verifique os logs acima para identificar problemas.</p>';
        echo '<p><a href="' . site_url('diagnostico_obras/corrigir') . '">Tentar corrigir problemas automaticamente</a></p>';
    }

    /**
     * Testar existência das tabelas
     */
    private function testarTabelas()
    {
        echo '<h2>1. Testando Tabelas</h2>';

        $tabelas = ['obras', 'obra_equipe', 'obra_etapas', 'obra_atividades'];
        $resultados = [];

        foreach ($tabelas as $tabela) {
            $existe = $this->db->table_exists($tabela);
            $resultados[$tabela] = $existe;
            echo '<p>' . ($existe ? '✅' : '❌') . ' Tabela <strong>' . $tabela . '</strong>: ' . ($existe ? 'Existe' : 'NÃO EXISTE') . '</p>';
        }

        return $resultados;
    }

    /**
     * Testar obras
     */
    private function testarObras()
    {
        echo '<h2>2. Testando Obras</h2>';

        $obras = $this->obras_model->getAll([], 5, 0);
        echo '<p>Total de obras ativas: ' . count($obras) . '</p>';

        if (count($obras) > 0) {
            echo '<ul>';
            foreach ($obras as $obra) {
                echo '<li>#' . $obra->id . ' - ' . htmlspecialchars($obra->nome) . ' (Status: ' . $obra->status . ')</li>';
            }
            echo '</ul>';
        } else {
            echo '<p style="color:orange">⚠️ Nenhuma obra encontrada. <a href="' . site_url('obras/adicionar') . '">Criar uma obra</a></p>';
        }

        return $obras;
    }

    /**
     * Testar equipes
     */
    private function testarEquipes()
    {
        echo '<h2>3. Testando Equipes</h2>';

        $obras = $this->obras_model->getAll([], 5, 0);

        if (count($obras) == 0) {
            echo '<p style="color:orange">⚠️ Nenhuma obra para testar equipe</p>';
            return [];
        }

        // Testar equipe da primeira obra
        $obra = $obras[0];
        $equipe = $this->obras_model->getEquipe($obra->id);

        echo '<p>Obra: ' . htmlspecialchars($obra->nome) . '</p>';
        echo '<p>Membros na equipe: ' . count($equipe) . '</p>';

        if (count($equipe) > 0) {
            echo '<ul>';
            foreach ($equipe as $membro) {
                echo '<li>' . htmlspecialchars($membro->tecnico_nome ?? 'Sem nome') . ' - ' . ($membro->funcao ?? 'Função não definida') . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p style="color:orange">⚠️ Nenhum técnico na equipe. <a href="' . site_url('obras/equipe/' . $obra->id) . '">Adicionar técnico</a></p>';
        }

        return $equipe;
    }

    /**
     * Testar etapas
     */
    private function testarEtapas()
    {
        echo '<h2>4. Testando Etapas</h2>';

        $obras = $this->obras_model->getAll([], 5, 0);

        if (count($obras) == 0) {
            echo '<p style="color:orange">⚠️ Nenhuma obra para testar etapas</p>';
            return [];
        }

        // Testar etapas da primeira obra
        $obra = $obras[0];
        $etapas = $this->obras_model->getEtapas($obra->id);

        echo '<p>Obra: ' . htmlspecialchars($obra->nome) . '</p>';
        echo '<p>Etapas: ' . count($etapas) . '</p>';

        if (count($etapas) > 0) {
            echo '<ul>';
            foreach ($etapas as $etapa) {
                echo '<li>' . htmlspecialchars($etapa->nome) . ' (Status: ' . ($etapa->status ?? 'N/A') . ')</li>';
            }
            echo '</ul>';
        } else {
            echo '<p style="color:orange">⚠️ Nenhuma etapa cadastrada. <a href="' . site_url('obras/etapas/' . $obra->id) . '">Adicionar etapas</a></p>';
        }

        return $etapas;
    }

    /**
     * Testar atividades
     */
    private function testarAtividades()
    {
        echo '<h2>5. Testando Atividades</h2>';

        $obras = $this->obras_model->getAll([], 5, 0);

        if (count($obras) == 0) {
            echo '<p style="color:orange">⚠️ Nenhuma obra para testar atividades</p>';
            return [];
        }

        // Testar atividades da primeira obra
        $obra = $obras[0];
        $atividades = $this->obra_atividades_model->getByObra($obra->id, [], 5);

        echo '<p>Obra: ' . htmlspecialchars($obra->nome) . '</p>';
        echo '<p>Atividades: ' . count($atividades) . '</p>';

        if (count($atividades) > 0) {
            echo '<ul>';
            foreach ($atividades as $atividade) {
                echo '<li>' . htmlspecialchars($atividade->descricao ?? 'Sem descrição') . ' - ' . ($atividade->tecnico_nome ?? 'Técnico não informado') . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p style="color:orange">⚠️ Nenhuma atividade registrada.</p>';
            echo '<p>Para testar:</p>';
            echo '<ol>';
            echo '<li>Certifique-se que a obra tenha técnicos na equipe</li>';
            echo '<li>Faça login como técnico</li>';
            echo '<li>Acesse "Minhas Obras" e execute a obra</li>';
            echo '<li>Registre uma atividade</li>';
            echo '</ol>';
        }

        return $atividades;
    }

    /**
     * Testar técnicos
     */
    private function testarTecnicos()
    {
        echo '<h2>6. Testando Técnicos</h2>';

        $this->db->where('is_tecnico', 1);
        $tecnicos = $this->db->get('usuarios')->result();

        echo '<p>Total de técnicos cadastrados: ' . count($tecnicos) . '</p>';

        if (count($tecnicos) > 0) {
            echo '<ul>';
            foreach ($tecnicos as $tecnico) {
                echo '<li>ID: ' . $tecnico->idUsuarios . ' - ' . htmlspecialchars($tecnico->nome) . ' (Nível: ' . ($tecnico->nivel_tecnico ?? 'N/A') . ')</li>';
            }
            echo '</ul>';
        } else {
            echo '<p style="color:red">❌ Nenhum técnico cadastrado!</p>';
            echo '<p>Para criar um técnico, acesse Configurações > Usuários e marque "É Técnico"</p>';
        }

        return $tecnicos;
    }

    /**
     * Tentar corrigir problemas automaticamente
     */
    public function corrigir()
    {
        echo '<h1>Correção Automática - Sistema de Obras</h1>';
        echo '<hr>';

        // Criar tabelas que não existem
        $tabelas = [
            'obra_equipe' => "CREATE TABLE IF NOT EXISTS obra_equipe (
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
                INDEX idx_tecnico (tecnico_id),
                INDEX idx_obra_tecnico_ativo (obra_id, tecnico_id, ativo)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            'obra_atividades' => "CREATE TABLE IF NOT EXISTS obra_atividades (
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
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                ativo TINYINT(1) DEFAULT 1,
                INDEX idx_obra_id (obra_id),
                INDEX idx_tecnico_id (tecnico_id),
                INDEX idx_data (data_atividade),
                INDEX idx_obra_data (obra_id, data_atividade)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            'obra_etapas' => "CREATE TABLE IF NOT EXISTS obra_etapas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                obra_id INT NOT NULL,
                numero_etapa INT DEFAULT 1,
                nome VARCHAR(255) NOT NULL,
                descricao TEXT,
                especialidade VARCHAR(100),
                data_inicio_prevista DATE,
                data_fim_prevista DATE,
                data_conclusao DATE,
                status VARCHAR(50) DEFAULT 'NaoIniciada',
                percentual_concluido INT DEFAULT 0,
                visivel_cliente TINYINT(1) DEFAULT 1,
                ativo TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_obra_id (obra_id),
                INDEX idx_obra_status (obra_id, status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            'obra_checkins' => "CREATE TABLE IF NOT EXISTS obra_checkins (
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
                INDEX idx_obra_tecnico (obra_id, tecnico_id),
                INDEX idx_checkout (check_out)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            'obra_diario' => "CREATE TABLE IF NOT EXISTS obra_diario (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];

        foreach ($tabelas as $nome => $sql) {
            if (!$this->db->table_exists($nome)) {
                echo '<p>Criando tabela: ' . $nome . '...</p>';
                $this->db->query($sql);
                echo '<p style="color:green">✅ Tabela ' . $nome . ' criada!</p>';
            } else {
                echo '<p style="color:green">✅ Tabela ' . $nome . ' já existe</p>';
            }
        }

        echo '<hr>';
        echo '<p><a href="' . site_url('diagnostico_obras') . '">Voltar ao diagnóstico</a></p>';
    }

    /**
     * Teste de fluxo completo
     */
    public function teste_fluxo()
    {
        echo '<h1>Teste de Fluxo Completo</h1>';
        echo '<hr>';

        // 1. Criar obra de teste
        echo '<h2>Passo 1: Criar Obra de Teste</h2>';

        $dados_obra = [
            'nome' => 'OBRA DE TESTE - ' . date('Y-m-d H:i:s'),
            'cliente_id' => 1, // Assumindo que existe cliente com ID 1
            'tipo_obra' => 'Teste',
            'status' => 'Prospeccao',
            'percentual_concluido' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'ativo' => 1
        ];

        $this->db->insert('obras', $dados_obra);
        $obra_id = $this->db->insert_id();

        if ($obra_id) {
            echo '<p style="color:green">✅ Obra criada com ID: ' . $obra_id . '</p>';
        } else {
            echo '<p style="color:red">❌ Falha ao criar obra</p>';
            return;
        }

        // 2. Adicionar técnico à equipe
        echo '<h2>Passo 2: Adicionar Técnico à Equipe</h2>';

        // Buscar primeiro técnico
        $this->db->where('is_tecnico', 1);
        $tecnico = $this->db->get('usuarios')->row();

        if ($tecnico) {
            $dados_equipe = [
                'obra_id' => $obra_id,
                'tecnico_id' => $tecnico->idUsuarios,
                'funcao' => 'Técnico Teste',
                'data_entrada' => date('Y-m-d'),
                'ativo' => 1
            ];

            $this->db->insert('obra_equipe', $dados_equipe);
            $equipe_id = $this->db->insert_id();

            if ($equipe_id) {
                echo '<p style="color:green">✅ Técnico ' . $tecnico->nome . ' adicionado à equipe (ID: ' . $equipe_id . ')</p>';
            } else {
                echo '<p style="color:red">❌ Falha ao adicionar técnico à equipe</p>';
            }
        } else {
            echo '<p style="color:orange">⚠️ Nenhum técnico encontrado para adicionar à equipe</p>';
        }

        // 3. Adicionar etapa
        echo '<h2>Passo 3: Adicionar Etapa</h2>';

        $dados_etapa = [
            'obra_id' => $obra_id,
            'numero_etapa' => 1,
            'nome' => 'Etapa de Teste',
            'descricao' => 'Esta é uma etapa de teste',
            'status' => 'pendente',
            'percentual_concluido' => 0,
            'ativo' => 1
        ];

        $etapa_id = $this->obras_model->adicionarEtapa($obra_id, $dados_etapa);

        if ($etapa_id) {
            echo '<p style="color:green">✅ Etapa criada com ID: ' . $etapa_id . '</p>';
        } else {
            echo '<p style="color:red">❌ Falha ao criar etapa</p>';
        }

        // 4. Verificar resultados
        echo '<h2>Passo 4: Verificar Resultados</h2>';

        $obra = $this->obras_model->getById($obra_id);
        $equipe = $this->obras_model->getEquipe($obra_id);
        $etapas = $this->obras_model->getEtapas($obra_id);

        echo '<ul>';
        echo '<li>Obra: ' . ($obra ? '✅ Encontrada' : '❌ Não encontrada') . '</li>';
        echo '<li>Equipe: ' . (count($equipe) > 0 ? '✅ ' . count($equipe) . ' membro(s)' : '❌ Vazia') . '</li>';
        echo '<li>Etapas: ' . (count($etapas) > 0 ? '✅ ' . count($etapas) . ' etapa(s)' : '❌ Nenhuma') . '</li>';
        echo '</ul>';

        echo '<hr>';
        echo '<p><strong>Links para teste:</strong></p>';
        echo '<ul>';
        echo '<li><a href="' . site_url('obras/visualizar/' . $obra_id) . '">Visualizar Obra (Admin)</a></li>';
        echo '<li><a href="' . site_url('obras/equipe/' . $obra_id) . '">Gerenciar Equipe</a></li>';
        echo '<li><a href="' . site_url('tecnicos/minhas_obras') . '">Portal do Técnico - Minhas Obras</a></li>';
        echo '</ul>';

        echo '<hr>';
        echo '<p style="color:orange"><strong>Nota:</strong> Esta obra de teste foi criada para diagnóstico. Exclua-a após os testes.</p>';
    }
}
