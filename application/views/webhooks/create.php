<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li><a href="<?= site_url('webhooks') ?>">Webhooks</a><span class="divider">/</span></li>
            <li class="active">Novo Webhook</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-webhook"></i></span>
                <h5>Novo Webhook</h5>
            </div>
            <div class="widget-content">
                <form action="<?= site_url('webhooks/create') ?>" method="post" class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label" for="name">Nome:</label>
                        <div class="controls">
                            <input type="text" name="name" id="name" class="span6" required placeholder="Ex: Integração ERP">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="url">URL:</label>
                        <div class="controls">
                            <input type="url" name="url" id="url" class="span6" required placeholder="https://exemplo.com/webhook">
                            <span class="help-block">URL que receberá as notificações POST</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="secret">Secret (opcional):</label>
                        <div class="controls">
                            <input type="text" name="secret" id="secret" class="span6" placeholder="Deixe em branco para gerar automaticamente">
                            <span class="help-block">Usado para assinar os payloads (HMAC SHA-256)</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Eventos:</label>
                        <div class="controls">
                            <?php foreach ($events as $key => $label): ?>
                                <label class="checkbox">
                                    <input type="checkbox" name="events[]" value="<?= $key ?>"> <?= $label ?>
                                </label>
                            <?php endforeach; ?>
                            <hr>
                            <label class="checkbox">
                                <input type="checkbox" id="select-all"> <strong>Selecionar todos</strong>
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="active" value="1" checked> Webhook ativo
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                        <a href="<?= site_url('webhooks') ?>" class="btn">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="events[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
