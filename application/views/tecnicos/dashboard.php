<?php $this->load->view('tema/header'); ?>

<div class="row-fluid" style="margin-top: 20px;">
    <div class="span12">
        <!-- Header -->
        <div class="widget-box">
            <div class="widget-title" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span class="icon" style="color: white;">
                    <i class="bx bx-hard-hat"></i>
                </span>
                <h5 style="color: white;">Portal do Técnico</h5>
                <div class="buttons" style="margin-top: 3px;">
                    <a href="<?php echo site_url('tecnicos/logout'); ?>" class="btn btn-mini btn-danger">
                        <i class="bx bx-log-out"></i> Sair
                    </a>
                </div>
            </div>

            <div class="widget-content" style="padding: 20px;">
                <!-- Perfil do Técnico -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span12" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                        <div style="
                            width: 80px;
                            height: 80px;
                            border-radius: 50%;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-size: 32px;
                            font-weight: 600;
                        ">
                            <?php echo strtoupper(substr($tecnico->nome ?? 'T', 0, 1)); ?>
                        </div>
                        <div>
                            <h4 style="margin: 0; color: #333;">
                                <?php echo htmlspecialchars($tecnico->nome ?? 'Técnico', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                            </h4>
                            <p style="margin: 5px 0 0 0; color: #888;">
                                <i class="bx bx-envelope"></i> <?php echo htmlspecialchars($tecnico->email ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                            </p>
                            <span class="badge badge-info" style="margin-top: 5px;">
                                <i class="bx bx-star"></i> Nível <?php echo $tecnico->nivel_tecnico ?? 'II'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Cards de Status -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span4">
                        <div style="
                            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                            border-radius: 12px;
                            padding: 20px;
                            color: white;
                            text-align: center;
                        ">
                            <div style="font-size: 36px; font-weight: 700;">
                                <?php echo count($os_hoje ?? []); ?>
                            </div>
                            <div style="font-size: 14px; opacity: 0.9;">
                                <i class="bx bx-calendar-check"></i> OS Hoje
                            </div>
                        </div>
                    </div>
                    <div class="span4">
                        <div style="
                            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                            border-radius: 12px;
                            padding: 20px;
                            color: white;
                            text-align: center;
                        ">
                            <div style="font-size: 36px; font-weight: 700;">
                                <?php echo count($os_pendentes ?? []); ?>
                            </div>
                            <div style="font-size: 14px; opacity: 0.9;">
                                <i class="bx bx-time-five"></i> Pendentes
                            </div>
                        </div>
                    </div>
                    <div class="span4">
                        <div style="
                            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                            border-radius: 12px;
                            padding: 20px;
                            color: white;
                            text-align: center;
                        ">
                            <div style="font-size: 36px; font-weight: 700;">
                                <?php echo count($os_concluidas ?? []); ?>
                            </div>
                            <div style="font-size: 14px; opacity: 0.9;">
                                <i class="bx bx-check-circle"></i> Concluídas (Semana)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Rápido -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span12">
                        <h5 style="margin-bottom: 15px; color: #333;">
                            <i class="bx bx-bolt" style="color: #667eea;"></i> Ações Rápidas
                        </h5>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-large" style="
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                border: none;
                                padding: 20px 30px;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                gap: 8px;
                                border-radius: 12px;
                            ">
                                <i class="bx bx-clipboard" style="font-size: 28px;"></i>
                                <span>Minhas OS</span>
                            </a>
                            <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="btn btn-large" style="
                                background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                                color: white;
                                border: none;
                                padding: 20px 30px;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                gap: 8px;
                                border-radius: 12px;
                            ">
                                <i class="bx bx-package" style="font-size: 28px;"></i>
                                <span>Meu Estoque</span>
                            </a>
                            <a href="<?php echo site_url('tecnicos/perfil'); ?>" class="btn btn-large" style="
                                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                                color: white;
                                border: none;
                                padding: 20px 30px;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                gap: 8px;
                                border-radius: 12px;
                            ">
                                <i class="bx bx-user" style="font-size: 28px;"></i>
                                <span>Meu Perfil</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- OS de Hoje -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget-box" style="border-radius: 12px; overflow: hidden;">
                            <div class="widget-title" style="background: #f8f9fa;">
                                <span class="icon"><i class="bx bx-calendar-event" style="color: #667eea;"></i></span>
                                <h5>OS de Hoje</h5>
                                <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-mini btn-info" style="margin-top: 3px;">
                                    Ver Todas
                                </a>
                            </div>
                            <div class="widget-content nopadding">
                                <?php if (!empty($os_hoje)): ?>
                                    <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                                        <thead>
                                            <tr style="background: #f8f9fa;">
                                                <th style="width: 60px; text-align: center;">OS</th>
                                                <th>Cliente</th>
                                                <th style="width: 150px;">Horário</th>
                                                <th style="width: 120px; text-align: center;">Status</th>
                                                <th style="width: 80px; text-align: center;">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($os_hoje as $os): ?>
                                                <tr>
                                                    <td style="text-align: center; font-weight: 600; color: #667eea;">
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
                                                        $statusClass = '';
                                                        $statusLabel = $os->status ?? 'Aberto';
                                                        switch ($statusLabel) {
                                                            case 'Aberto':
                                                                $statusClass = 'badge badge-info';
                                                                break;
                                                            case 'Em Andamento':
                                                                $statusClass = 'badge badge-warning';
                                                                break;
                                                            case 'Finalizado':
                                                                $statusClass = 'badge badge-success';
                                                                break;
                                                            default:
                                                                $statusClass = 'badge';
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
                                    <div style="padding: 40px; text-align: center;">
                                        <div style="font-size: 48px; color: #e0e0e0; margin-bottom: 15px;">
                                            <i class="bx bx-calendar-check"></i>
                                        </div>
                                        <h4 style="color: #666; font-weight: 400;">Nenhuma OS agendada para hoje</h4>
                                        <p style="color: #999;">Você não possui ordens de serviço agendadas para hoje.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estoque Resumo -->
                <?php if (!empty($estoque)): ?>
                <div class="row-fluid" style="margin-top: 20px;">
                    <div class="span12">
                        <div class="widget-box" style="border-radius: 12px; overflow: hidden;">
                            <div class="widget-title" style="background: #f8f9fa;">
                                <span class="icon"><i class="bx bx-package" style="color: #11998e;"></i></span>
                                <h5>Meu Estoque</h5>
                                <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="btn btn-mini btn-success" style="margin-top: 3px;">
                                    Ver Completo
                                </a>
                            </div>
                            <div class="widget-content" style="padding: 15px;">
                                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                    <?php
                                    $count = 0;
                                    foreach ($estoque as $item):
                                        if ($count >= 5) break;
                                    ?>
                                        <div style="
                                            background: #f8f9fa;
                                            border-radius: 8px;
                                            padding: 10px 15px;
                                            display: flex;
                                            align-items: center;
                                            gap: 10px;
                                            min-width: 200px;
                                        ">
                                            <i class="bx bx-package" style="font-size: 24px; color: #11998e;"></i>
                                            <div>
                                                <div style="font-weight: 600; font-size: 13px;">
                                                    <?php echo htmlspecialchars($item->produto_nome ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                                </div>
                                                <div style="font-size: 12px; color: #888;">
                                                    Qtd: <?php echo $item->quantidade; ?> <?php echo $item->unidade ?? ''; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        $count++;
                                    endforeach;
                                    ?>
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

<?php $this->load->view('tema/footer'); ?>
