<!-- Minhas Obras - Área do Técnico -->
<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="bx bx-hard-hat"></i></span>
        <h5>Minhas Obras</h5>
    </div>

    <!-- Lista de Obras -->
    <div class="row-fluid" style="margin-top: 20px;">
        <?php if (!empty($obras)): ?>
            <?php foreach ($obras as $obra): ?>
                <?php
                $statusClass = match($obra->status) {
                    'EmExecucao' => 'andamento',
                    'Contratada' => 'contratada',
                    default => 'planejada'
                };
                $statusLabel = match($obra->status) {
                    'EmExecucao' => 'Em Execução',
                    'Contratada' => 'Contratada',
                    default => $obra->status
                };
                ?>
                <div class="span6" style="margin-bottom: 20px;">
                    <div class="obra-card" style="
                        background: white;
                        border-radius: 16px;
                        overflow: hidden;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                        transition: all 0.3s;
                    " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 30px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'">

                        <!-- Header -->
                        <div style="
                            padding: 20px;
                            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                            color: white;
                        ">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">
                                        <i class="bx bx-hash"></i> <?= $obra->codigo ?>
                                    </div>
                                    <h4 style="margin: 0; font-size: 1.2rem;"><?= htmlspecialchars($obra->nome, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></h4>
                                    <div style="margin-top: 8px; font-size: 0.9rem; opacity: 0.9;">
                                        <i class="bx bx-user"></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                    </div>
                                </div>
                                <span style="
                                    padding: 6px 12px;
                                    border-radius: 20px;
                                    font-size: 0.75rem;
                                    font-weight: 600;
                                    background: rgba(255,255,255,0.2);
                                ">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                        </div>

                        <!-- Progresso -->
                        <div style="padding: 20px; border-bottom: 1px solid #eee;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-size: 0.85rem; color: #666;">Progresso da Obra</span>
                                <span style="font-weight: 700; color: #333;"><?= $obra->percentual_concluido ?? 0 ?>%</span>
                            </div>
                            <div style="height: 8px; background: #f0f0f0; border-radius: 4px; overflow: hidden;">
                                <div style="
                                    height: 100%;
                                    width: <?= $obra->percentual_concluido ?? 0 ?>%;
                                    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                                    border-radius: 4px;
                                    transition: width 0.5s ease;
                                "></div>
                            </div>
                        </div>

                        <!-- Métricas -->
                        <div style="display: flex; padding: 15px 0; border-bottom: 1px solid #eee;">
                            <div style="flex: 1; text-align: center; border-right: 1px solid #eee;">
                                <div style="font-size: 1.3rem; font-weight: 700; color: #667eea;"><?= $obra->minhas_os ?? 0 ?></div>
                                <div style="font-size: 0.75rem; color: #888;">Minhas OS</div>
                            </div>
                            <div style="flex: 1; text-align: center; border-right: 1px solid #eee;">
                                <div style="font-size: 1.3rem; font-weight: 700; color: #11998e;"><?= $obra->etapas_pendentes ?? 0 ?></div>
                                <div style="font-size: 0.75rem; color: #888;">Etapas Pendentes</div>
                            </div>
                            <div style="flex: 1; text-align: center;">
                                <div style="font-size: 1.3rem; font-weight: 700; color: #f093fb;"><?= count($obra->equipe ?? []) ?></div>
                                <div style="font-size: 0.75rem; color: #888;">Na Equipe</div>
                            </div>
                        </div>

                        <!-- Ação -->
                        <div style="padding: 15px 20px;">
                            <a href="<?= site_url('tecnicos_admin/tecnico_executar_obra/' . $obra->id) ?>" class="btn btn-success" style="width: 100%;">
                                <i class="bx bx-play-circle"></i> Executar Obra
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="span12" style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 4rem; color: #e0e0e0; margin-bottom: 20px;">
                    <i class="bx bx-building-house"></i>
                </div>
                <h3 style="color: #666; font-weight: 400;">Nenhuma obra atribuída</h3>
                <p style="color: #999; margin-top: 10px;">Você não está alocado em nenhuma obra no momento.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.obra-card {
    animation: fadeInUp 0.4s ease forwards;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.span6:nth-child(1) .obra-card { animation-delay: 0s; }
.span6:nth-child(2) .obra-card { animation-delay: 0.1s; }
.span6:nth-child(3) .obra-card { animation-delay: 0.2s; }
.span6:nth-child(4) .obra-card { animation-delay: 0.3s; }
</style>
