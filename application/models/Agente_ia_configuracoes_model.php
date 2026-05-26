<?php
/**
 * Agente_ia_configuracoes_model
 * Model para CRUD de configuracoes do agente IA (chave-valor)
 * Valores com sensivel=1 sao criptografados com AES-256-CBC
 */

class Agente_ia_configuracoes_model extends CI_Model
{
    protected string $table = 'agente_ia_configuracoes';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lista todas as configuracoes ou por grupo
     * Descriptografa valores sensiveis automaticamente
     */
    public function listar(?string $grupo = null): array
    {
        if (!$this->db->table_exists($this->table)) {
            return [];
        }
        if ($grupo) {
            $this->db->where('grupo', $grupo);
        }
        $results = $this->db
            ->order_by('grupo', 'ASC')
            ->order_by('chave', 'ASC')
            ->get($this->table)
            ->result_array();

        foreach ($results as &$row) {
            if (isset($row['sensivel']) && $row['sensivel'] == 1 && !empty($row['valor'])) {
                $decrypted = $this->decryptValue($row['valor']);
                $row['valor'] = ($decrypted !== false) ? $decrypted : $row['valor'];
            }
        }

        return $results;
    }

    /**
     * Busca valor por chave
     * Descriptografa automaticamente se sensivel=1
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

        if (!$row) {
            return $padrao;
        }

        $valor = $row->valor ?? $padrao;

        if (isset($row->sensivel) && $row->sensivel == 1 && !empty($valor)) {
            $decrypted = $this->decryptValue($valor);
            if ($decrypted !== false) {
                return $decrypted;
            }
        }

        return $valor;
    }

    /**
     * Salva multiplas configuracoes (formulario admin)
     * Criptografa valores sensiveis antes de salvar
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
            $row = $this->db->where('id', (int) $id)->get($this->table)->row();
            if (!$row) {
                continue;
            }
            $dados = [];
            if (isset($val['valor'])) {
                if ($row->sensivel == 1) {
                    // Se valor vazio mas existe valor_atual (hidden field), manter o atual
                    if (empty($val['valor']) && isset($val['valor_atual']) && !empty($val['valor_atual'])) {
                        continue;
                    }
                    // Se valor vazio e nao ha valor_atual, tambem pular
                    if (empty($val['valor'])) {
                        continue;
                    }
                    $dados['valor'] = $this->encryptValue($val['valor']);
                } else {
                    $dados['valor'] = $val['valor'];
                }
            }
            if (isset($val['descricao'])) {
                $dados['descricao'] = $val['descricao'];
            }
            if (!empty($dados)) {
                $this->db->where('id', (int) $id);
                $this->db->update($this->table, $dados);
                $atualizados += $this->db->affected_rows();
            }
        }
        return $atualizados;
    }

    /**
     * Atualiza ou insere uma configuracao
     * Se sensivel=1, criptografa o valor
     */
    public function set(string $chave, string $valor, string $grupo = 'geral', string $descricao = ''): bool
    {
        if (!$this->db->table_exists($this->table)) {
            return false;
        }

        $existe = $this->db->where('chave', $chave)->count_all_results($this->table);

        // Verificar se a chave deve ser sensivel
        $sensivel = $this->isSensitiveKey($chave);

        if ($sensivel) {
            $valor = $this->encryptValue($valor);
        }

        if ($existe) {
            $this->db->where('chave', $chave);
            return $this->db->update($this->table, ['valor' => $valor]);
        }

        return $this->db->insert($this->table, [
            'chave'     => $chave,
            'valor'     => $valor,
            'grupo'     => $grupo,
            'descricao' => $descricao,
            'sensivel'  => $sensivel ? 1 : 0,
        ]);
    }

    /**
     * Verifica se uma chave deve ser tratada como sensivel
     */
    private function isSensitiveKey(string $chave): bool
    {
        $sensitiveKeys = ['apikey', 'api_key', 'secret', 'token', 'password', 'senha', 'key'];
        foreach ($sensitiveKeys as $pattern) {
            if (stripos($chave, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Criptografa valor usando AES-256-CBC
     * Formato armazenado: base64(IV_16bytes + ciphertext)
     */
    private function encryptValue(string $value): string
    {
        $key = $this->getEncryptionKey();
        if ($key === false) {
            log_message('error', 'Agente_ia_configuracoes: encryption_key nao configurada');
            return $value;
        }

        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($value, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        if ($ciphertext === false) {
            log_message('error', 'Agente_ia_configuracoes: Falha ao criptografar valor');
            return $value;
        }

        return base64_encode($iv . $ciphertext);
    }

    /**
     * Descriptografa valor usando AES-256-CBC
     */
    private function decryptValue(string $value)
    {
        $key = $this->getEncryptionKey();
        if ($key === false) {
            return false;
        }

        $data = base64_decode($value, true);
        if ($data === false || strlen($data) < 17) {
            return false;
        }

        $iv = substr($data, 0, 16);
        $ciphertext = substr($data, 16);

        $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            log_message('error', 'Agente_ia_configuracoes: Falha ao descriptografar - chave pode ter mudado');
        }

        return $decrypted;
    }

    /**
     * Retorna a chave de criptografia configurada no .env
     */
    private function getEncryptionKey()
    {
        $key = $this->config->item('encryption_key');
        if (empty($key)) {
            log_message('error', 'Agente_ia_configuracoes: encryption_key nao configurada');
            return false;
        }
        return $key;
    }
}