<?php
/**
 * View para edição de Template de Email
 */
$ci = &get_instance();
?>

<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li><a href="<?= base_url('emails') ?>">Gerenciamento de Emails</a><span class="divider">/</span></li>
            <li class="active">Editar Template: <?= ucfirst(str_replace('_', ' ', $template_name)) ?></li>
        </ul>
    </div>
</div>

<?php if ($this->session->flashdata('success')): ?>
<div class="row-fluid">
    <div class="span12">
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= $this->session->flashdata('success') ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
<div class="row-fluid">
    <div class="span12">
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i> <?= $this->session->flashdata('error') ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row-fluid">
    <!-- Editor de Template -->
    <div class="span8">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-code"></i></span>
                <h5>Editor HTML - <?= ucfirst(str_replace('_', ' ', $template_name)) ?></h5>
                <div class="buttons">
                    <button type="button" class="btn btn-info btn-mini" onclick="abrirModalTags()">
                        <i class="fas fa-tags"></i> Tags Disponíveis
                    </button>
                    <a href="<?= base_url('emails') ?>" class="btn btn-mini">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= base_url('email/salvar_template') ?>" id="formTemplate">
                    <input type="hidden" name="template" value="<?= $template_name ?>">

                    <div class="control-group">
                        <label class="control-label" for="templateContent">Código HTML do Template:</label>
                        <div class="controls">
                            <textarea name="content" id="templateContent" class="span12" rows="25" style="font-family: 'Consolas', 'Monaco', 'Courier New', monospace; font-size: 13px;"><?= htmlspecialchars($template_content) ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Template
                        </button>
                        <button type="button" class="btn btn-primary" onclick="previewTemplate()">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <a href="<?= base_url('emails') ?>" class="btn">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar com Ajuda -->
    <div class="span4">
        <!-- Tags Rápidas -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-tags"></i></span>
                <h5>Tags Mais Usadas</h5>
            </div>
            <div class="widget-content">
                <p class="text-muted">Clique em uma tag para inseri-la no cursor:</p>
                <div class="well well-small">
                    <?php
                    $tagsRapidas = [
                        '{{cliente_nome}}', '{{os_id}}', '{{os_titulo}}',
                        '{{os_link_visualizar}}', '{{empresa_nome}}',
                        '{{data_atual}}', '{{titulo}}', '{{mensagem}}'
                    ];
                    foreach ($tagsRapidas as $tag): ?>
                        <button type="button" class="btn btn-mini btn-tag" data-tag="<?= $tag ?>" style="margin: 2px;" title="Inserir tag">
                            <code><?= $tag ?></code>
                        </button>
                    <?php endforeach; ?>
                </div>
                <p class="text-info">
                    <i class="fas fa-info-circle"></i> <strong>Dica:</strong> Use o botão acima para ver todas as tags disponíveis.
                </p>
            </div>
        </div>

        <!-- Estrutura Básica -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-code"></i></span>
                <h5>Estrutura HTML Recomendada</h5>
            </div>
            <div class="widget-content">
                <p>O template deve seguir esta estrutura básica:</p>
                <pre style="font-size: 11px; background: #f8f9fa; padding: 10px; border-radius: 4px;">&lt;?php
/**
 * Template: [Nome]
 * Descrição: [Descrição]
 */
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;title&gt;{{titulo}}&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;!-- Conteúdo --&gt;
&lt;/body&gt;
&lt;/html&gt;</pre>
            </div>
        </div>

        <!-- Boas Práticas -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-lightbulb"></i></span>
                <h5>Boas Práticas</h5>
            </div>
            <div class="widget-content">
                <ul style="margin-bottom: 0;">
                    <li>Use <strong>CSS inline</strong> para compatibilidade com clients de email</li>
                    <li>Mantenha a <strong>largura máxima de 600px</strong></li>
                    <li>Teste em diferentes clients de email</li>
                    <li>Adicione sempre um link para visualização no navegador</li>
                    <li>Inclua informações da empresa no rodapé</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Tags Disponíveis -->
<div id="modalTags" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalTagsLabel" aria-hidden="true" style="width: 700px; margin-left: -350px;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modalTagsLabel"><i class="fas fa-tags"></i> Tags Disponíveis para Templates</h3>
    </div>
    <div class="modal-body" style="max-height: 450px; overflow-y: auto;">
        <p class="text-info"><i class="fas fa-info-circle"></i> Clique em qualquer tag para inseri-la no editor:</p>

        <div class="accordion" id="accordionTags">
            <!-- Tags de Cliente -->
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionTags" href="#collapseCliente">
                        <i class="fas fa-user"></i> <strong>Cliente</strong>
                    </a>
                </div>
                <div id="collapseCliente" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <table class="table table-condensed table-striped">
                            <tbody>
                                <tr class="tag-row" data-tag="{{cliente_nome}}"><td><code>{{cliente_nome}}</code></td><td>Nome completo do cliente</td></tr>
                                <tr class="tag-row" data-tag="{{cliente_email}}"><td><code>{{cliente_email}}</code></td><td>Email do cliente</td></tr>
                                <tr class="tag-row" data-tag="{{cliente_telefone}}"><td><code>{{cliente_telefone}}</code></td><td>Telefone do cliente</td></tr>
                                <tr class="tag-row" data-tag="{{cliente_celular}}"><td><code>{{cliente_celular}}</code></td><td>Celular do cliente</td></tr>
                                <tr class="tag-row" data-tag="{{cliente_endereco}}"><td><code>{{cliente_endereco}}</code></td><td>Endereço completo do cliente</td></tr>
                                <tr class="tag-row" data-tag="{{cliente_documento}}"><td><code>{{cliente_documento}}</code></td><td>CPF/CNPJ do cliente</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tags de OS -->
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionTags" href="#collapseOS">
                        <i class="fas fa-wrench"></i> <strong>Ordem de Serviço</strong>
                    </a>
                </div>
                <div id="collapseOS" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <table class="table table-condensed table-striped">
                            <tbody>
                                <tr class="tag-row" data-tag="{{os_id}}"><td><code>{{os_id}}</code></td><td>Número da OS</td></tr>
                                <tr class="tag-row" data-tag="{{os_titulo}}"><td><code>{{os_titulo}}</code></td><td>Título da OS</td></tr>
                                <tr class="tag-row" data-tag="{{os_descricao}}"><td><code>{{os_descricao}}</code></td><td>Descrição da OS</td></tr>
                                <tr class="tag-row" data-tag="{{os_status}}"><td><code>{{os_status}}</code></td><td>Status da OS</td></tr>
                                <tr class="tag-row" data-tag="{{os_data_criacao}}"><td><code>{{os_data_criacao}}</code></td><td>Data de criação da OS</td></tr>
                                <tr class="tag-row" data-tag="{{os_data_vencimento}}"><td><code>{{os_data_vencimento}}</code></td><td>Data de vencimento da OS</td></tr>
                                <tr class="tag-row" data-tag="{{os_valor_total}}"><td><code>{{os_valor_total}}</code></td><td>Valor total da OS</td></tr>
                                <tr class="tag-row" data-tag="{{os_link_visualizar}}"><td><code>{{os_link_visualizar}}</code></td><td>Link para visualizar a OS</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tags de Venda -->
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionTags" href="#collapseVenda">
                        <i class="fas fa-shopping-cart"></i> <strong>Venda</strong>
                    </a>
                </div>
                <div id="collapseVenda" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <table class="table table-condensed table-striped">
                            <tbody>
                                <tr class="tag-row" data-tag="{{venda_id}}"><td><code>{{venda_id}}</code></td><td>ID da venda</td></tr>
                                <tr class="tag-row" data-tag="{{venda_data}}"><td><code>{{venda_data}}</code></td><td>Data da venda</td></tr>
                                <tr class="tag-row" data-tag="{{venda_valor_total}}"><td><code>{{venda_valor_total}}</code></td><td>Valor total da venda</td></tr>
                                <tr class="tag-row" data-tag="{{venda_status}}"><td><code>{{venda_status}}</code></td><td>Status da venda</td></tr>
                                <tr class="tag-row" data-tag="{{venda_link_visualizar}}"><td><code>{{venda_link_visualizar}}</code></td><td>Link para visualizar a venda</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tags de Cobrança -->
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionTags" href="#collapseCobranca">
                        <i class="fas fa-dollar-sign"></i> <strong>Cobrança</strong>
                    </a>
                </div>
                <div id="collapseCobranca" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <table class="table table-condensed table-striped">
                            <tbody>
                                <tr class="tag-row" data-tag="{{cobranca_descricao}}"><td><code>{{cobranca_descricao}}</code></td><td>Descrição da cobrança</td></tr>
                                <tr class="tag-row" data-tag="{{cobranca_valor}}"><td><code>{{cobranca_valor}}</code></td><td>Valor da cobrança</td></tr>
                                <tr class="tag-row" data-tag="{{cobranca_data_vencimento}}"><td><code>{{cobranca_data_vencimento}}</code></td><td>Data de vencimento</td></tr>
                                <tr class="tag-row" data-tag="{{cobranca_dias_atraso}}"><td><code>{{cobranca_dias_atraso}}</code></td><td>Dias de atraso</td></tr>
                                <tr class="tag-row" data-tag="{{cobranca_link_pagamento}}"><td><code>{{cobranca_link_pagamento}}</code></td><td>Link para pagamento</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tags de Sistema -->
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionTags" href="#collapseSistema">
                        <i class="fas fa-cog"></i> <strong>Sistema</strong>
                    </a>
                </div>
                <div id="collapseSistema" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <table class="table table-condensed table-striped">
                            <tbody>
                                <tr class="tag-row" data-tag="{{usuario_nome}}"><td><code>{{usuario_nome}}</code></td><td>Nome do usuário logado</td></tr>
                                <tr class="tag-row" data-tag="{{usuario_email}}"><td><code>{{usuario_email}}</code></td><td>Email do usuário logado</td></tr>
                                <tr class="tag-row" data-tag="{{empresa_nome}}"><td><code>{{empresa_nome}}</code></td><td>Nome da empresa</td></tr>
                                <tr class="tag-row" data-tag="{{empresa_telefone}}"><td><code>{{empresa_telefone}}</code></td><td>Telefone da empresa</td></tr>
                                <tr class="tag-row" data-tag="{{empresa_email}}"><td><code>{{empresa_email}}</code></td><td>Email da empresa</td></tr>
                                <tr class="tag-row" data-tag="{{empresa_endereco}}"><td><code>{{empresa_endereco}}</code></td><td>Endereço da empresa</td></tr>
                                <tr class="tag-row" data-tag="{{data_atual}}"><td><code>{{data_atual}}</code></td><td>Data atual (dd/mm/YYYY)</td></tr>
                                <tr class="tag-row" data-tag="{{hora_atual}}"><td><code>{{hora_atual}}</code></td><td>Hora atual (HH:mm)</td></tr>
                                <tr class="tag-row" data-tag="{{sistema_url}}"><td><code>{{sistema_url}}</code></td><td>URL do sistema</td></tr>
                                <tr class="tag-row" data-tag="{{ano_atual}}"><td><code>{{ano_atual}}</code></td><td>Ano atual (YYYY)</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tags Personalizadas -->
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionTags" href="#collapsePersonalizado">
                        <i class="fas fa-pencil-alt"></i> <strong>Personalizadas</strong>
                    </a>
                </div>
                <div id="collapsePersonalizado" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <table class="table table-condensed table-striped">
                            <tbody>
                                <tr class="tag-row" data-tag="{{titulo}}"><td><code>{{titulo}}</code></td><td>Título do email</td></tr>
                                <tr class="tag-row" data-tag="{{mensagem}}"><td><code>{{mensagem}}</code></td><td>Mensagem principal</td></tr>
                                <tr class="tag-row" data-tag="{{conteudo}}"><td><code>{{conteudo}}</code></td><td>Conteúdo personalizado</td></tr>
                                <tr class="tag-row" data-tag="{{destinatario}}"><td><code>{{destinatario}}</code></td><td>Nome do destinatário</td></tr>
                                <tr class="tag-row" data-tag="{{link}}"><td><code>{{link}}</code></td><td>Link genérico</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
    </div>
</div>

<!-- Modal de Preview -->
<div id="modalPreview" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalPreviewLabel" aria-hidden="true" style="width: 80%; margin-left: -40%;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modalPreviewLabel"><i class="fas fa-eye"></i> Preview do Template</h3>
    </div>
    <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Este é um preview com dados de exemplo. Os valores reais serão substituídos no envio.
        </div>
        <iframe id="previewFrame" src="" style="width: 100%; height: 400px; border: 1px solid #ddd;"></iframe>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <button class="btn btn-primary" onclick="window.open('<?= base_url('email/preview/' . $template_name) ?>', '_blank')">
            <i class="fas fa-external-link-alt"></i> Abrir em Nova Janela
        </button>
    </div>
</div>

<script>
// Referência ao textarea
var editor = document.getElementById('templateContent');

// Abrir modal de tags
function abrirModalTags() {
    $('#modalTags').modal('show');
}

// Preview do template
function previewTemplate() {
    var iframe = document.getElementById('previewFrame');
    iframe.src = '<?= base_url('email/preview/' . $template_name) ?>';
    $('#modalPreview').modal('show');
}

// Inserir tag no cursor do textarea
function insertTag(tag) {
    var start = editor.selectionStart;
    var end = editor.selectionEnd;
    var text = editor.value;

    var before = text.substring(0, start);
    var after = text.substring(end, text.length);

    editor.value = before + tag + after;
    editor.selectionStart = editor.selectionEnd = start + tag.length;
    editor.focus();
}

// Configurar clique nas tags rápidas
document.querySelectorAll('.btn-tag').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var tag = this.getAttribute('data-tag');
        insertTag(tag);
    });
});

// Configurar clique nas tags do modal
document.querySelectorAll('.tag-row').forEach(function(row) {
    row.style.cursor = 'pointer';
    row.addEventListener('click', function() {
        var tag = this.getAttribute('data-tag');
        insertTag(tag);
        $('#modalTags').modal('hide');
    });
});

// Atalho de teclado Ctrl+S para salvar
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.getElementById('formTemplate').submit();
    }
});

// Tab key suporte no textarea
editor.addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        var start = this.selectionStart;
        var end = this.selectionEnd;
        this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
        this.selectionStart = this.selectionEnd = start + 4;
    }
});
</script>
