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
$dias_decorridos = $data_inicio ? floor((time() - strtotime($data_inicio)) / 86400) : 0;
$dias_total = ($data_inicio && $data_prevista) ? floor((strtotime($data_prevista) - strtotime($data_inicio)) / 86400) : 0;
$dias_restantes = $data_prevista ? ceil((strtotime($data_prevista) - time()) / 86400) : 0;

// Status da obra com cor
$statusObra = $obra->status ?? 'Em Andamento';
$statusColors = [
    'Contratada' => ['bg' => '#f39c12', 'text' => '#fff', 'icon' => 'icon-file-alt'],
    'Em Andamento' => ['bg' => '#3498db', 'text' => '#fff', 'icon' => 'icon-refresh'],
    'EmExecucao' => ['bg' => '#3498db', 'text' => '#fff', 'icon' => 'icon-refresh'],
    'Concluída' => ['bg' => '#27ae60', 'text' => '#fff', 'icon' => 'icon-check'],
    'Concluida' => ['bg' => '#27ae60', 'text' => '#fff', 'icon' => 'icon-check'],
    'Paralisada' => ['bg' => '#e74c3c', 'text' => '#fff', 'icon' => 'icon-pause'],
    'Cancelada' => ['bg' => '#95a5a6', 'text' => '#fff', 'icon' => 'icon-ban-circle'],
];
$statusConfig = $statusColors[$statusObra] ?? ['bg' => '#7f8c8d', 'text' => '#fff', 'icon' => 'icon-question-sign'];

// Formatar valor do contrato
$valor_contrato = $obra->valor_contrato ?? 0;
$valor_formatado = $valor_contrato ? 'R$ ' . number_format($valor_contrato, 2, ',', '.') : 'Não informado';
?>

<!-- Header Moderno -->
<div class="relatorio-header">
    <div class="relatorio-header-content">
        <div class="relatorio-title">
            <div class="relatorio-icon">
                <i class="icon-file-alt"></i>
            </div>
            <div class="relatorio-text">
                <h1>Relatório Geral da Obra</h1>
                <p>Visão completa e detalhada do projeto</p>
            </div>
        </div>
        <div class="relatorio-actions">
            <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="btn-action btn-back">
                <i class="icon-arrow-left"></i> Voltar
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="icon-print"></i> Imprimir
            </button>
        </div>
    </div>
</div>

<!-- Info Card da Obra -->
<div class="obra-info-card">
    <div class="obra-info-grid">
        <div class="obra-info-main">
            <h2 class="obra-nome"><?php echo htmlspecialchars($obra->nome); ?></h2>
            <div class="obra-meta">
                <span class="obra-meta-item">
                    <i class="icon-user"></i> <?php echo htmlspecialchars($obra->cliente_nome ?? 'Cliente não definido'); ?>
                </span>
                <span class="obra-meta-item">
                    <i class="icon-map-marker"></i> <?php echo htmlspecialchars($obra->endereco ?? 'Endereço não definido'); ?>
                    <?php if ($obra->cidade): ?>
                        , <?php echo htmlspecialchars($obra->cidade); ?>/<?php echo htmlspecialchars($obra->estado); ?>
                    <?php endif; ?>
                </span>
                <span class="obra-meta-item">
                    <i class="icon-user-md"></i> Gestor: <?php echo htmlspecialchars($obra->gestor_nome ?? 'Não definido'); ?>
                </span>
                <?php if ($obra->responsavel_tecnico_nome): ?>
                <span class="obra-meta-item">
                    <i class="icon-wrench"></i> Resp. Técnico: <?php echo htmlspecialchars($obra->responsavel_tecnico_nome); ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="obra-info-status">
            <div class="status-badge" style="background: <?php echo $statusConfig['bg']; ?>; color: <?php echo $statusConfig['text']; ?>">
                <i class="<?php echo $statusConfig['icon']; ?>"></i>
                <?php echo $statusObra; ?>
            </div>
            <div class="data-relatorio">
                <i class="icon-calendar"></i> <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>
    </div>

    <!-- Informações Adicionais -->
    <div class="obra-detalhes-grid">
        <div class="detalhe-item">
            <span class="detalhe-label">Tipo de Obra</span>
            <span class="detalhe-valor"><?php echo htmlspecialchars($obra->tipo_obra ?? 'Não definido'); ?></span>
        </div>
        <div class="detalhe-item">
            <span class="detalhe-label">Valor do Contrato</span>
            <span class="detalhe-valor"><?php echo $valor_formatado; ?></span>
        </div>
        <div class="detalhe-item">
            <span class="detalhe-label">Data de Início</span>
            <span class="detalhe-valor">
                <?php echo $data_inicio ? date('d/m/Y', strtotime($data_inicio)) : 'Não definida'; ?>
            </span>
        </div>
        <div class="detalhe-item">
            <span class="detalhe-label">Previsão de Término</span>
            <span class="detalhe-valor">
                <?php echo $data_prevista ? date('d/m/Y', strtotime($data_prevista)) : 'Não definida'; ?>
            </span>
        </div>
        <?php if ($obra->data_fim_real): ?>
        <div class="detalhe-item">
            <span class="detalhe-label">Data de Conclusão</span>
            <span class="detalhe-valor">
                <?php echo date('d/m/Y', strtotime($obra->data_fim_real)); ?>
            </span>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($obra->observacoes): ?>
    <div class="obra-observacoes">
        <h4><i class="icon-info-sign"></i> Observações</h4>
        <p><?php echo nl2br(htmlspecialchars($obra->observacoes)); ?></p>
    </div>
    <?php endif; ?>
</div>

<!-- Dashboard de Estatísticas -->
<div class="stats-dashboard">
    <!-- Progresso Principal -->
    <div class="stat-card progresso-principal">
        <div class="stat-card-header">
            <i class="icon-tasks"></i>
            <span>Progresso Geral</span>
        </div>
        <div class="stat-card-body">
            <div class="progresso-circular" style="--progresso: <?php echo $percentual_concluido; ?>">
                <div class="progresso-valor"><?php echo $percentual_concluido; ?>%</div>
            </div>
            <div class="progresso-linear">
                <div class="progresso-barra" style="width: <?php echo $percentual_concluido; ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Cards de Status -->
    <div class="stat-card status-card">
        <div class="stat-card-header">
            <i class="icon-list"></i>
            <span>Atividades</span>
        </div>
        <div class="stat-card-body">
            <div class="atividades-grid">
                <div class="atividade-item total">
                    <div class="atividade-numero"><?php echo $total_atividades; ?></div>
                    <div class="atividade-label">Total</div>
                </div>
                <div class="atividade-item concluidas">
                    <div class="atividade-numero"><?php echo $concluidas; ?></div>
                    <div class="atividade-label">Concluídas</div>
                </div>
                <div class="atividade-item andamento">
                    <div class="atividade-numero"><?php echo $em_andamento; ?></div>
                    <div class="atividade-label">Em Andamento</div>
                </div>
                <div class="atividade-item pendentes">
                    <div class="atividade-numero"><?php echo $pendentes; ?></div>
                    <div class="atividade-label">Pendentes</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Horas Trabalhadas -->
    <div class="stat-card horas-card">
        <div class="stat-card-header">
            <i class="icon-time"></i>
            <span>Horas Trabalhadas</span>
        </div>
        <div class="stat-card-body">
            <div class="horas-principal">
                <span class="horas-numero"><?php echo number_format($total_horas, 0, ',', '.'); ?></span>
                <span class="horas-unidade">horas</span>
            </div>
            <?php if ($total_horas > 0 && $concluidas > 0): ?>
            <div class="horas-media">
                <i class="icon-info-sign"></i> Média de <?php echo round($total_horas / $concluidas, 1); ?>h por atividade
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cronograma -->
    <?php if ($data_inicio): ?>
    <div class="stat-card cronograma-card">
        <div class="stat-card-header">
            <i class="icon-calendar"></i>
            <span>Cronograma</span>
        </div>
        <div class="stat-card-body">
            <div class="cronograma-item">
                <span class="cronograma-label">Dias Decorridos</span>
                <span class="cronograma-valor"><?php echo $dias_decorridos; ?></span>
            </div>
            <?php if ($data_prevista): ?>
            <div class="cronograma-item">
                <span class="cronograma-label">Prazo Total</span>
                <span class="cronograma-valor"><?php echo $dias_total; ?> dias</span>
            </div>
            <div class="cronograma-item <?php echo $dias_restantes < 0 ? 'atrasado' : ''; ?>">
                <span class="cronograma-label"><?php echo $dias_restantes < 0 ? 'Dias de Atraso' : 'Dias Restantes'; ?></span>
                <span class="cronograma-valor"><?php echo abs($dias_restantes); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Seção de Etapas -->
<div class="etapas-section">
    <div class="section-header">
        <h3><i class="icon-sitemap"></i> Etapas da Obra</h3>
        <span class="etapas-count"><?php echo count($etapas); ?> etapa(s)</span>
    </div>

    <?php if (!empty($etapas)): ?>
    <div class="etapas-timeline">
        <?php foreach ($etapas as $index => $etapa):
            $etapa_percentual = $etapa->percentual_concluido ?? 0;
            $etapa_status = $etapa->status ?? 'pendente';
            $etapa_status_labels = [
                'pendente' => ['label' => 'Pendente', 'class' => 'status-pendente'],
                'em_andamento' => ['label' => 'Em Andamento', 'class' => 'status-andamento'],
                'concluida' => ['label' => 'Concluída', 'class' => 'status-concluida'],
                'atrasada' => ['label' => 'Atrasada', 'class' => 'status-atrasada'],
                'cancelada' => ['label' => 'Cancelada', 'class' => 'status-cancelada'],
                'NaoIniciada' => ['label' => 'Não Iniciada', 'class' => 'status-pendente'],
                'EmExecucao' => ['label' => 'Em Execução', 'class' => 'status-andamento'],
                'Concluida' => ['label' => 'Concluída', 'class' => 'status-concluida'],
            ];
            $etapa_config = $etapa_status_labels[$etapa_status] ?? ['label' => $etapa_status, 'class' => 'status-pendente'];
        ?>
        <div class="etapa-item">
            <div class="etapa-number"><?php echo $index + 1; ?></div>
            <div class="etapa-content">
                <div class="etapa-header">
                    <h4 class="etapa-nome"><?php echo htmlspecialchars($etapa->nome); ?></h4>
                    <span class="etapa-status <?php echo $etapa_config['class']; ?>">
                        <?php echo $etapa_config['label']; ?>
                    </span>
                </div>

                <?php if (!empty($etapa->descricao)): ?>
                <p class="etapa-descricao"><?php echo nl2br(htmlspecialchars($etapa->descricao)); ?></p>
                <?php endif; ?>

                <div class="etapa-stats">
                    <div class="etapa-stat">
                        <i class="icon-tasks"></i>
                        <?php echo $etapa->atividades_concluidas ?? 0; ?>/<?php echo $etapa->total_atividades ?? 0; ?> atividades
                    </div>
                    <div class="etapa-stat">
                        <i class="icon-clock"></i>
                        <?php echo $etapa->horas_trabalhadas ?? 0; ?>h trabalhadas
                    </div>
                    <?php if ($etapa->data_inicio_prevista): ?>
                    <div class="etapa-stat">
                        <i class="icon-calendar"></i>
                        <?php echo date('d/m/Y', strtotime($etapa->data_inicio_prevista)); ?>
                        <?php if ($etapa->data_fim_prevista): ?>
                            - <?php echo date('d/m/Y', strtotime($etapa->data_fim_prevista)); ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="etapa-progresso">
                    <div class="etapa-progresso-barra">
                        <div class="etapa-progresso-preenchido" style="width: <?php echo $etapa_percentual; ?>%"></div>
                    </div>
                    <span class="etapa-progresso-valor"><?php echo $etapa_percentual; ?>%</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="icon-info-sign"></i>
        <h4>Nenhuma etapa cadastrada</h4>
        <p>Adicione etapas para acompanhar o progresso da obra.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Seção de Equipe -->
<?php if (!empty($equipe)): ?>
<div class="equipe-section">
    <div class="section-header">
        <h3><i class="icon-group"></i> Equipe da Obra</h3>
        <span class="equipe-count"><?php echo count($equipe); ?> membro(s)</span>
    </div>

    <div class="equipe-grid">
        <?php foreach ($equipe as $membro): ?>
        <div class="membro-card">
            <div class="membro-avatar">
                <i class="icon-user"></i>
            </div>
            <div class="membro-info">
                <h4><?php echo htmlspecialchars($membro->nome); ?></h4>
                <span class="membro-funcao"><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></span>
                <?php if ($membro->telefone): ?>
                <span class="membro-contato">
                    <i class="icon-phone"></i> <?php echo htmlspecialchars($membro->telefone); ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Seção de Atividades Recentes -->
<div class="atividades-section">
    <div class="section-header">
        <h3><i class="icon-check"></i> Atividades Registradas</h3>
        <span class="atividades-count"><?php echo count($atividades); ?> atividade(s)</span>
    </div>

    <?php if (!empty($atividades)): ?>
    <div class="atividades-table-container">
        <table class="atividades-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Título</th>
                    <th>Etapa</th>
                    <th>Técnico</th>
                    <th>Status</th>
                    <th>Progresso</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($atividades as $atividade):
                    $status_class = '';
                    switch ($atividade->status ?? 'agendada') {
                        case 'concluida':
                        case 'Concluida':
                            $status_class = 'status-badge-concluida';
                            break;
                        case 'iniciada':
                        case 'in_progress':
                            $status_class = 'status-badge-andamento';
                            break;
                        case 'cancelada':
                            $status_class = 'status-badge-cancelada';
                            break;
                        default:
                            $status_class = 'status-badge-pendente';
                    }
                ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($atividade->data_atividade ?? $atividade->created_at)); ?></td>
                    <td><?php echo htmlspecialchars($atividade->titulo ?? $atividade->descricao ?? 'Sem título'); ?></td>
                    <td><?php echo htmlspecialchars($atividade->etapa_nome ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($atividade->tecnico_nome ?? $atividade->usuario_nome ?? 'N/A'); ?></td>
                    <td>
                        <span class="status-badge-mini <?php echo $status_class; ?>">
                            <?php echo $atividade->status ?? 'agendada'; ?>
                        </span>
                    </td>
                    <td><?php echo $atividade->percentual_concluido ?? 0; ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="icon-info-sign"></i>
        <h4>Nenhuma atividade registrada</h4>
        <p>As atividades aparecerão aqui quando forem cadastradas.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Resumo Visual -->
<?php if (!empty($etapas)): ?>
<div class="resumo-visual-section">
    <div class="section-header">
        <h3><i class="icon-bar-chart"></i> Distribuição do Progresso</h3>
    </div>
    <div class="resumo-barras">
        <?php foreach ($etapas as $etapa):
            $etapa_percentual = $etapa->percentual_concluido ?? 0;
        ?>
        <div class="barra-etapa" style="flex: 1;">
            <div class="barra-etapa-label"><?php echo htmlspecialchars($etapa->nome); ?></div>
            <div class="barra-etapa-container">
                <div class="barra-etapa-preenchida" style="height: <?php echo $etapa_percentual; ?>%;"></div>
            </div>
            <div class="barra-etapa-valor"><?php echo $etapa_percentual; ?>%</div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Informações do Emitente -->
<?php if (isset($emitente) && $emitente): ?>
<div class="emitente-section">
    <div class="emitente-content">
        <strong><?php echo $emitente->nome; ?></strong>
        <?php if ($emitente->cnpj): ?>
            <span>CNPJ: <?php echo $emitente->cnpj; ?></span>
        <?php endif; ?>
        <span><?php echo $emitente->rua; ?>, <?php echo $emitente->numero; ?> - <?php echo $emitente->bairro; ?></span>
        <span><?php echo $emitente->cidade; ?>/<?php echo $emitente->uf; ?></span>
        <?php if ($emitente->telefone): ?>
            <span>Tel: <?php echo $emitente->telefone; ?></span>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Assinaturas -->
<div class="assinaturas-section">
    <div class="assinaturas-grid">
        <div class="assinatura-box">
            <div class="assinatura-linha"></div>
            <p><strong>Responsável Técnico</strong></p>
            <p><?php echo $obra->gestor_nome ?? '_______________________'; ?></p>
        </div>
        <div class="assinatura-box">
            <div class="assinatura-linha"></div>
            <p><strong>Cliente</strong></p>
            <p><?php echo $obra->cliente_nome ?? '_______________________'; ?></p>
        </div>
    </div>
    <div class="relatorio-footer">
        <p>Relatório gerado em <?php echo date('d/m/Y H:i:s'); ?></p>
        <?php if (isset($emitente) && $emitente->nome): ?>
            <p><?php echo $emitente->nome; ?></p>
        <?php endif; ?>
    </div>
</div>

<style>
/* Header Moderno */
.relatorio-header {
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    margin: -20px -20px 30px -20px;
    padding: 30px 40px;
    position: relative;
    overflow: hidden;
}

.relatorio-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.relatorio-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
    gap: 20px;
}

.relatorio-title {
    display: flex;
    align-items: center;
    gap: 20px;
}

.relatorio-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
}

.relatorio-text h1 {
    margin: 0;
    color: white;
    font-size: 28px;
    font-weight: 700;
}

.relatorio-text p {
    margin: 5px 0 0;
    color: rgba(255,255,255,0.8);
    font-size: 14px;
}

.relatorio-actions {
    display: flex;
    gap: 12px;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-back {
    background: rgba(255,255,255,0.2);
    color: white;
    backdrop-filter: blur(10px);
}

.btn-back:hover {
    background: rgba(255,255,255,0.3);
    text-decoration: none;
    color: white;
}

.btn-print {
    background: white;
    color: #2c3e50;
}

.btn-print:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    text-decoration: none;
    color: #2c3e50;
}

/* Info Card da Obra */
.obra-info-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.obra-info-grid {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
}

.obra-nome {
    margin: 0 0 12px;
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
}

.obra-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.obra-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6b7280;
    font-size: 14px;
}

.obra-meta-item i {
    color: #3498db;
}

.obra-info-status {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 14px;
}

.data-relatorio {
    color: #6b7280;
    font-size: 13px;
}

/* Detalhes Grid */
.obra-detalhes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.detalhe-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detalhe-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detalhe-valor {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
}

/* Observações */
.obra-observacoes {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.obra-observacoes h4 {
    margin: 0 0 10px;
    color: #2c3e50;
    font-size: 14px;
}

.obra-observacoes p {
    margin: 0;
    color: #6b7280;
    font-size: 14px;
    line-height: 1.6;
}

/* Dashboard de Estatísticas */
.stats-dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    color: #6b7280;
    font-weight: 600;
    font-size: 14px;
}

.stat-card-header i {
    font-size: 18px;
    color: #3498db;
}

/* Progresso Principal */
.progresso-circular {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(
        #27ae60 calc(var(--progresso) * 1%),
        #e5e7eb calc(var(--progresso) * 1%)
    );
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    position: relative;
}

.progresso-circular::before {
    content: '';
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
    position: absolute;
}

.progresso-valor {
    position: relative;
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
}

.progresso-linear {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.progresso-barra {
    height: 100%;
    background: linear-gradient(90deg, #27ae60, #2ecc71);
    border-radius: 4px;
    transition: width 1s ease;
}

/* Atividades Grid */
.atividades-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.atividade-item {
    text-align: center;
    padding: 15px;
    border-radius: 12px;
    background: #f9fafb;
}

.atividade-item.total { border-left: 4px solid #667eea; }
.atividade-item.concluidas { border-left: 4px solid #27ae60; }
.atividade-item.andamento { border-left: 4px solid #3498db; }
.atividade-item.pendentes { border-left: 4px solid #f39c12; }

.atividade-numero {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
}

.atividade-label {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
}

/* Horas Card */
.horas-principal {
    text-align: center;
    margin-bottom: 10px;
}

.horas-numero {
    font-size: 48px;
    font-weight: 700;
    color: #3498db;
}

.horas-unidade {
    font-size: 18px;
    color: #6b7280;
}

.horas-media {
    text-align: center;
    color: #6b7280;
    font-size: 13px;
}

.horas-media i {
    color: #3498db;
}

/* Cronograma */
.cronograma-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.cronograma-item:last-child {
    border-bottom: none;
}

.cronograma-label {
    color: #6b7280;
    font-size: 14px;
}

.cronograma-valor {
    font-weight: 600;
    color: #2c3e50;
}

.cronograma-item.atrasado .cronograma-label,
.cronograma-item.atrasado .cronograma-valor {
    color: #e74c3c;
}

/* Seções */
.etapas-section,
.equipe-section,
.atividades-section,
.resumo-visual-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f3f4f6;
}

.section-header h3 {
    margin: 0;
    font-size: 18px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header h3 i {
    color: #3498db;
}

.etapas-count,
.equipe-count,
.atividades-count {
    background: #f3f4f6;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    color: #6b7280;
    font-weight: 500;
}

/* Etapas Timeline */
.etapas-timeline {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.etapa-item {
    display: flex;
    gap: 16px;
    padding: 20px;
    background: #f9fafb;
    border-radius: 12px;
    border-left: 4px solid #3498db;
}

.etapa-number {
    width: 40px;
    height: 40px;
    background: #3498db;
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.etapa-content {
    flex: 1;
}

.etapa-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 10px;
}

.etapa-nome {
    margin: 0;
    font-size: 16px;
    color: #2c3e50;
}

.etapa-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-pendente { background: #f39c12; color: white; }
.status-andamento { background: #3498db; color: white; }
.status-concluida { background: #27ae60; color: white; }
.status-atrasada { background: #e74c3c; color: white; }
.status-cancelada { background: #95a5a6; color: white; }

.etapa-descricao {
    margin: 8px 0;
    color: #6b7280;
    font-size: 14px;
}

.etapa-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin: 12px 0;
    font-size: 13px;
    color: #6b7280;
}

.etapa-stat {
    display: flex;
    align-items: center;
    gap: 6px;
}

.etapa-stat i {
    color: #3498db;
}

.etapa-progresso {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 12px;
}

.etapa-progresso-barra {
    flex: 1;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.etapa-progresso-preenchido {
    height: 100%;
    background: linear-gradient(90deg, #3498db, #2980b9);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.etapa-progresso-valor {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    min-width: 40px;
    text-align: right;
}

/* Equipe Grid */
.equipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
}

.membro-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
    transition: transform 0.2s;
}

.membro-card:hover {
    transform: translateY(-2px);
}

.membro-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.membro-info h4 {
    margin: 0 0 4px;
    font-size: 14px;
    color: #2c3e50;
}

.membro-funcao {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 4px;
}

.membro-contato {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #3498db;
}

/* Atividades Table */
.atividades-table-container {
    overflow-x: auto;
}

.atividades-table {
    width: 100%;
    border-collapse: collapse;
}

.atividades-table th,
.atividades-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #f3f4f6;
}

.atividades-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #6b7280;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.atividades-table td {
    font-size: 14px;
    color: #2c3e50;
}

.atividades-table tr:hover td {
    background: #f9fafb;
}

.status-badge-mini {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.status-badge-concluida { background: #d4edda; color: #155724; }
.status-badge-andamento { background: #cce5ff; color: #004085; }
.status-badge-pendente { background: #fff3cd; color: #856404; }
.status-badge-cancelada { background: #f8d7da; color: #721c24; }

/* Resumo Visual */
.resumo-barras {
    display: flex;
    gap: 20px;
    align-items: flex-end;
    height: 200px;
    padding: 20px 0;
}

.barra-etapa {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.barra-etapa-label {
    font-size: 12px;
    color: #6b7280;
    text-align: center;
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.barra-etapa-container {
    width: 40px;
    height: 150px;
    background: #f3f4f6;
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}

.barra-etapa-preenchida {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(180deg, #27ae60, #2ecc71);
    border-radius: 8px;
    transition: height 0.5s ease;
}

.barra-etapa-valor {
    font-size: 12px;
    font-weight: 600;
    color: #2c3e50;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #6b7280;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    opacity: 0.4;
}

.empty-state h4 {
    font-size: 16px;
    margin-bottom: 8px;
    color: #2c3e50;
}

.empty-state p {
    font-size: 14px;
    margin: 0;
}

/* Emitente Section */
.emitente-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
}

.emitente-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
    color: #6b7280;
    font-size: 14px;
}

.emitente-content strong {
    color: #2c3e50;
    font-size: 16px;
    margin-bottom: 8px;
}

/* Assinaturas Section */
.assinaturas-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.assinaturas-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 40px;
    margin-bottom: 40px;
}

.assinatura-box {
    text-align: center;
}

.assinatura-linha {
    border-top: 2px solid #2c3e50;
    margin-bottom: 12px;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
}

.assinatura-box p {
    margin: 4px 0;
    color: #6b7280;
    font-size: 14px;
}

.assinatura-box strong {
    color: #2c3e50;
}

.relatorio-footer {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #f3f4f6;
    color: #6b7280;
    font-size: 12px;
}

.relatorio-footer p {
    margin: 4px 0;
}

/* Print Styles */
@media print {
    .relatorio-header {
        margin: 0 0 20px 0;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .btn-action {
        display: none !important;
    }

    .stat-card,
    .obra-info-card,
    .etapas-section,
    .equipe-section,
    .atividades-section,
    .emitente-section,
    .assinaturas-section {
        box-shadow: none;
        border: 1px solid #e5e7eb;
        break-inside: avoid;
    }

    .etapa-item,
    .membro-card {
        break-inside: avoid;
    }
}
</style>