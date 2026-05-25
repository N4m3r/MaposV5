<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Convert serialized permissions to JSON format
 * Security fix: unserialize() is vulnerable to object injection attacks.
 * This migration converts all PHP serialized permission data to JSON.
 */
class Migration_Migrate_permissions_to_json extends CI_Migration
{
    public function up()
    {
        // Migrar permissoes da tabela 'permissoes'
        if ($this->db->table_exists('permissoes')) {
            $permissoes = $this->db->get('permissoes')->result();

            foreach ($permissoes as $row) {
                if (empty($row->permissoes)) {
                    continue;
                }

                // Verificar se ja esta em formato JSON
                $decoded = json_decode($row->permissoes, true);
                if ($decoded !== null) {
                    continue; // Ja e JSON, pular
                }

                // Converter de PHP serialized para JSON
                $unserialized = @unserialize($row->permissoes);
                if ($unserialized !== false) {
                    $json = json_encode($unserialized);
                    $this->db->where('idPermissao', $row->idPermissao);
                    $this->db->update('permissoes', ['permissoes' => $json]);
                }
            }
        }

        // Migrar permissoes da tabela 'usuarios_cliente' se existir
        if ($this->db->table_exists('usuarios_cliente')) {
            if ($this->db->field_exists('permissoes', 'usuarios_cliente')) {
                $usuarios = $this->db->get('usuarios_cliente')->result();

                foreach ($usuarios as $row) {
                    if (empty($row->permissoes)) {
                        continue;
                    }

                    $decoded = json_decode($row->permissoes, true);
                    if ($decoded !== null) {
                        continue;
                    }

                    $unserialized = @unserialize($row->permissoes);
                    if ($unserialized !== false) {
                        $json = json_encode($unserialized);
                        $this->db->where('id', $row->id);
                        $this->db->update('usuarios_cliente', ['permissoes' => $json]);
                    }
                }
            }
        }

        // Migrar tokens de reset de senha na tabela resets_de_senha se existir
        // (Nao precisa - tokens ja sao strings simples, nao serialized)
    }

    public function down()
    {
        // Reverter: converter JSON de volta para PHP serialized
        if ($this->db->table_exists('permissoes')) {
            $permissoes = $this->db->get('permissoes')->result();

            foreach ($permissoes as $row) {
                if (empty($row->permissoes)) {
                    continue;
                }

                $decoded = json_decode($row->permissoes, true);
                if ($decoded === null) {
                    continue; // Nao e JSON, pular
                }

                $serialized = serialize($decoded);
                $this->db->where('idPermissao', $row->idPermissao);
                $this->db->update('permissoes', ['permissoes' => $serialized]);
            }
        }

        if ($this->db->table_exists('usuarios_cliente')) {
            if ($this->db->field_exists('permissoes', 'usuarios_cliente')) {
                $usuarios = $this->db->get('usuarios_cliente')->result();

                foreach ($usuarios as $row) {
                    if (empty($row->permissoes)) {
                        continue;
                    }

                    $decoded = json_decode($row->permissoes, true);
                    if ($decoded === null) {
                        continue;
                    }

                    $serialized = serialize($decoded);
                    $this->db->where('id', $row->id);
                    $this->db->update('usuarios_cliente', ['permissoes' => $serialized]);
                }
            }
        }
    }
}