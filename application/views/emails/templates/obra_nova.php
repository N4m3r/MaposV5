<?php
/**
 * Template: Obra Nova
 * Disparado quando uma obra é cadastrada
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{titulo}}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Olá {{cliente_nome}},</h2>
        <p>Uma nova obra foi cadastrada para você:</p>
        <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #3498db; margin: 15px 0;">
            <strong>Obra:</strong> {{os_titulo}}<br>
            <strong>Status:</strong> {{os_status}}<br>
            <strong>Data de Início:</strong> {{os_data_criacao}}
        </div>
        <p>{{os_descricao}}</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{os_link_visualizar}}" style="background: #3498db; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px;">Visualizar Obra</a>
        </p>
        <hr>
        <p style="font-size: 12px; color: #666;">
            {{empresa_nome}}<br>
            Tel: {{empresa_telefone}}<br>
            Este é um email automático. Por favor, não responda.
        </p>
    </div>
</body>
</html>