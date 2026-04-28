<style>
.config-card{background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:20px}
.config-card h4{margin:0 0 15px 0;font-size:16px;font-weight:600;color:#333}
.form-group{margin-bottom:15px}
.form-group label{display:block;margin-bottom:5px;font-weight:500}
.form-control{width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:4px;font-size:14px}
.help-text{font-size:12px;color:#666;margin-top:5px}
.status-badge{display:inline-flex;align-items:center;padding:6px 12px;border-radius:20px;font-size:13px;font-weight:500}
.status-on{background:#d4edda;color:#155724}
.status-off{background:#f8d7da;color:#721c24}
.btn-action{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;font-size:14px;font-weight:500}
.btn-primary{background:#007bff;color:#fff}
.btn-success{background:#28a745;color:#fff}
.btn-danger{background:#dc3545;color:#fff}
.btn-secondary{background:#6c757d;color:#fff}
.checkbox-group{display:flex;flex-wrap:wrap;gap:15px}
.checkbox-item{display:flex;align-items:center;gap:8px}
.provider-section{display:none}
.provider-section.active{display:block}
.debug-panel{background:#1e1e1e;color:#d4d4d4;font-family:'Courier New',monospace;font-size:12px;padding:15px;border-radius:8px;margin-top:15px;max-height:400px;overflow-y:auto;display:none}
.debug-line{margin-bottom:4px;word-break:break-all}
.qr-container{text-align:center;padding:20px;background:#f8f9fa;border-radius:8px;margin-top:15px;display:none}
.qr-container img{max-width:280px;border-radius:8px}
.connection-actions{display:flex;gap:10px;margin-top:15px;flex-wrap:wrap}
</style>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bxl-whatsapp"></i></span>
                <h5>Configuracoes de Notificacoes WhatsApp</h5>
            </div>
            <div class="widget-content nopadding">
                <form action="<?php echo current_url(); ?>" method="post" class="form-horizontal">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <!-- Configuracoes Gerais -->
                    <div class="config-card">
                        <h4><i class="bx bx-cog"></i> Configuracoes Gerais</h4>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="whatsapp_ativo" value="1" <?php echo $config->whatsapp_ativo ? 'checked' : ''; ?>>
                                Ativar notificacoes WhatsApp
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="whatsapp_provedor">Provedor</label>
                            <select name="whatsapp_provedor" id="whatsapp_provedor" class="form-control" onchange="toggleProvider()">
                                <option value="desativado" <?php echo $config->whatsapp_provedor == 'desativado' ? 'selected' : ''; ?>>Desativado</option>
                                <option value="evolution" <?php echo $config->whatsapp_provedor == 'evolution' ? 'selected' : ''; ?>>Evolution API</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div id="status-section" style="<?php echo $config->whatsapp_provedor != 'desativado' ? '' : 'display:none'; ?>">
                            <div class="form-group">
                                <label>Status da Conexao</label>
                                <div>
                                    <?php if ($statusConexao && $statusConexao['connected']): ?>
                                        <span class="status-badge status-on"><i class="bx bx-check-circle"></i> Conectado (<?php echo $statusConexao['status']; ?>)</span>
                                    <?php else: ?>
                                        <span class="status-badge status-off"><i class="bx bx-x-circle"></i> <?php echo $statusConexao['status'] ?? 'Desconectado'; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="connection-actions">
                                <button type="button" class="btn-action btn-primary" onclick="verificarStatus()">
                                    <i class="bx bx-refresh"></i> Verificar Status
                                </button>
                                <button type="button" class="btn-action btn-secondary" onclick="diagnostico()">
                                    <i class="bx bx-data"></i> Diagnostico
                                </button>
                                <?php if ($config->whatsapp_provedor == 'evolution'): ?>
                                    <button type="button" class="btn-action btn-success" id="btn-qr" onclick="obterQRCode()">
                                        <i class="bx bx-qr"></i> Conectar (QR Code)
                                    </button>
                                    <button type="button" class="btn-action btn-danger" id="btn-desc" onclick="desconectar()">
                                        <i class="bx bx-log-out"></i> Desconectar
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div id="qr-box" class="qr-container">
                                <p>Escaneie o QR Code com seu WhatsApp:</p>
                                <img id="qr-img" src="" alt="QR Code">
                                <p class="help-text">Abra o WhatsApp no celular: Configuracoes > Dispositivos Conectados > Conectar dispositivo</p>
                            </div>

                            <div id="debug-panel" class="debug-panel">
                                <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                                    <strong style="color:#4ec9b0;"><i class="bx bx-bug"></i> Debug</strong>
                                    <button type="button" onclick="limparDebug()" style="background:#333;color:#fff;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;font-size:11px;">Limpar</button>
                                </div>
                                <div id="debug-log"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Evolution API -->
                    <div id="evolution-section" class="config-card provider-section <?php echo $config->whatsapp_provedor == 'evolution' ? 'active' : ''; ?>">
                        <h4><i class="bx bx-server"></i> Configuracoes Evolution API</h4>

                        <div class="form-group">
                            <label for="evolution_url">URL do Servidor</label>
                            <input type="url" name="evolution_url" id="evolution_url" class="form-control"
                                   value="<?php echo htmlspecialchars($config->evolution_url); ?>"
                                   placeholder="https://evo.seudominio.com">
                            <div class="help-text">Endereco do servidor Evolution (ex: https://evo.jj-ferreiras.com.br)</div>
                        </div>

                        <div class="form-group">
                            <label for="evolution_apikey">API Key</label>
                            <input type="text" name="evolution_apikey" id="evolution_apikey" class="form-control"
                                   value="<?php echo htmlspecialchars($config->evolution_apikey); ?>"
                                   placeholder="Sua API Key">
                            <div class="help-text">Chave de API do painel Evolution</div>
                        </div>

                        <div class="form-group">
                            <label for="evolution_instance">Nome da Instancia</label>
                            <input type="text" name="evolution_instance" id="evolution_instance" class="form-control"
                                   value="<?php echo htmlspecialchars($config->evolution_instance); ?>"
                                   placeholder="Mapos">
                            <div class="help-text">Nome da instancia no servidor Evolution (case-insensitive)</div>
                        </div>
                    </div>

                    <!-- Notificacoes -->
                    <div class="config-card">
                        <h4><i class="bx bx-bell"></i> Notificacoes Automaticas</h4>
                        <div class="checkbox-group">
                            <div class="checkbox-item"><input type="checkbox" name="notificacao_os_criada" value="1" <?php echo $config->notificacao_os_criada ? 'checked' : ''; ?>> <label>OS Criada</label></div>
                            <div class="checkbox-item"><input type="checkbox" name="notificacao_os_atualizada" value="1" <?php echo $config->notificacao_os_atualizada ? 'checked' : ''; ?>> <label>OS Atualizada</label></div>
                            <div class="checkbox-item"><input type="checkbox" name="notificacao_os_pronta" value="1" <?php echo $config->notificacao_os_pronta ? 'checked' : ''; ?>> <label>OS Pronta</label></div>
                            <div class="checkbox-item"><input type="checkbox" name="notificacao_os_orcamento" value="1" <?php echo $config->notificacao_os_orcamento ? 'checked' : ''; ?>> <label>Orcamento Disponivel</label></div>
                            <div class="checkbox-item"><input type="checkbox" name="notificacao_venda_realizada" value="1" <?php echo $config->notificacao_venda_realizada ? 'checked' : ''; ?>> <label>Venda Realizada</label></div>
                            <div class="checkbox-item"><input type="checkbox" name="notificacao_cobranca_gerada" value="1" <?php echo $config->notificacao_cobranca_gerada ? 'checked' : ''; ?>> <label>Cobranca Gerada</label></div>
                            <div class="checkbox-item"><input type="checkbox" name="notificacao_cobranca_vencimento" value="1" <?php echo $config->notificacao_cobranca_vencimento ? 'checked' : ''; ?>> <label>Lembrete Vencimento</label></div>
                            <div class="checkbox-item"><input type="checkbox" name="notificacao_lembrete_aniversario" value="1" <?php echo $config->notificacao_lembrete_aniversario ? 'checked' : ''; ?>> <label>Aniversario (Marketing)</label></div>
                        </div>
                    </div>

                    <!-- Horario -->
                    <div class="config-card">
                        <h4><i class="bx bx-time"></i> Horario de Envio</h4>
                        <div class="form-group">
                            <label><input type="checkbox" name="respeitar_horario" value="1" <?php echo $config->respeitar_horario ? 'checked' : ''; ?>> Respeitar horario de funcionamento</label>
                        </div>
                        <div class="form-group" style="display:flex;gap:20px;">
                            <div>
                                <label>Inicio</label>
                                <input type="time" name="horario_envio_inicio" class="form-control" value="<?php echo $config->horario_envio_inicio; ?>" style="width:120px">
                            </div>
                            <div>
                                <label>Fim</label>
                                <input type="time" name="horario_envio_fim" class="form-control" value="<?php echo $config->horario_envio_fim; ?>" style="width:120px">
                            </div>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="enviar_fim_semana" value="1" <?php echo $config->enviar_fim_semana ? 'checked' : ''; ?>> Permitir envios no fim de semana</label>
                        </div>
                    </div>

                    <!-- Teste -->
                    <div class="config-card">
                        <h4><i class="bx bx-send"></i> Testar Envio</h4>
                        <div class="form-group" style="display:flex;gap:10px;">
                            <input type="text" id="teste_numero" class="form-control" placeholder="(11) 99999-9999" style="flex:1">
                            <button type="button" class="btn-action btn-primary" onclick="testarEnvio()">
                                <i class="bx bx-send"></i> Enviar Teste
                            </button>
                        </div>
                        <div id="teste-resultado"></div>
                    </div>

                    <div class="form-actions" style="padding:20px;text-align:right;">
                        <button type="submit" class="btn-action btn-success">
                            <i class="bx bx-save"></i> Salvar Configuracoes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleProvider() {
    const p = document.getElementById('whatsapp_provedor').value;
    document.querySelectorAll('.provider-section').forEach(s => s.classList.remove('active'));
    if (p === 'evolution') {
        document.getElementById('evolution-section').classList.add('active');
    }
    document.getElementById('status-section').style.display = p !== 'desativado' ? 'block' : 'none';
}

function addDebug(tipo, msg) {
    const panel = document.getElementById('debug-panel');
    const log = document.getElementById('debug-log');
    panel.style.display = 'block';
    const hora = new Date().toLocaleTimeString('pt-BR', {hour12: false});
    let cor = '#d4d4d4', prefixo = '[INFO]';
    if (tipo === 'erro') { cor = '#f44747'; prefixo = '[ERRO]'; }
    if (tipo === 'ok') { cor = '#4ec9b0'; prefixo = '[OK]'; }
    const div = document.createElement('div');
    div.className = 'debug-line';
    div.innerHTML = `<span style="color:#858585;">${hora}</span> <span style="color:${cor};font-weight:bold;">${prefixo}</span> <span style="color:${cor};">${msg}</span>`;
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
}

function limparDebug() {
    document.getElementById('debug-log').innerHTML = '';
    document.getElementById('debug-panel').style.display = 'none';
    document.getElementById('qr-box').style.display = 'none';
}

function verificarStatus() {
    limparDebug();
    addDebug('info', 'Verificando status...');
    fetch('<?php echo site_url("notificacoesConfig/verificar_status"); ?>', {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        addDebug('info', 'Status: ' + (data.connected ? 'Conectado' : 'Desconectado'));
        if (data.error) addDebug('erro', 'Erro: ' + data.error);
        if (data.status) addDebug('info', 'Detalhe: ' + data.status);
        alert('Status: ' + (data.connected ? 'Conectado (' + data.status + ')' : 'Desconectado' + (data.error ? '\n' + data.error : '')));
    })
    .catch(err => addDebug('erro', err.message));
}

function diagnostico() {
    limparDebug();
    addDebug('info', 'Executando diagnostico...');
    fetch('<?php echo site_url("notificacoesConfig/diagnostico"); ?>', {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        addDebug('info', 'Tabela existe: ' + (data.tabela_existe ? 'SIM' : 'NAO'));
        if (data.config) {
            Object.entries(data.config).forEach(([k, v]) => {
                addDebug('info', k + ': ' + (v === null ? 'NULL' : v));
            });
        }
    })
    .catch(err => addDebug('erro', err.message));
}

function obterQRCode() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Obtendo...';
    limparDebug();
    addDebug('info', 'Solicitando QR Code...');

    fetch('<?php echo site_url("notificacoesConfig/obter_qr"); ?>', {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-qr"></i> Conectar (QR Code)';

        if (data.debug) {
            data.debug.forEach(d => addDebug(d.tipo || 'info', d.msg));
        }

        if (data.success && data.qr_code) {
            addDebug('ok', 'QR Code recebido!');
            document.getElementById('qr-img').src = data.qr_code;
            document.getElementById('qr-box').style.display = 'block';
        } else if (data.already_connected) {
            addDebug('info', 'Ja esta conectado!');
            alert('Ja esta conectado!');
        } else {
            addDebug('erro', 'Erro: ' + (data.error || 'Desconhecido'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-qr"></i> Conectar (QR Code)';
        addDebug('erro', 'Excecao: ' + err.message);
    });
}

function desconectar() {
    if (!confirm('Desconectar?')) return;
    fetch('<?php echo site_url("notificacoesConfig/desconectar"); ?>', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? 'Desconectado!' : 'Erro: ' + (data.error || 'Desconhecido'));
    });
}

function testarEnvio() {
    const numero = document.getElementById('teste_numero').value;
    const resultado = document.getElementById('teste-resultado');
    if (!numero) {
        resultado.innerHTML = '<span style="color:red">Informe um numero</span>';
        return;
    }
    resultado.innerHTML = '<i class="bx bx-loader bx-spin"></i> Enviando...';
    const fd = new FormData();
    fd.append('numero', numero);
    fd.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
    fetch('<?php echo site_url("notificacoesConfig/testar_envio"); ?>', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultado.innerHTML = '<span style="color:green"><i class="bx bx-check"></i> Enviado! ID: ' + data.message_id + '</span>';
        } else {
            resultado.innerHTML = '<span style="color:red"><i class="bx bx-x"></i> Erro: ' + (data.error || 'Desconhecido') + '</span>';
        }
    })
    .catch(err => {
        resultado.innerHTML = '<span style="color:red">Erro: ' + err.message + '</span>';
    });
}

$(document).ready(function() {
    toggleProvider();
});
</script>
