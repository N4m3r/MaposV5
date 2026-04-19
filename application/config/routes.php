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

// Rotas do Sistema de NFS-e e Boletos vinculados à OS
$route['nfse_os'] = 'nfse_os';
$route['nfse_os/index'] = 'nfse_os/index';
$route['nfse_os/emitir/(:num)'] = 'nfse_os/emitir/$1';
$route['nfse_os/gerar_boleto/(:num)'] = 'nfse_os/gerar_boleto/$1';
$route['nfse_os/gerar_boleto/(:num)/(:num)'] = 'nfse_os/gerar_boleto/$1/$2';
$route['nfse_os/calcular_impostos'] = 'nfse_os/calcular_impostos';
$route['nfse_os/visualizar/(:num)'] = 'nfse_os/visualizar/$1';
$route['nfse_os/cancelar_nfse/(:num)'] = 'nfse_os/cancelar_nfse/$1';
$route['nfse_os/cancelar_boleto/(:num)'] = 'nfse_os/cancelar_boleto/$1';
$route['nfse_os/registrar_pagamento/(:num)'] = 'nfse_os/registrar_pagamento/$1';
$route['nfse_os/relatorio'] = 'nfse_os/relatorio';
$route['nfse_os/api_get_os_dados/(:num)'] = 'nfse_os/api_get_os_dados/$1';
$route['nfse_os/enviar_boleto_email/(:num)'] = 'nfse_os/enviar_boleto_email/$1';
$route['nfse_os/preview/(:num)'] = 'nfse_os/preview/$1';
$route['nfse_os/imprimir_nfse/(:num)'] = 'nfse_os/imprimir_nfse/$1';
$route['nfse_os/emitir_nfse_api/(:num)'] = 'nfse_os/emitir_nfse_api/$1';
$route['nfse_os/cancelar_nfse_api/(:num)'] = 'nfse_os/cancelar_nfse_api/$1';
$route['nfse_os/consultar_nfse/(:num)'] = 'nfse_os/consultar_nfse/$1';

// Rotas de Impostos
$route['impostos'] = 'impostos';
$route['impostos/configuracoes'] = 'impostos/configuracoes';
$route['impostos/simulador'] = 'impostos/simulador';
$route['impostos/buscar_certificado'] = 'impostos/buscar_certificado';

// Rotas de DRE
$route['dre'] = 'dre';
$route['dre/contas'] = 'dre/contas';
$route['dre/lancamentos'] = 'dre/lancamentos';

// Rotas de Relatórios de Técnicos
$route['relatoriotecnicos'] = 'relatoriotecnicos';

// Rotas de Notificações
$route['notificacoes/listar'] = 'notificacoes/listar';
$route['notificacoes/marcar_lida'] = 'notificacoes/marcar_lida';

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

// Rotas do Dashboard para relatórios
$route['dashboard/relatorio_atendimentos'] = 'dashboard/relatorio_atendimentos';
$route['dashboard/relatorio_financeiro'] = 'dashboard/relatorio_financeiro';
$route['dashboard/relatorio_produtos'] = 'dashboard/relatorio_produtos';
$route['dashboard/relatorio_clientes'] = 'dashboard/relatorio_clientes';

// Rotas de Usuários Cliente (Multi-CNPJ)
$route['usuarioscliente'] = 'usuarioscliente';
$route['usuarioscliente/adicionar'] = 'usuarioscliente/adicionar';
$route['usuarioscliente/editar/(:num)'] = 'usuarioscliente/editar/$1';
$route['usuarioscliente/excluir/(:num)'] = 'usuarioscliente/excluir/$1';
$route['usuarioscliente/visualizar/(:num)'] = 'usuarioscliente/visualizar/$1';
$route['usuarioscliente/salvar'] = 'usuarioscliente/salvar';
$route['usuarioscliente/atualizar'] = 'usuarioscliente/atualizar';
$route['usuarioscliente/ativar/(:num)'] = 'usuarioscliente/ativar/$1';
$route['usuarioscliente/desativar/(:num)'] = 'usuarioscliente/desativar/$1';
// Rotas para gerenciar CNPJs vinculados
$route['usuarioscliente/adicionar_cnpj/(:num)'] = 'usuarioscliente/adicionar_cnpj/$1';
$route['usuarioscliente/remover_cnpj/(:num)/(:any)'] = 'usuarioscliente/remover_cnpj/$1/$2';
$route['usuarioscliente/get_cnpjs/(:num)'] = 'usuarioscliente/get_cnpjs/$1';

// Rotas do Gerenciador de Migrações
$route['migrate'] = 'migrate';
$route['migrate/latest'] = 'migrate/latest';
$route['migrate/version/(:num)'] = 'migrate/version/$1';
$route['migrate/reset'] = 'migrate/reset';
$route['migrate/status'] = 'migrate/status';

// ============================================
// ROTAS DO SISTEMA DE TÉCNICOS
// ============================================

// Portal do Técnico (Mobile/App)
$route['tecnicos'] = 'tecnicos/dashboard';
$route['tecnicos/login'] = 'tecnicos/login';
$route['tecnicos/autenticar'] = 'tecnicos/autenticar';
$route['tecnicos/logout'] = 'tecnicos/logout';
$route['tecnicos/dashboard'] = 'tecnicos/dashboard';
$route['tecnicos/minhas_os'] = 'tecnicos/minhas_os';
$route['tecnicos/executar_os/(:num)'] = 'tecnicos/executar_os/$1';
$route['tecnicos/relatorio_execucao/(:num)'] = 'tecnicos/relatorio_execucao/$1';
$route['tecnicos/meu_estoque'] = 'tecnicos/meu_estoque';
$route['tecnicos/perfil'] = 'tecnicos/perfil';
$route['tecnicos/atualizar_foto'] = 'tecnicos/atualizar_foto';

// APIs do Técnico
$route['tecnicos/api/login'] = 'tecnicos/api_login';
$route['tecnicos/api/verificar'] = 'tecnicos/api_verificar';
$route['tecnicos/iniciar_execucao'] = 'tecnicos/iniciar_execucao';
$route['tecnicos/finalizar_execucao'] = 'tecnicos/finalizar_execucao';
$route['tecnicos/adicionar_foto'] = 'tecnicos/adicionar_foto';
$route['tecnicos/salvar_checklist_item'] = 'tecnicos/salvar_checklist_item';
$route['tecnicos/registrar_uso_material'] = 'tecnicos/registrar_uso_material';

// Área do Técnico (Integrada ao Sistema)
$route['tecnico'] = 'tecnico';
$route['tecnico/os'] = 'tecnico/os';
$route['tecnico/visualizar/(:num)'] = 'tecnico/visualizar/$1';
$route['tecnico/checklist/(:num)'] = 'tecnico/checklist/$1';
$route['tecnico/pecas/(:num)'] = 'tecnico/pecas/$1';
$route['tecnico/etapas/(:num)'] = 'tecnico/etapas/$1';
$route['tecnico/timeline/(:num)'] = 'tecnico/timeline/$1';
$route['tecnico/iniciar_atendimento'] = 'tecnico/iniciar_atendimento';
$route['tecnico/finalizar_atendimento'] = 'tecnico/finalizar_atendimento';
$route['tecnico/atualizar_checklist_item'] = 'tecnico/atualizar_checklist_item';
$route['tecnico/adicionar_checklist_item'] = 'tecnico/adicionar_checklist_item';
$route['tecnico/adicionar_peca'] = 'tecnico/adicionar_peca';
$route['tecnico/iniciar_etapa'] = 'tecnico/iniciar_etapa';
$route['tecnico/concluir_etapa'] = 'tecnico/concluir_etapa';
$route['tecnico/api/listar_os'] = 'tecnico/api_listar_os';
$route['tecnico/api/os_detalhes/(:num)'] = 'tecnico/api_os_detalhes/$1';

// Administração de Técnicos
$route['tecnicos_admin'] = 'tecnicos_admin';
$route['tecnicos_admin/tecnicos'] = 'tecnicos_admin/tecnicos';
$route['tecnicos_admin/adicionar_tecnico'] = 'tecnicos_admin/adicionar_tecnico';
$route['tecnicos_admin/editar_tecnico/(:num)'] = 'tecnicos_admin/editar_tecnico/$1';
$route['tecnicos_admin/ver_tecnico/(:num)'] = 'tecnicos_admin/ver_tecnico/$1';
$route['tecnicos_admin/servicos_catalogo'] = 'tecnicos_admin/servicos_catalogo';
$route['tecnicos_admin/adicionar_servico'] = 'tecnicos_admin/adicionar_servico';
$route['tecnicos_admin/checklists'] = 'tecnicos_admin/checklists';
$route['tecnicos_admin/salvar_checklist'] = 'tecnicos_admin/salvar_checklist';
$route['tecnicos_admin/editar_checklist/(:num)'] = 'tecnicos_admin/editar_checklist/$1';
$route['tecnicos_admin/excluir_checklist/(:num)'] = 'tecnicos_admin/excluir_checklist/$1';
$route['tecnicos_admin/relatorios'] = 'tecnicos_admin/relatorios';
$route['tecnicos_admin/obras'] = 'tecnicos_admin/obras';
$route['tecnicos_admin/adicionar_obra'] = 'tecnicos_admin/adicionar_obra';
$route['tecnicos_admin/ver_obra/(:num)'] = 'tecnicos_admin/ver_obra/$1';
$route['tecnicos_admin/editar_obra/(:num)'] = 'tecnicos_admin/editar_obra/$1';
$route['tecnicos_admin/excluir_obra/(:num)'] = 'tecnicos_admin/excluir_obra/$1';
$route['tecnicos_admin/estoque_tecnico/(:num)'] = 'tecnicos_admin/estoque_tecnico/$1';
$route['tecnicos_admin/adicionar_estoque'] = 'tecnicos_admin/adicionar_estoque';
$route['tecnicos_admin/rotas'] = 'tecnicos_admin/rotas';
$route['tecnicos_admin/rotas/(:num)'] = 'tecnicos_admin/rotas/$1';
$route['tecnicos_admin/api/dados_dashboard'] = 'tecnicos_admin/api_dados_dashboard';
$route['tecnicos_admin/execucao'] = 'tecnicos_admin/execucao_obras';
$route['tecnicos_admin/buscar_os_por_obra'] = 'tecnicos_admin/buscar_os_por_obra';
$route['tecnicos_admin/editar_etapa/(:num)'] = 'tecnicos_admin/editar_etapa/$1';
$route['tecnicos_admin/excluir_etapa/(:num)'] = 'tecnicos_admin/excluir_etapa/$1';
$route['tecnicos_admin/buscar_etapa/(:num)'] = 'tecnicos_admin/buscar_etapa/$1';
$route['tecnicos_admin/buscar_tecnicos_disponiveis'] = 'tecnicos_admin/buscar_tecnicos_disponiveis';
$route['tecnicos_admin/alocar_tecnico'] = 'tecnicos_admin/alocar_tecnico';
$route['tecnicos_admin/adicionar_etapa'] = 'tecnicos_admin/adicionar_etapa';
$route['tecnicos_admin/atualizar_status_etapa'] = 'tecnicos_admin/atualizar_status_etapa';
$route['tecnicos_admin/buscar_atividades_obra/(:num)'] = 'tecnicos_admin/buscar_atividades_obra/$1';
$route['tecnicos_admin/adicionar_comentario'] = 'tecnicos_admin/adicionar_comentario';
$route['tecnicos_admin/remover_tecnico_equipe'] = 'tecnicos_admin/remover_tecnico_equipe';
$route['tecnicos_admin/buscar_os_disponiveis_simples'] = 'tecnicos_admin/buscar_os_disponiveis_simples';
$route['tecnicos_admin/minhas_obras'] = 'tecnicos_admin/minhas_obras_tecnico';
$route['tecnicos_admin/tecnico_executar_obra/(:num)'] = 'tecnicos_admin/tecnico_executar_obra/$1';
$route['tecnicos_admin/tecnico_atualizar_etapa'] = 'tecnicos_admin/tecnico_atualizar_etapa';
$route['tecnicos_admin/api_dados_obra/(:num)'] = 'tecnicos_admin/api_dados_obra/$1';

// Rotas para atribuir técnico às OS
$route['os/atribuir'] = 'os/atribuir';
$route['os/atribuirTecnicoAction'] = 'os/atribuirTecnicoAction';
$route['os/removerTecnicoAction'] = 'os/removerTecnicoAction';
$route['os/historicoAtribuicoes/(:num)'] = 'os/historicoAtribuicoes/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
