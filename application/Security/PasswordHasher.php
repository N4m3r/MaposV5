<?php
/**
 * Password Hasher
 * Hash de senhas usando Argon2id
 */

namespace Libraries\Security;

class PasswordHasher
{
    private array $options;

    public function __construct()
    {
        $this->options = [
            'memory_cost' => 65536,  // 64MB
            'time_cost' => 4,        // 4 iterações
            'threads' => 3           // 3 threads paralelas
        ];
    }

    /**
     * Cria hash da senha
     */
    public function hash(string $password): string
    {
        // Verifica se Argon2id está disponível
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($password, PASSWORD_ARGON2ID, $this->options);
        }

        // Fallback para BCRYPT se Argon2id não estiver disponível
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verifica senha
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Verifica se o hash precisa ser atualizado
     */
    public function needsRehash(string $hash): bool
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_needs_rehash($hash, PASSWORD_ARGON2ID, $this->options);
        }

        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Rehash da senha se necessário
     */
    public function rehashIfNeeded(string $password, string $hash): ?string
    {
        if ($this->needsRehash($hash)) {
            return $this->hash($password);
        }

        return null;
    }

    /**
     * Valida força da senha
     */
    public function validateStrength(string $password): array
    {
        $strength = 0;
        $errors = [];

        // Mínimo 8 caracteres
        if (strlen($password) < 8) {
            $errors[] = 'A senha deve ter no mínimo 8 caracteres';
        } else {
            $strength++;
        }

        // Pelo menos uma letra maiúscula
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'A senha deve conter pelo menos uma letra maiúscula';
        } else {
            $strength++;
        }

        // Pelo menos uma letra minúscula
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'A senha deve conter pelo menos uma letra minúscula';
        } else {
            $strength++;
        }

        // Pelo menos um número
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'A senha deve conter pelo menos um número';
        } else {
            $strength++;
        }

        // Pelo menos um caractere especial
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'A senha deve conter pelo menos um caractere especial';
        } else {
            $strength++;
        }

        return [
            'valid' => empty($errors),
            'strength' => $strength,
            'max_strength' => 5,
            'errors' => $errors,
            'score' => $this->calculateScore($password)
        ];
    }

    /**
     * Calcula score da senha (0-100)
     */
    private function calculateScore(string $password): int
    {
        $score = 0;
        $length = strlen($password);

        // Pontos por comprimento
        $score += min($length * 4, 40);

        // Pontos por variedade de caracteres
        if (preg_match('/[a-z]/', $password)) $score += 10;
        if (preg_match('/[A-Z]/', $password)) $score += 15;
        if (preg_match('/[0-9]/', $password)) $score += 15;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 20;

        return min($score, 100);
    }

    /**
     * Gera senha segura aleatória
     */
    public function generateSecurePassword(int $length = 16): string
    {
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password = [
            $upper[random_int(0, strlen($upper) - 1)],
            $lower[random_int(0, strlen($lower) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)],
            $special[random_int(0, strlen($special) - 1)]
        ];

        $all = $upper . $lower . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password[] = $all[random_int(0, strlen($all) - 1)];
        }

        shuffle($password);
        return implode('', $password);
    }
}
