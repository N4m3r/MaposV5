<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.tecnico-obras { padding: 20px; }
.tecnico-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
}
.tecnico-header h1 { margin: 0; font-size: 28px; font-weight: 700; }
.tecnico-header p { margin: 10px 0 0; opacity: 0.9; font-size: 16px; }

.stats-cards-tecnico {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card-tecnico {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-left: 5px solid #11998e;
    transition: all 0.3s;
}
.stat-card-tecnico:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
.stat-card-tecnico.warning { border-left-color: #f093fb; }
.stat-card-tecnico.info { border-left-color: #667eea; }
.stat-number-tecnico {
    font-size: 36px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}
.stat-label-tecnico {
    color: #888;
    font-size: 14px;
}
.stat-icon-tecnico {
    float: right;
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.obras-tecnico-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
}
.obra-tecnico-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
    transition: all 0.3s ease;
}
.obra-tecnico-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.obra-tecnico-header {
    padding: 25px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    position: relative;
}
.obra-tecnico-header.em-andamento { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.obra-tecnico-status {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.25);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.obra-tecnico-codigo {
    font-size: 13px;
    opacity: 0.9;
    margin-bottom: 5px;
}
.obra-tecnico-title {
    font-size: 20px;
    font-weight: 700;
    margin: 0;
}
.obra-tecnico-cliente {
    margin-top: 8px;
    font-size: 14px;
    opacity: 0.9;
}
.obra-tecnico-body { padding: 20px; }
.obra-tecnico-progress {
    margin-bottom: 20px;
}
.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 13px;
}
.progress-label { color: #666; }
.progress-value { font-weight: 700; color: #11998e; }
.progress-bar-modern {
    height: 10px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}
.progress-fill-modern {
    height: 100%;
    background: linear-gradient(90deg, #11998e, #38ef7d);
    border-radius: 10px;
    transition: width 0.5s ease;
}
.obra-tecnico-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 15px 0;
    border-top: 1px solid #f0f0f0;
}
.stat-item-modern {
    text-align: center;
}
.stat-item-value {
    font-size: 22px;
    font-weight: 700;
    color: #333;
}
.stat-item-value.primary { color: #667eea; }
.stat-item-value.success { color: #11998e; }
.stat-item-value.warning { color: #f093fb; }
.stat-item-label {
    font-size: 11px;
    color: #888;
    margin-top: 3px;
    text-transform: uppercase;
}
.obra-tecnico-actions {
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #e8e8e8;
}
.btn-executar {
    display: block;
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    text-align: center;
}
.btn-executar:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
}

.empty-state-tecnico {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
}
.empty-icon-tecnico {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    font-size: 60px;
    color: #ddd;
}
.empty-state-tecnico h3 { color: #666; font-weight: 400; margin-bottom: 10px; }
.empty-state-tecnico p { color: #999; margin-bottom: 25px; }

@media (max-width: 768px) {
    .obras-tecnico-grid { grid-template-columns: 1fr; }
}
</style>

<div class="tecnico-obras">

    <!-- Header -->
    <div class="tecnico-header">
        <h1><i class="icon-hard-hat"></i> Minhas Obras</h1>
        <p>Área de execução das obras atribuídas a você</p>
    </div>

    <?php if (!empty($obras)): ?>
        <!-- Stats Cards -->
        <div class="stats-cards-tecnico">
            <div class="stat-card-tecnico">
                <div class="stat-icon-tecnico"><i class="icon-building"></i></div>
                <div class="stat-number-tecnico"><?= count($obras) ?></div>
                <div class="stat-label-tecnico">Obras Ativas</div>
            </div>
            <div class="stat-card-tecnico warning">
                <div class="stat-icon-tecnico" style="background: linear-gradient(135deg, #f093fb, #f5576c);"><i class="icon-tasks"></i></div>
                <div class="stat-number-tecnico">
                    <?= array_sum(array_column($obras, 'etapas_pendentes')) ?>
                </div>
                <div class="stat-label-tecnico">Etapas Pendentes</div>
            </div>
            <div class="stat-card-tecnico info">
                <div class="stat-icon-tecnico" style="background: linear-gradient(135deg, #667eea, #764ba2);"><i class="icon-clipboard"></i></div>
                <div class="stat-number-tecnico">
                    <?= array_sum(array_column($obras, 'minhas_os')) ?>
                </div>
                <div class="stat-label-tecnico">Minhas OS</div>
            </div>
        </div>

        <!-- Obras Grid -->
        <div class="obras-tecnico-grid">
            <?php foreach ($obras as $obra): ?>
                <?php
                $headerClass = '';
                if ($obra->status == 'EmExecucao') $headerClass = 'em-andamento';
                ?>
                <div class="obra-tecnico-card">
                    <div class="obra-tecnico-header <?= $headerClass ?>">
                        <span class="obra-tecnico-status">
                            <?= $obra->status == 'EmExecucao' ? 'Em Execução' : $obra->status ?>
                        </span>
                        <div class="obra-tecnico-codigo">
                            <i class="icon-barcode"></i> <?= $obra->codigo ?>
                        </div>
                        <h4 class="obra-tecnico-title"><?= htmlspecialchars($obra->nome) ?></h4>
                        <div class="obra-tecnico-cliente">
                            <i class="icon-user"></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Não informado') ?>
                        </div>
                    </div>

                    <div class="obra-tecnico-body">
                        <!-- Progresso -->
                        <div class="obra-tecnico-progress">
                            <div class="progress-header">
                                <span class="progress-label">Progresso da Obra</span>
                                <span class="progress-value"><?= $obra->percentual_concluido ?? 0 ?>%</span>
                            </div>
                            <div class="progress-bar-modern">
                                <div class="progress-fill-modern" style="width: <?= $obra->percentual_concluido ?? 0 ?>%;"></div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="obra-tecnico-stats">
                            <div class="stat-item-modern">
                                <div class="stat-item-value primary"><?= $obra->minhas_os ?? 0 ?></div>
                                <div class="stat-item-label">Minhas OS</div>
                            </div>
                            <div class="stat-item-modern">
                                <div class="stat-item-value success"><?= $obra->etapas_pendentes ?? 0 ?></div>
                                <div class="stat-item-label">Etapas</div>
                            </div>
                            <div class="stat-item-modern">
                                <div class="stat-item-value warning"><?= count($obra->equipe ?? []) ?></div>
                                <div class="stat-item-label">Equipe</div>
                            </div>
                        </div>
                    </div>

                    <div class="obra-tecnico-actions">
                        <a href="<?= site_url('tecnicos/executar_obra/' . $obra->id) ?>" class="btn-executar">
                            <i class="icon-play-circle"></i> Executar Obra
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state-tecnico">
            <div class="empty-icon-tecnico">
                <i class="icon-building"></i>
            </div>
            <h3>Nenhuma obra atribuída</h3>
            <p>Você não está alocado em nenhuma obra no momento.</p>
            <p style="color: #888; font-size: 14px;">
                <i class="icon-info-sign"></i> Entre em contato com o gestor para ser alocado em uma obra.
            </p>
        </div>
    <?php endif; ?>
</div>

<script>
// Animate progress bars on load
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-fill-modern');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });
});
</script>
