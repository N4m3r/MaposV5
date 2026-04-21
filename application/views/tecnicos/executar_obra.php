<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Tema Moderno Obras - Mesmo padrão de minhas_obras -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
/* Container Principal */
.obras-container { padding: 15px; max-width: 100%; }

/* Header Mobile-First */
.obra-detalhe-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 25px 20px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
    position: relative;
}
.obra-detalhe-header h1 {
    margin: 0 0 8px 0;
    font-size: 22px;
    font-weight: 700;
}
.obra-detalhe-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}
.btn-voltar {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 12px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
}
.btn-voltar:hover {
    background: rgba(255,255,255,0.3);
    color: white;
    text-decoration: none;
}

/* Atividade em Andamento Alert */
.atividade-andamento-alert {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    color: white;
    box-shadow: 0 5px 20px rgba(243, 156, 18, 0.3);
}
.atividade-andamento-alert h3 {
    margin: 0 0 10px 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.atividade-andamento-alert p {
    margin: 0 0 15px 0;
    opacity: 0.95;
}
.btn-continuar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: white;
    color: #f39c12;
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-continuar:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Cards de Estatísticas */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
@media (min-width: 768px) {
    .stats-grid { grid-template-columns: repeat(4, 1fr); }
}
.stat-card {
    background: white;
    border-radius: 15px;
    padding: 20px 15px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-top: 4px solid #11998e;
}
.stat-card.warning { border-top-color: #f39c12; }
.stat-card.info { border-top-color: #3498db; }
.stat-card.danger { border-top-color: #e74c3c; }
.stat-numero {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}
.stat-label {
    font-size: 11px;
    color: #888;
    text-transform: uppercase;
    font-weight: 600;
}

/* Botão Iniciar Atendimento Principal */
.btn-iniciar-principal {
    display: block;
    width: 100%;
    padding: 20px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    border-radius: 15px;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.3);
}
.btn-iniciar-principal:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.4);
}
.btn-iniciar-principal i {
    font-size: 24px;
}

/* Cards de Etapas */
.etapas-section {
    margin-bottom: 20px;
}
.section-title {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.etapas-lista {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.etapa-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s;
}
.etapa-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.etapa-header-card {
    padding: 20px;
    position: relative;
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}
.etapa-header-card.pendente {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
}
.etapa-header-card.em-andamento {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}
.etapa-header-card.concluida {
    background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
}
.etapa-status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.25);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
}
.etapa-numero {
    font-size: 12px;
    opacity: 0.9;
    margin-bottom: 5px;
    color: white;
}
.etapa-nome {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 8px 0;
    line-height: 1.3;
    color: white;
}
.etapa-progresso {
    font-size: 13px;
    opacity: 0.9;
    color: white;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Corpo do Card */
.etapa-body {
    padding: 20px;
}
.etapa-barra-progresso {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 15px;
}
.etapa-barra-preenchida {
    height: 100%;
    background: linear-gradient(90deg, #11998e, #38ef7d);
    border-radius: 4px;
    transition: width 0.5s ease;
}
.etapa-descricao {
    color: #666;
    font-size: 13px;
    margin-bottom: 15px;
    line-height: 1.5;
}

/* Atividades da Etapa */
.etapa-atividades {
    border-top: 1px solid #f0f0f0;
    padding-top: 15px;
}
.etapa-atividades h4 {
    font-size: 13px;
    color: #888;
    margin-bottom: 10px;
    text-transform: uppercase;
}
.etapa-atividade-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
    border-bottom: 1px solid #f5f5f5;
    font-size: 13px;
    color: #555;
}
.etapa-atividade-item:last-child {
    border-bottom: none;
}
.etapa-atividade-item i {
    color: #27ae60;
}
.etapa-atividade-item.pendente i {
    color: #f39c12;
}

/* Ações do Card */
.etapa-actions {
    padding: 0 20px 20px;
    display: flex;
    gap: 10px;
}
.btn-etapa-acao {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all 0.3s;
}
.btn-etapa-acao.iniciar {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}
.btn-etapa-acao.iniciar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
}
.btn-etapa-acao.detalhes {
    background: #f8f9fa;
    color: #666;
}
.btn-etapa-acao.detalhes:hover {
    background: #e9ecef;
}

/* Atividades Recentes */
.atividades-recentes {
    background: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
.atividades-recentes h3 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #333;
}
.atividade-recente-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #f5f5f5;
    gap: 15px;
}
.atividade-recente-item:last-child {
    border-bottom: none;
}
.atividade-recente-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.atividade-recente-icon.concluida {
    background: #d4edda;
    color: #155724;
}
.atividade-recente-icon.andamento {
    background: #cce5ff;
    color: #004085;
}
.atividade-recente-icon.pendente {
    background: #fff3cd;
    color: #856404;
}
.atividade-recente-info {
    flex: 1;
}
.atividade-recente-info h4 {
    font-size: 14px;
    margin: 0 0 5px 0;
    color: #333;
}
.atividade-recente-info p {
    font-size: 12px;
    color: #888;
    margin: 0;
}
.atividade-recente-status {
    text-align: right;
}
.atividade-recente-status .hora {
    font-size: 13px;
    font-weight: 600;
    color: #333;
}
.atividade-recente-status .badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 5px;
}
.badge-success { background: #d4edda; color: #155724; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-info { background: #cce5ff; color: #004085; }

/* ================= MODAL WIZARD ================= */
.wizard-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 9999;
    overflow-y: auto;
}
.wizard-container {
    min-height: 100vh;
    padding: 15px;
    display: flex;
    flex-direction: column;
}
.wizard-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 20px;
    color: white;
    margin-bottom: 20px;
    position: relative;
}
.wizard-header h2 {
    margin: 0;
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.wizard-header p {
    margin: 5px 0 0 0;
    opacity: 0.9;
    font-size: 13px;
}
.btn-fechar-wizard {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.3s;
}
.btn-fechar-wizard:hover {
    background: rgba(255,255,255,0.3);
}

/* Steps */
.wizard-steps {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
}
.wizard-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    opacity: 0.4;
    transition: all 0.3s;
}
.wizard-step.active {
    opacity: 1;
}
.wizard-step.completed {
    opacity: 1;
}
.wizard-step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: white;
    color: #11998e;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 8px;
    border: 3px solid white;
    transition: all 0.3s;
}
.wizard-step.active .wizard-step-icon {
    box-shadow: 0 0 0 4px rgba(255,255,255,0.3);
}
.wizard-step.completed .wizard-step-icon {
    background: #27ae60;
    color: white;
}
.wizard-step-label {
    font-size: 11px;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
}

/* Wizard Body */
.wizard-body {
    flex: 1;
}
.wizard-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
.wizard-card-titulo {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.wizard-card-titulo i {
    color: #11998e;
    font-size: 20px;
}

/* Grid de Etapas no Wizard */
.etapas-wizard-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}
@media (min-width: 768px) {
    .etapas-wizard-grid { grid-template-columns: repeat(2, 1fr); }
}
.etapa-wizard-card {
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 15px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}
.etapa-wizard-card:hover {
    border-color: #11998e;
    background: #f0fff4;
    transform: translateY(-2px);
}
.etapa-wizard-card.selecionada {
    border-color: #11998e;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}
.etapa-wizard-card.selecionada .etapa-wizard-nome,
.etapa-wizard-card.selecionada .etapa-wizard-progresso {
    color: white;
}
.etapa-wizard-card i {
    font-size: 32px;
    color: #11998e;
    margin-bottom: 10px;
    display: block;
}
.etapa-wizard-card.selecionada i {
    color: white;
}
.etapa-wizard-nome {
    font-weight: 700;
    font-size: 15px;
    color: #333;
    margin-bottom: 5px;
}
.etapa-wizard-progresso {
    font-size: 12px;
    color: #888;
}

/* Foto Upload */
.foto-upload-area {
    border: 3px dashed #e0e0e0;
    border-radius: 15px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: #fafafa;
}
.foto-upload-area:hover {
    border-color: #11998e;
    background: #f0fff4;
}
.foto-upload-area i {
    font-size: 48px;
    color: #11998e;
    margin-bottom: 15px;
    display: block;
}
.foto-upload-area h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
    color: #333;
}
.foto-upload-area p {
    margin: 0;
    font-size: 13px;
    color: #888;
}
.foto-preview {
    max-width: 100%;
    border-radius: 10px;
    margin-top: 15px;
}

/* Inputs */
.wizard-input {
    width: 100%;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s;
    box-sizing: border-box;
}
.wizard-input:focus {
    outline: none;
    border-color: #11998e;
}

/* Timer */
.wizard-timer {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    color: white;
    margin-bottom: 20px;
}
.wizard-tempo {
    font-size: 48px;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    letter-spacing: 3px;
    margin-bottom: 5px;
}
.wizard-timer-label {
    font-size: 13px;
    opacity: 0.8;
}

/* Botões de Ação no Wizard */
.wizard-acoes-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.wizard-acao-btn {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}
.wizard-acao-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
.wizard-acao-btn i {
    font-size: 28px;
    margin-bottom: 8px;
    display: block;
}
.wizard-acao-btn.atividade { border-color: #3498db; color: #3498db; }
.wizard-acao-btn.atividade:hover { background: #ebf5fb; }

.wizard-acao-btn.foto { border-color: #9b59b6; color: #9b59b6; }
.wizard-acao-btn.foto:hover { background: #f5eef8; }

.wizard-acao-btn.pausar { border-color: #f39c12; color: #f39c12; }
.wizard-acao-btn.pausar:hover { background: #fef9e7; }

.wizard-acao-btn.finalizar { border-color: #27ae60; color: #27ae60; }
.wizard-acao-btn.finalizar:hover { background: #eafaf1; }

/* Lista de Atividades */
.wizard-atividades-lista {
    max-height: 300px;
    overflow-y: auto;
}
.wizard-atividade-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.wizard-atividade-item.executada {
    border-left: 4px solid #27ae60;
}
.wizard-atividade-item.pendente {
    border-left: 4px solid #f39c12;
}
.wizard-atividade-item i {
    font-size: 20px;
}
.wizard-atividade-item.executada i {
    color: #27ae60;
}
.wizard-atividade-item.pendente i {
    color: #f39c12;
}
.wizard-atividade-info {
    flex: 1;
}
.wizard-atividade-info h5 {
    margin: 0 0 3px 0;
    font-size: 14px;
    color: #333;
}
.wizard-atividade-info p {
    margin: 0;
    font-size: 12px;
    color: #888;
}

/* Botão Principal Wizard */
.wizard-btn-principal {
    display: block;
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    border-radius: 15px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    margin-top: 20px;
}
.wizard-btn-principal:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.3);
}
.wizard-btn-principal:disabled {
    background: #95a5a6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Modais Internos */
.wizard-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    padding: 15px;
}
.wizard-modal-content {
    background: white;
    border-radius: 20px;
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}
.wizard-modal-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 20px;
    border-radius: 20px 20px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.wizard-modal-header h3 {
    margin: 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.wizard-modal-body {
    padding: 25px;
}
.wizard-modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}
.wizard-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}
.wizard-btn.secundario {
    background: #f8f9fa;
    color: #666;
}
.wizard-btn.secundario:hover {
    background: #e9ecef;
}
.wizard-btn.primario {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}
.wizard-btn.primario:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}
.empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 40px;
    color: #ddd;
}
.empty-state p {
    color: #888;
    margin: 0;
}

/* Responsivo */
@media (min-width: 768px) {
    .obras-container { padding: 30px; max-width: 1200px; margin: 0 auto; }
    .wizard-container { padding: 30px; }
    .wizard-tempo { font-size: 64px; }
}

/* Animações */
@keyframes slideInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.etapa-card, .atividade-recente-item {
    animation: slideInUp 0.4s ease;
}
</style>

<div class="obras-container">

    <!-- Header da Obra -->
    <div class="obra-detalhe-header">
        <a href="<?= site_url('tecnicos/minhas_obras') ?>" class="btn-voltar">
            <i class="icon-arrow-left"></i> Voltar
        </a>
        <h1><i class="icon-building"></i> <?= htmlspecialchars($obra->nome) ?></h1>
        <p><i class="icon-user"></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Cliente não informado') ?></p>
    </div>

    <!-- Alerta Atividade em Andamento -->
    <?php if (!empty($wizard_em_andamento)): ?>
    <div class="atividade-andamento-alert">
        <h3><i class="icon-time"></i> Atividade em Andamento</h3>
        <p>
            <strong><?= htmlspecialchars($wizard_em_andamento->etapa_nome ?? 'Geral') ?></strong><br>
            Iniciado às <?= date('H:i', strtotime($wizard_em_andamento->hora_inicio)) ?> -
            <?= date('d/m/Y', strtotime($wizard_em_andamento->data_atividade ?? 'now')) ?>
        </p>
        <button class="btn-continuar" onclick="WizardAtendimento.continuar(<?= $wizard_em_andamento->id ?>, '<?= $wizard_em_andamento->hora_inicio ?>', '<?= htmlspecialchars($wizard_em_andamento->etapa_nome ?? 'Atividade geral') ?>')">
            <i class="icon-play"></i> CONTINUAR ATENDIMENTO
        </button>
    </div>
    <?php else: ?>
    <!-- Botão Iniciar Principal -->
    <button class="btn-iniciar-principal" onclick="WizardAtendimento.iniciar()">
        <i class="icon-play-circle"></i> INICIAR ATENDIMENTO
    </button>
    <?php endif; ?>

    <!-- Cards de Estatísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-numero"><?= count($etapas) ?></div>
            <div class="stat-label">Etapas Totais</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-numero"><?= count(array_filter($etapas, function($e) { return ($e->status ?? '') === 'concluida'; })) ?></div>
            <div class="stat-label">Concluídas</div>
        </div>
        <div class="stat-card info">
            <div class="stat-numero"><?= count($minhas_atividades) ?></div>
            <div class="stat-label">Minhas Atividades</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-numero"><?= $obra->percentual_concluido ?? 0 ?>%</div>
            <div class="stat-label">Progresso</div>
        </div>
    </div>

    <!-- Etapas da Obra -->
    <div class="etapas-section">
        <h3 class="section-title"><i class="icon-list"></i> Etapas da Obra</h3>

        <?php if (empty($etapas)): ?>
        <div class="wizard-card">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="icon-list-alt"></i>
                </div>
                <p>Nenhuma etapa cadastrada</p>
            </div>
        </div>
        <?php else: ?>
        <div class="etapas-lista">
            <?php foreach ($etapas as $etapa): ?>
                <?php
                $statusClass = '';
                $statusLabel = '';
                $statusIcon = '';
                switch($etapa->status ?? 'pendente') {
                    case 'concluida':
                        $statusClass = 'concluida';
                        $statusLabel = 'Concluída';
                        $statusIcon = 'icon-ok';
                        break;
                    case 'em-andamento':
                        $statusClass = 'em-andamento';
                        $statusLabel = 'Em Andamento';
                        $statusIcon = 'icon-play';
                        break;
                    default:
                        $statusClass = 'pendente';
                        $statusLabel = 'Pendente';
                        $statusIcon = 'icon-time';
                }
                ?>
                <div class="etapa-card">
                    <div class="etapa-header-card <?= $statusClass ?>">
                        <span class="etapa-status-badge"><?= $statusLabel ?></span>
                        <div class="etapa-numero">
                            <i class="<?= $statusIcon ?>"></i> Etapa #<?= $etapa->numero_etapa ?? $etapa->id ?>
                        </div>
                        <h2 class="etapa-nome"><?= htmlspecialchars($etapa->nome) ?></h2>
                        <div class="etapa-progresso">
                            <i class="icon-chart"></i> <?= $etapa->percentual_concluido ?? 0 ?>% concluído
                        </div>
                    </div>

                    <div class="etapa-body">
                        <div class="etapa-barra-progresso">
                            <div class="etapa-barra-preenchida" style="width: <?= $etapa->percentual_concluido ?? 0 ?>%"></div>
                        </div>

                        <?php if (!empty($etapa->descricao)): ?>
                        <p class="etapa-descricao"><?= nl2br(htmlspecialchars($etapa->descricao)) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($atividades_por_etapa[$etapa->id])): ?>
                        <div class="etapa-atividades">
                            <h4>Atividades Registradas</h4>
                            <?php foreach (array_slice($atividades_por_etapa[$etapa->id], 0, 3) as $ativ): ?>
                            <div class="etapa-atividade-item <?= ($ativ->status ?? '') === 'concluida' ? '' : 'pendente' ?>">
                                <i class="<?= ($ativ->status ?? '') === 'concluida' ? 'icon-check' : 'icon-time' ?>"></i>
                                <?= htmlspecialchars($ativ->titulo ?? $ativ->descricao ?? 'Atividade #' . $ativ->id) ?>
                            </div>
                            <?php endforeach; ?>
                            <?php if (count($atividades_por_etapa[$etapa->id]) > 3): ?>
                            <div class="etapa-atividade-item" style="color: #888;">
                                <i class="icon-ellipsis-horizontal"></i>
                                +<?= count($atividades_por_etapa[$etapa->id]) - 3 ?> atividades
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="etapa-actions">
                        <?php if (empty($wizard_em_andamento) && ($etapa->status ?? '') !== 'concluida'): ?>
                        <button class="btn-etapa-acao iniciar" onclick="WizardAtendimento.iniciarComEtapa(<?= $etapa->id ?>, '<?= htmlspecialchars($etapa->nome) ?>')">
                            <i class="icon-play"></i> Iniciar
                        </button>
                        <?php endif; ?>
                        <button class="btn-etapa-acao detalhes" onclick="this.closest('.etapa-card').querySelector('.etapa-detalhes').style.display = this.closest('.etapa-card').querySelector('.etapa-detalhes').style.display === 'none' ? 'block' : 'none'">
                            <i class="icon-chevron-down"></i> Detalhes
                        </button>
                    </div>

                    <div class="etapa-detalhes" style="display: none; padding: 0 20px 20px;">
                        <?php if (!empty($atividades_por_etapa[$etapa->id])): ?>
                        <div style="background: #f8f9fa; border-radius: 10px; padding: 15px;">
                            <h5 style="margin: 0 0 10px 0; font-size: 13px; color: #888;">Todas as Atividades:</h5>
                            <?php foreach ($atividades_por_etapa[$etapa->id] as $ativ): ?>
                            <div style="padding: 8px 0; border-bottom: 1px solid #eee; font-size: 13px;">
                                <i class="<?= ($ativ->status ?? '') === 'concluida' ? 'icon-check' : 'icon-time' ?>" style="color: <?= ($ativ->status ?? '') === 'concluida' ? '#27ae60' : '#f39c12' ?>;"></i>
                                <?= htmlspecialchars($ativ->titulo ?? $ativ->descricao ?? 'Atividade #' . $ativ->id) ?>
                                <span style="float: right; color: #888;">
                                    <?= date('d/m/Y', strtotime($ativ->data_atividade ?? $ativ->created_at ?? 'now')) ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Atividades Recentes -->
    <?php if (!empty($minhas_atividades)): ?>
    <div class="atividades-recentes">
        <h3><i class="icon-history"></i> Minhas Atividades Recentes</h3>

        <?php foreach (array_slice($minhas_atividades, 0, 5) as $ativ): ?>
        <div class="atividade-recente-item">
            <div class="atividade-recente-icon <?= ($ativ->status ?? '') === 'concluida' ? 'concluida' : (($ativ->status ?? '') === 'em_andamento' ? 'andamento' : 'pendente') ?>">
                <i class="icon-<?= ($ativ->status ?? '') === 'concluida' ? 'check' : (($ativ->status ?? '') === 'em_andamento' ? 'play' : 'time') ?>"></i>
            </div>
            <div class="atividade-recente-info">
                <h4><?= htmlspecialchars($ativ->etapa_nome ?? 'Atividade Geral') ?></h4>
                <p><?= htmlspecialchars($ativ->titulo ?? $ativ->descricao ?? 'Atividade #' . $ativ->id) ?></p>
            </div>
            <div class="atividade-recente-status">
                <div class="hora">
                    <?= !empty($ativ->hora_inicio) ? date('H:i', strtotime($ativ->hora_inicio)) : '--:--' ?>
                    -
                    <?= !empty($ativ->hora_fim) ? date('H:i', strtotime($ativ->hora_fim)) : '--:--' ?>
                </div>
                <span class="badge badge-<?= ($ativ->status ?? '') === 'concluida' ? 'success' : (($ativ->status ?? '') === 'em_andamento' ? 'info' : 'warning') ?>">
                    <?= ($ativ->status ?? '') === 'concluida' ? 'Concluída' : (($ativ->status ?? '') === 'em_andamento' ? 'Em Andamento' : 'Pendente') ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<!-- ================= MODAL WIZARD DE ATENDIMENTO ================= -->
<div id="wizardModal" class="wizard-overlay">
    <div class="wizard-container">

        <!-- Header -->
        <div class="wizard-header">
            <button class="btn-fechar-wizard" onclick="WizardAtendimento.fechar()">
                <i class="icon-remove"></i>
            </button>
            <h2><i class="icon-walk"></i> <span id="wizardTitle">Novo Atendimento</span></h2>
            <p>Registre sua entrada e acompanhe o tempo de execução</p>
        </div>

        <!-- Steps -->
        <div class="wizard-steps">
            <div class="wizard-step active" data-step="1">
                <div class="wizard-step-icon"><i class="icon-signin"></i></div>
                <div class="wizard-step-label">Check-in</div>
            </div>
            <div class="wizard-step" data-step="2">
                <div class="wizard-step-icon"><i class="icon-wrench"></i></div>
                <div class="wizard-step-label">Execução</div>
            </div>
            <div class="wizard-step" data-step="3">
                <div class="wizard-step-icon"><i class="icon-signout"></i></div>
                <div class="wizard-step-label">Finalizar</div>
            </div>
        </div>

        <!-- Body -->
        <div class="wizard-body">

            <!-- STEP 1: CHECK-IN -->
            <div id="stepCheckin" class="wizard-step-content">

                <!-- Seleção de Etapa -->
                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-list-check"></i> Selecione a Etapa
                    </div>
                    <div class="etapas-wizard-grid">
                        <?php foreach ($etapas as $etapa): ?>
                        <div class="etapa-wizard-card" data-etapa-id="<?= $etapa->id ?>" onclick="WizardAtendimento.selecionarEtapa(<?= $etapa->id ?>, '<?= htmlspecialchars($etapa->nome) ?>', this)">
                            <i class="icon-hard-hat"></i>
                            <div class="etapa-wizard-nome"><?= htmlspecialchars($etapa->nome) ?></div>
                            <div class="etapa-wizard-progresso"><?= $etapa->percentual_concluido ?? 0 ?>% concluído</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="checkinEtapaId" value="">
                </div>

                <!-- Foto do Check-in -->
                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-camera"></i> Foto do Local (Opcional)
                    </div>
                    <div class="foto-upload-area" onclick="document.getElementById('checkinFoto').click()">
                        <i class="icon-camera"></i>
                        <h4>Clique para adicionar foto</h4>
                        <p>Registre o estado inicial do local</p>
                        <input type="file" id="checkinFoto" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFoto(this, 'checkinPreview')">
                    </div>
                    <img id="checkinPreview" class="foto-preview" style="display: none;">
                </div>

                <!-- Observações -->
                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-edit"></i> Observações
                    </div>
                    <textarea id="checkinObservacoes" class="wizard-input" rows="4" placeholder="Descreva as condições do local, materiais disponíveis, ou qualquer observação importante..."></textarea>
                </div>

                <!-- Botão Iniciar -->
                <button id="btnIniciar" class="wizard-btn-principal" onclick="WizardAtendimento.realizarCheckin()" disabled>
                    <i class="icon-play"></i> INICIAR ATENDIMENTO
                </button>
            </div>

            <!-- STEP 2: EXECUÇÃO -->
            <div id="stepExecucao" class="wizard-step-content" style="display: none;">

                <!-- Timer -->
                <div class="wizard-timer">
                    <div class="wizard-tempo" id="timerDisplay">00:00:00</div>
                    <div class="wizard-timer-label"><i class="icon-time"></i> Tempo de Execução</div>
                </div>

                <!-- Info Etapa -->
                <div class="wizard-card" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="icon-info-sign" style="font-size: 24px;"></i>
                        <div>
                            <div style="font-size: 12px; opacity: 0.9;">Etapa em Execução</div>
                            <div style="font-size: 18px; font-weight: 700;" id="etapaExecucaoNome">--</div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="wizard-acoes-grid">
                    <div class="wizard-acao-btn atividade" onclick="WizardAtendimento.abrirModalAtividade()">
                        <i class="icon-plus"></i>
                        <strong>Registrar Atividade</strong>
                        <small style="display: block; margin-top: 5px; opacity: 0.7;">Adicione trabalhos</small>
                    </div>
                    <div class="wizard-acao-btn foto" onclick="WizardAtendimento.abrirModalFoto()">
                        <i class="icon-camera"></i>
                        <strong>Adicionar Foto</strong>
                        <small style="display: block; margin-top: 5px; opacity: 0.7;">Documente o serviço</small>
                    </div>
                    <div class="wizard-acao-btn pausar" onclick="WizardAtendimento.pausarAtendimento()">
                        <i class="icon-pause"></i>
                        <strong>Pausar</strong>
                        <small style="display: block; margin-top: 5px; opacity: 0.7;">Interrompa</small>
                    </div>
                    <div class="wizard-acao-btn finalizar" onclick="WizardAtendimento.abrirModalCheckout()">
                        <i class="icon-stop"></i>
                        <strong>Finalizar</strong>
                        <small style="display: block; margin-top: 5px; opacity: 0.7;">Encerre</small>
                    </div>
                </div>

                <!-- Atividades Registradas -->
                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-list-ul"></i> Atividades Registradas
                        <span id="contadorAtividades" style="background: #11998e; color: white; padding: 4px 12px; border-radius: 15px; font-size: 12px; margin-left: 10px;">0</span>
                    </div>
                    <div class="wizard-atividades-lista" id="listaAtividades">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="icon-clipboard"></i>
                            </div>
                            <p>Nenhuma atividade registrada ainda</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL REGISTRAR ATIVIDADE -->
<div id="modalAtividade" class="wizard-modal">
    <div class="wizard-modal-content">
        <div class="wizard-modal-header">
            <h3><i class="icon-plus"></i> Registrar Atividade</h3>
            <button class="btn-fechar-wizard" onclick="WizardAtendimento.fecharModal('modalAtividade')" style="position: static; width: 35px; height: 35px;">
                <i class="icon-remove"></i>
            </button>
        </div>
        <div class="wizard-modal-body">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">
                    <i class="icon-tag"></i> Tipo de Atividade *
                </label>
                <select id="atividadeTipoId" class="wizard-input">
                    <option value="">Selecione o tipo...</option>
                    <?php foreach ($tipos_atividades as $tipo): ?>
                        <option value="<?= is_object($tipo) ? $tipo->id : ($tipo['id'] ?? '') ?>">
                            <?= htmlspecialchars(is_object($tipo) ? $tipo->nome : ($tipo['nome'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">
                    <i class="icon-edit"></i> Descrição
                </label>
                <textarea id="atividadeDescricao" class="wizard-input" rows="4" placeholder="Descreva detalhadamente o que foi realizado..."></textarea>
            </div>

            <div>
                <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #555;">
                    <i class="icon-question-sign"></i> Status
                </label>
                <div style="display: flex; gap: 15px;">
                    <label style="flex: 1; padding: 15px; background: #d4edda; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <input type="radio" name="atividadeStatus" value="executada" checked style="margin: 0;">
                        <i class="icon-check" style="color: #27ae60;"></i>
                        <span style="font-weight: 600; color: #155724;">Executada</span>
                    </label>
                    <label style="flex: 1; padding: 15px; background: #fff3cd; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <input type="radio" name="atividadeStatus" value="pendente" style="margin: 0;">
                        <i class="icon-time" style="color: #f39c12;"></i>
                        <span style="font-weight: 600; color: #856404;">Pendente</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="wizard-modal-footer">
            <button class="wizard-btn secundario" onclick="WizardAtendimento.fecharModal('modalAtividade')">Cancelar</button>
            <button class="wizard-btn primario" onclick="WizardAtendimento.salvarAtividade()">
                <i class="icon-save"></i> Salvar
            </button>
        </div>
    </div>
</div>

<!-- MODAL ADICIONAR FOTO -->
<div id="modalFoto" class="wizard-modal">
    <div class="wizard-modal-content">
        <div class="wizard-modal-header" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
            <h3><i class="icon-camera"></i> Adicionar Foto</h3>
            <button class="btn-fechar-wizard" onclick="WizardAtendimento.fecharModal('modalFoto')" style="position: static; width: 35px; height: 35px;">
                <i class="icon-remove"></i>
            </button>
        </div>
        <div class="wizard-modal-body">
            <div class="foto-upload-area" onclick="document.getElementById('fotoFile').click()">
                <i class="icon-camera" style="color: #9b59b6;"></i>
                <h4>Clique para selecionar foto</h4>
                <p>Ou arraste e solte aqui</p>
                <input type="file" id="fotoFile" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFotoModal(this)">
            </div>
            <img id="fotoPreviewModal" class="foto-preview" style="display: none;">

            <div style="margin-top: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">
                    Descrição da Foto
                </label>
                <textarea id="fotoDescricao" class="wizard-input" rows="2" placeholder="Descreva o que está na foto..."></textarea>
            </div>
        </div>
        <div class="wizard-modal-footer">
            <button class="wizard-btn secundario" onclick="WizardAtendimento.fecharModal('modalFoto')">Cancelar</button>
            <button class="wizard-btn primario" onclick="WizardAtendimento.salvarFoto()" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                <i class="icon-camera"></i> Salvar Foto
            </button>
        </div>
    </div>
</div>

<!-- MODAL CHECKOUT / FINALIZAR -->
<div id="modalCheckout" class="wizard-modal">
    <div class="wizard-modal-content">
        <div class="wizard-modal-header" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%);">
            <h3><i class="icon-signout"></i> Finalizar Atendimento</h3>
            <button class="btn-fechar-wizard" onclick="WizardAtendimento.fecharModal('modalCheckout')" style="position: static; width: 35px; height: 35px;">
                <i class="icon-remove"></i>
            </button>
        </div>
        <div class="wizard-modal-body">
            <!-- Resumo -->
            <div style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; padding: 20px; border-radius: 15px; margin-bottom: 20px;">
                <div style="font-size: 12px; opacity: 0.8; margin-bottom: 5px;"><i class="icon-time"></i> Resumo</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 12px; opacity: 0.7;">Início</div>
                        <div style="font-size: 16px; font-weight: 600;" id="checkoutHoraInicio">--:--</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; opacity: 0.7;">Tempo Total</div>
                        <div style="font-size: 24px; font-weight: 700; color: #27ae60;" id="checkoutTempoTotal">--:--</div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">
                    <i class="icon-question-sign"></i> Trabalho Concluído? *
                </label>
                <select id="checkoutConcluido" class="wizard-input">
                    <option value="1">Sim, trabalho concluído</option>
                    <option value="0">Não, preciso retornar</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">
                    <i class="icon-edit"></i> Resumo do Trabalho
                </label>
                <textarea id="checkoutResumo" class="wizard-input" rows="3" placeholder="Descreva o que foi realizado durante o atendimento..."></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">
                    <i class="icon-exclamation-sign"></i> Problemas/Pendências
                </label>
                <textarea id="checkoutPendencias" class="wizard-input" rows="2" placeholder="Algum problema encontrado? Material faltando?"></textarea>
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">
                    <i class="icon-camera"></i> Foto de Saída (Opcional)
                </label>
                <div class="foto-upload-area" onclick="document.getElementById('checkoutFoto').click()" style="padding: 20px;">
                    <i class="icon-camera"></i>
                    <h4 style="font-size: 14px;">Adicionar foto de saída</h4>
                    <input type="file" id="checkoutFoto" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFoto(this, 'checkoutPreview')">
                </div>
                <img id="checkoutPreview" class="foto-preview" style="display: none;">
            </div>
        </div>
        <div class="wizard-modal-footer">
            <button class="wizard-btn secundario" onclick="WizardAtendimento.fecharModal('modalCheckout')">Voltar</button>
            <button class="wizard-btn primario" onclick="WizardAtendimento.realizarCheckout()" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%);">
                <i class="icon-signout"></i> FINALIZAR
            </button>
        </div>
    </div>
</div>

<script>
// WIZARD DE ATENDIMENTO - MESMO PADRÃO DE MINHAS_OBRAS
window.WizardAtendimento = {
    obraId: <?= json_encode($obra->id) ?>,
    etapaSelecionadaId: null,
    etapaSelecionadaNome: '',
    atividadeEmAndamento: null,
    timerInterval: null,
    horaInicio: null,
    atividadesRegistradas: [],
    csrfTokenName: '<?= config_item("csrf_token_name") ?>',
    csrfCookieName: '<?= config_item("csrf_cookie_name") ?>',
    stepAtual: 1,

    getCookie: function(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    },

    getCsrfToken: function() {
        return this.getCookie(this.csrfCookieName);
    },

    appendCsrf: function(formData) {
        var token = this.getCsrfToken();
        if (token) {
            formData.append(this.csrfTokenName, token);
        }
        return formData;
    },

    // INICIAR WIZARD
    iniciar: function() {
        this.stepAtual = 1;
        this.atualizarSteps();
        document.getElementById('wizardModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
        this.mostrarStep(1);
    },

    // INICIAR COM ETAPA PRE-SELECIONADA
    iniciarComEtapa: function(etapaId, etapaNome) {
        this.iniciar();
        setTimeout(function() {
            var el = document.querySelector('.etapa-wizard-card[data-etapa-id="' + etapaId + '"]');
            if (el) {
                WizardAtendimento.selecionarEtapa(etapaId, etapaNome, el);
            }
        }, 100);
    },

    // CONTINUAR ATENDIMENTO EM ANDAMENTO
    continuar: function(atividadeId, horaInicio, etapaNome) {
        this.atividadeEmAndamento = atividadeId;
        this.horaInicio = new Date(horaInicio);
        this.etapaSelecionadaNome = etapaNome;
        this.stepAtual = 2;

        document.getElementById('etapaExecucaoNome').textContent = etapaNome || 'Atividade geral';
        document.getElementById('wizardTitle').textContent = 'Continuar Atendimento';
        document.getElementById('wizardModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
        this.atualizarSteps();
        this.mostrarStep(2);
        this.iniciarTimer();
    },

    // FECHAR WIZARD
    fechar: function() {
        document.getElementById('wizardModal').style.display = 'none';
        document.body.style.overflow = '';
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
        // Reset
        this.etapaSelecionadaId = null;
        this.etapaSelecionadaNome = '';
        document.querySelectorAll('.etapa-wizard-card').forEach(function(el) {
            el.classList.remove('selecionada');
        });
        document.getElementById('btnIniciar').disabled = true;
    },

    // ATUALIZAR INDICADOR DE STEPS
    atualizarSteps: function() {
        document.querySelectorAll('.wizard-step').forEach(function(step, index) {
            var stepNum = index + 1;
            step.classList.remove('active', 'completed');
            if (stepNum < WizardAtendimento.stepAtual) {
                step.classList.add('completed');
            } else if (stepNum === WizardAtendimento.stepAtual) {
                step.classList.add('active');
            }
        });
    },

    // MOSTRAR STEP
    mostrarStep: function(step) {
        document.querySelectorAll('.wizard-step-content').forEach(function(el) {
            el.style.display = 'none';
        });
        if (step === 1) {
            document.getElementById('stepCheckin').style.display = 'block';
        } else if (step === 2) {
            document.getElementById('stepExecucao').style.display = 'block';
        }
    },

    // SELECIONAR ETAPA
    selecionarEtapa: function(etapaId, etapaNome, elemento) {
        this.etapaSelecionadaId = etapaId;
        this.etapaSelecionadaNome = etapaNome;
        document.getElementById('checkinEtapaId').value = etapaId;

        document.querySelectorAll('.etapa-wizard-card').forEach(function(el) {
            el.classList.remove('selecionada');
        });
        elemento.classList.add('selecionada');
        document.getElementById('btnIniciar').disabled = false;
    },

    // PREVIEW FOTO
    previewFoto: function(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.getElementById(previewId);
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    },

    previewFotoModal: function(input) {
        this.previewFoto(input, 'fotoPreviewModal');
        document.querySelector('#modalFoto .foto-upload-area').style.display = 'none';
        document.getElementById('fotoPreviewModal').style.display = 'block';
    },

    // REALIZAR CHECKIN
    realizarCheckin: function() {
        var etapaId = this.etapaSelecionadaId;
        if (!etapaId) {
            alert('Selecione uma etapa.');
            return;
        }

        var formData = new FormData();
        formData = this.appendCsrf(formData);
        formData.append('obra_id', this.obraId);
        formData.append('etapa_id', etapaId);
        formData.append('tipo_id', '1');

        var fotoInput = document.getElementById('checkinFoto');
        if (fotoInput.files.length > 0) {
            formData.append('foto', fotoInput.files[0]);
        }

        var observacoes = document.getElementById('checkinObservacoes').value;
        if (observacoes) {
            formData.append('observacoes', observacoes);
        }

        var btn = document.getElementById('btnIniciar');
        btn.innerHTML = '<i class="icon-refresh icon-spin"></i> Iniciando...';
        btn.disabled = true;

        var self = this;
        fetch('<?= site_url("atividades/checkin_obra") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(r) {
            // Verificar se a resposta é JSON
            var contentType = r.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // Se não for JSON, ler como texto para debug
                return r.text().then(function(text) {
                    console.error('Resposta não-JSON:', text.substring(0, 500));
                    throw new Error('Resposta inválida do servidor. Verifique o console (F12) para detalhes.');
                });
            }
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                self.atividadeEmAndamento = data.atividade_id;
                self.horaInicio = new Date();
                self.stepAtual = 2;
                document.getElementById('etapaExecucaoNome').textContent = self.etapaSelecionadaNome;
                document.getElementById('wizardTitle').textContent = 'Execução em Andamento';
                self.atualizarSteps();
                self.mostrarStep(2);
                self.iniciarTimer();
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
                btn.innerHTML = '<i class="icon-play"></i> INICIAR ATENDIMENTO';
                btn.disabled = false;
            }
        })
        .catch(function(err) {
            console.error('Erro completo:', err);
            alert('Erro ao iniciar: ' + err.message);
            btn.innerHTML = '<i class="icon-play"></i> INICIAR ATENDIMENTO';
            btn.disabled = false;
        });
    },

    // TIMER
    iniciarTimer: function() {
        var self = this;
        this.timerInterval = setInterval(function() {
            var agora = new Date();
            var diff = agora - self.horaInicio;

            var horas = Math.floor(diff / 3600000);
            var minutos = Math.floor((diff % 3600000) / 60000);
            var segundos = Math.floor((diff % 60000) / 1000);

            var formatado =
                String(horas).padStart(2, '0') + ':' +
                String(minutos).padStart(2, '0') + ':' +
                String(segundos).padStart(2, '0');

            document.getElementById('timerDisplay').textContent = formatado;
        }, 1000);
    },

    // MODAIS
    abrirModalAtividade: function() {
        document.getElementById('modalAtividade').style.display = 'flex';
    },

    abrirModalFoto: function() {
        document.getElementById('modalFoto').style.display = 'flex';
    },

    abrirModalCheckout: function() {
        document.getElementById('checkoutHoraInicio').textContent = this.horaInicio.toLocaleTimeString('pt-BR');

        var agora = new Date();
        var diff = agora - this.horaInicio;
        var horas = Math.floor(diff / 3600000);
        var minutos = Math.floor((diff % 3600000) / 60000);
        document.getElementById('checkoutTempoTotal').textContent =
            String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0');

        document.getElementById('modalCheckout').style.display = 'flex';
    },

    fecharModal: function(modalId) {
        document.getElementById(modalId).style.display = 'none';
    },

    // SALVAR ATIVIDADE
    salvarAtividade: function() {
        var tipoId = document.getElementById('atividadeTipoId').value;
        var descricao = document.getElementById('atividadeDescricao').value;
        var status = document.querySelector('input[name="atividadeStatus"]:checked').value;

        if (!tipoId) {
            alert('Selecione o tipo de atividade.');
            return;
        }

        var formData = new FormData();
        formData = this.appendCsrf(formData);
        formData.append('obra_id', this.obraId);
        formData.append('tipo_id', tipoId);
        formData.append('descricao', descricao);
        formData.append('status', status);

        var self = this;
        fetch('<?= site_url("atividades/adicionar_atividade_obra") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(r) {
            var contentType = r.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return r.text().then(function(text) {
                    console.error('Resposta não-JSON:', text.substring(0, 500));
                    throw new Error('Resposta inválida do servidor');
                });
            }
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                self.fecharModal('modalAtividade');
                self.adicionarAtividadeLista({
                    tipo: document.getElementById('atividadeTipoId').options[document.getElementById('atividadeTipoId').selectedIndex].text,
                    descricao: descricao,
                    status: status,
                    data: new Date().toLocaleString('pt-BR')
                });
                document.getElementById('atividadeTipoId').value = '';
                document.getElementById('atividadeDescricao').value = '';
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(function(err) {
            alert('Erro ao salvar atividade: ' + err.message);
            console.error(err);
        });
    },

    adicionarAtividadeLista: function(atividade) {
        var lista = document.getElementById('listaAtividades');
        if (this.atividadesRegistradas.length === 0) {
            lista.innerHTML = '';
        }
        this.atividadesRegistradas.push(atividade);
        document.getElementById('contadorAtividades').textContent = this.atividadesRegistradas.length;

        var statusClass = atividade.status === 'executada' ? 'executada' : 'pendente';
        var statusIcon = atividade.status === 'executada' ? 'icon-check' : 'icon-time';
        var statusColor = atividade.status === 'executada' ? '#27ae60' : '#f39c12';

        var html = '<div class="wizard-atividade-item ' + statusClass + '">' +
            '<i class="' + statusIcon + '" style="color: ' + statusColor + ';"></i>' +
            '<div class="wizard-atividade-info">' +
            '<h5>' + atividade.tipo + '</h5>' +
            '<p>' + atividade.descricao + '</p>' +
            '</div>' +
            '</div>';

        lista.innerHTML += html;
    },

    // SALVAR FOTO
    salvarFoto: function() {
        var fotoInput = document.getElementById('fotoFile');
        var descricao = document.getElementById('fotoDescricao').value;

        if (fotoInput.files.length === 0) {
            alert('Selecione uma foto.');
            return;
        }

        var formData = new FormData();
        formData = this.appendCsrf(formData);
        formData.append('obra_id', this.obraId);
        formData.append('foto', fotoInput.files[0]);
        formData.append('descricao', descricao);

        var self = this;
        fetch('<?= site_url("atividades/adicionar_foto_obra") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(r) {
            var contentType = r.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return r.text().then(function(text) {
                    console.error('Resposta não-JSON:', text.substring(0, 500));
                    throw new Error('Resposta inválida do servidor');
                });
            }
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                self.fecharModal('modalFoto');
                alert('Foto adicionada com sucesso!');
                fotoInput.value = '';
                document.getElementById('fotoDescricao').value = '';
                document.getElementById('fotoPreviewModal').style.display = 'none';
                document.querySelector('#modalFoto .foto-upload-area').style.display = 'block';
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(function(err) {
            alert('Erro ao adicionar foto: ' + err.message);
            console.error(err);
        });
    },

    // PAUSAR
    pausarAtendimento: function() {
        if (!confirm('Deseja pausar este atendimento?')) return;

        var body = 'obra_id=' + this.obraId;
        var token = this.getCsrfToken();
        if (token) {
            body += '&' + this.csrfTokenName + '=' + encodeURIComponent(token);
        }

        var self = this;
        fetch('<?= site_url("atividades/pausar") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: body
        })
        .then(function(r) {
            var contentType = r.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return r.text().then(function(text) {
                    console.error('Resposta não-JSON:', text.substring(0, 500));
                    throw new Error('Resposta inválida do servidor');
                });
            }
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                self.fechar();
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(function(err) {
            alert('Erro ao pausar: ' + err.message);
            console.error(err);
        });
    },

    // CHECKOUT / FINALIZAR
    realizarCheckout: function() {
        var concluida = document.getElementById('checkoutConcluido').value;
        var resumo = document.getElementById('checkoutResumo').value;
        var pendencias = document.getElementById('checkoutPendencias').value;

        var formData = new FormData();
        formData = this.appendCsrf(formData);
        formData.append('obra_id', this.obraId);
        formData.append('concluida', concluida);
        formData.append('resumo_final', resumo);
        formData.append('pendencias', pendencias);

        var fotoInput = document.getElementById('checkoutFoto');
        if (fotoInput.files.length > 0) {
            formData.append('foto', fotoInput.files[0]);
        }

        var self = this;
        fetch('<?= site_url("atividades/checkout_obra") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(r) {
            var contentType = r.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return r.text().then(function(text) {
                    console.error('Resposta não-JSON:', text.substring(0, 500));
                    throw new Error('Resposta inválida do servidor');
                });
            }
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                alert('Atendimento finalizado com sucesso!');
                self.fechar();
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(function(err) {
            alert('Erro ao finalizar atendimento: ' + err.message);
            console.error(err);
        });
    }
};

// Animate progress bars on load
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.etapa-barra-preenchida');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });
});
</script>
