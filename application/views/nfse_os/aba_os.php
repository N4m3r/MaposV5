<?php
/**
 * Aba de NFSe e Boleto na Visualização da OS
 * Wrapper fino — carrega sub-views para compatibilidade
 */

// Dados tributários com fallback
$tributacao = $tributacao ?? [
    'codigo_tributacao_nacional' => '010701',
    'codigo_tributacao_municipal' => '100',
    'descricao_servico' => 'Suporte técnico em informática, inclusive instalação, configuração e manutenção de programas de computação e bancos de dados.',
    'aliquota_iss' => '5.00',
];

$emitente = $emitente ?? null;
$totalServico = $totalServico ?? 0;
$totalProdutos = $totalProdutos ?? 0;
$servicos = $servicos ?? [];
$produtos = $produtos ?? [];

$valorTotalOS = floatval($totalServico) + floatval($totalProdutos);
$descontoTomador = floatval($result->valor_desconto ?? 0);
$valorServicosNFSe = $descontoTomador > 0 ? $descontoTomador : floatval($totalServico);

// Formatação para exibição (usado no dashboard/relatório)
if (!function_exists('formatarMoeda')) {
    function formatarMoeda($valor) {
        return 'R$ ' . number_format(floatval($valor), 2, ',', '.');
    }
}

if (!function_exists('formatarDocumento')) {
    function formatarDocumento($doc) {
        $doc = preg_replace('/\D/', '', $doc);
        if (strlen($doc) == 14) {
            return substr($doc, 0, 2) . '.' . substr($doc, 2, 3) . '.' . substr($doc, 5, 3) . '/' . substr($doc, 8, 4) . '-' . substr($doc, 12, 2);
        }
        if (strlen($doc) == 11) {
            return substr($doc, 0, 3) . '.' . substr($doc, 3, 3) . '.' . substr($doc, 6, 3) . '-' . substr($doc, 9, 2);
        }
        return $doc;
    }
}

// Variáveis para as sub-views
$regimeTributario = $tributacao['regime'] ?? 'simples_nacional';

$nfse_vars = [
    'result' => $result,
    'emitente' => $emitente,
    'tributacao' => $tributacao,
    'totalServico' => $totalServico,
    'totalProdutos' => $totalProdutos,
    'servicos' => $servicos,
    'produtos' => $produtos,
    'nfse_atual' => $nfse_atual ?? null,
    'historico_nfse' => $historico_nfse ?? [],
    'ambiente' => $ambiente ?? 'homologacao',
    'valorServicosNFSe' => $valorServicosNFSe,
    'regimeTributario' => $regimeTributario,
];

$boleto_vars = [
    'result' => $result,
    'nfse_atual' => $nfse_atual ?? null,
    'boleto_atual' => $boleto_atual ?? null,
    'historico_boleto' => $historico_boleto ?? [],
];

$produtos_vars = [
    'result' => $result,
    'produtos' => $produtos,
    'totalProdutos' => $totalProdutos,
    'tributacao' => $tributacao,
];
?>

<!-- Aba de Documentos Fiscais (backward compatible) -->
<div class="tab-pane" id="tab-documentos-fiscais">
    <?php $this->load->view('nfse_os/nfse_content', $nfse_vars); ?>
    <?php $this->load->view('nfse_os/boleto_content', $boleto_vars); ?>
</div>

<?php $this->load->view('nfse_os/nfse_scripts'); ?>