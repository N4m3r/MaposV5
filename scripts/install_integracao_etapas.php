<?php
/**
 * Script de Instalação - Integração Atividades com Etapas das Obras
 *
 * Este script executa a migration para integrar o sistema de registro
 * de atividades (Hora Início/Fim) com as etapas das obras.
 *
 * Uso: php install_integracao_etapas.php
 */

// Define o ambiente
$_SERVER['CI_ENV'] = 'development';

define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('ENVIRONMENT', 'development');

echo "========================================\n";
echo "  Integração: Atividades ↔ Etapas      \n";
echo "========================================\n\n";

// Carrega o CodeIgniter
require_once BASEPATH . 'core/Common.php';
require_once APPPATH . 'config/constants.php';

// Carrega a classe de migração
require_once APPPATH . 'database/migrations/20260422000001_integrar_atividades_etapas.php';

// Simula o CI
class CI_Migration_Mock {
    public $db;

    public function __construct() {
        // Carrega configuração do banco
        $db_config = require APPPATH . 'config/database.php';
        $active_group = 'default';

        // Conecta ao banco
        $db = $db_config['db'][$active_group];

        $this->db = new class($db) {
            private $pdo;

            public function __construct($config) {
                $dsn = "mysql:host={$config['hostname']};dbname={$config['database']};charset=utf8mb4";
                $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                ]);
            }

            public function query($sql) {
                return $this->pdo->query($sql);
            }

            public function table_exists($table) {
                try {
                    $stmt = $this->pdo->query("SHOW TABLES LIKE '{$table}'");
                    return $stmt->rowCount() > 0;
                } catch (Exception $e) {
                    return false;
                }
            }

            public function field_exists($field, $table) {
                try {
                    $stmt = $this->pdo->query("SHOW COLUMNS FROM `{$table}` LIKE '{$field}'");
                    return $stmt->rowCount() > 0;
                } catch (Exception $e) {
                    return false;
                }
            }
        };
    }
}

// Executa a migração
try {
    echo "Conectando ao banco de dados...\n";
    $mock = new CI_Migration_Mock();

    // Verifica se a tabela os_atividades existe
    if (!$mock->db->table_exists('os_atividades')) {
        echo "\n❌ ERRO: A tabela os_atividades não existe!\n";
        echo "Instale o sistema de atividades primeiro:\n";
        echo "  php install_atividades.php\n\n";
        exit(1);
    }

    echo "✓ Tabela os_atividades encontrada\n";

    // Verifica se a tabela obra_etapas existe
    if (!$mock->db->table_exists('obra_etapas')) {
        echo "\n❌ ERRO: A tabela obra_etapas não existe!\n";
        echo "Verifique se o sistema de obras está instalado.\n\n";
        exit(1);
    }

    echo "✓ Tabela obra_etapas encontrada\n";

    echo "\nExecutando migração de integração...\n";
    echo "----------------------------------------\n";

    $migration = new Migration_Integrar_atividades_etapas();
    $migration->db = $mock->db;
    $result = $migration->up();

    if ($result === false) {
        echo "\n❌ Migração falhou. Verifique os erros acima.\n\n";
        exit(1);
    }

    echo "\n========================================\n";
    echo "✅ Integração instalada com sucesso!\n";
    echo "========================================\n\n";

    echo "Alterações realizadas:\n";
    echo "  - Adicionado campo etapa_id em os_atividades\n";
    echo "  - Adicionado campo obra_atividade_id em os_atividades\n";
    echo "  - Criada tabela obra_atividades_vinculo\n";
    echo "  - Adicionado campo progresso_real em obra_etapas\n\n";

    echo "Funcionalidades habilitadas:\n";
    echo "  - Técnico deve selecionar etapa obrigatóriamente\n";
    echo "  - Vinculação com atividades planejadas\n";
    echo "  - Progresso da etapa atualizado automaticamente\n";
    echo "  - Dashboard mostra atividades em andamento\n\n";

    echo "Próximos passos:\n";
    echo "  1. Acesse: Área do Técnico > Minhas Obras\n";
    echo "  2. Selecione uma obra\n";
    echo "  3. Clique em 'Iniciar Atividade' no card verde\n";
    echo "  4. Selecione a etapa obrigatóriamente\n";
    echo "  5. Registre Hora Início/Fim\n\n";

} catch (Exception $e) {
    echo "\n❌ Erro na instalação: " . $e->getMessage() . "\n\n";
    echo "Verifique se o arquivo application/config/database.php está configurado corretamente.\n";
    exit(1);
}
