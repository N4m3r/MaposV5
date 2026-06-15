/**
 * Auto-CEP — Preenchimento automatico de endereco via ViaCEP (F4.4)
 *
 * - Detecta campos com name="cep" (ou data-cep-field)
 * - Ao sair do campo (blur), busca o endereco no ViaCEP
 *   https://viacep.com.br/ws/{cep}/json/
 * - Preenche automaticamente: logradouro, bairro, cidade, uf
 *   (mapeamento via data-cep-target="campo_destino" em <input>)
 *
 * Fallback: se a requisicao falhar, o usuario preenche manualmente.
 * Sem cache: cada consulta vai ao ViaCEP (eles sao free).
 */
(function(window, $) {
  'use strict';

  var TIMEOUT = 6000; // 6 segundos

  // Mapa de campos de destino: chave_destino -> name do input
  // Cada pagina pode customizar via <input data-cep-target="cep-logradouro" data-cep-source="cep">
  // Para manter retrocompatibilidade, usamos um mapa padrao.
  var DEFAULT_TARGETS = {
    'logradouro':  ['logradouro', 'endereco', 'rua', 'street'],
    'bairro':      ['bairro', 'neighborhood', 'distrito'],
    'cidade':      ['cidade', 'localidade', 'city', 'municipio'],
    'uf':          ['uf', 'estado', 'state', 'sigla_estado'],
    'ibge':        ['ibge', 'codigo_ibge', 'codigoMunicipio'],
  };

  function onlyDigits(s) { return (s || '').replace(/\D/g, ''); }

  function findField(target) {
    // 1) data-cep-target
    var el = document.querySelector('[data-cep-target="' + target + '"]');
    if (el) return el;
    // 2) name conhecido
    var names = DEFAULT_TARGETS[target] || [target];
    for (var i = 0; i < names.length; i++) {
      var n = names[i];
      el = document.querySelector('[name="' + n + '"], [name="' + n + '[]"], [id="' + n + '"]');
      if (el) return el;
    }
    return null;
  }

  function setValue(el, value) {
    if (!el || value == null) return;
    el.value = value;
    // Dispara change para reatividade
    var ev;
    try {
      ev = new Event('change', { bubbles: true });
    } catch (e) {
      ev = document.createEvent('Event');
      ev.initEvent('change', true, true);
    }
    el.dispatchEvent(ev);
  }

  function fetchCep(cep, cb) {
    var url = 'https://viacep.com.br/ws/' + cep + '/json/';
    if (window.fetch) {
      var controller = (window.AbortController) ? new AbortController() : null;
      var opts = controller ? { signal: controller.signal } : {};
      var timer = setTimeout(function() { if (controller) controller.abort(); }, TIMEOUT);
      fetch(url, opts)
        .then(function(r) { return r.json(); })
        .then(function(d) { clearTimeout(timer); cb(null, d); })
        .catch(function(e) { clearTimeout(timer); cb(e); });
    } else {
      // Fallback XHR
      var xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.timeout = TIMEOUT;
      xhr.onload = function() {
        if (xhr.status === 200) {
          try { cb(null, JSON.parse(xhr.responseText)); } catch (e) { cb(e); }
        } else cb(new Error('HTTP ' + xhr.status));
      };
      xhr.onerror = function() { cb(new Error('Network error')); };
      xhr.ontimeout = function() { cb(new Error('Timeout')); };
      xhr.send();
    }
  }

  function attach(input) {
    if (input.__uxCepAttached) return;
    input.__uxCepAttached = true;
    // Adiciona classe visual quando o usuario sai
    input.addEventListener('blur', function() {
      var cep = onlyDigits(input.value);
      if (cep.length !== 8) return;
      // Indica carregando
      input.classList.add('ux-cep-loading');
      input.setAttribute('placeholder', 'Buscando CEP...');
      fetchCep(cep, function(err, data) {
        input.classList.remove('ux-cep-loading');
        input.setAttribute('placeholder', '');
        if (err) {
          if (window.notify) window.notify.warning('Não foi possível buscar o CEP. Preencha manualmente.');
          return;
        }
        if (data && data.erro) {
          if (window.notify) window.notify.error('CEP não encontrado.');
          return;
        }
        if (!data) return;
        setValue(findField('logradouro'), data.logradouro || '');
        setValue(findField('bairro'),     data.bairro     || '');
        setValue(findField('cidade'),     data.localidade || '');
        setValue(findField('uf'),         data.uf         || '');
        setValue(findField('ibge'),       data.ibge       || '');
        if (window.notify) window.notify.success('Endereço preenchido automaticamente.');
      });
    });
  }

  function init() {
    var inputs = document.querySelectorAll('input[name="cep"], input[id="cep"], [data-cep-field]');
    inputs.forEach(attach);
  }

  // Wire-up
  $(function() {
    init();
    // Re-aplica para inputs inseridos via AJAX
    setTimeout(init, 2000);
  });

  window.UX.cep = { init: init, attach: attach };
})(window, jQuery);
