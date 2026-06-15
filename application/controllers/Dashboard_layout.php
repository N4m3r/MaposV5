<?php
/**
 * Controller: Dashboard_layout
 *
 * Persiste o layout customizado do dashboard por usuario (F3.1 + F3.2).
 *
 * Endpoints:
 *   GET  /dashboard_layout/listar              -> layout salvo do usuario logado
 *   POST /dashboard_layout/salvar              -> salva layout + visibilidade
 *   POST /dashboard_layout/resetar             -> remove customizacao
 *   GET  /dashboard_layout/perfis              -> lista os perfis de dashboard salvos
 *   POST /dashboard_layout/ativarPerfil        -> ativa um perfil salvo
 *   POST /dashboard_layout/salvarPerfil        -> salva o layout atual como novo perfil
 *
 * Tabela: dashboard_layouts
 *   - id
 *   - user_id   (NULL = global/perfil)
 *   - perfil    (chave do perfil: 'admin' | 'tecnico' | 'financeiro' | 'custom')
 *   - layout    (JSON: ordem dos widgets)
 *   - visibility (JSON: {widget: true|false})
 *   - ativo     (1 = perfil ativo para o usuario)
 *   - created_at, updated_at
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dashboard_layout extends MY_Controller
{
    private $table = 'dashboard_layouts';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_layout_model');
    }

    /**
     * Retorna o layout ativo do usuario logado.
     * Tenta primeiro um layout pessoal; se nao existir, retorna o do perfil
     * (admin/tecnico/financeiro) ou o padrao.
     */
    public function listar()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $perfil = $this->_perfilDoUsuario();
        $layout = $this->dashboard_layout_model->getAtivo($userId, $perfil);

        return $this->json_success(['layout' => $layout]);
    }

    /**
     * Salva o layout para o usuario logado.
     * POST body: { layout: ["kpis","quick","charts","atividades"], visibility: {...} }
     */
    public function salvar()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $layout = $this->input->post('layout');
        $vis    = $this->input->post('visibility');

        if (!is_array($layout) || !is_array($vis)) {
            return $this->json_error('layout e visibility devem ser arrays', 400);
        }

        // Validacao basica: chaves de widget sao strings curtas
        $layoutSanitizado = array_values(array_filter($layout, function($v) {
            return is_string($v) && preg_match('/^[a-z0-9_]{1,40}$/', $v);
        }));
        $visSanitizada = [];
        foreach ($vis as $k => $v) {
            if (is_string($k) && preg_match('/^[a-z0-9_]{1,40}$/', $k)) {
                $visSanitizada[$k] = (bool) $v;
            }
        }

        $perfil = $this->_perfilDoUsuario();
        $ok = $this->dashboard_layout_model->salvar($userId, $perfil, $layoutSanitizado, $visSanitizada);

        if (!$ok) {
            return $this->json_error('Falha ao salvar layout', 500);
        }

        // Invalida cache de configuracoes (se houver)
        $this->clearConfigCache();

        log_message('info', "[Dashboard] Usuario {$userId} (perfil {$perfil}) salvou layout customizado");
        return $this->json_success(['saved' => true]);
    }

    /**
     * Remove a customizacao do usuario (volta ao padrao).
     */
    public function resetar()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $perfil = $this->_perfilDoUsuario();
        $ok = $this->dashboard_layout_model->resetar($userId, $perfil);
        if (!$ok) {
            return $this->json_error('Falha ao resetar', 500);
        }
        return $this->json_success(['reset' => true]);
    }

    /**
     * Lista os perfis disponiveis (admin, tecnico, financeiro, custom).
     * GET /dashboard_layout/perfis
     */
    public function perfis()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $perfis = $this->dashboard_layout_model->listarPerfis($userId);
        return $this->json_success(['perfis' => $perfis]);
    }

    /**
     * Salva o layout atual como um novo perfil.
     * POST /dashboard_layout/salvarPerfil   body: {nome: 'Meu dashboard'}
     */
    public function salvarPerfil()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $nome = trim((string) $this->input->post('nome'));
        if (empty($nome) || strlen($nome) > 40) {
            return $this->json_error('Nome do perfil é obrigatório (até 40 caracteres)', 400);
        }

        $layout = $this->input->post('layout');
        $vis    = $this->input->post('visibility');
        if (!is_array($layout)) $layout = [];
        if (!is_array($vis))    $vis    = [];

        $perfilKey = 'user_' . $userId . '_' . substr(md5($nome), 0, 8);
        $ok = $this->dashboard_layout_model->salvar($userId, $perfilKey, $layout, $vis, $nome);

        if (!$ok) {
            return $this->json_error('Falha ao salvar perfil', 500);
        }
        return $this->json_success(['perfil' => $perfilKey, 'nome' => $nome]);
    }

    /**
     * Ativa um perfil salvo.
     * POST /dashboard_layout/ativarPerfil   body: {perfil: 'admin'}
     */
    public function ativarPerfil()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $perfil = trim((string) $this->input->post('perfil'));
        if (empty($perfil)) {
            return $this->json_error('perfil é obrigatório', 400);
        }

        $ok = $this->dashboard_layout_model->ativar($userId, $perfil);
        if (!$ok) {
            return $this->json_error('Falha ao ativar perfil', 500);
        }
        return $this->json_success(['perfil_ativo' => $perfil]);
    }

    /**
     * Determina o perfil padrao do usuario baseado em suas permissoes.
     */
    private function _perfilDoUsuario()
    {
        $perm = $this->session->userdata('permissao');
        // Tenta inferir
        if ($this->permission->checkPermission($perm, 'vLancamento')) return 'financeiro';
        if ($this->permission->checkPermission($perm, 'vTecnicoDashboard')) return 'tecnico';
        if ($this->permission->checkPermission($perm, 'cPermissao')) return 'admin';
        return 'padrao';
    }

    // ==============================================================
    private function json_success($data, $code = 200) { return json_success($data, $code); }
    private function json_error($message, $code = 400) { return json_error($message, $code); }
}
