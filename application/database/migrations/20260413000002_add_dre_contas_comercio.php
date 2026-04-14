<?php
/**
 * Migration: Adiciona contas DRE específicas para Comércio de Materiais de Construção
 * CNAE 4751201 - Comércio varejista de materiais de construção
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_dre_contas_comercio extends CI_Migration {

    public function up()
    {
        // Verificar se a tabela existe
        if ($this->db->table_exists('dre_contas')) {
            $now = date('Y-m-d H:i:s');

            // Contas específicas para comércio de materiais de construção
            $contas_comercio = [
                // RECEITA BRUTA - Vendas de Materiais
                [
                    'codigo' => '1.4',
                    'nome' => 'Vendas de Materiais de Construção',
                    'tipo' => 'RECEITA',
                    'grupo' => 'RECEITA_BRUTA',
                    'ordem' => 14,
                    'conta_pai_id' => NULL,
                    'nivel' => 2,
                    'sinal' => 'POSITIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '1.4.1',
                    'nome' => 'Vendas de Cimento e Argamassa',
                    'tipo' => 'RECEITA',
                    'grupo' => 'RECEITA_BRUTA',
                    'ordem' => 141,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'POSITIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '1.4.2',
                    'nome' => 'Vendas de Tijolos e Blocos',
                    'tipo' => 'RECEITA',
                    'grupo' => 'RECEITA_BRUTA',
                    'ordem' => 142,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'POSITIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '1.4.3',
                    'nome' => 'Vendas de Tintas e Vernizes',
                    'tipo' => 'RECEITA',
                    'grupo' => 'RECEITA_BRUTA',
                    'ordem' => 143,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'POSITIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '1.4.4',
                    'nome' => 'Vendas de Ferragens e Metais',
                    'tipo' => 'RECEITA',
                    'grupo' => 'RECEITA_BRUTA',
                    'ordem' => 144,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'POSITIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '1.4.5',
                    'nome' => 'Vendas de Materiais Elétricos',
                    'tipo' => 'RECEITA',
                    'grupo' => 'RECEITA_BRUTA',
                    'ordem' => 145,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'POSITIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '1.4.6',
                    'nome' => 'Vendas de Madeiras e Esquadrias',
                    'tipo' => 'RECEITA',
                    'grupo' => 'RECEITA_BRUTA',
                    'ordem' => 146,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'POSITIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],

                // DEDUÇÕES - ICMS e Outros Impostos sobre Vendas
                [
                    'codigo' => '2.4',
                    'nome' => 'ICMS sobre Vendas',
                    'tipo' => 'IMPOSTO',
                    'grupo' => 'DEDUCOES',
                    'ordem' => 24,
                    'conta_pai_id' => NULL,
                    'nivel' => 2,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '2.5',
                    'nome' => 'PIS/COFINS sobre Vendas',
                    'tipo' => 'IMPOSTO',
                    'grupo' => 'DEDUCOES',
                    'ordem' => 25,
                    'conta_pai_id' => NULL,
                    'nivel' => 2,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],

                // CUSTOS - Custo das Mercadorias Vendidas (CMV)
                [
                    'codigo' => '4.4',
                    'nome' => 'Custo das Mercadorias Vendidas',
                    'tipo' => 'CUSTO',
                    'grupo' => 'CUSTO',
                    'ordem' => 44,
                    'conta_pai_id' => NULL,
                    'nivel' => 2,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '4.4.1',
                    'nome' => 'Custo - Cimentos e Argamassas',
                    'tipo' => 'CUSTO',
                    'grupo' => 'CUSTO',
                    'ordem' => 441,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '4.4.2',
                    'nome' => 'Custo - Tijolos e Blocos',
                    'tipo' => 'CUSTO',
                    'grupo' => 'CUSTO',
                    'ordem' => 442,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '4.4.3',
                    'nome' => 'Custo - Tintas e Vernizes',
                    'tipo' => 'CUSTO',
                    'grupo' => 'CUSTO',
                    'ordem' => 443,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '4.4.4',
                    'nome' => 'Custo - Ferragens e Metais',
                    'tipo' => 'CUSTO',
                    'grupo' => 'CUSTO',
                    'ordem' => 444,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '4.4.5',
                    'nome' => 'Custo - Materiais Elétricos',
                    'tipo' => 'CUSTO',
                    'grupo' => 'CUSTO',
                    'ordem' => 445,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '4.4.6',
                    'nome' => 'Custo - Madeiras e Esquadrias',
                    'tipo' => 'CUSTO',
                    'grupo' => 'CUSTO',
                    'ordem' => 446,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],

                // DESPESAS OPERACIONAIS - Despesas com Vendas
                [
                    'codigo' => '6.3',
                    'nome' => 'Despesas com Vendas',
                    'tipo' => 'DESPESA',
                    'grupo' => 'DESPESA_OPERACIONAL',
                    'ordem' => 63,
                    'conta_pai_id' => NULL,
                    'nivel' => 2,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '6.3.1',
                    'nome' => 'Frete e Carretos',
                    'tipo' => 'DESPESA',
                    'grupo' => 'DESPESA_OPERACIONAL',
                    'ordem' => 631,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '6.3.2',
                    'nome' => 'Comissões de Vendedores',
                    'tipo' => 'DESPESA',
                    'grupo' => 'DESPESA_OPERACIONAL',
                    'ordem' => 632,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '6.3.3',
                    'nome' => 'Despesas com Armazenagem',
                    'tipo' => 'DESPESA',
                    'grupo' => 'DESPESA_OPERACIONAL',
                    'ordem' => 633,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'codigo' => '6.3.4',
                    'nome' => 'Perdas e Quebras de Estoque',
                    'tipo' => 'DESPESA',
                    'grupo' => 'DESPESA_OPERACIONAL',
                    'ordem' => 634,
                    'conta_pai_id' => NULL,
                    'nivel' => 3,
                    'sinal' => 'NEGATIVO',
                    'ativo' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
            ];

            foreach ($contas_comercio as $conta) {
                // Verificar se o código já existe
                $this->db->where('codigo', $conta['codigo']);
                $query = $this->db->get('dre_contas');

                if ($query->num_rows() == 0) {
                    $this->db->insert('dre_contas', $conta);
                }
            }
        }
    }

    public function down()
    {
        // Remover contas específicas de comércio
        if ($this->db->table_exists('dre_contas')) {
            $codigos = ['1.4', '1.4.1', '1.4.2', '1.4.3', '1.4.4', '1.4.5', '1.4.6',
                       '2.4', '2.5',
                       '4.4', '4.4.1', '4.4.2', '4.4.3', '4.4.4', '4.4.5', '4.4.6',
                       '6.3', '6.3.1', '6.3.2', '6.3.3', '6.3.4'];

            foreach ($codigos as $codigo) {
                $this->db->where('codigo', $codigo);
                $this->db->delete('dre_contas');
            }
        }
    }
}
