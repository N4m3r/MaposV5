<?php
/**
 * Agente_ia_configuracoes_model
 * Model para CRUD de configuracoes do agente IA (chave-valor)
 */

class Agente_ia_configuracoes_model extends CI_Model
{
    protected string $table = 'agente_ia_configuracoes';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lista todas as configuracoes ou por categoria
     */
    public function listar(?string $categoria = null): array
    {
        if (!$this->db->table_exists($this->table)) {
            return [];
        }
        if ($categoria) {
            $this->db->where('categoria', $categoria);
        }
        return $this->db
            ->order_by('categoria', 'ASC')
            ->order_by('chave', 'ASC')
            ->get($this->table)
            ->result_array();
    }

    /**
     * Busca valor por chave
     */
    public function get(string $chave, string $padrao = ''): string
    {
        if (!$this->db->table_exists($this->table)) {
            return $padrao;
        }
        $row = $this->db
            ->where('chave', $chave)
            ->get($this->table)
            ->row();
        return $row ? ($row->valor ?? $padrao) : $padrao;
    }

    /**
     * Salva multiplas configuracoes (formulario admin)
     */
    public function salvarMultiplos(array $configs): int
    {
        if (!$this->db->table_exists($this->table)) {
            return 0;
        }
        $atualizados = 0;
        foreach ($configs as $id => $val) {
            if (!is_array($val)) {
                continue;
            }
            $dados = [];
            if (isset($val['valor'])) {
                $dados['valor'] = $val['valor'];
            }
            if (isset($val['descricao'])) {
                $dados['descricao'] = $val['descricao'];
            }
            if (!empty($dados)) {
                $this->db->where('id', (int)$id);
                $this->db->update($this->table, $dados);
                $atualizados += $this->db->affected_rows();
            }
        }
        return $atualizados;
    }

    /**
     * Atualiza ou insere uma configuracao
     */
    public function set(string $chave, string $valor, string $categoria = 'geral', string $descricao = ''): bool
    {
        if (!$this->db->table_exists($this->table)) {
            return false;
        }
        $existe = $this->db->where('chave', $chave)->count_all_results($this->table);
        if ($existe) {
            $this->db->where('chave', $chave);
            return $this->db->update($this->table, ['valor' => $valor]);
        }
        return $this->db->insert($this->table, [
            'chave'       => $chave,
            'valor'       => $valor,
            'categoria'   => $categoria,
            'descricao'   => $descricao,
        ]);
    }
}
