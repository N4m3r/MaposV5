<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório da Obra - <?php echo htmlspecialchars($obra->nome ?? 'Obra'); ?></title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
        }
        .print-header-logo { max-width: 150px; max-height: 80px; }
        .print-header-info { text-align: right; }
        .print-header-info h2 { font-size: 16px; margin: 0 0 5px; color: #333; }
        .print-header-info p { font-size: 10px; margin: 2px 0; color: #666; }

        .print-title {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        .print-title h1 { font-size: 18px; margin: 0 0 5px; color: #333; }
        .print-title .relatorio-numero { font-size: 12px; color: #666; }

        .print-section { margin-bottom: 20px; }
        .print-section-title {
            font-size: 14px; font-weight: bold;
            background: #2c3e50; color: #fff;
            padding: 8px 12px; margin-bottom: 10px;
        }
        .print-info-grid { display: table; width: 100%; border-collapse: collapse; }
        .print-info-row { display: table-row; }
        .print-info-label {
            display: table-cell; width: 25%;
            padding: 6px 10px; background: #f9f9f9;
            font-weight: bold; border: 1px solid #ddd; font-size: 11px;
        }
        .print-info-value {
            display: table-cell; width: 75%;
            padding: 6px 10px; border: 1px solid #ddd; font-size: 11px;
        }
        .print-stats { display: flex; gap: 15px; margin-bottom: 20px; }
        .print-stat-box { flex: 1; border: 1px solid #ddd; padding: 10px; text-align: center; }
        .print-stat-value { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .print-stat-label { font-size: 10px; color: #666; text-transform: uppercase; }
        .print-progress-container { margin: 15px 0; }
        .print-progress-bar {
            height: 25px; background: #e9ecef; border: 1px solid #ddd;
            position: relative;
        }
        .print-progress-fill {
            height: 100%; background: #27ae60;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: bold; font-size: 12px;
        }
        .print-table {
            width: 100%; border-collapse: collapse;
            margin-bottom: 15px; font-size: 11px;
        }
        .print-table th {
            background: #2c3e50; color: #fff;
            padding: 8px; text-align: left; border: 1px solid #2c3e50;
        }
        .print-table td { padding: 6px 8px; border: 1px solid #ddd; }
        .print-table tr:nth-child(even) { background: #f9f9f9; }
        .print-badge {
            display: inline-block; padding: 2px 8px;
            font-size: 10px; border-radius: 3px; font-weight: bold;
        }
        .print-badge-concluida { background: #d4edda; color: #155724; }
        .print-badge-andamento { background: #fff3cd; color: #856404; }
        .print-badge-pendente { background: #f8d7da; color: #721c24; }
        .print-badge-paralisada { background: #ffeaa7; color: #d63031; }
        .print-signatures { margin-top: 40px; page-break-inside: avoid; }
        .print-signatures-grid { display: flex; justify-content: space-between; gap: 30px; }
        .print-signature-box { flex: 1; text-align: center; }
        .print-signature-line { border-top: 1px solid #333; margin: 60px 10px 5px; }
        .print-signature-name { font-weight: bold; font-size: 11px; }
        .print-signature-role { font-size: 10px; color: #666; }
        .print-footer {
            margin-top: 30px; padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9px; color: #666; text-align: center;
        }
        .print-page-break { page-break-before: always; }
        @media print {
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
        }
        .print-actions {
            position: fixed; top: 20px; right: 20px;
            display: flex; gap: 10px; z-index: 1000;
        }
        .print-btn {
            padding: 10px 20px; border: none; border-radius: 5px;
            cursor: pointer; font-size: 14px; font-weight: bold;
            display: flex; align-items: center; gap: 8px;
        }
        .print-btn-primary { background: #27ae60; color: white; }
        .print-btn-secondary { background: #6c757d; color: white; }
        .print-btn:hover { opacity: 0.9; }
        @media print { .print-actions { display: none !important; } }

        /* Fotos */
        .fotos-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .foto-item {
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            page-break-inside: avoid;
        }
        .foto-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
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
        // Estatísticas
        $percentual_concluido = $obra->percentual_concluido ?? 0;
        $total_atividades = $obra->total_atividades ?? 0;
        $ativ_concluidas = $obra->atividades_concluidas ?? 0;
        $ativ_em_andamento = 0;
        $ativ_pendentes = 0;
        foreach ($atividades as $a) {
            $s = strtolower($a->status ?? '');
            if (in_array($s, ['concluida', 'concluido', 'finalizado', 'finalizada'])) {
                // já contado
            } elseif (in_array($s, ['em_andamento', 'iniciada', 'reaberta'])) {
                $ativ_em_andamento++;
            } else {
                $ativ_pendentes++;
            }
        }
        if ($total_atividades > 0 && $ativ_pendentes === 0) {
            $ativ_pendentes = max(0, $total_atividades - $ativ_concluidas - $ativ_em_andamento);
        }

        $data_inicio = $obra->data_inicio_contrato ?? null;
        $data_prevista = $obra->data_fim_prevista ?? null;
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
        $numero_relatorio = 'REL-' . date('Y') . '-' . str_pad($obra->id ?? 0, 4, '0', STR_PAD_LEFT);

        $emitente_nome = $emitente->nome ?? '';
        $emitente_cnpj = $emitente->cnpj ?? '';
        $emitente_rua = $emitente->rua ?? $emitente->endereco ?? '';
        $emitente_numero = $emitente->numero ?? '';
        $emitente_endereco = trim($emitente_rua . ($emitente_numero ? ', ' . $emitente_numero : ''));
        $emitente_bairro = $emitente->bairro ?? '';
        $emitente_cidade = ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '');
        $emitente_telefone = $emitente->telefone ?? '';
        $emitente_logo = $emitente->url_logo ?? '';

        $cliente_nome = $cliente->nomeCliente ?? $obra->cliente_nome ?? 'N/A';
        $cliente_doc = $cliente->documento ?? 'N/A';
        ?>

        <!-- Cabeçalho com dados do emitente -->
        <div class="print-header">
            <?php if ($emitente_logo): ?>
            <div class="print-header-logo">
                <img src="<?php echo $emitente_logo; ?>" alt="Logo" style="max-width: 150px; max-height: 80px;">
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
                    <div class="print-info-value"><?php echo htmlspecialchars($cliente_nome); ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Documento:</div>
                    <div class="print-info-value"><?php echo htmlspecialchars($cliente_doc); ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Tipo:</div>
                    <div class="print-info-value">
                        <?php
                        $tiposLabels = ['Condominio'=>'Condomínio','Comercio'=>'Comércio','Residencia'=>'Residência','Industrial'=>'Industrial','Publica'=>'Pública'];
                        echo htmlspecialchars($tiposLabels[$obra->tipo_obra] ?? ($obra->tipo_obra ?? 'N/A'));
                        ?>
                    </div>
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
                    <div class="print-info-label">Data de Início:</div>
                    <div class="print-info-value"><?php echo $data_inicio ? date('d/m/Y', strtotime($data_inicio)) : 'N/D'; ?></div>
                </div>
                <div class="print-info-row">
                    <div class="print-info-label">Previsão de Término:</div>
                    <div class="print-info-value"><?php echo $data_prevista ? date('d/m/Y', strtotime($data_prevista)) : 'N/D'; ?></div>
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
                    <div class="print-stat-value"><?php echo $ativ_concluidas; ?></div>
                    <div class="print-stat-label">Concluídas</div>
                </div>
                <div class="print-stat-box">
                    <div class="print-stat-value"><?php echo $ativ_em_andamento; ?></div>
                    <div class="print-stat-label">Em Andamento</div>
                </div>
                <div class="print-stat-box">
                    <div class="print-stat-value"><?php echo $ativ_pendentes; ?></div>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etapas as $etapa):
                        $etapa_pct = $etapa->percentual_real ?? ($etapa->percentual_concluido ?? 0);
                        $etapa_status = $etapa->status ?? 'NaoIniciada';
                        $total_ativ = $etapa->total_atividades ?? 0;
                        $concl_ativ = $etapa->atividades_concluidas ?? 0;

                        // Determinar status real
                        if ($total_ativ > 0 && $concl_ativ === $total_ativ) {
                            $badgeClass = 'print-badge-concluida';
                            $statusText = 'Concluída';
                        } elseif ($concl_ativ > 0) {
                            $badgeClass = 'print-badge-andamento';
                            $statusText = 'Em Andamento';
                        } else {
                            $badgeClass = 'print-badge-pendente';
                            $statusText = 'Não Iniciada';
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($etapa->nome ?? 'Sem nome'); ?></td>
                        <td><span class="print-badge <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span></td>
                        <td><?php echo $etapa_pct; ?>%</td>
                        <td><?php echo $concl_ativ; ?>/<?php echo $total_ativ; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Atividades -->
        <?php if (!empty($atividades)): ?>
        <div class="print-section">
            <div class="print-section-title">REGISTRO DE ATIVIDADES</div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Atividade</th>
                        <th>Técnico</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($atividades as $atv):
                        $atv_status = strtolower($atv->status ?? 'agendada');
                        switch($atv_status) {
                            case 'concluida': case 'concluido': case 'finalizado': case 'finalizada':
                                $statusClass = 'print-badge-concluida';
                                $statusLabel = 'Concluída';
                                break;
                            case 'em_andamento': case 'iniciada': case 'reaberta':
                                $statusClass = 'print-badge-andamento';
                                $statusLabel = 'Em Andamento';
                                break;
                            case 'pausada': case 'pausado':
                                $statusClass = 'print-badge-paralisada';
                                $statusLabel = 'Pausada';
                                break;
                            default:
                                $statusClass = 'print-badge-pendente';
                                $statusLabel = 'Pendente';
                        }
                    ?>
                    <tr>
                        <td><?php echo !empty($atv->data_atividade) ? date('d/m/Y', strtotime($atv->data_atividade)) : (!empty($atv->created_at) ? date('d/m/Y', strtotime($atv->created_at)) : '-'); ?></td>
                        <td><?php echo htmlspecialchars($atv->titulo ?? $atv->descricao ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($atv->tecnico_nome ?? 'N/A'); ?></td>
                        <td><span class="print-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Fotos -->
        <?php if (!empty($fotos) && count($fotos) > 0): ?>
        <div class="print-section">
            <div class="print-section-title">FOTOS DO ATENDIMENTO (<?php echo count($fotos); ?>)</div>
            <div class="fotos-grid">
                <?php foreach ($fotos as $foto):
                    $url_foto = is_string($foto) ? $foto : ($foto->caminho ?? $foto->url ?? '');
                    if (strpos($url_foto, 'http') !== 0 && strpos($url_foto, '//') !== 0) {
                        $url_foto = base_url($url_foto);
                    }
                ?>
                <div class="foto-item no-break">
                    <img src="<?php echo $url_foto; ?>" alt="Foto">
                </div>
                <?php endforeach; ?>
            </div>
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
                        <td><?php echo htmlspecialchars($membro->tecnico_nome ?? $membro->nome ?? $membro->nomeUsuario ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></td>
                        <td><?php echo htmlspecialchars($membro->especialidade ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- OS Vinculadas -->
        <?php if (!empty($os_vinculadas)): ?>
        <div class="print-section">
            <div class="print-section-title">ORDENS DE SERVIÇO VINCULADAS</div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th>OS #</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Responsável</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($os_vinculadas as $os): ?>
                    <tr>
                        <td>#<?php echo $os->idOs ?? '-'; ?></td>
                        <td><?php echo !empty($os->dataInicial) ? date('d/m/Y', strtotime($os->dataInicial)) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($os->status ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($os->responsavel ?? $os->nomeCliente ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Observações -->
        <?php if (!empty($obra->observacoes)): ?>
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
                    <div class="print-signature-name"><?php echo htmlspecialchars($obra->responsavel_tecnico_nome ?? '_______________________'); ?></div>
                    <div class="print-signature-role">Responsável Técnico</div>
                </div>
                <div class="print-signature-box">
                    <div class="print-signature-line"></div>
                    <div class="print-signature-name"><?php echo htmlspecialchars($cliente_nome ?? '_______________________'); ?></div>
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
