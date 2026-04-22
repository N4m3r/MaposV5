<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Tema Moderno Obras Unificado -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
/* ============================================
   VISUALIZACAO DA OBRA - TEMA UNIFICADO
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

/* Barra de acoes rapidas */
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

/* Grid de conteudo */
.obra-content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}

.obra-main-column { display: flex; flex-direction: column; gap: 24px; }
.obra-sidebar-column { display: flex; flex-direction: column; gap: 24px; }

/* Cards de secao */
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

/* Grid de informacoes */
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

/* ============================================
   ATIVIDADES DO SISTEMA (WIZARD)
   ============================================ */

.atividades-wizard-section {
    background: var(--widget-box, #ffffff);
    border-radius: var(--radius-xl, 20px);
    padding: 28px;
    box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.07));
    border: 1px solid var(--border-color, rgba(0,0,0,0.05));
}

.atividades-wizard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--border-color, #f0f0f0);
}

.atividades-wizard-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--title, #333);
    display: flex;
    align-items: center;
    gap: 10px;
}

.atividades-wizard-title i {
    color: #11998e;
    font-size: 24px;
}

/* Estatisticas das atividades */
.atividades-estatisticas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
    margin-bottom: 24px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
}

.stat-card.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card.info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-card .number {
    font-size: 28px;
    font-weight: bold;
}

.stat-card .label {
    font-size: 13px;
    opacity: 0.9;
    margin-top: 4px;
}

/* Cards de atividade */
.atividade-execution-card {
    background: var(--body-color, #f8f9fa);
    border-radius: 16px;
    margin-bottom: 20px;
    overflow: hidden;
    border: 1px solid var(--border-color, #e8e8e8);
}

.atividade-execution-card:last-child {
    margin-bottom: 0;
}

.atividade-execution-header {
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid var(--border-color, #e0e0e0);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 12px;
}

.atividade-execution-card.status-concluida .atividade-execution-header {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
}

.atividade-execution-card.status-em_andamento .atividade-execution-header {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
}

.atividade-execution-card.status-pausada .atividade-execution-header {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
}

.atividade-execution-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.atividade-execution-title i {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.atividade-execution-title h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: var(--title, #333);
}

.atividade-execution-title small {
    display: block;
    font-size: 12px;
    color: var(--subtitle, #666);
    margin-top: 2px;
}

.atividade-status-tag {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.atividade-status-tag.concluida {
    background: #27ae60;
    color: white;
}

.atividade-status-tag.em_andamento {
    background: #f39c12;
    color: white;
}

.atividade-status-tag.pausada {
    background: #e74c3c;
    color: white;
}

.atividade-status-tag.finalizada {
    background: #95a5a6;
    color: white;
}

/* Grid de sessoes da atividade */
.atividade-sections-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    padding: 20px;
}

@media (max-width: 768px) {
    .atividade-sections-grid {
        grid-template-columns: 1fr;
    }
}

.atividade-section-box {
    background: white;
    border-radius: 12px;
    padding: 16px;
    border-left: 4px solid;
}

.atividade-section-box.registro { border-left-color: #667eea; }
.atividade-section-box.fotos { border-left-color: #11998e; }
.atividade-section-box.pausas { border-left-color: #f39c12; }
.atividade-section-box.finalizacao { border-left-color: #27ae60; }
.atividade-section-box.materiais { border-left-color: #9b59b6; }
.atividade-section-box.observacoes { border-left-color: #3498db; }

.atividade-section-header-small {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-weight: 600;
    font-size: 13px;
    color: var(--title, #333);
}

.atividade-section-header-small i {
    font-size: 16px;
}

.atividade-section-box.registro .atividade-section-header-small i { color: #667eea; }
.atividade-section-box.fotos .atividade-section-header-small i { color: #11998e; }
.atividade-section-box.pausas .atividade-section-header-small i { color: #f39c12; }
.atividade-section-box.finalizacao .atividade-section-header-small i { color: #27ae60; }
.atividade-section-box.materiais .atividade-section-header-small i { color: #9b59b6; }
.atividade-section-box.observacoes .atividade-section-header-small i { color: #3498db; }

/* Info de registro */
.registro-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.registro-info-item {
    display: flex;
    flex-direction: column;
}

.registro-info-item.full-width {
    grid-column: span 2;
}

.registro-info-label {
    font-size: 11px;
    color: #888;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.registro-info-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.registro-info-value.inicio { color: #27ae60; }
.registro-info-value.fim { color: #e74c3c; }

/* Fotos */
.fotos-grid-small {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
    gap: 8px;
}

.foto-thumb-small {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s;
}

.foto-thumb-small:hover {
    transform: scale(1.05);
}

.foto-count-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #e9ecef;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 12px;
    color: #666;
}

/* Pausas */
.pausas-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.pausa-item {
    background: #f8f9fa;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 13px;
}

.pausa-item.em_andamento {
    background: #fff3cd;
    border-left: 3px solid #f39c12;
}

.pausa-motivo {
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
}

.pausa-tempo {
    color: #666;
    font-size: 12px;
}

.pausa-observacao {
    color: #888;
    font-size: 11px;
    margin-top: 4px;
    font-style: italic;
}

/* Finalizacao */
.finalizacao-info {
    font-size: 13px;
}

.finalizacao-info p {
    margin: 4px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.finalizacao-info i {
    color: #27ae60;
    width: 16px;
}

/* Materiais */
.materiais-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.material-badge {
    background: #9b59b6;
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
}

/* Observacoes */
.observacao-text {
    font-size: 13px;
    color: #555;
    line-height: 1.5;
}

.observacao-text.problema {
    color: #e67e22;
}

.observacao-text.solucao {
    color: #27ae60;
}

/* Timeline de execucao */
.timeline-execution {
    position: relative;
    padding-left: 24px;
}

.timeline-execution::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 16px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -22px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #667eea;
    border: 2px solid white;
}

.timeline-dot.inicio { background: #27ae60; }
.timeline-dot.pausa { background: #f39c12; }
.timeline-dot.retorno { background: #3498db; }
.timeline-dot.fim { background: #e74c3c; }

.timeline-content {
    font-size: 13px;
}

.timeline-time {
    font-weight: 600;
    color: #333;
}

.timeline-action {
    color: #666;
}

/* Equipe */
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

/* Empty state */
.empty-state {
    text-align: center;
    padding: 40px;
    color: var(--subtitle, #888);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    opacity: 0.5;
}

/* ============================================
   WIZARD MODAL - CRIACAO DE ETAPAS/ATIVIDADES
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

/* Conteudo do wizard */
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

/* Formularios do wizard */
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

/* Revisao do wizard */
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

/* Botoes do wizard */
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
[data-theme="dark"] .atividades-wizard-section,
[data-theme="dark"] .wizard-modal .modal-footer,
[data-theme="dark"] .atividade-execution-card,
[data-theme="dark"] .atividade-section-box {
    background: var(--dark-2, #2d3748);
    border-color: var(--dark-4, #4a5568);
}

[data-theme="dark"] .obra-section-title,
[data-theme="dark"] .obra-info-value,
[data-theme="dark"] .etapa-title,
[data-theme="dark"] .equipe-nome,
[data-theme="dark"] .wizard-title,
[data-theme="dark"] .wizard-resumo-value,
[data-theme="dark"] .wizard-form-label,
[data-theme="dark"] .atividades-wizard-title,
[data-theme="dark"] .atividade-execution-title h4,
[data-theme="dark"] .atividade-section-header-small,
[data-theme="dark"] .registro-info-value,
[data-theme="dark"] .timeline-time {
    color: var(--white, #fff);
}

[data-theme="dark"] .obra-info-item,
[data-theme="dark"] .etapa-timeline-content,
[data-theme="dark"] .equipe-card,
[data-theme="dark"] .wizard-modal .modal-body,
[data-theme="dark"] .wizard-atividade-item,
[data-theme="dark"] .wizard-btn-adicionar,
[data-theme="dark"] .wizard-btn-secondary,
[data-theme="dark"] .atividade-execution-header,
[data-theme="dark"] .pausa-item {
    background: var(--dark-3, #3d4852);
}

[data-theme="dark"] .obra-info-label,
[data-theme="dark"] .obra-section-title i,
[data-theme="dark"] .etapa-meta,
[data-theme="dark"] .equipe-funcao,
[data-theme="dark"] .wizard-resumo-title,
[data-theme="dark"] .wizard-resumo-label,
[data-theme="dark"] .wizard-step-label,
[data-theme="dark"] .atividade-execution-title small,
[data-theme="dark"] .registro-info-label,
[data-theme="dark"] .timeline-action,
[data-theme="dark"] .observacao-text {
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
[data-theme="dark"] .wizard-step-line,
[data-theme="dark"] .timeline-execution::before {
    background: var(--dark-4, #4a5568);
}

[data-theme="dark"] .etapa-timeline-dot {
    background: var(--dark-4, #4a5568);
    border-color: var(--dark-2, #2d3748);
    box-shadow: 0 0 0 3px var(--dark-4, #4a5568);
}

[data-theme="dark"] .foto-count-badge {
    background: var(--dark-3, #3d4852);
    color: var(--dark-7, #a0aec0);
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
    .atividades-estatisticas { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .obra-view-container { padding: 16px; }
    .obra-view-header { padding: 24px; }
    .obra-view-title { font-size: 24px; }
    .obra-info-grid { grid-template-columns: 1fr; }
    .obra-info-item.full-width { grid-column: span 1; }
    .equipe-grid { grid-template-columns: repeat(2, 1fr); }
    .atividade-sections-grid { grid-template-columns: 1fr; }
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
            $status_label = 'Concluida';
            break;
        case 'paralisada':
            $status_header_class = 'paralisada';
            $status_label = 'Paralisada';
            break;
        case 'prospeccao':
            $status_header_class = 'prospeccao';
            $status_label = 'Prospeccao';
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

    <!-- Barra de Acoes -->
    <div class="obra-actions-quick">
        <button class="obra-action-btn btn-wizard" onclick="abrirWizard()">
            <i class="icon-magic"></i>
            Wizard: Nova Etapa + Atividades
        </button>

        <a href="<?php echo site_url('obras/equipe/' . $obra->id); ?>" class="obra-action-btn btn-equipe">
            <i class="icon-group"></i> Gerenciar Equipe
        </a>

        <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>" class="obra-action-btn btn-etapas">
            <i class="icon-tasks"></i> Gerenciar Etapas
        </a>

        <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>" class="obra-action-btn btn-atividades">
            <i class="icon-check"></i> Todas Atividades
        </a>

        <a href="<?php echo site_url('obras/editar/' . $obra->id); ?>" class="obra-action-btn btn-editar">
            <i class="icon-edit"></i> Editar Obra
        </a>

        <a href="<?php echo site_url('obras'); ?>" class="obra-action-btn btn-relatorio">
            <i class="icon-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Grid de Conteudo -->
    <div class="obra-content-grid">
        <!-- Coluna Principal -->
        <div class="obra-main-column">

            <!-- Informacoes da Obra -->
            <div class="obra-section-card">
                <div class="obra-section-header">
                    <div class="obra-section-title">
                        <i class="icon-info-sign"></i>
                        Informacoes Gerais
                    </div>
                </div>

                <div class="obra-info-grid">
                    <div class="obra-info-item">
                        <div class="obra-info-label">Codigo da Obra</div>
                        <div class="obra-info-value">
                            <i class="icon-barcode"></i>
                            <?php echo htmlspecialchars($obra->codigo ?? 'Nao definido'); ?>
                        </div>
                    </div>

                    <div class="obra-info-item">
                        <div class="obra-info-label">Tipo</div>
                        <div class="obra-info-value">
                            <i class="icon-cog"></i>
                            <?php echo htmlspecialchars($obra->tipo_obra ?? 'Nao definido'); ?>
                        </div>
                    </div>

                    <div class="obra-info-item full-width">
                        <div class="obra-info-label">Endereco</div>
                        <div class="obra-info-value">
                            <i class="icon-map-marker"></i>
                            <?php echo htmlspecialchars($obra->endereco ?? 'Nao informado'); ?>
                        </div>
                    </div>

                    <div class="obra-info-item">
                        <div class="obra-info-label">Data de Inicio</div>
                        <div class="obra-info-value">
                            <i class="icon-calendar"></i>
                            <?php echo $obra->data_inicio_contrato ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : 'Nao definido'; ?>
                        </div>
                    </div>

                    <div class="obra-info-item">
                        <div class="obra-info-label">Previsao de Termino</div>
                        <div class="obra-info-value">
                            <i class="icon-flag-checkered"></i>
                            <?php echo $obra->data_fim_prevista ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : 'Nao definido'; ?>
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
                    <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>" class="obra-section-action">
                        <i class="icon-eye-open"></i> Ver Todas
                    </a>
                </div>

                <?php if (!empty($etapas)): ?>
                <div class="etapas-timeline">
                    <?php foreach ($etapas as $etapa): ?>
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
                                    echo 'Inicio: ' . date('d/m/Y', strtotime($etapa->data_inicio_prevista));
                                    if ($etapa->data_fim_prevista) {
                                        echo ' | Termino: ' . date('d/m/Y', strtotime($etapa->data_fim_prevista));
                                    }
                                } else {
                                    echo 'Datas nao definidas';
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
                <div class="empty-state">
                    <i class="icon-tasks"></i>
                    <p>Nenhuma etapa cadastrada</p>
                    <button onclick="abrirWizard()" class="obra-section-action" style="margin-top: 16px;">
                        <i class="icon-plus"></i> Criar Primeira Etapa
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- ATIVIDADES DO SISTEMA (Wizard) -->
            <div class="atividades-wizard-section">
                <div class="atividades-wizard-header">
                    <div class="atividades-wizard-title">
                        <i class="icon-time"></i>
                        Registro de Atividades (Wizard)
                    </div>
                    <a href="<?php echo site_url('atividades/relatorio?obra_id=' . $obra->id); ?>" class="obra-section-action">
                        <i class="icon-file-alt"></i> Relatorio Completo
                    </a>
                </div>

                <!-- Estatisticas -->
                <?php if ($estatisticas_atividades): ?>
                <div class="atividades-estatisticas">
                    <div class="stat-card">
                        <div class="number"><?php echo $estatisticas_atividades['total_atividades'] ?? 0; ?></div>
                        <div class="label">Total</div>
                    </div>
                    <div class="stat-card success">
                        <div class="number"><?php echo $estatisticas_atividades['concluidas'] ?? 0; ?></div>
                        <div class="label">Concluidas</div>
                    </div>
                    <div class="stat-card warning">
                        <div class="number"><?php echo $estatisticas_atividades['em_andamento'] ?? 0; ?></div>
                        <div class="label">Em Andamento</div>
                    </div>
                    <div class="stat-card info">
                        <div class="number"><?php echo round(($estatisticas_atividades['tempo_total_minutos'] ?? 0) / 60, 1); ?>h</div>
                        <div class="label">Horas Trabalhadas</div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Lista de Atividades -->
                <?php if (!empty($atividades_sistema)): ?>
                <div class="atividades-execution-list">
                    <?php foreach ($atividades_sistema as $atv): ?>
                    <?php
                    // Determinar classe de status
                    $card_status_class = '';
                    $status_tag_class = '';
                    $status_tag_text = '';

                    if ($atv->status == 'finalizada' && $atv->concluida == 1) {
                        $card_status_class = 'status-concluida';
                        $status_tag_class = 'concluida';
                        $status_tag_text = 'Concluida';
                    } elseif ($atv->status == 'em_andamento') {
                        $card_status_class = 'status-em_andamento';
                        $status_tag_class = 'em_andamento';
                        $status_tag_text = 'Em Andamento';
                    } elseif ($atv->status == 'pausada') {
                        $card_status_class = 'status-pausada';
                        $status_tag_class = 'pausada';
                        $status_tag_text = 'Pausada';
                    } else {
                        $card_status_class = '';
                        $status_tag_class = 'finalizada';
                        $status_tag_text = 'Finalizada';
                    }

                    $cor_tipo = $atv->tipo_cor ?? '#667eea';
                    $icone_tipo = $atv->tipo_icone ?? 'bx-wrench';

                    // Carregar dados completos da atividade
                    $CI = &get_instance();
                    $CI->load->model('Atividades_model', 'ativ_model');
                    $atv_completa = $CI->ativ_model->getByIdCompleto($atv->idAtividade);
                    ?>
                    <div class="atividade-execution-card <?php echo $card_status_class; ?>">
                        <!-- Header da Atividade -->
                        <div class="atividade-execution-header">
                            <div class="atividade-execution-title">
                                <i class="bx <?php echo $icone_tipo; ?>" style="background: <?php echo $cor_tipo; ?>"></i>
                                <div>
                                    <h4><?php echo htmlspecialchars($atv->tipo_nome ?? 'Atividade'); ?></h4>
                                    <small>
                                        <i class="bx bx-user"></i> <?php echo htmlspecialchars($atv->nome_tecnico ?? 'Tecnico nao informado'); ?>
                                        | <i class="bx bx-calendar"></i> <?php echo date('d/m/Y', strtotime($atv->hora_inicio)); ?>
                                    </small>
                                </div>
                            </div>
                            <span class="atividade-status-tag <?php echo $status_tag_class; ?>">
                                <?php echo $status_tag_text; ?>
                            </span>
                        </div>

                        <!-- Grid de Secoes -->
                        <div class="atividade-sections-grid">
                            <!-- Secao: Registro (Hora Inicio/Fim) -->
                            <div class="atividade-section-box registro">
                                <div class="atividade-section-header-small">
                                    <i class="bx bx-time-five"></i>
                                    Registro de Tempo
                                </div>
                                <div class="registro-info-grid">
                                    <div class="registro-info-item">
                                        <span class="registro-info-label">Inicio</span>
                                        <span class="registro-info-value inicio">
                                            <?php echo date('H:i', strtotime($atv->hora_inicio)); ?>
                                        </span>
                                    </div>
                                    <div class="registro-info-item">
                                        <span class="registro-info-label">Termino</span>
                                        <span class="registro-info-value fim">
                                            <?php echo $atv->hora_fim ? date('H:i', strtotime($atv->hora_fim)) : '--:--'; ?>
                                        </span>
                                    </div>
                                    <div class="registro-info-item full-width">
                                        <span class="registro-info-label">Duracao</span>
                                        <span class="registro-info-value">
                                            <?php
                                            if ($atv->duracao_minutos) {
                                                $h = floor($atv->duracao_minutos / 60);
                                                $m = $atv->duracao_minutos % 60;
                                                echo sprintf('%02d:%02d', $h, $m);
                                                echo ' <small>(' . $atv->duracao_minutos . ' min)</small>';
                                            } else {
                                                echo '--:--';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Secao: Fotos -->
                            <div class="atividade-section-box fotos">
                                <div class="atividade-section-header-small">
                                    <i class="bx bx-camera"></i>
                                    Fotos
                                    <?php if (!empty($atv_completa->fotos)): ?>
                                    <span class="foto-count-badge">
                                        <i class="bx bx-image"></i> <?php echo count($atv_completa->fotos); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($atv_completa->fotos)): ?>
                                <div class="fotos-grid-small">
                                    <?php foreach (array_slice($atv_completa->fotos, 0, 4) as $foto): ?>
                                    <?php
                                    $url_foto = '';
                                    if (!empty($foto->caminho_arquivo)) {
                                        $url_foto = base_url('assets/atividades/fotos/' . $foto->caminho_arquivo);
                                    } elseif (!empty($foto->foto_base64)) {
                                        $url_foto = $foto->foto_base64;
                                    }
                                    ?>
                                    <?php if ($url_foto): ?>
                                    <img src="<?php echo $url_foto; ?>" class="foto-thumb-small" onclick="abrirFotoModal('<?php echo $url_foto; ?>')">
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p style="color: #888; font-size: 12px; margin: 0;">Nenhuma foto registrada</p>
                                <?php endif; ?>
                            </div>

                            <!-- Secao: Pausas -->
                            <div class="atividade-section-box pausas">
                                <div class="atividade-section-header-small">
                                    <i class="bx bx-pause-circle"></i>
                                    Pausas
                                </div>
                                <?php if (!empty($atv_completa->pausas)): ?>
                                <div class="pausas-list">
                                    <?php foreach ($atv_completa->pausas as $pausa): ?>
                                    <div class="pausa-item <?php echo empty($pausa->pausa_fim) ? 'em_andamento' : ''; ?>">
                                        <div class="pausa-motivo"><?php echo htmlspecialchars($pausa->motivo ?? 'Pausa'); ?></div>
                                        <div class="pausa-tempo">
                                            <?php echo date('H:i', strtotime($pausa->pausa_inicio)); ?>
                                            <?php if ($pausa->pausa_fim): ?>
                                            - <?php echo date('H:i', strtotime($pausa->pausa_fim)); ?>
                                            <?php else: ?>
                                            (em andamento)
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($pausa->observacao): ?>
                                        <div class="pausa-observacao"><?php echo htmlspecialchars($pausa->observacao); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p style="color: #888; font-size: 12px; margin: 0;">Nenhuma pausa registrada</p>
                                <?php endif; ?>
                            </div>

                            <!-- Secao: Finalizacao -->
                            <div class="atividade-section-box finalizacao">
                                <div class="atividade-section-header-small">
                                    <i class="bx bx-check-circle"></i>
                                    Finalizacao
                                </div>
                                <div class="finalizacao-info">
                                    <?php if ($atv->hora_fim): ?>
                                    <p><i class="bx bx-calendar-check"></i> Finalizada em <?php echo date('d/m/Y H:i', strtotime($atv->hora_fim)); ?></p>
                                    <?php if ($atv->concluida): ?>
                                    <p><i class="bx bx-check-shield"></i> Atividade concluida com sucesso</p>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <p style="color: #888;"><i class="bx bx-hourglass"></i> Aguardando finalizacao</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Secao: Materiais (se existir) -->
                            <?php if (!empty($atv_completa->materiais)): ?>
                            <div class="atividade-section-box materiais">
                                <div class="atividade-section-header-small">
                                    <i class="bx bx-package"></i>
                                    Materiais
                                </div>
                                <div class="materiais-list">
                                    <?php foreach ($atv_completa->materiais as $mat): ?>
                                    <span class="material-badge">
                                        <?php echo $mat->quantidade; ?> <?php echo $mat->unidade; ?> - <?php echo htmlspecialchars($mat->produto_descricao ?? $mat->nome_produto ?? 'Material'); ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Secao: Observacoes (se existir) -->
                            <?php if (!empty($atv->observacoes) || !empty($atv->problemas_encontrados) || !empty($atv->solucao_aplicada)): ?>
                            <div class="atividade-section-box observacoes">
                                <div class="atividade-section-header-small">
                                    <i class="bx bx-note"></i>
                                    Observacoes
                                </div>
                                <div class="observacao-content">
                                    <?php if (!empty($atv->observacoes)): ?>
                                    <p class="observacao-text"><?php echo nl2br(htmlspecialchars($atv->observacoes)); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($atv->problemas_encontrados)): ?>
                                    <p class="observacao-text problema"><i class="bx bx-error"></i> <?php echo htmlspecialchars($atv->problemas_encontrados); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($atv->solucao_aplicada)): ?>
                                    <p class="observacao-text solucao"><i class="bx bx-check"></i> <?php echo htmlspecialchars($atv->solucao_aplicada); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($atividades_sistema) >= 20): ?>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="<?php echo site_url('atividades/relatorio?obra_id=' . $obra->id); ?>" class="obra-section-action">
                        <i class="icon-eye-open"></i> Ver Todas as Atividades
                    </a>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-state">
                    <i class="icon-clipboard"></i>
                    <h4>Nenhuma atividade registrada</h4>
                    <p>As atividades executadas aparecerao aqui automaticamente.</p>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="obra-sidebar-column">

            <!-- Atividades Planejadas (obra_atividades) -->
            <div class="obra-section-card">
                <div class="obra-section-header">
                    <div class="obra-section-title">
                        <i class="icon-calendar"></i>
                        Atividades Planejadas
                    </div>
                    <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>" class="obra-section-action">
                        Ver Todas
                    </a>
                </div>

                <?php if (!empty($atividades_recentes)): ?>
                <div class="atividades-lista-rapida">
                    <?php foreach (array_slice($atividades_recentes, 0, 5) as $atividade): ?>
                    <?php
                    $ativ_class = ($atividade->status == 'concluida') ? 'concluida' : 'agendada';
                    ?>
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
                <?php else: ?>
                <div class="empty-state" style="padding: 30px;">
                    <p>Nenhuma atividade planejada</p>
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

                <?php if (!empty($equipe)): ?>
                <div class="equipe-grid">
                    <?php foreach (array_slice($equipe, 0, 6) as $membro): ?>
                    <div class="equipe-card">
                        <div class="equipe-avatar">
                            <i class="icon-user"></i>
                        </div>
                        <div class="equipe-nome"><?php echo htmlspecialchars($membro->nome ?? $membro->tecnico_nome ?? 'Sem nome'); ?></div>
                        <div class="equipe-funcao"><?php echo htmlspecialchars($membro->funcao ?? 'Tecnico'); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state" style="padding: 30px;">
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

<!-- Modal para visualizar foto -->
<div id="modalFoto" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Foto da Atividade</h3>
    </div>
    <div class="modal-body" style="text-align: center;">
        <img id="imgFotoModal" src="" style="max-width: 100%; max-height: 70vh; border-radius: 8px;">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Fechar</button>
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
                    <span class="wizard-step-label">Revisao</span>
                </div>
            </div>

            <!-- Passo 1: Informacoes da Etapa -->
            <div id="wizardStep1" class="wizard-content active">
                <div class="wizard-title">
                    <i class="icon-tasks"></i>
                    Informacoes da Etapa
                </div>

                <div class="wizard-form-row">
                    <div class="wizard-form-group">
                        <label class="wizard-form-label">
                            Numero da Etapa <span class="required">*</span>
                        </label>
                        <input type="number" name="etapa_numero" class="wizard-form-input"
                               value="<?php echo (count($etapas ?? []) + 1); ?>" min="1" required>
                    </div>

                    <div class="wizard-form-group">
                        <label class="wizard-form-label">
                            Nome da Etapa <span class="required">*</span>
                        </label>
                        <input type="text" name="etapa_nome" class="wizard-form-input"
                               placeholder="Ex: Fundacao, Estrutura, Acabamento..." required>
                    </div>
                </div>

                <div class="wizard-form-group">
                    <label class="wizard-form-label">Descricao</label>
                    <textarea name="etapa_descricao" class="wizard-form-textarea"
                              placeholder="Descreva o que sera feito nesta etapa..."></textarea>
                </div>

                <div class="wizard-form-row">
                    <div class="wizard-form-group">
                        <label class="wizard-form-label">Data de Inicio Prevista</label>
                        <input type="date" name="etapa_data_inicio" class="wizard-form-input">
                    </div>

                    <div class="wizard-form-group">
                        <label class="wizard-form-label">Data de Termino Prevista</label>
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
                    Adicione as atividades que serao realizadas nesta etapa. Voce pode adicionar quantas quiser.
                </p>

                <div id="wizardAtividadesLista" class="wizard-atividades-lista">
                    <!-- Atividades serao adicionadas aqui -->
                </div>

                <button type="button" class="wizard-btn-adicionar" onclick="adicionarAtividadeWizard()">
                    <i class="icon-plus"></i> Adicionar Atividade
                </button>
            </div>

            <!-- Passo 3: Revisao -->
            <div id="wizardStep3" class="wizard-content">
                <div class="wizard-title">
                    <i class="icon-check"></i>
                    Revisao e Confirmacao
                </div>

                <div class="wizard-resumo">
                    <div class="wizard-resumo-section">
                        <div class="wizard-resumo-title">Etapa</div>

                        <div class="wizard-resumo-item">
                            <span class="wizard-resumo-label">Numero:</span>
                            <span id="resumoEtapaNumero" class="wizard-resumo-value"></span>
                        </div>

                        <div class="wizard-resumo-item">
                            <span class="wizard-resumo-label">Nome:</span>
                            <span id="resumoEtapaNome" class="wizard-resumo-value"></span>
                        </div>

                        <div class="wizard-resumo-item">
                            <span class="wizard-resumo-label">Descricao:</span>
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
                    Proximo <i class="icon-arrow-right"></i>
                </button>

                <button type="submit" id="wizardBtnSalvar" class="wizard-btn wizard-btn-success" style="display: none;">
                    <i class="icon-save"></i> Salvar Etapa e Atividades
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Funcao para abrir modal de foto
function abrirFotoModal(url) {
    document.getElementById('imgFotoModal').src = url;
    $('#modalFoto').modal('show');
}

// Variaveis do wizard
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

// Navegacao
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

// Validacao
function validarStepAtual() {
    if (wizardStepAtual === 1) {
        const numero = document.querySelector('[name="etapa_numero"]').value;
        const nome = document.querySelector('[name="etapa_nome"]').value;

        if (!numero || !nome) {
            alert('Preencha o numero e o nome da etapa.');
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

    // Mostrar/esconder conteudos
    document.querySelectorAll('.wizard-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById('wizardStep' + wizardStepAtual).classList.add('active');

    // Atualizar botoes
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
                   placeholder="Titulo da atividade" required>
            <select name="atividades[${index}][tipo]" class="wizard-atividade-input" style="width: 120px;">
                <option value="trabalho">Trabalho</option>
                <option value="visita">Visita</option>
                <option value="manutencao">Manutencao</option>
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
        const titulo = item.querySelector('input').value || '(sem titulo)';
        const tipo = item.querySelector('select').value;
        html += `
            <div class="wizard-resumo-item" style="padding-left: 16px;">
                <span>${index + 1}. ${titulo} <span style="color: #888;">(${tipo})</span></span>
            </div>
        `;
    });
    document.getElementById('resumoAtividadesLista').innerHTML = html;
}

// Animacao de entrada dos cards
$(document).ready(function() {
    $('.obra-section-card, .atividades-wizard-section').each(function(index) {
        $(this).hide().delay(index * 150).fadeIn(500);
    });
});
</script>
