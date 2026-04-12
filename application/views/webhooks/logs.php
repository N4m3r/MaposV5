<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li><a href="<?= site_url('webhooks') ?>">Webhooks</a><span class="divider">/</span></li>
            <li class="active">Logs: <?= htmlspecialchars($webhook->name) ?></li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <a href="<?= site_url('webhooks') ?>" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        <a href="<?= site_url('webhooks/test/' . $webhook->id) ?>" class="btn btn-success"><i class="fas fa-play"></i> Testar Webhook</a>
    </div>
</div>

<div class="row-fluid" style="margin-top: 20px;">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-history"></i></span>
                <h5>Histórico de Envios</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($logs)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Evento</th>
                                <th>HTTP</th>
                                <th>Status</th>
                                <th>Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <?php
                                $payload = json_decode($log->payload ?? '{}', true);
                                $response = $log->response;
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?></td>
                                    <td><span class="label label-info"><?= $log->event ?></span></td>
                                    <td><?= $log->http_code ?: '-' ?></td>
                                    <td class="center">
                                        <?php if ($log->success): ?>
                                            <span class="label label-success">Sucesso</span>
                                        <?php else: ?>
                                            <span class="label label-important">Falha</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-mini" onclick="toggleDetails(<?= $log->id ?>)">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                                <tr id="details-<?= $log->id ?>" style="display: none;">
                                    <td colspan="5">
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <h5>Payload Enviado</h5>
                                                <pre style="background: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px;"><?= json_encode($payload, JSON_PRETTY_PRINT) ?></pre>
                                            </div>
                                            <div class="span6">
                                                <h5>Resposta</h5>
                                                <pre style="background: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px;"><?= htmlspecialchars($response) ?: 'Nenhuma resposta' ?></pre>
                                                <?php if ($log->error): ?>
                                                    <h5>Erro</h5>
                                                    <div class="alert alert-danger"><?= htmlspecialchars($log->error) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">Nenhum log encontrado para este webhook.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDetails(id) {
    const row = document.getElementById('details-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>
