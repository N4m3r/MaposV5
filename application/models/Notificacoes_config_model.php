<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes_config_model extends CI_Model
{
    private $tableName = 'notificacoes_config';

    public function __construct()
    {
        parent::__construct();
    }

    public function getConfig()
    {
        if (!$this->db->table_exists($this->tableName)) {
            return $this->criarConfigPadrao();
        }

        $config = $this->db->where('id', 1)->get($this->tableName)->row();

        if (!$config) {
            return $this->criarConfigPadrao();
        }

        return $config;
    }

    public function salvar($dados)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        $this->db->where('id', 1);
        return $this->db->update($this->tableName, $dados);
    }

    public function isWhatsAppAtivo()
    {
        $config = $this->getConfig();
        return isset($config->whatsapp_ativo) && $config->whatsapp_ativo == 1;
    }

    public function podeEnviar($tipo)
    {
        $config = $this->getConfig();

        if (!isset($config->whatsapp_ativo) || $config->whatsapp_ativo != 1) {
            return false;
        }

        $campoTipo = 'notificacao_' . $tipo;
        if (isset($config->$campoTipo) && $config->$campoTipo != 1) {
            return false;
        }

        return $this->verificarHorarioEnvio($config);
    }

    public function getProvedor()
    {
        $config = $this->getConfig();
        return $config->whatsapp_provedor ?? 'desativado';
    }

    private function verificarHorarioEnvio($config)
    {
        if (!isset($config->respeitar_horario) || $config->respeitar_horario != 1) {
            return true;
        }

        $horaAtual = date('H:i:s');
        $inicio = $config->horario_envio_inicio ?? '08:00:00';
        $fim = $config->horario_envio_fim ?? '18:00:00';

        if ($horaAtual < $inicio || $horaAtual > $fim) {
            return false;
        }

        if (!isset($config->enviar_fim_semana) || $config->enviar_fim_semana != 1) {
            $diaSemana = date('N');
            if ($diaSemana >= 6) {
                return false;
            }
        }

        return true;
    }

    private function criarConfigPadrao()
    {
        $padrao = (object) [
            'id' => 1,
            'whatsapp_provedor' => 'desativado',
            'whatsapp_ativo' => 0,
            'evolution_url' => '',
            'evolution_apikey' => '',
            'evolution_instance' => 'mapos',
            'meta_phone_number_id' => '',
            'meta_access_token' => '',
            'z_api_url' => '',
            'z_api_token' => '',
            'notificacao_os_criada' => 1,
            'notificacao_os_atualizada' => 1,
            'notificacao_os_pronta' => 1,
            'notificacao_os_orcamento' => 1,
            'notificacao_venda_realizada' => 0,
            'notificacao_cobranca_gerada' => 0,
            'notificacao_cobranca_vencimento' => 0,
            'notificacao_lembrete_aniversario' => 0,
            'horario_envio_inicio' => '08:00:00',
            'horario_envio_fim' => '18:00:00',
            'enviar_fim_semana' => 0,
            'respeitar_horario' => 1,
        ];

        if ($this->db->table_exists($this->tableName)) {
            $exists = $this->db->where('id', 1)->get($this->tableName)->row();
            if (!$exists) {
                $this->db->insert($this->tableName, (array) $padrao);
            }
        }

        return $padrao;
    }
}