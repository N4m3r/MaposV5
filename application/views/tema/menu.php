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
                <li class="<?php if (isset($menuPainel)) { echo 'active'; }; ?>">
                    <a class="tip-bottom" title="" href="<?= base_url() ?>"><i class='bx bx-home-alt iconX'></i>
                        <span class="title nav-title">Home</span>
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

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="submenu <?php if (isset($menuRelatorios)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom" title="" href="#"><i class='bx bx-line-chart iconX'></i>
                            <span class="title">Relatórios</span>
                            <span class="title-tooltip">Relatórios</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo isset($menuRelatorios) ? 'block' : 'none'; ?>;">
                            <li class="<?php if (isset($menuRelAtendimentos)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_atendimentos') ?>"><i class='bx bx-time iconX'></i><span class="title">Atendimentos</span></a></li>
                            <li class="<?php if (isset($menuRelFinanceiro)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_financeiro') ?>"><i class='bx bx-dollar-circle iconX'></i><span class="title">Financeiro</span></a></li>
                            <li class="<?php if (isset($menuRelProdutos)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_produtos') ?>"><i class='bx bx-package iconX'></i><span class="title">Produtos</span></a></li>
                            <li class="<?php if (isset($menuRelClientes)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_clientes') ?>"><i class='bx bx-user-check iconX'></i><span class="title">Clientes</span></a></li>
                            <li class="<?php if (isset($menuRelTecnicos)) { echo 'active'; }; ?>"><a href="<?= site_url('relatoriotecnicos') ?>"><i class='bx bx-hard-hat iconX'></i><span class="title">Performance Técnicos</span></a></li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                    <li class="<?php if (isset($menuClientes)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('clientes') ?>"><i class='bx bx-user iconX'></i><span class="title">Cliente / Fornecedor</span><span class="title-tooltip">Clientes</span></a></li>
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

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                    <li class="<?php if (isset($menuOs)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('os') ?>"><i class='bx bx-file iconX'></i><span class="title">Ordens de Serviço</span><span class="title-tooltip">Ordens</span></a></li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                    <li class="<?php if (isset($menuKanban)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('kanban') ?>"><i class='bx bx-columns iconX'></i><span class="title">Kanban Board</span><span class="title-tooltip">Kanban</span></a></li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
                    <li class="<?php if (isset($menuAtribuir)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('os/atribuir') ?>"><i class='bx bx-user-plus iconX'></i><span class="title">Atribuir Técnico</span><span class="title-tooltip">Atribuir Téc.</span></a></li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vGarantia')) { ?>
                    <li class="<?php if (isset($menuGarantia)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('garantias') ?>"><i class='bx bx-receipt iconX'></i><span class="title">Termos de Garantias</span><span class="title-tooltip">Garantias</span></a></li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
                    <li class="<?php if (isset($menuArquivos)) { echo 'active'; }; ?>"><a class="tip-bottom" href="<?= site_url('arquivos') ?>"><i class='bx bx-box iconX'></i><span class="title">Arquivos</span><span class="title-tooltip">Arquivos</span></a></li>
                <?php } ?>

                <!-- Financeiro - Agrupado -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento') || $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
                    <li class="submenu <?php if (isset($menuFinanceiro)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom" href="#"><i class='bx bx-dollar-circle iconX'></i>
                            <span class="title">Financeiro</span>
                            <span class="title-tooltip">Financeiro</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo isset($menuFinanceiro) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                                <li class="<?php if (isset($menuLancamentos)) { echo 'active'; }; ?>"><a href="<?= site_url('financeiro/lancamentos') ?>"><i class='bx bx-bar-chart-alt-2 iconX'></i><span class="title">Lançamentos</span></a></li>
                            <?php } ?>
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
                                <li class="<?php if (isset($menuCobrancas)) { echo 'active'; }; ?>"><a href="<?= site_url('cobrancas/cobrancas') ?>"><i class='bx bx-credit-card iconX'></i><span class="title">Cobranças</span></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- NOVAS FUNCIONALIDADES V5 - Agrupadas em submenu -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente')) { ?>
                    <li class="submenu <?php if (isset($menuNovidadesV5)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom" href="#"><i class='bx bx-rocket iconX'></i>
                            <span class="title">Novidades V5</span>
                            <span class="title-tooltip">Novidades V5</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo isset($menuNovidadesV5) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos')) { ?>
                                <li class="<?php if (isset($menuRelatorioAtendimentos)) { echo 'active'; }; ?>"><a href="<?= site_url('relatorioatendimentos') ?>"><i class='bx bx-time iconX'></i><span class="title">Rel. Atendimentos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
                                <li class="submenu <?php if (isset($menuCertificado)) { echo 'active open'; }; ?>">
                                    <a href="#"><i class='bx bx-id-card iconX'></i><span class="title">Certificado</span><i class='bx bx-chevron-down arrow'></i></a>
                                    <ul style="display: <?php echo isset($menuCertificado) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuCertificadoDashboard)) { echo 'active'; }; ?>"><a href="<?= site_url('certificado') ?>"><i class='bx bx-check-shield iconX'></i><span class="title">Status</span></a></li>
                                        <li class="<?php if (isset($menuCertificadoConfig)) { echo 'active'; }; ?>"><a href="<?= site_url('certificado/configurar') ?>"><i class='bx bx-cog iconX'></i><span class="title">Configurar</span></a></li>
                                        <li class="<?php if (isset($menuNfseListar)) { echo 'active'; }; ?>"><a href="<?= site_url('nfse') ?>"><i class='bx bx-receipt iconX'></i><span class="title">NFS-e Importadas</span></a></li>
                                        <li class="<?php if (isset($menuCertificadoImportar)) { echo 'active'; }; ?>"><a href="<?= site_url('certificado/importar_nfse') ?>"><i class='bx bx-import iconX'></i><span class="title">Importar NFS-e</span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
                                <li class="submenu <?php if (isset($menuImpostos)) { echo 'active open'; }; ?>">
                                    <a href="#"><i class='bx bx-money iconX'></i><span class="title">Impostos</span><i class='bx bx-chevron-down arrow'></i></a>
                                    <ul style="display: <?php echo isset($menuImpostos) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuImpostosDashboard)) { echo 'active'; }; ?>"><a href="<?= site_url('impostos') ?>"><i class='bx bx-chart iconX'></i><span class="title">Dashboard</span></a></li>
                                        <li class="<?php if (isset($menuImpostosConfig)) { echo 'active'; }; ?>"><a href="<?= site_url('impostos/configuracoes') ?>"><i class='bx bx-cog iconX'></i><span class="title">Configurações</span></a></li>
                                        <li class="<?php if (isset($menuImpostosSimulador)) { echo 'active'; }; ?>"><a href="<?= site_url('impostos/simulador') ?>"><i class='bx bx-calculator iconX'></i><span class="title">Simulador</span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
                                <li class="submenu <?php if (isset($menuDRE)) { echo 'active open'; }; ?>">
                                    <a href="#"><i class='bx bx-line-chart-down iconX'></i><span class="title">DRE Contábil</span><i class='bx bx-chevron-down arrow'></i></a>
                                    <ul style="display: <?php echo isset($menuDRE) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuDREDashboard)) { echo 'active'; }; ?>"><a href="<?= site_url('dre') ?>"><i class='bx bx-bar-chart-alt-2 iconX'></i><span class="title">Demonstração</span></a></li>
                                        <li class="<?php if (isset($menuDREContas)) { echo 'active'; }; ?>"><a href="<?= site_url('dre/contas') ?>"><i class='bx bx-list-ul iconX'></i><span class="title">Plano de Contas</span></a></li>
                                        <li class="<?php if (isset($menuDRELancamentos)) { echo 'active'; }; ?>"><a href="<?= site_url('dre/lancamentos') ?>"><i class='bx bx-book iconX'></i><span class="title">Lançamentos</span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente')) { ?>
                                <li class="submenu <?php if (isset($menuUsuariosCliente)) { echo 'active open'; }; ?>">
                                    <a href="#"><i class='bx bx-user-check iconX'></i><span class="title">Usuários Cliente</span><i class='bx bx-chevron-down arrow'></i></a>
                                    <ul style="display: <?php echo isset($menuUsuariosCliente) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuUsuariosClienteListar)) { echo 'active'; }; ?>"><a href="<?= site_url('usuarioscliente') ?>"><i class='bx bx-list-ul iconX'></i><span class="title">Listar Usuários</span></a></li>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuariosCliente')) { ?>
                                        <li class="<?php if (isset($menuUsuariosClienteAdicionar)) { echo 'active'; }; ?>"><a href="<?= site_url('usuarioscliente/adicionar') ?>"><i class='bx bx-plus iconX'></i><span class="title">Novo Usuário</span></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Ferramentas Admin -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="submenu <?php if (isset($menuFerramentas)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom" href="#"><i class='bx bx-wrench iconX'></i>
                            <span class="title">Ferramentas</span>
                            <span class="title-tooltip">Ferramentas</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo isset($menuFerramentas) ? 'block' : 'none'; ?>;">
                            <li class="<?php if (isset($menuEmailQueue)) { echo 'active'; }; ?>"><a href="<?= site_url('emails/dashboard') ?>"><i class='bx bx-envelope iconX'></i><span class="title">Fila de Emails</span></a></li>
                            <li class="<?php if (isset($menuWebhooks)) { echo 'active'; }; ?>"><a href="<?= site_url('webhooks') ?>"><i class='bx bx-webhook iconX'></i><span class="title">Webhooks</span></a></li>
                            <li class="<?php if (isset($menuWebhooksDocs)) { echo 'active'; }; ?>"><a href="<?= site_url('webhooks/docs') ?>" target="_blank"><i class='bx bx-book-open iconX'></i><span class="title">Docs Webhooks</span></a></li>
                            <li class="<?php if (isset($menuApiDocs)) { echo 'active'; }; ?>"><a href="<?= site_url('api/docs') ?>"><i class='bx bx-code-alt iconX'></i><span class="title">API v2</span></a></li>
                        </ul>
                    </li>
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
