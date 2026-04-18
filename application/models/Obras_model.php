<?php

/**
 * Model de Gestão de Obras
 */
class Obras_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Verificar se a tabela existe
     */
    private function tabelaExiste($tabela)
    {
        return $this->db->table_exists($tabela);
    }

    /**
     * Listar todas as obras
     */
    public function getAll($where = [], $limit = null)
    {
        if (!$this->tabelaExiste('obras')) {
            return [];
        }

        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by('data_criacao', 'DESC');

        if ($limit) {
            $this->db->limit($limit);
        }

        $query = $this->db->get('obras');

        if ($query === false) {
            return [];
        }

        return $query->result();
    }

    /**
     * Buscar obra por ID
     */
    public function getById($id)
    {
        if (!$this->tabelaExiste('obras')) {
            return null;
        }

        $this->db->where('id', $id);
        return $this->db->get('obras')->row();
    }

    /**
     * Adicionar obra
     */
    public function add($dados)
    {
        if (!$this->tabelaExiste('obras')) {
            return false;
        }

        $data = [
            'cliente_id' => $dados['cliente_id'],
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'endereco' => $dados['endereco'] ?? null,
            'responsavel_id' => $dados['responsavel_id'] ?? null,
            'data_inicio' => $dados['data_inicio'],
            'data_previsao_fim' => $dados['data_previsao_fim'] ?? null,
            'status' => $dados['status'] ?? 'planejamento',
            'etapa_atual' => 1,
            'data_criacao' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('obras', $data);
        return $this->db->insert_id();
    }

    /**
     * Atualizar obra
     */
    public function update($id, $dados)
    {
        if (!$this->tabelaExiste('obras')) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update('obras', $dados);
    }

    /**
     * Atualizar progresso da obra
     */
    public function atualizarProgresso($obra_id)
    {
        if (!$this->tabelaExiste('obra_etapas')) {
            return 0;
        }

        // Calcular progresso baseado nas etapas
        $this->db->where('obra_id', $obra_id);
        $total_etapas = $this->db->count_all_results('obra_etapas');

        $this->db->where('obra_id', $obra_id);
        $this->db->where('status', 'concluida');
        $etapas_concluidas = $this->db->count_all_results('obra_etapas');

        $progresso = $total_etapas > 0 ? round(($etapas_concluidas / $total_etapas) * 100) : 0;

        if ($this->tabelaExiste('obras')) {
            $this->db->where('id', $obra_id);
            $this->db->update('obras', ['percentual_concluido' => $progresso]);
        }

        return $progresso;
    }

    /**
     * Buscar etapas da obra
     */
    public function getEtapas($obra_id)
    {
        if (!$this->tabelaExiste('obra_etapas')) {
            return [];
        }

        $this->db->where('obra_id', $obra_id);
        $this->db->order_by('ordem', 'ASC');
        return $this->db->get('obra_etapas')->result();
    }

    /**
     * Adicionar etapa
     */
    public function adicionarEtapa($obra_id, $dados)
    {
        if (!$this->tabelaExiste('obra_etapas')) {
            return false;
        }

        $data = [
            'obra_id' => $obra_id,
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'ordem' => $dados['ordem'],
            'data_previsao' => $dados['data_previsao'] ?? null,
            'status' => 'pendente',
            'data_criacao' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('obra_etapas', $data);
        $etapa_id = $this->db->insert_id();

        // Atualizar total de etapas
        $this->atualizarTotalEtapas($obra_id);

        return $etapa_id;
    }

    /**
     * Atualizar total de etapas
     */
    private function atualizarTotalEtapas($obra_id)
    {
        if (!$this->tabelaExiste('obra_etapas') || !$this->tabelaExiste('obras')) {
            return;
        }

        $this->db->where('obra_id', $obra_id);
        $total = $this->db->count_all_results('obra_etapas');

        $this->db->where('id', $obra_id);
        $this->db->update('obras', ['total_etapas' => $total]);
    }

    /**
     * Buscar diário de obra
     */
    public function getDiario($obra_id, $data = null)
    {
        if (!$this->tabelaExiste('obra_diario')) {
            return [];
        }

        $this->db->where('obra_id', $obra_id);

        if ($data) {
            $this->db->where('data', $data);
        }

        $this->db->order_by('data', 'DESC');
        $this->db->order_by('hora_inicio', 'DESC');
        return $this->db->get('obra_diario')->result();
    }

    /**
     * Adicionar registro no diário
     */
    public function adicionarDiario($obra_id, $dados)
    {
        if (!$this->tabelaExiste('obra_diario')) {
            return false;
        }

        $data = [
            'obra_id' => $obra_id,
            'tecnico_id' => $dados['tecnico_id'],
            'data' => $dados['data'],
            'hora_inicio' => $dados['hora_inicio'],
            'hora_fim' => $dados['hora_fim'] ?? null,
            'atividade_realizada' => $dados['atividade_realizada'],
            'fotos_json' => isset($dados['fotos']) ? json_encode($dados['fotos']) : null,
            'observacoes' => $dados['observacoes'] ?? null,
            'etapa_id' => $dados['etapa_id'] ?? null,
            'clima' => $dados['clima'] ?? null,
        ];

        $this->db->insert('obra_diario', $data);
        return $this->db->insert_id();
    }

    /**
     * Equipe da obra
     */
    public function getEquipe($obra_id)
    {
        if (!$this->tabelaExiste('obra_equipe')) {
            return [];
        }

        $this->db->select('oe.*, u.nome as tecnico_nome, u.nivel_tecnico');
        $this->db->from('obra_equipe oe');
        $this->db->join('usuarios u', 'u.idUsuarios = oe.tecnico_id');
        $this->db->where('oe.obra_id', $obra_id);
        return $this->db->get()->result();
    }

    /**
     * Adicionar técnico à equipe
     */
    public function adicionarTecnicoEquipe($obra_id, $tecnico_id, $funcao = 'Técnico')
    {
        if (!$this->tabelaExiste('obra_equipe')) {
            return false;
        }

        $data = [
            'obra_id' => $obra_id,
            'tecnico_id' => $tecnico_id,
            'funcao' => $funcao,
            'data_entrada' => date('Y-m-d'),
        ];

        $this->db->insert('obra_equipe', $data);
        return $this->db->insert_id();
    }

    /**
     * Contar obras por status
     */
    public function countByStatus($status)
    {
        if (!$this->tabelaExiste('obras')) {
            return 0;
        }

        $this->db->where('status', $status);
        return $this->db->count_all_results('obras');
    }

    /**
     * Buscar obras ativas
     */
    public function getAtivas()
    {
        if (!$this->tabelaExiste('obras')) {
            return [];
        }

        $this->db->where_in('status', ['planejamento', 'em_andamento', 'paralisada']);
        return $this->db->get('obras')->result();
    }

    /**
     * Buscar cliente da obra
     */
    public function getCliente($obra_id)
    {
        if (!$this->tabelaExiste('obras')) {
            return null;
        }

        $this->db->select('c.*');
        $this->db->from('obras o');
        $this->db->join('clientes c', 'c.idClientes = o.cliente_id');
        $this->db->where('o.id', $obra_id);
        return $this->db->get()->row();
    }
}
