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
    <a href="#" class="d-inline d-sm-none btn">
        <div class="mode">
            <div class="moon-menu">
                <i class='bx bx-chevron-right iconX open-2'></i>
                <i class='bx bx-chevron-left iconX close-2'></i>
            </div>
        </div>
    </a>
    <!-- User Info -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            <?= strtoupper(mb_substr($this->session->userdata('nome') ?? 'U', 0, 2)) ?>
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name"><?= e($this->session->userdata('nome') ?? 'Usuario') ?></div>
            <div class="sidebar-user-role">
                <?php
                $perm_id = $this->session->userdata('permissao');
                $roles = [1 => 'Administrador', 2 => 'Gerente'];
                echo $roles[$perm_id] ?? 'Usuario';
                ?>
            </div>
        </div>
    </div>
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
                <!-- PRINCIPAL -->
                <li class="menu-divider"><span class="divider-text">PRINCIPAL</span></li>

                <li class="<?php if (isset($menuPainel)) { echo 'active'; }; ?>">
                    <a class="tip-bottom btn" title="" href="<?= base_url() ?>"><i class='bx bx-home-alt iconX'></i>
                        <span class="title nav-title">Inicio</span>
                        <span class="title-tooltip">Inicio</span>
                    </a>
                </li>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDashboard') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="<?php if (isset($menuDashboard)) { echo 'active'; }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('dashboard') ?>"><i class='bx bx-dashboard iconX'></i>
                            <span class="title">Dashboard</span>
                            <span class="title-tooltip">Dashboard</span>
                        </a>
                    </li>
                <?php } ?>

                <!-- OBRAS E PROJETOS (submenu colapsavel) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vObras') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
                    <li class="menu-divider"><span class="divider-text">OBRAS E PROJETOS</span></li>

                    <li class="submenu <?php if (isset($menuObras) || isset($menuObrasAdd) || isset($menuObrasTecnico)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom btn" title="" href="#">
                            <i class='bx bx-building-house iconX'></i>
                            <span class="title">Obras</span>
                            <span class="title-tooltip">Obras e Projetos</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo (isset($menuObras) || isset($menuObrasAdd) || isset($menuObrasTecnico)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vObras')) { ?>
                                <li class="<?php if (isset($menuObras)) { echo 'active'; }; ?>">
                                    <a class="tip-bottom btn" href="<?= site_url('obras') ?>" title="Gerenciar Obras">
                                        <i class='bx bx-building-house iconX'></i>
                                        <span class="title">Gerenciar Obras</span>
                                        <span class="title-tooltip">Obras</span>
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
                                <li class="<?php if (isset($menuObrasAdd)) { echo 'active'; }; ?>">
                                    <a class="tip-bottom btn" href="<?= site_url('obras/adicionar') ?>" title="Nova Obra">
                                        <i class='bx bx-plus-circle iconX'></i>
                                        <span class="title">Nova Obra</span>
                                        <span class="title-tooltip">Nova Obra</span>
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicoObra') && !$this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
                                <li class="<?php if (isset($menuObrasTecnico)) { echo 'active'; }; ?>">
                                    <a href="<?= site_url('obras_tecnico') ?>" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 8px; margin: 5px 10px;">
                                        <i class='bx bx-hard-hat iconX' style="color: white;"></i>
                                        <span class="title" style="color: white; font-weight: 600;">Minhas Obras</span>
                                        <span class="title-tooltip">Minhas Obras</span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- ORDENS DE SERVICO (submenu colapsavel) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs') || $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
                    <li class="menu-divider"><span class="divider-text">ORDENS DE SERVICO</span></li>

                    <li class="submenu <?php if (isset($menuOs) || isset($menuKanban) || isset($menuAtribuir) || isset($menuGarantia)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom btn" title="" href="#">
                            <i class='bx bx-file iconX'></i>
                            <span class="title">Ordens de Servico</span>
                            <span class="title-tooltip">OS</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo (isset($menuOs) || isset($menuKanban) || isset($menuAtribuir) || isset($menuGarantia)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                                <li class="<?php if (isset($menuOs)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('os') ?>"><i class='bx bx-file iconX'></i><span class="title">Todas as OS</span><span class="title-tooltip">Listar OS</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                                <li class="<?php if (isset($menuKanban)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('kanban') ?>"><i class='bx bx-columns iconX'></i><span class="title">Kanban Board</span><span class="title-tooltip">Kanban</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
                                <li class="<?php if (isset($menuAtribuir)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('os/atribuir') ?>"><i class='bx bx-user-plus iconX'></i><span class="title">Atribuir Tecnico</span><span class="title-tooltip">Atribuir</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vGarantia')) { ?>
                                <li class="<?php if (isset($menuGarantia)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('garantias') ?>"><i class='bx bx-receipt iconX'></i><span class="title">Garantias</span><span class="title-tooltip">Garantias</span></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- AREA DO TECNICO -->
                <?php if (
                    $this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicoDashboard') &&
                    !$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')
                ) { ?>
                    <li class="menu-divider"><span class="divider-text">AREA DO TECNICO</span></li>

                    <li class="<?php if (isset($menuAtividadesDashboard)) { echo 'active'; }; ?>">
                        <a href="<?= site_url('atividades') ?>" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 8px; margin: 5px 10px;">
                            <i class='bx bx-timer iconX' style="color: white;"></i>
                            <span class="title" style="color: white; font-weight: 600;">Minhas Atividades</span>
                            <span class="title-tooltip">Atividades</span>
                        </a>
                    </li>

                    <li class="<?php if (isset($menuTecnicoDashboard)) { echo 'active'; }; ?>">
                        <a href="<?= site_url('tecnico') ?>" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; margin: 5px 10px;">
                            <i class='bx bx-hard-hat iconX' style="color: white;"></i>
                            <span class="title" style="color: white; font-weight: 600;">Acessar Portal</span>
                            <span class="title-tooltip">Portal Tecnico</span>
                        </a>
                    </li>
                <?php } ?>

                <!-- ADMIN: GESTAO DE ATIVIDADES -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="menu-divider"><span class="divider-text">REGISTRO DE ATIVIDADES</span></li>
                    <li class="<?php if (isset($menuAtividadesDashboard)) { echo 'active'; }; ?>">
                        <a href="<?= site_url('atividades') ?>">
                            <i class='bx bx-timer iconX'></i>
                            <span class="title">Dashboard Atividades</span>
                            <span class="title-tooltip">Dashboard</span>
                        </a>
                    </li>
                    <li class="<?php if (isset($menuAtividadesRelatorio)) { echo 'active'; }; ?>">
                        <a href="<?= site_url('atividades/relatorio') ?>">
                            <i class='bx bx-chart iconX'></i>
                            <span class="title">Relatorio de Atividades</span>
                            <span class="title-tooltip">Relatorio</span>
                        </a>
                    </li>
                <?php } ?>

                <!-- CADASTROS (submenu colapsavel) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vServico') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) { ?>
                    <li class="menu-divider"><span class="divider-text">CADASTROS</span></li>

                    <li class="submenu <?php if (isset($menuClientes) || isset($menuProdutos) || isset($menuServicos) || isset($menuVendas) || isset($menuTecnicosAdmin)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom btn" title="" href="#">
                            <i class='bx bx-layer iconX'></i>
                            <span class="title">Cadastros</span>
                            <span class="title-tooltip">Cadastros</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo (isset($menuClientes) || isset($menuProdutos) || isset($menuServicos) || isset($menuVendas) || isset($menuTecnicosAdmin)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                                <li class="<?php if (isset($menuClientes)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('clientes') ?>"><i class='bx bx-user iconX'></i><span class="title">Clientes</span><span class="title-tooltip">Clientes</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                                <li class="<?php if (isset($menuProdutos)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('produtos') ?>"><i class='bx bx-basket iconX'></i><span class="title">Produtos</span><span class="title-tooltip">Produtos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
                                <li class="<?php if (isset($menuServicos)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('servicos') ?>"><i class='bx bx-wrench iconX'></i><span class="title">Servicos</span><span class="title-tooltip">Servicos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
                                <li class="<?php if (isset($menuVendas)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('vendas') ?>"><i class='bx bx-cart-alt iconX'></i><span class="title">Vendas</span><span class="title-tooltip">Vendas</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) { ?>
                                <?php
                                $this->load->model('tecnico_model');
                                $total_tecnicos = $this->db->where('is_tecnico', 1)->count_all_results('usuarios');
                                ?>
                                <li class="<?php if (isset($menuTecnicosAdmin)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('tecnicos_admin') ?>">
                                    <i class='bx bx-hard-hat iconX'></i>
                                    <span class="title">Tecnicos</span>
                                    <span class="menu-badge"><?= $total_tecnicos ?></span>
                                    <span class="title-tooltip">Tecnicos (<?= $total_tecnicos ?>)</span>
                                </a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- FINANCEIRO (submenu colapsavel) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
                    <li class="menu-divider"><span class="divider-text">FINANCEIRO</span></li>

                    <li class="submenu <?php if (isset($menuLancamentos) || isset($menuCobrancas)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom btn" title="" href="#">
                            <i class='bx bx-bar-chart-alt-2 iconX'></i>
                            <span class="title">Financeiro</span>
                            <span class="title-tooltip">Financeiro</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo (isset($menuLancamentos) || isset($menuCobrancas)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                                <li class="<?php if (isset($menuLancamentos)) { echo 'active'; }; ?>"><a href="<?= site_url('financeiro/lancamentos') ?>"><i class='bx bx-bar-chart-alt-2 iconX'></i><span class="title">Lancamentos</span><span class="title-tooltip">Lancamentos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
                                <li class="<?php if (isset($menuCobrancas)) { echo 'active'; }; ?>"><a href="<?= site_url('cobrancas/cobrancas') ?>"><i class='bx bx-credit-card iconX'></i><span class="title">Cobrancas</span><span class="title-tooltip">Cobrancas</span></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- DOCUMENTOS FISCAIS (submenu colapsavel) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
                    <li class="menu-divider"><span class="divider-text">DOCUMENTOS FISCAIS</span></li>

                    <li class="submenu <?php if (isset($menuNfseOsDashboard) || isset($menuNfseOsRelatorio) || isset($menuCertificado) || isset($menuImpostos)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom btn" title="" href="#">
                            <i class='bx bx-receipt iconX'></i>
                            <span class="title">Doc. Fiscais</span>
                            <span class="title-tooltip">Documentos Fiscais</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo (isset($menuNfseOsDashboard) || isset($menuNfseOsRelatorio) || isset($menuCertificado) || isset($menuImpostos)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) { ?>
                                <li class="<?php if (isset($menuNfseOsDashboard)) { echo 'active'; }; ?>"><a href="<?= site_url('nfse_os') ?>"><i class='bx bx-receipt iconX'></i><span class="title">NFSe Dashboard</span><span class="title-tooltip">NFSe</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) { ?>
                                <li class="<?php if (isset($menuNfseOsRelatorio)) { echo 'active'; }; ?>"><a href="<?= site_url('nfse_os/relatorio') ?>"><i class='bx bx-chart iconX'></i><span class="title">Relatorio NFSe/Boletos</span><span class="title-tooltip">Relatorio</span></a></li>
                            <?php } ?>

                            <!-- Certificado Digital (submenu) -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
                                <li class="submenu <?php if (isset($menuCertificado)) { echo 'active open'; }; ?>">
                                    <a class="tip-bottom btn" title="" href="#"><i class='bx bx-id-card iconX'></i>
                                        <span class="title">Certificado Digital</span>
                                        <span class="title-tooltip">Certificado</span>
                                        <i class='bx bx-chevron-down arrow'></i>
                                    </a>
                                    <ul style="display: <?php echo isset($menuCertificado) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuCertificadoDashboard)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('certificado') ?>">
                                                <i class='bx bx-check-shield iconX'></i>
                                                <span class="title">Status</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuCertificadoConfig)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('certificado/configurar') ?>">
                                                <i class='bx bx-cog iconX'></i>
                                                <span class="title">Configurar</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuNfseListar)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('nfse') ?>">
                                                <i class='bx bx-receipt iconX'></i>
                                                <span class="title">NFS-e Importadas</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuCertificadoImportar)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('certificado/importar_nfse') ?>">
                                                <i class='bx bx-import iconX'></i>
                                                <span class="title">Importar NFS-e</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>

                            <!-- Impostos Simples (submenu) -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
                                <li class="submenu <?php if (isset($menuImpostos)) { echo 'active open'; }; ?>">
                                    <a class="tip-bottom btn" title="" href="#"><i class='bx bx-money iconX'></i>
                                        <span class="title">Impostos Simples</span>
                                        <span class="title-tooltip">Impostos</span>
                                        <i class='bx bx-chevron-down arrow'></i>
                                    </a>
                                    <ul style="display: <?php echo isset($menuImpostos) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuImpostosDashboard)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('impostos') ?>">
                                                <i class='bx bx-chart iconX'></i>
                                                <span class="title">Dashboard</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuImpostosConfig)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('impostos/configuracoes') ?>">
                                                <i class='bx bx-cog iconX'></i>
                                                <span class="title">Configuracoes</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuImpostosSimulador)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('impostos/simulador') ?>">
                                                <i class='bx bx-calculator iconX'></i>
                                                <span class="title">Simulador</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- RELATORIOS (submenu colapsavel) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
                    <li class="menu-divider"><span class="divider-text">RELATORIOS</span></li>

                    <li class="submenu <?php if (isset($menuRelatorioAtendimentos) || isset($menuRelTecnicos) || isset($menuRelFinanceiro) || isset($menuRelProdutos) || isset($menuRelClientes) || isset($menuDRE)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom btn" title="" href="#">
                            <i class='bx bx-pie-chart-alt-2 iconX'></i>
                            <span class="title">Relatorios</span>
                            <span class="title-tooltip">Relatorios</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo (isset($menuRelatorioAtendimentos) || isset($menuRelTecnicos) || isset($menuRelFinanceiro) || isset($menuRelProdutos) || isset($menuRelClientes) || isset($menuDRE)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos')) { ?>
                                <li class="<?php if (isset($menuRelatorioAtendimentos)) { echo 'active'; }; ?>"><a href="<?= site_url('relatorioatendimentos') ?>"><i class='bx bx-time iconX'></i><span class="title">Atendimentos</span><span class="title-tooltip">Atendimentos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
                                <li class="<?php if (isset($menuRelTecnicos)) { echo 'active'; }; ?>"><a href="<?= site_url('relatoriotecnicos') ?>"><i class='bx bx-hard-hat iconX'></i><span class="title">Performance Tecnicos</span><span class="title-tooltip">Performance</span></a></li>
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

                            <!-- DRE Contabil (submenu) -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
                                <li class="submenu <?php if (isset($menuDRE)) { echo 'active open'; }; ?>">
                                    <a class="tip-bottom btn" title="" href="#"><i class='bx bx-line-chart-down iconX'></i>
                                        <span class="title">DRE Contabil</span>
                                        <span class="title-tooltip">DRE Contabil</span>
                                        <i class='bx bx-chevron-down arrow'></i>
                                    </a>
                                    <ul style="display: <?php echo isset($menuDRE) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuDREDashboard)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('dre') ?>">
                                                <i class='bx bx-bar-chart-alt-2 iconX'></i>
                                                <span class="title">Demonstracao</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuDREContas)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('dre/contas') ?>">
                                                <i class='bx bx-list-ul iconX'></i>
                                                <span class="title">Plano de Contas</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuDRELancamentos)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('dre/lancamentos') ?>">
                                                <i class='bx bx-book iconX'></i>
                                                <span class="title">Lancamentos</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- CONFIGURACOES (submenu colapsavel) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cAuditoria') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) { ?>
                    <li class="menu-divider"><span class="divider-text">CONFIGURACOES</span></li>

                    <li class="submenu <?php if (isset($menuUsuarios) || isset($menuPermissoes) || isset($menuUsuariosCliente) || isset($menuArquivos) || isset($menuEmitente) || isset($menuConfigSistema) || isset($menuModulos) || isset($menuBackup) || isset($menuAuditoria) || isset($menuConfiguracoesNotificacoes) || isset($menuConfiguracoesTemplates) || isset($menuConfiguracoesLogs) || isset($menuMigrate) || isset($menuDiagnostico) || isset($menuEmailQueue) || isset($menuEmailConfig) || isset($menuWebhooks) || isset($menuWebhooksDocs) || isset($menuApiDocs) || isset($menuAgenteIA)) { echo 'active open'; }; ?>">
                        <a class="tip-bottom btn" title="" href="#">
                            <i class='bx bx-cog iconX'></i>
                            <span class="title">Configuracoes</span>
                            <span class="title-tooltip">Configuracoes</span>
                            <i class='bx bx-chevron-down arrow'></i>
                        </a>
                        <ul style="display: <?php echo (isset($menuUsuarios) || isset($menuPermissoes) || isset($menuUsuariosCliente) || isset($menuArquivos) || isset($menuEmitente) || isset($menuConfigSistema) || isset($menuModulos) || isset($menuBackup) || isset($menuAuditoria) || isset($menuConfiguracoesNotificacoes) || isset($menuConfiguracoesTemplates) || isset($menuConfiguracoesLogs) || isset($menuMigrate) || isset($menuDiagnostico) || isset($menuEmailQueue) || isset($menuEmailConfig) || isset($menuWebhooks) || isset($menuWebhooksDocs) || isset($menuApiDocs) || isset($menuAgenteIA)) ? 'block' : 'none'; ?>;">
                            <!-- Usuarios e Permissoes -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) { ?>
                                <li class="<?php if (isset($menuUsuarios)) { echo 'active'; }; ?>"><a href="<?= site_url('usuarios') ?>"><i class='bx bx-user-circle iconX'></i><span class="title">Usuarios</span><span class="title-tooltip">Usuarios do Sistema</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                                <li class="<?php if (isset($menuPermissoes)) { echo 'active'; }; ?>"><a href="<?= site_url('permissoes') ?>"><i class='bx bx-shield-quarter iconX'></i><span class="title">Permissoes</span><span class="title-tooltip">Grupos de Permissao</span></a></li>
                            <?php } ?>

                            <!-- Usuarios Cliente (submenu) -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente')) { ?>
                                <li class="submenu <?php if (isset($menuUsuariosCliente)) { echo 'active open'; }; ?>">
                                    <a class="tip-bottom btn" title="" href="#"><i class='bx bx-group iconX'></i>
                                        <span class="title">Usuarios Cliente</span>
                                        <span class="title-tooltip">Portal Cliente</span>
                                        <i class='bx bx-chevron-down arrow'></i>
                                    </a>
                                    <ul style="display: <?php echo isset($menuUsuariosCliente) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuUsuariosClienteListar)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('usuarioscliente') ?>">
                                                <i class='bx bx-list-ul iconX'></i>
                                                <span class="title">Listar Usuarios</span>
                                            </a>
                                        </li>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuariosCliente')) { ?>
                                        <li class="<?php if (isset($menuUsuariosClienteAdicionar)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('usuarioscliente/adicionar') ?>">
                                                <i class='bx bx-plus iconX'></i>
                                                <span class="title">Novo Usuario</span>
                                            </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
                                <li class="<?php if (isset($menuArquivos)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('arquivos') ?>"><i class='bx bx-box iconX'></i><span class="title">Arquivos</span><span class="title-tooltip">Arquivos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) { ?>
                                <li class="<?php if (isset($menuEmitente)) { echo 'active'; }; ?>"><a href="<?= site_url('mapos/emitente') ?>"><i class='bx bx-building iconX'></i><span class="title">Emitente</span><span class="title-tooltip">Dados da Empresa</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema')) { ?>
                                <li class="<?php if (isset($menuConfigSistema)) { echo 'active'; }; ?>"><a href="<?= site_url('mapos/configurar') ?>"><i class='bx bx-cog iconX'></i><span class="title">Config. Sistema</span><span class="title-tooltip">Configuracoes do Sistema</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                                <li class="<?php if (isset($menuModulos)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('modulos') ?>"><i class='bx bx-extension iconX'></i><span class="title">Modulos</span><span class="title-tooltip">Modulos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) { ?>
                                <li class="<?php if (isset($menuBackup)) { echo 'active'; }; ?>"><a href="<?= site_url('backup') ?>"><i class='bx bx-data iconX'></i><span class="title">Backup</span><span class="title-tooltip">Backup e Restauracao</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cAuditoria')) { ?>
                                <li class="<?php if (isset($menuAuditoria)) { echo 'active'; }; ?>"><a href="<?= site_url('auditoria') ?>"><i class='bx bx-file-find iconX'></i><span class="title">Auditoria</span><span class="title-tooltip">Logs de Auditoria</span></a></li>
                            <?php } ?>

                            <!-- Comunicacao -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) { ?>
                                <li class="menu-divider-sub"><span class="divider-text-sub">Comunicacao</span></li>
                                <li class="<?php if (isset($menuConfiguracoesNotificacoes)) { echo 'active'; }; ?>">
                                    <a href="<?= site_url('notificacoes/configuracoes') ?>">
                                        <i class='bx bxl-whatsapp iconX'></i>
                                        <span class="title">Notificacoes</span>
                                        <span class="title-tooltip">Notificacoes</span>
                                    </a>
                                </li>
                                <li class="<?php if (isset($menuConfiguracoesTemplates)) { echo 'active'; }; ?>">
                                    <a href="<?= site_url('notificacoes/templates') ?>">
                                        <i class='bx bx-message-square-dots iconX'></i>
                                        <span class="title">Templates</span>
                                        <span class="title-tooltip">Templates</span>
                                    </a>
                                </li>
                                <li class="<?php if (isset($menuConfiguracoesLogs)) { echo 'active'; }; ?>">
                                    <a href="<?= site_url('notificacoes/logs') ?>">
                                        <i class='bx bx-history iconX'></i>
                                        <span class="title">Historico</span>
                                        <span class="title-tooltip">Historico</span>
                                    </a>
                                </li>
                            <?php } ?>

                            <!-- Administracao -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                                <li class="menu-divider-sub"><span class="divider-text-sub">Administracao</span></li>
                                <li class="<?php if (isset($menuMigrate)) { echo 'active'; }; ?>"><a href="<?= site_url('migrate') ?>"><i class='bx bx-data iconX'></i><span class="title">Migracoes DB</span><span class="title-tooltip">Migracoes</span></a></li>
                                <li class="<?php if (isset($menuDiagnostico)) { echo 'active'; }; ?>"><a href="<?= site_url('diagnostico') ?>"><i class='bx bx-bug iconX'></i><span class="title">Diagnostico</span><span class="title-tooltip">Diagnostico do Sistema</span></a></li>
                                <li class="<?php if (isset($menuEmailQueue)) { echo 'active'; }; ?>"><a href="<?= site_url('emails/dashboard') ?>"><i class='bx bx-envelope iconX'></i><span class="title">Fila de Emails</span><span class="title-tooltip">Fila Emails</span></a></li>
                                <li class="<?php if (isset($menuEmailConfig)) { echo 'active'; }; ?>"><a href="<?= site_url('email/configuracoes') ?>"><i class='bx bx-cog iconX'></i><span class="title">Config. Emails</span><span class="title-tooltip">Config Emails</span></a></li>
                                <li class="<?php if (isset($menuWebhooks)) { echo 'active'; }; ?>"><a href="<?= site_url('webhooks') ?>"><i class='bx bx-webhook iconX'></i><span class="title">Webhooks</span><span class="title-tooltip">Webhooks</span></a></li>
                                <li class="<?php if (isset($menuWebhooksDocs)) { echo 'active'; }; ?>"><a href="<?= site_url('webhooks/docs') ?>" target="_blank"><i class='bx bx-book-open iconX'></i><span class="title">Docs Webhooks</span><span class="title-tooltip">Docs Webhooks</span></a></li>
                                <li class="<?php if (isset($menuApiDocs)) { echo 'active'; }; ?>"><a href="<?= site_url('api/docs') ?>"><i class='bx bx-code-alt iconX'></i><span class="title">API v2</span><span class="title-tooltip">API v2</span></a></li>
                                <li class="<?php if (isset($menuAgenteIA)) { echo 'active'; }; ?>"><a href="<?= site_url('agente_ia') ?>"><i class='bx bx-bot iconX'></i><span class="title">Agente IA</span><span class="title-tooltip">Agente IA</span></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="botton-content">
            <ul style="padding: 0; margin: 0; list-style: none;">
                <li class="<?php if (isset($menuMinhaConta)) { echo 'active'; }; ?>">
                    <a class="tip-bottom btn" title="" href="<?= site_url('mapos/minhaConta'); ?>">
                        <i class='bx bx-user iconX'></i>
                        <span class="title">Minha Conta</span>
                        <span class="title-tooltip">Minha Conta</span>
                    </a>
                </li>
                <li>
                    <a class="tip-bottom btn" title="" href="<?= site_url('login/sair'); ?>">
                        <i class='bx bx-log-out-circle iconX'></i>
                        <span class="title">Sair</span>
                        <span class="title-tooltip">Sair</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!--End sidebar-menu-->