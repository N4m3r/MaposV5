<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid">
    <div class="span12">
        <!-- Cabeçalho -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-building"></i></span>
                <h5>Relatório de Progresso da Obra</h5>
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
                    <div class="span8">
                        <h3><?php echo $obra->nome; ?></h3>
                        <p><strong>Cliente:</strong> <?php echo $obra->cliente_nome ?? 'N/A'; ?></p>
                        <p><strong>Endereço:</strong> <?php echo $obra->endereco ?? 'N/A'; ?></p>
                        <p><strong>Gestor:</strong> <?php echo $obra->gestor_nome ?? 'Não definido'; ?></p>
                    </div>
                    <div class="span4 text-right">
                        <p><strong>Data do Relatório:</strong><br>
                        <?php echo date('d/m/Y'); ?></p>

                        <p><strong>Status:</strong><br>
                        <span class="label label-info"><?php echo $obra->status; ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progresso Geral -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-dashboard"></i></span>
                <h5>Progresso Geral</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="progress" style="height: 40px; margin: 20px 0;">
                            <div class="bar bar-success" style="width: <?php echo $obra->percentual_concluido ?? 0; ?>%;">
                                <strong style="font-size: 16px;"><?php echo $obra->percentual_concluido ?? 0; ?>%</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3 text-center">
                        <h4><?php echo $estatisticas['total_atividades'] ?? 0; ?></h4>
                        <small>Total de Atividades</small>
                    </div>
                    <div class="span3 text-center">
                        <h4><?php echo $estatisticas['concluidas'] ?? 0; ?></h4>
                        <small>Atividades Concluídas</small>
                    </div>
                    <div class="span3 text-center">
                        <h4><?php echo $estatisticas['em_andamento'] ?? 0; ?></h4>
                        <small>Em Andamento</small>
                    </div>
                    <div class="span3 text-center">
                        <h4><?php echo $estatisticas['total_horas'] ?? 0; ?>h</h4>
                        <small>Horas Trabalhadas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Etapas -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-tasks"></i></span>
                <h5>Detalhamento por Etapas</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($etapas)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Etapa</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Atividades</th>
                                <th>Concluídas</th>
                                <th>Progresso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($etapas as $etapa): ?>
                                <tr>
                                    <td><strong><?php echo $etapa->nome; ?></strong></td>
                                    <td><?php echo $etapa->descricao ? nl2br(htmlspecialchars($etapa->descricao)) : '-'; ?></td>
                                    <td>
                                        <span class="label <?php
                                            echo $etapa->status == 'concluida' ? 'label-success' :
                                                ($etapa->status == 'em_andamento' ? 'label-info' :
                                                    ($etapa->status == 'atrasada' ? 'label-important' : 'label-warning'));
                                        ?>">
                                            <?php
                                            $statusLabels = [
                                                'pendente' => 'Pendente',
                                                'em_andamento' => 'Em Andamento',
                                                'concluida' => 'Concluída',
                                                'atrasada' => 'Atrasada',
                                                'cancelada' => 'Cancelada'
                                            ];
                                            echo $statusLabels[$etapa->status] ?? $etapa->status;
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo $etapa->total_atividades ?? 0; ?></td>
                                    <td><?php echo $etapa->atividades_concluidas ?? 0; ?></td>
                                    <td>
                                        <div class="progress" style="margin-bottom: 0; height: 20px;">
                                            <div class="bar <?php echo ($etapa->percentual_concluido ?? 0) == 100 ? 'bar-success' : 'bar-info'; ?>"
                                                 style="width: <?php echo $etapa->percentual_concluido ?? 0; ?>%;">
                                                <?php echo $etapa->percentual_concluido ?? 0; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="icon-info-sign"></i> Nenhuma etapa cadastrada.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informações do Emitente -->
        <?php if (isset($emitente) && $emitente): ?>
            <div class="widget-box">
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span12 text-center">
                            <p>
                                <strong><?php echo $emitente->nome; ?></strong><br>
                                <?php if ($emitente->cnpj): ?>
                                    CNPJ: <?php echo $emitente->cnpj; ?><br>
                                <?php endif; ?>
                                <?php echo $emitente->rua; ?>, <?php echo $emitente->numero; ?> - <?php echo $emitente->bairro; ?><br>
                                <?php echo $emitente->cidade; ?>/<?php echo $emitente->uf; ?> - CEP: <?php echo $emitente->cep; ?><br>
                                <?php if ($emitente->telefone): ?>
                                    Tel: <?php echo $emitente->telefone; ?>
                                <?php endif; ?>
                            </p>
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
                            <strong>Responsável Técnico</strong><br>
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
                            <small>
                                Relatório gerado em <?php echo date('d/m/Y H:i:s'); ?>
                                <?php if (isset($emitente) && $emitente->nome): ?>
                                    - <?php echo $emitente->nome; ?>
                                <?php endif; ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .widget-title .buttons {
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

    .label-success {
        background-color: #468847 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .label-info {
        background-color: #3a87ad !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
