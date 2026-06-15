/**
 * Theme Customizer — Troca de cor primaria e modo escuro automatico
 * F3.3 (cor primaria) + F3.6 (prefers-color-scheme)
 *
 * Carrega:
 *   - Cor primaria salva em localStorage['ux-accent']
 *   - Modo de tema: 'auto' | 'light' | 'dark' (localStorage['ux-theme-mode'])
 *   - Quando 'auto', segue prefers-color-scheme do SO
 *
 * Aplica:
 *   - Atributo data-accent="blue|indigo|..." em <html>
 *   - Atributo data-theme="..." em <body>, atualizando ao mudar preferencia do SO
 */
(function(window, $) {
  'use strict';

  var BaseUrl = (typeof window.BaseUrl === 'string') ? window.BaseUrl : '/';

  // ====================================================================
  // Persistencia
  // ====================================================================
  var STORAGE_ACCENT = 'ux-accent';
  var STORAGE_MODE   = 'ux-theme-mode';

  function getStored(key, fallback) {
    try { return localStorage.getItem(key) || fallback; } catch (e) { return fallback; }
  }
  function setStored(key, val) {
    try { localStorage.setItem(key, val); } catch (e) {}
  }

  // ====================================================================
  // Cor primaria (F3.3)
  // ====================================================================
  var ACCENT_PALETTE = ['blue','indigo','purple','pink','red','orange','green','teal','cyan','dark'];

  function applyAccent(accent) {
    if (!accent || ACCENT_PALETTE.indexOf(accent) < 0) accent = 'blue';
    document.documentElement.setAttribute('data-accent', accent);
    setStored(STORAGE_ACCENT, accent);
    // Notifica Backend (sincroniza com sessao)
    if ($.ajax) {
      $.ajax({
        url: BaseUrl + 'index.php/notificacoes/trocar_tema',
        type: 'POST',
        dataType: 'json',
        data: { acento: accent }
      });
    }
    // Marca swatch ativa
    $('.ux-color-swatch').removeClass('active');
    $('.ux-color-swatch[data-accent="' + accent + '"]').addClass('active');
  }

  // ====================================================================
  // Modo de tema: auto / light / dark (F3.6)
  // ====================================================================
  var ALL_THEMES_LIGHT = ['default', 'white', 'whitegreen', 'whiteblack'];
  var ALL_THEMES_DARK  = ['puredark', 'darkviolet', 'darkorange'];

  function currentIsDark() {
    var t = document.body.getAttribute('data-theme') || 'default';
    return ALL_THEMES_DARK.indexOf(t) >= 0;
  }

  function systemPrefersDark() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  }

  function toggleTheme() {
    // Cycle: light theme <-> dark theme (mantendo o accent)
    var currentTheme = document.body.getAttribute('data-theme') || 'default';
    var nextIsDark = !currentIsDark();
    var nextTheme = nextIsDark ? 'puredark' : 'white';
    // Se o usuario ja usou o mode 'auto', ao clicar passa a ser 'manual'
    setStored(STORAGE_MODE, 'manual');
    setStored('ux-manual-theme', nextTheme);
    applyTheme(nextTheme);
    // Notifica backend (mantem o switch de dark/light)
    if ($.ajax) {
      $.ajax({ url: BaseUrl + 'index.php/notificacoes/trocar_tema', type: 'POST', dataType: 'json', data: { tema: nextTheme } });
    }
  }

  function applyTheme(theme) {
    document.body.setAttribute('data-theme', theme);
    // Atualiza icone do botao (sun <-> moon)
    var themeIcon = document.getElementById('theme-icon');
    if (themeIcon) {
      var iconName = ALL_THEMES_DARK.indexOf(theme) >= 0 ? 'sun' : 'moon';
      var svgUse = themeIcon.querySelector('use');
      var svgBaseUrl = (window.BaseUrl || '/') + 'assets/svg/icons.svg';
      if (svgUse) {
        svgUse.setAttribute('href', svgBaseUrl + '#' + iconName);
        svgUse.setAttribute('xlink:href', svgBaseUrl + '#' + iconName);
      } else {
        themeIcon.innerHTML = '<svg class="svg-icon" width="20" height="20" aria-hidden="true"><use href="' + svgBaseUrl + '#' + iconName + '"/></svg>';
      }
    }
    $(document).trigger('themeChanged', [theme]);
  }

  function applyMode() {
    var mode = getStored(STORAGE_MODE, 'auto');
    if (mode === 'auto') {
      var dark = systemPrefersDark();
      applyTheme(dark ? 'puredark' : 'white');
    } else {
      // manual: usa o tema salvo pelo usuario
      var manual = getStored('ux-manual-theme', null);
      if (manual) applyTheme(manual);
    }
  }

  // Reage a mudanca do SO em tempo real (F3.6)
  if (window.matchMedia) {
    var mq = window.matchMedia('(prefers-color-scheme: dark)');
    if (mq.addEventListener) {
      mq.addEventListener('change', function() {
        if (getStored(STORAGE_MODE, 'auto') === 'auto') applyMode();
      });
    } else if (mq.addListener) {
      mq.addListener(function() {
        if (getStored(STORAGE_MODE, 'auto') === 'auto') applyMode();
      });
    }
  }

  // ====================================================================
  // Wire-up
  // ====================================================================
  $(function() {
    // 1) Aplica accent persistido
    var savedAccent = getStored(STORAGE_ACCENT, null);
    if (savedAccent) applyAccent(savedAccent);

    // 2) Aplica mode persistido (se for auto e o body ja tem theme do servidor, deixa)
    var mode = getStored(STORAGE_MODE, 'auto');
    if (mode === 'auto') {
      applyMode();
    }

    // 3) Botao de tema agora alterna entre light/dark
    $('#btn-toggle-theme').off('click.themeCustomizer').on('click.themeCustomizer', function(e) {
      e.preventDefault();
      toggleTheme();
    });

    // 4) Color picker
    $(document).on('click', '.ux-color-swatch', function() {
      var accent = $(this).data('accent');
      applyAccent(accent);
    });

    // 5) Theme mode selector (radio)
    $(document).on('change', 'input[name="ux-theme-mode"]', function() {
      var newMode = $(this).val();
      setStored(STORAGE_MODE, newMode);
      if (newMode === 'auto') {
        applyMode();
      }
    });

    // 6) Locale selector (idioma) - carrega via AJAX quando o modal abrir
    var localeLoaded = false;
    function loadLocales() {
      if (localeLoaded) return;
      localeLoaded = true;
      var $list = $('#ux-locale-list');
      if (!$list.length) return;
      var base = (window.BaseUrl || '/') + 'index.php/ux_locale';
      $.getJSON(base + '/listar', function(resp) {
        if (!resp || !resp.success) return;
        var locales = resp.data.available || [];
        var current = resp.data.current || 'pt-BR';
        $list.empty();
        if (locales.length === 0) {
          $list.html('<em style="font-size:0.82rem;color:#6c757d;">Nenhum idioma cadastrado.</em>');
          return;
        }
        locales.forEach(function(loc) {
          var checked = (loc.locale === current) ? ' checked' : '';
          var rtl = loc.rtl ? ' (RTL)' : '';
          var html = '<label class="ux-theme-mode-option">'
                   + '<input type="radio" name="ux-locale" value="' + loc.locale + '"' + checked + '>'
                   + '<span class="ux-theme-mode-icon"><i class="bx bx-globe"></i></span>'
                   + '<span class="ux-theme-mode-info">'
                   + '<strong>' + loc.name + rtl + '</strong>'
                   + '<small>' + loc.english + '</small>'
                   + '</span>'
                   + '</label>';
          $list.append(html);
        });
      });
    }
    // Expõe para o seletor de tema abrir
    window.UxTheme = window.UxTheme || {};
    var _origOpen = window.UxTheme.open;
    window.UxTheme.open = function() {
      if (_origOpen) _origOpen();
      loadLocales();
    };
    // Carrega ja (caso o modal ja esteja aberto)
    loadLocales();
    $(document).on('change', 'input[name="ux-locale"]', function() {
      var newLocale = $(this).val();
      $.post((window.BaseUrl || '/') + 'index.php/ux_locale/setar', { locale: newLocale }, function() {
        location.reload();
      });
    });
  });

  // ====================================================================
  // API publica
  // ====================================================================
  window.UX.theme = {
    applyAccent: applyAccent,
    applyTheme:  applyTheme,
    applyMode:   applyMode,
    setMode:     function(m) { setStored(STORAGE_MODE, m); applyMode(); },
    palette:     ACCENT_PALETTE
  };

})(window, jQuery);
