<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes_templates_model extends CI_Model
{
    private $tableName = 'notificacoes_templates';

    public function __construct()
    {
        parent::__construct();
    }

    public function listar($filtros = [])
    {
        if (!$this->db->table_exists($this->tableName)) {
            return [];
        }

        if (!empty($filtros['categoria'])) {
            $this->db->where('categoria', $filtros['categoria']);
        }
        if (isset($filtros['ativo'])) {
            $this->db->where('ativo', $filtros['ativo']);
        }
        if (!empty($filtros['canal'])) {
            $this->db->where('canal', $filtros['canal']);
        }

        $this->db->order_by('categoria, nome');
        return $this->db->get($this->tableName)->result();
    }

    public function getById($id)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return null;
        }

        $this->db->where('id', $id);
        return $this->db->get($this->tableName)->row();
    }

    public function getByChave($chave)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return null;
        }

        $this->db->where('chave', $chave);
        return $this->db->get($this->tableName)->row();
    }

    public function salvar($dados)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        if (!empty($dados['id'])) {
            $this->db->where('id', $dados['id']);
            unset($dados['id']);
            return $this->db->update($this->tableName, $dados);
        }

        return $this->db->insert($this->tableName, $dados);
    }

    public function toggleAtivo($id)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        $template = $this->getById($id);
        if (!$template) {
            return false;
        }

        $novoStatus = $template->ativo ? 0 : 1;
        $this->db->where('id', $id);
        return $this->db->update($this->tableName, ['ativo' => $novoStatus]);
    }

    public function excluir($id)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->delete($this->tableName);
    }

    public function processarTemplate($chave, $variaveis = [])
    {
        $template = $this->getByChave($chave);

        if (!$template) {
            return [
                'success' => false,
                'error' => 'Template nao encontrado: ' . $chave,
            ];
        }

        $mensagem = $template->mensagem;
        $assunto = $template->assunto ?? '';

        $globais = $this->getVariaveisGlobais();
        $todasVariaveis = array_merge($globais, $variaveis);

        foreach ($todasVariaveis as $key => $value) {
            $mensagem = str_replace('{' . $key . '}', $value, $mensagem);
            $assunto = str_replace('{' . $key . '}', $value, $assunto);
        }

        return [
            'success' => true,
            'chave' => $chave,
            'nome' => $template->nome,
            'mensagem' => $mensagem,
            'assunto' => $assunto,
            'canal' => $template->canal ?? 'whatsapp',
        ];
    }

    public function getVariaveis($chave)
    {
        $template = $this->getByChave($chave);
        if ($template && !empty($template->variaveis)) {
            $decoded = json_decode($template->variaveis, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    public function getVariaveisGlobais()
    {
        return [
            'cliente_nome' => 'Nome do Cliente',
            'emitente_nome' => 'Nome da Empresa',
            'data_atual' => 'Data Atual',
            'hora_atual' => 'Hora Atual',
            'link_sistema' => 'Link do Sistema',
        ];
    }

    public function getVariaveisPorCategoria($categoria)
    {
        $vars = [
            'os' => [
                'os_id' => 'Numero da OS',
                'os_status' => 'Status da OS',
                'os_valor' => 'Valor da OS',
                'tecnico_nome' => 'Nome do Tecnico',
            ],
            'venda' => [
                'venda_id' => 'Numero da Venda',
                'venda_valor' => 'Valor da Venda',
            ],
            'cobranca' => [
                'cobranca_id' => 'Numero da Cobranca',
                'cobranca_valor' => 'Valor da Cobranca',
                'cobranca_vencimento' => 'Data de Vencimento',
            ],
            'marketing' => [
                'promocao_nome' => 'Nome da Promocao',
                'promocao_desconto' => 'Desconto',
            ],
        ];

        return $vars[$categoria] ?? [];
    }

    public function listarPorCategoria($categoria)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return [];
        }

        $this->db->where('categoria', $categoria);
        $this->db->where('ativo', 1);
        return $this->db->get($this->tableName)->result();
    }

    public function getChavesPadrao()
    {
        return ['os_criada', 'os_atualizada', 'os_pronta', 'os_orcamento', 'os_aguardando_peca', 'venda_realizada', 'cobranca_gerada', 'cobranca_vencimento', 'aniversario'];
    }
}