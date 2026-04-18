<?php
/**
 * Gerenciamento de Estoque do Técnico
 */
$tecnico = $tecnico ?? null;
$estoque = $estoque ?? [];
$produtos = $produtos ?? [];

if (!$tecnico) {
    echo '<div class="alert alert-error">Técnico não encontrado.</div>';
    return;
}

$id = $tecnico->idUsuarios ?? $tecnico->id;
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-shopping-cart"></i></span>
                <h5>Estoque do Veículo - <?php echo $tecnico->nome; ?></h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos_admin/ver_tecnico/' . $id); ?>" class="btn btn-mini">
                        <i class="icon-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Adicionar Produto -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="icon-plus"></i></span>
                                <h5>Adicionar Produto ao Estoque</h5>
                            </div>
                            <div class="widget-content">
                                <form method="post" action="<?php echo site_url('tecnicos_admin/adicionar_estoque'); ?>" class="form-inline">
                                    <input type="hidden" name="tecnico_id" value="<?php echo $id; ?>">

                                    <label>Produto: </label>
                                    <select name="produto_id" class="input-xlarge" required>
                                        <option value="">Selecione um produto...</option>
                                        <?php foreach ($produtos as $produto): ?>
                                            <option value="<?php echo $produto->idProdutos; ?>">
                                                <?php echo $produto->nome; ?> (<?php echo $produto->unidade ?? 'un'; ?>) - Estoque: <?php echo $produto->estoque ?? 0; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <label>Quantidade: </label>
                                    <input type="number" name="quantidade" class="input-small" min="1" value="1" required>

                                    <button type="submit" class="btn btn-success">
                                        <i class="icon-plus icon-white"></i> Adicionar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Estoque -->
                <div class="row-fluid">
                    <div class="span12">
                        <h5>Produtos em Estoque</h5>
                        <?php if (!empty($estoque)): ?>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Produto</th>
                                        <th>Descrição</th>
                                        <th>Quantidade</th>
                                        <th>Unidade</th>
                                        <th>Atualizado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($estoque as $item): ?>
                                        <tr>
                                            <td><code><?php echo $item->codDeBarra ?? $item->produto_id; ?></code></td>
                                            <td><?php echo $item->produto_nome; ?></td>
                                            <td><?php echo $item->produto_descricao ?? '-'; ?></td>
                                            <td>
                                                <span class="badge badge-info"><?php echo $item->quantidade; ?></span>
                                            </td>
                                            <td><?php echo $item->unidade ?? 'un'; ?></td>
                                            <td><?php echo isset($item->data_atualizacao) ? date('d/m/Y H:i', strtotime($item->data_atualizacao)) : '-'; ?></td>
                                            <td class="button-tip">
                                                <a href="#" class="btn btn-mini btn-warning" title="Ajustar">
                                                    <i class="icon-edit icon-white"></i>
                                                </a>
                                                <a href="#" class="btn btn-mini btn-danger" title="Remover">
                                                    <i class="icon-remove icon-white"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="icon-info-sign"></i> Nenhum produto em estoque para este técnico.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informações -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="alert alert-info">
                            <i class="icon-info-sign"></i> <strong>Dica:</strong>
                            O estoque vinculado ao técnico é descontado automaticamente quando ele registra o uso de material em uma OS.
                            <br>Para reabastecer, utilize o formulário acima.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
