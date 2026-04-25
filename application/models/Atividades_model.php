<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model para gerenciamento de Atividades de Técnicos
 * Registra cada atividade específica realizada durante um atendimento
 */
class Atividades_model extends CI_Model
{
    protected $table = 'os_atividades';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Inicia uma nova atividade
     */
    public function iniciar($dados)
    {
        $padrao = [
            'hora_inicio' => date('Y-m-d H:i:s'),
            'status' => 'em_andamento',
            'concluida' => 0,
        ];

        $dados = array_merge($padrao, $dados);

        $this->db->insert($this->table, $dados);

        // Verificar erro
        $error = $this->db->error();
        if ($error['code'] != 0) {
            log_message('error', 'Erro ao iniciar atividade: ' . print_r($error, true) . ' Dados: ' . print_r($dados, true));
            return false;
        }

        return $this->db->insert_id();
    }

    /**
     * Finaliza uma atividade
     */
    public function finalizar($atividade_id, $dados = [])
    {
        $atividade = $this->getById($atividade_id);
        if (!$atividade) {
            return false;
        }

        $hora_fim = date('Y-m-d H:i:s');

        // Calcula duração
        $duracao_minutos = $this->calcularDuracao($atividade->hora_inicio, $hora_fim, $atividade_id);

        $update = array_merge($dados, [
            'hora_fim' => $hora_fim,
            'status' => 'finalizada',
            'duracao_minutos' => $duracao_minutos,
        ]);

        $this->db->where('idAtividade', $atividade_id);
        return $this->db->update($this->table, $update);
    }

    /**
     * Pausa uma atividade
     */
    public function pausar($atividade_id, $motivo = null, $observacao = null)
    {
        $this->db->where('idAtividade', $atividade_id);
        $result = $this->db->update($this->table, [
            'status' => 'pausada',
            'pausado_em' => date('Y-m-d H:i:s'),
        ]);

        // Registra na tabela de pausas (se existir)
        if ($this->db->table_exists('atividades_pausas')) {
            try {
                $this->db->insert('atividades_pausas', [
                    'atividade_id' => $atividade_id,
                    'pausa_inicio' => date('Y-m-d H:i:s'),
                    'motivo' => $motivo,
                    'observacao' => $observacao,
                ]);
            } catch (Exception $e) {
                log_message('error', 'Erro ao registrar pausa: ' . $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Retoma uma atividade pausada
     */
    public function retomar($atividade_id)
    {
        // Atualiza a pausa mais recente (se a tabela existir)
        if ($this->db->table_exists('atividades_pausas')) {
            try {
                $this->db->where('atividade_id', $atividade_id);
                $this->db->where('pausa_fim IS NULL');
                $this->db->order_by('idPausa', 'DESC');
                $this->db->limit(1);
                $this->db->update('atividades_pausas', [
                    'pausa_fim' => date('Y-m-d H:i:s'),
                ]);
            } catch (Exception $e) {
                log_message('error', 'Erro ao atualizar pausa: ' . $e->getMessage());
            }
        }

        // Atualiza a atividade
        $this->db->where('idAtividade', $atividade_id);
        return $this->db->update($this->table, [
            'status' => 'em_andamento',
        ]);
    }

    /**
     * Obtém atividade por ID
     */
    public function getById($id)
    {
        $this->db->where('idAtividade', $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Obtém atividade com detalhes completos
     */
    public function getByIdCompleto($id)
    {
        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           atividades_tipos.icone as tipo_icone,
                           atividades_tipos.cor as tipo_cor,
                           usuarios.nome as nome_tecnico,
                           obra_etapas.nome as etapa_nome');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os_atividades.tecnico_id', 'left');
        $this->db->join('obra_etapas', 'obra_etapas.id = os_atividades.etapa_id', 'left');
        $this->db->where('os_atividades.idAtividade', $id);

        $atividade = $this->db->get()->row();

        if ($atividade) {
            $atividade->materiais = $this->getMateriais($id);
            $atividade->fotos = $this->getFotos($id);
            $atividade->pausas = $this->getPausas($id);
            $atividade->checklist = $this->getChecklist($id);
        }

        return $atividade;
    }

    /**
     * Lista atividades por OS
     */
    public function listarPorOS($os_id, $filtros = [])
    {
        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           atividades_tipos.icone as tipo_icone,
                           atividades_tipos.cor as tipo_cor,
                           usuarios.nome as nome_tecnico');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os_atividades.tecnico_id', 'left');
        $this->db->where('os_atividades.os_id', $os_id);
        $this->db->where('os_atividades.obra_id IS NULL');  // Só atividades não vinculadas a obras


        if (isset($filtros['status'])) {
            $this->db->where('os_atividades.status', $filtros['status']);
        }

        if (isset($filtros['tecnico_id'])) {
            $this->db->where('os_atividades.tecnico_id', $filtros['tecnico_id']);
        }

        if (isset($filtros['data_inicio'])) {
            $this->db->where('DATE(os_atividades.hora_inicio) >=', $filtros['data_inicio']);
        }

        if (isset($filtros['data_fim'])) {
            $this->db->where('DATE(os_atividades.hora_inicio) <=', $filtros['data_fim']);
        }

        $this->db->order_by('os_atividades.hora_inicio', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Lista atividades por técnico
     */
    public function listarPorTecnico($tecnico_id, $filtros = [], $limite = null)
    {
        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           os.descricaoProduto as os_equipamento,
                           os.defeito as os_defeito,
                           clientes.nomeCliente');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('os', 'os.idOs = os_atividades.os_id', 'left');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');
        $this->db->where('os_atividades.tecnico_id', $tecnico_id);

        if (isset($filtros['status'])) {
            $this->db->where('os_atividades.status', $filtros['status']);
        }

        if (isset($filtros['data'])) {
            $this->db->where('DATE(os_atividades.hora_inicio)', $filtros['data']);
        }

        $this->db->order_by('os_atividades.hora_inicio', 'DESC');

        if ($limite) {
            $this->db->limit($limite);
        }

        return $this->db->get()->result();
    }

    /**
     * Obtém atividade em andamento de um técnico
     */
    public function getAtividadeEmAndamento($tecnico_id)
    {
        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           atividades_tipos.icone as tipo_icone,
                           atividades_tipos.cor as tipo_cor,
                           os.descricaoProduto as os_equipamento,
                           clientes.nomeCliente,
                           clientes.telefone,
                           clientes.celular');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('os', 'os.idOs = os_atividades.os_id', 'left');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');
        $this->db->where('os_atividades.tecnico_id', $tecnico_id);
        $this->db->where_in('os_atividades.status', ['em_andamento', 'pausada']);
        $this->db->order_by('os_atividades.idAtividade', 'DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Verifica se técnico tem atividade em andamento (incluindo pausadas)
     */
    public function hasAtividadeEmAndamento($tecnico_id)
    {
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where_in('status', ['em_andamento', 'pausada']);
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Atualiza atividade
     */
    public function atualizar($atividade_id, $dados)
    {
        $this->db->where('idAtividade', $atividade_id);
        return $this->db->update($this->table, $dados);
    }

    /**
     * Exclui atividade (com verificação de permissões)
     */
    public function excluir($atividade_id, $tecnico_id = null)
    {
        $this->db->where('idAtividade', $atividade_id);

        if ($tecnico_id) {
            $this->db->where('tecnico_id', $tecnico_id);
        }

        // Só permite excluir se ainda não finalizou ou se finalizou há pouco tempo
        $this->db->group_start();
        $this->db->where('status', 'em_andamento');
        $this->db->or_where('hora_fim >', date('Y-m-d H:i:s', strtotime('-1 hour')));
        $this->db->group_end();

        return $this->db->delete($this->table);
    }

    /**
     * Adiciona material à atividade
     */
    public function adicionarMaterial($atividade_id, $dados)
    {
        $dados['atividade_id'] = $atividade_id;
        return $this->db->insert('atividades_materiais', $dados);
    }

    /**
     * Lista materiais de uma atividade
     */
    public function getMateriais($atividade_id)
    {
        $this->db->select('atividades_materiais.*, produtos.descricao as produto_descricao');
        $this->db->from('atividades_materiais');
        $this->db->join('produtos', 'produtos.idProdutos = atividades_materiais.produto_id', 'left');
        $this->db->where('atividade_id', $atividade_id);
        return $this->db->get()->result();
    }

    /**
     * Adiciona foto à atividade
     */
    public function adicionarFoto($atividade_id, $dados)
    {
        $dados['atividade_id'] = $atividade_id;
        return $this->db->insert('atividades_fotos', $dados);
    }

    /**
     * Lista fotos de uma atividade
     */
    public function getFotos($atividade_id)
    {
        $this->db->where('atividade_id', $atividade_id);
        $this->db->order_by('data_hora', 'ASC');
        return $this->db->get('atividades_fotos')->result();
    }

    /**
     * Lista pausas de uma atividade
     */
    public function getPausas($atividade_id)
    {
        $this->db->where('atividade_id', $atividade_id);
        $this->db->order_by('pausa_inicio', 'ASC');
        return $this->db->get('atividades_pausas')->result();
    }

    /**
     * Lista checklist de uma atividade
     */
    public function getChecklist($atividade_id)
    {
        $this->db->where('atividade_id', $atividade_id);
        $this->db->order_by('ordem', 'ASC');
        return $this->db->get('atividades_checklist')->result();
    }

    /**
     * Calcula duração em minutos, descontando pausas
     */
    private function calcularDuracao($hora_inicio, $hora_fim, $atividade_id)
    {
        $inicio = strtotime($hora_inicio);
        $fim = strtotime($hora_fim);
        $duracao_total = ($fim - $inicio) / 60; // em minutos

        // Desconta pausas
        $pausas = $this->getPausas($atividade_id);
        $tempo_pausado = 0;

        foreach ($pausas as $pausa) {
            if ($pausa->pausa_fim) {
                $pausa_inicio = strtotime($pausa->pausa_inicio);
                $pausa_fim = strtotime($pausa->pausa_fim);
                $tempo_pausado += ($pausa_fim - $pausa_inicio) / 60;
            }
        }

        return round($duracao_total - $tempo_pausado);
    }

    /**
     * Obtém estatísticas de atividades por técnico
     */
    public function getEstatisticasTecnico($tecnico_id, $data_inicio = null, $data_fim = null)
    {
        if (!$data_inicio) {
            $data_inicio = date('Y-m-01'); // Início do mês
        }
        if (!$data_fim) {
            $data_fim = date('Y-m-t'); // Fim do mês
        }

        // Total de atividades
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('DATE(hora_inicio) >=', $data_inicio);
        $this->db->where('DATE(hora_inicio) <=', $data_fim);
        $total = $this->db->count_all_results($this->table);

        // Atividades concluídas
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('status', 'finalizada');
        $this->db->where('concluida', 1);
        $this->db->where('DATE(hora_inicio) >=', $data_inicio);
        $this->db->where('DATE(hora_inicio) <=', $data_fim);
        $concluidas = $this->db->count_all_results($this->table);

        // Total de minutos trabalhados
        $this->db->select_sum('duracao_minutos', 'total');
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('status', 'finalizada');
        $this->db->where('DATE(hora_inicio) >=', $data_inicio);
        $this->db->where('DATE(hora_inicio) <=', $data_fim);
        $minutos = $this->db->get($this->table)->row()->total;

        // Atividades por categoria
        $this->db->select('categoria, COUNT(*) as total, SUM(duracao_minutos) as minutos');
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('status', 'finalizada');
        $this->db->where('DATE(hora_inicio) >=', $data_inicio);
        $this->db->where('DATE(hora_inicio) <=', $data_fim);
        $this->db->group_by('categoria');
        $por_categoria = $this->db->get($this->table)->result();

        return [
            'total_atividades' => $total,
            'concluidas' => $concluidas,
            'tempo_total_minutos' => $minutos ?: 0,
            'tempo_total_horas' => round(($minutos ?: 0) / 60, 2),
            'por_categoria' => $por_categoria,
        ];
    }

    /**
     * Obtém resumo do dia para o técnico
     */
    public function getResumoDia($tecnico_id, $data = null)
    {
        if (!$data) {
            $data = date('Y-m-d');
        }

        // Atividades em andamento
        $em_andamento = $this->getAtividadeEmAndamento($tecnico_id);

        // Atividades do dia
        $atividades = $this->listarPorTecnico($tecnico_id, ['data' => $data]);

        // Calcula tempo total trabalhado hoje
        $tempo_trabalhado = 0;
        $atividades_finalizadas = [];

        foreach ($atividades as $atv) {
            if ($atv->status == 'finalizada' && $atv->duracao_minutos) {
                $tempo_trabalhado += $atv->duracao_minutos;
                $atividades_finalizadas[] = $atv;
            }
        }

        return [
            'data' => $data,
            'em_andamento' => $em_andamento,
            'total_atividades' => count($atividades),
            'atividades_finalizadas' => $atividades_finalizadas,
            'tempo_trabalhado_minutos' => $tempo_trabalhado,
            'tempo_trabalhado_horas' => round($tempo_trabalhado / 60, 2),
        ];
    }

    /**
     * Gera relatório detalhado de atividades
     */
    public function gerarRelatorio($filtros = [])
    {
        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           os.descricaoProduto as os_equipamento,
                           os.defeito as os_defeito,
                           clientes.nomeCliente,
                           usuarios.nome as nome_tecnico');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('os', 'os.idOs = os_atividades.os_id', 'left');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os_atividades.tecnico_id', 'left');

        if (isset($filtros['data_inicio'])) {
            $this->db->where('DATE(os_atividades.hora_inicio) >=', $filtros['data_inicio']);
        }

        if (isset($filtros['data_fim'])) {
            $this->db->where('DATE(os_atividades.hora_inicio) <=', $filtros['data_fim']);
        }

        if (isset($filtros['tecnico_id'])) {
            $this->db->where('os_atividades.tecnico_id', $filtros['tecnico_id']);
        }

        if (isset($filtros['categoria'])) {
            $this->db->where('os_atividades.categoria', $filtros['categoria']);
        }

        if (isset($filtros['concluida'])) {
            $this->db->where('os_atividades.concluida', $filtros['concluida']);
        }

        $this->db->order_by('os_atividades.hora_inicio', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Lista atividades por Obra (integração com sistema de obras)
     */
    public function listarPorObra($obra_id, $filtros = [], $limite = null)
    {
        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           atividades_tipos.icone as tipo_icone,
                           atividades_tipos.cor as tipo_cor,
                           usuarios.nome as nome_tecnico');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os_atividades.tecnico_id', 'left');
        $this->db->where('os_atividades.obra_id', $obra_id);
        $this->db->where('os_atividades.obra_id IS NOT NULL');

        if (isset($filtros['status'])) {
            $this->db->where('os_atividades.status', $filtros['status']);
        }

        if (isset($filtros['tecnico_id'])) {
            // Incluir atividades onde o técnico é o responsável (incluindo reatendimentos)
            $this->db->where('os_atividades.tecnico_id', $filtros['tecnico_id']);
        }

        if (isset($filtros['data_inicio'])) {
            $this->db->where('DATE(os_atividades.hora_inicio) >=', $filtros['data_inicio']);
        }

        if (isset($filtros['data_fim'])) {
            $this->db->where('DATE(os_atividades.hora_inicio) <=', $filtros['data_fim']);
        }

        // Ordenação: reabertas (reatendimentos) primeiro, depois por hora_inicio
        // Usando FIELD para dar prioridade ao status 'reaberta'
        $this->db->order_by("FIELD(os_atividades.status, 'reaberta', 'em_andamento', 'pausada', 'finalizada')", '', FALSE);
        $this->db->order_by('os_atividades.hora_inicio', 'DESC');

        if ($limite) {
            $this->db->limit($limite);
        }

        return $this->db->get()->result();
    }

    /**
     * Obtém estatísticas de atividades por obra
     */
    public function getEstatisticasPorObra($obra_id)
    {
        // Total de atividades
        $this->db->where('obra_id', $obra_id);
        $total = $this->db->count_all_results($this->table);

        // Atividades concluídas
        $this->db->where('obra_id', $obra_id);
        $this->db->where('status', 'finalizada');
        $this->db->where('concluida', 1);
        $concluidas = $this->db->count_all_results($this->table);

        // Total de minutos trabalhados
        $this->db->select_sum('duracao_minutos', 'total');
        $this->db->where('obra_id', $obra_id);
        $this->db->where('status', 'finalizada');
        $minutos = $this->db->get($this->table)->row()->total;

        // Atividades em andamento
        $this->db->where('obra_id', $obra_id);
        $this->db->where('status', 'em_andamento');
        $em_andamento = $this->db->count_all_results($this->table);

        return [
            'total_atividades' => $total,
            'concluidas' => $concluidas,
            'pendentes' => $total - $concluidas,
            'em_andamento' => $em_andamento,
            'tempo_total_minutos' => $minutos ?: 0,
            'tempo_total_horas' => round(($minutos ?: 0) / 60, 2),
        ];
    }

    /**
     * Inicia atividade em uma obra (para integração com obras)
     */
    public function iniciarNaObra($obra_id, $etapa_id, $dados)
    {
        $padrao = [
            'obra_id' => $obra_id,
            'etapa_id' => $etapa_id,
            'hora_inicio' => date('Y-m-d H:i:s'),
            'status' => 'em_andamento',
            'concluida' => 0,
        ];

        $dados = array_merge($padrao, $dados);

        $this->db->insert($this->table, $dados);
        return $this->db->insert_id();
    }

    /**
     * Vincula atividade existente a uma obra
     */
    public function vincularObra($atividade_id, $obra_id, $etapa_id = null)
    {
        $update = [
            'obra_id' => $obra_id,
        ];

        if ($etapa_id) {
            $update['etapa_id'] = $etapa_id;
        }

        $this->db->where('idAtividade', $atividade_id);
        return $this->db->update($this->table, $update);
    }

    // =========================================================
    // MÉTODOS DE INTEGRAÇÃO COM ETAPAS DA OBRA
    // =========================================================

    /**
     * Lista atividades por obra agrupadas por etapa
     */
    public function listarPorObraAgrupadoPorEtapa($obra_id, $limite_por_etapa = 20)
    {
        // Busca etapas da obra
        $this->db->where('obra_id', $obra_id);
        $etapas = $this->db->get('obra_etapas')->result();

        $resultado = [];

        foreach ($etapas as $etapa) {
            $this->db->select('os_atividades.*,
                                   atividades_tipos.nome as tipo_nome,
                                   atividades_tipos.icone as tipo_icone,
                                   atividades_tipos.cor as tipo_cor,
                                   usuarios.nome as nome_tecnico,
                                   obra_atividades.titulo as atividade_planejada_titulo');
            $this->db->from($this->table);
            $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
            $this->db->join('usuarios', 'usuarios.idUsuarios = os_atividades.tecnico_id', 'left');
            $this->db->join('obra_atividades', 'obra_atividades.id = os_atividades.obra_atividade_id', 'left');
            $this->db->where('os_atividades.obra_id', $obra_id);
            $this->db->where('os_atividades.etapa_id', $etapa->id);
            $this->db->order_by('os_atividades.hora_inicio', 'DESC');
            $this->db->limit($limite_por_etapa);

            $atividades = $this->db->get()->result();

            $resultado[] = [
                'etapa' => $etapa,
                'atividades' => $atividades,
                'total_atividades' => count($atividades),
            ];
        }

        return $resultado;
    }

    /**
     * Vincula atividade realizada a uma etapa e opcionalmente a uma atividade planejada
     */
    public function vincularEtapa($atividade_id, $etapa_id, $obra_id, $obra_atividade_id = null)
    {
        $dados = [
            'etapa_id' => $etapa_id,
            'obra_id' => $obra_id,
        ];

        if ($obra_atividade_id) {
            $dados['obra_atividade_id'] = $obra_atividade_id;
        }

        $this->db->where('idAtividade', $atividade_id);
        return $this->db->update($this->table, $dados);
    }

    /**
     * Vincula atividade realizada a uma atividade planejada específica
     */
    public function vincularAtividadePlanejada($atividade_realizada_id, $obra_atividade_id, $etapa_id, $obra_id, $tecnico_id)
    {
        // Atualiza a atividade realizada
        $this->db->where('idAtividade', $atividade_realizada_id);
        $this->db->update($this->table, [
            'obra_atividade_id' => $obra_atividade_id,
            'etapa_id' => $etapa_id,
            'obra_id' => $obra_id,
        ]);

        // Insere registro na tabela de vínculo
        $this->db->insert('obra_atividades_vinculo', [
            'atividade_realizada_id' => $atividade_realizada_id,
            'obra_atividade_id' => $obra_atividade_id,
            'etapa_id' => $etapa_id,
            'obra_id' => $obra_id,
            'tecnico_id' => $tecnico_id,
            'data_vinculo' => date('Y-m-d H:i:s'),
        ]);

        return $this->db->insert_id();
    }

    /**
     * Obtém estatísticas de atividades por etapa da obra
     */
    public function getEstatisticasPorEtapa($etapa_id)
    {
        // Total de atividades na etapa
        $this->db->where('etapa_id', $etapa_id);
        $total = $this->db->count_all_results($this->table);

        // Atividades concluídas
        $this->db->where('etapa_id', $etapa_id);
        $this->db->where('status', 'finalizada');
        $this->db->where('concluida', 1);
        $concluidas = $this->db->count_all_results($this->table);

        // Total de minutos trabalhados na etapa
        $this->db->select_sum('duracao_minutos', 'total');
        $this->db->where('etapa_id', $etapa_id);
        $this->db->where('status', 'finalizada');
        $minutos = $this->db->get($this->table)->row()->total;

        // Atividades em andamento
        $this->db->where('etapa_id', $etapa_id);
        $this->db->where('status', 'em_andamento');
        $em_andamento = $this->db->count_all_results($this->table);

        // Calcula progresso (se houver atividades planejadas vinculadas)
        $progresso = 0;
        if ($total > 0) {
            $progresso = round(($concluidas / $total) * 100);
        }

        return [
            'etapa_id' => $etapa_id,
            'total_atividades' => $total,
            'concluidas' => $concluidas,
            'em_andamento' => $em_andamento,
            'pendentes' => $total - $concluidas - $em_andamento,
            'tempo_total_minutos' => $minutos ?: 0,
            'tempo_total_horas' => round(($minutos ?: 0) / 60, 2),
            'progresso_percentual' => $progresso,
        ];
    }

    /**
     * Obtém atividades de uma etapa específica
     */
    public function listarPorEtapa($etapa_id, $filtros = [])
    {
        $this->db->select('os_atividades.*,
                               atividades_tipos.nome as tipo_nome,
                               atividades_tipos.icone as tipo_icone,
                               atividades_tipos.cor as tipo_cor,
                               usuarios.nome as nome_tecnico,
                               obra_atividades.titulo as atividade_planejada_titulo');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os_atividades.tecnico_id', 'left');
        $this->db->join('obra_atividades', 'obra_atividades.id = os_atividades.obra_atividade_id', 'left');
        $this->db->where('os_atividades.etapa_id', $etapa_id);

        if (isset($filtros['status'])) {
            $this->db->where('os_atividades.status', $filtros['status']);
        }

        if (isset($filtros['tecnico_id'])) {
            $this->db->where('os_atividades.tecnico_id', $filtros['tecnico_id']);
        }

        if (isset($filtros['data'])) {
            $this->db->where('DATE(os_atividades.hora_inicio)', $filtros['data']);
        }

        $this->db->order_by('os_atividades.hora_inicio', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Obtém a atividade em andamento de um técnico em uma obra específica
     */
    public function getAtividadeEmAndamentoNaObra($tecnico_id, $obra_id)
    {
        $this->db->select('os_atividades.*,
                               atividades_tipos.nome as tipo_nome,
                               atividades_tipos.icone as tipo_icone,
                               atividades_tipos.cor as tipo_cor,
                               obra_etapas.nome as etapa_nome');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('obra_etapas', 'obra_etapas.id = os_atividades.etapa_id', 'left');
        $this->db->where('os_atividades.tecnico_id', $tecnico_id);
        $this->db->where('os_atividades.obra_id', $obra_id);
        $this->db->where_in('os_atividades.status', ['em_andamento', 'pausada', 'impedimento']);
        $this->db->order_by('os_atividades.idAtividade', 'DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Cria um registro de reatendimento quando uma atividade é reaberta
     * Isso permite reexecutar a atividade preservando o histórico anterior
     */
    public function criarReatendimento($dados)
    {
        // Verificar se a coluna reatendimento existe, se não, criar
        $this->verificarECriarColunaReatendimento();

        // Dados padrão para reatendimento
        $padrao = [
            'hora_inicio' => null, // Aguardando novo início
            'hora_fim' => null,
            'status' => 'reaberta',
            'concluida' => 0,
            'obra_atividade_id' => null,
            'obra_id' => null,
            'etapa_id' => null,
            'tipo_id' => null,
            'tecnico_id' => null,
            'descricao' => 'Reatendimento criado automaticamente',
            'motivo_reabertura' => null,
        ];

        $dados = array_merge($padrao, $dados);

        // Inserir na tabela de atividades
        $this->db->insert($this->table, $dados);
        $reatendimento_id = $this->db->insert_id();

        // Marcar como reatendimento usando metadata se a coluna não existir
        if ($reatendimento_id) {
            $this->marcarComoReatendimento($reatendimento_id);
            // Registrar no histórico da obra_atividade se houver vínculo
            if (!empty($dados['obra_atividade_id'])) {
                $this->registrarReatendimentoNoHistorico($dados['obra_atividade_id'], $reatendimento_id, $dados['motivo_reabertura']);
            }
        }

        return $reatendimento_id;
    }

    /**
     * Verifica e cria a coluna reatendimento se não existir
     */
    private function verificarECriarColunaReatendimento()
    {
        try {
            $colunas = $this->db->list_fields($this->table);
            if (!in_array('reatendimento', $colunas)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN reatendimento TINYINT(1) DEFAULT 0 AFTER concluida");
                log_message('debug', 'Coluna reatendimento criada na tabela ' . $this->table);
            }
            if (!in_array('motivo_reabertura', $colunas)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN motivo_reabertura TEXT NULL AFTER descricao");
                log_message('debug', 'Coluna motivo_reabertura criada na tabela ' . $this->table);
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao verificar/criar colunas: ' . $e->getMessage());
        }
    }

    /**
     * Marca uma atividade como reatendimento
     */
    private function marcarComoReatendimento($atividade_id)
    {
        try {
            $colunas = $this->db->list_fields($this->table);
            if (in_array('reatendimento', $colunas)) {
                $this->db->where('idAtividade', $atividade_id);
                $this->db->update($this->table, ['reatendimento' => 1]);
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao marcar reatendimento: ' . $e->getMessage());
        }
    }

    /**
     * Registra o reatendimento no histórico da atividade planejada
     */
    private function registrarReatendimentoNoHistorico($obra_atividade_id, $reatendimento_id, $motivo = null)
    {
        if (!$this->db->table_exists('obra_atividades_historico')) {
            return false;
        }

        try {
            // Verificar se a coluna reatendimento_id existe
            $colunas = $this->db->list_fields('obra_atividades_historico');
            $hasReatendimentoId = in_array('reatendimento_id', $colunas);

            $data = [
                'atividade_id' => $obra_atividade_id,
                'tipo_alteracao' => 'reatendimento',
                'descricao' => 'Reatendimento criado: ' . ($motivo ?? 'Reabertura para reexecução'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($hasReatendimentoId) {
                $data['reatendimento_id'] = $reatendimento_id;
            }

            $this->db->insert('obra_atividades_historico', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            log_message('error', 'Erro ao registrar reatendimento no histórico: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca histórico completo de execuções (incluindo reatendimentos) de uma atividade
     */
    public function getHistoricoExecucoes($obra_atividade_id)
    {
        // Verificar se coluna reatendimento existe
        $colunas = $this->db->list_fields($this->table);
        $hasReatendimento = in_array('reatendimento', $colunas);

        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           atividades_tipos.icone as tipo_icone,
                           atividades_tipos.cor as tipo_cor,
                           usuarios.nome as nome_tecnico,
                           obra_etapas.nome as etapa_nome');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os_atividades.tecnico_id', 'left');
        $this->db->join('obra_etapas', 'obra_etapas.id = os_atividades.etapa_id', 'left');
        $this->db->where('os_atividades.obra_atividade_id', $obra_atividade_id);
        $this->db->order_by('os_atividades.hora_inicio', 'DESC');

        $resultados = $this->db->get()->result();

        // Garantir que reatendimento seja preenchido
        if (!$hasReatendimento) {
            foreach ($resultados as $r) {
                // Se o status for 'reaberta', considerar como reatendimento
                $r->reatendimento = ($r->status === 'reaberta') ? 1 : 0;
            }
        }

        return $resultados;
    }

    /**
     * Lista reatendimentos de uma atividade planejada
     */
    public function listarReatendimentos($obra_atividade_id)
    {
        // Verificar se coluna reatendimento existe
        $colunas = $this->db->list_fields($this->table);
        $hasReatendimento = in_array('reatendimento', $colunas);

        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           atividades_tipos.icone as tipo_icone,
                           usuarios.nome as nome_tecnico,
                           obra_etapas.nome as etapa_nome');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os_atividades.tecnico_id', 'left');
        $this->db->join('obra_etapas', 'obra_etapas.id = os_atividades.etapa_id', 'left');
        $this->db->where('os_atividades.obra_atividade_id', $obra_atividade_id);

        // Se a coluna reatendimento existir, filtrar por ela, senão usar status 'reaberta'
        if ($hasReatendimento) {
            $this->db->where('os_atividades.reatendimento', 1);
        } else {
            $this->db->where('os_atividades.status', 'reaberta');
        }

        $this->db->order_by('os_atividades.hora_inicio', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Inicia um reatendimento (reatendimento em execução)
     * Transforma o status de 'reaberta' para 'em_andamento' e inicia o contador
     */
    public function iniciarReatendimento($reatendimento_id, $tecnico_id, $dados = [])
    {
        $atividade = $this->getById($reatendimento_id);
        if (!$atividade || $atividade->status !== 'reaberta') {
            log_message('error', 'Tentativa de iniciar reatendimento ID ' . $reatendimento_id . ' com status inválido: ' . ($atividade->status ?? 'null'));
            return false;
        }

        $update = [
            'status' => 'em_andamento',
            'hora_inicio' => date('Y-m-d H:i:s'),
            'tecnico_id' => $tecnico_id,
        ];

        if (!empty($dados['latitude'])) {
            $update['checkin_lat'] = $dados['latitude'];
        }
        if (!empty($dados['longitude'])) {
            $update['checkin_lng'] = $dados['longitude'];
        }

        $this->db->where('idAtividade', $reatendimento_id);
        $result = $this->db->update($this->table, $update);

        if ($result) {
            log_message('debug', 'Reatendimento ID ' . $reatendimento_id . ' iniciado pelo técnico ' . $tecnico_id);
        } else {
            log_message('error', 'Falha ao iniciar reatendimento ID ' . $reatendimento_id);
        }

        return $result;
    }

    /**
     * Finaliza um reatendimento
     * Similar ao finalizar() mas específico para reatendimentos
     */
    public function finalizarReatendimento($reatendimento_id, $dados = [])
    {
        $atividade = $this->getById($reatendimento_id);
        if (!$atividade || !in_array($atividade->status, ['em_andamento', 'pausada'])) {
            return false;
        }

        $hora_fim = date('Y-m-d H:i:s');

        // Calcular duração
        $duracao_minutos = $this->calcularDuracao($atividade->hora_inicio, $hora_fim, $reatendimento_id);

        $update = [
            'hora_fim' => $hora_fim,
            'status' => 'finalizada',
            'concluida' => 1,
            'duracao_minutos' => $duracao_minutos,
        ];

        if (!empty($dados['observacoes'])) {
            $update['observacoes'] = $dados['observacoes'];
        }

        $this->db->where('idAtividade', $reatendimento_id);
        return $this->db->update($this->table, $update);
    }

    /**
     * Obtém registros de execução (Hora Início/Fim) vinculados a uma atividade planejada
     */
    public function getRegistrosPorObraAtividade($obra_atividade_id)
    {
        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           atividades_tipos.icone as tipo_icone,
                           obra_etapas.nome as etapa_nome,
                           obra_etapas.numero_etapa');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('obra_etapas', 'obra_etapas.id = os_atividades.etapa_id', 'left');
        $this->db->where('os_atividades.obra_atividade_id', $obra_atividade_id);
        $this->db->order_by('os_atividades.hora_inicio', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Obtém o registro de execução em andamento vinculado a uma obra_atividade
     */
    public function getRegistroEmAndamentoPorObraAtividade($obra_atividade_id, $tecnico_id)
    {
        $this->db->select('os_atividades.*,
                           atividades_tipos.nome as tipo_nome,
                           atividades_tipos.icone as tipo_icone,
                           obra_etapas.nome as etapa_nome');
        $this->db->from($this->table);
        $this->db->join('atividades_tipos', 'atividades_tipos.idTipo = os_atividades.tipo_id', 'left');
        $this->db->join('obra_etapas', 'obra_etapas.id = os_atividades.etapa_id', 'left');
        $this->db->where('os_atividades.obra_atividade_id', $obra_atividade_id);
        $this->db->where('os_atividades.tecnico_id', $tecnico_id);
        $this->db->where('os_atividades.status', 'em_andamento');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Obtém a última execução (mais recente) de uma obra_atividade
     * Usado para identificar o técnico que realizou a última execução
     */
    public function getUltimaExecucao($obra_atividade_id)
    {
        $this->db->select('os_atividades.*, u.nome as tecnico_nome');
        $this->db->from($this->table);
        $this->db->join('usuarios u', 'u.idUsuarios = os_atividades.tecnico_id', 'left');
        $this->db->where('os_atividades.obra_atividade_id', $obra_atividade_id);
        $this->db->where('os_atividades.tecnico_id IS NOT NULL');
        $this->db->order_by('os_atividades.hora_fim', 'DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Obtém status de atividade disponíveis para configuração
     */
    public function getStatusAtividade()
    {
        // Verificar se tabela existe
        if ($this->db->table_exists('atividade_status')) {
            $this->db->where('ativo', 1);
            $this->db->order_by('ordem', 'ASC');
            return $this->db->get('atividade_status')->result();
        }
        // Retornar valores padrão
        return [
            (object)['id' => 1, 'nome' => 'Agendada', 'descricao' => 'Atividade agendada para execução futura', 'cor' => '#95a5a6', 'icone' => 'bx-calendar', 'fluxo' => 'inicial'],
            (object)['id' => 2, 'nome' => 'Iniciada', 'descricao' => 'Atividade em execução', 'cor' => '#3498db', 'icone' => 'bx-play-circle', 'fluxo' => 'execucao'],
            (object)['id' => 3, 'nome' => 'Pausada', 'descricao' => 'Atividade temporariamente pausada', 'cor' => '#f39c12', 'icone' => 'bx-pause-circle', 'fluxo' => 'execucao'],
            (object)['id' => 4, 'nome' => 'Concluída', 'descricao' => 'Atividade finalizada com sucesso', 'cor' => '#27ae60', 'icone' => 'bx-check-circle', 'fluxo' => 'final'],
            (object)['id' => 5, 'nome' => 'Cancelada', 'descricao' => 'Atividade cancelada', 'cor' => '#e74c3c', 'icone' => 'bx-x-circle', 'fluxo' => 'final'],
            (object)['id' => 6, 'nome' => 'Reaberta', 'descricao' => 'Atividade reaberta para reatendimento', 'cor' => '#9b59b6', 'icone' => 'bx-refresh', 'fluxo' => 'especial'],
        ];
    }
}
