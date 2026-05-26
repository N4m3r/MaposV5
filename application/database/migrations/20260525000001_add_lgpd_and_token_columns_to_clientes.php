<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Add LGPD compliance columns and access token to clientes table
 * - token_acesso: random token for client portal access (replaces email in URL)
 * - consentimento_lgpd: LGPD consent flag
 * - data_consentimento: when consent was given
 * - origem_dados: source of personal data
 */
class Migration_Add_lgpd_and_token_columns_to_clientes extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('clientes')) {
            return;
        }

        $fields = [];

        if (!$this->db->field_exists('consentimento_lgpd', 'clientes')) {
            $fields['consentimento_lgpd'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => $this->getLastColumn('clientes'),
            ];
        }

        if (!$this->db->field_exists('data_consentimento', 'clientes')) {
            $fields['data_consentimento'] = [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ];
        }

        if (!$this->db->field_exists('origem_dados', 'clientes')) {
            $fields['origem_dados'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => null,
            ];
        }

        if (!$this->db->field_exists('token_acesso', 'clientes')) {
            $fields['token_acesso'] = [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'default' => null,
            ];
        }

        if (!empty($fields)) {
            $this->dbforge->add_column('clientes', $fields);
        }

        // Backfill: generate access tokens for existing clients
        $clients = $this->db->select('idClientes')->where('token_acesso IS NULL', null, false)->get('clientes')->result();
        foreach ($clients as $client) {
            $token = bin2hex(random_bytes(32));
            $this->db->where('idClientes', $client->idClientes)
                      ->update('clientes', ['token_acesso' => $token]);
        }

        // Add unique index on token_acesso
        $indexExists = $this->db->query("SHOW INDEX FROM clientes WHERE Key_name = 'idx_clientes_token_acesso'")->num_rows();
        if (!$indexExists) {
            $this->db->query('CREATE UNIQUE INDEX idx_clientes_token_acesso ON clientes(token_acesso)');
        }
    }

    public function down()
    {
        if ($this->db->table_exists('clientes')) {
            $columns = ['consentimento_lgpd', 'data_consentimento', 'origem_dados', 'token_acesso'];
            foreach ($columns as $col) {
                if ($this->db->field_exists($col, 'clientes')) {
                    $this->dbforge->drop_column('clientes', $col);
                }
            }
        }
    }

    private function getLastColumn($table)
    {
        $fields = $this->db->field_data($table);
        if (!empty($fields)) {
            return $fields[count($fields) - 1]->name;
        }
        return 'id';
    }
}