<?php
/**
 * Bulk actions (F4.6)
 *
 * Endpoint unico para acoes em lote em qualquer entidade.
 *
 * POST /bulk/exec
 *  acao: concluir|excluir|imprimir|exportar|status
 *  entidade: os|clientes|servicos|produtos|cobrancas
 *  ids: array de IDs
 *  status: novo status (apenas para acao=status)
 *
 * GET /bulk/imprimir?entidade=os&ids=1,2,3
 * GET /bulk/exportar?entidade=os&ids=1,2,3
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Bulk extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function exec()
    {
        if (!$this->input->is_ajax_request() && $this->input->method() !== 'post') {
            show_404();
        }

        $acao = $this->input->post('acao');
        $entidade = $this->input->post('entidade');
        $ids = $this->input->post('ids');

        if (!is_array($ids)) {
            $ids = explode(',', (string)$ids);
        }
        $ids = array_filter(array_map('intval', $ids));
        if (empty($ids)) {
            return $this->_json(['success' => false, 'msg' => 'Nenhum ID enviado'], 400);
        }

        // Whitelist de entidades
        $allowed = [
            'os'        => ['table' => 'os',         'pk' => 'idOs',          'perm' => 'eOs'],
            'clientes'  => ['table' => 'clientes',   'pk' => 'idClientes',    'perm' => 'eCliente'],
            'servicos'  => ['table' => 'servicos',   'pk' => 'idServicos',    'perm' => 'eServico'],
            'produtos'  => ['table' => 'produtos',   'pk' => 'idProdutos',    'perm' => 'eProduto'],
            'cobrancas' => ['table' => 'cobrancas',  'pk' => 'idCobrancas',   'perm' => 'eCobranca'],
        ];

        if (!isset($allowed[$entidade])) {
            return $this->_json(['success' => false, 'msg' => 'Entidade invalida'], 400);
        }
        $cfg = $allowed[$entidade];

        // Permissao
        $permissao = $this->session->userdata('permissao');
        if (isset($cfg['perm']) && $cfg['perm'] && !$this->permission->checkPermission($permissao, $cfg['perm'])) {
            return $this->_json(['success' => false, 'msg' => 'Sem permissao'], 403);
        }

        // Whitelist de acoes
        $allowed_actions = ['concluir', 'excluir', 'status'];
        if (!in_array($acao, $allowed_actions, true)) {
            return $this->_json(['success' => false, 'msg' => 'Acao invalida'], 400);
        }

        // Soft delete: tabela precisa ter deleted_at
        $temSoftDelete = $this->db->field_exists('deleted_at', $cfg['table']);

        $this->db->trans_start();
        $afetados = 0;

        switch ($acao) {
            case 'concluir':
                // Muda status para 3 (concluida) — só faz sentido para OS
                if ($entidade === 'os') {
                    $this->db->where_in($cfg['pk'], $ids)->update($cfg['table'], ['status' => 3]);
                    $afetados = $this->db->affected_rows();
                } else {
                    $afetados = 0;
                }
                break;

            case 'excluir':
                if ($temSoftDelete) {
                    $this->db->where_in($cfg['pk'], $ids)->update($cfg['table'], ['deleted_at' => date('Y-m-d H:i:s')]);
                } else {
                    $this->db->where_in($cfg['pk'], $ids)->delete($cfg['table']);
                }
                $afetados = $this->db->affected_rows();
                break;

            case 'status':
                $novoStatus = (int)$this->input->post('status');
                if ($novoStatus < 0) {
                    return $this->_json(['success' => false, 'msg' => 'Status invalido'], 400);
                }
                $this->db->where_in($cfg['pk'], $ids)->update($cfg['table'], ['status' => $novoStatus]);
                $afetados = $this->db->affected_rows();
                break;
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            return $this->_json(['success' => false, 'msg' => 'Erro no banco de dados'], 500);
        }

        return $this->_json([
            'success'  => true,
            'msg'      => ucfirst($acao) . ': ' . $afetados . ' registro(s) afetado(s).',
            'afetados' => $afetados,
        ]);
    }

    public function imprimir()
    {
        $entidade = $this->input->get('entidade');
        $ids = $this->input->get('ids');
        if (!$entidade || !$ids) {
            show_404();
        }
        // Redireciona para a pagina de impressao da entidade
        switch ($entidade) {
            case 'os':
                redirect(site_url('os/imprimirMultiplo?ids=' . urlencode($ids)));
                break;
            default:
                $this->session->set_flashdata('error', 'Impressao em lote ainda nao disponivel para esta entidade.');
                redirect(base_url());
        }
    }

    public function exportar()
    {
        $entidade = $this->input->get('entidade');
        $ids = $this->input->get('ids');
        if (!$entidade || !$ids) {
            show_404();
        }
        // Exporta como CSV
        $allowed = [
            'clientes'  => ['table' => 'clientes',  'pk' => 'idClientes'],
            'os'        => ['table' => 'os',         'pk' => 'idOs'],
            'servicos'  => ['table' => 'servicos',   'pk' => 'idServicos'],
            'produtos'  => ['table' => 'produtos',   'pk' => 'idProdutos'],
        ];
        if (!isset($allowed[$entidade])) {
            show_404();
        }
        $cfg = $allowed[$entidade];
        $idArr = array_filter(array_map('intval', explode(',', $ids)));
        if (empty($idArr)) {
            show_404();
        }

        $rows = $this->db->select('*')
            ->from($cfg['table'])
            ->where_in($cfg['pk'], $idArr)
            ->get()->result_array();

        if (empty($rows)) {
            $this->session->set_flashdata('error', 'Nenhum registro para exportar.');
            redirect(base_url() . 'index.php/' . $entidade);
        }

        $filename = $entidade . '_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        // BOM para Excel abrir com acentos
        fwrite($out, "\xEF\xBB\xBF");
        // Header
        fputcsv($out, array_keys($rows[0]), ';');
        foreach ($rows as $r) {
            fputcsv($out, $r, ';');
        }
        fclose($out);
        exit;
    }

    private function _json($data, $code = 200)
    {
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
