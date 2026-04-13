<?php
/**
 * Lançamentos DRE
 */
?>

<style>
.lancamento-row:hover {
    background: #f9f9f9;
}
.lancamento-valor.credito { color: #27ae60; }
.lancamento-valor.debito { color: #e74c3c; }
</style>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('dre') ?>">DRE Contábil</a> <span class="divider">/</span></li>
            <li class="active">Lançamentos</li>
        </ul>
    </div>
</div>

<!-- Filtros -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-filter"></i></span>
                <h5>Filtros</h5>
                <div class="buttons">
                    <a href="<?= site_url('dre/lancamento_form') ?>" class="btn btn-success btn-small">
                        <i class="fas fa-plus"></i> Novo Lançamento
                    </a>
                    <a href="<?= site_url('dre') ?>" class="btn btn-small">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <form method="get" action="<?= site_url('dre/lancamentos') ?>" class="form-inline">
                    <div class="row-fluid">
                        <div class="span2">
                            <label>Data Início:</label>
                            <input type="date" name="data_inicio" class="span12" value="<?= $data_inicio ?>" />
                        </div>
                        <div class="span2">
                            <label>Data Fim:</label>
                            <input type="date" name="data_fim" class="span12" value="<?= $data_fim ?>" />
                        </div>
                        <div class="span3">
                            <label>Conta:</label>
                            <select name="conta_id" class="span12">
                                <option value="">Todas as contas</option>
                                <?php foreach ($contas as $c): ?>
                                <option value="<?= $c->id ?>"><?= $c->codigo ?> - <?= $c->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="span2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary span12">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Lançamentos -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-book"></i></span>
                <h5>Lançamentos do Período</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (empty($lancamentos)): ?>
                <div class="alert alert-info" style="margin: 20px;">
                    <i class="fas fa-info-circle"></i> Nenhum lançamento encontrado para o período selecionado.
                    <br><br>
                    <a href="<?= site_url('dre/lancamento_form') ?>" class="btn btn-small">
                        <i class="fas fa-plus"></i> Adicionar Lançamento
                    </a>
                    ou
                    <form method="post" action="<?= site_url('dre/integrar') ?>" style="display: inline;">
                        <input type="hidden" name="data_inicio" value="<?= $data_inicio ?>" />
                        <input type="hidden" name="data_fim" value="<?= $data_fim ?>" />
                        <button type="submit" class="btn btn-small btn-inverse">
                            <i class="fas fa-sync"></i> Importar Dados Automáticos
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Documento</th>
                            <th>Conta</th>
                            <th>Descrição</th>
                            <th class="text-right">Valor</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lancamentos as $l): ?>
                        <tr class="lancamento-row">
                            <td><?= date('d/m/Y', strtotime($l->data)) ?></td>
                            <td><?= $l->documento ?: '-' ?></td>
                            <td>
                                <span class="label"><?= $l->conta_codigo ?></span>
                                <?= $l->conta_nome ?>
                            </td>
                            <td><?= $l->descricao ?: '-' ?></td>
                            <td class="text-right lancamento-valor <?= strtolower($l->tipo_movimento) ?>">
                                <?= $l->tipo_movimento == 'CREDITO' ? '+' : '-' ?>
                                R$ <?= number_format($l->valor, 2, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <span class="label label-<?= $l->tipo_movimento == 'CREDITO' ? 'success' : 'important' ?>">
                                    <?= $l->tipo_movimento == 'CREDITO' ? 'Crédito' : 'Débito' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('dre/lancamento_form/' . $l->id) ?>" class="btn btn-mini btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('dre/lancamento_excluir/' . $l->id) ?>" class="btn btn-mini btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este lançamento?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background: #f0f0f0; font-weight: bold;">
                            <td colspan="4" class="text-right">TOTAL:</td>
                            <td class="text-right">
                                <?
                                $total = 0;
                                foreach ($lancamentos as $l) {
                                    $total += ($l->tipo_movimento == 'CREDITO' ? $l->valor : -$l->valor);
                                }
                                echo 'R$ ' . number_format($total, 2, ',', '.');
                                ?>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
