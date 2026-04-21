<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model de Atividades de Obra
 *
 * Gerencia atividades diárias, check-ins, histórico
 */
class Obra_atividades_model extends CI_Model
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
    // CRUD ATIVIDADES
    // ============================================

    /**
     * Buscar atividade por ID
     */
    public function getById($id)
    {
        if (!$this->tabelaExiste('obra_atividades')) {
            return null;
        }

        try {
            // Verificar se tabelas de join existem
            $join_usuarios = $this->db->table_exists('usuarios');
            $join_etapas = $this->db->table_exists('obra_etapas');

            // Selecionar todos os campos da atividade
            $this->db->select('obra_atividades.*');

            if ($join_usuarios) {
                $this->db->select('u.nome as tecnico_nome');
            }
            if ($join_etapas) {
                $this->db->select('oe.nome as etapa_nome, oe.numero_etapa');
            }

            $this->db->from('obra_atividades');

            if ($join_usuarios) {
                $this->db->join('usuarios u', 'u.idUsuarios = obra_atividades.tecnico_id', 'left');
            }
            if ($join_etapas) {
                $this->db->join('obra_etapas oe', 'oe.id = obra_atividades.etapa_id', 'left');
            }

            $this->db->where('obra_atividades.id', $id);

            $query = $this->db->get();

            log_message('debug', 'getById atividade ID ' . $id . ': ' . $this->db->last_query());

            return $query ? $query->row() : null;
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar atividade: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Listar atividades por obra
     */
    public function getByObra($obra_id, $filtros = [], $limit = null, $offset = 0)
    {
        if (!$this->tabelaExiste('obra_atividades')) {
            log_message('error', 'getByObra: Tabela obra_atividades nao existe');
            return [];
        }

        try {
            // Query simples primeiro (igual ao verificarAtividades)
            $this->db->where('obra_id', $obra_id);

            // Filtro ativo - só mostrar atividades ativas (se o campo existir)
            if ($this->db->field_exists('ativo', 'obra_atividades')) {
                $this->db->where('ativo', 1);
            }

            // Filtros opcionais
            if (!empty($filtros['status'])) {
                $this->db->where('status', $filtros['status']);
            }
            if (!empty($filtros['tecnico_id'])) {
                $this->db->where('tecnico_id', $filtros['tecnico_id']);
            }
            if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
                $this->db->where('data_atividade >=', $filtros['data_inicio']);
                $this->db->where('data_atividade <=', $filtros['data_fim']);
            }
            if (!empty($filtros['tipo'])) {
                $this->db->where('tipo', $filtros['tipo']);
            }
            if (!empty($filtros['etapa_id'])) {
                $this->db->where('etapa_id', $filtros['etapa_id']);
            }

            // Ordenação - mais recentes primeiro
            $this->db->order_by('data_atividade', 'DESC');

            // Verificar se campo hora_inicio existe antes de ordenar
            if ($this->db->field_exists('hora_inicio', 'obra_atividades')) {
                $this->db->order_by('hora_inicio', 'DESC');
            }

            if ($limit) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('obra_atividades');

            // Log da query para debug
            $last_query = $this->db->last_query();
            log_message('debug', 'SQL getByObra obra_id=' . $obra_id . ': ' . $last_query);

            if (!$query) {
                log_message('error', 'getByObra: Query retornou false');
                return [];
            }

            $result = $query->result();
            log_message('debug', 'getByObra: Retornando ' . count($result) . ' registros');

            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro ao listar atividades: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Atividades do dia para um técnico
     */
    public function getAtividadesDoDia($tecnico_id, $data = null)
    {
        if (!$data) {
            $data = date('Y-m-d');
        }

        return $this->getByObra(null, [
            'tecnico_id' => $tecnico_id,
            'data_inicio' => $data,
            'data_fim' => $data
        ]);
    }

    /**
     * Adicionar atividade
     */
    public function add($dados)
    {
        if (!$this->tabelaExiste('obra_atividades')) {
            return false;
        }

        try {
            // Obter colunas existentes na tabela
            $colunas_existentes = $this->db->list_fields('obra_atividades');

            // Iniciar array de dados apenas com colunas que existem
            $data = [];

            // Campos obrigatórios - só adicionar se existirem
            if (in_array('obra_id', $colunas_existentes)) {
                $data['obra_id'] = $dados['obra_id'];
            }
            if (in_array('titulo', $colunas_existentes)) {
                $data['titulo'] = $dados['titulo'] ?? $dados['descricao'] ?? 'Atividade';
            }
            if (in_array('data_atividade', $colunas_existentes)) {
                $data['data_atividade'] = $dados['data_atividade'] ?? date('Y-m-d');
            }

            // Campos opcionais - só adicionar se a coluna existir e tiver valor
            $campos_opcionais = [
                'etapa_id', 'tecnico_id', 'os_id', 'descricao', 'tipo', 'status',
                'percentual_concluido', 'visivel_cliente', 'hora_inicio', 'hora_fim',
                'horas_trabalhadas', 'impedimento', 'motivo_impedimento', 'tipo_impedimento',
                'checkin_lat', 'checkin_lng', 'checkout_lat', 'checkout_lng',
                'fotos_checkin', 'fotos_atividade', 'fotos_checkout', 'ativo'
            ];

            foreach ($campos_opcionais as $campo) {
                if (in_array($campo, $colunas_existentes)) {
                    if (isset($dados[$campo]) && $dados[$campo] !== '') {
                        $data[$campo] = $dados[$campo];
                    } elseif (in_array($campo, ['status', 'tipo'])) {
                        // Valores padrão para campos ENUM
                        $data[$campo] = ($campo == 'status') ? 'agendada' : 'trabalho';
                    } elseif (in_array($campo, ['percentual_concluido', 'visivel_cliente', 'ativo'])) {
                        // Valores padrão numéricos
                        $data[$campo] = 1;
                    }
                }
            }

            // Só adicionar timestamps se as colunas existirem
            if (in_array('created_at', $colunas_existentes)) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
            if (in_array('updated_at', $colunas_existentes)) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            // Log para debug
            log_message('debug', 'Inserindo atividade com dados: ' . json_encode($data));

            $this->db->insert('obra_atividades', $data);
            $id = $this->db->insert_id();

            // Registrar no histórico (se tabela existir)
            if ($id && $this->tabelaExiste('obra_atividades_historico')) {
                $this->registrarHistorico($id, 'criacao', $dados['tecnico_id'] ?? null, [
                    'descricao' => 'Atividade criada',
                    'percentual_novo' => $dados['percentual_concluido'] ?? 0
                ]);
            }

            return $id;
        } catch (Exception $e) {
            log_message('error', 'Erro ao adicionar atividade: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualizar atividade
     */
    public function update($id, $dados)
    {
        if (!$this->tabelaExiste('obra_atividades')) {
            return false;
        }

        try {
            // Buscar dados atuais para o histórico
            $atividade_atual = $this->getById($id);
            if (!$atividade_atual) {
                return false;
            }

            $data = [];

            // Só adicionar updated_at se a coluna existir
            if ($this->db->field_exists('updated_at', 'obra_atividades')) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            // Campos permitidos - só adicionar se a coluna existir na tabela
            $campos_permitidos = [
                'etapa_id', 'tecnico_id', 'titulo', 'descricao', 'tipo',
                'status', 'percentual_concluido', 'hora_inicio', 'hora_fim',
                'horas_trabalhadas', 'impedimento', 'motivo_impedimento',
                'tipo_impedimento', 'checkin_lat', 'checkin_lng',
                'checkout_lat', 'checkout_lng', 'fotos_checkin',
                'fotos_atividade', 'fotos_checkout', 'visivel_cliente',
                'data_atividade'
            ];

            // Obter colunas reais da tabela
            $colunas_existentes = $this->db->list_fields('obra_atividades');

            foreach ($campos_permitidos as $campo) {
                if (isset($dados[$campo]) && in_array($campo, $colunas_existentes)) {
                    $data[$campo] = $dados[$campo];
                }
            }

            $this->db->where('id', $id);
            $this->db->update('obra_atividades', $data);

            // Verificar se houve erro real na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro SQL ao atualizar atividade ID ' . $id . ': ' . print_r($error, true));
                log_message('error', 'Dados: ' . print_r($data, true));
                return false;
            }

            // Registrar mudança de percentual no histórico
            if (isset($dados['percentual_concluido'])) {
                if ($dados['percentual_concluido'] != ($atividade_atual->percentual_concluido ?? 0)) {
                    $this->registrarHistorico($id, 'progresso', $dados['tecnico_id'] ?? null, [
                        'descricao' => 'Progresso atualizado',
                        'percentual_anterior' => $atividade_atual->percentual_concluido ?? 0,
                        'percentual_novo' => $dados['percentual_concluido']
                    ]);
                }
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Erro ao atualizar atividade: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Excluir atividade
     */
    public function delete($id)
    {
        if (!$this->tabelaExiste('obra_atividades')) {
            return false;
        }

        try {
            $this->db->where('id', $id);
            return $this->db->delete('obra_atividades');
        } catch (Exception $e) {
            log_message('error', 'Erro ao excluir atividade: ' . $e->getMessage());
            return false;
        }
    }

    // ============================================
    // AÇÕES ESPECÍFICAS
    // ============================================

    /**
     * Iniciar atividade
     */
    public function iniciarAtividade($id, $tecnico_id, $dados = [])
    {
        $update_data = [
            'status' => 'iniciada',
            'tecnico_id' => $tecnico_id,
            'hora_inicio' => $dados['hora_inicio'] ?? date('H:i:s'),
            'checkin_lat' => $dados['latitude'] ?? null,
            'checkin_lng' => $dados['longitude'] ?? null,
        ];

        if (!empty($dados['foto'])) {
            $update_data['fotos_checkin'] = json_encode([$dados['foto']]);
        }

        $result = $this->update($id, $update_data);

        if ($result) {
            $this->registrarHistorico($id, 'inicio', $tecnico_id, [
                'descricao' => 'Atividade iniciada',
                'localizacao_lat' => $dados['latitude'] ?? null,
                'localizacao_lng' => $dados['longitude'] ?? null
            ]);
        }

        return $result;
    }

    /**
     * Pausar atividade
     */
    public function pausarAtividade($id, $tecnico_id, $dados = [])
    {
        $update_data = [
            'status' => 'pausada',
        ];

        $result = $this->update($id, $update_data);

        if ($result) {
            $this->registrarHistorico($id, 'pausa', $tecnico_id, [
                'descricao' => $dados['motivo'] ?? 'Atividade pausada',
                'horas_trabalhadas' => $dados['horas'] ?? 0
            ]);
        }

        return $result;
    }

    /**
     * Retomar atividade
     */
    public function retomarAtividade($id, $tecnico_id, $dados = [])
    {
        $update_data = [
            'status' => 'iniciada',
        ];

        $result = $this->update($id, $update_data);

        if ($result) {
            $this->registrarHistorico($id, 'retorno', $tecnico_id, [
                'descricao' => 'Atividade retomada',
                'localizacao_lat' => $dados['latitude'] ?? null,
                'localizacao_lng' => $dados['longitude'] ?? null
            ]);
        }

        return $result;
    }

    /**
     * Finalizar atividade
     */
    public function finalizarAtividade($id, $tecnico_id, $dados = [])
    {
        $atividade = $this->getById($id);
        if (!$atividade) {
            return false;
        }

        // Calcular horas trabalhadas
        $horas_trabalhadas = $dados['horas_trabalhadas'] ?? 0;
        if ($horas_trabalhadas == 0 && $atividade->hora_inicio) {
            $inicio = strtotime($atividade->hora_inicio);
            $fim = strtotime(date('H:i:s'));
            $horas_trabalhadas = round(($fim - $inicio) / 3600, 2);
        }

        $update_data = [
            'status' => 'concluida',
            'hora_fim' => $dados['hora_fim'] ?? date('H:i:s'),
            'horas_trabalhadas' => $horas_trabalhadas,
            'percentual_concluido' => $dados['percentual'] ?? 100,
            'checkout_lat' => $dados['latitude'] ?? null,
            'checkout_lng' => $dados['longitude'] ?? null,
        ];

        if (!empty($dados['fotos'])) {
            $update_data['fotos_checkout'] = json_encode($dados['fotos']);
        }

        $result = $this->update($id, $update_data);

        if ($result) {
            $this->registrarHistorico($id, 'conclusao', $tecnico_id, [
                'descricao' => 'Atividade concluída',
                'percentual_novo' => $dados['percentual'] ?? 100,
                'horas_trabalhadas' => $horas_trabalhadas,
                'localizacao_lat' => $dados['latitude'] ?? null,
                'localizacao_lng' => $dados['longitude'] ?? null
            ]);

            // Atualizar progresso da etapa
            if ($atividade->etapa_id) {
                $this->atualizarProgressoEtapa($atividade->etapa_id);
            }
        }

        return $result;
    }

    /**
     * Registrar impedimento
     */
    public function registrarImpedimento($id, $tecnico_id, $dados)
    {
        $update_data = [
            'status' => 'pausada',
            'impedimento' => 1,
            'tipo_impedimento' => $dados['tipo'] ?? 'outro',
            'motivo_impedimento' => $dados['descricao'] ?? null,
        ];

        $result = $this->update($id, $update_data);

        if ($result) {
            $this->registrarHistorico($id, 'impedimento', $tecnico_id, [
                'descricao' => 'Impedimento registrado: ' . ($dados['descricao'] ?? ''),
                'tipo_impedimento' => $dados['tipo'] ?? 'outro'
            ]);
        }

        return $result;
    }

    // ============================================
    // HISTÓRICO
    // ============================================

    /**
     * Registrar histórico
     */
    private function registrarHistorico($atividade_id, $tipo, $tecnico_id, $dados)
    {
        if (!$this->tabelaExiste('obra_atividades_historico')) {
            return false;
        }

        try {
            $data = [
                'atividade_id' => $atividade_id,
                'tecnico_id' => $tecnico_id,
                'tipo_alteracao' => $tipo,
                'descricao' => $dados['descricao'] ?? null,
                'percentual_anterior' => $dados['percentual_anterior'] ?? 0,
                'percentual_novo' => $dados['percentual_novo'] ?? 0,
                'horas_trabalhadas' => $dados['horas_trabalhadas'] ?? 0,
                'localizacao_lat' => $dados['localizacao_lat'] ?? null,
                'localizacao_lng' => $dados['localizacao_lng'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('obra_atividades_historico', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            log_message('error', 'Erro ao registrar histórico: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar histórico de atividade
     */
    public function getHistorico($atividade_id)
    {
        if (!$this->tabelaExiste('obra_atividades_historico')) {
            return [];
        }

        try {
            $this->db->select('obra_atividades_historico.*, u.nome as tecnico_nome');
            $this->db->from('obra_atividades_historico');
            $this->db->join('usuarios u', 'u.idUsuarios = obra_atividades_historico.tecnico_id', 'left');
            $this->db->where('atividade_id', $atividade_id);
            $this->db->order_by('created_at', 'DESC');
            $query = $this->db->get();
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar histórico: ' . $e->getMessage());
            return [];
        }
    }

    // ============================================
    // CÁLCULOS E ESTATÍSTICAS
    // ============================================

    /**
     * Atualizar progresso da etapa
     */
    private function atualizarProgressoEtapa($etapa_id)
    {
        if (!$this->tabelaExiste('obra_atividades') || !$this->tabelaExiste('obra_etapas')) {
            return false;
        }

        try {
            // Calcular média de percentual das atividades concluídas
            $this->db->select_avg('percentual_concluido', 'media_percentual');
            $this->db->where('etapa_id', $etapa_id);
            $this->db->where('status', 'concluida');
            $query = $this->db->get('obra_atividades');
            $result = $query ? $query->row() : null;

            $percentual = $result ? round($result->media_percentual) : 0;

            // Atualizar etapa
            $this->db->where('id', $etapa_id);
            $this->db->update('obra_etapas', [
                'percentual_concluido' => $percentual,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $percentual;
        } catch (Exception $e) {
            log_message('error', 'Erro ao atualizar progresso da etapa: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Estatísticas de atividades
     */
    public function getEstatisticas($obra_id, $data_inicio = null, $data_fim = null)
    {
        if (!$this->tabelaExiste('obra_atividades')) {
            return [];
        }

        try {
            $this->db->where('obra_id', $obra_id);

            if ($data_inicio && $data_fim) {
                $this->db->where('data_atividade >=', $data_inicio);
                $this->db->where('data_atividade <=', $data_fim);
            }

            $total = $this->db->count_all_results('obra_atividades');

            $this->db->where('obra_id', $obra_id);
            $this->db->where('status', 'concluida');
            if ($data_inicio && $data_fim) {
                $this->db->where('data_atividade >=', $data_inicio);
                $this->db->where('data_atividade <=', $data_fim);
            }
            $concluidas = $this->db->count_all_results('obra_atividades');

            $this->db->select_sum('horas_trabalhadas', 'total_horas');
            $this->db->where('obra_id', $obra_id);
            if ($data_inicio && $data_fim) {
                $this->db->where('data_atividade >=', $data_inicio);
                $this->db->where('data_atividade <=', $data_fim);
            }
            $query = $this->db->get('obra_atividades');
            $horas = $query ? $query->row()->total_horas : 0;

            return [
                'total_atividades' => $total,
                'concluidas' => $concluidas,
                'pendentes' => $total - $concluidas,
                'total_horas' => round($horas, 2)
            ];
        } catch (Exception $e) {
            log_message('error', 'Erro ao calcular estatísticas: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar atividades por status
     */
    public function countByStatus($obra_id)
    {
        if (!$this->tabelaExiste('obra_atividades')) {
            return [];
        }

        try {
            $this->db->select('status, COUNT(*) as total');
            $this->db->where('obra_id', $obra_id);
            $this->db->group_by('status');
            $query = $this->db->get('obra_atividades');

            $result = [];
            foreach ($query->result() as $row) {
                $result[$row->status] = $row->total;
            }

            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro ao contar por status: ' . $e->getMessage());
            return [];
        }
    }
}
