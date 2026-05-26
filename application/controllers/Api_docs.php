<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_docs extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function docs()
    {
        $this->load->view('api_docs/swagger');
    }

    public function openapi()
    {
        $baseUrl = base_url();

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Map-OS API',
                'version' => '2.0.0',
                'description' => 'API RESTful para o sistema Map-OS v5. Gerenciamento de OS, clientes, produtos, servicos, vendas, cobrancas, webhooks e mais.',
                'contact' => [
                    'name' => 'Map-OS',
                    'url' => 'https://github.com/RamonSilva20/mapos',
                ],
                'license' => [
                    'name' => 'MIT',
                    'url' => 'https://opensource.org/licenses/MIT',
                ],
            ],
            'servers' => [
                ['url' => $baseUrl . 'api/v2', 'description' => 'API v2'],
            ],
            'tags' => [
                ['name' => 'Auth', 'description' => 'Autenticacao JWT'],
                ['name' => 'Clientes', 'description' => 'Gerenciamento de clientes'],
                ['name' => 'OS', 'description' => 'Ordens de servico'],
                ['name' => 'Produtos', 'description' => 'Gerenciamento de produtos'],
                ['name' => 'Servicos', 'description' => 'Gerenciamento de servicos'],
                ['name' => 'Usuarios', 'description' => 'Gerenciamento de usuarios'],
                ['name' => 'Vendas', 'description' => 'Gerenciamento de vendas'],
                ['name' => 'Cobrancas', 'description' => 'Cobrancas e financeiro'],
                ['name' => 'Dashboard', 'description' => 'Dashboard, calendario e estatisticas'],
                ['name' => 'Webhooks', 'description' => 'Configuracao de webhooks'],
                ['name' => 'Notificacoes', 'description' => 'Notificacoes e templates'],
                ['name' => 'Relatorios', 'description' => 'Relatorios e exportacao'],
                ['name' => 'LGPD', 'description' => 'Conformidade LGPD'],
                ['name' => 'Agente IA', 'description' => 'Autorizacoes e acoes do agente IA'],
                ['name' => 'WhatsApp', 'description' => 'Webhooks do Evolution API / WhatsApp'],
                ['name' => 'Portal do Cliente', 'description' => 'API para o portal do cliente'],
            ],
            'components' => [
                'securitySchemes' => [
                    'BearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                        'description' => 'Token JWT obtido via POST /auth/login',
                    ],
                    'ApiKeyAuth' => [
                        'type' => 'apiKey',
                        'in' => 'header',
                        'name' => 'X-API-Key',
                        'description' => 'Chave API do sistema (env API_MAPOS_KEY). Requer header X-API-Scopes.',
                    ],
                ],
                'schemas' => [
                    'Error' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => false],
                            'message' => ['type' => 'string'],
                            'errors' => ['type' => 'array', 'items' => ['type' => 'string']],
                        ],
                    ],
                    'PaginatedResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean'],
                            'data' => ['type' => 'array', 'items' => ['type' => 'object']],
                            'meta' => [
                                'type' => 'object',
                                'properties' => [
                                    'total' => ['type' => 'integer'],
                                    'page' => ['type' => 'integer'],
                                    'per_page' => ['type' => 'integer'],
                                    'total_pages' => ['type' => 'integer'],
                                    'timestamp' => ['type' => 'string'],
                                    'version' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                    'LoginRequest' => [
                        'type' => 'object',
                        'required' => ['email', 'password'],
                        'properties' => [
                            'email' => ['type' => 'string', 'format' => 'email'],
                            'password' => ['type' => 'string', 'format' => 'password'],
                        ],
                    ],
                    'LoginResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean'],
                            'data' => [
                                'type' => 'object',
                                'properties' => [
                                    'access_token' => ['type' => 'string'],
                                    'refresh_token' => ['type' => 'string'],
                                    'token_type' => ['type' => 'string', 'example' => 'Bearer'],
                                    'expires_in' => ['type' => 'integer', 'example' => 86400],
                                    'user' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer'],
                                            'name' => ['type' => 'string'],
                                            'email' => ['type' => 'string'],
                                            'permissions' => ['type' => 'array', 'items' => ['type' => 'string']],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'Cliente' => [
                        'type' => 'object',
                        'properties' => [
                            'idClientes' => ['type' => 'integer'],
                            'nomeCliente' => ['type' => 'string'],
                            'documento' => ['type' => 'string'],
                            'email' => ['type' => 'string'],
                            'telefone' => ['type' => 'string'],
                            'celular' => ['type' => 'string'],
                            'rua' => ['type' => 'string'],
                            'numero' => ['type' => 'string'],
                            'bairro' => ['type' => 'string'],
                            'cidade' => ['type' => 'string'],
                            'estado' => ['type' => 'string'],
                        ],
                    ],
                    'OS' => [
                        'type' => 'object',
                        'properties' => [
                            'idOs' => ['type' => 'integer'],
                            'dataInicial' => ['type' => 'string', 'format' => 'date'],
                            'dataFinal' => ['type' => 'string', 'format' => 'date'],
                            'status' => ['type' => 'string', 'enum' => ['Aberto', 'Em Andamento', 'Negociacao', 'Orcamento', 'Finalizado', 'Faturado', 'Cancelado', 'Aguardando Pecas']],
                            'descricaoProduto' => ['type' => 'string'],
                            'defeito' => ['type' => 'string'],
                            'observacoes' => ['type' => 'string'],
                            'laudoTecnico' => ['type' => 'string'],
                            'valorTotal' => ['type' => 'number'],
                            'clientes_id' => ['type' => 'integer'],
                            'usuarios_id' => ['type' => 'integer'],
                        ],
                    ],
                    'Produto' => [
                        'type' => 'object',
                        'properties' => [
                            'idProdutos' => ['type' => 'integer'],
                            'descricao' => ['type' => 'string'],
                            'precoVenda' => ['type' => 'number'],
                            'estoque' => ['type' => 'integer'],
                            'estoqueMinimo' => ['type' => 'integer'],
                            'codDeBarra' => ['type' => 'string'],
                        ],
                    ],
                    'Servico' => [
                        'type' => 'object',
                        'properties' => [
                            'idServicos' => ['type' => 'integer'],
                            'nome' => ['type' => 'string'],
                            'descricao' => ['type' => 'string'],
                            'preco' => ['type' => 'number'],
                        ],
                    ],
                    'Usuario' => [
                        'type' => 'object',
                        'properties' => [
                            'idUsuarios' => ['type' => 'integer'],
                            'nome' => ['type' => 'string'],
                            'email' => ['type' => 'string'],
                            'telefone' => ['type' => 'string'],
                            'situacao' => ['type' => 'integer'],
                            'permissoes_id' => ['type' => 'integer'],
                        ],
                    ],
                    'Venda' => [
                        'type' => 'object',
                        'properties' => [
                            'idVendas' => ['type' => 'integer'],
                            'dataVenda' => ['type' => 'string', 'format' => 'date'],
                            'valorTotal' => ['type' => 'number'],
                            'clientes_id' => ['type' => 'integer'],
                            'usuarios_id' => ['type' => 'integer'],
                        ],
                    ],
                ],
                'parameters' => [
                    'page' => ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 1], 'description' => 'Numero da pagina'],
                    'per_page' => ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 20, 'maximum' => 100], 'description' => 'Itens por pagina (max 100)'],
                    'search' => ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string'], 'description' => 'Busca textual'],
                    'idPath' => ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID do recurso'],
                ],
            ],
            'paths' => $this->getPaths(),
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($spec, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    private function getPaths(): array
    {
        $p = [];

        // === AUTH ===
        $p['/auth/login'] = [
            'post' => [
                'tags' => ['Auth'],
                'summary' => 'Login - Gerar token JWT',
                'requestBody' => ['$ref' => '#/components/schemas/LoginRequest'],
                'responses' => [
                    '200' => ['description' => 'Token gerado', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/LoginResponse']]]],
                    '401' => ['description' => 'Credenciais invalidas'],
                ],
            ],
        ];
        $p['/auth/refresh'] = [
            'post' => [
                'tags' => ['Auth'],
                'summary' => 'Renovar token JWT',
                'security' => [['BearerAuth' => []]],
                'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['refresh_token'], 'properties' => ['refresh_token' => ['type' => 'string']]]]]],
                'responses' => ['200' => ['description' => 'Novo par de tokens'], '401' => ['description' => 'Refresh token invalido']],
            ],
        ];
        $p['/auth/logout'] = [
            'post' => [
                'tags' => ['Auth'],
                'summary' => 'Revogar token JWT',
                'security' => [['BearerAuth' => []]],
                'responses' => ['200' => ['description' => 'Token revogado']],
            ],
        ];
        $p['/health'] = [
            'get' => [
                'tags' => ['Auth'],
                'summary' => 'Health check',
                'responses' => ['200' => ['description' => 'API operacional']],
            ],
        ];

        // === CLIENTES ===
        $crud = ['Clientes', 'OS', 'Produtos', 'Servicos', 'Usuarios', 'Vendas'];
        $schemas = ['Clientes' => 'Cliente', 'OS' => 'OS', 'Produtos' => 'Produto', 'Servicos' => 'Servico', 'Usuarios' => 'Usuario', 'Vendas' => 'Venda'];
        $paths = ['Clientes' => '/clientes', 'OS' => '/os', 'Produtos' => '/produtos', 'Servicos' => '/servicos', 'Usuarios' => '/usuarios', 'Vendas' => '/vendas'];
        $permPrefix = ['Clientes' => 'clientes', 'OS' => 'os', 'Produtos' => 'produtos', 'Servicos' => 'servicos', 'Usuarios' => 'usuarios', 'Vendas' => 'vendas'];

        foreach ($crud as $tag) {
            $base = $paths[$tag];
            $schema = $schemas[$tag];
            $perm = $permPrefix[$tag];

            $p[$base] = [
                'get' => [
                    'tags' => [$tag],
                    'summary' => "Listar {$tag}",
                    'security' => [['BearerAuth' => []], ['ApiKeyAuth' => []]],
                    'parameters' => [['$ref' => '#/components/parameters/page'], ['$ref' => '#/components/parameters/per_page'], ['$ref' => '#/components/parameters/search']],
                    'responses' => ['200' => ['description' => 'Lista paginada', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PaginatedResponse']]]]],
                ],
                'post' => [
                    'tags' => [$tag],
                    'summary' => "Criar {$tag}",
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => ['content' => ['application/json' => ['schema' => ['$ref' => "#/components/schemas/{$schema}"]]]],
                    'responses' => ['201' => ['description' => 'Criado com sucesso'], '400' => ['description' => 'Dados invalidos'], '403' => ['description' => 'Sem permissao']],
                ],
            ];

            $p[$base . '/{id}'] = [
                'get' => [
                    'tags' => [$tag],
                    'summary' => "Obter {$tag} por ID",
                    'security' => [['BearerAuth' => []], ['ApiKeyAuth' => []]],
                    'parameters' => [['$ref' => '#/components/parameters/idPath']],
                    'responses' => ['200' => ['description' => 'Registro encontrado'], '404' => ['description' => 'Nao encontrado']],
                ],
                'put' => [
                    'tags' => [$tag],
                    'summary' => "Atualizar {$tag}",
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['$ref' => '#/components/parameters/idPath']],
                    'requestBody' => ['content' => ['application/json' => ['schema' => ['$ref' => "#/components/schemas/{$schema}"]]]],
                    'responses' => ['200' => ['description' => 'Atualizado'], '403' => ['description' => 'Sem permissao'], '404' => ['description' => 'Nao encontrado']],
                ],
                'delete' => [
                    'tags' => [$tag],
                    'summary' => "Remover {$tag}",
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['$ref' => '#/components/parameters/idPath']],
                    'responses' => ['200' => ['description' => 'Removido'], '403' => ['description' => 'Sem permissao'], '404' => ['description' => 'Nao encontrado']],
                ],
            ];
        }

        // === OS SUB-RECURSOS ===
        $p['/os/{id}/produtos'] = [
            'get' => ['tags' => ['OS'], 'summary' => 'Listar produtos da OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Lista de produtos']]],
            'post' => ['tags' => ['OS'], 'summary' => 'Adicionar produto a OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['idProduto', 'quantidade'], 'properties' => ['idProduto' => ['type' => 'integer'], 'quantidade' => ['type' => 'integer'], 'preco' => ['type' => 'number']]]]]], 'responses' => ['201' => ['description' => 'Produto adicionado']]],
        ];
        $p['/os/{id}/servicos'] = [
            'get' => ['tags' => ['OS'], 'summary' => 'Listar servicos da OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Lista de servicos']]],
            'post' => ['tags' => ['OS'], 'summary' => 'Adicionar servico a OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['idServico'], 'properties' => ['idServico' => ['type' => 'integer'], 'quantidade' => ['type' => 'integer'], 'preco' => ['type' => 'number']]]]]], 'responses' => ['201' => ['description' => 'Servico adicionado']]],
        ];
        $p['/os/{id}/anotacoes'] = [
            'get' => ['tags' => ['OS'], 'summary' => 'Listar anotacoes da OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Lista de anotacoes']]],
            'post' => ['tags' => ['OS'], 'summary' => 'Adicionar anotacao a OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['anotacao'], 'properties' => ['anotacao' => ['type' => 'string']]]]]], 'responses' => ['201' => ['description' => 'Anotacao adicionada']]],
        ];
        $p['/os/{id}/anexos'] = [
            'get' => ['tags' => ['OS'], 'summary' => 'Listar anexos da OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Lista de anexos']]],
            'post' => ['tags' => ['OS'], 'summary' => 'Upload anexo a OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'requestBody' => ['content' => ['multipart/form-data' => ['schema' => ['type' => 'object', 'required' => ['userfile'], 'properties' => ['userfile' => ['type' => 'string', 'format' => 'binary'], 'descricao' => ['type' => 'string']]]]]], 'responses' => ['201' => ['description' => 'Anexo enviado']]],
        ];
        $p['/os/{id}/desconto'] = [
            'post' => ['tags' => ['OS'], 'summary' => 'Aplicar desconto na OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['tipo_desconto', 'desconto'], 'properties' => ['tipo_desconto' => ['type' => 'string', 'enum' => ['porcento', 'real']], 'desconto' => ['type' => 'number']]]]]], 'responses' => ['200' => ['description' => 'Desconto aplicado']]],
        ];
        $p['/os/{id}/status'] = [
            'patch' => ['tags' => ['OS'], 'summary' => 'Atualizar status da OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['status'], 'properties' => ['status' => ['type' => 'string']]]]]], 'responses' => ['200' => ['description' => 'Status atualizado']]],
        ];
        $p['/os/{id}/tecnico'] = [
            'get' => ['tags' => ['OS'], 'summary' => 'Obter tecnico atual da OS', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Tecnico da OS']]],
        ];
        $p['/os/{id}/tecnico/historico'] = [
            'get' => ['tags' => ['OS'], 'summary' => 'Historico de atribuicoes de tecnico', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Historico']]],
        ];
        $p['/os/tecnico/atribuir'] = [
            'post' => ['tags' => ['OS'], 'summary' => 'Atribuir tecnico a OS', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['os_id', 'tecnico_id'], 'properties' => ['os_id' => ['type' => 'integer'], 'tecnico_id' => ['type' => 'integer']]]]]], 'responses' => ['200' => ['description' => 'Tecnico atribuido']]],
        ];
        $p['/os/tecnico/remover'] = [
            'post' => ['tags' => ['OS'], 'summary' => 'Remover tecnico da OS', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['os_id'], 'properties' => ['os_id' => ['type' => 'integer']]]]]], 'responses' => ['200' => ['description' => 'Tecnico removido']]],
        ];
        $p['/tecnicos'] = [
            'get' => ['tags' => ['OS'], 'summary' => 'Listar tecnicos disponiveis', 'security' => [['BearerAuth' => []]], 'responses' => ['200' => ['description' => 'Lista de tecnicos']]],
        ];

        // === DASHBOARD ===
        $p['/dashboard'] = ['get' => ['tags' => ['Dashboard'], 'summary' => 'Estatisticas do dashboard', 'security' => [['BearerAuth' => []]], 'responses' => ['200' => ['description' => 'Contagens e estatisticas']]]];
        $p['/calendario'] = ['get' => ['tags' => ['Dashboard'], 'summary' => 'Eventos do calendario', 'security' => [['BearerAuth' => []]], 'parameters' => [['name' => 'start', 'in' => 'query', 'schema' => ['type' => 'string', 'format' => 'date'], 'description' => 'Data inicio'], ['name' => 'end', 'in' => 'query', 'schema' => ['type' => 'string', 'format' => 'date'], 'description' => 'Data fim'], ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string'], 'description' => 'Filtrar por status']], 'responses' => ['200' => ['description' => 'Eventos do calendario']]]];
        $p['/emitente'] = ['get' => ['tags' => ['Dashboard'], 'summary' => 'Dados do emitente', 'security' => [['BearerAuth' => []]], 'responses' => ['200' => ['description' => 'Dados da empresa']]]];
        $p['/audit'] = ['get' => ['tags' => ['Dashboard'], 'summary' => 'Logs de auditoria', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/page'], ['$ref' => '#/components/parameters/per_page']], 'responses' => ['200' => ['description' => 'Logs paginados'], '403' => ['description' => 'Sem permissao']]]];

        // === COBRANCAS ===
        $p['/cobrancas'] = ['get' => ['tags' => ['Cobrancas'], 'summary' => 'Listar cobrancas', 'security' => [['BearerAuth' => []]], 'parameters' => [['name' => 'cliente_id', 'in' => 'query', 'schema' => ['type' => 'integer']], ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string']]], 'responses' => ['200' => ['description' => 'Lista de cobrancas']]]];
        $p['/cobrancas/pendentes'] = ['get' => ['tags' => ['Cobrancas'], 'summary' => 'Cobrancas pendentes proximas do vencimento', 'security' => [['BearerAuth' => []]], 'responses' => ['200' => ['description' => 'Cobrancas pendentes']]]];

        // === WEBHOOKS ===
        $p['/webhooks'] = [
            'get' => ['tags' => ['Webhooks'], 'summary' => 'Listar webhooks configurados', 'security' => [['BearerAuth' => []]], 'responses' => ['200' => ['description' => 'Lista de webhooks']]],
        ];
        $p['/webhooks/evolution'] = [
            'post' => ['tags' => ['WhatsApp'], 'summary' => 'Receber webhook do Evolution API', 'description' => 'Endpoint para receber mensagens WhatsApp via Evolution API', 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object']]]], 'responses' => ['200' => ['description' => 'Webhook recebido']]],
        ];
        $p['/webhooks/evolution/status'] = [
            'post' => ['tags' => ['WhatsApp'], 'summary' => 'Receber status de mensagem do Evolution API', 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object']]]], 'responses' => ['200' => ['description' => 'Status recebido']]],
        ];

        // === NOTIFICACOES ===
        $p['/notificacoes/template'] = ['get' => ['tags' => ['Notificacoes'], 'summary' => 'Obter template de notificacao', 'security' => [['BearerAuth' => []]], 'parameters' => [['name' => 'evento', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string']]], 'responses' => ['200' => ['description' => 'Template']]]];
        $p['/notificacoes/log'] = ['post' => ['tags' => ['Notificacoes'], 'summary' => 'Registrar notificacao enviada', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object']]]], 'responses' => ['201' => ['description' => 'Log registrado']]]];

        // === RELATORIOS ===
        $p['/relatorios/{tipo}'] = ['get' => ['tags' => ['Relatorios'], 'summary' => 'Gerar relatorio', 'security' => [['BearerAuth' => []]], 'parameters' => [['name' => 'tipo', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string', 'enum' => ['os_periodo', 'os_hoje', 'os_mes', 'historico_cliente', 'resumo_financeiro', 'vendas', 'estoque', 'produtividade_tecnico', 'cobrancas_vencidas', 'os_atrasados', 'clientes_top']]]], 'responses' => ['200' => ['description' => 'Dados do relatorio']]]];
        $p['/relatorios/exportar'] = ['post' => ['tags' => ['Relatorios'], 'summary' => 'Exportar relatorio em PDF', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['tipo'], 'properties' => ['tipo' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email']]]]]], 'responses' => ['200' => ['description' => 'URL de download ou email enviado']]]];

        // === LGPD ===
        $p['/lgpd/clientes/{id}/exportar'] = ['get' => ['tags' => ['LGPD'], 'summary' => 'Exportar dados do cliente (LGPD Art.18)', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Dados completos do cliente em JSON'], '403' => ['description' => 'Sem permissao lgpd_exportar']]]];
        $p['/lgpd/clientes/{id}/anonimizar'] = ['post' => ['tags' => ['LGPD'], 'summary' => 'Anonimizar dados pessoais do cliente', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Dados anonimizados'], '403' => ['description' => 'Sem permissao lgpd_anonimizar']]]];
        $p['/lgpd/clientes/{id}/consentimento'] = [
            'get' => ['tags' => ['LGPD'], 'summary' => 'Consultar status de consentimento', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Status de consentimento']]],
            'post' => ['tags' => ['LGPD'], 'summary' => 'Registrar consentimento LGPD', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Consentimento registrado']]],
        ];
        $p['/lgpd/clientes/{id}/revogar_consentimento'] = ['post' => ['tags' => ['LGPD'], 'summary' => 'Revogar consentimento LGPD', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Consentimento revogado']]]];
        $p['/lgpd/vazamentos'] = ['get' => ['tags' => ['LGPD'], 'summary' => 'Listar notificacoes de vazamento de dados', 'security' => [['BearerAuth' => []]], 'responses' => ['200' => ['description' => 'Lista de vazamentos']]]];
        $p['/lgpd/vazamentos/{id}'] = ['get' => ['tags' => ['LGPD'], 'summary' => 'Detalhe de notificacao de vazamento', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Detalhe do vazamento']]]];

        // === AGENTE IA ===
        $p['/autorizacoes/verificar'] = ['post' => ['tags' => ['Agente IA'], 'summary' => 'Verificar se telefone pode executar acao', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['numero_telefone', 'acao'], 'properties' => ['numero_telefone' => ['type' => 'string'], 'acao' => ['type' => 'string']]]]]], 'responses' => ['200' => ['description' => 'Resultado da verificacao']]]];
        $p['/autorizacoes/solicitar'] = ['post' => ['tags' => ['Agente IA'], 'summary' => 'Solicitar token de autorizacao', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['numero_telefone', 'acao'], 'properties' => ['numero_telefone' => ['type' => 'string'], 'acao' => ['type' => 'string']]]]]], 'responses' => ['200' => ['description' => 'Token de autorizacao gerado']]]];
        $p['/autorizacoes/validar'] = ['post' => ['tags' => ['Agente IA'], 'summary' => 'Validar token de autorizacao', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['token'], 'properties' => ['token' => ['type' => 'string']]]]]], 'responses' => ['200' => ['description' => 'Resultado da validacao']]]];
        $p['/autorizacoes/listar'] = ['get' => ['tags' => ['Agente IA'], 'summary' => 'Listar autorizacoes', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/page'], ['$ref' => '#/components/parameters/per_page']], 'responses' => ['200' => ['description' => 'Lista de autorizacoes']]]];
        $p['/autorizacoes/responder'] = ['post' => ['tags' => ['Agente IA'], 'summary' => 'Responder solicitacao de autorizacao', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['token', 'decisao'], 'properties' => ['token' => ['type' => 'string'], 'decisao' => ['type' => 'string', 'enum' => ['aprovar', 'rejeitar']]]]]]], 'responses' => ['200' => ['description' => 'Autorizacao respondida']]]];
        $p['/acoes/executar'] = ['post' => ['tags' => ['Agente IA'], 'summary' => 'Executar acao do agente IA', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['acao', 'numero_telefone'], 'properties' => ['acao' => ['type' => 'string', 'enum' => ['criar_os', 'aprovar_orcamento', 'atualizar_status_os', 'registrar_atividade', 'gerar_cobranca', 'gerar_boleto', 'excluir_os', 'emitir_nfse']], 'numero_telefone' => ['type' => 'string'], 'token_autorizacao' => ['type' => 'string'], 'dados' => ['type' => 'object']]]]]], 'responses' => ['200' => ['description' => 'Acao executada'], '403' => ['description' => 'Sem autorizacao']]]];

        // === PORTAL DO CLIENTE ===
        $p['/client/auth'] = [
            'post' => ['tags' => ['Portal do Cliente'], 'summary' => 'Login do cliente (portal)', 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['email', 'senha'], 'properties' => ['email' => ['type' => 'string', 'format' => 'email'], 'senha' => ['type' => 'string', 'format' => 'password']]]]]], 'responses' => ['200' => ['description' => 'Token JWT do cliente'], '401' => ['description' => 'Credenciais invalidas']]],
        ];
        $p['/client/os'] = [
            'get' => ['tags' => ['Portal do Cliente'], 'summary' => 'Listar OS do cliente autenticado', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/page'], ['$ref' => '#/components/parameters/per_page']], 'responses' => ['200' => ['description' => 'Lista de OS']]],
            'post' => ['tags' => ['Portal do Cliente'], 'summary' => 'Criar OS como cliente', 'security' => [['BearerAuth' => []]], 'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['descricaoProduto' => ['type' => 'string'], 'defeito' => ['type' => 'string'], 'observacoes' => ['type' => 'string'], 'dataFinal' => ['type' => 'string', 'format' => 'date']]]]]], 'responses' => ['201' => ['description' => 'OS criada']]],
        ];
        $p['/client/os/{id}'] = ['get' => ['tags' => ['Portal do Cliente'], 'summary' => 'Detalhe de OS do cliente', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Detalhe da OS'], '404' => ['description' => 'OS nao encontrada ou nao pertence ao cliente']]]];
        $p['/client/compras'] = ['get' => ['tags' => ['Portal do Cliente'], 'summary' => 'Listar compras do cliente', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/page'], ['$ref' => '#/components/parameters/per_page']], 'responses' => ['200' => ['description' => 'Lista de compras']]]];
        $p['/client/compras/{id}'] = ['get' => ['tags' => ['Portal do Cliente'], 'summary' => 'Detalhe de compra', 'security' => [['BearerAuth' => []]], 'parameters' => [['$ref' => '#/components/parameters/idPath']], 'responses' => ['200' => ['description' => 'Detalhe da compra']]]];
        $p['/client/cobrancas'] = ['get' => ['tags' => ['Portal do Cliente'], 'summary' => 'Listar cobrancas do cliente', 'security' => [['BearerAuth' => []]], 'responses' => ['200' => ['description' => 'Lista de cobrancas']]]];

        return $p;
    }
}