<?php
/**
 * Model: NFSe Emitida para OS
 * Gerencia emissão e controle de notas fiscais de serviço
 */

class Nfse_emitida_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('impostos_model');
    }

    /**
     * Emitir nova NFS-e para uma OS
     */
    public function emitir($os_id, $dados = [])
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return ['error' => 'Tabela de NFS-e não existe. Execute as migrations.'];
        }

        // Verificar se já existe NFSe emitida
        $existente = $this->getByOsId($os_id);
        if ($existente && in_array($existente->situacao, ['Emitida', 'Pendente'])) {
            return ['error' => 'Já existe NFS-e emitida para esta OS'];
        }

        // Buscar dados da OS
        $this->load->model('os_model');
        $os = $this->os_model->getById($os_id);

        if (!$os) {
            return ['error' => 'OS não encontrada'];
        }

        // Calcular valores
        $valor_servicos = $dados['valor_servicos'] ?? ($os->valorTotal ?? 0);
        $valor_deducoes = $dados['valor_deducoes'] ?? 0;

        // Calcular impostos
        $calculo_impostos = $this->calcularImpostosNfse($valor_servicos);
        if (!$calculo_impostos) {
            return ['error' => 'Erro ao calcular impostos. Verifique as configurações de impostos.'];
        }

        // Valor líquido após deduções
        $valor_liquido = $valor_servicos - $calculo_impostos['valor_total_impostos'] - $valor_deducoes;

        // Dados da NFS-e
        $nfse_data = [
            'os_id' => $os_id,
            'data_emissao' => date('Y-m-d H:i:s'),
            'valor_servicos' => $valor_servicos,
            'valor_deducoes' => $valor_deducoes,
            'valor_liquido' => $valor_liquido,
            'regime_tributario' => $dados['regime_tributario'] ?? 'simples_nacional',
            'valor_das' => $dados['valor_das'] ?? null,
            'aliquota_iss' => $calculo_impostos['aliquota_iss'],
            'valor_iss' => $calculo_impostos['iss'],
            'valor_inss' => $calculo_impostos['inss'] ?? 0,
            'valor_irrf' => $calculo_impostos['irrf'] ?? 0,
            'valor_csll' => $calculo_impostos['csll'] ?? 0,
            'valor_pis' => $calculo_impostos['pis'] ?? 0,
            'valor_cofins' => $calculo_impostos['cofins'] ?? 0,
            'valor_total_impostos' => $calculo_impostos['valor_total_impostos'],
            'retem_iss' => $dados['retem_iss'] ?? 0,
            'retem_irrf' => $dados['retem_irrf'] ?? 0,
            'retem_pis' => $dados['retem_pis'] ?? 0,
            'retem_cofins' => $dados['retem_cofins'] ?? 0,
            'retem_csll' => $dados['retem_csll'] ?? 0,
            'valor_retencao_iss' => $dados['valor_retencao_iss'] ?? 0,
            'valor_retencao_irrf' => $dados['valor_retencao_irrf'] ?? 0,
            'valor_retencao_pis' => $dados['valor_retencao_pis'] ?? 0,
            'valor_retencao_cofins' => $dados['valor_retencao_cofins'] ?? 0,
            'valor_retencao_csll' => $dados['valor_retencao_csll'] ?? 0,
            'valor_total_retencao' => $dados['valor_total_retencao'] ?? 0,
            'competencia' => $dados['competencia'] ?? date('Y-m-01'),
            'situacao' => 'Pendente',
            'emitido_por' => $this->session->userdata('idUsuarios'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Só incluir 'ambiente' se a coluna existir no banco
        if ($this->db->field_exists('ambiente', 'os_nfse_emitida')) {
            $nfse_data['ambiente'] = $dados['ambiente'] ?? 'homologacao';
        }

        // Inserir no banco
        if ($this->db->insert('os_nfse_emitida', $nfse_data)) {
            $nfse_id = $this->db->insert_id();

            // Atualizar status da OS
            $this->db->where('idOs', $os_id);
            $this->db->update('os', [
                'nfse_status' => 'Pendente',
                'valor_com_impostos' => $valor_liquido
            ]);

            log_info('NFS-e criada para OS #' . $os_id . ' - ID: ' . $nfse_id);

            // Integrar com DRE
            $this->load->model('dre_model');
            $this->dre_model->integrarNFSe($nfse_id, $nfse_data);

            return [
                'success' => true,
                'nfse_id' => $nfse_id,
                'valor_liquido' => $valor_liquido,
                'impostos' => $calculo_impostos,
                'message' => 'NFS-e criada com sucesso. Valor líquido após impostos: R$ ' . number_format($valor_liquido, 2, ',', '.')
            ];
        }

        return ['error' => 'Erro ao criar NFS-e no banco de dados'];
    }

    /**
     * Calcular impostos para NFS-e
     */
    private function calcularImpostosNfse($valor_bruto)
    {
        // Obter configurações de impostos
        $anexo = $this->impostos_model->getConfig('IMPOSTO_ANEXO_PADRAO') ?: 'III';
        $faixa = $this->impostos_model->getConfig('IMPOSTO_FAIXA_ATUAL') ?: 1;
        $aliquota_iss = $this->impostos_model->getConfig('IMPOSTO_ISS_MUNICIPAL') ?: 5.00;

        // Calcular impostos do Simples Nacional
        $calculos = $this->impostos_model->calcularImpostos($valor_bruto, $anexo, $faixa);

        if (!$calculos) {
            return null;
        }

        return [
            'aliquota_iss' => $aliquota_iss,
            'iss' => $calculos['iss'] ?? ($valor_bruto * $aliquota_iss / 100),
            'irpj' => $calculos['irpj'] ?? 0,
            'csll' => $calculos['csll'] ?? 0,
            'cofins' => $calculos['cofins'] ?? 0,
            'pis' => $calculos['pis'] ?? 0,
            'inss' => $calculos['inss'] ?? 0,
            'valor_total_impostos' => $calculos['valor_total_impostos'] ?? 0
        ];
    }

    /**
     * Confirmar emissão da NFS-e (após envio para prefeitura)
     */
    public function confirmarEmissao($nfse_id, $dados_nfse)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return ['error' => 'Tabela de NFS-e não existe'];
        }

        $this->db->where('id', $nfse_id);
        $update = [
            'situacao' => 'Emitida',
            'numero_nfse' => $dados_nfse['numero'] ?? null,
            'chave_acesso' => $dados_nfse['chave'] ?? null,
            'codigo_verificacao' => $dados_nfse['codigo_verificacao'] ?? null,
            'protocolo' => $dados_nfse['protocolo'] ?? null,
            'link_impressao' => $dados_nfse['link_impressao'] ?? null,
            'xml_path' => $dados_nfse['xml_path'] ?? null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->update('os_nfse_emitida', $update)) {
            // Atualizar status da OS
            $nfse = $this->getById($nfse_id);
            if ($nfse && isset($nfse->os_id)) {
                $this->db->where('idOs', $nfse->os_id);
                $this->db->update('os', ['nfse_status' => 'Emitida']);
            }

            return ['success' => true];
        }

        return ['error' => 'Erro ao atualizar NFS-e'];
    }

    /**
     * Vincular boleto à NFS-e
     */
    public function vincularBoleto($nfse_id, $boleto_id)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return ['error' => 'Tabela de NFS-e não existe'];
        }

        $this->db->where('id', $nfse_id);
        if ($this->db->update('os_nfse_emitida', [
            'cobranca_id' => $boleto_id,
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return ['success' => true];
        }
        return ['error' => 'Erro ao vincular boleto'];
    }

    /**
     * Cancelar NFS-e
     */
    public function cancelar($nfse_id, $motivo = '')
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return ['error' => 'Tabela de NFS-e não existe'];
        }

        $this->db->where('id', $nfse_id);
        if ($this->db->update('os_nfse_emitida', [
            'situacao' => 'Cancelada',
            'mensagem_retorno' => $motivo,
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            // Atualizar status da OS
            $nfse = $this->getById($nfse_id);
            if ($nfse && isset($nfse->os_id)) {
                $this->db->where('idOs', $nfse->os_id);
                $this->db->update('os', ['nfse_status' => 'Cancelada']);
            }

            return ['success' => true];
        }
        return ['error' => 'Erro ao cancelar NFS-e'];
    }

    /**
     * Obter NFS-e por ID
     */
    public function getById($id)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return null;
        }

        $this->db->where('id', $id);
        $query = $this->db->get('os_nfse_emitida');

        return $query ? $query->row() : null;
    }

    /**
     * Obter NFS-e por OS ID
     */
    public function getByOsId($os_id)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return null;
        }

        $this->db->where('os_id', $os_id);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('os_nfse_emitida');

        return $query ? $query->row() : null;
    }

    /**
     * Obter todas as NFS-e de uma OS
     */
    public function getAllByOsId($os_id)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return [];
        }

        $this->db->where('os_id', $os_id);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('os_nfse_emitida');

        return $query ? $query->result() : [];
    }

    /**
     * Listar NFS-e com filtros
     */
    public function listar($filtros = [], $limit = 50, $offset = 0)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida') || !$this->db->table_exists('os') || !$this->db->table_exists('clientes')) {
            return [];
        }

        $this->db->select('os_nfse_emitida.*, os.idOs, clientes.nomeCliente, os.status as os_status');
        $this->db->from('os_nfse_emitida');
        $this->db->join('os', 'os.idOs = os_nfse_emitida.os_id');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');

        if (!empty($filtros['situacao'])) {
            $this->db->where('os_nfse_emitida.situacao', $filtros['situacao']);
        }
        if (!empty($filtros['data_inicio'])) {
            $this->db->where('os_nfse_emitida.data_emissao >=', $filtros['data_inicio']);
        }
        if (!empty($filtros['data_fim'])) {
            $this->db->where('os_nfse_emitida.data_emissao <=', $filtros['data_fim']);
        }
        if (!empty($filtros['cliente'])) {
            $this->db->like('clientes.nomeCliente', $filtros['cliente']);
        }

        $this->db->order_by('os_nfse_emitida.data_emissao', 'DESC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    /**
     * Contar total de NFS-e
     */
    public function count($filtros = [])
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return 0;
        }

        if (!empty($filtros['situacao'])) {
            $this->db->where('situacao', $filtros['situacao']);
        }
        return $this->db->count_all_results('os_nfse_emitida');
    }

    /**
     * Buscar NFS-e por cliente
     */
    public function getByCliente($cliente_id, $perpage = 10, $start = 0)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida') || !$this->db->table_exists('os')) {
            return [];
        }

        $this->db->select('os_nfse_emitida.*, os.idOs');
        $this->db->from('os_nfse_emitida');
        $this->db->join('os', 'os.idOs = os_nfse_emitida.os_id');
        $this->db->where('os.clientes_id', $cliente_id);
        $this->db->order_by('os_nfse_emitida.data_emissao', 'DESC');
        $this->db->limit($perpage, $start);

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    /**
     * Contar NFS-e por cliente
     */
    public function countByCliente($cliente_id)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida') || !$this->db->table_exists('os')) {
            return 0;
        }

        $this->db->where('os.clientes_id', $cliente_id);
        $this->db->join('os', 'os.idOs = os_nfse_emitida.os_id', 'inner');

        $count = $this->db->count_all_results('os_nfse_emitida');
        return is_numeric($count) ? (int) $count : 0;
    }

    /**
     * Obter resumo para dashboard
     */
    public function getResumo($periodo = 'mes_atual')
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return [];
        }

        if ($periodo == 'mes_atual') {
            $this->db->where('data_emissao >=', date('Y-m-01'));
            $this->db->where('data_emissao <=', date('Y-m-t'));
        }

        $this->db->select('situacao, COUNT(*) as total, SUM(valor_servicos) as valor_total');
        $this->db->group_by('situacao');

        $query = $this->db->get('os_nfse_emitida');
        return $query ? $query->result() : [];
    }

    /**
     * Confirmar emissão com dados retornados pela API Nacional
     * Atualiza registro local com chave de acesso, protocolo, número, etc.
     */
    public function confirmarEmissaoApi($nfse_id, $dados_nfse, $xml_dps = null, $xml_nfse = null)
    {
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return ['error' => 'Tabela de NFS-e não existe'];
        }

        $update = [
            'situacao' => 'Emitida',
            'numero_nfse' => $dados_nfse['numero'] ?? null,
            'chave_acesso' => $dados_nfse['chave'] ?? null,
            'codigo_verificacao' => $dados_nfse['codigo_verificacao'] ?? null,
            'protocolo' => $dados_nfse['protocolo'] ?? null,
            'link_impressao' => $dados_nfse['link_impressao'] ?? null,
            'url_danfe' => $dados_nfse['url_danfe'] ?? null,
            'data_emissao_api' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Adicionar XMLs se as colunas existirem
        if ($xml_dps && $this->db->field_exists('xml_dps', 'os_nfse_emitida')) {
            $update['xml_dps'] = $xml_dps;
        }
        if ($xml_nfse && $this->db->field_exists('xml_nfse', 'os_nfse_emitida')) {
            $update['xml_nfse'] = $xml_nfse;
        }

        $this->db->where('id', $nfse_id);
        if ($this->db->update('os_nfse_emitida', $update)) {
            // Atualizar status da OS
            $nfse = $this->getById($nfse_id);
            if ($nfse && isset($nfse->os_id)) {
                $this->db->where('idOs', $nfse->os_id);
                $this->db->update('os', ['nfse_status' => 'Emitida']);
            }

            log_message('info', 'NFS-e Nacional: Emissão confirmada. ID=' . $nfse_id . ' Chave=' . ($dados_nfse['chave'] ?? ''));
            return ['success' => true];
        }

        return ['error' => 'Erro ao atualizar NFS-e'];
    }

    /**
     * Registrar cancelamento via API Nacional
     */
    public function registrarCancelamentoApi($nfse_id, $motivo = '', $data_cancelamento = null)
    {
        if (!$this->db->table_exists('os_nfse_emitida')) {
            return ['error' => 'Tabela de NFS-e não existe'];
        }

        $update = [
            'situacao' => 'Cancelada',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->db->field_exists('motivo_cancelamento', 'os_nfse_emitida')) {
            $update['motivo_cancelamento'] = $motivo;
        } else {
            $update['mensagem_retorno'] = 'Cancelada via API Nacional: ' . $motivo;
        }

        $this->db->where('id', $nfse_id);
        if ($this->db->update('os_nfse_emitida', $update)) {
            // Atualizar status da OS
            $nfse = $this->getById($nfse_id);
            if ($nfse && isset($nfse->os_id)) {
                $this->db->where('idOs', $nfse->os_id);
                $this->db->update('os', ['nfse_status' => 'Cancelada']);
            }

            log_message('info', 'NFS-e Nacional: Cancelamento registrado. ID=' . $nfse_id . ' Motivo=' . $motivo);
            return ['success' => true];
        }

        return ['error' => 'Erro ao registrar cancelamento'];
    }
}
