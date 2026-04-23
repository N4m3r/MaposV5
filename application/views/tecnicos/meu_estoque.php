<!-- Meu Estoque - Portal do Técnico (Design Moderno) -->

<style>
    /* Ajustes específicos para esta página */
    .estoque-welcome {
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        border: none;
        border-radius: 12px;
        margin-bottom: 24px;
    }

    .estoque-welcome-body {
        padding: 24px;
        color: #fff;
    }

    /* Cards */
    .estoque-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .estoque-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .estoque-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .estoque-card-title i {
        color: #9b59b6;
        font-size: 20px;
    }

    .estoque-card-body {
        padding: 20px;
    }

    /* Busca */
    .search-box-wrapper {
        position: relative;
        margin-bottom: 20px;
    }

    .search-box-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        font-size: 20px;
    }

    .search-box-wrapper input {
        width: 100%;
        padding: 12px 16px 12px 48px;
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        font-size: 15px;
        outline: none;
        transition: all 0.3s;
        background: #fff;
    }

    .search-box-wrapper input:focus {
        border-color: #9b59b6;
    }

    /* Alerta */
    .alert-estoque {
        background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
        border-left: 4px solid #f39c12;
        border-radius: 12px;
        margin-bottom: 20px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-icon {
        width: 40px;
        height: 40px;
        background: #f39c12;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .alert-content {
        flex: 1;
    }

    .alert-title {
        font-weight: 600;
        color: #856404;
        font-size: 15px;
    }

    .alert-text {
        color: #856404;
        font-size: 13px;
    }

    /* Lista de Estoque */
    .estoque-lista {
        display: flex;
        flex-direction: column;
    }

    .estoque-item {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .estoque-item:hover {
        background: #f8f9fa;
    }

    .estoque-item:last-child {
        border-bottom: none;
    }

    .estoque-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        font-size: 20px;
    }

    .estoque-icon.baixo {
        background: #ffebee;
        color: #e74c3c;
    }

    .estoque-icon.alerta {
        background: #fff3e0;
        color: #f39c12;
    }

    .estoque-icon.ok {
        background: #e8f5e9;
        color: #27ae60;
    }

    .estoque-info {
        flex: 1;
        min-width: 0;
    }

    .estoque-nome {
        font-weight: 600;
        color: #333;
        font-size: 15px;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .estoque-meta {
        font-size: 13px;
        color: #888;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .estoque-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .estoque-qtd {
        text-align: right;
    }

    .estoque-qtd-numero {
        font-size: 24px;
        font-weight: 700;
    }

    .estoque-qtd-numero.baixo { color: #e74c3c; }
    .estoque-qtd-numero.alerta { color: #f39c12; }
    .estoque-qtd-numero.ok { color: #27ae60; }

    .estoque-qtd-label {
        font-size: 11px;
        color: #888;
        text-transform: uppercase;
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
        padding: 14px 16px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        transition: all 0.2s;
    }

    .historico-item:hover {
        background: #e8f4f8;
        transform: translateX(4px);
    }

    .historico-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        font-size: 20px;
    }

    .historico-icon.entrada {
        background: #e8f5e9;
        color: #27ae60;
    }

    .historico-icon.saida {
        background: #ffebee;
        color: #e74c3c;
    }

    .historico-info {
        flex: 1;
        min-width: 0;
    }

    .historico-produto {
        font-weight: 600;
        color: #333;
        font-size: 14px;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .historico-data {
        font-size: 12px;
        color: #888;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .historico-os {
        background: #e3f2fd;
        color: #1976d2;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
    }

    .historico-qtd {
        font-weight: 700;
        font-size: 18px;
    }

    .historico-qtd.entrada {
        color: #27ae60;
    }

    .historico-qtd.saida {
        color: #e74c3c;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #888;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: #f0f0f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
        color: #bbb;
    }

    .empty-state h4 {
        margin: 0 0 8px 0;
        color: #666;
        font-weight: 500;
    }

    .empty-state p {
        margin: 0;
        font-size: 14px;
    }

    /* Badge */
    .badge-count {
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* DARK MODE */
    body[data-theme="dark"] .estoque-card,
    body[data-theme="dark"] .search-box-wrapper input {
        background: #1a1d29;
        border-color: #2d3347;
    }

    body[data-theme="dark"] .search-box-wrapper input:focus {
        border-color: #9b59b6;
    }

    body[data-theme="dark"] .estoque-card-title,
    body[data-theme="dark"] .estoque-nome,
    body[data-theme="dark"] .historico-produto {
        color: #e2e8f0;
    }

    body[data-theme="dark"] .estoque-card-header,
    body[data-theme="dark"] .estoque-item {
        border-color: #2d3347;
    }

    body[data-theme="dark"] .estoque-item:hover,
    body[data-theme="dark"] .historico-item {
        background: #252a3a;
        border-color: #2d3347;
    }

    body[data-theme="dark"] .historico-item:hover {
        background: #2d3347;
    }

    body[data-theme="dark"] .estoque-meta,
    body[data-theme="dark"] .historico-data,
    body[data-theme="dark"] .estoque-qtd-label {
        color: #a0aec0;
    }

    body[data-theme="dark"] .empty-state-icon {
        background: #252a3a;
        color: #4a5568;
    }

    body[data-theme="dark"] .empty-state h4 {
        color: #a0aec0;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .estoque-item {
            flex-wrap: wrap;
            padding: 12px 16px;
        }

        .estoque-qtd {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            margin-left: 60px;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
        }

        body[data-theme="dark"] .estoque-qtd {
            border-color: #2d3347;
        }

        .estoque-qtd-numero {
            font-size: 20px !important;
        }

        .historico-item {
            padding: 12px;
        }

        .historico-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
    }
</style>

<!-- Header -->
<div class="estoque-welcome">
    <div class="estoque-welcome-body">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    <i class='bx bx-package'></i>
                </div>
                <div>
                    <h2 style="margin: 0 0 4px 0; font-size: 22px; font-weight: 600;">Meu Estoque</h2>
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">
                        <?php echo count($estoque); ?> item(s) em seu veículo
                    </p>
                </div>
            </div>

            <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="tec-action-btn" style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px;">
                <i class='bx bx-arrow-back'></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alerta de estoque baixo -->
<?php
$estoque_baixo = array_filter($estoque, function($item) {
    return $item->quantidade <= 2;
});
if (!empty($estoque_baixo)): ?>
<div class="alert-estoque">
    <div class="alert-icon">
        <i class='bx bx-error-circle'></i>
    </div>
    <div class="alert-content">
        <div class="alert-title">Atenção!</div>
        <div class="alert-text"><?php echo count($estoque_baixo); ?> item(s) com estoque baixo</div>
    </div>
</div>
<?php endif; ?>

<!-- Busca -->
<div class="estoque-card">
    <div class="estoque-card-body">
        <div class="search-box-wrapper">
            <i class='bx bx-search'></i>
            <input type="text" id="searchInput" placeholder="Buscar item no estoque..." oninput="filtrarEstoque(this.value)">
        </div>
    </div>
</div>

<!-- Estoque Atual -->
<div class="estoque-card">
    <div class="estoque-card-header">
        <div class="estoque-card-title">
            <i class='bx bx-package'></i> Estoque Atual
        </div>
        <span class="badge-count"><?php echo count($estoque); ?> itens</span>
    </div>

    <div class="estoque-card-body" style="padding: 0;">
        <?php if (empty($estoque)): ?
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class='bx bx-package'></i>
                </div>
                <h4>Estoque Vazio</h4>
                <p>Nenhum item em seu veículo</p>
            </div>
        <?php else: ?
            <div class="estoque-lista">
                <?php foreach ($estoque as $item):
                    $statusClass = $item->quantidade <= 2 ? 'baixo' : ($item->quantidade <= 5 ? 'alerta' : 'ok');
                    $statusColor = $item->quantidade <= 2 ? '#e74c3c' : ($item->quantidade <= 5 ? '#f39c12' : '#27ae60');
                ?>
                <div class="estoque-item estoque-row" data-nome="<?php echo strtolower(htmlspecialchars($item->produto_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8')); ?>">

                    <div class="estoque-icon <?php echo $statusClass; ?>">
                        <?php if ($item->quantidade <= 2): ?>
                            <i class='bx bx-error-circle'></i>
                        <?php elseif ($item->quantidade <= 5): ?>
                            <i class='bx bx-error'></i>
                        <?php else: ?>
                            <i class='bx bx-check-circle'></i>
                        <?php endif; ?>
                    </div>

                    <div class="estoque-info">
                        <div class="estoque-nome">
                            <?php echo htmlspecialchars($item->produto_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </div>
                        <div class="estoque-meta">
                            <span><i class='bx bx-barcode'></i> <?php echo htmlspecialchars($item->codDeBarra ?? '-', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                            <span><i class='bx bx-ruler'></i> <?php echo htmlspecialchars($item->unidade ?? 'UN', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                        </div>
                    </div>

                    <div class="estoque-qtd">
                        <div class="estoque-qtd-numero <?php echo $statusClass; ?>">
                            <?php echo $item->quantidade; ?
                        </div>
                        <div class="estoque-qtd-label">unidades</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Histórico -->
<div class="estoque-card">
    <div class="estoque-card-header">
        <div class="estoque-card-title">
            <i class='bx bx-history'></i> Histórico (30 dias)
        </div>
    </div>

    <div class="estoque-card-body">
        <?php if (empty($historico)): ?
            <div class="empty-state" style="padding: 40px 20px;">
                <i class='bx bx-history' style="font-size: 32px; margin-bottom: 12px; opacity: 0.5;"></i>
                <p>Nenhum movimento no período</p>
            </div>
        <?php else: ?
            <div class="historico-lista">
                <?php foreach (array_slice($historico, 0, 10) as $mov):
                    $isEntrada = $mov->tipo == 'entrada';
                ?>
                <div class="historico-item">
                    <div class="historico-icon <?php echo $mov->tipo; ?>">
                        <?php if ($isEntrada): ?>
                            <i class='bx bx-down-arrow-alt'></i>
                        <?php else: ?>
                            <i class='bx bx-up-arrow-alt'></i>
                        <?php endif; ?>
                    </div>

                    <div class="historico-info">
                        <div class="historico-produto">
                            <?php echo htmlspecialchars($mov->produto_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </div>
                        <div class="historico-data">
                            <span><i class='bx bx-calendar' style="margin-right: 4px;"></i><?php echo date('d/m/Y H:i', strtotime($mov->data_hora)); ?></span>
                            <?php if ($mov->os_id): ?>
                                <span class="historico-os">OS #<?php echo $mov->os_id; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="historico-qtd <?php echo $mov->tipo; ?>">
                        <?php echo $isEntrada ? '+' : '-'; ?><?php echo $mov->quantidade; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filtrarEstoque(termo) {
    const rows = document.querySelectorAll('.estoque-row');
    const busca = termo.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');

    rows.forEach(row => {
        const nome = row.dataset.nome.normalize('NFD').replace(/[̀-ͯ]/g, '');
        row.style.display = nome.includes(busca) ? '' : 'none';
    });
}
</script>
