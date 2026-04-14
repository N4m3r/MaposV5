<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_permissao_relatorio_tecnicos extends CI_Migration {

    public function up()
    {
        // Adiciona a permissão vRelatorioTecnicos para o relatório de performance dos técnicos
        // Verifica se já existe permissão com este nome
        $this->db->where('nome', 'Visualizar Relatório de Técnicos');
        $exists = $this->db->get('permissoes');

        if ($exists->num_rows() == 0) {
            $permissoes = [
                'vRelatorioTecnicos' => 1,
            ];

            $this->db->insert('permissoes', [
                'nome' => 'Visualizar Relatório de Técnicos',
                'data' => date('Y-m-d'),
                'permissoes' => serialize($permissoes),
                'situacao' => 1,
            ]);
        }
    }

    public function down()
    {
        // Remove a permissão
        $this->db->where('nome', 'Visualizar Relatório de Técnicos');
        $this->db->delete('permissoes');
    }
}
