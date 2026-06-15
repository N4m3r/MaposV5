/**
 * Mobile Camera Capture (F5.4)
 *
 * - Detecta inputs com [data-ux-camera] ou input[type=file][accept^="image/"]
 * - Em dispositivos moveis, force capture=environment (camera traseira)
 * - Preview do arquivo selecionado em container [data-ux-camera-preview]
 * - Compressao client-side: max 1280px, JPEG 0.82
 *
 * Nao faz upload. Apenas prepara o arquivo para envio.
 */
(function(window, $) {
  'use strict';
  if (!window.UX) window.UX = {};

  var MAX_DIM = 1280;
  var QUALITY = 0.82;

  function isMobile() {
    return /Mobi|Android|iPhone|iPad/i.test(navigator.userAgent);
  }

  function compress(file) {
    return new Promise(function(resolve, reject) {
      var img = new Image();
      var url = URL.createObjectURL(file);
      img.onload = function() {
        URL.revokeObjectURL(url);
        var w = img.naturalWidth, h = img.naturalHeight;
        if (w > MAX_DIM || h > MAX_DIM) {
          if (w > h) { h = Math.round(h * (MAX_DIM / w)); w = MAX_DIM; }
          else       { w = Math.round(w * (MAX_DIM / h)); h = MAX_DIM; }
        }
        var canvas = document.createElement('canvas');
        canvas.width = w; canvas.height = h;
        var ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, w, h);
        try {
          canvas.toBlob(function(blob) {
            if (!blob) { resolve(file); return; }
            blob.name = (file.name || 'photo').replace(/\.[^.]+$/, '') + '.jpg';
            resolve(blob);
          }, 'image/jpeg', QUALITY);
        } catch (e) {
          // canvas tainted
          resolve(file);
        }
      };
      img.onerror = function() { URL.revokeObjectURL(url); resolve(file); };
      img.src = url;
    });
  }

  function attach(input) {
    if (input.__uxCamera) return;
    input.__uxCamera = true;

    // Garante accept de imagem
    if (!/image\//.test(input.getAttribute('accept') || '')) {
      input.setAttribute('accept', 'image/*');
    }
    // Em mobile, força camera traseira
    if (isMobile() && !input.hasAttribute('capture')) {
      input.setAttribute('capture', 'environment');
    }

    // Procura preview container
    var name = input.getAttribute('name') || input.id;
    var $preview = name ? $('[data-ux-camera-preview="' + name + '"]') : $();
    if (!$preview.length) {
      // cria preview inline
      $preview = $('<div class="ux-camera-preview" data-ux-camera-preview="' + name + '"></div>');
      $(input).after($preview);
    }

    $(input).on('change', function() {
      var file = input.files && input.files[0];
      if (!file) return;
      $preview.html('<div class="ux-camera-loading">Processando imagem...</div>');
      compress(file).then(function(blob) {
        var url = URL.createObjectURL(blob);
        $preview.html(
          '<div class="ux-camera-thumb">'
          + '<img src="' + url + '" alt="Foto capturada">'
          + '<button type="button" class="ux-camera-remove" aria-label="Remover foto">&times;</button>'
          + '<div class="ux-camera-info">' + (blob.size / 1024).toFixed(0) + ' KB</div>'
          + '</div>'
        );
        $preview.find('.ux-camera-remove').on('click', function() {
          input.value = '';
          URL.revokeObjectURL(url);
          $preview.html('');
        });
        // Substitui o arquivo original pelo comprimido
        try {
          var dt = new DataTransfer();
          dt.items.add(blob);
          input.files = dt.files;
        } catch (e) {
          // navegadores antigos nao suportam
        }
      });
    });
  }

  function init() {
    document.querySelectorAll('[data-ux-camera]').forEach(attach);
    document.querySelectorAll('input[type="file"][accept*="image"]').forEach(attach);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.UX.camera = { init: init, compress: compress };
})(window, jQuery);
