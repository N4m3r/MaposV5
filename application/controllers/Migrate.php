<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller para executar migrações via web
 * Acesso restrito a administradores
 */
class Migrate extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logado')) {
            redirect('login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para executar migrações.');
            redirect(base_url());
        }

        $this->load->library('migration');
    }

    /**
     * Página principal - Painel de migrações
     */
    public function index()
    {
        $migrationsPath = APPPATH . 'database/migrations/';
        $migrations = [];
        $currentVersion = $this->_getCurrentVersion();

        if (is_dir($migrationsPath)) {
            $files = glob($migrationsPath . '*.php');
            foreach ($files as $file) {
                $filename = basename($file);
                if (preg_match('/^(\d{14})_(.+)\.php$/', $filename, $matches)) {
                    $migrations[] = [
                        'version' => $matches[1],
                        'name' => $matches[2],
                        'file' => $filename,
                        'applied' => ($matches[1] <= $currentVersion)
                    ];
                }
            }
        }

        usort($migrations, function ($a, $b) {
            return strcmp($a['version'], $b['version']);
        });

        $this->data['migrations'] = $migrations;
        $this->data['current_version'] = $currentVersion;
        $this->data['view'] = 'migrate/index';

        return $this->layout();
    }

    /**
     * Desabilita db_debug temporariamente para evitar show_error()/exit()
     */
    private function _disableDbDebug()
    {
        $this->db->db_debug = false;
    }

    private function _enableDbDebug()
    {
        $this->db->db_debug = true;
    }

    /**
     * Executa todas as migrações pendentes
     */
    public function latest()
    {
        $isAjax = $this->input->is_ajax_request();
        $this->_disableDbDebug();

        ob_start();
        $result = $this->migration->latest();
        $output = ob_get_clean();

        $this->_enableDbDebug();

        if ($result === false) {
            $error = $this->migration->error_string();
            if (empty($error) && !empty($output)) {
                $error = strip_tags($output);
            }
            $dbError = $this->db->error();
            if (!empty($dbError['message']) && empty($error)) {
                $error = $dbError['message'];
            }
            log_message('error', 'Erro ao executar migrações: ' . $error);

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error]);
                return;
            }

            $this->session->set_flashdata('error', 'Erro ao executar migrações: ' . $error);
            redirect('migrate');
            return;
        }

        // Sucesso
        log_message('info', 'Migrações executadas com sucesso pelo usuário: ' . $this->session->userdata('nome'));

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Migrações executadas com sucesso!']);
            return;
        }

        $this->session->set_flashdata('success', 'Migrações executadas com sucesso!');
        redirect('migrate');
    }

    /**
     * Executa migrações uma por vez para isolar erros
     */
    public function runSequential()
    {
        $isAjax = $this->input->is_ajax_request();
        $this->_disableDbDebug();

        $migrationsPath = APPPATH . 'database/migrations/';
        $currentVersion = $this->_getCurrentVersion();
        $configVersion = $this->config->item('migration_version');

        // Coletar migrations pendentes
        $pending = [];
        if (is_dir($migrationsPath)) {
            $files = glob($migrationsPath . '*.php');
            foreach ($files as $file) {
                $filename = basename($file);
                if (preg_match('/^(\d{14})_(.+)\.php$/', $filename, $matches)) {
                    $ts = $matches[1];
                    if ($ts > $currentVersion && $ts <= $configVersion) {
                        $pending[$ts] = $matches[2];
                    }
                }
            }
        }

        ksort($pending);

        if (empty($pending)) {
            $this->_enableDbDebug();
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Nenhuma migration pendente.', 'ran' => 0, 'failed' => 0]);
                return;
            }
            $this->session->set_flashdata('success', 'Nenhuma migration pendente.');
            redirect('migrate');
            return;
        }

        $ran = 0;
        $failed = 0;
        $errors = [];

        foreach ($pending as $ts => $name) {
            ob_start();
            $result = $this->migration->version($ts);
            $output = ob_get_clean();

            if ($result === false) {
                $failed++;
                $error = $this->migration->error_string();
                if (empty($error) && !empty($output)) {
                    $error = strip_tags($output);
                }
                $dbError = $this->db->error();
                if (!empty($dbError['message']) && empty($error)) {
                    $error = $dbError['message'];
                }
                $errors[] = "{$ts}_{$name}: {$error}";
                log_message('error', "Migration falhou: {$ts}_{$name} - {$error}");
                break;
            }

            $ran++;
            log_message('info', "Migration OK: {$ts}_{$name}");
        }

        $this->_enableDbDebug();

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => ($failed === 0),
                'message' => $failed === 0
                    ? "{$ran} migrações executadas com sucesso!"
                    : "Executadas {$ran}, {$failed} falhou. Erro: " . implode('; ', $errors),
                'ran' => $ran,
                'failed' => $failed,
                'errors' => $errors
            ]);
            return;
        }

        if ($failed > 0) {
            $this->session->set_flashdata('error', 'Erros: ' . implode('; ', $errors));
        } else {
            $this->session->set_flashdata('success', "{$ran} migrações executadas com sucesso!");
        }
        redirect('migrate');
    }

    /**
     * Executa uma migração específica
     */
    public function version($version = null)
    {
        $isAjax = $this->input->is_ajax_request();

        if ($version === null) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Versão não especificada']);
                return;
            }
            $this->session->set_flashdata('error', 'Versão não especificada');
            redirect('migrate');
            return;
        }

        $this->_disableDbDebug();
        ob_start();
        $result = $this->migration->version($version);
        $output = ob_get_clean();
        $this->_enableDbDebug();

        if ($result === false) {
            $error = $this->migration->error_string();
            if (empty($error) && !empty($output)) {
                $error = strip_tags($output);
            }
            $dbError = $this->db->error();
            if (!empty($dbError['message']) && empty($error)) {
                $error = $dbError['message'];
            }

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error]);
                return;
            }

            $this->session->set_flashdata('error', 'Erro: ' . $error);
            redirect('migrate');
            return;
        }

        log_message('info', 'Migração ' . $version . ' executada pelo usuário: ' . $this->session->userdata('nome'));

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Migração ' . $version . ' executada com sucesso!']);
            return;
        }

        $this->session->set_flashdata('success', 'Migração ' . $version . ' executada com sucesso!');
        redirect('migrate');
    }

    /**
     * Reverte todas as migrações
     */
    public function reset()
    {
        $isAjax = $this->input->is_ajax_request();

        if (!$this->input->post('confirmar') && !$isAjax) {
            $this->session->set_flashdata('error', 'Requer confirmação para reverter migrações');
            redirect('migrate');
            return;
        }

        $this->_disableDbDebug();
        ob_start();
        $result = $this->migration->version(0);
        $output = ob_get_clean();
        $this->_enableDbDebug();

        if ($result === false) {
            $error = $this->migration->error_string();
            if (empty($error) && !empty($output)) {
                $error = strip_tags($output);
            }
            $dbError = $this->db->error();
            if (!empty($dbError['message']) && empty($error)) {
                $error = $dbError['message'];
            }

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error]);
                return;
            }

            $this->session->set_flashdata('error', 'Erro ao reverter migrações: ' . $error);
            redirect('migrate');
            return;
        }

        log_message('info', 'Migrações revertidas pelo usuário: ' . $this->session->userdata('nome'));

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Migrações revertidas com sucesso!']);
            return;
        }

        $this->session->set_flashdata('success', 'Migrações revertidas com sucesso!');
        redirect('migrate');
    }

    /**
     * API - Status das migrações (JSON)
     */
    public function status()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('migrate');
            return;
        }

        $migrationsPath = APPPATH . 'database/migrations/';
        $currentVersion = $this->_getCurrentVersion();
        $configVersion = $this->config->item('migration_version');
        $pending = [];
        $applied = [];

        if (is_dir($migrationsPath)) {
            $files = glob($migrationsPath . '*.php');
            foreach ($files as $file) {
                $filename = basename($file);
                if (preg_match('/^(\d{14})_(.+)\.php$/', $filename, $matches)) {
                    if ($matches[1] <= $currentVersion) {
                        $applied[] = $matches[1];
                    } else {
                        $pending[] = $matches[1];
                    }
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'current_version' => $currentVersion,
            'config_version' => $configVersion,
            'pending_count' => count($pending),
            'pending_versions' => $pending,
            'applied_count' => count($applied),
            'applied_versions' => $applied
        ]);
    }

    /**
     * Obtém a versão atual do banco de dados
     * CI3 armazena apenas uma linha com a versão atual na tabela migrations
     */
    private function _getCurrentVersion()
    {
        if (!$this->db->table_exists('migrations')) {
            return 0;
        }

        $row = $this->db->get('migrations')->row();
        if ($row && isset($row->version)) {
            return $row->version;
        }

        return 0;
    }
}