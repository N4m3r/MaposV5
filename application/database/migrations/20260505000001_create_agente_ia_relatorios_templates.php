<?php
/**
 * Migration: Create agente_ia_relatorios_templates table
 * Tabela para templates de relatorio editaveis no painel do Agente IA
 */

class Migration_create_agente_ia_relatorios_templates extends CI_Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `agente_ia_relatorios_templates` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `nome` VARCHAR(100) NOT NULL,
            `tipo` VARCHAR(50) NOT NULL COMMENT 'os_diario, os_mensal, financeiro, vendas, clientes, estoque, tecnico, inadimplencia, satisfacao, historico_cliente',
            `descricao` VARCHAR(255) NULL,
            `conteudo_html` LONGTEXT NOT NULL COMMENT 'HTML/Handlebars do template',
            `ativo` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NULL,
            INDEX `idx_tipo` (`tipo`),
            INDEX `idx_ativo` (`ativo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // Seed com template basico para cada tipo
        $seed = [
            [
                'nome' => 'Relatorio OS Diario - Basico',
                'tipo' => 'os_diario',
                'descricao' => 'Template padrao para relatorio diario de OS',
                'conteudo_html' => '<h2>Relatorio de OS - {{periodo_inicio}}</h2><p>Cliente: {{cliente_nome}}</p><table border="1"><tr><th>OS</th><th>Status</th><th>Tecnico</th><th>Valor</th></tr>{{#each itens}}<tr><td>{{idOs}}</td><td>{{status}}</td><td>{{tecnico}}</td><td>R$ {{valor}}</td></tr>{{/each}}</table><p>Total: {{total}} | Valor: R$ {{valor}}</p>',
                'ativo' => 1,
            ],
            [
                'nome' => 'Relatorio Financeiro - Basico',
                'tipo' => 'financeiro',
                'descricao' => 'Template padrao para resumo financeiro',
                'conteudo_html' => '<h2>Resumo Financeiro</h2><p>Periodo: {{periodo_inicio}} ate {{periodo_fim}}</p><p>A Receber: R$ {{a_receber}} | A Pagar: R$ {{a_pagar}} | Recebido: R$ {{recebido}}</p>',
                'ativo' => 1,
            ],
        ];

        foreach ($seed as $row) {
            $this->db->insert('agente_ia_relatorios_templates', $row);
        }
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `agente_ia_relatorios_templates`");
    }
}
