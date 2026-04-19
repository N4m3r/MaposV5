<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span class="icon"><i class="bx bx-calendar-check"></i></span>
            <h5>Relatorio de Atividades do Dia</h5>
        </div>
        <div class="buttons">
            <a href="<?= site_url('tecnicos_admin/execucao_obras') ?>" class="button btn btn-mini btn-default">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Voltar</span>
            </a>
            <a href="#" onclick="window.print()" class="button btn btn-mini btn-info">
                <span class="button__icon"><i class="bx bx-printer"></i></span>
                <span class="button__text2">Imprimir</span>
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row-fluid" style="margin-top: 20px;">
        <div class="span12">
            <div class="widget-box" style="background: #f8f9fa;">
                <div class="widget-content">
                    <form method="get" class="form-inline" style="margin: 0; display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <div class="control-group" style="margin: 0;">
                            <label style="margin-right: 5px;">Data:</label>
                            <input type="date" name="data" value="<?= $data ?>" class="input-small" style="margin-bottom: 0;">
                        </div>

                        <div class="control-group" style="margin: 0;">
                            <label style="margin-right: 5px;">Tecnico:</label>
                            <select name="tecnico_id" class="input-medium" style="margin-bottom: 0;">
                                <option value="">Todos</option>
                                <?php foreach ($tecnicos as $tec): ?>
                                    <option value="<?= $tec->idUsuarios ?>" <?= ($tecnico_id == $tec->idUsuarios) ? 'selected' : '' ?>><?= $tec->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="control-group" style="margin: 0;">
                            <label style="margin-right: 5px;">Obra:</label>
                            <select name="obra_id" class="input-medium" style="margin-bottom: 0;">
                                <option value="">Todas</option>
                                <?php foreach ($obras as $ob): ?>
                                    <option value="<?= $ob->id ?>" <?= ($obra_id == $ob->id) ? 'selected' : '' ?>><?= $ob->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter"></i> Filtrar
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo -->
    <div class="row-fluid" style="margin-top: 20px;">
        <div class="span4">
            <div class="widget-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="widget-content" style="padding: 20px;">
                    <div style="font-size: 2.5rem; font-weight: 700;"><?= count($atividades) ?></div>
                    <div style="font-size: 1rem; opacity: 0.9;">Total de Registros</div>
                </div>
            </div>
        </div>

        <div class="span4">
            <div class="widget-box" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                <div class="widget-content" style="padding: 20px;">
                    <div style="font-size: 2.5rem; font-weight: 700;"><?= number_format($total_horas, 2, ',', '.') ?>h</div>
                    <div style="font-size: 1rem; opacity: 0.9;">Total de Horas</div>
                </div>
            </div>
        </div>

        <div class="span4">
            <div class="widget-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <div class="widget-content" style="padding: 20px;">
                    <div style="font-size: 2.5rem; font-weight: 700;"><= $data ? date('d/m/Y', strtotime($data)) : date('d/m/Y') ?></div>
                    <div style="font-size: 1rem; opacity: 0.9;">Data do Relatorio</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Atividades -->
    <div class="row-fluid" style="margin-top: 20px;">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="bx bx-list-ul"></i></span>
                    <h5>Atividades Registradas</h5>
                </div>

                <div class="widget-content nopadding">
                    <?php if (!empty($atividades)): ?>

                        <table class="table table-bordered table-striped">

                            <thead>

                                <tr>

                                    <th style="width: 60px;">Hora</th>

                                    <th>Obra</th>

                                    <th>Tarefa</th>

                                    <th>Tecnico</th>

                                    <th>Tipo</th>

                                    <th>Descricao</th>

                                    <th style="width: 80px;">Progresso</th>

                                    <th style="width: 60px;">Horas</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($atividades as $atv): ?>


                                    <tr>


                                        <td><?= date('H:i', strtotime($atv->data_registro)) ?></td>


                                        <td>

                                            <strong><?= htmlspecialchars($atv->obra_codigo, ENT_QUOTES, 'UTF-8') ?></strong>

                                            <br><small style="color: #666;"><?= htmlspecialchars($atv->obra_nome, ENT_QUOTES, 'UTF-8') ?></small>

                                        </td>


                                        <td><?= htmlspecialchars($atv->tarefa_titulo, ENT_QUOTES, 'UTF-8') ?></td>


                                        <td><?= $atv->tecnico_nome ?: 'N/A' ?></td>


                                        <td>

                                            <?php
                                            $tipoLabel = [
                                                'atualizacao_status' => ['Status Atualizado', '#2196f3'],
                                                'atualizacao_progresso' => ['Progresso', '#4caf50'],
                                                'inicio_execucao' => ['Inicio', '#ff9800'],
                                                'fim_execucao' => ['Conclusao', '#9c27b0'],
                                                'comentario' => ['Comentario', '#9e9e9e']
                                            ][$atv->tipo] ?? [$atv->tipo, '#666'];
                                            ?>

                                            <span class="label" style="background: <?= $tipoLabel[1] ?>; color: white;"><?= $tipoLabel[0] ?></span>

                                        </td>


                                        <td><?= nl2br(htmlspecialchars($atv->descricao, ENT_QUOTES, 'UTF-8')) ?></td>


                                        <td>

                                            <?php if ($atv->percentual_novo > 0): ?>

                                                <div class="progress" style="margin: 0; height: 20px;">

                                                    <div class="bar" style="width: <?= $atv->percentual_novo ?>%;"><?= $atv->percentual_novo ?>%</div>

                                                </div>


                                                <?php if ($atv->percentual_anterior != $atv->percentual_novo): ?

                                                    <small style="color: #666;">De <?= $atv->percentual_anterior ?>% para <?= $atv->percentual_novo ?>%</small>

                                                <?php endif; ?>

                                            <?php else: ?

                                                <span style="color: #999;">-</span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <?php if ($atv->horas_trabalhadas > 0): ?

                                                <span style="color: #28a745; font-weight: 600;"><?= $atv->horas_trabalhadas ?>h</span>

                                            <?php else: ?

                                                <span style="color: #999;">-</span>

                                            <?php endif; ?>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    <?php else: ?


                        <div style="text-align: center; padding: 60px 20px;">

                            <i class="bx bx-calendar-x" style="font-size: 4rem; color: #ddd;"></i>

                            <p style="color: #999; margin-top: 20px; font-size: 1.1rem;">
                                Nenhuma atividade registrada nesta data.

                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

    <!-- Timeline Visual -->

    <?php if (!empty($atividades)): ?


        <div class="row-fluid" style="margin-top: 20px;">


            <div class="span12">


                <div class="widget-box">


                    <div class="widget-title">


                        <span class="icon"><i class="bx bx-time-five"></i></span>


                        <h5>Timeline do Dia</h5>


                    </div>


                    <div class="widget-content">


                        <div style="position: relative; padding-left: 30px;">


                            <div style="position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: #667eea;"></div>


                            <?php foreach ($atividades as $index => $atv): ?



                                <div style="position: relative; margin-bottom: 20px; padding-left: 20px;">


                                    <div style="position: absolute; left: -25px; top: 0; width: 12px; height: 12px; border-radius: 50%; background: #667eea; border: 2px solid white; box-shadow: 0 0 0 2px #667eea;"></div>


                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 3px solid #667eea;">


                                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">


                                            <div>


                                                <div style="font-weight: 600; color: #333; font-size: 1.05rem;">

                                                    <?= htmlspecialchars($atv->tarefa_titulo, ENT_QUOTES, 'UTF-8') ?>

                                                </div>


                                                <div style="color: #666; margin-top: 5px;">

                                                    <i class="bx bx-building"></i> <?= htmlspecialchars($atv->obra_nome, ENT_QUOTES, 'UTF-8') ?>

                                                </div>


                                                <div style="color: #666; margin-top: 5px;">

                                                    <i class="bx bx-user"></i> <?= $atv->tecnico_nome ?: 'N/A' ?>

                                                </div>


                                            </div>


                                            <div style="text-align: right;">


                                                <div style="font-size: 1.2rem; font-weight: 700; color: #667eea;">

                                                    <?= date('H:i', strtotime($atv->data_registro)) ?>

                                                </div>


                                                <?php if ($atv->horas_trabalhadas > 0): ?


                                                    <div style="color: #28a745; font-weight: 600;"><?= $atv->horas_trabalhadas ?> horas</div>


                                                <?php endif; ?>


                                            </div>


                                        </div>


                                        <?php if ($atv->descricao): ?


                                            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e0e0e0; color: #555;">


                                                <?= nl2br(htmlspecialchars($atv->descricao, ENT_QUOTES, 'UTF-8')) ?>


                                            </div>


                                        <?php endif; ?>


                                    </div>


                                </div>



                            <?php endforeach; ?>


                        </div>


                    </div>


                </div>


            </div>


        </div>


    <?php endif; ?>
</div>

<style>
@media print {
    .buttons, .widget-box:first-child {
        display: none !important;
    }

    .widget-box {
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
}
</style>
