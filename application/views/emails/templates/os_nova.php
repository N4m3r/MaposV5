<?php
/**
 * Template: OS Nova
 * Disparado quando uma nova Ordem de Serviço é criada
 * Tags disponíveis: {{cliente_nome}}, {{os_id}}, {{os_titulo}}, {{os_descricao}}, {{os_status}}, {{os_data_criacao}}, {{os_valor_total}}, {{os_link_visualizar}}, {{empresa_nome}}, {{empresa_telefone}}, {{data_atual}}
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{titulo}}</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <table role="presentation" style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <tr>
            <td style="padding: 30px;">
                <!-- Header -->
                <table role="presentation" style="width: 100%; border-bottom: 3px solid #3498db; padding-bottom: 20px; margin-bottom: 20px;">
                    <tr>
                        <td>
                            <h1 style="color: #2c3e50; margin: 0; font-size: 24px;">{{empresa_nome}}</h1>
                        </td>
                    </tr>
                </table>

                <!-- Saudação -->
                <h2 style="color: #2c3e50; margin-top: 0;">Olá {{cliente_nome}},</h2>

                <p style="font-size: 16px; color: #555;">
                    Uma nova <strong>Ordem de Serviço</strong> foi criada para você:
                </p>

                <!-- Card da OS -->
                <table role="presentation" style="width: 100%; background-color: #f8f9fa; border-left: 4px solid #3498db; margin: 25px 0; padding: 20px; border-radius: 4px;">
                    <tr>
                        <td>
                            <p style="margin: 0 0 10px 0; font-size: 18px;">
                                <strong style="color: #2c3e50;">OS #{{os_id}}</strong>
                            </p>
                            <p style="margin: 5px 0;">
                                <strong>Título:</strong> {{os_titulo}}
                            </p>
                            <p style="margin: 5px 0;">
                                <strong>Status:</strong> <span style="background-color: #e3f2fd; color: #1976d2; padding: 3px 10px; border-radius: 12px; font-size: 12px;">{{os_status}}</span>
                            </p>
                            <p style="margin: 5px 0;">
                                <strong>Data:</strong> {{os_data_criacao}}
                            </p>
                            <p style="margin: 5px 0;">
                                <strong>Valor:</strong> R$ {{os_valor_total}}
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Descrição -->
                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; margin: 20px 0;">
                    <p style="margin: 0; color: #856404;">
                        <strong>Descrição:</strong><br>
                        {{os_descricao}}
                    </p>
                </div>

                <!-- CTA Button -->
                <table role="presentation" style="width: 100%; margin: 30px 0;">
                    <tr>
                        <td style="text-align: center;">
                            <a href="{{os_link_visualizar}}" style="display: inline-block; background-color: #3498db; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 5px; font-weight: bold; font-size: 16px;">
                                Visualizar OS
                            </a>
                        </td>
                    </tr>
                </table>

                <!-- Divider -->
                <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

                <!-- Footer -->
                <table role="presentation" style="width: 100%;">
                    <tr>
                        <td style="text-align: center; color: #666; font-size: 12px;">
                            <p style="margin: 0 0 10px 0;">
                                <strong>{{empresa_nome}}</strong><br>
                                Tel: {{empresa_telefone}}
                            </p>
                            <p style="margin: 0; color: #999;">
                                Este é um email automático. Por favor, não responda.<br>
                                © {{ano_atual}} - Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
