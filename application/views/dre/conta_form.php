<?php
/**
 * Formulário de Conta DRE
 */
$is_edit = isset($conta) && $conta;
?>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('dre') ?>">DRE Contábil</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('dre/contas') ?>">Plano de Contas</a> <span class="divider">/</span></li>
            <li class="active"><?= $is_edit ? 'Editar' : 'Nova' ?> Conta</li>
        </ul>
    </div>
</div>

<!-- Formulário -->
<div class="row-fluid">
    <div class="span8 offset2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-<?= $is_edit ? 'edit' : 'plus' ?>"></i></span>
                <h5><?= $is_edit ? 'Editar' : 'Nova' ?> Conta DRE</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= site_url('dre/conta_salvar') ?>" class="form-horizontal">
                    <input type="hidden" name="id" value="<?= $is_edit ? $conta->id : '' ?>" />

                    <div class="control-group">
                        <label class="control-label">Código: *</label>
                        <div class="controls">
                            <input type="text" name="codigo" class="span4" value="<?= $is_edit ? $conta->codigo : '' ?>" required placeholder="Ex: 1.1" />
                            <span class="help-inline">Código hierárquico (ex: 1, 1.1, 1.1.1)</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Nome: *</label>
                        <div class="controls">
                            <input type="text" name="nome" class="span8" value="<?= $is_edit ? $conta->nome : '' ?>" required placeholder="Nome da conta" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Tipo: *</label>
                        <div class="controls">
                            <select name="tipo" class="span4" required>
                                <option value="">Selecione...</option>
                                <option value="RECEITA" <?= $is_edit && $conta->tipo == 'RECEITA' ? 'selected' : '' ?>>RECEITA</option>
                                <option value="CUSTO" <?= $is_edit && $conta->tipo == 'CUSTO' ? 'selected' : '' ?>>CUSTO</option>
                                <option value="DESPESA" <?= $is_edit && $conta->tipo == 'DESPESA' ? 'selected' : '' ?>>DESPESA</option>
                                <option value="IMPOSTO" <?= $is_edit && $conta->tipo == 'IMPOSTO' ? 'selected' : '' ?>>IMPOSTO</option>
                                <option value="TRANSFERENCIA" <?= $is_edit && $conta->tipo == 'TRANSFERENCIA' ? 'selected' : '' ?>>TRANSFERÊNCIA</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Grupo DRE: *</label>
                        <div class="controls">
                            <select name="grupo" class="span6" required>
                                <optgroup label="Estrutura DRE">
                                    <option value="RECEITA_BRUTA" <?= $is_edit && $conta->grupo == 'RECEITA_BRUTA' ? 'selected' : '' ?>>1. RECEITA BRUTA</option>
                                    <option value="DEDUCOES" <?= $is_edit && $conta->grupo == 'DEDUCOES' ? 'selected' : '' ?>>2. DEDUÇÕES DA RECEITA</option>
                                    <option value="CUSTO" <?= $is_edit && $conta->grupo == 'CUSTO' ? 'selected' : '' ?>>4. CUSTO DOS SERVIÇOS/PRODUTOS</option>
                                    <option value="DESPESA_OPERACIONAL" <?= $is_edit && $conta->grupo == 'DESPESA_OPERACIONAL' ? 'selected' : '' ?>>6. DESPESAS OPERACIONAIS</option>
                                    <option value="OUTRAS_RECEITAS" <?= $is_edit && $conta->grupo == 'OUTRAS_RECEITAS' ? 'selected' : '' ?>>8. OUTRAS RECEITAS</option>
                                    <option value="OUTRAS_DESPESAS" <?= $is_edit && $conta->grupo == 'OUTRAS_DESPESAS' ? 'selected' : '' ?>>8. OUTRAS DESPESAS</option>
                                    <option value="IMPOSTO_RENDA" <?= $is_edit && $conta->grupo == 'IMPOSTO_RENDA' ? 'selected' : '' ?>>10. IMPOSTO DE RENDA</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Ordem:</label>
                        <div class="controls">
                            <input type="number" name="ordem" class="span2" value="<?= $is_edit ? $conta->ordem : '' ?>" placeholder="0" />
                            <span class="help-inline">Ordem de exibição na DRE (menor = primeiro)</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Nível:</label>
                        <div class="controls">
                            <select name="nivel" class="span2">
                                <option value="1" <?= $is_edit && $conta->nivel == 1 ? 'selected' : '' ?>>1 - Principal</option>
                                <option value="2" <?= $is_edit && $conta->nivel == 2 ? 'selected' : '' ?>>2 - Subconta</option>
                                <option value="3" <?= $is_edit && $conta->nivel == 3 ? 'selected' : '' ?>>3 - Detalhe</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Sinal:</label>
                        <div class="controls">
                            <label class="radio inline">
                                <input type="radio" name="sinal" value="POSITIVO" <?= (!$is_edit || $conta->sinal == 'POSITIVO') ? 'checked' : '' ?> />
                                Positivo (Receita)
                            </label>
                            <label class="radio inline">
                                <input type="radio" name="sinal" value="NEGATIVO" <?= ($is_edit && $conta->sinal == 'NEGATIVO') ? 'checked' : '' ?> />
                                Negativo (Despesa)
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Ativo:</label>
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="ativo" value="1" <?= !$is_edit || $conta->ativo ? 'checked' : '' ?> />
                                Conta ativa
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> <?= $is_edit ? 'Atualizar' : 'Salvar' ?>
                        </button>
                        <a href="<?= site_url('dre/contas') ?>" class="btn">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
