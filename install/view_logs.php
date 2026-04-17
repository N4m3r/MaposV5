<?php
/**
 * Visualizador de Logs da Instalação - MapOS V5
 * Acessar via: http://seudominio.com/install/view_logs.php
 *
 * SEGURANÇA: Este arquivo deve ser DELETADO junto com o diretório install/
 * após a instalação ser concluída.
 */

// Verificar se a instalação já foi feita
$is_installed = file_exists('..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env');

$logDir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';

// Obter lista de arquivos de log
$logFiles = [];
if (is_dir($logDir)) {
    $files = glob($logDir . DIRECTORY_SEPARATOR . 'install_*.log');
    if ($files) {
        foreach ($files as $file) {
            $logFiles[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'modified' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }
    }
    // Ordenar por data de modificação (mais recente primeiro)
    usort($logFiles, function($a, $b) {
        return strtotime($b['modified']) - strtotime($a['modified']);
    });
}

// Obter log geral
$generalLog = $logDir . DIRECTORY_SEPARATOR . 'install.log';
$generalLogContent = '';
if (file_exists($generalLog)) {
    $generalLogContent = file_get_contents($generalLog);
}

// Obter conteúdo de um log específico
$selectedLog = $_GET['log'] ?? '';
$logContent = '';
if ($selectedLog && preg_match('/^install_\d+_\w+\.log$/', $selectedLog)) {
    $logPath = $logDir . DIRECTORY_SEPARATOR . $selectedLog;
    if (file_exists($logPath)) {
        $logContent = file_get_contents($logPath);
    }
}

// Ação: limpar logs
if (isset($_POST['clear_logs']) && $_POST['clear_logs'] === 'true') {
    if (is_dir($logDir)) {
        $logFiles = glob($logDir . DIRECTORY_SEPARATOR . '*.log');
        foreach ($logFiles as $file) {
            @unlink($file);
        }
    }
    header('Location: view_logs.php?cleared=1');
    exit;
}

$cleared = isset($_GET['cleared']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MapOS V5 - Logs de Instalação</title>
    <link rel="stylesheet" href="../install/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" />
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .log-container {
            background: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 600px;
            overflow-y: auto;
        }
        .log-container .log-info { color: #4ec9b0; }
        .log-container .log-warn { color: #dcdcaa; }
        .log-container .log-error { color: #f44747; }
        .log-container .log-debug { color: #608b4e; }
        .log-container .log-step { color: #569cd6; font-weight: bold; }
        .log-container .log-json { color: #ce9178; }
        .log-container .log-timestamp { color: #6a9955; }
        .log-container .log-elapsed { color: #b5cea8; }
        .log-file-item { cursor: pointer; }
        .log-file-item:hover { background: #e8e8e8; }
        .log-file-item.active { background: #d4edfa; }
        .stats-box { background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 15px; }
        .error-count { color: #d73b3b; font-weight: bold; }
        .success-count { color: #3c763d; font-weight: bold; }
        h1 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1><i class="fa fa-file-text"></i> Logs de Instalação - MapOS V5</h1>

        <?php if ($cleared): ?>
            <div class="alert alert-success">Logs limpos com sucesso!</div>
        <?php endif; ?>

        <?php if ($is_installed): ?>
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i>
                <strong>Atenção:</strong> O sistema já está instalado. Delete o diretório <code>install/</code> por segurança.
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-box text-center">
                    <h3><?php echo count($logFiles); ?></h3>
                    <small>Arquivos de Log</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-box text-center">
                    <h3 class="error-count"><?php
                        $totalErrors = 0;
                        foreach ($logFiles as $lf) {
                            $content = file_get_contents($lf['path']);
                            $totalErrors += substr_count($content, '[ERROR]');
                        }
                        echo $totalErrors;
                    ?></h3>
                    <small>Erros Encontrados</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-box text-center">
                    <h3 class="success-count"><?php
                        $totalSuccess = 0;
                        foreach ($logFiles as $lf) {
                            $content = file_get_contents($lf['path']);
                            if (strpos($content, 'RESULTADO FINAL DA INSTALAÇÃO') !== false && strpos($content, '"success":true') !== false) {
                                $totalSuccess++;
                            }
                        }
                        echo $totalSuccess;
                    ?></h3>
                    <small>Instalações Bem-sucedidas</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-box text-center">
                    <h3><?php echo $generalLogContent ? round(strlen($generalLogContent) / 1024, 1) . ' KB' : '0 KB'; ?></h3>
                    <small>Tamanho Total dos Logs</small>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div style="margin-bottom: 15px;">
            <form method="post" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja limpar todos os logs?');">
                <button type="submit" name="clear_logs" value="true" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> Limpar Todos os Logs
                </button>
            </form>
            <?php if ($logContent): ?>
                <button onclick="copyLog()" class="btn btn-default btn-sm">
                    <i class="fa fa-copy"></i> Copiar Log
                </button>
            <?php endif; ?>
        </div>

        <div class="row">
            <!-- Lista de Logs -->
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong><i class="fa fa-list"></i> Sessões de Instalação</strong>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <?php if (empty($logFiles)): ?>
                            <div style="padding: 15px; color: #999;">Nenhum log de instalação encontrado.</div>
                        <?php else: ?>
                            <?php foreach ($logFiles as $lf): ?>
                                <div class="log-file-item <?php echo $selectedLog === $lf['name'] ? 'active' : ''; ?>"
                                     style="padding: 8px 15px; border-bottom: 1px solid #eee;"
                                     onclick="window.location='view_logs.php?log=<?php echo urlencode($lf['name']); ?>'">
                                    <div>
                                        <i class="fa fa-file-text-o"></i>
                                        <strong><?php echo htmlspecialchars($lf['name']); ?></strong>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo $lf['modified']; ?> |
                                        <?php echo round($lf['size'] / 1024, 1); ?> KB
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Conteúdo do Log -->
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>
                            <i class="fa fa-terminal"></i>
                            <?php echo $selectedLog ? htmlspecialchars($selectedLog) : 'Selecione um arquivo de log'; ?>
                        </strong>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <?php if ($logContent): ?>
                            <div class="log-container" id="logContent"><?php
                                // Colorir o log por tipo
                                $lines = explode("\n", htmlspecialchars($logContent));
                                foreach ($lines as $line) {
                                    $class = '';
                                    if (strpos($line, '[INFO]') !== false) $class = 'log-info';
                                    elseif (strpos($line, '[WARN]') !== false) $class = 'log-warn';
                                    elseif (strpos($line, '[ERROR]') !== false) $class = 'log-error';
                                    elseif (strpos($line, '[DEBUG]') !== false) $class = 'log-debug';
                                    elseif (strpos($line, '[STEP]') !== false) $class = 'log-step';
                                    elseif (strpos($line, '===') !== false) $class = 'log-step';

                                    if ($class) {
                                        echo '<span class="' . $class . '">' . $line . '</span>' . "\n";
                                    } else {
                                        echo $line . "\n";
                                    }
                                }
                            ?></div>
                        <?php elseif (empty($selectedLog) && $generalLogContent): ?>
                            <div class="log-container" id="logContent"><?php
                                $lines = explode("\n", htmlspecialchars($generalLogContent));
                                foreach ($lines as $line) {
                                    $class = '';
                                    if (strpos($line, '[INFO]') !== false) $class = 'log-info';
                                    elseif (strpos($line, '[WARN]') !== false) $class = 'log-warn';
                                    elseif (strpos($line, '[ERROR]') !== false) $class = 'log-error';
                                    elseif (strpos($line, '[DEBUG]') !== false) $class = 'log-debug';
                                    elseif (strpos($line, '[STEP]') !== false) $class = 'log-step';

                                    if ($class) {
                                        echo '<span class="' . $class . '">' . $line . '</span>' . "\n";
                                    } else {
                                        echo $line . "\n";
                                    }
                                }
                            ?></div>
                        <?php else: ?>
                            <div style="padding: 40px; text-align: center; color: #999;">
                                <i class="fa fa-file-text-o" style="font-size: 48px;"></i>
                                <p style="margin-top: 15px;">
                                    <?php echo $selectedLog ? 'Arquivo de log não encontrado.' : 'Selecione uma sessão de instalação para ver os detalhes.'; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Erros resumidos -->
                <?php if ($logContent && strpos($logContent, '[ERROR]') !== false): ?>
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <strong><i class="fa fa-exclamation-triangle"></i> Resumo de Erros</strong>
                    </div>
                    <div class="panel-body">
                        <?php
                        $lines = explode("\n", $logContent);
                        foreach ($lines as $line) {
                            if (strpos($line, '[ERROR]') !== false) {
                                echo '<div style="color: #d73b3b; font-family: monospace; font-size: 12px; margin-bottom: 5px;">' .
                                     htmlspecialchars($line) . '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function copyLog() {
        var logEl = document.getElementById('logContent');
        if (logEl) {
            var text = logEl.innerText || logEl.textContent;
            navigator.clipboard.writeText(text).then(function() {
                alert('Log copiado para a área de transferência!');
            }).catch(function() {
                // Fallback
                var ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                alert('Log copiado!');
            });
        }
    }
    </script>
</body>
</html>