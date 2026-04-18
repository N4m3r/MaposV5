<?php
/**
 * Dashboard MapOS - Versão Moderna e Responsiva
 * Mantém o tema original com melhorias visuais e funcionais
 */
?>

<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/dist/excanvas.min.js"></script><![endif]-->

<script language="javascript" type="text/javascript" src="<?= base_url(); ?>assets/js/dist/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/dist/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/dist/plugins/jqplot.donutRenderer.min.js"></script>
<script src='<?= base_url(); ?>assets/js/fullcalendar.min.js'></script>
<script src='<?= base_url(); ?>assets/js/fullcalendar/locales/pt-br.js'></script>

<link href='<?= base_url(); ?>assets/css/fullcalendar.min.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/js/dist/jquery.jqplot.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

<!-- Dashboard Moderno - Estilos Inline para melhor performance -->
<style>
    /* Reset e Base */
    .dashboard-modern {
        font-family: 'Roboto', sans-serif;
        padding: 15px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        min-height: calc(100vh - 120px);
    }

    /* Header Moderno */
    .dashboard-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        border-radius: 16px;
        padding: 25px 30px;
        margin-bottom: 25px;
        color: white;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    .dashboard-header h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.8rem;
        margin: 0 0 5px 0;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .dashboard-header p {
        margin: 0;
        opacity: 0.8;
        font-size: 0.95rem;
    }

    /* Cards de Acesso Rápido Modernos */
    .quick-access-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .quick-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }

    .quick-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .quick-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .quick-card:hover::before {
        transform: scaleX(1);
    }

    .quick-card .icon-wrapper {
        width: 55px;
        height: 55px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 1.8rem;
        color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: transform 0.3s ease;
    }

    .quick-card:hover .icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }

    .quick-card .title {
        font-weight: 600;
        font-size: 0.95rem;
        color: #2c3e50;
        margin-bottom: 3px;
    }

    .quick-card .shortcut {
        font-size: 0.75rem;
        color: #7f8c8d;
        font-weight: 500;
    }

    /* Grid Principal */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
    }

    .dashboard-col-8 { grid-column: span 8; }
    .dashboard-col-4 { grid-column: span 4; }
    .dashboard-col-6 { grid-column: span 6; }
    .dashboard-col-12 { grid-column: span 12; }

    @media (max-width: 1200px) {
        .dashboard-col-8, .dashboard-col-4, .dashboard-col-6 {
            grid-column: span 6;
        }
    }

    @media (max-width: 768px) {
        .dashboard-col-8, .dashboard-col-4, .dashboard-col-6 {
            grid-column: span 12;
        }
        .quick-access-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Widget Cards */
    .widget-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .widget-header {
        padding: 18px 22px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .widget-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .widget-header h5 i {
        color: #667eea;
        font-size: 1.2rem;
    }

    .widget-body {
        padding: 20px;
    }

    /* Stats Cards Horizontal */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-box {
        background: white;
        border-radius: 12px;
        padding: 18px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
    }

    .stat-box:hover {
        transform: translateY(-3px);
    }

    .stat-box .number {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 5px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-box .label {
        font-size: 0.8rem;
        color: #7f8c8d;
        font-weight: 500;
    }

    /* Tabela Moderna */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modern-table thead th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 14px 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
    }

    .modern-table tbody tr {
        transition: background 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background: #f8f9fa;
    }

    .modern-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
        color: #495057;
    }

    .modern-table td:last-child {
        text-align: right;
    }

    /* Status Badges */
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Calendário Moderno */
    .calendar-wrapper {
        background: white;
        border-radius: 12px;
        padding: 15px;
    }

    .fc-event {
        border-radius: 6px !important;
        padding: 3px 6px !important;
        font-size: 0.85rem !important;
    }

    /* Loading Animation */
    @keyframes pulse-card {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .loading {
        animation: pulse-card 1.5s ease-in-out infinite;
    }

    /* Scrollbar personalizada */
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    /* Responsividade */
    @media (max-width: 480px) {
        .dashboard-header h1 {
            font-size: 1.3rem;
        }
        .quick-card {
            padding: 15px;
        }
        .quick-card .icon-wrapper {
            width: 45px;
            height: 45px;
            font-size: 1.4rem;
        }
    }
</style>

<div class="dashboard-modern">
    <!-- Header -->
    <div class="dashboard-header">
        <h1><i class='bx bx-grid-alt'></i> Dashboard</h1>
        <p>Bem-vindo ao sistema MapOS - Gestão inteligente em tempo real</p>
    </div>

    <!-- Quick Access Cards -->
    <div class="quick-access-grid">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) : ?>
        <a href="<?= site_url('clientes') ?>" class="quick-card">
            <div class="icon-wrapper">
                <i class='bx bx-user'></i>
            </div>
            <div class="title">Clientes</div>
            <div class="shortcut">F1</div>
        </a>
        <?php endif ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) : ?>
        <a href="<?= site_url('produtos') ?>" class="quick-card">
            <div class="icon-wrapper">
                <i class='bx bx-basket'></i>
            </div>
            <div class="title">Produtos</div>
            <div class="shortcut">F2</div>
        </a>
        <?php endif ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) : ?>
        <a href="<?= site_url('servicos') ?>" class="quick-card">
            <div class="icon-wrapper">
                <i class='bx bx-wrench'></i>
            </div>
            <div class="title">Serviços</div>
            <div class="shortcut">F3</div>
        </a>
        <?php endif ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) : ?>
        <a href="<?= site_url('os') ?>" class="quick-card">
            <div class="icon-wrapper">
                <i class='bx bx-file'></i>
            </div>
            <div class="title">Ordens</div>
            <div class="shortcut">F4</div>
        </a>
        <?php endif ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) : ?>
        <a href="<?= site_url('vendas/') ?>" class="quick-card">
            <div class="icon-wrapper">
                <i class='bx bx-cart-alt'></i>
            </div>
            <div class="title">Vendas</div>
            <div class="shortcut">F6</div>
        </a>
        <?php endif ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) : ?>
        <a href="<?= site_url('financeiro/lancamentos') ?>" class="quick-card">
            <div class="icon-wrapper">
                <i class='bx bx-bar-chart-alt-2'></i>
            </div>
            <div class="title">Financeiro</div>
            <div class="shortcut">F7</div>
        </a>
        <?php endif ?>
    </div>

    <!-- Dashboard Grid Principal -->
    <div class="dashboard-grid">
        <!-- Coluna Esquerda - Calendário e OS -->
        <div class="dashboard-col-8">
            <!-- Calendário -->
            <div class="widget-card">
                <div class="widget-header">
                    <h5><i class='bx bx-calendar'></i> Agenda de Ordens de Serviço</h5>
                    <select class="span2" name="statusOsGet" id="statusOsGet" style="margin: 0;">
                        <option value="">Todos Status</option>
                        <option value="Aberto">Aberto</option>
                        <option value="Faturado">Faturado</option>
                        <option value="Negociação">Negociação</option>
                        <option value="Orçamento">Orçamento</option>
                        <option value="Em Andamento">Em Andamento</option>
                        <option value="Finalizado">Finalizado</option>
                        <option value="Cancelado">Cancelado</option>
                        <option value="Aguardando Peças">Aguardando Peças</option>
                        <option value="Aprovado">Aprovado</option>
                    </select>
                    <button type="button" class="btn btn-small btn-info" id="btn-calendar">
                        <i class="bx bx-search"></i> Filtrar
                    </button>
                </div>
                <div class="widget-body">
                    <div class="calendar-wrapper">
                        <div id='source-calendar'></div>
                    </div>
                </div>
            </div>

            <!-- OS Recentes -->
            <div class="widget-card">
                <div class="widget-header">
                    <h5><i class='bx bx-clipboard'></i> Ordens de Serviço Recentes</h5>
                    <a href="<?= site_url('os') ?>" class="btn btn-small">Ver Todas</a>
                </div>
                <div class="widget-body" style="padding: 0;">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Cliente</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ordens_abertas != null) : ?>
                                <?php foreach (array_slice($ordens_abertas, 0, 5) as $o) : ?>
                                <?php
                                    $cores_status = [
                                        'Aberto' => ['#00cd00', '#e8f5e9'],
                                        'Em Andamento' => ['#436eee', '#e3f2fd'],
                                        'Orçamento' => ['#CDB380', '#fff8e1'],
                                        'Negociação' => ['#AEB404', '#f9fbe7'],
                                        'Cancelado' => ['#CD0000', '#ffebee'],
                                        'Finalizado' => ['#256', '#e8eaf6'],
                                        'Faturado' => ['#B266FF', '#f3e5f5'],
                                        'Aguardando Peças' => ['#FF7F00', '#fff3e0'],
                                        'Aprovado' => ['#808080', '#f5f5f5']
                                    ];
                                    $cor = $cores_status[$o->status] ?? ['#E0E4CC', '#fafafa'];
                                ?>
                                <tr>
                                    <td><strong>#<?= $o->idOs ?></strong></td>
                                    <td><?= htmlspecialchars($o->nomeCliente) ?></td>
                                    <td>
                                        <span class="status-badge" style="background: <?= $cor[1] ?>; color: <?= $cor[0] ?>;">
                                            <?= $o->status ?>
                                        </span>
                                    </td>
                                    <td><?= $o->dataFinal ? date('d/m/Y', strtotime($o->dataFinal)) : '-' ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) : ?>
                                            <a href="<?= base_url() ?>index.php/os/visualizar/<?= $o->idOs ?>" class="btn btn-small btn-info" title="Visualizar">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        <?php endif ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 30px;">
                                        <i class='bx bx-inbox' style="font-size: 2rem; color: #ccc;"></i>
                                        <p style="color: #999; margin-top: 10px;">Nenhuma OS recente</p>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Coluna Direita - Estatísticas e Info -->
        <div class="dashboard-col-4">
            <!-- Stats Cards -->
            <div class="stats-row">
                <div class="stat-box">
                    <div class="number"><?= $this->db->count_all('clientes'); ?></div>
                    <div class="label">Clientes</div>
                </div>
                <div class="stat-box">
                    <div class="number"><?= $this->db->count_all('os'); ?></div>
                    <div class="label">OS Total</div>
                </div>
                <div class="stat-box">
                    <div class="number"><?= $this->db->count_all('produtos'); ?></div>
                    <div class="label">Produtos</div>
                </div>
                <div class="stat-box">
                    <div class="number"><?= $this->db->count_all('vendas'); ?></div>
                    <div class="label">Vendas</div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="widget-card">
                <div class="widget-header">
                    <h5><i class='bx bx-bolt'></i> Ações Rápidas</h5>
                </div>
                <div class="widget-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <a href="<?php echo base_url(); ?>index.php/clientes/adicionar" class="btn btn-block" style="background: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 12px; border-radius: 8px;">
                            <i class='bx bx-user-plus'></i> Novo Cliente
                        </a>
                        <a href="<?php echo base_url(); ?>index.php/os/adicionar" class="btn btn-block" style="background: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 12px; border-radius: 8px;">
                            <i class='bx bx-file-plus'></i> Nova OS
                        </a>
                        <a href="<?php echo base_url(); ?>index.php/produtos/adicionar" class="btn btn-block" style="background: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 12px; border-radius: 8px;">
                            <i class='bx bx-package'></i> Novo Produto
                        </a>
                        <a href="<?php echo base_url(); ?>index.php/vendas/adicionar" class="btn btn-block" style="background: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 12px; border-radius: 8px;">
                            <i class='bx bx-cart'></i> Nova Venda
                        </a>
                    </div>
                </div>
            </div>

            <!-- OS por Status -->
            <div class="widget-card">
                <div class="widget-header">
                    <h5><i class='bx bx-pie-chart'></i> OS por Status</h5>
                </div>
                <div class="widget-body">
                    <div style="height: 200px;">
                        <canvas id="statusOsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Produtos com Estoque Baixo -->
            <div class="widget-card">
                <div class="widget-header">
                    <h5><i class='bx bx-error-circle'></i> Estoque Baixo</h5>
                    <span class="badge badge-warning"><?= count($produtos ?? []) ?></span>
                </div>
                <div class="widget-body" style="padding: 0; max-height: 300px; overflow-y: auto;" class="custom-scroll">
                    <table class="modern-table">
                        <tbody>
                            <?php if ($produtos != null && count($produtos) > 0) : ?>
                                <?php foreach (array_slice($produtos, 0, 5) as $p) : ?>
                                <tr>
                                    <td>
                                        <strong><?= $p->descricao ?></strong>
                                        <br><small style="color: #888;">Min: <?= $p->estoqueMinimo ?></small>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="badge badge-important"><?= $p->estoque ?> und</span>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 30px; color: #888;">
                                        <i class='bx bx-check-circle' style="font-size: 2rem; color: #4caf50;"></i>
                                        <p style="margin-top: 10px;">Estoque OK</p>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Financeiros (se houver permissão) -->
    <?php if ($estatisticas_financeiro != null && $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) : ?>
    <div class="dashboard-grid" style="margin-top: 20px;">
        <div class="dashboard-col-8">
            <div class="widget-card">
                <div class="widget-header">
                    <h5><i class='bx bx-trending-up'></i> Balanço Mensal</h5>
                    <form method="get" style="margin: 0; display: flex; gap: 10px;">
                        <input type="number" name="year" style="width: 80px; margin: 0;" value="<?php echo intval(preg_replace('/[^0-9]/', '', $this->input->get('year'))) ?: date('Y') ?>" class="input-small">
                        <button type="submit" class="btn btn-small btn-info"><i class='bx bx-search'></i></button>
                    </form>
                </div>
                <div class="widget-body">
                    <div style="height: 300px;">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-col-4">
            <div class="widget-card">
                <div class="widget-header">
                    <h5><i class='bx bx-wallet'></i> Resumo Financeiro</h5>
                </div>
                <div class="widget-body">
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 12px; padding: 20px; color: white;">
                            <div style="font-size: 0.85rem; opacity: 0.9;">Receita Total</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">
                                R$ <?= number_format($estatisticas_financeiro->total_receita ?? 0, 2, ',', '.') ?>
                            </div>
                        </div>
                        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 20px; color: white;">
                            <div style="font-size: 0.85rem; opacity: 0.9;">Despesa Total</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">
                                R$ <?= number_format($estatisticas_financeiro->total_despesa ?? 0, 2, ',', '.') ?>
                            </div>
                        </div>
                        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; padding: 20px; color: white;">
                            <div style="font-size: 0.85rem; opacity: 0.9;">Saldo em Caixa</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">
                                R$ <?= number_format(($estatisticas_financeiro->total_receita - $estatisticas_financeiro->total_despesa) ?? 0, 2, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
</div>

<!-- Modal Status OS Calendar -->
<div id="calendarModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><i class='bx bx-calendar-check'></i> Detalhes da OS</h3>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <div class="span6">
                <p><strong>OS:</strong> <span id="modalId"></span></p>
                <p><strong>Cliente:</strong> <span id="modalCliente"></span></p>
                <p><strong>Status:</strong> <span id="modalStatus"></span></p>
            </div>
            <div class="span6">
                <p><strong>Data Inicial:</strong> <span id="modalDataInicial"></span></p>
                <p><strong>Data Final:</strong> <span id="modalDataFinal"></span></p>
                <p><strong>Garantia:</strong> <span id="modalGarantia"></span></p>
            </div>
        </div>
        <hr>
        <p><strong>Descrição:</strong></p>
        <p id="modalDescription" style="background: #f5f5f5; padding: 10px; border-radius: 4px;"></p>
        <p><strong>Defeito:</strong></p>
        <p id="modalDefeito" style="background: #f5f5f5; padding: 10px; border-radius: 4px;"></p>
        <p><strong>Observações:</strong></p>
        <p id="modalObservacoes" style="background: #f5f5f5; padding: 10px; border-radius: 4px;"></p>
        <div class="row-fluid" style="margin-top: 15px;">
            <div class="span4" style="text-align: center; background: #e8f5e9; padding: 10px; border-radius: 4px;">
                <small>Subtotal</small><br><strong id="modalSubtotal"></strong>
            </div>
            <div class="span4" style="text-align: center; background: #fff3e0; padding: 10px; border-radius: 4px;">
                <small>Desconto</small><br><strong id="modalDesconto"></strong>
            </div>
            <div class="span4" style="text-align: center; background: #e3f2fd; padding: 10px; border-radius: 4px;">
                <small>Total</small><br><strong id="modalTotal"></strong>
            </div>
        </div>
    </div>
    <div class="modal-footer" style="display: flex; justify-content: center; gap: 10px;">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) : ?>
            <a id="modalIdVisualizar" href="" class="btn tip-top" title="Ver mais detalhes">
                <i class="fas fa-eye"></i> Visualizar
            </a>
        <?php endif; ?>
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) : ?>
            <a id="modalIdEditar" href="" class="btn btn-info tip-top" title="Editar OS">
                <i class="fas fa-edit"></i> Editar
            </a>
        <?php endif; ?>
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dOs')) : ?>
            <a id="linkExcluir" href="#modal-excluir-os" role="button" data-toggle="modal" os="" class="btn btn-danger tip-top" title="Excluir OS">
                <i class="fas fa-trash-alt"></i> Excluir
            </a>
        <?php endif; ?>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    // Inicializar Calendário
    var srcCalendarEl = document.getElementById('source-calendar');
    if (srcCalendarEl) {
        var srcCalendar = new FullCalendar.Calendar(srcCalendarEl, {
            locale: 'pt-br',
            height: 450,
            editable: false,
            selectable: false,
            businessHours: true,
            dayMaxEvents: true,
            displayEventTime: false,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: {
                url: "<?= base_url() . 'index.php/mapos/calendario'; ?>",
                method: 'GET',
                extraParams: function() {
                    return {
                        status: $("#statusOsGet").val(),
                    };
                },
                failure: function() {
                    alert('Falha ao buscar OS de calendário!');
                },
            },
            eventClick: function(info) {
                var eventObj = info.event.extendedProps;
                $('#modalId').html(eventObj.id);
                $('#modalIdVisualizar').attr("href", "<?php echo base_url(); ?>index.php/os/visualizar/" + eventObj.id);
                if (eventObj.editar) {
                    $('#modalIdEditar').show();
                    $('#linkExcluir').show();
                    $('#modalIdEditar').attr("href", "<?php echo base_url(); ?>index.php/os/editar/" + eventObj.id);
                    $('#modalIdExcluir').val(eventObj.id);
                } else {
                    $('#modalIdEditar').hide();
                    $('#linkExcluir').hide();
                }
                $('#modalCliente').html(eventObj.cliente);
                $('#modalDataInicial').html(eventObj.dataInicial);
                $('#modalDataFinal').html(eventObj.dataFinal);
                $('#modalGarantia').html(eventObj.garantia);
                $('#modalStatus').html(eventObj.status);
                $('#modalDescription').html(eventObj.description);
                $('#modalDefeito').html(eventObj.defeito);
                $('#modalObservacoes').html(eventObj.observacoes);
                $('#modalSubtotal').html(eventObj.subtotal);
                $('#modalDesconto').html(eventObj.desconto);
                $('#modalTotal').html(eventObj.total);
                $('#modalFaturado').html(eventObj.faturado);
                $('#eventUrl').attr('href', event.url);
                $('#calendarModal').modal();
            },
        });

        srcCalendar.render();

        $('#btn-calendar').on('click', function() {
            srcCalendar.refetchEvents();
        });
    }

    // Gráfico de OS por Status (Doughnut)
    var statusCtx = document.getElementById('statusOsChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Aberto', 'Em Andamento', 'Orçamento', 'Finalizado', 'Outros'],
                datasets: [{
                    data: [
                        <?= count(array_filter($ordens_abertas ?? [], function($o) { return $o->status == 'Aberto'; })) ?>,
                        <?= count(array_filter($ordens_abertas ?? [], function($o) { return $o->status == 'Em Andamento'; })) ?>,
                        <?= count($ordens_orcamentos ?? []) ?>,
                        <?= count($ordens_finalizadas ?? []) ?>,
                        <?= count($ordens_status ?? []) ?>
                    ],
                    backgroundColor: [
                        '#00cd00',
                        '#436eee',
                        '#CDB380',
                        '#256',
                        '#808080'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                }
            }
        });
    }

    // Gráfico Financeiro
    var financeCtx = document.getElementById('financeChart');
    if (financeCtx) {
        new Chart(financeCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [
                    {
                        label: 'Receita',
                        data: [
                            <?php echo($financeiro_mes->VALOR_JAN_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_FEV_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_MAR_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_ABR_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_MAI_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_JUN_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_JUL_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_AGO_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_SET_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_OUT_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_NOV_REC ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_DEZ_REC ?? 0); ?>
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'Despesas',
                        data: [
                            <?php echo($financeiro_mes->VALOR_JAN_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_FEV_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_MAR_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_ABR_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_MAI_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_JUN_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_JUL_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_AGO_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_SET_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_OUT_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_NOV_DES ?? 0); ?>,
                            <?php echo($financeiro_mes->VALOR_DEZ_DES ?? 0); ?>
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    }

    // Animações ao scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.widget-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease-out';
        observer.observe(card);
    });
});
</script>
