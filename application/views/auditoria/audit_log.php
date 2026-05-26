<div class="new122" style="margin-top: 0; min-height: 100vh">
<div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="fas fa-shield-alt"></i></span>
    <h5>Auditoria Estruturada</h5>
</div>

<!-- Tab navigation -->
<ul class="nav nav-tabs" style="margin-bottom: 15px;">
    <li class="<?php echo ($tab === 'logs') ? 'active' : ''; ?>">
        <a href="<?php echo site_url('auditoria?tab=logs'); ?>">Logs (Legado)</a>
    </li>
    <li class="<?php echo ($tab === 'audit') ? 'active' : ''; ?>">
        <a href="<?php echo site_url('auditoria?tab=audit'); ?>">Auditoria Estruturada</a>
    </li>
</ul>

<!-- Filters -->
<form method="get" class="form-inline" style="margin-bottom: 15px;">
    <input type="hidden" name="tab" value="audit">
    <select name="table_name" class="input-medium" style="width: 150px;">
        <option value="">Todas as tabelas</option>
        <?php foreach ($tableNames as $t): ?>
            <option value="<?php echo e($t['table_name']); ?>" <?php echo ($filters['table_name'] ?? '') === $t['table_name'] ? 'selected' : ''; ?>>
                <?php echo e($t['table_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="action" class="input-medium" style="width: 120px;">
        <option value="">Todas as acoes</option>
        <?php foreach ($actions as $a): ?>
            <option value="<?php echo e($a['action']); ?>" <?php echo ($filters['action'] ?? '') === $a['action'] ? 'selected' : ''; ?>>
                <?php echo e($a['action']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="date_from" class="input-small" value="<?php echo e($filters['date_from'] ?? ''); ?>" placeholder="De">
    <input type="date" name="date_to" class="input-small" value="<?php echo e($filters['date_to'] ?? ''); ?>" placeholder="Ate">
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filtrar</button>
    <a href="<?php echo site_url('auditoria?tab=audit'); ?>" class="btn btn-sm">Limpar</a>
</form>

<!-- Clean button -->
<a href="#modal-clean-audit" role="button" data-bs-toggle="modal" class="button btn btn-danger tip-top" style="max-width: 280px" title="Limpar Auditoria">
    <span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Remover Registros Antigos (90+ dias)</span>
</a>

<div class="widget-box" style="margin-top: 10px;">
    <div class="widget-content nopadding tab-content">
        <table id="tabela" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Usuario</th>
                    <th>Acao</th>
                    <th>Tabela</th>
                    <th>Registro</th>
                    <th>IP</th>
                    <th>Dados</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $r): ?>
                    <tr>
                        <td style="white-space: nowrap;"><?php echo date('d/m/Y H:i', strtotime($r->created_at)); ?></td>
                        <td><?php echo e($r->username ?? 'system'); ?></td>
                        <td>
                            <?php
                            $actionLabel = [
                                'INSERT' => '<span class="badge bg-success">INSERT</span>',
                                'UPDATE' => '<span class="badge bg-info">UPDATE</span>',
                                'DELETE' => '<span class="badge bg-danger">DELETE</span>',
                                'LOGIN'  => '<span class="badge bg-warning">LOGIN</span>',
                                'EXPORT' => '<span class="badge bg-dark">EXPORT</span>',
                                'ANONIMIZAR' => '<span class="badge bg-danger">ANONIMIZAR</span>',
                                'CONSENT' => '<span class="badge bg-info">CONSENT</span>',
                                'REVOKE_CONSENT' => '<span class="badge bg-warning">REVOKE</span>',
                                'BREACH_NOTIFY' => '<span class="badge bg-danger">VAZAMENTO</span>',
                            ];
                            echo $actionLabel[$r->action] ?? '<span class="label">' . e($r->action) . '</span>';
                            ?>
                        </td>
                        <td><?php echo e($r->table_name); ?></td>
                        <td><?php echo e($r->record_id ?? '-'); ?></td>
                        <td style="white-space: nowrap;"><?php echo e($r->ip_address ?? '-'); ?></td>
                        <td>
                            <?php if ($r->old_data || $r->new_data): ?>
                                <a href="#modal-diff-<?php echo $r->id; ?>" role="button" data-bs-toggle="modal" class="btn btn-sm btn-info">
                                    <i class="fas fa-code-compare"></i> Ver
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="7">Nenhum registro encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo $this->pagination->create_links(); ?>

<!-- Diff modals for each row -->
<?php foreach ($results as $r): ?>
    <?php if ($r->old_data || $r->new_data): ?>
    <div id="modal-diff-<?php echo $r->id; ?>" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-header">
            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            <h5>Auditoria #<?php echo $r->id; ?> — <?php echo e($r->action); ?> em <?php echo e($r->table_name); ?></h5>
        </div>
        <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
            <?php if ($r->old_data): ?>
                <h6 style="color: #e74c3c;">Dados Anteriores:</h6>
                <pre style="background: #fdf0f0; padding: 8px; border-radius: 4px; font-size: 0.85em; max-height: 180px; overflow: auto;"><?php
                    $old = json_decode($r->old_data, true);
                    echo e($old ? json_encode($old, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $r->old_data);
                ?></pre>
            <?php endif; ?>
            <?php if ($r->new_data): ?>
                <h6 style="color: #27ae60;">Dados Novos:</h6>
                <pre style="background: #f0fdf0; padding: 8px; border-radius: 4px; font-size: 0.85em; max-height: 180px; overflow: auto;"><?php
                    $new = json_decode($r->new_data, true);
                    echo e($new ? json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $r->new_data);
                ?></pre>
            <?php endif; ?>
            <?php if ($r->user_agent): ?>
                <small class="muted">User-Agent: <?php echo e($r->user_agent); ?></small>
            <?php endif; ?>
        </div>
        <div class="modal-footer">
            <button class="btn" data-bs-dismiss="modal">Fechar</button>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<!-- Clean modal -->
<div id="modal-clean-audit" class="modal fade" tabindex="-1" role="dialog">
    <form action="<?php echo site_url('auditoria/clean_audit') ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            <h5>Limpeza de Registros de Auditoria</h5>
        </div>
        <div class="modal-body">
            <h5 style="text-align: center">Remover registros de auditoria com mais de quantos dias?</h5>
            <div style="text-align: center; margin-top: 10px;">
                <input type="number" name="days" value="90" min="30" max="365" style="width: 80px; text-align: center;"> dias
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-danger">Excluir</button>
        </div>
    </form>
</div>
</div>