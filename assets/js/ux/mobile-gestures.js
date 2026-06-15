/**
 * Mobile Gestures (F5.3) — Hammer.js bindings
 *
 * Ativa em:
 *  - .ux-swipe-list    : swipe-left = arquivar, swipe-right = excluir (customizavel via data-attrs)
 *  - [data-ux-pull]    : pull-to-refresh em uma lista
 *  - .ux-kanban-board  : swipe horizontal muda o status da OS
 *
 * Hammer.js e carregado lazy via hammer-loader.js
 */
(function(window, $) {
  'use strict';

  if (!window.UX) window.UX = {};

  function setupList(el) {
    window.UX.hammer.ready(function() {
      var $el = $(el);
      var mc = new Hammer.Manager(el);
      mc.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL, threshold: 8, velocity: 0.3 }));
      mc.add(new Hammer.Press({ time: 500 }));

      var startX = 0;
      mc.on('swipeleft', function() {
        var left = $el.data('swipe-left') || $el.data('swipeLeft');
        if (typeof left === 'string' && left.length) {
          window.location.href = left;
        } else if (typeof left === 'function') {
          left($el);
        } else {
          $el.addClass('ux-swiped-left');
          setTimeout(function() { $el.removeClass('ux-swiped-left'); }, 1500);
        }
      });
      mc.on('swiperight', function() {
        var right = $el.data('swipe-right') || $el.data('swipeRight');
        if (typeof right === 'string' && right.length) {
          window.location.href = right;
        } else if (typeof right === 'function') {
          right($el);
        } else {
          $el.addClass('ux-swiped-right');
          setTimeout(function() { $el.removeClass('ux-swiped-right'); }, 1500);
        }
      });
    });
  }

  function setupKanban(el) {
    window.UX.hammer.ready(function() {
      var mc = new Hammer.Manager(el);
      mc.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL, threshold: 30, velocity: 0.5 }));

      var $el = $(el);
      mc.on('swipeleft', function() { cycleStatus($el, +1); });
      mc.on('swiperight', function() { cycleStatus($el, -1); });
    });
  }

  function cycleStatus($card, delta) {
    var $status = $card.find('[data-kanban-status-id]');
    if ($status.length === 0) return;
    var list = $card.closest('.ux-kanban-board').data('statuses') || [];
    if (!list.length) {
      // tenta deduzir
      list = $.map($card.closest('.ux-kanban-board').find('[data-kanban-status-id]'), function(n){
        return parseInt($(n).attr('data-kanban-status-id'), 10);
      });
      list = Array.from(new Set(list));
    }
    var current = parseInt($status.attr('data-kanban-status-id'), 10);
    var idx = list.indexOf(current);
    var next = list[(idx + delta + list.length) % list.length];
    if (next && next !== current) {
      $card.attr('data-kanban-target-status', next);
      // Visual feedback
      $card.css('transition', 'transform .25s ease');
      $card.css('transform', 'translateX(' + (delta * 30) + 'px)');
      setTimeout(function() {
        $card.css('transform', '');
      }, 200);
      // Dispara evento custom
      $card.trigger('ux:kanban:swipe', { from: current, to: next });
    }
  }

  function setupPullRefresh(el) {
    var $el = $(el);
    var threshold = parseInt($el.data('ux-pull') || '70', 10);
    var startY = 0;
    var pulling = false;

    $el.css('overflow', 'auto');
    $el.on('touchstart', function(e) {
      if (this.scrollTop <= 0) {
        startY = e.touches[0].clientY;
        pulling = true;
      }
    });
    $el.on('touchmove', function(e) {
      if (!pulling) return;
      var y = e.touches[0].clientY - startY;
      if (y > 0 && y < threshold * 2) {
        $el.find('.ux-pull-indicator').css('transform', 'translateY(' + Math.min(y, threshold) + 'px)');
      }
    });
    $el.on('touchend', function(e) {
      if (!pulling) return;
      var y = (e.changedTouches[0].clientY - startY);
      if (y > threshold) {
        $el.trigger('ux:pull:refresh');
      }
      $el.find('.ux-pull-indicator').css('transform', '');
      pulling = false;
    });
  }

  function init() {
    document.querySelectorAll('.ux-swipe-list').forEach(setupList);
    document.querySelectorAll('.ux-kanban-board .ux-kanban-card').forEach(setupKanban);
    document.querySelectorAll('[data-ux-pull]').forEach(setupPullRefresh);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Observa novos elementos
  if (window.MutationObserver) {
    var obs = new MutationObserver(function() { init(); });
    obs.observe(document.body, { childList: true, subtree: true });
  }

  window.UX.gestures = { init: init };
})(window, jQuery);
