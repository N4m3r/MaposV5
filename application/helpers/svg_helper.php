<?php
/**
 * SVG Icon Helper — MapOS V5
 *
 * Gera ícones SVG inline a partir do sprite assets/svg/icons.svg
 * Usa <svg><use href="...#id"/></svg> para máxima performance (1 HTTP request para todos os ícones).
 *
 * Uso:
 *   echo svg_icon('home');                    // ícone 20x20 (padrão sidebar)
 *   echo svg_icon('eye', 16, 16, 'btn-icon');// ícone 16x16 com classe extra
 *   echo svg_icon('edit', 14, 14, '', 'margin-right:4px');
 *   echo svg_icon('user', 24, 24, 'icon-wrapper-i'); // ícone grande
 */

if (!function_exists('svg_icon')) {
    /**
     * Retorna SVG inline usando sprite <use>.
     *
     * @param string $name       ID do símbolo no sprite (ex: 'home', 'eye', 'edit')
     * @param int    $width      Largura em pixels (default 20)
     * @param int    $height     Altura em pixels (default 20)
     * @param string $class      Classes CSS extras (ex: 'iconX btn-icon')
     * @param string $style      Estilos inline (ex: 'margin-right:4px')
     * @return string            HTML do SVG inline
     */
    function svg_icon($name, $width = 20, $height = 20, $class = '', $style = '', $id = '')
    {
        $base = base_url();
        $href = "{$base}assets/svg/icons.svg#{$name}";

        $classAttr = $class ? " class=\"svg-icon {$class}\"" : ' class="svg-icon"';
        $styleAttr = $style ? " style=\"{$style}\"" : '';
        $idAttr = $id ? " id=\"{$id}\"" : '';

        return "<svg{$idAttr}{$classAttr} width=\"{$width}\" height=\"{$height}\"{$styleAttr} aria-hidden=\"true\">" .
               "<use href=\"{$href}\"/></svg>";
    }
}

if (!function_exists('svg_icon_raw')) {
    /**
     * Retorna SVG inline direto (sem sprite) para ícones dinâmicos ou únicos.
     * Útil para badges, ícones de notificação, etc.
     *
     * @param string $d          Atributo 'd' do <path> SVG
     * @param int    $width      Largura (default 20)
     * @param int    $height     Altura (default 20)
     * @param string $class      Classes CSS
     * @param string $viewBox    ViewBox (default '0 0 24 24')
     * @return string
     */
    function svg_icon_raw($d, $width = 20, $height = 20, $class = '', $viewBox = '0 0 24 24')
    {
        $classAttr = $class ? " class=\"svg-icon {$class}\"" : ' class="svg-icon"';
        return "<svg{$classAttr} width=\"{$width}\" height=\"{$height}\" viewBox=\"{$viewBox}\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">" .
               "<path d=\"{$d}\"/>" .
               "</svg>";
    }
}