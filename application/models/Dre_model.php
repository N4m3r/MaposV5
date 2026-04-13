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
        $this->db->order_by('ordem', 'ASC');
        if ($ativo !== null) {
            $this->db->where('ativo', $ativo);
        }
        return $this->db->get('dre_contas')->result();
    }

    /**
     * Obtém contas por grupo DRE
     */
    public function getContasPorGrupo($grupo)
    {
        $this->db->where('grupo', $grupo);
        $this->db->where('ativo', 1);
        $this->db->order_by('ordem', 'ASC');
        return $this->db->get('dre_contas')->result();
    }

    /**
     * Obtém uma conta específica
     */
    public function getContaById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('dre_contas')->row();
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
        // Verificar se existem lançamentos
        $this->db->where('conta_id', $id);
        $lancamentos = $this->db->count_all_results('dre_lancamentos');

        if ($lancamentos > 0) {
            // Desativa em vez de excluir
            return $this->atualizarConta($id, ['ativo' => 0]);
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
        $this->db->select('dre_lancamentos.*, dre_contas.nome as conta_nome, dre_contas.codigo as conta_codigo, dre_contas.grupo');
        $this->db->from('dre_lancamentos');
        $this->db->join('dre_contas', 'dre_contas.id = dre_lancamentos.conta_id');
        $this->db->where('dre_lancamentos.data >=', $data_inicio);
        $this->db->where('dre_lancamentos.data <=', $data_fim);

        if ($conta_id) {
            $this->db->where('conta_id', $conta_id);
        }

        $this->db->order_by('dre_lancamentos.data', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Obtém total de lançamentos por conta no período
     */
    public function getTotalPorConta($conta_id, $data_inicio, $data_fim)
    {
        $this->db->select('SUM(CASE WHEN tipo_movimento = "CREDITO" THEN valor ELSE -valor END) as total');
        $this->db->where('conta_id', $conta_id);
        $this->db->where('data >=', $data_inicio);
        $this->db->where('data <=', $data_fim);
        $result = $this->db->get('dre_lancamentos')->row();

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

        $result = $this->db->query($sql, [$data_inicio, $data_fim])->result();

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
                'OS #' + LPAD(o.idOs, 4, '0') as documento
            FROM os o
            WHERE o.dataFinal BETWEEN ? AND ?
            AND o.status IN ('Finalizado', 'Faturado')
            AND o.valorTotal > 0
            AND NOT EXISTS (
                SELECT 1 FROM dre_lancamentos dl WHERE dl.os_id = o.idOs
            )
        ";

        $os_result = $this->db->query($sql_os, [$data_inicio, $data_fim])->result();

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
                'VENDA #' + LPAD(v.idVendas, 4, '0') as documento
            FROM vendas v
            WHERE v.dataVenda BETWEEN ? AND ?
            AND v.valorTotal > 0
            AND NOT EXISTS (
                SELECT 1 FROM dre_lancamentos dl WHERE dl.venda_id = v.idVendas
            )
        ";

        $vendas_result = $this->db->query($sql_vendas, [$data_inicio, $data_fim])->result();

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

        $lanc_result = $this->db->query($sql_lanc, [$data_inicio, $data_fim])->result();

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
