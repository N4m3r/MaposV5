<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Permissões para Sistema de NFS-e e Boletos vinculados à OS
 * NOTA: As permissões são controladas via checkboxes na categoria "NFSe e Boletos"
 * dentro de cada grupo de permissão (editarPermissao.php e adicionarPermissao.php)
 * Não são mais criados registros individuais na tabela permissoes
 */

class Migration_add_permissoes_nfse_os extends CI_Migration {

    public function up()
    {
        // As permissões vNFSe, cNFSe, eNFSe, rNFSe, vBoletoOS, cBoletoOS, eBoletoOS, pBoletoOS
        // são gerenciadas via checkboxes na view de edição/criação de permissões
        // Não é mais necessário criar registros individuais na tabela permissoes

        // Opcional: Remover permissões individuais antigas se existirem
        $permissoes_antigas = [
            'Visualizar NFSe (OS)',
            'Cadastrar NFSe (OS)',
            'Editar NFSe (OS)',
            'Visualizar Boleto OS',
            'Cadastrar Boleto OS',
            'Editar Boleto OS',
            'Relatório NFSe',
        ];

        foreach ($permissoes_antigas as $nome) {
            $this->db->where('nome', $nome);
            $this->db->delete('permissoes');
        }
    }

    public function down()
    {
        // Nada a desfazer - as permissões são gerenciadas via checkboxes
    }
}
