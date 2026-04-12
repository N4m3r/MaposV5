<?php
/**
 * Formulário de Conta DRE
 */
$is_edit = isset($conta) && $conta;
?\u003e

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="\u003c?= site_url('dashboard') ?\u003e">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="\u003c?= site_url('dre') ?\u003e">DRE Contábil</a> <span class="divider">/</span></li>
            <li><a href="\u003c?= site_url('dre/contas') ?\u003e">Plano de Contas</a> <span class="divider">/</span></li>
            <li class="active">\u003c?= $is_edit ? 'Editar' : 'Nova' ?\u003e Conta</li>
        </ul>
    </div>
</div>

<!-- Formulário -->
<div class="row-fluid">
    <div class="span8 offset2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-\u003c?= $is_edit ? 'edit' : 'plus' ?\u003e"></i></span>
                <h5>\u003c?= $is_edit ? 'Editar' : 'Nova' ?\u003e Conta DRE</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="\u003c?= site_url('dre/conta_salvar') ?\u003e" class="form-horizontal">
                    <input type="hidden" name="id" value="\u003c?= $is_edit ? $conta->id : '' ?\u003e" />

                    <div class="control-group">
                        <label class="control-label">Código: *</label>
                        <div class="controls">
                            <input type="text" name="codigo" class="span4" value="\u003c?= $is_edit ? $conta->codigo : '' ?\u003e" required placeholder="Ex: 1.1" />
                            <span class="help-inline">Código hierárquico (ex: 1, 1.1, 1.1.1)</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Nome: *</label>
                        <div class="controls">
                            <input type="text" name="nome" class="span8" value="\u003c?= $is_edit ? $conta->nome : '' ?\u003e" required placeholder="Nome da conta" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Tipo: *</label>
                        <div class="controls">
                            <select name="tipo" class="span4" required>
                                <option value="">Selecione...</option>
                                <option value="RECEITA" \u003c?= $is_edit && $conta->tipo == 'RECEITA' ? 'selected' : '' ?\u003e>RECEITA</option>
                                <option value="CUSTO" \u003c?= $is_edit && $conta->tipo == 'CUSTO' ? 'selected' : '' ?\u003e>CUSTO</option>
                                <option value="DESPESA" \u003c?= $is_edit && $conta->tipo == 'DESPESA' ? 'selected' : '' ?\u003e>DESPESA</option>
                                <option value="IMPOSTO" \u003c?= $is_edit && $conta->tipo == 'IMPOSTO' ? 'selected' : '' ?\u003e>IMPOSTO</option>
                                <option value="TRANSFERENCIA" \u003c?= $is_edit && $conta->tipo == 'TRANSFERENCIA' ? 'selected' : '' ?\u003e>TRANSFERÊNCIA</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Grupo DRE: *</label>
                        <div class="controls">
                            <select name="grupo" class="span6" required>
                                <optgroup label="Estrutura DRE">
                                    <option value="RECEITA_BRUTA" \u003c?= $is_edit && $conta->grupo == 'RECEITA_BRUTA' ? 'selected' : '' ?\u003e>1. RECEITA BRUTA</option>
                                    <option value="DEDUCOES" \u003c?= $is_edit && $conta->grupo == 'DEDUCOES' ? 'selected' : '' ?\u003e>2. DEDUÇÕES DA RECEITA</option>
                                    <option value="CUSTO" \u003c?= $is_edit && $conta->grupo == 'CUSTO' ? 'selected' : '' ?\u003e>4. CUSTO DOS SERVIÇOS/PRODUTOS</option>
                                    <option value="DESPESA_OPERACIONAL" \u003c?= $is_edit && $conta->grupo == 'DESPESA_OPERACIONAL' ? 'selected' : '' ?\u003e>6. DESPESAS OPERACIONAIS</option>
                                    <option value="OUTRAS_RECEITAS" \u003c?= $is_edit && $conta->grupo == 'OUTRAS_RECEITAS' ? 'selected' : '' ?\u003e>8. OUTRAS RECEITAS</option>
                                    <option value="OUTRAS_DESPESAS" \u003c?= $is_edit && $conta->grupo == 'OUTRAS_DESPESAS' ? 'selected' : '' ?\u003e>8. OUTRAS DESPESAS</option>
                                    <option value="IMPOSTO_RENDA" \u003c?= $is_edit && $conta->grupo == 'IMPOSTO_RENDA' ? 'selected' : '' ?\u003e>10. IMPOSTO DE RENDA</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Ordem:</label>
                        <div class="controls">
                            <input type="number" name="ordem" class="span2" value="\u003c?= $is_edit ? $conta->ordem : '' ?\u003e" placeholder="0" />
                            <span class="help-inline">Ordem de exibição na DRE (menor = primeiro)</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Nível:</label>
                        <div class="controls">
                            <select name="nivel" class="span2">
                                <option value="1" \u003c?= $is_edit && $conta->nivel == 1 ? 'selected' : '' ?\u003e>1 - Principal</option>
                                <option value="2" \u003c?= $is_edit && $conta->nivel == 2 ? 'selected' : '' ?\u003e>2 - Subconta</option>
                                <option value="3" \u003c?= $is_edit && $conta->nivel == 3 ? 'selected' : '' ?\u003e>3 - Detalhe</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Sinal:</label>
                        <div class="controls">
                            <label class="radio inline">
                                <input type="radio" name="sinal" value="POSITIVO" \u003c?= (!$is_edit || $conta->sinal == 'POSITIVO') ? 'checked' : '' ?\u003e />
                                Positivo (Receita)
                            </label>
                            <label class="radio inline">
                                <input type="radio" name="sinal" value="NEGATIVO" \u003c?= ($is_edit && $conta->sinal == 'NEGATIVO') ? 'checked' : '' ?\u003e />
                                Negativo (Despesa)
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Ativo:</label>
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="ativo" value="1" \u003c?= !$is_edit || $conta->ativo ? 'checked' : '' ?\u003e />
                                Conta ativa
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> \u003c?= $is_edit ? 'Atualizar' : 'Salvar' ?\u003e
                        </button>
                        <a href="\u003c?= site_url('dre/contas') ?\u003e" class="btn">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
