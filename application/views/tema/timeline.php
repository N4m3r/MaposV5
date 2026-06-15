<?php
/**
 * Timeline view (F4.7)
 */
?>
<div class="dashboard-container">
  <div class="dashboard-header">
    <div class="dashboard-title">
      <i class="bx bx-time"></i>
      Timeline de Atividades
    </div>
    <div class="dashboard-actions">
      <select id="ux-timeline-periodo" class="form-select form-select-sm" style="width:auto;display:inline-block;">
        <option value="7">Ultimos 7 dias</option>
        <option value="30" selected>Ultimos 30 dias</option>
        <option value="90">Ultimos 90 dias</option>
        <option value="365">Ultimo ano</option>
      </select>
      <select id="ux-timeline-modulo" class="form-select form-select-sm" style="width:auto;display:inline-block;">
        <option value="">Todos os modulos</option>
        <option value="os">OS</option>
        <option value="clientes">Clientes</option>
        <option value="financeiro">Financeiro</option>
        <option value="atividades">Atividades</option>
      </select>
      <button class="btn-action btn-primary" id="ux-timeline-refresh">
        <i class="bx bx-refresh"></i> Atualizar
      </button>
    </div>
  </div>

  <div id="ux-timeline-feed" class="ux-timeline">
    <div class="ux-timeline-item ux-timeline-info">
      <span class="ux-timeline-time">Carregando...</span>
      <span class="ux-timeline-title">Buscando eventos...</span>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/ux/timeline.js"></script>
