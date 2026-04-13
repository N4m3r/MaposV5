<?php
/**
 * INSTALAÇÃO SEGURA - DRE, Impostos e Certificado Digital
 * Para instalações limpas do MAPOS
 *
 * Instruções:
 * 1. Primeiro instale o MAPOS base normalmente
 * 2. Depois execute este script via navegador ou CLI
 * 3. Acesse: https://seusite.com/index.php/migrate/instalar_modulos
 */

class Migration_install_dre_impostos_certificado extends CI_Migration
{
    public function up()
    {
        echo "Iniciando instalação dos módulos DRE, Impostos e Certificado Digital...\n";

        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

        // =====================================================
        // 1. VERIFICAR TABELAS BASE
        // =====================================================
        $tabelas_base = ['usuarios', 'os', 'clientes', 'vendas', 'cobrancas', 'lancamentos', 'permissoes'];
        $tabelas_faltando = [];

        foreach ($tabelas_base as $tabela) {
            if (!$this->db->table_exists($tabela)) {
                $tabelas_faltando[] = $tabela;
            }
        }

        if (!empty($tabelas_faltando)) {
            echo "ERRO: As seguintes tabelas base do MAPOS não existem:\n";
            foreach ($tabelas_faltando as $t) {
                echo "  - {$t}\n";
            }
            echo "\nInstale o MAPOS base antes de executar esta migration!\n";
            return false;
        }

        echo "Tabelas base verificadas... OK\n";

        // =====================================================
        // 2. TABELAS DRE
        // =====================================================
        echo "Criando tabelas DRE...\n";

        // Tabela: dre_contas
        if (!$this->db->table_exists('dre_contas')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'codigo' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
                'nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
                'tipo' => ['type' => "ENUM('RECEITA', 'CUSTO', 'DESPESA', 'IMPOSTO', 'TRANSFERENCIA')", 'null' => false],
                'grupo' => ['type' => "ENUM('RECEITA_BRUTA', 'DEDUCOES', 'RECEITA_LIQUIDA', 'CUSTO', 'LUCRO_BRUTO', 'DESPESA_OPERACIONAL', 'LUCRO_OPERACIONAL', 'OUTRAS_RECEITAS', 'OUTRAS_DESPESAS', 'RESULTADO_FINANCEIRO', 'LUCRO_ANTES_IR', 'IMPOSTO_RENDA', 'LUCRO_LIQUIDO')", 'null' => false],
                'ordem' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'conta_pai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'nivel' => ['type' => 'INT', 'constraint' => 2, 'default' => 1],
                'sinal' => ['type' => "ENUM('POSITIVO', 'NEGATIVO')", 'default' => 'POSITIVO'],
                'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('dre_contas', true, ['ENGINE' => 'InnoDB']);
            echo "  - dre_contas criada\n";
        } else {
            echo "  - dre_contas já existe\n";
        }

        // Tabela: dre_lancamentos
        if (!$this->db->table_exists('dre_lancamentos')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'conta_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'data' => ['type' => 'DATE', 'null' => false],
                'valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'tipo_movimento' => ['type' => "ENUM('DEBITO', 'CREDITO')", 'null' => false],
                'descricao' => ['type' => 'TEXT', 'null' => true],
                'documento' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'os_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'venda_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'lancamento_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'usuarios_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('dre_lancamentos', true, ['ENGINE' => 'InnoDB']);
            echo "  - dre_lancamentos criada\n";
        } else {
            echo "  - dre_lancamentos já existe\n";
        }

        // Tabela: dre_config
        if (!$this->db->table_exists('dre_config')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'tipo' => ['type' => "ENUM('MAPEAMENTO_OS', 'MAPEAMENTO_VENDA', 'MAPEAMENTO_LANCAMENTO', 'CONFIG')", 'null' => false],
                'origem_tabela' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'origem_campo' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'conta_dre_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'condicao' => ['type' => 'TEXT', 'null' => true],
                'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('dre_config', true, ['ENGINE' => 'InnoDB']);
            echo "  - dre_config criada\n";
        } else {
            echo "  - dre_config já existe\n";
        }

        // =====================================================
        // 3. TABELAS IMPOSTOS
        // =====================================================
        echo "Criando tabelas de Impostos...\n";

        // Tabela: impostos_config
        if (!$this->db->table_exists('impostos_config')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'anexo' => ['type' => "ENUM('I', 'II', 'III', 'IV', 'V')", 'null' => false],
                'faixa' => ['type' => 'INT', 'constraint' => 2, 'null' => false],
                'aliquota_nominal' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => false],
                'irpj' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
                'csll' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
                'cofins' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
                'pis' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
                'cpp' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
                'iss' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
                'outros' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
                'atividade_principal' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('impostos_config', true, ['ENGINE' => 'InnoDB']);
            echo "  - impostos_config criada\n";
        } else {
            echo "  - impostos_config já existe\n";
        }

        // Tabela: impostos_retidos
        if (!$this->db->table_exists('impostos_retidos')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'cobranca_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'os_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'venda_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'cliente_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'valor_bruto' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
                'valor_liquido' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
                'aliquota_aplicada' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => false],
                'irpj_valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'csll_valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'cofins_valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'pis_valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'iss_valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'total_impostos' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
                'data_competencia' => ['type' => 'DATE', 'null' => false],
                'data_retencao' => ['type' => 'DATETIME', 'null' => false],
                'nota_fiscal' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'status' => ['type' => "ENUM('Retido', 'Recolhido', 'Estornado')", 'default' => 'Retido'],
                'observacao' => ['type' => 'TEXT', 'null' => true],
                'usuarios_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'dre_lancamento_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('impostos_retidos', true, ['ENGINE' => 'InnoDB']);
            echo "  - impostos_retidos criada\n";
        } else {
            echo "  - impostos_retidos já existe\n";
        }

        // Tabela: config_sistema_impostos
        if (!$this->db->table_exists('config_sistema_impostos')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'chave' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
                'valor' => ['type' => 'TEXT', 'null' => true],
                'descricao' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('config_sistema_impostos', true, ['ENGINE' => 'InnoDB']);
            echo "  - config_sistema_impostos criada\n";
        } else {
            echo "  - config_sistema_impostos já existe\n";
        }

        // =====================================================
        // 4. TABELAS CERTIFICADO DIGITAL
        // =====================================================
        echo "Criando tabelas de Certificado Digital...\n";

        // Tabela: certificado_digital
        if (!$this->db->table_exists('certificado_digital')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'tipo' => ['type' => "ENUM('A1', 'A3')", 'default' => 'A1'],
                'cnpj' => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => false],
                'razao_social' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'nome_fantasia' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'arquivo_caminho' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'arquivo_hash' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'senha' => ['type' => 'TEXT', 'null' => true],
                'data_validade' => ['type' => 'DATETIME', 'null' => true],
                'data_emissao' => ['type' => 'DATETIME', 'null' => true],
                'emissor' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'serial_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'ultimo_acesso' => ['type' => 'DATETIME', 'null' => true],
                'ultimo_erro' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('certificado_digital', true, ['ENGINE' => 'InnoDB']);
            echo "  - certificado_digital criada\n";
        } else {
            echo "  - certificado_digital já existe\n";
        }

        // Tabela: certificado_consultas
        if (!$this->db->table_exists('certificado_consultas')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'certificado_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'tipo_consulta' => ['type' => "ENUM('CNPJ', 'SIMPLES_NACIONAL', 'NFE', 'NFSE', 'SITUACAO_CADASTRO')", 'null' => false],
                'data_consulta' => ['type' => 'DATETIME', 'null' => false],
                'sucesso' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'dados_retorno' => ['type' => 'LONGTEXT', 'null' => true],
                'erro' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('certificado_consultas', true, ['ENGINE' => 'InnoDB']);
            echo "  - certificado_consultas criada\n";
        } else {
            echo "  - certificado_consultas já existe\n";
        }

        // Tabela: certificado_nfe_importada
        if (!$this->db->table_exists('certificado_nfe_importada')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'certificado_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'chave_acesso' => ['type' => 'VARCHAR', 'constraint' => 44, 'null' => false],
                'numero' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'serie' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'data_emissao' => ['type' => 'DATETIME', 'null' => true],
                'data_importacao' => ['type' => 'DATETIME', 'null' => true],
                'cnpj_destinatario' => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
                'valor_total' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
                'valor_impostos' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
                'xml_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'situacao' => ['type' => "ENUM('Autorizada', 'Cancelada', 'Denegada', 'Inutilizada')", 'default' => 'Autorizada'],
                'imposto_integrado' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'dados_xml' => ['type' => 'LONGTEXT', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('certificado_nfe_importada', true, ['ENGINE' => 'InnoDB']);
            echo "  - certificado_nfe_importada criada\n";
        } else {
            echo "  - certificado_nfe_importada já existe\n";
        }

        // =====================================================
        // 5. INSERIR DADOS INICIAIS
        // =====================================================
        echo "Inserindo dados iniciais...\n";

        // Verificar se já existem contas DRE
        $this->db->where('codigo', '1');
        $query = $this->db->get('dre_contas');
        if ($query->num_rows() == 0) {
            $this->inserirContasDREPadrao();
            echo "  - Contas DRE padrão inseridas\n";
        } else {
            echo "  - Contas DRE padrão já existem\n";
        }

        // Verificar se já existem alíquotas
        $this->db->where('anexo', 'III');
        $query = $this->db->get('impostos_config');
        if ($query->num_rows() == 0) {
            $this->inserirAliquotasImpostos();
            echo "  - Alíquotas de impostos inseridas\n";
        } else {
            echo "  - Alíquotas de impostos já existem\n";
        }

        // Verificar se já existem configurações
        $this->db->where('chave', 'IMPOSTO_ANEXO_PADRAO');
        $query = $this->db->get('config_sistema_impostos');
        if ($query->num_rows() == 0) {
            $this->inserirConfiguracoesImpostos();
            echo "  - Configurações de impostos inseridas\n";
        } else {
            echo "  - Configurações de impostos já existem\n";
        }

        // =====================================================
        // 6. INSERIR PERMISSÕES
        // =====================================================
        echo "Inserindo permissões...\n";
        $this->inserirPermissoes();

        // =====================================================
        // 7. ADICIONAR FOREIGN KEYS (OPCIONAL)
        // =====================================================
        echo "Adicionando foreign keys (se possível)...\n";
        $this->adicionarForeignKeys();

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        echo "\n=====================================\n";
        echo "INSTALAÇÃO CONCLUÍDA COM SUCESSO!\n";
        echo "=====================================\n";
        echo "\nPróximos passos:\n";
        echo "1. Configure o menu no sistema para acessar:\n";
        echo "   - DRE Contábil: /dre\n";
        echo "   - Impostos: /impostos\n";
        echo "   - Certificado: /certificado\n";
        echo "2. Atribua as permissões aos grupos de usuários\n";
        echo "3. Configure o certificado digital (opcional)\n";
        echo "4. Ajuste as configurações de impostos\n";

        return true;
    }

    public function down()
    {
        // Não remove tabelas para proteger dados
        echo "Migration down: Nenhuma tabela foi removida para proteger os dados.\n";
    }

    /**
     * Insere contas DRE padrão
     */
    private function inserirContasDREPadrao()
    {
        $now = date('Y-m-d H:i:s');

        $contas = [
            // RECEITA BRUTA
            ['codigo' => '1', 'nome' => 'RECEITA BRUTA', 'tipo' => 'RECEITA', 'grupo' => 'RECEITA_BRUTA', 'ordem' => 10, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '1.1', 'nome' => 'Receita de Serviços', 'tipo' => 'RECEITA', 'grupo' => 'RECEITA_BRUTA', 'ordem' => 11, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '1.2', 'nome' => 'Receita de Vendas', 'tipo' => 'RECEITA', 'grupo' => 'RECEITA_BRUTA', 'ordem' => 12, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '1.3', 'nome' => 'Outras Receitas Operacionais', 'tipo' => 'RECEITA', 'grupo' => 'RECEITA_BRUTA', 'ordem' => 13, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // DEDUÇÕES
            ['codigo' => '2', 'nome' => '(-) DEDUÇÕES DA RECEITA', 'tipo' => 'IMPOSTO', 'grupo' => 'DEDUCOES', 'ordem' => 20, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '2.1', 'nome' => 'Impostos Sobre Vendas', 'tipo' => 'IMPOSTO', 'grupo' => 'DEDUCOES', 'ordem' => 21, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '2.2', 'nome' => 'Devoluções e Abatimentos', 'tipo' => 'RECEITA', 'grupo' => 'DEDUCOES', 'ordem' => 22, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '2.3', 'nome' => 'Descontos Concedidos', 'tipo' => 'RECEITA', 'grupo' => 'DEDUCOES', 'ordem' => 23, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // RECEITA LÍQUIDA
            ['codigo' => '3', 'nome' => '= RECEITA LÍQUIDA', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'RECEITA_LIQUIDA', 'ordem' => 30, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // CUSTOS
            ['codigo' => '4', 'nome' => '(-) CUSTO DOS SERVIÇOS/PRODUTOS', 'tipo' => 'CUSTO', 'grupo' => 'CUSTO', 'ordem' => 40, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '4.1', 'nome' => 'Materiais Utilizados', 'tipo' => 'CUSTO', 'grupo' => 'CUSTO', 'ordem' => 41, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '4.2', 'nome' => 'Mão de Obra Direta', 'tipo' => 'CUSTO', 'grupo' => 'CUSTO', 'ordem' => 42, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '4.3', 'nome' => 'Custos Operacionais Diretos', 'tipo' => 'CUSTO', 'grupo' => 'CUSTO', 'ordem' => 43, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // LUCRO BRUTO
            ['codigo' => '5', 'nome' => '= LUCRO BRUTO', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'LUCRO_BRUTO', 'ordem' => 50, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // DESPESAS OPERACIONAIS
            ['codigo' => '6', 'nome' => '(-) DESPESAS OPERACIONAIS', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 60, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.1', 'nome' => 'Despesas Administrativas', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 61, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.1.1', 'nome' => 'Salários e Encargos Administrativos', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 611, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.1.2', 'nome' => 'Aluguel', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 612, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.1.3', 'nome' => 'Contas de Consumo', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 613, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.1.4', 'nome' => 'Material de Escritório', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 614, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.1.5', 'nome' => 'Honorários Profissionais', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 615, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.1.6', 'nome' => 'Outras Despesas Administrativas', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 619, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.2', 'nome' => 'Despesas com Vendas', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 62, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.2.1', 'nome' => 'Comissões', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 621, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.2.2', 'nome' => 'Propaganda e Publicidade', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 622, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '6.2.3', 'nome' => 'Despesas de Entrega', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 623, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // LUCRO OPERACIONAL
            ['codigo' => '7', 'nome' => '= LUCRO/PREJUÍZO OPERACIONAL', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'LUCRO_OPERACIONAL', 'ordem' => 70, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // RESULTADO FINANCEIRO
            ['codigo' => '8', 'nome' => 'RESULTADO FINANCEIRO', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'RESULTADO_FINANCEIRO', 'ordem' => 80, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '8.1', 'nome' => 'Receitas Financeiras', 'tipo' => 'RECEITA', 'grupo' => 'OUTRAS_RECEITAS', 'ordem' => 81, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '8.1.1', 'nome' => 'Juros Recebidos', 'tipo' => 'RECEITA', 'grupo' => 'OUTRAS_RECEITAS', 'ordem' => 811, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '8.1.2', 'nome' => 'Descontos Obtidos', 'tipo' => 'RECEITA', 'grupo' => 'OUTRAS_RECEITAS', 'ordem' => 812, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '8.2', 'nome' => 'Despesas Financeiras', 'tipo' => 'DESPESA', 'grupo' => 'OUTRAS_DESPESAS', 'ordem' => 82, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '8.2.1', 'nome' => 'Juros Pagos', 'tipo' => 'DESPESA', 'grupo' => 'OUTRAS_DESPESAS', 'ordem' => 821, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '8.2.2', 'nome' => 'Descontos Concedidos', 'tipo' => 'DESPESA', 'grupo' => 'OUTRAS_DESPESAS', 'ordem' => 822, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '8.2.3', 'nome' => 'Tarifas Bancárias', 'tipo' => 'DESPESA', 'grupo' => 'OUTRAS_DESPESAS', 'ordem' => 823, 'conta_pai_id' => null, 'nivel' => 3, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // LUCRO ANTES DO IR
            ['codigo' => '9', 'nome' => '= LUCRO/PREJUÍZO ANTES DO IR', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'LUCRO_ANTES_IR', 'ordem' => 90, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // IMPOSTO DE RENDA
            ['codigo' => '10', 'nome' => '(-) IMPOSTO DE RENDA E CONTRIBUIÇÕES', 'tipo' => 'IMPOSTO', 'grupo' => 'IMPOSTO_RENDA', 'ordem' => 100, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '10.1', 'nome' => 'IRPJ', 'tipo' => 'IMPOSTO', 'grupo' => 'IMPOSTO_RENDA', 'ordem' => 101, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '10.2', 'nome' => 'CSLL', 'tipo' => 'IMPOSTO', 'grupo' => 'IMPOSTO_RENDA', 'ordem' => 102, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => '10.3', 'nome' => 'PIS/COFINS', 'tipo' => 'IMPOSTO', 'grupo' => 'IMPOSTO_RENDA', 'ordem' => 103, 'conta_pai_id' => null, 'nivel' => 2, 'sinal' => 'NEGATIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
            // LUCRO LÍQUIDO
            ['codigo' => '11', 'nome' => '= LUCRO/PREJUÍZO LÍQUIDO DO EXERCÍCIO', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'LUCRO_LIQUIDO', 'ordem' => 110, 'conta_pai_id' => null, 'nivel' => 1, 'sinal' => 'POSITIVO', 'ativo' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($contas as $conta) {
            $this->db->insert('dre_contas', $conta);
        }
    }

    /**
     * Insere alíquotas de impostos
     */
    private function inserirAliquotasImpostos()
    {
        $now = date('Y-m-d H:i:s');

        // Anexo III - Serviços
        $anexo3 = [
            ['faixa' => 1, 'aliquota' => 6.00, 'irpj' => 4.00, 'csll' => 3.50, 'cofins' => 12.82, 'pis' => 2.78, 'cpp' => 43.40, 'iss' => 33.50],
            ['faixa' => 2, 'aliquota' => 11.20, 'irpj' => 4.00, 'csll' => 3.50, 'cofins' => 14.05, 'pis' => 3.05, 'cpp' => 38.99, 'iss' => 32.41],
            ['faixa' => 3, 'aliquota' => 13.50, 'irpj' => 4.00, 'csll' => 3.50, 'cofins' => 13.64, 'pis' => 2.96, 'cpp' => 37.62, 'iss' => 32.28],
            ['faixa' => 4, 'aliquota' => 16.00, 'irpj' => 4.00, 'csll' => 3.50, 'cofins' => 13.26, 'pis' => 2.87, 'cpp' => 35.13, 'iss' => 31.24],
            ['faixa' => 5, 'aliquota' => 21.00, 'irpj' => 4.00, 'csll' => 3.50, 'cofins' => 12.82, 'pis' => 2.78, 'cpp' => 34.23, 'iss' => 30.67],
        ];

        foreach ($anexo3 as $f) {
            $this->db->insert('impostos_config', [
                'anexo' => 'III',
                'faixa' => $f['faixa'],
                'aliquota_nominal' => $f['aliquota'],
                'irpj' => $f['irpj'],
                'csll' => $f['csll'],
                'cofins' => $f['cofins'],
                'pis' => $f['pis'],
                'cpp' => $f['cpp'],
                'iss' => $f['iss'],
                'outros' => 0,
                'atividade_principal' => 'Prestação de serviços em geral (Anexo III)',
                'ativo' => 1,
                'created_at' => $now
            ]);
        }

        // Anexo IV - Construção
        $anexo4 = [
            ['faixa' => 1, 'aliquota' => 4.50, 'irpj' => 0.00, 'csll' => 15.74, 'cofins' => 14.68, 'pis' => 3.19, 'cpp' => 41.50, 'iss' => 31.00],
            ['faixa' => 2, 'aliquota' => 9.00, 'irpj' => 0.00, 'csll' => 15.74, 'cofins' => 14.68, 'pis' => 3.19, 'cpp' => 41.50, 'iss' => 24.89],
            ['faixa' => 3, 'aliquota' => 13.50, 'irpj' => 0.00, 'csll' => 15.74, 'cofins' => 14.68, 'pis' => 3.19, 'cpp' => 42.09, 'iss' => 20.80],
            ['faixa' => 4, 'aliquota' => 17.00, 'irpj' => 1.00, 'csll' => 14.74, 'cofins' => 13.73, 'pis' => 2.98, 'cpp' => 39.40, 'iss' => 24.15],
            ['faixa' => 5, 'aliquota' => 21.00, 'irpj' => 1.00, 'csll' => 14.74, 'cofins' => 13.73, 'pis' => 2.98, 'cpp' => 38.48, 'iss' => 23.07],
        ];

        foreach ($anexo4 as $f) {
            $this->db->insert('impostos_config', [
                'anexo' => 'IV',
                'faixa' => $f['faixa'],
                'aliquota_nominal' => $f['aliquota'],
                'irpj' => $f['irpj'],
                'csll' => $f['csll'],
                'cofins' => $f['cofins'],
                'pis' => $f['pis'],
                'cpp' => $f['cpp'],
                'iss' => $f['iss'],
                'outros' => 0,
                'atividade_principal' => 'Construção e serviços com ISS próprio (Anexo IV)',
                'ativo' => 1,
                'created_at' => $now
            ]);
        }
    }

    /**
     * Insere configurações de impostos
     */
    private function inserirConfiguracoesImpostos()
    {
        $configs = [
            ['chave' => 'IMPOSTO_ANEXO_PADRAO', 'valor' => 'III', 'descricao' => 'Anexo do Simples Nacional padrão para a empresa'],
            ['chave' => 'IMPOSTO_FAIXA_ATUAL', 'valor' => '1', 'descricao' => 'Faixa de faturamento atual (1-5)'],
            ['chave' => 'IMPOSTO_RETENCAO_AUTOMATICA', 'valor' => '1', 'descricao' => 'Habilitar retenção automática em novos boletos (1=Sim, 0=Não)'],
            ['chave' => 'IMPOSTO_DRE_INTEGRACAO', 'valor' => '1', 'descricao' => 'Integrar retenções automaticamente com DRE (1=Sim, 0=Não)'],
            ['chave' => 'IMPOSTO_ISS_MUNICIPAL', 'valor' => '5.00', 'descricao' => 'Alíquota de ISS municipal para cálculo isolado (%)'],
        ];

        foreach ($configs as $c) {
            $this->db->insert('config_sistema_impostos', $c);
        }
    }

    /**
     * Insere permissões do sistema
     */
    private function inserirPermissoes()
    {
        $permissoes = [
            // DRE
            ['nome' => 'Visualizar DRE', 'permissoes' => ['vDRE' => 1]],
            ['nome' => 'Visualizar Relatório DRE', 'permissoes' => ['vDRERelatorio' => 1]],
            ['nome' => 'Cadastrar Conta DRE', 'permissoes' => ['cDREConta' => 1]],
            ['nome' => 'Deletar Conta DRE', 'permissoes' => ['dDREConta' => 1]],
            ['nome' => 'Visualizar Lançamentos DRE', 'permissoes' => ['vDRELancamento' => 1]],
            ['nome' => 'Cadastrar Lançamento DRE', 'permissoes' => ['cDRELancamento' => 1]],
            ['nome' => 'Deletar Lançamento DRE', 'permissoes' => ['dDRELancamento' => 1]],
            ['nome' => 'Integrar Dados DRE', 'permissoes' => ['cDREIntegracao' => 1]],
            ['nome' => 'Exportar DRE', 'permissoes' => ['vDREExportar' => 1]],
            ['nome' => 'Análise DRE', 'permissoes' => ['vDREAnalise' => 1]],
            // Impostos
            ['nome' => 'Visualizar Impostos', 'permissoes' => ['vImpostos' => 1]],
            ['nome' => 'Visualizar Relatório Impostos', 'permissoes' => ['vImpostosRelatorio' => 1]],
            ['nome' => 'Configurar Impostos', 'permissoes' => ['cImpostosConfig' => 1]],
            ['nome' => 'Editar Impostos', 'permissoes' => ['eImpostos' => 1]],
            ['nome' => 'Exportar Impostos', 'permissoes' => ['vImpostosExportar' => 1]],
            // Certificado
            ['nome' => 'Visualizar Certificado', 'permissoes' => ['vCertificado' => 1]],
            ['nome' => 'Configurar Certificado', 'permissoes' => ['cCertificado' => 1]],
            ['nome' => 'Editar Certificado', 'permissoes' => ['eCertificado' => 1]],
            ['nome' => 'Remover Certificado', 'permissoes' => ['dCertificado' => 1]],
        ];

        foreach ($permissoes as $p) {
            $this->db->where('nome', $p['nome']);
            $exists = $this->db->get('permissoes');

            if ($exists->num_rows() == 0) {
                $this->db->insert('permissoes', [
                    'nome' => $p['nome'],
                    'data' => date('Y-m-d'),
                    'permissoes' => serialize($p['permissoes']),
                    'situacao' => 1,
                ]);
            }
        }

        echo "  - Permissões inseridas/atualizadas\n";
    }

    /**
     * Adiciona foreign keys de forma segura
     */
    private function adicionarForeignKeys()
    {
        // Lista de FKs a serem adicionadas
        $foreign_keys = [
            [
                'tabela' => 'dre_lancamentos',
                'fk' => 'fk_lancamentos_conta',
                'coluna' => 'conta_id',
                'ref_tabela' => 'dre_contas',
                'ref_coluna' => 'id'
            ],
            [
                'tabela' => 'dre_lancamentos',
                'fk' => 'fk_lancamentos_usuario',
                'coluna' => 'usuarios_id',
                'ref_tabela' => 'usuarios',
                'ref_coluna' => 'idUsuarios'
            ],
            [
                'tabela' => 'dre_config',
                'fk' => 'fk_config_conta',
                'coluna' => 'conta_dre_id',
                'ref_tabela' => 'dre_contas',
                'ref_coluna' => 'id'
            ],
            [
                'tabela' => 'certificado_consultas',
                'fk' => 'fk_consulta_certificado',
                'coluna' => 'certificado_id',
                'ref_tabela' => 'certificado_digital',
                'ref_coluna' => 'id'
            ],
            [
                'tabela' => 'certificado_nfe_importada',
                'fk' => 'fk_nfe_certificado',
                'coluna' => 'certificado_id',
                'ref_tabela' => 'certificado_digital',
                'ref_coluna' => 'id'
            ],
        ];

        foreach ($foreign_keys as $fk) {
            try {
                // Verificar se FK já existe
                $query = $this->db->query("
                    SELECT CONSTRAINT_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = '{$fk['tabela']}'
                    AND CONSTRAINT_NAME = '{$fk['fk']}'
                ");

                if ($query->num_rows() == 0) {
                    $this->db->query("
                        ALTER TABLE `{$fk['tabela']}`
                        ADD CONSTRAINT `{$fk['fk']}`
                        FOREIGN KEY (`{$fk['coluna']}`)
                        REFERENCES `{$fk['ref_tabela']}`(`{$fk['ref_coluna']}`)
                        ON DELETE CASCADE ON UPDATE CASCADE
                    ");
                    echo "  - FK {$fk['fk']} adicionada\n";
                }
            } catch (Exception $e) {
                echo "  - FK {$fk['fk']} não adicionada (pode já existir ou erro: " . $e->getMessage() . ")\n";
            }
        }
    }
}
