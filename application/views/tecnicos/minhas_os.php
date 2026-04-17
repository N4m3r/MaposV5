<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2c3e50">
    <title>Minhas OS - Portal do Técnico</title>
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
            padding-bottom: 80px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            text-decoration: none;
        }

        .header-title {
            flex: 1;
        }

        .header-title h1 {
            font-size: 1.2rem;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .filter-tab {
            padding: 10px 20px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #666;
            font-size: 0.9rem;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }

        .os-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .os-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s;
        }

        .os-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
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

        .os-status.aberto {
            background: #e3f2fd;
            color: #1976d2;
        }

        .os-status.em_andamento {
            background: #e8f5e9;
            color: #388e3c;
        }

        .os-status.finalizada {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .os-status.cancelada {
            background: #ffebee;
            color: #c62828;
        }

        .os-client {
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 5px;
        }

        .os-info {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .os-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
            font-size: 0.85rem;
            color: #999;
        }

        .os-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }

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
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="back-btn">←</a>
            <div class="header-title">
                <h1>Minhas OS</h1>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Filtros -->
        <div class="filter-tabs">
            <a href="<?php echo site_url('tecnicos/minhas_os?status=todos'); ?>"
               class="filter-tab <?php echo $status_atual == 'todos' ? 'active' : ''; ?>">
                Todas
            </a>
            <a href="<?php echo site_url('tecnicos/minhas_os?status=Aberto'); ?>"
               class="filter-tab <?php echo $status_atual == 'Aberto' ? 'active' : ''; ?>">
                Abertas
            </a>
            <a href="<?php echo site_url('tecnicos/minhas_os?status=Em Andamento'); ?>"
               class="filter-tab <?php echo $status_atual == 'Em Andamento' ? 'active' : ''; ?>">
                Em Andamento
            </a>
            <a href="<?php echo site_url('tecnicos/minhas_os?status=Finalizada'); ?>"
               class="filter-tab <?php echo $status_atual == 'Finalizada' ? 'active' : ''; ?>">
                Finalizadas
            </a>
        </div>

        <!-- Lista de OS -->
        <?php if (empty($os_list)): ?>        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <h3>Nenhuma OS encontrada</h3>
            <p>Não há ordens de serviço <?php echo $status_atual != 'todos' ? 'com este status' : ''; ?></p>
        </div>
        <?php else: ?>        <div class="os-list">
            <?php foreach ($os_list as $os): ?>            <a href="<?php echo site_url('tecnicos/executar_os/' . $os->idOs); ?>" class="os-card">
                <div class="os-header">
                    <span class="os-number">#OS <?php echo $os->idOs; ?></span>
                    <span class="os-status <?php echo strtolower(str_replace(' ', '_', $os->status)); ?>">
                        <?php echo $os->status; ?>
                    </span>
                </div>

                <div class="os-client"><?php echo htmlspecialchars($os->cliente_nome); ?></div>

                <?php if ($os->descricaoProduto): ?>                <div class="os-info"><?php echo htmlspecialchars(substr($os->descricaoProduto, 0, 100)) . (strlen($os->descricaoProduto) > 100 ? '...' : ''); ?></div>
                <?php endif; ?>

                <div class="os-footer">
                    <div class="os-date">
                        📅 <?php echo date('d/m/Y', strtotime($os->dataInicial)); ?>
                    </div>
                    <div>
                        <?php if ($os->garantia): ?>                        🔧 Garantia
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <nav class="bottom-nav">
        <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span>Início</span>
        </a>
        <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="nav-item active">
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
</body>
</html>
