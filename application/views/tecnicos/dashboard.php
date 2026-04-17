<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2c3e50">
    <title>Dashboard - Portal do Técnico</title>
    <link rel="manifest" href="<?php echo base_url('assets/tecnicos/manifest.json'); ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f6fa;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            overflow: hidden;
            border: 3px solid rgba(255,255,255,0.5);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-level {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
        }

        .section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-badge {
            background: #667eea;
            color: white;
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 12px;
        }

        .os-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .os-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .os-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
        }

        .os-card.em_andamento {
            border-color: #4caf50;
            background: #f1f8e9;
        }

        .os-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .os-number {
            font-weight: 700;
            color: #667eea;
        }

        .os-status {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 500;
        }

        .os-status.aberto {
            background: #e3f2fd;
            color: #1976d2;
        }

        .os-status.em_andamento {
            background: #e8f5e9;
            color: #388e3c;
        }

        .os-status.aguardando {
            background: #fff3e0;
            color: #f57c00;
        }

        .os-client {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .os-address {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
        }

        .os-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #999;
        }

        .os-time {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .action-btn {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
        }

        .action-btn:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .action-label {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 5px 15px;
            color: #999;
            text-decoration: none;
            font-size: 0.75rem;
            transition: color 0.3s;
        }

        .nav-item.active {
            color: #667eea;
        }

        .nav-icon {
            font-size: 1.5rem;
            margin-bottom: 3px;
        }

        .content-wrapper {
            padding-bottom: 80px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="user-info">
                <div class="user-avatar">
                    <?php if ($tecnico->foto_tecnico): ?>
                        <img src="<?php echo base_url($tecnico->foto_tecnico); ?>" alt="Foto">
                    <?php else: ?>
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='80'>👤</text></svg>" alt="Foto">
                    <?php endif; ?>
                </div>
                <div>
                    <div class="user-name"><?php echo htmlspecialchars($tecnico->nome); ?></div>
                    <div class="user-level">
                        Nível <?php echo $tecnico->nivel_tecnico; ?>
                        <?php if ($tecnico->especialidades): ?>
                            • <?php echo htmlspecialchars($tecnico->especialidades); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <a href="<?php echo site_url('tecnicos/logout'); ?>" class="logout-btn">Sair</a>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="container">
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($os_hoje); ?></div>
                    <div class="stat-label">OS Hoje</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($os_pendentes); ?></div>
                    <div class="stat-label">Pendentes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $os_concluidas; ?></div>
                    <div class="stat-label">Esta Semana</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="action-btn">
                    <div class="action-icon">📋</div>
                    <div class="action-label">Minhas OS</div>
                </a>
                <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="action-btn">
                    <div class="action-icon">📦</div>
                    <div class="action-label">Meu Estoque</div>
                </a>
            </div>

            <!-- OS de Hoje -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        📅 OS de Hoje
                    </div>
                    <span class="section-badge"><?php echo count($os_hoje); ?></span>
                </div>

                <?php if (empty($os_hoje)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">✅</div>
                        <p>Nenhuma OS agendada para hoje</p>
                    </div>
                <?php else: ?>
                    <div class="os-list">
                        <?php foreach ($os_hoje as $os): ?>
                            <a href="<?php echo site_url('tecnicos/executar_os/' . $os->idOs); ?>"
                               class="os-card <?php echo $os->status == 'Em Andamento' ? 'em_andamento' : ''; ?>">
                                <div class="os-header">
                                    <span class="os-number">#OS <?php echo $os->idOs; ?></span>
                                    <span class="os-status <?php echo strtolower(str_replace(' ', '_', $os->status)); ?>">
                                        <?php echo $os->status; ?>
                                    </span>
                                </div>
                                <div class="os-client"><?php echo htmlspecialchars($os->cliente_nome); ?></div>
                                <div class="os-address">
                                    <?php echo htmlspecialchars($os->endereco ?? 'Endereço não informado'); ?>
                                </div>

                                <div class="os-footer">
                                    <div class="os-time">
                                        🕐 <?php echo date('H:i', strtotime($os->dataFinal ?? 'now')); ?>
                                    </div>
                                    <div>
                                        <?php if ($os->garantia): ?>
                                            🔧 Garantia
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Próximas OS -->
            <?php if (!empty($os_pendentes)): ?>
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        📋 Próximas OS
                    </div>
                    <span class="section-badge"><?php echo count($os_pendentes); ?></span>
                </div>

                <div class="os-list">
                    <?php foreach (array_slice($os_pendentes, 0, 3) as $os): ?>
                        <a href="<?php echo site_url('tecnicos/executar_os/' . $os->idOs); ?>" class="os-card">
                            <div class="os-header">
                                <span class="os-number">#OS <?php echo $os->idOs; ?></span>
                                <span class="os-status <?php echo strtolower(str_replace(' ', '_', $os->status)); ?>">
                                    <?php echo $os->status; ?>
                                </span>
                            </div>

                            <div class="os-client"><?php echo htmlspecialchars($os->cliente_nome); ?></div>

                            <div class="os-footer">
                                <div class="os-time">
                                    📅 <?php echo date('d/m/Y', strtotime($os->dataInicial)); ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if (count($os_pendentes) > 3): ?>
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="<?php echo site_url('tecnicos/minhas_os'); ?>"
                           style="color: #667eea; text-decoration: none; font-weight: 500;">
                            Ver todas as OS →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Resumo de Estoque -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        📦 Estoque no Veículo
                    </div>
                    <span class="section-badge"><?php echo count($estoque); ?></span>
                </div>

                <?php if (empty($estoque)): ?>                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <p>Nenhum item em estoque</p>
                    </div>
                <?php else: ?>                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <?php foreach (array_slice($estoque, 0, 3) as $item): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                <div>
                                    <div style="font-weight: 500;"><?php echo htmlspecialchars($item->produto_nome); ?></div>
                                    <div style="font-size: 0.8rem; color: #999;"><?php echo $item->codDeBarra; ?></div>
                                </div>
                                <div style="font-weight: 600; color: #667eea;">
                                    <?php echo $item->quantidade; ?> <?php echo $item->unidade; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="nav-item active">
            <span class="nav-icon">🏠</span>
            <span>Início</span>
        </a>
        <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="nav-item">
            <span class="nav-icon">📋</span>
            <span>OS</span>
        </a>
        <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="nav-item">
            <span class="nav-icon">📦</span>
            <span>Estoque</span>
        </a>
        <a href="<?php echo site_url('tecnicos/perfil'); ?>" class="nav-item">
            <span class="nav-icon">👤</span>
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
    </script>
</body>
</html>
