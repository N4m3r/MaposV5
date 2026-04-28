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
$is_preview = $is_preview ?? true;
$ambiente = $ambiente ?? 'homologacao';

// Resolver caminho da logo para mPDF (caminho absoluto do servidor)
$logoPath = '';
if ($emitente && !empty($emitente->url_logo)) {
    $fsPath = convertUrlToUploadsPath($emitente->url_logo);
    if (file_exists($fsPath)) {
        $logoPath = $fsPath;
    }
}
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

        /* Marca d'água menor e mais discreta */
        .watermark {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 28px;
            font-weight: bold;
            color: rgba(180, 40, 40, 0.07);
            white-space: nowrap;
            pointer-events: none;
            letter-spacing: 8px;
            z-index: 0;
            text-transform: uppercase;
        }

        .watermark-homolog {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 22px;
            font-weight: bold;
            color: rgba(200, 150, 0, 0.08);
            white-space: nowrap;
            pointer-events: none;
            letter-spacing: 6px;
            z-index: 0;
            text-transform: uppercase;
        }

        /* Cabeçalho com logo e dados */
        .header {
            border-bottom: 2px solid #333;
            padding: 15px 20px 10px 20px;
            overflow: hidden;
        }

        .header-left {
            float: left;
            width: 120px;
            text-align: center;
            padding-right: 15px;
        }

        .header-left img {
            width: 100px;
            max-height: 80px;
        }

        .header-center {
            float: left;
            padding-top: 5px;
        }

        .header-center h2 {
            font-size: 14px;
            margin: 0 0 4px 0;
            color: #333;
            letter-spacing: 1px;
        }

        .header-center .subtitle {
            font-size: 10px;
            color: #d9534f;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0;
        }

        .header-center .empresa-nome {
            font-size: 12px;
            font-weight: bold;
            color: #555;
            margin: 6px 0 2px 0;
        }

        .header-center .empresa-detail {
            font-size: 9px;
            color: #777;
            margin: 1px 0;
        }

        .header-right {
            float: right;
            text-align: right;
            font-size: 9px;
            color: #888;
            padding-top: 5px;
        }

        .header-right .os-number {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 4px;
        }

        /* Seções */
        .section {
            border-bottom: 1px solid #bbb;
            padding: 10px 20px;
            position: relative;
            z-index: 1;
        }

        .section:last-of-type {
            border-bottom: none;
        }

        .section-title {
            background: #f0f0f0;
            padding: 4px 10px;
            font-weight: bold;
            font-size: 10px;
            color: #444;
            margin-bottom: 8px;
            border-left: 3px solid #3a8abf;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title.prestador { border-left-color: #3a8abf; }
        .section-title.tomador { border-left-color: #5cb85c; }
        .section-title.discriminacao { border-left-color: #f0ad4e; }
        .section-title.valores { border-left-color: #d9534f; }
        .section-title.pix-section { border-left-color: #32bcad; }

        .row {
            overflow: hidden;
            margin: 2px 0;
        }

        .col-left {
            float: left;
            width: 30%;
            font-weight: bold;
            color: #555;
            font-size: 10px;
        }

        .col-right {
            float: left;
            width: 70%;
            color: #333;
            font-size: 10px;
        }

        .col-half {
            float: left;
            width: 48%;
            margin-right: 2%;
        }

        /* Tabela de valores */
        .valores-table {
            width: 100%;
            border-collapse: collapse;
        }

        .valores-table td {
            padding: 4px 10px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
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
            color: #c0392b;
        }

        .valores-table .total-row td {
            border-top: 2px solid #999;
            font-size: 11px;
        }

        .valores-table .liquido-row td {
            font-size: 13px;
            color: #2e7d32;
            background: #e8f5e9;
            border-top: 2px solid #2e7d32;
        }

        /* PIX */
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
            width: 110px;
            height: 110px;
        }

        .pix-info {
            float: left;
            width: 60%;
            padding-left: 15px;
        }

        .pix-info p {
            margin: 3px 0;
            font-size: 10px;
        }

        .pix-logo {
            width: 35px;
            vertical-align: middle;
            margin-right: 5px;
        }

        /* Rodapé */
        .footer {
            background: #f0f0f0;
            border-top: 2px solid #333;
            padding: 8px 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
            overflow: hidden;
        }

        .footer-left {
            float: left;
            text-align: left;
        }

        .footer-right {
            float: right;
            text-align: right;
        }

        .footer-center {
            text-align: center;
            color: #d9534f;
            font-weight: bold;
            font-size: 9px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<div class="documento">
    <?php if ($is_preview): ?>
    <!-- Marca d'água discreta -->
    <div class="watermark">Pré-visualização</div>
    <?php endif; ?>
    <?php if ($ambiente == 'homologacao'): ?>
    <div class="watermark-homolog">AMBIENTE DE HOMOLOGAÇÃO</div>
    <?php endif; ?>

    <!-- Cabeçalho -->
    <div class="header">
        <!-- Logo -->
        <div class="header-left">
            <?php if (!empty($logoPath)): ?>
                <img src="<?= $logoPath ?>" alt="<?= htmlspecialchars($emitente->nome ?? '') ?>">
            <?php elseif ($emitente && !empty($emitente->url_logo)): ?>
                <img src="<?= htmlspecialchars($emitente->url_logo, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($emitente->nome ?? '') ?>">
            <?php endif; ?>
        </div>

        <!-- Título + dados empresa -->
        <div class="header-center">
            <h2>NOTA FISCAL DE SERVIÇOS ELETRÔNICA</h2>
            <?php if ($is_preview): ?>
                <p class="subtitle">PRÉ-VISUALIZAÇÃO — SEM VALOR FISCAL</p>
            <?php endif; ?>
            <?php if ($emitente): ?>
                <p class="empresa-nome"><?= htmlspecialchars($emitente->nome ?? '') ?></p>
                <p class="empresa-detail">CNPJ: <?= htmlspecialchars($emitente->cnpj ?? '') ?><?php if (!empty($emitente->ie)): ?> | IE: <?= htmlspecialchars($emitente->ie) ?><?php endif; ?><?php if (!empty($emitente->inscricao_municipal)): ?> | IM: <?= htmlspecialchars($emitente->inscricao_municipal) ?><?php endif; ?></p>
                <p class="empresa-detail"><?= htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '') . ' - CEP: ' . ($emitente->cep ?? ''))) ?></p>
                <p class="empresa-detail">Tel: <?= htmlspecialchars($emitente->telefone ?? '') ?> | <?= htmlspecialchars($emitente->email ?? '') ?></p>
            <?php endif; ?>
        </div>

        <!-- Número da OS -->
        <div class="header-right">
            <?php if ($os): ?>
                <div class="os-number">OS <?= str_pad($os->idOs ?? 0, 6, '0', STR_PAD_LEFT) ?></div>
            <?php endif; ?>
            <div><?= date('d/m/Y') ?></div>
            <?php if (isset($nfse_numero) && $nfse_numero): ?>
                <div style="margin-top: 4px; font-size: 10px; font-weight: bold; color: #333;">NFS-e <?= htmlspecialchars($nfse_numero) ?></div>
            <?php endif; ?>
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
                </div>
                <div class="col-half">
                    <div class="row"><span class="col-left">Endereço:</span><span class="col-right"><?= htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? ''))) ?></span></div>
                    <div class="row"><span class="col-left">Cidade/UF:</span><span class="col-right"><?= htmlspecialchars(trim(($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '') . ' - CEP: ' . ($emitente->cep ?? ''))) ?></span></div>
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
                    <div class="row"><span class="col-left">CPF/CNPJ:</span><span class="col-right"><?= !empty($os->documento) ? fmtDoc($os->documento) : '—' ?></span></div>
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
        <p style="margin: 5px 0; line-height: 1.5; font-size: 10px;"><?= htmlspecialchars($descricao_servico ?? $tributacao['descricao_servico'] ?? '') ?></p>
        <div class="row" style="margin-top: 6px; font-size: 9px; color: #777;">
            <div class="col-half">
                <span class="col-left">Código Trib. LC 116:</span>
                <span class="col-right"><?= htmlspecialchars($tributacao['codigo_tributacao_nacional'] ?? '—') ?></span>
            </div>
            <div class="col-half">
                <span class="col-left">Código Municipal:</span>
                <span class="col-right"><?= htmlspecialchars($tributacao['codigo_tributacao_municipal'] ?? '—') ?></span>
            </div>
        </div>
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
                <td class="value-col"><?= fmtMoeda($impostos['iss'] ?? $impostos['iss_valor'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) PIS</td>
                <td class="value-col"><?= fmtMoeda($impostos['pis'] ?? $impostos['pis_valor'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) COFINS</td>
                <td class="value-col"><?= fmtMoeda($impostos['cofins'] ?? $impostos['cofins_valor'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) IRRF</td>
                <td class="value-col"><?= fmtMoeda($impostos['irrf'] ?? $impostos['irpj_valor'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) CSLL</td>
                <td class="value-col"><?= fmtMoeda($impostos['csll'] ?? $impostos['csll_valor'] ?? 0) ?></td>
            </tr>
            <tr class="deducao-row">
                <td class="label-col">(-) INSS/CPP</td>
                <td class="value-col"><?= fmtMoeda($impostos['inss'] ?? $impostos['cpp_valor'] ?? 0) ?></td>
            </tr>
            <tr class="total-row">
                <td class="label-col">Total Impostos</td>
                <td class="value-col"><?= fmtMoeda($impostos['valor_total_impostos'] ?? $impostos['total_impostos'] ?? 0) ?></td>
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
                <img src="<?= htmlspecialchars($qrCodePix, ENT_QUOTES, 'UTF-8') ?>" alt="QR Code PIX">
            </div>
            <div class="pix-info">
                <p style="font-size: 12px; font-weight: bold; color: #333;">
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
        <div class="footer-left">
            <?php if ($os): ?>
                OS Nº <?= str_pad($os->idOs ?? 0, 6, '0', STR_PAD_LEFT) ?> — <?= htmlspecialchars($os->nomeCliente ?? '') ?>
            <?php endif; ?>
        </div>
        <div class="footer-right">
            Gerado em <?= date('d/m/Y \à\s H:i:s') ?>
        </div>
    </div>
    <?php if ($is_preview): ?>
    <div style="text-align: center; padding: 6px 20px; font-size: 9px; color: #d9534f; font-weight: bold; letter-spacing: 1px; border-top: 1px solid #ddd;">
        PRÉ-VISUALIZAÇÃO — ESTE DOCUMENTO NÃO POSSUI VALOR FISCAL
    </div>
    <?php endif; ?>
    <?php if ($ambiente == 'homologacao'): ?>
    <div style="text-align: center; padding: 6px 20px; background: #fff3cd; color: #856404; font-weight: bold; font-size: 10px; letter-spacing: 1px;">
        AMBIENTE DE HOMOLOGAÇÃO — SEM VALOR FISCAL
    </div>
    <?php endif; ?>
</div>

</body>
</html>