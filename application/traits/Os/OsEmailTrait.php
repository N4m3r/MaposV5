<?php

defined('BASEPATH') or exit('No direct script access allowed');

namespace Application\Traits\Os;

trait OsEmailTrait
{
    private function enviarOsPorEmail($idOs, $remetentes, $assunto)
    {
        $dados = [];

        $this->load->model('mapos_model');
        $dados['result'] = $this->os_model->getById($idOs);
        if (! isset($dados['result']->email)) {
            return false;
        }

        $dados['produtos'] = $this->os_model->getProdutos($idOs);
        $dados['servicos'] = $this->os_model->getServicos($idOs);
        $dados['emitente'] = $this->mapos_model->getEmitente();
        $emitente = $dados['emitente'];
        if (! isset($emitente->email)) {
            return false;
        }

        $html = $this->load->view('os/emails/os', $dados, true);

        require_once APPPATH . 'libraries/Email/EmailQueue.php';
        $queue = new \Libraries\Email\EmailQueue();

        $remetentes = array_unique($remetentes);
        foreach ($remetentes as $remetente) {
            if ($remetente) {
                $queue->enqueue([
                    'to' => $remetente,
                    'subject' => $assunto,
                    'body_html' => $html,
                    'priority' => 3,
                ]);
            } else {
                log_info('Email não adicionado a Lista de envio de e-mails. Verifique se o remetente esta cadastrado. OS ID: ' . $idOs);
            }
        }

        return true;
    }

    /**
     * Enfileira email via sistema V5 (EmailQueue + TemplateEngine)
     */
    private function queueEmailV5($os, $template, $subject, $priority = 3, $attachments = [])
    {
        try {
            require_once APPPATH . 'libraries/Email/EmailQueue.php';
            require_once APPPATH . 'libraries/Email/TemplateEngine.php';

            $queue = new \Libraries\Email\EmailQueue();
            $templates = new \Libraries\Email\TemplateEngine();

            // Carrega configs de CC/BCC padrao
            $cc = [];
            $bcc = [];
            $this->db->where('config', 'email_cc_default');
            $rowCc = $this->db->get('configuracoes')->row();
            if ($rowCc && !empty($rowCc->valor)) {
                $cc = array_map('trim', explode(',', $rowCc->valor));
            }
            $this->db->where('config', 'email_bcc_default');
            $rowBcc = $this->db->get('configuracoes')->row();
            if ($rowBcc && !empty($rowBcc->valor)) {
                $bcc = array_map('trim', explode(',', $rowBcc->valor));
            }

            $templateData = [
                'cliente_nome' => $os->nomeCliente ?? $os->cliente_nome ?? '',
                'cliente_email' => $os->email ?? '',
                'os_id' => $os->idOs ?? '',
                'os_titulo' => $os->produto ?? 'OS #' . ($os->idOs ?? ''),
                'os_descricao' => $os->descricaoProduto ?? '',
                'os_status' => $os->status ?? '',
                'os_data_criacao' => isset($os->dataInicial) ? date('d/m/Y', strtotime($os->dataInicial)) : date('d/m/Y'),
                'os_data_vencimento' => isset($os->dataFinal) ? date('d/m/Y', strtotime($os->dataFinal)) : '',
                'os_valor_total' => number_format(($os->totalProdutos ?? 0) + ($os->totalServicos ?? 0), 2, ',', '.'),
                'os_link_visualizar' => base_url('os/visualizar/' . ($os->idOs ?? '')),
            ];

            $rendered = $templates->render($template, $templateData);

            $enqueueData = [
                'to' => $os->email ?? '',
                'to_name' => $templateData['cliente_nome'],
                'subject' => $subject,
                'body_html' => $rendered['html'],
                'body_text' => $rendered['text'] ?? strip_tags($rendered['html']),
                'template' => $template,
                'template_data' => $templateData,
                'priority' => $priority,
            ];

            if (!empty($cc)) {
                $enqueueData['cc'] = $cc;
            }
            if (!empty($bcc)) {
                $enqueueData['bcc'] = $bcc;
            }
            if (!empty($attachments)) {
                $enqueueData['attachments'] = (array) $attachments;
            }

            $queue->enqueue($enqueueData);
        } catch (\Exception $e) {
            log_message('error', '[queueEmailV5] Erro ao enfileirar email: ' . $e->getMessage());
        }
    }
}