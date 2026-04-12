<?php
/**
 * Componente: Data Table
 * Props: columns, data, actions, emptyMessage, showActions
 */

$columns = $columns ?? [];
$data = $data ?? [];
$actions = $actions ?? true;
$emptyMessage = $emptyMessage ?? 'Nenhum registro encontrado.';
$showActions = $showActions ?? true;

// Colunas padrão de ações
if ($showActions) {
    $columns[] = ['key' => 'actions', 'label' => 'Ações', 'class' => 'text-center'];
}
?>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered data-table">
        <thead>
            <tr>
                <?php foreach ($columns as $column): ?>
                    <?php if (is_array($column)): ?>
                        <th class="<?= $column['class'] ?? '' ?>"><?= htmlspecialchars($column['label']) ?></th>
                    <?php else: ?>
                        <th><?= htmlspecialchars($column) ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="<?= count($columns) ?>" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        <?= htmlspecialchars($emptyMessage) ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <?php
                            if (is_array($column)) {
                                $key = $column['key'];
                                $class = $column['class'] ?? '';
                            } else {
                                $key = $column;
                                $class = '';
                            }

                            // Coluna de ações
                            if ($key === 'actions' && $showActions && isset($row['id'])):
                            ?>
                                <td class="<?= $class ?>">
                                    <div class="btn-group">
                                        <a href="<?= base_url($row['view_url'] ?? '') ?>" class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url($row['edit_url'] ?? '') ?>" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (isset($row['delete_url'])): ?>
                                        <a href="<?= base_url($row['delete_url']) ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Deseja realmente excluir?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            <?php else: ?>
                                <td class="<?= $class ?>">
                                    <?php if (isset($row[$key])): ?>
                                        <?= is_string($row[$key]) ? htmlspecialchars($row[$key]) : $row[$key] ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
