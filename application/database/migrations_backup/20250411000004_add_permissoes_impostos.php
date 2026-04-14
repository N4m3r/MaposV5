<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_permissoes_impostos extends CI_Migration {

    public function up()
    {
        $permissoes = [
            [
                'nome' => 'Visualizar Impostos',
                'permissoes' => ['vImpostos' => 1]
            ],
            [
                'nome' => 'Visualizar Relatório Impostos',
                'permissoes' => ['vImpostosRelatorio' => 1]
            ],
            [
                'nome' => 'Configurar Impostos',
                'permissoes' => ['cImpostosConfig' => 1]
            ],
            [
                'nome' => 'Editar Impostos',
                'permissoes' => ['eImpostos' => 1]
            ],
            [
                'nome' => 'Exportar Impostos',
                'permissoes' => ['vImpostosExportar' => 1]
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
            'Visualizar Impostos',
            'Visualizar Relatório Impostos',
            'Configurar Impostos',
            'Editar Impostos',
            'Exportar Impostos',
        ];

        foreach ($nomes as $nome) {
            $this->db->where('nome', $nome);
            $this->db->delete('permissoes');
        }
    }
}
