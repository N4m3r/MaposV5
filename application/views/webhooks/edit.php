<?php
$eventsSelected = json_decode($webhook->events ?? '[]', true);
?>

<div class="row">
    <div class="col-12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="dropdown-divider">/</span></li>
            <li><a href="<?= site_url('webhooks') ?>">Webhooks</a><span class="dropdown-divider">/</span></li>
            <li class="active">Editar Webhook</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-webhook"></i></span>
                <h5>Editar Webhook: <?= htmlspecialchars($webhook->name) ?></h5>
            </div>
            <div class="widget-content">
                <form action="<?= site_url('webhooks/edit/' . $webhook->id) ?>" method="post" class="form-horizontal">
                    <div class="mb-3">
                        <label class="form-label" for="name">Nome:</label>
                        <div class="controls">
                            <input type="text" name="name" id="name" class="col-6" required value="<?= htmlspecialchars($webhook->name) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="url">URL:</label>
                        <div class="controls">
                            <input type="url" name="url" id="url" class="col-6" required value="<?= htmlspecialchars($webhook->url) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Eventos:</label>
                        <div class="controls">
                            <?php foreach ($events as $key => $label): ?>
                                <label class="checkbox">
                                    <input type="checkbox" name="events[]" value="<?= $key ?>" <?= in_array($key, $eventsSelected) ? 'checked' : '' ?>> <?= $label ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="active" value="1" <?= $webhook->active ? 'checked' : '' ?>> Webhook ativo
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
                        <a href="<?= site_url('webhooks') ?>" class="btn">Cancelar</a>
                    </div>
                </form>

                <hr>
                <h4>Credenciais da API</h4>
                <div class="well">
                    <p><strong>Webhook ID:</strong> <code><?= $webhook->id ?></code></p>
                    <p><strong>Secret:</strong> <code><?= $webhook->secret ?></code></p>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Mantenha o Secret em segurança!</p>
                </div>
            </div>
        </div>
    </div>
</div>
