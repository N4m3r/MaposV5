<?php
/**
 * Modo Campo (F5.7) — UI touch-friendly para tecnicos em obra
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="theme-color" content="#0467fc">
  <title>Modo Campo - MaposV5</title>
  <link rel="manifest" href="<?= base_url() ?>manifest.webmanifest">
  <link rel="stylesheet" href="<?= base_url() ?>assets/css/mapos.css?v=<?= @filemtime(FCPATH . 'assets/css/mapos.css') ?>">
  <link rel="stylesheet" href="<?= base_url() ?>assets/css/ux-components.css?v=<?= @filemtime(FCPATH . 'assets/css/ux-components.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
</head>
<body class="ux-campo-mode">

  <header class="ux-campo-header">
    <div class="ux-campo-status">
      <span class="ux-campo-dot" id="ux-campo-net"></span>
      <span id="ux-campo-net-label">Conectado</span>
    </div>
    <div class="ux-campo-clock" id="ux-campo-clock">--:--</div>
  </header>

  <main class="ux-campo-main">
    <h1 class="ux-campo-titulo">Minhas OS hoje</h1>
    <div id="ux-campo-os" class="ux-campo-os-list">
      <div class="ux-campo-loading">
        <i class="bx bx-loader-alt bx-spin"></i> Carregando...
      </div>
    </div>

    <div class="ux-campo-actions">
      <button class="ux-campo-btn ux-campo-btn-checkin" id="ux-campo-checkin">
        <i class="bx bx-log-in-circle"></i>
        <span>Fazer check-in</span>
      </button>
      <button class="ux-campo-btn ux-campo-btn-foto" id="ux-campo-foto">
        <i class="bx bx-camera"></i>
        <span>Tirar foto</span>
      </button>
      <button class="ux-campo-btn ux-campo-btn-obs" id="ux-campo-obs">
        <i class="bx bx-edit"></i>
        <span>Observacao</span>
      </button>
      <button class="ux-campo-btn ux-campo-btn-sync" id="ux-campo-sync">
        <i class="bx bx-sync"></i>
        <span>Sincronizar</span>
        <span class="ux-campo-pending" id="ux-campo-pending" style="display:none;">0</span>
      </button>
    </div>
  </main>

  <input type="file" id="ux-campo-foto-input" accept="image/*" capture="environment" style="display:none;">

  <div id="ux-campo-modal" class="ux-campo-modal" style="display:none;">
    <div class="ux-campo-modal-content">
      <h3 id="ux-campo-modal-titulo">Observacao</h3>
      <textarea id="ux-campo-modal-texto" rows="4" placeholder="Digite..."></textarea>
      <div class="ux-campo-modal-btns">
        <button class="ux-campo-btn-secondary" id="ux-campo-modal-cancel">Cancelar</button>
        <button class="ux-campo-btn-primary" id="ux-campo-modal-ok">Salvar</button>
      </div>
    </div>
  </div>

  <script>window.BaseUrl = "<?= base_url() ?>";</script>
  <script src="<?= base_url() ?>assets/js/ux/mobile-camera.js"></script>
  <script src="<?= base_url() ?>assets/js/ux/campo.js"></script>
</body>
</html>
