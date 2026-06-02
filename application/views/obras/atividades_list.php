<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$total = count($atividades);
$hoje = count(array_filter($atividades, function($a) { return isset($a->data_atividade) && $a->data_atividade == date('Y-m-d'); }));
$agendadas = count(array_filter($atividades, function($a) { return isset($a->status) && $a->status == 'agendada'; }));
$concluidas = count(array_filter($atividades, function($a) { return isset($a->status) && $a->status == 'concluida'; }));

$statusColors = [
    'agendada' => '#95a5a6',
    'iniciada' => '#3498db',
    'em-andamento' => '#4facfe',
    'pausada' => '#f39c12',
    'concluida' => '#27ae60',
    'cancelada' => '#7f8c8d',
];
?>

<style>
.atv-stats { display: flex; gap: 12px; margin-bottom: 12px; flex-wrap: wrap; }
.atv-stat-item { flex: 1; min-width: 120px; text-align: center; padding: 10px 8px; border-radius: 8px; background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.06); }
.atv-stat-item .stat-val { font-size: 22px; font-weight: 700; color: var(--title, #333); }
.atv-stat-item .stat-lbl { font-size: 11px; color: var(--cinza0, #9aa6b3); text-transform: uppercase; margin-top: 2px; }
body[data-theme="puredark"] .atv-stat-item .stat-val,
body[data-theme="darkviolet"] .atv-stat-item .stat-val,
body[data-theme="darkorange"] .atv-stat-item .stat-val { color: #e2e8f0; }
.atv-filters { display: flex; gap: 10px; align-items: center; margin-bottom: 12px; flex-wrap: wrap; }
.atv-filters input, .atv-filters select { height: 32px; font-size: 13px; }
.atv-status-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }
.atv-progress-bar { display: inline-block; width: 80px; height: 8px; background: rgba(0,0,0,0.08); border-radius: 4px; overflow: hidden; vertical-align: middle; margin-right: 6px; }
body[data-theme="puredark"] .atv-progress-bar,
body[data-theme="darkviolet"] .atv-progress-bar,
body[data-theme="darkorange"] .atv-progress-bar { background: rgba(255,255,255,0.1); }
.atv-progress-fill { height: 100%; border-radius: 4px; transition: width 0.3s; }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-calendar-check"></i>
        </span>
        <h5>Atividades — <?php echo htmlspecialchars($obra->nome); ?></h5>
        <div class="buttons">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
            <button onclick="$('#modalAdicionar').modal('show')" class="button btn btn-sm btn-success">
                <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                <span class="button__text2">Nova Atividade</span>
            </button>
            <?php endif; ?>
            <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="button btn btn-sm btn-warning">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Ver Obra</span>
            </a>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
    <div class="col-12" style="margin-left:0;margin-top:8px;">
        <div class="alert alert-success">
            <i class="bx bx-check-circle"></i> <?php echo htmlspecialchars($this->session->flashdata('success')); ?>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
    <div class="col-12" style="margin-left:0;margin-top:8px;">
        <div class="alert alert-danger">
            <i class="bx bx-x-circle"></i> <?php echo htmlspecialchars($this->session->flashdata('error')); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-12" style="margin-left:0;margin-top:8px;">
        <div class="atv-stats">
            <div class="atv-stat-item">
                <div class="stat-val"><?php echo $total; ?></div>
                <div class="stat-lbl">Total</div>
            </div>
            <div class="atv-stat-item">
                <div class="stat-val"><?php echo $hoje; ?></div>
                <div class="stat-lbl">Hoje</div>
            </div>
            <div class="atv-stat-item">
                <div class="stat-val"><?php echo $agendadas; ?></div>
                <div class="stat-lbl">Agendadas</div>
            </div>
            <div class="atv-stat-item">
                <div class="stat-val"><?php echo $concluidas; ?></div>
                <div class="stat-lbl">Concluídas</div>
            </div>
        </div>

        <div class="atv-filters">
            <input type="text" id="searchAtividade" placeholder="Buscar atividade..." class="col-3" onkeyup="filtrarAtividades()">
            <select id="filterStatus" class="col-2" onchange="filtrarAtividades()">
                <option value="">Todos os Status</option>
                <option value="agendada">Agendada</option>
                <option value="iniciada">Iniciada</option>
                <option value="pausada">Pausada</option>
                <option value="concluida">Concluída</option>
                <option value="cancelada">Cancelada</option>
            </select>
            <select id="filterTipo" class="col-2" onchange="filtrarAtividades()">
                <option value="">Todos os Tipos</option>
                <option value="trabalho">Trabalho</option>
                <option value="visita">Visita Técnica</option>
                <option value="manutencao">Manutenção</option>
                <option value="impedimento">Impedimento</option>
                <option value="outro">Outro</option>
            </select>
        </div>
    </div>

    <div class="widget-box" style="margin-top: 4px;">
        <div class="widget-content nopadding tab-content">

            <?php
            $todas_atividades = [];
            if (!empty($atividades)) {
                foreach ($atividades as $ativ) {
                    $todas_atividades[] = [
                        'id' => $ativ->id ?? $ativ->idAtividade ?? 0,
                        'titulo' => $ativ->titulo ?? 'Atividade',
                        'descricao' => $ativ->descricao ?? '',
                        'status' => $ativ->status ?? 'agendada',
                        'tipo' => $ativ->tipo ?? 'trabalho',
                        'data' => $ativ->data_atividade ?? $ativ->data_criacao ?? date('Y-m-d'),
                        'tecnico' => $ativ->nome_tecnico ?? $ativ->tecnico_nome ?? '—',
                        'etapa' => $ativ->nome_etapa ?? $ativ->etapa_nome ?? '—',
                        'progresso' => $ativ->percentual_concluido ?? 0,
                        'sistema' => 'antigo'
                    ];
                }
            }
            if (!empty($atividades_registradas)) {
                foreach ($atividades_registradas as $ativ) {
                    $status = 'agendada';
                    if (!empty($ativ->hora_fim)) { $status = 'concluida'; }
                    elseif (!empty($ativ->hora_inicio)) { $status = 'iniciada'; }
                    $todas_atividades[] = [
                        'id' => $ativ->idAtividade ?? 0,
                        'titulo' => $ativ->titulo ?? $ativ->tipo_atividade ?? 'Atividade Técnica',
                        'descricao' => $ativ->descricao ?? '',
                        'status' => $ativ->status ?? $status,
                        'tipo' => $ativ->categoria ?? 'trabalho',
                        'data' => date('Y-m-d', strtotime($ativ->hora_inicio ?? 'now')),
                        'tecnico' => $ativ->nome_tecnico ?? '—',
                        'etapa' => $ativ->etapa_nome ?? '—',
                        'progresso' => ($ativ->status == 'finalizada' && $ativ->concluida) ? 100 : 0,
                        'hora_inicio' => $ativ->hora_inicio ?? null,
                        'hora_fim' => $ativ->hora_fim ?? null,
                        'duracao' => $ativ->duracao_minutos ?? null,
                        'sistema' => 'novo'
                    ];
                }
            }
            usort($todas_atividades, function($a, $b) { return strtotime($b['data']) - strtotime($a['data']); });
            ?>

            <?php if (!empty($todas_atividades)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="atividadesTable">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Título</th>
                            <th>Técnico</th>
                            <th>Etapa</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Progresso</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="atividadesTableBody">
                        <?php foreach ($todas_atividades as $a):
                            $sColor = $statusColors[$a['status']] ?? '#667eea';
                            $sLabel = ucfirst($a['status']);
                            $pColor = $a['progresso'] >= 100 ? '#27ae60' : ($a['progresso'] > 50 ? '#3498db' : '#f39c12');
                            $aId = (int)$a['id'];
                            $aSistema = htmlspecialchars($a['sistema'] ?? 'antigo');
                        ?>
                        <tr data-titulo="<?php echo strtolower(str_replace('"', '', $a['titulo'])); ?>"
                            data-status="<?php echo $a['status']; ?>"
                            data-tipo="<?php echo $a['tipo']; ?>">
                            <td style="white-space:nowrap;">
                                <?php echo date('d/m/Y', strtotime($a['data'])); ?>
                                <?php if ($a['sistema'] == 'novo' && !empty($a['hora_inicio'])): ?>
                                <br><small style="color:var(--cinza0,#9aa6b3);"><?php echo date('H:i', strtotime($a['hora_inicio'])); ?>
                                <?php if (!empty($a['hora_fim'])): ?>
                                — <?php echo date('H:i', strtotime($a['hora_fim'])); ?>
                                <?php endif; ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="javascript:void(0)" onclick="abrirModalAtividade(<?php echo $aId; ?>, '<?php echo $aSistema; ?>')" style="font-weight:600;">
                                    <?php echo htmlspecialchars($a['titulo']); ?>
                                </a>
                                <?php if (!empty($a['descricao'])): ?>
                                <br><small style="color:var(--cinza0,#9aa6b3);"><?php echo htmlspecialchars(mb_strimwidth($a['descricao'], 0, 60, '...')); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($a['tecnico']); ?></td>
                            <td><?php echo htmlspecialchars($a['etapa']); ?></td>
                            <td>
                                <?php
                                $tipoIcons = ['trabalho' => 'bx-wrench', 'visita' => 'bx-map', 'manutencao' => 'bx-refresh', 'impedimento' => 'bx-block', 'outro' => 'bx-dots-horizontal'];
                                $tipoIcon = $tipoIcons[$a['tipo']] ?? 'bx-dots-horizontal';
                                ?>
                                <i class="bx <?php echo $tipoIcon; ?>"></i> <?php echo ucfirst($a['tipo']); ?>
                            </td>
                            <td>
                                <span class="atv-status-dot" style="background:<?php echo $sColor; ?>"></span>
                                <?php echo $sLabel; ?>
                            </td>
                            <td style="white-space:nowrap;">
                                <span class="atv-progress-bar"><span class="atv-progress-fill" style="width:<?php echo $a['progresso']; ?>%;background:<?php echo $pColor; ?>;"></span></span>
                                <span style="font-size:12px;font-weight:600;"><?php echo $a['progresso']; ?>%</span>
                            </td>
                            <td class="text-nowrap">
                                <a href="javascript:void(0)" class="btn-action btn-action-view" title="Ver detalhes" onclick="abrirModalAtividade(<?php echo $aId; ?>, '<?php echo $aSistema; ?>')">
                                    <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#view"/></svg>
                                </a>
                                <?php if ($this->session->userdata('permissao') == 1): ?>
                                <a href="javascript:void(0)" class="btn-action btn-action-delete" title="Excluir" onclick="event.stopPropagation(); excluirAtividade(<?php echo $aId; ?>, '<?php echo $aSistema; ?>')">
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
                <i class="bx bx-calendar-x" style="font-size:48px;display:block;margin-bottom:10px;opacity:0.4;"></i>
                <p>Nenhuma atividade encontrada.</p>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <button onclick="$('#modalAdicionar').modal('show')" class="button btn btn-sm btn-success" style="margin-top:10px;">
                    <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                    <span class="button__text2">Adicionar Atividade</span>
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Iniciar Registro -->
<div id="modalIniciarRegistro" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:10000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h3><i class="bx bx-play-circle"></i> Iniciar Atividade</h3>
            </div>
            <form id="formIniciarRegistro" onsubmit="return iniciarRegistroAtividade(event)">
                <div class="modal-body">
                    <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
                    <input type="hidden" name="latitude" id="registro_latitude">
                    <input type="hidden" name="longitude" id="registro_longitude">
                    <div class="mb-3">
                        <label for="etapa_id_registro"><strong>Etapa da Obra</strong> <span class="required">*</span></label>
                        <select name="etapa_id" id="etapa_id_registro" class="col-12" required>
                            <option value="">Selecione uma etapa...</option>
                            <?php if (isset($etapas) && !empty($etapas)): ?>
                                <?php foreach ($etapas as $e): ?>
                                <option value="<?php echo $e->id; ?>">#<?php echo $e->numero_etapa ?? 'N/A'; ?> — <?php echo $e->nome; ?><?php echo isset($e->progresso_real) && $e->progresso_real > 0 ? ' (' . $e->progresso_real . '%)' : ''; ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhuma etapa cadastrada</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_id_registro"><strong>Tipo de Atividade</strong> <span class="required">*</span></label>
                        <select name="tipo_id" id="tipo_id_registro" class="col-12" required>
                            <option value="">Selecione o tipo...</option>
                            <?php if (!empty($tipos_atividades)): ?>
                                <?php foreach ($tipos_atividades as $tipo): ?>
                                <option value="<?php echo $tipo->idTipo; ?>"><?php echo $tipo->nome; ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="1">Trabalho Técnico</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descricao_registro"><strong>Descrição</strong></label>
                        <textarea name="descricao" id="descricao_registro" class="col-12" rows="2" placeholder="Descreva o trabalho que será realizado..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="equipamento_registro"><strong>Equipamento/Local</strong></label>
                        <input type="text" name="equipamento" id="equipamento_registro" class="col-12" placeholder="Ex: Rack principal, Câmera 1...">
                    </div>
                    <div class="mb-3">
                        <label><strong>Localização GPS</strong></label><br>
                        <button type="button" class="btn btn-sm btn-info" onclick="obterLocalizacaoRegistro()">
                            <i class="bx bx-map-pin"></i> Obter Localização
                        </button>
                        <div id="gps_info_registro" style="margin-top:4px;font-size:12px;color:var(--cinza0,#9aa6b3);">
                            Clique para registrar a localização.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnIniciarRegistro">
                        <i class="bx bx-play"></i> Iniciar Atividade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Adicionar Atividade -->
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
<div id="modalAdicionar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:10000;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h3><i class="bx bx-plus-circle"></i> Nova Atividade</h3>
            </div>
            <form id="formAdicionarAtividade" onsubmit="return salvarAtividadeWizard(event)">
                <div class="modal-body">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
                    <input type="hidden" name="latitude" id="nova_latitude">
                    <input type="hidden" name="longitude" id="nova_longitude">

                    <div class="alert alert-info" style="margin-bottom:15px;">
                        <i class="bx bx-info-circle"></i> A atividade será criada no sistema de atendimento técnico.
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label for="etapa_id_nova"><strong>Etapa da Obra</strong> <span class="required">*</span></label>
                            <select name="etapa_id" id="etapa_id_nova" class="col-12" required>
                                <option value="">Selecione uma etapa...</option>
                                <?php if (isset($etapas) && !empty($etapas)): ?>
                                    <?php foreach ($etapas as $e): ?>
                                    <option value="<?php echo $e->id; ?>">#<?php echo $e->numero_etapa ?? 'N/A'; ?> — <?php echo $e->nome; ?><?php echo isset($e->progresso_real) && $e->progresso_real > 0 ? ' (' . $e->progresso_real . '%)' : ''; ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Nenhuma etapa cadastrada</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <?php if (!empty($tipos_atividades)): ?>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-6">
                            <label for="tipo_id_nova"><strong>Tipo de Atividade</strong> <span class="required">*</span></label>
                            <select name="tipo_id" id="tipo_id_nova" class="col-12" required>
                                <option value="">Selecione o tipo...</option>
                                <?php foreach ($tipos_atividades as $tipo): ?>
                                <option value="<?php echo $tipo->idTipo; ?>"><?php echo $tipo->nome; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="tipo_id" value="1">
                    <?php endif; ?>

                    <div class="row" style="margin-top:10px;">
                        <div class="col-12">
                            <label for="titulo_nova"><strong>Título da Atividade</strong> <span class="required">*</span></label>
                            <input type="text" name="titulo" id="titulo_nova" class="col-12" placeholder="Ex: Instalação elétrica..." required>
                        </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                        <div class="col-12">
                            <label for="descricao_nova"><strong>Descrição</strong></label>
                            <textarea name="descricao" id="descricao_nova" class="col-12" rows="2" placeholder="Descreva o trabalho..."></textarea>
                        </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                        <div class="col-6">
                            <label for="equipamento_nova"><strong>Equipamento/Local</strong></label>
                            <input type="text" name="equipamento" id="equipamento_nova" class="col-12" placeholder="Ex: Rack principal...">
                        </div>
                        <div class="col-6">
                            <label><strong>Localização GPS</strong></label><br>
                            <button type="button" class="btn btn-sm btn-info" onclick="obterLocalizacaoNovaAtividade()">
                                <i class="bx bx-map-pin"></i> Obter Localização
                            </button>
                            <div id="gps_info_nova" style="margin-top:4px;font-size:12px;color:var(--cinza0,#9aa6b3);">
                                Clique para registrar a localização.
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-6">
                            <label for="tecnico_id_nova"><strong>Técnico Responsável</strong></label>
                            <select name="tecnico_id" id="tecnico_id_nova" class="col-12">
                                <option value="">Selecione um técnico...</option>
                                <?php if (!empty($tecnicos)): ?>
                                    <?php foreach ($tecnicos as $t): ?>
                                    <option value="<?php echo $t->idUsuarios; ?>"><?php echo $t->nome; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-6" style="padding-top:28px;">
                            <label style="font-weight:normal;">
                                <input type="checkbox" name="visivel_cliente" value="1" checked> Visível ao cliente
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarAtividade">
                        <i class="bx bx-save"></i> Criar Atividade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Visualizar/Editar Atividade -->
<div id="modalVerAtividade" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:10000;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="modalVerLabel"><i class="bx bx-calendar-check"></i> Detalhes da Atividade</h3>
            </div>
            <div class="modal-body" id="modalVerBody" style="max-height:500px;overflow-y:auto;">
                <div style="text-align:center;padding:40px;">
                    <i class="bx bx-loader-alt bx-spin" style="font-size:40px;color:var(--sidebar-accent,#667eea);"></i>
                    <p style="margin-top:15px;color:var(--cinza0,#9aa6b3);">Carregando...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-warning" id="btnEditarAtividade" onclick="toggleEdicao()">
                    <i class="bx bx-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-success" id="btnSalvarAtividade" onclick="salvarAtividade()" style="display:none;">
                    <i class="bx bx-save"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var atividadeAtual = null;
var modoEdicao = false;

function filtrarAtividades() {
    var search = document.getElementById('searchAtividade').value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    var status = document.getElementById('filterStatus').value;
    var tipo = document.getElementById('filterTipo').value;
    var rows = document.querySelectorAll('#atividadesTableBody tr');
    rows.forEach(function(row) {
        var titulo = (row.getAttribute('data-titulo') || '').toLowerCase();
        var rowStatus = row.getAttribute('data-status') || '';
        var rowTipo = row.getAttribute('data-tipo') || '';
        var matchSearch = !search || titulo.includes(search);
        var matchStatus = !status || rowStatus === status;
        var matchTipo = !tipo || rowTipo === tipo;
        row.style.display = matchSearch && matchStatus && matchTipo ? '' : 'none';
    });
}

function obterLocalizacaoRegistro() {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('registro_latitude').value = position.coords.latitude;
            document.getElementById('registro_longitude').value = position.coords.longitude;
            document.getElementById('gps_info_registro').innerHTML = '<span style="color:#27ae60;"><i class="bx bx-check-circle"></i> Localização obtida!</span>';
        }, function(error) {
            document.getElementById('gps_info_registro').innerHTML = '<span style="color:#e74c3c;"><i class="bx bx-x-circle"></i> Erro: ' + error.message + '</span>';
        });
    } else {
        document.getElementById('gps_info_registro').innerHTML = '<span style="color:#e74c3c;">GPS não disponível.</span>';
    }
}

function iniciarRegistroAtividade(event) {
    event.preventDefault();
    var form = document.getElementById('formIniciarRegistro');
    var formData = new FormData(form);
    var etapaId = formData.get('etapa_id');
    var tipoId = formData.get('tipo_id');
    if (!etapaId) { alert('Selecione uma etapa da obra.'); document.getElementById('etapa_id_registro').focus(); return false; }
    if (!tipoId) { alert('Selecione o tipo de atividade.'); document.getElementById('tipo_id_registro').focus(); return false; }
    document.getElementById('btnIniciarRegistro').disabled = true;
    document.getElementById('btnIniciarRegistro').innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Iniciando...';
    fetch('<?php echo site_url("atividades/checkin_obra"); ?>', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { $('#modalIniciarRegistro').modal('hide'); alert('Atividade iniciada com sucesso!'); location.reload(); }
        else { alert('Erro: ' + (data.message || 'Erro ao iniciar atividade')); document.getElementById('btnIniciarRegistro').disabled = false; document.getElementById('btnIniciarRegistro').innerHTML = '<i class="bx bx-play"></i> Iniciar Atividade'; }
    })
    .catch(function() { alert('Erro ao iniciar atividade.'); document.getElementById('btnIniciarRegistro').disabled = false; document.getElementById('btnIniciarRegistro').innerHTML = '<i class="bx bx-play"></i> Iniciar Atividade'; });
    return false;
}

function obterLocalizacaoNovaAtividade() {
    if ('geolocation' in navigator) {
        document.getElementById('gps_info_nova').innerHTML = 'Obtendo localização...';
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('nova_latitude').value = position.coords.latitude;
            document.getElementById('nova_longitude').value = position.coords.longitude;
            document.getElementById('gps_info_nova').innerHTML = '<span style="color:#27ae60;"><i class="bx bx-check-circle"></i> Localização: ' + position.coords.latitude.toFixed(6) + ', ' + position.coords.longitude.toFixed(6) + '</span>';
        }, function(error) {
            document.getElementById('gps_info_nova').innerHTML = '<span style="color:#e74c3c;"><i class="bx bx-x-circle"></i> Erro: ' + error.message + '</span>';
        }, { enableHighAccuracy: true, timeout: 10000 });
    } else {
        document.getElementById('gps_info_nova').innerHTML = '<span style="color:#e74c3c;">GPS não disponível.</span>';
    }
}

function salvarAtividadeWizard(event) {
    event.preventDefault();
    var form = document.getElementById('formAdicionarAtividade');
    var formData = new FormData(form);
    var etapaId = formData.get('etapa_id');
    var tipoId = formData.get('tipo_id');
    var titulo = formData.get('titulo');
    if (!etapaId) { alert('Selecione uma etapa da obra.'); document.getElementById('etapa_id_nova').focus(); return false; }
    if (!tipoId) { alert('Selecione o tipo de atividade.'); document.getElementById('tipo_id_nova').focus(); return false; }
    if (!titulo || titulo.trim() === '') { alert('Informe o título da atividade.'); document.getElementById('titulo_nova').focus(); return false; }
    var btn = document.getElementById('btnSalvarAtividade');
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Salvando...';
    fetch('<?php echo site_url("atividades/checkin_obra"); ?>', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { $('#modalAdicionar').modal('hide'); alert('Atividade criada com sucesso!'); location.reload(); }
        else { alert('Erro: ' + (data.message || 'Erro ao criar atividade')); btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Criar Atividade'; }
    })
    .catch(function() { alert('Erro ao criar atividade.'); btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Criar Atividade'; });
    return false;
}

function abrirModalAtividade(id, sistema) {
    atividadeAtual = { id: id, sistema: sistema };
    modoEdicao = false;
    document.getElementById('modalVerBody').innerHTML = '<div style="text-align:center;padding:40px;"><i class="bx bx-loader-alt bx-spin" style="font-size:40px;color:var(--sidebar-accent,#667eea);"></i><p style="margin-top:15px;color:var(--cinza0,#9aa6b3);">Carregando...</p></div>';
    document.getElementById('btnEditarAtividade').style.display = 'inline-block';
    document.getElementById('btnSalvarAtividade').style.display = 'none';
    $('#modalVerAtividade').modal('show');
    var url = sistema === 'novo' ? '<?php echo site_url("atividades/detalhes/"); ?>' + id : '<?php echo site_url("obras/api_getAtividade/"); ?>' + id;
    $.ajax({ url: url, type: 'GET', dataType: 'json', headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
            if (data.success) {
                if (sistema === 'novo') { renderizarAtividadeNovo(data.atividade); }
                else { atividadeAtual.dados = data.atividade; atividadeAtual.execucao = data.execucao_real; renderizarAtividadeAntigo(data.atividade, data.execucao_real); }
            } else {
                document.getElementById('modalVerBody').innerHTML = '<div style="text-align:center;padding:40px;color:#e74c3c;"><i class="bx bx-error-circle" style="font-size:40px;"></i><p>' + (data.message || 'Erro ao carregar') + '</p></div>';
            }
        },
        error: function() {
            document.getElementById('modalVerBody').innerHTML = '<div style="text-align:center;padding:40px;color:#e74c3c;"><i class="bx bx-error-circle" style="font-size:40px;"></i><p>Erro ao carregar atividade</p></div>';
        }
    });
}

function renderizarAtividadeAntigo(atividade, execucao) {
    var statusClass = atividade.status || 'agendada';
    var statusLabel = statusClass.charAt(0).toUpperCase() + statusClass.slice(1);
    var h = '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;padding:10px;background:var(--widget-box,#f8f9fa);border-radius:8px;">';
    h += '<div><strong>Status:</strong> <span class="label label-' + (statusClass === 'concluida' ? 'success' : (statusClass === 'iniciada' ? 'info' : 'default')) + '">' + statusLabel + '</span></div>';
    h += '<div style="color:var(--cinza0,#9aa6b3);font-size:12px;">ID: #' + atividade.id + '</div></div>';
    h += '<div class="mb-3"><label style="font-weight:600;"><i class="bx bx-tag"></i> Título</label>';
    h += '<div class="view-field" style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + (atividade.titulo || '—') + '</div>';
    h += '<input type="text" name="titulo" class="col-12 edit-field" value="' + (atividade.titulo || '').replace(/"/g, '&quot;') + '" style="display:none;"></div>';
    h += '<div class="mb-3"><label style="font-weight:600;"><i class="bx bx-detail"></i> Descrição</label>';
    h += '<div class="view-field" style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);min-height:50px;">' + (atividade.descricao || '—') + '</div>';
    h += '<textarea name="descricao" class="col-12 edit-field" rows="3" style="display:none;">' + (atividade.descricao || '') + '</textarea></div>';
    h += '<div class="row"><div class="col-6"><label style="font-weight:600;"><i class="bx bx-calendar"></i> Data</label>';
    h += '<div class="view-field" style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + (atividade.data_atividade ? formatarData(atividade.data_atividade) : '—') + '</div>';
    h += '<input type="date" name="data_atividade" class="col-12 edit-field" value="' + (atividade.data_atividade || '') + '" style="display:none;"></div>';
    h += '<div class="col-6"><label style="font-weight:600;"><i class="bx bx-wrench"></i> Tipo</label>';
    h += '<div class="view-field" style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + (atividade.tipo ? atividade.tipo.charAt(0).toUpperCase() + atividade.tipo.slice(1) : '—') + '</div>';
    h += '<select name="tipo" class="col-12 edit-field" style="display:none;">';
    h += '<option value="trabalho" ' + (atividade.tipo === 'trabalho' ? 'selected' : '') + '>Trabalho</option>';
    h += '<option value="visita" ' + (atividade.tipo === 'visita' ? 'selected' : '') + '>Visita Técnica</option>';
    h += '<option value="manutencao" ' + (atividade.tipo === 'manutencao' ? 'selected' : '') + '>Manutenção</option>';
    h += '<option value="impedimento" ' + (atividade.tipo === 'impedimento' ? 'selected' : '') + '>Impedimento</option>';
    h += '<option value="outro" ' + (atividade.tipo === 'outro' ? 'selected' : '') + '>Outro</option></select></div></div>';
    if (execucao && execucao.idAtividade) {
        h += '<hr style="margin:20px 0;border-color:var(--border-color,#e0e0e0);">';
        h += '<div style="background:rgba(var(--sidebar-accent-rgb,4,103,252),0.06);padding:15px;border-radius:8px;border-left:4px solid var(--sidebar-accent,#667eea);">';
        h += '<h4 style="margin:0 0 10px 0;color:var(--sidebar-accent,#667eea);"><i class="bx bx-timer"></i> Execução Real</h4>';
        h += '<div class="row"><div class="col-6"><label style="font-weight:600;">Hora Início</label><div style="padding:10px;background:var(--widget-box,#fff);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + (execucao.hora_inicio ? formatarDataHora(execucao.hora_inicio) : '—') + '</div></div>';
        h += '<div class="col-6"><label style="font-weight:600;">Hora Fim</label><div style="padding:10px;background:var(--widget-box,#fff);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + (execucao.hora_fim ? formatarDataHora(execucao.hora_fim) : '—') + '</div></div></div>';
        if (execucao.duracao_minutos) { var hrs = Math.floor(execucao.duracao_minutos / 60); var mins = execucao.duracao_minutos % 60; h += '<div style="margin-top:10px;padding:10px;background:var(--widget-box,#fff);border-radius:6px;text-align:center;"><strong style="color:var(--sidebar-accent,#667eea);font-size:18px;"><i class="bx bx-time"></i> Duração: ' + hrs + 'h ' + mins + 'min</strong></div>'; }
        if (execucao.observacoes) { h += '<div style="margin-top:10px;"><label style="font-weight:600;">Observações</label><div style="padding:10px;background:var(--widget-box,#fff);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);white-space:pre-wrap;">' + execucao.observacoes + '</div></div>'; }
        h += '</div>';
    }
    h += '</div>';
    document.getElementById('modalVerBody').innerHTML = h;
}

function renderizarAtividadeNovo(atividade) {
    var h = '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;padding:10px;background:var(--widget-box,#f8f9fa);border-radius:8px;">';
    h += '<div><strong>Status:</strong> <span class="badge bg-info">' + (atividade.status ? atividade.status.toUpperCase() : 'N/A') + '</span></div>';
    h += '<div style="color:var(--cinza0,#9aa6b3);font-size:12px;">ID: #' + atividade.idAtividade + '</div></div>';
    h += '<div class="mb-3"><label style="font-weight:600;"><i class="bx bx-tag"></i> Tipo de Atividade</label>';
    h += '<div style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + (atividade.tipo_atividade || '—') + '</div></div>';
    h += '<div class="mb-3"><label style="font-weight:600;"><i class="bx bx-detail"></i> Descrição</label>';
    h += '<div style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);min-height:50px;">' + (atividade.descricao || '—') + '</div></div>';
    if (atividade.hora_inicio) {
        h += '<div class="row"><div class="col-6"><label style="font-weight:600;"><i class="bx bx-time"></i> Hora Início</label>';
        h += '<div style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + formatarDataHora(atividade.hora_inicio) + '</div></div>';
        h += '<div class="col-6"><label style="font-weight:600;"><i class="bx bx-time"></i> Hora Fim</label>';
        h += '<div style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + (atividade.hora_fim ? formatarDataHora(atividade.hora_fim) : 'Em andamento...') + '</div></div></div>';
        if (atividade.duracao_minutos) { var hrs = Math.floor(atividade.duracao_minutos / 60); var mins = atividade.duracao_minutos % 60; h += '<div style="margin-top:10px;padding:10px;background:rgba(var(--sidebar-accent-rgb,4,103,252),0.06);border-radius:8px;text-align:center;"><strong style="color:var(--sidebar-accent,#667eea);font-size:18px;"><i class="bx bx-timer"></i> Duração: ' + hrs + 'h ' + mins + 'min</strong></div>'; }
    }
    if (atividade.nome_tecnico) { h += '<div class="mb-3" style="margin-top:10px;"><label style="font-weight:600;"><i class="bx bx-user"></i> Técnico</label><div style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + atividade.nome_tecnico + '</div></div>'; }
    if (atividade.etapa_nome) { h += '<div class="mb-3"><label style="font-weight:600;"><i class="bx bx-layer"></i> Etapa</label><div style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);">' + atividade.etapa_nome + '</div></div>'; }
    if (atividade.observacoes) { h += '<div class="mb-3"><label style="font-weight:600;"><i class="bx bx-note"></i> Observações</label><div style="padding:10px;background:var(--widget-box,#f5f5f5);border-radius:6px;border:1px solid var(--border-color,#e0e0e0);white-space:pre-wrap;">' + atividade.observacoes + '</div></div>'; }
    document.getElementById('modalVerBody').innerHTML = h;
    document.getElementById('btnEditarAtividade').style.display = 'none';
    document.getElementById('btnSalvarAtividade').style.display = 'none';
}

function toggleEdicao() {
    modoEdicao = !modoEdicao;
    if (modoEdicao) {
        $('.view-field').hide(); $('.edit-field').show();
        document.getElementById('btnEditarAtividade').style.display = 'none';
        document.getElementById('btnSalvarAtividade').style.display = 'inline-block';
    } else {
        $('.view-field').show(); $('.edit-field').hide();
        document.getElementById('btnEditarAtividade').style.display = 'inline-block';
        document.getElementById('btnSalvarAtividade').style.display = 'none';
    }
}

function salvarAtividade() {
    if (!atividadeAtual || !atividadeAtual.id) { alert('Nenhuma atividade selecionada'); return; }
    var dados = { id: atividadeAtual.id };
    var tituloEl = document.querySelector('input[name="titulo"]');
    var descricaoEl = document.querySelector('textarea[name="descricao"]');
    var dataEl = document.querySelector('input[name="data_atividade"]');
    var tipoEl = document.querySelector('select[name="tipo"]');
    if (tituloEl) dados.titulo = tituloEl.value;
    if (descricaoEl) dados.descricao = descricaoEl.value;
    if (dataEl) dados.data_atividade = dataEl.value;
    if (tipoEl) dados.tipo = tipoEl.value;
    var btn = document.getElementById('btnSalvarAtividade');
    btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Salvando...';
    $.ajax({ url: '<?php echo site_url("obras/api_salvarAtividade"); ?>', type: 'POST', dataType: 'json', data: dados, headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) { if (data.success) { alert('Atividade atualizada!'); $('#modalVerAtividade').modal('hide'); location.reload(); } else { alert('Erro: ' + (data.message || 'Não foi possível salvar')); btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Salvar'; } },
        error: function() { alert('Erro ao salvar'); btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Salvar'; }
    });
}

function formatarData(dataStr) { if (!dataStr) return '—'; var parts = dataStr.split('-'); if (parts.length !== 3) return dataStr; return parts[2] + '/' + parts[1] + '/' + parts[0]; }
function formatarDataHora(dataHoraStr) { if (!dataHoraStr) return '—'; var data = new Date(dataHoraStr); if (isNaN(data.getTime())) return dataHoraStr; return data.toLocaleString('pt-BR'); }

function excluirAtividade(id, sistema) {
    if (!confirm('Tem certeza que deseja excluir esta atividade?')) return;
    var url = sistema === 'novo' ? '<?php echo site_url('atividades/excluir/'); ?>' + id : '<?php echo site_url('obras/excluirAtividade/'); ?>' + id;
    $.ajax({ url: url, type: 'POST', dataType: 'json', headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) { if (data.success) { location.reload(); } else { alert('Erro: ' + (data.message || 'Não foi possível excluir')); } },
        error: function() { location.reload(); }
    });
}

$('#modalAdicionar').on('shown.bs.modal', function() { $('#titulo').focus(); });
</script>