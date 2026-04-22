<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Tema Moderno Obras - Mesmo padrão de minhas_obras -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
/* ===== Container Principal ===== */
.obra-container { padding: 15px; max-width: 100%; }

/* ===== Header da Obra ===== */
.obra-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 25px 20px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
    position: relative;
}
.obra-header h1 {
    margin: 0 0 8px 0;
    font-size: 22px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}
.obra-header p {
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
    transition: all 0.3s;
}
.btn-voltar:hover {
    background: rgba(255,255,255,0.3);
    color: white;
    text-decoration: none;
}

/* ===== Alerta de Atividade em Andamento ===== */
.atividade-andamento {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    color: white;
    box-shadow: 0 5px 20px rgba(243, 156, 18, 0.3);
}
.atividade-andamento h3 {
    margin: 0 0 10px 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.atividade-andamento p {
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

/* ===== Cards de Etapas ===== */
.etapas-section { margin-bottom: 20px; }
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
.etapa-header {
    padding: 20px;
    position: relative;
    color: white;
}
.etapa-header.aberto {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}
.etapa-header.em-andamento {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}
.etapa-header.concluida {
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
}
.etapa-numero {
    font-size: 12px;
    opacity: 0.9;
    margin-bottom: 5px;
}
.etapa-nome {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 8px 0;
    line-height: 1.3;
}
.etapa-progresso {
    font-size: 13px;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ===== Corpo do Card de Etapa ===== */
.etapa-body {
    padding: 20px;
}
.etapa-barra {
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

/* ===== Lista de Atividades ===== */
.atividades-lista {
    border-top: 1px solid #f0f0f0;
    padding-top: 15px;
}
.atividades-lista h4 {
    font-size: 13px;
    color: #888;
    margin-bottom: 10px;
    text-transform: uppercase;
}
.atividade-item {
    display: flex;
    align-items: center;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 8px;
    background: #f8f9fa;
    transition: all 0.2s;
}
.atividade-item:hover {
    background: #e9ecef;
}
.atividade-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    flex-shrink: 0;
}
.atividade-icon.aberto {
    background: #fff3cd;
    color: #856404;
}
.atividade-icon.andamento {
    background: #cce5ff;
    color: #004085;
}
.atividade-icon.concluida {
    background: #d4edda;
    color: #155724;
}
.atividade-info {
    flex: 1;
}
.atividade-info h5 {
    margin: 0 0 3px 0;
    font-size: 14px;
    color: #333;
}
.atividade-info p {
    margin: 0;
    font-size: 12px;
    color: #888;
}
.atividade-acoes {
    display: flex;
    gap: 8px;
}
.btn-acao {
    padding: 8px 15px;
    border: none;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s;
}
.btn-acao.iniciar {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}
.btn-acao.iniciar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}
.btn-acao.finalizar {
    background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
    color: white;
}
.btn-acao.finalizar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
}
.btn-acao.detalhes {
    background: #f8f9fa;
    color: #666;
}
.btn-acao.detalhes:hover {
    background: #e9ecef;
}

/* ===== Modal Wizard ===== */
.wizard-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.85);
    z-index: 9999;
    overflow-y: auto;
}
.wizard-container {
    min-height: 100vh;
    padding: 20px;
    display: flex;
    flex-direction: column;
}
.wizard-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 25px;
    color: white;
    margin-bottom: 20px;
    position: relative;
    text-align: center;
}
.wizard-header h2 {
    margin: 0 0 8px 0;
    font-size: 22px;
    font-weight: 700;
}
.wizard-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
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

/* ===== Steps do Wizard ===== */
.wizard-steps {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 25px;
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
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: white;
    color: #11998e;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
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

/* ===== Cards do Wizard ===== */
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

/* ===== Seleção de Etapa ===== */
.etapas-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}
@media (min-width: 768px) {
    .etapas-grid { grid-template-columns: repeat(2, 1fr); }
}
.etapa-selecao {
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 15px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}
.etapa-selecao:hover {
    border-color: #11998e;
    background: #f0fff4;
    transform: translateY(-2px);
}
.etapa-selecao.selecionada {
    border-color: #11998e;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}
.etapa-selecao i {
    font-size: 32px;
    color: #95a5a6;
    margin-bottom: 10px;
    display: block;
}
.etapa-selecao.selecionada i {
    color: white;
}
.etapa-selecao h4 {
    margin: 0 0 5px 0;
    font-size: 15px;
}
.etapa-selecao p {
    margin: 0;
    font-size: 12px;
    opacity: 0.8;
}

/* ===== Seleção de Atividade ===== */
.atividades-grid {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.atividade-selecao {
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 12px;
}
.atividade-selecao:hover {
    border-color: #3498db;
    background: #ebf5fb;
}
.atividade-selecao.selecionada {
    border-color: #3498db;
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}
.atividade-selecao i {
    font-size: 24px;
    color: #95a5a6;
}
.atividade-selecao.selecionada i {
    color: white;
}
.atividade-selecao-info {
    flex: 1;
}
.atividade-selecao-info h5 {
    margin: 0 0 3px 0;
    font-size: 14px;
}
.atividade-selecao-info p {
    margin: 0;
    font-size: 12px;
    opacity: 0.8;
}

/* ===== Opções de Atividade ===== */
.opcoes-atividade {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.opcao-item {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 12px;
}
.opcao-item:hover {
    border-color: #11998e;
}
.opcao-item input[type="radio"] {
    margin: 0;
    width: 20px;
    height: 20px;
    accent-color: #11998e;
}
.opcao-item.normal { border-left: 4px solid #3498db; }
.opcao-item.impedimento { border-left: 4px solid #f39c12; }
.opcao-item i {
    font-size: 20px;
    color: #11998e;
}
.opcao-item.impedimento i {
    color: #f39c12;
}
.opcao-info {
    flex: 1;
}
.opcao-info h5 {
    margin: 0 0 3px 0;
    font-size: 14px;
    color: #333;
}
.opcao-info p {
    margin: 0;
    font-size: 12px;
    color: #888;
}

/* ===== Justificativa ===== */
.justificativa-box {
    background: #fff3cd;
    border: 2px solid #f1c40f;
    border-radius: 12px;
    padding: 20px;
    margin-top: 15px;
}
.justificativa-box h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #856404;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ===== Upload de Foto ===== */
.foto-upload {
    border: 3px dashed #e0e0e0;
    border-radius: 15px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: #fafafa;
}
.foto-upload:hover {
    border-color: #11998e;
    background: #f0fff4;
}
.foto-upload i {
    font-size: 48px;
    color: #11998e;
    margin-bottom: 15px;
    display: block;
}
.foto-upload h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
    color: #333;
}
.foto-upload p {
    margin: 0;
    font-size: 13px;
    color: #888;
}
.foto-preview {
    max-width: 100%;
    border-radius: 10px;
    margin-top: 15px;
}

/* ===== Timer ===== */
.wizard-timer {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    color: white;
    margin-bottom: 20px;
}
.timer-tempo {
    font-size: 48px;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    letter-spacing: 3px;
    margin-bottom: 5px;
}
.timer-label {
    font-size: 13px;
    opacity: 0.8;
}

/* ===== Info Execução ===== */
.info-execucao {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border-radius: 15px;
    padding: 20px;
    color: white;
    margin-bottom: 20px;
}
.info-execucao h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
    opacity: 0.9;
}
.info-execucao p {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
}

/* ===== Ações do Wizard ===== */
.wizard-acoes {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.wizard-acao {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}
.wizard-acao:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
.wizard-acao i {
    font-size: 28px;
    margin-bottom: 8px;
    display: block;
}
.wizard-acao.registrar { border-color: #3498db; color: #3498db; }
.wizard-acao.registrar:hover { background: #ebf5fb; }
.wizard-acao.foto { border-color: #9b59b6; color: #9b59b6; }
.wizard-acao.foto:hover { background: #f5eef8; }
.wizard-acao.pausar { border-color: #f39c12; color: #f39c12; }
.wizard-acao.pausar:hover { background: #fef9e7; }
.wizard-acao.finalizar { border-color: #27ae60; color: #27ae60; }
.wizard-acao.finalizar:hover { background: #eafaf1; }
.wizard-acao strong {
    display: block;
    font-size: 14px;
}
.wizard-acao small {
    font-size: 11px;
    opacity: 0.7;
}

/* ===== Lista de Atividades no Check-out ===== */
.checkout-atividades {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.checkout-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s;
}
.checkout-item:hover {
    background: #e9ecef;
}
.checkout-item input[type="checkbox"] {
    width: 22px;
    height: 22px;
    accent-color: #27ae60;
    cursor: pointer;
}
.checkout-item-info {
    flex: 1;
}
.checkout-item-info h5 {
    margin: 0 0 3px 0;
    font-size: 14px;
    color: #333;
}
.checkout-item-info p {
    margin: 0;
    font-size: 12px;
    color: #888;
}
.status-select {
    padding: 8px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 20px;
    font-size: 12px;
    background: white;
    cursor: pointer;
}
.status-select:focus {
    outline: none;
    border-color: #11998e;
}

/* ===== Botões Principais ===== */
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

/* ===== Inputs ===== */
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
textarea.wizard-input {
    resize: vertical;
    min-height: 100px;
}

/* ===== Resumo Checkout ===== */
.resumo-box {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    border-radius: 15px;
    padding: 20px;
    color: white;
    margin-bottom: 20px;
}
.resumo-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.resumo-item:last-child {
    border-bottom: none;
}
.resumo-item span {
    font-size: 13px;
    opacity: 0.8;
}
.resumo-item strong {
    font-size: 16px;
}
.resumo-item strong.tempo {
    color: #27ae60;
    font-size: 24px;
}

/* ===== Empty State ===== */
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
.empty-state h4 {
    color: #666;
    font-weight: 400;
    margin: 0 0 5px 0;
    font-size: 16px;
}
.empty-state p {
    color: #999;
    font-size: 13px;
    margin: 0;
}

/* ===== Animações ===== */
@keyframes slideInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.etapa-card {
    animation: slideInUp 0.4s ease;
}

/* ===== Responsivo ===== */
@media (min-width: 768px) {
    .obra-container { padding: 30px; max-width: 1200px; margin: 0 auto; }
    .wizard-container { padding: 30px; }
    .timer-tempo { font-size: 64px; }
}
</style>

<div class="obra-container">

    <!-- Header da Obra -->
    <div class="obra-header">
        <a href="<?= site_url('tecnicos/minhas_obras') ?>" class="btn-voltar">
            <i class="icon-arrow-left"></i> Voltar
        </a>
        <h1><i class="icon-building"></i> <?= htmlspecialchars($obra->nome ?? 'Obra sem nome') ?></h1>
        <p><i class="icon-user"></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Cliente não informado') ?></p>
    </div>

    <!-- Alerta de Atividade em Andamento -->
    <?php if (!empty($wizard_em_andamento)): ?>
    <div class="atividade-andamento">
        <h3><i class="icon-time"></i> Atividade em Andamento</h3>
        <p>
            <strong><?= htmlspecialchars($wizard_em_andamento->etapa_nome ?? 'Atividade Geral') ?></strong><br>
            <?= htmlspecialchars($wizard_em_andamento->titulo ?? $wizard_em_andamento->descricao ?? '') ?><br>
            <small>Iniciado às <?= date('H:i', strtotime($wizard_em_andamento->hora_inicio)) ?> - <?= date('d/m/Y', strtotime($wizard_em_andamento->data_atividade ?? 'now')) ?></small>
        </p>
        <?php $ativAndamentoId = $wizard_em_andamento->id ?? $wizard_em_andamento->idAtividade ?? null; ?>
        <?php if ($ativAndamentoId): ?>
        <button class="btn-continuar" onclick="WizardAtendimento.continuar(<?= $ativAndamentoId ?>)">
            <i class="icon-play"></i> CONTINUAR ATENDIMENTO
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Cards de Etapas -->
    <div class="etapas-section">
        <h3 class="section-title"><i class="icon-list"></i> Etapas da Obra</h3>

        <?php
        // Normalize etapas to array
        if (!is_array($etapas)) {
            $etapas = is_object($etapas) ? [$etapas] : [];
        }
        ?>
        <?php if (empty($etapas)): ?>
        <div class="wizard-card">
            <div class="empty-state">
                <div class="empty-icon"><i class="icon-list-alt"></i></div>
                <h4>Nenhuma etapa cadastrada</h4>
                <p>Entre em contato com o gestor da obra.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="etapas-lista">
            <?php
            // Ensure $etapas is iterable
            if (!is_array($etapas) && !is_object($etapas)) {
                $etapas = [];
            }
            foreach ($etapas as $etapa): ?>
                <?php
                // Skip invalid etapas
                if (!is_object($etapa)) continue;

                // Debug: log available properties on first iteration
                // var_dump(get_object_vars($etapa)); die();

                $etapaId = property_exists($etapa, 'id') ? $etapa->id : (property_exists($etapa, 'idEtapa') ? $etapa->idEtapa : null);
                if (!$etapaId) continue;

                $statusEtapa = $etapa->status ?? 'aberto';
                $statusClass = $statusEtapa === 'concluida' ? 'concluida' : ($statusEtapa === 'em-andamento' ? 'em-andamento' : 'aberto');
                $statusLabel = $statusEtapa === 'concluida' ? 'Concluída' : ($statusEtapa === 'em-andamento' ? 'Em Andamento' : 'Aberta');

                // Buscar atividades desta etapa
                $atividadesEtapa = $atividades_por_etapa[$etapaId] ?? [];
                $atividadeAndamento = null;
                foreach ($atividadesEtapa as $ativ) {
                    if (($ativ->status ?? '') === 'em_andamento') {
                        $atividadeAndamento = $ativ;
                        break;
                    }
                }
                ?>
                <div class="etapa-card">
                    <div class="etapa-header <?= $statusClass ?>">
                        <span class="etapa-status-badge"><?= $statusLabel ?></span>
                        <div class="etapa-numero"><i class="icon-hard-hat"></i> Etapa #<?= $etapa->numero_etapa ?? $etapaId ?></div>
                        <h2 class="etapa-nome"><?= htmlspecialchars($etapa->nome ?? 'Etapa sem nome') ?></h2>
                        <div class="etapa-progresso">
                            <i class="icon-chart"></i> <?= $etapa->percentual_concluido ?? 0 ?>% concluído
                        </div>
                    </div>

                    <div class="etapa-body">
                        <div class="etapa-barra">
                            <div class="etapa-barra-preenchida" style="width: <?= $etapa->percentual_concluido ?? 0 ?>%"></div>
                        </div>

                        <?php if (!empty($etapa->descricao)): ?>
                        <p class="etapa-descricao"><?= nl2br(htmlspecialchars($etapa->descricao)) ?></p>
                        <?php endif; ?>

                        <!-- Botão Iniciar Atendimento Geral -->
                        <?php if (empty($wizard_em_andamento)): ?>
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ddd;">
                            <button class="btn-acao iniciar" onclick="WizardAtendimento.iniciar(<?= $etapaId ?>, '<?= htmlspecialchars($etapa->nome ?? 'Etapa') ?>', null, null)" style="width: 100%; justify-content: center;">
                                <i class="icon-play"></i> Iniciar Atendimento nesta Etapa
                            </button>
                        </div>
                        <?php endif; ?>

                        <!-- Lista de Atividades -->
                        <?php if (!empty($atividadesEtapa)): ?>
                        <div class="atividades-lista">
                            <h4>Atividades Cadastradas</h4>
                            <?php foreach ($atividadesEtapa as $ativ): ?>
                                <?php
                                $statusAtiv = $ativ->status ?? 'agendada';
                                $statusAtivClass = ($statusAtiv === 'concluida' || $statusAtiv === 'concluida') ? 'concluida' : (($statusAtiv === 'em_andamento' || $statusAtiv === 'iniciada') ? 'andamento' : 'aberto');
                                $statusAtivLabel = ($statusAtiv === 'concluida' || $statusAtiv === 'concluida') ? 'Concluída' : (($statusAtiv === 'em_andamento' || $statusAtiv === 'iniciada') ? 'Em Andamento' : 'Aberta');
                                $podeIniciar = (in_array($statusAtiv, ['agendada', 'aberto', 'iniciada']) || empty($statusAtiv)) && empty($wizard_em_andamento);
                                ?>
                                <div class="atividade-item">
                                    <div class="atividade-icon <?= $statusAtivClass ?>">
                                        <i class="icon-<?= ($statusAtiv === 'concluida' || $statusAtiv === 'concluida') ? 'check' : (($statusAtiv === 'em_andamento' || $statusAtiv === 'iniciada') ? 'play' : 'time') ?>"></i>
                                    </div>
                                    <div class="atividade-info">
                                        <?php $ativId = $ativ->id ?? $ativ->idAtividade ?? null; ?>
                                        <h5><?= htmlspecialchars($ativ->titulo ?? $ativ->descricao ?? 'Atividade #' . ($ativId ?? 'N/A')) ?></h5>
                                        <p><?= $statusAtivLabel ?> • <?= !empty($ativ->hora_inicio) ? date('H:i', strtotime($ativ->hora_inicio)) : '--:--' ?></p>
                                    </div>
                                    <div class="atividade-acoes">
                                        <?php if ($podeIniciar && $ativId): ?>
                                        <button class="btn-acao iniciar" onclick="WizardAtendimento.iniciar(<?= $etapaId ?>, '<?= htmlspecialchars($etapa->nome ?? 'Etapa') ?>', <?= $ativId ?>, '<?= htmlspecialchars($ativ->titulo ?? $ativ->descricao ?? 'Atividade') ?>')">
                                            <i class="icon-play"></i> Iniciar
                                        </button>
                                        <?php elseif (($statusAtiv === 'em_andamento' || $statusAtiv === 'iniciada') && $ativId): ?>
                                        <button class="btn-acao finalizar" onclick="WizardAtendimento.continuar(<?= $ativId ?>)">
                                            <i class="icon-stop"></i> Finalizar
                                        </button>
                                        <?php else: ?>
                                        <span class="btn-acao detalhes">
                                            <i class="icon-check"></i> <?= $statusAtivLabel ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="empty-state" style="padding: 20px;">
                            <p>Nenhuma atividade cadastrada nesta etapa</p>
                            <?php if (empty($wizard_em_andamento)): ?>
                            <button class="btn-acao iniciar" onclick="WizardAtendimento.iniciar(<?= $etapaId ?>, '<?= htmlspecialchars($etapa->nome ?? 'Etapa') ?>', null, null)" style="margin-top: 10px;">
                                <i class="icon-play"></i> Iniciar Atendimento Geral
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- ================= MODAL WIZARD DE ATENDIMENTO ================= -->
<div id="wizardModal" class="wizard-overlay">
    <div class="wizard-container">

        <!-- CSRF Token para requisições AJAX -->
        <input type="hidden" name="MAPOS_TOKEN" value="<?= $this->security->get_csrf_hash() ?>">

        <!-- Header -->
        <div class="wizard-header">
            <button class="btn-fechar-wizard" onclick="WizardAtendimento.fechar()">
                <i class="icon-remove"></i>
            </button>
            <h2 id="wizardTitulo"><i class="icon-walk"></i> Iniciar Atendimento</h2>
            <p id="wizardSubtitulo">Selecione a etapa e atividade para iniciar</p>
        </div>

        <!-- Steps -->
        <div class="wizard-steps" id="wizardSteps">
            <div class="wizard-step active" data-step="1">
                <div class="wizard-step-icon"><i class="icon-list"></i></div>
                <div class="wizard-step-label">Etapa</div>
            </div>
            <div class="wizard-step" data-step="2">
                <div class="wizard-step-icon"><i class="icon-tasks"></i></div>
                <div class="wizard-step-label">Atividade</div>
            </div>
            <div class="wizard-step" data-step="3">
                <div class="wizard-step-icon"><i class="icon-signin"></i></div>
                <div class="wizard-step-label">Check-in</div>
            </div>
            <div class="wizard-step" data-step="4">
                <div class="wizard-step-icon"><i class="icon-signout"></i></div>
                <div class="wizard-step-label">Check-out</div>
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="wizard-body" id="wizardBody">

            <!-- STEP 1: SELECIONAR ETAPA -->
            <div id="step1" class="wizard-step-content">
                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-list"></i> Selecione a Etapa
                    </div>
                    <div class="etapas-grid" id="etapasGrid">
                        <?php
                        // Ensure $etapas is iterable
                        if (!is_array($etapas) && !is_object($etapas)) {
                            $etapas = [];
                        }
                        foreach ($etapas as $etapa):
                            $etapaId = property_exists($etapa, 'id') ? $etapa->id : (property_exists($etapa, 'idEtapa') ? $etapa->idEtapa : null);
                            if (!$etapaId) continue;
                        ?>
                        <div class="etapa-selecao" data-etapa-id="<?= $etapaId ?>" onclick="WizardAtendimento.selecionarEtapa(this)">
                            <i class="icon-hard-hat"></i>
                            <h4><?= htmlspecialchars($etapa->nome ?? 'Etapa') ?></h4>
                            <p><?= $etapa->percentual_concluido ?? 0 ?>% concluído</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="etapaSelecionadaId" value="">
                    <input type="hidden" id="etapaSelecionadaNome" value="">
                </div>

                <button class="wizard-btn-principal" id="btnAvancarEtapa" onclick="WizardAtendimento.avancarParaAtividade()" disabled>
                    <i class="icon-arrow-right"></i> AVANÇAR PARA ATIVIDADE
                </button>
            </div>

            <!-- STEP 2: SELECIONAR ATIVIDADE -->
            <div id="step2" class="wizard-step-content" style="display: none;">
                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-tasks"></i> Selecione a Atividade
                    </div>
                    <div class="atividades-grid" id="atividadesGrid">
                        <!-- Preenchido via JS -->
                    </div>
                    <input type="hidden" id="atividadeSelecionadaId" value="">
                    <input type="hidden" id="atividadeSelecionadaNome" value="">
                </div>

                <button class="wizard-btn-principal" id="btnAvancarAtividade" onclick="WizardAtendimento.avancarParaCheckin()" disabled>
                    <i class="icon-arrow-right"></i> AVANÇAR PARA CHECK-IN
                </button>
            </div>

            <!-- STEP 3: CHECK-IN -->
            <div id="step3" class="wizard-step-content" style="display: none;">
                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-signin"></i> Check-in de Entrada
                    </div>

                    <!-- Opções -->
                    <div class="opcoes-atividade">
                        <label class="opcao-item normal" onclick="WizardAtendimento.selecionarTipoExecucao('normal')">
                            <input type="radio" name="tipoExecucao" value="normal" checked>
                            <i class="icon-play"></i>
                            <div class="opcao-info">
                                <h5>Iniciar Atividade Normalmente</h5>
                                <p>Registro de hora e localização de entrada</p>
                            </div>
                        </label>

                        <label class="opcao-item impedimento" onclick="WizardAtendimento.selecionarTipoExecucao('impedimento')">
                            <input type="radio" name="tipoExecucao" value="impedimento">
                            <i class="icon-exclamation-sign"></i>
                            <div class="opcao-info">
                                <h5>Não é Possível Realizar</h5>
                                <p>Registrar impedimento com justificativa</p>
                            </div>
                        </label>
                    </div>

                    <!-- Justificativa (mostrar se impedimento) -->
                    <div id="boxJustificativa" class="justificativa-box" style="display: none;">
                        <h4><i class="icon-exclamation-sign"></i> Justificativa do Impedimento</h4>
                        <textarea id="justificativaTexto" class="wizard-input" rows="3" placeholder="Descreva o motivo pelo qual não é possível realizar esta atividade..."></textarea>
                    </div>

                    <!-- Foto Opcional -->
                    <div style="margin-top: 20px;">
                        <div class="wizard-card-titulo">
                            <i class="icon-camera"></i> Foto de Registro (Opcional)
                        </div>
                        <div class="foto-upload" onclick="document.getElementById('fotoCheckin').click()">
                            <i class="icon-camera"></i>
                            <h4>Clique para adicionar foto</h4>
                            <p>Registre o estado inicial do local</p>
                            <input type="file" id="fotoCheckin" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFoto(this, 'previewCheckin')">
                        </div>
                        <img id="previewCheckin" class="foto-preview" style="display: none;">
                    </div>

                    <!-- Info de Localização -->
                    <div style="margin-top: 20px; background: #e3f2fd; border-radius: 10px; padding: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="icon-map-marker" style="font-size: 20px; color: #3498db;"></i>
                        <div>
                            <strong style="font-size: 13px; color: #333;">Localização será registrada automaticamente</strong>
                            <p style="margin: 0; font-size: 12px; color: #666;">Hora de entrada: <span id="horaEntrada">--:--</span></p>
                        </div>
                    </div>
                </div>

                <button class="wizard-btn-principal" onclick="WizardAtendimento.realizarCheckin()">
                    <i class="icon-signin"></i> CONFIRMAR ENTRADA
                </button>
            </div>

            <!-- STEP 4: CHECK-OUT / EXECUÇÃO -->
            <div id="step4" class="wizard-step-content" style="display: none;">
                <!-- Timer -->
                <div class="wizard-timer">
                    <div class="timer-tempo" id="timerExecucao">00:00:00</div>
                    <div class="timer-label"><i class="icon-time"></i> Tempo de Execução</div>
                </div>

                <!-- Info -->
                <div class="info-execucao">
                    <h4>Atividade em Execução</h4>
                    <p id="infoAtividadeExecucao">--</p>
                </div>

                <!-- Ações durante execução -->
                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-cogs"></i> Ações Durante a Execução
                    </div>
                    <div class="wizard-acoes">
                        <div class="wizard-acao registrar" onclick="WizardAtendimento.registrarProgresso()">
                            <i class="icon-edit"></i>
                            <strong>Registrar</strong>
                            <small>Anotações</small>
                        </div>
                        <div class="wizard-acao foto" onclick="WizardAtendimento.adicionarFoto()">
                            <i class="icon-camera"></i>
                            <strong>Foto</strong>
                            <small>Documentar</small>
                        </div>
                        <div class="wizard-acao pausar" onclick="WizardAtendimento.pausarExecucao()">
                            <i class="icon-pause"></i>
                            <strong>Pausar</strong>
                            <small>Interromper</small>
                        </div>
                        <div class="wizard-acao finalizar" onclick="WizardAtendimento.avancarParaCheckout()">
                            <i class="icon-stop"></i>
                            <strong>Finalizar</strong>
                            <small>Encerrar</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 5: CHECKOUT FINAL -->
            <div id="step5" class="wizard-step-content" style="display: none;">
                <!-- Resumo -->
                <div class="resumo-box">
                    <div class="resumo-item">
                        <span>Início:</span>
                        <strong id="checkoutHoraInicio">--:--</strong>
                    </div>
                    <div class="resumo-item">
                        <span>Tempo Total:</span>
                        <strong class="tempo" id="checkoutTempoTotal">00:00</strong>
                    </div>
                </div>

                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-check"></i> Marcar Atividades
                    </div>
                    <div class="checkout-atividades" id="checkoutAtividades">
                        <!-- Preenchido via JS -->
                    </div>
                </div>

                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-camera"></i> Fotos de Registro
                    </div>
                    <div class="foto-upload" onclick="document.getElementById('fotoCheckout').click()">
                        <i class="icon-camera"></i>
                        <h4>Adicionar foto de saída</h4>
                        <p>Documente o trabalho realizado</p>
                        <input type="file" id="fotoCheckout" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFoto(this, 'previewCheckout')">
                    </div>
                    <img id="previewCheckout" class="foto-preview" style="display: none;">

                    <!-- Galeria de fotos já tiradas -->
                    <div id="galeriaFotos" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 15px;">
                        <!-- Preenchido via JS -->
                    </div>
                </div>

                <div class="wizard-card">
                    <div class="wizard-card-titulo">
                        <i class="icon-edit"></i> Observações Finais
                    </div>
                    <textarea id="observacoesCheckout" class="wizard-input" rows="3" placeholder="Descreva o que foi realizado, pendências ou observações importantes..."></textarea>
                </div>

                <button class="wizard-btn-principal" onclick="WizardAtendimento.realizarCheckout()">
                    <i class="icon-signout"></i> FINALIZAR ATENDIMENTO
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal para Registrar Progresso -->
<div id="modalProgresso" class="wizard-overlay" style="z-index: 10000;">
    <div class="wizard-container" style="justify-content: center;">
        <div class="wizard-card" style="max-width: 500px; margin: 0 auto;">
            <div class="wizard-card-titulo">
                <i class="icon-edit"></i> Registrar Progresso
            </div>
            <textarea id="textoProgresso" class="wizard-input" rows="5" placeholder="Descreva o progresso realizado..."></textarea>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button class="wizard-btn-principal" style="background: #95a5a6; flex: 1;" onclick="document.getElementById('modalProgresso').style.display='none'">
                    Cancelar
                </button>
                <button class="wizard-btn-principal" style="flex: 1;" onclick="WizardAtendimento.salvarProgresso()">
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Dados da obra e etapas
const dadosObra = {
    obraId: <?= json_encode($obra->id ?? null) ?>,
    etapas: <?= json_encode($etapas ?? []) ?>,
    atividadesPorEtapa: <?= json_encode($atividades_por_etapa ?? []) ?>,
    atividadeAndamento: <?= json_encode($wizard_em_andamento) ?>
};

// Wizard de Atendimento
const WizardAtendimento = {
    stepAtual: 1,
    etapaSelecionada: null,
    atividadeSelecionada: null,
    tipoExecucao: 'normal',
    horaInicio: null,
    timerInterval: null,
    fotosRegistradas: [],
    atividadesConcluidas: [],

    // Obter token CSRF
    getCsrfToken: function() {
        const tokenEl = document.querySelector('input[name="MAPOS_TOKEN"]');
        return tokenEl ? tokenEl.value : '';
    },

    // Iniciar wizard
    iniciar: function(etapaId = null, etapaNome = null, atividadeId = null, atividadeNome = null) {
        this.stepAtual = 1;
        this.etapaSelecionada = etapaId ? { id: etapaId, nome: etapaNome } : null;
        this.atividadeSelecionada = atividadeId ? { id: atividadeId, nome: atividadeNome } : null;

        // Abrir modal
        var modal = document.getElementById('wizardModal');
        if (modal) {
            modal.style.display = 'block';
        }
        document.body.style.overflow = 'hidden';

        // Se já tem etapa selecionada, marcar e avançar
        if (this.etapaSelecionada) {
            // Marcar etapa selecionada visualmente
            document.querySelectorAll('.etapa-selecao').forEach(el => {
                el.classList.remove('selecionada');
                if (parseInt(el.dataset.etapaId) === etapaId) {
                    el.classList.add('selecionada');
                }
            });

            var etapaIdInput = document.getElementById('etapaSelecionadaId');
            var etapaNomeInput = document.getElementById('etapaSelecionadaNome');
            var btnAvancar = document.getElementById('btnAvancarEtapa');

            if (etapaIdInput) etapaIdInput.value = etapaId;
            if (etapaNomeInput) etapaNomeInput.value = etapaNome;
            if (btnAvancar) btnAvancar.disabled = false;

            // Se tem atividade específica, ir direto para checkin
            // Se não tem atividade, mostrar seleção de atividades da etapa
            if (this.atividadeSelecionada) {
                this.atividadeSelecionada = { id: atividadeId, nome: atividadeNome };
                this.avancarParaCheckin();
            } else {
                this.avancarParaAtividade();
            }
        } else {
            this.mostrarStep(1);
        }

        this.atualizarSteps();
    },

    // Continuar atividade em andamento
    continuar: function(atividadeId) {
        this.stepAtual = 4;

        // Abrir modal
        var modal = document.getElementById('wizardModal');
        if (modal) {
            modal.style.display = 'block';
        }
        document.body.style.overflow = 'hidden';

        // Atualizar título
        var titulo = document.getElementById('wizardTitulo');
        var subtitulo = document.getElementById('wizardSubtitulo');
        if (titulo) titulo.innerHTML = '<i class="icon-play"></i> Execução em Andamento';
        if (subtitulo) subtitulo.textContent = 'Finalize ou registre o progresso da atividade';

        // Preencher info da atividade em andamento
        const atividade = dadosObra.atividadeAndamento;
        if (atividade) {
            this.horaInicio = new Date(atividade.hora_inicio);
            this.etapaSelecionada = {
                id: atividade.etapa_id || 0,
                nome: atividade.etapa_nome || 'Etapa'
            };
            this.atividadeSelecionada = {
                id: atividade.id || atividadeId,
                nome: atividade.titulo || atividade.descricao || 'Atividade'
            };

            var infoAtividade = document.getElementById('infoAtividadeExecucao');
            if (infoAtividade) {
                infoAtividade.textContent = this.etapaSelecionada.nome + ' - ' + this.atividadeSelecionada.nome;
            }
            this.iniciarTimer();
        }

        this.mostrarStep(4);
        this.atualizarSteps();
    },

    // Fechar wizard
    fechar: function() {
        var modal = document.getElementById('wizardModal');
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = '';
        this.pararTimer();
        this.resetarWizard();
    },

    // Resetar wizard
    resetarWizard: function() {
        this.stepAtual = 1;
        this.etapaSelecionada = null;
        this.atividadeSelecionada = null;
        this.tipoExecucao = 'normal';
        this.horaInicio = null;
        this.fotosRegistradas = [];
        this.atividadesConcluidas = [];

        // Limpar seleções
        document.querySelectorAll('.etapa-selecao, .atividade-selecao').forEach(el => {
            if (el) el.classList.remove('selecionada');
        });
        document.querySelectorAll('input[type="hidden"]').forEach(el => {
            if (el && el.id !== 'etapaSelecionadaId' && el.id !== 'atividadeSelecionadaId') el.value = '';
        });
        document.querySelectorAll('textarea').forEach(el => {
            if (el) el.value = '';
        });
        document.querySelectorAll('.foto-preview').forEach(el => {
            if (el) el.style.display = 'none';
        });
        var boxJust = document.getElementById('boxJustificativa');
        if (boxJust) boxJust.style.display = 'none';
        document.querySelectorAll('input[type="radio"][name="tipoExecucao"]').forEach(el => {
            if (el) el.checked = el.value === 'normal';
        });
    },

    // Mostrar step
    mostrarStep: function(step) {
        // Esconder todos os steps
        document.querySelectorAll('.wizard-step-content').forEach(el => {
            if (el) el.style.display = 'none';
        });
        // Mostrar o step atual
        var stepEl = document.getElementById('step' + step);
        if (stepEl) {
            stepEl.style.display = 'block';
        }
        this.stepAtual = step;
        this.atualizarSteps();
    },

    // Atualizar indicadores de step
    atualizarSteps: function() {
        document.querySelectorAll('.wizard-step').forEach((el, index) => {
            if (!el) return;
            const stepNum = index + 1;
            el.classList.remove('active', 'completed');
            if (stepNum < this.stepAtual) {
                el.classList.add('completed');
            } else if (stepNum === this.stepAtual) {
                el.classList.add('active');
            }
        });
    },

    // Selecionar etapa
    selecionarEtapa: function(elemento) {
        document.querySelectorAll('.etapa-selecao').forEach(el => {
            if (el) el.classList.remove('selecionada');
        });
        elemento.classList.add('selecionada');

        var h4 = elemento.querySelector('h4');
        this.etapaSelecionada = {
            id: parseInt(elemento.dataset.etapaId),
            nome: h4 ? h4.textContent : 'Etapa'
        };

        var etapaIdInput = document.getElementById('etapaSelecionadaId');
        var etapaNomeInput = document.getElementById('etapaSelecionadaNome');
        var btnAvancar = document.getElementById('btnAvancarEtapa');

        if (etapaIdInput) etapaIdInput.value = this.etapaSelecionada.id;
        if (etapaNomeInput) etapaNomeInput.value = this.etapaSelecionada.nome;
        if (btnAvancar) btnAvancar.disabled = false;
    },

    // Avançar para seleção de atividade
    avancarParaAtividade: function() {
        if (!this.etapaSelecionada) return;

        // Carregar atividades da etapa
        const atividades = dadosObra.atividadesPorEtapa[this.etapaSelecionada.id] || [];
        const grid = document.getElementById('atividadesGrid');

        // Filtrar apenas atividades que podem ser iniciadas (não concluídas)
        const atividadesDisponiveis = atividades.filter(atv => {
            const status = atv.status || 'agendada';
            return status !== 'concluida' && status !== 'concluida';
        });

        if (atividadesDisponiveis.length === 0) {
            // Se não tem atividades disponíveis, criar opção de atividade geral
            grid.innerHTML = `
                <div class="atividade-selecao" data-atividade-id="0" onclick="WizardAtendimento.selecionarAtividade(this)">
                    <i class="icon-tasks"></i>
                    <div class="atividade-selecao-info">
                        <h5>Atividade Geral na Etapa</h5>
                        <p>Execução de trabalhos diversos em ${this.etapaSelecionada.nome}</p>
                    </div>
                </div>
            `;
        } else {
            grid.innerHTML = atividadesDisponiveis.map(atv => {
                const status = atv.status || 'agendada';
                const statusLabel = (status === 'concluida' || status === 'concluida') ? 'Concluída' :
                                     ((status === 'em_andamento' || status === 'iniciada') ? 'Em Andamento' : 'Aberta');
                return `
                <div class="atividade-selecao" data-atividade-id="${atv.id}" onclick="WizardAtendimento.selecionarAtividade(this)">
                    <i class="icon-${(status === 'concluida' || status === 'concluida') ? 'check' : ((status === 'em_andamento' || status === 'iniciada') ? 'play' : 'time')}"></i>
                    <div class="atividade-selecao-info">
                        <h5>${atv.titulo || atv.descricao || 'Atividade #' + atv.id}</h5>
                        <p>${statusLabel}</p>
                    </div>
                </div>
            `}).join('');
        }

        this.mostrarStep(2);
    },

    // Selecionar atividade
    selecionarAtividade: function(elemento) {
        document.querySelectorAll('.atividade-selecao').forEach(el => el.classList.remove('selecionada'));
        elemento.classList.add('selecionada');

        this.atividadeSelecionada = {
            id: parseInt(elemento.dataset.atividadeId),
            nome: elemento.querySelector('h5').textContent
        };

        var atividadeIdInput = document.getElementById('atividadeSelecionadaId');
        var atividadeNomeInput = document.getElementById('atividadeSelecionadaNome');
        var btnAvancar = document.getElementById('btnAvancarAtividade');

        if (atividadeIdInput) atividadeIdInput.value = this.atividadeSelecionada.id;
        if (atividadeNomeInput) atividadeNomeInput.value = this.atividadeSelecionada.nome;
        if (btnAvancar) btnAvancar.disabled = false;
    },

    // Avançar para check-in
    avancarParaCheckin: function() {
        // Se não tem atividade selecionada (veio de atividade geral), marcar a primeira disponível ou criar uma
        if (!this.atividadeSelecionada) {
            const atividades = dadosObra.atividadesPorEtapa[this.etapaSelecionada?.id] || [];
            const atividadesDisponiveis = atividades.filter(atv => {
                const status = atv.status || 'agendada';
                return status !== 'concluida' && status !== 'concluida';
            });

            if (atividadesDisponiveis.length > 0) {
                this.atividadeSelecionada = {
                    id: atividadesDisponiveis[0].id,
                    nome: atividadesDisponiveis[0].titulo || atividadesDisponiveis[0].descricao || 'Atividade'
                };
            } else {
                // Atividade geral - ID 0
                this.atividadeSelecionada = {
                    id: 0,
                    nome: 'Atividade Geral'
                };
            }
        }

        var horaEntrada = document.getElementById('horaEntrada');
        if (horaEntrada) {
            horaEntrada.textContent = new Date().toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
        }
        this.mostrarStep(3);
    },

    // Selecionar tipo de execução
    selecionarTipoExecucao: function(tipo) {
        this.tipoExecucao = tipo;
        var boxJust = document.getElementById('boxJustificativa');
        if (boxJust) {
            boxJust.style.display = tipo === 'impedimento' ? 'block' : 'none';
        }
    },

    // Preview de foto
    previewFoto: function(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    },

    // Realizar check-in
    realizarCheckin: function() {
        if (this.tipoExecucao === 'impedimento') {
            const justificativa = document.getElementById('justificativaTexto').value;
            if (!justificativa.trim()) {
                alert('Por favor, informe a justificativa do impedimento.');
                return;
            }
        }

        // Obter localização
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.enviarCheckin(position.coords.latitude, position.coords.longitude);
                },
                (error) => {
                    console.warn('Erro ao obter localização:', error);
                    this.enviarCheckin(null, null);
                }
            );
        } else {
            this.enviarCheckin(null, null);
        }
    },

    // Enviar check-in para o servidor
    enviarCheckin: function(lat, lng) {
        const self = this;
        const csrfToken = this.getCsrfToken();

        // Primeiro testar se o controller responde
        const testFormData = new FormData();
        testFormData.append('MAPOS_TOKEN', csrfToken);
        testFormData.append('test', '1');

        fetch('<?= site_url("atividades/teste_ajax") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: testFormData
        })
        .then(r => {
            if (!r.ok) throw new Error('HTTP error: ' + r.status);
            return r.text();
        })
        .then(text => {
            try {
                const testData = JSON.parse(text);
                console.log('Teste AJAX:', testData);
                if (testData.success) {
                    self._enviarCheckinReal(lat, lng);
                } else {
                    alert('Erro de conexão: ' + (testData.message || 'Desconhecido'));
                }
            } catch (e) {
                console.error('Resposta não é JSON:', text.substring(0, 500));
                alert('Erro: servidor retornou resposta inválida.');
            }
        })
        .catch(err => {
            console.error('Erro no teste AJAX:', err);
            alert('Erro ao conectar com o servidor. Verifique sua conexão.');
        });
    },

    // Enviar check-in real para o servidor
    _enviarCheckinReal: function(lat, lng) {
        const csrfToken = this.getCsrfToken();
        const formData = new FormData();
        formData.append('MAPOS_TOKEN', csrfToken);
        formData.append('obra_id', dadosObra.obraId);
        formData.append('etapa_id', this.etapaSelecionada.id);
        formData.append('atividade_id', this.atividadeSelecionada.id || 0);
        formData.append('tipo_execucao', this.tipoExecucao);
        formData.append('justificativa', document.getElementById('justificativaTexto').value || '');
        formData.append('latitude', lat || '');
        formData.append('longitude', lng || '');

        const fotoInput = document.getElementById('fotoCheckin');
        if (fotoInput.files.length > 0) {
            formData.append('foto', fotoInput.files[0]);
        }

        fetch('<?= site_url("atividades/checkin_obra") ?>', {
            method: 'POST',
            body: formData
        })
        .then(r => {
            // Verificar se a resposta é JSON
            const contentType = r.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return r.json();
            } else {
                // Se não for JSON, mostrar o erro
                return r.text().then(text => {
                    console.error('Resposta não-JSON:', text.substring(0, 500));
                    throw new Error('Resposta do servidor não é JSON válido');
                });
            }
        })
        .then(data => {
            if (data.success) {
                this.horaInicio = new Date();

                if (this.tipoExecucao === 'impedimento') {
                    alert('Impedimento registrado com sucesso!');
                    this.fechar();
                    location.reload();
                } else {
                    // Ir para execução
                    var tituloEl = document.getElementById('wizardTitulo');
                    var subtituloEl = document.getElementById('wizardSubtitulo');
                    var infoEl = document.getElementById('infoAtividadeExecucao');

                    if (tituloEl) tituloEl.innerHTML = '<i class="icon-play"></i> Execução em Andamento';
                    if (subtituloEl) subtituloEl.textContent = 'Atividade iniciada com sucesso';
                    if (infoEl) {
                        var nomeEtapa = this.etapaSelecionada ? this.etapaSelecionada.nome : 'Etapa';
                        var nomeAtividade = this.atividadeSelecionada ? this.atividadeSelecionada.nome : 'Atividade';
                        infoEl.textContent = nomeEtapa + ' - ' + nomeAtividade;
                    }

                    this.mostrarStep(4);
                    this.iniciarTimer();
                }
            } else {
                alert('Erro: ' + (data.message || 'Erro ao iniciar atividade'));
            }
        })
        .catch(err => {
            console.error('Erro:', err);
            alert('Erro ao comunicar com o servidor. Verifique o console para mais detalhes.');
        });
    },

    // Iniciar timer
    iniciarTimer: function() {
        var self = this;
        this.timerInterval = setInterval(function() {
            if (!self.horaInicio) return;
            const agora = new Date();
            const diff = agora - self.horaInicio;

            const horas = Math.floor(diff / 3600000);
            const minutos = Math.floor((diff % 3600000) / 60000);
            const segundos = Math.floor((diff % 60000) / 1000);

            var timerEl = document.getElementById('timerExecucao');
            if (timerEl) {
                timerEl.textContent =
                    String(horas).padStart(2, '0') + ':' +
                    String(minutos).padStart(2, '0') + ':' +
                    String(segundos).padStart(2, '0');
            }
        }, 1000);
    },

    // Parar timer
    pararTimer: function() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    },

    // Registrar progresso
    registrarProgresso: function() {
        var modal = document.getElementById('modalProgresso');
        if (modal) modal.style.display = 'block';
    },

    // Salvar progresso
    salvarProgresso: function() {
        var textoEl = document.getElementById('textoProgresso');
        if (!textoEl) return;

        const texto = textoEl.value;
        if (!texto.trim()) {
            alert('Por favor, descreva o progresso.');
            return;
        }

        // Enviar para o servidor
        const csrfToken = this.getCsrfToken();
        const formData = new FormData();
        formData.append('MAPOS_TOKEN', csrfToken);
        formData.append('obra_id', dadosObra.obraId);
        formData.append('atividade_id', this.atividadeSelecionada ? this.atividadeSelecionada.id : (dadosObra.atividadeAndamento ? dadosObra.atividadeAndamento.id : 0));
        formData.append('observacao', texto);

        fetch('<?= site_url("atividades/registrar_observacao") ?>', {
            method: 'POST',
            body: formData
        })
        .then(r => {
            const contentType = r.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return r.json();
            } else {
                return r.text().then(text => {
                    console.error('Resposta não-JSON:', text.substring(0, 500));
                    throw new Error('Resposta do servidor não é JSON válido');
                });
            }
        })
        .then(data => {
            if (data.success) {
                alert('Progresso registrado com sucesso!');
                var modal = document.getElementById('modalProgresso');
                if (modal) modal.style.display = 'none';
                textoEl.value = '';
            } else {
                alert('Erro ao registrar progresso: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(err => {
            console.error('Erro:', err);
            alert('Erro ao salvar. Verifique o console para mais detalhes.');
        });
    },

    // Adicionar foto durante execução
    adicionarFoto: function() {
        var fotoInput = document.getElementById('fotoCheckout');
        if (fotoInput) fotoInput.click();
    },

    // Pausar execução
    pausarExecucao: function() {
        if (!confirm('Deseja pausar a execução desta atividade?')) return;

        this.pararTimer();

        const csrfToken = this.getCsrfToken();
        const formData = new FormData();
        formData.append('MAPOS_TOKEN', csrfToken);
        formData.append('obra_id', dadosObra.obraId);
        formData.append('atividade_id', this.atividadeSelecionada ? this.atividadeSelecionada.id :
                       (dadosObra.atividadeAndamento ? dadosObra.atividadeAndamento.id : 0));

        fetch('<?= site_url("atividades/pausar") ?>', {
            method: 'POST',
            body: formData
        })
        .then(r => {
            const contentType = r.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return r.json();
            } else {
                return r.text().then(text => {
                    console.error('Resposta não-JSON:', text.substring(0, 500));
                    throw new Error('Resposta do servidor não é JSON válido');
                });
            }
        })
        .then(data => {
            if (data.success) {
                alert('Atividade pausada!');
                this.fechar();
                location.reload();
            } else {
                alert('Erro ao pausar: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(err => {
            console.error('Erro:', err);
            alert('Erro ao pausar. Verifique o console para mais detalhes.');
        });
    },

    // Avançar para checkout
    avancarParaCheckout: function() {
        // Preencher resumo
        var horaInicioEl = document.getElementById('checkoutHoraInicio');
        var tempoTotalEl = document.getElementById('checkoutTempoTotal');

        if (this.horaInicio) {
            if (horaInicioEl) {
                horaInicioEl.textContent = this.horaInicio.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            }

            const agora = new Date();
            const diff = agora - this.horaInicio;
            const horas = Math.floor(diff / 3600000);
            const minutos = Math.floor((diff % 3600000) / 60000);

            if (tempoTotalEl) {
                tempoTotalEl.textContent = String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0');
            }
        } else {
            if (horaInicioEl) horaInicioEl.textContent = '--:--';
            if (tempoTotalEl) tempoTotalEl.textContent = '00:00';
        }

        // Carregar atividades para marcar
        const lista = document.getElementById('checkoutAtividades');
        const atividades = dadosObra.atividadesPorEtapa[this.etapaSelecionada?.id] || [];

        if (lista) {
            if (atividades.length === 0) {
                lista.innerHTML = `
                    <div class="empty-state" style="padding: 20px;">
                        <p>Nenhuma atividade específica para marcar</p>
                    </div>
                `;
            } else {
                lista.innerHTML = atividades.map(atv => `
                    <div class="checkout-item">
                        <input type="checkbox" id="chk_${atv.id}" checked onchange="WizardAtendimento.atualizarStatusAtividade(${atv.id})">
                        <div class="checkout-item-info">
                            <h5>${atv.titulo || atv.descricao || 'Atividade #' + atv.id}</h5>
                            <p>Marcar como concluída</p>
                        </div>
                        <select class="status-select" id="status_${atv.id}" onchange="WizardAtendimento.atualizarStatusAtividade(${atv.id})">
                            <option value="concluida">Concluída</option>
                            <option value="pendente">Pendente</option>
                            <option value="nao_realizada">Não Realizada</option>
                        </select>
                    </div>
                `).join('');
            }
        }

        this.mostrarStep(5);
    },

    // Atualizar status da atividade
    atualizarStatusAtividade: function(atividadeId) {
        var checkbox = document.getElementById('chk_' + atividadeId);
        var select = document.getElementById('status_' + atividadeId);

        if (!checkbox || !select) return;

        // Sincronizar checkbox com select
        if (select.value === 'concluida') {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    },

    // Realizar checkout final
    realizarCheckout: function() {
        // Obter localização
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.enviarCheckout(position.coords.latitude, position.coords.longitude);
                },
                (error) => {
                    console.warn('Erro ao obter localização:', error);
                    this.enviarCheckout(null, null);
                }
            );
        } else {
            this.enviarCheckout(null, null);
        }
    },

    // Enviar checkout
    enviarCheckout: function(lat, lng) {
        // Coletar status das atividades
        const statusAtividades = [];
        document.querySelectorAll('.checkout-item').forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox) {
                const id = checkbox.id.replace('chk_', '');
                const select = document.getElementById('status_' + id);
                if (select) {
                    statusAtividades.push({ id: id, status: select.value });
                }
            }
        });

        // Pegar observações
        var obsEl = document.getElementById('observacoesCheckout');
        var observacoes = obsEl ? obsEl.value : '';

        // Pegar atividade ID
        var atividadeId = this.atividadeSelecionada ? this.atividadeSelecionada.id :
                           (dadosObra.atividadeAndamento ? dadosObra.atividadeAndamento.id : 0);

        const csrfToken = this.getCsrfToken();
        const formData = new FormData();
        formData.append('MAPOS_TOKEN', csrfToken);
        formData.append('obra_id', dadosObra.obraId);
        formData.append('atividade_id', atividadeId);
        formData.append('status_atividades', JSON.stringify(statusAtividades));
        formData.append('observacoes', observacoes);
        formData.append('latitude', lat || '');
        formData.append('longitude', lng || '');

        const fotoInput = document.getElementById('fotoCheckout');
        if (fotoInput && fotoInput.files.length > 0) {
            formData.append('foto_saida', fotoInput.files[0]);
        }

        fetch('<?= site_url("atividades/checkout_obra") ?>', {
            method: 'POST',
            body: formData
        })
        .then(r => {
            // Verificar se a resposta é JSON
            const contentType = r.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return r.json();
            } else {
                // Se não for JSON, mostrar o erro
                return r.text().then(text => {
                    console.error('Resposta não-JSON:', text.substring(0, 500));
                    throw new Error('Resposta do servidor não é JSON válido');
                });
            }
        })
        .then(data => {
            if (data.success) {
                alert('Atendimento finalizado com sucesso!');
                this.fechar();
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Erro ao finalizar'));
            }
        })
        .catch(err => {
            console.error('Erro:', err);
            alert('Erro ao finalizar. Verifique o console para mais detalhes.');
        });
    }
};

// Animação das barras de progresso ao carregar
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.etapa-barra-preenchida').forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });
});
</script>
