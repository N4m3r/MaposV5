<?php
$atividades = $atividades ?? [];
$estatisticas = $estatisticas ?? ['total_atividades' => 0, 'concluidas' => 0, 'tempo_total_horas' => 0, 'por_categoria' => []];
$filtros = $filtros ?? ['data_inicio' => date('Y-m-01'), 'data_fim' => date('Y-m-t')];
?>

<style>
.timeline-container {
    position: relative;
    padding: 20px 0;
}

.timeline-date {
    background: #007bff;
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    display: inline-block;
    margin: 20px 0 15px;
    font-weight: bold;
}

.atividade-item {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 0 8px 8px 0;
}

.atividade-item.concluida {
    border-left-color: #28a745;
}

.atividade-item.nao-concluida {
    border-left-color: #dc3545;
}

.atividade-item.pausada {
    border-left-color: #6c757d;
}

.atividade-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.atividade-tempo {
    background: #e9ecef;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
}

.atividade-tempo .hora {
    font-weight: bold;
    color: #007bff;
}

.atividade-duracao {
    font-size: 14px;
    color: #6c757d;
}

.atividade-info {
    margin-top: 10px;
}

.atividade-info p {
    margin: 5px 0;
    font-size: 14px;
}

.atividade-info i {
    width: 20px;
    text-align: center;
    color: #007bff;
}

.resumo-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.resumo-card .stat {
    text-align: center;
    padding: 10px;
}

.resumo-card .stat-number {
    font-size: 32px;
    font-weight: bold;
}

.resumo-card .stat-label {
    font-size: 12px;
    opacity: 0.9;
}

.categoria-stat {
    display: inline-block;
    padding: 5px 15px;
    margin: 5px;
    border-radius: 20px;
    font-size: 14px;
}

.filtro-section {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    display: block;
}
</style>

<div class="row-fluid">
    <div class="span12">
        <h2><i class="bx bx-history"></i> Histórico de Atividades</h2>

        <!-- Resumo -->
        <div class="resumo-card">
            <div class="row-fluid">
                <div class="span3">
                    <div class="stat">
                        <div class="stat-number"><?= $estatisticas['total_atividades'] ?></div>
                        <div class="stat-label">Total de Atividades</div>
                    </div>
                </div>
                <div class="span3">
                    <div class="stat">
                        <div class="stat-number"><?= $estatisticas['concluidas'] ?></div>
                        <div class="stat-label">Concluídas</div>
                    </div>
                </div>
                <div class="span3">
                    <div class="stat">
                        <div class="stat-number"><?= $estatisticas['tempo_total_horas'] ?></div>
                        <div class="stat-label">Horas Trabalhadas</div>
                    </div>
                </div>
                <div class="span3">
                    <div class="stat">
                        <?php
                        $taxa = $estatisticas['total_atividades'] > 0
                            ? round(($estatisticas['concluidas'] / $estatisticas['total_atividades']) * 100)
                            : 0;
                        ?>
                        <div class="stat-number"><?= $taxa ?>%</div>
                        <div class="stat-label">Taxa de Conclusão</div>
                    </div>
                </div>
            </div>

            <!-- Categorias -->
            <div style="text-align: center; margin-top: 20px;">
                <?php foreach ($estatisticas['por_categoria'] ?? [] as $cat): ?
                    <?php
                        $cores = [
                            'rede' => '#007bff',
                            'cftv' => '#dc3545',
                            'seguranca' => '#6f42c1',
                            'infra' => '#fd7e14',
                            'internet' => '#17a2b8',
                            'geral' => '#6c757d',
                        ];
                        $cor = $cores[$cat->categoria] ?? '#6c757d';
                    ?>
                    <div class="categoria-stat" style="background: <?= $cor ?>30; color: <?= $cor ?>;">
                        <?= ucfirst($cat->categoria) ?>: <?= $cat->total ?>
                        <small>(<?= round($cat->minutos / 60, 1) ?>h)</small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filtro-section">
            <form method="get" class="row-fluid">
                <div class="span3">
                    <label>Data Início</label>
                    <input type="date" name="data_inicio" value="<?= $filtros['data_inicio'] ?>" class="input-block-level">
                </div>
                <div class="span3">
                    <label>Data Fim</label>
                    <input type="date" name="data_fim" value="<?= $filtros['data_fim'] ?>" class="input-block-level">
                </div>
                <div class="span3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary input-block-level">
                        <i class="bx bx-filter"></i> Filtrar
                    </button>
                </div>
                <div class="span3">
                    <label>&nbsp;</label>
                    <a href="<?= site_url('atividades/historico') ?>" class="btn input-block-level">
                        <i class="bx bx-reset"></i> Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista de Atividades -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-list-ul"></i></span>
                <h5>Atividades Registradas</h5>
            </div>
            <div class="widget-content">
                <?php
                $atividades_por_data = [];
                foreach ($atividades as $atv) {
                    $data = date('Y-m-d', strtotime($atv->hora_inicio));
                    if (!isset($atividades_por_data[$data])) {
                        $atividades_por_data[$data] = [];
                    }
                    $atividades_por_data[$data][] = $atv;
                }
                krsort($atividades_por_data);
                ?>

                <?php if (count($atividades_por_data) > 0): ?
                    <div class="timeline-container">
                        <?php foreach ($atividades_por_data as $data => $atvs_dia): ?
                            <div class="timeline-date">
                                <?= date('d/m/Y', strtotime($data)) ?> -
                                <?= strftime('%A', strtotime($data)) ?>
                            </div>

                            <?php foreach ($atvs_dia as $atv): ?
                                <?php
                                    $classe_status = '';
                                    if ($atv->status == 'finalizada') {
                                        $classe_status = $atv->concluida == 1 ? 'concluida' : 'nao-concluida';
                                    } elseif ($atv->status == 'pausada') {
                                        $classe_status = 'pausada';
                                    }
                                ?>
                                <div class="atividade-item <?= $classe_status ?>">
                                    <div class="atividade-header">
                                        <div>
                                            <strong><i class="bx bx-wrench"></i> <?= htmlspecialchars($atv->tipo_nome) ?></strong>
                                            <?php if ($atv->concluida == 1): ?
                                                <span class="label label-success"><i class="bx bx-check"></i> Concluída</span>
                                            <?php elseif ($atv->status == 'finalizada'): ?
                                                <span class="label label-important"><i class="bx bx-x"></i> Não Concluída</span>
                                            <?php elseif ($atv->status == 'pausada'): ?
                                                <span class="label"><i class="bx bx-pause"></i> Pausada</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="atividade-tempo">
                                            <span class="hora"><?= date('H:i', strtotime($atv->hora_inicio)) ?></span>
                                            <?php if ($atv->hora_fim): ?
                                                <span> até </span>
                                                <span class="hora"><?= date('H:i', strtotime($atv->hora_fim)) ?></span>
                                            <?php else: ?
                                                <span> -- </span>
                                                <span class="hora">--:--</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="atividade-duracao">
                                        <?php if ($atv->duracao_minutos): ?
                                            <i class="bx bx-time"></i> Duração:
                                            <strong><?= formatar_duracao($atv->duracao_minutos) ?></strong>
                                        <?php else: ?
                                            <i class="bx bx-time"></i> Sem duração registrada
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($atv->os_id): ?
                                    <div class="atividade-info">
                                        <p><i class="bx bx-file"></i> OS #<?= $atv->os_id ?></p>
                                        <?php if ($atv->os_equipamento): ?
                                            <p><i class="bx bx-package"></i> <?= htmlspecialchars($atv->os_equipamento) ?></p>
                                        <?php endif; ?>
                                        <?php if ($atv->nomeCliente): ?
                                            <p><i class="bx bx-user"></i> <?= htmlspecialchars($atv->nomeCliente) ?></p>
                                        <?php endif; ?>
                                        <?php if ($atv->equipamento): ?
                                            <p><i class="bx bx-wrench"></i> Equipamento: <?= htmlspecialchars($atv->equipamento) ?></p>
                                        <?php endif; ?>
                                        <?php if ($atv->descricao): ?
                                            <p><i class="bx bx-detail"></i> <?= htmlspecialchars($atv->descricao) ?></p>
                                        <?php endif; ?>

                                        <?php if ($atv->problemas_encontrados): ?
                                            <p class="text-warning"><i class="bx bx-error"></i> Problemas: <?= htmlspecialchars($atv->problemas_encontrados) ?></p>
                                        <?php endif; ?>

                                        <?php if ($atv->solucao_aplicada): ?
                                            <p class="text-success"><i class="bx bx-check-shield"></i> Solução: <?= htmlspecialchars($atv->solucao_aplicada) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?
                                </div>
                            <?php endforeach; ?
                        <?php endforeach; ?
                    </div>
                <?php else: ?
                    <div class="empty-state">
                        <i class="bx bx-inbox"></i>
                        <h3>Nenhuma atividade encontrada</h3>
                        <p>Não há atividades registradas no período selecionado.</p>
                        <a href="<?= site_url('atividades/selecionar_os') ?>" class="btn btn-success">
                            <i class="bx bx-play"></i> Iniciar Nova Atividade
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Voltar -->
        <a href="<?= site_url('atividades') ?>" class="btn">
            <i class="bx bx-arrow-back"></i> Voltar ao Dashboard
        </a>
    </div>
</div>
