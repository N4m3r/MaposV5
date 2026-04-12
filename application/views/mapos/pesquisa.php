<div class="span12" style="margin-left: 0; margin-top: 0">
    <!-- Formulário de Busca Unificada -->
    <div class="widget-box" style="margin-bottom: 20px">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-search"></i>
            </span>
            <h5>Busca Unificada - Clientes e OS</h5>
        </div>
        <div class="widget-content" style="padding: 20px">
            <form action="<?php echo site_url('mapos/pesquisar') ?>" method="get" class="form-inline">
                <div class="span4" style="margin-left: 0">
                    <input type="text" name="termo" class="span12" placeholder="Buscar por nome, documento, telefone, número da OS..." value="<?php echo $this->input->get('termo') ?>" />
                </div>
                <div class="span2">
                    <select name="tipo" class="span12">
                        <option value="todos" <?php echo $this->input->get('tipo') == 'todos' ? 'selected' : '' ?>>Todos</option>
                        <option value="clientes" <?php echo $this->input->get('tipo') == 'clientes' ? 'selected' : '' ?>>Clientes</option>
                        <option value="os" <?php echo $this->input->get('tipo') == 'os' ? 'selected' : '' ?>>Ordens de Serviço</option>
                        <option value="produtos" <?php echo $this->input->get('tipo') == 'produtos' ? 'selected' : '' ?>>Produtos</option>
                        <option value="servicos" <?php echo $this->input->get('tipo') == 'servicos' ? 'selected' : '' ?>>Serviços</option>
                    </select>
                </div>
                <div class="span2">
                    <button type="submit" class="button btn btn-warning">
                        <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                        <span class="button__text2">Buscar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($this->input->get('termo')): ?>

    <!-- Resultados da Busca -->
    <div class="span12" style="margin-left: 0">

        <!-- Clientes e OS lado a lado quando busca é 'todos' -->
        <?php if ($tipo == 'todos' || $tipo == 'clientes'): ?>
        <!-- Clientes -->
        <div class="span6" style="margin-left: 0">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <h5>Clientes Encontrados (<?php echo count($clientes) ?>)</h5>
                </div>
                <div class="widget-content nopadding">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Documento</th>
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($clientes) > 0): ?>
                                <?php foreach ($clientes as $r): ?>
                                <tr>
                                    <td><?php echo $r->idClientes ?></td>
                                    <td><?php echo $r->nomeCliente ?></td>
                                    <td><?php echo $r->documento ?></td>
                                    <td><?php echo $r->telefone ?: $r->celular ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                                            <a href="<?php echo base_url() ?>index.php/clientes/visualizar/<?php echo $r->idClientes ?>" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show"></i></a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) { ?>
                                            <a href="<?php echo base_url() ?>index.php/clientes/editar/<?php echo $r->idClientes ?>" class="btn-nwe3" title="Editar Cliente"><i class="bx bx-edit"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center">Nenhum cliente encontrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tipo == 'todos' || $tipo == 'os'): ?>
        <!-- OS -->
        <div class="span6" style="<?php echo $tipo == 'os' ? 'margin-left: 0; width: 100%' : '' ?>">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-diagnoses"></i>
                    </span>
                    <h5>Ordens de Serviço Encontradas (<?php echo count($os) ?>)</h5>
                </div>
                <div class="widget-content nopadding">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($os) > 0): ?>
                                <?php foreach ($os as $r):
                                    switch ($r->status) {
                                        case 'Aberto': $cor = '#00cd00'; break;
                                        case 'Em Andamento': $cor = '#436eee'; break;
                                        case 'Orçamento': $cor = '#CDB380'; break;
                                        case 'Negociação': $cor = '#AEB404'; break;
                                        case 'Cancelado': $cor = '#CD0000'; break;
                                        case 'Finalizado': $cor = '#256'; break;
                                        case 'Faturado': $cor = '#B266FF'; break;
                                        case 'Aguardando Peças': $cor = '#FF7F00'; break;
                                        case 'Aprovado': $cor = '#808080'; break;
                                        default: $cor = '#E0E4CC'; break;
                                    }
                                ?>
                                <tr>
                                    <td><?php echo $r->idOs ?></td>
                                    <td><?php echo $r->nomeCliente ?></td>
                                    <td><span class="badge" style="background-color: <?php echo $cor ?>; border-color: <?php echo $cor ?>"><?php echo $r->status ?></span></td>
                                    <td><?php echo date('d/m/Y', strtotime($r->dataInicial)) ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                                            <a href="<?php echo base_url() ?>index.php/os/visualizar/<?php echo $r->idOs ?>" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show"></i></a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
                                            <a href="<?php echo base_url() ?>index.php/os/editar/<?php echo $r->idOs ?>" class="btn-nwe3" title="Editar OS"><i class="bx bx-edit"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center">Nenhuma OS encontrada</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tipo == 'produtos'): ?>
        <!-- Produtos -->
        <div class="span12" style="margin-left: 0">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-shopping-bag"></i>
                    </span>
                    <h5>Produtos Encontrados (<?php echo count($produtos) ?>)</h5>
                </div>
                <div class="widget-content nopadding">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Descrição</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($produtos) > 0): ?>
                                <?php foreach ($produtos as $r): ?>
                                <tr>
                                    <td><?php echo $r->idProdutos ?></td>
                                    <td><?php echo $r->codDeBarra ?></td>
                                    <td><?php echo $r->descricao ?></td>
                                    <td>R$ <?php echo number_format($r->precoVenda, 2, ',', '.') ?></td>
                                    <td><?php echo $r->estoque ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                                            <a href="<?php echo base_url() ?>index.php/produtos/visualizar/<?php echo $r->idProdutos ?>" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show"></i></a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) { ?>
                                            <a href="<?php echo base_url() ?>index.php/produtos/editar/<?php echo $r->idProdutos ?>" class="btn-nwe3" title="Editar Produto"><i class="bx bx-edit"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center">Nenhum produto encontrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tipo == 'servicos'): ?>
        <!-- Serviços -->
        <div class="span12" style="margin-left: 0">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-wrench"></i>
                    </span>
                    <h5>Serviços Encontrados (<?php echo count($servicos) ?>)</h5>
                </div>
                <div class="widget-content nopadding">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($servicos) > 0): ?>
                                <?php foreach ($servicos as $r): ?>
                                <tr>
                                    <td><?php echo $r->idServicos ?></td>
                                    <td><?php echo $r->nome ?></td>
                                    <td>R$ <?php echo number_format($r->preco, 2, ',', '.') ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eServico')) { ?>
                                            <a href="<?php echo base_url() ?>index.php/servicos/editar/<?php echo $r->idServicos ?>" class="btn-nwe3" title="Editar Serviço"><i class="bx bx-edit"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center">Nenhum serviço encontrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Resumo quando busca é 'todos' -->
        <?php if ($tipo == 'todos'): ?>
        <div class="span12" style="margin-left: 0; margin-top: 20px">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-chart-bar"></i>
                    </span>
                    <h5>Resumo da Busca</h5>
                </div>
                <div class="widget-content" style="padding: 15px">
                    <div class="span3" style="text-align: center">
                        <h3><?php echo count($clientes) ?></h3>
                        <p>Clientes</p>
                        <a href="?termo=<?php echo urlencode($termo) ?>&tipo=clientes" class="btn btn-small btn-info">Ver apenas clientes</a>
                    </div>
                    <div class="span3" style="text-align: center">
                        <h3><?php echo count($os) ?></h3>
                        <p>Ordens de Serviço</p>
                        <a href="?termo=<?php echo urlencode($termo) ?>&tipo=os" class="btn btn-small btn-info">Ver apenas OS</a>
                    </div>
                    <div class="span3" style="text-align: center">
                        <h3><?php echo count($produtos) ?></h3>
                        <p>Produtos</p>
                        <a href="?termo=<?php echo urlencode($termo) ?>&tipo=produtos" class="btn btn-small btn-info">Ver apenas produtos</a>
                    </div>
                    <div class="span3" style="text-align: center">
                        <h3><?php echo count($servicos) ?></h3>
                        <p>Serviços</p>
                        <a href="?termo=<?php echo urlencode($termo) ?>&tipo=servicos" class="btn btn-small btn-info">Ver apenas serviços</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <?php else: ?>

    <!-- Mensagem quando não há termo de busca -->
    <div class="span12" style="margin-left: 0; text-align: center; padding: 50px">
        <div class="alert alert-info">
            <h4><i class="fas fa-info-circle"></i> Digite um termo de busca</h4>
            <p>Utilize o campo acima para buscar por <strong>Clientes</strong>, <strong>Ordens de Serviço</strong>, <strong>Produtos</strong> ou <strong>Serviços</strong>.</p>
            <p>Você pode buscar por:</p>
            <ul style="list-style: none; margin-top: 15px">
                <li><strong>Clientes:</strong> Nome, Documento (CPF/CNPJ), Telefone, Celular ou Email</li>
                <li><strong>OS:</strong> Número da OS, Nome do cliente, Descrição do produto, Defeito ou Status</li>
                <li><strong>Produtos:</strong> Código de barras ou Descrição</li>
                <li><strong>Serviços:</strong> Nome do serviço</li>
            </ul>
        </div>
    </div>

    <?php endif; ?>
</div>
