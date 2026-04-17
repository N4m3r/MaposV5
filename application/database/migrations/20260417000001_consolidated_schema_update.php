<?php
/**
 * Migration Consolidada - MapOS V5
 *
 * Esta migration unica traz qualquer banco de dados existente
 * ao schema atual do MapOS V5. Todos os IF NOT EXISTS / field_exists
 * garantem que é segura de executar múltiplas vezes.
 *
 * Para instalações novas, use banco.sql (já contém tudo).
 * Para upgrades de bancos existentes, execute index.php/migrate.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Consolidated_schema_update extends CI_Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

        // ========================================================
        // 1. ADICIONAR COLUNAS AUSENTES EM TABELAS EXISTENTES
        // ========================================================
        $this->_addMissingColumns();

        // ========================================================
        // 2. CRIAR TABELAS AUSENTES
        // ========================================================
        $this->_createMissingTables();

        // ========================================================
        // 3. INSERIR DADOS PADRÃO AUSENTES
        // ========================================================
        $this->_insertMissingData();

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        log_message('info', 'Migration consolidada executada com sucesso.');
    }

    public function down()
    {
        // Não faz rollback para proteger dados existentes
        log_message('info', 'Migration consolidada: rollback desabilitado para proteger dados.');
    }

    // ============================================================
    // HELPER: Adiciona coluna se não existir
    // ============================================================
    private function _addColumnIfNotExists($table, $column, $definition)
    {
        if (!$this->db->field_exists($column, $table)) {
            $this->dbforge->add_column($table, [$column => $definition]);
            log_message('info', "Coluna {$column} adicionada à tabela {$table}");
        }
    }

    // ============================================================
    // HELPER: Cria tabela se não existir
    // ============================================================
    private function _createTableIfNotExists($table, $fields, $keys = [])
    {
        if (!$this->db->table_exists($table)) {
            $this->dbforge->add_field($fields);
            foreach ($keys as $key) {
                if (is_array($key)) {
                    $this->dbforge->add_key($key);
                } else {
                    $this->dbforge->add_key($key, true);
                }
            }
            $this->dbforge->create_table($table, true);
            log_message('info', "Tabela {$table} criada");
        }
    }

    // ============================================================
    // 1. ADICIONAR COLUNAS AUSENTES
    // ============================================================
    private function _addMissingColumns()
    {
        // --- cobrancas ---
        if ($this->db->table_exists('cobrancas')) {
            $this->_addColumnIfNotExists('cobrancas', 'linha_digitavel', [
                'type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'barcode'
            ]);
            $this->_addColumnIfNotExists('cobrancas', 'pix_code', [
                'type' => 'TEXT', 'null' => true, 'after' => 'link'
            ]);
            $this->_addColumnIfNotExists('cobrancas', 'paid_at', [
                'type' => 'DATETIME', 'null' => true, 'after' => 'expire_at'
            ]);
            $this->_addColumnIfNotExists('cobrancas', 'updated_at', [
                'type' => 'DATETIME', 'null' => true, 'after' => 'created_at'
            ]);

            // Aumentar campo message para TEXT
            $field = $this->db->field_exists('message', 'cobrancas');
            if ($field) {
                $row = $this->db->query("SHOW COLUMNS FROM cobrancas LIKE 'message'")->row();
                if ($row && stripos($row->Type, 'text') === false) {
                    $this->db->query("ALTER TABLE cobrancas MODIFY COLUMN message TEXT NULL DEFAULT NULL");
                }
            }

            // Índices
            $this->_safeCreateIndex('cobrancas', 'idx_cobrancas_charge_id', 'charge_id');
            $this->_safeCreateIndex('cobrancas', 'idx_cobrancas_status_gateway', 'status, payment_gateway');
        }

        // --- os ---
        if ($this->db->table_exists('os')) {
            $this->_addColumnIfNotExists('os', 'tecnico_responsavel', [
                'type' => 'INT', 'constraint' => 11, 'null' => true,
                'comment' => 'ID do usuario tecnico responsavel pela OS'
            ]);
            $this->_addColumnIfNotExists('os', 'nfse_status', [
                'type' => "ENUM('Pendente','Emitida','Cancelada')", 'default' => 'Pendente',
                'comment' => 'Status da NFS-e vinculada'
            ]);
            $this->_addColumnIfNotExists('os', 'boleto_status', [
                'type' => "ENUM('Pendente','Emitido','Pago','Vencido','Cancelado')", 'default' => 'Pendente',
                'comment' => 'Status do boleto vinculado'
            ]);
            $this->_addColumnIfNotExists('os', 'data_vencimento_boleto', [
                'type' => 'DATE', 'null' => true,
                'comment' => 'Data de vencimento do boleto'
            ]);
            $this->_addColumnIfNotExists('os', 'valor_com_impostos', [
                'type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true,
                'comment' => 'Valor liquido apos deducao de impostos'
            ]);
            $this->_addColumnIfNotExists('os', 'certificado_vinculado', [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true,
                'after' => 'garantia'
            ]);
            $this->_addColumnIfNotExists('os', 'retencao_impostos', [
                'type' => 'TINYINT', 'constraint' => 1, 'default' => 0
            ]);
            $this->_addColumnIfNotExists('os', 'calculo_impostos', [
                'type' => 'TEXT', 'null' => true,
                'comment' => 'JSON com detalhes dos impostos calculados'
            ]);

            $this->_safeCreateIndex('os', 'idx_tecnico_responsavel', 'tecnico_responsavel');
        }

        // --- lancamentos ---
        if ($this->db->table_exists('lancamentos')) {
            $this->_addColumnIfNotExists('lancamentos', 'observacoes', [
                'type' => 'TEXT', 'null' => true
            ]);
            $this->_addColumnIfNotExists('lancamentos', 'webhook_notificado', [
                'type' => 'TINYINT', 'constraint' => 1, 'default' => 0
            ]);
        }

        // --- usuarios ---
        if ($this->db->table_exists('usuarios')) {
            $fields = $this->db->list_fields('usuarios');
            $tecFields = [
                'is_tecnico' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'comment' => 'Indica se é técnico de campo'],
                'nivel_tecnico' => ['type' => "ENUM('I','II','III','IV')", 'default' => 'II', 'comment' => 'Nível do técnico'],
                'especialidades' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'veiculo_placa' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'veiculo_tipo' => ['type' => "ENUM('Moto','Carro','Nenhum')", 'default' => 'Nenhum'],
                'coordenadas_base_lat' => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
                'coordenadas_base_lng' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
                'raio_atuacao_km' => ['type' => 'INT', 'default' => 50],
                'plantao_24h' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'app_tecnico_instalado' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'token_app' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'token_expira' => ['type' => 'DATETIME', 'null' => true],
                'ultimo_acesso_app' => ['type' => 'DATETIME', 'null' => true],
                'foto_tecnico' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            ];
            foreach ($tecFields as $col => $def) {
                if (!in_array($col, $fields)) {
                    $this->dbforge->add_column('usuarios', [$col => $def]);
                }
            }
        }

        // --- clientes ---
        if ($this->db->table_exists('clientes')) {
            $this->_addColumnIfNotExists('clientes', 'contato', [
                'type' => 'VARCHAR', 'constraint' => 45, 'null' => true
            ]);
            $this->_addColumnIfNotExists('clientes', 'complemento', [
                'type' => 'VARCHAR', 'constraint' => 45, 'null' => true
            ]);
            $this->_addColumnIfNotExists('clientes', 'fornecedor', [
                'type' => 'BOOLEAN', 'default' => 0
            ]);
            $this->_addColumnIfNotExists('clientes', 'senha', [
                'type' => 'VARCHAR', 'constraint' => 200, 'null' => true
            ]);
            $this->_addColumnIfNotExists('clientes', 'asaas_id', [
                'type' => 'VARCHAR', 'constraint' => 255, 'null' => true
            ]);
        }

        // --- vendas ---
        if ($this->db->table_exists('vendas')) {
            $this->_addColumnIfNotExists('vendas', 'observacoes', [
                'type' => 'TEXT', 'null' => true
            ]);
            $this->_addColumnIfNotExists('vendas', 'observacoes_cliente', [
                'type' => 'TEXT', 'null' => true
            ]);
            $this->_addColumnIfNotExists('vendas', 'garantia', [
                'type' => 'VARCHAR', 'constraint' => 45, 'null' => true
            ]);
            $this->_addColumnIfNotExists('vendas', 'status', [
                'type' => 'VARCHAR', 'constraint' => 45, 'null' => true
            ]);
        }

        // --- emitente ---
        if ($this->db->table_exists('emitente')) {
            $this->_addColumnIfNotExists('emitente', 'cep', [
                'type' => 'VARCHAR', 'constraint' => 20, 'null' => true
            ]);
        }

        // --- fotos_atendimento ---
        if ($this->db->table_exists('fotos_atendimento')) {
            $this->_addColumnIfNotExists('fotos_atendimento', 'imagem_base64', [
                'type' => 'LONGTEXT', 'null' => true
            ]);
            $this->_addColumnIfNotExists('fotos_atendimento', 'mime_type', [
                'type' => 'VARCHAR', 'constraint' => 30, 'null' => true
            ]);
        }

        // --- configuracoes - garantir campo config como VARCHAR(20) utf8mb4 ---
        if ($this->db->table_exists('configuracoes')) {
            $row = $this->db->query("SHOW COLUMNS FROM configuracoes LIKE 'config'")->row();
            if ($row && stripos($row->Type, 'text') !== false) {
                $this->db->query("ALTER TABLE configuracoes MODIFY COLUMN `config` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL");
            }
        }
    }

    // ============================================================
    // 2. CRIAR TABELAS AUSENTES
    // ============================================================
    private function _createMissingTables()
    {
        // --- cobrancas ---
        $this->_createTableIfNotExists('cobrancas', [
            'idCobranca' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'charge_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'conditional_discount_date' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'custom_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'expire_at' => ['type' => 'DATE', 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'message' => ['type' => 'TEXT', 'null' => true],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 11, 'null' => true],
            'payment_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'request_delivery_address' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 36, 'null' => true],
            'total' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'barcode' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'linha_digitavel' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'link' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'pix_code' => ['type' => 'TEXT', 'null' => true],
            'payment_gateway' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'pdf' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'vendas_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'clientes_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ], ['idCobranca']);

        // --- resets_de_senha ---
        $this->_createTableIfNotExists('resets_de_senha', [
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 200],
            'token' => ['type' => 'VARCHAR', 'constraint' => 255],
            'data_expiracao' => ['type' => 'DATETIME'],
            'token_utilizado' => ['type' => 'TINYINT'],
        ], ['id']);

        // --- email_queue ---
        $this->_createTableIfNotExists('email_queue', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'to_email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'to_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'subject' => ['type' => 'VARCHAR', 'constraint' => 500],
            'body_html' => ['type' => 'LONGTEXT', 'null' => true],
            'body_text' => ['type' => 'LONGTEXT', 'null' => true],
            'template' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'template_data' => ['type' => 'TEXT', 'null' => true],
            'attachments' => ['type' => 'TEXT', 'null' => true],
            'priority' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 3],
            'status' => ['type' => "ENUM('pending','processing','sent','failed','cancelled','scheduled')", 'default' => 'pending'],
            'attempts' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'max_retries' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 3],
            'tracking_id' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'message_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'scheduled_at' => ['type' => 'DATETIME', 'null' => true],
            'sent_at' => ['type' => 'DATETIME', 'null' => true],
            'opened_at' => ['type' => 'DATETIME', 'null' => true],
            'clicked_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
            'last_attempt' => ['type' => 'DATETIME', 'null' => true],
            'failed_at' => ['type' => 'DATETIME', 'null' => true],
            'error_message' => ['type' => 'TEXT', 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
        ], ['id']);

        // --- email_tracking ---
        $this->_createTableIfNotExists('email_tracking', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email_queue_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tracking_id' => ['type' => 'VARCHAR', 'constraint' => 64],
            'opened' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'opened_at' => ['type' => 'DATETIME', 'null' => true],
            'clicked' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'clicked_at' => ['type' => 'DATETIME', 'null' => true],
            'clicked_url' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- email_clicks ---
        $this->_createTableIfNotExists('email_clicks', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tracking_id' => ['type' => 'VARCHAR', 'constraint' => 32],
            'url' => ['type' => 'TEXT'],
            'clicked_at' => ['type' => 'DATETIME'],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
        ], ['id']);

        // --- scheduled_events ---
        $this->_createTableIfNotExists('scheduled_events', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'event_type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'event_data' => ['type' => 'JSON', 'null' => true],
            'execute_at' => ['type' => 'DATETIME'],
            'status' => ['type' => "ENUM('pending','completed','failed')", 'default' => 'pending'],
            'executed_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- webhooks ---
        $this->_createTableIfNotExists('webhooks', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'url' => ['type' => 'VARCHAR', 'constraint' => 500],
            'secret' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'events' => ['type' => 'JSON', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- webhook_logs ---
        $this->_createTableIfNotExists('webhook_logs', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'webhook_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'event_type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'payload' => ['type' => 'TEXT', 'null' => true],
            'response' => ['type' => 'TEXT', 'null' => true],
            'status_code' => ['type' => 'INT', 'null' => true],
            'success' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- certificado_config ---
        $this->_createTableIfNotExists('certificado_config', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_emitente' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'certificado_p12' => ['type' => 'LONGTEXT', 'null' => true],
            'senha_certificado' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'cnpj_certificado' => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
            'valido_de' => ['type' => 'DATETIME', 'null' => true],
            'valido_ate' => ['type' => 'DATETIME', 'null' => true],
            'arquivo_crt' => ['type' => 'LONGTEXT', 'null' => true],
            'arquivo_key' => ['type' => 'LONGTEXT', 'null' => true],
            'ambiente' => ['type' => "ENUM('homologacao','producao')", 'default' => 'homologacao'],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- nfse_importada ---
        $this->_createTableIfNotExists('nfse_importada', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_os' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'numero_nfse' => ['type' => 'VARCHAR', 'constraint' => 50],
            'codigo_verificacao' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'data_emissao' => ['type' => 'DATETIME', 'null' => true],
            'valor_servico' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'valor_liquido' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'prestador_cnpj' => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
            'prestador_nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tomador_cnpj' => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
            'tomador_nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status' => ['type' => "ENUM('ativa','cancelada')", 'default' => 'ativa'],
            'xml_content' => ['type' => 'LONGTEXT', 'null' => true],
            'pdf_content' => ['type' => 'LONGTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- push_notifications ---
        $this->_createTableIfNotExists('push_notifications', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'message' => ['type' => 'TEXT'],
            'data' => ['type' => 'JSON', 'null' => true],
            'is_read' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- checkin (simplificado) ---
        $this->_createTableIfNotExists('checkin', [
            'idCheckin' => ['type' => 'INT', 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
            'tecnico_id' => ['type' => 'INT', 'constraint' => 11],
            'tipo' => ['type' => "ENUM('inicio','pausa','retorno','finalizacao','checkin','checkout')"],
            'data_hora' => ['type' => 'DATETIME'],
            'observacao' => ['type' => 'TEXT', 'null' => true],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'latitude' => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'longitude' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'localizacao' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ], ['idCheckin']);

        // --- os_checkin (detalhado) ---
        $this->_createTableIfNotExists('os_checkin', [
            'idCheckin' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
            'usuarios_id' => ['type' => 'INT', 'constraint' => 11],
            'data_entrada' => ['type' => 'DATETIME', 'null' => true],
            'data_saida' => ['type' => 'DATETIME', 'null' => true],
            'latitude_entrada' => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'longitude_entrada' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'latitude_saida' => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'longitude_saida' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'observacao_entrada' => ['type' => 'TEXT', 'null' => true],
            'observacao_saida' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'Em Andamento'],
            'data_cadastro' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'data_atualizacao' => ['type' => 'DATETIME', 'null' => true],
        ], ['idCheckin']);

        // --- os_assinaturas ---
        $this->_createTableIfNotExists('os_assinaturas', [
            'idAssinatura' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
            'checkin_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'tipo' => ['type' => 'VARCHAR', 'constraint' => 20, 'comment' => 'tecnico_entrada, tecnico_saida, cliente_saida'],
            'assinatura' => ['type' => 'VARCHAR', 'constraint' => 255, 'comment' => 'Caminho da imagem da assinatura'],
            'nome_assinante' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'documento_assinante' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'data_assinatura' => ['type' => 'DATETIME'],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'data_cadastro' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ], ['idAssinatura']);

        // --- os_fotos_atendimento ---
        $this->_createTableIfNotExists('os_fotos_atendimento', [
            'idFoto' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
            'checkin_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'usuarios_id' => ['type' => 'INT', 'constraint' => 11],
            'arquivo' => ['type' => 'VARCHAR', 'constraint' => 255],
            'path' => ['type' => 'VARCHAR', 'constraint' => 255],
            'url' => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'etapa' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'durante'],
            'tamanho' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'tipo_arquivo' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'data_upload' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ], ['idFoto']);

        // --- fotos_atendimento (LONGBLOB) ---
        $this->_createTableIfNotExists('fotos_atendimento', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'checkin_id' => ['type' => 'INT', 'constraint' => 11],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
            'imagem' => ['type' => 'LONGBLOB'],
            'imagem_base64' => ['type' => 'LONGTEXT', 'null' => true],
            'mime_type' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'tipo' => ['type' => "ENUM('antes','depois','assinatura','outro')", 'default' => 'outro'],
            'data' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- os_tecnico_atribuicao ---
        $this->_createTableIfNotExists('os_tecnico_atribuicao', [
            'idAtribuicao' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11, 'comment' => 'ID da OS'],
            'tecnico_id' => ['type' => 'INT', 'constraint' => 11, 'comment' => 'ID do tecnico atribuido'],
            'atribuido_por' => ['type' => 'INT', 'constraint' => 11, 'comment' => 'ID do usuario que fez a atribuicao'],
            'data_atribuicao' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'data_remocao' => ['type' => 'DATETIME', 'null' => true],
            'motivo_remocao' => ['type' => 'TEXT', 'null' => true],
            'observacao' => ['type' => 'TEXT', 'null' => true],
        ], ['idAtribuicao']);

        // --- os_status_history ---
        $this->_createTableIfNotExists('os_status_history', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
            'status_antigo' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'status_novo' => ['type' => 'VARCHAR', 'constraint' => 45],
            'usuario_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'observacao' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- anotacoes_os ---
        $this->_createTableIfNotExists('anotacoes_os', [
            'idAnotacoes' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'anotacao' => ['type' => 'VARCHAR', 'constraint' => 255],
            'data_hora' => ['type' => 'DATETIME'],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
        ], ['idAnotacoes']);

        // --- os_nfse_emitida ---
        $this->_createTableIfNotExists('os_nfse_emitida', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11, 'comment' => 'ID da OS vinculada'],
            'numero_nfse' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'chave_acesso' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'data_emissao' => ['type' => 'DATETIME', 'null' => true],
            'valor_servicos' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_deducoes' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_liquido' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'aliquota_iss' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
            'valor_iss' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_inss' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_irrf' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_csll' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_pis' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_cofins' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_total_impostos' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'situacao' => ['type' => "ENUM('Pendente','Emitida','Cancelada','Substituida')", 'default' => 'Pendente'],
            'codigo_verificacao' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'link_impressao' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'xml_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'protocolo' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'mensagem_retorno' => ['type' => 'TEXT', 'null' => true],
            'cobranca_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'emitido_por' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ], ['id']);

        // --- os_boleto_emitido ---
        $this->_createTableIfNotExists('os_boleto_emitido', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
            'nfse_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nosso_numero' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'linha_digitavel' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'codigo_barras' => ['type' => 'VARCHAR', 'constraint' => 44, 'null' => true],
            'data_emissao' => ['type' => 'DATE', 'null' => true],
            'data_vencimento' => ['type' => 'DATE', 'null' => true],
            'data_pagamento' => ['type' => 'DATE', 'null' => true],
            'valor_original' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_desconto_impostos' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_liquido' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'valor_pago' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'multa' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'juros' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'status' => ['type' => "ENUM('Pendente','Emitido','Pago','Vencido','Cancelado')", 'default' => 'Pendente'],
            'instrucoes' => ['type' => 'TEXT', 'null' => true],
            'sacado_nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sacado_documento' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'sacado_endereco' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'pdf_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'remessa_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'retorno_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'gateway' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'gateway_transaction_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ], ['id']);

        // --- dre_contas ---
        $this->_createTableIfNotExists('dre_contas', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'codigo' => ['type' => 'VARCHAR', 'constraint' => 50],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255],
            'tipo' => ['type' => "ENUM('receita','custo','despesa')"],
            'categoria' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'ordem' => ['type' => 'INT', 'default' => 0],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- dre_lancamentos ---
        $this->_createTableIfNotExists('dre_lancamentos', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'conta_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'data_referencia' => ['type' => 'DATE'],
            'valor' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'id_os' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'id_venda' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'id_lancamento' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- dre_demonstracoes ---
        $this->_createTableIfNotExists('dre_demonstracoes', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'data_inicio' => ['type' => 'DATE'],
            'data_fim' => ['type' => 'DATE'],
            'tipo' => ['type' => "ENUM('mensal','trimestral','anual')", 'default' => 'mensal'],
            'status' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ], ['id']);

        // --- impostos_config ---
        $this->_createTableIfNotExists('impostos_config', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tipo_regime' => ['type' => "ENUM('simples_nacional','lucro_presumido','lucro_real')", 'default' => 'simples_nacional'],
            'anexo_simples' => ['type' => "ENUM('i','ii','iii','iv','v')", 'null' => true],
            'aliquota_iss' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'retem_iss' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'aliquota_pis' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'aliquota_cofins' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'aliquota_csll' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'aliquota_ir' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'aliquota_inss' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- impostos_retidos ---
        $this->_createTableIfNotExists('impostos_retidos', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_os' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'id_venda' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tipo_imposto' => ['type' => "ENUM('iss','pis','cofins','csll','ir','inss')"],
            'base_calculo' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'aliquota' => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'valor_retido' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'created_at' => ['type' => 'DATETIME'],
        ], ['id']);

        // --- configuracoes_impostos ---
        $this->_createTableIfNotExists('configuracoes_impostos', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'cnpj' => ['type' => 'VARCHAR', 'constraint' => 18],
            'razao_social' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'anexo_simples' => ['type' => "ENUM('I','II','III','IV','V')", 'default' => 'III'],
            'faixa_simples' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'aliquota_simples' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '6.00'],
            'retencao_iss' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'aliquota_iss' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '2.00'],
            'retencao_pis' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'aliquota_pis' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.65'],
            'retencao_cofins' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'aliquota_cofins' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '3.00'],
            'retencao_csll' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'aliquota_csll' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '1.00'],
            'retencao_inss' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'aliquota_inss' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '11.00'],
            'retencao_ir' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'aliquota_ir' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '1.50'],
            'valor_minimo_retencao' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'ativar_retencao_automatica' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ], ['id']);

        // --- calculos_impostos ---
        $this->_createTableIfNotExists('calculos_impostos', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'cnpj' => ['type' => 'VARCHAR', 'constraint' => 18],
            'os_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'venda_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'cobranca_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'valor_bruto' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'valor_liquido' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'iss' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'pis' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'cofins' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'csll' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'inss' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'ir' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'total_impostos' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'aliquota_efetiva' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'competencia' => ['type' => 'DATE'],
            'status' => ['type' => "ENUM('calculado','retido','recolhido','cancelado')", 'default' => 'calculado'],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ], ['id']);

        // --- usuarios_cliente ---
        $this->_createTableIfNotExists('usuarios_cliente', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'cliente_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'comment' => 'ID do cliente vinculado'],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'senha' => ['type' => 'VARCHAR', 'constraint' => 255],
            'telefone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'celular' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'ultimo_acesso' => ['type' => 'DATETIME', 'null' => true],
            'token_reset' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'token_expira' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ], ['id']);

        // --- usuarios_cliente_cnpjs ---
        $this->_createTableIfNotExists('usuarios_cliente_cnpjs', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'usuario_cliente_id' => ['type' => 'INT', 'constraint' => 11],
            'cnpj' => ['type' => 'VARCHAR', 'constraint' => 18, 'comment' => 'CNPJ formato 00.000.000/0000-00'],
            'razao_social' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nome_fantasia' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'principal' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ], ['id']);

        // --- usuarios_cliente_permissoes ---
        $this->_createTableIfNotExists('usuarios_cliente_permissoes', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'usuario_cliente_id' => ['type' => 'INT', 'constraint' => 11],
            'chave' => ['type' => 'VARCHAR', 'constraint' => 100, 'comment' => 'Nome da permissao/configuracao'],
            'valor' => ['type' => 'TEXT', 'null' => true, 'comment' => 'Valor da configuracao'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ], ['id']);

        // --- os_documentos ---
        $this->_createTableIfNotExists('os_documentos', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tipo' => ['type' => "ENUM('boleto','nfse','nfe','nfce','recibo','contrato','outro')"],
            'descricao' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'numero_documento' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'data_emissao' => ['type' => 'DATE', 'null' => true],
            'data_vencimento' => ['type' => 'DATE', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'arquivo' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'link_externo' => ['type' => 'TEXT', 'null' => true],
            'gateway_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'charge_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nfse_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ], ['id']);

        // --- Checklist tables ---
        $this->_createTableIfNotExists('checklist_templates', [
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 100],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'categoria' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'geral'],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('checklist_template_items', [
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'template_id' => ['type' => 'INT'],
            'ordem' => ['type' => 'INT', 'default' => 0],
            'descricao' => ['type' => 'VARCHAR', 'constraint' => 255],
            'tipo' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'checkbox'],
            'obrigatorio' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'opcoes' => ['type' => 'TEXT', 'null' => true],
        ], ['id']);

        $this->_createTableIfNotExists('os_checklist', [
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'os_id' => ['type' => 'INT'],
            'template_id' => ['type' => 'INT', 'null' => true],
            'item_id' => ['type' => 'INT'],
            'descricao' => ['type' => 'VARCHAR', 'constraint' => 255],
            'status' => ['type' => "ENUM('pendente','ok','nao_aplicavel','com_problema')", 'default' => 'pendente'],
            'observacao' => ['type' => 'TEXT', 'null' => true],
            'evidencia_foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'verificado_por' => ['type' => 'INT', 'null' => true],
            'verificado_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('os_timeline', [
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'os_id' => ['type' => 'INT'],
            'tipo' => ['type' => 'VARCHAR', 'constraint' => 50],
            'titulo' => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'usuario_id' => ['type' => 'INT', 'null' => true],
            'usuario_nome' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'metadata' => ['type' => 'JSON', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('os_pecas_utilizadas', [
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'os_id' => ['type' => 'INT'],
            'produto_id' => ['type' => 'INT', 'null' => true],
            'nome_peca' => ['type' => 'VARCHAR', 'constraint' => 255],
            'codigo' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'quantidade' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '1'],
            'valor_unitario' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0'],
            'valor_total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0'],
            'tipo' => ['type' => "ENUM('produto','servico','insumo','outro')", 'default' => 'produto'],
            'instalado_por' => ['type' => 'INT', 'null' => true],
            'instalado_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'garantia_dias' => ['type' => 'INT', 'default' => 0],
            'observacao' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('os_etapas', [
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'os_id' => ['type' => 'INT'],
            'etapa' => ['type' => 'VARCHAR', 'constraint' => 50],
            'status' => ['type' => "ENUM('pendente','em_andamento','concluida','cancelada')", 'default' => 'pendente'],
            'ordem' => ['type' => 'INT', 'default' => 0],
            'tempo_estimado_minutos' => ['type' => 'INT', 'null' => true],
            'tempo_real_minutos' => ['type' => 'INT', 'null' => true],
            'iniciado_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'concluido_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'responsavel_id' => ['type' => 'INT', 'null' => true],
            'observacao' => ['type' => 'TEXT', 'null' => true],
            'checklist' => ['type' => 'JSON', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('tecnico_competencias', [
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'usuario_id' => ['type' => 'INT'],
            'competencia' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nivel' => ['type' => "ENUM('basico','intermediario','avancado','especialista')", 'default' => 'basico'],
            'certificado' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'validade_certificado' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('tecnico_avaliacoes', [
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'os_id' => ['type' => 'INT'],
            'tecnico_id' => ['type' => 'INT'],
            'cliente_id' => ['type' => 'INT'],
            'nota_geral' => ['type' => 'INT', 'null' => true],
            'nota_atendimento' => ['type' => 'INT', 'null' => true],
            'nota_solucao' => ['type' => 'INT', 'null' => true],
            'nota_tempo' => ['type' => 'INT', 'null' => true],
            'comentario' => ['type' => 'TEXT', 'null' => true],
            'avaliado_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ], ['id']);

        // --- V5 Técnico tables ---
        $this->_createTableIfNotExists('servicos_catalogo', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'codigo' => ['type' => 'VARCHAR', 'constraint' => 20],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'categoria' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Geral'],
            'especialidade' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'tempo_estimado_minutos' => ['type' => 'INT', 'default' => 60],
            'checklist_padrao' => ['type' => 'JSON', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('os_servicos', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
            'servico_id' => ['type' => 'INT', 'constraint' => 11],
            'quantidade' => ['type' => 'INT', 'default' => 1],
            'observacao' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => "ENUM('Pendente','EmExecucao','Concluido','Cancelado')", 'default' => 'Pendente'],
            'checklist_execucao' => ['type' => 'JSON', 'null' => true],
            'checklist_completude' => ['type' => 'INT', 'default' => 0],
            'tecnico_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'data_inicio' => ['type' => 'DATETIME', 'null' => true],
            'data_conclusao' => ['type' => 'DATETIME', 'null' => true],
            'tempo_execucao_minutos' => ['type' => 'INT', 'null' => true],
            'fotos' => ['type' => 'JSON', 'null' => true],
            'assinatura_cliente' => ['type' => 'TEXT', 'null' => true],
            'laudo_tecnico' => ['type' => 'TEXT', 'null' => true],
            'ordem_execucao' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('tec_os_execucao', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'os_id' => ['type' => 'INT', 'constraint' => 11],
            'tecnico_id' => ['type' => 'INT', 'constraint' => 11],
            'tipo_servico' => ['type' => "ENUM('INS','MP','MC','CT','TR','UP','URG')", 'default' => 'MC'],
            'especialidade' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'checkin_horario' => ['type' => 'DATETIME', 'null' => true],
            'checkin_latitude' => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'checkin_longitude' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'checkin_endereco' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'checkin_foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'checkin_distancia_metros' => ['type' => 'INT', 'null' => true],
            'checkout_horario' => ['type' => 'DATETIME', 'null' => true],
            'checkout_latitude' => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'checkout_longitude' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'checkout_endereco' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'checkout_foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'checkout_distancia_metros' => ['type' => 'INT', 'null' => true],
            'tempo_atendimento_minutos' => ['type' => 'INT', 'null' => true],
            'tempo_deslocamento_minutos' => ['type' => 'INT', 'null' => true],
            'km_deslocamento' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'checklist_json' => ['type' => 'JSON', 'null' => true],
            'checklist_completude' => ['type' => 'INT', 'default' => 0],
            'fotos_antes' => ['type' => 'JSON', 'null' => true],
            'fotos_depois' => ['type' => 'JSON', 'null' => true],
            'fotos_durante' => ['type' => 'JSON', 'null' => true],
            'assinatura_cliente' => ['type' => 'TEXT', 'null' => true],
            'nome_responsavel' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'avaliacao' => ['type' => 'INT', 'null' => true],
            'comentario_cliente' => ['type' => 'TEXT', 'null' => true],
            'laudo_tecnico' => ['type' => 'TEXT', 'null' => true],
            'materiais_utilizados' => ['type' => 'JSON', 'null' => true],
            'observacoes_tecnico' => ['type' => 'TEXT', 'null' => true],
            'problema_encontrado' => ['type' => 'TEXT', 'null' => true],
            'solucao_aplicada' => ['type' => 'TEXT', 'null' => true],
            'recomendacoes' => ['type' => 'TEXT', 'null' => true],
            'oportunidade_venda' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'descricao_oportunidade' => ['type' => 'TEXT', 'null' => true],
            'status_execucao' => ['type' => "ENUM('Agendada','EmDeslocamento','EmAtendimento','Pausada','Concluida','Cancelada')", 'default' => 'Agendada'],
            'aprovada' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'aprovada_por' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'data_aprovacao' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('tec_checklist_template', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'tipo_os' => ['type' => 'VARCHAR', 'constraint' => 50],
            'tipo_servico' => ['type' => "ENUM('INS','MP','MC','CT','TR','UP')", 'default' => 'MC'],
            'nome_template' => ['type' => 'VARCHAR', 'constraint' => 100],
            'itens' => ['type' => 'JSON'],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('tec_estoque_veiculo', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'tecnico_id' => ['type' => 'INT', 'constraint' => 11],
            'produto_id' => ['type' => 'INT', 'constraint' => 11],
            'quantidade_disponivel' => ['type' => 'INT', 'default' => 0],
            'quantidade_reservada' => ['type' => 'INT', 'default' => 0],
            'localizacao' => ['type' => "ENUM('Veiculo','EmUso','Retirado')", 'default' => 'Veiculo'],
            'ultima_movimentacao' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('tec_rotas_tracking', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'tecnico_id' => ['type' => 'INT', 'constraint' => 11],
            'data' => ['type' => 'DATE'],
            'pontos_rota' => ['type' => 'JSON', 'null' => true],
            'km_total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0'],
            'os_atendidas' => ['type' => 'INT', 'default' => 0],
            'tempo_total_horas' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0'],
            'combustivel_estimado' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0'],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('obras', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'codigo' => ['type' => 'VARCHAR', 'constraint' => 50],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255],
            'cliente_id' => ['type' => 'INT', 'constraint' => 11],
            'tipo_obra' => ['type' => "ENUM('Condominio','Comercio','Residencia','Industrial','Publica')", 'default' => 'Condominio'],
            'especialidade_principal' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'endereco' => ['type' => 'TEXT', 'null' => true],
            'bairro' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cidade' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'estado' => ['type' => 'VARCHAR', 'constraint' => 2, 'null' => true],
            'cep' => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => true],
            'coordenadas_lat' => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'coordenadas_lng' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'data_inicio_contrato' => ['type' => 'DATE', 'null' => true],
            'data_fim_prevista' => ['type' => 'DATE', 'null' => true],
            'data_fim_real' => ['type' => 'DATE', 'null' => true],
            'prazo_dias' => ['type' => 'INT', 'null' => true],
            'status' => ['type' => "ENUM('Prospeccao','Orcamentacao','Contratada','EmExecucao','Paralisada','Finalizada','Entregue','Garantia')", 'default' => 'Prospeccao'],
            'percentual_concluido' => ['type' => 'INT', 'default' => 0],
            'gestor_obra_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'responsavel_tecnico_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'responsavel_comercial_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'contrato_arquivo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'projeto_arquivo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'art_arquivo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'memorial_descritivo' => ['type' => 'TEXT', 'null' => true],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('obra_etapas', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'obra_id' => ['type' => 'INT', 'constraint' => 11],
            'numero_etapa' => ['type' => 'INT', 'default' => 1],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 100],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'especialidade' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'data_inicio_prevista' => ['type' => 'DATE', 'null' => true],
            'data_fim_prevista' => ['type' => 'DATE', 'null' => true],
            'data_inicio_real' => ['type' => 'DATE', 'null' => true],
            'data_fim_real' => ['type' => 'DATE', 'null' => true],
            'percentual_concluido' => ['type' => 'INT', 'default' => 0],
            'status' => ['type' => "ENUM('NaoIniciada','EmAndamento','Concluida','Atrasada','Paralisada')", 'default' => 'NaoIniciada'],
            'tecnicos_designados' => ['type' => 'JSON', 'null' => true],
            'os_ids' => ['type' => 'JSON', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('obra_diario', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'obra_id' => ['type' => 'INT', 'constraint' => 11],
            'data' => ['type' => 'DATE'],
            'clima_manha' => ['type' => "ENUM('Sol','Nublado','Chuva','Garoa')", 'null' => true],
            'clima_tarde' => ['type' => "ENUM('Sol','Nublado','Chuva','Garoa')", 'null' => true],
            'equipe_presente' => ['type' => 'JSON', 'null' => true],
            'atividades_executadas' => ['type' => 'TEXT', 'null' => true],
            'etapas_avancadas' => ['type' => 'JSON', 'null' => true],
            'fotos' => ['type' => 'JSON', 'null' => true],
            'problemas' => ['type' => 'TEXT', 'null' => true],
            'acoes_corretivas' => ['type' => 'TEXT', 'null' => true],
            'material_recebido' => ['type' => 'TEXT', 'null' => true],
            'material_consumido' => ['type' => 'TEXT', 'null' => true],
            'visitas_cliente' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'visitas_fiscalizacao' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'preenchido_por' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'preenchido_em' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('obra_equipe', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'obra_id' => ['type' => 'INT', 'constraint' => 11],
            'tecnico_id' => ['type' => 'INT', 'constraint' => 11],
            'funcao' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Tecnico'],
            'data_entrada' => ['type' => 'DATE'],
            'data_saida' => ['type' => 'DATE', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
        ], ['id']);

        $this->_createTableIfNotExists('tec_estoque_historico', [
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'tecnico_id' => ['type' => 'INT', 'constraint' => 11],
            'produto_id' => ['type' => 'INT', 'constraint' => 11],
            'tipo' => ['type' => "ENUM('entrada','saida')"],
            'quantidade' => ['type' => 'INT'],
            'os_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'observacao' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'data_hora' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'registrado_por' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ], ['id']);

        // Índices adicionais para tabelas que acabaram de ser criadas
        $this->_safeCreateIndex('os_checkin', 'idx_os_id', 'os_id');
        $this->_safeCreateIndex('os_checkin', 'idx_status', 'status');
        $this->_safeCreateIndex('os_assinaturas', 'idx_os_id', 'os_id');
        $this->_safeCreateIndex('os_assinaturas', 'idx_tipo', 'tipo');
        $this->_safeCreateIndex('os_fotos_atendimento', 'idx_os_id', 'os_id');
        $this->_safeCreateIndex('os_fotos_atendimento', 'idx_etapa', 'etapa');
        $this->_safeCreateIndex('usuarios_cliente', 'idx_cliente_id', 'cliente_id');
        $this->_safeCreateIndex('usuarios_cliente_cnpjs', 'idx_usuario_cnpj', 'usuario_cliente_id, cnpj');
        $this->_safeCreateIndex('usuarios_cliente_permissoes', 'idx_usuario_chave', 'usuario_cliente_id, chave');
        $this->_safeCreateIndex('dre_demonstracoes', 'idx_data', 'data_inicio, data_fim');
        $this->_safeCreateIndex('dre_demonstracoes', 'idx_tipo', 'tipo');
        $this->_safeCreateIndex('dre_contas', 'idx_tipo', 'tipo');
        $this->_safeCreateIndex('dre_contas', 'idx_ativo', 'ativo');
        $this->_safeCreateIndex('dre_lancamentos', 'idx_conta_id', 'conta_id');
        $this->_safeCreateIndex('dre_lancamentos', 'idx_data_referencia', 'data_referencia');
        $this->_safeCreateIndex('impostos_retidos', 'idx_id_os', 'id_os');
        $this->_safeCreateIndex('impostos_retidos', 'idx_id_venda', 'id_venda');
        $this->_safeCreateIndex('impostos_retidos', 'idx_tipo_imposto', 'tipo_imposto');
        $this->_safeCreateIndex('push_notifications', 'idx_user_id', 'user_id');
        $this->_safeCreateIndex('push_notifications', 'idx_is_read', 'is_read');
        $this->_safeCreateIndex('checkin', 'idx_os_id', 'os_id');
    }

    // ============================================================
    // 3. INSERIR DADOS PADRÃO AUSENTES
    // ============================================================
    private function _insertMissingData()
    {
        // Configurações padrão
        if ($this->db->table_exists('configuracoes')) {
            $configs = [
                ['idConfig' => 7, 'config' => 'notifica_whats', 'valor' => 'Prezado(a), {CLIENTE_NOME} a OS de nº {NUMERO_OS} teve o status alterado para: {STATUS_OS}'],
                ['idConfig' => 8, 'config' => 'control_baixa', 'valor' => '0'],
                ['idConfig' => 9, 'config' => 'control_editos', 'valor' => '1'],
                ['idConfig' => 10, 'config' => 'control_datatable', 'valor' => '1'],
                ['idConfig' => 11, 'config' => 'pix_key', 'valor' => ''],
                ['idConfig' => 12, 'config' => 'os_status_list', 'valor' => '["Aberto","Faturado","Negociacao","Em Andamento","Orcamento","Finalizado","Cancelado","Aguardando Pecas","Aprovado"]'],
                ['idConfig' => 13, 'config' => 'control_edit_vendas', 'valor' => '1'],
                ['idConfig' => 14, 'config' => 'email_automatico', 'valor' => '1'],
                ['idConfig' => 15, 'config' => 'control_2vias', 'valor' => '0'],
            ];
            foreach ($configs as $c) {
                $this->db->where('config', $c['config']);
                if ($this->db->get('configuracoes')->num_rows() == 0) {
                    $this->db->insert('configuracoes', $c);
                }
            }
        }

        // Permissões padrão para grupos
        if ($this->db->table_exists('permissoes')) {
            $this->_insertPermissionGroup('Tecnico', [
                'vCliente' => 1, 'vProduto' => 1, 'vServico' => 1,
                'vTecnicoOS' => 1, 'eTecnicoCheckin' => 1, 'eTecnicoCheckout' => 1,
                'eTecnicoFotos' => 1, 'vTecnicoDashboard' => 1
            ]);
            $this->_insertPermissionGroup('Dashboard', [
                'vDashboard' => 1, 'vRelatorioCompleto' => 1, 'vExportarDados' => 1
            ]);

            // Atualizar admin com permissões DRE
            $admin = $this->db->where('idPermissao', 1)->get('permissoes')->row();
            if ($admin && !empty($admin->permissoes)) {
                $perms = @unserialize($admin->permissoes);
                if (is_array($perms)) {
                $newPerms = ['vDRE', 'vDREDemonstracao', 'vDREContas', 'vDRELancamentos', 'cDRE', 'eDRE', 'dDRE', 'vDashboard', 'vRelatorioCompleto', 'vExportarDados'];
                    $changed = false;
                    foreach ($newPerms as $p) {
                        if (!isset($perms[$p])) {
                            $perms[$p] = 1;
                            $changed = true;
                        }
                    }
                    if ($changed) {
                        $this->db->where('idPermissao', 1)->update('permissoes', ['permissoes' => serialize($perms)]);
                    }
                }
            }
        }

        // Dados DRE padrão
        if ($this->db->table_exists('dre_contas')) {
            $contas = [
                ['codigo' => '1', 'nome' => 'RECEITA BRUTA', 'tipo' => 'receita', 'categoria' => 'Receitas', 'ordem' => 1],
                ['codigo' => '1.1', 'nome' => 'Servicos', 'tipo' => 'receita', 'categoria' => 'Receitas', 'ordem' => 2],
                ['codigo' => '1.2', 'nome' => 'Produtos', 'tipo' => 'receita', 'categoria' => 'Receitas', 'ordem' => 3],
                ['codigo' => '2', 'nome' => 'IMPOSTOS', 'tipo' => 'despesa', 'categoria' => 'Impostos', 'ordem' => 10],
                ['codigo' => '2.1', 'nome' => 'ISS', 'tipo' => 'despesa', 'categoria' => 'Impostos', 'ordem' => 11],
                ['codigo' => '3', 'nome' => 'CUSTOS', 'tipo' => 'custo', 'categoria' => 'Custos', 'ordem' => 20],
                ['codigo' => '4', 'nome' => 'DESPESAS OPERACIONAIS', 'tipo' => 'despesa', 'categoria' => 'Despesas', 'ordem' => 30],
            ];
            $now = date('Y-m-d H:i:s');
            foreach ($contas as $c) {
                $this->db->where('codigo', $c['codigo']);
                if ($this->db->get('dre_contas')->num_rows() == 0) {
                    $c['ativo'] = 1;
                    $c['created_at'] = $now;
                    $c['updated_at'] = $now;
                    $this->db->insert('dre_contas', $c);
                }
            }
        }

        // Config impostos padrão
        if ($this->db->table_exists('impostos_config')) {
            $this->db->where('tipo_regime', 'simples_nacional');
            if ($this->db->get('impostos_config')->num_rows() == 0) {
                $now = date('Y-m-d H:i:s');
                $this->db->insert('impostos_config', [
                    'tipo_regime' => 'simples_nacional',
                    'anexo_simples' => 'iii',
                    'aliquota_iss' => 2.00,
                    'retem_iss' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        // Catálogo de serviços padrão
        if ($this->db->table_exists('servicos_catalogo')) {
            if ($this->db->count_all('servicos_catalogo') == 0) {
                $now = date('Y-m-d H:i:s');
                $servicos = [
                    ['codigo' => 'SRV-CFTV-001', 'nome' => 'Instalacao de Cameras', 'descricao' => 'Instalacao completa de cameras de seguranca', 'categoria' => 'CFTV', 'especialidade' => 'CFTV', 'tempo_estimado_minutos' => 90, 'ativo' => 1],
                    ['codigo' => 'SRV-CFTV-002', 'nome' => 'Configuracao de Sistema CFTV', 'descricao' => 'Configuracao de gravadores e acesso remoto', 'categoria' => 'CFTV', 'especialidade' => 'CFTV', 'tempo_estimado_minutos' => 60, 'ativo' => 1],
                    ['codigo' => 'SRV-CFTV-003', 'nome' => 'Manutencao Preventiva CFTV', 'descricao' => 'Limpeza e verificacao de sistema CFTV', 'categoria' => 'CFTV', 'especialidade' => 'CFTV', 'tempo_estimado_minutos' => 45, 'ativo' => 1],
                    ['codigo' => 'SRV-ALM-001', 'nome' => 'Instalacao de Alarme', 'descricao' => 'Instalacao de sensores e central de alarme', 'categoria' => 'Alarmes', 'especialidade' => 'Alarmes', 'tempo_estimado_minutos' => 120, 'ativo' => 1],
                    ['codigo' => 'SRV-RED-001', 'nome' => 'Passagem de Cabos de Rede', 'descricao' => 'Passagem e organizacao de cabos estruturados', 'categoria' => 'Redes', 'especialidade' => 'Redes', 'tempo_estimado_minutos' => 90, 'ativo' => 1],
                    ['codigo' => 'SRV-ACE-001', 'nome' => 'Instalacao de Controle de Acesso', 'descricao' => 'Instalacao de catracas/leitores/fechaduras', 'categoria' => 'ControleAcesso', 'especialidade' => 'ControleAcesso', 'tempo_estimado_minutos' => 180, 'ativo' => 1],
                ];
                foreach ($servicos as $s) {
                    $s['created_at'] = $now;
                    $s['updated_at'] = $now;
                    $this->db->insert('servicos_catalogo', $s);
                }
            }
        }

        // Checklist templates padrão
        if ($this->db->table_exists('tec_checklist_template')) {
            if ($this->db->count_all('tec_checklist_template') == 0) {
                $now = date('Y-m-d H:i:s');
                $this->db->insert('tec_checklist_template', [
                    'tipo_os' => 'CFTV', 'tipo_servico' => 'INS', 'nome_template' => 'Instalacao CFTV Padrao',
                    'itens' => '[{"ordem":1,"desc":"Verificar integridade dos equipamentos","obrigatorio":true},{"ordem":2,"desc":"Definir posicoes das cameras com cliente","obrigatorio":true}]',
                    'ativo' => 1, 'created_at' => $now, 'updated_at' => $now
                ]);
                $this->db->insert('tec_checklist_template', [
                    'tipo_os' => 'CFTV', 'tipo_servico' => 'MP', 'nome_template' => 'Manutencao CFTV Padrao',
                    'itens' => '[{"ordem":1,"desc":"Verificar funcionamento das cameras","obrigatorio":true},{"ordem":2,"desc":"Limpar lentes","obrigatorio":true}]',
                    'ativo' => 1, 'created_at' => $now, 'updated_at' => $now
                ]);
            }
        }

        // Checklist templates básicos
        if ($this->db->table_exists('checklist_templates') && $this->db->count_all('checklist_templates') == 0) {
            $this->db->insert('checklist_templates', ['nome' => 'Manutencao Preventiva', 'descricao' => 'Checklist padrao para manutencoes preventivas', 'categoria' => 'preventiva']);
            $this->db->insert('checklist_templates', ['nome' => 'Reparo Corretivo', 'descricao' => 'Checklist para reparos e correcoes', 'categoria' => 'corretiva']);
            $this->db->insert('checklist_templates', ['nome' => 'Instalacao', 'descricao' => 'Checklist para instalacoes de novos equipamentos', 'categoria' => 'instalacao']);
        }

        // Atualizar versão da migration
        $this->db->replace('migrations', ['version' => '20260417000001']);
    }

    // ============================================================
    // HELPER: Criar índice com segurança
    // ============================================================
    private function _safeCreateIndex($table, $indexName, $columns)
    {
        if (!$this->db->table_exists($table)) return;

        $query = $this->db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'");
        if ($query->num_rows() == 0) {
            try {
                $this->db->query("CREATE INDEX `{$indexName}` ON `{$table}`({$columns})");
            } catch (Exception $e) {
                // Índice pode falhar se coluna não existir, ignorar
                log_message('debug', "Indice {$indexName} nao criado em {$table}: " . $e->getMessage());
            }
        }
    }

    // ============================================================
    // HELPER: Inserir grupo de permissão se não existir
    // ============================================================
    private function _insertPermissionGroup($nome, $permissoes)
    {
        $exists = $this->db->where('nome', $nome)->get('permissoes');
        if ($exists->num_rows() == 0) {
            $this->db->insert('permissoes', [
                'nome' => $nome,
                'permissoes' => serialize($permissoes),
                'situacao' => 1,
                'data' => date('Y-m-d')
            ]);
        }
    }
}