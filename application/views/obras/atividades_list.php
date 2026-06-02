<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<div class="atividades-modern">
    <div class="atividades-header">
        <div class="atividades-header-content">
            <div class="atividades-header-left">
                <div class="atividades-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><?= svg_icon('chevron-left', 14, 14) ?> Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo $obra->nome; ?></a> &raquo;
                    <span>Atividades</span>
                </div>
                <h1><?= svg_icon('calendar', 28, 28) ?> Atividades da Obra</h1>
                <div class="atividades-subtitle">Gerencie as atividades e acompanhe o progresso do trabalho</div>
            </div>
            <div class="atividades-actions">
                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="atividades-btn atividades-btn-secondary">
                    <?= svg_icon('eye', 16, 16) ?> Ver Obra
                </a>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <button onclick="$('#modalAdicionar').modal('show')" class="atividades-btn atividades-btn-primary">
                    <?= svg_icon('plus', 16, 16) ?> Nova Atividade
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
    <div class="obras-alert-success">
        <?= svg_icon('check-circle', 20, 20) ?> <strong><?php echo htmlspecialchars($this->session->flashdata('success')); ?></strong>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div class="obras-alert-error">
        <?= svg_icon('x', 20, 20) ?> <strong><?php echo htmlspecialchars($this->session->flashdata('error')); ?></strong>
    </div>
    <?php endif; ?>

    <?php
    $total = count($atividades);
    $hoje = count(array_filter($atividades, function($a) {
        return isset($a->data_atividade) && $a->data_atividade == date('Y-m-d');
    }));
    $agendadas = count(array_filter($atividades, function($a) {
        return isset($a->status) && $a->status == 'agendada';
    }));
    $concluidas = count(array_filter($atividades, function($a) {
        return isset($a->status) && $a->status == 'concluida';
    }));
    ?>
    <div class="atividades-stats">
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon total"><?= svg_icon('list-check', 24, 24) ?></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $total; ?></div>
                <div class="atividades-stat-label">Total de Atividades</div>
            </div>
        </div>
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon hoje"><?= svg_icon('calendar', 24, 24) ?></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $hoje; ?></div>
                <div class="atividades-stat-label">Atividades Hoje</div>
            </div>
        </div>
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon agendadas"><?= svg_icon('clock', 24, 24) ?></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $agendadas; ?></div>
                <div class="atividades-stat-label">Agendadas</div>
            </div>
        </div>
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon concluidas"><?= svg_icon('check-circle', 24, 24) ?></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $concluidas; ?></div>
                <div class="atividades-stat-label">Concluídas</div>
            </div>
        </div>
    </div>

    <div class="atividades-filters">
        <?= svg_icon('search', 20, 20, '', 'color:var(--sidebar-accent,#667eea);') ?>
        <input type="text" id="searchAtividade" class="atividades-filter-input" placeholder="Buscar atividade..." onkeyup="filtrarAtividades()">
        <select id="filterStatus" class="atividades-filter-select" onchange="filtrarAtividades()">
            <option value="">Todos os Status</option>
            <option value="agendada">Agendada</option>
            <option value="iniciada">Iniciada</option>
            <option value="pausada">Pausada</option>
            <option value="concluida">Concluída</option>
            <option value="cancelada">Cancelada</option>
        </select>
        <select id="filterTipo" class="atividades-filter-select" onchange="filtrarAtividades()">
            <option value="">Todos os Tipos</option>
            <option value="trabalho">Trabalho</option>
            <option value="visita">Visita Técnica</option>
            <option value="manutencao">Manutenção</option>
            <option value="impedimento">Impedimento</option>
            <option value="outro">Outro</option>
        </select>
    </div>

    <?php $this->load->view('obras/atividades_list_new'); ?>
</div>

<!-- Modal Iniciar Registro de Atividade -->
<div id="modalIniciarRegistro" class="modal fade modal-atividades" tabindex="-1" role="dialog" aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="modalRegistroLabel"><?= svg_icon('clock', 20, 20) ?> Iniciar Atividade - Registro de Tempo</h3>
            </div>

            <form id="formIniciarRegistro" onsubmit="return iniciarRegistroAtividade(event)">
                <div class="modal-body">
                    <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
                    <input type="hidden" name="latitude" id="registro_latitude">
                    <input type="hidden" name="longitude" id="registro_longitude">

                    <div class="atividades-form-group">
                        <label class="atividades-form-label" for="etapa_id_registro">
                            <?= svg_icon('list-check', 16, 16) ?> Etapa da Obra <span class="required">*</span>
                        </label>
                        <select name="etapa_id" id="etapa_id_registro" class="atividades-form-select" required>
                            <option value="">Selecione uma etapa...</option>
                            <?php if (isset($etapas) && !empty($etapas)): ?>
                                <?php foreach ($etapas as $e): ?>
                                <option value="<?php echo $e->id; ?>">
                                    #<?php echo $e->numero_etapa ?? 'N/A'; ?> - <?php echo $e->nome; ?>
                                    <?php if (isset($e->progresso_real) && $e->progresso_real > 0): ?>
                                        (<?php echo $e->progresso_real; ?>%)
                                    <?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhuma etapa cadastrada</option>
                            <?php endif; ?>
                        </select>
                        <div class="atividades-form-hint">
                            <?= svg_icon('info-circle', 14, 14) ?> Selecione a etapa em que você está trabalhando. Obrigatório.
                        </div>
                    </div>

                    <div class="atividades-form-group">
                        <label class="atividades-form-label" for="tipo_id_registro">
                            <?= svg_icon('wrench', 16, 16) ?> Tipo de Atividade <span class="required">*</span>
                        </label>
                        <select name="tipo_id" id="tipo_id_registro" class="atividades-form-select" required>
                            <option value="">Selecione o tipo...</option>
                            <?php if (!empty($tipos_atividades)): ?>
                                <?php foreach ($tipos_atividades as $tipo): ?>
                                <option value="<?php echo $tipo->idTipo; ?>">
                                    <?php echo $tipo->nome; ?>
                                </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="1">Trabalho Técnico</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="atividades-form-group">
                        <label class="atividades-form-label" for="descricao_registro">
                            <?= svg_icon('comment', 16, 16) ?> Descrição da Atividade
                        </label>
                        <textarea name="descricao" id="descricao_registro" class="atividades-form-textarea" rows="2" placeholder="Descreva o trabalho que será realizado..."></textarea>
                    </div>

                    <div class="atividades-form-group">
                        <label class="atividades-form-label" for="equipamento_registro">
                            <?= svg_icon('wrench', 16, 16) ?> Equipamento/Local
                        </label>
                        <input type="text" name="equipamento" id="equipamento_registro" class="atividades-form-input" placeholder="Ex: Rack principal, Câmera 1, Sala do servidor...">
                    </div>

                    <div class="atividades-form-group">
                        <label class="atividades-form-label">
                            <?= svg_icon('map', 16, 16) ?> Localização GPS
                        </label>
                        <button type="button" class="atividades-btn atividades-btn-secondary" onclick="obterLocalizacaoRegistro()">
                            <?= svg_icon('map-pin', 16, 16) ?> Obter Localização
                        </button>
                        <div id="gps_info_registro" class="atividades-form-hint">
                            <?= svg_icon('info-circle', 14, 14) ?> Clique no botão acima para registrar sua localização.
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="atividades-btn-cancel" data-bs-dismiss="modal">
                        <?= svg_icon('x', 16, 16) ?> Cancelar
                    </button>
                    <button type="submit" class="atividades-btn-submit" id="btnIniciarRegistro">
                        <?= svg_icon('play', 16, 16) ?> INICIAR ATIVIDADE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function obterLocalizacaoRegistro() {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('registro_latitude').value = position.coords.latitude;
                document.getElementById('registro_longitude').value = position.coords.longitude;
                document.getElementById('gps_info_registro').innerHTML =
                    '<?= svg_icon("check-circle", 14, 14, "", "color:#27ae60;") ?> Localização obtida com sucesso!';
            },
            function(error) {
                document.getElementById('gps_info_registro').innerHTML =
                    '<?= svg_icon("x", 14, 14, "", "color:#e74c3c;") ?> Erro ao obter localização: ' + error.message;
            }
        );
    } else {
        document.getElementById('gps_info_registro').innerHTML =
            '<?= svg_icon("x", 14, 14, "", "color:#e74c3c;") ?> GPS não disponível no dispositivo.';
    }
}

function iniciarRegistroAtividade(event) {
    event.preventDefault();
    const form = document.getElementById('formIniciarRegistro');
    const formData = new FormData(form);
    const etapaId = formData.get('etapa_id');
    const tipoId = formData.get('tipo_id');

    if (!etapaId) {
        alert('Por favor, selecione uma etapa da obra.');
        document.getElementById('etapa_id_registro').focus();
        return false;
    }
    if (!tipoId) {
        alert('Por favor, selecione o tipo de atividade.');
        document.getElementById('tipo_id_registro').focus();
        return false;
    }

    document.getElementById('btnIniciarRegistro').disabled = true;
    document.getElementById('btnIniciarRegistro').innerHTML = 'Iniciando...';

    fetch('<?php echo site_url("atividades/checkin_obra"); ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#modalIniciarRegistro').modal('hide');
            alert('Atividade iniciada com sucesso! Hora Início registrada.');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro ao iniciar atividade'));
            document.getElementById('btnIniciarRegistro').disabled = false;
            document.getElementById('btnIniciarRegistro').innerHTML = '<?= svg_icon("play", 16, 16) ?> INICIAR ATIVIDADE';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao iniciar atividade. Tente novamente.');
        document.getElementById('btnIniciarRegistro').disabled = false;
        document.getElementById('btnIniciarRegistro').innerHTML = '<?= svg_icon("play", 16, 16) ?> INICIAR ATIVIDADE';
    });
    return false;
}
</script>

<!-- Modal Adicionar Atividade -->
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
<div id="modalAdicionar" class="modal fade modal-atividades" tabindex="-1" role="dialog" aria-labelledby="modalAtividadeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="modalAtividadeLabel"><?= svg_icon('plus-circle', 20, 20) ?> Nova Atividade - Wizard</h3>
            </div>

            <form id="formAdicionarAtividade" onsubmit="return salvarAtividadeWizard(event)">
                <div class="modal-body">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
                    <input type="hidden" name="latitude" id="nova_latitude">
                    <input type="hidden" name="longitude" id="nova_longitude">

                    <div class="obras-alert-info">
                        <strong>Modo Wizard:</strong> Esta atividade será criada no sistema de atendimento técnico.
                    </div>

                    <div class="atividades-form-group">
                        <label class="atividades-form-label" for="etapa_id_nova">
                            <?= svg_icon('list-check', 16, 16) ?> Etapa da Obra <span class="required">*</span>
                        </label>
                        <select name="etapa_id" id="etapa_id_nova" class="atividades-form-select" required>
                            <option value="">Selecione uma etapa...</option>
                            <?php if (isset($etapas) && !empty($etapas)): ?>
                                <?php foreach ($etapas as $e): ?>
                                <option value="<?php echo $e->id; ?>">
                                    #<?php echo $e->numero_etapa ?? 'N/A'; ?> - <?php echo $e->nome; ?>
                                    <?php if (isset($e->progresso_real) && $e->progresso_real > 0): ?>
                                        (<?php echo $e->progresso_real; ?>%)
                                    <?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhuma etapa cadastrada</option>
                            <?php endif; ?>
                        </select>
                        <div class="atividades-form-hint">
                            <?= svg_icon('info-circle', 14, 14) ?> Selecione a etapa em que a atividade será executada.
                        </div>
                    </div>

                    <?php if (!empty($tipos_atividades)): ?>
                    <div class="atividades-form-group">
                        <label class="atividades-form-label" for="tipo_id_nova">
                            <?= svg_icon('wrench', 16, 16) ?> Tipo de Atividade <span class="required">*</span>
                        </label>
                        <select name="tipo_id" id="tipo_id_nova" class="atividades-form-select" required>
                            <option value="">Selecione o tipo...</option>
                            <?php foreach ($tipos_atividades as $tipo): ?>
                            <option value="<?php echo $tipo->idTipo; ?>" data-categoria="<?php echo $tipo->categoria ?? 'geral'; ?>">
                                <?php echo $tipo->nome; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="tipo_id" value="1">
                    <?php endif; ?>

                    <div class="atividades-form-group">
                        <label class="atividades-form-label" for="titulo_nova">
                            <?= svg_icon('tag', 16, 16) ?> Título da Atividade <span class="required">*</span>
                        </label>
                        <input type="text" name="titulo" id="titulo_nova" class="atividades-form-input" placeholder="Ex: Instalação elétrica..." required>
                    </div>

                    <div class="atividades-form-group">
                        <label class="atividades-form-label" for="descricao_nova">
                            <?= svg_icon('comment', 16, 16) ?> Descrição da Atividade
                        </label>
                        <textarea name="descricao" id="descricao_nova" class="atividades-form-textarea" rows="2" placeholder="Descreva o trabalho que será realizado..."></textarea>
                    </div>

                    <div class="atividades-form-group">
                        <label class="atividades-form-label" for="equipamento_nova">
                            <?= svg_icon('wrench', 16, 16) ?> Equipamento/Local
                        </label>
                        <input type="text" name="equipamento" id="equipamento_nova" class="atividades-form-input" placeholder="Ex: Rack principal, Câmera 1, Sala do servidor...">
                    </div>

                    <div class="atividades-form-group">
                        <label class="atividades-form-label">
                            <?= svg_icon('map', 16, 16) ?> Localização GPS
                        </label>
                        <button type="button" class="atividades-btn atividades-btn-secondary" onclick="obterLocalizacaoNovaAtividade()">
                            <?= svg_icon('map', 16, 16) ?> Obter Localização Atual
                        </button>
                        <div id="gps_info_nova" class="atividades-form-hint">
                            <?= svg_icon('info-circle', 14, 14) ?> Clique para registrar a localização.
                        </div>
                    </div>

                    <hr class="obras-modal-divider">

                    <div class="atividades-form-row">
                        <div class="atividades-form-group">
                            <label class="atividades-form-label" for="tecnico_id_nova">
                                <?= svg_icon('user', 16, 16) ?> Técnico Responsável
                            </label>
                            <select name="tecnico_id" id="tecnico_id_nova" class="atividades-form-select">
                                <option value="">Selecione um técnico...</option>
                                <?php if (!empty($tecnicos)): ?>
                                    <?php foreach ($tecnicos as $t): ?>
                                    <option value="<?php echo $t->idUsuarios; ?>"><?php echo $t->nome; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="atividades-form-group">
                            <label class="atividades-form-checkbox">
                                <input type="checkbox" name="visivel_cliente" value="1" checked class="form-check-input-custom">
                                <div>
                                    <div class="atividades-form-checkbox-label">Visível ao cliente</div>
                                    <div class="atividades-form-checkbox-hint">O cliente poderá ver esta atividade</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="atividades-btn-cancel" data-bs-dismiss="modal">
                        <?= svg_icon('x', 16, 16) ?> Cancelar
                    </button>
                    <button type="submit" class="atividades-btn-submit" id="btnSalvarAtividade">
                        <?= svg_icon('save', 16, 16) ?> CRIAR ATIVIDADE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function obterLocalizacaoNovaAtividade() {
    if ('geolocation' in navigator) {
        document.getElementById('gps_info_nova').innerHTML = 'Obtendo localização...';
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('nova_latitude').value = position.coords.latitude;
                document.getElementById('nova_longitude').value = position.coords.longitude;
                document.getElementById('gps_info_nova').innerHTML = '<?= svg_icon("check-circle", 14, 14, "", "color:#27ae60;") ?> Localização: ' + position.coords.latitude.toFixed(6) + ', ' + position.coords.longitude.toFixed(6);
            },
            function(error) {
                document.getElementById('gps_info_nova').innerHTML = '<?= svg_icon("x", 14, 14, "", "color:#e74c3c;") ?> Erro: ' + error.message;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    } else {
        document.getElementById('gps_info_nova').innerHTML = '<?= svg_icon("x", 14, 14, "", "color:#e74c3c;") ?> GPS não disponível.';
    }
}

function salvarAtividadeWizard(event) {
    event.preventDefault();
    const form = document.getElementById('formAdicionarAtividade');
    const formData = new FormData(form);
    const etapaId = formData.get('etapa_id');
    const tipoId = formData.get('tipo_id');
    const titulo = formData.get('titulo');

    if (!etapaId) { alert('Por favor, selecione uma etapa da obra.'); document.getElementById('etapa_id_nova').focus(); return false; }
    if (!tipoId) { alert('Por favor, selecione o tipo de atividade.'); document.getElementById('tipo_id_nova').focus(); return false; }
    if (!titulo || titulo.trim() === '') { alert('Por favor, informe o título da atividade.'); document.getElementById('titulo_nova').focus(); return false; }

    const btn = document.getElementById('btnSalvarAtividade');
    btn.disabled = true;
    btn.innerHTML = 'Salvando...';

    fetch('<?php echo site_url("atividades/checkin_obra"); ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#modalAdicionar').modal('hide');
            alert('Atividade criada com sucesso no sistema de atendimento!');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro ao criar atividade'));
            btn.disabled = false;
            btn.innerHTML = '<?= svg_icon("save", 16, 16) ?> CRIAR ATIVIDADE';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao criar atividade. Tente novamente.');
        btn.disabled = false;
        btn.innerHTML = '<?= svg_icon("save", 16, 16) ?> CRIAR ATIVIDADE';
    });
    return false;
}

function filtrarAtividades() {
    const search = document.getElementById('searchAtividade').value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    const status = document.getElementById('filterStatus').value;
    const tipo = document.getElementById('filterTipo').value;
    const cards = document.querySelectorAll('.atividade-card');
    cards.forEach(card => {
        const titulo = card.getAttribute('data-titulo');
        const cardStatus = card.getAttribute('data-status');
        const cardTipo = card.getAttribute('data-tipo');
        const matchSearch = !search || titulo.includes(search);
        const matchStatus = !status || cardStatus === status;
        const matchTipo = !tipo || cardTipo === tipo;
        card.style.display = matchSearch && matchStatus && matchTipo ? 'block' : 'none';
    });
}

$('#modalAdicionar').on('shown.bs.modal', function () { $('#titulo').focus(); });

$(document).ready(function() {
    $('.atividade-card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });
});

let refreshInterval;
function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        if (!document.hidden) { location.reload(); }
    }, 10000);
}
function stopAutoRefresh() { clearInterval(refreshInterval); }

$(document).ready(function() { startAutoRefresh(); });
$('#modalAdicionar').on('shown.bs.modal', function () { stopAutoRefresh(); });
$('#modalAdicionar').on('hidden.bs.modal', function () { startAutoRefresh(); });
</script>