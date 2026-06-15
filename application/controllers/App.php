<?php
/**
 * App Controller — serve a aplicacao React (CoreUI).
 *
 * Todas as rotas /app/* sao roteadas para este controller, que carrega
 * a view `react_app.php`. O React Router (client-side) assume o controle
 * da sub-rota apos o React montar.
 *
 * Permissao exigida: usuario logado (admin ou tecnico). Caso contrario,
 * redireciona para /login ou /tecnicos/login respectivamente.
 */
class App extends CI_Controller
{
    /**
     * Carrega o bundle React. Aceita qualquer sub-rota como parametro
     * (ex: /app/dashboard, /app/os/visualizar/123) e ignora — o React
     * Router faz o roteamento client-side.
     */
    public function index()
    {
        // Seguranca: exige sessao ativa (admin ou tecnico)
        $sessao_admin   = $this->session->userdata('logado');
        $sessao_tecnico = $this->session->userdata('tec_id')
            && ($this->session->userdata('tec_logado') || $this->session->userdata('logged_in'));

        if (! $sessao_admin && ! $sessao_tecnico) {
            // Tecnico tem login proprio
            if ($this->session->userdata('tec_id')) {
                redirect('tecnicos/login');
            }
            redirect('login');
        }

        // Carrega configuracoes do sistema (tema, nome, etc) direto da tabela
        // 'configuracoes' (mesmo padrao de MY_Controller::load_configuration).
        // Nao usa Mapos_model::get_config() porque esse metodo nao existe —
        // o model mantem apenas metodos de OS/cliente; configs sao responsabilidade
        // do core controller. Cache ja eh feito por MY_Controller quando herdar dela.
        $this->load->database();
        $configuration = [];
        $configuracoes = $this->db->get('configuracoes')->result();
        foreach ($configuracoes as $c) {
            $configuration[$c->config] = $c->valor;
        }
        // Defaults minimos caso a tabela esteja vazia
        $configuration += [
            'app_theme' => 'white',
            'app_name'  => 'Mapos',
        ];

        // Carrega permissoes do usuario logado (padrao Mapos: array associativo)
        $permissoes_raw = $this->session->userdata('permissoes');
        $permissoes = [];
        if (is_array($permissoes_raw)) {
            // Permissoes vem como [key => bool] ou [key => 1] — extrai as chaves verdadeiras
            foreach ($permissoes_raw as $key => $val) {
                if ($val === true || $val === 1 || $val === '1') {
                    $permissoes[] = $key;
                }
            }
        } elseif (is_string($permissoes_raw)) {
            $permissoes = [$permissoes_raw];
        }

        // Disponibiliza para a view
        $this->data['configuration'] = $configuration;
        $this->data['app_config'] = [
            'baseUrl'     => base_url() . 'index.php/',
            'userName'    => $this->session->userdata('nome') ?: 'Usuario',
            'userEmail'   => $this->session->userdata('email') ?: '',
            'userAvatar'  => null,
            'permissions' => $permissoes,
            'theme'       => $configuration['app_theme'] ?? 'white',
        ];

        // Renderiza a view React (faz echo direto por ser SPA root)
        $this->load->view('react_app', $this->data);
    }

    /**
     * Alias para o redirect padrao "ir para o app".
     * /app  -> /app/dashboard
     */
    public function home()
    {
        redirect('app/dashboard');
    }
}
