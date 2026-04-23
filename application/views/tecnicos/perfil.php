<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Meta tags adicionais para mobile -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="format-detection" content="telephone=no">

<!-- Perfil do Técnico - Design Moderno -->
<style>
/* Container Principal */
.perfil-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

/* Header do Perfil */
.perfil-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.perfil-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.3; }
}

/* Avatar */
.perfil-avatar-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}

.perfil-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 5px solid rgba(255,255,255,0.3);
    overflow: hidden;
    background: white;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 30px rgba(0,0,0,0.2);
}

.perfil-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 40px rgba(0,0,0,0.3);
}

.perfil-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e7f1 100%);
    color: #667eea;
}

.avatar-placeholder i {
    font-size: 60px;
}

.avatar-edit-badge {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 40px;
    height: 40px;
    background: #27ae60;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    border: 3px solid white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    z-index: 10;
}

.avatar-edit-badge:hover {
    background: #229954;
    transform: scale(1.1);
}

/* Nome e Nível */
.perfil-nome {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    position: relative;
    z-index: 1;
}

.perfil-nivel {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.2);
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.perfil-nivel i {
    color: #ffd700;
}

/* Cards de Seção */
.perfil-section {
    background: white;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.perfil-section:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0;
}

/* Grid de Info */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
    transition: all 0.3s ease;
}

.info-card:hover {
    background: #e8f4f8;
    transform: translateY(-3px);
}

.info-card-icon {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
    font-size: 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    flex-shrink: 0;
}

.info-card-content {
    flex: 1;
    min-width: 0;
}

.info-card-label {
    font-size: 12px;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.info-card-value {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    word-break: break-word;
}

/* Especialidades */
.especialidades-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.especialidade-tag {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.especialidade-tag i {
    font-size: 12px;
}

/* Stats Cards */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e8e8e8 100%);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-card.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.stat-card.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
.stat-card.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }

.stat-value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 13px;
    opacity: 0.9;
}

/* Plantão Badge */
.plantao-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
}

.plantao-badge.ativo {
    background: #d4edda;
    color: #155724;
}

.plantao-badge.inativo {
    background: #f8f9fa;
    color: #666;
}

/* Botão Sair */
.btn-logout {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(238, 90, 90, 0.3);
}

.btn-logout:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(238, 90, 90, 0.4);
    text-decoration: none;
    color: white;
}

/* Modal de Foto - Base */
.foto-modal {
    max-width: 600px;
    margin: 0 auto;
}

.foto-modal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 6px 6px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.foto-modal .modal-header h3 {
    margin: 0;
    font-weight: 600;
    font-size: 16px;
}

.foto-modal .nav-tabs {
    border-bottom: 2px solid #eee;
    margin-bottom: 20px;
}

.foto-modal .nav-tabs > li > a {
    padding: 15px 25px;
    font-weight: 600;
    color: #666;
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    gap: 8px;
}

.foto-modal .nav-tabs > li.active > a {
    color: #667eea;
    border-bottom: 3px solid #667eea;
    background: #f8f9ff;
}

.foto-preview-area {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    margin-bottom: 20px;
    border: 2px dashed #ddd;
    transition: all 0.3s ease;
}

.foto-preview-area:hover {
    border-color: #667eea;
    background: #f0f4ff;
}

.foto-preview-area i {
    font-size: 48px;
    color: #667eea;
    margin-bottom: 15px;
    display: block;
}

.foto-preview-area p {
    color: #888;
    margin: 0;
}

.video-container {
    position: relative;
    display: inline-block;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

#video {
    max-width: 100%;
    max-height: 300px;
    display: block;
}

.btn-capturar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 15px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.btn-capturar:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-capturar:active {
    transform: scale(0.95);
}

.btn-capturar:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

#preview-img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

/* Botão Editar */
.btn-editar {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    color: white;
    border: 2px solid rgba(255,255,255,0.3);
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    z-index: 10;
}

.btn-editar:hover {
    background: rgba(255,255,255,0.3);
    border-color: white;
}

/* Modal de Edição */
.modal-edicao .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 6px 6px 0 0;
}

.modal-edicao .modal-header h3 {
    margin: 0;
    font-weight: 600;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 13px;
    color: #666;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
    -webkit-appearance: none;
    -webkit-tap-highlight-color: transparent;
    min-height: 44px;
}

.form-input:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.abas-navegacao {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    border-bottom: 2px solid #e8e8e8;
    padding-bottom: 10px;
}

.aba-btn {
    padding: 10px 20px;
    border: none;
    background: transparent;
    color: #666;
    font-weight: 600;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.3s ease;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

.aba-btn.active,
.aba-btn:hover {
    background: #f0f4ff;
    color: #667eea;
}

.aba-btn:active {
    transform: scale(0.98);
}

.aba-conteudo {
    display: none;
}

.aba-conteudo.active {
    display: block;
}

.btn-salvar {
    background: linear-gradient(135deg, #27ae60, #229954);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-salvar:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
}

.msg-sucesso {
    background: #d4edda;
    color: #155724;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
}

.msg-erro {
    background: #f8d7da;
    color: #721c24;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
}
@media (max-width: 768px) {
    .perfil-container {
        padding: 15px;
    }

    .perfil-header {
        padding: 30px 20px;
        border-radius: 16px;
    }

    .perfil-avatar {
        width: 120px;
        height: 120px;
    }

    .perfil-nome {
        font-size: 22px;
    }

    .stats-row {
        grid-template-columns: 1fr;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    /* Modal Edição Mobile */
    .modal-edicao {
        width: 100% !important;
        left: 0 !important;
        right: 0 !important;
        margin: 0 !important;
        top: 0 !important;
        bottom: 0 !important;
        border-radius: 0 !important;
        position: fixed !important;
    }

    .modal-edicao .modal-body {
        padding: 15px !important;
        max-height: calc(100vh - 140px);
        overflow-y: auto;
        padding-bottom: 80px !important;
    }

    .modal-edicao .modal-header {
        padding: 15px 20px;
    }

    .modal-edicao .modal-header h3 {
        font-size: 16px;
    }

    .modal-edicao .modal-footer {
        padding: 15px 20px;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #e8e8e8;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
        z-index: 10;
    }

    .abas-navegacao {
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 15px;
    }

    .aba-btn {
        padding: 8px 12px;
        font-size: 13px;
        flex: 1;
        min-width: 120px;
    }

    .aba-btn i {
        display: none;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        font-size: 12px;
    }

    .form-input {
        padding: 12px 15px;
        font-size: 16px;
        min-height: 44px;
        border-radius: 8px;
    }

    .btn-salvar {
        width: 100%;
        padding: 14px 20px;
        font-size: 16px;
    }

    .msg-sucesso,
    .msg-erro {
        padding: 10px 15px;
        font-size: 13px;
    }

    .btn-editar {
        padding: 8px 15px;
        font-size: 13px;
    }

    .btn-editar i {
        display: none;
    }

    /* Botão editar mais visível no mobile */
    .btn-editar {
        position: relative !important;
        top: auto !important;
        right: auto !important;
        display: block !important;
        width: 100% !important;
        margin-top: 15px !important;
        background: rgba(255,255,255,0.25) !important;
        border: 2px solid rgba(255,255,255,0.4) !important;
        text-align: center;
    }

    .btn-editar:active {
        background: rgba(255,255,255,0.35) !important;
        transform: scale(0.98);
    }
}

@media (max-width: 480px) {
    .perfil-avatar {
        width: 100px;
        height: 100px;
    }

    .section-header {
        flex-direction: column;
        text-align: center;
    }

    .modal-edicao .modal-header h3 {
        font-size: 14px;
    }

    .aba-btn {
        font-size: 12px;
        padding: 6px 10px;
    }

    /* ===== MODAL FOTO MOBILE ===== */
    .foto-modal {
        width: 100% !important;
        left: 0 !important;
        right: 0 !important;
        margin: 0 !important;
        top: 0 !important;
        bottom: 0 !important;
        border-radius: 0 !important;
        position: fixed !important;
    }

    .foto-modal .modal-header {
        padding: 15px 20px;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .foto-modal .modal-header h3 {
        font-size: 16px;
    }

    .foto-modal .modal-body {
        padding: 15px !important;
        height: calc(100vh - 120px);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .foto-modal .modal-footer {
        padding: 15px 20px;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #e8e8e8;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
        z-index: 100;
        display: flex;
        gap: 10px;
    }

    .foto-modal .modal-footer .btn {
        flex: 1;
        padding: 14px 20px;
        font-size: 16px;
        min-height: 48px;
    }

    .foto-modal .modal-footer .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    /* Abas no mobile */
    .foto-modal .nav-tabs {
        display: flex;
        margin: -15px -15px 15px -15px;
        border-bottom: 2px solid #eee;
    }

    .foto-modal .nav-tabs > li {
        flex: 1;
        text-align: center;
    }

    .foto-modal .nav-tabs > li > a {
        padding: 15px 10px;
        font-size: 14px;
        display: block;
    }

    .foto-modal .nav-tabs > li > a i {
        display: block;
        font-size: 20px;
        margin-bottom: 5px;
    }

    /* Preview area mobile */
    .foto-preview-area {
        padding: 30px 20px;
        margin: 0 -15px 15px -15px;
        border-radius: 0;
        border-left: none;
        border-right: none;
    }

    .foto-preview-area i {
        font-size: 56px;
    }

    .foto-preview-area p {
        font-size: 14px;
        margin-bottom: 15px;
    }

    /* Botão capturar maior para touch */
    .btn-capturar {
        width: 100%;
        padding: 16px 24px;
        font-size: 16px;
        border-radius: 10px;
        margin-top: 20px;
    }

    /* Esconder botão flutuante no mobile por padrão */
    .btn-captura-mobile {
        display: none;
    }

    /* Container de vídeo mobile */
    .video-container {
        width: 100%;
        border-radius: 0;
        margin: 0 -15px;
        width: calc(100% + 30px);
    }

    #video {
        width: 100%;
        max-height: 60vh;
        object-fit: cover;
    }

    /* Botão tirar foto flutuante no mobile */
    #camera-on {
        position: relative;
    }

    #camera-on .btn-capturar {
        position: fixed;
        bottom: 80px;
        left: 50%;
        transform: translateX(-50%);
        width: calc(100% - 40px);
        max-width: 300px;
        z-index: 50;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        border-radius: 30px;
        font-size: 18px;
        padding: 18px 30px;
    }

    #camera-on .btn-capturar:active {
        transform: translateX(-50%) scale(0.95);
    }

    /* Botão de captura flutuante */
    .btn-captura-mobile {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: white;
        border: 4px solid #667eea;
        color: #667eea;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        z-index: 10;
        -webkit-tap-highlight-color: transparent;
    }

    .btn-captura-mobile:active {
        transform: translateX(-50%) scale(0.9);
        background: #f0f4ff;
    }

    /* Esconder botão desktop no mobile */
    .btn-capturar-desktop {
        display: none !important;
    }

    /* Preview upload mobile */
    #upload-preview {
        padding-bottom: 60px;
    }

    #preview-img {
        max-height: 50vh;
        width: 100%;
        object-fit: contain;
    }

    #upload-preview .btn {
        width: 100%;
        padding: 14px 20px;
        font-size: 16px;
        margin-top: 15px;
    }
}

@media (max-width: 380px) {
    .foto-modal .nav-tabs > li > a {
        font-size: 12px;
        padding: 12px 8px;
    }

    .foto-modal .nav-tabs > li > a i {
        font-size: 18px;
    }

    .foto-preview-area i {
        font-size: 48px;
    }
}

/* Desktop - esconder botão flutuante */
@media (min-width: 769px) {
    .btn-captura-mobile {
        display: none !important;
    }

    .btn-capturar-desktop {
        display: inline-flex !important;
    }
}

/* ===== AJUSTES iOS ESPECÍFICOS ===== */
@supports (-webkit-touch-callout: none) {
    /* iOS Safari específico */
    .foto-modal .modal-body {
        -webkit-overflow-scrolling: touch;
    }

    .btn-capturar, .btn-captura-mobile {
        -webkit-tap-highlight-color: transparent;
    }

    /* Fix para câmera no iOS */
    #video {
        object-fit: cover;
        width: 100% !important;
        height: auto !important;
    }

    /* Fix para input file no iOS */
    #input-foto {
        font-size: 16px; /* Evita zoom no iOS */
    }

    /* Melhorar área de preview no iOS */
    .foto-preview-area {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
    }
}
</style>

<div class="perfil-container">
    <!-- Header com Avatar -->
    <div class="perfil-header">
        <button class="btn-editar" onclick="abrirModalEdicao()">
            <i class="icon-pencil"></i> Editar Perfil
        </button>

        <div class="perfil-avatar-wrapper">
            <div class="perfil-avatar" onclick="abrirCamera()">
                <?php if (!empty($tecnico->foto_tecnico) && file_exists(FCPATH . $tecnico->foto_tecnico)): ?>
                    <img src="<?php echo base_url($tecnico->foto_tecnico); ?>?v=<?php echo time(); ?>" alt="Foto">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <i class="icon-user"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="avatar-edit-badge" onclick="abrirCamera()" title="Alterar Foto">
                <i class="icon-camera"></i>
            </div>
        </div>

        <h3 class="perfil-nome"><?php echo htmlspecialchars($tecnico->nome ?? 'Técnico', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></h3>

        <div class="perfil-nivel">
            <i class="icon-star"></i>
            <span>Técnico Nível <?php echo $tecnico->nivel_tecnico ?? 1; ?></span>
        </div>
    </div>

    <!-- Informações Pessoais -->
    <div class="perfil-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="icon-user"></i>
            </div>
            <h4 class="section-title">Informações Pessoais</h4>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon-envelope"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">E-mail</div>
                    <div class="info-card-value"><?php echo htmlspecialchars($tecnico->email ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon-phone"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Telefone</div>
                    <div class="info-card-value"><?php echo htmlspecialchars($tecnico->telefone ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon-credit-card"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">CPF</div>
                    <div class="info-card-value"><?php echo htmlspecialchars($tecnico->cpf ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações Profissionais -->
    <div class="perfil-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="icon-wrench"></i>
            </div>
            <h4 class="section-title">Informações Profissionais</h4>
        </div>

        <div class="info-grid">
            <?php if (!empty($tecnico->especialidades)): ?>
            <div class="info-card" style="grid-column: 1 / -1;">
                <div class="info-card-icon">
                    <i class="icon-lightbulb"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Especialidades</div>
                    <div class="especialidades-list">
                        <?php foreach (explode(',', $tecnico->especialidades) as $esp): ?>
                            <span class="especialidade-tag">
                                <i class="icon-ok"></i>
                                <?php echo trim(htmlspecialchars($esp, ENT_COMPAT | ENT_HTML5, 'UTF-8')); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($tecnico->veiculo_placa): ?>
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon-truck"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Veículo</div>
                    <div class="info-card-value">
                        <?php echo htmlspecialchars(($tecnico->veiculo_tipo ?? '') . ' - ' . ($tecnico->veiculo_placa ?? ''), ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon-time"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Plantão 24h</div>
                    <div class="info-card-value">
                        <span class="plantao-badge <?php echo ($tecnico->plantao_24h ?? 0) ? 'ativo' : 'inativo'; ?>">
                            <i class="icon <?php echo ($tecnico->plantao_24h ?? 0) ? 'icon-ok' : 'icon-remove'; ?>"></i>
                            <?php echo ($tecnico->plantao_24h ?? 0) ? 'Disponível' : 'Indisponível'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="stats-row">
            <div class="stat-card blue">
                <div class="stat-value">0</div>
                <div class="stat-label">OS Hoje</div>
            </div>
            <div class="stat-card green">
                <div class="stat-value">0</div>
                <div class="stat-label">OS Semana</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-value">0</div>
                <div class="stat-label">OS Mês</div>
            </div>
        </div>
    </div>

    <!-- Configurações -->
    <div class="perfil-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="icon-cog"></i>
            </div>
            <h4 class="section-title">Configurações</h4>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon-time"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Último Acesso</div>
                    <div class="info-card-value">
                        <?php echo ($tecnico->ultimo_acesso_app ?? false) ? date('d/m/Y H:i', strtotime($tecnico->ultimo_acesso_app)) : 'Nunca acessou'; ?>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon-mobile-phone"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Versão do App</div>
                    <div class="info-card-value">v1.0.0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão Sair -->
    <a href="<?php echo site_url('tecnicos/logout'); ?>" class="btn-logout">
        <i class="icon-signout"></i>
        <span>Sair do Sistema</span>
    </a>
</div>

<!-- Modal de Foto -->
<div class="modal hide foto-modal" id="cameraModal">
    <div class="modal-header">
        <button type="button" class="close" onclick="fecharCamera()" style="color: white; opacity: 0.8;">&times;</button>
        <h3><i class="icon-camera"></i> Atualizar Foto de Perfil</h3>
    </div>
    <div class="modal-body">
        <!-- Abas -->
        <ul class="nav nav-tabs" id="fotoTab">
            <li class="active"><a href="#tab-camera" data-toggle="tab"><i class="icon-camera"></i> Câmera</a></li>
            <li><a href="#tab-upload" data-toggle="tab"><i class="icon-upload"></i> Galeria</a></li>
        </ul>

        <div class="tab-content">
            <!-- Aba Câmera -->
            <div class="tab-pane active" id="tab-camera">
                <div class="foto-preview-area" id="camera-off">
                    <i class="icon-camera"></i>
                    <p>Clique no botão abaixo para iniciar a câmera</p>
                    <button type="button" class="btn-capturar" onclick="iniciarCamera()">
                        <i class="icon-camera"></i> Iniciar Câmera
                    </button>
                </div>
                <div class="text-center" id="camera-on" style="display: none;">
                    <div class="video-container">
                        <video id="video" autoplay playsinline></video>
                        <!-- Botão de captura flutuante no mobile -->
                        <button type="button" class="btn-captura-mobile" onclick="capturarDaCamera()" title="Tirar Foto">
                            <i class="icon-camera"></i>
                        </button>
                    </div>
                    <!-- Botão fallback desktop -->
                    <button type="button" class="btn-capturar btn-capturar-desktop" onclick="capturarDaCamera()">
                        <i class="icon-camera"></i> Tirar Foto
                    </button>
                </div>
            </div>

            <!-- Aba Upload -->
            <div class="tab-pane" id="tab-upload">
                <div class="foto-preview-area" id="upload-placeholder">
                    <i class="icon-picture"></i>
                    <p>Selecione uma foto da galeria ou tire uma foto agora</p>
                    <input type="file" id="input-foto" accept="image/*" style="display: none;" onchange="previewUpload(this)">

                    <!-- Botão Galeria -->
                    <button type="button" class="btn-capturar" onclick="selecionarArquivo('gallery')" style="margin-bottom: 10px;">
                        <i class="icon-folder-open"></i> Abrir Galeria
                    </button>

                    <!-- Botão Câmera (fallback para mobile) -->
                    <button type="button" class="btn-capturar" onclick="selecionarArquivo('camera')" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <i class="icon-camera"></i> Tirar Foto Agora
                    </button>
                </div>
                <div class="text-center" id="upload-preview" style="display: none;">
                    <img id="preview-img" src="" alt="Preview">
                    <br><br>
                    <button type="button" class="btn" onclick="resetUpload()">
                        <i class="icon-remove"></i> Escolher Outra
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" onclick="fecharCamera()">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-salvar-foto" onclick="salvarFoto()" disabled>
            <i class="icon-ok"></i> Salvar Foto
        </button>
    </div>
</div>

<?php
// Dados para JavaScript
$csrf_token_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
$url_atualizar_foto = site_url("tecnicos/atualizar_foto");
?>

<script type="text/javascript">
// Variáveis globais
var stream = null;
var fotoCapturada = null;
var URL_ATUALIZAR_FOTO = '<?php echo $url_atualizar_foto; ?>';
var CSRF_TOKEN_NAME = '<?php echo $csrf_token_name; ?>';
var CSRF_HASH = '<?php echo $csrf_hash; ?>';

// Abrir modal de foto
function abrirCamera() {
    fotoCapturada = null;
    document.getElementById('btn-salvar-foto').disabled = true;

    // Resetar abas
    jQuery('#fotoTab a:first').tab('show');

    // Resetar câmera
    document.getElementById('camera-off').style.display = 'block';
    document.getElementById('camera-on').style.display = 'none';

    // Resetar upload
    document.getElementById('upload-placeholder').style.display = 'block';
    document.getElementById('upload-preview').style.display = 'none';
    document.getElementById('input-foto').value = '';

    // Detectar mobile e ajustar comportamento inicial
    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    // Prevenir scroll do body quando modal está aberto
    document.body.style.overflow = 'hidden';

    jQuery('#cameraModal').modal('show');

    // No mobile, pré-carregar aba galeria se câmera não estiver disponível
    if (isMobile) {
        setTimeout(function() {
            // Verificar se câmera pode estar bloqueada
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                // Mudar para aba galeria automaticamente
                jQuery('#fotoTab a[href="#tab-upload"]').tab('show');
            }
        }, 100);
    }
}

// Selecionar arquivo da galeria ou câmera (para mobile)
function selecionarArquivo(tipo) {
    var input = document.getElementById('input-foto');

    if (tipo === 'camera') {
        // Tentar usar capture no mobile
        input.setAttribute('capture', 'environment');
    } else {
        // Remover capture para galeria
        input.removeAttribute('capture');
    }

    // Trigger click
    input.click();
}

// Iniciar câmera - otimizado para mobile
function iniciarCamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Seu navegador não suporta acesso à câmera. Tente usar a opção "Galeria".');
        // Mudar automaticamente para aba galeria
        jQuery('#fotoTab a[href="#tab-upload"]').tab('show');
        return;
    }

    // Detectar se é mobile
    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    var constraints = {
        video: {
            facingMode: isMobile ? 'environment' : 'user', // Câmera traseira no mobile
            width: { ideal: 1280 },
            height: { ideal: 720 }
        }
    };

    // Mostrar loading
    var btnIniciar = document.querySelector('#camera-off .btn-capturar');
    if (btnIniciar) {
        btnIniciar.innerHTML = '<i class="icon-spinner icon-spin"></i> Iniciando...';
        btnIniciar.disabled = true;
    }

    navigator.mediaDevices.getUserMedia(constraints)
    .then(function(mediaStream) {
        stream = mediaStream;
        var video = document.getElementById('video');
        if (video) {
            video.srcObject = stream;

            // Esperar o vídeo estar pronto
            video.onloadedmetadata = function() {
                document.getElementById('camera-off').style.display = 'none';
                document.getElementById('camera-on').style.display = 'block';

                // Fullscreen no mobile
                if (isMobile && video.requestFullscreen) {
                    video.requestFullscreen().catch(function(err) {
                        // Ignora erro de fullscreen
                    });
                }

                habilitarSalvar();
            };
        }
    }).catch(function(err) {
        console.error('Erro ao abrir câmera:', err);

        // Tentar câmera frontal se a traseira falhar
        if (isMobile) {
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user' }
            }).then(function(mediaStream) {
                stream = mediaStream;
                var video = document.getElementById('video');
                if (video) {
                    video.srcObject = stream;
                    video.onloadedmetadata = function() {
                        document.getElementById('camera-off').style.display = 'none';
                        document.getElementById('camera-on').style.display = 'block';
                        habilitarSalvar();
                    };
                }
            }).catch(function() {
                alert('Não foi possível acessar a câmera. Por favor, use a opção "Galeria" para selecionar uma foto.');
                jQuery('#fotoTab a[href="#tab-upload"]').tab('show');
            });
        } else {
            alert('Não foi possível acessar a câmera. Por favor, use a opção "Galeria" para selecionar uma foto.');
            jQuery('#fotoTab a[href="#tab-upload"]').tab('show');
        }
    }).finally(function() {
        // Restaurar botão
        if (btnIniciar) {
            btnIniciar.innerHTML = '<i class="icon-camera"></i> Iniciar Câmera';
            btnIniciar.disabled = false;
        }
    });
}

// Preview de upload - otimizado para mobile
function previewUpload(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];

        // Verificar tamanho (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('A imagem é muito grande. Por favor, selecione uma imagem menor que 5MB.');
            input.value = '';
            return;
        }

        // Mostrar loading
        var placeholder = document.getElementById('upload-placeholder');
        placeholder.innerHTML = '<i class="icon-spinner icon-spin"></i><p>Processando imagem...</p>';

        var reader = new FileReader();
        reader.onload = function(e) {
            fotoCapturada = e.target.result;

            // Redimensionar imagem se for muito grande (para melhor performance)
            var img = new Image();
            img.onload = function() {
                var maxWidth = 800;
                var maxHeight = 800;
                var width = img.width;
                var height = img.height;

                if (width > maxWidth || height > maxHeight) {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);
                    fotoCapturada = canvas.toDataURL('image/jpeg', 0.8);
                }

                document.getElementById('preview-img').src = fotoCapturada;
                placeholder.style.display = 'none';
                document.getElementById('upload-preview').style.display = 'block';

                // Restaurar placeholder
                placeholder.innerHTML = '<i class="icon-picture"></i><p>Selecione uma foto da galeria ou tire uma foto agora</p><input type="file" id="input-foto" accept="image/*" style="display: none;" onchange="previewUpload(this)"><button type="button" class="btn-capturar" onclick="selecionarArquivo(\'gallery\')" style="margin-bottom: 10px;"><i class="icon-folder-open"></i> Abrir Galeria</button><button type="button" class="btn-capturar" onclick="selecionarArquivo(\'camera\')" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);"><i class="icon-camera"></i> Tirar Foto Agora</button>';

                habilitarSalvar();
            };
            img.src = fotoCapturada;
        };
        reader.readAsDataURL(file);
    }
}

// Resetar upload
function resetUpload() {
    fotoCapturada = null;
    document.getElementById('upload-placeholder').style.display = 'block';
    document.getElementById('upload-preview').style.display = 'none';
    document.getElementById('input-foto').value = '';
    document.getElementById('btn-salvar-foto').disabled = true;
}

// Habilitar botão salvar
function habilitarSalvar() {
    document.getElementById('btn-salvar-foto').disabled = false;
}

// Capturar foto da câmera
function capturarDaCamera() {
    var video = document.getElementById('video');
    if (!video) return;

    // Feedback visual no mobile
    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile && navigator.vibrate) {
        navigator.vibrate(30);
    }

    // Animação de flash
    var flash = document.createElement('div');
    flash.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:white;opacity:0.8;z-index:10000;pointer-events:none;transition:opacity 0.3s;';
    document.body.appendChild(flash);
    setTimeout(function() {
        flash.style.opacity = '0';
        setTimeout(function() {
            flash.remove();
        }, 300);
    }, 50);

    var canvas = document.createElement('canvas');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    var ctx = canvas.getContext('2d');
    if (ctx) {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    }

    fotoCapturada = canvas.toDataURL('image/jpeg', 0.8);

    // Mostrar preview
    document.getElementById('preview-img').src = fotoCapturada;
    document.getElementById('upload-placeholder').style.display = 'none';
    document.getElementById('upload-preview').style.display = 'block';

    // Parar câmera e mudar para aba upload
    if (stream) {
        var tracks = stream.getTracks();
        for (var i = 0; i < tracks.length; i++) {
            tracks[i].stop();
        }
        stream = null;
    }

    // Mudar para aba de preview
    jQuery('#fotoTab a[href="#tab-upload"]').tab('show');
}

// Salvar foto com loading
function salvarFoto() {
    if (!fotoCapturada) {
        alert('Selecione ou capture uma foto primeiro');
        return;
    }

    // Mostrar loading no botão
    var btnSalvar = document.getElementById('btn-salvar-foto');
    var textoOriginal = btnSalvar.innerHTML;
    btnSalvar.innerHTML = '<i class="icon-spinner icon-spin"></i> Salvando...';
    btnSalvar.disabled = true;

    var formData = new FormData();
    formData.append('foto', fotoCapturada);
    formData.append(CSRF_TOKEN_NAME, CSRF_HASH);

    fetch(URL_ATUALIZAR_FOTO, {
        method: 'POST',
        body: formData
    }).then(function(response) {
        return response.json();
    }).then(function(data) {
        if (data.success) {
            // Atualizar preview do avatar
            var avatar = document.querySelector('.perfil-avatar');
            if (avatar) {
                avatar.innerHTML = '<img src="' + fotoCapturada + '" alt="Foto">';
            }

            // Feedback visual suave no mobile
            var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            if (isMobile) {
                // Vibração se suportada
                if (navigator.vibrate) {
                    navigator.vibrate(50);
                }
            }

            fecharCamera();
            setTimeout(function() {
                alert('Foto atualizada com sucesso!');
            }, 300);
        } else {
            alert('Erro ao atualizar foto: ' + (data.message || 'Erro desconhecido'));
            btnSalvar.innerHTML = textoOriginal;
            btnSalvar.disabled = false;
        }
    }).catch(function(err) {
        alert('Erro ao enviar foto: ' + err.message);
        btnSalvar.innerHTML = textoOriginal;
        btnSalvar.disabled = false;
    });
}

// Fechar modal
function fecharCamera() {
    jQuery('#cameraModal').modal('hide');
    if (stream) {
        var tracks = stream.getTracks();
        for (var i = 0; i < tracks.length; i++) {
            tracks[i].stop();
        }
        stream = null;
    }

    // Restaurar scroll do body
    document.body.style.overflow = '';

    // Sair do fullscreen se estiver
    if (document.exitFullscreen && document.fullscreenElement) {
        document.exitFullscreen().catch(function() {
            // Ignora erro
        });
    }

    // Resetar estado
    setTimeout(function() {
        document.getElementById('camera-off').style.display = 'block';
        document.getElementById('camera-on').style.display = 'none';
        resetUpload();
    }, 300);
}

// ===== GESTOS MOBILE E TECLADO =====
(function() {
    var touchStartY = 0;
    var touchEndY = 0;
    var minSwipeDistance = 100;

    document.addEventListener('touchstart', function(e) {
        touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });

    document.addEventListener('touchend', function(e) {
        touchEndY = e.changedTouches[0].screenY;
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        var swipeDistance = touchEndY - touchStartY;

        // Swipe para baixo fecha o modal (se estiver aberto)
        if (swipeDistance > minSwipeDistance) {
            var fotoModal = jQuery('#cameraModal');
            if (fotoModal.hasClass('in')) {
                fecharCamera();
            }
        }
    }

    // Tecla ESC fecha o modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var fotoModal = jQuery('#cameraModal');
            if (fotoModal.hasClass('in')) {
                fecharCamera();
            }
        }
    });

    // Fechar modal ao clicar fora (backdrop)
    jQuery('#cameraModal').on('click', function(e) {
        if (e.target === this) {
            fecharCamera();
        }
    });
})();

// ===== FUNÇÕES DE EDIÇÃO DE PERFIL =====

function abrirModalEdicao() {
    jQuery('#edicaoModal').modal('show');
    // Adicionar hash na URL para permitir fechar com botão voltar no mobile
    if (window.innerWidth <= 768) {
        history.pushState({modal: 'edicao'}, '', '#editar-perfil');
    }
}

function fecharModalEdicao() {
    jQuery('#edicaoModal').modal('hide');
    document.getElementById('msg-sucesso').style.display = 'none';
    document.getElementById('msg-erro').style.display = 'none';
    // Remover hash da URL se existir
    if (window.location.hash === '#editar-perfil') {
        history.back();
    }
}

// Capturar botão voltar do navegador no mobile
window.addEventListener('popstate', function(e) {
    if (jQuery('#edicaoModal').hasClass('in')) {
        fecharModalEdicao();
    }
});

// Máscara para telefone no mobile
(function() {
    var telefoneInput = document.getElementById('edit-telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            var valor = e.target.value.replace(/\D/g, '');
            if (valor.length <= 11) {
                if (valor.length > 2) {
                    valor = '(' + valor.substring(0, 2) + ') ' + valor.substring(2);
                }
                if (valor.length > 9) {
                    valor = valor.substring(0, 9) + '-' + valor.substring(9, 13);
                }
                e.target.value = valor;
            }
        });
    }
})();

function trocarAba(aba) {
    // Esconder todas as abas
    var abas = document.querySelectorAll('.aba-conteudo');
    for (var i = 0; i < abas.length; i++) {
        abas[i].classList.remove('active');
    }

    // Remover active de todos os botões
    var botoes = document.querySelectorAll('.aba-btn');
    for (var i = 0; i < botoes.length; i++) {
        botoes[i].classList.remove('active');
    }

    // Mostrar aba selecionada
    document.getElementById('aba-' + aba).classList.add('active');
    document.querySelector('[data-aba="' + aba + '"]').classList.add('active');
}

function salvarInformacoes() {
    var dados = {
        email: document.getElementById('edit-email').value,
        telefone: document.getElementById('edit-telefone').value,
        data_nascimento: document.getElementById('edit-nascimento').value
    };

    // Validar email
    if (!dados.email || dados.email.indexOf('@') === -1) {
        mostrarErro('Por favor, informe um e-mail válido');
        return;
    }

    var formData = new FormData();
    for (var key in dados) {
        if (dados[key]) {
            formData.append(key, dados[key]);
        }
    }
    formData.append(CSRF_TOKEN_NAME, CSRF_HASH);

    fetch('<?php echo site_url("tecnicos/atualizar_perfil"); ?>', {
        method: 'POST',
        body: formData
    }).then(function(response) {
        return response.json();
    }).then(function(data) {
        if (data.success) {
            mostrarSucesso('Informações atualizadas com sucesso!');
            setTimeout(function() {
                location.reload();
            }, 1500);
        } else {
            mostrarErro(data.message || 'Erro ao atualizar informações');
        }
    }).catch(function(err) {
        mostrarErro('Erro ao salvar: ' + err.message);
    });
}

function trocarSenha() {
    var senhaAtual = document.getElementById('senha-atual').value;
    var novaSenha = document.getElementById('nova-senha').value;
    var confirmarSenha = document.getElementById('confirmar-senha').value;

    // Validar
    if (!senhaAtual || !novaSenha || !confirmarSenha) {
        mostrarErro('Por favor, preencha todos os campos de senha');
        return;
    }

    if (novaSenha.length < 6) {
        mostrarErro('A nova senha deve ter pelo menos 6 caracteres');
        return;
    }

    if (novaSenha !== confirmarSenha) {
        mostrarErro('As senhas não conferem');
        return;
    }

    var formData = new FormData();
    formData.append('senha_atual', senhaAtual);
    formData.append('nova_senha', novaSenha);
    formData.append(CSRF_TOKEN_NAME, CSRF_HASH);

    fetch('<?php echo site_url("tecnicos/trocar_senha"); ?>', {
        method: 'POST',
        body: formData
    }).then(function(response) {
        return response.json();
    }).then(function(data) {
        if (data.success) {
            mostrarSucesso('Senha alterada com sucesso!');
            document.getElementById('senha-atual').value = '';
            document.getElementById('nova-senha').value = '';
            document.getElementById('confirmar-senha').value = '';
        } else {
            mostrarErro(data.message || 'Erro ao alterar senha');
        }
    }).catch(function(err) {
        mostrarErro('Erro ao trocar senha: ' + err.message);
    });
}

function mostrarSucesso(msg) {
    var el = document.getElementById('msg-sucesso');
    el.textContent = msg;
    el.style.display = 'block';
    document.getElementById('msg-erro').style.display = 'none';
}

function mostrarErro(msg) {
    var el = document.getElementById('msg-erro');
    el.textContent = msg;
    el.style.display = 'block';
    document.getElementById('msg-sucesso').style.display = 'none';
}
</script>

<!-- Modal de Edição de Perfil -->
<div class="modal hide modal-edicao" id="edicaoModal">
    <div class="modal-header">
        <button type="button" class="close" onclick="fecharModalEdicao()" style="color: white; opacity: 0.8;">&times;</button>
        <h3><i class="icon-pencil"></i> Editar Perfil</h3>
    </div>
    <div class="modal-body" style="padding: 25px;">
        <!-- Mensagens -->
        <div id="msg-sucesso" class="msg-sucesso"></div>
        <div id="msg-erro" class="msg-erro"></div>

        <!-- Abas -->
        <div class="abas-navegacao">
            <button class="aba-btn active" data-aba="info" onclick="trocarAba('info')">
                <i class="icon-user"></i> Informações
            </button>
            <button class="aba-btn" data-aba="senha" onclick="trocarAba('senha')">
                <i class="icon-lock"></i> Trocar Senha
            </button>
        </div>

        <!-- Aba Informações -->
        <div id="aba-info" class="aba-conteudo active">
            <div class="form-group">
                <label class="form-label">Nome Completo</label>
                <input type="text" class="form-input" value="<?php echo htmlspecialchars($tecnico->nome ?? ''); ?>" disabled style="background: #f5f5f5;">
                <small style="color: #888;">O nome só pode ser alterado pelo administrador</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">E-mail *</label>
                    <input type="email" class="form-input" id="edit-email" value="<?php echo htmlspecialchars($tecnico->email ?? ''); ?>" autocomplete="email" inputmode="email">
                </div>

                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="tel" class="form-input" id="edit-telefone" value="<?php echo htmlspecialchars($tecnico->telefone ?? ''); ?>" placeholder="(00) 00000-0000" inputmode="tel" autocomplete="tel">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Data de Nascimento</label>
                <?php
                // Formatar data para o formato do input date (YYYY-MM-DD)
                $data_nasc = '';
                if (!empty($tecnico->data_nascimento)) {
                    $data_nasc = date('Y-m-d', strtotime($tecnico->data_nascimento));
                }
                ?>
                <input type="date" class="form-input" id="edit-nascimento" value="<?php echo $data_nasc; ?>">
            </div>

            <div class="form-group">
                <label class="form-label">CPF</label>
                <input type="text" class="form-input" value="<?php echo htmlspecialchars($tecnico->cpf ?? ''); ?>" disabled style="background: #f5f5f5;">
                <small style="color: #888;">O CPF não pode ser alterado</small>
            </div>

            <div style="text-align: right; margin-top: 25px;">
                <button type="button" class="btn btn-salvar" onclick="salvarInformacoes()">
                    <i class="icon-ok"></i> Salvar Alterações
                </button>
            </div>
        </div>

        <!-- Aba Trocar Senha -->
        <div id="aba-senha" class="aba-conteudo">
            <div class="form-group">
                <label class="form-label">Senha Atual *</label>
                <input type="password" class="form-input" id="senha-atual" placeholder="Digite sua senha atual" autocomplete="current-password">
            </div>

            <div class="form-group">
                <label class="form-label">Nova Senha *</label>
                <input type="password" class="form-input" id="nova-senha" placeholder="Mínimo 6 caracteres" autocomplete="new-password">
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar Nova Senha *</label>
                <input type="password" class="form-input" id="confirmar-senha" placeholder="Digite novamente a nova senha" autocomplete="new-password">
            </div>

            <div style="text-align: right; margin-top: 25px;">
                <button type="button" class="btn btn-salvar" onclick="trocarSenha()">
                    <i class="icon-ok"></i> Trocar Senha
                </button>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" onclick="fecharModalEdicao()">Fechar</button>
    </div>
</div>
