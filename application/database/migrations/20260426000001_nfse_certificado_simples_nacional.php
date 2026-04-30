<?php
/**
 * Migration: NFSe + Certificado + Simples Nacional
 *
 * Cria tabelas faltantes necessarias para o funcionamento completo
 * do modulo de NFS-e, Certificado Digital e Simples Nacional.
 *
 * Segura para executar multiplas vezes (IF NOT EXISTS).
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Nfse_certificado_simples_nacional extends CI_Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

        // ========================================================
        // 1. TABELA: config_sistema_impostos
        //    Usada por Impostos_model::getConfig() / setConfig()
        // ========================================================
        if (!$this->db->table_exists('config_sistema_impostos')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'chave' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
                'valor' => ['type' => 'TEXT', 'null' => true],
                'descricao' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('chave');
            $this->dbforge->create_table('config_sistema_impostos', true);
            log_message('info', 'Tabela config_sistema_impostos criada');
        }

        // ========================================================
        // 2. TABELA: certificado_consultas
        //    Usada por Certificado_model::registrarConsulta()
        // ========================================================
        if (!$this->db->table_exists('certificado_consultas')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'certificado_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'tipo_consulta' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
                'data_consulta' => ['type' => 'DATETIME', 'null' => false],
                'sucesso' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'dados_retorno' => ['type' => 'TEXT', 'null' => true],
                'erro' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('certificado_id');
            $this->dbforge->add_key('tipo_consulta');
            $this->dbforge->create_table('certificado_consultas', true);
            log_message('info', 'Tabela certificado_consultas criada');
        }

        // ========================================================
        // 3. TABELA: certificado_nfe_importada
        //    Usada por Certificado_model::importarNFSe()
        // ========================================================
        if (!$this->db->table_exists('certificado_nfe_importada')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'certificado_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'chave_acesso' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'numero' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'serie' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => '1'],
                'data_emissao' => ['type' => 'DATETIME', 'null' => true],
                'data_importacao' => ['type' => 'DATETIME', 'null' => true],
                'cnpj_destinatario' => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
                'valor_total' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'valor_impostos' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'situacao' => ['type' => "ENUM('Autorizada','Cancelada','Denegada')", 'default' => 'Autorizada'],
                'dados_xml' => ['type' => 'LONGTEXT', 'null' => true],
                'os_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('certificado_id');
            $this->dbforge->add_key('chave_acesso');
            $this->dbforge->create_table('certificado_nfe_importada', true);
            log_message('info', 'Tabela certificado_nfe_importada criada');
        } else {
            // Garantir coluna os_id em tabelas ja existentes
            $this->_addColumnIfNotExists('certificado_nfe_importada', 'os_id', [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'dados_xml'
            ]);
        }

        // ========================================================
        // 4. TABELA: impostos_retidos (garantir colunas extras)
        // ========================================================
        if ($this->db->table_exists('impostos_retidos')) {
            $this->_addColumnIfNotExists('impostos_retidos', 'os_id', [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'id'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'venda_id', [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'os_id'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'cobranca_id', [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'venda_id'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'cliente_id', [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'cobranca_id'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'valor_bruto', [
                'type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'cliente_id'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'valor_liquido', [
                'type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'valor_bruto'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'aliquota_aplicada', [
                'type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00', 'after' => 'valor_liquido'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'irpj_valor', [
                'type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'aliquota_aplicada'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'csll_valor', [
                'type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'irpj_valor'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'cofins_valor', [
                'type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'csll_valor'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'pis_valor', [
                'type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'cofins_valor'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'iss_valor', [
                'type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'pis_valor'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'total_impostos', [
                'type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'iss_valor'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'data_competencia', [
                'type' => 'DATE', 'null' => true, 'after' => 'total_impostos'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'data_retencao', [
                'type' => 'DATETIME', 'null' => true, 'after' => 'data_competencia'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'nota_fiscal', [
                'type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'data_retencao'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'status', [
                'type' => "ENUM('Retido','Estornado','Recolhido','Cancelado')", 'default' => 'Retido', 'after' => 'nota_fiscal'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'observacao', [
                'type' => 'TEXT', 'null' => true, 'after' => 'status'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'usuarios_id', [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'observacao'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'dre_lancamento_id', [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'usuarios_id'
            ]);
            $this->_addColumnIfNotExists('impostos_retidos', 'updated_at', [
                'type' => 'DATETIME', 'null' => true, 'after' => 'dre_lancamento_id'
            ]);
        }

        // ========================================================
        // 4.5. TABELA: os_boleto_emitido (campo valor_integral)
        // ========================================================
        if ($this->db->table_exists('os_boleto_emitido')) {
            $this->_addColumnIfNotExists('os_boleto_emitido', 'valor_integral', [
                'type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'valor_liquido',
                'comment' => '1 = boleto emitido com valor integral (retenção tomador)'
            ]);
        }

        // ========================================================
        // 5. DADOS PADRAO: config_sistema_impostos
        // ========================================================
        if ($this->db->table_exists('config_sistema_impostos')) {
            $configs = [
                ['chave' => 'IMPOSTO_RETENCAO_AUTOMATICA',   'valor' => '0',       'descricao' => 'Ativar retencao automatica de impostos'],
                ['chave' => 'IMPOSTO_ANEXO_PADRAO',           'valor' => 'III',   'descricao' => 'Anexo do Simples Nacional padrao'],
                ['chave' => 'IMPOSTO_FAIXA_ATUAL',            'valor' => '1',     'descricao' => 'Faixa de faturamento atual'],
                ['chave' => 'IMPOSTO_ISS_MUNICIPAL',          'valor' => '5.00',  'descricao' => 'Aliquota ISS municipal (%)'],
                ['chave' => 'IMPOSTO_DRE_INTEGRACAO',         'valor' => '0',     'descricao' => 'Integrar retencoes com DRE'],
                ['chave' => 'IMPOSTO_REGIME_TRIBUTARIO',      'valor' => 'simples_nacional', 'descricao' => 'Regime tributario padrao'],
                ['chave' => 'IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL', 'valor' => '010701', 'descricao' => 'Codigo tributacao nacional LC 116/2003'],
                ['chave' => 'IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL', 'valor' => '100', 'descricao' => 'Codigo tributacao municipal'],
                ['chave' => 'IMPOSTO_DESCRICAO_SERVICO',      'valor' => 'Servicos de informatica', 'descricao' => 'Descricao do servico para NFS-e'],
            ];

            foreach ($configs as $c) {
                $exists = $this->db->where('chave', $c['chave'])->get('config_sistema_impostos')->num_rows();
                if ($exists == 0) {
                    $c['created_at'] = date('Y-m-d H:i:s');
                    $c['updated_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('config_sistema_impostos', $c);
                }
            }
        }

        // ========================================================
        // 6. GARANTIR PERMISSOES NFSe NO ADMIN
        // ========================================================
        if ($this->db->table_exists('permissoes')) {
            $admin = $this->db->where('idPermissao', 1)->get('permissoes')->row();
            if ($admin && !empty($admin->permissoes)) {
                $perms = @unserialize($admin->permissoes);
                if (is_array($perms)) {
                    $newPerms = [
                        'vNFSe', 'cNFSe', 'eNFSe', 'rNFSe',
                        'vBoletoOS', 'cBoletoOS', 'eBoletoOS',
                    ];
                    $changed = false;
                    foreach ($newPerms as $p) {
                        if (!isset($perms[$p])) {
                            $perms[$p] = 1;
                            $changed = true;
                        }
                    }
                    if ($changed) {
                        $this->db->where('idPermissao', 1)->update('permissoes', [
                            'permissoes' => serialize($perms)
                        ]);
                    }
                }
            }
        }

        // ========================================================
        // 7. INDICES ADICIONAIS
        // ========================================================
        $this->_safeCreateIndex('config_sistema_impostos', 'idx_chave', 'chave');
        $this->_safeCreateIndex('certificado_consultas', 'idx_certificado_id', 'certificado_id');
        $this->_safeCreateIndex('certificado_nfe_importada', 'idx_certificado_id_imp', 'certificado_id');
        $this->_safeCreateIndex('certificado_nfe_importada', 'idx_chave_acesso', 'chave_acesso');
        $this->_safeCreateIndex('certificado_nfe_importada', 'idx_os_id_imp', 'os_id');

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        log_message('info', 'Migration NFSe+Certificado+Simples Nacional executada com sucesso.');
    }

    public function down()
    {
        log_message('info', 'Migration NFSe+Certificado: rollback desabilitado para proteger dados.');
    }

    // ============================================================
    // HELPERS
    // ============================================================
    private function _addColumnIfNotExists($table, $column, $definition)
    {
        if (!$this->db->field_exists($column, $table)) {
            $this->dbforge->add_column($table, [$column => $definition]);
            log_message('info', "Coluna {$column} adicionada a tabela {$table}");
        }
    }

    private function _safeCreateIndex($table, $indexName, $columns)
    {
        if (!$this->db->table_exists($table)) return;
        $query = $this->db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'");
        if ($query->num_rows() == 0) {
            try {
                $this->db->query("CREATE INDEX `{$indexName}` ON `{$table}`({$columns})");
            } catch (Exception $e) {
                log_message('debug', "Indice {$indexName} nao criado em {$table}: " . $e->getMessage());
            }
        }
    }
}
