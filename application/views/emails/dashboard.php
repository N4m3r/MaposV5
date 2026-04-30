<?php
/**
 * Email Dashboard View
 * Painel administrativo de emails
 */

$ci = &get_instance();
$ci->load->helper('date');
?>

<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li class="active">Gerenciamento de Emails</li>
        </ul>
    </div>
</div>

<?php if (!empty($db_error)): ?>
<div class="row-fluid">
    <div class="span12">
        <div class="alert alert-error">
            <h4><i class="fas fa-exclamation-triangle"></i> Erro no Banco de Dados</h4>
            <p><?= $db_error_message ?></p>
            <hr>
            <p><strong>Para corrigir, execute:</strong></p>
            <pre>php application/database/migrations/run_migrations.php</pre>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row-fluid">
    <!-- Stats Cards -->
    <div class="span3">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-clock"></i></span>
                <h5>Pendentes</h5>
            </div>
            <div class="widget-content">
                <h2 class="text-warning"><?= $stats['pending'] ?? 0 ?></h2>
                <p>Emails na fila de espera</p>
            </div>
        </div>
    </div>

    <div class="span3">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-spinner"></i></span>
                <h5>Processando</h5>
            </div>
            <div class="widget-content">
                <h2 class="text-info"><?= $stats['processing'] ?? 0 ?></h2>
                <p>Emails em processamento</p>
            </div>
        </div>
    </div>

    <div class="span3">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-check-circle"></i></span>
                <h5>Enviados</h5>
            </div>
            <div class="widget-content">
                <h2 class="text-success"><?= $stats['sent'] ?? 0 ?></h2>
                <p>Emails enviados com sucesso</p>
            </div>
        </div>
    </div>

    <div class="span3">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-times-circle"></i></span>
                <h5>Falhas</h5>
            </div>
            <div class="widget-content">
                <h2 class="text-error"><?= $stats['failed'] ?? 0 ?></h2>
                <p>Emails com falha no envio</p>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid" style="margin-top: 15px;">
    <div class="span2 offset1">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-line"></i></span>
                <h5>Taxa de Sucesso</h5>
            </div>
            <div class="widget-content center">
                <h2 class="text-success"><?= $stats['taxa_sucesso'] ?? 0 ?>%</h2>
                <p>Emails enviados/total</p>
            </div>
        </div>
    </div>

    <div class="span2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-calendar-day"></i></span>
                <h5>Enviados Hoje</h5>
            </div>
            <div class="widget-content center">
                <h2 class="text-info"><?= $stats['enviados_hoje'] ?? 0 ?></h2>
                <p>Emails hoje</p>
            </div>
        </div>
    </div>

    <div class="span2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-eye"></i></span>
                <h5>Aberturas</h5>
            </div>
            <div class="widget-content center">
                <h2 class="text-warning"><?= $stats['aberturas'] ?? 0 ?></h2>
                <p>Emails abertos</p>
            </div>
        </div>
    </div>

    <div class="span2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-mouse-pointer"></i></span>
                <h5>Cliques</h5>
            </div>
            <div class="widget-content center">
                <h2 class="text-info"><?= $stats['cliques'] ?? 0 ?></h2>
                <p>Links clicados</p>
            </div>
        </div>
    </div>

    <div class="span2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-exclamation-circle"></i></span>
                <h5>Bounce</h5>
            </div>
            <div class="widget-content center">
                <h2 class="text-error"><?= $stats['bounce'] ?? 0 ?>%</h2>
                <p>Taxa de rejeicao</p>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <!-- Templates -->
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-envelope"></i></span>
                <h5>Templates de Email</h5>
                <div class="buttons">
                    <button type="button" class="btn btn-success btn-mini" onclick="abrirModalTags()">
                        <i class="fas fa-tags"></i> Tags Disponíveis
                    </button>
                </div>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($templates)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Template</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($templates as $template): ?>
                                <tr>
                                    <td><?= ucfirst(str_replace('_', ' ', $template)) ?></td>
                                    <td class="center">
                                        <a href="<?= base_url("email/preview/{$template}") ?>" class="btn btn-mini btn-info" target="_blank" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url("email/editar_template/{$template}") ?>" class="btn btn-mini btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">
                        Nenhum template encontrado.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-cogs"></i></span>
                <h5>Ações</h5>
            </div>
            <div class="widget-content">
                <p><strong>Acoes Rapidas:</strong></p>
                <a href="<?= base_url('email/logs') ?>" class="btn btn-block btn-info">
                    <i class="fas fa-list-alt"></i> Ver Log de Envios
                </a>
                <a href="<?= base_url('email/configuracoes') ?>" class="btn btn-block btn-primary">
                    <i class="fas fa-cog"></i> Configuracoes de Email
                </a>

                <hr>

                <p><strong>Processar Fila (CLI):</strong></p>
                <pre>php index.php email cli_process</pre>
                <p><strong>Retry Falhos:</strong></p>
                <pre>php index.php email cli_retry</pre>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Tags Disponíveis -->
<div id="modalTags" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalTagsLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modalTagsLabel"><i class="fas fa-tags"></i> Tags Disponíveis para Templates</h3>
    </div>
    <div class="modal-body">
        <p class="text-info"><i class="fas fa-info-circle"></i> Use estas tags nos templates de email para personalizar o conteúdo dinamicamente:</p>

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
                                <tr><td><code>{{cliente_nome}}</code></td><td>Nome completo do cliente</td></tr>
                                <tr><td><code>{{cliente_email}}</code></td><td>Email do cliente</td></tr>
                                <tr><td><code>{{cliente_telefone}}</code></td><td>Telefone do cliente</td></tr>
                                <tr><td><code>{{cliente_celular}}</code></td><td>Celular do cliente</td></tr>
                                <tr><td><code>{{cliente_endereco}}</code></td><td>Endereço completo do cliente</td></tr>
                                <tr><td><code>{{cliente_documento}}</code></td><td>CPF/CNPJ do cliente</td></tr>
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
                                <tr><td><code>{{os_id}}</code></td><td>Número da OS</td></tr>
                                <tr><td><code>{{os_titulo}}</code></td><td>Título da OS</td></tr>
                                <tr><td><code>{{os_descricao}}</code></td><td>Descrição da OS</td></tr>
                                <tr><td><code>{{os_status}}</code></td><td>Status da OS</td></tr>
                                <tr><td><code>{{os_data_criacao}}</code></td><td>Data de criação da OS</td></tr>
                                <tr><td><code>{{os_data_vencimento}}</code></td><td>Data de vencimento da OS</td></tr>
                                <tr><td><code>{{os_valor_total}}</code></td><td>Valor total da OS</td></tr>
                                <tr><td><code>{{os_link_visualizar}}</code></td><td>Link para visualizar a OS</td></tr>
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
                                <tr><td><code>{{venda_id}}</code></td><td>ID da venda</td></tr>
                                <tr><td><code>{{venda_data}}</code></td><td>Data da venda</td></tr>
                                <tr><td><code>{{venda_valor_total}}</code></td><td>Valor total da venda</td></tr>
                                <tr><td><code>{{venda_status}}</code></td><td>Status da venda</td></tr>
                                <tr><td><code>{{venda_link_visualizar}}</code></td><td>Link para visualizar a venda</td></tr>
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
                                <tr><td><code>{{cobranca_descricao}}</code></td><td>Descrição da cobrança</td></tr>
                                <tr><td><code>{{cobranca_valor}}</code></td><td>Valor da cobrança</td></tr>
                                <tr><td><code>{{cobranca_data_vencimento}}</code></td><td>Data de vencimento</td></tr>
                                <tr><td><code>{{cobranca_dias_atraso}}</code></td><td>Dias de atraso</td></tr>
                                <tr><td><code>{{cobranca_link_pagamento}}</code></td><td>Link para pagamento</td></tr>
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
                                <tr><td><code>{{usuario_nome}}</code></td><td>Nome do usuário logado</td></tr>
                                <tr><td><code>{{usuario_email}}</code></td><td>Email do usuário logado</td></tr>
                                <tr><td><code>{{empresa_nome}}</code></td><td>Nome da empresa</td></tr>
                                <tr><td><code>{{empresa_telefone}}</code></td><td>Telefone da empresa</td></tr>
                                <tr><td><code>{{empresa_email}}</code></td><td>Email da empresa</td></tr>
                                <tr><td><code>{{empresa_endereco}}</code></td><td>Endereço da empresa</td></tr>
                                <tr><td><code>{{data_atual}}</code></td><td>Data atual (dd/mm/YYYY)</td></tr>
                                <tr><td><code>{{hora_atual}}</code></td><td>Hora atual (HH:mm)</td></tr>
                                <tr><td><code>{{sistema_url}}</code></td><td>URL do sistema</td></tr>
                                <tr><td><code>{{ano_atual}}</code></td><td>Ano atual (YYYY)</td></tr>
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
                                <tr><td><code>{{titulo}}</code></td><td>Título do email</td></tr>
                                <tr><td><code>{{mensagem}}</code></td><td>Mensagem principal</td></tr>
                                <tr><td><code>{{conteudo}}</code></td><td>Conteúdo personalizado</td></tr>
                                <tr><td><code>{{destinatario}}</code></td><td>Nome do destinatário</td></tr>
                                <tr><td><code>{{link}}</code></td><td>Link genérico</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info" style="margin-top: 15px; margin-bottom: 0;">
            <strong><i class="fas fa-lightbulb"></i> Dica:</strong> Clique em qualquer tag para copiá-la automaticamente!
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
    </div>
</div>

<script>
// Auto-refresh stats a cada 30 segundos
setInterval(function() {
    fetch('<?= base_url("email/api_stats") ?>')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Update counters
                const stats = data.stats;
                document.querySelectorAll('h2').forEach(el => {
                    const parent = el.closest('.widget-content');
                    if (parent) {
                        const title = parent.previousElementSibling.querySelector('h5')?.textContent;
                        if (title === 'Pendentes') el.textContent = stats.pending;
                        if (title === 'Processando') el.textContent = stats.processing;
                        if (title === 'Enviados') el.textContent = stats.sent;
                        if (title === 'Falhas') el.textContent = stats.failed;
                        if (title === 'Taxa de Sucesso') el.textContent = stats.taxa_sucesso + '%';
                        if (title === 'Enviados Hoje') el.textContent = stats.enviados_hoje;
                        if (title === 'Aberturas') el.textContent = stats.aberturas;
                        if (title === 'Cliques') el.textContent = stats.cliques;
                        if (title === 'Bounce') el.textContent = stats.bounce + '%';
                    }
                });
            }
        });
}, 30000);

// Abrir modal de tags
function abrirModalTags() {
    $('#modalTags').modal('show');
}

// Copiar tag ao clicar
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#modalTags code').forEach(function(el) {
        el.style.cursor = 'pointer';
        el.title = 'Clique para copiar';
        el.addEventListener('click', function() {
            const tag = this.textContent;
            navigator.clipboard.writeText(tag).then(function() {
                // Feedback visual
                const originalBg = el.style.backgroundColor;
                el.style.backgroundColor = '#28a745';
                el.style.color = '#fff';
                setTimeout(function() {
                    el.style.backgroundColor = originalBg;
                    el.style.color = '';
                }, 500);
            });
        });
    });
});
</script>
