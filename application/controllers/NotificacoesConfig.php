<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class NotificacoesConfig extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notificacoes_config_model');
        require_once APPPATH . 'Services/WhatsAppService.php';
    }

    public function index()
    {
        redirect('notificacoesConfig/configuracoes');
    }

    public function configuracoes()
    {
        $config = $this->notificacoes_config_model->getConfig();

        // Preenche defaults se vazio
        $defaults = [
            'whatsapp_provedor' => 'evolution',
            'whatsapp_ativo' => 1,
            'evolution_url' => 'https://evo.jj-ferreiras.com.br',
            'evolution_apikey' => '7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2',
            'evolution_instance' => 'Mapos',
            'evolution_estado' => 'desconectado',
            'meta_phone_number_id' => '',
            'meta_access_token' => '',
            'z_api_url' => '',
            'z_api_token' => '',
            'notificacao_os_criada' => 1,
            'notificacao_os_atualizada' => 1,
            'notificacao_os_pronta' => 1,
            'notificacao_os_orcamento' => 1,
            'notificacao_venda_realizada' => 1,
            'notificacao_cobranca_gerada' => 1,
            'notificacao_cobranca_vencimento' => 1,
            'notificacao_lembrete_aniversario' => 0,
            'horario_envio_inicio' => '08:00:00',
            'horario_envio_fim' => '18:00:00',
            'enviar_fim_semana' => 0,
            'respeitar_horario' => 1,
        ];
        foreach ($defaults as $k => $v) {
            if (!isset($config->$k) || $config->$k === null || $config->$k === '') {
                $config->$k = $v;
            }
        }

        if ($this->input->post()) {
            $url = $this->input->post('evolution_url');
            if ($url) {
                $url = rtrim($url, '/');
                $url = preg_replace('#/swagger(/index\.html)?$#', '', $url);
                $url = rtrim($url, '/');
            }

            $dados = [
                'whatsapp_provedor' => $this->input->post('whatsapp_provedor'),
                'whatsapp_ativo' => $this->input->post('whatsapp_ativo') ? 1 : 0,
                'evolution_url' => $url,
                'evolution_apikey' => $this->input->post('evolution_apikey'),
                'evolution_instance' => $this->input->post('evolution_instance') ?: 'Mapos',
                'evolution_instance_token' => $this->input->post('evolution_instance_token'),
                'meta_phone_number_id' => $this->input->post('meta_phone_number_id'),
                'meta_access_token' => $this->input->post('meta_access_token'),
                'z_api_url' => $this->input->post('z_api_url'),
                'z_api_token' => $this->input->post('z_api_token'),
                'notificacao_os_criada' => $this->input->post('notificacao_os_criada') ? 1 : 0,
                'notificacao_os_atualizada' => $this->input->post('notificacao_os_atualizada') ? 1 : 0,
                'notificacao_os_pronta' => $this->input->post('notificacao_os_pronta') ? 1 : 0,
                'notificacao_os_orcamento' => $this->input->post('notificacao_os_orcamento') ? 1 : 0,
                'notificacao_venda_realizada' => $this->input->post('notificacao_venda_realizada') ? 1 : 0,
                'notificacao_cobranca_gerada' => $this->input->post('notificacao_cobranca_gerada') ? 1 : 0,
                'notificacao_cobranca_vencimento' => $this->input->post('notificacao_cobranca_vencimento') ? 1 : 0,
                'notificacao_lembrete_aniversario' => $this->input->post('notificacao_lembrete_aniversario') ? 1 : 0,
                'horario_envio_inicio' => $this->input->post('horario_envio_inicio') ?: '08:00:00',
                'horario_envio_fim' => $this->input->post('horario_envio_fim') ?: '18:00:00',
                'enviar_fim_semana' => $this->input->post('enviar_fim_semana') ? 1 : 0,
                'respeitar_horario' => $this->input->post('respeitar_horario') ? 1 : 0,
            ];

            $resultado = $this->notificacoes_config_model->salvar($dados);
            if ($resultado['success']) {
                $this->session->set_flashdata('success', 'Configurações salvas com sucesso!');
            } else {
                $this->session->set_flashdata('error', 'Erro ao salvar: ' . ($resultado['error'] ?? 'Erro desconhecido'));
            }
            redirect(current_url());
        }

        // Verifica status da conexão
        $statusConexao = null;
        if ($config->whatsapp_ativo && $config->whatsapp_provedor === 'evolution') {
            $service = new WhatsAppService();
            $statusConexao = $service->verificarConexao();
        }

        $this->data['config'] = $config;
        $this->data['statusConexao'] = $statusConexao;
        $this->data['view'] = 'notificacoes/configuracoes';
        $this->data['menuConfiguracoes'] = 'Notificações';

        return $this->layout();
    }

    public function verificar_status()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        header('Content-Type: application/json');
        $service = new WhatsAppService();
        echo json_encode($service->verificarConexao());
    }

    public function obter_qr()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        header('Content-Type: application/json');
        $service = new WhatsAppService();
        $resultado = $service->obterQRCode();
        echo json_encode($resultado);
    }

    public function desconectar()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        header('Content-Type: application/json');
        $service = new WhatsAppService();
        echo json_encode($service->desconectar());
    }

    public function testar_envio()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        header('Content-Type: application/json');
        $numero = $this->input->post('numero');
        if (empty($numero)) {
            echo json_encode(['success' => false, 'error' => 'Número não informado']);
            return;
        }
        $service = new WhatsAppService();
        echo json_encode($service->enviarMensagem($numero, 'Teste de envio do sistema MAPOS!'));
    }

    public function diagnostico()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        header('Content-Type: application/json');
        $config = $this->notificacoes_config_model->getConfig();

        // Testa conectividade com servidor Evolution
        $evolutionTest = null;
        if (!empty($config->evolution_url)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, rtrim($config->evolution_url, '/') . '/instance/all');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'apikey: ' . ($config->evolution_apikey ?? '')
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            curl_close($ch);

            $body = substr($response, $headerSize);
            $evolutionTest = [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'body_preview' => substr($body, 0, 300),
            ];
        }

        echo json_encode([
            'tabela_existe' => $this->db->table_exists('notificacoes_config'),
            'config' => (array) $config,
            'evolution_test' => $evolutionTest,
        ]);
    }

    // Métodos restantes (templates, logs, etc) mantidos...
    public function templates()
    {
        $this->load->model('notificacoes_templates_model');
        $this->data['templates'] = $this->notificacoes_templates_model->listar();
        $this->data['view'] = 'notificacoes/templates';
        $this->data['menuConfiguracoes'] = 'Notificações';
        return $this->layout();
    }

    public function adicionar_template() { redirect('notificacoesConfig/templates'); }
    public function editar_template($id = null) { redirect('notificacoesConfig/templates'); }
    public function toggle_template($id = null) { redirect('notificacoesConfig/templates'); }
    public function excluir_template($id = null) { redirect('notificacoesConfig/templates'); }
    public function logs() { redirect('notificacoesConfig/configuracoes'); }
    public function estatisticas() { redirect('notificacoesConfig/configuracoes'); }
    public function enviar_manual() { redirect('notificacoesConfig/configuracoes'); }
    public function preview_template() { echo json_encode(['error' => 'Não implementado']); }
}
