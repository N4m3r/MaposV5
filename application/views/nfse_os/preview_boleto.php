<?php
/**
 * Preview de Boleto — Design Moderno & Clean
 * Layout otimizado para A4 (mPDF)
 */

if (!isset($boleto) || !isset($os)) {
    echo '<h2>Dados do boleto nao disponiveis</h2>';
    return;
}

// ========== HELPERS ==========
function fmtDoc($doc) {
    $doc = preg_replace('/\D/', '', $doc);
    if (strlen($doc) == 14) {
        return substr($doc, 0, 2) . '.' . substr($doc, 2, 3) . '.' . substr($doc, 5, 3) . '/' . substr($doc, 8, 4) . '-' . substr($doc, 12, 2);
    }
    if (strlen($doc) == 11) {
        return substr($doc, 0, 3) . '.' . substr($doc, 3, 3) . '.' . substr($doc, 6, 3) . '-' . substr($doc, 9, 2);
    }
    return $doc;
}
function fmtMoney($v) {
    return 'R$ ' . number_format(floatval($v), 2, ',', '.');
}

// ========== DADOS CEDENTE ==========
$cedenteNome = htmlspecialchars(($emitente->nome ?? $emitente->razaosocial ?? $emitente->nomeEmpresa ?? 'Cedente'), ENT_QUOTES, 'UTF-8');
$cedenteCnpj = fmtDoc($emitente->cnpj ?? '');
$cedenteEndereco = htmlspecialchars(
    trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '')),
    ENT_QUOTES, 'UTF-8'
);
$cedenteTelefone = htmlspecialchars($emitente->telefone ?? '', ENT_QUOTES, 'UTF-8');

// ========== DADOS SACADO ==========
$sacadoNome = htmlspecialchars($boleto->sacado_nome ?? ($os->nomeCliente ?? 'Sacado'), ENT_QUOTES, 'UTF-8');
$sacadoDoc = fmtDoc($boleto->sacado_documento ?? ($os->documento ?? ''));
$sacadoEndereco = htmlspecialchars(
    trim(($os->rua ?? '') . ', ' . ($os->numero ?? '') . ' - ' . ($os->bairro ?? '') . ', ' . ($os->cidade ?? '') . '/' . ($os->estado ?? '') . ' - CEP: ' . ($os->cep ?? '')),
    ENT_QUOTES, 'UTF-8'
);

// ========== DATAS / VALORES ==========
$vencimento = $boleto->data_vencimento ? date('d/m/Y', strtotime($boleto->data_vencimento)) : '---';
$emissao    = $boleto->data_emissao    ? date('d/m/Y', strtotime($boleto->data_emissao))    : '---';
$valorOriginal = floatval($boleto->valor_original ?? 0);
$valorLiquido  = floatval($boleto->valor_liquido  ?? $valorOriginal);

// ========== NFSe ==========
$numeroNfse = $nfse->numero_nfse ?? ($nfse->numero ?? '') ?: '';
$dataNfse   = !empty($nfse->data_emissao) ? date('d/m/Y', strtotime($nfse->data_emissao)) : '';
$chaveNfse  = $nfse->chave_acesso ?? '';
$valorNfse  = floatval($nfse->valor_servicos ?? $nfse->valor_total ?? 0);
$osNumero   = $os->idOs ?? $boleto->os_id ?? '';

// ========== LOGO ==========
$logoHtml = '';
if (!empty($logo_url)) {
    $logoHtml = '<img src="' . htmlspecialchars($logo_url, ENT_QUOTES, 'UTF-8') . '" style="max-height:50px;max-width:140px;display:block;" alt="Logo">';
} elseif (!empty($emitente->url_logo)) {
    $logoHtml = '<img src="' . htmlspecialchars($emitente->url_logo, ENT_QUOTES, 'UTF-8') . '" style="max-height:50px;max-width:140px;display:block;" alt="Logo">';
}

$instrucoes = nl2br(htmlspecialchars($boleto->instrucoes ?? 'Pagavel em qualquer banco ate o vencimento.', ENT_QUOTES, 'UTF-8'));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Boleto OS #<?= $osNumero ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #2c2c3a;
            background: #f4f6f9;
            padding: 0;
        }
        .page {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 18px 22px;
            position: relative;
        }

        /* Cabecalho */
        .header {
            background: #1a237e;
            background: linear-gradient(135deg, #0d1642 0%, #1a237e 40%, #3949ab 100%);
            border-radius: 8px;
            padding: 16px 20px;
            color: #fff;
            margin-bottom: 14px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; padding: 0; }
        .header-logo { width: 1%; padding-right: 14px; }
        .header-logo img { max-height: 48px; max-width: 130px; display: block; }
        .header-title {
            font-size: 18pt;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-sub {
            font-size: 8.5pt;
            opacity: 0.85;
            margin-top: 2px;
        }
        .header-badge {
            text-align: right;
        }
        .badge-preview {
            display: inline-block;
            background: #fc9d0f;
            color: #fff;
            font-size: 9pt;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 1px;
        }

        /* Cards */
        .card {
            background: #fff;
            border: 1px solid #e2e5ec;
            border-radius: 8px;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .card-header {
            background: #f8f9fc;
            border-bottom: 1px solid #e2e5ec;
            padding: 8px 14px;
            font-size: 8.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #1a237e;
            letter-spacing: 0.6px;
        }
        .card-body { padding: 12px 14px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td {
            padding: 3px 0;
            font-size: 9.5pt;
            vertical-align: top;
        }
        .info-table td.label {
            color: #6b6f80;
            width: 30%;
            font-size: 8.5pt;
            white-space: nowrap;
        }
        .info-table td.value {
            color: #2c2c3a;
            font-weight: 600;
            padding-left: 8px;
        }

        /* Valores */
        .values-row { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .values-row td { width: 33.33%; padding: 0 6px; vertical-align: top; }
        .values-row td:first-child { padding-left: 0; }
        .values-row td:last-child { padding-right: 0; }
        .value-box {
            background: #fff;
            border: 1px solid #e2e5ec;
            border-radius: 8px;
            text-align: center;
            padding: 14px 8px;
        }
        .value-box.highlight {
            background: #1a237e;
            border-color: #1a237e;
            color: #fff;
        }
        .value-box .lbl {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            color: #6b6f80;
        }
        .value-box.highlight .lbl { color: rgba(255,255,255,0.8); }
        .value-box .val {
            font-size: 16pt;
            font-weight: 700;
        }
        .value-box.highlight .val { color: #fff; }

        /* NFSe Box */
        .nfse-box {
            background: #eef2ff;
            border: 1px dashed #1a237e;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }
        .nfse-box-title {
            font-size: 9pt;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 6px;
        }
        .nfse-table { width: 100%; border-collapse: collapse; }
        .nfse-table td {
            font-size: 9pt;
            padding: 2px 0;
            color: #3a3a4a;
        }
        .nfse-table td.nfse-lbl {
            color: #6b6f80;
            width: 18%;
            font-size: 8.5pt;
        }
        .nfse-chave {
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            color: #1a237e;
            word-break: break-all;
        }

        /* Instrucoes */
        .instrucoes-box {
            background: #fff;
            border: 1px solid #e2e5ec;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }
        .instrucoes-box .title {
            font-size: 8.5pt;
            font-weight: 700;
            color: #1a237e;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .instrucoes-box .text {
            font-size: 9.5pt;
            color: #2c2c3a;
            line-height: 1.5;
        }

        /* Rodape / Codigo de barras */
        .footer-area {
            background: #f8f9fc;
            border: 1px solid #e2e5ec;
            border-radius: 8px;
            padding: 14px;
            text-align: center;
            margin-bottom: 12px;
        }
        .barcode-placeholder {
            display: inline-block;
            padding: 10px 20px;
            border: 1px dashed #c0c4d0;
            border-radius: 4px;
            color: #8a8fa5;
            font-size: 9pt;
        }
        .footer-note {
            font-size: 7.5pt;
            color: #8a8fa5;
            margin-top: 6px;
        }

        /* Assinatura */
        .sign-area {
            border-top: 1px solid #e2e5ec;
            padding-top: 10px;
            margin-top: 4px;
        }
        .sign-line {
            border-bottom: 1px solid #2c2c3a;
            height: 28px;
            margin-bottom: 2px;
        }
        .sign-label {
            font-size: 7.5pt;
            color: #6b6f80;
            text-align: center;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 42%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 72pt;
            color: rgba(200, 0, 0, 0.06);
            font-weight: 900;
            pointer-events: none;
            z-index: 0;
            letter-spacing: 6px;
        }

        @media print {
            body { background: #fff; }
            .page { padding: 12px 16px; }
        }
    </style>
</head>
<body>

<div class="page">
    <?php if ($is_preview): ?>
    <div class="watermark">PREVIEW</div>
    <?php endif; ?>

    <!-- CABECALHO -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <?= $logoHtml ?: '<div style="font-size:16pt;font-weight:700;line-height:1.1;">' . htmlspecialchars($cedenteNome) . '</div>' ?>
                </td>
                <td>
                    <div class="header-title">Boleto de Cobranca</div>
                    <div class="header-sub">OS #<?= $osNumero ?> &nbsp;|&nbsp; Emissao: <?= $emissao ?></div>
                </td>
                <td class="header-badge">
                    <?php if ($is_preview): ?>
                        <span class="badge-preview">PREVIEW</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- VALORES DESTACADOS -->
    <table class="values-row">
        <tr>
            <td>
                <div class="value-box">
                    <div class="lbl">Vencimento</div>
                    <div class="val" style="color:#1a237e;"><?= $vencimento ?></div>
                </div>
            </td>
            <td>
                <div class="value-box">
                    <div class="lbl">Valor Original</div>
                    <div class="val" style="color:#2c2c3a;"><?= fmtMoney($valorOriginal) ?></div>
                </div>
            </td>
            <td>
                <div class="value-box highlight">
                    <div class="lbl">Valor a Pagar</div>
                    <div class="val"><?= fmtMoney($valorLiquido) ?></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- CEDENTE -->
    <div class="card">
        <div class="card-header"><i class="fas fa-building"></i> Cedente</div>
        <div class="card-body">
            <table class="info-table">
                <tr>
                    <td class="label">Razao Social / Nome:</td>
                    <td class="value"><?= $cedenteNome ?></td>
                    <td class="label">CNPJ:</td>
                    <td class="value"><?= $cedenteCnpj ?: '---' ?></td>
                </tr>
                <tr>
                    <td class="label">Endereco:</td>
                    <td class="value"><?= $cedenteEndereco ?: '---' ?></td>
                    <td class="label">Telefone:</td>
                    <td class="value"><?= $cedenteTelefone ?: '---' ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- SACADO -->
    <div class="card">
        <div class="card-header"><i class="fas fa-user"></i> Sacado</div>
        <div class="card-body">
            <table class="info-table">
                <tr>
                    <td class="label">Nome:</td>
                    <td class="value"><?= $sacadoNome ?></td>
                    <td class="label">CPF/CNPJ:</td>
                    <td class="value"><?= $sacadoDoc ?: '---' ?></td>
                </tr>
                <tr>
                    <td class="label">Endereco:</td>
                    <td class="value" colspan="3"><?= $sacadoEndereco ?: '---' ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- NFSe VINCULADA -->
    <?php if ($numeroNfse): ?>
    <div class="nfse-box">
        <div class="nfse-box-title"><i class="fas fa-file-invoice"></i> NFS-e Vinculada</div>
        <table class="nfse-table">
            <tr>
                <td class="nfse-lbl">Numero:</td>
                <td><strong><?= htmlspecialchars($numeroNfse) ?></strong></td>
                <td class="nfse-lbl">Data Emissao:</td>
                <td><?= $dataNfse ?: '---' ?></td>
                <td class="nfse-lbl">OS:</td>
                <td>#<?= $osNumero ?></td>
            </tr>
            <?php if ($chaveNfse): ?>
            <tr>
                <td class="nfse-lbl">Chave Acesso:</td>
                <td colspan="5" class="nfse-chave"><?= htmlspecialchars($chaveNfse) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>

    <!-- INSTRUCOES -->
    <div class="instrucoes-box">
        <div class="title">Instrucoes</div>
        <div class="text"><?= $instrucoes ?></div>
    </div>

    <!-- CODIGO DE BARRAS / RODAPE -->
    <div class="footer-area">
        <?php if (!empty($boleto->codigo_barras)): ?>
            <div style="font-family:monospace;font-size:11pt;letter-spacing:2px;font-weight:700;color:#2c2c3a;margin-bottom:6px;">
                <?= preg_replace('/(\d{11})/', '$1 ', preg_replace('/\D/', '', $boleto->codigo_barras)) ?>
            </div>
        <?php else: ?>
            <div class="barcode-placeholder">
                [ Codigo de barras sera gerado pelo gateway bancario apos emissao ]
            </div>
        <?php endif; ?>
        <div class="footer-note">
            Este documento e um preview/simulacao. Para efetivar o pagamento, gere o boleto oficial pelo sistema.
        </div>
    </div>

    <!-- ASSINATURA -->
    <table style="width:100%;margin-top:4px;">
        <tr>
            <td style="width:48%;padding-right:2%;">
                <div class="sign-line"></div>
                <div class="sign-label">Autenticacao do Cedente</div>
            </td>
            <td style="width:48%;padding-left:2%;">
                <div class="sign-line"></div>
                <div class="sign-label">Assinatura do Sacado</div>
            </td>
        </tr>
    </table>

    <!-- Rodape legal -->
    <div style="text-align:center;margin-top:14px;font-size:7pt;color:#a0a4b0;">
        Documento gerado em <?= date('d/m/Y H:i') ?> &mdash; <?= htmlspecialchars($cedenteNome) ?>
    </div>
</div>

</body>
</html>
