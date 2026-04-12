<?php
/**
 * Kanban Board View
 * Visualização em Kanban das Ordens de Serviço
 */

$ci = &get_instance();
?>

<link rel="stylesheet" href="<?= base_url('assets/css/kanban.css') ?>">

<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li class="active">Kanban - Ordens de Serviço</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-th"></i></span>
                <h5>Kanban Board - Total: <?= $total_os ?> OS</h5>
                <div class="buttons">
                    <a href="<?= base_url('kanban/print') ?>" target="_blank" class="btn btn-mini">
                        <i class="icon-print"></i> Imprimir
                    </a>
                    <a href="<?= base_url('os') ?>" class="btn btn-mini btn-info">
                        <i class="icon-list"></i> Lista
                    </a>
                </div>
            </div>

            <div class="widget-content">
                <!-- Filtros -->
                <div class="kanban-filters mb-3">
                    <form method="GET" class="form-inline">
                        <select name="tecnico" class="span2">
                            <option value="">Todos os Técnicos</option>
                            <?php foreach ($tecnicos as $t): ?>
                                <option value="<?= $t->idUsuarios ?>" <?= $filters['tecnico'] == $t->idUsuarios ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t->nome) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="date" name="data_inicio" class="span2" placeholder="Data Início"
                               value="<?= $filters['data_inicio'] ?>">

                        <input type="date" name="data_fim" class="span2" placeholder="Data Fim"
                               value="<?= $filters['data_fim'] ?>">

                        <button type="submit" class="btn btn-primary">
                            <i class="icon-search"></i> Filtrar
                        </button>

                        <a href="<?= base_url('kanban') ?>" class="btn">Limpar</a>
                    </form>
                </div>

                <!-- Kanban Board -->
                <div class="kanban-board" id="kanban-board">
                    <?php foreach ($boards as $status => $board): ?>
                        <div class="kanban-column" data-status="<?= $status ?>">
                            <div class="kanban-header bg-<?= $board['color'] ?>">
                                <i class="icon <?= $board['icon'] ?>"></i>
                                <span class="kanban-title"><?= htmlspecialchars($board['title']) ?></span>
                                <span class="kanban-count badge"><?= $board['count'] ?></span>
                            </div>

                            <div class="kanban-items" id="column-<?= $status ?>"
                                 data-status="<?= $status ?>">
                                <?php foreach ($board['items'] as $item): ?>
                                    <div class="kanban-card" draggable="true"
                                         data-id="<?= $item->idOs ?>"
                                         data-status="<?= $status ?>">
                                        <div class="kanban-card-header">
                                            <span class="kanban-card-id">#<?= $item->idOs ?></span>
                                            <span class="badge badge-<?= $item->corPrioridade ?>">
                                                <?= $item->prioridade ?: 'Normal' ?>
                                            </span>
                                        </div>

                                        <div class="kanban-card-body">
                                            <strong><?= htmlspecialchars($item->nomeCliente) ?></strong>
                                            <p class="kanban-card-desc">
                                                <?= htmlspecialchars(substr($item->descricaoProduto ?? '', 0, 100)) ?>...
                                            </p>

                                            <?php if ($item->telefone): ?>
                                                <small class="text-muted">
                                                    <i class="icon-phone"></i> <?= $item->telefone ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>

                                        <div class="kanban-card-footer">
                                            <small class="text-muted">
                                                <i class="icon-calendar"></i>
                                                <?= date('d/m', strtotime($item->dataInicial)) ?>
                                            </small>

                                            <div class="kanban-card-actions">
                                                <a href="<?= base_url("os/visualizar/{$item->idOs}") ?>"
                                                   class="btn btn-mini btn-info" title="Visualizar">
                                                    <i class="icon-eye-open"></i>
                                                </a>

                                                <a href="<?= base_url("os/editar/{$item->idOs}") ?>"
                                                   class="btn btn-mini btn-primary" title="Editar">
                                                    <i class="icon-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/kanban.js') ?>"></script>
