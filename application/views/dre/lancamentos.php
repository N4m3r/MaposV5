<?php
/**
 * Lançamentos DRE
 */
?\u003e

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
            <li><a href="\u003c?= site_url('dashboard') ?\u003e">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="\u003c?= site_url('dre') ?\u003e">DRE Contábil</a> <span class="divider">/</span></li>
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
                    <a href="\u003c?= site_url('dre/lancamento_form') ?\u003e" class="btn btn-success btn-small">
                        <i class="fas fa-plus"></i> Novo Lançamento
                    </a>
                    <a href="\u003c?= site_url('dre') ?\u003e" class="btn btn-small">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <form method="get" action="\u003c?= site_url('dre/lancamentos') ?\u003e" class="form-inline">
                    <div class="row-fluid">
                        <div class="span2">
                            <label>Data Início:</label>
                            <input type="date" name="data_inicio" class="span12" value="\u003c?= $data_inicio ?\u003e" />
                        </div>
                        <div class="span2">
                            <label>Data Fim:</label>
                            <input type="date" name="data_fim" class="span12" value="\u003c?= $data_fim ?\u003e" />
                        </div>
                        <div class="span3">
                            <label>Conta:</label>
                            <select name="conta_id" class="span12">
                                <option value="">Todas as contas</option>
                                \u003c?php foreach ($contas as $c): ?\u003e
                                <option value="\u003c?= $c->id ?\u003e">\u003c?= $c->codigo ?\u003e - \u003c?= $c->nome ?\u003e</option>
                                \u003c?php endforeach; ?\u003e
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
                    <a href="\u003c?= site_url('dre/lancamento_form') ?\u003e" class="btn btn-small">
                        <i class="fas fa-plus"></i> Adicionar Lançamento
                    </a>
                    ou
                    <form method="post" action="\u003c?= site_url('dre/integrar') ?\u003e" style="display: inline;">
                        <input type="hidden" name="data_inicio" value="\u003c?= $data_inicio ?\u003e" />
                        <input type="hidden" name="data_fim" value="\u003c?= $data_fim ?\u003e" />
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
                        \u003c?php foreach ($lancamentos as $l): ?\u003e
                        <tr class="lancamento-row">
                            <td>\u003c?= date('d/m/Y', strtotime($l->data)) ?\u003e</td>
                            <td>\u003c?= $l->documento ?: '-' ?\u003e</td>
                            <td>
                                <span class="label">\u003c?= $l->conta_codigo ?\u003e</span>
                                \u003c?= $l->conta_nome ?\u003e
                            </td>
                            <td>\u003c?= $l->descricao ?: '-' ?\u003e</td>
                            <td class="text-right lancamento-valor \u003c?= strtolower($l->tipo_movimento) ?\u003e">
                                \u003c?= $l->tipo_movimento == 'CREDITO' ? '+' : '-' ?\u003e
                                R$ \u003c?= number_format($l->valor, 2, ',', '.') ?\u003e
                            </td>
                            <td class="text-center">
                                <span class="label label-\u003c?= $l->tipo_movimento == 'CREDITO' ? 'success' : 'important' ?\u003e">
                                    \u003c?= $l->tipo_movimento == 'CREDITO' ? 'Crédito' : 'Débito' ?\u003e
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="\u003c?= site_url('dre/lancamento_form/' . $l->id) ?\u003e" class="btn btn-mini btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="\u003c?= site_url('dre/lancamento_excluir/' . $l->id) ?\u003e" class="btn btn-mini btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este lançamento?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        \u003c?php endforeach; ?\u003e
                    </tbody>
                    <tfoot>
                        <tr style="background: #f0f0f0; font-weight: bold;">
                            <td colspan="4" class="text-right">TOTAL:</td>
                            <td class="text-right">
                                \u003c?
                                $total = 0;
                                foreach ($lancamentos as $l) {
                                    $total += ($l->tipo_movimento == 'CREDITO' ? $l->valor : -$l->valor);
                                }
                                echo 'R$ ' . number_format($total, 2, ',', '.');
                                ?\u003e
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
