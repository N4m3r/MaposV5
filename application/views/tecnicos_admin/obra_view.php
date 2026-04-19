<?php
// Status labels e cores
$statusLabels = [
    'Prospeccao' => 'Prospecção',
    'Orcamentacao' => 'Orçamentação',
    'Contratada' => 'Contratada',
    'EmExecucao' => 'Em Execução',
    'Paralisada' => 'Paralisada',
    'Finalizada' => 'Finalizada',
    'Entregue' => 'Entregue',
    'Garantia' => 'Garantia',
];

$statusColors = [
    'Prospeccao' => '#9e9e9e',
    'Orcamentacao' => '#ff9800',
    'Contratada' => '#2196f3',
    'EmExecucao' => '#4caf50',
    'Paralisada' => '#f44336',
    'Finalizada' => '#9c27b0',
    'Entregue' => '#00bcd4',
    'Garantia' => '#795548',
];

$etapaStatusLabels = [
    'pendente' => 'Pendente',
    'em_andamento' => 'Em Andamento',
    'concluida' => 'Concluída',
    'atrasada' => 'Atrasada',
];

$etapaStatusColors = [
    'pendente' => ['bg' => '#f5f5f5', 'color' => '#666'],
    'em_andamento' => ['bg' => '#e3f2fd', 'color' => '#1976d2'],
    'concluida' => ['bg' => '#e8f5e9', 'color' => '#388e3c'],
    'atrasada' => ['bg' => '#ffebee', 'color' => '#c62828'],
];
?>

<!-- Visualizar Obra - Versão Completa -->
<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="bx bx-building"></i></span>
        <h5>Detalhes da Obra</h5>
        <div class="buttons">
            <a href="<?= site_url('tecnicos_admin/obras') ?>" class="button btn btn-mini btn-default">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Voltar</span>
            </a>
            <a href="<?= site_url('tecnicos_admin/editar_obra/' . $obra->id) ?>" class="button btn btn-mini btn-warning">
                <span class="button__icon"><i class="bx bx-edit"></i></span>
                <span class="button__text2">Editar</span>
            </a>
        </div>
    </div>

    <?php if (!empty($obra)): ?>
        <!-- Header da Obra -->
        <div class="obra-header-card" style="margin: 20px 0; padding: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; color: white;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                <div>
                    <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 8px;">
                        <i class="bx bx-hash"></i> <?= $obra->codigo ?? '#' . $obra->id ?>
                    </div>
                    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700;">
                        <?= htmlspecialchars($obra->nome ?? 'Sem nome', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                    </h2>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <span style="padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(255,255,255,0.2); color: white;">
                            <?= $statusLabels[$obra->status] ?? $obra->status ?>
                        </span>
                        <span style="opacity: 0.9;">
                            <i class="bx bx-calendar"></i> Início: <?= isset($obra->data_inicio_contrato) ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : '-' ?>
                        </span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 3rem; font-weight: 700;"><?= $obra->percentual_concluido ?? 0 ?>%</div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Concluído</div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div style="margin-top: 20px;">
                <div style="height: 10px; background: rgba(255,255,255,0.2); border-radius: 5px; overflow: hidden;">
                    <div style="height: 100%; width: <?= $obra->percentual_concluido ?? 0 ?>%; background: white; border-radius: 5px; transition: width 0.5s ease;"></div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <!-- Coluna Principal -->
            <div class="span8">
                <!-- Informações da Obra -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: #f8f9fa;">
                        <span class="icon"><i class="bx bx-info-circle"></i></span>
                        <h5>Informações da Obra</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;"><i class="bx bx-user"></i> Cliente</div>
                                    <div style="font-weight: 600; color: #333;"><?= htmlspecialchars($obra->cliente_nome ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></div>
                                </div>
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;"><i class="bx bx-tag"></i> Tipo de Obra</div>
                                    <div style="font-weight: 600; color: #333;"><?= $obra->tipo_obra ?? 'Não informado' ?></div>
                                </div>
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;"><i class="bx bx-map-pin"></i> Endereço</div>
                                    <div style="font-weight: 600; color: #333;"><?= htmlspecialchars($obra->endereco ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;"><i class="bx bx-calendar-check"></i> Previsão de Término</div>
                                    <div style="font-weight: 600; color: #333;"><?= isset($obra->data_fim_prevista) ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : '-' ?></div>
                                </div>
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;"><i class="bx bx-task"></i> Total de Etapas</div>
                                    <div style="font-weight: 600; color: #333;"><?= count($etapas ?? []) ?></div>
                                </div>
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;"><i class="bx bx-clipboard"></i> Total de OS</div>
                                    <div style="font-weight: 600; color: #333;"><?= count($os_vinculadas ?? []) ?></div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($obra->observacoes)): ?>
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;"><i class="bx bx-note"></i> Observações</div>
                            <div style="color: #555; line-height: 1.6;"><?= nl2br(htmlspecialchars($obra->observacoes, ENT_COMPAT | ENT_HTML5, 'UTF-8')) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Etapas da Obra -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span class="icon"><i class="bx bx-list-check"></i></span>
                            <h5>Etapas da Obra <span class="label label-info" style="margin-left: 10px;"><?= count($etapas ?? []) ?></span></h5>
                        </div>
                        <a href="#modal-etapa" data-toggle="modal" class="btn btn-mini btn-success"><i class="bx bx-plus"></i> Adicionar</a>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <?php if (!empty($etapas)): ?>
                            <div class="etapas-list" style="display: flex; flex-direction: column; gap: 15px;">
                                <?php foreach ($etapas as $etapa):
                                    $etapaStatus = $etapa->status ?? 'pendente';
                                    $percentual = $etapa->percentual_concluido ?? 0;
                                    $corBarra = $percentual >= 100 ? '#4caf50' : ($percentual >= 50 ? '#ff9800' : '#2196f3');
                                ?>
                                <div class="etapa-item" style="padding: 20px; background: #f8f9fa; border-radius: 14px; border-left: 4px solid <?= $corBarra ?>;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 700; color: #333; font-size: 1.1rem;">
                                                <?= htmlspecialchars($etapa->nome ?? 'Etapa ' . $etapa->numero_etapa, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                            </div>
                                            <?php if (!empty($etapa->descricao)): ?>
                                            <div style="font-size: 0.85rem; color: #666; margin-top: 5px;"><?= htmlspecialchars($etapa->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div style="display: flex; gap: 5px;">
                                            <span class="etapa-status" style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: <?= $etapaStatusColors[$etapaStatus]['bg'] ?? '#f5f5f5' ?>; color: <?= $etapaStatusColors[$etapaStatus]['color'] ?? '#666' ?>;">
                                                <?= $etapaStatusLabels[$etapaStatus] ?? ucfirst($etapaStatus) ?>
                                            </span>
                                            <button type="button" class="btn btn-mini" onclick="editarEtapa(<?= $etapa->id ?>)"><i class="bx bx-edit"></i></button>
                                            <button type="button" class="btn btn-mini btn-danger" onclick="excluirEtapa(<?= $etapa->id ?>)"><i class="bx bx-trash"></i></button>
                                        </div>
                                    </div>

                                    <!-- Progresso da Etapa -->
                                    <div style="margin: 10px 0;">
                                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #666; margin-bottom: 5px;">
                                            <span>Progresso</span>
                                            <span><?= $percentual ?>%</span>
                                        </div>
                                        <div style="height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                                            <div style="height: 100%; width: <?= $percentual ?>%; background: <?= $corBarra ?>; border-radius: 3px; transition: width 0.3s;"></div>
                                        </div>
                                    </div>

                                    <!-- Datas e Ações -->
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; font-size: 0.85rem; color: #888;">
                                        <div>
                                            <?php if (!empty($etapa->data_inicio_prevista)): ?>
                                            <span style="margin-right: 15px;"><i class="bx bx-calendar"></i> Início: <?= date('d/m/Y', strtotime($etapa->data_inicio_prevista)) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($etapa->data_fim_prevista)): ?>
                                            <span><i class="bx bx-calendar-check"></i> Término: <?= date('d/m/Y', strtotime($etapa->data_fim_prevista)) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="display: flex; gap: 5px;">
                                            <?php if ($etapaStatus !== 'concluida'): ?>
                                            <button type="button" class="btn btn-mini btn-success" onclick="atualizarStatusEtapa(<?= $etapa->id ?>, 'em_andamento')"><i class="bx bx-play"></i> Iniciar</button>
                                            <button type="button" class="btn btn-mini btn-primary" onclick="atualizarStatusEtapa(<?= $etapa->id ?>, 'concluida')"><i class="bx bx-check"></i> Concluir</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: #888;">
                                <div style="font-size: 3rem; margin-bottom: 15px;"><i class="bx bx-list-ul"></i></div>
                                <p>Nenhuma etapa cadastrada</p>
                                <a href="#modal-etapa" data-toggle="modal" class="btn btn-success" style="margin-top: 10px;"><i class="bx bx-plus"></i> Adicionar Etapa</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- OS Vinculadas -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span class="icon"><i class="bx bx-clipboard"></i></span>
                            <h5>Ordens de Serviço Vinculadas <span class="badge badge-info"><?= count($os_vinculadas ?? []) ?></span></h5>
                        </div>
                        <a href="#modal-vincular-os" data-toggle="modal" class="btn btn-mini btn-success"><i class="bx bx-link"></i> Vincular OS</a>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <?php if (!empty($os_vinculadas)): ?>
                            <div class="os-list" style="display: flex; flex-direction: column; gap: 12px;">
                                <?php foreach ($os_vinculadas as $os):
                                    $osStatusColors = [
                                        'Aberto' => '#4caf50',
                                        'Em Andamento' => '#2196f3',
                                        'Finalizado' => '#9c27b0',
                                        'Cancelado' => '#f44336',
                                        'Orçamento' => '#ff9800',
                                    ];
                                    $osCor = $osStatusColors[$os->status] ?? '#888';
                                ?>
                                <div class="os-item" style="display: flex; align-items: center; gap: 12px; padding: 15px; background: #f8f9fa; border-radius: 12px;">
                                    <div style="width: 45px; height: 45px; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">#<?= $os->idOs ?></div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #333;"><?= htmlspecialchars($os->nomeCliente ?? 'Cliente', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></div>
                                        <div style="font-size: 0.8rem; color: #888;">
                                            <i class="bx bx-calendar"></i> <?= date('d/m/Y', strtotime($os->dataInicial)) ?>
                                            <span style="margin-left: 10px; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; background: <?= $osCor ?>; color: white;"><?= $os->status ?></span>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="<?= site_url('os/visualizar/' . $os->idOs) ?>" class="btn btn-mini btn-info" title="Visualizar OS"><i class="bx bx-show"></i></a>
                                        <form method="post" action="<?= site_url('tecnicos_admin/desvincular_os_obra') ?>" style="display: inline;" onsubmit="return confirm('Deseja realmente desvincular esta OS da obra?');">
                                            <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
                                            <input type="hidden" name="os_id" value="<?= $os->idOs ?>">
                                            <button type="submit" class="btn btn-mini btn-danger" title="Desvincular OS"><i class="bx bx-unlink"></i></button>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: #888;">
                                <div style="font-size: 3rem; margin-bottom: 15px;"><i class="bx bx-clipboard"></i></div>
                                <p>Nenhuma OS vinculada a esta obra</p>
                                <a href="#modal-vincular-os" data-toggle="modal" class="btn btn-success" style="margin-top: 10px;"><i class="bx bx-link"></i> Vincular OS</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Timeline / Atividades -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden;">
                    <div class="widget-title" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span class="icon"><i class="bx bx-timeline"></i></span>
                            <h5>Timeline da Obra</h5>
                        </div>
                        <a href="#modal-atividade" data-toggle="modal" class="btn btn-mini btn-primary"><i class="bx bx-plus"></i> Registrar Atividade</a>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <div id="timeline-obra" style="max-height: 400px; overflow-y: auto;">
                            <p style="text-align: center; color: #888; padding: 20px;">Carregando atividades...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral -->
            <div class="span4">
                <!-- Equipe -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <span class="icon" style="color: white;"><i class="bx bx-group"></i></span>
                        <h5 style="color: white;">Equipe <span class="label" style="margin-left: 10px; background: rgba(255,255,255,0.3); color: white;"><?= count($equipe ?? []) ?></span></h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <?php if (!empty($equipe)): ?>
                            <div class="equipe-list" style="display: flex; flex-direction: column; gap: 12px;">
                                <?php foreach ($equipe as $membro): ?>
                                <div class="equipe-item" style="display: flex; align-items: center; gap: 12px; padding: 15px; background: #f8f9fa; border-radius: 12px;">
                                    <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.1rem;">
                                        <?= strtoupper(substr($membro->tecnico_nome ?? 'T', 0, 1)) ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #333;"><?= htmlspecialchars($membro->tecnico_nome ?? 'Técnico', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></div>
                                        <div style="font-size: 0.8rem; color: #888;">
                                            <?= $membro->funcao ?? 'Técnico' ?>
                                            <?php if (!empty($membro->nivel_tecnico)): ?>• Nível <?= $membro->nivel_tecnico ?><?php endif; ?>
                                        </div>
                                    </div>
                                    <form method="post" action="<?= site_url('tecnicos_admin/remover_tecnico_equipe') ?>" style="display: inline;" onsubmit="return confirm('Deseja remover este técnico da equipe?');">
                                        <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
                                        <input type="hidden" name="tecnico_id" value="<?= $membro->tecnico_id ?>">
                                        <button type="submit" class="btn btn-mini btn-danger" title="Remover da equipe"><i class="bx bx-x"></i></button>
                                    </form>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 30px; color: #888;">
                                <div style="font-size: 2.5rem; margin-bottom: 10px;"><i class="bx bx-user-x"></i></div>
                                <p style="font-size: 0.9rem;">Nenhum técnico alocado</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: #f8f9fa;">
                        <span class="icon"><i class="bx bx-bolt"></i></span>
                        <h5>Ações Rápidas</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <a href="#modal-equipe" data-toggle="modal" class="quick-action" style="display: flex; align-items: center; gap: 12px; padding: 15px; background: #e3f2fd; border-radius: 12px; text-decoration: none; color: #1976d2;">
                                <i class="bx bx-user-plus" style="font-size: 1.3rem;"></i>
                                <span style="font-weight: 600;">Alocar Técnico</span>
                            </a>
                            <a href="<?= site_url('os/adicionar?obra_id=' . $obra->id) ?>" class="quick-action" style="display: flex; align-items: center; gap: 12px; padding: 15px; background: #fce4ec; border-radius: 12px; text-decoration: none; color: #c2185b;">
                                <i class="bx bx-file" style="font-size: 1.3rem;"></i>
                                <span style="font-weight: 600;">Criar OS para Obra</span>
                            </a>
                            <a href="#modal-materiais" data-toggle="modal" class="quick-action" style="display: flex; align-items: center; gap: 12px; padding: 15px; background: #fff3e0; border-radius: 12px; text-decoration: none; color: #f57c00;">
                                <i class="bx bx-package" style="font-size: 1.3rem;"></i>
                                <span style="font-weight: 600;">Materiais</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Progresso -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden;">
                    <div class="widget-title" style="background: #f8f9fa;">
                        <span class="icon"><i class="bx bx-chart-pie"></i></span>
                        <h5>Estatísticas</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px; text-align: center;">
                        <div id="chart-etapas" style="height: 200px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Adicionar Etapa -->
        <div id="modal-etapa" class="modal hide fade" tabindex="-1" role="dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5><i class="bx bx-plus-circle"></i> Adicionar Etapa</h5>
            </div>
            <form action="<?= site_url('tecnicos_admin/adicionar_etapa') ?>" method="post">
                <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
                <div class="modal-body">
                    <div class="control-group">
                        <label class="control-label">Nome da Etapa *</label>
                        <div class="controls">
                            <input type="text" name="nome" class="span12" placeholder="Ex: Preparação, Instalação, Testes" required>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Data Início Prevista</label>
                                <div class="controls">
                                    <input type="date" name="data_inicio_prevista" class="span12">
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Data Fim Prevista</label>
                                <div class="controls">
                                    <input type="date" name="data_fim_prevista" class="span12">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Descrição</label>
                        <div class="controls">
                            <textarea name="descricao" class="span12" rows="3" placeholder="Descrição detalhada da etapa"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Salvar Etapa</button>
                </div>
            </form>
        </div>

        <!-- Modal: Editar Etapa -->
        <div id="modal-editar-etapa" class="modal hide fade" tabindex="-1" role="dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5><i class="bx bx-edit"></i> Editar Etapa</h5>
            </div>
            <form id="form-editar-etapa" action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="etapa_id" id="edit-etapa-id">
                    <div class="control-group">
                        <label class="control-label">Nome da Etapa *</label>
                        <div class="controls">
                            <input type="text" name="nome" id="edit-etapa-nome" class="span12" required>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Status</label>
                        <div class="controls">
                            <select name="status" id="edit-etapa-status" class="span12">
                                <option value="pendente">Pendente</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="concluida">Concluída</option>
                                <option value="atrasada">Atrasada</option>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">% Concluído</label>
                                <div class="controls">
                                    <input type="number" name="percentual_concluido" id="edit-etapa-percentual" class="span12" min="0" max="100">
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Data Fim Prevista</label>
                                <div class="controls">
                                    <input type="date" name="data_fim_prevista" id="edit-etapa-data-fim" class="span12">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Descrição</label>
                        <div class="controls">
                            <textarea name="descricao" id="edit-etapa-descricao" class="span12" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Atualizar</button>
                </div>
            </form>
        </div>

        <!-- Modal: Alocar Técnico -->
        <div id="modal-equipe" class="modal hide fade" tabindex="-1" role="dialog" style="width: 600px; margin-left: -300px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">×</button>
                <h5 style="color: white;"><i class="bx bx-user-plus"></i> Alocar Técnico à Obra</h5>
            </div>
            <form action="<?= site_url('tecnicos_admin/alocar_tecnico') ?>" method="post">
                <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i> Selecione um técnico disponível para alocar a esta obra.
                    </div>
                    <div class="control-group">
                        <label class="control-label">Técnico *</label>
                        <div class="controls">
                            <select name="tecnico_id" id="select-tecnico" class="span12" required>
                                <option value="">-- Carregando técnicos disponíveis... --</option>
                            </select>
                        </div>
                        <span class="help-block" id="msg-tecnico" style="color: #666; font-size: 0.85rem;">
                            <i class="bx bx-loader bx-spin"></i> Carregando técnicos...
                        </span>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Função na Obra</label>
                                <div class="controls">
                                    <select name="funcao" class="span12">
                                        <option value="Técnico">Técnico</option>
                                        <option value="Encarregado">Encarregado</option>
                                        <option value="Auxiliar">Auxiliar</option>
                                        <option value="Responsável Técnico">Responsável Técnico</option>
                                        <option value="Engenheiro">Engenheiro</option>
                                        <option value="Arquiteto">Arquiteto</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Nível de Experiência</label>
                                <div class="controls">
                                    <select name="nivel_tecnico" class="span12">
                                        <option value="">-- Selecione --</option>
                                        <option value="1">Nível 1 - Júnior</option>
                                        <option value="2">Nível 2 - Pleno</option>
                                        <option value="3">Nível 3 - Sênior</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btn-alocar" disabled><i class="bx bx-save"></i> Alocar Técnico</button>
                </div>
            </form>
        </div>

        <!-- Modal: Vincular OS -->
        <div id="modal-vincular-os" class="modal hide fade" tabindex="-1" role="dialog" style="width: 800px; margin-left: -400px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">×</button>
                <h5 style="color: white;"><i class="bx bx-link"></i> Vincular OS à Obra</h5>
            </div>
            <form action="<?= site_url('tecnicos_admin/vincular_os_obra') ?>" method="post">
                <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    <!-- Busca -->
                    <div class="row-fluid" style="margin-bottom: 15px;">
                        <div class="span8">
                            <div class="control-group" style="margin-bottom: 0;">
                                <div class="controls">
                                    <div class="input-append span12" style="margin-left: 0;">
                                        <input type="text" id="buscar-os-termo" class="span10" placeholder="Buscar por número da OS, cliente ou CNPJ...">
                                        <button type="button" class="btn btn-info" id="btn-buscar-os"><i class="bx bx-search"></i> Buscar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="span4">
                            <select id="filtro-status-os" class="span12">
                                <option value="">Todos os Status</option>
                                <option value="Aberto">Aberto</option>
                                <option value="Em Andamento">Em Andamento</option>
                                <option value="Orçamento">Orçamento</option>
                                <option value="Finalizado">Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <!-- Resumo -->
                    <div class="alert alert-info" style="padding: 10px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span><i class="bx bx-info-circle"></i> <span id="total-os-disponiveis">0</span> OS disponíveis para vincular</span>
                            <span class="badge badge-info" id="badge-selecionadas">0 selecionada(s)</span>
                        </div>
                    </div>

                    <!-- Lista de OS -->
                    <div id="lista-os-container" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                        <div id="os-loading" style="text-align: center; padding: 40px;">
                            <i class="bx bx-loader bx-spin" style="font-size: 2rem; color: #667eea;"></i>
                            <p style="margin-top: 10px; color: #666;">Carregando OS disponíveis...</p>
                        </div>
                        <div id="os-vazia" style="display: none; text-align: center; padding: 40px;">
                            <i class="bx bx-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p style="margin-top: 15px; color: #666;">Nenhuma OS disponível para vincular.</p>
                            <p style="font-size: 0.85rem; color: #999;">Todas as OS já estão vinculadas a outras obras.</p>
                        </div>
                        <div id="os-lista" style="display: none; max-height: 300px; overflow-y: auto;">
                            <!-- OS serão inseridas aqui via JavaScript -->
                        </div>
                    </div>

                    <!-- OS Selecionada (hidden) -->
                    <input type="hidden" name="os_id" id="os-selecionada-id" required>

                    <!-- Preview da OS Selecionada -->
                    <div id="os-preview" style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; display: none;">
                        <h6 style="margin: 0 0 10px 0; color: #333;"><i class="bx bx-check-circle" style="color: #4caf50;"></i> OS Selecionada</h6>
                        <div id="os-preview-conteudo"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" type="button">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btn-vincular-os" disabled>
                        <i class="bx bx-link"></i> Vincular à Obra
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal: Registrar Atividade -->
        <div id="modal-atividade" class="modal hide fade" tabindex="-1" role="dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5><i class="bx bx-plus-circle"></i> Registrar Atividade</h5>
            </div>
            <form action="<?= site_url('tecnicos_admin/adicionar_comentario') ?>" method="post">
                <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
                <div class="modal-body">
                    <div class="control-group">
                        <label class="control-label">Tipo</label>
                        <div class="controls">
                            <select name="tipo" class="span12">
                                <option value="comentario">Comentário</option>
                                <option value="atualizacao">Atualização</option>
                                <option value="problema">Problema</option>
                                <option value="solucao">Solução</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Descrição *</label>
                        <div class="controls">
                            <textarea name="descricao" class="span12" rows="4" placeholder="Descreva a atividade, atualização ou observação..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Registrar</button>
                </div>
            </form>
        </div>

        <!-- Modal: Gerenciar Materiais -->
        <div id="modal-materiais" class="modal hide fade" tabindex="-1" role="dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5><i class="bx bx-package"></i> Materiais da Obra</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bx bx-info-circle"></i> Em desenvolvimento - Funcionalidade de materiais em breve.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal">Fechar</button>
            </div>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 80px 20px;">
            <div style="font-size: 5rem; color: #e0e0e0; margin-bottom: 20px;"><i class="bx bx-error-circle"></i></div>
            <h3 style="color: #666; font-weight: 400; margin-bottom: 10px;">Obra não encontrada</h3>
            <p style="color: #999; margin-bottom: 25px;">A obra solicitada não existe ou foi removida.</p>
            <a href="<?= site_url('tecnicos_admin/obras') ?>" class="button btn btn-success">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Voltar para Obras</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Variáveis globais
var osDisponiveis = [];
var osSelecionada = null;

$(document).ready(function() {
    // Carregar Timeline
    carregarTimeline();

    // Carregar OS disponíveis ao abrir modal
    $('#modal-vincular-os').on('show', function() {
        carregarOsDisponiveis();
    });

    // Carregar técnicos ao abrir modal
    $('#modal-equipe').on('show', function() {
        carregarTecnicosDisponiveis();
    });

    // Busca de OS ao digitar
    $('#buscar-os-termo').on('keyup', function(e) {
        if (e.which === 13) {
            carregarOsDisponiveis($(this).val());
        }
    });

    // Botão buscar OS
    $('#btn-buscar-os').on('click', function() {
        carregarOsDisponiveis($('#buscar-os-termo').val());
    });

    // Filtro de status
    $('#filtro-status-os').on('change', function() {
        filtrarOsLista($(this).val());
    });

    // Gráfico de Etapas
    var options = {
        series: [<?= count(array_filter($etapas ?? [], fn($e) => $e->status === 'concluida')) ?>, <?= count(array_filter($etapas ?? [], fn($e) => $e->status === 'em_andamento')) ?>, <?= count(array_filter($etapas ?? [], fn($e) => $e->status === 'pendente')) ?>],
        labels: ['Concluídas', 'Em Andamento', 'Pendentes'],
        chart: { type: 'donut', height: 200 },
        colors: ['#4caf50', '#2196f3', '#9e9e9e'],
        legend: { show: false },
        dataLabels: { enabled: false }
    };
    var chart = new ApexCharts(document.querySelector("#chart-etapas"), options);
    chart.render();
});

function carregarTecnicosDisponiveis() {
    var select = $('#select-tecnico');
    var msgElement = $('#msg-tecnico');

    select.prop('disabled', true).html('<option value="">Carregando técnicos...</option>');
    msgElement.html('<i class="bx bx-loader bx-spin"></i> Buscando técnicos disponíveis...');

    $.ajax({
        url: '<?= site_url("tecnicos_admin/buscar_tecnicos_disponiveis") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            select.empty();

            if (response.success && response.tecnicos && response.tecnicos.length > 0) {
                select.append('<option value="">-- Selecione um técnico --</option>');

                response.tecnicos.forEach(function(tecnico) {
                    var info = tecnico.nome;
                    if (tecnico.email) info += ' | ' + tecnico.email;
                    if (tecnico.telefone) info += ' | ' + tecnico.telefone;

                    select.append('<option value="' + tecnico.idUsuarios + '" data-nome="' + tecnico.nome + '" data-email="' + (tecnico.email || '') + '" data-telefone="' + (tecnico.telefone || '') + '" data-nivel="' + (tecnico.nivel_tecnico || '') + '" data-especialidades="' + (tecnico.especialidades || '') + '"\u003e' + info + '</option>');
                });

                select.prop('disabled', false);
                $('#btn-alocar').prop('disabled', false);
                msgElement.html('<i class="bx bx-check" style="color: #4caf50;"></i> ' + response.tecnicos.length + ' técnico(s) disponível(is)');
            } else {
                select.append('<option value="" disabled>Nenhum técnico disponível</option>');
                msgElement.html('<i class="bx bx-error" style="color: #ff9800;"></i> Nenhum técnico encontrado');
            }
        },
        error: function() {
            select.html('<option value="" disabled>Erro ao carregar técnicos</option>');
            msgElement.html('<i class="bx bx-error" style="color: #f44336;"></i> Erro ao buscar técnicos');
        }
    });
}

function carregarOsDisponiveis(termo = '') {
    $('#os-loading').show();
    $('#os-vazia').hide();
    $('#os-lista').hide();

    $.ajax({
        url: '<?= site_url("tecnicos_admin/buscar_os_disponiveis_simples") ?>',
        type: 'GET',
        data: { termo: termo },
        dataType: 'json',
        success: function(response) {
            $('#os-loading').hide();

            if (response.success && response.os && response.os.length > 0) {
                osDisponiveis = response.os;
                renderizarOsLista(response.os);
                $('#total-os-disponiveis').text(response.os.length);
                $('#os-lista').show();
            } else {
                osDisponiveis = [];
                $('#total-os-disponiveis').text('0');
                $('#os-vazia').show();
            }
        },
        error: function() {
            $('#os-loading').hide();
            $('#os-vazia').show();
            $('#total-os-disponiveis').text('0');
        }
    });
}

function renderizarOsLista(osList) {
    var container = $('#os-lista');
    container.empty();

    var coresStatus = {
        'Aberto': '#4caf50',
        'Em Andamento': '#2196f3',
        'Finalizado': '#9c27b0',
        'Cancelado': '#f44336',
        'Orçamento': '#ff9800',
        'Aprovado': '#00bcd4',
        'Faturado': '#795548'
    };

    osList.forEach(function(os, index) {
        var corStatus = coresStatus[os.status] || '#888';
        var docFormatado = os.documento ? formatarCNPJ(os.documento) : 'Não informado';
        var dataFormatada = os.dataInicial ? new Date(os.dataInicial).toLocaleDateString('pt-BR') : '-';

        var html = '<div class="os-item" data-id="' + os.idOs + '" data-status="' + os.status + '" style="' +
            'padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; transition: all 0.2s;' +
            'display: flex; align-items: center; gap: 15px;"' +
            'onclick="selecionarOS(' + os.idOs + ')" onmouseover="this.style.background=\'#f0f8ff\'" onmouseout="if(!this.classList.contains(\'selecionada\')) this.style.background=\'\'"\u003e';

        html += '<div style="width: 50px; height: 50px; border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; flex-shrink: 0;"\u003e#' + os.idOs + '</div\u003e';

        html += '<div style="flex: 1;"\u003e';
        html += '<div style="font-weight: 600; color: #333; margin-bottom: 3px;"\u003e' + os.nomeCliente + '</div\u003e';
        html += '<div style="font-size: 0.8rem; color: #666;"\u003e';
        html += '<i class="bx bx-id-card"></i> CNPJ: ' + docFormatado + ' | ';
        html += '<i class="bx bx-calendar"></i> ' + dataFormatada;
        html += '</div\u003e';
        html += '</div\u003e';

        html += '<span style="padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: ' + corStatus + '20; color: ' + corStatus + '; white-space: nowrap;"\u003e' + os.status + '</span\u003e';

        html += '<div class="os-radio" style="width: 24px; height: 24px; border: 2px solid #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"\u003e';
        html += '<i class="bx bx-check" style="color: #4caf50; display: none; font-size: 1rem;"\u003e</i>';
        html += '</div\u003e';

        html += '</div\u003e';
        container.append(html);
    });
}

function filtrarOsLista(status) {
    if (!status) {
        $('.os-item').show();
        return;
    }

    $('.os-item').each(function() {
        var osStatus = $(this).data('status');
        if (osStatus === status) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function selecionarOS(osId) {
    // Remove seleção anterior
    $('.os-item').removeClass('selecionada').css('background', '');
    $('.os-item .os-radio').css({borderColor: '#ddd', background: ''});
    $('.os-item .os-radio i').hide();

    // Adiciona seleção atual
    var item = $('.os-item[data-id="' + osId + '"]');
    item.addClass('selecionada').css('background', '#e3f2fd');
    item.find('.os-radio').css({borderColor: '#4caf50', background: '#4caf50'});
    item.find('.os-radio i').show().css('color', 'white');

    // Busca os dados da OS
    var os = osDisponiveis.find(function(o) { return o.idOs == osId; });
    if (os) {
        osSelecionada = os;
        mostrarPreviewOS(os);
    }
}

function mostrarPreviewOS(os) {
    $('#os-selecionada-id').val(os.idOs);
    $('#btn-vincular-os').prop('disabled', false);
    $('#badge-selecionadas').text('1 selecionada').removeClass('badge-info').addClass('badge-success');

    var coresStatus = {
        'Aberto': '#4caf50',
        'Em Andamento': '#2196f3',
        'Finalizado': '#9c27b0',
        'Cancelado': '#f44336',
        'Orçamento': '#ff9800'
    };

    var corStatus = coresStatus[os.status] || '#888';
    var docFormatado = os.documento ? formatarCNPJ(os.documento) : 'Não informado';
    var dataFormatada = os.dataInicial ? new Date(os.dataInicial).toLocaleDateString('pt-BR') : '-';

    var html = '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;"\u003e';
    html += '<div><strong>OS #:</strong> ' + os.idOs + '</div>';
    html += '<div><strong>Status:</strong> <span style="color: ' + corStatus + '; font-weight: 600;">' + os.status + '</span></div>';
    html += '<div><strong>Cliente:</strong> ' + os.nomeCliente + '</div>';
    html += '<div><strong>CNPJ:</strong> ' + docFormatado + '</div>';
    html += '<div><strong>Data:</strong> ' + dataFormatada + '</div>';
    if (os.valorTotal) {
        html += '<div><strong>Valor:</strong> R$ ' + parseFloat(os.valorTotal).toLocaleString('pt-BR', {minimumFractionDigits: 2}) + '</div>';
    }
    html += '</div>';

    $('#os-preview-conteudo').html(html);
    $('#os-preview').show();
}

function formatarCNPJ(cnpj) {
    if (!cnpj) return '';
    var numeros = cnpj.replace(/\D/g, '');
    if (numeros.length === 14) {
        return numeros.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
    return cnpj;
}

function carregarTimeline() {
    var container = $('#timeline-obra');
    container.html('<p style="text-align: center; color: #888; padding: 20px;">Carregando...</p>');

    $.ajax({
        url: '<?= site_url("tecnicos_admin/buscar_atividades_obra/" . ($obra->id ?? 0)) ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && (response.atividades.length > 0 || response.etapas_concluidas.length > 0)) {
                var html = '<div class="timeline" style="position: relative; padding-left: 30px;">';

                // Combinar atividades e etapas
                var itens = [];

                response.atividades.forEach(function(a) {
                    itens.push({
                        tipo: 'atividade',
                        data: a.created_at,
                        titulo: a.usuario_nome,
                        descricao: a.descricao,
                        categoria: a.tipo
                    });
                });

                response.etapas_concluidas.forEach(function(e) {
                    itens.push({
                        tipo: 'etapa',
                        data: e.data_fim_real,
                        titulo: 'Etapa Concluída',
                        descricao: e.nome,
                        categoria: 'conclusao'
                    });
                });

                // Ordenar por data
                itens.sort(function(a, b) {
                    return new Date(b.data) - new Date(a.data);
                });

                itens.forEach(function(item) {
                    var icones = {
                        'comentario': 'bx-comment',
                        'atualizacao': 'bx-refresh',
                        'problema': 'bx-error',
                        'solucao': 'bx-check',
                        'conclusao': 'bx-flag'
                    };
                    var cores = {
                        'comentario': '#2196f3',
                        'atualizacao': '#ff9800',
                        'problema': '#f44336',
                        'solucao': '#4caf50',
                        'conclusao': '#9c27b0'
                    };

                    var dataFormatada = new Date(item.data).toLocaleDateString('pt-BR');
                    var icone = icones[item.categoria] || 'bx-circle';
                    var cor = cores[item.categoria] || '#888';

                    html += '<div class="timeline-item" style="position: relative; margin-bottom: 20px;">';
                    html += '<div style="position: absolute; left: -30px; width: 20px; height: 20px; background: ' + cor + '; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 10px;"><i class="bx ' + icone + '"></i></div>';
                    html += '<div style="background: #f8f9fa; padding: 12px; border-radius: 8px;">';
                    html += '<div style="font-weight: 600; color: #333;">' + item.titulo + '</div>';
                    html += '<div style="font-size: 0.85rem; color: #666; margin-top: 5px;">' + item.descricao + '</div>';
                    html += '<div style="font-size: 0.75rem; color: #999; margin-top: 5px;"><i class="bx bx-calendar"></i> ' + dataFormatada + '</div>';
                    html += '</div></div>';
                });

                html += '</div>';
                container.html(html);
            } else {
                container.html('<p style="text-align: center; color: #888; padding: 20px;">Nenhuma atividade registrada.<br><a href="#modal-atividade" data-toggle="modal">Clique aqui para adicionar</a></p>');
            }
        },
        error: function() {
            container.html('<p style="text-align: center; color: #888; padding: 20px;">Erro ao carregar timeline.</p>');
        }
    });
}

function editarEtapa(etapaId) {
    // Buscar dados da etapa via AJAX e preencher o modal
    $.ajax({
        url: '<?= site_url("tecnicos_admin/buscar_etapa/") ?>' + etapaId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#edit-etapa-id').val(response.etapa.id);
                $('#edit-etapa-nome').val(response.etapa.nome);
                $('#edit-etapa-status').val(response.etapa.status);
                $('#edit-etapa-percentual').val(response.etapa.percentual_concluido || 0);
                $('#edit-etapa-data-fim').val(response.etapa.data_fim_prevista);
                $('#edit-etapa-descricao').val(response.etapa.descricao);
                $('#form-editar-etapa').attr('action', '<?= site_url("tecnicos_admin/editar_etapa/") ?>' + etapaId);
                $('#modal-editar-etapa').modal('show');
            } else {
                alert('Erro ao carregar dados da etapa');
            }
        },
        error: function() {
            // Fallback para formulário simples
            var nome = prompt('Nome da etapa:');
            if (nome) {
                window.location.href = '<?= site_url("tecnicos_admin/editar_etapa/") ?>' + etapaId + '?nome=' + encodeURIComponent(nome);
            }
        }
    });
}

function excluirEtapa(etapaId) {
    if (confirm('Tem certeza que deseja excluir esta etapa?')) {
        window.location.href = '<?= site_url("tecnicos_admin/excluir_etapa/") ?>' + etapaId;
    }
}

function atualizarStatusEtapa(etapaId, status) {
    var confirmMsg = status === 'concluida' ? 'Marcar esta etapa como concluída?' : 'Iniciar execução desta etapa?';
    if (!confirm(confirmMsg)) return;

    $.ajax({
        url: '<?= site_url("tecnicos_admin/atualizar_status_etapa") ?>',
        type: 'POST',
        data: { etapa_id: etapaId, status: status },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Erro: ' + response.message);
            }
        },
        error: function() {
            alert('Erro ao atualizar status');
        }
    });
}
</script>
