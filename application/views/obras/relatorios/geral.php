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
    'Contratada' => ['bg' => '#f39c12', 'text' => '#fff', 'label' => 'CONTRATADA', 'icon' => 'icon-file-alt'],
    'Em Andamento' => ['bg' => '#3498db', 'text' => '#fff', 'label' => 'EM ANDAMENTO', 'icon' => 'icon-refresh'],
    'EmExecucao' => ['bg' => '#3498db', 'text' => '#fff', 'label' => 'EM EXECUÇÃO', 'icon' => 'icon-refresh'],
    'Concluída' => ['bg' => '#27ae60', 'text' => '#fff', 'label' => 'CONCLUÍDA', 'icon' => 'icon-check'],
    'Concluida' => ['bg' => '#27ae60', 'text' => '#fff', 'label' => 'CONCLUÍDA', 'icon' => 'icon-check'],
    'Paralisada' => ['bg' => '#e74c3c', 'text' => '#fff', 'label' => 'PARALISADA', 'icon' => 'icon-pause'],
    'Cancelada' => ['bg' => '#95a5a6', 'text' => '#fff', 'label' => 'CANCELADA', 'icon' => 'icon-ban-circle'],
];
$statusConfig = $statusColors[$statusObra] ?? ['bg' => '#7f8c8d', 'text' => '#fff', 'label' => 'INDEFINIDO', 'icon' => 'icon-question-sign'];

// Formatar valor do contrato
$valor_contrato = $obra->valor_contrato ?? 0;
$valor_formatado = $valor_contrato ? 'R$ ' . number_format($valor_contrato, 2, ',', '.') : 'N/C';

// Número do relatório
$numero_relatorio = 'REL-' . date('Y') . '-' . str_pad($obra->id, 4, '0', STR_PAD_LEFT);

// Dados do emitente para impressão
$emitente_nome = $emitente->nome ?? '';
$emitente_cnpj = $emitente->cnpj ?? '';
$emitente_endereco = ($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '');
$emitente_bairro = $emitente->bairro ?? '';
$emitente_cidade = ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '');
$emitente_telefone = $emitente->telefone ?? '';
$emitente_logo = $emitente->url_logo ?? '';
?>

<!-- CSS DO RELATÓRIO -->
<style>
/* Container principal - integrado ao MapOS */
.relatorio-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header estilo MapOS */
.relatorio-header {
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    border-radius: 12px;
    padding: 25px 30px;
    margin-bottom: 25px;
    color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.relatorio-header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
}

.relatorio-titulo {
    display: flex;
    align-items: center;
    gap: 15px;
}

.relatorio-titulo i {
    font-size: 40px;
    opacity: 0.9;
}

.relatorio-titulo-text h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

.relatorio-titulo-text p {
    margin: 5px 0 0;
    opacity: 0.8;
    font-size: 14px;
}

.relatorio-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.relatorio-header-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.2);
}

.relatorio-obra-nome {
    font-size: 18px;
    font-weight: 600;
}

.relatorio-meta {
    display: flex;
    gap: 20px;
    font-size: 13px;
    opacity: 0.9;
}

.relatorio-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Botões de ação */
.relatorio-actions {
    display: flex;
    gap: 12px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.relatorio-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.relatorio-btn-secondary {
    background: #ecf0f1;
    color: #2c3e50;
}

.relatorio-btn-secondary:hover {
    background: #d5dbdb;
    text-decoration: none;
}

.relatorio-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.relatorio-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    text-decoration: none;
    color: white;
}

.relatorio-btn-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.relatorio-btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(17, 153, 142, 0.4);
    text-decoration: none;
    color: white;
}

/* Cards estilo MapOS */
.relatorio-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.relatorio-card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 12px;
}

.relatorio-card-header i {
    font-size: 20px;
    color: #3498db;
}

.relatorio-card-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
}

.relatorio-card-body {
    padding: 20px;
}

/* Grid de informações */
.relatorio-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.relatorio-info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.relatorio-info-item.full-width {
    grid-column: 1 / -1;
}

.relatorio-info-label {
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.relatorio-info-value {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
}

.relatorio-info-value.large {
    font-size: 18px;
    font-weight: 600;
}

/* Indicadores */
.relatorio-indicadores {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.relatorio-ind-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.relatorio-ind-card.destaque {
    border: 2px solid #3498db;
}

.relatorio-ind-header {
    background: #f8f9fa;
    padding: 12px 15px;
    border-bottom: 1px solid #e9ecef;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 8px;
}

.relatorio-ind-header i {
    color: #3498db;
}

.relatorio-ind-body {
    padding: 20px;
}

/* Progresso circular */
.relatorio-progress-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(
        #27ae60 calc(var(--progress) * 1%),
        #e9ecef calc(var(--progress) * 1%)
    );
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    position: relative;
}

.relatorio-progress-circle::before {
    content: '';
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
    position: absolute;
}

.relatorio-progress-value {
    position: relative;
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
}

.relatorio-progress-value small {
    font-size: 14px;
    color: #6c757d;
}

.relatorio-progress-bar {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 10px;
}

.relatorio-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #27ae60, #2ecc71);
    border-radius: 4px;
    transition: width 0.5s ease;
}

/* Cards de status */
.relatorio-status-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.relatorio-status-item {
    text-align: center;
    padding: 15px;
    border-radius: 8px;
    background: #f8f9fa;
}

.relatorio-status-item.total { border-left: 4px solid #3498db; }
.relatorio-status-item.concluidas { border-left: 4px solid #27ae60; }
.relatorio-status-item.andamento { border-left: 4px solid #f39c12; }
.relatorio-status-item.pendentes { border-left: 4px solid #6c757d; }

.relatorio-status-num {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
}

.relatorio-status-label {
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
    margin-top: 4px;
}

/* Tabelas */
.relatorio-table-wrap {
    overflow-x: auto;
}

.relatorio-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.relatorio-table thead {
    background: #f8f9fa;
}

.relatorio-table th {
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
}

.relatorio-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e9ecef;
    color: #495057;
}

.relatorio-table tbody tr:hover {
    background: #f8f9fa;
}

/* Badges */
.relatorio-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
}

.relatorio-badge-nao-iniciada { background: #e9ecef; color: #6c757d; }
.relatorio-badge-pendente { background: #fff3cd; color: #856404; }
.relatorio-badge-andamento { background: #cce5ff; color: #004085; }
.relatorio-badge-concluida { background: #d4edda; color: #155724; }
.relatorio-badge-atrasada { background: #f8d7da; color: #721c24; }
.relatorio-badge-cancelada { background: #f5f5f5; color: #6c757d; text-decoration: line-through; }

/* Barra de progresso na tabela */
.relatorio-table-progress {
    display: flex;
    align-items: center;
    gap: 10px;
}

.relatorio-table-bar {
    flex: 1;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    min-width: 60px;
}

.relatorio-table-fill {
    height: 100%;
    background: #3498db;
    border-radius: 3px;
}

/* Equipe */
.relatorio-equipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 15px;
}

.relatorio-equipe-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #3498db;
}

.relatorio-equipe-avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    flex-shrink: 0;
}

.relatorio-equipe-info {
    min-width: 0;
}

.relatorio-equipe-nome {
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.relatorio-equipe-cargo {
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
}

/* Timeline etapas */
.relatorio-etapas {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.relatorio-etapa-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #3498db;
}

.relatorio-etapa-num {
    width: 35px;
    height: 35px;
    background: #3498db;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
}

.relatorio-etapa-content {
    flex: 1;
}

.relatorio-etapa-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 8px;
}

.relatorio-etapa-nome {
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

/* Footer */
.relatorio-footer {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-size: 12px;
    border-top: 1px solid #e9ecef;
    margin-top: 20px;
}

/* ESTILOS DE IMPRESSÃO - Documento Formal */
@media print {
    /* Esconde elementos da UI */
    .no-print,
    .relatorio-actions,
    .relatorio-btn {
        display: none !important;
    }

    /* Reset de cores para impressão */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    body {
        background: white;
        font-size: 11pt;
        line-height: 1.4;
    }

    .relatorio-container {
        padding: 0;
        max-width: 100%;
    }

    /* Header do documento */
    .relatorio-header {
        background: white !important;
        color: black !important;
        border: 2px solid #333;
        border-radius: 0;
        padding: 20px;
        margin-bottom: 30px;
    }

    .relatorio-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 1px solid #333;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }

    .relatorio-titulo {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .relatorio-titulo i {
        display: none;
    }

    /* Logo na impressão */
    .print-logo {
        display: block !important;
        max-width: 150px;
        max-height: 80px;
        margin-bottom: 10px;
    }

    .print-emitente {
        text-align: left;
        font-size: 10pt;
        line-height: 1.5;
    }

    .print-emitente strong {
        font-size: 12pt;
        display: block;
        margin-bottom: 5px;
    }

    .relatorio-titulo-text h1 {
        font-size: 18pt;
        color: #333;
        margin: 0;
    }

    .relatorio-titulo-text p {
        color: #666;
        margin: 5px 0 0;
        font-size: 10pt;
    }

    .relatorio-status-badge {
        border: 2px solid #333;
        background: white !important;
        color: #333 !important;
        padding: 8px 15px;
    }

    .relatorio-header-info {
        border-top: 1px solid #333;
        padding-top: 15px;
    }

    .relatorio-obra-nome {
        font-size: 14pt;
        color: #333;
    }

    .relatorio-meta {
        color: #666;
    }

    /* Cards na impressão */
    .relatorio-card {
        box-shadow: none;
        border: 1px solid #333;
        border-radius: 0;
        margin-bottom: 20px;
        break-inside: avoid;
    }

    .relatorio-card-header {
        background: #f0f0f0 !important;
        border-bottom: 1px solid #333;
        padding: 10px 15px;
    }

    .relatorio-card-header h3 {
        font-size: 12pt;
        color: #333;
    }

    .relatorio-card-body {
        padding: 15px;
    }

    /* Tabelas na impressão */
    .relatorio-table {
        font-size: 9pt;
    }

    .relatorio-table thead {
        background: #f0f0f0 !important;
    }

    .relatorio-table th,
    .relatorio-table td {
        padding: 8px 10px;
        border: 1px solid #333;
    }

    /* Badges em preto e branco */
    .relatorio-badge {
        border: 1px solid #333;
        background: white !important;
        color: #333 !important;
    }

    /* Quebra de página */
    .page-break {
        page-break-before: always;
    }

    .no-break {
        break-inside: avoid;
    }

    /* Footer do documento */
    .relatorio-footer {
        border-top: 2px solid #333;
        margin-top: 30px;
        padding-top: 20px;
    }

    /* Assinaturas para documento */
    .print-assinaturas {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-top: 40px;
        page-break-inside: avoid;
    }

    .print-ass-box {
        text-align: center;
    }

    .print-ass-linha {
        border-top: 1px solid #333;
        margin-bottom: 10px;
        width: 80%;
        margin-left: auto;
        margin-right: auto;
    }

    .print-ass-nome {
        font-weight: bold;
        font-size: 10pt;
    }

    .print-ass-cargo {
        font-size: 9pt;
        color: #666;
    }

    /* Numeração de página */
    @page {
        margin: 20mm 15mm;
        @bottom-center {
            content: "Página " counter(page) " de " counter(pages);
            font-size: 9pt;
        }
    }
}

/* Esconde elementos de impressão na tela */
.print-only {
    display: none;
}

@media print {
    .print-only {
        display: block !important;
    }
}

/* Responsivo */
@media (max-width: 768px) {
    .relatorio-header-top {
        flex-direction: column;
        align-items: flex-start;
    }

    .relatorio-meta {
        flex-direction: column;
        gap: 8px;
    }

    .relatorio-indicadores {
        grid-template-columns: 1fr;
    }

    .relatorio-info-grid {
        grid-template-columns: 1fr;
    }

    .relatorio-actions {
        flex-direction: column;
    }

    .relatorio-btn {
        width: 100%;
        justify-content: center;
    }

    .relatorio-equipe-grid {
        grid-template-columns: 1fr;
    }

    .relatorio-table {
        font-size: 12px;
    }

    .relatorio-table th,
    .relatorio-table td {
        padding: 8px 10px;
    }
}
</style>

<div class="relatorio-container">
    <!-- HEADER -->
    <div class="relatorio-header">
        <div class="relatorio-header-top">
            <div style="display: flex; align-items: flex-start; gap: 20px;">
                <!-- Logo (visível na impressão) -->
                <div class="print-only" style="display: none;">
                    <?php if ($emitente_logo): ?>
                        <img src="<?php echo base_url($emitente_logo); ?>" alt="Logo" class="print-logo" style="max-width: 120px;">
                    <?php endif; ?>
                    <div class="print-emitente">
                        <strong><?php echo $emitente_nome; ?></strong>
                        <?php if ($emitente_cnpj): ?>CNPJ: <?php echo $emitente_cnpj; ?><br><?php endif; ?>
                        <?php echo $emitente_endereco; ?> - <?php echo $emitente_bairro; ?><br>
                        <?php echo $emitente_cidade; ?>
                        <?php if ($emitente_telefone): ?><br>Tel: <?php echo $emitente_telefone; ?><?php endif; ?>
                    </div>
                </div>

                <div class="relatorio-titulo no-print">
                    <i class="icon-file-alt"></i>
                    <div class="relatorio-titulo-text">
                        <h1>Relatório Geral da Obra</h1>
                        <p>Visão completa do projeto e acompanhamento</p>
                    </div>
                </div>
            </div>

            <div class="relatorio-status-badge" style="background: <?php echo $statusConfig['bg']; ?>; color: <?php echo $statusConfig['text']; ?>">
                <i class="<?php echo $statusConfig['icon']; ?>"></i>
                <?php echo $statusConfig['label']; ?>
            </div>
        </div>

        <div class="relatorio-header-info">
            <div class="relatorio-obra-nome"><?php echo htmlspecialchars($obra->nome); ?></div>
            <div class="relatorio-meta">
                <span><i class="icon-calendar"></i> <?php echo date('d/m/Y'); ?></span>
                <span><i class="icon-file-alt"></i> <?php echo $numero_relatorio; ?></span>
                <span><i class="icon-user"></i> <?php echo htmlspecialchars($obra->cliente_nome ?? 'Cliente não definido'); ?></span>
            </div>
        </div>
    </div>

    <!-- BOTÕES DE AÇÃO -->
    <div class="relatorio-actions no-print">
        <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="relatorio-btn relatorio-btn-secondary">
            <i class="icon-arrow-left"></i> Voltar à Obra
        </a>
        <button onclick="window.print()" class="relatorio-btn relatorio-btn-primary">
            <i class="icon-print"></i> Imprimir / PDF
        </button>
    </div>

    <!-- INDICADORES PRINCIPAIS -->
    <div class="relatorio-indicadores">
        <!-- Progresso -->
        <div class="relatorio-ind-card destaque">
            <div class="relatorio-ind-header">
                <i class="icon-tasks"></i> Progresso da Obra
            </div>
            <div class="relatorio-ind-body">
                <div class="relatorio-progress-circle" style="--progress: <?php echo $percentual_concluido; ?>">
                    <div class="relatorio-progress-value"><?php echo $percentual_concluido; ?><small?>%</small></div>
                </div>
                <div class="relatorio-progress-bar">
                    <div class="relatorio-progress-fill" style="width: <?php echo $percentual_concluido; ?>%>"></div>
                </div>
            </div>
        </div>

        <!-- Atividades -->
        <div class="relatorio-ind-card">
            <div class="relatorio-ind-header">
                <i class="icon-check"></i> Atividades
            </div>
            <div class="relatorio-ind-body">
                <div class="relatorio-status-grid">
                    <div class="relatorio-status-item total">
                        <div class="relatorio-status-num"><?php echo $total_atividades; ?></div>
                        <div class="relatorio-status-label">Total</div>
                    </div>
                    <div class="relatorio-status-item concluidas">
                        <div class="relatorio-status-num"><?php echo $concluidas; ?></div>
                        <div class="relatorio-status-label">Concluídas</div>
                    </div>
                    <div class="relatorio-status-item andamento">
                        <div class="relatorio-status-num"><?php echo $em_andamento; ?></div>
                        <div class="relatorio-status-label">Em Andamento</div>
                    </div>
                    <div class="relatorio-status-item pendentes">
                        <div class="relatorio-status-num"><?php echo $pendentes; ?></div>
                        <div class="relatorio-status-label">Pendentes</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cronograma -->
        <div class="relatorio-ind-card">
            <div class="relatorio-ind-header">
                <i class="icon-calendar"></i> Cronograma
            </div>
            <div class="relatorio-ind-body">
                <?php if ($data_inicio && $data_prevista):
                    $dias_total = floor((strtotime($data_prevista) - strtotime($data_inicio)) / 86400);
                    $percentual_tempo = $dias_total > 0 ? min(100, ($dias_decorridos / $dias_total) * 100) : 0;
                ?>
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 12px; color: #6c757d;">Tempo Decorrido</span>
                        <span style="font-weight: 600;"><?php echo $dias_decorridos; ?> dias</span>
                    </div>
                    <div class="relatorio-progress-bar">
                        <div class="relatorio-progress-fill" style="width: <?php echo $percentual_tempo; ??>%; background: #3498db;"></div>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; color: #6c757d;">
                    <span>Início: <?php echo date('d/m/Y', strtotime($data_inicio)); ?></span>
                    <span>Término: <?php echo date('d/m/Y', strtotime($data_prevista)); ?></span>
                </div>
                <?php if ($dias_restantes > 0): ?>
                    <div style="margin-top: 10px; text-align: center; color: #3498db; font-size: 13px; font-weight: 600;">
                        <?php echo $dias_restantes; ?> dias restantes
                    </div>
                <?php elseif ($dias_restantes < 0): ?>
                    <div style="margin-top: 10px; text-align: center; color: #e74c3c; font-size: 13px; font-weight: 600;">
                        Atraso de <?php echo abs($dias_restantes); ?> dias
                    </div>
                <?php endif; ?>
                <?php else: ?>
                <div style="text-align: center; color: #6c757d; padding: 20px;">
                    Cronograma não definido
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Horas -->
        <div class="relatorio-ind-card">
            <div class="relatorio-ind-header">
                <i class="icon-time"></i> Mão de Obra
            </div>
            <div class="relatorio-ind-body" style="text-align: center;">
                <div style="font-size: 36px; font-weight: 700; color: #2c3e50;">
                    <?php echo number_format($total_horas, 0, ',', '.'); ?>
                </div>
                <div style="color: #6c757d; font-size: 14px;">horas trabalhadas</div>
                <?php if ($total_horas > 0): ?>
                <div style="margin-top: 10px; font-size: 12px; color: #6c757d;">
                    ≈ <?php echo number_format($total_horas / 8, 1, ',', '.'); ?> dias-homem
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- INFORMAÇÕES DA OBRA -->
    <div class="relatorio-card no-break">
        <div class="relatorio-card-header">
            <i class="icon-building"></i>
            <h3>Informações da Obra</h3>
        </div>
        <div class="relatorio-card-body">
            <div class="relatorio-info-grid">
                <div class="relatorio-info-item full-width">
                    <div class="relatorio-info-label">Nome do Projeto</div>
                    <div class="relatorio-info-value large"><?php echo htmlspecialchars($obra->nome); ?></div>
                </div>
                <div class="relatorio-info-item">
                    <div class="relatorio-info-label">Cliente</div>
                    <div class="relatorio-info-value"><?php echo htmlspecialchars($obra->cliente_nome ?? 'Não informado'); ?></div>
                </div>
                <div class="relatorio-info-item">
                    <div class="relatorio-info-label">Valor do Contrato</div>
                    <div class="relatorio-info-value"><?php echo $valor_formatado; ?></div>
                </div>
                <div class="relatorio-info-item">
                    <div class="relatorio-info-label">Tipo de Obra</div>
                    <div class="relatorio-info-value"><?php echo htmlspecialchars($obra->tipo_obra ?? 'Não definido'); ?></div>
                </div>
                <div class="relatorio-info-item">
                    <div class="relatorio-info-label">Data de Início</div>
                    <div class="relatorio-info-value"><?php echo $data_inicio ? date('d/m/Y', strtotime($data_inicio)) : 'N/C'; ?></div>
                </div>
                <div class="relatorio-info-item">
                    <div class="relatorio-info-label">Previsão de Término</div>
                    <div class="relatorio-info-value"><?php echo $data_prevista ? date('d/m/Y', strtotime($data_prevista)) : 'N/C'; ?></div>
                </div>
                <div class="relatorio-info-item full-width">
                    <div class="relatorio-info-label">Endereço</div>
                    <div class="relatorio-info-value">
                        <?php echo htmlspecialchars($obra->endereco ?? 'Não informado'); ?>
                        <?php if ($obra->cidade ?? false): ?>,
                            <?php echo htmlspecialchars($obra->cidade); ?> - <?php echo htmlspecialchars($obra->estado ?? ''); ?>
                        <?php endif; ?>
                        <?php if ($obra->cep ?? false): ?>
                            CEP: <?php echo htmlspecialchars($obra->cep); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($obra->observacoes ?? false): ?>
                <div class="relatorio-info-item full-width">
                    <div class="relatorio-info-label">Observações</div>
                    <div class="relatorio-info-value"><?php echo nl2br(htmlspecialchars($obra->observacoes)); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- EQUIPE TÉCNICA -->
    <div class="relatorio-card no-break">
        <div class="relatorio-card-header">
            <i class="icon-group"></i>
            <h3>Equipe Técnica</h3>
        </div>
        <div class="relatorio-card-body">
            <div class="relatorio-equipe-grid">
                <div class="relatorio-equipe-card">
                    <div class="relatorio-equipe-avatar"><i class="icon-user-md"></i></div>
                    <div class="relatorio-equipe-info">
                        <div class="relatorio-equipe-cargo">Gestor de Projeto</div>
                        <div class="relatorio-equipe-nome"><?php echo htmlspecialchars($obra->gestor_nome ?? 'Não alocado'); ?></div>
                    </div>
                </div>
                <div class="relatorio-equipe-card">
                    <div class="relatorio-equipe-avatar"><i class="icon-wrench"></i></div>
                    <div class="relatorio-equipe-info">
                        <div class="relatorio-equipe-cargo">Responsável Técnico</div>
                        <div class="relatorio-equipe-nome"><?php echo htmlspecialchars($obra->responsavel_tecnico_nome ?? $obra->gestor_nome ?? 'Não alocado'); ?></div>
                    </div>
                </div>
                <?php if (!empty($equipe)): ?>
                    <?php foreach (array_slice($equipe, 0, 4) as $membro): ?>
                    <div class="relatorio-equipe-card" style="border-left-color: #95a5a6;">
                        <div class="relatorio-equipe-avatar" style="background: #95a5a6;"><i class="icon-user"></i></div>
                        <div class="relatorio-equipe-info">
                            <div class="relatorio-equipe-cargo"><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></div>
                            <div class="relatorio-equipe-nome"><?php echo htmlspecialchars($membro->nome ?? $membro->nomeUsuario ?? 'N/A'); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ETAPAS DA OBRA -->
    <div class="relatorio-card">
        <div class="relatorio-card-header">
            <i class="icon-sitemap"></i>
            <h3>Etapas / Disciplinas</h3>
        </div>
        <div class="relatorio-card-body">
            <?php if (!empty($etapas)): ?>
            <div class="relatorio-table-wrap">
                <table class="relatorio-table">
                    <thead>
                        <tr>
                            <th width="40">Nº</th>
                            <th>ETAPA / DISCIPLINA</th>
                            <th width="120">STATUS</th>
                            <th width="100">ATIVIDADES</th>
                            <th width="80">HORAS</th>
                            <th width="120"?>% EXECUÇÃO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($etapas as $index => $etapa):
                            $etapa_pct = $etapa->percentual_concluido ?? 0;
                            $etapa_status = $etapa->status ?? 'NaoIniciada';
                            $statusClasses = [
                                'NaoIniciada' => 'relatorio-badge-nao-iniciada',
                                'pendente' => 'relatorio-badge-pendente',
                                'em_andamento' => 'relatorio-badge-andamento',
                                'EmExecucao' => 'relatorio-badge-andamento',
                                'concluida' => 'relatorio-badge-concluida',
                                'Concluida' => 'relatorio-badge-concluida',
                                'atrasada' => 'relatorio-badge-atrasada',
                                'cancelada' => 'relatorio-badge-cancelada',
                            ];
                            $badgeClass = $statusClasses[$etapa_status] ?? 'relatorio-badge-nao-iniciada';
                        ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($etapa->nome ?? 'Sem nome'); ?></strong>
                                <?php if ($etapa->descricao ?? false): ?>
                                    <br><small style="color: #6c757d;"><?php echo htmlspecialchars(substr($etapa->descricao, 0, 60)) . (strlen($etapa->descricao) > 60 ? '...' : ''); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><span class="relatorio-badge <?php echo $badgeClass; ?>"><?php echo $statusLabels[$etapa_status] ?? 'N/D'; ?></span></td>
                            <td><?php echo $etapa->atividades_concluidas ?? 0; ?>/<?php echo $etapa->total_atividades ?? 0; ?></td>
                            <td><?php echo $etapa->horas_trabalhadas ?? 0; ?>h</td>
                            <td>
                                <div class="relatorio-table-progress">
                                    <div class="relatorio-table-bar">
                                        <div class="relatorio-table-fill" style="width: <?php echo $etapa_pct; ?>%>"></div>
                                    </div>
                                    <span style="font-size: 12px; font-weight: 600;"><?php echo $etapa_pct; ??>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #6c757d;">
                <i class="icon-info-sign" style="font-size: 32px; margin-bottom: 15px; display: block;"></i>
                <p>Nenhuma etapa cadastrada para esta obra.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ATIVIDADES RECENTES -->
    <div class="relatorio-card">
        <div class="relatorio-card-header">
            <i class="icon-check"></i>
            <h3>Atividades Executadas</h3>
        </div>
        <div class="relatorio-card-body">
            <?php if (!empty($atividades)): ?>
            <div class="relatorio-table-wrap">
                <table class="relatorio-table">
                    <thead>
                        <tr>
                            <th>DATA</th>
                            <th>DESCRIÇÃO</th>
                            <th>ETAPA</th>
                            <th>EXECUTOR</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($atividades, 0, 15) as $atv):
                            $atv_status = $atv->status ?? 'agendada';
                            $statusClass = '';
                            switch($atv_status) {
                                case 'concluida': case 'Concluida':
                                    $statusClass = 'relatorio-badge-concluida';
                                    $statusLabel = 'Concluída';
                                    break;
                                case 'iniciada': case 'in_progress':
                                    $statusClass = 'relatorio-badge-andamento';
                                    $statusLabel = 'Em Andamento';
                                    break;
                                case 'cancelada':
                                    $statusClass = 'relatorio-badge-cancelada';
                                    $statusLabel = 'Cancelada';
                                    break;
                                default:
                                    $statusClass = 'relatorio-badge-pendente';
                                    $statusLabel = 'Pendente';
                            }
                        ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($atv->data_atividade ?? $atv->created_at)); ?></td>
                            <td><?php echo htmlspecialchars($atv->titulo ?? $atv->descricao ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($atv->etapa_nome ?? 'Geral'); ?></td>
                            <td><?php echo htmlspecialchars($atv->tecnico_nome ?? $atv->usuario_nome ?? 'N/A'); ?></td>
                            <td><span class="relatorio-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (count($atividades) > 15): ?>
                <div style="text-align: center; padding: 15px; color: #6c757d; font-size: 13px;">
                    ... e mais <?php echo count($atividades) - 15; ?> atividade(s)
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #6c757d;">
                <i class="icon-info-sign" style="font-size: 32px; margin-bottom: 15px; display: block;"></i>
                <p>Nenhuma atividade registrada.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ASSINATURAS (apenas impressão) -->
    <div class="print-only print-assinaturas" style="display: none;">
        <div class="print-ass-box">
            <div class="print-ass-linha"></div>
            <div class="print-ass-nome"><?php echo htmlspecialchars($obra->gestor_nome ?? '_______________________'); ?></div>
            <div class="print-ass-cargo">Gestor de Projeto</div>
        </div>
        <div class="print-ass-box">
            <div class="print-ass-linha"></div>
            <div class="print-ass-nome"><?php echo htmlspecialchars($obra->responsavel_tecnico_nome ?? $obra->gestor_nome ?? '_______________________'); ?></div>
            <div class="print-ass-cargo">Responsável Técnico</div>
        </div>
        <div class="print-ass-box">
            <div class="print-ass-linha"></div>
            <div class="print-ass-nome"><?php echo htmlspecialchars($obra->cliente_nome ?? '_______________________'); ?></div>
            <div class="print-ass-cargo">Cliente / Proprietário</div>
        </div>
        <div class="print-ass-box">
            <div class="print-ass-linha"></div>
            <div class="print-ass-nome"><?php echo date('d/m/Y'); ?></div>
            <div class="print-ass-cargo">Data do Relatório</div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="relatorio-footer">
        <p><strong><?php echo $emitente_nome; ?></strong></p>
        <?php if ($emitente_cnpj): ?><p>CNPJ: <?php echo $emitente_cnpj; ?></p><?php endif; ?>
        <p><?php echo $emitente_endereco; ?> - <?php echo $emitente_bairro; ?> - <?php echo $emitente_cidade; ?></p>
        <p style="margin-top: 10px;">Relatório gerado em <?php echo date('d/m/Y H:i:s'); ?> | <?php echo $numero_relatorio; ?></p>
    </div>
</div>

<?php
// Labels de status para etapas
$statusLabels = [
    'NaoIniciada' => 'NÃO INICIADA',
    'pendente' => 'PENDENTE',
    'em_andamento' => 'EM ANDAMENTO',
    'EmExecucao' => 'EM EXECUÇÃO',
    'concluida' => 'CONCLUÍDA',
    'Concluida' => 'CONCLUÍDA',
    'atrasada' => 'ATRASADA',
    'cancelada' => 'CANCELADA',
];
?>