<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class='bx bx-edit'></i> Editar Template: <?= e($template->nome) ?></h5>
                <a href="<?= site_url('notificacoes/templates') ?>" class="btn btn-sm btn-secondary float-end"><i class='bx bx-arrow-back'></i> Voltar</a>
            </div>
            <div class="card-body">
                <form method="post" action="<?= current_url() ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Chave</label>
                                <input type="text" class="form-control" value="<?= e($template->chave) ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label>Nome *</label>
                                <input type="text" name="nome" class="form-control" required value="<?= e($template->nome) ?>">
                            </div>
                            <div class="form-group">
                                <label>Descricao</label>
                                <textarea name="descricao" class="form-control" rows="2"><?= e($template->descricao ?? '') ?></textarea>
                            </div>
                            <?php if (empty($is_padrao) || !$is_padrao): ?>
                            <div class="form-group">
                                <label>Categoria</label>
                                <select name="categoria" class="form-control">
                                    <?php foreach ($categorias as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= ($template->categoria ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assunto (email)</label>
                                <input type="text" name="assunto" class="form-control" value="<?= e($template->assunto ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Mensagem *</label>
                                <textarea name="mensagem" class="form-control" rows="8" required><?= e($template->mensagem) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="ativo" value="1" <?= ($template->ativo ?? 0) ? 'checked' : '' ?>>
                                    Ativo
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($variaveis_globais)): ?>
                    <div class="mt-3">
                        <h6>Variaveis disponiveis:</h6>
                        <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($variaveis_globais as $key => $desc): ?>
                            <span class="badge bg-info" title="<?= e($desc) ?>">{<?= $key ?>}</span>
                        <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>