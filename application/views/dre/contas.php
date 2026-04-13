<?php
/**
 * Plano de Contas DRE
 */
?>

<style>
.conta-row {
    display: flex;
    align-items: center;
    padding: 8px 15px;
    border-bottom: 1px solid #eee;
}
.conta-row:hover {
    background: #f9f9f9;
}
.conta-row.nivel-1 { font-weight: bold; background: #f0f0f0; }
.conta-row.nivel-2 { padding-left: 30px; }
.conta-row.nivel-3 { padding-left: 50px; font-size: 13px; }

.conta-codigo {
    width: 100px;
    font-family: monospace;
    color: #666;
}
.conta-nome {
    flex: 1;
}
.conta-tipo {
    width: 120px;
}
.conta-grupo {
    width: 180px;
}
.conta-acoes {
    width: 100px;
    text-align: right;
}

.badge-tipo {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
}
.badge-tipo.RECEITA { background: #27ae60; color: white; }
.badge-tipo.CUSTO { background: #e74c3c; color: white; }
.badge-tipo.DESPESA { background: #e67e22; color: white; }
.badge-tipo.IMPOSTO { background: #9b59b6; color: white; }
.badge-tipo.TRANSFERENCIA { background: #95a5a6; color: white; }
</style>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('dre') ?>">DRE Contábil</a> <span class="divider">/</span></li>
            <li class="active">Plano de Contas</li>
        </ul>
    </div>
</div>

<!-- Botões -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-list-alt"></i></span>
                <h5>Plano de Contas DRE</h5>
                <div class="buttons">
                    <a href="<?= site_url('dre/conta_form') ?>" class="btn btn-success btn-small">
                        <i class="fas fa-plus"></i> Nova Conta
                    </a>
                    <a href="<?= site_url('dre') ?>" class="btn btn-small">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <p class="text-info">
                    <i class="fas fa-info-circle"></i>
                    Configure as contas do plano de contas para a Demonstração do Resultado do Exercício.
                    As contas são organizadas em grupos que compõem a estrutura da DRE.
                </p>

                <!-- Legenda -->
                <div class="well well-small" style="margin-bottom: 20px;">
                    <strong>Legenda:</strong>
                    <span class="badge-tipo RECEITA" style="margin-left: 10px;">RECEITA</span>
                    <span class="badge-tipo CUSTO">CUSTO</span>
                    <span class="badge-tipo DESPESA">DESPESA</span>
                    <span class="badge-tipo IMPOSTO">IMPOSTO</span>
                    <span class="badge-tipo TRANSFERENCIA">TRANSFERÊNCIA</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Contas -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-content nopadding">
                <!-- Header da Lista -->
                <div class="conta-row nivel-1" style="background: #e0e0e0;">
                    <div class="conta-codigo"><strong>Código</strong></div>
                    <div class="conta-nome"><strong>Nome</strong></div>
                    <div class="conta-tipo"><strong>Tipo</strong></div>
                    <div class="conta-grupo"><strong>Grupo DRE</strong></div>
                    <div class="conta-acoes"><strong>Ações</strong></div>
                </div>

                <!-- Contas -->
                <?php foreach ($contas as $conta): ?>
                <?php $nivel = isset($conta->nivel) ? $conta->nivel : 1; ?>
                <div class="conta-row nivel-<?= $nivel ?>">
                    <div class="conta-codigo"><?= $conta->codigo ?></div>
                    <div class="conta-nome"><?= $conta->nome ?></div>
                    <div class="conta-tipo">
                        <span class="badge-tipo <?= $conta->tipo ?>"><?= $conta->tipo ?></span>
                    </div>
                    <div class="conta-grupo">
                        <?= isset($conta->grupo) ? str_replace('_', ' ', $conta->grupo) : '-' ?>
                    </div>
                    <div class="conta-acoes">
                        <a href="<?= site_url('dre/conta_form/' . $conta->id) ?>" class="btn btn-mini btn-info" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= site_url('dre/conta_excluir/' . $conta->id) ?>" class="btn btn-mini btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta conta?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Estrutura DRE -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-sitemap"></i></span>
                <h5>Estrutura da DRE</h5>
            </div>
            <div class="widget-content">
                <ol>
                    <li><strong>RECEITA BRUTA</strong></li>
                    <li><strong>(-) DEDUÇÕES</strong></li>
                    <li><strong>= RECEITA LÍQUIDA</strong></li>
                    <li><strong>(-) CUSTOS</strong></li>
                    <li><strong>= LUCRO BRUTO</strong></li>
                    <li><strong>(-) DESPESAS OPERACIONAIS</strong></li>
                    <li><strong>= LUCRO OPERACIONAL</strong></li>
                    <li><strong>(+/-) OUTRAS RECEITAS/DESPESAS</strong></li>
                    <li><strong>= LUCRO ANTES DO IR</strong></li>
                    <li><strong>(-) IMPOSTO DE RENDA</strong></li>
                    <li><strong>= LUCRO LÍQUIDO</strong></li>
                </ol>
            </div>
        </div>
    </div>
</div>
