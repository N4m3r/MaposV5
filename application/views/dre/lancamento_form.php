<?php
/**
 * Formulário de Lançamento DRE
 */
$is_edit = isset($lancamento) && $lancamento;
$contas = $this->dre_model->getContas(1);
?\u003e

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="\u003c?= site_url('dashboard') ?\u003e">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="\u003c?= site_url('dre') ?\u003e">DRE Contábil</a> <span class="divider">/</span></li>
            <li><a href="\u003c?= site_url('dre/lancamentos') ?\u003e">Lançamentos</a> <span class="divider">/</span></li>
            <li class="active">\u003c?= $is_edit ? 'Editar' : 'Novo' ?\u003e Lançamento</li>
        </ul>
    </div>
</div>

<!-- Formulário -->
<div class="row-fluid">
    <div class="span8 offset2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-\u003c?= $is_edit ? 'edit' : 'plus' ?\u003e"></i></span>
                <h5>\u003c?= $is_edit ? 'Editar' : 'Novo' ?\u003e Lançamento</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="\u003c?= site_url('dre/lancamento_salvar') ?\u003e" class="form-horizontal">
                    <input type="hidden" name="id" value="\u003c?= $is_edit ? $lancamento->id : '' ?\u003e" />

                    <div class="control-group">
                        <label class="control-label">Conta: *</label>
                        <div class="controls">
                            <select name="conta_id" class="span8" required>
                                <option value="">Selecione a conta...</option>
                                \u003c?php foreach ($contas as $c): ?\u003e
                                <option value="\u003c?= $c->id ?\u003e" \u003c?= ($is_edit && $lancamento->conta_id == $c->id) ? 'selected' : '' ?\u003e>
                                    \u003c?= $c->codigo ?\u003e - \u003c?= $c->nome ?\u003e
                                </option>
                                \u003c?php endforeach; ?\u003e
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Data: *</label>
                        <div class="controls">
                            <input type="date" name="data" class="span3" value="\u003c?= $is_edit ? $lancamento->data : date('Y-m-d') ?\u003e" required />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Valor: *</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">R$</span>
                                <input type="text" name="valor" class="span3" value="\u003c?= $is_edit ? number_format($lancamento->valor, 2, ',', '.') : '' ?\u003e" required placeholder="0,00" />
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Tipo: *</label>
                        <div class="controls">
                            <label class="radio inline">
                                <input type="radio" name="tipo_movimento" value="CREDITO" \u003c?= (!$is_edit || $lancamento->tipo_movimento == 'CREDITO') ? 'checked' : '' ?\u003e />
                                <span style="color: #27ae60;"><i class="fas fa-plus-circle"></i> Crédito (Entrada)</span>
                            </label>
                            <label class="radio inline">
                                <input type="radio" name="tipo_movimento" value="DEBITO" \u003c?= ($is_edit && $lancamento->tipo_movimento == 'DEBITO') ? 'checked' : '' ?\u003e />
                                <span style="color: #e74c3c;"><i class="fas fa-minus-circle"></i> Débito (Saída)</span>
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Descrição:</label>
                        <div class="controls">
                            <textarea name="descricao" class="span8" rows="3">\u003c?= $is_edit ? $lancamento->descricao : '' ?\u003e</textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Documento:</label>
                        <div class="controls">
                            <input type="text" name="documento" class="span4" value="\u003c?= $is_edit ? $lancamento->documento : '' ?\u003e" placeholder="Nº NF, OS, etc" />
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> \u003c?= $is_edit ? 'Atualizar' : 'Salvar' ?\u003e
                        </button>
                        <a href="\u003c?= site_url('dre/lancamentos') ?\u003e" class="btn">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
