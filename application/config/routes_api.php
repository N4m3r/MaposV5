<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// =============================================================================
// ROTAS API V2 - MAPOS 5.0 (UNIFICADA)
// =============================================================================

// Auth JWT
$route['api/v2/auth/login'] = 'api/v2/AuthController/login';
$route['api/v2/auth/refresh'] = 'api/v2/AuthController/refresh';
$route['api/v2/auth/logout'] = 'api/v2/AuthController/logout';
$route['api/v2/health'] = 'api/v2/AuthController/health';

// Clientes
$route['api/v2/clientes'] = 'api/v2/ClientesController/index';
$route['api/v2/clientes/(:num)'] = 'api/v2/ClientesController/show/$1';
$route['api/v2/clientes/(:num)/os'] = 'api/v2/ClientesController/os/$1';

// Ordens de Servico
$route['api/v2/os'] = 'api/v2/OsController/index';
$route['api/v2/os/(:num)'] = 'api/v2/OsController/show/$1';
$route['api/v2/os/(:num)/status'] = 'api/v2/OsController/updateStatus/$1';
$route['api/v2/os/(:num)/produtos'] = 'api/v2/OsController/produtos/$1';
$route['api/v2/os/(:num)/produtos/(:num)'] = 'api/v2/OsController/produtoUpdate/$1/$2';
$route['api/v2/os/(:num)/servicos'] = 'api/v2/OsController/servicos/$1';
$route['api/v2/os/(:num)/servicos/(:num)'] = 'api/v2/OsController/servicoUpdate/$1/$2';
$route['api/v2/os/(:num)/anotacoes'] = 'api/v2/OsController/anotacoes/$1';
$route['api/v2/os/(:num)/anotacoes/(:num)'] = 'api/v2/OsController/anotacaoDelete/$1/$2';
$route['api/v2/os/(:num)/anexos'] = 'api/v2/OsController/anexos/$1';
$route['api/v2/os/(:num)/anexos/(:num)'] = 'api/v2/OsController/anexoDelete/$1/$2';
$route['api/v2/os/(:num)/desconto'] = 'api/v2/OsController/desconto/$1';
$route['api/v2/os/tecnico/atribuir'] = 'api/v2/OsController/atribuirTecnico';
$route['api/v2/os/tecnico/remover'] = 'api/v2/OsController/removerTecnico';
$route['api/v2/os/(:num)/tecnico'] = 'api/v2/OsController/tecnico/$1';
$route['api/v2/os/(:num)/tecnico/historico'] = 'api/v2/OsController/historicoTecnico/$1';
$route['api/v2/tecnicos'] = 'api/v2/OsController/listarTecnicos';

// Vendas
$route['api/v2/vendas'] = 'api/v2/VendasController/index';
$route['api/v2/vendas/(:num)'] = 'api/v2/VendasController/show/$1';

// Produtos
$route['api/v2/produtos'] = 'api/v2/ProdutosController/index';
$route['api/v2/produtos/(:num)'] = 'api/v2/ProdutosController/show/$1';
$route['api/v2/produtos/(:num)/estoque'] = 'api/v2/ProdutosController/estoque/$1';

// Servicos
$route['api/v2/servicos'] = 'api/v2/ServicosController/index';
$route['api/v2/servicos/(:num)'] = 'api/v2/ServicosController/show/$1';

// Usuarios
$route['api/v2/usuarios'] = 'api/v2/UsuariosController/index';
$route['api/v2/usuarios/conta'] = 'api/v2/UsuariosController/conta';
$route['api/v2/usuarios/(:num)'] = 'api/v2/UsuariosController/show/$1';

// Dashboard / Calendario / Emitente / Audit
$route['api/v2/dashboard'] = 'api/v2/DashboardController/index';
$route['api/v2/calendario'] = 'api/v2/DashboardController/calendario';
$route['api/v2/emitente'] = 'api/v2/DashboardController/emitente';
$route['api/v2/audit'] = 'api/v2/DashboardController/audit';

// Webhooks
$route['api/v2/webhooks'] = 'api/v2/WebhooksController/index';
$route['api/v2/webhooks/(:num)'] = 'api/v2/WebhooksController/show/$1';

// Notificacoes (Agente IA / n8n)
$route['api/v2/notificacoes/template'] = 'api/v2/NotificacoesController/template';
$route['api/v2/notificacoes/log']      = 'api/v2/NotificacoesController/log';

// Cobrancas
$route['api/v2/cobrancas']            = 'api/v2/CobrancasController/index';
$route['api/v2/cobrancas/pendentes'] = 'api/v2/CobrancasController/pendentes';

// Autorizacoes (Agente IA)
$route['api/v2/autorizacoes/verificar']  = 'api/v2/AutorizacoesController/verificar';
$route['api/v2/autorizacoes/solicitar']  = 'api/v2/AutorizacoesController/solicitar';
$route['api/v2/autorizacoes/validar']    = 'api/v2/AutorizacoesController/validar';
$route['api/v2/autorizacoes/listar']     = 'api/v2/AutorizacoesController/listar';
$route['api/v2/autorizacoes/responder']  = 'api/v2/AutorizacoesController/responder';

// Relatorios
$route['api/v2/relatorios/exportar']     = 'api/v2/RelatoriosController/exportar';
$route['api/v2/relatorios/(:any)']       = 'api/v2/RelatoriosController/index/$1';

// Acoes do Agente IA
$route['api/v2/acoes/executar']          = 'api/v2/AcoesController/executar';

// LGPD (Protecao de Dados)
$route['api/v2/lgpd/clientes/(:num)/exportar']     = 'api/v2/LgpdController/exportar/$1';
$route['api/v2/lgpd/clientes/(:num)/anonimizar']    = 'api/v2/LgpdController/anonimizar/$1';
$route['api/v2/lgpd/clientes/(:num)/consentimento'] = 'api/v2/LgpdController/consentimento/$1';
$route['api/v2/lgpd/clientes/(:num)/revogar_consentimento'] = 'api/v2/LgpdController/revogar_consentimento/$1';
$route['api/v2/lgpd/vazamentos']                   = 'api/v2/LgpdController/vazamentos';
$route['api/v2/lgpd/vazamentos/(:num)']             = 'api/v2/LgpdController/vazamento_detalhe/$1';

// Webhooks externos (Evolution API)
$route['api/v2/webhooks/evolution']        = 'api/v2/WhatsappController/evolution';
$route['api/v2/webhooks/evolution/status'] = 'api/v2/WhatsappController/evolution_status';

// Client Portal
$route['api/v2/client/auth'] = 'api/v2/ClientAuthController/login';
$route['api/v2/client/os'] = 'api/v2/ClientPortalController/os';
$route['api/v2/client/os/(:num)'] = 'api/v2/ClientPortalController/os/$1';
$route['api/v2/client/compras'] = 'api/v2/ClientPortalController/compras';
$route['api/v2/client/compras/(:num)'] = 'api/v2/ClientPortalController/compras/$1';
$route['api/v2/client/cobrancas'] = 'api/v2/ClientPortalController/cobrancas';

// Documentacao Swagger
$route['api/docs'] = 'api_docs/docs';
$route['api/docs/swagger'] = 'api_docs/docs';
$route['api/docs/openapi.json'] = 'api_docs/openapi';