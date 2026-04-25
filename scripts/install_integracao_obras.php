#!/usr/bin/env php
<?php
/**
 * Script de instalação da Integração Atividades ↔ Obras
 *
 * Este script executa a migration para integrar o sistema de atividades
 * (Hora Início/Fim) com o sistema de obras existente.
 *
 * Uso: php install_integracao_obras.php
 */

// Define o ambiente
$_SERVER['CI_ENV'] = 'development';

define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('ENVIRONMENT', 'development');

echo "========================================\n";
echo "  Integração: Atividades ↔ Obras       \n";
echo "========================================\n\n";

// Carrega o CodeIgniter
require_once BASEPATH . 'core/Common.php';
require_once APPPATH . 'config/constants.php';

// Carrega a classe de migração
require_once APPPATH . 'database/migrations/20260421000003_add_obra_id_to_atividades.php';

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
        };
    }
}

// Executa a migração
try {
    echo "Conectando ao banco de dados...\n";
    $mock = new CI_Migration_Mock();

    echo "Executando migração de integração...\n";
    $migration = new Migration_Add_obra_id_to_atividades();
    $migration->db = $mock->db;
    $migration->up();

    echo "\n✅ Integração instalada com sucesso!\n\n";
    echo "Alterações realizadas:\n";
    echo "  - Adicionado campo obra_id na tabela os_atividades\n";
    echo "  - Criada tabela obra_etapa_atividades_tipos\n";
    echo "  - Criada tabela obra_atividades_fotos\n\n";
    echo "Funcionalidades habilitadas:\n";
    echo "  - Registrar atividades em obras com Hora Início/Fim\n";
    echo "  - Visualizar atividades e fotos na página da obra\n";
    echo "  - Integração completa entre os dois sistemas\n\n";
    echo "Próximos passos:\n";
    echo "  1. Acesse: Área do Técnico > Minhas Obras\n";
    echo "  2. Selecione uma obra\n";
    echo "  3. Clique em 'Iniciar Atividade'\n";
    echo "  4. Use o wizard para registrar Hora Início/Fim\n\n";

} catch (Exception $e) {
    echo "\n❌ Erro na instalação: " . $e->getMessage() . "\n\n";
    echo "Verifique se o arquivo application/config/database.php está configurado corretamente.\n";
    exit(1);
}
