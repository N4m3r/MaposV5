<?php
/**
 * Migration: Criar tabela de notificacoes de vazamento de dados (LGPD Art. 48)
 * Registra incidentes de vazamento e notificacao aos titulares afetados
 */

class Migration_create_data_breach_notifications_table extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('data_breach_notifications')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'titulo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'descricao' => [
                'type' => 'TEXT',
            ],
            'tipo_dado_afetado' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Ex: dados pessoais, financeiros, credenciais',
            ],
            'medidas_adotadas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_ocorrencia' => [
                'type' => 'DATETIME',
            ],
            'data_descoberta' => [
                'type' => 'DATETIME',
            ],
            'notificado_anpd' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'data_notificacao_anpd' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'titulares_notificados' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'data_notificacao_titulares' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'num_titulares_afetados' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'default' => 'investigando',
                'comment' => 'investigando, notificado, resolvido',
            ],
            'registrado_por' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
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
        $this->dbforge->add_key('status');
        $this->dbforge->add_key('data_ocorrencia');
        $this->dbforge->create_table('data_breach_notifications', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('data_breach_notifications', true);
    }
}