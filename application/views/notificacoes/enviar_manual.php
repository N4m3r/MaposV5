<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class='bx bx-send'></i> Enviar Mensagem Manual</h5>
            </div>
            <div class="card-body">
                <form method="post" action="<?= current_url() ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cliente (opcional)</label>
                                <select name="cliente_id" class="form-control">
                                    <option value="">Selecionar cliente...</option>
                                    <?php if (!empty($clientes)): foreach ($clientes as $c): ?>
                                    <option value="<?= $c->idClientes ?>"><?= e($c->nomeCliente) ?> - <?= e($c->celular ?? $c->telefone ?? '') ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Telefone *</label>
                                <input type="text" name="telefone" class="form-control" required placeholder="5511999999999">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mensagem *</label>
                        <textarea name="mensagem" class="form-control" rows="5" required placeholder="Digite a mensagem..."></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-success"><i class='bx bx-send'></i> Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>