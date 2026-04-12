<?php
/**
 * Validator
 * Validador aprimorado com suporte a CPF/CNPJ
 */

namespace Libraries\Validation;

class Validator
{
    private array $errors = [];
    private array $data = [];

    /**
     * Valida dados conforme regras
     */
    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            $value = $data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                if (!$this->checkRule($field, $value, $rule)) {
                    break;
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Verifica uma regra específica
     */
    private function checkRule(string $field, $value, string $rule): bool
    {
        [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field][] = "O campo {$field} é obrigatório.";
                    return false;
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "O campo {$field} deve ser um e-mail válido.";
                    return false;
                }
                break;

            case 'min':
                if (!empty($value) && strlen((string)$value) < (int)$param) {
                    $this->errors[$field][] = "O campo {$field} deve ter no mínimo {$param} caracteres.";
                    return false;
                }
                break;

            case 'max':
                if (!empty($value) && strlen((string)$value) > (int)$param) {
                    $this->errors[$field][] = "O campo {$field} deve ter no máximo {$param} caracteres.";
                    return false;
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = "O campo {$field} deve ser numérico.";
                    return false;
                }
                break;

            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->errors[$field][] = "O campo {$field} deve ser um número inteiro.";
                    return false;
                }
                break;

            case 'cpf':
                if (!empty($value) && !$this->validateCpf($value)) {
                    $this->errors[$field][] = "O CPF informado é inválido.";
                    return false;
                }
                break;

            case 'cnpj':
                if (!empty($value) && !$this->validateCnpj($value)) {
                    $this->errors[$field][] = "O CNPJ informado é inválido.";
                    return false;
                }
                break;

            case 'cpf_cnpj':
                if (!empty($value)) {
                    $clean = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($clean) === 11) {
                        if (!$this->validateCpf($clean)) {
                            $this->errors[$field][] = "O CPF informado é inválido.";
                            return false;
                        }
                    } elseif (strlen($clean) === 14) {
                        if (!$this->validateCnpj($clean)) {
                            $this->errors[$field][] = "O CNPJ informado é inválido.";
                            return false;
                        }
                    } else {
                        $this->errors[$field][] = "O documento informado é inválido.";
                        return false;
                    }
                }
                break;

            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->errors[$field][] = "O campo {$field} deve ser uma data válida.";
                    return false;
                }
                break;

            case 'date_format':
                if (!empty($value)) {
                    $d = \DateTime::createFromFormat($param, $value);
                    if (!$d || $d->format($param) !== $value) {
                        $this->errors[$field][] = "O campo {$field} deve estar no formato {$param}.";
                        return false;
                    }
                }
                break;

            case 'phone':
                if (!empty($value)) {
                    $clean = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($clean) < 10 || strlen($clean) > 11) {
                        $this->errors[$field][] = "O campo {$field} deve ser um telefone válido.";
                        return false;
                    }
                }
                break;

            case 'cep':
                if (!empty($value)) {
                    $clean = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($clean) !== 8) {
                        $this->errors[$field][] = "O CEP informado é inválido.";
                        return false;
                    }
                }
                break;

            case 'unique':
                // Verifica unicidade no banco
                if (!empty($value) && !$this->isUnique($field, $value, $param)) {
                    $this->errors[$field][] = "O valor informado para {$field} já existe.";
                    return false;
                }
                break;

            case 'in':
                $allowed = explode(',', $param);
                if (!empty($value) && !in_array($value, $allowed)) {
                    $this->errors[$field][] = "O campo {$field} deve ser um dos seguintes valores: " . implode(', ', $allowed);
                    return false;
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (!isset($this->data[$confirmField]) || $value !== $this->data[$confirmField]) {
                    $this->errors[$field][] = "A confirmação do campo {$field} não corresponde.";
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Valida CPF
     */
    private function validateCpf(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida CNPJ
     */
    private function validateCnpj(string $cnpj): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        // Primeiro dígito
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights1[$i];
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        if ($cnpj[12] != $digit1) {
            return false;
        }

        // Segundo dígito
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights2[$i];
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        return $cnpj[13] == $digit2;
    }

    /**
     * Verifica se valor é único
     */
    private function isUnique(string $field, $value, string $table): bool
    {
        $ci = \u0026get_instance();
        $ci->load->database();

        // Extrai nome da tabela e campo ID opcional
        $parts = explode(',', $table);
        $tableName = $parts[0];
        $idField = $parts[1] ?? 'id';
        $idValue = $parts[2] ?? null;

        $ci->db->where($field, $value);

        if ($idValue) {
            $ci->db->where($idField . ' !=', $idValue);
        }

        return $ci->db->count_all_results($tableName) === 0;
    }

    /**
     * Retorna erros de validação
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Retorna primeira mensagem de erro
     */
    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }
}
