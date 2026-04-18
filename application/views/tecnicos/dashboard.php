<!-- Dashboard do Técnico - Portal Interno -->
<div class="row-fluid">
    <div class="span12">

        <!-- Perfil do Técnico -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-hard-hat"></i></span>
                <h5>Portal do Técnico</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/logout'); ?>" class="btn btn-mini btn-danger">
                        <i class="bx bx-log-out"></i> Sair
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="media">
                            <div class="pull-left">
                                <div class="tec-avatar">
                                    <?php echo strtoupper(substr($tecnico->nome ?? 'T', 0, 1)); ?>
                                </div>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <?php echo htmlspecialchars($tecnico->nome ?? 'Técnico', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                </h4>
                                <p class="text-muted">
                                    <i class="bx bx-envelope"></i>
                                    <?php echo htmlspecialchars($tecnico->email ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                </p>
                                <span class="label label-info">
                                    <i class="bx bx-star"></i> Nível <?php echo $tecnico->nivel_tecnico ?? 'II'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Status -->
        <div class="row-fluid">
            <div class="span4 responsive-col">
                <div class="widget-box stat-card stat-card-success">
                    <div class="widget-content text-center">
                        <div class="stat-number"><?php echo count($os_hoje ?? []); ?></div>
                        <div class="stat-label">
                            <i class="bx bx-calendar-check"></i> OS Hoje
                        </div>
                    </div>
                </div>
            </div>
            <div class="span4 responsive-col">
                <div class="widget-box stat-card stat-card-warning">
                    <div class="widget-content text-center">
                        <div class="stat-number"><?php echo count($os_pendentes ?? []); ?></div>
                        <div class="stat-label">
                            <i class="bx bx-time-five"></i> Pendentes
                        </div>
                    </div>
                </div>
            </div>
            <div class="span4 responsive-col">
                <div class="widget-box stat-card stat-card-info">
                    <div class="widget-content text-center">
                        <div class="stat-number"><?php echo $os_concluidas ?? 0; ?></div>
                        <div class="stat-label">
                            <i class="bx bx-check-circle"></i> Concluídas (Semana)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-bolt"></i></span>
                <h5>Ações Rápidas</h5>
            </div>
            <div class="widget-content">
                <div class="quick-actions">
                    <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-large btn-action btn-action-primary">
                        <i class="bx bx-clipboard"></i>
                        <span>Minhas OS</span>
                    </a>
                    <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="btn btn-large btn-action btn-action-success">
                        <i class="bx bx-package"></i>
                        <span>Meu Estoque</span>
                    </a>
                    <a href="<?php echo site_url('tecnicos/perfil'); ?>" class="btn btn-large btn-action btn-action-warning">
                        <i class="bx bx-user"></i>
                        <span>Meu Perfil</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- OS de Hoje -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-calendar-event"></i></span>
                <h5>OS de Hoje</h5>
                <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-mini btn-info">
                    Ver Todas
                </a>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($os_hoje)): ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">OS</th>
                                <th>Cliente</th>
                                <th style="width: 150px;">Horário</th>
                                <th style="width: 120px; text-align: center;">Status</th>
                                <th style="width: 80px; text-align: center;">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($os_hoje as $os): ?>
                                <tr>
                                    <td class="text-center os-number">
                                        #<?php echo $os->idOs; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($os->cliente_nome ?? 'N/A', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                    </td>
                                    <td>
                                        <?php echo isset($os->hora_inicial) ? $os->hora_inicial : '-'; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $statusLabel = $os->status ?? 'Aberto';
                                        $statusClass = 'label';
                                        switch ($statusLabel) {
                                            case 'Aberto':
                                                $statusClass = 'label label-info';
                                                break;
                                            case 'Em Andamento':
                                                $statusClass = 'label label-warning';
                                                break;
                                            case 'Finalizada':
                                                $statusClass = 'label label-success';
                                                break;
                                            default:
                                                $statusClass = 'label';
                                        }
                                        ?>
                                        <span class="<?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo site_url('tecnicos/executar_os/' . $os->idOs); ?>"
                                           class="btn btn-mini btn-success" title="Executar OS">
                                            <i class="bx bx-play"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bx bx-calendar-check"></i>
                        </div>
                        <h4>Nenhuma OS agendada para hoje</h4>
                        <p>Você não possui ordens de serviço agendadas para hoje.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Estoque Resumo -->
        <?php if (!empty($estoque)): ?>
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-package"></i></span>
                <h5>Meu Estoque</h5>
                <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="btn btn-mini btn-success">
                    Ver Completo
                </a>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <?php
                    $count = 0;
                    foreach ($estoque as $item):
                        if ($count >= 6) break;
                    ?>
                        <div class="span2 responsive-col-6">
                            <div class="estoque-item text-center">
                                <i class="bx bx-package estoque-icon"></i>
                                <div class="estoque-nome">
                                    <?php echo htmlspecialchars($item->produto_nome ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                </div>
                                <div class="estoque-qtd">
                                    Qtd: <?php echo $item->quantidade; ?> <?php echo $item->unidade ?? ''; ?>
                                </div>
                            </div>
                        </div>
                    <?php
                        $count++;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<style>
/* Avatar do Técnico */
.tec-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #667eea;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    font-weight: 600;
    margin-right: 15px;
}

/* Cards de Estatísticas */
.stat-card .widget-content {
    padding: 20px;
}

.stat-card-success .widget-content {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.stat-card-warning .widget-content {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-card-info .widget-content {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stat-number {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
}

/* Ações Rápidas */
.quick-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
}

.btn-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    padding: 15px 25px;
    min-width: 120px;
    color: white;
    border: none;
    border-radius: 8px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: white;
    text-decoration: none;
}

.btn-action i {
    font-size: 24px;
}

.btn-action-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.btn-action-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.btn-action-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

/* Tabela de OS */
.os-number {
    font-weight: 600;
    color: #667eea;
}

/* Estado Vazio */
.empty-state {
    padding: 40px;
    text-align: center;
}

.empty-state-icon {
    font-size: 48px;
    color: #e0e0e0;
    margin-bottom: 15px;
}

.empty-state h4 {
    color: #666;
    font-weight: 400;
}

.empty-state p {
    color: #999;
}

/* Estoque */
.estoque-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
}

.estoque-icon {
    font-size: 24px;
    color: #11998e;
    margin-bottom: 8px;
}

.estoque-nome {
    font-weight: 600;
    font-size: 12px;
    margin-bottom: 4px;
    word-break: break-word;
}

.estoque-qtd {
    font-size: 11px;
    color: #888;
}

/* Responsividade Mobile */
@media (max-width: 768px) {
    .tec-avatar {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }

    .media-body h4 {
        font-size: 16px;
    }

    .stat-number {
        font-size: 28px;
    }

    .stat-label {
        font-size: 12px;
    }

    .btn-action {
        min-width: 100px;
        padding: 12px 15px;
    }

    .btn-action span {
        font-size: 12px;
    }

    .quick-actions {
        justify-content: space-around;
    }

    /* Tabela responsiva */
    .table-responsive {
        overflow-x: auto;
    }

    table td, table th {
        font-size: 12px;
    }

    /* Estoque em mobile */
    .responsive-col-6 {
        width: 48% !important;
        margin-left: 1% !important;
        margin-right: 1% !important;
        float: left !important;
    }
}

@media (max-width: 480px) {
    .span4.responsive-col {
        width: 100% !important;
        margin-left: 0 !important;
        margin-bottom: 10px;
    }

    .stat-number {
        font-size: 24px;
    }

    .btn-action {
        min-width: 90px;
        padding: 10px;
    }

    .btn-action i {
        font-size: 20px;
    }
}

/* Animações suaves */
.widget-box {
    transition: all 0.3s ease;
}

.widget-box:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Melhorias no texto */
.text-center {
    text-align: center;
}

.text-muted {
    color: #888;
}
</style>
