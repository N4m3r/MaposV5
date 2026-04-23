<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// Garantir que a variável $obras exista (controller pode passar como $results)
$obras = isset($obras) ? $obras : (isset($results) ? $results : []);
?>

<!-- Tema Moderno Obras - CSS Unificado -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
/* ============================================
   SISTEMA DE OBRAS - TEMA UNIFICADO
   Suporte completo a Dark/Light Mode
   ============================================ */

/* Container principal */
.obras-unified-container {
    padding: 24px;
    max-width: 1600px;
    margin: 0 auto;
}

/* Header principal */
.obras-main-header {
    background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    border-radius: var(--radius-xl, 20px);
    padding: 32px;
    color: white;
    margin-bottom: 30px;
    box-shadow: var(--shadow-xl, 0 20px 40px rgba(0,0,0,0.15));
    position: relative;
    overflow: hidden;
}

.obras-main-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 500px;
    height: 500px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    pointer-events: none;
}

.obras-header-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}

.obras-header-title h1 {
    margin: 0 0 8px 0;
    font-size: 32px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.obras-header-title p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}

/* Stats Cards Modernos */
.obras-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.obras-stat-card {
    background: var(--widget-box, #ffffff);
    border-radius: var(--radius-lg, 16px);
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.07));
    border: 1px solid var(--border-color, rgba(0,0,0,0.05));
    transition: all var(--transition-normal, 0.3s ease);
}

.obras-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg, 0 10px 25px rgba(0,0,0,0.1));
}

.obras-stat-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md, 12px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
}

.obras-stat-icon.blue { background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%)); }
.obras-stat-icon.green { background: var(--gradient-success, linear-gradient(135deg, #11998e 0%, #38ef7d 100%)); }
.obras-stat-icon.orange { background: var(--gradient-warning, linear-gradient(135deg, #feca57 0%, #ff9f43 100%)); }
.obras-stat-icon.red { background: var(--gradient-danger, linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%)); }
.obras-stat-icon.cyan { background: var(--gradient-info, linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)); }

.obras-stat-info { flex: 1; }
.obras-stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--title, #333);
    line-height: 1;
    margin-bottom: 4px;
}
.obras-stat-label {
    font-size: 14px;
    color: var(--subtitle, #666);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Barra de filtros moderna */
.obras-filter-bar {
    background: var(--widget-box, #f8f9fa);
    border-radius: var(--radius-lg, 16px);
    padding: 20px 24px;
    margin-bottom: 24px;
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
    border: 1px solid var(--border-color, rgba(0,0,0,0.05));
    box-shadow: var(--shadow-sm, 0 2px 4px rgba(0,0,0,0.05));
}

.obras-filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.obras-filter-group label {
    font-size: 14px;
    font-weight: 600;
    color: var(--title, #333);
    white-space: nowrap;
}

.obras-filter-input,
.obras-filter-select {
    padding: 12px 16px;
    border-radius: var(--radius-md, 10px);
    border: 2px solid var(--border-color, #e0e0e0);
    background: var(--widget-box, #fff);
    color: var(--title, #333);
    font-size: 14px;
    min-width: 180px;
    transition: all var(--transition-fast, 0.15s ease);
}

.obras-filter-input:focus,
.obras-filter-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.obras-filter-btn {
    padding: 12px 24px;
    border-radius: var(--radius-md, 10px);
    border: none;
    background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    color: white;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all var(--transition-normal, 0.3s ease);
}

.obras-filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.obras-filter-btn.secondary {
    background: var(--body-color, #6c757d);
}

.obras-filter-btn.secondary:hover {
    background: #5a6268;
}

.obras-add-btn {
    margin-left: auto;
    padding: 12px 28px;
    background: var(--gradient-success, linear-gradient(135deg, #11998e 0%, #38ef7d 100%));
}

.obras-add-btn:hover {
    box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
}

/* Grid de cards de obras */
.obras-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 24px;
}

/* Card de obra individual */
.obra-item-card {
    background: var(--widget-box, #ffffff);
    border-radius: var(--radius-xl, 20px);
    overflow: hidden;
    box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.07));
    border: 1px solid var(--border-color, rgba(0,0,0,0.05));
    transition: all var(--transition-normal, 0.3s ease);
    display: flex;
    flex-direction: column;
}

.obra-item-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl, 0 20px 40px rgba(0,0,0,0.12));
}

.obra-card-header {
    padding: 24px;
    background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    color: white;
    position: relative;
}

.obra-card-header.andamento {
    background: var(--gradient-info, linear-gradient(135deg, #4facfe 0%, #00f2fe 100%));
}

.obra-card-header.concluida {
    background: var(--gradient-success, linear-gradient(135deg, #11998e 0%, #38ef7d 100%));
}

.obra-card-header.paralisada {
    background: var(--gradient-danger, linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%));
}

.obra-card-header.prospeccao {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #333;
}

.obra-card-status-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(255,255,255,0.25);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(10px);
}

.obra-card-title {
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 8px 0;
    line-height: 1.3;
    padding-right: 80px;
}

.obra-card-cliente {
    font-size: 14px;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 6px;
}

.obra-card-body {
    padding: 24px;
    flex: 1;
}

.obra-card-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.05));
}

.obra-card-info-row:last-child {
    border-bottom: none;
}

.obra-card-info-label {
    font-size: 13px;
    color: var(--subtitle, #888);
    display: flex;
    align-items: center;
    gap: 6px;
}

.obra-card-info-value {
    font-weight: 600;
    color: var(--title, #333);
    font-size: 14px;
}

/* Progresso no card */
.obra-card-progress {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color, rgba(0,0,0,0.05));
}

.obra-card-progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 13px;
}

.obra-card-progress-label {
    color: var(--subtitle, #666);
    font-weight: 600;
}

.obra-card-progress-value {
    color: #667eea;
    font-weight: 700;
}

.obra-card-progress-bar {
    height: 8px;
    background: var(--body-color, #e9ecef);
    border-radius: 10px;
    overflow: hidden;
}

.obra-card-progress-fill {
    height: 100%;
    background: var(--gradient-primary, linear-gradient(90deg, #667eea 0%, #764ba2 100%));
    border-radius: 10px;
    transition: width 0.5s ease;
}

/* Estatísticas rápidas no card */
.obra-card-stats {
    display: flex;
    gap: 16px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color, rgba(0,0,0,0.05));
}

.obra-card-stat {
    flex: 1;
    text-align: center;
    padding: 12px;
    background: var(--body-color, #f8f9fa);
    border-radius: var(--radius-md, 10px);
}

.obra-card-stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #667eea;
    display: block;
}

.obra-card-stat-label {
    font-size: 11px;
    color: var(--subtitle, #888);
    text-transform: uppercase;
}

/* Ações do card */
.obra-card-actions {
    display: flex;
    gap: 8px;
    padding: 16px 24px;
    background: var(--body-color, #f8f9fa);
    border-top: 1px solid var(--border-color, rgba(0,0,0,0.05));
}

.obra-card-btn {
    flex: 1;
    padding: 10px;
    border-radius: var(--radius-md, 10px);
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    transition: all var(--transition-fast, 0.15s ease);
    color: white;
}

.obra-card-btn.view {
    background: #667eea;
}

.obra-card-btn.view:hover {
    background: #5568d3;
    transform: translateY(-2px);
}

.obra-card-btn.edit {
    background: #f39c12;
}

.obra-card-btn.edit:hover {
    background: #e67e22;
    transform: translateY(-2px);
}

/* Empty state */
.obras-empty-state {
    text-align: center;
    padding: 80px 20px;
    background: var(--widget-box, white);
    border-radius: var(--radius-xl, 20px);
    box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.07));
}

.obras-empty-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    font-size: 60px;
    color: #667eea;
}

.obras-empty-title {
    font-size: 24px;
    color: var(--title, #333);
    margin-bottom: 10px;
}

.obras-empty-desc {
    color: var(--subtitle, #666);
    margin-bottom: 30px;
    font-size: 16px;
}

/* Paginação */
.obras-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 40px;
    padding: 20px;
}

/* ============================================
   DARK MODE SUPPORT
   ============================================ */

[data-theme="dark"] .obras-stat-card,
[data-theme="dark"] .obra-item-card,
[data-theme="dark"] .obras-filter-bar,
[data-theme="dark"] .obras-empty-state {
    background: var(--dark-2, #2d3748);
    border-color: var(--dark-4, #4a5568);
}

[data-theme="dark"] .obras-stat-value,
[data-theme="dark"] .obra-card-title,
[data-theme="dark"] .obra-card-info-value,
[data-theme="dark"] .obras-filter-group label,
[data-theme="dark"] .obras-header-title h1,
[data-theme="dark"] .obras-empty-title {
    color: var(--white, #fff);
}

[data-theme="dark"] .obras-stat-label,
[data-theme="dark"] .obra-card-cliente,
[data-theme="dark"] .obra-card-info-label,
[data-theme="dark"] .obras-empty-desc,
[data-theme="dark"] .obra-card-progress-label {
    color: var(--dark-7, #a0aec0);
}

[data-theme="dark"] .obras-filter-input,
[data-theme="dark"] .obras-filter-select {
    background: var(--dark-3, #3d4852);
    border-color: var(--dark-4, #4a5568);
    color: var(--white, #fff);
}

[data-theme="dark"] .obras-filter-input:focus,
[data-theme="dark"] .obras-filter-select:focus {
    border-color: #667eea;
}

[data-theme="dark"] .obra-card-stat,
[data-theme="dark"] .obra-card-actions {
    background: var(--dark-3, #3d4852);
}

[data-theme="dark"] .obra-card-progress-bar {
    background: var(--dark-4, #4a5568);
}

/* Responsividade */
@media (max-width: 768px) {
    .obras-unified-container { padding: 16px; }
    .obras-header-content { flex-direction: column; }
    .obras-header-title h1 { font-size: 24px; }
    .obras-stats-row { grid-template-columns: repeat(2, 1fr); }
    .obras-filter-bar { flex-direction: column; align-items: stretch; }
    .obras-add-btn { margin-left: 0; width: 100%; justify-content: center; }
    .obras-cards-grid { grid-template-columns: 1fr; }
    .obra-card-actions { flex-wrap: wrap; }
}
</style>

<div class="obras-unified-container">
    <!-- Header Principal -->
    <div class="obras-main-header">
        <div class="obras-header-content">
            <div class="obras-header-title">
                <h1><i class="icon-building"></i> Gerenciamento de Obras</h1>
                <p>Acompanhe e gerencie todas as obras do sistema</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="<?php echo site_url('obras/adicionar'); ?>" class="obras-filter-btn obras-add-btn">
                    <i class="icon-plus"></i> Nova Obra
                </a>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')): ?>
                <a href="<?php echo site_url('obras/configuracoes'); ?>" class="obras-filter-btn" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white;">
                    <i class="icon-cog"></i> Configurações
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="obras-stats-row">
        <div class="obras-stat-card">
            <div class="obras-stat-icon blue">
                <i class="icon-building"></i>
            </div>
            <div class="obras-stat-info">
                <div class="obras-stat-value"><?php echo isset($total_obras) ? $total_obras : count($obras); ?></div>
                <div class="obras-stat-label">Total de Obras</div>
            </div>
        </div>

        <div class="obras-stat-card">
            <div class="obras-stat-icon green">
                <i class="icon-play-circle"></i>
            </div>
            <div class="obras-stat-info">
                <div class="obras-stat-value"><?php echo isset($obras_em_andamento) ? $obras_em_andamento : count(array_filter($obras ?? [], function($o) { return $o->status == 'em-andamento'; })); ?></div>
                <div class="obras-stat-label">Em Andamento</div>
            </div>
        </div>

        <div class="obras-stat-card">
            <div class="obras-stat-icon cyan">
                <i class="icon-calendar"></i>
            </div>
            <div class="obras-stat-info">
                <div class="obras-stat-value"><?php echo isset($obras_contratadas) ? $obras_contratadas : count(array_filter($obras ?? [], function($o) { return $o->status == 'contratada'; })); ?></div>
                <div class="obras-stat-label">Contratadas</div>
            </div>
        </div>

        <div class="obras-stat-card">
            <div class="obras-stat-icon orange">
                <i class="icon-check-circle"></i>
            </div>
            <div class="obras-stat-info">
                <div class="obras-stat-value"><?php echo isset($obras_concluidas) ? $obras_concluidas : count(array_filter($obras ?? [], function($o) { return $o->status == 'concluida'; })); ?></div>
                <div class="obras-stat-label">Concluídas</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="obras-filter-bar">
        <div class="obras-filter-group">
            <label><i class="icon-search"></i> Buscar:</label>
            <input type="text" id="searchObra" class="obras-filter-input" placeholder="Nome da obra..." onkeyup="filtrarObras()">
        </div>

        <div class="obras-filter-group">
            <label><i class="icon-filter"></i> Status:</label>
            <select id="filterStatus" class="obras-filter-select" onchange="filtrarObras()">
                <option value="">Todos</option>
                <option value="prospeccao">Prospecção</option>
                <option value="contratada">Contratada</option>
                <option value="em-andamento">Em Andamento</option>
                <option value="concluida">Concluída</option>
                <option value="paralisada">Paralisada</option>
            </select>
        </div>

        <button class="obras-filter-btn secondary" onclick="limparFiltros()">
            <i class="icon-refresh"></i> Limpar
        </button>
    </div>

    <!-- Grid de Obras -->
    <?php if (!empty($obras)): ?>
    <div class="obras-cards-grid" id="obrasGrid">
        <?php foreach ($obras as $obra): ?>
        <?php
        $status_class = '';
        $status_label = '';
        switch ($obra->status) {
            case 'em-andamento':
            case 'em execucao':
                $status_class = 'andamento';
                $status_label = 'Em Andamento';
                break;
            case 'concluida':
                $status_class = 'concluida';
                $status_label = 'Concluída';
                break;
            case 'paralisada':
                $status_class = 'paralisada';
                $status_label = 'Paralisada';
                break;
            case 'prospeccao':
                $status_class = 'prospeccao';
                $status_label = 'Prospecção';
                break;
            default:
                $status_class = '';
                $status_label = ucfirst($obra->status);
        }
        $progresso = $obra->percentual_concluido ?? 0;
        ?>
        <div class="obra-item-card" data-nome="<?php echo strtolower($obra->nome); ?>" data-status="<?php echo $obra->status; ?>">
            <div class="obra-card-header <?php echo $status_class; ?>">
                <span class="obra-card-status-badge"><?php echo $status_label; ?></span>
                <h3 class="obra-card-title"><?php echo htmlspecialchars($obra->nome); ?></h3>
                <div class="obra-card-cliente">
                    <i class="icon-user"></i> <?php echo htmlspecialchars($obra->cliente_nome ?? 'Sem cliente'); ?>
                </div>
            </div>

            <div class="obra-card-body">
                <div class="obra-card-info-row">
                    <span class="obra-card-info-label">
                        <i class="icon-map-marker"></i> Endereço
                    </span>
                    <span class="obra-card-info-value">
                        <?php echo htmlspecialchars($obra->endereco ?? 'Não informado'); ?>
                    </span>
                </div>

                <div class="obra-card-info-row">
                    <span class="obra-card-info-label">
                        <i class="icon-calendar"></i> Início
                    </span>
                    <span class="obra-card-info-value">
                        <?php echo $obra->data_inicio_contrato ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : 'Não definido'; ?>
                    </span>
                </div>

                <div class="obra-card-info-row">
                    <span class="obra-card-info-label">
                        <i class="icon-flag-checkered"></i> Previsão
                    </span>
                    <span class="obra-card-info-value">
                        <?php echo $obra->data_fim_prevista ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : 'Não definido'; ?>
                    </span>
                </div>

                <!-- Progresso -->
                <div class="obra-card-progress">
                    <div class="obra-card-progress-header">
                        <span class="obra-card-progress-label">Progresso</span>
                        <span class="obra-card-progress-value"><?php echo $progresso; ?>%</span>
                    </div>
                    <div class="obra-card-progress-bar">
                        <div class="obra-card-progress-fill" style="width: <?php echo $progresso; ?>"></div>
                    </div>
                </div>

                <!-- Stats rápidas -->
                <div class="obra-card-stats">
                    <div class="obra-card-stat">
                        <span class="obra-card-stat-value"><?php echo $obra->total_etapas ?? 0; ?></span>
                        <span class="obra-card-stat-label">Etapas</span>
                    </div>
                    <div class="obra-card-stat">
                        <span class="obra-card-stat-value"><?php echo $obra->total_atividades ?? 0; ?></span>
                        <span class="obra-card-stat-label">Atividades</span>
                    </div>
                    <div class="obra-card-stat">
                        <span class="obra-card-stat-value"><?php echo $obra->total_equipe ?? 0; ?></span>
                        <span class="obra-card-stat-label">Equipe</span>
                    </div>
                </div>
            </div>

            <div class="obra-card-actions">
                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="obra-card-btn view">
                    <i class="icon-eye-open"></i> Visualizar
                </a>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <a href="<?php echo site_url('obras/editar/' . $obra->id); ?>" class="obra-card-btn edit">
                    <i class="icon-edit"></i> Editar
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <?php if (isset($pagination)): ?>
    <div class="obras-pagination">
        <?php echo $pagination; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <!-- Empty State -->
    <div class="obras-empty-state">
        <div class="obras-empty-icon">
            <i class="icon-building"></i>
        </div>
        <h3 class="obras-empty-title">Nenhuma obra encontrada</h3>
        <p class="obras-empty-desc">Comece cadastrando uma nova obra para gerenciar seus projetos.</p>
        <a href="<?php echo site_url('obras/adicionar'); ?>" class="obras-filter-btn">
            <i class="icon-plus"></i> Cadastrar Nova Obra
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
// Filtro de obras
function filtrarObras() {
    const search = document.getElementById('searchObra').value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    const status = document.getElementById('filterStatus').value;
    const cards = document.querySelectorAll('.obra-item-card');

    cards.forEach(card => {
        const nome = card.getAttribute('data-nome');
        const cardStatus = card.getAttribute('data-status');

        const matchSearch = !search || nome.includes(search);
        const matchStatus = !status || cardStatus === status;

        card.style.display = matchSearch && matchStatus ? 'flex' : 'none';
    });
}

function limparFiltros() {
    document.getElementById('searchObra').value = '';
    document.getElementById('filterStatus').value = '';
    filtrarObras();
}

// Animação de entrada
$(document).ready(function() {
    $('.obra-item-card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });
});
</script>
