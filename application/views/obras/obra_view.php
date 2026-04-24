<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Nova Visualização de Obra - Design Moderno -->
<style>
/* ============================================
   VISUALIZAÇÃO DA OBRA - NOVO DESIGN
   ============================================ */

.obra-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header Principal */
.obra-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 24px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.obra-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.obra-header.em-andamento {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    box-shadow: 0 10px 40px rgba(79, 172, 254, 0.3);
}

.obra-header.concluida {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
}

.obra-header.paralisada {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    box-shadow: 0 10px 40px rgba(245, 87, 108, 0.3);
}

.obra-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    position: relative;
    z-index: 1;
}

.obra-header-info { flex: 1; }

.obra-breadcrumb {
    font-size: 13px;
    opacity: 0.9;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.obra-breadcrumb a {
    color: inherit;
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.obra-breadcrumb a:hover { opacity: 1; }

.obra-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.obra-title i {
    font-size: 32px;
    opacity: 0.9;
}

.obra-cliente {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 500;
}

.obra-header-status {
    text-align: right;
}

.status-badge {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(10px);
    margin-bottom: 16px;
}

/* Progresso Principal */
.obra-progress-section {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.2);
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.progress-title {
    font-size: 14px;
    font-weight: 600;
    opacity: 0.9;
}

.progress-percentage {
    font-size: 32px;
    font-weight: 700;
}

.progress-bar-container {
    height: 10px;
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: white;
    border-radius: 10px;
    transition: width 0.6s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Grid de Cards */
.obra-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

@media (max-width: 992px) {
    .obra-grid { grid-template-columns: 1fr; }
    .obra-header-row { flex-direction: column; }
    .obra-header-status { text-align: left; width: 100%; }
}

/* Cards */
.card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f0f0f0;
}

.card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-title i {
    color: #667eea;
    font-size: 22px;
}

.card-action {
    padding: 8px 16px;
    border-radius: 8px;
    background: #667eea;
    color: white;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.card-action:hover {
    background: #5568d3;
    transform: translateY(-2px);
}

/* Card de Prazo */
.prazo-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.prazo-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.prazo-item {
    text-align: center;
    padding: 16px;
    background: white;
    border-radius: 10px;
}

.prazo-label {
    font-size: 12px;
    color: #888;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 8px;
}

.prazo-value {
    font-size: 20px;
    font-weight: 700;
    color: #333;
}

.prazo-value i {
    color: #667eea;
    margin-right: 6px;
}

.prazo-dias {
    grid-column: span 2;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.prazo-dias .prazo-label { color: rgba(255,255,255,0.8); }
.prazo-dias .prazo-value { color: white; font-size: 28px; }

.prazo-dias.atrasado {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
}

.prazo-dias.concluido {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.info-item {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 10px;
}

.info-item.full-width {
    grid-column: span 2;
}

.info-label {
    font-size: 11px;
    color: #888;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 6px;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-value i {
    color: #667eea;
}

/* Timeline de Etapas */
.etapas-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.etapa-card {
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e8e8e8;
    transition: all 0.3s;
}

.etapa-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.etapa-header-card {
    padding: 20px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.etapa-header-card:hover {
    background: #f0f0f0;
}

.etapa-main-info {
    display: flex;
    align-items: center;
    gap: 16px;
    flex: 1;
}

.etapa-number {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #667eea;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    flex-shrink: 0;
}

.etapa-number.concluida { background: #11998e; }
.etapa-number.andamento { background: #4facfe; }
.etapa-number.pendente { background: #95a5a6; }
.etapa-number.atrasada { background: #e74c3c; }

.etapa-info { flex: 1; }

.etapa-name {
    font-weight: 700;
    font-size: 15px;
    color: #333;
    margin-bottom: 4px;
}

.etapa-meta-text {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 12px;
}

.etapa-progress-section {
    width: 150px;
    text-align: right;
}

.etapa-progress-bar {
    height: 6px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 6px;
}

.etapa-progress-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.4s ease;
}

.etapa-progress-fill.concluida { background: #11998e; }
.etapa-progress-fill.andamento { background: #4facfe; }
.etapa-progress-fill.pendente { background: #95a5a6; }
.etapa-progress-fill.atrasada { background: #e74c3c; }

.etapa-progress-text {
    font-size: 12px;
    font-weight: 600;
    color: #667eea;
}

.etapa-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.etapa-status.concluida {
    background: rgba(17, 153, 142, 0.1);
    color: #11998e;
}

.etapa-status.andamento {
    background: rgba(79, 172, 254, 0.1);
    color: #4facfe;
}

.etapa-status.pendente {
    background: rgba(149, 165, 166, 0.1);
    color: #95a5a6;
}

.etapa-status.atrasada {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.etapa-toggle {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s;
    flex-shrink: 0;
}

.etapa-toggle.expanded {
    transform: rotate(180deg);
}

.etapa-toggle i {
    color: #667eea;
    font-size: 14px;
}

/* Sub-lista de Atividades */
.etapa-atividades {
    display: none;
    background: white;
    border-top: 1px solid #e8e8e8;
}

.etapa-atividades.expanded {
    display: block;
}

.atividades-header {
    padding: 16px 20px;
    background: #f8f9fa;
    font-size: 13px;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.atividades-list {
    padding: 12px 20px 20px;
}

.atividade-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 8px;
    transition: all 0.2s;
}

.atividade-item:hover {
    background: #f0f0f0;
    transform: translateX(4px);
}

.atividade-item:last-child { margin-bottom: 0; }

.atividade-status-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.atividade-status-icon.concluida {
    background: rgba(17, 153, 142, 0.1);
    color: #11998e;
}

.atividade-status-icon.andamento {
    background: rgba(79, 172, 254, 0.1);
    color: #4facfe;
}

.atividade-status-icon.pendente {
    background: rgba(149, 165, 166, 0.1);
    color: #95a5a6;
}

.atividade-status-icon.pausada {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.atividade-content { flex: 1; }

.atividade-title {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    margin-bottom: 4px;
}

.atividade-meta {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 12px;
}

.atividade-progress {
    text-align: right;
}

.atividade-progress-bar {
    width: 80px;
    height: 4px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 4px;
}

.atividade-progress-fill {
    height: 100%;
    background: #667eea;
    border-radius: 10px;
}

.atividade-progress-text {
    font-size: 11px;
    font-weight: 600;
    color: #667eea;
}

/* Equipe */
.equipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 16px;
}

.equipe-item {
    text-align: center;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
    transition: all 0.2s;
}

.equipe-item:hover {
    background: #f0f0f0;
    transform: translateY(-3px);
}

.equipe-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    color: white;
    font-size: 20px;
}

.equipe-name {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    margin-bottom: 4px;
}

.equipe-role {
    font-size: 12px;
    color: #888;
}

/* Ações Rápidas */
.acoes-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.acao-btn {
    padding: 12px 20px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: white;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.acao-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.acao-btn i { font-size: 14px; }

.acao-wizard { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.acao-relatorio { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }
.acao-etapas { background: #667eea; }
.acao-equipe { background: #764ba2; }
.acao-atividades { background: #f39c12; }
.acao-editar { background: #e74c3c; }
.acao-voltar { background: #95a5a6; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #888;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    opacity: 0.4;
}

.empty-state h4 {
    font-size: 16px;
    margin-bottom: 8px;
    color: #333;
}

.empty-state p {
    font-size: 14px;
    margin: 0;
}

/* Atividades Recentes */
.atividade-recente {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 10px;
    border-left: 4px solid #667eea;
    transition: all 0.2s;
}

.atividade-recente:hover {
    background: #f0f0f0;
    transform: translateX(4px);
}

.atividade-recente:last-child { margin-bottom: 0; }

.atividade-recente.concluida { border-left-color: #11998e; }
.atividade-recente.pendente { border-left-color: #95a5a6; }

.atividade-recente-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.atividade-recente.concluida .atividade-recente-icon {
    background: rgba(17, 153, 142, 0.1);
    color: #11998e;
}

.atividade-recente-content { flex: 1; }

.atividade-recente-title {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    margin-bottom: 4px;
}

.atividade-recente-meta {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Responsividade */
@media (max-width: 768px) {
    .obra-container { padding: 16px; }
    .obra-title { font-size: 22px; }
    .obra-title i { font-size: 26px; }
    .prazo-grid { grid-template-columns: 1fr; }
    .prazo-dias { grid-column: span 1; }
    .info-grid { grid-template-columns: 1fr; }
    .info-item.full-width { grid-column: span 1; }
    .etapa-progress-section { display: none; }
    .equipe-grid { grid-template-columns: repeat(2, 1fr); }
    .acoes-bar { justify-content: center; }
}

/* Dark Mode Support */
[data-theme="dark"] .card {
    background: #2d3748;
    border-color: #4a5568;
}

[data-theme="dark"] .card-title,
[data-theme="dark"] .obra-title,
[data-theme="dark"] .info-value,
[data-theme="dark"] .prazo-value,
[data-theme="dark"] .etapa-name,
[data-theme="dark"] .atividade-title,
[data-theme="dark"] .equipe-name,
[data-theme="dark"] .atividade-recente-title {
    color: #fff;
}

[data-theme="dark"] .info-item,
[data-theme="dark"] .etapa-card,
[data-theme="dark"] .atividade-item,
[data-theme="dark"] .equipe-item,
[data-theme="dark"] .atividades-header,
[data-theme="dark"] .atividade-recente {
    background: #3d4852;
}

[data-theme="dark"] .info-label,
[data-theme="dark"] .prazo-label,
[data-theme="dark"] .etapa-meta-text,
[data-theme="dark"] .atividade-meta,
[data-theme="dark"] .equipe-role,
[data-theme="dark"] .atividade-recente-meta {
    color: #a0aec0;
}

[data-theme="dark"] .etapa-atividades {
    background: #2d3748;
    border-color: #4a5568;
}
</style>

<div class="obra-container">
    <?php
    // Definir classe do header baseado no status
    $status_class = '';
    $status_label = '';
    switch ($obra->status) {
        case 'EmExecucao':
        case 'Em Andamento':
        case 'em-andamento':
            $status_class = 'em-andamento';
            $status_label = 'Em Andamento';
            break;
        case 'Concluida':
        case 'concluida':
            $status_class = 'concluida';
            $status_label = 'Concluída';
            break;
        case 'Paralisada':
        case 'paralisada':
            $status_class = 'paralisada';
            $status_label = 'Paralisada';
            break;
        default:
            $status_class = '';
            $status_label = ucfirst($obra->status);
    }

    // Calcular dias restantes
    $dias_restantes = null;
    $prazo_class = '';
    if ($obra->data_fim_prevista) {
        $hoje = new DateTime();
        $previsto = new DateTime($obra->data_fim_prevista);
        $dias_restantes = $hoje->diff($previsto, false)->format('%r%a');

        if ($obra->status == 'Concluida' || $obra->status == 'concluida') {
            $prazo_class = 'concluido';
        } elseif ($dias_restantes < 0) {
            $prazo_class = 'atrasado';
        }
    }

    $progresso = $obra->percentual_concluido ?? 0;
    ?>

    <!-- Header da Obra -->
    <div class="obra-header <?php echo $status_class; ?>">
        <div class="obra-header-row">
            <div class="obra-header-info">
                <div class="obra-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><i class="icon-arrow-left"></i> Obras</a>
                    <span>/</span>
                    <span>Visualizar</span>
                </div>
                <h1 class="obra-title">
                    <i class="icon-building"></i>
                    <?php echo htmlspecialchars($obra->nome); ?>
                </h1>
                <div class="obra-cliente">
                    <i class="icon-user"></i>
                    <?php echo htmlspecialchars($obra->cliente_nome ?? 'Sem cliente'); ?>
                </div>
            </div>

            <div class="obra-header-status">
                <span class="status-badge"><?php echo $status_label; ?></span>

                <div class="obra-progress-section">
                    <div class="progress-header">
                        <span class="progress-title">Progresso Total</span>
                        <span class="progress-percentage"><?php echo $progresso; ?>%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" style="width: <?php echo $progresso; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="acoes-bar">
        <a href="<?php echo site_url('obras/relatorioGeral/' . $obra->id); ?>" class="acao-btn acao-relatorio">
            <i class="icon-file-alt"></i> Relatório Geral
        </a>
        <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>" class="acao-btn acao-etapas">
            <i class="icon-tasks"></i> Gerenciar Etapas
        </a>
        <a href="<?php echo site_url('obras/equipe/' . $obra->id); ?>" class="acao-btn acao-equipe">
            <i class="icon-group"></i> Equipe
        </a>
        <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>" class="acao-btn acao-atividades">
            <i class="icon-check"></i> Atividades
        </a>
        <a href="<?php echo site_url('obras/editar/' . $obra->id); ?>" class="acao-btn acao-editar">
            <i class="icon-edit"></i> Editar
        </a>
        <a href="<?php echo site_url('obras'); ?>" class="acao-btn acao-voltar">
            <i class="icon-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Grid Principal -->
    <div class="obra-grid">
        <!-- Coluna Esquerda -->
        <div class="obra-coluna">
            <!-- Card de Prazo e Progresso -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-calendar"></i>
                        Prazo da Obra
                    </div>
                </div>

                <div class="prazo-card">
                    <div class="prazo-grid">
                        <div class="prazo-item">
                            <div class="prazo-label">Data de Início</div>
                            <div class="prazo-value">
                                <i class="icon-play"></i>
                                <?php echo $obra->data_inicio_contrato ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : 'Não definida'; ?>
                            </div>
                        </div>
                        <div class="prazo-item">
                            <div class="prazo-label">Data de Término Prevista</div>
                            <div class="prazo-value">
                                <i class="icon-flag-checkered"></i>
                                <?php echo $obra->data_fim_prevista ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : 'Não definida'; ?>
                            </div>
                        </div>
                        <?php if ($obra->data_fim_prevista): ?>
                        <div class="prazo-item prazo-dias <?php echo $prazo_class; ?>">
                            <div class="prazo-label">
                                <?php
                                if ($obra->status == 'Concluida' || $obra->status == 'concluida') {
                                    echo 'Obra Concluída';
                                } elseif ($dias_restantes < 0) {
                                    echo 'Dias de Atraso';
                                } else {
                                    echo 'Dias Restantes';
                                }
                                ?>
                            </div>
                            <div class="prazo-value">
                                <?php
                                if ($obra->status == 'Concluida' || $obra->status == 'concluida') {
                                    echo '<i class="icon-check"></i> Finalizada';
                                } else {
                                    echo abs($dias_restantes) . ' dias';
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Código</div>
                        <div class="info-value">
                            <i class="icon-barcode"></i>
                            <?php echo htmlspecialchars($obra->codigo ?? 'N/A'); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tipo</div>
                        <div class="info-value">
                            <i class="icon-cog"></i>
                            <?php
                            $tiposObraLabels = [
                                'Condominio' => 'Condomínio',
                                'Comercio' => 'Comércio',
                                'Residencia' => 'Residência',
                                'Industrial' => 'Industrial',
                                'Publica' => 'Pública',
                            ];
                            echo htmlspecialchars($tiposObraLabels[$obra->tipo_obra] ?? ($obra->tipo_obra ?: 'Não definido'));
                            ?>
                        </div>
                    </div>
                    <div class="info-item full-width">
                        <div class="info-label">Endereço</div>
                        <div class="info-value">
                            <i class="icon-map-marker"></i>
                            <?php
                            $endereco = [];
                            if (!empty($obra->endereco)) $endereco[] = $obra->endereco;
                            if (!empty($obra->bairro)) $endereco[] = $obra->bairro;
                            if (!empty($obra->cidade)) $endereco[] = $obra->cidade;
                            if (!empty($obra->estado)) $endereco[] = $obra->estado;
                            echo !empty($endereco) ? htmlspecialchars(implode(', ', $endereco)) : 'Não informado';
                            ?>
                        </div>
                    </div>
                    <?php if (!empty($obra->valor_contrato)): ?>
                    <div class="info-item">
                        <div class="info-label">Valor do Contrato</div>
                        <div class="info-value">
                            <i class="icon-money"></i>
                            R$ <?php echo number_format($obra->valor_contrato, 2, ',', '.'); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($obra->observacoes)): ?>
                    <div class="info-item full-width">
                        <div class="info-label">Observações</div>
                        <div class="info-value" style="white-space: pre-wrap;">
                            <?php echo nl2br(htmlspecialchars($obra->observacoes)); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card de Etapas -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-tasks"></i>
                        Etapas da Obra
                    </div>
                    <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>" class="card-action">
                        <i class="icon-plus"></i> Gerenciar
                    </a>
                </div>

                <?php if (!empty($etapas)): ?>
                <div class="etapas-container">
                    <?php
                    // Buscar atividades por etapa
                    $atividades_por_etapa = [];
                    if (!empty($atividades_recentes)) {
                        foreach ($atividades_recentes as $atividade) {
                            $etapa_id = $atividade->etapa_id ?? 0;
                            if (!isset($atividades_por_etapa[$etapa_id])) {
                                $atividades_por_etapa[$etapa_id] = [];
                            }
                            $atividades_por_etapa[$etapa_id][] = $atividade;
                        }
                    }

                    foreach ($etapas as $index => $etapa):
                        $etapa_status = $etapa->status ?? 'NaoIniciada';
                        $etapa_class = '';
                        $status_text = '';

                        switch ($etapa_status) {
                            case 'Concluida':
                            case 'concluida':
                                $etapa_class = 'concluida';
                                $status_text = 'Concluída';
                                break;
                            case 'EmAndamento':
                            case 'Em Andamento':
                            case 'em-andamento':
                                $etapa_class = 'andamento';
                                $status_text = 'Em Andamento';
                                break;
                            case 'Atrasada':
                            case 'atrasada':
                                $etapa_class = 'atrasada';
                                $status_text = 'Atrasada';
                                break;
                            default:
                                $etapa_class = 'pendente';
                                $status_text = 'Não Iniciada';
                        }

                        $etapa_atividades = $atividades_por_etapa[$etapa->id] ?? [];
                        $tem_atividades = !empty($etapa_atividades);
                    ?>
                    <div class="etapa-card">
                        <div class="etapa-header-card" onclick="toggleEtapa(<?php echo $etapa->id; ?>)">
                            <div class="etapa-main-info">
                                <div class="etapa-number <?php echo $etapa_class; ?>">
                                    <?php echo $etapa->numero_etapa; ?>
                                </div>
                                <div class="etapa-info">
                                    <div class="etapa-name"><?php echo htmlspecialchars($etapa->nome); ?></div>
                                    <div class="etapa-meta-text">
                                        <span><i class="icon-calendar"></i>
                                            <?php
                                            if ($etapa->data_inicio_prevista && $etapa->data_fim_prevista) {
                                                echo date('d/m/Y', strtotime($etapa->data_inicio_prevista)) . ' a ' . date('d/m/Y', strtotime($etapa->data_fim_prevista));
                                            } elseif ($etapa->data_inicio_prevista) {
                                                echo 'Início: ' . date('d/m/Y', strtotime($etapa->data_inicio_prevista));
                                            } else {
                                                echo '<span style="color: #999;"><i class="icon-warning-sign" style="color: #f39c12;"></i> Prazo não definido</span>';
                                            }
                                            ?>
                                        </span>
                                        <?php if ($etapa->total_atividades > 0): ?>
                                        <span><i class="icon-tasks"></i> <?php echo $etapa->total_atividades; ?> atividade(s)</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="etapa-progress-section">
                                <div class="etapa-progress-bar">
                                    <div class="etapa-progress-fill <?php echo $etapa_class; ?>" style="width: <?php echo $etapa->percentual_concluido ?? 0; ?>%"></div>
                                </div>
                                <div class="etapa-progress-text"><?php echo $etapa->percentual_concluido ?? 0; ?>%</div>
                            </div>

                            <span class="etapa-status <?php echo $etapa_class; ?>"><?php echo $status_text; ?></span>

                            <div class="etapa-toggle <?php echo $tem_atividades ? '' : 'disabled'; ?>" id="toggle-<?php echo $etapa->id; ?>">
                                <i class="icon-chevron-down"></i>
                            </div>
                        </div>

                        <?php if ($tem_atividades): ?>
                        <div class="etapa-atividades" id="atividades-<?php echo $etapa->id; ?>">
                            <div class="atividades-header">
                                <span>Atividades desta etapa</span>
                                <span><?php echo count($etapa_atividades); ?> atividade(s)</span>
                            </div>
                            <div class="atividades-list">
                                <?php foreach ($etapa_atividades as $atividade):
                                    $ativ_status = $atividade->status ?? 'agendada';
                                    $ativ_class = '';
                                    $ativ_icon = '';

                                    switch ($ativ_status) {
                                        case 'concluida':
                                            $ativ_class = 'concluida';
                                            $ativ_icon = 'icon-check';
                                            break;
                                        case 'iniciada':
                                            $ativ_class = 'andamento';
                                            $ativ_icon = 'icon-play';
                                            break;
                                        case 'pausada':
                                            $ativ_class = 'pausada';
                                            $ativ_icon = 'icon-pause';
                                            break;
                                        default:
                                            $ativ_class = 'pendente';
                                            $ativ_icon = 'icon-time';
                                    }
                                ?>
                                <div class="atividade-item">
                                    <div class="atividade-status-icon <?php echo $ativ_class; ?>">
                                        <i class="<?php echo $ativ_icon; ?>"></i>
                                    </div>
                                    <div class="atividade-content">
                                        <div class="atividade-title"><?php echo htmlspecialchars($atividade->titulo ?? 'Atividade #' . $atividade->id); ?></div>
                                        <div class="atividade-meta">
                                            <span><i class="icon-calendar"></i> <?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?></span>
                                            <?php if (!empty($atividade->tecnico_nome)): ?>
                                            <span><i class="icon-user"></i> <?php echo htmlspecialchars($atividade->tecnico_nome); ?></span>
                                            <?php endif; ?>
                                            <span class="atividade-status-text <?php echo $ativ_class; ?>">
                                                <?php echo ucfirst($ativ_status); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="atividade-progress">
                                        <div class="atividade-progress-bar">
                                            <div class="atividade-progress-fill" style="width: <?php echo $atividade->percentual_concluido ?? 0; ?>%"></div>
                                        </div>
                                        <div class="atividade-progress-text"><?php echo $atividade->percentual_concluido ?? 0; ?>%</div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="icon-tasks"></i>
                    <h4>Nenhuma etapa cadastrada</h4>
                    <p>Clique em "Gerenciar" para adicionar etapas à obra.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Coluna Direita -->
        <div class="obra-coluna">
            <!-- Card de Equipe -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-group"></i>
                        Equipe
                    </div>
                    <a href="<?php echo site_url('obras/equipe/' . $obra->id); ?>" class="card-action">
                        <i class="icon-cog"></i> Gerenciar
                    </a>
                </div>

                <?php if (!empty($equipe)): ?>
                <div class="equipe-grid">
                    <?php foreach (array_slice($equipe, 0, 8) as $membro): ?>
                    <div class="equipe-item">
                        <div class="equipe-avatar">
                            <i class="icon-user"></i>
                        </div>
                        <div class="equipe-name"><?php echo htmlspecialchars($membro->nome ?? $membro->tecnico_nome ?? 'Sem nome'); ?></div>
                        <div class="equipe-role"><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($equipe) > 8): ?>
                    <div class="equipe-item" style="opacity: 0.6;">
                        <div class="equipe-avatar" style="background: #95a5a6;">
                            <i class="icon-plus"></i>
                        </div>
                        <div class="equipe-name">+<?php echo count($equipe) - 8; ?></div>
                        <div class="equipe-role">membros</div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="icon-group"></i>
                    <h4>Sem equipe alocada</h4>
                    <p>Adicione técnicos à equipe da obra.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Card de Atividades Recentes -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-check"></i>
                        Atividades Recentes
                    </div>
                    <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>" class="card-action">
                        <i class="icon-eye-open"></i> Ver Todas
                    </a>
                </div>

                <?php if (!empty($atividades_recentes)): ?>
                <div>
                    <?php foreach (array_slice($atividades_recentes, 0, 5) as $atividade):
                        $ativ_class = ($atividade->status == 'concluida') ? 'concluida' : 'pendente';
                    ?>
                    <div class="atividade-recente <?php echo $ativ_class; ?>">
                        <div class="atividade-recente-icon">
                            <i class="icon-tasks"></i>
                        </div>
                        <div class="atividade-recente-content">
                            <div class="atividade-recente-title"><?php echo htmlspecialchars($atividade->titulo ?? 'Atividade #' . $atividade->id); ?></div>
                            <div class="atividade-recente-meta">
                                <i class="icon-calendar"></i> <?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?>
                                <?php if (!empty($atividade->tecnico_nome)): ?>
                                | <i class="icon-user"></i> <?php echo htmlspecialchars($atividade->tecnico_nome); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="icon-check"></i>
                    <h4>Sem atividades recentes</h4>
                    <p>As atividades aparecerão aqui.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Estatísticas -->
            <?php if (!empty($estatisticas_atividades)): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-bar-chart"></i>
                        Estatísticas
                    </div>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Total de Atividades</div>
                        <div class="info-value">
                            <i class="icon-tasks" style="color: #667eea;"></i>
                            <?php echo $estatisticas_atividades['total_atividades'] ?? 0; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Concluídas</div>
                        <div class="info-value">
                            <i class="icon-check" style="color: #11998e;"></i>
                            <?php echo $estatisticas_atividades['concluidas'] ?? 0; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Em Andamento</div>
                        <div class="info-value">
                            <i class="icon-play" style="color: #f39c12;"></i>
                            <?php echo $estatisticas_atividades['em_andamento'] ?? 0; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Horas Trabalhadas</div>
                        <div class="info-value">
                            <i class="icon-time" style="color: #9b59b6;"></i>
                            <?php echo round(($estatisticas_atividades['tempo_total_minutos'] ?? 0) / 60, 1); ?>h
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Toggle de etapas
function toggleEtapa(etapaId) {
    const atividadesDiv = document.getElementById('atividades-' + etapaId);
    const toggleBtn = document.getElementById('toggle-' + etapaId);

    if (atividadesDiv) {
        atividadesDiv.classList.toggle('expanded');
        toggleBtn.classList.toggle('expanded');
    }
}

// Abrir wizard (reutilizando função existente se disponível)
function abrirWizard() {
    if (typeof window.abrirWizardModal === 'function') {
        window.abrirWizardModal();
    } else {
        // Fallback: redirecionar para página de etapas
        window.location.href = '<?php echo site_url('obras/etapas/' . $obra->id); ?>';
    }
}

// Animação de entrada
$(document).ready(function() {
    $('.card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });
});
</script>

<!-- Wizard Modal -->
<div id="wizardModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="wizardModalLabel" aria-hidden="true" style="width: 800px; max-width: 90%; left: 50%; margin-left: -400px;">
    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 4px 4px 0 0;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.8;">&times;</button>
        <h3 id="wizardModalLabel"><i class="icon-magic"></i> Nova Etapa + Atividades</h3>
    </div>
    <form id="wizardForm" action="<?php echo site_url('obras/salvarWizard/' . $obra->id); ?>" method="post">
        <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
            <div class="row-fluid">
                <div class="span12">
                    <h4><i class="icon-tasks"></i> Informações da Etapa</h4>
                    <hr style="margin: 10px 0;">

                    <div class="row-fluid">
                        <div class="span2">
                            <label for="etapa_numero">Número <span class="required">*</span></label>
                            <input type="number" name="etapa_numero" id="etapa_numero" class="span12" value="<?php echo (count($etapas ?? []) + 1); ?>" min="1" required>
                        </div>
                        <div class="span10">
                            <label for="etapa_nome">Nome da Etapa <span class="required">*</span></label>
                            <input type="text" name="etapa_nome" id="etapa_nome" class="span12" placeholder="Ex: Fundação, Estrutura, Acabamento..." required>
                        </div>
                    </div>

                    <div class="row-fluid" style="margin-top: 10px;">
                        <div class="span12">
                            <label for="etapa_descricao">Descrição</label>
                            <textarea name="etapa_descricao" id="etapa_descricao" class="span12" rows="2" placeholder="Descreva o que será feito nesta etapa..."></textarea>
                        </div>
                    </div>

                    <div class="row-fluid" style="margin-top: 10px;">
                        <div class="span6">
                            <label for="etapa_data_inicio">Data de Início Prevista</label>
                            <input type="date" name="etapa_data_inicio" id="etapa_data_inicio" class="span12">
                        </div>
                        <div class="span6">
                            <label for="etapa_data_fim">Data de Término Prevista</label>
                            <input type="date" name="etapa_data_fim" id="etapa_data_fim" class="span12">
                        </div>
                    </div>

                    <h4 style="margin-top: 25px;"><i class="icon-check"></i> Atividades da Etapa</h4>
                    <hr style="margin: 10px 0;">

                    <div id="atividadesContainer">
                        <!-- Atividades serão adicionadas aqui -->
                    </div>

                    <button type="button" class="btn btn-block" onclick="adicionarAtividade()" style="margin-top: 10px; border: 2px dashed #ddd; background: #f9f9f9; color: #666;">
                        <i class="icon-plus"></i> Adicionar Atividade
                    </button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="icon-save"></i> Salvar Etapa e Atividades</button>
        </div>
    </form>
</div>

<script>
// Variáveis do wizard
let atividadeCount = 0;

// Abrir modal do wizard
function abrirWizard() {
    $('#wizardModal').modal('show');
    // Limpar e adicionar primeira atividade
    document.getElementById('atividadesContainer').innerHTML = '';
    adicionarAtividade();
}

// Abrir modal do wizard (compatibilidade)
window.abrirWizardModal = abrirWizard;

// Adicionar campo de atividade
function adicionarAtividade() {
    const container = document.getElementById('atividadesContainer');
    const index = atividadeCount++;

    const html = `
        <div class="row-fluid atividade-item" style="margin-bottom: 10px;" id="atividade-${index}">
            <div class="span8">
                <input type="text" name="atividades[${index}][titulo]" class="span12" placeholder="Título da atividade" required>
            </div>
            <div class="span3">
                <select name="atividades[${index}][tipo]" class="span12">
                    <option value="trabalho">Trabalho</option>
                    <option value="visita">Visita</option>
                    <option value="manutencao">Manutenção</option>
                    <option value="impedimento">Impedimento</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div class="span1">
                <button type="button" class="btn btn-danger btn-block" onclick="removerAtividade(${index})" title="Remover">
                    <i class="icon-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
}

// Remover campo de atividade
function removerAtividade(index) {
    const item = document.getElementById('atividade-' + index);
    if (item) {
        item.remove();
    }
}

// Validar formulário antes de enviar
document.getElementById('wizardForm').addEventListener('submit', function(e) {
    const numero = document.getElementById('etapa_numero').value;
    const nome = document.getElementById('etapa_nome').value;

    if (!numero || !nome) {
        e.preventDefault();
        alert('Preencha o número e o nome da etapa.');
        return false;
    }
});
</script>
