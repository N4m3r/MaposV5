<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes_agendadas_model extends CI_Model
{
    protected $table = 'notificacoes_agendadas';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Agenda uma nova notificação
     */
    public function agendar($dados)
    {
        $padrao = [
            'data_hora_envio' => null,
            'data_hora_enviado' => null,
            'cliente_id' => null,
            'telefone' => null,
            'email' => null,
            'template_chave' => null,
            'variaveis' => null,
            'status' => 'agendada',
            'os_id' => null,
            'venda_id' => null,
            'origem' => 'sistema',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $dados = array_merge($padrao, $dados);

        // Valida data/hora
        if (empty($dados['data_hora_envio'])) {
            $dados['data_hora_envio'] = date('Y-m-d H:i:s');
        }

        // Converte variáveis para JSON se for array
        if (is_array($dados['variaveis'])) {
            $dados['variaveis'] = json_encode($dados['variaveis']);
        }

        $this->db->insert($this->table, $dados);
        return $this->db->insert_id();
    }

    /**
     * Obtém notificações agendadas para envio
     */
    public function getPendentes($limite = 50)
    {
        $this->db->where('status', 'agendada');
        $this->db->where('data_hora_envio <=', date('Y-m-d H:i:s'));
        $this->db->order_by('data_hora_envio', 'ASC');
        $this->db->limit($limite);

        return $this->db->get($this->table)->result();
    }

    /**
     * Marca como enviada
     */
    public function marcarEnviada($id)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, [
            'status' => 'enviada',
            'data_hora_enviado' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Marca como falha
     */
    public function marcarFalha($id, $erro = null)
    {
        $update = ['status' => 'falha'];

        if ($erro) {
            // Não há campo erro na tabela, mas podemos logar em outro lugar
            log_message('error', "Falha ao enviar notificação agendada ID {$id}: {$erro}");
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $update);
    }

    /**
     * Cancela uma notificação agendada
     */
    public function cancelar($id)
    {
        $this->db->where('id', $id);
        $this->db->where('status', 'agendada'); // Só cancela se ainda estiver agendada

        return $this->db->update($this->table, [
            'status' => 'cancelada',
        ]);
    }

    /**
     * Lista notificações agendadas
     */
    public function listar($filtros = [], $limite = 50, $offset = 0)
    {
        $this->aplicarFiltros($filtros);

        $this->db->order_by('data_hora_envio', 'DESC');
        $this->db->limit($limite, $offset);

        return $this->db->get($this->table)->result();
    }

    /**
     * Conta total de registros
     */
    public function contar($filtros = [])
    {
        $this->aplicarFiltros($filtros);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Aplica filtros
     */
    private function aplicarFiltros($filtros)
    {
        if (isset($filtros['status']) && $filtros['status']) {
            $this->db->where('status', $filtros['status']);
        }

        if (isset($filtros['cliente_id']) && $filtros['cliente_id']) {
            $this->db->where('cliente_id', $filtros['cliente_id']);
        }

        if (isset($filtros['data_inicio']) && $filtros['data_inicio']) {
            $this->db->where('data_hora_envio >=', $filtros['data_inicio'] . ' 00:00:00');
        }

        if (isset($filtros['data_fim']) && $filtros['data_fim']) {
            $this->db->where('data_hora_envio <=', $filtros['data_fim'] . ' 23:59:59');
        }
    }

    /**
     * Obtém por ID
     */
    public function getById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Atualiza uma notificação
     */
    public function atualizar($id, $dados)
    {
        // Só permite atualizar se estiver agendada
        $this->db->where('id', $id);
        $this->db->where('status', 'agendada');

        if (is_array($dados['variaveis'])) {
            $dados['variaveis'] = json_encode($dados['variaveis']);
        }

        return $this->db->update($this->table, $dados);
    }

    /**
     * Exclui notificações antigas já processadas
     */
    public function limparAntigos($dias = 30)
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));

        $this->db->where('status IN ("enviada", "cancelada")', null, false);
        $this->db->where('data_hora_envio <', $dataLimite);

        return $this->db->delete($this->table);
    }

    /**
     * Agenda notificação de aniversário
     */
    public function agendarAniversario($cliente, $dataAniversario)
    {
        // Agenda para o próximo aniversário
        $anoAtual = date('Y');
        $dataEnvio = $anoAtual . '-' . date('m-d', strtotime($dataAniversario)) . ' 09:00:00';

        // Se já passou este ano, agenda para o próximo
        if (strtotime($dataEnvio) < time()) {
            $anoAtual++;
            $dataEnvio = $anoAtual . '-' . date('m-d', strtotime($dataAniversario)) . ' 09:00:00';
        }

        return $this->agendar([
            'cliente_id' => $cliente->idClientes,
            'telefone' => $cliente->celular,
            'template_chave' => 'aniversario',
            'variaveis' => [
                'cliente_nome' => $cliente->nomeCliente,
                'cupom_desconto' => 'ANIV' . $anoAtual . $cliente->idClientes,
            ],
            'data_hora_envio' => $dataEnvio,
            'origem' => 'sistema',
        ]);
    }

    /**
     * Agenda lembrete de vencimento
     */
    public function agendarLembreteVencimento($cobranca, $diasAntes = 3)
    {
        $dataVencimento = new DateTime($cobranca->data_vencimento);
        $dataVencimento->sub(new DateInterval("P{$diasAntes}D"));

        // Não agenda se a data já passou
        if ($dataVencimento < new DateTime()) {
            return false;
        }

        return $this->agendar([
            'cliente_id' => $cobranca->clientes_id,
            'telefone' => $cobranca->celular,
            'template_chave' => 'cobranca_vencimento',
            'variaveis' => [
                'cliente_nome' => $cobranca->nomeCliente,
                'dias' => $diasAntes,
                'valor' => number_format($cobranca->valor, 2, ',', '.'),
                'data_vencimento' => date('d/m/Y', strtotime($cobranca->data_vencimento)),
                'link_pagamento' => $cobranca->link_pagamento ?? '',
            ],
            'data_hora_envio' => $dataVencimento->format('Y-m-d') . ' 10:00:00',
            'origem' => 'sistema',
        ]);
    }
}
