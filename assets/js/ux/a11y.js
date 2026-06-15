/**
 * Focus trap + Esc handler (F6.4)
 *
 * Captura o foco dentro de modais abertos e fecha ao pressionar Esc.
 * Funciona com modais Bootstrap 5 (.modal.show) e custom modais (.ux-modal-open).
 */
(function(window, $) {
  'use strict';
  if (!window.UX) window.UX = {};

  var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

  function trapFocus(container) {
    var first = container.querySelectorAll(FOCUSABLE)[0];
    var last  = null;
    var list  = container.querySelectorAll(FOCUSABLE);
    if (list.length) last = list[list.length - 1];

    container.__uxFocusHandler = function(e) {
      if (e.key !== 'Tab') return;
      if (!first) { e.preventDefault(); return; }
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    };
    container.addEventListener('keydown', container.__uxFocusHandler);

    if (first) setTimeout(function() { first.focus(); }, 50);
  }

  function releaseFocus(container) {
    if (container.__uxFocusHandler) {
      container.removeEventListener('keydown', container.__uxFocusHandler);
      delete container.__uxFocusHandler;
    }
  }

  function escHandler(e) {
    if (e.key !== 'Escape') return;
    // Fecha modal Bootstrap aberto
    var open = document.querySelector('.modal.show');
    if (open) {
      var inst = bootstrap.Modal.getInstance(open);
      if (inst) inst.hide();
      return;
    }
    // Fecha modal custom
    var custom = document.querySelector('.ux-modal-open');
    if (custom) {
      custom.classList.remove('ux-modal-open');
      return;
    }
    // Fecha dropdown aberto
    var dd = document.querySelector('.dropdown-menu.show');
    if (dd) {
      var t = dd.previousElementSibling;
      if (t && t.click) t.click();
    }
  }

  function observeModals() {
    if (!window.MutationObserver) return;
    var obs = new MutationObserver(function(mutations) {
      mutations.forEach(function(m) {
        m.addedNodes.forEach(function(n) {
          if (n.nodeType !== 1) return;
          if (n.classList && n.classList.contains('modal') && n.classList.contains('show')) {
            trapFocus(n);
          }
        });
        if (m.type === 'attributes' && m.target.classList && m.target.classList.contains('modal')) {
          if (m.target.classList.contains('show')) {
            trapFocus(m.target);
          } else {
            releaseFocus(m.target);
          }
        }
      });
    });
    obs.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['class'] });
  }

  function init() {
    document.addEventListener('keydown', escHandler);
    observeModals();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.UX.a11y = {
    trapFocus: trapFocus,
    releaseFocus: releaseFocus,
  };
})(window, jQuery);
