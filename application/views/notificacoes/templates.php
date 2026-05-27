<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class='bx bx-message-square-dots'></i> Templates de Notificacoes</h5>
                <a href="<?= site_url('notificacoes/adicionar_template') ?>" class="btn btn-success btn-sm float-end"><i class='bx bx-plus'></i> Novo Template</a>
            </div>
            <div class="card-body">
                <?php if (empty($templates)): ?>
                    <div class="text-center p-4">
                        <i class='bx bx-message-square-dots' style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="mt-2 text-muted">Nenhum template cadastrado.</p>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Chave</th>
                                <th>Categoria</th>
                                <th>Canal</th>
                                <th>Status</th>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($templates as $t): ?>
                            <tr>
                                <td><?= e($t->nome) ?></td>
                                <td><code><?= e($t->chave) ?></code></td>
                                <td><?= $categorias[$t->categoria] ?? $t->categoria ?></td>
                                <td><?= strtoupper($t->canal ?? 'whatsapp') ?></td>
                                <td>
                                    <a href="<?= site_url('notificacoes/templates/toggle/' . $t->id) ?>" class="badge bg-<?= $t->ativo ? 'success' : 'secondary' ?>">
                                        <?= $t->ativo ? 'Ativo' : 'Inativo' ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= site_url('notificacoes/templates/editar/' . $t->id) ?>" class="btn btn-sm btn-warning" title="Editar"><i class='bx bx-edit'></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>