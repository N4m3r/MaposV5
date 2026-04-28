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
        // Verifica se a tabela existe
        if (!$this->db->table_exists($this->table)) {
            log_message('error', '[NotificacoesConfig] Tabela ' . $this->table . ' NÃO existe!');
            return (object)[];
        }

        $this->db->where('id', 1);
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 0) {
            log_message('info', '[NotificacoesConfig] Registro id=1 não encontrado. Criando padrão...');
            $this->criarConfigPadrao();
            $this->db->where('id', 1);
            $query = $this->db->get($this->table);
        }

        $row = $query->row();
        log_message('debug', '[NotificacoesConfig] getConfig: id=' . ($row->id ?? 'N/D') . ', provider=' . ($row->whatsapp_provedor ?? 'N/D') . ', url=' . ($row->evolution_url ?? 'N/D') . ', instance=' . ($row->evolution_instance ?? 'N/D'));

        return $row;
    }

    /**
     * Salva as configurações
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function salvar($dados)
    {
        log_message('debug', '[NotificacoesConfig] salvar() chamado. Dados: ' . json_encode($dados));

        // Verifica se a tabela existe
        if (!$this->db->table_exists($this->table)) {
            log_message('error', '[NotificacoesConfig] Tabela ' . $this->table . ' não existe ao tentar salvar!');
            return ['success' => false, 'error' => 'Tabela de configurações não existe'];
        }

        // Filtra apenas colunas que existem na tabela (evita erro "Unknown column")
        try {
            $colunasExistentes = $this->db->list_fields($this->table);
            $dadosFiltrados = [];
            foreach ($dados as $chave => $valor) {
                if (in_array($chave, $colunasExistentes)) {
                    $dadosFiltrados[$chave] = $valor;
                } else {
                    log_message('debug', '[NotificacoesConfig] Campo ignorado (não existe na tabela): ' . $chave);
                }
            }
        } catch (Exception $e) {
            log_message('warning', '[NotificacoesConfig] Não foi possível listar colunas: ' . $e->getMessage());
            $dadosFiltrados = $dados;
        }

        $this->db->where('id', 1);
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 0) {
            log_message('debug', '[NotificacoesConfig] Registro não existe. Fazendo INSERT...');
            $dadosFiltrados['id'] = 1;
            $result = $this->db->insert($this->table, $dadosFiltrados);
            $error = $this->db->error();
            if (!$result || $error['code'] != 0) {
                log_message('error', '[NotificacoesConfig] Erro ao inserir config: ' . ($error['message'] ?? 'Unknown') . ' | SQL: ' . $this->db->last_query());
                return ['success' => false, 'error' => $error['message'] ?? 'Erro ao inserir configuração'];
            }
            log_message('debug', '[NotificacoesConfig] INSERT realizado com sucesso');
            return ['success' => true, 'error' => null];
        }

        log_message('debug', '[NotificacoesConfig] Registro existe. Fazendo UPDATE...');
        $this->db->where('id', 1);
        $this->db->update($this->table, $dadosFiltrados);

        $error = $this->db->error();
        if ($error['code'] != 0) {
            log_message('error', '[NotificacoesConfig] Erro ao atualizar config: ' . $error['message'] . ' | SQL: ' . $this->db->last_query());
            return ['success' => false, 'error' => $error['message']];
        }

        $affected = $this->db->affected_rows();
        log_message('debug', '[NotificacoesConfig] UPDATE realizado. Linhas afetadas: ' . $affected);

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
     * Atualiza o token da instância Evolution Go
     * Não depende da coluna existir no banco (usa cache de arquivo no serviço)
     */
    public function atualizarInstanceToken($token)
    {
        try {
            $this->db->where('id', 1);
            return $this->db->update($this->table, [
                'evolution_instance_token' => $token,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            log_message('error', '[NotificacoesConfig] Coluna evolution_instance_token pode não existir. Erro: ' . $e->getMessage());
            return false;
        }
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
            'evolution_instance' => 'Mapos',
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
