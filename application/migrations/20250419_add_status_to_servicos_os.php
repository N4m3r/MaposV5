<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_status_to_servicos_os extends CI_Migration
{
    public function up()
    {
        // Verificar se coluna status existe
        if (!$this->db->field_exists('status', 'servicos_os')) {
            $this->load->dbforge();
            $fields = [
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'Pendente',
                    'null' => FALSE,
                    'after' => 'subTotal'
                ]
            ];
            $this->dbforge->add_column('servicos_os', $fields);

            // Atualizar registros existentes para Pendente
            $this->db->update('servicos_os', ['status' => 'Pendente']);

            log_message('info', 'Migration: Coluna status adicionada à tabela servicos_os');
        }
    }

    public function down()
    {
        if ($this->db->field_exists('status', 'servicos_os')) {
            $this->load->dbforge();
            $this->dbforge->drop_column('servicos_os', 'status');
        }
    }
}
