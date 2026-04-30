<?php

use Libraries\Gateways\BasePaymentGateway;
use Libraries\Gateways\Contracts\PaymentGateway;

/**
 * Gateway de pagamento Banco Cora
 * Implementa geração de Boleto Bancário e QR Code PIX via API do Cora
 *
 * @link https://developers.cora.com.br/reference/invoice
 */
class Cora extends BasePaymentGateway
{
    /** @var string Access Token OAuth2 */
    private $accessToken;

    /** @var string URL base da API */
    private $apiBaseUrl;

    /** @var array Configurações do gateway */
    private $coraConfig;

    /** @var string Ambiente (sandbox/producao) */
    private $environment;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->config('payment_gateways');
        $this->ci->load->model('Os_model');
        $this->ci->load->model('vendas_model');
        $this->ci->load->model('cobrancas_model');
        $this->ci->load->model('mapos_model');
        $this->ci->load->model('clientes_model');
        $this->ci->load->model('emitente_model');

        $this->coraConfig = $this->ci->config->item('payment_gateways')['Cora'];
        $this->environment = $this->coraConfig['production'] === true ? 'producao' : 'sandbox';
        $this->apiBaseUrl = $this->environment === 'producao'
            ? 'https://api.cora.com.br'
            : 'https://api.stage.cora.com.br';

        // Obtém o access token OAuth2
        $this->authenticate();
    }

    /**
     * Autenticação OAuth2 com Client Credentials
     *
     * @throws Exception
     */
    private function authenticate()
    {
        $clientId = $this->coraConfig['credentials']['client_id'];
        $clientSecret = $this->coraConfig['credentials']['client_secret'];

        if (empty($clientId) || empty($clientSecret)) {
            throw new \Exception('Credenciais do Banco Cora não configuradas!');
        }

        $authString = base64_encode($clientId . ':' . $clientSecret);

        $ch = curl_init($this->apiBaseUrl . '/oauth/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'client_credentials',
            'scope' => 'invoice',
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $authString,
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            throw new \Exception('Falha na autenticação com o Banco Cora. HTTP: ' . $httpCode);
        }

        $data = json_decode($response);
        if (empty($data->access_token)) {
            throw new \Exception('Token de acesso não retornado pelo Banco Cora');
        }

        $this->accessToken = $data->access_token;
    }

    /**
     * Faz requisição HTTP para a API do Cora
     *
     * @param string $method Método HTTP (GET, POST, PATCH, DELETE)
     * @param string $endpoint Endpoint da API (ex: /v2/invoices/)
     * @param array|null $data Dados a serem enviados
     * @param array $headers Headers adicionais
     * @return object Resposta da API
     * @throws Exception
     */
    private function apiRequest($method, $endpoint, $data = null, $headers = [])
    {
        $url = $this->apiBaseUrl . $endpoint;

        $defaultHeaders = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
        ];

        // Adiciona Idempotency-Key para requisições POST
        if ($method === 'POST') {
            $idempotencyKey = isset($headers['Idempotency-Key'])
                ? $headers['Idempotency-Key']
                : $this->generateIdempotencyKey();
            $defaultHeaders[] = 'Idempotency-Key: ' . $idempotencyKey;
        }

        $allHeaders = array_merge($defaultHeaders, $headers);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('Erro cURL: ' . $error);
        }

        $result = json_decode($response);

        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMessage = isset($result->error_description)
                ? $result->error_description
                : (isset($result->message) ? $result->message : 'Erro na requisição à API do Cora');
            throw new \Exception('Erro API Cora (HTTP ' . $httpCode . '): ' . $errorMessage);
        }

        return $result;
    }

    /**
     * Gera UUID v4 para idempotência
     *
     * @return string UUID v4
     */
    private function generateIdempotencyKey()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0x4000, 0x4fff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Gera cobrança com Boleto Bancário
     *
     * @param int $id ID da OS ou Venda
     * @param string $tipo Tipo de entidade (os ou venda)
     * @return array Dados da cobrança gerada
     * @throws Exception
     */
    protected function gerarCobrancaBoleto($id, $tipo)
    {
        return $this->gerarInvoice($id, $tipo, ['BANK_SLIP']);
    }

    /**
     * Gera cobrança com Link de Pagamento (PIX)
     *
     * @param int $id ID da OS ou Venda
     * @param string $tipo Tipo de entidade (os ou venda)
     * @return array Dados da cobrança gerada
     * @throws Exception
     */
    protected function gerarCobrancaLink($id, $tipo)
    {
        // Para o Cora, link = PIX
        return $this->gerarInvoice($id, $tipo, ['PIX']);
    }

    /**
     * Gera cobrança com Boleto + PIX (ambos)
     *
     * @param int $id ID da OS ou Venda
     * @param string $tipo Tipo de entidade (os ou venda)
     * @return array Dados da cobrança gerada
     * @throws Exception
     */
    public function gerarCobrancaBoletoPix($id, $tipo)
    {
        return $this->gerarInvoice($id, $tipo, ['BANK_SLIP', 'PIX']);
    }

    /**
     * Gera Invoice na API do Cora
     *
     * @param int $id ID da OS ou Venda
     * @param string $tipo Tipo de entidade (os ou venda)
     * @param array $paymentForms Formas de pagamento (BANK_SLIP, PIX)
     * @return array Dados da cobrança gerada
     * @throws Exception
     */
    private function gerarInvoice($id, $tipo, $paymentForms = ['BANK_SLIP', 'PIX'])
    {
        $entity = $this->findEntity($id, $tipo);
        $cliente = $this->ci->clientes_model->getById($entity->clientes_id);

        if (!$cliente) {
            throw new \Exception('Cliente não encontrado!');
        }

        // Valida dados do cliente
        if ($err = $this->errosCadastro($entity)) {
            throw new \Exception($err);
        }

        // Obtém emitente para usar como sacador/cedente
        $emitente = $this->ci->mapos_model->getEmitente();
        if (!$emitente) {
            throw new \Exception('Emitente não configurado! Configure em Configurações > Emitente.');
        }

        // Calcula valores e prepara descrição detalhada
        $valores = $this->calcularValorTotalEDescricao($id, $tipo);
        $totalAmount = $valores['total'];
        $descricaoItens = $valores['descricao'];

        if ($totalAmount <= 0) {
            throw new \Exception('Valor da cobrança deve ser maior que zero!');
        }

        // Valor mínimo do Cora é R$ 5,00
        if ($totalAmount < 5.00) {
            throw new \Exception('Valor mínimo para boleto no Cora é R$ 5,00!');
        }

        // Prepara título e descrição
        $title = $tipo === PaymentGateway::PAYMENT_TYPE_OS ? "OS #$id" : "Venda #$id";
        $dueDate = (new DateTime())->add(new DateInterval($this->coraConfig['boleto_expiration'] ?? 'P3D'));

        // Monta descrição completa
        $descricaoCompleta = "Pagamento referente a $title\n";
        $descricaoCompleta .= "Emitido por: {$emitente->nome}\n";
        if (!empty($descricaoItens)) {
            $descricaoCompleta .= "\nItens:\n" . $descricaoItens;
        }

        // Prepara dados do cliente (sacado)
        $documentoLimpo = preg_replace('/[^0-9]/', '', $cliente->documento);
        $tipoDocumento = strlen($documentoLimpo) <= 11 ? 'CPF' : 'CNPJ';

        $requestData = [
            'code' => ($tipo === PaymentGateway::PAYMENT_TYPE_OS ? 'OS' : 'VND') . "_$id_" . time(),
            'customer' => [
                'name' => $this->limitarString($cliente->nomeCliente, 100),
                'email' => $cliente->email ?? '',
                'document' => [
                    'identity' => $documentoLimpo,
                    'type' => $tipoDocumento,
                ],
                'address' => [
                    'street' => $this->limitarString($cliente->rua ?? 'N/A', 100),
                    'number' => $this->limitarString($cliente->numero ?? '0', 10),
                    'complement' => $this->limitarString($cliente->complemento ?? '', 50),
                    'district' => $this->limitarString($cliente->bairro ?? 'N/A', 50),
                    'city' => $this->limitarString($cliente->cidade ?? 'N/A', 50),
                    'state' => $this->limitarString($cliente->estado ?? 'N/A', 2),
                    'zip_code' => preg_replace('/[^0-9]/', '', $cliente->cep),
                ],
            ],
            'services' => [
                [
                    'name' => $this->limitarString($title, 100),
                    'description' => $this->limitarString($descricaoCompleta, 500),
                    'amount' => intval($totalAmount * 100), // Valor em centavos
                ],
            ],
            'payment_terms' => [
                'due_date' => $dueDate->format('Y-m-d'),
                'fine' => [
                    'percentage' => floatval($this->coraConfig['fine_percentage'] ?? 0),
                ],
                'interest' => [
                    'percentage' => floatval($this->coraConfig['interest_percentage'] ?? 0),
                ],
                'discount' => [
                    'percentage' => 0,
                    'date' => null,
                ],
            ],
            'payment_forms' => $paymentForms,
            'notification' => [
                'type' => 'EMAIL',
                'receiver' => $cliente->email ?? '',
            ],
        ];

        // Envia requisição para API do Cora (v2)
        $result = $this->apiRequest('POST', '/v2/invoices/', $requestData);

        // Verifica se o invoice foi criado
        if (empty($result->id)) {
            throw new \Exception('Invoice não gerado pela API do Cora');
        }

        // Prepara dados do boleto
        $barcode = '';
        $linhaDigitavel = '';
        $pdfUrl = '';
        $pixCode = '';
        $qrCodeUrl = '';

        // Extrai dados do boleto se disponível
        if (in_array('BANK_SLIP', $paymentForms) && isset($result->bank_slip)) {
            $barcode = $result->bank_slip->barcode ?? '';
            $linhaDigitavel = $result->bank_slip->digitable_line ?? '';
            $pdfUrl = $result->bank_slip->pdf ?? '';
        }

        // Extrai dados do PIX se disponível
        if (in_array('PIX', $paymentForms) && isset($result->pix)) {
            $pixCode = $result->pix->emv ?? '';
            $qrCodeUrl = $result->payment_options->pix->qr_code_png ?? '';
        }

        // Define qual código usar como barcode principal
        $barcodePrincipal = $linhaDigitavel ?: $pixCode ?: $result->id;

        // Prepara dados para salvar no banco
        $data = [
            'barcode' => $barcodePrincipal,
            'linha_digitavel' => $linhaDigitavel,
            'link' => $qrCodeUrl ?: $pdfUrl,
            'payment_url' => $pdfUrl ?: $qrCodeUrl,
            'pdf' => $pdfUrl,
            'pix_code' => $pixCode,
            'expire_at' => $dueDate->format('Y-m-d'),
            'charge_id' => $result->id,
            'status' => $this->mapStatus($result->status),
            'total' => getMoneyAsCents($totalAmount),
            'payment' => in_array('BANK_SLIP', $paymentForms) ? 'BANK_SLIP' : 'PIX',
            'clientes_id' => $entity->idClientes,
            'payment_method' => in_array('BANK_SLIP', $paymentForms) ? 'boleto' : 'pix',
            'payment_gateway' => 'Cora',
            'message' => $descricaoCompleta,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($tipo === PaymentGateway::PAYMENT_TYPE_OS) {
            $data['os_id'] = $id;
        } else {
            $data['vendas_id'] = $id;
        }

        // Salva no banco
        if ($cobrancaId = $this->ci->cobrancas_model->add('cobrancas', $data, true)) {
            $data['idCobranca'] = $cobrancaId;
            log_info('Cobrança Cora criada com sucesso. ID: ' . $result->id);
        } else {
            throw new \Exception('Erro ao salvar cobrança no banco de dados!');
        }

        return $data;
    }

    /**
     * Calcula o valor total e gera descrição detalhada
     *
     * @param int $id ID da OS ou Venda
     * @param string $tipo Tipo de entidade
     * @return array ['total' => float, 'descricao' => string]
     */
    private function calcularValorTotalEDescricao($id, $tipo)
    {
        $produtos = $tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getProdutos($id)
            : $this->ci->vendas_model->getProdutos($id);

        $servicos = $tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getServicos($id)
            : [];

        $entity = $tipo === PaymentGateway::PAYMENT_TYPE_OS
            ? $this->ci->Os_model->getById($id)
            : $this->ci->vendas_model->getById($id);

        $descricao = "";
        $totalProdutos = 0;
        $totalServicos = 0;

        // Adiciona produtos na descrição
        if (!empty($produtos)) {
            $descricao .= "PRODUTOS:\n";
            foreach ($produtos as $item) {
                $subtotal = floatval($item->preco) * intval($item->quantidade);
                $totalProdutos += $subtotal;
                $descricao .= "- {$item->descricao} ({$item->quantidade}x R$ " . number_format($item->preco, 2, ',', '.') . ")\n";
            }
            $descricao .= "Subtotal Produtos: R$ " . number_format($totalProdutos, 2, ',', '.') . "\n\n";
        }

        // Adiciona serviços na descrição
        if (!empty($servicos)) {
            $descricao .= "SERVIÇOS:\n";
            foreach ($servicos as $item) {
                $subtotal = floatval($item->preco) * intval($item->quantidade);
                $totalServicos += $subtotal;
                $descricao .= "- {$item->nome} ({$item->quantidade}x R$ " . number_format($item->preco, 2, ',', '.') . ")\n";
            }
            $descricao .= "Subtotal Serviços: R$ " . number_format($totalServicos, 2, ',', '.') . "\n\n";
        }

        $subtotal = $totalProdutos + $totalServicos;

        // Aplica desconto
        $desconto = floatval($entity->desconto ?? 0);
        $tipoDesconto = $entity->tipo_desconto ?? 'real';
        $valorDesconto = 0;

        if ($desconto > 0) {
            if ($tipoDesconto === 'porcento') {
                $valorDesconto = $subtotal * ($desconto / 100);
                $descricao .= "Desconto: {$desconto}% (R$ " . number_format($valorDesconto, 2, ',', '.') . ")\n";
            } else {
                $valorDesconto = $desconto;
                $descricao .= "Desconto: R$ " . number_format($valorDesconto, 2, ',', '.') . "\n";
            }
        }

        $total = $subtotal - $valorDesconto;
        $descricao .= "TOTAL: R$ " . number_format($total, 2, ',', '.');

        return [
            'total' => $total,
            'descricao' => $descricao,
            'subtotal' => $subtotal,
            'desconto' => $valorDesconto,
        ];
    }

    /**
     * Limita string ao tamanho máximo
     *
     * @param string $string Texto a limitar
     * @param int $limite Tamanho máximo
     * @return string Texto limitado
     */
    private function limitarString($string, $limite)
    {
        if (empty($string)) {
            return '';
        }
        return substr($string, 0, $limite);
    }

    /**
     * Mapeia status do Cora para status interno
     *
     * @param string $status Status retornado pela API do Cora
     * @return string Status mapeado
     */
    private function mapStatus($status)
    {
        $statusMap = [
            'DRAFT' => 'PENDING',
            'OPEN' => 'PENDING',
            'PAID' => 'RECEIVED',
            'CANCELLED' => 'CANCELLED',
            'IN_PAYMENT' => 'CONFIRMED',
            'LATE' => 'OVERDUE',
            'EXPIRED' => 'OVERDUE',
        ];

        return $statusMap[$status] ?? $status;
    }

    /**
     * Cancela uma cobrança (boleto ou PIX)
     *
     * @param int $id ID da cobrança
     * @throws Exception
     */
    public function cancelar($id)
    {
        $cobranca = $this->ci->cobrancas_model->getById($id);
        if (!$cobranca) {
            throw new \Exception('Cobrança não existe!');
        }

        // Só cancela se estiver pendente
        if (!in_array($cobranca->status, ['PENDING', 'OPEN', 'DRAFT'])) {
            throw new \Exception('Não é possível cancelar cobrança com status: ' . $cobranca->status);
        }

        try {
            // Cancela na API do Cora via DELETE
            $this->apiRequest('DELETE', '/v2/invoices/' . $cobranca->charge_id);
            log_info('Cobrança cancelada na API Cora. Charge ID: ' . $cobranca->charge_id);
        } catch (\Exception $e) {
            // Se já estiver cancelado na API, ignora o erro
            if (strpos($e->getMessage(), '404') === false) {
                log_info('Erro ao cancelar cobrança na API Cora: ' . $e->getMessage());
            }
        }

        // Atualiza status no banco
        $this->ci->cobrancas_model->edit(
            'cobrancas',
            [
                'status' => 'CANCELLED',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            'idCobranca',
            $id
        );

        log_info('Cobrança #' . $id . ' cancelada no sistema');
    }

    /**
     * Atualiza status da cobrança consultando a API
     *
     * @param int $id ID da cobrança
     * @throws Exception
     */
    public function atualizarDados($id)
    {
        $cobranca = $this->ci->cobrancas_model->getById($id);
        if (!$cobranca) {
            throw new \Exception('Cobrança não existe!');
        }

        try {
            $result = $this->apiRequest('GET', '/v2/invoices/' . $cobranca->charge_id);

            $mappedStatus = $this->mapStatus($result->status);

            // Atualiza dados adicionais se disponíveis
            $updateData = [
                'status' => $mappedStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Se foi pago, salva data de pagamento
            if ($mappedStatus === 'RECEIVED' && isset($result->paid_at)) {
                $updateData['paid_at'] = date('Y-m-d H:i:s', strtotime($result->paid_at));
            }

            $this->ci->cobrancas_model->edit(
                'cobrancas',
                $updateData,
                'idCobranca',
                $id
            );

            $this->ci->session->set_flashdata('success', 'Cobrança atualizada com sucesso! Status: ' . $mappedStatus);
        } catch (\Exception $e) {
            $this->ci->session->set_flashdata('error', 'Erro ao atualizar cobrança: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Confirma pagamento manualmente
     *
     * @param int $id ID da cobrança
     * @throws Exception
     */
    public function confirmarPagamento($id)
    {
        $cobranca = $this->ci->cobrancas_model->getById($id);
        if (!$cobranca) {
            throw new \Exception('Cobrança não existe!');
        }

        // Atualiza status para RECEIVED
        $this->ci->cobrancas_model->edit(
            'cobrancas',
            [
                'status' => 'RECEIVED',
                'updated_at' => date('Y-m-d H:i:s'),
                'paid_at' => date('Y-m-d H:i:s'),
            ],
            'idCobranca',
            $id
        );

        log_info('Pagamento da cobrança #' . $id . ' confirmado manualmente');
        $this->ci->session->set_flashdata('success', 'Pagamento confirmado com sucesso!');
    }

    /**
     * Envia cobrança por email
     *
     * @param int $id ID da cobrança
     * @throws Exception
     */
    public function enviarPorEmail($id)
    {
        $cobranca = $this->ci->cobrancas_model->getById($id);
        if (!$cobranca) {
            throw new \Exception('Cobrança não existe!');
        }

        $emitente = $this->ci->mapos_model->getEmitente();
        if (!$emitente) {
            throw new \Exception('Emitente não configurado!');
        }

        $cliente = $this->ci->clientes_model->getById($cobranca->clientes_id);

        if (!$cliente || empty($cliente->email)) {
            throw new \Exception('Cliente não possui email cadastrado!');
        }

        // Determina qual template usar
        $template = 'cobrancas/emails/cobranca_cora';

        // Carrega a view de email
        $html = $this->ci->load->view(
            $template,
            [
                'cobranca' => $cobranca,
                'emitente' => $emitente,
                'cliente' => $cliente,
                'paymentGatewaysConfig' => $this->ci->config->item('payment_gateways'),
            ],
            true
        );

        // Monta assunto
        $assunto = 'Cobrança - ' . $emitente->nome;
        if ($cobranca->payment_method === 'boleto') {
            $assunto .= ' (Boleto)';
        } elseif ($cobranca->payment_method === 'pix') {
            $assunto .= ' (PIX)';
        }
        if ($cobranca->os_id) {
            $assunto .= ' - OS #' . $cobranca->os_id;
        } else {
            $assunto .= ' - Venda #' . $cobranca->vendas_id;
        }

        require_once APPPATH . 'libraries/Email/EmailQueue.php';
        $queue = new \Libraries\Email\EmailQueue();

        $queue->enqueue([
            'to' => $cliente->email,
            'subject' => $assunto,
            'body_html' => $html,
            'priority' => 3,
        ]);
        log_info('Email de cobrança #' . $id . ' adicionado à fila para: ' . $cliente->email);
        $this->ci->session->set_flashdata('success', 'Email adicionado à fila de envio!');
    }

    /**
     * Simula pagamento de boleto (apenas em sandbox)
     * Usado para testes
     *
     * @param int $id ID da cobrança
     * @return bool
     * @throws Exception
     */
    public function simularPagamentoSandbox($id)
    {
        if ($this->environment !== 'sandbox') {
            throw new \Exception('Simulação de pagamento só disponível em ambiente sandbox!');
        }

        $cobranca = $this->ci->cobrancas_model->getById($id);
        if (!$cobranca) {
            throw new \Exception('Cobrança não existe!');
        }

        try {
            // Simula pagamento via endpoint de teste
            $result = $this->apiRequest('POST', '/v2/invoices/pay', [
                'id' => $cobranca->charge_id,
            ]);

            log_info('Pagamento simulado no sandbox para cobrança #' . $id);

            // Atualiza status
            $this->atualizarDados($id);

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Erro ao simular pagamento: ' . $e->getMessage());
        }
    }
}
