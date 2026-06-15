<?php
/**
 * View: /ajuda/tela/{slug}
 * Mostra o conteudo de ajuda de uma tela especifica.
 * Se o slug nao existir, mostra mensagem amigavel.
 */
$telaExiste = !empty($tela);
?>
<style>
.ux-help-tela { max-width: 820px; margin: 0 auto; }
.ux-help-tela .ht-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 22px 26px; border-radius: 12px; margin-bottom: 22px; }
.ux-help-tela .ht-header h1 { margin: 0 0 6px; font-size: 1.5rem; display: flex; align-items: center; gap: 8px; }
.ux-help-tela .ht-header p { margin: 0; opacity: 0.9; font-size: 0.95rem; }
.ux-help-tela .ht-actions { margin-top: 14px; }
.ux-help-tela .ht-actions a { color: #fff; background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px; }
.ux-help-tela .ht-actions a:hover { background: rgba(255,255,255,0.3); color: #fff; text-decoration: none; }
.ux-help-tela .ht-section { background: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 16px 20px; margin-bottom: 14px; }
.ux-help-tela .ht-section h2 { font-size: 1.05rem; font-weight: 600; color: #2c3e50; margin: 0 0 10px; }
.ux-help-tela .ht-section p, .ux-help-tela .ht-section li { color: #495057; line-height: 1.55; font-size: 0.9rem; }
.ux-help-tela .ht-section ul, .ux-help-tela .ht-section ol { padding-left: 20px; }
.ux-help-tela .ht-back { display: inline-flex; align-items: center; gap: 4px; color: #6c757d; font-size: 0.85rem; text-decoration: none; margin-bottom: 12px; }
.ux-help-tela .ht-back:hover { color: #0d6efd; text-decoration: none; }
.ux-help-tela .ht-notfound { text-align: center; padding: 60px 20px; color: #6c757d; }
.ux-help-tela .ht-notfound i { font-size: 4rem; opacity: 0.4; }
.ux-help-tela .ht-notfound h2 { font-size: 1.3rem; margin: 12px 0 6px; }
[data-theme="puredark"] .ux-help-tela .ht-section { background: #1f1f1f; border-color: #333; }
[data-theme="puredark"] .ux-help-tela .ht-section h2 { color: #eaeaea; }
[data-theme="puredark"] .ux-help-tela .ht-section p,
[data-theme="puredark"] .ux-help-tela .ht-section li { color: #ccc; }
</style>

<div class="ux-help-tela">
  <a href="<?= site_url('ajuda') ?>" class="ht-back"><i class='bx bx-arrow-back'></i> Voltar para a central</a>

  <?php if (!$telaExiste): ?>
    <div class="ht-notfound">
      <i class='bx bx-help-circle'></i>
      <h2>Pagina de ajuda nao encontrada</h2>
      <p>O topico <code><?= e($tela_atual) ?></code> nao existe ou foi removido.</p>
      <a href="<?= site_url('ajuda') ?>" class="btn btn-primary mt-3"><i class='bx bx-home'></i> Ir para a central de ajuda</a>
    </div>
  <?php else: ?>
    <div class="ht-header">
      <h1><i class='bx <?= e($tela['icone'] ?? 'bx-help-circle') ?>'></i> <?= e($tela['titulo']) ?></h1>
      <p><?= e($tela['resumo']) ?></p>
      <?php if (!empty($tela['url'])): ?>
        <div class="ht-actions">
          <a href="<?= site_url($tela['url']) ?>" target="_blank" rel="noopener">
            <i class='bx bx-link-external'></i> Ir para a tela
          </a>
        </div>
      <?php endif; ?>
    </div>

    <?php foreach (($tela['secoes'] ?? []) as $secao): ?>
      <div class="ht-section">
        <h2><?= e($secao['titulo']) ?></h2>
        <div class="ht-body"><?= $secao['conteudo'] /* HTML ja preparado */ ?></div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
