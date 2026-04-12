<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_permissao_exportar_dados extends CI_Migration {

    public function up()
    {
        // Adiciona a permissão vExportarDados para exportar CSV
        $this->db->where('nome', 'Exportar Dados');
        $exists = $this->db->get('permissoes');

        if ($exists->num_rows() == 0) {
            $permissoes = [
                'vExportarDados' => 1,
            ];

            $this->db->insert('permissoes', [
                'nome' => 'Exportar Dados',
                'data' => date('Y-m-d'),
                'permissoes' => serialize($permissoes),
                'situacao' => 1,
            ]);
        }
    }

    public function down()
    {
        $this->db->where('nome', 'Exportar Dados');
        $this->db->delete('permissoes');
    }
}
