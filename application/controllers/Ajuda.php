<?php
/**
 * Controller: Ajuda
 *
 * Pagina de ajuda contextual (Fase 2.6 do Plano UX).
 * Rota padrao: /ajuda             -> hub central com lista de topicos
 * Rota:        /ajuda/tela/{slug} -> ajuda especifica de uma tela
 *
 * Os textos sao definidos em application/config/ux_help.php (facil de editar).
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ajuda extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->config('ux_help');
    }

    /**
     * Hub central: lista todas as telas disponiveis com ajuda.
     */
    public function index()
    {
        $help = $this->config->item('ux_help');

        // Agrupa por categoria para a view
        $grupos = [];
        foreach ($help['telas'] as $slug => $tela) {
            $cat = $tela['categoria'] ?? 'geral';
            $grupos[$cat][$slug] = $tela;
        }

        $this->data['help'] = $help;
        $this->data['grupos'] = $grupos;
        $this->data['title'] = 'Ajuda - MaposV5';
        $this->data['tela_atual'] = null;
        $this->data['view'] = 'ajuda/index';

        $this->layout();
    }

    /**
     * Ajuda especifica de uma tela. URL: /ajuda/tela/{slug}
     * @param string $slug Identificador da tela (ex: "os_adicionar")
     */
    public function tela($slug = null)
    {
        if (empty($slug)) {
            redirect('ajuda');
            return;
        }

        $help = $this->config->item('ux_help');
        $tela = $help['telas'][$slug] ?? null;

        $this->data['tela_atual'] = $slug;
        $this->data['tela'] = $tela;
        $this->data['title'] = ($tela['titulo'] ?? 'Ajuda') . ' - MaposV5';
        $this->data['help'] = $help;
        $this->data['view'] = 'ajuda/tela';

        $this->layout();
    }
}
