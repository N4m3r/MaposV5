<?php
/**
 * Preview de Boleto para impressão em folha A4
 * Layout com Recibo do Sacado (parte superior) e Ficha de Compensação (parte inferior)
 */

if (!isset($boleto) || !isset($os)) {
    echo '<h2>Dados do boleto não disponíveis</h2>';
    return;
}

// Dados formatados
$cedenteNome = htmlspecialchars(($emitente->nome ?? $emitente->razaosocial ?? $emitente->nomeEmpresa ?? 'Cedente'), ENT_QUOTES, 'UTF-8');
$cedenteCnpj = '';
if (!empty($emitente->cnpj)) {
    $cnpj = preg_replace('/\D/', '', $emitente->cnpj);
    if (strlen($cnpj) == 14) {
        $cedenteCnpj = substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }
}
$cedenteEndereco = htmlspecialchars(
    trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '')),
    ENT_QUOTES, 'UTF-8'
);

$sacadoNome = htmlspecialchars($boleto->sacado_nome ?? ($os->nomeCliente ?? 'Sacado'), ENT_QUOTES, 'UTF-8');
$sacadoDoc = '';
if (!empty($boleto->sacado_documento)) {
    $doc = preg_replace('/\D/', '', $boleto->sacado_documento);
    if (strlen($doc) == 14) {
        $sacadoDoc = substr($doc, 0, 2) . '.' . substr($doc, 2, 3) . '.' . substr($doc, 5, 3) . '/' . substr($doc, 8, 4) . '-' . substr($doc, 12, 2);
    } elseif (strlen($doc) == 11) {
        $sacadoDoc = substr($doc, 0, 3) . '.' . substr($doc, 3, 3) . '.' . substr($doc, 6, 3) . '-' . substr($doc, 9, 2);
    }
}
$sacadoEndereco = htmlspecialchars($boleto->sacado_endereco ?? ($os->rua ?? '') . ', ' . ($os->numero ?? '') . ' - ' . ($os->bairro ?? ''), ENT_QUOTES, 'UTF-8');

$vencimento = $boleto->data_vencimento ? date('d/m/Y', strtotime($boleto->data_vencimento)) : '---';
$emissao = $boleto->data_emissao ? date('d/m/Y', strtotime($boleto->data_emissao)) : '---';
$valor = floatval($boleto->valor_liquido ?? 0);
$valorFormatado = number_format($valor, 2, ',', '.');
$valorExtenso = '';
try {
    // Tentar converter para extenso se existir helper
    if (function_exists('valorPorExtenso')) {
        $valorExtenso = valorPorExtenso($valor);
    }
} catch (Exception $e) {
    $valorExtenso = '';
}

$nossoNumero = $boleto->nosso_numero ?: str_pad($boleto->id, 10, '0', STR_PAD_LEFT);
$linhaDigitavel = $boleto->linha_digitavel ?: '';
$codigoBarras = $boleto->codigo_barras ?: '';
$instrucoes = nl2br(htmlspecialchars($boleto->instrucoes ?? 'Pagável em qualquer banco até o vencimento.', ENT_QUOTES, 'UTF-8'));

// Dados da NFSe para referência
$numeroNfse = $nfse->numero_nfse ?? ($nfse->id ?? '') ?: '';
$osNumero = $os->idOs ?? $boleto->os_id ?? '';

// Código de barras estilizado (só exibe se houver)
$codigoBarrasHtml = '';
if ($codigoBarras) {
    // Formata como números em caixas (estilo boleto)
    $chars = str_split(preg_replace('/\D/', '', $codigoBarras));
    $codigoBarrasHtml = '<div style="display:flex;gap:1px;justify-content:center;margin-top:4px;">';
    foreach ($chars as $ch) {
        $width = (intval($ch) % 2 == 0) ? '2px' : '3px';
        $codigoBarrasHtml .= '<div style="width:' . $width . ';height:36px;background:#000;"></div>';
    }
    $codigoBarrasHtml .= '</div>';
}
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
            color: #000;
            background: #fff;
            padding: 10px;
        }
        .boleto-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }
        .boleto-section {
            border: 1px solid #000;
            margin-bottom: 15px;
        }
        .section-title {
            background: #f5f5f5;
            border-bottom: 1px solid #000;
            padding: 4px 8px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .boleto-row {
            display: flex;
            border-bottom: 1px solid #000;
        }
        .boleto-row:last-child { border-bottom: none; }
        .boleto-cell {
            padding: 4px 6px;
            border-right: 1px solid #000;
            flex: 1;
        }
        .boleto-cell:last-child { border-right: none; }
        .boleto-cell label {
            display: block;
            font-size: 6.5pt;
            color: #555;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        .boleto-cell .value {
            font-size: 9pt;
            font-weight: bold;
            word-break: break-all;
        }
        .cell-sm { flex: 0.6; }
        .cell-md { flex: 1.2; }
        .cell-lg { flex: 2; }
        .cell-xl { flex: 3; }
        .cell-auto { flex: 0 0 auto; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-lg { font-size: 11pt !important; }
        .font-xl { font-size: 13pt !important; }
        .cut-line {
            border-top: 1px dashed #999;
            margin: 10px 0;
            position: relative;
            text-align: right;
            padding-top: 2px;
        }
        .cut-line span {
            font-size: 7pt;
            color: #666;
            background: #fff;
            padding-left: 6px;
        }
        .logo-area {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            font-weight: bold;
            font-size: 12pt;
            border-right: 1px solid #000;
            flex: 0 0 80px;
            text-align: center;
        }
        .codigo-banco {
            font-size: 14pt;
            letter-spacing: 1px;
        }
        .linha-digitavel-area {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 8px;
        }
        .barcode-area {
            padding: 8px;
            text-align: center;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 48pt;
            color: rgba(200, 0, 0, 0.08);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
        .relative { position: relative; }
        .autenticacao {
            border-top: 1px solid #000;
            padding: 6px;
            font-size: 7pt;
            min-height: 40px;
        }
        .nfse-ref {
            background: #fffbe6;
            border: 1px dashed #d4a017;
            padding: 4px 6px;
            font-size: 7.5pt;
            margin-top: 4px;
        }
        @media print {
            body { padding: 0; }
            .boleto-container { max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="boleto-container">

    <!-- ==================== RECIBO DO SACADO ==================== -->
    <div class="boleto-section">
        <div class="section-title">Recibo do Sacado</div>

        <div class="boleto-row">
            <div class="boleto-cell cell-xl">
                <label>Cedente</label>
                <div class="value"><?= $cedenteNome ?></div>
            </div>
            <div class="boleto-cell cell-md">
                <label>CNPJ do Cedente</label>
                <div class="value"><?= $cedenteCnpj ?: '---' ?></div>
            </div>
            <div class="boleto-cell cell-sm text-right">
                <label>Vencimento</label>
                <div class="value font-lg"><?= $vencimento ?></div>
            </div>
        </div>

        <div class="boleto-row">
            <div class="boleto-cell cell-lg">
                <label>Sacado</label>
                <div class="value"><?= $sacadoNome ?></div>
            </div>
            <div class="boleto-cell cell-md">
                <label>CPF/CNPJ do Sacado</label>
                <div class="value"><?= $sacadoDoc ?: '---' ?></div>
            </div>
            <div class="boleto-cell cell-sm text-right">
                <label>Valor do Documento</label>
                <div class="value font-xl">R$ <?= $valorFormatado ?></div>
            </div>
        </div>

        <div class="boleto-row">
            <div class="boleto-cell cell-xl">
                <label>Endereço do Cedente</label>
                <div class="value"><?= $cedenteEndereco ?></div>
            </div>
            <div class="boleto-cell cell-md">
                <label>Nosso Número</label>
                <div class="value"><?= $nossoNumero ?></div>
            </div>
        </div>

        <div class="boleto-row">
            <div class="boleto-cell cell-lg">
                <label>Endereço do Sacado</label>
                <div class="value"><?= $sacadoEndereco ?></div>
            </div>
            <div class="boleto-cell cell-md">
                <label>Data do Documento</label>
                <div class="value"><?= $emissao ?></div>
            </div>
            <div class="boleto-cell cell-sm">
                <label>Nº Documento</label>
                <div class="value"><?= str_pad($osNumero, 6, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>

        <?php if ($numeroNfse): ?>
        <div class="boleto-row">
            <div class="boleto-cell">
                <div class="nfse-ref">
                    <strong>NFS-e vinculada:</strong> Nº <?= htmlspecialchars($numeroNfse) ?> | OS #<?= $osNumero ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="boleto-row">
            <div class="boleto-cell">
                <label>Instruções</label>
                <div style="font-size: 8pt; line-height: 1.4;"><?= $instrucoes ?></div>
            </div>
        </div>

        <div class="autenticacao">
            <label>Autenticação Mecânica / Assinatura do Cedente</label>
        </div>
    </div>

    <!-- Linha de Corte -->
    <div class="cut-line"><span>Corte na linha pontilhada</span></div>

    <!-- ==================== FICHA DE COMPENSAÇÃO ==================== -->
    <div class="boleto-section relative">
        <?php if ($is_preview): ?>
        <div class="watermark">PREVIEW</div>
        <?php endif; ?>

        <div class="boleto-row">
            <div class="logo-area">
                <div class="codigo-banco">BOLETO</div>
            </div>
            <div class="linha-digitavel-area">
                <?= $linhaDigitavel ?: '00000.00000 00000.000000 00000.000000 0 00000000000000' ?>
            </div>
        </div>

        <div class="boleto-row">
            <div class="boleto-cell cell-xl">
                <label>Local de Pagamento</label>
                <div class="value">Pagável em qualquer banco até o vencimento.</div>
            </div>
            <div class="boleto-cell cell-sm text-right">
                <label>Vencimento</label>
                <div class="value font-lg"><?= $vencimento ?></div>
            </div>
        </div>

        <div class="boleto-row">
            <div class="boleto-cell cell-lg">
                <label>Cedente</label>
                <div class="value"><?= $cedenteNome ?></div>
            </div>
            <div class="boleto-cell cell-md">
                <label>Agência / Código Cedente</label>
                <div class="value">---</div>
            </div>
            <div class="boleto-cell cell-sm text-right">
                <label>Nosso Número</label>
                <div class="value"><?= $nossoNumero ?></div>
            </div>
        </div>

        <div class="boleto-row">
            <div class="boleto-cell cell-sm">
                <label>Data do Doc.</label>
                <div class="value"><?= $emissao ?></div>
            </div>
            <div class="boleto-cell cell-sm">
                <label>Nº Documento</label>
                <div class="value"><?= str_pad($osNumero, 6, '0', STR_PAD_LEFT) ?></div>
            </div>
            <div class="boleto-cell cell-sm">
                <label>Espécie Doc.</label>
                <div class="value">DM</div>
            </div>
            <div class="boleto-cell cell-sm">
                <label>Aceite</label>
                <div class="value">N</div>
            </div>
            <div class="boleto-cell cell-sm">
                <label>Data Proc.</label>
                <div class="value"><?= $emissao ?></div>
            </div>
            <div class="boleto-cell cell-sm text-right">
                <label>(=) Valor do Documento</label>
                <div class="value font-lg">R$ <?= $valorFormatado ?></div>
            </div>
        </div>

        <div class="boleto-row">
            <div class="boleto-cell cell-md">
                <label>Uso do Banco</label>
                <div class="value">&nbsp;</div>
            </div>
            <div class="boleto-cell cell-sm">
                <label>Carteira</label>
                <div class="value">---</div>
            </div>
            <div class="boleto-cell cell-sm">
                <label>Espécie</label>
                <div class="value">R$</div>
            </div>
            <div class="boleto-cell cell-sm">
                <label>Quantidade</label>
                <div class="value">---</div>
            </div>
            <div class="boleto-cell cell-sm">
                <label>Valor</label>
                <div class="value">---</div>
            </div>
            <div class="boleto-cell cell-sm text-right">
                <label>(-) Desconto / Abatimento</label>
                <div class="value">---</div>
            </div>
        </div>

        <div class="boleto-row">
            <div class="boleto-cell cell-xl">
                <label>Instruções de Responsabilidade do Cedente</label>
                <div style="font-size: 8pt; line-height: 1.4;"><?= $instrucoes ?></div>
            </div>
            <div class="boleto-cell cell-sm">
                <div class="boleto-cell" style="border-bottom:1px solid #000;border-right:none;padding:2px 0;">
                    <label>(-) Outras Deduções</label>
                    <div class="value">---</div>
                </div>
                <div class="boleto-cell" style="border-bottom:1px solid #000;border-right:none;padding:2px 0;">
                    <label>(+) Mora / Multa</label>
                    <div class="value">---</div>
                </div>
                <div class="boleto-cell" style="border-bottom:1px solid #000;border-right:none;padding:2px 0;">
                    <label>(+) Outros Acréscimos</label>
                    <div class="value">---</div>
                </div>
                <div class="boleto-cell" style="border-right:none;padding:2px 0;">
                    <label>(=) Valor Cobrado</label>
                    <div class="value font-lg">R$ <?= $valorFormatado ?></div>
                </div>
            </div>
        </div>

        <div class="boleto-row">
            <div class="boleto-cell cell-lg">
                <label>Sacado</label>
                <div class="value"><?= $sacadoNome ?></div>
                <div style="font-size: 8pt; margin-top: 2px;">CPF/CNPJ: <?= $sacadoDoc ?: '---' ?> | <?= $sacadoEndereco ?></div>
            </div>
            <div class="boleto-cell cell-md">
                <label>Sacador / Avalista</label>
                <div class="value">&nbsp;</div>
            </div>
        </div>

        <?php if ($numeroNfse): ?>
        <div class="boleto-row">
            <div class="boleto-cell">
                <div class="nfse-ref">
                    <strong>Referência:</strong> NFS-e Nº <?= htmlspecialchars($numeroNfse) ?> | OS #<?= $osNumero ?> | Emitido via MAP-OS
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Código de Barras -->
        <div class="barcode-area">
            <?php if ($codigoBarrasHtml): ?>
                <?= $codigoBarrasHtml ?>
                <div style="font-size: 8pt; margin-top: 4px; letter-spacing: 2px; font-family: monospace;">
                    <?= preg_replace('/(\d{4})/', '$1 ', preg_replace('/\D/', '', $codigoBarras)) ?>
                </div>
            <?php else: ?>
                <div style="font-size: 8pt; color: #999; padding: 8px; border: 1px dashed #ccc;">
                    [Código de barras será gerado pelo gateway bancário após emissão]
                </div>
            <?php endif; ?>
        </div>

        <div class="autenticacao">
            <label>Autenticação Mecânica / Ficha de Compensação</label>
        </div>
    </div>

</div>

</body>
</html>
