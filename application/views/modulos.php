<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<link href="<?= base_url() ?>assets/css/dashboard.css" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
.modulos-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.modulos-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.modulos-header h1 {
    margin: 0;
    font-size: 28px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.modulos-header p {
    margin: 10px 0 0 0;
    opacity: 0.9;
    font-size: 15px;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-left: 4px solid #667eea;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card.success {
    border-left-color: #27ae60;
}

.stat-card.warning {
    border-left-color: #f39c12;
}

.stat-card.info {
    border-left-color: #3498db;
}

.stat-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 15px;
}

.stat-card.success .stat-card-icon {
    background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
}

.stat-card.warning .stat-card-icon {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}

.stat-card.info .stat-card-icon {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}

.stat-label {
    color: #888;
    font-size: 14px;
}

.stat-change {
    margin-top: 10px;
    font-size: 13px;
    color: #27ae60;
    font-weight: 500;
}

/* Seções */
.section-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #667eea;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #667eea, #764ba2);
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 3px solid #667eea;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 25px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #667eea;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #667eea;
}

.timeline-item.feature::before { background: #27ae60; box-shadow: 0 0 0 2px #27ae60; }
.timeline-item.fix::before { background: #e74c3c; box-shadow: 0 0 0 2px #e74c3c; }
.timeline-item.update::before { background: #3498db; box-shadow: 0 0 0 2px #3498db; }
.timeline-item.refactor::before { background: #9b59b6; box-shadow: 0 0 0 2px #9b59b6; }

.timeline-date {
    font-size: 12px;
    color: #888;
    margin-bottom: 5px;
}

.timeline-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.timeline-desc {
    font-size: 14px;
    color: #666;
    line-height: 1.5;
}

.timeline-stats {
    display: flex;
    gap: 15px;
    margin-top: 10px;
    font-size: 12px;
}

.timeline-stat {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    background: white;
    border-radius: 20px;
}

.timeline-stat.added {
    color: #27ae60;
}

.timeline-stat.removed {
    color: #e74c3c;
}

/* Módulos Grid */
.modulos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.modulo-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    border: 2px solid transparent;
    transition: all 0.3s;
}

.modulo-card:hover {
    border-color: #667eea;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.modulo-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.modulo-icon {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.modulo-title {
    font-weight: 600;
    color: #333;
    font-size: 16px;
}

.modulo-status {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 600;
}

.modulo-status.completed {
    background: #d4edda;
    color: #155724;
}

.modulo-status.in-progress {
    background: #fff3cd;
    color: #856404;
}

.modulo-status.planned {
    background: #d1ecf1;
    color: #0c5460;
}

.modulo-desc {
    font-size: 13px;
    color: #666;
    margin-bottom: 15px;
    line-height: 1.5;
}

.modulo-stats {
    display: flex;
    gap: 15px;
    font-size: 12px;
    color: #888;
}

.modulo-stat {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* GitHub Link */
.github-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 25px;
    background: #24292e;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
}

.github-link:hover {
    background: #2f363d;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

/* Responsivo */
@media (max-width: 768px) {
    .modulos-container {
        padding: 15px;
    }

    .modulos-header h1 {
        font-size: 22px;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .stat-value {
        font-size: 24px;
    }
}
</style>

<div class="modulos-container">
    <!-- Header -->
    <div class="modulos-header">
        <h1><i class="bx bx-code-block"></i> Módulos e Evolução do Sistema</h1>
        <p>Acompanhe o desenvolvimento e as adições de funcionalidades desde a versão original do Map-OS</p>
    </div>

    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-icon"><i class="bx bx-git-commit"></i></div>
            <div class="stat-value"><?= number_format($total_commits ?? 0) ?></div>
            <div class="stat-label">Total de Commits</div>
            <div class="stat-change"><i class="bx bx-up-arrow-alt"></i> Desde o início do projeto</div>
        </div>

        <div class="stat-card success">
            <div class="stat-card-icon"><i class="bx bx-plus-circle"></i></div>
            <div class="stat-value"><?= number_format($linhas_adicionadas ?? 0) ?></div>
            <div class="stat-label">Linhas Adicionadas</div>
            <div class="stat-change">Código novo implementado</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-card-icon"><i class="bx bx-minus-circle"></i></div>
            <div class="stat-value"><?= number_format($linhas_removidas ?? 0) ?></div>
            <div class="stat-label">Linhas Removidas</div>
            <div class="stat-change">Refatoração e limpeza</div>
        </div>

        <div class="stat-card info">
            <div class="stat-card-icon"><i class="bx bx-package"></i></div>
            <div class="stat-value"><?= count($modulos ?? []) ?></div>
            <div class="stat-label">Módulos Adicionados</div>
            <div class="stat-change">Funcionalidades extras</div>
        </div>

        <div class="stat-card" style="border-left-color: #e74c3c;">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <i class="bx bx-code-curly"></i>
            </div>
            <div class="stat-value"><?= number_format($total_linhas_codigo ?? 0) ?></div>
            <div class="stat-label">Total de Linhas de Código</div>
            <div class="stat-change"><?= $linhas_por_linguagem ? implode(', ', array_slice(array_map(fn($k, $v) => "$k: " . number_format($v), array_keys($linhas_por_linguagem), array_values($linhas_por_linguagem)), 0, 3)) : '' ?></div>
        </div>
    </div>

    <!-- Módulos Implementados -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title">
                <i class="bx bx-cube"></i>
                Módulos Adicionados
            </div>
            <span class="modulo-status completed">Versão Atual</span>
        </div>

        <div class="modulos-grid">
            <?php foreach ($modulos as $modulo): ?>
            <div class="modulo-card">
                <div class="modulo-header">
                    <div class="modulo-icon">
                        <i class="<?= $modulo['icone'] ?>"></i>
                    </div>
                    <div>
                        <div class="modulo-title"><?= htmlspecialchars($modulo['nome']) ?></div>
                        <span class="modulo-status <?= $modulo['status'] ?>">
                            <?= ucfirst($modulo['status']) ?>
                        </span>
                    </div>
                </div>
                <div class="modulo-desc">
                    <?= htmlspecialchars($modulo['descricao']) ?>
                </div>
                <div class="modulo-stats">
                    <div class="modulo-stat">
                        <i class="bx bx-calendar"></i>
                        <?= date('d/m/Y', strtotime($modulo['data'])) ?>
                    </div>
                    <div class="modulo-stat">
                        <i class="bx bx-code"></i>
                        <?= number_format($modulo['linhas'] ?? 0) ?> linhas
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Atualizações Futuras -->
    <?php if (!empty($modulos_futuros)): ?>
    <div class="section-card">
        <div class="section-header">
            <div class="section-title">
                <i class="bx bx-rocket"></i>
                Atualizações Futuras
            </div>
            <span class="modulo-status planned">Em Planejamento</span>
        </div>

        <div class="modulos-grid">
            <?php foreach ($modulos_futuros as $modulo): ?>
            <div class="modulo-card" style="border: 2px dashed #3498db; background: #f0f8ff;">
                <div class="modulo-header">
                    <div class="modulo-icon" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                        <i class="<?= $modulo['icone'] ?>"></i>
                    </div>
                    <div>
                        <div class="modulo-title"><?= htmlspecialchars($modulo['nome']) ?></div>
                        <span class="modulo-status <?= $modulo['status'] ?>">
                            <?= ucfirst($modulo['status']) ?>
                        </span>
                    </div>
                </div>
                <div class="modulo-desc">
                    <?= htmlspecialchars($modulo['descricao']) ?>
                </div>
                <div class="modulo-stats">
                    <div class="modulo-stat">
                        <i class="bx bx-calendar"></i>
                        Previsto: <?= date('d/m/Y', strtotime($modulo['data'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Timeline de Desenvolvimento -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title">
                <i class="bx bx-time-five"></i>
                Timeline de Desenvolvimento
            </div>
        </div>

        <div class="timeline">
            <?php foreach ($timeline as $item): ?>
            <div class="timeline-item <?= $item['tipo'] ?>">
                <div class="timeline-date">
                    <i class="bx bx-calendar"></i> <?= date('d/m/Y', strtotime($item['data'])) ?>
                </div>
                <div class="timeline-title">
                    <?= htmlspecialchars($item['titulo']) ?>
                </div>
                <div class="timeline-desc">
                    <?= htmlspecialchars($item['descricao']) ?>
                </div>
                <?php if (isset($item['adicionadas']) || isset($item['removidas'])): ?>
                <div class="timeline-stats">
                    <?php if (isset($item['adicionadas'])): ?>
                    <div class="timeline-stat added">
                        <i class="bx bx-plus"></i> +<?= number_format($item['adicionadas']) ?> linhas
                    </div>
                    <?php endif; ?>
                    <?php if (isset($item['removidas'])): ?>
                    <div class="timeline-stat removed">
                        <i class="bx bx-minus"></i> -<?= number_format($item['removidas']) ?> linhas
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Resumo por Tipo -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title">
                <i class="bx bx-pie-chart-alt-2"></i>
                Resumo por Categoria
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6">
                <h4 style="margin-bottom: 15px;">Módulos por Status</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Quantidade</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="label label-success">Concluído</span></td>
                            <td><?= $stats['concluidos'] ?? 0 ?></td>
                            <td><?= $total_modulos > 0 ? round(($stats['concluidos'] / $total_modulos) * 100) : 0 ?>%</td>
                        </tr>
                        <tr>
                            <td><span class="label label-warning">Em Progresso</span></td>
                            <td><?= $stats['em_progresso'] ?? 0 ?></td>
                            <td><?= $total_modulos > 0 ? round(($stats['em_progresso'] / $total_modulos) * 100) : 0 ?>%</td>
                        </tr>
                        <tr>
                            <td><span class="label label-info">Planejado</span></td>
                            <td><?= $stats['planejados'] ?? 0 ?></td>
                            <td><?= $total_modulos > 0 ? round(($stats['planejados'] / $total_modulos) * 100) : 0 ?>%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="span6">
                <h4 style="margin-bottom: 15px;">Referência GitHub</h4>
                <p style="margin-bottom: 15px;">Acesse o repositório original para comparar com a versão base do sistema:</p>
                <a href="https://github.com/RamonSilva20/mapos/pulse" target="_blank" class="github-link">
                    <i class="bx bxl-github"></i>
                    Ver Map-OS Original no GitHub
                </a>
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                    <strong>Commit Original:</strong><br>
                    <code style="font-size: 12px;">162ec5ec841a0efcd9fbd456d5e5b9d0ed67034c</code><br>
                    <strong>Total de Commits:</strong> <?= $total_commits ?? 0 ?><br>
                    <strong>Última Atualização:</strong> <?= date('d/m/Y H:i') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Animação dos números
function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        element.textContent = value.toLocaleString('pt-BR');
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Animar cards quando entrarem na tela
document.addEventListener('DOMContentLoaded', function() {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(function(el) {
        const finalValue = parseInt(el.textContent.replace(/\./g, '').replace(/,/g, ''));
        if (!isNaN(finalValue)) {
            animateValue(el, 0, finalValue, 1500);
        }
    });
});
</script>
