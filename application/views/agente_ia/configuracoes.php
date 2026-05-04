<?php
/**
 * View: configuracoes.php
 * Painel de configuracoes do Agente IA
 */

$gruposPt = [
    'integracao'   => 'Integracao (n8n / Evolution)',
    'ia'           => 'Inteligencia Artificial (LLM / Whisper)',
    'autorizacao'  => 'Autorizacoes e Rate Limit',
    'notificacao'  => 'Notificacoes',
    'geral'        => 'Geral',
];

$iconesGrupo = [
    'integracao'   => 'bx-plug',
    'ia'           => 'bx-brain',
    'autorizacao'  => 'bx-shield',
    'notificacao'  => 'bx-bell',
    'geral'        => 'bx-cog',
];

$coresGrupo = [
    'integracao'   => '#4facfe',
    'ia'           => '#6C5CE7',
    'autorizacao'  => '#f2994a',
    'notificacao'  => '#eb3349',
    'geral'        => '#636e72',
];
?>

<style>
.config-group { margin-bottom: 25px; }
.config-group-header {
    padding: 12px 18px;
    border-radius: 6px 6px 0 0;
    color: #fff;
    font-weight: 600;
    font-size: 1.05em;
    display: flex;
    align-items: center;
    gap: 10px;
}
.config-group-body {
    border: 1px solid #e0e0e0;
    border-top: none;
    border-radius: 0 0 6px 6px;
    padding: 18px;
    background: #fafafa;
}
.config-item {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}
.config-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}
.config-item label {
    font-weight: 600;
    color: #2d3436;
    display: block;
    margin-bottom: 4px;
}
.config-item .desc {
    font-size: 0.82em;
    color: #636e72;
    margin-bottom: 6px;
}
.config-item input[type="text"],
.config-item input[type="password"],
.config-item input[type="number"] {
    width: 100%;
    padding: 7px 10px;
    border: 1px solid #dfe6e9;
    border-radius: 4px;
    font-size: 0.95em;
    box-sizing: border-box;
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
                        $grupos[$c['grupo']][] = $c;
                    }
                    foreach ($grupos as $cat => $items):
                        $cor = $coresGrupo[$cat] ?? '#636e72';
                        $icone = $iconesGrupo[$cat] ?? 'bx-cog';
                        $nome = $gruposPt[$cat] ?? ucfirst($cat);
                    ?>
                        <div class="config-group">
                            <div class="config-group-header" style="background-color: <?php echo $cor; ?>">
                                <i class="bx <?php echo $icone; ?>"></i>
                                <span><?php echo $nome; ?></span>
                            </div>
                            <div class="config-group-body">
                                <?php foreach ($items as $cfg): ?>
                                    <div class="config-item">
                                        <label><?php echo $cfg['chave']; ?></label>
                                        <?php if (!empty($cfg['descricao'])): ?>
                                            <div class="desc"><?php echo $cfg['descricao']; ?></div>
                                        <?php endif; ?>
                                        <input type="text"
                                               name="configs[<?php echo $cfg['id']; ?>][valor]"
                                               value="<?php echo htmlspecialchars($cfg['valor'] ?? ''); ?>"
                                               placeholder="Valor...">
                                    </div>
                                <?php endforeach; ?>
                            </div>
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
