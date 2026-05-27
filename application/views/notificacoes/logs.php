<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class='bx bx-history'></i> Historico de Notificacoes</h5>
            </div>
            <div class="card-body">
                <?php if (isset($estatisticas)): ?>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3><?= $estatisticas->total ?? 0 ?></h3>
                                <small>Total (30d)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3><?= $estatisticas->enviados ?? 0 ?></h3>
                                <small>Enviados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3><?= $estatisticas->falhas ?? 0 ?></h3>
                                <small>Falhas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3><?= $estatisticas->sucesso_percentual ?? 0 ?>%</h3>
                                <small>Taxa de Sucesso</small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <form method="get" class="row g-2 mb-3">
                    <div class="col-auto">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Todos status</option>
                            <option value="enviado" <?= ($filtros['status'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                            <option value="falha" <?= ($filtros['status'] ?? '') === 'falha' ? 'selected' : '' ?>>Falha</option>
                            <option value="pendente" <?= ($filtros['status'] ?? '') === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary"><i class='bx bx-search'></i> Filtrar</button>
                    </div>
                </form>

                <?php if (empty($logs)): ?>
                    <div class="text-center p-4">
                        <i class='bx bx-history' style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="mt-2 text-muted">Nenhum log encontrado.</p>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Template</th>
                                <th>Telefone</th>
                                <th>Canal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($logs as $l): ?>
                            <tr>
                                <td><?= $l->created_at ?? '' ?></td>
                                <td><code><?= $l->template_chave ?? '' ?></code></td>
                                <td><?= $l->telefone ?? '' ?></td>
                                <td><?= strtoupper($l->canal ?? 'whatsapp') ?></td>
                                <td>
                                    <?php
                                    $statusClass = ['enviado' => 'success', 'falha' => 'danger', 'pendente' => 'warning', 'lido' => 'info'];
                                    $cls = $statusClass[$l->status ?? ''] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $cls ?>"><?= $l->status ?? 'N/A' ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?= $this->pagination->create_links() ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>