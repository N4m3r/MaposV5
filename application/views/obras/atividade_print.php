<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Atendimento #<?php echo $atividade->id ?? ''; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* A4 Page */
        @page {
            size: A4;
            margin: 15mm;
        }

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

        /* Cabeçalho Emitente */
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
        .header-emitente .info {
            flex: 1;
        }
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

        /* Título do relatório */
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
        .relatorio-titulo p {
            font-size: 12px;
            opacity: 0.9;
        }

        /* Seções */
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
        .section-body {
            padding: 15px;
        }

        /* Grid de informações */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
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

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-badge.agendada { background: #f8f9fa; color: #666; border: 1px solid #ddd; }
        .status-badge.iniciada { background: #fff8e6; color: #f39c12; border: 1px solid #f39c12; }
        .status-badge.pausada { background: #ffebee; color: #e74c3c; border: 1px solid #e74c3c; }
        .status-badge.concluida { background: #e8f5e9; color: #27ae60; border: 1px solid #27ae60; }
        .status-badge.cancelada { background: #eceff1; color: #546e7f; border: 1px solid #546e7f; }
        .status-badge.reaberta { background: #f3e5f5; color: #9b59b6; border: 1px solid #9b59b6; }
        .status-badge.impedimento { background: #fff3e0; color: #e67e22; border: 1px solid #e67e22; }

        /* Progresso */
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

        /* Fotos */
        .fotos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
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
        .foto-badge {
            padding: 4px 8px;
            font-size: 10px;
            font-weight: 600;
            text-align: center;
            color: white;
        }
        .foto-badge.checkin { background: #11998e; }
        .foto-badge.checkout { background: #e74c3c; }
        .foto-badge.execucao { background: #667eea; }
        .foto-badge.impedimento { background: #e67e22; }

        /* Anotações */
        .anotacao-box {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 3px solid;
        }
        .anotacao-box.observacao { background: #f8f9fa; border-left-color: #3498db; }
        .anotacao-box.problema { background: #fff8e6; border-left-color: #f39c12; }
        .anotacao-box.solucao { background: #e8f5e9; border-left-color: #27ae60; }
        .anotacao-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 6px;
            color: #555;
        }
        .anotacao-text {
            font-size: 13px;
            color: #333;
            white-space: pre-wrap;
        }

        /* Execuções */
        .execucao-item {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-left: 3px solid #667eea;
        }
        .execucao-item.reatendimento { border-left-color: #9b59b6; background: #faf5ff; }
        .execucao-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .execucao-tag {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            color: white;
        }
        .execucao-tag.original { background: #27ae60; }
        .execucao-tag.reatendimento { background: #9b59b6; }
        .execucao-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .execucao-grid .info-item { gap: 2px; }

        /* Check-ins */
        .checkin-item {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 8px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .checkin-tipo {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            color: white;
        }
        .checkin-tipo.entrada { background: #27ae60; }
        .checkin-tipo.saida { background: #e74c3c; }

        /* Histórico */
        .historico-item {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .historico-item:last-child { border-bottom: none; }
        .historico-data { font-size: 11px; color: #888; white-space: nowrap; margin-left: 10px; }
        .historico-desc { font-size: 12px; color: #333; }

        /* Localização */
        .localizacao-box {
            padding: 10px;
            background: #e3f2fd;
            border-radius: 6px;
            font-size: 12px;
        }

        /* Rodapé */
        .rodape {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 11px;
            color: #888;
        }

        /* Assinaturas */
        .assinaturas {
            margin-top: 40px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
        }
        .assinatura-box {
            text-align: center;
        }
        .assinatura-linha {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 8px;
            font-size: 12px;
            color: #333;
        }
        .assinatura-linha .cargo {
            font-size: 10px;
            color: #888;
        }

        /* Botão imprimir */
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

        @media print {
            body {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                padding: 15mm;
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .btn-print { display: none !important; }
            .section { break-inside: avoid; }
            .fotos-grid { grid-template-columns: repeat(4, 1fr); }
            .no-break { break-inside: avoid; }
        }

        @media (max-width: 600px) {
            .info-grid { grid-template-columns: 1fr; }
            .info-item.full-width { grid-column: span 1; }
            .execucao-grid { grid-template-columns: 1fr; }
            .assinaturas { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<button class="btn-print" onclick="window.print()">
    <span style="font-size: 16px;">🖨️</span> Imprimir Relatório
</button>

<!-- Cabeçalho Emitente -->
<div class="header-emitente">
    <?php if (!empty($emitente->url_logo)): ?>
    <img src="<?php echo $emitente->url_logo; ?>" alt="Logo" class="logo">
    <?php endif; ?>
    <div class="info">
        <h2><?php echo htmlspecialchars($emitente->nome ?? $emitente->emitente ?? 'Empresa'); ?></h2>
        <p><?php echo htmlspecialchars($emitente->endereco ?? 'Endereço não informado'); ?>
            <?php if (!empty($emitente->numero)): ?>, <?php echo $emitente->numero; ?><?php endif; ?></p>
        <p><?php echo htmlspecialchars($emitente->bairro ?? ''); ?>
            <?php if (!empty($emitente->cidade)): ?> - <?php echo $emitente->cidade; ?><?php endif; ?>
            <?php if (!empty($emitente->estado)): ?>/<?php echo $emitente->estado; ?><?php endif; ?></p>
        <?php if (!empty($emitente->telefone)): ?><p>Tel: <?php echo $emitente->telefone; ?></p><?php endif; ?>
        <?php if (!empty($emitente->email)): ?><p>Email: <?php echo $emitente->email; ?></p><?php endif; ?>
    </div>
    <div class="doc">
        <div class="numero">#<?php echo $atividade->id ?? ''; ?></div>
        <div>Relatório de Atendimento</div>
        <div><?php echo date('d/m/Y H:i'); ?></div>
    </div>
</div>

<!-- Título -->
<div class="relatorio-titulo">
    <h1>Relatório de Atendimento</h1>
    <p>Atividade realizada em campo conforme registro do sistema</p>
</div>

<!-- Dados da Obra e Cliente -->
<div class="section">
    <div class="section-header">Dados da Obra e Cliente</div>
    <div class="section-body">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Obra</span>
                <span class="info-value"><?php echo htmlspecialchars($obra->nome ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Cliente</span>
                <span class="info-value"><?php echo htmlspecialchars($obra->cliente_nome ?? $cliente->nomeCliente ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Documento Cliente</span>
                <span class="info-value"><?php echo htmlspecialchars($cliente->documento ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Endereço da Obra</span>
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
        </div>
    </div>
</div>

<!-- Dados da Atividade -->
<div class="section">
    <div class="section-header">Dados da Atividade</div>
    <div class="section-body">
        <div class="info-grid">
            <div class="info-item full-width">
                <span class="info-label">Título</span>
                <span class="info-value"><?php echo htmlspecialchars($atividade->titulo ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <?php
                    $impedimento_print = ($atividade_real->impedimento ?? $atividade->impedimento ?? 0);
                    $status_print = $impedimento_print ? 'impedimento' : ($atividade->status ?? 'agendada');
                    ?>
                    <span class="status-badge <?php echo $status_print; ?>">
                        <?php
                        $statusLabelsPrint = [
                            'agendada' => 'Agendada',
                            'iniciada' => 'Em Execução',
                            'pausada' => 'Pausada',
                            'concluida' => 'Concluída',
                            'cancelada' => 'Cancelada',
                            'reaberta' => 'Reaberta',
                            'impedimento' => 'Impedido'
                        ];
                        echo $statusLabelsPrint[$status_print] ?? ucfirst($status_print);
                        ?>
                    </span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Data Programada</span>
                <span class="info-value"><?php echo !empty($atividade->data_atividade) ? date('d/m/Y', strtotime($atividade->data_atividade)) : 'N/A'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Etapa</span>
                <span class="info-value"><?php echo htmlspecialchars($atividade->etapa_nome ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Técnico Responsável</span>
                <span class="info-value"><?php echo htmlspecialchars($atividade->tecnico_nome ?? 'Não atribuído'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Tipo</span>
                <span class="info-value"><?php echo ucfirst($atividade->tipo ?? 'trabalho'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Hora Início</span>
                <span class="info-value" style="color: #27ae60;">
                    <?php echo !empty($atividade->hora_inicio) ? date('d/m/Y H:i', strtotime($atividade->hora_inicio)) : '--:--'; ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Hora Fim</span>
                <span class="info-value" style="color: #e74c3c;">
                    <?php echo !empty($atividade->hora_fim) ? date('d/m/Y H:i', strtotime($atividade->hora_fim)) : '--:--'; ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Horas Trabalhadas</span>
                <span class="info-value" style="color: #667eea; font-size: 16px;">
                    <?php
                    if (!empty($atividade->horas_trabalhadas)) {
                        $h = floor($atividade->horas_trabalhadas);
                        $m = round(($atividade->horas_trabalhadas - $h) * 60);
                        echo sprintf('%02dh %02dmin', $h, $m);
                    } else {
                        echo '--:--';
                    }
                    ?>
                </span>
            </div>
            <div class="info-item full-width">
                <span class="info-label">Progresso da Atividade</span>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $atividade->percentual_concluido ?? 0; ?>%">
                        <?php echo $atividade->percentual_concluido ?? 0; ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Anotações e Registros -->
<?php
$obs = $atividade->observacoes ?? null;
$prob = $atividade->problemas_encontrados ?? null;
$sol = $atividade->solucao_aplicada ?? null;
if (!empty($obs) || !empty($prob) || !empty($sol)):
?>
<div class="section">
    <div class="section-header">Anotações e Registros do Atendimento</div>
    <div class="section-body">
        <?php if (!empty($obs)): ?>
        <div class="anotacao-box observacao no-break">
            <div class="anotacao-label">Observações Gerais</div>
            <div class="anotacao-text"><?php echo nl2br(htmlspecialchars($obs)); ?></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($prob)): ?>
        <div class="anotacao-box problema no-break">
            <div class="anotacao-label">Problemas Encontrados</div>
            <div class="anotacao-text"><?php echo nl2br(htmlspecialchars($prob)); ?></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($sol)): ?>
        <div class="anotacao-box solucao no-break">
            <div class="anotacao-label">Solução Aplicada</div>
            <div class="anotacao-text"><?php echo nl2br(htmlspecialchars($sol)); ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Execuções / Reatendimentos -->
<?php if (!empty($historico_execucoes) && count($historico_execucoes) > 0): ?>
<div class="section">
    <div class="section-header">Histórico de Execuções</div>
    <div class="section-body">
        <?php foreach ($historico_execucoes as $index => $exec): ?>
        <div class="execucao-item <?php echo ($exec->reatendimento ?? false) ? 'reatendimento' : ''; ?> no-break">
            <div class="execucao-header">
                <span class="execucao-tag <?php echo ($exec->reatendimento ?? false) ? 'reatendimento' : 'original'; ?>">
                    <?php echo ($exec->reatendimento ?? false) ? 'Reatendimento' : 'Execução Original'; ?>
                </span>
                <span style="font-size: 11px; color: #888;">ID: #<?php echo $exec->idAtividade ?? ''; ?></span>
            </div>
            <div class="execucao-grid">
                <div class="info-item">
                    <span class="info-label">Técnico</span>
                    <span class="info-value"><?php echo htmlspecialchars($exec->nome_tecnico ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Início</span>
                    <span class="info-value"><?php echo !empty($exec->hora_inicio) ? date('d/m/Y H:i', strtotime($exec->hora_inicio)) : '--:--'; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fim</span>
                    <span class="info-value"><?php echo !empty($exec->hora_fim) ? date('d/m/Y H:i', strtotime($exec->hora_fim)) : '--:--'; ?></span>
                </div>
                <?php if (!empty($exec->duracao_minutos)): ?>
                <div class="info-item">
                    <span class="info-label">Duração</span>
                    <span class="info-value">
                        <?php
                        $h = floor($exec->duracao_minutos / 60);
                        $m = $exec->duracao_minutos % 60;
                        echo sprintf('%02dh %02dmin', $h, $m);
                        ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($exec->motivo_reabertura)): ?>
            <div style="margin-top: 8px; padding: 8px; background: #fff3e0; border-radius: 4px; font-size: 12px;">
                <strong>Motivo da Reabertura:</strong> <?php echo htmlspecialchars($exec->motivo_reabertura); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Fotos -->
<?php if (!empty($fotos_atividade) && count($fotos_atividade) > 0): ?>
<div class="section">
    <div class="section-header">Fotos do Atendimento (<?php echo count($fotos_atividade); ?>)</div>
    <div class="section-body">
        <div class="fotos-grid">
            <?php foreach ($fotos_atividade as $foto):
                $url_foto = '';
                if (!empty($foto->caminho_arquivo)) {
                    if (strpos($foto->caminho_arquivo, 'http') === 0) {
                        $url_foto = $foto->caminho_arquivo;
                    } elseif (strpos($foto->caminho_arquivo, 'assets/') === 0) {
                        $url_foto = base_url($foto->caminho_arquivo);
                    } else {
                        $url_foto = base_url('assets/atividades/fotos/' . $foto->caminho_arquivo);
                    }
                } elseif (!empty($foto->foto_base64)) {
                    $url_foto = $foto->foto_base64;
                }
                $tipo_foto = $foto->tipo_foto ?? 'execucao';
                $tipo_text = ['checkin'=>'Check-in', 'checkout'=>'Finalização', 'execucao'=>'Execução', 'impedimento'=>'Impedimento'][$tipo_foto] ?? 'Execução';
            ?>
            <?php if ($url_foto): ?>
            <div class="foto-item no-break">
                <img src="<?php echo $url_foto; ?>" alt="Foto">
                <div class="foto-badge <?php echo $tipo_foto; ?>"><?php echo $tipo_text; ?></div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Check-ins -->
<?php if (!empty($checkins) && count($checkins) > 0): ?>
<div class="section">
    <div class="section-header">Registros de Localização</div>
    <div class="section-body">
        <?php foreach ($checkins as $ck): ?>
        <div class="checkin-item no-break">
            <div>
                <span class="checkin-tipo <?php echo ($ck->tipo ?? 'entrada') === 'saida' ? 'saida' : 'entrada'; ?>">
                    <?php echo ($ck->tipo ?? 'entrada') === 'saida' ? 'Saída' : 'Entrada'; ?>
                </span>
                <span style="margin-left: 10px; font-size: 12px; color: #666;">
                    <?php echo !empty($ck->created_at) ? date('d/m/Y H:i', strtotime($ck->created_at)) : ''; ?>
                </span>
            </div>
            <?php if (!empty($ck->latitude) && !empty($ck->longitude)): ?>
            <span style="font-size: 11px; color: #888;">
                Lat: <?php echo $ck->latitude; ?> | Lng: <?php echo $ck->longitude; ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Histórico de Alterações -->
<?php if (!empty($historico) && count($historico) > 0): ?>
<div class="section">
    <div class="section-header">Histórico de Alterações</div>
    <div class="section-body">
        <?php foreach ($historico as $hist): ?>
        <div class="historico-item">
            <span class="historico-desc"><?php echo htmlspecialchars($hist->descricao ?? ''); ?></span>
            <span class="historico-data"><?php echo !empty($hist->created_at) ? date('d/m/Y H:i', strtotime($hist->created_at)) : ''; ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Assinaturas -->
<div class="assinaturas no-break">
    <div class="assinatura-box">
        <div class="assinatura-linha">
            <?php echo htmlspecialchars($emitente->nome ?? $emitente->emitente ?? 'Empresa'); ?>
            <div class="cargo">Responsável Técnico / Empresa</div>
        </div>
    </div>
    <div class="assinatura-box">
        <div class="assinatura-linha">
            <?php echo htmlspecialchars($obra->cliente_nome ?? $cliente->nomeCliente ?? 'Cliente'); ?>
            <div class="cargo">Cliente / Responsável</div>
        </div>
    </div>
</div>

<!-- Rodapé -->
<div class="rodape no-break">
    <p><strong><?php echo htmlspecialchars($emitente->nome ?? $emitente->emitente ?? ''); ?></strong></p>
    <?php if (!empty($emitente->telefone)): ?><p>Tel: <?php echo $emitente->telefone; ?></p><?php endif; ?>
    <p>Relatório gerado em <?php echo date('d/m/Y \à\s H:i'); ?> pelo sistema Map-OS</p>
    <p style="margin-top: 5px; font-size: 10px;">Este documento é um relatório gerado automaticamente pelo sistema e não possui valor fiscal.</p>
</div>

<script>
    // Auto-print em 500ms após carregar (opcional)
    // setTimeout(function() { window.print(); }, 500);
</script>

</body>
</html>
