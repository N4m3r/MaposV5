<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.relatorio-container { padding: 20px; max-width: 1200px; margin: 0 auto; }

/* Header */
.relatorio-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.relatorio-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}
.relatorio-title h1 {
    margin: 0 0 5px 0;
    font-size: 28px;
    font-weight: 700;
}
.relatorio-subtitle {
    opacity: 0.95;
    font-size: 16px;
}
.relatorio-actions {
    display: flex;
    gap: 10px;
}
.relatorio-btn {
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}
.relatorio-btn:hover { transform: translateY(-2px); }
.relatorio-btn-secondary {
    background: rgba(255,255,255,0.2);
    color: white;
}
.relatorio-btn-secondary:hover { background: rgba(255,255,255,0.3); }
.relatorio-btn-primary {
    background: white;
    color: #667eea;
}
.relatorio-btn-primary:hover { background: #f8f9fa; }

/* Cards */
.relatorio-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
}
.relatorio-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.relatorio-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}
.relatorio-card-title i { color: #667eea; font-size: 22px; }

/* Info Grid */
.relatorio-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.relatorio-info-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
}
.relatorio-info-label {
    font-size: 12px;
    color: #888;
    text-transform: uppercase;
    margin-bottom: 5px;
}
.relatorio-info-value {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

/* Filter Form */
.relatorio-filter {
    display: flex;
    gap: 15px;
    align-items: flex-end;
    flex-wrap: wrap;
}
.relatorio-filter-group { flex: 1; min-width: 200px; }
.relatorio-filter-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}
.relatorio-filter-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    font-size: 14px;
    box-sizing: border-box;
}
.relatorio-filter-input:focus {
    border-color: #667eea;
    outline: none;
}
.relatorio-filter-btn {
    padding: 12px 25px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Table */
.relatorio-table {
    width: 100%;
    border-collapse: collapse;
}
.relatorio-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 14px;
    border-bottom: 2px solid #e8e8e8;
}
.relatorio-table td {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    color: #666;
    font-size: 14px;
}
.relatorio-table tr:hover { background: #f8f9fa; }

/* Status Badges */
.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}
.status-badge.agendada { background: #ecf0f1; color: #7f8c8d; }
.status-badge.iniciada { background: #3498db; color: white; }
.status-badge.pausada { background: #f39c12; color: white; }
.status-badge.concluida { background: #27ae60; color: white; }
.status-badge.cancelada { background: #e74c3c; color: white; }

/* Progress Bar */
.progress-bar-container {
    width: 100%;
    height: 10px;
    background: #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
}
.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 5px;
    transition: width 0.3s ease;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}
.empty-state-icon {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}
.empty-state-text {
    color: #888;
    font-size: 16px;
}

/* Print Styles */
@media print {
    .relatorio-btn { display: none; }
    .relatorio-filter { display: none; }
    .relatorio-card { box-shadow: none; border: 1px solid #ddd; }
}

/* Responsive */
@media (max-width: 768px) {
    .relatorio-info-grid { grid-template-columns: 1fr; }
    .relatorio-filter { flex-direction: column; }
    .relatorio-filter-group { width: 100%; }
}
</style>

<div class="relatorio-container">
    <!-- Header -->
    <div class="relatorio-header">
        <div class="relatorio-header-content">
            <div class="relatorio-title">
                <h1><i class="icon-file-alt"></i> Relatório Diário de Obra (RDO)</h1>
                <div class="relatorio-subtitle"><?php echo $obra->nome; ?></div>
            </div>
            <div class="relatorio-actions">
                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="relatorio-btn relatorio-btn-secondary">
                    <i class="icon-arrow-left"></i> Voltar
                </a>
                <button onclick="window.print()" class="relatorio-btn relatorio-btn-primary">
                    <i class="icon-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>

    <!-- Informações da Obra -->
    <div class="relatorio-card">
        <div class="relatorio-card-header">
            <div class="relatorio-card-title">
                <i class="icon-info-sign"></i> Informações da Obra
            </div>
        </div>

        <div class="relatorio-info-grid">
            <div class="relatorio-info-item">
                <div class="relatorio-info-label">Obra</div>
                <div class="relatorio-info-value"><?php echo $obra->nome; ?></div>
            </div>

            <div class="relatorio-info-item">
                <div class="relatorio-info-label">Cliente</div>
                <div class="relatorio-info-value"><?php echo $obra->cliente_nome ?? 'N/A'; ?></div>
            </div>

            <div class="relatorio-info-item">
                <div class="relatorio-info-label">Endereço</div>
                <div class="relatorio-info-value"><?php echo $obra->endereco ?? 'N/A'; ?></div>
            </div>

            <div class="relatorio-info-item">
                <div class="relatorio-info-label">Data do Relatório</div>
                <div class="relatorio-info-value"><?php echo date('d/m/Y', strtotime($data_relatorio)); ?></div>
            </div>
        </div>
    </div>

    <!-- Filtro de Data -->
    <div class="relatorio-card">
        <div class="relatorio-card-header">
            <div class="relatorio-card-title">
                <i class="icon-calendar"></i> Selecionar Data
            </div>
        </div>

        <form method="get" action="<?php echo site_url('obras/relatorioDiario/' . $obra->id); ?>" class="relatorio-filter">
            <div class="relatorio-filter-group">
                <label class="relatorio-filter-label">Data</label>
                <input type="date" name="data" value="<?php echo $data_relatorio; ?>" class="relatorio-filter-input">
            </div>
            <button type="submit" class="relatorio-filter-btn">
                <i class="icon-search"></i> Filtrar
            </button>
        </form>
    </div>

    <!-- Atividades do Dia -->
    <div class="relatorio-card">
        <div class="relatorio-card-header">
            <div class="relatorio-card-title">
                <i class="icon-tasks"></i> Atividades Realizadas
            </div>
            <div style="font-size: 14px; color: #888;">
                <?php echo count($atividades); ?> atividade(s)
            </div>
        </div>

        <?php if (!empty($atividades)): ?>
        <div style="overflow-x: auto;">
            <table class="relatorio-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Etapa</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Responsável</th>
                        <th>Horas</th>
                        <th>Progresso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($atividades as $atividade): ?>
                    <tr>
                        <td><?php echo $atividade->id; ?></td>
                        <td><?php echo $atividade->etapa_nome ?? 'N/A'; ?></td>
                        <td><?php echo nl2br(htmlspecialchars($atividade->descricao ?? 'N/A')); ?></td>
                        <td>
                            <span class="status-badge <?php echo $atividade->status; ?>">
                                <?php
                                $statusLabels = [
                                    'agendada' => 'Agendada',
                                    'iniciada' => 'Em Andamento',
                                    'pausada' => 'Pausada',
                                    'concluida' => 'Concluída',
                                    'cancelada' => 'Cancelada'
                                ];
                                echo $statusLabels[$atividade->status] ?? $atividade->status;
                                ?>
                            </span>
                        </td>
                        <td><?php echo $atividade->responsavel_nome ?? 'N/A'; ?></td>
                        <td><?php echo $atividade->horas_trabalhadas ?? 0; ?>h</td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: <?php echo $atividade->percentual_concluido ?? 0; ?>%"></div>
                            </div>
                            <div style="text-align: center; margin-top: 5px; font-size: 12px; color: #666;">
                                <?php echo $atividade->percentual_concluido ?? 0; ?>%
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="icon-tasks"></i></div>
            <div class="empty-state-text">Nenhuma atividade registrada nesta data.</div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Resumo -->
    <?php if (!empty($atividades)): ?
    <div class="relatorio-card">
        <div class="relatorio-card-header">
            <div class="relatorio-card-title">
                <i class="icon-chart-bar"></i> Resumo do Dia
            </div>
        </div>

        <div class="relatorio-info-grid">
            <div class="relatorio-info-item">
                <div class="relatorio-info-label">Total de Atividades</div>
                <div class="relatorio-info-value"><?php echo count($atividades); ?></div>
            </div>

            <div class="relatorio-info-item">
                <div class="relatorio-info-label">Horas Trabalhadas</div>
                <div class="relatorio-info-value">
                    <?php
                    $totalHoras = array_sum(array_column($atividades, 'horas_trabalhadas'));
                    echo $totalHoras ?? 0;
                    ?>h
                </div>
            </div>

            <div class="relatorio-info-item">
                <div class="relatorio-info-label">Atividades Concluídas</div>
                <div class="relatorio-info-value">
                    <?php
                    $concluidas = count(array_filter($atividades, function($a) { return $a->status == 'concluida'; }));
                    echo $concluidas;
                    ?>
                </div>
            </div>

            <div class="relatorio-info-item">
                <div class="relatorio-info-label">Progresso Médio</div>
                <div class="relatorio-info-value">
                    <?php
                    $progressoMedio = count($atividades) > 0
                        ? round(array_sum(array_column($atividades, 'percentual_concluido')) / count($atividades))
                        : 0;
                    echo $progressoMedio;
                    ?>%
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
