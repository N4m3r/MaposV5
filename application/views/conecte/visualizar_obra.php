<!-- Tema Moderno Obras -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<div class="row-fluid obras-modern-container" style="margin-top: 0">
    <div class="span12">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; padding: 25px; color: white; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">
                        <a href="<?= base_url('index.php/mine/obras') ?>" style="color: white; text-decoration: none;"><i class="bx bx-arrow-back"></i> Voltar para Minhas Obras</a>
                    </div>
                    <h2 style="margin: 0; font-size: 28px; font-weight: 700;"><i class="bx bx-building-house"></i> <?= htmlspecialchars($obra->nome) ?></h2>
                    <p style="margin: 10px 0 0; opacity: 0.9;">
                        <i class="bx bx-map"></i> <?= htmlspecialchars($obra->endereco ?? 'Endereço não informado') ?>
                    </p>
                </div>
                <div style="text-align: right;">
                    <?php
                    $statusColors = [
                        'Em Andamento' => ['bg' => '#3498db', 'text' => '#fff'],
                        'Contratada' => ['bg' => '#f39c12', 'text' => '#fff'],
                        'EmExecucao' => ['bg' => '#3498db', 'text' => '#fff'],
                        'Concluída' => ['bg' => '#1dd1a1', 'text' => '#fff'],
                        'Concluida' => ['bg' => '#1dd1a1', 'text' => '#fff'],
                        'Paralisada' => ['bg' => '#e74c3c', 'text' => '#fff'],
                        'Cancelada' => ['bg' => '#95a5a6', 'text' => '#fff'],
                        'Prospeccao' => ['bg' => '#bdc3c7', 'text' => '#333']
                    ];
                    $colors = $statusColors[$obra->status] ?? ['bg' => '#bdc3c7', 'text' => '#333'];
                    ?>
                    <span style="background: <?= $colors['bg'] ?>; color: <?= $colors['text'] ?>; padding: 10px 20px; border-radius: 25px; font-size: 14px; font-weight: 600;">
                        <?= $obra->status ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Progresso Principal -->
        <div style="background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3 style="margin: 0 0 5px 0; color: #333;"><i class="bx bx-chart"></i> Progresso da Obra</h3>
                    <p style="margin: 0; color: #888;">Acompanhe o andamento da sua obra em tempo real</p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 42px; font-weight: 700; color: #667eea;"><?= $obra->percentual_concluido ?? 0 ?>%</div>
                    <div style="font-size: 14px; color: #888;">Concluído</div>
                </div>
            </div>

            <!-- Barra de Progresso -->
            <?php
            $progresso = $obra->percentual_concluido ?? 0;
            if ($progresso < 30) {
                $progressoColor = 'linear-gradient(90deg, #ff6b6b, #ee5a52)';
            } elseif ($progresso < 70) {
                $progressoColor = 'linear-gradient(90deg, #feca57, #ff9f43)';
            } else {
                $progressoColor = 'linear-gradient(90deg, #1dd1a1, #10ac84)';
            }
            ?>
            <div style="background: #e0e0e0; border-radius: 15px; height: 20px; overflow: hidden;">
                <div style="width: <?= $progresso ?>%; height: 100%; background: <?= $progressoColor ?>; border-radius: 15px; transition: width 0.5s ease;"></div>
            </div>

            <!-- Informações do Período -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                <div style="text-align: center;">
                    <div style="font-size: 13px; color: #888; margin-bottom: 5px;"><i class="bx bx-calendar"></i> Data de Início</div>
                    <div style="font-size: 18px; font-weight: 600; color: #333;"><?= isset($obra->data_inicio_contrato) ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : '-' ?></div>
                </div>

                <div style="text-align: center;">
                    <div style="font-size: 13px; color: #888; margin-bottom: 5px;"><i class="bx bx-calendar-check"></i> Previsão de Término</div>
                    <div style="font-size: 18px; font-weight: 600; color: #333;"><?= isset($obra->data_fim_prevista) ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : 'A definir' ?></div>
                </div>

                <div style="text-align: center;">
                    <div style="font-size: 13px; color: #888; margin-bottom: 5px;"><i class="bx bx-time"></i> Tipo de Obra</div>
                    <div style="font-size: 18px; font-weight: 600; color: #333;"><?= $obra->tipo_obra ?? 'Não informado' ?></div>
                </div>

                <div style="text-align: center;">
                    <div style="font-size: 13px; color: #888; margin-bottom: 5px;"><i class="bx bx-barcode"></i> Código</div>
                    <div style="font-size: 18px; font-weight: 600; color: #333;"># <?= $obra->codigo ?></div>
                </div>
            </div>
        </div>

        <!-- Resumo em Cards -->
        <?php if (!empty($etapas) || !empty($equipe) || !empty($atividades)): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 25px;">

            <?php if (!empty($etapas)): ?>
            <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                        <i class="bx bx-list-check"></i>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #333;"><?= count($etapas) ?></div>
                        <div style="font-size: 14px; color: #888;">Etapas Cadastradas</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($equipe)): ?>
            <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #11998e, #38ef7d); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                        <i class="bx bx-group"></i>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #333;"><?= count($equipe) ?></div>
                        <div style="font-size: 14px; color: #888;">Membros na Equipe</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($atividades)): ?>
            <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #f39c12, #e67e22); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                        <i class="bx bx-calendar-check"></i>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #333;"><?= count($atividades) ?></div>
                        <div style="font-size: 14px; color: #888;">Atividades Recentes</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($os_vinculadas)): ?>
            <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #e74c3c, #c0392b); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                        <i class="bx bx-file"></i>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #333;"><?= count($os_vinculadas) ?></div>
                        <div style="font-size: 14px; color: #888;">OS Vinculadas</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Etapas -->
        <?php if (!empty($etapas)): ?>
        <div style="background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 20px 0; color: #333;"><i class="bx bx-list-check" style="color: #667eea;"></i> Etapas da Obra</h4>

            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($etapas as $etapa): ?>
                <?php
                $etapaColors = [
                    'pendente' => ['bg' => '#f5f5f5', 'border' => '#ddd', 'icon' => 'bx-time', 'color' => '#888'],
                    'em_andamento' => ['bg' => '#e3f2fd', 'border' => '#2196f3', 'icon' => 'bx-refresh', 'color' => '#2196f3'],
                    'concluida' => ['bg' => '#e8f5e9', 'border' => '#4caf50', 'icon' => 'bx-check', 'color' => '#4caf50']
                ];
                $etapaStyle = $etapaColors[$etapa->status] ?? $etapaColors['pendente'];
                $etapaProgresso = $etapa->percentual_concluido ?? ($etapa->status == 'concluida' ? 100 : ($etapa->status == 'em_andamento' ? 50 : 0));
                ?>

                <div style="background: <?= $etapaStyle['bg'] ?>; border-left: 4px solid <?= $etapaStyle['border'] ?>; border-radius: 10px; padding: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; color: <?= $etapaStyle['color'] ?>; font-size: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <i class="bx <?= $etapaStyle['icon'] ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #333; font-size: 16px;"><?= htmlspecialchars($etapa->nome) ?></div>
                                <div style="font-size: 13px; color: #666;"><?= htmlspecialchars($etapa->especialidade ?? 'Sem especialidade definida') ?></div>
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <div style="font-size: 20px; font-weight: 700; color: <?= $etapaStyle['color'] ?>;"><?= $etapaProgresso ?>%</div>
                            <div style="font-size: 12px; color: #888;"><?= ucfirst(str_replace('_', ' ', $etapa->status)) ?></div>
                        </div>
                    </div>

                    <?php if ($etapaProgresso > 0): ?>
                    <div style="margin-top: 12px;">
                        <div style="background: rgba(255,255,255,0.5); border-radius: 5px; height: 6px; overflow: hidden;">
                            <div style="width: <?= $etapaProgresso ?>%; height: 100%; background: <?= $etapaStyle['color'] ?>; border-radius: 5px;"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Atividades Recentes -->
        <?php if (!empty($atividades)): ?>
        <div style="background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 20px 0; color: #333;"><i class="bx bx-calendar-check" style="color: #667eea;"></i> Atividades Recentes</h4>

            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach (array_slice($atividades, 0, 5) as $atividade): ?>
                <?php
                $tipoConfig = [
                    'trabalho' => ['icon' => 'bx-wrench', 'color' => '#3498db', 'label' => 'Trabalho'],
                    'impedimento' => ['icon' => 'bx-error', 'color' => '#e74c3c', 'label' => 'Impedimento'],
                    'visita' => ['icon' => 'bx-walk', 'color' => '#1dd1a1', 'label' => 'Visita'],
                    'manutencao' => ['icon' => 'bx-cog', 'color' => '#f39c12', 'label' => 'Manutenção'],
                    'outro' => ['icon' => 'bx-dots-horizontal', 'color' => '#95a5a6', 'label' => 'Outro']
                ];
                $tipo = $tipoConfig[$atividade->tipo] ?? $tipoConfig['outro'];

                $statusConfig = [
                    'agendada' => ['bg' => '#f5f5f5', 'color' => '#666'],
                    'iniciada' => ['bg' => '#e3f2fd', 'color' => '#2196f3'],
                    'pausada' => ['bg' => '#fff3e0', 'color' => '#f39c12'],
                    'concluida' => ['bg' => '#e8f5e9', 'color' => '#4caf50'],
                    'cancelada' => ['bg' => '#ffebee', 'color' => '#e74c3c']
                ];
                $status = $statusConfig[$atividade->status] ?? $statusConfig['agendada'];
                ?>

                <div style="display: flex; gap: 15px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                    <div style="width: 45px; height: 45px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; color: <?= $tipo['color'] ?>; font-size: 22px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); flex-shrink: 0;">
                        <i class="bx <?= $tipo['icon'] ?>"></i>
                    </div>

                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="font-weight: 600; color: #333; margin-bottom: 4px;"><?= htmlspecialchars($atividade->titulo ?? $atividade->descricao ?? 'Atividade sem título') ?></div>
                                <div style="font-size: 13px; color: #666;">
                                    <i class="bx bx-user"></i> <?= htmlspecialchars($atividade->tecnico_nome ?? 'Técnico não informado') ?>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 13px; color: #888; margin-bottom: 4px;"><i class="bx bx-calendar"></i> <?= date('d/m/Y', strtotime($atividade->data_atividade)) ?></div>
                                <span style="background: <?= $status['bg'] ?>; color: <?= $status['color'] ?>; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;"><?= ucfirst($atividade->status) ?></span>
                            </div>
                        </div>

                        <?php if (!empty($atividade->descricao)): ?>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e0e0e0; font-size: 14px; color: #555; line-height: 1.5;">
                            <?= nl2br(htmlspecialchars($atividade->descricao)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($atividades) > 5): ?>
            <div style="text-align: center; margin-top: 20px;">
                <span style="font-size: 13px; color: #888;">+ <?= count($atividades) - 5 ?> atividades anteriores</span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Equipe -->
        <?php if (!empty($equipe)): ?>
        <div style="background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 20px 0; color: #333;"><i class="bx bx-group" style="color: #667eea;"></i> Equipe da Obra</h4>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                <?php foreach ($equipe as $membro): ?>

                <div style="background: #f8f9fa; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 12px;">
                    <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; font-weight: 700; flex-shrink: 0;">
                        <?= substr($membro->tecnico_nome, 0, 1) ?>
                    </div>

                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; color: #333; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($membro->tecnico_nome ?? '-') ?></div>
                        <div style="font-size: 12px; color: #667eea;"><?= htmlspecialchars($membro->funcao ?? 'Técnico') ?></div>
                    </div>

                    <div style="width: 10px; height: 10px; border-radius: 50%; background: <?= $membro->ativo ? '#1dd1a1' : '#bbb' ?>; flex-shrink: 0;" title="<?= $membro->ativo ? 'Ativo' : 'Inativo' ?>"></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Fotos da Obra -->
        <?php if (!empty($fotos)): ?>
        <div style="background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 20px 0; color: #333;"><i class="bx bx-images" style="color: #667eea;"></i> Fotos da Obra</h4>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
                <?php foreach ($fotos as $foto): ?>
                <a href="<?= base_url($foto) ?>" target="_blank" style="display: block; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s;">
                    <img src="<?= base_url($foto) ?>" style="width: 100%; height: 150px; object-fit: cover; display: block;">
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- OS Vinculadas -->
        <?php if (!empty($os_vinculadas)): ?>
        <div style="background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <h4 style="margin: 0 0 20px 0; color: #333;"><i class="bx bx-file" style="color: #667eea;"></i> Ordens de Serviço Vinculadas</h4>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0;">OS #</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0;">Data Inicial</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0;">Data Final</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0;">Status</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0;">Responsável</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($os_vinculadas as $os): ?>
                        <?php
                        $osStatusColors = [
                            'Aberto' => '#e74c3c',
                            'Em Andamento' => '#3498db',
                            'Finalizado' => '#1dd1a1',
                            'Faturado' => '#95a5a6',
                            'Cancelado' => '#7f8c8d'
                        ];
                        $osColor = $osStatusColors[$os->status] ?? '#95a5a6';
                        ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;"><strong># <?= $os->idOs ?></strong></td>
                            <td style="padding: 12px;"><?= date('d/m/Y', strtotime($os->dataInicial)) ?></td>
                            <td style="padding: 12px;"><?= isset($os->dataFinal) ? date('d/m/Y', strtotime($os->dataFinal)) : '-' ?></td>
                            <td style="padding: 12px;">
                                <span style="background: <?= $osColor ?>20; color: <?= $osColor ?>; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    <?= $os->status ?>
                                </span>
                            </td>
                            <td style="padding: 12px;"><?= htmlspecialchars($os->responsavel ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Observações -->
        <?php if (!empty($obra->observacoes)): ?>
        <div style="background: linear-gradient(135deg, #fff9e6, #fff3cd); border-radius: 15px; padding: 25px; margin-bottom: 25px; border-left: 4px solid #ffc107;">
            <h4 style="margin: 0 0 15px 0; color: #856404;"><i class="bx bx-info-circle"></i> Observações Importantes</h4>
            <div style="color: #856404; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($obra->observacoes)) ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
