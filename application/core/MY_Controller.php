<?php

class MY_Controller extends CI_Controller
{
    public $data = [
        'configuration' => [
            'per_page' => 10,
            'next_link' => 'Próxima',
            'prev_link' => 'Anterior',
            'full_tag_open' => '<nav><ul class="pagination justify-content-center">',
            'full_tag_close' => '</ul></nav>',
            'num_tag_open' => '<li class="page-item">',
            'num_tag_close' => '</li>',
            'cur_tag_open' => '<li class="page-item active"><span class="page-link">',
            'cur_tag_close' => '</span></li>',
            'prev_tag_open' => '<li class="page-item">',
            'prev_tag_close' => '</li>',
            'next_tag_open' => '<li class="page-item">',
            'next_tag_close' => '</li>',
            'first_link' => 'Primeira',
            'last_link' => 'Última',
            'first_tag_open' => '<li class="page-item">',
            'first_tag_close' => '</li>',
            'last_tag_open' => '<li class="page-item">',
            'last_tag_close' => '</li>',
            'attributes' => ['class' => 'page-link'],
            'app_name' => 'Map-OS',
            'app_theme' => 'default',
            'os_notification' => 'cliente',
            'control_estoque' => '1',
            'notifica_whats' => '',
            'control_baixa' => '0',
            'control_editos' => '1',
            'control_datatable' => '1',
            'pix_key' => '',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        // Headers de seguranca para permitir geolocalizacao em iframes cross-origin
        // https://www.chromium.org/Home/chromium-security/deprecating-permissions-in-cross-origin-iframes/
        // Permissions-Policy é o header moderno (substitui Feature-Policy)
        header('Permissions-Policy: geolocation=(self)');

        // Carregar configurações primeiro (necessário para todas as áreas)
        $this->load_configuration();

        // Verificar se está acessando área do técnico (controller Tecnicos)
        // O controller Tecnicos tem sua própria autenticação
        $router = &load_class('Router', 'core');
        $controller = $router->fetch_class();
        $metodos_publicos_tecnico = ['login', 'autenticar', 'logout', 'api_login', 'api_verificar'];
        $metodo = $router->fetch_method();

        // Se for controller da API (por URI, classe ou diretório), não redirecionar
        $directory = $router->fetch_directory();
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $controllerLower = strtolower($controller);
        $isApiV2 = (strpos($requestUri, '/api/v2') !== false);
        // Lista explicita de controllers de API (mais seguro que strpos === 0 no nome)
        $apiControllers = ['api', 'api_docs', 'api_tools', 'webhook', 'webhooks'];
        $isApiController = in_array($controllerLower, $apiControllers, true)
            || ($directory && strpos(strtolower($directory), 'api') === 0);

        if ($isApiV2 || $isApiController) {
            // Não redirecionar - controllers da API têm autenticação própria
        }
        // Se for controller Tecnicos e método público, não redirecionar
        elseif (strtolower($controller) === 'tecnicos' && in_array($metodo, $metodos_publicos_tecnico)) {
            // Não redirecionar - o Tecnicos controller tem sua própria autenticação
        }
        // Se for controller Tecnicos mas método protegido, verificar sessão do técnico
        elseif (strtolower($controller) === 'tecnicos') {
            // Verifica sessão de técnico: nova (tec_logado) ou legada (logged_in + tec_id)
            $sessao_tecnico_valida = $this->session->userdata('tec_id') &&
                                     ($this->session->userdata('tec_logado') || $this->session->userdata('logged_in'));
            $sessao_admin_valida = $this->session->userdata('logado');
            if ((! session_id()) || (! $sessao_tecnico_valida && ! $sessao_admin_valida)) {
                redirect('tecnicos/login');
            }
        }
        // Para outros controllers, verificar sessão padrão (admin) OU sessão de técnico
        else {
            $sessao_admin = $this->session->userdata('logado');
            // Verifica sessão de técnico: nova (tec_logado) ou legada (logged_in + tec_id)
            $sessao_tecnico = $this->session->userdata('tec_id') &&
                              ($this->session->userdata('tec_logado') || $this->session->userdata('logged_in'));

            if ((! session_id()) || (! $sessao_admin && ! $sessao_tecnico)) {
                redirect('login');
            }
        }

        // Carregar library de permissoes
        $this->load->library('permission');
    }

    /**
     * Carrega configuracoes do banco com cache de arquivo (TTL 5 minutos).
     * Metodo protegido - use clearConfigCache() apos atualizar configuracoes.
     */
    private function load_configuration()
    {
        $cacheFile = APPPATH . 'cache/configuracoes_cache.json';
        $ttl = 300; // 5 minutos

        // Tenta usar cache se disponivel
        if (is_readable($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
            $cached = @file_get_contents($cacheFile);
            $decoded = $cached !== false ? json_decode($cached, true) : null;
            if (is_array($decoded)) {
                foreach ($decoded as $config => $valor) {
                    $this->data['configuration'][$config] = $valor;
                }
                return;
            }
        }

        // Cache miss ou invalido - consulta banco
        $this->load->database();
        $configuracoes = $this->db->get('configuracoes')->result();
        $serialized = [];

        foreach ($configuracoes as $c) {
            $this->data['configuration'][$c->config] = $c->valor;
            $serialized[$c->config] = $c->valor;
        }

        // Persiste cache (silencioso se nao tiver permissao de escrita)
        @file_put_contents($cacheFile, json_encode($serialized));
    }

    /**
     * Invalida o cache de configuracoes.
     * Chame apos salvar configuracoes em Mapos_model::updateConfiguracao().
     */
    protected function clearConfigCache()
    {
        $cacheFile = APPPATH . 'cache/configuracoes_cache.json';
        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }
    }

    /**
     * Verifica se o usuario logado eh um tecnico
     * Baseado no grupo de permissao (NAO no idPermissao 1 = Administrador)
     */
    protected function isTecnico()
    {
        $permissao_id = $this->session->userdata('permissao');

        // Admin (cPermissao) nunca eh considerado tecnico
        // Library 'permission' ja foi carregada no __construct (linha 98)
        // Nao recarregar para evitar overhead desnecessario
        if ($this->permission->checkPermission($permissao_id, 'cPermissao')) {
            return false;
        }

        // Verifica se tem permissao especifica de tecnico
        return $this->permission->checkPermission($permissao_id, 'vTecnicoDashboard');
    }

    public function layout()
    {
        // Define se estamos na area do tecnico
        $this->data['is_area_tecnico'] = $this->isTecnico();

        // load views
        $this->load->view('tema/topo', $this->data);

        $this->load->view('tema/conteudo', $this->data);
        $this->load->view('tema/rodape');
    }
}
