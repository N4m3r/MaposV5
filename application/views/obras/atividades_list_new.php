<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Grid de Atividades -->
<div class="atividades-grid" id="atividadesGrid">
    <?php
    // Mesclar atividades do sistema antigo e novo
    $todas_atividades = [];

    // Atividades do sistema antigo (agendadas)
    if (!empty($atividades)) {
        foreach ($atividades as $ativ) {
            $todas_atividades[] = [
                'id' => $ativ->id ?? $ativ->idAtividade ?? 0,
                'titulo' => $ativ->titulo ?? 'Atividade',
                'descricao' => $ativ->descricao ?? '',
                'status' => $ativ->status ?? 'agendada',
                'tipo' => $ativ->tipo ?? 'trabalho',
                'data' => $ativ->data_atividade ?? $ativ->data_criacao ?? date('Y-m-d'),
                'tecnico' => $ativ->nome_tecnico ?? $ativ->tecnico_nome ?? 'Não atribuído',
                'etapa' => $ativ->nome_etapa ?? $ativ->etapa_nome ?? 'Geral',
                'progresso' => $ativ->percentual_concluido ?? 0,
                'sistema' => 'antigo'
            ];
        }
    }

    // Atividades do novo sistema (registradas com Hora Início/Fim)
    if (!empty($atividades_registradas)) {
        foreach ($atividades_registradas as $ativ) {
            $status = 'agendada';
            if (!empty($ativ->hora_fim)) {
                $status = 'concluida';
            } elseif (!empty($ativ->hora_inicio)) {
                $status = 'iniciada';
            }

            $todas_atividades[] = [
                'id' => $ativ->idAtividade ?? 0,
                'titulo' => $ativ->titulo ?? $ativ->tipo_atividade ?? 'Atividade Técnica',
                'descricao' => $ativ->descricao ?? '',
                'status' => $ativ->status ?? $status,
                'tipo' => $ativ->categoria ?? 'trabalho',
                'data' => date('Y-m-d', strtotime($ativ->hora_inicio ?? 'now')),
                'tecnico' => $ativ->nome_tecnico ?? 'Não atribuído',
                'etapa' => $ativ->etapa_nome ?? 'Geral',
                'progresso' => ($ativ->status == 'finalizada' && $ativ->concluida) ? 100 : 0,
                'hora_inicio' => $ativ->hora_inicio ?? null,
                'hora_fim' => $ativ->hora_fim ?? null,
                'duracao' => $ativ->duracao_minutos ?? null,
                'sistema' => 'novo'
            ];
        }
    }

    // Ordenar por data (mais recente primeiro)
    usort($todas_atividades, function($a, $b) {
        return strtotime($b['data']) - strtotime($a['data']);
    });

    if (!empty($todas_atividades)):
        foreach ($todas_atividades as $atividade):
            $status_class = $atividade['status'];
            $status_label = ucfirst($atividade['status']);
            $atividade_id = (int)$atividade['id'];
            $atividade_sistema = htmlspecialchars($atividade['sistema'] ?? 'antigo');
    ?>
    <div class="atividade-card <?php echo $status_class; ?>"
         data-titulo="<?php echo strtolower(str_replace('"', '', $atividade['titulo'])); ?>"
         data-status="<?php echo $atividade['status']; ?>"
         data-tipo="<?php echo $atividade['tipo']; ?>"
         data-sistema="<?php echo $atividade_sistema; ?>"
         onclick="abrirModalAtividade(<?php echo $atividade_id; ?>, '<?php echo $atividade_sistema; ?>')"
         style="cursor: pointer;">

        <div class="atividade-card-header">
            <div class="atividade-card-title-section">
                <div class="atividade-card-date">
                    <i class="bx bx-calendar"></i>
                    <?php echo date('d/m/Y', strtotime($atividade['data'])); ?>
                    <?php if ($atividade['sistema'] == 'novo' && !empty($atividade['hora_inicio'])): ?>
                        <span style="margin-left: 5px; color: #667eea;">
                            <i class="bx bx-time"></i>
                            <?php echo date('H:i', strtotime($atividade['hora_inicio'])); ?>
                            <?php if (!empty($atividade['hora_fim'])): ?>
                                - <?php echo date('H:i', strtotime($atividade['hora_fim'])); ?>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h3 class="atividade-card-title"><?php echo htmlspecialchars($atividade['titulo']); ?></h3>
            </div>
            <span class="atividade-status-badge <?php echo $status_class; ?>">
                <?php echo $status_label; ?>
            </span>
        </div>

        <div class="atividade-card-body">
            <?php if (!empty($atividade['descricao'])): ?>
            <div class="atividade-card-desc">
                <?php echo htmlspecialchars($atividade['descricao']); ?>
            </div>
            <?php endif; ?>

            <div class="atividade-card-meta">
                <div class="atividade-meta-item">
                    <i class="bx bx-user"></i>
                    <?php echo htmlspecialchars($atividade['tecnico']); ?>
                </div>
                <div class="atividade-meta-item">
                    <i class="bx bx-layer"></i>
                    <?php echo htmlspecialchars($atividade['etapa']); ?>
                </div>
                <?php if ($atividade['tipo']): ?>
                <div class="atividade-meta-item tipo-<?php echo $atividade['tipo']; ?>">
                    <i class="bx bx-wrench"></i>
                    <?php echo ucfirst($atividade['tipo']); ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($atividade['duracao'])): ?>
                <div class="atividade-meta-item">
                    <i class="bx bx-time"></i>
                    <?php
                    $horas = floor($atividade['duracao'] / 60);
                    $minutos = $atividade['duracao'] % 60;
                    echo $horas . 'h ' . $minutos . 'min';
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="atividade-card-footer">
            <div class="atividade-progress-section">
                <div class="atividade-progress-header">
                    <span class="atividade-progress-label">Progresso</span>
                    <span class="atividade-progress-value"><?php echo $atividade['progresso']; ?>%</span>
                </div>
                <div class="atividade-progress-bar">
                    <div class="atividade-progress-fill" style="width: <?php echo $atividade['progresso']; ?>%; background: <?php echo $atividade['progresso'] >= 100 ? '#27ae60' : ($atividade['progresso'] > 50 ? '#3498db' : '#f39c12'); ?>;"></div>
                </div>
            </div>

            <div class="atividade-card-actions" onclick="event.stopPropagation();">
                <a href="javascript:void(0)" class="atividade-card-btn atividade-card-btn-view" title="Ver detalhes"
                   onclick="abrirModalAtividade(<?php echo $atividade_id; ?>, '<?php echo $atividade_sistema; ?>')">
                    <i class="bx bx-eye"></i>
                </a>
                <?php if ($this->session->userdata('permissao') == 1): ?>
                <a href="javascript:void(0)" class="atividade-card-btn atividade-card-btn-delete" title="Excluir"
                   onclick="event.stopPropagation(); excluirAtividade(<?php echo $atividade_id; ?>, '<?php echo $atividade_sistema; ?>')">
                    <i class="bx bx-trash"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($atividade['sistema'] == 'novo'): ?>
        <div class="atividade-visibilidade visivel" title="Sistema Novo - Registro de Tempo">
            <i class="bx bx-timer"></i>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

<?php else: ?>

    <!-- Empty State -->
    <div class="atividades-empty">
        <div class="atividades-empty-icon">
            <i class="bx bx-calendar-x"></i>
        </div>
        <h3>Nenhuma atividade encontrada</h3>
        <p>Esta obra ainda não possui atividades registradas.</p>
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
        <button onclick="$('#modalAdicionar').modal('show')" class="atividades-btn atividades-btn-primary">
            <i class="bx bx-plus"></i> Adicionar Primeira Atividade
        </button>
        <?php endif; ?>
    </div>

<?php endif; ?>
</div>

<!-- Modal Visualizar/Editar Atividade -->
<div id="modalVerAtividade" class="modal hide fade modal-atividades" tabindex="-1" role="dialog" aria-labelledby="modalVerLabel" aria-hidden="true" style="width: 800px; margin-left: -400px;">
    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.8;">&times;</button>
        <h3 id="modalVerLabel"><i class="bx bx-calendar-check"></i> Detalhes da Atividade</h3>
    </div>

    <div class="modal-body" id="modalVerBody" style="max-height: 500px; overflow-y: auto;">
        <div style="text-align: center; padding: 40px;">
            <i class="bx bx-loader-alt bx-spin" style="font-size: 40px; color: #667eea;"></i>
            <p style="margin-top: 15px; color: #666;">Carregando...</p>
        </div>
    </div>

    <div class="modal-footer" id="modalVerFooter">
        <button type="button" class="atividades-btn-cancel" data-dismiss="modal">
            <i class="bx bx-x"></i> Fechar
        </button>
        <button type="button" class="atividades-btn-submit" id="btnEditarAtividade" onclick="toggleEdicao()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="bx bx-edit"></i> Editar
        </button>
        <button type="button" class="atividades-btn-submit" id="btnSalvarAtividade" onclick="salvarAtividade()" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); display: none;">
            <i class="bx bx-save"></i> Salvar
        </button>
    </div>
</div>

<script type="text/javascript">
// Variáveis globais
var atividadeAtual = null;
var modoEdicao = false;

function abrirModalAtividade(id, sistema) {
    atividadeAtual = { id: id, sistema: sistema };
    modoEdicao = false;

    document.getElementById('modalVerBody').innerHTML = '<div style="text-align: center; padding: 40px;"><i class="bx bx-loader-alt bx-spin" style="font-size: 40px; color: #667eea;"></i><p style="margin-top: 15px; color: #666;">Carregando...</p></div>';
    document.getElementById('btnEditarAtividade').style.display = 'inline-block';
    document.getElementById('btnSalvarAtividade').style.display = 'none';

    jQuery('#modalVerAtividade').modal('show');

    if (sistema === 'novo') {
        jQuery.ajax({
            url: '<?php echo site_url("atividades/detalhes/"); ?>' + id,
            type: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(data) {
                if (data.success) {
                    renderizarAtividadeNovo(data.atividade);
                } else {
                    document.getElementById('modalVerBody').innerHTML = '<div style="text-align: center; padding: 40px; color: #dc3545;"><i class="bx bx-error-circle" style="font-size: 40px;"></i><p>Erro ao carregar atividade</p></div>';
                }
            },
            error: function() {
                document.getElementById('modalVerBody').innerHTML = '<div style="text-align: center; padding: 40px; color: #dc3545;"><i class="bx bx-error-circle" style="font-size: 40px;"></i><p>Erro ao carregar atividade</p></div>';
            }
        });
    } else {
        jQuery.ajax({
            url: '<?php echo site_url("obras/api_getAtividade/"); ?>' + id,
            type: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(data) {
                if (data.success) {
                    atividadeAtual.dados = data.atividade;
                    atividadeAtual.execucao = data.execucao_real;
                    renderizarAtividadeAntigo(data.atividade, data.execucao_real);
                } else {
                    document.getElementById('modalVerBody').innerHTML = '<div style="text-align: center; padding: 40px; color: #dc3545;"><i class="bx bx-error-circle" style="font-size: 40px;"></i><p>' + (data.message || 'Erro ao carregar') + '</p></div>';
                }
            },
            error: function() {
                document.getElementById('modalVerBody').innerHTML = '<div style="text-align: center; padding: 40px; color: #dc3545;"><i class="bx bx-error-circle" style="font-size: 40px;"></i><p>Erro ao carregar atividade</p></div>';
            }
        });
    }
}

function renderizarAtividadeAntigo(atividade, execucao) {
    var html = '<div id="atividadeView">';

    var statusClass = atividade.status || 'agendada';
    var statusLabel = statusClass.charAt(0).toUpperCase() + statusClass.slice(1);

    html += '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px;">';
    html += '<div><strong>Status:</strong> <span class="label label-' + (statusClass === 'concluida' ? 'success' : (statusClass === 'iniciada' ? 'info' : 'default')) + '">' + statusLabel + '</span></div>';
    html += '<div style="color: #666; font-size: 13px;">ID: #' + atividade.id + '</div>';
    html += '</div>';

    html += '<div class="atividades-form-group">';
    html += '<label class="atividades-form-label"><i class="bx bx-tag"></i> Título</label>';
    html += '<div class="view-field" style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0;">' + (atividade.titulo || '-') + '</div>';
    html += '<input type="text" name="titulo" class="atividades-form-input edit-field" value="' + (atividade.titulo || '').replace(/"/g, '&quot;') + '" style="display: none;">';
    html += '</div>';

    html += '<div class="atividades-form-group">';
    html += '<label class="atividades-form-label"><i class="bx bx-detail"></i> Descrição</label>';
    html += '<div class="view-field" style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0; min-height: 60px;">' + (atividade.descricao || '-') + '</div>';
    html += '<textarea name="descricao" class="atividades-form-textarea edit-field" rows="3" style="display: none;">' + (atividade.descricao || '') + '</textarea>';
    html += '</div>';

    html += '<div class="atividades-form-row">';
    html += '<div class="atividades-form-group">';
    html += '<label class="atividades-form-label"><i class="bx bx-calendar"></i> Data</label>';
    html += '<div class="view-field" style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0;">' + (atividade.data_atividade ? formatarData(atividade.data_atividade) : '-') + '</div>';
    html += '<input type="date" name="data_atividade" class="atividades-form-input edit-field" value="' + (atividade.data_atividade || '') + '" style="display: none;">';
    html += '</div>';

    html += '<div class="atividades-form-group">';
    html += '<label class="atividades-form-label"><i class="bx bx-wrench"></i> Tipo</label>';
    html += '<div class="view-field" style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0;">' + (atividade.tipo ? atividade.tipo.charAt(0).toUpperCase() + atividade.tipo.slice(1) : '-') + '</div>';
    html += '<select name="tipo" class="atividades-form-select edit-field" style="display: none;">';
    html += '<option value="trabalho" ' + (atividade.tipo === 'trabalho' ? 'selected' : '') + '>Trabalho</option>';
    html += '<option value="visita" ' + (atividade.tipo === 'visita' ? 'selected' : '') + '>Visita Técnica</option>';
    html += '<option value="manutencao" ' + (atividade.tipo === 'manutencao' ? 'selected' : '') + '>Manutenção</option>';
    html += '<option value="impedimento" ' + (atividade.tipo === 'impedimento' ? 'selected' : '') + '>Impedimento</option>';
    html += '<option value="outro" ' + (atividade.tipo === 'outro' ? 'selected' : '') + '>Outro</option>';
    html += '</select>';
    html += '</div>';
    html += '</div>';

    if (execucao && execucao.idAtividade) {
        html += '<hr style="margin: 25px 0; border-color: #e0e0e0;">';
        html += '<div style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); padding: 20px; border-radius: 10px; border-left: 4px solid #667eea;">';
        html += '<h4 style="margin: 0 0 15px 0; color: #667eea;"><i class="bx bx-timer"></i> Execução Real (Wizard)</h4>';

        html += '<div class="atividades-form-row">';
        html += '<div class="atividades-form-group">';
        html += '<label class="atividades-form-label">Hora Início</label>';
        html += '<div style="padding: 12px; background: white; border-radius: 8px; border: 1px solid #e0e0e0;">' + (execucao.hora_inicio ? formatarDataHora(execucao.hora_inicio) : '-') + '</div>';
        html += '</div>';

        html += '<div class="atividades-form-group">';
        html += '<label class="atividades-form-label">Hora Fim</label>';
        html += '<div style="padding: 12px; background: white; border-radius: 8px; border: 1px solid #e0e0e0;">' + (execucao.hora_fim ? formatarDataHora(execucao.hora_fim) : '-') + '</div>';
        html += '</div>';
        html += '</div>';

        if (execucao.duracao_minutos) {
            var horas = Math.floor(execucao.duracao_minutos / 60);
            var minutos = execucao.duracao_minutos % 60;
            html += '<div style="margin-top: 15px; padding: 15px; background: white; border-radius: 8px; text-align: center;">';
            html += '<strong style="color: #667eea; font-size: 18px;"><i class="bx bx-time"></i> Duração: ' + horas + 'h ' + minutos + 'min</strong>';
            html += '</div>';
        }

        if (execucao.observacoes) {
            html += '<div style="margin-top: 15px;">';
            html += '<label class="atividades-form-label">Observações da Execução</label>';
            html += '<div style="padding: 12px; background: white; border-radius: 8px; border: 1px solid #e0e0e0; white-space: pre-wrap;">' + execucao.observacoes + '</div>';
            html += '</div>';
        }

        html += '</div>';
    }

    html += '</div>';
    document.getElementById('modalVerBody').innerHTML = html;
}

function renderizarAtividadeNovo(atividade) {
    var html = '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px;">';
    html += '<div><strong>Status:</strong> <span class="label label-info">' + (atividade.status ? atividade.status.toUpperCase() : 'N/A') + '</span></div>';
    html += '<div style="color: #666; font-size: 13px;">Sistema Novo (Wizard) - ID: #' + atividade.idAtividade + '</div>';
    html += '</div>';

    html += '<div class="atividades-form-group">';
    html += '<label class="atividades-form-label"><i class="bx bx-tag"></i> Tipo de Atividade</label>';
    html += '<div style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0;">' + (atividade.tipo_atividade || '-') + '</div>';
    html += '</div>';

    html += '<div class="atividades-form-group">';
    html += '<label class="atividades-form-label"><i class="bx bx-detail"></i> Descrição</label>';
    html += '<div style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0; min-height: 60px;">' + (atividade.descricao || '-') + '</div>';
    html += '</div>';

    if (atividade.hora_inicio) {
        html += '<div class="atividades-form-row">';
        html += '<div class="atividades-form-group">';
        html += '<label class="atividades-form-label"><i class="bx bx-time"></i> Hora Início</label>';
        html += '<div style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0;">' + formatarDataHora(atividade.hora_inicio) + '</div>';
        html += '</div>';

        html += '<div class="atividades-form-group">';
        html += '<label class="atividades-form-label"><i class="bx bx-time"></i> Hora Fim</label>';
        html += '<div style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0;">' + (atividade.hora_fim ? formatarDataHora(atividade.hora_fim) : 'Em andamento...') + '</div>';
        html += '</div>';
        html += '</div>';

        if (atividade.duracao_minutos) {
            var horas = Math.floor(atividade.duracao_minutos / 60);
            var minutos = atividade.duracao_minutos % 60;
            html += '<div style="margin-top: 15px; padding: 15px; background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border-radius: 8px; text-align: center;">';
            html += '<strong style="color: #667eea; font-size: 18px;"><i class="bx bx-timer"></i> Duração Total: ' + horas + 'h ' + minutos + 'min</strong>';
            html += '</div>';
        }
    }

    if (atividade.nome_tecnico) {
        html += '<div class="atividades-form-group" style="margin-top: 15px;">';
        html += '<label class="atividades-form-label"><i class="bx bx-user"></i> Técnico</label>';
        html += '<div style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0;">' + atividade.nome_tecnico + '</div>';
        html += '</div>';
    }

    if (atividade.etapa_nome) {
        html += '<div class="atividades-form-group">';
        html += '<label class="atividades-form-label"><i class="bx bx-layer"></i> Etapa</label>';
        html += '<div style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0;">' + atividade.etapa_nome + '</div>';
        html += '</div>';
    }

    if (atividade.observacoes) {
        html += '<div class="atividades-form-group">';
        html += '<label class="atividades-form-label"><i class="bx bx-note"></i> Observações</label>';
        html += '<div style="padding: 12px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0; white-space: pre-wrap;">' + atividade.observacoes + '</div>';
        html += '</div>';
    }

    document.getElementById('modalVerBody').innerHTML = html;
    document.getElementById('btnEditarAtividade').style.display = 'none';
    document.getElementById('btnSalvarAtividade').style.display = 'none';
}

function toggleEdicao() {
    modoEdicao = !modoEdicao;

    if (modoEdicao) {
        jQuery('.view-field').hide();
        jQuery('.edit-field').show();
        document.getElementById('btnEditarAtividade').style.display = 'none';
        document.getElementById('btnSalvarAtividade').style.display = 'inline-block';
    } else {
        jQuery('.view-field').show();
        jQuery('.edit-field').hide();
        document.getElementById('btnEditarAtividade').style.display = 'inline-block';
        document.getElementById('btnSalvarAtividade').style.display = 'none';
    }
}

function salvarAtividade() {
    if (!atividadeAtual || !atividadeAtual.id) {
        alert('Erro: Nenhuma atividade selecionada');
        return;
    }

    var tituloEl = document.querySelector('input[name="titulo"]');
    var descricaoEl = document.querySelector('textarea[name="descricao"]');
    var dataEl = document.querySelector('input[name="data_atividade"]');
    var tipoEl = document.querySelector('select[name="tipo"]');

    var dados = {
        id: atividadeAtual.id,
        titulo: tituloEl ? tituloEl.value : '',
        descricao: descricaoEl ? descricaoEl.value : '',
        data_atividade: dataEl ? dataEl.value : '',
        tipo: tipoEl ? tipoEl.value : ''
    };

    var btn = document.getElementById('btnSalvarAtividade');
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Salvando...';

    jQuery.ajax({
        url: '<?php echo site_url("obras/api_salvarAtividade"); ?>',
        type: 'POST',
        dataType: 'json',
        data: dados,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
            if (data.success) {
                alert('Atividade atualizada com sucesso!');
                jQuery('#modalVerAtividade').modal('hide');
                window.location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Não foi possível salvar'));
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-save"></i> Salvar';
            }
        },
        error: function() {
            alert('Erro ao salvar atividade');
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-save"></i> Salvar';
        }
    });
}

function formatarData(dataStr) {
    if (!dataStr) return '-';
    var parts = dataStr.split('-');
    if (parts.length !== 3) return dataStr;
    return parts[2] + '/' + parts[1] + '/' + parts[0];
}

function formatarDataHora(dataHoraStr) {
    if (!dataHoraStr) return '-';
    var data = new Date(dataHoraStr);
    if (isNaN(data.getTime())) return dataHoraStr;
    return data.toLocaleString('pt-BR');
}

function excluirAtividade(id, sistema) {
    if (!confirm('Tem certeza que deseja excluir esta atividade?')) {
        return;
    }

    var url = sistema === 'novo'
        ? '<?php echo site_url('atividades/excluir/'); ?>' + id
        : '<?php echo site_url('obras/excluirAtividade/'); ?>' + id;

    jQuery.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Não foi possível excluir'));
            }
        },
        error: function() {
            window.location.reload();
        }
    });
}
</script>
