<!DOCTYPE html>
<html lang="pt-br">

<head>
  <title><?= $configuration['app_name'] ?: 'Map-OS' ?></title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token-name" content="<?= config_item("csrf_token_name") ?>">
  <meta name="csrf-cookie-name" content="<?= config_item("csrf_cookie_name") ?>">
  <link rel="shortcut icon" type="image/png" href="<?= base_url(); ?>assets/img/favicon.png" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-style.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-media.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/custom.css" />
  <link href="<?= base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/fullcalendar.css" />
  <?php if ($configuration['app_theme'] == 'white') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white.css" />
  <?php } ?>
  <?php if ($configuration['app_theme'] == 'puredark') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-pure-dark.css" />
  <?php } ?>
  <?php if ($configuration['app_theme'] == 'darkviolet') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-dark-violet.css" />
  <?php } ?>
  <?php if ($configuration['app_theme'] == 'darkorange') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-dark-orange.css" />
  <?php } ?>
  <?php if ($configuration['app_theme'] == 'whitegreen') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white-green.css" />
  <?php } ?>
  <?php if ($configuration['app_theme'] == 'whiteblack') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tema-white-black.css" />
  <?php } ?>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;500;700&display=swap' rel='stylesheet' type='text/css'>
  <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-1.12.4.min.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/shortcut.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
  <script type="text/javascript" src="<?= base_url(); ?>assets/js/datatables.min.js"></script>
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
    .notif-badge {
      position: absolute; top: 2px; right: 2px; background: #e74c3c; color: #fff;
      font-size: 10px; font-weight: bold; border-radius: 50%; min-width: 18px;
      height: 18px; line-height: 18px; text-align: center; padding: 0 4px;
    }
    .notif-item { padding: 8px 12px; border-bottom: 1px solid #eee; cursor: pointer; }
    .notif-item:hover { background: #f5f5f5; }
    .notif-item.nao-lida { background: #eef6ff; }
    .notif-item .notif-titulo { font-weight: 600; font-size: 12px; margin-bottom: 2px; }
    .notif-item .notif-msg { font-size: 11px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .notif-item .notif-data { font-size: 10px; color: #999; margin-top: 2px; }
    .notif-item .notif-icone { margin-right: 8px; font-size: 16px; vertical-align: middle; }
    #theme-toggle-btn a { cursor: pointer; }
    /* Notificações sempre por cima - sobrepõe tudo */
    #notifications-dropdown { position: static !important; }
    #notifications-dropdown.open { position: relative !important; z-index: 999999 !important; }
    #notifications-dropdown .dropdown-menu {
        z-index: 999999 !important;
        position: fixed !important;
        top: 60px !important;
        right: 60px !important;
        left: auto !important;
        bottom: auto !important;
        max-height: 400px;
        overflow-y: auto;
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    }
  </style>
</head>

<?php
// Verificar se estamos na área do técnico (Portal do Técnico) - ANTES do body
$is_area_tecnico = (strpos(uri_string(), 'tecnicos') === 0);
$body_class = $is_area_tecnico ? 'portal-tecnico' : '';
?>
<body data-theme="<?= isset($configuration['app_theme']) ? $configuration['app_theme'] : 'default' ?>" class="<?= $body_class ?>">
  <!--top-Header-menu-->

  <?php if ($is_area_tecnico): ?>
  <!-- Header para Área do Técnico -->
  <div class="navebarn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div id="user-nav" class="navbar navbar-inverse" style="background: transparent; border: none;">
      <ul class="nav">
        <!-- Botão Trocar Tema -->
        <li class="" id="theme-toggle-btn">
          <a href="#" class="tip-right" title="Alternar Tema" id="btn-toggle-theme">
            <i class='bx bx-sun iconN' id="theme-icon"></i>
          </a>
        </li>

        <li class="dropdown">
          <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Meu Perfil">
            <i class='bx bx-user-circle iconN'></i><span class="text"></span>
          </a>
          <ul class="dropdown-menu">
            <li class=""><a title="Dashboard" href="<?= site_url('tecnicos/dashboard'); ?>"><i class='bx bx-home-alt'></i> <span class="text">Dashboard</span></a></li>
            <li class=""><a title="Meu Perfil" href="<?= site_url('tecnicos/perfil'); ?>"><i class='bx bx-user'></i> <span class="text">Meu Perfil</span></a></li>
            <li class="divider"></li>
            <li class=""><a title="Sair" href="<?= site_url('tecnicos/logout'); ?>"><i class='bx bx-log-out-circle'></i> <span class="text">Sair</span></a></li>
          </ul>
        </li>

        <!-- Botão Sair Direto -->
        <li>
          <a href="<?= site_url('tecnicos/logout') ?>" class="tip-right" title="Sair do Sistema">
            <i class='bx bx-log-out-circle iconN'></i><span class="text"></span>
          </a>
        </li>
      </ul>
    </div>

    <!-- New User - Técnico -->
    <div id="userr" style="padding-right:45px;display:flex;flex-direction:column;align-items:flex-end;justify-content:center;">

      <!-- Notificações ao lado da foto -->
      <div class="notificacoes-header" style="position:absolute;right:45px;top:50%;transform:translateY(-50%);z-index:1000;">
        <ul class="nav" style="margin:0;">
          <li class="dropdown" id="notifications-dropdown" style="list-style:none;">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Notificações" style="padding:10px;display:block;position:relative;">
              <i class='bx bx-bell' style="font-size:22px;color:#fff;"></i>
              <span class="notif-badge" id="notif-count" style="display:none;position:absolute;top:2px;right:2px;background:#e74c3c;color:#fff;font-size:10px;font-weight:bold;border-radius:50%;min-width:18px;height:18px;line-height:18px;text-align:center;padding:0 4px;">0</span>
            </a>
            <ul class="dropdown-menu" id="notif-list" style="width:340px;right:0;left:auto;margin-top:10px;">
              <li class="notif-header" style="padding:8px 12px;font-weight:bold;border-bottom:1px solid #ddd;">
                <span>Notificações</span>
                <a href="#" id="notif-marcar-todas" style="float:right;font-size:11px;font-weight:normal;">Marcar todas como lidas</a>
              </li>
              <li id="notif-items" style="max-height:320px;overflow-y:auto;">
                <div style="padding:15px;text-align:center;color:#888;">Carregando...</div>
              </li>
            </ul>
          </li>
        </ul>
      </div>

      <div class="user-names userT0">
        <?php
        function saudacaoTecnico()
        {
          $hora = date('H');
          if ($hora >= 00 && $hora < 12) {
            return 'Bom dia, ';
          } elseif ($hora >= 12 && $hora < 18) {
            return 'Boa tarde, ';
          } else {
            return 'Boa noite, ';
          }
        }
        echo saudacaoTecnico();
        ?>
      </div>
      <div class="userT"><?= $this->session->userdata('tec_nome') ?: 'Técnico' ?></div>

      <section style="display:block;position:absolute;right:10px">
        <div class="profile">
          <div class="profile-img">
            <a href="<?= site_url('tecnicos/perfil'); ?>">
              <img src="<?= base_url() ?>assets/img/User.png" alt="Técnico">
            </a>
          </div>
        </div>
      </section>
    </div>
  </div>
  <!-- End User Técnico -->

  <?php else: ?>
  <!-- Header Padrão (Admin) -->
  <div class="navebarn">
    <div id="user-nav" class="navbar navbar-inverse">
      <ul class="nav">
        <!-- Botão Trocar Tema -->
        <li class="" id="theme-toggle-btn">
          <a href="#" class="tip-right" title="Alternar Tema" id="btn-toggle-theme">
            <i class='bx bx-sun iconN' id="theme-icon"></i>
          </a>
        </li>

        <li class="dropdown">
          <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Perfis"><i class='bx bx-user-circle iconN'></i><span class="text"></span></a>
          <ul class="dropdown-menu">
            <li class=""><a title="Área do Cliente" href="<?= site_url(); ?>/mine" target="_blank"> <span class="text">Área do Cliente</span></a></li>
            <li class=""><a title="Meu Perfil" href="<?= site_url('mapos/minhaConta'); ?>"><span class="text">Meu Perfil</span></a></li>
            <li class="divider"></li>
            <li class=""><a title="Sair do Sistema" href="<?= site_url('login/sair'); ?>"><i class='bx bx-log-out-circle'></i> <span class="text">Sair do Sistema</span></a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Relatórios"><i class='bx bx-pie-chart-alt-2 iconN'></i><span class="text"></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?= site_url('relatorios/clientes') ?>">Clientes</a></li>
            <li><a href="<?= site_url('relatorios/produtos') ?>">Produtos</a></li>
            <li><a href="<?= site_url('relatorios/servicos') ?>">Serviços</a></li>
            <li><a href="<?= site_url('relatorios/os') ?>">Ordens de Serviço</a></li>
            <li><a href="<?= site_url('relatorios/vendas') ?>">Vendas</a></li>
            <li><a href="<?= site_url('relatorios/financeiro') ?>">Financeiro</a></li>
            <li><a href="<?= site_url('relatorios/sku') ?>">SKU</a></li>
            <li><a href="<?= site_url('relatorios/receitasBrutasMei') ?>">Receitas Brutas - MEI</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Configurações"><i class='bx bx-cog iconN'></i><span class="text"></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?= site_url('mapos/configurar') ?>">Sistema</a></li>
            <li><a href="<?= site_url('usuarios') ?>">Usuários</a></li>
            <li><a href="<?= site_url('mapos/emitente') ?>">Emitente</a></li>
            <li><a href="<?= site_url('permissoes') ?>">Permissões</a></li>
            <li><a href="<?= site_url('auditoria') ?>">Auditoria</a></li>
            <li><a href="<?= site_url('mapos/emails') ?>">Emails</a></li>
            <li><a href="<?= site_url('menu_backup') ?>">Backup</a></li>
          </ul>
        </li>

        <!-- Notificações -->
        <li class="dropdown" id="notifications-dropdown" style="position:relative;z-index:99999;">
          <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Notificações">
            <i class='bx bx-bell iconN'></i>
            <span class="notif-badge" id="notif-count" style="display:none;">0</span>
          </a>
          <ul class="dropdown-menu" id="notif-list" style="width:340px;right:0;left:auto;z-index:99999;position:absolute;">
            <li class="notif-header" style="padding:8px 12px;font-weight:bold;border-bottom:1px solid #ddd;">
              <span>Notificações</span>
              <a href="#" id="notif-marcar-todas" style="float:right;font-size:11px;font-weight:normal;">Marcar todas como lidas</a>
            </li>
            <li id="notif-items" style="max-height:320px;overflow-y:auto;">
              <div style="padding:15px;text-align:center;color:#888;">Carregando...</div>
            </li>
          </ul>
        </li>

        <!-- Botão Sair Direto -->
        <li>
          <a href="<?= site_url('login/sair') ?>" class="tip-right" title="Sair do Sistema">
            <i class='bx bx-log-out-circle iconN'></i><span class="text"></span>
          </a>
        </li>
      </ul>
    </div>

    <!-- New User -->
    <div id="userr" style="padding-right:45px;display:flex;flex-direction:column;align-items:flex-end;justify-content:center;">

      <div class="user-names userT0">
        <?php
        function saudacao()
        {
          $hora = date('H');
          if ($hora >= 00 && $hora < 12) {
            return 'Bom dia, ';
          } elseif ($hora >= 12 && $hora < 18) {
            return 'Boa tarde, ';
          } else {
            return 'Boa noite, ';
          }
        }

        $login = '';
        echo saudacao($login); // Irá retornar conforme o horário
        ?>
      </div>
      <div class="userT"><?= $this->session->userdata('nome_admin') ?></div>

      <section style="display:block;position:absolute;right:10px">
        <div class="profile">
          <div class="profile-img">
            <a href="<?= site_url('mapos/minhaConta'); ?>"><img src="<?= !is_file(FCPATH . "assets/userImage/" . $this->session->userdata('url_image_user_admin')) ?  base_url() . "assets/img/User.png" : base_url() . "assets/userImage/" . $this->session->userdata('url_image_user_admin') ?>" alt=""></a>
          </div>
        </div>
      </section>

    </div>
  </div>
  <!-- End User -->
  <?php endif; ?>

  <!--start-top-serch-->
  <div style="display: none" id="search">
    <form action="<?= site_url('mapos/pesquisar') ?>">
      <input type="text" name="termo" placeholder="Pesquisar..." />
      <button type="submit" class="tip-bottom" title="Pesquisar"><i class="fas fa-search fa-white"></i></button>
    </form>
  </div>
  <!--close-top-serch-->
