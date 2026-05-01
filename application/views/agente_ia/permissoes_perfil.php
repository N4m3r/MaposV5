<?php
/**
 * View: permissoes_perfil.php
 * Gerenciamento de permissoes do agente IA por perfil
 */
?>

<style>
.perm-grid { display:flex; flex-wrap:wrap; gap:10px; }
.perm-card {
    flex:1 1 calc(33% - 10px);
    border:1px solid #ddd;
    border-radius:6px;
    padding:12px;
    background:#fff;
}
.perm-card .perfil-header {
    font-weight:700;
    font-size:1.1em;
    margin-bottom:8px;
    border-bottom:2px solid #eee;
    padding-bottom:4px;
    text-transform:capitalize;
}
.perm-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:4px 0;
    border-bottom:1px solid #f5f5f5;
    font-size:0.9em;
}
.perm-row:last-child { border-bottom:none; }
.perm-nivel-select { width:50px; margin:0 5px; }
.chk-2fa { margin-left:8px; }
</style>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-lock-alt iconX"></i></span>
                <h5>Permissoes do Agente IA por Perfil</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('agente_ia'); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                    <button type="submit" form="formPerms" class="btn btn-success btn-mini">
                        <i class="bx bx-save"></i> Salvar
                    </button>
                </div>
            </div>

            <div class="widget-content">
                <form id="formPerms" method="post" action="<?php echo site_url('agente_ia/salvar_permissoes'); ?>">
                    <div class="alert alert-info">
                        <strong>Instrucoes:</strong>
                        <ul>
                            <li><b>Nivel Max Auto:</b> ate qual nivel o agente executa sem pedir confirmacao (1-5).</li>
                            <li><b>2FA:</b> se marcado, acao requer senha ou codigo por email/totp.</li>
                            <li>Nivel 1=leitura | 2=baixa | 3=media | 4=alta | 5=critica</li>
                        </ul>
                    </div>

                    <div class="perm-grid">
                        <?php
                        $perfis = ['cliente','tecnico','admin','financeiro','vendedor','desconhecido'];
                        foreach ($perfis as $perfil):
                            $perfilPerms = array_filter($permissoes ?? [], fn($p) => $p['perfil'] === $perfil);
                        ?>
                            <div class="perm-card">
                                <div class="perfil-header">
                                    <i class="bx bx-user-circle"></i> <?php echo ucfirst($perfil); ?>
                                </div>
                                <?php if (empty($perfilPerms)): ?
                                    <div class="muted">Nenhuma permissao definida.</div>
                                <?php else: ?
                                    <?php foreach ($perfilPerms as $perm): ?
                                        <div class="perm-row">
                                            <span><?php echo ucwords(str_replace('_', ' ', $perm['acao'])); ?></span>
                                            <span>
                                                <input type="number"
                                                       class="perm-nivel-select"
                                                       name="perms[<?php echo $perm['id']; ?>][nivel_maximo_automatico]"
                                                       value="<?php echo $perm['nivel_maximo_automatico']; ?>"
                                                       min="1" max="5"
                                                       title="Nivel maximo automatico">
                                                <label class="chk-2fa" title="Requer 2FA">
                                                    <input type="checkbox"
                                                           name="perms[<?php echo $perm['id']; ?>][requer_2fa]"
                                                           value="1"
                                                           <?php echo ($perm['requer_2fa'] ?? 0) ? 'checked' : ''; ?>
                                                    > 2FA
                                                </label>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
