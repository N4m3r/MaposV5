<?php

defined('BASEPATH') or exit('No direct script access allowed');

namespace Application\Traits\Os;

trait OsAutocompleteTrait
{
    private function autocompleteResponse(callable $modelCallback): void
    {
        if (!$this->session->userdata('logado')) {
            $this->output->set_status_header(401)->set_output(json_encode(['error' => 'Não autenticado']));
            return;
        }
        if ($term = $this->input->get('term')) {
            $result = $modelCallback(strtolower($term));
            if ($result !== null) {
                $this->output->set_content_type('application/json')->set_output(json_encode($result));
            }
        }
    }

    public function autoCompleteProduto()
    {
        $this->autocompleteResponse(fn($q) => $this->os_model->autoCompleteProduto($q));
    }

    public function autoCompleteProdutoSaida()
    {
        $this->autocompleteResponse(fn($q) => $this->os_model->autoCompleteProdutoSaida($q));
    }

    public function autoCompleteCliente()
    {
        $this->autocompleteResponse(fn($q) => $this->os_model->autoCompleteCliente($q));
    }

    public function autoCompleteUsuario()
    {
        $this->autocompleteResponse(fn($q) => $this->os_model->autoCompleteUsuario($q));
    }

    public function autoCompleteTermoGarantia()
    {
        $this->autocompleteResponse(fn($q) => $this->os_model->autoCompleteTermoGarantia($q));
    }

    public function autoCompleteServico()
    {
        $this->autocompleteResponse(fn($q) => $this->os_model->autoCompleteServico($q));
    }
}