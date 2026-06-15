/**
 * Bottom Navigation (Mobile) — F5.1
 *
 * Adiciona uma barra de navegação inferior fixa em telas < 768px
 * com 5 botoes principais: Inicio / OS / + / Chat / Mais
 *
 * Ativa via detecção de viewport E se o body nao tiver
 * data-no-mobile-nav="true" (para paginas que nao devem ter).
 */
(function(window, $) {
  'use strict';

  var BaseUrl = (typeof window.BaseUrl === 'string') ? window.BaseUrl : '/';

  var ITEMS = [
    { icon: 'bx-home',       label: 'Inicio', url: 'dashboard',             match: 'dashboard' },
    { icon: 'bx-file',       label: 'OS',     url: 'os',                    match: 'os' },
    { icon: 'bx-plus-circle',label: '',       url: 'os/adicionar',          primary: true },
    { icon: 'bx-chat',       label: 'Chat',   url: 'agente_ia',             match: 'agente' },
    { icon: 'bx-menu',       label: 'Mais',   url: '#menu-mobile',          action: 'menu' },
  ];

  function isMobile() {
    return window.innerWidth < 768;
  }

  function build() {
    if (document.getElementById('ux-bottom-nav')) return;
    if (document.body.getAttribute('data-no-mobile-nav') === 'true') return;

    var path = window.location.pathname;
    var html = '<nav id="ux-bottom-nav" class="ux-bottom-nav">';
    ITEMS.forEach(function(item) {
      var active = '';
      if (item.match && path.indexOf(item.match) >= 0) active = ' active';
      if (item.primary) {
        html += '<a href="' + BaseUrl + 'index.php/' + item.url + '" class="ux-bn-item ux-bn-primary' + active + '" title="Nova OS">'
              + '<i class="bx ' + item.icon + '"></i></a>';
      } else if (item.action === 'menu') {
        html += '<a href="#" class="ux-bn-item" data-ux-bn-menu title="' + item.label + '">'
              + '<i class="bx ' + item.icon + '"></i>'
              + '<span>' + item.label + '</span></a>';
      } else {
        html += '<a href="' + BaseUrl + 'index.php/' + item.url + '" class="ux-bn-item' + active + '" title="' + item.label + '">'
              + '<i class="bx ' + item.icon + '"></i>'
              + '<span>' + item.label + '</span></a>';
      }
    });
    html += '</nav>';
    document.body.insertAdjacentHTML('beforeend', html);
    // Ajusta o padding do body para nao cobrir o conteudo
    document.body.classList.add('ux-has-bottom-nav');
  }

  function destroy() {
    var n = document.getElementById('ux-bottom-nav');
    if (n) n.remove();
    document.body.classList.remove('ux-has-bottom-nav');
  }

  function update() {
    if (isMobile()) build();
    else destroy();
  }

  // Click no item "Mais" - abre o menu lateral mobile (offcanvas)
  $(document).on('click', '[data-ux-bn-menu]', function(e) {
    e.preventDefault();
    var btn = document.querySelector('[data-bs-target="#mobileNavOffcanvas"]');
    if (btn) btn.click();
    else {
      // Fallback: rola para o topo onde fica o menu
      var nav = document.querySelector('.navebarn');
      if (nav) nav.scrollIntoView({ behavior: 'smooth' });
    }
  });

  $(function() {
    update();
    var t;
    window.addEventListener('resize', function() {
      clearTimeout(t);
      t = setTimeout(update, 150);
    });
  });

  window.UX.bottomNav = { build: build, destroy: destroy, refresh: update };
})(window, jQuery);
