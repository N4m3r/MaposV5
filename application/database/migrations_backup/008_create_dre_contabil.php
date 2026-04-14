<?php
/**
 * Migration: Sistema de DRE Contábil
 * Cria estrutura de contas e lançamentos para Demonstração do Resultado
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_dre_contabil extends CI_Migration {

    public function up()
    {
        // Tabela: dre_contas (Plano de Contas para DRE)
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
                'null' => FALSE
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['RECEITA', 'CUSTO', 'DESPESA', 'IMPOSTO', 'TRANSFERENCIA'],
                'null' => FALSE
            ],
            'grupo' => [
                'type' => 'ENUM',
                'constraint' => [
                    'RECEITA_BRUTA',
                    'DEDUCOES',
                    'RECEITA_LIQUIDA',
                    'CUSTO',
                    'LUCRO_BRUTO',
                    'DESPESA_OPERACIONAL',
                    'LUCRO_OPERACIONAL',
                    'OUTRAS_RECEITAS',
                    'OUTRAS_DESPESAS',
                    'RESULTADO_FINANCEIRO',
                    'LUCRO_ANTES_IR',
                    'IMPOSTO_RENDA',
                    'LUCRO_LIQUIDO'
                ],
                'null' => FALSE
            ],
            'ordem' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'conta_pai_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE
            ],
            'nivel' => [
                'type' => 'INT',
                'constraint' => 2,
                'default' => 1
            ],
            'sinal' => [
                'type' => 'ENUM',
                'constraint' => ['POSITIVO', 'NEGATIVO'],
                'default' => 'POSITIVO'
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
        $this->dbforge->add_key('codigo');
        $this->dbforge->add_key('tipo');
        $this->dbforge->add_key('grupo');
        $this->dbforge->create_table('dre_contas', TRUE, ['ENGINE' => 'InnoDB']);

        // Tabela: dre_lancamentos (Lançamentos Contábeis)
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'conta_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'data' => [
                'type' => 'DATE',
                'null' => FALSE
            ],
            'valor' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE,
                'default' => 0.00
            ],
            'tipo_movimento' => [
                'type' => 'ENUM',
                'constraint' => ['DEBITO', 'CREDITO'],
                'null' => FALSE
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'documento' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'os_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE
            ],
            'venda_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE
            ],
            'lancamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE
            ],
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
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
        $this->dbforge->add_key('conta_id');
        $this->dbforge->add_key('data');
        $this->dbforge->add_key('os_id');
        $this->dbforge->add_key('venda_id');
        $this->dbforge->create_table('dre_lancamentos', TRUE, ['ENGINE' => 'InnoDB']);

        // Tabela: dre_config (Configurações e mapeamentos)
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['MAPEAMENTO_OS', 'MAPEAMENTO_VENDA', 'MAPEAMENTO_LANCAMENTO', 'CONFIG'],
                'null' => FALSE
            ],
            'origem_tabela' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'origem_campo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'conta_dre_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'condicao' => [
                'type' => 'TEXT',
                'null' => TRUE
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
        $this->dbforge->create_table('dre_config', TRUE, ['ENGINE' => 'InnoDB']);

        // Adicionar Foreign Keys
        $this->db->query('ALTER TABLE `dre_lancamentos`
            ADD CONSTRAINT `fk_lancamentos_conta` FOREIGN KEY (`conta_id`) REFERENCES `dre_contas`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `fk_lancamentos_usuario` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios`(`idUsuarios`) ON DELETE RESTRICT ON UPDATE CASCADE
        ');

        $this->db->query('ALTER TABLE `dre_config`
            ADD CONSTRAINT `fk_config_conta` FOREIGN KEY (`conta_dre_id`) REFERENCES `dre_contas`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ');

        // Inserir estrutura padrão do DRE
        $this->seedEstruturaDRE();
    }

    public function down()
    {
        $this->dbforge->drop_table('dre_config', TRUE);
        $this->dbforge->drop_table('dre_lancamentos', TRUE);
        $this->dbforge->drop_table('dre_contas', TRUE);
    }

    private function seedEstruturaDRE()
    {
        $now = date('Y-m-d H:i:s');

        // Estrutura DRE Completa
        $contas = [
            // RECEITA BRUTA
            ['codigo' => '1', 'nome' => 'RECEITA BRUTA', 'tipo' => 'RECEITA', 'grupo' => 'RECEITA_BRUTA', 'ordem' => 10, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'POSITIVO'],
            ['codigo' => '1.1', 'nome' => 'Receita de Serviços', 'tipo' => 'RECEITA', 'grupo' => 'RECEITA_BRUTA', 'ordem' => 11, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'POSITIVO'],
            ['codigo' => '1.2', 'nome' => 'Receita de Vendas', 'tipo' => 'RECEITA', 'grupo' => 'RECEITA_BRUTA', 'ordem' => 12, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'POSITIVO'],
            ['codigo' => '1.3', 'nome' => 'Outras Receitas Operacionais', 'tipo' => 'RECEITA', 'grupo' => 'RECEITA_BRUTA', 'ordem' => 13, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'POSITIVO'],

            // DEDUÇÕES
            ['codigo' => '2', 'nome' => '(-) DEDUÇÕES DA RECEITA', 'tipo' => 'IMPOSTO', 'grupo' => 'DEDUCOES', 'ordem' => 20, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'NEGATIVO'],
            ['codigo' => '2.1', 'nome' => 'Impostos Sobre Vendas', 'tipo' => 'IMPOSTO', 'grupo' => 'DEDUCOES', 'ordem' => 21, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],
            ['codigo' => '2.2', 'nome' => 'Devoluções e Abatimentos', 'tipo' => 'RECEITA', 'grupo' => 'DEDUCOES', 'ordem' => 22, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],
            ['codigo' => '2.3', 'nome' => 'Descontos Concedidos', 'tipo' => 'RECEITA', 'grupo' => 'DEDUCOES', 'ordem' => 23, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],

            // RECEITA LÍQUIDA (Grupo calculado)
            ['codigo' => '3', 'nome' => '= RECEITA LÍQUIDA', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'RECEITA_LIQUIDA', 'ordem' => 30, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'POSITIVO'],

            // CUSTOS
            ['codigo' => '4', 'nome' => '(-) CUSTO DOS SERVIÇOS/PRODUTOS', 'tipo' => 'CUSTO', 'grupo' => 'CUSTO', 'ordem' => 40, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'NEGATIVO'],
            ['codigo' => '4.1', 'nome' => 'Materiais Utilizados', 'tipo' => 'CUSTO', 'grupo' => 'CUSTO', 'ordem' => 41, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],
            ['codigo' => '4.2', 'nome' => 'Mão de Obra Direta', 'tipo' => 'CUSTO', 'grupo' => 'CUSTO', 'ordem' => 42, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],
            ['codigo' => '4.3', 'nome' => 'Custos Operacionais Diretos', 'tipo' => 'CUSTO', 'grupo' => 'CUSTO', 'ordem' => 43, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],

            // LUCRO BRUTO (Grupo calculado)
            ['codigo' => '5', 'nome' => '= LUCRO BRUTO', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'LUCRO_BRUTO', 'ordem' => 50, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'POSITIVO'],

            // DESPESAS OPERACIONAIS
            ['codigo' => '6', 'nome' => '(-) DESPESAS OPERACIONAIS', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 60, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'NEGATIVO'],

            // Despesas Administrativas
            ['codigo' => '6.1', 'nome' => 'Despesas Administrativas', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 61, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],
            ['codigo' => '6.1.1', 'nome' => 'Salários e Encargos Administrativos', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 611, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],
            ['codigo' => '6.1.2', 'nome' => 'Aluguel', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 612, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],
            ['codigo' => '6.1.3', 'nome' => 'Contas de Consumo (Água, Luz, Telefone)', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 613, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],
            ['codigo' => '6.1.4', 'nome' => 'Material de Escritório', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 614, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],
            ['codigo' => '6.1.5', 'nome' => 'Honorários Profissionais', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 615, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],
            ['codigo' => '6.1.6', 'nome' => 'Outras Despesas Administrativas', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 619, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],

            // Despesas com Vendas
            ['codigo' => '6.2', 'nome' => 'Despesas com Vendas', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 62, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],
            ['codigo' => '6.2.1', 'nome' => 'Comissões', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 621, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],
            ['codigo' => '6.2.2', 'nome' => 'Propaganda e Publicidade', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 622, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],
            ['codigo' => '6.2.3', 'nome' => 'Despesas de Entrega', 'tipo' => 'DESPESA', 'grupo' => 'DESPESA_OPERACIONAL', 'ordem' => 623, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],

            // LUCRO OPERACIONAL (Grupo calculado)
            ['codigo' => '7', 'nome' => '= LUCRO/PREJUÍZO OPERACIONAL', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'LUCRO_OPERACIONAL', 'ordem' => 70, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'POSITIVO'],

            // RESULTADO FINANCEIRO
            ['codigo' => '8', 'nome' => 'RESULTADO FINANCEIRO', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'RESULTADO_FINANCEIRO', 'ordem' => 80, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'POSITIVO'],
            ['codigo' => '8.1', 'nome' => 'Receitas Financeiras', 'tipo' => 'RECEITA', 'grupo' => 'OUTRAS_RECEITAS', 'ordem' => 81, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'POSITIVO'],
            ['codigo' => '8.1.1', 'nome' => 'Juros Recebidos', 'tipo' => 'RECEITA', 'grupo' => 'OUTRAS_RECEITAS', 'ordem' => 811, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'POSITIVO'],
            ['codigo' => '8.1.2', 'nome' => 'Descontos Obtidos', 'tipo' => 'RECEITA', 'grupo' => 'OUTRAS_RECEITAS', 'ordem' => 812, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'POSITIVO'],
            ['codigo' => '8.2', 'nome' => 'Despesas Financeiras', 'tipo' => 'DESPESA', 'grupo' => 'OUTRAS_DESPESAS', 'ordem' => 82, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],
            ['codigo' => '8.2.1', 'nome' => 'Juros Pagos', 'tipo' => 'DESPESA', 'grupo' => 'OUTRAS_DESPESAS', 'ordem' => 821, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],
            ['codigo' => '8.2.2', 'nome' => 'Descontos Concedidos', 'tipo' => 'DESPESA', 'grupo' => 'OUTRAS_DESPESAS', 'ordem' => 822, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],
            ['codigo' => '8.2.3', 'nome' => 'Tarifas Bancárias', 'tipo' => 'DESPESA', 'grupo' => 'OUTRAS_DESPESAS', 'ordem' => 823, 'conta_pai_id' => NULL, 'nivel' => 3, 'sinal' => 'NEGATIVO'],

            // LUCRO ANTES DO IR
            ['codigo' => '9', 'nome' => '= LUCRO/PREJUÍZO ANTES DO IR', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'LUCRO_ANTES_IR', 'ordem' => 90, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'POSITIVO'],

            // IMPOSTO DE RENDA
            ['codigo' => '10', 'nome' => '(-) IMPOSTO DE RENDA E CONTRIBUIÇÕES', 'tipo' => 'IMPOSTO', 'grupo' => 'IMPOSTO_RENDA', 'ordem' => 100, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'NEGATIVO'],
            ['codigo' => '10.1', 'nome' => 'IRPJ', 'tipo' => 'IMPOSTO', 'grupo' => 'IMPOSTO_RENDA', 'ordem' => 101, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],
            ['codigo' => '10.2', 'nome' => 'CSLL', 'tipo' => 'IMPOSTO', 'grupo' => 'IMPOSTO_RENDA', 'ordem' => 102, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],
            ['codigo' => '10.3', 'nome' => 'PIS/COFINS', 'tipo' => 'IMPOSTO', 'grupo' => 'IMPOSTO_RENDA', 'ordem' => 103, 'conta_pai_id' => NULL, 'nivel' => 2, 'sinal' => 'NEGATIVO'],

            // LUCRO LÍQUIDO
            ['codigo' => '11', 'nome' => '= LUCRO/PREJUÍZO LÍQUIDO DO EXERCÍCIO', 'tipo' => 'TRANSFERENCIA', 'grupo' => 'LUCRO_LIQUIDO', 'ordem' => 110, 'conta_pai_id' => NULL, 'nivel' => 1, 'sinal' => 'POSITIVO'],
        ];

        foreach ($contas as $conta) {
            $conta['created_at'] = $now;
            $conta['updated_at'] = $now;
            $this->db->insert('dre_contas', $conta);
        }
    }
}
