<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li class="active">API v2 Documentação</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <!-- Sidebar -->
    <div class="span3">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-book"></i></span>
                <h5>Índice</h5>
            </div>
            <div class="widget-content" style="background: #2c3e50;">
                <ul class="nav nav-list" style="padding-left: 15px;">
                    <li class="nav-header" style="color: #fff; text-shadow: none; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 5px; margin-bottom: 10px;">Geral</li>
                    <li><a href="#intro" style="color: #fff; text-shadow: none;">Introdução</a></li>
                    <li><a href="#auth" style="color: #fff; text-shadow: none;">Autenticação</a></li>
                    <li><a href="#rate" style="color: #fff; text-shadow: none;">Rate Limits</a></li>
                    <li class="nav-header" style="color: #fff; text-shadow: none; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 5px; margin: 15px 0 10px 0;">Endpoints</li>
                    <li><a href="#clientes" style="color: #fff; text-shadow: none;">Clientes</a></li>
                    <li><a href="#os" style="color: #fff; text-shadow: none;">Ordens de Serviço</a></li>
                    <li><a href="#vendas" style="color: #fff; text-shadow: none;">Vendas</a></li>
                    <li><a href="#produtos" style="color: #fff; text-shadow: none;">Produtos</a></li>
                    <li><a href="#webhooks" style="color: #fff; text-shadow: none;">Webhooks</a></li>
                    <li><a href="#agenteia" style="color: #fff; text-shadow: none;">Agente IA / Autorizacoes</a></li>
                </ul>
            </div>
        </div>

        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-key"></i></span>
                <h5>Token de Autorizacao (Agente IA)</h5>
            </div>
            <div class="widget-content">
                <p>Gere um token de teste para o fluxo de autorizacoes do Agente IA:</p>
                <pre id="auth-token-display" style="font-size: 11px; word-break: break-all;">Clique em Gerar</pre>
                <button class="btn btn-small btn-primary" onclick="gerarAuthToken()">Gerar AUTH-Token</button>
            </div>
        </div>

        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-key"></i></span>
                <h5>Seu Token JWT</h5>
            </div>
            <div class="widget-content">
                <p>Use o token abaixo para autenticar suas requisicoes:</p>
                <pre id="token-display" style="font-size: 11px; word-break: break-all;">Clique em Gerar</pre>
                <button class="btn btn-small btn-primary" onclick="gerarToken()">Gerar Token</button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="span9">
        <!-- Introdução -->
        <div class="widget-box" id="intro">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                <h5>Introdução</h5>
            </div>
            <div class="widget-content">
                <p>A API v2 do MAPOS permite integrar seu sistema com outras aplicações.</p>

                <h5>Base URL</h5>
                <pre><?= $apiBaseUrl ?></pre>

                <h5>Health Check</h5>
                <p>Verifique se a API está online:</p>
                <pre>GET <?= $apiBaseUrl ?>/health</pre>
                <p><a href="<?= $apiBaseUrl ?>/health" target="_blank" class="btn btn-mini btn-info">Testar Health Check</a></p>

                <h5>Formato de Resposta</h5>
                <p>Todas as respostas são em JSON:</p>
                <pre>{
  "success": true,
  "data": { ... },
  "meta": {
    "total": 100,
    "page": 1,
    "per_page": 20
  }
}

// Em caso de erro:
{
  "success": false,
  "error": "Mensagem de erro",
  "code": 400
}</pre>
            </div>
        </div>

        <!-- Autenticação -->
        <div class="widget-box" id="auth">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-lock"></i></span>
                <h5>Autenticação</h5>
            </div>
            <div class="widget-content">
                <p>A API usa autenticação JWT (JSON Web Token). Inclua o token no header de cada requisição:</p>

                <pre>Authorization: Bearer {seu_token_jwt}</pre>

                <h5>Exemplo com cURL</h5>
                <pre>curl -X GET "<?= $apiBaseUrl ?>/clientes" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"</pre>

            </div>
        </div>

        <!-- Rate Limits -->
        <div class="widget-box" id="rate">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                <h5>Rate Limits</h5>
            </div>
            <div class="widget-content">
                <p>Para garantir a estabilidade do sistema, existem limites de requisições:</p>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Limite</th>
                            <th>Janela</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Geral</td>
                            <td>100</td>
                            <td>1 minuto</td>
                        </tr>
                        <tr>
                            <td>Login</td>
                            <td>5</td>
                            <td>1 minuto</td>
                        </tr>
                    </tbody>
                </table>

                <p>Os headers de resposta indicam o status do rate limit:</p>
                <pre>X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1640995200</pre>
            </div>
        </div>

        <!-- Clientes -->
        <div class="widget-box" id="clientes">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-users"></i></span>
                <h5>Clientes</h5>
            </div>
            <div class="widget-content">

                <h6><span class="label label-success">GET</span> /clientes</h6>
                <p>Lista todos os clientes.</p>
                <pre>// Query Parameters:
?page=1        // Página atual
?per_page=20   // Itens por página
?search=joao   // Busca por nome/email/telefone
?situacao=1    // 1=Ativo, 0=Inativo</pre>

                <hr>

                <h6><span class="label label-success">GET</span> /clientes/{id}</h6>
                <p>Obtém detalhes de um cliente.</p>

                <hr>

                <h6><span class="label label-info">POST</span> /clientes</h6>
                <p>Cria um novo cliente.</p>
                <pre>{
  "nomeCliente": "João Silva",
  "documento": "123.456.789-00",
  "telefone": "(11) 98765-4321",
  "email": "joao@email.com",
  "rua": "Rua Exemplo",
  "numero": "123",
  "bairro": "Centro",
  "cidade": "São Paulo",
  "estado": "SP",
  "cep": "01000-000"
}</pre>

                <hr>

                <h6><span class="label label-warning">PUT</span> /clientes/{id}</h6>
                <p>Atualiza um cliente.</p>

                <hr>

                <h6><span class="label label-important">DELETE</span> /clientes/{id}</h6>
                <p>Remove um cliente.</p>
            </div>
        </div>

        <!-- OS -->
        <div class="widget-box" id="os">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-clipboard"></i></span>
                <h5>Ordens de Serviço</h5>
            </div>
            <div class="widget-content">

                <h6><span class="label label-success">GET</span> /os</h6>
                <p>Lista todas as OS.</p>
                <pre>// Query Parameters:
?status=Aberto        // Filtrar por status
?cliente_id=123       // Filtrar por cliente
?tecnico_id=456       // Filtrar por técnico
?data_inicio=2024-01-01
?data_fim=2024-12-31</pre>

                <hr>

                <h6><span class="label label-info">POST</span> /os</h6>
                <p>Cria uma nova OS.</p>
                <pre>{
  "clientes_id": 123,
  "initial_name": "João Silva",
  "initial_document": "123.456.789-00",
  "initial_address": "Rua Exemplo, 123",
  "equipamento": "Notebook Dell",
  "marca": "Dell",
  "modelo": "Inspiron 15",
  "serial": "ABC123456",
  "descricaoProduto": "Problema na tela",
  "status": "Aberto",
  "prioridade": "Normal"
}</pre>

                <hr>

                <h6><span class="label label-warning">PUT</span> /os/{id}/status</h6>
                <p>Atualiza status da OS.</p>
                <pre>{ "status": "Finalizado", "observacoes": "Reparo concluído" }</pre>
            </div>
        </div>

        <!-- Vendas -->
        <div class="widget-box" id="vendas">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                <h5>Vendas</h5>
            </div>
            <div class="widget-content">

                <h6><span class="label label-success">GET</span> /vendas</h6>
                <p>Lista todas as vendas.</p>

                <hr>

                <h6><span class="label label-info">POST</span> /vendas</h6>
                <p>Cria uma nova venda.</p>
                <pre>{
  "clientes_id": 123,
  "valor_total": 1500.00,
  "desconto": 0,
  "tipo_desconto": "real",
  "forma_pgto": "Dinheiro",
  "itens": [
    {
      "produtos_id": 1,
      "quantidade": 2,
      "preco_venda": 750.00
    }
  ]
}</pre>
            </div>
        </div>

        <!-- Produtos -->
        <div class="widget-box" id="produtos">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-box"></i></span>
                <h5>Produtos</h5>
            </div>
            <div class="widget-content">

                <h6><span class="label label-success">GET</span> /produtos</h6>
                <p>Lista todos os produtos.</p>
                <pre>// Query Parameters:
?estoque_baixo=true   // Apenas produtos com estoque baixo
?ativo=true</pre>

                <hr>

                <h6><span class="label label-success">GET</span> /produtos/{id}/estoque</h6>
                <p>Consulta movimentações de estoque.</p>
            </div>
        </div>

        <!-- Webhooks -->
        <div class="widget-box" id="webhooks">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-webhook"></i></span>
                <h5>Webhooks</h5>
            </div>
            <div class="widget-content">

                <h6>Eventos Disponíveis</h6>

                <ul>
                    <li><code>os.created</code> - OS criada</li>
                    <li><code>os.updated</code> - OS atualizada</li>
                    <li><code>os.status_changed</code> - Status da OS alterado</li>
                    <li><code>os.finished</code> - OS finalizada</li>
                    <li><code>cliente.created</code> - Cliente criado</li>
                    <li><code>cliente.updated</code> - Cliente atualizado</li>
                    <li><code>venda.created</code> - Venda criada</li>
                    <li><code>venda.paid</code> - Venda paga</li>
                    <li><code>cobranca.paid</code> - Cobrança paga</li>
                    <li><code>produto.low_stock</code> - Produto com estoque baixo</li>
                </ul>

                <hr>

                <h6>Payload de Exemplo</h6>

                <pre>{
  "event": "os.created",
  "timestamp": "2024-01-15T10:30:00Z",
  "data": {
    "id": 123,
    "cliente": {
      "id": 456,
      "nome": "João Silva"
    },
    "equipamento": "Notebook Dell",
    "status": "Aberto",
    "valor": 150.00
  }
}</pre>

                <hr>

                <h6>Verificação de Assinatura</h6>
                <p>Cada webhook inclui um header de assinatura:</p>

                <pre>X-Webhook-Signature: sha256={hmac_sha256(payload, secret)}</pre>

                <p>Exemplo de verificação em PHP:</p>

                <pre>$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'];
$expected = hash_hmac('sha256', $payload, $secret);

if (!hash_equals('sha256=' . $expected, $signature)) {
  http_response_code(401);
  exit('Assinatura inválida');
}</pre>
            </div>
        </div>

        <!-- Agente IA / Autorizacoes -->
        <div class="widget-box" id="agenteia">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-robot"></i></span>
                <h5>Agente IA / Autorizacoes</h5>
            </div>
            <div class="widget-content">
                <p>Endpoints para controle de autorizacoes do Agente IA. O agente solicita aprovacao para acoes criticas via token no formato <code>AUTH-XXXXXXXX</code>.</p>

                <h6><span class="label label-success">GET</span> /autorizacoes/listar</h6>
                <p>Lista autorizacoes pendentes/aprovadas/rejeitadas.</p>
                <pre>// Query Parameters:
?status=pendente     // pendente | aprovada | rejeitada | expirada
?numero=559199999    // Filtrar por numero
?page=1
?per_page=20</pre>

                <hr>

                <h6><span class="label label-info">POST</span> /autorizacoes/verificar</h6>
                <p>Verifica se um numero pode executar uma acao sem autorizacao.</p>
                <pre>{
  "numero_telefone": "5591999999999",
  "acao": "criar_os"
}</pre>

                <hr>

                <h6><span class="label label-info">POST</span> /autorizacoes/solicitar</h6>
                <p>Cria uma nova autorizacao e retorna o token <code>AUTH-XXXXXXXX</code>.</p>
                <pre>{
  "numero_telefone": "5591999999999",
  "acao": "criar_os",
  "dados_json": "{\"clientes_id\":123}",
  "metodo": "whatsapp",
  "minutos_expira": 15
}

// Resposta:
{
  "success": true,
  "data": {
    "token": "AUTH-XXXXXXXX",
    "expires_at": "2026-05-03 15:30:00",
    "mensagem": "..."
  }
}</pre>

                <hr>

                <h6><span class="label label-info">POST</span> /autorizacoes/validar</h6>
                <p>Valida o token respondido pelo usuario.</p>
                <pre>{
  "numero_telefone": "5591999999999",
  "token": "AUTH-XXXXXXXX",
  "resposta_usuario": "sim"
}

// Resposta aprovada:
{
  "success": true,
  "data": {
    "status": "aprovada",
    "executar": true,
    "acao": "criar_os",
    "dados_json": { ... }
  }
}</pre>

                <hr>

                <h6><span class="label label-info">POST</span> /autorizacoes/responder</h6>
                <p>Admin responde uma autorizacao pelo painel.</p>
                <pre>{
  "autorizacao_id": 123,
  "resposta": "aprovar",
  "observacoes": "Autorizado pelo gestor"
}</pre>
            </div>
        </div>
    </div>
</div>

<script>
function gerarAuthToken() {
    fetch('<?= base_url('api_tools/auth_token') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.token) {
            document.getElementById('auth-token-display').textContent = data.token;
        } else {
            alert('Erro ao gerar token de autorizacao: ' + (data.error || 'desconhecido'));
        }
    })
    .catch(e => alert('Erro: ' + e.message));
}

function gerarToken() {
    fetch('<?= base_url('api_tools/token') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.token) {
            document.getElementById('token-display').textContent = data.token;
        } else {
            alert('Erro ao gerar token: ' + (data.error || 'desconhecido'));
        }
    })
    .catch(e => alert('Erro: ' + e.message));
}
</script>
