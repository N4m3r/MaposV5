<?php
/**
 * Component Helper
 * Helper para renderização de componentes reutilizáveis
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Renderiza um componente
 *
 * @param string $name Nome do componente
 * @param array $props Propriedades do componente
 * @return string HTML renderizado
 */
function component(string $name, array $props = []): string
{
    $componentPath = APPPATH . "views/components/{$name}.php";

    if (!file_exists($componentPath)) {
        log_message('error', "Componente não encontrado: {$name}");
        return "<!-- Componente {$name} não encontrado -->";
    }

    // Extrai props para variáveis locais
    extract($props);

    // Inicia buffer de saída
    ob_start();
    include $componentPath;
    return ob_get_clean();
}

/**
 * Renderiza um botão
 *
 * @param string $text Texto do botão
 * @param string $url URL de destino (se houver)
 * @param string $type Tipo: primary, secondary, success, danger, warning, info
 * @param array $attrs Atributos adicionais
 * @return string HTML
 */
function button(string $text, string $url = '', string $type = 'primary', array $attrs = []): string
{
    $class = "btn btn-{$type}";
    if (isset($attrs['size'])) {
        $class .= " btn-{$attrs['size']}";
        unset($attrs['size']);
    }
    if (isset($attrs['outline']) && $attrs['outline']) {
        $class = "btn btn-outline-{$type}";
        unset($attrs['outline']);
    }

    $attributes = '';
    foreach ($attrs as $key => $value) {
        if ($key !== 'icon') {
            $attributes .= " {$key}=\"" . htmlspecialchars($value) . "\"";
        }
    }

    $icon = $attrs['icon'] ?? '';
    if ($icon) {
        $text = "<i class='{$icon}'></i> {$text}";
    }

    if ($url) {
        return "<a href=\"" . htmlspecialchars($url) . "\" class=\"{$class}\"{$attributes}>{$text}</a>";
    }

    return "<button type=\"button\" class=\"{$class}\"{$attributes}>{$text}</button>";
}

/**
 * Renderiza um card
 *
 * @param string $title Título do card
 * @param string $content Conteúdo HTML
 * @param array $options Opções adicionais
 * @return string HTML
 */
function card(string $title = '', string $content = '', array $options = []): string
{
    $header = $title ? "<div class='card-header'\u003e<h5 class='card-title mb-0'\u003e{$title}\u003c/h5\u003e</div\u003e" : '';
    $footer = $options['footer'] ?? '';
    $class = $options['class'] ?? '';

    $html = "<div class='card {$class}'\u003e";
    $html .= $header;
    $html .= "<div class='card-body'\u003e{$content}\u003c/div\u003e";
    if ($footer) {
        $html .= "<div class='card-footer'\u003e{$footer}\u003c/div\u003e";
    }
    $html .= "</div\u003e";

    return $html;
}

/**
 * Renderiza uma badge
 *
 * @param string $text Texto da badge
 * @param string $type Tipo: primary, secondary, success, danger, warning, info
 * @return string HTML
 */
function badge(string $text, string $type = 'primary'): string
{
    return "<span class='badge bg-{$type}'\u003e" . htmlspecialchars($text) . "</span\u003e";
}

/**
 * Renderiza um alerta
 *
 * @param string $message Mensagem
 * @param string $type Tipo: success, danger, warning, info
 * @param bool $dismissible Se pode ser fechado
 * @return string HTML
 */
function alert(string $message, string $type = 'info', bool $dismissible = false): string
{
    $class = "alert alert-{$type}";
    $dismiss = '';

    if ($dismissible) {
        $class .= ' alert-dismissible fade show';
        $dismiss = "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
    }

    return "<div class='{$class}' role='alert'\u003e{$message}{$dismiss}\u003c/div\u003e";
}

/**
 * Renderiza paginação
 *
 * @param int $total Total de registros
 * @param int $perPage Registros por página
 * @param int $currentPage Página atual
 * @param string $baseUrl URL base
 * @return string HTML
 */
function pagination(int $total, int $perPage, int $currentPage, string $baseUrl): string
{
    $totalPages = (int) ceil($total / $perPage);

    if ($totalPages <= 1) {
        return '';
    }

    $html = "<ul class='pagination'\u003e";

    // Botão anterior
    $prevClass = $currentPage <= 1 ? 'disabled' : '';
    $prevUrl = $currentPage > 1 ? $baseUrl . '?page=' . ($currentPage - 1) : '#';
    $html .= "<li class='page-item {$prevClass}'\u003e<a class='page-link' href='{$prevUrl}'\u003eAnterior</a\u003e</li\u003e";

    // Páginas
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage ? 'active' : '';
        $url = $baseUrl . '?page=' . $i;
        $html .= "<li class='page-item {$active}'\u003e<a class='page-link' href='{$url}'\u003e{$i}</a\u003e</li\u003e";
    }

    // Botão próximo
    $nextClass = $currentPage >= $totalPages ? 'disabled' : '';
    $nextUrl = $currentPage < $totalPages ? $baseUrl . '?page=' . ($currentPage + 1) : '#';
    $html .= "<li class='page-item {$nextClass}'\u003e<a class='page-link' href='{$nextUrl}'\u003ePróximo</a\u003e</li\u003e";

    $html .= "</ul\u003e";

    return $html;
}

/**
 * Renderiza um formulário de busca
 *
 * @param string $action URL de ação
 * @param string $placeholder Placeholder do input
 * @param string $value Valor atual
 * @return string HTML
 */
function searchForm(string $action, string $placeholder = 'Buscar...', string $value = ''): string
{
    $value = htmlspecialchars($value);
    return "
        <form method='GET' action='{$action}' class='form-search'\u003e
            <div class='input-group'\u003e
                <input type='text' name='search' class='form-control' placeholder='{$placeholder}' value='{$value}'\u003e
                <button type='submit' class='btn btn-primary'\u003e
                    <i class='fas fa-search'\u003e</i\u003e
                </button\u003e
            </div\u003e
        </form\u003e
    ";
}

/**
 * Renderiza um modal
 *
 * @param string $id ID do modal
 * @param string $title Título
 * @param string $content Conteúdo
 * @param array $options Opções adicionais
 * @return string HTML
 */
function modal(string $id, string $title, string $content, array $options = []): string
{
    $size = $options['size'] ?? '';
    $footer = $options['footer'] ?? '';
    $backdrop = $options['backdrop'] ?? 'static';

    $sizeClass = $size ? "modal-{$size}" : '';

    return "
        <div class='modal fade' id='{$id}' tabindex='-1' data-bs-backdrop='{$backdrop}'\u003e
            <div class='modal-dialog {$sizeClass}'\u003e
                <div class='modal-content'\u003e
                    <div class='modal-header'\u003e
                        <h5 class='modal-title'\u003e{$title}</h5\u003e
                        <button type='button' class='btn-close' data-bs-dismiss='modal'\u003e</button\u003e
                    </div\u003e
                    <div class='modal-body'\u003e
                        {$content}
                    </div\u003e
                    " . ($footer ? "<div class='modal-footer'\u003e{$footer}</div\u003e" : '') . "
                </div\u003e
            </div\u003e
        </div\u003e
    ";
}

/**
 * Renderiza estrelas de avaliação
 *
 * @param int $rating Nota de 1 a 5
 * @param int $max Máximo de estrelas
 * @return string HTML
 */
function starRating(int $rating, int $max = 5): string
{
    $html = "<div class='star-rating'\u003e";
    for ($i = 1; $i <= $max; $i++) {
        $class = $i <= $rating ? 'fas fa-star text-warning' : 'far fa-star text-muted';
        $html .= "<i class='{$class}'\u003e</i\u003e";
    }
    $html .= "</div\u003e";
    return $html;
}

/**
 * Formata valor monetário
 *
 * @param float $value Valor
 * @param string $currency Símbolo da moeda
 * @return string Valor formatado
 */
function money(float $value, string $currency = 'R$'): string
{
    return $currency . ' ' . number_format($value, 2, ',', '.');
}

/**
 * Formata data
 *
 * @param string $date Data
 * @param string $format Formato
 * @return string Data formatada
 */
function formatDate(string $date, string $format = 'd/m/Y'): string
{
    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : $date;
}

/**
 * Retorna classe de status
 *
 * @param string $status Status
 * @return string Classe CSS
 */
function statusClass(string $status): string
{
    $map = [
        'ativo' => 'success',
        'inativo' => 'secondary',
        'pendente' => 'warning',
        'aprovado' => 'success',
        'reprovado' => 'danger',
        'cancelado' => 'danger',
        'finalizado' => 'success',
        'em_andamento' => 'info',
        'aberto' => 'primary',
        'fechado' => 'secondary'
    ];

    return $map[strtolower($status)] ?? 'secondary';
}

/**
 * Renderiza badge de status
 *
 * @param string $status Status
 * @return string HTML
 */
function statusBadge(string $status): string
{
    $class = statusClass($status);
    return badge($status, $class);
}
