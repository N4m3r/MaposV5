<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nova Ordem de Serviço</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4a90d9; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .footer { font-size: 12px; color: #666; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nova Ordem de Serviço</h1>
        </div>
        <div class="content">
            <p>Olá, <?= $cliente_nome ?? 'Cliente' ?>!</p>
            <p>Sua ordem de serviço <strong>#OS<?= $os_id ?? '' ?></strong> foi criada com sucesso.</p>
            
            <p><strong>Descrição:</strong> <?= $descricao ?? '' ?></p>
            <p><strong>Status:</strong> <?= $status ?? 'Pendente' ?></p>
            
            <p>Você pode acompanhar o status da sua OS através do nosso sistema.</p>
        </div>
        <div class="footer">
            <p>Este é um email automático. Por favor, não responda.</p>
            <p><?= date('Y') ?> - Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
