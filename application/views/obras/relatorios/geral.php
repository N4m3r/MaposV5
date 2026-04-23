<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Calcular estatísticas
$percentual_concluido = $obra->percentual_concluido ?? 0;
$total_atividades = $estatisticas['total_atividades'] ?? 0;
$concluidas = $estatisticas['concluidas'] ?? 0;
$em_andamento = $estatisticas['em_andamento'] ?? 0;
$pendentes = $total_atividades - $concluidas - $em_andamento;
$total_horas = $estatisticas['total_horas'] ?? 0;

// Calcular dias decorridos e previstos
$data_inicio = $obra->data_inicio_contrato ?? $obra->data_inicio ?? null;
$data_prevista = $obra->data_fim_prevista ?? $obra->data_prevista ?? null;
$data_hoje = new DateTime();
$dias_decorridos = $data_inicio ? $data_hoje->diff(new DateTime($data_inicio))->days : 0;
$dias_restantes = $data_prevista ? ceil((strtotime($data_prevista) - time()) / 86400) : 0;

// Status da obra com cor
$statusObra = $obra->status ?? 'Em Andamento';
$statusColors = [
    'Contratada' => ['bg' => '#f39c12', 'text' => '#fff', 'label' => 'CONTRATADA'],
    'Em Andamento' => ['bg' => '#3498db', 'text' => '#fff', 'label' => 'EM ANDAMENTO'],
    'EmExecucao' => ['bg' => '#3498db', 'text' => '#fff', 'label' => 'EM EXECUÇÃO'],
    'Concluída' => ['bg' => '#27ae60', 'text' => '#fff', 'label' => 'CONCLUÍDA'],
    'Concluida' => ['bg' => '#27ae60', 'text' => '#fff', 'label' => 'CONCLUÍDA'],
    'Paralisada' => ['bg' => '#e74c3c', 'text' => '#fff', 'label' => 'PARALISADA'],
    'Cancelada' => ['bg' => '#95a5a6', 'text' => '#fff', 'label' => 'CANCELADA'],
];
$statusConfig = $statusColors[$statusObra] ?? ['bg' => '#7f8c8d', 'text' => '#fff', 'label' => 'INDEFINIDO'];

// Formatar valor do contrato
$valor_contrato = $obra->valor_contrato ?? 0;
$valor_formatado = $valor_contrato ? 'R$ ' . number_format($valor_contrato, 2, ',', '.') : 'N/C';

// Número do relatório
$numero_relatorio = 'REL-' . date('Y') . '-' . str_pad($obra->id, 4, '0', STR_PAD_LEFT);
?>

<!-- HEADER TÉCNICO -->
<header class="rel-header">
    <div class="rel-header-left">
        <div class="rel-logo">
            <i class="icon-building"></i>
            <div class="rel-logo-text">
                <span class="rel-empresa"><?php echo $emitente->nome ?? 'EMPRESA'; ?></span>
                <span class="rel-doc">RELATÓRIO TÉCNICO DE OBRA</span>
            </div>
        </div>
    </div>
    <div class="rel-header-center">
        <div class="rel-numero"><?php echo $numero_relatorio; ?></div>
        <div class="rel-data">Emissão: <?php echo date('d/m/Y'); ?></div>
    </div>
    <div class="rel-header-right">
        <div class="rel-status-badge" style="background: <?php echo $statusConfig['bg']; ?>; color: <?php echo $statusConfig['text']; ?>">
            <?php echo $statusConfig['label']; ?>
        </div>
    </div>
</header>

<!-- AÇÕES (não imprime) -->
<div class="rel-actions no-print">
    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="rel-btn rel-btn-secondary">
        <i class="icon-arrow-left"></i> Voltar
    </a>
    <button onclick="window.print()" class="rel-btn rel-btn-primary">
        <i class="icon-print"></i> Imprimir / PDF
    </button>
</div>

<!-- IDENTIFICAÇÃO DA OBRA -->
<section class="rel-section rel-section-identificacao">
    <div class="rel-section-header">
        <span class="rel-section-num">01</span>
        <h2 class="rel-section-title">IDENTIFICAÇÃO DA OBRA</h2>
    </div>

    <div class="rel-ident-grid">
        <div class="rel-ident-main">
            <div class="rel-field">
                <label>NOME DA OBRA / PROJETO</label>
                <value><?php echo strtoupper(htmlspecialchars($obra->nome)); ?></value>
            </div>
            <div class="rel-field-row">
                <div class="rel-field rel-field-2">
                    <label>CLIENTE / PROPRIETÁRIO</label>
                    <value><?php echo htmlspecialchars($obra->cliente_nome ?? 'NÃO INFORMADO'); ?></value>
                </div>
                <div class="rel-field rel-field-1">
                    <label>CNPJ/CPF</label>
                    <value><?php echo htmlspecialchars($obra->cliente_documento ?? 'N/C'); ?></value>
                </div>
            </div>
            <div class="rel-field">
                <label>ENDEREÇO</label>
                <value>
                    <?php echo htmlspecialchars($obra->endereco ?? 'NÃO INFORMADO'); ?>
                    <?php if ($obra->cidade ?? false): ?>
                        , <?php echo htmlspecialchars($obra->cidade); ?> - <?php echo htmlspecialchars($obra->estado ?? ''); ?>
                    <?php endif; ?>
                    <?php if ($obra->cep ?? false): ?>
                        CEP: <?php echo htmlspecialchars($obra->cep); ?>
                    <?php endif; ?>
                </value>
            </div>
        </div>

        <div class="rel-ident-side">
            <div class="rel-info-box">
                <div class="rel-info-item">
                    <span class="rel-info-label">Tipo</span>
                    <span class="rel-info-value"><?php echo htmlspecialchars($obra->tipo_obra ?? 'N/C'); ?></span>
                </div>
                <div class="rel-info-item">
                    <span class="rel-info-label">Contrato</span>
                    <span class="rel-info-value"><?php echo $valor_formatado; ?></span>
                </div>
                <div class="rel-info-item">
                    <span class="rel-info-label">Início</span>
                    <span class="rel-info-value"><?php echo $data_inicio ? date('d/m/Y', strtotime($data_inicio)) : 'N/C'; ?></span>
                </div>
                <div class="rel-info-item">
                    <span class="rel-info-label">Previsão Término</span>
                    <span class="rel-info-value"><?php echo $data_prevista ? date('d/m/Y', strtotime($data_prevista)) : 'N/C'; ?></span>
                </div>
                <?php if ($obra->data_fim_real ?? false): ?>
                <div class="rel-info-item destaque">
                    <span class="rel-info-label">Término Real</span>
                    <span class="rel-info-value"><?php echo date('d/m/Y', strtotime($obra->data_fim_real)); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- EQUIPE TÉCNICA -->
<section class="rel-section rel-section-equipe">
    <div class="rel-section-header">
        <span class="rel-section-num">02</span>
        <h2 class="rel-section-title">EQUIPE TÉCNICA</h2>
    </div>

    <div class="rel-equipe-grid">
        <div class="rel-equipe-card">
            <div class="rel-equipe-icon"><i class="icon-user-md"></i></div>
            <div class="rel-equipe-info">
                <span class="rel-equipe-cargo">GESTOR DE PROJETO</span>
                <span class="rel-equipe-nome"><?php echo htmlspecialchars($obra->gestor_nome ?? 'NÃO ALOCADO'); ?></span>
            </div>
        </div>
        <div class="rel-equipe-card">
            <div class="rel-equipe-icon"><i class="icon-wrench"></i></div>
            <div class="rel-equipe-info">
                <span class="rel-equipe-cargo">RESPONSÁVEL TÉCNICO</span>
                <span class="rel-equipe-nome"><?php echo htmlspecialchars($obra->responsavel_tecnico_nome ?? $obra->gestor_nome ?? 'NÃO ALOCADO'); ?></span>
            </div>
        </div>
        <?php if (!empty($equipe)): ?>
            <?php $idx = 1; foreach (array_slice($equipe, 0, 4) as $membro): ?>
            <div class="rel-equipe-card secundario">
                <div class="rel-equipe-icon"><i class="icon-user"></i></div>
                <div class="rel-equipe-info">
                    <span class="rel-equipe-cargo"><?php echo strtoupper(htmlspecialchars($membro->funcao ?? 'TÉCNICO')); ?></span>
                    <span class="rel-equipe-nome"><?php echo htmlspecialchars($membro->nome ?? $membro->nomeUsuario ?? 'N/A'); ?></span>
                </div>
            </div>
            <?php $idx++; endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- INDICADORES DE DESEMPENHO -->
<section class="rel-section rel-section-indicadores">
    <div class="rel-section-header">
        <span class="rel-section-num">03</span>
        <h2 class="rel-section-title">INDICADORES DE DESEMPENHO</h2>
    </div>

    <div class="rel-indicadores-grid">
        <!-- Progresso Físico -->
        <div class="rel-ind-card rel-ind-destaque">
            <div class="rel-ind-header">
                <i class="icon-tasks"></i>
                <span>PROGRESSO FÍSICO</span>
            </div>
            <div class="rel-ind-body">
                <div class="rel-progress-circle" style="--progress: <?php echo $percentual_concluido; ?>">
                    <div class="rel-progress-value"><?php echo $percentual_concluido; ?><small>%</small></div>
                </div>
                <div class="rel-progress-linear">
                    <div class="rel-progress-bar">
                        <div class="rel-progress-fill" style="width: <?php echo $percentual_concluido; ?%"></div>
                    </div>
                    <div class="rel-progress-labels">
                        <span>Início</span>
                        <span>Conclusão</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cronograma -->
        <div class="rel-ind-card">
            <div class="rel-ind-header">
                <i class="icon-calendar"></i>
                <span>CRONOGRAMA</span>
            </div>
            <div class="rel-ind-body">
                <?php if ($data_inicio && $data_prevista):
                    $dias_total = floor((strtotime($data_prevista) - strtotime($data_inicio)) / 86400);
                    $percentual_tempo = $dias_total > 0 ? min(100, ($dias_decorridos / $dias_total) * 100) : 0;
                    $variacao = $percentual_concluido - $percentual_tempo;
                ?>
                <div class="rel-crono-item">
                    <span class="rel-crono-label">Tempo Decorrido</span>
                    <span class="rel-crono-value"><?php echo $dias_decorridos; ?> dias</span>
                </div>
                <div class="rel-crono-bar">
                    <div class="rel-crono-fill" style="width: <?php echo $percentual_tempo; ?%"></div>
                </div>
                <div class="rel-crono-variacao <?php echo $variacao >= 0 ? 'positiva' : 'negativa'; ?>">
                    <i class="icon-<?php echo $variacao >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                    <?php echo abs(round($variacao, 1)); ?>% vs Físico
                </div>
                <?php if ($dias_restantes > 0): ?>
                <div class="rel-crono-restante">
                    <?php echo $dias_restantes; ?> dias restantes
                </div>
                <?php elseif ($dias_restantes < 0): ?>
                <div class="rel-crono-atraso">
                    Atraso de <?php echo abs($dias_restantes); ?> dias
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="rel-crono-nao-definido">Cronograma não definido</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Atividades -->
        <div class="rel-ind-card">
            <div class="rel-ind-header">
                <i class="icon-check"></i>
                <span>ATIVIDADES</span>
            </div>
            <div class="rel-ind-body">
                <div class="rel-atv-grid">
                    <div class="rel-atv-item total">
                        <span class="rel-atv-num"><?php echo $total_atividades; ?></span>
                        <span class="rel-atv-label">Total</span>
                    </div>
                    <div class="rel-atv-item concluidas">
                        <span class="rel-atv-num"><?php echo $concluidas; ?></span>
                        <span class="rel-atv-label">Concluídas</span>
                    </div>
                    <div class="rel-atv-item andamento">
                        <span class="rel-atv-num"><?php echo $em_andamento; ?></span>
                        <span class="rel-atv-label">Em Andamento</span>
                    </div>
                    <div class="rel-atv-item pendentes">
                        <span class="rel-atv-num"><?php echo $pendentes; ?></span>
                        <span class="rel-atv-label">Pendentes</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horas -->
        <div class="rel-ind-card">
            <div class="rel-ind-header">
                <i class="icon-time"></i>
                <span>MAO DE OBRA</span>
            </div>
            <div class="rel-ind-body">
                <div class="rel-horas-principal">
                    <span class="rel-horas-num"><?php echo number_format($total_horas, 0, ',', '.'); ?></span>
                    <span class="rel-horas-unid">horas</span>
                </div>
                <?php if ($total_horas > 0 && $concluidas > 0): ?>
                <div class="rel-horas-media">
                    Média de <?php echo round($total_horas / $concluidas, 1); ?>h por atividade
                </div>
                <?php endif; ?>
                <div class="rel-horas-equiv">
                    ≈ <?php echo number_format($total_horas / 8, 1, ',', '.'); ?> dias-homem
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ETAPAS / DISCIPLINAS -->
<section class="rel-section rel-section-etapas">
    <div class="rel-section-header">
        <span class="rel-section-num">04</span>
        <h2 class="rel-section-title">ETAPAS / DISCIPLINAS</h2>
    </div>

    <?php if (!empty($etapas)): ?>
    <div class="rel-etapas-table-wrap">
        <table class="rel-etapas-table">
            <thead>
                <tr>
                    <th class="col-num">Nº</th>
                    <th class="col-etapa">ETAPA / DISCIPLINA</th>
                    <th class="col-status">STATUS</th>
                    <th class="col-atv">ATIVIDADES</th>
                    <th class="col-horas">HORAS</th>
                    <th class="col-progresso">% EXECUÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($etapas as $index => $etapa):
                    $etapa_pct = $etapa->percentual_concluido ?? 0;
                    $etapa_status = $etapa->status ?? 'NaoIniciada';

                    $statusLabels = [
                        'NaoIniciada' => ['label' => 'NÃO INICIADA', 'class' => 'status-nao-iniciada'],
                        'pendente' => ['label' => 'PENDENTE', 'class' => 'status-pendente'],
                        'em_andamento' => ['label' => 'EM ANDAMENTO', 'class' => 'status-andamento'],
                        'EmExecucao' => ['label' => 'EM EXECUÇÃO', 'class' => 'status-andamento'],
                        'concluida' => ['label' => 'CONCLUÍDA', 'class' => 'status-concluida'],
                        'Concluida' => ['label' => 'CONCLUÍDA', 'class' => 'status-concluida'],
                        'atrasada' => ['label' => 'ATRASADA', 'class' => 'status-atrasada'],
                        'cancelada' => ['label' => 'CANCELADA', 'class' => 'status-cancelada'],
                    ];
                    $st = $statusLabels[$etapa_status] ?? ['label' => 'N/D', 'class' => 'status-nao-iniciada'];
                ?>
                <tr>
                    <td class="col-num"><?php echo $index + 1; ?></td>
                    <td class="col-etapa">
                        <strong><?php echo htmlspecialchars($etapa->nome ?? 'Sem nome'); ?></strong>
                        <?php if ($etapa->descricao ?? false): ?>
                        <small><?php echo htmlspecialchars(substr($etapa->descricao, 0, 60)) . (strlen($etapa->descricao) > 60 ? '...' : ''); ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="col-status">
                        <span class="rel-status-tag <?php echo $st['class']; ?>"><?php echo $st['label']; ?></span>
                    </td>
                    <td class="col-atv">
                        <?php echo $etapa->atividades_concluidas ?? 0; ?>/<?php echo $etapa->total_atividades ?? 0; ?>
                    </td>
                    <td class="col-horas"><?php echo $etapa->horas_trabalhadas ?? 0; ?>h</td>
                    <td class="col-progresso">
                        <div class="rel-table-progress">
                            <div class="rel-table-bar">
                                <div class="rel-table-fill" style="width: <?php echo $etapa_pct; ?%"></div>
                            </div>
                            <span><?php echo $etapa_pct; ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="rel-total-label">TOTAL GERAL</td>
                    <td class="col-atv"><?php echo $concluidas; ?>/<?php echo $total_atividades; ?></td>
                    <td class="col-horas"><?php echo $total_horas; ?>h</td>
                    <td class="col-progresso">
                        <div class="rel-table-progress">
                            <div class="rel-table-bar">
                                <div class="rel-table-fill destaque" style="width: <?php echo $percentual_concluido; ?%"></div>
                            </div>
                            <span><?php echo $percentual_concluido; ?>%</span>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="rel-empty">
        <i class="icon-info-sign"></i>
        <p>Nenhuma etapa cadastrada para esta obra.</p>
    </div>
    <?php endif; ?>
</section>

<!-- ATIVIDADES EXECUTADAS -->
<section class="rel-section rel-section-atividades">
    <div class="rel-section-header">
        <span class="rel-section-num">05</span>
        <h2 class="rel-section-title">ATIVIDADES EXECUTADAS</h2>
    </div>

    <?php if (!empty($atividades)): ?>
    <div class="rel-atv-table-wrap">
        <table class="rel-atv-table">
            <thead>
                <tr>
                    <th class="col-data">DATA</th>
                    <th class="col-titulo">DESCRIÇÃO DA ATIVIDADE</th>
                    <th class="col-etapa">ETAPA</th>
                    <th class="col-resp">EXECUTOR</th>
                    <th class="col-status">SITUAÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $atividades_slice = array_slice($atividades, 0, 20);
                foreach ($atividades_slice as $atv):
                    $atv_status = $atv->status ?? 'agendada';
                    $statusClass = '';
                    $statusLabel = '';
                    switch($atv_status) {
                        case 'concluida': case 'Concluida':
                            $statusClass = 'sit-concluida';
                            $statusLabel = 'CONCLUÍDA';
                            break;
                        case 'iniciada': case 'in_progress': case 'andamento':
                            $statusClass = 'sit-andamento';
                            $statusLabel = 'EM ANDAMENTO';
                            break;
                        case 'cancelada':
                            $statusClass = 'sit-cancelada';
                            $statusLabel = 'CANCELADA';
                            break;
                        default:
                            $statusClass = 'sit-pendente';
                            $statusLabel = 'PENDENTE';
                    }
                ?>
                <tr>
                    <td class="col-data"><?php echo date('d/m/Y', strtotime($atv->data_atividade ?? $atv->created_at)); ?></td>
                    <td class="col-titulo">
                        <?php echo htmlspecialchars($atv->titulo ?? $atv->descricao ?? 'N/A'); ?>
                    </td>
                    <td class="col-etapa"><?php echo htmlspecialchars($atv->etapa_nome ?? 'Geral'); ?></td>
                    <td class="col-resp"><?php echo htmlspecialchars($atv->tecnico_nome ?? $atv->usuario_nome ?? 'N/A'); ?></td>
                    <td class="col-status"><span class="sit-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (count($atividades) > 20): ?>
        <div class="rel-atv-mais">
            ... e mais <?php echo count($atividades) - 20; ?> atividade(s) registrada(s)
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="rel-empty">
        <i class="icon-info-sign"></i>
        <p>Nenhuma atividade registrada.</p>
    </div>
    <?php endif; ?>
</section>

<!-- OBSERVAÇÕES TÉCNICAS -->
<?php if ($obra->observacoes ?? false): ?>
<section class="rel-section rel-section-obs">
    <div class="rel-section-header">
        <span class="rel-section-num">06</span>
        <h2 class="rel-section-title">OBSERVAÇÕES TÉCNICAS</h2>
    </div>
    <div class="rel-obs-content">
        <?php echo nl2br(htmlspecialchars($obra->observacoes)); ?>
    </div>
</section>
<?php endif; ?>

<!-- ASSINATURAS -->
<section class="rel-section rel-section-assinaturas">
    <div class="rel-assinaturas-grid">
        <div class="rel-ass-box">
            <div class="rel-ass-linha"></div>
            <div class="rel-ass-info">
                <strong><?php echo htmlspecialchars($obra->gestor_nome ?? '_______________________'); ?></strong>
                <span>GESTOR DE PROJETO</span>
            </div>
        </div>
        <div class="rel-ass-box">
            <div class="rel-ass-linha"></div>
            <div class="rel-ass-info">
                <strong><?php echo htmlspecialchars($obra->responsavel_tecnico_nome ?? $obra->gestor_nome ?? '_______________________'); ?></strong>
                <span>RESPONSÁVEL TÉCNICO</span>
            </div>
        </div>
        <div class="rel-ass-box">
            <div class="rel-ass-linha"></div>
            <div class="rel-ass-info">
                <strong><?php echo htmlspecialchars($obra->cliente_nome ?? '_______________________'); ?></strong>
                <span>CLIENTE / PROPRIETÁRIO</span>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="rel-footer">
    <div class="rel-footer-left">
        <?php echo $emitente->nome ?? ''; ?>
        <?php if ($emitente->cnpj ?? false): ?>| CNPJ: <?php echo $emitente->cnpj; ?> <?php endif; ?>
    </div>
    <div class="rel-footer-center">
        Relatório emitido em <?php echo date('d/m/Y \à\s H:i'); ?> | Página 1 de 1
    </div>
    <div class="rel-footer-right">
        <?php echo $numero_relatorio; ?>
    </div>
</footer>

<!-- CSS TÉCNICO / MODERNO -->
<style>
/* ===== BASE ===== */
* { box-sizing: border-box; }

body {
    font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: #f5f5f5;
    color: #2c3e50;
    line-height: 1.5;
}

/* ===== HEADER ===== */
.rel-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: -20px -20px 0 -20px;
}

.rel-logo {
    display: flex;
    align-items: center;
    gap: 15px;
}

.rel-logo i {
    font-size: 40px;
    opacity: 0.9;
}

.rel-logo-text {
    display: flex;
    flex-direction: column;
}

.rel-empresa {
    font-size: 12px;
    letter-spacing: 2px;
    opacity: 0.8;
    font-weight: 500;
}

.rel-doc {
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 1px;
}

.rel-header-center {
    text-align: center;
}

.rel-numero {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 2px;
    background: rgba(255,255,255,0.15);
    padding: 5px 15px;
    border-radius: 4px;
}

.rel-data {
    font-size: 12px;
    margin-top: 5px;
    opacity: 0.9;
}

.rel-status-badge {
    padding: 10px 20px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1px;
}

/* ===== AÇÕES (NO PRINT) ===== */
.rel-actions {
    display: flex;
    gap: 10px;
    padding: 20px 30px;
    background: white;
    border-bottom: 1px solid #e0e0e0;
    margin: 0 -20px;
}

.rel-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.rel-btn-primary {
    background: #2a5298;
    color: white;
}

.rel-btn-primary:hover {
    background: #1e3c72;
}

.rel-btn-secondary {
    background: #ecf0f1;
    color: #2c3e50;
}

.rel-btn-secondary:hover {
    background: #d5dbdb;
}

/* ===== SEÇÕES ===== */
.rel-section {
    background: white;
    margin: 20px 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    overflow: hidden;
}

.rel-section-header {
    background: #f8f9fa;
    border-bottom: 3px solid #2a5298;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.rel-section-num {
    background: #2a5298;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
}

.rel-section-title {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #2c3e50;
}

/* ===== IDENTIFICAÇÃO ===== */
.rel-section-identificacao { padding: 0; }

.rel-ident-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 0;
}

.rel-ident-main {
    padding: 20px;
    border-right: 1px solid #ecf0f1;
}

.rel-field {
    margin-bottom: 15px;
}

.rel-field label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    color: #7f8c8d;
    letter-spacing: 1px;
    margin-bottom: 4px;
}

.rel-field value {
    display: block;
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
}

.rel-field-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 15px;
}

.rel-ident-side {
    padding: 20px;
    background: #fafbfc;
}

.rel-info-box {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.rel-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
    border-bottom: 1px dashed #ddd;
}

.rel-info-item:last-child {
    border-bottom: none;
}

.rel-info-item.destaque {
    background: #e8f5e9;
    margin: -5px -10px;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #a5d6a7;
}

.rel-info-label {
    font-size: 10px;
    font-weight: 600;
    color: #7f8c8d;
    letter-spacing: 0.5px;
}

.rel-info-value {
    font-size: 13px;
    font-weight: 600;
    color: #2c3e50;
}

/* ===== EQUIPE ===== */
.rel-equipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    padding: 20px;
}

.rel-equipe-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 4px solid #2a5298;
}

.rel-equipe-card.secundario {
    border-left-color: #95a5a6;
    background: #fafbfc;
}

.rel-equipe-icon {
    width: 45px;
    height: 45px;
    background: #2a5298;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.rel-equipe-card.secundario .rel-equipe-icon {
    background: #95a5a6;
}

.rel-equipe-info {
    display: flex;
    flex-direction: column;
}

.rel-equipe-cargo {
    font-size: 10px;
    font-weight: 700;
    color: #7f8c8d;
    letter-spacing: 0.5px;
}

.rel-equipe-nome {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
}

/* ===== INDICADORES ===== */
.rel-indicadores-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    padding: 20px;
}

.rel-ind-card {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
}

.rel-ind-destaque {
    grid-column: span 2;
    background: linear-gradient(135deg, #f8f9fa 0%, #e8f4f8 100%);
    border: 2px solid #2a5298;
}

@media (max-width: 768px) {
    .rel-ind-destaque { grid-column: span 1; }
}

.rel-ind-header {
    background: #2c3e50;
    color: white;
    padding: 10px 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
}

.rel-ind-destaque .rel-ind-header {
    background: #2a5298;
}

.rel-ind-header i {
    font-size: 14px;
}

.rel-ind-body {
    padding: 20px;
}

/* Progresso Circular */
.rel-progress-circle {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: conic-gradient(
        #27ae60 calc(var(--progress) * 1%),
        #e0e0e0 calc(var(--progress) * 1%)
    );
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    position: relative;
}

.rel-progress-circle::before {
    content: '';
    width: 110px;
    height: 110px;
    background: white;
    border-radius: 50%;
    position: absolute;
}

.rel-progress-value {
    position: relative;
    font-size: 36px;
    font-weight: 700;
    color: #2c3e50;
}

.rel-progress-value small {
    font-size: 18px;
    color: #7f8c8d;
}

/* Progresso Linear */
.rel-progress-linear {
    margin-top: 10px;
}

.rel-progress-bar {
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
}

.rel-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #27ae60, #2ecc71);
    border-radius: 4px;
    transition: width 0.5s ease;
}

.rel-progress-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 10px;
    color: #95a5a6;
}

/* Cronograma */
.rel-crono-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.rel-crono-label {
    font-size: 11px;
    color: #7f8c8d;
}

.rel-crono-value {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
}

.rel-crono-bar {
    height: 6px;
    background: #e0e0e0;
    border-radius: 3px;
    overflow: hidden;
    margin: 10px 0;
}

.rel-crono-fill {
    height: 100%;
    background: #3498db;
    border-radius: 3px;
}

.rel-crono-variacao {
    text-align: center;
    padding: 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 10px;
}

.rel-crono-variacao.positiva {
    background: #d4edda;
    color: #155724;
}

.rel-crono-variacao.negativa {
    background: #f8d7da;
    color: #721c24;
}

.rel-crono-restante {
    text-align: center;
    margin-top: 10px;
    font-size: 11px;
    color: #3498db;
    font-weight: 600;
}

.rel-crono-atraso {
    text-align: center;
    margin-top: 10px;
    font-size: 11px;
    color: #e74c3c;
    font-weight: 600;
}

/* Atividades Grid */
.rel-atv-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.rel-atv-item {
    text-align: center;
    padding: 15px 10px;
    border-radius: 6px;
    background: white;
    border: 1px solid #e0e0e0;
}

.rel-atv-item.total { border-top: 3px solid #3498db; }
.rel-atv-item.concluidas { border-top: 3px solid #27ae60; }
.rel-atv-item.andamento { border-top: 3px solid #f39c12; }
.rel-atv-item.pendentes { border-top: 3px solid #95a5a6; }

.rel-atv-num {
    display: block;
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
}

.rel-atv-label {
    font-size: 10px;
    color: #7f8c8d;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* Horas */
.rel-horas-principal {
    text-align: center;
    margin-bottom: 15px;
}

.rel-horas-num {
    font-size: 42px;
    font-weight: 700;
    color: #2a5298;
}

.rel-horas-unid {
    font-size: 14px;
    color: #7f8c8d;
    margin-left: 5px;
}

.rel-horas-media, .rel-horas-equiv {
    text-align: center;
    font-size: 11px;
    color: #7f8c8d;
    margin-top: 8px;
}

/* ===== TABELAS ===== */
.rel-etapas-table-wrap,
.rel-atv-table-wrap {
    overflow-x: auto;
    padding: 0;
}

.rel-etapas-table,
.rel-atv-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.rel-etapas-table thead,
.rel-atv-table thead {
    background: #2c3e50;
    color: white;
}

.rel-etapas-table th,
.rel-atv-table th {
    padding: 12px 10px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    border-right: 1px solid rgba(255,255,255,0.1);
}

.rel-etapas-table th:last-child,
.rel-atv-table th:last-child {
    border-right: none;
}

.rel-etapas-table td,
.rel-atv-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #ecf0f1;
}

.rel-etapas-table tbody tr:hover,
.rel-atv-table tbody tr:hover {
    background: #f8f9fa;
}

.rel-etapas-table tfoot {
    background: #ecf0f1;
    font-weight: 700;
}

/* Colunas específicas */
.col-num { width: 40px; text-align: center; }
.col-etapa { min-width: 200px; }
.col-etapa strong { display: block; color: #2c3e50; }
.col-etapa small { display: block; color: #7f8c8d; font-size: 11px; margin-top: 3px; }
.col-status { width: 120px; text-align: center; }
.col-atv { width: 100px; text-align: center; }
.col-horas { width: 80px; text-align: center; }
.col-progresso { width: 150px; }
.col-data { width: 90px; text-align: center; }
.col-titulo { min-width: 250px; }
.col-resp { width: 150px; }

/* Status tags */
.rel-status-tag {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.status-nao-iniciada { background: #ecf0f1; color: #7f8c8d; }
.status-pendente { background: #fff3cd; color: #856404; }
.status-andamento { background: #cce5ff; color: #004085; }
.status-concluida { background: #d4edda; color: #155724; }
.status-atrasada { background: #f8d7da; color: #721c24; }
.status-cancelada { background: #f5f5f5; color: #6c757d; text-decoration: line-through; }

/* Situação badges */
.sit-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.sit-concluida { background: #d4edda; color: #155724; }
.sit-andamento { background: #cce5ff; color: #004085; }
.sit-pendente { background: #fff3cd; color: #856404; }
.sit-cancelada { background: #f8d7da; color: #721c24; }

/* Progresso na tabela */
.rel-table-progress {
    display: flex;
    align-items: center;
    gap: 10px;
}

.rel-table-bar {
    flex: 1;
    height: 6px;
    background: #e0e0e0;
    border-radius: 3px;
    overflow: hidden;
}

.rel-table-fill {
    height: 100%;
    background: #3498db;
    border-radius: 3px;
}

.rel-table-fill.destaque {
    background: #27ae60;
}

.rel-total-label {
    text-align: right;
    padding-right: 20px;
    letter-spacing: 1px;
}

.rel-atv-mais {
    text-align: center;
    padding: 15px;
    color: #7f8c8d;
    font-size: 12px;
    background: #f8f9fa;
    border-top: 1px solid #ecf0f1;
}

/* ===== OBSERVAÇÕES ===== */
.rel-obs-content {
    padding: 20px;
    font-size: 13px;
    line-height: 1.8;
    color: #2c3e50;
    background: #fafbfc;
    margin: 0 20px 20px;
    border-radius: 6px;
    border-left: 4px solid #f39c12;
}

/* ===== ASSINATURAS ===== */
.rel-assinaturas-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    padding: 30px;
}

.rel-ass-box {
    text-align: center;
}

.rel-ass-linha {
    border-top: 2px solid #2c3e50;
    margin-bottom: 15px;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
}

.rel-ass-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.rel-ass-info strong {
    font-size: 13px;
    color: #2c3e50;
}

.rel-ass-info span {
    font-size: 10px;
    color: #7f8c8d;
    letter-spacing: 1px;
}

/* ===== FOOTER ===== */
.rel-footer {
    background: #2c3e50;
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    margin: 20px -20px -20px -20px;
}

/* ===== EMPTY STATE ===== */
.rel-empty {
    text-align: center;
    padding: 40px;
    color: #95a5a6;
}

.rel-empty i {
    font-size: 36px;
    margin-bottom: 15px;
    display: block;
}

.rel-empty p {
    font-size: 13px;
    margin: 0;
}

/* ===== PRINT STYLES ===== */
@media print {
    body { background: white; }

    .rel-actions,
    .no-print { display: none !important; }

    .rel-header {
        margin: -10mm -10mm 10mm -10mm;
        padding: 15mm 20mm;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .rel-section {
        box-shadow: none;
        border: 1px solid #ddd;
        break-inside: avoid;
        margin: 10mm 0;
    }

    .rel-section-header {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .rel-etapas-table thead,
    .rel-atv-table thead {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .rel-ind-header {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .rel-footer {
        margin: 10mm -10mm -10mm -10mm;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .rel-section-assinaturas {
        margin-bottom: 30mm;
    }
}

@page {
    size: A4 landscape;
    margin: 10mm;
}
</style>