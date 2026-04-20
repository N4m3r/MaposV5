<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Tema Moderno Obras Unificado -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
/* ============================================
   VISUALIZAÇÃO DA OBRA - TEMA UNIFICADO
   ============================================ */

.obra-view-container {
    padding: 24px;
    max-width: 1600px;
    margin: 0 auto;
}

/* Header com gradiente */
.obra-view-header {
    background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    border-radius: var(--radius-xl, 20px);
    padding: 32px;
    color: white;
    margin-bottom: 24px;
    box-shadow: var(--shadow-xl, 0 20px 40px rgba(0,0,0,0.15));
    position: relative;
    overflow: hidden;
}

.obra-view-header::before {
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

.obra-view-header.andamento {
    background: var(--gradient-info, linear-gradient(135deg, #4facfe 0%, #00f2fe 100%));
}

.obra-view-header.concluida {
    background: var(--gradient-success, linear-gradient(135deg, #11998e 0%, #38ef7d 100%));
}

.obra-view-header.paralisada {
    background: var(--gradient-danger, linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%));
}

.obra-view-header.prospeccao {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #333;
}

.obra-header-wrapper {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 24px;
}

.obra-header-left { flex: 1; min-width: 300px; }

.obra-breadcrumb {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.obra-breadcrumb a {
    color: inherit;
    opacity: 0.8;
    text-decoration: none;
    transition: opacity 0.3s;
}

.obra-breadcrumb a:hover { opacity: 1; text-decoration: underline; }

.obra-view-title {
    margin: 0 0 12px 0;
    font-size: 32px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.obra-view-cliente {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 14px;
}

.obra-header-right { text-align: right; }

.obra-status-badge-large {
    font-size: 16px;
    font-weight: 700;
    padding: 12px 24px;
    border-radius: 50px;
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(10px);
    display: inline-block;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Progresso principal */
.obra-progress-main {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.2);
}

.obra-progress-bar-main {
    height: 12px;
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 12px;
}

.obra-progress-fill-main {
    height: 100%;
    background: white;
    border-radius: 10px;
    transition: width 0.5s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.obra-progress-info-main {
    display: flex;
    justify-content: space-between;
    font-size: 18px;
    font-weight: 700;
}

/* Barra de ações rápidas */
.obra-actions-quick {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.obra-action-btn {
    padding: 14px 24px;
    border-radius: var(--radius-lg, 12px);
    border: none;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all var(--transition-normal, 0.3s ease);
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: white;
    box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.1));
}

.obra-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.btn-equipe { background: #11998e; }
.btn-etapas { background: #667eea; }
.btn-atividades { background: #764ba2; }
.btn-relatorio { background: #f39c12; }
.btn-editar { background: #e74c3c; }
.btn-wizard {
    background: var(--gradient-success, linear-gradient(135deg, #11998e 0%, #38ef7d 100%));
    font-size: 15px;
    padding: 14px 28px;
}

/* Grid de conteúdo */
.obra-content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}

.obra-main-column { display: flex; flex-direction: column; gap: 24px; }
.obra-sidebar-column { display: flex; flex-direction: column; gap: 24px; }

/* Cards de seção */
.obra-section-card {
    background: var(--widget-box, #ffffff);
    border-radius: var(--radius-xl, 20px);
    padding: 28px;
    box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.07));
    border: 1px solid var(--border-color, rgba(0,0,0,0.05));
}

.obra-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--border-color, #f0f0f0);
}

.obra-section-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--title, #333);
    display: flex;
    align-items: center;
    gap: 10px;
}

.obra-section-title i {
    color: #667eea;
    font-size: 24px;
}

.obra-section-action {
    padding: 10px 18px;
    border-radius: var(--radius-md, 10px);
    background: #667eea;
    color: white;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.obra-section-action:hover {
    background: #5568d3;
    transform: translateY(-2px);
}

/* Grid de informações */
.obra-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.obra-info-item {
    background: var(--body-color, #f8f9fa);
    padding: 18px;
    border-radius: var(--radius-md, 12px);
    transition: all 0.3s;
}

.obra-info-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.obra-info-item.full-width { grid-column: span 2; }

.obra-info-label {
    font-size: 12px;
    color: var(--subtitle, #888);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.obra-info-value {
    font-size: 15px;
    font-weight: 600;
    color: var(--title, #333);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Timeline de etapas */
.etapas-timeline {
    position: relative;
    padding-left: 32px;
}

.etapas-timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--border-color, #e8e8e8);
    border-radius: 3px;
}

.etapa-timeline-item {
    position: relative;
    padding-bottom: 24px;
}

.etapa-timeline-item:last-child { padding-bottom: 0; }

.etapa-timeline-dot {
    position: absolute;
    left: -28px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--body-color, #ddd);
    border: 4px solid var(--widget-box, white);
    box-shadow: 0 0 0 3px var(--border-color, #e8e8e8);
}

.etapa-timeline-dot.concluida {
    background: var(--obra-status-concluida, #27ae60);
    box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.3);
}

.etapa-timeline-dot.andamento {
    background: var(--obra-status-em-andamento, #3498db);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.etapa-timeline-content {
    background: var(--body-color, #f8f9fa);
    padding: 18px;
    border-radius: var(--radius-md, 12px);
    border-left: 4px solid transparent;
    transition: all 0.3s;
}

.etapa-timeline-content:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.etapa-timeline-content.concluida { border-left-color: #27ae60; }
.etapa-timeline-content.andamento { border-left-color: #3498db; }
.etapa-timeline-content.pendente { border-left-color: #f39c12; }

.etapa-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.etapa-title {
    font-weight: 700;
    color: var(--title, #333);
    font-size: 16px;
}

.etapa-status-badge {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
}

.etapa-status-badge.concluida {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.etapa-status-badge.andamento {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

.etapa-status-badge.pendente {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
}

.etapa-meta {
    font-size: 13px;
    color: var(--subtitle, #888);
    margin-top: 4px;
}

.etapa-atividades-count {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 8px;
    font-size: 12px;
    color: #667eea;
    font-weight: 600;
}

/* Lista de atividades rápidas */
.atividades-lista-rapida { display: flex; flex-direction: column; gap: 12px; }

.atividade-item-rapido {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    background: var(--body-color, #f8f9fa);
    border-radius: var(--radius-md, 10px);
    border-left: 3px solid #667eea;
    transition: all 0.3s;
}

.atividade-item-rapido:hover {
    transform: translateX(5px);
    background: var(--border-color, #e8e8e8);
}

.atividade-item-rapido.concluida { border-left-color: #27ae60; }
.atividade-item-rapido.agendada { border-left-color: #95a5a6; }

.atividade-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.atividade-content { flex: 1; min-width: 0; }

.atividade-titulo {
    font-weight: 600;
    color: var(--title, #333);
    font-size: 14px;
    margin-bottom: 2px;
}

.atividade-meta {
    font-size: 12px;
    color: var(--subtitle, #888);
}

/* Cards de equipe */
.equipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 16px;
}

.equipe-card {
    text-align: center;
    padding: 16px;
    background: var(--body-color, #f8f9fa);
    border-radius: var(--radius-md, 12px);
    transition: all 0.3s;
}

.equipe-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.equipe-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    color: white;
    font-size: 24px;
}

.equipe-nome {
    font-weight: 600;
    color: var(--title, #333);
    font-size: 14px;
    margin-bottom: 4px;
}

.equipe-funcao {
    font-size: 12px;
    color: var(--subtitle, #888);
}

/* ============================================
   WIZARD MODAL - CRIAÇÃO DE ETAPAS/ATIVIDADES
   ============================================ */

.wizard-modal .modal-header {
    background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    color: white;
    padding: 28px 32px;
    border: none;
    border-radius: 20px 20px 0 0;
}

.wizard-modal .modal-header h3 {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.wizard-modal .modal-body {
    padding: 32px;
    background: var(--body-color, #f8f9fa);
    min-height: 400px;
}

.wizard-modal .modal-footer {
    padding: 20px 32px;
    background: var(--widget-box, white);
    border-top: 1px solid var(--border-color, #e0e0e0);
    border-radius: 0 0 20px 20px;
    display: flex;
    justify-content: space-between;
}

/* Steps do wizard */
.wizard-steps {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 32px;
}

.wizard-step {
    display: flex;
    align-items: center;
    gap: 8px;
}

.wizard-step-number {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--border-color, #e0e0e0);
    color: var(--subtitle, #888);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
}

.wizard-step.active .wizard-step-number {
    background: #667eea;
    color: white;
}

.wizard-step.completed .wizard-step-number {
    background: #27ae60;
    color: white;
}

.wizard-step-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--subtitle, #888);
}

.wizard-step.active .wizard-step-label {
    color: #667eea;
}

.wizard-step-line {
    width: 40px;
    height: 2px;
    background: var(--border-color, #e0e0e0);
}

.wizard-step.completed + .wizard-step-line,
.wizard-step.completed .wizard-step-line {
    background: #27ae60;
}

/* Conteúdo do wizard */
.wizard-content { display: none; }
.wizard-content.active { display: block; animation: fadeIn 0.3s ease; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.wizard-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--title, #333);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.wizard-title i { color: #667eea; }

/* Formulários do wizard */
.wizard-form-group {
    margin-bottom: 20px;
}

.wizard-form-label {
    display: block;
    font-weight: 600;
    color: var(--title, #333);
    margin-bottom: 8px;
    font-size: 14px;
}

.wizard-form-label .required {
    color: #e74c3c;
}

.wizard-form-input,
.wizard-form-select,
.wizard-form-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border-color, #e0e0e0);
    border-radius: var(--radius-md, 10px);
    font-size: 15px;
    color: var(--title, #333);
    background: var(--widget-box, white);
    transition: all 0.3s;
    box-sizing: border-box;
}

.wizard-form-input:focus,
.wizard-form-select:focus,
.wizard-form-textarea:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.wizard-form-textarea {
    resize: vertical;
    min-height: 100px;
}

.wizard-form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

/* Lista de atividades no wizard */
.wizard-atividades-lista {
    max-height: 300px;
    overflow-y: auto;
    border: 2px solid var(--border-color, #e0e0e0);
    border-radius: var(--radius-md, 10px);
    padding: 16px;
    background: var(--widget-box, white);
    margin-bottom: 16px;
}

.wizard-atividade-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--body-color, #f8f9fa);
    border-radius: var(--radius-sm, 8px);
    margin-bottom: 8px;
}

.wizard-atividade-item:last-child { margin-bottom: 0; }

.wizard-atividade-input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid var(--border-color, #e0e0e0);
    border-radius: var(--radius-sm, 6px);
    font-size: 14px;
}

.wizard-btn-remover {
    padding: 8px 12px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: var(--radius-sm, 6px);
    cursor: pointer;
    font-size: 12px;
}

.wizard-btn-adicionar {
    padding: 10px 16px;
    background: var(--body-color, #f8f9fa);
    border: 2px dashed var(--border-color, #ccc);
    border-radius: var(--radius-md, 10px);
    color: var(--subtitle, #888);
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    width: 100%;
}

.wizard-btn-adicionar:hover {
    border-color: #667eea;
    color: #667eea;
}

/* Revisão do wizard */
.wizard-resumo {
    background: var(--widget-box, white);
    border-radius: var(--radius-lg, 16px);
    padding: 24px;
}

.wizard-resumo-section {
    margin-bottom: 24px;
}

.wizard-resumo-section:last-child { margin-bottom: 0; }

.wizard-resumo-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--subtitle, #888);
    text-transform: uppercase;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border-color, #e0e0e0);
}

.wizard-resumo-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
}

.wizard-resumo-label { color: var(--subtitle, #888); }

.wizard-resumo-value {
    font-weight: 600;
    color: var(--title, #333);
}

/* Botões do wizard */
.wizard-btn {
    padding: 12px 24px;
    border-radius: var(--radius-md, 10px);
    border: none;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.wizard-btn-primary {
    background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    color: white;
}

.wizard-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.wizard-btn-secondary {
    background: var(--body-color, #f8f9fa);
    color: var(--title, #333);
}

.wizard-btn-secondary:hover {
    background: var(--border-color, #e0e0e0);
}

.wizard-btn-success {
    background: var(--gradient-success, linear-gradient(135deg, #11998e 0%, #38ef7d 100%));
    color: white;
}

.wizard-btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
}

/* ============================================
   DARK MODE SUPPORT
   ============================================ */

[data-theme="dark"] .obra-section-card,
[data-theme="dark"] .wizard-modal .modal-footer {
    background: var(--dark-2, #2d3748);
    border-color: var(--dark-4, #4a5568);
}

[data-theme="dark"] .obra-section-title,
[data-theme="dark"] .obra-info-value,
[data-theme="dark"] .etapa-title,
[data-theme="dark"] .atividade-titulo,
[data-theme="dark"] .equipe-nome,
[data-theme="dark"] .wizard-title,
[data-theme="dark"] .wizard-resumo-value,
[data-theme="dark"] .wizard-form-label {
    color: var(--white, #fff);
}

[data-theme="dark"] .obra-info-item,
[data-theme="dark"] .etapa-timeline-content,
[data-theme="dark"] .atividade-item-rapido,
[data-theme="dark"] .equipe-card,
[data-theme="dark"] .wizard-modal .modal-body,
[data-theme="dark"] .wizard-atividade-item,
[data-theme="dark"] .wizard-btn-adicionar,
[data-theme="dark"] .wizard-btn-secondary {
    background: var(--dark-3, #3d4852);
}

[data-theme="dark"] .obra-info-label,
[data-theme="dark"] .obra-section-title i,
[data-theme="dark"] .etapa-meta,
[data-theme="dark"] .atividade-meta,
[data-theme="dark"] .equipe-funcao,
[data-theme="dark"] .wizard-resumo-title,
[data-theme="dark"] .wizard-resumo-label,
[data-theme="dark"] .wizard-step-label {
    color: var(--dark-7, #a0aec0);
}

[data-theme="dark"] .wizard-form-input,
[data-theme="dark"] .wizard-form-select,
[data-theme="dark"] .wizard-form-textarea,
[data-theme="dark"] .wizard-atividade-input,
[data-theme="dark"] .wizard-resumo,
[data-theme="dark"] .wizard-atividades-lista {
    background: var(--dark-2, #2d3748);
    border-color: var(--dark-4, #4a5568);
    color: var(--white, #fff);
}

[data-theme="dark"] .etapas-timeline::before,
[data-theme="dark"] .wizard-step-line {
    background: var(--dark-4, #4a5568);
}

[data-theme="dark"] .etapa-timeline-dot {
    background: var(--dark-4, #4a5568);
    border-color: var(--dark-2, #2d3748);
    box-shadow: 0 0 0 3px var(--dark-4, #4a5568);
}

/* Responsividade */
@media (max-width: 992px) {
    .obra-content-grid { grid-template-columns: 1fr; }
    .obra-header-wrapper { flex-direction: column; }
    .obra-header-right { text-align: left; width: 100%; }
    .obra-actions-quick { justify-content: center; }
    .wizard-form-row { grid-template-columns: 1fr; }
    .wizard-steps { flex-wrap: wrap; }
    .wizard-step-line { display: none; }
}

@media (max-width: 768px) {
    .obra-view-container { padding: 16px; }
    .obra-view-header { padding: 24px; }
    .obra-view-title { font-size: 24px; }
    .obra-info-grid { grid-template-columns: 1fr; }
    .obra-info-item.full-width { grid-column: span 1; }
    .equipe-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="obra-view-container">
    <?php
    $status_header_class = '';
    $status_label = '';
    switch ($obra->status) {
        case 'em-andamento':
        case 'em execucao':
            $status_header_class = 'andamento';
            $status_label = 'Em Andamento';
            break;
        case 'concluida':
            $status_header_class = 'concluida';
            $status_label = 'Concluída';
            break;
        case 'paralisada':
            $status_header_class = 'paralisada';
            $status_label = 'Paralisada';
            break;
        case 'prospeccao':
            $status_header_class = 'prospeccao';
            $status_label = 'Prospecção';
            break;
        default:
            $status_header_class = '';
            $status_label = ucfirst($obra->status);
    }
    $progresso = $obra->percentual_concluido ?? 0;
    ?>

    <!-- Header da Obra -->
    <div class="obra-view-header <?php echo $status_header_class; ?>">
        <div class="obra-header-wrapper">
            <div class="obra-header-left">
                <div class="obra-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><i class="icon-arrow-left"></i> Obras</a>
                    <span>/</span>
                    <span>Visualizar</span>
                </div>
                <h1 class="obra-view-title">
                    <i class="icon-building"></i>
                    <?php echo htmlspecialchars($obra->nome); ?>
                </h1>
                <div class="obra-view-cliente">
                    <i class="icon-user"></i>
                    <?php echo htmlspecialchars($obra->cliente_nome ?? 'Sem cliente'); ?>
                </div>
            </div>

            <div class="obra-header-right">
                <div class="obra-status-badge-large">
                    <?php echo $status_label; ?>
                </div>

                <div class="obra-progress-main">
                    <div class="obra-progress-bar-main">
                        <div class="obra-progress-fill-main" style="width: <?php echo $progresso; ?>%"></div>
                    </div>
                    <div class="obra-progress-info-main">
                        <span>Progresso da Obra</span>
                        <span><?php echo $progresso; ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Ações -->
    <div class="obra-actions-quick">
        <button class="obra-action-btn btn-wizard" onclick="abrirWizard()">
            <i class="icon-magic"></i>
            Wizard: Nova Etapa + Atividades
        </button>

        <a href="<?php echo site_url('obras/equipe/' . $obra->id); ?>" class="obra-action-btn btn-equipe">
            <i class="icon-group"></i> Gerenciar Equipe
        </a>

        <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>" class="obra-action-btn btn-atividades">
            <i class="icon-tasks"></i> Todas Atividades
        </a>

        <a href="<?php echo site_url('obras/editar/' . $obra->id); ?>" class="obra-action-btn btn-editar">
            <i class="icon-edit"></i> Editar Obra
        </a>

        <a href="<?php echo site_url('obras'); ?>" class="obra-action-btn btn-relatorio">
            <i class="icon-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Grid de Conteúdo -->
    <div class="obra-content-grid">
        <!-- Coluna Principal -->
        <div class="obra-main-column">

            <!-- Informações da Obra -->
            <div class="obra-section-card">
                <div class="obra-section-header">
                    <div class="obra-section-title">
                        <i class="icon-info-sign"></i>
                        Informações Gerais
                    </div>
                </div>

                <div class="obra-info-grid">
                    <div class="obra-info-item">
                        <div class="obra-info-label">Código da Obra</div>
                        <div class="obra-info-value">
                            <i class="icon-barcode"></i>
                            <?php echo htmlspecialchars($obra->codigo ?? 'Não definido'); ?>
                        </div>
                    </div>

                    <div class="obra-info-item">
                        <div class="obra-info-label">Tipo</div>
                        <div class="obra-info-value">
                            <i class="icon-cog"></i>
                            <?php echo htmlspecialchars($obra->tipo_obra ?? 'Não definido'); ?>
                        </div>
                    </div>

                    <div class="obra-info-item full-width">
                        <div class="obra-info-label">Endereço</div>
                        <div class="obra-info-value">
                            <i class="icon-map-marker"></i>
                            <?php echo htmlspecialchars($obra->endereco ?? 'Não informado'); ?>
                        </div>
                    </div>

                    <div class="obra-info-item">
                        <div class="obra-info-label">Data de Início</div>
                        <div class="obra-info-value">
                            <i class="icon-calendar"></i>
                            <?php echo $obra->data_inicio_contrato ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : 'Não definido'; ?>
                        </div>
                    </div>

                    <div class="obra-info-item">
                        <div class="obra-info-label">Previsão de Término</div>
                        <div class="obra-info-value">
                            <i class="icon-flag-checkered"></i>
                            <?php echo $obra->data_fim_prevista ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : 'Não definido'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Etapas -->
            <div class="obra-section-card">
                <div class="obra-section-header">
                    <div class="obra-section-title">
                        <i class="icon-tasks"></i>
                        Etapas da Obra
                    </div>
                    <button class="obra-section-action" onclick="abrirWizard()">
                        <i class="icon-plus"></i> Nova Etapa
                    </button>
                </div>

                <?php if (!empty($etapas)): ?>
                <div class="etapas-timeline">
                    <?php foreach ($etapas as $etapa): ?
                    <?php
                    $etapa_status = $etapa->status ?? 'pendente';
                    $etapa_class = '';
                    $dot_class = '';
                    switch ($etapa_status) {
                        case 'concluida':
                            $etapa_class = 'concluida';
                            $dot_class = 'concluida';
                            break;
                        case 'em-andamento':
                        case 'em_andamento':
                            $etapa_class = 'andamento';
                            $dot_class = 'andamento';
                            break;
                        default:
                            $etapa_class = 'pendente';
                            $dot_class = '';
                    }
                    ?>
                    <div class="etapa-timeline-item">
                        <div class="etapa-timeline-dot <?php echo $dot_class; ?>"></div>

                        <div class="etapa-timeline-content <?php echo $etapa_class; ?>">
                            <div class="etapa-header">
                                <div class="etapa-title">
                                    #<?php echo $etapa->numero_etapa; ?> - <?php echo htmlspecialchars($etapa->nome); ?>
                                </div>
                                <span class="etapa-status-badge <?php echo $etapa_class; ?>">
                                    <?php echo ucfirst(str_replace('-', ' ', $etapa_status)); ?>
                                </span>
                            </div>

                            <div class="etapa-meta">
                                <i class="icon-calendar"></i>
                                <?php
                                if ($etapa->data_inicio_prevista) {
                                    echo 'Início: ' . date('d/m/Y', strtotime($etapa->data_inicio_prevista));
                                    if ($etapa->data_fim_prevista) {
                                        echo ' | Término: ' . date('d/m/Y', strtotime($etapa->data_fim_prevista));
                                    }
                                } else {
                                    echo 'Datas não definidas';
                                }
                                ?>
                            </div>

                            <div class="etapa-atividades-count">
                                <i class="icon-tasks"></i>
                                <?php echo $etapa->total_atividades ?? 0; ?> atividade(s)
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div style="text-align: center; padding: 40px; color: var(--subtitle, #888);">
                    <i class="icon-tasks" style="font-size: 48px; margin-bottom: 16px; display: block; opacity: 0.5;"></i>
                    <p>Nenhuma etapa cadastrada</p>
                    <button onclick="abrirWizard()" class="obra-section-action" style="margin-top: 16px;">
                        <i class="icon-plus"></i> Criar Primeira Etapa
                    </button>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="obra-sidebar-column">

            <!-- Atividades Recentes -->
            <div class="obra-section-card">
                <div class="obra-section-header">
                    <div class="obra-section-title">
                        <i class="icon-calendar"></i>
                        Atividades Recentes
                    </div>
                    <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>" class="obra-section-action">
                        Ver Todas
                    </a>
                </div>

                <?php if (!empty($atividades_recentes)): ?
                <div class="atividades-lista-rapida">
                    <?php foreach (array_slice($atividades_recentes, 0, 5) as $atividade): ?
                    <?php $ativ_class = ($atividade->status == 'concluida') ? 'concluida' : 'agendada'; ?>

                    <div class="atividade-item-rapido <?php echo $ativ_class; ?>">
                        <div class="atividade-icon">
                            <i class="icon-tasks"></i>
                        </div>

                        <div class="atividade-content">
                            <div class="atividade-titulo"><?php echo htmlspecialchars($atividade->titulo); ?></div>
                            <div class="atividade-meta">
                                <i class="icon-calendar"></i>
                                <?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?>
                                <?php if ($atividade->tecnico_nome): ?>
                                <span>| <i class="icon-user"></i> <?php echo htmlspecialchars($atividade->tecnico_nome); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?
                <div style="text-align: center; padding: 30px; color: var(--subtitle, #888);">
                    <p>Nenhuma atividade recente</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Equipe -->
            <div class="obra-section-card">
                <div class="obra-section-header">
                    <div class="obra-section-title">
                        <i class="icon-group"></i>
                        Equipe
                    </div>
                    <a href="<?php echo site_url('obras/equipe/' . $obra->id); ?>" class="obra-section-action">
                        Gerenciar
                    </a>
                </div>

                <?php if (!empty($equipe)): ?
                <div class="equipe-grid">
                    <?php foreach (array_slice($equipe, 0, 6) as $membro): ?>
                    <div class="equipe-card">
                        <div class="equipe-avatar">
                            <i class="icon-user"></i>
                        </div>
                        <div class="equipe-nome"><?php echo htmlspecialchars($membro->nome); ?></div>
                        <div class="equipe-funcao"><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?
                <div style="text-align: center; padding: 30px; color: var(--subtitle, #888);">
                    <p>Nenhum membro na equipe</p>
                    <a href="<?php echo site_url('obras/equipe/' . $obra->id); ?>" class="obra-section-action" style="margin-top: 16px; display: inline-block;">
                        <i class="icon-plus"></i> Adicionar
                    </a>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ==========================================
     WIZARD MODAL - NOVA ETAPA + ATIVIDADES
     ========================================== -->
<div id="wizardModal" class="modal hide fade wizard-modal" tabindex="-1" role="dialog" aria-labelledby="wizardModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="wizardModalLabel">
            <i class="icon-magic"></i>
            Wizard: Nova Etapa + Atividades
        </h3>
    </div>

    <form id="wizardForm" action="<?php echo site_url('obras/salvarWizard/' . $obra->id); ?>" method="post">
        <div class="modal-body">

            <!-- Steps -->
            <div class="wizard-steps">
                <div class="wizard-step active" data-step="1">
                    <div class="wizard-step-number">1</div>
                    <span class="wizard-step-label">Etapa</span>
                </div>
                <div class="wizard-step-line"></div>

                <div class="wizard-step" data-step="2">
                    <div class="wizard-step-number">2</div>
                    <span class="wizard-step-label">Atividades</span>
                </div>
                <div class="wizard-step-line"></div>

                <div class="wizard-step" data-step="3">
                    <div class="wizard-step-number">3</div>
                    <span class="wizard-step-label">Revisão</span>
                </div>
            </div>

            <!-- Passo 1: Informações da Etapa -->
            <div id="wizardStep1" class="wizard-content active">
                <div class="wizard-title">
                    <i class="icon-tasks"></i>
                    Informações da Etapa
                </div>

                <div class="wizard-form-row">
                    <div class="wizard-form-group">
                        <label class="wizard-form-label">
                            Número da Etapa <span class="required">*</span>
                        </label>
                        <input type="number" name="etapa_numero" class="wizard-form-input"
                               value="<?php echo (count($etapas ?? []) + 1); ?>" min="1" required>
                    </div>

                    <div class="wizard-form-group">
                        <label class="wizard-form-label">
                            Nome da Etapa <span class="required">*</span>
                        </label>
                        <input type="text" name="etapa_nome" class="wizard-form-input"
                               placeholder="Ex: Fundação, Estrutura, Acabamento..." required>
                    </div>
                </div>

                <div class="wizard-form-group">
                    <label class="wizard-form-label">Descrição</label>
                    <textarea name="etapa_descricao" class="wizard-form-textarea"
                              placeholder="Descreva o que será feito nesta etapa..."></textarea>
                </div>

                <div class="wizard-form-row">
                    <div class="wizard-form-group">
                        <label class="wizard-form-label">Data de Início Prevista</label>
                        <input type="date" name="etapa_data_inicio" class="wizard-form-input">
                    </div>

                    <div class="wizard-form-group">
                        <label class="wizard-form-label">Data de Término Prevista</label>
                        <input type="date" name="etapa_data_fim" class="wizard-form-input">
                    </div>
                </div>
            </div>

            <!-- Passo 2: Atividades -->
            <div id="wizardStep2" class="wizard-content">
                <div class="wizard-title">
                    <i class="icon-tasks"></i>
                    Atividades da Etapa
                </div>

                <p style="margin-bottom: 16px; color: var(--subtitle, #888); font-size: 14px;">
                    Adicione as atividades que serão realizadas nesta etapa. Você pode adicionar quantas quiser.
                </p>

                <div id="wizardAtividadesLista" class="wizard-atividades-lista">
                    <!-- Atividades serão adicionadas aqui -->
                </div>

                <button type="button" class="wizard-btn-adicionar" onclick="adicionarAtividadeWizard()">
                    <i class="icon-plus"></i> Adicionar Atividade
                </button>
            </div>

            <!-- Passo 3: Revisão -->
            <div id="wizardStep3" class="wizard-content">
                <div class="wizard-title">
                    <i class="icon-check"></i>
                    Revisão e Confirmação
                </div>

                <div class="wizard-resumo">
                    <div class="wizard-resumo-section">
                        <div class="wizard-resumo-title">Etapa</div>

                        <div class="wizard-resumo-item">
                            <span class="wizard-resumo-label">Número:</span>
                            <span id="resumoEtapaNumero" class="wizard-resumo-value"></span>
                        </div>

                        <div class="wizard-resumo-item">
                            <span class="wizard-resumo-label">Nome:</span>
                            <span id="resumoEtapaNome" class="wizard-resumo-value"></span>
                        </div>

                        <div class="wizard-resumo-item">
                            <span class="wizard-resumo-label">Descrição:</span>
                            <span id="resumoEtapaDescricao" class="wizard-resumo-value"></span>
                        </div>
                    </div>

                    <div class="wizard-resumo-section">
                        <div class="wizard-resumo-title">Atividades (<span id="resumoTotalAtividades">0</span>)</div>

                        <div id="resumoAtividadesLista">
                            <!-- Lista de atividades -->
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div>
                <button type="button" id="wizardBtnAnterior" class="wizard-btn wizard-btn-secondary" onclick="wizardAnterior()" style="display: none;">
                    <i class="icon-arrow-left"></i> Anterior
                </button>
            </div>

            <div>
                <button type="button" id="wizardBtnProximo" class="wizard-btn wizard-btn-primary" onclick="wizardProximo()">
                    Próximo <i class="icon-arrow-right"></i>
                </button>

                <button type="submit" id="wizardBtnSalvar" class="wizard-btn wizard-btn-success" style="display: none;">
                    <i class="icon-save"></i> Salvar Etapa e Atividades
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Variáveis do wizard
let wizardStepAtual = 1;
const wizardTotalSteps = 3;

// Abrir modal
function abrirWizard() {
    $('#wizardModal').modal('show');
    wizardReset();
}

// Resetar wizard
function wizardReset() {
    wizardStepAtual = 1;
    atualizarWizardUI();
    document.getElementById('wizardForm').reset();
    document.getElementById('wizardAtividadesLista').innerHTML = '';
    adicionarAtividadeWizard(); // Adiciona primeira atividade
}

// Navegação
function wizardProximo() {
    if (validarStepAtual()) {
        if (wizardStepAtual < wizardTotalSteps) {
            wizardStepAtual++;
            atualizarWizardUI();
        }
    }
}

function wizardAnterior() {
    if (wizardStepAtual > 1) {
        wizardStepAtual--;
        atualizarWizardUI();
    }
}

// Validação
function validarStepAtual() {
    if (wizardStepAtual === 1) {
        const numero = document.querySelector('[name="etapa_numero"]').value;
        const nome = document.querySelector('[name="etapa_nome"]').value;

        if (!numero || !nome) {
            alert('Preencha o número e o nome da etapa.');
            return false;
        }
    }
    return true;
}

// Atualizar UI do wizard
function atualizarWizardUI() {
    // Atualizar steps
    document.querySelectorAll('.wizard-step').forEach(step => {
        const stepNum = parseInt(step.dataset.step);
        step.classList.remove('active', 'completed');

        if (stepNum === wizardStepAtual) {
            step.classList.add('active');
        } else if (stepNum < wizardStepAtual) {
            step.classList.add('completed');
        }
    });

    // Mostrar/esconder conteúdos
    document.querySelectorAll('.wizard-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById('wizardStep' + wizardStepAtual).classList.add('active');

    // Atualizar botões
    document.getElementById('wizardBtnAnterior').style.display = wizardStepAtual > 1 ? 'flex' : 'none';
    document.getElementById('wizardBtnProximo').style.display = wizardStepAtual < wizardTotalSteps ? 'flex' : 'none';
    document.getElementById('wizardBtnSalvar').style.display = wizardStepAtual === wizardTotalSteps ? 'flex' : 'none';

    // Se for o step 3, atualizar resumo
    if (wizardStepAtual === 3) {
        atualizarResumo();
    }
}

// Adicionar atividade no wizard
function adicionarAtividadeWizard() {
    const container = document.getElementById('wizardAtividadesLista');
    const index = container.children.length;

    const html = `
        <div class="wizard-atividade-item" data-index="${index}">
            <input type="text" name="atividades[${index}][titulo]"
                   class="wizard-atividade-input"
                   placeholder="Título da atividade" required>
            <select name="atividades[${index}][tipo]" class="wizard-atividade-input" style="width: 120px;">
                <option value="trabalho">Trabalho</option>
                <option value="visita">Visita</option>
                <option value="manutencao">Manutenção</option>
                <option value="impedimento">Impedimento</option>
            </select>
            <button type="button" class="wizard-btn-remover" onclick="removerAtividadeWizard(${index})">
                <i class="icon-trash"></i>
            </button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
}

// Remover atividade do wizard
function removerAtividadeWizard(index) {
    const container = document.getElementById('wizardAtividadesLista');
    const item = container.querySelector(`[data-index="${index}"]`);
    if (item && container.children.length > 1) {
        item.remove();
    }
}

// Atualizar resumo do wizard
function atualizarResumo() {
    // Dados da etapa
    document.getElementById('resumoEtapaNumero').textContent = document.querySelector('[name="etapa_numero"]').value;
    document.getElementById('resumoEtapaNome').textContent = document.querySelector('[name="etapa_nome"]').value;
    document.getElementById('resumoEtapaDescricao').textContent = document.querySelector('[name="etapa_descricao"]').value || '-';

    // Atividades
    const atividades = document.querySelectorAll('.wizard-atividade-item');
    document.getElementById('resumoTotalAtividades').textContent = atividades.length;

    let html = '';
    atividades.forEach((item, index) => {
        const titulo = item.querySelector('input').value || '(sem título)';
        const tipo = item.querySelector('select').value;
        html += `
            <div class="wizard-resumo-item" style="padding-left: 16px;">
                <span>${index + 1}. ${titulo} <span style="color: #888;">(${tipo})</span></span>
            </div>
        `;
    });
    document.getElementById('resumoAtividadesLista').innerHTML = html;
}

// Animação de entrada dos cards
$(document).ready(function() {
    $('.obra-section-card').each(function(index) {
        $(this).hide().delay(index * 150).fadeIn(500);
    });
});
</script>
