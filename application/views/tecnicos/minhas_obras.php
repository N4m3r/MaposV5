<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Tema Moderno Obras -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
/* Container Principal */
.obras-container { padding: 15px; max-width: 100%; }

/* Header Mobile-First */
.obras-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 25px 20px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
}
.obras-header h1 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 700;
}
.obras-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

/* Cards de Estatísticas */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 25px;
}
.stat-card {
    background: white;
    border-radius: 15px;
    padding: 20px 15px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-top: 4px solid #11998e;
}
.stat-card.warning { border-top-color: #f39c12; }
.stat-card.info { border-top-color: #3498db; }
.stat-numero {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}
.stat-label {
    font-size: 11px;
    color: #888;
    text-transform: uppercase;
    font-weight: 600;
}

/* Filtros */
.filtros-container {
    background: white;
    border-radius: 15px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.filtros-titulo {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
}
.filtros-grid {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 5px;
}
.filtro-btn {
    padding: 10px 20px;
    border: 2px solid #e8e8e8;
    border-radius: 25px;
    background: white;
    color: #666;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.3s;
}
.filtro-btn:hover,
.filtro-btn.active {
    border-color: #11998e;
    background: #f0fff4;
    color: #11998e;
}

/* Cards de Obras */
.obras-lista {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.obra-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s;
}
.obra-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.obra-header-card {
    padding: 20px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    position: relative;
}
.obra-header-card.em-execucao {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}
.obra-status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.25);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}
.obra-codigo {
    font-size: 12px;
    opacity: 0.9;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.obra-nome {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 8px 0;
    line-height: 1.3;
}
.obra-cliente {
    font-size: 13px;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Corpo do Card */
.obra-body {
    padding: 20px;
}
.obra-progresso-section {
    margin-bottom: 20px;
}
.obra-progresso-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.obra-progresso-label {
    font-size: 13px;
    color: #666;
}
.obra-progresso-valor {
    font-size: 20px;
    font-weight: 700;
    color: #11998e;
}
.obra-barra-progresso {
    height: 10px;
    background: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
}
.obra-barra-preenchida {
    height: 100%;
    background: linear-gradient(90deg, #11998e, #38ef7d);
    border-radius: 5px;
    transition: width 0.5s ease;
}

/* Stats Grid dentro do card */
.obra-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 15px 0;
    border-top: 1px solid #f0f0f0;
}
.obra-stat-item {
    text-align: center;
}
.obra-stat-valor {
    font-size: 20px;
    font-weight: 700;
    color: #333;
}
.obra-stat-valor.os { color: #3498db; }
.obra-stat-valor.etapas { color: #f39c12; }
.obra-stat-valor.equipe { color: #9b59b6; }
.obra-stat-label {
    font-size: 10px;
    color: #888;
    text-transform: uppercase;
    margin-top: 4px;
}

/* Botões de Ação */
.obra-actions {
    padding: 0 20px 20px;
}
.btn-executar-obra {
    display: block;
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    text-decoration: none;
}
.btn-executar-obra:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
    color: white;
    text-decoration: none;
}
.btn-executar-obra i {
    font-size: 20px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.empty-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    font-size: 50px;
    color: #ddd;
}
.empty-state h3 {
    color: #666;
    font-weight: 400;
    margin-bottom: 10px;
    font-size: 18px;
}
.empty-state p {
    color: #999;
    font-size: 14px;
    margin-bottom: 25px;
}

/* Desktop Responsive */
@media (min-width: 768px) {
    .obras-container { padding: 30px; max-width: 1200px; margin: 0 auto; }
    .obras-lista {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 20px;
    }
    .stats-grid { gap: 20px; }
    .stat-card { padding: 25px; }
}

/* Animações */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.obra-card {
    animation: slideInUp 0.4s ease;
}

/* Alerta de Info */
.info-box {
    background: #e8f4f8;
    border-left: 4px solid #3498db;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 0 10px 10px 0;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.info-box i {
    color: #3498db;
    font-size: 20px;
    margin-top: 2px;
}
.info-box p {
    margin: 0;
    color: #555;
    font-size: 13px;
}

/* Mini Etapas Progress */
.etapas-mini-list {
    margin: 12px 0;
    max-height: 140px;
    overflow-y: auto;
}
.etapa-mini-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 0;
    border-bottom: 1px solid #f5f5f5;
    font-size: 12px;
}
.etapa-mini-item:last-child {
    border-bottom: none;
}
.etapa-mini-numero {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 11px;
    color: #666;
    flex-shrink: 0;
}
.etapa-mini-numero.concluida {
    background: #11998e;
    color: white;
}
.etapa-mini-nome {
    flex: 1;
    color: #444;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.etapa-mini-barra {
    width: 60px;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    flex-shrink: 0;
}
.etapa-mini-preenchida {
    height: 100%;
    border-radius: 3px;
    transition: width 0.4s ease;
}
.etapa-mini-pct {
    width: 32px;
    text-align: right;
    font-weight: 700;
    font-size: 11px;
    color: #667eea;
    flex-shrink: 0;
}

/* Atividades Stats */
.atividades-stats {
    display: flex;
    gap: 12px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #f0f0f0;
}
.atividade-stat {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #666;
}
.atividade-stat i {
    font-size: 14px;
}
.atividade-stat .num {
    font-weight: 700;
    color: #333;
}
.atividade-stat.concluidas i { color: #11998e; }
.atividade-stat.pendentes i { color: #f39c12; }
.atividade-stat.total i { color: #3498db; }
</style>

<div class="obras-container">

    <!-- Header -->
    <div class="obras-header">
        <h1><i class="icon-hard-hat"></i> Minhas Obras</h1>
        <p>Obras em que você está alocado para execução</p>
    </div>

    <?php if (!empty($obras)): ?>

        <!-- Cards de Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-numero"><?= count($obras) ?></div>
                <div class="stat-label">Obras Ativas</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-numero"><?= array_sum(array_column($obras, 'atividades_pendentes')) ?></div>
                <div class="stat-label">Ativ. Pendentes</div>
            </div>
            <div class="stat-card info">
                <div class="stat-numero"><?= array_sum(array_column($obras, 'minhas_os')) ?></div>
                <div class="stat-label">Minhas OS</div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <i class="icon-info-sign"></i>
            <p>
                <strong>Dica:</strong> Clique em "Executar Obra" para registrar seu check-in,
                acompanhar etapas e registrar atividades com fotos.
            </p>
        </div>

        <!-- Lista de Obras -->
        <div class="obras-lista">
            <?php foreach ($obras as $obra): ?>
                <?php
                $headerClass = ($obra->status == 'EmExecucao') ? 'em-execucao' : '';
                $statusLabel = ($obra->status == 'EmExecucao') ? 'Em Execução' : $obra->status;
                ?>

                <div class="obra-card">
                    <div class="obra-header-card <?= $headerClass ?>">
                        <span class="obra-status-badge"><?= $statusLabel ?></span>
                        <div class="obra-codigo">
                            <i class="icon-barcode"></i> <?= $obra->codigo ?>
                        </div>
                        <h2 class="obra-nome"><?= htmlspecialchars($obra->nome) ?></h2>
                        <div class="obra-cliente">
                            <i class="icon-user"></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Não informado') ?>
                        </div>
                    </div>

                    <div class="obra-body">
                        <!-- Progresso -->
                        <div class="obra-progresso-section">
                            <div class="obra-progresso-header">
                                <span class="obra-progresso-label">Progresso da Obra</span>
                                <span class="obra-progresso-valor"><?= $obra->percentual_concluido ?? 0 ?>%</span>
                            </div>
                            <div class="obra-barra-progresso">
                                <div class="obra-barra-preenchida" style="width: <?= $obra->percentual_concluido ?? 0 ?>%"></div>
                            </div>
                        </div>

                        <!-- Etapas com Progresso -->
                        <?php if (!empty($obra->etapas)): ?
                        <div class="etapas-mini-list">
                            <?php foreach (array_slice($obra->etapas, 0, 4) as $etapa):
                                $etapa_pct = $etapa->percentual_concluido ?? 0;
                                $etapa_class = $etapa_pct >= 100 ? 'concluida' : '';
                                $barra_cor = $etapa_pct >= 100 ? '#11998e' : ($etapa_pct >= 50 ? '#4facfe' : ($etapa_pct > 0 ? '#f39c12' : '#95a5a6'));
                            ?>
                            <div class="etapa-mini-item">
                                <div class="etapa-mini-numero <?= $etapa_class ?>"><?= $etapa->numero_etapa ?></div>
                                <div class="etapa-mini-nome"><?= htmlspecialchars($etapa->nome) ?></div>
                                <div class="etapa-mini-barra">
                                    <div class="etapa-mini-preenchida" style="width: <?= $etapa_pct ?>%; background: <?= $barra_cor ?>"></div>
                                </div>
                                <div class="etapa-mini-pct"><?= $etapa_pct ?>%</div>
                            </div>
                            <?php endforeach; ?
                            <?php if (count($obra->etapas) > 4): ?
                            <div class="etapa-mini-item" style="justify-content: center; color: #888; font-size: 11px;">
                                <i class="icon-ellipsis-horizontal"></i> <?= count($obra->etapas) - 4 ?> etapa(s) a mais
                            </div>
                            <?php endif; ?
                        </div>
                        <?php endif; ?

                        <!-- Atividades Stats -->
                        <div class="atividades-stats">
                            <div class="atividade-stat total">
                                <i class="icon-tasks"></i>
                                <span class="num"><?= $obra->atividades_total ?? 0 ?></span> ativ.
                            </div>
                            <div class="atividade-stat concluidas">
                                <i class="icon-check"></i>
                                <span class="num"><?= $obra->atividades_concluidas ?? 0 ?></span> concl.
                            </div>
                            <div class="atividade-stat pendentes">
                                <i class="icon-clock"></i>
                                <span class="num"><?= $obra->atividades_pendentes ?? 0 ?></span> pend.
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="obra-stats">
                            <div class="obra-stat-item">
                                <div class="obra-stat-valor os"><?= $obra->minhas_os ?? 0 ?></div>
                                <div class="obra-stat-label">Minhas OS</div>
                            </div>
                            <div class="obra-stat-item">
                                <div class="obra-stat-valor etapas"><?= $obra->etapas_pendentes ?? 0 ?>/<?= $obra->total_etapas ?? 0 ?></div>
                                <div class="obra-stat-label">Etapas</div>
                            </div>
                            <div class="obra-stat-item">
                                <div class="obra-stat-valor equipe"><?= count($obra->equipe ?? []) ?></div>
                                <div class="obra-stat-label">Equipe</div>
                            </div>
                        </div>
                    </div>

                    <div class="obra-actions">
                        <a href="<?= site_url('tecnicos/executar_obra/' . $obra->id) ?>" class="btn-executar-obra">
                            <i class="icon-play-circle"></i> Executar Obra
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>

        <div class="empty-state">
            <div class="empty-icon">
                <i class="icon-building"></i>
            </div>
            <h3>Nenhuma obra atribuída</h3>
            <p>Você não está alocado em nenhuma obra no momento.</p>
            <p style="color: #888; font-size: 14px; margin-top: 15px;">
                <i class="icon-info-sign"></i> Entre em contato com o gestor para ser alocado em uma obra.
            </p>
        </div>

    <?php endif; ?>

</div>

<script>
// Animate progress bars on scroll
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.obra-barra-preenchida');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });
});

// Add animation on scroll
function animateOnScroll() {
    const cards = document.querySelectorAll('.obra-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
    });
}

animateOnScroll();
</script>
