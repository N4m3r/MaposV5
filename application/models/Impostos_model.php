<?php
/**
 * Model: Impostos Simples Nacional
 * Gerencia retenção automática de impostos em boletos e integração com DRE
 */
class Impostos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        if (file_exists(APPPATH . 'models/Dre_model.php')) {
            $this->load->model('dre_model');
        }
        if (file_exists(APPPATH . 'models/Certificado_model.php')) {
            $this->load->model('certificado_model');
        }
    }

    // ==================== CONFIGURAÇÕES ====================

    /**
     * Configurações padrão do sistema
     */
    private $config_padrao = [
        'IMPOSTO_RETENCAO_AUTOMATICA' => '0',
        'IMPOSTO_ANEXO_PADRAO' => 'III',
        'IMPOSTO_FAIXA_ATUAL' => '1',
        'IMPOSTO_ISS_MUNICIPAL' => '5.00',
        'IMPOSTO_DRE_INTEGRACAO' => '0',
        'IMPOSTO_REGIME_TRIBUTARIO' => 'simples_nacional',
    ];

    /**
     * Obtém configuração do sistema
     */
    public function getConfig($chave)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('config_sistema_impostos')) {
            return $this->config_padrao[$chave] ?? null;
        }

        try {
            $this->db->where('chave', $chave);
            $query = $this->db->get('config_sistema_impostos');

            if ($query === false) {
                return $this->config_padrao[$chave] ?? null;
            }

            $result = $query->row();
            return $result ? $result->valor : ($this->config_padrao[$chave] ?? null);
        } catch (Exception $e) {
            return $this->config_padrao[$chave] ?? null;
        }
    }

    /**
     * Atualiza configuração
     */
    public function setConfig($chave, $valor)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('config_sistema_impostos')) {
            return false;
        }

        $this->db->where('chave', $chave);
        $query = $this->db->get('config_sistema_impostos');

        if ($query === false) {
            return false;
        }

        $exists = $query->row();

        if ($exists) {
            $this->db->where('chave', $chave);
            return $this->db->update('config_sistema_impostos', [
                'valor' => $valor,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->db->insert('config_sistema_impostos', [
                'chave' => $chave,
                'valor' => $valor,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Obtém todas as alíquotas de um anexo
     */
    public function getAliquotasAnexo($anexo = 'III')
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('configuracoes_impostos')) {
            // Retornar dados padrão se tabela não existe
            return $this->getAliquotasPadrao($anexo);
        }

        try {
            $this->db->where('anexo_simples', $anexo);
            $this->db->where('ativo', 1);
            $this->db->order_by('faixa_simples', 'ASC');

            $query = $this->db->get('configuracoes_impostos');

            if ($query === false) {
                return $this->getAliquotasPadrao($anexo);
            }

            $rows = $query->result();

            // Se não houver dados, retornar padrão
            if (empty($rows)) {
                return $this->getAliquotasPadrao($anexo);
            }

            // Mapear colunas para o formato esperado
            $result = [];
            foreach ($rows as $row) {
                $item = new stdClass();
                $item->id = $row->id;
                $item->anexo = $row->anexo_simples;
                $item->faixa = $row->faixa_simples;
                $item->aliquota_nominal = $row->aliquota_simples;
                $item->irpj = $row->aliquota_ir;
                $item->csll = $row->aliquota_csll;
                $item->cofins = $row->aliquota_cofins;
                $item->pis = $row->aliquota_pis;
                $item->cpp = $row->aliquota_inss;
                $item->iss = $row->aliquota_iss;
                $item->ativo = $row->ativo;
                $result[] = $item;
            }

            return $result;
        } catch (Exception $e) {
            return $this->getAliquotasPadrao($anexo);
        }
    }

    /**
     * Retorna alíquotas padrão quando não há dados no banco
     */
    private function getAliquotasPadrao($anexo = 'III')
    {
        // Alíquotas padrão do Anexo III - Serviços (Simples Nacional 2024)
        $aliquotas_iii = [
            (object) ['id' => 1, 'anexo' => 'III', 'faixa' => 1, 'aliquota_nominal' => 6.00, 'irpj' => 0.36, 'csll' => 0.36, 'cofins' => 1.38, 'pis' => 0.30, 'cpp' => 2.40, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 2, 'anexo' => 'III', 'faixa' => 2, 'aliquota_nominal' => 11.20, 'irpj' => 0.72, 'csll' => 0.72, 'cofins' => 2.76, 'pis' => 0.60, 'cpp' => 4.80, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 3, 'anexo' => 'III', 'faixa' => 3, 'aliquota_nominal' => 13.95, 'irpj' => 0.90, 'csll' => 0.90, 'cofins' => 3.45, 'pis' => 0.75, 'cpp' => 5.95, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 4, 'anexo' => 'III', 'faixa' => 4, 'aliquota_nominal' => 16.17, 'irpj' => 1.04, 'csll' => 1.04, 'cofins' => 3.99, 'pis' => 0.87, 'cpp' => 6.89, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 5, 'anexo' => 'III', 'faixa' => 5, 'aliquota_nominal' => 18.00, 'irpj' => 1.16, 'csll' => 1.16, 'cofins' => 4.45, 'pis' => 0.97, 'cpp' => 7.66, 'iss' => 0.00, 'ativo' => 1],
        ];

        // Alíquotas do Anexo IV - Construção (Simples Nacional 2024)
        $aliquotas_iv = [
            (object) ['id' => 6, 'anexo' => 'IV', 'faixa' => 1, 'aliquota_nominal' => 4.50, 'irpj' => 0.27, 'csll' => 0.27, 'cofins' => 1.04, 'pis' => 0.23, 'cpp' => 1.95, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 7, 'anexo' => 'IV', 'faixa' => 2, 'aliquota_nominal' => 9.00, 'irpj' => 0.54, 'csll' => 0.54, 'cofins' => 2.07, 'pis' => 0.45, 'cpp' => 3.90, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 8, 'anexo' => 'IV', 'faixa' => 3, 'aliquota_nominal' => 11.34, 'irpj' => 0.68, 'csll' => 0.68, 'cofins' => 2.61, 'pis' => 0.57, 'cpp' => 4.91, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 9, 'anexo' => 'IV', 'faixa' => 4, 'aliquota_nominal' => 12.82, 'irpj' => 0.77, 'csll' => 0.77, 'cofins' => 2.95, 'pis' => 0.64, 'cpp' => 5.55, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 10, 'anexo' => 'IV', 'faixa' => 5, 'aliquota_nominal' => 14.05, 'irpj' => 0.84, 'csll' => 0.84, 'cofins' => 3.23, 'pis' => 0.71, 'cpp' => 6.09, 'iss' => 0.00, 'ativo' => 1],
        ];

        // Alíquotas do Anexo V - Comércio (CNAE 4751201 - Materiais de construção)
        // Alíquotas de 2024 para comércio varejista
        $aliquotas_v = [
            (object) ['id' => 11, 'anexo' => 'V', 'faixa' => 1, 'aliquota_nominal' => 4.00, 'irpj' => 0.80, 'csll' => 0.80, 'cofins' => 2.34, 'pis' => 0.50, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 12, 'anexo' => 'V', 'faixa' => 2, 'aliquota_nominal' => 7.30, 'irpj' => 1.46, 'csll' => 1.46, 'cofins' => 2.67, 'pis' => 0.63, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 13, 'anexo' => 'V', 'faixa' => 3, 'aliquota_nominal' => 9.50, 'irpj' => 1.90, 'csll' => 1.90, 'cofins' => 3.27, 'pis' => 0.77, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 14, 'anexo' => 'V', 'faixa' => 4, 'aliquota_nominal' => 10.74, 'irpj' => 2.15, 'csll' => 2.15, 'cofins' => 3.46, 'pis' => 0.82, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
            (object) ['id' => 15, 'anexo' => 'V', 'faixa' => 5, 'aliquota_nominal' => 11.78, 'irpj' => 2.36, 'csll' => 2.36, 'cofins' => 3.67, 'pis' => 0.87, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
        ];

        if ($anexo == 'IV') {
            return $aliquotas_iv;
        } elseif ($anexo == 'V') {
            return $aliquotas_v;
        }

        return $aliquotas_iii;
    }

    /**
     * Obtém alíquota específica por faixa
     */
    public function getAliquotaFaixa($anexo, $faixa)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('configuracoes_impostos')) {
            return $this->getAliquotaPadrao($anexo, $faixa);
        }

        try {
            $this->db->where('anexo_simples', $anexo);
            $this->db->where('faixa_simples', $faixa);
            $this->db->where('ativo', 1);

            $query = $this->db->get('configuracoes_impostos');

            if ($query === false) {
                return $this->getAliquotaPadrao($anexo, $faixa);
            }

            $row = $query->row();

            if (!$row) {
                return $this->getAliquotaPadrao($anexo, $faixa);
            }

            // Mapear colunas de configuracoes_impostos para o formato esperado pelo simulador
            $result = new stdClass();
            $result->id = $row->id;
            $result->anexo = $row->anexo_simples;
            $result->faixa = $row->faixa_simples;
            $result->aliquota_nominal = $row->aliquota_simples;
            $result->irpj = $row->aliquota_ir;
            $result->csll = $row->aliquota_csll;
            $result->cofins = $row->aliquota_cofins;
            $result->pis = $row->aliquota_pis;
            $result->cpp = $row->aliquota_inss;
            $result->iss = $row->aliquota_iss;
            $result->ativo = $row->ativo;

            return $result;
        } catch (Exception $e) {
            return $this->getAliquotaPadrao($anexo, $faixa);
        }
    }

    /**
     * Retorna alíquota padrão quando não há dados no banco
     */
    private function getAliquotaPadrao($anexo, $faixa)
    {
        $aliquotas = [
            'III' => [
                1 => (object) ['id' => 1, 'anexo' => 'III', 'faixa' => 1, 'aliquota_nominal' => 6.00, 'irpj' => 0.36, 'csll' => 0.36, 'cofins' => 1.38, 'pis' => 0.30, 'cpp' => 2.40, 'iss' => 0.00, 'ativo' => 1],
                2 => (object) ['id' => 2, 'anexo' => 'III', 'faixa' => 2, 'aliquota_nominal' => 11.20, 'irpj' => 0.72, 'csll' => 0.72, 'cofins' => 2.76, 'pis' => 0.60, 'cpp' => 4.80, 'iss' => 0.00, 'ativo' => 1],
                3 => (object) ['id' => 3, 'anexo' => 'III', 'faixa' => 3, 'aliquota_nominal' => 13.95, 'irpj' => 0.90, 'csll' => 0.90, 'cofins' => 3.45, 'pis' => 0.75, 'cpp' => 5.95, 'iss' => 0.00, 'ativo' => 1],
                4 => (object) ['id' => 4, 'anexo' => 'III', 'faixa' => 4, 'aliquota_nominal' => 16.17, 'irpj' => 1.04, 'csll' => 1.04, 'cofins' => 3.99, 'pis' => 0.87, 'cpp' => 6.89, 'iss' => 0.00, 'ativo' => 1],
                5 => (object) ['id' => 5, 'anexo' => 'III', 'faixa' => 5, 'aliquota_nominal' => 18.00, 'irpj' => 1.16, 'csll' => 1.16, 'cofins' => 4.45, 'pis' => 0.97, 'cpp' => 7.66, 'iss' => 0.00, 'ativo' => 1],
            ],
            'IV' => [
                1 => (object) ['id' => 6, 'anexo' => 'IV', 'faixa' => 1, 'aliquota_nominal' => 4.50, 'irpj' => 0.27, 'csll' => 0.27, 'cofins' => 1.04, 'pis' => 0.23, 'cpp' => 1.95, 'iss' => 0.00, 'ativo' => 1],
                2 => (object) ['id' => 7, 'anexo' => 'IV', 'faixa' => 2, 'aliquota_nominal' => 9.00, 'irpj' => 0.54, 'csll' => 0.54, 'cofins' => 2.07, 'pis' => 0.45, 'cpp' => 3.90, 'iss' => 0.00, 'ativo' => 1],
                3 => (object) ['id' => 8, 'anexo' => 'IV', 'faixa' => 3, 'aliquota_nominal' => 11.34, 'irpj' => 0.68, 'csll' => 0.68, 'cofins' => 2.61, 'pis' => 0.57, 'cpp' => 4.91, 'iss' => 0.00, 'ativo' => 1],
                4 => (object) ['id' => 9, 'anexo' => 'IV', 'faixa' => 4, 'aliquota_nominal' => 12.82, 'irpj' => 0.77, 'csll' => 0.77, 'cofins' => 2.95, 'pis' => 0.64, 'cpp' => 5.55, 'iss' => 0.00, 'ativo' => 1],
                5 => (object) ['id' => 10, 'anexo' => 'IV', 'faixa' => 5, 'aliquota_nominal' => 14.05, 'irpj' => 0.84, 'csll' => 0.84, 'cofins' => 3.23, 'pis' => 0.71, 'cpp' => 6.09, 'iss' => 0.00, 'ativo' => 1],
            ],
            'V' => [
                1 => (object) ['id' => 11, 'anexo' => 'V', 'faixa' => 1, 'aliquota_nominal' => 4.00, 'irpj' => 0.80, 'csll' => 0.80, 'cofins' => 2.34, 'pis' => 0.50, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
                2 => (object) ['id' => 12, 'anexo' => 'V', 'faixa' => 2, 'aliquota_nominal' => 7.30, 'irpj' => 1.46, 'csll' => 1.46, 'cofins' => 2.67, 'pis' => 0.63, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
                3 => (object) ['id' => 13, 'anexo' => 'V', 'faixa' => 3, 'aliquota_nominal' => 9.50, 'irpj' => 1.90, 'csll' => 1.90, 'cofins' => 3.27, 'pis' => 0.77, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
                4 => (object) ['id' => 14, 'anexo' => 'V', 'faixa' => 4, 'aliquota_nominal' => 10.74, 'irpj' => 2.15, 'csll' => 2.15, 'cofins' => 3.46, 'pis' => 0.82, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
                5 => (object) ['id' => 15, 'anexo' => 'V', 'faixa' => 5, 'aliquota_nominal' => 11.78, 'irpj' => 2.36, 'csll' => 2.36, 'cofins' => 3.67, 'pis' => 0.87, 'cpp' => 0.00, 'iss' => 0.00, 'ativo' => 1],
            ],
        ];

        return $aliquotas[$anexo][$faixa] ?? $aliquotas['III'][1];
    }

    /**
     * Calcula a alíquota efetiva com base no faturamento
     * (simplificado - usa a faixa configurada)
     * Se houver certificado configurado, tenta identificar automaticamente
     */
    public function getAliquotaEfetiva($anexo = null, $faixa = null)
    {
        // Tentar obter do certificado primeiro (se model existir)
        $certificado = null;
        if (isset($this->certificado_model) && method_exists($this->certificado_model, 'getCertificadoAtivo')) {
            $certificado = $this->certificado_model->getCertificadoAtivo();
        }

        if ($certificado) {
            // Buscar dados do Simples Nacional vinculados ao certificado
            if ($this->db->table_exists('certificado_consultas')) {
                $this->db->where('certificado_id', $certificado->id);
                $this->db->where('tipo_consulta', 'SIMPLES_NACIONAL');
                $this->db->where('sucesso', 1);
                $this->db->order_by('data_consulta', 'DESC');
                $consulta = $this->db->get('certificado_consultas', 1)->row();

                if ($consulta && $consulta->dados_retorno) {
                    $dados = json_decode($consulta->dados_retorno, true);
                    if (isset($dados['anexo_sugerido']) && !$anexo) {
                        $anexo = $dados['anexo_sugerido'];
                    }
                }
            }

            // Se não tiver anexo definido, usar padrão
            if (!$anexo) {
                $anexo = $this->getConfig('IMPOSTO_ANEXO_PADRAO') ?: 'III';
            }

            // Tentar identificar faixa pelo faturamento (simplificado)
            if (!$faixa) {
                $faixa = $this->identificarFaixaFaturamento($certificado->cnpj);
            }
        } else {
            // Usar configurações manuais
            if (!$anexo) {
                $anexo = $this->getConfig('IMPOSTO_ANEXO_PADRAO') ?: 'III';
            }
            if (!$faixa) {
                $faixa = $this->getConfig('IMPOSTO_FAIXA_ATUAL') ?: 1;
            }
        }

        return $this->getAliquotaFaixa($anexo, $faixa);
    }

    /**
     * Identifica a faixa de faturamento com base no histórico
     */
    private function identificarFaixaFaturamento($cnpj)
    {
        // Calcular faturamento dos últimos 12 meses
        $data_inicio = date('Y-m-d', strtotime('-12 months'));
        $data_fim = date('Y-m-t');

        // Verificar se a tabela existe
        if (!$this->db->table_exists('impostos_retidos')) {
            return (int)($this->getConfig('IMPOSTO_FAIXA_ATUAL') ?: 1);
        }

        try {
            $sql = "
                SELECT SUM(valor_bruto) as total
                FROM impostos_retidos
                WHERE data_competencia BETWEEN ? AND ?
            ";

            $query = $this->db->query($sql, [$data_inicio, $data_fim]);

            if ($query === false) {
                return (int)($this->getConfig('IMPOSTO_FAIXA_ATUAL') ?: 1);
            }

            $result = $query->row();
            $faturamento = $result ? ($result->total ?: 0) : 0;
        } catch (Exception $e) {
            return (int)($this->getConfig('IMPOSTO_FAIXA_ATUAL') ?: 1);
        }

        // Definir faixa conforme tabela do Simples Nacional 2024
        // Anexo III - Serviços
        // 1ª Faixa: até R$ 180.000
        // 2ª Faixa: de R$ 180.001 a R$ 360.000
        // 3ª Faixa: de R$ 360.001 a R$ 720.000
        // 4ª Faixa: de R$ 720.001 a R$ 1.800.000
        // 5ª Faixa: de R$ 1.800.001 a R$ 4.800.000

        if ($faturamento <= 180000) return 1;
        if ($faturamento <= 360000) return 2;
        if ($faturamento <= 720000) return 3;
        if ($faturamento <= 1800000) return 4;
        return 5;
    }

    // ==================== CÁLCULO DE IMPOSTOS ====================

    /**
     * Calcula os impostos sobre um valor
     * Retorna array com todos os valores de impostos
     */
    public function calcularImpostos($valor_bruto, $anexo = null, $faixa = null)
    {
        // Verificar regime tributário
        $regime = $this->getConfig('IMPOSTO_REGIME_TRIBUTARIO') ?: 'simples_nacional';

        if ($regime === 'lucro_presumido') {
            return $this->calcularImpostosLucroPresumido($valor_bruto);
        }

        // Simples Nacional (padrão)
        $aliquota = $this->getAliquotaEfetiva($anexo, $faixa);

        if (!$aliquota) {
            return false;
        }

        // Calcular cada imposto baseado na proporção dentro da alíquota nominal
        $total_impostos = 0;
        $aliquota_nominal = floatval($aliquota->aliquota_nominal);

        // Se não houver alíquota nominal, usar divisão padrão
        if ($aliquota_nominal == 0) {
            $aliquota_nominal = 6.0; // Alíquota mínima padrão
        }

        $calculos = [
            'aliquota_nominal' => $aliquota_nominal,
            'aliquota_irpj' => floatval($aliquota->irpj),
            'aliquota_csll' => floatval($aliquota->csll),
            'aliquota_cofins' => floatval($aliquota->cofins),
            'aliquota_pis' => floatval($aliquota->pis),
            'aliquota_cpp' => floatval($aliquota->cpp),
            'aliquota_iss' => floatval($aliquota->iss),
        ];

        // Calcular valores em R$
        $calculos['irpj_valor'] = round($valor_bruto * ($calculos['aliquota_irpj'] / 100), 2);
        $calculos['csll_valor'] = round($valor_bruto * ($calculos['aliquota_csll'] / 100), 2);
        $calculos['cofins_valor'] = round($valor_bruto * ($calculos['aliquota_cofins'] / 100), 2);
        $calculos['pis_valor'] = round($valor_bruto * ($calculos['aliquota_pis'] / 100), 2);
        $calculos['iss_valor'] = round($valor_bruto * ($calculos['aliquota_iss'] / 100), 2);
        $calculos['cpp_valor'] = round($valor_bruto * ($calculos['aliquota_cpp'] / 100), 2);

        // Total de impostos para retenção (exceto CPP que é contribuição previdenciária)
        $calculos['total_impostos'] = $calculos['irpj_valor'] + $calculos['csll_valor'] +
                                       $calculos['cofins_valor'] + $calculos['pis_valor'] +
                                       $calculos['iss_valor'];

        // Total completo incluindo CPP (para NFS-e e display)
        $calculos['valor_total_impostos'] = $calculos['total_impostos'] + $calculos['cpp_valor'];

        $calculos['valor_liquido'] = $valor_bruto - $calculos['valor_total_impostos'];
        $calculos['iss'] = $calculos['iss_valor'];
        $calculos['irpj'] = $calculos['irpj_valor'];
        $calculos['irrf'] = $calculos['irpj_valor'];
        $calculos['csll'] = $calculos['csll_valor'];
        $calculos['cofins'] = $calculos['cofins_valor'];
        $calculos['pis'] = $calculos['pis_valor'];
        $calculos['inss'] = $calculos['cpp_valor'];

        return $calculos;
    }

    /**
     * Calcula impostos para Lucro Presumido
     * Alíquotas fixas de retenção na fonte para prestadores de serviço
     */
    public function calcularImpostosLucroPresumido($valor_bruto)
    {
        // Alíquotas de retenção na fonte para serviços (Lucro Presumido)
        // IRPJ: 4,8% (sobre 32% da base = 1,536% efetivo, mas retenção direta é 1,5%)
        // CSLL: 2,88% (sobre 32% da base), retenção 1%
        // PIS: 0,65%
        // COFINS: 3%
        // ISS: variável por município (padrão 5%)
        $aliquota_iss = floatval($this->getConfig('IMPOSTO_ISS_MUNICIPAL')) ?: 5.00;

        $irpj_valor = round($valor_bruto * 1.5 / 100, 2);    // 1,5% sobre serviços
        $csll_valor = round($valor_bruto * 1.0 / 100, 2);    // 1,0% sobre serviços
        $cofins_valor = round($valor_bruto * 3.0 / 100, 2);   // 3,0%
        $pis_valor = round($valor_bruto * 0.65 / 100, 2);     // 0,65%
        $iss_valor = round($valor_bruto * $aliquota_iss / 100, 2);

        $total_impostos = $irpj_valor + $csll_valor + $cofins_valor + $pis_valor + $iss_valor;

        return [
            'regime' => 'lucro_presumido',
            'aliquota_nominal' => round(($total_impostos / max($valor_bruto, 0.01)) * 100, 2),
            'aliquota_irpj' => 1.5,
            'aliquota_csll' => 1.0,
            'aliquota_cofins' => 3.0,
            'aliquota_pis' => 0.65,
            'aliquota_cpp' => 0,
            'aliquota_iss' => $aliquota_iss,
            'irpj_valor' => $irpj_valor,
            'irpj' => $irpj_valor,
            'irrf' => $irpj_valor,
            'csll_valor' => $csll_valor,
            'csll' => $csll_valor,
            'cofins_valor' => $cofins_valor,
            'cofins' => $cofins_valor,
            'pis_valor' => $pis_valor,
            'pis' => $pis_valor,
            'iss_valor' => $iss_valor,
            'iss' => $iss_valor,
            'cpp_valor' => 0,
            'inss' => 0,
            'total_impostos' => $total_impostos,
            'valor_total_impostos' => $total_impostos,
            'valor_liquido' => $valor_bruto - $total_impostos,
        ];
    }

    /**
     * Calcula impostos apenas com ISS (para cálculo isolado)
     */
    public function calcularISS($valor_bruto)
    {
        $aliquota_iss = floatval($this->getConfig('IMPOSTO_ISS_MUNICIPAL')) ?: 5.00;
        $valor_iss = round($valor_bruto * ($aliquota_iss / 100), 2);

        return [
            'aliquota' => $aliquota_iss,
            'valor' => $valor_iss,
            'valor_liquido' => $valor_bruto - $valor_iss
        ];
    }

    // ==================== RETENÇÃO EM BOLETOS ====================

    /**
     * Registra retenção de impostos para uma cobrança
     * Chamado automaticamente na geração de boleto
     */
    public function reterImpostos($dados)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('impostos_retidos')) {
            return false;
        }

        // Verificar se retenção automática está ativa
        if ($this->getConfig('IMPOSTO_RETENCAO_AUTOMATICA') != '1') {
            return false;
        }

        // Calcular impostos
        $calculos = $this->calcularImpostos($dados['valor_bruto']);

        if (!$calculos) {
            return false;
        }

        // Preparar dados para inserção
        $retencao = [
            'cobranca_id' => $dados['cobranca_id'] ?? null,
            'os_id' => $dados['os_id'] ?? null,
            'venda_id' => $dados['venda_id'] ?? null,
            'cliente_id' => $dados['cliente_id'],
            'valor_bruto' => $dados['valor_bruto'],
            'valor_liquido' => $calculos['valor_liquido'],
            'aliquota_aplicada' => $calculos['aliquota_nominal'],
            'irpj_valor' => $calculos['irpj_valor'],
            'csll_valor' => $calculos['csll_valor'],
            'cofins_valor' => $calculos['cofins_valor'],
            'pis_valor' => $calculos['pis_valor'],
            'iss_valor' => $calculos['iss_valor'],
            'total_impostos' => $calculos['total_impostos'],
            'data_competencia' => $dados['data_competencia'] ?? date('Y-m-01'),
            'data_retencao' => date('Y-m-d H:i:s'),
            'nota_fiscal' => $dados['nota_fiscal'] ?? null,
            'status' => 'Retido',
            'observacao' => $dados['observacao'] ?? 'Retenção automática na geração do boleto',
            'usuarios_id' => $this->session->userdata('id_admin'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('impostos_retidos', $retencao)) {
            $retencao_id = $this->db->insert_id();

            // Integrar com DRE se configurado
            if ($this->getConfig('IMPOSTO_DRE_INTEGRACAO') == '1') {
                $this->integrarComDRE($retencao, $retencao_id);
            }

            return $retencao_id;
        }

        return false;
    }

    /**
     * Integra a retenção com lançamentos DRE
     */
    private function integrarComDRE($retencao, $retencao_id)
    {
        if (!isset($this->dre_model) || !method_exists($this->dre_model, 'adicionarLancamento')) {
            return;
        }

        if (!$this->db->table_exists('dre_contas')) {
            return;
        }

        // Buscar conta de deduções do DRE
        $this->db->where('codigo', '2.1'); // Impostos Sobre Vendas
        $conta_imposto = $this->db->get('dre_contas')->row();

        if ($conta_imposto) {
            $lancamento_id = $this->dre_model->adicionarLancamento([
                'conta_id' => $conta_imposto->id,
                'data' => date('Y-m-d'),
                'valor' => $retencao['total_impostos'],
                'tipo_movimento' => 'DEBITO',
                'descricao' => 'Impostos retidos Simples Nacional - Boleto #' . ($retencao['cobranca_id'] ?? 'N/A'),
                'documento' => 'NF ' . ($retencao['nota_fiscal'] ?? 'N/A'),
            ]);

            // Atualizar vínculo
            if ($lancamento_id) {
                $this->db->where('id', $retencao_id);
                $this->db->update('impostos_retidos', [
                    'dre_lancamento_id' => $lancamento_id
                ]);
            }
        }
    }

    /**
     * Obtém retenções por período
     */
    public function getRetencoes($data_inicio, $data_fim, $cliente_id = null)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('impostos_retidos')) {
            return [];
        }

        $this->db->select('ir.*, c.nomeCliente, os.idOs as os_numero, cobrancas.charge_id');
        $this->db->from('impostos_retidos ir');
        $this->db->join('clientes c', 'c.idClientes = ir.cliente_id', 'left');
        $this->db->join('os', 'os.idOs = ir.os_id', 'left');
        $this->db->join('cobrancas', 'cobrancas.idCobranca = ir.cobranca_id', 'left');
        $this->db->where('ir.data_competencia >=', $data_inicio);
        $this->db->where('ir.data_competencia <=', $data_fim);

        if ($cliente_id) {
            $this->db->where('ir.cliente_id', $cliente_id);
        }

        $this->db->order_by('ir.data_retencao', 'DESC');

        $query = $this->db->get();

        // Verificar se a query foi bem sucedida
        if ($query === false) {
            return [];
        }

        return $query->result();
    }

    /**
     * Obtém totais de impostos por período
     */
    public function getTotaisImpostos($data_inicio, $data_fim)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('impostos_retidos')) {
            return (object) [
                'total_retencoes' => 0,
                'total_bruto' => 0,
                'total_liquido' => 0,
                'total_impostos' => 0,
                'total_irpj' => 0,
                'total_csll' => 0,
                'total_cofins' => 0,
                'total_pis' => 0,
                'total_iss' => 0,
                'percentual_imposto' => 0
            ];
        }

        $sql = "
            SELECT
                COUNT(*) as total_retencoes,
                SUM(valor_bruto) as total_bruto,
                SUM(valor_liquido) as total_liquido,
                SUM(total_impostos) as total_impostos,
                SUM(irpj_valor) as total_irpj,
                SUM(csll_valor) as total_csll,
                SUM(cofins_valor) as total_cofins,
                SUM(pis_valor) as total_pis,
                SUM(iss_valor) as total_iss
            FROM impostos_retidos
            WHERE data_competencia BETWEEN ? AND ?
            AND status != 'Estornado'
        ";

        $query = $this->db->query($sql, [$data_inicio, $data_fim]);

        // Verificar se a query foi bem sucedida
        if ($query === false) {
            return (object) [
                'total_retencoes' => 0,
                'total_bruto' => 0,
                'total_liquido' => 0,
                'total_impostos' => 0,
                'total_irpj' => 0,
                'total_csll' => 0,
                'total_cofins' => 0,
                'total_pis' => 0,
                'total_iss' => 0,
                'percentual_imposto' => 0
            ];
        }

        $result = $query->row();

        // Adicionar percentuais
        if ($result && $result->total_bruto > 0) {
            $result->percentual_imposto = round(($result->total_impostos / $result->total_bruto) * 100, 2);
        } else {
            $result->percentual_imposto = 0;
        }

        return $result;
    }

    /**
     * Obtém evolução mensal de impostos
     */
    public function getEvolucaoImpostos($meses = 6)
    {
        $dados = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $data_fim = date('Y-m-t', strtotime("-{$i} months"));
            $data_inicio = date('Y-m-01', strtotime("-{$i} months"));

            $totais = $this->getTotaisImpostos($data_inicio, $data_fim);

            $dados[] = [
                'mes' => date('Y-m', strtotime($data_inicio)),
                'mes_formatado' => date('m/Y', strtotime($data_inicio)),
                'total_bruto' => $totais->total_bruto ?: 0,
                'total_impostos' => $totais->total_impostos ?: 0,
                'total_liquido' => $totais->total_liquido ?: 0,
                'percentual' => $totais->percentual_imposto ?: 0,
            ];
        }

        return $dados;
    }

    /**
     * Atualiza status de uma retenção
     */
    public function atualizarStatusRetencao($id, $status, $observacao = null)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('impostos_retidos')) {
            return false;
        }

        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($observacao) {
            $data['observacao'] = $observacao;
        }

        $this->db->where('id', $id);
        return $this->db->update('impostos_retidos', $data);
    }

    /**
     * Processa retenção automática quando uma cobrança é criada
     * Chamado pelo hook após geração de boleto
     */
    public function processarRetencaoCobranca($cobranca_id, $dados_adicionais = [])
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('impostos_retidos')) {
            return false;
        }

        // Verificar se retenção automática está ativa
        if ($this->getConfig('IMPOSTO_RETENCAO_AUTOMATICA') != '1') {
            return false;
        }

        // Buscar dados da cobrança
        $this->db->where('idCobranca', $cobranca_id);
        $cobranca = $this->db->get('cobrancas')->row();

        if (!$cobranca) {
            return false;
        }

        // Verificar se já existe retenção para esta cobrança
        $this->db->where('cobranca_id', $cobranca_id);
        $query_existente = $this->db->get('impostos_retidos');

        if ($query_existente === false) {
            return false;
        }

        if ($query_existente->num_rows() > 0) {
            return false; // Já existe retenção
        }

        // Preparar dados para retenção
        $dados_retencao = [
            'cobranca_id' => $cobranca_id,
            'os_id' => $cobranca->os_id,
            'venda_id' => $cobranca->vendas_id,
            'cliente_id' => $cobranca->clientes_id,
            'data_competencia' => date('Y-m-01'),
            'nota_fiscal' => $dados_adicionais['nota_fiscal'] ?? null,
        ];

        // Buscar valor da OS ou Venda
        if ($cobranca->os_id) {
            $this->db->where('idOs', $cobranca->os_id);
            $os = $this->db->get('os')->row();
            $dados_retencao['valor_bruto'] = $os ? floatval($os->valorTotal) : 0;
        } elseif ($cobranca->vendas_id) {
            $this->db->where('idVendas', $cobranca->vendas_id);
            $venda = $this->db->get('vendas')->row();
            $dados_retencao['valor_bruto'] = $venda ? floatval($venda->valorTotal) : 0;
        } else {
            $dados_retencao['valor_bruto'] = 0;
        }

        if ($dados_retencao['valor_bruto'] <= 0) {
            return false;
        }

        return $this->reterImpostos($dados_retencao);
    }

    /**
     * Estorna uma retenção (quando boleto é cancelado)
     */
    public function estornarRetencao($cobranca_id)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('impostos_retidos')) {
            return false;
        }

        $this->db->where('cobranca_id', $cobranca_id);
        $this->db->where('status', 'Retido');
        $query = $this->db->get('impostos_retidos');

        if ($query === false) {
            return false;
        }

        $retencao = $query->row();

        if ($retencao) {
            $this->db->where('id', $retencao->id);
            $this->db->update('impostos_retidos', [
                'status' => 'Estornado',
                'observacao' => 'Estornado por cancelamento do boleto',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Estornar também no DRE
            if ($retencao->dre_lancamento_id && isset($this->dre_model) && method_exists($this->dre_model, 'excluirLancamento')) {
                $this->dre_model->excluirLancamento($retencao->dre_lancamento_id);
            }

            return true;
        }

        return false;
    }

    // ==================== RELATÓRIOS ====================

    /**
     * Gera relatório de impostos para o DRE
     */
    public function getRelatorioImpostosDRE($data_inicio, $data_fim)
    {
        $totais = $this->getTotaisImpostos($data_inicio, $data_fim);
        $retencoes = $this->getRetencoes($data_inicio, $data_fim);
        $evolucao = $this->getEvolucaoImpostos(6);

        return [
            'totais' => $totais,
            'retencoes' => $retencoes,
            'evolucao' => $evolucao,
            'periodo' => [
                'inicio' => $data_inicio,
                'fim' => $data_fim
            ]
        ];
    }

    /**
     * Obtém configurações de tributação para NFS-e
     */
    public function getConfiguracaoTributacao()
    {
        return [
            'codigo_tributacao_nacional' => $this->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL') ?: '010701',
            'codigo_tributacao_municipal' => $this->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL') ?: '100',
            'descricao_servico' => $this->getConfig('IMPOSTO_DESCRICAO_SERVICO') ?: 'Suporte técnico em informática, inclusive instalação, configuração e manutenção de programas de computação e bancos de dados.',
            'aliquota_iss' => $this->getConfig('IMPOSTO_ISS_MUNICIPAL') ?: '5.00',
        ];
    }

    /**
     * Exporta relatório de impostos
     */
    public function exportarRelatorio($data_inicio, $data_fim)
    {
        $retencoes = $this->getRetencoes($data_inicio, $data_fim);

        $csv = [
            ['RELATÓRIO DE IMPOSTOS RETIDOS - SIMPLES NACIONAL'],
            ['Período: ' . date('d/m/Y', strtotime($data_inicio)) . ' a ' . date('d/m/Y', strtotime($data_fim))],
            [''],
            ['Cliente', 'NF', 'Valor Bruto', 'IRPJ', 'CSLL', 'COFINS', 'PIS', 'ISS', 'Total Impostos', 'Valor Líquido', 'Data', 'Status']
        ];

        foreach ($retencoes as $r) {
            $csv[] = [
                $r->nomeCliente,
                $r->nota_fiscal ?: '-',
                number_format($r->valor_bruto, 2, ',', '.'),
                number_format($r->irpj_valor, 2, ',', '.'),
                number_format($r->csll_valor, 2, ',', '.'),
                number_format($r->cofins_valor, 2, ',', '.'),
                number_format($r->pis_valor, 2, ',', '.'),
                number_format($r->iss_valor, 2, ',', '.'),
                number_format($r->total_impostos, 2, ',', '.'),
                number_format($r->valor_liquido, 2, ',', '.'),
                date('d/m/Y', strtotime($r->data_retencao)),
                $r->status
            ];
        }

        return $csv;
    }
}
