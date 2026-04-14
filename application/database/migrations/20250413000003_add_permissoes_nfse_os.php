<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Permissões para Sistema de NFS-e e Boletos vinculados à OS
 * Adiciona permissões necessárias para emissão e gestão de notas e cobranças
 */

class Migration_add_permissoes_nfse_os extends CI_Migration {

    public function up()
    {
        $permissoes = [
            [
                'nome' => 'Visualizar NFSe (OS)',
                'permissoes' => ['vNFSe' => 1]
            ],
            [
                'nome' => 'Cadastrar NFSe (OS)',
                'permissoes' => ['cNFSe' => 1]
            ],
            [
                'nome' => 'Editar NFSe (OS)',
                'permissoes' => ['eNFSe' => 1]
            ],
            [
                'nome' => 'Visualizar Boleto OS',
                'permissoes' => ['vBoletoOS' => 1]
            ],
            [
                'nome' => 'Cadastrar Boleto OS',
                'permissoes' => ['cBoletoOS' => 1]
            ],
            [
                'nome' => 'Editar Boleto OS',
                'permissoes' => ['eBoletoOS' => 1]
            ],
            [
                'nome' => 'Relatório NFSe',
                'permissoes' => ['rNFSe' => 1]
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
            'Visualizar NFSe (OS)',
            'Cadastrar NFSe (OS)',
            'Editar NFSe (OS)',
            'Visualizar Boleto OS',
            'Cadastrar Boleto OS',
            'Editar Boleto OS',
            'Relatório NFSe',
        ];

        foreach ($nomes as $nome) {
            $this->db->where('nome', $nome);
            $this->db->delete('permissoes');
        }
    }
}
