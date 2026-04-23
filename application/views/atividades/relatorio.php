<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="span12" style="margin-top: 20px; margin-left: 0;">
    <div class="row-fluid">
        <div class="span12" style="margin-bottom: 15px;">
            <a href="<?php echo site_url('atividades'); ?>" class="btn btn-mini">
                <i class="bx bx-arrow-back"></i> Voltar
            </a>
            <?php if ($this->session->userdata('permissao') == 1): ?>
            <button class="btn btn-mini btn-success" onclick="exportarRelatorio()">
                <i class="bx bx-export"></i> Exportar
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row-fluid">
        <div class="span12" style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <form method="get" action="<?php echo site_url('atividades/relatorio'); ?>" class="form-inline">
                <div class="row-fluid">
                    <div class="span3">
                        <label>Data Início:</label>
                        <input type="date" name="data_inicio" class="input-block-level"
                               value="<?php echo $filtros['data_inicio'] ?? date('Y-m-01'); ?>">
                    </div>
                    <div class="span3">
                        <label>Data Fim:</label>
                        <input type="date" name="data_fim" class="input-block-level"
                               value="<?php echo $filtros['data_fim'] ?? date('Y-m-t'); ?>">
                    </div>
                    <div class="span3">
                        <label>Técnico:</label>
                        <select name="tecnico_id" class="input-block-level">
                            <option value="">Todos</option>
                            <?php foreach ($tecnicos as $tec): ?>
                            <option value="<?php echo $tec->idUsuarios; ?>"
                                    <?php echo ($filtros['tecnico_id'] ?? '') == $tec->idUsuarios ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tec->nome); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="span3" style="padding-top: 25px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Atividades -->
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="bx bx-list-ul"></i></span>
                    <h5>Atividades Registradas</h5>
                </div>

                <div class="widget-content nopadding">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Técnico</th>
                                <th>OS/Obra</th>
                                <th>Tipo</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Duração</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($atividades)): ?
                                <?php foreach ($atividades as $atv): ?
                                <tr>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($atv->hora_inicio)); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($atv->nome_tecnico ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($atv->os_id): ?
                                            OS #<?php echo $atv->os_id; ?>
                                        <?php elseif ($atv->obra_id): ?
                                            Obra: <?php echo htmlspecialchars($atv->obra_nome ?? '#'.$atv->obra_id); ?>
                                        <?php else: ?
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($atv->tipo_nome ?? 'Atividade'); ?></td>
                                    <td><?php echo $atv->hora_inicio ? date('H:i', strtotime($atv->hora_inicio)) : '--:--'; ?></td>
                                    <td><?php echo $atv->hora_fim ? date('H:i', strtotime($atv->hora_fim)) : '--:--'; ?></td>
                                    <td>
                                        <?php
                                        if ($atv->duracao_minutos) {
                                            $horas = floor($atv->duracao_minutos / 60);
                                            $minutos = $atv->duracao_minutos % 60;
                                            echo $horas . 'h ' . $minutos . 'min';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ($atv->status) {
                                            case 'em_andamento':
                                                $statusClass = 'label-warning';
                                                $statusText = 'Em Andamento';
                                                break;
                                            case 'pausada':
                                                $statusClass = 'label-info';
                                                $statusText = 'Pausada';
                                                break;
                                            case 'finalizada':
                                                if ($atv->concluida) {
                                                    $statusClass = 'label-success';
                                                    $statusText = 'Concluída';
                                                } else {
                                                    $statusClass = 'label-important';
                                                    $statusText = 'Não Concluída';
                                                }
                                                break;
                                            default:
                                                $statusClass = 'label-default';
                                                $statusText = $atv->status;
                                        }
                                        ?>
                                        <span class="label <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?
                            <?php else: ?
                                <tr>
                                    <td colspan="8" class="text-center">Nenhuma atividade encontrada.</td>
                                </tr>
                            <?php endif; ?
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportarRelatorio() {
    var dataInicio = document.querySelector('input[name="data_inicio"]').value;
    var dataFim = document.querySelector('input[name="data_fim"]').value;
    var tecnico = document.querySelector('select[name="tecnico_id"]').value;

    var url = '<?php echo site_url('atividades/exportar'); ?>' +
              '?data_inicio=' + dataInicio +
              '&data_fim=' + dataFim +
              '&tecnico_id=' + tecnico;

    window.open(url, '_blank');
}
</script>
