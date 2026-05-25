<?php

class Login extends CI_Controller
{
    private static $loginAttempts = [];
    private static $MAX_ATTEMPTS = 5;
    private static $LOCKOUT_MINUTES = 15;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mapos_model');
    }

    public function index()
    {
        $data['emitente'] = $this->mapos_model->getEmitente();
        $this->load->view('mapos/login', $data);
    }

    public function sair()
    {
        $this->session->sess_destroy();

        return redirect('login');
    }

    public function verificarLogin()
    {
        header('Access-Control-Allow-Origin: ' . base_url());
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type');

        // Rate limiting
        $ip = $this->input->ip_address();
        $lockoutInfo = $this->isLockedOut($ip);
        if ($lockoutInfo['locked']) {
            $json = ['result' => false, 'message' => 'Conta temporariamente bloqueada. Tente novamente em ' . $lockoutInfo['minutes'] . ' minutos.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()];
            echo json_encode($json);
            exit();
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'E-mail', 'valid_email|required|trim');
        $this->form_validation->set_rules('senha', 'Senha', 'required|trim');
        if ($this->form_validation->run() == false) {
            $json = ['result' => false, 'message' => validation_errors()];
            echo json_encode($json);
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('senha');
            $this->load->model('Mapos_model');
            $user = $this->Mapos_model->check_credentials($email);

            if ($user) {
                // Verificar se acesso esta expirado
                if ($this->chk_date($user->dataExpiracao)) {
                    $json = ['result' => false, 'message' => 'A conta do usuario esta expirada, por favor entre em contato com o administrador do sistema.'];
                    echo json_encode($json);
                    exit();
                }

                // Verificar credenciais do usuario
                if (password_verify($password, $user->senha)) {
                    // Login bem-sucedido: regenerar sessao para prevenir fixacao
                    $this->session->sess_regenerate();
                    // Limpar tentativas
                    $this->clearAttempts($ip);
                    $session_admin_data = ['nome_admin' => $user->nome, 'email_admin' => $user->email, 'url_image_user_admin' => $user->url_image_user, 'id_admin' => $user->idUsuarios, 'permissao' => $user->permissoes_id, 'logado' => true];
                    $this->session->set_userdata($session_admin_data);
                    log_info('Efetuou login no sistema');
                    $json = ['result' => true];
                    echo json_encode($json);
                } else {
                    $this->recordFailedAttempt($ip);
                    $remaining = self::$MAX_ATTEMPTS - $this->getAttemptCount($ip);
                    $json = ['result' => false, 'message' => 'Os dados de acesso estao incorretos.' . ($remaining > 0 ? ' Tentativas restantes: ' . $remaining : ''), 'MAPOS_TOKEN' => $this->security->get_csrf_hash()];
                    echo json_encode($json);
                }
            } else {
                $this->recordFailedAttempt($ip);
                $remaining = self::$MAX_ATTEMPTS - $this->getAttemptCount($ip);
                $json = ['result' => false, 'message' => 'Usuario nao encontrado, verifique se suas credenciais estao corretas.' . ($remaining > 0 ? ' Tentativas restantes: ' . $remaining : ''), 'MAPOS_TOKEN' => $this->security->get_csrf_hash()];
                echo json_encode($json);
            }
        }
        exit();
    }

    /**
     * Registra tentativa de login falhada
     */
    private function recordFailedAttempt($ip)
    {
        $key = $this->getAttemptKey($ip);

        // Usar cache de arquivo para persistir entre requisicoes
        $cacheFile = APPPATH . 'cache/login_attempts_' . md5($ip) . '.json';
        $attempts = [];
        if (file_exists($cacheFile)) {
            $attempts = json_decode(file_get_contents($cacheFile), true) ?: [];
        }

        $attempts[] = time();

        // Manter apenas tentativas dos ultimos 15 minutos
        $cutoff = time() - (self::$LOCKOUT_MINUTES * 60);
        $attempts = array_filter($attempts, function($t) use ($cutoff) { return $t > $cutoff; });

        @file_put_contents($cacheFile, json_encode($attempts));
    }

    /**
     * Obtem o numero de tentativas falhadas recentes
     */
    private function getAttemptCount($ip)
    {
        $cacheFile = APPPATH . 'cache/login_attempts_' . md5($ip) . '.json';
        if (!file_exists($cacheFile)) {
            return 0;
        }

        $attempts = json_decode(file_get_contents($cacheFile), true) ?: [];
        $cutoff = time() - (self::$LOCKOUT_MINUTES * 60);

        return count(array_filter($attempts, function($t) use ($cutoff) { return $t > $cutoff; }));
    }

    /**
     * Verifica se o IP esta bloqueado por tentativas excessivas
     */
    private function isLockedOut($ip)
    {
        $count = $this->getAttemptCount($ip);
        if ($count >= self::$MAX_ATTEMPTS) {
            $cacheFile = APPPATH . 'cache/login_attempts_' . md5($ip) . '.json';
            $attempts = json_decode(file_get_contents($cacheFile), true) ?: [];
            $cutoff = time() - (self::$LOCKOUT_MINUTES * 60);
            $recentAttempts = array_filter($attempts, function($t) use ($cutoff) { return $t > $cutoff; });
            if (!empty($recentAttempts)) {
                $oldestAttempt = min($recentAttempts);
                $secondsLeft = (self::$LOCKOUT_MINUTES * 60) - (time() - $oldestAttempt);
                $minutesLeft = max(1, ceil($secondsLeft / 60));
                return ['locked' => true, 'minutes' => $minutesLeft];
            }
        }
        return ['locked' => false, 'minutes' => 0];
    }

    /**
     * Limpa tentativas apos login bem-sucedido
     */
    private function clearAttempts($ip)
    {
        $cacheFile = APPPATH . 'cache/login_attempts_' . md5($ip) . '.json';
        if (file_exists($cacheFile)) {
            @unlink($cacheFile);
        }
    }

    private function getAttemptKey($ip)
    {
        return 'login_attempts_' . md5($ip);
    }

    private function chk_date($data_banco)
    {
        $data_banco = new DateTime($data_banco);
        $data_hoje = new DateTime('now');

        return $data_banco < $data_hoje;
    }
}