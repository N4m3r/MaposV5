<?php

/**
 * Model de Execução de OS pelo Técnico
 */
class Tec_os_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Buscar OS por ID
     */
    public function getOsById($os_id)
    {
        $this->db->where('idOs', $os_id);
        $query = $this->db->get('os');
        return $query ? $query->row() : null;
    }

    /**
     * Buscar cliente da OS
     */
    public function getClienteByOs($os_id)
    {
        // Reset query builder
        $this->db->reset_query();

        // Buscar primeiro a OS para obter o clientes_id
        $this->db->where('idOs', $os_id);
        $os_query = $this->db->get('os');

        if (!$os_query || !$os_query->row()) {
            log_message('error', 'getClienteByOs: OS não encontrada: ' . $os_id);
            return null;
        }

        $os = $os_query->row();

        // Se não tem cliente vinculado
        if (empty($os->clientes_id)) {
            log_message('error', 'getClienteByOs: OS ' . $os_id . ' não tem clientes_id');
            $cliente = new stdClass();
            $cliente->idClientes = null;
            $cliente->nomeCliente = 'Cliente não vinculado';
            $cliente->endereco = '';
            $cliente->telefone = '';
            $cliente->celular = '';
            $cliente->email = '';
            $cliente->documento = '';
            return $cliente;
        }

        // Reset query builder antes da próxima query
        $this->db->reset_query();

        // Buscar o cliente diretamente na tabela clientes
        $this->db->where('idClientes', $os->clientes_id);
        $cliente_query = $this->db->get('clientes');

        if (!$cliente_query || !$cliente_query->row()) {
            log_message('error', 'getClienteByOs: Cliente ID ' . $os->clientes_id . ' não encontrado na tabela');
            $cliente = new stdClass();
            $cliente->idClientes = $os->clientes_id;
            $cliente->nomeCliente = 'Cliente não encontrado (ID: ' . $os->clientes_id . ')';
            $cliente->endereco = '';
            $cliente->telefone = '';
            $cliente->celular = '';
            $cliente->email = '';
            $cliente->documento = '';
            return $cliente;
        }

        $cliente = $cliente_query->row();

        // Debug log
        log_message('debug', 'getClienteByOs: Cliente encontrado - ID: ' . $cliente->idClientes . ', Nome: ' . ($cliente->nomeCliente ?? 'VAZIO'));

        // Monta o endereço completo
        $endereco_parts = [];
        if (!empty($cliente->rua)) {
            $endereco = $cliente->rua;
            if (!empty($cliente->numero)) {
                $endereco .= ', ' . $cliente->numero;
            }
            $endereco_parts[] = $endereco;
        }
        if (!empty($cliente->complemento)) {
            $endereco_parts[] = $cliente->complemento;
        }
        if (!empty($cliente->bairro)) {
            $endereco_parts[] = $cliente->bairro;
        }
        if (!empty($cliente->cidade)) {
            $cidade_estado = $cliente->cidade;
            if (!empty($cliente->estado)) {
                $cidade_estado .= '/' . $cliente->estado;
            }
            $endereco_parts[] = $cidade_estado;
        }
        if (!empty($cliente->cep)) {
            $endereco_parts[] = 'CEP: ' . $cliente->cep;
        }

        $cliente->endereco = !empty($endereco_parts) ? implode(' - ', $endereco_parts) : 'Endereço não informado';

        return $cliente;
    }

    /**
     * Listar OS do técnico por status
     */
    public function getOsPorTecnico($tecnico_id, $status = 'todos')
    {
        log_message('debug', 'Buscando OS para tecnico_id: ' . $tecnico_id . ', status: ' . $status);

        $tecnico_id = (int) $tecnico_id;

        // CORRECAO: nomeCliente em vez de nome
        $sql = "SELECT os.*, c.nomeCliente as cliente_nome, c.telefone as cliente_telefone
                FROM os
                LEFT JOIN clientes c ON c.idClientes = os.clientes_id
                WHERE os.tecnico_responsavel = ?";

        $params = [$tecnico_id];
        if ($status !== 'todos') {
            $sql .= " AND os.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY os.dataInicial DESC";

        $query = $this->db->query($sql, $params);

        if (!$query) {
            log_message('error', 'Erro na query getOsPorTecnico');
            return [];
        }

        $result = $query->result();
        log_message('debug', 'Total de OS encontradas: ' . count($result));

        return $result;
    }

    /**
     * OS do dia
     */
    public function getOsDoDia($tecnico_id)
    {
        $hoje = date('Y-m-d');

        $sql = "SELECT os.*, c.nomeCliente as cliente_nome, c.telefone as cliente_telefone
                FROM os
                LEFT JOIN clientes c ON c.idClientes = os.clientes_id
                WHERE os.tecnico_responsavel = ?
                AND os.dataInicial = ?
                AND os.status IN ('Aberto', 'Em Andamento', 'Aguardando Peças')
                ORDER BY os.dataFinal ASC";

        $query = $this->db->query($sql, [$tecnico_id, $hoje]);

        if (!$query) {
            log_message('error', 'Erro na query getOsDoDia');
            return [];
        }

        return $query->result();
    }

    /**
     * OS pendentes
     */
    public function getOsPendentes($tecnico_id)
    {
        $sql = "SELECT os.*, c.nomeCliente as cliente_nome
                FROM os
                LEFT JOIN clientes c ON c.idClientes = os.clientes_id
                WHERE os.tecnico_responsavel = ?
                AND os.status IN ('Aberto', 'Em Andamento', 'Aguardando Peças')
                ORDER BY os.dataInicial ASC";

        $query = $this->db->query($sql, [$tecnico_id]);

        if (!$query) {
            log_message('error', 'Erro na query getOsPendentes');
            return [];
        }

        return $query->result();
    }

    /**
     * OS concluídas na semana
     */
    public function getOsConcluidasSemana($tecnico_id)
    {
        $inicio_semana = date('Y-m-d', strtotime('monday this week'));

        $this->db->where('tecnico_responsavel', $tecnico_id);
        $this->db->where('status', 'Finalizada');
        $this->db->where('dataFinal >=', $inicio_semana);

        return $this->db->count_all_results('os');
    }

    /**
     * Buscar produtos da OS
     */
    public function getProdutosOs($os_id)
    {
        $this->db->select('produtos_os.*, produtos.descricao, produtos.unidade, produtos.estoque');
        $this->db->from('produtos_os');
        $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
        $this->db->where('produtos_os.os_id', $os_id);

        $query = $this->db->get();

        if ($query === false) {
            log_message('error', 'Erro na query getProdutosOs: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }

    /**
     * Buscar serviços da OS (tabela padrão MAPOS ou tabela os_servicos do portal)
     */
    public function getServicosOs($os_id)
    {
        $this->db->reset_query();

        // Primeiro tenta buscar da tabela padrão servicos_os
        $this->db->select('servicos_os.*, servicos.nome as servico_nome, servicos.preco as servico_preco, servicos.codigo as servico_codigo, servicos.checklist_padrao');
        $this->db->from('servicos_os');
        $this->db->join('servicos', 'servicos.idServicos = servicos_os.servicos_id', 'left');
        $this->db->where('servicos_os.os_id', $os_id);

        $query = $this->db->get();

        if ($query === false) {
            log_message('error', 'Erro na query getServicosOs: ' . $this->db->last_query());
            return [];
        }

        $result = $query->result();
        log_message('debug', 'getServicosOs: OS ' . $os_id . ' - ' . count($result) . ' serviços encontrados em servicos_os');

        // Se não encontrou nada, tenta buscar da tabela os_servicos (portal do técnico)
        if (empty($result) && $this->db->table_exists('os_servicos')) {
            $this->db->reset_query();
            $this->db->select('os_servicos.id as idServicos_os, os_servicos.servico_id as servicos_id, os_servicos.quantidade, os_servicos.status, os_servicos.observacao, servicos.nome as servico_nome, servicos.preco as servico_preco, servicos.codigo as servico_codigo');
            $this->db->from('os_servicos');
            $this->db->join('servicos', 'servicos.idServicos = os_servicos.servico_id', 'left');
            $this->db->where('os_servicos.os_id', $os_id);

            $query2 = $this->db->get();
            if ($query2 !== false) {
                $result = $query2->result();
                log_message('debug', 'getServicosOs: OS ' . $os_id . ' - ' . count($result) . ' serviços encontrados em os_servicos');
            }
        }

        return $result;
    }

    /**
     * Adicionar serviço à OS
     */
    public function adicionarServicoOs($os_id, $servico_catalogo_id, $dados = [])
    {
        $data = [
            'os_id' => $os_id,
            'servico_catalogo_id' => $servico_catalogo_id,
            'quantidade' => $dados['quantidade'] ?? 1,
            'status' => 'pendente',
            'data_criacao' => date('Y-m-d H:i:s'),
        ];

        return $this->db->insert('os_servicos', $data);
    }

    /**
     * Iniciar execução da OS
     */
    public function iniciarExecucao($dados)
    {
        $this->db->insert('tec_os_execucao', $dados);

        $insert_id = $this->db->insert_id();
        $error = $this->db->error();

        if ($error['code'] != 0) {
            log_message('error', 'Tec_os_model::iniciarExecucao - Erro DB: ' . $error['message']);
        }

        log_message('info', 'Tec_os_model::iniciarExecucao - Insert ID: ' . $insert_id . ', Last Query: ' . $this->db->last_query());

        return $insert_id;
    }

    /**
     * Buscar execução por ID
     */
    public function getExecucaoById($execucao_id)
    {
        $this->db->where('id', $execucao_id);
        $query = $this->db->get('tec_os_execucao');
        return $query ? $query->row() : null;
    }

    /**
     * Buscar execução atual (em andamento)
     */
    public function getExecucaoAtual($os_id, $tecnico_id)
    {
        $this->db->where('os_id', $os_id);
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('checkout_horario IS NULL', null, false); // Em andamento = sem checkout
        $query = $this->db->get('tec_os_execucao');
        return $query ? $query->row() : null;
    }

    /**
     * Finalizar execução
     */
    public function finalizarExecucao($execucao_id, $dados)
    {
        $this->db->where('id', $execucao_id);
        return $this->db->update('tec_os_execucao', $dados);
    }

    /**
     * Adicionar foto à galeria
     */
    public function adicionarFotoGaleria($execucao_id, $foto)
    {
        log_message('info', 'adicionarFotoGaleria - Iniciando para execucao_id: ' . $execucao_id);

        $execucao = $this->getExecucaoById($execucao_id);

        if (!$execucao) {
            log_message('error', 'adicionarFotoGaleria - Execucao nao encontrada: ' . $execucao_id);
            return false;
        }

        $galeria = json_decode($execucao->fotos_galeria_json, true) ?: [];
        log_message('info', 'adicionarFotoGaleria - Fotos existentes: ' . count($galeria));

        $galeria[] = [
            'caminho' => $foto['caminho'],
            'descricao' => $foto['descricao'],
            'tipo' => $foto['tipo'],
            'lat' => $foto['lat'],
            'lng' => $foto['lng'],
            'data_hora' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id', $execucao_id);
        $resultado = $this->db->update('tec_os_execucao', [
            'fotos_galeria_json' => json_encode($galeria),
        ]);

        if (!$resultado) {
            log_message('error', 'adicionarFotoGaleria - Erro no update: ' . $this->db->error()['message']);
        } else {
            log_message('info', 'adicionarFotoGaleria - Update OK, affected rows: ' . $this->db->affected_rows());
        }

        return $resultado;
    }

    /**
     * Obter checklist de execução
     */
    public function getChecklistExecucao($os_id)
    {
        // Buscar execução
        $this->db->where('os_id', $os_id);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('tec_os_execucao');
        $execucao = $query ? $query->row() : null;

        if (!$execucao || !$execucao->checklist_json) {
            // Retornar checklist padrão baseado nos serviços da OS
            return $this->getChecklistPadraoOs($os_id);
        }

        return json_decode($execucao->checklist_json, true);
    }

    /**
     * Salvar item do checklist
     */
    public function salvarChecklistItem($execucao_id, $item_id, $dados)
    {
        $execucao = $this->getExecucaoById($execucao_id);

        if (!$execucao) {
            return false;
        }

        $checklist = json_decode($execucao->checklist_json, true) ?: [];

        $checklist[$item_id] = [
            'status' => $dados['status'],
            'observacao' => $dados['observacao'],
            'valor' => $dados['valor'],
            'data_hora' => $dados['data_hora'],
        ];

        // Calcular progresso
        $total = count($checklist);
        $concluidos = 0;
        foreach ($checklist as $item) {
            if ($item['status'] !== 'pendente') {
                $concluidos++;
            }
        }
        $progresso = $total > 0 ? round(($concluidos / $total) * 100) : 0;

        $this->db->where('id', $execucao_id);
        return $this->db->update('tec_os_execucao', [
            'checklist_json' => json_encode($checklist),
            'checklist_completude' => $progresso,
        ]);
    }

    /**
     * Obter checklist padrão baseado nos serviços da OS
     */
    private function getChecklistPadraoOs($os_id)
    {
        $servicos = $this->getServicosOs($os_id);
        $checklist = [];

        foreach ($servicos as $servico) {
            if (isset($servico->checklist_padrao) && $servico->checklist_padrao) {
                $items = json_decode($servico->checklist_padrao, true);
                if ($items) {
                    foreach ($items as $item) {
                        $item['servico'] = $servico->servico_nome;
                        $item['status'] = 'pendente';
                        $checklist[] = $item;
                    }
                }
            }
        }

        return $checklist;
    }

    /**
     * Buscar catálogo de serviços (usa tabela servicos do sistema)
     */
    public function getServicosCatalogo($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by('nome', 'ASC');

        $query = $this->db->get('servicos');

        if ($query === false) {
            log_message('error', 'Erro na query getServicosCatalogo: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }

    /**
     * Buscar serviço do catálogo por ID
     */
    public function getServicoCatalogoById($id)
    {
        $this->db->where('idServicos', $id);
        $query = $this->db->get('servicos');
        return $query ? $query->row() : null;
    }

    /**
     * Adicionar serviço ao catálogo (tabela servicos)
     */
    public function adicionarServicoCatalogo($dados)
    {
        $data = [
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'preco' => $dados['preco'] ?? 0,
        ];

        $this->db->insert('servicos', $data);
        return $this->db->insert_id();
    }

    /**
     * Atualizar serviço do catálogo
     */
    public function atualizarServicoCatalogo($id, $dados)
    {
        $this->db->where('idServicos', $id);
        return $this->db->update('servicos', $dados);
    }

    /**
     * Excluir serviço do catálogo
     */
    public function excluirServicoCatalogo($id)
    {
        $this->db->where('idServicos', $id);
        return $this->db->delete('servicos');
    }

    /**
     * Buscar templates de checklist
     */
    public function getChecklistTemplates($tipo = null)
    {
        if ($tipo) {
            $this->db->where('tipo_servico', $tipo);
        }

        $this->db->where('ativo', 1);

        $query = $this->db->get('tec_checklist_template');

        if ($query === false) {
            log_message('error', 'Erro na query getChecklistTemplates: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }

    /**
     * Buscar template por ID
     */
    public function getChecklistTemplateById($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('tec_checklist_template');
        return $query ? $query->row() : null;
    }

    /**
     * Adicionar template de checklist
     */
    public function adicionarChecklistTemplate($dados)
    {
        $data = [
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'tipo_servico' => $dados['tipo_servico'],
            'itens_json' => json_encode($dados['itens']),
            'ativo' => 1,
            'data_criacao' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('tec_checklist_template', $data);
        return $this->db->insert_id();
    }

    /**
     * Obter estatísticas de execução
     */
    public function getEstatisticasExecucao($tecnico_id = null, $periodo = 'mes')
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('tec_os_execucao')) {
            log_message('error', 'Tabela tec_os_execucao não existe');
            return [
                'total_execucoes' => 0,
                'media_tempo_horas' => 0,
            ];
        }

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

        $this->db->select('COUNT(*) as total');
        $this->db->where('data_checkin >=', $inicio);

        if ($tecnico_id) {
            $this->db->where('tecnico_id', $tecnico_id);
        }

        $query = $this->db->get('tec_os_execucao');
        if ($query === false) {
            log_message('error', 'Erro na query getEstatisticasExecucao (count): ' . $this->db->last_query());
            $total = 0;
        } else {
            $result = $query->row();
            $total = $result->total ?? 0;
        }

        // Média de tempo
        $this->db->select_avg('tempo_total_horas');
        $this->db->where('data_checkin >=', $inicio);
        $this->db->where('status', 'concluida');

        if ($tecnico_id) {
            $this->db->where('tecnico_id', $tecnico_id);
        }

        $query = $this->db->get('tec_os_execucao');
        if ($query === false) {
            log_message('error', 'Erro na query getEstatisticasExecucao (avg): ' . $this->db->last_query());
            $media_tempo = 0;
        } else {
            $result = $query->row();
            $media_tempo = $result->tempo_total_horas ?? 0;
        }

        return [
            'total_execucoes' => $total,
            'media_tempo_horas' => round($media_tempo, 2),
        ];
    }

    /**
     * Buscar histórico de execuções
     */
    public function getHistoricoExecucoes($os_id = null, $tecnico_id = null, $limit = 50)
    {
        $this->db->select('te.*, os.garantia as os_garantia, c.nomeCliente as cliente_nome');
        $this->db->from('tec_os_execucao te');
        $this->db->join('os os', 'os.idOs = te.os_id');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');

        if ($os_id) {
            $this->db->where('te.os_id', $os_id);
        }

        if ($tecnico_id) {
            $this->db->where('te.tecnico_id', $tecnico_id);
        }

        $this->db->order_by('te.data_checkin', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();

        if ($query === false) {
            log_message('error', 'Erro na query getHistoricoExecucoes: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }

    /**
     * Obter OS próximas (agendadas)
     */
    public function getOsProximas($tecnico_id, $dias = 7)
    {
        $hoje = date('Y-m-d');
        $futuro = date('Y-m-d', strtotime("+{$dias} days"));

        $this->db->select('os.*, c.nomeCliente as cliente_nome, c.telefone as cliente_telefone');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->where('os.tecnico_responsavel', $tecnico_id);
        $this->db->where('os.dataInicial >=', $hoje);
        $this->db->where('os.dataInicial <=', $futuro);
        $this->db->where_in('os.status', ['Aberto', 'Aprovado']);
        $this->db->order_by('os.dataInicial', 'ASC');

        $query = $this->db->get();

        if ($query === false) {
            log_message('error', 'Erro na query getOsProximas: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }

    /**
     * Pesquisar OS
     */
    public function pesquisarOs($termo, $tecnico_id = null)
    {
        $this->db->select('os.*, c.nomeCliente as cliente_nome');
        $this->db->from('os');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');

        if ($tecnico_id) {
            $this->db->where('os.tecnico_responsavel', $tecnico_id);
        }

        $this->db->group_start();
        $this->db->like('os.idOs', $termo);
        $this->db->or_like('c.nomeCliente', $termo);
        $this->db->or_like('c.telefone', $termo);
        $this->db->group_end();

        $this->db->order_by('os.idOs', 'DESC');
        $this->db->limit(20);

        $query = $this->db->get();

        if ($query === false) {
            log_message('error', 'Erro na query pesquisarOs: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }

    /**
     * Obter resumo de execuções para dashboard
     */
    public function getResumoExecucoes($tecnico_id)
    {
        $hoje = date('Y-m-d');
        $semana_inicio = date('Y-m-d', strtotime('monday this week'));
        $mes_inicio = date('Y-m-01');

        // Hoje
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('DATE(data_checkin)', $hoje);
        $hoje_count = $this->db->count_all_results('tec_os_execucao');

        // Semana
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('DATE(data_checkin) >=', $semana_inicio);
        $semana_count = $this->db->count_all_results('tec_os_execucao');

        // Mês
        $this->db->where('tecnico_id', $tecnico_id);
        $this->db->where('DATE(data_checkin) >=', $mes_inicio);
        $mes_count = $this->db->count_all_results('tec_os_execucao');

        return [
            'hoje' => $hoje_count,
            'semana' => $semana_count,
            'mes' => $mes_count,
        ];
    }

    /**
     * Verificar se existe execução em andamento
     */
    public function verificarExecucaoAtiva($tecnico_id)
    {
        $this->db->select('tec_os_execucao.*, os.idOs, c.nomeCliente as cliente_nome');
        $this->db->from('tec_os_execucao');
        $this->db->join('os os', 'os.idOs = tec_os_execucao.os_id');
        $this->db->join('clientes c', 'c.idClientes = os.clientes_id');
        $this->db->where('tec_os_execucao.tecnico_id', $tecnico_id);
        $this->db->where('tec_os_execucao.status', 'em_execucao');

        $query = $this->db->get();
        return $query ? $query->row() : null;
    }

    /**
     * Buscar execucoes tecnicas por OS (para admin/relatorios)
     */
    public function getExecucoesByOs($os_id)
    {
        if (!$this->db->table_exists('tec_os_execucao')) {
            return [];
        }

        $this->db->select('te.*, t.nome as tecnico_nome');
        $this->db->from('tec_os_execucao te');
        $this->db->join('tecnicos t', 't.idTecnicos = te.tecnico_id', 'left');
        $this->db->where('te.os_id', $os_id);
        $this->db->order_by('te.checkin_horario', 'desc');

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    /**
     * Buscar fotos do tecnico por OS (do campo JSON fotos_galeria_json)
     */
    public function getFotosByOs($os_id)
    {
        if (!$this->db->table_exists('tec_os_execucao')) {
            return [];
        }

        $this->db->select('te.id, te.fotos_galeria_json, te.tecnico_id, te.checkin_horario, t.nome as tecnico_nome');
        $this->db->from('tec_os_execucao te');
        $this->db->join('tecnicos t', 't.idTecnicos = te.tecnico_id', 'left');
        $this->db->where('te.os_id', $os_id);
        $this->db->order_by('te.checkin_horario', 'desc');

        $query = $this->db->get();
        $execucoes = $query ? $query->result() : [];

        // Extrair fotos do JSON de cada execucao
        $fotos = [];
        foreach ($execucoes as $execucao) {
            if (!empty($execucao->fotos_galeria_json)) {
                $galeria = json_decode($execucao->fotos_galeria_json, true);
                if ($galeria && is_array($galeria)) {
                    foreach ($galeria as $foto) {
                        $fotos[] = (object) [
                            'caminho' => $foto['caminho'] ?? '',
                            'descricao' => $foto['descricao'] ?? '',
                            'tipo' => $foto['tipo'] ?? 'foto',
                            'data_envio' => $foto['data_hora'] ?? $execucao->checkin_horario,
                            'tecnico_nome' => $execucao->tecnico_nome ?? 'Tecnico',
                            'latitude' => $foto['lat'] ?? null,
                            'longitude' => $foto['lng'] ?? null,
                        ];
                    }
                }
            }
        }

        return $fotos;
    }
}
