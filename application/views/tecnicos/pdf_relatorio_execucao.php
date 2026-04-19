<?php
/**
 * Template PDF para Relatório de Execução
 * Este arquivo é usado para gerar o PDF via mPDF
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Execução - OS #<?php echo $os->idOs; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
            color: #667eea;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 10pt;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #667eea;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 10px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            color: #666;
            width: 30%;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 9pt;
            font-weight: bold;
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }
        .status-executado {
            background: #d4edda;
            color: #155724;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }
        table.data-table th {
            background: #f5f5f5;
            font-weight: bold;
        }
        table.data-table tr:nth-child(even) {
            background: #fafafa;
        }
        .timeline-item {
            margin-bottom: 15px;
            padding: 10px;
            border-left: 3px solid #667eea;
            background: #f9f9f9;
        }
        .timeline-date {
            font-size: 9pt;
            color: #888;
        }
        .timeline-title {
            font-weight: bold;
            margin: 5px 0;
        }
        .timeline-details {
            font-size: 10pt;
            color: #666;
        }
        .foto-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .foto-item {
            width: 140px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        .foto-item img {
            max-width: 100%;
            height: 100px;
            object-fit: cover;
        }
        .foto-desc {
            font-size: 8pt;
            color: #666;
            margin-top: 5px;
        }
        .assinatura-box {
            text-align: center;
            margin-top: 10px;
            padding: 15px;
            border: 1px dashed #ccc;
            border-radius: 5px;
        }
        .assinatura-box img {
            max-width: 200px;
            max-height: 80px;
        }
        .assinatura-nome {
            font-size: 10pt;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #999;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .empty-msg {
            color: #999;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>

    <!-- Cabeçalho -->
    <div class="header">
        <h1>Relatório de Execução de Serviço</h1>
        <p>Ordem de Serviço #<?php echo sprintf('%04d', $os->idOs); ?> | Emitido em <?php echo date('d/m/Y \à\s H:i'); ?></p>
        <?php if (!empty($emitente)): ?>
            <p><strong><?php echo $emitente->nome ?? 'Empresa'; ?></strong></p>
        <?php endif; ?>
    </div>

    <!-- Informações da OS -->
    <div class="section">
        <div class="section-title">Informações da OS</div>
        <table class="info-table">
            <tr>
                <td class="label">OS Número:</td>
                <td>#<?php echo sprintf('%04d', $os->idOs); ?></td>
                <td class="label">Data de Entrada:</td>
                <td><?php echo date('d/m/Y', strtotime($os->dataInicial)); ?></td>
            </tr>
            <tr>
                <td class="label">Status:</td>
                <td><span class="status-badge"><?php echo $os->status; ?></span></td>
                <td class="label">Data Prevista:</td>
                <td><?php echo !empty($os->dataFinal) ? date('d/m/Y', strtotime($os->dataFinal)) : 'Não definida'; ?></td>
            </tr>
            <?php if ($os->descricaoProduto): ?>
            <tr>
                <td class="label">Descrição:</td>
                <td colspan="3"><?php echo strip_tags($os->descricaoProduto); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Informações do Cliente -->
    <div class="section">
        <div class="section-title">Informações do Cliente</div>
        <table class="info-table">
            <tr>
                <td class="label">Nome:</td>
                <td><?php echo htmlspecialchars($cliente->nomeCliente ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
            </tr>
            <?php if (!empty($cliente->endereco)): ?>
            <tr>
                <td class="label">Endereço:</td>
                <td>
                    <?php
                    $endereco = [];
                    if (!empty($cliente->endereco)) $endereco[] = $cliente->endereco;
                    if (!empty($cliente->numero)) $endereco[] = $cliente->numero;
                    if (!empty($cliente->bairro)) $endereco[] = $cliente->bairro;
                    if (!empty($cliente->cidade)) $endereco[] = $cliente->cidade;
                    if (!empty($cliente->estado)) $endereco[] = $cliente->estado;
                    if (!empty($cliente->cep)) $endereco[] = 'CEP: ' . $cliente->cep;
                    echo implode(', ', $endereco);
                    ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($cliente->telefone) || !empty($cliente->celular)): ?>
            <tr>
                <td class="label">Telefone:</td>
                <td>
                    <?php
                    $telefones = [];
                    if (!empty($cliente->telefone)) $telefones[] = $cliente->telefone;
                    if (!empty($cliente->celular)) $telefones[] = $cliente->celular;
                    echo implode(' / ', $telefones);
                    ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($cliente->email)): ?>
            <tr>
                <td class="label">E-mail:</td>
                <td><?php echo $cliente->email; ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Produtos -->
    <?php if (!empty($produtos)): ?>
    <div class="section">
        <div class="section-title">Produtos Utilizados</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produto->descricao ?? $produto->nome ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                    <td><?php echo $produto->quantidade ?? 1; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Serviços -->
    <?php if (!empty($servicos)): ?>
    <div class="section">
        <div class="section-title">Serviços Executados</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th style="width: 120px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicos as $servico):
                    $status = $servico->status ?? 'Pendente';
                    $statusClass = strtolower($status) === 'executado' ? 'status-executado' : 'status-pendente';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($servico->nome ?? $servico->descricao ?? 'Serviço', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Timeline de Execuções -->
    <?php if (!empty($execucoes)): ?>
    <div class="section">
        <div class="section-title">Histórico de Execução</div>
        <?php foreach ($execucoes as $execucao): ?>
        <div class="timeline-item">
            <div class="timeline-date">
                <strong>Check-in:</strong> <?php echo date('d/m/Y H:i', strtotime($execucao->checkin_horario)); ?>
                <?php if ($execucao->checkout_horario): ?>
                    | <strong>Check-out:</strong> <?php echo date('d/m/Y H:i', strtotime($execucao->checkout_horario)); ?>
                <?php endif; ?>
            </div>
            <?php if ($execucao->tempo_atendimento_minutos): ?>
            <div class="timeline-details">
                <strong>Tempo de Atendimento:</strong> <?php echo floor($execucao->tempo_atendimento_minutos / 60) . 'h ' . ($execucao->tempo_atendimento_minutos % 60) . 'min'; ?>
            </div>
            <?php endif; ?>
            <?php if ($execucao->checklist_json):
                $checklist = json_decode($execucao->checklist_json, true);
                if (!empty($checklist['observacoes'])): ?>
            <div class="timeline-details">
                <strong>Observações:</strong> <?php echo nl2br(htmlspecialchars($checklist['observacoes'])); ?>
            </div>
            <?php endif; endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Check-ins do Sistema -->
    <?php if (!empty($checkins)): ?>
    <div class="section">
        <div class="section-title">Registros de Check-in</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data Entrada</th>
                    <th>Data Saída</th>
                    <th>Status</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checkins as $checkin): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($checkin->data_entrada)); ?></td>
                    <td><?php echo $checkin->data_saida ? date('d/m/Y H:i', strtotime($checkin->data_saida)) : '---'; ?></td>
                    <td><span class="status-badge"><?php echo $checkin->status ?? 'Em Andamento'; ?></span></td>
                    <td><?php echo htmlspecialchars($checkin->observacao_saida ?? $checkin->observacao_entrada ?? '-', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Fotos do Atendimento -->
    <?php if (!empty($fotosPorEtapa['entrada']) || !empty($fotosPorEtapa['durante']) || !empty($fotosPorEtapa['saida'])): ?>
    <div class="section">
        <div class="section-title">Fotos do Atendimento</div>

        <?php if (!empty($fotosPorEtapa['entrada'])): ?>
        <p><strong>Fotos de Entrada:</strong></p>
        <div class="foto-grid">
            <?php foreach ($fotosPorEtapa['entrada'] as $foto): ?>
            <div class="foto-item">
                <?php if (!empty($foto->imagem_base64)): ?>
                    <img src="data:image/jpeg;base64,<?php echo $foto->imagem_base64; ?>" alt="Foto entrada">
                <?php else: ?>
                    <img src="<?php echo $foto->path ?? $foto->url ?? ''; ?>" alt="Foto entrada">
                <?php endif; ?>
                <div class="foto-desc"><?php echo htmlspecialchars($foto->descricao ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($fotosPorEtapa['durante'])): ?>
        <p style="margin-top: 15px;"><strong>Fotos Durante o Serviço:</strong></p>
        <div class="foto-grid">
            <?php foreach ($fotosPorEtapa['durante'] as $foto): ?>
            <div class="foto-item">
                <?php if (!empty($foto->imagem_base64)): ?>
                    <img src="data:image/jpeg;base64,<?php echo $foto->imagem_base64; ?>" alt="Foto durante">
                <?php else: ?>
                    <img src="<?php echo $foto->path ?? $foto->url ?? ''; ?>" alt="Foto durante">
                <?php endif; ?>
                <div class="foto-desc"><?php echo htmlspecialchars($foto->descricao ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($fotosPorEtapa['saida'])): ?>
        <p style="margin-top: 15px;"><strong>Fotos de Saída:</strong></p>
        <div class="foto-grid">
            <?php foreach ($fotosPorEtapa['saida'] as $foto): ?>
            <div class="foto-item">
                <?php if (!empty($foto->imagem_base64)): ?>
                    <img src="data:image/jpeg;base64,<?php echo $foto->imagem_base64; ?>" alt="Foto saida">
                <?php else: ?>
                    <img src="<?php echo $foto->path ?? $foto->url ?? ''; ?>" alt="Foto saida">
                <?php endif; ?>
                <div class="foto-desc"><?php echo htmlspecialchars($foto->descricao ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Fotos do Portal do Técnico -->
    <?php if (!empty($fotosTecnico)): ?>
    <div class="section">
        <div class="section-title">Fotos do Técnico</div>
        <div class="foto-grid">
            <?php foreach ($fotosTecnico as $foto): ?>
            <div class="foto-item">
                <img src="<?php echo base_url($foto->caminho ?? $foto->foto ?? ''); ?>" alt="Foto técnico">
                <div class="foto-desc"><?php echo htmlspecialchars($foto->descricao ?? $foto->tipo ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Assinaturas -->
    <?php if (!empty($assinaturasPorTipo)): ?>
    <div class="section">
        <div class="section-title">Assinaturas</div>
        <table class="info-table">
            <?php if (isset($assinaturasPorTipo['tecnico_entrada'])): ?>
            <tr>
                <td class="label">Assinatura Técnico (Entrada):</td>
                <td>
                    <div class="assinatura-box">
                        <img src="<?php echo base_url($assinaturasPorTipo['tecnico_entrada']->assinatura ?? ''); ?>" alt="Assinatura técnico">
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            <?php if (isset($assinaturasPorTipo['cliente_saida'])): ?>
            <tr>
                <td class="label">Assinatura Cliente (Saída):</td>
                <td>
                    <div class="assinatura-box">
                        <img src="<?php echo base_url($assinaturasPorTipo['cliente_saida']->assinatura ?? ''); ?>" alt="Assinatura cliente">
                        <?php if (!empty($assinaturasPorTipo['cliente_saida']->nome_assinante)): ?>
                            <div class="assinatura-nome"><?php echo htmlspecialchars($assinaturasPorTipo['cliente_saida']->nome_assinante, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>

    <!-- Rodapé -->
    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema de Gestão de Técnicos - Map-OS</p>
        <p>Documento confidencial - Uso interno</p>
    </div>

</body>
</html>
