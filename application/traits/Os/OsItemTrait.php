<?php

defined('BASEPATH') or exit('No direct script access allowed');

namespace Application\Traits\Os;

trait OsItemTrait
{
    public function adicionarProduto()
    {
        $this->load->library('form_validation');

        if ($this->form_validation->run('adicionar_produto_os') === false) {
            $errors = validation_errors();

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $preco = $this->input->post('preco');
        $quantidade = $this->input->post('quantidade');
        $subtotal = $preco * $quantidade;
        $produto = $this->input->post('idProduto');
        $data = [
            'quantidade' => $quantidade,
            'subTotal' => $subtotal,
            'produtos_id' => $produto,
            'preco' => $preco,
            'os_id' => $this->input->post('idOsProduto'),
        ];

        $id = $this->input->post('idOsProduto');
        $os = $this->os_model->getById($id);
        if ($os == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar inserir produto na OS.');
            redirect(base_url() . 'index.php/os/gerenciar/');
        }

        if ($this->os_model->add('produtos_os', $data) == true) {
            $this->load->model('produtos_model');

            if ($this->data['configuration']['control_estoque']) {
                $this->produtos_model->updateEstoque($produto, $quantidade, '-');
            }

            $this->os_model->resetDiscount($id);

            log_info('Adicionou produto a uma OS. ID (OS): ' . $this->input->post('idOsProduto'));

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false]));
        }
    }

    public function excluirProduto()
    {
        $id = $this->input->post('idProduto');
        $idOs = $this->input->post('idOs');

        $os = $this->os_model->getById($idOs);
        if ($os == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir produto na OS.');
            redirect(base_url() . 'index.php/os/gerenciar/');
        }

        if ($this->os_model->delete('produtos_os', 'idProdutos_os', $id) == true) {
            $quantidade = $this->input->post('quantidade');
            $produto = $this->input->post('produto');

            $this->load->model('produtos_model');

            if ($this->data['configuration']['control_estoque']) {
                $this->produtos_model->updateEstoque($produto, $quantidade, '+');
            }

            $this->os_model->resetDiscount($idOs);

            log_info('Removeu produto de uma OS. ID (OS): ' . $idOs);

            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true]));
        } else {
            $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false]));
        }
    }

    public function adicionarServico()
    {
        $this->load->library('form_validation');

        if ($this->form_validation->run('adicionar_servico_os') === false) {
            $errors = validation_errors();

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $data = [
            'servicos_id' => $this->input->post('idServico'),
            'quantidade' => $this->input->post('quantidade'),
            'preco' => $this->input->post('preco'),
            'os_id' => $this->input->post('idOsServico'),
            'subTotal' => $this->input->post('preco') * $this->input->post('quantidade'),
        ];

        if ($this->os_model->add('servicos_os', $data) == true) {
            log_info('Adicionou serviço a uma OS. ID (OS): ' . $this->input->post('idOsServico'));

            $this->os_model->resetDiscount($this->input->post('idOsServico'));

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false]));
        }
    }

    public function excluirServico()
    {
        $ID = $this->input->post('idServico');
        $idOs = $this->input->post('idOs');

        if ($this->os_model->delete('servicos_os', 'idServicos_os', $ID) == true) {
            log_info('Removeu serviço de uma OS. ID (OS): ' . $idOs);
            $this->os_model->resetDiscount($idOs);
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true]));
        } else {
            $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false]));
        }
    }

    public function editarProduto()
    {
        $this->load->library('form_validation');

        $rules = [
            [
                'field' => 'idProdutoOs',
                'label' => 'ID Produto OS',
                'rules' => 'trim|required|numeric',
            ],
            [
                'field' => 'quantidade',
                'label' => 'quantidade',
                'rules' => 'trim|required|numeric|greater_than[0]',
            ],
            [
                'field' => 'preco',
                'label' => 'preco',
                'rules' => 'trim|required|numeric|greater_than[-1]',
            ],
        ];

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() === false) {
            $errors = validation_errors();
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $idProdutoOs = $this->input->post('idProdutoOs');
        $quantidade = $this->input->post('quantidade');
        $preco = $this->input->post('preco');
        $subtotal = $preco * $quantidade;

        $data = [
            'quantidade' => $quantidade,
            'preco' => $preco,
            'subTotal' => $subtotal,
        ];

        $this->db->where('idProdutos_os', $idProdutoOs);
        if ($this->db->update('produtos_os', $data)) {
            $this->os_model->resetDiscount($this->input->post('idOs'));

            log_info('Editou produto da OS. ID (Produto OS): ' . $idProdutoOs);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false]));
        }
    }

    /**
     * Zera o preço e subtotal de todos os produtos de uma OS
     */
    public function zerarPrecosProdutos()
    {
        $idOs = $this->input->post('idOs');

        if (!$idOs) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['result' => false, 'message' => 'OS não informada']));
        }

        // Verifica permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['result' => false, 'message' => 'Você não tem permissão para editar a OS']));
        }

        // Atualiza todos os produtos da OS: preco = 0, subTotal = 0
        $this->db->where('os_id', $idOs);
        $data = [
            'preco' => 0.00,
            'subTotal' => 0.00,
        ];

        if ($this->db->update('produtos_os', $data)) {
            // Limpa descontos da OS
            $this->os_model->resetDiscount($idOs);

            log_info('Zerou preços dos produtos da OS. ID: ' . $idOs);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false, 'message' => 'Erro ao atualizar preços']));
        }
    }

    public function editarServico()
    {
        $this->load->library('form_validation');

        $rules = [
            [
                'field' => 'idServicoOs',
                'label' => 'ID Servico OS',
                'rules' => 'trim|required|numeric',
            ],
            [
                'field' => 'quantidade',
                'label' => 'quantidade',
                'rules' => 'trim|required|numeric|greater_than[0]',
            ],
            [
                'field' => 'preco',
                'label' => 'preco',
                'rules' => 'trim|required|numeric|greater_than[-1]',
            ],
        ];

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() === false) {
            $errors = validation_errors();
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $idServicoOs = $this->input->post('idServicoOs');
        $quantidade = $this->input->post('quantidade');
        $preco = $this->input->post('preco');
        $subtotal = $preco * $quantidade;

        $data = [
            'quantidade' => $quantidade,
            'preco' => $preco,
            'subTotal' => $subtotal,
        ];

        $this->db->where('idServicos_os', $idServicoOs);
        if ($this->db->update('servicos_os', $data)) {
            $this->os_model->resetDiscount($this->input->post('idOs'));

            log_info('Editou serviço da OS. ID (Servico OS): ' . $idServicoOs);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false]));
        }
    }
}