<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Tema Moderno Obras -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
    .obra-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-radius: 12px;
        padding: 24px;
        color: white;
        margin-bottom: 24px;
    }
    .obra-header h2 {
        margin: 0 0 8px 0;
        font-size: 24px;
    }
    .obra-header p {
        margin: 0;
        opacity: 0.9;
    }
    .obra-status {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 12px;
    }

    .progress-section {
        margin: 20px 0;
    }
    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .progress-bar {
        height: 10px;
        background: rgba(255,255,255,0.2);
        border-radius: 5px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: white;
        border-radius: 5px;
        transition: width 0.5s ease;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 16px;
        color: #333;
    }

    .etapa-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
        border-left: 4px solid #ddd;
    }
    .etapa-item.pendente { border-left-color: #f39c12; }
    .etapa-item.em-andamento { border-left-color: #3498db; }
    .etapa-item.concluida { border-left-color: #27ae60; }

    .etapa-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .etapa-nome {
        font-weight: 600;
        font-size: 15px;
    }
    .etapa-status {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .etapa-status.pendente { background: #fff3cd; color: #856404; }
    .etapa-status.em-andamento { background: #d1ecf1; color: #0c5460; }
    .etapa-status.concluida { background: #d4edda; color: #155724; }

    .btn-acao {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-acao:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }
    .btn-primary-tec {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .btn-primary-tec:hover {
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    .btn-success-tec {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    .btn-success-tec:hover {
        color: white;
        box-shadow: 0 4px 12px rgba(17, 153, 142, 0.4);
    }

    .os-lista {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .os-item {
        display: flex;
        align-items: center;
        padding: 14px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .os-numero {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 14px;
    }
    .os-info {
        flex: 1;
    }
    .os-cliente {
        font-weight: 600;
    }
    .os-data {
        font-size: 13px;
        color: #888;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #888;
    }
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    /* Wizard de Execucao de Atividade */
    .wizard-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-top: 20px;
    }
    .wizard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 24px 20px;
        text-align: center;
    }
    .wizard-header h4 {
        margin: 0 0 8px 0;
        font-size: 20px;
        font-weight: 600;
    }
    .wizard-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 14px;
    }

    /* Progress Steps */
    .wizard-steps {
        display: flex;
        justify-content: center;
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
    }
    .step-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .step {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }
    .step.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    .step.completed {
        background: #27ae60;
        color: white;
    }
    .step-line {
        width: 30px;
        height: 3px;
        background: #e0e0e0;
        border-radius: 2px;
    }
    .step-line.completed {
        background: #27ae60;
    }

    /* Wizard Content */
    .wizard-content {
        padding: 24px 20px;
        min-height: 300px;
    }
    .wizard-step-content {
        display: none;
    }
    .wizard-step-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Step Title */
    .step-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        text-align: center;
    }
    .step-subtitle {
        font-size: 14px;
        color: #666;
        margin-bottom: 24px;
        text-align: center;
    }

    /* Cards de Selecao */
    .select-cards {
        display: grid;
        gap: 12px;
    }
    .select-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .select-card:hover {
        border-color: #667eea;
        background: #f8f9ff;
    }
    .select-card.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, #667eea08 0%, #764ba208 100%);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
    }
    .select-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: #f0f0f0;
        transition: all 0.3s;
    }
    .select-card.selected .select-card-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .select-card-info {
        flex: 1;
    }
    .select-card-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }
    .select-card-desc {
        font-size: 13px;
        color: #666;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 14px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s;
        background: white;
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    /* Slider de Progresso */
    .progress-slider-container {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .progress-value {
        font-size: 36px;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 8px;
    }
    .progress-label {
        font-size: 14px;
        color: #666;
        margin-bottom: 16px;
    }
    .progress-slider {
        width: 100%;
        height: 8px;
        border-radius: 4px;
        background: #e0e0e0;
        outline: none;
        -webkit-appearance: none;
    }
    .progress-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
    }
    .progress-slider::-moz-range-thumb {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        cursor: pointer;
        border: none;
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
    }

    /* Upload de Fotos */
    .photo-upload-area {
        border: 2px dashed #ccc;
        border-radius: 16px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #fafafa;
    }
    .photo-upload-area:hover {
        border-color: #667eea;
        background: #f8f9ff;
    }
    .photo-upload-area.has-photos {
        border-color: #27ae60;
        background: #f0faf0;
    }
    .photo-upload-area i {
        font-size: 48px;
        color: #999;
        margin-bottom: 16px;
        display: block;
    }
    .photo-upload-area:hover i {
        color: #667eea;
    }
    .photo-upload-area.has-photos i {
        color: #27ae60;
    }
    .photo-upload-text {
        font-size: 16px;
        color: #666;
        margin-bottom: 8px;
    }
    .photo-upload-hint {
        font-size: 13px;
        color: #999;
    }
    #fotoInput {
        display: none;
    }

    /* Preview de Fotos */
    .photos-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
        margin-top: 20px;
    }
    .photo-preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .photo-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(231, 76, 60, 0.9);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.2s;
    }
    .photo-remove-btn:hover {
        background: #c0392b;
        transform: scale(1.1);
    }
    .photo-count {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        display: inline-block;
        margin-top: 12px;
    }
    .progress-presets {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-top: 16px;
        flex-wrap: wrap;
    }
    .progress-preset {
        padding: 6px 12px;
        border: 1px solid #ddd;
        border-radius: 20px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }
    .progress-preset:hover {
        border-color: #667eea;
        color: #667eea;
    }

    /* Resumo */
    .resumo-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .resumo-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0;
    }
    .resumo-item:last-child {
        border-bottom: none;
    }
    .resumo-label {
        color: #666;
        font-size: 14px;
    }
    .resumo-value {
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    /* Botoes de Navegacao */
    .wizard-nav {
        display: flex;
        gap: 12px;
        padding: 20px;
        background: #f8f9fa;
        border-top: 1px solid #e0e0e0;
    }
    .wizard-btn {
        flex: 1;
        padding: 14px 24px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .wizard-btn-secondary {
        background: white;
        color: #666;
        border: 2px solid #e0e0e0;
    }
    .wizard-btn-secondary:hover {
        border-color: #667eea;
        color: #667eea;
    }
    .wizard-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .wizard-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    .wizard-btn-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    .wizard-btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
    }
    .wizard-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Etapas com Quick Select */
    .etapa-cards {
        display: grid;
        gap: 10px;
        max-height: 300px;
        overflow-y: auto;
        padding-right: 8px;
    }
    .etapa-card {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .etapa-card:hover {
        border-color: #667eea;
    }
    .etapa-card.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, #667eea08 0%, #764ba208 100%);
    }
    .etapa-card.pular {
        background: #f0f0f0;
        border-style: dashed;
        text-align: center;
        color: #666;
    }
    .etapa-card.pular:hover {
        background: #e8e8e8;
        border-color: #999;
    }
    .etapa-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }
    .etapa-card-nome {
        font-weight: 600;
        font-size: 14px;
    }
    .etapa-card-status {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 10px;
    }
    .etapa-card-progress {
        font-size: 12px;
        color: #666;
    }

    /* Alertas */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        display: none;
    }
    .alert.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .alert.show {
        display: block;
    }

    /* Loading */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 8px;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Melhorias no Wizard - Feedback Visual */
    .wizard-container {
        position: relative;
    }

    /* Indicador de passo atual */
    .step.active {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
        50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6); }
    }

    /* Cards de seleção melhorados */
    .select-card.selected .select-card-icon {
        animation: iconPop 0.3s ease;
    }
    @keyframes iconPop {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    /* Cards de etapa melhorados */
    .etapa-card.selected {
        animation: slideHighlight 0.4s ease;
    }
    @keyframes slideHighlight {
        from { transform: translateX(0); background: #fff; }
        50% { transform: translateX(5px); }
        to { transform: translateX(0); background: linear-gradient(135deg, #667eea08 0%, #764ba208 100%); }
    }

    /* Slider de progresso estilizado */
    .progress-slider {
        background: linear-gradient(to right, #667eea 0%, #667eea 0%, #e0e0e0 0%, #e0e0e0 100%);
    }

    /* Upload area com drag highlight */
    .photo-upload-area.dragover {
        border-color: #667eea;
        background: #f0f4ff;
        transform: scale(1.02);
    }

    /* Preview de fotos melhorado */
    .photo-preview-item {
        transition: all 0.3s ease;
    }
    .photo-preview-item:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Resumo com destaque */
    .resumo-box {
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    .resumo-box:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Alertas animados */
    .alert {
        animation: alertSlide 0.3s ease;
    }
    @keyframes alertSlide {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Botão pular etapa */
    .btn-pular {
        background: transparent;
        border: 1px dashed #999;
        color: #666;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        margin-left: auto;
        transition: all 0.3s;
    }
    .btn-pular:hover {
        border-color: #667eea;
        color: #667eea;
        background: #f8f9ff;
    }

    /* Tooltip de ajuda */
    .help-tooltip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        font-size: 12px;
        cursor: help;
        margin-left: 8px;
    }
    .help-tooltip:hover {
        background: #667eea;
        color: white;
    }

    /* Keyboard hint */
    .keyboard-hint {
        font-size: 12px;
        color: #888;
        margin-top: 8px;
        text-align: center;
    }
    .keyboard-hint kbd {
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
        border: 1px solid #ddd;
    }

    /* Dark mode support */
    body[data-theme="dark"] .section-title { color: #e8e8e8; }
    body[data-theme="dark"] .etapa-item { background: #252a3a; }
    body[data-theme="dark"] .etapa-nome { color: #e8e8e8; }
    body[data-theme="dark"] .os-item { background: #252a3a; }
    body[data-theme="dark"] .os-cliente { color: #e8e8e8; }
    body[data-theme="dark"] .os-data { color: #888; }

    /* Dark mode - Wizard */
    body[data-theme="dark"] .wizard-container { background: #1a1d29; }
    body[data-theme="dark"] .wizard-steps { background: #252a3a; border-color: #3a3f4f; }
    body[data-theme="dark"] .step { background: #3a3f4f; color: #a0a8b8; }
    body[data-theme="dark"] .step-line { background: #3a3f4f; }
    body[data-theme="dark"] .wizard-content { background: #1a1d29; }
    body[data-theme="dark"] .step-title { color: #e8e8e8; }
    body[data-theme="dark"] .step-subtitle { color: #a0a8b8; }
    body[data-theme="dark"] .wizard-nav { background: #252a3a; border-color: #3a3f4f; }

    /* Dark mode - Cards de Selecao */
    body[data-theme="dark"] .select-card { background: #252a3a; border-color: #3a3f4f; }
    body[data-theme="dark"] .select-card:hover { border-color: #667eea; background: #2a3050; }
    body[data-theme="dark"] .select-card.selected { background: rgba(102, 126, 234, 0.15); }
    body[data-theme="dark"] .select-card-icon { background: #3a3f4f; }
    body[data-theme="dark"] .select-card-title { color: #e8e8e8; }
    body[data-theme="dark"] .select-card-desc { color: #a0a8b8; }

    /* Dark mode - Formularios */
    body[data-theme="dark"] .form-group label { color: #e8e8e8; }
    body[data-theme="dark"] .form-group input,
    body[data-theme="dark"] .form-group textarea,
    body[data-theme="dark"] .form-group select {
        background: #252a3a;
        border-color: #3a3f4f;
        color: #e8e8e8;
    }
    body[data-theme="dark"] .form-group input:focus,
    body[data-theme="dark"] .form-group textarea:focus,
    body[data-theme="dark"] .form-group select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }

    /* Dark mode - Progress Slider */
    body[data-theme="dark"] .progress-slider-container { background: #252a3a; }
    body[data-theme="dark"] .progress-slider { background: #3a3f4f; }
    body[data-theme="dark"] .progress-value { color: #667eea; }
    body[data-theme="dark"] .progress-label { color: #a0a8b8; }
    body[data-theme="dark"] .progress-preset {
        background: #252a3a;
        border-color: #3a3f4f;
        color: #e8e8e8;
    }
    body[data-theme="dark"] .progress-preset:hover { border-color: #667eea; color: #667eea; }

    /* Dark mode - Etapa Cards */
    body[data-theme="dark"] .etapa-card { background: #252a3a; border-color: #3a3f4f; }
    body[data-theme="dark"] .etapa-card:hover { border-color: #667eea; }
    body[data-theme="dark"] .etapa-card.selected { background: rgba(102, 126, 234, 0.15); }
    body[data-theme="dark"] .etapa-card.pular { background: #3a3f4f; color: #a0a8b8; }
    body[data-theme="dark"] .etapa-card-nome { color: #e8e8e8; }
    body[data-theme="dark"] .etapa-card-progress { color: #a0a8b8; }

    /* Dark mode - Resumo */
    body[data-theme="dark"] .resumo-box { background: #252a3a; }
    body[data-theme="dark"] .resumo-item { border-color: #3a3f4f; }
    body[data-theme="dark"] .resumo-label { color: #a0a8b8; }
    body[data-theme="dark"] .resumo-value { color: #e8e8e8; }

    /* Dark mode - Botoes */
    body[data-theme="dark"] .wizard-btn-secondary {
        background: #3a3f4f;
        border-color: #4a5060;
        color: #e8e8e8;
    }
    body[data-theme="dark"] .wizard-btn-secondary:hover {
        border-color: #667eea;
        color: #667eea;
        background: #4a5060;
    }

    /* Dark mode - Upload de Fotos */
    body[data-theme="dark"] .photo-upload-area {
        background: #252a3a;
        border-color: #3a3f4f;
    }
    body[data-theme="dark"] .photo-upload-area:hover {
        border-color: #667eea;
        background: #2a3050;
    }
    body[data-theme="dark"] .photo-upload-area.has-photos {
        border-color: #27ae60;
        background: #1a3a2a;
    }
    body[data-theme="dark"] .photo-upload-area i {
        color: #4a5060;
    }
    body[data-theme="dark"] .photo-upload-area:hover i {
        color: #667eea;
    }
    body[data-theme="dark"] .photo-upload-text {
        color: #a0a8b8;
    }
    body[data-theme="dark"] .photo-upload-hint {
        color: #6a7080;
    }
    body[data-theme="dark"] .photo-preview-item {
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    /* Dark mode - Dica box */
    body[data-theme="dark"] .dica-box {
        background: #1a3a4a !important;
        color: #8ecae6 !important;
    }

    /* ==========================================
       RESPONSIVIDADE - Mobile & Desktop
       ========================================== */

    /* Container principal - Layout responsivo */
    .main-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    @media (min-width: 1024px) {
        .main-container {
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
    }

    /* Header da obra - Responsivo */
    @media (max-width: 768px) {
        .obra-header {
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 16px;
        }
        .obra-header h2 {
            font-size: 18px;
        }
        .obra-header p {
            font-size: 13px;
        }
    }

    /* Wizard Container - Mobile First */
    .wizard-container {
        width: 100%;
        max-width: 100%;
        margin: 0;
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        .wizard-container {
            border-radius: 0;
            margin: 0 -8px;
            width: calc(100% + 16px);
        }
    }

    /* Wizard Header responsivo */
    @media (max-width: 768px) {
        .wizard-header {
            padding: 16px 12px;
        }
        .wizard-header h4 {
            font-size: 16px;
        }
        .wizard-header p {
            font-size: 12px;
        }
    }

    /* Wizard Steps - Scrollable em mobile */
    @media (max-width: 768px) {
        .wizard-steps {
            padding: 12px 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            justify-content: flex-start;
        }
        .step-indicator {
            min-width: max-content;
            gap: 4px;
        }
        .step {
            width: 28px;
            height: 28px;
            font-size: 12px;
            flex-shrink: 0;
        }
        .step-line {
            width: 20px;
            height: 2px;
        }
    }

    /* Wizard Content responsivo */
    @media (max-width: 768px) {
        .wizard-content {
            padding: 16px 12px;
            min-height: auto;
        }
        .step-title {
            font-size: 16px;
        }
        .step-subtitle {
            font-size: 13px;
        }
    }

    /* Cards de seleção responsivos */
    @media (max-width: 768px) {
        .select-cards {
            gap: 8px;
        }
        .select-card {
            padding: 12px;
            gap: 12px;
        }
        .select-card-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }
        .select-card-title {
            font-size: 14px;
        }
        .select-card-desc {
            font-size: 12px;
        }
    }

    /* Etapa cards responsivos */
    @media (max-width: 768px) {
        .etapa-cards {
            max-height: 250px;
            gap: 8px;
        }
        .etapa-card {
            padding: 10px;
        }
        .etapa-card-nome {
            font-size: 13px;
        }
        .etapa-card-status {
            font-size: 10px;
        }
    }

    /* Formulários responsivos */
    @media (max-width: 768px) {
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            font-size: 14px;
            margin-bottom: 6px;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 12px;
            font-size: 16px; /* Evita zoom no iOS */
            border-radius: 8px;
        }
        .form-group textarea {
            min-height: 100px;
        }
    }

    /* Progress Slider responsivo */
    @media (max-width: 768px) {
        .progress-slider-container {
            padding: 16px;
        }
        .progress-value {
            font-size: 28px;
        }
        .progress-presets {
            gap: 6px;
        }
        .progress-preset {
            padding: 6px 10px;
            font-size: 11px;
        }
    }

    /* Upload de fotos responsivo */
    @media (max-width: 768px) {
        .photo-upload-area {
            padding: 24px 16px;
        }
        .photo-upload-area i {
            font-size: 36px;
        }
        .photo-upload-text {
            font-size: 14px;
        }
        .photo-upload-hint {
            font-size: 12px;
        }
        .photos-preview {
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 8px;
        }
        .photo-preview-item {
            border-radius: 8px;
        }
    }

    /* Resumo responsivo */
    @media (max-width: 768px) {
        .resumo-box {
            padding: 16px;
        }
        .resumo-item {
            flex-direction: column;
            gap: 4px;
            padding: 8px 0;
        }
        .resumo-label {
            font-size: 12px;
        }
        .resumo-value {
            font-size: 13px;
            text-align: left !important;
            max-width: 100% !important;
        }
    }

    /* Navegação do wizard responsiva */
    @media (max-width: 768px) {
        .wizard-nav {
            padding: 12px;
            gap: 8px;
            flex-wrap: wrap;
        }
        .wizard-btn {
            padding: 12px 16px;
            font-size: 14px;
            min-height: 48px;
            flex: 1 1 calc(50% - 4px);
        }
        .wizard-btn-success {
            flex: 1 1 100%;
            order: -1;
        }
    }

    /* Etapas da obra responsivas */
    @media (max-width: 768px) {
        .etapa-item {
            padding: 12px;
            margin-bottom: 8px;
        }
        .etapa-nome {
            font-size: 14px;
        }
        .etapa-status {
            padding: 3px 8px;
            font-size: 10px;
        }
    }

    /* OS lista responsiva */
    @media (max-width: 768px) {
        .os-item {
            padding: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .os-numero {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }
        .os-cliente {
            font-size: 14px;
        }
        .os-data {
            font-size: 12px;
        }
        .btn-acao {
            width: 100%;
            justify-content: center;
            margin-top: 8px;
            padding: 12px 20px;
        }
    }

    /* Títulos de seção responsivos */
    @media (max-width: 768px) {
        .section-title {
            font-size: 16px;
            margin-bottom: 12px;
        }
    }

    /* Toast notifications responsivos */
    @media (max-width: 768px) {
        #toastContainer {
            left: 16px !important;
            right: 16px !important;
            top: auto !important;
            bottom: 80px !important;
            max-width: none !important;
        }
    }

    /* Atalhos de teclado escondidos em mobile */
    @media (max-width: 768px) {
        .keyboard-hint {
            display: none !important;
        }
    }

    /* Landscape mode em mobile */
    @media (max-height: 500px) and (orientation: landscape) {
        .wizard-header {
            padding: 12px;
        }
        .wizard-header h4 {
            font-size: 14px;
        }
        .wizard-steps {
            padding: 8px;
        }
        .wizard-content {
            padding: 12px;
            min-height: 200px;
        }
    }

    /* Tablets */
    @media (min-width: 769px) and (max-width: 1023px) {
        .main-container {
            grid-template-columns: 1fr;
        }
    }

    /* Tela grande - Desktop */
    @media (min-width: 1400px) {
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
        }
    }

    /* Fix para safe areas em iPhone X+ */
    @supports (padding: max(0px)) {
        @media (max-width: 768px) {
            .wizard-container {
                padding-left: max(0px, env(safe-area-inset-left));
                padding-right: max(0px, env(safe-area-inset-right));
            }
            .wizard-nav {
                padding-bottom: max(12px, env(safe-area-inset-bottom));
            }
        }
    }

    /* Dark mode responsivo */
    @media (max-width: 768px) {
        body[data-theme="dark"] .wizard-container {
            border-radius: 0;
        }
    }

    /* Estilos para seleção de atividades (Step 2) */
    .atividades-grid {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .atividades-grid::-webkit-scrollbar {
        width: 6px;
    }

    .atividades-grid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .atividades-grid::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 3px;
    }

    .atividade-card-wizard {
        position: relative;
        transition: all 0.3s ease;
    }

    .atividade-card-wizard.selected {
        border-color: #667eea !important;
        background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
    }

    .atividade-card-wizard.selected::after {
        content: '\2713';
        position: absolute;
        top: 10px;
        right: 10px;
        background: #667eea;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }

    .loading-atividades {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .criar-atividade-section input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }

    .empty-atividades {
        border: 2px dashed #e0e0e0;
    }

    /* Estilos para o step de Materiais */
    .material-item {
        animation: fadeIn 0.3s ease;
    }

    #stepMateriais {
        display: none;
    }

    #stepMateriais.active {
        display: block;
    }

    .material-form input:focus,
    .material-form select:focus,
    .material-form textarea:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }

    .empty-materiais {
        animation: pulse 2s infinite;
    }

    .select-card.selected-foradoscopo {
        border-color: #ff6b6b !important;
        background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
    }

    .select-card.selected-foradoscopo .select-card-icon {
        background: linear-gradient(135deg, #ff6b6b, #ee5a5a) !important;
    }
</style>

<!-- Header da Obra -->
<div class="obra-header">
    <h2><i class='bx bx-building'></i> <?= htmlspecialchars($obra->nome ?? 'Obra') ?></h2>
    <p><i class='bx bx-user'></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Cliente nao informado') ?></p>
    <span class="obra-status"><?= $obra->status ?? 'N/A' ?></span>

    <div class="progress-section">
        <div class="progress-header">
            <span>Progresso</span>
            <span><?= $obra->percentual_concluido ?? 0 ?>%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $obra->percentual_concluido ?? 0 ?>%"></div>
        </div>
    </div>
</div>

<div class="main-container">

    <!-- Coluna Esquerda: Etapas e Atividades -->
    <div>
        <h3 class="section-title"><i class='bx bx-list-check'></i> Etapas da Obra</h3>

        <?php if (!empty($etapas)): ?>
            <?php foreach ($etapas as $etapa): ?>
                <?php
                $statusClass = strtolower(str_replace(' ', '-', $etapa->status));
                $statusLabel = $etapa->status;
                ?>
                <div class="etapa-item <?= $statusClass ?>">
                    <div class="etapa-header">
                        <span class="etapa-nome"><?= htmlspecialchars($etapa->nome) ?></span>
                        <span class="etapa-status <?= $statusClass ?>"><?= $statusLabel ?></span>
                    </div>
                    <?php if ($etapa->descricao): ?>
                        <p style="margin: 8px 0 0 0; font-size: 13px; color: #666;">
                            <?= htmlspecialchars($etapa->descricao) ?>
                        </p>
                    <?php endif; ?>
                    <?php if (($etapa->percentual_concluido ?? 0) > 0): ?>
                        <div style="margin-top: 10px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                                <span>Progresso</span>
                                <span><?= $etapa->percentual_concluido ?? 0 ?>%</span>
                            </div>
                            <div style="height: 6px; background: #e0e0e0; border-radius: 3px;">
                                <div style="width: <?= $etapa->percentual_concluido ?? 0 ?>%; height: 100%; background: linear-gradient(90deg, #667eea, #764ba2); border-radius: 3px;"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class='bx bx-list-ul'></i>
                <p>Nenhuma etapa cadastrada</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Coluna Direita: Minhas OS, Atividades e Registrar Atividade -->
    <div>
        <h3 class="section-title"><i class='bx bx-clipboard'></i> Minhas OS nesta Obra</h3>

        <?php if (!empty($minhas_os)): ?>
            <div class="os-lista">
                <?php foreach ($minhas_os as $os): ?>
                    <div class="os-item">
                        <div class="os-numero">#<?= $os->idOs ?></div>
                        <div class="os-info">
                            <div class="os-cliente"><?= htmlspecialchars($os->nomeCliente) ?></div>
                            <div class="os-data">
                                <?= date('d/m/Y', strtotime($os->dataInicial)) ?> • <?= $os->status ?>
                            </div>
                        </div>
                        <a href="<?= site_url('tecnicos/executar_os/' . $os->idOs) ?>" class="btn-acao btn-primary-tec">
                            <i class='bx bx-play'></i> Executar
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class='bx bx-clipboard'></i>
                <p>Voce nao tem OS nesta obra</p>
            </div>
        <?php endif; ?>

        <!-- Minhas Atividades Registradas -->
        <h3 class="section-title" style="margin-top: 30px;"><i class='bx bx-history'></i> Minhas Atividades</h3>

        <?php if (!empty($minhas_atividades)): ?>
            <div class="atividades-lista" style="max-height: 400px; overflow-y: auto;">
                <?php
                $tipo_labels = [
                    'execucao' => 'Execução',
                    'problema' => 'Problema',
                    'observacao' => 'Observação'
                ];
                $tipo_cores = [
                    'execucao' => '#27ae60',
                    'problema' => '#e74c3c',
                    'observacao' => '#3498db'
                ];
                foreach ($minhas_atividades as $atividade):
                    $tipo = $atividade->tipo ?? 'execucao';
                    $cor = $tipo_cores[$tipo] ?? '#666';
                    $label = $tipo_labels[$tipo] ?? ucfirst($tipo);
                    $tem_fotos = !empty($atividade->fotos_atividade) || !empty($atividade->fotos);
                ?>
                    <div class="atividade-item" style="background: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 10px; border-left: 4px solid <?= $cor ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="background: <?= $cor ?>20; color: <?= $cor ?>; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                    <?= $label ?>
                                </span>
                                <span style="color: #888; font-size: 12px;">
                                    <?= date('d/m/Y', strtotime($atividade->data_atividade)) ?>
                                </span>
                            </div>
                            <?php if ($tem_fotos): ?>
                                <i class='bx bx-camera' style="color: #667eea; font-size: 18px;"></i>
                            <?php endif; ?>
                        </div>
                        <p style="margin: 0; font-size: 13px; color: #333; line-height: 1.4;">
                            <?= htmlspecialchars(substr($atividade->descricao, 0, 100)) ?>
                            <?= strlen($atividade->descricao) > 100 ? '...' : '' ?>
                        </p>

                        <?php if (($atividade->percentual_concluido ?? 0) > 0): ?>
                            <div style="margin-top: 8px;">
                                <div style="display: flex; justify-content: space-between; font-size: 11px; color: #666; margin-bottom: 3px;">
                                    <span>Progresso</span>
                                    <span><?= $atividade->percentual_concluido ?>%</span>
                                </div>
                                <div style="height: 4px; background: #e0e0e0; border-radius: 2px;">
                                    <div style="width: <?= $atividade->percentual_concluido ?>%; height: 100%; background: <?= $cor ?>; border-radius: 2px;"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" style="padding: 20px;">
                <i class='bx bx-history' style="font-size: 32px; color: #ddd;"></i>
                <p style="font-size: 13px;">Nenhuma atividade registrada hoje</p>
            </div>
        <?php endif; ?>

        <!-- Wizard de Execucao de Atividade -->
        <div class="wizard-container" id="wizardAtividade">
            <div class="wizard-header">
                <h4><i class='bx bx-play-circle'></i> Registrar Execucao</h4>
                <p>Siga os passos para registrar sua atividade</p>
            </div>

            <!-- Progress Steps -->
            <div class="wizard-steps">
                <div class="step-indicator">
                    <div class="step active" data-step="1">1</div>
                    <div class="step-line" data-step="1"></div>
                    <div class="step" data-step="2">2</div>
                    <div class="step-line" data-step="2"></div>
                    <div class="step" data-step="3">3</div>
                    <div class="step-line" data-step="3"></div>
                    <div class="step" data-step="4">4</div>
                    <div class="step-line" data-step="4"></div>
                    <div class="step" data-step="5">5</div>
                    <div class="step-line" data-step="5"></div>
                    <div class="step" data-step="6">6</div>
                    <div class="step-line" data-step="6"></div>
                    <div class="step" data-step="7">7</div>
                </div>
            </div>

            <!-- Wizard Content -->
            <div class="wizard-content">
                <form id="formAtividade">
                    <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
                    <input type="hidden" name="etapa_id" id="etapa_id" value="">
                    <input type="hidden" name="tipo" id="tipo" value="execucao">
                    <input type="hidden" name="percentual_concluido" id="percentual_concluido" value="0">

                    <!-- Step 1: Selecionar Etapa -->
                    <div class="wizard-step-content active" data-step="1">
                        <div class="step-title"><i class='bx bx-list-check'></i> Qual etapa voce trabalhou?</div>
                        <div class="step-subtitle">Selecione a etapa da obra ou pule esta etapa</div>

                        <div class="etapa-cards">
                            <div class="etapa-card pular" onclick="wizard.selectEtapa('')">
                                <i class='bx bx-skip-next'></i> Pular / Nao especificar
                            </div>
                            <?php if (!empty($etapas)): foreach ($etapas as $etapa):
                                $statusColor = $etapa->status === 'Concluida' ? '#27ae60' : ($etapa->status === 'Em Andamento' ? '#3498db' : '#f39c12');
                            ?>
                            <div class="etapa-card" data-etapa-id="<?= $etapa->id ?>" onclick="wizard.selectEtapa('<?= $etapa->id ?>', '<?= htmlspecialchars($etapa->nome) ?>')">
                                <div class="etapa-card-header">
                                    <span class="etapa-card-nome"><?= htmlspecialchars($etapa->nome) ?></span>
                                    <span class="etapa-card-status" style="background: <?= $statusColor ?>20; color: <?= $statusColor ?>">
                                        <?= $etapa->status ?>
                                    </span>
                                </div>
                                <div class="etapa-card-progress">
                                    Progresso: <?= $etapa->percentual_concluido ?? 0 ?>%
                                </div>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                    <!-- Step 2: Selecionar ou Criar Atividade -->
                    <div class="wizard-step-content" data-step="2">
                        <div class="step-title"><i class='bx bx-layer'></i> Selecione a Atividade</div>
                        <div class="step-subtitle" id="atividadeSubtitle">Escolha uma atividade existente ou crie uma nova</div>

                        <input type="hidden" name="atividade_id" id="atividade_id" value="">

                        <div id="atividadesContainer">
                            <!-- As atividades serão carregadas dinamicamente via JavaScript -->
                            <div class="loading-atividades" style="text-align: center; padding: 30px; color: #888;">
                                <i class='bx bx-loader-alt bx-spin' style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                Carregando atividades...
                            </div>
                        </div>

                        <div class="criar-atividade-section" style="margin-top: 20px; padding-top: 20px; border-top: 2px dashed #e0e0e0;">
                            <div class="step-subtitle" style="margin-bottom: 15px;"><i class='bx bx-plus-circle'></i> Ou crie uma nova atividade</div>

                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px;">Título da Atividade</label>
                                <input type="text" id="novo_titulo_atividade" placeholder="Ex: Instalação das tubulações do 2º andar"
                                    style="width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; transition: all 0.3s;"
                                    onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e0e0e0'"
                                    oninput="wizard.verificarNovoTitulo()">
                            </div>

                            <button type="button" class="select-card" id="btnCriarAtividade" onclick="wizard.criarNovaAtividade()"
                                style="width: 100%; opacity: 0.6; pointer-events: none;">
                                <div class="select-card-icon" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                                    <i class='bx bx-plus'></i>
                                </div>
                                <div class="select-card-info">
                                    <div class="select-card-title">Criar Nova Atividade</div>
                                    <div class="select-card-desc">Inicie uma nova atividade para esta etapa</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Tipo de Atividade -->
                    <div class="wizard-step-content" data-step="3">
                        <div class="step-title"><i class='bx bx-task'></i> Que tipo de atividade foi?</div>
                        <div class="step-subtitle">Selecione o tipo que melhor descreve seu trabalho</div>

                        <div class="select-cards">
                            <div class="select-card" onclick="wizard.selectTipo('execucao', 'Execucao de Servico')">
                                <div class="select-card-icon" style="background: #d4edda; color: #155724;">
                                    <i class='bx bx-check-circle'></i>
                                </div>
                                <div class="select-card-info">
                                    <div class="select-card-title">Execucao de Servico</div>
                                    <div class="select-card-desc">Trabalho realizado, instalacao, manutencao ou reparo</div>
                                </div>
                            </div>

                            <div class="select-card" onclick="wizard.selectTipo('problema', 'Problema/Impedimento')">
                                <div class="select-card-icon" style="background: #f8d7da; color: #721c24;">
                                    <i class='bx bx-error-circle'></i>
                                </div>
                                <div class="select-card-info">
                                    <div class="select-card-title">Problema/Impedimento</div>
                                    <div class="select-card-desc">Dificuldade encontrada, falta de material ou impedimento</div>
                                </div>
                            </div>

                            <div class="select-card" onclick="wizard.selectTipo('observacao', 'Observacao')">
                                <div class="select-card-icon" style="background: #fff3cd; color: #856404;">
                                    <i class='bx bx-note'></i>
                                </div>
                                <div class="select-card-info">
                                    <div class="select-card-title">Observacao</div>
                                    <div class="select-card-desc">Anotacao importante sobre a obra ou etapa</div>
                                </div>
                            </div>

                            <div class="select-card" onclick="wizard.selectTipo('fora_do_escopo', 'Atividade Nao Planejada')">
                                <div class="select-card-icon" style="background: linear-gradient(135deg, #ff6b6b, #ee5a5a); color: white;">
                                    <i class='bx bx-error-alt'></i>
                                </div>
                                <div class="select-card-info">
                                    <div class="select-card-title">Atividade Nao Planejada</div>
                                    <div class="select-card-desc">Servico fora do escopo, imprevisto ou solicitacao extra do cliente</div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 20px; padding: 15px; background: #fff5f5; border-left: 4px solid #ee5a5a; border-radius: 8px;">
                            <div style="font-size: 13px; color: #721c24;">
                                <i class='bx bx-info-circle'></i> <strong>Atividade Nao Planejada:</strong> Use esta opcao para registrar servicos que nao estavam previstos nas etapas/atividades da obra, como imprevistos ou solicitacoes extras do cliente.
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Descricao -->
                    <div class="wizard-step-content" data-step="4">
                        <div class="step-title"><i class='bx bx-edit-alt'></i> Descreva o que foi feito</div>
                        <div class="step-subtitle">Detalhe a atividade realizada</div>

                        <div class="form-group">
                            <textarea name="descricao" id="descricao" placeholder="Ex: Instalacao das tubulacoes PVC no pavimento superior, conexoes de todos os pontos de agua fria..." required oninput="document.getElementById('charCount').textContent = this.value.length + ' caracteres'"></textarea>
                            <div style="text-align: right; font-size: 11px; color: #888; margin-top: 4px;">
                                <span id="charCount">0 caracteres</span> (min. 10)
                            </div>
                        </div>

                        <div class="dica-box" style="background: #e8f4fd; border-radius: 8px; padding: 12px; font-size: 13px; color: #0c5460;">
                            <i class='bx bx-info-circle'></i> <strong>Dica:</strong> Seja especifico. Inclua materiais usados, medidas, localizacao e qualquer detalhe relevante.
                        </div>
                    </div>


                    <!-- Step 5: Fotos -->
                    <div class="wizard-step-content" data-step="5">
                        <div class="step-title"><i class='bx bx-camera'></i> Anexe fotos da execucao</div>
                        <div class="step-subtitle">Registre visualmente o que foi realizado ou problemas encontrados</div>

                        <!-- Upload Area -->
                        <div class="photo-upload-area" id="photoUploadArea" onclick="document.getElementById('fotoInput').click()">
                            <i class='bx bx-image-add'></i>
                            <div class="photo-upload-text">Toque para selecionar fotos</div>
                            <div class="photo-upload-hint">
                                <i class='bx bx-mobile-alt'></i> Da galeria do dispositivo <i class='bx bx-camera' style="margin-left: 10px;"></i> Ou tire uma foto
                            </div>
                            <input type="file" id="fotoInput" name="fotos[]" multiple accept="image/*" capture="environment" onchange="wizard.handlePhotoSelect(event)">
                        </div>

                        <!-- Preview das Fotos -->
                        <div class="photos-preview" id="photosPreview"></div>

                        <div style="text-align: center; margin-top: 16px;">
                            <span class="photo-count" id="photoCount" style="display: none;">0 fotos selecionadas</span>
                        </div>

                        <div style="background: #fff3cd; border-radius: 8px; padding: 12px; font-size: 13px; color: #856404; margin-top: 16px;">
                            <i class='bx bx-bulb'></i> <strong>Dica:</strong> Fotos sao opcionais, mas ajudam muito a documentar o trabalho. Pode pular esta etapa se nao tiver fotos.
                        </div>
                    </div>

                    <!-- Step 6: Progresso -->
                    <div class="wizard-step-content" data-step="6">
                        <div class="step-title"><i class='bx bx-trending-up'></i> Qual o percentual de conclusao?</div>
                        <div class="step-subtitle">Ajuste o slider para indicar o progresso desta etapa</div>

                        <div class="progress-slider-container">
                            <div class="progress-value" id="progressValue">0%</div>
                            <div class="progress-label">Concluido</div>

                            <input type="range" class="progress-slider" id="progressSlider" min="0" max="100" value="0" oninput="wizard.updateProgress(this.value)">

                            <div class="progress-presets">
                                <span class="progress-preset" onclick="wizard.updateProgress(0)">Inicio (0%)</span>
                                <span class="progress-preset" onclick="wizard.updateProgress(25)">25%</span>
                                <span class="progress-preset" onclick="wizard.updateProgress(50)">50%</span>
                                <span class="progress-preset" onclick="wizard.updateProgress(75)">75%</span>
                                <span class="progress-preset" onclick="wizard.updateProgress(100)">Concluido (100%)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 7: Resumo -->
                    <div class="wizard-step-content" data-step="7">
                        <div class="step-title"><i class='bx bx-check-double'></i> Revise e confirme</div>
                        <div class="step-subtitle">Verifique as informacoes antes de finalizar</div>

                        <div class="resumo-box" id="resumoBox">
                            <div class="resumo-item">
                                <span class="resumo-label">Etapa:</span>
                                <span class="resumo-value" id="resumoEtapa">-</span>
                            </div>
                            <div class="resumo-item">
                                <span class="resumo-label">Atividade:</span>
                                <span class="resumo-value" id="resumoAtividade">Nova Atividade</span>
                            </div>
                            <div class="resumo-item">
                                <span class="resumo-label">Tipo:</span>
                                <span class="resumo-value" id="resumoTipo">-</span>
                            </div>
                            <div class="resumo-item">
                                <span class="resumo-label">Fotos Anexadas:</span>
                                <span class="resumo-value" id="resumoFotos">-</span>
                            </div>
                            <div class="resumo-item">
                                <span class="resumo-label">Progresso:</span>
                                <span class="resumo-value" id="resumoProgresso">0%</span>
                            </div>
                            <div class="resumo-item">
                                <span class="resumo-label">Descricao:</span>
                                <span class="resumo-value" id="resumoDescricao" style="text-align: right; max-width: 60%; word-break: break-word;">-</span>
                            </div>
                        </div>

                        <div id="alertSuccess" class="alert success" style="display: none;">
                            <i class='bx bx-check-circle'></i> Atividade registrada com sucesso!
                        </div>
                        <div id="alertError" class="alert error" style="display: none;">
                            <i class='bx bx-error-circle'></i> <span id="errorMessage">Erro ao registrar atividade.</span>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Navigation -->
            <div class="wizard-nav">
                <button type="button" class="wizard-btn wizard-btn-secondary" id="btnVoltar" onclick="wizard.prevStep()" disabled>
                    <i class='bx bx-chevron-left'></i> Voltar
                </button>
                <button type="button" class="wizard-btn wizard-btn-primary" id="btnAvancar" onclick="wizard.nextStep()">
                    Avancar <i class='bx bx-chevron-right'></i>
                </button>
                <button type="button" class="btn-pular" onclick="wizard.pularWizard()" style="margin-left: auto; background: transparent; border: 1px dashed #999; color: #666; padding: 8px 16px; border-radius: 8px; font-size: 13px; cursor: pointer;">
                    Cancelar
                </button>
                <button type="button" class="wizard-btn wizard-btn-success" id="btnFinalizar" onclick="wizard.submitForm()" style="display: none;">
                    <i class='bx bx-check'></i> Finalizar Registro
                </button>
            </div>

            <!-- Dicas de teclado -->
            <div style="background: #f8f9fa; padding: 10px 20px; border-top: 1px solid #e0e0e0; text-align: center; font-size: 12px; color: #888;">
                <i class='bx bx-keyboard' style="margin-right: 6px;"></i>
                <b>Atalhos:</b>
                <kbd style="background: white; padding: 2px 6px; border-radius: 4px; border: 1px solid #ddd; margin: 0 4px;">Alt + →</kbd> Avançar
                <kbd style="background: white; padding: 2px 6px; border-radius: 4px; border: 1px solid #ddd; margin: 0 4px;">Alt + ←</kbd> Voltar
                <kbd style="background: white; padding: 2px 6px; border-radius: 4px; border: 1px solid #ddd; margin: 0 4px;">Ctrl + Enter</kbd> Finalizar
            </div>
        </div>
    </div>

</div>

<script>
// Wizard de Execucao de Atividade - Versao Aprimorada
const wizard = {
    currentStep: 1,
    totalSteps: 7,
    data: {
        etapa_id: '',
        etapa_nome: '-',
        atividade_id: '',
        atividade_nome: '',
        is_nova_atividade: false,
        tipo: '',
        tipo_nome: '',
        descricao: '',
        percentual: 0,
        fotos: []
    },
    atividadesPorEtapa: <?= json_encode($atividades_por_etapa ?? []) ?>,
    isSubmitting: false,

    init: function() {
        // Animar barra de progresso da obra ao carregar
        const progressFill = document.querySelector('.obra-header .progress-fill');
        if (progressFill) {
            const width = progressFill.style.width;
            progressFill.style.width = '0%';
            setTimeout(() => {
                progressFill.style.width = width;
            }, 300);
        }

        this.createToastContainer();
        this.updateUI();
        this.setupKeyboardNavigation();
        this.setupDragAndDrop();
    },

    // Setup Drag and Drop para desktop
    setupDragAndDrop: function() {
        const uploadArea = document.getElementById('photoUploadArea');
        if (!uploadArea) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('dragover');
            }, false);
        });

        uploadArea.addEventListener('drop', (e) => {
            const files = Array.from(e.dataTransfer.files);
            this.processPhotoFiles(files);
        }, false);
    },

    // Processar arquivos de foto (reutilizável)
    processPhotoFiles: function(files) {
        if (!files || files.length === 0) return;

        const maxPhotos = 10;
        if (this.data.fotos.length + files.length > maxPhotos) {
            this.showToast(`Limite de ${maxPhotos} fotos atingido`, 'warning');
            return;
        }

        this.showToast(`Processando ${files.length} foto(s)...`, 'info');

        let processed = 0;
        files.forEach(file => {
            if (!file.type.startsWith('image/')) {
                this.showToast(`${file.name} não é uma imagem`, 'warning');
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                this.showToast(`${file.name} muito grande (max 10MB)`, 'warning');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.data.fotos.push({
                    file: file,
                    preview: e.target.result,
                    name: file.name
                });
                this.updatePhotoPreview();
                processed++;

                if (processed === files.filter(f => f.type.startsWith('image/')).length) {
                    this.showToast(`${processed} foto(s) adicionada(s)`, 'success');
                }
            };
            reader.onerror = () => {
                this.showToast(`Erro ao ler: ${file.name}`, 'error');
            };
            reader.readAsDataURL(file);
        });
    },

    // Criar container para toast notifications - Responsivo
    createToastContainer: function() {
        if (!document.getElementById('toastContainer')) {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            // Mobile: bottom center, Desktop: top right
            const isMobile = window.innerWidth <= 768;
            container.style.cssText = isMobile
                ? 'position:fixed;left:16px;right:16px;bottom:80px;z-index:9999;'
                : 'position:fixed;top:20px;right:20px;z-index:9999;max-width:350px;';
            document.body.appendChild(container);

            // Atualizar posição ao redimensionar
            window.addEventListener('resize', () => {
                const isMobileNow = window.innerWidth <= 768;
                if (isMobileNow) {
                    container.style.cssText = 'position:fixed;left:16px;right:16px;bottom:80px;z-index:9999;';
                } else {
                    container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;max-width:350px;';
                }
            });
        }
    },

    // Toast notification
    showToast: function(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');

        const colors = {
            success: '#27ae60',
            error: '#e74c3c',
            warning: '#f39c12',
            info: '#3498db'
        };

        const icons = {
            success: 'bx-check-circle',
            error: 'bx-error-circle',
            warning: 'bx-error',
            info: 'bx-info-circle'
        };

        toast.style.cssText = `
            background: ${colors[type]};
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: slideIn 0.3s ease;
            cursor: pointer;
        `;

        toast.innerHTML = `
            <i class='bx ${icons[type]}' style="font-size: 24px;"></i>
            <span style="flex:1;">${message}</span>
            <i class='bx bx-x' style="opacity:0.7;"></i>
        `;

        toast.onclick = () => toast.remove();

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    },

    // Navegacao por teclado
    setupKeyboardNavigation: function() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                if (this.currentStep === this.totalSteps && !this.isSubmitting) {
                    this.submitForm();
                } else {
                    this.nextStep();
                }
            } else if (e.key === 'ArrowRight' && e.altKey) {
                e.preventDefault();
                this.nextStep();
            } else if (e.key === 'ArrowLeft' && e.altKey) {
                e.preventDefault();
                this.prevStep();
            }
        });
    },

    // Selecao de Etapa com animacao - Agora carrega atividades da etapa
    selectEtapa: function(id, nome) {
        this.data.etapa_id = id;
        this.data.etapa_nome = id ? nome : 'Nao especificada';

        // Reset atividade selection
        this.data.atividade_id = '';
        this.data.atividade_nome = '';
        this.data.is_nova_atividade = false;
        document.getElementById('atividade_id').value = '';
        document.getElementById('novo_titulo_atividade').value = '';

        // Visual feedback com animacao
        document.querySelectorAll('.etapa-card').forEach(card => {
            card.classList.remove('selected');
            card.style.transform = '';
            if (card.dataset.etapaId === id || (!id && card.classList.contains('pular'))) {
                card.classList.add('selected');
                card.style.transform = 'scale(1.02)';
                setTimeout(() => card.style.transform = '', 200);
            }
        });

        document.getElementById('etapa_id').value = id;
        this.showToast(id ? `Etapa selecionada: ${nome}` : 'Nenhuma etapa especificada', 'success');

        // Carregar atividades da etapa selecionada
        this.carregarAtividadesDaEtapa(id, nome);

        setTimeout(() => this.nextStep(), 400);
    },

    // Carregar atividades da etapa selecionada
    carregarAtividadesDaEtapa: function(etapaId, etapaNome) {
        const container = document.getElementById('atividadesContainer');
        const subtitle = document.getElementById('atividadeSubtitle');

        subtitle.textContent = etapaId
            ? `Atividades da etapa: ${etapaNome}`
            : 'Atividades sem etapa definida';

        const atividades = this.atividadesPorEtapa[etapaId || 'sem_etapa'] || [];

        if (atividades.length === 0) {
            container.innerHTML = `
                <div class="empty-atividades" style="text-align: center; padding: 30px; color: #888; background: #f8f9fa; border-radius: 12px;">
                    <i class='bx bx-layer' style="font-size: 40px; margin-bottom: 10px; display: block; color: #ddd;"></i>
                    <p style="margin: 0; font-size: 14px;">Nenhuma atividade encontrada nesta etapa</p>
                    <p style="margin: 8px 0 0 0; font-size: 12px; color: #aaa;">Crie uma nova atividade abaixo</p>
                </div>
            `;
        } else {
            let html = '<div class="atividades-grid" style="display: grid; gap: 12px;">';
            atividades.forEach(atv => {
                const tipoLabels = { 'execucao': 'Execução', 'problema': 'Problema', 'observacao': 'Observação' };
                const tipoCores = { 'execucao': '#27ae60', 'problema': '#e74c3c', 'observacao': '#f39c12' };
                const tipo = atv.tipo || 'execucao';
                const cor = tipoCores[tipo] || '#667eea';
                const label = tipoLabels[tipo] || tipo;
                const temFotos = atv.fotos_atividade || atv.fotos;
                const percentual = atv.percentual_concluido || 0;
                const titulo = atv.titulo || atv.descricao?.substring(0, 50) || 'Atividade';

                html += `
                    <div class="atividade-card-wizard" data-atividade-id="${atv.id}"
                        onclick="wizard.selectAtividade('${atv.id}', '${this.escapeHtml(titulo)}')"
                        style="background: white; border: 2px solid #e0e0e0; border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s;"
                        onmouseover="this.style.borderColor='#667eea'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';"
                        onmouseout="if(!this.classList.contains('selected')){this.style.borderColor='#e0e0e0'; this.style.transform=''; this.style.boxShadow='';}"
                    >
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="background: ${cor}20; color: ${cor}; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">${label}</span>
                                ${temFotos ? "<i class='bx bx-camera' style='color: #667eea; font-size: 16px;'></i>" : ''}
                            </div>
                            <span style="font-size: 12px; color: #888;">${new Date(atv.data_atividade).toLocaleDateString('pt-BR')}</span>
                        </div>
                        <p style="margin: 0; font-size: 13px; color: #333; line-height: 1.4; margin-bottom: 8px;">
                            ${this.escapeHtml(titulo)}
                        </p>
                        ${percentual > 0 ? `
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="flex: 1; height: 4px; background: #e0e0e0; border-radius: 2px;">
                                    <div style="width: ${percentual}%; height: 100%; background: ${cor}; border-radius: 2px;"></div>
                                </div>
                                <span style="font-size: 11px; color: #666;">${percentual}%</span>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }
    },

    // Escape HTML para evitar XSS
    escapeHtml: function(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    // Selecionar uma atividade existente
    selectAtividade: function(id, nome) {
        this.data.atividade_id = id;
        this.data.atividade_nome = nome;
        this.data.is_nova_atividade = false;
        document.getElementById('atividade_id').value = id;

        // Visual feedback
        document.querySelectorAll('.atividade-card-wizard').forEach(card => {
            card.classList.remove('selected');
            card.style.borderColor = '#e0e0e0';
            card.style.transform = '';
            card.style.boxShadow = '';
        });

        const selected = document.querySelector(`.atividade-card-wizard[data-atividade-id="${id}"]`);
        if (selected) {
            selected.classList.add('selected');
            selected.style.borderColor = '#667eea';
            selected.style.transform = 'scale(1.02)';
            selected.style.boxShadow = '0 4px 15px rgba(102,126,234,0.3)';
        }

        this.showToast(`Atividade selecionada: ${nome.substring(0, 30)}${nome.length > 30 ? '...' : ''}`, 'success');

        setTimeout(() => this.nextStep(), 400);
    },

    // Verificar se pode criar nova atividade
    verificarNovoTitulo: function() {
        const titulo = document.getElementById('novo_titulo_atividade').value.trim();
        const btn = document.getElementById('btnCriarAtividade');

        if (titulo.length >= 3) {
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
            btn.style.borderColor = '#667eea';
        } else {
            btn.style.opacity = '0.6';
            btn.style.pointerEvents = 'none';
            btn.style.borderColor = 'transparent';
        }
    },

    // Criar nova atividade
    criarNovaAtividade: function() {
        const titulo = document.getElementById('novo_titulo_atividade').value.trim();
        if (titulo.length < 3) {
            this.showToast('Digite um título de pelo menos 3 caracteres', 'warning');
            return;
        }

        this.data.atividade_id = '';
        this.data.atividade_nome = titulo;
        this.data.is_nova_atividade = true;
        document.getElementById('atividade_id').value = '';

        this.showToast(`Nova atividade: ${titulo}`, 'success');
        this.nextStep();
    },

    // Selecao de Tipo com feedback visual
    selectTipo: function(tipo, nome) {
        this.data.tipo = tipo;
        this.data.tipo_nome = nome;
        this.data.is_foradoscopo = tipo === 'fora_do_escopo';

        // Visual feedback
        document.querySelectorAll('.select-card').forEach(card => {
            card.classList.remove('selected', 'selected-foradoscopo');
            card.style.transform = '';
        });

        const selectedCard = event.currentTarget;
        selectedCard.classList.add('selected');
        if (tipo === 'fora_do_escopo') {
            selectedCard.classList.add('selected-foradoscopo');
        }
        selectedCard.style.transform = 'scale(1.03)';
        setTimeout(() => selectedCard.style.transform = '', 200);

        document.getElementById('tipo').value = tipo;

        // Atualizar cor do progresso baseado no tipo
        const progressValue = document.getElementById('progressValue');
        const cores = {
            'problema': '#e74c3c',
            'observacao': '#f39c12',
            'execucao': '#667eea',
            'fora_do_escopo': '#ff6b6b'
        };
        progressValue.style.color = cores[tipo] || '#667eea';

        this.showToast(`Tipo selecionado: ${nome}`, 'success');
        setTimeout(() => this.nextStep(), 400);
    },
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    },

    // Atualizar Progresso com feedback
    updateProgress: function(value) {
        this.data.percentual = parseInt(value);
        document.getElementById('percentual_concluido').value = value;
        document.getElementById('progressValue').textContent = value + '%';

        const progressValue = document.getElementById('progressValue');
        const progressSlider = document.getElementById('progressSlider');

        // Atualizar cor baseado no valor e tipo
        const coresTipo = {
            'problema': { baixo: '#e74c3c', medio: '#e74c3c', alto: '#3498db', completo: '#27ae60' },
            'observacao': { baixo: '#f39c12', medio: '#f39c12', alto: '#3498db', completo: '#27ae60' },
            'execucao': { baixo: '#667eea', medio: '#3498db', alto: '#3498db', completo: '#27ae60' }
        };

        const cores = coresTipo[this.data.tipo] || coresTipo['execucao'];

        if (value == 100) {
            progressValue.style.color = cores.completo;
            progressValue.innerHTML = value + '% <i class="bx bx-party" style="margin-left:8px;"></i>';
            this.showToast('Progresso completo! Atividade concluída.', 'success');
        } else if (value >= 75) {
            progressValue.style.color = cores.alto;
            progressValue.textContent = value + '%';
        } else if (value >= 50) {
            progressValue.style.color = cores.medio;
            progressValue.textContent = value + '%';
        } else {
            progressValue.style.color = cores.baixo;
            progressValue.textContent = value + '%';
        }

        // Feedback visual no slider
        progressSlider.style.background = `linear-gradient(to right, ${progressValue.style.color} 0%, ${progressValue.style.color} ${value}%, #e0e0e0 ${value}%, #e0e0e0 100%)`;
    },

    // Navegacao melhorada
    nextStep: function() {
        if (this.currentStep < this.totalSteps) {
            if (!this.validateStep(this.currentStep)) return;

            // Animacao de transicao
            const currentContent = document.querySelector(`.wizard-step-content[data-step="${this.currentStep}"]`);
            if (currentContent) {
                currentContent.style.animation = 'fadeOut 0.2s ease';
            }

            setTimeout(() => {
                this.currentStep++;
                this.updateUI();
            }, 200);
        }
    },

    prevStep: function() {
        if (this.currentStep > 1) {
            const currentContent = document.querySelector(`.wizard-step-content[data-step="${this.currentStep}"]`);
            if (currentContent) {
                currentContent.style.animation = 'fadeOutRight 0.2s ease';
            }

            setTimeout(() => {
                this.currentStep--;
                this.updateUI();
            }, 200);
        }
    },

    validateStep: function(step) {
        switch(step) {
            case 1: // Etapa - sempre valido (pode ser vazio)
                return true;
            case 2: // Atividade - obrigatorio (existente ou nova)
                if (!this.data.atividade_id && !this.data.is_nova_atividade) {
                    this.showToast('Por favor, selecione uma atividade existente ou crie uma nova', 'warning');
                    return false;
                }
                return true;
            case 3: // Tipo - obrigatorio
                if (!this.data.tipo) {
                    this.showToast('Por favor, selecione o tipo de atividade', 'warning');
                    return false;
                }
                return true;
            case 4: // Descricao - obrigatoria
                const descricao = document.getElementById('descricao').value.trim();
                if (!descricao || descricao.length < 10) {
                    this.showToast('Por favor, descreva a atividade com pelo menos 10 caracteres', 'warning');
                    document.getElementById('descricao').focus();
                    document.getElementById('descricao').style.borderColor = '#e74c3c';
                    setTimeout(() => {
                        document.getElementById('descricao').style.borderColor = '';
                    }, 2000);
                    return false;
                }
                this.data.descricao = descricao;
                return true;
            case 5: // Fotos - sempre valido (opcional)
                return true;
            case 6: // Progresso - sempre valido
                return true;
            case 7: // Resumo - sempre valido
                return true;
            default:
                return true;
        }
    },

    // Handler para selecao de fotos - usa processPhotoFiles
    handlePhotoSelect: function(event) {
        const files = Array.from(event.target.files);
        this.processPhotoFiles(files);
        event.target.value = '';
    },

    removePhoto: function(index) {
        const foto = this.data.fotos[index];
        this.data.fotos.splice(index, 1);
        this.updatePhotoPreview();
        this.showToast(`Foto removida: ${foto?.name || 'imagem'}`, 'info');
    },

    updatePhotoPreview: function() {
        const previewContainer = document.getElementById('photosPreview');
        const uploadArea = document.getElementById('photoUploadArea');
        const photoCount = document.getElementById('photoCount');

        previewContainer.innerHTML = '';

        const count = this.data.fotos.length;
        if (count > 0) {
            photoCount.style.display = 'inline-block';
            photoCount.innerHTML = `<i class='bx bx-image'></i> ${count} foto${count > 1 ? 's' : ''} anexada${count > 1 ? 's' : ''}`;
            uploadArea.classList.add('has-photos');
            uploadArea.innerHTML = `
                <i class='bx bx-plus-circle'></i>
                <div class="photo-upload-text">Adicionar mais fotos</div>
                <div class="photo-upload-hint">Toque aqui ou arraste imagens</div>
            `;
        } else {
            photoCount.style.display = 'none';
            uploadArea.classList.remove('has-photos');
            uploadArea.innerHTML = `
                <i class='bx bx-image-add'></i>
                <div class="photo-upload-text">Toque para selecionar fotos</div>
                <div class="photo-upload-hint">
                    <i class='bx bx-mobile-alt'></i> Galeria <i class='bx bx-camera' style="margin-left: 10px;"></i> Câmera
                </div>
            `;
        }

        this.data.fotos.forEach((foto, index) => {
            const div = document.createElement('div');
            div.className = 'photo-preview-item';
            div.style.animation = 'fadeInScale 0.3s ease';
            div.innerHTML = `
                <img src="${foto.preview}" alt="Foto ${index + 1}" loading="lazy">
                <button type="button" class="photo-remove-btn" onclick="wizard.removePhoto(${index})" title="Remover foto">
                    <i class='bx bx-x'></i>
                </button>
                <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.6);color:white;font-size:11px;padding:4px 8px;border-radius:0 0 12px 12px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    ${foto.name}
                </div>
            `;
            previewContainer.appendChild(div);
        });
    },

    updateUI: function() {
        // Esconder todos os steps
        document.querySelectorAll('.wizard-step-content').forEach(content => {
            content.classList.remove('active');
            content.style.animation = '';
        });

        // Mostrar step atual
        const currentStepEl = document.querySelector(`.wizard-step-content[data-step="${this.currentStep}"]`);
        if (currentStepEl) {
            currentStepEl.classList.add('active');
            currentStepEl.style.animation = 'fadeIn 0.4s ease';
        }

        // Atualizar indicadores de step
        document.querySelectorAll('.step').forEach((step, index) => {
            const stepNum = index + 1;
            step.classList.remove('active', 'completed');
            if (stepNum === this.currentStep) {
                step.classList.add('active');
            } else if (stepNum < this.currentStep) {
                step.classList.add('completed');
                step.innerHTML = '<i class="bx bx-check"></i>';
            } else {
                step.textContent = stepNum;
            }
        });

        // Atualizar linhas de progresso
        document.querySelectorAll('.step-line').forEach((line, index) => {
            const stepNum = index + 1;
            line.classList.toggle('completed', stepNum < this.currentStep);
        });

        // Atualizar botoes
        const btnVoltar = document.getElementById('btnVoltar');
        const btnAvancar = document.getElementById('btnAvancar');
        const btnFinalizar = document.getElementById('btnFinalizar');

        btnVoltar.disabled = this.currentStep === 1;
        btnVoltar.style.opacity = this.currentStep === 1 ? '0.5' : '1';

        if (this.currentStep === this.totalSteps) {
            this.atualizarResumo();
            btnAvancar.style.display = 'none';
            btnFinalizar.style.display = 'flex';
        } else {
            btnAvancar.style.display = 'flex';
            btnFinalizar.style.display = 'none';

            // Atualizar texto do botão avançar
            if (this.currentStep === 1) {
                btnAvancar.innerHTML = 'Pular Etapa <i class="bx bx-chevron-right"></i>';
            } else {
                btnAvancar.innerHTML = 'Avançar <i class="bx bx-chevron-right"></i>';
            }
        }
    },

    atualizarResumo: function() {
        document.getElementById('resumoEtapa').textContent = this.data.etapa_nome;

        // Resumo da atividade
        const resumoAtividade = document.getElementById('resumoAtividade');
        if (this.data.atividade_id) {
            resumoAtividade.innerHTML = `<i class='bx bx-check-circle' style="color: #27ae60; margin-right: 4px;"></i>
                ${this.escapeHtml(this.data.atividade_nome.substring(0, 40))}${this.data.atividade_nome.length > 40 ? '...' : ''}
                <span style="font-size: 11px; color: #888; margin-left: 8px;">(Existente)</span>`;
        } else if (this.data.is_nova_atividade) {
            resumoAtividade.innerHTML = `<i class='bx bx-plus-circle' style="color: #667eea; margin-right: 4px;"></i>
                ${this.escapeHtml(this.data.atividade_nome.substring(0, 40))}${this.data.atividade_nome.length > 40 ? '...' : ''}
                <span style="font-size: 11px; color: #888; margin-left: 8px;">(Nova)</span>`;
        } else {
            resumoAtividade.textContent = '-';
        }

        // Resumo de tipo especial (fora do escopo)
        let tipoTexto = this.data.tipo_nome || '-';
        if (this.data.tipo === 'fora_do_escopo') {
            tipoTexto += ' <span style="color: #ff6b6b; font-size: 11px;">(Nao Planejada)</span>';
        }
        document.getElementById('resumoTipo').innerHTML = tipoTexto;

        // Resumo de materiais (apenas para atividades fora do escopo)
        let resumoMateriaisEl = document.getElementById('resumoMateriais');
        if (this.data.tipo === 'fora_do_escopo') {
            if (!resumoMateriaisEl) {
                // Criar elemento dinamicamente
                const resumoBox = document.getElementById('resumoBox');
                const novoItem = document.createElement('div');
                novoItem.className = 'resumo-item';
                novoItem.id = 'resumoMateriaisItem';
                novoItem.innerHTML = `
                    <span class="resumo-label">Materiais:</span>
                    <span class="resumo-value" id="resumoMateriais">-</span>
                `;
                // Inserir antes do Progresso
                const progressoItem = resumoBox.querySelector('.resumo-item:nth-child(5)');
                if (progressoItem) {
                    resumoBox.insertBefore(novoItem, progressoItem);
                }
                resumoMateriaisEl = document.getElementById('resumoMateriais');
            }

            let materiaisTexto = '';
            if (this.data.materiais.length > 0) {
                materiaisTexto = `${this.data.materiais.length} item(s)`;
                const totalValor = this.data.materiais.reduce((sum, m) => sum + ((m.valor || 0) * m.qtd), 0);
                if (totalValor > 0) {
                    materiaisTexto += ` | Total: R$ ${totalValor.toFixed(2)}`;
                }
            } else {
                materiaisTexto = 'Nenhum material registrado';
            }

            // Adicionar solicitante
            if (this.data.solicitante) {
                materiaisTexto += `<br><span style="font-size: 12px; color: #666;">Solicitante: ${this.escapeHtml(this.data.solicitante)}</span>`;
            }

            resumoMateriaisEl.innerHTML = materiaisTexto;
        } else if (resumoMateriaisEl) {
            // Remover elemento se nao for fora do escopo
            const item = document.getElementById('resumoMateriaisItem');
            if (item) item.remove();
        }

        // Resumo de fotos com ícone
        const resumoFotos = document.getElementById('resumoFotos');
        if (this.data.fotos.length > 0) {
            resumoFotos.innerHTML = `<i class='bx bx-image' style="color: #667eea; margin-right: 4px;"></i> ${this.data.fotos.length} foto${this.data.fotos.length > 1 ? 's' : ''}`;
            resumoFotos.style.color = '#667eea';
        } else {
            resumoFotos.textContent = 'Nenhuma foto anexada';
            resumoFotos.style.color = '#999';
        }

        document.getElementById('resumoProgresso').textContent = this.data.percentual + '%';
        document.getElementById('resumoDescricao').textContent = this.data.descricao || '-';

        // Cores no resumo
        const resumoProgresso = document.getElementById('resumoProgresso');
        if (this.data.percentual == 100) {
            resumoProgresso.style.color = '#27ae60';
        } else if (this.data.tipo === 'problema') {
            resumoProgresso.style.color = '#e74c3c';
        } else if (this.data.tipo === 'observacao') {
            resumoProgresso.style.color = '#f39c12';
        } else if (this.data.tipo === 'fora_do_escopo') {
            resumoProgresso.style.color = '#ff6b6b';
        } else {
            resumoProgresso.style.color = '#667eea';
        }
    },

    submitForm: async function() {
        if (this.isSubmitting) return;

        const btnFinalizar = document.getElementById('btnFinalizar');
        const alertSuccess = document.getElementById('alertSuccess');
        const alertError = document.getElementById('alertError');
        const errorMessage = document.getElementById('errorMessage');

        this.isSubmitting = true;
        btnFinalizar.disabled = true;
        btnFinalizar.innerHTML = '<span class="loading"></span> Enviando...';

        alertSuccess.style.display = 'none';
        alertError.style.display = 'none';

        try {
            // Preparar FormData
            const formData = new FormData();
            formData.append('obra_id', document.querySelector('input[name="obra_id"]').value);
            formData.append('etapa_id', this.data.etapa_id);
            formData.append('atividade_id', this.data.atividade_id);
            if (this.data.is_nova_atividade && this.data.atividade_nome) {
                formData.append('titulo', this.data.atividade_nome);
            }
            formData.append('tipo', this.data.tipo);
            formData.append('descricao', this.data.descricao);
            formData.append('percentual_concluido', this.data.percentual);

            // Adicionar dados de materiais (se for atividade fora do escopo)
            if (this.data.tipo === 'fora_do_escopo') {
                formData.append('is_foradoscopo', '1');
                formData.append('solicitante', this.data.solicitante || '');
                formData.append('justificativa', this.data.justificativa || '');
                if (this.data.materiais.length > 0) {
                    formData.append('materiais', JSON.stringify(this.data.materiais));
                }
            }

            // Adicionar fotos
            this.data.fotos.forEach((foto, index) => {
                formData.append(`foto_${index}`, foto.file);
            });

            const response = await fetch('<?= site_url("tecnicos/api_registrar_atividade_obra") ?>', {
                method: 'POST',
                body: formData
            });

            // Verificar se a resposta é JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Resposta não-JSON recebida:', text.substring(0, 500));
                throw new Error('Resposta inválida do servidor. Verifique se está logado.');
            }

            const result = await response.json();

            if (result.success) {
                alertSuccess.style.display = 'block';
                alertSuccess.classList.add('show');
                document.getElementById('resumoBox').style.display = 'none';
                document.querySelector('.step-subtitle').textContent = 'Atividade registrada com sucesso!';

                this.showToast('Atividade registrada com sucesso!', 'success');

                // Recarregar após 2 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error(result.message || 'Erro ao registrar atividade');
            }
        } catch (error) {
            console.error('Erro:', error);

            errorMessage.textContent = error.message || 'Erro de conexão. Tente novamente.';
            alertError.style.display = 'block';
            alertError.classList.add('show');

            this.showToast(error.message || 'Erro ao enviar. Tente novamente.', 'error');

            btnFinalizar.disabled = false;
            btnFinalizar.innerHTML = '<i class="bx bx-revision"></i> Tentar Novamente';
            this.isSubmitting = false;
        }
    },

    // Pular wizard
    pularWizard: function() {
        if (confirm('Deseja cancelar o registro de atividade?')) {
            document.getElementById('wizardAtividade').style.display = 'none';
        }
    },

    // Reiniciar wizard
    reset: function() {
        this.currentStep = 1;
        this.data = {
            etapa_id: '',
            etapa_nome: '-',
            tipo: '',
            tipo_nome: '',
            descricao: '',
            percentual: 0,
            fotos: []
        };
        this.isSubmitting = false;
        document.getElementById('formAtividade').reset();
        this.updateUI();
        this.updatePhotoPreview();
    }
};

// CSS adicional para animacoes
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }
    @keyframes fadeOutRight {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(10px); }
    }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }
    .wizard-step-content { display: none; }
    .wizard-step-content.active { display: block; }
`;
document.head.appendChild(style);

// Inicializar quando carregar
document.addEventListener('DOMContentLoaded', function() {
    wizard.init();
});
</script>
