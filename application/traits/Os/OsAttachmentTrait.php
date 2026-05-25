<?php

defined('BASEPATH') or exit('No direct script access allowed');

namespace Application\Traits\Os;

trait OsAttachmentTrait
{
    public function anexar()
    {
        $this->load->library('upload');
        $this->load->library('image_lib');

        $directory = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'OS-' . $this->input->post('idOsServico');

        // If it exist, check if it's a directory
        if (! is_dir($directory . DIRECTORY_SEPARATOR . 'thumbs')) {
            // make directory for images and thumbs
            try {
                mkdir($directory . DIRECTORY_SEPARATOR . 'thumbs', 0755, true);
            } catch (Exception $e) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false, 'mensagem' => 'Erro ao criar diretorio de upload.']));
                log_message('error', 'Upload directory error: ' . $e->getMessage());
            }
        }

        $upload_conf = [
            'upload_path' => $directory,
            'allowed_types' => 'jpg|png|gif|jpeg|pdf|docx|txt|xls|xlsx|doc|zip',
            'max_size' => 10240, // 10MB max
        ];

        $this->upload->initialize($upload_conf);

        foreach ($_FILES['userfile'] as $key => $val) {
            $i = 1;
            foreach ($val as $v) {
                $field_name = 'file_' . $i;
                $_FILES[$field_name][$key] = $v;
                $i++;
            }
        }
        unset($_FILES['userfile']);

        $error = [];
        $success = [];

        foreach ($_FILES as $field_name => $file) {
            if (! $this->upload->do_upload($field_name)) {
                $error['upload'][] = $this->upload->display_errors();
            } else {
                $upload_data = $this->upload->data();

                // Gera um nome de arquivo aleatório mantendo a extensão original
                $new_file_name = uniqid() . '.' . pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                $new_file_path = $upload_data['file_path'] . $new_file_name;

                rename($upload_data['full_path'], $new_file_path);

                if ($upload_data['is_image'] == 1) {
                    $resize_conf = [
                        'source_image' => $new_file_path,
                        'new_image' => $upload_data['file_path'] . 'thumbs' . DIRECTORY_SEPARATOR . 'thumb_' . $new_file_name,
                        'width' => 200,
                        'height' => 125,
                    ];

                    $this->image_lib->initialize($resize_conf);

                    if (! $this->image_lib->resize()) {
                        $error['resize'][] = $this->image_lib->display_errors();
                    } else {
                        $success[] = $upload_data;
                        $this->load->model('Os_model');
                        $result = $this->Os_model->anexar($this->input->post('idOsServico'), $new_file_name, base_url('assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'OS-' . $this->input->post('idOsServico')), 'thumb_' . $new_file_name, $directory);
                        if (! $result) {
                            $error['db'][] = 'Erro ao inserir no banco de dados.';
                        }
                    }
                } else {
                    $success[] = $upload_data;

                    $this->load->model('Os_model');

                    $result = $this->Os_model->anexar($this->input->post('idOsServico'), $new_file_name, base_url('assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'OS-' . $this->input->post('idOsServico')), '', $directory);
                    if (! $result) {
                        $error['db'][] = 'Erro ao inserir no banco de dados.';
                    }
                }
            }
        }

        if (count($error) > 0) {
            $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false, 'mensagem' => 'Ocorreu um erro ao processar os arquivos.', 'errors' => $error]));
        } else {
            log_info('Adicionou anexo(s) a uma OS. ID (OS): ' . $this->input->post('idOsServico'));
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true, 'mensagem' => 'Arquivo(s) anexado(s) com sucesso.']));
        }
    }

    public function excluirAnexo($id = null)
    {
        if ($id == null || ! is_numeric($id)) {
            $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.']));
        } else {
            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos', 1)->row();
            $idOs = $this->input->post('idOs');

            unlink($file->path . DIRECTORY_SEPARATOR . $file->anexo);

            if ($file->thumb != null) {
                unlink($file->path . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $file->thumb);
            }

            if ($this->os_model->delete('anexos', 'idAnexos', $id) == true) {
                log_info('Removeu anexo de uma OS. ID (OS): ' . $idOs);
                $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true, 'mensagem' => 'Anexo excluído com sucesso.']));
            } else {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.']));
            }
        }
    }

    public function downloadanexo($id = null)
    {
        if ($id != null && is_numeric($id)) {
            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos', 1)->row();

            $this->load->library('zip');
            $path = $file->path;
            $this->zip->read_file($path . '/' . $file->anexo);
            $this->zip->download('file' . date('d-m-Y-H.i.s') . '.zip');
        }
    }
}