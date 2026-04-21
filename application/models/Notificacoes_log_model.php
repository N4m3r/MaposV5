<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes_log_model extends CI_Model
{
    protected $table = 'notificacoes_log';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Registra uma nova notificação no log
     */
    public function registrar($dados)
    {
        $padrao = [
            'cliente_id' => null,
            'telefone' => null,
            'email' => null,
            'template_chave' => null,
            'assunto' => null,
            'mensagem' => null,
            'mensagem_processada' => null,
            'status' => 'pendente',
            'canal' => 'whatsapp',
            'provedor' => null,
            'external_id' => null,
            'resposta_api' => null,
            'erro' => null,
            'os_id' => null,
            'venda_id' => null,
            'tentativas' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'sent_at' => null,
            'delivered_at' => null,
            'read_at' => null,
        ];

        $dados = array_merge($padrao, $dados);

        $this->db->insert($this->table, $dados);
        return $this->db->insert_id();
    }

    /**
     * Atualiza status da notificação
     */
    public function atualizarStatus($id, $status, $dadosAdicionais = [])
    {
        $update = ['status' => $status];

        switch ($status) {
            case 'enviado':
                $update['sent_at'] = date('Y-m-d H:i:s');
                break;
            case 'entregue':
                $update['delivered_at'] = date('Y-m-d H:i:s');
                break;
            case 'lido':
                $update['read_at'] = date('Y-m-d H:i:s');
                break;
        }

        $update = array_merge($update, $dadosAdicionais);

        $this->db->where('id', $id);
        return $this->db->update($this->table, $update);
    }

    /**
     * Incrementa tentativas de envio
     */
    public function incrementarTentativas($id)
    {
        $this->db->where('id', $id);
        $this->db->set('tentativas', 'tentativas + 1', false);
        return $this->db->update($this->table);
    }

    /**
     * Registra erro
     */
    public function registrarErro($id, $erro)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, [
            'status' => 'falha',
            'erro' => $erro,
        ]);
    }

    /**
     * Lista logs com filtros
     */
    public function listar($filtros = [], $limite = 50, $offset = 0)
    {
        $this->aplicarFiltros($filtros);

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limite, $offset);

        return $this->db->get($this->table)->result();
    }

    /**
     * Conta total de registros com filtros
     */
    public function contar($filtros = [])
    {
        $this->aplicarFiltros($filtros);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Aplica filtros na query
     */
    private function aplicarFiltros($filtros)
    {
        if (isset($filtros['cliente_id']) && $filtros['cliente_id']) {
            $this->db->where('cliente_id', $filtros['cliente_id']);
        }

        if (isset($filtros['status']) && $filtros['status']) {
            $this->db->where('status', $filtros['status']);
        }

        if (isset($filtros['canal']) && $filtros['canal']) {
            $this->db->where('canal', $filtros['canal']);
        }

        if (isset($filtros['template_chave']) && $filtros['template_chave']) {
            $this->db->where('template_chave', $filtros['template_chave']);
        }

        if (isset($filtros['os_id']) && $filtros['os_id']) {
            $this->db->where('os_id', $filtros['os_id']);
        }

        if (isset($filtros['data_inicio']) && $filtros['data_inicio']) {
            $this->db->where('created_at >=', $filtros['data_inicio'] . ' 00:00:00');
        }

        if (isset($filtros['data_fim']) && $filtros['data_fim']) {
            $this->db->where('created_at <=', $filtros['data_fim'] . ' 23:59:59');
        }

        if (isset($filtros['busca']) && $filtros['busca']) {
            $this->db->group_start();
            $this->db->like('telefone', $filtros['busca']);
            $this->db->or_like('email', $filtros['busca']);
            $this->db->or_like('mensagem_processada', $filtros['busca']);
            $this->db->group_end();
        }
    }

    /**
     * Obtém estatísticas de envio
     */
    public function getEstatisticas($periodoDias = 30)
    {
        $dataInicio = date('Y-m-d', strtotime("-{$periodoDias} days"));

        // Estatísticas por status
        $this->db->select('status, COUNT(*) as total');
        $this->db->where('created_at >=', $dataInicio . ' 00:00:00');
        $this->db->group_by('status');
        $porStatus = $this->db->get($this->table)->result();

        // Estatísticas por canal
        $this->db->select('canal, COUNT(*) as total');
        $this->db->where('created_at >=', $dataInicio . ' 00:00:00');
        $this->db->group_by('canal');
        $porCanal = $this->db->get($this->table)->result();

        // Total enviado hoje
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where_in('status', ['enviado', 'entregue', 'lido']);
        $totalHoje = $this->db->count_all_results($this->table);

        // Taxa de sucesso
        $this->db->where('created_at >=', $dataInicio . ' 00:00:00');
        $totalPeriodo = $this->db->count_all_results($this->table);

        $this->db->where('created_at >=', $dataInicio . ' 00:00:00');
        $this->db->where_in('status', ['enviado', 'entregue', 'lido']);
        $totalSucesso = $this->db->count_all_results($this->table);

        $taxaSucesso = $totalPeriodo > 0 ? round(($totalSucesso / $totalPeriodo) * 100, 2) : 0;

        return [
            'por_status' => $porStatus,
            'por_canal' => $porCanal,
            'total_hoje' => $totalHoje,
            'total_periodo' => $totalPeriodo,
            'taxa_sucesso' => $taxaSucesso,
        ];
    }

    /**
     * Limpa logs antigos
     */
    public function limparAntigos($dias = 90)
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));

        $this->db->where('created_at <', $dataLimite);
        $this->db->where('status', 'lido'); // Apenas mensagens lidas

        return $this->db->delete($this->table);
    }

    /**
     * Obtém notificações pendentes para reprocessamento
     */
    public function getPendentes($limite = 10)
    {
        $this->db->where('status', 'pendente');
        $this->db->or_where('status', 'falha');
        $this->db->where('tentativas <', 3);
        $this->db->order_by('created_at', 'ASC');
        $this->db->limit($limite);

        return $this->db->get($this->table)->result();
    }

    /**
     * Obtém detalhes de uma notificação
     */
    public function getById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }
}
