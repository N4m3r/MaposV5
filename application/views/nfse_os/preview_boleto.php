<?php
/**
 * Preview de Boleto — Layout Novo
 * Dados reais da OS, NFSe e Emitente
 */

if (empty($boleto) || empty($os)) {
    echo '<h2 style="text-align:center;padding:40px;">Dados do boleto nao disponiveis</h2>';
    return;
}

// ===== HELPERS =====
function fmtDoc($doc) {
    $doc = preg_replace('/\D/', '', $doc);
    if (strlen($doc) == 14) return substr($doc,0,2).'.'.substr($doc,2,3).'.'.substr($doc,5,3).'/'.substr($doc,8,4).'-'.substr($doc,12,2);
    if (strlen($doc) == 11) return substr($doc,0,3).'.'.substr($doc,3,3).'.'.substr($doc,6,3).'-'.substr($doc,9,2);
    return $doc;
}
function fmtMoney($v) {
    return 'R$ ' . number_format(floatval($v), 2, ',', '.');
}

// ===== EMITENTE =====
$cedenteNome = htmlspecialchars($emitente->nome ?? $emitente->razaosocial ?? $emitente->nomeEmpresa ?? 'Cedente', ENT_QUOTES, 'UTF-8');
$cedenteCnpj = fmtDoc($emitente->cnpj ?? '');
$cedenteEndereco = htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '')), ENT_QUOTES, 'UTF-8');
$cedenteTelefone = htmlspecialchars($emitente->telefone ?? '', ENT_QUOTES, 'UTF-8');
$cedenteEmail = htmlspecialchars($emitente->email ?? '', ENT_QUOTES, 'UTF-8');

// ===== SACADO (OS) =====
$sacadoNome = htmlspecialchars($boleto->sacado_nome ?? ($os->nomeCliente ?? 'Sacado'), ENT_QUOTES, 'UTF-8');
$sacadoDoc = fmtDoc($boleto->sacado_documento ?? ($os->documento ?? ''));
$sacadoEndereco = htmlspecialchars(trim(($os->rua ?? '') . ', ' . ($os->numero ?? '') . ' - ' . ($os->bairro ?? '') . ', ' . ($os->cidade ?? '') . '/' . ($os->estado ?? '') . ' - CEP: ' . ($os->cep ?? '')), ENT_QUOTES, 'UTF-8');

// ===== BOLETO =====
$vencimento = !empty($boleto->data_vencimento) ? date('d/m/Y', strtotime($boleto->data_vencimento)) : '---';
$emissao = !empty($boleto->data_emissao) ? date('d/m/Y', strtotime($boleto->data_emissao)) : '---';
$valorOriginal = floatval($boleto->valor_original ?? 0);
$valorLiquido = floatval($boleto->valor_liquido ?? $valorOriginal);
$instrucoesTexto = nl2br(htmlspecialchars($boleto->instrucoes ?? 'Pagavel em qualquer banco ate o vencimento.', ENT_QUOTES, 'UTF-8'));
$osNumero = $os->idOs ?? $boleto->os_id ?? '';
$nossoNumero = $boleto->nosso_numero ?? '---';

// ===== NFSe =====
$isImportada = false;
$numeroNfse = '';
$dataNfse = '';
$chaveNfse = '';
$valorNfse = 0;
$valorImpostosNfse = 0;
$descServico = '';

if (!empty($nfse)) {
    $isImportada = !empty($nfse->is_importada) || !empty($nfse->dados_xml);
    $numeroNfse = $nfse->numero_nfse ?? ($nfse->numero ?? '');
    $dataNfse = !empty($nfse->data_emissao) ? date('d/m/Y', strtotime($nfse->data_emissao)) : '';
    $chaveNfse = $nfse->chave_acesso ?? '';
    $valorNfse = floatval($nfse->valor_servicos ?? $nfse->valor_total ?? 0);
    $valorImpostosNfse = floatval($nfse->valor_total_impostos ?? $nfse->valor_impostos ?? 0);

    // Descricao do servico: prioridade para a extraida do XML, depois do campo da NFSe
    if (!empty($descricao_servico)) {
        $descServico = $descricao_servico;
    } elseif (!empty($nfse->descricao_servico)) {
        $descServico = $nfse->descricao_servico;
    }
}

// ===== LOGO (caminho absoluto no servidor para mPDF) =====
$logoHtml = '';
if (!empty($logo_path) && file_exists($logo_path)) {
    $logoHtml = '<img src="' . $logo_path . '" style="max-height:55px;max-width:160px;display:block;" alt="Logo">';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>OS #<?= $osNumero ?></title>
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:Helvetica,Arial,sans-serif; font-size:10pt; color:#2c2c3a; background:#eef1f7; }
.page { width:100%; max-width:210mm; margin:0 auto; background:#fff; padding:16px 20px; }

/* Topo */
.topo { background:#16248c; background:linear-gradient(135deg,#0d1642 0%,#1a237e 50%,#3949ab 100%); border-radius:6px; padding:14px 18px; color:#fff; margin-bottom:12px; }
.topo-table { width:100%; border-collapse:collapse; }
.topo-table td { vertical-align:middle; padding:0; }
.topo-logo { width:1%; padding-right:12px; white-space:nowrap; }
.topo-logo img { max-height:50px; max-width:150px; display:block; }
.topo-emitente { font-size:14pt; font-weight:700; letter-spacing:0.3px; }
.topo-info { font-size:8.5pt; opacity:0.9; margin-top:2px; }

/* Valores */
.valores-table { width:100%; border-collapse:collapse; margin-bottom:12px; }
.valores-table td { width:33.33%; padding:0 5px; vertical-align:top; }
.valores-table td:first-child { padding-left:0; }
.valores-table td:last-child { padding-right:0; }
.val-box { background:#fff; border:1px solid #d0d4e0; border-radius:6px; text-align:center; padding:12px 6px; }
.val-box.destaque { background:#1a237e; border-color:#1a237e; color:#fff; }
.val-box .rotulo { font-size:7.5pt; text-transform:uppercase; letter-spacing:0.5px; color:#6b6f80; margin-bottom:4px; }
.val-box.destaque .rotulo { color:rgba(255,255,255,0.85); }
.val-box .montante { font-size:15pt; font-weight:700; }

/* Secao */
.secao { border:1px solid #d0d4e0; border-radius:6px; margin-bottom:10px; overflow:hidden; }
.secao-titulo { background:#f4f6fb; border-bottom:1px solid #d0d4e0; padding:6px 12px; font-size:8pt; font-weight:700; text-transform:uppercase; color:#1a237e; letter-spacing:0.5px; }
.secao-corpo { padding:10px 12px; }
.dados-table { width:100%; border-collapse:collapse; }
.dados-table td { padding:2px 0; font-size:9.5pt; vertical-align:top; }
.dados-table td.etq { color:#6b6f80; width:22%; font-size:8.5pt; white-space:nowrap; padding-right:8px; }
.dados-table td.info { color:#2c2c3a; font-weight:600; }

/* NFSe */
.nfse-secao { background:#f0f3ff; border:1px dashed #1a237e; border-radius:6px; padding:10px 12px; margin-bottom:10px; }
.nfse-titulo { font-size:9pt; font-weight:700; color:#1a237e; margin-bottom:6px; }

/* Instrucoes */
.instr-box { border:1px solid #d0d4e0; border-radius:6px; padding:10px 12px; margin-bottom:10px; }
.instr-titulo { font-size:8pt; font-weight:700; color:#1a237e; text-transform:uppercase; margin-bottom:4px; }
.instr-texto { font-size:9.5pt; color:#2c2c3a; line-height:1.5; }

/* Rodape */
.rodape { background:#f4f6fb; border:1px solid #d0d4e0; border-radius:6px; padding:12px; text-align:center; margin-bottom:10px; }
.codbarra-placeholder { display:inline-block; padding:8px 16px; border:1px dashed #b0b4c0; border-radius:4px; color:#8a8fa5; font-size:8.5pt; }
.rodape-aviso { font-size:7.5pt; color:#8a8fa5; margin-top:4px; }

/* Assinatura */
.assin-table { width:100%; margin-top:4px; }
.assin-table td { width:48%; padding:0 4px; vertical-align:top; }
.assin-linha { border-bottom:1px solid #2c2c3a; height:26px; margin-bottom:2px; }
.assin-rotulo { font-size:7.5pt; color:#6b6f80; text-align:center; }

/* Legal */
.legal { text-align:center; margin-top:10px; font-size:7pt; color:#a0a4b0; }

@media print { body { background:#fff; } .page { padding:12px 16px; } }
</style>
</head>
<body>

<div class="page">

<!-- TOPO -->
<div class="topo">
    <table class="topo-table">
        <tr>
            <td class="topo-logo">
                <?= $logoHtml ?: '<div style="font-size:13pt;font-weight:700;">' . $cedenteNome . '</div>' ?>
            </td>
            <td>
                <div class="topo-emitente"><?= $cedenteNome ?></div>
                <div class="topo-info">OS #<?= $osNumero ?> &nbsp;|&nbsp; Emissao: <?= $emissao ?> &nbsp;|&nbsp; Vencimento: <?= $vencimento ?></div>
            </td>
        </tr>
    </table>
</div>

<!-- VALORES -->
<table class="valores-table">
    <tr>
        <td>
            <div class="val-box">
                <div class="rotulo">Vencimento</div>
                <div class="montante" style="color:#1a237e;"><?= $vencimento ?></div>
            </div>
        </td>
        <td>
            <div class="val-box">
                <div class="rotulo">Valor Original</div>
                <div class="montante" style="color:#2c2c3a;"><?= fmtMoney($valorOriginal) ?></div>
            </div>
        </td>
        <td>
            <div class="val-box destaque">
                <div class="rotulo">Valor a Pagar</div>
                <div class="montante"><?= fmtMoney($valorLiquido) ?></div>
            </div>
        </td>
    </tr>
</table>

<!-- CEDENTE -->
<div class="secao">
    <div class="secao-titulo">Cedente</div>
    <div class="secao-corpo">
        <table class="dados-table">
            <tr><td class="etq">Nome/Razao Social:</td><td class="info"><?= $cedenteNome ?></td><td class="etq">CNPJ:</td><td class="info"><?= $cedenteCnpj ?: '---' ?></td></tr>
            <tr><td class="etq">Endereco:</td><td class="info"><?= $cedenteEndereco ?: '---' ?></td><td class="etq">Telefone:</td><td class="info"><?= $cedenteTelefone ?: '---' ?></td></tr>
        </table>
    </div>
</div>

<!-- SACADO -->
<div class="secao">
    <div class="secao-titulo">Sacado</div>
    <div class="secao-corpo">
        <table class="dados-table">
            <tr><td class="etq">Nome:</td><td class="info"><?= $sacadoNome ?></td><td class="etq">CPF/CNPJ:</td><td class="info"><?= $sacadoDoc ?: '---' ?></td></tr>
            <tr><td class="etq">Endereco:</td><td class="info" colspan="3"><?= $sacadoEndereco ?: '---' ?></td></tr>
        </table>
    </div>
</div>

<!-- NFSe -->
<?php if (!empty($numeroNfse)): ?>
<div class="nfse-secao">
    <div class="nfse-titulo">NFS-e Vinculada <?= $isImportada ? '(Importada)' : '' ?></div>
    <table class="dados-table">
        <tr>
            <td class="etq">Numero:</td><td class="info"><?= htmlspecialchars($numeroNfse) ?></td>
            <td class="etq">Data Emissao:</td><td class="info"><?= $dataNfse ?: '---' ?></td>
            <td class="etq">OS:</td><td class="info">#<?= $osNumero ?></td>
        </tr>
        <?php if ($chaveNfse): ?>
        <tr>
            <td class="etq">Chave:</td><td class="info" colspan="5" style="font-family:monospace;font-size:8pt;color:#1a237e;"><?= htmlspecialchars($chaveNfse) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="etq">Valor Servicos:</td><td class="info"><?= fmtMoney($valorNfse) ?></td>
            <td class="etq">Impostos:</td><td class="info"><?= fmtMoney($valorImpostosNfse) ?></td>
            <td class="etq">Liquido:</td><td class="info"><?= fmtMoney($valorNfse - $valorImpostosNfse) ?></td>
        </tr>
        <?php if (!empty($descServico)): ?>
        <tr>
            <td class="etq">Descricao:</td><td colspan="5" style="font-size:9pt;color:#3a3a4a;padding-top:4px;"><?= nl2br(htmlspecialchars($descServico)) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<?php endif; ?>

<!-- INSTRUCOES -->
<div class="instr-box">
    <div class="instr-titulo">Instrucoes</div>
    <div class="instr-texto"><?= $instrucoesTexto ?></div>
</div>

<!-- RODAPE / CODIGO DE BARRAS -->
<div class="rodape">
    <?php if (!empty($boleto->codigo_barras)): ?>
        <div style="font-family:monospace;font-size:11pt;letter-spacing:2px;font-weight:700;color:#2c2c3a;">
            <?= preg_replace('/(\d{11})/', '$1 ', preg_replace('/\D/', '', $boleto->codigo_barras)) ?>
        </div>
    <?php else: ?>
        <div class="codbarra-placeholder">[ Codigo de barras sera gerado pelo gateway bancario apos emissao ]</div>
    <?php endif; ?>
    <div class="rodape-aviso">Este documento e um preview/simulacao. Para efetivar o pagamento, gere o boleto oficial pelo sistema.</div>
</div>

<!-- ASSINATURAS -->
<table class="assin-table">
    <tr>
        <td><div class="assin-linha"></div><div class="assin-rotulo">Autenticacao do Cedente</div></td>
        <td><div class="assin-linha"></div><div class="assin-rotulo">Assinatura do Sacado</div></td>
    </tr>
</table>

<div class="legal">Documento gerado em <?= date('d/m/Y H:i') ?> &mdash; <?= $cedenteNome ?></div>

</div>
</body>
</html>
