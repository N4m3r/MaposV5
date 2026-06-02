<style>
  select { width: 70px; }
</style>
<div class="new122" style="margin-top: 0; min-height: 100vh">
<div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="fas fa-clock"></i></span>
        <h5>Logs</h5>
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
    <a class="os-tab-btn <?php echo ($tab ?? 'logs') === 'logs' ? 'active' : ''; ?>" href="<?php echo site_url('auditoria?tab=logs'); ?>"><i class="bx bx-list-ul"></i> Logs (Legado)</a>
    <a class="os-tab-btn <?php echo ($tab ?? '') === 'audit' ? 'active' : ''; ?>" href="<?php echo site_url('auditoria?tab=audit'); ?>"><i class="bx bx-shield-alt"></i> Auditoria Estruturada</a>
</div>
  <a href="#modal-excluir" role="button" data-bs-toggle="modal" class="button btn btn-danger tip-top" title="Excluir Logs">
  <span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Remover Logs - 30 dias ou mais</span></a>

<div class="widget-box">
    <h5 style="padding: 3px 0"></h5>
    <div class="widget-content nopadding tab-content">
        <table id="tabela" class="table table-bordered ">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>IP</th>
                    <th>Tarefa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $r) {
                    echo '<tr>';
                    echo '<td>' . $r->usuario . '</td>';
                    echo '<td>' . date('d/m/Y', strtotime($r->data)) . '</td>';
                    echo '<td>' . $r->hora . '</td>';
                    echo '<td>' . $r->ip . '</td>';
                    echo '<td>' . $r->tarefa . '</td>';
                    echo '</tr>';
                } ?>
                <?php if (!$results) { ?>
                    <tr>
                        <td colspan="5">Nenhum registro encontrado.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-excluir" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo site_url('auditoria/clean') ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            <h5>Limpeza de Logs</h5>
        </div>
        <div class="modal-body">
            <h5 style="text-align: center">Deseja realmente remover os logs mais antigos?</h5>
        </div>
        <div class="modal-footer">
            <button class="btn" data-bs-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-danger">Excluir</button>
        </div>
    </form>
</div>
</div>
