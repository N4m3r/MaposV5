<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<?php
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
        case 'EmExecucao': case 'Em Andamento': case 'em-andamento':
            $status_class = 'em-andamento'; $status_label = 'Em Andamento'; $status_cor = '#4facfe'; break;
        case 'Concluida': case 'concluida':
            $status_class = 'concluida'; $status_label = 'Concluída'; $status_cor = '#11998e'; break;
        case 'Paralisada': case 'paralisada':
            $status_class = 'paralisada'; $status_label = 'Paralisada'; $status_cor = '#f093fb'; break;
        default:
            $status_class = ''; $status_label = ucfirst($obra->status);
    }
}

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
$total_etapas = count($etapas);
$etapas_concluidas = count(array_filter($etapas, function($e) { return $e->status == 'Concluida'; }));
$etapas_andamento = count(array_filter($etapas, function($e) { return $e->status == 'EmAndamento'; }));
$etapas_pendentes = $total_etapas - $etapas_concluidas - $etapas_andamento;
?>

<style>
.obra-header.status-dinamico {
    background: linear-gradient(135deg, <?php echo $status_cor; ?> 0%, <?php echo $status_cor; ?> 100%) !important;
}
</style>

<div class="etapas-container">
    <div class="obra-header <?php echo $status_class; ?>">
        <div class="obra-header-row">
            <div class="obra-header-info">
                <div class="obra-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><?= svg_icon('chevron-left', 14, 14) ?> Obras</a>
                    <span>/</span>
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo htmlspecialchars($obra->nome); ?></a>
                    <span>/</span>
                    <span>Etapas</span>
                </div>
                <h1 class="obra-title">
                    <?= svg_icon('list-check', 28, 28) ?>
                    Gerenciar Etapas
                </h1>
                <div class="obra-cliente">
                    <?= svg_icon('user', 16, 16) ?>
                    <?php echo htmlspecialchars($obra->cliente_nome ?? 'Sem cliente'); ?>
                </div>
            </div>

            <div class="obra-header-status">
                <span class="status-badge"><?php echo $status_label; ?></span>

                <div class="obra-progress-section">
                    <div class="progresso-header">
                        <span class="progresso-label">Progresso Total</span>
                        <span class="progresso-valor"><?php echo $progresso; ?>%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" style="width: <?php echo $progresso; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="acoes-bar">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
        <button class="acao-btn edit" onclick="abrirModalEtapa()">
            <?= svg_icon('plus', 16, 16) ?> Nova Etapa
        </button>
        <?php endif; ?>
        <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="acao-btn view">
            <?= svg_icon('eye', 16, 16) ?> Ver Obra
        </a>
        <a href="<?php echo site_url('obras'); ?>" class="acao-btn back">
            <?= svg_icon('chevron-left', 16, 16) ?> Voltar
        </a>
    </div>

    <div class="obra-grid">
        <div class="obra-coluna">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <?= svg_icon('calendar', 22, 22) ?>
                        Prazo da Obra
                    </div>
                </div>

                <div class="prazo-card">
                    <div class="prazo-grid">
                        <div class="prazo-item">
                            <div class="prazo-label">Data de Início</div>
                            <div class="prazo-value">
                                <?= svg_icon('play', 16, 16) ?>
                                <?php echo $obra->data_inicio_contrato ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : 'Não definida'; ?>
                            </div>
                        </div>
                        <div class="prazo-item">
                            <div class="prazo-label">Data de Término Prevista</div>
                            <div class="prazo-value">
                                <?= svg_icon('flag-checkered', 16, 16) ?>
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
                                    echo svg_icon('check', 16, 16) . ' Finalizada';
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
                            <?= svg_icon('barcode', 16, 16) ?>
                            <?php echo htmlspecialchars($obra->codigo ?? 'N/A'); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tipo</div>
                        <div class="info-value">
                            <?= svg_icon('cog', 16, 16) ?>
                            <?php echo htmlspecialchars($obra->tipo_obra ?? 'N/A'); ?>
                        </div>
                    </div>
                    <div class="info-item full-width">
                        <div class="info-label">Endereço</div>
                        <div class="info-value">
                            <?= svg_icon('map', 16, 16) ?>
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

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <?= svg_icon('list-check', 22, 22) ?>
                        Timeline de Etapas
                    </div>
                    <div class="card-header-count">
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
                                    case 'Concluida': case 'concluida': $etapa_class = 'concluida'; $status_text = 'Concluída'; break;
                                    case 'EmAndamento': case 'Em Andamento': case 'em-andamento': $etapa_class = 'andamento'; $status_text = 'Em Andamento'; break;
                                    case 'Atrasada': case 'atrasada': $etapa_class = 'atrasada'; $status_text = 'Atrasada'; break;
                                    default: $etapa_class = 'pendente'; $status_text = 'Não Iniciada';
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
                                                <span><?= svg_icon('calendar', 14, 14) ?>
                                                    <?php
                                                    if ($etapa->data_inicio_prevista && $etapa->data_fim_prevista) {
                                                        echo date('d/m/Y', strtotime($etapa->data_inicio_prevista)) . ' a ' . date('d/m/Y', strtotime($etapa->data_fim_prevista));
                                                    } elseif ($etapa->data_inicio_prevista) {
                                                        echo 'Início: ' . date('d/m/Y', strtotime($etapa->data_inicio_prevista));
                                                    } else {
                                                        echo '<span class="etapa-prazo-undefined">' . svg_icon('error-circle', 14, 14) . ' Prazo não definido</span>';
                                                    }
                                                    ?>
                                                </span>
                                                <?php if ($etapa->total_atividades > 0): ?>
                                                <span><?= svg_icon('list-check', 14, 14) ?> <?php echo $etapa->total_atividades; ?> ativ.</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="etapa-progress-section">
                                        <div class="etapa-progress-bar">
                                            <div class="etapa-progress-fill <?php echo $etapa_class; ?>" style="width: <?php echo $progresso_etapa; ?>%"></div>
                                        </div>
                                        <div class="etapa-progress-text"><?php echo $progresso_etapa; ?>%</div>
                                    </div>

                                    <span class="etapa-status <?php echo $etapa_class; ?>"><?php echo $status_text; ?></span>

                                    <div class="etapa-actions">
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                                        <button type="button" class="etapa-btn etapa-btn-edit" onclick="event.stopPropagation(); editarEtapa(<?php echo $etapa->id; ?>, '<?php echo htmlspecialchars($etapa->nome, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($etapa->descricao ?? '', ENT_QUOTES); ?>', '<?php echo $etapa->numero_etapa; ?>', '<?php echo $etapa->especialidade ?? ''; ?>', '<?php echo $etapa->data_inicio_prevista ?? ''; ?>', '<?php echo $etapa->data_fim_prevista ?? ''; ?>', '<?php echo $etapa->status; ?>', '<?php echo $progresso_etapa; ?>')" title="Editar">
                                            <?= svg_icon('edit', 14, 14) ?>
                                        </button>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                                        <a href="<?php echo site_url('obras/excluirEtapa/' . $etapa->id); ?>" class="etapa-btn etapa-btn-delete" onclick="event.stopPropagation(); return confirm('Tem certeza que deseja excluir esta etapa?');" title="Excluir">
                                            <?= svg_icon('trash', 14, 14) ?>
                                        </a>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($tem_atividades): ?>
                                        <div class="etapa-toggle" id="toggle-<?php echo $etapa->id; ?>" onclick="event.stopPropagation(); toggleEtapa(<?php echo $etapa->id; ?>)">
                                            <?= svg_icon('chevron-down', 14, 14) ?>
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
                                                case 'concluida': $ativ_class = 'concluida'; $ativ_icon = 'check'; break;
                                                case 'iniciada': $ativ_class = 'andamento'; $ativ_icon = 'play'; break;
                                                case 'pausada': $ativ_class = 'pausada'; $ativ_icon = 'pause'; break;
                                                default: $ativ_class = 'pendente'; $ativ_icon = 'clock';
                                            }
                                        ?>
                                        <div class="atividade-subitem">
                                            <div class="atividade-status-icon <?php echo $ativ_class; ?>">
                                                <?= svg_icon($ativ_icon, 16, 16) ?>
                                            </div>
                                            <div class="atividade-content">
                                                <div class="atividade-title"><?php echo htmlspecialchars($atividade->titulo ?? 'Atividade #' . $atividade->id); ?></div>
                                                <div class="atividade-meta">
                                                    <span><?= svg_icon('calendar', 14, 14) ?> <?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?></span>
                                                    <?php if (!empty($atividade->tecnico_nome)): ?>
                                                    <span><?= svg_icon('user', 14, 14) ?> <?php echo htmlspecialchars($atividade->tecnico_nome); ?></span>
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
                    <div class="empty-state-moderno">
                        <?= svg_icon('list-check', 48, 48, '', 'opacity:0.4;display:block;margin:0 auto 16px;') ?>
                        <h4>Nenhuma etapa cadastrada</h4>
                        <p>Adicione etapas para organizar o progresso da obra.</p>
                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                        <button onclick="abrirModalEtapa()" class="config-add-btn">
                            <?= svg_icon('plus', 16, 16) ?> Adicionar Primeira Etapa
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="obra-coluna">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <?= svg_icon('bar-chart', 22, 22) ?>
                        Estatísticas
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $total_etapas; ?></div>
                        <div class="stat-label">Total de Etapas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value green"><?php echo $etapas_concluidas; ?></div>
                        <div class="stat-label">Concluídas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value blue"><?php echo $etapas_andamento; ?></div>
                        <div class="stat-label">Em Andamento</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value gray"><?php echo $etapas_pendentes; ?></div>
                        <div class="stat-label">Pendentes</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <?= svg_icon('users', 22, 22) ?>
                        Equipe
                    </div>
                </div>

                <?php if (!empty($equipe)): ?>
                <div class="equipe-grid">
                    <?php foreach (array_slice($equipe, 0, 6) as $membro): ?>
                    <div class="equipe-item">
                        <div class="equipe-avatar">
                            <?= svg_icon('user', 16, 16) ?>
                        </div>
                        <div class="equipe-name"><?php echo htmlspecialchars($membro->nome ?? $membro->tecnico_nome ?? 'Sem nome'); ?></div>
                        <div class="equipe-role"><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($equipe) > 6): ?>
                    <div class="equipe-item equipe-item-overflow">
                        <div class="equipe-avatar equipe-avatar-gray">
                            <?= svg_icon('plus', 16, 16) ?>
                        </div>
                        <div class="equipe-name">+<?php echo count($equipe) - 6; ?></div>
                        <div class="equipe-role">membros</div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="empty-state-moderno">
                    <p>Sem equipe alocada</p>
                </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <?= svg_icon('check', 22, 22) ?>
                        Atividades Recentes
                    </div>
                </div>

                <?php if (!empty($atividades_recentes)): ?>
                <div>
                    <?php foreach (array_slice($atividades_recentes, 0, 5) as $atividade):
                        $ativ_class = ($atividade->status == 'concluida') ? 'concluida' : 'pendente';
                    ?>
                    <div class="atividade-recente <?php echo $ativ_class; ?>">
                        <div class="atividade-recente-icon">
                            <?= svg_icon('list-check', 22, 22) ?>
                        </div>
                        <div class="atividade-recente-content">
                            <div class="atividade-recente-title"><?php echo htmlspecialchars($atividade->titulo ?? 'Atividade #' . $atividade->id); ?></div>
                            <div class="atividade-recente-meta">
                                <?= svg_icon('calendar', 14, 14) ?> <?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state-moderno">
                    <p>Sem atividades recentes</p>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($estatisticas_atividades)): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <?= svg_icon('clock', 22, 22) ?>
                        Registro de Horas
                    </div>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $estatisticas_atividades['total_atividades'] ?? 0; ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value green"><?php echo round(($estatisticas_atividades['tempo_total_minutos'] ?? 0) / 60, 1); ?>h</div>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="modalEtapaLabel"><?= svg_icon('plus-circle', 20, 20) ?> <span id="modalEtapaTitle">Nova Etapa</span></h3>
            </div>

            <form id="formEtapa" action="<?php echo site_url('obras/adicionarEtapa'); ?>" method="post">
                <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
                <div class="modal-body">
                    <div class="form-grid">
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

                    <div class="form-grid">
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
    </div>
</div>

<script>
function toggleEtapa(etapaId) {
    const atividadesDiv = document.getElementById('atividades-' + etapaId);
    const toggleBtn = document.getElementById('toggle-' + etapaId);
    if (atividadesDiv) {
        atividadesDiv.classList.toggle('expanded');
        if (toggleBtn) toggleBtn.classList.toggle('expanded');
    }
}

function abrirModalEtapa() {
    document.getElementById('formEtapa').action = '<?php echo site_url("obras/adicionarEtapa"); ?>';
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

function editarEtapa(id, nome, descricao, numero, especialidade, dataInicio, dataFim, status, percentual) {
    document.getElementById('formEtapa').action = '<?php echo site_url("obras/editarEtapa/"); ?>' + id;
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

$(document).ready(function() {
    $('.card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });
    $('.etapa-item').each(function(index) {
        $(this).hide().delay(index * 150).fadeIn(500);
    });
});
</script>