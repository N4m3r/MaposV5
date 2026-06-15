<?php
/**
 * Controller: Ux_dup
 * Busca duplicatas provaveis em entidades (F4.5).
 *
 * GET /ux_dup/buscar?entidade=clientes&campo=documento&valor=12345678900
 *
 * Para performance e seguranca:
 *   - Apenas entidades permitidas (whitelist)
 *   - Apenas campos indexados (whitelist)
 *   - Limite de 10 resultados
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ux_dup extends MY_Controller
{
    /**
     * Whitelist de (entidade, campo) suportados.
     * Cada entrada mapeia para SQL LIKE otimizado.
     */
    private $whitelist = [
        'clientes' => [
            'documento' => ['idField' => 'idCliente',     'nameField' => 'nomeCliente'],
            'nomeCliente'=>['idField' => 'idCliente',     'nameField' => 'nomeCliente'],
            'email'     => ['idField' => 'idCliente',     'nameField' => 'nomeCliente'],
            'telefone'  => ['idField' => 'idCliente',     'nameField' => 'nomeCliente'],
        ],
        'produtos' => [
            'descricao' => ['idField' => 'idProdutos',    'nameField' => 'descricao'],
            'codDeBarra'=> ['idField' => 'idProdutos',    'nameField' => 'descricao'],
        ],
        'servicos' => [
            'nome'      => ['idField' => 'idServicos',    'nameField' => 'nome'],
        ],
    ];

    public function buscar()
    {
        $entidade = $this->input->get('entidade', true);
        $campo    = $this->input->get('campo', true);
        $valor    = trim((string) $this->input->get('valor'));

        if (empty($entidade) || empty($campo) || empty($valor) || strlen($valor) < 3) {
            return $this->json_success(['matches' => []]);
        }

        if (!isset($this->whitelist[$entidade][$campo])) {
            return $this->json_error('Entidade/campo nao permitido', 400);
        }
        $cfg = $this->whitelist[$entidade][$campo];

        $this->load->database();
        if (!$this->db->table_exists($entidade)) {
            return $this->json_success(['matches' => []]);
        }

        // LIKE para campos de texto, exato para documento/email
        $isText = in_array($campo, ['nomeCliente', 'descricao', 'nome', 'email', 'telefone'], true);
        if ($isText) {
            $like = '%' . $this->db->escape_like_str($valor) . '%';
            $this->db->like($campo, $like);
        } else {
            // documento, codDeBarra — match exato (apos sanitizar)
            $clean = preg_replace('/\D/', '', $valor);
            if ($clean === '') return $this->json_success(['matches' => []]);
            $this->db->where($campo, $clean);
        }
        // Soft delete se aplicavel
        if ($this->db->field_exists('deleted_at', $entidade)) {
            $this->db->where('deleted_at IS NULL', null, false);
        }
        $this->db->limit(10);
        $rows = $this->db->get($entidade)->result();

        $matches = [];
        $baseUrl = 'index.php/' . rtrim($entidade, 's') . '/visualizar/';
        // Caso especial: rota OS = 'os/visualizar'
        if ($entidade === 'os') $baseUrl = 'index.php/os/visualizar/';
        $nameField = $cfg['nameField'];
        $idField   = $cfg['idField'];
        foreach ($rows as $r) {
            $matches[] = [
                'id'    => (int) ($r->$idField ?? 0),
                'label' => (string) ($r->$nameField ?? 'Registro #' . ($r->$idField ?? '?')),
                'url'   => site_url($baseUrl . ($r->$idField ?? '')),
            ];
        }
        return $this->json_success(['matches' => $matches]);
    }

    private function json_success($d, $c = 200) { return json_success($d, $c); }
    private function json_error($m, $c = 400)   { return json_error($m, $c); }
}
