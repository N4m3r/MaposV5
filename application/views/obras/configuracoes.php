<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ===== ESTILOS DA PÁGINA DE CONFIGURAÇÕES ===== */
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

/* Layout com Abas */
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

/* Conteúdo das Abas */
.config-content {
    display: none;
}

.config-content.active {
    display: block;
}

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

/* Formulários */
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

.config-form-control:disabled {
    background: #f5f5f5;
    cursor: not-allowed;
}

select.config-form-control {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
}

/* Lista de Itens Configuráveis */
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
    background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
    color: white;
}

.config-btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.config-btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    justify-content: center;
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

.config-grid-item.active {
    border-color: #27ae60;
    background: #f0fff4;
}

.config-grid-item.inactive {
    opacity: 0.7;
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
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

/* Responsivo */
@media (max-width: 992px) {
    .config-layout {
        grid-template-columns: 1fr;
    }

    .config-tabs {
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
}

/* DARK MODE */
body[data-theme="dark"] .config-tabs,
body[data-theme="dark"] .config-card,
body[data-theme="dark"] .config-modal {
    background: #1a1d29;
}

body[data-theme="dark"] .config-tab-item {
    color: #a0aec0;
    border-bottom-color: #2d3347;
}

body[data-theme="dark"] .config-tab-item:hover {
    background: #252a3a;
    color: #667eea;
}

body[data-theme="dark"] .config-tab-item.active {
    background: #252a3a;
    color: #667eea;
}

body[data-theme="dark"] .config-card-header,
body[data-theme="dark"] .config-modal-footer {
    border-color: #2d3347;
}

body[data-theme="dark"] .config-card-title,
body[data-theme="dark"] .config-item-name {
    color: #e2e8f0;
}

body[data-theme="dark"] .config-item {
    background: #252a3a;
}

body[data-theme="dark"] .config-item:hover {
    background: #1a1d29;
}

body[data-theme="dark"] .config-form-control {
    background: #1a1d29;
    border-color: #2d3347;
    color: #e2e8f0;
}

body[data-theme="dark"] .config-grid-item {
    background: #252a3a;
}

body[data-theme="dark"] .config-grid-item:hover {
    background: #1a1d29;
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

    <!-- Layout com Abas -->
    <div class="config-layout">
        <!-- Sidebar de Abas -->
        <div class="config-tabs">
            <div class="config-tab-item active" onclick="switchTab('geral', this)">
                <i class='bx bx-slider'></i>
                <span>Geral</span>
            </div>
            <div class="config-tab-item" onclick="switchTab('tipos-obra', this)">
                <i class='bx bx-building-house'></i>
                <span>Tipos de Obra</span>
                <span class="badge-count"><?php echo count($tipos_obra ?? ['Reforma', 'Construcao', 'Instalacao', 'Manutencao', 'Outro']); ?></span>
            </div>
            <div class="config-tab-item" onclick="switchTab('tipos-atividade', this)">
                <i class='bx bx-task'></i>
                <span>Tipos de Atividade</span>
                <span class="badge-count"><?php echo count($tipos_atividades ?? []); ?></span>
            </div>
            <div class="config-tab-item" onclick="switchTab('status-obra', this)">
                <i class='bx bx-flag'></i>
                <span>Status de Obra</span>
                <span class="badge-count"><?php echo count($status_obra ?? ['Prospeccao', 'Em Andamento', 'Paralisada', 'Concluida', 'Cancelada']); ?></span>
            </div>
            <div class="config-tab-item" onclick="switchTab('status-atividade', this)">
                <i class='bx bx-check-circle'></i>
                <span>Status de Atividade</span>
                <span class="badge-count"><?php echo count($status_atividade ?? ['agendada', 'iniciada', 'pausada', 'concluida', 'cancelada', 'reaberta']); ?></span>
            </div>
            <div class="config-tab-item" onclick="switchTab('especialidades', this)">
                <i class='bx bx-hard-hat'></i>
                <span>Especialidades</span>
                <span class="badge-count"><?php echo count($especialidades ?? []); ?></span>
            </div>
            <div class="config-tab-item" onclick="switchTab('funcoes', this)">
                <i class='bx bx-group'></i>
                <span>Funções da Equipe</span>
                <span class="badge-count"><?php echo count($funcoes_equipe ?? []); ?></span>
            </div>
            <div class="config-tab-item" onclick="switchTab('notificacoes', this)">
                <i class='bx bx-bell'></i>
                <span>Notificações</span>
            </div>
            <div class="config-tab-item" onclick="switchTab('permissoes', this)">
                <i class='bx bx-lock'></i>
                <span>Permissões</span>
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="config-content-wrapper">
            <!-- ABA: GERAL -->
            <div id="tab-geral" class="config-content active">
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

                        <div class="config-items-list">
                            <?php
                            $tipos_obra_padrao = [
                                ['id' => 1, 'nome' => 'Reforma', 'descricao' => 'Reformas e renovações', 'cor' => '#3498db', 'icone' => 'bx-brush'],
                                ['id' => 2, 'nome' => 'Construção', 'descricao' => 'Obras novas', 'cor' => '#27ae60', 'icone' => 'bx-building'],
                                ['id' => 3, 'nome' => 'Instalação', 'descricao' => 'Instalações técnicas', 'cor' => '#9b59b6', 'icone' => 'bx-plug'],
                                ['id' => 4, 'nome' => 'Manutenção', 'descricao' => 'Manutenções corretivas e preventivas', 'cor' => '#f39c12', 'icone' => 'bx-wrench'],
                                ['id' => 5, 'nome' => 'Outro', 'descricao' => 'Outros tipos', 'cor' => '#95a5a6', 'icone' => 'bx-box'],
                            ];
                            $tipos = $tipos_obra ?? $tipos_obra_padrao;
                            foreach ($tipos as $tipo):
                            ?>
                            <div class="config-item">
                                <div class="config-item-icon" style="background: <?php echo $tipo['cor']; ?>20; color: <?php echo $tipo['cor']; ?>;">
                                    <i class='bx <?php echo $tipo['icone'] ?? 'bx-building'; ?>'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <span class="tipo-color" style="background: <?php echo $tipo['cor']; ?>"></span>
                                        <?php echo htmlspecialchars($tipo['nome']); ?>
                                    </div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($tipo['descricao'] ?? ''); ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarTipoObra(<?php echo $tipo['id']; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirTipoObra(<?php echo $tipo['id']; ?>)">
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

                        <div class="config-items-list">
                            <?php
                            $tipos_atv_padrao = [
                                ['id' => 1, 'nome' => 'Trabalho', 'descricao' => 'Execução de serviços técnicos', 'cor' => '#3498db', 'icone' => 'bx-wrench', 'categoria' => 'execucao'],
                                ['id' => 2, 'nome' => 'Visita Técnica', 'descricao' => 'Visitas de inspeção e levantamento', 'cor' => '#9b59b6', 'icone' => 'bx-search', 'categoria' => 'visita'],
                                ['id' => 3, 'nome' => 'Manutenção', 'descricao' => 'Manutenção preventiva ou corretiva', 'cor' => '#27ae60', 'icone' => 'bx-cog', 'categoria' => 'manutencao'],
                                ['id' => 4, 'nome' => 'Impedimento', 'descricao' => 'Registro de impedimentos encontrados', 'cor' => '#e74c3c', 'icone' => 'bx-block', 'categoria' => 'impedimento'],
                                ['id' => 5, 'nome' => 'Outro', 'descricao' => 'Outras atividades', 'cor' => '#95a5a6', 'icone' => 'bx-help-circle', 'categoria' => 'outro'],
                            ];
                            $tipos_atv = $tipos_atividades ?? $tipos_atv_padrao;
                            foreach ($tipos_atv as $tipo):
                            ?>
                            <div class="config-item">
                                <div class="config-item-icon" style="background: <?php echo $tipo['cor']; ?>20; color: <?php echo $tipo['cor']; ?>;">
                                    <i class='bx <?php echo $tipo['icone'] ?? 'bx-task'; ?>'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <span class="tipo-color" style="background: <?php echo $tipo['cor']; ?>"></span>
                                        <?php echo htmlspecialchars($tipo['nome']); ?>
                                    </div>
                                    <div class="config-item-meta">
                                        <?php echo htmlspecialchars($tipo['descricao'] ?? ''); ?>
                                        <span style="margin-left: 8px; padding: 2px 8px; background: #f0f0f0; border-radius: 4px; font-size: 11px; text-transform: uppercase;">
                                            <?php echo $tipo['categoria'] ?? 'outro'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarTipoAtividade(<?php echo $tipo['id']; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirTipoAtividade(<?php echo $tipo['id']; ?>)">
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

                        <div class="config-items-list">
                            <?php
                            $status_obra_padrao = [
                                ['id' => 1, 'nome' => 'Prospecção', 'descricao' => 'Obra em fase inicial/orçamento', 'cor' => '#95a5a6', 'icone' => 'bx-search', 'ordem' => 1, 'finalizado' => false],
                                ['id' => 2, 'nome' => 'Em Andamento', 'descricao' => 'Obra sendo executada', 'cor' => '#3498db', 'icone' => 'bx-play-circle', 'ordem' => 2, 'finalizado' => false],
                                ['id' => 3, 'nome' => 'Paralisada', 'descricao' => 'Obra temporariamente parada', 'cor' => '#f39c12', 'icone' => 'bx-pause-circle', 'ordem' => 3, 'finalizado' => false],
                                ['id' => 4, 'nome' => 'Concluída', 'descricao' => 'Obra finalizada com sucesso', 'cor' => '#27ae60', 'icone' => 'bx-check-circle', 'ordem' => 4, 'finalizado' => true],
                                ['id' => 5, 'nome' => 'Cancelada', 'descricao' => 'Obra cancelada', 'cor' => '#e74c3c', 'icone' => 'bx-x-circle', 'ordem' => 5, 'finalizado' => true],
                            ];
                            $status_list = $status_obra ?? $status_obra_padrao;
                            foreach ($status_list as $status):
                            ?>
                            <div class="config-item">
                                <div class="config-item-icon" style="background: <?php echo $status['cor']; ?>20; color: <?php echo $status['cor']; ?>;">
                                    <i class='bx <?php echo $status['icone'] ?? 'bx-flag'; ?>'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <span class="status-badge-config" style="background: <?php echo $status['cor']; ?>20; color: <?php echo $status['cor']; ?>;">
                                            <?php echo htmlspecialchars($status['nome']); ?>
                                        </span>
                                        <?php if ($status['finalizado']): ?>
                                            <span style="margin-left: 8px; padding: 2px 8px; background: #e8f5e9; color: #27ae60; border-radius: 4px; font-size: 11px;">FINALIZADO</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($status['descricao'] ?? ''); ?> • Ordem: <?php echo $status['ordem']; ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarStatusObra(<?php echo $status['id']; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <?php if (!in_array($status['nome'], ['Prospecção', 'Em Andamento', 'Concluída'])): ?>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirStatusObra(<?php echo $status['id']; ?>)">
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

                        <div class="config-items-list">
                            <?php
                            $status_atv_padrao = [
                                ['id' => 1, 'nome' => 'Agendada', 'descricao' => 'Atividade agendada para execução futura', 'cor' => '#95a5a6', 'icone' => 'bx-calendar', 'fluxo' => 'inicial'],
                                ['id' => 2, 'nome' => 'Iniciada', 'descricao' => 'Atividade em execução', 'cor' => '#3498db', 'icone' => 'bx-play-circle', 'fluxo' => 'execucao'],
                                ['id' => 3, 'nome' => 'Pausada', 'descricao' => 'Atividade temporariamente pausada', 'cor' => '#f39c12', 'icone' => 'bx-pause-circle', 'fluxo' => 'execucao'],
                                ['id' => 4, 'nome' => 'Concluída', 'descricao' => 'Atividade finalizada com sucesso', 'cor' => '#27ae60', 'icone' => 'bx-check-circle', 'fluxo' => 'final'],
                                ['id' => 5, 'nome' => 'Cancelada', 'descricao' => 'Atividade cancelada', 'cor' => '#e74c3c', 'icone' => 'bx-x-circle', 'fluxo' => 'final'],
                                ['id' => 6, 'nome' => 'Reaberta', 'descricao' => 'Atividade reaberta para reatendimento', 'cor' => '#9b59b6', 'icone' => 'bx-refresh', 'fluxo' => 'especial'],
                            ];
                            $status_atv_list = $status_atividade ?? $status_atv_padrao;
                            foreach ($status_atv_list as $status):
                            ?>
                            <div class="config-item">
                                <div class="config-item-icon" style="background: <?php echo $status['cor']; ?>20; color: <?php echo $status['cor']; ?>;">
                                    <i class='bx <?php echo $status['icone'] ?? 'bx-radio-circle'; ?>'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <span class="status-badge-config" style="background: <?php echo $status['cor']; ?>20; color: <?php echo $status['cor']; ?>;">
                                            <?php echo htmlspecialchars($status['nome']); ?>
                                        </span>
                                        <span style="margin-left: 8px; padding: 2px 8px; background: #f0f0f0; border-radius: 4px; font-size: 11px; text-transform: uppercase;">
                                            <?php echo $status['fluxo'] ?? 'normal'; ?>
                                        </span>
                                    </div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($status['descricao'] ?? ''); ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarStatusAtividade(<?php echo $status['id']; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <?php if (!in_array($status['nome'], ['Agendada', 'Iniciada', 'Concluída'])): ?>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirStatusAtividade(<?php echo $status['id']; ?>)">
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

                        <div class="config-grid">
                            <?php
                            $especialidades_padrao = [
                                ['id' => 1, 'nome' => 'Elétrica', 'descricao' => 'Instalações elétricas', 'cor' => '#f1c40f', 'icone' => 'bx-bolt-circle'],
                                ['id' => 2, 'nome' => 'Hidráulica', 'descricao' => 'Instalações hidráulicas', 'cor' => '#3498db', 'icone' => 'bx-water'],
                                ['id' => 3, 'nome' => 'Estrutural', 'descricao' => 'Estrutura da obra', 'cor' => '#7f8c8d', 'icone' => 'bx-building'],
                                ['id' => 4, 'nome' => 'Acabamento', 'descricao' => 'Acabamentos finais', 'cor' => '#e67e22', 'icone' => 'bx-paint'],
                                ['id' => 5, 'nome' => 'Paisagismo', 'descricao' => 'Áreas externas e jardinagem', 'cor' => '#27ae60', 'icone' => 'bx-leaf'],
                                ['id' => 6, 'nome' => 'Segurança', 'descricao' => 'Sistemas de segurança', 'cor' => '#e74c3c', 'icone' => 'bx-shield'],
                            ];
                            $esp_list = $especialidades ?? $especialidades_padrao;
                            foreach ($esp_list as $esp):
                            ?>
                            <div class="config-grid-item">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                    <div style="width: 48px; height: 48px; background: <?php echo $esp['cor']; ?>20; color: <?php echo $esp['cor']; ?>; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                        <i class='bx <?php echo $esp['icone'] ?? 'bx-hard-hat'; ?>'></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #333;"><?php echo htmlspecialchars($esp['nome']); ?></div>
                                        <span class="tipo-color" style="background: <?php echo $esp['cor']; ?>"></span>
                                    </div>
                                </div>
                                <div style="font-size: 13px; color: #666; margin-bottom: 16px;">
                                    <?php echo htmlspecialchars($esp['descricao'] ?? ''); ?>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button class="config-btn config-btn-secondary config-btn-sm" style="flex: 1;" onclick="editarEspecialidade(<?php echo $esp['id']; ?>)">
                                        <i class='bx bx-edit'></i> Editar
                                    </button>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirEspecialidade(<?php echo $esp['id']; ?>)">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA: FUNÇÕES DA EQUIPE -->
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

                        <div class="config-items-list">
                            <?php
                            $funcoes_padrao = [
                                ['id' => 1, 'nome' => 'Engenheiro Responsável', 'descricao' => 'Responsável técnico pela obra', 'nivel' => 'alto'],
                                ['id' => 2, 'nome' => 'Mestre de Obras', 'descricao' => 'Coordenação de equipe de trabalho', 'nivel' => 'medio'],
                                ['id' => 3, 'nome' => 'Técnico de Segurança', 'descricao' => 'Responsável por normas de segurança', 'nivel' => 'medio'],
                                ['id' => 4, 'nome' => 'Pedreiro', 'descricao' => 'Execução de serviços de alvenaria', 'nivel' => 'baixo'],
                                ['id' => 5, 'nome' => 'Eletricista', 'descricao' => 'Instalações elétricas', 'nivel' => 'baixo'],
                                ['id' => 6, 'nome' => 'Encanador', 'descricao' => 'Instalações hidráulicas', 'nivel' => 'baixo'],
                                ['id' => 7, 'nome' => 'Pintor', 'descricao' => 'Serviços de pintura', 'nivel' => 'baixo'],
                                ['id' => 8, 'nome' => 'Auxiliar', 'descricao' => 'Apoio geral na obra', 'nivel' => 'baixo'],
                            ];
                            $funcoes = $funcoes_equipe ?? $funcoes_padrao;
                            foreach ($funcoes as $funcao):
                                $nivel_cor = ['alto' => '#e74c3c', 'medio' => '#f39c12', 'baixo' => '#27ae60'][$funcao['nivel'] ?? 'baixo'];
                            ?>
                            <div class="config-item">
                                <div class="config-item-icon" style="background: <?php echo $nivel_cor; ?>20; color: <?php echo $nivel_cor; ?>;">
                                    <i class='bx bx-user'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name">
                                        <?php echo htmlspecialchars($funcao['nome']); ?>
                                        <span style="margin-left: 8px; padding: 2px 8px; background: <?php echo $nivel_cor; ?>20; color: <?php echo $nivel_cor; ?>; border-radius: 4px; font-size: 11px; text-transform: uppercase;">
                                            <?php echo $funcao['nivel'] ?? 'baixo'; ?>
                                        </span>
                                    </div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($funcao['descricao'] ?? ''); ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="editarFuncao(<?php echo $funcao['id']; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class="config-btn config-btn-danger config-btn-sm" onclick="excluirFuncao(<?php echo $funcao['id']; ?>)">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA: NOTIFICAÇÕES -->
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

            <!-- ABA: PERMISSÕES -->
            <div id="tab-permissoes" class="config-content">
                <div class="config-card">
                    <div class="config-card-header">
                        <div class="config-card-title">
                            <i class='bx bx-lock'></i>
                            Permissões por Perfil
                        </div>
                    </div>
                    <div class="config-card-body">
                        <div class="config-alert config-alert-warning">
                            <i class='bx bx-error-circle'></i>
                            <div>
                                <strong>Atenção:</strong> Alterar permissões afeta o acesso de todos os usuários do sistema.
                            </div>
                        </div>

                        <div class="config-items-list">
                            <?php
                            $perfis = [
                                ['id' => 1, 'nome' => 'Administrador', 'descricao' => 'Acesso total ao sistema', 'cor' => '#e74c3c'],
                                ['id' => 2, 'nome' => 'Gestor de Obras', 'descricao' => 'Gerencia obras, etapas e equipes', 'cor' => '#3498db'],
                                ['id' => 3, 'nome' => 'Técnico', 'descricao' => 'Executa atividades e registra presença', 'cor' => '#27ae60'],
                                ['id' => 4, 'nome' => 'Cliente', 'descricao' => 'Visualiza progresso da obra', 'cor' => '#f39c12'],
                            ];
                            foreach ($perfis as $perfil):
                            ?>
                            <div class="config-item" style="border-left: 4px solid <?php echo $perfil['cor']; ?>;">
                                <div class="config-item-icon" style="background: <?php echo $perfil['cor']; ?>20; color: <?php echo $perfil['cor']; ?>;">
                                    <i class='bx bx-user-circle'></i>
                                </div>
                                <div class="config-item-info">
                                    <div class="config-item-name"><?php echo htmlspecialchars($perfil['nome']); ?></div>
                                    <div class="config-item-meta"><?php echo htmlspecialchars($perfil['descricao']); ?></div>
                                </div>
                                <div class="config-item-actions">
                                    <button class="config-btn config-btn-secondary config-btn-sm" onclick="configurarPermissoes(<?php echo $perfil['id']; ?>)">
                                        <i class='bx bx-cog'></i> Configurar
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
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

<script>
// Navegação entre abas
function switchTab(tabName, element) {
    // Remove active de todas as abas
    document.querySelectorAll('.config-tab-item').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.config-content').forEach(content => content.classList.remove('active'));

    // Adiciona active na aba clicada
    element.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');

    // Salva preferência no localStorage
    localStorage.setItem('obras_config_tab', tabName);
}

// Carrega aba salva
(function loadSavedTab() {
    const savedTab = localStorage.getItem('obras_config_tab');
    if (savedTab) {
        const tabElement = document.querySelector(`.config-tab-item[onclick*="${savedTab}"]`);
        if (tabElement) {
            switchTab(savedTab, tabElement);
        }
    }
})();

// Modal functions
function abrirModal(title, content) {
    document.getElementById('modalTitle').innerHTML = title;
    document.getElementById('modalBody').innerHTML = content;
    document.getElementById('modalConfig').classList.add('active');
}

function fecharModal() {
    document.getElementById('modalConfig').classList.remove('active');
}

function salvarModal() {
    // Implementar salvamento via AJAX
    alert('Função de salvar será implementada conforme a estrutura do banco de dados');
    fecharModal();
}

// Funções específicas para cada tipo
function abrirModalTipoObra() {
    const content = `
        <div class="config-form-group">
            <label>Nome do Tipo</label>
            <input type="text" class="config-form-control" placeholder="Ex: Reforma Residencial">
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea class="config-form-control" rows="2" placeholder="Descrição breve"></textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: repeat(2, 1fr);">
            <div class="config-form-group">
                <label>Cor</label>
                <input type="color" class="config-form-control" value="#3498db">
            </div>
            <div class="config-form-group">
                <label>Ícone (classe Boxicons)</label>
                <input type="text" class="config-form-control" value="bx-building" placeholder="Ex: bx-building">
            </div>
        </div>
    `;
    abrirModal("<i class='bx bx-plus-circle'></i> Novo Tipo de Obra", content);
}

function abrirModalTipoAtividade() {
    const content = `
        <div class="config-form-group">
            <label>Nome do Tipo</label>
            <input type="text" class="config-form-control" placeholder="Ex: Instalação">
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea class="config-form-control" rows="2"></textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: repeat(2, 1fr);">
            <div class="config-form-group">
                <label>Cor</label>
                <input type="color" class="config-form-control" value="#3498db">
            </div>
            <div class="config-form-group">
                <label>Categoria</label>
                <select class="config-form-control">
                    <option value="execucao">Execução</option>
                    <option value="visita">Visita</option>
                    <option value="manutencao">Manutenção</option>
                    <option value="impedimento">Impedimento</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
        </div>
    `;
    abrirModal("<i class='bx bx-plus-circle'></i> Novo Tipo de Atividade", content);
}

function abrirModalStatusObra() {
    const content = `
        <div class="config-form-group">
            <label>Nome do Status</label>
            <input type="text" class="config-form-control" placeholder="Ex: Em Aprovação">
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea class="config-form-control" rows="2"></textarea>
        </div>
        <div class="config-form-group">
            <label>Cor</label>
            <input type="color" class="config-form-control" value="#95a5a6">
        </div>
        <div class="config-form-group">
            <label class="config-toggle">
                <input type="checkbox" checked>
                <span class="toggle-slider"></span>
                <span style="margin-left: 12px;">Status finalizado (obra concluída/cancelada)</span>
            </label>
        </div>
    `;
    abrirModal("<i class='bx bx-plus-circle'></i> Novo Status de Obra", content);
}

function abrirModalStatusAtividade() {
    const content = `
        <div class="config-form-group">
            <label>Nome do Status</label>
            <input type="text" class="config-form-control" placeholder="Ex: Aguardando Material">
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea class="config-form-control" rows="2"></textarea>
        </div>
        <div class="config-grid" style="grid-template-columns: repeat(2, 1fr);">
            <div class="config-form-group">
                <label>Cor</label>
                <input type="color" class="config-form-control" value="#f39c12">
            </div>
            <div class="config-form-group">
                <label>Fluxo</label>
                <select class="config-form-control">
                    <option value="inicial">Inicial</option>
                    <option value="execucao">Em Execução</option>
                    <option value="final">Final</option>
                    <option value="especial">Especial</option>
                </select>
            </div>
        </div>
    `;
    abrirModal("<i class='bx bx-plus-circle'></i> Novo Status de Atividade", content);
}

function abrirModalEspecialidade() {
    const content = `
        <div class="config-form-group">
            <label>Nome da Especialidade</label>
            <input type="text" class="config-form-control" placeholder="Ex: Gesso">
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea class="config-form-control" rows="2"></textarea>
        </div>
        <div class="config-form-group">
            <label>Cor</label>
            <input type="color" class="config-form-control" value="#e67e22">
        </div>
    `;
    abrirModal("<i class='bx bx-plus-circle'></i> Nova Especialidade", content);
}

function abrirModalFuncao() {
    const content = `
        <div class="config-form-group">
            <label>Nome da Função</label>
            <input type="text" class="config-form-control" placeholder="Ex: Gesseiro">
        </div>
        <div class="config-form-group">
            <label>Descrição</label>
            <textarea class="config-form-control" rows="2"></textarea>
        </div>
        <div class="config-form-group">
            <label>Nível de Acesso</label>
            <select class="config-form-control">
                <option value="baixo">Baixo - Apenas execução</option>
                <option value="medio">Médio - Coordenação</option>
                <option value="alto">Alto - Gestão</option>
            </select>
        </div>
    `;
    abrirModal("<i class='bx bx-plus-circle'></i> Nova Função", content);
}

// Funções de edição e exclusão
function editarTipoObra(id) {
    alert('Editar tipo de obra ID: ' + id);
}

function excluirTipoObra(id) {
    if (confirm('Tem certeza que deseja excluir este tipo de obra?')) {
        alert('Excluir tipo de obra ID: ' + id);
    }
}

function editarTipoAtividade(id) {
    alert('Editar tipo de atividade ID: ' + id);
}

function excluirTipoAtividade(id) {
    if (confirm('Tem certeza que deseja excluir este tipo de atividade?')) {
        alert('Excluir tipo de atividade ID: ' + id);
    }
}

function editarStatusObra(id) {
    alert('Editar status de obra ID: ' + id);
}

function excluirStatusObra(id) {
    if (confirm('Tem certeza que deseja excluir este status?')) {
        alert('Excluir status de obra ID: ' + id);
    }
}

function editarStatusAtividade(id) {
    alert('Editar status de atividade ID: ' + id);
}

function excluirStatusAtividade(id) {
    if (confirm('Tem certeza que deseja excluir este status?')) {
        alert('Excluir status de atividade ID: ' + id);
    }
}

function editarEspecialidade(id) {
    alert('Editar especialidade ID: ' + id);
}

function excluirEspecialidade(id) {
    if (confirm('Tem certeza que deseja excluir esta especialidade?')) {
        alert('Excluir especialidade ID: ' + id);
    }
}

function editarFuncao(id) {
    alert('Editar função ID: ' + id);
}

function excluirFuncao(id) {
    if (confirm('Tem certeza que deseja excluir esta função?')) {
        alert('Excluir função ID: ' + id);
    }
}

function configurarPermissoes(perfilId) {
    alert('Configurar permissões do perfil ID: ' + perfilId);
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    if (event.target.classList.contains('config-modal-overlay')) {
        fecharModal();
    }
}

// Atalho ESC para fechar modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModal();
    }
});
</script>
</div>
</div>
