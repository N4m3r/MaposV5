<!DOCTYPE html>
<html lang="pt-br">

<head>
  <title><?= ($configuration['app_name'] ?? null) ?: 'Map-OS' ?></title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token-name" content="<?= config_item("csrf_token_name") ?>">
  <meta name="csrf-cookie-name" content="<?= config_item("csrf_cookie_name") ?>">
  <link rel="shortcut icon" type="image/png" href="<?= base_url(); ?>assets/img/favicon.png" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap5.min.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-style.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-media.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/custom.css" />
  <link href="<?= base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/fullcalendar.css" />
  <?php if (($configuration['app_theme'] ?? null) == 'white') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white.css" />
  <?php } ?>
  <?php if (($configuration['app_theme'] ?? null) == 'puredark') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-pure-dark.css" />
  <?php } ?>
  <?php if (($configuration['app_theme'] ?? null) == 'darkviolet') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-dark-violet.css" />
  <?php } ?>
  <?php if (($configuration['app_theme'] ?? null) == 'darkorange') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-dark-orange.css" />
  <?php } ?>
  <?php if (($configuration['app_theme'] ?? null) == 'whitegreen') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white-green.css" />
  <?php } ?>
  <?php if (($configuration['app_theme'] ?? null) == 'whiteblack') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white-black.css" />
  <?php } ?>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css' crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" crossorigin="anonymous">
  <link href='https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;500;700&display=swap' rel='stylesheet' type='text/css' crossorigin="anonymous">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet' integrity="sha384-B6nB7GjeyR0Ln2Rf3Znp7Z7r4B4eR3i0Uq8k5+u2C2YJXTHVDbB+m9Z8dDqWlZ4H" crossorigin="anonymous">
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/shortcut.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/datatables.min.js"></script>
  <!-- TODO: Remove legacy SweetAlert v1 (sweetalert.min.js) after migrating all usages to SweetAlert2 -->
<script type="text/javascript" src="<?= base_url(); ?>assets/js/sweetalert.min.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
  <script type="text/javascript">
    shortcut.add("escape", function() {
      location.href = '<?= base_url(); ?>';
    });
    shortcut.add("F1", function() {
      location.href = '<?= site_url('clientes'); ?>';
    });
    shortcut.add("F2", function() {
      location.href = '<?= site_url('produtos'); ?>';
    });
    shortcut.add("F3", function() {
      location.href = '<?= site_url('servicos'); ?>';
    });
    shortcut.add("F4", function() {
      location.href = '<?= site_url('os'); ?>';
    });
    //shortcut.add("F5", function() {});
    shortcut.add("F6", function() {
      location.href = '<?= site_url('vendas/adicionar'); ?>';
    });
    shortcut.add("F7", function() {
      location.href = '<?= site_url('financeiro/lancamentos'); ?>';
    });
    shortcut.add("F8", function() {});
    shortcut.add("F9", function() {});
    shortcut.add("F10", function() {});
    //shortcut.add("F11", function() {});
    shortcut.add("F12", function() {});
    window.BaseUrl = "<?= base_url() ?>";
  </script>
  <style>
    /* ============================================
       TOPBAR / HEADER - Modern Design
       ============================================ */
    .navebarn {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-left: 242px;
      margin-top: 0;
      padding: 0 20px;
      height: 56px;
      background: var(--funSider, #333649);
      border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15);
      transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease;
      position: relative;
      z-index: 100;
    }

    /* Left section: logo + breadcrumb */
    .topbar-left {
      display: flex;
      align-items: center;
      gap: 16px;
      min-width: 0;
    }
    .topbar-left .topbar-breadcrumb {
      font-size: 14px;
      color: var(--cinza0, #a4a6b3);
      font-weight: 400;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .topbar-left .topbar-breadcrumb span {
      color: var(--sidebar-accent, #0467fc);
      font-weight: 600;
    }

    /* Right section: actions */
    .topbar-right {
      display: flex;
      align-items: center;
      gap: 4px;
    }

    /* Topbar action button (icon buttons) */
    .topbar-action {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      border-radius: 10px;
      border: none;
      background: transparent;
      color: var(--cinza0, #a4a6b3);
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
    }
    .topbar-action:hover {
      background: var(--sidebar-hover-bg, rgba(255,255,255,0.06));
      color: var(--sidebar-accent, #0467fc);
      transform: translateY(-1px);
    }
    .topbar-action i {
      font-size: 20px;
      transition: all 0.2s ease;
    }
    .topbar-action:hover i {
      transform: scale(1.1);
    }

    /* Badge for notifications */
    .topbar-action .topbar-badge {
      position: absolute;
      top: 4px;
      right: 4px;
      min-width: 16px;
      height: 16px;
      padding: 0 4px;
      font-size: 9px;
      font-weight: 700;
      line-height: 16px;
      text-align: center;
      color: #fff;
      background: #e74c3c;
      border-radius: 8px;
      animation: badgePulse 2s infinite;
    }
    @keyframes badgePulse {
      0%, 100% { box-shadow: 0 0 0 0 rgba(231,76,60,0.4); }
      50% { box-shadow: 0 0 0 4px rgba(231,76,60,0); }
    }

    /* Dropdown menu styling */
    .topbar-dropdown {
      position: relative;
    }
    .topbar-dropdown .dropdown-menu {
      background: var(--funSider, #333649);
      border: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15);
      border-radius: 10px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
      min-width: 220px;
      padding: 6px;
      margin-top: 8px;
      z-index: 999999;
    }
    .topbar-dropdown .dropdown-menu::before {
      content: '';
      position: absolute;
      top: -6px;
      right: 14px;
      width: 12px;
      height: 12px;
      background: var(--funSider, #333649);
      border-left: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15);
      border-top: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15);
      transform: rotate(45deg);
    }
    .topbar-dropdown .dropdown-menu li a {
      color: var(--cinza0, #a4a6b3);
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }
    .topbar-dropdown .dropdown-menu li a:hover {
      background: var(--sidebar-hover-bg, rgba(255,255,255,0.06));
      color: var(--sidebar-accent, #0467fc);
      transform: translateX(2px);
    }
    .topbar-dropdown .dropdown-menu li a i {
      font-size: 16px;
      width: 20px;
      text-align: center;
    }
    .topbar-dropdown .dropdown-menu .dropdown-divider {
      border-color: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.1);
      margin: 4px 8px;
    }

    /* Notification dropdown */
    .topbar-notif-dropdown .dropdown-menu {
      width: 360px;
      min-width: 360px;
      right: 0;
      left: auto !important;
      position: fixed !important;
      top: 62px !important;
    }
    .topbar-notif-dropdown .dropdown-menu::before {
      right: 8px;
    }

    /* User profile area */
    .topbar-user {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 4px 12px 4px 4px;
      border-radius: 10px;
      background: var(--sidebar-hover-bg, rgba(255,255,255,0.06));
      cursor: pointer;
      transition: all 0.2s ease;
      border: 1px solid transparent;
      margin-left: 4px;
    }
    .topbar-user:hover {
      background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.1);
      border-color: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.2);
    }
    .topbar-user-avatar {
      width: 32px;
      height: 32px;
      min-width: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--sidebar-accent, #0467fc), rgba(var(--sidebar-accent-rgb, 4,103,252), 0.6));
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.5px;
      overflow: hidden;
    }
    .topbar-user-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }
    .topbar-user-info {
      display: flex;
      flex-direction: column;
      min-width: 0;
    }
    .topbar-user-name {
      font-size: 12px;
      font-weight: 600;
      color: var(--cinza0, #a4a6b3);
      line-height: 1.2;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 140px;
    }
    .topbar-user-role {
      font-size: 10px;
      color: var(--sidebar-text-muted, #8a8a8a);
      line-height: 1.2;
    }
    .topbar-user:hover .topbar-user-name {
      color: var(--sidebar-accent, #0467fc);
    }
    .topbar-user-chevron {
      color: var(--sidebar-text-muted, #8a8a8a);
      font-size: 14px;
      transition: transform 0.2s ease;
    }
    .topbar-dropdown.open .topbar-user-chevron {
      transform: rotate(180deg);
    }

    /* Responsive */
    @media (max-width: 992px) {
      .navebarn { margin-left: 82px; }
      .topbar-user-info { display: none; }
      .topbar-user-chevron { display: none; }
    }
    @media (max-width: 768px) {
      .navebarn { margin-left: 0; padding: 0 12px; height: 50px; }
      .topbar-left .topbar-breadcrumb { display: none; }
    }

    /* Notification items */
    .notif-badge {
      position: absolute; top: 2px; right: 2px; background: #e74c3c; color: #fff;
      font-size: 10px; font-weight: bold; border-radius: 50%; min-width: 18px;
      height: 18px; line-height: 18px; text-align: center; padding: 0 4px;
    }
    .notif-item { padding: 8px 12px; border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.1); cursor: pointer; border-radius: 6px; margin: 2px 0; }
    .notif-item:hover { background: var(--sidebar-hover-bg, rgba(255,255,255,0.06)); }
    .notif-item.nao-lida { background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.08); }
    .notif-item .notif-titulo { font-weight: 600; font-size: 12px; margin-bottom: 2px; color: var(--cinza0, #a4a6b3); }
    .notif-item .notif-msg { font-size: 11px; color: var(--sidebar-text-muted, #8a8a8a); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .notif-item .notif-data { font-size: 10px; color: var(--sidebar-text-muted, #8a8a8a); margin-top: 2px; }
    .notif-item .notif-icone { margin-right: 8px; font-size: 16px; vertical-align: middle; color: var(--sidebar-accent, #0467fc); }
    .notif-header { color: var(--cinza0, #a4a6b3) !important; border-bottom-color: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15) !important; }
    .notif-header a { color: var(--sidebar-accent, #0467fc) !important; }

    #theme-toggle-btn a { cursor: pointer; }
    #notifications-dropdown { position: static !important; }
    #notifications-dropdown.open { position: relative !important; z-index: 999999 !important; }
  </style>
</head>

<body data-theme="<?= $configuration['app_theme'] ?? 'default' ?>">
  <!--top-Header-menu-->

  <?php if (isset($is_area_tecnico) && $is_area_tecnico): ?>
  <!-- Header para Area do Tecnico - Modern Design -->
  <div class="navebarn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-bottom-color: rgba(255,255,255,0.1);">
    <!-- Left Section -->
    <div class="topbar-left" style="display: flex; align-items: center;">
      <a href="#" id="sidebar-toggle-mobile" class="topbar-toggle-btn" title="Menu" style="margin-right: 15px; color: #fff; font-size: 24px; display: flex; align-items: center; justify-content: center;">
        <i class='bx bx-menu'></i>
      </a>
      <div class="topbar-breadcrumb" style="color:rgba(255,255,255,0.85); display: flex; align-items: center;">
        <span style="color:#fff;">Portal Tecnico</span>
      </div>
    </div>

    <!-- Right Section -->
    <div class="topbar-right">
      <!-- Theme Toggle -->
      <div class="topbar-action" id="theme-toggle-btn">
        <a href="#" title="Alternar Tema" id="btn-toggle-theme" style="color:rgba(255,255,255,0.8);text-decoration:none;">
          <i class='bx bx-sun' id="theme-icon" style="font-size:20px;"></i>
        </a>
      </div>

      <!-- Notifications -->
      <div class="topbar-dropdown topbar-notif-dropdown" id="notifications-dropdown">
        <a href="#" class="topbar-action" data-bs-toggle="dropdown" title="Notificacoes" style="color:rgba(255,255,255,0.8);">
          <i class='bx bx-bell'></i>
          <span class="topbar-badge" id="notif-count" style="display:none;">0</span>
        </a>
        <ul class="dropdown-menu" id="notif-list">
          <li class="notif-header" style="padding:10px 14px;font-weight:600;border-bottom:1px solid rgba(255,255,255,0.1);display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;">Notificacoes</span>
            <a href="#" id="notif-marcar-todas" style="font-size:11px;font-weight:500;">Marcar todas</a>
          </li>
          <li id="notif-items" style="max-height:320px;overflow-y:auto;padding:4px;">
            <div style="padding:20px;text-align:center;font-size:13px;opacity:0.5;">
              <i class='bx bx-bell' style="font-size:32px;"></i><br>
              Carregando...
            </div>
          </li>
        </ul>
      </div>

      <!-- User Profile Dropdown -->
      <div class="topbar-dropdown">
        <a href="#" class="topbar-user" data-bs-toggle="dropdown" style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.15);">
          <div class="topbar-user-avatar" style="background:rgba(255,255,255,0.2);">
            <?= strtoupper(mb_substr($this->session->userdata('tec_nome') ?? 'T', 0, 2)) ?>
          </div>
          <div class="topbar-user-info">
            <div class="topbar-user-name" style="color:rgba(255,255,255,0.9);"><?= e($this->session->userdata('tec_nome') ?? 'Tecnico') ?></div>
            <div class="topbar-user-role" style="color:rgba(255,255,255,0.6);">Tecnico</div>
          </div>
          <i class='bx bx-chevron-down topbar-user-chevron' style="color:rgba(255,255,255,0.6);"></i>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?= site_url('tecnicos/dashboard') ?>"><i class='bx bx-home-alt'></i> Dashboard</a></li>
          <li><a href="<?= site_url('tecnicos/perfil') ?>"><i class='bx bx-user'></i> Meu Perfil</a></li>
          <li class="dropdown-divider"></li>
          <li><a href="<?= site_url('tecnicos/logout') ?>" style="color:#e05555;"><i class='bx bx-log-out-circle'></i> Sair</a></li>
        </ul>
      </div>
    </div>
  </div>
  <!-- End Header Tecnico -->

  <?php else: ?>
  <!-- Header Padrão (Admin) - Modern Design -->
  <div class="navebarn">
    <!-- Left Section -->
    <div class="topbar-left" style="display: flex; align-items: center;">
      <a href="#" id="sidebar-toggle-mobile-admin" class="topbar-toggle-btn" title="Menu" style="margin-right: 15px; font-size: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
        <i class='bx bx-menu'></i>
      </a>
      <div class="topbar-breadcrumb" style="display: flex; align-items: center;">
        <span><?= $configuration['app_name'] ?? 'Map-OS' ?></span>
      </div>
    </div>

    <!-- Right Section: Actions + User -->
    <div class="topbar-right">
      <!-- Theme Toggle -->
      <div class="topbar-action" id="theme-toggle-btn">
        <a href="#" title="Alternar Tema" id="btn-toggle-theme" style="color:inherit;text-decoration:none;">
          <i class='bx bx-sun' id="theme-icon" style="font-size:20px;"></i>
        </a>
      </div>

      <!-- Notifications -->
      <div class="topbar-dropdown topbar-notif-dropdown" id="notifications-dropdown">
        <a href="#" class="topbar-action" data-bs-toggle="dropdown" title="Notificacoes">
          <i class='bx bx-bell'></i>
          <span class="topbar-badge" id="notif-count" style="display:none;">0</span>
        </a>
        <ul class="dropdown-menu" id="notif-list">
          <li class="notif-header" style="padding:10px 14px;font-weight:600;border-bottom:1px solid rgba(var(--sidebar-accent-rgb,4,103,252),0.15);display:flex;justify-content:space-between;align-items:center;">
            <span style="color:var(--cinza0,#a4a6b3);font-size:13px;">Notificacoes</span>
            <a href="#" id="notif-marcar-todas" style="font-size:11px;font-weight:500;color:var(--sidebar-accent,#0467fc);">Marcar todas</a>
          </li>
          <li id="notif-items" style="max-height:320px;overflow-y:auto;padding:4px;">
            <div style="padding:20px;text-align:center;color:var(--sidebar-text-muted,#8a8a8a);font-size:13px;">
              <i class='bx bx-bell' style="font-size:32px;opacity:0.3;"></i><br>
              Carregando...
            </div>
          </li>
        </ul>
      </div>

      <!-- Reports Dropdown -->
      <div class="topbar-dropdown">
        <a href="#" class="topbar-action" data-bs-toggle="dropdown" title="Relatorios">
          <i class='bx bx-pie-chart-alt-2'></i>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?= site_url('relatorios/clientes') ?>"><i class='bx bx-user'></i> Clientes</a></li>
          <li><a href="<?= site_url('relatorios/produtos') ?>"><i class='bx bx-basket'></i> Produtos</a></li>
          <li><a href="<?= site_url('relatorios/servicos') ?>"><i class='bx bx-wrench'></i> Servicos</a></li>
          <li><a href="<?= site_url('relatorios/os') ?>"><i class='bx bx-file'></i> Ordens de Servico</a></li>
          <li><a href="<?= site_url('relatorios/vendas') ?>"><i class='bx bx-cart-alt'></i> Vendas</a></li>
          <li><a href="<?= site_url('relatorios/financeiro') ?>"><i class='bx bx-bar-chart-alt-2'></i> Financeiro</a></li>
          <li class="dropdown-divider"></li>
          <li><a href="<?= site_url('relatorios/sku') ?>"><i class='bx bx-barcode'></i> SKU</a></li>
          <li><a href="<?= site_url('relatorios/receitasBrutasMei') ?>"><i class='bx bx-line-chart'></i> Receitas Brutas MEI</a></li>
        </ul>
      </div>

      <!-- Quick Settings Dropdown -->
      <div class="topbar-dropdown">
        <a href="#" class="topbar-action" data-bs-toggle="dropdown" title="Configuracoes Rapidas">
          <i class='bx bx-cog'></i>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?= site_url('mapos/configurar') ?>"><i class='bx bx-slider-alt'></i> Sistema</a></li>
          <li><a href="<?= site_url('mapos/emitente') ?>"><i class='bx bx-building'></i> Emitente</a></li>
          <li><a href="<?= site_url('usuarios') ?>"><i class='bx bx-user-circle'></i> Usuarios</a></li>
          <li><a href="<?= site_url('permissoes') ?>"><i class='bx bx-shield-quarter'></i> Permissoes</a></li>
          <li class="dropdown-divider"></li>
          <li><a href="<?= site_url('backup') ?>"><i class='bx bx-data'></i> Backup</a></li>
          <li><a href="<?= site_url('auditoria') ?>"><i class='bx bx-file-find'></i> Auditoria</a></li>
          <li><a href="<?= site_url('diagnostico') ?>"><i class='bx bx-bug'></i> Diagnostico</a></li>
          <li class="dropdown-divider"></li>
          <li><a href="<?= site_url('email/logs') ?>"><i class='bx bx-envelope'></i> Emails</a></li>
        </ul>
      </div>

      <!-- User Profile Dropdown -->
      <div class="topbar-dropdown">
        <a href="#" class="topbar-user" data-bs-toggle="dropdown">
          <div class="topbar-user-avatar">
            <?= strtoupper(mb_substr($this->session->userdata('nome') ?? 'U', 0, 2)) ?>
          </div>
          <div class="topbar-user-info">
            <div class="topbar-user-name"><?= e($this->session->userdata('nome') ?? 'Usuario') ?></div>
            <div class="topbar-user-role">
              <?php
              $perm_id = $this->session->userdata('permissao');
              $roles = [1 => 'Administrador', 2 => 'Gerente'];
              echo $roles[$perm_id] ?? 'Usuario';
              ?>
            </div>
          </div>
          <i class='bx bx-chevron-down topbar-user-chevron'></i>
        </a>
        <ul class="dropdown-menu" style="min-width:200px;">
          <li><a href="<?= site_url('mapos/minhaConta') ?>"><i class='bx bx-user'></i> Meu Perfil</a></li>
          <li><a href="<?= site_url() ?>/mine" target="_blank"><i class='bx bx-globe'></i> Area do Cliente</a></li>
          <li class="dropdown-divider"></li>
          <li><a href="<?= site_url('login/sair') ?>" style="color:#e05555;"><i class='bx bx-log-out-circle'></i> Sair do Sistema</a></li>
        </ul>
      </div>
    </div>
  </div>
  <!-- End Header Admin -->
  <?php endif; ?>

  <!-- Search overlay (hidden, uses sidebar search) -->

  <script>
    $(document).ready(function() {
      $('#sidebar-toggle-mobile, #sidebar-toggle-mobile-admin').click(function(e) {
        e.preventDefault();
        var sidebar = $('#sidebar');
        var ul = $('#sidebar > ul');
        if (sidebar.hasClass('open')) {
          sidebar.removeClass('open');
          ul.slideUp(250);
        } else {
          sidebar.addClass('open');
          ul.slideDown(250);
        }
      });
    });
  </script>
