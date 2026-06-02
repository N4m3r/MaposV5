<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$obras = isset($obras) ? $obras : (isset($results) ? $results : []);
if (empty($obras)) {
    log_message('debug', 'obras_list.php: Nenhuma obra encontrada para exibição');
} else {
    log_message('debug', 'obras_list.php: ' . count($obras) . ' obras carregadas');
}
?>

<style>
.obras-stats { display: flex; gap: 12px; margin-bottom: 15px; flex-wrap: wrap; }
.obras-stat-item { flex: 1; min-width: 130px; text-align: center; padding: 10px 8px; border-radius: 8px; background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.06); }
.obras-stat-item .stat-val { font-size: 22px; font-weight: 700; color: var(--title, #333); }
.obras-stat-item .stat-lbl { font-size: 11px; color: var(--cinza0, #9aa6b3); text-transform: uppercase; margin-top: 2px; }
body[data-theme="puredark"] .obras-stat-item .stat-val,
body[data-theme="darkviolet"] .obras-stat-item .stat-val,
body[data-theme="darkorange"] .obras-stat-item .stat-val { color: #e2e8f0; }
.obras-filters { display: flex; gap: 10px; align-items: center; margin-bottom: 15px; flex-wrap: wrap; }
.obras-filters input, .obras-filters select { height: 32px; font-size: 13px; }
.obra-status-dot-inline { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-building"></i>
        </span>
        <h5>Obras</h5>
        <div class="buttons">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')): ?>
            <a href="<?php echo site_url('obras/adicionar'); ?>" class="button btn btn-sm btn-success">
                <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                <span class="button__text2">Nova Obra</span>
            </a>
            <?php endif; ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')): ?>
            <a href="<?php echo site_url('obras/configuracoes'); ?>" class="button btn btn-sm btn-warning">
                <span class="button__icon"><i class="bx bx-cog"></i></span>
                <span class="button__text2">Configurações</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Estatísticas rápidas -->
    <div class="col-12" style="margin-left: 0; margin-top: 10px;">
        <div class="obras-stats">
            <div class="obras-stat-item">
                <div class="stat-val"><?php echo isset($total_obras) ? $total_obras : count($obras); ?></div>
                <div class="stat-lbl">Total</div>
            </div>
            <div class="obras-stat-item">
                <div class="stat-val"><?php echo isset($obras_em_andamento) ? $obras_em_andamento : count(array_filter($obras ?? [], function($o) { return ($o->status ?? '') == 'em-andamento'; })); ?></div>
                <div class="stat-lbl">Em Andamento</div>
            </div>
            <div class="obras-stat-item">
                <div class="stat-val"><?php echo isset($obras_contratadas) ? $obras_contratadas : count(array_filter($obras ?? [], function($o) { return ($o->status ?? '') == 'contratada'; })); ?></div>
                <div class="stat-lbl">Contratadas</div>
            </div>
            <div class="obras-stat-item">
                <div class="stat-val"><?php echo isset($obras_concluidas) ? $obras_concluidas : count(array_filter($obras ?? [], function($o) { return ($o->status ?? '') == 'concluida'; })); ?></div>
                <div class="stat-lbl">Concluídas</div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="obras-filters">
            <input type="text" id="searchObra" placeholder="Buscar obra..." class="col-3" onkeyup="filtrarObras()">
            <select id="filterStatus" class="col-2" onchange="filtrarObras()">
                <option value="">Todos os Status</option>
                <?php foreach ($status_obra as $s): ?>
                    <option value="<?php echo htmlspecialchars(strtolower(preg_replace('/[^a-z]/', '', $s->nome))); ?>"><?php echo htmlspecialchars($s->nome); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
            <button type="button" class="button btn btn-sm btn-info" onclick="atualizarTodosProgressos()">
                <span class="button__icon"><i class="bx bx-refresh"></i></span>
                <span class="button__text2">Recalcular</span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="widget-box" style="margin-top: 8px">
        <div class="widget-content nopadding tab-content">
            <?php if (!empty($obras)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Obra</th>
                            <th>Cliente</th>
                            <th>Status</th>
                            <th>Progresso</th>
                            <th>Início</th>
                            <th>Previsão</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="obrasTableBody">
                        <?php
                        static $statusMapList = null;
                        if ($statusMapList === null) {
                            $statusMapList = [];
                            foreach ($status_obra as $s) {
                                $key = strtolower(preg_replace('/[^a-z]/', '', $s->nome));
                                $statusMapList[$key] = $s;
                            }
                        }

                        foreach ($obras as $obra):
                            if (!is_object($obra)) continue;

                            $status_lower = strtolower(trim($obra->status ?? ''));
                            $status_norm_key = strtolower(preg_replace('/[^a-z]/', '', $obra->status ?? ''));
                            $status_label = ucfirst($obra->status ?? '');
                            $status_cor = '#667eea';

                            if (isset($statusMapList[$status_norm_key])) {
                                $s = $statusMapList[$status_norm_key];
                                $status_label = $s->nome;
                                $status_cor = $s->cor ?? '#667eea';
                            } else {
                                switch ($status_lower) {
                                    case 'em-andamento': case 'em_execucao': case 'em execucao': $status_label = 'Em Andamento'; $status_cor = '#4facfe'; break;
                                    case 'concluida': case 'concluída': case 'finalizada': $status_label = 'Concluída'; $status_cor = '#11998e'; break;
                                    case 'paralisada': case 'pausada': case 'suspensa': $status_label = 'Paralisada'; $status_cor = '#e74c3c'; break;
                                    case 'prospeccao': case 'prospecção': case 'novo': case 'nova': $status_label = 'Prospecção'; $status_cor = '#95a5a6'; break;
                                    case 'contratada': case 'aprovada': $status_label = 'Contratada'; $status_cor = '#f39c12'; break;
                                    case 'cancelada': case 'cancelado': $status_label = 'Cancelada'; $status_cor = '#7f8c8d'; break;
                                }
                            }
                            $progresso = $obra->percentual_concluido ?? 0;
                            $status_normalized = $status_norm_key ?: $status_lower;
                        ?>
                        <tr data-nome="<?php echo strtolower($obra->nome); ?>" data-status="<?php echo $status_normalized; ?>">
                            <td><?php echo $obra->id; ?></td>
                            <td>
                                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" style="font-weight:600;">
                                    <?php echo htmlspecialchars($obra->nome); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($obra->cliente_nome ?? '—'); ?></td>
                            <td>
                                <span class="obra-status-dot-inline" style="background: <?php echo $status_cor; ?>"></span>
                                <?php echo $status_label; ?>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <div style="flex:1;height:8px;background:rgba(0,0,0,0.1);border-radius:4px;overflow:hidden;">
                                        <div style="width:<?php echo $progresso; ?>%;height:100%;background:<?php echo $status_cor; ?>;border-radius:4px;"></div>
                                    </div>
                                    <span style="font-size:12px;font-weight:600;min-width:32px;"><?php echo $progresso; ?>%</span>
                                </div>
                            </td>
                            <td><?php echo $obra->data_inicio_contrato ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : '—'; ?></td>
                            <td><?php echo $obra->data_fim_prevista ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : '—'; ?></td>
                            <td class="text-nowrap">
                                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="btn-action btn-action-view" title="Visualizar">
                                    <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#view"/></svg>
                                </a>
                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                                <a href="<?php echo site_url('obras/editar/' . $obra->id); ?>" class="btn-action btn-action-edit" title="Editar">
                                    <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#edit"/></svg>
                                </a>
                                <?php endif; ?>
                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                                <a href="<?php echo site_url('obras/excluir/' . $obra->id); ?>" class="btn-action btn-action-delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta obra?');">
                                    <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#delete"/></svg>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div style="text-align:center;padding:40px;color:var(--cinza0,#9aa6b3);">
                <i class="bx bx-building" style="font-size:48px;display:block;margin-bottom:10px;opacity:0.4;"></i>
                <p>Nenhuma obra encontrada.</p>
                <a href="<?php echo site_url('obras/adicionar'); ?>" class="button btn btn-sm btn-success">
                    <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                    <span class="button__text2">Cadastrar Obra</span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($pagination)): ?>
    <?php echo $pagination; ?>
    <?php endif; ?>
</div>

<!-- Toast container -->
<div id="obra-toast-container"></div>

<script>
function filtrarObras() {
    const search = document.getElementById('searchObra').value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    const status = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#obrasTableBody tr');

    rows.forEach(row => {
        const nome = (row.getAttribute('data-nome') || '').toLowerCase();
        const rowStatus = row.getAttribute('data-status') || '';
        const matchSearch = !search || nome.includes(search);
        const matchStatus = !status || rowStatus === status;
        row.style.display = matchSearch && matchStatus ? '' : 'none';
    });
}

function atualizarTodosProgressos() {
    $.ajax({
        url: '<?php echo site_url("obras/api_atualizarProgressoGeral"); ?>',
        method: 'POST',
        dataType: 'json',
        data: { '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' },
        success: function(response) {
            if (response.success) {
                alert('Progressos atualizados com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + (response.message || 'Erro ao recalcular progressos'));
            }
        },
        error: function() {
            alert('Erro ao recalcular progressos. Tente novamente.');
        }
    });
}
</script>