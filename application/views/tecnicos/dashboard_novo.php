<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
    /* Ajustes adicionais para dark mode no dashboard */
    body[data-theme="dark"] .welcome-card {
        background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%) !important;
    }

    body[data-theme="dark"] .btn-ver-obras {
        background: rgba(255,255,255,0.1) !important;
        color: #fff !important;
    }

    body[data-theme="dark"] .btn-ver-obras:hover {
        background: rgba(255,255,255,0.2) !important;
    }

    body[data-theme="dark"] .empty-state {
        color: #888 !important;
    }

    body[data-theme="dark"] .progress-bar-bg {
        background: #2d3347 !important;
    }
</style>

<!-- Header com saudação -->
<div class="tec-card welcome-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
    <div class="tec-card-body" style="color: #fff;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700;">
                <?= strtoupper(substr($tecnico->nome ?? 'T', 0, 1)) ?>
            </div>
            <div>
                <h2 style="margin: 0 0 4px 0; font-size: 20px;">
                    <?php
                    $hora = date('H');
                    $saudacao = ($hora < 12) ? 'Bom dia' : (($hora < 18) ? 'Boa tarde' : 'Boa noite');
                    echo $saudacao . ', ' . htmlspecialchars($tecnico->nome ?? 'Técnico');
                    ?>
                </h2>
                <p style="margin: 0; opacity: 0.9; font-size: 14px;">
                    <?= date('d/m/Y') ?> • Nível <?= $tecnico->nivel_tecnico ?? 'II' ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="tec-stats-grid">
    <div class="tec-stat-card">
        <div class="tec-stat-value"><?= count($os_hoje ?? []) ?></div>
        <div class="tec-stat-label">OS Hoje</div>
    </div>
    <div class="tec-stat-card warning">
        <div class="tec-stat-value"><?= count($os_pendentes ?? []) ?></div>
        <div class="tec-stat-label">Pendentes</div>
    </div>
    <div class="tec-stat-card success">
        <div class="tec-stat-value"><?= $os_concluidas ?? 0 ?></div>
        <div class="tec-stat-label">Concluídas</div>
    </div>
    <div class="tec-stat-card info">
        <div class="tec-stat-value"><?= count($minhas_obras ?? []) ?></div>
        <div class="tec-stat-label">Obras</div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="tec-card">
    <div class="tec-card-header">
        <div class="tec-card-title">
            <i class='bx bx-rocket'></i> Ações Rápidas
        </div>
    </div>
    <div class="tec-card-body">
        <div class="tec-quick-actions">
            <a href="<?= site_url('tecnicos/minhas_os') ?>" class="tec-action-btn">
                <i class='bx bx-clipboard' style="color: #3498db;"></i>
                <span>Minhas OS</span>
            </a>
            <a href="<?= site_url('tecnicos/minhas_obras') ?>" class="tec-action-btn">
                <i class='bx bx-building' style="color: #27ae60;"></i>
                <span>Minhas Obras</span>
            </a>
            <a href="<?= site_url('tecnicos/meu_estoque') ?>" class="tec-action-btn">
                <i class='bx bx-package' style="color: #9b59b6;"></i>
                <span>Meu Estoque</span>
            </a>
            <a href="<?= site_url('tecnicos/perfil') ?>" class="tec-action-btn">
                <i class='bx bx-user' style="color: #f39c12;"></i>
                <span>Meu Perfil</span>
            </a>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
    <!-- OS de Hoje -->
    <div class="tec-card">
        <div class="tec-card-header">
            <div class="tec-card-title">
                <i class='bx bx-calendar-check'></i> OS de Hoje
            </div>
            <a href="<?= site_url('tecnicos/minhas_os') ?>" style="font-size: 13px; color: #667eea; text-decoration: none;">Ver todas</a>
        </div>
        <div class="tec-card-body">
            <?php if (!empty($os_hoje)): ?>
                <div class="tec-list">
                    <?php foreach (array_slice($os_hoje, 0, 5) as $os): ?>
                        <?php
                        $statusClass = 'aberto';
                        if ($os->status == 'Em Andamento') $statusClass = 'andamento';
                        elseif ($os->status == 'Pendente') $statusClass = 'pendente';
                        elseif ($os->status == 'Finalizada') $statusClass = 'concluido';
                        ?>
                        <div class="tec-list-item">
                            <div class="tec-list-icon" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                                <i class='bx bx-wrench'></i>
                            </div>
                            <div class="tec-list-content">
                                <div class="tec-list-title">OS #<?= $os->idOs ?> - <?= htmlspecialchars($os->cliente_nome ?? 'Cliente') ?></div>
                                <div class="tec-list-subtitle">
                                    <?= isset($os->hora_inicial) ? $os->hora_inicial : 'Sem horário' ?> • <?= $os->status ?>
                                </div>
                            </div>
                            <span class="tec-list-status <?= $statusClass ?>"><?= $os->status ?></span>
                            <a href="<?= site_url('tecnicos/executar_os/' . $os->idOs) ?>" class="tec-list-action" style="color: #27ae60; font-size: 20px;">
                                <i class='bx bx-play-circle'></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state" style="text-align: center; padding: 40px; color: #888;">
                    <i class='bx bx-calendar' style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p>Nenhuma OS agendada para hoje</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Minhas Obras -->
    <div class="tec-card">
        <div class="tec-card-header">
            <div class="tec-card-title">
                <i class='bx bx-building'></i> Minhas Obras
            </div>
            <a href="<?= site_url('tecnicos/minhas_obras') ?>" style="font-size: 13px; color: #667eea; text-decoration: none;">Ver todas</a>
        </div>
        <div class="tec-card-body">
            <?php if (!empty($minhas_obras)): ?>
                <div class="tec-list">
                    <?php foreach (array_slice($minhas_obras, 0, 5) as $obra): ?>
                        <div class="tec-list-item">
                            <div class="tec-list-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                                <i class='bx bx-hard-hat'></i>
                            </div>
                            <div class="tec-list-content">
                                <div class="tec-list-title"><?= htmlspecialchars($obra->nome) ?></div>
                                <div class="tec-list-subtitle">
                                    <?= htmlspecialchars($obra->cliente_nome ?? 'Cliente') ?> • <?= $obra->percentual_concluido ?? 0 ?>% concluído
                                </div>
                            </div>
                            <div class="progress-bar-bg" style="width: 60px; height: 6px; background: #e8e8e8; border-radius: 3px; overflow: hidden;">
                                <div style="width: <?= $obra->percentual_concluido ?? 0 ?>%; height: 100%; background: linear-gradient(90deg, #27ae60, #2ecc71); border-radius: 3px;"></div>
                            </div>
                            <a href="<?= site_url('tecnicos/executar_obra/' . $obra->id) ?>" class="tec-list-action" style="color: #27ae60; font-size: 20px;">
                                <i class='bx bx-play-circle'></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state" style="text-align: center; padding: 40px; color: #888;">
                    <i class='bx bx-building' style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p>Você não está alocado em nenhuma obra</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Estoque Rápido -->
<div class="tec-card" style="margin-top: 20px;">
    <div class="tec-card-header">
        <div class="tec-card-title">
            <i class='bx bx-package'></i> Meu Estoque
        </div>
        <a href="<?= site_url('tecnicos/meu_estoque') ?>" style="font-size: 13px; color: #667eea; text-decoration: none;">Ver tudo</a>
    </div>
    <div class="tec-card-body">
        <?php if (!empty($estoque)): ?>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <?php foreach (array_slice($estoque, 0, 6) as $item): ?>
                    <div style="background: #f8f9fa; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                        <i class='bx bx-package' style="color: #27ae60; font-size: 20px;"></i>
                        <span style="font-size: 14px; font-weight: 500;"><?= htmlspecialchars($item->produto_nome ?? 'Produto') ?></span>
                        <span style="background: #e8f4f8; color: #3498db; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            <?= $item->quantidade ?> <?= $item->unidade ?? '' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 30px; color: #888;">
                <i class='bx bx-package' style="font-size: 32px; margin-bottom: 10px; opacity: 0.5;"></i>
                <p>Nenhum item em estoque</p>
            </div>
        <?php endif; ?>
    </div>
</div>
