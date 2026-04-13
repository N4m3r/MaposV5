<?php
/**
 * Script de atualização de permissões
 * Adiciona permissões para DRE, Impostos Simples Nacional e Certificado Digital
 *
 * Executar via: php updates/update_permissoes_dre_impostos_certificado.php
 * Ou incluir no processo de atualização do sistema
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

    // Buscar permissões atuais do administrador (idPermissao = 1)
    $stmt = $pdo->query("SELECT idPermissao, permissoes FROM permissoes WHERE idPermissao = 1 LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "Permissão de administrador não encontrada.\n";
        exit(1);
    }

    // Deserializar permissões
    $permissoes = @unserialize($row['permissoes']);

    if ($permissoes === false && $row['permissoes'] !== 'b:0;') {
        echo "Erro ao deserializar permissões existentes.\n";
        exit(1);
    }

    if (!is_array($permissoes)) {
        $permissoes = [];
    }

    // Novas permissões a serem adicionadas
    $novas_permissoes = [
        // Certificado Digital
        'vCertificado' => '1',
        'cCertificado' => '1',
        'eCertificado' => '1',
        'dCertificado' => '1',
        // Impostos Simples Nacional
        'vImpostos' => '1',
        'cImpostosConfig' => '1',
        'eImpostos' => '1',
        'vImpostosRelatorio' => '1',
        'vImpostosExportar' => '1',
        // DRE
        'vDRE' => '1',
        'vDRERelatorio' => '1',
        'cDREConta' => '1',
        'dDREConta' => '1',
        'cDRELancamento' => '1',
        'dDRELancamento' => '1',
        'vDRELancamento' => '1',
        'cDREIntegracao' => '1',
        'vDREExportar' => '1',
        'vDREAnalise' => '1',
    ];

    // Mesclar permissões
    $permissoes_atualizadas = array_merge($permissoes, $novas_permissoes);

    // Serializar e atualizar no banco
    $permissoes_serializadas = serialize($permissoes_atualizadas);

    $update_stmt = $pdo->prepare("UPDATE permissoes SET permissoes = ? WHERE idPermissao = 1");
    $update_stmt->execute([$permissoes_serializadas]);

    echo "Permissões atualizadas com sucesso!\n";
    echo "Novas permissões adicionadas:\n";
    foreach ($novas_permissoes as $key => $value) {
        if (!isset($permissoes[$key])) {
            echo "  - $key\n";
        }
    }
    echo "\nTotal de permissões agora: " . count($permissoes_atualizadas) . "\n";

} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage() . "\n";
    exit(1);
}
