<?php
/**
 * View parcial: Seletor de tema (F3.3 + F3.6)
 * Adicionado em 2026-06-15.
 *
 * Modal com:
 *   - Paleta de cores primarias (10 opcoes)
 *   - Modo de tema: Automatico (segue SO) / Claro / Escuro
 *
 * O JS em theme-customizer.js gerencia persistencia em localStorage
 * e aplicacao em tempo real.
 */
?>
<div class="ux-theme-modal" id="ux-theme-modal" role="dialog" aria-modal="true" aria-labelledby="ux-theme-title" style="display:none;">
  <div class="ux-theme-backdrop" data-theme-close></div>
  <div class="ux-theme-dialog">
    <div class="ux-theme-header">
      <h3 id="ux-theme-title"><i class='bx bx-palette'></i> Aparência</h3>
      <button type="button" class="ux-theme-close" data-theme-close aria-label="Fechar">
        <i class='bx bx-x'></i>
      </button>
    </div>
    <div class="ux-theme-body">
      <section>
        <h4>Cor primária</h4>
        <p class="ux-theme-hint">Define a cor de destaque em botoes, links e elementos interativos.</p>
        <div class="ux-color-picker" id="ux-color-picker">
          <span class="ux-color-swatch" data-accent="blue"   style="background:#0467fc;" title="Azul"></span>
          <span class="ux-color-swatch" data-accent="indigo" style="background:#6610f2;" title="Indigo"></span>
          <span class="ux-color-swatch" data-accent="purple" style="background:#7c3aed;" title="Roxo"></span>
          <span class="ux-color-swatch" data-accent="pink"   style="background:#d63384;" title="Rosa"></span>
          <span class="ux-color-swatch" data-accent="red"    style="background:#dc3545;" title="Vermelho"></span>
          <span class="ux-color-swatch" data-accent="orange" style="background:#fd7e14;" title="Laranja"></span>
          <span class="ux-color-swatch" data-accent="green"  style="background:#198754;" title="Verde"></span>
          <span class="ux-color-swatch" data-accent="teal"   style="background:#20c997;" title="Verde-agua"></span>
          <span class="ux-color-swatch" data-accent="cyan"   style="background:#0dcaf0;" title="Ciano"></span>
          <span class="ux-color-swatch" data-accent="dark"   style="background:#212529;" title="Preto"></span>
        </div>
      </section>

      <section>
        <h4>Modo do tema</h4>
        <p class="ux-theme-hint">No modo automatico, o sistema segue a preferencia do seu sistema operacional.</p>
        <div class="ux-theme-mode">
          <label class="ux-theme-mode-option">
            <input type="radio" name="ux-theme-mode" value="auto">
            <span class="ux-theme-mode-icon"><i class='bx bx-desktop'></i></span>
            <span class="ux-theme-mode-info">
              <strong>Automatico</strong>
              <small>Segue o tema do seu sistema operacional</small>
            </span>
          </label>
          <label class="ux-theme-mode-option">
            <input type="radio" name="ux-theme-mode" value="manual">
            <span class="ux-theme-mode-icon"><i class='bx bx-sun'></i></span>
            <span class="ux-theme-mode-info">
              <strong>Manual</strong>
              <small>Use o botao de sol/lua no topo para alternar</small>
            </span>
          </label>
        </div>
      </section>

      <section>
        <h4>Densidade das tabelas</h4>
        <p class="ux-theme-hint">Controla o espacamento das linhas nas listagens. Atalho: <kbd>Ctrl+Alt+D</kbd></p>
        <div class="ux-theme-mode">
          <label class="ux-theme-mode-option">
            <input type="radio" name="ux-density" value="compact">
            <span class="ux-theme-mode-icon"><i class='bx bx-list-ul'></i></span>
            <span class="ux-theme-mode-info">
              <strong>Compacta</strong>
              <small>Mais linhas visiveis, menos espacamento</small>
            </span>
          </label>
          <label class="ux-theme-mode-option">
            <input type="radio" name="ux-density" value="comfortable">
            <span class="ux-theme-mode-icon"><i class='bx bx-menu'></i></span>
            <span class="ux-theme-mode-info">
              <strong>Padrao</strong>
              <small>Equilibrio entre densidade e legibilidade</small>
            </span>
          </label>
          <label class="ux-theme-mode-option">
            <input type="radio" name="ux-density" value="spacious">
            <span class="ux-theme-mode-icon"><i class='bx bx-spreadsheet'></i></span>
            <span class="ux-theme-mode-info">
              <strong>Espacosa</strong>
              <small>Mais ar entre linhas, mais facil de ler</small>
            </span>
          </label>
        </div>
      </section>

      <section>
        <h4>Idioma</h4>
        <p class="ux-theme-hint">Escolha o idioma do sistema. Requer <a href="<?= site_url('ajuda') ?>" target="_blank">traducoes</a> cadastradas.</p>
        <div class="ux-theme-mode" id="ux-locale-list">
          <em style="font-size: 0.82rem; color: #6c757d;">Carregando idiomas...</em>
        </div>
      </section>
    </div>
  </div>
</div>
<script>
(function() {
  'use strict';
  var open = false;
  var modal = document.getElementById('ux-theme-modal');
  if (!modal) return;

  // Restaurar estado (marca swatch e radio)
  function refreshUi() {
    var accent = localStorage.getItem('ux-accent') || 'blue';
    var swatches = document.querySelectorAll('.ux-color-swatch');
    swatches.forEach(function(s) {
      if (s.getAttribute('data-accent') === accent) s.classList.add('active');
      else s.classList.remove('active');
    });
    var mode = localStorage.getItem('ux-theme-mode') || 'auto';
    var radios = document.querySelectorAll('input[name="ux-theme-mode"]');
    radios.forEach(function(r) { r.checked = (r.value === mode); });
  }

  window.UxTheme = {
    open: function() {
      modal.style.display = 'flex';
      refreshUi();
      open = true;
      document.addEventListener('keydown', escClose);
    },
    close: function() {
      modal.style.display = 'none';
      open = false;
      document.removeEventListener('keydown', escClose);
    }
  };

  function escClose(e) {
    if (e.key === 'Escape' && open) window.UxTheme.close();
  }

  // Click em backdrop / botao fechar
  modal.addEventListener('click', function(e) {
    if (e.target.hasAttribute('data-theme-close')) window.UxTheme.close();
  });

  // Abre quando clicar no botao de cor no topo (vamos adicionar)
  document.addEventListener('click', function(e) {
    var trigger = e.target.closest('[data-ux-theme-open]');
    if (trigger) {
      e.preventDefault();
      window.UxTheme.open();
    }
  });

  // Primeira renderizacao
  refreshUi();
})();
</script>
