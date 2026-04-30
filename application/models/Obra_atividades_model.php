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
            // Query simples primeiro
            $this->db->where('id', $id);
            $query = $this->db->get('obra_atividades');

            if (!$query || $query->num_rows() == 0) {
                return null;
            }

            $atividade = $query->row();

            // Compatibilidade: mapear usuario_id para tecnico_id se necessário
            if (!isset($atividade->tecnico_id) && isset($atividade->usuario_id)) {
                $atividade->tecnico_id = $atividade->usuario_id;
            }
            // Compatibilidade: mapear usuario_nome para tecnico_nome se necessário
            if ((!isset($atividade->tecnico_nome) || empty($atividade->tecnico_nome)) && isset($atividade->usuario_nome)) {
                $atividade->tecnico_nome = $atividade->usuario_nome;
            }

            log_message('debug', 'getById - Atividade ID ' . $id . ' encontrada. etapa_id=' . ($atividade->etapa_id ?? 'NULL') . ', tecnico_id=' . ($atividade->tecnico_id ?? 'NULL'));

            // Buscar nome do técnico separadamente se existir
            if (!empty($atividade->tecnico_id) && $this->db->table_exists('usuarios')) {
                $this->db->select('nome');
                $this->db->where('idUsuarios', $atividade->tecnico_id);
                $tecnico_query = $this->db->get('usuarios');
                if ($tecnico_query && $tecnico_query->num_rows() > 0) {
                    $atividade->tecnico_nome = $tecnico_query->row()->nome;
                }
            }

            // Buscar nome da etapa separadamente se existir
            if (!empty($atividade->etapa_id) && $this->db->table_exists('obra_etapas')) {
                log_message('debug', 'getById - Buscando etapa ID: ' . $atividade->etapa_id);
                $this->db->select('nome, numero_etapa');
                $this->db->where('id', $atividade->etapa_id);
                $etapa_query = $this->db->get('obra_etapas');
                if ($etapa_query && $etapa_query->num_rows() > 0) {
                    $etapa = $etapa_query->row();
                    $atividade->etapa_nome = $etapa->nome;
                    $atividade->numero_etapa = $etapa->numero_etapa;
                    log_message('debug', 'getById - Etapa encontrada: ' . $etapa->nome);
                } else {
                    log_message('debug', 'getById - Etapa não encontrada ou query falhou');
                }
            } else {
                log_message('debug', 'getById - Sem etapa: etapa_id=' . ($atividade->etapa_id ?? 'NULL') . ', tabela existe=' . ($this->db->table_exists('obra_etapas') ? 'SIM' : 'NAO'));
            }

            return $atividade;
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
            // Detectar qual campo de técnico existe (compatibilidade com estrutura antiga)
            $colunas = $this->db->list_fields('obra_atividades');
            $campo_tecnico = in_array('tecnico_id', $colunas) ? 'tecnico_id' : (in_array('usuario_id', $colunas) ? 'usuario_id' : null);
            $campo_tecnico_nome = in_array('usuario_nome', $colunas) ? 'usuario_nome' : null;

            // Query com joins para buscar nomes de técnico e etapa
            $this->db->select('obra_atividades.*, u.nome as tecnico_nome_join, oe.nome as etapa_nome, oe.numero_etapa');
            $this->db->from('obra_atividades');
            $this->db->where('obra_atividades.obra_id', $obra_id);

            // Join com usuarios (técnico) - LEFT JOIN para não perder atividades sem técnico
            if ($this->db->table_exists('usuarios') && $campo_tecnico) {
                $this->db->join('usuarios u', 'u.idUsuarios = obra_atividades.' . $campo_tecnico, 'left');
            }

            // Join com obra_etapas - LEFT JOIN para não perder atividades sem etapa
            if ($this->db->table_exists('obra_etapas') && in_array('etapa_id', $colunas)) {
                $this->db->join('obra_etapas oe', 'oe.id = obra_atividades.etapa_id', 'left');
            }

            // Filtro ativo - só mostrar atividades ativas (se o campo existir)
            if ($this->db->field_exists('ativo', 'obra_atividades')) {
                $this->db->where('obra_atividades.ativo', 1);
            }

            // Filtros opcionais
            if (!empty($filtros['status'])) {
                $this->db->where('obra_atividades.status', $filtros['status']);
            }
            if (!empty($filtros['tecnico_id'])) {
                $this->db->where('obra_atividades.tecnico_id', $filtros['tecnico_id']);
            }
            if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
                $this->db->where('obra_atividades.data_atividade >=', $filtros['data_inicio']);
                $this->db->where('obra_atividades.data_atividade <=', $filtros['data_fim']);
            }
            if (!empty($filtros['tipo'])) {
                $this->db->where('obra_atividades.tipo', $filtros['tipo']);
            }
            if (!empty($filtros['etapa_id'])) {
                $this->db->where('obra_atividades.etapa_id', $filtros['etapa_id']);
            }

            // Ordenação - mais recentes primeiro
            $this->db->order_by('obra_atividades.data_atividade', 'DESC');

            // Verificar se campo hora_inicio existe antes de ordenar
            if ($this->db->field_exists('hora_inicio', 'obra_atividades')) {
                $this->db->order_by('obra_atividades.hora_inicio', 'DESC');
            }

            if ($limit) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get();

            // Log da query para debug
            $last_query = $this->db->last_query();
            log_message('debug', 'SQL getByObra obra_id=' . $obra_id . ': ' . $last_query);

            // Verificar erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'getByObra: Erro SQL: ' . $error['message']);
                return [];
            }

            if (!$query) {
                log_message('error', 'getByObra: Query retornou false');
                return [];
            }

            $result = $query->result();

            // Mapear campos antigos para novos
            foreach ($result as $atividade) {
                // Compatibilidade: mapear usuario_id para tecnico_id
                if (!isset($atividade->tecnico_id) && isset($atividade->usuario_id)) {
                    $atividade->tecnico_id = $atividade->usuario_id;
                }
                // Compatibilidade: mapear usuario_nome ou tecnico_nome_join para tecnico_nome
                if ((!isset($atividade->tecnico_nome) || empty($atividade->tecnico_nome)) && isset($atividade->tecnico_nome_join)) {
                    $atividade->tecnico_nome = $atividade->tecnico_nome_join;
                }
                if ((!isset($atividade->tecnico_nome) || empty($atividade->tecnico_nome)) && isset($atividade->usuario_nome)) {
                    $atividade->tecnico_nome = $atividade->usuario_nome;
                }
                // Compatibilidade: garantir que status existe
                if (!isset($atividade->status)) {
                    $atividade->status = 'agendada';
                }
                // Compatibilidade: garantir que titulo existe
                if (!isset($atividade->titulo)) {
                    $atividade->titulo = $atividade->descricao ?? 'Atividade #' . $atividade->id;
                }
                // Compatibilidade: garantir que data_atividade existe
                if (!isset($atividade->data_atividade) && isset($atividade->created_at)) {
                    $atividade->data_atividade = date('Y-m-d', strtotime($atividade->created_at));
                }
            }

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
                'etapa_id', 'tecnico_id', 'usuario_id', 'os_id', 'descricao', 'tipo', 'status',
                'percentual_concluido', 'visivel_cliente', 'hora_inicio', 'hora_fim',
                'horas_trabalhadas', 'impedimento', 'motivo_impedimento', 'tipo_impedimento',
                'checkin_lat', 'checkin_lng', 'checkout_lat', 'checkout_lng',
                'fotos_checkin', 'fotos_atividade', 'fotos_checkout', 'ativo'
            ];

            // Mapear tecnico_id para usuario_id se necessário (compatibilidade)
            if (!in_array('tecnico_id', $colunas_existentes) && in_array('usuario_id', $colunas_existentes)) {
                if (isset($dados['tecnico_id'])) {
                    $dados['usuario_id'] = $dados['tecnico_id'];
                }
            }

            foreach ($campos_opcionais as $campo) {
                if (in_array($campo, $colunas_existentes)) {
                    // Usar array_key_exists para permitir valores NULL (diferente do isset que retorna false para null)
                    if (array_key_exists($campo, $dados)) {
                        // Se o valor não é null e não é string vazia, adicionar
                        if ($dados[$campo] !== null && $dados[$campo] !== '') {
                            $data[$campo] = $dados[$campo];
                            log_message('debug', 'add atividade - campo ' . $campo . ' = ' . $dados[$campo]);
                        } elseif ($dados[$campo] === null) {
                            // Valor explicitamente null - salvar como null no banco
                            $data[$campo] = null;
                            log_message('debug', 'add atividade - campo ' . $campo . ' = NULL');
                        }
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
            log_message('debug', 'Dados array completo: ' . print_r($data, true));

            $this->db->insert('obra_atividades', $data);
            $id = $this->db->insert_id();

            // Registrar no histórico (se tabela existir)
            if ($id && $this->tabelaExiste('obra_atividades_historico')) {
                $this->registrarHistorico($id, 'criacao', $dados['tecnico_id'] ?? null, [
                    'descricao' => 'Atividade criada',
                    'percentual_novo' => $dados['percentual_concluido'] ?? 0
                ]);
            }

            // Atualizar progresso da etapa
            if (!empty($dados['etapa_id'])) {
                $this->atualizarProgressoEtapa($dados['etapa_id']);
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
                'etapa_id', 'tecnico_id', 'usuario_id', 'titulo', 'descricao', 'tipo',
                'status', 'percentual_concluido', 'hora_inicio', 'hora_fim',
                'horas_trabalhadas', 'impedimento', 'motivo_impedimento',
                'tipo_impedimento', 'checkin_lat', 'checkin_lng',
                'checkout_lat', 'checkout_lng', 'fotos_checkin',
                'fotos_atividade', 'fotos_checkout', 'visivel_cliente',
                'data_atividade'
            ];

            // Obter colunas reais da tabela
            $colunas_existentes = $this->db->list_fields('obra_atividades');

            // Mapear tecnico_id para usuario_id se necessário (compatibilidade)
            if (!in_array('tecnico_id', $colunas_existentes) && in_array('usuario_id', $colunas_existentes)) {
                if (array_key_exists('tecnico_id', $dados)) {
                    $dados['usuario_id'] = $dados['tecnico_id'];
                }
            }

            foreach ($campos_permitidos as $campo) {
                if (array_key_exists($campo, $dados) && in_array($campo, $colunas_existentes)) {
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

            // Atualizar progresso geral da obra sempre que atividades são alteradas
            if (!empty($atividade_atual->obra_id)) {
                $CI =& get_instance();
                $CI->load->model('obras_model');
                $CI->obras_model->atualizarProgressoPorAtividades($atividade_atual->obra_id);
                log_message('info', 'Progresso da obra ' . $atividade_atual->obra_id . ' atualizado após alteração na atividade ' . $id);
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
            // Buscar etapa_id antes de excluir para recalcular progresso depois
            $etapa_id = null;
            if ($this->db->field_exists('etapa_id', 'obra_atividades')) {
                $this->db->select('etapa_id');
                $this->db->where('id', $id);
                $query = $this->db->get('obra_atividades');
                if ($query && $query->num_rows() > 0) {
                    $etapa_id = $query->row()->etapa_id;
                }
            }

            $this->db->where('id', $id);
            $result = $this->db->delete('obra_atividades');

            // Atualizar progresso da etapa
            if ($result && $etapa_id) {
                $this->atualizarProgressoEtapa($etapa_id);
            }

            return $result;
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

                // Verificar se etapa foi concluída
                $this->db->select('percentual_concluido, numero_etapa, nome');
                $this->db->where('id', $atividade->etapa_id);
                $etapa = $this->db->get('obra_etapas')->row();
                if ($etapa && $etapa->percentual_concluido >= 100) {
                    log_message('info', 'Etapa ' . $etapa->numero_etapa . ' da obra ' . $atividade->obra_id . ' concluída.');
                }
            }

        }

        return $result;
    }

    /**
     * Registrar impedimento
     */
    public function registrarImpedimento($id, $tecnico_id, $dados)
    {
        $atividade = $this->getById($id);

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
     * Adicionar histórico (wrapper público para registrarHistorico)
     */
    public function adicionarHistorico($atividade_id, $tipo, $descricao, $dados = [])
    {
        $tecnico_id = $dados['tecnico_id'] ?? $this->session->userdata('idUsuarios');
        return $this->registrarHistorico($atividade_id, $tipo, $tecnico_id, array_merge(['descricao' => $descricao], $dados));
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
    public function atualizarProgressoEtapa($etapa_id)
    {
        if (!$this->tabelaExiste('obra_atividades') || !$this->tabelaExiste('obra_etapas')) {
            return false;
        }

        try {
            // Calcular média de percentual de TODAS as atividades da etapa
            $this->db->select_avg('percentual_concluido', 'media_percentual');
            $this->db->where('etapa_id', $etapa_id);
            $query = $this->db->get('obra_atividades');
            $result = $query ? $query->row() : null;

            $percentual = $result && $result->media_percentual !== null ? round($result->media_percentual) : 0;
            $percentual = min(max($percentual, 0), 100);

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
     * Reabrir atividade para reatendimento
     * Cria uma nova instância de execução mantendo o histórico
     */
    public function reabrirParaReatendimento($id, $tecnico_id, $motivo = null)
    {
        $atividade = $this->getById($id);
        if (!$atividade) {
            return false;
        }

        // Atualizar status para reaberta
        $update_data = [
            'status' => 'reaberta',
            'data_conclusao' => null,
            'percentual_concluido' => 0,
            'hora_fim' => null,
        ];

        $result = $this->update($id, $update_data);

        if ($result) {
            // Registrar no histórico
            $this->registrarHistorico($id, 'reatendimento', $tecnico_id, [
                'descricao' => 'Atividade reaberta para reatendimento: ' . ($motivo ?? 'Sem motivo especificado'),
            ]);
        }

        return $result;
    }

    /**
     * Buscar histórico de reatendimentos de uma atividade
     */
    public function getReatendimentos($obra_atividade_id)
    {
        if (!$this->tabelaExiste('obra_atividades_historico')) {
            return [];
        }

        try {
            $this->db->select('obra_atividades_historico.*, u.nome as tecnico_nome');
            $this->db->from('obra_atividades_historico');
            $this->db->join('usuarios u', 'u.idUsuarios = obra_atividades_historico.tecnico_id', 'left');
            $this->db->where('obra_atividades_historico.atividade_id', $obra_atividade_id);
            $this->db->where('obra_atividades_historico.tipo_alteracao', 'reatendimento');
            $this->db->order_by('obra_atividades_historico.created_at', 'DESC');
            $query = $this->db->get();
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar reatendimentos: ' . $e->getMessage());
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
