<?php
/**
 * Dashboard do Técnico - Portal Interno
 * Design integrado ao tema MAPOS
 */
?>

<!-- Header do Técnico -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box" style="margin-top: 0;">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-hard-hat"></i></span>
                <h5>Portal do Técnico</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/logout'); ?>" class="btn btn-mini btn-danger">
                        <i class="bx bx-log-out"></i> Sair
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span8">
                        <div class="media">
                            <div class="pull-left">
                                <div class="tec-avatar" style="
                                    width: 70px;
                                    height: 70px;
                                    border-radius: 50%;
                                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: white;
                                    font-size: 28px;
                                    font-weight: 600;
                                ">
                                    <?php echo strtoupper(substr($tecnico->nome ?? 'T', 0, 1)); ?>
                                </div>
                            </div>
                            <div class="media-body" style="padding-left: 15px;">
                                <h4 class="media-heading" style="margin: 5px 0 5px 0; font-size: 18px;">
                                    <?php echo htmlspecialchars($tecnico->nome ?? 'Técnico', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                </h4>
                                <p style="margin: 0; color: #666; font-size: 13px;">
                                    <i class="bx bx-envelope"></i> <?php echo htmlspecialchars($tecnico->email ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                </p>
                                <span class="label label-info" style="margin-top: 5px; display: inline-block;">
                                    <i class="bx bx-star"></i> Nível <?php echo $tecnico->nivel_tecnico ?? 'II'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="span4 text-right">
                        <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; display: inline-block; text-align: center;">
                            <div style="font-size: 13px; color: #666; margin-bottom: 5px;">
                                <i class="bx bx-calendar"></i> <?php echo date('d/m/Y'); ?>
                            </div>
                            <div style="font-size: 18px; font-weight: bold; color: #333;">
                                <?php
                                $dia_semana = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                                echo $dia_semana[date('w')];
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row-fluid">
    <!-- OS de Hoje -->
    <div class="span3">
        <div class="widget-box" style="background: #3498db; border: none;">
            <div class="widget-content" style="padding: 20px; color: white;">
                <div class="row-fluid">
                    <div class="span8">
                        <div style="font-size: 32px; font-weight: bold;"><?php echo count($os_hoje ?? []); ?></div>
                        <div style="font-size: 13px; opacity: 0.9;">OS Hoje</div>
                    </div>
                    <div class="span4 text-right">
                        <i class="bx bx-calendar-check" style="font-size: 40px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendentes -->
    <div class="span3">
        <div class="widget-box" style="background: #e74c3c; border: none;">
            <div class="widget-content" style="padding: 20px; color: white;">
                <div class="row-fluid">
                    <div class="span8">
                        <div style="font-size: 32px; font-weight: bold;"><?php echo count($os_pendentes ?? []); ?></div>
                        <div style="font-size: 13px; opacity: 0.9;">Pendentes</div>
                    </div>
                    <div class="span4 text-right">
                        <i class="bx bx-time-five" style="font-size: 40px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Concluídas -->
    <div class="span3">
        <div class="widget-box" style="background: #27ae60; border: none;">
            <div class="widget-content" style="padding: 20px; color: white;">
                <div class="row-fluid">
                    <div class="span8">
                        <div style="font-size: 32px; font-weight: bold;"><?php echo $os_concluidas ?? 0; ?></div>
                        <div style="font-size: 13px; opacity: 0.9;">Concluídas (Semana)</div>
                    </div>
                    <div class="span4 text-right">
                        <i class="bx bx-check-circle" style="font-size: 40px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estoque -->
    <div class="span3">
        <div class="widget-box" style="background: #9b59b6; border: none;">
            <div class="widget-content" style="padding: 20px; color: white;">
                <div class="row-fluid">
                    <div class="span8">
                        <div style="font-size: 32px; font-weight: bold;"><?php echo count($estoque ?? []); ?></div>
                        <div style="font-size: 13px; opacity: 0.9;">Itens em Estoque</div>
                    </div>
                    <div class="span4 text-right">
                        <i class="bx bx-package" style="font-size: 40px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-bolt"></i></span>
                <h5>Ações Rápidas</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid" style="text-align: center;">
                    <div class="span4">
                        <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-large btn-info" style="width: 90%; padding: 20px;">
                            <i class="bx bx-clipboard" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                            <span style="font-size: 14px;">Minhas OS</span>
                        </a>
                    </div>
                    <div class="span4">
                        <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="btn btn-large btn-success" style="width: 90%; padding: 20px;">
                            <i class="bx bx-package" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                            <span style="font-size: 14px;">Meu Estoque</span>
                        </a>
                    </div>
                    <div class="span4">
                        <a href="<?php echo site_url('tecnicos/perfil'); ?>" class="btn btn-large btn-warning" style="width: 90%; padding: 20px;">
                            <i class="bx bx-user" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                            <span style="font-size: 14px;">Meu Perfil</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- OS de Hoje -->
<div class="row-fluid">
    <div class="span8">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-calendar-event"></i></span>
                <h5>OS de Hoje</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-mini btn-info">
                        <i class="bx bx-list-ul"></i> Ver Todas
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($os_hoje)): ?>
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">OS</th>
                                <th>Cliente</th>
                                <th style="width: 100px;">Horário</th>
                                <th style="width: 100px; text-align: center;">Status</th>
                                <th style="width: 80px; text-align: center;">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($os_hoje as $os): ?>
                                <tr>
                                    <td style="text-align: center; font-weight: bold; color: #667eea;">
                                        #<?php echo $os->idOs; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($os->cliente_nome ?? 'N/A', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                    </td>
                                    <td>
                                        <?php echo isset($os->hora_inicial) ? $os->hora_inicial : '-'; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php
                                        $statusLabel = $os->status ?? 'Aberto';
                                        $statusClass = 'label';
                                        switch ($statusLabel) {
                                            case 'Aberto':
                                                $statusClass = 'label label-info';
                                                break;
                                            case 'Em Andamento':
                                                $statusClass = 'label label-warning';
                                                break;
                                            case 'Finalizada':
                                                $statusClass = 'label label-success';
                                                break;
                                            default:
                                                $statusClass = 'label';
                                        }
                                        ?>
                                        <span class="<?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="<?php echo site_url('tecnicos/executar_os/' . $os->idOs); ?>"
                                           class="btn btn-mini btn-success" title="Executar OS">
                                            <i class="bx bx-play"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="padding: 40px; text-align: center; color: #999;">
                        <i class="bx bx-calendar-check" style="font-size: 48px; opacity: 0.3;"></i>
                        <h4 style="margin: 15px 0 5px 0; color: #666; font-weight: normal;">Nenhuma OS agendada para hoje</h4>
                        <p style="font-size: 13px;">Você não possui ordens de serviço agendadas para hoje.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Resumo do Estoque -->
    <div class="span4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-package"></i></span>
                <h5>Meu Estoque</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="btn btn-mini btn-success">
                        <i class="bx bx-list-ul"></i> Ver Tudo
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <?php if (!empty($estoque)): ?>
                    <div style="max-height: 250px; overflow-y: auto;">
                        <?php
                        $count = 0;
                        foreach ($estoque as $item):
                            if ($count >= 5) break;
                        ?>
                            <div style="padding: 10px; border-bottom: 1px solid #eee;">
                                <div style="font-weight: 600; font-size: 13px; margin-bottom: 3px;">
                                    <i class="bx bx-package" style="color: #27ae60;"></i>
                                    <?php echo htmlspecialchars($item->produto_nome ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                </div>
                                <div style="font-size: 12px; color: #888;">
                                    Qtd: <span class="label label-info"><?php echo $item->quantidade; ?></span>
                                    <?php echo $item->unidade ?? ''; ?>
                                </div>
                            </div>
                        <?php
                            $count++;
                        endforeach;
                        ?>
                    </div>
                    <?php if (count($estoque) > 5): ?>
                        <div style="text-align: center; padding: 10px; border-top: 1px solid #eee;">
                            <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="btn btn-small btn-default">
                                Ver mais <?php echo count($estoque) - 5; ?> itens
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="padding: 30px; text-align: center; color: #999;">
                        <i class="bx bx-package" style="font-size: 40px; opacity: 0.3;"></i>
                        <p style="margin-top: 10px; font-size: 13px;">Nenhum item em estoque</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
