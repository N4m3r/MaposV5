<?php
/**
 * Log de Envios de Email
 */
?>

<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li><a href="<?= base_url('emails/dashboard') ?>">Emails</a><span class="divider">/</span></li>
            <li class="active">Log de Envios</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php if ($this->session->flashdata('success')) { ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">x</button>
                <?= $this->session->flashdata('success') ?>
            </div>
        <?php } ?>
        <?php if ($this->session->flashdata('error')) { ?>
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">x</button>
                <?= $this->session->flashdata('error') ?>
            </div>
        <?php } ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-list-alt"></i></span>
                <h5>Log de Envios</h5>
                <div class="buttons">
                    <a href="<?= base_url('email/logs') ?>" class="btn btn-mini <?= empty($status_filter) ? 'btn-inverse' : '' ?>">Todos</a>
                    <a href="<?= base_url('email/logs?status=pending') ?>" class="btn btn-mini <?= $status_filter === 'pending' ? 'btn-warning' : '' ?>">Pendentes</a>
                    <a href="<?= base_url('email/logs?status=sent') ?>" class="btn btn-mini <?= $status_filter === 'sent' ? 'btn-success' : '' ?>">Enviados</a>
                    <a href="<?= base_url('email/logs?status=failed') ?>" class="btn btn-mini <?= $status_filter === 'failed' ? 'btn-danger' : '' ?>">Falhas</a>
                    <a href="<?= base_url('email/logs?status=scheduled') ?>" class="btn btn-mini <?= $status_filter === 'scheduled' ? 'btn-info' : '' ?>">Agendados</a>
                </div>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Para</th>
                            <th>Assunto</th>
                            <th width="100">Status</th>
                            <th width="120">Data</th>
                            <th width="80">Tentativas</th>
                            <th>Erro</th>
                            <th width="90">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= $log->id ?></td>
                                    <td>
                                        <?= htmlspecialchars($log->to_email) ?>
                                        <?php if (!empty($log->to_name)): ?>
                                            <br><small class="muted"><?= htmlspecialchars($log->to_name) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($log->subject) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'pending' => 'label label-warning',
                                            'processing' => 'label label-info',
                                            'sent' => 'label label-success',
                                            'failed' => 'label label-important',
                                            'cancelled' => 'label',
                                            'scheduled' => 'label label-info',
                                        ][$log->status] ?? 'label';
                                        ?>
                                        <span class="<?= $statusClass ?>"><?= ucfirst($log->status) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($log->created_at)) ?></td>
                                    <td class="center"><?= $log->attempts ?? 0 ?></td>
                                    <td>
                                        <?php if (!empty($log->error_message)): ?>
                                            <small class="text-error"><?= htmlspecialchars($log->error_message) ?></small>
                                        <?php else: ?>
                                            <span class="muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="center">
                                        <?php if ($log->status === 'failed'): ?>
                                            <a href="<?= base_url('email/reenviar/' . $log->id) ?>" class="btn btn-mini btn-success" title="Reenviar">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="center">
                                    <div class="alert alert-info" style="margin: 10px;">
                                        Nenhum registro encontrado.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination" style="margin: 15px;">
                        <ul>
                            <?php if ($page > 1): ?>
                                <li><a href="<?= base_url('email/logs?page=' . ($page - 1) . (!empty($status_filter) ? '&status=' . $status_filter : '')) ?>">Anterior</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="<?= $i === $page ? 'active' : '' ?>">
                                    <a href="<?= base_url('email/logs?page=' . $i . (!empty($status_filter) ? '&status=' . $status_filter : '')) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $total_pages): ?>
                                <li><a href="<?= base_url('email/logs?page=' . ($page + 1) . (!empty($status_filter) ? '&status=' . $status_filter : '')) ?>">Proxima</a></li>
                            <?php endif; ?>
                        </ul>
                        <span class="muted" style="margin-left: 15px;">Total: <?= $total ?> registros</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span12" style="text-align: center; margin-top: 10px; margin-bottom: 40px;">
        <a href="<?= base_url('emails/dashboard') ?>" class="btn btn-large">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>
    </div>
</div>
