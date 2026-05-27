<div class="new122" style="margin-top: 0; min-height: 100vh">
<div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
    <h5>Notificacoes de Vazamento (LGPD Art. 48)</h5>
</div>

<!-- Tab navigation -->
<style>
    .os-tabs { display: flex; gap: 6px; padding: 0 0 15px 0; flex-wrap: wrap; }
    .os-tab-btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; border: 1px solid var(--dark-2, #272835); border-radius: 6px; background: var(--dark-2, #272835); color: var(--dark-cinz, #8788a4); font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .os-tab-btn:hover { background: #2d2e3a; color: var(--branco, #caced8); text-decoration: none; }
    .os-tab-btn.active { background: #2d335b; color: #fff; font-weight: bold; border-color: #4a4d7a; }
    .os-tab-btn i { font-size: 15px; }
    @media (max-width: 767px) {
        .os-tabs { flex-direction: column; }
        .os-tab-btn { width: 100%; justify-content: center; }
    }
</style>
<div class="os-tabs" style="margin-bottom: 15px;">
    <a class="os-tab-btn" href="<?php echo site_url('auditoria?tab=logs'); ?>"><i class="bx bx-list-ul"></i> Logs (Legado)</a>
    <a class="os-tab-btn" href="<?php echo site_url('auditoria?tab=audit'); ?>"><i class="bx bx-shield-alt"></i> Auditoria Estruturada</a>
    <a class="os-tab-btn active" href="<?php echo site_url('auditoria/vazamentos'); ?>"><i class="bx bx-error-circle"></i> Vazamentos</a>
</div>

<a href="#modal-novo-vazamento" role="button" data-bs-toggle="modal" class="button btn btn-warning tip-top" style="max-width: 300px" title="Registrar Vazamento">
    <span class="button__icon"><i class='fas fa-exclamation-triangle'></i></span>
    <span class="button__text2">Registrar Novo Vazamento</span>
</a>

<div class="widget-box" style="margin-top: 10px;">
    <div class="widget-content nopadding tab-content">
        <table id="tabela" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titulo</th>
                    <th>Tipo de Dado</th>
                    <th>Data Ocorrencia</th>
                    <th>Status</th>
                    <th>ANPD</th>
                    <th>Titulares</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($breaches as $b): ?>
                    <tr>
                        <td><?php echo $b->id; ?></td>
                        <td><?php echo e($b->titulo); ?></td>
                        <td><?php echo e($b->tipo_dado_afetado ?? '-'); ?></td>
                        <td style="white-space: nowrap;"><?php echo date('d/m/Y H:i', strtotime($b->data_ocorrencia)); ?></td>
                        <td>
                            <?php
                            $statusLabels = [
                                'investigando' => '<span class="badge bg-warning">Investigando</span>',
                                'notificado'   => '<span class="badge bg-info">Notificado</span>',
                                'resolvido'    => '<span class="badge bg-success">Resolvido</span>',
                            ];
                            echo $statusLabels[$b->status] ?? '<span class="label">' . e($b->status) . '</span>';
                            ?>
                        </td>
                        <td>
                            <?php if ($b->notificado_anpd): ?>
                                <span class="badge bg-success">Sim</span>
                                <br><small><?php echo $b->data_notificacao_anpd ? date('d/m/Y', strtotime($b->data_notificacao_anpd)) : ''; ?></small>
                            <?php else: ?>
                                <span class="badge bg-danger">Nao</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($b->titulares_notificados): ?>
                                <span class="badge bg-success">Notificados</span>
                                <br><small><?php echo $b->num_titulares_afetados; ?> titular(es)</small>
                            <?php else: ?>
                                <a href="<?php echo site_url('auditoria/vazamento_notificar_titulares/' . $b->id); ?>"
                                   class="btn btn-sm btn-warning"
                                   onclick="return confirm('Confirma notificacao aos titulares afetados?');">
                                    <i class="fas fa-bell"></i> Notificar
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="#modal-detalhe-<?php echo $b->id; ?>" role="button" data-bs-toggle="modal" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($breaches)): ?>
                    <tr>
                        <td colspan="8">Nenhuma notificacao de vazamento registrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo $this->pagination->create_links(); ?>

<!-- Detail modals -->
<?php foreach ($breaches as $b): ?>
<div id="modal-detalhe-<?php echo $b->id; ?>" class="modal fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        <h5>Vazamento #<?php echo $b->id; ?> — <?php echo e($b->titulo); ?></h5>
    </div>
    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
        <p><strong>Descricao:</strong><br><?php echo nl2br(e($b->descricao)); ?></p>
        <p><strong>Tipo de dado afetado:</strong> <?php echo e($b->tipo_dado_afetado ?? '-'); ?></p>
        <p><strong>Medidas adotadas:</strong><br><?php echo nl2br(e($b->medidas_adotadas ?? 'Nenhuma medida registrada')); ?></p>
        <p><strong>Data da ocorrencia:</strong> <?php echo date('d/m/Y H:i', strtotime($b->data_ocorrencia)); ?></p>
        <p><strong>Data da descoberta:</strong> <?php echo date('d/m/Y H:i', strtotime($b->data_descoberta)); ?></p>
        <p><strong>Status:</strong> <?php echo e($b->status); ?></p>
        <p><strong>Notificado a ANPD:</strong> <?php echo $b->notificado_anpd ? 'Sim (' . date('d/m/Y', strtotime($b->data_notificacao_anpd)) . ')' : 'Nao'; ?></p>
        <p><strong>Titulares notificados:</strong> <?php echo $b->titulares_notificados ? 'Sim (' . $b->num_titulares_afetados . ')' : 'Nao'; ?></p>
        <p><strong>Registrado por:</strong> Usuario ID <?php echo $b->registrado_por ?? '-'; ?></p>
        <p><small class="muted">Criado em: <?php echo $b->created_at; ?></small></p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-bs-dismiss="modal">Fechar</button>
    </div>
</div>
<?php endforeach; ?>

<!-- New breach modal -->
<div id="modal-novo-vazamento" class="modal fade" tabindex="-1" style="width: 700px; margin-left: -250px;">
    <form action="<?php echo site_url('auditoria/vazamento_novo'); ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            <h5>Registrar Notificacao de Vazamento (LGPD Art. 48)</h5>
        </div>
        <div class="modal-body">
            <label>Titulo *</label>
            <input type="text" name="titulo" class="col-6" required style="width: 100%;">

            <label>Descricao do Incidente *</label>
            <textarea name="descricao" rows="4" class="col-6" required style="width: 100%;"></textarea>

            <label>Tipo de Dado Afetado</label>
            <input type="text" name="tipo_dado_afetado" class="col-6" placeholder="Ex: dados pessoais, financeiros, credenciais" style="width: 100%;">

            <label>Medidas Adotadas</label>
            <textarea name="medidas_adotadas" rows="3" class="col-6" placeholder="Acoes tomadas para conter e mitigar o vazamento" style="width: 100%;"></textarea>

            <div class="row" style="margin-top: 10px;">
                <div class="col-6">
                    <label>Data da Ocorrencia *</label>
                    <input type="datetime-local" name="data_ocorrencia" required>
                </div>
                <div class="col-6">
                    <label>Data da Descoberta *</label>
                    <input type="datetime-local" name="data_descoberta" required>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-6">
                    <label>Status</label>
                    <select name="status">
                        <option value="investigando">Investigando</option>
                        <option value="notificado">Notificado</option>
                        <option value="resolvido">Resolvido</option>
                    </select>
                </div>
                <div class="col-6" style="padding-top: 25px;">
                    <label class="checkbox">
                        <input type="checkbox" name="notificado_anpd" value="1"> ANPD ja notificada
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-warning">Registrar Vazamento</button>
        </div>
    </form>
</div>
</div>