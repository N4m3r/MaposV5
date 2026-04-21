<?php
$os_pendentes = $os_pendentes ?? [];
$os_hoje = $os_hoje ?? [];
$atividade_andamento = $atividade_andamento ?? null;
?>

<style>
.os-card {
    border-left: 4px solid #007bff;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
    border-radius: 0 8px 8px 0;
    transition: all 0.3s;
}

.os-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.os-card.urgente { border-left-color: #dc3545; }
.os-card.andamento { border-left-color: #ffc107; }
.os-card.concluida { border-left-color: #28a745; }

.os-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.os-numero {
    font-size: 18px;
    font-weight: bold;
    color: #007bff;
}

.os-status {
    font-size: 12px;
    padding: 4px 12px;
    border-radius: 15px;
}

.os-info {
    margin: 10px 0;
}

.os-info p {
    margin: 5px 0;
    color: #6c757d;
}

.os-info i {
    width: 20px;
    text-align: center;
}

.btn-iniciar {
    margin-top: 10px;
}

.alert-atividade {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
}
</style>

<div class="row-fluid">
    <div class="span12">
        <h2><i class="bx bx-clipboard"></i> Selecionar Ordem de Serviço</h2>

        <!-- Atividade em Andamento -->
        <?php if ($atividade_andamento): ?>
        <div class="alert-atividade">
            <div class="row-fluid">
                <div class="span8">
                    <h4><i class="bx bx-time-five"></i> Você tem uma atividade em andamento</h4>
                    <p>OS #<?= $atividade_andamento->os_id ?> - <?= htmlspecialchars($atividade_andamento->nomeCliente ?? 'N/A') ?></p>
                    <p><i class="bx bx-wrench"></i> <?= htmlspecialchars($atividade_andamento->tipo_nome) ?></p>
                    <p><i class="bx bx-play-circle"></i> Início: <strong><?= date('H:i', strtotime($atividade_andamento->hora_inicio)) ?></strong></p>
                </div>
                <div class="span4 text-right">
                    <a href="<?= site_url('atividades/wizard/' . $atividade_andamento->os_id) ?>" class="btn btn-large btn-light">
                        <i class="bx bx-play-circle"></i> Continuar Atendimento
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- OS de Hoje -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-calendar-check"></i></span>
                <h5>OS do Dia - <?= date('d/m/Y') ?></h5>
            </div>
            <div class="widget-content">
                <?php if (count($os_hoje) > 0): ?
                    <?php foreach ($os_hoje as $os): ?
                        <?php
                            $status_class = 'pendente';
                            if (strpos(strtolower($os->status ?? ''), 'atendi') !== false) {
                                $status_class = 'andamento';
                            } elseif (strpos(strtolower($os->status ?? ''), 'finaliz') !== false) {
                                $status_class = 'concluida';
                            }
                        ?>
                        <div class="os-card <?= $status_class ?>">
                            <div class="os-header">
                                <span class="os-numero">OS #<?= $os->idOs ?></span>
                                <span class="os-status label"><?= $os->status ?></span>
                            </div>
                            <div class="os-info">
                                <p><i class="bx bx-user"></i> <strong><?= htmlspecialchars($os->nomeCliente ?? 'N/A') ?></strong></p>
                                <p><i class="bx bx-map"></i> <?= htmlspecialchars(($os->rua ?? '') . ' ' . ($os->numero ?? '') . ' - ' . ($os->bairro ?? '')) ?></p>
                                <p><i class="bx bx-phone"></i> <?= htmlspecialchars($os->telefone ?? 'N/A') ?></p>
                                <p><i class="bx bx-wrench"></i> <?= htmlspecialchars($os->descricaoProduto ?? 'N/A') ?></p>
                            </div>
                            <?php if (!$atividade_andamento): ?>
                                <a href="<?= site_url('atividades/wizard/' . $os->idOs) ?>" class="btn btn-success btn-iniciar">
                                    <i class="bx bx-play"></i> Iniciar Atendimento
                                </a>
                            <?php else: ?
                                <button class="btn disabled" disabled>
                                    <i class="bx bx-lock"></i> Finalize a atividade atual
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?
                <?php else: ?
                    <div class="empty-state">
                        <i class="bx bx-calendar-x"></i>
                        <p>Nenhuma OS designada para hoje.</p>
                        <p class="small">Verifique com o administrador ou volte amanhã.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- OS Pendentes -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-time"></i></span>
                <h5>OS Pendentes</h5>
            </div>
            <div class="widget-content">
                <?php if (count($os_pendentes) > 0): ?
                    <div class="row-fluid">
                        <?php foreach ($os_pendentes as $os): ?
                            <div class="span6">
                                <div class="os-card">
                                    <div class="os-header">
                                        <span class="os-numero">OS #<?= $os->idOs ?></span>
                                        <span class="os-status label"><?= $os->status ?></span>
                                    </div>
                                    <div class="os-info">
                                        <p><i class="bx bx-user"></i> <strong><?= htmlspecialchars($os->nomeCliente ?? 'N/A') ?></strong></p>
                                        <p><i class="bx bx-map"></i> <?= htmlspecialchars(($os->rua ?? '') . ' ' . ($os->numero ?? '')) ?></p>
                                        <p><i class="bx bx-wrench"></i> <?= truncar_texto($os->descricaoProduto ?? 'N/A', 50) ?></p>
                                    </div>
                                    <?php if (!$atividade_andamento): ?>
                                        <a href="<?= site_url('atividades/wizard/' . $os->idOs) ?>" class="btn btn-small btn-success">
                                            <i class="bx bx-play"></i> Iniciar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?
                    </div>
                <?php else: ?
                    <div class="empty-state">
                        <i class="bx bx-check-circle"></i>
                        <p>Nenhuma OS pendente!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Voltar -->
        <a href="<?= site_url('atividades') ?>" class="btn">
            <i class="bx bx-arrow-back"></i> Voltar ao Dashboard
        </a>
    </div>
</div>
