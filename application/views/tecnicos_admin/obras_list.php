<!-- Gestão de Obras - Versão Moderna -->
<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-building"></i>
        </span>
        <h5>Gestão de Obras</h5>
        <div class="buttons">
            <a href="#modal-nova-obra" data-toggle="modal" class="button btn btn-mini btn-success">
                <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                <span class="button__text2">Nova Obra</span>
            </a>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row-fluid" style="margin: 15px 0 25px;">
        <div class="span2">
            <div class="status-card total">
                <span class="status-number"><?= count($obras) ?></span>
                <span class="status-label">Total</span>
            </div>
        </div>
        <div class="span2">
            <div class="status-card planejada">
                <span class="status-number">-</span>
                <span class="status-label">Planejadas</span>
            </div>
        </div>
        <div class="span2">
            <div class="status-card andamento">
                <span class="status-number">-</span>
                <span class="status-label">Em Andamento</span>
            </div>
        </div>
        <div class="span2">
            <div class="status-card paralisada">
                <span class="status-number">-</span>
                <span class="status-label">Paralisadas</span>
            </div>
        </div>
        <div class="span2">
            <div class="status-card concluida">
                <span class="status-number">-</span>
                <span class="status-label">Concluídas</span>
            </div>
        </div>
        <div class="span2">
            <div class="status-card progresso">
                <span class="status-number">-</span>
                <span class="status-label">Progresso Médio</span>
            </div>
        </div>
    </div>

    <!-- Lista de Obras -->
    <?php if (!empty($obras)): ?>
        <div class="row-fluid">
            <?php foreach ($obras as $obra):
                $statusConfig = [
                    'planejamento' => ['label' => 'Planejada', 'class' => 'planejada', 'icon' => 'bx-calendar'],
                    'em_andamento' => ['label' => 'Em Andamento', 'class' => 'andamento', 'icon' => 'bx-rocket'],
                    'paralisada' => ['label' => 'Paralisada', 'class' => 'paralisada', 'icon' => 'bx-pause-circle'],
                    'concluida' => ['label' => 'Concluída', 'class' => 'concluida', 'icon' => 'bx-check-circle'],
                ][$obra->status ?? 'planejamento'] ?? $statusConfig['planejamento'];

                $progresso = $obra->percentual_concluido ?? $obra->progresso ?? 0;
                $tipoObra = $obra->tipo_obra ?? 'Outro';

                // Cores por tipo
                $tipoColors = [
                    'Condominio' => '#667eea',
                    'Comercio' => '#11998e',
                    'Residencia' => '#f093fb',
                    'Industrial' => '#ff6b6b',
                    'Publica' => '#4facfe',
                ];
                $tipoColor = $tipoColors[$tipoObra] ?? '#888';
            ?>
                <div class="span4" style="margin-bottom: 20px;">
                    <div class="obra-card">
                        <div class="obra-header">
                            <div class="obra-icon" style="background: <?= $tipoColor ?>;">
                                <i class="bx bx-building"></i>
                            </div>
                            <div class="obra-title">
                                <h6><?= htmlspecialchars($obra->nome ?? 'Sem nome', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></h6>
                                <span class="obra-code">
                                    <i class="bx bx-hash"></i> <?= $obra->codigo ?? '#' . $obra->id ?>
                                </span>
                            </div>
                            <div class="obra-status-badge <?= $statusConfig['class'] ?>">
                                <i class="bx <?= $statusConfig['icon'] ?>"></i>
                                <?= $statusConfig['label'] ?>
                            </div>
                        </div>

                        <div class="obra-body">
                            <div class="obra-info">
                                <div class="info-row">
                                    <span class="info-label"><i class="bx bx-user"></i> Cliente</span>
                                    <span class="info-value"><?= htmlspecialchars($obra->cliente_nome ?? '-', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bx bx-map-pin"></i> Endereço</span>
                                    <span class="info-value"><?= htmlspecialchars($obra->endereco ?? '-', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bx bx-hard-hat"></i> Responsável</span>
                                    <span class="info-value"><?= htmlspecialchars($obra->responsavel_nome ?? 'Não atribuído', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bx bx-calendar"></i> Previsão</span>
                                    <span class="info-value">
                                        <?= isset($obra->data_previsao_fim) ? date('d/m/Y', strtotime($obra->data_previsao_fim)) : '-' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Progresso -->
                            <div class="obra-progress">
                                <div class="progress-header">
                                    <span>Progresso</span>
                                    <span class="progress-percent"><?= $progresso ?>%</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: <?= $progresso ?>%; background: <?= $tipoColor ?>;">
                                        </div>
                                </div>
                            </div>

                            <!-- Métricas -->
                            <div class="obra-metrics">
                                <div class="metric">
                                    <i class="bx bx-task"></i>
                                    <span><?= $obra->total_os ?? 0 ?> OS</span>
                                </div>
                                <div class="metric">
                                    <i class="bx bx-group"></i>
                                    <span><?= $obra->total_equipe ?? 0 ?> Técnicos</span>
                                </div>
                                <div class="metric">
                                    <i class="bx bx-layer"></i>
                                    <span><?= $obra->total_etapas ?? 0 ?> Etapas</span>
                                </div>
                            </div>
                        </div>

                        <div class="obra-footer">
                            <div class="obra-type" style="color: <?= $tipoColor ?>;">
                                <i class="bx bx-tag"></i>
                                <?= $tipoObra ?>
                            </div>
                            <div class="obra-actions">
                                <a href="<?= site_url('tecnicos_admin/ver_obra/' . $obra->id) ?>"
                                   class="btn-action btn-view" title="Ver detalhes">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="<?= site_url('tecnicos_admin/editar_obra/' . $obra->id) ?>"
                                   class="btn-action btn-edit" title="Editar">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="<?= site_url('tecnicos_admin/excluir_obra/' . $obra->id) ?>"
                                   class="btn-action btn-delete" title="Excluir"
                                   onclick="return confirm('Tem certeza que deseja excluir esta obra?')">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bx bx-building-house"></i>
            </div>
            <h3>Nenhuma obra cadastrada</h3>
            <p>O módulo de obras permite gerenciar projetos maiores que envolvem múltiplas OS e equipes.</p>

            <div class="features-grid">
                <div class="feature">
                    <i class="bx bx-folder-open"></i>
                    <span>Agrupar OS relacionadas</span>
                </div>
                <div class="feature">
                    <i class="bx bx-line-chart"></i>
                    <span>Acompanhar progresso</span>
                </div>
                <div class="feature">
                    <i class="bx bx-package"></i>
                    <span>Controlar materiais</span>
                </div>
                <div class="feature">
                    <i class="bx bx-group"></i>
                    <span>Gerenciar equipes</span>
                </div>
            </div>

            <a href="#modal-nova-obra" data-toggle="modal" class="button btn btn-success btn-large">
                <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                <span class="button__text2">Criar Primeira Obra</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Nova Obra -->
<div id="modal-nova-obra" class="modal hide fade" tabindex="-1" role="dialog">
    <form action="<?= site_url('tecnicos_admin/adicionar_obra') ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h5><i class="bx bx-plus-circle"></i> Nova Obra</h5>
        </div>

        <div class="modal-body">
            <div class="row-fluid">
                <div class="span8">
                    <div class="control-group">
                        <label class="control-label">Nome da Obra *</label>
                        <div class="controls">
                            <input type="text" name="nome" class="span12" placeholder="Ex: Instalação CFTV Condomínio XYZ"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="span4">
                    <div class="control-group">
                        <label class="control-label">Código</label>
                        <div class="controls">
                            <input type="text" name="codigo" class="span12" placeholder="OB-2024-001">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label">Cliente *</label>
                        <div class="controls">
                            <select name="cliente_id" class="span12" required>
                                <option value="">Selecione...</option>
                                <?php
                                // Buscar clientes ativos
                                $clientes_query = $this->db->order_by('nomeCliente', 'ASC')->get('clientes');
                                $clientes = $clientes_query ? $clientes_query->result() : [];
                                if (!empty($clientes)):
                                    foreach ($clientes as $cliente): ?>
                                        <option value="<?= $cliente->idClientes ?>">
                                            <?= htmlspecialchars($cliente->nomeCliente, ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label">Tipo de Obra</label>
                        <div class="controls">
                            <select name="tipo_obra" class="span12">
                                <option value="Condominio">Condomínio</option>
                                <option value="Comercio">Comércio</option>
                                <option value="Residencia">Residência</option>
                                <option value="Industrial">Industrial</option>
                                <option value="Publica">Pública</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Endereço</label>
                <div class="controls">
                    <input type="text" name="endereco" class="span12" placeholder="Rua, número, bairro, cidade">
                </div>
            </div>

            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label">Data de Início</label>
                        <div class="controls">
                            <input type="date" name="data_inicio" class="span12">
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label">Previsão de Término</label>
                        <div class="controls">
                            <input type="date" name="data_previsao_fim" class="span12">
                        </div>
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Descrição / Observações</label>
                <div class="controls">
                    <textarea name="descricao" class="span12" rows="3"
                              placeholder="Descreva os detalhes da obra..."></textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer" style="display: flex; justify-content: center; gap: 10px;">
            <button type="button" class="button btn btn-warning" data-dismiss="modal">
                <span class="button__icon"><i class="bx bx-x"></i></span>
                <span class="button__text2">Cancelar</span>
            </button>
            <button type="submit" class="button btn btn-success">
                <span class="button__icon"><i class="bx bx-save"></i></span>
                <span class="button__text2">Criar Obra</span>
            </button>
        </div>
    </form>
</div>

<style>
/* Status Cards */
.status-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.status-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.status-card .status-number {
    display: block;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

.status-card .status-label {
    font-size: 12px;
    color: #666;
}

.status-card.total { border-top: 4px solid #667eea; }
.status-card.total .status-number { color: #667eea; }

.status-card.planejada { border-top: 4px solid #9e9e9e; }
.status-card.planejada .status-number { color: #9e9e9e; }

.status-card.andamento { border-top: 4px solid #2196f3; }
.status-card.andamento .status-number { color: #2196f3; }

.status-card.paralisada { border-top: 4px solid #ff9800; }
.status-card.paralisada .status-number { color: #ff9800; }

.status-card.concluida { border-top: 4px solid #4caf50; }
.status-card.concluida .status-number { color: #4caf50; }

.status-card.progresso { border-top: 4px solid #9c27b0; }
.status-card.progresso .status-number { color: #9c27b0; }

/* Obra Cards */
.obra-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.obra-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.obra-header {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.obra-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
}

.obra-title {
    flex: 1;
    min-width: 0;
}

.obra-title h6 {
    margin: 0 0 5px 0;
    font-size: 15px;
    font-weight: 600;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.obra-code {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 4px;
}

.obra-status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.obra-status-badge.planejada { background: #f5f5f5; color: #666; }
.obra-status-badge.andamento { background: #e3f2fd; color: #1976d2; }
.obra-status-badge.paralisada { background: #fff3e0; color: #f57c00; }
.obra-status-badge.concluida { background: #e8f5e9; color: #388e3c; }

.obra-body {
    padding: 20px;
    flex: 1;
}

.obra-info {
    margin-bottom: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f5f5f5;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 5px;
}

.info-label i {
    font-size: 14px;
}

.info-value {
    font-size: 13px;
    color: #333;
    font-weight: 500;
    max-width: 60%;
    text-align: right;
}

.obra-progress {
    margin-bottom: 20px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 12px;
    color: #666;
}

.progress-percent {
    font-weight: 700;
    color: #333;
}

.progress-bar-container {
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    border-radius: 4px;
    transition: width 0.5s ease;
}

.obra-metrics {
    display: flex;
    justify-content: space-around;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.metric {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: #666;
}

.metric i {
    font-size: 20px;
    color: #667eea;
}

.obra-footer {
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.obra-type {
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.obra-actions {
    display: flex;
    gap: 8px;
}

.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-view { background: #e3f2fd; color: #1976d2; }
.btn-view:hover { background: #1976d2; color: white; text-decoration: none; }

.btn-edit { background: #fff3e0; color: #f57c00; }
.btn-edit:hover { background: #f57c00; color: white; text-decoration: none; }

.btn-delete { background: #ffebee; color: #c62828; }
.btn-delete:hover { background: #c62828; color: white; text-decoration: none; }

/* Empty State */
.empty-state {
    padding: 60px;
    text-align: center;
}

.empty-icon {
    font-size: 100px;
    color: #e0e0e0;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #666;
    font-weight: 400;
    margin-bottom: 10px;
}

.empty-state p {
    color: #999;
    margin-bottom: 30px;
}

.features-grid {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.feature {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    color: #666;
}

.feature i {
    font-size: 28px;
    color: #667eea;
}

/* Animations */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.obra-card {
    animation: fadeInUp 0.4s ease forwards;
}

.span4:nth-child(1) .obra-card { animation-delay: 0s; }
.span4:nth-child(2) .obra-card { animation-delay: 0.1s; }
.span4:nth-child(3) .obra-card { animation-delay: 0.2s; }
</style>

<script>
$(document).ready(function() {
    // Calcular estatísticas
    var counts = { planejada: 0, andamento: 0, paralisada: 0, concluida: 0, totalProgresso: 0, totalObras: 0 };

    $('.obra-card').each(function() {
        var status = $(this).find('.obra-status-badge').text().trim().toLowerCase();
        var progresso = parseInt($(this).find('.progress-percent').text()) || 0;

        counts.totalProgresso += progresso;
        counts.totalObras++;

        if (status.includes('planejada')) counts.planejada++;
        else if (status.includes('andamento')) counts.andamento++;
        else if (status.includes('paralisada')) counts.paralisada++;
        else if (status.includes('concluída') || status.includes('concluida')) counts.concluida++;
    });

    // Atualizar cards de status
    $('.status-card.planejada .status-number').text(counts.planejada);
    $('.status-card.andamento .status-number').text(counts.andamento);
    $('.status-card.paralisada .status-number').text(counts.paralisada);
    $('.status-card.concluida .status-number').text(counts.concluida);
    $('.status-card.progresso .status-number').text(counts.totalObras > 0 ? Math.round(counts.totalProgresso / counts.totalObras) + '%' : '0%');
});
</script>
