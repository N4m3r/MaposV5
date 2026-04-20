<?php
/**
 * Script de Verificação do Banco de Dados - Sistema de Obras
 * Execute este script para verificar se todas as tabelas e colunas necessárias existem
 *
 * Acesse: https://seusite.com/MaposV5/verificar_bd_obras.php
 */

require_once 'application/config/database.php';

// Criar conexão
$conn = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

echo "<!DOCTYPE html><html><head><title>Verificação BD Obras</title></head><body>";
echo "<h1>Verificação do Banco de Dados - Sistema de Obras</h1>";
echo "<hr>";

$tabelas_necessarias = [
    'obras' => [
        'id', 'codigo', 'nome', 'cliente_id', 'tipo_obra', 'endereco',
        'status', 'percentual_concluido', 'data_inicio_contrato',
        'data_fim_prevista', 'ativo', 'created_at'
    ],
    'obra_equipe' => [
        'id', 'obra_id', 'tecnico_id', 'funcao', 'data_entrada',
        'ativo', 'created_at'
    ],
    'obra_etapas' => [
        'id', 'obra_id', 'numero_etapa', 'nome', 'descricao',
        'status', 'percentual_concluido', 'ativo'
    ],
    'obra_atividades' => [
        'id', 'obra_id', 'etapa_id', 'tecnico_id', 'titulo',
        'descricao', 'tipo', 'status', 'data_atividade', 'ativo'
    ],
    'obra_checkins' => [
        'id', 'obra_id', 'tecnico_id', 'check_in', 'check_out'
    ],
    'obra_diario' => [
        'id', 'obra_id', 'tecnico_id', 'data', 'atividade_realizada'
    ]
];

$total_ok = 0;
$total_erros = 0;

foreach ($tabelas_necessarias as $tabela => $colunas) {
    echo "<h2>Tabela: {$tabela}</h2>";

    // Verificar se tabela existe
    $result = $conn->query("SHOW TABLES LIKE '{$tabela}'");

    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Tabela existe</p>";

        // Verificar colunas
        $result_cols = $conn->query("SHOW COLUMNS FROM {$tabela}");
        $colunas_existentes = [];

        while ($row = $result_cols->fetch_assoc()) {
            $colunas_existentes[] = $row['Field'];
        }

        echo "<ul>";
        foreach ($colunas as $coluna) {
            if (in_array($coluna, $colunas_existentes)) {
                echo "<li style='color: green;'>✅ {$coluna}</li>";
                $total_ok++;
            } else {
                echo "<li style='color: red;'>❌ {$coluna} (AUSENTE)</li>";
                $total_erros++;
            }
        }
        echo "</ul>";

        // Mostrar dados da tabela
        $result_count = $conn->query("SELECT COUNT(*) as total FROM {$tabela}");
        $count = $result_count->fetch_assoc()['total'];
        echo "<p><strong>Registros:</strong> {$count}</p>";

    } else {
        echo "<p style='color: red;'>❌ Tabela NÃO EXISTE</p>";
        $total_erros += count($colunas);
    }

    echo "<hr>";
}

// Resumo
echo "<h2>Resumo</h2>";
echo "<p><strong>Total OK:</strong> {$total_ok}</p>";
echo "<p><strong>Total Erros:</strong> {$total_erros}</p>";

if ($total_erros == 0) {
    echo "<p style='color: green; font-size: 20px;'>✅ Todas as tabelas e colunas estão OK!</p>";
} else {
    echo "<p style='color: red; font-size: 20px;'>❌ Existem {$total_erros} problema(s) no banco de dados.</p>";
    echo "<p>Acesse o diagnóstico para corrigir: <a href='index.php/diagnostico_obras/corrigir'>Clique aqui</a></p>";
}

echo "</body></html>";

$conn->close();
?>
