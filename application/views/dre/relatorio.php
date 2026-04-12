<?php
/**
 * Relatório DRE - Impressão
 */
$dre = $results['dre'];
$indicadores = $results['indicadores'];
$data_inicio = $results['data_inicio'];
$data_fim = $results['data_fim'];
$comparativo = $results['comparativo'];
?>

<style>
@media print {
    .no-print { display: none !important; }
    .widget-box { border: none; box-shadow: none; }
    .widget-title { background: #f5f5f5; }
}

.dre-relatorio {
    font-family: 'Arial', sans-serif;
    max-width: 900px;
    margin: 0 auto;
}

.dre-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    border-bottom: 3px double #333;
}

.dre-header h1 {
    font-size: 24px;
    margin: 0 0 10px 0;
    text-transform: uppercase;
}

.dre-header .periodo {
    font-size: 14px;
    color: #666;
}

.dre-tabela {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.dre-tabela th,
.dre-tabela td {
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.dre-tabela .linha-grupo {
    font-weight: bold;
    background: #f8f9fa;
}

.dre-tabela .linha-destaque {
    font-weight: bold;
    background: #e8f4f8;
    border-top: 2px solid #3498db;
    border-bottom: 2px solid #3498db;
}

.dre-tabela .linha-sub {
    padding-left: 30px;
    font-size: 12px;
}

.dre-tabela .valor {
    text-align: right;
    font-family: monospace;
    white-space: nowrap;
}

.dre-tabela .percentual {
    text-align: right;
    color: #666;
    font-size: 12px;
}

.dre-assinatura {
    margin-top: 60px;
    display: flex;
    justify-content: space-between;
}

.dre-assinatura .linha {
    width: 200px;
    border-top: 1px solid #333;
    margin-top: 40px;
    text-align: center;
    padding-top: 5px;
    font-size: 12px;
}

.indicadores-resumo {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.indicadores-resumo .item {
    display: inline-block;
    margin-right: 40px;
}

.indicadores-resumo .label {
    font-size: 11px;
    color: #666;
    display: block;
}

.indicadores-resumo .value {
    font-size: 18px;
    font-weight: bold;
}
</style>

<!-- Botões de Ação -->
<div class="row-fluid no-print">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-content">
                <div class="btn-group">
                    <a href="javascript:window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Imprimir
                    </a>
                    <a href="<?= site_url('dre/exportar?data_inicio=' . $data_inicio . '&data_fim=' . $data_fim) ?>" class="btn btn-success">
                        <i class="fas fa-download"></i> Exportar CSV
                    </a>
                    <a href="<?= site_url('dre?data_inicio=' . $data_inicio . '&data_fim=' . $data_fim) ?>" class="btn">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Relatório -->
<div class="dre-relatorio">
    <div class="dre-header">
        <h1>DEMONSTRAÇÃO DO RESULTADO DO EXERCÍCIO</h1>
        <div class="periodo">
            Período: <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?>
        </div>
        <div style="margin-top: 15px; font-size: 12px;">
            Data de Emissão: <?= date('d/m/Y H:i') ?><br>
            Emitido por: <?= $this->session->userdata('nome') ?>
        </div>
    </div>

    <!-- Indicadores Resumo -->
    <div class="indicadores-resumo">
        <div class="item">
            <span class="label">Receita Bruta</span>
            <span class="value">R$ <?= number_format($indicadores['receita_bruta'], 2, ',', '.') ?></span>
        </div>
        <div class="item">
            <span class="label">Margem Bruta</span>
            <span class="value"><?= $indicadores['margem_bruta'] ?>%</span>
        </div>
        <div class="item">
            <span class="label">Margem Operacional</span>
            <span class="value"><?= $indicadores['margem_operacional'] ?>%</span>
        </div>
        <div class="item">
            <span class="label">Margem Líquida</span>
            <span class="value"><?= $indicadores['margem_liquida'] ?>%</span>
        </div>
    </div>

    <!-- Tabela DRE -->
    <table class="dre-tabela">
        <thead>
            <tr>
                <th style="width: 60%;">Descrição</th>
                <th class="valor" style="width: 20%;">Valor (R$)</th>
                <th class="percentual" style="width: 20%;">% da Receita</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dre['grupos'] as $grupo): ?>
            <tr class="<?= $grupo['destaque'] ? 'linha-destaque' : 'linha-grupo' ?>">
                <td><?= $grupo['titulo'] ?></td>
                <td class="valor">
                    <?= $grupo['valor'] >= 0 ? '' : '-' ?>
                    R$ <?= number_format(abs($grupo['valor']), 2, ',', '.') ?>
                </td>
                <td class="percentual"><?= number_format($grupo['percentual'], 2) ?>%</td>
            </tr>
                <?php if (!$grupo['destaque'] && !empty($grupo['contas'])): ?>
                    <?php foreach ($grupo['contas'] as $conta): ?>
                        <?php
                        $valor_conta = $this->dre_model->getTotalPorConta($conta->id, $data_inicio, $data_fim);
                        if ($valor_conta != 0):
                        ?>
                        <tr class="linha-sub">
                            <td><?= $conta->nome ?></td>
                            <td class="valor">R$ <?= number_format(abs($valor_conta), 2, ',', '.') ?></td>
                            <td class="percentual">
                                <?= $indicadores['receita_bruta'] > 0 ? number_format(($valor_conta / $indicadores['receita_bruta']) * 100, 2) : 0 ?>%
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Notas e Assinaturas -->
    <div style="margin-top: 40px; font-size: 11px; color: #666;">
        <p><strong>Notas:</strong></p>
        <p>1. Este relatório foi gerado automaticamente pelo sistema.</p>
        <p>2. Os valores estão em Reais (R$).</p>
        <p>3. Para mais detalhes, consulte o plano de contas e lançamentos contábeis.</p>
    </div>

    <div class="dre-assinatura">
        <div class="linha">
            Responsável Contábil<br>
            ___________________
        </div>
        <div class="linha">
            Direção<br>
            ___________________
        </div>
    </div>
</div>
