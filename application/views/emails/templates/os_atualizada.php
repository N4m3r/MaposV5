<?php
/**
 * Template: OS Atualizada
 * Disparado quando uma OS é editada ou status alterado
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
        <p>Sua Ordem de Serviço foi atualizada:</p>
        <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0;">
            <strong>OS #{{os_id}}</strong><br>
            <strong>Título:</strong> {{os_titulo}}<br>
            <strong>Status:</strong> {{os_status}}<br>
            <strong>Data:</strong> {{os_data_criacao}}
        </div>
        <p>{{os_descricao}}</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{os_link_visualizar}}" style="background: #ffc107; color: #000; padding: 12px 30px; text-decoration: none; border-radius: 5px;">Visualizar OS</a>
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