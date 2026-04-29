<?php
/**
 * Controller: Teste e Diagnóstico do Módulo NFSe
 * Valida o ambiente antes de emissão de NFS-e
 * Acessar via: index.php/teste_nfse
 */

class Teste_nfse extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('impostos_model');
        $this->load->model('mapos_model');
        $this->load->model('os_model');
    }

    public function index()
    {
        $this->data['view'] = 'teste_nfse';
        return $this->layout();
    }
}
