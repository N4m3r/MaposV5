<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Rotas API V1
$route['api/v1'] = 'api/v1/ApiController/index';
$route['api/v1/audit'] = 'api/v1/ApiController/audit';
$route['api/v1/emitente'] = 'api/v1/ApiController/emitente';
$route['api/v1/calendario'] = 'api/v1/ApiController/calendario';
$route['api/v1/login'] = 'api/v1/UsuariosController/login';
$route['api/v1/reGenToken'] = 'api/v1/UsuariosController/reGenToken';
$route['api/v1/conta'] = 'api/v1/UsuariosController/conta';
$route['api/v1/clientes'] = 'api/v1/ClientesController/index';
$route['api/v1/clientes/(:num)'] = 'api/v1/ClientesController/index/$1';
$route['api/v1/produtos'] = 'api/v1/ProdutosController/index';
$route['api/v1/produtos/(:num)'] = 'api/v1/ProdutosController/index/$1';
$route['api/v1/servicos'] = 'api/v1/ServicosController/index';
$route['api/v1/servicos/(:num)'] = 'api/v1/ServicosController/index/$1';
$route['api/v1/usuarios'] = 'api/v1/UsuariosController/index';
$route['api/v1/usuarios/(:num)'] = 'api/v1/UsuariosController/index/$1';
$route['api/v1/os'] = 'api/v1/OsController/index';
$route['api/v1/os/(:num)'] = 'api/v1/OsController/index/$1';
$route['api/v1/os/(:num)/produtos'] = 'api/v1/OsController/produtos/$1';
$route['api/v1/os/(:num)/produtos/(:num)'] = 'api/v1/OsController/produtos/$1/$2';
$route['api/v1/os/(:num)/servicos'] = 'api/v1/OsController/servicos/$1';
$route['api/v1/os/(:num)/servicos/(:num)'] = 'api/v1/OsController/servicos/$1/$2';
$route['api/v1/os/(:num)/anotacoes'] = 'api/v1/OsController/anotacoes/$1';
$route['api/v1/os/(:num)/anotacoes/(:num)'] = 'api/v1/OsController/anotacoes/$1/$2';
$route['api/v1/os/(:num)/anexos'] = 'api/v1/OsController/anexos/$1';
$route['api/v1/os/(:num)/anexos/(:num)'] = 'api/v1/OsController/anexos/$1/$2';
$route['api/v1/os/(:num)/desconto'] = 'api/v1/OsController/desconto/$1';
$route['api/v1/os/(:num)/tecnico'] = 'api/v1/OsController/tecnico/$1';
$route['api/v1/os/tecnico/atribuir'] = 'api/v1/OsController/atribuirTecnico';
$route['api/v1/os/tecnico/remover'] = 'api/v1/OsController/removerTecnico';
$route['api/v1/os/tecnico/historico/(:num)'] = 'api/v1/OsController/historicoTecnico/$1';
$route['api/v1/tecnicos'] = 'api/v1/OsController/listarTecnicos';

/*
Routes for clients API
Rotas Para API area do cliente.
*/

$route['api/v1/client'] = 'api/v1/client/ClientOsController/index';
$route['api/v1/client/auth'] = 'api/v1/client/ClientLoginController/login';

$route['api/v1/client/os'] = 'api/v1/client/ClientOsController/os';
$route['api/v1/client/os/(:num)'] = 'api/v1/client/ClientOsController/os/$1';

$route['api/v1/client/compras'] = 'api/v1/client/ClientComprasController/index';
$route['api/v1/client/compras/(:num)'] = 'api/v1/client/ClientComprasController/index/$1';

$route['api/v1/client/cobrancas'] = 'api/v1/client/ClientCobrancasController/index';

// =============================================================================
// ROTAS API V2 - MAPOS 5.0
// =============================================================================

// Auth JWT
$route['api/v2/auth/login'] = 'api/v2/AuthController/login';
$route['api/v2/auth/refresh'] = 'api/v2/AuthController/refresh';
$route['api/v2/auth/logout'] = 'api/v2/AuthController/logout';

// Clientes
$route['api/v2/clientes'] = 'api/v2/ClientesController/index';
$route['api/v2/clientes/(:num)'] = 'api/v2/ClientesController/show/$1';
$route['api/v2/clientes/(:num)/os'] = 'api/v2/ClientesController/os/$1';

// Ordens de Serviço
$route['api/v2/os'] = 'api/v2/OsController/index';
$route['api/v2/os/(:num)'] = 'api/v2/OsController/show/$1';
$route['api/v2/os/(:num)/status'] = 'api/v2/OsController/updateStatus/$1';
$route['api/v2/os/(:num)/produtos'] = 'api/v2/OsController/produtos/$1';
$route['api/v2/os/(:num)/servicos'] = 'api/v2/OsController/servicos/$1';

// Vendas
$route['api/v2/vendas'] = 'api/v2/VendasController/index';
$route['api/v2/vendas/(:num)'] = 'api/v2/VendasController/show/$1';

// Produtos
$route['api/v2/produtos'] = 'api/v2/ProdutosController/index';
$route['api/v2/produtos/(:num)'] = 'api/v2/ProdutosController/show/$1';
$route['api/v2/produtos/(:num)/estoque'] = 'api/v2/ProdutosController/estoque/$1';

// Webhooks
$route['api/v2/webhooks'] = 'api/v2/WebhooksController/index';
$route['api/v2/webhooks/(:num)'] = 'api/v2/WebhooksController/show/$1';

// Evolution API - Monitoramento de IP dinâmico
$route['api/v2/evolution/atualizar-ip'] = 'api/v2/EvolutionController/atualizar_ip';

// Documentação
$route['api/v2'] = 'api/docs';
$route['api/docs'] = 'api/docs';

