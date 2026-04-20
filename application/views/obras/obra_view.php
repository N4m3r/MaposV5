<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.obra-unified { padding: 20px; }
.obra-header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.obra-header-card.andamento { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.obra-header-card.concluida { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.obra-header-card.paralisada { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

.obra-header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}
.obra-title-section h1 {
    margin: 0 0 10px 0;
    font-size: 32px;
    font-weight: 700;
}
.obra-cliente-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 14px;
}
.obra-status-section {
    text-align: right;
}
.obra-status-main {
    font-size: 18px;
    font-weight: 600;
    padding: 10px 25px;
    border-radius: 25px;
    background: rgba(255,255,255,0.25);
    display: inline-block;
    margin-bottom: 10px;
}
.obra-progress-main {
    margin-top: 25px;
}
.obra-progress-bar-large {
    height: 20px;
    background: rgba(255,255,255,0.3);
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 10px;
}
.obra-progress-fill-large {
    height: 100%;
    background: white;
    border-radius: 15px;
    transition: width 0.5s ease;
}
.obra-progress-info {
    display: flex;
    justify-content: space-between;
    font-size: 16px;
    font-weight: 600;
}

.obra-actions-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}
.obra-action-btn {
    padding: 12px 25px;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: white;
    font-size: 14px;
}
.obra-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
.btn-equipe { background: #11998e; }
.btn-atividades { background: #667eea; }
.btn-relatorio { background: #764ba2; }
.btn-editar { background: #f093fb; color: #333; }
.btn-voltar { background: #6c757d; }

.obra-grid-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}
.obra-main-content { display: flex; flex-direction: column; gap: 25px; }
.obra-sidebar { display: flex; flex-direction: column; gap: 25px; }

.section-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
}
.section-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.section-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}
.section-card-title i { color: #667eea; font-size: 22px; }
.section-card-action {
    padding: 8px 15px;
    border-radius: 8px;
    background: #667eea;
    color: white;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s;
}
.section-card-action:hover {
    background: #5568d3;
    transform: translateY(-1px);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}
.info-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
}
.info-item.full-width { grid-column: span 2; }
.info-label {
    font-size: 12px;
    color: #888;
    margin-bottom: 5px;
    text-transform: uppercase;
}
.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.etapas-timeline { position: relative; padding-left: 30px; }
.etapas-timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #e8e8e8;
    border-radius: 3px;
}
.etapa-item {
    position: relative;
    padding-bottom: 25px;
}
.etapa-item:last-child { padding-bottom: 0; }
.etapa-dot {
    position: absolute;
    left: -26px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #ddd;
    border: 3px solid white;
    box-shadow: 0 0 0 3px #e8e8e8;
}
.etapa-dot.concluida { background: #11998e; box-shadow: 0 0 0 3px #11998e33; }
.etapa-dot.andamento { background: #4facfe; box-shadow: 0 0 0 3px #4facfe33; }
.etapa-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
}
.etapa-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.etapa-nome { font-weight: 600; color: #333; }
.etapa-progress {
    height: 6px;
    background: #e8e8e8;
    border-radius: 5px;
    overflow: hidden;
    margin-top: 10px;
}
.etapa-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 5px;
}

.equipe-list { display: flex; flex-direction: column; gap: 12px; }
.equipe-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s;
}
.equipe-item:hover { background: #e8e8e8; transform: translateX(5px); }
.equipe-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 600;
}
.equipe-info { flex: 1; }
.equipe-nome { font-weight: 600; color: #333; }
.equipe-funcao { font-size: 12px; color: #888; }
.equipe-status {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #11998e;
}
.equipe-status.inativo { background: #ccc; }

.stats-mini-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}
.stat-mini-item {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 20px;
    border-radius: 12px;
    text-align: center;
}
.stat-mini-value {
    font-size: 28px;
    font-weight: 700;
    color: #667eea;
}
.stat-mini-label {
    font-size: 12px;
    color: #888;
    margin-top: 5px;
}

@media (max-width: 992px) {
    .obra-grid-content { grid-template-columns: 1fr; }
}
</style>

<div class="obra-unified">
    <?php
    $headerClass = 'prospeccao';
    if ($resumo['obra']->status == 'Em Andamento') $headerClass = 'andamento';
    elseif (in_array($resumo['obra']->status, ['Concluida', 'Concluída'])) $headerClass = 'concluida';
    elseif ($resumo['obra']->status == 'Paralisada') $headerClass = 'paralisada';
    ?>

    <!-- Header Card -->
    <div class="obra-header-card <?php echo $headerClass; ?>">
        <div class="obra-header-top">
            <div class="obra-title-section">
                <h1><i class="icon-building"></i> <?php echo $resumo['obra']->nome; ?></h1>
                <div class="obra-cliente-badge">
                    <i class="icon-user"></i>
                    <?php echo $resumo['obra']->cliente_nome ?? 'Cliente não definido'; ?>
                </div>
            </div>
            <div class="obra-status-section">
                <div class="obra-status-main"><?php echo $resumo['obra']->status; ?></div>
                <div>Código: <strong>#<?php echo $resumo['obra']->id; ?></strong></div>
            </div>
        </div>

        <div class="obra-progress-main">
            <div class="obra-progress-bar-large">
                <div class="obra-progress-fill-large" style="width: <?php echo $resumo['obra']->percentual_concluido ?? 0; ?>%;"></div>
            </div>
            <div class="obra-progress-info">
                <span>Progresso Geral</span>
                <span><?php echo $resumo['obra']->percentual_concluido ?? 0; ?>% Concluído</span>
            </div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="obra-actions-bar">
        <a href="<?php echo site_url('obras/equipe/' . $resumo['obra']->id); ?>" class="obra-action-btn btn-equipe">
            <i class="icon-group"></i> Gerenciar Equipe
        </a>
        <a href="<?php echo site_url('obras/atividades/' . $resumo['obra']->id); ?>" class="obra-action-btn btn-atividades">
            <i class="icon-tasks"></i> Atividades
        </a>
        <a href="<?php echo site_url('obras/relatorioProgresso/' . $resumo['obra']->id); ?>" class="obra-action-btn btn-relatorio">
            <i class="icon-file-alt"></i> Relatórios
        </a>
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
        <a href="<?php echo site_url('obras/editar/' . $resumo['obra']->id); ?>" class="obra-action-btn btn-editar">
            <i class="icon-edit"></i> Editar Obra
        </a>
        <?php endif; ?>
        <a href="<?php echo site_url('obras'); ?>" class="obra-action-btn btn-voltar">
            <i class="icon-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="obra-grid-content">
        <!-- Main Content -->
        <div class="obra-main-content">

            <!-- Informações -->
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="icon-info-sign"></i> Informações da Obra
                    </div>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Endereço</div>
                        <div class="info-value"><i class="icon-map-marker"></i> <?php echo $resumo['obra']->endereco ?? 'N/A'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Gestor Responsável</div>
                        <div class="info-value"><i class="icon-user"></i> <?php echo $resumo['obra']->gestor_nome ?? 'Não definido'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Início</div>
                        <div class="info-value"><i class="icon-calendar"></i> <?php echo $resumo['obra']->data_inicio_contrato ? date('d/m/Y', strtotime($resumo['obra']->data_inicio_contrato)) : 'N/A'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Previsão de Término</div>
                        <div class="info-value"><i class="icon-calendar"></i> <?php echo $resumo['obra']->data_fim_prevista ? date('d/m/Y', strtotime($resumo['obra']->data_fim_prevista)) : 'N/A'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Valor do Contrato</div>
                        <div class="info-value" style="color: #667eea;">R$ <?php echo number_format($resumo['obra']->valor_contrato ?? 0, 2, ',', '.'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Dias Restantes</div>
                        <div class="info-value">
                            <?php if ($resumo['dias_restantes'] !== null): ?>
                                <?php if ($resumo['dias_restantes'] >= 0): ?>
                                    <span style="color: #11998e;"><i class="icon-time"></i> <?php echo $resumo['dias_restantes']; ?> dias</span>
                                <?php else: ?>
                                    <span style="color: #f5576c;"><i class="icon-warning-sign"></i> <?php echo abs($resumo['dias_restantes']); ?> dias em atraso</span>
                                <?php endif; ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($resumo['obra']->observacoes): ?>
                    <div class="info-item full-width">
                        <div class="info-label">Descrição</div>
                        <div class="info-value"><?php echo nl2br(htmlspecialchars($resumo['obra']->observacoes)); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Etapas -->
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="icon-tasks"></i> Etapas da Obra
                    </div>
                    <a href="<?php echo site_url('obras/etapas/' . $resumo['obra']->id); ?>" class="section-card-action">
                        Ver Todas <i class="icon-arrow-right"></i>
                    </a>
                </div>

                <div class="etapas-timeline">
                    <?php if (!empty($etapas) && is_array($etapas) && count($etapas) > 0): ?>
                        <?php foreach (array_slice($etapas, 0, 4) as $etapa): ?>
                        <?php
                        $dotClass = '';
                        if ($etapa->status == 'Concluida' || $etapa->percentual_concluido == 100) $dotClass = 'concluida';
                        elseif ($etapa->status == 'Em Andamento') $dotClass = 'andamento';
                        ?>
                        <div class="etapa-item">
                            <div class="etapa-dot <?php echo $dotClass; ?>"></div>
                            <div class="etapa-content">
                                <div class="etapa-header">
                                    <div>
                                        <span class="etapa-nome"><?php echo $etapa->numero_etapa; ?> - <?php echo $etapa->nome; ?></span>
                                        <span class="label" style="margin-left: 10px;">
                                            <?php echo $etapa->status; ?>
                                        </span>
                                    </div>
                                    <div><strong><?php echo $etapa->percentual_concluido ?? 0; ?>%</strong></div>
                                </div>
                                <div class="etapa-progress">
                                    <div class="etapa-progress-bar" style="width: <?php echo $etapa->percentual_concluido ?? 0; ?>%;"></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($etapas) > 4): ?>
                        <div style="text-align: center; padding: 15px;">
                            <a href="<?php echo site_url('obras/etapas/' . $resumo['obra']->id); ?>" style="color: #667eea; font-weight: 600;">
                                Ver mais <?php echo count($etapas) - 4; ?> etapas <i class="icon-arrow-down"></i>
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info" style="margin: 0;">
                            <i class="icon-info-sign"></i> Nenhuma etapa cadastrada.
                            <a href="<?php echo site_url('obras/etapas/' . $resumo['obra']->id); ?>">Clique aqui para adicionar.</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="obra-sidebar">

            <!-- Resumo -->
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="icon-dashboard"></i> Resumo
                    </div>
                </div>

                <div class="stats-mini-grid">
                    <div class="stat-mini-item">
                        <div class="stat-mini-value"><?php echo $resumo['total_etapas']; ?></div>
                        <div class="stat-mini-label">Etapas</div>
                    </div>
                    <div class="stat-mini-item">
                        <div class="stat-mini-value"><?php echo count($equipe); ?></div>
                        <div class="stat-mini-label">Técnicos</div>
                    </div>
                    <div class="stat-mini-item">
                        <div class="stat-mini-value"><?php echo $estatisticas['total_atividades'] ?? 0; ?></div>
                        <div class="stat-mini-label">Atividades</div>
                    </div>
                    <div class="stat-mini-item">
                        <div class="stat-mini-value"><?php echo $estatisticas['total_horas'] ?? 0; ?>h</div>
                        <div class="stat-mini-label">Horas</div>
                    </div>
                </div>
            </div>

            <!-- Equipe -->
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="icon-group"></i> Equipe
                    </div>
                    <a href="<?php echo site_url('obras/equipe/' . $resumo['obra']->id); ?>" class="section-card-action">
                        Gerenciar <i class="icon-cog"></i>
                    </a>
                </div>

                <div class="equipe-list">
                    <?php if (count($equipe) > 0): ?>
                        <?php foreach (array_slice($equipe, 0, 5) as $membro): ?>
                        <div class="equipe-item">
                            <div class="equipe-avatar">
                                <?php echo substr($membro->tecnico_nome, 0, 1); ?>
                            </div>
                            <div class="equipe-info">
                                <div class="equipe-nome"><?php echo $membro->tecnico_nome; ?></div>
                                <div class="equipe-funcao"><?php echo $membro->funcao; ?></div>
                            </div>
                            <div class="equipe-status <?php echo $membro->ativo ? '' : 'inativo'; ?>"></div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($equipe) > 5): ?>
                        <div style="text-align: center; padding-top: 10px;">
                            <a href="<?php echo site_url('obras/equipe/' . $resumo['obra']->id); ?>" style="color: #667eea;">
                                + <?php echo count($equipe) - 5; ?> mais...
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center" style="padding: 20px;">
                            <i class="icon-group" style="font-size: 40px; color: #ddd;"></i><br>
                            <small style="color: #888;">Nenhum técnico alocado</small><br><br>
                            <a href="<?php echo site_url('obras/equipe/' . $resumo['obra']->id); ?>" class="btn btn-small btn-success">
                                <i class="icon-plus"></i> Adicionar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Links Rápidos -->
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="icon-link"></i> Ações Rápidas
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="<?php echo site_url('obras/atividades/' . $resumo['obra']->id); ?>" class="btn" style="text-align: left; padding: 15px; border-radius: 10px; background: #f8f9fa;">
                        <i class="icon-tasks" style="color: #667eea;"></i> <strong>Atividades</strong>
                        <span class="pull-right badge badge-info"><?php echo $estatisticas['total_atividades'] ?? 0; ?></span>
                    </a>

                    <a href="<?php echo site_url('obras/relatorioDiario/' . $resumo['obra']->id); ?>" class="btn" style="text-align: left; padding: 15px; border-radius: 10px; background: #f8f9fa;">
                        <i class="icon-calendar" style="color: #11998e;"></i> <strong>Diário de Obra</strong>
                    </a>

                    <a href="<?php echo site_url('obras/relatorioProgresso/' . $resumo['obra']->id); ?>" class="btn" style="text-align: left; padding: 15px; border-radius: 10px; background: #f8f9fa;">
                        <i class="icon-file-alt" style="color: #764ba2;"></i> <strong>Relatório PDF</strong>
                    </a>

                    <a href="<?php echo site_url('os/adicionar?obra_id=' . $resumo['obra']->id); ?>" class="btn" style="text-align: left; padding: 15px; border-radius: 10px; background: #f8f9fa;">
                        <i class="icon-plus" style="color: #f093fb;"></i> <strong>Vincular OS</strong>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Animate progress bar on load
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.querySelector('.obra-progress-fill-large');
    const width = progressBar.style.width;
    progressBar.style.width = '0%';
    setTimeout(() => {
        progressBar.style.width = width;
    }, 300);
});
</script>
