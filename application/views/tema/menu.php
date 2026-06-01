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
    <!-- Desktop collapse/expand toggle -->
    <button class="sidebar-collapse-toggle" title="Expandir/Recolher menu" aria-label="Alternar menu">
        <?= svg_icon('chevrons-left', 20, 20, 'iconX collapse-icon') ?>
        <?= svg_icon('chevrons-right', 20, 20, 'iconX expand-icon') ?>
    </button>
    <!-- Mobile toggle (kept for small screens) -->
    <a href="#" class="d-inline d-sm-none btn">
        <div class="mode">
            <div class="moon-menu">
                <?= svg_icon('chevron-right', 18, 18, 'iconX open-2') ?>
                <?= svg_icon('chevron-left', 18, 18, 'iconX close-2') ?>
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
                <?= svg_icon('search', 20, 20, 'iconX') ?></button>
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
                    <a class="tip-bottom btn" title="" href="<?= base_url() ?>"><?= svg_icon('home', 20, 20, 'iconX') ?>
                        <span class="title nav-title">Inicio</span>
                        <span class="title-tooltip">Inicio</span>
                    </a>
                </li>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDashboard') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="<?php if (isset($menuDashboard)) { echo 'active'; }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('dashboard') ?>"><?= svg_icon('dashboard', 20, 20, 'iconX') ?>
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
                            <?= svg_icon('building-house', 20, 20, 'iconX') ?>
                            <span class="title">Obras</span>
                            <span class="title-tooltip">Obras e Projetos</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo (isset($menuObras) || isset($menuObrasAdd) || isset($menuObrasTecnico)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vObras')) { ?>
                                <li class="<?php if (isset($menuObras)) { echo 'active'; }; ?>">
                                    <a class="tip-bottom btn" href="<?= site_url('obras') ?>" title="Gerenciar Obras">
                                        <?= svg_icon('building-house', 20, 20, 'iconX') ?>
                                        <span class="title">Gerenciar Obras</span>
                                        <span class="title-tooltip">Obras</span>
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
                                <li class="<?php if (isset($menuObrasAdd)) { echo 'active'; }; ?>">
                                    <a class="tip-bottom btn" href="<?= site_url('obras/adicionar') ?>" title="Nova Obra">
                                        <?= svg_icon('plus-circle', 20, 20, 'iconX') ?>
                                        <span class="title">Nova Obra</span>
                                        <span class="title-tooltip">Nova Obra</span>
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicoObra') && !$this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')) { ?>
                                <li class="<?php if (isset($menuObrasTecnico)) { echo 'active'; }; ?>">
                                    <a href="<?= site_url('obras_tecnico') ?>" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 8px; margin: 5px 10px;">
                                        <?= svg_icon('hard-hat', 20, 20, 'iconX', 'color: white;') ?>
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
                            <?= svg_icon('file', 20, 20, 'iconX') ?>
                            <span class="title">Ordens de Servico</span>
                            <span class="title-tooltip">OS</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo (isset($menuOs) || isset($menuKanban) || isset($menuAtribuir) || isset($menuGarantia)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                                <li class="<?php if (isset($menuOs)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('os') ?>"><?= svg_icon('file', 20, 20, 'iconX') ?><span class="title">Todas as OS</span><span class="title-tooltip">Listar OS</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                                <li class="<?php if (isset($menuKanban)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('kanban') ?>"><?= svg_icon('columns', 20, 20, 'iconX') ?><span class="title">Kanban Board</span><span class="title-tooltip">Kanban</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
                                <li class="<?php if (isset($menuAtribuir)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('os/atribuir') ?>"><?= svg_icon('user-plus', 20, 20, 'iconX') ?><span class="title">Atribuir Tecnico</span><span class="title-tooltip">Atribuir</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vGarantia')) { ?>
                                <li class="<?php if (isset($menuGarantia)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('garantias') ?>"><?= svg_icon('receipt', 20, 20, 'iconX') ?><span class="title">Garantias</span><span class="title-tooltip">Garantias</span></a></li>
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
                            <?= svg_icon('timer', 20, 20, 'iconX', 'color: white;') ?>
                            <span class="title" style="color: white; font-weight: 600;">Minhas Atividades</span>
                            <span class="title-tooltip">Atividades</span>
                        </a>
                    </li>

                    <li class="<?php if (isset($menuTecnicoDashboard)) { echo 'active'; }; ?>">
                        <a href="<?= site_url('tecnico') ?>" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; margin: 5px 10px;">
                            <?= svg_icon('hard-hat', 20, 20, 'iconX', 'color: white;') ?>
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
                            <?= svg_icon('timer', 20, 20, 'iconX') ?>
                            <span class="title">Dashboard Atividades</span>
                            <span class="title-tooltip">Dashboard</span>
                        </a>
                    </li>
                    <li class="<?php if (isset($menuAtividadesRelatorio)) { echo 'active'; }; ?>">
                        <a href="<?= site_url('atividades/relatorio') ?>">
                            <?= svg_icon('chart', 20, 20, 'iconX') ?>
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
                            <?= svg_icon('layers', 20, 20, 'iconX') ?>
                            <span class="title">Cadastros</span>
                            <span class="title-tooltip">Cadastros</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo (isset($menuClientes) || isset($menuProdutos) || isset($menuServicos) || isset($menuVendas) || isset($menuTecnicosAdmin)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                                <li class="<?php if (isset($menuClientes)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('clientes') ?>"><?= svg_icon('user', 20, 20, 'iconX') ?><span class="title">Clientes</span><span class="title-tooltip">Clientes</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                                <li class="<?php if (isset($menuProdutos)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('produtos') ?>"><?= svg_icon('basket', 20, 20, 'iconX') ?><span class="title">Produtos</span><span class="title-tooltip">Produtos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
                                <li class="<?php if (isset($menuServicos)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('servicos') ?>"><?= svg_icon('wrench', 20, 20, 'iconX') ?><span class="title">Servicos</span><span class="title-tooltip">Servicos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
                                <li class="<?php if (isset($menuVendas)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('vendas') ?>"><?= svg_icon('cart', 20, 20, 'iconX') ?><span class="title">Vendas</span><span class="title-tooltip">Vendas</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cTecnico')) { ?>
                                <?php
                                $this->load->model('tecnico_model');
                                $total_tecnicos = $this->db->where('is_tecnico', 1)->count_all_results('usuarios');
                                ?>
                                <li class="<?php if (isset($menuTecnicosAdmin)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('tecnicos_admin') ?>">
                                    <?= svg_icon('hard-hat', 20, 20, 'iconX') ?>
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
                            <?= svg_icon('bar-chart-alt', 20, 20, 'iconX') ?>
                            <span class="title">Financeiro</span>
                            <span class="title-tooltip">Financeiro</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo (isset($menuLancamentos) || isset($menuCobrancas)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                                <li class="<?php if (isset($menuLancamentos)) { echo 'active'; }; ?>"><a href="<?= site_url('financeiro/lancamentos') ?>"><?= svg_icon('bar-chart-alt', 20, 20, 'iconX') ?><span class="title">Lancamentos</span><span class="title-tooltip">Lancamentos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
                                <li class="<?php if (isset($menuCobrancas)) { echo 'active'; }; ?>"><a href="<?= site_url('cobrancas/cobrancas') ?>"><?= svg_icon('credit-card', 20, 20, 'iconX') ?><span class="title">Cobrancas</span><span class="title-tooltip">Cobrancas</span></a></li>
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
                            <?= svg_icon('receipt', 20, 20, 'iconX') ?>
                            <span class="title">Doc. Fiscais</span>
                            <span class="title-tooltip">Documentos Fiscais</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo (isset($menuNfseOsDashboard) || isset($menuNfseOsRelatorio) || isset($menuCertificado) || isset($menuImpostos)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) { ?>
                                <li class="<?php if (isset($menuNfseOsDashboard)) { echo 'active'; }; ?>"><a href="<?= site_url('nfse_os') ?>"><?= svg_icon('receipt', 20, 20, 'iconX') ?><span class="title">NFSe Dashboard</span><span class="title-tooltip">NFSe</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNFSe')) { ?>
                                <li class="<?php if (isset($menuNfseOsRelatorio)) { echo 'active'; }; ?>"><a href="<?= site_url('nfse_os/relatorio') ?>"><?= svg_icon('chart', 20, 20, 'iconX') ?><span class="title">Relatorio NFSe/Boletos</span><span class="title-tooltip">Relatorio</span></a></li>
                            <?php } ?>

                            <!-- Certificado Digital (submenu) -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
                                <li class="submenu <?php if (isset($menuCertificado)) { echo 'active open'; }; ?>">
                                    <a class="tip-bottom btn" title="" href="#"><?= svg_icon('id-card', 20, 20, 'iconX') ?>
                                        <span class="title">Certificado Digital</span>
                                        <span class="title-tooltip">Certificado</span>
                                        <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                                    </a>
                                    <ul style="display: <?php echo isset($menuCertificado) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuCertificadoDashboard)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('certificado') ?>">
                                                <?= svg_icon('shield-check', 20, 20, 'iconX') ?>
                                                <span class="title">Status</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuCertificadoConfig)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('certificado/configurar') ?>">
                                                <?= svg_icon('cog', 20, 20, 'iconX') ?>
                                                <span class="title">Configurar</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuNfseListar)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('nfse') ?>">
                                                <?= svg_icon('receipt', 20, 20, 'iconX') ?>
                                                <span class="title">NFS-e Importadas</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuCertificadoImportar)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('certificado/importar_nfse') ?>">
                                                <?= svg_icon('import', 20, 20, 'iconX') ?>
                                                <span class="title">Importar NFS-e</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>

                            <!-- Impostos Simples (submenu) -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
                                <li class="submenu <?php if (isset($menuImpostos)) { echo 'active open'; }; ?>">
                                    <a class="tip-bottom btn" title="" href="#"><?= svg_icon('money', 20, 20, 'iconX') ?>
                                        <span class="title">Impostos Simples</span>
                                        <span class="title-tooltip">Impostos</span>
                                        <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                                    </a>
                                    <ul style="display: <?php echo isset($menuImpostos) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuImpostosDashboard)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('impostos') ?>">
                                                <?= svg_icon('chart', 20, 20, 'iconX') ?>
                                                <span class="title">Dashboard</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuImpostosConfig)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('impostos/configuracoes') ?>">
                                                <?= svg_icon('cog', 20, 20, 'iconX') ?>
                                                <span class="title">Configuracoes</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuImpostosSimulador)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('impostos/simulador') ?>">
                                                <?= svg_icon('calculator', 20, 20, 'iconX') ?>
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
                            <?= svg_icon('pie-chart', 20, 20, 'iconX') ?>
                            <span class="title">Relatorios</span>
                            <span class="title-tooltip">Relatorios</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo (isset($menuRelatorioAtendimentos) || isset($menuRelTecnicos) || isset($menuRelFinanceiro) || isset($menuRelProdutos) || isset($menuRelClientes) || isset($menuDRE)) ? 'block' : 'none'; ?>;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos')) { ?>
                                <li class="<?php if (isset($menuRelatorioAtendimentos)) { echo 'active'; }; ?>"><a href="<?= site_url('relatorioatendimentos') ?>"><?= svg_icon('time', 20, 20, 'iconX') ?><span class="title">Atendimentos</span><span class="title-tooltip">Atendimentos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
                                <li class="<?php if (isset($menuRelTecnicos)) { echo 'active'; }; ?>"><a href="<?= site_url('relatoriotecnicos') ?>"><?= svg_icon('hard-hat', 20, 20, 'iconX') ?><span class="title">Performance Tecnicos</span><span class="title-tooltip">Performance</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
                                <li class="<?php if (isset($menuRelFinanceiro)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_financeiro') ?>"><?= svg_icon('dollar-circle', 20, 20, 'iconX') ?><span class="title">Financeiro</span><span class="title-tooltip">Financeiro</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
                                <li class="<?php if (isset($menuRelProdutos)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_produtos') ?>"><?= svg_icon('package', 20, 20, 'iconX') ?><span class="title">Produtos</span><span class="title-tooltip">Produtos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto')) { ?>
                                <li class="<?php if (isset($menuRelClientes)) { echo 'active'; }; ?>"><a href="<?= site_url('dashboard/relatorio_clientes') ?>"><?= svg_icon('user-check', 20, 20, 'iconX') ?><span class="title">Clientes</span><span class="title-tooltip">Clientes</span></a></li>
                            <?php } ?>

                            <!-- DRE Contabil (submenu) -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
                                <li class="submenu <?php if (isset($menuDRE)) { echo 'active open'; }; ?>">
                                    <a class="tip-bottom btn" title="" href="#"><?= svg_icon('line-chart-down', 20, 20, 'iconX') ?>
                                        <span class="title">DRE Contabil</span>
                                        <span class="title-tooltip">DRE Contabil</span>
                                        <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                                    </a>
                                    <ul style="display: <?php echo isset($menuDRE) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuDREDashboard)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('dre') ?>">
                                                <?= svg_icon('bar-chart-alt', 20, 20, 'iconX') ?>
                                                <span class="title">Demonstracao</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuDREContas)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('dre/contas') ?>">
                                                <?= svg_icon('list-ul', 20, 20, 'iconX') ?>
                                                <span class="title">Plano de Contas</span>
                                            </a>
                                        </li>
                                        <li class="<?php if (isset($menuDRELancamentos)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('dre/lancamentos') ?>">
                                                <?= svg_icon('book', 20, 20, 'iconX') ?>
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
                            <?= svg_icon('cog', 20, 20, 'iconX') ?>
                            <span class="title">Configuracoes</span>
                            <span class="title-tooltip">Configuracoes</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo (isset($menuUsuarios) || isset($menuPermissoes) || isset($menuUsuariosCliente) || isset($menuArquivos) || isset($menuEmitente) || isset($menuConfigSistema) || isset($menuModulos) || isset($menuBackup) || isset($menuAuditoria) || isset($menuConfiguracoesNotificacoes) || isset($menuConfiguracoesTemplates) || isset($menuConfiguracoesLogs) || isset($menuMigrate) || isset($menuDiagnostico) || isset($menuEmailQueue) || isset($menuEmailConfig) || isset($menuWebhooks) || isset($menuWebhooksDocs) || isset($menuApiDocs) || isset($menuAgenteIA)) ? 'block' : 'none'; ?>;">
                            <!-- Usuarios e Permissoes -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) { ?>
                                <li class="<?php if (isset($menuUsuarios)) { echo 'active'; }; ?>"><a href="<?= site_url('usuarios') ?>"><?= svg_icon('user-circle', 20, 20, 'iconX') ?><span class="title">Usuarios</span><span class="title-tooltip">Usuarios do Sistema</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                                <li class="<?php if (isset($menuPermissoes)) { echo 'active'; }; ?>"><a href="<?= site_url('permissoes') ?>"><?= svg_icon('shield-check', 20, 20, 'iconX') ?><span class="title">Permissoes</span><span class="title-tooltip">Grupos de Permissao</span></a></li>
                            <?php } ?>

                            <!-- Usuarios Cliente (submenu) -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente')) { ?>
                                <li class="submenu <?php if (isset($menuUsuariosCliente)) { echo 'active open'; }; ?>">
                                    <a class="tip-bottom btn" title="" href="#"><?= svg_icon('users', 20, 20, 'iconX') ?>
                                        <span class="title">Usuarios Cliente</span>
                                        <span class="title-tooltip">Portal Cliente</span>
                                        <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                                    </a>
                                    <ul style="display: <?php echo isset($menuUsuariosCliente) ? 'block' : 'none'; ?>;">
                                        <li class="<?php if (isset($menuUsuariosClienteListar)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('usuarioscliente') ?>">
                                                <?= svg_icon('list-ul', 20, 20, 'iconX') ?>
                                                <span class="title">Listar Usuarios</span>
                                            </a>
                                        </li>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuariosCliente')) { ?>
                                        <li class="<?php if (isset($menuUsuariosClienteAdicionar)) { echo 'active'; }; ?>">
                                            <a href="<?= site_url('usuarioscliente/adicionar') ?>">
                                                <?= svg_icon('plus', 20, 20, 'iconX') ?>
                                                <span class="title">Novo Usuario</span>
                                            </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
                                <li class="<?php if (isset($menuArquivos)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('arquivos') ?>"><?= svg_icon('box', 20, 20, 'iconX') ?><span class="title">Arquivos</span><span class="title-tooltip">Arquivos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) { ?>
                                <li class="<?php if (isset($menuEmitente)) { echo 'active'; }; ?>"><a href="<?= site_url('mapos/emitente') ?>"><?= svg_icon('building', 20, 20, 'iconX') ?><span class="title">Emitente</span><span class="title-tooltip">Dados da Empresa</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema')) { ?>
                                <li class="<?php if (isset($menuConfigSistema)) { echo 'active'; }; ?>"><a href="<?= site_url('mapos/configurar') ?>"><?= svg_icon('cog', 20, 20, 'iconX') ?><span class="title">Config. Sistema</span><span class="title-tooltip">Configuracoes do Sistema</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                                <li class="<?php if (isset($menuModulos)) { echo 'active'; }; ?>"><a class="tip-bottom btn" href="<?= site_url('modulos') ?>"><?= svg_icon('extension', 20, 20, 'iconX') ?><span class="title">Modulos</span><span class="title-tooltip">Modulos</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) { ?>
                                <li class="<?php if (isset($menuBackup)) { echo 'active'; }; ?>"><a href="<?= site_url('backup') ?>"><?= svg_icon('database', 20, 20, 'iconX') ?><span class="title">Backup</span><span class="title-tooltip">Backup e Restauracao</span></a></li>
                            <?php } ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cAuditoria')) { ?>
                                <li class="<?php if (isset($menuAuditoria)) { echo 'active'; }; ?>"><a href="<?= site_url('auditoria') ?>"><?= svg_icon('file-find', 20, 20, 'iconX') ?><span class="title">Auditoria</span><span class="title-tooltip">Logs de Auditoria</span></a></li>
                            <?php } ?>

                            <!-- Comunicacao -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) { ?>
                                <li class="menu-divider-sub"><span class="divider-text-sub">Comunicacao</span></li>
                                <li class="<?php if (isset($menuConfiguracoesNotificacoes)) { echo 'active'; }; ?>">
                                    <a href="<?= site_url('notificacoes/configuracoes') ?>">
                                        <?= svg_icon('whatsapp', 20, 20, 'iconX') ?>
                                        <span class="title">Notificacoes</span>
                                        <span class="title-tooltip">Notificacoes</span>
                                    </a>
                                </li>
                                <li class="<?php if (isset($menuConfiguracoesTemplates)) { echo 'active'; }; ?>">
                                    <a href="<?= site_url('notificacoes/templates') ?>">
                                        <?= svg_icon('message-square-dots', 20, 20, 'iconX') ?>
                                        <span class="title">Templates</span>
                                        <span class="title-tooltip">Templates</span>
                                    </a>
                                </li>
                                <li class="<?php if (isset($menuConfiguracoesLogs)) { echo 'active'; }; ?>">
                                    <a href="<?= site_url('notificacoes/logs') ?>">
                                        <?= svg_icon('history', 20, 20, 'iconX') ?>
                                        <span class="title">Historico</span>
                                        <span class="title-tooltip">Historico</span>
                                    </a>
                                </li>
                            <?php } ?>

                            <!-- Administracao -->
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                                <li class="menu-divider-sub"><span class="divider-text-sub">Administracao</span></li>
                                <li class="<?php if (isset($menuMigrate)) { echo 'active'; }; ?>"><a href="<?= site_url('migrate') ?>"><?= svg_icon('database', 20, 20, 'iconX') ?><span class="title">Migracoes DB</span><span class="title-tooltip">Migracoes</span></a></li>
                                <li class="<?php if (isset($menuDiagnostico)) { echo 'active'; }; ?>"><a href="<?= site_url('diagnostico') ?>"><?= svg_icon('bug', 20, 20, 'iconX') ?><span class="title">Diagnostico</span><span class="title-tooltip">Diagnostico do Sistema</span></a></li>
                                <li class="<?php if (isset($menuEmailQueue)) { echo 'active'; }; ?>"><a href="<?= site_url('emails/dashboard') ?>"><?= svg_icon('envelope', 20, 20, 'iconX') ?><span class="title">Fila de Emails</span><span class="title-tooltip">Fila Emails</span></a></li>
                                <li class="<?php if (isset($menuEmailConfig)) { echo 'active'; }; ?>"><a href="<?= site_url('email/configuracoes') ?>"><?= svg_icon('cog', 20, 20, 'iconX') ?><span class="title">Config. Emails</span><span class="title-tooltip">Config Emails</span></a></li>
                                <li class="<?php if (isset($menuWebhooks)) { echo 'active'; }; ?>"><a href="<?= site_url('webhooks') ?>"><?= svg_icon('webhook', 20, 20, 'iconX') ?><span class="title">Webhooks</span><span class="title-tooltip">Webhooks</span></a></li>
                                <li class="<?php if (isset($menuWebhooksDocs)) { echo 'active'; }; ?>"><a href="<?= site_url('webhooks/docs') ?>" target="_blank"><?= svg_icon('book-open', 20, 20, 'iconX') ?><span class="title">Docs Webhooks</span><span class="title-tooltip">Docs Webhooks</span></a></li>
                                <li class="<?php if (isset($menuApiDocs)) { echo 'active'; }; ?>"><a href="<?= site_url('api/docs') ?>"><?= svg_icon('code-alt', 20, 20, 'iconX') ?><span class="title">API v2</span><span class="title-tooltip">API v2</span></a></li>
                                <li class="<?php if (isset($menuAgenteIA)) { echo 'active'; }; ?>"><a href="<?= site_url('agente_ia') ?>"><?= svg_icon('bot', 20, 20, 'iconX') ?><span class="title">Agente IA</span><span class="title-tooltip">Agente IA</span></a></li>
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
                        <?= svg_icon('user', 20, 20, 'iconX') ?>
                        <span class="title">Minha Conta</span>
                        <span class="title-tooltip">Minha Conta</span>
                    </a>
                </li>
                <li>
                    <a class="tip-bottom btn" title="" href="<?= site_url('login/sair'); ?>">
                        <?= svg_icon('log-out', 20, 20, 'iconX') ?>
                        <span class="title">Sair</span>
                        <span class="title-tooltip">Sair</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!--End sidebar-menu-->