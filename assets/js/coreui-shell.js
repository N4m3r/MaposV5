/**
 * coreui-shell.js — handlers para o shell CoreUI das views PHP legadas.
 *
 * Ativado automaticamente quando o body tem a classe `coreui-shell`.
 * Complementa o tema via body[data-theme] ja presente no mapos.css.
 *
 * Recursos:
 * - Sidebar collapse/expand (botao hamburger mobile)
 * - Theme switcher (sincroniza com /notificacoes/trocar_tema se existir)
 * - Marcar notificacao como lida (compat com carregaNotificacoes do rodape.php)
 */
(function (window, $) {
    'use strict';
    if (!window.UX) window.UX = {};

    function persistTheme(id) {
        // Persiste no CodeIgniter (silencioso, sem redirect)
        try {
            var body = document.body;
            var data = new URLSearchParams();
            data.set('tema', id);
            fetch('/index.php/notificacoes/trocar_tema', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: data.toString(),
                credentials: 'same-origin',
            }).catch(function () { /* silenciar */ });
        } catch (e) { /* silenciar */ }
    }

    function applyTheme(id) {
        document.body.setAttribute('data-theme', id);
        try { localStorage.setItem('app_theme', id); } catch (e) {}
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: id }));
        persistTheme(id);
    }

    function initSidebar() {
        var btn = document.getElementById('btn-toggle-sidebar-mobile');
        var sidebar = document.getElementById('app-sidebar');
        var main = document.getElementById('app-main');
        if (!btn || !sidebar) return;

        btn.addEventListener('click', function () {
            sidebar.classList.toggle('visible-mobile');
        });

        // Fechar sidebar mobile ao clicar fora
        document.addEventListener('click', function (e) {
            if (!sidebar.classList.contains('visible-mobile')) return;
            if (sidebar.contains(e.target) || btn.contains(e.target)) return;
            sidebar.classList.remove('visible-mobile');
        });
    }

    function initThemeSwitcher() {
        var links = document.querySelectorAll('[data-theme-pick]');
        links.forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                var id = a.getAttribute('data-theme-pick');
                applyTheme(id);
            });
        });

        // Aplica tema persistido em localStorage (caso backend ainda nao tenha gravado)
        try {
            var stored = localStorage.getItem('app_theme');
            if (stored && document.body.getAttribute('data-theme') !== stored) {
                document.body.setAttribute('data-theme', stored);
            }
        } catch (e) {}
    }

    function initNotifMarkAll() {
        var btn = document.getElementById('notif-marcar-todas');
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            try {
                fetch('/index.php/notificacoes/marcar_lida', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: '',
                    credentials: 'same-origin',
                }).then(function () {
                    var count = document.getElementById('notif-count');
                    if (count) count.style.display = 'none';
                    // Atualiza lista se carregaNotificacoes existir
                    if (typeof window.carregarNotificacoes === 'function') {
                        window.carregarNotificacoes();
                    }
                });
            } catch (err) { /* silenciar */ }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (!document.body.classList.contains('coreui-shell')) return;
        initSidebar();
        initThemeSwitcher();
        initNotifMarkAll();
        window.UX.applyTheme = applyTheme;
    });
})(window, window.jQuery);
