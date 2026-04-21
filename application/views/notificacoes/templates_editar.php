<style>
    .edit-form {
        padding: 20px;
    }
    .form-section {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #ddd);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .form-section h4 {
        margin: 0 0 20px 0;
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
    .form-group label .required {
        color: #dc3545;
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
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
    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }
    .help-text {
        font-size: 12px;
        color: var(--help-text-color, #666);
        margin-top: 5px;
    }
    .variables-section {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .variables-section h5 {
        margin: 0 0 10px 0;
        font-size: 14px;
        color: #495057;
    }
    .variable-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .variable-tag {
        background: #e9ecef;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 4px 10px;
        font-size: 13px;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s;
        font-family: monospace;
    }
    .variable-tag:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    .variable-tag span {
        font-size: 11px;
        color: #6c757d;
        margin-left: 5px;
    }
    .variable-tag:hover span {
        color: rgba(255,255,255,0.8);
    }
    .preview-section {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        margin-top: 20px;
    }
    .preview-section h5 {
        margin: 0 0 10px 0;
        font-size: 14px;
        color: #495057;
    }
    .preview-content {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        font-size: 14px;
        white-space: pre-wrap;
        min-height: 80px;
    }
    .preview-whatsapp {
        background: #dcf8c6;
        border-radius: 8px;
        padding: 12px;
        max-width: 80%;
        margin-left: auto;
        position: relative;
    }
    .preview-whatsapp::after {
        content: '';
        position: absolute;
        right: -8px;
        top: 50%;
        border: 8px solid transparent;
        border-left-color: #dcf8c6;
        transform: translateY(-50%);
    }
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-success {
        background: #28a745;
        color: white;
    }
    .btn-success:hover {
        background: #1e7e34;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    .btn-secondary:hover {
        background: #545b62;
    }
    .btn-info {
        background: #17a2b8;
        color: white;
    }
    .btn-info:hover {
        background: #117a8b;
    }
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-top: 1px solid var(--border-color, #ddd);
    }
    .readonly-info {
        background: #e9ecef;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 13px;
        color: #495057;
        font-family: monospace;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="bx bx-edit"></i>
                </span>
                <h5>Editar Template: <?php echo htmlspecialchars($template->nome); ?></h5>
            </div>
            <div class="widget-content nopadding">
                <form action="<?php echo current_url(); ?>" id="formTemplate" method="post" class="edit-form">

                    <!-- Informações Básicas -->
                    <div class="form-section">
                        <h4><i class="bx bx-info-circle"></i> Informações Básicas</h4>

                        <div class="form-group">
                            <label>Chave do Template</label>
                            <div class="readonly-info"><?php echo $template->chave; ?></div>
                            <div class="help-text">Identificador único usado no código (não pode ser alterado)</div>
                        </div>

                        <div class="form-group">
                            <label for="nome">Nome <span class="required">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control" required
                                   value="<?php echo htmlspecialchars($template->nome); ?>"
                                   placeholder="Ex: OS Criada">
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <input type="text" name="descricao" id="descricao" class="form-control"
                                   value="<?php echo htmlspecialchars($template->descricao); ?>"
                                   placeholder="Breve descrição de quando este template é usado">
                        </div>

                        <div class="form-group">
                            <label>Categoria</label>
                            <div class="readonly-info"><?php echo ucfirst($template->categoria); ?></div>
                        </div>

                        <div class="form-group">
                            <label>Canal</label>
                            <div class="readonly-info"><?php echo strtoupper($template->canal); ?></div>
                        </div>
                    </div>

                    <!-- Variáveis Disponíveis -->
                    <?php if (!empty($variaveis)): ?>
                        <div class="form-section">
                            <h4><i class="bx bx-variable"></i> Variáveis Disponíveis</h4>
                            <div class="help-text" style="margin-bottom:10px;">Clique em uma variável para inserir no texto:</div>
                            <div class="variables-section">
                                <div class="variable-list">
                                    <?php foreach ($variaveis as $chave => $descricao): ?>
                                        <span class="variable-tag" onclick="inserirVariavel('{<?php echo $chave; ?>}')" title="<?php echo htmlspecialchars($descricao); ?>">
                                            {<?php echo $chave; ?>}
                                            <span><?php echo htmlspecialchars($descricao); ?></span>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Conteúdo da Mensagem -->
                    <div class="form-section">
                        <h4><i class="bx bx-message"></i> Conteúdo da Mensagem</h4>

                        <?php if ($template->canal == 'email' || $template->canal == 'todos'): ?>
                            <div class="form-group">
                                <label for="assunto">Assunto (para E-mail)</label>
                                <input type="text" name="assunto" id="assunto" class="form-control"
                                       value="<?php echo htmlspecialchars($template->assunto); ?>"
                                       placeholder="Assunto do e-mail">
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="mensagem">Mensagem <span class="required">*</span></label>
                            <textarea name="mensagem" id="mensagem" class="form-control" required
                                      oninput="atualizarPreview()"><?php echo htmlspecialchars($template->mensagem); ?></textarea>
                            <div class="help-text">Use as variáveis acima entre chaves {exemplo}. A mensagem pode incluir emojis. <a href="https://getemoji.com/" target="_blank">Obter emojis</a></div>
                        </div>

                        <div class="preview-section">
                            <h5><i class="bx bx-show"></i> Preview (simulação WhatsApp)</h5>
                            <div class="preview-whatsapp">
                                <div id="preview-content" class="preview-content">
                                    <?php echo nl2br(htmlspecialchars($template->mensagem)); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opções -->
                    <div class="form-section">
                        <h4><i class="bx bx-cog"></i> Opções</h4>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="ativo" value="1" <?php echo $template->ativo ? 'checked' : ''; ?>>
                                Template Ativo
                            </label>
                            <div class="help-text">Templates inativos não são usados automaticamente</div>
                        </div>

                        <?php if ($template->e_marketing): ?>
                            <div class="form-group">
                                <div class="help-text" style="color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px;">
                                    <i class="bx bx-info-circle"></i> Este é um template de marketing. Certifique-se de que o cliente autorizou receber comunicações comerciais.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botões -->
                    <div class="form-actions">
                        <a href="<?php echo site_url('notificacoesConfig/templates'); ?>" class="btn-action btn-secondary">
                            <i class="bx bx-arrow-back"></i> Voltar
                        </a>
                        <div>
                            <button type="button" class="btn-action btn-info" onclick="atualizarPreview()">
                                <i class="bx bx-refresh"></i> Atualizar Preview
                            </button>
                            <button type="submit" class="btn-action btn-success">
                                <i class="bx bx-save"></i> Salvar Template
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Dados de exemplo para preview
const dadosExemplo = {
    'cliente_nome': 'João Silva',
    'os_id': '1234',
    'equipamento': 'iPhone 12 Pro',
    'defeito': 'Tela quebrada',
    'data_previsao': '25/04/2026',
    'link_consulta': 'https://suaempresa.com.br/os/1234',
    'emitente_nome': 'Minha Empresa',
    'emitente_telefone': '(11) 99999-9999',
    'emitente_endereco': 'Rua Exemplo, 123 - Centro',
    'emitente_horario': 'Seg-Sex: 08h às 18h',
    'status_atual': 'Em Andamento',
    'status_anterior': 'Aberto',
    'valor_total': '850,00',
    'valor_orcamento': '450,00',
    'tempo_estimado': '2-3 dias úteis',
    'link_aprovar': 'https://suaempresa.com.br/aprovar',
    'link_recusar': 'https://suaempresa.com.br/recusar',
    'pecas_aguardando': 'Tela iPhone 12',
    'previsao_peca': '20/04/2026',
    'venda_id': '567',
    'valor': '299,90',
    'data_venda': '20/04/2026',
    'referente': 'OS #1234',
    'data_vencimento': '25/04/2026',
    'link_pagamento': 'https://suaempresa.com.br/pagar',
    'dias': '3',
    'cupom_desconto': 'ANIV2026',
    'mensagem': 'Mensagem de teste'
};

function inserirVariavel(variavel) {
    const textarea = document.getElementById('mensagem');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;

    textarea.value = text.substring(0, start) + variavel + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + variavel.length;
    textarea.focus();

    atualizarPreview();
}

function atualizarPreview() {
    const mensagem = document.getElementById('mensagem').value;
    let preview = mensagem;

    // Substitui variáveis pelos dados de exemplo
    Object.keys(dadosExemplo).forEach(chave => {
        const regex = new RegExp('{' + chave + '}', 'g');
        preview = preview.replace(regex, dadosExemplo[chave]);
    });

    // Remove variáveis não substituídas
    preview = preview.replace(/\{[^}]+\}/g, '[valor]');

    document.getElementById('preview-content').innerHTML = preview.replace(/\n/g, '<br>');
}

// Atualiza preview ao carregar
$(document).ready(function() {
    atualizarPreview();
});
</script>
