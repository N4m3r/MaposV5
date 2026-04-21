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

// Rotas de Configurações de Notificações WhatsApp
$route['notificacoes'] = 'notificacoesConfig/configuracoes';
$route['notificacoes/configuracoes'] = 'notificacoesConfig/configuracoes';
$route['notificacoes/templates'] = 'notificacoesConfig/templates';
$route['notificacoes/templates/editar/(:num)'] = 'notificacoesConfig/editar_template/$1';
$route['notificacoes/templates/toggle/(:num)'] = 'notificacoesConfig/toggle_template/$1';
$route['notificacoes/logs'] = 'notificacoesConfig/logs';
$route['notificacoes/logs/(:num)'] = 'notificacoesConfig/logs/$1';
$route['notificacoes/estatisticas'] = 'notificacoesConfig/estatisticas';
$route['notificacoes/enviar-manual'] = 'notificacoesConfig/enviar_manual';
$route['notificacoes/obter-qr'] = 'notificacoesConfig/obter_qr';
$route['notificacoes/verificar-status'] = 'notificacoesConfig/verificar_status';
$route['notificacoes/desconectar'] = 'notificacoesConfig/desconectar';
$route['notificacoes/testar-envio'] = 'notificacoesConfig/testar_envio';
$route['notificacoes/preview-template'] = 'notificacoesConfig/preview_template';

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

// Rotas da Área do Cliente (Portal do Cliente)
$route['mine/relatorioatendimento/(:num)'] = 'mine/relatorioAtendimento/$1';
$route['mine/relatorioAtendimento/(:num)'] = 'mine/relatorioAtendimento/$1';
$route['mine/visualizarOs/(:num)'] = 'mine/visualizarOs/$1';
$route['mine/imprimirOs/(:num)'] = 'mine/imprimirOs/$1';
$route['mine/detalhesOs/(:num)'] = 'mine/detalhesOs/$1';
$route['mine/aprovarOs/(:num)'] = 'mine/aprovarOs/$1';
$route['mine/visualizarCompra/(:num)'] = 'mine/visualizarCompra/$1';
$route['mine/imprimirCompra/(:num)'] = 'mine/imprimirCompra/$1';
$route['mine/visualizarObra/(:num)'] = 'mine/visualizarObra/$1';
$route['mine/atualizarcobranca/(:num)'] = 'mine/atualizarcobranca/$1';
$route['mine/enviarcobranca/(:num)'] = 'mine/enviarcobranca/$1';
$route['mine/uploadFotoCliente'] = 'mine/uploadFotoCliente';
$route['mine/salvarAssinaturaCliente'] = 'mine/salvarAssinaturaCliente';

// Rotas do Dashboard para relatórios
$route['dashboard/relatorio_atendimentos'] = 'dashboard/relatorio_atendimentos';
$route['dashboard/relatorio_financeiro'] = 'dashboard/relatorio_financeiro';
$route['dashboard/relatorio_produtos'] = 'dashboard/relatorio_produtos';
$route['dashboard/relatorio_clientes'] = 'dashboard/relatorio_clientes';

// ============================================
// ROTAS DO SISTEMA DE OBRAS (Gestão Completa)
// ============================================

// Administração de Obras
$route['obras'] = 'obras/gerenciar';
$route['obras/gerenciar'] = 'obras/gerenciar';
$route['obras/adicionar'] = 'obras/adicionar';
$route['obras/editar/(:num)'] = 'obras/editar/$1';
$route['obras/visualizar/(:num)'] = 'obras/visualizar/$1';
$route['obras/excluir'] = 'obras/excluir';
$route['obras/etapas/(:num)'] = 'obras/etapas/$1';
$route['obras/adicionarEtapa'] = 'obras/adicionarEtapa';
$route['obras/editarEtapa/(:num)'] = 'obras/editarEtapa/$1';
$route['obras/excluirEtapa/(:num)'] = 'obras/excluirEtapa/$1';
$route['obras/atividades/(:num)'] = 'obras/atividades/$1';
$route['obras/adicionarAtividade'] = 'obras/adicionarAtividade';
$route['obras/editarAtividade/(:num)'] = 'obras/editarAtividade/$1';
$route['obras/visualizarAtividade/(:num)'] = 'obras/visualizarAtividade/$1';
$route['obras/relatorioProgresso/(:num)'] = 'obras/relatorioProgresso/$1';
$route['obras/relatorioDiario/(:num)'] = 'obras/relatorioDiario/$1';
$route['obras/api/atualizarProgresso/(:num)'] = 'obras/api_atualizarProgresso/$1';
$route['obras/api/dadosGrafico/(:num)'] = 'obras/api_dadosGrafico/$1';
$route['obras/api/getCliente/(:num)'] = 'obras/api_getCliente/$1';

// Equipe
$route['obras/equipe/(:num)'] = 'obras/equipe/$1';
$route['obras/adicionarTecnico'] = 'obras/adicionarTecnico';
$route['obras/removerTecnico/(:num)'] = 'obras/removerTecnico/$1';

// Portal do Técnico - Obras
$route['obras_tecnico'] = 'obras_tecnico/minhasObras';
$route['obras_tecnico/minhasObras'] = 'obras_tecnico/minhasObras';
$route['obras_tecnico/obra/(:num)'] = 'obras_tecnico/obra/$1';
$route['obras_tecnico/atividade/(:num)'] = 'obras_tecnico/atividade/$1';
$route['obras_tecnico/iniciarAtividade'] = 'obras_tecnico/iniciarAtividade';
$route['obras_tecnico/pausarAtividade'] = 'obras_tecnico/pausarAtividade';
$route['obras_tecnico/retomarAtividade'] = 'obras_tecnico/retomarAtividade';
$route['obras_tecnico/finalizarAtividade'] = 'obras_tecnico/finalizarAtividade';
$route['obras_tecnico/registrarImpedimento'] = 'obras_tecnico/registrarImpedimento';
$route['obras_tecnico/registrarCheckin'] = 'obras_tecnico/registrarCheckin';
$route['obras_tecnico/registrarCheckout'] = 'obras_tecnico/registrarCheckout';
$route['obras_tecnico/uploadFoto'] = 'obras_tecnico/uploadFoto';
$route['obras_tecnico/listarFotos/(:num)'] = 'obras_tecnico/listarFotos/$1';
$route['obras_tecnico/relatorioDiario/(:num)'] = 'obras_tecnico/relatorioDiario/$1';
$route['obras_tecnico/api/getAtividades'] = 'obras_tecnico/api_getAtividades';
$route['obras_tecnico/api/registrarAcao'] = 'obras_tecnico/api_registrarAcao';
$route['obras_tecnico/api/getObra/(:num)'] = 'obras_tecnico/api_getObra/$1';

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
$route['tecnicos/minhas_obras'] = 'tecnicos/minhas_obras';
$route['tecnicos/executar_obra/(:num)'] = 'tecnicos/executar_obra/$1';
$route['tecnicos/api/adicionar_comentario'] = 'tecnicos/api_adicionar_comentario';
$route['tecnicos/api/atualizar_etapa'] = 'tecnicos/api_atualizar_etapa';
$route['tecnicos/api/atualizar_status_etapa'] = 'tecnicos/api_atualizar_status_etapa';
$route['tecnicos/api/buscar_tarefas'] = 'tecnicos/api_buscar_tarefas';
$route['tecnicos/api/checkin_obra'] = 'tecnicos/api_checkin_obra';
$route['tecnicos/api/checkout_obra'] = 'tecnicos/api_checkout_obra';
$route['tecnicos/api/checkin_ativo_obra'] = 'tecnicos/api_checkin_ativo_obra';
$route['tecnicos/api/registrar_atividade_obra'] = 'tecnicos/api_registrar_atividade_obra';
$route['tecnicos/api/relatorio_diario_obra'] = 'tecnicos/api_relatorio_diario_obra';

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
