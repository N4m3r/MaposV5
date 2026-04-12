<?php
/**
 * View: Checklist Técnico
 * Lista de verificação para OS
 */
?>

<style>
.checklist-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    transition: all 0.3s;
}
.checklist-item:hover {
    background: #f9f9f9;
}
.checklist-item.ok {
    background: #d4edda;
}
.checklist-item.com_problema {
    background: #f8d7da;
}
.checklist-item.nao_aplicavel {
    background: #e2e3e5;
    opacity: 0.7;
}
.status-btn {
    margin: 0 3px;
}
.status-btn.active {
    font-weight: bold;
    transform: scale(1.1);
}
.progress-container {
    margin: 20px 0;
}
.progress {
    height: 25px;
}
.timeline-item {
    border-left: 3px solid #007bff;
    padding-left: 15px;
    margin-bottom: 15px;
    position: relative;
}
.timeline-item::before {
    content: '';
    width: 10px;
    height: 10px;
    background: #007bff;
    border-radius: 50%;
    position: absolute;
    left: -7px;
    top: 5px;
}
</style>

<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('tecnico') ?>">Área do Técnico</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('tecnico/os') ?>">Minhas OS</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('tecnico/visualizar/' . $os->idOs) ?>">OS #<?= sprintf('%04d', $os->idOs) ?></a> <span class="divider">/</span></li>
            <li class="active">Checklist</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <!-- Progresso -->
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-tasks"></i></span>
                <h5>Progresso do Checklist</h5>
                <div class="buttons">
                    <a href="<?= site_url('tecnico/visualizar/' . $os->idOs) ?>" class="btn btn-small">
                        <i class="fas fa-arrow-left"></i> Voltar para OS
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <div class="progress-container">
                    <div class="progress progress-striped">
                        <div class="bar bar-success" style="width: <?= $estatisticas['percentual_concluido'] ?>%;">
                            <?= $estatisticas['percentual_concluido'] ?>%
                        </div>
                    </div>
                </div>
                <div class="row-fluid text-center">
                    <div class="span3">
                        <span class="label label-warning"><?= $estatisticas['pendente'] ?></span>
                        <small>Pendentes</small>
                    </div>
                    <div class="span3">
                        <span class="label label-success"><?= $estatisticas['ok'] ?></span>
                        <small>OK</small>
                    </div>
                    <div class="span3">
                        <span class="label label-info"><?= $estatisticas['nao_aplicavel'] ?></span>
                        <small>N/A</small>
                    </div>
                    <div class="span3">
                        <span class="label label-important"><?= $estatisticas['com_problema'] ?></span>
                        <small>Problemas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <!-- Checklist -->
    <div class="span8">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-clipboard-check"></i></span>
                <h5>Itens do Checklist</h5>
                <div class="buttons">
                    <button class="btn btn-small btn-success" onclick="adicionarItem()">
                        <i class="fas fa-plus"></i> Adicionar Item
                    </button>
                </div>
            </div>
            <div class="widget-content nopadding">
                <div id="checklist-container">
                    <?php foreach ($checklist as $item): ?>
                    <div class="checklist-item <?= $item->status ?>" data-item-id="<?= $item->id ?>">
                        <div class="row-fluid">
                            <div class="span6">
                                <strong><?= $item->descricao ?></strong>
                                <?php if ($item->observacao): ?>
                                    <br><small class="text-muted"><?= $item->observacao ?></small>
                                <?php endif; ?>
                                <?php if ($item->verificado_at): ?>
                                    <br><small class="text-info">
                                        <i class="fas fa-user"></i>
                                        <?= $this->session->userdata('nome') ?> em
                                        <?= date('d/m/Y H:i', strtotime($item->verificado_at)) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="span6 text-right">
                                <div class="btn-group">
                                    <button class="btn btn-small btn-success status-btn <?= $item->status == 'ok' ? 'active' : '' ?>"
                                            onclick="atualizarStatus(<?= $item->id ?>, 'ok')" title="OK">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-small btn-warning status-btn <?= $item->status == 'nao_aplicavel' ? 'active' : '' ?>"
                                            onclick="atualizarStatus(<?= $item->id ?>, 'nao_aplicavel')" title="Não Aplicável">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button class="btn btn-small btn-danger status-btn <?= $item->status == 'com_problema' ? 'active' : '' ?>"
                                            onclick="atualizarStatus(<?= $item->id ?>, 'com_problema')" title="Com Problema">
                                        <i class="fas fa-exclamation"></i>
                                    </button>
                                    <button class="btn btn-small status-btn <?= $item->status == 'pendente' ? 'active' : '' ?>"
                                            onclick="atualizarStatus(<?= $item->id ?>, 'pendente')" title="Pendente">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                </div>
                                <button class="btn btn-small btn-info" onclick="editarObservacao(<?= $item->id ?>, '<?= htmlspecialchars($item->observacao) ?>')">
                                    <i class="fas fa-comment"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Adicionar Item Form -->
                <div id="add-item-form" style="display: none; padding: 15px; border-top: 2px solid #ddd; background: #f5f5f5;">
                    <div class="row-fluid">
                        <div class="span10">
                            <input type="text" id="novo-item-descricao" class="span12" placeholder="Descrição do novo item...">
                        </div>
                        <div class="span2">
                            <button class="btn btn-small btn-success" onclick="salvarNovoItem()">
                                <i class="fas fa-save"></i>
                            </button>
                            <button class="btn btn-small" onclick="cancelarNovoItem()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="span4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-history"></i></span>
                <h5>Timeline</h5>
            </div>
            <div class="widget-content" id="timeline-container">
                <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Carregando...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Observação -->
<div id="modal-observacao" class="modal hide">
    <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button">×</button>
        <h3>Observação</h3>
    </div>
    <div class="modal-body">
        <input type="hidden" id="obs-item-id">
        <textarea id="obs-texto" class="span12" rows="3" placeholder="Digite uma observação..."></textarea>
    </div>
    <div class="modal-footer">
        <a data-dismiss="modal" class="btn" href="#">Cancelar</a>
        <button class="btn btn-primary" onclick="salvarObservacao()">Salvar</button>
    </div>
</div>

<script>
const os_id = <?= $os->idOs ?>;

// Atualizar status do item
function atualizarStatus(item_id, status) {
    $.ajax({
        url: '<?= site_url("tecnico/atualizar_checklist_item") ?>',
        type: 'POST',
        data: {
            item_id: item_id,
            status: status,
            os_id: os_id
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Erro: ' + response.message);
            }
        },
        error: function() {
            alert('Erro na comunicação');
        }
    });
}

// Editar observação
function editarObservacao(item_id, observacao) {
    $('#obs-item-id').val(item_id);
    $('#obs-texto').val(observacao);
    $('#modal-observacao').modal('show');
}

// Salvar observação
function salvarObservacao() {
    const item_id = $('#obs-item-id').val();
    const observacao = $('#obs-texto').val();
    const status = $('.checklist-item[data-item-id="' + item_id + '"]').hasClass('ok') ? 'ok' : 'pendente';

    $.ajax({
        url: '<?= site_url("tecnico/atualizar_checklist_item") ?>',
        type: 'POST',
        data: {
            item_id: item_id,
            status: status,
            observacao: observacao,
            os_id: os_id
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        }
    });
}

// Mostrar form para adicionar item
function adicionarItem() {
    $('#add-item-form').slideDown();
    $('#novo-item-descricao').focus();
}

// Cancelar novo item
function cancelarNovoItem() {
    $('#add-item-form').slideUp();
    $('#novo-item-descricao').val('');
}

// Salvar novo item
function salvarNovoItem() {
    const descricao = $('#novo-item-descricao').val();
    if (!descricao.trim()) {
        alert('Digite uma descrição');
        return;
    }

    $.ajax({
        url: '<?= site_url("tecnico/adicionar_checklist_item") ?>',
        type: 'POST',
        data: {
            os_id: os_id,
            descricao: descricao
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Erro: ' + response.message);
            }
        }
    });
}

// Carregar timeline
function carregarTimeline() {
    $.ajax({
        url: '<?= site_url("tecnico/timeline/") ?>' + os_id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = '';
                response.data.forEach(function(item) {
                    html += `
                        <div class="timeline-item">
                            <strong>${item.titulo}</strong>
                            <small class="text-muted pull-right">${moment(item.created_at).fromNow()}</small>
                            ${item.descricao ? '<br><small>' + item.descricao + '</small>' : ''}
                            ${item.usuario_nome ? '<br><small class="text-info"><i class="fas fa-user"></i> ' + item.usuario_nome + '</small>' : ''}
                        </div>
                    `;
                });
                $('#timeline-container').html(html);
            } else {
                $('#timeline-container').html('<p class="text-muted text-center">Nenhuma atividade registrada</p>');
            }
        },
        error: function() {
            $('#timeline-container').html('<p class="text-error text-center">Erro ao carregar timeline</p>');
        }
    });
}

// Carregar timeline ao iniciar
$(document).ready(function() {
    carregarTimeline();
});
</script>
