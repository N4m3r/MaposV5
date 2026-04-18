<!-- Minhas OS - Portal do Técnico -->
<div class="row-fluid">
    <div class="span12">

        <!-- Header da Página -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-clipboard"></i></span>
                <h5>Minhas Ordens de Serviço</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="btn btn-mini btn-info">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Filtros -->
                <div class="filter-tabs">
                    <a href="<?php echo site_url('tecnicos/minhas_os?status=todos'); ?>"
                       class="filter-tab <?php echo $status_atual == 'todos' ? 'active' : ''; ?>">
                        <i class="bx bx-list-ul"></i> Todas
                    </a>
                    <a href="<?php echo site_url('tecnicos/minhas_os?status=Aberto'); ?>"
                       class="filter-tab <?php echo $status_atual == 'Aberto' ? 'active' : ''; ?>">
                        <i class="bx bx-circle"></i> Abertas
                    </a>
                    <a href="<?php echo site_url('tecnicos/minhas_os?status=Em Andamento'); ?>"
                       class="filter-tab <?php echo $status_atual == 'Em Andamento' ? 'active' : ''; ?>">
                        <i class="bx bx-play-circle"></i> Em Andamento
                    </a>
                    <a href="<?php echo site_url('tecnicos/minhas_os?status=Finalizada'); ?>"
                       class="filter-tab <?php echo $status_atual == 'Finalizada' ? 'active' : ''; ?>">
                        <i class="bx bx-check-circle"></i> Finalizadas
                    </a>
                </div>

                <!-- Lista de OS -->
                <?php if (empty($os_list)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bx bx-clipboard"></i>
                        </div>
                        <h4>Nenhuma OS encontrada</h4>
                        <p>Não há ordens de serviço <?php echo $status_atual != 'todos' ? 'com este status' : ''; ?></p>
                    </div>
                <?php else: ?>
                    <div class="os-list">
                        <?php foreach ($os_list as $os): ?>
                            <div class="os-card">
                                <div class="os-header">
                                    <span class="os-number">#OS <?php echo $os->idOs; ?></span>
                                    <span class="os-status status-<?php echo strtolower(str_replace(' ', '_', $os->status)); ?>">
                                        <?php echo $os->status; ?>
                                    </span>
                                </div>

                                <div class="os-client">
                                    <i class="bx bx-user"></i>
                                    <?php echo htmlspecialchars($os->cliente_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                </div>

                                <?php if ($os->descricaoProduto): ?>
                                    <div class="os-desc">
                                        <?php echo htmlspecialchars(substr($os->descricaoProduto, 0, 100), ENT_COMPAT | ENT_HTML5, 'UTF-8') . (strlen($os->descricaoProduto) > 100 ? '...' : ''); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="os-footer">
                                    <div class="os-date">
                                        <i class="bx bx-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($os->dataInicial)); ?>
                                    </div>
                                    <a href="<?php echo site_url('tecnicos/executar_os/' . $os->idOs); ?>"
                                       class="btn btn-mini btn-success">
                                        <i class="bx bx-play"></i> Executar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<style>
/* Filtros */
.filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 8px 16px;
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 20px;
    white-space: nowrap;
    text-decoration: none;
    color: #666;
    font-size: 0.9rem;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.filter-tab:hover {
    text-decoration: none;
    border-color: #667eea;
    color: #667eea;
}

.filter-tab.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
}

/* Lista de OS */
.os-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.os-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s;
}

.os-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #667eea;
}

.os-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.os-number {
    font-weight: 700;
    color: #667eea;
    font-size: 1.1rem;
}

.os-status {
    font-size: 0.75rem;
    padding: 5px 12px;
    border-radius: 12px;
    font-weight: 600;
}

.os-status.status-aberto {
    background: #e3f2fd;
    color: #1976d2;
}

.os-status.status-em_andamento {
    background: #fff3e0;
    color: #ef6c00;
}

.os-status.status-finalizada {
    background: #e8f5e9;
    color: #2e7d32;
}

.os-status.status-cancelada {
    background: #ffebee;
    color: #c62828;
}

.os-client {
    font-weight: 600;
    font-size: 1.05rem;
    margin-bottom: 8px;
    color: #333;
}

.os-client i {
    color: #667eea;
}

.os-desc {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 12px;
    line-height: 1.5;
}

.os-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.os-date {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #888;
    font-size: 0.9rem;
}

.os-date i {
    color: #667eea;
}

/* Estado Vazio */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-icon i {
    font-size: 64px;
    color: #e0e0e0;
}

.empty-state h4 {
    color: #666;
    font-weight: 400;
}

/* Responsividade Mobile */
@media (max-width: 768px) {
    .filter-tabs {
        overflow-x: auto;
        padding-bottom: 5px;
        -webkit-overflow-scrolling: touch;
    }

    .filter-tab {
        flex-shrink: 0;
    }

    .os-card {
        padding: 15px;
    }

    .os-number {
        font-size: 1rem;
    }

    .os-client {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .os-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .os-footer {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }

    .os-footer .btn {
        width: 100%;
    }
}
</style>
