<?php
/**
 * Migration: Permissões para gerenciar usuários do portal do cliente
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_permissoes_usuarios_cliente extends CI_Migration {

    public function up()
    {
        $permissoes = [
            [
                'nome' => 'Visualizar Usuários Cliente',
                'permissoes' => ['vUsuariosCliente' => 1]
            ],
            [
                'nome' => 'Adicionar Usuários Cliente',
                'permissoes' => ['cUsuariosCliente' => 1]
            ],
            [
                'nome' => 'Editar Usuários Cliente',
                'permissoes' => ['eUsuariosCliente' => 1]
            ],
            [
                'nome' => 'Remover Usuários Cliente',
                'permissoes' => ['dUsuariosCliente' => 1]
            ],
            [
                'nome' => 'Configurar Permissões Usuários Cliente',
                'permissoes' => ['cPermUsuariosCliente' => 1]
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
            'Visualizar Usuários Cliente',
            'Adicionar Usuários Cliente',
            'Editar Usuários Cliente',
            'Remover Usuários Cliente',
            'Configurar Permissões Usuários Cliente',
        ];

        foreach ($nomes as $nome) {
            $this->db->where('nome', $nome);
            $this->db->delete('permissoes');
        }
    }
}
