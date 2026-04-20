<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model de Check-ins de Obra
 *
 * Gerencia check-ins, check-outs, pausas
 */
class Obra_checkins_model extends CI_Model
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

    /**
     * Buscar check-in por ID
     */
    public function getById($id)
    {
        if (!$this->tabelaExiste('obra_checkins')) {
            return null;
        }

        try {
            $this->db->where('id', $id);
            $query = $this->db->get('obra_checkins');
            return $query ? $query->row() : null;
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar check-in: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Listar check-ins por atividade
     */
    public function getByAtividade($atividade_id)
    {
        if (!$this->tabelaExiste('obra_checkins')) {
            return [];
        }

        try {
            $this->db->select('obra_checkins.*, u.nome as tecnico_nome');
            $this->db->from('obra_checkins');
            $this->db->join('usuarios u', 'u.idUsuarios = obra_checkins.tecnico_id', 'left');
            $this->db->where('obra_checkins.atividade_id', $atividade_id);
            $this->db->order_by('obra_checkins.created_at', 'ASC');
            $query = $this->db->get();
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao listar check-ins: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar check-ins por técnico
     */
    public function getByTecnico($tecnico_id, $data = null)
    {
        if (!$this->tabelaExiste('obra_checkins')) {
            return [];
        }

        try {
            $this->db->where('tecnico_id', $tecnico_id);

            if ($data) {
                $this->db->where('DATE(created_at)', $data);
            }

            $this->db->order_by('created_at', 'DESC');
            $query = $this->db->get('obra_checkins');
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar check-ins do técnico: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Adicionar check-in
     */
    public function add($dados)
    {
        if (!$this->tabelaExiste('obra_checkins')) {
            return false;
        }

        try {
            $data = [
                'atividade_id' => $dados['atividade_id'],
                'tecnico_id' => $dados['tecnico_id'],
                'tipo' => $dados['tipo'], // checkin, checkout, pausa, retorno
                'latitude' => $dados['latitude'] ?? null,
                'longitude' => $dados['longitude'] ?? null,
                'endereco_detectado' => $dados['endereco'] ?? null,
                'foto_url' => $dados['foto'] ?? null,
                'observacao' => $dados['observacao'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obra_checkins', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            log_message('error', 'Erro ao adicionar check-in: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Registrar check-in
     */
    public function registrarCheckin($atividade_id, $tecnico_id, $dados = [])
    {
        return $this->add(array_merge($dados, [
            'atividade_id' => $atividade_id,
            'tecnico_id' => $tecnico_id,
            'tipo' => 'checkin'
        ]));
    }

    /**
     * Registrar check-out
     */
    public function registrarCheckout($atividade_id, $tecnico_id, $dados = [])
    {
        return $this->add(array_merge($dados, [
            'atividade_id' => $atividade_id,
            'tecnico_id' => $tecnico_id,
            'tipo' => 'checkout'
        ]));
    }

    /**
     * Registrar pausa
     */
    public function registrarPausa($atividade_id, $tecnico_id, $dados = [])
    {
        return $this->add(array_merge($dados, [
            'atividade_id' => $atividade_id,
            'tecnico_id' => $tecnico_id,
            'tipo' => 'pausa'
        ]));
    }

    /**
     * Registrar retorno
     */
    public function registrarRetorno($atividade_id, $tecnico_id, $dados = [])
    {
        return $this->add(array_merge($dados, [
            'atividade_id' => $atividade_id,
            'tecnico_id' => $tecnico_id,
            'tipo' => 'retorno'
        ]));
    }

    /**
     * Buscar último check-in do técnico
     */
    public function getUltimoCheckin($atividade_id, $tecnico_id)
    {
        if (!$this->tabelaExiste('obra_checkins')) {
            return null;
        }

        try {
            $this->db->where('atividade_id', $atividade_id);
            $this->db->where('tecnico_id', $tecnico_id);
            $this->db->where_in('tipo', ['checkin', 'retorno']);
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get('obra_checkins');
            return $query ? $query->row() : null;
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar último check-in: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar se técnico está checked-in
     */
    public function estaCheckedIn($atividade_id, $tecnico_id)
    {
        if (!$this->tabelaExiste('obra_checkins')) {
            return false;
        }

        try {
            $this->db->where('atividade_id', $atividade_id);
            $this->db->where('tecnico_id', $tecnico_id);
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get('obra_checkins');

            if (!$query || !$query->num_rows()) {
                return false;
            }

            $ultimo = $query->row();
            return in_array($ultimo->tipo, ['checkin', 'retorno']);
        } catch (Exception $e) {
            log_message('error', 'Erro ao verificar check-in: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcular tempo trabalhado
     */
    public function calcularTempoTrabalhado($atividade_id)
    {
        if (!$this->tabelaExiste('obra_checkins')) {
            return 0;
        }

        try {
            $checkins = $this->getByAtividade($atividade_id);
            $total_minutos = 0;
            $ultimo_checkin = null;

            foreach ($checkins as $check) {
                if (in_array($check->tipo, ['checkin', 'retorno'])) {
                    $ultimo_checkin = strtotime($check->created_at);
                } elseif (in_array($check->tipo, ['checkout', 'pausa']) && $ultimo_checkin) {
                    $total_minutos += (strtotime($check->created_at) - $ultimo_checkin) / 60;
                    $ultimo_checkin = null;
                }
            }

            // Se ainda estiver checked in
            if ($ultimo_checkin) {
                $total_minutos += (time() - $ultimo_checkin) / 60;
            }

            return round($total_minutos / 60, 2); // Retorna em horas
        } catch (Exception $e) {
            log_message('error', 'Erro ao calcular tempo: ' . $e->getMessage());
            return 0;
        }
    }
}
