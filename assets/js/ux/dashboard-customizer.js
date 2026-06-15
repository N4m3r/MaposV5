/**
 * Dashboard Customizer — Drag-and-drop de widgets
 * F3.1 do Plano UX
 *
 * - Ativa o modo "editar layout" quando o usuario clica no botao
 * - Permite arrastar e reordenar widgets
 * - Permite ocultar/mostrar widgets via checkbox
 * - Salva a configuracao no backend por usuario
 *
 * Dependencias:
 *   - SortableJS (sortable-loader.js)
 *   - jQuery
 *   - window.UX.notify (opcional)
 */
(function(window, $) {
  'use strict';

  var BaseUrl = (typeof window.BaseUrl === 'string') ? window.BaseUrl : '/';
  var STORAGE_KEY = 'ux-dashboard-layout-cache'; // cache otimista

  var api = {
    editing: false,
    sortableInstances: [],

    /** Ativa o modo de edicao */
    enableEdit: function() {
      if (this.editing) return;
      this.editing = true;
      document.body.classList.add('ux-dashboard-editing');
      this.applySortable();
      this.refreshToggleButtons();
      this.refreshVisibility();
      if (window.notify) window.notify.info('Modo de edição ativado. Arraste os widgets para reordenar.');
    },

    /** Desativa o modo de edicao */
    disableEdit: function() {
      if (!this.editing) return;
      this.editing = false;
      document.body.classList.remove('ux-dashboard-editing');
      this.destroySortable();
      this.refreshToggleButtons();
      this.save();
    },

    /** Persiste o estado atual no backend */
    save: function() {
      var widgets = document.querySelectorAll('[data-widget-key]');
      var layout = [];
      var visibility = {};
      widgets.forEach(function(w) {
        var key = w.getAttribute('data-widget-key');
        layout.push(key);
        visibility[key] = !w.classList.contains('ux-widget-hidden');
      });
      var payload = { layout: layout, visibility: visibility };
      // Cache local
      try { localStorage.setItem(STORAGE_KEY, JSON.stringify(payload)); } catch (e) {}
      // Backend
      $.ajax({
        url: BaseUrl + 'index.php/dashboard_layout/salvar',
        type: 'POST',
        dataType: 'json',
        data: payload
      });
    },

    /** Aplica layout salvo (ordem e visibilidade) */
    applySavedLayout: function() {
      // 1) Cache local (imediato)
      var cached = null;
      try { cached = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null'); } catch (e) {}
      if (cached && cached.layout) {
        this.applyLayout(cached);
      }
      // 2) Backend (autoritativo)
      $.ajax({
        url: BaseUrl + 'index.php/dashboard_layout/listar',
        type: 'GET',
        dataType: 'json',
        success: function(resp) {
          if (resp && resp.success && resp.data && resp.data.layout) {
            api.applyLayout(resp.data);
            try { localStorage.setItem(STORAGE_KEY, JSON.stringify(resp.data)); } catch (e) {}
          }
        }
      });
    },

    applyLayout: function(data) {
      var container = document.querySelector('.dashboard-container');
      if (!container) return;
      var order = data.layout || [];
      var vis = data.visibility || {};

      // Esconde widgets ocultos
      Object.keys(vis).forEach(function(key) {
        var w = document.querySelector('[data-widget-key="' + key + '"]');
        if (w) {
          if (vis[key]) w.classList.remove('ux-widget-hidden');
          else w.classList.add('ux-widget-hidden');
        }
      });

      // Reordena
      order.forEach(function(key) {
        var w = document.querySelector('[data-widget-key="' + key + '"]');
        if (w) container.appendChild(w); // appendChild move o elemento
      });
    },

    /** Cria instancias Sortable nos containers */
    applySortable: function() {
      var self = this;
      var containers = document.querySelectorAll('.dashboard-container');
      containers.forEach(function(c) {
        if (c.__uxSortable) return; // ja existe
        var s = new window.Sortable(c, {
          animation: 180,
          handle: '[data-widget-handle]',
          draggable: '[data-widget-key]',
          ghostClass: 'ux-widget-ghost',
          chosenClass: 'ux-widget-chosen',
          onEnd: function() {
            self.save();
          }
        });
        c.__uxSortable = s;
        self.sortableInstances.push(s);
      });
    },

    destroySortable: function() {
      this.sortableInstances.forEach(function(s) {
        try { s.destroy(); } catch (e) {}
      });
      this.sortableInstances = [];
    },

    /** Restaura o layout padrao (remove customizacao) */
    reset: function() {
      try { localStorage.removeItem(STORAGE_KEY); } catch (e) {}
      $.ajax({
        url: BaseUrl + 'index.php/dashboard_layout/resetar',
        type: 'POST',
        dataType: 'json',
        success: function() { location.reload(); }
      });
    },

    /** Sincroniza botoes de "ocultar widget" */
    refreshVisibility: function() {
      var widgets = document.querySelectorAll('[data-widget-key]');
      widgets.forEach(function(w) {
        var key = w.getAttribute('data-widget-key');
        var btn = w.querySelector('[data-widget-toggle]');
        if (btn) {
          if (w.classList.contains('ux-widget-hidden')) {
            btn.innerHTML = '<i class="bx bx-show"></i>';
            btn.title = 'Mostrar';
          } else {
            btn.innerHTML = '<i class="bx bx-hide"></i>';
            btn.title = 'Ocultar';
          }
        }
      });
    },

    refreshToggleButtons: function() {
      var btn = document.getElementById('ux-dashboard-edit-btn');
      var resetBtn = document.getElementById('ux-dashboard-reset-btn');
      if (!btn) return;
      if (this.editing) {
        btn.innerHTML = '<i class="bx bx-check"></i> Concluir';
        btn.classList.add('active');
        if (resetBtn) resetBtn.style.display = '';
      } else {
        btn.innerHTML = '<i class="bx bx-layout"></i> Editar layout';
        btn.classList.remove('active');
        if (resetBtn) resetBtn.style.display = 'none';
      }
    }
  };

  // ====================================================================
  // Wire-up
  // ====================================================================
  $(function() {
    // 1) Botao de editar
    $(document).on('click', '#ux-dashboard-edit-btn', function(e) {
      e.preventDefault();
      if (api.editing) api.disableEdit();
      else api.enableEdit();
    });

    // 2) Botao de resetar
    $(document).on('click', '#ux-dashboard-reset-btn', function(e) {
      e.preventDefault();
      if (confirm('Restaurar o layout padrao do dashboard? Suas customizacoes serao perdidas.')) {
        api.reset();
      }
    });

    // 3) Botao de ocultar/mostrar widget individual
    $(document).on('click', '[data-widget-toggle]', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var w = this.closest('[data-widget-key]');
      if (!w) return;
      w.classList.toggle('ux-widget-hidden');
      api.refreshVisibility();
      api.save();
    });

    // 4) Aplica layout salvo na carga inicial
    api.applySavedLayout();
  });

  // API publica
  window.UX.dashboard = api;
})(window, jQuery);
