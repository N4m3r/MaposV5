<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mine extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Conecte_model');
        $this->load->helper('Security_helper');
    }

    public function index()
    {
        // Se já está logado como usuário do portal, redireciona para painel
        if ($this->session->userdata('usuario_cliente_id')) {
            redirect('mine/painel');
        }
        // Se já está logado como cliente tradicional, redireciona para painel
        if ($this->session->userdata('conectado')) {
            redirect('mine/painel');
        }
        // Carrega a nova tela de login do portal do cliente
        $this->load->model('mapos_model');
        $data['emitente'] = $this->mapos_model->getEmitente();
        $this->load->view('conecte/login_usuario', $data);
    }

    /**
     * Login tradicional por email/senha do cliente (sistema antigo)
     * Mantido para compatibilidade
     */
    public function login_token()
    {
        // Se já está logado, redireciona
        if ($this->session->userdata('conectado')) {
            redirect('mine/painel');
        }
        $this->load->view('conecte/login');
    }

    public function sair()
    {
        $this->session->sess_destroy();
        redirect('mine');
    }

    public function resetarSenha()
    {
        $this->load->view('conecte/resetar_senha');
    }

    public function senhaSalvar()
    {
        $this->load->library('form_validation');
        $data['custom_error'] = '';
        $this->form_validation->set_rules('senha', 'Senha', 'required');

        if ($this->input->post('token') == null || $this->input->post('token') == '') {
            return redirect('mine');
        }
        if ($this->form_validation->run() == false) {
            echo json_encode(['result' => false, 'message' => 'Por favor digite uma senha']);
        } else {
            $token = $this->check_token($this->input->post('token'));
            $cliente = $this->check_credentials($token->email);

            if ($token == null && $cliente == null) {
                $session_mine_data = $cliente->nomeCliente ? ['nome' => $cliente->nomeCliente] : ['nome' => 'Inexistente'];
                $this->session->set_userdata($session_mine_data);
                log_info('Alteração de senha. Porém, os dados de acesso estão incorretos.');
                echo json_encode(['result' => false, 'message' => 'Os dados de acesso estão incorretos.']);
            } else {
                if ($token->email == $cliente->email) {
                    $data = [
                        'senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                    ];

                    $dataToken = [
                        'token_utilizado' => true,
                    ];
                    $this->load->model('resetSenhas_model', '', true);
                    if ($this->Conecte_model->edit('clientes', $data, 'idClientes', $cliente->idClientes) == true) {
                        if ($this->resetSenhas_model->edit('resets_de_senha', $dataToken, 'id', $token->id) == true) {
                            $session_mine_data = $cliente->nomeCliente ? ['nome' => $cliente->nomeCliente] : ['nome' => 'Inexistente'];
                            $this->session->set_userdata($session_mine_data);
                            log_info('Alteração da senha realizada com sucesso.');
                            echo json_encode(['result' => true]);
                        }
                    }
                } else {
                    $session_mine_data = $cliente->nomeCliente ? ['nome' => $cliente->nomeCliente] : ['nome' => 'Inexistente'];
                    $this->session->set_userdata($session_mine_data);
                    log_info('Alteração de senha. Porém, dados divergentes.');
                    echo json_encode(['result' => false, 'message' => 'Dados divergentes.']);
                }
            }
        }
    }

    public function tokenManual()
    {
        $this->load->library('form_validation');
        $data['custom_error'] = '';
        $this->form_validation->set_rules('token', 'Token', 'required');

        if ($this->form_validation->run('token') == false) {
            $this->session->set_flashdata(['error' => (validation_errors() ? 'Por favor digite o token' : false)]);

            return $this->load->view('conecte/token_digita');
        } else {
            $token = $this->check_token($this->input->post('token'));

            if ($this->validateDate($token->data_expiracao)) {
                $this->session->set_flashdata(['error' => 'Token expirado']);
                $session_mine_data = $token->email ? ['nome' => $token->email] : ['nome' => 'Inexistente'];
                $this->session->set_userdata($session_mine_data);
                log_info('Digitou Token. Porém, Token expirado');

                return redirect(base_url() . 'index.php/mine');
            } else {
                if ($token) {
                    if (($cliente = $this->check_credentials($token->email)) == null) {
                        $this->session->set_flashdata(['error' => 'Os dados de acesso estão incorretos.']);
                        $session_mine_data = $cliente->nomeCliente ? ['nome' => $cliente->nomeCliente] : ['nome' => 'Inexistente'];
                        $this->session->set_userdata($session_mine_data);
                        log_info('Digitou Token. Porém, os dados de acesso estão incorretos.');

                        return $this->load->view('conecte/token_digita');
                    } else {
                        if ($token->email == $cliente->email && $token->token_utilizado == false) {
                            return $this->load->view('conecte/nova_senha', $token);
                        } else {
                            $this->session->set_flashdata('error', 'Dados divergentes ou Token invalido.');
                            $session_mine_data = $cliente->nomeCliente ? ['nome' => $cliente->nomeCliente] : ['nome' => 'Inexistente'];
                            $this->session->set_userdata($session_mine_data);
                            log_info('Digitou Token. Porém, dados divergentes ou Token invalido.');

                            return redirect(base_url() . 'index.php/mine');
                        }
                    }
                } else {
                    $this->session->set_flashdata(['error' => 'Token Invalido']);
                    $session_mine_data = $token->email ? ['nome' => $token->email] : ['nome' => 'Inexistente'];
                    $this->session->set_userdata($session_mine_data);
                    log_info('Digitou Token. Porém, Token invalido.');

                    return $this->load->view('conecte/token_digita');
                }
            }
        }
        $this->load->view('conecte/token_digita');
    }

    public function verifyTokenSenha()
    {
        $token = $this->uri->uri_to_assoc(3);
        $token = $this->check_token($token['token']);

        if ($token == null || $token == '') {
            $this->session->set_flashdata(['error' => 'Token invalido']);
            $session_mine_data = $token->email ? ['nome' => $token->email] : ['nome' => 'Inexistente'];
            $this->session->set_userdata($session_mine_data);
            log_info('Acesso via link do email (Token). Porém, Token invalido.');

            return $this->load->view('conecte/token_digita');
        } else {
            if ($this->validateDate($token->data_expiracao)) {
                $this->session->set_flashdata(['error' => 'Token expirado']);
                $session_mine_data = $token->email ? ['nome' => $token->email] : ['nome' => 'Inexistente'];
                $this->session->set_userdata($session_mine_data);
                log_info('Acesso via link do email (Token). Porém, Token expirado');

                return redirect(base_url() . 'index.php/mine');
            } else {
                if ($token) {
                    if (($cliente = $this->check_credentials($token->email)) == null) {
                        $this->session->set_flashdata(['error' => 'Os dados de acesso estão incorretos.']);
                        $session_mine_data = $cliente->nomeCliente ? ['nome' => $cliente->nomeCliente] : ['nome' => 'Inexistente'];
                        $this->session->set_userdata($session_mine_data);
                        log_info('Acesso via link do email (Token). Porém, dados de acesso estão incorretos.');

                        return $this->load->view('conecte/token_digita');
                    } else {
                        if ($token->email == $cliente->email && $token->token_utilizado == false) {
                            return $this->load->view('conecte/nova_senha', $token);
                        } else {
                            $this->session->set_flashdata('error', 'Dados divergentes ou Token invalido.');
                            $session_mine_data = $cliente->nomeCliente ? ['nome' => $cliente->nomeCliente] : ['nome' => 'Inexistente'];
                            $this->session->set_userdata($session_mine_data);
                            log_info('Acesso via link do email (Token). Porém, dados divergentes ou Token invalido.');

                            return redirect(base_url() . 'index.php/mine');
                        }
                    }
                } else {
                    $this->session->set_flashdata(['error' => 'Token Invalido']);
                    $session_mine_data = $token->email ? ['nome' => $token->email] : ['nome' => 'Inexistente'];
                    $this->session->set_userdata($session_mine_data);
                    log_info('Acesso via link do email (Token). Porém, Token invalido.');

                    return $this->load->view('conecte/token_digita');
                }

                return $this->load->view('conecte/nova_senha', $token);
            }
        }
    }

    public function gerarTokenResetarSenha()
    {
        if (! $cliente = $this->check_credentials($this->input->post('email'))) {
            $this->session->set_flashdata(['error' => 'Os dados de acesso estão incorretos.']);
            $session_mine_data = $cliente ? ['nome' => $cliente->nomeCliente] : ['nome' => 'Inexistente'];
            $this->session->set_userdata($session_mine_data);
            log_info('Cliente solicitou alteração de senha. Porém falhou ao realizar solicitação!');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->load->helper('string');
            $this->load->model('resetSenhas_model', '', true);
            $data = [
                'email' => $cliente->email,
                'token' => random_string('alnum', 32),
                'data_expiracao' => date('Y-m-d H:i:s'),
            ];
            if ($this->resetSenhas_model->add('resets_de_senha', $data) == true) {
                $this->enviarRecuperarSenha($cliente->idClientes, $cliente->email, 'Recuperar Senha', json_encode($data));
                $session_mine_data = ['nome' => $cliente->nomeCliente];
                $this->session->set_userdata($session_mine_data);
                log_info('Cliente solicitou alteração de senha.');
                $this->session->set_flashdata('success', 'Solicitação realizada com sucesso! <br> Um e-mail com as instruções será enviado para ' . $cliente->email);
                redirect(base_url() . 'index.php/mine');
            } else {
                $this->session->set_flashdata('error', 'Falha ao realizar solicitação!');
                $session_mine_data = $cliente->nomeCliente ? ['nome' => $cliente->nomeCliente] : ['nome' => 'Inexistente'];
                $this->session->set_userdata($session_mine_data);
                log_info('Cliente solicitou alteração de senha. Porém falhou ao realizar solicitação!');
                redirect(current_url());
            }
        }
    }

    /**
     * Login de Usuário do Portal do Cliente (novo sistema)
     */
    public function login_usuario()
    {
        // Se já está logado, redireciona
        if ($this->session->userdata('usuario_cliente_id')) {
            redirect('mine/painel');
        }

        $this->load->model('usuarios_cliente_model');
        $this->load->model('mapos_model');

        if ($this->input->post()) {
            $email = $this->input->post('email');
            $senha = $this->input->post('senha');

            // Validação básica
            if (empty($email) || empty($senha)) {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['result' => false, 'message' => 'E-mail e senha são obrigatórios.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()]);
                    return;
                }
                $this->session->set_flashdata('error', 'E-mail e senha são obrigatórios.');
                redirect('mine');
            }

            $usuario = $this->usuarios_cliente_model->login($email, $senha);

            if ($usuario) {
                // Verifica se usuário está ativo
                if (!$usuario->ativo) {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['result' => false, 'message' => 'Usuário desativado. Contate o administrador.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()]);
                        return;
                    }
                    $this->session->set_flashdata('error', 'Usuário desativado. Contate o administrador.');
                    redirect('mine');
                }

                // Busca CNPJs do usuário
                $cnpjs = $this->usuarios_cliente_model->getCnpjs($usuario->id);
                $permissoes = $this->usuarios_cliente_model->getAllPermissoes($usuario->id);

                // Define sessão
                $session_data = [
                    'usuario_cliente_id' => $usuario->id,
                    'usuario_cliente_nome' => $usuario->nome,
                    'usuario_cliente_email' => $usuario->email,
                    'usuario_cliente_cnpjs' => $cnpjs,
                    'usuario_cliente_permissoes' => $permissoes,
                    'conectado' => true,
                    'tipo_acesso' => 'usuario_cliente'
                ];

                // Se tiver cliente vinculado, adiciona à sessão
                if ($usuario->cliente_id) {
                    $session_data['cliente_id'] = $usuario->cliente_id;
                }

                $this->session->set_userdata($session_data);
                log_info('Usuário do cliente efetuou login: ' . $usuario->email);

                // Resposta AJAX ou redirect
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['result' => true, 'redirect' => site_url('mine/painel')]);
                    return;
                }
                redirect('mine/painel');
            } else {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['result' => false, 'message' => 'E-mail ou senha incorretos.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()]);
                    return;
                }
                $this->session->set_flashdata('error', 'E-mail ou senha incorretos.');
                redirect('mine');
            }
        }

        $data['emitente'] = $this->mapos_model->getEmitente();
        $this->load->view('conecte/login_usuario', $data);
    }

    /**
     * Recuperar senha do usuário do portal
     */
    public function recuperar_senha()
    {
        $this->load->model('usuarios_cliente_model');
        $this->load->model('mapos_model');

        if ($this->input->post()) {
            $email = $this->input->post('email');

            $usuario = $this->usuarios_cliente_model->getByEmail($email);
            if ($usuario) {
                $token = $this->usuarios_cliente_model->gerarTokenReset($email);

                if ($token) {
                    // Enviar email com link
                    $this->enviarEmailRecuperacao($usuario, $token);
                    $this->session->set_flashdata('success', 'Enviamos instruções para recuperar sua senha no email informado.');
                }
            } else {
                $this->session->set_flashdata('error', 'E-mail não encontrado em nossa base de dados.');
            }

            redirect('mine/recuperar_senha');
        }

        $data['emitente'] = $this->mapos_model->getEmitente();
        $this->load->view('conecte/recuperar_senha', $data);
    }

    /**
     * Resetar senha com token
     */
    public function resetar_senha($token = null)
    {
        if (!$token) {
            redirect('mine/login_usuario');
        }

        $this->load->model('usuarios_cliente_model');
        $this->load->model('mapos_model');

        $usuario = $this->usuarios_cliente_model->validarTokenReset($token);

        if (!$usuario) {
            $this->session->set_flashdata('error', 'Link de recuperação inválido ou expirado.');
            redirect('mine/login_usuario');
        }

        if ($this->input->post()) {
            $novaSenha = $this->input->post('nova_senha');
            $confirmar = $this->input->post('confirmar_senha');

            if (strlen($novaSenha) < 6) {
                $this->session->set_flashdata('error', 'A senha deve ter pelo menos 6 caracteres.');
                redirect(current_url());
            }

            if ($novaSenha !== $confirmar) {
                $this->session->set_flashdata('error', 'As senhas não conferem.');
                redirect(current_url());
            }

            if ($this->usuarios_cliente_model->resetarSenha($token, $novaSenha)) {
                $this->session->set_flashdata('success', 'Senha alterada com sucesso! Faça login com a nova senha.');
                redirect('mine/login_usuario');
            } else {
                $this->session->set_flashdata('error', 'Erro ao alterar senha.');
            }
        }

        $data['token'] = $token;
        $data['emitente'] = $this->mapos_model->getEmitente();
        $this->load->view('conecte/resetar_senha_usuario', $data);
    }

    /**
     * Logout do usuário
     */
    public function sair_usuario()
    {
        $this->session->unset_userdata([
            'usuario_cliente_id',
            'usuario_cliente_nome',
            'usuario_cliente_email',
            'usuario_cliente_cnpjs',
            'usuario_cliente_permissoes',
            'conectado',
            'tipo_acesso',
            'cliente_id'
        ]);
        $this->session->set_flashdata('success', 'Você saiu do sistema.');
        redirect('mine/login_usuario');
    }

    /**
     * Verificar permissão do usuário
     */
    private function usuarioTemPermissao($chave)
    {
        $permissoes = $this->session->userdata('usuario_cliente_permissoes');

        if (!$permissoes || !isset($permissoes[$chave])) {
            return false;
        }

        return $permissoes[$chave] === true || $permissoes[$chave] === '1' || $permissoes[$chave] === 1;
    }

    /**
     * Login tradicional por token (mantido para compatibilidade)
     */
    public function login()
    {
        header('Access-Control-Allow-Origin: ' . base_url());
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'E-mail', 'valid_email|required|trim');
        $this->form_validation->set_rules('senha', 'Senha', 'required|trim');
        if ($this->form_validation->run() == false) {
            echo json_encode(['result' => false, 'message' => validation_errors()]);
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('senha');
            $cliente = $this->check_credentials($email);

            if ($cliente) {
                // Verificar se o cliente tem senha definida
                if (empty($cliente->senha)) {
                    log_info('Tentativa de login com cliente sem senha definida: ' . $email);
                    echo json_encode(['result' => false, 'message' => 'Conta sem senha definida. Por favor, use a opção "Esqueceu a senha?" para criar uma senha.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()]);
                    return;
                }
                // Verificar credenciais do usuário
                if (password_verify($password, $cliente->senha)) {
                    $session_mine_data = [
                        'nome' => $cliente->nomeCliente, 
                        'cliente_id' => $cliente->idClientes, 
                        'email' => $cliente->email, 
                        'conectado' => true, 
                        'isCliente' => true
                    ];
                    $this->session->set_userdata($session_mine_data);
                    log_info($_SERVER['REMOTE_ADDR'] . ' Efetuou login no sistema');

                    // Registrar login na auditoria
                    $this->load->model('Audit_model');
                    $log_data = [
                        'usuario' => $cliente->nomeCliente,
                        'tarefa' => 'Cliente ' . $cliente->nomeCliente . ' efetuou login',
                        'data' => date('Y-m-d'),
                        'hora' => date('H:i:s'),
                        'ip' => $_SERVER['REMOTE_ADDR']
                    ];

                    $this->Audit_model->add($log_data);

                    echo json_encode(['result' => true]);
                } else {
                    echo json_encode(['result' => false, 'message' => 'Os dados de acesso estão incorretos.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()]);
                }
            } else {
                echo json_encode(['result' => false, 'message' => 'Usuário não encontrado, verifique se suas credenciais estão corretas.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()]);
            }
        }
    }

    public function painel()
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        // Novo sistema de usuário do cliente
        if ($this->session->userdata('tipo_acesso') == 'usuario_cliente') {
            $this->load->model('usuarios_cliente_model');

            $usuario_id = $this->session->userdata('usuario_cliente_id');
            $usuario = $this->usuarios_cliente_model->getById($usuario_id);

            // Verifica se usuário ainda existe e está ativo
            if (!$usuario || !$usuario->ativo) {
                $this->session->set_flashdata('error', 'Usuário desativado ou não encontrado.');
                $this->sair_usuario();
                return;
            }

            // Carrega dados do painel
            $stats = $this->usuarios_cliente_model->countOsByStatus($usuario_id);
            $os = $this->usuarios_cliente_model->getOsByCnpjs($usuario_id, ['limit' => 10]);

            // Garante que stats tenha todas as chaves necessárias
            $statsPadrao = [
                'total' => 0,
                'Aberto' => 0,
                'Orçamento' => 0,
                'Negociação' => 0,
                'Aprovado' => 0,
                'Em Andamento' => 0,
                'Aguardando Peças' => 0,
                'Finalizado' => 0,
                'Faturado' => 0,
                'Cancelado' => 0,
            ];
            $stats = array_merge($statsPadrao, $stats ?? []);

            // Carrega permissões do usuário
            $permissoes = $this->usuarios_cliente_model->getAllPermissoes($usuario_id);
            $cliente_id = $this->session->userdata('cliente_id');

            // Carrega dados financeiros e obras conforme permissões
            $cobrancas = [];
            $boletos = [];
            $notasFiscais = [];
            $obras = [];

            // Se não tiver cliente_id, tenta obter pelos CNPJs vinculados
            if (!$cliente_id) {
                $cnpjs = $this->usuarios_cliente_model->getCnpjs($usuario_id);
                if (!empty($cnpjs)) {
                    $primeiroCnpj = preg_replace('/[^0-9]/', '', $cnpjs[0]->cnpj);
                    $this->load->model('clientes_model');
                    $cliente = $this->clientes_model->getByDocumento($primeiroCnpj);
                    if ($cliente) {
                        $cliente_id = $cliente->idClientes;
                        // Atualiza sessão
                        $this->session->set_userdata('cliente_id', $cliente_id);
                    }
                }
            }

            // Carrega cobranças se tiver permissão
            if ($this->usuarios_cliente_model->hasPermissao($usuario_id, 'visualizar_cobrancas') && $cliente_id) {
                $this->load->model('cobrancas_model');
                $cobrancas = $this->cobrancas_model->getByCliente($cliente_id, 5, 0) ?? [];
            }

            // Carrega boletos se tiver permissão
            if ($this->usuarios_cliente_model->hasPermissao($usuario_id, 'visualizar_boletos') && $cliente_id) {
                $this->load->model('cobrancas_model');
                $boletos = $this->cobrancas_model->getBoletosByCliente($cliente_id, 5, 0) ?? [];
            }

            // Carrega notas fiscais se tiver permissão
            if ($this->usuarios_cliente_model->hasPermissao($usuario_id, 'visualizar_notas_fiscais') && $cliente_id) {
                $this->load->model('nfse_emitida_model');
                $notasFiscais = $this->nfse_emitida_model->getByCliente($cliente_id, 5, 0) ?? [];
            }

            // Carrega obras se tiver permissão
            if ($this->usuarios_cliente_model->hasPermissao($usuario_id, 'visualizar_obras') && $cliente_id) {
                $this->load->model('obras_model');
                $obras = $this->obras_model->getByCliente($cliente_id, 5, 0) ?? [];
            }

            log_message('debug', 'Painel Usuário - OS: ' . count($os) . ', Cobranças: ' . count($cobrancas) . ', Boletos: ' . count($boletos) . ', Obras: ' . count($obras));

            $data['menuPainel'] = 'painel';
            $data['usuario'] = $usuario;
            $data['stats'] = $stats;
            $data['os'] = $os ?? [];
            $data['cnpjs'] = $this->session->userdata('usuario_cliente_cnpjs') ?? [];
            $data['permissoes'] = $permissoes;
            $data['cobrancas'] = $cobrancas;
            $data['boletos'] = $boletos;
            $data['notasFiscais'] = $notasFiscais;
            $data['obras'] = $obras;
            $data['output'] = 'conecte/painel_usuario';

            $this->load->view('conecte/template', $data);
        } else {
            // Sistema antigo por token
            $data['menuPainel'] = 'painel';
            $data['compras'] = $this->Conecte_model->getLastCompras($this->session->userdata('cliente_id'));
            $data['os'] = $this->Conecte_model->getLastOs($this->session->userdata('cliente_id'));
            $data['output'] = 'conecte/painel';
            $this->load->view('conecte/template', $data);
        }
    }

    public function conta()
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        $data['menuConta'] = 'conta';
        $data['result'] = $this->Conecte_model->getDados();

        $data['output'] = 'conecte/conta';
        $this->load->view('conecte/template', $data);
    }

    public function editarDados()
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        $data['menuConta'] = 'conta';

        $this->load->library('form_validation');
        $data['custom_error'] = '';

        if ($this->form_validation->run('clientes') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $senha = $this->input->post('senha');
            if ($senha != null) {
                $senha = password_hash($senha, PASSWORD_DEFAULT);
                $data = [
                    'nomeCliente' => $this->input->post('nomeCliente'),
                    'documento' => $this->input->post('documento'),
                    'telefone' => $this->input->post('telefone'),
                    'celular' => $this->input->post('celular'),
                    'email' => $this->input->post('email'),
                    'senha' => $senha,
                    'rua' => $this->input->post('rua'),
                    'numero' => $this->input->post('numero'),
                    'complemento' => $this->input->post('complemento'),
                    'bairro' => $this->input->post('bairro'),
                    'cidade' => $this->input->post('cidade'),
                    'estado' => $this->input->post('estado'),
                    'cep' => $this->input->post('cep'),
                    'contato' => $this->input->post('contato'),
                ];
            } else {
                $data = [
                    'nomeCliente' => $this->input->post('nomeCliente'),
                    'documento' => $this->input->post('documento'),
                    'telefone' => $this->input->post('telefone'),
                    'celular' => $this->input->post('celular'),
                    'email' => $this->input->post('email'),
                    'rua' => $this->input->post('rua'),
                    'numero' => $this->input->post('numero'),
                    'complemento' => $this->input->post('complemento'),
                    'bairro' => $this->input->post('bairro'),
                    'cidade' => $this->input->post('cidade'),
                    'estado' => $this->input->post('estado'),
                    'cep' => $this->input->post('cep'),
                    'contato' => $this->input->post('contato'),
                ];
            }

            if ($this->Conecte_model->edit('clientes', $data, 'idClientes', $this->input->post('idClientes')) == true) {
                $this->session->set_flashdata('success', 'Dados editados com sucesso!');
                redirect(base_url() . 'index.php/mine/conta');
            } else {
            }
        }

        $data['result'] = $this->Conecte_model->getDados();

        $data['output'] = 'conecte/editar_dados';
        $this->load->view('conecte/template', $data);
    }

    public function compras()
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        $data['menuVendas'] = 'vendas';
        $this->load->library('pagination');

        $config['base_url'] = base_url() . 'index.php/mine/compras/';
        $config['total_rows'] = $this->Conecte_model->count('vendas', $this->session->userdata('cliente_id'));
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $data['results'] = $this->Conecte_model->getCompras('vendas', '*', '', $config['per_page'], $this->uri->segment(3), '', '', $this->session->userdata('cliente_id'));

        $data['output'] = 'conecte/compras';
        $this->load->view('conecte/template', $data);
    }

    public function cobrancas()
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        $this->load->library('pagination');
        $this->load->config('payment_gateways');

        $data['menuCobrancas'] = 'cobrancas';

        $config['base_url'] = base_url() . 'index.php/mine/cobrancas/';
        $config['total_rows'] = $this->Conecte_model->count('cobrancas', $this->session->userdata('cliente_id'));
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $data['results'] = $this->Conecte_model->getCobrancas('cobrancas', '*', '', $config['per_page'], $this->uri->segment(3), '', '', $this->session->userdata('cliente_id'));
        $data['output'] = 'conecte/cobrancas';

        $this->load->view('conecte/template', $data);
    }

    public function atualizarcobranca($id = null)
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para atualizar cobrança.');
            redirect(base_url());
        }

        $this->load->model('cobrancas_model');
        $this->cobrancas_model->atualizarStatus($this->uri->segment(3));

        redirect(site_url('mine/cobrancas/'));
    }

    public function enviarcobranca()
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para atualizar cobrança.');
            redirect(base_url());
        }

        $this->load->model('cobrancas_model');
        $this->cobrancas_model->enviarEmail($this->uri->segment(3));
        $this->session->set_flashdata('success', 'Email adicionado na fila.');

        redirect(site_url('mine/cobrancas/'));
    }

    public function os()
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        $data['menuOs'] = 'os';
        $this->load->library('pagination');
        $this->load->model('clientes_model');

        // Verificar se é novo sistema de usuário cliente
        $cliente_id = $this->session->userdata('cliente_id');
        $usuario_cliente_id = $this->session->userdata('usuario_cliente_id');

        // Parâmetros de filtro
        $filtros = [
            'busca' => $this->input->get('busca'),
            'status' => $this->input->get('status'),
            'data_inicio' => $this->input->get('data_inicio'),
            'data_fim' => $this->input->get('data_fim'),
        ];

        // Guardar filtros na sessão para manter ao paginar
        if ($this->input->get()) {
            $this->session->set_userdata('os_filtros', $filtros);
        } elseif ($this->session->userdata('os_filtros') && !$this->input->get('limpar')) {
            $filtros = $this->session->userdata('os_filtros');
        }

        // Limpar filtros
        if ($this->input->get('limpar')) {
            $this->session->unset_userdata('os_filtros');
            $filtros = ['busca' => '', 'status' => '', 'data_inicio' => '', 'data_fim' => ''];
            redirect('mine/os');
        }

        // Buscar clientes vinculados ao usuário para o filtro
        $clientes_vinculados = [];
        if ($this->session->userdata('tipo_acesso') == 'usuario_cliente' && $usuario_cliente_id) {
            $this->load->model('usuarios_cliente_model');
            $clientes_vinculados = $this->usuarios_cliente_model->getOsByCnpjs($usuario_cliente_id, ['limit' => 1000]);
            // Extrair IDs únicos de clientes
            $clientes_ids = [];
            foreach ($clientes_vinculados as $os) {
                if (!in_array($os->clientes_id, $clientes_ids)) {
                    $clientes_ids[] = $os->clientes_id;
                }
            }
            if (!empty($clientes_ids)) {
                $this->db->where_in('idClientes', $clientes_ids);
                $data['clientes_filtro'] = $this->db->get('clientes')->result();
            } else {
                $data['clientes_filtro'] = [];
            }
        } else {
            // Sistema antigo - apenas um cliente
            if ($cliente_id) {
                $cliente = $this->clientes_model->getById($cliente_id);
                $data['clientes_filtro'] = $cliente ? [$cliente] : [];
            } else {
                $data['clientes_filtro'] = [];
            }
        }

        // Contar total com filtros
        $this->db->from('os');
        if ($this->session->userdata('tipo_acesso') == 'usuario_cliente' && $usuario_cliente_id) {
            // Buscar OS pelos CNPJs do usuário
            $os_list = $this->usuarios_cliente_model->getOsByCnpjs($usuario_cliente_id);
            $os_ids = array_map(function($o) { return $o->idOs; }, $os_list);
            if (!empty($os_ids)) {
                $this->db->where_in('idOs', $os_ids);
            } else {
                $this->db->where('idOs', 0); // Nenhuma OS
            }
        } else {
            $this->db->where('clientes_id', $cliente_id);
        }

        // Aplicar filtros na contagem
        if (!empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('idOs', $filtros['busca']);
            $this->db->or_like('descricaoProduto', $filtros['busca']);
            $this->db->or_like('status', $filtros['busca']);
            $this->db->group_end();
        }
        if (!empty($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }
        if (!empty($filtros['data_inicio'])) {
            $this->db->where('dataInicial >=', $filtros['data_inicio']);
        }
        if (!empty($filtros['data_fim'])) {
            $this->db->where('dataInicial <=', $filtros['data_fim']);
        }

        $config['total_rows'] = $this->db->count_all_results();

        // Configuração da paginação
        $config['base_url'] = base_url() . 'index.php/mine/os/';
        $config['per_page'] = 15;
        $config['reuse_query_string'] = true;
        $config['next_link'] = 'Próxima <i class="bx bx-chevron-right"></i>';
        $config['prev_link'] = '<i class="bx bx-chevron-left"></i> Anterior';
        $config['first_link'] = '<i class="bx bx-chevrons-left"></i> Primeira';
        $config['last_link'] = 'Última <i class="bx bx-chevrons-right"></i>';
        $config['full_tag_open'] = '<div class="pagination-wrapper"><ul class="pagination modern">';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="nav">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="nav">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="nav">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="nav">';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        // Buscar resultados com filtros
        $this->db->select('os.*, clientes.nomeCliente, clientes.documento, usuarios.nome as responsavel');
        $this->db->from('os');
        $this->db->join('clientes', 'os.clientes_id = clientes.idClientes', 'left');
        $this->db->join('usuarios', 'os.usuarios_id = usuarios.idUsuarios', 'left');

        if ($this->session->userdata('tipo_acesso') == 'usuario_cliente' && $usuario_cliente_id) {
            if (!empty($os_ids)) {
                $this->db->where_in('os.idOs', $os_ids);
            } else {
                $this->db->where('os.idOs', 0);
            }
        } else {
            $this->db->where('os.clientes_id', $cliente_id);
        }

        // Aplicar filtros na busca
        if (!empty($filtros['busca'])) {
            $this->db->group_start();
            $this->db->like('os.idOs', $filtros['busca']);
            $this->db->or_like('os.descricaoProduto', $filtros['busca']);
            $this->db->or_like('os.status', $filtros['busca']);
            $this->db->or_like('clientes.nomeCliente', $filtros['busca']);
            $this->db->group_end();
        }
        if (!empty($filtros['status'])) {
            $this->db->where('os.status', $filtros['status']);
        }
        if (!empty($filtros['data_inicio'])) {
            $this->db->where('os.dataInicial >=', $filtros['data_inicio']);
        }
        if (!empty($filtros['data_fim'])) {
            $this->db->where('os.dataInicial <=', $filtros['data_fim']);
        }

        $this->db->order_by('os.idOs', 'desc');
        $this->db->limit($config['per_page'], $this->uri->segment(3) ? $this->uri->segment(3) : 0);
        $data['results'] = $this->db->get()->result();

        // Dados para a view
        $data['filtros'] = $filtros;
        $data['total_os'] = $config['total_rows'];

        // Status disponíveis para filtro
        $data['status_list'] = [
            'Aberto' => 'Aberto',
            'Orçamento' => 'Orçamento',
            'Negociação' => 'Negociação',
            'Aprovado' => 'Aprovado',
            'Em Andamento' => 'Em Andamento',
            'Aguardando Peças' => 'Aguardando Peças',
            'Finalizado' => 'Finalizado',
            'Faturado' => 'Faturado',
            'Cancelado' => 'Cancelado'
        ];

        $data['output'] = 'conecte/os';
        $this->load->view('conecte/template', $data);
    }

    public function visualizarOs($id = null)
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        $data['menuOs'] = 'os';
        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->load->model('os_model');
        $this->CI = &get_instance();
        $this->CI->load->database();

        $data['pix_key'] = $this->CI->db->get_where('configuracoes', ['config' => 'pix_key'])->row_object()->valor;
        $data['result'] = $this->os_model->getById($this->uri->segment(3));
        $data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));

        // Carregar documentos fiscais vinculados à OS (cobranças, NFS-e, impostos)
        $data['documentos_fiscais'] = $this->os_model->getDocumentosFiscais($this->uri->segment(3));

        // Carregar cobranças/boletos específicos da OS
        $cobrancas = $this->os_model->getCobrancas($this->uri->segment(3));
        $data['cobrancas_os'] = is_array($cobrancas) ? $cobrancas : [];

        // Carregar notas fiscais específicas da OS
        $this->load->model('nfse_emitida_model');
        $notasEmitidas = $this->nfse_emitida_model->getAllByOsId($this->uri->segment(3)) ?? [];

        // Também buscar na tabela de notas importadas
        $notasImportadas = [];
        try {
            $this->db->select('*');
            $this->db->from('certificado_nfe_importada');
            $this->db->where('os_id', $this->uri->segment(3));
            $this->db->order_by('id', 'DESC');
            $query = $this->db->get();
            if ($query) {
                $notasImportadas = $query->result();
            }
        } catch (Exception $e) {
            // Tabela pode não existir
            $notasImportadas = [];
        }

        // Mesclar notas emitidas e importadas
        $data['notas_fiscais_os'] = array_merge($notasEmitidas, $notasImportadas);

        log_message('debug', 'visualizarOs OS=' . $this->uri->segment(3) . ' Cobrancas=' . count($data['cobrancas_os']) . ' Notas=' . count($data['notas_fiscais_os']));

        $data['emitente'] = $this->mapos_model->getEmitente();
        $data['qrCode'] = $this->os_model->getQrCode(
            $id,
            $data['pix_key'],
            $data['emitente']
        );
        $data['chaveFormatada'] = $this->formatarChave($data['pix_key']);

        if ($data['result']->idClientes != $this->session->userdata('cliente_id')) {
            $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
            redirect('mine/painel');
        }

        // Verificar permissão para aprovar OS (novo sistema de usuários)
        $data['pode_aprovar'] = false;
        if ($this->session->userdata('tipo_acesso') == 'usuario_cliente') {
            $this->load->model('usuarios_cliente_model');
            $data['pode_aprovar'] = $this->usuarios_cliente_model->hasPermissao(
                $this->session->userdata('usuario_cliente_id'),
                'aprovar_os'
            );
        } else {
            // Sistema antigo - cliente tem permissão por padrão
            $data['pode_aprovar'] = true;
        }

        $data['output'] = 'conecte/visualizar_os';
        $this->load->view('conecte/template', $data);
    }

    public function aprovarOs($id = null)
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        if (! $id || ! is_numeric($id)) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('mine/os');
        }

        $this->load->model('os_model');
        $os = $this->os_model->getById($id);

        if (! $os) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('mine/os');
        }

        // Verificar se a OS pertence ao cliente logado
        if ($os->idClientes != $this->session->userdata('cliente_id')) {
            $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
            redirect('mine/painel');
        }

        // Verificar permissão para aprovar
        if ($this->session->userdata('tipo_acesso') == 'usuario_cliente') {
            $this->load->model('usuarios_cliente_model');
            if (! $this->usuarios_cliente_model->hasPermissao(
                $this->session->userdata('usuario_cliente_id'),
                'aprovar_os'
            )) {
                $this->session->set_flashdata('error', 'Você não tem permissão para aprovar OS.');
                redirect('mine/visualizarOs/' . $id);
            }
        }

        // Verificar se o status atual permite aprovação
        $statusPermitidos = ['Orçamento', 'Aberto', 'Negociação'];
        if (! in_array($os->status, $statusPermitidos)) {
            $this->session->set_flashdata('error', 'Esta OS não pode ser aprovada no status atual: ' . $os->status);
            redirect('mine/visualizarOs/' . $id);
        }

        // Atualizar status para Aprovado
        $data = ['status' => 'Aprovado'];
        if ($this->os_model->edit('os', $data, 'idOs', $id)) {
            $this->session->set_flashdata('success', 'Ordem de Serviço aprovada com sucesso!');
            log_info('Cliente aprovou a OS. ID: ' . $id);
        } else {
            $this->session->set_flashdata('error', 'Erro ao aprovar a Ordem de Serviço.');
        }

        redirect('mine/visualizarOs/' . $id);
    }

    public function validarCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }
        $soma1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma1 += $cpf[$i] * (10 - $i);
        }
        $resto1 = $soma1 % 11;
        $dv1 = ($resto1 < 2) ? 0 : 11 - $resto1;
        if ($dv1 != $cpf[9]) {
            return false;
        }
        $soma2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma2 += $cpf[$i] * (11 - $i);
        }
        $resto2 = $soma2 % 11;
        $dv2 = ($resto2 < 2) ? 0 : 11 - $resto2;

        return $dv2 == $cpf[10];
    }

    public function validarCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1+$/', $cnpj)) {
            return false;
        }
        $soma1 = 0;
        for ($i = 0, $pos = 5; $i < 12; $i++, $pos--) {
            $pos = ($pos < 2) ? 9 : $pos;
            $soma1 += $cnpj[$i] * $pos;
        }
        $dv1 = ($soma1 % 11 < 2) ? 0 : 11 - ($soma1 % 11);
        if ($dv1 != $cnpj[12]) {
            return false;
        }
        $soma2 = 0;
        for ($i = 0, $pos = 6; $i < 13; $i++, $pos--) {
            $pos = ($pos < 2) ? 9 : $pos;
            $soma2 += $cnpj[$i] * $pos;
        }
        $dv2 = ($soma2 % 11 < 2) ? 0 : 11 - ($soma2 % 11);

        return $dv2 == $cnpj[13];
    }

    public function formatarChave($chave)
    {
        if ($this->validarCPF($chave)) {
            return substr($chave, 0, 3) . '.' . substr($chave, 3, 3) . '.' . substr($chave, 6, 3) . '-' . substr($chave, 9);
        } elseif ($this->validarCNPJ($chave)) {
            return substr($chave, 0, 2) . '.' . substr($chave, 2, 3) . '.' . substr($chave, 5, 3) . '/' . substr($chave, 8, 4) . '-' . substr($chave, 12);
        } elseif (strlen($chave) === 11) {
            return '(' . substr($chave, 0, 2) . ') ' . substr($chave, 2, 5) . '-' . substr($chave, 7);
        }

        return $chave;
    }

    public function gerarPagamentoGerencianetBoleto()
    {
        print_r(json_encode(['code' => 4001, 'error' => 'Erro interno', 'errorDescription' => 'Cobrança não pode ser gerada pelo lado do cliente']));

    }

    public function gerarPagamentoGerencianetLink()
    {
        print_r(json_encode(['code' => 4001, 'error' => 'Erro interno', 'errorDescription' => 'Cobrança não pode ser gerada pelo lado do cliente']));

    }

    public function imprimirOs($id = null)
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('mine');
        }

        $data['menuOs'] = 'os';
        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->load->model('os_model');
        $data['result'] = $this->os_model->getById($this->uri->segment(3));
        $data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $data['emitente'] = $this->mapos_model->getEmitente();
        $data['pix_key'] = $this->db->get_where('configuracoes', ['config' => 'pix_key'])->row_object()->valor;
        $data['qrCode'] = $this->os_model->getQrCode(
            $id,
            $data['pix_key'],
            $data['emitente']
        );
        $data['chaveFormatada'] = $this->formatarChave($data['pix_key']);      

        if ($data['result']->idClientes != $this->session->userdata('cliente_id')) {
            $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
            redirect('mine/painel');
        }

        $this->load->view('conecte/imprimirOs', $data);
    }

    public function visualizarCompra($id = null)
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }

        $data['menuVendas'] = 'vendas';
        $data['custom_error'] = '';
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->load->model('mapos_model');
        $this->load->model('os_model');
        $this->load->model('vendas_model');        

        $data['result'] = $this->vendas_model->getById($this->uri->segment(3));
        $data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $data['emitente'] = $this->mapos_model->getEmitente();
        $data['pix_key'] = $this->CI->db->get_where('configuracoes', ['config' => 'pix_key'])->row_object()->valor;
        $data['qrCode'] = $this->vendas_model->getQrCode(
            $id,
            $data['pix_key'],
            $data['emitente']
        );
        $data['chaveFormatada'] = $this->formatarChave($data['pix_key']);
        
        if ($data['result']->clientes_id != $this->session->userdata('cliente_id')) {
            $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
            redirect('mine/painel');
        }

        $data['output'] = 'conecte/visualizar_compra';

        $this->load->view('conecte/template', $data);
    }

    public function imprimirCompra($id = null)
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('mine');
        }

        $data['menuVendas'] = 'vendas';
        $data['custom_error'] = '';

        $this->load->model('mapos_model');
        $this->load->model('vendas_model');
        $this->load->model('os_model');

        $data['result'] = $this->vendas_model->getById($id);
        $data['produtos'] = $this->vendas_model->getProdutos($id);
        $data['emitente'] = $this->mapos_model->getEmitente();

        $this->CI = &get_instance();
        $this->CI->load->database();
        $data['pix_key'] = $this->CI->db->get_where('configuracoes', ['config' => 'pix_key'])->row_object()->valor;
        $data['qrCode'] = $this->vendas_model->getQrCode($id, $data['pix_key'], $data['emitente']);
        $data['chaveFormatada'] = $this->formatarChave($data['pix_key']);

        if ($data['result']->clientes_id != $this->session->userdata('cliente_id')) {
            $this->session->set_flashdata('error', 'Esta venda não pertence ao cliente logado.');
            redirect('mine/painel');
        }

        $this->load->view('conecte/imprimirVenda', $data);
    }

    public function minha_ordem_de_servico($y = null, $when = null)
    {
        if (($y != null) && (is_numeric($y))) {
            // Do not forget this number -> 44023
            // function sending => y = (7653 * ID) + 44023
            // function recieving => x = (y - 44023) / 7653

            // Example ID = 2 | y = 59329

            $y = intval($y);
            $id = ($y - 44023) / 7653;

            $data['menuOs'] = 'os';
            $this->data['custom_error'] = '';
            $this->load->model('mapos_model');
            $this->load->model('os_model');
            $data['result'] = $this->os_model->getById($id);
            if ($data['result'] == null) {
                // Resposta em caso de não encontrar a ordem de serviço
                //$this->load->view('conecte/login');
            } else {
                $data['produtos'] = $this->os_model->getProdutos($id);
                $data['servicos'] = $this->os_model->getServicos($id);
                $data['emitente'] = $this->mapos_model->getEmitente();

                $this->load->view('conecte/minha_os', $data);
            }
        } else {
            // Resposta em caso de não encontrar a ordem de serviço
            //$this->load->view('conecte/');
        }
    }

    public function adicionarOs()
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }
        $this->load->library('form_validation');

        $this->form_validation->set_rules('descricaoProduto', 'Descrição', 'required');
        $this->form_validation->set_rules('defeito', 'Defeito');
        $this->form_validation->set_rules('observacoes', 'Observações');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {
            $id = null;
            $usuario = $this->db->query('SELECT usuarios_id, count(*) as down FROM os GROUP BY usuarios_id ORDER BY down LIMIT 1')->row();
            if ($usuario == null) {
                $this->db->where('situacao', 1);
                $this->db->limit(1);
                $usuario = $this->db->get('usuarios')->row();

                if ($usuario->idUsuarios == null) {
                    $this->session->set_flashdata('error', 'Ocorreu um erro ao cadastrar a ordem de serviço, por favor contate o administrador do sistema.');
                    redirect('mine/os');
                } else {
                    $id = $usuario->idUsuarios;
                }
            } else {
                $id = $usuario->usuarios_id;
            }

            $data = [
                'dataInicial' => date('Y-m-d'),
                'clientes_id' => $this->session->userdata('cliente_id'),
                'usuarios_id' => $id,
                'dataFinal' => date('Y-m-d'),
                'descricaoProduto' => $this->security->xss_clean($this->input->post('descricaoProduto')),
                'defeito' => $this->security->xss_clean($this->input->post('defeito')),
                'status' => 'Aberto',
                'observacoes' => $this->security->xss_clean(set_value('observacoes')),
                'faturado' => 0,
            ];

            if (is_numeric($id = $this->Conecte_model->add('os', $data, true))) {
                $this->load->model('mapos_model');
                $this->load->model('usuarios_model');

                $idOs = $id;
                $os = $this->Conecte_model->getById($id);

                $remetentes = [];
                $usuarios = $this->usuarios_model->getAll();

                foreach ($usuarios as $usuario) {
                    array_push($remetentes, $usuario->email);
                }
                array_push($remetentes, $os->email);

                $this->enviarOsPorEmail($idOs, $remetentes, 'Nova Ordem de Serviço #' . $idOs . ' - Criada pelo Cliente');
                $this->session->set_flashdata('success', 'OS adicionada com sucesso!');
                redirect('mine/detalhesOs/' . $id);
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $data['output'] = 'conecte/adicionarOs';
        $this->load->view('conecte/template', $data);
    }

    public function detalhesOs($id = null)
    {
        if (is_numeric($id) && $id != null) {
            $this->load->model('mapos_model');
            $this->load->model('os_model');

            $this->data['result'] = $this->os_model->getById($id);
            $this->data['produtos'] = $this->os_model->getProdutos($id);
            $this->data['servicos'] = $this->os_model->getServicos($id);
            $this->data['anexos'] = $this->os_model->getAnexos($id);

            if ($this->data['result']->idClientes != $this->session->userdata('cliente_id')) {
                $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
                redirect('mine/painel');
            }

            $this->data['output'] = 'conecte/detalhes_os';
            $this->load->view('conecte/template', $this->data);
        } else {
            echo 'teste';
        }
    }

    public function cadastrar()
    {
        $this->load->model('clientes_model', '', true);
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $id = 0;

        if ($this->form_validation->run('clientes') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } elseif (strtolower($this->input->post('captcha')) != strtolower($this->session->userdata('captchaWord'))) {
            $this->session->set_flashdata('error', 'Os caracteres da imagem não foram preenchidos corretamente!');
        } else {
            $data = [
                'nomeCliente' => set_value('nomeCliente'),
                'documento' => set_value('documento'),
                'telefone' => set_value('telefone'),
                'celular' => $this->input->post('celular'),
                'email' => set_value('email'),
                'senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                'rua' => set_value('rua'),
                'complemento' => set_value('complemento'),
                'numero' => set_value('numero'),
                'bairro' => set_value('bairro'),
                'cidade' => set_value('cidade'),
                'estado' => set_value('estado'),
                'cep' => set_value('cep'),
                'dataCadastro' => date('Y-m-d'),
                'contato' => $this->input->post('contato'),
            ];

            $id = $this->clientes_model->add('clientes', $data);

            if ($id > 0) {
                $this->enviarEmailBoasVindas($id);
                $this->enviarEmailTecnicoNotificaClienteNovo($id);
                $this->session->set_flashdata('success', 'Cadastro realizado com sucesso! <br> Um e-mail de boas vindas será enviado para ' . $data['email']);
                redirect(base_url() . 'index.php/mine');
            } else {
                $this->session->set_flashdata('error', 'Falha ao realizar cadastro!');
            }
        }

        $this->load->view('conecte/cadastrar', $this->data);
    }

    public function downloadanexo($id = null)
    {
        if (! session_id() || ! $this->session->userdata('conectado')) {
            redirect('mine');
        }
        if ($id != null && is_numeric($id)) {
            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos', 1)->row();

            $this->load->library('zip');
            $path = $file->path;
            $this->zip->read_file($path . '/' . $file->anexo);
            $this->zip->download('file' . date('d-m-Y-H.i.s') . '.zip');
        }
    }

    private function check_credentials($email)
    {
        $this->db->where('email', $email);
        $this->db->limit(1);

        return $this->db->get('clientes')->row();
    }

    private function check_token($token)
    {
        $this->db->where('token', $token);
        $this->db->limit(1);

        return $this->db->get('resets_de_senha')->row();
    }

    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $dateStart = new \DateTime($date);
        $dateNow = new \DateTime(date($format));

        $dateDiff = $dateStart->diff($dateNow);

        if ($dateDiff->days >= 1) {
            return true;
        } else {
            return false;
        }
    }

    private function enviarRecuperarSenha($idClientes, $clienteEmail, $assunto, $token)
    {
        $dados = [];
        $this->load->model('mapos_model');
        $this->load->model('clientes_model', '', true);

        $dados['emitente'] = $this->mapos_model->getEmitente();
        $dados['cliente'] = $this->clientes_model->getById($idClientes);
        $dados['resets_de_senha'] = json_decode($token);

        $emitente = $dados['emitente'];
        $remetente = $clienteEmail;

        $html = $this->load->view('conecte/emails/clientenovasenha', $dados, true);

        $this->load->model('email_model');

        if ($emitente == null) {
            $this->session->set_flashdata(['error' => 'Cadastrar Emitente.\n\n Por favor contate o administrador do sistema.']);

            return redirect(base_url() . 'index.php/mine/resetarSenha');
        }

        $headers = [
            'From' => "\"$emitente->nome\" <$emitente->email>",
            'Subject' => $assunto,
            'Return-Path' => '',
        ];
        $email = [
            'to' => $remetente,
            'message' => $html,
            'status' => 'pending',
            'date' => date('Y-m-d H:i:s'),
            'headers' => serialize($headers),
        ];

        return $this->email_model->add('email_queue', $email);
    }

    private function enviarOsPorEmail($idOs, $remetentes, $assunto)
    {
        $dados = [];

        $this->load->model('mapos_model');
        $this->load->model('os_model');
        $dados['result'] = $this->os_model->getById($idOs);
        if (! isset($dados['result']->email)) {
            return false;
        }

        $dados['produtos'] = $this->os_model->getProdutos($idOs);
        $dados['servicos'] = $this->os_model->getServicos($idOs);
        $dados['emitente'] = $this->mapos_model->getEmitente();

        $emitente = $dados['emitente'];
        if (! isset($emitente)) {
            return false;
        }

        $html = $this->load->view('os/emails/os', $dados, true);

        $this->load->model('email_model');

        $remetentes = array_unique($remetentes);
        foreach ($remetentes as $remetente) {
            $headers = [
                'From' => $emitente->email,
                'Subject' => $assunto,
                'Return-Path' => '',
            ];
            $email = [
                'to' => $remetente,
                'message' => $html,
                'status' => 'pending',
                'date' => date('Y-m-d H:i:s'),
                'headers' => serialize($headers),
            ];
            $this->email_model->add('email_queue', $email);
        }

        return true;
    }

    private function enviarEmailBoasVindas($id)
    {
        $dados = [];
        $this->load->model('mapos_model');
        $this->load->model('clientes_model', '', true);

        $dados['emitente'] = $this->mapos_model->getEmitente();
        $dados['cliente'] = $this->clientes_model->getById($id);

        $emitente = $dados['emitente'];
        $remetente = $dados['cliente'];
        $assunto = 'Bem-vindo!';

        $html = $this->load->view('os/emails/clientenovo', $dados, true);

        $this->load->model('email_model');

        $headers = [
            'From' => "\"$emitente->nome\" <$emitente->email>",
            'Subject' => $assunto,
            'Return-Path' => '',
        ];
        $email = [
            'to' => $remetente->email,
            'message' => $html,
            'status' => 'pending',
            'date' => date('Y-m-d H:i:s'),
            'headers' => serialize($headers),
        ];

        return $this->email_model->add('email_queue', $email);
    }

    private function enviarEmailTecnicoNotificaClienteNovo($id)
    {
        $dados = [];
        $this->load->model('mapos_model');
        $this->load->model('clientes_model', '', true);
        $this->load->model('usuarios_model');

        $dados['emitente'] = $this->mapos_model->getEmitente();
        $dados['cliente'] = $this->clientes_model->getById($id);

        $emitente = $dados['emitente'];
        $assunto = 'Novo Cliente Cadastrado no Sistema';

        $usuarios = [];
        $usuarios = $this->usuarios_model->getAll();

        foreach ($usuarios as $usuario) {
            $dados['usuario'] = $usuario;
            $html = $this->load->view('os/emails/clientenovonotifica', $dados, true);
            $headers = [
                'From' => "\"$emitente->nome\" <$emitente->email>",
                'Subject' => $assunto,
                'Return-Path' => '',
            ];
            $email = [
                'to' => $usuario->email,
                'message' => $html,
                'status' => 'pending',
                'date' => date('Y-m-d H:i:s'),
                'headers' => serialize($headers),
            ];
            $this->email_model->add('email_queue', $email);
        }
    }

    public function captcha()
    {
        header('Content-type: image/jpeg');

        $arrFont = ['font-ZXX_Noise.otf', 'font-karabine.ttf', 'font-capture.ttf', 'font-captcha.ttf'];
        shuffle($arrFont);

        $codigoCaptcha = substr(md5(time()), 0, 7);
        $img = imagecreatefromjpeg('./assets/img/captcha_bg.jpg');
        $corCaptcha = imagecolorallocate($img, 255, 0, 0);
        $font = './assets/font-awesome/' . $arrFont[0];

        imagettftext($img, 23, 0, 5, rand(30, 35), $corCaptcha, $font, $codigoCaptcha);
        imagepng($img);
        imagedestroy($img);

        $this->session->set_userdata('captchaWord', $codigoCaptcha);
    }

    /**
     * Enviar email de recuperação de senha do usuário do portal
     */
    private function enviarEmailRecuperacao($usuario, $token)
    {
        $this->load->model('mapos_model');
        $emitente = $this->mapos_model->getEmitente();

        if (!$emitente) {
            return false;
        }

        $link = site_url('mine/resetar_senha/' . $token);

        $assunto = 'Recuperação de Senha - Portal do Cliente';
        $mensagem = "
        <html>
        <body>
            <h2>Olá, {$usuario->nome}!</h2>
            <p>Recebemos uma solicitação para redefinir sua senha no Portal do Cliente.</p>
            <p>Para criar uma nova senha, clique no link abaixo:</p>
            <p><a href='{$link}' style='padding: 10px 20px; background: #2d335b; color: #fff; text-decoration: none; border-radius: 4px;'>Redefinir Senha</a></p>
            <p>Ou copie e cole este link no navegador:</p>
            <p>{$link}</p>
            <p><strong>Este link expira em 24 horas.</strong></p>
            <p>Se você não solicitou esta recuperação, ignore este email.</p>
            <hr>
            <p><strong>{$emitente->nome}</strong></p>
        </body>
        </html>
        ";

        $this->load->model('email_model');

        $headers = [
            'From' => "\"{$emitente->nome}\" <{$emitente->email}>",
            'Subject' => $assunto,
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=UTF-8',
        ];

        $email = [
            'to' => $usuario->email,
            'message' => $mensagem,
            'status' => 'pending',
            'date' => date('Y-m-d H:i:s'),
            'headers' => serialize($headers),
        ];

        return $this->email_model->add('email_queue', $email);
    }

    /**
     * Visualizar relatório de atendimento/execução da OS (Cliente)
     */
    public function relatorioAtendimento($os_id = null)
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('mine');
        }

        if (!$os_id) {
            $this->session->set_flashdata('error', 'OS não informada.');
            redirect('mine/painel');
        }

        $this->load->model('os_model');
        $this->load->model('tec_os_model');
        $this->load->model('mapos_model');
        $this->load->model('clientes_model');
        $this->load->model('checkin_model');
        $this->load->model('fotosatendimento_model');
        $this->load->model('assinaturas_model');

        // Buscar OS
        $os = $this->os_model->getById($os_id);
        if (!$os) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('mine/painel');
        }

        // Verificar se a OS pertence ao cliente logado
        $cliente_id = $this->session->userdata('cliente_id');
        if ($os->clientes_id != $cliente_id) {
            $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
            redirect('mine/painel');
        }

        // Verificar se OS está finalizada (só mostrar relatório para OS finalizadas)
        if ($os->status != 'Finalizado' && $os->status != 'Finalizada') {
            $this->session->set_flashdata('warning', 'O relatório de atendimento estará disponível após a finalização da OS.');
            redirect('mine/visualizarOs/' . $os_id);
        }

        // Preparar dados para o relatório
        $data['os'] = $os;
        $data['cliente'] = $this->clientes_model->getById($os->clientes_id);
        $data['produtos'] = $this->os_model->getProdutos($os_id);
        $data['servicos'] = $this->os_model->getServicos($os_id);
        $data['emitente'] = $this->mapos_model->getEmitente();

        // Buscar execuções do técnico
        $data['execucoes'] = $this->tec_os_model->getExecucoesByOs($os_id);

        // Buscar checkins
        $data['checkins'] = $this->checkin_model->getAllByOs($os_id);

        // Buscar fotos do atendimento
        $fotos = $this->fotosatendimento_model->getByOs($os_id);
        $data['fotosPorEtapa'] = [
            'entrada' => [],
            'durante' => [],
            'saida' => []
        ];
        foreach ($fotos as $foto) {
            $data['fotosPorEtapa'][$foto->etapa][] = $foto;
        }

        // Buscar assinaturas
        $assinaturas = $this->assinaturas_model->getByOs($os_id);
        $data['assinaturasPorTipo'] = [];
        if (!empty($assinaturas)) {
            foreach ($assinaturas as $assinatura) {
                $data['assinaturasPorTipo'][$assinatura->tipo] = $assinatura;
            }
        }

        // Fotos do portal do técnico
        $data['fotosTecnico'] = $this->tec_os_model->getFotosByOs($os_id);

        $data['menuOs'] = 'os';
        $data['output'] = 'conecte/relatorio_atendimento';
        $this->load->view('conecte/template', $data);
    }

    /**
     * Listar boletos do cliente
     */
    public function boletos()
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('mine');
        }

        $this->load->helper('cliente_permissions');
        clienteCheckPermission('visualizar_boletos');

        $this->load->library('pagination');
        $this->load->model('cobrancas_model');

        $data['menuCobrancas'] = 'boletos';

        $cliente_id = $this->session->userdata('cliente_id');

        $config['base_url'] = base_url() . 'index.php/mine/boletos/';
        $config['total_rows'] = $this->cobrancas_model->countByCliente($cliente_id);
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';

        $this->pagination->initialize($config);

        $data['results'] = $this->cobrancas_model->getBoletosByCliente($cliente_id, $config['per_page'], $this->uri->segment(3));
        $data['output'] = 'conecte/boletos';

        $this->load->view('conecte/template', $data);
    }

    /**
     * Listar notas fiscais do cliente
     */
    public function notasfiscais()
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('mine');
        }

        $this->load->helper('cliente_permissions');
        clienteCheckPermission('visualizar_notas_fiscais');

        $this->load->library('pagination');
        $this->load->model('nfse_emitida_model');

        $data['menuNotas'] = 'notas';

        $cliente_id = $this->session->userdata('cliente_id');

        $config['base_url'] = base_url() . 'index.php/mine/notasfiscais/';
        $config['total_rows'] = $this->nfse_emitida_model->countByCliente($cliente_id);
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';

        $this->pagination->initialize($config);

        $data['results'] = $this->nfse_emitida_model->getByCliente($cliente_id, $config['per_page'], $this->uri->segment(3));
        $data['output'] = 'conecte/notasfiscais';

        $this->load->view('conecte/template', $data);
    }

    /**
     * Listar obras do cliente
     */
    public function obras()
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('mine');
        }

        $this->load->helper('cliente_permissions');
        clienteCheckPermission('visualizar_obras');

        $this->load->library('pagination');
        $this->load->model('obras_model');

        $data['menuObras'] = 'obras';

        $cliente_id = $this->session->userdata('cliente_id');

        $config['base_url'] = base_url() . 'index.php/mine/obras/';
        $config['total_rows'] = $this->obras_model->countByCliente($cliente_id);
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';

        $this->pagination->initialize($config);

        $data['results'] = $this->obras_model->getByCliente($cliente_id, $config['per_page'], $this->uri->segment(3));
        $data['output'] = 'conecte/obras';

        $this->load->view('conecte/template', $data);
    }

    /**
     * Visualizar obra do cliente
     */
    public function visualizarObra($id = null)
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('mine');
        }

        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('mine/obras');
        }

        $this->load->helper('cliente_permissions');
        clienteCheckPermission('visualizar_obras');

        $this->load->model('obras_model');
        $this->load->model('mapos_model');

        $obra = $this->obras_model->getById($id);

        if (!$obra) {
            $this->session->set_flashdata('error', 'Obra não encontrada.');
            redirect('mine/obras');
        }

        // Verificar se a obra pertence ao cliente logado
        $cliente_id = $this->session->userdata('cliente_id');
        if ($obra->cliente_id != $cliente_id) {
            $this->session->set_flashdata('error', 'Esta obra não pertence ao cliente logado.');
            redirect('mine/obras');
        }

        // Carregar OS vinculadas
        $os_vinculadas = $this->obras_model->getOsVinculadas($id);

        // Carregar etapas
        $etapas = $this->obras_model->getEtapas($id);

        // Carregar diário
        $diario = $this->obras_model->getDiario($id);

        // Carregar equipe
        $equipe = $this->obras_model->getEquipe($id);

        $data['menuObras'] = 'obras';
        $data['obra'] = $obra;
        $data['os_vinculadas'] = $os_vinculadas;
        $data['etapas'] = $etapas;
        $data['diario'] = $diario;
        $data['equipe'] = $equipe;
        $data['emitente'] = $this->mapos_model->getEmitente();
        $data['output'] = 'conecte/visualizar_obra';

        $this->load->view('conecte/template', $data);
    }

}

/* End of file conecte.php */
/* Location: ./application/controllers/conecte.php */
