<?php
// Dashboard Administrativo de Técnicos - Versão Moderna e Intuitiva
// Sem obras - Foco em gestão de técnicos e OS
?>

<!-- Header Moderno -->
<div class="dashboard-header">
    <div class="header-content">
        <div class="header-title">
            <div class="header-icon">
                <i class='bx bx-hard-hat'></i>
            </div>
            <div class="header-text">
                <h1>Gestão de Técnicos</h1>
                <p>Controle completo da sua equipe técnica</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="<?php echo site_url('tecnicos_admin/adicionar_tecnico'); ?>" class="btn-primary-modern">
                <i class='bx bx-plus'></i>
                <span>Novo Técnico</span>
            </a>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="stats-grid-modern">
    <div class="stat-card-modern primary">
        <div class="stat-header">
            <div class="stat-icon-box">
                <i class='bx bx-group'></i>
            </div>
            <div class="stat-badge">Ativos</div>
        </div>
        <div class="stat-body">
            <h3 class="stat-number" data-count="<?php echo $total_tecnicos ?? 0; ?>">0</h3>
            <p class="stat-label">Técnicos Cadastrados</p>
        </div>
        <div class="stat-footer">
            <i class='bx bx-trending-up'></i>
            <span>Equipe completa</span>
        </div>
    </div>

    <div class="stat-card-modern success">
        <div class="stat-header">
            <div class="stat-icon-box">
                <i class='bx bx-calendar-check'></i>
            </div>
            <div class="stat-badge">Hoje</div>
        </div>
        <div class="stat-body">
            <h3 class="stat-number" data-count="<?php echo count($os_hoje ?? []); ?>">0</h3>
            <p class="stat-label">OS Agendadas</p>
        </div>
        <div class="stat-footer">
            <i class='bx bx-time'></i>
            <span>Em andamento</span>
        </div>
    </div>

    <div class="stat-card-modern warning">
        <div class="stat-header">
            <div class="stat-icon-box">
                <i class='bx bx-task'></i>
            </div>
            <div class="stat-badge">Mês</div>
        </div>
        <div class="stat-body">
            <h3 class="stat-number" data-count="<?php echo $execucoes_mes['total_execucoes'] ?? 0; ?>">0</h3>
            <p class="stat-label">Execuções Realizadas</p>
        </div>
        <div class="stat-footer">
            <i class='bx bx-check-circle'></i>
            <span>Concluídas</span>
        </div>
    </div>

    <div class="stat-card-modern info">
        <div class="stat-header">
            <div class="stat-icon-box">
                <i class='bx bx-time-five'></i>
            </div>
            <div class="stat-badge">Média</div>
        </div>
        <div class="stat-body">
            <h3 class="stat-number-text"><?php echo number_format($execucoes_mes['media_tempo_horas'] ?? 0, 1); ?>h</h3>
            <p class="stat-label">Tempo Médio por OS</p>
        </div>
        <div class="stat-footer">
            <i class='bx bx-stopwatch'></i>
            <span>Por atendimento</span>
        </div>
    </div>
</div>

<!-- Menu Principal -->
<div class="main-menu-grid">
    <!-- Card: Gestão de Técnicos -->
    <div class="menu-card-modern">
        <div class="menu-card-header accent-purple">
            <div class="menu-icon">
                <i class='bx bx-user-check'></i>
            </div>
            <h3>Gestão de Técnicos</h3>
            <p>Gerencie sua equipe técnica</p>
        </div>
        <div class="menu-card-body">
            <a href="<?php echo site_url('tecnicos_admin/adicionar_tecnico'); ?>" class="menu-item">
                <div class="item-icon bg-purple">
                    <i class='bx bx-user-plus'></i>
                </div>
                <div class="item-content">
                    <span class="item-title">Cadastrar Técnico</span>
                    <span class="item-desc">Adicionar novo profissional</span>
                </div>
                <i class='bx bx-chevron-right'></i>
            </a>
            <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="menu-item">
                <div class="item-icon bg-green">
                    <i class='bx bx-list-ul'></i>
                </div>
                <div class="item-content">
                    <span class="item-title">Listar Técnicos</span>
                    <span class="item-desc">Ver todos os técnicos</span>
                </div>
                <i class='bx bx-chevron-right'></i>
            </a>
            <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="menu-item">
                <div class="item-icon bg-orange">
                    <i class='bx bx-package'></i>
                </div>
                <div class="item-content">
                    <span class="item-title">Estoque por Técnico</span>
                    <span class="item-desc">Materiais e equipamentos</span>
                </div>
                <i class='bx bx-chevron-right'></i>
            </a>
        </div>
    </div>

    <!-- Card: OS e Serviços -->
    <div class="menu-card-modern">
        <div class="menu-card-header accent-teal">
            <div class="menu-icon">
                <i class='bx bx-clipboard'></i>
            </div>
            <h3>OS e Serviços</h3>
            <p>Controle de ordens de serviço</p>
        </div>
        <div class="menu-card-body">
            <a href="<?php echo site_url('os'); ?>" class="menu-item">
                <div class="item-icon bg-teal">
                    <i class='bx bx-task'></i>
                </div>
                <div class="item-content">
                    <span class="item-title">Todas as OS</span>
                    <span class="item-desc">Lista completa de ordens</span>
                </div>
                <i class='bx bx-chevron-right'></i>
            </a>
            <a href="<?php echo site_url('tecnicos_admin/servicos_catalogo'); ?>" class="menu-item">
                <div class="item-icon bg-blue">
                    <i class='bx bx-cog'></i>
                </div>
                <div class="item-content">
                    <span class="item-title">Catálogo de Serviços</span>
                    <span class="item-desc">Tipos de serviços</span>
                </div>
                <i class='bx bx-chevron-right'></i>
            </a>
            <a href="<?php echo site_url('tecnicos_admin/checklists'); ?>" class="menu-item">
                <div class="item-icon bg-pink">
                    <i class='bx bx-check-square'></i>
                </div>
                <div class="item-content">
                    <span class="item-title">Templates Checklist</span>
                    <span class="item-desc">Modelos de verificação</span>
                </div>
                <i class='bx bx-chevron-right'></i>
            </a>
        </div>
    </div>

    <!-- Card: Relatórios e Monitoramento -->
    <div class="menu-card-modern">
        <div class="menu-card-header accent-coral">
            <div class="menu-icon">
                <i class='bx bx-bar-chart-alt-2'></i>
            </div>
            <h3>Relatórios</h3>
            <p>Análise e acompanhamento</p>
        </div>
        <div class="menu-card-body">
            <a href="<?php echo site_url('tecnicos_admin/relatorios'); ?>" class="menu-item">
                <div class="item-icon bg-coral">
                    <i class='bx bx-line-chart'></i>
                </div>
                <div class="item-content">
                    <span class="item-title">Produtividade</span>
                    <span class="item-desc">Relatórios de desempenho</span>
                </div>
                <i class='bx bx-chevron-right'></i>
            </a>
            <a href="<?php echo site_url('tecnicos_admin/rotas'); ?>" class="menu-item">
                <div class="item-icon bg-indigo">
                    <i class='bx bx-map-alt'></i>
                </div>
                <div class="item-content">
                    <span class="item-title">Rotas dos Técnicos</span>
                    <span class="item-desc">Rastreamento e locais</span>
                </div>
                <i class='bx bx-chevron-right'></i>
            </a>
            <a href="<?php echo site_url('relatoriotecnicos'); ?>" class="menu-item">
                <div class="item-icon bg-yellow">
                    <i class='bx bx-file-report'></i>
                </div>
                <div class="item-content">
                    <span class="item-title">Relatório Completo</span>
                    <span class="item-desc">Visão geral da equipe</span>
                </div>
                <i class='bx bx-chevron-right'></i>
            </a>
        </div>
    </div>
</div>

<!-- Seção: OS de Hoje -->
<div class="section-modern">
    <div class="section-header">
        <div class="section-title">
            <i class='bx bx-calendar-event'></i>
            <h2>OS de Hoje</h2>
            <span class="badge-count"><?php echo count($os_hoje ?? []); ?> agendada(s)</span>
        </div>
        <a href="<?php echo site_url('os'); ?>" class="section-action">
            Ver todas <i class='bx bx-arrow-right'></i>
        </a>
    </div>

    <div class="section-content">
        <?php if (!empty($os_hoje)): ?>
            <div class="os-grid">
                <?php foreach ($os_hoje as $os):
                    $status = $os->status ?? 'Aberto';
                    switch($status) {
                        case 'Aberto':
                            $statusClass = ['bg' => '#e3f2fd', 'color' => '#1976d2', 'icon' => 'bx-time', 'label' => 'Aberto'];
                            break;
                        case 'Em Andamento':
                            $statusClass = ['bg' => '#fff3e0', 'color' => '#f57c00', 'icon' => 'bx-loader-alt', 'label' => 'Em Andamento'];
                            break;
                        case 'Finalizado':
                        case 'Finalizada':
                            $statusClass = ['bg' => '#e8f5e9', 'color' => '#388e3c', 'icon' => 'bx-check-circle', 'label' => 'Finalizado'];
                            break;
                        default:
                            $statusClass = ['bg' => '#f5f5f5', 'color' => '#666', 'icon' => 'bx-help-circle', 'label' => $status];
                    }
                ?>
                <div class="os-card">
                    <div class="os-card-header" style="background: <?php echo $statusClass['bg']; ?>;">
                        <div class="os-number">#<?php echo $os->idOs; ?></div>
                        <div class="os-status" style="color: <?php echo $statusClass['color']; ?>;">
                            <i class='bx <?php echo $statusClass['icon']; ?>'></i>
                            <span><?php echo $statusClass['label']; ?></span>
                        </div>
                    </div>
                    <div class="os-card-body">
                        <div class="os-client">
                            <div class="client-avatar-small">
                                <?php echo strtoupper(substr($os->cliente_nome ?? 'C', 0, 1)); ?>
                            </div>
                            <span class="client-name"><?php echo $os->cliente_nome ?? 'N/A'; ?></span>
                        </div>
                        <div class="os-technician">
                            <i class='bx bx-hard-hat'></i>
                            <span><?php echo $os->tecnico_nome ?? 'Não atribuído'; ?></span>
                        </div>
                    </div>
                    <div class="os-card-footer">
                        <a href="<?php echo site_url('os/visualizar/' . $os->idOs); ?>" class="btn-view-os">
                            <i class='bx bx-show'></i>
                            Ver detalhes
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state-modern">
                <div class="empty-icon">
                    <i class='bx bx-calendar-x'></i>
                </div>
                <h3>Nenhuma OS agendada</h3>
                <p>Não há ordens de serviço programadas para hoje.</p>
                <a href="<?php echo site_url('os/adicionar'); ?>" class="btn-primary-modern">
                    <i class='bx bx-plus'></i>
                    Nova OS
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="quick-actions-section">
    <h3 class="quick-actions-title">
        <i class='bx bx-bolt'></i>
        Ações Rápidas
    </h3>
    <div class="quick-actions-grid-modern">
        <a href="<?php echo site_url('os/adicionar'); ?>" class="quick-action-card">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <i class='bx bx-plus-circle'></i>
            </div>
            <span class="quick-action-label">Nova OS</span>
        </a>
        <a href="<?php echo site_url('tecnicos_admin/rotas'); ?>" class="quick-action-card">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                <i class='bx bx-map'></i>
            </div>
            <span class="quick-action-label">Ver Rotas</span>
        </a>
        <a href="<?php echo site_url('tecnicos_admin/relatorios'); ?>" class="quick-action-card">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                <i class='bx bx-pie-chart'></i>
            </div>
            <span class="quick-action-label">Relatórios</span>
        </a>
        <a href="<?php echo site_url('tecnicos_admin/checklists'); ?>" class="quick-action-card">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                <i class='bx bx-check-shield'></i>
            </div>
            <span class="quick-action-label">Checklists</span>
        </a>
    </div>
</div>

<style>
/* Header Moderno */
.dashboard-header {
    margin: -20px -20px 30px -20px;
    padding: 30px 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.dashboard-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
}

.header-title {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
    backdrop-filter: blur(10px);
}

.header-text h1 {
    margin: 0;
    color: white;
    font-size: 28px;
    font-weight: 700;
}

.header-text p {
    margin: 5px 0 0;
    color: rgba(255,255,255,0.8);
    font-size: 14px;
}

.btn-primary-modern {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: white;
    color: #667eea;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    text-decoration: none;
    color: #667eea;
}

.btn-primary-modern i {
    font-size: 20px;
}

/* Stats Grid Moderno */
.stats-grid-modern {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card-modern {
    background: white;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-card-modern.primary::before { background: linear-gradient(90deg, #667eea, #764ba2); }
.stat-card-modern.success::before { background: linear-gradient(90deg, #11998e, #38ef7d); }
.stat-card-modern.warning::before { background: linear-gradient(90deg, #f093fb, #f5576c); }
.stat-card-modern.info::before { background: linear-gradient(90deg, #4facfe, #00f2fe); }

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.stat-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-card-modern.primary .stat-icon-box { background: linear-gradient(135deg, #667eea, #764ba2); }
.stat-card-modern.success .stat-icon-box { background: linear-gradient(135deg, #11998e, #38ef7d); }
.stat-card-modern.warning .stat-icon-box { background: linear-gradient(135deg, #f093fb, #f5576c); }
.stat-card-modern.info .stat-icon-box { background: linear-gradient(135deg, #4facfe, #00f2fe); }

.stat-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card-modern.primary .stat-badge { background: #ede9fe; color: #764ba2; }
.stat-card-modern.success .stat-badge { background: #d1fae5; color: #059669; }
.stat-card-modern.warning .stat-badge { background: #fce7f3; color: #db2777; }
.stat-card-modern.info .stat-badge { background: #dbeafe; color: #2563eb; }

.stat-body {
    margin-bottom: 16px;
}

.stat-number, .stat-number-text {
    font-size: 36px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    line-height: 1;
}

.stat-label {
    margin: 8px 0 0;
    color: #6b7280;
    font-size: 14px;
}

.stat-footer {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6b7280;
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
}

.stat-footer i {
    font-size: 16px;
}

/* Menu Principal */
.main-menu-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 30px;
}

.menu-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.menu-card-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}

.menu-card-header {
    padding: 24px;
    color: white;
    position: relative;
}

.menu-card-header.accent-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.menu-card-header.accent-teal {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.menu-card-header.accent-coral {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
}

.menu-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 16px;
}

.menu-card-header h3 {
    margin: 0 0 4px;
    font-size: 18px;
    font-weight: 600;
}

.menu-card-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 13px;
}

.menu-card-body {
    padding: 16px;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px;
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
    margin-bottom: 8px;
}

.menu-item:last-child {
    margin-bottom: 0;
}

.menu-item:hover {
    background: #f9fafb;
    text-decoration: none;
}

.item-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.bg-purple { background: linear-gradient(135deg, #667eea, #764ba2); }
.bg-green { background: linear-gradient(135deg, #11998e, #38ef7d); }
.bg-orange { background: linear-gradient(135deg, #ff9a56, #ff6b6b); }
.bg-teal { background: linear-gradient(135deg, #11998e, #38ef7d); }
.bg-blue { background: linear-gradient(135deg, #4facfe, #00f2fe); }
.bg-pink { background: linear-gradient(135deg, #f093fb, #f5576c); }
.bg-coral { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); }
.bg-indigo { background: linear-gradient(135deg, #667eea, #764ba2); }
.bg-yellow { background: linear-gradient(135deg, #fbbf24, #f59e0b); }

.item-content {
    flex: 1;
}

.item-title {
    display: block;
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
}

.item-desc {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-top: 2px;
}

.menu-item > i.bx-chevron-right {
    color: #d1d5db;
    font-size: 20px;
    transition: all 0.2s;
}

.menu-item:hover > i.bx-chevron-right {
    color: #667eea;
    transform: translateX(3px);
}

/* Seção Moderna */
.section-modern {
    background: white;
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f3f4f6;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title i {
    font-size: 24px;
    color: #667eea;
}

.section-title h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.badge-count {
    padding: 4px 12px;
    background: #ede9fe;
    color: #764ba2;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.section-action {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #667eea;
    font-weight: 500;
    font-size: 14px;
    text-decoration: none;
}

.section-action:hover {
    text-decoration: none;
    color: #764ba2;
}

/* OS Grid */
.os-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

.os-card {
    background: #f9fafb;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.os-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.os-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.os-number {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
}

.os-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
    background: white;
}

.os-card-body {
    padding: 16px;
}

.os-client {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.client-avatar-small {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.client-name {
    font-weight: 500;
    color: #374151;
    font-size: 14px;
}

.os-technician {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6b7280;
    font-size: 13px;
}

.os-technician i {
    color: #667eea;
}

.os-card-footer {
    padding: 12px 16px;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.btn-view-os {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px;
    background: white;
    border-radius: 10px;
    color: #667eea;
    font-weight: 500;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-view-os:hover {
    background: #667eea;
    color: white;
    text-decoration: none;
}

/* Empty State Moderno */
.empty-state-modern {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 80px;
    color: #e5e7eb;
    margin-bottom: 20px;
}

.empty-state-modern h3 {
    margin: 0 0 8px;
    color: #374151;
    font-size: 20px;
    font-weight: 600;
}

.empty-state-modern p {
    margin: 0 0 24px;
    color: #9ca3af;
}

/* Ações Rápidas */
.quick-actions-section {
    margin-bottom: 30px;
}

.quick-actions-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 20px;
    font-size: 16px;
    font-weight: 600;
    color: #374151;
}

.quick-actions-title i {
    color: #f59e0b;
    font-size: 20px;
}

.quick-actions-grid-modern {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.quick-action-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.quick-action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    text-decoration: none;
}

.quick-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    color: white;
    font-size: 28px;
    transition: transform 0.3s ease;
}

.quick-action-card:hover .quick-action-icon {
    transform: scale(1.1);
}

.quick-action-label {
    display: block;
    color: #374151;
    font-weight: 600;
    font-size: 14px;
}

/* Responsividade */
@media (max-width: 1200px) {
    .stats-grid-modern {
        grid-template-columns: repeat(2, 1fr);
    }
    .main-menu-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        padding: 24px;
    }
    .header-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    .stats-grid-modern {
        grid-template-columns: 1fr;
    }
    .main-menu-grid {
        grid-template-columns: 1fr;
    }
    .quick-actions-grid-modern {
        grid-template-columns: repeat(2, 1fr);
    }
    .os-grid {
        grid-template-columns: 1fr;
    }
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card-modern, .menu-card-modern, .os-card {
    animation: fadeInUp 0.5s ease forwards;
}

.stat-card-modern:nth-child(1) { animation-delay: 0.1s; }
.stat-card-modern:nth-child(2) { animation-delay: 0.2s; }
.stat-card-modern:nth-child(3) { animation-delay: 0.3s; }
.stat-card-modern:nth-child(4) { animation-delay: 0.4s; }
</style>

<script>
$(document).ready(function() {
    // Animate numbers counting up
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = value;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Animate stat numbers
    document.querySelectorAll('.stat-number[data-count]').forEach(function(el) {
        const countTo = parseInt(el.getAttribute('data-count'));
        animateValue(el, 0, countTo, 1500);
    });

    // Add hover effect to cards
    $('.stat-card-modern, .menu-card-modern, .os-card').hover(
        function() { $(this).addClass('hovered'); },
        function() { $(this).removeClass('hovered'); }
    );
});
</script>
