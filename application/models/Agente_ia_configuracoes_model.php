<?php
/**
 * Agente_ia_configuracoes_model
<<<<<<< HEAD
 * Model para CRUD de configuracoes do agente IA (chave-valor)
=======
 * Gerencia configuracoes do agente IA (chave/valor).
>>>>>>> 10b417cfe75fa9265859bfcda71135ba088587d4
 */

class Agente_ia_configuracoes_model extends CI_Model
{
<<<<<<< HEAD
    protected string $table = 'agente_ia_configuracoes';
=======
    protected string $tabela = 'agente_ia_configuracoes';
>>>>>>> 10b417cfe75fa9265859bfcda71135ba088587d4

    public function __construct()
    {
        parent::__construct();
    }

<<<<<<< HEAD
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
=======
    // Listar todas as configuracoes (agrupadas por grupo)
    public function listar(): array
    {
        $query = $this->db->order_by('grupo, chave')->get($this->tabela);
        $resultados = $query->result_array();

        $agrupados = [];
        foreach ($resultados as $row) {
            $grupo = $row['grupo'];
            if (!isset($agrupados[$grupo])) {
                $agrupados[$grupo] = [];
            }
            $agrupados[$grupo][] = $row;
        }
        return $agrupados;
    }

    // Listar todas em array chave => valor
    public function listarComoArray(): array
    {
        $query = $this->db->get($this->tabela);
        $result = [];
        foreach ($query->result_array() as $row) {
            $result[$row['chave']] = $row['valor'];
        }
        return $result;
    }

    // Pegar um valor especifico
    public function get(string $chave, string $padrao = ''): string
    {
        $row = $this->db->where('chave', $chave)->get($this->tabela)->row_array();
        return $row['valor'] ?? $padrao;
    }

    // Salvar/atualizar um valor
    public function set(string $chave, string $valor): bool
    {
        $existe = $this->db->where('chave', $chave)->count_all_results($this->tabela);
        if ($existe) {
            return $this->db->where('chave', $chave)->update($this->tabela, ['valor' => $valor]);
        }
        return $this->db->insert($this->tabela, [
            'chave' => $chave,
            'valor' => $valor,
            'grupo' => 'geral'
        ]);
    }

    // Salvar multiplos de uma vez
    public function salvarMultiplos(array $dados): int
    {
        $atualizados = 0;
        foreach ($dados as $chave => $valor) {
            if ($this->set($chave, $valor)) {
                $atualizados++;
>>>>>>> 10b417cfe75fa9265859bfcda71135ba088587d4
            }
        }
        return $atualizados;
    }

<<<<<<< HEAD
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
=======
    // Verificar se a tabela existe
    public function tabelaExiste(): bool
    {
        return $this->db->table_exists($this->tabela);
>>>>>>> 10b417cfe75fa9265859bfcda71135ba088587d4
    }
}
