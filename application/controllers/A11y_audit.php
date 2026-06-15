<?php
/**
 * Audit de acessibilidade (F6.7) — roda axe-core na pagina atual
 * Endpoint: /auditoria/a11y
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class A11y_audit extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function index()
    {
        $this->data['title'] = 'Auditoria de Acessibilidade (WCAG 2.1)';
        $this->data['view']  = 'tema/a11y_audit';
        return $this->layout();
    }
}
