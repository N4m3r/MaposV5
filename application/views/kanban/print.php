<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Kanban</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background: #f5f5f5; }
        .status-aberto { color: #3b82f6; }
        .status-andamento { color: #f59e0b; }
        .status-finalizado { color: #10b981; }
        .status-cancelado { color: #ef4444; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <h2>Kanban - <?php echo htmlspecialchars($titulo ?? 'Quadro de Tarefas'); ?></h2>
    <p>Data: <?php echo date('d/m/Y H:i'); ?></p>

    <?php if (!empty($quadros)): ?>
    <?php foreach ($quadros as $status => $items): ?>
    <h3><?php echo htmlspecialchars($status); ?> (<?php echo count($items); ?>)</h3>
    <table>
        <thead>
            <tr>
                <th>OS</th>
                <th>Cliente</th>
                <th>Titulo</th>
                <th>Tecnico</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['idOs'] ?? $item['os_id'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($item['nomeCliente'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($item['titulo'] ?? $item['descricao'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($item['tecnico_nome'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($item['dataInicial'] ?? ''); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endforeach; ?>
    <?php else: ?>
    <p>Nenhum dado disponivel para impressao.</p>
    <?php endif; ?>

    <button class="no-print" onclick="window.print()" style="margin-top:20px;padding:10px 20px;cursor:pointer;">Imprimir</button>
</body>
</html>