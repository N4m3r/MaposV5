<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model: Usuários da Área do Cliente
 * Gerencia usuários do portal do cliente com permissões e vínculos por CNPJ
 */
class Usuarios_cliente_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ==================== CRUD BÁSICO ====================

    /**
     * Obter usuário por ID
     */
    public function getById($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('usuarios_cliente');
        return $query ? $query->row() : null;
    }

    /**
     * Obter usuário por email
     */
    public function getByEmail($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('usuarios_cliente');
        return $query ? $query->row() : null;
    }

    /**
     * Listar todos os usuários
     */
    public function getAll($where = [], $limit = 0, $offset = 0)
    {
        $this->db->select('uc.*, c.nomeCliente as cliente_nome, (SELECT COUNT(*) FROM usuarios_cliente_cnpjs ucj WHERE ucj.usuario_cliente_id = uc.id) as total_cnpjs');
        $this->db->from('usuarios_cliente uc');
        $this->db->join('clientes c', 'c.idClientes = uc.cliente_id', 'left');

        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        $this->db->order_by('uc.nome', 'ASC');

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    /**
     * Contar usuários
     */
    public function count($where = [])
    {
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        return $this->db->count_all_results('usuarios_cliente');
    }

    /**
     * Adicionar usuário
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert('usuarios_cliente', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Atualizar usuário
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update('usuarios_cliente', $data);
    }

    /**
     * Excluir usuário
     */
    public function delete($id)
    {
        // Remove vínculos primeiro
        $this->db->where('usuario_cliente_id', $id);
        $this->db->delete('usuarios_cliente_cnpjs');

        $this->db->where('usuario_cliente_id', $id);
        $this->db->delete('usuarios_cliente_permissoes');

        // Remove usuário
        $this->db->where('id', $id);
        return $this->db->delete('usuarios_cliente');
    }

    // ==================== CNPJs VINCULADOS ====================

    /**
     * Adicionar CNPJ ao usuário
     */
    public function addCnpj($usuario_id, $cnpj, $razao_social = null)
    {
        // Remove formatação do CNPJ para padronizar
        $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpj);

        // Se CNPJ vazio, retorna erro
        if (empty($cnpjLimpo)) {
            return false;
        }

        $cnpjFormatado = $this->formatarCnpj($cnpjLimpo);

        // Verifica se já existe
        $this->db->where('usuario_cliente_id', $usuario_id);
        $this->db->where('cnpj', $cnpjFormatado);
        $query = $this->db->get('usuarios_cliente_cnpjs');

        if ($query && $query->num_rows() > 0) {
            return true; // Já existe
        }

        // Tenta inserir
        $result = $this->db->insert('usuarios_cliente_cnpjs', [
            'usuario_cliente_id' => $usuario_id,
            'cnpj' => $cnpjFormatado,
            'razao_social' => $razao_social,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Se falhou, loga o erro
        if (!$result) {
            log_message('error', 'Erro ao inserir CNPJ: ' . $this->db->error()['message']);
        }

        return $result;
    }

    /**
     * Remover CNPJ do usuário
     * @param int $usuario_id - ID do usuário cliente
     * @param mixed $cnpj_id - ID do registro ou CNPJ formatado
     * @return bool
     */
    public function removeCnpj($usuario_id, $cnpj_id)
    {
        // Se for numérico, assume que é o ID do registro
        if (is_numeric($cnpj_id)) {
            $this->db->where('id', $cnpj_id);
        } else {
            // Se for string, assume que é o CNPJ
            $this->db->where('cnpj', $cnpj_id);
        }
        $this->db->where('usuario_cliente_id', $usuario_id);
        return $this->db->delete('usuarios_cliente_cnpjs');
    }

    /**
     * Remover CNPJ do usuário pelo CNPJ (string)
     * @param int $usuario_id - ID do usuário cliente
     * @param string $cnpj - CNPJ formatado (00.000.000/0000-00)
     * @return bool
     */
    public function removeCnpjByCnpj($usuario_id, $cnpj)
    {
        $this->db->where('cnpj', $cnpj);
        $this->db->where('usuario_cliente_id', $usuario_id);
        return $this->db->delete('usuarios_cliente_cnpjs');
    }

    /**
     * Listar CNPJs do usuário
     */
    public function getCnpjs($usuario_id)
    {
        $this->db->where('usuario_cliente_id', $usuario_id);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('usuarios_cliente_cnpjs');
        return $query ? $query->result() : [];
    }

    /**
     * Verificar se usuário tem acesso ao CNPJ
     */
    public function hasCnpjAccess($usuario_id, $cnpj)
    {
        $cnpjFormatado = $this->formatarCnpj(preg_replace('/[^0-9]/', '', $cnpj));

        $this->db->where('usuario_cliente_id', $usuario_id);
        $this->db->where('cnpj', $cnpjFormatado);
        return $this->db->get('usuarios_cliente_cnpjs')->num_rows() > 0;
    }

    // ==================== PERMISSÕES ====================

    /**
     * Definir permissão/configuração
     */
    public function setPermissao($usuario_id, $chave, $valor)
    {
        // Verifica se já existe
        $this->db->where('usuario_cliente_id', $usuario_id);
        $this->db->where('chave', $chave);
        $exists = $this->db->get('usuarios_cliente_permissoes')->row();

        if ($exists) {
            $this->db->where('id', $exists->id);
            return $this->db->update('usuarios_cliente_permissoes', [
                'valor' => is_array($valor) || is_object($valor) ? json_encode($valor) : $valor,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->db->insert('usuarios_cliente_permissoes', [
                'usuario_cliente_id' => $usuario_id,
                'chave' => $chave,
                'valor' => is_array($valor) || is_object($valor) ? json_encode($valor) : $valor,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Obter permissão/configuração
     */
    public function getPermissao($usuario_id, $chave, $default = null)
    {
        $this->db->where('usuario_cliente_id', $usuario_id);
        $this->db->where('chave', $chave);
        $query = $this->db->get('usuarios_cliente_permissoes');
        $result = $query ? $query->row() : null;

        if ($result) {
            $valor = $result->valor;
            // Tenta decodificar JSON
            $decoded = json_decode($valor, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $valor;
        }

        return $default;
    }

    /**
     * Obter todas as permissões do usuário
     */
    public function getAllPermissoes($usuario_id)
    {
        $this->db->where('usuario_cliente_id', $usuario_id);
        $query = $this->db->get('usuarios_cliente_permissoes');
        $results = $query ? $query->result() : [];

        $permissoes = [];
        foreach ($results as $r) {
            $valor = $r->valor;
            $decoded = json_decode($valor, true);
            $permissoes[$r->chave] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $valor;
        }

        return $permissoes;
    }

    /**
     * Remover permissão
     */
    public function removePermissao($usuario_id, $chave)
    {
        $this->db->where('usuario_cliente_id', $usuario_id);
        $this->db->where('chave', $chave);
        return $this->db->delete('usuarios_cliente_permissoes');
    }

    // ==================== PERMISSÕES PADRÃO ====================

    /**
     * Permissões padrão para novos usuários
     */
    public function getPermissoesPadrao()
    {
        return [
            'visualizar_os' => true,
            'visualizar_os_apenas_vinculadas' => true, // Se false, vê todas do CNPJ
            'visualizar_detalhes_os' => true,
            'visualizar_produtos_os' => true,
            'visualizar_servicos_os' => true,
            'visualizar_anexos_os' => true,
            'visualizar_documentos_fiscais' => true,
            'visualizar_financeiro' => true,
            'visualizar_historico_pagamentos' => true,
            'visualizar_cobrancas' => true,
            'visualizar_boletos' => true,
            'visualizar_notas_fiscais' => true,
            'visualizar_obras' => true,
            'visualizar_detalhes_obra' => true,
            'visualizar_compras' => true,
            'imprimir_os' => true,
            'editar_perfil' => true,
            'solicitar_orcamento' => true,
            'aprovar_os' => false, // Aprovar/reprovar OS
            'receber_notificacoes' => true,
            'acesso_mobile' => true,
        ];
    }

    /**
     * Aplicar permissões padrão ao usuário
     */
    public function aplicarPermissoesPadrao($usuario_id)
    {
        $permissoes = $this->getPermissoesPadrao();
        foreach ($permissoes as $chave => $valor) {
            $this->setPermissao($usuario_id, $chave, $valor);
        }
    }

    /**
     * Verificar se usuário tem permissão
     */
    public function hasPermissao($usuario_id, $chave)
    {
        $permissao = $this->getPermissao($usuario_id, $chave, false);
        return $permissao === true || $permissao === '1' || $permissao === 1;
    }

    // ==================== OS VINCULADAS ====================

    /**
     * Buscar OS vinculadas aos CNPJs do usuário
     */
    public function getOsByCnpjs($usuario_id, $filtros = [])
    {
        // Busca CNPJs do usuário
        $cnpjs = $this->getCnpjs($usuario_id);
        if (empty($cnpjs)) {
            log_message('debug', 'getOsByCnpjs: Usuário ' . $usuario_id . ' não tem CNPJs vinculados');
            return [];
        }

        // Prepara lista de CNPJs (com e sem formatação para garantir match)
        $cnpjsLimpos = [];
        $cnpjsFormatados = [];
        foreach ($cnpjs as $c) {
            $limpo = preg_replace('/[^0-9]/', '', $c->cnpj);
            $formatado = $this->formatarCnpj($limpo);
            $cnpjsLimpos[] = $limpo;
            $cnpjsFormatados[] = $formatado;
        }

        log_message('debug', 'getOsByCnpjs: Buscando OS para CNPJs: ' . implode(', ', $cnpjsLimpos));

        // Busca clientes com esses CNPJs (tenta com e sem formatação)
        $this->db->select('idClientes, documento');
        $this->db->group_start();
        $this->db->where_in('documento', $cnpjsLimpos);
        $this->db->or_where_in('documento', $cnpjsFormatados);
        $this->db->group_end();
        $query = $this->db->get('clientes');

        if (!$query) {
            log_message('error', 'getOsByCnpjs: Erro ao buscar clientes: ' . print_r($this->db->error(), true));
            return [];
        }

        $clientes = $query->result();

        if (empty($clientes)) {
            log_message('debug', 'getOsByCnpjs: Nenhum cliente encontrado para os CNPJs fornecidos');
            return [];
        }

        $clientesIds = array_map(function($c) { return $c->idClientes; }, $clientes);
        log_message('debug', 'getOsByCnpjs: Clientes encontrados: ' . implode(', ', $clientesIds));

        // Busca OS desses clientes
        $this->db->select('os.*, clientes.nomeCliente, clientes.documento');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where_in('os.clientes_id', $clientesIds);

        // Aplicar filtros
        if (!empty($filtros['status'])) {
            $this->db->where('os.status', $filtros['status']);
        }

        if (!empty($filtros['data_inicio'])) {
            $this->db->where('os.dataInicial >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $this->db->where('os.dataInicial <=', $filtros['data_fim']);
        }

        if (!empty($filtros['limit'])) {
            $this->db->limit($filtros['limit']);
        }

        $this->db->order_by('os.idOs', 'DESC');
        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'getOsByCnpjs: Erro ao buscar OS: ' . print_r($this->db->error(), true));
            return [];
        }

        $result = $query->result();
        log_message('debug', 'getOsByCnpjs: Total de OS encontradas: ' . count($result));

        return $result;
    }

    /**
     * Contar OS por status
     */
    public function countOsByStatus($usuario_id)
    {
        $os = $this->getOsByCnpjs($usuario_id);

        $counts = [
            'total' => count($os),
            'Aberto' => 0,
            'Orçamento' => 0,
            'Negociação' => 0,
            'Aprovado' => 0,
            'Em Andamento' => 0,
            'Aguardando Peças' => 0,
            'Finalizado' => 0,
            'Faturado' => 0,
            'Cancelado' => 0,
        ];

        foreach ($os as $o) {
            if (isset($counts[$o->status])) {
                $counts[$o->status]++;
            }
        }

        return $counts;
    }

    // ==================== AUTENTICAÇÃO ====================

    /**
     * Login
     */
    public function login($email, $senha)
    {
        $this->db->where('email', $email);
        $this->db->where('ativo', 1);
        $query = $this->db->get('usuarios_cliente');
        $usuario = $query ? $query->row() : null;

        if ($usuario && password_verify($senha, $usuario->senha)) {
            // Atualiza último acesso
            $this->db->where('id', $usuario->id);
            $this->db->update('usuarios_cliente', ['ultimo_acesso' => date('Y-m-d H:i:s')]);

            return $usuario;
        }

        return false;
    }

    /**
     * Gerar token de reset
     */
    public function gerarTokenReset($email)
    {
        $usuario = $this->getByEmail($email);
        if (!$usuario) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->db->where('id', $usuario->id);
        $this->db->update('usuarios_cliente', [
            'token_reset' => $token,
            'token_expira' => $expira
        ]);

        return $token;
    }

    /**
     * Validar token de reset
     */
    public function validarTokenReset($token)
    {
        $this->db->where('token_reset', $token);
        $this->db->where('token_expira >', date('Y-m-d H:i:s'));
        $this->db->where('ativo', 1);
        $query = $this->db->get('usuarios_cliente');
        return $query ? $query->row() : null;
    }

    /**
     * Resetar senha
     */
    public function resetarSenha($token, $novaSenha)
    {
        $usuario = $this->validarTokenReset($token);
        if (!$usuario) {
            return false;
        }

        $this->db->where('id', $usuario->id);
        return $this->db->update('usuarios_cliente', [
            'senha' => password_hash($novaSenha, PASSWORD_DEFAULT),
            'token_reset' => null,
            'token_expira' => null
        ]);
    }

    // ==================== HELPERS ====================

    /**
     * Formatar CNPJ
     */
    private function formatarCnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
}
