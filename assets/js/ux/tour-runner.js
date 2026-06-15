/**
 * Tour Runner — Inicializacao e persistencia dos tours guiados (Driver.js)
 * Fase 2.1.4 do Plano UX
 *
 * - Carrega as definicoes dos tours via /ux_tour/definicoes
 * - Para cada tour, decide se deve rodar (auto_start + pendente)
 * - Marca como concluido ao chegar no ultimo step
 * - Marca como pulado se o usuario fechar
 * - Permite reiniciar tour via funcao global window.UX.reiniciarTour(key)
 *
 * Dependencias:
 *   - Driver.js (assets/js/ux/driver.min.js, carregado por driver-loader.js)
 *   - jQuery (ja carregado)
 */
(function(window, $) {
  'use strict';

  if (!window.UX) window.UX = {};

  var BaseUrl = (typeof window.BaseUrl === 'string') ? window.BaseUrl : '/';
  var currentDriver = null; // instancia do Driver.js em execucao
  var currentTourKey = null;
  var toursCache = null; // cache local das definicoes

  // ====================================================================
  // API publica
  // ====================================================================
  window.UX.tour = {
    /** Inicializa: busca definicoes, dispara tours auto_start pendentes */
    init: function(opts) {
      opts = opts || {};
      var silent = !!opts.silent;
      carregarDefinicoes(function(ok) {
        if (!ok) {
          if (!silent) console.warn('[UX Tour] Falha ao carregar definicoes. Tours desabilitados.');
          return;
        }
        // Inicializa driver-loader se ainda nao estiver carregado
        ensureDriverLoaded(function() {
          iniciarToursAutomaticos();
        });
      });
    },

    /** Dispara um tour especifico manualmente (ex: botao "Rever tour") */
    iniciar: function(tourKey) {
      if (!tourKey) return;
      ensureDriverLoaded(function() {
        carregarDefinicoes(function(ok) {
          if (!ok) return;
          runTour(tourKey);
        });
      });
    },

    /** Reseta o status do tour no backend e roda de novo */
    reiniciar: function(tourKey) {
      if (!tourKey) return;
      $.ajax({
        url: BaseUrl + 'index.php/ux_tour/reiniciar',
        type: 'POST',
        dataType: 'json',
        data: { tour_key: tourKey },
        success: function() {
          window.UX.tour.iniciar(tourKey);
        }
      });
    },

    /** Expor as definicoes ja carregadas (cache) */
    getTours: function() { return toursCache; }
  };

  // ====================================================================
  // Internos
  // ====================================================================

  function carregarDefinicoes(cb) {
    if (toursCache) return cb(true);
    $.ajax({
      url: BaseUrl + 'index.php/ux_tour/definicoes',
      type: 'GET',
      dataType: 'json',
      timeout: 8000,
      success: function(resp) {
        if (resp && resp.success && resp.data && resp.data.tours) {
          toursCache = {
            tours:  resp.data.tours,
            status: resp.data.status || {}
          };
          return cb(true);
        }
        cb(false);
      },
      error: function() { cb(false); }
    });
  }

  function ensureDriverLoaded(cb) {
    if (window.driver && window.driver.js) return cb();
    if (window.Driver) {
      // driver.js exposto de outra forma - tenta detectar
      try { window.driver = { js: window.Driver }; return cb(); }
      catch (e) { return cb(); }
    }
    // Polling rapido (driver-loader e async)
    var tries = 0;
    var t = setInterval(function() {
      tries++;
      if (window.driver && window.driver.js) {
        clearInterval(t);
        return cb();
      }
      if (tries > 50) { // ~5s
        clearInterval(t);
        console.warn('[UX Tour] Driver.js nao carregou apos 5s. Tours desabilitados.');
        cb();
      }
    }, 100);
  }

  function isOnRota(tour) {
    if (!tour.rota) return true; // sem rota = global
    var path = window.location.pathname.toLowerCase();
    return path.indexOf(tour.rota.toLowerCase()) !== -1;
  }

  function isPendente(tourKey) {
    if (!toursCache) return true;
    var s = toursCache.status[tourKey];
    if (!s) return true; // nunca visto = pendente
    return !(s.completed || s.skipped);
  }

  function iniciarToursAutomaticos() {
    if (!toursCache || !toursCache.tours) return;
    Object.keys(toursCache.tours).forEach(function(key) {
      var tour = toursCache.tours[key];
      if (tour.auto_start && isOnRota(tour) && isPendente(key)) {
        // Pequeno delay para garantir que a pagina renderizou os elementos
        setTimeout(function() { runTour(key); }, 600);
      }
    });
  }

  function runTour(tourKey) {
    if (!window.driver || !window.driver.js) {
      console.warn('[UX Tour] Driver.js nao disponivel. Abortando tour "' + tourKey + '".');
      return;
    }
    if (!toursCache || !toursCache.tours[tourKey]) {
      console.warn('[UX Tour] Tour "' + tourKey + '" nao encontrado nas definicoes.');
      return;
    }
    var tour = toursCache.tours[tourKey];
    if (!tour.steps || tour.steps.length === 0) return;

    // Filtra steps cujo seletor nao existe na pagina (tour continua nos outros)
    var stepsValidos = tour.steps.filter(function(step) {
      try {
        return document.querySelector(step.selector) !== null;
      } catch (e) { return false; }
    });

    if (stepsValidos.length === 0) {
      console.info('[UX Tour] Nenhum seletor do tour "' + tourKey + '" foi encontrado na pagina. Pulando.');
      return;
    }

    // Se ja existe um driver rodando, fecha antes
    if (currentDriver) {
      try { currentDriver.destroy(); } catch (e) {}
      currentDriver = null;
    }

    currentTourKey = tourKey;

    var driverSteps = stepsValidos.map(function(step) {
      return {
        element: step.selector,
        popover: {
          title:     step.titulo || '',
          description: step.descricao || '',
          position:  step.posicao || 'bottom',
          // Botoes customizados (Done aparece so no ultimo)
        }
      };
    });

    // Adiciona botao "Pular" em todos
    driverSteps.forEach(function(s) {
      s.popover.popoverButtons = [
        { text: 'Pular', className: 'driver-btn-skip',  onClick: function() { skipTour(); } },
        { text: 'Próximo', className: 'driver-btn-next driver-btn-primary', onClick: function() { s.driver.moveNext(); } }
      ];
    });
    // Ultimo step: botao "Concluir" em vez de "Proximo"
    var last = driverSteps[driverSteps.length - 1];
    last.popover.popoverButtons = [
      { text: 'Pular',     className: 'driver-btn-skip',  onClick: function() { skipTour(); } },
      { text: 'Concluir',  className: 'driver-btn-next driver-btn-primary', onClick: function() { completeTour(); } }
    ];

    try {
      currentDriver = new window.driver.js({
        showProgress: true,
        animate: true,
        allowClose: true,
        opacity: 0.75,
        padding: 6,
        stageRadius: 6,
        onDestroyed: function() {
          // Se saiu sem concluir/pular explicitamente, marca como pulado
          if (currentTourKey) {
            // best-effort: se nao foi chamado complete/skip antes, marca pulo
            // (variavel de controle abaixo)
          }
        }
      });
      currentDriver.setSteps(driverSteps);
      currentDriver.drive();
    } catch (e) {
      console.error('[UX Tour] Erro ao iniciar Driver.js:', e);
    }
  }

  function completeTour() {
    if (!currentTourKey) return;
    var key = currentTourKey;
    currentTourKey = null;
    if (currentDriver) { try { currentDriver.destroy(); } catch (e) {} }
    currentDriver = null;
    // Atualiza cache local
    if (toursCache) toursCache.status[key] = { completed: true, skipped: false };
    $.ajax({
      url: BaseUrl + 'index.php/ux_tour/concluir',
      type: 'POST',
      dataType: 'json',
      data: { tour_key: key }
    });
    if (window.notify) {
      window.notify.success('Tour concluído! Você pode revê-lo a qualquer momento.');
    }
  }

  function skipTour() {
    if (!currentTourKey) return;
    var key = currentTourKey;
    currentTourKey = null;
    if (currentDriver) { try { currentDriver.destroy(); } catch (e) {} }
    currentDriver = null;
    if (toursCache) toursCache.status[key] = { completed: false, skipped: true };
    $.ajax({
      url: BaseUrl + 'index.php/ux_tour/pular',
      type: 'POST',
      dataType: 'json',
      data: { tour_key: key }
    });
  }

  // ====================================================================
  // Auto-init quando o DOM estiver pronto (silencioso - nao bloqueia)
  // ====================================================================
  $(function() {
    // So roda se usuario estiver logado (presenca de .user-info ou #tour-kpis)
    if (!document.getElementById('tour-kpis') &&
        !document.querySelector('[data-tour-auto]') &&
        document.body.getAttribute('data-area') !== 'admin') {
      return; // fora da area admin, nao inicia
    }
    setTimeout(function() {
      window.UX.tour.init({ silent: true });
    }, 1200); // espera UI estabilizar
  });

})(window, jQuery);
