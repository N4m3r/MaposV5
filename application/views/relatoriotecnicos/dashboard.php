<?php
/**
 * Dashboard de Performance dos Técnicos
 * Relatório completo com gráficos e métricas
 */
$data_inicio = $results['data_inicio'];
$data_fim = $results['data_fim'];
$tecnico_id = $results['tecnico_id'];
$kpi = $results['kpi_geral'];
?>

<style>
.kpi-card {
    text-align: center;
    padding: 20px;
}
.kpi-value {
    font-size: 32px;
    font-weight: bold;
    color: #2c3e50;
}
.kpi-label {
    font-size: 14px;
    color: #7f8c8d;
    text-transform: uppercase;
}
.kpi-trend {
    font-size: 12px;
    margin-top: 5px;
}
.kpi-trend.up { color: #27ae60; }
.kpi-trend.down { color: #e74c3c; }

.ranking-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #ecf0f1;
}
.ranking-position {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #3498db;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 15px;
}
.ranking-position.top3 {
    background: #f1c40f;
    color: #2c3e50;
}
.ranking-info {
    flex: 1;
}
.ranking-name {
    font-weight: bold;
}
.ranking-score {
    font-size: 12px;
    color: #7f8c8d;
}
.ranking-stats {
    text-align: right;
}

.chart-container {
    position: relative;
    height: 300px;
}

.metric-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}
.metric-title {
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
}
.metric-value {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}
.metric-delta {
    font-size: 12px;
}
</style>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li class="active">Relatório de Performance dos Técnicos</li>
        </ul>
    </div>
</div>

<!-- Título e Filtros -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-line"></i></span>
                <h5>Performance dos Técnicos</h5>
                <div class="buttons">
                    <a href="<?= site_url('relatoriotecnicos/exportar?data_inicio=' . $data_inicio . '&data_fim=' . $data_fim) ?>" class="btn btn-success btn-small">
                        <i class="fas fa-download"></i> Exportar CSV
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <form method="get" action="<?= site_url('relatoriotecnicos') ?>" class="form-inline">
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
                            <label>Técnico:</label>
                            <select name="tecnico_id" class="span12">
                                <option value="">Todos os Técnicos</option>
                                <?php foreach ($results['tecnicos'] as $tecnico): ?>
                                    <option value="<?= $tecnico->idUsuarios ?>" <?= ($tecnico_id == $tecnico->idUsuarios) ? 'selected' : '' ?>>
                                        <?= $tecnico->nome ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="span2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary span12">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                        <div class="span3">
                            <label>&nbsp;</label>
                            <div class="btn-group span12">
                                <a href="?data_inicio=<?= date('Y-m-d') ?>&data_fim=<?= date('Y-m-d') ?>" class="btn btn-small">Hoje</a>
                                <a href="?data_inicio=<?= date('Y-m-01') ?>&data_fim=<?= date('Y-m-d') ?>" class="btn btn-small">Mês</a>
                                <a href="?data_inicio=<?= date('Y-m-01', strtotime('-1 month')) ?>&data_fim=<?= date('Y-m-t', strtotime('-1 month')) ?>" class="btn btn-small">Mês Ant.</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- KPIs Gerais -->
<div class="row-fluid">
    <div class="span12">
        <h5><i class="fas fa-tachometer-alt"></i> Indicadores Gerais</h5>
    </div>
</div>

<div class="row-fluid">
    <div class="span2">
        <div class="widget-box">
            <div class="kpi-card">
                <div class="kpi-value"><?= $kpi['total_os'] ?></div>
                <div class="kpi-label">Total OS</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="kpi-card">
                <div class="kpi-value" style="color: #27ae60;"><?= $kpi['os_finalizadas'] ?></div>
                <div class="kpi-label">Finalizadas</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="kpi-card">
                <div class="kpi-value" style="color: #3498db;"><?= $kpi['taxa_conclusao'] ?>%</div>
                <div class="kpi-label">Taxa Conclusão</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="kpi-card">
                <div class="kpi-value"><?= $kpi['total_tecnicos'] ?></div>
                <div class="kpi-label">Técnicos Ativos</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="kpi-card">
                <div class="kpi-value" style="color: #9b59b6;"><?= $kpi['media_os_tecnico'] ?></div>
                <div class="kpi-label">Média OS/Técnico</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="kpi-card">
                <div class="kpi-value" style="color: #e67e22;"><?= count($results['ranking']) ?></div>
                <div class="kpi-label">Técnicos Produtivos</div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos Principais -->
<div class="row-fluid">
    <!-- OS por Técnico -->
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-bar"></i></span>
                <h5>Quantidade de OS por Técnico</h5>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <canvas id="chartOsPorTecnico"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Horas Trabalhadas -->
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-clock"></i></span>
                <h5>Horas Trabalhadas por Técnico</h5>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <canvas id="chartHorasTrabalhadas"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ranking e Eficiência -->
<div class="row-fluid">
    <!-- Ranking -->
    <div class="span4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-trophy"></i></span>
                <h5>Ranking de Produtividade</h5>
            </div>
            <div class="widget-content">
                <?php foreach ($results['ranking'] as $tecnico): ?>
                    <div class="ranking-item">
                        <div class="ranking-position <?= $tecnico->posicao <= 3 ? 'top3' : '' ?>">
                            <?= $tecnico->posicao ?>
                        </div>
                        <div class="ranking-info">
                            <div class="ranking-name"><?= $tecnico->tecnico ?></div>
                            <div class="ranking-score">Score: <?= $tecnico->score ?> | Eficiência: <?= $tecnico->eficiencia ?>%</div>
                        </div>
                        <div class="ranking-stats">
                            <strong><?= $tecnico->total_os ?></strong> OS<br>
                            <small><?= $tecnico->horas_trabalhadas ?>h</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Gráfico de Eficiência -->
    <div class="span4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-percentage"></i></span>
                <h5>Taxa de Eficiência por Técnico</h5>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <canvas id="chartEficiencia"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Projeções -->
    <div class="span4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-line"></i></span>
                <h5>Projeções para Próximo Mês</h5>
            </div>
            <div class="widget-content">
                <div class="metric-card">
                    <div class="metric-title">Taxa de Crescimento</div>
                    <div class="metric-value" style="color: <?= $results['projecoes']['taxa_crescimento'] >= 0 ? '#27ae60' : '#e74c3c' ?>">
                        <?= $results['projecoes']['taxa_crescimento'] ?>%
                    </div>
                    <div class="metric-delta <?= $results['projecoes']['taxa_crescimento'] >= 0 ? 'up' : 'down' ?>">
                        <i class="fas fa-arrow-<?= $results['projecoes']['taxa_crescimento'] >= 0 ? 'up' : 'down' ?>"></i>
                        <?= abs($results['projecoes']['taxa_crescimento']) ?>%
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-title">Média Diária Atual</div>
                    <div class="metric-value"><?= $results['projecoes']['media_os_dia'] ?></div>
                    <div class="metric-delta">OS por dia útil</div>
                </div>

                <div class="metric-card">
                    <div class="metric-title">Projeção Próximo Mês</div>
                    <div class="metric-value" style="color: #3498db;"><?= $results['projecoes']['projecao_proximo_mes'] ?></div>
                    <div class="metric-delta">OS estimadas</div>
                </div>

                <div class="metric-card">
                    <div class="metric-title">Dias Úteis no Mês</div>
                    <div class="metric-value"><?= $results['projecoes']['dias_uteis_mes'] ?></div>
                    <div class="metric-delta">Base para cálculo</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Performance Detalhada -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-table"></i></span>
                <h5>Performance Detalhada por Técnico</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>Técnico</th>
                            <th class="text-center">Total OS</th>
                            <th class="text-center">Finalizadas</th>
                            <th class="text-center">Horas Total</th>
                            <th class="text-center">Média Horas/OS</th>
                            <th class="text-center">Dias Trab.</th>
                            <th class="text-center">Média OS/Dia</th>
                            <th class="text-center">Média Horas/Dia</th>
                            <th class="text-center">Eficiência</th>
                            <th class="text-center">Ticket Médio</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results['performance_tecnicos'] as $t): ?>
                            <tr>
                                <td>
                                    <strong><?= $t->tecnico ?></strong>
                                </td>
                                <td class="text-center"><?= $t->total_os ?></td>
                                <td class="text-center">
                                    <span class="label label-success"><?= $t->os_finalizadas ?></span>
                                </td>
                                <td class="text-center"><?= $t->horas_trabalhadas ?>h</td>
                                <td class="text-center"><?= $t->media_horas_os ?>h</td>
                                <td class="text-center"><?= $t->dias_trabalhados ?></td>
                                <td class="text-center"><?= $t->media_os_dia ?></td>
                                <td class="text-center"><?= $t->media_horas_dia ?>h</td>
                                <td class="text-center">
                                    <div class="progress" style="margin-bottom: 0; height: 20px;">
                                        <div class="bar bar-success" style="width: <?= $t->eficiencia ?>%;">
                                            <?= $t->eficiencia ?>%
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">R$ <?= number_format($t->ticket_medio, 2, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('relatoriotecnicos/detalhe/' . $t->idUsuarios . '?data_inicio=' . $data_inicio . '&data_fim=' . $data_fim) ?>" class="btn btn-mini btn-info" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico Comparativo Mensal -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-area"></i></span>
                <h5>Comparativo Mensal (Últimos 6 meses)</h5>
            </div>
            <div class="widget-content">
                <div class="chart-container" style="height: 350px;">
                    <canvas id="chartComparativoMensal"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para Gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Dados PHP para JS
const osPorTecnico = <?= json_encode($results['os_por_tecnico']) ?>;
const performanceTecnicos = <?= json_encode($results['performance_tecnicos']) ?>;
const comparativoMensal = <?= json_encode($results['comparativo_mensal']) ?>;

// Cores para gráficos
const colors = [
    '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6',
    '#1abc9c', '#34495e', '#e67e22', '#95a5a6', '#d35400'
];

// Gráfico 1: OS por Técnico
new Chart(document.getElementById('chartOsPorTecnico'), {
    type: 'bar',
    data: {
        labels: osPorTecnico.map(t => t.tecnico),
        datasets: [{
            label: 'Total OS',
            data: osPorTecnico.map(t => t.quantidade),
            backgroundColor: colors,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Gráfico 2: Horas Trabalhadas
new Chart(document.getElementById('chartHorasTrabalhadas'), {
    type: 'bar',
    data: {
        labels: performanceTecnicos.map(t => t.tecnico),
        datasets: [{
            label: 'Horas Trabalhadas',
            data: performanceTecnicos.map(t => t.horas_trabalhadas),
            backgroundColor: '#9b59b6',
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Horas' } }
        }
    }
});

// Gráfico 3: Eficiência (Radar)
new Chart(document.getElementById('chartEficiencia'), {
    type: 'doughnut',
    data: {
        labels: performanceTecnicos.map(t => t.tecnico),
        datasets: [{
            data: performanceTecnicos.map(t => t.eficiencia),
            backgroundColor: colors,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right', labels: { boxWidth: 15, font: { size: 11 } } }
        }
    }
});

// Gráfico 4: Comparativo Mensal (Linha)
new Chart(document.getElementById('chartComparativoMensal'), {
    type: 'line',
    data: {
        labels: comparativoMensal.map(m => m.mes_formatado),
        datasets: [
            {
                label: 'Total OS',
                data: comparativoMensal.map(m => m.total_os),
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Finalizadas',
                data: comparativoMensal.map(m => m.finalizadas),
                borderColor: '#27ae60',
                backgroundColor: 'rgba(39, 174, 96, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Média OS/Técnico',
                data: comparativoMensal.map(m => m.media_os_tecnico),
                borderColor: '#e67e22',
                borderDash: [5, 5],
                fill: false,
                tension: 0.4
            }
        ]
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
