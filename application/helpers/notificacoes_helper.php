<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Helper de Notificações
 * Facilita o envio de notificações em qualquer parte do sistema
 */

/**
 * Envia notificação WhatsApp
 *
 * @param string $templateChave Chave do template
 * @param array $variaveis Variáveis para substituição
 * @param array $opcoes Opções adicionais (cliente_id, os_id, venda_id, etc)
 * @return array Resultado do envio
 */
function notificar_whatsapp($templateChave, $variaveis = [], $opcoes = [])
{
    $CI = &get_instance();
    $CI->load->model('notificacoes_config_model');
    $CI->load->model('notificacoes_templates_model');
    $CI->load->model('notificacoes_log_model');
    $CI->load->service('WhatsAppService');

    // Verifica se WhatsApp está ativo
    if (!$CI->notificacoes_config_model->isWhatsAppAtivo()) {
        return ['success' => false, 'error' => 'WhatsApp desativado'];
    }

    // Processa template
    $template = $CI->notificacoes_templates_model->processarTemplate($templateChave, $variaveis);

    if (!$template) {
        return ['success' => false, 'error' => 'Template não encontrado: ' . $templateChave];
    }

    // Obtém telefone
    $telefone = $opcoes['telefone'] ?? null;

    if (!$telefone && isset($opcoes['cliente_id'])) {
        $CI->load->model('clientes_model');
        $cliente = $CI->clientes_model->getById($opcoes['cliente_id']);
        $telefone = $cliente->celular ?? $cliente->telefone ?? null;
    }

    if (empty($telefone)) {
        return ['success' => false, 'error' => 'Telefone não informado'];
    }

    // Registra no log
    $logId = $CI->notificacoes_log_model->registrar([
        'cliente_id' => $opcoes['cliente_id'] ?? null,
        'telefone' => $telefone,
        'template_chave' => $templateChave,
        'mensagem' => $template['mensagem'],
        'mensagem_processada' => $template['mensagem'],
        'canal' => 'whatsapp',
        'os_id' => $opcoes['os_id'] ?? null,
        'venda_id' => $opcoes['venda_id'] ?? null,
        'status' => 'enviando',
    ]);

    // Envia mensagem
    $service = new WhatsAppService();
    $resultado = $service->enviarMensagem($telefone, $template['mensagem']);

    // Atualiza log
    if ($resultado['success']) {
        $CI->notificacoes_log_model->atualizarStatus($logId, 'enviado', [
            'external_id' => $resultado['message_id'] ?? null,
            'resposta_api' => json_encode($resultado['response'] ?? []),
            'provedor' => $CI->notificacoes_config_model->getProvedor(),
        ]);
    } else {
        $CI->notificacoes_log_model->registrarErro($logId, $resultado['error'] ?? 'Erro desconhecido');
    }

    return array_merge($resultado, ['log_id' => $logId]);
}

/**
 * Notifica criação de OS
 */
function notificar_os_criada($osId, $clienteId)
{
    $CI = &get_instance();
    $CI->load->model('os_model');
    $CI->load->model('clientes_model');
    $CI->load->model('mapos_model');

    $os = $CI->os_model->getById($osId);
    $cliente = $CI->clientes_model->getById($clienteId);
    $emitente = $CI->mapos_model->getEmitente();

    if (!$os || !$cliente) {
        return ['success' => false, 'error' => 'OS ou Cliente não encontrado'];
    }

    $variaveis = [
        'cliente_nome' => $cliente->nomeCliente,
        'os_id' => $osId,
        'equipamento' => $os->descricaoProduto,
        'defeito' => $os->defeito,
        'data_previsao' => date('d/m/Y', strtotime($os->dataFinal)),
        'link_consulta' => site_url('mine') . '?token=' . urlencode($cliente->email),
        'emitente_nome' => $emitente->nome,
        'emitente_telefone' => $emitente->telefone,
    ];

    return notificar_whatsapp('os_criada', $variaveis, [
        'cliente_id' => $clienteId,
        'os_id' => $osId,
        'telefone' => $cliente->celular,
    ]);
}

/**
 * Notifica atualização de status da OS
 */
function notificar_os_atualizada($osId, $clienteId, $statusAnterior = null)
{
    $CI = &get_instance();
    $CI->load->model('os_model');
    $CI->load->model('clientes_model');
    $CI->load->model('mapos_model');

    $os = $CI->os_model->getById($osId);
    $cliente = $CI->clientes_model->getById($clienteId);
    $emitente = $CI->mapos_model->getEmitente();

    if (!$os || !$cliente) {
        return ['success' => false, 'error' => 'OS ou Cliente não encontrado'];
    }

    // Define template baseado no status
    $templateChave = 'os_atualizada';
    if (strtolower($os->status) == 'finalizado') {
        $templateChave = 'os_pronta';
    } elseif (strtolower($os->status) == 'aguardando peças') {
        $templateChave = 'os_aguardando_peca';
    }

    // Calcula valor total
    $totais = $CI->os_model->valorTotalOS($osId);
    $valorTotal = ($totais['totalServico'] ?? 0) + ($totais['totalProdutos'] ?? 0);

    $variaveis = [
        'cliente_nome' => $cliente->nomeCliente,
        'os_id' => $osId,
        'equipamento' => $os->descricaoProduto,
        'status_atual' => $os->status,
        'status_anterior' => $statusAnterior ?? '',
        'valor_total' => number_format($valorTotal, 2, ',', '.'),
        'emitente_nome' => $emitente->nome,
        'emitente_telefone' => $emitente->telefone,
        'emitente_endereco' => $emitente->rua . ', ' . $emitente->numero . ' - ' . $emitente->bairro,
        'emitente_horario' => 'Seg-Sex: 08h às 18h',
    ];

    return notificar_whatsapp($templateChave, $variaveis, [
        'cliente_id' => $clienteId,
        'os_id' => $osId,
        'telefone' => $cliente->celular,
    ]);
}

/**
 * Notifica orçamento disponível
 */
function notificar_orcamento_disponivel($osId, $clienteId, $valorOrcamento)
{
    $CI = &get_instance();
    $CI->load->model('os_model');
    $CI->load->model('clientes_model');

    $os = $CI->os_model->getById($osId);
    $cliente = $CI->clientes_model->getById($clienteId);

    if (!$os || !$cliente) {
        return ['success' => false, 'error' => 'OS ou Cliente não encontrado'];
    }

    $variaveis = [
        'cliente_nome' => $cliente->nomeCliente,
        'os_id' => $osId,
        'equipamento' => $os->descricaoProduto,
        'valor_orcamento' => number_format($valorOrcamento, 2, ',', '.'),
        'tempo_estimado' => '3-5 dias úteis',
        'link_aprovar' => site_url('mine/aprovar/' . $osId) . '?token=' . urlencode($cliente->email),
        'link_recusar' => site_url('mine/recusar/' . $osId) . '?token=' . urlencode($cliente->email),
    ];

    return notificar_whatsapp('os_orcamento', $variaveis, [
        'cliente_id' => $clienteId,
        'os_id' => $osId,
        'telefone' => $cliente->celular,
    ]);
}

/**
 * Notifica venda realizada
 */
function notificar_venda_realizada($vendaId, $clienteId)
{
    $CI = &get_instance();
    $CI->load->model('vendas_model');
    $CI->load->model('clientes_model');

    $venda = $CI->vendas_model->getById($vendaId);
    $cliente = $CI->clientes_model->getById($clienteId);

    if (!$venda || !$cliente) {
        return ['success' => false, 'error' => 'Venda ou Cliente não encontrado'];
    }

    $variaveis = [
        'cliente_nome' => $cliente->nomeCliente,
        'venda_id' => $vendaId,
        'valor_total' => number_format($venda->valorTotal ?? 0, 2, ',', '.'),
        'data_venda' => date('d/m/Y', strtotime($venda->data)),
    ];

    return notificar_whatsapp('venda_realizada', $variaveis, [
        'cliente_id' => $clienteId,
        'venda_id' => $vendaId,
        'telefone' => $cliente->celular,
    ]);
}

/**
 * Notifica cobrança gerada
 */
function notificar_cobranca_gerada($cobrancaId, $clienteId)
{
    $CI = &get_instance();
    $CI->load->model('cobrancas_model');
    $CI->load->model('clientes_model');

    $cobranca = $CI->cobrancas_model->getById($cobrancaId);
    $cliente = $CI->clientes_model->getById($clienteId);

    if (!$cobranca || !$cliente) {
        return ['success' => false, 'error' => 'Cobrança ou Cliente não encontrado'];
    }

    $referente = $cobranca->os_id ? 'OS #' . $cobranca->os_id : 'Venda #' . $cobranca->vendas_id;

    $variaveis = [
        'cliente_nome' => $cliente->nomeCliente,
        'referente' => $referente,
        'valor' => number_format($cobranca->valor, 2, ',', '.'),
        'data_vencimento' => date('d/m/Y', strtotime($cobranca->data_vencimento)),
        'link_pagamento' => $cobranca->link_pagamento ?? 'Link não disponível',
    ];

    return notificar_whatsapp('cobranca_gerada', $variaveis, [
        'cliente_id' => $clienteId,
        'telefone' => $cliente->celular,
    ]);
}

/**
 * Verifica status do WhatsApp
 */
function whatsapp_status()
{
    $CI = &get_instance();
    $CI->load->service('WhatsAppService');

    $service = new WhatsAppService();
    return $service->verificarConexao();
}

/**
 * Formata número de telefone para exibição
 */
function formatar_telefone($numero)
{
    return WhatsAppService::formatarNumero($numero);
}

/**
 * Agenda notificação para envio futuro
 */
function agendar_notificacao($templateChave, $variaveis, $dataHora, $opcoes = [])
{
    $CI = &get_instance();
    $CI->load->model('notificacoes_agendadas_model');

    return $CI->notificacoes_agendadas_model->agendar([
        'template_chave' => $templateChave,
        'variaveis' => $variaveis,
        'data_hora_envio' => $dataHora,
        'cliente_id' => $opcoes['cliente_id'] ?? null,
        'os_id' => $opcoes['os_id'] ?? null,
        'venda_id' => $opcoes['venda_id'] ?? null,
        'telefone' => $opcoes['telefone'] ?? null,
        'origem' => $opcoes['origem'] ?? 'sistema',
    ]);
}
