<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model para log de interacoes do WhatsApp Agent
 */
class Whatsapp_log_interacoes_model extends CI_Model
{
    protected $table = 'whatsapp_log_interacoes';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Registra uma interacao
     */
    public function registrar($dados)
    {
        $insert = [
            'numero_telefone' => $dados['numero'] ?? null,
            'tipo_mensagem' => $dados['tipo'] ?? 'texto',
            'direcao' => $dados['direcao'] ?? 'entrada',
            'conteudo' => $dados['conteudo'] ?? null,
            'intencao_detectada' => $dados['intencao'] ?? null,
            'os_id' => $dados['os_id'] ?? null,
            'status' => $dados['status'] ?? 'recebido',
        ];

        return $this->db->insert($this->table, $insert);
    }

    /**
     * Lista interacoes por numero
     */
    public function listarPorNumero($numero, $limite = 50)
    {
        return $this->db->where('numero_telefone', $numero)
            ->order_by('created_at', 'DESC')
            ->limit($limite)
            ->get($this->table)
            ->result();
    }

    /**
     * Lista interacoes do dia
     */
    public function listarHoje($limite = 100)
    {
        return $this->db->where('DATE(created_at)', date('Y-m-d'))
            ->order_by('created_at', 'DESC')
            ->limit($limite)
            ->get($this->table)
            ->result();
    }

    /**
     * Conta interacoes do dia
     */
    public function contarHoje()
    {
        return $this->db->where('DATE(created_at)', date('Y-m-d'))
            ->count_all_results($this->table);
    }

    /**
     * Lista todas as interacoes com paginacao
     */
    public function listar($limite = 25, $offset = 0)
    {
        return $this->db->order_by('created_at', 'DESC')
            ->limit($limite, $offset)
            ->get($this->table)
            ->result();
    }

    /**
     * Conta total
     */
    public function contar()
    {
        return $this->db->count_all_results($this->table);
    }
}
