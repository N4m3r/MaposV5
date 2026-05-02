<?php
/**
 * View: configuracoes.php
<<<<<<< HEAD
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
=======
 * Tela de configuracoes gerais do Agente IA
 */

$gruposPt = [
    'evolution' => 'Evolution API (WhatsApp)',
    'n8n'       => 'n8n (Workflow Automation)',
    'llm'       => 'LLM / Modelo de IA',
    'geral'     => 'Configuracoes Gerais',
];

$iconesGrupo = [
    'evolution' => 'bxl-whatsapp',
    'n8n'       => 'bx-git-branch',
    'llm'       => 'bx-brain',
    'geral'     => 'bx-cog',
];

$coresGrupo = [
    'evolution' => '#25D366',
    'n8n'       => '#FF6D5A',
    'llm'       => '#6C5CE7',
    'geral'     => '#636e72',
];
?'>

<style>
.config-group {
    margin-bottom: 25px;
}
.config-group-header {
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
    color: #fff;
    font-weight: 600;
    font-size: 1.1em;
    display: flex;
    align-items: center;
    gap: 10px;
}
.config-group-body {
    border: 1px solid #e0e0e0;
    border-top: none;
    border-radius: 0 0 8px 8px;
    padding: 20px;
    background: #fafafa;
}
.config-item {
    margin-bottom: 15px;
}
.config-item:last-child {
    margin-bottom: 0;
}
.config-item label {
    font-weight: 600;
    color: #2d3436;
    display: block;
    margin-bottom: 5px;
}
.config-item .desc {
    font-size: 0.85em;
    color: #636e72;
    margin-bottom: 5px;
}
.config-item input[type="text"],
.config-item input[type="password"],
.config-item input[type="number"],
.config-item textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #dfe6e9;
    border-radius: 4px;
    font-size: 0.95em;
    box-sizing: border-box;
}
.config-item textarea {
    resize: vertical;
    min-height: 60px;
    font-family: monospace;
}
.toggle-switch {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 5px;
}
.toggle-switch input[type="checkbox"] {
    width: 40px;
    height: 22px;
    accent-color: #00b894;
}
.toggle-switch .toggle-label {
    font-weight: 500;
}
.test-btn {
    margin-top: 8px;
    font-size: 0.85em;
}
.btn-test {
    background: #00b894;
    color: #fff;
    border: none;
    padding: 6px 14px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85em;
}
.btn-test:hover {
    background: #00a383;
}
.btn-test:disabled {
    background: #b2bec3;
    cursor: not-allowed;
}
.test-result {
    margin-top: 8px;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 0.85em;
    display: none;
}
.test-result.ok    { background: #d4edda; color: #155724; display: block; }
.test-result.err   { background: #f8d7da; color: #721c24; display: block; }
.test-result.warn  { background: #fff3cd; color: #856404; display: block; }
>>>>>>> 10b417cfe75fa9265859bfcda71135ba088587d4
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
<<<<<<< HEAD
                    <button type="submit" form="formConfigs" class="btn btn-success btn-mini">
                        <i class="bx bx-save"></i> Salvar
                    </button>
=======
>>>>>>> 10b417cfe75fa9265859bfcda71135ba088587d4
                </div>
            </div>

            <div class="widget-content">
<<<<<<< HEAD
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
=======

                <form action="<?php echo site_url('agente_ia/salvar_configuracoes'); ?>" method="post" id="formConfig">

                <?php foreach ($configs as $grupo => $itens): ?>

                    <div class="config-group">
                        <div class="config-group-header" style="background-color: <?php echo $coresGrupo[$grupo] ?? '#636e72'; ?>">
                            <i class="bx <?php echo $iconesGrupo[$grupo] ?? 'bx-cog'; ?>"></i>
                            <span><?php echo $gruposPt[$grupo] ?? ucfirst($grupo); ?></span>
                        </div>

                        <div class="config-group-body">
                            <div class="row-fluid">

                            <?php foreach ($itens as $item): ?
                                <?php $inputType = (stripos($item['chave'], 'apikey') !== false || $item['sensivel']) ? 'password' : ((stripos($item['chave'], 'enabled') !== false) ? 'checkbox' : 'text'); ?>
                                <?php if (stripos($item['chave'], 'prompt') !== false) $inputType = 'textarea'; ?
                                <?php if (stripos($item['chave'], 'max_tokens') !== false || stripos($item['chave'], 'timeout') !== false) $inputType = 'number'; ?
                                <?php $isToggle = ($inputType === 'checkbox'); ?
                                <?php $spanClass = in_array($item['chave'], ['llm_system_prompt','mensagem_boas_vindas']) ? 'span12' : 'span6'; ?
                                <?php if ($item['chave'] === 'numero_whatsapp_principal') $spanClass = 'span6'; ?

                                <div class="config-item <?php echo $spanClass; ?>">
                                    <label><?php echo ucwords(str_replace(['_','-'], ' ', $item['chave'])); ?></label>
                                    <?php if (!empty($item['descricao'])): ?
                                        <div class="desc"><?php echo $item['descricao']; ?></div>
                                    <?php endif; ?

                                    <?php if ($isToggle): ?
                                        <div class="toggle-switch">
                                            <input type="checkbox"
                                                name="configs[<?php echo $item['chave']; ?>]"
                                                value="1"
                                                class="marcar"
                                                <?php echo ($item['valor'] == '1') ? 'checked="checked"' : ''; ?>
                                                >
                                            <span class="toggle-label">
                                                <?php echo ($item['valor'] == '1') ? 'Ativo' : 'Inativo'; ?>
                                            </span>
                                        </div>
                                    <?php elseif ($inputType === 'textarea'): ?
                                        <textarea name="configs[<?php echo $item['chave']; ?>]"
                                            rows="3"
                                            class="input-xxlarge"
                                        ><?php echo htmlspecialchars($item['valor']); ?></textarea>
                                    <?php else: ?
                                        <input type="<?php echo $inputType; ?>"
                                            name="configs[<?php echo $item['chave']; ?>]"
                                            value="<?php echo htmlspecialchars($item['valor']); ?>"
                                            class="input-xlarge"
                                            >
                                    <?php endif; ?

                                    <?php if ($item['chave'] === 'evolution_url'): ?
                                        <div class="test-btn">
                                            <button type="button" class="btn-test" onclick="testarEvolution(this)">
                                                <i class="bx bx-plug"></i> Testar conexao
                                            </button>
                                            <div class="test-result" id="result-evolution"></div>
                                        </div>
                                    <?php endif; ?

                                    <?php if ($item['chave'] === 'n8n_webhook_url'): ?
                                        <div class="test-btn">
                                            <button type="button" class="btn-test" onclick="testarN8n(this)">
                                                <i class="bx bx-broadcast"></i> Testar webhook
                                            </button>
                                            <div class="test-result" id="result-n8n"></div>
                                        </div>
                                    <?php endif; ?

                                </div>
                            <?php endforeach; ?

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

                <div style="text-align: center; margin-top: 20px;">
                    <button type="submit" class="btn btn-success btn-large">
                        <i class="bx bx-save"></i> Salvar Configuracoes
                    </button>
                </div>

>>>>>>> 10b417cfe75fa9265859bfcda71135ba088587d4
                </form>
            </div>
        </div>
    </div>
</div>
<<<<<<< HEAD
=======

<script>
function testarEvolution(btn) {
    var url = document.getElementsByName('configs[evolution_url]')[0].value;
    var result = document.getElementById('result-evolution');
    if (!url) {
        result.textContent = 'Preencha a URL da Evolution primeiro.';
        result.className = 'test-result err';
        return;
    }
    btn.disabled = true;
    result.textContent = 'Testando...';
    result.className = 'test-result warn';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', url + '/instance/fetchInstances', true);
    xhr.setRequestHeader('apikey', document.getElementsByName('configs[evolution_apikey]')[0].value);
    xhr.timeout = 10000;
    xhr.onload = function() {
        btn.disabled = false;
        if (xhr.status === 200) {
            result.textContent = 'Conexao OK! Evolution respondendo.';
            result.className = 'test-result ok';
        } else {
            result.textContent = 'Erro HTTP ' + xhr.status + '. Verifique URL e API Key.';
            result.className = 'test-result err';
        }
    };
    xhr.onerror = function() {
        btn.disabled = false;
        result.textContent = 'Falha na conexao. Verifique se a Evolution esta rodando na URL informada.';
        result.className = 'test-result err';
    };
    xhr.ontimeout = function() {
        btn.disabled = false;
        result.textContent = 'Timeout. Verifique a URL e a rede.';
        result.className = 'test-result err';
    };
    xhr.send();
}

function testarN8n(btn) {
    var url = document.getElementsByName('configs[n8n_webhook_url]')[0].value;
    var result = document.getElementById('result-n8n');
    if (!url) {
        result.textContent = 'Preencha a URL do webhook do n8n primeiro.';
        result.className = 'test-result err';
        return;
    }
    btn.disabled = true;
    result.textContent = 'Testando...';
    result.className = 'test-result warn';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.timeout = 10000;
    xhr.onload = function() {
        btn.disabled = false;
        if (xhr.status >= 200 && xhr.status < 300) {
            result.textContent = 'Webhook respondendo!';
            result.className = 'test-result ok';
        } else if (xhr.status === 403 || xhr.status === 401) {
            result.textContent = 'Webhook existente (erro ' + xhr.status + '). Pode estar correto.';
            result.className = 'test-result ok';
        } else {
            result.textContent = 'Erro HTTP ' + xhr.status + '. Verifique a URL.';
            result.className = 'test-result err';
        }
    };
    xhr.onerror = function() {
        btn.disabled = false;
        result.textContent = 'Falha na conexao. Verifique a URL do n8n.';
        result.className = 'test-result err';
    };
    xhr.ontimeout = function() {
        btn.disabled = false;
        result.textContent = 'Timeout. Verifique a URL e a rede.';
        result.className = 'test-result err';
    };
    xhr.send();
}

// Atualiza o texto ao marcar/desmarcar toggle
document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(function(chk) {
    chk.addEventListener('change', function() {
        var label = this.parentElement.querySelector('.toggle-label');
        label.textContent = this.checked ? 'Ativo' : 'Inativo';
    });
});
</script>
>>>>>>> 10b417cfe75fa9265859bfcda71135ba088587d4
