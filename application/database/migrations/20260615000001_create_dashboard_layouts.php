<?php
/**
 * Migration: Tabela dashboard_layouts (F3.1 + F3.2)
 * Persiste o layout customizado do dashboard por usuario/perfil.
 *
 * Adicionado em 2026-06-15.
 */
class Migration_create_dashboard_layouts extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('dashboard_layouts')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'NULL = layout compartilhado por perfil',
            ],
            'perfil' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'comment' => 'admin | tecnico | financeiro | padrao | user_<id>_<hash>',
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
                'comment' => 'Nome amigavel (para perfis salvos pelo usuario)',
            ],
            'layout' => [
                'type' => 'TEXT',
                'comment' => 'JSON: array ordenado de chaves de widgets',
            ],
            'visibility' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON: {widget: bool}',
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = este e o layout ativo do usuario',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => date('Y-m-d H:i:s'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key(['user_id', 'perfil']);
        $this->dbforge->add_key('perfil');
        $this->dbforge->add_key('ativo');
        $this->dbforge->create_table('dashboard_layouts', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('dashboard_layouts', true);
    }
}
