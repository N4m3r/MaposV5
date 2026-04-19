<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once __DIR__ . '/../vendor/autoload.php';

function pdf_create($html, $filename, $stream = true, $landscape = false)
{
    // Aumentar limites de memória para processar HTML grande
    ini_set('pcre.backtrack_limit', '5000000');
    ini_set('pcre.recursion_limit', '5000000');

    $config = [
        'c',
        $landscape ? 'A4-L' : 'A4',
        'tempDir' => FCPATH . 'assets/uploads/temp/',
        'autoScriptToLang' => false,
        'autoLangToFont' => false,
    ];

    $mpdf = new \Mpdf\Mpdf($config);

    $mpdf->showImageErrors = false;
    $mpdf->setAutoTopMargin = false;
    $mpdf->setAutoBottomMargin = false;

    // Processar HTML em partes se for muito grande
    $htmlSize = strlen($html);
    if ($htmlSize > 1000000) {
        // Para HTML grandes, dividir em seções
        $mpdf->WriteHTML($html, 2); // Mode 2 para HTML completo
    } else {
        $mpdf->WriteHTML($html);
    }

    if ($stream) {
        $mpdf->Output($filename . '.pdf', 'I');
    } else {
        $mpdf->Output(FCPATH . 'assets/uploads/temp/' . $filename . '.pdf', 'F');

        return FCPATH . 'assets/uploads/temp/' . $filename . '.pdf';
    }
}
