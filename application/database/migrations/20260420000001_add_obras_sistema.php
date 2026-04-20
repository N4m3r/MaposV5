<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration para Sistema Completo de Gestão de Obras
 * Inclui: atividades, checkins, notificações cliente, mensagens
 */
class Migration_Add_Obras_Sistema extends CI_Migration {

    public function up()
    {
        // ============================================
        // 1. TABELA: obra_atividades
        // ============================================
        if (!$this->db->table_exists('obra_atividades')) {
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
                'etapa_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'tecnico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'os_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'data_atividade' => [
                    'type' => 'DATE',
                    'null' => FALSE
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
                'tipo' => [
                    'type' => 'ENUM',
                    'constraint' => ['trabalho', 'impedimento', 'visita', 'manutencao', 'outro'],
                    'default' => 'trabalho'
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['agendada', 'iniciada', 'pausada', 'concluida', 'cancelada'],
                    'default' => 'agendada'
                ],
                'percentual_concluido' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    'default' => 0
                ],
                'hora_inicio' => [
                    'type' => 'TIME',
                    'null' => TRUE
                ],
                'hora_fim' => [
                    'type' => 'TIME',
                    'null' => TRUE
                ],
                'horas_trabalhadas' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => 0.00
                ],
                'impedimento' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'motivo_impedimento' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tipo_impedimento' => [
                    'type' => 'ENUM',
                    'constraint' => ['clima', 'falta_material', 'falta_ferramenta', 'acesso_negado',
                                     'problema_tecnico', 'outro'],
                    'null' => TRUE
                ],
                'checkin_lat' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => TRUE
                ],
                'checkin_lng' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => TRUE
                ],
                'checkout_lat' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => TRUE
                ],
                'checkout_lng' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => TRUE
                ],
                'fotos_checkin' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'fotos_atividade' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'fotos_checkout' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'visivel_cliente' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1
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
            $this->dbforge->add_key('etapa_id');
            $this->dbforge->add_key('tecnico_id');
            $this->dbforge->add_key('data_atividade');
            $this->dbforge->add_key('status');
            $this->dbforge->create_table('obra_atividades');
        }

        // ============================================
        // 2. TABELA: obra_atividades_historico
        // ============================================
        if (!$this->db->table_exists('obra_atividades_historico')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'atividade_id' => [
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
                'tipo_alteracao' => [
                    'type' => 'ENUM',
                    'constraint' => ['inicio', 'pausa', 'retorno', 'conclusao', 'impedimento', 'foto', 'observacao'],
                    'null' => FALSE
                ],
                'descricao' => [
                    'type' => 'TEXT',
                    'null' => TRUE
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
                'localizacao_lat' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => TRUE
                ],
                'localizacao_lng' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => TRUE
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('atividade_id');
            $this->dbforge->create_table('obra_atividades_historico');
        }

        // ============================================
        // 3. TABELA: obra_checkins
        // ============================================
        if (!$this->db->table_exists('obra_checkins')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'atividade_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'tecnico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'tipo' => [
                    'type' => 'ENUM',
                    'constraint' => ['checkin', 'checkout', 'pausa', 'retorno'],
                    'null' => FALSE
                ],
                'latitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => TRUE
                ],
                'longitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => TRUE
                ],
                'endereco_detectado' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'foto_url' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'observacao' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('atividade_id');
            $this->dbforge->add_key('tecnico_id');
            $this->dbforge->create_table('obra_checkins');
        }

        // ============================================
        // 4. TABELA: obra_cliente_notificacoes
        // ============================================
        if (!$this->db->table_exists('obra_cliente_notificacoes')) {
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
                'cliente_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'tipo' => [
                    'type' => 'ENUM',
                    'constraint' => ['etapa_inicio', 'etapa_fim', 'fotos_novas', 'impedimento',
                                     'atraso', 'mensagem', 'relatorio', 'outro'],
                    'null' => FALSE
                ],
                'titulo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => FALSE
                ],
                'mensagem' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'url_destino' => [
                    'type' => 'VARCHAR',
                    'constraint' => 500,
                    'null' => TRUE
                ],
                'entidade_relacionada' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE
                ],
                'entidade_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ],
                'lida' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'data_leitura' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'email_enviado' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'whatsapp_enviado' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('obra_id');
            $this->dbforge->add_key('cliente_id');
            $this->dbforge->add_key('lida');
            $this->dbforge->add_key('created_at');
            $this->dbforge->create_table('obra_cliente_notificacoes');
        }

        // ============================================
        // 5. TABELA: obra_cliente_acessos
        // ============================================
        if (!$this->db->table_exists('obra_cliente_acessos')) {
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
                'cliente_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'ip' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => TRUE
                ],
                'user_agent' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'pagina_acessada' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'tempo_na_pagina' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('obra_id');
            $this->dbforge->add_key('cliente_id');
            $this->dbforge->add_key('created_at');
            $this->dbforge->create_table('obra_cliente_acessos');
        }

        // ============================================
        // 6. TABELA: obra_compartilhamentos
        // ============================================
        if (!$this->db->table_exists('obra_compartilhamentos')) {
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
                'cliente_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'token' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => FALSE
                ],
                'tipo' => [
                    'type' => 'ENUM',
                    'constraint' => ['fotos', 'relatorio', 'progresso'],
                    'default' => 'fotos'
                ],
                'data_inicio' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'data_fim' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'etapa_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ],
                'data_expiracao' => [
                    'type' => 'DATETIME',
                    'null' => FALSE
                ],
                'acessos_permitidos' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'acessos_realizados' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'ativo' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('obra_id');
            $this->dbforge->add_key('token');
            $this->dbforge->add_key('data_expiracao');
            $this->dbforge->create_table('obra_compartilhamentos');
        }

        // ============================================
        // 7. TABELA: obra_mensagens
        // ============================================
        if (!$this->db->table_exists('obra_mensagens')) {
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
                'remetente_tipo' => [
                    'type' => 'ENUM',
                    'constraint' => ['cliente', 'gestor', 'sistema'],
                    'null' => FALSE
                ],
                'remetente_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'mensagem' => [
                    'type' => 'TEXT',
                    'null' => FALSE
                ],
                'anexo_url' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'anexo_tipo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE
                ],
                'lida' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'data_leitura' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'resposta_para' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('obra_id');
            $this->dbforge->add_key('remetente_id');
            $this->dbforge->add_key('lida');
            $this->dbforge->add_key('created_at');
            $this->dbforge->create_table('obra_mensagens');
        }

        // ============================================
        // 8. ADICIONAR COLUNAS À TABELA obras EXISTENTE
        // ============================================
        if ($this->db->table_exists('obras')) {
            // Verificar e adicionar colunas se não existirem
            if (!$this->db->field_exists('visivel_cliente', 'obras')) {
                $this->dbforge->add_column('obras', [
                    'visivel_cliente' => [
                        'type' => 'TINYINT',
                        'constraint' => 1,
                        'default' => 1,
                        'after' => 'status'
                    ]
                ]);
            }

            if (!$this->db->field_exists('ativo', 'obras')) {
                $this->dbforge->add_column('obras', [
                    'ativo' => [
                        'type' => 'TINYINT',
                        'constraint' => 1,
                        'default' => 1,
                        'after' => 'visivel_cliente'
                    ]
                ]);
            }

            if (!$this->db->field_exists('valor_contrato', 'obras')) {
                $this->dbforge->add_column('obras', [
                    'valor_contrato' => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,2',
                        'null' => TRUE,
                        'after' => 'observacoes'
                    ]
                ]);
            }

            if (!$this->db->field_exists('gestor_id', 'obras')) {
                $this->dbforge->add_column('obras', [
                    'gestor_id' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'null' => TRUE,
                        'after' => 'responsavel_tecnico_id'
                    ]
                ]);
            }
        }

        // ============================================
        // 9. ADICIONAR COLUNAS À TABELA obra_etapas
        // ============================================
        if ($this->db->table_exists('obra_etapas')) {
            if (!$this->db->field_exists('visivel_cliente', 'obra_etapas')) {
                $this->dbforge->add_column('obra_etapas', [
                    'visivel_cliente' => [
                        'type' => 'TINYINT',
                        'constraint' => 1,
                        'default' => 1,
                        'after' => 'status'
                    ]
                ]);
            }
        }
    }

    public function down()
    {
        // Remover tabelas na ordem inversa (por causa das FKs)
        $this->dbforge->drop_table('obra_mensagens', TRUE);
        $this->dbforge->drop_table('obra_compartilhamentos', TRUE);
        $this->dbforge->drop_table('obra_cliente_acessos', TRUE);
        $this->dbforge->drop_table('obra_cliente_notificacoes', TRUE);
        $this->dbforge->drop_table('obra_checkins', TRUE);
        $this->dbforge->drop_table('obra_atividades_historico', TRUE);
        $this->dbforge->drop_table('obra_atividades', TRUE);
    }
}
