<?php
/**
 * Documentação: Webhooks do MAPOS
 * Sistema de notificações em tempo real
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação Webhooks - MAPOS</title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background: #f5f5f5;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .docs-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 40px;
        }
        .docs-header {
            border-bottom: 2px solid #2d335b;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .docs-header h1 {
            color: #2d335b;
            margin: 0;
            font-size: 32px;
        }
        .docs-header p {
            color: #666;
            margin-top: 10px;
            font-size: 16px;
        }
        .section {
            margin-bottom: 40px;
        }
        .section h2 {
            color: #2d335b;
            font-size: 24px;
            margin-bottom: 15px;
            border-left: 4px solid #2d335b;
            padding-left: 15px;
        }
        .section h3 {
            color: #444;
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 14px;
            overflow-x: auto;
            margin: 15px 0;
        }
        .code-block .key {
            color: #9cdcfe;
        }
        .code-block .string {
            color: #ce9178;
        }
        .code-block .number {
            color: #b5cea8;
        }
        .code-block .boolean {
            color: #569cd6;
        }
        .event-card {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .event-card h4 {
            color: #2d335b;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .event-card .event-name {
            background: #2d335b;
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 13px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .event-card p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .event-card ul {
            margin: 10px 0 0 0;
            padding-left: 20px;
        }
        .event-card li {
            color: #555;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .alert-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .alert-box i {
            color: #2196f3;
            margin-right: 10px;
        }
        .method-post {
            background: #4caf50;
            color: #fff;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 10px;
        }
        .table-docs {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .table-docs th {
            background: #2d335b;
            color: #fff;
            padding: 12px;
            text-align: left;
            font-weight: 500;
        }
        .table-docs td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        .table-docs tr:hover td {
            background: #f5f5f5;
        }
        .badge-required {
            background: #f44336;
            color: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        .badge-optional {
            background: #9e9e9e;
            color: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2d335b;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="docs-container">
        <a href="<?php echo site_url('dashboard'); ?>" class="back-link">
            <i class='bx bx-arrow-left'></i> Voltar para o Dashboard
        </a>

        <div class="docs-header">
            <h1><i class='bx bx-webhook'></i> Webhooks</h1>
            <p>Documentação completa do sistema de notificações em tempo real do MAPOS</p>
        </div>

        <div class="section">
            <h2>O que são Webhooks?</h2>
            <p>Webhooks são notificações em tempo real enviadas para URLs externas quando eventos específicos ocorrem no sistema. Eles permitem que sistemas externos sejam notificados automaticamente sobre mudanças, eliminando a necessidade de consultas constantes (polling) à API.</p>

            <div class="alert-box">
                <i class='bx bx-info-circle'></i>
                <strong>Como funciona:</strong> Quando um evento ocorre no MAPOS (ex: uma OS é criada), o sistema envia uma requisição HTTP POST para a URL configurada com os dados do evento em formato JSON.
            </div>
        </div>

        <div class="section">
            <h2>Eventos Disponíveis</h2>

            <h3>Ordens de Serviço (OS)</h3>
            <div class="event-card">
                <span class="event-name">os.criada</span>
                <h4>OS Criada</h4>
                <p>Disparado quando uma nova ordem de serviço é cadastrada no sistema.</p>
                <ul>
                    <li>Dados incluídos: ID da OS, cliente, descrição, status, data de criação</li>
                </ul>
            </div>

            <div class="event-card">
                <span class="event-name">os.atualizada</span>
                <h4>OS Atualizada</h4>
                <p>Disparado quando uma ordem de serviço é modificada.</p>
                <ul>
                    <li>Dados incluídos: ID da OS, campos alterados, valores anteriores e novos</li>
                </ul>
            </div>

            <div class="event-card">
                <span class="event-name">os.finalizada</span>
                <h4>OS Finalizada</h4>
                <p>Disparado quando o status de uma OS é alterado para "Finalizado".</p>
                <ul>
                    <li>Dados incluídos: ID da OS, data de finalização, técnico responsável, laudo técnico</li>
                </ul>
            </div>

            <div class="event-card">
                <span class="event-name">os.aprovada</span>
                <h4>OS Aprovada</h4>
                <p>Disparado quando uma OS é aprovada pelo cliente.</p>
                <ul>
                    <li>Dados incluídos: ID da OS, data da aprovação, cliente</li>
                </ul>
            </div>

            <h3>Clientes</h3>
            <div class="event-card">
                <span class="event-name">cliente.criado</span>
                <h4>Cliente Criado</h4>
                <p>Disparado quando um novo cliente é cadastrado.</p>
                <ul>
                    <li>Dados incluídos: ID do cliente, nome, documento, contato, endereço</li>
                </ul>
            </div>

            <div class="event-card">
                <span class="event-name">cliente.atualizado</span>
                <h4>Cliente Atualizado</h4>
                <p>Disparado quando os dados de um cliente são modificados.</p>
                <ul>
                    <li>Dados incluídos: ID do cliente, campos alterados</li>
                </ul>
            </div>

            <h3>Vendas</h3>
            <div class="event-card">
                <span class="event-name">venda.criada</span>
                <h4>Venda Criada</h4>
                <p>Disparado quando uma nova venda é registrada.</p>
                <ul>
                    <li>Dados incluídos: ID da venda, cliente, produtos, valor total</li>
                </ul>
            </div>

            <div class="event-card">
                <span class="event-name">venda.paga</span>
                <h4>Venda Paga</h4>
                <p>Disparado quando uma venda é marcada como paga.</p>
                <ul>
                    <li>Dados incluídos: ID da venda, data do pagamento, valor, forma de pagamento</li>
                </ul>
            </div>

            <h3>Cobranças</h3>
            <div class="event-card">
                <span class="event-name">cobranca.criada</span>
                <h4>Cobrança Criada</h4>
                <p>Disparado quando uma cobrança/boleto é gerada.</p>
                <ul>
                    <li>Dados incluídos: ID da cobrança, valor, vencimento, link de pagamento</li>
                </ul>
            </div>

            <div class="event-card">
                <span class="event-name">cobranca.paga</span>
                <h4>Cobrança Paga</h4>
                <p>Disparado quando uma cobrança é confirmada como paga.</p>
                <ul>
                    <li>Dados incluídos: ID da cobrança, data do pagamento, valor pago, gateway</li>
                </ul>
            </div>

            <div class="event-card">
                <span class="event-name">cobranca.vencida</span>
                <h4>Cobrança Vencida</h4>
                <p>Disparado quando uma cobrança atinge a data de vencimento sem pagamento.</p>
                <ul>
                    <li>Dados incluídos: ID da cobrança, data de vencimento, valor</li>
                </ul>
            </div>

            <h3>Estoque</h3>
            <div class="event-card">
                <span class="event-name">produto.estoque_baixo</span>
                <h4>Produto com Estoque Baixo</h4>
                <p>Disparado quando a quantidade de um produto fica abaixo do mínimo configurado.</p>
                <ul>
                    <li>Dados incluídos: ID do produto, descrição, estoque atual, estoque mínimo</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2>Payload dos Eventos</h2>
            <p>Todos os webhooks são enviados via <span class="method-post">POST</span> com o seguinte formato JSON:</p>

            <h3>Exemplo: OS Criada</h3>
            <div class="code-block">
{
    <span class="key">"evento"</span>: <span class="string">"os.criada"</span>,
    <span class="key">"timestamp"</span>: <span class="string">"2026-04-12T14:30:00Z"</span>,
    <span class="key">"dados"</span>: {
        <span class="key">"id"</span>: <span class="number">1234</span>,
        <span class="key">"numero_os"</span>: <span class="string">"01234"</span>,
        <span class="key">"cliente"</span>: {
            <span class="key">"id"</span>: <span class="number">567</span>,
            <span class="key">"nome"</span>: <span class="string">"Empresa ABC Ltda"</span>,
            <span class="key">"documento"</span>: <span class="string">"12.345.678/0001-90"</span>,
            <span class="key">"email"</span>: <span class="string">"contato@empresaabc.com"</span>,
            <span class="key">"telefone"</span>: <span class="string">"(11) 98765-4321"</span>
        },
        <span class="key">"descricao"</span>: <span class="string">"Manutenção preventiva servidor"</span>,
        <span class="key">"status"</span>: <span class="string">"Aberto"</span>,
        <span class="key">"data_criacao"</span>: <span class="string">"2026-04-12T14:30:00Z"</span>,
        <span class="key">"valor_total"</span>: <span class="number">1500.00</span>,
        <span class="key">"tecnico_responsavel"</span>: <span class="string">"João Silva"</span>
    }
}
            </div>

            <h3>Exemplo: Cobrança Paga</h3>
            <div class="code-block">
{
    <span class="key">"evento"</span>: <span class="string">"cobranca.paga"</span>,
    <span class="key">"timestamp"</span>: <span class="string">"2026-04-12T10:15:00Z"</span>,
    <span class="key">"dados"</span>: {
        <span class="key">"id"</span>: <span class="number">9876</span>,
        <span class="key">"charge_id"</span>: <span class="string">"pay_123456789"</span>,
        <span class="key">"os_id"</span>: <span class="number">1234</span>,
        <span class="key">"cliente"</span>: {
            <span class="key">"id"</span>: <span class="number">567</span>,
            <span class="key">"nome"</span>: <span class="string">"Empresa ABC Ltda"</span>
        },
        <span class="key">"valor"</span>: <span class="number">1500.00</span>,
        <span class="key">"valor_pago"</span>: <span class="number">1500.00</span>,
        <span class="key">"data_pagamento"</span>: <span class="string">"2026-04-12T10:15:00Z"</span>,
        <span class="key">"gateway"</span>: <span class="string">"Gerencianet"</span>,
        <span class="key">"forma_pagamento"</span>: <span class="string">"boleto"</span>
    }
}
            </div>
        </div>

        <div class="section">
            <h2>Configuração de Webhooks</h2>
            <p>Para configurar um webhook, acesse <strong>Ferramentas V5 → Webhooks</strong> no menu principal do MAPOS.</p>

            <h3>Campos de Configuração</h3>
            <table class="table-docs">
                <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nome <span class="badge-required">Obrigatório</span></td>
                        <td>Texto</td>
                        <td>Identificação do webhook (ex: "Notificação Slack")</td>
                    </tr>
                    <tr>
                        <td>URL <span class="badge-required">Obrigatório</span></td>
                        <td>URL</td>
                        <td>Endereço HTTPS que receberá as notificações</td>
                    </tr>
                    <tr>
                        <td>Eventos <span class="badge-required">Obrigatório</span></td>
                        <td>Lista</td>
                        <td>Selecione quais eventos dispararão este webhook</td>
                    </tr>
                    <tr>
                        <td>Secret <span class="badge-optional">Opcional</span></td>
                        <td>Texto</td>
                        <td>Chave para assinar o payload (HMAC-SHA256)</td>
                    </tr>
                    <tr>
                        <td>Ativo</td>
                        <td>Booleano</td>
                        <td>Habilita/desabilita o envio de notificações</td>
                    </tr>
                    <tr>
                        <td>Retry</td>
                        <td>Número</td>
                        <td>Tentativas de reenvio em caso de falha (padrão: 3)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Segurança</h2>

            <h3>Assinatura do Payload</h3>
            <p>Se configurado um <strong>Secret</strong>, o MAPOS enviará um header adicional com a assinatura do payload:</p>

            <div class="code-block">
X-Webhook-Signature: sha256=abc123def456...
            </div>

            <p>Para verificar a autenticidade do webhook no seu servidor:</p>

            <div class="code-block">
<span class="key">// PHP</span>
$secret = <span class="string">'sua_chave_secreta'</span>;
$payload = file_get_contents(<span class="string">'php://input'</span>);
$signature = hash_hmac(<span class="string">'sha256'</span>, $payload, $secret);

<span class="key">// Comparar com o header recebido</span>
$receivedSignature = $_SERVER[<span class="string">'HTTP_X_WEBHOOK_SIGNATURE'</span>];
<span class="key">if</span> (<span class="string">'sha256='</span> . $signature === $receivedSignature) {
    <span class="key">// Webhook válido</span>
} <span class="key">else</span> {
    <span class="key">// Webhook inválido - rejeitar</span>
}
            </div>

            <h3>IP Permitidos</h3>
            <p>As requisições de webhook do MAPOS são enviadas a partir dos seguintes IPs:</p>
            <ul>
                <li>Seu IP do servidor MAPOS</li>
            </ul>
            <p>Recomendamos filtrar por estes IPs no firewall do seu servidor de destino.</p>
        </div>

        <div class="section">
            <h2>Respostas Esperadas</h2>
            <p>O seu endpoint deve responder com os seguintes códigos HTTP:</p>

            <table class="table-docs">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Significado</th>
                        <th>Ação do MAPOS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>200 OK</strong></td>
                        <td>Webhook recebido e processado com sucesso</td>
                        <td>Marcar como entregue</td>
                    </tr>
                    <tr>
                        <td><strong>201 Created</strong></td>
                        <td>Recurso criado a partir do webhook</td>
                        <td>Marcar como entregue</td>
                    </tr>
                    <tr>
                        <td><strong>204 No Content</strong></td>
                        <td>Sucesso sem conteúdo de resposta</td>
                        <td>Marcar como entregue</td>
                    </tr>
                    <tr>
                        <td><strong>400 Bad Request</strong></td>
                        <td>Dados inválidos recebidos</td>
                        <td>Registrar erro, tentar novamente</td>
                    </tr>
                    <tr>
                        <td><strong>500+</strong></td>
                        <td>Erro interno no servidor</td>
                        <td>Agendar retry</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Retry e Fallback</h2>
            <p>Se o seu servidor não responder com sucesso (códigos 2xx), o MAPOS automaticamente:</p>
            <ul>
                <li>Tenta reenviar após <strong>5 minutos</strong></li>
                <li>Segunda tentativa após <strong>15 minutos</strong></li>
                <li>Terceira tentativa após <strong>1 hora</strong></li>
            </ul>
            <p>Após 3 tentativas sem sucesso, o webhook será marcado como <strong>falho</strong> e notificará os administradores.</p>
        </div>

        <div class="section">
            <h2>Exemplos de Integração</h2>

            <h3>Receber no Slack</h3>
            <div class="code-block">
<span class="key">// Endpoint PHP para Slack</span>
$payload = json_decode(file_get_contents(<span class="string">'php://input'</span>), <span class="boolean">true</span>);

<span class="key">if</span> ($payload[<span class="string">'evento'</span>] === <span class="string">'os.criada'</span>) {
    $os = $payload[<span class="string">'dados'</span>];
    $mensagem = <span class="string">"Nova OS criada: #{$os['numero_os']} - {$os['descricao']}"</span>;

    <span class="key">// Enviar para Slack</span>
    $slackWebhook = <span class="string">'https://hooks.slack.com/services/...'</span>;
    $data = [<span class="key">'text'</span> => $mensagem];

    $ch = curl_init($slackWebhook);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, <span class="boolean">true</span>);
    curl_exec($ch);
    curl_close($ch);
}
            </div>

            <h3>Receber em Sistema Externo</h3>
            <div class="code-block">
<span class="key">// Node.js/Express</span>
app.post(<span class="string">'/webhook/mapos'</span>, (req, res) => {
    <span class="key">const</span> { evento, dados } = req.body;

    <span class="key">switch</span> (evento) {
        <span class="key">case</span> <span class="string">'os.finalizada'</span>:
            <span class="key">// Atualizar CRM</span>
            await crm.updateTicket(dados.id, { status: <span class="string">'completed'</span> });
            <span class="key">break</span>;

        <span class="key">case</span> <span class="string">'cobranca.paga'</span>:
            <span class="key">// Liberar acesso/gerar NF</span>
            await financeiro.confirmPayment(dados.id);
            <span class="key">break</span>;
    }

    res.status(<span class="number">200</span>).send(<span class="string">'OK'</span>);
});
            </div>
        </div>

        <div class="section">
            <h2>Limites e Boas Práticas</h2>
            <ul>
                <li><strong>Timeout:</strong> O MAPOS aguarda até 30 segundos por resposta</li>
                <li><strong>Payload máximo:</strong> 1MB por requisição</li>
                <li><strong>Rate limit:</strong> Máximo de 100 webhooks/minuto por URL</li>
                <li>Seu endpoint deve responder rapidamente (processamento assíncrono recomendado)</li>
                <li>Sempre retorne HTTP 200 assim que receber os dados</li>
                <li>Armazene o payload e processe em fila/fila de background</li>
                <li>Implemente idempotência (evite processar o mesmo evento 2x)</li>
            </ul>
        </div>

        <div class="section">
            <h2>Suporte</h2>
            <p>Em caso de dúvidas ou problemas com webhooks:</p>
            <ul>
                <li>Verifique os logs em <strong>Ferramentas V5 → Webhooks → Logs</strong></li>
                <li>Teste seu endpoint usando a função "Testar Webhook"</li>
                <li>Verifique se seu servidor aceita requisições POST do IP do MAPOS</li>
                <li>Confira se o SSL/TLS do seu endpoint está válido</li>
            </ul>
        </div>
    </div>
</body>
</html>
