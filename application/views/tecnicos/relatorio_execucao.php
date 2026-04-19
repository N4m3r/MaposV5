<!-- Relatório de Execução - Portal do Técnico -->
<div id="content">
<style>
.portal-tecnico-content { margin-top: 15px !important; }
@media (max-width: 768px) { .portal-tecnico-content { margin-top: 10px !important; } }

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
}
.status-badge.status-finalizada {
    background: #e8f5e9;
    color: #2e7d32;
}

/* Relatório Card */
.relatorio-card {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.relatorio-card h5 {
    margin: 0 0 15px 0;
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.info-row {
    display: flex;
    gap: 20px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.8rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    color: #333;
    font-weight: 500;
}

/* Timeline de Execução */
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #667eea;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding: 15px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 20px;
    width: 12px;
    height: 12px;
    background: #667eea;
    border-radius: 50%;
    border: 2px solid white;
}
.timeline-data {
    font-size: 0.85rem;
    color: #888;
    margin-bottom: 5px;
}
.timeline-titulo {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}
.timeline-detalhes {
    font-size: 0.9rem;
    color: #666;
}

/* Fotos Grid */
.fotos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
}
.foto-item {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
}
.foto-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}
.foto-tipo {
    padding: 8px;
    background: #f8f9fa;
    font-size: 0.8rem;
    text-align: center;
    color: #666;
}

/* Assinatura */
.assinatura-box {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: white;
}
.assinatura-img {
    max-width: 100%;
    max-height: 200px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #999;
}
.empty-text {
    margin: 0;
}
</style>

<div class="row-fluid portal-tecnico-content">
    <div class="span12">

        <!-- Header -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-file"></i></span>
                <h5>Relatório de Execução - OS #<?php echo $os->idOs; ?></h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Informações da OS -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-clipboard"></i> Informações da OS</h5>
                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">OS Nº</span>
                            <span class="info-value"><?php echo $os->idOs; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Data</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($os->dataInicial)); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="status-badge status-finalizada"><?php echo $os->status; ?></span>
                        </div>
                    </div>
                    <?php if ($os->descricaoProduto): ?>
                        <div class="info-row">
                            <div class="info-item" style="flex: 1;">
                                <span class="info-label">Descrição</span>
                                <span class="info-value"><?php echo $os->descricaoProduto; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Informações do Cliente -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-user"></i> Cliente</h5>
                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">Nome</span>
                            <span class="info-value"><?php echo htmlspecialchars($cliente->nomeCliente ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                        </div>
                    </div>
                    <?php if (!empty($cliente->endereco)): ?>
                        <div class="info-row">
                            <div class="info-item">
                                <span class="info-label">Endereço</span>
                                <span class="info-value"><?php echo htmlspecialchars($cliente->endereco, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Execuções -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-timer"></i> Histórico de Execução</h5>
                    <?php if (!empty($execucoes)): ?>
                        <div class="timeline">
                            <?php foreach ($execucoes as $exec): ?>
                                <div class="timeline-item">
                                    <div class="timeline-data">
                                        <i class="bx bx-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($exec->checkin_horario)); ?>
                                    </div>
                                    <div class="timeline-titulo">
                                        Execução #<?php echo $exec->id; ?>
                                    </div>
                                    <div class="timeline-detalhes">
                                        <?php if (!empty($exec->tempo_atendimento_minutos)): ?>
                                            <p><i class="bx bx-time"></i> Tempo: <?php echo round($exec->tempo_atendimento_minutos / 60, 2); ?> horas</p>
                                        <?php endif; ?>
                                        <?php if (!empty($exec->checklist_json)):
                                            $checklist = json_decode($exec->checklist_json, true);
                                            if (!empty($checklist['nome_cliente_assina'])): ?>
                                                <p><i class="bx bx-user-check"></i> Assinado por: <?php echo htmlspecialchars($checklist['nome_cliente_assina'], ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($checklist['observacoes'])): ?>
                                                <p><i class="bx bx-note"></i> Observações: <?php echo htmlspecialchars($checklist['observacoes'], ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-text">Nenhuma execução registrada</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Serviços Executados -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-wrench"></i> Serviços da OS</h5>
                    <?php if (!empty($servicos)): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th>Quantidade</th>
                                    <th>Preço Unit.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($servicos as $servico): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($servico->servico_nome ?? $servico->nome ?? 'Serviço', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                            <?php if (!empty($servico->servico_codigo) || !empty($servico->codigo)): ?>
                                                <br><small class="text-muted">Código: <?php echo htmlspecialchars($servico->servico_codigo ?? $servico->codigo ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $servico->quantidade ?? 1; ?></td>
                                        <td>R$ <?php echo number_format($servico->preco ?? $servico->servico_preco ?? 0, 2, ',', '.'); ?></td>
                                        <td>R$ <?php echo number_format(($servico->quantidade ?? 1) * ($servico->preco ?? $servico->servico_preco ?? 0), 2, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-text">Nenhum serviço cadastrado</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Produtos Utilizados -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-package"></i> Produtos/Materiais</h5>
                    <?php if (!empty($produtos)): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Preço Unit.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $produto): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($produto->descricao ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                                        <td><?php echo $produto->quantidade ?? 0; ?> <?php echo htmlspecialchars($produto->unidade ?? 'un', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                                        <td>R$ <?php echo number_format($produto->preco ?? 0, 2, ',', '.'); ?></td>
                                        <td>R$ <?php echo number_format(($produto->quantidade ?? 0) * ($produto->preco ?? 0), 2, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-text">Nenhum produto utilizado</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Fotos -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-camera"></i> Registro Fotográfico</h5>
                    <?php
                    // Coletar todas as fotos das execuções
                    $todas_fotos = [];
                    if (!empty($execucoes)) {
                        foreach ($execucoes as $exec) {
                            if (!empty($exec->fotos_galeria_json)) {
                                $fotos = json_decode($exec->fotos_galeria_json, true);
                                if (is_array($fotos)) {
                                    $todas_fotos = array_merge($todas_fotos, $fotos);
                                }
                            }
                            if (!empty($exec->checkin_foto)) {
                                $todas_fotos[] = ['caminho' => $exec->checkin_foto, 'tipo' => 'checkin', 'data_hora' => $exec->checkin_horario];
                            }
                            if (!empty($exec->checkout_foto)) {
                                $todas_fotos[] = ['caminho' => $exec->checkout_foto, 'tipo' => 'checkout', 'data_hora' => $exec->checkout_horario ?? $exec->data_checkin];
                            }
                        }
                    }
                    ?>
                    <?php if (!empty($todas_fotos)): ?>
                        <div class="fotos-grid">
                            <?php foreach ($todas_fotos as $foto): ?>
                                <div class="foto-item">
                                    <img src="<?php echo base_url($foto['caminho']); ?>" alt="Foto do serviço">
                                    <div class="foto-tipo">
                                        <?php
                                        $tipo = $foto['tipo'] ?? 'foto';
                                        $tipos_label = [
                                            'checkin' => 'Check-in',
                                            'checkout' => 'Check-out',
                                            'antes' => 'Antes',
                                            'depois' => 'Depois',
                                            'problema' => 'Problema',
                                            'detalhe' => 'Detalhe',
                                            'foto' => 'Foto'
                                        ];
                                        echo $tipos_label[$tipo] ?? ucfirst($tipo);
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-text">Nenhuma foto registrada</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Assinatura -->
                <?php if (!empty($execucoes)):
                    $assinatura = null;
                    foreach ($execucoes as $exec) {
                        if (!empty($exec->checklist_json)) {
                            $checklist = json_decode($exec->checklist_json, true);
                            if (!empty($checklist['assinatura_cliente'])) {
                                $assinatura = $checklist['assinatura_cliente'];
                                break;
                            }
                        }
                    }
                    if ($assinatura):
                ?>
                    <div class="relatorio-card">
                        <h5><i class="bx bx-pencil"></i> Assinatura do Cliente</h5>
                        <div class="assinatura-box">
                            <img src="<?php echo $assinatura; ?>" alt="Assinatura" class="assinatura-img">
                        </div>
                    </div>
                <?php endif; endif; ?>

                <!-- Botão Voltar -->
                <div style="text-align: center; margin-top: 30px;">
                    <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-large">
                        <i class="bx bx-arrow-back"></i> Voltar para Minhas OS
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
</div>