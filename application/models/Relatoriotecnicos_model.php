<?php

class Relatoriotecnicos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ==================== KPIs GERAIS ====================

    /**
     * Retorna KPIs gerais do período
     */
    public function getKPIsGerais($data_inicio, $data_fim)
    {
        // Total de OS
        $this->db->where('dataInicial >=', $data_inicio);
        $this->db->where('dataInicial <=', $data_fim);
        $total_os = $this->db->count_all_results('os');

        // OS Finalizadas
        $this->db->where('dataInicial >=', $data_inicio);
        $this->db->where('dataInicial <=', $data_fim);
        $this->db->where_in('status', ['Finalizado', 'Faturado']);
        $os_finalizadas = $this->db->count_all_results('os');

        // Total de técnicos ativos
        $this->db->where('situacao', 1);
        $total_tecnicos = $this->db->count_all_results('usuarios');

        // Média de OS por técnico
        $media_os_tecnico = $total_tecnicos > 0 ? round($total_os / $total_tecnicos, 1) : 0;

        // Taxa de conclusão
        $taxa_conclusao = $total_os > 0 ? round(($os_finalizadas / $total_os) * 100, 2) : 0;

        return [
            'total_os' => $total_os,
            'os_finalizadas' => $os_finalizadas,
            'total_tecnicos' => $total_tecnicos,
            'media_os_tecnico' => $media_os_tecnico,
            'taxa_conclusao' => $taxa_conclusao
        ];
    }

    // ==================== PERFORMANCE POR TÉCNICO ====================

    /**
     * Retorna performance detalhada de todos os técnicos
     */
    public function getPerformanceTecnicos($data_inicio, $data_fim)
    {
        $sql = "
            SELECT
                u.idUsuarios,
                u.nome AS tecnico,
                COUNT(DISTINCT o.idOs) AS total_os,
                COUNT(DISTINCT CASE WHEN o.status IN ('Finalizado', 'Faturado') THEN o.idOs END) AS os_finalizadas,
                COUNT(DISTINCT oc.idCheckin) AS total_checkins,
                SUM(CASE WHEN oc.data_saida IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, oc.data_entrada, oc.data_saida) ELSE 0 END) AS tempo_total_minutos,
                AVG(CASE WHEN oc.data_saida IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, oc.data_entrada, oc.data_saida) END) AS media_tempo_os,
                COUNT(DISTINCT DATE(oc.data_entrada)) AS dias_trabalhados,
                SUM(CASE WHEN o.status IN ('Finalizado', 'Faturado') THEN COALESCE(o.valorTotal, 0) ELSE 0 END) AS valor_total
            FROM usuarios u
            LEFT JOIN os o ON o.tecnico_responsavel = u.idUsuarios
                AND o.dataInicial BETWEEN ? AND ?
            LEFT JOIN os_checkin oc ON oc.os_id = o.idOs
            WHERE u.situacao = 1
            GROUP BY u.idUsuarios, u.nome
            HAVING total_os > 0
            ORDER BY total_os DESC
        ";

        $query = $this->db->query($sql, [$data_inicio . ' 00:00:00', $data_fim . ' 23:59:59']);

        // Verificar se a query falhou
        if ($query === false) {
            return [];
        }

        $result = $query->result();

        // Calcular métricas derivadas
        foreach ($result as $row) {
            $row->horas_trabalhadas = round($row->tempo_total_minutos / 60, 2);
            $row->media_horas_os = $row->total_checkins > 0
                ? round(($row->tempo_total_minutos / 60) / $row->total_checkins, 2)
                : 0;
            $row->media_os_dia = $row->dias_trabalhados > 0
                ? round($row->total_os / $row->dias_trabalhados, 2)
                : 0;
            $row->eficiencia = $row->total_os > 0
                ? round(($row->os_finalizadas / $row->total_os) * 100, 2)
                : 0;
            $row->ticket_medio = $row->os_finalizadas > 0
                ? round($row->valor_total / $row->os_finalizadas, 2)
                : 0;

            // Média de horas por dia trabalhado
            $row->media_horas_dia = $row->dias_trabalhados > 0
                ? round(($row->tempo_total_minutos / 60) / $row->dias_trabalhados, 2)
                : 0;
        }

        return $result;
    }

    /**
     * Performance de um técnico específico
     */
    public function getPerformanceTecnico($tecnico_id, $data_inicio, $data_fim)
    {
        $sql = "
            SELECT
                u.nome AS tecnico,
                COUNT(DISTINCT o.idOs) AS total_os,
                COUNT(DISTINCT CASE WHEN o.status IN ('Finalizado', 'Faturado') THEN o.idOs END) AS os_finalizadas,
                COUNT(DISTINCT oc.idCheckin) AS total_checkins,
                SUM(CASE WHEN oc.data_saida IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, oc.data_entrada, oc.data_saida) ELSE 0 END) AS tempo_total_minutos,
                AVG(CASE WHEN oc.data_saida IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, oc.data_entrada, oc.data_saida) END) AS media_tempo_os,
                COUNT(DISTINCT DATE(oc.data_entrada)) AS dias_trabalhados,
                SUM(CASE WHEN o.status IN ('Finalizado', 'Faturado') THEN COALESCE(o.valorTotal, 0) ELSE 0 END) AS valor_total
            FROM usuarios u
            LEFT JOIN os o ON o.tecnico_responsavel = u.idUsuarios
                AND o.dataInicial BETWEEN ? AND ?
            LEFT JOIN os_checkin oc ON oc.os_id = o.idOs
            WHERE u.idUsuarios = ?
            GROUP BY u.idUsuarios, u.nome
        ";

        $query = $this->db->query($sql, [$data_inicio . ' 00:00:00', $data_fim . ' 23:59:59', $tecnico_id]);

        if ($query === false) {
            return null;
        }

        $result = $query->row();

        if ($result) {
            $result->horas_trabalhadas = round($result->tempo_total_minutos / 60, 2);
            $result->media_horas_dia = $result->dias_trabalhados > 0
                ? round(($result->tempo_total_minutos / 60) / $result->dias_trabalhados, 2)
                : 0;
            $result->media_os_dia = $result->dias_trabalhados > 0
                ? round($result->total_os / $result->dias_trabalhados, 2)
                : 0;
            $result->eficiencia = $result->total_os > 0
                ? round(($result->os_finalizadas / $result->total_os) * 100, 2)
                : 0;
        }

        return $result;
    }

    // ==================== HORAS TRABALHADAS ====================

    /**
     * Retorna horas trabalhadas por dia
     */
    public function getHorasTrabalhadasPorDia($data_inicio, $data_fim, $tecnico_id = null)
    {
        $this->db->select([
            'DATE(oc.data_entrada) as data',
            'u.nome as tecnico',
            'COUNT(DISTINCT o.idOs) as total_os',
            'SUM(TIMESTAMPDIFF(MINUTE, oc.data_entrada, oc.data_saida)) as minutos_trabalhados',
            'ROUND(SUM(TIMESTAMPDIFF(MINUTE, oc.data_entrada, oc.data_saida)) / 60, 2) as horas_trabalhadas'
        ]);
        $this->db->from('os_checkin oc');
        $this->db->join('usuarios u', 'u.idUsuarios = oc.usuarios_id');
        $this->db->join('os o', 'o.idOs = oc.os_id');
        $this->db->where('oc.data_entrada >=', $data_inicio . ' 00:00:00');
        $this->db->where('oc.data_entrada <=', $data_fim . ' 23:59:59');
        $this->db->where('oc.data_saida IS NOT NULL');

        if ($tecnico_id) {
            $this->db->where('oc.usuarios_id', $tecnico_id);
        }

        $this->db->group_by('DATE(oc.data_entrada), u.idUsuarios');
        $this->db->order_by('data', 'ASC');

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    // ==================== OS POR TÉCNICO ====================

    /**
     * Retorna quantidade de OS por técnico
     */
    public function getOSPorTecnico($data_inicio, $data_fim)
    {
        $sql = "
            SELECT
                u.nome as tecnico,
                COUNT(o.idOs) as quantidade,
                COUNT(CASE WHEN o.status IN ('Finalizado', 'Faturado') THEN 1 END) as finalizadas,
                COUNT(CASE WHEN o.status NOT IN ('Finalizado', 'Faturado', 'Cancelado') THEN 1 END) as em_andamento,
                COUNT(CASE WHEN o.status = 'Cancelado' THEN 1 END) as canceladas
            FROM usuarios u
            LEFT JOIN os o ON o.tecnico_responsavel = u.idUsuarios
                AND o.dataInicial BETWEEN ? AND ?
            WHERE u.situacao = 1
            GROUP BY u.idUsuarios, u.nome
            HAVING quantidade > 0
            ORDER BY quantidade DESC
        ";

        $query = $this->db->query($sql, [$data_inicio, $data_fim]);
        return ($query !== false) ? $query->result() : [];
    }

    // ==================== PRODUTIVIDADE ====================

    /**
     * Produtividade diária consolidada
     */
    public function getProdutividadeDiaria($data_inicio, $data_fim)
    {
        $sql = "
            SELECT
                DATE(data_entrada) as data,
                COUNT(DISTINCT usuarios_id) as tecnicos_ativos,
                COUNT(*) as total_atendimentos,
                SUM(TIMESTAMPDIFF(MINUTE, data_entrada, data_saida)) / COUNT(DISTINCT usuarios_id) / 60 as media_horas_tecnico
            FROM os_checkin
            WHERE data_entrada BETWEEN ? AND ?
            AND data_saida IS NOT NULL
            GROUP BY DATE(data_entrada)
            ORDER BY data ASC
        ";

        $query = $this->db->query($sql, [$data_inicio . ' 00:00:00', $data_fim . ' 23:59:59']);
        $result = $query ? $query->result() : [];

        foreach ($result as $row) {
            $row->media_horas_tecnico = round($row->media_horas_tecnico, 2);
        }

        return $result;
    }

    /**
     * Ranking de técnicos
     */
    public function getRankingTecnicos($data_inicio, $data_fim)
    {
        $performance = $this->getPerformanceTecnicos($data_inicio, $data_fim);

        // Calcular score composto (peso: OS 40%, Eficiência 40%, Horas 20%)
        foreach ($performance as $key => $tecnico) {
            $score_os = min($tecnico->total_os / 50, 1) * 40; // Normaliza até 50 OS
            $score_eficiencia = $tecnico->eficiencia * 0.4;
            $score_horas = min($tecnico->media_horas_dia / 8, 1) * 20; // Ideal 8h/dia

            $tecnico->score = round($score_os + $score_eficiencia + $score_horas, 2);
            $tecnico->posicao = $key + 1;
        }

        // Reordena por score
        usort($performance, function($a, $b) {
            return $b->score <=> $a->score;
        });

        // Atualiza posições
        foreach ($performance as $key => $tecnico) {
            $tecnico->posicao = $key + 1;
        }

        return $performance;
    }

    // ==================== PROJEÇÕES ====================

    /**
     * Calcula projeções para o próximo mês
     */
    public function getProjecoes($data_inicio, $data_fim)
    {
        // Calcular taxa de crescimento baseada nos últimos 3 meses
        $data_3meses = date('Y-m-d', strtotime('-3 months'));

        $sql = "
            SELECT
                DATE_FORMAT(dataInicial, '%Y-%m') as mes,
                COUNT(*) as total_os
            FROM os
            WHERE dataInicial >= ?
            GROUP BY DATE_FORMAT(dataInicial, '%Y-%m')
            ORDER BY mes ASC
        ";

        $query = $this->db->query($sql, [$data_3meses]);
        $historico = $query ? $query->result() : [];

        $taxa_crescimento = 0;
        if (count($historico) >= 2) {
            $primeiro = $historico[0]->total_os;
            $ultimo = $historico[count($historico) - 1]->total_os;
            if ($primeiro > 0) {
                $taxa_crescimento = (($ultimo - $primeiro) / $primeiro) * 100;
            }
        }

        // Médias atuais
        $media_os_dia = $this->getMediaOSDia($data_inicio, $data_fim);
        $dias_uteis_mes = 22;

        $projecao_proximo_mes = $media_os_dia * $dias_uteis_mes * (1 + ($taxa_crescimento / 100));

        return [
            'taxa_crescimento' => round($taxa_crescimento, 2),
            'media_os_dia' => round($media_os_dia, 2),
            'projecao_proximo_mes' => round($projecao_proximo_mes, 0),
            'dias_uteis_mes' => $dias_uteis_mes
        ];
    }

    /**
     * Média de OS por dia
     */
    private function getMediaOSDia($data_inicio, $data_fim)
    {
        $sql = "
            SELECT
                COUNT(*) / COUNT(DISTINCT DATE(dataInicial)) as media
            FROM os
            WHERE dataInicial BETWEEN ? AND ?
        ";

        $result = $this->db->query($sql, [$data_inicio, $data_fim])->row();
        return $result ? $result->media : 0;
    }

    // ==================== COMPARATIVOS ====================

    /**
     * Comparativo mensal dos últimos N meses
     */
    public function getComparativoMensal($meses = 6)
    {
        $sql = "
            SELECT
                DATE_FORMAT(o.dataInicial, '%Y-%m') as mes,
                DATE_FORMAT(o.dataInicial, '%m/%Y') as mes_formatado,
                COUNT(DISTINCT o.idOs) as total_os,
                COUNT(DISTINCT CASE WHEN o.status IN ('Finalizado', 'Faturado') THEN o.idOs END) as finalizadas,
                COUNT(DISTINCT o.tecnico_responsavel) as tecnicos_ativos,
                ROUND(COUNT(DISTINCT o.idOs) / COUNT(DISTINCT o.tecnico_responsavel), 2) as media_os_tecnico
            FROM os o
            WHERE o.dataInicial >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(o.dataInicial, '%Y-%m')
            ORDER BY mes ASC
        ";

        $query = $this->db->query($sql, [$meses]);
        return $query ? $query->result() : [];
    }

    // ==================== DETALHES ====================

    /**
     * Evolução diária de um técnico
     */
    public function getEvolucaoDiariaTecnico($tecnico_id, $data_inicio, $data_fim)
    {
        $sql = "
            SELECT
                DATE(o.dataInicial) as data,
                COUNT(o.idOs) as total_os,
                COUNT(CASE WHEN o.status IN ('Finalizado', 'Faturado') THEN 1 END) as finalizadas,
                COALESCE(SUM(TIMESTAMPDIFF(MINUTE, oc.data_entrada, oc.data_saida)), 0) / 60 as horas_trabalhadas
            FROM os o
            LEFT JOIN os_checkin oc ON oc.os_id = o.idOs AND oc.usuarios_id = o.tecnico_responsavel
            WHERE o.tecnico_responsavel = ?
            AND o.dataInicial BETWEEN ? AND ?
            GROUP BY DATE(o.dataInicial)
            ORDER BY data ASC
        ";

        $query = $this->db->query($sql, [$tecnico_id, $data_inicio, $data_fim]);
        return $query ? $query->result() : [];
    }

    /**
     * Atividades detalhadas de um técnico
     */
    public function getAtividadesDetalhadas($tecnico_id, $data_inicio, $data_fim)
    {
        $this->db->select([
            'o.idOs',
            'o.status',
            'o.dataInicial',
            'o.dataFinal',
            'o.valorTotal',
            'c.nomeCliente',
            'oc.data_entrada',
            'oc.data_saida',
            'TIMESTAMPDIFF(MINUTE, oc.data_entrada, oc.data_saida) as duracao_minutos'
        ]);
        $this->db->from('os o');
        $this->db->join('clientes c', 'c.idClientes = o.clientes_id', 'left');
        $this->db->join('os_checkin oc', 'oc.os_id = o.idOs AND oc.usuarios_id = o.tecnico_responsavel', 'left');
        $this->db->where('o.tecnico_responsavel', $tecnico_id);
        $this->db->where('o.dataInicial >=', $data_inicio);
        $this->db->where('o.dataInicial <=', $data_fim);
        $this->db->order_by('o.dataInicial', 'DESC');

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    /**
     * Eficiência dos técnicos (API para gráficos)
     */
    public function getEficienciaTecnicos($data_inicio, $data_fim)
    {
        $sql = "
            SELECT
                u.nome as tecnico,
                COUNT(o.idOs) as total_os,
                ROUND(
                    COUNT(CASE WHEN o.status IN ('Finalizado', 'Faturado') THEN 1 END) * 100.0 / COUNT(o.idOs),
                    2
                ) as eficiencia
            FROM usuarios u
            LEFT JOIN os o ON o.tecnico_responsavel = u.idUsuarios
                AND o.dataInicial BETWEEN ? AND ?
            WHERE u.situacao = 1
            GROUP BY u.idUsuarios, u.nome
            HAVING total_os > 0
            ORDER BY eficiencia DESC
        ";

        $query = $this->db->query($sql, [$data_inicio, $data_fim]);
        return $query ? $query->result() : [];
    }

    /**
     * Timeline de atendimentos para gráfico
     */
    public function getTimelineAtendimentos($data_inicio, $data_fim)
    {
        $sql = "
            SELECT
                DATE(data_entrada) as data,
                HOUR(data_entrada) as hora,
                COUNT(*) as atendimentos
            FROM os_checkin
            WHERE data_entrada BETWEEN ? AND ?
            GROUP BY DATE(data_entrada), HOUR(data_entrada)
            ORDER BY data, hora
        ";

        $query = $this->db->query($sql, [$data_inicio . ' 00:00:00', $data_fim . ' 23:59:59']);
        return $query ? $query->result() : [];
    }
}
