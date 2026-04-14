<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2c3e50">
    <title>Meu Estoque - Portal do Técnico</title>
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

        .header-title h1 {
            font-size: 1.2rem;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-bar {
            background: white;
            border-radius: 12px;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .search-bar input {
            flex: 1;
            border: none;
            font-size: 1rem;
            outline: none;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .card-badge {
            background: #667eea;
            color: white;
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 12px;
            font-weight: 600;
        }

        .stock-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 4px solid #667eea;
        }

        .stock-item.low {
            border-left-color: #f44336;
        }

        .stock-item.warning {
            border-left-color: #ff9800;
        }

        .stock-info h3 {
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .stock-info p {
            font-size: 0.8rem;
            color: #999;
        }

        .stock-qty {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .qty-badge {
            background: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            color: #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .qty-badge.low {
            color: #f44336;
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

        .history-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .history-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .history-icon.saida {
            background: #ffebee;
            color: #f44336;
        }

        .history-icon.entrada {
            background: #e8f5e9;
            color: #4caf50;
        }

        .history-info {
            flex: 1;
        }

        .history-info h4 {
            font-size: 0.95rem;
            margin-bottom: 3px;
        }

        .history-info p {
            font-size: 0.8rem;
            color: #999;
        }

        .history-qty {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .history-qty.positive {
            color: #4caf50;
        }

        .history-qty.negative {
            color: #f44336;
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

        .alert-card {
            background: #fff3e0;
            border: 2px solid #ff9800;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #ef6c00;
            margin-bottom: 5px;
        }

        .alert-text {
            font-size: 0.9rem;
            color: #f57c00;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="back-btn">←</a>
            <div class="header-title">
                <h1>Meu Estoque</h1>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Busca -->
        <div class="search-bar">
            <span>🔍</span>
            <input type="text" id="searchInput" placeholder="Buscar item..."
                   oninput="filtrarEstoque(this.value)">
        </div>

        <!-- Alerta de estoque baixo -->
        <?php
        $estoque_baixo = array_filter($estoque, function($item) {
            return $item->quantidade <= 2;
        });
        if (!empty($estoque_baixo)): ?
003e
        <div class="alert-card">
            <div class="alert-title">
                ⚠️ Atenção
            </div>
            <div class="alert-text">
                <?php echo count($estoque_baixo); ?> item(s) com estoque baixo
            </div>
        </div>
        <?php endif; ?>

        <!-- Estoque Atual -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">📦 Estoque Atual</span>
                <span class="card-badge"><?php echo count($estoque); ?> itens</span>
            </div>

            <?php if (empty($estoque)): ?
003e
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>Estoque Vazio</h3>
                <p>Nenhum item em seu veículo</p>
            </div>
            <?php else: ?
003e
            <div id="estoqueList">
                <?php foreach ($estoque as $item): ?
003e
                <div class="stock-item <?php echo $item->quantidade <= 2 ? 'low' : ($item->quantidade <= 5 ? 'warning' : ''); ?>"
                     data-nome="<?php echo strtolower($item->produto_nome); ?>">
                    <div class="stock-info">
                        <h3><?php echo htmlspecialchars($item->produto_nome); ?></h3>
                        <p>Código: <?php echo $item->codDeBarra; ?> | Un: <?php echo $item->unidade; ?></p>
                    </div>
                    <div class="stock-qty">
                        <span class="qty-badge <?php echo $item->quantidade <= 2 ? 'low' : ''; ?>">
                            <?php echo $item->quantidade; ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Histórico -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">📜 Histórico (30 dias)</span>
            </div>

            <?php if (empty($historico)): ?
003e
            <p style="text-align: center; color: #999; padding: 20px;">
                Nenhum movimento no período
            </p>
            <?php else: ?
003e
                <?php foreach (array_slice($historico, 0, 10) as $mov): ?
003e
                <div class="history-item">
                    <div class="history-icon <?php echo $mov->tipo; ?>">
                        <?php echo $mov->tipo == 'entrada' ? '📥' : '📤'; ?>
                    </div>
                    <div class="history-info">
                        <h4><?php echo htmlspecialchars($mov->produto_nome); ?></h4>
                        <p><?php echo date('d/m/Y H:i', strtotime($mov->data_hora)); ?>
                            <?php if ($mov->os_id): ?
003e
                            | OS #<?php echo $mov->os_id; ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="history-qty <?php echo $mov->tipo == 'entrada' ? 'positive' : 'negative'; ?>">
                        <?php echo $mov->tipo == 'entrada' ? '+' : '-'; ?><?php echo $mov->quantidade; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span>Início</span>
        </a>
        <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="nav-item">
            <span class="nav-icon">📋</span>
            <span>OS</span>
        </a>
        <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="nav-item active">
            <span class="nav-icon">📦</span>
            <span>Estoque</span>
        </a>
        <a href="<?php echo site_url('tecnicos/perfil'); ?>" class="nav-item">
            <span class="nav-icon">👤</span>
            <span>Perfil</span>
        </a>
    </nav>

    <script>
        function filtrarEstoque(termo) {
            const itens = document.querySelectorAll('.stock-item');
            const busca = termo.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            itens.forEach(item => {
                const nome = item.dataset.nome.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                item.style.display = nome.includes(busca) ? 'flex' : 'none';
            });
        }
    </script>
</body>
</html>
