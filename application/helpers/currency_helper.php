<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Helper de funções monetárias/currency
 */

if (!function_exists('format_currency')) {
    /**
     * Formata um valor como moeda brasileira (R$)
     *
     * @param float $value
     * @param bool $show_symbol
     * @return string
     */
    function format_currency($value, $show_symbol = true)
    {
        $formatted = number_format($value, 2, ',', '.');
        return $show_symbol ? 'R$ ' . $formatted : $formatted;
    }
}

if (!function_exists('currency_to_decimal')) {
    /**
     * Converte valor monetário brasileiro para decimal
     *
     * @param string $value
     * @return float
     */
    function currency_to_decimal($value)
    {
        if (empty($value)) {
            return 0.00;
        }

        // Remove símbolo de moeda e espaços
        $value = str_replace(['R$', ' '], '', $value);

        // Remove pontos de milhar
        $value = str_replace('.', '', $value);

        // Substitui vírgula decimal por ponto
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}

if (!function_exists('decimal_to_currency')) {
    /**
     * Converte decimal para formato de moeda brasileira
     *
     * @param float $value
     * @return string
     */
    function decimal_to_currency($value)
    {
        return number_format($value, 2, ',', '.');
    }
}
