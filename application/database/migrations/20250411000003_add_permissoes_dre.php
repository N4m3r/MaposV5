<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_permissoes_dre extends CI_Migration {

    public function up()
    {
        $permissoes = [
            [
                'nome' => 'Visualizar DRE',
                'permissoes' => ['vDRE' => 1]
            ],
            [
                'nome' => 'Visualizar Relatório DRE',
                'permissoes' => ['vDRERelatorio' => 1]
            ],
            [
                'nome' => 'Cadastrar Conta DRE',
                'permissoes' => ['cDREConta' => 1]
            ],
            [
                'nome' => 'Deletar Conta DRE',
                'permissoes' => ['dDREConta' => 1]
            ],
            [
                'nome' => 'Visualizar Lançamentos DRE',
                'permissoes' => ['vDRELancamento' => 1]
            ],
            [
                'nome' => 'Cadastrar Lançamento DRE',
                'permissoes' => ['cDRELancamento' => 1]
            ],
            [
                'nome' => 'Deletar Lançamento DRE',
                'permissoes' => ['dDRELancamento' => 1]
            ],
            [
                'nome' => 'Integrar Dados DRE',
                'permissoes' => ['cDREIntegracao' => 1]
            ],
            [
                'nome' => 'Exportar DRE',
                'permissoes' => ['vDREExportar' => 1]
            ],
            [
                'nome' => 'Análise DRE',
                'permissoes' => ['vDREAnalise' => 1]
            ],
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
    }

    public function down()
    {
        $nomes = [
            'Visualizar DRE',
            'Visualizar Relatório DRE',
            'Cadastrar Conta DRE',
            'Deletar Conta DRE',
            'Visualizar Lançamentos DRE',
            'Cadastrar Lançamento DRE',
            'Deletar Lançamento DRE',
            'Integrar Dados DRE',
            'Exportar DRE',
            'Análise DRE'
        ];

        foreach ($nomes as $nome) {
            $this->db->where('nome', $nome);
            $this->db->delete('permissoes');
        }
    }
}
