<?php
/**
 * Migration: Sistema de Impostos Simples Nacional
 * Configuração de alíquotas e retenção automática em boletos
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_impostos_simples extends CI_Migration {

    public function up()
    {
        // Tabela: impostos_config - Configurações de alíquotas do Simples
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'anexo' => [
                'type' => 'ENUM',
                'constraint' => ['I', 'II', 'III', 'IV', 'V'],
                'null' => FALSE,
                'comment' => 'Anexo do Simples Nacional'
            ],
            'faixa' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => FALSE,
                'comment' => 'Faixa de faturamento (1-5)'
            ],
            'aliquota_nominal' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'comment' => 'Alíquota nominal total (%)'
            ],
            'irpj' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'default' => 0.00,
                'comment' => '% IRPJ dentro da alíquota'
            ],
            'csll' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'default' => 0.00,
                'comment' => '% CSLL dentro da alíquota'
            ],
            'cofins' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'default' => 0.00,
                'comment' => '% COFINS dentro da alíquota'
            ],
            'pis' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'default' => 0.00,
                'comment' => '% PIS dentro da alíquota'
            ],
            'cpp' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'default' => 0.00,
                'comment' => '% Contribuição Previdenciária'
            ],
            'iss' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'default' => 0.00,
                'comment' => '% ISS dentro da alíquota'
            ],
            'outros' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'default' => 0.00
            ],
            'atividade_principal' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'comment' => 'Descrição das atividades'
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
        $this->dbforge->add_key(['anexo', 'faixa']);
        $this->dbforge->create_table('impostos_config', TRUE, ['ENGINE' => 'InnoDB']);

        // Tabela: impostos_retidos - Registro de impostos retidos em boletos
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'cobranca_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'ID da cobrança/boleto'
            ],
            'os_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'ID da OS relacionada'
            ],
            'venda_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'ID da venda relacionada'
            ],
            'cliente_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'valor_bruto' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE,
                'comment' => 'Valor bruto do serviço'
            ],
            'valor_liquido' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE,
                'comment' => 'Valor após retenção'
            ],
            'aliquota_aplicada' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE,
                'comment' => 'Alíquota % aplicada'
            ],
            'irpj_valor' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE,
                'default' => 0.00
            ],
            'csll_valor' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE,
                'default' => 0.00
            ],
            'cofins_valor' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE,
                'default' => 0.00
            ],
            'pis_valor' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE,
                'default' => 0.00
            ],
            'iss_valor' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE,
                'default' => 0.00
            ],
            'total_impostos' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE,
                'default' => 0.00
            ],
            'data_competencia' => [
                'type' => 'DATE',
                'null' => FALSE,
                'comment' => 'Mês/ano de competência'
            ],
            'data_retencao' => [
                'type' => 'DATETIME',
                'null' => FALSE,
                'comment' => 'Data da retenção'
            ],
            'nota_fiscal' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE,
                'comment' => 'Número da NFSe'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Retido', 'Recolhido', 'Estornado'],
                'default' => 'Retido'
            ],
            'observacao' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'dre_lancamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'Vínculo com lançamento DRE'
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
        $this->dbforge->add_key('cobranca_id');
        $this->dbforge->add_key('os_id');
        $this->dbforge->add_key('data_competencia');
        $this->dbforge->create_table('impostos_retidos', TRUE, ['ENGINE' => 'InnoDB']);

        // Tabela: config_sistema_impostos - Configurações gerais
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'chave' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ],
            'valor' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('chave');
        $this->dbforge->create_table('config_sistema_impostos', TRUE, ['ENGINE' => 'InnoDB']);

        // Inserir configurações padrão do Simples Nacional
        $this->seedImpostosConfig();
        $this->seedConfigSistema();
    }

    public function down()
    {
        $this->dbforge->drop_table('config_sistema_impostos', TRUE);
        $this->dbforge->drop_table('impostos_retidos', TRUE);
        $this->dbforge->drop_table('impostos_config', TRUE);
    }

    /**
     * Seed com alíquotas do Simples Nacional 2024
     * Anexo III - Empresas de Serviços (Maioria das empresas de OS)
     */
    private function seedImpostosConfig()
    {
        $now = date('Y-m-d H:i:s');

        // ANEXO III - Serviços (Prestadora de serviços sem ISS próprio)
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

        // ANEXO IV - Construção e Serviços com ISS próprio
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

    private function seedConfigSistema()
    {
        $configs = [
            [
                'chave' => 'IMPOSTO_ANEXO_PADRAO',
                'valor' => 'III',
                'descricao' => 'Anexo do Simples Nacional padrão para a empresa'
            ],
            [
                'chave' => 'IMPOSTO_FAIXA_ATUAL',
                'valor' => '1',
                'descricao' => 'Faixa de faturamento atual (1-5)'
            ],
            [
                'chave' => 'IMPOSTO_RETENCAO_AUTOMATICA',
                'valor' => '1',
                'descricao' => 'Habilitar retenção automática em novos boletos (1=Sim, 0=Não)'
            ],
            [
                'chave' => 'IMPOSTO_DRE_INTEGRACAO',
                'valor' => '1',
                'descricao' => 'Integrar retenções automaticamente com DRE (1=Sim, 0=Não)'
            ],
            [
                'chave' => 'IMPOSTO_ISS_MUNICIPAL',
                'valor' => '5.00',
                'descricao' => 'Alíquota de ISS municipal para cálculo isolado (%)'
            ],
        ];

        foreach ($configs as $c) {
            $this->db->insert('config_sistema_impostos', $c);
        }
    }
}
