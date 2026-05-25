<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cobrancas_model extends CI_Model
{
    /**
     * Whitelist of allowed payment gateway library names.
     * Only these values are accepted when dynamically loading a gateway library.
     */
    private const ALLOWED_GATEWAYS = ['Asaas', 'MercadoPago', 'GerencianetSdk', 'Cora'];

    protected $fillable = [
        'charge_id', 'clientes_id', 'os_id', 'vendas_id', 'payment_gateway',
        'status', 'valor', 'expire_at', 'link', 'pdf_link', 'barcode',
        'linha_digitavel', 'payment_method', 'payment_date', 'created_at',
        'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields, 'vendas.*,os.*');
        $this->db->from($table);
        $this->db->limit($perpage, $start);
        $this->db->order_by('idCobranca', 'desc');
        if (is_array($where) && !empty($where)) {
            $this->db->where($where);
        }

        $query = $this->db->get();
        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->select('cobrancas.*, clientes.*');
        $this->db->from('cobrancas');
        $this->db->where('cobrancas.idCobranca', $id);
        $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getByOs($id)
    {
        $this->db->select('cobrancas.*, clientes.*, os.*');
        $this->db->from('cobrancas');
        $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id');
        $this->db->join('os', 'os.idOs = cobrancas.os_id');
        $this->db->where('cobrancas.charge_id', $id);
        $this->db->group_by('cobrancas.idCobranca');

        return $this->db->get()->row();
    }

    public function getByVendas($id)
    {
        $this->db->select('cobrancas.*, clientes.*, vendas.*');
        $this->db->from('cobrancas');
        $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id');
        $this->db->join('vendas', 'vendas.idVendas = cobrancas.vendas_id');
        $this->db->where('cobrancas.charge_id', $id);
        $this->db->group_by('cobrancas.idCobranca');

        return $this->db->get()->row();
    }

    /**
     * Validate and load a payment gateway library.
     * Only gateway names in the ALLOWED_GATEWAYS whitelist are permitted.
     *
     * @param  string     $gatewayName The payment_gateway value from the database
     * @return bool       True if the library was loaded successfully, false otherwise
     */
    private function loadPaymentGateway($gatewayName)
    {
        if (! in_array($gatewayName, self::ALLOWED_GATEWAYS, true)) {
            log_message('error', 'Rejected payment gateway library: ' . $gatewayName . ' is not in the allowed whitelist.');
            return false;
        }

        $this->load->library('Gateways/' . $gatewayName, null, 'PaymentGateway');
        return true;
    }

    public function add($table, $data, $returnId = false)
    {
        if ($table === 'cobrancas') {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            if ($returnId == true) {
                return $this->db->insert_id($table);
            }

            return true;
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        if ($table === 'cobrancas') {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }

    /**
     * Contar cobranças por cliente
     */
    public function countByCliente($cliente_id)
    {
        try {
            $this->db->where('clientes_id', $cliente_id);
            $query = $this->db->select('COUNT(*) as total')->get('cobrancas');

            if ($query && $query->num_rows() > 0) {
                return (int) $query->row()->total;
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao contar cobranças por cliente: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Buscar cobranças por cliente
     */
    public function getByCliente($cliente_id, $perpage = 10, $start = 0)
    {
        try {
            $this->db->select('cobrancas.*, clientes.nomeCliente');
            $this->db->from('cobrancas');
            $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id');
            $this->db->where('cobrancas.clientes_id', $cliente_id);
            $this->db->order_by('cobrancas.idCobranca', 'desc');
            $this->db->limit($perpage, $start);
            $query = $this->db->get();
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar cobranças por cliente: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar boletos por cliente (cobranças do tipo boleto)
     */
    public function getBoletosByCliente($cliente_id, $perpage = 10, $start = 0)
    {
        try {
            $this->db->select('cobrancas.*, clientes.nomeCliente');
            $this->db->from('cobrancas');
            $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id');
            $this->db->where('cobrancas.clientes_id', $cliente_id);
            $this->db->where_in('cobrancas.payment_gateway', ['gerencianet_boleto', 'boleto', 'cora_boleto']);
            $this->db->order_by('cobrancas.idCobranca', 'desc');
            $this->db->limit($perpage, $start);
            $query = $this->db->get();
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar boletos por cliente: ' . $e->getMessage());
            return [];
        }
    }

    public function atualizarStatus($idCobranca)
    {
        $cobranca = $this->getById($idCobranca);
        if (empty($cobranca)) {
            return $this->session->set_flashdata('error', 'Cobrança não existe!');
        }

        $gatewayDePagamento = $cobranca->payment_gateway;
        if (! $this->loadPaymentGateway($gatewayDePagamento)) {
            return false;
        }

        $result = $this->PaymentGateway->atualizarDados($cobranca->idCobranca);

        return $result;
    }

    public function confirmarPagamento($idCobranca)
    {
        $cobranca = $this->getById($idCobranca);
        if (empty($cobranca)) {
            return $this->session->set_flashdata('error', 'Cobrança não existe!');
        }

        $gatewayDePagamento = $cobranca->payment_gateway;
        if (! $this->loadPaymentGateway($gatewayDePagamento)) {
            return false;
        }

        $result = $this->PaymentGateway->confirmarPagamento($cobranca->idCobranca);

        return $result;
    }

    public function cancelarPagamento($idCobranca)
    {
        $cobranca = $this->getById($idCobranca);
        if (empty($cobranca)) {
            return $this->session->set_flashdata('error', 'Cobrança não existe!');
        }

        $gatewayDePagamento = $cobranca->payment_gateway;
        if (! $this->loadPaymentGateway($gatewayDePagamento)) {
            return false;
        }

        $result = $this->PaymentGateway->cancelar($cobranca->idCobranca);

        return $result;
    }

    public function enviarEmail($idCobranca)
    {
        $cobranca = $this->getById($idCobranca);
        if (empty($cobranca)) {
            return $this->session->set_flashdata('error', 'Cobrança não existe!');
        }

        $gatewayDePagamento = $cobranca->payment_gateway;
        if (! $this->loadPaymentGateway($gatewayDePagamento)) {
            return false;
        }

        $result = $this->PaymentGateway->enviarPorEmail($cobranca->idCobranca);

        return $result;
    }
}
