<?php
/**
 * View: /ajuda
 * Hub central de ajuda - agrupa por categoria
 */
$categorias = [
    'geral'         => ['nome' => 'Geral',           'icone' => 'bx-world'],
    'cadastros'     => ['nome' => 'Cadastros',       'icone' => 'bx-user-pin'],
    'os'            => ['nome' => 'Ordens de Servico','icone' => 'bx-file'],
    'financeiro'    => ['nome' => 'Financeiro',      'icone' => 'bx-wallet'],
    'configuracoes' => ['nome' => 'Configuracoes',   'icone' => 'bx-cog'],
];
?>
<style>
.ux-help-hub { max-width: 980px; margin: 0 auto; }
.ux-help-intro { background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); border: 1px solid #667eea30; border-radius: 12px; padding: 18px 22px; margin-bottom: 24px; }
.ux-help-intro h1 { margin: 0 0 4px; font-size: 1.4rem; color: #2c3e50; }
.ux-help-intro p { margin: 0; color: #6c757d; font-size: 0.95rem; }
.ux-help-category { margin-bottom: 28px; }
.ux-help-category h2 { font-size: 1.05rem; font-weight: 600; color: #495057; margin: 0 0 10px; display: flex; align-items: center; gap: 6px; }
.ux-help-category h2 i { color: #667eea; }
.ux-help-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
.ux-help-card {
  display: block; padding: 14px 16px; border: 1px solid #e9ecef; border-radius: 8px;
  text-decoration: none; color: inherit; background: #fff; transition: all 0.15s ease;
}
.ux-help-card:hover { border-color: #0d6efd; box-shadow: 0 2px 8px rgba(13,110,253,0.12); transform: translateY(-2px); color: inherit; text-decoration: none; }
.ux-help-card .hc-icon { font-size: 1.6rem; color: #667eea; margin-bottom: 6px; }
.ux-help-card .hc-title { font-weight: 600; font-size: 0.95rem; margin-bottom: 4px; color: #212529; }
.ux-help-card .hc-resumo { font-size: 0.82rem; color: #6c757d; line-height: 1.4; }
[data-theme="puredark"] .ux-help-card { background: #1f1f1f; border-color: #333; }
[data-theme="puredark"] .ux-help-card .hc-title { color: #eaeaea; }
[data-theme="puredark"] .ux-help-card .hc-resumo { color: #999; }
</style>

<div class="ux-help-hub">
  <div class="ux-help-intro">
    <h1><i class='bx bx-help-circle'></i> Central de Ajuda</h1>
    <p>Encontre guias passo a passo para usar cada tela do sistema. Selecione abaixo ou use a busca global (<kbd>Ctrl+K</kbd>) e digite o nome do que procura.</p>
  </div>

  <?php foreach ($grupos as $catSlug => $itens): ?>
    <?php $cat = $categorias[$catSlug] ?? ['nome' => ucfirst($catSlug), 'icone' => 'bx-folder']; ?>
    <div class="ux-help-category">
      <h2><i class='bx <?= e($cat['icone']) ?>'></i> <?= e($cat['nome']) ?></h2>
      <div class="ux-help-grid">
        <?php foreach ($itens as $slug => $tela): ?>
          <a href="<?= site_url('ajuda/tela/' . $slug) ?>" class="ux-help-card">
            <div class="hc-icon"><i class='bx <?= e($tela['icone'] ?? 'bx-help-circle') ?>'></i></div>
            <div class="hc-title"><?= e($tela['titulo']) ?></div>
            <div class="hc-resumo"><?= e($tela['resumo']) ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
