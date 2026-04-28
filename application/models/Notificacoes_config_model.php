<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes_config_model extends CI_Model
{
    protected $table = 'notificacoes_config';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtém as configurações de notificações (sempre retorna o registro 1)
     */
    public function getConfig()
    {
        $this->db->where('id', 1);
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 0) {
            // Cria configuração padrão se não existir
            $this->criarConfigPadrao();
            $this->db->where('id', 1);
            $query = $this->db->get($this->table);
        }

        return $query->row();
    }

    /**
     * Salva as configurações
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function salvar($dados)
    {
        $this->db->where('id', 1);
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 0) {
            $dados['id'] = 1;
            $result = $this->db->insert($this->table, $dados);
            $error = $this->db->error();
            if (!$result || $error['code'] != 0) {
                log_message('error', '[NotificacoesConfig] Erro ao inserir config: ' . ($error['message'] ?? 'Unknown'));
                return ['success' => false, 'error' => $error['message'] ?? 'Erro ao inserir configuração'];
            }
            return ['success' => true, 'error' => null];
        }

        $this->db->where('id', 1);
        $this->db->update($this->table, $dados);

        $error = $this->db->error();
        if ($error['code'] != 0) {
            log_message('error', '[NotificacoesConfig] Erro ao atualizar config: ' . $error['message']);
            return ['success' => false, 'error' => $error['message']];
        }

        return ['success' => true, 'error' => null];
    }

    /**
     * Atualiza estado da conexão Evolution
     */
    public function atualizarEstadoEvolution($estado)
    {
        $this->db->where('id', 1);
        return $this->db->update($this->table, [
            'evolution_estado' => $estado,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Verifica se as notificações WhatsApp estão ativas
     */
    public function isWhatsAppAtivo()
    {
        $config = $this->getConfig();
        return $config && $config->whatsapp_ativo == 1;
    }

    /**
     * Verifica se pode enviar notificação para determinado tipo
     */
    public function podeEnviar($tipo)
    {
        $config = $this->getConfig();

        if (!$config || $config->whatsapp_ativo != 1) {
            return false;
        }

        // Verifica horário de funcionamento
        if ($config->respeitar_horario == 1) {
            if (!$this->verificarHorarioEnvio($config)) {
                return false;
            }
        }

        // Verifica se o tipo específico está habilitado
        $campo = 'notificacao_' . $tipo;
        return isset($config->$campo) && $config->$campo == 1;
    }

    /**
     * Verifica se está dentro do horário permitido para envio
     */
    private function verificarHorarioEnvio($config)
    {
        $horaAtual = date('H:i:s');
        $diaSemana = date('w'); // 0 = Domingo, 6 = Sábado

        // Verifica fim de semana
        if (($diaSemana == 0 || $diaSemana == 6) && $config->enviar_fim_semana != 1) {
            return false;
        }

        // Verifica horário
        if ($horaAtual < $config->horario_envio_inicio || $horaAtual > $config->horario_envio_fim) {
            return false;
        }

        return true;
    }

    /**
     * Retorna o provedor configurado
     */
    public function getProvedor()
    {
        $config = $this->getConfig();
        return $config ? $config->whatsapp_provedor : 'desativado';
    }

    /**
     * Cria configuração padrão
     */
    private function criarConfigPadrao()
    {
        $dados = [
            'id' => 1,
            'whatsapp_provedor' => 'desativado',
            'whatsapp_ativo' => 0,
            'evolution_version' => 'v2',
            'evolution_instance' => 'mapos',
            'evolution_estado' => 'desconectado',
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

        return $this->db->insert($this->table, $dados);
    }
}
