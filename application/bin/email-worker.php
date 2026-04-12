<?php
/**
 * Email Worker - Processa fila de emails em background
 * Uso: php email-worker.php [start|stop|status]
 */

// Configurações
$PID_FILE = __DIR__ . '/email-worker.pid';
$LOG_FILE = __DIR__ . '/../../logs/email-worker.log';

// Garante diretório de logs
if (!is_dir(dirname($LOG_FILE))) {
    mkdir(dirname($LOG_FILE), 0755, true);
}

function log_message($msg) {
    global $LOG_FILE;
    $line = date('Y-m-d H:i:s') . " - {$msg}\n";
    file_put_contents($LOG_FILE, $line, FILE_APPEND);
    echo $line;
}

function is_running() {
    global $PID_FILE;
    
    if (!file_exists($PID_FILE)) {
        return false;
    }
    
    $pid = file_get_contents($PID_FILE);
    
    // Verifica se processo existe (Windows/Linux)
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec("tasklist /FI \"PID eq {$pid}\" 2>NUL", $output);
        return count($output) > 1;
    } else {
        return file_exists("/proc/{$pid}");
    }
}

function start_worker() {
    global $PID_FILE;
    
    if (is_running()) {
        echo "Worker já está rodando.\n";
        return;
    }
    
    log_message("Iniciando Email Worker...");
    
    // Fork processo (Linux) ou background (Windows)
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows - inicia em background
        $cmd = 'start /B php "' . __FILE__ . '" worker';
        pclose(popen($cmd, 'r'));
    } else {
        // Linux - daemonize
        $pid = pcntl_fork();
        
        if ($pid < 0) {
            die("Falha ao criar processo\n");
        } elseif ($pid > 0) {
            // Processo pai
            file_put_contents($PID_FILE, $pid);
            echo "Worker iniciado (PID: {$pid})\n";
            return;
        }
        
        // Processo filho - torna-se daemon
        posix_setsid();
        file_put_contents($PID_FILE, posix_getpid());
    }
    
    // Loop principal do worker
    run_worker();
}

function stop_worker() {
    global $PID_FILE;
    
    if (!is_running()) {
        echo "Worker não está rodando.\n";
        @unlink($PID_FILE);
        return;
    }
    
    $pid = file_get_contents($PID_FILE);
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec("taskkill /PID {$pid} /F 2>NUL");
    } else {
        posix_kill($pid, SIGTERM);
    }
    
    @unlink($PID_FILE);
    log_message("Worker parado.");
    echo "Worker parado.\n";
}

function run_worker() {
    log_message("Worker iniciado - PID: " . getmypid());
    
    // Carrega CodeIgniter
    require_once __DIR__ . '/../../index.php';
    
    $ci = &get_instance();
    $ci->load->database();
    
    $interval = 30; // segundos entre verificações
    
    while (true) {
        try {
            // Busca emails pendentes
            $query = $ci->db->get_where('email_queue', [
                'status' => 'pending',
                'scheduled_at <=' => date('Y-m-d H:i:s')
            ], 10);
            
            $emails = $query->result();
            
            if (count($emails) > 0) {
                log_message("Processando " . count($emails) . " emails...");
                
                // Carrega SMTP Pool
                require_once APPPATH . 'libraries/email/SmtpPool.php';
                $smtp = new \Libraries\Email\SmtpPool();
                
                $results = $smtp->sendBatch($emails);
                
                // Atualiza status
                foreach ($results as $id => $result) {
                    if ($result['success']) {
                        $ci->db->where('id', $id);
                        $ci->db->update('email_queue', [
                            'status' => 'sent',
                            'sent_at' => date('Y-m-d H:i:s')
                        ]);
                        log_message("Email {$id} enviado.");
                    } else {
                        $ci->db->where('id', $id);
                        $ci->db->set('retry_count', 'retry_count + 1', false);
                        $ci->db->update('email_queue', [
                            'status' => 'failed',
                            'error_message' => $result['error']
                        ]);
                        log_message("Email {$id} falhou: " . $result['error']);
                    }
                }
            }
        } catch (Exception $e) {
            log_message("Erro: " . $e->getMessage());
        }
        
        sleep($interval);
    }
}

// Comandos CLI
$command = $argv[1] ?? 'status';

switch ($command) {
    case 'start':
        start_worker();
        break;
    case 'stop':
        stop_worker();
        break;
    case 'status':
        if (is_running()) {
            echo "Worker está rodando.\n";
        } else {
            echo "Worker não está rodando.\n";
        }
        break;
    case 'worker':
        run_worker();
        break;
    default:
        echo "Uso: php email-worker.php [start|stop|status]\n";
}
