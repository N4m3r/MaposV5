<?php
/**
 * Migration: Adiciona alíquotas do Anexo V (Comércio e Indústria)
 * CNAE 4751201 - Comércio varejista de materiais de construção
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_anexo_v_aliquotas extends CI_Migration {

    public function up()
    {
        // Verificar se a tabela existe
        if ($this->db->table_exists('impostos_config')) {
            // Inserir alíquotas do Anexo V - Comércio
            // Alíquotas 2024 para comércio varejista (Anexo V)
            $aliquotas = [
                [
                    'anexo' => 'V',
                    'faixa' => 1,
                    'aliquota_nominal' => 4.00,
                    'irpj' => 0.80,
                    'csll' => 0.80,
                    'cofins' => 2.34,
                    'pis' => 0.50,
                    'cpp' => 0.00,
                    'iss' => 0.00,
                    'ativo' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'anexo' => 'V',
                    'faixa' => 2,
                    'aliquota_nominal' => 7.30,
                    'irpj' => 1.46,
                    'csll' => 1.46,
                    'cofins' => 2.67,
                    'pis' => 0.63,
                    'cpp' => 0.00,
                    'iss' => 0.00,
                    'ativo' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'anexo' => 'V',
                    'faixa' => 3,
                    'aliquota_nominal' => 9.50,
                    'irpj' => 1.90,
                    'csll' => 1.90,
                    'cofins' => 3.27,
                    'pis' => 0.77,
                    'cpp' => 0.00,
                    'iss' => 0.00,
                    'ativo' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'anexo' => 'V',
                    'faixa' => 4,
                    'aliquota_nominal' => 10.74,
                    'irpj' => 2.15,
                    'csll' => 2.15,
                    'cofins' => 3.46,
                    'pis' => 0.82,
                    'cpp' => 0.00,
                    'iss' => 0.00,
                    'ativo' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'anexo' => 'V',
                    'faixa' => 5,
                    'aliquota_nominal' => 11.78,
                    'irpj' => 2.36,
                    'csll' => 2.36,
                    'cofins' => 3.67,
                    'pis' => 0.87,
                    'cpp' => 0.00,
                    'iss' => 0.00,
                    'ativo' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];

            foreach ($aliquotas as $aliquota) {
                // Verificar se já existe
                $this->db->where('anexo', $aliquota['anexo']);
                $this->db->where('faixa', $aliquota['faixa']);
                $query = $this->db->get('impostos_config');

                if ($query->num_rows() == 0) {
                    $this->db->insert('impostos_config', $aliquota);
                }
            }
        }
    }

    public function down()
    {
        // Remover alíquotas do Anexo V
        if ($this->db->table_exists('impostos_config')) {
            $this->db->where('anexo', 'V');
            $this->db->delete('impostos_config');
        }
    }
}
