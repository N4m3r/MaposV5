<?php
/**
 * Model: Boleto Emitido para OS
 * Gerencia geração e controle de boletos vinculados a OS
 */

class Boleto_os_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Gerar novo boleto para OS
     */
    public function gerar($os_id, $nfse_id = null, $config = [])
    {
        // Verificar se já existe boleto ativo
        $existente = $this->getAtivoByOsId($os_id);
        if ($existente && in_array($existente->status, ['Emitido', 'Pendente'])) {
            return ['error' => 'Já existe boleto emitido para esta OS. Cancele o anterior primeiro.'];
        }

        // Buscar dados da OS
        $this->load->model('os_model');
        $os = $this->os_model->getById($os_id);

        if (!$os) {
            return ['error' => 'OS não encontrada'];
        }

        // Se tem NFSe vinculada, usar valor da NFSe
        $valor_original = $os->valorTotal ?? 0;
        $valor_retencoes = 0;
        $tem_retencao_tomador = false;

        if ($nfse_id) {
            $this->load->model('nfse_emitida_model');
            $nfse = $this->nfse_emitida_model->getById($nfse_id);
            if ($nfse) {
                $valor_original = floatval($nfse->valor_servicos);
                $valor_retencoes = floatval($nfse->valor_total_retencao ?? 0);
                $tem_retencao_tomador = $valor_retencoes > 0;
            } elseif ($this->db->table_exists('certificado_nfe_importada')) {
                // Fallback: NFSe importada via XML (tabela certificado_nfe_importada)
                $nfseImp = $this->db->where('id', $nfse_id)->get('certificado_nfe_importada')->row();
                if ($nfseImp) {
                    $valor_original = floatval($nfseImp->valor_total ?? 0);
                    $valor_retencoes = 0.00;
                    $tem_retencao_tomador = false;
                }
            }
        }

        // Definir se o boleto vai com valor integral
        // Padrão: boleto sempre com valor integral (valor dos serviços)
        // Com retenção: usuário pode escolher valor líquido (integral - retenções)
        $valor_integral = true;
        if ($tem_retencao_tomador && !empty($config['valor_integral'])) {
            $valor_integral = true;
        } elseif ($tem_retencao_tomador) {
            // Quando há retenção e não explicitou valor_integral, usar integral por padrão
            $valor_integral = true;
        } elseif (!$tem_retencao_tomador && isset($config['valor_integral']) && !$config['valor_integral']) {
            $valor_integral = false;
        }

        if ($valor_integral) {
            $valor_liquido = $valor_original;
        } else {
            $valor_liquido = $valor_original - $valor_retencoes;
        }

        // Configurar data de vencimento
        $data_vencimento = $config['data_vencimento'] ?? date('Y-m-d', strtotime('+5 days'));

        // Dados do boleto
        $boleto_data = [
            'os_id' => $os_id,
            'nfse_id' => $nfse_id,
            'data_emissao' => date('Y-m-d'),
            'data_vencimento' => $data_vencimento,
            'valor_original' => $valor_original,
            'valor_desconto_impostos' => 0,
            'valor_liquido' => $valor_liquido,
            'valor_integral' => $valor_integral ? 1 : 0,
            'status' => 'Pendente',
            'sacado_nome' => $os->nomeCliente,
            'sacado_documento' => $os->documento ?? '',
            'sacado_endereco' => ($os->rua ?? '') . ', ' . ($os->numero ?? '') . ' - ' . ($os->bairro ?? ''),
            'instrucoes' => $config['instrucoes'] ?? 'Pagável em qualquer banco até o vencimento.',
            'gateway' => $config['gateway'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Inserir no banco
        if ($this->db->insert('os_boleto_emitido', $boleto_data)) {
            $boleto_id = $this->db->insert_id();

            // Se tem NFSe, vincular boleto e registrar retenção de impostos no DRE
            if ($nfse_id && $nfse) {
                $this->load->model('nfse_emitida_model');
                $this->nfse_emitida_model->vincularBoleto($nfse_id, $boleto_id);

                // Registrar retenção de impostos para DRE (se houver retenções pelo tomador)
                if ($valor_retencoes > 0) {
                    $this->load->model('impostos_model');
                    $retencao_id = $this->impostos_model->reterImpostos([
                        'os_id'         => $os_id,
                        'cliente_id'    => $os->clientes_id ?? 0,
                        'valor_bruto'   => $valor_original,
                        'data_competencia' => $nfse->competencia ?? date('Y-m-01'),
                        'nota_fiscal'   => $nfse->numero_nfse,
                        'observacao'    => 'Retenção via NFS-e #' . ($nfse->numero_nfse ?? 'N/A') . ' - Boleto OS #' . $os_id,
                    ]);

                    if ($retencao_id) {
                        log_info('Retenção de impostos registrada no DRE para boleto OS #' . $os_id . ' (NFSe #' . ($nfse->numero_nfse ?? 'N/A') . ')');
                    }
                }
            }

            // Atualizar status da OS
            $this->db->where('idOs', $os_id);
            $this->db->update('os', [
                'boleto_status' => 'Pendente',
                'data_vencimento_boleto' => $data_vencimento
            ]);

            log_info('Boleto criado para OS #' . $os_id . ' - ID: ' . $boleto_id);

            return [
                'success' => true,
                'boleto_id' => $boleto_id,
                'valor_original' => $valor_original,
                'valor_desconto_impostos' => 0,
                'valor_liquido' => $valor_liquido,
                'message' => 'Boleto gerado com sucesso. Valor líquido: R$ ' . number_format($valor_liquido, 2, ',', '.')
            ];
        }

        return ['error' => 'Erro ao criar boleto no banco de dados'];
    }

    /**
     * Confirmar emissão do boleto
     */
    public function confirmarEmissao($boleto_id, $dados_boleto)
    {
        $this->db->where('id', $boleto_id);
        $update = [
            'status' => 'Emitido',
            'nosso_numero' => $dados_boleto['nosso_numero'] ?? null,
            'linha_digitavel' => $dados_boleto['linha_digitavel'] ?? null,
            'codigo_barras' => $dados_boleto['codigo_barras'] ?? null,
            'pdf_path' => $dados_boleto['pdf_path'] ?? null,
            'gateway_transaction_id' => $dados_boleto['transaction_id'] ?? null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->update('os_boleto_emitido', $update)) {
            // Atualizar status da OS
            $boleto = $this->getById($boleto_id);
            $this->db->where('idOs', $boleto->os_id);
            $this->db->update('os', ['boleto_status' => 'Emitido']);

            return ['success' => true];
        }

        return ['error' => 'Erro ao atualizar boleto'];
    }

    /**
     * Registrar pagamento do boleto
     */
    public function registrarPagamento($boleto_id, $dados_pagamento = [])
    {
        $this->db->where('id', $boleto_id);
        $update = [
            'status' => 'Pago',
            'data_pagamento' => $dados_pagamento['data_pagamento'] ?? date('Y-m-d'),
            'valor_pago' => $dados_pagamento['valor_pago'] ?? 0,
            'multa' => $dados_pagamento['multa'] ?? 0,
            'juros' => $dados_pagamento['juros'] ?? 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->update('os_boleto_emitido', $update)) {
            // Atualizar status da OS
            $boleto = $this->getById($boleto_id);
            $this->db->where('idOs', $boleto->os_id);
            $this->db->update('os', ['boleto_status' => 'Pago']);

            // Criar lançamento no financeiro se configurado
            $this->criarLancamentoFinanceiro($boleto);

            return ['success' => true];
        }

        return ['error' => 'Erro ao registrar pagamento'];
    }

    /**
     * Verificar boletos vencidos
     */
    public function verificarVencidos()
    {
        $this->db->where('status', 'Emitido');
        $this->db->where('data_vencimento <', date('Y-m-d'));
        $boletos = $this->db->get('os_boleto_emitido')->result();

        foreach ($boletos as $boleto) {
            $this->db->where('id', $boleto->id);
            $this->db->update('os_boleto_emitido', [
                'status' => 'Vencido',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Atualizar status da OS
            $this->db->where('idOs', $boleto->os_id);
            $this->db->update('os', ['boleto_status' => 'Vencido']);
        }

        return count($boletos);
    }

    /**
     * Cancelar boleto
     */
    public function cancelar($boleto_id, $motivo = '')
    {
        $boleto = $this->getById($boleto_id);
        if (!$boleto) {
            return ['error' => 'Boleto não encontrado'];
        }

        $this->db->where('id', $boleto_id);
        if ($this->db->update('os_boleto_emitido', [
            'status' => 'Cancelado',
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            // Atualizar status da OS
            $this->db->where('idOs', $boleto->os_id);
            $this->db->update('os', [
                'boleto_status' => 'Cancelado',
                'data_vencimento_boleto' => null
            ]);

            // Estornar retenção de impostos no DRE (se existir)
            if ($boleto->os_id) {
                $this->load->model('impostos_model');
                $this->impostos_model->estornarRetencao(['os_id' => $boleto->os_id]);
            }

            return ['success' => true];
        }
        return ['error' => 'Erro ao cancelar boleto'];
    }

    /**
     * Criar lançamento financeiro automaticamente
     */
    private function criarLancamentoFinanceiro($boleto)
    {
        // Verificar se integração está habilitada
        $this->load->model('impostos_model');
        if ($this->impostos_model->getConfig('IMPOSTO_DRE_INTEGRACAO') != '1') {
            return;
        }

        // Buscar OS
        $this->load->model('os_model');
        $os = $this->os_model->getById($boleto->os_id);

        if (!$os) {
            return;
        }

        // Dados do lançamento
        $dados = [
            'descricao' => 'Pagamento OS #' . $boleto->os_id . ' - Boleto ' . $boleto->nosso_numero,
            'valor' => $boleto->valor_liquido,
            'data_vencimento' => $boleto->data_vencimento,
            'data_pagamento' => $boleto->data_pagamento,
            'cliente' => $boleto->sacado_nome,
            'forma_pgto' => 'Boleto',
            'tipo' => 'receita',
            'observacao' => 'Gerado automaticamente pelo sistema de boletos OS',
            'os_id' => $boleto->os_id
        ];

        // Inserir no financeiro
        $this->db->insert('lancamentos', $dados);
    }

    /**
     * Obter boleto por ID
     */
    public function getById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('os_boleto_emitido')->row();
    }

    /**
     * Obter boleto ativo por OS ID
     */
    public function getAtivoByOsId($os_id)
    {
        $this->db->where('os_id', $os_id);
        $this->db->where_in('status', ['Pendente', 'Emitido']);
        $this->db->order_by('id', 'DESC');
        return $this->db->get('os_boleto_emitido')->row();
    }

    /**
     * Obter todos os boletos de uma OS
     */
    public function getAllByOsId($os_id)
    {
        $this->db->where('os_id', $os_id);
        $this->db->order_by('id', 'DESC');
        return $this->db->get('os_boleto_emitido')->result();
    }

    /**
     * Listar boletos com filtros
     */
    public function listar($filtros = [], $limit = 50, $offset = 0)
    {
        $this->db->select('os_boleto_emitido.*, os.idOs, clientes.nomeCliente, os.status as os_status');
        $this->db->from('os_boleto_emitido');
        $this->db->join('os', 'os.idOs = os_boleto_emitido.os_id');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');

        if (!empty($filtros['status'])) {
            $this->db->where('os_boleto_emitido.status', $filtros['status']);
        }
        if (!empty($filtros['data_inicio'])) {
            $this->db->where('os_boleto_emitido.data_emissao >=', $filtros['data_inicio']);
        }
        if (!empty($filtros['data_fim'])) {
            $this->db->where('os_boleto_emitido.data_emissao <=', $filtros['data_fim']);
        }
        if (!empty($filtros['vencidos'])) {
            $this->db->where('os_boleto_emitido.data_vencimento <', date('Y-m-d'));
            $this->db->where('os_boleto_emitido.status', 'Emitido');
        }

        $this->db->order_by('os_boleto_emitido.data_emissao', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * Obter resumo para dashboard
     */
    public function getResumo($periodo = 'mes_atual')
    {
        if ($periodo == 'mes_atual') {
            $this->db->where('data_emissao >=', date('Y-m-01'));
            $this->db->where('data_emissao <=', date('Y-m-t'));
        }

        $this->db->select('status, COUNT(*) as total, SUM(valor_liquido) as valor_total');
        $this->db->group_by('status');

        return $this->db->get('os_boleto_emitido')->result();
    }

    /**
     * Obter boletos vencidos não pagos
     */
    public function getVencidos()
    {
        $this->db->select('os_boleto_emitido.*, os.idOs, clientes.nomeCliente, clientes.celular as celular_cliente');
        $this->db->from('os_boleto_emitido');
        $this->db->join('os', 'os.idOs = os_boleto_emitido.os_id');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where('os_boleto_emitido.status', 'Vencido');
        $this->db->order_by('os_boleto_emitido.data_vencimento', 'ASC');

        return $this->db->get()->result();
    }
}
