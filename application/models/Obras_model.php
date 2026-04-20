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
     * Retorna a classe CSS para o label de status
     */
    public function getStatusLabelClass($status)
    {
        $classes = [
            'Prospeccao' => 'label-default',
            'Em Andamento' => 'label-info',
            'Paralisada' => 'label-warning',
            'Concluida' => 'label-success',
            'Concluída' => 'label-success',
            'Cancelada' => 'label-inverse',
        ];
        return $classes[$status] ?? 'label-default';
    }

    /**
     * Listar todas as obras
     */
    public function getAll($where = [], $limit = null, $offset = null)
    {
        if (!$this->tabelaExiste('obras')) {
            return [];
        }

        try {
            $this->db->select('o.*, c.nomeCliente as cliente_nome, c.documento as cliente_documento');
            $this->db->from('obras o');
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');

            // Filtrar apenas obras ativas (não excluídas)
            $this->db->where('o.ativo', 1);

            if (!empty($where)) {
                $this->db->where($where);
            }

            $this->db->order_by('o.created_at', 'DESC');

            if ($limit) {
                $this->db->limit($limit, $offset ?: 0);
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
            $this->db->where('o.ativo', 1);
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
            // Calcular progresso baseado nas etapas usando métodos seguros
            $total_etapas = $this->contarRegistros('obra_etapas', ['obra_id' => $obra_id]);

            $etapas_concluidas = 0;
            if ($this->tabelaExiste('obra_etapas')) {
                $sql = "SELECT COUNT(*) as total FROM obra_etapas WHERE obra_id = ? AND status = ?";
                $query = $this->db->query($sql, [$obra_id, 'Concluida']);
                if ($query && $query->num_rows() > 0) {
                    $etapas_concluidas = (int) $query->row()->total;
                }
            }

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

            // Só filtrar por ativo se a coluna existir
            if ($this->db->field_exists('ativo', 'obra_etapas')) {
                $this->db->where('ativo', 1);
            }

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
                'status' => 'NaoIniciada',
            ];

            // Adicionar colunas opcionais se existirem
            if ($this->db->field_exists('ativo', 'obra_etapas')) {
                $data['ativo'] = 1;
            }
            if ($this->db->field_exists('visivel_cliente', 'obra_etapas')) {
                $data['visivel_cliente'] = 1;
            }
            if ($this->db->field_exists('created_at', 'obra_etapas')) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            $this->db->insert('obra_etapas', $data);
            $etapa_id = $this->db->insert_id();

            // Atualizar total de etapas
            $this->atualizarTotalEtapas($obra_id);

            return $etapa_id;
        } catch (Exception $e) {
            log_message('error', 'Erro ao adicionar etapa: ' . $e->getMessage());
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
            $total = $this->contarRegistros('obra_etapas', ['obra_id' => $obra_id]);

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
     * Remover técnico da equipe (soft delete)
     */
    public function removerTecnicoEquipe($equipe_id)
    {
        try {
            $this->db->where('id', $equipe_id);
            $this->db->update('obra_equipe', ['ativo' => 0, 'data_saida' => date('Y-m-d')]);
            return $this->db->affected_rows() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Verificar se técnico está na equipe da obra
     */
    public function tecnicoNaEquipe($obra_id, $tecnico_id)
    {
        if (!$this->tabelaExiste('obra_equipe')) {
            return false;
        }

        try {
            $this->db->where('obra_id', $obra_id);
            $this->db->where('tecnico_id', $tecnico_id);
            $this->db->where('ativo', 1);
            $query = $this->db->get('obra_equipe');

            return $query && $query->num_rows() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Contar obras por status
     */
    public function countByStatus($status)
    {
        return $this->contarRegistros('obras', ['status' => $status]);
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
     * Buscar obras por documento do cliente (CPF/CNPJ)
     * Busca tanto com formatação quanto sem
     */
    public function getByClienteDocumento($documento, $perpage = 10, $start = 0)
    {
        if (!$this->tabelaExiste('obras') || !$this->tabelaExiste('clientes')) {
            return [];
        }

        try {
            // Limpar documento para busca
            $documentoLimpo = preg_replace('/[^0-9]/', '', $documento);

            $this->db->select('o.*');
            $this->db->from('obras o');
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
            $this->db->where('o.ativo', 1);

            // Buscar por documento com ou sem formatação
            $this->db->group_start();
            $this->db->where('c.documento', $documento);
            $this->db->or_where('c.documento', $documentoLimpo);
            $this->db->group_end();

            $this->db->order_by('o.created_at', 'DESC');
            $this->db->limit($perpage, $start);
            $query = $this->db->get();

            if ($query === false || !is_object($query)) {
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar obras por documento: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar obras por documento do cliente (CPF/CNPJ)
     */
    public function countByClienteDocumento($documento)
    {
        if (!$this->tabelaExiste('obras') || !$this->tabelaExiste('clientes')) {
            return 0;
        }

        try {
            // Limpar documento para busca
            $documentoLimpo = preg_replace('/[^0-9]/', '', $documento);

            $this->db->from('obras o');
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
            $this->db->where('o.ativo', 1);

            // Buscar por documento com ou sem formatação
            $this->db->group_start();
            $this->db->where('c.documento', $documento);
            $this->db->or_where('c.documento', $documentoLimpo);
            $this->db->group_end();

            $query = $this->db->select('COUNT(*) as total')->get();

            if ($query && $query->num_rows() > 0) {
                return (int) $query->row()->total;
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao contar obras por documento: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Buscar obras por cliente combinando ID e documento
     * Útil quando o cliente pode ter múltiplos registros com o mesmo CNPJ
     */
    public function getByClienteCompleto($cliente_id, $documento, $perpage = 10, $start = 0)
    {
        if (!$this->tabelaExiste('obras') || !$this->tabelaExiste('clientes')) {
            return [];
        }

        try {
            // Limpar documento para busca
            $documentoLimpo = preg_replace('/[^0-9]/', '', $documento);

            $this->db->select('o.*, c.nomeCliente as cliente_nome, c.documento as cliente_documento');
            $this->db->from('obras o');
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
            $this->db->where('o.ativo', 1);

            // Buscar por ID do cliente OU por documento
            $this->db->group_start();
            $this->db->where('o.cliente_id', $cliente_id);

            if (!empty($documentoLimpo)) {
                $this->db->or_group_start();
                $this->db->where('c.documento', $documento);
                $this->db->or_where('c.documento', $documentoLimpo);
                $this->db->group_end();
            }

            $this->db->group_end();

            $this->db->order_by('o.created_at', 'DESC');
            $this->db->limit($perpage, $start);
            $query = $this->db->get();

            if ($query === false || !is_object($query)) {
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar obras completas: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar obras por cliente combinando ID e documento
     */
    public function countByClienteCompleto($cliente_id, $documento)
    {
        if (!$this->tabelaExiste('obras') || !$this->tabelaExiste('clientes')) {
            return 0;
        }

        try {
            // Limpar documento para busca
            $documentoLimpo = preg_replace('/[^0-9]/', '', $documento);

            $this->db->from('obras o');
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
            $this->db->where('o.ativo', 1);

            // Buscar por ID do cliente OU por documento
            $this->db->group_start();
            $this->db->where('o.cliente_id', $cliente_id);

            if (!empty($documentoLimpo)) {
                $this->db->or_group_start();
                $this->db->where('c.documento', $documento);
                $this->db->or_where('c.documento', $documentoLimpo);
                $this->db->group_end();
            }

            $this->db->group_end();

            $query = $this->db->select('COUNT(*) as total')->get();

            if ($query && $query->num_rows() > 0) {
                return (int) $query->row()->total;
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao contar obras completas: ' . $e->getMessage());
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

    // ============================================
    // NOVOS MÉTODOS PARA SISTEMA DE OBRAS COMPLETO
    // ============================================

    /**
     * Buscar obra com dados completos (incluindo cliente e gestor)
     */
    public function getByIdCompleto($id)
    {
        if (!$this->tabelaExiste('obras')) {
            return null;
        }

        try {
            $this->db->select('o.*, c.nomeCliente as cliente_nome, c.documento as cliente_documento,
                c.email as cliente_email, c.telefone as cliente_telefone,
                u.nome as gestor_nome, u.telefone as gestor_telefone');
            $this->db->from('obras o');
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
            $this->db->join('usuarios u', 'u.idUsuarios = o.gestor_id', 'left');
            $this->db->where('o.id', $id);
            $this->db->where('o.ativo', 1);
            $query = $this->db->get();

            return $query ? $query->row() : null;
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar obra completa: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calcular progresso geral da obra
     */
    public function calcularProgresso($obra_id)
    {
        if (!$this->tabelaExiste('obra_etapas')) {
            return 0;
        }

        try {
            $this->db->select_avg('percentual_concluido', 'progresso');
            $this->db->where('obra_id', $obra_id);
            $query = $this->db->get('obra_etapas');

            $result = $query ? $query->row() : null;
            return $result ? round($result->progresso) : 0;
        } catch (Exception $e) {
            log_message('error', 'Erro ao calcular progresso: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Buscar etapas da obra com estatísticas
     */
    public function getEtapasComEstatisticas($obra_id)
    {
        if (!$this->tabelaExiste('obra_etapas')) {
            return [];
        }

        try {
            $this->db->select('oe.*,
                COUNT(DISTINCT oa.id) as total_atividades,
                COUNT(DISTINCT CASE WHEN oa.status = "concluida" THEN oa.id END) as atividades_concluidas');
            $this->db->from('obra_etapas oe');

            // Só fazer join com atividades se a tabela existir
            if ($this->tabelaExiste('obra_atividades')) {
                $this->db->join('obra_atividades oa', 'oa.etapa_id = oe.id AND oa.obra_id = oe.obra_id' . ($this->db->field_exists('ativo', 'obra_atividades') ? ' AND oa.ativo = 1' : ''), 'left');
            }

            $this->db->where('oe.obra_id', $obra_id);

            // Só filtrar por ativo se a coluna existir
            if ($this->db->field_exists('ativo', 'obra_etapas')) {
                $this->db->where('oe.ativo', 1);
            }

            $this->db->group_by('oe.id');
            $this->db->order_by('oe.numero_etapa', 'ASC');
            $query = $this->db->get();

            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar etapas: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar registros de forma segura
     */
    private function contarRegistros($tabela, $where = [])
    {
        if (!$this->tabelaExiste($tabela)) {
            return 0;
        }

        try {
            $sql = "SELECT COUNT(*) as total FROM {$tabela}";
            $params = [];

            if (!empty($where)) {
                $conditions = [];
                foreach ($where as $col => $val) {
                    $conditions[] = "{$col} = ?";
                    $params[] = $val;
                }
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $query = $this->db->query($sql, $params);
            if ($query && $query->num_rows() > 0) {
                return (int) $query->row()->total;
            }
        } catch (Exception $e) {
            log_message('error', "Erro ao contar em {$tabela}: " . $e->getMessage());
        }

        return 0;
    }

    /**
     * Resumo da obra para dashboard
     */
    public function getResumo($obra_id)
    {
        if (!$this->tabelaExiste('obras')) {
            return null;
        }

        try {
            $obra = $this->getByIdCompleto($obra_id);
            if (!$obra) {
                return null;
            }

            // Contar etapas usando método seguro
            $total_etapas = $this->contarRegistros('obra_etapas', ['obra_id' => $obra_id]);
            $etapas_concluidas = 0;
            if ($this->tabelaExiste('obra_etapas')) {
                try {
                    $sql = "SELECT COUNT(*) as total FROM obra_etapas WHERE obra_id = ? AND status = ?";
                    $query = $this->db->query($sql, [$obra_id, 'Concluida']);
                    if ($query && $query->num_rows() > 0) {
                        $etapas_concluidas = (int) $query->row()->total;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Erro ao contar etapas concluídas: ' . $e->getMessage());
                }
            }

            // Contar atividades
            $total_atividades = $this->contarRegistros('obra_atividades', ['obra_id' => $obra_id]);
            $atividades_hoje = $this->contarRegistros('obra_atividades', [
                'obra_id' => $obra_id,
                'data_atividade' => date('Y-m-d')
            ]);

            // Calcular dias restantes
            $dias_restantes = null;
            if ($obra->data_fim_prevista) {
                $hoje = new DateTime();
                $previsto = new DateTime($obra->data_fim_prevista);
                $dias_restantes = $hoje->diff($previsto, false)->format('%r%a');
            }

            return [
                'obra' => $obra,
                'total_etapas' => $total_etapas,
                'etapas_concluidas' => $etapas_concluidas,
                'total_atividades' => $total_atividades,
                'atividades_hoje' => $atividades_hoje,
                'dias_restantes' => $dias_restantes,
            ];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar resumo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar se cliente tem acesso à obra
     */
    public function clienteTemAcesso($obra_id, $cliente_id)
    {
        if (!$this->tabelaExiste('obras')) {
            return false;
        }

        try {
            $this->db->where('id', $obra_id);
            $sql = "SELECT COUNT(*) as total FROM obras WHERE id = ? AND cliente_id = ? AND visivel_cliente = 1 AND ativo = 1";
            $query = $this->db->query($sql, [$obra_id, $cliente_id]);
            return ($query && $query->num_rows() > 0) ? $query->row()->total > 0 : false;
        } catch (Exception $e) {
            log_message('error', 'Erro ao verificar acesso: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar obras por técnico (onde o técnico está na equipe)
     */
    public function getObrasPorTecnico($tecnico_id)
    {
        if (!$this->tabelaExiste('obras') || !$this->tabelaExiste('obra_equipe')) {
            // Fallback: buscar obras onde o técnico é responsável técnico
            $this->db->where('responsavel_tecnico_id', $tecnico_id);
            $this->db->where('ativo', 1);
            return $this->db->get('obras')->result();
        }

        try {
            $this->db->distinct();
            $this->db->select('o.*, c.nomeCliente as cliente_nome, oe.funcao');
            $this->db->from('obras o');
            $this->db->join('obra_equipe oe', 'oe.obra_id = o.id AND oe.tecnico_id = ' . (int)$tecnico_id);
            $this->db->join('clientes c', 'c.idClientes = o.cliente_id', 'left');
            $this->db->where('oe.ativo', 1);
            $this->db->where('o.ativo', 1);
            $this->db->group_by('o.id');
            $this->db->order_by('o.data_inicio_contrato', 'DESC');

            $query = $this->db->get();
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar obras do técnico: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar se técnico está na equipe da obra
     */
    public function tecnicoEstaNaEquipe($obra_id, $tecnico_id)
    {
        if (!$this->tabelaExiste('obra_equipe')) {
            // Fallback: verificar se é responsável técnico
            $sql = "SELECT COUNT(*) as total FROM obras WHERE id = ? AND responsavel_tecnico_id = ? AND ativo = 1";
            $query = $this->db->query($sql, [$obra_id, $tecnico_id]);
            return ($query && $query->num_rows() > 0) ? $query->row()->total > 0 : false;
        }

        try {
            $sql = "SELECT COUNT(*) as total FROM obra_equipe WHERE obra_id = ? AND tecnico_id = ? AND ativo = 1";
            $query = $this->db->query($sql, [$obra_id, $tecnico_id]);
            return ($query && $query->num_rows() > 0) ? $query->row()->total > 0 : false;
        } catch (Exception $e) {
            log_message('error', 'Erro ao verificar equipe: ' . $e->getMessage());
            return false;
        }
    }
}

