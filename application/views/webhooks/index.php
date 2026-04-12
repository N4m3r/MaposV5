<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li class="active">Webhooks</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <a href="<?= site_url('webhooks/create') ?>" class="btn btn-success"><i class="fas fa-plus"></i> Novo Webhook</a>
    </div>
</div>

<div class="row-fluid" style="margin-top: 20px;">
    <!-- Webhooks -->
    <div class="span8">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-webhook"></i></span>
                <h5>Webhooks Configurados</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($webhooks)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>URL</th>
                                <th>Eventos</th>
                                <th width="80">Status</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($webhooks as $webhook): ?>
                                <tr>
                                    <td><?= htmlspecialchars($webhook->name) ?></td>
                                    <td><code><?= htmlspecialchars($webhook->url) ?></code></td>
                                    <td>
                                        <?php
                                        $events = json_decode($webhook->events ?? '[]', true);
                                        foreach ($events as $event): ?>
                                            <span class="label label-info"><?= $event ?></span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td class="center">
                                        <?php if ($webhook->active): ?>
                                            <span class="label label-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="label">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="center">
                                        <a href="<?= site_url('webhooks/edit/' . $webhook->id) ?>" class="btn btn-mini btn-info" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= site_url('webhooks/logs/' . $webhook->id) ?>" class="btn btn-mini btn-warning" title="Logs">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <button onclick="testWebhook(<?= $webhook->id ?>)" class="btn btn-mini btn-success" title="Testar">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <a href="<?= site_url('webhooks/delete/' . $webhook->id) ?>" class="btn btn-mini btn-danger" title="Excluir" onclick="return confirm('Tem certeza?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4>Nenhum webhook configurado</h4>
                        <p>Webhooks permitem integrar o MAPOS com outros sistemas. Clique em "Novo Webhook" para começar.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Logs Recentes -->
    <div class="span4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-history"></i></span>
                <h5>Logs Recentes</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($logs)): ?>
                    <table class="table table-bordered">
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td>
                                        <span class="label"><?= $log->event ?></span>
                                        <br>
                                        <small><?= date('d/m H:i', strtotime($log->created_at)) ?></small>
                                    </td>
                                    <td width="60" class="center">
                                        <?php if ($log->success): ?>
                                            <span class="label label-success"><?= $log->http_code ?></span>
                                        <?php else: ?>
                                            <span class="label label-important"><?= $log->http_code ?: 'ERR' ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">Nenhum log registrado.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                <h5>Documentação</h5>
            </div>
            <div class="widget-content">
                <p><strong>O que são Webhooks?</strong></p>
                <p>Webhooks são notificações em tempo real enviadas para URLs externas quando eventos ocorrem no sistema.</p>
                <hr>
                <p><strong>Eventos disponíveis:</strong></p>
                <ul>
                    <li>OS criada/atualizada/finalizada</li>
                    <li>Cliente criado/atualizado</li>
                    <li>Venda criada/paga</li>
                    <li>Cobrança criada/paga/vencida</li>
                    <li>Produto com estoque baixo</li>
                </ul>
                <hr>
                <p><strong>Cabeçalhos HTTP:</strong></p>
                <pre>Content-Type: application/json
X-Webhook-Signature: sha256=...
X-Webhook-ID: 123</pre>
            </div>
        </div>
    </div>
</div>

<script>
function testWebhook(id) {
    fetch('<?= base_url('webhooks/test/') ?>' + id)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('✓ Webhook testado com sucesso!\nHTTP: ' + data.http_code);
            } else {
                alert('✗ Falha no teste:\n' + (data.error || 'HTTP ' + data.http_code));
            }
        });
}
</script>
