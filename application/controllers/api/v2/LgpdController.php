<?php
/**
 * LgpdController - API v2
 * Endpoints para conformidade com a LGPD (Lei Geral de Protecao de Dados)
 * - Exportacao de dados (Art. 18)
 * - Anonimizacao de dados pessoais
 * - Gerenciamento de consentimento
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class LgpdController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('clientes_model');
        $this->load->model('os_model');
        $this->load->helper('audit_helper');
    }

    /**
     * GET /api/v2/lgpd/clientes/{id}/exportar
     * Exporta todos os dados de um cliente (direito de portabilidade - Art. 18)
     */
    public function exportar(int $id): void
    {
        $this->checkPermission('lgpd_exportar');

        $cliente = $this->getClientOr404($id);

        $data = [
            'cliente' => $this->sanitizeCliente($cliente),
            'ordens_servico' => $this->getOsData($id),
            'cobrancas' => $this->getCobrancasData($id),
            'lancamentos' => $this->getLancamentosData($id),
            'exportado_em' => date('c'),
            'exportado_por' => $this->currentUser->nome ?? 'api',
        ];

        log_audit('EXPORT', 'clientes', $id, null, ['tipo' => 'exportacao_lgpd']);

        $this->success($data);
    }

    /**
     * POST /api/v2/lgpd/clientes/{id}/anonimizar
     * Anonimiza dados pessoais do cliente (direito ao esquecimento - Art. 18, VI)
     * Mantem registros financeiros e OS para conformidade fiscal
     */
    public function anonimizar(int $id): void
    {
        $this->checkPermission('lgpd_anonimizar');

        $cliente = $this->getClientOr404($id);

        $suffix = strtoupper(substr(md5($id . time()), 0, 8));

        $anonymizedData = [
            'nomeCliente'  => 'ANONIMIZADO_' . $suffix,
            'documento'    => '00000000000',
            'email'        => 'anonimizado_' . $suffix . '@anonimizado.local',
            'telefone'     => '0000000000',
            'celular'      => '0000000000',
            'contato'      => 'ANONIMIZADO',
            'rua'          => 'ANONIMIZADO',
            'numero'       => '0',
            'complemento'  => 'ANONIMIZADO',
            'bairro'       => 'ANONIMIZADO',
            'cidade'       => 'ANONIMIZADO',
            'estado'       => 'XX',
            'cep'           => '00000000',
            'cpfCnpj'      => null,
            'asaas_id'     => null,
            'senha'        => null,
            'consentimento_lgpd' => 0,
            'data_consentimento' => null,
        ];

        $oldData = (array) $cliente;
        $this->db->where('idClientes', $id);
        $this->db->update('clientes', $anonymizedData);

        log_audit('ANONIMIZAR', 'clientes', $id, $oldData, $anonymizedData);

        $this->success([
            'message' => 'Dados pessoais anonimizados com sucesso. Registros financeiros e OS foram mantidos.',
            'cliente_id' => $id,
        ]);
    }

    /**
     * GET /api/v2/lgpd/clientes/{id}/consentimento
     * Retorna o status de consentimento LGPD do cliente
     */
    public function consentimento(int $id): void
    {
        $cliente = $this->getClientOr404($id);

        $this->success([
            'cliente_id'           => (int) $cliente->idClientes,
            'consentimento_lgpd'   => (int) ($cliente->consentimento_lgpd ?? 0),
            'data_consentimento'   => $cliente->data_consentimento ?? null,
            'origem_dados'         => $cliente->origem_dados ?? null,
        ]);
    }

    /**
     * POST /api/v2/lgpd/clientes/{id}/consentimento
     * Registra consentimento LGPD do cliente
     */
    public function consentimento_post(int $id): void
    {
        $this->checkPermission('lgpd_consentimento');

        $cliente = $this->getClientOr404($id);
        $input = $this->getJsonInput();
        $origem = $input['origem_dados'] ?? 'consentimento_explicito';

        $this->db->where('idClientes', $id);
        $this->db->update('clientes', [
            'consentimento_lgpd'   => 1,
            'data_consentimento'    => date('Y-m-d H:i:s'),
            'origem_dados'          => $origem,
        ]);

        log_audit('CONSENT', 'clientes', $id, null, [
            'consentimento_lgpd' => 1,
            'data_consentimento' => date('Y-m-d H:i:s'),
            'origem_dados'       => $origem,
        ]);

        $this->success([
            'message'              => 'Consentimento registrado com sucesso.',
            'cliente_id'           => $id,
            'consentimento_lgpd'   => 1,
            'data_consentimento'   => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * POST /api/v2/lgpd/clientes/{id}/revogar_consentimento
     * Revoga consentimento LGPD do cliente (Art. 8, §5)
     */
    public function revogar_consentimento(int $id): void
    {
        $this->checkPermission('lgpd_consentimento');

        $cliente = $this->getClientOr404($id);
        $input = $this->getJsonInput();
        $motivo = $input['motivo'] ?? 'revogacao_explicita';

        $oldData = [
            'consentimento_lgpd' => (int) ($cliente->consentimento_lgpd ?? 0),
            'data_consentimento' => $cliente->data_consentimento ?? null,
            'origem_dados'       => $cliente->origem_dados ?? null,
        ];

        $this->db->where('idClientes', $id);
        $this->db->update('clientes', [
            'consentimento_lgpd'   => 0,
            'data_consentimento'    => null,
            'origem_dados'         => null,
        ]);

        log_audit('REVOKE_CONSENT', 'clientes', $id, $oldData, [
            'consentimento_lgpd' => 0,
            'motivo'             => $motivo,
        ]);

        $this->success([
            'message'              => 'Consentimento revogado com sucesso.',
            'cliente_id'           => $id,
            'consentimento_lgpd'   => 0,
        ]);
    }

    /**
     * GET /api/v2/lgpd/vazamentos
     * Lista notificacoes de vazamento registradas
     */
    public function vazamentos(): void
    {
        $this->checkPermission('cAuditoria');

        if (!$this->db->table_exists('data_breach_notifications')) {
            $this->success([]);
            return;
        }

        $this->load->model('Data_breach_model', 'breachModel');
        $limit = (int) ($this->input->get('limit') ?: 50);
        $offset = (int) ($this->input->get('offset') ?: 0);

        $results = $this->breachModel->getAll($limit, $offset);
        $this->success($results);
    }

    /**
     * GET /api/v2/lgpd/vazamentos/{id}
     * Detalhe de uma notificacao de vazamento
     */
    public function vazamento_detalhe(int $id): void
    {
        $this->checkPermission('cAuditoria');

        if (!$this->db->table_exists('data_breach_notifications')) {
            $this->notFound('Notificacao de vazamento');
            return;
        }

        $this->load->model('Data_breach_model', 'breachModel');
        $result = $this->breachModel->getById($id);

        if (!$result) {
            $this->notFound('Notificacao de vazamento');
            return;
        }

        $this->success($result);
    }

    // ========================================================================
    // Private helpers
    // ========================================================================

    private function getClientOr404(int $id)
    {
        $cliente = $this->clientes_model->getById($id);
        if (!$cliente) {
            $this->notFound('Cliente');
            exit;
        }
        return $cliente;
    }

    private function sanitizeCliente($cliente): array
    {
        return [
            'idClientes'      => $cliente->idClientes,
            'nomeCliente'     => $cliente->nomeCliente,
            'documento'       => $cliente->documento,
            'email'           => $cliente->email,
            'telefone'        => $cliente->telefone,
            'celular'         => $cliente->celular,
            'dataCadastro'    => $cliente->dataCadastro,
            'consentimento_lgpd'  => $cliente->consentimento_lgpd ?? 0,
            'data_consentimento'  => $cliente->data_consentimento ?? null,
            'origem_dados'    => $cliente->origem_dados ?? null,
        ];
    }

    private function getOsData(int $clienteId): array
    {
        $this->db->select('idOs, dataInicial, dataFinal, descricaoProduto, defeito, status, valorTotal, faturado');
        $this->db->where('clientes_id', $clienteId);
        $query = $this->db->get('os');
        return $query ? $query->result_array() : [];
    }

    private function getCobrancasData(int $clienteId): array
    {
        if (!$this->db->table_exists('cobrancas')) {
            return [];
        }
        $this->db->where('client_id', $clienteId);
        $query = $this->db->get('cobrancas');
        return $query ? $query->result_array() : [];
    }

    private function getLancamentosData(int $clienteId): array
    {
        if (!$this->db->table_exists('lancamentos')) {
            return [];
        }
        $this->db->where('cliente_id', $clienteId);
        $query = $this->db->get('lancamentos');
        return $query ? $query->result_array() : [];
    }
}