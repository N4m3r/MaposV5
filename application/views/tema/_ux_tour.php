<?php
/**
 * View parcial: _ux_tour
 * Carrega Driver.js (lib do tour) e o runner que consome o backend.
 * Adicionado em 2026-06-14 (Fase 2.1.1 + 2.1.4 do Plano UX).
 *
 * Dependencias externas (ja carregadas):
 *   - jQuery 3.x
 *   - window.BaseUrl  (definido pelo layout)
 *   - window.notify   (helper JS, se existir)
 */
?>
<!-- Fase 2.1.1: Driver.js (wrapper que tenta local -> CDN) -->
<script src="<?= base_url() ?>assets/js/ux/driver-loader.js"></script>

<!-- Fase 2.1.4: Tour runner -->
<script src="<?= base_url() ?>assets/js/ux/tour-runner.js"></script>
