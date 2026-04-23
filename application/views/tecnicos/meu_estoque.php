<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Meu Estoque - Portal do Técnico (Design Moderno) -->

<style>
    /* Ajustes adicionais para dark mode */
    body[data-theme="dark"] .welcome-card {
        background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%) !important;
    }
    body[data-theme="dark"] .empty-state {
        color: #888 !important;
    }
    body[data-theme="dark"] .empty-state i {
        opacity: 0.3 !important;
    }
    body[data-theme="dark"] .estoque-item {
        background: #2d3748 !important;
        border-color: #4a5568 !important;
    }
    body[data-theme="dark"] .estoque-item .produto-nome {
        color: #e2e8f0 !important;
    }
    body[data-theme="dark"] .estoque-item .produto-info {
        color: #a0aec0 !important;
    }
    body[data-theme="dark"] .historico-item {
        background: #2d3748 !important;
        border-color: #4a5568 !important;
    }
    body[data-theme="dark"] .historico-item .info-text {
        color: #e2e8f0 !important;
    }
    body[data-theme="dark"] .historico-item .info-date {
        color: #a0aec0 !important;
    }
    body[data-theme="dark"] .search-box input {
        background: #2d3748 !important;
        border-color: #4a5568 !important;
        color: #e2e8f0 !important;
    }
    body[data-theme="dark"] .search-box .search-icon {
        color: #a0aec0 !important;
    }
</style>

<!-- Header com título -->
<div class="tec-card welcome-card" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); border: none;">
    <div class="tec-card-body" style="color: #fff;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    <i class='bx bx-package'></i>
                </div>
                <div>
                    <h2 style="margin: 0 0 4px 0; font-size: 22px;">Meu Estoque</h2>
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">
                        <?php echo count($estoque); ?> item(s) em seu veículo
                    </p>
                </div>
            </div>
            <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="tec-btn" style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
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
<div class="tec-card" style="border-left: 4px solid #f39c12; background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);">
    <div class="tec-card-body" style="display: flex; align-items: center; gap: 12px; padding: 16px 20px;">
        <div style="width: 40px; height: 40px; background: #f39c12; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
            <i class='bx bx-error-circle'></i>
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 600; color: #856404; font-size: 15px;">Atenção!</div>
            <div style="color: #856404; font-size: 13px;"><?php echo count($estoque_baixo); ?> item(s) com estoque baixo</div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Busca -->
<div class="tec-card">
    <div class="tec-card-body" style="padding: 16px 20px;">
        <div class="search-box" style="position: relative;">
            <i class='bx bx-search search-icon' style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #888; font-size: 20px;"></i>
            <input type="text" id="searchInput" placeholder="Buscar item no estoque..."
                   oninput="filtrarEstoque(this.value)"
                   style="width: 100%; padding: 12px 16px 12px 48px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 15px; outline: none; transition: all 0.3s;">
        </div>
    </div>
</div>

<!-- Estoque Atual -->
<div class="tec-card">
    <div class="tec-card-header">
        <div class="tec-card-title">
            <i class='bx bx-package'></i> Estoque Atual
        </div>
        <span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
            <?php echo count($estoque); ?> itens
        </span>
    </div>
    <div class="tec-card-body" style="padding: 0;">
        <?php if (empty($estoque)): ?>
            <div class="empty-state" style="text-align: center; padding: 60px 20px; color: #888;">
                <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 36px; color: #bbb;">
                    <i class='bx bx-package'></i>
                </div>
                <h4 style="margin: 0 0 8px 0; color: #666; font-weight: 500;">Estoque Vazio</h4>
                <p style="margin: 0; font-size: 14px;">Nenhum item em seu veículo</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column;">
                <?php foreach ($estoque as $item):
                    $statusClass = $item->quantidade <= 2 ? 'baixo' : ($item->quantidade <= 5 ? 'alerta' : 'ok');
                    $statusColor = $item->quantidade <= 2 ? '#e74c3c' : ($item->quantidade <= 5 ? '#f39c12' : '#27ae60');
                    $statusBg = $item->quantidade <= 2 ? '#ffebee' : ($item->quantidade <= 5 ? '#fff3e0' : '#e8f5e9');
                ?>
                <div class="estoque-row" data-nome="<?php echo strtolower(htmlspecialchars($item->produto_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8')); ?>"
                     style="display: flex; align-items: center; padding: 16px 20px; border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">

                    <!-- Ícone de Status -->
                    <div style="width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px; font-size: 20px; background: <?php echo $statusBg; ?>; color: <?php echo $statusColor; ?>;">
                        <?php if ($item->quantidade <= 2): ?>
                            <i class='bx bx-error-circle'></i>
                        <?php elseif ($item->quantidade <= 5): ?>
                            <i class='bx bx-error'></i>
                        <?php else: ?>
                            <i class='bx bx-check-circle'></i>
                        <?php endif; ?>
                    </div>

                    <!-- Info do Produto -->
                    <div style="flex: 1; min-width: 0;">
                        <div class="produto-nome" style="font-weight: 600; color: #333; font-size: 15px; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($item->produto_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </div>
                        <div class="produto-info" style="font-size: 13px; color: #888; display: flex; gap: 12px; flex-wrap: wrap;">
                            <span><i class='bx bx-barcode' style="margin-right: 4px;"></i><?php echo htmlspecialchars($item->codDeBarra ?? '-', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                            <span><i class='bx bx-ruler' style="margin-right: 4px;"></i><?php echo htmlspecialchars($item->unidade ?? 'UN', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                        </div>
                    </div>

                    <!-- Quantidade -->
                    <div style="text-align: right; margin-left: 12px;">
                        <div style="font-size: 24px; font-weight: 700; color: <?php echo $statusColor; ?>;">
                            <?php echo $item->quantidade; ?>
                        </div>
                        <div style="font-size: 11px; color: #888; text-transform: uppercase;">unidades</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Histórico -->
<div class="tec-card">
    <div class="tec-card-header">
        <div class="tec-card-title">
            <i class='bx bx-history'></i> Histórico (30 dias)
        </div>
    </div>
    <div class="tec-card-body" style="padding: 16px 20px;">
        <?php if (empty($historico)): ?>
            <div class="empty-state" style="text-align: center; padding: 40px 20px; color: #888;">
                <i class='bx bx-history' style="font-size: 32px; margin-bottom: 12px; opacity: 0.5;"></i>
                <p style="margin: 0; font-size: 14px;">Nenhum movimento no período</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach (array_slice($historico, 0, 10) as $mov):
                    $isEntrada = $mov->tipo == 'entrada';
                    $iconColor = $isEntrada ? '#27ae60' : '#e74c3c';
                    $iconBg = $isEntrada ? '#e8f5e9' : '#ffebee';
                ?>
                <div class="historico-item" style="display: flex; align-items: center; padding: 14px 16px; background: #f8f9fa; border-radius: 12px; border: 1px solid #f0f0f0;">
                    <!-- Ícone -->
                    <div style="width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 14px; font-size: 20px; background: <?php echo $iconBg; ?>; color: <?php echo $iconColor; ?>;">
                        <?php if ($isEntrada): ?>
                            <i class='bx bx-down-arrow-alt'></i>
                        <?php else: ?>
                            <i class='bx bx-up-arrow-alt'></i>
                        <?php endif; ?>
                    </div>

                    <!-- Info -->
                    <div style="flex: 1; min-width: 0;">
                        <div class="info-text" style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($mov->produto_nome, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </div>
                        <div class="info-date" style="font-size: 12px; color: #888;">
                            <i class='bx bx-calendar' style="margin-right: 4px;"></i>
                            <?php echo date('d/m/Y H:i', strtotime($mov->data_hora)); ?>
                            <?php if ($mov->os_id): ?>
                                <span style="margin-left: 8px; background: #e3f2fd; color: #1976d2; padding: 2px 8px; border-radius: 10px; font-size: 11px;">
                                    OS #<?php echo $mov->os_id; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quantidade -->
                    <div style="font-weight: 700; font-size: 18px; color: <?php echo $iconColor; ?>;">
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

<style>
/* Hover effects */
.estoque-row:hover {
    background: #f8f9fa;
}

/* Search input focus */
.search-box input:focus {
    border-color: #9b59b6 !important;
}

/* Responsividade */
@media (max-width: 768px) {
    .estoque-row {
        flex-wrap: wrap;
        padding: 12px 16px;
    }

    .estoque-row > div:last-child {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        margin-left: 60px;
        padding-top: 10px;
        border-top: 1px solid #f0f0f0;
    }

    .estoque-row > div:last-child > div:first-child {
        font-size: 20px !important;
    }

    .produto-nome {
        font-size: 14px !important;
    }

    .produto-info {
        font-size: 12px !important;
    }

    .historico-item {
        padding: 12px;
    }

    .historico-item > div:last-child {
        font-size: 16px !important;
    }
}
</style>
</div>
