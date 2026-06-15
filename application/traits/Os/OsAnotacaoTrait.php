<?php

namespace Application\Traits\Os;

/**
 * Anotacoes vinculadas a uma OS.
 *
 * Formato de retorno: application/json
 * Requer: tabela anotacoes_os com colunas (idAnotacoes, anotacao, data_hora, os_id)
 */
trait OsAnotacaoTrait
{
    /**
     * POST /os/adicionarAnotacao
     * Body: anotacao, os_id
     */
    public function adicionarAnotacao()
    {
        $this->load->library('form_validation');
        if ($this->form_validation->run('anotacoes_os') == false) {
            $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(validation_errors()));
        } else {
            $data = [
                'anotacao' => '[' . $this->session->userdata('nome_admin') . '] ' . $this->input->post('anotacao'),
                'data_hora' => date('Y-m-d H:i:s'),
                'os_id' => $this->input->post('os_id'),
            ];

            if ($this->os_model->add('anotacoes_os', $data) == true) {
                log_info('Adicionou anotação a uma OS. ID (OS): ' . $this->input->post('os_id'));
                $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true]));
            } else {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false]));
            }
        }
    }

    /**
     * POST /os/excluirAnotacao
     * Body: idAnotacao, idOs
     */
    public function excluirAnotacao()
    {
        $id = $this->input->post('idAnotacao');
        $idOs = $this->input->post('idOs');

        if ($this->os_model->delete('anotacoes_os', 'idAnotacoes', $id) == true) {
            log_info('Removeu anotação de uma OS. ID (OS): ' . $idOs);
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true]));
        } else {
            $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false]));
        }
    }
}
