<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - Técnicos</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/fontawesome.min.css'); ?>">
    <style>
        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .stat-card .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .menu-item {
            display: block;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        .menu-item:hover {
            background: #667eea;
            color: white;
            text-decoration: none;
        }
        .menu-item i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2><i class="fas fa-tools"></i> Gestão de Técnicos</h2>
                <p class="text-muted">Dashboard Administrativo</p>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="number"><?php echo isset($total_tecnicos) ? $total_tecnicos : 0; ?></div>
                    <div class="label">Técnicos Cadastrados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="number"><?php echo isset($os_hoje) ? count($os_hoje) : 0; ?></div>
                    <div class="label">OS Hoje</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                    <div class="number"><?php echo isset($execucoes_mes) && isset($execucoes_mes['total_execucoes']) ? $execucoes_mes['total_execucoes'] : 0; ?></div>
                    <div class="label">Execuções no Mês</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                    <div class="number"><?php echo isset($execucoes_mes) && isset($execucoes_mes['media_tempo_horas']) ? $execucoes_mes['media_tempo_horas'] : 0; ?>h</div>
                    <div class="label">Tempo Médio</div>
                </div>
            </div>
        </div>

        <!-- Menu Principal -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="dashboard-card">
                    <h5><i class="fas fa-users-cog"></i> Gestão de Técnicos</h5>
                    <hr>
                    <a href="<?php echo site_url('tecnicos_admin/adicionar_tecnico'); ?>" class="menu-item">
                        <i class="fas fa-user-plus"></i> Cadastrar Técnico
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="menu-item">
                        <i class="fas fa-list"></i> Listar Técnicos
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="menu-item">
                        <i class="fas fa-box"></i> Estoque por Técnico
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <h5><i class="fas fa-clipboard-list"></i> OS e Serviços</h5>
                    <hr>
                    <a href="<?php echo site_url('os'); ?>" class="menu-item">
                        <i class="fas fa-tasks"></i> Todas as OS
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/servicos_catalogo'); ?>" class="menu-item">
                        <i class="fas fa-cogs"></i> Catálogo de Serviços
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/checklists'); ?>" class="menu-item">
                        <i class="fas fa-check-square"></i> Templates de Checklist
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <h5><i class="fas fa-chart-bar"></i> Relatórios</h5>
                    <hr>
                    <a href="<?php echo site_url('tecnicos_admin/relatorios'); ?>" class="menu-item">
                        <i class="fas fa-chart-line"></i> Relatórios de Produtividade
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/rotas'); ?>" class="menu-item">
                        <i class="fas fa-route"></i> Rotas dos Técnicos
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/obras'); ?>" class="menu-item">
                        <i class="fas fa-building"></i> Gestão de Obras
                    </a>
                </div>
            </div>
        </div>

        <!-- OS do Dia -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="dashboard-card">
                    <h5><i class="fas fa-calendar-day"></i> OS de Hoje</h5>
                    <hr>
                    <?php if (!empty($os_hoje)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>OS #</th>
                                        <th>Cliente</th>
                                        <th>Técnico</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($os_hoje as $os): ?>
                                        <tr>
                                            <td><?php echo $os->idOs; ?></td>
                                            <td><?php echo $os->cliente_nome; ?></td>
                                            <td><?php echo isset($os->tecnico_nome) ? $os->tecnico_nome : 'N/A'; ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $os->status == 'Aberto' ? 'primary' : ($os->status == 'Em Andamento' ? 'warning' : 'info'); ?>">
                                                    <?php echo $os->status; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo site_url('os/visualizar/' . $os->idOs); ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">Nenhuma OS agendada para hoje.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
</body>
</html>
