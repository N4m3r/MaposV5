<?php
/**
 * Catálogo de Serviços - Usa tabela 'servicos' do sistema
 */
$servicos = $servicos ?? [];
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-wrench"></i></span>
                <h5>Catálogo de Serviços</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('servicos/adicionar'); ?>" class="btn btn-mini btn-success">
                        <i class="icon-plus icon-white"></i> Novo Serviço
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($servicos)): ?>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Código</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th style="width: 120px;">Preço</th>
                                <th style="width: 100px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicos as $servico): ?>
                                <tr>
                                    <td><code>#<?php echo $servico->idServicos; ?></code></td>
                                    <td><?php echo $servico->nome; ?></td>
                                    <td><?php echo $servico->descricao ?: '-'; ?></td>
                                    <td>R$ <?php echo number_format($servico->preco, 2, ',', '.'); ?></td>
                                    <td class="button-tip">
                                        <a href="<?php echo site_url('servicos/editar/' . $servico->idServicos); ?>"
                                           class="btn btn-mini btn-warning" title="Editar">
                                            <i class="icon-edit icon-white"></i>
                                        </a>
                                        <a href="<?php echo site_url('servicos/visualizar/' . $servico->idServicos); ?>"
                                           class="btn btn-mini btn-info" title="Visualizar">
                                            <i class="icon-eye-open icon-white"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="icon-info-sign"></i> Nenhum serviço cadastrado.
                        <a href="<?php echo site_url('servicos/adicionar'); ?>" class="btn btn-small btn-success">
                            <i class="icon-plus icon-white"></i> Cadastrar primeiro serviço
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
