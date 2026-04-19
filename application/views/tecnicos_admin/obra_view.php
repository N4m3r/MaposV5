<!-- Visualizar Obra - Versão Moderna -->
<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-building"></i>
        </span>
        <h5>Detalhes da Obra</h5>
        <div class="buttons">
            <a href="<?php echo site_url('tecnicos_admin/obras'); ?>" class="button btn btn-mini btn-default">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Voltar</span>
            </a>
            <a href="<?php echo site_url('tecnicos_admin/editar_obra/' . $obra->id); ?>" class="button btn btn-mini btn-warning">
                <span class="button__icon"><i class="bx bx-edit"></i></span>
                <span class="button__text2">Editar</span>
            </a>
        </div>
    </div>

    <?php if (!empty($obra)): ?>
        <!-- Info Header -->
        <div class="obra-header-card" style="margin: 20px 0; padding: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; color: white;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                <div>
                    <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 8px;">
                        <i class="bx bx-hash"></i> <?php echo $obra->codigo ?? '#' . $obra->id; ?>
                    </div>
                    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700;">
                        <?php echo htmlspecialchars($obra->nome ?? 'Sem nome', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                    </h2>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <span class="obra-status-badge" style="
                            padding: 6px 14px;
                            border-radius: 20px;
                            font-size: 0.85rem;
                            font-weight: 600;
                            background: rgba(255,255,255,0.2);
                            color: white;
                        ">
                            <?php
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
                            echo $statusLabels[$obra->status] ?? $obra->status;
                            ?>
                        </span>
                        <span style="opacity: 0.9;">
                            <i class="bx bx-calendar"></i>
                            Início: <?php echo isset($obra->data_inicio_contrato) ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : '-'; ?>
                        </span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 3rem; font-weight: 700;">
                        <?php echo $obra->percentual_concluido ?? 0; ?>%
                    </div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Concluído</div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div style="margin-top: 20px;">
                <div style="height: 8px; background: rgba(255,255,255,0.2); border-radius: 4px; overflow: hidden;">
                    <div style="
                        height: 100%;
                        width: <?php echo $obra->percentual_concluido ?? 0; ?>%;
                        background: white;
                        border-radius: 4px;
                        transition: width 0.5s ease;
                    "></div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <!-- Coluna Esquerda -->
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
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;">
                                        <i class="bx bx-user"></i> Cliente
                                    </div>
                                    <div style="font-weight: 600; color: #333;">
                                        <?php echo htmlspecialchars($obra->cliente_nome ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                    </div>
                                </div>
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;">
                                        <i class="bx bx-tag"></i> Tipo de Obra
                                    </div>
                                    <div style="font-weight: 600; color: #333;">
                                        <?php echo $obra->tipo_obra ?? 'Não informado'; ?>
                                    </div>
                                </div>
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;">
                                        <i class="bx bx-hard-hat"></i> Responsável Técnico
                                    </div>
                                    <div style="font-weight: 600; color: #333;">
                                        <?php echo htmlspecialchars($obra->responsavel_nome ?? 'Não atribuído', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;">
                                        <i class="bx bx-calendar-check"></i> Previsão de Término
                                    </div>
                                    <div style="font-weight: 600; color: #333;">
                                        <?php echo isset($obra->data_fim_prevista) ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : '-'; ?>
                                    </div>
                                </div>
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;">
                                        <i class="bx bx-map-pin"></i> Endereço
                                    </div>
                                    <div style="font-weight: 600; color: #333;">
                                        <?php echo htmlspecialchars($obra->endereco ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                    </div>
                                </div>
                                <?php if (!empty($obra->especialidade_principal)): ?>
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;">
                                        <i class="bx bx-briefcase"></i> Especialidade
                                    </div>
                                    <div style="font-weight: 600; color: #333;">
                                        <?php echo htmlspecialchars($obra->especialidade_principal, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($obra->observacoes)): ?>
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            <div style="font-size: 0.85rem; color: #888; margin-bottom: 5px;">
                                <i class="bx bx-note"></i> Observações
                            </div>
                            <div style="color: #555; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($obra->observacoes, ENT_COMPAT | ENT_HTML5, 'UTF-8')); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Etapas da Obra -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: #f8f9fa;">
                        <span class="icon"><i class="bx bx-list-check"></i></span>
                        <h5>Etapas da Obra</h5>
                        <span class="label label-info" style="margin-left: 10px;"><?php echo count($etapas ?? []); ?></span>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <?php if (!empty($etapas)): ?>
                            <div class="etapas-list" style="display: flex; flex-direction: column; gap: 15px;">
                                <?php foreach ($etapas as $etapa): ?>
                                <div class="etapa-item" style="
                                    padding: 20px;
                                    background: #f8f9fa;
                                    border-radius: 14px;
                                    border-left: 4px solid <?php echo $etapa->status == 'concluida' ? '#4caf50' : ($etapa->status == 'em_andamento' ? '#667eea' : '#ccc'); ?>;
                                ">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                        <div>
                                            <div style="font-weight: 700; color: #333; font-size: 1.1rem;">
                                                <?php echo htmlspecialchars($etapa->nome ?? 'Etapa ' . $etapa->numero_etapa, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                            </div>
                                            <?php if (!empty($etapa->descricao)): ?>
                                            <div style="font-size: 0.85rem; color: #666; margin-top: 5px;">
                                                <?php echo htmlspecialchars($etapa->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <span class="etapa-status" style="
                                            padding: 4px 12px;
                                            border-radius: 12px;
                                            font-size: 0.75rem;
                                            font-weight: 600;
                                            <?php
                                            $etapaStatusColors = [
                                                'pendente' => 'background: #f5f5f5; color: #666;',
                                                'em_andamento' => 'background: #e3f2fd; color: #1976d2;',
                                                'concluida' => 'background: #e8f5e9; color: #388e3c;',
                                            ];
                                            echo $etapaStatusColors[$etapa->status] ?? $etapaStatusColors['pendente'];
                                            ?>
                                        ">
                                            <?php echo ucfirst($etapa->status); ?>
                                        </span>
                                    </div>
                                    <div style="display: flex; gap: 20px; font-size: 0.85rem; color: #888;">
                                        <?php if (!empty($etapa->data_inicio_prevista)): ?>
                                        <span><i class="bx bx-calendar"></i> Início: <?php echo date('d/m/Y', strtotime($etapa->data_inicio_prevista)); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($etapa->data_fim_prevista)): ?>
                                        <span><i class="bx bx-calendar-check"></i> Término: <?php echo date('d/m/Y', strtotime($etapa->data_fim_prevista)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state" style="text-align: center; padding: 40px; color: #888;">
                                <div style="font-size: 3rem; margin-bottom: 15px;"><i class="bx bx-list-ul"></i></div>
                                <p>Nenhuma etapa cadastrada</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- OS Vinculadas -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden;">
                    <div class="widget-title" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span class="icon"><i class="bx bx-clipboard"></i></span>
                            <h5>Ordens de Serviço Vinculadas <span class="badge badge-info"><?= count($os_vinculadas ?? []) ?></span></h5>
                        </div>
                        <a href="#modal-vincular-os" data-toggle="modal" class="btn btn-mini btn-success">
                            <i class="bx bx-plus"></i> Vincular OS
                        </a>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <?php
                        if (!empty($os_vinculadas)):
                            $statusColors = [
                                'Aberto' => '#00cd00',
                                'Em Andamento' => '#436eee',
                                'Finalizado' => '#256',
                                'Cancelado' => '#CD0000',
                                'Orçamento' => '#CDB380',
                                'Aprovado' => '#808080',
                                'Faturado' => '#B266FF'
                            ];
                        ?>
                            <div class="os-list" style="display: flex; flex-direction: column; gap: 12px;">
                                <?php foreach ($os_vinculadas as $os): ?>
                                <div class="os-item" style="
                                    display: flex;
                                    align-items: center;
                                    gap: 12px;
                                    padding: 15px;
                                    background: #f8f9fa;
                                    border-radius: 12px;
                                    transition: all 0.2s;
                                " onmouseover="this.style.background='#e3f2fd'" onmouseout="this.style.background='#f8f9fa'">
                                    <div style="
                                        width: 45px;
                                        height: 45px;
                                        border-radius: 12px;
                                        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                                        color: white;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        font-weight: 600;
                                        font-size: 0.9rem;
                                    ">
                                        #<?= $os->idOs ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #333;">
                                            <?= htmlspecialchars($os->nomeCliente ?? 'Cliente', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #888;">
                                            <i class="bx bx-calendar"></i> <?= date('d/m/Y', strtotime($os->dataInicial)) ?>
                                            <span style="margin-left: 10px;">
                                                <span class="badge" style="
                                                    background: <?= isset($statusColors[$os->status]) ? $statusColors[$os->status] : '#888' ?>;
                                                    color: white;
                                                    font-size: 0.7rem;
                                                    padding: 3px 8px;
                                                ">
                                                    <?= $os->status ?>
                                                </span>
                                            </span>
                                        </div>
                                        <?php if (!empty($os->valorTotal)): ?>
                                        <div style="font-size: 0.8rem; color: #666; margin-top: 4px;">
                                            <i class="bx bx-money"></i> R$ <?= number_format($os->valorTotal, 2, ',', '.') ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="<?= site_url('os/visualizar/' . $os->idOs) ?>" class="btn btn-mini btn-info" title="Visualizar OS">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <form method="post" action="<?= site_url('tecnicos_admin/desvincular_os_obra') ?>" style="display: inline;" onsubmit="return confirm('Deseja realmente desvincular esta OS da obra?');">
                                            <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
                                            <input type="hidden" name="os_id" value="<?= $os->idOs ?>">
                                            <button type="submit" class="btn btn-mini btn-danger" title="Desvincular OS">
                                                <i class="bx bx-unlink"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state" style="text-align: center; padding: 40px; color: #888;">
                                <div style="font-size: 3rem; margin-bottom: 15px;"><i class="bx bx-clipboard"></i></div>
                                <p>Nenhuma OS vinculada a esta obra</p>
                                <p style="font-size: 0.85rem; margin-top: 10px;">Clique em "Vincular OS" para adicionar ordens de serviço</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita -->
            <div class="span4">
                <!-- Equipe -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <span class="icon" style="color: white;"><i class="bx bx-group"></i></span>
                        <h5 style="color: white;">Equipe</h5>
                        <span class="label" style="margin-left: 10px; background: rgba(255,255,255,0.3); color: white;"><?php echo count($equipe ?? []); ?></span>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <?php if (!empty($equipe)): ?>
                            <div class="equipe-list" style="display: flex; flex-direction: column; gap: 12px;">
                                <?php foreach ($equipe as $membro): ?>
                                <div class="equipe-item" style="
                                    display: flex;
                                    align-items: center;
                                    gap: 12px;
                                    padding: 15px;
                                    background: #f8f9fa;
                                    border-radius: 12px;
                                    transition: all 0.2s;
                                " onmouseover="this.style.background='#e3f2fd'" onmouseout="this.style.background='#f8f9fa'">
                                    <div style="
                                        width: 45px;
                                        height: 45px;
                                        border-radius: 50%;
                                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                        color: white;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        font-weight: 600;
                                        font-size: 1.1rem;
                                    ">
                                        <?php echo strtoupper(substr($membro->tecnico_nome ?? 'T', 0, 1)); ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #333;">
                                            <?php echo htmlspecialchars($membro->tecnico_nome ?? 'Técnico', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #888;">
                                            <?php echo $membro->funcao ?? 'Técnico'; ?>
                                            <?php if (!empty($membro->nivel_tecnico)): ?>
                                                • Nível <?php echo $membro->nivel_tecnico; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state" style="text-align: center; padding: 30px; color: #888;">
                                <div style="font-size: 2.5rem; margin-bottom: 10px;"><i class="bx bx-user-x"></i></div>
                                <p style="font-size: 0.9rem;">Nenhum técnico alocado</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden;">
                    <div class="widget-title" style="background: #f8f9fa;">
                        <span class="icon"><i class="bx bx-bolt"></i></span>
                        <h5>Ações Rápidas</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <a href="#modal-etapa" data-toggle="modal" class="quick-action" style="
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                padding: 15px;
                                background: #e3f2fd;
                                border-radius: 12px;
                                text-decoration: none;
                                color: #1976d2;
                                transition: all 0.2s;
                            " onmouseover="this.style.background='#1976d2'; this.style.color='white'" onmouseout="this.style.background='#e3f2fd'; this.style.color='#1976d2'">
                                <i class="bx bx-plus-circle" style="font-size: 1.3rem;"></i>
                                <span style="font-weight: 600;">Adicionar Etapa</span>
                            </a>
                            <a href="#modal-equipe" data-toggle="modal" class="quick-action" style="
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                padding: 15px;
                                background: #e8f5e9;
                                border-radius: 12px;
                                text-decoration: none;
                                color: #388e3c;
                                transition: all 0.2s;
                            " onmouseover="this.style.background='#388e3c'; this.style.color='white'" onmouseout="this.style.background='#e8f5e9'; this.style.color='#388e3c'">
                                <i class="bx bx-user-plus" style="font-size: 1.3rem;"></i>
                                <span style="font-weight: 600;">Alocar Técnico</span>
                            </a>
                            <a href="#modal-materiais" data-toggle="modal" class="quick-action" style="
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                padding: 15px;
                                background: #fff3e0;
                                border-radius: 12px;
                                text-decoration: none;
                                color: #f57c00;
                                transition: all 0.2s;
                            " onmouseover="this.style.background='#f57c00'; this.style.color='white'" onmouseout="this.style.background='#fff3e0'; this.style.color='#f57c00'">
                                <i class="bx bx-package" style="font-size: 1.3rem;"></i>
                                <span style="font-weight: 600;">Gerenciar Materiais</span>
                            </a>
                            <a href="<?php echo site_url('os/adicionar?obra_id=' . $obra->id); ?>" class="quick-action" style="
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                padding: 15px;
                                background: #fce4ec;
                                border-radius: 12px;
                                text-decoration: none;
                                color: #c2185b;
                                transition: all 0.2s;
                            " onmouseover="this.style.background='#c2185b'; this.style.color='white'" onmouseout="this.style.background='#fce4ec'; this.style.color='#c2185b'">
                                <i class="bx bx-file" style="font-size: 1.3rem;"></i>
                                <span style="font-weight: 600;">Adicionar OS</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div style="text-align: center; padding: 80px 20px;">
            <div style="font-size: 5rem; color: #e0e0e0; margin-bottom: 20px;">
                <i class="bx bx-error-circle"></i>
            </div>
            <h3 style="color: #666; font-weight: 400; margin-bottom: 10px;">Obra não encontrada</h3>
            <p style="color: #999; margin-bottom: 25px;">A obra solicitada não existe ou foi removida.</p>
            <a href="<?php echo site_url('tecnicos_admin/obras'); ?>" class="button btn btn-success">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Voltar para Obras</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- Modal: Adicionar Etapa -->
    <div id="modal-etapa" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalEtapaLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="modalEtapaLabel"><i class="bx bx-plus-circle"></i> Adicionar Etapa</h5>
        </div>
        <form action="<?php echo site_url('tecnicos_admin/adicionar_etapa'); ?>" method="post">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <div class="modal-body">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label">Nome da Etapa *</label>
                            <div class="controls">
                                <input type="text" name="nome" class="span12" placeholder="Ex: Preparação, Instalação, Testes" required>
                            </div>
                        </div>
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
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label">Descrição</label>
                            <div class="controls">
                                <textarea name="descricao" class="span12" rows="3" placeholder="Descrição detalhada da etapa"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Salvar Etapa</button>
            </div>
        </form>
    </div>

    <!-- Modal: Alocar Técnico -->
    <div id="modal-equipe" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalEquipeLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="modalEquipeLabel"><i class="bx bx-user-plus"></i> Alocar Técnico</h5>
        </div>
        <form action="<?php echo site_url('tecnicos_admin/alocar_tecnico'); ?>" method="post">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <div class="modal-body">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label">Técnico *</label>
                            <div class="controls">
                                <select name="tecnico_id" class="span12" required>
                                    <option value="">-- Selecione o Técnico --</option>
                                    <?php
                                    $this->db->where('status', 1);
                                    $tecnicos_query = $this->db->get('usuarios');
                                    $tecnicos = $tecnicos_query ? $tecnicos_query->result() : [];
                                    foreach ($tecnicos as $t): ?>
                                        <option value="<?php echo $t->idUsuarios; ?>">
                                            <?php echo htmlspecialchars($t->nome, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php echo !empty($t->telefone) ? ' - ' . $t->telefone : ''; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <div class="control-group">
                            <label class="control-label">Função</label>
                            <div class="controls">
                                <select name="funcao" class="span12">
                                    <option value="Técnico">Técnico</option>
                                    <option value="Encarregado">Encarregado</option>
                                    <option value="Auxiliar">Auxiliar</option>
                                    <option value="Responsável Técnico">Responsável Técnico</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="control-group">
                            <label class="control-label">Nível</label>
                            <div class="controls">
                                <select name="nivel_tecnico" class="span12">
                                    <option value="">-- Selecione --</option>
                                    <option value="1">Nível 1</option>
                                    <option value="2">Nível 2</option>
                                    <option value="3">Nível 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="bx bx-save"></i> Alocar</button>
            </div>
        </form>
    </div>

    <!-- Modal: Vincular OS -->
    <div id="modal-vincular-os" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalVincularOSLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="modalVincularOSLabel"><i class="bx bx-link"></i> Vincular Ordem de Serviço</h5>
        </div>
        <form action="<?php echo site_url('tecnicos_admin/vincular_os_obra'); ?>" method="post">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bx bx-info-circle"></i>
                    Selecione uma OS do cliente <strong><?= htmlspecialchars($obra->cliente_nome ?? '') ?></strong>
                    <?php if (!empty($obra->cliente_documento)): ?>
                        <br><small><i class="bx bx-id-card"></i> CNPJ: <?= $obra->cliente_documento ?></small>
                    <?php endif; ?>
                </div>

                <div class="control-group">
                    <label class="control-label">Buscar OS do CNPJ *</label>
                    <div class="controls">
                        <select name="os_id" id="select-os-vincular" class="span12" required>
                            <option value="">-- Carregando OS disponíveis... --</option>
                        </select>
                    </div>
                    <span class="help-block">
                        <i class="bx bx-filter"></i> Buscando OS de todos os cadastros com o mesmo CNPJ
                    </span>
                </div>

                <div id="os-detalhes" style="margin-top: 15px; display: none;">
                    <div class="well well-small" style="background: #f8f9fa; border-radius: 8px;">
                        <h6 style="margin-top: 0;"><i class="bx bx-detail"></i> Detalhes da OS</h6>
                        <div id="os-info"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btn-vincular-os" disabled>
                    <i class="bx bx-link"></i> Vincular OS
                </button>
            </div>
        </form>
    </div>

    <script>
    // Carregar OS disponíveis ao abrir o modal
    $('#modal-vincular-os').on('show', function() {
        carregarOsDisponiveis();
    });

    function carregarOsDisponiveis() {
        var obraId = <?= $obra->id ?>;
        var clienteId = <?= $obra->cliente_id ?? 'null' ?>;

        var select = $('#select-os-vincular');
        select.empty();
        select.append('<option value="">-- Carregando OS disponíveis... --</option>');

        $.ajax({
            url: '<?= site_url("tecnicos_admin/buscar_os_disponiveis") ?>',
            type: 'GET',
            data: {
                obra_id: obraId
            },
            dataType: 'json',
            success: function(response) {
                select.empty();
                select.append('<option value="">-- Selecione uma OS --</option>');

                // Mostrar informações de debug
                var debugInfo = '';
                if (response.cliente_nome) {
                    debugInfo = '<div class="alert alert-info" style="margin-top: 10px; padding: 8px; font-size: 12px;">' +
                        '<i class="bx bx-info-circle"></i> ' +
                        'Cliente: <strong>' + response.cliente_nome + '</strong><br>' +
                        'CNPJ: ' + response.cnpj + '<br>' +
                        'Total de OS deste cliente: ' + response.total_os_cliente + '<br>' +
                        'OS disponíveis: ' + (response.os ? response.os.length : 0) +
                    '</div>';
                }
                $('#os-detalhes').html(debugInfo).show();

                if (response.success && response.os && response.os.length > 0) {
                    response.os.forEach(function(os) {
                        var documento = os.documento ? os.documento.replace(/[^0-9]/g, '').replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5') : '';
                        select.append('<option value="' + os.idOs + '" data-status="' + os.status + '" data-data="' + os.dataInicial + '" data-cliente="' + os.nomeCliente + '" data-documento="' + documento + '"\u003e' +
                            '#' + os.idOs + ' - ' + os.nomeCliente + ' [' + documento + '] (' + os.status + ')' +
                        '</option>');
                    });
                } else {
                    var msg = response.message || 'Nenhuma OS disponível para vincular (todas já estão vinculadas ou não existem OS para este cliente)';
                    select.append('<option value="" disabled>' + msg + '</option>');
                }
            },
            error: function(xhr, status, error) {
                select.empty();
                select.append('<option value="" disabled>Erro ao carregar OS: ' + error + '</option>');
            }
        });
    }

    function formatarData(dataStr) {
        if (!dataStr) return '-';
        var data = new Date(dataStr);
        return data.toLocaleDateString('pt-BR');
    }

    // Mostrar detalhes ao selecionar OS
    $('#select-os-vincular').on('change', function() {
        var selected = $(this).find('option:selected');
        var osId = $(this).val();

        if (osId) {
            var status = selected.data('status');
            var data = selected.data('data');
            var cliente = selected.data('cliente');
            var documento = selected.data('documento');

            $('#os-info').html(
                '<p><strong><i class="bx bx-hash"></i> OS #:</strong> ' + osId + '</p>' +
                '<p><strong><i class="bx bx-user"></i> Cliente:</strong> ' + cliente + '</p>' +
                '<p><strong><i class="bx bx-id-card"></i> CNPJ:</strong> ' + documento + '</p>' +
                '<p><strong><i class="bx bx-flag"></i> Status:</strong> <span class="badge" style="background: #667eea; color: white; padding: 3px 8px; border-radius: 4px;">' + status + '</span></p>' +
                '<p><strong><i class="bx bx-calendar"></i> Data:</strong> ' + formatarData(data) + '</p>'
            );
            $('#os-detalhes').show();
            $('#btn-vincular-os').prop('disabled', false);
        } else {
            $('#os-detalhes').hide();
            $('#btn-vincular-os').prop('disabled', true);
        }
    });
    </script>

    <!-- Modal: Gerenciar Materiais -->
    <div id="modal-materiais" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalMateriaisLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="modalMateriaisLabel"><i class="bx bx-package"></i> Gerenciar Materiais da Obra</h5>
        </div>
        <form action="<?php echo site_url('tecnicos_admin/salvar_materiais'); ?>" method="post">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <div class="modal-body">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle"></i> Adicione os materiais necessários para esta obra.
                        </div>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th width="100">Qtd</th>
                                    <th>Observação</th>
                                    <th width="50">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="materiais-list">
                                <!-- Linhas dinâmicas serão adicionadas aqui -->
                                <tr class="material-row">
                                    <td>
                                        <input type="text" name="materiais[0][nome]" class="span12" placeholder="Nome do material" required>
                                    </td>
                                    <td>
                                        <input type="number" name="materiais[0][quantidade]" class="span12" value="1" min="1" required>
                                    </td>
                                    <td>
                                        <input type="text" name="materiais[0][observacao]" class="span12" placeholder="Observação">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-mini btn-remover-material" title="Remover">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success btn-mini" id="btn-adicionar-material">
                            <i class="bx bx-plus"></i> Adicionar Material
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                <button type="submit" class="btn btn-warning"><i class="bx bx-save"></i> Salvar Materiais</button>
            </div>
        </form>
    </div>
</div>

<script>
// Script para gerenciar materiais dinâmicos
$(document).ready(function() {
    var materialIndex = 1;

    // Adicionar nova linha de material
    $('#btn-adicionar-material').click(function() {
        var newRow = `
            <tr class="material-row">
                <td>
                    <input type="text" name="materiais[${materialIndex}][nome]" class="span12" placeholder="Nome do material" required>
                </td>
                <td>
                    <input type="number" name="materiais[${materialIndex}][quantidade]" class="span12" value="1" min="1" required>
                </td>
                <td>
                    <input type="text" name="materiais[${materialIndex}][observacao]" class="span12" placeholder="Observação">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-mini btn-remover-material" title="Remover">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#materiais-list').append(newRow);
        materialIndex++;
    });

    // Remover linha de material
    $(document).on('click', '.btn-remover-material', function() {
        var rows = $('.material-row');
        if (rows.length > 1) {
            $(this).closest('.material-row').remove();
        } else {
            alert('Deve haver pelo menos um material na lista.');
        }
    });
});
</script>

<style>
.etapa-item {
    transition: all 0.3s;
}
.etapa-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.equipe-item {
    transition: all 0.2s;
}

.quick-action {
    transition: all 0.2s;
}

.info-item {
    transition: all 0.2s;
}
</style>
