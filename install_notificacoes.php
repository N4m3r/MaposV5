#!/usr/bin/env php
<?php
/**
 * Script de instalação do Sistema de Notificações WhatsApp
 *
 * Este script executa a migration necessária para criar as tabelas
 * do sistema de notificações.
 *
 * Uso: php install_notificacoes.php
 */

// Define o ambiente
$_SERVER['CI_ENV'] = 'development';

define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('ENVIRONMENT', 'development');

echo "========================================\n";
echo "  Instalação - Notificações WhatsApp\n";
echo "========================================\n\n";

// Carrega o CodeIgniter
require_once BASEPATH . 'core/Common.php';
require_once APPPATH . 'config/constants.php';

// Carrega a classe de migração
require_once APPPATH . 'database/migrations/20260421000001_create_notificacoes_whatsapp_tables.php';

// Simula o CI
class CI_Migration_Mock {
    public $db;

    public function __construct() {
        // Carrega configuração do banco
        $db_config = require APPPATH . 'config/database.php';
        $active_group = 'default';
        $active_record = true;

        // Conecta ao banco
        $db = $db_config['db'][$active_group];

        $this->db = new class($db) {
            private $pdo;
            private $db_config;

            public function __construct($config) {
                $this->db_config = $config;
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
                $stmt = $this->pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                return $stmt->rowCount() > 0;
            }

            public function field_exists($field, $table) {
                $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
                $stmt->execute([$field]);
                return $stmt->rowCount() > 0;
            }

            public function insert($table, $data) {
                $fields = implode(',', array_keys($data));
                $placeholders = implode(',', array_fill(0, count($data), '?'));
                $stmt = $this->pdo->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})");
                return $stmt->execute(array_values($data));
            }

            public function replace($table, $data) {
                $fields = implode(',', array_keys($data));
                $placeholders = implode(',', array_fill(0, count($data), '?'));
                $updates = [];
                foreach (array_keys($data) as $field) {
                    $updates[] = "{$field} = VALUES({$field})";
                }
                $update_str = implode(',', $updates);
                $stmt = $this->pdo->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders}) ON DUPLICATE KEY UPDATE {$update_str}");
                return $stmt->execute(array_values($data));
            }
        };
    }
}

// Executa a migração
try {
    echo "Conectando ao banco de dados...\n";
    $mock = new CI_Migration_Mock();

    echo "Executando migração...\n";
    $migration = new Migration_Create_notificacoes_whatsapp_tables();
    $migration->db = $mock->db;
    $migration->up();

    echo "\n✅ Instalação concluída com sucesso!\n\n";
    echo "Tabelas criadas:\n";
    echo "  - notificacoes_config\n";
    echo "  - notificacoes_templates\n";
    echo "  - notificacoes_log\n";
    echo "  - notificacoes_agendadas\n";
    echo "  - clientes_notificacoes_consent\n\n";
    echo "Templates padrão inseridos: 9\n\n";
    echo "Próximos passos:\n";
    echo "  1. Acesse: Configurações > Notificações > WhatsApp\n";
    echo "  2. Configure seu provedor (Evolution API, Meta API ou Z-API)\n";
    echo "  3. Conecte seu WhatsApp\n";
    echo "  4. Teste o envio\n\n";

} catch (Exception $e) {
    echo "\n❌ Erro na instalação: " . $e->getMessage() . "\n\n";
    echo "Verifique se o arquivo application/config/database.php está configurado corretamente.\n";
    exit(1);
}
