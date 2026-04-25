<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório da Obra - <?= htmlspecialchars($obra->nome ?? 'Obra') ?></title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            box-sizing: border-box;
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999;
        }
        .btn-print:hover { transform: translateY(-2px); }

        /* Cabeçalho */
        .header-emitente {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
        }
        .header-emitente .logo {
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
        }
        .header-emitente .info { flex: 1; }
        .header-emitente .info h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 4px;
        }
        .header-emitente .info p {
            font-size: 12px;
            color: #666;
            margin: 2px 0;
        }
        .header-emitente .doc {
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        .header-emitente .doc .numero {
            font-size: 16px;
            font-weight: 700;
            color: #667eea;
        }

        .relatorio-titulo {
            text-align: center;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .relatorio-titulo h1 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .relatorio-titulo p { font-size: 12px; opacity: 0.9; }

        .section {
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .section-header {
            background: #f8f9fa;
            padding: 10px 15px;
            font-size: 13px;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e0e0e0;
        }
        .section-body { padding: 15px; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .info-item { display: flex; flex-direction: column; gap: 2px; }
        .info-item.full-width { grid-column: span 2; }
        .info-label {
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .progress-bar {
            height: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 10px;
            color: white;
            font-size: 11px;
            font-weight: 700;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-badge.concluida { background: #e8f5e9; color: #27ae60; border: 1px solid #27ae60; }
        .status-badge.andamento { background: #e3f2fd; color: #2196f3; border: 1px solid #2196f3; }
        .status-badge.pendente { background: #f8f9fa; color: #666; border: 1px solid #ddd; }

        .etapa-item {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-left: 3px solid #667eea;
        }
        .etapa-item.concluida { border-left-color: #27ae60; background: #e8f5e9; }
        .etapa-item.andamento { border-left-color: #2196f3; background: #e3f2fd; }
        .etapa-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .etapa-nome { font-weight: 700; color: #333; font-size: 14px; }
        .etapa-meta { font-size: 11px; color: #888; margin-top: 4px; }

        .atividade-item {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 8px;
            background: #fff;
            border: 1px solid #e0e0e0;
        }
        .atividade-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .atividade-titulo { font-weight: 600; color: #333; font-size: 13px; }
        .atividade-meta { font-size: 11px; color: #888; margin-top: 4px; }

        .fotos-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .foto-item {
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .foto-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }

        .equipe-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .equipe-item {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .equipe-nome { font-weight: 600; font-size: 12px; color: #333; }
        .equipe-cargo { font-size: 11px; color: #888; }

        .rodape {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 11px;
            color: #888;
        }

        @media print {
            body { padding: 15mm; width: 210mm; min-height: 297mm; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .btn-print { display: none !important; }
            .section { break-inside: avoid; }
            .no-break { break-inside: avoid; }
        }
    </style>
</head>
<body>

<button class="btn-print" onclick="window.print()">
    <span style="font-size: 16px;">&#x1F5A8;</span> Imprimir Relatório
</button>

<!-- Cabeçalho Emitente -->
<div class="header-emitente">
    <?php if (!empty($emitente->url_logo)): ?>
    <img src="<?php echo $emitente->url_logo; ?>" alt="Logo" class="logo">
    <?php endif; ?>
    <div class="info">
        <h2><?php echo htmlspecialchars($emitente->nome ?? $emitente->emitente ?? 'Empresa'); ?></h2>
        <p><?php echo htmlspecialchars($emitente->rua ?? $emitente->endereco ?? 'Endereço não informado'); ?>
            <?php if (!empty($emitente->numero)): ?>, <?php echo $emitente->numero; ?><?php endif; ?></p>
        <p><?php echo htmlspecialchars($emitente->bairro ?? ''); ?>
            <?php if (!empty($emitente->cidade)): ?> - <?php echo $emitente->cidade; ?><?php endif; ?>
            <?php if (!empty($emitente->uf)): ?>/<?php echo $emitente->uf; ?><?php endif; ?></p>
        <?php if (!empty($emitente->telefone)): ?><p>Tel: <?php echo $emitente->telefone; ?></p><?php endif; ?>
    </div>
    <div class="doc">
        <div class="numero">#<?php echo $obra->id ?? ''; ?></div>
        <div>Relatório Geral da Obra</div>
        <div><?php echo date('d/m/Y H:i'); ?></div>
    </div>
</div>

<!-- Título -->
<div class="relatorio-titulo">
    <h1>Relatório Geral da Obra</h1>
    <p>Acompanhamento completo da execução da obra</p>
</div>

<!-- Dados da Obra -->
<div class="section">
    <div class="section-header">Dados da Obra</div>
    <div class="section-body">
        <div class="info-grid">
            <div class="info-item full-width">
                <span class="info-label">Nome da Obra</span>
                <span class="info-value"><?php echo htmlspecialchars($obra->nome ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Código</span>
                <span class="info-value"><?php echo htmlspecialchars($obra->codigo ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="status-badge <?php echo strtolower($obra->status ?? 'pendente'); ?>">
                        <?php echo ucfirst($obra->status ?? 'Pendente'); ?>
                    </span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Tipo</span>
                <span class="info-value"><?php echo htmlspecialchars($obra->tipo_obra ?? 'Não informado'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Cliente</span>
                <span class="info-value"><?php echo htmlspecialchars($cliente->nomeCliente ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Documento</span>
                <span class="info-value"><?php echo htmlspecialchars($cliente->documento ?? 'N/A'); ?></span>
            </div>
            <div class="info-item full-width">
                <span class="info-label">Endereço</span>
                <span class="info-value">
                    <?php
                    $endereco = [];
                    if (!empty($obra->endereco)) $endereco[] = $obra->endereco;
                    if (!empty($obra->bairro)) $endereco[] = $obra->bairro;
                    if (!empty($obra->cidade)) $endereco[] = $obra->cidade;
                    if (!empty($obra->estado)) $endereco[] = $obra->estado;
                    echo !empty($endereco) ? htmlspecialchars(implode(', ', $endereco)) : 'Não informado';
                    ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Data Início</span>
                <span class="info-value"><?php echo !empty($obra->data_inicio_contrato) ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : 'N/A'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Previsão Término</span>
                <span class="info-value"><?php echo !empty($obra->data_fim_prevista) ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : 'N/A'; ?></span>
            </div>
            <div class="info-item full-width">
                <span class="info-label">Progresso da Obra</span>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $obra->percentual_concluido ?? 0; ?>%">
                        <?php echo $obra->percentual_concluido ?? 0; ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Etapas -->
<?php if (!empty($etapas)): ?>
<div class="section">
    <div class="section-header">Etapas da Obra (<?php echo count($etapas); ?>)</div>
    <div class="section-body">
        <?php foreach ($etapas as $etapa):
            $pct = $etapa->percentual_concluido ?? ($etapa->percentual_real ?? 0);
            $stClass = $pct >= 100 ? 'concluida' : ($pct > 0 ? 'andamento' : '');
        ?>
        <div class="etapa-item <?php echo $stClass; ?> no-break">
            <div class="etapa-header">
                <div>
                    <div class="etapa-nome">#<?php echo $etapa->numero_etapa ?? $etapa->id; ?> - <?php echo htmlspecialchars($etapa->nome); ?></div>
                    <div class="etapa-meta">
                        <?php echo htmlspecialchars($etapa->especialidade ?? 'Sem especialidade'); ?> |
                        <?php echo $etapa->total_atividades ?? 0; ?> atividades |
                        <?php echo $etapa->atividades_concluidas ?? 0; ?> concluídas
                    </div>
                </div>
                <span class="status-badge <?php echo $stClass ?: 'pendente'; ?>">
                    <?php echo $pct; ?>%
                </span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $pct; ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Atividades -->
<?php if (!empty($atividades)): ?>
<div class="section">
    <div class="section-header">Atividades Realizadas (<?php echo count($atividades); ?>)</div>
    <div class="section-body">
        <?php foreach ($atividades as $atv):
            $st = strtolower($atv->status ?? 'agendada');
            $stBadge = in_array($st, ['concluida','concluido','finalizado','finalizada']) ? 'concluida' : (in_array($st, ['em_andamento','iniciada']) ? 'andamento' : 'pendente');
        ?>
        <div class="atividade-item no-break">
            <div class="atividade-header">
                <div class="atividade-titulo"><?php echo htmlspecialchars($atv->titulo ?? $atv->descricao ?? 'Atividade #' . ($atv->id ?? '')); ?></div>
                <span class="status-badge <?php echo $stBadge; ?>"><?php echo ucfirst($atv->status ?? 'Agendada'); ?></span>
            </div>
            <div class="atividade-meta">
                <i class="bx bx-user"></i> <?php echo htmlspecialchars($atv->tecnico_nome ?? 'Não atribuído'); ?>
                &nbsp;|&nbsp;
                <i class="bx bx-calendar"></i> <?php echo !empty($atv->data_atividade) ? date('d/m/Y', strtotime($atv->data_atividade)) : 'N/A'; ?>
                <?php if (!empty($atv->hora_inicio)): ?>
                &nbsp;|&nbsp;
                <i class="bx bx-time"></i> <?php echo date('H:i', strtotime($atv->hora_inicio)); ?>
                <?php if (!empty($atv->hora_fim)): ?> - <?php echo date('H:i', strtotime($atv->hora_fim)); ?><?php endif; ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($atv->observacoes)): ?>
            <div style="margin-top: 8px; padding: 8px; background: #f8f9fa; border-radius: 4px; font-size: 12px;">
                <strong>Obs:</strong> <?php echo nl2br(htmlspecialchars($atv->observacoes)); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Fotos -->
<?php if (!empty($fotos)): ?>
<div class="section">
    <div class="section-header">Fotos do Atendimento (<?php echo count($fotos); ?>)</div>
    <div class="section-body">
        <div class="fotos-grid">
            <?php foreach ($fotos as $foto): ?>
            <div class="foto-item no-break">
                <img src="<?php echo base_url($foto); ?>" alt="Foto">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Equipe -->
<?php if (!empty($equipe)): ?>
<div class="section">
    <div class="section-header">Equipe da Obra (<?php echo count($equipe); ?>)</div>
    <div class="section-body">
        <div class="equipe-grid">
            <?php foreach ($equipe as $membro): ?>
            <div class="equipe-item no-break">
                <div class="equipe-nome"><?php echo htmlspecialchars($membro->tecnico_nome ?? '-'); ?></div>
                <div class="equipe-cargo"><?php echo htmlspecialchars($membro->funcao ?? 'Técnico'); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- OS Vinculadas -->
<?php if (!empty($os_vinculadas)): ?>
<div class="section">
    <div class="section-header">Ordens de Serviço Vinculadas</div>
    <div class="section-body">
        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e0e0e0;">OS #</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e0e0e0;">Data</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e0e0e0;">Status</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e0e0e0;">Responsável</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($os_vinculadas as $os): ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">#<?php echo $os->idOs ?? '-'; ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo !empty($os->dataInicial) ? date('d/m/Y', strtotime($os->dataInicial)) : '-'; ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($os->status ?? '-'); ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($os->nomeCliente ?? $os->responsavel ?? '-'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Rodapé -->
<div class="rodape no-break">
    <p><strong><?php echo htmlspecialchars($emitente->nome ?? $emitente->emitente ?? ''); ?></strong></p>
    <?php if (!empty($emitente->telefone)): ?><p>Tel: <?php echo $emitente->telefone; ?></p><?php endif; ?>
    <p>Relatório gerado em <?php echo date('d/m/Y \à\s H:i'); ?> pelo sistema Map-OS</p>
    <p style="margin-top: 5px; font-size: 10px;">Este documento é um relatório gerado automaticamente pelo sistema e não possui valor fiscal.</p>
</div>

</body>
</html>
