<?php

class PermissoesNfseSeeder extends Seeder
{
    private $table = 'permissoes';

    public function run()
    {
        echo 'Running PermissoesNfse Seeder';

        // Inserir permissões individuais do sistema NFSe + Boleto
        $permissoes = [
            [
                'idPermissao' => null,
                'nome' => 'Visualizar NFSe (OS)',
                'permissoes' => serialize(['vNFSe' => 1]),
                'situacao' => 1,
                'data' => date('Y-m-d'),
            ],
            [
                'idPermissao' => null,
                'nome' => 'Cadastrar NFSe (OS)',
                'permissoes' => serialize(['cNFSe' => 1]),
                'situacao' => 1,
                'data' => date('Y-m-d'),
            ],
            [
                'idPermissao' => null,
                'nome' => 'Editar NFSe (OS)',
                'permissoes' => serialize(['eNFSe' => 1]),
                'situacao' => 1,
                'data' => date('Y-m-d'),
            ],
            [
                'idPermissao' => null,
                'nome' => 'Visualizar Boleto OS',
                'permissoes' => serialize(['vBoletoOS' => 1]),
                'situacao' => 1,
                'data' => date('Y-m-d'),
            ],
            [
                'idPermissao' => null,
                'nome' => 'Cadastrar Boleto OS',
                'permissoes' => serialize(['cBoletoOS' => 1]),
                'situacao' => 1,
                'data' => date('Y-m-d'),
            ],
            [
                'idPermissao' => null,
                'nome' => 'Editar Boleto OS',
                'permissoes' => serialize(['eBoletoOS' => 1]),
                'situacao' => 1,
                'data' => date('Y-m-d'),
            ],
            [
                'idPermissao' => null,
                'nome' => 'Relatório NFSe',
                'permissoes' => serialize(['rNFSe' => 1]),
                'situacao' => 1,
                'data' => date('Y-m-d'),
            ],
        ];

        foreach ($permissoes as $p) {
            // Verificar se já existe
            $this->db->where('nome', $p['nome']);
            $exists = $this->db->get($this->table);

            if ($exists->num_rows() == 0) {
                $this->db->insert($this->table, $p);
            }
        }

        echo PHP_EOL;
    }
}
