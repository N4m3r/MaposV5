<?php
/**
 * Análise Vertical e Horizontal do DRE
 */
$meses = $results['meses'];
$ano = $results['ano'];
$mesesNomes = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// Coletar todos os grupos possíveis
$grupos = [];
if (!empty($meses)) {
    $grupos = array_keys($meses[1]['dre']['grupos'] ?? []);
}
?>

<style>
.analise-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.analise-table th, .analise-table td { padding: 6px 8px; border: 1px solid #ddd; text-align: right; }
.analise-table th { background: #f8f9fa; font-weight: 600; text-align: center; }
.analise-table td:first-child { text-align: left; }
.analise-table .destaque { font-weight: bold; background: #e8f4f8; }
.analise-table .positivo { color: #27ae60; }
.analise-table .negativo { color: #e74c3c; }
.analise-table .mes-header { background: #3498db; color: white; }
.var-positivo { color: #27ae60; }
.var-negativo { color: #e74c3c; }
</style>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('dre') ?>">DRE Contábil</a> <span class="divider">/</span></li>
            <li class="active">Análise Vertical/Horizontal</li>
        </ul>
    </div>
</div>

<!-- Filtros -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-filter"></i></span>
                <h5>Período de Análise</h5>
            </div>
            <div class="widget-content">
                <form method="get" action="<?= site_url('dre/analise') ?>" class="form-inline">
                    <div class="row-fluid">
                        <div class="span3">
                            <label>Ano:</label>
                            <select name="ano" class="span12">
                                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= $y == $ano ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="span2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary span12">
                                <i class="fas fa-search"></i> Gerar Análise
                            </button>
                        </div>
                        <div class="span7">
                            <label>&nbsp;</label>
                            <div class="btn-group">
                                <a href="<?= site_url('dre') ?>" class="btn btn-small"><i class="fas fa-arrow-left"></i> Voltar ao DRE</a>
                                <a href="<?= site_url('dre/relatorio?data_inicio=' . $ano . '-01-01&data_fim=' . $ano . '-12-31') ?>" class="btn btn-small btn-info"><i class="fas fa-print"></i> Relatório Anual</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Análise Vertical -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-bar"></i></span>
                <h5>Análise Vertical - <?= $ano ?></h5>
                <small class="muted" style="margin-left: 10px;">Percentual de cada grupo em relação à Receita Bruta</small>
            </div>
            <div class="widget-content nopadding" style="overflow-x: auto;">
                <table class="analise-table">
                    <thead>
                        <tr>
                            <th style="text-align: left; min-width: 180px;">Grupo</th>
                            <?php foreach ($meses as $m): ?>
                            <th class="mes-header"><?= $mesesNomes[$m['mes']] ?? $m['mes'] ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grupos as $gk): ?>
                        <?php
                            $isDestaque = $meses[1]['dre']['grupos'][$gk]['destaque'] ?? false;
                            $titulo = $meses[1]['dre']['grupos'][$gk]['titulo'] ?? $gk;
                        ?>
                        <tr class="<?= $isDestaque ? 'destaque' : '' ?>">
                            <td><?= $titulo ?></td>
                            <?php foreach ($meses as $m): ?>
                            <?php
                                $valor = $m['dre']['grupos'][$gk]['valor'] ?? 0;
                                $receita = $m['dre']['grupos']['RECEITA_BRUTA']['valor'] ?? 0;
                                $percent = $receita > 0 ? round(($valor / $receita) * 100, 1) : 0;
                                $class = $valor >= 0 ? 'positivo' : 'negativo';
                            ?>
                            <td>
                                <div class="<?= $class ?>"><?= $percent ?>%</div>
                                <div style="font-size: 10px; color: #888;">R$ <?= number_format($valor, 0, ',', '.') ?></div>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Análise Horizontal (Variação Mensal) -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-line"></i></span>
                <h5>Análise Horizontal - Variação Mensal</h5>
                <small class="muted" style="margin-left: 10px;">Variação percentual em relação ao mês anterior</small>
            </div>
            <div class="widget-content nopadding" style="overflow-x: auto;">
                <table class="analise-table">
                    <thead>
                        <tr>
                            <th style="text-align: left; min-width: 180px;">Grupo</th>
                            <?php for ($i = 2; $i <= 12; $i++): ?>
                            <?php if (isset($meses[$i])): ?>
                            <th class="mes-header">vs <?= $mesesNomes[$i-1] ?? ($i-1) ?></th>
                            <?php endif; ?>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grupos as $gk): ?>
                        <?php $titulo = $meses[1]['dre']['grupos'][$gk]['titulo'] ?? $gk; ?>
                        <tr>
                            <td><?= $titulo ?></td>
                            <?php for ($i = 2; $i <= 12; $i++): ?>
                            <?php if (!isset($meses[$i]) || !isset($meses[$i-1])) continue; ?>
                            <?php
                                $valor_atual = $meses[$i]['dre']['grupos'][$gk]['valor'] ?? 0;
                                $valor_anterior = $meses[$i-1]['dre']['grupos'][$gk]['valor'] ?? 0;
                                if ($valor_anterior != 0) {
                                    $variacao = round((($valor_atual - $valor_anterior) / abs($valor_anterior)) * 100, 1);
                                } else {
                                    $variacao = $valor_atual != 0 ? 100 : 0;
                                }
                                $class = $variacao >= 0 ? 'var-positivo' : 'var-negativo';
                            ?>
                            <td class="<?= $class ?>">
                                <?= $variacao >= 0 ? '+' : '' ?><?= $variacao ?>%
                            </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Evolução Anual -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-area"></i></span>
                <h5>Evolução Anual - <?= $ano ?></h5>
            </div>
            <div class="widget-content">
                <div class="dre-grafico-container" style="height: 350px;">
                    <canvas id="chartAnalise"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
const mesesAnalise = <?= json_encode(array_values(array_map(function($m) use ($mesesNomes) {
    return [
        'mes' => $m['mes'],
        'label' => $mesesNomes[$m['mes']] ?? $m['mes_nome'],
        'receita_bruta' => $m['dre']['grupos']['RECEITA_BRUTA']['valor'] ?? 0,
        'lucro_bruto' => $m['dre']['grupos']['LUCRO_BRUTO']['valor'] ?? 0,
        'lucro_operacional' => $m['dre']['grupos']['LUCRO_OPERACIONAL']['valor'] ?? 0,
        'lucro_liquido' => $m['dre']['grupos']['LUCRO_LIQUIDO']['valor'] ?? 0,
    ];
}, $meses))) ?>;

new Chart(document.getElementById('chartAnalise'), {
    type: 'bar',
    data: {
        labels: mesesAnalise.map(e => e.label),
        datasets: [
            {
                label: 'Receita Bruta',
                data: mesesAnalise.map(e => e.receita_bruta),
                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                borderColor: '#3498db',
                borderWidth: 1
            },
            {
                label: 'Lucro Bruto',
                data: mesesAnalise.map(e => e.lucro_bruto),
                backgroundColor: 'rgba(46, 204, 113, 0.7)',
                borderColor: '#2ecc71',
                borderWidth: 1
            },
            {
                label: 'Lucro Líquido',
                data: mesesAnalise.map(e => e.lucro_liquido),
                backgroundColor: 'rgba(231, 76, 60, 0.7)',
                borderColor: '#e74c3c',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
});
</script>