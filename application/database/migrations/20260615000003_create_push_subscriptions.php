<?php
/**
 * Migration: Tabela push_subscriptions (F5.6)
 * Armazena inscricoes Web Push de cada usuario.
 *
 * Adicionado em 2026-06-15.
 *
 * Estrutura:
 *  - id
 *  - user_id       : usuario logado
 *  - endpoint      : URL unica do Push Service
 *  - p256dh        : chave publica do cliente
 *  - auth          : segredo de autenticacao
 *  - user_agent    : navegador/dispositivo (para identificar e limpar duplicatas)
 *  - ativo         : 1 = inscricao valida
 *  - created_at
 *  - updated_at
 */
class Migration_create_push_subscriptions extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('push_subscriptions')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'endpoint' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'p256dh' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'auth' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => date('Y-m-d H:i:s'),
            ],
            'updated_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
            ],
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('user_id');
        // Index unico no endpoint para evitar duplicatas
        $this->dbforge->add_key('endpoint', false);
        $this->dbforge->create_table('push_subscriptions', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('push_subscriptions', true);
    }
}
