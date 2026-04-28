<?php
/**
 * Model: DRE (Demonstração do Resultado do Exercício)
 * Sistema de contabilidade completo com plano de contas e lançamentos
 */
class Dre_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ==================== CONTAS ====================

    /**
     * Obtém todas as contas do plano de contas DRE
     */
    public function getContas($ativo = null)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('dre_contas')) {
            return [];
        }

        $this->db->order_by('ordem', 'ASC');
        if ($ativo !== null) {
            $this->db->where('ativo', $ativo);
        }

        $query = $this->db->get('dre_contas');

        if ($query === false) {
            return [];
        }

        return $query->result();
    }

    /**
     * Obtém contas por grupo DRE
     */
    public function getContasPorGrupo($grupo)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('dre_contas')) {
            return [];
        }

        $this->db->where('grupo', $grupo);
        $this->db->where('ativo', 1);
        $this->db->order_by('ordem', 'ASC');

        $query = $this->db->get('dre_contas');

        if ($query === false) {
            return [];
        }

        return $query->result();
    }

    /**
     * Obtém uma conta específica
     */
    public function getContaById($id)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('dre_contas')) {
            return null;
        }

        $this->db->where('id', $id);
        $query = $this->db->get('dre_contas');

        if ($query === false) {
            return null;
        }

        return $query->row();
    }

    /**
     * Adiciona nova conta ao plano
     */
    public function adicionarConta($dados)
    {
        $dados['created_at'] = date('Y-m-d H:i:s');
        $dados['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('dre_contas', $dados);
    }

    /**
     * Atualiza conta existente
     */
    public function atualizarConta($id, $dados)
    {
        $dados['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('dre_contas', $dados);
    }

    /**
     * Remove conta
     */
    public function excluirConta($id)
    {
        // Verificar se as tabelas existem
        if (!$this->db->table_exists('dre_contas')) {
            return false;
        }

        // Verificar se existem lançamentos (se a tabela existir)
        if ($this->db->table_exists('dre_lancamentos')) {
            $this->db->where('conta_id', $id);
            $lancamentos = $this->db->count_all_results('dre_lancamentos');

            if ($lancamentos > 0) {
                // Desativa em vez de excluir
                return $this->atualizarConta($id, ['ativo' => 0]);
            }
        }

        return $this->db->delete('dre_contas', ['id' => $id]);
    }

    // ==================== LANÇAMENTOS ====================

    /**
     * Adiciona lançamento contábil
     */
    public function adicionarLancamento($dados)
    {
        $dados['created_at'] = date('Y-m-d H:i:s');
        $dados['updated_at'] = date('Y-m-d H:i:s');
        $dados['usuarios_id'] = $this->session->userdata('id_admin');

        if ($this->db->insert('dre_lancamentos', $dados)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Obtém lançamentos por período
     */
    public function getLancamentos($data_inicio, $data_fim, $conta_id = null)
    {
        // Verificar se as tabelas existem
        if (!$this->db->table_exists('dre_lancamentos') || !$this->db->table_exists('dre_contas')) {
            return [];
        }

        $this->db->select('dre_lancamentos.*, dre_contas.nome as conta_nome, dre_contas.codigo as conta_codigo, dre_contas.grupo');
        $this->db->from('dre_lancamentos');
        $this->db->join('dre_contas', 'dre_contas.id = dre_lancamentos.conta_id');
        $this->db->where('dre_lancamentos.data >=', $data_inicio);
        $this->db->where('dre_lancamentos.data <=', $data_fim);

        if ($conta_id) {
            $this->db->where('conta_id', $conta_id);
        }

        $this->db->order_by('dre_lancamentos.data', 'DESC');

        $query = $this->db->get();

        // Verificar se a consulta foi bem sucedida
        if ($query === false) {
            return [];
        }

        return $query->result();
    }

    /**
     * Obtém total de lançamentos por conta no período
     */
    public function getTotalPorConta($conta_id, $data_inicio, $data_fim)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('dre_lancamentos')) {
            return 0;
        }

        $this->db->select('SUM(CASE WHEN tipo_movimento = "CREDITO" THEN valor ELSE -valor END) as total');
        $this->db->where('conta_id', $conta_id);
        $this->db->where('data >=', $data_inicio);
        $this->db->where('data <=', $data_fim);

        $query = $this->db->get('dre_lancamentos');

        if ($query === false) {
            return 0;
        }

        $result = $query->row();

        return $result ? floatval($result->total) : 0;
    }

    /**
     * Atualiza lançamento
     */
    public function atualizarLancamento($id, $dados)
    {
        $dados['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('dre_lancamentos', $dados);
    }

    /**
     * Exclui lançamento
     */
    public function excluirLancamento($id)
    {
        return $this->db->delete('dre_lancamentos', ['id' => $id]);
    }

    /**
     * Obtém totais por todas as contas ativas em uma única query (evita N+1)
     */
    public function getTotaisPorContas($data_inicio, $data_fim)
    {
        if (!$this->db->table_exists('dre_lancamentos') || !$this->db->table_exists('dre_contas')) {
            return [];
        }

        $sql = "
            SELECT
                c.id as conta_id,
                c.grupo,
                c.sinal,
                SUM(CASE WHEN dl.tipo_movimento = 'CREDITO' THEN dl.valor ELSE -dl.valor END) as total
            FROM dre_contas c
            LEFT JOIN dre_lancamentos dl ON dl.conta_id = c.id AND dl.data BETWEEN ? AND ?
            WHERE c.ativo = 1
            GROUP BY c.id, c.grupo, c.sinal
        ";

        $query = $this->db->query($sql, [$data_inicio, $data_fim]);
        if ($query === false) {
            return [];
        }

        $result = [];
        foreach ($query->result() as $row) {
            $valor = floatval($row->total);
            if ($row->sinal == 'NEGATIVO') {
                $valor = $valor * -1;
            }
            $result[$row->conta_id] = [
                'total' => $valor,
                'grupo' => $row->grupo,
            ];
        }
        return $result;
    }

    /**
     * Integra uma OS específica ao DRE automaticamente
     * Chamado quando OS muda para Finalizado/Faturado
     */
    public function integrarOS($os_id)
    {
        if (!$this->db->table_exists('dre_lancamentos') || !$this->db->table_exists('dre_contas')) {
            return false;
        }

        $os = $this->db->where('idOs', $os_id)->get('os')->row();
        if (!$os) {
            return false;
        }

        if (!in_array($os->status, ['Finalizado', 'Faturado'])) {
            return false;
        }

        // Verificar se já existe lançamento para esta OS
        $existe = $this->db->where('os_id', $os_id)->count_all_results('dre_lancamentos');
        if ($existe > 0) {
            return false;
        }

        $data_lanc = $os->dataFinal ?: $os->dataInicial ?: date('Y-m-d');
        $valor = floatval($os->valorTotal);
        if ($valor <= 0) {
            return false;
        }

        // Buscar totais de produtos e serviços da OS
        $totalProdutos = $this->db->select('COALESCE(SUM(preco * quantidade), 0) as total')
            ->where('os_id', $os_id)->get('produtos_os')->row()->total ?? 0;
        $totalServicos = $this->db->select('COALESCE(SUM(preco * quantidade), 0) as total')
            ->where('os_id', $os_id)->get('servicos_os')->row()->total ?? 0;

        $inseridos = 0;

        // 1. Receita de Serviços (se houver serviços)
        if ($totalServicos > 0) {
            $conta = $this->db->where('codigo', '1.1')->where('ativo', 1)->get('dre_contas')->row();
            if ($conta) {
                $this->adicionarLancamento([
                    'conta_id' => $conta->id,
                    'data' => $data_lanc,
                    'valor' => $totalServicos,
                    'tipo_movimento' => 'CREDITO',
                    'descricao' => 'Receita de serviços - OS #' . str_pad($os_id, 4, '0', STR_PAD_LEFT),
                    'documento' => 'OS #' . str_pad($os_id, 4, '0', STR_PAD_LEFT),
                    'os_id' => $os_id,
                ]);
                $inseridos++;
            }
        }

        // 2. Receita de Produtos (se houver produtos)
        if ($totalProdutos > 0) {
            $conta = $this->db->where('codigo', '1.2')->where('ativo', 1)->get('dre_contas')->row();
            if ($conta) {
                $this->adicionarLancamento([
                    'conta_id' => $conta->id,
                    'data' => $data_lanc,
                    'valor' => $totalProdutos,
                    'tipo_movimento' => 'CREDITO',
                    'descricao' => 'Receita de produtos - OS #' . str_pad($os_id, 4, '0', STR_PAD_LEFT),
                    'documento' => 'OS #' . str_pad($os_id, 4, '0', STR_PAD_LEFT),
                    'os_id' => $os_id,
                ]);
                $inseridos++;
            }
        }

        // 3. Custo dos produtos (se houver produtos, debitar no grupo CUSTO)
        if ($totalProdutos > 0) {
            $conta_custo = $this->db->where('grupo', 'CUSTO')->where('ativo', 1)
                ->order_by('ordem', 'ASC')->limit(1)->get('dre_contas')->row();
            if ($conta_custo) {
                $this->adicionarLancamento([
                    'conta_id' => $conta_custo->id,
                    'data' => $data_lanc,
                    'valor' => $totalProdutos,
                    'tipo_movimento' => 'DEBITO',
                    'descricao' => 'Custo de produtos - OS #' . str_pad($os_id, 4, '0', STR_PAD_LEFT),
                    'documento' => 'OS #' . str_pad($os_id, 4, '0', STR_PAD_LEFT),
                    'os_id' => $os_id,
                ]);
                $inseridos++;
            }
        }

        // 4. Impostos (usar Simples Nacional se configurado)
        if ($this->db->table_exists('impostos_config')) {
            $config = $this->db->get('impostos_config')->row();
            if ($config && $config->tipo_regime == 'simples_nacional') {
                // Buscar faixa do Simples Nacional baseada no faturamento
                $conta_imposto = $this->db->where('grupo', 'IMPOSTO_RENDA')->where('ativo', 1)
                    ->order_by('ordem', 'ASC')->limit(1)->get('dre_contas')->row();

                if ($conta_imposto) {
                    $imposto = $this->calcularImpostoSimples($valor, $config);
                    if ($imposto > 0) {
                        $this->adicionarLancamento([
                            'conta_id' => $conta_imposto->id,
                            'data' => $data_lanc,
                            'valor' => $imposto,
                            'tipo_movimento' => 'DEBITO',
                            'descricao' => 'Impostos Simples Nacional - OS #' . str_pad($os_id, 4, '0', STR_PAD_LEFT),
                            'documento' => 'OS #' . str_pad($os_id, 4, '0', STR_PAD_LEFT),
                            'os_id' => $os_id,
                        ]);
                        $inseridos++;
                    }
                }
            }
        }

        return $inseridos > 0;
    }

    /**
     * Calcula imposto estimado pelo Simples Nacional
     */
    private function calcularImpostoSimples($valor, $config)
    {
        $anexo = $config->anexo_simples ?? 'III';
        $faixas = [
            'I'   => [0.06, 0.075, 0.09, 0.0995, 0.1065, 0.1333],
            'II'  => [0.065, 0.0836, 0.10, 0.1055, 0.1128, 0.1434],
            'III' => [0.06, 0.08, 0.10, 0.105, 0.1125, 0.14],
            'IV'  => [0.065, 0.0863, 0.1056, 0.1123, 0.12, 0.14],
            'V'   => [0.065, 0.0863, 0.1056, 0.1123, 0.12, 0.14],
        ];

        $limites = [180000, 360000, 720000, 1800000, 3600000, 4800000];
        $faixa = $faixas[$anexo] ?? $faixas['III'];

        // Usar a primeira faixa como aproximação para uma OS individual
        $aliquota = $faixa[0];
        return round($valor * $aliquota, 2);
    }

    /**
     * Integra uma NFS-e ao DRE após emissão
     * Lança DAS (Simples Nacional)
     * Lança retenções do tomador como deduções
     */
    public function integrarNFSe($nfse_id, $nfse_data)
    {
        if (!$this->db->table_exists('dre_lancamentos') || !$this->db->table_exists('dre_contas')) {
            return false;
        }

        $valor_servicos = floatval($nfse_data['valor_servicos'] ?? 0);
        $os_id = $nfse_data['os_id'] ?? null;
        $data_lanc = $nfse_data['data_emissao'] ?? date('Y-m-d');
        $inseridos = 0;

        // 1. Impostos - DAS (Simples Nacional)
        $conta_imposto = $this->db->where('grupo', 'IMPOSTO_RENDA')->where('ativo', 1)
            ->order_by('ordem', 'ASC')->limit(1)->get('dre_contas')->row();

        if ($conta_imposto) {
            // DAS - valor único que engloba todos os impostos
            $valor_das = floatval($nfse_data['valor_das'] ?? $nfse_data['valor_total_impostos'] ?? 0);
            if ($valor_das > 0) {
                $this->adicionarLancamento([
                    'conta_id' => $conta_imposto->id,
                    'data' => $data_lanc,
                    'valor' => $valor_das,
                    'tipo_movimento' => 'DEBITO',
                    'descricao' => 'DAS - Simples Nacional (NFSe #' . $nfse_id . ')',
                    'documento' => 'NFSe #' . $nfse_id,
                    'os_id' => $os_id,
                ]);
                $inseridos++;
            }
        }

        // 2. Retenções do tomador - deduções na receita
        $valor_total_retencao = floatval($nfse_data['valor_total_retencao'] ?? 0);
        if ($valor_total_retencao > 0) {
            // ISS retido -> Deduções da Receita
            $valor_ret_iss = floatval($nfse_data['valor_retencao_iss'] ?? 0);
            if ($valor_ret_iss > 0) {
                $conta_iss_ret = $this->db->where('codigo', '2.1')->where('ativo', 1)->get('dre_contas')->row();
                if (!$conta_iss_ret) {
                    $conta_iss_ret = $this->db->where('grupo', 'DEDUCOES')->where('ativo', 1)
                        ->order_by('ordem', 'ASC')->limit(1)->get('dre_contas')->row();
                }
                if ($conta_iss_ret) {
                    $this->adicionarLancamento([
                        'conta_id' => $conta_iss_ret->id,
                        'data' => $data_lanc,
                        'valor' => $valor_ret_iss,
                        'tipo_movimento' => 'DEBITO',
                        'descricao' => 'ISS retido na fonte (NFSe #' . $nfse_id . ')',
                        'documento' => 'NFSe #' . $nfse_id,
                        'os_id' => $os_id,
                    ]);
                    $inseridos++;
                }
            }

            // IRRF, PIS/COFINS, CSLL retidos -> Imposto de Renda
            $valor_ret_irrf = floatval($nfse_data['valor_retencao_irrf'] ?? 0);
            $valor_ret_pis = floatval($nfse_data['valor_retencao_pis'] ?? 0);
            $valor_ret_cofins = floatval($nfse_data['valor_retencao_cofins'] ?? 0);
            $valor_ret_csll = floatval($nfse_data['valor_retencao_csll'] ?? 0);

            $retencoes_federais = $valor_ret_irrf + $valor_ret_pis + $valor_ret_cofins + $valor_ret_csll;
            if ($retencoes_federais > 0 && $conta_imposto) {
                $this->adicionarLancamento([
                    'conta_id' => $conta_imposto->id,
                    'data' => $data_lanc,
                    'valor' => $retencoes_federais,
                    'tipo_movimento' => 'DEBITO',
                    'descricao' => 'Retenções federais (IRRF/PIS/COFINS/CSLL) - NFSe #' . $nfse_id,
                    'documento' => 'NFSe #' . $nfse_id,
                    'os_id' => $os_id,
                ]);
                $inseridos++;
            }
        }

        return $inseridos > 0;
    }

    /**
     * Obtém resumo de OS no período para o dashboard
     */
    public function getResumoOS($data_inicio, $data_fim)
    {
        $result = [
            'total_os' => 0,
            'valor_total' => 0,
            'os_finalizadas' => 0,
            'os_faturadas' => 0,
            'valor_finalizado' => 0,
            'valor_faturado' => 0,
        ];

        if (!$this->db->table_exists('os')) {
            return $result;
        }

        $this->db->select('
            COUNT(*) as total_os,
            COALESCE(SUM(valorTotal), 0) as valor_total,
            SUM(CASE WHEN status = \'Finalizado\' THEN 1 ELSE 0 END) as os_finalizadas,
            SUM(CASE WHEN status = \'Faturado\' THEN 1 ELSE 0 END) as os_faturadas,
            COALESCE(SUM(CASE WHEN status IN (\'Finalizado\', \'Faturado\') THEN valorTotal ELSE 0 END), 0) as valor_finalizado
        ');
        $this->db->where('dataFinal >=', $data_inicio);
        $this->db->where('dataFinal <=', $data_fim);

        $query = $this->db->get('os');
        if ($query && $query->row()) {
            $row = $query->row();
            $result['total_os'] = intval($row->total_os);
            $result['valor_total'] = floatval($row->valor_total);
            $result['os_finalizadas'] = intval($row->os_finalizadas);
            $result['os_faturadas'] = intval($row->os_faturadas);
            $result['valor_finalizado'] = floatval($row->valor_finalizado);
            $result['valor_faturado'] = floatval($row->valor_finalizado);
        }

        return $result;
    }

    // ==================== CÁLCULO DO DRE ====================

    /**
     * Gera o DRE completo para um período
     */
    public function gerarDRE($data_inicio, $data_fim, $comparativo = false)
    {
        $dre = [];
        $contas = $this->getContas(1); // Ativas

        // Agrupar contas por grupo
        $contasPorGrupo = [];
        foreach ($contas as $conta) {
            if (isset($conta->grupo) && $conta->grupo !== null) {
                $contasPorGrupo[$conta->grupo][] = $conta;
            }
        }

        // Calcular valores por grupo
        $totais = $this->calcularTotaisGrupos($data_inicio, $data_fim, $contasPorGrupo);

        // Estrutura DRE formatada
        $dre = [
            'periodo' => [
                'inicio' => $data_inicio,
                'fim' => $data_fim,
            ],
            'grupos' => [
                'RECEITA_BRUTA' => [
                    'titulo' => 'RECEITA BRUTA',
                    'valor' => $totais['RECEITA_BRUTA'] ?? 0,
                    'contas' => $contasPorGrupo['RECEITA_BRUTA'] ?? [],
                    'ordem' => 1,
                    'destaque' => false
                ],
                'DEDUCOES' => [
                    'titulo' => '(-) DEDUÇÕES DA RECEITA',
                    'valor' => ($totais['DEDUCOES'] ?? 0) * -1,
                    'contas' => $contasPorGrupo['DEDUCOES'] ?? [],
                    'ordem' => 2,
                    'destaque' => false
                ],
                'RECEITA_LIQUIDA' => [
                    'titulo' => '= RECEITA LÍQUIDA',
                    'valor' => ($totais['RECEITA_BRUTA'] ?? 0) + ($totais['DEDUCOES'] ?? 0),
                    'contas' => [],
                    'ordem' => 3,
                    'destaque' => true
                ],
                'CUSTO' => [
                    'titulo' => '(-) CUSTO DOS SERVIÇOS/PRODUTOS',
                    'valor' => ($totais['CUSTO'] ?? 0) * -1,
                    'contas' => $contasPorGrupo['CUSTO'] ?? [],
                    'ordem' => 4,
                    'destaque' => false
                ],
                'LUCRO_BRUTO' => [
                    'titulo' => '= LUCRO BRUTO',
                    'valor' => ($totais['RECEITA_BRUTA'] ?? 0) + ($totais['DEDUCOES'] ?? 0) + ($totais['CUSTO'] ?? 0),
                    'contas' => [],
                    'ordem' => 5,
                    'destaque' => true
                ],
                'DESPESA_OPERACIONAL' => [
                    'titulo' => '(-) DESPESAS OPERACIONAIS',
                    'valor' => ($totais['DESPESA_OPERACIONAL'] ?? 0) * -1,
                    'contas' => $contasPorGrupo['DESPESA_OPERACIONAL'] ?? [],
                    'ordem' => 6,
                    'destaque' => false
                ],
                'LUCRO_OPERACIONAL' => [
                    'titulo' => '= LUCRO/PREJUÍZO OPERACIONAL',
                    'valor' => ($totais['RECEITA_BRUTA'] ?? 0) + ($totais['DEDUCOES'] ?? 0) + ($totais['CUSTO'] ?? 0) + ($totais['DESPESA_OPERACIONAL'] ?? 0),
                    'contas' => [],
                    'ordem' => 7,
                    'destaque' => true
                ],
                'OUTRAS_RECEITAS' => [
                    'titulo' => '(+) OUTRAS RECEITAS',
                    'valor' => $totais['OUTRAS_RECEITAS'] ?? 0,
                    'contas' => $contasPorGrupo['OUTRAS_RECEITAS'] ?? [],
                    'ordem' => 8,
                    'destaque' => false
                ],
                'OUTRAS_DESPESAS' => [
                    'titulo' => '(-) OUTRAS DESPESAS',
                    'valor' => ($totais['OUTRAS_DESPESAS'] ?? 0) * -1,
                    'contas' => $contasPorGrupo['OUTRAS_DESPESAS'] ?? [],
                    'ordem' => 9,
                    'destaque' => false
                ],
                'LUCRO_ANTES_IR' => [
                    'titulo' => '= LUCRO/PREJUÍZO ANTES DO IR',
                    'valor' => ($totais['RECEITA_BRUTA'] ?? 0) + ($totais['DEDUCOES'] ?? 0) + ($totais['CUSTO'] ?? 0) +
                              ($totais['DESPESA_OPERACIONAL'] ?? 0) + ($totais['OUTRAS_RECEITAS'] ?? 0) + ($totais['OUTRAS_DESPESAS'] ?? 0),
                    'contas' => [],
                    'ordem' => 10,
                    'destaque' => true
                ],
                'IMPOSTO_RENDA' => [
                    'titulo' => '(-) IMPOSTO DE RENDA E CONTRIBUIÇÕES',
                    'valor' => ($totais['IMPOSTO_RENDA'] ?? 0) * -1,
                    'contas' => $contasPorGrupo['IMPOSTO_RENDA'] ?? [],
                    'ordem' => 11,
                    'destaque' => false
                ],
                'LUCRO_LIQUIDO' => [
                    'titulo' => '= LUCRO/PREJUÍZO LÍQUIDO DO EXERCÍCIO',
                    'valor' => ($totais['RECEITA_BRUTA'] ?? 0) + ($totais['DEDUCOES'] ?? 0) + ($totais['CUSTO'] ?? 0) +
                              ($totais['DESPESA_OPERACIONAL'] ?? 0) + ($totais['OUTRAS_RECEITAS'] ?? 0) +
                              ($totais['OUTRAS_DESPESAS'] ?? 0) + ($totais['IMPOSTO_RENDA'] ?? 0),
                    'contas' => [],
                    'ordem' => 12,
                    'destaque' => true
                ],
            ]
        ];

        // Calcular percentuais
        $receitaBruta = $totais['RECEITA_BRUTA'] ?? 0;
        foreach ($dre['grupos'] as $key => &$grupo) {
            $grupo['percentual'] = $receitaBruta > 0 ? round(($grupo['valor'] / $receitaBruta) * 100, 2) : 0;
        }

        // Comparativo com período anterior
        if ($comparativo) {
            $dias = (strtotime($data_fim) - strtotime($data_inicio)) / (60 * 60 * 24);
            $data_inicio_ant = date('Y-m-d', strtotime($data_inicio . " -{$dias} days"));
            $data_fim_ant = date('Y-m-d', strtotime($data_inicio . " -1 day"));

            $dre_anterior = $this->gerarDRE($data_inicio_ant, $data_fim_ant, false);
            $dre['comparativo'] = $dre_anterior;
            $dre['periodo_anterior'] = ['inicio' => $data_inicio_ant, 'fim' => $data_fim_ant];
        }

        return $dre;
    }

    /**
     * Calcula totais por grupos de contas
     */
    private function calcularTotaisGrupos($data_inicio, $data_fim, $contasPorGrupo)
    {
        $totais = [];

        // Verificar se as tabelas existem
        if (!$this->db->table_exists('dre_lancamentos') || !$this->db->table_exists('dre_contas')) {
            return $totais;
        }

        $sql = "
            SELECT
                c.grupo,
                c.sinal,
                SUM(CASE
                    WHEN dl.tipo_movimento = 'CREDITO' THEN dl.valor
                    ELSE -dl.valor
                END) as total
            FROM dre_lancamentos dl
            JOIN dre_contas c ON c.id = dl.conta_id
            WHERE dl.data BETWEEN ? AND ?
            AND c.ativo = 1
            GROUP BY c.grupo, c.sinal
        ";

        $query = $this->db->query($sql, [$data_inicio, $data_fim]);

        // Verificar se a query foi bem sucedida
        if ($query === false) {
            return $totais;
        }

        $result = $query->result();

        foreach ($result as $row) {
            $valor = floatval($row->total);
            // Ajustar sinal conforme a conta
            if ($row->sinal == 'NEGATIVO') {
                $valor = $valor * -1;
            }

            if (!isset($totais[$row->grupo])) {
                $totais[$row->grupo] = 0;
            }
            $totais[$row->grupo] += $valor;
        }

        return $totais;
    }

    // ==================== INTEGRAÇÃO AUTOMÁTICA ====================

    /**
     * Integra dados automáticos do sistema para o DRE
     * Busca de OS, Vendas e Lançamentos Financeiros
     */
    public function integrarDadosAutomaticos($data_inicio, $data_fim)
    {
        $inseridos = 0;

        // 1. OS Finalizadas -> Receita de Serviços
        $sql_os = "
            SELECT
                o.idOs,
                o.valorTotal as valor,
                o.dataFinal as data,
                CONCAT('OS #', LPAD(o.idOs, 4, '0')) as documento
            FROM os o
            WHERE o.dataFinal BETWEEN ? AND ?
            AND o.status IN ('Finalizado', 'Faturado')
            AND o.valorTotal > 0
            AND NOT EXISTS (
                SELECT 1 FROM dre_lancamentos dl WHERE dl.os_id = o.idOs
            )
        ";

        $query_os = $this->db->query($sql_os, [$data_inicio, $data_fim]);
        $os_result = ($query_os !== false) ? $query_os->result() : [];

        // Buscar conta de Receita de Serviços
        $this->db->where('codigo', '1.1');
        $conta_servico = $this->db->get('dre_contas')->row();

        if ($conta_servico) {
            foreach ($os_result as $os) {
                $this->adicionarLancamento([
                    'conta_id' => $conta_servico->id,
                    'data' => $os->data,
                    'valor' => $os->valor,
                    'tipo_movimento' => 'CREDITO',
                    'descricao' => 'Receita de OS finalizada',
                    'documento' => $os->documento,
                    'os_id' => $os->idOs
                ]);
                $inseridos++;
            }
        }

        // 2. Vendas Finalizadas -> Receita de Vendas
        $sql_vendas = "
            SELECT
                v.idVendas,
                v.valorTotal as valor,
                v.dataVenda as data,
                CONCAT('VENDA #', LPAD(v.idVendas, 4, '0')) as documento
            FROM vendas v
            WHERE v.dataVenda BETWEEN ? AND ?
            AND v.valorTotal > 0
            AND NOT EXISTS (
                SELECT 1 FROM dre_lancamentos dl WHERE dl.venda_id = v.idVendas
            )
        ";

        $query_vendas = $this->db->query($sql_vendas, [$data_inicio, $data_fim]);
        $vendas_result = ($query_vendas !== false) ? $query_vendas->result() : [];

        // Buscar conta de Receita de Vendas
        $this->db->where('codigo', '1.2');
        $conta_vendas = $this->db->get('dre_contas')->row();

        if ($conta_vendas) {
            foreach ($vendas_result as $venda) {
                $this->adicionarLancamento([
                    'conta_id' => $conta_vendas->id,
                    'data' => $venda->data,
                    'valor' => $venda->valor,
                    'tipo_movimento' => 'CREDITO',
                    'descricao' => 'Receita de venda',
                    'documento' => $venda->documento,
                    'venda_id' => $venda->idVendas
                ]);
                $inseridos++;
            }
        }

        // 3. Lançamentos Financeiros -> Despesas
        $sql_lanc = "
            SELECT
                l.idLancamentos,
                l.valor as valor,
                l.data_vencimento as data,
                l.descricao,
                l.tipo
            FROM lancamentos l
            WHERE l.data_vencimento BETWEEN ? AND ?
            AND l.baixado = 1
            AND NOT EXISTS (
                SELECT 1 FROM dre_lancamentos dl WHERE dl.lancamento_id = l.idLancamentos
            )
        ";

        $query_lanc = $this->db->query($sql_lanc, [$data_inicio, $data_fim]);
        $lanc_result = ($query_lanc !== false) ? $query_lanc->result() : [];

        // Mapear tipos de lançamento para contas DRE
        $mapeamento = [
            'despesa' => '6.1.6', // Outras Despesas Administrativas
            'receita' => '1.3',    // Outras Receitas Operacionais
        ];

        foreach ($lanc_result as $lanc) {
            $codigo_conta = $mapeamento[$lanc->tipo] ?? '6.1.6';

            $this->db->where('codigo', $codigo_conta);
            $conta = $this->db->get('dre_contas')->row();

            if ($conta) {
                $this->adicionarLancamento([
                    'conta_id' => $conta->id,
                    'data' => $lanc->data,
                    'valor' => $lanc->valor,
                    'tipo_movimento' => $lanc->tipo == 'receita' ? 'CREDITO' : 'DEBITO',
                    'descricao' => $lanc->descricao,
                    'documento' => 'LANC #' . $lanc->idLancamentos,
                    'lancamento_id' => $lanc->idLancamentos
                ]);
                $inseridos++;
            }
        }

        return $inseridos;
    }

    // ==================== DASHBOARD E INDICADORES ====================

    /**
     * Obtém indicadores financeiros para dashboard
     */
    public function getIndicadores($data_inicio, $data_fim)
    {
        $dre = $this->gerarDRE($data_inicio, $data_fim);
        $grupos = $dre['grupos'];

        $receitaBruta = $grupos['RECEITA_BRUTA']['valor'] ?? 0;
        $lucroBruto = $grupos['LUCRO_BRUTO']['valor'] ?? 0;
        $lucroOperacional = $grupos['LUCRO_OPERACIONAL']['valor'] ?? 0;
        $lucroLiquido = $grupos['LUCRO_LIQUIDO']['valor'] ?? 0;

        return [
            'receita_bruta' => $receitaBruta,
            'lucro_bruto' => $lucroBruto,
            'lucro_operacional' => $lucroOperacional,
            'lucro_liquido' => $lucroLiquido,
            'margem_bruta' => $receitaBruta > 0 ? round(($lucroBruto / $receitaBruta) * 100, 2) : 0,
            'margem_operacional' => $receitaBruta > 0 ? round(($lucroOperacional / $receitaBruta) * 100, 2) : 0,
            'margem_liquida' => $receitaBruta > 0 ? round(($lucroLiquido / $receitaBruta) * 100, 2) : 0,
            'ebitda' => $lucroOperacional + ($grupos['DESPESA_OPERACIONAL']['valor'] ?? 0), // Aproximação
        ];
    }

    /**
     * Evolução mensal dos principais indicadores
     */
    public function getEvolucaoMensal($meses = 6)
    {
        $dados = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $data_fim = date('Y-m-t', strtotime("-{$i} months"));
            $data_inicio = date('Y-m-01', strtotime("-{$i} months"));

            $dre = $this->gerarDRE($data_inicio, $data_fim);

            $dados[] = [
                'mes' => date('Y-m', strtotime($data_inicio)),
                'mes_formatado' => date('m/Y', strtotime($data_inicio)),
                'receita_bruta' => $dre['grupos']['RECEITA_BRUTA']['valor'] ?? 0,
                'lucro_bruto' => $dre['grupos']['LUCRO_BRUTO']['valor'] ?? 0,
                'lucro_operacional' => $dre['grupos']['LUCRO_OPERACIONAL']['valor'] ?? 0,
                'lucro_liquido' => $dre['grupos']['LUCRO_LIQUIDO']['valor'] ?? 0,
            ];
        }

        return $dados;
    }

    // ==================== RELATÓRIOS ====================

    /**
     * Exporta DRE para Excel/CSV
     */
    public function exportarDRE($data_inicio, $data_fim, $formato = 'csv')
    {
        $dre = $this->gerarDRE($data_inicio, $data_fim, true);

        $csv = [];
        $csv[] = ['DEMONSTRAÇÃO DO RESULTADO DO EXERCÍCIO'];
        $csv[] = ['Período: ' . date('d/m/Y', strtotime($data_inicio)) . ' a ' . date('d/m/Y', strtotime($data_fim))];
        $csv[] = [];
        $csv[] = ['Conta', 'Valor (R$)', '% sobre Receita'];

        foreach ($dre['grupos'] as $grupo) {
            $csv[] = [
                $grupo['titulo'],
                number_format($grupo['valor'], 2, ',', '.'),
                $grupo['percentual'] . '%'
            ];
        }

        return $csv;
    }
}
