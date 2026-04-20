<link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet">

<style>
.dashboard-container {
    padding: 20px;
}

/* Header do Dashboard */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.dashboard-title {
    font-size: 1.6rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 12px;
}

.dashboard-title i {
    color: #667eea;
    font-size: 2rem;
}

.dashboard-actions {
    display: flex;
    gap: 10px;
}

.btn-action {
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: white;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.btn-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.btn-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

/* KPIs Cards */
.kpi-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kpi-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.kpi-card.warning { border-left-color: #f093fb; }
.kpi-card.success { border-left-color: #11998e; }
.kpi-card.info { border-left-color: #4facfe; }
.kpi-card.danger { border-left-color: #f5576c; }

.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.kpi-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.kpi-card.warning .kpi-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.kpi-card.success .kpi-icon { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.kpi-card.info .kpi-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.kpi-card.danger .kpi-icon { background: linear-gradient(135deg, #f5576c 0%, #ff9a9e 100%); }

.kpi-trend {
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 600;
}

.kpi-trend.up {
    background: rgba(17, 153, 142, 0.1);
    color: #11998e;
}

.kpi-trend.down {
    background: rgba(245, 87, 108, 0.1);
    color: #f5576c;
}

.kpi-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 5px 0;
}

.kpi-label {
    font-size: 0.9rem;
    color: #7f8c8d;
}

/* Acesso Rápido - Menu Principal */
.quick-access {
    margin-bottom: 30px;
}

.quick-access-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.quick-access-title i {
    color: #667eea;
}

.quick-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.quick-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    display: block;
}

.quick-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.quick-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.quick-card:hover::before {
    transform: scaleX(1);
}

.quick-card-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 12px;
}

.quick-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    color: white;
    flex-shrink: 0;
}

.quick-card-icon.os { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.quick-card-icon.clientes { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.quick-card-icon.produtos { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.quick-card-icon.servicos { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.quick-card-icon.vendas { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.quick-card-icon.financeiro { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
.quick-card-icon.relatorios { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
.quick-card-icon.config { background: linear-gradient(135deg, #434343 0%, #000000 100%); }
.quick-card-icon.tecnico { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%); }
.quick-card-icon.obras { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.quick-card-icon.cobrancas { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); }
.quick-card-icon.estoque { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); }

.quick-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

.quick-card-desc {
    font-size: 0.85rem;
    color: #7f8c8d;
    margin-bottom: 15px;
}

.quick-card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.quick-action-btn {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
}

.quick-action-btn.primary {
    background: #667eea;
    color: white;
}

.quick-action-btn.primary:hover {
    background: #5568d3;
}

.quick-action-btn.secondary {
    background: #f8f9fa;
    color: #6c757d;
    border: 1px solid #e0e0e0;
}

.quick-action-btn.secondary:hover {
    background: #e9ecef;
}

/* Seções de Gráficos */
.charts-section {
    margin-top: 30px;
}

.charts-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.chart-container {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.chart-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-title i {
    color: #667eea;
    font-size: 1.2rem;
}

.chart-canvas {
    max-height: 280px;
}

/* Atividades Recentes */
.activities-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-top: 20px;
}

.activities-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.activities-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
}

.activity-item {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f5f5f5;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #667eea;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-text {
    font-size: 0.9rem;
    color: #2c3e50;
    margin-bottom: 4px;
}

.activity-text strong {
    color: #667eea;
}

.activity-time {
    font-size: 0.8rem;
    color: #95a5a6;
}

/* Filtros */
.filters-bar {
    background: white;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: center;
}

.filters-bar label {
    font-weight: 500;
    color: #2c3e50;
}

.filters-bar select,
.filters-bar input {
    padding: 8px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.9rem;
}

.btn-filter {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-filter:hover {
    opacity: 0.9;
}

/* Loading */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsivo */
@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .kpi-cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .quick-grid {
        grid-template-columns: 1fr;
    }

    .charts-row {
        grid-template-columns: 1fr;
    }

    .filters-bar {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="dashboard-title">
            <i class="bx bxs-dashboard"></i>
            Dashboard - Visão Geral
        </div>
        <div class="dashboard-actions">
            <button class="btn-action btn-primary" onclick="carregarDados()">
                <i class="bx bx-refresh"></i> Atualizar
            </button>
            <a href="<?= site_url('os/adicionar') ?>" class="btn-action btn-success">
                <i class="bx bx-plus-circle"></i> Nova OS
            </a>
            <button class="btn-action btn-info" onclick="exportarDados()">
                <i class="bx bx-download"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-bar">
        <label><i class="bx bx-calendar"></i> Período:</label>
        <select id="filtro-periodo" onchange="carregarDados()">
            <option value="hoje">Hoje</option>
            <option value="semana">Esta Semana</option>
            <option value="mes" selected>Este Mês</option>
            <option value="ano">Este Ano</option>
        </select>
        <input type="date" id="data-inicio" style="display:none;">
        <input type="date" id="data-fim" style="display:none;">
    </div>

    <!-- KPIs -->
    <div class="kpi-cards">
        <div class="kpi-card">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="bx bx-file"></i></div>
            </div>
            <div class="kpi-value" id="kpi-total-os">-</div>
            <div class="kpi-label">Total de OS</div>
        </div>

        <div class="kpi-card warning">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="bx bx-time"></i></div>
            </div>
            <div class="kpi-value" id="kpi-os-pendentes">-</div>
            <div class="kpi-label">OS Pendentes</div>
        </div>

        <div class="kpi-card success">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="bx bx-check-circle"></i></div>
            </div>
            <div class="kpi-value" id="kpi-os-finalizadas">-</div>
            <div class="kpi-label">OS Finalizadas</div>
        </div>

        <div class="kpi-card info">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="bx bx-dollar-circle"></i></div>
            </div>
            <div class="kpi-value" id="kpi-valor-faturado">-</div>
            <div class="kpi-label">Valor Faturado</div>
        </div>

        <div class="kpi-card danger">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="bx bx-receipt"></i></div>
            </div>
            <div class="kpi-value" id="kpi-ticket-medio">-</div>
            <div class="kpi-label">Ticket Médio</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="bx bx-user-plus"></i></div>
            </div>
            <div class="kpi-value" id="kpi-novos-clientes">-</div>
            <div class="kpi-label">Novos Clientes</div>
        </div>

        <div class="kpi-card" style="border-left-color: #ff6b6b;">
            <div class="kpi-header">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);">
                    <i class="bx bx-hard-hat"></i>
                </div>
            </div>
            <div class="kpi-value" id="kpi-tecnicos-ativos">-</div>
            <div class="kpi-label">Técnicos Ativos</div>
        </div>
    </div>

    <!-- Acesso Rápido - Menu Principal -->
    <div class="quick-access">
        <div class="quick-access-title">
            <i class="bx bx-rocket"></i>
            Acesso Rápido - Funcionalidades
        </div>

        <div class="quick-grid">
            <!-- Ordens de Serviço -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon os"><i class="bx bx-clipboard"></i></div>
                    <div class="quick-card-title">Ordens de Serviço</div>
                </div>
                <div class="quick-card-desc">Gerencie todas as ordens de serviço do sistema</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('os') ?>" class="quick-action-btn primary">
                        <i class="bx bx-list-ul"></i> Listar
                    </a>
                    <a href="<?= site_url('os/adicionar') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-plus"></i> Nova
                    </a>
                </div>
            </div>

            <!-- Clientes -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon clientes"><i class="bx bx-group"></i></div>
                    <div class="quick-card-title">Clientes</div>
                </div>
                <div class="quick-card-desc">Cadastro e gestão de clientes</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('clientes') ?>" class="quick-action-btn primary">
                        <i class="bx bx-list-ul"></i> Listar
                    </a>
                    <a href="<?= site_url('clientes/adicionar') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-plus"></i> Novo
                    </a>
                </div>
            </div>

            <!-- Produtos -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon produtos"><i class="bx bx-package"></i></div>
                    <div class="quick-card-title">Produtos</div>
                </div>
                <div class="quick-card-desc">Controle de produtos e estoque</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('produtos') ?>" class="quick-action-btn primary">
                        <i class="bx bx-list-ul"></i> Listar
                    </a>
                    <a href="<?= site_url('produtos/adicionar') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-plus"></i> Novo
                    </a>
                </div>
            </div>

            <!-- Serviços -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon servicos"><i class="bx bx-wrench"></i></div>
                    <div class="quick-card-title">Serviços</div>
                </div>
                <div class="quick-card-desc">Cadastro de serviços prestados</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('servicos') ?>" class="quick-action-btn primary">
                        <i class="bx bx-list-ul"></i> Listar
                    </a>
                    <a href="<?= site_url('servicos/adicionar') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-plus"></i> Novo
                    </a>
                </div>
            </div>

            <!-- Vendas -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon vendas"><i class="bx bx-cart"></i></div>
                    <div class="quick-card-title">Vendas</div>
                </div>
                <div class="quick-card-desc">Controle de vendas e orçamentos</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('vendas') ?>" class="quick-action-btn primary">
                        <i class="bx bx-list-ul"></i> Listar
                    </a>
                    <a href="<?= site_url('vendas/adicionar') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-plus"></i> Nova
                    </a>
                </div>
            </div>

            <!-- Obras -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon obras"><i class="bx bx-building-house"></i></div>
                    <div class="quick-card-title">Obras</div>
                </div>
                <div class="quick-card-desc">Gerenciamento de obras e projetos</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('obras') ?>" class="quick-action-btn primary">
                        <i class="bx bx-list-ul"></i> Listar
                    </a>
                    <a href="<?= site_url('obras/adicionar') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-plus"></i> Nova
                    </a>
                </div>
            </div>

            <!-- Técnicos -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon tecnico"><i class="bx bx-hard-hat"></i></div>
                    <div class="quick-card-title">Técnicos</div>
                </div>
                <div class="quick-card-desc">Gestão de equipe técnica</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('tecnicos_admin') ?>" class="quick-action-btn primary">
                        <i class="bx bx-list-ul"></i> Painel
                    </a>
                    <a href="<?= site_url('tecnicos_admin/execucao') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-run"></i> Execuções
                    </a>
                </div>
            </div>

            <!-- Financeiro -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon financeiro"><i class="bx bx-dollar-circle"></i></div>
                    <div class="quick-card-title">Financeiro</div>
                </div>
                <div class="quick-card-desc">Controle financeiro e cobranças</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('financeiro') ?>" class="quick-action-btn primary">
                        <i class="bx bx-chart"></i> Dashboard
                    </a>
                    <a href="<?= site_url('cobrancas') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-credit-card"></i> Cobranças
                    </a>
                </div>
            </div>

            <!-- Relatórios -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon relatorios"><i class="bx bx-pie-chart-alt-2"></i></div>
                    <div class="quick-card-title">Relatórios</div>
                </div>
                <div class="quick-card-desc">Relatórios e análises do sistema</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('relatorios') ?>" class="quick-action-btn primary">
                        <i class="bx bx-file"></i> Geral
                    </a>
                    <a href="<?= site_url('dre') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-line-chart"></i> DRE
                    </a>
                </div>
            </div>

            <!-- Configurações -->
            <div class="quick-card">
                <div class="quick-card-header">
                    <div class="quick-card-icon config"><i class="bx bx-cog"></i></div>
                    <div class="quick-card-title">Configurações</div>
                </div>
                <div class="quick-card-desc">Configurações do sistema e emitente</div>
                <div class="quick-card-actions">
                    <a href="<?= site_url('mapos/configurar') ?>" class="quick-action-btn primary">
                        <i class="bx bx-sliders"></i> Sistema
                    </a>
                    <a href="<?= site_url('usuarios') ?>" class="quick-action-btn secondary">
                        <i class="bx bx-user"></i> Usuários
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-section">
        <div class="charts-row">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="bx bx-pie-chart-alt-2"></i>
                    OS por Status
                </div>
                <canvas id="chart-os-status" class="chart-canvas"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">
                    <i class="bx bx-line-chart"></i>
                    OS por Período
                </div>
                <canvas id="chart-os-mes" class="chart-canvas"></canvas>
            </div>
        </div>

        <div class="charts-row">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="bx bx-bar-chart-alt-2"></i>
                    Faturamento Mensal
                </div>
                <canvas id="chart-faturamento" class="chart-canvas"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">
                    <i class="bx bx-user-check"></i>
                    OS por Técnico
                </div>
                <canvas id="chart-por-tecnico" class="chart-canvas"></canvas>
            </div>
        </div>
    </div>

    <!-- Atividades Recentes -->
    <div class="activities-section">
        <div class="activities-header">
            <div class="activities-title">
                <i class="bx bx-time-five"></i>
                Atividades Recentes
            </div>
            <a href="<?= site_url('auditoria') ?>" class="quick-action-btn secondary">
                Ver Todas
            </a>
        </div>
        <div id="atividades-recentes">
            <div class="activity-item">
                <div class="activity-icon"><i class="bx bx-refresh"></i></div>
                <div class="activity-content">
                    <div class="activity-text">Carregando atividades...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner"></div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Variáveis globais para os gráficos
let charts = {};

// Configurações padrão do Chart.js
Chart.defaults.font.family = "'Segoe UI', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
Chart.defaults.color = '#666';

$(document).ready(function() {
    carregarDados();
    carregarAtividades();
});

function showLoading() {
    $('#loading-overlay').show();
}

function hideLoading() {
    $('#loading-overlay').hide();
}

function carregarDados() {
    showLoading();

    const periodo = $('#filtro-periodo').val();

    $.ajax({
        url: '<?= site_url("dashboard/dadosGraficos") ?>',
        type: 'GET',
        data: { periodo: periodo },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                atualizarKPIs(response.data.kpi);
                criarGraficoStatus(response.data.os_por_status);
                criarGraficoMes(response.data.os_por_mes);
                criarGraficoFaturamento(response.data.faturamento_mensal);
                criarGraficoTecnico(response.data.por_tecnico);
                if (response.data.top_produtos) {
                    criarGraficoProdutos(response.data.top_produtos);
                }
                if (response.data.top_servicos) {
                    criarGraficoServicos(response.data.top_servicos);
                }
            }
            hideLoading();
        },
        error: function() {
            console.error('Erro ao carregar dados do dashboard');
            hideLoading();
        }
    });
}

function carregarAtividades() {
    $.ajax({
        url: '<?= site_url("auditoria/ultimas") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.length > 0) {
                let html = '';
                response.slice(0, 5).forEach(function(item) {
                    html += `
                        <div class="activity-item">
                            <div class="activity-icon"><i class="bx ${item.icone || 'bx-info-circle'}"></i></div>
                            <div class="activity-content">
                                <div class="activity-text">${item.descricao}</div>
                                <div class="activity-time">${item.data} às ${item.hora}</div>
                            </div>
                        </div>
                    `;
                });
                $('#atividades-recentes').html(html);
            } else {
                $('#atividades-recentes').html('<div class="activity-item"><div class="activity-content"><div class="activity-text">Nenhuma atividade recente</div></div></div>');
            }
        },
        error: function() {
            $('#atividades-recentes').html('<div class="activity-item"><div class="activity-content"><div class="activity-text">Erro ao carregar atividades</div></div></div>');
        }
    });
}

function atualizarKPIs(kpi) {
    $('#kpi-total-os').text(kpi.total_os || '0');
    $('#kpi-os-pendentes').text(kpi.os_pendentes || '0');
    $('#kpi-os-finalizadas').text(kpi.os_finalizadas || '0');
    $('#kpi-valor-faturado').text('R$ ' + formatarValor(kpi.valor_faturado || 0));
    $('#kpi-ticket-medio').text('R$ ' + formatarValor(kpi.ticket_medio || 0));
    $('#kpi-novos-clientes').text(kpi.novos_clientes || '0');
    $('#kpi-tecnicos-ativos').text(kpi.tecnicos_ativos || '-');
}

function formatarValor(valor) {
    return parseFloat(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function criarGraficoStatus(dados) {
    if (!dados || dados.length === 0) return;
    const ctx = document.getElementById('chart-os-status').getContext('2d');

    if (charts.status) charts.status.destroy();

    const labels = dados.map(d => d.status);
    const valores = dados.map(d => d.total);
    const cores = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#11998e', '#fa709a', '#30cfd0'];

    charts.status = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: valores,
                backgroundColor: cores,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function criarGraficoMes(dados) {
    if (!dados || dados.length === 0) return;
    const ctx = document.getElementById('chart-os-mes').getContext('2d');

    if (charts.mes) charts.mes.destroy();

    const labels = dados.map(d => {
        const [ano, mes] = d.mes.split('-');
        return `${mes}/${ano}`;
    });
    const valores = dados.map(d => d.total);

    charts.mes = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Quantidade de OS',
                data: valores,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function criarGraficoFaturamento(dados) {
    if (!dados || dados.length === 0) return;
    const ctx = document.getElementById('chart-faturamento').getContext('2d');

    if (charts.faturamento) charts.faturamento.destroy();

    const labels = dados.map(d => {
        const [ano, mes] = d.mes.split('-');
        return `${mes}/${ano}`;
    });
    const valores = dados.map(d => d.total);

    charts.faturamento = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Faturamento (R$)',
                data: valores,
                backgroundColor: 'rgba(17, 153, 142, 0.8)',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toFixed(0);
                        }
                    }
                }
            }
        }
    });
}

function criarGraficoTecnico(dados) {
    if (!dados || dados.length === 0) return;
    const ctx = document.getElementById('chart-por-tecnico').getContext('2d');

    if (charts.tecnico) charts.tecnico.destroy();

    const labels = dados.map(d => d.tecnico || 'N/A');
    const valores = dados.map(d => d.total);

    charts.tecnico = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Quantidade de OS',
                data: valores,
                backgroundColor: 'rgba(118, 75, 162, 0.8)',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });
}

function criarGraficoProdutos(dados) {
    if (!dados || dados.length === 0) return;
}

function criarGraficoServicos(dados) {
    if (!dados || dados.length === 0) return;
}

function exportarDados() {
    const periodo = $('#filtro-periodo').val();
    window.open('<?= site_url("dashboard/exportar") ?>?tipo=atendimentos&periodo=' + periodo, '_blank');
}
</script>