<?php
/**
 * Modo Campo (F5.7) — UI simplificada para tecnicos em obra
 *
 * Caracteristicas:
 *  - Tela cheia, sem sidebar
 *  - Fontes grandes, botoes touch-friendly
 *  - Foco em acoes: check-in/out, registrar atividade, fotos, proxima OS
 *  - Funciona offline-first: estado salvo no localStorage e sincronizado
 *    via /campo/sync
 *
 * Acesso:
 *   /campo              - dashboard campo
 *   /campo/checkin      - registrar chegada
 *   /campo/foto         - upload de foto
 *   /campo/sync         - endpoint AJAX para sincronizar fila offline
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Campo extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function index()
    {
        $this->data['title'] = 'Modo Campo';
        $this->data['view']  = 'tema/campo/index';
        $this->data['layout_clean'] = true;   // sem sidebar
        $this->data['hideChrome']   = true;   // sem topbar padrao
        return $this->layout();
    }

    public function api_agenda()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $userId = (int)$this->session->userdata('id');
        $data = [
            'sucesso'   => true,
            'hoje'      => date('Y-m-d'),
            'minhas_os' => [],
            'checkin'   => null,
        ];
        try {
            if ($this->db->table_exists('os')) {
                $data['minhas_os'] = $this->db->select('idOs, status, valorTotal, dataInicial, dataFinal, descricaoProduto')
                    ->from('os')
                    ->where('usuario_id', $userId)
                    ->where('status NOT IN', '(3, 5)', false)
                    ->order_by('dataInicial', 'ASC')
                    ->limit(20)
                    ->get()->result_array();
            }
            if ($this->db->table_exists('atividades')) {
                $row = $this->db->select('id, data_inicio, status')
                    ->from('atividades')
                    ->where('usuario_id', $userId)
                    ->where('DATE(data_inicio)', date('Y-m-d'))
                    ->order_by('id', 'DESC')
                    ->limit(1)
                    ->get()->row();
                if ($row) {
                    $data['checkin'] = [
                        'id'          => $row->id,
                        'data_inicio' => $row->data_inicio,
                        'status'      => $row->status,
                    ];
                }
            }
        } catch (Exception $e) { /* silencioso */ }
        return $this->_json($data);
    }

    /**
     * Sincroniza fila offline de eventos capturados no navegador
     * (fotos, checkins, observacoes) que ficaram pendentes.
     */
    public function sync()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $body = json_decode($this->input->raw_input_stream, true);
        $eventos = isset($body['eventos']) && is_array($body['eventos']) ? $body['eventos'] : [];
        $userId = (int)$this->session->userdata('id');
        $result = ['success' => true, 'processados' => 0, 'falhas' => []];

        foreach ($eventos as $e) {
            try {
                $tipo = isset($e['tipo']) ? $e['tipo'] : '';
                $payload = isset($e['payload']) ? $e['payload'] : [];
                $clientTs = isset($e['ts']) ? (int)$e['ts'] : time();

                switch ($tipo) {
                    case 'checkin':
                    case 'checkout':
                        if ($this->db->table_exists('atividades')) {
                            $this->db->insert('atividades', [
                                'usuario_id'  => $userId,
                                'titulo'      => ($tipo === 'checkin' ? 'Check-in' : 'Check-out') . ' (Modo Campo)',
                                'data_inicio' => date('Y-m-d H:i:s', $clientTs),
                                'status'      => $tipo === 'checkin' ? 'em_andamento' : 'concluida',
                                'os_id'       => isset($payload['os_id']) ? (int)$payload['os_id'] : null,
                                'observacoes' => isset($payload['obs']) ? (string)$payload['obs'] : null,
                            ]);
                        }
                        $result['processados']++;
                        break;

                    case 'observacao':
                        if ($this->db->table_exists('atividades') && !empty($payload['os_id'])) {
                            $this->db->insert('atividades', [
                                'usuario_id'  => $userId,
                                'titulo'      => 'Observacao (Modo Campo)',
                                'data_inicio' => date('Y-m-d H:i:s', $clientTs),
                                'status'      => 'concluida',
                                'os_id'       => (int)$payload['os_id'],
                                'observacoes' => isset($payload['texto']) ? (string)$payload['texto'] : '',
                            ]);
                            $result['processados']++;
                        }
                        break;

                    case 'foto':
                        // Fotos vao para /uploads via upload normal; aqui so registra metadata
                        if ($this->db->table_exists('os_fotos') && !empty($payload['os_id'])) {
                            $this->db->insert('os_fotos', [
                                'os_id'      => (int)$payload['os_id'],
                                'arquivo'    => isset($payload['filename']) ? (string)$payload['filename'] : '',
                                'created_at' => date('Y-m-d H:i:s', $clientTs),
                            ]);
                            $result['processados']++;
                        }
                        break;

                    default:
                        $result['falhas'][] = 'tipo desconhecido: ' . $tipo;
                }
            } catch (Exception $ex) {
                $result['falhas'][] = $ex->getMessage();
            }
        }
        return $this->_json($result);
    }

    private function _json($data, $code = 200)
    {
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
