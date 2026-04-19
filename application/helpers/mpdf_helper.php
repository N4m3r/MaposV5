<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Carregar autoload do vendor se ainda não estiver carregado
$autoload_path = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload_path)) {
    require_once $autoload_path;
}

function pdf_create($html, $filename, $stream = true, $landscape = false)
{
    try {
        // Verificar se mPDF está disponível
        if (!class_exists('\Mpdf\Mpdf')) {
            log_message('error', 'pdf_create: Classe Mpdf não encontrada');
            return false;
        }

        // Aumentar limites de memória para processar HTML grande
        ini_set('pcre.backtrack_limit', '5000000');
        ini_set('pcre.recursion_limit', '5000000');

        // Configuração básica
        $config = [
            'mode' => 'utf-8',
            'format' => $landscape ? 'A4-L' : 'A4',
            'tempDir' => FCPATH . 'assets/uploads/temp/',
            'margin_top' => 10,
            'margin_bottom' => 10,
        ];

        $mpdf = new \Mpdf\Mpdf($config);
        $mpdf->showImageErrors = false;

        // Escrever HTML
        $mpdf->WriteHTML($html);

        if ($stream) {
            $mpdf->Output($filename . '.pdf', 'I');
        } else {
            $output_path = FCPATH . 'assets/uploads/temp/' . $filename . '.pdf';
            $mpdf->Output($output_path, 'F');
            return $output_path;
        }
    } catch (Exception $e) {
        log_message('error', 'pdf_create erro: ' . $e->getMessage());
        return false;
    }
}
