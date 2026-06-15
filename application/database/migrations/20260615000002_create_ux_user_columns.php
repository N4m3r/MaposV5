<?php
/**
 * Migration: Tabela ux_user_columns (F3.5)
 * Persiste colunas visiveis/ocultas por usuario e listagem.
 */
class Migration_create_ux_user_columns extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('ux_user_columns')) return;

        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'table_key' => ['type' => 'VARCHAR', 'constraint' => 60],
            'hidden'    => ['type' => 'TEXT', 'null' => true, 'comment' => 'JSON: [col_key, ...]'],
            'ordem'     => ['type' => 'TEXT', 'null' => true, 'comment' => 'JSON: ordem preferida'],
            'created_at'=> ['type' => 'DATETIME', 'default' => date('Y-m-d H:i:s')],
            'updated_at'=> ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key(['user_id', 'table_key'], false, true);
        $this->dbforge->create_table('ux_user_columns', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('ux_user_columns', true);
    }
}
