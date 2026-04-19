<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Obra_Tarefas extends CI_Migration {

    public function up()
    {
        // Tabela de tarefas das obras
        if (!$this->db->table_exists('obra_tarefas')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'obra_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'tecnico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'titulo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => FALSE
                ],
                'descricao' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['pendente', 'em_andamento', 'pausada', 'concluida', 'cancelada'],
                    'default' => 'pendente'
                ],
                'prioridade' => [
                    'type' => 'ENUM',
                    'constraint' => ['baixa', 'normal', 'alta', 'urgente'],
                    'default' => 'normal'
                ],
                'data_inicio' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'data_fim_prevista' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'data_fim_real' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'percentual_concluido' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    'default' => 0
                ],
                'observacoes' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'criado_por' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
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
            $this->dbforge->add_key('obra_id');
            $this->dbforge->add_key('tecnico_id');
            $this->dbforge->add_key('status');
            $this->dbforge->create_table('obra_tarefas');
        }

        // Tabela de histórico/registro de atividades das tarefas
        if (!$this->db->table_exists('obra_tarefas_historico')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'tarefa_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'tecnico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'tipo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => FALSE
                ],
                'descricao' => [
                    'type' => 'TEXT',
                    'null' => FALSE
                ],
                'percentual_anterior' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    'default' => 0
                ],
                'percentual_novo' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    'default' => 0
                ],
                'horas_trabalhadas' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => 0.00
                ],
                'data_registro' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'localizacao_lat' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => TRUE
                ],
                'localizacao_lng' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => TRUE
                ]
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('tarefa_id');
            $this->dbforge->add_key('tecnico_id');
            $this->dbforge->create_table('obra_tarefas_historico');
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('obra_tarefas_historico', TRUE);
        $this->dbforge->drop_table('obra_tarefas', TRUE);
    }
}
