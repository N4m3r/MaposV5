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

<div class="row-fluid">
    <!-- Templates -->
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-envelope"></i></span>
                <h5>Templates de Email</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($templates)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Template</th>
                                <th width="100">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($templates as $template): ?>
                                <tr>
                                    <td><?= ucfirst(str_replace('_', ' ', $template)) ?></td>
                                    <td class="center">
                                        <a href="<?= base_url("emails/preview/{$template}") ?>" class="btn btn-mini btn-info" target="_blank">
                                            <i class="fas fa-eye"></i> Visualizar
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
                <p><strong>Processar Fila:</strong></p>
                <pre>php index.php email cli_process</pre>
                <p><strong>Processar Eventos:</strong></p>
                <pre>php index.php email cli_events</pre>
                <p><strong>Retry Falhos:</strong></p>
                <pre>php index.php email cli_retry</pre>

                <hr>

                <p><strong>Iniciar Worker (Background):</strong></p>
                <pre>php application/bin/email-worker.php start</pre>

                <p><strong>Parar Worker:</strong></p>
                <pre>php application/bin/email-worker.php stop</pre>
            </div>
        </div>
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
                    }
                });
            }
        });
}, 30000);
</script>
