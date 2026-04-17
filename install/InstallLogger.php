<?php

/**
 * InstallLogger - Sistema de logs detalhado para a instalação do MapOS V5
 *
 * Registra cada etapa do processo de instalação com nível de detalhe
 * suficiente para diagnosticar qualquer problema.
 *
 * Níveis de log:
 *   INFO    - Informações gerais de progresso
 *   WARN    - Avisos que não impedem a instalação
 *   ERROR   - Erros que impedem a instalação
 *   DEBUG   - Dados técnicos detalhados para diagnóstico
 *   STEP    - Marcação de etapas do processo
 */

class InstallLogger
{
    private $logDir;
    private $session_id;
    private $start_time;
    private $step_times = [];
    private $current_step = null;
    private $step_start = null;

    // Níveis de log com cores para terminal
    private const LEVELS = [
        'INFO'  => 'INFO',
        'WARN'  => 'WARN',
        'ERROR' => 'ERROR',
        'DEBUG' => 'DEBUG',
        'STEP'  => 'STEP',
    ];

    public function __construct($logDir = null)
    {
        $this->logDir = $logDir ?: __DIR__ . DIRECTORY_SEPARATOR . 'logs';
        $this->session_id = date('Ymd_His') . '_' . substr(uniqid(), -6);
        $this->start_time = microtime(true);

        // Garantir que o diretório de logs existe
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }

        // Criar arquivo .htaccess se não existir (proteção)
        $htaccess = $this->logDir . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Deny from all\n");
        }

        // Criar arquivo .gitkeep para manter o diretório no git
        $gitkeep = $this->logDir . DIRECTORY_SEPARATOR . '.gitkeep';
        if (!file_exists($gitkeep)) {
            file_put_contents($gitkeep, '');
        }
    }

    /**
     * Log de informação geral
     */
    public function info($message, $data = null)
    {
        $this->write('INFO', $message, $data);
    }

    /**
     * Log de aviso (não impede a instalação)
     */
    public function warn($message, $data = null)
    {
        $this->write('WARN', $message, $data);
    }

    /**
     * Log de erro (impede a instalação)
     */
    public function error($message, $data = null)
    {
        $this->write('ERROR', $message, $data);
    }

    /**
     * Log de debug (dados técnicos detalhados)
     */
    public function debug($message, $data = null)
    {
        $this->write('DEBUG', $message, $data);
    }

    /**
     * Marca o início de uma etapa da instalação
     */
    public function stepStart($stepNumber, $stepName)
    {
        $this->current_step = $stepNumber;
        $this->step_start = microtime(true);
        $this->write('STEP', ">>> ETAPA {$stepNumber} INICIADA: {$stepName}");
    }

    /**
     * Marca a conclusão de uma etapa da instalação
     */
    public function stepEnd($stepNumber, $success = true)
    {
        $elapsed = isset($this->step_start) ? round(microtime(true) - $this->step_start, 3) : 0;
        $this->step_times[$stepNumber] = $elapsed;
        $status = $success ? 'OK' : 'FALHOU';
        $this->write('STEP', "<<< ETAPA {$stepNumber} FINALIZADA: {$status} ({$elapsed}s)");
        $this->current_step = null;
        $this->step_start = null;
    }

    /**
     * Registra o ambiente do servidor (PHP, extensões, etc.)
     */
    public function logServerEnvironment()
    {
        $this->write('INFO', '=== AMBIENTE DO SERVIDOR ===');

        // PHP
        $this->write('DEBUG', 'Versão PHP', [
            'version' => PHP_VERSION,
            'sapi' => PHP_SAPI,
            'os' => PHP_OS,
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'display_errors' => ini_get('display_errors'),
            'error_reporting' => error_reporting(),
            'allow_url_fopen' => ini_get('allow_url_fopen'),
            'disable_functions' => ini_get('disable_functions'),
        ]);

        // Extensões críticas
        $extensions = ['mysqli', 'curl', 'gd', 'zip', 'mbstring', 'openssl', 'json', 'pdo', 'fileinfo'];
        $extStatus = [];
        foreach ($extensions as $ext) {
            $extStatus[$ext] = extension_loaded($ext) ? 'CARREGADA' : 'NÃO CARREGADA';
        }
        $this->write('DEBUG', 'Extensões PHP', $extStatus);

        // Diretório de trabalho
        $this->write('DEBUG', 'Diretórios', [
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'install_dir' => __DIR__,
            'log_dir' => $this->logDir,
        ]);
    }

    /**
     * Registra os dados recebidos via POST (ocultando senhas)
     */
    public function logPostData($postData)
    {
        $safe = $postData;
        // Ocultar senhas nos logs
        foreach (['dbpassword', 'password'] as $key) {
            if (isset($safe[$key]) && $safe[$key] !== '') {
                $safe[$key] = '********';
            }
        }
        $this->write('DEBUG', 'Dados POST recebidos', $safe);
    }

    /**
     * Registra detalhes da conexão MySQL
     */
    public function logMySqlConnection($mysqli, $host, $dbname)
    {
        $this->write('DEBUG', 'Detalhes da conexão MySQL', [
            'host' => $host,
            'database' => $dbname,
            'server_info' => $mysqli->server_info ?? 'N/A',
            'server_version' => $mysqli->server_version ?? 0,
            'client_info' => $mysqli->client_info ?? 'N/A',
            'charset' => $mysqli->character_set_name(),
            'thread_id' => $mysqli->thread_id,
            'protocol_version' => $mysqli->protocol_version,
        ]);
    }

    /**
     * Registra resultado de uma query SQL
     */
    public function logQueryResult($queryLabel, $success, $affectedRows = null, $error = null, $querySnippet = null)
    {
        $data = [
            'success' => $success,
            'affected_rows' => $affectedRows,
        ];
        if ($error) {
            $data['error'] = $error;
        }
        if ($querySnippet) {
            // Limitar snippet para não poluir o log
            $data['query_snippet'] = mb_substr($querySnippet, 0, 200) . (mb_strlen($querySnippet) > 200 ? '...' : '');
        }
        $level = $success ? 'DEBUG' : 'ERROR';
        $this->write($level, "Query [{$queryLabel}]", $data);
    }

    /**
     * Registra o resultado do multi_query do banco.sql
     */
    public function logMultiQueryResult($totalQueries, $errors, $tables)
    {
        $this->write('INFO', 'Resultado do banco.sql', [
            'total_queries_processadas' => $totalQueries,
            'erros_encontrados' => count($errors),
            'tabelas_criadas' => count($tables),
        ]);

        if (!empty($errors)) {
            $this->write('ERROR', 'Erros no banco.sql', $errors);
        }

        $this->write('DEBUG', 'Lista de tabelas criadas', $tables);
    }

    /**
     * Registra detalhes da criação do arquivo .env
     */
    public function logEnvCreation($outputPath, $fileSize, $replacements)
    {
        $this->write('DEBUG', 'Arquivo .env criado', [
            'path' => $outputPath,
            'size_bytes' => $fileSize,
            'replacements_feitos' => array_keys($replacements),
            // Não logar valores, apenas as chaves substituídas
        ]);
    }

    /**
     * Registra permissões de diretórios/arquivos
     */
    public function logFilePermissions($paths)
    {
        $results = [];
        foreach ($paths as $label => $path) {
            $results[$label] = [
                'path' => $path,
                'exists' => file_exists($path),
                'readable' => is_readable($path),
                'writable' => is_writable($path),
                'perms' => file_exists($path) ? substr(sprintf('%o', @fileperms($path)), -4) : 'N/A',
            ];
        }
        $this->write('DEBUG', 'Permissões de arquivos/diretórios', $results);
    }

    /**
     * Registra o resultado final da instalação
     */
    public function logFinalResult($success, $message, $details = [])
    {
        $totalTime = round(microtime(true) - $this->start_time, 3);

        $data = array_merge([
            'success' => $success,
            'message' => $message,
            'tempo_total_segundos' => $totalTime,
            'tempos_por_etapa' => $this->step_times,
            'session_id' => $this->session_id,
        ], $details);

        $level = $success ? 'INFO' : 'ERROR';
        $this->write($level, '=== RESULTADO FINAL DA INSTALAÇÃO ===', $data);
    }

    /**
     * Escreve a entrada de log no arquivo
     */
    private function write($level, $message, $data = null)
    {
        $timestamp = date('Y-m-d H:i:s') . '.' . sprintf('%03d', (int)(microtime(true) * 1000) % 1000);
        $elapsed = round(microtime(true) - $this->start_time, 3);

        $stepTag = $this->current_step ? "[Etapa {$this->current_step}]" : '';
        $prefix = "[{$timestamp}] [+{$elapsed}s] [{$level}] {$stepTag} ";

        $line = $prefix . $message;

        if ($data !== null) {
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            if ($json === false) {
                $json = '(erro ao codificar JSON: ' . json_last_error_msg() . ')';
            }
            $line .= "\n" . $json;
        }

        $line .= "\n";

        // Escrever no arquivo da sessão
        $sessionFile = $this->logDir . DIRECTORY_SEPARATOR . 'install_' . $this->session_id . '.log';
        file_put_contents($sessionFile, $line, FILE_APPEND | LOCK_EX);

        // Escrever no arquivo de log geral (todas as sessões)
        $generalFile = $this->logDir . DIRECTORY_SEPARATOR . 'install.log';
        file_put_contents($generalFile, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Retorna o caminho do arquivo de log da sessão atual
     */
    public function getLogFile()
    {
        return $this->logDir . DIRECTORY_SEPARATOR . 'install_' . $this->session_id . '.log';
    }

    /**
     * Retorna o ID da sessão atual
     */
    public function getSessionId()
    {
        return $this->session_id;
    }
}