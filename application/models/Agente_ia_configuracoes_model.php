<?php
/**
 * Agente_ia_configuracoes_model
 * Gerencia configuracoes do agente IA (chave/valor).
 */

class Agente_ia_configuracoes_model extends CI_Model
{
    protected string $tabela = 'agente_ia_configuracoes';

    public function __construct()
    {
        parent::__construct();
    }

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
            }
        }
        return $atualizados;
    }

    // Verificar se a tabela existe
    public function tabelaExiste(): bool
    {
        return $this->db->table_exists($this->tabela);
    }
}
