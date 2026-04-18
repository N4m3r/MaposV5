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
                    <div class="widget-title" style="background: #f8f9fa;">
                        <span class="icon"><i class="bx bx-clipboard"></i></span>
                        <h5>Ordens de Serviço Vinculadas</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <div class="empty-state" style="text-align: center; padding: 40px; color: #888;">
                            <div style="font-size: 3rem; margin-bottom: 15px;"><i class="bx bx-clipboard"></i></div>
                            <p>As OS vinculadas a esta obra aparecerão aqui</p>
                        </div>
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
                            <a href="#" class="quick-action" style="
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
                            <a href="#" class="quick-action" style="
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
                            <a href="#" class="quick-action" style="
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
                            <a href="#" class="quick-action" style="
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
</div>

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
