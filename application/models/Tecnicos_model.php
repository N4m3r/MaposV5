<?php

/**
 * Model de Gerenciamento de Técnicos
 */
class Tecnicos_model extends CI_Model
{
    protected $table = 'usuarios';
    protected $estoque_table = 'tec_estoque_veiculo';
    protected $rotas_table = 'tec_rotas_tracking';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Buscar técnico por ID
     */
    public function getById($id)
    {
        $this->db->where('idUsuarios', $id);
        $this->db->group_start();
        $this->db->where('is_tecnico', 1);
        $this->db->or_where('app_tecnico_instalado', 1);
        $this->db->group_end();
        return $this->db->get($this->table)->row();
    }

    /**
     * Buscar técnico por email
     */
    public function getByEmail($email)
    {
        $this->db->where('email', $email);
        return $this->db->get($this->table)->row();
    }

    /**
     * Buscar técnico por token
     */
    public function getByToken($token)
    {
        $this->db->where('token_app', $token);
        $this->db->group_start();
        $this->db->where('is_tecnico', 1);
        $this->db->or_where('app_tecnico_instalado', 1);
        $this->db->group_end();
        return $this->db->get($this->table)->row();
    }

    /**
     * Listar todos os técnicos
     */
    public function getAll($where = [], $limit = null, $offset = null)
    {
        $this->db->group_start();
        $this->db->where('is_tecnico', 1);
        $this->db->or_where('app_tecnico_instalado', 1);
        $this->db->group_end();

        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('nome', 'ASC');
        $query = $this->db->get($this->table);
        return $query ? $query->result() : [];
    }

    /**
     * Contar técnicos
     */
    public function count($where = [])
    {
        $this->db->group_start();
        $this->db->where('is_tecnico', 1);
        $this->db->or_where('app_tecnico_instalado', 1);
        $this->db->group_end();

        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->count_all_results($this->table);
    }

    /**
     * Adicionar técnico
     */
    public function add($data)
    {
        $data['is_tecnico'] = 1;
        $data['situacao'] = 1;

        // Verificar se as colunas existem antes de inserir
        $campos_existentes = $this->db->list_fields($this->table);
        $data_filtrado = array_intersect_key($data, array_flip($campos_existentes));

        if ($this->db->insert($this->table, $data_filtrado)) {
            return $this->db->insert_id();
        }

        // Log do erro para debug
        log_message('error', 'Erro ao cadastrar técnico: ' . $this->db->error()['message']);
        log_message('error', 'Query: ' . $this->db->last_query());

        return false;
    }

    /**
     * Atualizar técnico
     */
    public function update($id, $data)
    {
        // Verificar se as colunas existem antes de atualizar
        $campos_existentes = $this->db->list_fields($this->table);
        $data_filtrado = array_intersect_key($data, array_flip($campos_existentes));

        $this->db->where('idUsuarios', $id);
        return $this->db->update($this->table, $data_filtrado);
    }

    /**
     * Excluir técnico (soft delete - apenas remove flag)
     */
    public function delete($id)
    {
        $this->db->where('idUsuarios', $id);
        return $this->db->update($this->table, ['is_tecnico' => 0, 'app_tecnico_instalado' => 0]);
    }

    /**
     * Registrar acesso do técnico
     */
    public function registrar_acesso($id, $lat = null, $lng = null, $foto = null)
    {
        $data = [
            'ultimo_acesso_app' => date('Y-m-d H:i:s'),
            'app_tecnico_instalado' => 1,
        ];

        $this->db->where('idUsuarios', $id);
        $this->db->update($this->table, $data);

        // Registrar na tabela de rotas
        $rota = [
            'tecnico_id' => $id,
            'data_hora' => date('Y-m-d H:i:s'),
            'latitude' => $lat,
            'longitude' => $lng,
            'tipo' => 'login',
            'foto' => $foto,
        ];

        $this->db->insert($this->rotas_table, $rota);

        return true;
    }

    /**
     * Atualizar token
     */
    public function atualizar_token($id, $token)
    {
        $data = [
            'token_app' => $token,
            'token_expira' => date('Y-m-d H:i:s', strtotime('+7 days')),
        ];

        $this->db->where('idUsuarios', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Buscar estoque do veículo do técnico
     */
    public function getEstoqueVeiculo($tecnico_id)
    {
        $this->db->select('ev.*, p.nome as produto_nome, p.descricao as produto_descricao, p.codDeBarra, p.unidade');
        $this->db->from($this->estoque_table . ' ev');
        $this->db->join('produtos p', 'p.idProdutos = ev.produto_id', 'left');
        $this->db->where('ev.tecnico_id', $tecnico_id);
        $this->db->where('ev.quantidade >', 0);
        $this->db->order_by('p.nome', 'ASC');

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    /**
     * Buscar item específico do estoque
     */
    public function getEstoqueItem($tecnico_id, $produto_id)
    {
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('produto_id', $produto_id);
        return $this->db->get($this->estoque_table)->row();
    }

    /**
     * Atualizar quantidade em estoque
     */
    public function atualizarEstoque($tecnico_id, $produto_id, $quantidade, $tipo = 'saida', $observacao = '', $os_id = null)
    {
        $estoque = $this->getEstoqueItem($tecnico_id, $produto_id);

        if ($estoque) {
            // Atualizar estoque existente
            $nova_quantidade = $tipo === 'entrada'
                ? $estoque->quantidade + $quantidade
                : $estoque->quantidade - $quantidade;

            $this->db->where('id', $estoque->id);
            $this->db->update($this->estoque_table, [
                'quantidade' => $nova_quantidade,
                'data_atualizacao' => date('Y-m-d H:i:s'),
            ]);
        } else if ($tipo === 'entrada') {
            // Criar novo item no estoque
            $this->db->insert($this->estoque_table, [
                'tecnico_id' => $tecnico_id,
                'produto_id' => $produto_id,
                'quantidade' => $quantidade,
                'data_atualizacao' => date('Y-m-d H:i:s'),
            ]);
        }

        // Registrar movimentação no histórico
        $this->db->insert('tec_estoque_historico', [
            'tecnico_id' => $tecnico_id,
            'produto_id' => $produto_id,
            'tipo' => $tipo,
            'quantidade' => $quantidade,
            'observacao' => $observacao,
            'os_id' => $os_id,
            'data_hora' => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    /**
     * Registrar uso de material
     */
    public function registrarUsoMaterial($tecnico_id, $dados)
    {
        return $this->atualizarEstoque(
            $tecnico_id,
            $dados['produto_id'],
            $dados['quantidade'],
            'saida',
            $dados['observacao'] ?? '',
            $dados['os_id'] ?? null
        );
    }

    /**
     * Obter histórico de estoque
     */
    public function getHistoricoEstoque($tecnico_id, $dias = 30)
    {
        $this->db->select('eh.*, p.nome as produto_nome');
        $this->db->from('tec_estoque_historico eh');
        $this->db->join('produtos p', 'p.idProdutos = eh.produto_id', 'left');
        $this->db->where('eh.tecnico_id', $tecnico_id);
        $this->db->where('eh.data_hora >=', date('Y-m-d H:i:s', strtotime("-{$dias} days")));
        $this->db->order_by('eh.data_hora', 'DESC');

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    /**
     * Obter rotas do técnico
     */
    public function getRotas($tecnico_id, $data = null)
    {
        $this->db->where('tecnico_id', $tecnico_id);

        if ($data) {
            $this->db->where('DATE(data_hora)', $data);
        }

        $this->db->order_by('data_hora', 'ASC');
        $query = $this->db->get($this->rotas_table);
        return $query ? $query->result() : [];
    }

    /**
     * Obter estatísticas do técnico
     */
    public function getEstatisticas($tecnico_id, $periodo = 'mes')
    {
        switch ($periodo) {
            case 'semana':
                $inicio = date('Y-m-d', strtotime('-7 days'));
                break;
            case 'mes':
                $inicio = date('Y-m-01');
                break;
            case 'ano':
                $inicio = date('Y-01-01');
                break;
            default:
                $inicio = date('Y-m-d', strtotime('-30 days'));
        }

        // OS executadas
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('status', 'concluida');
        $this->db->where('DATE(data_checkin) >=', $inicio);
        $os_concluidas = $this->db->count_all_results('tec_os_execucao');

        // Horas trabalhadas
        $this->db->select_sum('tempo_total_horas');
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('status', 'concluida');
        $this->db->where('DATE(data_checkin) >=', $inicio);
        $horas_result = $this->db->get('tec_os_execucao')->row();
        $horas_trabalhadas = $horas_result->tempo_total_horas ?: 0;

        // Material utilizado
        $this->db->select_sum('quantidade');
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('tipo', 'saida');
        $this->db->where('DATE(data_hora) >=', $inicio);
        $material_result = $this->db->get('tec_estoque_historico')->row();
        $material_utilizado = $material_result->quantidade ?: 0;

        return [
            'os_concluidas' => $os_concluidas,
            'horas_trabalhadas' => round($horas_trabalhadas, 2),
            'material_utilizado' => $material_utilizado,
        ];
    }

    /**
     * Listar técnicos disponíveis para plantão
     */
    public function getTecnicosPlantao()
    {
        $this->db->where('is_tecnico', 1);
        $this->db->where('plantao_24h', 1);
        $this->db->where('situacao', 1);
        $query = $this->db->get($this->table);
        return $query ? $query->result() : [];
    }

    /**
     * Buscar técnicos por especialidade
     */
    public function getByEspecialidade($especialidade)
    {
        $this->db->where('is_tecnico', 1);
        $this->db->where('situacao', 1);
        $this->db->like('especialidades', $especialidade);
        $query = $this->db->get($this->table);
        return $query ? $query->result() : [];
    }

    /**
     * Verificar disponibilidade do técnico
     */
    public function verificarDisponibilidade($tecnico_id, $data, $hora_inicio, $hora_fim)
    {
        // Verificar se técnico já tem OS agendada no período
        $this->db->where('usuarios_id', $tecnico_id);
        $this->db->where('dataInicial', $data);
        $this->db->where_in('status', ['Aberto', 'Em Andamento']);

        $query = $this->db->get('os');
        $os_existentes = $query ? $query->result() : [];

        foreach ($os_existentes as $os) {
            // Verificar conflito de horário (simplificado)
            // Em produção, adicionar campos de hora na OS
            return false;
        }

        return true;
    }
}
