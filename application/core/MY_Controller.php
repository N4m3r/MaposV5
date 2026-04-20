<?php

class MY_Controller extends CI_Controller
{
    public $data = [
        'configuration' => [
            'per_page' => 10,
            'next_link' => 'Próxima',
            'prev_link' => 'Anterior',
            'full_tag_open' => '<div class="pagination alternate"><ul>',
            'full_tag_close' => '</ul></div>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
            'cur_tag_open' => '<li><a style="color: #2D335B"><b>',
            'cur_tag_close' => '</b></a></li>',
            'prev_tag_open' => '<li>',
            'prev_tag_close' => '</li>',
            'next_tag_open' => '<li>',
            'next_tag_close' => '</li>',
            'first_link' => 'Primeira',
            'last_link' => 'Última',
            'first_tag_open' => '<li>',
            'first_tag_close' => '</li>',
            'last_tag_open' => '<li>',
            'last_tag_close' => '</li>',
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
        header('Permissions-Policy: geolocation=(self)');
        header('Feature-Policy: geolocation *');

        if ((! session_id()) || (! $this->session->userdata('logado'))) {
            redirect('login');
        }

        // Carregar library de permissoes
        $this->load->library('permission');

        $this->load_configuration();
    }

    private function load_configuration()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $configuracoes = $this->CI->db->get('configuracoes')->result();

        foreach ($configuracoes as $c) {
            $this->data['configuration'][$c->config] = $c->valor;
        }
    }

    /**
     * Verifica se o usuario logado eh um tecnico
     * Baseado no grupo de permissao (NAO no idPermissao 1 = Administrador)
     */
    protected function isTecnico()
    {
        $permissao_id = $this->session->userdata('permissao');

        // Administrador (idPermissao 1) NUNCA é técnico
        if ($permissao_id == 1) {
            return false;
        }

        // Verifica se tem permissao especifica de tecnico
        $this->load->library('permission');
        return $this->permission->checkPermission($permissao_id, 'vTecnicoDashboard');
    }

    public function layout()
    {
        // load views
        $this->load->view('tema/topo', $this->data);

        // Verifica se eh tecnico e carrega menu apropriado
        if ($this->isTecnico()) {
            $this->load->view('tema/menu_tecnico');
        } else {
            $this->load->view('tema/menu');
        }

        $this->load->view('tema/conteudo', $this->data);
        $this->load->view('tema/rodape');
    }
}
