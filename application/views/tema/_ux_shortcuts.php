<?php
/**
 * View parcial: Banner de atalhos F1-F12
 * Incluir apos o <body>, antes do conteudo.
 *
 * Mostra ao usuario quais teclas de atalho estao disponiveis.
 * Recolhivel (toggle persistido em localStorage).
 */
?>
<div class="ux-shortcut-banner" id="ux-shortcut-banner" data-tour-atalhos>
  <span class="ux-shortcut-banner-label">
    <i class='bx bx-keyboard'></i> Atalhos:
  </span>
  <div class="ux-shortcut-banner-items">
    <span class="ux-shortcut-item"><span class="ux-shortcut-key">Esc</span> Inicio</span>
    <span class="ux-shortcut-item"><span class="ux-shortcut-key">F1</span> Clientes</span>
    <span class="ux-shortcut-item"><span class="ux-shortcut-key">F2</span> Produtos</span>
    <span class="ux-shortcut-item"><span class="ux-shortcut-key">F3</span> Servicos</span>
    <span class="ux-shortcut-item"><span class="ux-shortcut-key">F4</span> OS</span>
    <span class="ux-shortcut-item"><span class="ux-shortcut-key">F6</span> Nova Venda</span>
    <span class="ux-shortcut-item"><span class="ux-shortcut-key">F7</span> Financeiro</span>
    <span class="ux-shortcut-item"><span class="ux-shortcut-key">Ctrl K</span> Buscar</span>
    <span class="ux-shortcut-item"><span class="ux-shortcut-key">?</span> Ajuda</span>
  </div>
  <button type="button" class="ux-shortcut-toggle" id="ux-shortcut-toggle" title="Recolher/Expandir">
    <i class='bx bx-chevron-up' id="ux-shortcut-toggle-icon"></i>
  </button>
</div>
<script>
(function() {
  var banner = document.getElementById('ux-shortcut-banner');
  var btn = document.getElementById('ux-shortcut-toggle');
  var icon = document.getElementById('ux-shortcut-toggle-icon');
  if (!banner || !btn) return;

  // Restaurar estado
  try {
    if (localStorage.getItem('ux-shortcut-collapsed') === '1') {
      banner.classList.add('collapsed');
      icon.className = 'bx bx-chevron-down';
    }
  } catch(e) {}

  btn.addEventListener('click', function() {
    banner.classList.toggle('collapsed');
    var collapsed = banner.classList.contains('collapsed');
    icon.className = collapsed ? 'bx bx-chevron-down' : 'bx bx-chevron-up';
    try { localStorage.setItem('ux-shortcut-collapsed', collapsed ? '1' : '0'); } catch(e) {}
  });
})();
</script>
