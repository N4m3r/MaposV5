<?php
/**
 * Template: Venda Realizada
 * Disparado quando uma venda é criada
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
        <p>Sua venda foi iniciada com sucesso!</p>
        <div style="background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0;">
            <strong>Venda #{{venda_id}}</strong><br>
            <strong>Data:</strong> {{venda_data}}<br>
            <strong>Status:</strong> {{venda_status}}<br>
            <strong>Valor Total:</strong> R$ {{venda_valor_total}}
        </div>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{venda_link_visualizar}}" style="background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px;">Visualizar Venda</a>
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