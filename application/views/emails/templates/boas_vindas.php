<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; }
        h2 { color: #333; }
        p { color: #555; line-height: 1.6; }
        .footer { margin-top: 30px; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bem-vindo ao Sistema MAPOS!</h2>
        <p>Ol<?php if (!empty($cliente_nome)): ?> <?= htmlspecialchars($cliente_nome) ?><?php else: ?> Usuario<?php endif; ?>,</p>

        <p>Este é um email de teste do sistema automatizado de notificações.</p>

        <p>Se você recebeu esta mensagem, significa que a configuração SMTP está funcionando corretamente.</p>

        <p><strong>Data do teste:</strong> <?= htmlspecialchars($data_atual ?? date('d/m/Y')) ?></p>
        <p><strong>URL do sistema:</strong> <?= htmlspecialchars($sistema_url ?? '') ?></p>

        <div class="footer">
            <p>Email enviado automaticamente pelo sistema MAPOS.</p>
        </div>
    </div>
</body>
</html>