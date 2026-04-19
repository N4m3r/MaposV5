<!--sidebar-menu-->
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
                <!-- DASHBOARD -->
                <li class="menu-divider"><span class="divider-text">PRINCIPAL</span></li>

                <li class="<?php if (isset($menuPainel)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= base_url() ?>"><i class='bx bx-home-alt iconX'></i>
                        <span class="title nav-title">Início</span>
                        <span class="title-tooltip">Início</span>
                    </a>
                </li>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDashboard') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="<?php if (isset($menuDashboard)) { echo 'active'; }; ?>">
                        <a class="tip-bottom" title="" href="<?= site_url('dashboard') ?>"><i class='bx bx-dashboard iconX'></i>
                            <span class="title">Dashboard</span>
                            <span class="title-tooltip">Dashboard</span>
                        </a>
                    </li>
                <?php } ?>

                <!-- ORDENS DE SERVIÇO -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs') || $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
                    <li class="menu-divider"><span class="divider-text">ORDENS DE SERVIÇO</span></li>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                        <li class="<?php if (isset($menuOs)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('os') ?>"><i class='bx bx-file iconX'></i><span class="title">Todas as OS</span><span class="title-tooltip">Listar OS</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                        <li class="<?php if (isset($menuKanban)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('kanban') ?>"><i class='bx bx-columns iconX'></i><span class="title">Kanban Board</span><span class="title-tooltip">Kanban</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
                        <li class="<?php if (isset($menuAtribuir)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('os/atribuir') ?>"><i class='bx bx-user-plus iconX'></i><span class="title">Atribuir Técnico</span><span class="title-tooltip">Atribuir</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vGarantia')) { ?>
                        <li class="<?php if (isset($menuGarantia)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('garantias') ?>"><i class='bx bx-receipt iconX'></i><span class="title">Garantias</span><span class="title-tooltip">Garantias</span></a></li>
                    <?php } ?>
                <?php } ?>

                <!-- ÁREA DO TÉCNICO - Destaque especial -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicoDashboard')) { ?>
                    <li class="menu-divider"><span class="divider-text">ÁREA DO TÉCNICO</span></li>
                    <li class="<?php if (isset($menuTecnicoDashboard)) { echo 'active'; }; ?>">
                        <a href="<?= site_url('tecnico') ?>" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; margin: 5px 10px;">
                            <i class='bx bx-hard-hat iconX' style="color: white;"></i>
                            <span class="title" style="color: white; font-weight: 600;">Acessar Portal</span>
                            <span class="title-tooltip">Portal Técnico</span>
                        </a>
                    </li>
                <?php } ?>

                <!-- CADASTROS -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vServico') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) { ?>
                    <li class="menu-divider"><span class="divider-text">CADASTROS</span></li>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                        <li class="<?php if (isset($menuClientes)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('clientes') ?>"><i class='bx bx-user iconX'></i><span class="title">Clientes</span><span class="title-tooltip">Clientes</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                        <li class="<?php if (isset($menuProdutos)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('produtos') ?>"><i class='bx bx-basket iconX'></i><span class="title">Produtos</span><span class="title-tooltip">Produtos</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
                        <li class="<?php if (isset($menuServicos)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('servicos') ?>"><i class='bx bx-wrench iconX'></i><span class="title">Serviços</span><span class="title-tooltip">Serviços</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
                        <li class="<?php if (isset($menuVendas)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('vendas') ?>"><i class='bx bx-cart-alt iconX'></i><span class="title">Vendas</span><span class="title-tooltip">Vendas</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) { ?>
                        <li class="<?php if (isset($menuTecnicosAdmin)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('tecnicos_admin') ?>"><i class='bx bx-hard-hat iconX'></i><span class="title">Técnicos</span><span class="title-tooltip">Técnicos</span></a></li>
                    <?php } ?>
                <?php } ?>

                <!-- FINANCEIRO -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
                    <li class="menu-divider"><span class="divider-text">FINANCEIRO</span></li>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                        <li class="<?php if (isset($menuLancamentos)) { echo 'active'; }; ?>"><a href="<?= site_url('financeiro/lancamentos') ?>"><i class='bx bx-bar-chart-alt-2 iconX'></i><span class="title">Lançamentos</span><span class="title-tooltip">Lançamentos</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
                        <li class="<?php if (isset($menuCobrancas)) { echo 'active'; }; ?>"><a href="<?= site_url('cobrancas/cobrancas') ?>"><i class='bx bx-credit-card iconX'></i><span class="title">Cobranças</span><span class="title-tooltip">Cobranças</span></a></li>
                    <?php } ?>
                <?php } ?>

                <!-- DOCUMENTOS FISCAIS (NFSe, Impostos, Certificados) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
                    <li class="menu-divider"><span class="divider-text">DOCUMENTOS FISCAIS</span></li>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) { ?>
                        <li class="<?php if (isset($menuNfseOsDashboard)) { echo 'active'; }; ?>"><a href="<?= site_url('nfse_os') ?>"><i class='bx bx-receipt iconX'></i><span class="title">NFSe Dashboard</span><span class="title-tooltip">NFSe</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) { ?>
                        <li class="<?php if (isset($menuNfseOsRelatorio)) { echo 'active'; }; ?>"><a href="<?= site_url('nfse_os/relatorio') ?>"><i class='bx bx-chart iconX'></i><span class="title">Relatório NFSe/Boletos</span><span class="title-tooltip">Relatório</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
                        <li class="<?php if (isset($menuCertificadoDashboard)) { echo 'active'; }; ?>"><a href="<?= site_url('certificado') ?>"><i class='bx bx-check-shield iconX'></i><span class="title">Certificados</span><span class="title-tooltip">Certificados</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
                        <li class="<?php if (isset($menuNfseListar)) { echo 'active'; }; ?>"><a href="<?= site_url('nfse') ?>"><i class='bx bx-import iconX'></i><span class="title">NFS-e Importadas</span><span class="title-tooltip">NFS-e</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
                        <li class="<?php if (isset($menuImpostosDashboard)) { echo 'active'; }; ?>"><a href="<?= site_url('impostos') ?>"><i class='bx bx-calculator iconX'></i><span class="title">Impostos</span><span class="title-tooltip">Impostos</span></a></li>
                    <?php } ?>
                <?php } ?>

                <!-- RELATÓRIOS - Todos em um só lugar -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
                    <li class="menu-divider"><span class="divider-text">RELATÓRIOS</span></li>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos')) { ?>
                        <li class="<?php if (isset($menuRelatorioAtendimentos)) { echo 'active'; }; ?>"><a href="<?= site_url('relatorioatendimentos') ?>"><i class='bx bx-time iconX'></i><span class="title">Atendimentos</span><span class="title-tooltip">Atendimentos</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
                        <li class="<?php if (isset($menuRelTecnicos)) { echo 'active'; }; ?>"><a href="<?= site_url('relatoriotecnicos') ?>"><i class='bx bx-hard-hat iconX'></i><span class="title">Performance Técnicos</span><span class="title-tooltip">Performance</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
                        <li class="<?php if (isset($menuRelFinanceiro)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_financeiro') ?>"><i class='bx bx-dollar-circle iconX'></i><span class="title">Financeiro</span><span class="title-tooltip">Financeiro</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
                        <li class="<?php if (isset($menuRelProdutos)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_produtos') ?>"><i class='bx bx-package iconX'></i><span class="title">Produtos</span><span class="title-tooltip">Produtos</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
                        <li class="<?php if (isset($menuRelClientes)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_clientes') ?>"><i class='bx bx-user-check iconX'></i><span class="title">Clientes</span><span class="title-tooltip">Clientes</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
                        <li class="<?php if (isset($menuDREDashboard)) { echo 'active'; }; ?>"><a href="<?= site_url('dre') ?>"><i class='bx bx-bar-chart-alt-2 iconX'></i><span class="title">DRE Contábil</span><span class="title-tooltip">DRE</span></a></li>
                    <?php } ?>
                <?php } ?>

                <!-- CONFIGURAÇÕES -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="menu-divider"><span class="divider-text">CONFIGURAÇÕES</span></li>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente')) { ?>
                        <li class="<?php if (isset($menuUsuariosClienteListar)) { echo 'active'; }; ?>"><a href="<?= site_url('usuarioscliente') ?>"><i class='bx bx-user-circle iconX'></i><span class="title">Usuários Cliente</span><span class="title-tooltip">Usuários</span></a></li>
                    <?php } ?>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
                        <li class="<?php if (isset($menuArquivos)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('arquivos') ?>"><i class='bx bx-box iconX'></i><span class="title">Arquivos</span><span class="title-tooltip">Arquivos</span></a></li>
                    <?php } ?>

                    <!-- Ferramentas Admin - apenas para administradores -->
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                        <li class="menu-divider-sub"><span class="divider-text-sub">Administração</span></li>
                        <li class="<?php if (isset($menuMigrate)) { echo 'active'; }; ?>"><a href="<?= site_url('migrate') ?>"><i class='bx bx-data iconX'></i><span class="title">Migrações DB</span><span class="title-tooltip">Migrações</span></a></li>
                        <li class="<?php if (isset($menuEmailQueue)) { echo 'active'; }; ?>"><a href="<?= site_url('emails/dashboard') ?>"><i class='bx bx-envelope iconX'></i><span class="title">Fila de Emails</span><span class="title-tooltip">Fila Emails</span></a></li>
                        <li class="<?php if (isset($menuWebhooks)) { echo 'active'; }; ?>"><a href="<?= site_url('webhooks') ?>"><i class='bx bx-webhook iconX'></i><span class="title">Webhooks</span><span class="title-tooltip">Webhooks</span></a></li>
                        <li class="<?php if (isset($menuApiDocs)) { echo 'active'; }; ?>"><a href="<?= site_url('api/docs') ?>"><i class='bx bx-code-alt iconX'></i><span class="title">API v2</span><span class="title-tooltip">API v2</span></a></li>
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
