<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_permissoes_certificado extends CI_Migration {

    public function up()
    {
        $permissoes = [
            [
                'nome' => 'Visualizar Certificado',
                'permissoes' => ['vCertificado' => 1]
            ],
            [
                'nome' => 'Configurar Certificado',
                'permissoes' => ['cCertificado' => 1]
            ],
            [
                'nome' => 'Editar Certificado',
                'permissoes' => ['eCertificado' => 1]
            ],
            [
                'nome' => 'Remover Certificado',
                'permissoes' => ['dCertificado' => 1]
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
            'Visualizar Certificado',
            'Configurar Certificado',
            'Editar Certificado',
            'Remover Certificado',
        ];

        foreach ($nomes as $nome) {
            $this->db->where('nome', $nome);
            $this->db->delete('permissoes');
        }
    }
}
