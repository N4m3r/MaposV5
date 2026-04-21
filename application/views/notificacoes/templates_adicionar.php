<style>
    .wizard-container {
        display: flex;
        gap: 0;
        min-height: 600px;
    }
    .wizard-sidebar {
        width: 250px;
        background: #f8f9fa;
        border-right: 1px solid var(--border-color, #ddd);
        padding: 0;
    }
    .wizard-step {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    .wizard-step:hover {
        background: #e9ecef;
    }
    .wizard-step.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    .wizard-step.completed {
        background: #d4edda;
        color: #155724;
    }
    .wizard-step.completed::after {
        content: '\2713';
        position: absolute;
        right: 15px;
        font-weight: bold;
    }
    .step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 13px;
    }
    .wizard-step.active .step-number {
        background: white;
        color: #007bff;
    }
    .wizard-step.completed .step-number {
        background: #28a745;
        color: white;
    }
    .step-title {
        font-weight: 500;
        font-size: 14px;
    }
    .step-desc {
        font-size: 11px;
        opacity: 0.8;
    }
    .wizard-content {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
    }
    .step-panel {
        display: none;
    }
    .step-panel.active {
        display: block;
        animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .panel-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--heading-color, #333);
    }
    .panel-subtitle {
        color: #6c757d;
        margin-bottom: 25px;
        font-size: 14px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-color, #333);
        font-size: 14px;
    }
    .form-group label .required {
        color: #dc3545;
        margin-left: 3px;
    }
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        background: var(--input-bg, #fff);
        color: var(--input-color, #333);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }
    select.form-control {
        height: 46px;
        padding: 0 15px;
    }
    textarea.form-control {
        min-height: 180px;
        resize: vertical;
        font-family: inherit;
        line-height: 1.6;
    }
    .input-hint {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #6c757d;
        margin-top: 8px;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 6px;
    }
    .input-hint i {
        color: #17a2b8;
        font-size: 16px;
    }
    .example-box {
        background: #e7f3ff;
        border: 1px solid #b8daff;
        border-radius: 8px;
        padding: 12px 15px;
        margin-top: 10px;
        font-size: 13px;
        color: #004085;
    }
    .example-box strong {
        display: block;
        margin-bottom: 5px;
    }
    .example-value {
        font-family: monospace;
        background: white;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid #b8daff;
    }
    .template-examples {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .example-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 18px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .example-card:hover {
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.15);
    }
    .example-card.selected {
        border-color: #28a745;
        background: #f0fff4;
    }
    .example-card h5 {
        margin: 0 0 8px 0;
        font-size: 15px;
        color: #333;
    }
    .example-card p {
        margin: 0;
        font-size: 13px;
        color: #666;
        line-height: 1.5;
    }
    .example-card .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        margin-top: 10px;
    }
    .badge-os { background: #cce5ff; color: #004085; }
    .badge-venda { background: #d4edda; color: #155724; }
    .badge-cobranca { background: #fff3cd; color: #856404; }
    .badge-marketing { background: #f8d7da; color: #721c24; }

    /* Área de Variáveis */
    .variables-area {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 15px;
    }
    .variables-box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
    }
    .variables-box h5 {
        margin: 0 0 15px 0;
        font-size: 14px;
        color: #495057;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .variables-box h5 i {
        color: #007bff;
    }
    .var-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .var-chip {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 13px;
        font-family: monospace;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .var-chip:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
        transform: scale(1.05);
    }
    .var-chip .desc {
        font-size: 11px;
        opacity: 0.7;
        font-family: sans-serif;
    }
    .var-chip:hover .desc {
        opacity: 0.9;
    }

    /* Editor de Mensagem */
    .message-editor {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
    }
    .editor-toolbar {
        background: #f8f9fa;
        padding: 10px 15px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .toolbar-btn {
        background: white;
        border: 1px solid #dee2e6;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }
    .toolbar-btn:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    .message-editor textarea {
        border: none;
        padding: 20px;
        font-size: 15px;
        line-height: 1.7;
    }
    .message-editor textarea:focus {
        box-shadow: none;
    }
    .editor-footer {
        background: #f8f9fa;
        padding: 10px 15px;
        border-top: 1px solid #e9ecef;
        font-size: 12px;
        color: #6c757d;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Preview do WhatsApp */
    .preview-container {
        background: #e5ddd5;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        min-height: 300px;
    }
    .preview-header {
        text-align: center;
        color: #666;
        font-size: 13px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .preview-header::before,
    .preview-header::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(0,0,0,0.1);
    }
    .whatsapp-bubble {
        background: #dcf8c6;
        border-radius: 12px;
        border-top-right-radius: 2px;
        padding: 12px 15px;
        max-width: 85%;
        margin-left: auto;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        font-size: 14.5px;
        line-height: 1.5;
        color: #111;
        word-wrap: break-word;
        position: relative;
    }
    .whatsapp-time {
        text-align: right;
        font-size: 11px;
        color: #999;
        margin-top: 5px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
    }
    .whatsapp-check {
        color: #53bdeb;
    }
    .preview-empty {
        text-align: center;
        color: #999;
        padding: 60px 20px;
        font-style: italic;
    }

    /* Opções */
    .options-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 15px;
    }
    .option-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 18px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .option-card:hover {
        border-color: #007bff;
    }
    .option-card.selected {
        border-color: #28a745;
        background: #f0fff4;
    }
    .option-card input[type="checkbox"] {
        width: 20px;
        height: 20px;
        margin-top: 2px;
        cursor: pointer;
    }
    .option-content h5 {
        margin: 0 0 5px 0;
        font-size: 14px;
    }
    .option-content p {
        margin: 0;
        font-size: 12px;
        color: #666;
    }

    /* Botões de Navegação */
    .wizard-nav {
        display: flex;
        justify-content: space-between;
        padding: 20px 25px;
        border-top: 1px solid var(--border-color, #ddd);
        background: white;
    }
    .btn-wizard {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-wizard-prev {
        background: #6c757d;
        color: white;
    }
    .btn-wizard-prev:hover {
        background: #545b62;
    }
    .btn-wizard-next {
        background: #007bff;
        color: white;
    }
    .btn-wizard-next:hover {
        background: #0056b3;
    }
    .btn-wizard-save {
        background: #28a745;
        color: white;
    }
    .btn-wizard-save:hover {
        background: #1e7e34;
    }
    .btn-wizard:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Variáveis Personalizadas */
    .custom-var-builder {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        margin-top: 15px;
    }
    .custom-var-row {
        display: grid;
        grid-template-columns: 1fr 1.5fr auto;
        gap: 12px;
        margin-bottom: 12px;
        align-items: center;
    }
    .custom-var-row input {
        padding: 10px 12px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 13px;
    }
    .btn-icon-action {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.2s;
    }
    .btn-add {
        background: #28a745;
        color: white;
    }
    .btn-add:hover {
        background: #218838;
    }
    .btn-remove {
        background: #dc3545;
        color: white;
    }
    .btn-remove:hover {
        background: #c82333;
    }
    .btn-add-main {
        background: #e9ecef;
        color: #495057;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        font-weight: 500;
    }
    .btn-add-main:hover {
        background: #dee2e6;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .wizard-container {
            flex-direction: column;
        }
        .wizard-sidebar {
            width: 100%;
            display: flex;
            overflow-x: auto;
        }
        .wizard-step {
            min-width: 150px;
            border-bottom: none;
            border-right: 1px solid #e9ecef;
        }
        .variables-area {
            grid-template-columns: 1fr;
        }
        .options-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="bx bx-plus-circle"></i>
                </span>
                <h5>Criar Novo Template de Notificação</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('notificacoesConfig/templates'); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Cancelar
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding">

                <form action="<?php echo current_url(); ?>" id="formTemplate" method="post">
                    <div class="wizard-container">
                        <!-- Sidebar -->
                        <div class="wizard-sidebar">
                            <div class="wizard-step active" data-step="1" onclick="goToStep(1)">
                                <div class="step-number">1</div>
                                <div>
                                    <div class="step-title">Informações</div>
                                    <div class="step-desc">Nome e categoria</div>
                                </div>
                            </div>
                            <div class="wizard-step" data-step="2" onclick="goToStep(2)">
                                <div class="step-number">2</div>
                                <div>
                                    <div class="step-title">Exemplos</div>
                                    <div class="step-desc">Escolha ou comece do zero</div>
                                </div>
                            </div>
                            <div class="wizard-step" data-step="3" onclick="goToStep(3)">
                                <div class="step-number">3</div>
                                <div>
                                    <div class="step-title">Mensagem</div>
                                    <div class="step-desc">Escreva o conteúdo</div>
                                </div>
                            </div>
                            <div class="wizard-step" data-step="4" onclick="goToStep(4)">
                                <div class="step-number">4</div>
                                <div>
                                    <div class="step-title">Variáveis</div>
                                    <div class="step-desc">Dados dinâmicos</div>
                                </div>
                            </div>
                            <div class="wizard-step" data-step="5" onclick="goToStep(5)">
                                <div class="step-number">5</div>
                                <div>
                                    <div class="step-title">Opções</div>
                                    <div class="step-desc">Configurações finais</div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="wizard-content">

                            <!-- PASSO 1: Informações Básicas -->
                            <div class="step-panel active" id="step-1">
                                <div class="panel-title"><i class="bx bx-info-circle"></i> Informações Básicas</div>
                                <div class="panel-subtitle">Defina como identificar e categorizar este template</div>

                                <div class="form-group">
                                    <label for="chave">Identificador (Chave) <span class="required">*</span></label>
                                    <input type="text" name="chave" id="chave" class="form-control" required
                                           placeholder="Ex: boas_vindas_cliente"
                                           pattern="[a-z0-9_]+"
                                           onblur="validarChave()"
                                           style="font-family: monospace;">
                                    <div class="input-hint">
                                        <i class="bx bx-key"></i>
                                        <div>
                                            <strong>Dica:</strong> Use apenas letras minúsculas, números e underline.
                                            <br>Esta chave é usada no código para identificar o template.
                                        </div>
                                    </div>
                                    <div class="example-box">
                                        <strong>Exemplo válido:</strong>
                                        <span class="example-value">boas_vindas_cliente</span>,
                                        <span class="example-value">msg_aniversario</span>,
                                        <span class="example-value">alerta_cobranca</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="nome">Nome do Template <span class="required">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control" required
                                           placeholder="Ex: Mensagem de Boas-vindas">
                                    <div class="input-hint">
                                        <i class="bx bx-tag"></i>
                                        <div>Nome amigável para identificar este template na lista</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descricao">Descrição / Quando usar</label>
                                    <input type="text" name="descricao" id="descricao" class="form-control"
                                           placeholder="Ex: Enviada quando um novo cliente é cadastrado no sistema">
                                    <div class="input-hint">
                                        <i class="bx bx-help-circle"></i>
                                        <div>Descreva em que situação esta mensagem será enviada</div>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                    <div class="form-group">
                                        <label for="categoria">Categoria <span class="required">*</span></label>
                                        <select name="categoria" id="categoria" class="form-control" required onchange="atualizarVariaveisCategoria()">
                                            <option value="">Selecione...</option>
                                            <option value="os">📋 Ordens de Serviço</option>
                                            <option value="venda">🛒 Vendas</option>
                                            <option value="cobranca">💰 Cobranças</option>
                                            <option value="marketing">📢 Marketing</option>
                                            <option value="sistema">⚙️ Sistema</option>
                                            <option value="personalizado">✨ Personalizado</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="canal">Canal de Envio <span class="required">*</span></label>
                                        <select name="canal" id="canal" class="form-control" required onchange="toggleAssunto()">
                                            <option value="whatsapp">📱 WhatsApp (recomendado)</option>
                                            <option value="email">📧 E-mail</option>
                                            <option value="sms">💬 SMS</option>
                                            <option value="todos">🌐 Todos os canais</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- PASSO 2: Exemplos Pré-definidos -->
                            <div class="step-panel" id="step-2">
                                <div class="panel-title"><i class="bx bx-collection"></i> Escolha um Ponto de Partida</div>
                                <div class="panel-subtitle">Selecione um exemplo pré-definido ou continue para criar do zero</div>

                                <div class="template-examples" id="templateExamples">
                                    <div class="example-card" onclick="usarExemplo('personalizado')">
                                        <h5>📝 Começar do Zero</h5>
                                        <p>Crie sua mensagem personalizada do início. Você terá total liberdade para escrever.</p>
                                        <span class="badge badge-os">Qualquer categoria</span>
                                    </div>

                                    <div class="example-card" data-categoria="os" onclick="usarExemplo('os_criada')">
                                        <h5>📋 Nova OS Criada</h5>
                                        <p>Informe o cliente sobre a criação da ordem de serviço com detalhes do equipamento.</p>
                                        <span class="badge badge-os">Ordens de Serviço</span>
                                    </div>

                                    <div class="example-card" data-categoria="os" onclick="usarExemplo('os_pronta')">
                                        <h5>✅ OS Pronta para Retirada</h5>
                                        <p>Notifique que o serviço foi concluído com valor e instruções de retirada.</p>
                                        <span class="badge badge-os">Ordens de Serviço</span>
                                    </div>

                                    <div class="example-card" data-categoria="venda" onclick="usarExemplo('venda_realizada')">
                                        <h5>🛒 Confirmação de Venda</h5>
                                        <p>Confirme a compra do cliente com detalhes dos produtos adquiridos.</p>
                                        <span class="badge badge-venda">Vendas</span>
                                    </div>

                                    <div class="example-card" data-categoria="cobranca" onclick="usarExemplo('cobranca_gerada')">
                                        <h5>💳 Cobrança Gerada</h5>
                                        <p>Envie boleto ou link de pagamento com valor e data de vencimento.</p>
                                        <span class="badge badge-cobranca">Cobranças</span>
                                    </div>

                                    <div class="example-card" data-categoria="cobranca" onclick="usarExemplo('cobranca_lembrete')">
                                        <h5>⏰ Lembrete de Vencimento</h5>
                                        <p>Alerta sobre vencimento próximo para evitar atrasos e multas.</p>
                                        <span class="badge badge-cobranca">Cobranças</span>
                                    </div>

                                    <div class="example-card" data-categoria="marketing" onclick="usarExemplo('marketing_aniversario')">
                                        <h5>🎂 Feliz Aniversário</h5>
                                        <p>Parabenize o cliente com cupom de desconto especial.</p>
                                        <span class="badge badge-marketing">Marketing</span>
                                    </div>

                                    <div class="example-card" data-categoria="marketing" onclick="usarExemplo('marketing_promo')">
                                        <h5>🎁 Promoção Especial</h5>
                                        <p>Divulgue ofertas e promoções para seus clientes.</p>
                                        <span class="badge badge-marketing">Marketing</span>
                                    </div>
                                </div>
                            </div>

                            <!-- PASSO 3: Mensagem -->
                            <div class="step-panel" id="step-3">
                                <div class="panel-title"><i class="bx bx-message-square-edit"></i> Escreva a Mensagem</div>
                                <div class="panel-subtitle">Clique nas variáveis para inserir. Use emojis para tornar mais amigável.</div>

                                <div id="campo-assunto" style="display: none; margin-bottom: 20px;">
                                    <div class="form-group">
                                        <label for="assunto"><i class="bx bx-envelope"></i> Assunto do E-mail</label>
                                        <input type="text" name="assunto" id="assunto" class="form-control"
                                               placeholder="Ex: Sua Ordem de Serviço foi atualizada"
                                               style="font-size: 15px;">
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 25px;">
                                    <div>
                                        <label style="margin-bottom: 12px; display: block;"><i class="bx bx-edit"></i> Conteúdo da Mensagem <span class="required">*</span></label>

                                        <div class="message-editor">
                                            <div class="editor-toolbar">
                                                <button type="button" class="toolbar-btn" onclick="inserirEmoji('👋')" title="Saudação">👋</button>
                                                <button type="button" class="toolbar-btn" onclick="inserirEmoji('✅')" title="OK">✅</button>
                                                <button type="button" class="toolbar-btn" onclick="inserirEmoji('📋')" title="Documento">📋</button>
                                                <button type="button" class="toolbar-btn" onclick="inserirEmoji('💰')" title="Dinheiro">💰</button>
                                                <button type="button" class="toolbar-btn" onclick="inserirEmoji('📅')" title="Data">📅</button>
                                                <button type="button" class="toolbar-btn" onclick="inserirEmoji('🎉')" title="Parabéns">🎉</button>
                                                <button type="button" class="toolbar-btn" onclick="inserirEmoji('⏰')" title="Urgente">⏰</button>
                                                <button type="button" class="toolbar-btn" onclick="inserirEmoji('🙏')" title="Agradecimento">🙏</button>
                                                <a href="https://getemoji.com" target="_blank" class="toolbar-btn" title="Mais emojis"><i class="bx bx-link-external"></i> Mais</a>
                                            </div>
                                            <textarea name="mensagem" id="mensagem" class="form-control" required
                                                      placeholder="Olá {cliente_nome}! 👋

Sua Ordem de Serviço #{os_id} foi registrada em nosso sistema.

📋 Equipamento: {equipamento}
📝 Defeito: {defeito}

Acompanhe o status pelo link:
{link_consulta}

Obrigado pela preferência! 🤝"
                                                      oninput="atualizarPreview()"
                                                      style="min-height: 280px;"></textarea>
                                            <div class="editor-footer">
                                                <span id="char-count">0 caracteres</span>
                                                <a href="#" onclick="limparMensagem(); return false;"><i class="bx bx-trash"></i> Limpar</a>
                                            </div>
                                        </div>

                                        <div class="input-hint" style="margin-top: 15px;">
                                            <i class="bx bx-info-circle"></i>
                                            <div>
                                                <strong>Variáveis:</strong> Clique nas variáveis ao lado para inserir no texto.
                                                Elas serão substituídas pelos dados reais no momento do envio.
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label style="margin-bottom: 12px; display: block;"><i class="bx bx-mobile"></i> Preview no WhatsApp</label>

                                        <div class="preview-container">
                                            <div class="preview-header">HOJE</div>
                                            <div id="preview-area">
                                                <div class="preview-empty">
                                                    <i class="bx bx-message-square-dots" style="font-size: 48px; display: block; margin-bottom: 15px; color: #bbb;"></i>
                                                    Digite sua mensagem para ver o preview
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PASSO 4: Variáveis -->
                            <div class="step-panel" id="step-4">
                                <div class="panel-title"><i class="bx bx-variable"></i> Variáveis Disponíveis</div>
                                <div class="panel-subtitle">Clique em qualquer variável para inserir na mensagem. Crie variáveis personalizadas se necessário.</div>

                                <div class="variables-area">
                                    <div class="variables-box">
                                        <h5><i class="bx bx-globe"></i> Variáveis Globais (todos os templates)</h5>
                                        <div class="var-chips" id="vars-globais">
                                            <?php foreach ($variaveis_globais as $chave => $descricao): ?>
                                                <span class="var-chip" onclick="inserirVariavel('{<?php echo $chave; ?>}')"
                                                      title="<?php echo htmlspecialchars($descricao); ?>">
                                                    {<?php echo $chave; ?>}
                                                    <span class="desc"><?php echo htmlspecialchars($descricao); ?></span>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="variables-box">
                                        <h5><i class="bx bx-folder"></i> Variáveis da Categoria</h5>
                                        <div class="var-chips" id="vars-categoria">
                                            <div style="color: #999; font-size: 13px; font-style: italic;">
                                                Selecione uma categoria no Passo 1 para ver variáveis específicas
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="custom-var-builder">
                                    <h5 style="margin: 0 0 15px 0;"><i class="bx bx-plus-circle"></i> Criar Variáveis Personalizadas</h5>
                                    <p style="margin: 0 0 15px 0; font-size: 13px; color: #666;">
                                        Precisa de dados específicos deste template? Crie suas próprias variáveis:
                                    </p>

                                    <div id="custom-vars-container">
                                        <div class="custom-var-row">
                                            <input type="text" name="variavel_nome[]" placeholder="Nome (ex: codigo_promocao)" pattern="[a-z0-9_]+">
                                            <input type="text" name="variavel_desc[]" placeholder="Descrição (ex: Código da promoção)">
                                            <button type="button" class="btn-icon-action btn-remove" onclick="removerVariavel(this)">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="button" class="btn-add-main" onclick="adicionarVariavel()">
                                        <i class="bx bx-plus"></i> Adicionar outra variável
                                    </button>
                                </div>
                            </div>

                            <!-- PASSO 5: Opções -->
                            <div class="step-panel" id="step-5">
                                <div class="panel-title"><i class="bx bx-cog"></i> Configurações Finais</div>
                                <div class="panel-subtitle">Defina as opções de comportamento deste template</div>

                                <div class="options-grid">
                                    <label class="option-card" onclick="toggleCheckbox('ativo')">
                                        <input type="checkbox" name="ativo" id="ativo" value="1" checked>
                                        <div class="option-content">
                                            <h5><i class="bx bx-check-circle" style="color: #28a745;"></i> Template Ativo</h5>
                                            <p>O template será usado automaticamente pelo sistema quando o evento ocorrer.</p>
                                        </div>
                                    </label>

                                    <label class="option-card" onclick="toggleCheckbox('e_marketing')">
                                        <input type="checkbox" name="e_marketing" id="e_marketing" value="1">
                                        <div class="option-content">
                                            <h5><i class="bx bx-bullhorn" style="color: #ffc107;"></i> É Marketing / Promocional</h5>
                                            <p>Mensagens comerciais só serão enviadas se o cliente autorizou (LGPD).</p>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                                    <label><i class="bx bx-code"></i> Resumo do Template</label>
                                    <div id="template-summary" style="font-size: 13px; color: #666; line-height: 1.8;">
                                        Preencha os passos anteriores para ver o resumo...
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Navegação -->
                    <div class="wizard-nav">
                        <button type="button" class="btn-wizard btn-wizard-prev" id="btn-prev" onclick="prevStep()" disabled>
                            <i class="bx bx-left-arrow-alt"></i> Anterior
                        </button>
                        <div>
                            <span id="step-indicator" style="color: #999; font-size: 14px; margin-right: 20px;">Passo 1 de 5</span>
                            <button type="button" class="btn-wizard btn-wizard-next" id="btn-next" onclick="nextStep()">
                                Próximo <i class="bx bx-right-arrow-alt"></i>
                            </button>
                            <button type="submit" class="btn-wizard btn-wizard-save" id="btn-save" style="display: none;">
                                <i class="bx bx-save"></i> Criar Template
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Estado do wizard
let currentStep = 1;
const totalSteps = 5;

// Dados de exemplo para preview
const dadosExemplo = {
    'cliente_nome': 'João Silva',
    'cliente_telefone': '(11) 99999-9999',
    'cliente_email': 'joao@email.com',
    'cliente_documento': '123.456.789-00',
    'data_atual': new Date().toLocaleDateString('pt-BR'),
    'hora_atual': new Date().toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'}),
    'emitente_nome': '<?php echo addslashes($this->session->userdata("nome") ?: "Sua Empresa"); ?>',
    'emitente_telefone': '(00) 0000-0000',
    'emitente_endereco': 'Rua Exemplo, 123 - Centro',
    'emitente_horario': 'Seg-Sex: 08h às 18h',
    'link_sistema': '<?php echo base_url(); ?>',
    'os_id': '1234',
    'equipamento': 'iPhone 12 Pro',
    'defeito': 'Tela quebrada',
    'data_previsao': '25/04/2026',
    'link_consulta': '<?php echo base_url(); ?>/consulta/1234',
    'status_atual': 'Em Andamento',
    'status_anterior': 'Aberto',
    'valor_total': '850,00',
    'valor_orcamento': '450,00',
    'tempo_estimado': '2-3 dias úteis',
    'link_aprovar': '<?php echo base_url(); ?>/aprovar',
    'link_recusar': '<?php echo base_url(); ?>/recusar',
    'pecas_aguardando': 'Tela iPhone 12',
    'previsao_peca': '20/04/2026',
    'venda_id': '567',
    'valor': '299,90',
    'data_venda': '20/04/2026',
    'referente': 'OS #1234',
    'data_vencimento': '25/04/2026',
    'link_pagamento': '<?php echo base_url(); ?>/pagar',
    'dias': '3',
    'cupom_desconto': 'ANIV2026',
    'mensagem': 'Sua mensagem aqui'
};

// Variáveis por categoria
const varsPorCategoria = {
    'os': [
        {chave: 'os_id', desc: 'Número da OS'},
        {chave: 'equipamento', desc: 'Equipamento'},
        {chave: 'defeito', desc: 'Defeito informado'},
        {chave: 'data_previsao', desc: 'Previsão de conclusão'},
        {chave: 'status_atual', desc: 'Status atual'},
        {chave: 'status_anterior', desc: 'Status anterior'},
        {chave: 'valor_total', desc: 'Valor total'},
        {chave: 'valor_orcamento', desc: 'Valor orçamento'},
        {chave: 'tempo_estimado', desc: 'Tempo estimado'},
        {chave: 'link_consulta', desc: 'Link consulta OS'},
        {chave: 'link_aprovar', desc: 'Link aprovar'},
        {chave: 'link_recusar', desc: 'Link recusar'},
        {chave: 'pecas_aguardando', desc: 'Peças aguardando'},
        {chave: 'previsao_peca', desc: 'Previsão chegada peça'}
    ],
    'venda': [
        {chave: 'venda_id', desc: 'Número da venda'},
        {chave: 'valor_total', desc: 'Valor total'},
        {chave: 'valor', desc: 'Valor da venda'},
        {chave: 'data_venda', desc: 'Data da venda'}
    ],
    'cobranca': [
        {chave: 'referente', desc: 'Referência'},
        {chave: 'valor', desc: 'Valor da cobrança'},
        {chave: 'data_vencimento', desc: 'Data vencimento'},
        {chave: 'dias', desc: 'Dias até vencimento'},
        {chave: 'link_pagamento', desc: 'Link pagamento'}
    ],
    'marketing': [
        {chave: 'cupom_desconto', desc: 'Cupom de desconto'},
        {chave: 'promocao_nome', desc: 'Nome da promoção'},
        {chave: 'validade_oferta', desc: 'Validade da oferta'}
    ],
    'sistema': [],
    'personalizado': []
};

// Exemplos de mensagens
const exemplosMensagens = {
    'personalizado': '',
    'os_criada': `Olá {cliente_nome}! 👋

Sua Ordem de Serviço #{os_id} foi registrada em nosso sistema.

📋 *Equipamento:* {equipamento}
📝 *Defeito:* {defeito}
📅 *Previsão:* {data_previsao}

Acompanhe o status pelo link:
{link_consulta}

Obrigado pela preferência! 🤝`,
    'os_pronta': `Olá {cliente_nome}! 🎉

Sua Ordem de Serviço #{os_id} está *PRONTA*! ✅

📋 {equipamento}
💰 *Valor:* R$ {valor_total}

📍 *Retirada em:*
{emitente_endereco}
⏰ *Funcionamento:* {emitente_horario}

Dúvidas? Responda aqui ou ligue {emitente_telefone}`,
    'venda_realizada': `Olá {cliente_nome}! 🛒

Sua compra foi registrada com sucesso!

*Venda #{venda_id}*
💰 *Valor:* R$ {valor_total}
📅 *Data:* {data_venda}

Agradecemos sua preferência! 💙`,
    'cobranca_gerada': `Olá {cliente_nome}! 💳

Sua cobrança foi gerada:

*Referente a:* {referente}
💰 *Valor:* R$ {valor}
📅 *Vencimento:* {data_vencimento}

💳 *Pagar agora:* {link_pagamento}

Após o pagamento, envie o comprovante aqui! ✅`,
    'cobranca_lembrete': `Olá {cliente_nome}! ⏰

Lembrete: sua cobrança vence em {dias} dia(s)!

*Valor:* R$ {valor}
📅 *Vencimento:* {data_vencimento}

💳 *Pagar:* {link_pagamento}

Evite multas e juros! 🙏`,
    'marketing_aniversario': `🎂 *Feliz Aniversário, {cliente_nome}!* 🎉

Desejamos um dia incrível cheio de conquistas!

🎁 *Presente:* {cupom_desconto}
Válido por 7 dias!

Obrigado por fazer parte da nossa história! 💙`,
    'marketing_promo': `Olá {cliente_nome}! 🎁

*Temos uma oferta especial para você!*

Aproveite descontos exclusivos em nossos serviços.

🎫 *Use o código:* {cupom_desconto}
⏰ *Válido até:* {validade_oferta}

Acesse: {link_sistema}

Não perca! 🚀`
};

// Navegação do wizard
function goToStep(step) {
    if (step < 1 || step > totalSteps) return;

    currentStep = step;

    // Atualizar sidebar
    document.querySelectorAll('.wizard-step').forEach((el, idx) => {
        el.classList.remove('active', 'completed');
        if (idx + 1 === step) {
            el.classList.add('active');
        } else if (idx + 1 < step) {
            el.classList.add('completed');
        }
    });

    // Mostrar painel correto
    document.querySelectorAll('.step-panel').forEach((el, idx) => {
        el.classList.remove('active');
        if (idx + 1 === step) {
            el.classList.add('active');
        }
    });

    // Atualizar botões
    document.getElementById('btn-prev').disabled = step === 1;
    document.getElementById('btn-next').style.display = step === totalSteps ? 'none' : 'inline-flex';
    document.getElementById('btn-save').style.display = step === totalSteps ? 'inline-flex' : 'none';
    document.getElementById('step-indicator').textContent = `Passo ${step} de ${totalSteps}`;

    // Ações específicas por passo
    if (step === 3) {
        atualizarPreview();
    }
    if (step === 5) {
        atualizarResumo();
    }
}

function nextStep() {
    if (currentStep < totalSteps) {
        goToStep(currentStep + 1);
    }
}

function prevStep() {
    if (currentStep > 1) {
        goToStep(currentStep - 1);
    }
}

// Validação da chave
function validarChave() {
    const chave = document.getElementById('chave').value;
    const chaveLimpa = chave.toLowerCase().replace(/[^a-z0-9_]/g, '');
    if (chave !== chaveLimpa) {
        document.getElementById('chave').value = chaveLimpa;
        alert('A chave foi ajustada para conter apenas letras minúsculas, números e underline.');
    }
}

// Usar exemplo pré-definido
function usarExemplo(tipo) {
    const mensagem = exemplosMensagens[tipo] || '';
    document.getElementById('mensagem').value = mensagem;

    // Atualizar categoria se corresponder
    if (tipo !== 'personalizado') {
        const categoria = tipo.split('_')[0];
        if (varsPorCategoria[categoria]) {
            document.getElementById('categoria').value = categoria;
            atualizarVariaveisCategoria();
        }
    }

    // Marcar como selecionado visualmente
    document.querySelectorAll('.example-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');

    // Avançar para próximo passo
    setTimeout(() => nextStep(), 300);
}

// Atualizar variáveis por categoria
function atualizarVariaveisCategoria() {
    const categoria = document.getElementById('categoria').value;
    const container = document.getElementById('vars-categoria');

    // Filtrar exemplos visíveis
    document.querySelectorAll('.example-card').forEach(card => {
        if (!card.dataset.categoria) return;
        if (!categoria || card.dataset.categoria === categoria) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });

    if (!categoria || !varsPorCategoria[categoria]) {
        container.innerHTML = '<div style="color: #999; font-size: 13px; font-style: italic;">Selecione uma categoria no Passo 1 para ver variáveis específicas</div>';
        return;
    }

    const vars = varsPorCategoria[categoria];
    if (vars.length === 0) {
        container.innerHTML = '<div style="color: #999; font-size: 13px; font-style: italic;">Esta categoria não possui variáveis específicas adicionais</div>';
        return;
    }

    container.innerHTML = vars.map(v => `
        <span class="var-chip" onclick="inserirVariavel('{${v.chave}}')" title="${v.desc}">
            {${v.chave}}
            <span class="desc">${v.desc}</span>
        </span>
    `).join('');
}

// Inserir variável no textarea
function inserirVariavel(variavel) {
    const textarea = document.getElementById('mensagem');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;

    textarea.value = text.substring(0, start) + variavel + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + variavel.length;
    textarea.focus();

    atualizarPreview();

    // Feedback visual
    if (event && event.target) {
        const chip = event.target.closest('.var-chip') || event.target.closest('.variable-tag');
        if (chip) {
            chip.style.transform = 'scale(0.95)';
            setTimeout(() => chip.style.transform = '', 150);
        }
    }
}

// Inserir emoji
function inserirEmoji(emoji) {
    const textarea = document.getElementById('mensagem');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;

    textarea.value = text.substring(0, start) + emoji + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
    textarea.focus();

    atualizarPreview();
}

// Atualizar preview
function atualizarPreview() {
    const mensagem = document.getElementById('mensagem').value;
    const previewArea = document.getElementById('preview-area');

    // Atualizar contador
    document.getElementById('char-count').textContent = mensagem.length + ' caracteres';

    if (!mensagem.trim()) {
        previewArea.innerHTML = `
            <div class="preview-empty">
                <i class="bx bx-message-square-dots" style="font-size: 48px; display: block; margin-bottom: 15px; color: #bbb;"></i>
                Digite sua mensagem para ver o preview
            </div>
        `;
        return;
    }

    let preview = mensagem;

    // Substitui variáveis de exemplo
    Object.keys(dadosExemplo).forEach(chave => {
        const regex = new RegExp('{' + chave + '}', 'g');
        preview = preview.replace(regex, dadosExemplo[chave]);
    });

    // Pega variáveis personalizadas do formulário
    const varNomes = document.querySelectorAll('input[name="variavel_nome[]"]');
    varNomes.forEach(input => {
        if (input.value) {
            const regex = new RegExp('{' + input.value + '}', 'g');
            preview = preview.replace(regex, '[' + input.value + ']');
        }
    });

    // Remove variáveis não substituídas
    preview = preview.replace(/\{[^}]+\}/g, '[valor]');

    const hora = new Date().toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});

    previewArea.innerHTML = `
        <div class="whatsapp-bubble">
            ${preview.replace(/\n/g, '<br>')}
            <div class="whatsapp-time">
                ${hora} <i class="bx bx-check-double whatsapp-check"></i>
            </div>
        </div>
    `;
}

// Limpar mensagem
function limparMensagem() {
    if (confirm('Tem certeza que deseja limpar a mensagem?')) {
        document.getElementById('mensagem').value = '';
        atualizarPreview();
    }
}

// Toggle do assunto (email)
function toggleAssunto() {
    const canal = document.getElementById('canal').value;
    const campoAssunto = document.getElementById('campo-assunto');
    campoAssunto.style.display = (canal === 'email' || canal === 'todos') ? 'block' : 'none';
}

// Variáveis personalizadas
function adicionarVariavel() {
    const container = document.getElementById('custom-vars-container');
    const row = document.createElement('div');
    row.className = 'custom-var-row';
    row.innerHTML = `
        <input type="text" name="variavel_nome[]" placeholder="Nome (ex: codigo_promocao)" pattern="[a-z0-9_]+">
        <input type="text" name="variavel_desc[]" placeholder="Descrição (ex: Código da promoção)">
        <button type="button" class="btn-icon-action btn-remove" onclick="removerVariavel(this)">
            <i class="bx bx-trash"></i>
        </button>
    `;
    container.appendChild(row);
}

function removerVariavel(btn) {
    const rows = document.querySelectorAll('.custom-var-row');
    if (rows.length > 1) {
        btn.closest('.custom-var-row').remove();
    } else {
        const inputs = btn.closest('.custom-var-row').querySelectorAll('input');
        inputs.forEach(input => input.value = '');
    }
    atualizarPreview();
}

// Toggle checkbox via card
function toggleCheckbox(id) {
    const checkbox = document.getElementById(id);
    checkbox.checked = !checkbox.checked;

    // Atualizar visual
    document.querySelectorAll('.option-card').forEach(card => {
        const cb = card.querySelector('input[type="checkbox"]');
        if (cb.id === id) {
            card.classList.toggle('selected', cb.checked);
        }
    });
}

// Atualizar resumo
function atualizarResumo() {
    const chave = document.getElementById('chave').value || '-';
    const nome = document.getElementById('nome').value || '-';
    const categoria = document.getElementById('categoria');
    const categoriaText = categoria.options[categoria.selectedIndex]?.text || '-';
    const canal = document.getElementById('canal');
    const canalText = canal.options[canal.selectedIndex]?.text || '-';
    const mensagem = document.getElementById('mensagem').value || '(sem mensagem)';
    const ativo = document.getElementById('ativo').checked ? '✅ Ativo' : '❌ Inativo';
    const marketing = document.getElementById('e_marketing').checked ? ' | 📢 Marketing' : '';

    document.getElementById('template-summary').innerHTML = `
        <strong>Identificador:</strong> {${chave}} <br>
        <strong>Nome:</strong> ${nome} <br>
        <strong>Categoria:</strong> ${categoriaText} <br>
        <strong>Canal:</strong> ${canalText} <br>
        <strong>Status:</strong> ${ativo}${marketing} <br>
        <strong>Mensagem:</strong> <pre style="margin: 10px 0 0 0; background: white; padding: 10px; border-radius: 6px;">${mensagem.substring(0, 200)}${mensagem.length > 200 ? '...' : ''}</pre>
    `;
}

// Validação do formulário
document.getElementById('formTemplate').addEventListener('submit', function(e) {
    const chave = document.getElementById('chave').value;
    const mensagem = document.getElementById('mensagem').value;

    if (!/^[a-z0-9_]+$/.test(chave)) {
        e.preventDefault();
        alert('A chave deve conter apenas letras minúsculas, números e underline.');
        goToStep(1);
        return false;
    }

    if (!mensagem.trim()) {
        e.preventDefault();
        alert('A mensagem não pode estar vazia.');
        goToStep(3);
        return false;
    }
});

// Inicializar
$(document).ready(function() {
    atualizarVariaveisCategoria();
    atualizarPreview();
    toggleAssunto();
});
</script>
