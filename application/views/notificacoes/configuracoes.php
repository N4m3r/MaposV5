<style>
    .config-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #ddd);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .config-card h4 {
        margin: 0 0 15px 0;
        color: var(--heading-color, #333);
        font-size: 16px;
        font-weight: 600;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: var(--text-color, #333);
    }
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--input-border, #ddd);
        border-radius: 4px;
        font-size: 14px;
        background: var(--input-bg, #fff);
        color: var(--input-color, #333);
    }
    .form-control:focus {
        border-color: var(--input-focus-border, #66afe9);
        outline: none;
        box-shadow: 0 0 0 2px rgba(102, 175, 233, 0.3);
    }
    .help-text {
        font-size: 12px;
        color: var(--help-text-color, #666);
        margin-top: 5px;
    }
    .status-indicator {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }
    .status-connected {
        background: #d4edda;
        color: #155724;
    }
    .status-disconnected {
        background: #f8d7da;
        color: #721c24;
    }
    .status-connecting {
        background: #fff3cd;
        color: #856404;
    }
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-primary {
        background: #007bff;
        color: white;
    }
    .btn-primary:hover {
        background: #0056b3;
    }
    .btn-success {
        background: #28a745;
        color: white;
    }
    .btn-success:hover {
        background: #1e7e34;
    }
    .btn-danger {
        background: #dc3545;
        color: white;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    .btn-secondary:hover {
        background: #545b62;
    }
    .qr-code-container {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin: 15px 0;
    }
    .qr-code-container img {
        max-width: 300px;
        border-radius: 8px;
    }
    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .checkbox-item label {
        margin: 0;
        cursor: pointer;
        font-weight: normal;
    }
    .time-input {
        width: 120px !important;
    }
    .provider-section {
        display: none;
    }
    .provider-section.active {
        display: block;
    }
    .connection-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }
    .test-section {
        border-top: 1px solid var(--border-color, #ddd);
        padding-top: 20px;
        margin-top: 20px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="bx bxl-whatsapp"></i>
                </span>
                <h5>Configurações de Notificações WhatsApp</h5>
            </div>
            <div class="widget-content nopadding">
                <form action="<?php echo current_url(); ?>" id="formConfiguracoes" method="post" class="form-horizontal">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <!-- Configurações Gerais -->
                    <div class="config-card">
                        <h4><i class="bx bx-cog"></i> Configurações Gerais</h4>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="whatsapp_ativo" value="1" <?php echo $config->whatsapp_ativo ? 'checked' : ''; ?>>
                                Ativar notificações WhatsApp
                            </label>
                            <div class="help-text">Habilita o envio de mensagens via WhatsApp</div>
                        </div>

                        <div class="form-group">
                            <label for="whatsapp_provedor">Provedor de WhatsApp</label>
                            <select name="whatsapp_provedor" id="whatsapp_provedor" class="form-control" onchange="toggleProvider()">
                                <option value="desativado" <?php echo $config->whatsapp_provedor == 'desativado' ? 'selected' : ''; ?>>Desativado</option>
                                <option value="evolution" <?php echo $config->whatsapp_provedor == 'evolution' ? 'selected' : ''; ?>>Evolution API (Recomendado)</option>
                                <option value="meta_api" <?php echo $config->whatsapp_provedor == 'meta_api' ? 'selected' : ''; ?>>Meta API (Oficial)</option>
                                <option value="z_api" <?php echo $config->whatsapp_provedor == 'z_api' ? 'selected' : ''; ?>>Z-API</option>
                            </select>
                            <div class="help-text">
                                <strong>Evolution API:</strong> Gratuito, requer servidor próprio<br>
                                <strong>Meta API:</strong> Oficial, paga por mensagem<br>
                                <strong>Z-API:</strong> Serviço pago brasileiro
                            </div>
                        </div>

                        <!-- Status da Conexão -->
                        <div class="form-group" id="status-section" style="<?php echo $config->whatsapp_provedor != 'desativado' ? '' : 'display:none'; ?>">
                            <label>Status da Conexão</label>
                            <div>
                                <?php if ($statusConexao): ?>
                                    <span class="status-indicator <?php echo $statusConexao['connected'] ? 'status-connected' : 'status-disconnected'; ?>">
                                        <i class="bx <?php echo $statusConexao['connected'] ? 'bx-check-circle' : 'bx-x-circle'; ?>"></i>
                                        <?php echo $statusConexao['connected'] ? 'Conectado' : 'Desconectado'; ?>
                                        <?php echo $statusConexao['status'] ? ' (' . $statusConexao['status'] . ')' : ''; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="status-indicator status-disconnected">
                                        <i class="bx bx-x-circle"></i> Não configurado
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="connection-actions" id="connection-actions">
                                <button type="button" class="btn-action btn-primary" onclick="verificarStatus()">
                                    <i class="bx bx-refresh"></i> Verificar Status
                                </button>
                                <?php if ($config->whatsapp_provedor == 'evolution'): ?>
                                    <button type="button" class="btn-action btn-success" onclick="obterQRCode()">
                                        <i class="bx bx-qr"></i> Conectar (QR Code)
                                    </button>
                                    <button type="button" class="btn-action btn-danger" onclick="desconectar()">
                                        <i class="bx bx-log-out"></i> Desconectar
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div id="qr-code-display" style="display:none;">
                                <div class="qr-code-container">
                                    <p>Escaneie o QR Code com seu WhatsApp:</p>
                                    <img id="qr-code-img" src="" alt="QR Code">
                                    <p class="help-text">Abra o WhatsApp no celular: Configurações > Dispositivos Conectados > Conectar dispositivo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evolution API -->
                    <div id="evolution-section" class="config-card provider-section <?php echo $config->whatsapp_provedor == 'evolution' ? 'active' : ''; ?>">
                        <h4><i class="bx bx-server"></i> Configurações Evolution API</h4>

                        <div class="form-group">
                            <label for="evolution_url">URL do Servidor</label>
                            <input type="url" name="evolution_url" id="evolution_url" class="form-control"
                                   value="<?php echo htmlspecialchars($config->evolution_url); ?>"
                                   placeholder="http://localhost:8080">
                            <div class="help-text">Endereço do servidor Evolution API (ex: http://seu-servidor:8080)</div>
                        </div>

                        <div class="form-group">
                            <label for="evolution_apikey">API Key</label>
                            <input type="text" name="evolution_apikey" id="evolution_apikey" class="form-control"
                                   value="<?php echo htmlspecialchars($config->evolution_apikey); ?>"
                                   placeholder="Sua API Key">
                            <div class="help-text">Chave de API configurada no servidor Evolution</div>
                        </div>

                        <div class="form-group">
                            <label for="evolution_version">Versão da API</label>
                            <select name="evolution_version" id="evolution_version" class="form-control">
                                <option value="v2" <?php echo ($config->evolution_version ?? 'v2') == 'v2' ? 'selected' : ''; ?>>Self-Hosted (v2)</option>
                                <option value="go" <?php echo ($config->evolution_version ?? 'v2') == 'go' ? 'selected' : ''; ?>>Evolution Go (SaaS)</option>
                            </select>
                            <div class="help-text">Self-Hosted usa instância no URL. Evolution Go usa a API Key para identificar a instância.</div>
                        </div>

                        <div class="form-group">
                            <label for="evolution_instance">Nome da Instância</label>
                            <input type="text" name="evolution_instance" id="evolution_instance" class="form-control"
                                   value="<?php echo htmlspecialchars($config->evolution_instance); ?>"
                                   placeholder="mapos">
                            <div class="help-text">Nome único para esta instância (ex: mapos). No Evolution Go, pode deixar como 'mapos'.</div>
                        </div>
                    </div>

                    <!-- Meta API -->
                    <div id="meta-section" class="config-card provider-section <?php echo $config->whatsapp_provedor == 'meta_api' ? 'active' : ''; ?>">
                        <h4><i class="bx bxl-facebook-circle"></i> Configurações Meta API (Oficial)</h4>

                        <div class="form-group">
                            <label for="meta_phone_number_id">Phone Number ID</label>
                            <input type="text" name="meta_phone_number_id" id="meta_phone_number_id" class="form-control"
                                   value="<?php echo htmlspecialchars($config->meta_phone_number_id); ?>"
                                   placeholder="123456789012345">
                            <div class="help-text">ID do número de telefone no WhatsApp Business</div>
                        </div>

                        <div class="form-group">
                            <label for="meta_access_token">Access Token</label>
                            <textarea name="meta_access_token" id="meta_access_token" class="form-control" rows="3"
                                      placeholder="Token de acesso permanente"><?php echo htmlspecialchars($config->meta_access_token); ?></textarea>
                            <div class="help-text">Token de acesso permanente do Facebook Developers</div>
                        </div>
                    </div>

                    <!-- Z-API -->
                    <div id="zapi-section" class="config-card provider-section <?php echo $config->whatsapp_provedor == 'z_api' ? 'active' : ''; ?>">
                        <h4><i class="bx bx-chat"></i> Configurações Z-API</h4>

                        <div class="form-group">
                            <label for="z_api_url">URL da API</label>
                            <input type="url" name="z_api_url" id="z_api_url" class="form-control"
                                   value="<?php echo htmlspecialchars($config->z_api_url); ?>"
                                   placeholder="https://api.z-api.io">
                            <div class="help-text">URL base da API Z-API</div>
                        </div>

                        <div class="form-group">
                            <label for="z_api_token">Token</label>
                            <input type="text" name="z_api_token" id="z_api_token" class="form-control"
                                   value="<?php echo htmlspecialchars($config->z_api_token); ?>"
                                   placeholder="Seu token Z-API">
                            <div class="help-text">Token de autenticação da Z-API</div>
                        </div>
                    </div>

                    <!-- Notificações Automáticas -->
                    <div class="config-card">
                        <h4><i class="bx bx-bell"></i> Notificações Automáticas</h4>
                        <div class="help-text" style="margin-bottom:15px;">Selecione quais eventos devem enviar notificações automaticamente:</div>

                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" name="notificacao_os_criada" id="notificacao_os_criada" value="1" <?php echo $config->notificacao_os_criada ? 'checked' : ''; ?>>
                                <label for="notificacao_os_criada">OS Criada</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="notificacao_os_atualizada" id="notificacao_os_atualizada" value="1" <?php echo $config->notificacao_os_atualizada ? 'checked' : ''; ?>>
                                <label for="notificacao_os_atualizada">OS Atualizada</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="notificacao_os_pronta" id="notificacao_os_pronta" value="1" <?php echo $config->notificacao_os_pronta ? 'checked' : ''; ?>>
                                <label for="notificacao_os_pronta">OS Pronta</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="notificacao_os_orcamento" id="notificacao_os_orcamento" value="1" <?php echo $config->notificacao_os_orcamento ? 'checked' : ''; ?>>
                                <label for="notificacao_os_orcamento">Orçamento Disponível</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="notificacao_venda_realizada" id="notificacao_venda_realizada" value="1" <?php echo $config->notificacao_venda_realizada ? 'checked' : ''; ?>>
                                <label for="notificacao_venda_realizada">Venda Realizada</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="notificacao_cobranca_gerada" id="notificacao_cobranca_gerada" value="1" <?php echo $config->notificacao_cobranca_gerada ? 'checked' : ''; ?>>
                                <label for="notificacao_cobranca_gerada">Cobrança Gerada</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="notificacao_cobranca_vencimento" id="notificacao_cobranca_vencimento" value="1" <?php echo $config->notificacao_cobranca_vencimento ? 'checked' : ''; ?>>
                                <label for="notificacao_cobranca_vencimento">Lembrete Vencimento</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="notificacao_lembrete_aniversario" id="notificacao_lembrete_aniversario" value="1" <?php echo $config->notificacao_lembrete_aniversario ? 'checked' : ''; ?>>
                                <label for="notificacao_lembrete_aniversario">Aniversário (Marketing)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Horário de Funcionamento -->
                    <div class="config-card">
                        <h4><i class="bx bx-time"></i> Horário de Funcionamento</h4>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="respeitar_horario" value="1" <?php echo $config->respeitar_horario ? 'checked' : ''; ?>>
                                Respeitar horário de funcionamento
                            </label>
                            <div class="help-text">Só enviar mensagens durante o horário configurado</div>
                        </div>

                        <div class="form-group" style="display:flex; gap:20px;">
                            <div>
                                <label for="horario_envio_inicio">Horário Início</label>
                                <input type="time" name="horario_envio_inicio" id="horario_envio_inicio"
                                       class="form-control time-input" value="<?php echo $config->horario_envio_inicio; ?>">
                            </div>
                            <div>
                                <label for="horario_envio_fim">Horário Fim</label>
                                <input type="time" name="horario_envio_fim" id="horario_envio_fim"
                                       class="form-control time-input" value="<?php echo $config->horario_envio_fim; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="enviar_fim_semana" value="1" <?php echo $config->enviar_fim_semana ? 'checked' : ''; ?>>
                                Permitir envios no fim de semana
                            </label>
                            <div class="help-text">Desmarque para não enviar mensagens aos sábados e domingos</div>
                        </div>
                    </div>

                    <!-- Teste de Envio -->
                    <div class="config-card test-section">
                        <h4><i class="bx bx-send"></i> Testar Envio</h4>

                        <div class="form-group">
                            <label for="teste_numero">Número de Teste</label>
                            <div style="display:flex; gap:10px;">
                                <input type="text" id="teste_numero" class="form-control" placeholder="(11) 99999-9999" style="flex:1;">
                                <button type="button" class="btn-action btn-primary" onclick="testarEnvio()">
                                    <i class="bx bx-send"></i> Enviar Teste
                                </button>
                            </div>
                            <div class="help-text">Envie uma mensagem de teste para verificar a conexão</div>
                        </div>
                        <div id="teste-resultado" style="margin-top:10px;"></div>
                    </div>

                    <!-- Botões -->
                    <div class="form-actions" style="padding:20px; text-align:right;">
                        <button type="submit" class="btn-action btn-success">
                            <i class="bx bx-save"></i> Salvar Configurações
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleProvider() {
    const provedor = document.getElementById('whatsapp_provedor').value;

    // Esconde todas as seções
    document.querySelectorAll('.provider-section').forEach(section => {
        section.classList.remove('active');
    });

    // Mostra seção do provedor selecionado
    if (provedor === 'evolution') {
        document.getElementById('evolution-section').classList.add('active');
    } else if (provedor === 'meta_api') {
        document.getElementById('meta-section').classList.add('active');
    } else if (provedor === 'z_api') {
        document.getElementById('zapi-section').classList.add('active');
    }

    // Mostra/esconde status
    document.getElementById('status-section').style.display = provedor !== 'desativado' ? 'block' : 'none';
}

function verificarStatus() {
    fetch('<?php echo site_url("notificacoesConfig/verificar_status"); ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => r.json())
        .then(data => {
            alert('Status: ' + (data.connected ? 'Conectado (' + data.status + ')' : 'Desconectado'));
            if (data.error) {
                alert('Erro: ' + data.error);
            }
        })
        .catch(err => alert('Erro ao verificar status: ' + err));
}

function obterQRCode() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Obtendo QR Code...';

    fetch('<?php echo site_url("notificacoesConfig/obter_qr"); ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-qr"></i> Conectar (QR Code)';

            if (data.success && data.qr_code) {
                document.getElementById('qr-code-img').src = data.qr_code;
                document.getElementById('qr-code-display').style.display = 'block';
            } else if (data.already_connected) {
                alert('Já está conectado!');
            } else {
                alert('Erro ao obter QR Code: ' + (data.error || 'Erro desconhecido'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-qr"></i> Conectar (QR Code)';
            alert('Erro ao obter QR Code: ' + err);
        });
}

function desconectar() {
    if (!confirm('Tem certeza que deseja desconectar?')) return;

    fetch('<?php echo site_url("notificacoesConfig/desconectar"); ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Desconectado com sucesso!');
                document.getElementById('qr-code-display').style.display = 'none';
            } else {
                alert('Erro ao desconectar: ' + (data.error || 'Erro desconhecido'));
            }
        })
        .catch(err => alert('Erro: ' + err));
}

function testarEnvio() {
    const numero = document.getElementById('teste_numero').value;
    const resultado = document.getElementById('teste-resultado');

    if (!numero) {
        resultado.innerHTML = '<span style="color:red;">Informe um número</span>';
        return;
    }

    resultado.innerHTML = '<i class="bx bx-loader bx-spin"></i> Enviando...';

    const formData = new FormData();
    formData.append('numero', numero);
    formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

    fetch('<?php echo site_url("notificacoesConfig/testar_envio"); ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultado.innerHTML = '<span style="color:green;"><i class="bx bx-check"></i> Mensagem enviada! ID: ' + data.message_id + '</span>';
        } else {
            resultado.innerHTML = '<span style="color:red;"><i class="bx bx-x"></i> Erro: ' + (data.error || 'Erro desconhecido') + '</span>';
        }
    })
    .catch(err => {
        resultado.innerHTML = '<span style="color:red;"><i class="bx bx-x"></i> Erro: ' + err + '</span>';
    });
}

// Inicializa
$(document).ready(function() {
    toggleProvider();
});
</script>
