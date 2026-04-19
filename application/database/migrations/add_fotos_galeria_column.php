<?php
/**
 * Script para adicionar coluna fotos_galeria_json na tabela tec_os_execucao
 * Execute: php application/database/migrations/add_fotos_galeria_column.php
 */

require_once 'application/config/database.php';

$db = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($db->connect_error) {
    die("Erro na conexao: " . $db->connect_error);
}

// Adicionar coluna fotos_galeria_json
$sql = "ALTER TABLE `tec_os_execucao` ADD COLUMN IF NOT EXISTS `fotos_galeria_json` JSON NULL AFTER `fotos_durante`";

if ($db->query($sql) === TRUE) {
    echo "Coluna fotos_galeria_json adicionada com sucesso!\n";
} else {
    echo "Erro ao adicionar coluna: " . $db->error . "\n";
}

// Atualizar coluna checklist_completude (se necessario)
$sql2 = "ALTER TABLE `tec_os_execucao` CHANGE COLUMN IF EXISTS `progresso_execucao` `checklist_completude` INT DEFAULT 0";
if ($db->query($sql2) === TRUE) {
    echo "Coluna renomeada com sucesso!\n";
}

$db->close();
echo "Concluido!\n";
