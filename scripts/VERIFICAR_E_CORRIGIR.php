<?php
/**
 * =========================================================
 * VERIFICAÇÃO E CORREÇÃO MAPOS V5
 * Script completo para diagnosticar e corrigir problemas
 * =========================================================
 *
 * USO:
 * 1. Envie para: /home/jj-ferreiras/www/mapos3/VERIFICAR_E_CORRIGIR.php
 * 2. Acesse: https://jj-ferreiras.com.br/mapos3/VERIFICAR_E_CORRIGIR.php
 * 3. Veja o diagnóstico e correções aplicadas
 * 4. DELETE após usar!
 */

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Verificação MAPOS V5</title></head><body style='font-family:Arial,sans-serif;background:#f0f2f5;padding:20px;'>";
echo "<div style='max-width:900px;margin:0 auto;background:white;padding:30px;border-radius:10px;box-shadow:0 2px 15px rgba(0,0,0,0.1);'>";
echo "<h1 style='color:#2c3e50;text-align:center;margin-bottom:5px;'>🔍 VERIFICAÇÃO MAPOS V5</h1>";
echo "<p style='text-align:center;color:#666;margin-top:0;'>Diagnóstico e correção automática</p><hr>";

$erros = [];
$avisos = [];
$sucessos = [];

// =====================================================================
// 1. VERIFICAR CONEXÃO COM BANCO
// =====================================================================
echo "<h2 style='color:#3498db;'>📡 1. Conexão com Banco de Dados</h2>";

try {
    require_once 'application/config/database.php';
    $db = $db['default'];

    echo "<p><strong>Banco:</strong> {$db['database']}<br>";
    echo "<strong>Host:</strong> {$db['hostname']}</p>";

    $mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }

    echo "<p style='color:green;'>✅ Conectado com sucesso!</p>";
    $sucessos[] = "Conexão com banco";

} catch (Exception $e) {
    echo "<p style='color:red;'>❌ ERRO: " . $e->getMessage() . "</p>";
    $erros[] = "Conexão com banco: " . $e->getMessage();
    echo "</div></body></html>";
    exit;
}

// =====================================================================
// 2. VERIFICAR TABELAS NECESSÁRIAS
// =====================================================================
echo "<h2 style='color:#3498db;'>📋 2. Tabelas do Banco</h2><ul>";

$tabelas = [
    'permissoes' => 'Permissões de usuários',
    'email_queue' => 'Fila de emails',
    'email_tracking' => 'Rastreamento de emails',
    'scheduled_events' => 'Eventos agendados',
    'certificado_nfe_importada' => 'NFS-e importadas'
];

foreach ($tabelas as $tabela => $descricao) {
    $result = $mysqli->query("SHOW TABLES LIKE '$tabela'");
    if ($result->num_rows > 0) {
        echo "<li style='color:green;'>✅ $descricao ($tabela)</li>";
        $sucessos[] = "Tabela $tabela existe";
    } else {
        echo "<li style='color:red;'>❌ $descricao ($tabela) - <strong>FALTANDO</strong></li>";
        $erros[] = "Tabela $tabela não existe";
    }
}
echo "</ul>";

// =====================================================================
// 3. CORRIGIR PERMISSÕES
// =====================================================================
echo "<h2 style='color:#3498db;'>🔧 3. Correção de Permissões</h2>";

$novasPermissoes = [
    'vCertificado' => 'Certificado Digital',
    'vImpostos' => 'Impostos Simples',
    'vDRE' => 'DRE Contábil',
    'vRelatorioAtendimentos' => 'Relatório de Atendimentos',
    'vWebhooks' => 'Webhooks',
    'cDocOs' => 'Vincular Documentos à OS',
];

$result = $mysqli->query("SELECT idPermissao, nome, permissoes FROM permissoes WHERE situacao = 1");
$atualizadas = 0;

while ($row = $result->fetch_assoc()) {
    $perms = @unserialize($row['permissoes']);
    if (!is_array($perms)) $perms = [];

    $adicionadas = [];
    foreach ($novasPermissoes as $key => $desc) {
        if (!isset($perms[$key])) {
            $perms[$key] = 1;
            $adicionadas[] = $key;
        }
    }

    if (count($adicionadas) > 0) {
        $novaSerializada = serialize($perms);
        $stmt = $mysqli->prepare("UPDATE permissoes SET permissoes = ? WHERE idPermissao = ?");
        $stmt->bind_param("si", $novaSerializada, $row['idPermissao']);
        if ($stmt->execute()) {
            $atualizadas++;
            echo "<p style='color:green;margin:2px 0;'>✅ {$row['nome']}: +" . count($adicionadas) . " permissões</p>";
        }
        $stmt->close();
    }
}

if ($atualizadas == 0) {
    echo "<p style='color:orange;'>⚠️ Nenhuma permissão precisou ser atualizada</p>";
} else {
    echo "<p style='color:green;font-weight:bold;'>✅ Total de permissões atualizadas: $atualizadas</p>";
}

// =====================================================================
// 4. VERIFICAR ARQUIVOS DO SISTEMA
// =====================================================================
echo "<h2 style='color:#3498db;'>📁 4. Arquivos do Sistema</h2><ul>";

$arquivos = [
    'application/libraries/email/EmailQueue.php' => 'Biblioteca EmailQueue',
    'application/libraries/email/TemplateEngine.php' => 'Biblioteca TemplateEngine',
    'application/libraries/email/EmailTracker.php' => 'Biblioteca EmailTracker',
    'application/libraries/email/SmtpPool.php' => 'Biblioteca SmtpPool',
    'application/libraries/scheduler/EventScheduler.php' => 'Biblioteca EventScheduler',
    'application/controllers/Email.php' => 'Controller Email',
    'application/controllers/Webhook.php' => 'Controller Webhook',
    'application/controllers/Certificado.php' => 'Controller Certificado',
    'application/views/emails/dashboard.php' => 'View Dashboard Emails',
    'application/views/certificado/listar_nfse.php' => 'View Listar NFS-e',
];

foreach ($arquivos as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        echo "<li style='color:green;'>✅ $descricao</li>";
    } else {
        echo "<li style='color:red;'>❌ $descricao - <strong>FALTANDO</strong></li>";
        $erros[] = "Arquivo $arquivo não existe";
    }
}
echo "</ul>";

// =====================================================================
// 5. VERIFICAR ROTAS
// =====================================================================
echo "<h2 style='color:#3498db;'>🌐 5. Configuração de Rotas</h2>";

$rotasNecessarias = [
    'emails/dashboard',
    'nfse',
    'webhooks',
    'dre',
    'impostos',
    'certificado'
];

$conteudoRoutes = file_get_contents('application/config/routes.php');
$rotasOk = true;

foreach ($rotasNecessarias as $rota) {
    if (strpos($conteudoRoutes, $rota) !== false) {
        echo "<p style='color:green;margin:2px 0;'>✅ Rota '$rota' configurada</p>";
    } else {
        echo "<p style='color:red;margin:2px 0;'>❌ Rota '$rota' <strong>FALTANDO</strong></p>";
        $rotasOk = false;
    }
}

// =====================================================================
// 6. VERIFICAR MENU
// =====================================================================
echo "<h2 style='color:#3498db;'>📋 6. Menu do Sistema</h2>";

$conteudoMenu = file_get_contents('application/views/tema/menu.php');
$menusEsperados = ['vCertificado', 'vImpostos', 'vDRE', 'vWebhooks', 'vRelatorioAtendimentos'];
$menuOk = true;

foreach ($menusEsperados as $menu) {
    if (strpos($conteudoMenu, $menu) !== false) {
        echo "<p style='color:green;margin:2px 0;'>✅ Menu '$menu' presente</p>";
    } else {
        echo "<p style='color:red;margin:2px 0;'>❌ Menu '$menu' <strong>FALTANDO</strong></p>";
        $menuOk = false;
    }
}

// =====================================================================
// RESUMO
// =====================================================================
echo "<hr><h2 style='color:#2c3e50;'>📊 Resumo da Verificação</h2>";

$totalErros = count($erros);
$totalSucessos = count($sucessos);

if ($totalErros == 0) {
    echo "<div style='background:#d4edda;color:#155724;padding:20px;border-radius:5px;border-left:5px solid #28a745;'>";
    echo "<h3 style='margin-top:0;'>✅ TUDO CERTO!</h3>";
    echo "<p>Todas as verificações passaram. O sistema está configurado corretamente.</p>";
    echo "<p><strong>Próximo passo:</strong> Faça <b>logout e login</b> novamente para aplicar as permissões.</p>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da;color:#721c24;padding:20px;border-radius:5px;border-left:5px solid #dc3545;'>";
    echo "<h3 style='margin-top:0;'>⚠️ ATENÇÃO: $totalErros problema(s) encontrado(s)</h3>";
    echo "<p>Verifique os itens marcados em vermelho acima.</p>";
    echo "</div>";
}

// Lista o que foi corrigido
if ($atualizadas > 0) {
    echo "<div style='background:#fff3cd;color:#856404;padding:15px;border-radius:5px;margin-top:15px;'>";
    echo "<h4>🔧 Correções Aplicadas:</h4>";
    echo "<p>$atualizadas permissões de usuário foram atualizadas com as novas funcionalidades V5.</p>";
    echo "<p><strong>Faça logout e login para ver as mudanças!</strong></p>";
    echo "</div>";
}

// =====================================================================
// AÇÃO FINAL
// =====================================================================
echo "<hr><div style='background:#e3f2fd;padding:20px;border-radius:5px;margin-top:20px;'>";
echo "<h3>🗑️ Ação Requerida</h3>";
echo "<p>Por segurança, <strong>DELETE este arquivo</strong> após a verificação:</p>";
echo "<pre style='background:#333;color:#fff;padding:10px;border-radius:3px;'>";
echo "rm /home/jj-ferreiras/www/mapos3/VERIFICAR_E_CORRIGIR.php";
echo "</pre>";
echo "</div>";

echo "</div></body></html>";

$mysqli->close();
