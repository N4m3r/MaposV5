<?php
/**
 * Migration: Adicionar permissões para vincular documentos fiscais à OS
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_permissoes_documentos_os extends CI_Migration {

    public function up()
    {
        $permissoes = [
            [
                'nome' => 'Visualizar Documentos OS',
                'permissoes' => ['vDocOs' => 1]
            ],
            [
                'nome' => 'Vincular Documentos OS',
                'permissoes' => ['cDocOs' => 1]
            ],
            [
                'nome' => 'Desvincular Documentos OS',
                'permissoes' => ['dDocOs' => 1]
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
            'Visualizar Documentos OS',
            'Vincular Documentos OS',
            'Desvincular Documentos OS',
        ];

        foreach ($nomes as $nome) {
            $this->db->where('nome', $nome);
            $this->db->delete('permissoes');
        }
    }
}
