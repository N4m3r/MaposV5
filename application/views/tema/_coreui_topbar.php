<?php
/**
 * Topbar CoreUI para views PHP legadas.
 * Reutilizavel em todas as views PHP via shell_start.php.
 * Inclui: hamburger mobile, brand, busca global, theme switcher, notificacoes, user.
 */
$perm = $this->session->userdata('permissao');
$userName = $this->session->userdata('nome') ?? 'Usuario';
$roles = [1 => 'Administrador', 2 => 'Gerente'];
$userRole = $roles[$perm] ?? 'Usuario';
$appName = $configuration['app_name'] ?? 'Map-OS';
$router = &load_class('Router', 'core');
$currentClass = strtolower($router->fetch_class());
$appTheme = $configuration['app_theme'] ?? 'white';
?>
<header class="app-topbar" role="banner">
    <button type="button" class="app-icon-btn d-md-none" id="btn-toggle-sidebar-mobile" aria-label="Abrir menu">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <div class="app-topbar-brand">
        <strong><?= e($appName) ?></strong>
        <span class="text-muted ms-2 d-none d-md-inline" style="font-size:0.85rem;opacity:0.7;">
            / <?= e(ucfirst($currentClass)) ?>
        </span>
    </div>

    <form class="app-topbar-search ms-auto" action="<?= site_url('mapos/pesquisar') ?>" method="get" role="search">
        <div class="input-group input-group-sm">
            <span class="input-group-text" style="background:transparent;border-right:none;">
                <i class="bi bi-search" aria-hidden="true"></i>
            </span>
            <input
                type="search"
                name="termo"
                class="form-control"
                placeholder="Buscar (Ctrl+K)"
                aria-label="Busca global"
                style="border-left:none;"
            >
        </div>
    </form>

    <div class="app-topbar-actions">
        <!-- Theme switcher (sincronizado com mapos.css data-theme) -->
        <div class="dropdown">
            <button type="button" class="app-icon-btn" data-bs-toggle="dropdown" aria-label="Trocar tema" aria-expanded="false">
                <i class="bi bi-circle-half" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">Tema</h6></li>
                <?php foreach ([
                    'white'      => ['Claro',          'sun'],
                    'puredark'   => ['Claro Escuro',   'moon-stars'],
                    'whitegreen' => ['Claro Verde',    'tree'],
                    'whiteblack' => ['Claro Preto',    'circle-half'],
                    'darkviolet' => ['Escuro Violeta', 'star'],
                    'darkorange' => ['Escuro Laranja', 'fire'],
                ] as $id => $info): ?>
                <li>
                    <a class="dropdown-item <?= $appTheme === $id ? 'active' : '' ?>"
                       href="#"
                       data-theme-pick="<?= e($id) ?>"
                       data-theme-icon="<?= e($info[1]) ?>">
                        <i class="bi bi-<?= e($info[1]) ?> me-2" aria-hidden="true"></i><?= e($info[0]) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Notificacoes (compartilhado com rodape.php) -->
        <div class="dropdown">
            <button type="button" class="app-icon-btn position-relative" data-bs-toggle="dropdown" aria-label="Notificacoes" aria-expanded="false">
                <i class="bi bi-bell" aria-hidden="true"></i>
                <span class="badge bg-danger notif-badge-count" id="notif-count" style="display:none;">0</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0">
                <div class="notif-header">
                    <strong>Notificacoes</strong>
                    <a href="#" id="notif-marcar-todas" class="small">Marcar todas</a>
                </div>
                <div class="notif-list" id="notif-items">
                    <div class="text-center text-muted p-3 small">Carregando...</div>
                </div>
            </div>
        </div>

        <!-- User dropdown -->
        <div class="dropdown">
            <button type="button" class="app-topbar-user" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="topbar-user-avatar" style="width:32px;height:32px;border-radius:50%;background:var(--color-accent,#3c4b64);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:13px;">
                    <?= strtoupper(mb_substr($userName, 0, 2)) ?>
                </div>
                <div class="d-none d-md-block" style="line-height:1.15;">
                    <div style="font-size:0.875rem;font-weight:500;"><?= e($userName) ?></div>
                    <div style="font-size:0.7rem;color:var(--color-text-muted);"><?= e($userRole) ?></div>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= site_url('mapos/minhaConta') ?>"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                <li><a class="dropdown-item" href="<?= site_url() ?>/mine" target="_blank"><i class="bi bi-globe me-2"></i>Area do Cliente</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= site_url('login/sair') ?>"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
            </ul>
        </div>
    </div>
</header>
