<?php
/**
 * PWA install (F5.5) + Push (F5.6)
 * - Botao de instalar
 * - Registra o Service Worker
 * - Carrega o push-manager.js
 */
?>
<button type="button" id="ux-pwa-install" class="btn btn-primary btn-sm"
        style="display:none;" title="Instalar como aplicativo">
  <i class="bx bx-download"></i>
  <span>Instalar app</span>
</button>
<?php
// Carrega o manager do PWA (registra o SW e mostra o botao se houver prompt)
$sw_url = base_url() . 'service-worker.js';
$uxPwa = '<script>window.UX = window.UX || {};'
       . 'window.UX.sw = { register: function(){'
       . '  if (!("serviceWorker" in navigator)) return;'
       . '  navigator.serviceWorker.register(' . json_encode($sw_url) . ').catch(function(){});'
       . '} };'
       . 'document.addEventListener("DOMContentLoaded", function(){ try { window.UX.sw.register(); } catch(e){} });'
       . '</script>';
echo $uxPwa;
?>
<script src="<?= base_url() ?>assets/js/ux/push-manager.js"></script>
