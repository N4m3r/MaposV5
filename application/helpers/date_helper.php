<?php

function dateInterval($startDate, $finalDate)
{
    $data = date('d/m/Y', strtotime($startDate));

    // Criar o objeto representando a data
    $obj_data = DateTime::createFromFormat('d/m/Y', $data);
    
    if (!$obj_data) {
        throw new InvalidArgumentException('Erro ao converter a data: ' . $startDate);
    }

    $obj_data->setTime(0, 0, 0);

    // Realizar a soma de dias
    $intervalo = new DateInterval('P' . intval($finalDate) . 'D');
    $obj_data->add($intervalo);

    // Formatar a data obtida
    return $obj_data->format('d/m/Y');
}

/**
 * Parse a Brazilian date string (dd/mm/YYYY) to SQL format (YYYY-mm-dd).
 * Returns $default on failure (defaults to null).
 */
function parseDateBr($dateBr, $default = null)
{
    if (empty($dateBr)) {
        return $default;
    }

    $parts = explode('/', $dateBr);

    if (count($parts) !== 3) {
        return $default;
    }

    $day   = (int) $parts[0];
    $month = (int) $parts[1];
    $year  = (int) $parts[2];

    if (!checkdate($month, $day, $year)) {
        return $default;
    }

    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}
