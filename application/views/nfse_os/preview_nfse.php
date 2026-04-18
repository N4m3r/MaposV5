<?php
/**
 * Pré-visualização NFS-e em PDF
 * Template HTML para mPDF
 */

function fmtMoeda($v) {
    return 'R$ ' . number_format(floatval($v), 2, ',', '.');
}

function fmtDoc($doc) {
    $doc = preg_replace('/\D/', '', $doc);
    if (strlen($doc) == 14) {
        return substr($doc,0,2).'.'.substr($doc,2,3).'.'.substr($doc,5,3).'/'.substr($doc,8,4).'-'.substr($doc,12,2);
    }
    if (strlen($doc) == 11) {
        return substr($doc,0,3).'.'.substr($doc,3,3).'.'.substr($doc,6,3).'-'.substr($doc,9,2);
    }
    return $doc;
}

$emitente = $emitente ?? null;
$os = $os ?? null;
$tributacao = $tributacao ?? [];
$impostos = $impostos ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .documento {
            width: 100%;
            position: relative;
            border: 2px solid #333;
            padding: 0;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 60px;
            font-weight: bold;
            color: rgba(200, 50, 50, 0.06);
            white-space: nowrap;
            pointer-events: none;
            letter-spacing: 6px;
            z-index: 0;
        }

        .header {
            background: #f0f0f0;
            border-bottom: 2px solid #333;
            padding: 12px 20px;
            overflow: hidden;
        }

        .header-logo {
            float: left;
            width: 120px;
        }

        .header-logo img {
            width: 100px;
            max-height: 70px;
        }

        .header-info {
            float: right;
            text-align: right;
        }

        .header-title {
            text-align: center;
            padding: 10px 0;
        }

        .header-title h1 {
            font-size: 16px;
            margin: 0;
            color: #333;
            letter-spacing: 1px;
        }

        .header-title small {
            font-size: 10px;
            color: #d9534f;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .section {
            border-bottom: 1px solid #999;
            padding: 10px 20px;
            position: relative;
            z-index: 1;
        }

        .section:last-child {
            border-bottom: none;
        }

        .section-title {
            background: #e8e8e8;
            padding: 4px 10px;
            font-weight: bold;
            font-size: 11px;
            color: #333;
            margin-bottom: 8px;
            border-left: 3px solid #3a8abf;
        }

        .section-title.prestador {
            border-left-color: #3a8abf;
        }

        .section-title.tomador {
            border-left-color: #5cb85c;
        }

        .section-title.discriminacao {
            border-left-color: #f0ad4e;
        }

        .section-title.valores {
            border-left-color: #d9534f;
        }

        .section-title.pix-section {
            border-left-color: #32bcad;
        }

        .row {
            overflow: hidden;
            margin: 3px 0;
        }

        .col-left {
            float: left;
            width: 30%;
            font-weight: bold;
            color: #555;
        }

        .col-right {
            float: left;
            width: 70%;
            color: #333;
        }

        .col-half {
            float: left;
            width: 48%;
            margin-right: 2%;
        }

        .valores-table {
            width: 100%;
            border-collapse: collapse;
        }

        .valores-table td {
            padding: 4px 10px;
            border-bottom: 1px solid #eee;
        }

        .valores-table .label-col {
            width: 55%;
            color: #555;
        }

        .valores-table .value-col {
            width: 45%;
            text-align: right;
            font-weight: bold;
        }

        .valores-table .deducao-row .value-col {
            color: #d9534f;
        }

        .valores-table .total-row td {
            border-top: 2px solid #999;
            font-size: 12px;
        }

        .valores-table .liquido-row td {
            font-size: 14px;
            color: #2e7d32;
            background: #e8f5e9;
            border-top: 2px solid #2e7d32;
        }

        .pix-area {
            overflow: hidden;
            padding: 10px 0;
        }

        .pix-qr {
            float: left;
            width: 130px;
            text-align: center;
        }

        .pix-qr img {
            width: 120px;
            height: 120px;
        }

        .pix-info {
            float: left;
            width: 60%;
            padding-left: 15px;
        }

        .pix-info p {
            margin: 4px 0;
        }

        .pix-logo {
            width: 40px;
            vertical-align: middle;
            margin-right: 5px;
        }

        .footer {
            background: #f0f0f0;
            border-top: 2px solid #333;
            padding: 8px 20px;
            text-align: center;
            font-size: 9px;
            color: #888;
        }

        .os-info {
            float: left;
            text-align: left;
        }

        .os-date {
            float: right;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="documento">
    <!-- Marca d'água -->
    <div class="watermark">PRÉ-VISUALIZAÇÃO</div>

    <!-- Cabeçalho -->
    <div class="header">
        <?php if ($emitente && !empty($emitente->url_logo) && file_exists(convertUrlToUploadsPath($emitente->url_logo))): ?>
            <div class="header-logo">
                <img src="<?= $emitente->url_logo ?>" alt="<?= htmlspecialchars($emitente->nome ?? '') ?>">
            </div>
        <?php else: ?>
            <div class="header-logo"></div>
        <?php endif; ?>

        <div class="header-info">
            <?php if ($emitente): ?>
                <strong><?= htmlspecialchars($emitente->nome ?? '') ?></strong><br>
                CNPJ: <?= htmlspecialchars($emitente->cnpj ?? '') ?><br>
                <?= htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? ''))) ?><br>
                Tel: <?= htmlspecialchars($emitente->telefone ?? '') ?> | <?= htmlspecialchars($emitente->email ?? '') ?>
            <?php endif; ?>
        </div>

        <div class="header-title">
            <h1>NOTA FISCAL DE SERVIÇOS ELETRÔNICA</h1>
            <small>DOCUMENTO DE PRÉ-VISUALIZAÇÃO — SEM VALOR FISCAL</small>
        </div>
    </div>

    <!-- Prestador -->
    <div class="section">
        <div class="section-title prestador">PRESTADOR DE SERVIÇO</div>
        <?php if ($emitente): ?>
            <div class="row">
                <div class="col-half">
                    <div class="row"><span class="col-left">Nome/Razão Social:</span><span class="col-right"><?= htmlspecialchars($emitente->nome ?? '') ?></span></div>
                    <div class="row"><span class="col-left">CNPJ:</span><span class="col-right"><?= fmtDoc($emitente->cnpj ?? '') ?></span></div>
                    <div class="row"><span class="col-left">Inscrição Estadual:</span><span class="col-right"><?= htmlspecialchars($emitente->ie ?? '—') ?></span></div>
                </div>
                <div class="col-half">
                    <div class="row"><span class="col-left">Endereço:</span><span class="col-right"><?= htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? ''))) ?></span></div>
                    <div class="row"><span class="col-left">Cidade/UF:</span><span class="col-right"><?= htmlspecialchars(trim(($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '') . ' - CEP: ' . ($emitente->cep ?? ''))) ?></span></div>
                    <div class="row"><span class="col-left">Telefone:</span><span class="col-right"><?= htmlspecialchars($emitente->telefone ?? '—') ?></span></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tomador -->
    <div class="section">
        <div class="section-title tomador">TOMADOR DE SERVIÇO</div>
        <?php if ($os): ?>
            <div class="row">
                <div class="col-half">
                    <div class="row"><span class="col-left">Nome/Razão Social:</span><span class="col-right"><?= htmlspecialchars($os->nomeCliente ?? '') ?></span></div>
                    <div class="row"><span class="col-left">CPF/CNPJ:</span><span class="col-right"><?= !empty($os->cnpj) ? fmtDoc($os->cnpj) : (!empty($os->cpf_cgc) ? fmtDoc($os->cpf_cgc) : '—') ?></span></div>
                </div>
                <div class="col-half">
                    <div class="row"><span class="col-left">Endereço:</span><span class="col-right"><?= htmlspecialchars(trim(($os->rua ?? '') . ', ' . ($os->numero ?? '') . ' - ' . ($os->bairro ?? ''))) ?></span></div>
                    <div class="row"><span class="col-left">Cidade/UF:</span><span class="col-right"><?= htmlspecialchars(trim(($os->cidade ?? '') . '/' . ($os->estado ?? '') . ' - CEP: ' . ($os->cep ?? ''))) ?></span></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Discriminação -->
    <div class="section">
        <div class="section-title discriminacao">DISCRIMINAÇÃO DOS SERVIÇOS</div>
        <p style="margin: 5px 0; line-height: 1.5;"><?= htmlspecialchars($descricao_servico ?? $tributacao['descricao_servico'] ?? '') ?></p>
        <div class="row" style="margin-top: 8px; font-size: 10px; color: #666;">
            <div class="col-half">
                <span class="col-left">Código Trib. LC 116:</span>
                <span class="col-right"><?= htmlspecialchars($tributacao['codigo_tributacao_nacional'] ?? '—') ?></span>
            </div>
            <div class="col-half">
                <span class="col-left">Código Municipal:</span>
                <span class="col-right"><?= htmlspecialchars($tributacao['codigo_tributacao_municipal'] ?? '—') ?></span>
            </div>
        </div>
        <?php if ($os): ?>
            <div class="row" style="margin-top: 5px; font-size: 10px; color: #666;">
                <span class="col-left">OS Nº:</span>
                <span class="col-right"><?= str_pad($os->idOs ?? 0, 6, '0', STR_PAD_LEFT) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Valores -->
    <div class="section">
        <div class="section-title valores">VALORES</div>
        <table class="valores-table">
            <tr>
                <td class="label-col">Valor dos Serviços</td>
                <td class="value-col"><?= fmtMoeda($valor_servicos) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) Deduções</td>
                <td class="value-col"><?= fmtMoeda($valor_deducoes) ?></td>
            </tr>
            <tr>
                <td class="label-col">Base de Cálculo</td>
                <td class="value-col"><?= fmtMoeda($valor_servicos - $valor_deducoes) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) ISS (<?= $tributacao['aliquota_iss'] ?? '5.00' ?>%)</td>
                <td class="value-col"><?= fmtMoeda($impostos['iss'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) PIS</td>
                <td class="value-col"><?= fmtMoeda($impostos['pis'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) COFINS</td>
                <td class="value-col"><?= fmtMoeda($impostos['cofins'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) IRRF</td>
                <td class="value-col"><?= fmtMoeda($impostos['irrf'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) CSLL</td>
                <td class="value-col"><?= fmtMoeda($impostos['csll'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) INSS/CPP</td>
                <td class="value-col"><?= fmtMoeda($impostos['inss'] ?? 0) ?></td>
            </tr>
            <tr class="total-row">
                <td class="label-col">Total Impostos</td>
                <td class="value-col"><?= fmtMoeda($impostos['valor_total_impostos'] ?? 0) ?></td>
            </tr>
            <tr class="liquido-row">
                <td class="label-col">VALOR LÍQUIDO</td>
                <td class="value-col"><?= fmtMoeda($valor_liquido) ?></td>
            </tr>
        </table>
    </div>

    <!-- QR Code PIX -->
    <?php if (!empty($qrCodePix)): ?>
    <div class="section">
        <div class="section-title pix-section">PAGAMENTO VIA PIX</div>
        <div class="pix-area">
            <div class="pix-qr">
                <img src="<?= $qrCodePix ?>" alt="QR Code PIX">
            </div>
            <div class="pix-info">
                <p style="font-size: 13px; font-weight: bold; color: #333;">
                    <img src="<?= base_url('assets/img/logo_pix.png') ?>" class="pix-logo" alt="PIX">
                    Pagamento via PIX
                </p>
                <p>Escaneie o QR Code ao lado para realizar o pagamento automático.</p>
                <p><strong>Valor:</strong> <?= fmtMoeda($valor_liquido) ?></p>
                <?php if (!empty($chaveFormatada)): ?>
                    <p><strong>Chave PIX:</strong> <?= htmlspecialchars($chaveFormatada) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Rodapé -->
    <div class="footer">
        <div class="os-info">
            <?php if ($os): ?>
                OS Nº <?= str_pad($os->idOs ?? 0, 6, '0', STR_PAD_LEFT) ?> — <?= htmlspecialchars($os->nomeCliente ?? '') ?>
            <?php endif; ?>
        </div>
        <div class="os-date">
            Pré-visualização gerada em <?= date('d/m/Y \à\s H:i:s') ?> — Este documento NÃO possui valor fiscal.
        </div>
    </div>
</div>

</body>
</html>