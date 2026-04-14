<?php
/**
 * Migration: Criar tabela de usuários da área do cliente
 * Permite múltiplos usuários por cliente com permissões personalizadas
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_usuarios_cliente extends CI_Migration {

    public function up()
    {
        // Tabela de usuários do portal do cliente
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'cliente_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'comment' => 'ID do cliente vinculado (opcional, pode ser nulo se acesso apenas por CNPJ)'
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
                'unique' => TRUE
            ],
            'senha' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'telefone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'token_reset' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'token_expira' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'ultimo_acesso' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('usuarios_cliente', TRUE);

        // Adicionar índice para cliente_id
        $this->db->query('ALTER TABLE `usuarios_cliente` ADD INDEX `idx_cliente_id` (`cliente_id`)');

        // Tabela de vínculo entre usuário e CNPJs permitidos
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'usuario_cliente_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'cnpj' => [
                'type' => 'VARCHAR',
                'constraint' => 18,
                'null' => FALSE,
                'comment' => 'CNPJ no formato 00.000.000/0000-00'
            ],
            'razao_social' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('usuarios_cliente_cnpjs', TRUE);

        $this->db->query('ALTER TABLE `usuarios_cliente_cnpjs` ADD INDEX `idx_usuario_cnpj` (`usuario_cliente_id`, `cnpj`)');

        // Tabela de permissões/configurações do usuário
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'usuario_cliente_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'chave' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
                'comment' => 'Nome da permissão/configuração'
            ],
            'valor' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Valor da configuração (pode ser serializado)'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('usuarios_cliente_permissoes', TRUE);

        $this->db->query('ALTER TABLE `usuarios_cliente_permissoes` ADD INDEX `idx_usuario_chave` (`usuario_cliente_id`, `chave`)');
    }

    public function down()
    {
        $this->dbforge->drop_table('usuarios_cliente_permissoes');
        $this->dbforge->drop_table('usuarios_cliente_cnpjs');
        $this->dbforge->drop_table('usuarios_cliente');
    }
}
