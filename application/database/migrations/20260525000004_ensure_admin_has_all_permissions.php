<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Ensure admin role (idPermissao=1) has all permissions explicitly
 * This is required because the idPermissao==1 bypass was removed from Permission.php.
 * The admin role must have every permission set to 1 in its JSON.
 */
class Migration_Ensure_admin_has_all_permissions extends CI_Migration
{
    /**
     * All known permission keys used in the system
     */
    private $allPermissions = [
        'aCliente', 'vCliente', 'eCliente', 'dCliente', 'cCliente',
        'aOs', 'vOs', 'eOs', 'dOs',
        'aProduto', 'vProduto', 'eProduto', 'dProduto',
        'aServico', 'vServico', 'eServico', 'dServico',
        'aVenda', 'vVenda', 'eVenda', 'dVenda',
        'aArquivo', 'vArquivo', 'eArquivo', 'dArquivo',
        'aCobranca', 'vCobranca', 'eCobranca', 'dCobranca',
        'aLancamento', 'vLancamento', 'eLancamento', 'dLancamento',
        'aUsuario', 'vUsuario', 'eUsuario', 'dUsuario',
        'aPermissao', 'vPermissao', 'ePermissao', 'dPermissao',
        'aConfiguracao', 'eConfiguracao',
        'aRelatorio', 'vRelatorio',
        'vTecnicoDashboard', 'eTecnicoOs',
        'aGarantia', 'vGarantia', 'eGarantia',
        'vMapa',
        'aImportar',
        'vPainel',
        'eNfse',
        'aObra', 'vObra', 'eObra', 'dObra',
        'cAgenteIA', 'vAgenteIA', 'eAgenteIA',
        'cAuditoria',
        'lgpd_exportar', 'lgpd_anonimizar', 'lgpd_consentimento',
    ];

    public function up()
    {
        if (!$this->db->table_exists('permissoes')) {
            return;
        }

        $admin = $this->db->where('idPermissao', 1)->get('permissoes')->row();
        if (!$admin) {
            return;
        }

        // Decode current permissions
        $perms = [];
        if (!empty($admin->permissoes)) {
            $decoded = json_decode($admin->permissoes, true);
            if (is_array($decoded)) {
                $perms = $decoded;
            } else {
                $decoded = @unserialize($admin->permissoes);
                if (is_array($decoded)) {
                    $perms = $decoded;
                }
            }
        }

        // Set all known permissions to 1
        foreach ($this->allPermissions as $perm) {
            $perms[$perm] = 1;
        }

        $json = json_encode($perms);

        $this->db->where('idPermissao', 1);
        $this->db->update('permissoes', ['permissoes' => $json]);

        log_message('info', 'Migration: Admin role (idPermissao=1) updated with all permissions explicitly');
    }

    public function down()
    {
        // Cannot revert - admin must keep all permissions
    }
}