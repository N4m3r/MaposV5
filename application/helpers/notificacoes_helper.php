<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('notificar_whatsapp')) {
    function notificar_whatsapp($templateChave, $variaveis = [], $opcoes = [])
    {
        $ci = &get_instance();
        $ci->load->model('notificacoes_config_model');
        $ci->load->model('notificacoes_templates_model');
        $ci->load->model('notificacoes_log_model');

        if (!$ci->notificacoes_config_model->isWhatsAppAtivo()) {
            return ['success' => false, 'error' => 'WhatsApp desativado'];
        }

        $processado = $ci->notificacoes_templates_model->processarTemplate($templateChave, $variaveis);
        if (!$processado['success']) {
            return $processado;
        }

        $telefone = $opcoes['telefone'] ?? '';
        if (empty($telefone) && !empty($opcoes['cliente_id'])) {
            $ci->db->where('idClientes', $opcoes['cliente_id']);
            $cliente = $ci->db->get('clientes')->row();
            if ($cliente) {
                $telefone = $cliente->celular ?? '';
            }
        }

        if (empty($telefone)) {
            return ['success' => false, 'error' => 'Telefone nao encontrado'];
        }

        $logId = $ci->notificacoes_log_model->registrar([
            'template_chave' => $templateChave,
            'cliente_id' => $opcoes['cliente_id'] ?? null,
            'telefone' => $telefone,
            'mensagem' => $processado['mensagem'],
            'canal' => $processado['canal'] ?? 'whatsapp',
        ]);

        $ci->load->library('WhatsAppService');
        $service = new WhatsAppService();
        $resultado = $service->enviarMensagem($telefone, $processado['mensagem']);

        if ($resultado['success']) {
            $ci->notificacoes_log_model->atualizarStatus($logId, 'enviado');
        } else {
            $ci->notificacoes_log_model->registrarErro($logId, $resultado['error'] ?? 'Erro desconhecido');
        }

        return $resultado;
    }
}

if (!function_exists('notificar_os_criada')) {
    function notificar_os_criada($osId, $clienteId)
    {
        $ci = &get_instance();
        $ci->load->model('Os_model');
        $os = $ci->Os_model->getById($osId);

        return notificar_whatsapp('os_criada', [
            'os_id' => $osId,
            'cliente_nome' => $os->nomeCliente ?? '',
            'os_status' => 'Aberto',
        ], ['cliente_id' => $clienteId]);
    }
}

if (!function_exists('notificar_os_atualizada')) {
    function notificar_os_atualizada($osId, $clienteId, $statusAnterior = '')
    {
        $ci = &get_instance();
        $ci->load->model('Os_model');
        $os = $ci->Os_model->getById($osId);

        $novoStatus = $os->status ?? '';

        $chaveMap = [
            'Finalizado' => 'os_pronta',
            'Aguardando Pecas' => 'os_aguardando_peca',
        ];
        $chave = $chaveMap[$novoStatus] ?? 'os_atualizada';

        return notificar_whatsapp($chave, [
            'os_id' => $osId,
            'cliente_nome' => $os->nomeCliente ?? '',
            'os_status' => $novoStatus,
            'status_anterior' => $statusAnterior,
        ], ['cliente_id' => $clienteId]);
    }
}

if (!function_exists('notificar_venda_realizada')) {
    function notificar_venda_realizada($vendaId, $clienteId)
    {
        return notificar_whatsapp('venda_realizada', [
            'venda_id' => $vendaId,
        ], ['cliente_id' => $clienteId]);
    }
}

if (!function_exists('notificar_cobranca_gerada')) {
    function notificar_cobranca_gerada($cobrancaId, $clienteId)
    {
        return notificar_whatsapp('cobranca_gerada', [
            'cobranca_id' => $cobrancaId,
        ], ['cliente_id' => $clienteId]);
    }
}

if (!function_exists('whatsapp_status')) {
    function whatsapp_status()
    {
        $ci = &get_instance();
        $ci->load->library('WhatsAppService');
        $service = new WhatsAppService();
        return $service->verificarConexao();
    }
}

if (!function_exists('formatar_telefone')) {
    function formatar_telefone($numero)
    {
        return WhatsAppService::formatarNumero($numero);
    }
}