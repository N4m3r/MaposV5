<?php
/**
 * Dashboard da Área do Cliente - Versão Moderna
 * Com controle de acesso baseado em permissões
 */

// Carregar helper de permissões
$CI =& get_instance();
$CI->load->helper('cliente_permissions');

// Verificar permissões
$podeVerOS = clienteHasPermission('visualizar_os');
$podeVerCompras = clienteHasPermission('visualizar_compras');
$podeVerCobrancas = clienteHasPermission('visualizar_cobrancas');
$podeVerBoletos = clienteHasPermission('visualizar_boletos');
$podeVerNotasFiscais = clienteHasPermission('visualizar_notas_fiscais');
$podeVerObras = clienteHasPermission('visualizar_obras');
$podeVerFinanceiro = clienteHasPermission('visualizar_financeiro');
$podeEditarPerfil = clienteHasPermission('editar_perfil');
$podeSolicitarOrcamento = clienteHasPermission('solicitar_orcamento');

// Contadores
$totalCobrancas = count($cobrancas ?? []);
$totalBoletos = count($boletos ?? []);
$totalNotasFiscais = count($notasFiscais ?? []);
$totalObras = count($obras ?? []);

// Contadores para estatísticas
$totalOS = count($os ?? []);
$totalCompras = count($compras ?? []);
$osAbertas = count(array_filter($os ?? [], function($o) { return $o->status == 'Aberto'; }));
$osAndamento = count(array_filter($os ?? [], function($o) { return $o->status == 'Em Andamento'; }));
$osFinalizadas = count(array_filter($os ?? [], function($o) { return $o->status == 'Finalizado'; }));
?\>

<link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    .cliente-dashboard {
        font-family: 'Roboto', sans-serif;
        padding: 15px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        min-height: calc(100vh - 120px);
    }

    /* Cards de Acesso Rápido */
    .quick-access-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .quick-card {
        background: white;
        border-radius: 16px;
        padding: 25px 20px;
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
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.8rem;
        color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: transform 0.3s ease;
    }

    .quick-card:hover .icon-wrapper {
        transform: scale(1.1);
    }

    .quick-card .title {
        font-weight: 600;
        font-size: 1rem;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .quick-card .subtitle {
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    /* Estatísticas */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .stat-card .number {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 5px;
    }

    .stat-card .label {
        font-size: 0.85rem;
        color: #7f8c8d;
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

    /* Status Badges Modernos */
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Botão de Ação Principal */
    .btn-action {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Grid de Widgets */
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

    /* Mensagem de Boas-vindas */
    .welcome-bar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 20px 25px;
        margin-bottom: 25px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    .welcome-bar h2 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 600;
    }

    .welcome-bar p {
        margin: 5px 0 0 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }

    /* Responsividade */
    @media (max-width: 480px) {
        .welcome-bar h2 {
            font-size: 1.1rem;
        }
        .quick-card {
            padding: 20px 15px;
        }
        .quick-card .icon-wrapper {
            width: 50px;
            height: 50px;
            font-size: 1.4rem;
        }
    }

    /* Estado vazio */
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #888;
    }

    .empty-state i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 15px;
        display: block;
    }

    .empty-state h4 {
        font-weight: 400;
        color: #666;
        margin-bottom: 10px;
    }
</style>

<div class="cliente-dashboard">

    <!-- Barra de Boas-vindas -->
    <div class="welcome-bar">
        <div>
            <h2><i class='bx bx-user-circle'></i> Olá, <?= htmlspecialchars($result->nomeCliente ?? 'Cliente') ?>!</h2>
            <p>Bem-vindo à sua área do cliente. Aqui você pode acompanhar suas ordens de serviço, compras e muito mais.</p>
        </div>
        <?php if ($podeSolicitarOrcamento): ?>
        <a href="<?= base_url('index.php/mine/adicionarOs') ?>" class="btn-action">
            <i class='bx bx-plus'></i> Solicitar Orçamento
        </a>
        <?php endif; ?>
    </div>

    <!-- Cards de Acesso Rápido -->
    <div class="quick-access-grid">
        <?php if ($podeVerOS): ?>
        <a href="<?= base_url('index.php/mine/os') ?>" class="quick-card">
            <div class="icon-wrapper" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                <i class='bx bx-file'></i>
            </div>
            <div class="title">Ordens de Serviço</div>
            <div class="subtitle"><?= $osAbertas ?> em aberto</div>
        </a>
        <?php endif; ?>

        <?php if ($podeVerCompras): ?>
        <a href="<?= base_url('index.php/mine/compras') ?>" class="quick-card">
            <div class="icon-wrapper" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%)">
                <i class='bx bx-cart-alt'></i>
            </div>
            <div class="title">Minhas Compras</div>
            <div class="subtitle"><?= count($compras ?? []) ?> compras</div>
        </a>
        <?php endif; ?>

        <?php if ($podeVerCobrancas): ?>
        <a href="<?= base_url('index.php/mine/cobrancas') ?>" class="quick-card">
            <div class="icon-wrapper" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%)">
                <i class='bx bx-credit-card-front'></i>
            </div>
            <div class="title">Cobranças</div>
            <div class="subtitle"><?= $totalCobrancas ?> cobrança(s)</div>
        </a>
        <?php endif; ?>

        <?php if ($podeVerBoletos): ?>
        <a href="<?= base_url('index.php/mine/boletos') ?>" class="quick-card">
            <div class="icon-wrapper" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%)">
                <i class='bx bx-barcode'></i>
            </div>
            <div class="title">Boletos</div>
            <div class="subtitle"><?= $totalBoletos ?> boleto(s)</div>
        </a>
        <?php endif; ?>

        <?php if ($podeVerNotasFiscais): ?>
        <a href="<?= base_url('index.php/mine/notasfiscais') ?>" class="quick-card">
            <div class="icon-wrapper" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%)">
                <i class='bx bx-receipt'></i>
            </div>
            <div class="title">Notas Fiscais</div>
            <div class="subtitle"><?= $totalNotasFiscais ?> NFS-e</div>
        </a>
        <?php endif; ?>

        <?php if ($podeVerObras): ?>
        <a href="<?= base_url('index.php/mine/obras') ?>" class="quick-card">
            <div class="icon-wrapper" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%)">
                <i class='bx bx-building-house'></i>
            </div>
            <div class="title">Obras</div>
            <div class="subtitle"><?= $totalObras ?> obra(s)</div>
        </a>
        <?php endif; ?>

        <?php if ($podeEditarPerfil): ?>
        <a href="<?= base_url('index.php/mine/conta') ?>" class="quick-card">
            <div class="icon-wrapper" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)">
                <i class='bx bx-user-circle'></i>
            </div>
            <div class="title">Minha Conta</div>
            <div class="subtitle">Gerenciar dados</div>
        </a>
        <?php endif; ?>
    </div>

    <?php if ($podeVerOS): ?>
    <!-- Estatísticas Rápidas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="number"><?= $totalOS ?></div>
            <div class="label">Total de OS</div>
        </div>
        <div class="stat-card">
            <div class="number" style="color: #00cd00"><?= $osAbertas ?></div>
            <div class="label">OS Abertas</div>
        </div>
        <div class="stat-card">
            <div class="number" style="color: #436eee"><?= $osAndamento ?></div>
            <div class="label">Em Andamento</div>
        </div>
        <div class="stat-card">
            <div class="number" style="color: #256"><?= $osFinalizadas ?></div>
            <div class="label">Finalizadas</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Grid Principal -->
    <div class="dashboard-grid">

        <?php if ($podeVerOS): ?>
        <!-- Coluna Esquerda - Últimas OS -->
        <div class="dashboard-col-8">
            <div class="widget-card">
                <div class="widget-header">
                    <h5><i class='bx bx-file'></i> Últimas Ordens de Serviço</h5>
                    <a href="<?= base_url('index.php/mine/os') ?>" class="btn btn-small">Ver Todas</a>
                </div>
                <div class="widget-body" style="padding: 0">
                    <?php if (!empty($os)): ?>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Responsável</th>
                                <th>Data Inicial</th>
                                <th>Data Final</th>
                                <th>Status</th>
                                <th style="text-align:right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($os, 0, 5) as $o): ?>
                            <?php
                                $coresStatus = [
                                    'Aberto' => '#00cd00',
                                    'Em Andamento' => '#436eee',
                                    'Orçamento' => '#CDB380',
                                    'Negociação' => '#AEB404',
                                    'Cancelado' => '#CD0000',
                                    'Finalizado' => '#256',
                                    'Faturado' => '#B266FF',
                                    'Aguardando Peças' => '#FF7F00',
                                    'Aprovado' => '#808080'
                                ];
                                $cor = $coresStatus[$o->status] ?? '#E0E4CC';
                            ?>
                            <tr>
                                <td><strong>#<?= $o->idOs ?></strong></td>
                                <td><?= htmlspecialchars($o->nome) ?></td>
                                <td><?= date('d/m/Y', strtotime($o->dataInicial)) ?></td>
                                <td><?= date('d/m/Y', strtotime($o->dataFinal)) ?></td>
                                <td>
                                    <span class="status-badge" style="background: <?= $cor ?>20; color: <?= $cor ?>">
                                        <?= $o->status ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('index.php/mine/visualizarOs/' . $o->idOs) ?>" class="btn btn-small" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($o->status == 'Finalizado' || $o->status == 'Finalizada'): ?>
                                        <a href="<?= base_url('index.php/mine/relatorioAtendimento/' . $o->idOs) ?>" class="btn btn-small" style="background: #28a745; color: white;" title="Relatório de Atendimento">
                                            <i class="bx bx-file"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('index.php/mine/imprimirOs/' . $o->idOs) ?>" class="btn btn-small" target="_blank" title="Imprimir">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class='bx bx-file'></i>
                        <h4>Nenhuma ordem de serviço encontrada</h4>
                        <p>Você ainda não possui ordens de serviço cadastradas.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($podeVerCompras): ?>
        <!-- Coluna Direita - Últimas Compras -->
        <div class="<?= $podeVerOS ? 'dashboard-col-4' : 'dashboard-col-12' ?>">
            <div class="widget-card">
                <div class="widget-header">
                    <h5><i class='bx bx-cart-alt'></i> Últimas Compras</h5>
                    <a href="<?= base_url('index.php/mine/compras') ?>" class="btn btn-small">Ver Todas</a>
                </div>
                <div class="widget-body" style="padding: 0">
                    <?php if (!empty($compras)): ?>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th style="text-align:right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($compras, 0, 5) as $c): ?>
                            <?php
                                $coresStatus = [
                                    'Aberto' => '#00cd00',
                                    'Finalizado' => '#256',
                                    'Faturado' => '#B266FF',
                                    'Cancelado' => '#CD0000'
                                ];
                                $cor = $coresStatus[$c->status] ?? '#E0E4CC';
                            ?>
                            <tr>
                                <td><strong>#<?= $c->idVendas ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($c->dataVenda)) ?></td>
                                <td>
                                    <span class="status-badge" style="background: <?= $cor ?>20; color: <?= $cor ?>">
                                        <?= $c->status ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('index.php/mine/visualizarCompra/' . $c->idVendas) ?>" class="btn btn-small" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class='bx bx-cart'></i>
                        <h4>Nenhuma compra encontrada</h4>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>
