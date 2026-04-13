<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_permissoes_dre extends CI_Migration {

    public function up()
    {
        // Buscar permissão do administrador (idPermissao = 1)
        $this->db->where('idPermissao', 1);
        $query = $this->db->get('permissoes');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $permissoes = @unserialize($row->permissoes);

            if (!is_array($permissoes)) {
                $permissoes = [];
            }

            // Adicionar permissões DRE se não existirem
            $novas_permissoes = [
                'vDRE' => 1,
                'cDRE' => 1,
                'eDRE' => 1,
                'dDRE' => 1,
            ];

            $atualizado = false;
            foreach ($novas_permissoes as $key => $value) {
                if (!isset($permissoes[$key])) {
                    $permissoes[$key] = $value;
                    $atualizado = true;
                }
            }

            if ($atualizado) {
                $this->db->where('idPermissao', 1);
                $this->db->update('permissoes', [
                    'permissoes' => serialize($permissoes)
                ]);
            }
        }
    }

    public function down()
    {
        // Buscar permissão do administrador (idPermissao = 1)
        $this->db->where('idPermissao', 1);
        $query = $this->db->get('permissoes');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $permissoes = @unserialize($row->permissoes);

            if (is_array($permissoes)) {
                // Remover permissões DRE
                $permissoes_dre = ['vDRE', 'cDRE', 'eDRE', 'dDRE'];
                foreach ($permissoes_dre as $key) {
                    unset($permissoes[$key]);
                }

                $this->db->where('idPermissao', 1);
                $this->db->update('permissoes', [
                    'permissoes' => serialize($permissoes)
                ]);
            }
        }
    }
}
