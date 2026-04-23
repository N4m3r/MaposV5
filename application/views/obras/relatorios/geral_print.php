<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório da Obra - <?php echo htmlspecialchars($obra->nome ?? 'Obra'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
        }

        /* Cabeçalho com emitente */
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
        }

        .print-header-logo {
            max-width: 150px;
            max-height: 80px;
        }

        .print-header-info {
            text-align: right;
        }

        .print-header-info h2 {
            font-size: 16px;
            margin: 0 0 5px;
            color: #333;
        }

        .print-header-info p {
            font-size: 10px;
            margin: 2px 0;
            color: #666;
        }

        /* Título do relatório */
        .print-title {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }

        .print-title h1 {
            font-size: 18px;
            margin: 0 0 5px;
            color: #333;
        }

        .print-title .relatorio-numero {
            font-size: 12px;
            color: #666;
        }

        /* Seções */
        .print-section {
            margin-bottom: 20px;
        }

        .print-section-title {
            font-size: 14px;
            font-weight: bold;
            background: #2c3e50;
            color: #fff;
            padding: 8px 12px;
            margin-bottom: 10px;
        }

        /* Informações da obra */
        .print-info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .print-info-row {
            display: table-row;
        }

        .print-info-label {
            display: table-cell;
            width: 25%;
            padding: 6px 10px;
            background: #f9f9f9;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        .print-info-value {
            display: table-cell;
            width: 75%;
            padding: 6px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        /* Cards de estatísticas */
        .print-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .print-stat-box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .print-stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        .print-stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        /* Progresso */
        .print-progress-container {
            margin: 15px 0;
        }

        .print-progress-bar {
            height: 25px;
            background: #e9ecef;
            border: 1px solid #ddd;
            position: relative;
        }

        .print-progress-fill {
            height: 100%;
            background: #27ae60;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
            font-size: 12px;
        }

        /* Tabelas */
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .print-table th {
            background: #2c3e50;
            color: #fff;
            padding: 8px;
            text-align: left;
            border: 1px solid #2c3e50;
        }

        .print-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        .print-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Status badges */
        .print-badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 10px;
            border-radius: 3px;
            font-weight: bold;
        }

        .print-badge-concluida { background: #d4edda; color: #155724; }
        .print-badge-andamento { background: #fff3cd; color: #856404; }
        .print-badge-pendente { background: #f8d7da; color: #721c24; }
        .print-badge-paralisada { background: #ffeaa7; color: #d63031; }

        /* Assinaturas */
        .print-signatures {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .print-signatures-grid {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .print-signature-box {
            flex: 1;
            text-align: center;
        }

        .print-signature-line {
            border-top: 1px solid #333;
            margin: 60px 10px 5px;
        }

        .print-signature-name {
            font-weight: bold;
            font-size: 11px;
        }

        .print-signature-role {
            font-size: 10px;
            color: #666;
        }

        /* Rodapé */
        .print-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
            text-align: center;
        }

        /* Quebras de página */
        .print-page-break {
            page-break-before: always;
        }

        /* Esconder elementos não necessários na impressão */
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Botão de imprimir na tela */
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .print-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .print-btn-primary {
            background: #27ae60;
            color: white;
        }

        .print-btn-secondary {
            background: #6c757d;
            color: white;
        }

        .print-btn:hover {
            opacity: 0.9;
        }

        @media print {
            .print-actions {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Botões de ação -->
    <div class="print-actions no-print">
        <button onclick="window.print()" class="print-btn print-btn-primary">
            <i class="icon-print"></i> Imprimir / Salvar PDF
        </button>
        <button onclick="window.close()" class="print-btn print-btn-secondary">
            <i class="icon-remove"></i> Fechar
        </button>
    </div>

    <div class="print-container">
        <?php
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

        // Status da obra
        $statusObra = $obra->status ?? 'Em Andamento';
        $statusLabels = [
            'Prospeccao' => 'Prospecção',
            'Orcamentacao' => 'Orçamentação',
            'Contratada' => 'Contratada',
            'EmExecucao' => 'Em Execução',
            'Em Andamento' => 'Em Andamento',
            'Paralisada' => 'Paralisada',
            'Finalizada' => 'Finalizada',
            'Entregue' => 'Entregue',
            'Garantia' => 'Garantia',
            'Concluida' => 'Concluída',
            'Concluída' => 'Concluída',
        ];

        // Formatar valor do contrato
        $valor_contrato = $obra->valor_contrato ?? 0;
        $valor_formatado = $valor_contrato ? 'R$ ' . number_format($valor_contrato, 2, ',', '.') : 'N/C';

        // Número do relatório
        $numero_relatorio = 'REL-' . date('Y') . '-' . str_pad($obra->id, 4, '0', STR_PAD_LEFT);

        // Dados do emitente
        $emitente_nome = $emitente->nome ?? '';
        $emitente_cnpj = $emitente->cnpj ?? '';
        $emitente_endereco = ($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '');
        $emitente_bairro = $emitente->bairro ?? '';
        $emitente_cidade = ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '');
        $emitente_telefone = $emitente->telefone ?? '';
        $emitente_logo = $emitente->url_logo ?? '';
        ?>

        <!-- Cabeçalho com dados do emitente -->
        <div class="print-header">
            <?php if ($emitente_logo): ?>
            <div class="print-header-logo">
                <img src="<?php echo base_url($emitente_logo); ?>" alt="Logo" style="max-width: 150px; max-height: 80px;">
            </div>
            <?php endif; ?>
            <div class="print-header-info" style="<?php echo $emitente_logo ? '' : 'text-align: left; width: 100%;'; ?>">
                <h2><?php echo htmlspecialchars($emitente_nome); ?></h2>
                <?php if ($emitente_cnpj): ?>
                <p>CNPJ: <?php echo htmlspecialchars($emitente_cnpj); ?></p>
                <?php endif; ?>
                <?php if ($emitente_endereco): ?>
                <p><?php echo htmlspecialchars($emitente_endereco); ?></p>
                <?php endif; ?>
                <?php if ($emitente_bairro || $emitente_cidade): ?>
                <p><?php echo htmlspecialchars($emitente_bairro); ?><?php echo $emitente_bairro && $emitente_cidade ? ' - ' : ''; ?><?php echo htmlspecialchars($emitente_cidade); ?></p>
                <?php endif; ?>
                <?php if ($emitente_telefone): ?>
                <p>Tel: <?php echo htmlspecialchars($emitente_telefone); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Título do relatório -->
        <div class="print-title">
            <h1>RELATÓRIO GERAL DE OBRA</h1>
            <div class="relatorio-numero">
                Nº <?php echo $numero_relatorio; ?> | Emitido em <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>

        <!-- Dados da Obra -->
        <div class="print-section">
            <div class="print-section-title">DADOS DA OBRA</div>
            <div class="print-info-grid">
                <div class="print-info-row">
                    <div class="print-info-label">Nome da Obra:</div>
                    <div class="print-info-value"><?php echo htmlspecialchars($obra->nome ?? 'N/A'); ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Cliente:</div>
                    <div class="print-info-value"><?php echo htmlspecialchars($obra->cliente_nome ?? 'N/A'); ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Tipo:</div>
                    <div class="print-info-value"><?php echo htmlspecialchars($obra->tipo_obra ?? 'N/A'); ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Status:</div>
                    <div class="print-info-value"><?php echo htmlspecialchars($statusLabels[$statusObra] ?? $statusObra); ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Endereço:</div>
                    <div class="print-info-value">
                        <?php echo htmlspecialchars($obra->endereco ?? 'N/A'); ?>
                        <?php if ($obra->bairro): ?> - <?php echo htmlspecialchars($obra->bairro); ?><?php endif; ?>
                        <?php if ($obra->cidade): ?>, <?php echo htmlspecialchars($obra->cidade); ?> - <?php echo htmlspecialchars($obra->estado ?? ''); ?><?php endif; ?>
                    </div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Valor do Contrato:</div>
                    <div class="print-info-value"><?php echo $valor_formatado; ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Data de Início:</div>
                    <div class="print-info-value"><?php echo $data_inicio ? date('d/m/Y', strtotime($data_inicio)) : 'N/D'; ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Previsão de Término:</div>
                    <div class="print-info-value"><?php echo $data_prevista ? date('d/m/Y', strtotime($data_prevista)) : 'N/D'; ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Gestor Responsável:</div>
                    <div class="print-info-value"><?php echo htmlspecialchars($obra->gestor_nome ?? 'N/A'); ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Responsável Técnico:</div>
                    <div class="print-info-value"><?php echo htmlspecialchars($obra->responsavel_tecnico_nome ?? 'N/A'); ?></div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="print-section">
            <div class="print-section-title">RESUMO DO PROJETO</div>
            <div class="print-stats">
                <div class="print-stat-box">
                    <div class="print-stat-value"><?php echo $percentual_concluido; ?>%</div>
                    <div class="print-stat-label">Progresso Geral</div>
                </div>
                <div class="print-stat-box">
                    <div class="print-stat-value"><?php echo $total_atividades; ?></div>
                    <div class="print-stat-label">Total de Atividades</div>
                </div>
                <div class="print-stat-box">
                    <div class="print-stat-value"><?php echo $concluidas; ?></div>
                    <div class="print-stat-label">Concluídas</div>
                </div>
                <div class="print-stat-box">
                    <div class="print-stat-value"><?php echo $em_andamento; ?></div>
                    <div class="print-stat-label">Em Andamento</div>
                </div>
                <div class="print-stat-box">
                    <div class="print-stat-value"><?php echo $pendentes; ?></div>
                    <div class="print-stat-label">Pendentes</div>
                </div>
            </div>

            <div class="print-progress-container">
                <div class="print-progress-bar">
                    <div class="print-progress-fill" style="width: <?php echo $percentual_concluido; ?>%">
                        <?php echo $percentual_concluido; ?>% Concluído
                    </div>
                </div>
            </div>
        </div>

        <!-- Etapas -->
        <?php if (!empty($etapas)): ?>
        <div class="print-section">
            <div class="print-section-title">ETAPAS DA OBRA</div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th>Etapa</th>
                        <th>Status</th>
                        <th>Progresso</th>
                        <th>Atividades</th>
                        <th>Horas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etapas as $etapa):
                        $etapa_pct = $etapa->percentual_concluido ?? 0;
                        $etapa_status = $etapa->status ?? 'NaoIniciada';
                        $badgeClass = '';
                        $statusText = '';
                        switch($etapa_status) {
                            case 'Concluida':
                                $badgeClass = 'print-badge-concluida';
                                $statusText = 'Concluída';
                                break;
                            case 'EmAndamento':
                                $badgeClass = 'print-badge-andamento';
                                $statusText = 'Em Andamento';
                                break;
                            case 'Paralisada':
                                $badgeClass = 'print-badge-paralisada';
                                $statusText = 'Paralisada';
                                break;
                            default:
                                $badgeClass = 'print-badge-pendente';
                                $statusText = 'Não Iniciada';
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($etapa->nome ?? 'Sem nome'); ?></td>
                        <td><span class="print-badge <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span></td>
                        <td><?php echo $etapa_pct; ?>%</td>
                        <td><?php echo $etapa->atividades_concluidas ?? 0; ?>/<?php echo $etapa->total_atividades ?? 0; ?></td>
                        <td><?php echo $etapa->horas_trabalhadas ?? 0; ?>h</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Atividades do Sistema Novo -->
        <?php if (!empty($atividades_sistema)): ?>
        <div class="print-section">
            <div class="print-section-title">REGISTRO DE ATIVIDADES</div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Atividade</th>
                        <th>Técnico</th>
                        <th>Status</th>
                        <th>Duração</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($atividades_sistema as $atv):
                        $atv_status = $atv->status ?? 'agendada';
                        $statusClass = '';
                        $statusLabel = '';
                        switch($atv_status) {
                            case 'finalizada':
                                $statusClass = 'print-badge-concluida';
                                $statusLabel = 'Concluída';
                                break;
                            case 'em_andamento':
                                $statusClass = 'print-badge-andamento';
                                $statusLabel = 'Em Andamento';
                                break;
                            case 'pausada':
                                $statusClass = 'print-badge-paralisada';
                                $statusLabel = 'Pausada';
                                break;
                            default:
                                $statusClass = 'print-badge-pendente';
                                $statusLabel = 'Pendente';
                        }
                        $duracao = '';
                        if ($atv->duracao_minutos) {
                            $horas = floor($atv->duracao_minutos / 60);
                            $minutos = $atv->duracao_minutos % 60;
                            $duracao = $horas . 'h' . ($minutos ? ' ' . $minutos . 'min' : '');
                        }
                    ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($atv->hora_inicio ?? $atv->created_at)); ?></td>
                        <td><?php echo htmlspecialchars($atv->titulo ?? $atv->descricao ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($atv->nome_tecnico ?? 'N/A'); ?></td>
                        <td><span class="print-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td><?php echo $duracao ?: '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Equipe -->
        <?php if (!empty($equipe)): ?>
        <div class="print-section">
            <div class="print-section-title">EQUIPE</div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Especialidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipe as $membro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($membro->nome ?? $membro->nomeUsuario ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></td>
                        <td><?php echo htmlspecialchars($membro->especialidade ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Observações -->
        <?php if ($obra->observacoes): ?>
        <div class="print-section">
            <div class="print-section-title">OBSERVAÇÕES</div>
            <div style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9; min-height: 80px;">
                <?php echo nl2br(htmlspecialchars($obra->observacoes)); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Assinaturas -->
        <div class="print-signatures">
            <div class="print-section-title">ASSINATURAS</div>
            <div class="print-signatures-grid">
                <div class="print-signature-box">
                    <div class="print-signature-line"></div>
                    <div class="print-signature-name"><?php echo htmlspecialchars($obra->gestor_nome ?? '_______________________'); ?></div>
                    <div class="print-signature-role">Gestor de Projeto</div>
                </div>
                <div class="print-signature-box">
                    <div class="print-signature-line"></div>
                    <div class="print-signature-name"><?php echo htmlspecialchars($obra->responsavel_tecnico_nome ?? $obra->gestor_nome ?? '_______________________'); ?></div>
                    <div class="print-signature-role">Responsável Técnico</div>
                </div>
                <div class="print-signature-box">
                    <div class="print-signature-line"></div>
                    <div class="print-signature-name"><?php echo htmlspecialchars($obra->cliente_nome ?? '_______________________'); ?></div>
                    <div class="print-signature-role">Cliente / Proprietário</div>
                </div>
                <div class="print-signature-box">
                    <div class="print-signature-line"></div>
                    <div class="print-signature-name"><?php echo date('d/m/Y'); ?></div>
                    <div class="print-signature-role">Data do Relatório</div>
                </div>
            </div>
        </div>

        <!-- Rodapé -->
        <div class="print-footer">
            Documento gerado eletronicamente pelo sistema MapOS - <?php echo htmlspecialchars($emitente_nome); ?><br>
            Este relatório é válido sem assinatura e carimbo quando acompanhado do respectivo documento digital.
        </div>
    </div>
</body>
</html>
