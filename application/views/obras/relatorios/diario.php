<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid">
    <div class="span12">
        <!-- Cabeçalho do relatório -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-file-alt"></i></span>
                <h5>Relatório Diário de Obra</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="btn btn-mini btn-default">
                        <i class="icon-arrow-left"></i> Voltar
                    </a>
                    <button onclick="window.print()" class="btn btn-mini btn-info">
                        <i class="icon-print icon-white"></i> Imprimir
                    </button>
                </div>
            </div>

            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span6">
                        <h4><?php echo $obra->nome; ?></h4>
                        <p><strong>Cliente:</strong> <?php echo $obra->cliente_nome ?? 'N/A'; ?></p>
                        <p><strong>Endereço:</strong> <?php echo $obra->endereco ?? 'N/A'; ?></p>
                    </div>
                    <div class="span6 text-right">
                        <h4>Data do Relatório</h4>
                        <p style="font-size: 18px; font-weight: bold;">
                            <?php echo date('d/m/Y', strtotime($data_relatorio)); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtro de data -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-calendar"></i></span>
                <h5>Selecionar Data</h5>
            </div>
            <div class="widget-content">
                <form method="get" action="<?php echo site_url('obras/relatorioDiario/' . $obra->id); ?>" class="form-inline">
                    <label for="data">Data:</label>
                    <input type="date" name="data" id="data" value="<?php echo $data_relatorio; ?>" class="input-small">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-search icon-white"></i> Filtrar
                    </button>
                </form>
            </div>
        </div>

        <!-- Atividades do dia -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-tasks"></i></span>
                <h5>Atividades Realizadas</h5>
            </div>

            <div class="widget-content nopadding">
                <?php if (!empty($atividades)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 20%;">Etapa</th>
                                <th style="width: 35%;">Descrição</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 15%;">Responsável</th>
                                <th style="width: 10%;">Horas</th>
                                <th style="width: 10%;">Progresso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($atividades as $atividade): ?>
                                <tr>
                                    <td><?php echo $atividade->id; ?></td>
                                    <td><?php echo $atividade->etapa_nome ?? 'N/A'; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($atividade->descricao)); ?></td>
                                    <td>
                                        <span class="label <?php echo $atividade->status == 'concluida' ? 'label-success' : ($atividade->status == 'em_andamento' ? 'label-info' : 'label-warning'); ?>">
                                            <?php
                                            $statusLabels = [
                                                'pendente' => 'Pendente',
                                                'em_andamento' => 'Em Andamento',
                                                'concluida' => 'Concluída',
                                                'cancelada' => 'Cancelada'
                                            ];
                                            echo $statusLabels[$atividade->status] ?? $atividade->status;
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo $atividade->responsavel_nome ?? 'N/A'; ?></td>
                                    <td><?php echo $atividade->horas_trabalhadas ?? 0; ?>h</td>
                                    <td>
                                        <div class="progress" style="margin-bottom: 0; height: 20px;">
                                            <div class="bar <?php echo $atividade->percentual_concluido == 100 ? 'bar-success' : 'bar-info'; ?>"
                                                 style="width: <?php echo $atividade->percentual_concluido ?? 0; ?>%;">
                                                <?php echo $atividade->percentual_concluido ?? 0; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="icon-info-sign"></i> Nenhuma atividade registrada para esta data.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Resumo -->
        <?php if (!empty($atividades)): ?>
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="icon-chart-pie"></i></span>
                    <h5>Resumo do Dia</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span3 text-center">
                            <h3><?php echo count($atividades); ?></h3>
                            <small>Atividades</small>
                        </div>
                        <div class="span3 text-center">
                            <h3>
                                <?php
                                $concluidas = array_filter($atividades, function($a) { return $a->status == 'concluida'; });
                                echo count($concluidas);
                                ?>
                            </h3>
                            <small>Concluídas</small>
                        </div>
                        <div class="span3 text-center">
                            <h3>
                                <?php
                                $horasTotal = array_sum(array_map(function($a) { return $a->horas_trabalhadas ?? 0; }, $atividades));
                                echo $horasTotal;
                                ?>h
                            </h3>
                            <small>Horas Trabalhadas</small>
                        </div>
                        <div class="span3 text-center">
                            <h3>
                                <?php
                                $progressoMedio = count($atividades) > 0
                                    ? round(array_sum(array_map(function($a) { return $a->percentual_concluido ?? 0; }, $atividades)) / count($atividades), 1)
                                    : 0;
                                echo $progressoMedio;
                                ?>%
                            </h3>
                            <small>Progresso Médio</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Assinaturas -->
        <div class="widget-box" style="margin-top: 40px;">
            <div class="widget-content">
                <div class="row-fluid" style="margin-top: 50px;">
                    <div class="span6 text-center">
                        <hr style="width: 80%; margin: 0 auto;">
                        <p style="margin-top: 10px;">
                            <strong>Responsável pela Obra</strong><br>
                            <?php echo $obra->gestor_nome ?? '_______________________'; ?>
                        </p>
                    </div>
                    <div class="span6 text-center">
                        <hr style="width: 80%; margin: 0 auto;">
                        <p style="margin-top: 10px;">
                            <strong>Cliente</strong><br>
                            <?php echo $obra->cliente_nome ?? '_______________________'; ?>
                        </p>
                    </div>
                </div>
                <div class="row-fluid" style="margin-top: 30px;">
                    <div class="span12 text-center">
                        <p class="muted">
                            <small>Documento gerado em <?php echo date('d/m/Y H:i:s'); ?></small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .widget-title .buttons,
    .widget-box:has(.form-inline) {
        display: none !important;
    }

    .widget-box {
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }

    .progress {
        background-color: #f5f5f5 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .bar-success {
        background-color: #5bb75b !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .bar-info {
        background-color: #49afcd !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
