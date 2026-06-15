<?php
/**
 * A11y audit view (F6.7)
 * Carrega axe-core via CDN e roda na pagina atual
 */
?>
<div class="dashboard-container">
  <div class="dashboard-header">
    <div class="dashboard-title">
      <i class="bx bx-check-shield"></i>
      Auditoria de Acessibilidade (WCAG 2.1 AA)
    </div>
    <div class="dashboard-actions">
      <button id="ux-a11y-run" class="btn-action btn-primary">
        <i class="bx bx-play"></i> Rodar auditoria
      </button>
    </div>
  </div>

  <div class="ux-a11y-info">
    <p><strong>Esta auditoria</strong> usa a biblioteca <a href="https://github.com/dequelabs/axe-core" target="_blank" rel="noopener">axe-core</a> para detectar violacoes de acessibilidade nesta pagina. As checagens cobrem WCAG 2.1 AA.</p>
    <p><strong>Limitacao:</strong> axe-core roda no navegador, entao as paginas que nao estao carregadas nao podem ser auditadas. Use este painel apos navegar para a pagina que deseja auditar.</p>
  </div>

  <div id="ux-a11y-summary" class="ux-a11y-summary" style="display:none;"></div>
  <div id="ux-a11y-results"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axe-core/4.10.0/axe.min.js"></script>
<script src="<?= base_url() ?>assets/js/ux/a11y-audit.js"></script>
