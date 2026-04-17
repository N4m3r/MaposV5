<?php
/**
 * Menu exclusivo para técnicos
 * Este menu é carregado automaticamente quando o usuário tem permissão de técnico
 * Mantém a mesma aparência e tema do MapOS (menu principal)
 */
?>
<!--sidebar-menu tecnico-->
<nav id="sidebar">
    <div id="newlog">
        <div class="icon2">
            <img src="<?php echo base_url() ?>assets/img/logo-two.png">
        </div>
        <div class="title1">
            <?= $configuration['app_theme'] == 'white' ||  $configuration['app_theme'] == 'whitegreen' ? '<img src="' . base_url() . 'assets/img/logo-mapos.png">' : '<img src="' . base_url() . 'assets/img/logo-mapos-branco.png">'; ?>
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

    <!-- Start Pesquisar-->
    <li class="search-box">
        <form style="display: flex" action="<?= site_url('mapos/pesquisar') ?>">
            <button style="background:transparent;border:transparent" type="submit" class="tip-bottom" title="">
                <i class='bx bx-search iconX'></i></button>
            <input style="background:transparent;<?= $configuration['app_theme'] == 'white' ? 'color:#313030;' : 'color:#fff;' ?>border:transparent" type="search" name="termo" placeholder="Pesquise aqui...">
            <span class="title-tooltip">Pesquisar</span>
        </form>
    </li>
    <!-- End Pesquisar-->

    <div class="menu-bar">
        <div class="menu menu-scrollable">

            <ul class="menu-links" style="position: relative;">
                <!-- Dashboard Técnico -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicoDashboard')) { ?>
                <li class="<?php if (isset($menuTecnicoDashboard)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('tecnico') ?>">
                        <i class='bx bx-home-alt iconX'></i>
                        <span class="title nav-title">Home</span>
                        <span class="title-tooltip">Início</span>
                    </a>
                </li>
                <?php } ?>

                <!-- Minhas Ordens de Serviço -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicoOS')) { ?>
                <li class="<?php if (isset($menuMinhasOs) || isset($menuOs)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('tecnico/os') ?>">
                        <i class='bx bx-file iconX'></i>
                        <span class="title">Minhas OS</span>
                        <span class="title-tooltip">Minhas OS</span>
                    </a>
                </li>
                <?php } ?>

                <li class="menu-divider"><span class="divider-text">CADASTROS</span></li>

                <!-- Produtos (Visualização) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                <li class="<?php if (isset($menuProdutos)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('produtos') ?>">
                        <i class='bx bx-basket iconX'></i>
                        <span class="title">Produtos</span>
                        <span class="title-tooltip">Produtos</span>
                    </a>
                </li>
                <?php } ?>

                <!-- Serviços (Visualização) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
                <li class="<?php if (isset($menuServicos)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('servicos') ?>">
                        <i class='bx bx-wrench iconX'></i>
                        <span class="title">Serviços</span>
                        <span class="title-tooltip">Serviços</span>
                    </a>
                </li>
                <?php } ?>

                <!-- Clientes (Visualização) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                <li class="<?php if (isset($menuClientes)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('clientes') ?>">
                        <i class='bx bx-user iconX'></i>
                        <span class="title">Clientes</span>
                        <span class="title-tooltip">Clientes</span>
                    </a>
                </li>
                <?php } ?>

                <!-- Relatórios -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioTecnicos') || $this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos')) { ?>
                <li class="menu-divider"><span class="divider-text">RELATÓRIOS</span></li>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioTecnicos')) { ?>
                <li class="<?php if (isset($menuRelTecnicos)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('relatoriotecnicos') ?>">
                        <i class='bx bx-hard-hat iconX'></i>
                        <span class="title">Performance</span>
                        <span class="title-tooltip">Performance Técnicos</span>
                    </a>
                </li>
                <?php } ?>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos')) { ?>
                <li class="<?php if (isset($menuRelatorioAtendimentos)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= site_url('relatorioatendimentos') ?>">
                        <i class='bx bx-time iconX'></i>
                        <span class="title">Atendimentos</span>
                        <span class="title-tooltip">Rel. Atendimentos</span>
                    </a>
                </li>
                <?php } ?>
                <?php } ?>

            </ul>
        </div>

        <div class="botton-content">
            <li class="">
                <a class="tip-bottom" title="" href="<?= site_url('login/sair'); ?>">
                    <i class='bx bx-log-out-circle iconX'></i>
                    <span class="title">Sair</span>
                    <span class="title-tooltip">Sair</span>
                </a>
            </li>
        </div>
    </div>
</nav>
<!--End sidebar-menu-->