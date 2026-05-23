<?php

/**
 * Migration: Add LGPD consent fields to clientes table.
 */

class Migration_20260523000003_add_lgpd_consentimento extends CI_Migration {

    public function up()
    {
        if ($this->db->table_exists('clientes')) {
            if (! $this->db->field_exists('consentimento_lgpd', 'clientes')) {
                $this->dbforge->add_column('clientes', [
                    'consentimento_lgpd' => [
                        'type' => 'TINYINT',
                        'constraint' => 1,
                        'default' => 0,
                        'after' => 'fornecedor',
                    ],
                ]);
            }

            if (! $this->db->field_exists('data_consentimento', 'clientes')) {
                $this->dbforge->add_column('clientes', [
                    'data_consentimento' => [
                        'type' => 'DATETIME',
                        'null' => true,
                        'default' => null,
                        'after' => 'consentimento_lgpd',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->table_exists('clientes')) {
            if ($this->db->field_exists('consentimento_lgpd', 'clientes')) {
                $this->dbforge->drop_column('clientes', 'consentimento_lgpd');
            }

            if ($this->db->field_exists('data_consentimento', 'clientes')) {
                $this->dbforge->drop_column('clientes', 'data_consentimento');
            }
        }
    }
}