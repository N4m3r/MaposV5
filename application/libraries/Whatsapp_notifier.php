<?php

/**
 * WhatsApp Notifier Library
 * Envia notificacoes WhatsApp para clientes quando OS muda de status.
 * Usa a Evolution API para enviar mensagens automaticas.
 */
class Whatsapp_notifier
{
    private $evoUrl;
    private $evoApiKey;
    private $evoInstance;
    private $agentUrl;
    private $agentApiKey;

    public function __construct()
    {
        $ci = &get_instance();
        $ci->load->database();

        // Ler configuracoes do .env ou configuracoes do sistema
        $this->evoUrl      = getenv('EVOLUTION_URL') ?: ($_ENV['EVOLUTION_URL'] ?? '');
        $this->evoApiKey    = getenv('EVOLUTION_API_KEY') ?: ($_ENV['EVOLUTION_API_KEY'] ?? '');
        $this->evoInstance  = getenv('EVOLUTION_INSTANCE') ?: ($_ENV['EVOLUTION_INSTANCE'] ?? 'Mapos');
        $this->agentUrl     = getenv('AGENT_URL') ?: ($_ENV['AGENT_URL'] ?? '');
        $this->agentApiKey  = getenv('AGENT_API_KEY') ?: ($_ENV['AGENT_API_KEY'] ?? '');
    }

    /**
     * Notifica o cliente sobre mudanca de status da OS via WhatsApp.
     *
     * @param int $osId ID da OS
     * @param string $novoStatus Novo status
     * @param string $statusAnterior Status anterior
     * @return bool Sucesso do envio
     */
    public function notificarStatusOs(int $osId, string $novoStatus, string $statusAnterior = ''): bool
    {
        $ci = &get_instance();
        $ci->load->model('Os_model');

        // Buscar dados da OS
        $os = $ci->Os_model->getById($osId);
        if (!$os) {
            return false;
        }

        // Buscar dados do cliente
        $ci->db->where('idClientes', $os->clientes_id);
        $cliente = $ci->db->get('clientes')->row();
        if (!$cliente) {
            return false;
        }

        // Buscar telefone do cliente
        $celular = $this->normalizarNumero($cliente->celular ?? '');
        if (empty($celular)) {
            return false;
        }

        // Verificar se cliente aceita notificacoes (tabela whatsapp_integracao)
        $ci->db->where('numero_telefone', $celular);
        $ci->db->where('situacao', 1);
        $integracao = $ci->db->get('whatsapp_integracao')->row();
        if (!$integracao) {
            // Cliente nao integrado — nao notificar
            return false;
        }

        // Montar mensagem
        $msg = $this->montarMensagemStatus($osId, $cliente->nomeCliente, $novoStatus, $statusAnterior, $os);

        // Enviar via Evolution API
        return $this->enviarMensagem($celular, $msg);
    }

    /**
     * Envia pesquisa de satisfacao apos OS finalizada.
     *
     * @param int $osId ID da OS finalizada
     * @return bool
     */
    public function enviarPesquisaSatisfacao(int $osId): bool
    {
        $ci = &get_instance();
        $ci->load->model('Os_model');

        $os = $ci->Os_model->getById($osId);
        if (!$os) {
            return false;
        }

        $ci->db->where('idClientes', $os->clientes_id);
        $cliente = $ci->db->get('clientes')->row();
        if (!$cliente) {
            return false;
        }

        $celular = $this->normalizarNumero($cliente->celular ?? '');
        if (empty($celular)) {
            return false;
        }

        // Verificar integracao
        $ci->db->where('numero_telefone', $celular);
        $ci->db->where('situacao', 1);
        $integracao = $ci->db->get('whatsapp_integracao')->row();
        if (!$integracao) {
            return false;
        }

        $msg = $this->pesquisarSatisfacao($osId, $cliente->nomeCliente);
        return $this->enviarMensagem($celular, $msg);
    }

    /**
     * Monta mensagem de notificacao de status.
     */
    private function montarMensagemStatus(int $osId, string $nomeCliente, string $novoStatus, string $statusAnterior, object $os): string
    {
        $statusIcons = [
            'Aberto'            => '🆕',
            'Orçamento'         => '📋',
            'Aprovado'          => '✅',
            'Em Andamento'      => '🔧',
            'Aguardando Peças'  => '⏳',
            'Faturado'          => '💰',
            'Finalizado'        => '🎉',
            'Cancelado'         => '❌',
        ];

        $icon = $statusIcons[$novoStatus] ?? '📌';

        $msg = "{$icon} *Atualizacao da OS #{$osId}*\n\n";
        $msg .= "Ola *{$nomeCliente}*!\n\n";
        $msg .= "A Ordem de Servico *#{$osId}* teve o status atualizado:\n\n";
        if ($statusAnterior) {
            $msg .= "De: _{$statusAnterior}_\n";
            $msg .= "Para: *{$novoStatus}*\n\n";
        } else {
            $msg .= "Status atual: *{$novoStatus}*\n\n";
        }

        if (isset($os->descricaoProduto)) {
            $msg .= "Equipamento: {$os->descricaoProduto}\n";
        }

        // Mensagens especiais por status
        if (strtolower($novoStatus) === 'finalizado') {
            $msg .= "\n✨ Sua OS foi finalizada! O equipamento esta pronto para retirada.\n";
            $msg .= "Responda com uma nota de *1 a 5* para avaliar nosso atendimento!";
        } elseif (strtolower($novoStatus) === 'cancelado') {
            $msg .= "\nSe precisar de algo, estamos a disposicao!";
        } elseif (strtolower($novoStatus) === 'aprovado') {
            $msg .= "\nO orcamento foi aprovado! Iniciaremos o atendimento em breve.";
        } elseif (strtolower($novoStatus) === 'aguardando pecas') {
            $msg .= "\nEstamos aguardando a chegada das pecas necessarias.";
        }

        $msg .= "\n_Powered by JJ Ferreiras_";

        return $msg;
    }

    /**
     * Monta mensagem de pesquisa de satisfacao.
     */
    private function pesquisarSatisfacao(int $osId, string $nomeCliente): string
    {
        $msg = "Pesquisa de Satisfacao - OS #{$osId}\n\n";
        $msg .= "Ola *{$nomeCliente}*!\n\n";
        $msg .= "Sua OS *#{$osId}* foi finalizada. Como foi nossa atendencia?\n\n";
        $msg .= "Digite uma nota de *1 a 5*:\n";
        $msg .= "5 = Excelente\n";
        $msg .= "4 = Bom\n";
        $msg .= "3 = Regular\n";
        $msg .= "2 = Ruim\n";
        $msg .= "1 = Pessimo\n\n";
        $msg .= "Ou deixe um comentario com sua opniao!";

        return $msg;
    }

    /**
     * Envia mensagem WhatsApp via Evolution API.
     */
    private function enviarMensagem(string $numero, string $mensagem): bool
    {
        if (empty($this->evoUrl) || empty($this->evoApiKey)) {
            log_message('error', '[Whatsapp_notifier] Evolution API nao configurada');
            return false;
        }

        $url = rtrim($this->evoUrl, '/') . '/message/sendText/' . $this->evoInstance;

        $payload = json_encode([
            'number' => $numero,
            'text'   => $mensagem,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'apikey: ' . $this->evoApiKey,
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            log_message('info', "[Whatsapp_notifier] Notificacao enviada para {$numero} (HTTP {$httpCode})");
            return true;
        }

        log_message('error', "[Whatsapp_notifier] Falha ao enviar: HTTP {$httpCode} - {$error}");
        return false;
    }

    /**
     * Normaliza numero de telefone para formato internacional.
     */
    private function normalizarNumero(string $numero): string
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        // Se nao tem codigo do pais, adicionar +55 (Brasil)
        if (strlen($numero) === 11) {
            $numero = '55' . $numero;
        }
        return $numero;
    }
}