<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Font Awesome como fallback para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<div class="obras-container">

    <!-- Header -->
    <div class="obra-header">
        <div class="obra-header-content">
            <div class="obra-header-left">
                <div class="obra-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>">Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo htmlspecialchars($obra->nome); ?></a> &raquo;
                    <span>Atividades</span>
                </div>
                <h1><i class='bx bx-task'></i> Atividades da Obra</h1>
                <div class="obra-header-subtitle">
                    <span><i class='bx bx-building'></i> <?php echo htmlspecialchars($obra->nome); ?></span>
                    <span><i class='bx bx-user'></i> <?php echo htmlspecialchars($obra->cliente_nome ?? 'Cliente não definido'); ?></span>
                    <span><i class='bx bx-calendar'></i> <?php echo count($atividades); ?> atividade(s)</span>
                </div>
            </div>
            <div>
                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Voltar à Obra
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <?php
        $total = count($atividades);
        $concluidas = count(array_filter($atividades, fn($a) => $a->status === 'concluida'));
        $em_andamento = count(array_filter($atividades, fn($a) => in_array($a->status, ['iniciada', 'pausada'])));
        $agendadas = count(array_filter($atividades, fn($a) => $a->status === 'agendada'));
        $reabertas = count(array_filter($atividades, fn($a) => $a->status === 'reaberta'));
        $percentual = $total > 0 ? round(($concluidas / $total) * 100) : 0;
        ?>
        <div class="stat-card success">
            <div class="stat-value"><?php echo $concluidas; ?></div>
            <div class="stat-label">Concluídas</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value"><?php echo $em_andamento; ?></div>
            <div class="stat-label">Em Andamento</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value"><?php echo $agendadas; ?></div>
            <div class="stat-label">Agendadas</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-value"><?php echo $reabertas; ?></div>
            <div class="stat-label">Reabertas</div>
        </div>
        <div class="stat-card <?php echo $percentual >= 80 ? 'success' : ($percentual >= 50 ? 'warning' : 'danger'); ?>">
            <div class="stat-value"><?php echo $percentual; ?>%</div>
            <div class="stat-label">Progresso</div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class='bx bx-list-ul'></i> Lista de Atividades
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn btn-primary btn-sm" onclick="abrirModalNovaAtividade()">
                    <i class='bx bx-plus'></i> Nova Atividade
                </button>
                <a href="<?php echo site_url('obras/salvarWizard/' . $obra->id); ?>" class="btn btn-success btn-sm">
                    <i class='bx bx-layer'></i> Wizard de Etapas
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-nav">
            <button class="tab-btn active" onclick="switchTab('todas', this)">
                <i class='bx bx-grid-alt'></i> Todas (<?php echo $total; ?>)
            </button>
            <button class="tab-btn" onclick="switchTab('agendadas', this)">
                <i class='bx bx-calendar'></i> Agendadas (<?php echo $agendadas; ?>)
            </button>
            <button class="tab-btn" onclick="switchTab('andamento', this)">
                <i class='bx bx-play-circle'></i> Em Andamento (<?php echo $em_andamento; ?>)
            </button>
            <button class="tab-btn" onclick="switchTab('concluidas', this)">
                <i class='bx bx-check-circle'></i> Concluídas (<?php echo $concluidas; ?>)
            </button>
            <button class="tab-btn" onclick="switchTab('reabertas', this)">
                <i class='bx bx-refresh'></i> Reabertas (<?php echo $reabertas; ?>)
            </button>
        </div>

        <div class="card-body">
            <!-- Filtros -->
            <div class="filtros-grid">
                <div class="filtro-group">
                    <label>Buscar</label>
                    <input type="text" id="filtroBusca" placeholder="Título, descrição..." onkeyup="filtrarAtividades()">
                </div>
                <div class="filtro-group">
                    <label>Técnico</label>
                    <select id="filtroTecnico" onchange="filtrarAtividades()">
                        <option value="">Todos</option>
                        <?php foreach ($tecnicos as $t): ?>
                        <option value="<?php echo $t->idUsuarios; ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filtro-group">
                    <label>Etapa</label>
                    <select id="filtroEtapa" onchange="filtrarAtividades()">
                        <option value="">Todas</option>
                        <?php foreach ($etapas as $e): ?>
                        <option value="<?php echo $e->id; ?>"><?php echo htmlspecialchars($e->nome); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filtro-group">
                    <label>Tipo</label>
                    <select id="filtroTipo" onchange="filtrarAtividades()">
                        <option value="">Todos</option>
                        <?php foreach ($tipos_atividades as $t): ?>
                            <option value="<?php echo htmlspecialchars($t->nome); ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Tabela -->
            <?php if (empty($atividades)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class='bx bx-task-x'></i>
                </div>
                <h4>Nenhuma atividade encontrada</h4>
                <p>Esta obra ainda não possui atividades cadastradas.</p>
                <button class="btn btn-primary" onclick="abrirModalNovaAtividade()">
                    <i class='bx bx-plus'></i> Criar Primeira Atividade
                </button>
            </div>
            <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="atividades-table" id="tabelaAtividades">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Atividade</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 100px;">Progresso</th>
                            <th>Técnico</th>
                            <th>Etapa</th>
                            <th style="width: 180px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($atividades as $atividade):
                            $statusClass = $atividade->status ?? 'agendada';
                            $progresso = $atividade->percentual_concluido ?? 0;
                            $progressoClass = $progresso >= 80 ? 'high' : ($progresso >= 50 ? 'medium' : 'low');
                        ?>
                        <tr class="atividade-row"
                            data-status="<?php echo $statusClass; ?>"
                            data-tecnico="<?php echo $atividade->tecnico_id ?? ''; ?>"
                            data-etapa="<?php echo $atividade->etapa_id ?? ''; ?>"
                            data-tipo="<?php echo $atividade->tipo ?? 'trabalho'; ?>"
                            data-busca="<?php echo strtolower(htmlspecialchars(($atividade->titulo ?? '') . ' ' . ($atividade->descricao ?? ''))); ?>">
                            <td><?php echo $atividade->id; ?></td>
                            <td>
                                <div class="info-cell">
                                    <span class="info-primary"><?php echo htmlspecialchars($atividade->titulo ?? 'Sem título'); ?></span>
                                    <span class="info-secondary"><?php echo htmlspecialchars(substr($atividade->descricao ?? '', 0, 60)) . (strlen($atividade->descricao ?? '') > 60 ? '...' : ''); ?></span>
                                    <span class="info-tertiary">
                                        <i class='bx bx-calendar'></i> <?php echo date('d/m/Y', strtotime($atividade->data_atividade ?? 'now')); ?>
                                        <?php if ($atividade->tipo === 'impedimento'): ?>
                                            <span style="color: #e74c3c; margin-left: 8px;"><i class='bx bx-error'></i> Impedimento</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($statusClass); ?>
                                </span>
                                <?php if ($statusClass === 'reaberta'): ?>
                                    <div class="reatendimento-badge" style="margin-top: 6px;">
                                        <i class='bx bx-refresh'></i> Reatendimento
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="progress-wrapper">
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill <?php echo $progressoClass; ?>" style="width: <?php echo $progresso; ?>%"></div>
                                    </div>
                                    <span class="progress-text"><?php echo $progresso; ?>%</span>
                                </div>
                            </td>
                            <td>
                                <div class="info-cell">
                                    <span class="info-primary"><?php echo htmlspecialchars($atividade->tecnico_nome ?? 'Não atribuído'); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="info-cell">
                                    <span class="info-primary"><?php echo htmlspecialchars($atividade->etapa_nome ?? 'Sem etapa'); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="acoes-cell">
                                    <button class="btn btn-primary btn-xs btn-icon" onclick="abrirModalEditar(<?php echo $atividade->id; ?>)" title="Editar Rápido">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <a href="<?php echo site_url('obras/visualizarAtividade/' . $atividade->id); ?>" class="btn btn-info btn-xs btn-icon" title="Visualizar Completo">
                                        <i class='bx bx-eye'></i>
                                    </a>
                                    <?php if (in_array($statusClass, ['concluida', 'cancelada', 'reaberta'])): ?>
                                    <button class="btn btn-warning btn-xs" onclick="reabrirAtividade(<?php echo $atividade->id; ?>)" title="Reabrir/Reatendimento">
                                        <i class='bx bx-refresh'></i> Reabrir
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($statusClass === 'agendada'): ?>
                                    <a href="<?php echo site_url('tecnicos/executar_obra/' . $obra->id . '?atividade=' . $atividade->id); ?>" class="btn btn-success btn-xs btn-icon" title="Iniciar Execução">
                                        <i class='bx bx-play'></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Atividades Registradas (Hora Início/Fim) -->
    <?php if (!empty($atividades_registradas)): ?>
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class='bx bx-time'></i> Registros de Execução (Wizard)
            </div>
            <span class="badge-count" style="background: #27ae60; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                <?php echo count($atividades_registradas); ?> registros
            </span>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto;">
                <table class="atividades-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Técnico</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Duração</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($atividades_registradas as $reg): ?>
                        <tr>
                            <td><?php echo $reg->idAtividade; ?></td>
                            <td><?php echo htmlspecialchars($reg->tipo_nome ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($reg->nome_tecnico ?? 'N/A'); ?></td>
                            <td><?php echo $reg->hora_inicio ? date('d/m/Y H:i', strtotime($reg->hora_inicio)) : '-'; ?></td>
                            <td><?php echo $reg->hora_fim ? date('d/m/Y H:i', strtotime($reg->hora_fim)) : '-'; ?></td>
                            <td>
                                <?php if ($reg->duracao_minutos): ?>
                                    <?php echo floor($reg->duracao_minutos / 60); ?>h <?php echo $reg->duracao_minutos % 60; ?>m
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $reg->status ?? 'em_andamento'; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $reg->status ?? 'em_andamento')); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo site_url('obras/visualizarAtividade/' . $reg->obra_atividade_id); ?>" class="btn btn-info btn-xs btn-icon">
                                    <i class='bx bx-eye'></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- Modal: Editar Atividade -->
<div class="modal-overlay" id="modalEditar">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-title">
                <i class='bx bx-edit'></i> Editar Atividade
            </div>
            <button class="modal-close" onclick="fecharModal('modalEditar')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <form id="formEditar" method="POST">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Título <span class="required">*</span></label>
                        <input type="text" name="titulo" id="edit_titulo" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Descrição</label>
                        <textarea name="descricao" id="edit_descricao" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status">
                            <?php foreach ($status_atividade as $s): ?>
                                <option value="<?php echo htmlspecialchars($s->nome); ?>"><?php echo htmlspecialchars($s->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Data</label>
                        <input type="date" name="data_atividade" id="edit_data">
                    </div>
                    <div class="form-group">
                        <label>Técnico</label>
                        <select name="tecnico_id" id="edit_tecnico">
                            <option value="">Não atribuído</option>
                            <?php foreach ($tecnicos as $t): ?>
                            <option value="<?php echo $t->idUsuarios; ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Etapa</label>
                        <select name="etapa_id" id="edit_etapa">
                            <option value="">Sem etapa</option>
                            <?php foreach ($etapas as $e): ?>
                            <option value="<?php echo $e->id; ?>"><?php echo htmlspecialchars($e->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="tipo" id="edit_tipo">
                            <?php foreach ($tipos_atividades as $t): ?>
                                <option value="<?php echo htmlspecialchars($t->nome); ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Progresso (%)</label>
                        <input type="number" name="percentual_concluido" id="edit_progresso" min="0" max="100" value="0">
                    </div>
                    <div class="form-group full-width">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="visivel_cliente" id="edit_visivel" value="1">
                            <label for="edit_visivel">Visível ao cliente</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalEditar')">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save'></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Nova Atividade -->
<div class="modal-overlay" id="modalNova">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-title">
                <i class='bx bx-plus'></i> Nova Atividade
            </div>
            <button class="modal-close" onclick="fecharModal('modalNova')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <form action="<?php echo site_url('obras/adicionarAtividade'); ?>" method="POST">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Título <span class="required">*</span></label>
                        <input type="text" name="titulo" required placeholder="Nome da atividade...">
                    </div>
                    <div class="form-group full-width">
                        <label>Descrição</label>
                        <textarea name="descricao" rows="3" placeholder="Descreva a atividade..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="tipo">
                            <?php foreach ($tipos_atividades as $t): ?>
                                <option value="<?php echo htmlspecialchars($t->nome); ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Data</label>
                        <input type="date" name="data_atividade" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Técnico</label>
                        <select name="tecnico_id">
                            <option value="">Não atribuído</option>
                            <?php foreach ($tecnicos as $t): ?>
                            <option value="<?php echo $t->idUsuarios; ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Etapa</label>
                        <select name="etapa_id">
                            <option value="">Sem etapa</option>
                            <?php foreach ($etapas as $e): ?>
                            <option value="<?php echo $e->id; ?>"><?php echo htmlspecialchars($e->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="visivel_cliente" value="1" checked>
                            <label>Visível ao cliente</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalNova')">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save'></i> Criar Atividade
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reabrir Atividade (Reatendimento) -->
<div class="modal-overlay" id="modalReabrir">
    <div class="modal-container" style="max-width: 500px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;">
            <div class="modal-title" style="color: white;">
                <i class='bx bx-refresh'></i> Reabrir Atividade - Reatendimento
            </div>
            <button class="modal-close" onclick="fecharModal('modalReabrir')" style="background: rgba(255,255,255,0.2); color: white;">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <form id="formReabrir" method="POST" action="">
            <div class="modal-body">
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin: 0; font-size: 14px; color: #666;">
                        <i class='bx bx-info-circle'></i>
                        Ao reabrir esta atividade, um <strong>reatendimento</strong> será criado para permitir nova execução.
                        O histórico anterior será preservado.
                    </p>
                </div>
                <div class="form-group full-width">
                    <label>Motivo da Reabertura <span class="required">*</span></label>
                    <textarea name="observacao_status" rows="3" required placeholder="Informe o motivo da reabertura..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalReabrir')">Cancelar</button>
                <button type="submit" class="btn" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;">
                    <i class='bx bx-refresh'></i> Confirmar Reabertura
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Dados das atividades para edição rápida
const atividadesData = <?php echo json_encode(array_map(function($a) {
    return [
        'id' => $a->id,
        'titulo' => $a->titulo ?? '',
        'descricao' => $a->descricao ?? '',
        'status' => $a->status ?? 'agendada',
        'data_atividade' => $a->data_atividade ?? date('Y-m-d'),
        'tecnico_id' => $a->tecnico_id ?? '',
        'etapa_id' => $a->etapa_id ?? '',
        'tipo' => $a->tipo ?? 'trabalho',
        'percentual_concluido' => $a->percentual_concluido ?? 0,
        'visivel_cliente' => $a->visivel_cliente ?? 0
    ];
}, $atividades)); ?>;

// Abrir modal de edição
function abrirModalEditar(id) {
    const atividade = atividadesData.find(a => a.id == id);
    if (!atividade) return;

    document.getElementById('edit_titulo').value = atividade.titulo;
    document.getElementById('edit_descricao').value = atividade.descricao;
    document.getElementById('edit_status').value = atividade.status;
    document.getElementById('edit_data').value = atividade.data_atividade;
    document.getElementById('edit_tecnico').value = atividade.tecnico_id || '';
    document.getElementById('edit_etapa').value = atividade.etapa_id || '';
    document.getElementById('edit_tipo').value = atividade.tipo;
    document.getElementById('edit_progresso').value = atividade.percentual_concluido;
    document.getElementById('edit_visivel').checked = atividade.visivel_cliente == 1;

    document.getElementById('formEditar').action = '<?php echo site_url('obras/editarAtividade/'); ?>' + id;

    document.getElementById('modalEditar').classList.add('active');
}

// Abrir modal nova atividade
function abrirModalNovaAtividade() {
    document.getElementById('modalNova').classList.add('active');
}

// Reabrir atividade (reatendimento)
function reabrirAtividade(id) {
    // Limpar formulário anterior
    const form = document.getElementById('formReabrir');
    form.action = '<?php echo site_url('obras/atualizarStatusAtividade/'); ?>' + id;

    // Adicionar input hidden para o status
    let statusInput = form.querySelector('input[name="novo_status"]');
    if (!statusInput) {
        statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'novo_status';
        form.appendChild(statusInput);
    }
    statusInput.value = 'reaberta';

    document.getElementById('modalReabrir').classList.add('active');
}

// Fechar modal
function fecharModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.classList.remove('active');
    }
}

// Switch tabs
function switchTab(tabName, btn) {
    // Remove active de todos os tabs
    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    // Filtrar por status
    const rows = document.querySelectorAll('.atividade-row');
    rows.forEach(row => {
        if (tabName === 'todas') {
            row.style.display = '';
        } else if (tabName === 'andamento') {
            const status = row.dataset.status;
            row.style.display = (status === 'iniciada' || status === 'pausada') ? '' : 'none';
        } else if (tabName === 'reabertas') {
            row.style.display = (row.dataset.status === 'reaberta') ? '' : 'none';
        } else {
            row.style.display = (row.dataset.status === tabName) ? '' : 'none';
        }
    });
}

// Filtrar atividades
function filtrarAtividades() {
    const busca = document.getElementById('filtroBusca').value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    const tecnico = document.getElementById('filtroTecnico').value;
    const etapa = document.getElementById('filtroEtapa').value;
    const tipo = document.getElementById('filtroTipo').value;

    const rows = document.querySelectorAll('.atividade-row');
    rows.forEach(row => {
        const rowBusca = row.dataset.busca.normalize('NFD').replace(/[̀-ͯ]/g, '');
        const matchBusca = !busca || rowBusca.includes(busca);
        const matchTecnico = !tecnico || row.dataset.tecnico === tecnico;
        const matchEtapa = !etapa || row.dataset.etapa === etapa;
        const matchTipo = !tipo || row.dataset.tipo === tipo;

        row.style.display = (matchBusca && matchTecnico && matchEtapa && matchTipo) ? '' : 'none';
    });
}

// Atalho de teclado ESC para fechar modais
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
    }
});

// Verificar se Boxicons carregou, senão usar Font Awesome
(function checkIcons() {
    // Mapeamento de ícones Boxicons → Font Awesome 6 Free (Solid)
    const iconMap = {
        'bx-task': 'fa-solid fa-tasks',
        'bx-building': 'fa-solid fa-building',
        'bx-user': 'fa-solid fa-user',
        'bx-calendar': 'fa-solid fa-calendar',
        'bx-arrow-back': 'fa-solid fa-arrow-left',
        'bx-list-ul': 'fa-solid fa-list-ul',
        'bx-plus': 'fa-solid fa-plus',
        'bx-layer': 'fa-solid fa-layer-group',
        'bx-grid-alt': 'fa-solid fa-th',
        'bx-play-circle': 'fa-solid fa-play-circle',
        'bx-check-circle': 'fa-solid fa-check-circle',
        'bx-refresh': 'fa-solid fa-sync-alt',
        'bx-task-x': 'fa-solid fa-clipboard-list',
        'bx-calendar-x': 'fa-solid fa-calendar-times',
        'bx-error': 'fa-solid fa-exclamation-circle',
        'bx-edit': 'fa-solid fa-edit',
        'bx-eye': 'fa-solid fa-eye',
        'bx-play': 'fa-solid fa-play',
        'bx-time': 'fa-solid fa-clock',
        'bx-save': 'fa-solid fa-save',
        'bx-x': 'fa-solid fa-times',
        'bx-info-circle': 'fa-solid fa-info-circle',
        'bx-time': 'fa-solid fa-clock'
    };

    function aplicarFallback() {
        document.querySelectorAll('i[class*="bx-"]').forEach(el => {
            const classes = Array.from(el.classList);
            const bxClass = classes.find(c => c.startsWith('bx-'));
            if (bxClass && iconMap[bxClass]) {
                el.className = iconMap[bxClass];
            }
        });
    }

    // Tentar detectar se Boxicons carregou
    const testIcon = document.querySelector('i.bx');
    if (!testIcon) return;

    // Método 1: Verificar se a fonte está aplicada via computed style
    try {
        const computedStyle = window.getComputedStyle(testIcon, '::before');
        const fontFamily = computedStyle.fontFamily || '';
        const content = computedStyle.content || '';

        if (!fontFamily.toLowerCase().includes('boxicons') || content === 'none' || content === '' || content === '""') {
            aplicarFallback();
            return;
        }
    } catch (e) {
        // Se falhar a verificação, aplicar fallback
        aplicarFallback();
        return;
    }

    // Método 2: Verificar após um timeout como backup
    setTimeout(function() {
        const test = document.querySelector('i.bx');
        if (test) {
            const rect = test.getBoundingClientRect();
            // Se o ícone tem largura quase zero, provavelmente não carregou
            if (rect.width < 5 && rect.height < 5) {
                aplicarFallback();
            }
        }
    }, 2000);
})();
</script>
