<?php
/**
 * Controller: Busca global (Fase 1.7)
 *
 * Endpoint: GET /busca?q=termo
 * Resposta JSON para alimentar o modal de busca global (Cmd/Ctrl+K).
 *
 * Retorna ate 5 resultados por categoria:
 *   - clientes, os, produtos, servicos
 *
 * Permissao exigida: usuario logado (qualquer papel).
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Busca extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Apenas exige login (ja garantido pelo MY_Controller::is_logado() implicitamente)
    }

    /**
     * GET /busca?q=termo
     * Resposta: { success, results: { clientes: [...], os: [...], produtos: [...], servicos: [...] } }
     */
    public function index()
    {
        $q = trim((string) $this->input->get('q'));
        if (strlen($q) < 2) {
            json_success('', ['results' => ['clientes' => [], 'os' => [], 'produtos' => [], 'servicos' => []]]);
            return;
        }

        $this->load->database();

        $results = [
            'clientes' => $this->buscarClientes($q),
            'os'       => $this->buscarOs($q),
            'produtos' => $this->buscarProdutos($q),
            'servicos' => $this->buscarServicos($q),
        ];

        json_success('', ['results' => $results]);
    }

    private function buscarClientes($q)
    {
        $like = '%' . $this->db->escape_like_str($q) . '%';
        $rows = $this->db
            ->select('idCliente, nomeCliente, documento, telefone, email')
            ->from('clientes')
            ->group_start()
                ->like('nomeCliente', $like)
                ->or_like('documento', $like)
                ->or_like('telefone', $like)
                ->or_like('email', $like)
            ->group_end()
            ->where('deleted_at IS NULL', null, false)
            ->limit(5)
            ->get()
            ->result();

        return array_map(function ($r) {
            return [
                'id'       => (int) $r->idCliente,
                'title'    => $r->nomeCliente,
                'subtitle' => trim(implode(' · ', array_filter([
                    $r->documento,
                    $r->telefone,
                    $r->email,
                ]))),
                'url'      => site_url('clientes/visualizar/' . $r->idCliente),
                'icon'     => 'bx-user',
            ];
        }, $rows);
    }

    private function buscarOs($q)
    {
        $like = '%' . $this->db->escape_like_str($q) . '%';
        $rows = $this->db
            ->select('o.idOs, o.dataInicial, o.status, o.ValorTotal, c.nomeCliente')
            ->from('os o')
            ->join('clientes c', 'c.idCliente = o.clientes_id = o.clientes_id', 'left', false)
            ->group_start()
                ->like('c.nomeCliente', $like)
                ->or_like('o.idOs', $q)
            ->group_end()
            ->where('o.deleted_at IS NULL', null, false)
            ->order_by('o.idOs', 'desc')
            ->limit(5)
            ->get()
            ->result();

        return array_map(function ($r) {
            return [
                'id'       => (int) $r->idOs,
                'title'    => 'OS #' . $r->idOs . ' — ' . ($r->nomeCliente ?: 'Sem cliente'),
                'subtitle' => 'Status: ' . $r->status,
                'url'      => site_url('os/visualizar/' . $r->idOs),
                'icon'     => 'bx-file',
            ];
        }, $rows);
    }

    private function buscarProdutos($q)
    {
        $like = '%' . $this->db->escape_like_str($q) . '%';
        $rows = $this->db
            ->select('idProdutos, descricao, codDeBarra, precoVenda, estoque')
            ->from('produtos')
            ->group_start()
                ->like('descricao', $like)
                ->or_like('codDeBarra', $like)
            ->group_end()
            ->where('deleted_at IS NULL', null, false)
            ->limit(5)
            ->get()
            ->result();

        return array_map(function ($r) {
            return [
                'id'       => (int) $r->idProdutos,
                'title'    => $r->descricao,
                'subtitle' => trim(implode(' · ', array_filter([
                    $r->codDeBarra ? 'Cód: ' . $r->codDeBarra : null,
                    isset($r->estoque) ? 'Estoque: ' . $r->estoque : null,
                    isset($r->precoVenda) ? 'R$ ' . number_format((float) $r->precoVenda, 2, ',', '.') : null,
                ]))),
                'url'      => site_url('produtos/editar/' . $r->idProdutos),
                'icon'     => 'bx-package',
            ];
        }, $rows);
    }

    private function buscarServicos($q)
    {
        $like = '%' . $this->db->escape_like_str($q) . '%';
        $rows = $this->db
            ->select('idServicos, nome, preco, descricao')
            ->from('servicos')
            ->like('nome', $like)
            ->or_like('descricao', $like)
            ->limit(5)
            ->get()
            ->result();

        return array_map(function ($r) {
            return [
                'id'       => (int) $r->idServicos,
                'title'    => $r->nome,
                'subtitle' => 'R$ ' . number_format((float) $r->preco, 2, ',', '.'),
                'url'      => site_url('servicos/editar/' . $r->idServicos),
                'icon'     => 'bx-wrench',
            ];
        }, $rows);
    }
}
