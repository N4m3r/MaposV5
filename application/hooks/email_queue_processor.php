<?php
/**
 * Email Queue Processor Hook
 * Poor Man's Cron - Processa fila de emails e eventos durante requisições HTTP
 * Sem necessidade de cron externo
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Hook chamado após o controller executar.
 * Registra shutdown function para processamento não-bloqueante.
 */
function process_email_queue()
{
    // Registra processamento para após envio da resposta HTTP
    if (function_exists('register_shutdown_function')) {
        register_shutdown_function('email_queue_shutdown_handler');
    }
}

/**
 * Handler executado após a resposta HTTP ser enviada.
 * Processa emails pendentes e eventos agendados de forma silenciosa.
 */
function email_queue_shutdown_handler()
{
    // Desativa exibição de erros para não poluir output
    $oldDisplayErrors = ini_get('display_errors');
    ini_set('display_errors', '0');
    @error_reporting(0);

    try {
        // Obtém instancia CI
        $ci = &get_instance();
        if (!$ci) {
            return;
        }

        // Carrega dependências
        if (!isset($ci->db)) {
            $ci->load->database();
        }

        // Controle de frequencia: arquivo de lock com timestamp
        $lockFile = APPPATH . 'cache/email_queue.lock';

        // Le intervalo configurado no banco (default 60 segundos)
        $intervalSeconds = 60;
        if ($ci->db->table_exists('configuracoes')) {
            $query = $ci->db->where('config', 'email_queue_interval')->get('configuracoes');
            if ($query) {
                $row = $query->row();
                $intervalSeconds = (int) ($row->valor ?? 60);
            }
        }
        if ($intervalSeconds < 10) {
            $intervalSeconds = 10; // Minimo 10 segundos
        }

        if (file_exists($lockFile)) {
            $lastRun = (int) file_get_contents($lockFile);
            if ((time() - $lastRun) < $intervalSeconds) {
                return;
            }
        }

        // Atualiza lock
        file_put_contents($lockFile, (string) time());

        // ==========================
        // 1. Processa Eventos Agendados
        // ==========================
        try {
            require_once APPPATH . 'libraries/Scheduler/EventScheduler.php';
            $scheduler = new \Libraries\Scheduler\EventScheduler();
            $scheduler->process();
        } catch (\Exception $e) {
            log_message('error', '[EmailQueueHook] Erro ao processar eventos: ' . $e->getMessage());
        }

        // ==========================
        // 2. Processa Emails Pendentes
        // ==========================
        try {
            require_once APPPATH . 'libraries/Email/EmailQueue.php';
            $queue = new \Libraries\Email\EmailQueue();

            // Le batch size configurado no banco (default 3)
            $batchSize = 3;
            if ($ci->db->table_exists('configuracoes')) {
                $query = $ci->db->where('config', 'email_batch_size')->get('configuracoes');
                if ($query) {
                    $row = $query->row();
                    $batchSize = (int) ($row->valor ?? 3);
                }
            }
            if ($batchSize < 1 || $batchSize > 50) {
                $batchSize = 3;
            }

            // Busca emails pendentes
            $emails = $queue->process($batchSize);

            if (!empty($emails)) {
                require_once APPPATH . 'libraries/Email/SmtpPool.php';
                $smtp = new \Libraries\Email\SmtpPool();
                $results = $smtp->sendBatch($emails);

                foreach ($results as $id => $result) {
                    if ($result['success']) {
                        $queue->markAsSent($id, $result['message_id'] ?? '');
                    } else {
                        $queue->markAsFailed($id, $result['error'] ?? 'Unknown error');

                        // Notificacao interna para admins sobre falha
                        try {
                            $ci->load->model('notificacoes_model');
                            if (method_exists($ci->notificacoes_model, 'notificarTodos')) {
                                $emailInfo = $queue->find($id);
                                $toEmail = $emailInfo->to_email ?? 'desconhecido';
                                $subject = $emailInfo->subject ?? 'sem assunto';
                                $ci->notificacoes_model->notificarTodos([
                                    'titulo' => 'Falha no envio de email',
                                    'mensagem' => "Falha ao enviar email para {$toEmail} (Assunto: {$subject}). Erro: " . ($result['error'] ?? 'Unknown error'),
                                    'url' => base_url('email/logs?status=failed'),
                                    'icone' => 'bx-envelope',
                                    'tipo' => 'warning',
                                ]);
                            }
                        } catch (\Exception $e) {
                            log_message('error', '[EmailQueueHook] Erro ao notificar falha: ' . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', '[EmailQueueHook] Erro ao processar emails: ' . $e->getMessage());
        }
    } catch (\Throwable $t) {
        // Silencia qualquer erro para nao afetar o usuario
        error_log('[EmailQueueHook] Fatal: ' . $t->getMessage());
    } finally {
        ini_set('display_errors', $oldDisplayErrors);
    }
}
