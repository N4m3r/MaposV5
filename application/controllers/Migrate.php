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

        // Verifica se está logado
        if (!$this->session->userdata('logado')) {
            redirect('login');
        }

        // Verifica permissão de administrador
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
        // Obtém lista de migrações pendentes
        $migrationsPath = APPPATH . 'database/migrations/';
        $migrations = [];

        if (is_dir($migrationsPath)) {
            $files = glob($migrationsPath . '*.php');
            foreach ($files as $file) {
                $filename = basename($file);
                if (preg_match('/^(\d{14})_(.+)\.php$/', $filename, $matches)) {
                    $migrations[] = [
                        'version' => $matches[1],
                        'name' => $matches[2],
                        'file' => $filename,
                        'applied' => $this->isMigrationApplied($matches[1])
                    ];
                }
            }
        }

        // Ordena por versão
        usort($migrations, function ($a, $b) {
            return strcmp($a['version'], $b['version']);
        });

        $this->data['migrations'] = $migrations;
        $this->data['current_version'] = $this->getCurrentVersion();
        $this->data['view'] = 'migrate/index';

        return $this->layout();
    }

    /**
     * Executa todas as migrações pendentes
     */
    public function latest()
    {
        try {
            if ($this->migration->latest() === false) {
                $error = $this->migration->error_string();
                log_error('Erro ao executar migrações: ' . $error);

                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => false, 'message' => $error]);
                    return;
                }

                $this->session->set_flashdata('error', 'Erro ao executar migrações: ' . $error);
            } else {
                log_info('Migrações executadas com sucesso pelo usuário: ' . $this->session->userdata('nome'));

                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => true, 'message' => 'Migrações executadas com sucesso!']);
                    return;
                }

                $this->session->set_flashdata('success', 'Migrações executadas com sucesso!');
            }
        } catch (Exception $e) {
            log_error('Erro ao executar migrações: ' . $e->getMessage());

            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                return;
            }

            $this->session->set_flashdata('error', 'Erro: ' . $e->getMessage());
        }

        redirect('migrate');
    }

    /**
     * Executa uma migração específica
     */
    public function version($version = null)
    {
        if ($version === null) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Versão não especificada']);
                return;
            }

            $this->session->set_flashdata('error', 'Versão não especificada');
            redirect('migrate');
        }

        try {
            if ($this->migration->version($version) === false) {
                $error = $this->migration->error_string();

                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => false, 'message' => $error]);
                    return;
                }

                $this->session->set_flashdata('error', 'Erro: ' . $error);
            } else {
                log_info('Migração ' . $version . ' executada pelo usuário: ' . $this->session->userdata('nome'));

                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => true, 'message' => 'Migração ' . $version . ' executada com sucesso!']);
                    return;
                }

                $this->session->set_flashdata('success', 'Migração ' . $version . ' executada com sucesso!');
            }
        } catch (Exception $e) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                return;
            }

            $this->session->set_flashdata('error', 'Erro: ' . $e->getMessage());
        }

        redirect('migrate');
    }

    /**
     * Reverte todas as migrações
     */
    public function reset()
    {
        // Verifica se é POST ou confirmação AJAX
        if (!$this->input->post('confirmar') && !$this->input->is_ajax_request()) {
            $this->session->set_flashdata('error', 'Requer confirmação para reverter migrações');
            redirect('migrate');
        }

        try {
            if ($this->migration->version(0) === false) {
                $error = $this->migration->error_string();

                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => false, 'message' => $error]);
                    return;
                }

                $this->session->set_flashdata('error', 'Erro ao reverter migrações: ' . $error);
            } else {
                log_info('Migrações revertidas pelo usuário: ' . $this->session->userdata('nome'));

                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => true, 'message' => 'Migrações revertidas com sucesso!']);
                    return;
                }

                $this->session->set_flashdata('success', 'Migrações revertidas com sucesso!');
            }
        } catch (Exception $e) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                return;
            }

            $this->session->set_flashdata('error', 'Erro: ' . $e->getMessage());
        }

        redirect('migrate');
    }

    /**
     * API - Status das migrações (JSON)
     */
    public function status()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('migrate');
        }

        $migrationsPath = APPPATH . 'database/migrations/';
        $pending = [];
        $applied = [];

        if (is_dir($migrationsPath)) {
            $files = glob($migrationsPath . '*.php');
            foreach ($files as $file) {
                $filename = basename($file);
                if (preg_match('/^(\d{14})_(.+)\.php$/', $filename, $matches)) {
                    if ($this->isMigrationApplied($matches[1])) {
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
            'current_version' => $this->getCurrentVersion(),
            'pending_count' => count($pending),
            'pending_versions' => $pending,
            'applied_count' => count($applied),
            'applied_versions' => $applied
        ]);
    }

    /**
     * Verifica se uma migração foi aplicada
     */
    private function isMigrationApplied($version)
    {
        $query = $this->db->get_where('migrations', ['version' => $version]);
        return $query->num_rows() > 0;
    }

    /**
     * Obtém a versão atual do banco de dados
     */
    private function getCurrentVersion()
    {
        $row = $this->db->select_max('version')->get('migrations')->row();
        return $row ? $row->version : 0;
    }
}
