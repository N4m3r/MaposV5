<?php
/**
 * Formulário de Lançamento DRE
 */
$is_edit = isset($lancamento) && $lancamento;
$contas = $this->dre_model->getContas(1);

// Agrupar contas por grupo para seleção mais intuitiva
$contasPorGrupo = [];
$grupoLabels = [
    'RECEITA_BRUTA' => 'Receita Bruta',
    'DEDUCOES' => 'Deduções da Receita',
    'CUSTO' => 'Custo dos Serviços/Produtos',
    'DESPESA_OPERACIONAL' => 'Despesas Operacionais',
    'OUTRAS_RECEITAS' => 'Outras Receitas',
    'OUTRAS_DESPESAS' => 'Outras Despesas',
    'IMPOSTO_RENDA' => 'Imposto de Renda e Contribuições',
];
foreach ($contas as $c) {
    $grupo = $c->grupo ?? 'OUTRAS_RECEITAS';
    $contasPorGrupo[$grupo][] = $c;
}
?>

<style>
.os-search-results {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-top: none;
    background: #fff;
    position: absolute;
    z-index: 1000;
    width: 96%;
    display: none;
}
.os-search-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}
.os-search-item:hover {
    background: #f0f7ff;
}
.os-search-item small {
    color: #888;
}
.conta-group-label {
    font-weight: bold;
    padding: 5px 8px;
    background: #f5f5f5;
    color: #555;
}
.tipo-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 11px;
    margin-left: 5px;
}
.tipo-badge.credito { background: #d4edda; color: #155724; }
.tipo-badge.debito { background: #f8d7da; color: #721c24; }
</style>

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
        <!-- Vincular OS -->
        <?php if (!$is_edit): ?>
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-link"></i></span>
                <h5>Vincular a uma Ordem de Serviço (opcional)</h5>
            </div>
            <div class="widget-content">
                <div class="control-group">
                    <label class="control-label">Buscar OS:</label>
                    <div class="controls" style="position: relative;">
                        <input type="text" id="os-search-input" class="span8" placeholder="Digite o número ou cliente da OS..." autocomplete="off" />
                        <div id="os-search-results" class="os-search-results"></div>
                        <input type="hidden" id="os-selected-id" name="os_id" value="" />
                    </div>
                </div>
                <div id="os-selected-info" style="display: none;" class="alert alert-info">
                    <i class="fas fa-check-circle"></i> <strong id="os-selected-label"></strong>
                    <span id="os-selected-details" style="margin-left: 10px;"></span>
                    <button type="button" id="os-clear-btn" class="btn btn-mini btn-danger pull-right"><i class="fas fa-times"></i> Remover</button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-<?= $is_edit ? 'edit' : 'plus' ?>"></i></i></span>
                <h5><?= $is_edit ? 'Editar' : 'Novo' ?> Lançamento</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= site_url('dre/lancamento_salvar') ?>" class="form-horizontal" id="lancamento-form">
                    <input type="hidden" name="id" value="<?= $is_edit ? $lancamento->id : '' ?>" />

                    <div class="control-group">
                        <label class="control-label">Conta: *</label>
                        <div class="controls">
                            <select name="conta_id" class="span8" required id="conta-select">
                                <option value="">Selecione a conta...</option>
                                <?php foreach ($contasPorGrupo as $grupoKey => $contasGrupo): ?>
                                <optgroup label="<?= $grupoLabels[$grupoKey] ?? str_replace('_', ' ', $grupoKey) ?>">
                                    <?php foreach ($contasGrupo as $c): ?>
                                    <option value="<?= $c->id ?>" data-grupo="<?= $c->grupo ?? '' ?>" <?= ($is_edit && $lancamento->conta_id == $c->id) ? 'selected' : '' ?>>
                                        <?= $c->codigo ?> - <?= $c->nome ?>
                                    </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Tipo: *</label>
                        <div class="controls">
                            <label class="radio inline">
                                <input type="radio" name="tipo_movimento" value="CREDITO" id="tipo-credito" <?= (!$is_edit || $lancamento->tipo_movimento == 'CREDITO') ? 'checked' : '' ?> />
                                <span class="tipo-badge credito"><i class="fas fa-plus-circle"></i> Crédito (Entrada)</span>
                            </label>
                            <label class="radio inline">
                                <input type="radio" name="tipo_movimento" value="DEBITO" id="tipo-debito" <?= ($is_edit && $lancamento->tipo_movimento == 'DEBITO') ? 'checked' : '' ?> />
                                <span class="tipo-badge debito"><i class="fas fa-minus-circle"></i> Débito (Saída)</span>
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Data: *</label>
                        <div class="controls">
                            <input type="date" name="data" class="span3" id="lanc-data" value="<?= $is_edit ? $lancamento->data : date('Y-m-d') ?>" required />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Valor: *</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">R$</span>
                                <input type="text" name="valor" class="span3" id="lanc-valor" value="<?= $is_edit ? number_format($lancamento->valor, 2, ',', '.') : '' ?>" required placeholder="0,00" />
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Descrição:</label>
                        <div class="controls">
                            <textarea name="descricao" class="span8" rows="3" id="lanc-descricao"><?= $is_edit ? $lancamento->descricao : '' ?></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Documento:</label>
                        <div class="controls">
                            <input type="text" name="documento" class="span4" id="lanc-documento" value="<?= $is_edit ? $lancamento->documento : '' ?>" placeholder="Nº NF, OS, etc" />
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

<script>
// OS Search
var searchTimer = null;
$('#os-search-input').on('input', function() {
    var term = $(this).val().trim();
    clearTimeout(searchTimer);
    if (term.length < 2) {
        $('#os-search-results').hide();
        return;
    }
    searchTimer = setTimeout(function() {
        $.get('<?= site_url("os/gerenciar") ?>', { pesquisa: term }, function() {}).fail(function() {
            // Fallback: buscar via AJAX direto
            $.ajax({
                url: '<?= site_url("dre/api_buscar_os") ?>',
                data: { term: term },
                dataType: 'json',
                success: function(data) {
                    if (!data.success || !data.results || data.results.length === 0) {
                        $('#os-search-results').html('<div style="padding:10px;color:#888;">Nenhuma OS encontrada</div>').show();
                        return;
                    }
                    var html = '';
                    for (var i = 0; i < data.results.length; i++) {
                        var os = data.results[i];
                        html += '<div class="os-search-item" data-id="' + os.id + '" data-valor="' + os.valor + '" data-data="' + os.data + '" data-status="' + os.status + '">';
                        html += '<strong>OS #' + os.id + '</strong> - ' + os.cliente;
                        html += ' <small>(' + os.status + ' | R$ ' + parseFloat(os.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2}) + ')</small>';
                        html += '</div>';
                    }
                    $('#os-search-results').html(html).show();
                }
            });
        });
    }, 300);
});

$(document).on('click', '.os-search-item', function() {
    var id = $(this).data('id');
    var valor = $(this).data('valor');
    var data = $(this).data('data');
    var status = $(this).data('status');
    var label = $(this).text().trim();

    $('#os-selected-id').val(id);
    $('#os-selected-label').text(label);
    $('#os-selected-details').text('Valor: R$ ' + parseFloat(valor).toLocaleString('pt-BR', {minimumFractionDigits: 2}) + ' | Data: ' + data);
    $('#os-selected-info').show();
    $('#os-search-input').val('');
    $('#os-search-results').hide();

    // Preencher campos automaticamente
    if ($('#lanc-valor').val() === '') {
        $('#lanc-valor').val(parseFloat(valor).toLocaleString('pt-BR', {minimumFractionDigits: 2}));
    }
    if ($('#lanc-data').val() === '' || $('#lanc-data').val() === new Date().toISOString().split('T')[0]) {
        if (data) $('#lanc-data').val(data);
    }
    if ($('#lanc-documento').val() === '') {
        $('#lanc-documento').val('OS #' + String(id).padStart(4, '0'));
    }
    if ($('#lanc-descricao').val() === '') {
        $('#lanc-descricao').val('Receita de OS #' + String(id).padStart(4, '0'));
    }
    // Selecionar conta de receita por padrão
    if ($('#conta-select').val() === '') {
        $('#conta-select').find('option[value!=""]').first().prop('selected', true);
    }
    // Selecionar Crédito
    $('#tipo-credito').prop('checked', true);
});

$('#os-clear-btn').on('click', function() {
    $('#os-selected-id').val('');
    $('#os-selected-info').hide();
});

$(document).on('click', function(e) {
    if (!$(e.target).closest('#os-search-input, #os-search-results').length) {
        $('#os-search-results').hide();
    }
});
</script>