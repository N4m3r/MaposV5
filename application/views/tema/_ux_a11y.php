<?php
/**
 * A11y enhancements (F6) — focus trap + Esc + atalhos extras
 */
?>
<script src="<?= base_url() ?>assets/js/ux/a11y.js"></script>
<script src="<?= base_url() ?>assets/js/ux/a11y-forms.js"></script>
<script>
// Atalho "/" foca o campo de busca
(function() {
  document.addEventListener('keydown', function(e) {
    if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
      var s = document.querySelector('.topbar-search-input');
      if (s) { e.preventDefault(); s.focus(); s.select(); }
    }
  });
})();
</script>
