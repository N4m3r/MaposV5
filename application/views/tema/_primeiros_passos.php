<?php
/**
 * View parcial: Checklist "Primeiros Passos"
 * Adicionado em 2026-06-14 (Fase 2.2 do Plano UX).
 *
 * Widget exibido no dashboard para usuarios que ainda nao configuraram
 * o sistema. Desaparece automaticamente quando todos os itens estao OK.
 *
 * Uso:
 *   $this->load->view('tema/_primeiros_passos');
 *
 * Os itens sao:
 *   1. Cadastrar dados da empresa (emitente)
 *   2. Adicionar primeiro cliente
 *   3. Adicionar primeiro produto/servico
 *   4. Criar primeira OS
 *   5. Fazer primeiro lancamento financeiro
 */
?>
<div class="ux-primeiros-passos" data-tour-primeiros-passos style="display:none;">
  <div class="upp-header">
    <i class='bx bx-rocket'></i>
    <h3>Primeiros Passos</h3>
    <button type="button" class="upp-close" id="upp-close" title="Fechar (lembrar depois)">
      <i class='bx bx-x'></i>
    </button>
  </div>
  <p class="upp-subtitle">Complete essas tarefas para começar a usar o sistema com tudo:</p>
  <ul class="upp-list" id="upp-list">
    <li class="upp-item" data-key="tem_emitente">
      <i class='bx bx-circle upp-check'></i>
      <span class="upp-label">Configurar dados da sua empresa</span>
      <a href="<?= site_url('mapos/emitente') ?>" class="upp-action">Fazer <i class='bx bx-chevron-right'></i></a>
    </li>
    <li class="upp-item" data-key="tem_cliente">
      <i class='bx bx-circle upp-check'></i>
      <span class="upp-label">Adicionar seu primeiro cliente</span>
      <a href="<?= site_url('clientes/adicionar') ?>" class="upp-action">Fazer <i class='bx bx-chevron-right'></i></a>
    </li>
    <li class="upp-item" data-key="tem_produto">
      <i class='bx bx-circle upp-check'></i>
      <span class="upp-label">Cadastrar produto ou serviço</span>
      <a href="<?= site_url('produtos/adicionar') ?>" class="upp-action">Fazer <i class='bx bx-chevron-right'></i></a>
    </li>
    <li class="upp-item" data-key="tem_os">
      <i class='bx bx-circle upp-check'></i>
      <span class="upp-label">Criar sua primeira OS</span>
      <a href="<?= site_url('os/adicionar') ?>" class="upp-action">Fazer <i class='bx bx-chevron-right'></i></a>
    </li>
    <li class="upp-item" data-key="tem_lancamento">
      <i class='bx bx-circle upp-check'></i>
      <span class="upp-label">Registrar um lançamento financeiro</span>
      <a href="<?= site_url('financeiro/lancamentos/adicionar') ?>" class="upp-action">Fazer <i class='bx bx-chevron-right'></i></a>
    </li>
  </ul>
  <div class="upp-progress">
    <div class="upp-progress-bar"><span id="upp-progress-fill"></span></div>
    <span id="upp-progress-text">0/5</span>
  </div>
</div>

<script>
(function() {
  'use strict';
  // Usuario pode fechar o widget (nao aparece mais nesta sessao)
  var closed = false;
  try { closed = sessionStorage.getItem('upp-closed') === '1'; } catch (e) {}

  var root = document.querySelector('.ux-primeiros-passos');
  if (!root) return;
  if (closed) { root.style.display = 'none'; return; }

  document.getElementById('upp-close').addEventListener('click', function() {
    root.style.display = 'none';
    try { sessionStorage.setItem('upp-closed', '1'); } catch (e) {}
  });

  function render(checklist) {
    var items = root.querySelectorAll('.upp-item');
    var done = 0, total = items.length;
    items.forEach(function(li) {
      var key = li.getAttribute('data-key');
      var ok = !!checklist[key];
      if (ok) {
        li.classList.add('done');
        var ic = li.querySelector('.upp-check');
        if (ic) ic.className = 'bx bxs-check-circle upp-check';
        var a = li.querySelector('.upp-action');
        if (a) a.style.display = 'none';
        done++;
      }
    });
    var pct = total > 0 ? Math.round((done / total) * 100) : 0;
    var fill = document.getElementById('upp-progress-fill');
    var text = document.getElementById('upp-progress-text');
    if (fill) fill.style.width = pct + '%';
    if (text) text.textContent = done + '/' + total;

    // Esconde se 100%
    if (done === total) {
      setTimeout(function() { root.style.display = 'none'; }, 800);
    } else {
      root.style.display = '';
    }
  }

  // Busca o estado via endpoint
  var base = (window.BaseUrl || '/') + 'index.php/busca/primeirosPassos';
  if (window.fetch) {
    fetch(base, { credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(resp) {
        if (resp && resp.success && resp.data && resp.data.checklist) {
          render(resp.data.checklist);
        } else {
          // Resposta inesperada: assume nao completo e mostra
          root.style.display = '';
        }
      })
      .catch(function() { root.style.display = ''; });
  } else {
    root.style.display = '';
  }
})();
</script>
