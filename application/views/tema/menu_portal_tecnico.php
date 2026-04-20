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

    <a href="#" class="visible-phone">
        <div class="mode">
            <div class="moon-menu">
                <i class='bx bx-chevron-right iconX open-2'></i>
                <i class='bx bx-chevron-left iconX close-2'></i>
            </div>
        </div>
    </a>

    <div class="menu-bar">
        <div class="menu menu-scrollable">

            <ul class="menu-links" style="position: relative;">

                <!-- Dashboard -->
                <li class="<?php if (isset($menuDashboard)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('tecnicos/dashboard') ?>">
                        <i class='bx bx-home-alt iconX'></i>
                        <span class="title nav-title">Dashboard</span>
                        <span class="title-tooltip">Início</span>
                    </a>
                </li>

                <!-- Minhas OS -->
                <li class="<?php if (isset($menuMinhasOs)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('tecnicos/minhas_os') ?>">
                        <i class='bx bx-clipboard iconX'></i>
                        <span class="title">Minhas OS</span>
                        <span class="title-tooltip">Minhas OS</span>
                    </a>
                </li>

                <!-- Minhas Obras -->
                <li class="<?php if (isset($menuObras)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('tecnicos_admin/minhas_obras') ?>">
                        <i class='bx bx-building iconX'></i>
                        <span class="title">Minhas Obras</span>
                        <span class="title-tooltip">Minhas Obras</span>
                    </a>
                </li>

                <!-- Meu Estoque -->
                <li class="<?php if (isset($menuEstoque)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('tecnicos/meu_estoque') ?>">
                        <i class='bx bx-package iconX'></i>
                        <span class="title">Meu Estoque</span>
                        <span class="title-tooltip">Meu Estoque</span>
                    </a>
                </li>

                <!-- Meu Perfil -->
                <li class="<?php if (isset($menuPerfil)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('tecnicos/perfil') ?>">
                        <i class='bx bx-user iconX'></i>
                        <span class="title">Meu Perfil</span>
                        <span class="title-tooltip">Meu Perfil</span>
                    </a>
                </li>

                <!-- Sair -->
                <li>
                    <a class="tip-bottom" title="" href="<?= site_url('tecnicos/logout') ?>">
                        <i class='bx bx-log-out iconX'></i>
                        <span class="title">Sair</span>
                        <span class="title-tooltip">Sair</span>
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>
