<?php
/**
 * View: Formulário de Edição de Obra
 */
?>

<div class="widget-title" style="margin: -20px 0 20px">
    <span class="icon"><i class="bx bx-building"></i></span>
    <h5>Editar Obra</h5>
    <div class="buttons">
        <a href="<?= site_url('tecnicos_admin/obras') ?>" class="button btn btn-mini btn-default">
            <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
            <span class="button__text2">Voltar</span>
        </a>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-edit"></i></span>
                <h5>Informações da Obra</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= site_url('tecnicos_admin/editar_obra/' . $obra->id) ?>" class="form-horizontal">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Nome da Obra *</label>
                                <div class="controls">
                                    <input type="text" name="nome" class="span12" value="<?= htmlspecialchars($obra->nome ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Código</label>
                                <div class="controls">
                                    <input type="text" name="codigo" class="span12" value="<?= htmlspecialchars($obra->codigo ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Cliente *</label>
                                <div class="controls">
                                    <select name="cliente_id" class="span12" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <?php $selected = ((int)$obra->cliente_id === (int)$cliente->idClientes) ? 'selected="selected"' : ''; ?>
                                            <option value="<?= $cliente->idClientes ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($cliente->nomeCliente, ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($cliente->documento ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($cliente_atual) && $cliente_atual): ?>
                                        <span class="help-block">
                                            <i class="bx bx-user-check" style="color: #4caf50;"></i>
                                            Cliente atual: <strong><?= htmlspecialchars($cliente_atual->nomeCliente, ENT_QUOTES, 'UTF-8') ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Tipo de Obra</label>
                                <div class="controls">
                                    <select name="tipo_obra" class="span12">
                                        <option value="Condominio" <?= ($obra->tipo_obra == 'Condominio') ? 'selected' : '' ?>>Condomínio</option>
                                        <option value="Comercio" <?= ($obra->tipo_obra == 'Comercio') ? 'selected' : '' ?>>Comércio</option>
                                        <option value="Residencia" <?= ($obra->tipo_obra == 'Residencia') ? 'selected' : '' ?>>Residência</option>
                                        <option value="Industrial" <?= ($obra->tipo_obra == 'Industrial') ? 'selected' : '' ?>>Industrial</option>
                                        <option value="Publica" <?= ($obra->tipo_obra == 'Publica') ? 'selected' : '' ?>>Pública</option>
                                        <option value="Outro" <?= ($obra->tipo_obra == 'Outro') ? 'selected' : '' ?>>Outro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span12">
                            <div class="control-group">
                                <label class="control-label">Endereço</label>
                                <div class="controls">
                                    <input type="text" name="endereco" class="span12" value="<?= htmlspecialchars($obra->endereco ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span4">
                            <div class="control-group">
                                <label class="control-label">Data de Início</label>
                                <div class="controls">
                                    <input type="date" name="data_inicio" class="span12" value="<?= $obra->data_inicio ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="span4">
                            <div class="control-group">
                                <label class="control-label">Previsão de Término</label>
                                <div class="controls">
                                    <input type="date" name="data_previsao_fim" class="span12" value="<?= $obra->data_previsao_fim ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="span4">
                            <div class="control-group">
                                <label class="control-label">Status</label>
                                <div class="controls">
                                    <select name="status" class="span12">
                                        <option value="planejamento" <?= ($obra->status == 'planejamento') ? 'selected' : '' ?>>Planejamento</option>
                                        <option value="em_andamento" <?= ($obra->status == 'em_andamento') ? 'selected' : '' ?>>Em Andamento</option>
                                        <option value="paralisada" <?= ($obra->status == 'paralisada') ? 'selected' : '' ?>>Paralisada</option>
                                        <option value="concluida" <?= ($obra->status == 'concluida') ? 'selected' : '' ?>>Concluída</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span12">
                            <div class="control-group">
                                <label class="control-label">Descrição</label>
                                <div class="controls">
                                    <textarea name="descricao" class="span12" rows="4"><?= htmlspecialchars($obra->descricao ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Salvar Alterações
                        </button>
                        <a href="<?= site_url('tecnicos_admin/obras') ?>" class="btn btn-default">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
