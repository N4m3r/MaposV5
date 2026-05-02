<?php
/**
 * View: configuracoes.php
 * Painel de configuracoes do Agente IA
 */
?>

<style>
.config-card {
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 15px;
    background: #fff;
}
.config-card h6 {
    margin: 0 0 10px 0;
    font-size: 1em;
    text-transform: uppercase;
    color: #555;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}
.config-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f5f5f5;
}
.config-row:last-child { border-bottom: none; }
.config-row label {
    font-weight: 600;
    margin: 0;
    flex: 1;
}
.config-row .desc {
    font-size: 0.8em;
    color: #888;
    margin-top: 2px;
}
.config-row input,
.config-row select {
    width: 300px;
    margin: 0;
}
</style>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-cog iconX"></i></span>
                <h5>Configuracoes do Agente IA</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('agente_ia'); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                    <button type="submit" form="formConfigs" class="btn btn-success btn-mini">
                        <i class="bx bx-save"></i> Salvar
                    </button>
                </div>
            </div>

            <div class="widget-content">
                <form id="formConfigs" method="post" action="<?php echo site_url('agente_ia/salvar_configuracoes'); ?>">
                    <?php
                    $grupos = [];
                    foreach ($configs as $c) {
                        $grupos[$c['categoria']][] = $c;
                    }
                    $nomesCategoria = [
                        'integracao'   => 'Integracao (n8n / Evolution)',
                        'ia'           => 'Inteligencia Artificial (LLM / Whisper)',
                        'autorizacao'  => 'Autorizacoes e Rate Limit',
                        'notificacao'  => 'Notificacoes',
                        'geral'        => 'Geral',
                    ];
                    foreach ($grupos as $cat => $items):
                    ?>
                        <div class="config-card">
                            <h6><i class="bx bx-folder"></i> <?php echo $nomesCategoria[$cat] ?? ucfirst($cat); ?></h6>
                            <?php foreach ($items as $cfg): ?>
                                <div class="config-row">
                                    <div>
                                        <label><?php echo $cfg['chave']; ?></label>
                                        <div class="desc"><?php echo $cfg['descricao']; ?></div>
                                    </div>
                                    <input type="text"
                                           name="configs[<?php echo $cfg['id']; ?>][valor]"
                                           value="<?php echo htmlspecialchars($cfg['valor'] ?? ''); ?>"
                                           class="input-xlarge"
                                           placeholder="Valor...">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($configs)): ?>
                        <div class="alert alert-info">Nenhuma configuracao encontrada. Execute a migration para criar os valores padrao.</div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
