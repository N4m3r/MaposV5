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
                <?= svg_icon('chevron-right', 18, 18, 'iconX open-2') ?>
                <?= svg_icon('chevron-left', 18, 18, 'iconX close-2') ?>
            </div>
        </div>
    </a>
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
        <div class="menu">

            <ul class="menu-links" style="position: relative;">
                <li class="<?php if (isset($menuPainel)) {
                    echo 'active';
                }; ?>">
                    <a class="tip-bottom btn" title="" href="<?= base_url() ?>"><?= svg_icon('home', 20, 20, 'iconX') ?>
                        <span class="title nav-title">Home</span>
                        <span class="title-tooltip">Início</span>
                    </a>
                </li>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDashboard') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="<?php if (isset($menuDashboard)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('dashboard') ?>"><?= svg_icon('dashboard', 20, 20, 'iconX') ?>
                            <span class="title">Dashboard</span>
                            <span class="title-tooltip">Dashboard</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioCompleto') ||
                          $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="submenu <?php if (isset($menuRelatorios)) {
                        echo 'active open';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="#"><?= svg_icon('line-chart-down', 20, 20, 'iconX') ?>
                            <span class="title">Relatórios</span>
                            <span class="title-tooltip">Relatórios</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo isset($menuRelatorios) ? 'block' : 'none'; ?>;">
                            <li class="<?php if (isset($menuRelAtendimentos)) {
                                echo 'active';
                            }; ?>">
                                <a href="<?= site_url('dashboard/relatorio_atendimentos') ?>">
                                    <?= svg_icon('timer', 20, 20, 'iconX') ?>
                                    <span class="title">Atendimentos</span>
                                </a>
                            </li>
                            <li class="<?php if (isset($menuRelFinanceiro)) {
                                echo 'active';
                            }; ?>">
                                <a href="<?= site_url('dashboard/relatorio_financeiro') ?>">
                                    <?= svg_icon('dollar-circle', 20, 20, 'iconX') ?>
                                    <span class="title">Financeiro</span>
                                </a>
                            </li>
                            <li class="<?php if (isset($menuRelProdutos)) {
                                echo 'active';
                            }; ?>">
                                <a href="<?= site_url('dashboard/relatorio_produtos') ?>">
                                    <?= svg_icon('package', 20, 20, 'iconX') ?>
                                    <span class="title">Produtos</span>
                                </a>
                            </li>
                            <li class="<?php if (isset($menuRelClientes)) {
                                echo 'active';
                            }; ?>">
                                <a href="<?= site_url('dashboard/relatorio_clientes') ?>">
                                    <?= svg_icon('user-check', 20, 20, 'iconX') ?>
                                    <span class="title">Clientes</span>
                                </a>
                            </li>
                            <li class="<?php if (isset($menuRelTecnicos)) {
                                echo 'active';
                            }; ?>">
                                <a href="<?= site_url('relatoriotecnicos') ?>">
                                    <?= svg_icon('hard-hat', 20, 20, 'iconX') ?>
                                    <span class="title">Performance Técnicos</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                    <li class="<?php if (isset($menuClientes)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('clientes') ?>"><?= svg_icon('user', 20, 20, 'iconX') ?>
                            <span class="title">Cliente / Fornecedor</span>
                            <span class="title-tooltip">Clientes</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                    <li class="<?php if (isset($menuProdutos)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('produtos') ?>"><?= svg_icon('basket', 20, 20, 'iconX') ?>
                            <span class="title">Produtos</span>
                            <span class="title-tooltip">Produtos</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
                    <li class="<?php if (isset($menuServicos)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('servicos') ?>"><?= svg_icon('wrench', 20, 20, 'iconX') ?>
                            <span class="title">Serviços</span>
                            <span class="title-tooltip">Serviços</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
                    <li class="<?php if (isset($menuVendas)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('vendas') ?>"><?= svg_icon('cart', 20, 20, 'iconX') ?></span>
                            <span class="title">Vendas</span>
                            <span class="title-tooltip">Vendas</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                    <li class="<?php if (isset($menuOs)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('os') ?>"><?= svg_icon('file', 20, 20, 'iconX') ?>
                            <span class="title">Ordens de Serviço</span>
                            <span class="title-tooltip">Ordens</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                    <li class="<?php if (isset($menuKanban)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('kanban') ?>"><?= svg_icon('columns', 20, 20, 'iconX') ?>
                            <span class="title">Kanban Board</span>
                            <span class="title-tooltip">Kanban</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
                    <li class="<?php if (isset($menuAtribuir)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('os/atribuir') ?>"><?= svg_icon('user-plus', 20, 20, 'iconX') ?>
                            <span class="title">Atribuir Técnico</span>
                            <span class="title-tooltip">Atribuir Téc.</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vGarantia')) { ?>
                    <li class="<?php if (isset($menuGarantia)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('garantias') ?>"><?= svg_icon('receipt', 20, 20, 'iconX') ?>
                            <span class="title">Termos de Garantias</span>
                            <span class="title-tooltip">Garantias</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
                    <li class="<?php if (isset($menuArquivos)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('arquivos') ?>"><?= svg_icon('box', 20, 20, 'iconX') ?>
                            <span class="title">Arquivos</span>
                            <span class="title-tooltip">Arquivos</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                    <li class="<?php if (isset($menuLancamentos)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('financeiro/lancamentos') ?>"><?= svg_icon('bar-chart-alt', 20, 20, 'iconX') ?>
                            <span class="title">Lançamentos</span>
                            <span class="title-tooltip">Lançamentos</span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) { ?>
                    <li class="<?php if (isset($menuCobrancas)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('cobrancas/cobrancas') ?>"><?= svg_icon('dollar-circle', 20, 20, 'iconX') ?>
                            <span class="title">Cobranças</span>
                            <span class="title-tooltip">Cobranças</span>
                        </a>
                    </li>
                <?php } ?>

                <!-- RELATÓRIO DE ATENDIMENTOS -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorioAtendimentos')) { ?>
                    <li class="<?php if (isset($menuRelatorioAtendimentos)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('relatorioatendimentos') ?>"><?= svg_icon('timer', 20, 20, 'iconX') ?>
                            <span class="title">Atendimentos</span>
                            <span class="title-tooltip">Atendimentos</span>
                        </a>
                    </li>
                <?php } ?>

                <!-- WEBHOOKS -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vWebhooks')) { ?>
                    <li class="<?php if (isset($menuWebhooks)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('webhooks') ?>"><?= svg_icon('webhook', 20, 20, 'iconX') ?>
                            <span class="title">Webhooks</span>
                            <span class="title-tooltip">Webhooks</span>
                        </a>
                    </li>
                <?php } ?>

                <!-- NFS-e (NOTAS FISCAIS) -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
                    <li class="<?php if (isset($menuNfseListar)) {
                        echo 'active';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="<?= site_url('nfse') ?>"><?= svg_icon('receipt', 20, 20, 'iconX') ?>
                            <span class="title">NFS-e</span>
                            <span class="title-tooltip">NFS-e</span>
                        </a>
                    </li>
                <?php } ?>

                <!-- CERTIFICADO DIGITAL -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
                    <li class="submenu <?php if (isset($menuCertificado)) {
                        echo 'active open';
                    }; ?>">
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

                <!-- IMPOSTOS SIMPLES -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
                    <li class="submenu <?php if (isset($menuImpostos)) {
                        echo 'active open';
                    }; ?>">
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
                                    <span class="title">Configurações</span>
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

                <!-- DRE CONTÁBIL -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
                    <li class="submenu <?php if (isset($menuDRE)) {
                        echo 'active open';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="#"><?= svg_icon('line-chart-down', 20, 20, 'iconX') ?>
                            <span class="title">DRE Contábil</span>
                            <span class="title-tooltip">DRE Contábil</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo isset($menuDRE) ? 'block' : 'none'; ?>;">
                            <li class="<?php if (isset($menuDREDashboard)) { echo 'active'; }; ?>">
                                <a href="<?= site_url('dre') ?>">
                                    <?= svg_icon('bar-chart-alt', 20, 20, 'iconX') ?>
                                    <span class="title">Demonstração</span>
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
                                    <span class="title">Lançamentos</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vDRE')) { ?>
                    <li class="submenu <?php if (isset($menuDRE)) {
                        echo 'active open';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="#"><?= svg_icon('line-chart-down', 20, 20, 'iconX') ?>
                            <span class="title">DRE Contábil</span>
                            <span class="title-tooltip">DRE Contábil</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo isset($menuDRE) ? 'block' : 'none'; ?>;">
                            <li class="<?php if (isset($menuDREDashboard)) { echo 'active'; }; ?>">
                                <a href="<?= site_url('dre') ?>">
                                    <?= svg_icon('bar-chart-alt', 20, 20, 'iconX') ?>
                                    <span class="title">Demonstração</span>
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
                                    <span class="title">Lançamentos</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
                    <li class="submenu <?php if (isset($menuCertificado)) {
                        echo 'active open';
                    }; ?>">
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

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vImpostos')) { ?>
                    <li class="submenu <?php if (isset($menuImpostos)) {
                        echo 'active open';
                    }; ?>">
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
                                    <span class="title">Configurações</span>
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

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vUsuariosCliente')) { ?>
                    <li class="submenu <?php if (isset($menuUsuariosCliente)) {
                        echo 'active open';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="#"><?= svg_icon('user-check', 20, 20, 'iconX') ?>
                            <span class="title">Usuários Cliente</span>
                            <span class="title-tooltip">Portal Cliente</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo isset($menuUsuariosCliente) ? 'block' : 'none'; ?>;">
                            <li class="<?php if (isset($menuUsuariosClienteListar)) { echo 'active'; }; ?>">
                                <a href="<?= site_url('usuarioscliente') ?>">
                                    <?= svg_icon('list-ul', 20, 20, 'iconX') ?>
                                    <span class="title">Listar Usuários</span>
                                </a>
                            </li>
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuariosCliente')) { ?>
                            <li class="<?php if (isset($menuUsuariosClienteAdicionar)) { echo 'active'; }; ?>">
                                <a href="<?= site_url('usuarioscliente/adicionar') ?>">
                                    <?= svg_icon('plus', 20, 20, 'iconX') ?>
                                    <span class="title">Novo Usuário</span>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                    <li class="submenu <?php if (isset($menuFerramentasV5)) {
                        echo 'active open';
                    }; ?>">
                        <a class="tip-bottom btn" title="" href="#"><?= svg_icon('rocket', 20, 20, 'iconX') ?>
                            <span class="title">Ferramentas V5</span>
                            <span class="title-tooltip">Ferramentas</span>
                            <?= svg_icon('chevron-down', 18, 18, 'arrow') ?>
                        </a>
                        <ul style="display: <?php echo isset($menuFerramentasV5) ? 'block' : 'none'; ?>;">
                            <li class="<?php if (isset($menuEmailQueue)) {
                                echo 'active';
                            }; ?>">
                                <a href="<?= site_url('emails/dashboard') ?>">
                                    <?= svg_icon('envelope', 20, 20, 'iconX') ?>
                                    <span class="title">Fila de Emails</span>
                                </a>
                            </li>
                            <li class="<?php if (isset($menuWebhooks)) {
                                echo 'active';
                            }; ?>">
                                <a href="<?= site_url('webhooks') ?>">
                                    <?= svg_icon('webhook', 20, 20, 'iconX') ?>
                                    <span class="title">Webhooks</span>
                                </a>
                            </li>
                            <li class="<?php if (isset($menuWebhooksDocs)) {
                                echo 'active';
                            }; ?>">
                                <a href="<?= site_url('webhooks/docs') ?>" target="_blank">
                                    <?= svg_icon('book-open', 20, 20, 'iconX') ?>
                                    <span class="title">Docs Webhooks</span>
                                </a>
                            </li>
                            <li class="<?php if (isset($menuApiDocs)) {
                                echo 'active';
                            }; ?>">
                                <a href="<?= site_url('api/docs') ?>">
                                    <?= svg_icon('code-alt', 20, 20, 'iconX') ?>
                                    <span class="title">API v2</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="botton-content">
            <li class="">
                <a class="tip-bottom btn" title="" href="<?= site_url('login/sair'); ?>">
                    <?= svg_icon('log-out', 20, 20, 'iconX') ?>
                    <span class="title">Sair</span>
                    <span class="title-tooltip">Sair</span>
                </a>
            </li>
        </div>
    </div>
</nav>
<!--End sidebar-menu-->
