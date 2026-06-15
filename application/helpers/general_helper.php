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

/**
 * Helpers de UX/UI adicionados no Plano de Melhorias Fase 1.
 * Padronizam: breadcrumb, empty states, botoes, notificacoes.
 */

if (!function_exists('breadcrumb')) {
    /**
     * Renderiza uma trilha de navegaçao (breadcrumb).
     *
     * @param array  $items      Array associativo [['label' => 'X', 'url' => 'y'], ...]
     *                           Itens sem 'url' sao tratados como pagina atual (nao link).
     * @param string $separator  Caractere/HTML entre os itens (default: <i bx-chevron-right>).
     * @param string $home_label Label do primeiro item (home) - opcional, prepende.
     * @return string HTML do breadcrumb
     */
    function breadcrumb(array $items, string $separator = '<i class=\'bx bx-chevron-right\'></i>', string $home_label = 'Início'): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<nav aria-label="breadcrumb" class="ux-breadcrumb">';
        $html .= '<ol class="ux-breadcrumb-list">';

        // Item home
        $homeIcon = '<i class=\'bx bx-home-alt\'></i>';
        $html .= '<li class="ux-breadcrumb-item">';
        $html .= '<a href="' . base_url() . '" class="ux-breadcrumb-link">' . $homeIcon . ' <span>' . e($home_label) . '</span></a>';
        $html .= '</li>';

        $total = count($items);
        foreach ($items as $i => $item) {
            $html .= '<li class="ux-breadcrumb-separator" aria-hidden="true">' . $separator . '</li>';
            $label = e($item['label'] ?? '');
            $icon = isset($item['icon']) ? '<i class=\'' . e($item['icon']) . '\'></i> ' : '';

            // Ultimo item ou sem URL = pagina atual (nao link)
            $isLast = ($i === $total - 1);
            if ($isLast || empty($item['url'])) {
                $html .= '<li class="ux-breadcrumb-item ux-breadcrumb-current" aria-current="page">' . $icon . $label . '</li>';
            } else {
                $html .= '<li class="ux-breadcrumb-item">';
                $html .= '<a href="' . e($item['url']) . '" class="ux-breadcrumb-link">' . $icon . $label . '</a>';
                $html .= '</li>';
            }
        }

        $html .= '</ol>';
        $html .= '</nav>';

        return $html;
    }
}

if (!function_exists('empty_state')) {
    /**
     * Renderiza um estado vazio amigavel com CTA.
     *
     * @param string $title     Titulo principal (ex: "Nenhum cliente ainda")
     * @param string $message   Mensagem secundaria (ex: "Comece cadastrando seu primeiro cliente")
     * @param array  $cta       ['label' => 'Adicionar cliente', 'url' => '/clientes/adicionar', 'icon' => 'bx-plus']
     *                           Se vazio, nao renderiza botao.
     * @param string $icon      Classe do Boxicon (ex: 'bx-user', 'bx-package')
     * @return string HTML do empty state
     */
    function empty_state(string $title, string $message = '', array $cta = [], string $icon = 'bx-inbox'): string
    {
        $html = '<div class="ux-empty-state">';
        $html .= '<div class="ux-empty-state-icon"><i class="bx ' . e($icon) . '"></i></div>';
        $html .= '<h3 class="ux-empty-state-title">' . e($title) . '</h3>';
        if ($message !== '') {
            $html .= '<p class="ux-empty-state-message">' . e($message) . '</p>';
        }
        if (!empty($cta['label']) && !empty($cta['url'])) {
            $ctaIcon = isset($cta['icon']) ? '<i class="bx ' . e($cta['icon']) . '"></i> ' : '';
            $variant = e($cta['variant'] ?? 'primary');
            $html .= '<a href="' . e($cta['url']) . '" class="ux-btn ux-btn-' . $variant . '">';
            $html .= $ctaIcon . e($cta['label']);
            $html .= '</a>';
        }
        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('btn_action')) {
    /**
     * Renderiza um botao de acao padronizado.
     *
     * @param string $url       URL de destino
     * @param string $label     Texto do botao
     * @param string $variant   Variante: primary, secondary, success, danger, warning, info
     * @param string $icon      Classe do Boxicon (opcional)
     * @param array  $attrs     Atributos extras ['data-id' => 1, 'onclick' => '...']
     * @return string HTML do botao
     */
    function btn_action(string $url, string $label, string $variant = 'primary', string $icon = '', array $attrs = []): string
    {
        $iconHtml = $icon !== '' ? '<i class="bx ' . e($icon) . '"></i> ' : '';
        $attrStr = '';
        foreach ($attrs as $k => $v) {
            $attrStr .= ' ' . e($k) . '="' . e($v) . '"';
        }
        return '<a href="' . e($url) . '" class="ux-btn ux-btn-' . e($variant) . '"' . $attrStr . '>'
             . $iconHtml . e($label) . '</a>';
    }
}

if (!function_exists('notify')) {
    /**
     * Renderiza uma notificacao (alerta) padronizada.
     *
     * @param string $message  Mensagem
     * @param string $type     success, error, warning, info
     * @param string $action   URL opcional para acao (ex: "Ver detalhes")
     * @param string $actionLabel Texto do link de acao
     * @return string HTML do alerta
     */
    function notify(string $message, string $type = 'info', string $action = '', string $actionLabel = ''): string
    {
        $iconMap = [
            'success' => 'bx-check-circle',
            'error'   => 'bx-error-circle',
            'warning' => 'bx-error',
            'info'    => 'bx-info-circle',
        ];
        $icon = $iconMap[$type] ?? 'bx-info-circle';
        $actionHtml = '';
        if ($action !== '' && $actionLabel !== '') {
            $actionHtml = ' <a href="' . e($action) . '" class="ux-notify-action">' . e($actionLabel) . ' &rarr;</a>';
        }
        return '<div class="ux-notify ux-notify-' . e($type) . '" role="alert">'
             . '<i class="bx ' . e($icon) . '"></i> '
             . '<span>' . e($message) . '</span>'
             . $actionHtml
             . '</div>';
    }
}
