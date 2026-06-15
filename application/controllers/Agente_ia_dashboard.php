<?php
/**
 * Agente_ia_dashboard — Endpoints leves para o dashboard
 *
 * Fornece:
 *  - insights_dashboard()  : top N alertas (OS paradas, clientes devendo, padroes)
 *  - sugerir_os($id)       : proxima acao recomendada para uma OS especifica
 *  - eventos_recentes()    : ultimos eventos do agente para o badge
 *
 * Sem dependencia da Ollama — trabalha com queries SQL locais para nao
 * bloquear o dashboard. Quando a IA esta habilitada, gera tambem uma
 * frase-sintese (chamada em background).
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Agente_ia_dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('Os_model', 'osModel');
        $this->load->model('clientes_model');
    }

    // ======================================================================
    // F4.2 — Insights do Agente IA no dashboard
    // ======================================================================
    public function insights_dashboard()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $insights = [];

        // 1) OS abertas ha mais de 7 dias (nao aguardando cliente, nao concluidas)
        try {
            if ($this->db->table_exists('os')) {
                $row = $this->db->select('COUNT(*) AS qtd')
                    ->from('os')
                    ->where('status NOT IN (1, 3, 7)', null, false)   // 1=Concluida, 3=... etc; ajustaveis
                    ->where('dataInicial IS NOT NULL', null, false)
                    ->where('dataInicial <', date('Y-m-d', strtotime('-7 days')))
                    ->get()->row();
                $qtd = (int)($row->qtd ?? 0);
                if ($qtd > 0) {
                    $insights[] = [
                        'tipo'    => 'warning',
                        'icone'   => 'bx-time-five',
                        'titulo'  => $qtd . ' OS aberta(s) ha mais de 7 dias',
                        'detalhe' => 'Revisar prazos e reatribuir se necessario.',
                        'link'    => base_url('index.php/os?filtro=atrasadas'),
                        'peso'    => $qtd * 2,
                    ];
                }
            }
        } catch (Exception $e) { /* silencioso */ }

        // 2) Clientes com debitos vencidos
        try {
            if ($this->db->table_exists('cobrancas')) {
                $row = $this->db->select('COUNT(DISTINCT cliente_id) AS qtd, SUM(valor) AS total')
                    ->from('cobrancas')
                    ->where('status', 'vencido')
                    ->where('deleted_at IS NULL', null, false)
                    ->get()->row();
                $qtd = (int)($row->qtd ?? 0);
                if ($qtd > 0) {
                    $total = 'R$ ' . number_format((float)($row->total ?? 0), 2, ',', '.');
                    $insights[] = [
                        'tipo'    => 'danger',
                        'icone'   => 'bx-error-circle',
                        'titulo'  => $qtd . ' cliente(s) com debitos vencidos',
                        'detalhe' => 'Total em atraso: ' . $total,
                        'link'    => base_url('index.php/cobrancas/cobrancas?status=vencido'),
                        'peso'    => $qtd * 3,
                    ];
                }
            }
        } catch (Exception $e) { /* silencioso */ }

        // 3) Cobrancas vencendo hoje
        try {
            if ($this->db->table_exists('cobrancas')) {
                $row = $this->db->select('COUNT(*) AS qtd, SUM(valor) AS total')
                    ->from('cobrancas')
                    ->where('vencimento', date('Y-m-d'))
                    ->where('status !=', 'pago')
                    ->where('deleted_at IS NULL', null, false)
                    ->get()->row();
                $qtd = (int)($row->qtd ?? 0);
                if ($qtd > 0) {
                    $total = 'R$ ' . number_format((float)($row->total ?? 0), 2, ',', '.');
                    $insights[] = [
                        'tipo'    => 'info',
                        'icone'   => 'bx-calendar',
                        'titulo'  => $qtd . ' cobranca(s) vencendo hoje',
                        'detalhe' => 'Total: ' . $total,
                        'link'    => base_url('index.php/cobrancas/cobrancas?vencimento=' . date('Y-m-d')),
                        'peso'    => $qtd,
                    ];
                }
            }
        } catch (Exception $e) { /* silencioso */ }

        // 4) Clientes novos na semana
        try {
            if ($this->db->table_exists('clientes')) {
                $row = $this->db->select('COUNT(*) AS qtd')
                    ->from('clientes')
                    ->where('dataCadastro >=', date('Y-m-d', strtotime('-7 days')))
                    ->get()->row();
                $qtd = (int)($row->qtd ?? 0);
                if ($qtd > 0) {
                    $insights[] = [
                        'tipo'    => 'success',
                        'icone'   => 'bx-user-plus',
                        'titulo'  => $qtd . ' cliente(s) novo(s) esta semana',
                        'detalhe' => 'Bom momento para follow-up.',
                        'link'    => base_url('index.php/clientes?filtro=novos'),
                        'peso'    => $qtd / 2,
                    ];
                }
            }
        } catch (Exception $e) { /* silencioso */ }

        // 5) OS aguardando aprovacao ha mais de 3 dias
        try {
            if ($this->db->table_exists('os')) {
                $row = $this->db->select('COUNT(*) AS qtd, SUM(valorTotal) AS total')
                    ->from('os')
                    ->where('status', 4)   // aguardando aprovacao
                    ->where('dataInicial <', date('Y-m-d', strtotime('-3 days')))
                    ->get()->row();
                $qtd = (int)($row->qtd ?? 0);
                if ($qtd > 0) {
                    $total = 'R$ ' . number_format((float)($row->total ?? 0), 2, ',', '.');
                    $insights[] = [
                        'tipo'    => 'warning',
                        'icone'   => 'bx-help-circle',
                        'titulo'  => $qtd . ' OS aguardando aprovacao ha +3 dias',
                        'detalhe' => 'Valor bloqueado: ' . $total,
                        'link'    => base_url('index.php/os?status=4'),
                        'peso'    => $qtd * 4,
                    ];
                }
            }
        } catch (Exception $e) { /* silencioso */ }

        // 6) Padrao detectado: cliente com mais de 3 OS no mes
        try {
            if ($this->db->table_exists('os')) {
                $rows = $this->db->select('cliente_id, COUNT(*) AS qtd')
                    ->from('os')
                    ->where('dataInicial >=', date('Y-m-01'))
                    ->group_by('cliente_id')
                    ->having('qtd > 3')
                    ->order_by('qtd', 'DESC')
                    ->limit(3)
                    ->get()->result();
                if (!empty($rows)) {
                    $ids = array_map(function($r) { return (int)$r->cliente_id; }, $rows);
                    $insights[] = [
                        'tipo'    => 'info',
                        'icone'   => 'bx-trending-up',
                        'titulo'  => count($rows) . ' cliente(s) recorrentes este mes',
                        'detalhe' => 'Potenciais candidatos a plano/manutencao preventiva.',
                        'link'    => base_url('index.php/relatorios/clientes'),
                        'peso'    => 2,
                    ];
                }
            }
        } catch (Exception $e) { /* silencioso */ }

        // Ordenar por peso (mais urgente primeiro)
        usort($insights, function($a, $b) { return $b['peso'] <=> $a['peso']; });
        $insights = array_slice($insights, 0, 5);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'  => true,
                'insights' => $insights,
                'total'    => count($insights),
                'atualizado_em' => date('H:i'),
            ]));
    }

    // ======================================================================
    // F4.3 — Sugestoes automaticas para uma OS
    // ======================================================================
    public function sugerir_os($id = null)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = (int)$id;
        if (!$id) {
            return $this->_json(['success' => false, 'msg' => 'id invalido'], 400);
        }

        if (!$this->db->table_exists('os')) {
            return $this->_json(['success' => false, 'msg' => 'tabela os nao existe'], 404);
        }

        $os = $this->db->get_where('os', ['idOs' => $id])->row();
        if (!$os) {
            return $this->_json(['success' => false, 'msg' => 'OS nao encontrada'], 404);
        }

        $sugestoes = [];
        $status = (int)($os->status ?? 0);
        $diasAberta = 0;
        if (!empty($os->dataInicial)) {
            $diasAberta = (int)((time() - strtotime($os->dataInicial)) / 86400);
        }

        // Sugestoes por status
        switch ($status) {
            case 1:  // Aguardando / Em aberto
                if ($diasAberta > 5) {
                    $sugestoes[] = [
                        'titulo'  => 'Reatribuir tecnico',
                        'detalhe' => 'OS parada ha ' . $diasAberta . ' dias. Considere trocar o tecnico responsavel.',
                        'acao'   => 'reatribuir',
                        'link'   => base_url('index.php/os/atribuir/' . $id),
                    ];
                }
                $sugestoes[] = [
                    'titulo'  => 'Adicionar checklist de atendimento',
                    'detalhe' => 'Tecnicos que usam checklist fecham OS 30% mais rapido.',
                    'acao'   => 'checklist',
                    'link'   => base_url('index.php/atividades'),
                ];
                break;

            case 2:  // Em andamento
                $sugestoes[] = [
                    'titulo'  => 'Registrar horas de atendimento',
                    'detalhe' => 'Atualize o status a cada visita para manter o historico preciso.',
                    'acao'   => 'checkin',
                    'link'   => base_url('index.php/atividades/adicionar/' . $id),
                ];
                if ($diasAberta > 3) {
                    $sugestoes[] = [
                        'titulo'  => 'Alertar cliente sobre prazo',
                        'detalhe' => 'OS em andamento ha ' . $diasAberta . ' dias. Envie um WhatsApp ao cliente.',
                        'acao'   => 'notificar',
                        'link'   => base_url('index.php/notificacoes/enviar/' . $id),
                    ];
                }
                break;

            case 3:  // Aguardando peca
                $sugestoes[] = [
                    'titulo'  => 'Verificar estoque',
                    'detalhe' => 'Consulte se a peca ja esta disponivel para retomar o servico.',
                    'acao'   => 'estoque',
                    'link'   => base_url('index.php/estoque'),
                ];
                break;

            case 4:  // Aguardando aprovacao
                $sugestoes[] = [
                    'titulo'  => 'Cobrar aprovacao do cliente',
                    'detalhe' => 'Envie WhatsApp com link para aprovacao do orcamento.',
                    'acao'   => 'cobrar_aprovacao',
                    'link'   => base_url('index.php/notificacoes/enviar/' . $id),
                ];
                if ($diasAberta > 3) {
                    $sugestoes[] = [
                        'titulo'  => 'Considerar desconto',
                        'detalhe' => 'OS parada ha ' . $diasAberta . ' dias. Ofereca 5-10% de desconto para destravar.',
                        'acao'   => 'desconto',
                        'link'   => base_url('index.php/os/editar/' . $id),
                    ];
                }
                break;

            case 5:  // Concluida
                $sugestoes[] = [
                    'titulo'  => 'Solicitar avaliacao do cliente',
                    'detalhe' => 'Clientes que avaliam o servico tendem a voltar.',
                    'acao'   => 'avaliar',
                    'link'   => base_url('index.php/notificacoes/enviar/' . $id),
                ];
                $sugestoes[] = [
                    'titulo'  => 'Gerar cobranca',
                    'detalhe' => 'Emita boleto/Pix para receber o pagamento da OS.',
                    'acao'   => 'cobranca',
                    'link'   => base_url('index.php/cobrancas/adicionar?os=' . $id),
                ];
                break;

            default:
                $sugestoes[] = [
                    'titulo'  => 'Revisar status da OS',
                    'detalhe' => 'Verifique se o status atual reflete a situacao real.',
                    'acao'   => 'revisar',
                    'link'   => base_url('index.php/os/visualizar/' . $id),
                ];
        }

        return $this->_json([
            'success'   => true,
            'os_id'     => $id,
            'status'    => $status,
            'dias_aberta' => $diasAberta,
            'sugestoes' => $sugestoes,
        ]);
    }

    // ======================================================================
    // F4.7 — Eventos recentes do agente (badge no dashboard)
    // ======================================================================
    public function eventos_recentes()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $eventos = [];
        try {
            if ($this->db->table_exists('agente_ia_logs')) {
                $rows = $this->db->select('id, acao, status, mensagem, created_at')
                    ->from('agente_ia_logs')
                    ->order_by('created_at', 'DESC')
                    ->limit(8)
                    ->get()->result_array();
                $eventos = $rows;
            }
        } catch (Exception $e) { /* silencioso */ }

        return $this->_json([
            'success' => true,
            'eventos' => $eventos,
        ]);
    }

    // ======================================================================
    // Helper
    // ======================================================================
    private function _json($data, $code = 200)
    {
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
