<?php
/**
 * Migration: Criar tabela ux_tour_progress
 * Armazena o status dos tours guiados por usuario.
 *
 * Campos:
 *   - id: PK
 *   - user_id: FK para usuarios.id
 *   - tour_key: identificador do tour (ex: 'dashboard_inicial', 'os_basico')
 *   - completed_at: quando o usuario concluiu o tour (NULL = pendente)
 *   - skipped: 1 se o usuario pulou, 0 caso contrario
 *   - created_at, updated_at
 *
 * Adicionado: 2026-06-14 (Fase 2.1.2 do Plano UX)
 */
class Migration_create_ux_tour_progress extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('ux_tour_progress')) {
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
                'comment' => 'FK usuarios.id',
            ],
            'tour_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Identificador unico do tour (slug)',
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Quando concluiu (NULL = pendente)',
            ],
            'skipped' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = pulou, 0 = concluiu',
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
        // Chave unica: um usuario so pode ter 1 registro por tour
        $this->dbforge->add_key(['user_id', 'tour_key'], false, true);
        $this->dbforge->add_key('tour_key');
        $this->dbforge->create_table('ux_tour_progress', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('ux_tour_progress', true);
    }
}
