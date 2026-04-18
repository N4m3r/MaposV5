<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#667eea">
    <title>Dashboard - Portal do Técnico</title>
    <link rel="manifest" href="<?php echo base_url('assets/tecnicos/manifest.json'); ?>">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            color: #333;
            -webkit-font-smoothing: antialiased;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 20px 35px;
            position: sticky;
            top: 0;
            z-index: 100;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 600px;
            margin: 0 auto;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: white;
            overflow: hidden;
            border: 4px solid rgba(255,255,255,0.4);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            animation: pulse-avatar 2s infinite;
        }

        @keyframes pulse-avatar {
            0%, 100% { box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
            50% { box-shadow: 0 5px 30px rgba(255,255,255,0.4); }
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-avatar .default-avatar {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            font-size: 28px;
        }

        .user-details h2 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .user-level {
            font-size: 0.85rem;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .user-level i {
            color: #ffd700;
        }

        .logout-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            transition: all 0.3s;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }

        .container {
            padding: 0 20px 100px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: -25px 0 30px;
            position: relative;
            z-index: 10;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s;
            animation: slideUp 0.5s ease-out backwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 1.4rem;
        }

        .stat-card:nth-child(1) .stat-icon { background: #e3f2fd; color: #1976d2; }
        .stat-card:nth-child(2) .stat-icon { background: #fff3e0; color: #f57c00; }
        .stat-card:nth-child(3) .stat-icon { background: #e8f5e9; color: #388e3c; }

        .stat-number {
            font-size: 1.6rem;
            font-weight: 700;
            color: #333;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #888;
            margin-top: 6px;
            font-weight: 500;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .action-btn {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 25px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 5px 15px rgba(0,0,0,0.06);
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .action-btn:hover::before {
            transform: scaleX(1);
        }

        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .action-icon {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.8rem;
            color: white;
        }

        .action-btn:nth-child(1) .action-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .action-btn:nth-child(2) .action-icon { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }

        .action-label {
            font-weight: 600;
            font-size: 0.95rem;
            color: #333;
        }

        .action-desc {
            font-size: 0.75rem;
            color: #888;
            margin-top: 4px;
        }

        /* Sections */
        .section {
            background: white;
            border-radius: 24px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
        }

        .section-title i {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .section-badge {
            background: #667eea;
            color: white;
            font-size: 0.8rem;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* OS Cards */
        .os-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .os-card {
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 18px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }

        .os-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #667eea;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .os-card:hover::before {
            opacity: 1;
        }

        .os-card:hover {
            border-color: #667eea;
            transform: translateX(5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
        }

        .os-card.em_andamento {
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
            border-color: #4caf50;
        }

        .os-card.em_andamento::before {
            background: #4caf50;
            opacity: 1;
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
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .os-status {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .os-status.aberto { background: #e3f2fd; color: #1976d2; }
        .os-status.em_andamento { background: #4caf50; color: white; }
        .os-status.aguardando { background: #fff3e0; color: #f57c00; }

        .os-client {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 1rem;
        }

        .os-address {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 5px;
        }

        .os-address i {
            color: #667eea;
            margin-top: 2px;
        }

        .os-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid #e8e8e8;
        }

        .os-time {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            color: #888;
        }

        .os-time i {
            color: #667eea;
        }

        .os-priority {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
        }

        .os-priority.urgente { background: #ffebee; color: #c62828; }
        .os-priority.normal { background: #e8f5e9; color: #388e3c; }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #888;
        }

        .empty-icon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
        }

        .empty-state h4 {
            color: #666;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 0.9rem;
        }

        /* Estoque Items */
        .estoque-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .estoque-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 14px;
            transition: all 0.3s;
        }

        .estoque-item:hover {
            background: #e8f5e9;
            transform: translateX(5px);
        }

        .estoque-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .estoque-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .estoque-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }

        .estoque-code {
            font-size: 0.75rem;
            color: #888;
        }

        .estoque-qty {
            font-weight: 700;
            color: #667eea;
            font-size: 1.1rem;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 12px 0 20px;
            box-shadow: 0 -5px 30px rgba(0,0,0,0.1);
            border-radius: 25px 25px 0 0;
            z-index: 1000;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 20px;
            color: #999;
            text-decoration: none;
            font-size: 0.7rem;
            transition: all 0.3s;
            position: relative;
        }

        .nav-item.active {
            color: #667eea;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            top: -12px;
            width: 40px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 4px;
        }

        .nav-icon {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .nav-item.active .nav-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        /* Ver mais link */
        .ver-mais {
            text-align: center;
            margin-top: 20px;
        }

        .ver-mais a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .ver-mais a:hover {
            background: #e3f2fd;
        }

        .ver-mais a i {
            transition: transform 0.3s;
        }

        .ver-mais a:hover i {
            transform: translateX(5px);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .section {
            animation: fadeIn 0.5s ease-out;
        }

        @media (max-width: 480px) {
            .stats-grid {
                gap: 10px;
            }

            .stat-card {
                padding: 15px 10px;
            }

            .stat-number {
                font-size: 1.3rem;
            }

            .quick-actions {
                gap: 12px;
            }

            .action-btn {
                padding: 20px 15px;
            }

            .action-icon {
                width: 45px;
                height: 45px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="user-info">
                <div class="user-avatar">
                    <?php if (!empty($tecnico->foto_tecnico)): ?>
                        <img src="<?php echo base_url($tecnico->foto_tecnico); ?>" alt="Foto">
                    <?php else: ?>
                        <div class="default-avatar">
                            <i class='bx bx-user'></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="user-details">
                    <h2><?php echo htmlspecialchars($tecnico->nome, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></h2>
                    <div class="user-level">
                        <i class='bx bxs-star'></i>
                        Nível <?php echo $tecnico->nivel_tecnico ?? 'I'; ?>
                        <?php if (!empty($tecnico->especialidades)): ?>
                            <span>• <?php echo htmlspecialchars($tecnico->especialidades, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <a href="<?php echo site_url('tecnicos/logout'); ?>" class="logout-btn" title="Sair">
                <i class='bx bx-log-out'></i>
            </a>
        </div>
    </header>

    <div class="container">
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class='bx bx-calendar-check'></i>
                </div>
                <div class="stat-number"><?php echo count($os_hoje ?? []); ?></div>
                <div class="stat-label">OS Hoje</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class='bx bx-time-five'></i>
                </div>
                <div class="stat-number"><?php echo count($os_pendentes ?? []); ?></div>
                <div class="stat-label">Pendentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class='bx bx-check-circle'></i>
                </div>
                <div class="stat-number"><?php echo $os_concluidas ?? 0; ?></div>
                <div class="stat-label">Esta Semana</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="action-btn">
                <div class="action-icon">
                    <i class='bx bx-clipboard'></i>
                </div>
                <div class="action-label">Minhas OS</div>
                <div class="action-desc">Ver ordens de serviço</div>
            </a>
            <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="action-btn">
                <div class="action-icon">
                    <i class='bx bx-package'></i>
                </div>
                <div class="action-label">Meu Estoque</div>
                <div class="action-desc">Materiais disponíveis</div>
            </a>
        </div>

        <!-- OS de Hoje -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">
                    <i class='bx bx-calendar-event'></i>
                    OS de Hoje
                </div>
                <span class="section-badge"><?php echo count($os_hoje ?? []); ?></span>
            </div>

            <?php if (empty($os_hoje)): ?
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class='bx bx-check-double'></i>
                    </div>
                    <h4>Nenhuma OS agendada</h4>
                    <p>Você não possui ordens de serviço para hoje.</p>
                </div>
            <?php else: ?
                <div class="os-list">
                    <?php foreach ($os_hoje as $os): ?
                        <a href="<?php echo site_url('tecnicos/executar_os/' . $os->idOs); ?>"
                           class="os-card <?php echo $os->status == 'Em Andamento' ? 'em_andamento' : ''; ?>">
                            <div class="os-header">
                                <span class="os-number">
                                    <i class='bx bx-hash'></i> OS <?php echo $os->idOs; ?>
                                </span>
                                <span class="os-status <?php echo strtolower(str_replace(' ', '_', $os->status)); ?>">
                                    <i class='bx <?php echo $os->status == 'Em Andamento' ? 'bx-play' : 'bx-time'; ?>'></i>
                                    <?php echo $os->status; ?>
                                </span>
                            </div>
                            <div class="os-client">
                                <i class='bx bx-user'></i> <?php echo htmlspecialchars($os->cliente_nome ?? 'Cliente não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                            </div>
                            <div class="os-address">
                                <i class='bx bx-map-pin'></i>
                                <?php echo htmlspecialchars($os->endereco ?? 'Endereço não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                            </div>

                            <div class="os-footer">
                                <div class="os-time">
                                    <i class='bx bx-time'></i>
                                    <?php echo isset($os->dataFinal) ? date('H:i', strtotime($os->dataFinal)) : '--:--'; ?>
                                </div>
                                <?php if (!empty($os->garantia)): ?
                                    <span class="os-priority normal">Garantia</span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Próximas OS -->
        <?php if (!empty($os_pendentes)): ?
        <div class="section">
            <div class="section-header">
                <div class="section-title">
                    <i class='bx bx-calendar'></i>
                    Próximas OS
                </div>
                <span class="section-badge"><?php echo count($os_pendentes); ?></span>
            </div>

            <div class="os-list">
                <?php foreach (array_slice($os_pendentes, 0, 3) as $os): ?
                    <a href="<?php echo site_url('tecnicos/executar_os/' . $os->idOs); ?>" class="os-card">
                        <div class="os-header">
                            <span class="os-number">
                                <i class='bx bx-hash'></i> OS <?php echo $os->idOs; ?>
                            </span>
                            <span class="os-status <?php echo strtolower(str_replace(' ', '_', $os->status)); ?>">
                                <?php echo $os->status; ?>
                            </span>
                        </div>

                        <div class="os-client">
                            <i class='bx bx-user'></i> <?php echo htmlspecialchars($os->cliente_nome ?? 'Cliente não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </div>

                        <div class="os-footer">
                            <div class="os-time">
                                <i class='bx bx-calendar-alt'></i>
                                <?php echo isset($os->dataInicial) ? date('d/m/Y', strtotime($os->dataInicial)) : '--/--/--'; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (count($os_pendentes) > 3): ?
                <div class="ver-mais">
                    <a href="<?php echo site_url('tecnicos/minhas_os'); ?>">
                        Ver todas as OS
                        <i class='bx bx-chevron-right'></i>
                    </a>
                </div>
            <?php endif; ?
003e
        </div>
        <?php endif; ?>

        <!-- Resumo de Estoque -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">
                    <i class='bx bx-package'></i>
                    Estoque no Veículo
                </div>
                <span class="section-badge"><?php echo count($estoque ?? []); ?></span>
            </div>

            <?php if (empty($estoque)): ?
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class='bx bx-box'></i>
                    </div>
                    <h4>Estoque vazio</h4>
                    <p>Nenhum material registrado.</p>
                </div>
            <?php else: ?
                <div class="estoque-list">
                    <?php foreach (array_slice($estoque, 0, 3) as $item): ?
                        <div class="estoque-item">
                            <div class="estoque-info">
                                <div class="estoque-icon">
                                    <i class='bx bx-wrench'></i>
                                </div>
                                <div>
                                    <div class="estoque-name"><?php echo htmlspecialchars($item->produto_nome ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                                    <div class="estoque-code"><?php echo $item->codDeBarra ?? ''; ?></div>
                                </div>
                            </div>
                            <div class="estoque-qty">
                                <?php echo $item->quantidade ?? 0; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($estoque) > 3): ?
                    <div class="ver-mais">
                        <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>">
                            Ver estoque completo
                            <i class='bx bx-chevron-right'></i>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="nav-item active">
            <div class="nav-icon">
                <i class='bx bx-home-alt'></i>
            </div>
            <span>Início</span>
        </a>
        <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="nav-item">
            <div class="nav-icon">
                <i class='bx bx-clipboard'></i>
            </div>
            <span>OS</span>
        </a>
        <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="nav-item">
            <div class="nav-icon">
                <i class='bx bx-package'></i>
            </div>
            <span>Estoque</span>
        </a>
        <a href="<?php echo site_url('tecnicos/perfil'); ?>" class="nav-item">
            <div class="nav-icon">
                <i class='bx bx-user'></i>
            </div>
            <span>Perfil</span>
        </a>
    </nav>

    <script>
        // Registrar Service Worker para PWA
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('<?php echo base_url('assets/tecnicos/sw.js'); ?>')
                .then(reg => console.log('Service Worker registrado'))
                .catch(err => console.log('Erro ao registrar Service Worker:', err));
        }

        // Adicionar animação aos cards ao scroll
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

        document.querySelectorAll('.section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'all 0.5s ease-out';
            observer.observe(section);
        });
    </script>
</body>
</html>
