<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Add_agente_ia_permissoes
 * Adiciona as permissoes vAgenteIA e cAgenteIA ao grupo Administrador
 * e insere templates individuais na tabela permissoes.
 *
 * Data: 2026-05-03
 */
class Migration_Add_agente_ia_permissoes extends CI_Migration
{
    public function up()
    {
        // ================================================================
        // 1. Templates de permissoes individuais (para criacao de novos grupos)
        //    Segue o padrao usado em database/sql/permissoes_nfse.sql
        // ================================================================
        $this->db->query("INSERT IGNORE INTO permissoes (nome, permissoes, situacao, data) VALUES
            ('Visualizar Painel Agente IA', 'a:1:{s:9:\"vAgenteIA\";i:1;}', 1, CURDATE()),
            ('Configurar Agente IA',        'a:1:{s:9:\"cAgenteIA\";i:1;}', 1, CURDATE()),
            ('Autorizar/Rejeitar Agente IA','a:1:{s:9:\"eAgenteIA\";i:1;}', 1, CURDATE())");

        // ================================================================
        // 2. Atualiza grupo Administrador (idPermissao = 1)
        //    Usa PHP unserialize/serialize para nao quebrar o formato.
        // ================================================================
        $this->adicionarAoGrupo(1, [
            'vAgenteIA' => '1',
            'cAgenteIA' => '1',
            'eAgenteIA' => '1',
        ]);

        // ================================================================
        // 3. Atualiza demais grupos ativos que historicamente usam o painel
        //    (tecnico=2, financeiro=3, vendedor=4 — se existirem no banco)
        //    Apenas vAgenteIA (visualizacao) para nao dar acesso de config
        //    a todos os perfis.
        // ================================================================
        foreach ([2, 3, 4] as $id) {
            $existe = $this->db->where('idPermissao', $id)->count_all_results('permissoes');
            if ($existe > 0) {
                $this->adicionarAoGrupo($id, ['vAgenteIA' => '1']);
            }
        }
    }

    public function down()
    {
        // ================================================================
        // Reverte: remove vAgenteIA e cAgenteIA dos grupos afetados
        // ================================================================
        foreach ([1, 2, 3, 4] as $id) {
            $this->removerDoGrupo($id, ['vAgenteIA', 'cAgenteIA', 'eAgenteIA']);
        }
    }

    // =====================================================================
    // Helpers
    // =====================================================================

    /**
     * Adiciona chaves de permissao a um grupo existente via serialize PHP.
     */
    private function adicionarAoGrupo(int $idPermissao, array $novas): void
    {
        $row = $this->db->where('idPermissao', $idPermissao)->get('permissoes')->row();
        if (!$row || empty($row->permissoes)) {
            return;
        }

        $perms = @unserialize($row->permissoes);
        if (!is_array($perms)) {
            $perms = [];
        }

        foreach ($novas as $key => $val) {
            $perms[$key] = $val;
        }

        $this->db->where('idPermissao', $idPermissao);
        $this->db->update('permissoes', ['permissoes' => serialize($perms)]);
    }

    /**
     * Remove chaves de permissao de um grupo existente via serialize PHP.
     */
    private function removerDoGrupo(int $idPermissao, array $chaves): void
    {
        $row = $this->db->where('idPermissao', $idPermissao)->get('permissoes')->row();
        if (!$row || empty($row->permissoes)) {
            return;
        }

        $perms = @unserialize($row->permissoes);
        if (!is_array($perms)) {
            return;
        }

        foreach ($chaves as $key) {
            unset($perms[$key]);
        }

        $this->db->where('idPermissao', $idPermissao);
        $this->db->update('permissoes', ['permissoes' => serialize($perms)]);
    }
}
