<?php
/**
 * Relatórios de Produtividade dos Técnicos
 */
$tecnicos = $tecnicos ?? [];
$estatisticas = $estatisticas ?? null;
$data_inicio = $data_inicio ?? date('Y-m-01');
$data_fim = $data_fim ?? date('Y-m-t');
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-bar-chart"></i></span>
                <h5>Relatórios de Produtividade</h5>
            </div>
            <div class="widget-content">

                <!-- Filtros -->
                <form method="get" action="" class="form-inline" style="margin-bottom: 20px;">
                    <label>Período: </label>
                    <input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>" class="input-small">
                    <span> até </span>
                    <input type="date" name="data_fim" value="<?php echo $data_fim; ?>" class="input-small">

                    <button type="submit" class="btn btn-primary">
                        <i class="icon-filter icon-white"></i> Filtrar
                    </button>

                    <a href="<?php echo site_url('tecnicos_admin/relatorios'); ?>" class="btn">
                        <i class="icon-refresh"></i> Limpar
                    </a>
                </form>

                <!-- Estatísticas Gerais -->
                <?php if ($estatisticas): ?>
                    <div class="row-fluid" style="margin-bottom: 20px;">
                        <div class="span4">
                            <div class="alert alert-info">
                                <h4><i class="icon-tasks"></i> Total de OS</h4>
                                <p style="font-size: 2em; margin: 10px 0;"><?php echo $estatisticas->total_os ?? 0; ?></p>
                            </div>
                        </div>

                        <div class="span4">
                            <div class="alert alert-success">
                                <h4><i class="icon-time"></i> Tempo Médio</h4>
                                <p style="font-size: 2em; margin: 10px 0;">
                                    <?php echo round($estatisticas->media_tempo ?? 0, 2); ?>h
                                </p>
                            </div>
                        </div>

                        <div class="span4">
                            <div class="alert alert-warning">
                                <h4><i class="icon-group"></i> Técnicos Ativos</h4>
                                <p style="font-size: 2em; margin: 10px 0;"><?php echo count($tecnicos); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Lista de Técnicos -->
                <h5>Desempenho por Técnico</h5>
                <?php if (!empty($tecnicos)): ?>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Técnico</th>
                                <th>Nível</th>
                                <th>OS Concluídas</th>
                                <th>Horas Trabalhadas</th>
                                <th>Média/OS</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tecnicos as $tec): ?>
                                <tr>
                                    <td><?php echo $tec->nome; ?></td>
                                    <td><span class="badge"><?php echo $tec->nivel_tecnico ?? 'II'; ?></span></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>
                                        <a href="<?php echo site_url('tecnicos_admin/ver_tecnico/' . ($tec->idUsuarios ?? $tec->id)); ?>" class="btn btn-mini btn-info">
                                            <i class="icon-eye-open icon-white"></i> Detalhes
                                        </a>
                                        <a href="<?php echo site_url('tecnicos_admin/rotas/' . ($tec->idUsuarios ?? $tec->id)); ?>" class="btn btn-mini btn-warning">
                                            <i class="icon-road icon-white"></i> Rotas
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">Nenhum técnico cadastrado.</div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
