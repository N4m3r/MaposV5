<?php
/**
 * Migration: Sistema Completo de Técnicos
 * Data: 2026-04-13
 * Cria todas as tabelas e campos necessários para o sistema de técnicos
 */

class Migration_Sistema_Tecnicos_Completo extends CI_Migration
{
    public function up()
    {
        // 1. Adicionar campos na tabela usuarios
        $this->add_campos_usuarios();

        // 2. Criar tabela de catálogo de serviços
        $this->criar_tabela_servicos_catalogo();

        // 3. Criar tabela de serviços vinculados à OS
        $this->criar_tabela_os_servicos();

        // 4. Criar tabela de execução técnica detalhada
        $this->criar_tabela_tec_os_execucao();

        // 5. Criar tabela de templates de checklists
        $this->criar_tabela_tec_checklist_template();

        // 6. Criar tabela de estoque no veículo
        $this->criar_tabela_tec_estoque_veiculo();

        // 7. Criar tabela de tracking de rotas
        $this->criar_tabela_tec_rotas_tracking();

        // 8. Criar tabela de obras
        $this->criar_tabela_obras();

        // 9. Criar tabela de etapas de obras
        $this->criar_tabela_obra_etapas();

        // 10. Criar tabela de diário de obra
        $this->criar_tabela_obra_diario();

        // 11. Inserir dados iniciais
        $this->inserir_dados_iniciais();
    }

    private function add_campos_usuarios()
    {
        // Verificar se campos já existem
        $fields = $this->db->list_fields('usuarios');

        $campos = [
            'is_tecnico' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => FALSE,
                'comment' => 'Indica se é técnico de campo',
                'after' => 'url_image_user'
            ],
            'nivel_tecnico' => [
                'type' => 'ENUM',
                'constraint' => ["'I'", "'II'", "'III'", "'IV'"],
                'default' => 'II',
                'null' => TRUE,
                'comment' => 'Nível do técnico: I=Aprendiz, II=Técnico, III=Especialista, IV=Coordenador',
                'after' => 'is_tecnico'
            ],
            'especialidades' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'comment' => 'Especialidades separadas por vírgula: CFTV,Alarmes,Redes,ControleAcesso',
                'after' => 'nivel_tecnico'
            ],
            'veiculo_placa' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => TRUE,
                'after' => 'especialidades'
            ],
            'veiculo_tipo' => [
                'type' => 'ENUM',
                'constraint' => ["'Moto'", "'Carro'", "'Nenhum'"],
                'default' => 'Nenhum',
                'null' => TRUE,
                'after' => 'veiculo_placa'
            ],
            'coordenadas_base_lat' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => TRUE,
                'comment' => 'Latitude da base/matriz',
                'after' => 'veiculo_tipo'
            ],
            'coordenadas_base_lng' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => TRUE,
                'comment' => 'Longitude da base/matriz',
                'after' => 'coordenadas_base_lat'
            ],
            'raio_atuacao_km' => [
                'type' => 'INT',
                'default' => 50,
                'null' => TRUE,
                'comment' => 'Raio máximo de atuação em km',
                'after' => 'coordenadas_base_lng'
            ],
            'plantao_24h' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => TRUE,
                'after' => 'raio_atuacao_km'
            ],
            'app_tecnico_instalado' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => TRUE,
                'after' => 'plantao_24h'
            ],
            'token_app' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'comment' => 'Token para notificações push',
                'after' => 'app_tecnico_instalado'
            ],
            'token_expira' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'after' => 'token_app'
            ],
            'ultimo_acesso_app' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'after' => 'token_expira'
            ],
            'foto_tecnico' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'after' => 'ultimo_acesso_app'
            ]
        ];

        foreach ($campos as $nome => $definicao) {
            if (!in_array($nome, $fields)) {
                $this->dbforge->add_column('usuarios', [$nome => $definicao]);
            }
        }
    }

    private function criar_tabela_servicos_catalogo()
    {
        if (!$this->db->table_exists('servicos_catalogo')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'codigo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => TRUE,
                    'unique' => TRUE
                ],
                'nome' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => FALSE
                ],
                'descricao' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'categoria' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'default' => 'Geral',
                    'comment' => 'CFTV, Alarme, Rede, ControleAcesso, etc'
                ],
                'especialidade' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE,
                    'comment' => 'Qual técnico pode executar'
                ],
                'tempo_estimado_minutos' => [
                    'type' => 'INT',
                    'default' => 60
                ],
                'checklist_padrao' => [
                    'type' => 'JSON',
                    'null' => TRUE,
                    'comment' => 'Checklist em formato JSON'
                ],
                'ativo' => [
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
            $this->dbforge->add_key('categoria');
            $this->dbforge->add_key('especialidade');
            $this->dbforge->create_table('servicos_catalogo', TRUE, ['ENGINE' => 'InnoDB']);
        }
    }

    private function criar_tabela_os_servicos()
    {
        if (!$this->db->table_exists('os_servicos')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'os_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'servico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'quantidade' => [
                    'type' => 'INT',
                    'default' => 1
                ],
                'observacao' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'status' => [
                    'type' => "ENUM('Pendente','EmExecucao','Concluido','Cancelado')",
                    'default' => 'Pendente',
                    'null' => FALSE
                ],
                'checklist_execucao' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'checklist_completude' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'tecnico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'data_inicio' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'data_conclusao' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'tempo_execucao_minutos' => [
                    'type' => 'INT',
                    'null' => TRUE
                ],
                'fotos' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'assinatura_cliente' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'laudo_tecnico' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'ordem_execucao' => [
                    'type' => 'INT',
                    'default' => 0
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
            $this->dbforge->add_key('os_id');
            $this->dbforge->add_key('status');
            $this->dbforge->add_key('tecnico_id');
            $this->dbforge->create_table('os_servicos', TRUE, ['ENGINE' => 'InnoDB']);
        }
    }

    private function criar_tabela_tec_os_execucao()
    {
        if (!$this->db->table_exists('tec_os_execucao')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'os_id' => [
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
                'tipo_servico' => [
                    'type' => "ENUM('INS','MP','MC','CT','TR','UP','URG')",
                    'default' => 'MC',
                    'comment' => 'INS=Instalação, MP=Manut.Prev, MC=Manut.Corr, CT=Consultoria, TR=Treinamento, UP=Upgrade, URG=Urgência'
                ],
                'especialidade' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE
                ],
                'checkin_horario' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'checkin_latitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => TRUE
                ],
                'checkin_longitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => TRUE
                ],
                'checkin_endereco' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'checkin_foto' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'checkin_distancia_metros' => [
                    'type' => 'INT',
                    'null' => TRUE,
                    'comment' => 'Distância do cliente'
                ],
                'checkout_horario' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'checkout_latitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => TRUE
                ],
                'checkout_longitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => TRUE
                ],
                'checkout_endereco' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'checkout_foto' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'checkout_distancia_metros' => [
                    'type' => 'INT',
                    'null' => TRUE
                ],
                'tempo_atendimento_minutos' => [
                    'type' => 'INT',
                    'null' => TRUE
                ],
                'tempo_deslocamento_minutos' => [
                    'type' => 'INT',
                    'null' => TRUE
                ],
                'km_deslocamento' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => TRUE
                ],
                'checklist_json' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'checklist_completude' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'fotos_antes' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'fotos_depois' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'fotos_durante' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'assinatura_cliente' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'nome_responsavel' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'avaliacao' => [
                    'type' => 'INT',
                    'null' => TRUE,
                    'comment' => '1-5 estrelas'
                ],
                'comentario_cliente' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'laudo_tecnico' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'materiais_utilizados' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'observacoes_tecnico' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'problema_encontrado' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'solucao_aplicada' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'recomendacoes' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'oportunidade_venda' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'descricao_oportunidade' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'status_execucao' => [
                    'type' => "ENUM('Agendada','EmDeslocamento','EmAtendimento','Pausada','Concluida','Cancelada')",
                    'default' => 'Agendada',
                    'null' => FALSE
                ],
                'aprovada' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'aprovada_por' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'data_aprovacao' => [
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
            $this->dbforge->add_key('os_id');
            $this->dbforge->add_key('tecnico_id');
            $this->dbforge->add_key('status_execucao');
            $this->dbforge->add_key('checkin_horario');
            $this->dbforge->create_table('tec_os_execucao', TRUE, ['ENGINE' => 'InnoDB']);
        }
    }

    private function criar_tabela_tec_checklist_template()
    {
        if (!$this->db->table_exists('tec_checklist_template')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'tipo_os' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => FALSE
                ],
                'tipo_servico' => [
                    'type' => "ENUM('INS','MP','MC','CT','TR','UP')",
                    'default' => 'MC',
                    'null' => FALSE
                ],
                'nome_template' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE
                ],
                'itens' => [
                    'type' => 'JSON',
                    'null' => FALSE
                ],
                'ativo' => [
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
            $this->dbforge->add_key('tipo_os');
            $this->dbforge->add_key('tipo_servico');
            $this->dbforge->add_key(['tipo_os', 'tipo_servico'], TRUE, 'uk_template');
            $this->dbforge->create_table('tec_checklist_template', TRUE, ['ENGINE' => 'InnoDB']);
        }
    }

    private function criar_tabela_tec_estoque_veiculo()
    {
        if (!$this->db->table_exists('tec_estoque_veiculo')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'tecnico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'produto_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'quantidade_disponivel' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'quantidade_reservada' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'localizacao' => [
                    'type' => "ENUM('Veiculo','EmUso','Retirado')",
                    'default' => 'Veiculo'
                ],
                'ultima_movimentacao' => [
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
            $this->dbforge->add_key(['tecnico_id', 'produto_id'], TRUE, 'uk_tecnico_produto');
            $this->dbforge->add_key('tecnico_id');
            $this->dbforge->create_table('tec_estoque_veiculo', TRUE, ['ENGINE' => 'InnoDB']);
        }
    }

    private function criar_tabela_tec_rotas_tracking()
    {
        if (!$this->db->table_exists('tec_rotas_tracking')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'tecnico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'data' => [
                    'type' => 'DATE',
                    'null' => FALSE
                ],
                'pontos_rota' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'km_total' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0
                ],
                'os_atendidas' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'tempo_total_horas' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => 0
                ],
                'combustivel_estimado' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ]);

            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key(['tecnico_id', 'data'], TRUE, 'uk_tecnico_dia');
            $this->dbforge->add_key('data');
            $this->dbforge->create_table('tec_rotas_tracking', TRUE, ['ENGINE' => 'InnoDB']);
        }
    }

    private function criar_tabela_obras()
    {
        if (!$this->db->table_exists('obras')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'codigo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE,
                    'unique' => TRUE
                ],
                'nome' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => FALSE
                ],
                'cliente_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'tipo_obra' => [
                    'type' => "ENUM('Condominio','Comercio','Residencia','Industrial','Publica')",
                    'default' => 'Condominio'
                ],
                'especialidade_principal' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE
                ],
                'endereco' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'bairro' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE
                ],
                'cidade' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE
                ],
                'estado' => [
                    'type' => 'VARCHAR',
                    'constraint' => 2,
                    'null' => TRUE
                ],
                'cep' => [
                    'type' => 'VARCHAR',
                    'constraint' => 9,
                    'null' => TRUE
                ],
                'coordenadas_lat' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => TRUE
                ],
                'coordenadas_lng' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => TRUE
                ],
                'data_inicio_contrato' => [
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
                'prazo_dias' => [
                    'type' => 'INT',
                    'null' => TRUE
                ],
                'status' => [
                    'type' => "ENUM('Prospeccao','Orcamentacao','Contratada','EmExecucao','Paralisada','Finalizada','Entregue','Garantia')",
                    'default' => 'Prospeccao'
                ],
                'percentual_concluido' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'gestor_obra_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'responsavel_tecnico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'responsavel_comercial_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'contrato_arquivo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'projeto_arquivo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'art_arquivo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'memorial_descritivo' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'observacoes' => [
                    'type' => 'TEXT',
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
            $this->dbforge->add_key('cliente_id');
            $this->dbforge->add_key('status');
            $this->dbforge->add_key('gestor_obra_id');
            $this->dbforge->create_table('obras', TRUE, ['ENGINE' => 'InnoDB']);
        }
    }

    private function criar_tabela_obra_etapas()
    {
        if (!$this->db->table_exists('obra_etapas')) {
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
                'numero_etapa' => [
                    'type' => 'INT',
                    'default' => 1
                ],
                'nome' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => FALSE
                ],
                'descricao' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'especialidade' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE
                ],
                'data_inicio_prevista' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'data_fim_prevista' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'data_inicio_real' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'data_fim_real' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'percentual_concluido' => [
                    'type' => 'INT',
                    'default' => 0
                ],
                'status' => [
                    'type' => "ENUM('NaoIniciada','EmAndamento','Concluida','Atrasada','Paralisada')",
                    'default' => 'NaoIniciada'
                ],
                'tecnicos_designados' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'os_ids' => [
                    'type' => 'JSON',
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
            $this->dbforge->add_key('status');
            $this->dbforge->create_table('obra_etapas', TRUE, ['ENGINE' => 'InnoDB']);
        }
    }

    private function criar_tabela_obra_diario()
    {
        if (!$this->db->table_exists('obra_diario')) {
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
                'data' => [
                    'type' => 'DATE',
                    'null' => FALSE
                ],
                'clima_manha' => [
                    'type' => "ENUM('Sol','Nublado','Chuva','Garoa')",
                    'null' => TRUE
                ],
                'clima_tarde' => [
                    'type' => "ENUM('Sol','Nublado','Chuva','Garoa')",
                    'null' => TRUE
                ],
                'equipe_presente' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'atividades_executadas' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'etapas_avancadas' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'fotos' => [
                    'type' => 'JSON',
                    'null' => TRUE
                ],
                'problemas' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'acoes_corretivas' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'material_recebido' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'material_consumido' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'visitas_cliente' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'visitas_fiscalizacao' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'preenchido_por' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ],
                'preenchido_em' => [
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
            $this->dbforge->add_key(['obra_id', 'data'], TRUE, 'uk_obra_data');
            $this->dbforge->add_key('data');
            $this->dbforge->add_key('preenchido_por');
            $this->dbforge->create_table('obra_diario', TRUE, ['ENGINE' => 'InnoDB']);
        }
    }

    private function inserir_dados_iniciais()
    {
        // Catálogo de serviços
        $servicos = [
            [
                'codigo' => 'SRV-CFTV-001',
                'nome' => 'Instalação de Câmeras',
                'descricao' => 'Instalação completa de câmeras de segurança',
                'categoria' => 'CFTV',
                'especialidade' => 'CFTV',
                'tempo_estimado_minutos' => 90,
                'checklist_padrao' => json_encode([
                    ['ordem' => 1, 'desc' => 'Verificar posição com cliente'],
                    ['ordem' => 2, 'desc' => 'Instalar suporte'],
                    ['ordem' => 3, 'desc' => 'Conectar cabos'],
                    ['ordem' => 4, 'desc' => 'Ajustar ângulo'],
                    ['ordem' => 5, 'desc' => 'Testar imagem']
                ]),
                'ativo' => 1
            ],
            [
                'codigo' => 'SRV-CFTV-002',
                'nome' => 'Configuração de Sistema CFTV',
                'descricao' => 'Configuração de gravadores e acesso remoto',
                'categoria' => 'CFTV',
                'especialidade' => 'CFTV',
                'tempo_estimado_minutos' => 60,
                'checklist_padrao' => json_encode([
                    ['ordem' => 1, 'desc' => 'Configurar rede'],
                    ['ordem' => 2, 'desc' => 'Configurar gravação'],
                    ['ordem' => 3, 'desc' => 'Configurar app cliente'],
                    ['ordem' => 4, 'desc' => 'Testar acesso remoto']
                ]),
                'ativo' => 1
            ],
            [
                'codigo' => 'SRV-CFTV-003',
                'nome' => 'Manutenção Preventiva CFTV',
                'descricao' => 'Limpeza e verificação de sistema CFTV',
                'categoria' => 'CFTV',
                'especialidade' => 'CFTV',
                'tempo_estimado_minutos' => 45,
                'checklist_padrao' => json_encode([
                    ['ordem' => 1, 'desc' => 'Limpar lentes'],
                    ['ordem' => 2, 'desc' => 'Verificar conexões'],
                    ['ordem' => 3, 'desc' => 'Testar gravação'],
                    ['ordem' => 4, 'desc' => 'Verificar espaço em disco']
                ]),
                'ativo' => 1
            ],
            [
                'codigo' => 'SRV-ALM-001',
                'nome' => 'Instalação de Alarme',
                'descricao' => 'Instalação de sensores e central de alarme',
                'categoria' => 'Alarmes',
                'especialidade' => 'Alarmes',
                'tempo_estimado_minutos' => 120,
                'checklist_padrao' => json_encode([
                    ['ordem' => 1, 'desc' => 'Instalar sensores'],
                    ['ordem' => 2, 'desc' => 'Instalar sirene'],
                    ['ordem' => 3, 'desc' => 'Programar zonas'],
                    ['ordem' => 4, 'desc' => 'Testar disparo']
                ]),
                'ativo' => 1
            ],
            [
                'codigo' => 'SRV-RED-001',
                'nome' => 'Passagem de Cabos de Rede',
                'descricao' => 'Passagem e organização de cabos estruturados',
                'categoria' => 'Redes',
                'especialidade' => 'Redes',
                'tempo_estimado_minutos' => 90,
                'checklist_padrao' => json_encode([
                    ['ordem' => 1, 'desc' => 'Identificar pontos'],
                    ['ordem' => 2, 'desc' => 'Passar cabos'],
                    ['ordem' => 3, 'desc' => 'Crimpagem'],
                    ['ordem' => 4, 'desc' => 'Testar conectividade']
                ]),
                'ativo' => 1
            ],
            [
                'codigo' => 'SRV-ACE-001',
                'nome' => 'Instalação de Controle de Acesso',
                'descricao' => 'Instalação de catracas/leitores/fechaduras',
                'categoria' => 'ControleAcesso',
                'especialidade' => 'ControleAcesso',
                'tempo_estimado_minutos' => 180,
                'checklist_padrao' => json_encode([
                    ['ordem' => 1, 'desc' => 'Instalar equipamentos'],
                    ['ordem' => 2, 'desc' => 'Ligar elétrica'],
                    ['ordem' => 3, 'desc' => 'Configurar usuários'],
                    ['ordem' => 4, 'desc' => 'Testar liberações']
                ]),
                'ativo' => 1
            ]
        ];

        foreach ($servicos as $servico) {
            // Verificar se já existe
            $exists = $this->db
                ->where('codigo', $servico['codigo'])
                ->get('servicos_catalogo')
                ->row();

            if (!$exists) {
                $this->db->insert('servicos_catalogo', $servico);
            }
        }

        // Checklists templates
        $templates = [
            [
                'tipo_os' => 'CFTV',
                'tipo_servico' => 'INS',
                'nome_template' => 'Instalação CFTV Padrão',
                'itens' => json_encode([
                    ['ordem' => 1, 'desc' => 'Verificar integridade dos equipamentos', 'obrigatorio' => true],
                    ['ordem' => 2, 'desc' => 'Definir posições das câmeras com cliente', 'obrigatorio' => true],
                    ['ordem' => 3, 'desc' => 'Tirar foto do local antes', 'obrigatorio' => true],
                    ['ordem' => 4, 'desc' => 'Instalar suportes', 'obrigatorio' => true],
                    ['ordem' => 5, 'desc' => 'Passar cabeamento', 'obrigatorio' => true],
                    ['ordem' => 6, 'desc' => 'Conectar câmeras', 'obrigatorio' => true],
                    ['ordem' => 7, 'desc' => 'Configurar gravação', 'obrigatorio' => true],
                    ['ordem' => 8, 'desc' => 'Testar acesso remoto', 'obrigatorio' => true],
                    ['ordem' => 9, 'desc' => 'Orientar cliente', 'obrigatorio' => true]
                ]),
                'ativo' => 1
            ],
            [
                'tipo_os' => 'CFTV',
                'tipo_servico' => 'MP',
                'nome_template' => 'Manutenção CFTV Padrão',
                'itens' => json_encode([
                    ['ordem' => 1, 'desc' => 'Verificar funcionamento das câmeras', 'obrigatorio' => true],
                    ['ordem' => 2, 'desc' => 'Limpar lentes', 'obrigatorio' => true],
                    ['ordem' => 3, 'desc' => 'Verificar conexões', 'obrigatorio' => true],
                    ['ordem' => 4, 'desc' => 'Verificar espaço em disco', 'obrigatorio' => true],
                    ['ordem' => 5, 'desc' => 'Testar gravação', 'obrigatorio' => true]
                ]),
                'ativo' => 1
            ],
            [
                'tipo_os' => 'Alarme',
                'tipo_servico' => 'INS',
                'nome_template' => 'Instalação de Alarme',
                'itens' => json_encode([
                    ['ordem' => 1, 'desc' => 'Verificar equipamentos'],
                    ['ordem' => 2, 'desc' => 'Instalar sensores'],
                    ['ordem' => 3, 'desc' => 'Instalar sirene'],
                    ['ordem' => 4, 'desc' => 'Programar central'],
                    ['ordem' => 5, 'desc' => 'Testar disparo']
                ]),
                'ativo' => 1
            ],
            [
                'tipo_os' => 'Rede',
                'tipo_servico' => 'INS',
                'nome_template' => 'Passagem de Cabos',
                'itens' => json_encode([
                    ['ordem' => 1, 'desc' => 'Identificar pontos'],
                    ['ordem' => 2, 'desc' => 'Passar cabos'],
                    ['ordem' => 3, 'desc' => 'Instalar tomadas'],
                    ['ordem' => 4, 'desc' => 'Testar conectividade']
                ]),
                'ativo' => 1
            ]
        ];

        foreach ($templates as $template) {
            $exists = $this->db
                ->where('tipo_os', $template['tipo_os'])
                ->where('tipo_servico', $template['tipo_servico'])
                ->get('tec_checklist_template')
                ->row();

            if (!$exists) {
                $this->db->insert('tec_checklist_template', $template);
            }
        }
    }

    public function down()
    {
        // Remover tabelas na ordem inversa (respeitar foreign keys)
        $tabelas = [
            'obra_diario',
            'obra_etapas',
            'obras',
            'tec_rotas_tracking',
            'tec_estoque_veiculo',
            'tec_checklist_template',
            'tec_os_execucao',
            'os_servicos',
            'servicos_catalogo'
        ];

        foreach ($tabelas as $tabela) {
            if ($this->db->table_exists($tabela)) {
                $this->dbforge->drop_table($tabela);
            }
        }

        // Remover campos de usuarios
        $campos = [
            'is_tecnico',
            'nivel_tecnico',
            'especialidades',
            'veiculo_placa',
            'veiculo_tipo',
            'coordenadas_base_lat',
            'coordenadas_base_lng',
            'raio_atuacao_km',
            'plantao_24h',
            'app_tecnico_instalado',
            'token_app',
            'token_expira',
            'ultimo_acesso_app',
            'foto_tecnico'
        ];

        foreach ($campos as $campo) {
            if ($this->db->field_exists($campo, 'usuarios')) {
                $this->dbforge->drop_column('usuarios', $campo);
            }
        }
    }
}
