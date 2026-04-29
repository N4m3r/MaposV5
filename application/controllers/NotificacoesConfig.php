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
            'evolution_url_interna' => 'http://127.0.0.1:8091',
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
                'evolution_url_interna' => $this->input->post('evolution_url_interna'),
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

        $service = new WhatsAppService();
        $diagnostico = $service->diagnostico();
        $config = $this->notificacoes_config_model->getConfig();

        echo json_encode([
            'tabela_existe' => $this->db->table_exists('notificacoes_config'),
            'config_resumo' => [
                'url' => $config->evolution_url ?? 'N/A',
                'apikey' => !empty($config->evolution_apikey) ? substr($config->evolution_apikey, 0, 8) . '...' : 'N/A',
                'instance' => $config->evolution_instance ?? 'N/A',
                'instance_token' => !empty($config->evolution_instance_token) ? substr($config->evolution_instance_token, 0, 8) . '...' : 'N/A',
                'ativo' => $config->whatsapp_ativo ?? 'N/A',
                'provedor' => $config->whatsapp_provedor ?? 'N/A',
            ],
            'evolution_diagnostico' => $diagnostico,
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

    /**
     * Testa endpoints da Evolution API Go com os tokens corretos
     */
    public function testar_curl()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        header('Content-Type: application/json');

        $config = $this->notificacoes_config_model->getConfig();
        $service = new WhatsAppService();
        $base = rtrim($service->getApiUrl() ?? $config->evolution_url ?? '', '/');
        $apikey = $config->evolution_apikey ?? '';
        $instance = $config->evolution_instance ?? 'Mapos';
        $instanceToken = $config->evolution_instance_token ?? '';

        $resultados = [];

        // 1. /instance/all com API key global (deve funcionar)
        $resultados[] = $this->_curlTest('1_instance_all_apikey', $base . '/instance/all', ['apikey: ' . $apikey]);

        // 2. /instance/all com token de instancia (deve falhar - 401)
        if ($instanceToken) {
            $resultados[] = $this->_curlTest('2_instance_all_token', $base . '/instance/all', ['apikey: ' . $instanceToken]);
        }

        // 3. /instance/status com API key global (deve falhar - 401)
        $resultados[] = $this->_curlTest('3_instance_status_apikey', $base . '/instance/status', ['apikey: ' . $apikey]);

        // 4. /instance/status com token de instancia (deve funcionar)
        if ($instanceToken) {
            $resultados[] = $this->_curlTest('4_instance_status_token', $base . '/instance/status', ['apikey: ' . $instanceToken]);
        }

        // 5. /send/text com API key global (deve falhar - 401)
        $resultados[] = $this->_curlTestPost('5_send_text_apikey', $base . '/send/text', ['apikey: ' . $apikey, 'Content-Type: application/json'], ['number' => '5511999999999', 'text' => 'Teste']);

        // 6. /send/text com token de instancia (deve funcionar - pode retornar 500 se numero nao existe)
        if ($instanceToken) {
            $resultados[] = $this->_curlTestPost('6_send_text_token', $base . '/send/text', ['apikey: ' . $instanceToken, 'Content-Type: application/json'], ['number' => '5511999999999', 'text' => 'Teste']);
        }

        // 7. /instance/qr com API key global (deve falhar - 401)
        $resultados[] = $this->_curlTest('7_qr_apikey', $base . '/instance/qr?instanceId=' . urlencode($instance), ['apikey: ' . $apikey]);

        // 8. /instance/qr com token de instancia (pode retornar 400 se ja logado)
        if ($instanceToken) {
            $resultados[] = $this->_curlTest('8_qr_token', $base . '/instance/qr?instanceId=' . urlencode($instance), ['apikey: ' . $instanceToken]);
        }

        // 9. /instance/disconnect com token de instancia (nao executa para nao desconectar)
        $resultados[] = [
            'nome' => '9_disconnect_token',
            'http_code' => 'N/A',
            'nota' => 'Teste omitido para nao desconectar a instancia. Endpoint: POST /instance/disconnect com token da instancia'
        ];

        // 10. Verificar DNS
        $dns = gethostbyname(parse_url($base, PHP_URL_HOST));

        echo json_encode([
            'url_base' => $base,
            'apikey_prefixo' => substr($apikey, 0, 8) . '...',
            'instance_token_prefixo' => $instanceToken ? substr($instanceToken, 0, 8) . '...' : 'N/A',
            'dns' => $dns,
            'resultados' => $resultados
        ]);
    }

    private function _curlTest($nome, $url, $headers, $follow = true, $gzip = true, $httpVersion = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_ENCODING, $gzip ? '' : null);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        if ($httpVersion) {
            curl_setopt($ch, CURLOPT_HTTP_VERSION, $httpVersion);
        }

        $defaultHeaders = [
            'Accept: application/json, text/plain, */*',
            'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
        ];
        if ($headers !== null) {
            $allHeaders = array_merge($defaultHeaders, $headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $defaultHeaders);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        $respHeaders = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        return [
            'nome' => $nome,
            'http_code' => $httpCode,
            'error' => $error,
            'final_url' => $finalUrl,
            'headers' => $respHeaders,
            'body' => $body,
        ];
    }

    private function _curlTestVerbose($url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $verboseLog = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verboseLog);

        if ($headers !== null) {
            $allHeaders = array_merge(['Accept: application/json'], $headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        rewind($verboseLog);
        $verbose = stream_get_contents($verboseLog);
        fclose($verboseLog);

        $respHeaders = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        return [
            'http_code' => $httpCode,
            'error' => $error,
            'headers' => $respHeaders,
            'body' => $body,
            'verbose' => $verbose,
        ];
    }

    private function _curlTestCustomUA($nome, $url, $headers, $ua)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_HEADER, true);
        if ($ua !== '') {
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        }

        if ($headers !== null) {
            $allHeaders = array_merge(['Accept: application/json'], $headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        return [
            'nome' => $nome,
            'http_code' => $httpCode,
            'error' => $error,
            'headers' => substr($response, 0, $headerSize),
            'body' => substr($response, $headerSize),
        ];
    }

    private function _curlTestPost($nome, $url, $headers, $payload = ['number' => '5511999999999', 'text' => 'Teste'])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $defaultHeaders = [
            'Accept: application/json, text/plain, */*',
            'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
        ];
        if ($headers !== null) {
            $allHeaders = array_merge($defaultHeaders, $headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $defaultHeaders);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        return [
            'nome' => $nome,
            'http_code' => $httpCode,
            'error' => $error,
            'final_url' => $finalUrl,
            'headers' => substr($response, 0, $headerSize),
            'body' => substr($response, $headerSize),
        ];
    }

    private function _curlTestPostGo($nome, $url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'number' => '5511999999999',
            'text' => 'Teste',
            'options' => ['delay' => 1200]
        ]));

        if ($headers !== null) {
            $allHeaders = array_merge(['Accept: application/json', 'Content-Type: application/json'], $headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        return [
            'nome' => $nome,
            'http_code' => $httpCode,
            'error' => $error,
            'headers' => substr($response, 0, $headerSize),
            'body' => substr($response, $headerSize),
        ];
    }

    private function _curlTestRaw($nome, $url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_HEADER, true);
        if ($headers !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        return [
            'nome' => $nome,
            'http_code' => $httpCode,
            'error' => $error,
            'headers' => substr($response, 0, $headerSize),
            'body' => substr($response, $headerSize),
        ];
    }

    private function _curlTestTls($nome, $url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        if ($headers !== null) {
            $allHeaders = array_merge(['Accept: application/json'], $headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        return [
            'nome' => $nome,
            'http_code' => $httpCode,
            'error' => $error,
            'headers' => substr($response, 0, $headerSize),
            'body' => substr($response, $headerSize),
        ];
    }
}
