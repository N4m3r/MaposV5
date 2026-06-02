<!-- Relatório de Execução - Portal do Técnico -->
<style>
.portal-tecnico-content { margin-top: 0 !important; }
@media (max-width: 768px) { .portal-tecnico-content { margin-top: 0 !important; } }

/* ===== Relatório Modern Theme ===== */
:root {
    --rel-accent: var(--sidebar-accent, #0467fc);
    --rel-accent-rgb: var(--sidebar-accent-rgb, 4, 103, 252);
    --rel-bg: #f8fafc;
    --rel-card-bg: #ffffff;
    --rel-text: #1e293b;
    --rel-text-secondary: #64748b;
    --rel-border: #e2e8f0;
    --rel-radius: 14px;
    --rel-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03);
    --rel-shadow-hover: 0 4px 20px rgba(0,0,0,0.08);
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.02em;
}
.status-badge svg.svg-icon { width: 14px; height: 14px; }
.status-badge.status-finalizada {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    color: #166534;
}

/* ===== Page Header ===== */
.rel-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid rgba(var(--rel-accent-rgb), 0.12);
    flex-wrap: wrap;
}
.rel-page-title {
    display: flex;
    align-items: center;
    gap: 10px;
}
.rel-page-title svg.svg-icon { color: var(--rel-accent); flex-shrink: 0; }
.rel-page-title h5 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--rel-text);
    letter-spacing: -0.01em;
}

/* ===== Page Body ===== */
.rel-page-body {
    /* No extra styling needed — cards handle their own spacing */
}

/* ===== Relatório Card ===== */
.rel-card {
    border: 1px solid var(--rel-border);
    border-radius: var(--rel-radius);
    box-shadow: var(--rel-shadow);
    padding: 24px;
    margin-bottom: 20px;
    transition: box-shadow 0.25s ease, border-color 0.25s ease;
}
.rel-card:hover {
    box-shadow: var(--rel-shadow-hover);
    border-color: rgba(var(--rel-accent-rgb), 0.15);
}

.rel-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 2px solid rgba(var(--rel-accent-rgb), 0.12);
}
.rel-card-header svg.svg-icon {
    width: 20px;
    height: 20px;
    color: var(--rel-accent);
    flex-shrink: 0;
}
.rel-card-header h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: var(--rel-text);
    letter-spacing: -0.01em;
}

/* Info Row / Grid */
.rel-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}
.rel-info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.rel-info-label {
    font-size: 0.72rem;
    color: var(--rel-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 600;
}
.rel-info-value {
    font-size: 0.95rem;
    color: var(--rel-text);
    font-weight: 500;
}

/* ===== Client Card ===== */
.rel-client-card {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
.rel-client-avatar {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--rel-accent), rgba(var(--rel-accent-rgb), 0.7));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(var(--rel-accent-rgb), 0.25);
}
.rel-client-avatar svg.svg-icon { width: 26px; height: 26px; color: #fff; }

.rel-client-info { flex: 1; min-width: 0; }
.rel-client-info h4 {
    margin: 0 0 8px 0;
    color: var(--rel-text);
    font-size: 1.05rem;
    font-weight: 700;
    word-break: break-word;
}
.rel-client-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 18px;
}
.rel-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--rel-text-secondary);
    font-size: 0.87rem;
}
.rel-meta-item svg.svg-icon {
    width: 15px;
    height: 15px;
    color: var(--rel-accent);
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .rel-client-card {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .rel-client-meta { justify-content: center; }
}

/* ===== Timeline ===== */
.rel-timeline {
    position: relative;
    padding-left: 32px;
}
.rel-timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, var(--rel-accent), rgba(var(--rel-accent-rgb), 0.15));
    border-radius: 2px;
}
.rel-timeline-item {
    position: relative;
    margin-bottom: 16px;
    padding: 14px 16px;
    background: var(--rel-bg);
    border-radius: 10px;
    border: 1px solid var(--rel-border);
    transition: border-color 0.2s ease;
}
.rel-timeline-item:hover { border-color: rgba(var(--rel-accent-rgb), 0.25); }
.rel-timeline-item::before {
    content: '';
    position: absolute;
    left: -28px;
    top: 18px;
    width: 12px;
    height: 12px;
    background: var(--rel-accent);
    border-radius: 50%;
    border: 2.5px solid var(--rel-card-bg);
    box-shadow: 0 0 0 2px var(--rel-accent);
}
.rel-timeline-date {
    font-size: 0.82rem;
    color: var(--rel-text-secondary);
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 4px;
}
.rel-timeline-date svg.svg-icon { width: 14px; height: 14px; }
.rel-timeline-title {
    font-weight: 600;
    color: var(--rel-text);
    margin-bottom: 6px;
    font-size: 0.92rem;
}
.rel-timeline-detail {
    font-size: 0.87rem;
    color: var(--rel-text-secondary);
    display: flex;
    align-items: center;
    gap: 5px;
}
.rel-timeline-detail svg.svg-icon { width: 14px; height: 14px; color: var(--rel-accent); }

/* ===== Checkin Table ===== */
.rel-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid var(--rel-border);
    border-radius: 10px;
    overflow: hidden;
    font-size: 0.85rem;
}
.rel-table th {
    background: rgba(var(--rel-accent-rgb), 0.06);
    font-weight: 700;
    color: var(--rel-text);
    padding: 10px 12px;
    text-align: left;
    border-bottom: 2px solid rgba(var(--rel-accent-rgb), 0.12);
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.rel-table td {
    padding: 10px 12px;
    border-bottom: 1px solid var(--rel-border);
    color: var(--rel-text);
    vertical-align: top;
}
.rel-table tr:last-child td { border-bottom: none; }
.rel-table tr:nth-child(even) td { background: rgba(var(--rel-accent-rgb), 0.02); }
.rel-table small { color: var(--rel-text-secondary); font-size: 0.75rem; }

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.73rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.badge-status svg.svg-icon { width: 12px; height: 12px; }
.badge-success {
    background: #dcfce7;
    color: #166534;
}
.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

/* ===== Photos Grid ===== */
.rel-photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 14px;
}
.rel-photo-card {
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid var(--rel-border);
    background: var(--rel-card-bg);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.rel-photo-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--rel-shadow-hover);
}
.rel-photo-card img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    display: block;
}
.rel-photo-fallback {
    display: none;
    width: 100%;
    height: 140px;
    background: var(--rel-bg);
    border: 2px dashed var(--rel-border);
    border-radius: 10px;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: var(--rel-text-secondary);
    font-size: 0.82rem;
    gap: 6px;
}
.rel-photo-fallback svg.svg-icon { width: 28px; height: 28px; opacity: 0.35; }
.rel-photo-label {
    padding: 8px 10px;
    background: var(--rel-bg);
    font-size: 0.78rem;
    text-align: center;
    color: var(--rel-text-secondary);
    font-weight: 500;
}

/* Photo section titles */
.rel-photo-section-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--rel-accent);
    margin-bottom: 10px;
    padding-bottom: 6px;
    border-bottom: 1px solid rgba(var(--rel-accent-rgb), 0.1);
    display: flex;
    align-items: center;
    gap: 6px;
}
.rel-photo-section-title svg.svg-icon { width: 16px; height: 16px; }

/* ===== Assinatura ===== */
.rel-sign-box {
    border: 2px dashed var(--rel-border);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    background: var(--rel-bg);
    transition: border-color 0.2s ease;
}
.rel-sign-box:hover { border-color: rgba(var(--rel-accent-rgb), 0.25); }
.rel-sign-box h6 {
    margin: 0 0 12px 0;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--rel-text);
}
.rel-sign-img {
    max-width: 100%;
    max-height: 180px;
    border: 1px solid var(--rel-border);
    border-radius: 8px;
}
.rel-sign-fallback {
    display: none;
    padding: 20px;
    background: var(--rel-bg);
    border-radius: 8px;
    text-align: center;
    color: var(--rel-text-secondary);
}
.rel-sign-fallback svg.svg-icon { width: 28px; height: 28px; opacity: 0.35; display: block; margin: 0 auto 8px; }
.rel-sign-name {
    margin-top: 10px;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--rel-text);
}
.rel-sign-date {
    font-size: 0.78rem;
    color: var(--rel-text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    margin-top: 4px;
}
.rel-sign-date svg.svg-icon { width: 13px; height: 13px; }

/* ===== Empty State ===== */
.rel-empty {
    text-align: center;
    padding: 32px;
    color: var(--rel-text-secondary);
}
.rel-empty svg.svg-icon { width: 36px; height: 36px; opacity: 0.3; display: block; margin: 0 auto 10px; }

/* ===== Back Button ===== */
.rel-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 24px;
    border-radius: 10px;
    background: var(--rel-bg);
    border: 1px solid var(--rel-border);
    color: var(--rel-text);
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.2s ease;
}
.rel-back-btn:hover {
    background: rgba(var(--rel-accent-rgb), 0.06);
    border-color: rgba(var(--rel-accent-rgb), 0.2);
    color: var(--rel-accent);
    text-decoration: none;
}
.rel-back-btn svg.svg-icon { width: 16px; height: 16px; }

/* ===== Header Actions ===== */
.rel-header-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.rel-btn-whatsapp {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 8px;
    background: #25d366;
    color: #fff;
    border: none;
    font-weight: 600;
    font-size: 0.82rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.rel-btn-whatsapp:hover { background: #1fba59; color: #fff; text-decoration: none; }
.rel-btn-whatsapp svg.svg-icon { width: 16px; height: 16px; }

.rel-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 8px;
    background: var(--rel-bg);
    border: 1px solid var(--rel-border);
    color: var(--rel-text);
    font-weight: 600;
    font-size: 0.82rem;
    text-decoration: none;
    transition: all 0.2s ease;
}
.rel-btn-back:hover {
    background: rgba(var(--rel-accent-rgb), 0.06);
    border-color: rgba(var(--rel-accent-rgb), 0.2);
    color: var(--rel-accent);
    text-decoration: none;
}
.rel-btn-back svg.svg-icon { width: 16px; height: 16px; }

/* ===== Modal Overrides ===== */
.rel-modal .modal-header {
    background: linear-gradient(135deg, #25d366, #128c7e);
    color: #fff;
    border-radius: 0;
    border: none;
    padding: 16px 20px;
}
.rel-modal .modal-header h3 {
    color: #fff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.05rem;
}
.rel-modal .modal-header h3 svg.svg-icon { width: 22px; height: 22px; }
.rel-modal .modal-header .close {
    color: #fff;
    opacity: 0.8;
    font-size: 1.5rem;
}
.rel-modal .modal-body { padding: 24px 20px; }
.rel-modal .modal-footer { padding: 12px 20px; border-top: 1px solid var(--rel-border); }
.rel-modal .form-label {
    font-weight: 600;
    color: var(--rel-text);
    font-size: 0.88rem;
    margin-bottom: 6px;
}
.rel-modal .form-control,
.rel-modal input[type="text"] {
    border: 1.5px solid var(--rel-border);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 0.9rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.rel-modal .form-control:focus,
.rel-modal input[type="text"]:focus {
    border-color: var(--rel-accent);
    box-shadow: 0 0 0 3px rgba(var(--rel-accent-rgb), 0.12);
    outline: none;
}

/* SVG icon sizing in cards */
.rel-card svg.svg-icon { color: var(--rel-accent); }
.rel-client-avatar svg.svg-icon { color: #fff; }

/* Responsive tweaks */
@media (max-width: 576px) {
    .rel-card { padding: 16px; }
    .rel-info-grid { grid-template-columns: 1fr 1fr; }
    .rel-photos-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
    .rel-header-actions { width: 100%; }
    .rel-header-actions .rel-btn-whatsapp,
    .rel-header-actions .rel-btn-back { flex: 1; justify-content: center; }
}
</style>

<div class="row portal-tecnico-content">
    <div class="col-12">

        <!-- Header -->
        <div class="rel-page-header">
            <div class="rel-page-title">
                <?= svg_icon('file-text', 24, 24) ?>
                <h5>Relatório de Execução - OS #<?php echo $os->idOs; ?></h5>
            </div>
            <div class="rel-header-actions">
                <button type="button" class="rel-btn-whatsapp" onclick="abrirModalWhatsApp()">
                    <?= svg_icon('whatsapp', 16, 16) ?> Enviar PDF
                </button>
                <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="rel-btn-back">
                    <?= svg_icon('chevron-left', 16, 16) ?> Voltar
                </a>
            </div>
        </div>

        <div class="rel-page-body">

                <!-- Informações da Empresa (Emitente) -->
                <?php if (!empty($emitente)): ?>
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('building', 20, 20) ?>
                        <h5>Empresa</h5>
                    </div>
                    <div class="rel-client-card">
                        <div class="rel-client-info">
                            <h4><?= htmlspecialchars($emitente->nome ?? 'Empresa', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></h4>
                            <?php if (!empty($emitente->cnpj)): ?>
                                <div class="rel-client-meta">
                                    <span class="rel-meta-item">
                                        <?= svg_icon('id-card', 15, 15) ?>
                                        CNPJ: <?= htmlspecialchars($emitente->cnpj, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($emitente->rua) || !empty($emitente->cidade)): ?>
                                <div class="rel-client-meta">
                                    <span class="rel-meta-item">
                                        <?= svg_icon('map', 15, 15) ?>
                                        <?php
                                        $endereco_emitente = [];
                                        if (!empty($emitente->rua)) $endereco_emitente[] = $emitente->rua;
                                        if (!empty($emitente->numero)) $endereco_emitente[] = $emitente->numero;
                                        if (!empty($emitente->complemento)) $endereco_emitente[] = $emitente->complemento;
                                        if (!empty($emitente->bairro)) $endereco_emitente[] = $emitente->bairro;
                                        echo implode(', ', $endereco_emitente);
                                        if (!empty($emitente->cidade)) {
                                            echo (!empty($endereco_emitente) ? ' - ' : '') . $emitente->cidade;
                                            if (!empty($emitente->uf)) echo '/' . $emitente->uf;
                                        }
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($emitente->telefone) || !empty($emitente->email)): ?>
                                <div class="rel-client-meta">
                                    <?php if (!empty($emitente->telefone)): ?>
                                        <span class="rel-meta-item">
                                            <?= svg_icon('phone', 15, 15) ?>
                                            <?= htmlspecialchars($emitente->telefone, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($emitente->email)): ?>
                                        <span class="rel-meta-item">
                                            <?= svg_icon('envelope', 15, 15) ?>
                                            <?= htmlspecialchars($emitente->email, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Informações da OS -->
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('clipboard', 20, 20) ?>
                        <h5>Informações da OS</h5>
                    </div>
                    <div class="rel-info-grid">
                        <div class="rel-info-item">
                            <span class="rel-info-label">OS Nº</span>
                            <span class="rel-info-value">#<?php echo sprintf('%04d', $os->idOs); ?></span>
                        </div>
                        <div class="rel-info-item">
                            <span class="rel-info-label">Data de Entrada</span>
                            <span class="rel-info-value"><?= date('d/m/Y', strtotime($os->dataInicial)); ?></span>
                        </div>
                        <div class="rel-info-item">
                            <span class="rel-info-label">Status</span>
                            <span class="status-badge status-finalizada">
                                <?= svg_icon('check-circle', 14, 14) ?> <?= htmlspecialchars($os->status, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                            </span>
                        </div>
                        <div class="rel-info-item">
                            <span class="rel-info-label">Data Prevista</span>
                            <span class="rel-info-value"><?= !empty($os->dataFinal) ? date('d/m/Y', strtotime($os->dataFinal)) : 'Não definida'; ?></span>
                        </div>
                    </div>
                    <?php if ($os->descricaoProduto): ?>
                        <div style="margin-top: 14px;">
                            <div class="rel-info-item">
                                <span class="rel-info-label">Descrição do Produto/Serviço</span>
                                <span class="rel-info-value"><?= htmlspecialchars(strip_tags($os->descricaoProduto), ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Informações do Cliente -->
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('user', 20, 20) ?>
                        <h5>Cliente</h5>
                    </div>
                    <div class="rel-client-card">
                        <div class="rel-client-avatar">
                            <?= svg_icon('user', 26, 26) ?>
                        </div>
                        <div class="rel-client-info">
                            <h4><?= htmlspecialchars($cliente->nomeCliente ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></h4>

                            <?php if (!empty($cliente->endereco)): ?>
                                <div class="rel-client-meta">
                                    <span class="rel-meta-item">
                                        <?= svg_icon('map', 15, 15) ?>
                                        <?= htmlspecialchars($cliente->endereco, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($cliente->telefone) || !empty($cliente->email)): ?>
                                <div class="rel-client-meta">
                                    <?php if (!empty($cliente->telefone)): ?>
                                        <span class="rel-meta-item">
                                            <?= svg_icon('phone', 15, 15) ?>
                                            <?= htmlspecialchars($cliente->telefone, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($cliente->email)): ?>
                                        <span class="rel-meta-item">
                                            <?= svg_icon('envelope', 15, 15) ?>
                                            <?= htmlspecialchars($cliente->email, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($cliente->documento) || !empty($cliente->cpf) || !empty($cliente->cnpj)): ?>
                                <div class="rel-client-meta">
                                    <span class="rel-meta-item">
                                        <?= svg_icon('id-card', 15, 15) ?>
                                        <?php
                                        $doc = $cliente->documento ?? $cliente->cpf ?? $cliente->cnpj ?? '';
                                        echo 'CPF/CNPJ: ' . htmlspecialchars($doc, ENT_COMPAT | ENT_HTML5, 'UTF-8');
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Checkins -->
                <?php if (!empty($checkins)): ?>
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('time', 20, 20) ?>
                        <h5>Histórico de Atendimentos</h5>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="rel-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Técnico</th>
                                    <th>Entrada</th>
                                    <th>Saída</th>
                                    <th>Tempo Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($checkins as $index => $checkin): ?>
                                    <tr>
                                        <td><?= $index + 1; ?></td>
                                        <td><?= htmlspecialchars($checkin->nome_tecnico, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></td>
                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($checkin->data_entrada)); ?>
                                            <?php if ($checkin->latitude_entrada && $checkin->longitude_entrada): ?>
                                                <br><small>Loc: <?= $checkin->latitude_entrada . ', ' . $checkin->longitude_entrada; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($checkin->data_saida): ?>
                                                <?= date('d/m/Y H:i', strtotime($checkin->data_saida)); ?>
                                                <?php if ($checkin->latitude_saida && $checkin->longitude_saida): ?>
                                                    <br><small>Loc: <?= $checkin->latitude_saida . ', ' . $checkin->longitude_saida; ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($checkin->data_saida) {
                                                $entrada = new DateTime($checkin->data_entrada);
                                                $saida = new DateTime($checkin->data_saida);
                                                $intervalo = $entrada->diff($saida);
                                                echo $intervalo->format('%h horas %i minutos');
                                            } else {
                                                echo 'Em andamento';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($checkin->data_saida): ?>
                                                <span class="badge-status badge-success">
                                                    <?= svg_icon('check', 12, 12) ?> Finalizado
                                                </span>
                                            <?php else: ?>
                                                <span class="badge-status badge-warning">
                                                    <?= svg_icon('clock', 12, 12) ?> Em Andamento
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php if ($checkin->observacao_entrada || $checkin->observacao_saida): ?>
                                        <tr style="background: rgba(var(--rel-accent-rgb), 0.03);">
                                            <td colspan="6">
                                                <?php if ($checkin->observacao_entrada): ?>
                                                    <strong>Obs. Entrada:</strong> <?= nl2br(htmlspecialchars($checkin->observacao_entrada, ENT_COMPAT | ENT_HTML5, 'UTF-8')) ?><br>
                                                <?php endif; ?>
                                                <?php if ($checkin->observacao_saida): ?>
                                                    <strong>Obs. Saída:</strong> <?= nl2br(htmlspecialchars($checkin->observacao_saida, ENT_COMPAT | ENT_HTML5, 'UTF-8')) ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Execuções do Portal do Técnico -->
                <?php if (!empty($execucoes)): ?>
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('timer', 20, 20) ?>
                        <h5>Execuções do Portal do Técnico</h5>
                    </div>
                    <div class="rel-timeline">
                        <?php foreach ($execucoes as $exec): ?>
                            <div class="rel-timeline-item">
                                <div class="rel-timeline-date">
                                    <?= svg_icon('calendar', 14, 14) ?> <?= date('d/m/Y H:i', strtotime($exec->checkin_horario)); ?>
                                </div>
                                <div class="rel-timeline-title">
                                    Execução #<?= $exec->id; ?> - <?= htmlspecialchars($exec->tecnico_nome ?? 'Técnico', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                </div>
                                <?php if (!empty($exec->tempo_atendimento_minutos)): ?>
                                    <div class="rel-timeline-detail">
                                        <?= svg_icon('clock', 14, 14) ?> Tempo: <?php
                                            $horas = floor($exec->tempo_atendimento_minutos / 60);
                                            $minutos = $exec->tempo_atendimento_minutos % 60;
                                            echo $horas . 'h ' . $minutos . 'min';
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($exec->checklist_json)):
                                    $checklist = json_decode($exec->checklist_json, true);
                                    if (!empty($checklist['observacoes'])): ?>
                                        <div class="rel-timeline-detail">
                                            <?= svg_icon('comment', 14, 14) ?> <?= nl2br(htmlspecialchars($checklist['observacoes'], ENT_COMPAT | ENT_HTML5, 'UTF-8')) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Serviços Executados -->
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('wrench', 20, 20) ?>
                        <h5>Serviços da OS</h5>
                    </div>
                    <?php if (!empty($servicos)): ?>
                        <div style="overflow-x: auto;">
                            <table class="rel-table">
                                <thead>
                                    <tr>
                                        <th>Serviço</th>
                                        <th>Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($servicos as $servico): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($servico->servico_nome ?? $servico->nome ?? 'Serviço', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                                <?php if (!empty($servico->servico_codigo) || !empty($servico->codigo)): ?>
                                                    <br><small style="color: var(--rel-text-secondary);">Código: <?= htmlspecialchars($servico->servico_codigo ?? $servico->codigo ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $servico->quantidade ?? 1; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="rel-empty">
                            <?= svg_icon('clipboard', 36, 36, '', 'display:block;margin:0 auto 10px;opacity:0.3;') ?>
                            Nenhum serviço cadastrado
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Produtos Utilizados -->
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('package', 20, 20) ?>
                        <h5>Produtos/Materiais</h5>
                    </div>
                    <?php if (!empty($produtos)): ?>
                        <div style="overflow-x: auto;">
                            <table class="rel-table">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $produto): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($produto->descricao ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></td>
                                            <td><?= $produto->quantidade ?? 0; ?> <?= htmlspecialchars($produto->unidade ?? 'un', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="rel-empty">
                            <?= svg_icon('package', 36, 36, '', 'display:block;margin:0 auto 10px;opacity:0.3;') ?>
                            Nenhum produto utilizado
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Fotos do Portal do Técnico -->
                <?php if (!empty($fotosTecnico)): ?>
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('camera', 20, 20) ?>
                        <h5>Fotos do Técnico</h5>
                    </div>
                    <div class="rel-photos-grid">
                        <?php foreach ($fotosTecnico as $foto): ?>
                            <div class="rel-photo-card">
                                <img src="<?= base_url($foto->caminho); ?>"
                                     alt="Foto do técnico"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="rel-photo-fallback">
                                    <?= svg_icon('image', 28, 28, '', 'display:block;margin:0 auto 6px;opacity:0.35;') ?>
                                    <span>Foto não disponível</span>
                                </div>
                                <div class="rel-photo-label">
                                    <?php
                                    $tipo = $foto->tipo ?? 'foto';
                                    $tipos_label = [
                                        'checkin' => 'Check-in',
                                        'checkout' => 'Check-out',
                                        'antes' => 'Antes',
                                        'depois' => 'Depois',
                                        'problema' => 'Problema',
                                        'detalhe' => 'Detalhe',
                                        'durante' => 'Durante',
                                        'foto' => 'Foto'
                                    ];
                                    echo $tipos_label[$tipo] ?? ucfirst($tipo);
                                    ?>
                                    <?php if (!empty($foto->data_envio)): ?>
                                        <br><small><?= date('d/m/Y H:i', strtotime($foto->data_envio)); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Fotos do Sistema de Atendimento - Organizadas por Etapa -->
                <?php if (!empty($fotosPorEtapa['entrada']) || !empty($fotosPorEtapa['durante']) || !empty($fotosPorEtapa['saida'])): ?>
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('image', 20, 20) ?>
                        <h5>Registro Fotográfico do Atendimento</h5>
                    </div>

                    <!-- Fotos de Entrada -->
                    <?php if (!empty($fotosPorEtapa['entrada'])): ?>
                    <div class="rel-photo-section" style="margin-bottom: 20px;">
                        <div class="rel-photo-section-title">
                            <?= svg_icon('import', 16, 16) ?> Fotos de Entrada
                        </div>
                        <div class="rel-photos-grid">
                            <?php foreach ($fotosPorEtapa['entrada'] as $foto): ?>
                                <div class="rel-photo-card">
                                    <?php
                                    $imgUrl = !empty($foto->imagem_base64)
                                        ? base_url('index.php/checkin/verFotoDB/' . $foto->idFoto)
                                        : $foto->url;
                                    ?>
                                    <img src="<?= $imgUrl; ?>"
                                         alt="Foto de entrada"
                                         loading="lazy"
                                         decoding="async"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rel-photo-fallback">
                                        <?= svg_icon('image', 28, 28, '', 'display:block;margin:0 auto 6px;opacity:0.35;') ?>
                                        <span>Foto não disponível</span>
                                    </div>
                                    <div class="rel-photo-label">
                                        Entrada
                                        <?php if (!empty($foto->descricao)): ?>
                                            <br><small><?= htmlspecialchars($foto->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Fotos Durante -->
                    <?php if (!empty($fotosPorEtapa['durante'])): ?>
                    <div class="rel-photo-section" style="margin-bottom: 20px;">
                        <div class="rel-photo-section-title">
                            <?= svg_icon('time', 16, 16) ?> Fotos Durante o Atendimento
                        </div>
                        <div class="rel-photos-grid">
                            <?php foreach ($fotosPorEtapa['durante'] as $foto): ?>
                                <div class="rel-photo-card">
                                    <?php
                                    $imgUrl = !empty($foto->imagem_base64)
                                        ? base_url('index.php/checkin/verFotoDB/' . $foto->idFoto)
                                        : $foto->url;
                                    ?>
                                    <img src="<?= $imgUrl; ?>"
                                         alt="Foto durante atendimento"
                                         loading="lazy"
                                         decoding="async"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rel-photo-fallback">
                                        <?= svg_icon('image', 28, 28, '', 'display:block;margin:0 auto 6px;opacity:0.35;') ?>
                                        <span>Foto não disponível</span>
                                    </div>
                                    <div class="rel-photo-label">
                                        Durante
                                        <?php if (!empty($foto->descricao)): ?>
                                            <br><small><?= htmlspecialchars($foto->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Fotos de Saída -->
                    <?php if (!empty($fotosPorEtapa['saida'])): ?>
                    <div class="rel-photo-section">
                        <div class="rel-photo-section-title">
                            <?= svg_icon('log-out', 16, 16) ?> Fotos de Saída
                        </div>
                        <div class="rel-photos-grid">
                            <?php foreach ($fotosPorEtapa['saida'] as $foto): ?>
                                <div class="rel-photo-card">
                                    <?php
                                    $imgUrl = !empty($foto->imagem_base64)
                                        ? base_url('index.php/checkin/verFotoDB/' . $foto->idFoto)
                                        : $foto->url;
                                    ?>
                                    <img src="<?= $imgUrl; ?>"
                                         alt="Foto de saída"
                                         loading="lazy"
                                         decoding="async"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rel-photo-fallback">
                                        <?= svg_icon('image', 28, 28, '', 'display:block;margin:0 auto 6px;opacity:0.35;') ?>
                                        <span>Foto não disponível</span>
                                    </div>
                                    <div class="rel-photo-label">
                                        Saída
                                        <?php if (!empty($foto->descricao)): ?>
                                            <br><small><?= htmlspecialchars($foto->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Fotos do Sistema de Atendimento (fallback - todas as fotos) -->
                <?php if (!empty($fotosAtendimento) && empty($fotosPorEtapa['entrada']) && empty($fotosPorEtapa['durante']) && empty($fotosPorEtapa['saida'])): ?>
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('image', 20, 20) ?>
                        <h5>Registro Fotográfico do Atendimento</h5>
                    </div>
                    <div class="rel-photos-grid">
                        <?php foreach ($fotosAtendimento as $foto): ?>
                            <div class="rel-photo-card">
                                <img src="<?= $foto->url; ?>"
                                     alt="Foto de atendimento"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="rel-photo-fallback">
                                    <?= svg_icon('image', 28, 28, '', 'display:block;margin:0 auto 6px;opacity:0.35;') ?>
                                    <span>Foto não disponível</span>
                                </div>
                                <div class="rel-photo-label">
                                    <?php
                                    $etapa = $foto->etapa ?? 'foto';
                                    $etapas_label = [
                                        'entrada' => 'Entrada',
                                        'saida' => 'Saída',
                                        'durante' => 'Durante',
                                        'foto' => 'Foto'
                                    ];
                                    echo $etapas_label[$etapa] ?? ucfirst($etapa);
                                    ?>
                                    <?php if (!empty($foto->descricao)): ?>
                                        <br><small><?= htmlspecialchars($foto->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Assinaturas -->
                <?php if (!empty($assinaturas) || !empty($execucoes)): ?>
                <div class="rel-card">
                    <div class="rel-card-header">
                        <?= svg_icon('edit', 20, 20) ?>
                        <h5>Assinaturas</h5>
                    </div>
                    <div class="row">
                        <?php if (!empty($assinaturas)): ?>
                            <?php foreach ($assinaturas as $assinatura):
                                $tipo_label = '';
                                switch ($assinatura->tipo) {
                                    case 'tecnico_entrada':
                                        $tipo_label = 'Técnico - Entrada';
                                        break;
                                    case 'tecnico_saida':
                                        $tipo_label = 'Técnico - Saída';
                                        break;
                                    case 'cliente_saida':
                                        $tipo_label = 'Cliente - Saída';
                                        break;
                                    default:
                                        $tipo_label = ucfirst(str_replace('_', ' ', $assinatura->tipo));
                                }
                            ?>
                                <div class="col-6">
                                    <div class="rel-sign-box">
                                        <h6><?= htmlspecialchars($tipo_label, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></h6>
                                        <?php if (!empty($assinatura->assinatura)): ?>
                                            <?php
                                            $img_src = $assinatura->url_visualizacao ?? base_url('index.php/checkin/verAssinatura/' . $assinatura->idAssinatura);
                                            ?>
                                            <img src="<?= $img_src; ?>" alt="Assinatura <?= htmlspecialchars($tipo_label, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>" class="rel-sign-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <div class="rel-sign-fallback">
                                                <?= svg_icon('image', 28, 28, '', 'display:block;margin:0 auto 8px;opacity:0.35;') ?>
                                                Assinatura salva (erro ao carregar imagem)
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($assinatura->nome_assinante)): ?>
                                            <p class="rel-sign-name">
                                                <strong>Assinado por:</strong> <?= htmlspecialchars($assinatura->nome_assinante, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($assinatura->data_assinatura)): ?>
                                            <p class="rel-sign-date">
                                                <?= svg_icon('calendar', 13, 13) ?> <?= date('d/m/Y H:i', strtotime($assinatura->data_assinatura)); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Botão Voltar -->
                <div style="text-align: center; margin-top: 24px; margin-bottom: 16px;">
                    <a href="<?= site_url('tecnicos/minhas_os'); ?>" class="rel-back-btn">
                        <?= svg_icon('chevron-left', 16, 16) ?> Voltar para Minhas OS
                    </a>
                </div>

        </div>

    </div>
</div>

<!-- Modal WhatsApp -->
<div id="modalWhatsApp" class="modal fade rel-modal" tabindex="-1" role="dialog" aria-labelledby="modalWhatsAppLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="modalWhatsAppLabel"><?= svg_icon('whatsapp', 22, 22, '', 'color:#fff;') ?> Enviar Relatório por WhatsApp</h3>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label" for="telefone_whatsapp">Número do WhatsApp:</label>
            <div class="controls">
                <input type="text" id="telefone_whatsapp" name="telefone_whatsapp" class="form-control col-12" placeholder="(00) 00000-0000">
                <span class="form-text" style="color: var(--rel-text-secondary); font-size: 0.82rem;">Informe o número com DDD. Ex: (11) 98765-4321</span>
            </div>
        </div>
        <div id="mensagem_status" style="display: none; margin-top: 15px;"></div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm" data-bs-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-sm rel-btn-whatsapp" id="btnEnviarWhatsApp" onclick="enviarPdfWhatsApp()">
            <?= svg_icon('whatsapp', 16, 16) ?> Enviar
        </button>
    </div>
</div>

<script>
function abrirModalWhatsApp() {
    var telefoneCliente = '<?php echo preg_replace("/[^0-9]/", "", $cliente->telefone ?? $cliente->celular ?? ""); ?>';
    if (telefoneCliente && telefoneCliente.length >= 10) {
        telefoneCliente = telefoneCliente.replace(/(\d{2})(\d{4,5})(\d{4})/, "($1) $2-$3");
        $('#telefone_whatsapp').val(telefoneCliente);
    }
    $('#modalWhatsApp').modal('show');
}

function enviarPdfWhatsApp() {
    var telefone = $('#telefone_whatsapp').val().replace(/\D/g, '');
    var svgBaseUrl = '<?= base_url() ?>assets/svg/icons.svg';

    if (telefone.length < 10) {
        $('#mensagem_status').html('<div class="alert alert-danger" style="display:flex;align-items:center;gap:8px;">' +
            '<svg class="svg-icon" width="18" height="18" aria-hidden="true"><use href="' + svgBaseUrl + '#error-circle"/></svg>' +
            'Número de telefone inválido. Informe o DDD e o número completo.</div>').show();
        return;
    }

    $('#btnEnviarWhatsApp').prop('disabled', true).html(
        '<svg class="svg-icon" width="16" height="16" aria-hidden="true" style="animation: spin 1s linear infinite;"><use href="' + svgBaseUrl + '#loader"/></svg> Enviando...'
    );

    $.ajax({
        url: '<?php echo site_url("tecnicos/enviar_pdf_whatsapp/" . $os->idOs); ?>',
        type: 'POST',
        data: {
            telefone: telefone,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var htmlSucesso = '<div class="alert alert-success" style="display:flex;flex-direction:column;gap:6px;">' +
                    '<span style="display:flex;align-items:center;gap:6px;">' +
                    '<svg class="svg-icon" width="18" height="18" aria-hidden="true"><use href="' + svgBaseUrl + '#check-circle"/></svg>' +
                    response.message + '</span>' +
                    '<a href="' + response.whatsapp_link + '" target="_blank" class="rel-btn-whatsapp" style="display:inline-flex;align-items:center;gap:6px;width:fit-content;">' +
                    '<svg class="svg-icon" width="16" height="16" aria-hidden="true"><use href="' + svgBaseUrl + '#whatsapp"/></svg>' +
                    'Abrir WhatsApp</a></div>';
                $('#mensagem_status').html(htmlSucesso).show();

                setTimeout(function() {
                    window.open(response.whatsapp_link, '_blank');
                }, 1000);

                setTimeout(function() {
                    $('#modalWhatsApp').modal('hide');
                    $('#mensagem_status').hide();
                }, 10000);
            } else {
                $('#mensagem_status').html('<div class="alert alert-danger" style="display:flex;align-items:center;gap:8px;">' +
                    '<svg class="svg-icon" width="18" height="18" aria-hidden="true"><use href="' + svgBaseUrl + '#error-circle"/></svg>' +
                    response.message + '</div>').show();
            }
        },
        error: function(xhr, status, error) {
            $('#mensagem_status').html('<div class="alert alert-danger" style="display:flex;align-items:center;gap:8px;">' +
                '<svg class="svg-icon" width="18" height="18" aria-hidden="true"><use href="' + svgBaseUrl + '#error-circle"/></svg>' +
                'Erro ao enviar: ' + error + '</div>').show();
        },
        complete: function() {
            $('#btnEnviarWhatsApp').prop('disabled', false).html(
                '<svg class="svg-icon" width="16" height="16" aria-hidden="true"><use href="' + svgBaseUrl + '#whatsapp"/></svg> Enviar'
            );
        }
    });
}

// Máscara para o campo de telefone
$('#telefone_whatsapp').on('input', function() {
    var valor = $(this).val().replace(/\D/g, '');
    if (valor.length > 11) valor = valor.substring(0, 11);

    if (valor.length >= 2) {
        valor = '(' + valor.substring(0, 2) + ') ' + valor.substring(2);
    }
    if (valor.length >= 10) {
        var pos = valor.indexOf(') ') + 2;
        valor = valor.substring(0, pos + 5) + '-' + valor.substring(pos + 5);
    }

    $(this).val(valor);
});
</script>

<style>
/* Spinner animation for loader icon */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>