<!-- Visão de Execução das Obras - Dashboard Simplificado -->
<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span class="icon">
                <i class="bx bx-rocket"></i>
            </span>
            <h5>Execução das Obras</h5>
        </div>
        <div class="buttons">
            <a href="<?= site_url('tecnicos_admin/obras') ?>" class="button btn btn-mini btn-default">
                <span class="button__icon"><i class="bx bx-building"></i></span>
                <span class="button__text2">Gestão de Obras</span>
            </a>
            <a href="#modal-vincular-os-obra" data-toggle="modal" class="button btn btn-mini btn-success">
                <span class="button__icon"><i class="bx bx-link"></i></span>
                <span class="button__text2">Vincular OS</span>
            </a>
        </div>
    </div>

    <!-- Dashboard de Obras -->
    <div class="row-fluid" style="margin-top: 20px;">
        <?php if (!empty($obras)): ?>
            <?php foreach ($obras as $obra): ?>
                <?php
                $statusClass = match($obra->status) {
                    'EmExecucao' => 'andamento',
                    'Contratada' => 'contratada',
                    'Paralisada' => 'paralisada',
                    'Finalizada', 'Entregue' => 'concluida',
                    default => 'planejada'
                };

                $statusLabel = match($obra->status) {
                    'EmExecucao' => 'Em Execução',
                    'Contratada' => 'Contratada',
                    'Paralisada' => 'Paralisada',
                    'Orcamentacao' => 'Orçamentação',
                    'Finalizada' => 'Finalizada',
                    'Entregue' => 'Entregue',
                    default => 'Planejada'
                };

                $percentualObra = $obra->percentual_concluido ?? 0;
                $corProgresso = $percentualObra >= 80 ? '#4caf50' : ($percentualObra >= 50 ? '#ff9800' : '#2196f3');
                ?>
                <div class="span6" style="margin-bottom: 20px;">
                    <div class="obra-execucao-card" style="
                        background: white;
                        border-radius: 16px;
                        overflow: hidden;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                        transition: all 0.3s;
                    " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 30px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'">
                        <!-- Header do Card -->
                        <div style="
                            padding: 20px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                        ">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="flex: 1;">
                                    <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">
                                        <i class="bx bx-hash"></i> <?= $obra->codigo ?? '#' . $obra->id ?>
                                    </div>
                                    <h4 style="margin: 0; font-size: 1.2rem; font-weight: 600;">
                                        <?= htmlspecialchars($obra->nome ?? 'Sem nome', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                    </h4>
                                    <div style="margin-top: 8px; font-size: 0.9rem; opacity: 0.9;">
                                        <i class="bx bx-user"></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                    </div>
                                </div>
                                <span style="
                                    padding: 6px 14px;
                                    border-radius: 20px;
                                    font-size: 0.75rem;
                                    font-weight: 600;
                                    background: rgba(255,255,255,0.2);
                                ">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                        </div>

                        <!-- Progresso -->
                        <div style="padding: 20px; border-bottom: 1px solid #eee;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="font-size: 0.85rem; color: #666;">Progresso da Obra</span>
                                <span style="font-weight: 700; color: <?= $corProgresso ?>;"><?= $percentualObra ?>%</span>
                            </div>
                            <div style="height: 8px; background: #f0f0f0; border-radius: 4px; overflow: hidden;">
                                <div style="
                                    height: 100%;
                                    width: <?= $percentualObra ?>%;
                                    background: <?= $corProgresso ?>;
                                    border-radius: 4px;
                                    transition: width 0.5s ease;
                                "></div>
                            </div>
                        </div>

                        <!-- Métricas Rápidas -->
                        <div style="display: flex; padding: 15px 0; border-bottom: 1px solid #eee;">
                            <div style="flex: 1; text-align: center; border-right: 1px solid #eee;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;"><?= $obra->total_os ?></div>
                                <div style="font-size: 0.75rem; color: #888;">OS Vinculadas</div>
                            </div>
                            <div style="flex: 1; text-align: center; border-right: 1px solid #eee;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: #11998e;"><?= $obra->total_etapas ?></div>
                                <div style="font-size: 0.75rem; color: #888;">Etapas</div>
                            </div>
                            <div style="flex: 1; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: #f093fb;"><?= $obra->total_equipe ?></div>
                                <div style="font-size: 0.75rem; color: #888;">Técnicos</div>
                            </div>
                        </div>

                        <!-- OS Recentes -->
                        <?php if (!empty($obra->os_recentes)): ?>
                            <div style="padding: 15px 20px; background: #f8f9fa;">
                                <div style="font-size: 0.8rem; color: #888; margin-bottom: 10px;">
                                    <i class="bx bx-clipboard"></i> OS Recentes
                                </div>
                                <?php foreach (array_slice($obra->os_recentes, 0, 3) as $os): ?>
                                    <?php
                                    $corStatus = match($os->status) {
                                        'Aberto' => '#4caf50',
                                        'Em Andamento' => '#2196f3',
                                        'Finalizado' => '#9c27b0',
                                        'Cancelado' => '#f44336',
                                        default => '#888'
                                    };
                                    ?>
                                    <div style="
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                        padding: 8px 0;
                                        border-bottom: 1px solid #eee;
                                        font-size: 0.85rem;
                                    ">
                                        <div>
                                            <span style="font-weight: 600;">#<?= $os->idOs ?></span>
                                            <span style="color: #666; margin-left: 5px;"><?= $os->nomeCliente ?></span>
                                        </div>
                                        <span style="
                                            padding: 3px 8px;
                                            border-radius: 10px;
                                            font-size: 0.7rem;
                                            background: <?= $corStatus ?>;
                                            color: white;
                                        ">
                                            <?= $os->status ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Ações -->
                        <div style="padding: 15px 20px; display: flex; gap: 10px;">
                            <a href="<?= site_url('tecnicos_admin/ver_obra/' . $obra->id) ?>" class="btn btn-small btn-info" style="flex: 1;">
                                <i class="bx bx-show"></i> Ver Detalhes
                            </a>
                            <a href="<?= site_url('os/adicionar?obra_id=' . $obra->id) ?>" class="btn btn-small btn-success" style="flex: 1;">
                                <i class="bx bx-plus"></i> Nova OS
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="span12" style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 4rem; color: #e0e0e0; margin-bottom: 20px;">
                    <i class="bx bx-rocket"></i>
                </div>
                <h3 style="color: #666; font-weight: 400;">Nenhuma obra em execução</h3>
                <p style="color: #999; margin-top: 10px;">Não há obras ativas no momento.</p>
                <a href="<?= site_url('tecnicos_admin/obras') ?>" class="btn btn-success" style="margin-top: 20px;">
                    <i class="bx bx-building"></i> Ver Todas as Obras
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Vincular OS à Obra (Simplificado) -->
<div id="modal-vincular-os-obra" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalVincularLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h5 id="modalVincularLabel"><i class="bx bx-link"></i> Vincular OS à Obra</h5>
    </div>
    <form action="<?= site_url('tecnicos_admin/vincular_os_obra') ?>" method="post">
        <div class="modal-body">
            <!-- Passo 1: Selecionar a Obra -->
            <div class="control-group">
                <label class="control-label">Obra *</label>
                <div class="controls">
                    <select name="obra_id" id="select-obra-vincular" class="span12" required>
                        <option value="">-- Selecione a Obra --</option>
                        <?php foreach ($obras as $o): ?>
                            <option value="<?= $o->id ?>">
                                <?= htmlspecialchars($o->nome, ENT_QUOTES, 'UTF-8') ?> (<?= $o->codigo ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Passo 2: Selecionar a OS -->
            <div class="control-group" style="margin-top: 15px;">
                <label class="control-label">Ordem de Serviço *</label>
                <div class="controls">
                    <select name="os_id" id="select-os-vincular" class="span12" required disabled>
                        <option value="">-- Primeiro selecione uma obra --</option>
                    </select>
                </div>
                <span class="help-block" id="msg-os" style="color: #666; font-size: 0.85rem;">
                    <i class="bx bx-info-circle"></i> Selecione uma obra para ver as OS disponíveis
                </span>
            </div>

            <!-- Preview da OS -->
            <div id="preview-os" style="margin-top: 15px; display: none;">
                <div style="background: #f8f9fa; border-radius: 8px; padding: 15px;">
                    <h6 style="margin-top: 0; color: #667eea;"><i class="bx bx-detail"></i> Detalhes da OS</h6>
                    <div id="preview-os-content"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button type="submit" class="btn btn-success" id="btn-vincular" disabled>
                <i class="bx bx-link"></i> Vincular à Obra
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Quando selecionar uma obra, carregar OS disponíveis
    $('#select-obra-vincular').on('change', function() {
        var obraId = $(this).val();
        var osSelect = $('#select-os-vincular');
        var msgElement = $('#msg-os');

        if (!obraId) {
            osSelect.prop('disabled', true).html('<option value="">-- Primeiro selecione uma obra --</option>');
            msgElement.html('<i class="bx bx-info-circle"></i> Selecione uma obra para ver as OS disponíveis');
            $('#preview-os').hide();
            $('#btn-vincular').prop('disabled', true);
            return;
        }

        // Buscar OS disponíveis
        osSelect.prop('disabled', true).html('<option value="">-- Carregando OS... --</option>');
        msgElement.html('<i class="bx bx-loader bx-spin"></i> Buscando OS disponíveis...');

        $.ajax({
            url: '<?= site_url("tecnicos_admin/buscar_os_por_obra") ?>',
            type: 'GET',
            data: { obra_id: obraId },
            dataType: 'json',
            success: function(response) {
                osSelect.empty();

                if (response.success && response.os && response.os.length > 0) {
                    // Separar OS não vinculadas e já vinculadas
                    var osNaoVinculadas = response.os.filter(function(os) { return !os.ja_vinculada; });
                    var osVinculadas = response.os.filter(function(os) { return os.ja_vinculada; });

                    osSelect.append('<option value="">-- Selecione uma OS --</option>');

                    // Grupo: OS Disponíveis
                    if (osNaoVinculadas.length > 0) {
                        osSelect.append('<optgroup label="📋 OS Disponíveis (' + osNaoVinculadas.length + ')">');
                        osNaoVinculadas.forEach(function(os) {
                            var docFormatado = os.documento ? os.documento.replace(/[^0-9]/g, '').replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5') : '';
                            osSelect.append(
                                '<option value="' + os.idOs + '" ' +
                                'data-cliente="' + (os.nomeCliente || '') + '" ' +
                                'data-documento="' + docFormatado + '" ' +
                                'data-status="' + os.status + '" ' +
                                'data-data="' + os.dataInicial + '" ' +
                                'data-vinculada="0">'
                                + '#' + os.idOs + ' - ' + os.nomeCliente +
                                (docFormatado ? ' (' + docFormatado + ')' : '') +
                                ' [' + os.status + ']' +
                                '</option>'
                            );
                        });
                        osSelect.append('</optgroup>');
                    }

                    // Grupo: OS Já Vinculadas a Outras Obras
                    if (osVinculadas.length > 0) {
                        osSelect.append('<optgroup label="🔗 OS em Outras Obras (' + osVinculadas.length + ')">');
                        osVinculadas.forEach(function(os) {
                            var docFormatado = os.documento ? os.documento.replace(/[^0-9]/g, '').replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5') : '';
                            osSelect.append(
                                '<option value="' + os.idOs + '" ' +
                                'data-cliente="' + (os.nomeCliente || '') + '" ' +
                                'data-documento="' + docFormatado + '" ' +
                                'data-status="' + os.status + '" ' +
                                'data-data="' + os.dataInicial + '" ' +
                                'data-vinculada="1" ' +
                                'data-obra-vinculada="' + (os.nome_obra_vinculada || '') + '" ' +
                                'style="color: #ff9800;">'
                                + '#' + os.idOs + ' - ' + os.nomeCliente +
                                ' → ' + (os.nome_obra_vinculada || 'Outra obra') +
                                '</option>'
                            );
                        });
                        osSelect.append('</optgroup>');
                    }

                    osSelect.prop('disabled', false);
                    msgElement.html('<i class="bx bx-check" style="color: #4caf50;"></i> ' + osNaoVinculadas.length + ' disponíveis, ' + osVinculadas.length + ' em outras obras');
                } else {
                    osSelect.append('<option value="" disabled>Nenhuma OS cadastrada</option>');
                    msgElement.html('<i class="bx bx-error" style="color: #ff9800;"></i> Nenhuma OS encontrada no sistema');
                }
            },
            error: function() {
                osSelect.html('<option value="" disabled>Erro ao carregar</option>');
                msgElement.html('<i class="bx bx-error" style="color: #f44336;"></i> Erro ao buscar OS');
            }
        });
    });

    // Mostrar preview ao selecionar OS
    $('#select-os-vincular').on('change', function() {
        var selected = $(this).find('option:selected');
        var osId = $(this).val();

        if (osId) {
            var cliente = selected.data('cliente');
            var documento = selected.data('documento');
            var status = selected.data('status');
            var data = selected.data('data');
            var jaVinculada = selected.data('vinculada') == '1';
            var obraVinculada = selected.data('obra-vinculada') || '';

            var corStatus = {
                'Aberto': '#4caf50',
                'Em Andamento': '#2196f3',
                'Finalizado': '#9c27b0',
                'Cancelado': '#f44336'
            }[status] || '#888';

            var html = '<div style="display: grid; gap: 8px; font-size: 0.9rem;">';

            // Aviso se já está vinculada
            if (jaVinculada) {
                html += '<div style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 10px; border-radius: 4px; margin-bottom: 10px;">' +
                    '<i class="bx bx-error-circle" style="color: #ff9800;"></i> ' +
                    '<strong style="color: #e65100;">Atenção:</strong> Esta OS já está vinculada à obra <strong>"' + obraVinculada + '".</strong><br>' +
                    'Ao vincular aqui, ela será <strong>movida</strong> para esta obra.' +
                    '</div>';
            }

            html += '<div><strong>OS:</strong> #' + osId + '</div>' +
                '<div><strong>Cliente:</strong> ' + (cliente || '-') + '</div>' +
                (documento ? '<div><strong>CNPJ:</strong> ' + documento + '</div>' : '') +
                '<div><strong>Status:</strong> <span style="background: ' + corStatus + '; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem;">' + status + '</span></div>' +
                '<div><strong>Data:</strong> ' + (data ? new Date(data).toLocaleDateString('pt-BR') : '-') + '</div>' +
                '</div>';

            $('#preview-os-content').html(html);
            $('#preview-os').show();
            $('#btn-vincular').prop('disabled', false);

            // Mudar cor do botão se já está vinculada
            if (jaVinculada) {
                $('#btn-vincular').removeClass('btn-success').addClass('btn-warning').html('<i class="bx bx-transfer"></i> Mover para Esta Obra');
            } else {
                $('#btn-vincular').removeClass('btn-warning').addClass('btn-success').html('<i class="bx bx-link"></i> Vincular à Obra');
            }
        } else {
            $('#preview-os').hide();
            $('#btn-vincular').prop('disabled', true);
            $('#btn-vincular').removeClass('btn-warning').addClass('btn-success').html('<i class="bx bx-link"></i> Vincular à Obra');
        }
    });
});
</script>

<style>
.obra-execucao-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

/* Animação de entrada */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.obra-execucao-card {
    animation: slideInUp 0.4s ease forwards;
}

.span6:nth-child(1) .obra-execucao-card { animation-delay: 0s; }
.span6:nth-child(2) .obra-execucao-card { animation-delay: 0.1s; }
.span6:nth-child(3) .obra-execucao-card { animation-delay: 0.2s; }
.span6:nth-child(4) .obra-execucao-card { animation-delay: 0.3s; }
</style>
