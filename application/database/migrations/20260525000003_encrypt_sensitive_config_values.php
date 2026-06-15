<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Encrypt existing sensitive config values in agente_ia_configuracoes
 * Uses APP_ENCRYPTION_KEY from .env for AES-256-CBC encryption
 */
class Migration_Encrypt_sensitive_config_values extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('agente_ia_configuracoes')) {
            return;
        }

        // Dotenv\Dotenv::createImmutable popula $_ENV e $_SERVER mas NAO getenv().
        // Tentar $_ENV primeiro, depois getenv() como fallback, e por fim o config do CI.
        $encryptionKey = $_ENV['APP_ENCRYPTION_KEY'] ?? getenv('APP_ENCRYPTION_KEY') ?: $this->config->item('encryption_key');
        if (empty($encryptionKey)) {
            log_message('error', 'Migration: APP_ENCRYPTION_KEY nao configurada. Pulando criptografia de config sensiveis.');
            return;
        }

        $rows = $this->db->where('sensivel', 1)->get('agente_ia_configuracoes')->result();
        $encrypted = 0;

        foreach ($rows as $row) {
            if (empty($row->valor)) {
                continue;
            }

            // Verificar se ja esta criptografado (base64 com tamanho >= 32 bytes apos decode = IV + ciphertext)
            $decoded = base64_decode($row->valor, true);
            if ($decoded !== false && strlen($decoded) >= 17) {
                $iv = substr($decoded, 0, 16);
                $ciphertext = substr($decoded, 16);
                $testDecrypt = @openssl_decrypt($ciphertext, 'AES-256-CBC', $encryptionKey, OPENSSL_RAW_DATA, $iv);
                if ($testDecrypt !== false) {
                    // Ja esta criptografado com esta chave
                    continue;
                }
            }

            // Criptografar valor
            $iv = openssl_random_pseudo_bytes(16);
            $ciphertext = openssl_encrypt($row->valor, 'AES-256-CBC', $encryptionKey, OPENSSL_RAW_DATA, $iv);

            if ($ciphertext !== false) {
                $encryptedValue = base64_encode($iv . $ciphertext);
                $this->db->where('id', $row->id)
                          ->update('agente_ia_configuracoes', ['valor' => $encryptedValue]);
                $encrypted++;
            }
        }

        log_message('info', "Migration: {$encrypted} valores sensiveis criptografados em agente_ia_configuracoes");
    }

    public function down()
    {
        // Nao e possivel reverter a criptografia sem a chave
        log_message('info', 'Migration down: Nao e possivel reverter a criptografia de config sensiveis automaticamente');
    }
}