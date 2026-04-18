<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Helper de Permissões para Área do Cliente
 * Verifica se o usuário logado tem permissão para uma ação específica
 */

if (!function_exists('clienteHasPermission')) {
    /**
     * Verifica se o usuário cliente tem uma permissão específica
     *
     * @param string $chave Chave da permissão
     * @return bool
     */
    function clienteHasPermission($chave)
    {
        $CI =& get_instance();

        // Se for login tradicional (não usuário do portal), retorna true para manter compatibilidade
        if (!$CI->session->userdata('usuario_cliente_id')) {
            return true;
        }

        // Se for admin do cliente (tipo_acesso), verificar
        $tipoAcesso = $CI->session->userdata('tipo_acesso');

        // Carregar model
        $CI->load->model('usuarios_cliente_model');

        $usuarioId = $CI->session->userdata('usuario_cliente_id');
        return $CI->usuarios_cliente_model->hasPermissao($usuarioId, $chave);
    }
}

if (!function_exists('clienteCheckPermission')) {
    /**
     * Verifica permissão e redireciona se não tiver
     *
     * @param string $chave
     * @param string $redirectUrl
     */
    function clienteCheckPermission($chave, $redirectUrl = 'mine/painel')
    {
        if (!clienteHasPermission($chave)) {
            $CI =& get_instance();
            $CI->session->set_flashdata('error', 'Você não tem permissão para acessar esta funcionalidade.');
            redirect($redirectUrl);
        }
    }
}

if (!function_exists('clienteGetPermissoes')) {
    /**
     * Retorna todas as permissões do usuário logado
     *
     * @return array
     */
    function clienteGetPermissoes()
    {
        $CI =& get_instance();

        // Se for login tradicional
        if (!$CI->session->userdata('usuario_cliente_id')) {
            return [];
        }

        $usuarioId = $CI->session->userdata('usuario_cliente_id');
        $permissoes = $CI->session->userdata('usuario_cliente_permissoes');

        if (!$permissoes) {
            $CI->load->model('usuarios_cliente_model');
            $permissoes = $CI->usuarios_cliente_model->getAllPermissoes($usuarioId);
            $CI->session->set_userdata('usuario_cliente_permissoes', $permissoes);
        }

        return $permissoes;
    }
}

if (!function_exists('isUsuarioCliente')) {
    /**
     * Verifica se o usuário logado é um usuário do portal do cliente
     *
     * @return bool
     */
    function isUsuarioCliente()
    {
        $CI =& get_instance();
        return $CI->session->userdata('tipo_acesso') === 'usuario_cliente';
    }
}

if (!function_exists('getUsuarioClienteId')) {
    /**
     * Retorna o ID do usuário cliente logado
     *
     * @return int|null
     */
    function getUsuarioClienteId()
    {
        $CI =& get_instance();
        return $CI->session->userdata('usuario_cliente_id');
    }
}

if (!function_exists('getUsuarioClienteCnpjs')) {
    /**
     * Retorna os CNPJs vinculados ao usuário cliente
     *
     * @return array
     */
    function getUsuarioClienteCnpjs()
    {
        $CI =& get_instance();

        if (!isUsuarioCliente()) {
            return [];
        }

        $cnpjs = $CI->session->userdata('usuario_cliente_cnpjs');

        if (!$cnpjs) {
            $CI->load->model('usuarios_cliente_model');
            $usuarioId = $CI->session->userdata('usuario_cliente_id');
            $cnpjs = $CI->usuarios_cliente_model->getCnpjs($usuarioId);
            $CI->session->set_userdata('usuario_cliente_cnpjs', $cnpjs);
        }

        return $cnpjs ?: [];
    }
}
