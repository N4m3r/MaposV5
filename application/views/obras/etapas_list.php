<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Página de Etapas - Design Moderno -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

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
                    <a href="<?php echo site_url('obras'); ?>"><i class="bx bx-arrow-back"></i> Obras</a>
                    <span>/</span>
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo htmlspecialchars($obra->nome); ?></a>
                    <span>/</span>
                    <span>Etapas</span>
                </div>
                <h1 class="obra-title">
                    <i class="bx bx-list-check"></i>
                    Gerenciar Etapas
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
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
        <button class="acao-btn acao-etapa" onclick="abrirModalEtapa()">
            <i class="bx bx-plus"></i> Nova Etapa
        </button>
        <?php endif; ?>
        <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="acao-btn acao-visualizar">
            <i class="bx bx-show"></i> Ver Obra
        </a>
        <a href="<?php echo site_url('obras'); ?>" class="acao-btn acao-voltar">
            <i class="bx bx-arrow-back"></i> Voltar
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
                            <?php echo htmlspecialchars($obra->tipo_obra ?? 'N/A'); ?>
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
                </div>
            </div>

            <!-- Card de Etapas -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-trip"></i>
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
                                                <span><i class="bx bx-list-check"></i> <?php echo $etapa->total_atividades; ?> ativ.</span>
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
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                                        <a href="<?php echo site_url('obras/excluirEtapa/' . $etapa->id); ?>" class="etapa-btn etapa-btn-delete" onclick="event.stopPropagation(); return confirm('Tem certeza que deseja excluir esta etapa?');" title="Excluir">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($tem_atividades): ?>
                                        <div class="etapa-toggle" id="toggle-<?php echo $etapa->id; ?>" onclick="event.stopPropagation(); toggleEtapa(<?php echo $etapa->id; ?>)">
                                            <i class="bx bx-chevron-down"></i>
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
                                        <div class="atividade-subitem">
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
                        <i class="bx bx-list-check"></i>
                        <h4>Nenhuma etapa cadastrada</h4>
                        <p>Adicione etapas para organizar o progresso da obra.</p>
                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                        <button onclick="abrirModalEtapa()" class="card-action" style="margin-top: 16px;">
                            <i class="bx bx-plus"></i> Adicionar Primeira Etapa
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
                        <i class="bx bx-bar-chart"></i>
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
                        <i class="bx bx-group"></i>
                        Equipe
                    </div>
                </div>

                <?php if (!empty($equipe)): ?>
                <div class="equipe-grid">
                    <?php foreach (array_slice($equipe, 0, 6) as $membro): ?>
                    <div class="equipe-item">
                        <div class="equipe-avatar">
                            <i class="bx bx-user"></i>
                        </div>
                        <div class="equipe-name"><?php echo htmlspecialchars($membro->nome ?? $membro->tecnico_nome ?? 'Sem nome'); ?></div>
                        <div class="equipe-role"><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($equipe) > 6): ?>
                    <div class="equipe-item" style="opacity: 0.6;">
                        <div class="equipe-avatar" style="background: #95a5a6;">
                            <i class="bx bx-plus"></i>
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
                        <i class="bx bx-check"></i>
                        Atividades Recentes
                    </div>
                </div>

                <?php if (!empty($atividades_recentes)): ?>
                <div>
                    <?php foreach (array_slice($atividades_recentes, 0, 5) as $atividade): ?>
                    <div style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(102, 126, 234, 0.1); color: #667eea; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bx bx-list-check"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 13px; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($atividade->titulo ?? 'Atividade #' . $atividade->id); ?></div>
                            <div style="font-size: 11px; color: #888;">
                                <i class="bx bx-calendar"></i> <?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?>
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
                        <i class="bx bx-time-five"></i>
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
<div id="modalEtapa" class="modal fade modal-etapas" tabindex="-1" role="dialog" aria-labelledby="modalEtapaLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="modalEtapaLabel"><i class="bx bx-plus-circle"></i> <span id="modalEtapaTitle">Nova Etapa</span></h3>
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
            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
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

