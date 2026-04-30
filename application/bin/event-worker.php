<?php
/**
 * Event Worker
 * Worker em loop contínuo para processamento de eventos agendados
 *
 * Uso: php application/bin/event-worker.php
 */

define('BASEPATH', dirname(__DIR__, 2) . '/');
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'production');
define('APPPATH', BASEPATH . 'application/');
define('FCPATH', BASEPATH);

if (PHP_SAPI !== 'cli') {
    die("Este script deve ser executado via CLI\n");
}

require_once BASEPATH . 'index.php';
require_once APPPATH . 'libraries/scheduler/EventScheduler.php';

use Libraries\Scheduler\EventScheduler;

class EventWorker
{
    private EventScheduler $scheduler;
    private bool $running = true;
    private string $pidFile;
    private array $stats = [
        'processed' => 0,
        'completed' => 0,
        'failed' => 0,
        'started_at' => null
    ];

    public function __construct()
    {
        $this->scheduler = new EventScheduler();
        $this->pidFile = APPPATH . 'cache/event-worker.pid';
        $this->stats['started_at'] = date('Y-m-d H:i:s');
    }

    /**
     * Inicia o worker
     */
    public function start(): void
    {
        if ($this->isRunning()) {
            echo "[ERRO] Event Worker j&aacute; est&aacute; rodando (PID: " . $this->getPid() . ")\n";
            exit(1);
        }

        $this->createPidFile();

        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'shutdown']);
            pcntl_signal(SIGINT, [$this, 'shutdown']);
        }

        echo "[INFO] Event Worker iniciado em " . date('Y-m-d H:i:s') . "\n";
        echo "[INFO] PID: " . getmypid() . "\n";
        echo str_repeat("-", 60) . "\n";

        while ($this->running) {
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

            $this->checkControlFile();

            try {
                $count = $this->processEvents();

                if ($count > 0) {
                    echo "[INFO] " . date('H:i:s') . " - {$count} eventos processados\n";
                }

                // Verifica eventos a cada minuto
                for ($i = 0; $i < 60 && $this->running; $i++) {
                    sleep(1);
                }
            } catch (\Exception $e) {
                echo "[ERRO] " . $e->getMessage() . "\n";
                error_log("[EventWorker] " . $e->getMessage());
                sleep(60);
            }
        }

        $this->cleanup();
        echo "[INFO] Event Worker finalizado em " . date('Y-m-d H:i:s') . "\n";
    }

    /**
     * Processa eventos agendados
     */
    private function processEvents(): int
    {
        $ci = \u0026get_instance();
        $ci->load->database();

        $events = $ci->db
            ->where('status', 'pending')
            ->where('execute_at <=', date('Y-m-d H:i:s'))
            ->get('scheduled_events')
            ->result();

        $count = 0;
        foreach ($events as $event) {
            try {
                $this->scheduler->process();
                $count++;
                $this->stats['completed']++;
            } catch (\Exception $e) {
                $this->stats['failed']++;
                echo "[ERRO] Falha ao processar evento {$event->id}: " . $e->getMessage() . "\n";
            }
        }

        $this->stats['processed'] += $count;
        return $count;
    }

    /**
     * Verifica arquivo de controle
     */
    private function checkControlFile(): void
    {
        $controlFile = APPPATH . 'cache/event-worker.control';

        if (!file_exists($controlFile)) {
            return;
        }

        $command = trim(file_get_contents($controlFile));
        unlink($controlFile);

        switch ($command) {
            case 'stop':
                echo "[INFO] Comando 'stop' recebido\n";
                $this->shutdown();
                break;

            case 'status':
                $this->printStatus();
                break;
        }
    }

    /**
     * Imprime status atual
     */
    private function printStatus(): void
    {
        $running = time() - strtotime($this->stats['started_at']);
        $hours = floor($running / 3600);
        $minutes = floor(($running % 3600) / 60);

        echo str_repeat("=", 60) . "\n";
        echo "STATUS DO EVENT WORKER\n";
        echo str_repeat("=", 60) . "\n";
        echo "PID: " . getmypid() . "\n";
        echo "Rodando h&aacute;: {$hours}h {$minutes}m\n";
        echo "Processados: {$this->stats['processed']}\n";
        echo "Completados: {$this->stats['completed']}\n";
        echo "Falhas: {$this->stats['failed']}\n";
        echo str_repeat("=", 60) . "\n";
    }

    /**
     * Graceful shutdown
     */
    public function shutdown(): void
    {
        echo "\n[INFO] Recebido sinal de parada...\n";
        $this->running = false;
    }

    private function isRunning(): bool
    {
        if (!file_exists($this->pidFile)) {
            return false;
        }

        $pid = (int) file_get_contents($this->pidFile);

        if (function_exists('posix_kill')) {
            return posix_kill($pid, 0);
        }

        return (time() - filemtime($this->pidFile)) < 300;
    }

    private function getPid(): ?int
    {
        return file_exists($this->pidFile)
            ? (int) file_get_contents($this->pidFile)
            : null;
    }

    private function createPidFile(): void
    {
        file_put_contents($this->pidFile, getmypid());
    }

    private function cleanup(): void
    {
        if (file_exists($this->pidFile)) {
            unlink($this->pidFile);
        }
    }
}

// CLI Interface
$command = $argv[1] ?? 'start';

switch ($command) {
    case 'start':
        $worker = new EventWorker();
        $worker->start();
        break;

    case 'stop':
        file_put_contents(APPPATH . 'cache/event-worker.control', 'stop');
        echo "[INFO] Comando de parada enviado\n";
        break;

    case 'status':
        file_put_contents(APPPATH . 'cache/event-worker.control', 'status');
        echo "[INFO] Solicitando status...\n";
        break;

    case 'restart':
        file_put_contents(APPPATH . 'cache/event-worker.control', 'stop');
        sleep(2);
        $worker = new EventWorker();
        $worker->start();
        break;

    default:
        echo "Uso: php event-worker.php [start|stop|restart|status]\n";
        exit(1);
}
