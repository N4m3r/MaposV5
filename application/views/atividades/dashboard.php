<?php
$atividade_em_andamento = $atividade_em_andamento ?? null;
$resumo_dia = $resumo_dia ?? ['em_andamento' => null, 'total_atividades' => 0, 'tempo_trabalhado_horas' => 0];
$estatisticas = $estatisticas ?? ['total_atividades' => 0, 'concluidas' => 0, 'tempo_total_horas' => 0, 'por_categoria' => []];
$os_hoje = $os_hoje ?? [];
$is_admin = $is_admin ?? false;
?>

<style>
.dashboard-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.status-indicator {
    width: 15px;
    height: 15px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.status-online { background: #28a745; animation: pulse-green 2s infinite; }
.status-busy { background: #ffc107; animation: pulse-yellow 2s infinite; }
.status-offline { background: #6c757d; }

@keyframes pulse-green {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes pulse-yellow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.atividade-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.atividade-card h4 {
    margin: 0 0 10px 0;
}

.stat-box {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-box .stat-number {
    font-size: 28px;
    font-weight: bold;
    color: #007bff;
}

.stat-box .stat-label {
    font-size: 12px;
    color: #6c757d;
}

.os-card {
    border-left: 4px solid #007bff;
    padding: 15px;
    margin-bottom: 10px;
    background: #f8f9fa;
    border-radius: 0 8px 8px 0;
}

.os-card.prioridade-alta { border-left-color: #dc3545; }
.os-card.prioridade-media { border-left-color: #ffc107; }
.os-card.prioridade-baixa { border-left-color: #28a745; }

.btn-acao {
    margin: 3px;
}

.hora-display {
    font-size: 48px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    text-align: center;
}

.categoria-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    margin: 2px;
}
</style>

<div class="row-fluid">
    <div class="span12">
        <h2><i class="bx bx-user-circle"></i> Área do Técnico</h2>

        <!-- Status do Técnico -->
        <div class="dashboard-card">
            <div class="row-fluid">
                <div class="span8">
                    <h4>
                        <span class="status-indicator <?= $atividade_em_andamento ? 'status-busy' : 'status-online' ?>"></span>
                        Status: <strong><?= $atividade_em_andamento ? 'EM ATENDIMENTO' : 'DISPONÍVEL' ?></strong>
                    </h4>
                    <p class="text-muted"><?= date('d/m/Y - l') ?></p>
                </div>
                <div class="span4 text-right">
                    <?php if ($is_admin): ?>
                        <a href="<?= site_url('atividades/relatorio') ?>" class="btn">
                            <i class="bx bx-chart"></i> Relatórios
                        </a>
                    <?php endif; ?>
                    <a href="<?= site_url('atividades/historico') ?>" class="btn">
                        <i class="bx bx-history"></i> Histórico
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($atividade_em_andamento): ?>
<!-- ATIVIDADE EM ANDAMENTO -->
<div class="row-fluid">
    <div class="span12">
        <div class="atividade-card">
            <div class="row-fluid">
                <div class="span8">
                    <h4><i class="bx bx-wrench"></i> Atividade em Andamento</h4>
                    <h3><?= htmlspecialchars($atividade_em_andamento->tipo_nome) ?></h3>
                    <p>
                        <strong>OS #<?= $atividade_em_andamento->os_id ?></strong> -
                        <?= htmlspecialchars($atividade_em_andamento->nomeCliente) ?>
                    </p>
                    <p><i class="bx bx-map"></i> <?= htmlspecialchars($atividade_em_andamento->os_equipamento ?? 'N/A') ?></p>
                    <p>
                        <i class="bx bx-time"></i> Início: <strong><?= date('H:i', strtotime($atividade_em_andamento->hora_inicio)) ?></strong>
                        <span id="duracao-atual" style="margin-left: 20px;">Calculando...</span>
                    </p>
                </div>
                <div class="span4 text-right">
                    <div class="hora-display" id="cronometro">00:00:00</div>
                    <br>
                    <a href="<?= site_url('atividades/wizard/' . $atividade_em_andamento->os_id) ?>" class="btn btn-large btn-light">
                        <i class="bx bx-play-circle"></i> Continuar Atendimento
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Cronômetro para atividade em andamento
const horaInicio = new Date('<?= $atividade_em_andamento->hora_inicio ?>');
setInterval(function() {
    const agora = new Date();
    const diff = agora - horaInicio;
    const horas = Math.floor(diff / 3600000);
    const minutos = Math.floor((diff % 3600000) / 60000);
    const segundos = Math.floor((diff % 60000) / 1000);

    document.getElementById('cronometro').textContent =
        String(horas).padStart(2, '0') + ':' +
        String(minutos).padStart(2, '0') + ':' +
        String(segundos).padStart(2, '0');

    document.getElementById('duracao-atual').textContent =
        'Duração: ' + horas + 'h ' + minutos + 'min';
}, 1000);
</script>
<?php endif; ?>

<!-- ESTATÍSTICAS DO DIA -->
<div class="row-fluid">
    <div class="span12">
        <div class="dashboard-card">
            <h4><i class="bx bx-calendar-check"></i> Resumo de Hoje (<?= date('d/m/Y') ?>)</h4>
            <div class="row-fluid">
                <div class="span3">
                    <div class="stat-box">
                        <div class="stat-number"><?= $resumo_dia['total_atividades'] ?></div>
                        <div class="stat-label">Atividades Hoje</div>
                    </div>
                </div>
                <div class="span3">
                    <div class="stat-box">
                        <div class="stat-number"><?= count($resumo_dia['atividades_finalizadas'] ?? []) ?></div>
                        <div class="stat-label">Finalizadas</div>
                    </div>
                </div>
                <div class="span3">
                    <div class="stat-box">
                        <div class="stat-number"><?= $resumo_dia['tempo_trabalhado_horas'] ?></div>
                        <div class="stat-label">Horas Trabalhadas</div>
                    </div>
                </div>
                <div class="span3">
                    <div class="stat-box">
                        <div class="stat-number"><?= $atividade_em_andamento ? '1' : '0' ?></div>
                        <div class="stat-label">Em Andamento</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- OS DO DIA -->
<div class="row-fluid">
    <div class="span8">
        <div class="dashboard-card">
            <h4><i class="bx bx-list-ul"></i> Ordens de Serviço - Hoje</h4>

            <?php if (count($os_hoje) > 0): ?
                <?php foreach ($os_hoje as $os): ?
                    <?php
                        $prioridade = 'baixa';
                        if (strpos(strtolower($os->descricaoProduto ?? ''), 'urgente') !== false) {
                            $prioridade = 'alta';
                        } elseif (strpos(strtolower($os->status ?? ''), 'atendido') !== false) {
                            $prioridade = 'media';
                        }
                    ?>
                    <div class="os-card prioridade-<?= $prioridade ?>">
                        <div class="row-fluid">
                            <div class="span8">
                                <strong>OS #<?= $os->idOs ?></strong> -
                                <?= htmlspecialchars($os->nomeCliente ?? 'N/A') ?>
                                <br>
                                <small><i class="bx bx-wrench"></i> <?= htmlspecialchars($os->descricaoProduto ?? 'N/A') ?></small>
                                <?php if ($os->garantia ?? false): ?
                                    <span class="label label-warning"><i class="bx bx-shield"></i> Garantia</span>
                                <?php endif; ?>
                            </div>
                            <div class="span4 text-right">
                                <?php if ($atividade_em_andamento && $atividade_em_andamento->os_id == $os->idOs): ?
                                    <a href="<?= site_url('atividades/wizard/' . $os->idOs) ?>" class="btn btn-warning btn-small">
                                        <i class="bx bx-play-circle"></i> Continuar
                                    </a>
                                <?php elseif (!$atividade_em_andamento): ?
                                    <a href="<?= site_url('atividades/wizard/' . $os->idOs) ?>" class="btn btn-success btn-small">
                                        <i class="bx bx-play"></i> Iniciar
                                    </a>
                                <?php else: ?
                                    <span class="label">Em outra OS</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?
            <?php else: ?
                <div class="alert alert-info">
                    <i class="bx bx-info-circle"></i> Nenhuma OS designada para hoje.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ESTATÍSTICAS DO MÊS -->
    <div class="span4">
        <div class="dashboard-card">
            <h4><i class="bx bx-trophy"></i> Desempenho do Mês</h4>
            <br>
            <p><strong>Total de Atividades:</strong> <span class="pull-right"><?= $estatisticas['total_atividades'] ?></span></p>
            <p><strong>Concluídas:</strong> <span class="pull-right text-success"><?= $estatisticas['concluidas'] ?></span></p>
            <p><strong>Horas Totais:</strong> <span class="pull-right"><?= $estatisticas['tempo_total_horas'] ?>h</span></p>

            <hr>

            <h5>Por Categoria:</h5>
            <?php foreach ($estatisticas['por_categoria'] ?? [] as $cat): ?
                <div class="categoria-badge" style="background: <?= $cat->categoria ?>20; color: <?= $cat->categoria ?>;">
                    <?= ucfirst($cat->categoria) ?>: <?= $cat->total ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="dashboard-card">
            <h4><i class="bx bx-link"></i> Acesso Rápido</h4>
            <a href="<?= site_url('atividades/selecionar_os') ?>" class="btn btn-block">
                <i class="bx bx-search"></i> Selecionar OS
            </a>
            <a href="<?= site_url('atividades/historico') ?>" class="btn btn-block">
                <i class="bx bx-history"></i> Ver Histórico
            </a>
            <?php if ($is_admin): ?
                <a href="<?= site_url('atividades/relatorio') ?>" class="btn btn-block">
                    <i class="bx bx-chart"></i> Relatórios Admin
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
