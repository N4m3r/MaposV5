<?php
/**
 * Detalhe de Performance por Técnico
 */
$d = $results;
$tecnico = $d['tecnico'];
$performance = $d['performance'];
$evolucao = $d['evolucao_diaria'];
$atividades = $d['atividades'];
$data_inicio = $d['data_inicio'];
$data_fim = $d['data_fim'];
?>

<style>
.detail-kpi {
    text-align: center;
    padding: 15px;
}
.detail-kpi .value {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}
.detail-kpi .label {
    font-size: 11px;
    color: #7f8c8d;
    text-transform: uppercase;
    margin-top: 4px;
}
.chart-container {
    position: relative;
    height: 280px;
}
</style>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('relatoriotecnicos') ?>">Relatório de Técnicos</a> <span class="divider">/</span></li>
            <li class="active"><?= $tecnico->nome ?></li>
        </ul>
    </div>
</div>

<!-- KPIs do Técnico -->
<?php if ($performance): ?>
<div class="row-fluid">
    <div class="span2">
        <div class="widget-box">
            <div class="detail-kpi">
                <div class="value"><?= $performance->total_os ?></div>
                <div class="label">Total OS</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="detail-kpi">
                <div class="value" style="color:#27ae60;"><?= $performance->os_finalizadas ?></div>
                <div class="label">Finalizadas</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="detail-kpi">
                <div class="value" style="color:#3498db;"><?= $performance->horas_trabalhadas ?>h</div>
                <div class="label">Horas Trabalhadas</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="detail-kpi">
                <div class="value" style="color:#9b59b6;"><?= $performance->media_horas_dia ?>h</div>
                <div class="label">Média H/Dia</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="detail-kpi">
                <div class="value" style="color:#e67e22;"><?= $performance->media_os_dia ?></div>
                <div class="label">Média OS/Dia</div>
            </div>
        </div>
    </div>
    <div class="span2">
        <div class="widget-box">
            <div class="detail-kpi">
                <div class="value" style="color: <?= $performance->eficiencia >= 80 ? '#27ae60' : '#e74c3c' ?>;">
                    <?= $performance->eficiencia ?>%
                </div>
                <div class="label">Eficiência</div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Evolução Diária -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-chart-line"></i></span>
                <h5>Evolução Diária - <?= $tecnico->nome ?></h5>
                <div class="buttons">
                    <a href="<?= site_url('relatoriotecnicos') ?>?data_inicio=<?= $data_inicio ?>&data_fim=<?= $data_fim ?>" class="btn btn-small">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <canvas id="chartEvolucao"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Atividades Detalhadas -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-list"></i></span>
                <h5>Atividades do Período</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>OS</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Duração</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($atividades)): ?>
                        <tr><td colspan="8" style="text-align:center; color:#999;">Nenhuma atividade no período</td></tr>
                        <?php else: ?>
                            <?php foreach ($atividades as $a): ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('os/visualizar/' . $a->idOs) ?>"><?= $a->idOs ?></a>
                                </td>
                                <td><?= $a->nomeCliente ?: '-' ?></td>
                                <td><?= date('d/m/Y', strtotime($a->dataInicial)) ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    if (in_array($a->status, ['Finalizado', 'Faturado'])) $status_class = 'label-success';
                                    elseif ($a->status == 'Cancelado') $status_class = 'label-important';
                                    else $status_class = 'label-info';
                                    ?>
                                    <span class="label <?= $status_class ?>"><?= $a->status ?></span>
                                </td>
                                <td><?= $a->data_entrada ? date('d/m/Y H:i', strtotime($a->data_entrada)) : '-' ?></td>
                                <td><?= $a->data_saida ? date('d/m/Y H:i', strtotime($a->data_saida)) : '-' ?></td>
                                <td>
                                    <?php if ($a->duracao_minutos): ?>
                                        <?= floor($a->duracao_minutos / 60) ?>h <?= $a->duracao_minutos % 60 ?>min
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>R$ <?= number_format($a->valorTotal, 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Gráfico de Evolução Diária
(function() {
    var evolucao = <?= json_encode($evolucao) ?>;
    if (evolucao && evolucao.length > 0) {
        var ctx = document.getElementById('chartEvolucao').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: evolucao.map(function(e) {
                    var d = new Date(e.data + 'T00:00:00');
                    return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                }),
                datasets: [
                    {
                        label: 'Total OS',
                        data: evolucao.map(function(e) { return parseInt(e.total_os) || 0; }),
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: '#3498db',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Finalizadas',
                        data: evolucao.map(function(e) { return parseInt(e.finalizadas) || 0; }),
                        backgroundColor: 'rgba(39, 174, 96, 0.7)',
                        borderColor: '#27ae60',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Horas Trabalhadas',
                        data: evolucao.map(function(e) { return parseFloat(e.horas_trabalhadas) || 0; }),
                        type: 'line',
                        borderColor: '#e67e22',
                        backgroundColor: 'rgba(230, 126, 34, 0.1)',
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: { display: true, text: 'OS' },
                        ticks: { stepSize: 1 }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: { display: true, text: 'Horas' },
                        grid: { drawOnChartArea: false }
                    }
                },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12 } }
                }
            }
        });
    }
})();
</script>