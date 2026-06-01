<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<div class="atividades-modern">
    <!-- Header -->
    <div class="atividades-header">
        <div class="atividades-header-content">
            <div class="atividades-header-left">
                <div class="atividades-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><i class="bx bx-arrow-back"></i> Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo $obra->nome; ?></a> &raquo;
                    <span>Atividades</span>
                </div>
                <h1><i class="bx bx-calendar"></i> Atividades da Obra</h1>
                <div class="atividades-subtitle">Gerencie as atividades e acompanhe o progresso do trabalho</div>
            </div>
            <div class="atividades-actions">
                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="atividades-btn atividades-btn-secondary">
                    <i class="bx bx-show"></i> Ver Obra
                </a>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <button onclick="$('#modalAdicionar').modal('show')" class="atividades-btn atividades-btn-primary">
                    <i class="bx bx-plus"></i> Nova Atividade
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mensagens Flash -->
    <?php if ($this->session->flashdata('success')): ?>
    <div style="background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="bx bx-check" style="font-size: 20px;"></i>
        <strong><?php echo $this->session->flashdata('success'); ?></strong>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="bx bx-x" style="font-size: 20px;"></i>
        <strong><?php echo $this->session->flashdata('error'); ?></strong>
    </div>
    <?php endif; ?>

    <!-- Stats -->
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
            <div class="atividades-stat-icon total"><i class="bx bx-list-check"></i></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $total; ?></div>
                <div class="atividades-stat-label">Total de Atividades</div>
            </div>
        </div>
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon hoje"><i class="bx bx-calendar"></i></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $hoje; ?></div>
                <div class="atividades-stat-label">Atividades Hoje</div>
            </div>
        </div>
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon agendadas"><i class="bx bx-time-five"></i></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $agendadas; ?></div>
                <div class="atividades-stat-label">Agendadas</div>
            </div>
        </div>
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon concluidas"><i class="bx bx-check"></i></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $concluidas; ?></div>
                <div class="atividades-stat-label">Concluídas</div>
            </div>
        </div>
    </div>


    <!-- Filtros -->
    <div class="atividades-filters">
        <i class="bx bx-search" style="font-size: 20px; color: #667eea;"></i>
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


    <!-- Grid de Atividades (Mesclado: Sistema Antigo + Wizard) -->
    <?php $this->load->view('obras/atividades_list_new'); ?>
</div>

<!-- Modal Iniciar Registro de Atividade (Hora Início/Fim) -->
<div id="modalIniciarRegistro" class="modal fade modal-atividades" tabindex="-1" role="dialog" aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.8;">&times;</button>
        <h3 id="modalRegistroLabel"><i class="bx bx-timer"></i> Iniciar Atividade - Registro de Tempo</h3>
    </div>

    <form id="formIniciarRegistro" onsubmit="return iniciarRegistroAtividade(event)">
        <div class="modal-body">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <input type="hidden" name="latitude" id="registro_latitude">
            <input type="hidden" name="longitude" id="registro_longitude">

            <!-- Seleção de Etapa (OBRIGATÓRIA) -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="etapa_id_registro">
                    <i class="bx bx-layer"></i> Etapa da Obra <span style="color: #dc3545;">*</span>
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
                <div style="font-size: 12px; color: #666; margin-top: 5px;">
                    <i class="bx bx-info-circle"></i> Selecione a etapa em que você está trabalhando. Obrigatório.
                </div>
            </div>

            <!-- Tipo de Atividade -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="tipo_id_registro">
                    <i class="bx bx-wrench"></i> Tipo de Atividade <span style="color: #dc3545;">*</span>
                </label>
                <select name="tipo_id" id="tipo_id_registro" class="atividades-form-select" required>
                    <option value="">Selecione o tipo...</option>
                    <?php if (!empty($tipos_atividades)): ?>
                        <optgroup label="Rede Estruturada">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if ($tipo->categoria == 'rede'): ?>
                            <option value="<?php echo $tipo->idTipo; ?>" data-categoria="rede">
                                <i class="bx bx-network-chart"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="CFTV">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if ($tipo->categoria == 'cftv'): ?>
                            <option value="<?php echo $tipo->idTipo; ?>" data-categoria="cftv">
                                <i class="bx bx-camera"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="Infraestrutura">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if ($tipo->categoria == 'infra'): ?>
                            <option value="<?php echo $tipo->idTipo; ?>" data-categoria="infra">
                                <i class="bx bx-server"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="Segurança">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if ($tipo->categoria == 'seguranca'): ?>
                            <option value="<?php echo $tipo->idTipo; ?>" data-categoria="seguranca">
                                <i class="bx bx-shield"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="Geral">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if (!in_array($tipo->categoria, ['rede', 'cftv', 'infra', 'seguranca'])): ?>
                            <option value="<?php echo $tipo->idTipo; ?>">
                                <i class="bx bx-wrench"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                    <?php else: ?>
                        <option value="1">Trabalho Técnico</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Descrição -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="descricao_registro">
                    <i class="bx bx-detail"></i> Descrição da Atividade
                </label>
                <textarea name="descricao" id="descricao_registro" class="atividades-form-textarea" rows="2" placeholder="Descreva o trabalho que será realizado..."></textarea>
            </div>

            <!-- Equipamento/Local -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="equipamento_registro">
                    <i class="bx bx-wrench"></i> Equipamento/Local
                </label>
                <input type="text" name="equipamento" id="equipamento_registro" class="atividades-form-input" placeholder="Ex: Rack principal, Câmera 1, Sala do servidor...">
            </div>

            <!-- GPS -->
            <div class="atividades-form-group">
                <label class="atividades-form-label">
                    <i class="bx bx-map"></i> Localização GPS
                </label>
                <button type="button" class="btn btn-info" onclick="obterLocalizacaoRegistro()">
                    <i class="bx bx-map-pin"></i> Obter Localização
                </button>
                <div id="gps_info_registro" style="margin-top: 10px; font-size: 12px; color: #666;">
                    <i class="bx bx-info-circle"></i> Clique no botão acima para registrar sua localização.
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="atividades-btn-cancel" data-bs-dismiss="modal">
                <i class="bx bx-x"></i> Cancelar
            </button>
            <button type="submit" class="atividades-btn-submit" id="btnIniciarRegistro" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <i class="bx bx-play"></i> INICIAR ATIVIDADE
            </button>
        </div>
    </form>
</div>

<script>
// Função para obter localização GPS
function obterLocalizacaoRegistro() {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('registro_latitude').value = position.coords.latitude;
                document.getElementById('registro_longitude').value = position.coords.longitude;
                document.getElementById('gps_info_registro').innerHTML =
                    '<i class="bx bx-check-circle" style="color: #28a745;"></i> Localização obtida com sucesso!';
            },
            function(error) {
                document.getElementById('gps_info_registro').innerHTML =
                    '<i class="bx bx-error-circle" style="color: #dc3545;"></i> Erro ao obter localização: ' + error.message;
            }
        );
    } else {
        document.getElementById('gps_info_registro').innerHTML =
            '<i class="bx bx-error-circle" style="color: #dc3545;"></i> GPS não disponível no dispositivo.';
    }
}

// Função para iniciar o registro de atividade
function iniciarRegistroAtividade(event) {
    event.preventDefault();

    const form = document.getElementById('formIniciarRegistro');
    const formData = new FormData(form);

    // Validação
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

    // Desabilita botão para evitar duplo clique
    document.getElementById('btnIniciarRegistro').disabled = true;
    document.getElementById('btnIniciarRegistro').innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Iniciando...';

    // Envia requisição AJAX
    fetch('<?php echo site_url("atividades/checkin_obra"); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fecha modal e recarrega página
            $('#modalIniciarRegistro').modal('hide');
            alert('Atividade iniciada com sucesso! Hora Início registrada.');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro ao iniciar atividade'));
            document.getElementById('btnIniciarRegistro').disabled = false;
            document.getElementById('btnIniciarRegistro').innerHTML = '<i class="bx bx-play"></i> INICIAR ATIVIDADE';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao iniciar atividade. Tente novamente.');
        document.getElementById('btnIniciarRegistro').disabled = false;
        document.getElementById('btnIniciarRegistro').innerHTML = '<i class="bx bx-play"></i> INICIAR ATIVIDADE';
    });

    return false;
}
</script>

<!-- Modal Adicionar Atividade (Integrado com Wizard) -->
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
<div id="modalAdicionar" class="modal fade modal-atividades" tabindex="-1" role="dialog" aria-labelledby="modalAtividadeLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="modalAtividadeLabel"><i class="bx bx-plus-circle"></i> Nova Atividade - Wizard</h3>
    </div>

    <form id="formAdicionarAtividade" onsubmit="return salvarAtividadeWizard(event)">
        <div class="modal-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <input type="hidden" name="latitude" id="nova_latitude">
            <input type="hidden" name="longitude" id="nova_longitude">

            <!-- Alerta informativo -->
            <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 12px 15px; margin-bottom: 20px; border-radius: 0 8px 8px 0;">
                <i class="bx bx-info-circle" style="color: #2196f3;"></i>
                <strong>Modo Wizard:</strong> Esta atividade será criada no sistema de atendimento técnico.
            </div>

            <!-- Seleção de Etapa (OBRIGATÓRIA) -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="etapa_id_nova">
                    <i class="bx bx-list-check"></i> Etapa da Obra <span style="color: #dc3545;">*</span>
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
                    <i class="bx bx-info-circle"></i> Selecione a etapa em que a atividade será executada.
                </div>
            </div>

            <!-- Tipo de Atividade (do wizard) -->
            <?php if (!empty($tipos_atividades)): ?>
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="tipo_id_nova">
                    <i class="bx bx-wrench"></i> Tipo de Atividade <span style="color: #dc3545;">*</span>
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

            <!-- Título -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="titulo_nova">
                    <i class="bx bx-tag"></i> Título da Atividade <span style="color: #dc3545;">*</span>
                </label>
                <input type="text" name="titulo" id="titulo_nova" class="atividades-form-input" placeholder="Ex: Instalação elétrica..." required>
            </div>

            <!-- Descrição -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="descricao_nova">
                    <i class="bx bx-align-left"></i> Descrição da Atividade
                </label>
                <textarea name="descricao" id="descricao_nova" class="atividades-form-textarea" rows="2" placeholder="Descreva o trabalho que será realizado..."></textarea>
            </div>

            <!-- Equipamento/Local -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="equipamento_nova">
                    <i class="bx bx-wrench"></i> Equipamento/Local
                </label>
                <input type="text" name="equipamento" id="equipamento_nova" class="atividades-form-input" placeholder="Ex: Rack principal, Câmera 1, Sala do servidor...">
            </div>

            <!-- Localização GPS -->
            <div class="atividades-form-group">
                <label class="atividades-form-label">
                    <i class="bx bx-map"></i> Localização GPS
                </label>
                <button type="button" class="btn btn-info" onclick="obterLocalizacaoNovaAtividade()" style="margin-bottom: 10px;">
                    <i class="bx bx-map"></i> Obter Localização Atual
                </button>
                <div id="gps_info_nova" class="atividades-form-hint">
                    <i class="bx bx-info-circle"></i> Clique para registrar a localização.
                </div>
            </div>

            <hr style="margin: 20px 0; border-color: #e0e0e0;">

            <!-- Campos adicionais -->
            <div class="atividades-form-row">
                <!-- Técnico Responsável -->
                <div class="atividades-form-group">
                    <label class="atividades-form-label" for="tecnico_id_nova">
                        <i class="bx bx-user"></i> Técnico Responsável
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

                <!-- Visível ao Cliente -->
                <div class="atividades-form-group">
                    <label class="atividades-form-checkbox" style="margin: 10px 0 0 0;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <input type="checkbox" name="visivel_cliente" value="1" checked style="width: 20px; height: 20px; margin: 0;">
                            <div>
                                <div class="atividades-form-checkbox-label">Visível ao cliente</div>
                                <div class="atividades-form-checkbox-hint">O cliente poderá ver esta atividade</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="atividades-btn-cancel" data-bs-dismiss="modal">
                <i class="bx bx-x"></i> Cancelar
            </button>
            <button type="submit" class="atividades-btn-submit" id="btnSalvarAtividade" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <i class="bx bx-save"></i> CRIAR ATIVIDADE
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<script>
// Função para obter localização GPS
function obterLocalizacaoNovaAtividade() {
    if ('geolocation' in navigator) {
        document.getElementById('gps_info_nova').innerHTML = '<i class="bx bx-time-five"></i> Obtendo localização...';
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('nova_latitude').value = position.coords.latitude;
                document.getElementById('nova_longitude').value = position.coords.longitude;
                document.getElementById('gps_info_nova').innerHTML = '<i class="bx bx-check" style="color: #28a745;"></i> Localização: ' + position.coords.latitude.toFixed(6) + ', ' + position.coords.longitude.toFixed(6);
            },
            function(error) {
                document.getElementById('gps_info_nova').innerHTML = '<i class="bx bx-x" style="color: #dc3545;"></i> Erro: ' + error.message;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    } else {
        document.getElementById('gps_info_nova').innerHTML = '<i class="bx bx-x" style="color: #dc3545;"></i> GPS não disponível.';
    }
}

// Função para salvar atividade no formato do wizard
function salvarAtividadeWizard(event) {
    event.preventDefault();

    const form = document.getElementById('formAdicionarAtividade');
    const formData = new FormData(form);

    // Validação
    const etapaId = formData.get('etapa_id');
    const tipoId = formData.get('tipo_id');
    const titulo = formData.get('titulo');

    if (!etapaId) {
        alert('Por favor, selecione uma etapa da obra.');
        document.getElementById('etapa_id_nova').focus();
        return false;
    }

    if (!tipoId) {
        alert('Por favor, selecione o tipo de atividade.');
        document.getElementById('tipo_id_nova').focus();
        return false;
    }

    if (!titulo || titulo.trim() === '') {
        alert('Por favor, informe o título da atividade.');
        document.getElementById('titulo_nova').focus();
        return false;
    }

    // Desabilita botão para evitar duplo clique
    const btn = document.getElementById('btnSalvarAtividade');
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-time-five"></i> Salvando...';

    // Envia requisição AJAX
    fetch('<?php echo site_url("atividades/checkin_obra"); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
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
            btn.innerHTML = '<i class="bx bx-save"></i> CRIAR ATIVIDADE';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao criar atividade. Tente novamente.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-save"></i> CRIAR ATIVIDADE';
    });

    return false;
}
</script>

<script>
// Filtro de atividades
function filtrarAtividades() {
    const search = document.getElementById('searchAtividade').value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
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

// Focus no campo título quando abrir o modal
$('#modalAdicionar').on('shown.bs.modal', function () {
    $('#titulo').focus();
});

// Animação de entrada
$(document).ready(function() {
    $('.atividade-card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });
});

// Auto-refresh a cada 10 segundos se a aba estiver visível
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        if (!document.hidden) {
            location.reload();
        }
    }, 10000); // 10 segundos
}

function stopAutoRefresh() {
    clearInterval(refreshInterval);
}

// Iniciar auto-refresh quando a página carregar
$(document).ready(function() {
    startAutoRefresh();
});

// Parar refresh quando o modal estiver aberto (para não perder dados do formulário)
$('#modalAdicionar').on('shown.bs.modal', function () {
    stopAutoRefresh();
});

$('#modalAdicionar').on('hidden.bs.modal', function () {
    startAutoRefresh();
});
</script>
