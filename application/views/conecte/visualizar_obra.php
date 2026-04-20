<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-building-house"></i></span>
                <h5>Detalhes da Obra</h5>
                <div class="buttons">
                    <a href="<?= site_url('mine/obras') ?>" class="btn btn-mini btn-inverse">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Cabeçalho da Obra -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span8">
                        <h3 style="margin: 0 0 10px 0;"><?= htmlspecialchars($obra->nome) ?></h3>
                        <p style="margin: 5px 0;">
                            <strong>Código:</strong> <span class="label label-info"><?= $obra->codigo ?></span>
                        </p>
                        <p style="margin: 5px 0;">
                            <strong>Tipo:</strong> <?= $obra->tipo_obra ?? '-' ?>
                        </p>
                        <p style="margin: 5px 0;">
                            <strong>Endereço:</strong> <?= htmlspecialchars($obra->endereco ?? '-') ?>
                        </p>
                    </div>

                    <div class="span4">
                        <div style="text-align: right;">
                            <?php
                            $statusColors = [
                                'Em Andamento' => 'info',
                                'Contratada' => 'warning',
                                'EmExecucao' => 'info',
                                'Concluída' => 'success',
                                'Concluida' => 'success',
                                'Paralisada' => 'important',
                                'Cancelada' => 'inverse',
                                'Prospeccao' => 'default'
                            ];
                            $statusLabel = $statusColors[$obra->status] ?? 'default';
                            ?>
                            <span class="label label-<?= $statusLabel ?>" style="font-size: 1.1em; padding: 8px 16px;">
                                <?= $obra->status ?>
                            </span>
                        </div>

                        <div style="margin-top: 15px;">
                            <strong>Progresso:</strong>
                            <div class="progress" style="margin: 5px 0;">
                                <div class="bar" style="width: <?= $obra->percentual_concluido ?? 0 ?>%;">
                                    <?= $obra->percentual_concluido ?? 0 ?>%
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <strong>Período:</strong><br>
                            <?= isset($obra->data_inicio_contrato) ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : '-' ?>
                            <?= isset($obra->data_fim_prevista) ? ' até ' . date('d/m/Y', strtotime($obra->data_fim_prevista)) : '' ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($obra->observacoes)): ?>
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span12">
                        <div class="well">
                            <strong>Observações:</strong><br>
                            <?= nl2br(htmlspecialchars($obra->observacoes)) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- OS Vinculadas -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-file"></i></span>
                                <h5>Ordens de Serviço Vinculadas</h5>
                            </div>
                            <div class="widget-content nopadding">
                                <?php if (!empty($os_vinculadas)): ?>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>OS #</th>
                                                <th>Data Inicial</th>
                                                <th>Data Final</th>
                                                <th>Status</th>
                                                <th>Responsável</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($os_vinculadas as $os): ?>
                                            <tr>
                                                <td><strong>#<?= $os->idOs ?></strong></td>
                                                <td><?= date('d/m/Y', strtotime($os->dataInicial)) ?></td>
                                                <td><?= isset($os->dataFinal) ? date('d/m/Y', strtotime($os->dataFinal)) : '-' ?></td>
                                                <td>
                                                    <?php
                                                    $osStatusColors = [
                                                        'Aberto' => 'important',
                                                        'Em Andamento' => 'info',
                                                        'Finalizado' => 'success',
                                                        'Faturado' => 'inverse',
                                                        'Cancelado' => 'default'
                                                    ];
                                                    $osStatusLabel = $osStatusColors[$os->status] ?? 'default';
                                                    ?>
                                                    <span class="label label-<?= $osStatusLabel ?>"><?= $os->status ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($os->responsavel ?? '-') ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-info" style="margin: 10px;">
                                        <i class="bx bx-info-circle"></i> Nenhuma ordem de serviço vinculada a esta obra.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Etapas -->
                <?php if (!empty($etapas)): ?>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-list-check"></i></span>
                                <h5>Etapas da Obra</h5>
                            </div>
                            <div class="widget-content nopadding">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Etapa</th>
                                            <th>Nome</th>
                                            <th>Especialidade</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($etapas as $etapa): ?>
                                        <tr>
                                            <td><?= $etapa->numero_etapa ?></td>
                                            <td><?= htmlspecialchars($etapa->nome) ?></td>
                                            <td><?= htmlspecialchars($etapa->especialidade ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $etapaStatusColors = [
                                                    'pendente' => 'default',
                                                    'em_andamento' => 'info',
                                                    'concluida' => 'success'
                                                ];
                                                $etapaStatusLabel = $etapaStatusColors[$etapa->status] ?? 'default';
                                                ?>
                                                <span class="label label-<?= $etapaStatusLabel ?>"><?= ucfirst(str_replace('_', ' ', $etapa->status)) ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Equipe -->
                <?php if (!empty($equipe)): ?>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-group"></i></span>
                                <h5>Equipe da Obra</h5>
                            </div>
                            <div class="widget-content nopadding">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Técnico</th>
                                            <th>Função</th>
                                            <th>Data Entrada</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($equipe as $membro): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($membro->tecnico_nome ?? '-') ?></td>
                                            <td><?= htmlspecialchars($membro->funcao ?? 'Técnico') ?></td>
                                            <td><?= isset($membro->data_entrada) ? date('d/m/Y', strtotime($membro->data_entrada)) : '-' ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Atividades Recentes -->
                <?php if (!empty($atividades)): ?>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-calendar-check"></i></span>
                                <h5>Atividades Recentes</h5>
                            </div>
                            <div class="widget-content nopadding">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Título</th>
                                            <th>Técnico</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($atividades as $atividade): ?>
                                        <?php
                                        $tipoColors = [
                                            'trabalho' => 'info',
                                            'impedimento' => 'important',
                                            'visita' => 'success',
                                            'manutencao' => 'warning',
                                            'outro' => 'default'
                                        ];
                                        $tipoLabel = $tipoColors[$atividade->tipo] ?? 'default';

                                        $statusColors = [
                                            'agendada' => 'default',
                                            'iniciada' => 'info',
                                            'pausada' => 'warning',
                                            'concluida' => 'success',
                                            'cancelada' => 'inverse'
                                        ];
                                        $statusLabel = $statusColors[$atividade->status] ?? 'default';
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($atividade->data_atividade)) ?></td>
                                            <td><?= htmlspecialchars($atividade->titulo) ?></td>
                                            <td><?= htmlspecialchars($atividade->tecnico_nome ?? '-') ?></td>
                                            <td><span class="label label-<?= $tipoLabel ?>"><?= ucfirst($atividade->tipo) ?></span></td>
                                            <td><span class="label label-<?= $statusLabel ?>"><?= ucfirst($atividade->status) ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Galeria de Fotos -->
                <?php if (!empty($fotos)): ?>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-images"></i></span>
                                <h5>Fotos da Obra</h5>
                            </div>
                            <div class="widget-content">
                                <div class="row-fluid">
                                    <?php foreach ($fotos as $foto): ?>
                                    <div class="span2" style="margin-bottom: 10px;">
                                        <a href="<?= base_url($foto) ?>" target="_blank" class="thumbnail">
                                            <img src="<?= base_url($foto) ?>" style="height: 100px; object-fit: cover;">
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
