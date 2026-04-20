<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-building-house"></i>
        </span>
        <h5>Minhas Obras</h5>
    </div>

    <div class="widget-content nopadding tab-content">

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><i class="icon-ok"></i> <?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><i class="icon-remove"></i> <?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>

        <?php if (isset($results) && count($results) > 0): ?>
            <!-- Card View -->
            <div class="row-fluid" style="padding: 15px;">
                <?php foreach ($results as $r): ?>
                    <?php
                    $status = $r->status ?? 'Em Andamento';
                    $statusColors = [
                        'Em Andamento' => 'info',
                        'Contratada' => 'warning',
                        'EmExecucao' => 'info',
                        'Concluída' => 'success',
                        'Concluida' => 'success',
                        'Paralisada' => 'important',
                        'Cancelada' => 'inverse',
                        'Prospeccao' => 'default',
                        'Prospecção' => 'default'
                    ];
                    $statusLabel = $statusColors[$status] ?? 'default';
                    $progresso = $r->percentual_concluido ?? $r->progresso ?? 0;
                    $dias_restantes = $r->data_fim_prevista ? ceil((strtotime($r->data_fim_prevista) - time()) / 86400) : null;
                    ?>

                    <div class="span4" style="margin-bottom: 15px;">
                        <div class="well" style="padding: 15px; margin: 0;">
                            <div class="pull-right">
                                <span class="label label-<?php echo $statusLabel; ?>"><?php echo $status; ?></span>
                            </div>
                            <h4 style="margin-top: 0;">
                                <i class="bx bx-building"></i>
                                <?php echo htmlspecialchars($r->nome ?? 'Obra #' . $r->id); ?>
                            </h4>

                            <p class="text-muted">
                                <i class="bx bx-map"></i> <?php echo htmlspecialchars($r->endereco ?? 'Endereço não informado'); ?>
                            </p>

                            <!-- Progresso -->
                            <div class="control-group">
                                <label>Progresso: <?php echo $progresso; ?>%</label>
                                <div class="progress" style="margin: 5px 0; height: 12px;">
                                    <div class="bar bar-success" style="width: <?php echo $progresso; ?>%;"></div>
                                </div>
                            </div>

                            <!-- Info adicional -->
                            <div class="row-fluid" style="margin-top: 10px; font-size: 12px;">
                                <div class="span6">
                                    <strong>Início:</strong><br>
                                    <?php echo $r->data_inicio_contrato ? date('d/m/Y', strtotime($r->data_inicio_contrato)) : 'N/A'; ?>
                                </div>
                                <div class="span6">
                                    <strong>Previsão:</strong><br>
                                    <?php if ($dias_restantes !== null && $dias_restantes >= 0): ?>
                                        <span class="text-success"><?php echo date('d/m/Y', strtotime($r->data_fim_prevista)); ?></span>
                                    <?php elseif ($dias_restantes < 0): ?>
                                        <span class="text-error">Atrasada</span>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div style="margin-top: 15px;">
                                <a href="<?php echo site_url('mine/visualizarObra/' . $r->id); ?>" class="btn btn-block btn-primary">
                                    <i class="bx bx-show"></i> Acompanhar Obra
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if (++$count % 3 == 0): ?>
                        </div><div class="row-fluid" style="padding: 0 15px;">
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="alert alert-info" style="margin: 15px;">
                <i class="bx bx-info-circle"></i>
                <strong>Nenhuma obra encontrada.</strong><br>
                Você ainda não possui obras vinculadas à sua conta.
            </div>
        <?php endif; ?>

    </div>
</div>

<?php if (isset($results) && count($results) > 10): ?>
    <?php echo $this->pagination->create_links(); ?>
<?php endif; ?>
