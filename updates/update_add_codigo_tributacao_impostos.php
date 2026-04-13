<?php
/**
 * Script de atualização: Adicionar configurações de código de tributação para NFS-e
 * Data: 2026-04-13
 */

require_once __DIR__ . '/../application/config/database.php';

// Conectar ao banco
$host = $db['default']['hostname'];
$user = $db['default']['username'];
$pass = $db['default']['password'];
$dbname = $db['default']['database'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configurações a serem adicionadas
    $configs = [
        ['IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL', '010701', 'Código de Tributação Nacional (LC 116/2003)'],
        ['IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL', '100', 'Código de Tributação Municipal'],
        ['IMPOSTO_DESCRICAO_SERVICO', 'Suporte técnico em informática, inclusive instalação, configuração e manutenção de programas de computação e bancos de dados.', 'Descrição do serviço para NFS-e'],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO config_sistema_impostos (chave, valor, descricao) VALUES (?, ?, ?)");

    foreach ($configs as $config) {
        $stmt->execute($config);
    }

    echo "Configurações de código de tributação adicionadas com sucesso!\n";
    echo "Código Nacional: 010701\n";
    echo "Código Municipal: 100\n";
    echo "Descrição: Suporte técnico em informática\n";

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}
