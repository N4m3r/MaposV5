<?php

class Clientes_model extends MY_Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'idClientes';
    protected $fillable = [
        'nomeCliente', 'documento', 'telefone', 'celular', 'email',
        'sexo', 'rua', 'numero', 'complemento', 'bairro', 'cidade',
        'estado', 'cep', 'contato', 'fornecedor', 'cpfCnpj',
        'inscricao', 'situacao', 'dataExpiracao', 'obs', 'senha'
    ];
    protected $softDelete = true;

    private $mainTable = 'clientes';

    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('idClientes', 'desc');
        if ($perpage > 0) {
            $this->db->limit($perpage, $start);
        }
        if ($where) {
            $this->db->like('nomeCliente', $where);
            $this->db->or_like('documento', $where);
            $this->db->or_like('email', $where);
            $this->db->or_like('telefone', $where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        if (empty($id) || !is_numeric($id)) {
            return false;
        }
        $this->db->select('idClientes, nomeCliente, documento, telefone, celular, email, sexo, rua, numero, complemento, bairro, cidade, estado, cep, contato, fornecedor, cpfCnpj, inscricao, situacao, dataExpiracao, obs, dataCadastro, asaas_id, consentimento_lgpd, data_consentimento, origem_dados, token_acesso');
        if ($this->db->field_exists('deleted_at', $this->mainTable)) {
            $this->db->where('clientes.deleted_at IS NULL', null, false);
        }
        $this->db->where('idClientes', $id);
        $this->db->limit(1);

        $query = $this->db->get('clientes');
        if (!$query) {
            return false;
        }
        return $query->row();
    }

    /**
     * Retorna todos os clientes ativos (nao deletados)
     */
    public function getAll()
    {
        if ($this->db->field_exists('deleted_at', $this->mainTable)) {
            $this->db->where('clientes.deleted_at IS NULL', null, false);
        }
        $this->db->order_by('nomeCliente', 'ASC');
        return $this->db->get('clientes')->result();
    }

    /**
     * Buscar cliente por documento (CPF/CNPJ)
     * Busca com e sem formatação
     */
    public function getByDocumento($documento)
    {
        $documentoLimpo = preg_replace('/[^0-9]/', '', $documento);
        $documentoFormatado = $this->formatarDocumento($documentoLimpo);

        $this->db->group_start();
        $this->db->where('documento', $documentoLimpo);
        $this->db->or_where('documento', $documentoFormatado);
        $this->db->group_end();
        $this->db->limit(1);

        $query = $this->db->get('clientes');
        return $query ? $query->row() : null;
    }

    /**
     * Formatar documento (CPF/CNPJ)
     */
    private function formatarDocumento($doc)
    {
        $doc = preg_replace('/[^0-9]/', '', $doc);
        if (strlen($doc) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        } elseif (strlen($doc) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        }
        return $doc;
    }

    public function add($table, $data)
    {
        if ($table === 'clientes') {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() >= 1) {
            $insertId = $this->db->insert_id($table);
            if ($table === 'clientes') {
                $this->generateAccessToken($insertId);
                log_audit('INSERT', 'clientes', $insertId, null, $data);
            }
            return $insertId;
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        if ($table === 'clientes') {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        if ($table === $this->mainTable) {
            $oldData = (array) $this->db->where($fieldID, $ID)->get($this->mainTable)->row();
        }
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            if ($table === $this->mainTable) {
                log_audit('UPDATE', 'clientes', $ID, $oldData ?? null, $data);
            }
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        if ($table === $this->mainTable && $this->db->field_exists('deleted_at', $this->mainTable)) {
            // Soft delete for main table
            $this->db->where($fieldID, $ID);
            $this->db->update($this->mainTable, ['deleted_at' => date('Y-m-d H:i:s')]);
            if ($this->db->affected_rows() >= 0) {
                log_audit('DELETE', 'clientes', $ID);
                return true;
            }
            return false;
        }

        // Hard delete for other tables (produtos_os, servicos_os, etc.)
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() >= 1) {
            return true;
        }

        return false;
    }

    /**
     * Restore a soft-deleted client
     */
    public function restore(int $id): bool
    {
        $this->db->where($this->primaryKey, $id);
        $this->db->update($this->mainTable, ['deleted_at' => null]);
        if ($this->db->affected_rows() >= 0) {
            log_audit('RESTORE', 'clientes', $id);
            return true;
        }
        return false;
    }

    /**
     * Permanently delete a client (administrative only)
     */
    public function forceDelete($id)
    {
        $this->db->where($this->primaryKey, $id);
        $this->db->delete($this->mainTable);
        return $this->db->affected_rows() >= 1;
    }

    /**
     * Get client by access token
     */
    public function getByToken($token)
    {
        if (empty($token)) {
            return null;
        }
        $this->db->where('token_acesso', $token);
        if ($this->db->field_exists('deleted_at', $this->mainTable)) {
            $this->db->where('deleted_at IS NULL', null, false);
        }
        $this->db->limit(1);
        return $this->db->get('clientes')->row();
    }

    /**
     * Generate access token for a client
     */
    public function generateAccessToken($id)
    {
        $token = bin2hex(random_bytes(32));
        $this->db->where($this->primaryKey, $id);
        $this->db->update($this->mainTable, ['token_acesso' => $token]);
        return $token;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }

    public function getOsByCliente($id)
    {
        $this->db->where('clientes_id', $id);
        $this->db->order_by('idOs', 'desc');
        $this->db->limit(10);

        return $this->db->get('os')->result();
    }

    /**
     * Retorna todas as OS vinculados ao cliente
     *
     * @param  int  $id
     * @return array
     */
    public function getAllOsByClient($id)
    {
        $this->db->where('clientes_id', $id);

        return $this->db->get('os')->result();
    }

    /**
     * Remover todas as OS por cliente
     * Performance: uses batch DELETE with IN clause instead of 3 queries per OS in a loop
     *
     * @param  array  $os
     * @return bool
     */
    public function removeClientOs($os)
    {
        if (empty($os)) {
            return true;
        }

        $os_ids = array_map(function ($o) {
            return $o->idOs;
        }, $os);

        $this->db->trans_start();

        // Batch delete – 3 queries total regardless of how many OS
        $this->db->where_in('os_id', $os_ids);
        $this->db->delete('servicos_os');

        $this->db->where_in('os_id', $os_ids);
        $this->db->delete('produtos_os');

        $this->db->where_in('idOs', $os_ids);
        $this->db->delete('os');

        $this->db->trans_complete();

        return $this->db->trans_status() !== false;
    }

    /**
     * Retorna todas as Vendas vinculados ao cliente
     *
     * @param  int  $id
     * @return array
     */
    public function getAllVendasByClient($id)
    {
        $this->db->where('clientes_id', $id);

        return $this->db->get('vendas')->result();
    }

    /**
     * Remover todas as Vendas por cliente
     * Performance: uses batch DELETE with IN clause instead of 2 queries per Venda in a loop
     *
     * @param  array  $vendas
     * @return bool
     */
    public function removeClientVendas($vendas)
    {
        if (empty($vendas)) {
            return true;
        }

        $venda_ids = array_map(function ($v) {
            return $v->idVendas;
        }, $vendas);

        $this->db->trans_start();

        // Batch delete – 2 queries total regardless of how many Vendas
        $this->db->where_in('vendas_id', $venda_ids);
        $this->db->delete('itens_de_vendas');

        $this->db->where_in('idVendas', $venda_ids);
        $this->db->delete('vendas');

        $this->db->trans_complete();

        return $this->db->trans_status() !== false;
    }
}
