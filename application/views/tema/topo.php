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
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-style.css?v=<?= filemtime(FCPATH . 'assets/css/matrix-style.css') ?>" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-media.css" />
  <link href="<?= base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/fullcalendar.css" />
  <?php if (($configuration['app_theme'] ?? null) == 'white') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white.css" />
  <?php } ?>
  <?php if (($configuration['app_theme'] ?? null) == 'puredark') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-pure-dark.css?v=<?= filemtime(FCPATH . 'assets/css/tema-pure-dark.css') ?>" />
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
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/custom.css?v=<?= filemtime(FCPATH . 'assets/css/custom.css') ?>" />
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
       TOPBAR / HEADER - Modern Navigation Bar
       ============================================ */
    .navebarn {
      display: flex;
      align-items: center;
      padding: 0 16px;
      height: 52px;
      background: var(--funSider, #333649);
      border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15);
      position: relative;
      z-index: 100;
    }

    /* Brand */
    .topbar-brand {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-right: 16px;
      flex-shrink: 0;
    }
    .topbar-brand-logo {
      width: 32px;
      height: 32px;
      object-fit: contain;
      border-radius: 6px;
      filter: drop-shadow(0 2px 6px rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.3));
      transition: transform 0.2s ease;
    }
    .topbar-brand:hover .topbar-brand-logo {
      transform: scale(1.1);
    }
    .topbar-brand-name {
      font-size: 15px;
      font-weight: 700;
      color: var(--sidebar-accent, #0467fc);
      white-space: nowrap;
      letter-spacing: 0.3px;
    }

    /* Navigation links (center) */
    .topbar-nav {
      display: flex;
      align-items: center;
      gap: 2px;
      flex: 1;
      min-width: 0;
    }

    .topbar-nav-link {
      display: flex;
      align-items: center;
      gap: 5px;
      padding: 6px 10px;
      border-radius: 8px;
      color: var(--cinza0, #a4a6b3);
      font-size: 13px;
      font-weight: 500;
      white-space: nowrap;
      text-decoration: none;
      transition: all 0.2s ease;
      cursor: pointer;
      border: none;
      background: transparent;
    }
    .topbar-nav-link:hover,
    .topbar-nav-link:focus {
      background: var(--sidebar-hover-bg, rgba(255,255,255,0.06));
      color: var(--sidebar-accent, #0467fc);
    }
    .topbar-nav-link .svg-icon {
      flex-shrink: 0;
    }
    .topbar-nav-link .nav-chevron {
      font-size: 10px;
      opacity: 0.5;
      transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .topbar-nav-link[aria-expanded="true"] .nav-chevron {
      transform: rotate(180deg);
      opacity: 1;
    }
    .topbar-nav-link.active {
      background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.12);
      color: var(--sidebar-accent, #0467fc);
    }

    /* Navigation dropdowns */
    .topbar-nav-dropdown {
      position: relative;
    }
    .topbar-nav-dropdown .dropdown-menu {
      display: none;
      background: var(--funSider, #333649);
      border: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15);
      border-radius: 10px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
      min-width: 220px;
      max-width: 280px;
      padding: 6px;
      margin-top: 6px;
      z-index: 999999;
      position: absolute;
      top: 100%;
      left: 0;
    }
    /* Show dropdown on click (via .show class from BS5 JS) OR on hover */
    .topbar-nav-dropdown:hover .dropdown-menu,
    .topbar-nav-dropdown .dropdown-menu.show {
      display: block !important;
      animation: dropdownFadeIn 0.15s ease-out;
    }
    @keyframes dropdownFadeIn {
      from { opacity: 0; transform: translateY(-4px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .topbar-nav-dropdown .dropdown-menu::before {
      content: '';
      position: absolute;
      top: -6px;
      left: 16px;
      width: 12px;
      height: 12px;
      background: var(--funSider, #333649);
      border-left: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15);
      border-top: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15);
      transform: rotate(45deg);
      z-index: -1;
    }
    .topbar-nav-dropdown .dropdown-menu li a {
      color: var(--cinza0, #a4a6b3);
      padding: 7px 10px;
      border-radius: 6px;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.15s ease;
    }
    .topbar-nav-dropdown .dropdown-menu li a:hover {
      background: var(--sidebar-hover-bg, rgba(255,255,255,0.06));
      color: var(--sidebar-accent, #0467fc);
      transform: translateX(2px);
    }
    .topbar-nav-dropdown .dropdown-menu li a .svg-icon {
      width: 18px;
      flex-shrink: 0;
    }
    .topbar-nav-dropdown .dropdown-menu .dropdown-divider {
      border-color: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.1);
      margin: 4px 8px;
    }
    .topbar-nav-dropdown .dropdown-menu .dropdown-header {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--sidebar-text-muted, #8a8a8a);
      padding: 6px 10px 3px;
    }

    /* Right section */
    .topbar-right {
      display: flex;
      align-items: center;
      gap: 4px;
      flex-shrink: 0;
      margin-left: auto;
    }

    /* Search */
    .topbar-search {
      position: relative;
      display: flex;
      align-items: center;
    }
    .topbar-search-input {
      width: 0;
      padding: 0;
      border: none;
      background: transparent;
      color: var(--cinza0, #a4a6b3);
      font-size: 13px;
      height: 34px;
      border-radius: 8px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      outline: none;
    }
    .topbar-search-input:focus {
      width: 200px;
      padding: 0 10px;
      background: var(--sidebar-hover-bg, rgba(255,255,255,0.06));
      border: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.2);
      margin-right: 6px;
    }
    .topbar-search-input::placeholder {
      color: var(--sidebar-text-muted, #8a8a8a);
    }

    /* Topbar action button (icon buttons) */
    .topbar-action {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      border-radius: 8px;
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

    /* Badge */
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

    /* Notification dropdown */
    .topbar-notif-dropdown .dropdown-menu {
      width: 360px;
      min-width: 360px;
      right: 0;
      left: auto !important;
      position: fixed !important;
      top: 58px !important;
    }
    .topbar-notif-dropdown .dropdown-menu::before {
      right: 8px;
      left: auto;
    }

    /* User profile area */
    .topbar-user {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 3px 10px 3px 3px;
      border-radius: 8px;
      background: var(--sidebar-hover-bg, rgba(255,255,255,0.06));
      cursor: pointer;
      transition: all 0.2s ease;
      border: 1px solid transparent;
    }
    .topbar-user:hover {
      background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.1);
      border-color: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.2);
    }
    .topbar-user-avatar {
      width: 30px;
      height: 30px;
      min-width: 30px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--sidebar-accent, #0467fc), rgba(var(--sidebar-accent-rgb, 4,103,252), 0.6));
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 11px;
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
      max-width: 120px;
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
      font-size: 12px;
      transition: transform 0.2s ease;
      margin-left: 2px;
    }
    .topbar-dropdown.show .topbar-user-chevron {
      transform: rotate(180deg);
    }

    /* Mobile hamburger */
    .topbar-hamburger {
      display: none;
      width: 36px;
      height: 36px;
      border-radius: 8px;
      border: none;
      background: transparent;
      color: var(--cinza0, #a4a6b3);
      cursor: pointer;
      align-items: center;
      justify-content: center;
      margin-right: 8px;
    }

    /* Notification items */
    .notif-item { padding: 8px 12px; border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4,103,252), 0.1); cursor: pointer; border-radius: 6px; margin: 2px 0; }
    .notif-item:hover { background: var(--sidebar-hover-bg, rgba(255,255,255,0.06)); }
    .notif-item.nao-lida { background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.08); }
    .notif-item .notif-titulo { font-weight: 600; font-size: 12px; margin-bottom: 2px; color: var(--cinza0, #a4a6b3); }
    .notif-item .notif-msg { font-size: 11px; color: var(--sidebar-text-muted, #8a8a8a); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .notif-item .notif-data { font-size: 10px; color: var(--sidebar-text-muted, #8a8a8a); margin-top: 2px; }
    .notif-item .notif-icone { margin-right: 8px; vertical-align: middle; color: var(--sidebar-accent, #0467fc); }
    .notif-header { color: var(--cinza0, #a4a6b3) !important; border-bottom-color: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.15) !important; }
    .notif-header a { color: var(--sidebar-accent, #0467fc) !important; }

    #theme-toggle-btn a { cursor: pointer; }
    #notifications-dropdown { position: static !important; }
    #notifications-dropdown.open { position: relative !important; z-index: 999999 !important; }

    /* Responsive */
    @media (max-width: 992px) {
      .topbar-user-info { display: none; }
      .topbar-user-chevron { display: none; }
      .topbar-nav { display: none; }
      .topbar-hamburger { display: flex; }
    }
    @media (min-width: 993px) {
      #mobileNavOffcanvas { display: none !important; }
    }

    /* Offcanvas for mobile nav */
    .offcanvas-nav .offcanvas-body {
      padding: 12px;
    }
    .offcanvas-nav .nav-section-title {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--sidebar-text-muted, #8a8a8a);
      padding: 10px 8px 4px;
    }
    .offcanvas-nav .nav-link-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 10px;
      border-radius: 8px;
      color: var(--cinza0, #a4a6b3);
      font-size: 14px;
      text-decoration: none;
      transition: all 0.15s ease;
    }
    .offcanvas-nav .nav-link-item:hover {
      background: var(--sidebar-hover-bg, rgba(255,255,255,0.06));
      color: var(--sidebar-accent, #0467fc);
    }
    .offcanvas-nav .nav-link-item.active {
      background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.12);
      color: var(--sidebar-accent, #0467fc);
    }
    .offcanvas-nav .nav-link-sub {
      padding-left: 24px;
      font-size: 13px;
      color: var(--sidebar-text-muted, #8a8a8a);
    }
  </style>
</head>

<body data-theme="<?= $configuration['app_theme'] ?? 'default' ?>">

  <?php if (isset($is_area_tecnico) && $is_area_tecnico): ?>
  <!-- Header para Area do Tecnico -->
  <div class="navebarn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-bottom-color: rgba(255,255,255,0.1);">
    <div class="topbar-brand">
      <div style="filter:drop-shadow(0 2px 8px rgba(255,255,255,0.3))">
        <?= svg_icon('hard-hat', 24, 24, '', 'color:#fff') ?>
      </div>
      <span class="topbar-brand-name" style="color:#fff;">Portal Tecnico</span>
    </div>

    <div class="topbar-right">
      <div class="topbar-action" id="theme-toggle-btn">
        <a href="#" title="Alternar Tema" id="btn-toggle-theme" style="color:rgba(255,255,255,0.8);text-decoration:none;">
          <span id="theme-icon" class="svg-icon-wrap"><?= svg_icon('sun', 20, 20) ?></span>
        </a>
      </div>
      <div class="topbar-dropdown topbar-notif-dropdown" id="notifications-dropdown">
        <a href="#" class="topbar-action btn" data-bs-toggle="dropdown" title="Notificacoes" style="color:rgba(255,255,255,0.8);">
          <?= svg_icon('bell', 22, 22) ?>
          <span class="topbar-badge" id="notif-count" style="display:none;">0</span>
        </a>
        <ul class="dropdown-menu" id="notif-list">
          <li class="notif-header" style="padding:10px 14px;font-weight:600;border-bottom:1px solid rgba(255,255,255,0.1);display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;">Notificacoes</span>
            <a href="#" id="notif-marcar-todas" style="font-size:11px;font-weight:500;">Marcar todas</a>
          </li>
          <li id="notif-items" style="max-height:320px;overflow-y:auto;padding:4px;">
            <div style="padding:20px;text-align:center;font-size:13px;opacity:0.5;">
              <?= svg_icon('bell', 32, 32) ?><br>
              Carregando...
            </div>
          </li>
        </ul>
      </div>
      <div class="topbar-dropdown">
        <a href="#" class="topbar-user btn" data-bs-toggle="dropdown" style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.15);">
          <div class="topbar-user-avatar" style="background:rgba(255,255,255,0.2);">
            <?= strtoupper(mb_substr($this->session->userdata('tec_nome') ?? 'T', 0, 2)) ?>
          </div>
          <div class="topbar-user-info">
            <div class="topbar-user-name" style="color:rgba(255,255,255,0.9);"><?= e($this->session->userdata('tec_nome') ?? 'Tecnico') ?></div>
            <div class="topbar-user-role" style="color:rgba(255,255,255,0.6);">Tecnico</div>
          </div>
          <?= svg_icon('chevron-down', 14, 14, 'topbar-user-chevron', 'color:rgba(255,255,255,0.6)') ?>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?= site_url('tecnicos/dashboard') ?>"><?= svg_icon('home-alt', 16, 16) ?> Dashboard</a></li>
          <li><a href="<?= site_url('tecnicos/perfil') ?>"><?= svg_icon('user', 16, 16) ?> Meu Perfil</a></li>
          <li class="dropdown-divider"></li>
          <li><a href="<?= site_url('tecnicos/logout') ?>" style="color:#e05555;"><?= svg_icon('log-out', 16, 16) ?> Sair</a></li>
        </ul>
      </div>
    </div>
  </div>

  <?php else: ?>
  <!-- Header Padrao (Admin) - Navegacao Completa -->
  <div class="navebarn">
    <!-- Mobile hamburger -->
    <button class="topbar-hamburger" data-bs-toggle="offcanvas" data-bs-target="#mobileNavOffcanvas" aria-label="Menu">
      <?= svg_icon('menu', 22, 22) ?>
    </button>

    <!-- Brand -->
    <div class="topbar-brand">
      <?php
      $emitente = $this->db->get('emitente')->row();
      $topbarLogo = (!empty($emitente->url_logo)) ? $emitente->url_logo : null;
      ?>
      <?php if ($topbarLogo): ?>
        <img src="<?= e($topbarLogo) ?>" alt="<?= e($configuration['app_name'] ?? 'Map-OS') ?>" class="topbar-brand-logo">
      <?php else: ?>
        <?= svg_icon('cube-alt', 22, 22, '', 'color:var(--sidebar-accent, #0467fc);filter:drop-shadow(0 2px 6px rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.3))') ?>
      <?php endif; ?>
      <span class="topbar-brand-name"><?= $configuration['app_name'] ?? 'Map-OS' ?></span>
    </div>

    <!-- Navigation Dropdowns -->
    <div class="topbar-nav">
      <!-- Inicio -->
      <a href="<?= base_url() ?>" class="topbar-nav-link <?php if (isset($menuPainel)) { echo 'active'; }; ?>">
        <?= svg_icon('home', 16, 16) ?> Inicio
      </a>

      <!-- Obras -->
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vObras') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
      <div class="topbar-nav-dropdown dropdown">
        <a href="#" class="topbar-nav-link btn <?php if (isset($menuObras) || isset($menuObrasAdd) || isset($menuObrasTecnico)) { echo 'active'; }; ?>" data-bs-toggle="dropdown" data-bs-auto-close="true">
          <?= svg_icon('building-house', 16, 16) ?> Obras <?= svg_icon('chevron-down', 10, 10, 'nav-chevron') ?>
        </a>
        <ul class="dropdown-menu">
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vObras')) { ?>
          <li><a href="<?= site_url('obras') ?>"><?= svg_icon('building-house', 16, 16) ?> Gerenciar Obras</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
          <li><a href="<?= site_url('obras/adicionar') ?>"><?= svg_icon('plus-circle', 16, 16) ?> Nova Obra</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicoObra') && !$this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
          <li><a href="<?= site_url('obras_tecnico') ?>"><?= svg_icon('hard-hat', 16, 16) ?> Minhas Obras</a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>

      <!-- Ordens de Servico -->
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs') || $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
      <div class="topbar-nav-dropdown dropdown">
        <a href="#" class="topbar-nav-link btn <?php if (isset($menuOs) || isset($menuKanban) || isset($menuAtribuir) || isset($menuGarantia)) { echo 'active'; }; ?>" data-bs-toggle="dropdown" data-bs-auto-close="true">
          <?= svg_icon('file', 16, 16) ?> OS <?= svg_icon('chevron-down', 10, 10, 'nav-chevron') ?>
        </a>
        <ul class="dropdown-menu">
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
          <li><a href="<?= site_url('os') ?>"><?= svg_icon('file', 16, 16) ?> Todas as OS</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
          <li><a href="<?= site_url('kanban') ?>"><?= svg_icon('columns', 16, 16) ?> Kanban Board</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
          <li><a href="<?= site_url('os/atribuir') ?>"><?= svg_icon('user-plus', 16, 16) ?> Atribuir Tecnico</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vGarantia')) { ?>
          <li><a href="<?= site_url('garantias') ?>"><?= svg_icon('receipt', 16, 16) ?> Garantias</a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>

      <!-- Cadastros -->
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vServico') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) { ?>
      <div class="topbar-nav-dropdown dropdown">
        <a href="#" class="topbar-nav-link btn <?php if (isset($menuClientes) || isset($menuProdutos) || isset($menuServicos) || isset($menuVendas) || isset($menuTecnicosAdmin)) { echo 'active'; }; ?>" data-bs-toggle="dropdown" data-bs-auto-close="true">
          <?= svg_icon('layers', 16, 16) ?> Cadastros <?= svg_icon('chevron-down', 10, 10, 'nav-chevron') ?>
        </a>
        <ul class="dropdown-menu">
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
          <li><a href="<?= site_url('clientes') ?>"><?= svg_icon('user', 16, 16) ?> Clientes</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
          <li><a href="<?= site_url('produtos') ?>"><?= svg_icon('basket', 16, 16) ?> Produtos</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
          <li><a href="<?= site_url('servicos') ?>"><?= svg_icon('wrench', 16, 16) ?> Servicos</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
          <li><a href="<?= site_url('vendas') ?>"><?= svg_icon('cart', 16, 16) ?> Vendas</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) { ?>
          <li><a href="<?= site_url('tecnicos_admin') ?>"><?= svg_icon('hard-hat', 16, 16) ?> Tecnicos</a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>

      <!-- Financeiro -->
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
      <div class="topbar-nav-dropdown dropdown">
        <a href="#" class="topbar-nav-link btn <?php if (isset($menuLancamentos) || isset($menuCobrancas)) { echo 'active'; }; ?>" data-bs-toggle="dropdown" data-bs-auto-close="true">
          <?= svg_icon('bar-chart-alt', 16, 16) ?> Financeiro <?= svg_icon('chevron-down', 10, 10, 'nav-chevron') ?>
        </a>
        <ul class="dropdown-menu">
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
          <li><a href="<?= site_url('financeiro/lancamentos') ?>"><?= svg_icon('bar-chart-alt', 16, 16) ?> Lancamentos</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
          <li><a href="<?= site_url('cobrancas/cobrancas') ?>"><?= svg_icon('credit-card', 16, 16) ?> Cobrancas</a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>

      <!-- Doc. Fiscais -->
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
      <div class="topbar-nav-dropdown dropdown">
        <a href="#" class="topbar-nav-link btn <?php if (isset($menuNfseOsDashboard) || isset($menuNfseOsRelatorio) || isset($menuCertificado) || isset($menuImpostos)) { echo 'active'; }; ?>" data-bs-toggle="dropdown" data-bs-auto-close="true">
          <?= svg_icon('receipt', 16, 16) ?> Doc. Fiscais <?= svg_icon('chevron-down', 10, 10, 'nav-chevron') ?>
        </a>
        <ul class="dropdown-menu">
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) { ?>
          <li><a href="<?= site_url('nfse_os') ?>"><?= svg_icon('receipt', 16, 16) ?> NFSe Dashboard</a></li>
          <li><a href="<?= site_url('nfse_os/relatorio') ?>"><?= svg_icon('chart', 16, 16) ?> Relatorio NFSe/Boletos</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
          <li class="dropdown-header">Certificado Digital</li>
          <li><a href="<?= site_url('certificado') ?>"><?= svg_icon('shield-check', 16, 16) ?> Status</a></li>
          <li><a href="<?= site_url('certificado/configurar') ?>"><?= svg_icon('cog', 16, 16) ?> Configurar</a></li>
          <li><a href="<?= site_url('nfse') ?>"><?= svg_icon('receipt', 16, 16) ?> NFS-e Importadas</a></li>
          <li><a href="<?= site_url('certificado/importar_nfse') ?>"><?= svg_icon('import', 16, 16) ?> Importar NFS-e</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
          <li class="dropdown-divider"></li>
          <li class="dropdown-header">Impostos Simples</li>
          <li><a href="<?= site_url('impostos') ?>"><?= svg_icon('chart', 16, 16) ?> Dashboard</a></li>
          <li><a href="<?= site_url('impostos/configuracoes') ?>"><?= svg_icon('cog', 16, 16) ?> Configuracoes</a></li>
          <li><a href="<?= site_url('impostos/simulador') ?>"><?= svg_icon('calculator', 16, 16) ?> Simulador</a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>

      <!-- Relatorios -->
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
      <div class="topbar-nav-dropdown dropdown">
        <a href="#" class="topbar-nav-link btn <?php if (isset($menuRelatorioAtendimentos) || isset($menuRelTecnicos) || isset($menuRelFinanceiro) || isset($menuRelProdutos) || isset($menuRelClientes) || isset($menuDRE)) { echo 'active'; }; ?>" data-bs-toggle="dropdown" data-bs-auto-close="true">
          <?= svg_icon('pie-chart', 16, 16) ?> Relatorios <?= svg_icon('chevron-down', 10, 10, 'nav-chevron') ?>
        </a>
        <ul class="dropdown-menu">
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos')) { ?>
          <li><a href="<?= site_url('relatorioatendimentos') ?>"><?= svg_icon('time', 16, 16) ?> Atendimentos</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
          <li><a href="<?= site_url('relatoriotecnicos') ?>"><?= svg_icon('hard-hat', 16, 16) ?> Performance Tecnicos</a></li>
          <li><a href="<?= site_url('dashboard/relatorio_financeiro') ?>"><?= svg_icon('dollar-circle', 16, 16) ?> Financeiro</a></li>
          <li><a href="<?= site_url('dashboard/relatorio_produtos') ?>"><?= svg_icon('package', 16, 16) ?> Produtos</a></li>
          <li><a href="<?= site_url('dashboard/relatorio_clientes') ?>"><?= svg_icon('user-check', 16, 16) ?> Clientes</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
          <li class="dropdown-divider"></li>
          <li class="dropdown-header">DRE Contabil</li>
          <li><a href="<?= site_url('dre') ?>"><?= svg_icon('bar-chart-alt', 16, 16) ?> Demonstracao</a></li>
          <li><a href="<?= site_url('dre/contas') ?>"><?= svg_icon('list-ul', 16, 16) ?> Plano de Contas</a></li>
          <li><a href="<?= site_url('dre/lancamentos') ?>"><?= svg_icon('book', 16, 16) ?> Lancamentos</a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>

      <!-- Configuracoes -->
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'cAuditoria') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) { ?>
      <div class="topbar-nav-dropdown dropdown">
        <a href="#" class="topbar-nav-link btn <?php if (isset($menuUsuarios) || isset($menuPermissoes) || isset($menuArquivos) || isset($menuEmitente) || isset($menuConfigSistema) || isset($menuModulos) || isset($menuBackup) || isset($menuAuditoria) || isset($menuConfiguracoesNotificacoes) || isset($menuMigrate) || isset($menuDiagnostico) || isset($menuWebhooks) || isset($menuApiDocs)) { echo 'active'; }; ?>" data-bs-toggle="dropdown" data-bs-auto-close="true">
          <?= svg_icon('cog', 16, 16) ?> Configuracoes <?= svg_icon('chevron-down', 10, 10, 'nav-chevron') ?>
        </a>
        <ul class="dropdown-menu" style="min-width:240px; max-height:70vh; overflow-y:auto;">
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) { ?>
          <li><a href="<?= site_url('usuarios') ?>"><?= svg_icon('user-circle', 16, 16) ?> Usuarios</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
          <li><a href="<?= site_url('permissoes') ?>"><?= svg_icon('shield-check', 16, 16) ?> Permissoes</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente')) { ?>
          <li class="dropdown-header">Usuarios Cliente</li>
          <li><a href="<?= site_url('usuarioscliente') ?>"><?= svg_icon('list-ul', 16, 16) ?> Listar Usuarios</a></li>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuariosCliente')) { ?>
          <li><a href="<?= site_url('usuarioscliente/adicionar') ?>"><?= svg_icon('plus', 16, 16) ?> Novo Usuario</a></li>
          <?php } ?>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
          <li class="dropdown-divider"></li>
          <li><a href="<?= site_url('arquivos') ?>"><?= svg_icon('box', 16, 16) ?> Arquivos</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) { ?>
          <li><a href="<?= site_url('mapos/emitente') ?>"><?= svg_icon('building', 16, 16) ?> Emitente</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema')) { ?>
          <li><a href="<?= site_url('mapos/configurar') ?>"><?= svg_icon('slider-alt', 16, 16) ?> Config. Sistema</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
          <li><a href="<?= site_url('modulos') ?>"><?= svg_icon('extension', 16, 16) ?> Modulos</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) { ?>
          <li class="dropdown-divider"></li>
          <li><a href="<?= site_url('backup') ?>"><?= svg_icon('database', 16, 16) ?> Backup</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cAuditoria')) { ?>
          <li><a href="<?= site_url('auditoria') ?>"><?= svg_icon('file-find', 16, 16) ?> Auditoria</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) { ?>
          <li class="dropdown-header">Comunicacao</li>
          <li><a href="<?= site_url('notificacoes/configuracoes') ?>"><?= svg_icon('whatsapp', 16, 16) ?> Notificacoes</a></li>
          <li><a href="<?= site_url('notificacoes/templates') ?>"><?= svg_icon('message-square-dots', 16, 16) ?> Templates</a></li>
          <li><a href="<?= site_url('notificacoes/logs') ?>"><?= svg_icon('history', 16, 16) ?> Historico</a></li>
          <?php } ?>
          <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
          <li class="dropdown-header">Administracao</li>
          <li><a href="<?= site_url('migrate') ?>"><?= svg_icon('database', 16, 16) ?> Migracoes DB</a></li>
          <li><a href="<?= site_url('diagnostico') ?>"><?= svg_icon('bug', 16, 16) ?> Diagnostico</a></li>
          <li><a href="<?= site_url('emails/dashboard') ?>"><?= svg_icon('envelope', 16, 16) ?> Fila de Emails</a></li>
          <li><a href="<?= site_url('email/configuracoes') ?>"><?= svg_icon('cog', 16, 16) ?> Config. Emails</a></li>
          <li><a href="<?= site_url('webhooks') ?>"><?= svg_icon('webhook', 16, 16) ?> Webhooks</a></li>
          <li><a href="<?= site_url('webhooks/docs') ?>" target="_blank"><?= svg_icon('book-open', 16, 16) ?> Docs Webhooks</a></li>
          <li><a href="<?= site_url('api/docs') ?>"><?= svg_icon('code-alt', 16, 16) ?> API v2</a></li>
          <li><a href="<?= site_url('agente_ia') ?>"><?= svg_icon('bot', 16, 16) ?> Agente IA</a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>

      <!-- Tecnico area (non-admin) -->
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicoDashboard') &&
                !$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
      <div class="topbar-nav-dropdown dropdown">
        <a href="#" class="topbar-nav-link btn" data-bs-toggle="dropdown" data-bs-auto-close="true">
          <?= svg_icon('hard-hat', 16, 16) ?> Tecnico <?= svg_icon('chevron-down', 10, 10, 'nav-chevron') ?>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?= site_url('atividades') ?>"><?= svg_icon('timer', 16, 16) ?> Minhas Atividades</a></li>
          <li><a href="<?= site_url('tecnico') ?>"><?= svg_icon('hard-hat', 16, 16) ?> Acessar Portal</a></li>
        </ul>
      </div>
      <?php } ?>

      <!-- Admin: Registro de Atividades -->
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
      <div class="topbar-nav-dropdown dropdown">
        <a href="#" class="topbar-nav-link btn <?php if (isset($menuAtividadesDashboard) || isset($menuAtividadesRelatorio)) { echo 'active'; }; ?>" data-bs-toggle="dropdown" data-bs-auto-close="true">
          <?= svg_icon('timer', 16, 16) ?> Atividades <?= svg_icon('chevron-down', 10, 10, 'nav-chevron') ?>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?= site_url('atividades') ?>"><?= svg_icon('timer', 16, 16) ?> Dashboard Atividades</a></li>
          <li><a href="<?= site_url('atividades/relatorio') ?>"><?= svg_icon('chart', 16, 16) ?> Relatorio de Atividades</a></li>
        </ul>
      </div>
      <?php } ?>
    </div>

    <!-- Right Section -->
    <div class="topbar-right">
      <!-- Search -->
      <form class="topbar-search" action="<?= site_url('mapos/pesquisar') ?>" method="get">
        <input type="search" name="termo" class="topbar-search-input" placeholder="Pesquisar...">
        <button type="submit" class="topbar-action" title="Pesquisar" style="border:none;cursor:pointer;">
          <?= svg_icon('search', 18, 18) ?>
        </button>
      </form>

      <!-- Theme Toggle -->
      <div class="topbar-action" id="theme-toggle-btn">
        <a href="#" title="Alternar Tema" id="btn-toggle-theme" style="color:inherit;text-decoration:none;">
          <span id="theme-icon" class="svg-icon-wrap"><?= svg_icon('sun', 18, 18) ?></span>
        </a>
      </div>

      <!-- Notifications -->
      <div class="topbar-dropdown topbar-notif-dropdown" id="notifications-dropdown">
        <a href="#" class="topbar-action btn" data-bs-toggle="dropdown" title="Notificacoes">
          <?= svg_icon('bell', 18, 18) ?>
          <span class="topbar-badge" id="notif-count" style="display:none;">0</span>
        </a>
        <ul class="dropdown-menu" id="notif-list">
          <li class="notif-header" style="padding:10px 14px;font-weight:600;border-bottom:1px solid rgba(var(--sidebar-accent-rgb,4,103,252),0.15);display:flex;justify-content:space-between;align-items:center;">
            <span style="color:var(--cinza0,#a4a6b3);font-size:13px;">Notificacoes</span>
            <a href="#" id="notif-marcar-todas" style="font-size:11px;font-weight:500;color:var(--sidebar-accent,#0467fc);">Marcar todas</a>
          </li>
          <li id="notif-items" style="max-height:320px;overflow-y:auto;padding:4px;">
            <div style="padding:20px;text-align:center;color:var(--sidebar-text-muted,#8a8a8a);font-size:13px;">
              <?= svg_icon('bell', 32, 32, '', 'opacity:0.3') ?><br>
              Carregando...
            </div>
          </li>
        </ul>
      </div>

      <!-- User Profile -->
      <div class="topbar-dropdown dropdown">
        <a href="#" class="topbar-user btn" data-bs-toggle="dropdown">
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
          <?= svg_icon('chevron-down', 14, 14, 'topbar-user-chevron') ?>
        </a>
        <ul class="dropdown-menu" style="min-width:190px;">
          <li><a href="<?= site_url('mapos/minhaConta') ?>"><?= svg_icon('user', 16, 16) ?> Meu Perfil</a></li>
          <li><a href="<?= site_url() ?>/mine" target="_blank"><?= svg_icon('globe', 16, 16) ?> Area do Cliente</a></li>
          <li class="dropdown-divider"></li>
          <li><a href="<?= site_url('login/sair') ?>" style="color:#e05555;"><?= svg_icon('log-out', 16, 16) ?> Sair do Sistema</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Mobile Offcanvas Navigation -->
  <div class="offcanvas offcanvas-start offcanvas-nav" tabindex="-1" id="mobileNavOffcanvas" style="background:var(--funSider, #333649); color:var(--cinza0, #a4a6b3); max-width:280px;">
    <div class="offcanvas-header" style="border-bottom:1px solid rgba(var(--sidebar-accent-rgb,4,103,252),0.15);">
      <div style="display:flex;align-items:center;gap:8px;">
        <?php if ($topbarLogo): ?>
          <img src="<?= e($topbarLogo) ?>" alt="<?= e($configuration['app_name'] ?? 'Map-OS') ?>" class="topbar-brand-logo" style="width:28px;height:28px;object-fit:contain;">
        <?php else: ?>
          <?= svg_icon('cube-alt', 22, 22, '', 'color:var(--sidebar-accent, #0467fc)') ?>
        <?php endif; ?>
        <span style="font-weight:700;font-size:15px;color:var(--sidebar-accent, #0467fc);"><?= $configuration['app_name'] ?? 'Map-OS' ?></span>
      </div>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body">
      <!-- Search -->
      <form action="<?= site_url('mapos/pesquisar') ?>" method="get" style="margin-bottom:12px;">
        <div style="display:flex;gap:6px;">
          <input type="search" name="termo" placeholder="Pesquisar..." style="flex:1;padding:8px 10px;border-radius:8px;border:1px solid rgba(var(--sidebar-accent-rgb,4,103,252),0.2);background:var(--sidebar-hover-bg, rgba(255,255,255,0.06));color:var(--cinza0,#a4a6b3);font-size:14px;outline:none;">
          <button type="submit" style="background:transparent;border:none;color:var(--cinza0,#a4a6b3);cursor:pointer;"><?= svg_icon('search', 18, 18) ?></button>
        </div>
      </form>

      <div class="nav-section-title">Principal</div>
      <a href="<?= base_url() ?>" class="nav-link-item <?php if (isset($menuPainel)) { echo 'active'; }; ?>"><?= svg_icon('home', 16, 16) ?> Inicio</a>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDashboard') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
      <a href="<?= site_url('dashboard') ?>" class="nav-link-item <?php if (isset($menuDashboard)) { echo 'active'; }; ?>"><?= svg_icon('dashboard', 16, 16) ?> Dashboard</a>
      <?php } ?>

      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vObras') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
      <div class="nav-section-title">Obras e Projetos</div>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vObras')) { ?>
      <a href="<?= site_url('obras') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('building-house', 16, 16) ?> Gerenciar Obras</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
      <a href="<?= site_url('obras/adicionar') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('plus-circle', 16, 16) ?> Nova Obra</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicoObra') && !$this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
      <a href="<?= site_url('obras_tecnico') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('hard-hat', 16, 16) ?> Minhas Obras</a>
      <?php } ?>
      <?php } ?>

      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs') || $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
      <div class="nav-section-title">Ordens de Servico</div>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
      <a href="<?= site_url('os') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('file', 16, 16) ?> Todas as OS</a>
      <a href="<?= site_url('kanban') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('columns', 16, 16) ?> Kanban Board</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
      <a href="<?= site_url('os/atribuir') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('user-plus', 16, 16) ?> Atribuir Tecnico</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vGarantia')) { ?>
      <a href="<?= site_url('garantias') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('receipt', 16, 16) ?> Garantias</a>
      <?php } ?>
      <?php } ?>

      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vServico') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda') ||
                $this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) { ?>
      <div class="nav-section-title">Cadastros</div>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
      <a href="<?= site_url('clientes') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('user', 16, 16) ?> Clientes</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
      <a href="<?= site_url('produtos') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('basket', 16, 16) ?> Produtos</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
      <a href="<?= site_url('servicos') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('wrench', 16, 16) ?> Servicos</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
      <a href="<?= site_url('vendas') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('cart', 16, 16) ?> Vendas</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) { ?>
      <a href="<?= site_url('tecnicos_admin') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('hard-hat', 16, 16) ?> Tecnicos</a>
      <?php } ?>
      <?php } ?>

      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento') || $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
      <div class="nav-section-title">Financeiro</div>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
      <a href="<?= site_url('financeiro/lancamentos') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('bar-chart-alt', 16, 16) ?> Lancamentos</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
      <a href="<?= site_url('cobrancas/cobrancas') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('credit-card', 16, 16) ?> Cobrancas</a>
      <?php } ?>
      <?php } ?>

      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe') || $this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado') || $this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
      <div class="nav-section-title">Documentos Fiscais</div>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) { ?>
      <a href="<?= site_url('nfse_os') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('receipt', 16, 16) ?> NFSe Dashboard</a>
      <a href="<?= site_url('nfse_os/relatorio') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('chart', 16, 16) ?> Relatorio NFSe</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
      <a href="<?= site_url('certificado') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('shield-check', 14, 14) ?> Certificado: Status</a>
      <a href="<?= site_url('certificado/configurar') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('cog', 14, 14) ?> Certificado: Configurar</a>
      <a href="<?= site_url('nfse') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('receipt', 14, 14) ?> NFS-e Importadas</a>
      <a href="<?= site_url('certificado/importar_nfse') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('import', 14, 14) ?> Importar NFS-e</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
      <a href="<?= site_url('impostos') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('chart', 14, 14) ?> Impostos: Dashboard</a>
      <a href="<?= site_url('impostos/configuracoes') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('cog', 14, 14) ?> Impostos: Config</a>
      <a href="<?= site_url('impostos/simulador') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('calculator', 14, 14) ?> Impostos: Simulador</a>
      <?php } ?>
      <?php } ?>

      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto') || $this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos') || $this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
      <div class="nav-section-title">Relatorios</div>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos')) { ?>
      <a href="<?= site_url('relatorioatendimentos') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('time', 16, 16) ?> Atendimentos</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
      <a href="<?= site_url('relatoriotecnicos') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('hard-hat', 16, 16) ?> Performance Tecnicos</a>
      <a href="<?= site_url('dashboard/relatorio_financeiro') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('dollar-circle', 16, 16) ?> Financeiro</a>
      <a href="<?= site_url('dashboard/relatorio_produtos') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('package', 16, 16) ?> Produtos</a>
      <a href="<?= site_url('dashboard/relatorio_clientes') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('user-check', 16, 16) ?> Clientes</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
      <a href="<?= site_url('dre') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('bar-chart-alt', 14, 14) ?> DRE: Demonstracao</a>
      <a href="<?= site_url('dre/contas') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('list-ul', 14, 14) ?> DRE: Plano de Contas</a>
      <a href="<?= site_url('dre/lancamentos') ?>" class="nav-link-item nav-link-sub" style="padding-left:32px;font-size:12px;"><?= svg_icon('book', 14, 14) ?> DRE: Lancamentos</a>
      <?php } ?>
      <?php } ?>

      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) { ?>
      <div class="nav-section-title">Configuracoes</div>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) { ?>
      <a href="<?= site_url('usuarios') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('user-circle', 16, 16) ?> Usuarios</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
      <a href="<?= site_url('permissoes') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('shield-check', 16, 16) ?> Permissoes</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
      <a href="<?= site_url('arquivos') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('box', 16, 16) ?> Arquivos</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) { ?>
      <a href="<?= site_url('mapos/emitente') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('building', 16, 16) ?> Emitente</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema')) { ?>
      <a href="<?= site_url('mapos/configurar') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('cog', 16, 16) ?> Config. Sistema</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
      <a href="<?= site_url('modulos') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('extension', 16, 16) ?> Modulos</a>
      <a href="<?= site_url('backup') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('database', 16, 16) ?> Backup</a>
      <a href="<?= site_url('auditoria') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('file-find', 16, 16) ?> Auditoria</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) { ?>
      <a href="<?= site_url('notificacoes/configuracoes') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('whatsapp', 16, 16) ?> Notificacoes</a>
      <a href="<?= site_url('notificacoes/templates') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('message-square-dots', 16, 16) ?> Templates</a>
      <a href="<?= site_url('notificacoes/logs') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('history', 16, 16) ?> Historico</a>
      <?php } ?>
      <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
      <a href="<?= site_url('migrate') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('database', 16, 16) ?> Migracoes DB</a>
      <a href="<?= site_url('diagnostico') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('bug', 16, 16) ?> Diagnostico</a>
      <a href="<?= site_url('emails/dashboard') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('envelope', 16, 16) ?> Fila de Emails</a>
      <a href="<?= site_url('webhooks') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('webhook', 16, 16) ?> Webhooks</a>
      <a href="<?= site_url('api/docs') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('code-alt', 16, 16) ?> API v2</a>
      <a href="<?= site_url('agente_ia') ?>" class="nav-link-item nav-link-sub"><?= svg_icon('bot', 16, 16) ?> Agente IA</a>
      <?php } ?>
      <?php } ?>

      <div style="border-top:1px solid rgba(var(--sidebar-accent-rgb,4,103,252),0.15);margin-top:12px;padding-top:12px;">
        <a href="<?= site_url('mapos/minhaConta') ?>" class="nav-link-item"><?= svg_icon('user', 16, 16) ?> Minha Conta</a>
        <a href="<?= site_url('login/sair') ?>" class="nav-link-item" style="color:#e05555;"><?= svg_icon('log-out', 16, 16) ?> Sair do Sistema</a>
      </div>
    </div>
  </div>
  <?php endif; ?>