<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes_log_model extends CI_Model
{
    private $tableName = 'notificacoes_log';

    public function __construct()
    {
        parent::__construct();
    }

    public function registrar($dados)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        $dados['status'] = $dados['status'] ?? 'pendente';
        $dados['canal'] = $dados['canal'] ?? 'whatsapp';
        $dados['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->tableName, $dados);
        return $this->db->insert_id();
    }

    public function atualizarStatus($id, $status, $dadosAdicionais = [])
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        $update = ['status' => $status];

        $timestamps = [
            'enviado' => 'sent_at',
            'entregue' => 'delivered_at',
            'lido' => 'read_at',
        ];

        if (isset($timestamps[$status])) {
            $update[$timestamps[$status]] = date('Y-m-d H:i:s');
        }

        if (!empty($dadosAdicionais)) {
            $update = array_merge($update, $dadosAdicionais);
        }

        $this->db->where('id', $id);
        return $this->db->update($this->tableName, $update);
    }

    public function incrementarTentativas($id)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        $this->db->set('tentativas', 'tentativas + 1', false);
        $this->db->where('id', $id);
        return $this->db->update($this->tableName);
    }

    public function registrarErro($id, $erro)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->tableName, [
            'status' => 'falha',
            'erro' => $erro,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function listar($filtros = [], $limite = 20, $offset = 0)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return [];
        }

        if (!empty($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }
        if (!empty($filtros['canal'])) {
            $this->db->where('canal', $filtros['canal']);
        }
        if (!empty($filtros['data_inicio'])) {
            $this->db->where('created_at >=', $filtros['data_inicio'] . ' 00:00:00');
        }
        if (!empty($filtros['data_fim'])) {
            $this->db->where('created_at <=', $filtros['data_fim'] . ' 23:59:59');
        }
        if (!empty($filtros['busca'])) {
            $this->db->like('mensagem', $filtros['busca']);
        }

        $this->db->order_by('created_at', 'desc');
        $this->db->limit($limite, $offset);
        return $this->db->get($this->tableName)->result();
    }

    public function contar($filtros = [])
    {
        if (!$this->db->table_exists($this->tableName)) {
            return 0;
        }

        if (!empty($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }
        if (!empty($filtros['canal'])) {
            $this->db->where('canal', $filtros['canal']);
        }
        if (!empty($filtros['data_inicio'])) {
            $this->db->where('created_at >=', $filtros['data_inicio'] . ' 00:00:00');
        }
        if (!empty($filtros['data_fim'])) {
            $this->db->where('created_at <=', $filtros['data_fim'] . ' 23:59:59');
        }

        return $this->db->count_all_results($this->tableName);
    }

    public function getEstatisticas($periodoDias = 30)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return (object) [
                'total' => 0,
                'enviados' => 0,
                'falhas' => 0,
                'pendentes' => 0,
                'hoje' => 0,
                'sucesso_percentual' => 0,
            ];
        }

        $dataInicio = date('Y-m-d', strtotime("-{$periodoDias} days"));

        $total = $this->db->where('created_at >=', $dataInicio)->count_all_results($this->tableName);
        $enviados = $this->db->where('created_at >=', $dataInicio)->where('status', 'enviado')->count_all_results($this->tableName);
        $falhas = $this->db->where('created_at >=', $dataInicio)->where('status', 'falha')->count_all_results($this->tableName);
        $pendentes = $this->db->where('created_at >=', $dataInicio)->where('status', 'pendente')->count_all_results($this->tableName);
        $hoje = $this->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results($this->tableName);

        return (object) [
            'total' => $total,
            'enviados' => $enviados,
            'falhas' => $falhas,
            'pendentes' => $pendentes,
            'hoje' => $hoje,
            'sucesso_percentual' => $total > 0 ? round(($enviados / $total) * 100, 1) : 0,
        ];
    }

    public function limparAntigos($dias = 90)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return 0;
        }

        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        $this->db->where('status', 'lido');
        $this->db->where('created_at <', $dataLimite);
        $this->db->delete($this->tableName);
        return $this->db->affected_rows();
    }

    public function getPendentes($limite = 50)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return [];
        }

        $this->db->where_in('status', ['pendente', 'falha']);
        $this->db->where('tentativas <', 3);
        $this->db->order_by('created_at', 'asc');
        $this->db->limit($limite);
        return $this->db->get($this->tableName)->result();
    }

    public function getById($id)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return null;
        }

        $this->db->where('id', $id);
        return $this->db->get($this->tableName)->row();
    }
}