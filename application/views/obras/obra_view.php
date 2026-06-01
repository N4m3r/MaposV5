<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Nova Visualização de Obra - Design Moderno -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<div class="obra-container">
    <?php
    // Construir mapas de configuração para lookup
    $statusObraMap = [];
    foreach ($status_obra as $s) {
        $key = strtolower(preg_replace('/[^a-z]/', '', $s->nome));
        $statusObraMap[$key] = $s;
    }
    $tiposObraMap = [];
    foreach ($tipos_obra as $t) {
        $tiposObraMap[$t->nome] = $t;
    }

    // Definir classe e label do status baseado na configuração
    $status_atual_norm = strtolower(preg_replace('/[^a-z]/', '', $obra->status ?? ''));
    if (isset($statusObraMap[$status_atual_norm])) {
        $status_config = $statusObraMap[$status_atual_norm];
        $status_label = $status_config->nome;
        $status_cor = $status_config->cor ?? '#667eea';
    } else {
        // Fallback hardcoded para compatibilidade
        switch ($obra->status) {
            case 'EmExecucao':
            case 'Em Andamento':
            case 'em-andamento':
                $status_label = 'Em Andamento';
                $status_cor = '#4facfe';
                break;
            case 'Concluida':
            case 'concluida':
                $status_label = 'Concluída';
                $status_cor = '#11998e';
                break;
            case 'Paralisada':
            case 'paralisada':
                $status_label = 'Paralisada';
                $status_cor = '#f093fb';
                break;
            default:
                $status_label = ucfirst($obra->status);
                $status_cor = '#667eea';
        }
    }
    $status_class = 'status-dinamico';

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
    ?>

    <!-- Header da Obra -->
    <div class="obra-header <?php echo $status_class; ?>" style="background: linear-gradient(135deg, <?php echo $status_cor; ?> 0%, <?php echo $status_cor; ?> 100%);">
        <div class="obra-header-row">
            <div class="obra-header-info">
                <div class="obra-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><i class="bx bx-arrow-back"></i> Obras</a>
                    <span>/</span>
                    <span>Visualizar</span>
                </div>
                <h1 class="obra-title">
                    <i class="bx bx-building"></i>
                    <?php echo htmlspecialchars($obra->nome); ?>
                </h1>
                <div class="obra-cliente">
                    <i class="bx bx-user"></i>
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
        <a href="<?php echo site_url('obras/relatorioGeral/' . $obra->id); ?>" class="acao-btn acao-relatorio">
            <i class="bx bx-file-alt"></i> Relatório Geral
        </a>
        <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>" class="acao-btn acao-etapas">
            <i class="bx bx-list-check"></i> Gerenciar Etapas
        </a>
        <a href="<?php echo site_url('obras/equipe/' . $obra->id); ?>" class="acao-btn acao-equipe">
            <i class="bx bx-group"></i> Equipe
        </a>
        <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>" class="acao-btn acao-atividades">
            <i class="bx bx-check"></i> Atividades
        </a>
        <a href="<?php echo site_url('obras/editar/' . $obra->id); ?>" class="acao-btn acao-editar">
            <i class="bx bx-edit"></i> Editar
        </a>
        <a href="<?php echo site_url('obras'); ?>" class="acao-btn acao-voltar">
            <i class="bx bx-arrow-back"></i> Voltar
        </a>
    </div>

    <!-- Grid Principal -->
    <div class="obra-grid">
        <!-- Coluna Esquerda -->
        <div class="obra-coluna">
            <!-- Card de Prazo e Progresso -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-calendar"></i>
                        Prazo da Obra
                    </div>
                </div>

                <div class="prazo-card">
                    <div class="prazo-grid">
                        <div class="prazo-item">
                            <div class="prazo-label">Data de Início</div>
                            <div class="prazo-value">
                                <i class="bx bx-play"></i>
                                <?php echo $obra->data_inicio_contrato ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : 'Não definida'; ?>
                            </div>
                        </div>
                        <div class="prazo-item">
                            <div class="prazo-label">Data de Término Prevista</div>
                            <div class="prazo-value">
                                <i class="bx bx-flag-checkered"></i>
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
                                    echo '<i class="bx bx-check"></i> Finalizada';
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
                            <i class="bx bx-barcode"></i>
                            <?php echo htmlspecialchars($obra->codigo ?? 'N/A'); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tipo</div>
                        <div class="info-value">
                            <i class="bx bx-cog"></i>
                            <?php
                            echo htmlspecialchars($tiposObraMap[$obra->tipo_obra]->nome ?? ($obra->tipo_obra ?: 'Não definido'));
                            ?>
                        </div>
                    </div>
                    <div class="info-item full-width">
                        <div class="info-label">Endereço</div>
                        <div class="info-value">
                            <i class="bx bx-map"></i>
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
                    <?php if (!empty($obra->valor_contrato)): ?>
                    <div class="info-item">
                        <div class="info-label">Valor do Contrato</div>
                        <div class="info-value">
                            <i class="bx bx-money"></i>
                            R$ <?php echo number_format($obra->valor_contrato, 2, ',', '.'); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($obra->observacoes)): ?>
                    <div class="info-item full-width">
                        <div class="info-label">Observações</div>
                        <div class="info-value" style="white-space: pre-wrap;">
                            <?php echo nl2br(htmlspecialchars($obra->observacoes)); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card de Etapas -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-list-check"></i>
                        Etapas da Obra
                    </div>
                    <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>" class="card-action">
                        <i class="bx bx-plus"></i> Gerenciar
                    </a>
                </div>

                <?php if (!empty($etapas)): ?>
                <div class="etapas-container">
                    <?php
                    // Buscar atividades por etapa
                    $atividades_por_etapa = [];
                    if (!empty($atividades_recentes)) {
                        foreach ($atividades_recentes as $atividade) {
                            $etapa_id = $atividade->etapa_id ?? 0;
                            if (!isset($atividades_por_etapa[$etapa_id])) {
                                $atividades_por_etapa[$etapa_id] = [];
                            }
                            $atividades_por_etapa[$etapa_id][] = $atividade;
                        }
                    }

                    foreach ($etapas as $index => $etapa):
                        $etapa_status = $etapa->status ?? 'NaoIniciada';
                        $etapa_class = '';
                        $status_text = '';

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

                        $etapa_atividades = $atividades_por_etapa[$etapa->id] ?? [];
                        $tem_atividades = !empty($etapa_atividades);
                    ?>
                    <div class="etapa-card">
                        <div class="etapa-header-card" onclick="toggleEtapa(<?php echo $etapa->id; ?>)">
                            <div class="etapa-main-info">
                                <div class="etapa-number <?php echo $etapa_class; ?>">
                                    <?php echo $etapa->numero_etapa; ?>
                                </div>
                                <div class="etapa-info">
                                    <div class="etapa-name"><?php echo htmlspecialchars($etapa->nome); ?></div>
                                    <div class="etapa-meta-text">
                                        <span><i class="bx bx-calendar"></i>
                                            <?php
                                            if ($etapa->data_inicio_prevista && $etapa->data_fim_prevista) {
                                                echo date('d/m/Y', strtotime($etapa->data_inicio_prevista)) . ' a ' . date('d/m/Y', strtotime($etapa->data_fim_prevista));
                                            } elseif ($etapa->data_inicio_prevista) {
                                                echo 'Início: ' . date('d/m/Y', strtotime($etapa->data_inicio_prevista));
                                            } else {
                                                echo '<span style="color: #999;"><i class="bx bx-error" style="color: #f39c12;"></i> Prazo não definido</span>';
                                            }
                                            ?>
                                        </span>
                                        <?php if ($etapa->total_atividades > 0): ?>
                                        <span><i class="bx bx-list-check"></i> <?php echo $etapa->total_atividades; ?> atividade(s)</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="etapa-progress-section">
                                <div class="etapa-progress-bar">
                                    <div class="etapa-progress-fill <?php echo $etapa_class; ?>" style="width: <?php echo $etapa->percentual_concluido ?? 0; ?>%"></div>
                                </div>
                                <div class="etapa-progress-text"><?php echo $etapa->percentual_concluido ?? 0; ?>%</div>
                            </div>

                            <span class="etapa-status <?php echo $etapa_class; ?>"><?php echo $status_text; ?></span>

                            <div class="etapa-toggle <?php echo $tem_atividades ? '' : 'disabled'; ?>" id="toggle-<?php echo $etapa->id; ?>">
                                <i class="bx bx-chevron-down"></i>
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
                                            $ativ_icon = 'bx bx-check';
                                            break;
                                        case 'iniciada':
                                            $ativ_class = 'andamento';
                                            $ativ_icon = 'bx bx-play';
                                            break;
                                        case 'pausada':
                                            $ativ_class = 'pausada';
                                            $ativ_icon = 'bx bx-pause';
                                            break;
                                        default:
                                            $ativ_class = 'pendente';
                                            $ativ_icon = 'bx bx-time-five';
                                    }
                                ?>
                                <div class="atividade-item">
                                    <div class="atividade-status-icon <?php echo $ativ_class; ?>">
                                        <i class="<?php echo $ativ_icon; ?>"></i>
                                    </div>
                                    <div class="atividade-content">
                                        <div class="atividade-title"><?php echo htmlspecialchars($atividade->titulo ?? 'Atividade #' . $atividade->id); ?></div>
                                        <div class="atividade-meta">
                                            <span><i class="bx bx-calendar"></i> <?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?></span>
                                            <?php if (!empty($atividade->tecnico_nome)): ?>
                                            <span><i class="bx bx-user"></i> <?php echo htmlspecialchars($atividade->tecnico_nome); ?></span>
                                            <?php endif; ?>
                                            <span class="atividade-status-text <?php echo $ativ_class; ?>">
                                                <?php echo ucfirst($ativ_status); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="atividade-progress">
                                        <div class="atividade-progress-bar">
                                            <div class="atividade-progress-fill" style="width: <?php echo $atividade->percentual_concluido ?? 0; ?>%"></div>
                                        </div>
                                        <div class="atividade-progress-text"><?php echo $atividade->percentual_concluido ?? 0; ?>%</div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bx bx-list-check"></i>
                    <h4>Nenhuma etapa cadastrada</h4>
                    <p>Clique em "Gerenciar" para adicionar etapas à obra.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Coluna Direita -->
        <div class="obra-coluna">
            <!-- Card de Equipe -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-group"></i>
                        Equipe
                    </div>
                    <a href="<?php echo site_url('obras/equipe/' . $obra->id); ?>" class="card-action">
                        <i class="bx bx-cog"></i> Gerenciar
                    </a>
                </div>

                <?php if (!empty($equipe)): ?>
                <div class="equipe-grid">
                    <?php foreach (array_slice($equipe, 0, 8) as $membro): ?>
                    <div class="equipe-item">
                        <div class="equipe-avatar">
                            <i class="bx bx-user"></i>
                        </div>
                        <div class="equipe-name"><?php echo htmlspecialchars($membro->nome ?? $membro->tecnico_nome ?? 'Sem nome'); ?></div>
                        <div class="equipe-role"><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($equipe) > 8): ?>
                    <div class="equipe-item" style="opacity: 0.6;">
                        <div class="equipe-avatar" style="background: #95a5a6;">
                            <i class="bx bx-plus"></i>
                        </div>
                        <div class="equipe-name">+<?php echo count($equipe) - 8; ?></div>
                        <div class="equipe-role">membros</div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bx bx-group"></i>
                    <h4>Sem equipe alocada</h4>
                    <p>Adicione técnicos à equipe da obra.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Card de Atividades Recentes -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-check"></i>
                        Atividades Recentes
                    </div>
                    <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>" class="card-action">
                        <i class="bx bx-show"></i> Ver Todas
                    </a>
                </div>

                <?php if (!empty($atividades_recentes)): ?>
                <div>
                    <?php foreach (array_slice($atividades_recentes, 0, 5) as $atividade):
                        $ativ_class = ($atividade->status == 'concluida') ? 'concluida' : 'pendente';
                    ?>
                    <div class="atividade-recente <?php echo $ativ_class; ?>">
                        <div class="atividade-recente-icon">
                            <i class="bx bx-list-check"></i>
                        </div>
                        <div class="atividade-recente-content">
                            <div class="atividade-recente-title"><?php echo htmlspecialchars($atividade->titulo ?? 'Atividade #' . $atividade->id); ?></div>
                            <div class="atividade-recente-meta">
                                <i class="bx bx-calendar"></i> <?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?>
                                <?php if (!empty($atividade->tecnico_nome)): ?>
                                | <i class="bx bx-user"></i> <?php echo htmlspecialchars($atividade->tecnico_nome); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bx bx-check"></i>
                    <h4>Sem atividades recentes</h4>
                    <p>As atividades aparecerão aqui.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Estatísticas -->
            <?php if (!empty($estatisticas_atividades)): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-bar-chart"></i>
                        Estatísticas
                    </div>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Total de Atividades</div>
                        <div class="info-value">
                            <i class="bx bx-list-check" style="color: #667eea;"></i>
                            <?php echo $estatisticas_atividades['total_atividades'] ?? 0; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Concluídas</div>
                        <div class="info-value">
                            <i class="bx bx-check" style="color: #11998e;"></i>
                            <?php echo $estatisticas_atividades['concluidas'] ?? 0; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Em Andamento</div>
                        <div class="info-value">
                            <i class="bx bx-play" style="color: #f39c12;"></i>
                            <?php echo $estatisticas_atividades['em_andamento'] ?? 0; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Horas Trabalhadas</div>
                        <div class="info-value">
                            <i class="bx bx-time-five" style="color: #9b59b6;"></i>
                            <?php echo round(($estatisticas_atividades['tempo_total_minutos'] ?? 0) / 60, 1); ?>h
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Toggle de etapas
function toggleEtapa(etapaId) {
    const atividadesDiv = document.getElementById('atividades-' + etapaId);
    const toggleBtn = document.getElementById('toggle-' + etapaId);

    if (atividadesDiv) {
        atividadesDiv.classList.toggle('expanded');
        toggleBtn.classList.toggle('expanded');
    }
}

// Abrir wizard (reutilizando função existente se disponível)
function abrirWizard() {
    if (typeof window.abrirWizardModal === 'function') {
        window.abrirWizardModal();
    } else {
        // Fallback: redirecionar para página de etapas
        window.location.href = '<?php echo site_url('obras/etapas/' . $obra->id); ?>';
    }
}

// Animação de entrada
$(document).ready(function() {
    $('.card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });
});
</script>

<!-- Wizard Modal -->
<div id="wizardModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="wizardModalLabel" aria-hidden="true" style="width: 800px; max-width: 90%; left: 50%; margin-left: -400px;">
    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 4px 4px 0 0;">
        <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.8;">&times;</button>
        <h3 id="wizardModalLabel"><i class="bx bx-magic-wand"></i> Nova Etapa + Atividades</h3>
    </div>
    <form id="wizardForm" action="<?php echo site_url('obras/salvarWizard/' . $obra->id); ?>" method="post">
        <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
            <div class="row">
                <div class="col-12">
                    <h4><i class="bx bx-list-check"></i> Informações da Etapa</h4>
                    <hr style="margin: 10px 0;">

                    <div class="row">
                        <div class="col-2">
                            <label for="etapa_numero">Número <span class="required">*</span></label>
                            <input type="number" name="etapa_numero" id="etapa_numero" class="col-12" value="<?php echo (count($etapas ?? []) + 1); ?>" min="1" required>
                        </div>
                        <div class="col-10">
                            <label for="etapa_nome">Nome da Etapa <span class="required">*</span></label>
                            <input type="text" name="etapa_nome" id="etapa_nome" class="col-12" placeholder="Ex: Fundação, Estrutura, Acabamento..." required>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="col-12">
                            <label for="etapa_descricao">Descrição</label>
                            <textarea name="etapa_descricao" id="etapa_descricao" class="col-12" rows="2" placeholder="Descreva o que será feito nesta etapa..."></textarea>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="col-6">
                            <label for="etapa_data_inicio">Data de Início Prevista</label>
                            <input type="date" name="etapa_data_inicio" id="etapa_data_inicio" class="col-12">
                        </div>
                        <div class="col-6">
                            <label for="etapa_data_fim">Data de Término Prevista</label>
                            <input type="date" name="etapa_data_fim" id="etapa_data_fim" class="col-12">
                        </div>
                    </div>

                    <h4 style="margin-top: 25px;"><i class="bx bx-check"></i> Atividades da Etapa</h4>
                    <hr style="margin: 10px 0;">

                    <div id="atividadesContainer">
                        <!-- Atividades serão adicionadas aqui -->
                    </div>

                    <button type="button" class="btn btn-block" onclick="adicionarAtividade()" style="margin-top: 10px; border: 2px dashed #ddd; background: #f9f9f9; color: #666;">
                        <i class="bx bx-plus"></i> Adicionar Atividade
                    </button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Salvar Etapa e Atividades</button>
        </div>
    </form>
</div>

<script>
// Variáveis do wizard
let atividadeCount = 0;

// Abrir modal do wizard
function abrirWizard() {
    $('#wizardModal').modal('show');
    // Limpar e adicionar primeira atividade
    document.getElementById('atividadesContainer').innerHTML = '';
    adicionarAtividade();
}

// Abrir modal do wizard (compatibilidade)
window.abrirWizardModal = abrirWizard;

// Adicionar campo de atividade
function adicionarAtividade() {
    const container = document.getElementById('atividadesContainer');
    const index = atividadeCount++;

    const html = `
        <div class="row atividade-item" style="margin-bottom: 10px;" id="atividade-${index}">
            <div class="col-8">
                <input type="text" name="atividades[${index}][titulo]" class="col-12" placeholder="Título da atividade" required>
            </div>
            <div class="col-3">
                <select name="atividades[${index}][tipo]" class="col-12">
                    <option value="trabalho">Trabalho</option>
                    <option value="visita">Visita</option>
                    <option value="manutencao">Manutenção</option>
                    <option value="impedimento">Impedimento</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-danger btn-block" onclick="removerAtividade(${index})" title="Remover">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
}

// Remover campo de atividade
function removerAtividade(index) {
    const item = document.getElementById('atividade-' + index);
    if (item) {
        item.remove();
    }
}

// Validar formulário antes de enviar
document.getElementById('wizardForm').addEventListener('submit', function(e) {
    const numero = document.getElementById('etapa_numero').value;
    const nome = document.getElementById('etapa_nome').value;

    if (!numero || !nome) {
        e.preventDefault();
        alert('Preencha o número e o nome da etapa.');
        return false;
    }
});
</script>
