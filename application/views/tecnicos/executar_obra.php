<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
/* Reset e Base */
.obra-execucao { padding: 15px; max-width: 100%; }

/* Header Mobile-First */
.exec-header-mobile {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 15px;
    padding: 20px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.3);
}
.exec-header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}
.exec-header-info h1 {
    margin: 0 0 5px 0;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.2;
}
.exec-header-info p {
    margin: 0;
    opacity: 0.9;
    font-size: 13px;
}
.btn-voltar {
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Status Card */
.status-card {
    background: rgba(255,255,255,0.15);
    border-radius: 12px;
    padding: 15px;
    margin-top: 15px;
}
.status-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.status-label {
    font-size: 11px;
    text-transform: uppercase;
    opacity: 0.8;
}
.status-value {
    font-size: 24px;
    font-weight: 700;
}
.progress-bar-mobile {
    height: 8px;
    background: rgba(255,255,255,0.3);
    border-radius: 4px;
    overflow: hidden;
}
.progress-fill-mobile {
    height: 100%;
    background: white;
    border-radius: 4px;
    transition: width 0.5s ease;
}

/* Card de Ação Principal - Check-in/Check-out */
.action-card-principal {
    background: white;
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.1);
    text-align: center;
    border: 2px solid #e8e8e8;
}
.action-card-principal.ativo {
    border-color: #11998e;
    background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
}
.status-trabalho {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}
.status-trabalho strong {
    display: block;
    font-size: 18px;
    color: #333;
    margin-top: 5px;
}
.btn-principal-acao {
    width: 100%;
    padding: 18px 30px;
    border: none;
    border-radius: 15px;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin: 15px 0;
    transition: all 0.3s;
}
.btn-principal-acao.checkin {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
}
.btn-principal-acao.checkin:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.5);
}
.btn-principal-acao.checkout {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    box-shadow: 0 5px 20px rgba(231, 76, 60, 0.4);
}
.btn-principal-acao.checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(231, 76, 60, 0.5);
}
.btn-principal-acao:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}
.tempo-trabalhando {
    font-size: 32px;
    font-weight: 700;
    color: #11998e;
    font-family: 'Courier New', monospace;
    margin: 10px 0;
}
.info-localizacao {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

/* Menu Rápido */
.menu-rapido {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.btn-menu-rapido {
    background: white;
    border: 2px solid #e8e8e8;
    border-radius: 15px;
    padding: 20px 15px;
    text-align: center;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
}
.btn-menu-rapido:hover {
    border-color: #11998e;
    background: #f0fff4;
    transform: translateY(-2px);
}
.btn-menu-rapido i {
    font-size: 28px;
    color: #11998e;
    display: block;
    margin-bottom: 8px;
}
.btn-menu-rapido span {
    font-size: 13px;
    font-weight: 600;
}

/* Cards de Seção */
.section-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
}
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f5f5f5;
}
.section-title {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-title i {
    color: #11998e;
    font-size: 20px;
}
.btn-ver-tudo {
    font-size: 12px;
    color: #11998e;
    text-decoration: none;
    font-weight: 600;
}

/* Etapas Simplificadas */
.etapas-lista {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.etapa-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    border-left: 4px solid #ddd;
    transition: all 0.3s;
}
.etapa-item.ativa { border-left-color: #3498db; background: #ebf5fb; }
.etapa-item.concluida { border-left-color: #11998e; background: #e8f8f5; }
.etapa-item.atrasada { border-left-color: #e74c3c; background: #fdedec; }
.etapa-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}
.etapa-nome {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    flex: 1;
}
.etapa-status {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
}
.etapa-status.pendente { background: #f39c12; color: white; }
.etapa-status.andamento { background: #3498db; color: white; }
.etapa-status.concluida { background: #11998e; color: white; }
.etapa-status.atrasada { background: #e74c3c; color: white; }
.etapa-prazo {
    font-size: 12px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 5px;
}
.etapa-progresso {
    margin-top: 10px;
}
.progresso-barra {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}
.progresso-preenchido {
    height: 100%;
    background: linear-gradient(90deg, #11998e, #38ef7d);
    border-radius: 3px;
}
.btn-acao-etapa {
    width: 100%;
    margin-top: 10px;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: #11998e;
    color: white;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

/* Atividades do Dia */
.atividade-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 10px;
}
.atividade-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.atividade-hora {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 5px;
}
.atividade-descricao {
    font-size: 14px;
    color: #333;
    margin-bottom: 8px;
}
.atividade-tipo {
    display: inline-block;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 12px;
    font-weight: 600;
}
.atividade-tipo.execucao { background: #d4edda; color: #155724; }
.atividade-tipo.problema { background: #f8d7da; color: #721c24; }
.atividade-tipo.observacao { background: #fff3cd; color: #856404; }
.btn-adicionar-atividade {
    width: 100%;
    padding: 15px;
    border: 2px dashed #11998e;
    border-radius: 12px;
    background: #f0fff4;
    color: #11998e;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-adicionar-atividade:hover {
    background: #e6fffa;
}

/* Resumo do Dia */
.resumo-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 20px;
    color: white;
    margin-bottom: 15px;
}
.resumo-titulo {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 15px;
}
.resumo-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    text-align: center;
}
.resumo-item-valor {
    font-size: 24px;
    font-weight: 700;
}
.resumo-item-label {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 5px;
}

/* Botão Flutuante */
.fab-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    transition: all 0.3s;
}
.fab-button:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.5);
}

/* Modais */
.modal-header-custom {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
    padding: 20px;
    border-radius: 15px 15px 0 0;
}
.modal-body-custom {
    padding: 20px;
}
.form-group-custom {
    margin-bottom: 20px;
}
.form-label-custom {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}
.form-input-custom, .form-textarea-custom, .form-select-custom {
    width: 100%;
    padding: 15px;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    font-size: 16px;
    font-family: inherit;
}
.form-input-custom:focus, .form-textarea-custom:focus, .form-select-custom:focus {
    outline: none;
    border-color: #11998e;
}
.form-textarea-custom {
    resize: vertical;
    min-height: 100px;
}
.btn-submit-custom {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
}

/* Preview de Fotos */
.foto-preview-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-top: 15px;
}
.foto-preview-item {
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.foto-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.btn-adicionar-foto {
    border: 2px dashed #ddd;
    background: #f8f9fa;
    cursor: pointer;
    color: #888;
    font-size: 24px;
}
.btn-adicionar-foto:hover {
    border-color: #11998e;
    color: #11998e;
}

/* Alertas */
.alerta-card {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.alerta-card i {
    color: #f39c12;
    font-size: 20px;
}
.alerta-card.erro {
    background: #f8d7da;
    border-color: #f5c6cb;
}
.alerta-card.erro i {
    color: #e74c3c;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #888;
}
.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}
.empty-state p {
    font-size: 14px;
}

/* Desktop Responsive */
@media (min-width: 768px) {
    .obra-execucao { padding: 30px; max-width: 1200px; margin: 0 auto; }
    .exec-grid-desktop {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
    }
    .menu-rapido { grid-template-columns: repeat(4, 1fr); }
}

/* Animações */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
.animacao-pulse {
    animation: pulse 2s infinite;
}
</style>

<div class="obra-execucao">

    <!-- Header -->
    <div class="exec-header-mobile">
        <div class="exec-header-top">
            <div class="exec-header-info">
                <h1><i class="icon-building"></i> <?= htmlspecialchars($obra->nome) ?></h1>
                <p><i class="icon-user"></i> <?= htmlspecialchars($obra->cliente_nome ?? 'N/A') ?></p>
            </div>
            <a href="<?= site_url('tecnicos/minhas_obras') ?>" class="btn-voltar">
                <i class="icon-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="status-card">
            <div class="status-card-header">
                <span class="status-label">Progresso da Obra</span>
                <span class="status-value"><?= $obra->percentual_concluido ?? 0 ?>%</span>
            </div>
            <div class="progress-bar-mobile">
                <div class="progress-fill-mobile" style="width: <?= $obra->percentual_concluido ?? 0 ?>%"></div>
            </div>
        </div>
    </div>

    <?php if (isset($obra) && $obra): ?>

        <!-- Card Principal: Check-in/Check-out -->
        <div id="card-acao-principal" class="action-card-principal">
            <div class="status-trabalho">
                Status do Trabalho
                <strong id="status-trabalho-texto">Não iniciado</strong>
            </div>

            <div id="tempo-trabalhando-container" style="display: none;">
                <div class="tempo-trabalhando" id="tempo-trabalhando">00:00:00</div>
                <div class="info-localizacao">
                    <i class="icon-map-marker"></i>
                    <span id="status-localizacao">Obtendo localização...</span>
                </div>
            </div>

            <button type="button" id="btn-acao-principal" class="btn-principal-acao checkin" onclick="executarAcaoPrincipal()">
                <i class="icon-play-circle"></i>
                <span id="texto-btn-acao">Iniciar Trabalho</span>
            </button>

            <input type="hidden" id="checkin-ativo-id" value="">
            <input type="hidden" id="latitude" value="">
            <input type="hidden" id="longitude" value="">
        </div>

        <!-- Resumo do Dia -->
        <div class="resumo-card">
            <div class="resumo-titulo"><i class="icon-calendar"></i> Resumo de Hoje</div>
            <div class="resumo-grid">
                <div class="resumo-item">
                    <div class="resumo-item-valor" id="resumo-horas">0h</div>
                    <div class="resumo-item-label">Trabalhadas</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-item-valor" id="resumo-atividades">0</div>
                    <div class="resumo-item-label">Atividades</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-item-valor" id="resumo-checkins">0</div>
                    <div class="resumo-item-label">Entradas</div>
                </div>
            </div>
        </div>

        <!-- Menu Rápido -->
        <div class="menu-rapido">
            <a href="#etapas" class="btn-menu-rapido" onclick="mostrarSecao('etapas')">
                <i class="icon-tasks"></i>
                <span>Etapas</span>
            </a>
            <a href="#atividades" class="btn-menu-rapido" onclick="mostrarSecao('atividades')">
                <i class="icon-clipboard"></i>
                <span>Atividades</span>
            </a>
            <a href="#minhas-os" class="btn-menu-rapido" onclick="mostrarSecao('minhas-os')">
                <i class="icon-wrench"></i>
                <span>Minhas OS</span>
            </a>
            <a href="javascript:void(0)" class="btn-menu-rapido" onclick="abrirModalRelatorio()">
                <i class="icon-file-alt"></i>
                <span>Relatório</span>
            </a>
        </div>

        <!-- Seção: Etapas -->
        <div id="secao-etapas" class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <i class="icon-tasks"></i> Etapas da Obra
                </div>
                <a href="javascript:void(0)" class="btn-ver-tudo" onclick="toggleExpandirEtapas()">
                    Ver todas
                </a>
            </div>

            <div class="etapas-lista">
                <?php if (!empty($etapas)): ?>
                    <?php foreach ($etapas as $i => $etapa): ?>
                        <?php
                        $statusClass = 'pendente';
                        $statusLabel = 'Pendente';
                        if ($etapa->status == 'concluida') {
                            $statusClass = 'concluida';
                            $statusLabel = 'Concluída';
                        } elseif ($etapa->status == 'em_andamento') {
                            $statusClass = 'andamento';
                            $statusLabel = 'Em Andamento';
                        } elseif ($etapa->status == 'atrasada') {
                            $statusClass = 'atrasada';
                            $statusLabel = 'Atrasada';
                        }

                        // Calcular dias restantes
                        $diasRestantes = '';
                        if ($etapa->data_fim_prevista) {
                            $hoje = new DateTime();
                            $fim = new DateTime($etapa->data_fim_prevista);
                            $diff = $hoje->diff($fim);
                            if ($fim >= $hoje) {
                                $diasRestantes = $diff->days . ' dias restantes';
                            } else {
                                $diasRestantes = 'Atrasada ' . $diff->days . ' dias';
                            }
                        }
                        ?>
                        <div class="etapa-item <?= $statusClass ?> <?= $i > 2 ? 'etapa-extra hidden' : '' ?>" data-etapa-id="<?= $etapa->id ?>">
                            <div class="etapa-header">
                                <div class="etapa-nome"><?= htmlspecialchars($etapa->nome) ?></div>
                                <span class="etapa-status <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </div>
                            <div class="etapa-prazo">
                                <i class="icon-calendar"></i>
                                <?php if ($etapa->data_inicio_prevista): ?>
                                    <?= date('d/m/Y', strtotime($etapa->data_inicio_prevista)) ?> -
                                    <?= $etapa->data_fim_prevista ? date('d/m/Y', strtotime($etapa->data_fim_prevista)) : 'Não definido' ?>
                                    <?php if ($diasRestantes): ?>
                                        <span style="margin-left: 10px; color: #888;">(<?= $diasRestantes ?>)</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    Prazo não definido
                                <?php endif; ?>
                            </div>
                            <div class="etapa-progresso">
                                <div class="progresso-barra">
                                    <div class="progresso-preenchido" style="width: <?= $etapa->percentual_concluido ?? 0 ?>"></div>
                                </div>
                                <div style="text-align: right; font-size: 12px; color: #666; margin-top: 5px;">
                                    <?= $etapa->percentual_concluido ?? 0 ?>% concluído
                                </div>
                            </div>
                            <?php if ($etapa->status != 'concluida'): ?>
                                <button type="button" class="btn-acao-etapa" onclick="atualizarProgressoEtapa(<?= $etapa->id ?>, '<?= htmlspecialchars($etapa->nome) ?>')">
                                    <i class="icon-refresh"></i> Atualizar Progresso
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($etapas) > 3): ?>
                        <div style="text-align: center; margin-top: 15px;">
                            <button type="button" id="btn-ver-mais-etapas" class="btn-ver-tudo" onclick="toggleExpandirEtapas()">
                                Ver mais <?= count($etapas) - 3 ?> etapas
                            </button>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="icon-tasks"></i>
                        <p>Nenhuma etapa cadastrada</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seção: Atividades do Dia -->
        <div id="secao-atividades" class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <i class="icon-clipboard"></i> Atividades de Hoje
                </div>
            </div>

            <div id="lista-atividades">
                <div class="empty-state">
                    <i class="icon-clipboard"></i>
                    <p>Nenhuma atividade registrada hoje</p>
                </div>
            </div>

            <button type="button" class="btn-adicionar-atividade" onclick="abrirModalAtividade()">
                <i class="icon-plus"></i> Registrar Nova Atividade
            </button>
        </div>

        <!-- Seção: Minhas OS -->
        <div id="secao-minhas-os" class="section-card" style="display: none;">
            <div class="section-header">
                <div class="section-title">
                    <i class="icon-wrench"></i> Minhas OS nesta Obra
                </div>
            </div>

            <?php if (!empty($minhas_os)): ?>
                <?php foreach ($minhas_os as $os): ?>
                    <div class="atividade-item">
                        <div class="atividade-header">
                            <span style="font-weight: 600;">OS #<?= $os->idOs ?></span>
                            <span class="atividade-tipo <?= strtolower($os->status) ?>">
                                <?= $os->status ?>
                            </span>
                        </div>
                        <div class="atividade-descricao">
                            <i class="icon-user"></i> <?= htmlspecialchars($os->nomeCliente ?? 'N/A') ?>
                        </div>
                        <div style="margin-top: 10px;">
                            <a href="<?= site_url('tecnicos/executar_os/' . $os->idOs) ?>" class="btn-acao-etapa" style="display: inline-block; width: auto; padding: 8px 20px;">
                                <i class="icon-play"></i> Executar OS
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="icon-wrench"></i>
                    <p>Nenhuma OS atribuída nesta obra</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Botão Flutuante -->
        <button type="button" class="fab-button" onclick="abrirModalAtividade()" title="Registrar Atividade Rápida">
            <i class="icon-plus"></i>
        </button>

    <?php else: ?>
        <div class="alerta-card erro">
            <i class="icon-exclamation-sign"></i>
            <div>
                <strong>Obra não encontrada</strong><br>
                Você não tem acesso a esta obra ou ela não existe.
            </div>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="<?= site_url('tecnicos/minhas_obras') ?>" class="btn-acao-etapa" style="display: inline-block; width: auto; padding: 15px 30px;">
                <i class="icon-arrow-left"></i> Voltar para Minhas Obras
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Atualizar Progresso da Etapa -->
<div id="modal-progresso-etapa" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header-custom">
        <button type="button" class="close" data-dismiss="modal" style="color: white;">×</button>
        <h4><i class="icon-refresh"></i> Atualizar Progresso</h4>
    </div>
    <form id="form-progresso-etapa" onsubmit="salvarProgressoEtapa(event)">
        <div class="modal-body-custom">
            <input type="hidden" id="progresso-etapa-id" name="etapa_id">

            <div class="form-group-custom">
                <label class="form-label-custom">Etapa</label>
                <input type="text" id="progresso-etapa-nome" class="form-input-custom" readonly style="background: #f5f5f5;">
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Percentual Concluído</label>
                <input type="range" name="percentual" id="progresso-percentual" min="0" max="100" value="0" class="form-input-custom" style="padding: 0;" oninput="atualizarValorRange(this.value)">
                <div style="text-align: center; font-size: 28px; font-weight: 700; color: #11998e; margin-top: 10px;">
                    <span id="progresso-valor">0</span>%
                </div>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Observação</label>
                <textarea name="observacao" class="form-textarea-custom" placeholder="Descreva o que foi realizado..."></textarea>
            </div>
        </div>
        <div class="modal-footer" style="padding: 0 20px 20px; border: none;">
            <button type="button" class="btn btn-large" data-dismiss="modal" style="width: 48%; margin-right: 4%;">Cancelar</button>
            <button type="submit" class="btn-submit-custom" style="width: 48%;">
                <i class="icon-save"></i> Salvar
            </button>
        </div>
    </form>
</div>

<!-- Modal: Registrar Atividade -->
<div id="modal-atividade" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header-custom">
        <button type="button" class="close" data-dismiss="modal" style="color: white;">×</button>
        <h4><i class="icon-plus"></i> Registrar Atividade</h4>
    </div>
    <form id="form-atividade" onsubmit="salvarAtividade(event)">
        <div class="modal-body-custom">
            <input type="hidden" name="obra_id" value="<?= $obra->id ?? '' ?>">

            <div class="form-group-custom">
                <label class="form-label-custom">Etapa</label>
                <select name="etapa_id" class="form-select-custom" required>
                    <option value="">Selecione a etapa...</option>
                    <?php foreach ($etapas as $etapa): ?>
                        <option value="<?= $etapa->id ?>"><?= htmlspecialchars($etapa->nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Tipo</label>
                <select name="tipo" class="form-select-custom" required>
                    <option value="execucao">✓ Execução</option>
                    <option value="problema">⚠ Problema</option>
                    <option value="observacao">📝 Observação</option>
                </select>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Descrição da Atividade</label>
                <textarea name="descricao" class="form-textarea-custom" placeholder="Descreva o que foi feito..." required></textarea>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Percentual Concluído</label>
                <input type="range" name="percentual_concluido" min="0" max="100" value="0" class="form-input-custom" style="padding: 0;" oninput="atualizarValorRange2(this.value)">
                <div style="text-align: center; font-size: 24px; font-weight: 700; color: #11998e; margin-top: 10px;">
                    <span id="atividade-valor">0</span>%
                </div>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Fotos de Evidência</label>
                <div class="foto-preview-container">
                    <div class="foto-preview-item" id="preview-fotos"></div>
                    <button type="button" class="foto-preview-item btn-adicionar-foto" onclick="document.getElementById('input-foto-atividade').click()">
                        <i class="icon-camera"></i>
                    </button>
                </div>
                <input type="file" id="input-foto-atividade" accept="image/*" multiple capture="environment" style="display: none;" onchange="processarFotos(this)">
                <input type="hidden" name="fotos" id="fotos-base64">
            </div>
        </div>
        <div class="modal-footer" style="padding: 0 20px 20px; border: none;">
            <button type="button" class="btn btn-large" data-dismiss="modal" style="width: 48%; margin-right: 4%;">Cancelar</button>
            <button type="submit" class="btn-submit-custom" style="width: 48%;">
                <i class="icon-save"></i> Registrar
            </button>
        </div>
    </form>
</div>

<!-- Modal: Check-in com Foto -->
<div id="modal-checkin" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header-custom">
        <button type="button" class="close" data-dismiss="modal" style="color: white;">×</button>
        <h4><i class="icon-play-circle"></i> Iniciar Trabalho</h4>
    </div>
    <div class="modal-body-custom" style="text-align: center;">
        <p style="margin-bottom: 20px;">Tire uma foto para registrar seu check-in</p>

        <div style="margin-bottom: 20px;">
            <video id="video-checkin" style="width: 100%; max-width: 400px; border-radius: 15px; display: none;" autoplay></video>
            <canvas id="canvas-checkin" style="display: none;"></canvas>
            <img id="preview-checkin" style="width: 100%; max-width: 400px; border-radius: 15px; display: none;">
        </div>

        <button type="button" id="btn-capturar-foto" class="btn-principal-acao checkin" style="margin: 10px auto;">
            <i class="icon-camera"></i> Tirar Foto
        </button>

        <div class="form-group-custom" style="text-align: left; margin-top: 20px;">
            <label class="form-label-custom">Observação (opcional)</label>
            <textarea id="observacao-checkin" class="form-textarea-custom" placeholder="Alguma observação sobre o início do trabalho..."></textarea>
        </div>
    </div>
    <div class="modal-footer" style="padding: 0 20px 20px; border: none;">
        <button type="button" class="btn btn-large" data-dismiss="modal" style="width: 48%; margin-right: 4%;">Cancelar</button>
        <button type="button" id="btn-confirmar-checkin" class="btn-submit-custom" style="width: 48%;" onclick="confirmarCheckin()" disabled>
            <i class="icon-ok"></i> Confirmar Check-in
        </button>
    </div>
</div>

<!-- Modal: Check-out com Foto -->
<div id="modal-checkout" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header-custom" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
        <button type="button" class="close" data-dismiss="modal" style="color: white;">×</button>
        <h4><i class="icon-stop"></i> Finalizar Trabalho</h4>
    </div>
    <div class="modal-body-custom" style="text-align: center;">
        <p style="margin-bottom: 20px;">Tempo trabalhado: <strong id="tempo-checkout" style="font-size: 24px;">00:00:00</strong></p>

        <div style="margin-bottom: 20px;">
            <video id="video-checkout" style="width: 100%; max-width: 400px; border-radius: 15px; display: none;" autoplay></video>
            <canvas id="canvas-checkout" style="display: none;"></canvas>
            <img id="preview-checkout" style="width: 100%; max-width: 400px; border-radius: 15px; display: none;">
        </div>

        <button type="button" id="btn-capturar-foto-checkout" class="btn-principal-acao checkout" style="margin: 10px auto;">
            <i class="icon-camera"></i> Tirar Foto
        </button>

        <div class="form-group-custom" style="text-align: left; margin-top: 20px;">
            <label class="form-label-custom">Atividades Realizadas</label>
            <textarea id="atividades-checkout" class="form-textarea-custom" placeholder="Descreva as atividades realizadas neste período..."></textarea>
        </div>

        <div class="form-group-custom" style="text-align: left;">
            <label class="form-label-custom">Observação (opcional)</label>
            <textarea id="observacao-checkout" class="form-textarea-custom" placeholder="Alguma observação sobre o trabalho realizado..."></textarea>
        </div>
    </div>
    <div class="modal-footer" style="padding: 0 20px 20px; border: none;">
        <button type="button" class="btn btn-large" data-dismiss="modal" style="width: 48%; margin-right: 4%;">Cancelar</button>
        <button type="button" id="btn-confirmar-checkout" class="btn-submit-custom" style="width: 48%; background: linear-gradient(135deg, #e74c3c, #c0392b);" onclick="confirmarCheckout()" disabled>
            <i class="icon-stop"></i> Confirmar Check-out
        </button>
    </div>
</div>

<script>
// Variáveis globais
let checkinAtivo = null;
let timerInterval = null;
let fotosCapturadas = [];
let streamVideo = null;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    verificarCheckinAtivo();
    carregarResumoDia();
    obterLocalizacao();
});

// Obter localização
function obterLocalizacao() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                document.getElementById('status-localizacao').textContent = 'Localização obtida ✓';
            },
            function(error) {
                document.getElementById('status-localizacao').textContent = 'GPS não disponível';
                console.log('Erro ao obter localização:', error);
            }
        );
    } else {
        document.getElementById('status-localizacao').textContent = 'GPS não suportado';
    }
}

// Verificar se existe check-in ativo
function verificarCheckinAtivo() {
    const obraId = '<?= $obra->id ?? 0 ?>';

    fetch('<?= site_url("tecnicos/api_checkin_ativo_obra") ?>?obra_id=' + obraId)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.checkin) {
                checkinAtivo = data.checkin;
                atualizarInterfaceCheckin(true);
                iniciarTimer(data.checkin.check_in);
            } else {
                atualizarInterfaceCheckin(false);
            }
        })
        .catch(err => {
            console.error('Erro ao verificar check-in:', err);
        });
}

// Atualizar interface baseada no status
function atualizarInterfaceCheckin(estaTrabalhando) {
    const card = document.getElementById('card-acao-principal');
    const btn = document.getElementById('btn-acao-principal');
    const textoBtn = document.getElementById('texto-btn-acao');
    const statusTexto = document.getElementById('status-trabalho-texto');
    const tempoContainer = document.getElementById('tempo-trabalhando-container');

    if (estaTrabalhando) {
        card.classList.add('ativo');
        btn.classList.remove('checkin');
        btn.classList.add('checkout');
        textoBtn.textContent = 'Finalizar Trabalho';
        statusTexto.textContent = 'Trabalhando';
        statusTexto.style.color = '#11998e';
        tempoContainer.style.display = 'block';
    } else {
        card.classList.remove('ativo');
        btn.classList.remove('checkout');
        btn.classList.add('checkin');
        textoBtn.textContent = 'Iniciar Trabalho';
        statusTexto.textContent = 'Não iniciado';
        statusTexto.style.color = '#888';
        tempoContainer.style.display = 'none';
    }
}

// Executar ação principal (check-in ou check-out)
function executarAcaoPrincipal() {
    if (checkinAtivo) {
        abrirModalCheckout();
    } else {
        abrirModalCheckin();
    }
}

// Abrir modal de check-in
function abrirModalCheckin() {
    $('#modal-checkin').modal('show');
    iniciarCamera('video-checkin', 'btn-capturar-foto');
}

// Abrir modal de check-out
function abrirModalCheckout() {
    if (checkinAtivo) {
        const inicio = new Date(checkinAtivo.check_in);
        const agora = new Date();
        const diff = Math.floor((agora - inicio) / 1000);
        const horas = Math.floor(diff / 3600);
        const minutos = Math.floor((diff % 3600) / 60);
        const segundos = diff % 60;
        document.getElementById('tempo-checkout').textContent =
            String(horas).padStart(2, '0') + ':' +
            String(minutos).padStart(2, '0') + ':' +
            String(segundos).padStart(2, '0');
    }
    $('#modal-checkout').modal('show');
    iniciarCamera('video-checkout', 'btn-capturar-foto-checkout');
}

// Iniciar câmera
function iniciarCamera(videoId, btnId) {
    const video = document.getElementById(videoId);
    const btn = document.getElementById(btnId);

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function(stream) {
                streamVideo = stream;
                video.srcObject = stream;
                video.style.display = 'block';

                btn.onclick = function() {
                    tirarFoto(videoId, videoId.replace('video', 'preview'));
                };
            })
            .catch(function(err) {
                console.log('Erro ao acessar câmera:', err);
                alert('Não foi possível acessar a câmera. Verifique as permissões.');
            });
    }
}

// Tirar foto
function tirarFoto(videoId, previewId) {
    const video = document.getElementById(videoId);
    const canvas = document.getElementById(videoId.replace('video', 'canvas'));
    const preview = document.getElementById(previewId);

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const fotoBase64 = canvas.toDataURL('image/jpeg');

    preview.src = fotoBase64;
    preview.style.display = 'block';
    video.style.display = 'none';

    // Parar câmera
    if (streamVideo) {
        streamVideo.getTracks().forEach(track => track.stop());
    }

    // Habilitar botão de confirmar
    const btnConfirmarId = videoId === 'video-checkin' ? 'btn-confirmar-checkin' : 'btn-confirmar-checkout';
    document.getElementById(btnConfirmarId).disabled = false;

    return fotoBase64;
}

// Confirmar check-in
function confirmarCheckin() {
    const preview = document.getElementById('preview-checkin');
    const observacao = document.getElementById('observacao-checkin').value;
    const latitude = document.getElementById('latitude').value;
    const longitude = document.getElementById('longitude').value;

    const dados = {
        obra_id: '<?= $obra->id ?? 0 ?>',
        latitude: latitude,
        longitude: longitude,
        foto: preview.src,
        observacao: observacao
    };

    fetch('<?= site_url("tecnicos/api_checkin_obra") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(dados)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            $('#modal-checkin').modal('hide');
            checkinAtivo = { id: data.checkin_id, check_in: new Date().toISOString() };
            atualizarInterfaceCheckin(true);
            iniciarTimer(new Date().toISOString());
            carregarResumoDia();
            alert('Check-in realizado com sucesso!');
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        alert('Erro ao realizar check-in');
    });
}

// Confirmar check-out
function confirmarCheckout() {
    const preview = document.getElementById('preview-checkout');
    const atividades = document.getElementById('atividades-checkout').value;
    const observacao = document.getElementById('observacao-checkout').value;
    const latitude = document.getElementById('latitude').value;
    const longitude = document.getElementById('longitude').value;

    const dados = {
        checkin_id: checkinAtivo.id,
        latitude: latitude,
        longitude: longitude,
        foto: preview.src,
        atividades_realizadas: atividades,
        observacao: observacao
    };

    fetch('<?= site_url("tecnicos/api_checkout_obra") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(dados)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            $('#modal-checkout').modal('hide');
            checkinAtivo = null;
            atualizarInterfaceCheckin(false);
            pararTimer();
            carregarResumoDia();
            alert('Check-out realizado! Horas trabalhadas: ' + data.horas_trabalhadas);
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        alert('Erro ao realizar check-out');
    });
}

// Timer
function iniciarTimer(horaInicio) {
    pararTimer();
    const inicio = new Date(horaInicio);

    timerInterval = setInterval(function() {
        const agora = new Date();
        const diff = Math.floor((agora - inicio) / 1000);
        const horas = Math.floor(diff / 3600);
        const minutos = Math.floor((diff % 3600) / 60);
        const segundos = diff % 60;

        document.getElementById('tempo-trabalhando').textContent =
            String(horas).padStart(2, '0') + ':' +
            String(minutos).padStart(2, '0') + ':' +
            String(segundos).padStart(2, '0');
    }, 1000);
}

function pararTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
    document.getElementById('tempo-trabalhando').textContent = '00:00:00';
}

// Carregar resumo do dia
function carregarResumoDia() {
    const obraId = '<?= $obra->id ?? 0 ?>';

    fetch('<?= site_url("tecnicos/api_relatorio_diario_obra") ?>?obra_id=' + obraId)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('resumo-horas').textContent = data.total_horas + 'h';
                document.getElementById('resumo-atividades').textContent = data.total_atividades;
                document.getElementById('resumo-checkins').textContent = data.checkins.length;

                // Atualizar lista de atividades
                if (data.atividades.length > 0) {
                    const container = document.getElementById('lista-atividades');
                    container.innerHTML = data.atividades.map(a => `
                        <div class="atividade-item">
                            <div class="atividade-header">
                                <span class="atividade-hora">
                                    <i class="icon-time"></i> ${a.data_atividade}
                                </span>
                                <span class="atividade-tipo ${a.tipo}">${a.tipo}</span>
                            </div>
                            <div class="atividade-descricao">${a.descricao}</div>
                            ${a.percentual_concluido > 0 ? `<small style="color: #11998e;">${a.percentual_concluido}% concluído</small>` : ''}
                        </div>
                    `).join('');
                }
            }
        })
        .catch(err => console.error('Erro ao carregar resumo:', err));
}

// Mostrar seção
function mostrarSecao(secao) {
    document.getElementById('secao-etapas').style.display = secao === 'etapas' ? 'block' : 'none';
    document.getElementById('secao-atividades').style.display = secao === 'atividades' ? 'block' : 'none';
    document.getElementById('secao-minhas-os').style.display = secao === 'minhas-os' ? 'block' : 'none';
}

// Toggle expandir etapas
function toggleExpandirEtapas() {
    const extras = document.querySelectorAll('.etapa-extra');
    const btn = document.getElementById('btn-ver-mais-etapas');

    extras.forEach(e => {
        if (e.classList.contains('hidden')) {
            e.classList.remove('hidden');
            btn.textContent = 'Ver menos';
        } else {
            e.classList.add('hidden');
            btn.textContent = 'Ver mais <?= count($etapas ?? []) - 3 ?> etapas';
        }
    });
}

// Abrir modal de atividade
function abrirModalAtividade() {
    $('#modal-atividade').modal('show');
}

// Atualizar valor do range
function atualizarValorRange(valor) {
    document.getElementById('progresso-valor').textContent = valor;
}

function atualizarValorRange2(valor) {
    document.getElementById('atividade-valor').textContent = valor;
}

// Atualizar progresso da etapa
function atualizarProgressoEtapa(etapaId, etapaNome) {
    document.getElementById('progresso-etapa-id').value = etapaId;
    document.getElementById('progresso-etapa-nome').value = etapaNome;
    $('#modal-progresso-etapa').modal('show');
}

// Salvar progresso da etapa
function salvarProgressoEtapa(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    formData.append('obra_id', '<?= $obra->id ?? 0 ?>');

    fetch('<?= site_url("tecnicos/api_atualizar_etapa") ?>', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            $('#modal-progresso-etapa').modal('hide');
            alert('Progresso atualizado!');
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        alert('Erro ao atualizar progresso');
    });
}

// Processar fotos da atividade
function processarFotos(input) {
    const container = document.getElementById('preview-fotos');
    const fotosBase64 = [];

    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            container.innerHTML = '';
            container.appendChild(img);
            fotosBase64.push(e.target.result);
            document.getElementById('fotos-base64').value = JSON.stringify(fotosBase64);
        };
        reader.readAsDataURL(file);
    });
}

// Salvar atividade
function salvarAtividade(event) {
    event.preventDefault();

    const formData = new FormData(event.target);

    fetch('<?= site_url("tecnicos/api_registrar_atividade_obra") ?>', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            $('#modal-atividade').modal('hide');
            document.getElementById('form-atividade').reset();
            document.getElementById('preview-fotos').innerHTML = '';
            carregarResumoDia();
            alert('Atividade registrada!');
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        alert('Erro ao registrar atividade');
    });
}

// Abrir modal relatório
function abrirModalRelatorio() {
    const obraId = '<?= $obra->id ?? 0 ?>';
    window.open('<?= site_url("tecnicos/relatorio_obra/") ?>' + obraId, '_blank');
}
</script>
