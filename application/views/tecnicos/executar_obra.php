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
.atividade-icon.reaberta {
    background: #ffeaa7;
    color: #d68910;
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
.btn-acao.iniciar {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}
.btn-acao.iniciar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
}
.btn-acao.reabrir {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
}
.btn-acao.reabrir:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
}
.btn-acao:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
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
    gap: 12px;
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
    position: relative;
}
.atividade-selecao:hover {
    border-color: #3498db;
    background: #ebf5fb;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
}
.atividade-selecao.selecionada {
    border-color: #11998e;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}
.atividade-selecao.selecionada::after {
    content: '\2713';
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    width: 28px;
    height: 28px;
    background: white;
    color: #11998e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
}
.atividade-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
    flex-shrink: 0;
}
.atividade-icon i {
    font-size: 20px;
    color: #95a5a6;
}
.atividade-icon.aberta { background: #fff3cd; }
.atividade-icon.aberta i { color: #856404; }
.atividade-icon.andamento { background: #cce5ff; }
.atividade-icon.andamento i { color: #004085; }
.atividade-icon.concluida { background: #d4edda; }
.atividade-icon.concluida i { color: #155724; }
.atividade-icon.reaberta { background: #fff3cd; }
.atividade-icon.reaberta i { color: #856404; }
.atividade-selecao.selecionada .atividade-icon {
    background: rgba(255,255,255,0.2);
}
.atividade-selecao.selecionada .atividade-icon i {
    color: white;
}
.atividade-selecao-info {
    flex: 1;
}
.atividade-selecao-info h5 {
    margin: 0 0 5px 0;
    font-size: 15px;
    font-weight: 600;
    display: flex;
    align-items: center;
}
.atividade-selecao-info p {
    margin: 0;
    font-size: 12px;
    opacity: 0.8;
    color: #666;
}
.atividade-selecao.selecionada .atividade-selecao-info p {
    color: rgba(255,255,255,0.9);
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

/* ===== Botão Remover Foto ===== */
.btn-remover-foto {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 30px;
    height: 30px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
    transition: all 0.2s;
    z-index: 10;
}
.btn-remover-foto:hover {
    background: #c0392b;
    transform: scale(1.1);
}
.btn-remover-foto i {
    font-size: 14px;
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


                        <!-- Lista de Atividades -->
                        <?php if (!empty($atividadesEtapa)): ?>
                        <div class="atividades-lista">
                            <h4>Atividades Cadastradas</h4>
                            <?php foreach ($atividadesEtapa as $ativ): ?>
                                <?php
                                $statusAtiv = $ativ->status ?? 'agendada';
                                $statusAtivClass = ($statusAtiv === 'concluida' || $statusAtiv === 'concluido') ? 'concluida' :
                                                    (($statusAtiv === 'em_andamento' || $statusAtiv === 'iniciada') ? 'andamento' :
                                                    (($statusAtiv === 'reaberta' || $statusAtiv === 'reaberto') ? 'reaberta' : 'aberto'));
                                $statusAtivLabel = ($statusAtiv === 'concluida' || $statusAtiv === 'concluido') ? 'Concluída' :
                                                    (($statusAtiv === 'em_andamento' || $statusAtiv === 'iniciada') ? 'Em Andamento' :
                                                    (($statusAtiv === 'reaberta' || $statusAtiv === 'reaberto') ? 'Reaberta' : 'Aberta'));
                                ?>
                                <div class="atividade-item">
                                    <div class="atividade-icon <?= $statusAtivClass ?>">
                                        <i class="icon-<?=
                                            ($statusAtiv === 'concluida' || $statusAtiv === 'concluido') ? 'check' :
                                            (($statusAtiv === 'em_andamento' || $statusAtiv === 'iniciada') ? 'play' :
                                            (($statusAtiv === 'reaberta' || $statusAtiv === 'reaberto') ? 'refresh' : 'time'))
                                        ?>"></i>
                                    </div>
                                    <div class="atividade-info">
                                        <?php $ativId = $ativ->id ?? $ativ->idAtividade ?? null; ?>
                                        <h5><?= htmlspecialchars($ativ->titulo ?? $ativ->descricao ?? 'Atividade #' . ($ativId ?? 'N/A')) ?></h5>
                                        <p><?= $statusAtivLabel ?> • <?= !empty($ativ->hora_inicio) ? date('H:i', strtotime($ativ->hora_inicio)) : '--:--' ?></p>
                                    </div>
                                    <div class="atividade-acoes">
                                        <?php if (($statusAtiv === 'em_andamento' || $statusAtiv === 'iniciada') && $ativId): ?>
                                        <button class="btn-acao finalizar" onclick="WizardAtendimento.continuar(<?= $ativId ?>)">
                                            <i class="icon-stop"></i> Finalizar
                                        </button>
                                        <?php elseif ($statusAtiv === 'concluida' && $ativId): ?>
                                        <button class="btn-acao reabrir" onclick="reabrirAtividade(<?= $ativId ?>, '<?= htmlspecialchars($ativ->titulo ?? $ativ->descricao ?? 'Atividade #' . $ativId, ENT_QUOTES) ?>')">
                                            <i class="icon-refresh"></i> Reabrir
                                        </button>
                                        <?php elseif (in_array($statusAtiv, ['agendada', 'pendente', 'aberta', 'reaberta', 'reaberto', 'nao_iniciada', 'nao_iniciado']) && $ativId): ?>
                                        <button class="btn-acao iniciar" onclick="WizardAtendimento.iniciarAtividade(<?= $ativId ?>)">
                                            <i class="icon-play"></i> Iniciar
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
                        <div class="foto-upload" id="uploadCheckin" onclick="document.getElementById('fotoCheckin').click()">
                            <i class="icon-camera"></i>
                            <h4>Clique para adicionar foto</h4>
                            <p>Registre o estado inicial do local</p>
                            <input type="file" id="fotoCheckin" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFoto(this, 'previewCheckin', 'uploadCheckin')">
                        </div>
                        <div id="containerPreviewCheckin" style="display: none; position: relative; margin-top: 15px;">
                            <img id="previewCheckin" class="foto-preview" style="width: 100%; max-width: 300px; border-radius: 10px;">
                            <button type="button" class="btn-remover-foto" onclick="WizardAtendimento.removerFoto('fotoCheckin', 'previewCheckin', 'containerPreviewCheckin', 'uploadCheckin')" title="Remover foto">
                                <i class="icon-remove"></i>
                            </button>
                        </div>
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
                    <div class="foto-upload" id="uploadCheckout" onclick="document.getElementById('fotoCheckout').click()">
                        <i class="icon-camera"></i>
                        <h4>Adicionar foto de saída</h4>
                        <p>Documente o trabalho realizado</p>
                        <input type="file" id="fotoCheckout" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFoto(this, 'previewCheckout', 'uploadCheckout')">
                    </div>
                    <div id="containerPreviewCheckout" style="display: none; position: relative; margin-top: 15px;">
                        <img id="previewCheckout" class="foto-preview" style="width: 100%; max-width: 300px; border-radius: 10px;">
                        <button type="button" class="btn-remover-foto" onclick="WizardAtendimento.removerFoto('fotoCheckout', 'previewCheckout', 'containerPreviewCheckout', 'uploadCheckout')" title="Remover foto">
                            <i class="icon-remove"></i>
                        </button>
                    </div>

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

<!-- Modal de Confirmação - Iniciar Atividade -->
<div id="modalConfirmarIniciar" class="wizard-overlay" style="z-index: 10001; display: none;">
    <div class="wizard-container" style="justify-content: center;">
        <div class="confirm-modal-card">
            <div class="confirm-modal-icon">
                <i class="icon-play-circle"></i>
            </div>
            <h3 class="confirm-modal-titulo">Iniciar Atendimento?</h3>
            <p class="confirm-modal-subtitulo">
                Você está prestes a iniciar
            </p>

            <div class="confirm-modal-info-box">
                <div class="confirm-modal-info-item">
                    <i class="icon-hard-hat"></i>
                    <span id="confirmarIniciarEtapa">--</span>
                </div>
                <div class="confirm-modal-divider"></div>
                <div class="confirm-modal-info-item highlight">
                    <i class="icon-tasks"></i>
                    <span id="confirmarIniciarAtividade">--</span>
                </div>
            </div>

            <div class="confirm-modal-botoes">
                <button class="confirm-modal-btn cancelar" onclick="document.getElementById('modalConfirmarIniciar').style.display='none'">
                    <i class="icon-remove"></i> Cancelar
                </button>
                <button class="confirm-modal-btn confirmar" onclick="WizardAtendimento.confirmarIniciar()">
                    <i class="icon-play"></i> Iniciar Agora
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== Modal de Confirmação - Design Aprimorado ===== */
.confirm-modal-card {
    max-width: 480px;
    width: 90%;
    margin: 0 auto;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 20px;
    padding: 35px 30px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(17,153,142,0.1);
    border: 2px solid rgba(17,153,142,0.15);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.confirm-modal-icon {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 8px 25px rgba(17,153,142,0.35);
}

.confirm-modal-icon i {
    font-size: 45px;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.confirm-modal-titulo {
    margin: 0 0 12px 0;
    font-size: 26px;
    font-weight: 700;
    color: #2c3e50;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
    letter-spacing: -0.3px;
}

.confirm-modal-subtitulo {
    color: #5a6c7d;
    font-size: 16px;
    margin: 0 0 25px 0;
    font-weight: 500;
}

.confirm-modal-info-box {
    background: linear-gradient(135deg, #f0f9f7 0%, #e8f5f2 100%);
    border: 2px solid rgba(17,153,142,0.2);
    border-radius: 15px;
    padding: 22px 20px;
    margin-bottom: 25px;
    text-align: left;
    box-shadow: inset 0 2px 4px rgba(255,255,255,0.8), 0 2px 8px rgba(17,153,142,0.08);
}

.confirm-modal-info-item {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 16px;
    color: #34495e;
    font-weight: 600;
    padding: 8px 0;
}

.confirm-modal-info-item i {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    flex-shrink: 0;
    box-shadow: 0 3px 8px rgba(17,153,142,0.25);
}

.confirm-modal-info-item.highlight {
    font-size: 18px;
    color: #11998e;
    font-weight: 700;
}

.confirm-modal-divider {
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, rgba(17,153,142,0.3) 50%, transparent 100%);
    margin: 12px 0;
}

.confirm-modal-botoes {
    display: flex;
    gap: 15px;
}

.confirm-modal-btn {
    flex: 1;
    padding: 16px 20px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.confirm-modal-btn.cancelar {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(127,140,141,0.3);
}

.confirm-modal-btn.cancelar:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(127,140,141,0.4);
}

.confirm-modal-btn.confirmar {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    box-shadow: 0 4px 20px rgba(17,153,142,0.4);
}

.confirm-modal-btn.confirmar:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(17,153,142,0.5);
}

.confirm-modal-btn:active {
    transform: translateY(-1px);
}

/* Responsivo */
@media (max-width: 480px) {
    .confirm-modal-card {
        padding: 25px 20px;
        border-radius: 16px;
    }

    .confirm-modal-icon {
        width: 75px;
        height: 75px;
    }

    .confirm-modal-icon i {
        font-size: 38px;
    }

    .confirm-modal-titulo {
        font-size: 22px;
    }

    .confirm-modal-subtitulo {
        font-size: 14px;
    }

    .confirm-modal-info-item {
        font-size: 15px;
    }

    .confirm-modal-info-item.highlight {
        font-size: 16px;
    }

    .confirm-modal-btn {
        padding: 14px 16px;
        font-size: 15px;
    }
}
</style>

<!-- Modal de Confirmação - Finalizar Atividade -->
<div id="modalConfirmarFinalizar" class="wizard-overlay" style="z-index: 10001; display: none;">
    <div class="wizard-container" style="justify-content: center;">
        <div class="wizard-card" style="max-width: 450px; margin: 0 auto; text-align: center;">
            <div style="font-size: 60px; color: #27ae60; margin-bottom: 20px;">
                <i class="icon-stop-circle"></i>
            </div>

            <h3 style="margin: 0 0 10px 0; font-size: 20px;">Finalizar Atendimento?</h3>

            <p style="color: #666; margin-bottom: 20px;">
                Deseja encerrar a atividade atual e prosseguir para o checkout?
            </p>

            <div class="resumo-box" style="margin-bottom: 20px;">
                <div class="resumo-item">
                    <span>Tempo de execução:</span>
                    <strong class="tempo" id="confirmarFinalizarTempo">00:00</strong>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button class="wizard-btn-principal" style="background: #95a5a6; flex: 1;" onclick="document.getElementById('modalConfirmarFinalizar').style.display='none'">
                    <i class="icon-remove"></i> Continuar
                </button>
                <button class="wizard-btn-principal" style="flex: 1; background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);" onclick="WizardAtendimento.confirmarFinalizar()">
                    <i class="icon-stop"></i> Finalizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação - Pausar Atividade -->
<div id="modalConfirmarPausar" class="wizard-overlay" style="z-index: 10001; display: none;">
    <div class="wizard-container" style="justify-content: center;">
        <div class="wizard-card" style="max-width: 450px; margin: 0 auto; text-align: center;">
            <div style="font-size: 60px; color: #f39c12; margin-bottom: 20px;">
                <i class="icon-pause-circle"></i>
            </div>
            <h3 style="margin: 0 0 10px 0; font-size: 20px;">Pausar Atividade?</h3>
            <p style="color: #666; margin-bottom: 20px;">
                Você está prestes a pausar a atividade atual.<br>
                <strong id="confirmarPausarAtividade">--</strong>
            </p>

            <div class="resumo-box" style="margin-bottom: 20px;">
                <div class="resumo-item">
                    <span>Tempo decorrido:</span>
                    <strong class="tempo" id="confirmarPausarTempo">00:00</strong>
                </div>
            </div>

            <div style="background: #fff3cd; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: left;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #856404;">
                    <i class="icon-edit"></i> Motivo da pausa (opcional):
                </label>
                <textarea id="motivoPausa" class="wizard-input" rows="3" placeholder="Informe o motivo da pausa..."></textarea>
            </div>

            <div style="display: flex; gap: 10px;">
                <button class="wizard-btn-principal" style="background: #95a5a6; flex: 1;" onclick="document.getElementById('modalConfirmarPausar').style.display='none'">
                    <i class="icon-remove"></i> Continuar
                </button>
                <button class="wizard-btn-principal" style="flex: 1; background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);" onclick="WizardAtendimento.confirmarPausar()">
                    <i class="icon-pause"></i> Pausar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação - Registrar Progresso -->
<div id="modalConfirmarProgresso" class="wizard-overlay" style="z-index: 10001; display: none;">
    <div class="wizard-container" style="justify-content: center;">
        <div class="wizard-card" style="max-width: 500px; margin: 0 auto;">
            <div style="font-size: 50px; color: #3498db; margin-bottom: 15px; text-align: center;">
                <i class="icon-edit-sign"></i>
            </div>
            <h3 style="margin: 0 0 10px 0; font-size: 20px; text-align: center;">Registrar Progresso</h3>
            <p style="color: #666; margin-bottom: 20px; text-align: center;">
                Descreva o que foi realizado até o momento:
            </p>

            <div style="background: #e3f2fd; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <i class="icon-tasks" style="color: #3498db;"></i>
                    <span id="confirmarProgressoAtividade" style="font-weight: 600;">--</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: #666;">
                    <i class="icon-time" style="color: #3498db;"></i>
                    <span id="confirmarProgressoTempo">Tempo: 00:00</span>
                </div>
            </div>

            <textarea id="textoProgressoModal" class="wizard-input" rows="4" placeholder="Descreva o progresso realizado, o que foi concluído, dificuldades encontradas..."></textarea>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button class="wizard-btn-principal" style="background: #95a5a6; flex: 1;" onclick="document.getElementById('modalConfirmarProgresso').style.display='none'">
                    <i class="icon-remove"></i> Cancelar
                </button>
                <button class="wizard-btn-principal" style="flex: 1; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);" onclick="WizardAtendimento.confirmarRegistrarProgresso()">
                    <i class="icon-save"></i> Registrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação - Avançar para Checkout -->
<div id="modalConfirmarCheckout" class="wizard-overlay" style="z-index: 10001; display: none;">
    <div class="wizard-container" style="justify-content: center;">
        <div class="wizard-card" style="max-width: 450px; margin: 0 auto; text-align: center;">
            <div style="font-size: 60px; color: #27ae60; margin-bottom: 20px;">
                <i class="icon-signout"></i>
            </div>
            <h3 style="margin: 0 0 10px 0; font-size: 20px;">Finalizar Atividade?</h3>
            <p style="color: #666; margin-bottom: 20px;">
                Você está prestes a encerrar:<br>
                <strong id="confirmarCheckoutAtividade">--</strong>
            </p>

            <div class="resumo-box" style="margin-bottom: 20px;">
                <div class="resumo-item">
                    <span>Início:</span>
                    <strong id="confirmarCheckoutHoraInicio">--:--</strong>
                </div>
                <div class="resumo-item">
                    <span>Tempo Total:</span>
                    <strong class="tempo" id="confirmarCheckoutTempo">00:00</strong>
                </div>
            </div>

            <div style="background: #fff3cd; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: left;">
                <p style="margin: 0; color: #856404; font-size: 13px;">
                    <i class="icon-info-sign"></i> <strong>Atenção:</strong> Ao prosseguir, você será direcionado para o checkout onde poderá:
                </p>
                <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #856404; font-size: 13px;">
                    <li>Marcar a atividade como concluída, pendente ou não realizada</li>
                    <li>Adicionar fotos do trabalho realizado</li>
                    <li>Registrar observações finais</li>
                </ul>
            </div>

            <div style="display: flex; gap: 10px;">
                <button class="wizard-btn-principal" style="background: #95a5a6; flex: 1;" onclick="document.getElementById('modalConfirmarCheckout').style.display='none'">
                    <i class="icon-remove"></i> Voltar
                </button>
                <button class="wizard-btn-principal" style="flex: 1; background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);" onclick="WizardAtendimento.confirmarAvancarCheckout()">
                    <i class="icon-arrow-right"></i> Prosseguir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação - Foto Salva -->
<div id="modalFotoSalva" class="wizard-overlay" style="z-index: 10002; display: none;">
    <div class="wizard-container" style="justify-content: center;">
        <div class="wizard-card" style="max-width: 400px; margin: 0 auto; text-align: center;">
            <div style="font-size: 60px; color: #9b59b6; margin-bottom: 20px;">
                <i class="icon-camera"></i>
            </div>
            <h3 style="margin: 0 0 10px 0; font-size: 20px;">Foto Registrada!</h3>
            <p style="color: #666; margin-bottom: 20px;">
                A foto foi salva com sucesso na atividade.
            </p>

            <!-- Preview da foto salva -->
            <div style="background: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                <img id="previewFotoSalva" src="" alt="Foto salva" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
            </div>

            <div style="background: #e3f2fd; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: left;">
                <p style="margin: 0; color: #1976d2; font-size: 13px;">
                    <i class="icon-info-sign"></i> <strong>Localização registrada:</strong><br>
                    <span id="fotoLocalizacao">--</span>
                </p>
                <p style="margin: 8px 0 0 0; color: #1976d2; font-size: 13px;">
                    <i class="icon-time"></i> <strong>Data/Hora:</strong> <span id="fotoDataHora">--</span>
                </p>
            </div>

            <button class="wizard-btn-principal" onclick="WizardAtendimento.fecharModalFoto()">
                <i class="icon-ok"></i> OK, Entendido
            </button>
        </div>
    </div>
</div>

<!-- Input escondido para upload de foto durante execução -->
<input type="file" id="fotoExecucaoInput" accept="image/*" capture="environment" style="display: none;">

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

    // Dados temporários para confirmação
    _dadosConfirmacao: null,

    // Iniciar wizard - mostra modal de confirmação
    iniciar: function(etapaId = null, etapaNome = null, atividadeId = null, atividadeNome = null) {
        // Guardar dados para confirmação
        this._dadosConfirmacao = {
            etapaId: etapaId,
            etapaNome: etapaNome,
            atividadeId: atividadeId,
            atividadeNome: atividadeNome
        };

        // Atualizar modal de confirmação
        var etapaEl = document.getElementById('confirmarIniciarEtapa');
        var atividadeEl = document.getElementById('confirmarIniciarAtividade');

        if (etapaEl) etapaEl.textContent = etapaNome || 'Etapa Geral';
        if (atividadeEl) atividadeEl.textContent = atividadeNome || 'Atendimento Geral';

        // Abrir modal de confirmação
        var modal = document.getElementById('modalConfirmarIniciar');
        if (modal) modal.style.display = 'block';
    },

    // Confirmar início após modal
    confirmarIniciar: function() {
        // Fechar modal de confirmação
        var modalConfirm = document.getElementById('modalConfirmarIniciar');
        if (modalConfirm) modalConfirm.style.display = 'none';

        var dados = this._dadosConfirmacao;
        if (!dados) return;

        this.stepAtual = 1;
        this.etapaSelecionada = dados.etapaId ? { id: dados.etapaId, nome: dados.etapaNome } : null;
        this.atividadeSelecionada = dados.atividadeId ? { id: dados.atividadeId, nome: dados.atividadeNome } : null;

        // Abrir wizard principal
        var modal = document.getElementById('wizardModal');
        if (modal) modal.style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Se já tem etapa selecionada
        if (this.etapaSelecionada) {
            document.querySelectorAll('.etapa-selecao').forEach(el => {
                el.classList.remove('selecionada');
                if (parseInt(el.dataset.etapaId) === dados.etapaId) {
                    el.classList.add('selecionada');
                }
            });

            var etapaIdInput = document.getElementById('etapaSelecionadaId');
            var etapaNomeInput = document.getElementById('etapaSelecionadaNome');
            var btnAvancar = document.getElementById('btnAvancarEtapa');

            if (etapaIdInput) etapaIdInput.value = dados.etapaId;
            if (etapaNomeInput) etapaNomeInput.value = dados.etapaNome;
            if (btnAvancar) btnAvancar.disabled = false;

            if (this.atividadeSelecionada) {
                this.avancarParaCheckin();
            } else {
                this.avancarParaAtividade();
            }
        } else {
            this.mostrarStep(1);
        }

        this.atualizarSteps();
    },

    // Iniciar atividade específica (para atividades reabertas/pendentes)
    iniciarAtividade: function(atividadeId) {
        // Buscar a atividade nos dados disponíveis
        var atividade = null;
        var etapaId = null;
        var etapaNome = null;

        // Procurar em todas as etapas
        for (var eid in dadosObra.atividadesPorEtapa) {
            var atvs = dadosObra.atividadesPorEtapa[eid] || [];
            for (var i = 0; i < atvs.length; i++) {
                var atv = atvs[i];
                if ((atv.id || atv.idAtividade) == atividadeId) {
                    atividade = atv;
                    etapaId = eid;
                    // Buscar nome da etapa (compatível com ES5)
                    var etapaNome = 'Etapa';
                    for (var j = 0; j < dadosObra.etapas.length; j++) {
                        if (dadosObra.etapas[j].id == eid) {
                            etapaNome = dadosObra.etapas[j].nome || dadosObra.etapas[j].titulo || 'Etapa';
                            break;
                        }
                    }
                    break;
                }
            }
            if (atividade) break;
        }

        if (!atividade) {
            alert('Atividade não encontrada. Recarregue a página e tente novamente.');
            return;
        }

        // Iniciar o wizard com a atividade selecionada
        this.iniciar(etapaId, etapaNome, atividadeId, atividade.titulo || atividade.descricao || 'Atividade');
    },

    // Continuar atividade em andamento - mostra modal de confirmação
    continuar: function(atividadeId) {
        // Guardar ID para confirmação
        this._atividadeFinalizarId = atividadeId;

        // Calcular tempo decorrido
        const atividade = dadosObra.atividadeAndamento;
        var tempoTexto = '00:00';
        if (atividade && atividade.hora_inicio) {
            const inicio = new Date(atividade.hora_inicio);
            const agora = new Date();
            const diff = agora - inicio;
            const horas = Math.floor(diff / 3600000);
            const minutos = Math.floor((diff % 3600000) / 60000);
            tempoTexto = String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0');
        }

        // Atualizar modal de confirmação
        var tempoEl = document.getElementById('confirmarFinalizarTempo');
        if (tempoEl) tempoEl.textContent = tempoTexto;

        // Abrir modal de confirmação
        var modal = document.getElementById('modalConfirmarFinalizar');
        if (modal) modal.style.display = 'block';
    },

    // Confirmar finalização e abrir wizard
    confirmarFinalizar: function() {
        // Fechar modal de confirmação
        var modalConfirm = document.getElementById('modalConfirmarFinalizar');
        if (modalConfirm) modalConfirm.style.display = 'none';

        var atividadeId = this._atividadeFinalizarId;
        this.stepAtual = 4;

        // Abrir wizard
        var modal = document.getElementById('wizardModal');
        if (modal) modal.style.display = 'block';
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

        // Atualizar título com contador
        const tituloStep2 = document.querySelector('#step2 .wizard-card-titulo');
        if (tituloStep2) {
            if (atividadesDisponiveis.length === 0) {
                tituloStep2.innerHTML = '<i class="icon-tasks"></i> Selecione a Atividade (Atividade Geral)';
            } else if (atividadesDisponiveis.length === 1) {
                tituloStep2.innerHTML = '<i class="icon-tasks"></i> Selecione a Atividade (1 disponível)';
            } else {
                tituloStep2.innerHTML = '<i class="icon-tasks"></i> Selecione uma das ' + atividadesDisponiveis.length + ' Atividades';
            }
        }

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
            // Auto-selecionar atividade geral
            setTimeout(() => {
                const atvGeral = grid.querySelector('.atividade-selecao');
                if (atvGeral) this.selecionarAtividade(atvGeral);
            }, 100);
        } else {
            grid.innerHTML = atividadesDisponiveis.map((atv, index) => {
                const status = atv.status || 'agendada';
                const statusLabel = (status === 'concluida' || status === 'concluido') ? 'Concluída' :
                                     ((status === 'em_andamento' || status === 'iniciada') ? 'Em Andamento' :
                                     ((status === 'reaberta' || status === 'reaberto') ? 'Reaberta' : 'Aberta'));
                const statusClass = (status === 'concluida' || status === 'concluido') ? 'concluida' :
                                     ((status === 'em_andamento' || status === 'iniciada') ? 'andamento' :
                                     ((status === 'reaberta' || status === 'reaberto') ? 'reaberta' : 'aberta'));
                const iconName = (status === 'concluida' || status === 'concluido') ? 'check' :
                                  ((status === 'em_andamento' || status === 'iniciada') ? 'play' :
                                  ((status === 'reaberta' || status === 'reaberto') ? 'refresh' : 'time'));
                const isRecomendada = index === 0 ? '<span style="background:#11998e;color:white;font-size:10px;padding:2px 6px;border-radius:10px;margin-left:8px;">RECOMENDADA</span>' : '';
                return `
                <div class="atividade-selecao" data-atividade-id="${atv.id}" data-atividade-index="${index}" onclick="WizardAtendimento.selecionarAtividade(this)">
                    <div class="atividade-icon ${statusClass}">
                        <i class="icon-${iconName}"></i>
                    </div>
                    <div class="atividade-selecao-info">
                        <h5>${atv.titulo || atv.descricao || 'Atividade #' + atv.id}${isRecomendada}</h5>
                        <p>${statusLabel} ${atv.hora_inicio ? '- Início: ' + atv.hora_inicio.substring(0,5) : ''}</p>
                    </div>
                </div>
            `}).join('');
        }

        // Resetar seleção anterior
        this.atividadeSelecionada = null;
        var atividadeIdInput = document.getElementById('atividadeSelecionadaId');
        var atividadeNomeInput = document.getElementById('atividadeSelecionadaNome');
        var btnAvancar = document.getElementById('btnAvancarAtividade');
        if (atividadeIdInput) atividadeIdInput.value = '';
        if (atividadeNomeInput) atividadeNomeInput.value = '';
        if (btnAvancar) btnAvancar.disabled = true;

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
    previewFoto: function(input, previewId, uploadId) {
        if (input.files && input.files[0]) {
            // Validar tamanho (max 5MB)
            if (input.files[0].size > 5 * 1024 * 1024) {
                alert('A foto deve ter no máximo 5MB.');
                input.value = '';
                return;
            }

            // Validar tipo
            if (!input.files[0].type.startsWith('image/')) {
                alert('Por favor, selecione apenas arquivos de imagem.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                const container = document.getElementById('containerPreview' + previewId.replace('preview', ''));
                const upload = document.getElementById(uploadId);

                img.src = e.target.result;

                // Mostrar container do preview e esconder upload
                if (container) container.style.display = 'block';
                if (upload) upload.style.display = 'none';
            };
            reader.onerror = function() {
                alert('Erro ao ler a foto. Tente novamente.');
                input.value = '';
            };
            reader.readAsDataURL(input.files[0]);
        }
    },

    // Remover foto selecionada
    removerFoto: function(inputId, previewId, containerId, uploadId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const container = document.getElementById(containerId);
        const upload = document.getElementById(uploadId);

        // Limpar input file
        if (input) {
            input.value = '';
        }

        // Limpar preview
        if (preview) {
            preview.src = '';
        }

        // Esconder container do preview
        if (container) {
            container.style.display = 'none';
        }

        // Mostrar botão de upload novamente
        if (upload) {
            upload.style.display = 'block';
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
            console.log('Enviando foto:', fotoInput.files[0].name, 'Tamanho:', fotoInput.files[0].size);
            formData.append('foto', fotoInput.files[0]);
        } else {
            console.log('Nenhuma foto selecionada');
        }

        fetch('<?= site_url("atividades/checkin_obra") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
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

    // Registrar progresso - abre modal de confirmação
    registrarProgresso: function() {
        const atividadeEmAndamento = dadosObra.atividadeAndamento;

        // Calcular tempo decorrido
        const inicio = atividadeEmAndamento && atividadeEmAndamento.hora_inicio
            ? new Date(atividadeEmAndamento.hora_inicio)
            : this.horaInicio;
        var tempoTexto = '00:00';
        if (inicio) {
            const agora = new Date();
            const diff = agora - inicio;
            const horas = Math.floor(diff / 3600000);
            const minutos = Math.floor((diff % 3600000) / 60000);
            tempoTexto = String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0');
        }

        // Atualizar modal
        const atividadeEl = document.getElementById('confirmarProgressoAtividade');
        const tempoEl = document.getElementById('confirmarProgressoTempo');

        if (atividadeEl) {
            const nomeAtividade = atividadeEmAndamento
                ? (atividadeEmAndamento.titulo || atividadeEmAndamento.descricao || 'Atividade em andamento')
                : (this.atividadeSelecionada ? this.atividadeSelecionada.nome : 'Atividade');
            atividadeEl.textContent = nomeAtividade;
        }
        if (tempoEl) tempoEl.textContent = 'Tempo: ' + tempoTexto;

        // Limpar campo de texto
        const textoEl = document.getElementById('textoProgressoModal');
        if (textoEl) textoEl.value = '';

        // Abrir modal de confirmação
        var modal = document.getElementById('modalConfirmarProgresso');
        if (modal) modal.style.display = 'block';
    },

    // Confirmar registro de progresso no modal
    confirmarRegistrarProgresso: function() {
        var textoEl = document.getElementById('textoProgressoModal');
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
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
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
                var modal = document.getElementById('modalConfirmarProgresso');
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

    // Salvar progresso (mantido para compatibilidade)
    salvarProgresso: function() {
        this.confirmarRegistrarProgresso();
    },

    // Adicionar foto durante execução
    adicionarFoto: function() {
        var fotoInput = document.getElementById('fotoExecucaoInput');
        if (!fotoInput) return;

        // Limpar seleção anterior
        fotoInput.value = '';

        // Configurar evento change para processar a foto
        fotoInput.onchange = function(e) {
            if (e.target.files && e.target.files[0]) {
                WizardAtendimento.processarFotoExecucao(e.target.files[0]);
            }
        };

        fotoInput.click();
    },

    // Processar e enviar foto de execução
    processarFotoExecucao: function(file) {
        // Validar tamanho (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('A foto deve ter no máximo 5MB.');
            return;
        }

        // Validar tipo
        if (!file.type.startsWith('image/')) {
            alert('Por favor, selecione apenas arquivos de imagem.');
            return;
        }

        const self = this;

        // Criar preview
        const reader = new FileReader();
        reader.onload = function(e) {
            self.enviarFotoExecucao(file, e.target.result);
        };
        reader.readAsDataURL(file);
    },

    // Enviar foto para o servidor
    enviarFotoExecucao: function(file, previewBase64) {
        const csrfToken = this.getCsrfToken();
        const atividadeEmAndamento = dadosObra.atividadeAndamento;

        // Buscar ID da atividade
        const atividadeId = atividadeEmAndamento
            ? (atividadeEmAndamento.id || atividadeEmAndamento.idAtividade)
            : (this.atividadeSelecionada ? this.atividadeSelecionada.id : null);

        if (!atividadeId) {
            alert('Nenhuma atividade em andamento para vincular a foto.');
            return;
        }

        // Obter localização
        const self = this;
        const enviar = function(lat, lng) {
            const formData = new FormData();
            formData.append('MAPOS_TOKEN', csrfToken);
            formData.append('obra_id', dadosObra.obraId);
            formData.append('atividade_id', atividadeId);
            formData.append('tipo_foto', 'execucao');
            formData.append('etapa', 'durante');
            formData.append('foto', file);
            formData.append('latitude', lat || '');
            formData.append('longitude', lng || '');

            // Mostrar loading
            const loading = document.createElement('div');
            loading.id = 'fotoLoading';
            loading.innerHTML = '<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:99999;display:flex;align-items:center;justify-content:center;"><div style="background:white;padding:20px;border-radius:10px;text-align:center;"><i class="icon-spinner icon-spin" style="font-size:30px;color:#9b59b6;"></i><p>Salvando foto...</p></div></div>';
            document.body.appendChild(loading);

            fetch('<?= site_url("atividades/adicionar_foto_obra") ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(function(r) {
                var loading = document.getElementById('fotoLoading');
                if (loading) loading.remove();

                // Tentar obter JSON de qualquer forma
                return r.text().then(function(text) {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Resposta não-JSON:', text.substring(0, 500));
                        throw new Error('Resposta do servidor não é JSON válido');
                    }
                });
            })
            .then(function(data) {
                if (data.success) {
                    // Mostrar modal de confirmação
                    self.mostrarModalFotoSalva(previewBase64, lat, lng);
                } else {
                    alert('Erro ao salvar foto: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(function(err) {
                var loading = document.getElementById('fotoLoading');
                if (loading) loading.remove();
                console.error('Erro:', err);
                alert('Erro ao salvar foto. Verifique o console para mais detalhes.');
            });
        };

        // Obter localização
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    enviar(position.coords.latitude, position.coords.longitude);
                },
                (error) => {
                    console.warn('Erro ao obter localização:', error);
                    enviar(null, null);
                }
            );
        } else {
            enviar(null, null);
        }
    },

    // Mostrar modal de foto salva
    mostrarModalFotoSalva: function(previewBase64, lat, lng) {
        // Atualizar preview
        var previewEl = document.getElementById('previewFotoSalva');
        if (previewEl) {
            previewEl.src = previewBase64;
        }

        // Atualizar localização
        var locEl = document.getElementById('fotoLocalizacao');
        if (locEl) {
            if (lat && lng) {
                locEl.textContent = 'Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6);
            } else {
                locEl.textContent = 'Localização não disponível';
            }
        }

        // Atualizar data/hora
        var dataHoraEl = document.getElementById('fotoDataHora');
        if (dataHoraEl) {
            dataHoraEl.textContent = new Date().toLocaleString('pt-BR');
        }

        // Abrir modal
        var modal = document.getElementById('modalFotoSalva');
        if (modal) {
            modal.style.display = 'block';
        }
    },

    // Fechar modal de foto
    fecharModalFoto: function() {
        var modal = document.getElementById('modalFotoSalva');
        if (modal) {
            modal.style.display = 'none';
        }
    },

    // Pausar execução
    pausarExecucao: function() {
        // Buscar atividade em andamento
        const atividadeEmAndamento = dadosObra.atividadeAndamento;
        if (!atividadeEmAndamento) {
            alert('Nenhuma atividade em andamento.');
            return;
        }

        // Calcular tempo decorrido
        const inicio = atividadeEmAndamento.hora_inicio ? new Date(atividadeEmAndamento.hora_inicio) : this.horaInicio;
        var tempoTexto = '00:00';
        if (inicio) {
            const agora = new Date();
            const diff = agora - inicio;
            const horas = Math.floor(diff / 3600000);
            const minutos = Math.floor((diff % 3600000) / 60000);
            tempoTexto = String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0');
        }

        // Atualizar modal
        const atividadeEl = document.getElementById('confirmarPausarAtividade');
        const tempoEl = document.getElementById('confirmarPausarTempo');

        if (atividadeEl) {
            const nomeAtividade = atividadeEmAndamento.titulo || atividadeEmAndamento.descricao || 'Atividade em andamento';
            atividadeEl.textContent = nomeAtividade;
        }
        if (tempoEl) tempoEl.textContent = tempoTexto;

        // Limpar campo de motivo
        const motivoEl = document.getElementById('motivoPausa');
        if (motivoEl) motivoEl.value = '';

        // Abrir modal
        var modal = document.getElementById('modalConfirmarPausar');
        if (modal) modal.style.display = 'block';
    },

    // Confirmar pausa no modal
    confirmarPausar: function() {
        this.pararTimer();

        const csrfToken = this.getCsrfToken();

        // Buscar atividade em andamento
        const atividadeEmAndamento = dadosObra.atividadeAndamento;
        const atividadeId = atividadeEmAndamento ? (atividadeEmAndamento.id || atividadeEmAndamento.idAtividade) : null;

        if (!atividadeId) {
            alert('Nenhuma atividade em andamento para pausar.');
            return;
        }

        // Pegar motivo da pausa
        const motivoEl = document.getElementById('motivoPausa');
        const motivo = motivoEl ? motivoEl.value : '';

        const formData = new FormData();
        formData.append('MAPOS_TOKEN', csrfToken);
        formData.append('obra_id', dadosObra.obraId);
        formData.append('atividade_id', atividadeId);
        formData.append('observacao', motivo);

        fetch('<?= site_url("atividades/pausar") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
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
                // Fechar modal
                var modal = document.getElementById('modalConfirmarPausar');
                if (modal) modal.style.display = 'none';

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

    // Avançar para checkout - mostra modal de confirmação primeiro
    avancarParaCheckout: function() {
        const atividadeEmAndamento = dadosObra.atividadeAndamento;

        // Calcular tempo e dados para o modal
        var horaInicioTexto = '--:--';
        var tempoTotalTexto = '00:00';

        if (this.horaInicio) {
            horaInicioTexto = this.horaInicio.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            const agora = new Date();
            const diff = agora - this.horaInicio;
            const horas = Math.floor(diff / 3600000);
            const minutos = Math.floor((diff % 3600000) / 60000);
            tempoTotalTexto = String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0');
        } else if (atividadeEmAndamento && atividadeEmAndamento.hora_inicio) {
            const inicio = new Date(atividadeEmAndamento.hora_inicio);
            horaInicioTexto = inicio.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            const agora = new Date();
            const diff = agora - inicio;
            const horas = Math.floor(diff / 3600000);
            const minutos = Math.floor((diff % 3600000) / 60000);
            tempoTotalTexto = String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0');
        }

        // Atualizar modal
        const atividadeEl = document.getElementById('confirmarCheckoutAtividade');
        const horaInicioEl = document.getElementById('confirmarCheckoutHoraInicio');
        const tempoEl = document.getElementById('confirmarCheckoutTempo');

        if (atividadeEl) {
            const nomeAtividade = atividadeEmAndamento
                ? (atividadeEmAndamento.titulo || atividadeEmAndamento.descricao || 'Atividade em andamento')
                : (this.atividadeSelecionada ? this.atividadeSelecionada.nome : 'Atividade');
            const nomeEtapa = this.etapaSelecionada ? this.etapaSelecionada.nome : 'Etapa';
            atividadeEl.textContent = nomeEtapa + ' - ' + nomeAtividade;
        }
        if (horaInicioEl) horaInicioEl.textContent = horaInicioTexto;
        if (tempoEl) tempoEl.textContent = tempoTotalTexto;

        // Abrir modal de confirmação
        var modal = document.getElementById('modalConfirmarCheckout');
        if (modal) modal.style.display = 'block';
    },

    // Confirmar avanço para checkout no modal
    confirmarAvancarCheckout: function() {
        // Fechar modal de confirmação
        var modalConfirm = document.getElementById('modalConfirmarCheckout');
        if (modalConfirm) modalConfirm.style.display = 'none';

        // Preencher resumo no step 5
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
                // Radio buttons - apenas uma atividade pode ser selecionada
                lista.innerHTML = atividades.map((atv, index) => `
                    <div class="checkout-item" onclick="WizardAtendimento.selecionarAtividadeCheckout(${atv.id})">
                        <input type="radio" name="atividade_checkout" id="chk_${atv.id}" value="${atv.id}" ${index === 0 ? 'checked' : ''}>
                        <div class="checkout-item-info">
                            <h5>${atv.titulo || atv.descricao || 'Atividade #' + atv.id}</h5>
                            <p>Selecione para marcar o status</p>
                        </div>
                        <select class="status-select" id="status_${atv.id}" onchange="WizardAtendimento.atualizarStatusAtividade(${atv.id})" onclick="event.stopPropagation()">
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

    // Selecionar atividade no checkout (radio button)
    selecionarAtividadeCheckout: function(atividadeId) {
        var radio = document.getElementById('chk_' + atividadeId);
        if (radio) {
            radio.checked = true;
        }
    },

    // Atualizar status da atividade
    atualizarStatusAtividade: function(atividadeId) {
        var radio = document.getElementById('chk_' + atividadeId);
        var select = document.getElementById('status_' + atividadeId);

        if (!radio || !select) return;

        // Se mudou o status, seleciona o radio
        radio.checked = true;
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
        // Coletar status da atividade selecionada (apenas uma)
        const statusAtividades = [];
        const radioSelecionado = document.querySelector('input[name="atividade_checkout"]:checked');

        if (radioSelecionado) {
            const id = radioSelecionado.value;
            const select = document.getElementById('status_' + id);
            if (select) {
                statusAtividades.push({ id: id, status: select.value });
            }
        }

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
            console.log('Enviando foto checkout:', fotoInput.files[0].name, 'Tamanho:', fotoInput.files[0].size);
            formData.append('foto_saida', fotoInput.files[0]);
        } else {
            console.log('Nenhuma foto de saída selecionada');
        }

        fetch('<?= site_url("atividades/checkout_obra") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
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

// Fechar modais ao clicar fora
document.addEventListener('click', function(e) {
    var modalIniciar = document.getElementById('modalConfirmarIniciar');
    var modalFinalizar = document.getElementById('modalConfirmarFinalizar');
    var modalPausar = document.getElementById('modalConfirmarPausar');
    var modalProgresso = document.getElementById('modalConfirmarProgresso');
    var modalCheckout = document.getElementById('modalConfirmarCheckout');
    var modalProgressoAntigo = document.getElementById('modalProgresso');
    var modalFoto = document.getElementById('modalFotoSalva');

    if (modalIniciar && e.target === modalIniciar) {
        modalIniciar.style.display = 'none';
    }
    if (modalFinalizar && e.target === modalFinalizar) {
        modalFinalizar.style.display = 'none';
    }
    if (modalPausar && e.target === modalPausar) {
        modalPausar.style.display = 'none';
    }
    if (modalProgresso && e.target === modalProgresso) {
        modalProgresso.style.display = 'none';
    }
    if (modalCheckout && e.target === modalCheckout) {
        modalCheckout.style.display = 'none';
    }
    if (modalProgressoAntigo && e.target === modalProgressoAntigo) {
        modalProgressoAntigo.style.display = 'none';
    }
    if (modalFoto && e.target === modalFoto) {
        modalFoto.style.display = 'none';
    }
});

// Fechar modais ao pressionar ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var modais = ['modalConfirmarIniciar', 'modalConfirmarFinalizar', 'modalConfirmarPausar', 'modalConfirmarProgresso', 'modalConfirmarCheckout', 'modalProgresso', 'modalFotoSalva'];
        modais.forEach(function(id) {
            var modal = document.getElementById(id);
            if (modal) modal.style.display = 'none';
        });
    }
});

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

// ===== FUNÇÃO DE REABERTURA DE ATIVIDADE =====
function reabrirAtividade(atividadeId, tituloAtividade) {
    // Confirmar com o técnico
    var motivo = prompt(
        'Reabrir atividade: ' + tituloAtividade + '\n\n' +
        'Informe o motivo da reabertura (opcional):'
    );

    // Se clicou em cancelar, aborta
    if (motivo === null) {
        return;
    }

    // Desabilitar o botão para evitar cliques duplos
    var btn = document.querySelector('button[onclick*="reabrirAtividade(' + atividadeId + '"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="icon-refresh icon-spin"></i> Reabrindo...';
    }

    // Preparar dados para envio
    var formData = new FormData();
    formData.append('atividade_id', atividadeId);
    formData.append('motivo', motivo);

    // Obter token CSRF do input hidden no wizard ou da meta tag
    var csrfToken = '';
    var csrfInput = document.querySelector('input[name="MAPOS_TOKEN"]');
    if (csrfInput) {
        csrfToken = csrfInput.value;
    }
    formData.append('MAPOS_TOKEN', csrfToken);

    // Fazer requisição AJAX
    fetch('<?= site_url("tecnicos/reabrir_atividade") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(r) {
        if (!r.ok) {
            throw new Error('HTTP error: ' + r.status);
        }
        return r.json();
    })
    .then(function(data) {
        if (data.success) {
            alert('Atividade reaberta com sucesso!\n\n' + (data.message || 'Você pode iniciar um novo atendimento para esta atividade.'));
            // Recarregar a página para mostrar a atividade como reaberta
            location.reload();
        } else {
            alert('Erro ao reabrir atividade: ' + (data.message || 'Erro desconhecido'));
            // Reabilitar o botão em caso de erro
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="icon-refresh"></i> Reabrir';
            }
        }
    })
    .catch(function(err) {
        console.error('Erro:', err);
        alert('Erro ao reabrir atividade. Verifique sua conexão e tente novamente.');
        // Reabilitar o botão em caso de erro
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="icon-refresh"></i> Reabrir';
        }
    });
}
</script>
