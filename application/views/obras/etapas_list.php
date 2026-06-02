<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Página de Etapas - Design Moderno -->
<style>
/* ============================================
   PÁGINA DE ETAPAS - NOVO DESIGN
   ============================================ */

.etapas-container {
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

.obra-title i { font-size: 32px; }

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

.obra-header-status { text-align: right; }

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

/* Grid Principal */
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
    margin-bottom: 24px;
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
    border: none;
    cursor: pointer;
}

.card-action:hover {
    background: #5568d3;
    transform: translateY(-2px);
}

.card-action i { font-size: 14px; }

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

.info-item.full-width { grid-column: span 2; }

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

.info-value i { color: #667eea; }

/* Etapas - Estilo Timeline */
.etapas-timeline-container {
    position: relative;
}

.etapas-timeline {
    position: relative;
    padding-left: 40px;
}

.etapas-timeline::before {
    content: '';
    position: absolute;
    left: 14px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #667eea, #764ba2);
    border-radius: 3px;
}

.etapa-item {
    position: relative;
    margin-bottom: 24px;
}

.etapa-item:last-child { margin-bottom: 0; }

.etapa-dot {
    position: absolute;
    left: -36px;
    top: 20px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    z-index: 1;
    transition: transform 0.3s;
}

.etapa-item:hover .etapa-dot { transform: scale(1.2); }

.etapa-dot.pendente { background: #95a5a6; }
.etapa-dot.andamento {
    background: #4facfe;
    animation: pulse 2s infinite;
}
.etapa-dot.concluida { background: #11998e; }
.etapa-dot.atrasada { background: #e74c3c; }

@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(79, 172, 254, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(79, 172, 254, 0); }
}

.etapa-card {
    background: #f8f9fa;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e8e8e8;
    transition: all 0.3s;
}

.etapa-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.etapa-header-card {
    padding: 20px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    border-left: 5px solid transparent;
}

.etapa-header-card:hover { background: #f0f0f0; }

.etapa-header-card.pendente { border-left-color: #95a5a6; }
.etapa-header-card.andamento { border-left-color: #4facfe; background: #f0f8ff; }
.etapa-header-card.concluida { border-left-color: #11998e; background: #f0fff7; }
.etapa-header-card.atrasada { border-left-color: #e74c3c; background: #fff5f5; }

.etapa-main-info {
    display: flex;
    align-items: center;
    gap: 16px;
    flex: 1;
}

.etapa-number {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #667eea;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
    flex-shrink: 0;
}

.etapa-number.pendente { background: #95a5a6; }
.etapa-number.andamento { background: #4facfe; }
.etapa-number.concluida { background: #11998e; }
.etapa-number.atrasada { background: #e74c3c; }

.etapa-info { flex: 1; }

.etapa-name {
    font-weight: 700;
    font-size: 16px;
    color: #333;
    margin-bottom: 4px;
}

.etapa-meta-text {
    font-size: 13px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 12px;
}

.etapa-progress-section {
    width: 150px;
    text-align: center;
}

.etapa-progress-bar {
    height: 8px;
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

.etapa-progress-text {
    font-size: 14px;
    font-weight: 700;
    color: #667eea;
}

.etapa-status {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.etapa-status.pendente {
    background: rgba(149, 165, 166, 0.1);
    color: #95a5a6;
}

.etapa-status.andamento {
    background: rgba(79, 172, 254, 0.1);
    color: #4facfe;
}

.etapa-status.concluida {
    background: rgba(17, 153, 142, 0.1);
    color: #11998e;
}

.etapa-status.atrasada {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.etapa-toggle {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    flex-shrink: 0;
}

.etapa-toggle.expanded {
    transform: rotate(180deg);
    background: #667eea;
}

.etapa-toggle.expanded i { color: white; }

.etapa-toggle i {
    color: #667eea;
    font-size: 14px;
}

.etapa-actions {
    display: flex;
    gap: 8px;
}

.etapa-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.etapa-btn:hover { transform: translateY(-2px); }

.etapa-btn-edit {
    background: #e3f2fd;
    color: #1976d2;
}

.etapa-btn-edit:hover {
    background: #1976d2;
    color: white;
}

.etapa-btn-delete {
    background: #ffebee;
    color: #c62828;
}

.etapa-btn-delete:hover {
    background: #c62828;
    color: white;
}

/* Sub-lista de Atividades */
.etapa-atividades {
    display: none;
    background: white;
    border-top: 1px solid #e8e8e8;
}

.etapa-atividades.expanded { display: block; }

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
    padding: 16px 20px;
}

.atividade-subitem {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 8px;
    transition: all 0.2s;
}

.atividade-subitem:hover {
    background: #f0f0f0;
    transform: translateX(4px);
}

.atividade-subitem:last-child { margin-bottom: 0; }

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

.atividade-progress-mini {
    text-align: right;
}

.atividade-progress-mini-bar {
    width: 60px;
    height: 4px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 4px;
}

.atividade-progress-mini-fill {
    height: 100%;
    background: #667eea;
    border-radius: 10px;
}

.atividade-progress-mini-text {
    font-size: 11px;
    font-weight: 600;
    color: #667eea;
}

/* Equipe */
.equipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
}

.equipe-item {
    text-align: center;
    padding: 14px;
    background: #f8f9fa;
    border-radius: 12px;
    transition: all 0.2s;
}

.equipe-item:hover {
    background: #f0f0f0;
    transform: translateY(-3px);
}

.equipe-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    color: white;
    font-size: 18px;
}

.equipe-name {
    font-weight: 600;
    font-size: 13px;
    color: #333;
    margin-bottom: 4px;
}

.equipe-role {
    font-size: 11px;
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

.acao-etapa { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.acao-visualizar { background: #667eea; }
.acao-voltar { background: #95a5a6; }

/* Estatísticas Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.stat-item {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 12px;
    text-align: center;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 12px;
    color: #888;
    text-transform: uppercase;
    font-weight: 600;
}

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

/* Modal Moderno */
.modal-etapas .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 24px;
    border: none;
    border-radius: 4px 4px 0 0;
}

.modal-etapas .modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.modal-etapas .modal-header .close {
    color: white;
    opacity: 0.9;
    font-size: 24px;
}

.modal-etapas .modal-body {
    padding: 24px;
    max-height: 500px;
    overflow-y: auto;
}

.modal-etapas .modal-footer {
    padding: 16px 24px;
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
}

.form-group { margin-bottom: 16px; }

.form-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
    font-size: 13px;
}

.form-label .required { color: #e74c3c; }

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    color: #333;
    background: #fff;
    font-size: 14px;
    color: #333;
    background: white;
    transition: all 0.2s;
    box-sizing: border-box;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 80px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 10px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-cancel {
    background: #e0e0e0;
    color: #666;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover { background: #d0d0d0; }

/* Responsividade */
@media (max-width: 768px) {
    .etapas-container { padding: 16px; }
    .obra-title { font-size: 22px; }
    .obra-title i { font-size: 26px; }
    .prazo-grid { grid-template-columns: 1fr; }
    .prazo-dias { grid-column: span 1; }
    .info-grid { grid-template-columns: 1fr; }
    .info-item.full-width { grid-column: span 1; }
    .etapas-timeline { padding-left: 30px; }
    .etapas-timeline::before { left: 10px; }
    .etapa-dot { left: -30px; }
    .etapa-progress-section { display: none; }
    .etapa-actions { flex-direction: column; }
    .equipe-grid { grid-template-columns: repeat(2, 1fr); }
    .acoes-bar { justify-content: center; }
    .form-row { grid-template-columns: 1fr; }
}

/* Dark Mode Support */
[data-theme="dark"] .card,
[data-theme="dark"] .prazo-item,
[data-theme="dark"] .info-item,
[data-theme="dark"] .etapa-card,
[data-theme="dark"] .atividade-subitem,
[data-theme="dark"] .equipe-item,
[data-theme="dark"] .stat-item {
    background: #3d4852;
}

[data-theme="dark"] .card-title,
[data-theme="dark"] .obra-title,
[data-theme="dark"] .info-value,
[data-theme="dark"] .prazo-value,
[data-theme="dark"] .etapa-name,
[data-theme="dark"] .atividade-title,
[data-theme="dark"] .equipe-name,
[data-theme="dark"] .stat-value {
    color: #fff;
}

[data-theme="dark"] .card,
[data-theme="dark"] .etapa-card,
[data-theme="dark"] .atividade-subitem {
    border-color: #4a5568;
}

[data-theme="dark"] .info-label,
[data-theme="dark"] .prazo-label,
[data-theme="dark"] .etapa-meta-text,
[data-theme="dark"] .atividade-meta,
[data-theme="dark"] .equipe-role,
[data-theme="dark"] .stat-label {
    color: #a0aec0;
}

[data-theme="dark"] .etapa-atividades {
    background: #2d3748;
    border-color: #4a5568;
}

[data-theme="dark"] .etapa-header-card.andamento { background: #2d3748; }
[data-theme="dark"] .etapa-header-card.concluida { background: #2d3748; }
[data-theme="dark"] .etapa-header-card.atrasada { background: #2d3748; }
</style>

<div class="etapas-container">
    <?php
    // Definir classe e label do header baseado na configuração
    $status_atual_norm = strtolower(preg_replace('/[^a-z]/', '', $obra->status ?? ''));
    $status_class = '';
    $status_label = '';
    $status_cor = '#667eea';
    foreach ($status_obra as $s) {
        $s_norm = strtolower(preg_replace('/[^a-z]/', '', $s->nome));
        if ($status_atual_norm === $s_norm) {
            $status_class = 'status-dinamico';
            $status_label = $s->nome;
            $status_cor = $s->cor ?? '#667eea';
            break;
        }
    }
    if (!$status_label) {
        switch ($obra->status) {
            case 'EmExecucao':
            case 'Em Andamento':
            case 'em-andamento':
                $status_class = 'em-andamento';
                $status_label = 'Em Andamento';
                $status_cor = '#4facfe';
                break;
            case 'Concluida':
            case 'concluida':
                $status_class = 'concluida';
                $status_label = 'Concluída';
                $status_cor = '#11998e';
                break;
            case 'Paralisada':
            case 'paralisada':
                $status_class = 'paralisada';
                $status_label = 'Paralisada';
                $status_cor = '#f093fb';
                break;
            default:
                $status_class = '';
                $status_label = ucfirst($obra->status);
        }
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

    // Calcular estatísticas das etapas
    $total_etapas = count($etapas);
    $etapas_concluidas = count(array_filter($etapas, function($e) { return $e->status == 'Concluida'; }));
    $etapas_andamento = count(array_filter($etapas, function($e) { return $e->status == 'EmAndamento'; }));
    $etapas_pendentes = $total_etapas - $etapas_concluidas - $etapas_andamento;
    ?>

    <!-- Header da Obra -->
    <div class="obra-header <?php echo $status_class; ?>" style="background: linear-gradient(135deg, <?php echo $status_cor; ?> 0%, <?php echo $status_cor; ?> 100%);">
        <div class="obra-header-row">
            <div class="obra-header-info">
                <div class="obra-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><i class="icon-arrow-left"></i> Obras</a>
                    <span>/</span>
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo htmlspecialchars($obra->nome); ?></a>
                    <span>/</span>
                    <span>Etapas</span>
                </div>
                <h1 class="obra-title">
                    <i class="icon-tasks"></i>
                    Gerenciar Etapas
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
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
        <button class="acao-btn acao-etapa" onclick="abrirModalEtapa()">
            <i class="icon-plus"></i> Nova Etapa
        </button>
        <?php endif; ?>
        <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="acao-btn acao-visualizar">
            <i class="icon-eye-open"></i> Ver Obra
        </a>
        <a href="<?php echo site_url('obras'); ?>" class="acao-btn acao-voltar">
            <i class="icon-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Grid Principal -->
    <div class="obra-grid">
        <!-- Coluna Esquerda - Prazo e Etapas -->
        <div class="obra-coluna">
            <!-- Card de Prazo e Dados -->
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
                            <?php echo htmlspecialchars($obra->tipo_obra ?? 'N/A'); ?>
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
                </div>
            </div>

            <!-- Card de Etapas -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-road"></i>
                        Timeline de Etapas
                    </div>
                    <div style="font-size: 13px; color: #888;">
                        <?php echo $total_etapas; ?> etapa(s)
                    </div>
                </div>

                <div class="etapas-timeline-container">
                    <?php if (!empty($etapas)): ?>
                    <div class="etapas-timeline">
                        <?php foreach ($etapas as $etapa):
                            $etapa_status = $etapa->status ?? 'Não Iniciada';
                            $etapa_status_norm = strtolower(preg_replace('/[^a-z]/', '', $etapa_status));
                            $etapa_class = '';
                            $status_text = '';

                            // Buscar configuração dinâmica
                            $status_encontrado = false;
                            foreach ($status_obra as $s) {
                                $s_norm = strtolower(preg_replace('/[^a-z]/', '', $s->nome));
                                if ($etapa_status_norm === $s_norm) {
                                    $etapa_class = $s_norm;
                                    $status_text = $s->nome;
                                    $status_encontrado = true;
                                    break;
                                }
                            }
                            if (!$status_encontrado) {
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
                            }

                            $etapa_atividades = $atividades_por_etapa[$etapa->id] ?? [];
                            $tem_atividades = !empty($etapa_atividades);
                            $progresso_etapa = $etapa->percentual_concluido ?? 0;
                        ?>
                        <div class="etapa-item">
                            <div class="etapa-dot <?php echo $etapa_class; ?>"></div>

                            <div class="etapa-card">
                                <div class="etapa-header-card <?php echo $etapa_class; ?>" onclick="toggleEtapa(<?php echo $etapa->id; ?>)">
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
                                                <span><i class="icon-tasks"></i> <?php echo $etapa->total_atividades; ?> ativ.</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="etapa-progress-section">
                                        <div class="etapa-progress-bar">
                                            <div class="etapa-progress-fill <?php echo $etapa_class; ?>" style="width: <?php echo $progresso_etapa; ?>%; background: <?php
                                                if ($progresso_etapa >= 100) echo '#11998e';
                                                elseif ($progresso_etapa >= 50) echo '#4facfe';
                                                elseif ($progresso_etapa > 0) echo '#f39c12';
                                                else echo '#95a5a6';
                                            ?>"></div>
                                        </div>
                                        <div class="etapa-progress-text"><?php echo $progresso_etapa; ?>%</div>
                                    </div>

                                    <span class="etapa-status <?php echo $etapa_class; ?>"><?php echo $status_text; ?></span>

                                    <div class="etapa-actions">
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                                        <button type="button" class="etapa-btn etapa-btn-edit" onclick="event.stopPropagation(); editarEtapa(<?php echo $etapa->id; ?>, '<?php echo htmlspecialchars($etapa->nome, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($etapa->descricao ?? '', ENT_QUOTES); ?>', '<?php echo $etapa->numero_etapa; ?>', '<?php echo $etapa->especialidade ?? ''; ?>', '<?php echo $etapa->data_inicio_prevista ?? ''; ?>', '<?php echo $etapa->data_fim_prevista ?? ''; ?>', '<?php echo $etapa->status; ?>', '<?php echo $progresso_etapa; ?>')" title="Editar">
                                            <i class="icon-edit"></i>
                                        </button>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                                        <a href="<?php echo site_url('obras/excluirEtapa/' . $etapa->id); ?>" class="etapa-btn etapa-btn-delete" onclick="event.stopPropagation(); return confirm('Tem certeza que deseja excluir esta etapa?');" title="Excluir">
                                            <i class="icon-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($tem_atividades): ?>
                                        <div class="etapa-toggle" id="toggle-<?php echo $etapa->id; ?>" onclick="event.stopPropagation(); toggleEtapa(<?php echo $etapa->id; ?>)">
                                            <i class="icon-chevron-down"></i>
                                        </div>
                                        <?php endif; ?>
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
                                        <div class="atividade-subitem">
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
                                                </div>
                                            </div>
                                            <div class="atividade-progress-mini">
                                                <div class="atividade-progress-mini-bar">
                                                    <div class="atividade-progress-mini-fill" style="width: <?php echo $atividade->percentual_concluido ?? 0; ?>%"></div>
                                                </div>
                                                <div class="atividade-progress-mini-text"><?php echo $atividade->percentual_concluido ?? 0; ?>%</div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="icon-tasks"></i>
                        <h4>Nenhuma etapa cadastrada</h4>
                        <p>Adicione etapas para organizar o progresso da obra.</p>
                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                        <button onclick="abrirModalEtapa()" class="card-action" style="margin-top: 16px;">
                            <i class="icon-plus"></i> Adicionar Primeira Etapa
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Coluna Direita - Sidebar -->
        <div class="obra-coluna">
            <!-- Estatísticas das Etapas -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-bar-chart"></i>
                        Estatísticas
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $total_etapas; ?></div>
                        <div class="stat-label">Total de Etapas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #11998e;"><?php echo $etapas_concluidas; ?></div>
                        <div class="stat-label">Concluídas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #4facfe;"><?php echo $etapas_andamento; ?></div>
                        <div class="stat-label">Em Andamento</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #95a5a6;"><?php echo $etapas_pendentes; ?></div>
                        <div class="stat-label">Pendentes</div>
                    </div>
                </div>
            </div>

            <!-- Equipe -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-group"></i>
                        Equipe
                    </div>
                </div>

                <?php if (!empty($equipe)): ?>
                <div class="equipe-grid">
                    <?php foreach (array_slice($equipe, 0, 6) as $membro): ?>
                    <div class="equipe-item">
                        <div class="equipe-avatar">
                            <i class="icon-user"></i>
                        </div>
                        <div class="equipe-name"><?php echo htmlspecialchars($membro->nome ?? $membro->tecnico_nome ?? 'Sem nome'); ?></div>
                        <div class="equipe-role"><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($equipe) > 6): ?>
                    <div class="equipe-item" style="opacity: 0.6;">
                        <div class="equipe-avatar" style="background: #95a5a6;">
                            <i class="icon-plus"></i>
                        </div>
                        <div class="equipe-name">+<?php echo count($equipe) - 6; ?></div>
                        <div class="equipe-role">membros</div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="empty-state" style="padding: 20px;">
                    <p>Sem equipe alocada</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Atividades Recentes -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-check"></i>
                        Atividades Recentes
                    </div>
                </div>

                <?php if (!empty($atividades_recentes)): ?>
                <div>
                    <?php foreach (array_slice($atividades_recentes, 0, 5) as $atividade): ?>
                    <div style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(102, 126, 234, 0.1); color: #667eea; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="icon-tasks"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 13px; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($atividade->titulo ?? 'Atividade #' . $atividade->id); ?></div>
                            <div style="font-size: 11px; color: #888;">
                                <i class="icon-calendar"></i> <?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state" style="padding: 20px;">
                    <p>Sem atividades recentes</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Estatísticas de Atividades do Sistema -->
            <?php if (!empty($estatisticas_atividades)): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="icon-time"></i>
                        Registro de Horas
                    </div>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $estatisticas_atividades['total_atividades'] ?? 0; ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #11998e;"><?php echo round(($estatisticas_atividades['tempo_total_minutos'] ?? 0) / 60, 1); ?>h</div>
                        <div class="stat-label">Horas</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Adicionar/Editar Etapa -->
<div id="modalEtapa" class="modal hide fade modal-etapas" tabindex="-1" role="dialog" aria-labelledby="modalEtapaLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="modalEtapaLabel"><i class="icon-plus-sign"></i> <span id="modalEtapaTitle">Nova Etapa</span></h3>
    </div>

    <form id="formEtapa" action="<?php echo site_url('obras/adicionarEtapa'); ?>" method="post">
        <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="numero_etapa">Número <span class="required">*</span></label>
                    <input type="number" name="numero_etapa" id="numero_etapa" class="form-input" value="<?php echo $total_etapas + 1; ?>" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="etapa_status">Status</label>
                    <select name="status" id="etapa_status" class="form-select">
                        <?php foreach ($status_obra as $s): ?>
                            <option value="<?php echo htmlspecialchars($s->nome); ?>"><?php echo htmlspecialchars($s->nome); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="nome">Nome da Etapa <span class="required">*</span></label>
                <input type="text" name="nome" id="nome" class="form-input" maxlength="100" placeholder="Ex: Fundação, Estrutura, Acabamento..." required>
            </div>

            <div class="form-group">
                <label class="form-label" for="especialidade">Especialidade</label>
                <select name="especialidade" id="especialidade" class="form-select">
                    <option value="">Selecione...</option>
                    <?php foreach ($especialidades as $esp): ?>
                        <option value="<?php echo htmlspecialchars($esp->nome); ?>"><?php echo htmlspecialchars($esp->nome); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="descricao">Descrição</label>
                <textarea name="descricao" id="descricao" class="form-textarea" rows="3" placeholder="Descreva os detalhes desta etapa..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="data_inicio_prevista">Data Início Prevista</label>
                    <input type="date" name="data_inicio_prevista" id="data_inicio_prevista" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label" for="data_fim_prevista">Data Término Prevista</label>
                    <input type="date" name="data_fim_prevista" id="data_fim_prevista" class="form-input">
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn-submit">Salvar Etapa</button>
        </div>
    </form>
</div>

<script>
// Toggle de etapas
function toggleEtapa(etapaId) {
    const atividadesDiv = document.getElementById('atividades-' + etapaId);
    const toggleBtn = document.getElementById('toggle-' + etapaId);

    if (atividadesDiv) {
        atividadesDiv.classList.toggle('expanded');
        if (toggleBtn) toggleBtn.classList.toggle('expanded');
    }
}

// Abrir modal para nova etapa
function abrirModalEtapa() {
    document.getElementById('formEtapa').action = '<?php echo site_url('obras/adicionarEtapa'); ?>';
    document.getElementById('modalEtapaTitle').textContent = 'Nova Etapa';
    document.getElementById('numero_etapa').value = '<?php echo $total_etapas + 1; ?>';
    document.getElementById('nome').value = '';
    document.getElementById('especialidade').value = '';
    document.getElementById('descricao').value = '';
    document.getElementById('data_inicio_prevista').value = '';
    document.getElementById('data_fim_prevista').value = '';
    document.getElementById('etapa_status').value = 'NaoIniciada';
    $('#modalEtapa').modal('show');
}

// Abrir modal para editar etapa
function editarEtapa(id, nome, descricao, numero, especialidade, dataInicio, dataFim, status, percentual) {
    document.getElementById('formEtapa').action = '<?php echo site_url('obras/editarEtapa/'); ?>' + id;
    document.getElementById('modalEtapaTitle').textContent = 'Editar Etapa';
    document.getElementById('numero_etapa').value = numero;
    document.getElementById('nome').value = nome;
    document.getElementById('especialidade').value = especialidade || '';
    document.getElementById('descricao').value = descricao || '';
    document.getElementById('data_inicio_prevista').value = dataInicio || '';
    document.getElementById('data_fim_prevista').value = dataFim || '';
    document.getElementById('etapa_status').value = status || 'NaoIniciada';
    $('#modalEtapa').modal('show');
}

// Animação de entrada
$(document).ready(function() {
    $('.card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });

    $('.etapa-item').each(function(index) {
        $(this).hide().delay(index * 150).fadeIn(500);
    });
});
</script>

