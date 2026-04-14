<?php
/**
 * Migration: Sistema de Certificado Digital
 * Armazenamento e integração com certificados A1/A3
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_certificado_digital extends CI_Migration {

    public function up()
    {
        // Tabela: certificado_digital - Configuração do certificado
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['A1', 'A3'],
                'default' => 'A1',
                'comment' => 'Tipo do certificado'
            ],
            'cnpj' => [
                'type' => 'VARCHAR',
                'constraint' => 14,
                'null' => FALSE,
                'comment' => 'CNPJ do titular'
            ],
            'razao_social' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'nome_fantasia' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'arquivo_caminho' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE,
                'comment' => 'Caminho do arquivo PFX (A1)'
            ],
            'arquivo_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'comment' => 'Hash para verificação de integridade'
            ],
            'senha' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Senha criptografada do certificado'
            ],
            'data_validade' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'data_emissao' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'emissor' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'ultimo_acesso' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'ultimo_erro' => [
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
        $this->dbforge->add_key('cnpj');
        $this->dbforge->create_table('certificado_digital', TRUE, ['ENGINE' => 'InnoDB']);

        // Tabela: certificado_consultas - Log de consultas à Receita
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'certificado_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ],
            'tipo_consulta' => [
                'type' => 'ENUM',
                'constraint' => ['CNPJ', 'SIMPLES_NACIONAL', 'NFE', 'NFSE', 'SITUACAO_CADASTRO'],
                'null' => FALSE
            ],
            'data_consulta' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ],
            'sucesso' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'dados_retorno' => [
                'type' => 'LONGTEXT',
                'null' => TRUE,
                'comment' => 'JSON com dados retornados'
            ],
            'erro' => [
                'type' => 'TEXT',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('certificado_id');
        $this->dbforge->create_table('certificado_consultas', TRUE, ['ENGINE' => 'InnoDB']);

        // Tabela: certificado_nfe_importada - Notas fiscais importadas
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'certificado_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ],
            'chave_acesso' => [
                'type' => 'VARCHAR',
                'constraint' => 44,
                'null' => FALSE
            ],
            'numero' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'serie' => [
                'type' => 'VARCHAR',
                'constraint' => 10
            ],
            'data_emissao' => [
                'type' => 'DATETIME'
            ],
            'data_importacao' => [
                'type' => 'DATETIME'
            ],
            'cnpj_destinatario' => [
                'type' => 'VARCHAR',
                'constraint' => 14
            ],
            'valor_total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2'
            ],
            'valor_impostos' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2'
            ],
            'xml_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'situacao' => [
                'type' => 'ENUM',
                'constraint' => ['Autorizada', 'Cancelada', 'Denegada', 'Inutilizada'],
                'default' => 'Autorizada'
            ],
            'imposto_integrado' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Se já foi lançado no sistema de impostos'
            ],
            'dados_xml' => [
                'type' => 'LONGTEXT',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('chave_acesso');
        $this->dbforge->create_table('certificado_nfe_importada', TRUE, ['ENGINE' => 'InnoDB']);

        // Adicionar FKs
        $this->db->query('ALTER TABLE `certificado_consultas`
            ADD CONSTRAINT `fk_consulta_certificado` FOREIGN KEY (`certificado_id`) REFERENCES `certificado_digital`(`id`) ON DELETE CASCADE
        ');

        $this->db->query('ALTER TABLE `certificado_nfe_importada`
            ADD CONSTRAINT `fk_nfe_certificado` FOREIGN KEY (`certificado_id`) REFERENCES `certificado_digital`(`id`) ON DELETE CASCADE
        ');
    }

    public function down()
    {
        $this->dbforge->drop_table('certificado_nfe_importada', TRUE);
        $this->dbforge->drop_table('certificado_consultas', TRUE);
        $this->dbforge->drop_table('certificado_digital', TRUE);
    }
}
