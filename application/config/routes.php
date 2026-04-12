<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = 'mapos';
$route['404_override'] = '';

// Rotas de Email
$route['emails/dashboard'] = 'email/dashboard';
$route['emails/(:any)'] = 'email/$1';

// Rotas de Certificado Digital e NFS-e
$route['certificado'] = 'certificado';
$route['certificado/configurar'] = 'certificado/configurar';
$route['certificado/nfse'] = 'certificado/nfse';
$route['certificado/importar_nfse'] = 'certificado/importar_nfse';
$route['certificado/listar_nfse_disponiveis'] = 'certificado/listar_nfse_disponiveis';
$route['certificado/vincular_nfse_os'] = 'certificado/vincular_nfse_os';

// Rotas simplificadas para NFS-e
$route['nfse'] = 'certificado/nfse';
$route['nfse/importar'] = 'certificado/importar_nfse';

// Rotas de Impostos
$route['impostos'] = 'impostos';
$route['impostos/configuracoes'] = 'impostos/configuracoes';
$route['impostos/simulador'] = 'impostos/simulador';

// Rotas de DRE
$route['dre'] = 'dre';
$route['dre/contas'] = 'dre/contas';
$route['dre/lancamentos'] = 'dre/lancamentos';

// Rotas de Relatórios de Técnicos
$route['relatoriotecnicos'] = 'relatoriotecnicos';

// Rotas de Webhooks
$route['webhooks'] = 'webhooks';
$route['webhooks/docs'] = 'webhooks/docs';

// Rotas da API
if (filter_var($_ENV['API_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
    require APPPATH . 'config/routes_api.php';
}

// Rotas API V2 (sempre habilitadas para MAPOS 5.0)
$route['api/v2'] = 'api/docs';
$route['api/docs'] = 'api/docs';
$route['api/v2/health'] = 'api/v2/AuthController/health';
$route['api/v2/auth/login'] = 'api/v2/AuthController/login';
$route['api/v2/auth/refresh'] = 'api/v2/AuthController/refresh';
$route['api/v2/auth/logout'] = 'api/v2/AuthController/logout';
$route['api/v2/clientes'] = 'api/v2/ClientesController/index';
$route['api/v2/clientes/(:num)'] = 'api/v2/ClientesController/show/$1';
$route['api/v2/os'] = 'api/v2/OsController/index';
$route['api/v2/os/(:num)'] = 'api/v2/OsController/show/$1';
$route['api/v2/vendas'] = 'api/v2/VendasController/index';
$route['api/v2/vendas/(:num)'] = 'api/v2/VendasController/show/$1';
$route['api/v2/produtos'] = 'api/v2/ProdutosController/index';
$route['api/v2/produtos/(:num)'] = 'api/v2/ProdutosController/show/$1';
$route['api/v2/webhooks'] = 'api/v2/WebhooksController/index';

// Rotas do Relatório de Atendimentos
$route['relatorioatendimentos'] = 'relatorioatendimentos';
$route['relatorioatendimentos/listar'] = 'relatorioatendimentos/listar';
$route['relatorioatendimentos/estatisticas'] = 'relatorioatendimentos/estatisticas';
$route['relatorioatendimentos/exportar'] = 'relatorioatendimentos/exportar';
$route['relatorioatendimentos/visualizar/(:num)'] = 'relatorioatendimentos/visualizar/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
