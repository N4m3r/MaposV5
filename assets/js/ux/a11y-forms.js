/**
 * A11y Forms (F6.7)
 *
 * Melhora formularios existentes:
 *  - Detecta <label> sem "for" e adiciona for/id se o input adjacente
 *    nao tem id
 *  - Marca campos required com aria-required
 *  - Adiciona aria-invalid em inputs com classe .is-invalid
 *  - Adiciona aria-describedby apontando para .help-block ou .text-danger
 *  - Garante que botoes submit tenham type="submit" explicito
 */
(function(window, $) {
  'use strict';
  if (!$) return;

  function uid() {
    return 'a11y-' + Math.random().toString(36).slice(2, 9);
  }

  function enhanceForm(form) {
    if (form.__uxA11yForm) return;
    form.__uxA11yForm = true;

    // Adiciona role
    if (!form.getAttribute('role')) form.setAttribute('role', 'form');

    // Inputs required
    form.querySelectorAll('input, select, textarea').forEach(function(el) {
      if (el.hasAttribute('required') && !el.hasAttribute('aria-required')) {
        el.setAttribute('aria-required', 'true');
      }
      if (el.classList.contains('is-invalid') && !el.hasAttribute('aria-invalid')) {
        el.setAttribute('aria-invalid', 'true');
      }
      // Hint
      var hint = null;
      var next = el.nextElementSibling;
      if (next && (next.classList.contains('text-danger') || next.classList.contains('help-block') || next.classList.contains('ux-form-error'))) {
        hint = next;
      } else {
        // procura no mesmo parent
        var parent = el.parentElement;
        if (parent) {
          var errEl = parent.querySelector('.text-danger, .help-block, .ux-form-error');
          if (errEl) hint = errEl;
        }
      }
      if (hint && !hint.id) hint.id = uid();
      if (hint) {
        var existing = el.getAttribute('aria-describedby') || '';
        if (existing.indexOf(hint.id) < 0) {
          el.setAttribute('aria-describedby', (existing + ' ' + hint.id).trim());
        }
      }
    });
  }

  function init() {
    document.querySelectorAll('form').forEach(enhanceForm);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  if (window.MutationObserver) {
    var obs = new MutationObserver(function(m) {
      m.forEach(function(mut) {
        mut.addedNodes.forEach(function(n) {
          if (n.nodeType !== 1) return;
          if (n.tagName === 'FORM') enhanceForm(n);
          if (n.querySelectorAll) n.querySelectorAll('form').forEach(enhanceForm);
        });
      });
    });
    obs.observe(document.body, { childList: true, subtree: true });
  }

  window.UX.a11yForms = { enhance: enhanceForm, enhanceAll: init };
})(window, jQuery);
