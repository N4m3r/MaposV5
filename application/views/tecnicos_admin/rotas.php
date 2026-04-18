<?php
/**
 * Rastreamento de Rotas dos Técnicos
 */
$tecnico_id = $tecnico_id ?? null;
$data = $data ?? date('Y-m-d');
$tecnico = $tecnico ?? null;
$rotas = $rotas ?? [];
$tecnicos = $tecnicos ?? [];
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-road"></i></span>
                <h5>Rastreamento de Rotas</h5>
            </div>
            <div class="widget-content">

                <!-- Filtros -->
                <form method="get" action="" class="form-inline" style="margin-bottom: 20px;">
                    <label>Técnico: </label>
                    <select name="tecnico_id" class="input-xlarge">
                        <option value="">Selecione um técnico...</option>
                        <?php foreach ($tecnicos as $tec): ?>
                            <option value="<?php echo $tec->idUsuarios ?? $tec->id; ?>"
                                <?php echo ($tecnico_id == ($tec->idUsuarios ?? $tec->id)) ? 'selected' : ''; ?>>
                                <?php echo $tec->nome; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Data: </label>
                    <input type="date" name="data" value="<?php echo $data; ?>" class="input-small">

                    <button type="submit" class="btn btn-primary">
                        <i class="icon-filter icon-white"></i> Filtrar
                    </button>
                </form>

                <!-- Informações do Técnico -->
                <?php if ($tecnico): ?>
                    <div class="alert alert-info">
                        <strong><i class="icon-user"></i> <?php echo $tecnico->nome; ?></strong> |
                        <?php if ($tecnico->veiculo_placa): ?>
                            Veículo: <?php echo $tecnico->veiculo_tipo . ' - ' . $tecnico->veiculo_placa; ?> |
                        <?php endif; ?>
                        Data: <?php echo date('d/m/Y', strtotime($data)); ?>
                    </div>
                <?php endif; ?>

                <!-- Lista de Pontos -->
                <div class="row-fluid">
                    <div class="span5">
                        <h5>Pontos de Rastreamento</h5>
                        <?php if (!empty($rotas)): ?>
                            <div style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Hora</th>
                                            <th>Tipo</th>
                                            <th>Localização</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rotas as $rota): ?>
                                            <tr>
                                                <td><?php echo date('H:i:s', strtotime($rota->data_hora)); ?></td>
                                                <td>
                                                    <?php
                                                    $badge_class = [
                                                        'login' => 'success',
                                                        'checkin' => 'info',
                                                        'checkout' => 'warning',
                                                        'rastreamento' => 'default',
                                                    ][$rota->tipo] ?? 'default';
                                                    ?>
                                                    <span class="badge badge-<?php echo $badge_class; ?>">
                                                        <?php echo $rota->tipo; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($rota->latitude && $rota->longitude): ?>
                                                        <a href="https://www.google.com/maps?q=<?php echo $rota->latitude; ?>,<?php echo $rota->longitude; ?>"
                                                           target="_blank" class="btn btn-mini">
                                                            <i class="icon-map-marker"></i> Ver no Mapa
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="muted">Sem coordenadas</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?
                            <div class="alert alert-info">
                                <i class="icon-info-sign"></i>
                                <?php if ($tecnico_id): ?>
                                    Nenhum ponto de rastreamento encontrado para esta data.
                                <?php else: ?
                                    Selecione um técnico e data para visualizar as rotas.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?
                    </div>

                    <!-- Placeholder para Mapa -->
                    <div class="span7">
                        <h5>Visualização no Mapa</h5>
                        <div style="background: #f5f5f5; border: 2px dashed #ddd; height: 400px; display: flex; align-items: center; justify-content: center;">
                            <div class="text-center text-muted">
                                <i class="icon-map-marker" style="font-size: 3em; display: block; margin-bottom: 10px;"></i>
                                <p>Mapa de visualização das rotas</p>
                                <?php if (!empty($rotas)): ?>
                                    <p><small><?php echo count($rotas); ?> pontos registrados</small></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Resumo -->
                        <?php if (!empty($rotas)): ?>
                            <div class="row-fluid" style="margin-top: 15px;">
                                <div class="span4">
                                    <div class="alert alert-success">
                                        <strong>Login:</strong> <?php echo count(array_filter($rotas, fn($r) => $r->tipo == 'login')); ?>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="alert alert-info">
                                        <strong>Check-ins:</strong> <?php echo count(array_filter($rotas, fn($r) => $r->tipo == 'checkin')); ?>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="alert alert-warning">
                                        <strong>Check-outs:</strong> <?php echo count(array_filter($rotas, fn($r) => $r->tipo == 'checkout')); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
