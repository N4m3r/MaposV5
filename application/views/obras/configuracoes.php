<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ===== ESTILOS DA PAGINA DE CONFIGURACOES ===== */
.config-container {
    padding: 24px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.config-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 32px;
    color: white;
    margin-bottom: 24px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.config-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}

.config-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.config-header p {
    margin: 8px 0 0 0;
    opacity: 0.9;
    font-size: 15px;
}

/* Layout com Abas - Sistema CSS-only usando :target */
.config-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 24px;
}

/* Sidebar de Abas */
.config-tabs {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    overflow: hidden;
    height: fit-content;
    position: sticky;
    top: 20px;
}

.config-tab-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    color: #666;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all 0.2s;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}

.config-tab-item:last-child {
    border-bottom: none;
}

.config-tab-item:hover {
    background: #f8f9fa;
    color: #667eea;
}

.config-tab-item.active {
    background: #f0f4ff;
    color: #667eea;
    border-left-color: #667eea;
}

.config-tab-item i {
    font-size: 20px;
    width: 24px;
    text-align: center;
}

.config-tab-item .badge-count {
    margin-left: auto;
    background: #e8e8e8;
    color: #666;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
}

/* Conteudo das Abas - Sistema :target */
.config-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

/* Conteúdo das abas - controlado por JS */
.config-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.config-content.ativo {
    display: block;
}

/* Se nenhuma aba estiver selecionada, mostra a primeira (Geral) */
.config-content-wrapper:not(:has(.config-content.ativo)) #tab-geral {
    display: block;
}

/* Quando uma aba especifica esta ativa, esconde a geral */
.config-content-wrapper:has(.config-content.ativo) #tab-geral {
    display: none;
}

/* Fallback para navegadores sem :has() */
@supports not selector(:has(.config-content.ativo)) {
    #tab-geral {
        display: block;
    }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Cards */
.config-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    overflow: hidden;
}

.config-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.config-card-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.config-card-title i {
    color: #667eea;
    font-size: 22px;
}

.config-card-body {
    padding: 24px;
}

/* Formularios */
.config-form-group {
    margin-bottom: 20px;
}

.config-form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #555;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.config-form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
    background: white;
}

.config-form-control:focus {
    outline: none;
    border-color: #667eea;
}

select.config-form-control {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
}

/* Lista de Itens Configuraveis */
.config-items-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.config-item {
    display: flex;
    align-items: center;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px solid transparent;
    transition: all 0.2s;
}

.config-item:hover {
    border-color: #667eea;
    background: #fff;
}

.config-item-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
    font-size: 20px;
    flex-shrink: 0;
}

.config-item-info {
    flex: 1;
    min-width: 0;
}

.config-item-name {
    font-weight: 600;
    color: #333;
    font-size: 15px;
    margin-bottom: 4px;
}

.config-item-meta {
    font-size: 13px;
    color: #888;
}

.config-item-actions {
    display: flex;
    gap: 8px;
}

.config-btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.config-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.config-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.config-btn-secondary {
    background: #f0f0f0;
    color: #666;
}

.config-btn-secondary:hover {
    background: #e0e0e0;
}

.config-btn-danger {
    background: #fee;
    color: #e74c3c;
}

.config-btn-danger:hover {
    background: #fcc;
}

.config-btn-success {
    background: #e8f5e9;
    color: #27ae60;
}

.config-btn-success:hover {
    background: #c8e6c9;
}

.config-btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

/* Grid de Cards */
.config-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.config-grid-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    border: 2px solid transparent;
    transition: all 0.2s;
}

.config-grid-item:hover {
    border-color: #667eea;
    background: white;
    transform: translateY(-2px);
}

/* Cores para tipos */
.tipo-color {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Toggle Switch */
.config-toggle {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
}

.config-toggle input {
    display: none;
}

.toggle-slider {
    width: 48px;
    height: 24px;
    background: #ccc;
    border-radius: 12px;
    position: relative;
    transition: background 0.3s;
}

.toggle-slider::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: transform 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.config-toggle input:checked + .toggle-slider {
    background: #667eea;
}

.config-toggle input:checked + .toggle-slider::after {
    transform: translateX(24px);
}

/* Status Badges */
.status-badge-config {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    gap: 6px;
}

/* Alertas */
.config-alert {
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.config-alert-info {
    background: #e3f2fd;
    color: #1976d2;
    border-left: 4px solid #2196f3;
}

.config-alert-warning {
    background: #fff3e0;
    color: #f57c00;
    border-left: 4px solid #ff9800;
}

.config-alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    border-left: 4px solid #4caf50;
}

/* Modal */
.config-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.config-modal-overlay.active {
    display: flex;
}

.config-modal {
    background: white;
    border-radius: 16px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.config-modal-header {
    padding: 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.config-modal-header h3 {
    margin: 0;
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.config-modal-body {
    padding: 24px;
    overflow-y: auto;
    max-height: 60vh;
}

.config-modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* Color Picker */
.color-picker-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.color-picker-wrapper input[type="color"] {
    width: 60px;
    height: 40px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.color-picker-wrapper input[type="text"] {
    flex: 1;
}

/* Icon Selector */
.icon-selector {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 8px;
    max-height: 200px;
    overflow-y: auto;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.icon-option {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 20px;
    color: #666;
}

.icon-option:hover {
    border-color: #667eea;
    color: #667eea;
}

.icon-option.selected {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

/* Responsivo */
@media (max-width: 992px) {
    .config-layout {
        grid-template-columns: 1fr;
    }

    .config-tabs {
        position: static;
        display: flex;
        overflow-x: auto;
        padding: 8px;
        gap: 8px;
    }

    .config-tab-item {
        white-space: nowrap;
        border-left: none;
        border-bottom: 3px solid transparent;
        border-radius: 8px;
    }

    .config-tab-item.active {
        border-left-color: transparent;
        border-bottom-color: #667eea;
    }

    .config-grid {
        grid-template-columns: 1fr;
    }

    .icon-selector {
        grid-template-columns: repeat(6, 1fr);
    }
}

@media (max-width: 768px) {
    .config-container {
        padding: 16px;
    }

    .config-header {
        padding: 24px;
    }

    .config-header h1 {
        font-size: 22px;
    }

    .config-card-header,
    .config-card-body {
        padding: 16px;
    }

    .config-item {
        flex-wrap: wrap;
    }

    .config-item-actions {
        width: 100%;
        margin-top: 12px;
        justify-content: flex-end;
    }

    .icon-selector {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Loading */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
}

.loading-overlay.active {
    display: flex;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #888;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.3;
}

.empty-state h4 {
    margin: 0 0 8px 0;
    color: #666;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

/* Sort Handle */
.sort-handle {
    cursor: grab;
    padding: 8px;
    color: #ccc;
    margin-right: 8px;
}

.sort-handle:hover {
    color: #667eea;
}

.sort-handle:active {
    cursor: grabbing;
}
</style>

<div class="config-container">
    <!-- Header -->
    <div class="config-header">
        <div class="config-header-content">
            <div>
                <h1><i class='bx bx-cog'></i> Configurações do Sistema de Obras</h1>
                <p>Gerencie tipos, status, especialidades e preferências do sistema</p>
            </div>
            <a href="<?php echo site_url('obras'); ?>" class="btn" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">
                <i class='bx bx-arrow-back'></i> Voltar às Obras
            </a>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
    <div class="config-alert config-alert-success" style="margin-bottom: 20px;">
        <i class='bx bx-check-circle'></i>
        <div><?php echo $this->session->flashdata('success'); ?></div>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div class="config-alert config-alert-warning" style="margin-bottom: 20px;">
        <i class='bx bx-error-circle'></i>
        <div><?php echo $this->session->flashdata('error'); ?></div>
    </div>
    <?php endif; ?>

    <!-- Botões de acesso rápido -->
    <div style="text-align: center; margin-bottom: 24px;">
        <button type="button" onclick="abrirModalConfiguracoes()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 16px 32px; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
            <i class='bx bx-cog' style="font-size: 20px; vertical-align: middle; margin-right: 8px;"></i>
            Abrir Painel de Configurações
        </button>
    </div>

    <!-- Cards resumo na página principal -->
    <div class="config-grid">
        <div class="config-grid-item" style="cursor: pointer;" onclick="abrirModalConfiguracoes('tab-tipos-obra')">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 48px; height: 48px; background: #667eea20; color: #667eea; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class='bx bx-building-house'></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: #333;">Tipos de Obra</div>
                    <div style="font-size: 24px; font-weight: 700; color: #667eea;"><?php echo count($tipos_obra ?? []); ?></div>
                </div>
            </div>
        </div>
        <div class="config-grid-item" style="cursor: pointer;" onclick="abrirModalConfiguracoes('tab-tipos-atividade')">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 48px; height: 48px; background: #764ba220; color: #764ba2; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class='bx bx-task'></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: #333;">Tipos de Atividade</div>
                    <div style="font-size: 24px; font-weight: 700; color: #764ba2;"><?php echo count($tipos_atividades ?? []); ?></div>
                </div>
            </div>
        </div>
        <div class="config-grid-item" style="cursor: pointer;" onclick="abrirModalConfiguracoes('tab-status-obra')">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 48px; height: 48px; background: #3498db20; color: #3498db; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class='bx bx-flag'></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: #333;">Status de Obra</div>
                    <div style="font-size: 24px; font-weight: 700; color: #3498db;"><?php echo count($status_obra ?? []); ?></div>
                </div>
            </div>
        </div>
        <div class="config-grid-item" style="cursor: pointer;" onclick="abrirModalConfiguracoes('tab-especialidades')">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 48px; height: 48px; background: #e74c3c20; color: #e74c3c; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class='bx bx-hard-hat'></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: #333;">Especialidades</div>
                    <div style="font-size: 24px; font-weight: 700; color: #e74c3c;"><?php echo count($especialidades ?? []); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL DE CONFIGURAÇÕES (FULL SCREEN) ========== -->
<div class="config-modal-overlay" id="modalConfiguracoes" style="display: none; z-index: 9999; align-items: flex-start; padding-top: 20px; overflow-y: auto;">
    <div class="config-modal" style="max-width: 1200px; width: 95%; margin: 20px auto; max-height: none;">
        <div class="config-modal-header" style="position: sticky; top: 0; z-index: 10; display: flex; justify-content: space-between; align-items: center;">
            <h3><i class='bx bx-cog'></i> <span>Configurações do Sistema de Obras</span></h3>
            <button type="button" onclick="fecharModalConfiguracoes()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; font-size: 20px;">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <div class="config-modal-body" style="padding: 0; overflow: visible;">
            <!-- Layout com Abas dentro do Modal -->
            <div class="config-layout" style="padding: 24px;">
                <!-- Sidebar de Abas -->
                <div class="config-tabs" style="position: sticky; top: 20px;">
                    <div class="config-tab-item active" data-tab="tab-geral" onclick="ativarAbaModal('tab-geral')">
                        <i class='bx bx-slider'></i>
                        <span>Geral</span>
                    </div>
                    <div class="config-tab-item" data-tab="tab-tipos-obra" onclick="ativarAbaModal('tab-tipos-obra')">
                        <i class='bx bx-building-house'></i>
                        <span>Tipos de Obra</span>
                        <span class="badge-count" id="count-tipos-obra"><?php echo count($tipos_obra ?? []); ?></span>
                    </div>
                    <div class="config-tab-item" data-tab="tab-tipos-atividade" onclick="ativarAbaModal('tab-tipos-atividade')">
                        <i class='bx bx-task'></i>
                        <span>Tipos de Atividade</span>
                        <span class="badge-count" id="count-tipos-atividade"><?php echo count($tipos_atividades ?? []); ?></span>
                    </div>
                    <div class="config-tab-item" data-tab="tab-status-obra" onclick="ativarAbaModal('tab-status-obra')">
                        <i class='bx bx-flag'></i>
                        <span>Status de Obra</span>
                        <span class="badge-count" id="count-status-obra"><?php echo count($status_obra ?? []); ?></span>
                    </div>
                    <div class="config-tab-item" data-tab="tab-status-atividade" onclick="ativarAbaModal('tab-status-atividade')">
                        <i class='bx bx-check-circle'></i>
                        <span>Status de Atividade</span>
                        <span class="badge-count" id="count-status-atividade"><?php echo count($status_atividade ?? []); ?></span>
                    </div>
                    <div class="config-tab-item" data-tab="tab-especialidades" onclick="ativarAbaModal('tab-especialidades')">
                        <i class='bx bx-hard-hat'></i>
                        <span>Especialidades</span>
                        <span class="badge-count" id="count-especialidades"><?php echo count($especialidades ?? []); ?></span>
                    </div>
                    <div class="config-tab-item" data-tab="tab-funcoes" onclick="ativarAbaModal('tab-funcoes')">
                        <i class='bx bx-group'></i>
                        <span>Funções da Equipe</span>
                        <span class="badge-count" id="count-funcoes"><?php echo count($funcoes_equipe ?? []); ?></span>
                    </div>
                    <div class="config-tab-item" data-tab="tab-notificacoes" onclick="ativarAbaModal('tab-notificacoes')">
                        <i class='bx bx-bell'></i>
                        <span>Notificações</span>
                    </div>
                </div>

                <!-- Conteudo -->
                <div class="config-content-wrapper">
            <!-- ABA: GERAL -->
            <div id="tab-geral" class="config-content">
                <div class="config-card">
                    <div class="config-card-header">
                        <div class="config-card-title">
                            <i class='bx bx-slider'></i>
                            Configurações Gerais
                        </div>
                    </div>
                    <div class="config-card-body">
                        <form id="form-config-geral" method="post" action="<?php echo site_url('obras/salvarConfiguracao'); ?>">
                            <div class="config-form-group">
                                <label>Nome do Sistema de Obras</label>
                                <input type="text" name="nome_sistema" class="config-form-control" value="<?php echo $config['nome_sistema'] ?? 'Gestão de Obras'; ?>" placeholder="Ex: Gestão de Obras">
                            </div>

                            <div class="config-grid" style="grid-template-columns: repeat(2, 1fr);">
                                <div class="config-form-group">
                                    <label>Prazo Padrão para Início (dias)</label>
                                    <input type="number" name="prazo_inicio_padrao" class="config-form-control" value="<?php echo $config['prazo_inicio_padrao'] ?? 7; ?>" min="0">
                                </div>

                                <div class="config-form-group">
                                    <label>Prazo Padrão para Execução (dias)</label>
                                    <input type="number" name="prazo_execucao_padrao" class="config-form-control" value="<?php echo $config['prazo_execucao_padrao'] ?? 30; ?>" min="1">
                                </div>
                            </div>

                            <hr style="border: none; border-top: 1px solid #f0f0f0; margin: 24px 0;">

                            <h4 style="margin: 0 0 16px 0; color: #333;">Funcionalidades</h4>

                            <div class="config-items-list">
                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Sistema de Atividades</div>
                                        <div class="config-item-meta">Permite criar e gerenciar atividades dentro das obras</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="habilitar_atividades" <?php echo ($config['habilitar_atividades'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Sistema de Etapas</div>
                                        <div class="config-item-meta">Divide a obra em etapas/fases executáveis</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="habilitar_etapas" <?php echo ($config['habilitar_etapas'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Check-in/Check-out</div>
                                        <div class="config-item-meta">Registro de presença e tempo dos técnicos</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="habilitar_checkin" <?php echo ($config['habilitar_checkin'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Geolocalização</div>
                                        <div class="config-item-meta">Captura localização GPS nas atividades</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="habilitar_gps" <?php echo ($config['habilitar_gps'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Reatendimento</div>
                                        <div class="config-item-meta">Permite reabrir atividades para nova execução</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="habilitar_reatendimento" <?php echo ($config['habilitar_reatendimento'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Portal do Técnico</div>
                                        <div class="config-item-meta">Acesso mobile para técnicos executarem atividades</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="habilitar_portal_tecnico" <?php echo ($config['habilitar_portal_tecnico'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div style="margin-top: 24px; text-align: right;">
                                <button type="submit" class="config-btn config-btn-primary">
                                    <i class='bx bx-save'></i> Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ABA: TIPOS DE OBRA -->
            <div id="tab-tipos-obra" class="config-content">
                <div class="config-card">
                    <div class="config-card-header">
                        <div class="config-card-title">
                            <i class='bx bx-building-house'></i>
                            Tipos de Obra
                        </div>
                        <button class="config-btn config-btn-primary config-btn-sm" onclick="abrirModalTipoObra()">
                            <i class='bx bx-plus'></i> Novo Tipo
                        </button>
                    </div>
                    <div class="config-card-body">
                        <div class="config-alert config-alert-info">
                            <i class='bx bx-info-circle'></i>
                            <div>
                                <strong>Tipos de Obra</strong> são usados para categorizar as obras no cadastro e relatórios.
                            </div>
                        </div>

                        <div class="config-items-list" id="lista-tipos-obra">
                            <?php foreach ($tipos_obra as $tipo): ?>
                            <div class="config-item" data-id="<?php echo $tipo->id; ?>">
                                <div class="config-item-icon" style="background: <?php echo $tipo->cor; ?>20; color: <?php echo $tipo->cor; ?>;">
                                    <i class='bx <?php echo $tipo->icone; ?>'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <span class="tipo-color" style="background: <?php echo $tipo->cor; ?>"></span>
                                        <?php echo htmlspecialchars($tipo->nome); ?>
                                    </div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($tipo->descricao ?? ''); ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarTipoObra(<?php echo $tipo->id; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirTipoObra(<?php echo $tipo->id; ?>, '<?php echo htmlspecialchars($tipo->nome); ?>')">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA: TIPOS DE ATIVIDADE -->
            <div id="tab-tipos-atividade" class="config-content">
                <div class="config-card">
                    <div class="config-card-header">
                        <div class="config-card-title">
                            <i class='bx bx-task'></i>
                            Tipos de Atividade
                        </div>
                        <button class="config-btn config-btn-primary config-btn-sm" onclick="abrirModalTipoAtividade()">
                            <i class='bx bx-plus'></i> Novo Tipo
                        </button>
                    </div>
                    <div class="config-card-body">
                        <div class="config-alert config-alert-info">
                            <i class='bx bx-info-circle'></i>
                            <div>
                                <strong>Tipos de Atividade</strong> definem as categorias de trabalho que podem ser executados nas obras.
                            </div>
                        </div>

                        <div class="config-items-list" id="lista-tipos-atividade">
                            <?php foreach ($tipos_atividades as $tipo): ?>
                            <div class="config-item" data-id="<?php echo $tipo->idTipo ?? $tipo->id; ?>">
                                <div class="config-item-icon" style="background: <?php echo $tipo->cor; ?>20; color: <?php echo $tipo->cor; ?>;">
                                    <i class='bx <?php echo $tipo->icone; ?>'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <span class="tipo-color" style="background: <?php echo $tipo->cor; ?>"></span>
                                        <?php echo htmlspecialchars($tipo->nome); ?>
                                        <span style="margin-left: 8px; padding: 2px 8px; background: #f0f0f0; border-radius: 4px; font-size: 11px; text-transform: uppercase;">
                                            <?php echo $tipo->categoria ?? 'outro'; ?>
                                        </span>
                                    </div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($tipo->descricao ?? ''); ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarTipoAtividade(<?php echo $tipo->idTipo ?? $tipo->id; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirTipoAtividade(<?php echo $tipo->idTipo ?? $tipo->id; ?>, '<?php echo htmlspecialchars($tipo->nome); ?>')">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA: STATUS DE OBRA -->
            <div id="tab-status-obra" class="config-content">
                <div class="config-card">
                    <div class="config-card-header">
                        <div class="config-card-title">
                            <i class='bx bx-flag'></i>
                            Status de Obra
                        </div>
                        <button class="config-btn config-btn-primary config-btn-sm" onclick="abrirModalStatusObra()">
                            <i class='bx bx-plus'></i> Novo Status
                        </button>
                    </div>
                    <div class="config-card-body">
                        <div class="config-alert config-alert-warning">
                            <i class='bx bx-error-circle'></i>
                            <div>
                                <strong>Atenção:</strong> Alterar status padrão pode afetar relatórios e fluxos de trabalho existentes.
                            </div>
                        </div>

                        <div class="config-items-list" id="lista-status-obra">
                            <?php foreach ($status_obra as $status): ?>
                            <div class="config-item" data-id="<?php echo $status->id; ?>" data-ordem="<?php echo $status->ordem; ?>">
                                <div class="config-item-icon" style="background: <?php echo $status->cor; ?>20; color: <?php echo $status->cor; ?>;">
                                    <i class='bx <?php echo $status->icone; ?>'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <span class="status-badge-config" style="background: <?php echo $status->cor; ?>20; color: <?php echo $status->cor; ?>;">
                                            <?php echo htmlspecialchars($status->nome); ?>
                                        </span>
                                        <?php if ($status->finalizado): ?>
                                            <span style="margin-left: 8px; padding: 2px 8px; background: #e8f5e9; color: #27ae60; border-radius: 4px; font-size: 11px;">FINALIZADO</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($status->descricao ?? ''); ?> • Ordem: <?php echo $status->ordem; ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarStatusObra(<?php echo $status->id; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <?php if (!in_array($status->nome, ['Prospecção', 'Em Andamento', 'Concluída'])): ?>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirStatusObra(<?php echo $status->id; ?>, '<?php echo htmlspecialchars($status->nome); ?>')">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA: STATUS DE ATIVIDADE -->
            <div id="tab-status-atividade" class="config-content">
                <div class="config-card">
                    <div class="config-card-header">
                        <div class="config-card-title">
                            <i class='bx bx-check-circle'></i>
                            Status de Atividade
                        </div>
                        <button class="config-btn config-btn-primary config-btn-sm" onclick="abrirModalStatusAtividade()">
                            <i class='bx bx-plus'></i> Novo Status
                        </button>
                    </div>
                    <div class="config-card-body">
                        <div class="config-alert config-alert-info">
                            <i class='bx bx-info-circle'></i>
                            <div>
                                <strong>Fluxo padrão:</strong> Agendada → Iniciada → Pausada (opcional) → Concluída/Cancelada
                            </div>
                        </div>

                        <div class="config-items-list" id="lista-status-atividade">
                            <?php foreach ($status_atividade as $status): ?>
                            <div class="config-item" data-id="<?php echo $status->id; ?>">
                                <div class="config-item-icon" style="background: <?php echo $status->cor; ?>20; color: <?php echo $status->cor; ?>;">
                                    <i class='bx <?php echo $status->icone; ?>'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <span class="status-badge-config" style="background: <?php echo $status->cor; ?>20; color: <?php echo $status->cor; ?>;">
                                            <?php echo htmlspecialchars($status->nome); ?>
                                        </span>
                                        <span style="margin-left: 8px; padding: 2px 8px; background: #f0f0f0; border-radius: 4px; font-size: 11px; text-transform: uppercase;">
                                            <?php echo $status->fluxo ?? 'normal'; ?>
                                        </span>
                                    </div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($status->descricao ?? ''); ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarStatusAtividade(<?php echo $status->id; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <?php if (!in_array($status->nome, ['Agendada', 'Iniciada', 'Concluída'])): ?>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirStatusAtividade(<?php echo $status->id; ?>, '<?php echo htmlspecialchars($status->nome); ?>')">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA: ESPECIALIDADES -->
            <div id="tab-especialidades" class="config-content">
                <div class="config-card">
                    <div class="config-card-header">
                        <div class="config-card-title">
                            <i class='bx bx-hard-hat'></i>
                            Especialidades (Etapas)
                        </div>
                        <button class="config-btn config-btn-primary config-btn-sm" onclick="abrirModalEspecialidade()">
                            <i class='bx bx-plus'></i> Nova Especialidade
                        </button>
                    </div>
                    <div class="config-card-body">
                        <div class="config-alert config-alert-info">
                            <i class='bx bx-info-circle'></i>
                            <div>
                                <strong>Especialidades</strong> são usadas para classificar as etapas da obra (Ex: Elétrica, Hidráulica, Acabamento).
                            </div>
                        </div>

                        <div class="config-grid" id="lista-especialidades">
                            <?php foreach ($especialidades as $esp): ?>
                            <div class="config-grid-item" data-id="<?php echo $esp->id; ?>">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                    <div style="width: 48px; height: 48px; background: <?php echo $esp->cor; ?>20; color: <?php echo $esp->cor; ?>; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                        <i class='bx <?php echo $esp->icone; ?>'></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #333;"><?php echo htmlspecialchars($esp->nome); ?></div>
                                        <span class="tipo-color" style="background: <?php echo $esp->cor; ?>"></span>
                                    </div>
                                </div>
                                <div style="font-size: 13px; color: #666; margin-bottom: 16px;">
                                    <?php echo htmlspecialchars($esp->descricao ?? ''); ?>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button class="config-btn config-btn-secondary config-btn-sm" style="flex: 1;" onclick="editarEspecialidade(<?php echo $esp->id; ?>)">
                                        <i class='bx bx-edit'></i> Editar
                                    </button>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirEspecialidade(<?php echo $esp->id; ?>, '<?php echo htmlspecialchars($esp->nome); ?>')">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA: FUNCOES DA EQUIPE -->
            <div id="tab-funcoes" class="config-content">
                <div class="config-card">
                    <div class="config-card-header">
                        <div class="config-card-title">
                            <i class='bx bx-group'></i>
                            Funções da Equipe
                        </div>
                        <button class="config-btn config-btn-primary config-btn-sm" onclick="abrirModalFuncao()">
                            <i class='bx bx-plus'></i> Nova Função
                        </button>
                    </div>
                    <div class="config-card-body">
                        <div class="config-alert config-alert-info">
                            <i class='bx bx-info-circle'></i>
                            <div>
                                <strong>Funções</strong> definem os papéis dos membros da equipe na obra (Ex: Engenheiro, Mestre de Obras, Auxiliar).
                            </div>
                        </div>

                        <div class="config-items-list" id="lista-funcoes">
                            <?php foreach ($funcoes_equipe as $funcao):
                                $nivel_cor = ['alto' => '#e74c3c', 'medio' => '#f39c12', 'baixo' => '#27ae60'][$funcao->nivel ?? 'baixo'];
                            ?>
                            <div class="config-item" data-id="<?php echo $funcao->id; ?>">
                                <div class="config-item-icon" style="background: <?php echo $nivel_cor; ?>20; color: <?php echo $nivel_cor; ?>;">
                                    <i class='bx bx-user'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <?php echo htmlspecialchars($funcao->nome); ?>
                                        <span style="margin-left: 8px; padding: 2px 8px; background: <?php echo $nivel_cor; ?>20; color: <?php echo $nivel_cor; ?>; border-radius: 4px; font-size: 11px; text-transform: uppercase;">
                                            <?php echo $funcao->nivel ?? 'baixo'; ?>
                                        </span>
                                    </div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($funcao->descricao ?? ''); ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarFuncao(<?php echo $funcao->id; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirFuncao(<?php echo $funcao->id; ?>, '<?php echo htmlspecialchars($funcao->nome); ?>')">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA: NOTIFICACOES -->
            <div id="tab-notificacoes" class="config-content">
                <div class="config-card">
                    <div class="config-card-header">
                        <div class="config-card-title">
                            <i class='bx bx-bell'></i>
                            Configurações de Notificações
                        </div>
                    </div>
                    <div class="config-card-body">
                        <form id="form-config-notificacoes" method="post" action="<?php echo site_url('obras/salvarConfiguracaoNotificacoes'); ?>">
                            <h4 style="margin: 0 0 16px 0; color: #333;">Eventos que geram notificações</h4>

                            <div class="config-items-list">
                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Nova obra cadastrada</div>
                                        <div class="config-item-meta">Notificar gestores quando uma nova obra for criada</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="notif_nova_obra" <?php echo ($config_notif['nova_obra'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Obra concluída</div>
                                        <div class="config-item-meta">Notificar quando uma obra for finalizada</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="notif_obra_concluida" <?php echo ($config_notif['obra_concluida'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Atividade atrasada</div>
                                        <div class="config-item-meta">Alertar sobre atividades com prazo vencido</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="notif_atividade_atrasada" <?php echo ($config_notif['atividade_atrasada'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Atividade reaberta</div>
                                        <div class="config-item-meta">Notificar quando uma atividade for reaberta para reatendimento</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="notif_atividade_reaberta" <?php echo ($config_notif['atividade_reaberta'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Check-in do técnico</div>
                                        <div class="config-item-meta">Notificar entrada de técnicos na obra</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="notif_checkin" <?php echo ($config_notif['checkin'] ?? false) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Impedimento registrado</div>
                                        <div class="config-item-meta">Alerta imediato quando um técnico registrar impedimento</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="notif_impedimento" <?php echo ($config_notif['impedimento'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <hr style="border: none; border-top: 1px solid #f0f0f0; margin: 24px 0;">

                            <h4 style="margin: 0 0 16px 0; color: #333;">Canais de Notificação</h4>

                            <div class="config-grid" style="grid-template-columns: repeat(3, 1fr);">
                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">E-mail</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="canal_email" <?php echo ($config_notif['canal_email'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">WhatsApp</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="canal_whatsapp" <?php echo ($config_notif['canal_whatsapp'] ?? false) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="config-item">
                                    <div class="config-item-info">
                                        <div class="config-item-name">Notificação no Sistema</div>
                                    </div>
                                    <label class="config-toggle">
                                        <input type="checkbox" name="canal_sistema" <?php echo ($config_notif['canal_sistema'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div style="margin-top: 24px; text-align: right;">
                                <button type="submit" class="config-btn config-btn-primary">
                                    <i class='bx bx-save'></i> Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Modal Genérico -->
<div class="config-modal-overlay" id="modalConfig">
    <div class="config-modal">
        <div class="config-modal-header">
            <h3 id="modalTitle"><i class='bx bx-plus-circle'></i> <span>Novo Item</span></h3>
        </div>
        <div class="config-modal-body" id="modalBody">
            <!-- Conteúdo dinâmico -->
        </div>
        <div class="config-modal-footer">
            <button class="config-btn config-btn-secondary" onclick="fecharModal()">Cancelar</button>
            <button class="config-btn config-btn-primary" onclick="salvarModal()">
                <i class='bx bx-save'></i> Salvar
            </button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script type="text/javascript">
// Garantir que erros anteriores nao quebrem este script
(function() {
    'use strict';

    // Token CSRF
    var MAPOS_TOKEN = <?php echo json_encode($this->security->get_csrf_hash()); ?>;

    // ========== MODAL DE CONFIGURACOES ==========
    window.abrirModalConfiguracoes = function(abaInicial) {
    console.log('[DEBUG] abrirModalConfiguracoes chamada, abaInicial:', abaInicial);
    var modal = document.getElementById('modalConfiguracoes');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Ativar aba inicial
    if (abaInicial) {
        ativarAbaModal(abaInicial);
    } else {
        ativarAbaModal('tab-geral');
    }
}

function fecharModalConfiguracoes() {
    console.log('[DEBUG] fecharModalConfiguracoes chamada');
    var modal = document.getElementById('modalConfiguracoes');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Sistema de abas dentro do modal (sem hash, sem :target)
function ativarAbaModal(abaId) {
    console.log('[DEBUG] ativarAbaModal chamada com:', abaId);
    if (!abaId) abaId = 'tab-geral';
    if (abaId.charAt(0) === '#') abaId = abaId.substring(1);

    // Destacar aba no menu
    var modal = document.getElementById('modalConfiguracoes');
    var abas = modal.querySelectorAll('.config-tab-item');
    abas.forEach(function(aba) {
        aba.classList.remove('active');
        if (aba.getAttribute('data-tab') === abaId) {
            aba.classList.add('active');
        }
    });

    // Mostrar conteudo correspondente
    var conteudos = modal.querySelectorAll('.config-content');
    conteudos.forEach(function(c) {
        c.classList.remove('ativo');
    });

    var alvo = modal.querySelector('#' + abaId);
    if (alvo) {
        console.log('[DEBUG] Aba encontrada no modal, ativando:', abaId);
        alvo.classList.add('ativo');
    } else {
        console.warn('[DEBUG] Aba NAO encontrada no modal, fallback para tab-geral. Buscado:', abaId);
        var fallback = modal.querySelector('#tab-geral');
        if (fallback) fallback.classList.add('ativo');
    }
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    var modal = document.getElementById('modalConfiguracoes');
    if (event.target === modal) {
        fecharModalConfiguracoes();
    }
    // Fechar modal generico tambem
    if (event.target.classList.contains('config-modal-overlay')) {
        fecharModal();
    }
};

// Atalho ESC para fechar modal de configuracoes
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalConfiguracoes();
    }
});

// Variáveis do modal
let modalAtual = null;
let itemEditando = null;

// Funções do modal
function abrirModal(titulo, conteudoHtml) {
    document.getElementById('modalTitle').innerHTML = titulo;
    document.getElementById('modalBody').innerHTML = conteudoHtml;
    document.getElementById('modalConfig').classList.add('active');
}

function fecharModal() {
    document.getElementById('modalConfig').classList.remove('active');
    modalAtual = null;
    itemEditando = null;
}

function mostrarLoading() {
    document.getElementById('loadingOverlay').classList.add('active');
}

function ocultarLoading() {
    document.getElementById('loadingOverlay').classList.remove('active');
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    if (event.target.classList.contains('config-modal-overlay')) {
        fecharModal();
    }
};

// Atalho ESC para fechar modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModal();
    }
});

// ===================== TIPOS DE OBRA =====================

function abrirModalTipoObra() {
    modalAtual = 'tipo-obra';
    itemEditando = null;

    var html = `
        <input type="hidden" id="tipo_obra_id" value="">
        <div class="config-form-group">
            <label>Nome do Tipo</label>
            <input type="text" id="tipo_obra_nome" class="config-form-control" placeholder="Ex: Reforma, Construção..." required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="tipo_obra_descricao" class="config-form-control" rows="2" placeholder="Descrição opcional..."></textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="tipo_obra_cor" value="#3498db">
                    <input type="text" id="tipo_obra_cor_texto" class="config-form-control" value="#3498db" onchange="document.getElementById('tipo_obra_cor').value = this.value">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="tipo_obra_icone" class="config-form-control">
                    <option value="bx-building">Prédio</option>
                    <option value="bx-home">Casa</option>
                    <option value="bx-brush">Pincel</option>
                    <option value="bx-wrench">Ferramenta</option>
                    <option value="bx-plug">Plug</option>
                    <option value="bx-box">Caixa</option>
                    <option value="bx-hard-hat">Capacete</option>
                </select>
            </div>
        </div>
    `;

    abrirModal("<i class='bx bx-plus-circle'></i> Novo Tipo de Obra", html);
}

function editarTipoObra(id) {
    modalAtual = 'tipo-obra';
    itemEditando = id;

    // Buscar dados do item
    var item = document.querySelector('[data-id="' + id + '"]');
    if (!item) return;

    var nome = item.querySelector('.config-item-name').textContent.trim();
    var descricao = item.querySelector('.config-item-meta')?.textContent || '';
    var cor = item.querySelector('.tipo-color')?.style.backgroundColor || '#3498db';

    var html = `
        <input type="hidden" id="tipo_obra_id" value="${id}">
        <div class="config-form-group">
            <label>Nome do Tipo</label>
            <input type="text" id="tipo_obra_nome" class="config-form-control" value="${nome}" required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="tipo_obra_descricao" class="config-form-control" rows="2">${descricao}</textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="tipo_obra_cor" value="${cor}">
                    <input type="text" id="tipo_obra_cor_texto" class="config-form-control" value="${cor}" onchange="document.getElementById('tipo_obra_cor').value = this.value">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="tipo_obra_icone" class="config-form-control">
                    <option value="bx-building">Prédio</option>
                    <option value="bx-home">Casa</option>
                    <option value="bx-brush">Pincel</option>
                    <option value="bx-wrench">Ferramenta</option>
                    <option value="bx-plug">Plug</option>
                    <option value="bx-box">Caixa</option>
                    <option value="bx-hard-hat">Capacete</option>
                </select>
            </div>
        </div>
    `;

    abrirModal("<i class='bx bx-edit'></i> Editar Tipo de Obra", html);
}

function excluirTipoObra(id, nome) {
    if (!confirm('Tem certeza que deseja excluir o tipo "' + nome + '"?\n\nEsta ação não pode ser desfeita.')) {
        return;
    }

    mostrarLoading();

    var formData = new FormData();
    formData.append('MAPOS_TOKEN', MAPOS_TOKEN);
    formData.append('id', id);

    console.log('[DEBUG] excluirTipoObra - enviando fetch para:', '<?php echo site_url("obras/excluirTipoObra"); ?>');
    fetch('<?php echo site_url("obras/excluirTipoObra"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(r) {
        console.log('[DEBUG] excluirTipoObra - status:', r.status, 'content-type:', r.headers.get('content-type'));
        return r.text().then(function(text) {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('[DEBUG] excluirTipoObra - resposta NAO e JSON:', text.substring(0, 500));
                throw new Error('Resposta do servidor nao e JSON valido');
            }
        });
    })
    .then(data => {
        ocultarLoading();
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível excluir'));
        }
    })
    .catch(err => {
        ocultarLoading();
        console.error('[DEBUG] excluirTipoObra - erro no fetch:', err);
        alert('Erro ao excluir. Tente novamente.');
    });
}

// ===================== TIPOS DE ATIVIDADE =====================

function abrirModalTipoAtividade() {
    modalAtual = 'tipo-atividade';
    itemEditando = null;

    var html = `
        <input type="hidden" id="tipo_atividade_id" value="">
        <div class="config-form-group">
            <label>Nome do Tipo</label>
            <input type="text" id="tipo_atividade_nome" class="config-form-control" placeholder="Ex: Trabalho, Visita Técnica..." required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="tipo_atividade_descricao" class="config-form-control" rows="2" placeholder="Descrição opcional..."></textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Categoria</label>
                <select id="tipo_atividade_categoria" class="config-form-control">
                    <option value="execucao">Execução</option>
                    <option value="visita">Visita</option>
                    <option value="manutencao">Manutenção</option>
                    <option value="impedimento">Impedimento</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div class="config-form-group">
                <label>Duração Estimada (min)</label>
                <input type="number" id="tipo_atividade_duracao" class="config-form-control" value="30" min="5">
            </div>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="tipo_atividade_cor" value="#3498db">
                    <input type="text" class="config-form-control" value="#3498db" onchange="document.getElementById('tipo_atividade_cor').value = this.value">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="tipo_atividade_icone" class="config-form-control">
                    <option value="bx-wrench">Ferramenta</option>
                    <option value="bx-search">Lupa</option>
                    <option value="bx-cog">Engrenagem</option>
                    <option value="bx-block">Bloqueio</option>
                    <option value="bx-task">Tarefa</option>
                    <option value="bx-check-circle">Check</option>
                </select>
            </div>
        </div>
    `;

    abrirModal("<i class='bx bx-plus-circle'></i> Novo Tipo de Atividade", html);
}

function editarTipoAtividade(id) {
    modalAtual = 'tipo-atividade';
    itemEditando = id;

    var item = document.querySelector('#lista-tipos-atividade [data-id="' + id + '"]');
    if (!item) return;

    var nome = item.querySelector('.config-item-name').childNodes[0].textContent.trim();
    var descricao = item.querySelector('.config-item-meta')?.textContent || '';

    var html = `
        <input type="hidden" id="tipo_atividade_id" value="${id}">
        <div class="config-form-group">
            <label>Nome do Tipo</label>
            <input type="text" id="tipo_atividade_nome" class="config-form-control" value="${nome}" required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="tipo_atividade_descricao" class="config-form-control" rows="2">${descricao}</textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Categoria</label>
                <select id="tipo_atividade_categoria" class="config-form-control">
                    <option value="execucao">Execução</option>
                    <option value="visita">Visita</option>
                    <option value="manutencao">Manutenção</option>
                    <option value="impedimento">Impedimento</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div class="config-form-group">
                <label>Duração Estimada (min)</label>
                <input type="number" id="tipo_atividade_duracao" class="config-form-control" value="30" min="5">
            </div>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="tipo_atividade_cor" value="#3498db">
                    <input type="text" class="config-form-control" value="#3498db">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="tipo_atividade_icone" class="config-form-control">
                    <option value="bx-wrench">Ferramenta</option>
                    <option value="bx-search">Lupa</option>
                    <option value="bx-cog">Engrenagem</option>
                    <option value="bx-block">Bloqueio</option>
                    <option value="bx-task">Tarefa</option>
                    <option value="bx-check-circle">Check</option>
                </select>
            </div>
        </div>
    `;

    abrirModal("<i class='bx bx-edit'></i> Editar Tipo de Atividade", html);
}

function excluirTipoAtividade(id, nome) {
    if (!confirm('Tem certeza que deseja excluir "' + nome + '"?')) {
        return;
    }

    mostrarLoading();

    var formData = new FormData();
    formData.append('MAPOS_TOKEN', MAPOS_TOKEN);
    formData.append('id', id);

    console.log('[DEBUG] excluirTipoAtividade - enviando fetch');
    fetch('<?php echo site_url("obras/excluirTipoAtividade"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(r) {
        console.log('[DEBUG] excluirTipoAtividade - status:', r.status);
        return r.text().then(function(text) {
            try { return JSON.parse(text); }
            catch (e) {
                console.error('[DEBUG] excluirTipoAtividade - resposta NAO e JSON:', text.substring(0, 500));
                throw new Error('Resposta do servidor nao e JSON valido');
            }
        });
    })
    .then(data => {
        ocultarLoading();
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível excluir'));
        }
    })
    .catch(err => {
        ocultarLoading();
        console.error('[DEBUG] excluirTipoAtividade - erro no fetch:', err);
        alert('Erro ao excluir. Tente novamente.');
    });
}

// ===================== STATUS DE OBRA =====================

function abrirModalStatusObra() {
    modalAtual = 'status-obra';
    itemEditando = null;

    var html = `
        <input type="hidden" id="status_obra_id" value="">
        <div class="config-form-group">
            <label>Nome do Status</label>
            <input type="text" id="status_obra_nome" class="config-form-control" placeholder="Ex: Em Andamento, Concluída..." required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="status_obra_descricao" class="config-form-control" rows="2"></textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="status_obra_cor" value="#3498db">
                    <input type="text" class="config-form-control" value="#3498db">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="status_obra_icone" class="config-form-control">
                    <option value="bx-flag">Bandeira</option>
                    <option value="bx-play-circle">Play</option>
                    <option value="bx-pause-circle">Pause</option>
                    <option value="bx-check-circle">Check</option>
                    <option value="bx-x-circle">X</option>
                    <option value="bx-search">Busca</option>
                </select>
            </div>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Ordem</label>
                <input type="number" id="status_obra_ordem" class="config-form-control" value="1" min="1">
            </div>
            <div class="config-form-group" style="display: flex; align-items: center; padding-top: 24px;">
                <label class="config-toggle" style="cursor: pointer;">
                    <input type="checkbox" id="status_obra_finalizado">
                    <span class="toggle-slider"></span>
                    <span style="margin-left: 12px;">Status Finalizado</span>
                </label>
            </div>
        </div>
    `;

    abrirModal("<i class='bx bx-plus-circle'></i> Novo Status de Obra", html);
}

function editarStatusObra(id) {
    modalAtual = 'status-obra';
    itemEditando = id;

    var item = document.querySelector('#lista-status-obra [data-id="' + id + '"]');
    if (!item) return;

    var nome = item.querySelector('.config-item-name').textContent.replace('FINALIZADO', '').trim();
    var descricao = item.querySelector('.config-item-meta')?.textContent.split('•')[0] || '';
    var finalizado = item.querySelector('.config-item-name').textContent.includes('FINALIZADO');

    var html = `
        <input type="hidden" id="status_obra_id" value="${id}">
        <div class="config-form-group">
            <label>Nome do Status</label>
            <input type="text" id="status_obra_nome" class="config-form-control" value="${nome}" required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="status_obra_descricao" class="config-form-control" rows="2">${descricao}</textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="status_obra_cor" value="#3498db">
                    <input type="text" class="config-form-control" value="#3498db">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="status_obra_icone" class="config-form-control">
                    <option value="bx-flag">Bandeira</option>
                    <option value="bx-play-circle">Play</option>
                    <option value="bx-pause-circle">Pause</option>
                    <option value="bx-check-circle">Check</option>
                    <option value="bx-x-circle">X</option>
                    <option value="bx-search">Busca</option>
                </select>
            </div>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Ordem</label>
                <input type="number" id="status_obra_ordem" class="config-form-control" value="1" min="1">
            </div>
            <div class="config-form-group" style="display: flex; align-items: center; padding-top: 24px;">
                <label class="config-toggle" style="cursor: pointer;">
                    <input type="checkbox" id="status_obra_finalizado" ${finalizado ? 'checked' : ''}>
                    <span class="toggle-slider"></span>
                    <span style="margin-left: 12px;">Status Finalizado</span>
                </label>
            </div>
        </div>
    `;

    abrirModal("<i class='bx bx-edit'></i> Editar Status de Obra", html);
}

function excluirStatusObra(id, nome) {
    if (!confirm('Excluir status "' + nome + '"?')) return;

    mostrarLoading();
    var formData = new FormData();
    formData.append('MAPOS_TOKEN', MAPOS_TOKEN);
    formData.append('id', id);

    console.log('[DEBUG] excluirStatusObra - enviando fetch');
    fetch('<?php echo site_url("obras/excluirStatusObra"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(r) {
        console.log('[DEBUG] excluirStatusObra - status:', r.status);
        return r.text().then(function(text) {
            try { return JSON.parse(text); }
            catch (e) {
                console.error('[DEBUG] excluirStatusObra - resposta NAO e JSON:', text.substring(0, 500));
                throw new Error('Resposta do servidor nao e JSON valido');
            }
        });
    })
    .then(data => {
        ocultarLoading();
        if (data.success) location.reload();
        else alert('Erro: ' + (data.message || 'Não foi possível excluir'));
    })
    .catch(err => {
        ocultarLoading();
        console.error('[DEBUG] excluirStatusObra - erro no fetch:', err);
        alert('Erro ao excluir');
    });
}

// ===================== STATUS DE ATIVIDADE =====================

function abrirModalStatusAtividade() {
    modalAtual = 'status-atividade';
    itemEditando = null;

    var html = `
        <input type="hidden" id="status_atividade_id" value="">
        <div class="config-form-group">
            <label>Nome do Status</label>
            <input type="text" id="status_atividade_nome" class="config-form-control" placeholder="Ex: Agendada, Em Andamento..." required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="status_atividade_descricao" class="config-form-control" rows="2"></textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="status_atividade_cor" value="#3498db">
                    <input type="text" class="config-form-control" value="#3498db">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="status_atividade_icone" class="config-form-control">
                    <option value="bx-calendar">Calendário</option>
                    <option value="bx-play-circle">Play</option>
                    <option value="bx-pause-circle">Pause</option>
                    <option value="bx-check-circle">Check</option>
                    <option value="bx-x-circle">X</option>
                    <option value="bx-refresh">Refresh</option>
                </select>
            </div>
        </div>
        <div class="config-form-group">
            <label>Fluxo do Status</label>
            <select id="status_atividade_fluxo" class="config-form-control">
                <option value="inicial">Inicial (agendada)</option>
                <option value="execucao">Em Execução</option>
                <option value="final">Final (concluída/cancelada)</option>
                <option value="especial">Especial</option>
            </select>
        </div>
    `;

    abrirModal("<i class='bx bx-plus-circle'></i> Novo Status de Atividade", html);
}

function editarStatusAtividade(id) {
    modalAtual = 'status-atividade';
    itemEditando = id;

    var item = document.querySelector('#lista-status-atividade [data-id="' + id + '"]');
    if (!item) return;

    var nome = item.querySelector('.config-item-name').childNodes[0].textContent.trim();
    var descricao = item.querySelector('.config-item-meta')?.textContent || '';

    var html = `
        <input type="hidden" id="status_atividade_id" value="${id}">
        <div class="config-form-group">
            <label>Nome do Status</label>
            <input type="text" id="status_atividade_nome" class="config-form-control" value="${nome}" required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="status_atividade_descricao" class="config-form-control" rows="2">${descricao}</textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="status_atividade_cor" value="#3498db">
                    <input type="text" class="config-form-control" value="#3498db">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="status_atividade_icone" class="config-form-control">
                    <option value="bx-calendar">Calendário</option>
                    <option value="bx-play-circle">Play</option>
                    <option value="bx-pause-circle">Pause</option>
                    <option value="bx-check-circle">Check</option>
                    <option value="bx-x-circle">X</option>
                    <option value="bx-refresh">Refresh</option>
                </select>
            </div>
        </div>
        <div class="config-form-group">
            <label>Fluxo do Status</label>
            <select id="status_atividade_fluxo" class="config-form-control">
                <option value="inicial">Inicial (agendada)</option>
                <option value="execucao">Em Execução</option>
                <option value="final">Final (concluída/cancelada)</option>
                <option value="especial">Especial</option>
            </select>
        </div>
    `;

    abrirModal("<i class='bx bx-edit'></i> Editar Status de Atividade", html);
}

function excluirStatusAtividade(id, nome) {
    if (!confirm('Excluir "' + nome + '"?')) return;

    mostrarLoading();
    var formData = new FormData();
    formData.append('MAPOS_TOKEN', MAPOS_TOKEN);
    formData.append('id', id);

    console.log('[DEBUG] excluirStatusAtividade - enviando fetch');
    fetch('<?php echo site_url("obras/excluirStatusAtividade"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(r) {
        console.log('[DEBUG] excluirStatusAtividade - status:', r.status);
        return r.text().then(function(text) {
            try { return JSON.parse(text); }
            catch (e) {
                console.error('[DEBUG] excluirStatusAtividade - resposta NAO e JSON:', text.substring(0, 500));
                throw new Error('Resposta do servidor nao e JSON valido');
            }
        });
    })
    .then(data => {
        ocultarLoading();
        if (data.success) location.reload();
        else alert('Erro: ' + (data.message || 'Erro ao excluir'));
    })
    .catch(err => {
        ocultarLoading();
        console.error('[DEBUG] excluirStatusAtividade - erro no fetch:', err);
        alert('Erro ao excluir');
    });
}

// ===================== ESPECIALIDADES =====================

function abrirModalEspecialidade() {
    modalAtual = 'especialidade';
    itemEditando = null;

    var html = `
        <input type="hidden" id="especialidade_id" value="">
        <div class="config-form-group">
            <label>Nome da Especialidade</label>
            <input type="text" id="especialidade_nome" class="config-form-control" placeholder="Ex: Elétrica, Hidráulica..." required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="especialidade_descricao" class="config-form-control" rows="2"></textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="especialidade_cor" value="#3498db">
                    <input type="text" class="config-form-control" value="#3498db">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="especialidade_icone" class="config-form-control">
                    <option value="bx-bolt-circle">Raio</option>
                    <option value="bx-water">Água</option>
                    <option value="bx-building">Prédio</option>
                    <option value="bx-paint">Pincel</option>
                    <option value="bx-leaf">Folha</option>
                    <option value="bx-shield">Escudo</option>
                    <option value="bx-hard-hat">Capacete</option>
                </select>
            </div>
        </div>
    `;

    abrirModal("<i class='bx bx-plus-circle'></i> Nova Especialidade", html);
}

function editarEspecialidade(id) {
    modalAtual = 'especialidade';
    itemEditando = id;

    var item = document.querySelector('#lista-especialidades [data-id="' + id + '"]');
    if (!item) return;

    var nome = item.querySelector('.config-item-name div').textContent.trim();
    var descricao = item.querySelector('.config-grid-item > div:nth-child(2)')?.textContent.trim() || '';

    var html = `
        <input type="hidden" id="especialidade_id" value="${id}">
        <div class="config-form-group">
            <label>Nome da Especialidade</label>
            <input type="text" id="especialidade_nome" class="config-form-control" value="${nome}" required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="especialidade_descricao" class="config-form-control" rows="2">${descricao}</textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="config-form-group">
                <label>Cor</label>
                <div class="color-picker-wrapper">
                    <input type="color" id="especialidade_cor" value="#3498db">
                    <input type="text" class="config-form-control" value="#3498db">
                </div>
            </div>
            <div class="config-form-group">
                <label>Ícone</label>
                <select id="especialidade_icone" class="config-form-control">
                    <option value="bx-bolt-circle">Raio</option>
                    <option value="bx-water">Água</option>
                    <option value="bx-building">Prédio</option>
                    <option value="bx-paint">Pincel</option>
                    <option value="bx-leaf">Folha</option>
                    <option value="bx-shield">Escudo</option>
                    <option value="bx-hard-hat">Capacete</option>
                </select>
            </div>
        </div>
    `;

    abrirModal("<i class='bx bx-edit'></i> Editar Especialidade", html);
}

function excluirEspecialidade(id, nome) {
    if (!confirm('Excluir "' + nome + '"?')) return;

    mostrarLoading();
    var formData = new FormData();
    formData.append('MAPOS_TOKEN', MAPOS_TOKEN);
    formData.append('id', id);

    console.log('[DEBUG] excluirEspecialidade - enviando fetch');
    fetch('<?php echo site_url("obras/excluirEspecialidade"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(r) {
        console.log('[DEBUG] excluirEspecialidade - status:', r.status);
        return r.text().then(function(text) {
            try { return JSON.parse(text); }
            catch (e) {
                console.error('[DEBUG] excluirEspecialidade - resposta NAO e JSON:', text.substring(0, 500));
                throw new Error('Resposta do servidor nao e JSON valido');
            }
        });
    })
    .then(data => {
        ocultarLoading();
        if (data.success) location.reload();
        else alert('Erro: ' + (data.message || 'Erro ao excluir'));
    })
    .catch(err => {
        ocultarLoading();
        console.error('[DEBUG] excluirEspecialidade - erro no fetch:', err);
        alert('Erro ao excluir');
    });
}

// ===================== FUNCOES DA EQUIPE =====================

function abrirModalFuncao() {
    modalAtual = 'funcao';
    itemEditando = null;

    var html = `
        <input type="hidden" id="funcao_id" value="">
        <div class="config-form-group">
            <label>Nome da Função</label>
            <input type="text" id="funcao_nome" class="config-form-control" placeholder="Ex: Engenheiro, Pedreiro..." required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="funcao_descricao" class="config-form-control" rows="2"></textarea>
        </div>
        <div class="config-form-group">
            <label>Nível Hierárquico</label>
            <select id="funcao_nivel" class="config-form-control">
                <option value="alto">Alto (Gestão)</option>
                <option value="medio">Médio (Coordenação)</option>
                <option value="baixo">Baixo (Operacional)</option>
            </select>
        </div>
    `;

    abrirModal("<i class='bx bx-plus-circle'></i> Nova Função", html);
}

function editarFuncao(id) {
    modalAtual = 'funcao';
    itemEditando = id;

    var item = document.querySelector('#lista-funcoes [data-id="' + id + '"]');
    if (!item) return;

    var nome = item.querySelector('.config-item-name').childNodes[0].textContent.trim();
    var descricao = item.querySelector('.config-item-meta')?.textContent || '';
    var nivel = item.querySelector('.config-item-name span')?.textContent.trim().toLowerCase() || 'baixo';

    var html = `
        <input type="hidden" id="funcao_id" value="${id}">
        <div class="config-form-group">
            <label>Nome da Função</label>
            <input type="text" id="funcao_nome" class="config-form-control" value="${nome}" required>
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea id="funcao_descricao" class="config-form-control" rows="2">${descricao}</textarea>
        </div>
        <div class="config-form-group">
            <label>Nível Hierárquico</label>
            <select id="funcao_nivel" class="config-form-control">
                <option value="alto" ${nivel === 'alto' ? 'selected' : ''}>Alto (Gestão)</option>
                <option value="medio" ${nivel === 'médio' ? 'selected' : ''}>Médio (Coordenação)</option>
                <option value="baixo" ${nivel === 'baixo' ? 'selected' : ''}>Baixo (Operacional)</option>
            </select>
        </div>
    `;

    abrirModal("<i class='bx bx-edit'></i> Editar Função", html);
}

function excluirFuncao(id, nome) {
    if (!confirm('Excluir "' + nome + '"?')) return;

    mostrarLoading();
    var formData = new FormData();
    formData.append('MAPOS_TOKEN', MAPOS_TOKEN);
    formData.append('id', id);

    console.log('[DEBUG] excluirFuncao - enviando fetch');
    fetch('<?php echo site_url("obras/excluirFuncao"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(r) {
        console.log('[DEBUG] excluirFuncao - status:', r.status);
        return r.text().then(function(text) {
            try { return JSON.parse(text); }
            catch (e) {
                console.error('[DEBUG] excluirFuncao - resposta NAO e JSON:', text.substring(0, 500));
                throw new Error('Resposta do servidor nao e JSON valido');
            }
        });
    })
    .then(data => {
        ocultarLoading();
        if (data.success) location.reload();
        else alert('Erro: ' + (data.message || 'Erro ao excluir'));
    })
    .catch(err => {
        ocultarLoading();
        console.error('[DEBUG] excluirFuncao - erro no fetch:', err);
        alert('Erro ao excluir');
    });
}

// ===================== SALVAR MODAL =====================

function salvarModal() {
    if (!modalAtual) return;

    mostrarLoading();

    var formData = new FormData();
    formData.append('MAPOS_TOKEN', MAPOS_TOKEN);

    var url = '';

    switch(modalAtual) {
        case 'tipo-obra':
            url = '<?php echo site_url("obras/salvarTipoObra"); ?>';
            formData.append('id', document.getElementById('tipo_obra_id')?.value || '');
            formData.append('nome', document.getElementById('tipo_obra_nome')?.value || '');
            formData.append('descricao', document.getElementById('tipo_obra_descricao')?.value || '');
            formData.append('cor', document.getElementById('tipo_obra_cor')?.value || '#3498db');
            formData.append('icone', document.getElementById('tipo_obra_icone')?.value || 'bx-building');
            break;

        case 'tipo-atividade':
            url = '<?php echo site_url("obras/salvarTipoAtividade"); ?>';
            formData.append('id', document.getElementById('tipo_atividade_id')?.value || '');
            formData.append('nome', document.getElementById('tipo_atividade_nome')?.value || '');
            formData.append('descricao', document.getElementById('tipo_atividade_descricao')?.value || '');
            formData.append('categoria', document.getElementById('tipo_atividade_categoria')?.value || 'outro');
            formData.append('duracao', document.getElementById('tipo_atividade_duracao')?.value || 30);
            formData.append('cor', document.getElementById('tipo_atividade_cor')?.value || '#3498db');
            formData.append('icone', document.getElementById('tipo_atividade_icone')?.value || 'bx-wrench');
            break;

        case 'status-obra':
            url = '<?php echo site_url("obras/salvarStatusObra"); ?>';
            formData.append('id', document.getElementById('status_obra_id')?.value || '');
            formData.append('nome', document.getElementById('status_obra_nome')?.value || '');
            formData.append('descricao', document.getElementById('status_obra_descricao')?.value || '');
            formData.append('cor', document.getElementById('status_obra_cor')?.value || '#3498db');
            formData.append('icone', document.getElementById('status_obra_icone')?.value || 'bx-flag');
            formData.append('ordem', document.getElementById('status_obra_ordem')?.value || 1);
            formData.append('finalizado', document.getElementById('status_obra_finalizado')?.checked ? 1 : 0);
            break;

        case 'status-atividade':
            url = '<?php echo site_url("obras/salvarStatusAtividade"); ?>';
            formData.append('id', document.getElementById('status_atividade_id')?.value || '');
            formData.append('nome', document.getElementById('status_atividade_nome')?.value || '');
            formData.append('descricao', document.getElementById('status_atividade_descricao')?.value || '');
            formData.append('cor', document.getElementById('status_atividade_cor')?.value || '#3498db');
            formData.append('icone', document.getElementById('status_atividade_icone')?.value || 'bx-calendar');
            formData.append('fluxo', document.getElementById('status_atividade_fluxo')?.value || 'normal');
            break;

        case 'especialidade':
            url = '<?php echo site_url("obras/salvarEspecialidade"); ?>';
            formData.append('id', document.getElementById('especialidade_id')?.value || '');
            formData.append('nome', document.getElementById('especialidade_nome')?.value || '');
            formData.append('descricao', document.getElementById('especialidade_descricao')?.value || '');
            formData.append('cor', document.getElementById('especialidade_cor')?.value || '#3498db');
            formData.append('icone', document.getElementById('especialidade_icone')?.value || 'bx-hard-hat');
            break;

        case 'funcao':
            url = '<?php echo site_url("obras/salvarFuncao"); ?>';
            formData.append('id', document.getElementById('funcao_id')?.value || '');
            formData.append('nome', document.getElementById('funcao_nome')?.value || '');
            formData.append('descricao', document.getElementById('funcao_descricao')?.value || '');
            formData.append('nivel', document.getElementById('funcao_nivel')?.value || 'baixo');
            break;
    }

    // Validação básica
    var nome = formData.get('nome');
    if (!nome || nome.trim() === '') {
        ocultarLoading();
        alert('Por favor, preencha o nome.');
        return;
    }

    console.log('[DEBUG] salvarModal - enviando para:', url, 'modalAtual:', modalAtual);
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(function(r) {
        console.log('[DEBUG] salvarModal - status:', r.status, 'content-type:', r.headers.get('content-type'));
        return r.text().then(function(text) {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('[DEBUG] salvarModal - resposta NAO e JSON. Primeiros 500 chars:', text.substring(0, 500));
                throw new Error('Resposta do servidor nao e JSON valido');
            }
        });
    })
    .then(data => {
        ocultarLoading();
        if (data.success) {
            fecharModal();
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível salvar'));
        }
    })
    .catch(err => {
        ocultarLoading();
        console.error(err);
        alert('Erro ao salvar. Tente novamente.');
    });
}

// Sincronizar color picker com input text
document.addEventListener('input', function(e) {
    if (e.target.type === 'color') {
        var textInput = e.target.parentElement.querySelector('input[type="text"]');
        if (textInput) textInput.value = e.target.value;
    }
});

// Expor funcoes globais para onclick no HTML
window.fecharModalConfiguracoes = fecharModalConfiguracoes;
window.ativarAbaModal = ativarAbaModal;
window.fecharModal = fecharModal;
window.salvarModal = salvarModal;
window.abrirModal = abrirModal;
})();
</script>
