<?php
// Helper para cores de status
function getStatusColor($status) {
    $colors = [
        'Aberto' => '#00cd00',
        'Orçamento' => '#CDB380',
        'Negociação' => '#AEB404',
        'Aprovado' => '#808080',
        'Em Andamento' => '#436eee',
        'Aguardando Peças' => '#FF7F00',
        'Finalizado' => '#256',
        'Finalizada' => '#256',
        'Faturado' => '#B266FF',
        'Cancelado' => '#CD0000'
    ];
    return $colors[$status] ?? '#7f8c8d';
}
?>

<style>
/* Estilos modernos para a página de OS */
.os-container {
    padding: 20px;
}

.os-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.os-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.os-title i {
    color: #667eea;
}

.os-stats {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.stat-box {
    background: white;
    padding: 10px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 10px;
}

.stat-box .number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #667eea;
}

.stat-box .label {
    font-size: 0.85rem;
    color: #7f8c8d;
}

/* Filtros */
.filter-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 6px;
}

.filter-group input,
.filter-group select {
    padding: 10px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.filter-group input:focus,
.filter-group select:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.btn-filter {
    padding: 10px 20px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-filter:hover {
    background: #5568d3;
}

.btn-clear {
    padding: 10px 20px;
    background: #f8f9fa;
    color: #6c757d;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-clear:hover {
    background: #e9ecef;
}

/* Tabela moderna */
.os-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    overflow: hidden;
}

.os-table-header {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.os-table-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
}

.os-table {
    width: 100%;
    border-collapse: collapse;
}

.os-table thead {
    background: #f8f9fa;
}

.os-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.os-table td {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.os-table tbody tr:hover {
    background: #f8f9fa;
}

.os-number {
    font-weight: 700;
    color: #667eea;
    font-size: 1.1rem;
}

.os-cliente {
    display: flex;
    flex-direction: column;
}

.os-cliente .nome {
    font-weight: 600;
    color: #2c3e50;
}

.os-cliente .cnpj {
    font-size: 0.8rem;
    color: #7f8c8d;
}

.os-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.os-status::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
}

.os-date {
    color: #2c3e50;
    font-size: 0.9rem;
}

.os-date .label {
    font-size: 0.75rem;
    color: #7f8c8d;
    margin-bottom: 2px;
}

.os-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.os-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.os-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.os-btn-view { background: #667eea; }
.os-btn-print { background: #34495e; }
.os-btn-report { background: #27ae60; }
.os-btn-detail { background: #3498db; }

/* Paginação moderna */
.pagination-wrapper {
    padding: 20px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: center;
}

.pagination.modern {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 5px;
}

.pagination.modern li {
    margin: 0;
}

.pagination.modern li a,
.pagination.modern li span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 15px;
    border-radius: 8px;
    text-decoration: none;
    color: #667eea;
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
}

.pagination.modern li.active a,
.pagination.modern li.active span {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.pagination.modern li a:hover {
    background: #e9ecef;
}

.pagination.modern li.nav a {
    font-weight: 500;
}

/* Estado vazio */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    margin-bottom: 25px;
}

.empty-state i {
    font-size: 4rem;
    color: #e0e0e0;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state p {
    color: #7f8c8d;
    margin-bottom: 25px;
}

/* Responsividade */
@media (max-width: 768px) {
    .os-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .filter-grid {
        grid-template-columns: 1fr;
    }

    .os-table {
        display: block;
        overflow-x: auto;
    }

    .os-btn {
        width: 32px;
        height: 32px;
    }
}
</style>

<div class="os-container">
    <!-- Header -->
    <div class="os-header">
        <div class="os-title">
            <i class="bx bx-clipboard"></i>
            Minhas Ordens de Serviço
        </div>

        <div class="os-stats">
            <div class="stat-box">
                <div class="number"><?= $total_os ?></div>
                <div class="label">Total de OS</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filter-card">
        <form method="get" action="<?= site_url('mine/os') ?>">
            <div class="filter-grid">
                <div class="filter-group">
                    <label><i class="bx bx-search"></i> Buscar</label>
                    <input type="text" name="busca" value="<?= htmlspecialchars($filtros['busca'] ?? '') ?>"
                           placeholder="Nº OS, descrição, status...">
                </div>

                <div class="filter-group">
                    <label><i class="bx bx-flag"></i> Status</label>
                    <select name="status">
                        <option value="">Todos os status</option>
                        <?php foreach ($status_list as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($filtros['status'] ?? '') == $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label><i class="bx bx-calendar"></i> Data Inicial</label>
                    <input type="date" name="data_inicio" value="<?= $filtros['data_inicio'] ?? '' ?>">
                </div>

                <div class="filter-group">
                    <label><i class="bx bx-calendar"></i> Data Final</label>
                    <input type="date" name="data_fim" value="<?= $filtros['data_fim'] ?? '' ?>">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="bx bx-search"></i> Filtrar
                    </button>
                    <a href="<?= site_url('mine/os?limpar=1') ?>" class="btn-clear">
                        <i class="bx bx-x"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <?php if (empty($results)): ?>
        <!-- Estado vazio -->
        <div class="empty-state">
            <i class="bx bx-clipboard"></i>
            <h3>Nenhuma OS encontrada</h3>
            <p>Não encontramos ordens de serviço com os filtros selecionados.</p>
            <?php if (!empty($filtros['busca']) || !empty($filtros['status']) || !empty($filtros['data_inicio']) || !empty($filtros['data_fim'])): ?>
                <a href="<?= site_url('mine/os?limpar=1') ?>" class="btn-filter">
                    <i class="bx bx-x"></i> Limpar Filtros
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Tabela de OS -->
        <div class="os-table-container">
            <div class="os-table-header">
                <div class="os-table-title">
                    <i class="bx bx-list-ul"></i>
                    Resultados da Busca
                </div>
                <div style="color: #7f8c8d; font-size: 0.9rem;">
                    Mostrando <?= is_array($results) ? count($results) : 0 ?> de <?= $total_os ?? 0 ?> OS
                </div>
            </div>

            <table class="os-table">
                <thead>
                    <tr>
                        <th>OS</th>
                        <th>Cliente</th>
                        <th>Responsável</th>
                        <th>Período</th>
                        <th>Garantia</th>
                        <th>Status</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $r): ?>
                        <?php
                        $cor = getStatusColor($r->status ?? '');

                        // Calcular garantia
                        $vencGarantia = '';
                        $corGarantia = '';
                        if (!empty($r->garantia) && is_numeric($r->garantia) && !empty($r->dataFinal)) {
                            try {
                                $vencGarantia = dateInterval($r->dataFinal, $r->garantia);
                                $dataGarantia = explode('/', $vencGarantia);
                                if (count($dataGarantia) == 3) {
                                    $dataGarantiaFormatada = $dataGarantia[2] . '-' . $dataGarantia[1] . '-' . $dataGarantia[0];
                                    $corGarantia = (strtotime($dataGarantiaFormatada) >= strtotime(date('Y-m-d'))) ? '#4d9c79' : '#f24c6f';
                                }
                            } catch (Exception $e) {
                                $vencGarantia = 'Erro no cálculo';
                                $corGarantia = '#95a5a6';
                            }
                        } elseif (isset($r->garantia) && $r->garantia == "0") {
                            $vencGarantia = 'Sem Garantia';
                        }
                        ?>
                        <tr>
                            <td>
                                <div class="os-number">#<?= $r->idOs ?></div>
                            </td>
                            <td>
                                <div class="os-cliente">
                                    <span class="nome"><?= htmlspecialchars($r->nomeCliente ?? 'N/A') ?></span>
                                    <?php if (!empty($r->documento)): ?>
                                        <span class="cnpj"><?= $r->documento ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($r->responsavel ?? 'N/A') ?></td>
                            <td>
                                <div class="os-date">
                                    <div class="label">Início</div>
                                    <?= !empty($r->dataInicial) ? date('d/m/Y', strtotime($r->dataInicial)) : 'N/A' ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($vencGarantia): ?>
                                    <span class="badge" style="background-color: <?= $corGarantia ?>; color: white;">
                                        <?= $vencGarantia ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #95a5a6;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="os-status" style="background-color: <?= $cor ?>20; color: <?= $cor ?>;">
                                    <?= htmlspecialchars($r->status ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <div class="os-actions">
                                    <a href="<?= base_url('index.php/mine/visualizarOs/' . $r->idOs) ?>"
                                       class="os-btn os-btn-view" title="Visualizar">
                                        <i class="bx bx-show-alt"></i>
                                    </a>

                                    <?php if (isset($r->status) && ($r->status == 'Finalizado' || $r->status == 'Finalizada')): ?>
                                        <a href="<?= base_url('index.php/mine/relatorioAtendimento/' . $r->idOs) ?>"
                                           class="os-btn os-btn-report" title="Relatório de Atendimento">
                                            <i class="bx bx-file"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?= base_url('index.php/mine/imprimirOs/' . $r->idOs) ?>"
                                       class="os-btn os-btn-print" title="Imprimir" target="_blank">
                                        <i class="bx bx-printer"></i>
                                    </a>

                                    <a href="<?= base_url('index.php/mine/detalhesOs/' . $r->idOs) ?>"
                                       class="os-btn os-btn-detail" title="Ver Detalhes">
                                        <i class="bx bx-detail"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Paginação -->
            <?php if ($this->pagination->create_links()): ?>
                <?= $this->pagination->create_links() ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
