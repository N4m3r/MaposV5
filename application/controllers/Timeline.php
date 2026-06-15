<?php
/**
 * Timeline — feed de atividades (F4.7)
 *
 * Junta eventos de varias fontes (atividades, OS, cobrancas, clientes)
 * e retorna um feed cronologico. Pode ser filtrado por:
 *  - usuario (default: logado)
 *  - periodo (ultimos 7 dias, 30 dias, todos)
 *  - modulo (os, clientes, financeiro, atividades)
 *  - contexto_id (ID de uma OS, cliente, etc)
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Timeline extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function index()
    {
        $this->data['title'] = 'Timeline de Atividades';
        $this->data['view']  = 'tema/timeline';
        return $this->layout();
    }

    public function feed()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $usuarioId = (int)$this->input->get('usuario_id') ?: (int)$this->session->userdata('id');
        $periodo = $this->input->get('periodo') ?: '30';   // dias
        $modulo  = $this->input->get('modulo') ?: '';
        $limite  = min(200, max(10, (int)$this->input->get('limite') ?: 80));

        $eventos = [];

        // 1) Atividades do sistema (tabela atividades)
        if ($this->db->table_exists('atividades') && !$modulo || $modulo === 'atividades') {
            $this->db->select('a.id, a.titulo, a.data_inicio, a.data_fim, a.status, a.os_id, u.nome AS usuario')
                ->from('atividades a')
                ->join('usuarios u', 'u.idUsuarios = a.usuario_id', 'left')
                ->where('a.data_inicio >=', date('Y-m-d', strtotime("-{$periodo} days")))
                ->order_by('a.data_inicio', 'DESC')
                ->limit($limite);
            $rows = $this->db->get()->result_array();
            foreach ($rows as $r) {
                $eventos[] = [
                    'tipo'      => $this->_ativTipo($r['status']),
                    'icone'     => 'bx-timer',
                    'titulo'    => 'Atividade: ' . ($r['titulo'] ?? ''),
                    'descricao' => 'Por ' . ($r['usuario'] ?? 'usuario') . ' na OS #' . ($r['os_id'] ?? '-'),
                    'data'      => $r['data_fim'] ?: $r['data_inicio'],
                    'modulo'    => 'atividades',
                    'link'      => site_url('atividades/visualizar/' . ($r['id'] ?? '')),
                ];
            }
        }

        // 2) OS recentes (criadas / editadas)
        if ($this->db->table_exists('os') && (!$modulo || $modulo === 'os')) {
            $this->db->select('o.idOs, o.dataInicial, o.dataFinal, o.status, o.valorTotal, c.nomeCliente')
                ->from('os o')
                ->join('clientes c', 'c.idClientes = o.cliente_id', 'left')
                ->where('o.dataInicial >=', date('Y-m-d', strtotime("-{$periodo} days")))
                ->order_by('o.dataInicial', 'DESC')
                ->limit($limite);
            $rows = $this->db->get()->result_array();
            foreach ($rows as $r) {
                $eventos[] = [
                    'tipo'      => 'info',
                    'icone'     => 'bx-file',
                    'titulo'    => 'OS #' . $r['idOs'] . ' aberta',
                    'descricao' => 'Cliente: ' . ($r['nomeCliente'] ?? 'N/I') . ' - R$ ' . number_format((float)($r['valorTotal'] ?? 0), 2, ',', '.'),
                    'data'      => $r['dataInicial'],
                    'modulo'    => 'os',
                    'link'      => site_url('os/visualizar/' . $r['idOs']),
                ];
            }
        }

        // 3) Cobrancas recentes
        if ($this->db->table_exists('cobrancas') && (!$modulo || $modulo === 'financeiro')) {
            $this->db->select('cb.idCobrancas, cb.data_criacao, cb.vencimento, cb.status, cb.valor, c.nomeCliente')
                ->from('cobrancas cb')
                ->join('clientes c', 'c.idClientes = cb.cliente_id', 'left')
                ->where('cb.data_criacao >=', date('Y-m-d', strtotime("-{$periodo} days")))
                ->order_by('cb.data_criacao', 'DESC')
                ->limit($limite);
            $rows = $this->db->get()->result_array();
            foreach ($rows as $r) {
                $eventos[] = [
                    'tipo'      => $r['status'] === 'pago' ? 'success' : 'warning',
                    'icone'     => 'bx-credit-card',
                    'titulo'    => 'Cobranca ' . ($r['status'] === 'pago' ? 'paga' : 'criada'),
                    'descricao' => 'Cliente: ' . ($r['nomeCliente'] ?? 'N/I') . ' - R$ ' . number_format((float)($r['valor'] ?? 0), 2, ',', '.'),
                    'data'      => $r['data_criacao'],
                    'modulo'    => 'financeiro',
                    'link'      => site_url('cobrancas/cobrancas/visualizar/' . $r['idCobrancas']),
                ];
            }
        }

        // 4) Auditoria (logs do sistema)
        if ($this->db->table_exists('auditoria') && (!$modulo || $modulo === 'auditoria')) {
            $this->db->select('a.id, a.acao, a.tabela, a.registro_id, a.created_at, u.nome AS usuario')
                ->from('auditoria a')
                ->join('usuarios u', 'u.idUsuarios = a.usuario_id', 'left')
                ->where('a.created_at >=', date('Y-m-d H:i:s', strtotime("-{$periodo} days")))
                ->order_by('a.created_at', 'DESC')
                ->limit($limite);
            $rows = $this->db->get()->result_array();
            foreach ($rows as $r) {
                $eventos[] = [
                    'tipo'      => 'info',
                    'icone'     => 'bx-history',
                    'titulo'    => $r['acao'] . ' em ' . $r['tabela'],
                    'descricao' => 'Por ' . ($r['usuario'] ?? 'sistema') . ' - ID #' . $r['registro_id'],
                    'data'      => $r['created_at'],
                    'modulo'    => 'auditoria',
                    'link'      => site_url('auditoria'),
                ];
            }
        }

        // Ordena por data
        usort($eventos, function($a, $b) {
            return strtotime($b['data'] ?? '1970') <=> strtotime($a['data'] ?? '1970');
        });

        $eventos = array_slice($eventos, 0, $limite);

        // Adiciona "tempo relativo"
        $agora = time();
        foreach ($eventos as &$e) {
            $ts = strtotime($e['data'] ?? '1970');
            $diff = $agora - $ts;
            if ($diff < 0)              $e['relativo'] = 'agora';
            else if ($diff < 60)        $e['relativo'] = 'agora';
            else if ($diff < 3600)      $e['relativo'] = floor($diff / 60) . ' min atras';
            else if ($diff < 86400)     $e['relativo'] = floor($diff / 3600) . 'h atras';
            else if ($diff < 604800)    $e['relativo'] = floor($diff / 86400) . 'd atras';
            else                         $e['relativo'] = date('d/m/Y', $ts);
        }

        return $this->_json([
            'success' => true,
            'eventos' => $eventos,
            'total'   => count($eventos),
        ]);
    }

    private function _ativTipo($status)
    {
        switch (strtolower((string)$status)) {
            case 'concluida': case 'concluido': case 'finalizada':
                return 'success';
            case 'pausada': case 'cancelada':
                return 'warning';
            case 'pendente': case 'em_andamento':
                return 'info';
            default:
                return 'info';
        }
    }

    private function _json($data, $code = 200)
    {
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
