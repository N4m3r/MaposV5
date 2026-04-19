<!-- Dashboard Administrativo de Técnicos - Versão Moderna -->
<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-hard-hat"></i>
        </span>
        <h5>Dashboard - Gestão de Técnicos</h5>
        <div class="buttons">
            <a href="<?php echo site_url('tecnicos_admin/adicionar_tecnico'); ?>" class="button btn btn-mini btn-success">
                <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                <span class="button__text2">Novo Técnico</span>
            </a>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row-fluid" style="margin: 15px 0 25px;">
        <div class="span3">
            <div class="dashboard-stats-card primary">
                <div class="stats-icon">
                    <i class="bx bx-group"></i>
                </div>
                <div class="stats-content">
                    <span class="stats-number" data-count="<?php echo $total_tecnicos ?? 0; ?>">0</span>
                    <span class="stats-label">Técnicos Cadastrados</span>
                </div>
                <div class="stats-trend up">
                    <i class="bx bx-trending-up"></i>
                    <span>Ativos</span>
                </div>
            </div>
        </div>

        <div class="span3">
            <div class="dashboard-stats-card success">
                <div class="stats-icon">
                    <i class="bx bx-calendar-check"></i>
                </div>
                <div class="stats-content">
                    <span class="stats-number" data-count="<?php echo count($os_hoje ?? []); ?>">0</span>
                    <span class="stats-label">OS Hoje</span>
                </div>
                <div class="stats-trend">
                    <i class="bx bx-time"></i>
                    <span>Agendadas</span>
                </div>
            </div>
        </div>

        <div class="span3">
            <div class="dashboard-stats-card warning">
                <div class="stats-icon">
                    <i class="bx bx-task"></i>
                </div>
                <div class="stats-content">
                    <span class="stats-number" data-count="<?php echo $execucoes_mes['total_execucoes'] ?? 0; ?>">0</span>
                    <span class="stats-label">Execuções no Mês</span>
                </div>
                <div class="stats-trend up">
                    <i class="bx bx-trending-up"></i>
                    <span>Total</span>
                </div>
            </div>
        </div>

        <div class="span3">
            <div class="dashboard-stats-card danger">
                <div class="stats-icon">
                    <i class="bx bx-time-five"></i>
                </div>
                <div class="stats-content">
                    <span class="stats-number"><?php echo number_format($execucoes_mes['media_tempo_horas'] ?? 0, 1); ?>h</span>
                    <span class="stats-label">Tempo Médio/OS</span>
                </div>
                <div class="stats-trend down">
                    <i class="bx bx-trending-down"></i>
                    <span>Média</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Rápido Cards -->
    <div class="row-fluid" style="margin-bottom: 25px;">
        <div class="span4">
            <div class="menu-card">
                <div class="menu-card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bx bx-user-check"></i>
                    <h6>Gestão de Técnicos</h6>
                </div>
                <div class="menu-card-body">
                    <a href="<?php echo site_url('tecnicos_admin/adicionar_tecnico'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-primary">
                            <i class="bx bx-user-plus"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Cadastrar Técnico</span>
                            <span class="desc">Adicionar novo técnico</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-success">
                            <i class="bx bx-list-ul"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Listar Técnicos</span>
                            <span class="desc">Ver todos os técnicos</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-warning">
                            <i class="bx bx-package"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Estoque por Técnico</span>
                            <span class="desc">Gerenciar materiais</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="span4">
            <div class="menu-card">
                <div class="menu-card-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <i class="bx bx-clipboard"></i>
                    <h6>OS e Serviços</h6>
                </div>
                <div class="menu-card-body">
                    <a href="<?php echo site_url('os'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-success">
                            <i class="bx bx-task"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Todas as OS</span>
                            <span class="desc">Lista completa de OS</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/servicos_catalogo'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-info">
                            <i class="bx bx-cog"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Catálogo de Serviços</span>
                            <span class="desc">Tipos de serviços</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/checklists'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-purple">
                            <i class="bx bx-check-square"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Templates Checklist</span>
                            <span class="desc">Modelos de checklists</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="span4">
            <div class="menu-card">
                <div class="menu-card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="bx bx-bar-chart-alt-2"></i>
                    <h6>Relatórios e Monitoramento</h6>
                </div>
                <div class="menu-card-body">
                    <a href="<?php echo site_url('tecnicos_admin/relatorios'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-danger">
                            <i class="bx bx-line-chart"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Produtividade</span>
                            <span class="desc">Relatórios de desempenho</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/rotas'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-orange">
                            <i class="bx bx-map-alt"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Rotas dos Técnicos</span>
                            <span class="desc">Rastreamento GPS</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/execucao'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-dark">
                            <i class="bx bx-rocket"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Execução de Obras</span>
                            <span class="desc">Acompanhamento em tempo real</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/obras'); ?>" class="quick-link">
                        <div class="quick-link-icon bg-secondary">
                            <i class="bx bx-building"></i>
                        </div>
                        <div class="quick-link-text">
                            <span class="title">Gestão de Obras</span>
                            <span class="desc">Projetos e obras</span>
                        </div>
                        <i class="bx bx-chevron-right arrow"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- OS de Hoje -->
    <div class="row-fluid">
        <div class="span8">
            <div class="widget-box" style="border-radius: 12px; overflow: hidden;">
                <div class="widget-title" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <span class="icon" style="color: white;">
                        <i class="bx bx-calendar-event"></i>
                    </span>
                    <h5 style="color: white; font-weight: 600;">OS de Hoje</h5>
                    <span class="label label-info" style="margin-left: 10px;"><?php echo count($os_hoje ?? []); ?> agendada(s)</span>
                </div>
                <div class="widget-content nopadding">
                    <?php if (!empty($os_hoje)): ?>
                        <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="width: 70px; text-align: center;">OS #</th>
                                    <th>Cliente</th>
                                    <th style="width: 150px;">Técnico</th>
                                    <th style="width: 120px; text-align: center;">Status</th>
                                    <th style="width: 80px; text-align: center;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($os_hoje as $os):
                                    $statusClass = match($os->status ?? 'Aberto') {
                                        'Aberto' => ['bg' => '#e3f2fd', 'color' => '#1976d2', 'icon' => 'bx-time'],
                                        'Em Andamento' => ['bg' => '#fff3e0', 'color' => '#f57c00', 'icon' => 'bx-loader-alt'],
                                        'Finalizado' => ['bg' => '#e8f5e9', 'color' => '#388e3c', 'icon' => 'bx-check-circle'],
                                        default => ['bg' => '#f5f5f5', 'color' => '#666', 'icon' => 'bx-help-circle']
                                    };
                                ?>
                                <tr>
                                    <td style="text-align: center; font-weight: 700; color: #667eea;">
                                        #<?php echo $os->idOs; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div class="client-avatar" style="
                                                width: 32px;
                                                height: 32px;
                                                border-radius: 50%;
                                                background: linear-gradient(135deg, #667eea, #764ba2);
                                                color: white;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                font-size: 12px;
                                                font-weight: 600;
                                            ">
                                                <?php echo strtoupper(substr($os->cliente_nome ?? 'C', 0, 1)); ?>
                                            </div>
                                            <span><?php echo $os->cliente_nome ?? 'N/A'; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="display: flex; align-items: center; gap: 5px;">
                                            <i class="bx bx-hard-hat" style="color: #667eea;"></i>
                                            <?php echo $os->tecnico_nome ?? 'Não atribuído'; ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge-status" style="
                                            background: <?php echo $statusClass['bg']; ?>;
                                            color: <?php echo $statusClass['color']; ?>;
                                            padding: 5px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 4px;
                                        ">
                                            <i class="bx <?php echo $statusClass['icon']; ?>"></i>
                                            <?php echo $os->status; ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="<?php echo site_url('os/visualizar/' . $os->idOs); ?>"
                                           class="btn-action btn-view" title="Ver OS">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div style="padding: 50px; text-align: center;">
                            <div style="font-size: 60px; color: #e0e0e0; margin-bottom: 15px;">
                                <i class="bx bx-calendar-x"></i>
                            </div>
                            <h4 style="color: #666; font-weight: 400; margin-bottom: 8px;">Nenhuma OS agendada</h4>
                            <p style="color: #999; margin-bottom: 20px;">Não há ordens de serviço para hoje.</p>
                            <a href="<?php echo site_url('os/adicionar'); ?>" class="button btn btn-success">
                                <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                                <span class="button__text2">Nova OS</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="span4">
            <!-- Ações Rápidas -->
            <div class="widget-box" style="border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
                <div class="widget-title" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none;">
                    <span class="icon" style="color: white;">
                        <i class="bx bx-bolt"></i>
                    </span>
                    <h5 style="color: white; font-weight: 600;">Ações Rápidas</h5>
                </div>
                <div class="widget-content" style="padding: 15px;">
                    <div class="quick-actions-grid">
                        <a href="<?php echo site_url('os/adicionar'); ?>" class="quick-action-item">
                            <i class="bx bx-plus-circle" style="color: #667eea;"></i>
                            <span>Nova OS</span>
                        </a>
                        <a href="<?php echo site_url('tecnicos_admin/rotas'); ?>" class="quick-action-item">
                            <i class="bx bx-map" style="color: #11998e;"></i>
                            <span>Ver Rotas</span>
                        </a>
                        <a href="<?php echo site_url('tecnicos_admin/relatorios'); ?>" class="quick-action-item">
                            <i class="bx bx-pie-chart" style="color: #f57c00;"></i>
                            <span>Relatórios</span>
                        </a>
                        <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="quick-action-item">
                            <i class="bx bx-group" style="color: #c2185b;"></i>
                            <span>Técnicos</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status do Sistema -->
            <div class="widget-box" style="border-radius: 12px; overflow: hidden;">
                <div class="widget-title" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none;">
                    <span class="icon" style="color: white;">
                        <i class="bx bx-pulse"></i>
                    </span>
                    <h5 style="color: white; font-weight: 600;">Status do Sistema</h5>
                </div>
                <div class="widget-content" style="padding: 20px;">
                    <div class="status-item">
                        <div class="status-icon active">
                            <i class="bx bx-check-circle"></i>
                        </div>
                        <div class="status-info">
                            <span class="status-title">Sistema Online</span>
                            <span class="status-desc">Operacional</span>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-icon active">
                            <i class="bx bx-wifi"></i>
                        </div>
                        <div class="status-info">
                            <span class="status-title">Rastreamento</span>
                            <span class="status-desc">Ativo</span>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-icon warning">
                            <i class="bx bx-package"></i>
                        </div>
                        <div class="status-info">
                            <span class="status-title">Estoque</span>
                            <span class="status-desc">Verificar níveis</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Stats Cards */
.dashboard-stats-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.dashboard-stats-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
}

.dashboard-stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.dashboard-stats-card.primary::before { background: linear-gradient(135deg, #667eea, #764ba2); }
.dashboard-stats-card.success::before { background: linear-gradient(135deg, #11998e, #38ef7d); }
.dashboard-stats-card.warning::before { background: linear-gradient(135deg, #f093fb, #f5576c); }
.dashboard-stats-card.danger::before { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); }

.stats-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: white;
    flex-shrink: 0;
}

.dashboard-stats-card.primary .stats-icon {
    background: linear-gradient(135deg, #667eea, #764ba2);
}
.dashboard-stats-card.success .stats-icon {
    background: linear-gradient(135deg, #11998e, #38ef7d);
}
.dashboard-stats-card.warning .stats-icon {
    background: linear-gradient(135deg, #f093fb, #f5576c);
}
.dashboard-stats-card.danger .stats-icon {
    background: linear-gradient(135deg, #ff6b6b, #ee5a5a);
}

.stats-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.stats-number {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

.stats-label {
    font-size: 13px;
    color: #888;
    margin-top: 4px;
}

.stats-trend {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #888;
    padding: 4px 10px;
    background: #f5f5f5;
    border-radius: 20px;
}

.stats-trend.up {
    color: #11998e;
    background: #e8f5e9;
}

.stats-trend.down {
    color: #f5576c;
    background: #ffebee;
}

/* Menu Cards */
.menu-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.menu-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.menu-card-header {
    padding: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 12px;
}

.menu-card-header i {
    font-size: 28px;
}

.menu-card-header h6 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.menu-card-body {
    padding: 15px;
}

.quick-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 10px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
    margin-bottom: 8px;
}

.quick-link:last-child {
    margin-bottom: 0;
}

.quick-link:hover {
    background: #f8f9fa;
    text-decoration: none;
}

.quick-link-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.quick-link-icon.bg-primary { background: linear-gradient(135deg, #667eea, #764ba2); }
.quick-link-icon.bg-success { background: linear-gradient(135deg, #11998e, #38ef7d); }
.quick-link-icon.bg-info { background: linear-gradient(135deg, #4facfe, #00f2fe); }
.quick-link-icon.bg-warning { background: linear-gradient(135deg, #f093fb, #f5576c); }
.quick-link-icon.bg-purple { background: linear-gradient(135deg, #a18cd1, #fbc2eb); }
.quick-link-icon.bg-danger { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); }
.quick-link-icon.bg-orange { background: linear-gradient(135deg, #ff9a56, #ff6b6b); }
.quick-link-icon.bg-dark { background: linear-gradient(135deg, #434343, #000); }

.quick-link-text {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.quick-link-text .title {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.quick-link-text .desc {
    font-size: 12px;
    color: #888;
}

.quick-link .arrow {
    color: #ccc;
    font-size: 20px;
    transition: all 0.2s;
}

.quick-link:hover .arrow {
    color: #667eea;
    transform: translateX(3px);
}

/* Quick Actions Grid */
.quick-actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.quick-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 15px 10px;
    border-radius: 10px;
    background: #f8f9fa;
    text-decoration: none;
    transition: all 0.2s;
}

.quick-action-item:hover {
    background: #667eea;
    text-decoration: none;
}

.quick-action-item i {
    font-size: 24px;
}

.quick-action-item span {
    font-size: 12px;
    font-weight: 600;
    color: #333;
}

.quick-action-item:hover span {
    color: white;
}

.quick-action-item:hover i {
    color: white !important;
}

/* Status Items */
.status-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.status-item:last-child {
    border-bottom: none;
}

.status-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.status-icon.active {
    background: #e8f5e9;
    color: #388e3c;
}

.status-icon.warning {
    background: #fff3e0;
    color: #f57c00;
}

.status-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.status-title {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.status-desc {
    font-size: 12px;
    color: #888;
}

/* Action Buttons */
.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s;
    text-decoration: none;
    margin: 0 auto;
}

.btn-view {
    background: #e3f2fd;
    color: #1976d2;
}

.btn-view:hover {
    background: #1976d2;
    color: white;
}

/* Client Avatar */
.client-avatar {
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

/* Animations */
@keyframes countUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.stats-number {
    animation: countUp 0.5s ease forwards;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-stats-card {
        margin-bottom: 15px;
    }
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
$(document).ready(function() {
    // Animate numbers counting up
    $('.stats-number[data-count]').each(function() {
        const $this = $(this);
        const countTo = parseInt($this.attr('data-count'));
        let countNum = 0;
        const duration = 1000;
        const increment = countTo / (duration / 16);

        const timer = setInterval(function() {
            countNum += increment;
            if (countNum >= countTo) {
                countNum = countTo;
                clearInterval(timer);
            }
            $this.text(Math.floor(countNum));
        }, 16);
    });

    // Add stagger animation to menu cards
    $('.menu-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
        $(this).addClass('fade-in-up');
    });

    // Table row hover effect
    $('table tbody tr').hover(
        function() { $(this).addClass('row-highlight'); },
        function() { $(this).removeClass('row-highlight'); }
    );
});
</script>
