<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Evolution Controller - API v2
 * Endpoint para receber atualizações de IP público do servidor local Evolution API.
 *
 * URL: /index.php/api/v2/evolution/atualizar-ip
 *
 * Este endpoint é chamado pelo script monitor-ip-evolution.sh rodando no
 * servidor Linux local sempre que o IP público mudar.
 */
class EvolutionController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('notificacoes_config_model');
        $this->load->model('mapos_model');
    }

    /**
     * POST /api/v2/evolution/atualizar-ip
     * Recebe novo IP público e atualiza a URL da Evolution API.
     *
     * Headers obrigatórios:
     *   Content-Type: application/json
     *   X-IP-Token: {token_configurado_no_mapos}
     *
     * Body JSON:
     *   {
     *     "evolution_url": "http://201.55.123.45:8080",
     *     "ip": "201.55.123.45"
     *   }
     */
    public function atualizar_ip()
    {
        // Só aceita POST
        if ($this->input->method() !== 'post') {
            $this->json_response([
                'success' => false,
                'message' => 'Método não permitido. Use POST.'
            ], 405);
            return;
        }

        // Valida token de segurança
        $tokenHeader = $this->input->get_request_header('X-IP-Token', true);
        $tokenEsperado = $this->obterTokenSeguranca();

        if (empty($tokenEsperado)) {
            $this->json_response([
                'success' => false,
                'message' => 'Token de segurança não configurado no MapOS. Execute: INSERT INTO configuracoes (config, valor) VALUES (\'evolution_ip_token\', \'seu-token\')'
            ], 500);
            return;
        }

        if ($tokenHeader !== $tokenEsperado) {
            log_info('Tentativa de atualização de IP Evolution com token inválido. IP: ' . $this->input->ip_address());
            $this->json_response([
                'success' => false,
                'message' => 'Token inválido'
            ], 401);
            return;
        }

        // Obtém dados do body
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (empty($data) || !isset($data['evolution_url'])) {
            $this->json_response([
                'success' => false,
                'message' => 'Parâmetro "evolution_url" é obrigatório'
            ], 400);
            return;
        }

        $novaUrl = rtrim($data['evolution_url'], '/');
        $novaUrl = preg_replace('#/swagger(/index\.html)?$#', '', $novaUrl);
        $novaUrl = rtrim($novaUrl, '/');
        $novoIp = $data['ip'] ?? '';

        // Valida URL básica
        if (!filter_var($novaUrl, FILTER_VALIDATE_URL)) {
            $this->json_response([
                'success' => false,
                'message' => 'URL inválida fornecida'
            ], 400);
            return;
        }

        // Atualiza a URL da Evolution no banco
        $resultado = $this->notificacoes_config_model->salvar([
            'evolution_url' => $novaUrl,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($resultado['success']) {
            log_info('Evolution API URL atualizada automaticamente via monitor de IP. Nova URL: ' . $novaUrl . ' | IP: ' . $novoIp);
            $this->json_response([
                'success' => true,
                'message' => 'URL da Evolution API atualizada com sucesso',
                'nova_url' => $novaUrl,
                'ip' => $novoIp,
                'atualizado_em' => date('Y-m-d H:i:s')
            ], 200);
        } else {
            log_info('Falha ao atualizar Evolution API URL automaticamente. URL: ' . $novaUrl);
            $this->json_response([
                'success' => false,
                'message' => 'Erro ao atualizar URL no banco de dados'
            ], 500);
        }
    }

    /**
     * Obtém o token de segurança configurado no MapOS.
     * Busca na tabela configuracoes (config = 'evolution_ip_token').
     *
     * @return string|null
     */
    private function obterTokenSeguranca(): ?string
    {
        $this->db->where('config', 'evolution_ip_token');
        $query = $this->db->get('configuracoes');

        if ($query->num_rows() > 0) {
            return $query->row()->valor;
        }

        // Fallback: tenta ler de variável de ambiente
        $envToken = $_ENV['EVOLUTION_IP_TOKEN'] ?? null;
        if (!empty($envToken)) {
            return $envToken;
        }

        return null;
    }

    /**
     * Retorna resposta JSON padronizada.
     *
     * @param array $data
     * @param int   $httpCode
     */
    private function json_response(array $data, int $httpCode = 200): void
    {
        $this->output
            ->set_status_header($httpCode)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data));
    }
}
