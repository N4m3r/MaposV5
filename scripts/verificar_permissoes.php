<?php
/**
 * Script para verificar e atualizar permissões
 * Mostra feedback detalhado
 */

echo "=== VERIFICAÇÃO DE PERMISSÕES ===\n\n";

// Verifica se consegue carregar o CodeIgniter
try {
    require_once 'application/config/database.php';
    echo "✓ Configuração do banco carregada\n";
} catch (Exception $e) {
    echo "✗ Erro ao carregar configuração: " . $e->getMessage() . "\n";
    exit;
}

$db = $db['default'];
echo "→ Conectando ao banco: {$db['database']}...\n";

$mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($mysqli->connect_error) {
    die("✗ Erro de conexão: " . $mysqli->connect_error . "\n");
}

echo "✓ Conectado ao banco!\n\n";

// Verifica se tabela existe
$result = $mysqli->query("SHOW TABLES LIKE 'permissoes'");
if ($result->num_rows == 0) {
    die("✗ Tabela 'permissoes' não encontrada!\n");
}
echo "✓ Tabela 'permissoes' existe\n\n";

// Buscar todas as permissões
$result = $mysqli->query("SELECT idPermissao, nome, permissoes FROM permissoes WHERE situacao = 1 ORDER BY idPermissao");

if (!$result) {
    die("✗ Erro ao buscar permissões: " . $mysqli->error . "\n");
}

$total = $result->num_rows;
echo "→ Encontradas {$total} permissões ativas:\n\n";

$novasPermissoes = [
    // Certificado
    'vCertificado' => 'Certificado Digital',
    'cCertificado' => 'Adicionar Certificado',
    'eCertificado' => 'Editar Certificado',
    'dCertificado' => 'Excluir Certificado',
    // Impostos
    'vImpostos' => 'Impostos Simples',
    'cImpostos' => 'Adicionar Impostos',
    'eImpostos' => 'Editar Impostos',
    'dImpostos' => 'Excluir Impostos',
    'cImpostosConfig' => 'Configurar Impostos',
    'vImpostosRelatorio' => 'Relatório de Impostos',
    'vImpostosExportar' => 'Exportar Impostos',
    // DRE Contábil
    'vDRE' => 'Visualizar DRE',
    'vDREDemonstracao' => 'Demonstração DRE',
    'vDREContas' => 'Plano de Contas DRE',
    'vDRELancamentos' => 'Lançamentos DRE',
    'cDRE' => 'Adicionar DRE',
    'eDRE' => 'Editar DRE',
    'dDRE' => 'Excluir DRE',
    // Outras
    'vRelatorioAtendimentos' => 'Relatório de Atendimentos',
    'vWebhooks' => 'Webhooks',
    'cDocOs' => 'Vincular Documentos à OS',
    // Dashboard
    'vDashboard' => 'Dashboard',
    'vRelatorioCompleto' => 'Relatório Completo',
    'vExportarDados' => 'Exportar Dados',
    // Portal do Cliente
    'vUsuariosCliente' => 'Usuários Cliente',
    'cUsuariosCliente' => 'Adicionar Usuário Cliente',
    'eUsuariosCliente' => 'Editar Usuário Cliente',
    'dUsuariosCliente' => 'Excluir Usuário Cliente',
    'cPermUsuariosCliente' => 'Permissões Usuário Cliente',
    // Técnicos
    'vRelatorioTecnicos' => 'Relatório de Técnicos',
    'vBtnAtendimento' => 'Botão Atendimento',
    'vTecnicoOS' => 'Técnico Visualizar OS',
    'eTecnicoCheckin' => 'Técnico Checkin',
    'eTecnicoCheckout' => 'Técnico Checkout',
    'eTecnicoFotos' => 'Técnico Fotos',
];

$atualizadas = 0;

while ($row = $result->fetch_assoc()) {
    $id = $row['idPermissao'];
    $nome = $row['nome'];
    $permissoesAtuais = @unserialize($row['permissoes']);

    if (!is_array($permissoesAtuais)) {
        echo "⚠ Permissão '$nome' (ID: $id) - Dados corrompidos, inicializando...\n";
        $permissoesAtuais = [];
    }

    echo "→ '$nome' (ID: $id): ";

    // Verifica quais novas permissões estão faltando
    $faltando = [];
    foreach ($novasPermissoes as $chave => $descricao) {
        if (!isset($permissoesAtuais[$chave])) {
            $faltando[] = $chave;
            $permissoesAtuais[$chave] = 1;
        }
    }

    if (empty($faltando)) {
        echo "OK (já atualizada)\n";
    } else {
        // Atualiza no banco
        $novasPermissoesSerializadas = serialize($permissoesAtuais);
        $stmt = $mysqli->prepare("UPDATE permissoes SET permissoes = ? WHERE idPermissao = ?");
        $stmt->bind_param("si", $novasPermissoesSerializadas, $id);

        if ($stmt->execute()) {
            echo "✓ ATUALIZADA (+" . implode(', ', $faltando) . ")\n";
            $atualizadas++;
        } else {
            echo "✗ ERRO: " . $stmt->error . "\n";
        }
        $stmt->close();
    }
}

echo "\n=== RESUMO ===\n";
echo "Total de permissões verificadas: {$total}\n";
echo "Permissões atualizadas: {$atualizadas}\n";

if ($atualizadas > 0) {
    echo "\n✓ Novas funcionalidades liberadas!\n";
    echo "Faça logout e login novamente para aplicar as mudanças.\n";
} else {
    echo "\n✓ Todas as permissões já estão atualizadas.\n";
}

$mysqli->close();
