<?php
/**
 * Migration: Adiciona Inscricao Municipal na tabela emitente
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_inscricao_municipal_emitente extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('emitente')) {
            if (!$this->db->field_exists('inscricao_municipal', 'emitente')) {
                $this->dbforge->add_column('emitente', [
                    'inscricao_municipal' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                        'null' => true,
                        'after' => 'ie',
                        'comment' => 'Inscricao Municipal do emitente'
                    ]
                ]);
            }
        }
        log_message('info', 'Migration Add_inscricao_municipal_emitente executada com sucesso.');
    }

    public function down()
    {
        if ($this->db->table_exists('emitente')) {
            if ($this->db->field_exists('inscricao_municipal', 'emitente')) {
                $this->dbforge->drop_column('emitente', 'inscricao_municipal');
            }
        }
    }
}
