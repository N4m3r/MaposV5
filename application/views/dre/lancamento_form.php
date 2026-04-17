<?php
/**
 * Formulário de Lançamento DRE
 */
$is_edit = isset($lancamento) && $lancamento;
$contas = $this->dre_model->getContas(1);
?>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('dre') ?>">DRE Contábil</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('dre/lancamentos') ?>">Lançamentos</a> <span class="divider">/</span></li>
            <li class="active"><?= $is_edit ? 'Editar' : 'Novo' ?> Lançamento</li>
        </ul>
    </div>
</div>

<!-- Formulário -->
<div class="row-fluid">
    <div class="span8 offset2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-<?= $is_edit ? 'edit' : 'plus' ?>"></i></span>
                <h5><?= $is_edit ? 'Editar' : 'Novo' ?> Lançamento</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= site_url('dre/lancamento_salvar') ?>" class="form-horizontal">
                    <input type="hidden" name="id" value="<?= $is_edit ? $lancamento->id : '' ?>" />

                    <div class="control-group">
                        <label class="control-label">Conta: *</label>
                        <div class="controls">
                            <select name="conta_id" class="span8" required>
                                <option value="">Selecione a conta...</option>
                                <?php foreach ($contas as $c): ?>
                                <option value="<?= $c->id ?>" <?= ($is_edit && $lancamento->conta_id == $c->id) ? 'selected' : '' ?>>
                                    <?= $c->codigo ?> - <?= $c->nome ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Data: *</label>
                        <div class="controls">
                            <input type="date" name="data" class="span3" value="<?= $is_edit ? $lancamento->data : date('Y-m-d') ?>" required />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Valor: *</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">R$</span>
                                <input type="text" name="valor" class="span3" value="<?= $is_edit ? number_format($lancamento->valor, 2, ',', '.') : '' ?>" required placeholder="0,00" />
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Tipo: *</label>
                        <div class="controls">
                            <label class="radio inline">
                                <input type="radio" name="tipo_movimento" value="CREDITO" <?= (!$is_edit || $lancamento->tipo_movimento == 'CREDITO') ? 'checked' : '' ?> />
                                <span style="color: #27ae60;"><i class="fas fa-plus-circle"></i> Crédito (Entrada)</span>
                            </label>
                            <label class="radio inline">
                                <input type="radio" name="tipo_movimento" value="DEBITO" <?= ($is_edit && $lancamento->tipo_movimento == 'DEBITO') ? 'checked' : '' ?> />
                                <span style="color: #e74c3c;"><i class="fas fa-minus-circle"></i> Débito (Saída)</span>
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Descrição:</label>
                        <div class="controls">
                            <textarea name="descricao" class="span8" rows="3"><?= $is_edit ? $lancamento->descricao : '' ?></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Documento:</label>
                        <div class="controls">
                            <input type="text" name="documento" class="span4" value="<?= $is_edit ? $lancamento->documento : '' ?>" placeholder="Nº NF, OS, etc" />
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> <?= $is_edit ? 'Atualizar' : 'Salvar' ?>
                        </button>
                        <a href="<?= site_url('dre/lancamentos') ?>" class="btn">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>