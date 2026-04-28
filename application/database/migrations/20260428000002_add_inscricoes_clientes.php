<?php
/**
 * Migration: Adiciona Inscricao Municipal e Estadual na tabela clientes
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_inscricoes_clientes extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('clientes')) {
            if (!$this->db->field_exists('inscricao_municipal', 'clientes')) {
                $this->dbforge->add_column('clientes', [
                    'inscricao_municipal' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                        'null' => true,
                        'after' => 'documento',
                        'comment' => 'Inscricao Municipal do cliente/fornecedor'
                    ]
                ]);
            }
            if (!$this->db->field_exists('inscricao_estadual', 'clientes')) {
                $this->dbforge->add_column('clientes', [
                    'inscricao_estadual' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                        'null' => true,
                        'after' => 'inscricao_municipal',
                        'comment' => 'Inscricao Estadual do cliente/fornecedor'
                    ]
                ]);
            }
        }
        log_message('info', 'Migration Add_inscricoes_clientes executada com sucesso.');
    }

    public function down()
    {
        if ($this->db->table_exists('clientes')) {
            if ($this->db->field_exists('inscricao_municipal', 'clientes')) {
                $this->dbforge->drop_column('clientes', 'inscricao_municipal');
            }
            if ($this->db->field_exists('inscricao_estadual', 'clientes')) {
                $this->dbforge->drop_column('clientes', 'inscricao_estadual');
            }
        }
    }
}
