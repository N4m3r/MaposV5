<?php
/**
 * Agente_ia_autorizacoes_model
 * Model para CRUD e operacoes da tabela agente_ia_autorizacoes
 */

class Agente_ia_autorizacoes_model extends CI_Model
{
    protected string $table = 'agente_ia_autorizacoes';

    public function __construct()
    {
        parent::__construct();
    }

    // ========================================================================
    // CREATE
    // ========================================================================

    /**
     * Cria novo registro de autorizacao
     */
    public function criar(array $dados): ?int
    {
        $this->db->insert($this->table, $dados);
        return $this->db->insert_id() ?: null;
    }

    // ========================================================================
    // READ
    // ========================================================================

    /**
     * Busca autorizacao por token unico
     */
    public function buscarPorToken(string $token): ?array
    {
        $row = $this->db
            ->where('token', $token)
            ->get($this->table)
            ->row_array();
        return $row ?: null;
    }

    /**
     * Busca autorizacao por ID
     */
    public function buscarPorId(int $id): ?array
    {
        $row = $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row_array();
        return $row ?: null;
    }

    /**
     * Lista autorizacoes com filtros e paginacao
     */
    public function listar(array $filtros = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        // Aplica filtros (reutilizavel)
        $this->aplicarFiltros($filtros);

        // Count total
        $total = $this->db->count_all_results($this->table);

        // Reset para query paginada
        $this->db->reset_query();

        // Reaplica filtros
        $this->aplicarFiltros($filtros);

        $items = $this->db
            ->order_by('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get($this->table)
            ->result_array();

        return ['items' => $items, 'total' => $total];
    }

    /**
     * Aplica filtros comuns na query builder
     */
    private function aplicarFiltros(array $filtros): void
    {
        if (!empty($filtros['status'])) {
            $this->db->where('status', $filtros['status']);
        }
        if (!empty($filtros['numero'])) {
            $this->db->where('numero_telefone', $filtros['numero']);
        }
        if (!empty($filtros['usuarios_id'])) {
            $this->db->where('usuarios_id', (int)$filtros['usuarios_id']);
        }
        if (!empty($filtros['clientes_id'])) {
            $this->db->where('clientes_id', (int)$filtros['clientes_id']);
        }
        if (!empty($filtros['acao'])) {
            $this->db->where('acao', $filtros['acao']);
        }
        if (!empty($filtros['data_inicio'])) {
            $this->db->where('created_at >=', $filtros['data_inicio']);
        }
        if (!empty($filtros['data_fim'])) {
            $this->db->where('created_at <=', $filtros['data_fim']);
        }
    }

    // ========================================================================
    // UPDATE
    // ========================================================================

    /**
     * Atualiza status e campos extras de uma autorizacao
     */
    public function atualizarStatus(int $id, string $novoStatus, array $extras = []): bool
    {
        $dados = array_merge(
            ['status' => $novoStatus],
            $extras
        );

        $this->db->where('id', $id);
        return $this->db->update($this->table, $dados);
    }

    /**
     * Marca como executada e registra resultado
     */
    public function marcarExecutada(int $id, array $resultado): bool
    {
        return $this->atualizarStatus($id, 'executada', [
            'resultado_json' => json_encode($resultado),
            'executed_at'    => date('Y-m-d H:i:s')
        ]);
    }

    // ========================================================================
    // DELETE / UTIL
    // ========================================================================

    /**
     * Marca autorizacoes expiradas como expiradas
     * Ideal para rodar em cron job diario
     */
    public function expirarPendentes(): int
    {
        $this->db->where('status', 'pendente');
        $this->db->where('expires_at <', date('Y-m-d H:i:s'));
        $this->db->update($this->table, ['status' => 'expirada']);
        return $this->db->affected_rows();
    }

    /**
     * Conta autorizacoes pendentes ou por status
     */
    public function contarPorStatus(string $status = null, string $numero = null): int
    {
        if ($status) {
            $this->db->where('status', $status);
        }
        if ($numero) {
            $this->db->where('numero_telefone', $numero);
        }
        return $this->db->count_all_results($this->table);
    }

    /**
     * Rate limiting: conta solicitacoes recentes por numero
     */
    public function contarRecentes(string $numero, string $acao = null, int $minutos = 5): int
    {
        $desde = date('Y-m-d H:i:s', strtotime("-{$minutos} minutes"));
        $this->db->where('numero_telefone', $numero);
        $this->db->where('created_at >=', $desde);
        if ($acao) {
            $this->db->where('acao', $acao);
        }
        return $this->db->count_all_results($this->table);
    }

    /**
     * Log de verificacao (nao cria token, so registra consulta)
     */
    public function logVerificacao(array $dados): void
    {
        // Insere em agente_ia_logs_conversa para auditoria
        $this->db->insert('agente_ia_logs_conversa', [
            'numero_telefone'  => $dados['numero_telefone'] ?? null,
            'tipo'             => 'sistema',
            'mensagem'         => sprintf('Verificacao: acao=%s permitido=%s motivo=%s', $dados['acao'] ?? '-', $dados['permitido'] ? 'sim' : 'nao', $dados['motivo'] ?? '-'),
            'intencao_detectada' => $dados['acao'] ?? null,
            'metadados_json'   => json_encode([
                'acao'    => $dados['acao'] ?? null,
                'permitido'=> $dados['permitido'] ?? false,
                'motivo'  => $dados['motivo'] ?? null,
                'ip'      => $dados['ip'] ?? null
            ]),
            'ip_origem'        => $dados['ip'] ?? null,
            'created_at'       => date('Y-m-d H:i:s')
        ]);
    }
}
