<?php
/**
 * Visualizar detalhes de um atendimento
 */

// Calcular tempo de atendimento
$tempo = '-';
if ($checkin->data_saida) {
    $entrada = new DateTime($checkin->data_entrada);
    $saida = new DateTime($checkin->data_saida);
    $intervalo = $entrada->diff($saida);
    $tempo = $intervalo->format('%h:%i');
}
?>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('dashboard'); ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?php echo site_url('relatorioatendimentos'); ?>">Relatório de Atendimentos</a> <span class="divider">/</span></li>
            <li class="active">Visualizar Atendimento #<?php echo $checkin->idCheckin; ?></li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-time"></i></span>
                <h5>Detalhes do Atendimento #<?php echo $checkin->idCheckin; ?></h5>
                <div class="buttons">
                    <a href="<?php echo site_url('os/visualizar/' . $checkin->os_id); ?>" class="btn btn-mini btn-info">
                        <i class="bx bx-show"></i> Ver OS
                    </a>
                    <a href="<?php echo site_url('relatorioatendimentos'); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="widget-content">
                <!-- Status do Atendimento -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span12">
                        <?php if ($checkin->data_saida): ?>
                            <div class="alert alert-success">
                                <i class="bx bx-check-circle"></i>
                                <strong>Atendimento Finalizado</strong>
                                <span class="pull-right">Tempo total: <strong><?php echo $tempo; ?> horas</strong></span>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bx bx-loader bx-spin"></i>
                                <strong>Atendimento em Andamento</strong>
                                <span class="pull-right">Iniciado em: <strong><?php echo date('d/m/Y H:i', strtotime($checkin->data_entrada)); ?></strong></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informações Principais -->
                <div class="row-fluid">
                    <!-- Dados da OS -->
                    <div class="span6">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-task"></i></span>
                                <h5>Ordem de Serviço</h5>
                            </div>
                            <div class="widget-content">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 120px;">OS Nº:</th>
                                        <td>
                                            <a href="<?php echo site_url('os/visualizar/' . $checkin->os_id); ?>">
                                                #<?php echo $checkin->os_id; ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>ID Check-in:</th>
                                        <td><?php echo $checkin->idCheckin; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <?php if ($checkin->data_saida): ?>
                                                <span class="label label-success">Finalizado</span>
                                            <?php else: ?>
                                                <span class="label label-warning">Em Andamento</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Dados do Técnico -->
                    <div class="span6">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-user"></i></span>
                                <h5>Técnico</h5>
                            </div>
                            <div class="widget-content">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 120px;">ID Usuário:</th>
                                        <td><?php echo $checkin->usuarios_id; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Data Entrada:</th>
                                        <td><?php echo date('d/m/Y H:i', strtotime($checkin->data_entrada)); ?></td>
                                    </tr>
                                    <?php if ($checkin->data_saida): ?>
                                    <tr>
                                        <th>Data Saída:</th>
                                        <td><?php echo date('d/m/Y H:i', strtotime($checkin->data_saida)); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="row-fluid">
                    <div class="span6">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-log-in"></i></span>
                                <h5>Observação de Entrada</h5>
                            </div>
                            <div class="widget-content">
                                <?php if (!empty($checkin->observacao_entrada)): ?>
                                    <p><?php echo nl2br(htmlspecialchars($checkin->observacao_entrada)); ?></p>
                                <?php else: ?>
                                    <p class="text-muted"><em>Nenhuma observação de entrada registrada.</em></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="span6">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-log-out"></i></span>
                                <h5>Observação de Saída</h5>
                            </div>
                            <div class="widget-content">
                                <?php if (!empty($checkin->observacao_saida)): ?>
                                    <p><?php echo nl2br(htmlspecialchars($checkin->observacao_saida)); ?></p>
                                <?php else: ?>
                                    <p class="text-muted"><em>Nenhuma observação de saída registrada.</em></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="form-actions" style="text-align: right; margin-bottom: 0;">
                            <a href="<?php echo site_url('relatorioatendimentos'); ?>" class="btn">
                                <i class="bx bx-arrow-back"></i> Voltar para Lista
                            </a>
                            <a href="<?php echo site_url('os/visualizar/' . $checkin->os_id); ?>" class="btn btn-primary">
                                <i class="bx bx-show"></i> Visualizar OS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
