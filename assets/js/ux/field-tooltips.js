/**
 * Field Tooltips (Tippy.js)
 * Fase 2.5 do Plano UX
 *
 * Ativa tooltips contextuais em:
 *   1) Qualquer elemento com atributo data-tippy-content="texto"
 *   2) Inputs/selects com name conhecido (margem, aliquota, NCM, CFOP, etc.)
 *
 * Configuracao:
 *   - Touch devices: tooltips aparecem ao tocar (com delay 200ms)
 *   - Keyboard: Tab no campo mostra tooltip
 *   - Dark mode: usa estilo customizado
 */
(function(window, $) {
  'use strict';

  var BaseUrl = (typeof window.BaseUrl === 'string') ? window.BaseUrl : '/';

  // Mapa de campos tecnicos com tooltip padrao.
  // O backend (helper glossary()) pode sobrescrever com data-tippy-content.
  var FIELD_HINTS = {
    'margem':         'Percentual de lucro sobre o custo. Ex: 100 = dobrar o preco.',
    'aliquota':       'Percentual de imposto incidente (ICMS, ISS, etc).',
    'aliquota_icms':  'Imposto sobre circulacao de mercadorias. Varia por estado.',
    'aliquota_iss':   'Imposto sobre servicos. Usado em NFS-e. Varia por cidade.',
    'ncm':            'Nomenclatura Comum do Mercosul. Codigo de 8 digitos para classificar produtos.',
    'cfop':           'Codigo Fiscal de Operacoes. Define a natureza da operacao (venda, devolucao, etc).',
    'cst':            'Codigo de Situacao Tributaria. Detalha a tributacao do ICMS/PIS/Cofins.',
    'codigo_servico': 'Codigo do servico municipal (LC 116). Usado na NFS-e.',
    'numero_nfse':    'Numero sequencial da NFS-e emitida pela prefeitura.',
    'codigo_barras':  'Codigo de barras do boleto. Gerado automaticamente pelo banco.',
    'pix_key':        'Chave PIX para recebimento. Pode ser CPF/CNPJ, email, celular ou chave aleatoria.',
    'vencimento':     'Data limite para pagamento. Apos esta data incidem juros/multa.',
    'dias_vencimento':'Quantidade de dias ate o vencimento. Ex: 7 = vence em 7 dias.',
    'desconto':       'Valor ou percentual descontado do preco original.',
    'comissao':       'Percentual pago ao vendedor sobre a venda.',
    'custo':          'Valor de compra do produto. Base para calculo de lucro.',
    'estoque_minimo': 'Quantidade minima em estoque para disparar alerta de compra.',
    'unidade':        'Unidade de medida (UN, KG, MT, LT, etc).',
    'origem':         'Origem da mercadoria: 0=Nacional, 1=Estrangeira importacao direta, etc.'
  };

  function ensureTippy(cb) {
    if (window.tippy) return cb();
    var tries = 0;
    var t = setInterval(function() {
      tries++;
      if (window.tippy) {
        clearInterval(t);
        return cb();
      }
      if (tries > 50) {
        clearInterval(t);
        console.warn('[UX Tooltip] Tippy.js nao carregou apos 5s.');
        cb();
      }
    }, 100);
  }

  function activate() {
    if (!window.tippy) return;

    // 1) Elementos explicitos: data-tippy-content="..."
    window.tippy('[data-tippy-content]', {
      allowHTML: true,
      maxWidth: 320,
      delay: [200, 0],
      duration: [200, 150],
      placement: 'top',
      arrow: true,
      theme: 'ux-glossary'
    });

    // 2) Mapeamento automatico de campos tecnicos
    Object.keys(FIELD_HINTS).forEach(function(name) {
      // Inputs/selects com name="xxx" ou id="xxx"
      var sel = '[name="' + name + '"],[id="' + name + '"]';
      try {
        var nodes = document.querySelectorAll(sel);
        nodes.forEach(function(node) {
          // Se ja tem data-tippy-content explicito, respeita
          if (node.getAttribute('data-tippy-content')) return;
          // Adiciona atributo para o seletor do Tippy pegar
          node.setAttribute('data-tippy-content', FIELD_HINTS[name]);
          // Ativa neste node especifico
          window.tippy(node, {
            allowHTML: false,
            maxWidth: 320,
            delay: [200, 0],
            duration: [200, 150],
            placement: 'top',
            arrow: true,
            theme: 'ux-glossary'
          });
        });
      } catch (e) { /* seletor invalido - ignora */ }
    });
  }

  // API publica
  window.UX.tooltip = {
    /** Ativa os tooltips manualmente (apos carregar Tippy + DOM) */
    activate: activate,
    /** Adiciona um mapeamento de hint customizado em runtime */
    addHint: function(name, text) { FIELD_HINTS[name] = text; }
  };

  // Auto-init
  $(function() {
    ensureTippy(function() {
      // 1ª passada
      activate();
      // Reaplica apos 2s (caso o DOM tenha mudado - ex: AJAX insert)
      setTimeout(activate, 2000);
    });
  });

})(window, jQuery);
