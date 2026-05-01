<?php
/**
 * RelatoriosController - API v2
 * Gera relatorios em formato texto, JSON ou PDF (via mPDF).
 *
 * Endpoints:
 *   GET  /api/v2/relatorios/{tipo}
 *   POST /api/v2/relatorios/exportar  -> Gera PDF e retorna URL temporaria
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class RelatoriosController extends BaseController
{
    protected Agente_ia_relatorios_model $relModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('agente_ia_relatorios_model', 'relModel');
    }

    // ========================================================================
    // LISTAR / CONSULTAR RELATORIO (JSON)
    // ========================================================================

    /**
     * GET /api/v2/relatorios/{tipo}
     *
     * Tipos validos: os_periodo | historico_cliente | resumo_financeiro |
     *                vendas | estoque | produtividade_tecnico | os_hoje | os_mes
     *
     * Query params:
     *   dt_inicio, dt_fim, tecnico_id, cliente_id, status, texto (0 ou 1)
     */
    public function index_get(string $tipo = '')
    {
        $tipo   = $tipo ?: $this->input->get('tipo');
        $texto  = (int) ($this->input->get('texto') ?: 0);

        if (!$tipo) {
            return $this->error('Informe o tipo de relatorio.', 400, [
                'tipos_validos' => ['os_periodo','historico_cliente','resumo_financeiro',
                            'vendas','estoque','produtividade_tecnico','os_hoje','os_mes']
            ]);
        }

        $resultado = $this->gerarDados($tipo);
        if (!$resultado) {
            return $this->error('Parametros insuficientes para o tipo informado.', 400);
        }

        if ($texto) {
            $textoSintetizado = $this->sintetizarTexto($tipo, $resultado);
            return $this->success([
                'tipo'   => $tipo,
                'formato'=> 'texto',
                'texto'  => $textoSintetizado,
                'dados'  => $resultado
            ]);
        }

        return $this->success([
            'tipo'    => $tipo,
            'formato' => 'json',
            'dados'   => $resultado
        ]);
    }

    // ========================================================================
    // EXPORTAR PDF
    // ========================================================================

    /**
     * POST /api/v2/relatorios/exportar
     *
     * Body:
     *   tipo          (string, obr)
     *   formato       (string) pdf | csv — default pdf
     *   dt_inicio     (string) YYYY-MM-DD
     *   dt_fim        (string) YYYY-MM-DD
     *   tecnico_id    (int)
     *   cliente_id    (int)
     *   email         (string, opt) — envia por email se informado
     *
     * Response:
     *   success: true
     *   data:
     *     download_url   => string
     *     expires_at     => datetime
     *     filename       => string
     */
    public function exportar_post()
    {
        $tipo       = $this->input->post('tipo');
        $formato    = $this->input->post('formato') ?: 'pdf';
        $emailDest  = $this->input->post('email');

        if (!$tipo) {
            return $this->error('Campo obrigatorio: tipo', 400);
        }

        $dados = $this->gerarDados($tipo);
        if (!$dados) {
            return $this->error('Parametros insuficientes.', 400);
        }

        $filename = 'relatorio_' . $tipo . '_' . date('Ymd_His') . '.' . $formato;
        $uploadDir = FCPATH . 'assets/relatorios_temp/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filePath = $uploadDir . $filename;

        try {
            if ($formato === 'pdf') {
                $this->gerarPdfMpdf($dados, $filePath, $tipo);
            } else {
                return $this->error('Formato nao suportado. Use: pdf', 400);
            }
        } catch (\Throwable $e) {
            log_message('error', '[RelatoriosController] Erro gerando PDF: ' . $e->getMessage());
            return $this->error('Erro ao gerar relatorio: ' . $e->getMessage(), 500);
        }

        $downloadUrl = base_url('assets/relatorios_temp/' . $filename);
        $expiresAt   = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Se email informado, coloca na fila de email
        if ($emailDest) {
            $this->enfileirarEmailRelatorio($emailDest, $dados, $filePath, $downloadUrl);
        }

        return $this->success([
            'download_url' => $downloadUrl,
            'expires_at'   => $expiresAt,
            'filename'     => $filename,
            'tamanho_bytes'=> filesize($filePath),
            'enviado_email'=> (bool) $emailDest,
        ]);
    }

    // ========================================================================
    // GERADORES DE DADOS
    // ========================================================================

    private function gerarDados(string $tipo): ?array
    {
        $dtInicio = $this->input->get_post('dt_inicio') ?: date('Y-m-01');
        $dtFim    = $this->input->get_post('dt_fim')    ?: date('Y-m-t');
        $tecnico  = (int) ($this->input->get_post('tecnico_id') ?: 0);
        $cliente  = (int) ($this->input->get_post('cliente_id') ?: 0);
        $status   = $this->input->get_post('status') ?: null;

        switch ($tipo) {
            case 'os_periodo':
            case 'os_mes':
            case 'os_hoje':
                if ($tipo === 'os_hoje') {
                    $dtInicio = $dtFim = date('Y-m-d');
                }
                return $this->relModel->osPeriodo($dtInicio, $dtFim, $tecnico ?: null, $cliente ?: null, $status);

            case 'historico_cliente':
                if (!$cliente) return null;
                return $this->relModel->historicoCliente($cliente);

            case 'resumo_financeiro':
                return $this->relModel->resumoFinanceiro($dtInicio, $dtFim);

            case 'vendas':
                return $this->relModel->resumoVendas($dtInicio, $dtFim);

            case 'estoque':
                return $this->relModel->estoqueAtual();

            case 'produtividade_tecnico':
                if (!$tecnico) return null;
                return $this->relModel->produtividadeTecnico($tecnico, $dtInicio, $dtFim);

            default:
                return null;
        }
    }

    // ========================================================================
    // PDF (mPDF)
    // ========================================================================

    private function gerarPdfMpdf(array $dados, string $caminho, string $tipo): void
    {
        if (!class_exists('Mpdf\Mpdf')) {
            require_once FCPATH . 'vendor/autoload.php';
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $html = $this->montarHtmlRelatorio($dados, $tipo);
        $mpdf->WriteHTML($html);
        $mpdf->Output($caminho, 'F');
    }

    private function montarHtmlRelatorio(array $dados, string $tipo): string
    {
        $titulo = $this->nomeTitulo($tipo);
        $r      = $dados['resumo'] ?? [];

        $html = "<!DOCTYPE html><html><head><meta charset='UTF-8'>
            <style>
                body{font-family:Arial,sans-serif;font-size:12px;color:#333}
                h1{font-size:18px;border-bottom:2px solid #007bff;padding-bottom:6px}
                h2{font-size:14px;color:#007bff;margin-top:20px}
                table{width:100%;border-collapse:collapse;margin-top:10px}
                th{background:#007bff;color:#fff;padding:6px;text-align:left;font-size:11px}
                td{padding:5px;border-bottom:1px solid #ddd;font-size:11px}
                .resumo{background:#f8f9fa;padding:10px;border-radius:4px;margin-top:10px}
                .resumo p{margin:3px 0;font-size:12px}
                .footer{margin-top:30px;font-size:10px;color:#888;text-align:center;border-top:1px solid #ddd;padding-top:8px}
            </style></head><body>";

        $html .= "<h1>" . htmlspecialchars($titulo) . "</h1>";
        $html .= "<p>Gerado em " . date('d/m/Y H:i') . "</p>";

        // Resumo
        $html .= "<div class='resumo'><h2>Resumo</h2>";
        foreach ($r as $k => $v) {
            if (is_array($v)) continue;
            $label = str_replace('_', ' ', $k);
            $html .= "<p><strong>" . ucfirst($label) . ":</strong> " . htmlspecialchars((string)$v) . "</p>";
        }
        $html .= "</div>";

        // Tabela de dados
        $items = $dados['items'] ?? [];
        if (!empty($items)) {
            $html .= "<h2>Detalhes</h2><table><thead><tr>";
            $first = reset($items);
            foreach (array_keys($first) as $col) {
                $html .= "<th>" . ucfirst(str_replace('_', ' ', $col)) . "</th>";
            }
            $html .= "</tr></thead><tbody>";
            foreach ($items as $row) {
                $html .= "<tr>";
                foreach ($row as $val) {
                    $html .= "<td>" . htmlspecialchars((string)($val ?? '-')) . "</td>";
                }
                $html .= "</tr>";
            }
            $html .= "</tbody></table>";
        }

        $html .= "<div class='footer'>Gerado via MapOS Agente IA - " . ($dados['periodo']['inicio'] ?? '') . " ate " . ($dados['periodo']['fim'] ?? '') . "</div>";
        $html .= "</body></html>";

        return $html;
    }

    // ========================================================================
    // TEXTO SINTETIZADO (para WhatsApp / resumo textual)
    // ========================================================================

    private function sintetizarTexto(string $tipo, array $dados): string
    {
        $r = $dados['resumo'] ?? [];
        switch ($tipo) {
            case 'os_hoje':
            case 'os_periodo':
            case 'os_mes':
                return sprintf(
                    "Relatorio de OS (%s a %s):\nTotal de OS: %d\nValor total: R$ %s\nPor status: %s",
                    $dados['periodo']['inicio'] ?? '-',
                    $dados['periodo']['fim'] ?? '-',
                    $r['total_os'] ?? 0,
                    number_format($r['total_valor'] ?? 0, 2, ',', '.'),
                    json_encode($r['por_status'] ?? [])
                );
            case 'resumo_financeiro':
                return sprintf(
                    "Resumo Financeiro:\nTotal: R$ %s\nA receber: R$ %s\nRecebido: R$ %s",
                    number_format($r['total_valor'] ?? 0, 2, ',', '.'),
                    number_format($r['total_a_receber'] ?? 0, 2, ',', '.'),
                    number_format($r['total_recebido'] ?? 0, 2, ',', '.')
                );
            case 'vendas':
                return sprintf(
                    "Vendas no periodo:\nQuantidade: %d\nTotal: R$ %s\nTicket medio: R$ %s",
                    $r['total_vendas'] ?? 0,
                    number_format($r['total_valor'] ?? 0, 2, ',', '.'),
                    number_format($r['ticket_medio'] ?? 0, 2, ',', '.')
                );
            case 'estoque':
                return sprintf(
                    "Estoque atual:\nTotal produtos: %d\nBaixo do minimo: %d\nValor em estoque: R$ %s",
                    $r['total_produtos'] ?? 0,
                    $r['baixo_minimo'] ?? 0,
                    number_format($r['valor_estoque'] ?? 0, 2, ',', '.')
                );
            case 'historico_cliente':
                return sprintf(
                    "Historico do Cliente %s:\nTotal OS: %d | Finalizadas: %d\nDivida atual: R$ %s",
                    ($dados['cliente']['nomeCliente'] ?? ''),
                    $r['total_os'] ?? 0,
                    $r['os_finalizadas'] ?? 0,
                    number_format($r['total_divida'] ?? 0, 2, ',', '.')
                );
            default:
                return 'Relatorio gerado. Veja o PDF para mais detalhes.';
        }
    }

    // ========================================================================
    // UTIL
    // ========================================================================

    private function nomeTitulo(string $tipo): string
    {
        $map = [
            'os_periodo'           => 'Relatorio de Ordens de Servico',
            'os_hoje'              => 'Relatorio de OS do Dia',
            'os_mes'               => 'Relatorio de OS do Mes',
            'historico_cliente'    => 'Historico do Cliente',
            'resumo_financeiro'    => 'Resumo Financeiro',
            'vendas'               => 'Relatorio de Vendas',
            'estoque'              => 'Relatorio de Estoque',
            'produtividade_tecnico'=> 'Produtividade do Tecnico',
        ];
        return $map[$tipo] ?? 'Relatorio';
    }

    private function enfileirarEmailRelatorio(string $email, array $dados, string $filePath, string $downloadUrl): void
    {
        // Insere na fila de emails do MapOS se existir a tabela
        try {
            $this->db->insert('email_queue', [
                'email'      => $email,
                'assunto'    => 'Relatorio: ' . ($dados['tipo'] ?? ''),
                'mensagem'   => nl2br($this->sintetizarTexto($dados['tipo'] ?? 'default', $dados) . "</br></br>Download: " . $downloadUrl),
                'status'     => 'pendente',
                'attachments'=> $filePath,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enfileirar email de relatorio: ' . $e->getMessage());
        }
    }
}
