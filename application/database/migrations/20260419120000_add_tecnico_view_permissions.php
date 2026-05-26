<?php

/**
 * Migration: Add vTecnicoFotos and vTecnicoAssinaturas permissions
 * Data: 2026-04-19
 *
 * Adiciona permissões de visualização de fotos e assinaturas para o grupo Técnico
 */

class Migration_Add_tecnico_view_permissions extends CI_Migration
{
    private function decodePermissions($raw)
    {
        if (empty($raw)) return [];
        $decoded = @json_decode($raw, true);
        if (is_array($decoded)) return $decoded;
        $decoded = @unserialize($raw);
        return is_array($decoded) ? $decoded : [];
    }

    public function up()
    {
        // Atualizar grupo Administrador (id=1) com as novas permissões
        $admin = $this->db->where('idPermissao', 1)->get('permissoes')->row();
        if ($admin) {
            $perms = $this->decodePermissions($admin->permissoes);
            $perms['vTecnicoFotos'] = '1';
            $perms['vTecnicoAssinaturas'] = '1';
            $this->db->where('idPermissao', 1)->update('permissoes', ['permissoes' => json_encode($perms)]);
        }

        // Atualizar grupo Técnico com as novas permissões
        $tecnico = $this->db->where('nome', 'Técnico')->get('permissoes')->row();
        if ($tecnico) {
            $perms = $this->decodePermissions($tecnico->permissoes);
            $perms['vTecnicoFotos'] = '1';
            $perms['vTecnicoAssinaturas'] = '1';
            $this->db->where('nome', 'Técnico')->update('permissoes', ['permissoes' => json_encode($perms)]);
        }

        echo "Permissões vTecnicoFotos e vTecnicoAssinaturas adicionadas com sucesso!\n";
    }

    public function down()
    {
        // Reverter as alterações se necessário
        $admin = $this->db->where('idPermissao', 1)->get('permissoes')->row();
        if ($admin) {
            $perms = $this->decodePermissions($admin->permissoes);
            unset($perms['vTecnicoFotos']);
            unset($perms['vTecnicoAssinaturas']);
            $this->db->where('idPermissao', 1)->update('permissoes', ['permissoes' => json_encode($perms)]);
        }

        $tecnico = $this->db->where('nome', 'Técnico')->get('permissoes')->row();
        if ($tecnico) {
            $perms = $this->decodePermissions($tecnico->permissoes);
            unset($perms['vTecnicoFotos']);
            unset($perms['vTecnicoAssinaturas']);
            $this->db->where('nome', 'Técnico')->update('permissoes', ['permissoes' => json_encode($perms)]);
        }
    }
}