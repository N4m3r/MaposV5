<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auditoria extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'cAuditoria')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar logs do sistema.');
            redirect(base_url());
        }
        $this->load->model('Audit_model');
        $this->load->model('Data_breach_model', 'breachModel');
        $this->data['menuConfiguracoes'] = 'Auditoria';
    }

    /**
     * Tab: legacy logs (default) or structured audit_log
     */
    public function index()
    {
        $tab = $this->input->get('tab') ?: 'logs';

        if ($tab === 'audit' && $this->db->table_exists('audit_log')) {
            return $this->auditIndex();
        }

        return $this->logsIndex();
    }

    /**
     * Legacy logs tab
     */
    private function logsIndex()
    {
        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('auditoria/index/?tab=logs');
        $this->data['configuration']['total_rows'] = $this->Audit_model->count('logs');
        $this->data['configuration']['page_query_string'] = true;
        $this->data['configuration']['query_string_segment'] = 'per_page';

        $this->pagination->initialize($this->data['configuration']);

        $offset = (int) $this->input->get('per_page');
        $this->data['results'] = $this->Audit_model->get('logs', '*', '', $this->data['configuration']['per_page'], $offset);
        $this->data['tab'] = 'logs';
        $this->data['view'] = 'auditoria/logs';

        return $this->layout();
    }

    /**
     * Structured audit_log tab with filters
     */
    private function auditIndex()
    {
        $this->load->library('pagination');

        $filters = [
            'table_name' => $this->input->get('table_name'),
            'action'     => $this->input->get('action'),
            'date_from'  => $this->input->get('date_from'),
            'date_to'    => $this->input->get('date_to'),
        ];

        $this->data['configuration']['base_url'] = site_url('auditoria/index/?tab=audit' . $this->buildQueryString($filters));
        $this->data['configuration']['total_rows'] = $this->Audit_model->countAudit($filters);
        $this->data['configuration']['page_query_string'] = true;
        $this->data['configuration']['query_string_segment'] = 'per_page';
        $this->data['configuration']['per_page'] = 50;

        $this->pagination->initialize($this->data['configuration']);

        $offset = (int) $this->input->get('per_page');
        $this->data['results'] = $this->Audit_model->getAudit($filters, $this->data['configuration']['per_page'], $offset);
        $this->data['filters'] = $filters;
        $this->data['tableNames'] = $this->Audit_model->getTableNames();
        $this->data['actions'] = $this->Audit_model->getActions();
        $this->data['tab'] = 'audit';
        $this->data['view'] = 'auditoria/audit_log';

        return $this->layout();
    }

    public function clean()
    {
        if ($this->Audit_model->clean()) {
            log_info('Efetuou limpeza de logs');
            $this->session->set_flashdata('success', 'Limpeza de logs realizada com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Nenhum log com mais de 30 dias encontrado.');
        }
        redirect(site_url('auditoria?tab=logs'));
    }

    public function clean_audit()
    {
        $days = (int) ($this->input->post('days') ?: 90);
        if ($this->Audit_model->cleanOld($days)) {
            log_audit('DELETE', 'audit_log', null, null, ['days_removed' => $days]);
            $this->session->set_flashdata('success', "Registros de auditoria com mais de {$days} dias removidos.");
        } else {
            $this->session->set_flashdata('error', 'Tabela audit_log não existe ou nenhum registro encontrado.');
        }
        redirect(site_url('auditoria?tab=audit'));
    }

    private function buildQueryString(array $filters): string
    {
        $parts = [];
        foreach ($filters as $k => $v) {
            if ($v !== null && $v !== '') {
                $parts[] = urlencode($k) . '=' . urlencode($v);
            }
        }
        return $parts ? '&' . implode('&', $parts) : '';
    }

    // ========================================================================
    // Data Breach Notifications (LGPD Art. 48)
    // ========================================================================

    /**
     * Lista notificacoes de vazamento
     */
    public function vazamentos()
    {
        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('auditoria/vazamentos/');
        $this->data['configuration']['total_rows'] = $this->breachModel->count();

        $this->pagination->initialize($this->data['configuration']);

        $offset = (int) ($this->uri->segment(3) ?: 0);
        $this->data['breaches'] = $this->breachModel->getAll($this->data['configuration']['per_page'], $offset);
        $this->data['view'] = 'auditoria/breach_notifications';

        return $this->layout();
    }

    /**
     * Cria nova notificacao de vazamento
     */
    public function vazamento_novo()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('titulo', 'Titulo', 'required|trim');
        $this->form_validation->set_rules('descricao', 'Descricao', 'required|trim');
        $this->form_validation->set_rules('data_ocorrencia', 'Data da Ocorrencia', 'required');
        $this->form_validation->set_rules('data_descoberta', 'Data da Descoberta', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('auditoria/vazamentos');
            return;
        }

        $data = [
            'titulo'             => $this->input->post('titulo'),
            'descricao'          => $this->input->post('descricao'),
            'tipo_dado_afetado'  => $this->input->post('tipo_dado_afetado'),
            'medidas_adotadas'   => $this->input->post('medidas_adotadas'),
            'data_ocorrencia'    => $this->input->post('data_ocorrencia'),
            'data_descoberta'    => $this->input->post('data_descoberta'),
            'notificado_anpd'    => $this->input->post('notificado_anpd') ? 1 : 0,
            'data_notificacao_anpd' => $this->input->post('notificado_anpd') ? date('Y-m-d H:i:s') : null,
            'status'             => $this->input->post('status') ?: 'investigando',
            'registrado_por'     => $this->session->userdata('id_admin'),
        ];

        $id = $this->breachModel->add($data);

        if ($id) {
            log_audit('BREACH_NOTIFY', 'data_breach_notifications', $id, null, $data);
            $this->session->set_flashdata('success', 'Notificacao de vazamento registrada com sucesso. ID: ' . $id);
        } else {
            $this->session->set_flashdata('error', 'Erro ao registrar notificacao. Tabela pode nao existir.');
        }

        redirect('auditoria/vazamentos');
    }

    /**
     * Notifica titulares afetados sobre o vazamento
     */
    public function vazamento_notificar_titulares($id)
    {
        $breach = $this->breachModel->getById((int) $id);
        if (!$breach) {
            $this->session->set_flashdata('error', 'Notificacao nao encontrada.');
            redirect('auditoria/vazamentos');
            return;
        }

        // Contar clientes com consentimento ativo (titulares de dados)
        $numAfetados = 0;
        if ($this->db->table_exists('clientes')) {
            $numAfetados = $this->db
                ->where('consentimento_lgpd', 1)
                ->where('deleted_at IS NULL')
                ->count_all_results('clientes');
        }

        $this->breachModel->markTitularesNotificados((int) $id, $numAfetados);

        log_audit('BREACH_NOTIFY', 'data_breach_notifications', $id, null, [
            'titulares_notificados' => $numAfetados,
        ]);

        $this->session->set_flashdata('success', "Titulares notificados. {$numAfetados} clientes com consentimento ativo foram contabilizados.");
        redirect('auditoria/vazamentos');
    }
}