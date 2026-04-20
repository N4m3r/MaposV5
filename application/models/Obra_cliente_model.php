<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model de Cliente para Obras
 *
 * Gerencia notificações, mensagens, acessos e compartilhamentos
 */
class Obra_cliente_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Verificar se tabela existe
     */
    private function tabelaExiste($tabela)
    {
        try {
            return $this->db->table_exists($tabela);
        } catch (Exception $e) {
            return false;
        }
    }

    // ============================================
    // NOTIFICAÇÕES
    // ============================================

    /**
     * Criar notificação
     */
    public function criarNotificacao($obra_id, $cliente_id, $tipo, $titulo, $mensagem = '', $dados = [])
    {
        if (!$this->tabelaExiste('obra_cliente_notificacoes')) {
            return false;
        }

        try {
            $data = [
                'obra_id' => $obra_id,
                'cliente_id' => $cliente_id,
                'tipo' => $tipo,
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'url_destino' => $dados['url'] ?? null,
                'entidade_relacionada' => $dados['entidade'] ?? null,
                'entidade_id' => $dados['entidade_id'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obra_cliente_notificacoes', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            log_message('error', 'Erro ao criar notificação: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar notificações do cliente
     */
    public function getNotificacoes($cliente_id, $obra_id = null, $nao_lidas = false, $limit = 50)
    {
        if (!$this->tabelaExiste('obra_cliente_notificacoes')) {
            return [];
        }

        try {
            $this->db->select('obra_cliente_notificacoes.*, o.nome as obra_nome, o.codigo as obra_codigo');
            $this->db->from('obra_cliente_notificacoes');
            $this->db->join('obras o', 'o.id = obra_cliente_notificacoes.obra_id', 'left');
            $this->db->where('obra_cliente_notificacoes.cliente_id', $cliente_id);

            if ($obra_id) {
                $this->db->where('obra_cliente_notificacoes.obra_id', $obra_id);
            }

            if ($nao_lidas) {
                $this->db->where('obra_cliente_notificacoes.lida', 0);
            }

            $this->db->order_by('obra_cliente_notificacoes.created_at', 'DESC');
            $this->db->limit($limit);

            $query = $this->db->get();
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar notificações: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar notificações não lidas
     */
    public function countNaoLidas($cliente_id, $obra_id = null)
    {
        if (!$this->tabelaExiste('obra_cliente_notificacoes')) {
            return 0;
        }

        try {
            $this->db->where('cliente_id', $cliente_id);
            $this->db->where('lida', 0);

            if ($obra_id) {
                $this->db->where('obra_id', $obra_id);
            }

            return $this->db->count_all_results('obra_cliente_notificacoes');
        } catch (Exception $e) {
            log_message('error', 'Erro ao contar notificações: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Marcar notificação como lida
     */
    public function marcarLida($notificacao_id, $cliente_id)
    {
        if (!$this->tabelaExiste('obra_cliente_notificacoes')) {
            return false;
        }

        try {
            $this->db->where('id', $notificacao_id);
            $this->db->where('cliente_id', $cliente_id);
            return $this->db->update('obra_cliente_notificacoes', [
                'lida' => 1,
                'data_leitura' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erro ao marcar notificação como lida: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Marcar todas como lidas
     */
    public function marcarTodasLidas($cliente_id, $obra_id = null)
    {
        if (!$this->tabelaExiste('obra_cliente_notificacoes')) {
            return false;
        }

        try {
            $this->db->where('cliente_id', $cliente_id);
            $this->db->where('lida', 0);

            if ($obra_id) {
                $this->db->where('obra_id', $obra_id);
            }

            return $this->db->update('obra_cliente_notificacoes', [
                'lida' => 1,
                'data_leitura' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erro ao marcar todas notificações: ' . $e->getMessage());
            return false;
        }
    }

    // ============================================
    // MENSAGENS
    // ============================================

    /**
     * Enviar mensagem
     */
    public function enviarMensagem($obra_id, $remetente_tipo, $remetente_id, $mensagem, $dados = [])
    {
        if (!$this->tabelaExiste('obra_mensagens')) {
            return false;
        }

        try {
            $data = [
                'obra_id' => $obra_id,
                'remetente_tipo' => $remetente_tipo, // cliente, gestor, sistema
                'remetente_id' => $remetente_id,
                'mensagem' => $mensagem,
                'anexo_url' => $dados['anexo_url'] ?? null,
                'anexo_tipo' => $dados['anexo_tipo'] ?? null,
                'resposta_para' => $dados['resposta_para'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obra_mensagens', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            log_message('error', 'Erro ao enviar mensagem: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar mensagens da obra
     */
    public function getMensagens($obra_id, $limit = 50, $offset = 0)
    {
        if (!$this->tabelaExiste('obra_mensagens')) {
            return [];
        }

        try {
            $this->db->select('obra_mensagens.*,
                CASE
                    WHEN remetente_tipo = "cliente" THEN c.nomeCliente
                    WHEN remetente_tipo = "gestor" THEN u.nome
                    ELSE "Sistema"
                END as remetente_nome');
            $this->db->from('obra_mensagens');
            $this->db->join('clientes c', 'c.idClientes = obra_mensagens.remetente_id AND obra_mensagens.remetente_tipo = "cliente"', 'left');
            $this->db->join('usuarios u', 'u.idUsuarios = obra_mensagens.remetente_id AND obra_mensagens.remetente_tipo = "gestor"', 'left');
            $this->db->where('obra_mensagens.obra_id', $obra_id);
            $this->db->order_by('obra_mensagens.created_at', 'DESC');
            $this->db->limit($limit, $offset);

            $query = $this->db->get();
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar mensagens: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar mensagens não lidas
     */
    public function countMensagensNaoLidas($obra_id, $usuario_tipo, $usuario_id)
    {
        if (!$this->tabelaExiste('obra_mensagens')) {
            return 0;
        }

        try {
            $this->db->where('obra_id', $obra_id);
            $this->db->where('lida', 0);

            // Não contar mensagens do próprio usuário
            $this->db->where('(remetente_tipo != "' . $usuario_tipo . '" OR remetente_id != ' . $usuario_id . ')');

            return $this->db->count_all_results('obra_mensagens');
        } catch (Exception $e) {
            log_message('error', 'Erro ao contar mensagens: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Marcar mensagens como lidas
     */
    public function marcarMensagensLidas($obra_id, $usuario_tipo, $usuario_id)
    {
        if (!$this->tabelaExiste('obra_mensagens')) {
            return false;
        }

        try {
            $this->db->where('obra_id', $obra_id);
            $this->db->where('lida', 0);
            $this->db->where('(remetente_tipo != "' . $usuario_tipo . '" OR remetente_id != ' . $usuario_id . ')');

            return $this->db->update('obra_mensagens', [
                'lida' => 1,
                'data_leitura' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erro ao marcar mensagens lidas: ' . $e->getMessage());
            return false;
        }
    }

    // ============================================
    // COMPARTILHAMENTOS
    // ============================================

    /**
     * Criar link de compartilhamento
     */
    public function criarCompartilhamento($obra_id, $cliente_id, $tipo = 'fotos', $dados = [])
    {
        if (!$this->tabelaExiste('obra_compartilhamentos')) {
            return false;
        }

        try {
            $token = bin2hex(random_bytes(32));
            $expiracao = $dados['expiracao_dias'] ?? 30;

            $data = [
                'obra_id' => $obra_id,
                'cliente_id' => $cliente_id,
                'token' => $token,
                'tipo' => $tipo,
                'data_inicio' => $dados['data_inicio'] ?? null,
                'data_fim' => $dados['data_fim'] ?? null,
                'etapa_id' => $dados['etapa_id'] ?? null,
                'data_expiracao' => date('Y-m-d H:i:s', strtotime("+$expiracao days")),
                'acessos_permitidos' => $dados['acessos_permitidos'] ?? 0,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obra_compartilhamentos', $data);
            return $this->db->insert_id() ? $token : false;
        } catch (Exception $e) {
            log_message('error', 'Erro ao criar compartilhamento: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar compartilhamento por token
     */
    public function getCompartilhamentoByToken($token)
    {
        if (!$this->tabelaExiste('obra_compartilhamentos')) {
            return null;
        }

        try {
            $this->db->where('token', $token);
            $this->db->where('ativo', 1);
            $this->db->where('data_expiracao >', date('Y-m-d H:i:s'));
            $query = $this->db->get('obra_compartilhamentos');
            return $query ? $query->row() : null;
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar compartilhamento: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Registrar acesso ao compartilhamento
     */
    public function registrarAcessoCompartilhamento($token)
    {
        if (!$this->tabelaExiste('obra_compartilhamentos')) {
            return false;
        }

        try {
            $this->db->where('token', $token);
            $this->db->set('acessos_realizados', 'acessos_realizados + 1', FALSE);
            return $this->db->update('obra_compartilhamentos');
        } catch (Exception $e) {
            log_message('error', 'Erro ao registrar acesso: ' . $e->getMessage());
            return false;
        }
    }

    // ============================================
    // ACESSOS
    // ============================================

    /**
     * Registrar acesso do cliente
     */
    public function registrarAcesso($obra_id, $cliente_id, $pagina, $tempo = 0)
    {
        if (!$this->tabelaExiste('obra_cliente_acessos')) {
            return false;
        }

        try {
            $data = [
                'obra_id' => $obra_id,
                'cliente_id' => $cliente_id,
                'ip' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'pagina_acessada' => $pagina,
                'tempo_na_pagina' => $tempo,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obra_cliente_acessos', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            log_message('error', 'Erro ao registrar acesso: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Estatísticas de acesso
     */
    public function getEstatisticasAcesso($obra_id, $data_inicio = null, $data_fim = null)
    {
        if (!$this->tabelaExiste('obra_cliente_acessos')) {
            return [];
        }

        try {
            $this->db->where('obra_id', $obra_id);

            if ($data_inicio && $data_fim) {
                $this->db->where('created_at >=', $data_inicio . ' 00:00:00');
                $this->db->where('created_at <=', $data_fim . ' 23:59:59');
            }

            $total = $this->db->count_all_results('obra_cliente_acessos');

            $this->db->select('COUNT(DISTINCT cliente_id) as clientes_unicos');
            $this->db->where('obra_id', $obra_id);

            if ($data_inicio && $data_fim) {
                $this->db->where('created_at >=', $data_inicio . ' 00:00:00');
                $this->db->where('created_at <=', $data_fim . ' 23:59:59');
            }

            $query = $this->db->get('obra_cliente_acessos');
            $clientes_unicos = $query ? $query->row()->clientes_unicos : 0;

            return [
                'total_acessos' => $total,
                'clientes_unicos' => $clientes_unicos,
            ];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar estatísticas: ' . $e->getMessage());
            return [];
        }
    }
}
