<!--sidebar-menu Portal do Técnico-->
<nav id="sidebar">
    <div id="newlog">
        <div class="icon2">
            <img src="<?php echo base_url() ?>assets/img/logo-two.png">
        </div>
        <div class="title1">
            <?php if (isset($configuration['app_theme']) && ($configuration['app_theme'] == 'white' || $configuration['app_theme'] == 'whitegreen')): ?>
                <img src="<?php echo base_url() ?>assets/img/logo-mapos.png">
            <?php else: ?>
                <img src="<?php echo base_url() ?>assets/img/logo-mapos-branco.png">
            <?php endif; ?>
        </div>
    </div>

    <!-- Botão toggle do sidebar -->
    <div class="mode" title="Recolher/Expandir Menu">
        <div class="moon-menu">
            <?= svg_icon('chevron-left', 18, 18, 'iconX open-2') ?>
            <?= svg_icon('chevron-right', 18, 18, 'iconX close-2') ?>
        </div>
    </div>

    <div class="menu-bar">
        <div class="menu menu-scrollable">

            <ul class="menu-links" style="position: relative;">

                <!-- Dashboard -->
                <li class="<?php if (isset($menuDashboard)) { echo 'active'; }; ?>">
                    <a class="tip-bottom btn" title="" href="<?= site_url('tecnicos/dashboard') ?>">
                        <?= svg_icon('home', 20, 20, 'iconX') ?>
                        <span class="title nav-title">Dashboard</span>
                        <span class="title-tooltip">Início</span>
                    </a>
                </li>

                <!-- Minhas OS -->
                <li class="<?php if (isset($menuMinhasOs)) { echo 'active'; }; ?>">
                    <a class="tip-bottom btn" title="" href="<?= site_url('tecnicos/minhas_os') ?>">
                        <?= svg_icon('clipboard', 20, 20, 'iconX') ?>
                        <span class="title">Minhas OS</span>
                        <span class="title-tooltip">Minhas OS</span>
                    </a>
                </li>

                <!-- Minhas Obras -->
                <li class="<?php if (isset($menuObras)) { echo 'active'; }; ?>">
                    <a class="tip-bottom btn" title="" href="<?= site_url('tecnicos/minhas_obras') ?>">
                        <?= svg_icon('building', 20, 20, 'iconX') ?>
                        <span class="title">Minhas Obras</span>
                        <span class="title-tooltip">Minhas Obras</span>
                    </a>
                </li>

                <!-- Meu Estoque -->
                <li class="<?php if (isset($menuEstoque)) { echo 'active'; }; ?>">
                    <a class="tip-bottom btn" title="" href="<?= site_url('tecnicos/meu_estoque') ?>">
                        <?= svg_icon('package', 20, 20, 'iconX') ?>
                        <span class="title">Meu Estoque</span>
                        <span class="title-tooltip">Meu Estoque</span>
                    </a>
                </li>

                <!-- Meu Perfil -->
                <li class="<?php if (isset($menuPerfil)) { echo 'active'; }; ?>">
                    <a class="tip-bottom btn" title="" href="<?= site_url('tecnicos/perfil') ?>">
                        <?= svg_icon('user', 20, 20, 'iconX') ?>
                        <span class="title">Meu Perfil</span>
                        <span class="title-tooltip">Meu Perfil</span>
                    </a>
                </li>

                <!-- Sair -->
                <li>
                    <a class="tip-bottom btn" title="" href="<?= site_url('tecnicos/logout') ?>">
                        <?= svg_icon('log-out', 20, 20, 'iconX') ?>
                        <span class="title">Sair</span>
                        <span class="title-tooltip">Sair</span>
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>
