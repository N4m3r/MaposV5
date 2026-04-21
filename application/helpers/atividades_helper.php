<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Helper para Sistema de Atividades de Técnicos
 * Funções auxiliares para cálculos, formatações e validações
 */

// ========== FORMATAÇÃO DE TEMPO ==========

/**
 * Formata minutos em string legível
 * Ex: 125 -> "2h 5min"
 */
if (!function_exists('formatar_duracao')) {
    function formatar_duracao($minutos)
    {
        if (!$minutos || $minutos <= 0) {
            return '0min';
        }

        $horas = floor($minutos / 60);
        $mins = $minutos % 60;

        if ($horas > 0 && $mins > 0) {
            return "{$horas}h {$mins}min";
        } elseif ($horas > 0) {
            return "{$horas}h";
        } else {
            return "{$mins}min";
        }
    }
}

/**
 * Formata minutos em decimal
 * Ex: 90 -> "1.5"
 */
if (!function_exists('formatar_duracao_decimal')) {
    function formatar_duracao_decimal($minutos)
    {
        if (!$minutos || $minutos <= 0) {
            return '0';
        }
        return number_format($minutos / 60, 1);
    }
}

/**
 * Calcula diferença entre duas datas em minutos
 */
if (!function_exists('calcular_diferenca_minutos')) {
    function calcular_diferenca_minutos($hora_inicio, $hora_fim = null)
    {
        if (!$hora_inicio) {
            return 0;
        }

        $inicio = strtotime($hora_inicio);
        $fim = $hora_fim ? strtotime($hora_fim) : time();

        return round(($fim - $inicio) / 60);
    }
}

/**
 * Formata data/hora para exibição
 */
if (!function_exists('formatar_data_hora')) {
    function formatar_data_hora($data_hora, $formato = 'd/m/Y H:i')
    {
        if (!$data_hora) {
            return '--:--';
        }
        return date($formato, strtotime($data_hora));
    }
}

/**
 * Formata apenas hora
 */
if (!function_exists('formatar_hora')) {
    function formatar_hora($data_hora)
    {
        if (!$data_hora) {
            return '--:--';
        }
        return date('H:i', strtotime($data_hora));
    }
}

// ========== STATUS E CORES ==========

/**
 * Retorna cor baseada no status da atividade
 */
if (!function_exists('get_cor_status')) {
    function get_cor_status($status)
    {
        $cores = [
            'em_andamento' => '#ffc107',  // amarelo
            'finalizada' => '#28a745',   // verde
            'pausada' => '#6c757d',     // cinza
            'cancelada' => '#dc3545',   // vermelho
        ];

        return $cores[$status] ?? '#6c757d';
    }
}

/**
 * Retorna label traduzido do status
 */
if (!function_exists('get_label_status')) {
    function get_label_status($status)
    {
        $labels = [
            'em_andamento' => 'Em Andamento',
            'finalizada' => 'Finalizada',
            'pausada' => 'Pausada',
            'cancelada' => 'Cancelada',
        ];

        return $labels[$status] ?? ucfirst($status);
    }
}

/**
 * Retorna classe CSS do Bootstrap baseada no status
 */
if (!function_exists('get_badge_class_status')) {
    function get_badge_class_status($status)
    {
        $classes = [
            'em_andamento' => 'badge badge-warning',
            'finalizada' => 'badge badge-success',
            'pausada' => 'badge',
            'cancelada' => 'badge badge-important',
        ];

        return $classes[$status] ?? 'badge';
    }
}

/**
 * Retorna cor da categoria
 */
if (!function_exists('get_cor_categoria')) {
    function get_cor_categoria($categoria)
    {
        $cores = [
            'rede' => '#007bff',
            'cftv' => '#dc3545',
            'seguranca' => '#6f42c1',
            'infra' => '#fd7e14',
            'internet' => '#17a2b8',
            'geral' => '#6c757d',
        ];

        return $cores[$categoria] ?? '#6c757d';
    }
}

/**
 * Retorna ícone da categoria
 */
if (!function_exists('get_icone_categoria')) {
    function get_icone_categoria($categoria)
    {
        $icones = [
            'rede' => 'bx-network-chart',
            'cftv' => 'bx-camera',
            'seguranca' => 'bx-shield-alt',
            'infra' => 'bx-hdd',
            'internet' => 'bx-wifi',
            'geral' => 'bx-wrench',
        ];

        return $icones[$categoria] ?? 'bx-wrench';
    }
}

/**
 * Retorna nome legível da categoria
 */
if (!function_exists('get_nome_categoria')) {
    function get_nome_categoria($categoria)
    {
        $nomes = [
            'rede' => 'Rede Estruturada',
            'cftv' => 'CFTV IP',
            'seguranca' => 'Segurança',
            'infra' => 'Infraestrutura',
            'internet' => 'Internet/Rede',
            'geral' => 'Serviços Gerais',
        ];

        return $nomes[$categoria] ?? ucfirst($categoria);
    }
}

// ========== VALIDAÇÕES ==========

/**
 * Verifica se técnico pode iniciar nova atividade
 */
if (!function_exists('pode_iniciar_atividade')) {
    function pode_iniciar_atividade($tecnico_id, &$CI = null)
    {
        if (!$CI) {
            $CI =& get_instance();
        }

        $CI->load->model('Atividades_model');
        return !$CI->Atividades_model->hasAtividadeEmAndamento($tecnico_id);
    }
}

/**
 * Verifica se técnico tem atividade em andamento
 */
if (!function_exists('tem_atividade_em_andamento')) {
    function tem_atividade_em_andamento($tecnico_id, &$CI = null)
    {
        if (!$CI) {
            $CI =& get_instance();
        }

        $CI->load->model('Atividades_model');
        return $CI->Atividades_model->hasAtividadeEmAndamento($tecnico_id);
    }
}

// ========== CÁLCULOS ==========

/**
 * Calcula tempo trabalhado em um período
 */
if (!function_exists('calcular_tempo_trabalhado')) {
    function calcular_tempo_trabalhado($atividades)
    {
        $total_minutos = 0;

        foreach ($atividades as $atv) {
            if ($atv->status == 'finalizada' && $atv->duracao_minutos) {
                $total_minutos += $atv->duracao_minutos;
            }
        }

        return $total_minutos;
    }
}

/**
 * Calcula taxa de conclusão
 */
if (!function_exists('calcular_taxa_conclusao')) {
    function calcular_taxa_conclusao($atividades)
    {
        $total = count($atividades);
        if ($total == 0) {
            return 0;
        }

        $concluidas = 0;
        foreach ($atividades as $atv) {
            if ($atv->concluida == 1) {
                $concluidas++;
            }
        }

        return round(($concluidas / $total) * 100);
    }
}

/**
 * Calcula média de tempo por atividade
 */
if (!function_exists('calcular_media_tempo')) {
    function calcular_media_tempo($atividades)
    {
        $total_minutos = 0;
        $contador = 0;

        foreach ($atividades as $atv) {
            if ($atv->duracao_minutos && $atv->duracao_minutos > 0) {
                $total_minutos += $atv->duracao_minutos;
                $contador++;
            }
        }

        if ($contador == 0) {
            return 0;
        }

        return round($total_minutos / $contador);
    }
}

// ========== AJUSTES DE TEMPO ==========

/**
 * Arredonda minutos para intervalos comerciais
 */
if (!function_exists('arredondar_intervalo')) {
    function arredondar_intervalo($minutos, $intervalo = 15)
    {
        return ceil($minutos / $intervalo) * $intervalo;
    }
}

/**
 * Converte horas:minutos para minutos
 */
if (!function_exists('time_to_minutos')) {
    function time_to_minutos($time)
    {
        if (strpos($time, ':') !== false) {
            list($horas, $minutos) = explode(':', $time);
            return ($horas * 60) + $minutos;
        }
        return (int) $time;
    }
}

/**
 * Converte minutos para horas:minutos
 */
if (!function_exists('minutos_to_time')) {
    function minutos_to_time($minutos)
    {
        $h = floor($minutos / 60);
        $m = $minutos % 60;
        return sprintf('%02d:%02d', $h, $m);
    }
}

// ========== GEOLOCALIZAÇÃO ==========

/**
 * Calcula distância entre dois pontos (haversine)
 */
if (!function_exists('calcular_distancia')) {
    function calcular_distancia($lat1, $lon1, $lat2, $lon2)
    {
        $raio_terra = 6371; // km

        $dlat = deg2rad($lat2 - $lat1);
        $dlon = deg2rad($lon2 - $lon1);

        $a = sin($dlat / 2) * sin($dlat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dlon / 2) * sin($dlon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $raio_terra * $c; // distância em km
    }
}

/**
 * Verifica se coordenadas são válidas
 */
if (!function_exists('coordenadas_validas')) {
    function coordenadas_validas($lat, $lon)
    {
        return is_numeric($lat) && is_numeric($lon)
            && $lat >= -90 && $lat <= 90
            && $lon >= -180 && $lon <= 180;
    }
}

// ========== UTILIDADES PARA VIEWS ==========

/**
 * Gera HTML para cronômetro ao vivo
 */
if (!function_exists('cronometro_html')) {
    function cronometro_html($hora_inicio, $element_id = 'cronometro')
    {
        $inicio = strtotime($hora_inicio);
        $diff = time() - $inicio;
        $horas = floor($diff / 3600);
        $mins = floor(($diff % 3600) / 60);
        $segs = $diff % 60;

        return sprintf('<span id="%s" data-inicio="%s">%02d:%02d:%02d</span>',
            $element_id,
            $hora_inicio,
            $horas,
            $mins,
            $segs
        );
    }
}

/**
 * Gera badge de prioridade
 */
if (!function_exists('badge_prioridade')) {
    function badge_prioridade($prioridade)
    {
        $classes = [
            'baixa' => 'badge',
            'normal' => 'badge badge-info',
            'alta' => 'badge badge-warning',
            'urgente' => 'badge badge-important',
        ];

        $class = $classes[$prioridade] ?? 'badge';
        $label = ucfirst($prioridade);

        return "<span class=\"{$class}\">{$label}</span>";
    }
}

/**
 * Gera ícone BoxIcons
 */
if (!function_exists('bx_icon')) {
    function bx_icon($nome, $cor = null)
    {
        $style = $cor ? " style=\"color: {$cor};\"" : '';
        return "<i class=\"bx {$nome}\"{$style}></i>";
    }
}

/**
 * Trunca texto
 */
if (!function_exists('truncar_texto')) {
    function truncar_texto($texto, $tamanho = 50)
    {
        if (strlen($texto) > $tamanho) {
            return substr($texto, 0, $tamanho) . '...';
        }
        return $texto;
    }
}

// ========== FUNÇÕES PARA RELATÓRIOS ==========

/**
 * Agrupa atividades por data
 */
if (!function_exists('agrupar_por_data')) {
    function agrupar_por_data($atividades)
    {
        $grupos = [];

        foreach ($atividades as $atv) {
            $data = date('Y-m-d', strtotime($atv->hora_inicio));
            if (!isset($grupos[$data])) {
                $grupos[$data] = [];
            }
            $grupos[$data][] = $atv;
        }

        krsort($grupos); // Mais recente primeiro
        return $grupos;
    }
}

/**
 * Calcula estatísticas de uma lista de atividades
 */
if (!function_exists('calcular_estatisticas_atividades')) {
    function calcular_estatisticas_atividades($atividades)
    {
        $stats = [
            'total' => count($atividades),
            'concluidas' => 0,
            'nao_concluidas' => 0,
            'tempo_total' => 0,
            'por_categoria' => [],
            'por_status' => [],
        ];

        foreach ($atividades as $atv) {
            // Contagem por status
            $stats['por_status'][$atv->status] = ($stats['por_status'][$atv->status] ?? 0) + 1;

            // Contagem por categoria
            $cat = $atv->categoria ?? 'geral';
            $stats['por_categoria'][$cat] = ($stats['por_categoria'][$cat] ?? 0) + 1;

            // Tempo total
            if ($atv->duracao_minutos) {
                $stats['tempo_total'] += $atv->duracao_minutos;
            }

            // Concluídas vs não concluídas
            if ($atv->concluida == 1) {
                $stats['concluidas']++;
            } elseif ($atv->status == 'finalizada' && $atv->concluida == 0) {
                $stats['nao_concluidas']++;
            }
        }

        // Calcula média
        $stats['tempo_medio'] = $stats['total'] > 0 ? round($stats['tempo_total'] / $stats['total']) : 0;

        return $stats;
    }
}

// ========== NOTIFICAÇÕES ==========

/**
 * Formata mensagem de resumo para notificação
 */
if (!function_exists('formatar_resumo_notificacao')) {
    function formatar_resumo_notificacao($atividades)
    {
        $tempo = calcular_tempo_trabalhado($atividades);
        $total = count($atividades);
        $concluidas = 0;

        foreach ($atividades as $atv) {
            if ($atv->concluida == 1) {
                $concluidas++;
            }
        }

        return [
            'total' => $total,
            'concluidas' => $concluidas,
            'tempo_formatado' => formatar_duracao($tempo),
        ];
    }
}
