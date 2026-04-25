#!/usr/bin/env php
<?php
/**
 * Script de instalação do Sistema de Registro de Atividades
 *
 * Este script executa a migration necessária para criar as tabelas
 * do sistema de atividades de técnicos.
 *
 * Uso: php install_atividades.php
 */

// Define o ambiente
$_SERVER['CI_ENV'] = 'development';

define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('ENVIRONMENT', 'development');

echo "========================================\n";
echo "  Instalação - Sistema de Atividades   \n";
echo "========================================\n\n";

// Carrega o CodeIgniter
require_once BASEPATH . 'core/Common.php';
require_once APPPATH . 'config/constants.php';

// Carrega a classe de migração
require_once APPPATH . 'database/migrations/20260421000002_create_atividades_tables.php';

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

            public function insert($table, $data) {
                $fields = implode(',', array_keys($data));
                $placeholders = implode(',', array_fill(0, count($data), '?'));
                $stmt = $this->pdo->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})");
                $stmt->execute(array_values($data));
                return $this->pdo->lastInsertId();
            }
        };
    }
}

// Executa a migração
try {
    echo "Conectando ao banco de dados...\n";
    $mock = new CI_Migration_Mock();

    echo "Executando migração...\n";
    $migration = new Migration_Create_atividades_tables();
    $migration->db = $mock->db;
    $migration->up();

    echo "\n✅ Instalação concluída com sucesso!\n\n";
    echo "Tabelas criadas:\n";
    echo "  - atividades_tipos (tipos de atividades)\n";
    echo "  - os_atividades (registros de atividades)\n";
    echo "  - atividades_materiais (materiais utilizados)\n";
    echo "  - atividades_fotos (fotos das atividades)\n";
    echo "  - atividades_checklist (itens de verificação)\n";
    echo "  - atividades_pausas (registro de pausas)\n\n";
    echo "Tipos de atividades padrão inseridos: 25+\n";
    echo "Categorias: Rede, CFTV, Segurança, Infra, Internet, Geral\n\n";
    echo "Próximos passos:\n";
    echo "  1. Acesse: Área do Técnico > Minhas Atividades\n";
    echo "  2. Selecione uma OS para iniciar\n";
    echo "  3. Faça o check-in (registra Hora Início)\n";
    echo "  4. Registre as atividades com início e fim\n";
    echo "  5. Finalize o atendimento (registra Hora Fim)\n\n";

} catch (Exception $e) {
    echo "\n❌ Erro na instalação: " . $e->getMessage() . "\n\n";
    echo "Verifique se o arquivo application/config/database.php está configurado corretamente.\n";
    exit(1);
}
