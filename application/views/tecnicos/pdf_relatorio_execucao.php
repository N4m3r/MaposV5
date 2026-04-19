<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório OS #<?php echo $os->idOs; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.4; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 16pt; color: #667eea; }
        .header p { margin: 3px 0 0 0; font-size: 9pt; color: #666; }
        .section { margin-bottom: 15px; border: 1px solid #ddd; padding: 10px; }
        .section-title { font-size: 11pt; font-weight: bold; color: #667eea; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 8px; vertical-align: top; font-size: 9pt; }
        .label { font-weight: bold; color: #666; width: 25%; }
        .status { display: inline-block; padding: 2px 8px; border-radius: 8px; font-size: 8pt; font-weight: bold; background: #e8f5e9; color: #2e7d32; }
        .status-pendente { background: #fff3cd; color: #856404; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 5px; text-align: left; font-size: 9pt; }
        .data-table th { background: #f5f5f5; font-weight: bold; }
        .timeline { margin-bottom: 10px; padding: 8px; border-left: 2px solid #667eea; background: #f9f9f9; }
        .timeline small { color: #888; }
        .footer { text-align: center; font-size: 8pt; color: #999; margin-top: 20px; padding-top: 8px; border-top: 1px solid #eee; }
        .fotos-info { font-size: 9pt; color: #666; font-style: italic; }
        .assinatura img { max-width: 150px; max-height: 60px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Relatório de Execução</h1>
    <p>OS #<?php echo sprintf('%04d', $os->idOs); ?> | <?php echo date('d/m/Y H:i'); ?></p>
    <?php if (!empty($emitente)) echo '<p><strong>' . ($emitente->nome ?? 'Empresa') . '</strong></p>'; ?>
</div>

<div class="section">
    <div class="section-title">Informações da OS</div>
    <table>
        <tr>
            <td class="label">OS:</td><td>#<?php echo sprintf('%04d', $os->idOs); ?></td>
            <td class="label">Entrada:</td><td><?php echo date('d/m/Y', strtotime($os->dataInicial)); ?></td>
        </tr>
        <tr>
            <td class="label">Status:</td><td><span class="status"><?php echo $os->status; ?></span></td>
            <td class="label">Previsto:</td><td><?php echo !empty($os->dataFinal) ? date('d/m/Y', strtotime($os->dataFinal)) : 'Não definida'; ?></td>
        </tr>
        <?php if ($os->descricaoProduto): ?>        <tr><td class="label">Descrição:</td><td colspan="3"><?php echo strip_tags($os->descricaoProduto); ?></td></tr>
        <?php endif; ?>
    </table>
</div>

<div class="section">
    <div class="section-title">Cliente</div>
    <table>
        <tr><td class="label">Nome:</td><td colspan="3"><?php echo htmlspecialchars($cliente->nomeCliente ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td></tr>
        <?php if (!empty($cliente->endereco)): ?>        <tr>
            <td class="label">Endereço:</td>
            <td colspan="3">
                <?php
                $end = array_filter([$cliente->endereco, $cliente->numero, $cliente->bairro, $cliente->cidade, $cliente->estado]);
                echo implode(', ', $end);
                ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($cliente->telefone) || !empty($cliente->celular)): ?>        <tr>
            <td class="label">Telefone:</td>
            <td colspan="3"><?php echo implode(' / ', array_filter([$cliente->telefone, $cliente->celular])); ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<?php if (!empty($produtos)): ?>
<div class="section">
    <div class="section-title">Produtos</div>
    <table class="data-table">
        <tr><th>Produto</th><th>Qtd</th></tr>
        <?php foreach ($produtos as $p): ?>        <tr>
            <td><?php echo htmlspecialchars($p->descricao ?? $p->nome ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
            <td><?php echo $p->quantidade ?? 1; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($servicos)): ?>
<div class="section">
    <div class="section-title">Serviços</div>
    <table class="data-table">
        <tr><th>Serviço</th><th>Status</th></tr>
        <?php foreach ($servicos as $s):
            $status = $s->status ?? 'Pendente';
            $class = strtolower($status) === 'executado' ? 'status' : 'status status-pendente';
        ?>        <tr>
            <td><?php echo htmlspecialchars($s->nome ?? $s->descricao ?? 'Serviço', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
            <td><span class="<?php echo $class; ?>"><?php echo $status; ?></span></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($execucoes)): ?>
<div class="section">
    <div class="section-title">Execução</div>
    <?php foreach ($execucoes as $e): ?>    <div class="timeline">
        <small>Check-in: <?php echo date('d/m/Y H:i', strtotime($e->checkin_horario)); ?>
        <?php if ($e->checkout_horario) echo ' | Check-out: ' . date('d/m/Y H:i', strtotime($e->checkout_horario)); ?></small><br>
        <?php if ($e->tempo_atendimento_minutos): ?>            <small>Tempo: <?php echo floor($e->tempo_atendimento_minutos / 60) . 'h ' . ($e->tempo_atendimento_minutos % 60) . 'min'; ?></small><br>
        <?php endif; ?>
        <?php if ($e->checklist_json):
            $chk = json_decode($e->checklist_json, true);
            if (!empty($chk['observacoes'])) echo '<small>Obs: ' . htmlspecialchars(substr($chk['observacoes'], 0, 200)) . '</small>';
        endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($checkins)): ?>
<div class="section">
    <div class="section-title">Check-ins</div>
    <table class="data-table">
        <tr><th>Entrada</th><th>Saída</th><th>Status</th></tr>
        <?php foreach ($checkins as $c): ?>        <tr>
            <td><?php echo date('d/m/Y H:i', strtotime($c->data_entrada)); ?></td>
            <td><?php echo $c->data_saida ? date('d/m/Y H:i', strtotime($c->data_saida)) : '-'; ?></td>
            <td><?php echo $c->status ?? 'Em Andamento'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<?php
$totalFotos = count($fotosPorEtapa['entrada'] ?? []) + count($fotosPorEtapa['durante'] ?? []) + count($fotosPorEtapa['saida'] ?? []) + count($fotosTecnico ?? []);
if ($totalFotos > 0):
?}
<div class="section">
    <div class="section-title">Registro Fotográfico</div>
    <p class="fotos-info">Total de fotos registradas: <?php echo $totalFotos; ?></p>
    <p class="fotos-info">As fotos estão disponíveis no sistema para visualização completa.</p>
</div>
<?php endif; ?>

<?php if (!empty($assinaturasPorTipo)): ?>
<div class="section">
    <div class="section-title">Assinaturas</div>
    <table>
        <?php if (isset($assinaturasPorTipo['cliente_saida'])): ?>        <tr>
            <td class="label">Cliente:</td>
            <td class="assinatura">
                <img src="<?php echo base_url($assinaturasPorTipo['cliente_saida']->assinatura ?? ''); ?>" alt=""><br>
                <?php if (!empty($assinaturasPorTipo['cliente_saida']->nome_assinante')) echo '<small>' . htmlspecialchars($assinaturasPorTipo['cliente_saida']->nome_assinante) . '</small>'; ?>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<?php endif; ?>

<div class="footer">
    <p>Sistema Map-OS - Documento confidencial</p>
</div>

</body>
</html>
