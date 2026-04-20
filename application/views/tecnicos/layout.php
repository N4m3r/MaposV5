<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Portal do Técnico - Map-OS' ?></title>
    <link rel="shortcut icon" href="<?= base_url() ?>assets/img/favicon.png">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap.min.css">
    <link href="<?= base_url() ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script src="<?= base_url() ?>assets/js/jquery-1.12.4.min.js"></script>
    <style>
        /* RESET E BASE */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Roboto, -apple-system, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        /* LAYOUT PRINCIPAL */
        .tec-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .tec-sidebar {
            width: 220px;
            background: #1a1d29;
            color: #fff;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease;
        }

        .tec-sidebar.collapsed {
            width: 70px;
        }

        .tec-sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .tec-sidebar-logo {
            max-width: 140px;
            height: auto;
        }

        .tec-sidebar.collapsed .tec-sidebar-logo {
            max-width: 40px;
        }

        /* MENU */
        .tec-menu {
            flex: 1;
            padding: 15px 0;
            overflow-y: auto;
        }

        .tec-menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .tec-menu-item:hover,
        .tec-menu-item.active {
            background: rgba(255,255,255,0.05);
            color: #fff;
            border-left-color: #667eea;
            text-decoration: none;
        }

        .tec-menu-item i {
            font-size: 20px;
            width: 30px;
            text-align: center;
            margin-right: 12px;
        }

        .tec-sidebar.collapsed .tec-menu-item span {
            display: none;
        }

        .tec-menu-label {
            font-size: 14px;
            font-weight: 500;
        }

        /* TOGGLE SIDEBAR */
        .tec-sidebar-toggle {
            position: absolute;
            right: -12px;
            top: 80px;
            width: 24px;
            height: 24px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #fff;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 1001;
        }

        /* CONTEÚDO */
        .tec-main {
            flex: 1;
            margin-left: 220px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .tec-sidebar.collapsed ~ .tec-main {
            margin-left: 70px;
        }

        /* TOP NAVBAR - COMPACTA */
        .tec-navbar {
            background: #fff;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .tec-navbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .tec-page-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .tec-navbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .tec-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .tec-user-info:hover {
            background: #f5f7fa;
        }

        .tec-user-avatar {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
        }

        .tec-user-name {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .tec-user-role {
            font-size: 12px;
            color: #888;
        }

        /* BOTÕES NAVBAR */
        .tec-nav-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            text-decoration: none;
            transition: all 0.2s;
            position: relative;
            background: transparent;
            border: none;
        }

        .tec-nav-btn:hover {
            background: #f5f7fa;
            color: #333;
            text-decoration: none;
        }

        .tec-nav-btn i {
            font-size: 20px;
        }

        .tec-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #e74c3c;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
        }

        /* CONTEÚDO PRINCIPAL */
        .tec-content {
            flex: 1;
            padding: 24px;
        }

        /* CARDS */
        .tec-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .tec-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tec-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tec-card-title i {
            color: #667eea;
        }

        .tec-card-body {
            padding: 20px;
        }

        /* STATS GRID */
        .tec-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .tec-stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
        }

        .tec-stat-card.success { border-left-color: #27ae60; }
        .tec-stat-card.warning { border-left-color: #f39c12; }
        .tec-stat-card.danger { border-left-color: #e74c3c; }
        .tec-stat-card.info { border-left-color: #3498db; }

        .tec-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }

        .tec-stat-label {
            font-size: 13px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* QUICK ACTIONS */
        .tec-quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
        }

        .tec-action-btn {
            background: #fff;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            color: #333;
            text-decoration: none;
            transition: all 0.2s;
        }

        .tec-action-btn:hover {
            border-color: #667eea;
            background: #f8f9ff;
            text-decoration: none;
            color: #667eea;
            transform: translateY(-2px);
        }

        .tec-action-btn i {
            font-size: 28px;
            display: block;
            margin-bottom: 8px;
        }

        .tec-action-btn span {
            font-size: 14px;
            font-weight: 500;
        }

        /* LISTA DE ITENS */
        .tec-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .tec-list-item {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .tec-list-item:hover {
            background: #e8f4f8;
        }

        .tec-list-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin-right: 14px;
            flex-shrink: 0;
        }

        .tec-list-content {
            flex: 1;
            min-width: 0;
        }

        .tec-list-title {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tec-list-subtitle {
            font-size: 12px;
            color: #888;
        }

        .tec-list-status {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .tec-list-status.aberto { background: #d4edda; color: #155724; }
        .tec-list-status.andamento { background: #fff3cd; color: #856404; }
        .tec-list-status.pendente { background: #f8d7da; color: #721c24; }
        .tec-list-status.concluido { background: #d1ecf1; color: #0c5460; }

        .tec-list-action {
            margin-left: 12px;
        }

        /* RESPONSIVO */
        @media (max-width: 768px) {
            .tec-sidebar {
                transform: translateX(-100%);
            }

            .tec-sidebar.open {
                transform: translateX(0);
            }

            .tec-main {
                margin-left: 0;
            }

            .tec-navbar {
                padding: 0 16px;
            }

            .tec-user-name,
            .tec-user-role {
                display: none;
            }

            .tec-content {
                padding: 16px;
            }
        }

        /* ============================================
           MODO ESCURO (DARK MODE)
           ============================================ */

        /* Quando body tem data-theme="dark" */
        body[data-theme="dark"] {
            background: #0f1117;
            color: #e8e8e8;
        }

        body[data-theme="dark"] .tec-main {
            background: #0f1117;
        }

        /* Navbar no dark mode */
        body[data-theme="dark"] .tec-navbar {
            background: #1a1d29;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        body[data-theme="dark"] .tec-page-title {
            color: #e8e8e8;
        }

        body[data-theme="dark"] .tec-nav-btn {
            color: #a0a0a0;
        }

        body[data-theme="dark"] .tec-nav-btn:hover {
            background: #252a3a;
            color: #e8e8e8;
        }

        body[data-theme="dark"] .tec-user-info:hover {
            background: #252a3a;
        }

        body[data-theme="dark"] .tec-user-name {
            color: #e8e8e8;
        }

        body[data-theme="dark"] .tec-user-role {
            color: #888;
        }

        /* Cards no dark mode */
        body[data-theme="dark"] .tec-card {
            background: #1a1d29;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        body[data-theme="dark"] .tec-card-header {
            border-bottom-color: #2d3347;
        }

        body[data-theme="dark"] .tec-card-title {
            color: #e8e8e8;
        }

        body[data-theme="dark"] .tec-stat-card {
            background: #1a1d29;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        body[data-theme="dark"] .tec-stat-value {
            color: #e8e8e8;
        }

        /* Quick Actions no dark mode */
        body[data-theme="dark"] .tec-action-btn {
            background: #1a1d29;
            border-color: #2d3347;
            color: #e8e8e8;
        }

        body[data-theme="dark"] .tec-action-btn:hover {
            background: #252a3a;
            border-color: #667eea;
        }

        /* Lista no dark mode */
        body[data-theme="dark"] .tec-list-item {
            background: #252a3a;
        }

        body[data-theme="dark"] .tec-list-item:hover {
            background: #2d3347;
        }

        body[data-theme="dark"] .tec-list-title {
            color: #e8e8e8;
        }

        body[data-theme="dark"] .tec-list-subtitle {
            color: #888;
        }
    </style>
</head>
<body>
    <div class="tec-wrapper">
        <!-- Sidebar -->
        <aside class="tec-sidebar" id="tecSidebar">
            <div class="tec-sidebar-header">
                <img src="<?= base_url() ?>assets/img/logo-mapos-branco.png" alt="Map-OS" class="tec-sidebar-logo">
            </div>

            <nav class="tec-menu">
                <a href="<?= site_url('tecnicos/dashboard') ?>" class="tec-menu-item <?= isset($menuDashboard) ? 'active' : '' ?>">
                    <i class='bx bx-home-alt'></i>
                    <span class="tec-menu-label">Dashboard</span>
                </a>
                <a href="<?= site_url('tecnicos/minhas_os') ?>" class="tec-menu-item <?= isset($menuMinhasOs) ? 'active' : '' ?>">
                    <i class='bx bx-clipboard'></i>
                    <span class="tec-menu-label">Minhas OS</span>
                </a>
                <a href="<?= site_url('tecnicos/minhas_obras') ?>" class="tec-menu-item <?= isset($menuObras) ? 'active' : '' ?>">
                    <i class='bx bx-building'></i>
                    <span class="tec-menu-label">Minhas Obras</span>
                </a>
                <a href="<?= site_url('tecnicos/meu_estoque') ?>" class="tec-menu-item <?= isset($menuEstoque) ? 'active' : '' ?>">
                    <i class='bx bx-package'></i>
                    <span class="tec-menu-label">Meu Estoque</span>
                </a>
                <a href="<?= site_url('tecnicos/perfil') ?>" class="tec-menu-item <?= isset($menuPerfil) ? 'active' : '' ?>">
                    <i class='bx bx-user'></i>
                    <span class="tec-menu-label">Meu Perfil</span>
                </a>
            </nav>

            <div class="tec-sidebar-toggle" onclick="toggleSidebar()">
                <i class='bx bx-chevron-left' id="toggleIcon"></i>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="tec-main">
            <!-- Navbar Compacta -->
            <header class="tec-navbar">
                <div class="tec-navbar-left">
                    <h1 class="tec-page-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
                </div>

                <div class="tec-navbar-right">
                    <!-- Notificações -->
                    <a href="#" class="tec-nav-btn" title="Notificações">
                        <i class='bx bx-bell'></i>
                        <span class="tec-badge" id="notifCount" style="display:none">0</span>
                    </a>

                    <!-- Tema -->
                    <button class="tec-nav-btn" onclick="toggleTheme()" title="Alternar Tema">
                        <i class='bx bx-moon' id="themeIcon"></i>
                    </button>

                    <!-- Sair -->
                    <a href="<?= site_url('tecnicos/logout') ?>" class="tec-nav-btn" title="Sair">
                        <i class='bx bx-log-out'></i>
                    </a>

                    <!-- Usuário -->
                    <a href="<?= site_url('tecnicos/perfil') ?>" class="tec-user-info">
                        <div class="tec-user-avatar">
                            <?= strtoupper(substr($this->session->userdata('tec_nome') ?? 'T', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="tec-user-name"><?= $this->session->userdata('tec_nome') ?? 'Técnico' ?></div>
                            <div class="tec-user-role">Técnico</div>
                        </div>
                    </a>
                </div>
            </header>

            <!-- Conteúdo -->
            <div class="tec-content">
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('tecSidebar');
            const icon = document.getElementById('toggleIcon');
            sidebar.classList.toggle('collapsed');

            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('bx-chevron-left');
                icon.classList.add('bx-chevron-right');
            } else {
                icon.classList.remove('bx-chevron-right');
                icon.classList.add('bx-chevron-left');
            }
        }

        // Toggle Theme
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = body.getAttribute('data-theme') || 'light';

            // Alternar entre light e dark
            if (currentTheme === 'dark') {
                body.setAttribute('data-theme', 'light');
                themeIcon.classList.remove('bx-sun');
                themeIcon.classList.add('bx-moon');
                localStorage.setItem('tec-theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.classList.remove('bx-moon');
                themeIcon.classList.add('bx-sun');
                localStorage.setItem('tec-theme', 'dark');
            }
        }

        // Carregar tema salvo ao iniciar
        (function loadTheme() {
            const savedTheme = localStorage.getItem('tec-theme') || 'light';
            const themeIcon = document.getElementById('themeIcon');

            if (savedTheme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                themeIcon.classList.remove('bx-moon');
                themeIcon.classList.add('bx-sun');
            } else {
                document.body.setAttribute('data-theme', 'light');
                themeIcon.classList.remove('bx-sun');
                themeIcon.classList.add('bx-moon');
            }
        })();

        // Carregar notificações
        function loadNotifications() {
            $.getJSON('<?= base_url() ?>index.php/notificacoes/listar', function(resp) {
                if (resp.success && resp.nao_lidas > 0) {
                    $('#notifCount').text(resp.nao_lidas > 99 ? '99+' : resp.nao_lidas).show();
                }
            });
        }

        $(document).ready(function() {
            loadNotifications();
            setInterval(loadNotifications, 60000);
        });
    </script>
</body>
</html>
