<?php

use Piggly\Pix\Parser;

if (! function_exists('e')) {
    /**
     * Escapa HTML para prevenir XSS.
     * Uso: <?php echo e($variavel) ?> em vez de <?php echo $variavel ?>
     */
    function e($string, $doubleEncode = false)
    {
        if ($string === null || $string === '') {
            return '';
        }
        return htmlspecialchars((string) $string, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }
}

if (! function_exists('convertUrlToUploadsPath')) {
    function convertUrlToUploadsPath($url)
    {
        if (! $url) {
            return;
        }

        return FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . basename($url);
    }
}

if (! function_exists('limitarTexto')) {
    function limitarTexto($texto, $limite)
    {
        $contador = strlen($texto);

        if ($contador >= $limite) {
            $texto = substr($texto, 0, strrpos(substr($texto, 0, $limite), ' ')) . '...';

            return $texto;
        } else {
            return $texto;
        }
    }
}

if (! function_exists('getMoneyAsCents')) {
    function getMoneyAsCents($value)
    {
        // make sure we are dealing with a proper number now, no +.4393 or 3...304 or 76.5895,94
        if (! is_numeric($value)) {
            throw new \InvalidArgumentException('A entrada deve ser numérica!');
        }

        return intval(round(floatval($value), 2) * 100);
    }
}

if (! function_exists('getCobrancaTransactionStatus')) {
    function getCobrancaTransactionStatus($paymentGatewaysConfig, $paymentGateway, $status)
    {
        return $paymentGatewaysConfig[$paymentGateway]['transaction_status'][$status];
    }
}

if (! function_exists('getPixKeyType')) {
    function getPixKeyType($value)
    {
        if (Parser::validateDocument($value)) {
            return Parser::KEY_TYPE_DOCUMENT;
        }

        if (Parser::validateEmail($value)) {
            return Parser::KEY_TYPE_EMAIL;
        }

        if (Parser::validatePhone($value)) {
            return Parser::KEY_TYPE_PHONE;
        }

        if (Parser::validateRandom($value)) {
            return Parser::KEY_TYPE_RANDOM;
        }

        return null;
    }
}

if (! function_exists('getAmount')) {
    function getAmount($money)
    {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '', $stringWithCommaOrDot);

        return floatval(str_replace(',', '.', $removedThousandSeparator));
    }
}

/**
 * Resolve email recipients based on notification configuration.
 * @param string $notificationType The notification type: 'todos', 'cliente', 'tecnico', 'emitente'
 * @param string $clientEmail Client email
 * @param string $technicianEmail Technician email
 * @param string $issuerEmail Issuer/company email
 * @return array Array of email addresses
 */
if (!function_exists('resolveEmailRecipients')) {
    function resolveEmailRecipients($notificationType, $clientEmail, $technicianEmail, $issuerEmail)
    {
        $recipients = [];

        switch ($notificationType) {
            case 'todos':
                $recipients[] = $clientEmail;
                $recipients[] = $technicianEmail;
                $recipients[] = $issuerEmail;
                break;
            case 'cliente':
                $recipients[] = $clientEmail;
                break;
            case 'tecnico':
                $recipients[] = $technicianEmail;
                break;
            case 'emitente':
                $recipients[] = $issuerEmail;
                break;
            default:
                $recipients[] = $clientEmail;
                break;
        }

        return array_filter($recipients);
    }
}

/**
 * Helpers de resposta JSON padronizados.
 * Substituem `echo json_encode(...)` por helpers que ja setam
 * Content-Type e (opcionalmente) codigo HTTP. Ajudam a evitar
 * saida duplicada (echo + set_output) e a manter contratos consistentes.
 *
 * Como sao `exit`-antes (padrao usado pelos controllers legados),
 * use apenas em endpoints AJAX. Em API v2 prefira ApiResponseTrait.
 */
if (!function_exists('json_response')) {
    /**
     * Imprime uma resposta JSON e encerra o script.
     *
     * @param array $data           Payload (sera convertido via json_encode)
     * @param int   $httpCode       Codigo HTTP (default 200)
     * @param bool  $exit           Se true (default), chama exit apos imprimir
     */
    function json_response(array $data, int $httpCode = 200, bool $exit = true)
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($httpCode);
        }
        echo json_encode($data);
        if ($exit) {
            exit;
        }
    }
}

if (!function_exists('json_success')) {
    /**
     * Atalho para resposta de sucesso: {success: true, message: ..., ...}
     */
    function json_success(string $message = '', array $extra = [], int $httpCode = 200, bool $exit = true)
    {
        $payload = array_merge(['success' => true, 'message' => $message], $extra);
        json_response($payload, $httpCode, $exit);
    }
}

if (!function_exists('json_error')) {
    /**
     * Atalho para resposta de erro: {success: false, message: ..., ...}
     */
    function json_error(string $message = '', array $extra = [], int $httpCode = 400, bool $exit = true)
    {
        $payload = array_merge(['success' => false, 'message' => $message], $extra);
        json_response($payload, $httpCode, $exit);
    }
}
