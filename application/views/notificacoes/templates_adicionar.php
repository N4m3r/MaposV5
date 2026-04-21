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
    select.form-control {
        height: 40px;
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
    .variables-builder {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .variables-builder h5 {
        margin: 0 0 15px 0;
        font-size: 14px;
        color: #495057;
    }
    .variable-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        align-items: center;
    }
    .variable-row input {
        flex: 1;
        padding: 8px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
    }
    .variable-row input:first-child {
        flex: 0.4;
    }
    .btn-add-var, .btn-remove-var {
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
    }
    .btn-add-var {
        background: #28a745;
        color: white;
    }
    .btn-add-var:hover {
        background: #218838;
    }
    .btn-remove-var {
        background: #dc3545;
        color: white;
    }
    .btn-remove-var:hover {
        background: #c82333;
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
    .checkbox-group {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .checkbox-group label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: normal;
        cursor: pointer;
    }
    .alert-info {
        background: #d1ecf1;
        border: 1px solid #bee5eb;
        color: #0c5460;
        padding: 12px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .alert-info i {
        margin-right: 8px;
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
        margin-bottom: 20px;
    }
    .alert-info i {
        margin-right: 8px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="bx bx-plus-circle"></i>
                </span>
                <h5>Adicionar Novo Template</h5>
            </div>
            <div class="widget-content nopadding">
                <form action="<?php echo current_url(); ?>" id="formTemplate" method="post" class="edit-form">

                    <div class="alert-info">
                        <i class="bx bx-info-circle"></i>
                        <strong>Dica:</strong> Crie templates personalizados para usar no sistema. Use variáveis entre chaves {exemplo} para dados dinâmicos.
                    </div>

                    <!-- Informações Básicas -->
                    <div class="form-section">
                        <h4><i class="bx bx-info-circle"></i> Informações Básicas</h4>

                        <div class="form-group">
                            <label for="chave">Chave do Template <span class="required">*</span></label>
                            <input type="text" name="chave" id="chave" class="form-control" required
                                   pattern="[a-z0-9_]+"
                                   placeholder="Ex: minha_mensagem_personalizada"
                                   onchange="validarChave()">
                            <div class="help-text">Identificador único: apenas letras minúsculas, números e underline. Ex: boas_vindas_cliente</div>
                        </div>

                        <div class="form-group">
                            <label for="nome">Nome <span class="required">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control" required
                                   placeholder="Ex: Mensagem de Boas-vindas">
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <input type="text" name="descricao" id="descricao" class="form-control"
                                   placeholder="Quando este template é usado?">
                        </div>

                        <div class="form-group">
                            <label for="categoria">Categoria <span class="required">*</span></label>
                            <select name="categoria" id="categoria" class="form-control" required>
                                <?php foreach ($categorias as $key => $nome): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $nome; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="canal">Canal <span class="required">*</span></label>
                            <select name="canal" id="canal" class="form-control" required onchange="toggleAssunto()">
                                <?php foreach ($canais as $key => $nome): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $nome; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Variáveis Globais Disponíveis -->
                    <div class="form-section">
                        <h4><i class="bx bx-globe"></i> Variáveis Globais Disponíveis</h4>
                        <div class="help-text" style="margin-bottom: 10px;">
                            Você pode usar estas variáveis em qualquer template. Clique para copiar:
                        </div>
                        <div class="variable-list" style="margin-bottom: 15px;">
                            <?php foreach ($variaveis_globais as $chave => $descricao): ?>
                                <span class="variable-tag" onclick="copiarVariavel('{<?php echo $chave; ?>}')" title="<?php echo htmlspecialchars($descricao); ?>">
                                    {<?php echo $chave; ?>}
                                    <span><?php echo htmlspecialchars($descricao); ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Variáveis Personalizadas -->
                    <div class="form-section">
                        <h4><i class="bx bx-variable"></i> Variáveis do Template (Personalizadas)</h4>
                        <div class="help-text" style="margin-bottom: 15px;">
                            Defina variáveis exclusivas deste template. Elas serão substituídas pelos valores reais no momento do envio.
                        </div>

                        <div class="variables-builder">
                            <div id="variaveis-container">
                                <div class="variable-row">
                                    <input type="text" name="variavel_nome[]" placeholder="Nome da variável (ex: cliente_nome)" pattern="[a-z0-9_]+">
                                    <input type="text" name="variavel_desc[]" placeholder="Descrição (ex: Nome do cliente)">
                                    <button type="button" class="btn-remove-var" onclick="removerVariavel(this)"><i class="bx bx-minus"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn-add-var" onclick="adicionarVariavel()">
                                <i class="bx bx-plus"></i> Adicionar Variável
                            </button>
                        </div>
                    </div>

                    <!-- Conteúdo da Mensagem -->
                    <div class="form-section">
                        <h4><i class="bx bx-message"></i> Conteúdo da Mensagem</h4>

                        <div class="form-group" id="campo-assunto" style="display: none;">
                            <label for="assunto">Assunto (para E-mail)</label>
                            <input type="text" name="assunto" id="assunto" class="form-control"
                                   placeholder="Assunto do e-mail">
                        </div>

                        <div class="form-group">
                            <label for="mensagem">Mensagem <span class="required">*</span></label>
                            <textarea name="mensagem" id="mensagem" class="form-control" required
                                      placeholder="Digite sua mensagem aqui... Use {variavel} para inserir variáveis dinâmicas."
                                      oninput="atualizarPreview()"></textarea>
                            <div class="help-text">
                                Use as variáveis definidas acima entre chaves {exemplo}. A mensagem pode incluir emojis.
                                <a href="https://getemoji.com/" target="_blank">Obter emojis</a>
                            </div>
                        </div>

                        <div class="preview-section">
                            <h5><i class="bx bx-show"></i> Preview (simulação WhatsApp)</h5>
                            <div class="preview-whatsapp">
                                <div id="preview-content" class="preview-content">
                                    Preview da mensagem aparecerá aqui...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opções -->
                    <div class="form-section">
                        <h4><i class="bx bx-cog"></i> Opções</h4>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <label>
                                    <input type="checkbox" name="ativo" value="1" checked>
                                    Template Ativo
                                </label>
                                <label>
                                    <input type="checkbox" name="e_marketing" value="1">
                                    É Marketing (requer consentimento do cliente)
                                </label>
                            </div>
                            <div class="help-text">Templates de marketing só são enviados se o cliente autorizou comunicações comerciais.</div>
                        </div>
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
// Dados de exemplo para preview
const dadosExemplo = {
    'cliente_nome': 'João Silva',
    'cliente_telefone': '(11) 99999-9999',
    'cliente_email': 'joao@email.com',
    'data_atual': new Date().toLocaleDateString('pt-BR'),
    'hora_atual': new Date().toLocaleTimeString('pt-BR'),
    'emitente_nome': '<?php echo $this->session->userdata("nome"); ?>',
    'emitente_telefone': '(00) 0000-0000',
    'link_sistema': '<?php echo base_url(); ?>',
    'mensagem': 'Sua mensagem personalizada'
};

function validarChave() {
    const chave = document.getElementById('chave').value;
    const chaveLimpa = chave.toLowerCase().replace(/[^a-z0-9_]/g, '');
    if (chave !== chaveLimpa) {
        document.getElementById('chave').value = chaveLimpa;
        alert('A chave foi ajustada para conter apenas letras minúsculas, números e underline.');
    }
}

function adicionarVariavel() {
    const container = document.getElementById('variaveis-container');
    const row = document.createElement('div');
    row.className = 'variable-row';
    row.innerHTML = `
        <input type="text" name="variavel_nome[]" placeholder="Nome da variável (ex: cliente_nome)" pattern="[a-z0-9_]+">
        <input type="text" name="variavel_desc[]" placeholder="Descrição (ex: Nome do cliente)">
        <button type="button" class="btn-remove-var" onclick="removerVariavel(this)"><i class="bx bx-minus"></i></button>
    `;
    container.appendChild(row);
}

function removerVariavel(btn) {
    const rows = document.querySelectorAll('.variable-row');
    if (rows.length > 1) {
        btn.closest('.variable-row').remove();
    } else {
        // Limpa o último em vez de remover
        const inputs = btn.closest('.variable-row').querySelectorAll('input');
        inputs.forEach(input => input.value = '');
    }
    atualizarPreview();
}

function toggleAssunto() {
    const canal = document.getElementById('canal').value;
    const campoAssunto = document.getElementById('campo-assunto');
    if (canal === 'email' || canal === 'todos') {
        campoAssunto.style.display = 'block';
    } else {
        campoAssunto.style.display = 'none';
    }
}

function atualizarPreview() {
    const mensagem = document.getElementById('mensagem').value;
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

    document.getElementById('preview-content').innerHTML = preview.replace(/\n/g, '<br>');
}

// Validação do formulário
document.getElementById('formTemplate').addEventListener('submit', function(e) {
    const chave = document.getElementById('chave').value;
    if (!/^[a-z0-9_]+$/.test(chave)) {
        e.preventDefault();
        alert('A chave deve conter apenas letras minúsculas, números e underline.');
        return false;
    }
});function copiarVariavel(variavel) {
    const textarea = document.getElementById('mensagem');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;

    textarea.value = text.substring(0, start) + variavel + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + variavel.length;
    textarea.focus();

    atualizarPreview();

    // Feedback visual
    const tag = event.target.closest('.variable-tag');
    tag.style.background = '#28a745';
    tag.style.color = 'white';
    tag.style.borderColor = '#28a745';
    setTimeout(() => {
        tag.style.background = '';
        tag.style.color = '';
        tag.style.borderColor = '';
    }, 300);
}

// Inicializa
$(document).ready(function() {
    atualizarPreview();
    toggleAssunto();
});
</script>
