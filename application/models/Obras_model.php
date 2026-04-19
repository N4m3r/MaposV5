<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
        try {
            return $this->db->table_exists($tabela);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Listar todas as obras
     */
    public function getAll($where = [], $limit = null)
    {
        if (!$this->tabelaExiste('obras')) {
            return [];
        }

        try {
            $this->db->select('o.*, c.nomeCliente as cliente_nome, c.documento as cliente_documento');
            $this->db->from('obras o');
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');

            if (!empty($where)) {
                $this->db->where($where);
            }

            $this->db->order_by('o.created_at', 'DESC');

            if ($limit) {
                $this->db->limit($limit);
            }

            $query = $this->db->get();

            if ($query === false || !is_object($query)) {
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Buscar obra por ID
     */
    public function getById($id)
    {
        if (!$this->tabelaExiste('obras')) {
            return null;
        }

        try {
            $this->db->select('o.*, c.nomeCliente as cliente_nome, c.documento as cliente_documento');
            $this->db->from('obras o');
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
            $this->db->where('o.id', $id);
            $query = $this->db->get();

            if ($query === false || !is_object($query)) {
                return null;
            }

            return $query->row();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Adicionar obra
     */
    public function add($dados)
    {
        if (!$this->tabelaExiste('obras')) {
            return false;
        }

        try {
            // Se não tiver código, gerar um
            if (empty($dados['codigo'])) {
                $dados['codigo'] = 'OB-' . date('Y') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            }

            $data = [
                'codigo' => $dados['codigo'],
                'nome' => $dados['nome'],
                'cliente_id' => $dados['cliente_id'],
                'tipo_obra' => $dados['tipo_obra'] ?? 'Outro',
                'endereco' => $dados['endereco'] ?? null,
                'data_inicio_contrato' => $dados['data_inicio'] ?? date('Y-m-d'),
                'data_fim_prevista' => $dados['data_previsao_fim'] ?? null,
                'observacoes' => $dados['descricao'] ?? null,
                'status' => $dados['status'] ?? 'Prospeccao',
                'percentual_concluido' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obras', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Atualizar obra
     */
    public function update($id, $dados)
    {
        if (!$this->tabelaExiste('obras')) {
            return false;
        }

        try {
            $this->db->where('id', $id);
            return $this->db->update('obras', $dados);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Atualizar progresso da obra
     */
    public function atualizarProgresso($obra_id)
    {
        if (!$this->tabelaExiste('obra_etapas')) {
            return 0;
        }

        try {
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
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Buscar etapas da obra
     */
    public function getEtapas($obra_id)
    {
        if (!$this->tabelaExiste('obra_etapas')) {
            return [];
        }

        try {
            $this->db->where('obra_id', $obra_id);
            $this->db->order_by('numero_etapa', 'ASC');
            $query = $this->db->get('obra_etapas');

            if ($query === false || !is_object($query)) {
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Adicionar etapa
     */
    public function adicionarEtapa($obra_id, $dados)
    {
        if (!$this->tabelaExiste('obra_etapas')) {
            return false;
        }

        try {
            $data = [
                'obra_id' => $obra_id,
                'numero_etapa' => $dados['numero_etapa'] ?? 1,
                'nome' => $dados['nome'],
                'descricao' => $dados['descricao'] ?? null,
                'especialidade' => $dados['especialidade'] ?? null,
                'data_inicio_prevista' => $dados['data_inicio_prevista'] ?? null,
                'data_fim_prevista' => $dados['data_fim_prevista'] ?? null,
                'status' => 'pendente',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obra_etapas', $data);
            $etapa_id = $this->db->insert_id();

            // Atualizar total de etapas
            $this->atualizarTotalEtapas($obra_id);

            return $etapa_id;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Atualizar total de etapas
     */
    private function atualizarTotalEtapas($obra_id)
    {
        if (!$this->tabelaExiste('obra_etapas') || !$this->tabelaExiste('obras')) {
            return;
        }

        try {
            $this->db->where('obra_id', $obra_id);
            $total = $this->db->count_all_results('obra_etapas');

            $this->db->where('id', $obra_id);
            $this->db->update('obras', [
                'total_etapas' => $total,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            // Silenciar erro
        }
    }

    /**
     * Buscar diário de obra
     */
    public function getDiario($obra_id, $data = null)
    {
        if (!$this->tabelaExiste('obra_diario')) {
            return [];
        }

        try {
            $this->db->where('obra_id', $obra_id);

            if ($data) {
                $this->db->where('data', $data);
            }

            $this->db->order_by('data', 'DESC');
            $this->db->order_by('hora_inicio', 'DESC');
            $query = $this->db->get('obra_diario');

            if ($query === false || !is_object($query)) {
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Adicionar registro no diário
     */
    public function adicionarDiario($obra_id, $dados)
    {
        if (!$this->tabelaExiste('obra_diario')) {
            return false;
        }

        try {
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
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obra_diario', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Equipe da obra
     */
    public function getEquipe($obra_id)
    {
        if (!$this->tabelaExiste('obra_equipe')) {
            $this->criarTabelaEquipe();
            return [];
        }

        try {
            $this->db->select('oe.*, u.nome as tecnico_nome, u.nivel_tecnico');
            $this->db->from('obra_equipe oe');
            $this->db->join('usuarios u', 'u.idUsuarios = oe.tecnico_id', 'left');
            $this->db->where('oe.obra_id', $obra_id);
            $this->db->where('oe.ativo', 1);
            $query = $this->db->get();

            if ($query === false || !is_object($query)) {
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Adicionar técnico à equipe
     */
    public function adicionarTecnicoEquipe($obra_id, $tecnico_id, $funcao = 'Técnico')
    {
        if (!$this->tabelaExiste('obra_equipe')) {
            $this->criarTabelaEquipe();
        }

        try {
            $data = [
                'obra_id' => $obra_id,
                'tecnico_id' => $tecnico_id,
                'funcao' => $funcao,
                'data_entrada' => date('Y-m-d'),
                'ativo' => 1,
            ];

            $this->db->insert('obra_equipe', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Contar obras por status
     */
    public function countByStatus($status)
    {
        if (!$this->tabelaExiste('obras')) {
            return 0;
        }

        try {
            $this->db->where('status', $status);
            return $this->db->count_all_results('obras');
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Buscar obras ativas
     */
    public function getAtivas()
    {
        if (!$this->tabelaExiste('obras')) {
            return [];
        }

        try {
            $this->db->where_in('status', ['Contratada', 'EmExecucao', 'Paralisada']);
            $query = $this->db->get('obras');

            if ($query === false || !is_object($query)) {
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Buscar obras por cliente
     */
    public function getByCliente($cliente_id, $perpage = 10, $start = 0)
    {
        if (!$this->tabelaExiste('obras')) {
            return [];
        }

        try {
            $this->db->where('cliente_id', $cliente_id);
            $this->db->where('ativo', 1);
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit($perpage, $start);
            $query = $this->db->get('obras');

            if ($query === false || !is_object($query)) {
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Contar obras por cliente
     */
    public function countByCliente($cliente_id)
    {
        if (!$this->tabelaExiste('obras')) {
            return 0;
        }

        try {
            $this->db->where('cliente_id', $cliente_id);
            $this->db->where('ativo', 1);
            $query = $this->db->select('COUNT(*) as total')->get('obras');

            if ($query && $query->num_rows() > 0) {
                return (int) $query->row()->total;
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao contar obras por cliente: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Buscar cliente da obra
     */
    public function getCliente($obra_id)
    {
        if (!$this->tabelaExiste('obras') || !$this->tabelaExiste('clientes')) {
            return null;
        }

        try {
            $this->db->select('c.*');
            $this->db->from('obras o');
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
            $this->db->where('o.id', $obra_id);
            $query = $this->db->get();

            if ($query === false || !is_object($query)) {
                return null;
            }

            return $query->row();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Excluir obra (soft delete)
     */
    public function delete($id)
    {
        if (!$this->tabelaExiste('obras')) {
            return false;
        }

        try {
            $this->db->where('id', $id);
            return $this->db->update('obras', [
                'ativo' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Alocar técnico à obra (wrapper para compatibilidade)
     */
    public function alocarTecnico($dados)
    {
        return $this->adicionarTecnicoEquipe(
            $dados['obra_id'],
            $dados['tecnico_id'],
            $dados['funcao'] ?? 'Técnico'
        );
    }

    /**
     * Adicionar material à obra
     */
    public function adicionarMaterial($dados)
    {
        if (!$this->tabelaExiste('obra_materiais')) {
            // Criar tabela se não existir
            $this->criarTabelaMateriais();
        }

        try {
            $data = [
                'obra_id' => $dados['obra_id'],
                'nome' => $dados['nome'],
                'quantidade' => $dados['quantidade'] ?? 1,
                'quantidade_usada' => 0,
                'observacao' => $dados['observacao'] ?? null,
                'status' => 'pendente',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obra_materiais', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Criar tabela de materiais se não existir
     */
    private function criarTabelaMateriais()
    {
        $sql = "CREATE TABLE IF NOT EXISTS obra_materiais (
            id INT AUTO_INCREMENT PRIMARY KEY,
            obra_id INT NOT NULL,
            nome VARCHAR(255) NOT NULL,
            quantidade INT DEFAULT 1,
            quantidade_usada INT DEFAULT 0,
            observacao TEXT,
            status VARCHAR(50) DEFAULT 'pendente',
            created_at DATETIME,
            updated_at DATETIME,
            INDEX idx_obra_id (obra_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->db->query($sql);
    }

    /**
     * Criar tabela de equipe se não existir
     */
    private function criarTabelaEquipe()
    {
        $sql = "CREATE TABLE IF NOT EXISTS obra_equipe (
            id INT AUTO_INCREMENT PRIMARY KEY,
            obra_id INT NOT NULL,
            tecnico_id INT NOT NULL,
            funcao VARCHAR(50) DEFAULT 'Técnico',
            data_entrada DATE NOT NULL,
            data_saida DATE DEFAULT NULL,
            ativo TINYINT(1) DEFAULT 1,
            observacoes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_obra_tecnico (obra_id, tecnico_id, data_entrada),
            INDEX idx_tecnico (tecnico_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->db->query($sql);
    }

    /**
     * Listar materiais da obra
     */
    public function getMateriais($obra_id)
    {
        if (!$this->tabelaExiste('obra_materiais')) {
            return [];
        }

        try {
            $this->db->where('obra_id', $obra_id);
            $query = $this->db->get('obra_materiais');

            if ($query === false || !is_object($query)) {
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Buscar OS vinculadas à obra
     */
    public function getOsVinculadas($obra_id)
    {
        $this->db->select('os.idOs, os.dataInicial, os.dataFinal, os.status, os.garantia, os.valorTotal, c.nomeCliente, u.nome as responsavel');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->join('usuarios u', 'u.idUsuarios = os.usuarios_id', 'left');
        $this->db->where('os.obra_id', $obra_id);
        $this->db->order_by('os.dataInicial', 'DESC');

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    /**
     * Buscar OS disponíveis para vincular (do mesmo cliente, não vinculadas)
     */
    public function getOsDisponiveis($cliente_id, $obra_id = null)
    {
        $this->db->select('os.idOs, os.dataInicial, os.dataFinal, os.status, c.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->where('os.clientes_id', $cliente_id);
        $this->db->where('(os.obra_id IS NULL OR os.obra_id = 0)');

        // Se for atualizar uma obra específica, excluir as já vinculadas a ela
        if ($obra_id) {
            $this->db->where('os.obra_id !=', $obra_id);
        }

        $this->db->order_by('os.dataInicial', 'DESC');
        $this->db->limit(50);

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }
}
