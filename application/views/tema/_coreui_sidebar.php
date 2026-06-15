<?php
/**
 * Sidebar CoreUI (modo escuro, 256px).
 * Reutilizavel em todas as views PHP legadas via shell_start.php.
 *
 * Itens sao filtrados por permissao do usuario logado.
 * Esta sidebar espelha a versao React (AppSidebarNav) e o antigo menu.php,
 * mas usa classes CoreUI/utility em vez de `widget-box` Maruti.
 */
$perm = $this->session->userdata('permissao');
$permissoes = $this->session->userdata('permissoes');
$router = &load_class('Router', 'core');
$currentClass = strtolower($router->fetch_class());
$currentMethod = strtolower($router->fetch_method());
$appName = $configuration['app_name'] ?? 'Map-OS';
$userName = $this->session->userdata('nome') ?? 'Usuario';

// Helper para verificar permissao (compatibilidade com biblioteca Permission)
$hasPerm = function($key) use ($permissoes) {
    if (is_array($permissoes)) {
        foreach ($permissoes as $k => $v) {
            if (strtolower($k) === strtolower($key) && ($v === true || $v === 1 || $v === '1')) {
                return true;
            }
        }
    }
    return false;
};
$isAdmin = $hasPerm('cPermissao');

$menuItems = [
    ['to' => 'dashboard',          'label' => 'Dashboard',          'icon' => 'speedometer2',     'perm' => 'vDashboard',  'group' => 'principal'],
    ['to' => 'os',                 'label' => 'Ordens de Servico',  'icon' => 'clipboard-data',   'perm' => 'vOs',         'group' => 'os'],
    ['to' => 'kanban',             'label' => 'Kanban',             'icon' => 'kanban',           'perm' => 'vKanban',     'group' => 'os'],
    ['to' => 'garantias',          'label' => 'Garantias',          'icon' => 'shield-check',     'perm' => 'vGarantia',   'group' => 'os'],
    ['to' => 'clientes',           'label' => 'Clientes',           'icon' => 'people',           'perm' => 'vCliente',    'group' => 'cadastros'],
    ['to' => 'produtos',           'label' => 'Produtos',           'icon' => 'box-seam',         'perm' => 'vProduto',    'group' => 'cadastros'],
    ['to' => 'servicos',           'label' => 'Servicos',           'icon' => 'tools',            'perm' => 'vServico',    'group' => 'cadastros'],
    ['to' => 'vendas',             'label' => 'Vendas',             'icon' => 'cart4',            'perm' => 'vVenda',      'group' => 'cadastros'],
    ['to' => 'financeiro/lancamentos', 'label' => 'Lancamentos',    'icon' => 'cash-stack',       'perm' => 'vLancamento', 'group' => 'financeiro'],
    ['to' => 'cobrancas/cobrancas','label' => 'Cobrancas',          'icon' => 'credit-card',      'perm' => 'vCobranca',   'group' => 'financeiro'],
    ['to' => 'nfse_os',            'label' => 'NFS-e',              'icon' => 'receipt',          'perm' => 'vNFSe',       'group' => 'fiscal'],
    ['to' => 'obras',              'label' => 'Obras',              'icon' => 'building',         'perm' => 'vObra',       'group' => 'projetos'],
    ['to' => 'arquivos',           'label' => 'Arquivos',           'icon' => 'folder2-open',     'perm' => 'vArquivo',    'group' => 'projetos'],
    ['to' => 'relatorioatendimentos','label' => 'Relatorios',       'icon' => 'pie-chart',        'perm' => 'vRelatorioAtendimentos', 'group' => 'relatorios'],
    ['to' => 'usuarios',           'label' => 'Usuarios',           'icon' => 'person',           'perm' => 'cUsuario',    'group' => 'config', 'adminOnly' => true],
    ['to' => 'permissoes',         'label' => 'Permissoes',         'icon' => 'shield-lock',      'perm' => 'cPermissao',  'group' => 'config', 'adminOnly' => true],
    ['to' => 'mapos',              'label' => 'Configuracoes',      'icon' => 'gear',             'perm' => 'cSistema',    'group' => 'config', 'adminOnly' => true],
    ['to' => 'modulos',            'label' => 'Modulos',            'icon' => 'puzzle',           'perm' => 'cPermissao',  'group' => 'config', 'adminOnly' => true],
    ['to' => 'agente_ia',          'label' => 'Agente IA',          'icon' => 'robot',            'perm' => 'cPermissao',  'group' => 'config', 'adminOnly' => true],
];

// Agrupa itens visiveis por grupo
$grouped = [];
foreach ($menuItems as $item) {
    if (!empty($item['adminOnly']) && ! $isAdmin) continue;
    if (!empty($item['perm']) && ! $hasPerm($item['perm'])) continue;
    $grouped[$item['group']][] = $item;
}

$groupLabels = [
    'principal'  => 'Principal',
    'os'         => 'Ordens de Servico',
    'cadastros'  => 'Cadastros',
    'financeiro' => 'Financeiro',
    'fiscal'     => 'Documentos Fiscais',
    'projetos'   => 'Projetos',
    'relatorios' => 'Relatorios',
    'config'     => 'Configuracoes',
];
?>
<aside class="app-sidebar" id="app-sidebar" aria-label="Navegacao principal">
    <a href="<?= base_url() ?>" class="app-sidebar-brand">
        <i class="bi bi-hexagon-fill" style="color:var(--color-accent,#3c4b64);"></i>
        <span><?= e($appName) ?></span>
    </a>

    <nav class="app-sidebar-nav" aria-label="Menu">
        <?php foreach ($grouped as $groupKey => $items): ?>
            <?php if (count($grouped) > 1): ?>
            <div class="app-sidebar-section"><?= e($groupLabels[$groupKey] ?? ucfirst($groupKey)) ?></div>
            <?php endif; ?>
            <?php foreach ($items as $item):
                $href = site_url($item['to']);
                $active = ($currentClass === strtolower(strtok($item['to'], '/'))) ? 'active' : '';
            ?>
            <a href="<?= $href ?>" class="<?= $active ?>" title="<?= e($item['label']) ?>" aria-current="<?= $active ? 'page' : 'false' ?>">
                <i class="bi bi-<?= e($item['icon']) ?>" aria-hidden="true"></i>
                <span><?= e($item['label']) ?></span>
            </a>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <div class="app-sidebar-section">Sessao</div>
        <a href="<?= site_url('login/sair') ?>" title="Sair" style="color:#e88;">
            <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
            <span>Sair</span>
        </a>
    </nav>
</aside>
