<?php
/**
 * Dashboard de Impostos
 */
$totais = $results['totais'];
$evolucao = $results['evolucao'];
$data_inicio = $results['data_inicio'];
$data_fim = $results['data_fim'];
?>

<style>
.imposto-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    text-align: center;
}
.imposto-card h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
}
.imposto-card .valor {
    font-size: 28px;
    font-weight: bold;
    color: #2c3e50;
}
.imposto-card .percent {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}
.imposto-card.imposto-negativo .valor {
    color: #e74c3c;
}
</style>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li class="active">Impostos Simples Nacional</li>
        </ul>
    </div>
</div>

<!-- Filtros -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-filter"></i></span>
                <h5>Período</h5>
                <div class="buttons">
                    <a href="<?= site_url('impostos/exportar?data_inicio=' . $data_inicio . '&data_fim=' . $data_fim) ?>" class="btn btn-success btn-small">
                        <i class="fas fa-download"></i> Exportar
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <form method="get" action="<?= site_url('impostos') ?>" class="form-inline">
                    <div class="row-fluid">
                        <div class="span3">
                            <label>Data Início:</label>
                            <input type="date" name="data_inicio" class="span12" value="<?= $data_inicio ?>" />
                        </div>
                        <div class="span3">
                            <label>Data Fim:</label>
                            <input type="date" name="data_fim" class="span12" value="<?= $data_fim ?>" />
                        </div>
                        <div class="span2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary span12">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                        <div class="span4">
                            <label>&nbsp;</label>
                            <div class="btn-group span12">
                                <a href="?data_inicio=<?= date('Y-m-01') ?>&data_fim=<?= date('Y-m-t') ?>" class="btn btn-small">Mês Atual</a>
                                <a href="?data_inicio=<?= date('Y-m-01', strtotime('-1 month')) ?>&data_fim=<?= date('Y-m-t', strtotime('-1 month')) ?>" class="btn btn-small">Mês Anterior</a>
                                <a href="?data_inicio=<?= date('Y-01-01') ?>&data_fim=<?= date('Y-m-d') ?>" class="btn btn-small">Ano</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Totais -->
<div class="row-fluid">
    <div class="span3">
        <div class="imposto-card">
            <h4><i class="fas fa-dollar-sign"></i> Valor Bruto</h4>
            <div class="valor">R$ <?= number_format($totais->total_bruto ?: 0, 2, ',', '.') ?></div>
            <div class="percent"><?= $totais->total_retencoes ?: 0 ?> retenções</div>
        </div>
    </div>

    <div class="span3">
        <div class="imposto-card imposto-negativo">
            <h4><i class="fas fa-minus-circle"></i> Total Impostos</h4>
            <div class="valor">R$ <?= number_format($totais->total_impostos ?: 0, 2, ',', '.') ?></div>
            <div class="percent"><?= $totais->percentual_imposto ?: 0 ?>% do faturamento</div>
        </div>
    </div>

    <div class="span3">
        <div class="imposto-card">
            <h4><i class="fas fa-hand-holding-usd"></i> Valor Líquido</h4>
            <div class="valor">R$ <?= number_format($totais->total_liquido ?: 0, 2, ',', '.') ?></div>
            <div class="percent">Recebido após impostos</div>
        </div>
    </div>

    <div class="span3">
        <div class="imposto-card">
            <h4><i class="fas fa-percentage"></i> Alíquota Média</h4>
            <div class="valor"><?= number_format($totais->percentual_imposto ?: 0, 2) ?>%</div>
            <div class="percent">Efetiva no período</div>
        </div>
    </div>
</div>

<!-- Detalhamento de Impostos -->
<div class="row-fluid">
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-pie"></i></span>
                <h5>Composição dos Impostos</h5>
            </div>
            <div class="widget-content">
                <div id="grafico-composicao" style="height: 250px;"></div>
            </div>
        </div>
    </div>

    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-bar"></i></span>
                <h5>Evolução Mensal</h5>
            </div>
            <div class="widget-content">
                <div id="grafico-evolucao" style="height: 250px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Detalhamento -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-list"></i></span>
                <h5>Detalhamento por Tipo de Imposto</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Imposto</th>
                            <th class="text-right">Valor Total</th>
                            <th class="text-right">% do Faturamento</th>
                            <th class="text-right">% dos Impostos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $impostos = [
                            ['nome' => 'IRPJ', 'valor' => $totais->total_irpj],
                            ['nome' => 'CSLL', 'valor' => $totais->total_csll],
                            ['nome' => 'COFINS', 'valor' => $totais->total_cofins],
                            ['nome' => 'PIS', 'valor' => $totais->total_pis],
                            ['nome' => 'ISS', 'valor' => $totais->total_iss],
                        ];
                        foreach ($impostos as $imposto):
                            $pct_fat = $totais->total_bruto > 0 ? ($imposto['valor'] / $totais->total_bruto * 100) : 0;
                            $pct_imp = $totais->total_impostos > 0 ? ($imposto['valor'] / $totais->total_impostos * 100) : 0;
                        ?>
                        <tr>
                            <td><strong><?= $imposto['nome'] ?></strong></td>
                            <td class="text-right">R$ <?= number_format($imposto['valor'] ?: 0, 2, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($pct_fat, 2) ?>%</td>
                            <td class="text-right"><?= number_format($pct_imp, 2) ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="font-weight: bold; background: #f0f0f0;">
                            <td>TOTAL</td>
                            <td class="text-right">R$ <?= number_format($totais->total_impostos ?: 0, 2, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($totais->percentual_imposto ?: 0, 2) ?>%</td>
                            <td class="text-right">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Informações de Tributação NFS-e -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-file-invoice"></i></span>
                <h5>Configuração de Tributação para NFS-e</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span4">
                        <div class="alert alert-info" style="margin-bottom: 0;">
                            <i class="fas fa-barcode"></i> <strong>Código Tributação Nacional:</strong><br>
                            <span style="font-size: 18px;"><?= $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL') ?: '010701' ?></span>
                        </div>
                    </div>
                    <div class="span4">
                        <div class="alert alert-info" style="margin-bottom: 0;">
                            <i class="fas fa-city"></i> <strong>Código Tributação Municipal:</strong><br>
                            <span style="font-size: 18px;"><?= $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL') ?: '100' ?></span>
                        </div>
                    </div>
                    <div class="span4">
                        <div class="alert alert-success" style="margin-bottom: 0;">
                            <i class="fas fa-percentage"></i> <strong>Alíquota ISS:</strong><br>
                            <span style="font-size: 18px;"><?= $this->impostos_model->getConfig('IMPOSTO_ISS_MUNICIPAL') ?: '5.00' ?>%</span>
                        </div>
                    </div>
                </div>
                <div class="row-fluid" style="margin-top: 10px;">
                    <div class="span12">
                        <div class="well well-small">
                            <i class="fas fa-info-circle"></i> <strong>Descrição do Serviço:</strong><br>
                            <?= $this->impostos_model->getConfig('IMPOSTO_DESCRICAO_SERVICO') ?: 'Suporte técnico em informática, inclusive instalação, configuração e manutenção de programas de computação e bancos de dados.' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-cogs"></i></span>
                <h5>Ações Rápidas</h5>
            </div>
            <div class="widget-content">
                <div class="btn-group">
                    <a href="<?= site_url('impostos/configuracoes') ?>" class="btn btn-info">
                        <i class="fas fa-cogs"></i> Configurações
                    </a>
                    <a href="<?= site_url('impostos/simulador') ?>" class="btn btn-primary">
                        <i class="fas fa-calculator"></i> Simulador
                    </a>
                    <a href="<?= site_url('impostos/retencoes') ?>" class="btn btn-warning">
                        <i class="fas fa-list"></i> Lista de Retenções
                    </a>
                    <a href="<?= site_url('impostos/relatorio') ?>" class="btn btn-success">
                        <i class="fas fa-file-alt"></i> Relatório Completo
                    </a>
                    <a href="<?= site_url('dre') ?>" class="btn btn-inverse">
                        <i class="fas fa-chart-line"></i> Ver no DRE
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Gráfico de Composição
new Chart(document.getElementById('grafico-composicao'), {
    type: 'doughnut',
    data: {
        labels: ['IRPJ', 'CSLL', 'COFINS', 'PIS', 'ISS'],
        datasets: [{
            data: [
                <?= $totais->total_irpj ?: 0 ?>,
                <?= $totais->total_csll ?: 0 ?>,
                <?= $totais->total_cofins ?: 0 ?>,
                <?= $totais->total_pis ?: 0 ?>,
                <?= $totais->total_iss ?: 0 ?>
            ],
            backgroundColor: ['#e74c3c', '#9b59b6', '#f39c12', '#3498db', '#27ae60']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right' }
        }
    }
});

// Gráfico de Evolução
const evolucao = <?= json_encode($evolucao) ?>;
new Chart(document.getElementById('grafico-evolucao'), {
    type: 'bar',
    data: {
        labels: evolucao.map(e => e.mes_formatado),
        datasets: [{
            label: 'Impostos Retidos',
            data: evolucao.map(e => e.total_impostos),
            backgroundColor: '#e74c3c'
        }, {
            label: 'Valor Líquido',
            data: evolucao.map(e => e.total_liquido),
            backgroundColor: '#27ae60'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
