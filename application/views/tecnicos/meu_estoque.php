<!-- Meu Estoque - Portal do Técnico -->
<style>
.portal-tecnico-content { margin-top: 15px !important; }
@media (max-width: 768px) { .portal-tecnico-content { margin-top: 10px !important; } }
</style>

<div class="row-fluid portal-tecnico-content">
    <div class="span12">

        <!-- Header -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-package"></i></span>
                <h5>Meu Estoque</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="btn btn-mini btn-info">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Busca -->
                <div class="search-box">
                    <div class="input-prepend">
                        <span class="add-on"><i class="bx bx-search"></i></span>
                        <input type="text" id="searchInput" class="span12" placeholder="Buscar item..."
                               oninput="filtrarEstoque(this.value)">
                    </div>
                </div>

                <!-- Alerta de estoque baixo -->
                <?php
                $estoque_baixo = array_filter($estoque, function($item) {
                    return $item->quantidade <= 2;
                });
                if (!empty($estoque_baixo)): ?>
                <div class="alert alert-warning">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <i class="bx bx-error-circle"></i>
                    <strong>Atenção!</strong> <?php echo count($estoque_baixo); ?> item(s) com estoque baixo
                </div>
                <?php endif; ?>

                <!-- Estoque Atual -->
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="bx bx-package"></i></span>
                        <h5>Estoque Atual</h5>
                        <span class="label label-info"><?php echo count($estoque); ?> itens</span>
                    </div>
                    <div class="widget-content nopadding">
                        <?php if (empty($estoque)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bx bx-package"></i>
                                </div>
                                <h4>Estoque Vazio</h4>
                                <p>Nenhum item em seu veículo</p>
                            </div>
                        <?php else: ?>
                            <table class="table table-bordered table-hover" id="estoqueTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>Produto</th>
                                        <th style="width: 120px; text-align: center;">Código</th>
                                        <th style="width: 100px; text-align: center;">Quantidade</th>
                                        <th style="width: 80px; text-align: center;">Unidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($estoque as $item): ?>
                                    <tr class="estoque-row <?php echo $item->quantidade <= 2 ? 'estoque-baixo' : ($item->quantidade <= 5 ? 'estoque-alerta' : ''); ?>"
                                        data-nome="<?php echo strtolower(htmlspecialchars($item->produto_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8')); ?>">
                                        <td class="text-center">
                                            <?php if ($item->quantidade <= 2): ?>
                                                <i class="bx bx-error-circle" style="color: #f44336;" title="Estoque baixo"></i>
                                            <?php elseif ($item->quantidade <= 5): ?>
                                                <i class="bx bx-error" style="color: #ff9800;" title="Estoque em alerta"></i>
                                            <?php else: ?>
                                                <i class="bx bx-check-circle" style="color: #4caf50;"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item->produto_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></strong>
                                        </td>
                                        <td class="text-center"><?php echo htmlspecialchars($item->codDeBarra ?? '-', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                                        <td class="text-center">
                                            <span class="estoque-badge <?php echo $item->quantidade <= 2 ? 'badge-baixo' : ($item->quantidade <= 5 ? 'badge-alerta' : 'badge-ok'); ?>">
                                                <?php echo $item->quantidade; ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?php echo htmlspecialchars($item->unidade ?? 'UN', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Histórico -->
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="bx bx-history"></i></span>
                        <h5>Histórico (30 dias)</h5>
                    </div>
                    <div class="widget-content">
                        <?php if (empty($historico)): ?>
                            <div class="empty-state">
                                <p>Nenhum movimento no período</p>
                            </div>
                        <?php else: ?>
                            <div class="historico-lista">
                                <?php foreach (array_slice($historico, 0, 10) as $mov): ?>
                                <div class="historico-item">
                                    <div class="historico-icon <?php echo $mov->tipo; ?>">
                                        <?php if ($mov->tipo == 'entrada'): ?>
                                            <i class="bx bx-down-arrow-alt"></i>
                                        <?php else: ?>
                                            <i class="bx bx-up-arrow-alt"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="historico-info">
                                        <h4><?php echo htmlspecialchars($mov->produto_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></h4>
                                        <p>
                                            <?php echo date('d/m/Y H:i', strtotime($mov->data_hora)); ?>
                                            <?php if ($mov->os_id): ?>
                                                | OS #<?php echo $mov->os_id; ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="historico-qtd <?php echo $mov->tipo; ?>">
                                        <?php echo $mov->tipo == 'entrada' ? '+' : '-'; ?><?php echo $mov->quantidade; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
/* Search Box */
.search-box {
    margin-bottom: 20px;
}

.search-box .input-prepend {
    width: 100%;
}

.search-box .add-on {
    background: #f8f9fa;
    border-color: #ddd;
    color: #667eea;
}

.search-box input {
    border-color: #ddd;
}

/* Tabela de Estoque */
.estoque-row.estoque-baixo {
    background-color: #ffebee;
}

.estoque-row.estoque-alerta {
    background-color: #fff3e0;
}

.estoque-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
}

.estoque-badge.badge-baixo {
    background: #ffebee;
    color: #c62828;
}

.estoque-badge.badge-alerta {
    background: #fff3e0;
    color: #ef6c00;
}

.estoque-badge.badge-ok {
    background: #e8f5e9;
    color: #2e7d32;
}

/* Histórico */
.historico-lista {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.historico-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}

.historico-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.historico-icon.entrada {
    background: #e8f5e9;
    color: #4caf50;
}

.historico-icon.saida {
    background: #ffebee;
    color: #f44336;
}

.historico-info {
    flex: 1;
}

.historico-info h4 {
    margin: 0 0 3px 0;
    font-size: 0.95rem;
}

.historico-info p {
    margin: 0;
    font-size: 0.8rem;
    color: #888;
}

.historico-qtd {
    font-weight: 600;
    font-size: 1.1rem;
}

.historico-qtd.entrada {
    color: #4caf50;
}

.historico-qtd.saida {
    color: #f44336;
}

/* Estado Vazio */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #999;
}

.empty-state .empty-icon {
    font-size: 48px;
    color: #e0e0e0;
    margin-bottom: 15px;
}

.empty-state h4 {
    color: #666;
    font-weight: 400;
}

.empty-state p {
    margin: 0;
}

/* Alerta */
.alert-warning {
    background: #fff3e0;
    border-color: #ff9800;
    color: #ef6c00;
}

.alert-warning i {
    margin-right: 5px;
}

/* Responsividade Mobile */
@media (max-width: 768px) {
    .historico-item {
        flex-wrap: wrap;
    }

    .historico-info {
        width: calc(100% - 60px);
    }

    .historico-qtd {
        width: 100%;
        text-align: right;
        margin-top: 5px;
    }
}

@media (max-width: 480px) {
    table td, table th {
        font-size: 12px;
        padding: 8px 5px;
    }

    .estoque-badge {
        padding: 2px 8px;
        font-size: 0.8rem;
    }
}
</style>

<script>
function filtrarEstoque(termo) {
    const rows = document.querySelectorAll('.estoque-row');
    const busca = termo.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    rows.forEach(row => {
        const nome = row.dataset.nome.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        row.style.display = nome.includes(busca) ? '' : 'none';
    });
}
</script>
