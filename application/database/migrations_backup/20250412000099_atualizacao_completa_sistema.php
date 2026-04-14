<?php
/**
 * Migration: Atualização Completa do Sistema MAPOS
 *
 * Cria todas as tabelas necessárias para:
 * - Sistema DRE Contábil
 * - Sistema de Impostos e Certificados Digitais
 * - Sistema de Usuários do Cliente
 * - Sistema de Webhooks
 * - Vinculação de Documentos à OS
 *
 * Esta migration é segura e pode ser executada em um
 * banco de dados existente sem perda de dados.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Atualizacao_completa_sistema extends CI_Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

        // ========================================================
        // 1. SISTEMA DRE CONTÁBIL
        // ========================================================

        // Tabela de Demonstrações DRE
        if (!$this->db->table_exists('dre_demonstracoes')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'descricao' => ['type' => 'TEXT', 'null' => true],
                'data_inicio' => ['type' => 'DATE', 'null' => false],
                'data_fim' => ['type' => 'DATE', 'null' => false],
                'tipo' => ['type' => "ENUM('mensal', 'trimestral', 'anual')", 'default' => 'mensal'],
                'status' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key(['data_inicio', 'data_fim']);
            $this->dbforge->add_key('tipo');
            $this->dbforge->create_table('dre_demonstracoes');
        }

        // Tabela de Plano de Contas DRE
        if (!$this->db->table_exists('dre_contas')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'codigo' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
                'nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'descricao' => ['type' => 'TEXT', 'null' => true],
                'tipo' => ['type' => "ENUM('receita', 'custo', 'despesa', 'deducao', 'resultado')", 'null' => false],
                'categoria' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'conta_pai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'ordem' => ['type' => 'INT', 'constraint' => 5, 'default' => 0],
                'formula' => ['type' => 'TEXT', 'null' => true, 'comment' => 'Fórmula de cálculo se conta calculada'],
                'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('codigo');
            $this->dbforge->add_key('tipo');
            $this->dbforge->add_key('conta_pai_id');
            $this->dbforge->add_key('ordem');
            $this->dbforge->create_table('dre_contas');

            // Inserir contas DRE padrão
            $this->inserirContasDREPadrao();
        }

        // Tabela de Lançamentos DRE
        if (!$this->db->table_exists('dre_lancamentos')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'demonstracao_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'conta_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'descricao' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'data_lancamento' => ['type' => 'DATE', 'null' => false],
                'referencia_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'comment' => 'ID da OS, venda ou outro documento'],
                'referencia_tipo' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'comment' => 'os, venda, despesa, etc.'],
                'observacoes' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('demonstracao_id');
            $this->dbforge->add_key('conta_id');
            $this->dbforge->add_key('data_lancamento');
            $this->dbforge->add_key(['referencia_tipo', 'referencia_id']);
            $this->dbforge->create_table('dre_lancamentos');
        }

        // ========================================================
        // 2. SISTEMA DE IMPOSTOS
        // ========================================================

        // Tabela de Certificados Digitais
        if (!$this->db->table_exists('certificados digitais')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'cnpj' => ['type' => 'VARCHAR', 'constraint' => 18, 'null' => false],
                'tipo' => ['type' => "ENUM('A1', 'A3')", 'default' => 'A1'],
                'arquivo_pfx' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'senha_criptografada' => ['type' => 'TEXT', 'null' => true, 'comment' => 'Senha criptografada com AES-256'],
                'data_emissao' => ['type' => 'DATE', 'null' => true],
                'data_validade' => ['type' => 'DATE', 'null' => false],
                'data_vencimento' => ['type' => 'DATE', 'null' => true],
                'emissor' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'ultima_verificacao' => ['type' => 'DATETIME', 'null' => true],
                'status_validacao' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('cnpj');
            $this->dbforge->add_key('data_validade');
            $this->dbforge->add_key('ativo');
            $this->dbforge->create_table('certificados digitais');
        }

        // Tabela de Configurações de Impostos
        if (!$this->db->table_exists('configuracoes_impostos')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'cnpj' => ['type' => 'VARCHAR', 'constraint' => 18, 'null' => false],
                'razao_social' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'anexo_simples' => ['type' => "ENUM('I', 'II', 'III', 'IV', 'V')", 'default' => 'III'],
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
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('cnpj');
            $this->dbforge->add_key('anexo_simples');
            $this->dbforge->add_key('ativo');
            $this->dbforge->create_table('configuracoes_impostos');
        }

        // Tabela de Cálculos de Impostos
        if (!$this->db->table_exists('calculos_impostos')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'cnpj' => ['type' => 'VARCHAR', 'constraint' => 18, 'null' => false],
                'os_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'venda_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'cobranca_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'valor_bruto' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
                'valor_liquido' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
                'iss' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'pis' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'cofins' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'csll' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'inss' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'ir' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'total_impostos' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'aliquota_efetiva' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                'competencia' => ['type' => 'DATE', 'null' => false],
                'status' => ['type' => "ENUM('calculado', 'retido', 'recolhido', 'cancelado')", 'default' => 'calculado'],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('cnpj');
            $this->dbforge->add_key('os_id');
            $this->dbforge->add_key('venda_id');
            $this->dbforge->add_key('cobranca_id');
            $this->dbforge->add_key('competencia');
            $this->dbforge->add_key('status');
            $this->dbforge->create_table('calculos_impostos');
        }

        // Tabela de NFS-e Importadas
        if (!$this->db->table_exists('nfse_importadas')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'cnpj' => ['type' => 'VARCHAR', 'constraint' => 18, 'null' => false],
                'numero_nota' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
                'codigo_verificacao' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'data_emissao' => ['type' => 'DATETIME', 'null' => true],
                'valor_servicos' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'valor_deducoes' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'valor_iss' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'aliquota_iss' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
                'valor_pis' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'valor_cofins' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'valor_csll' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'valor_inss' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'valor_ir' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'tomador_cnpj' => ['type' => 'VARCHAR', 'constraint' => 18, 'null' => true],
                'tomador_nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'xml_conteudo' => ['type' => 'LONGTEXT', 'null' => true],
                'status' => ['type' => "ENUM('pendente', 'processada', 'cancelada')", 'default' => 'pendente'],
                'os_vinculada_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key(['cnpj', 'numero_nota']);
            $this->dbforge->add_key('tomador_cnpj');
            $this->dbforge->add_key('status');
            $this->dbforge->create_table('nfse_importadas');
        }

        // ========================================================
        // 3. SISTEMA DE USUÁRIOS DO CLIENTE
        // ========================================================

        // Tabela de Usuários do Cliente
        if (!$this->db->table_exists('usuarios_cliente')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'senha' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'telefone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'celular' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'ultimo_acesso' => ['type' => 'DATETIME', 'null' => true],
                'token_reset' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'token_expira' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('email');
            $this->dbforge->add_key('token_reset');
            $this->dbforge->add_key('ativo');
            $this->dbforge->create_table('usuarios_cliente');
        }

        // Tabela de CNPJs Vinculados
        if (!$this->db->table_exists('usuarios_cliente_cnpjs')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'usuario_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'cnpj' => ['type' => 'VARCHAR', 'constraint' => 18, 'null' => false],
                'razao_social' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'nome_fantasia' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'principal' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key(['usuario_id', 'cnpj']);
            $this->dbforge->add_key('cnpj');
            $this->dbforge->add_key('principal');
            $this->dbforge->create_table('usuarios_cliente_cnpjs');
        }

        // Tabela de Permissões
        if (!$this->db->table_exists('usuarios_cliente_permissoes')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'usuario_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'permissao' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
                'valor' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key(['usuario_id', 'permissao']);
            $this->dbforge->add_key('permissao');
            $this->dbforge->create_table('usuarios_cliente_permissoes');
        }

        // ========================================================
        // 4. SISTEMA DE WEBHOOKS
        // ========================================================

        // Tabela de Webhooks
        if (!$this->db->table_exists('webhooks')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'url' => ['type' => 'TEXT', 'null' => false],
                'events' => ['type' => 'TEXT', 'null' => true, 'comment' => 'JSON array de eventos'],
                'secret' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'retry_count' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 3],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('active');
            $this->dbforge->create_table('webhooks');
        }

        // Tabela de Logs de Webhooks
        if (!$this->db->table_exists('webhook_logs')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'webhook_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'event' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
                'payload' => ['type' => 'LONGTEXT', 'null' => true, 'comment' => 'JSON do payload enviado'],
                'response' => ['type' => 'LONGTEXT', 'null' => true],
                'http_code' => ['type' => 'INT', 'constraint' => 4, 'null' => true],
                'success' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'error' => ['type' => 'TEXT', 'null' => true],
                'attempt' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('webhook_id');
            $this->dbforge->add_key('event');
            $this->dbforge->add_key('success');
            $this->dbforge->add_key('created_at');
            $this->dbforge->create_table('webhook_logs');
        }

        // ========================================================
        // 5. VINCULAÇÃO DE DOCUMENTOS À OS
        // ========================================================

        // Tabela de Documentos Vinculados
        if (!$this->db->table_exists('os_documentos')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'os_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'tipo' => ['type' => "ENUM('boleto', 'nfse', 'nfe', 'nfce', 'recibo', 'contrato', 'outro')", 'null' => false],
                'descricao' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'numero_documento' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
                'data_emissao' => ['type' => 'DATE', 'null' => true],
                'data_vencimento' => ['type' => 'DATE', 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'arquivo' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'link_externo' => ['type' => 'TEXT', 'null' => true],
                'gateway_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'comment' => 'ID do boleto no gateway'],
                'charge_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'comment' => 'ID da cobrança'],
                'nfse_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'observacoes' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('os_id');
            $this->dbforge->add_key('tipo');
            $this->dbforge->add_key('gateway_id');
            $this->dbforge->add_key('charge_id');
            $this->dbforge->create_table('os_documentos');
        }

        // ========================================================
        // 6. COLUNAS EM TABELAS EXISTENTES
        // ========================================================

        // Verificar e adicionar colunas na tabela OS
        $this->adicionarColunaSeNaoExistir('os', 'certificado_vinculado', [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'null' => true,
            'after' => 'garantia'
        ]);

        $this->adicionarColunaSeNaoExistir('os', 'retencao_impostos', [
            'type' => 'TINYINT',
            'constraint' => 1,
            'default' => 0
        ]);

        $this->adicionarColunaSeNaoExistir('os', 'valor_liquido', [
            'type' => 'DECIMAL',
            'constraint' => '15,2',
            'null' => true
        ]);

        $this->adicionarColunaSeNaoExistir('os', 'calculo_impostos', [
            'type' => 'TEXT',
            'null' => true,
            'comment' => 'JSON com valores dos impostos'
        ]);

        // Adicionar coluna em lancamentos (se a tabela existir)
        if ($this->db->table_exists('lancamentos')) {
            $this->adicionarColunaSeNaoExistir('lancamentos', 'webhook_notificado', [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ]);
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function down()
    {
        // Esta migration não remove tabelas para proteger os dados
        // Apenas desativa as colunas adicionais se necessário
        log_message('info', 'Migration down: Nenhuma tabela foi removida para proteger os dados existentes.');
    }

    /**
     * Adiciona uma coluna se ela não existir na tabela
     */
    private function adicionarColunaSeNaoExistir($tabela, $coluna, $definicao)
    {
        // Verificar se coluna existe
        $query = $this->db->query("SHOW COLUMNS FROM {$tabela} LIKE '{$coluna}'");
        if ($query->num_rows() == 0) {
            $this->dbforge->add_column($tabela, [$coluna => $definicao]);
            log_message('info', "Coluna {$coluna} adicionada à tabela {$tabela}");
        }
    }

    /**
     * Insere as contas DRE padrão
     */
    private function inserirContasDREPadrao()
    {
        $contas = [
            // Receitas
            ['codigo' => '1', 'nome' => 'RECEITAS OPERACIONAIS', 'descricao' => 'Receitas com vendas e serviços', 'tipo' => 'receita', 'categoria' => 'receita', 'ordem' => 100],
            ['codigo' => '1.1', 'nome' => 'Vendas de Produtos', 'descricao' => 'Receita com vendas de produtos', 'tipo' => 'receita', 'categoria' => 'vendas', 'ordem' => 110],
            ['codigo' => '1.2', 'nome' => 'Serviços Prestados', 'descricao' => 'Receita com prestação de serviços', 'tipo' => 'receita', 'categoria' => 'servicos', 'ordem' => 120],
            // Deduções
            ['codigo' => '2', 'nome' => 'DEDUÇÕES DA RECEITA', 'descricao' => 'Impostos e descontos sobre vendas', 'tipo' => 'deducao', 'categoria' => 'deducao', 'ordem' => 200],
            ['codigo' => '2.1', 'nome' => 'ICMS sobre Vendas', 'descricao' => 'ICMS incidente sobre vendas', 'tipo' => 'deducao', 'categoria' => 'impostos', 'ordem' => 210],
            ['codigo' => '2.2', 'nome' => 'PIS/COFINS', 'descricao' => 'PIS e COFINS sobre faturamento', 'tipo' => 'deducao', 'categoria' => 'impostos', 'ordem' => 220],
            ['codigo' => '2.3', 'nome' => 'ISSQN', 'descricao' => 'ISS sobre serviços', 'tipo' => 'deducao', 'categoria' => 'impostos', 'ordem' => 230],
            ['codigo' => '2.4', 'nome' => 'Descontos Incondicionais', 'descricao' => 'Descontos concedidos nas vendas', 'tipo' => 'deducao', 'categoria' => 'descontos', 'ordem' => 240],
            // Receita Líquida
            ['codigo' => '3', 'nome' => 'RECEITA LÍQUIDA', 'descricao' => 'Receita bruta menos deduções', 'tipo' => 'resultado', 'categoria' => 'resultado', 'ordem' => 300],
            // Custos
            ['codigo' => '4', 'nome' => 'CUSTOS', 'descricao' => 'Custos dos produtos e serviços vendidos', 'tipo' => 'custo', 'categoria' => 'custos', 'ordem' => 400],
            ['codigo' => '4.1', 'nome' => 'CPV - Custo dos Produtos', 'descricao' => 'Custo das mercadorias vendidas', 'tipo' => 'custo', 'categoria' => 'cpv', 'ordem' => 410],
            ['codigo' => '4.2', 'nome' => 'CSP - Custo dos Serviços', 'descricao' => 'Custo dos serviços prestados', 'tipo' => 'custo', 'categoria' => 'csp', 'ordem' => 420],
            ['codigo' => '4.3', 'nome' => 'Mão de Obra Direta', 'descricao' => 'Salários e encargos da equipe técnica', 'tipo' => 'custo', 'categoria' => 'mod', 'ordem' => 430],
            ['codigo' => '4.4', 'nome' => 'Material de Consumo', 'descricao' => 'Materiais utilizados nos serviços', 'tipo' => 'custo', 'categoria' => 'insumos', 'ordem' => 440],
            // Lucro Bruto
            ['codigo' => '5', 'nome' => 'LUCRO BRUTO', 'descricao' => 'Receita líquida menos custos', 'tipo' => 'resultado', 'categoria' => 'resultado', 'ordem' => 500],
            // Despesas Operacionais
            ['codigo' => '6', 'nome' => 'DESPESAS OPERACIONAIS', 'descricao' => 'Despesas administrativas e comerciais', 'tipo' => 'despesa', 'categoria' => 'despesas', 'ordem' => 600],
            ['codigo' => '6.1', 'nome' => 'Despesas Administrativas', 'descricao' => 'Despesas gerais e administrativas', 'tipo' => 'despesa', 'categoria' => 'administrativas', 'ordem' => 610],
            ['codigo' => '6.2', 'nome' => 'Despesas Comerciais', 'descricao' => 'Despesas de vendas e marketing', 'tipo' => 'despesa', 'categoria' => 'comerciais', 'ordem' => 620],
            ['codigo' => '6.3', 'nome' => 'Despesas com Pessoal', 'descricao' => 'Salários administrativos e encargos', 'tipo' => 'despesa', 'categoria' => 'pessoal', 'ordem' => 630],
            ['codigo' => '6.4', 'nome' => 'Aluguel e Condomínio', 'descricao' => 'Despesas com imóvel', 'tipo' => 'despesa', 'categoria' => 'imoveis', 'ordem' => 640],
            ['codigo' => '6.5', 'nome' => 'Serviços de Terceiros', 'descricao' => 'Contador, advogado, consultorias', 'tipo' => 'despesa', 'categoria' => 'terceiros', 'ordem' => 650],
            ['codigo' => '6.6', 'nome' => 'Despesas Financeiras', 'descricao' => 'Juros, tarifas bancárias, etc.', 'tipo' => 'despesa', 'categoria' => 'financeiras', 'ordem' => 660],
            // LAIR
            ['codigo' => '7', 'nome' => 'LAIR', 'descricao' => 'Lucro antes dos impostos', 'tipo' => 'resultado', 'categoria' => 'resultado', 'ordem' => 700],
            // Impostos
            ['codigo' => '8', 'nome' => 'IMPOSTOS SOBRE LUCRO', 'descricao' => 'IRPJ e CSLL', 'tipo' => 'despesa', 'categoria' => 'impostos', 'ordem' => 800],
            ['codigo' => '8.1', 'nome' => 'IRPJ', 'descricao' => 'Imposto de Renda Pessoa Jurídica', 'tipo' => 'despesa', 'categoria' => 'irpj', 'ordem' => 810],
            ['codigo' => '8.2', 'nome' => 'CSLL', 'descricao' => 'Contribuição Social sobre Lucro Líquido', 'tipo' => 'despesa', 'categoria' => 'csll', 'ordem' => 820],
            // Líquido
            ['codigo' => '9', 'nome' => 'LUCRO/PREJUÍZO LÍQUIDO', 'descricao' => 'Resultado final do período', 'tipo' => 'resultado', 'categoria' => 'resultado', 'ordem' => 900],
        ];

        foreach ($contas as $conta) {
            $this->db->insert('dre_contas', $conta);
        }
    }
}
