<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes_templates_model extends CI_Model
{
    protected $table = 'notificacoes_templates';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lista todos os templates
     */
    public function listar($filtros = [])
    {
        if (isset($filtros['categoria'])) {
            $this->db->where('categoria', $filtros['categoria']);
        }

        if (isset($filtros['ativo'])) {
            $this->db->where('ativo', $filtros['ativo']);
        }

        if (isset($filtros['canal'])) {
            $this->db->where('canal', $filtros['canal']);
        }

        $this->db->order_by('categoria', 'ASC');
        $this->db->order_by('nome', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Obtém um template por ID
     */
    public function getById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Obtém um template por chave
     */
    public function getByChave($chave)
    {
        $this->db->where('chave', $chave);
        return $this->db->get($this->table)->row();
    }

    /**
     * Salva um template
     */
    public function salvar($dados)
    {
        if (isset($dados['id']) && !empty($dados['id'])) {
            $this->db->where('id', $dados['id']);
            unset($dados['id']);
            return $this->db->update($this->table, $dados);
        }

        return $this->db->insert($this->table, $dados);
    }

    /**
     * Atualiza status ativo/inativo
     */
    public function toggleAtivo($id)
    {
        $template = $this->getById($id);
        if (!$template) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, [
            'ativo' => $template->ativo ? 0 : 1
        ]);
    }

    /**
     * Processa variáveis no template
     */
    public function processarTemplate($chave, $variaveis = [])
    {
        $template = $this->getByChave($chave);

        if (!$template) {
            return false;
        }

        $mensagem = $template->mensagem;
        $assunto = $template->assunto;

        // Substitui variáveis no formato {nome_variavel}
        foreach ($variaveis as $chave => $valor) {
            $mensagem = str_replace('{' . $chave . '}', $valor, $mensagem);
            if ($assunto) {
                $assunto = str_replace('{' . $chave . '}', $valor, $assunto);
            }
        }

        return [
            'chave' => $template->chave,
            'nome' => $template->nome,
            'canal' => $template->canal,
            'assunto' => $assunto,
            'mensagem' => $mensagem,
            'e_marketing' => $template->e_marketing,
        ];
    }

    /**
     * Retorna as variáveis disponíveis para um template
     */
    public function getVariaveis($chave)
    {
        $template = $this->getByChave($chave);

        if (!$template || empty($template->variaveis)) {
            return [];
        }

        return json_decode($template->variaveis, true);
    }

    /**
     * Retorna variáveis globais disponíveis em todos os templates
     */
    public function getVariaveisGlobais()
    {
        return [
            'cliente_nome' => 'Nome do cliente',
            'cliente_telefone' => 'Telefone do cliente',
            'cliente_email' => 'E-mail do cliente',
            'cliente_documento' => 'CPF/CNPJ do cliente',
            'data_atual' => 'Data atual (DD/MM/AAAA)',
            'hora_atual' => 'Hora atual (HH:MM)',
            'emitente_nome' => 'Nome da empresa emitente',
            'emitente_telefone' => 'Telefone da empresa',
            'emitente_endereco' => 'Endereço da empresa',
            'emitente_horario' => 'Horário de funcionamento',
            'link_sistema' => 'URL do sistema',
        ];
    }

    /**
     * Retorna variáveis específicas por categoria
     */
    public function getVariaveisPorCategoria($categoria)
    {
        $variaveis = [
            'os' => [
                'os_id' => 'Número da OS',
                'equipamento' => 'Descrição do equipamento',
                'defeito' => 'Defeito informado',
                'data_previsao' => 'Data prevista para conclusão',
                'status_atual' => 'Status atual da OS',
                'status_anterior' => 'Status anterior da OS',
                'valor_total' => 'Valor total da OS',
                'valor_orcamento' => 'Valor do orçamento',
                'tempo_estimado' => 'Tempo estimado para execução',
                'pecas_aguardando' => 'Lista de peças aguardando',
                'previsao_peca' => 'Previsão de chegada das peças',
                'link_consulta' => 'Link para consulta pública da OS',
                'link_aprovar' => 'Link para aprovar orçamento',
                'link_recusar' => 'Link para recusar orçamento',
            ],
            'venda' => [
                'venda_id' => 'Número da venda',
                'valor_total' => 'Valor total da venda',
                'data_venda' => 'Data da venda',
                'produtos_lista' => 'Lista de produtos vendidos',
                'vendedor_nome' => 'Nome do vendedor',
            ],
            'cobranca' => [
                'referente' => 'Referência da cobrança (OS/Venda)',
                'valor' => 'Valor da cobrança',
                'data_vencimento' => 'Data de vencimento',
                'dias' => 'Dias até o vencimento',
                'link_pagamento' => 'Link para pagamento',
                'codigo_barras' => 'Código de barras do boleto',
                'linha_digitavel' => 'Linha digitável do boleto',
            ],
            'marketing' => [
                'cupom_desconto' => 'Cupom de desconto oferecido',
                'promocao_nome' => 'Nome da promoção',
                'promocao_descricao' => 'Descrição da promoção',
                'validade_oferta' => 'Data de validade da oferta',
            ],
        ];

        return $variaveis[$categoria] ?? [];
    }

    /**
     * Lista templates por categoria
     */
    public function listarPorCategoria($categoria)
    {
        $this->db->where('categoria', $categoria);
        $this->db->where('ativo', 1);
        $this->db->order_by('nome', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Exclui um template (apenas se não for padrão)
     */
    public function excluir($id)
    {
        $template = $this->getById($id);

        // Templates padrão não podem ser excluídos, apenas desativados
        if ($template && in_array($template->chave, $this->getChavesPadrao())) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Retorna chaves dos templates padrão
     */
    private function getChavesPadrao()
    {
        return [
            'os_criada',
            'os_atualizada',
            'os_pronta',
            'os_orcamento',
            'os_aguardando_peca',
            'venda_realizada',
            'cobranca_gerada',
            'cobranca_vencimento',
            'aniversario'
        ];
    }
}
