<?php
/**
 * Impressao A4 da NFSe Importada
 * Baseado no modelo preview_nfse.php
 * Extrai dados reais do XML da nota
 */

function fmtMoeda($v) {
    return 'R$ ' . number_format(floatval($v), 2, ',', '.');
}

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

$e = $emitente ?? null;
$c = $cliente ?? null;
$n = $nota ?? null;
$x = $xmlData ?? [];
$logo = $logo_url ?? base_url('assets/img/logo.png');

// Dados do prestador: prioridade XML > banco
$pCnpj = !empty($x['prestador']['cpf_cnpj']) ? $x['prestador']['cpf_cnpj'] : ($e->cnpj ?? '');
$pNome = !empty($x['prestador']['nome']) ? $x['prestador']['nome'] : ($e->nome ?? $e->razaosocial ?? '');
$pIM = !empty($x['prestador']['inscricao_municipal']) ? $x['prestador']['inscricao_municipal'] : ($e->ie ?? $e->inscricao_municipal ?? '');
$pRua = !empty($x['prestador']['endereco']) ? $x['prestador']['endereco'] : ($e->rua ?? '');
$pNumero = !empty($x['prestador']['numero']) ? $x['prestador']['numero'] : ($e->numero ?? '');
$pBairro = !empty($x['prestador']['bairro']) ? $x['prestador']['bairro'] : ($e->bairro ?? '');
$pCidade = !empty($x['prestador']['cidade']) ? $x['prestador']['cidade'] : ($e->cidade ?? '');
$pUf = !empty($x['prestador']['uf']) ? $x['prestador']['uf'] : ($e->uf ?? '');
$pCep = !empty($x['prestador']['cep']) ? $x['prestador']['cep'] : ($e->cep ?? '');
$pTel = !empty($x['prestador']['telefone']) ? $x['prestador']['telefone'] : ($e->telefone ?? '');
$pEmail = !empty($x['prestador']['email']) ? $x['prestador']['email'] : ($e->email ?? '');

// Dados do tomador: prioridade XML > banco
$tDoc = !empty($x['tomador']['cpf_cnpj']) ? $x['tomador']['cpf_cnpj'] : ($c->documento ?? $c->cnpj ?? '');
$tNome = !empty($x['tomador']['nome']) ? $x['tomador']['nome'] : ($c->nomeCliente ?? $c->nome ?? '');
$tIM = !empty($x['tomador']['inscricao_municipal']) ? $x['tomador']['inscricao_municipal'] : ($c->inscricao_municipal ?? '');
$tRua = !empty($x['tomador']['endereco']) ? $x['tomador']['endereco'] : ($c->rua ?? $os->rua ?? '');
$tNumero = !empty($x['tomador']['numero']) ? $x['tomador']['numero'] : ($c->numero ?? $os->numero ?? '');
$tBairro = !empty($x['tomador']['bairro']) ? $x['tomador']['bairro'] : ($c->bairro ?? $os->bairro ?? '');
$tCidade = !empty($x['tomador']['cidade']) ? $x['tomador']['cidade'] : ($c->cidade ?? $os->cidade ?? '');
$tUf = !empty($x['tomador']['uf']) ? $x['tomador']['uf'] : ($c->estado ?? $c->uf ?? $os->estado ?? '');
$tCep = !empty($x['tomador']['cep']) ? $x['tomador']['cep'] : ($c->cep ?? $os->cep ?? '');
$tTel = !empty($x['tomador']['telefone']) ? $x['tomador']['telefone'] : ($c->telefone ?? '');
$tEmail = !empty($x['tomador']['email']) ? $x['tomador']['email'] : ($c->email ?? '');

// Dados da nota: prioridade XML > banco
$nfNumero = !empty($x['numero']) ? $x['numero'] : ($n->numero ?? '');
$nfSerie = !empty($x['serie']) ? $x['serie'] : ($n->serie ?? '');
$nfChave = !empty($x['chave_acesso']) ? preg_replace('/^NFS/i', '', $x['chave_acesso']) : ($n->chave_acesso ?? '');
$nfCodVerif = !empty($x['codigo_verificacao']) ? $x['codigo_verificacao'] : '';
$nfDataEmi = !empty($x['data_emissao']) ? $x['data_emissao'] : ($n->data_emissao ?? '');
$nfCompetencia = !empty($x['competencia']) ? $x['competencia'] : '';
$nfDesc = !empty($x['descricao_servico']) ? $x['descricao_servico'] : '';
$nfAliq = !empty($x['aliquota_iss']) ? $x['aliquota_iss'] : ($tributacao['aliquota_iss'] ?? '5.00');

$vServ = floatval($x['valor_servicos'] ?? $n->valor_total ?? 0);
$vDed = floatval($x['valor_deducoes'] ?? 0);
$vLiq = floatval($x['valor_liquido'] ?? 0);
$vISS = floatval($x['valor_iss'] ?? 0);
$vPIS = floatval($x['valor_pis'] ?? 0);
$vCOFINS = floatval($x['valor_cofins'] ?? 0);
$vIRRF = floatval($x['valor_irrf'] ?? 0);
$vCSLL = floatval($x['valor_csll'] ?? 0);
$vINSS = floatval($x['valor_inss'] ?? 0);
$vTotImpostos = floatval($x['valor_total_impostos'] ?? $n->valor_impostos ?? 0);

if ($vLiq == 0 && $vServ > 0) {
    $vLiq = $vServ - $vTotImpostos;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>NFS-e <?= htmlspecialchars($nfNumero) ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
            background: #e5e5e5;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            position: relative;
        }
        .documento {
            width: 100%;
            position: relative;
            border: 2px solid #333;
            padding: 0;
        }
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
        .section {
            border-bottom: 1px solid #bbb;
            padding: 10px 20px;
            position: relative;
            z-index: 1;
        }
        .section:last-of-type { border-bottom: none; }
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
        .row { overflow: hidden; margin: 2px 0; }
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
        .valores-table .deducao-row .value-col { color: #c0392b; }
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
        .footer {
            background: #f0f0f0;
            border-top: 2px solid #333;
            padding: 8px 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
            overflow: hidden;
        }
        .footer-left { float: left; text-align: left; }
        .footer-right { float: right; text-align: right; }
        .chave-box {
            border: 1px dashed #999;
            padding: 6px 10px;
            text-align: center;
            font-family: monospace;
            font-size: 11px;
            letter-spacing: 1px;
            margin-top: 8px;
            word-break: break-all;
            background: #fafafa;
        }
        .chave-box label {
            font-size: 9px;
            color: #666;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 10px 20px;
            font-size: 14px;
            background: #1086dd;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-print:hover { background: #0d6efd; }
        @media print {
            body { background: #fff; }
            .page { box-shadow: none; margin: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir
    </button>

    <div class="page">
        <div class="documento">
            <!-- Marca d'agua -->
            <div class="watermark">Importada</div>

            <!-- Cabecalho -->
            <div class="header">
                <div class="header-left">
                    <img src="<?= $logo ?>" alt="Logo" onerror="this.style.display='none'">
                </div>
                <div class="header-center">
                    <h2>NOTA FISCAL DE SERVICOS ELETRONICA</h2>
                    <p class="subtitle">IMPORTADA DE OUTRO SISTEMA</p>
                    <?php if ($pNome): ?>
                        <p class="empresa-nome"><?= htmlspecialchars($pNome) ?></p>
                        <p class="empresa-detail">CNPJ: <?= !empty($pCnpj) ? fmtDoc($pCnpj) : '---' ?> <?= !empty($pIM) ? '| IM: ' . htmlspecialchars($pIM) : '' ?></p>
                        <p class="empresa-detail"><?= htmlspecialchars(trim($pRua . ', ' . $pNumero . ' - ' . $pBairro . ', ' . $pCidade . '/' . $pUf . ' - CEP: ' . $pCep)) ?></p>
                        <p class="empresa-detail">Tel: <?= htmlspecialchars($pTel ?: '---') ?> | <?= htmlspecialchars($pEmail ?: '---') ?></p>
                    <?php endif; ?>
                </div>
                <div class="header-right">
                    <?php if ($n->os_id): ?>
                        <div class="os-number">OS <?= str_pad($n->os_id, 6, '0', STR_PAD_LEFT) ?></div>
                    <?php endif; ?>
                    <div><?= date('d/m/Y') ?></div>
                    <?php if ($nfNumero): ?>
                        <div style="margin-top:4px; font-size:10px; font-weight:bold; color:#333;">NFS-e <?= htmlspecialchars($nfNumero) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Prestador -->
            <div class="section">
                <div class="section-title prestador">Prestador de Servico</div>
                <div class="row">
                    <div class="col-half">
                        <div class="row"><span class="col-left">Nome/Razao Social:</span><span class="col-right"><?= htmlspecialchars($pNome ?: '---') ?></span></div>
                        <div class="row"><span class="col-left">CNPJ:</span><span class="col-right"><?= !empty($pCnpj) ? fmtDoc($pCnpj) : '---' ?></span></div>
                        <?php if ($pIM): ?>
                        <div class="row"><span class="col-left">Insc. Municipal:</span><span class="col-right"><?= htmlspecialchars($pIM) ?></span></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-half">
                        <div class="row"><span class="col-left">Endereco:</span><span class="col-right"><?= htmlspecialchars(trim($pRua . ', ' . $pNumero . ' - ' . $pBairro)) ?: '---' ?></span></div>
                        <div class="row"><span class="col-left">Cidade/UF:</span><span class="col-right"><?= htmlspecialchars(trim($pCidade . '/' . $pUf . ' - CEP: ' . $pCep)) ?: '---' ?></span></div>
                    </div>
                </div>
            </div>

            <!-- Tomador -->
            <div class="section">
                <div class="section-title tomador">Tomador de Servico</div>
                <div class="row">
                    <div class="col-half">
                        <div class="row"><span class="col-left">Nome/Razao Social:</span><span class="col-right"><?= htmlspecialchars($tNome ?: '---') ?></span></div>
                        <div class="row"><span class="col-left">CPF/CNPJ:</span><span class="col-right"><?= !empty($tDoc) ? fmtDoc($tDoc) : '---' ?></span></div>
                        <?php if ($tIM): ?>
                        <div class="row"><span class="col-left">Insc. Municipal:</span><span class="col-right"><?= htmlspecialchars($tIM) ?></span></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-half">
                        <div class="row"><span class="col-left">Endereco:</span><span class="col-right"><?= htmlspecialchars(trim($tRua . ', ' . $tNumero . ' - ' . $tBairro)) ?: '---' ?></span></div>
                        <div class="row"><span class="col-left">Cidade/UF:</span><span class="col-right"><?= htmlspecialchars(trim($tCidade . '/' . $tUf . ' - CEP: ' . $tCep)) ?: '---' ?></span></div>
                    </div>
                </div>
            </div>

            <!-- Dados da Nota -->
            <div class="section">
                <div class="section-title discriminacao">Dados da Nota Fiscal</div>
                <div class="row">
                    <div class="col-half">
                        <div class="row"><span class="col-left">Numero:</span><span class="col-right"><?= htmlspecialchars($nfNumero ?: '---') ?></span></div>
                        <div class="row"><span class="col-left">Serie:</span><span class="col-right"><?= htmlspecialchars($nfSerie ?: '---') ?></span></div>
                        <div class="row"><span class="col-left">Data Emissao:</span><span class="col-right"><?= !empty($nfDataEmi) ? date('d/m/Y', strtotime($nfDataEmi)) : '---' ?></span></div>
                        <div class="row"><span class="col-left">Competencia:</span><span class="col-right"><?= htmlspecialchars($nfCompetencia ?: (!empty($nfDataEmi) ? date('m/Y', strtotime($nfDataEmi)) : '---')) ?></span></div>
                    </div>
                    <div class="col-half">
                        <div class="row"><span class="col-left">Situacao:</span><span class="col-right"><?= htmlspecialchars($n->situacao ?: 'Importada') ?></span></div>
                        <div class="row"><span class="col-left">Data Importacao:</span><span class="col-right"><?= !empty($n->data_importacao) ? date('d/m/Y H:i', strtotime($n->data_importacao)) : '---' ?></span></div>
                        <?php if ($nfCodVerif): ?>
                        <div class="row"><span class="col-left">Cod. Verificacao:</span><span class="col-right"><?= htmlspecialchars($nfCodVerif) ?></span></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($nfChave): ?>
                <div class="chave-box">
                    <label>Chave de Acesso da NFS-e</label>
                    <?= htmlspecialchars($nfChave) ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Discriminacao -->
            <div class="section">
                <div class="section-title discriminacao">Discriminacao dos Servicos</div>
                <p style="margin:5px 0; line-height:1.5; font-size:10px;"><?= htmlspecialchars($nfDesc ?: 'Servicos prestados conforme contrato.') ?></p>
                <?php if ($nfAliq): ?>
                <div class="row" style="margin-top:6px; font-size:9px; color:#777;">
                    <div class="col-half">
                        <span class="col-left">Aliquota ISS:</span>
                        <span class="col-right"><?= htmlspecialchars($nfAliq) ?>%</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Valores -->
            <div class="section">
                <div class="section-title valores">Valores</div>
                <table class="valores-table">
                    <tr>
                        <td class="label-col">Valor dos Servicos</td>
                        <td class="value-col"><?= fmtMoeda($vServ) ?></td>
                    </tr>
                    <?php if ($vDed > 0): ?>
                    <tr class="deducao-row">
                        <td class="label-col">(-) Deducoes</td>
                        <td class="value-col"><?= fmtMoeda($vDed) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="label-col">Base de Calculo</td>
                        <td class="value-col"><?= fmtMoeda($vServ - $vDed) ?></td>
                    </tr>
                    <?php if ($vISS > 0): ?>
                    <tr class="deducao-row">
                        <td class="label-col">(-) ISS</td>
                        <td class="value-col"><?= fmtMoeda($vISS) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vPIS > 0): ?>
                    <tr class="deducao-row">
                        <td class="label-col">(-) PIS</td>
                        <td class="value-col"><?= fmtMoeda($vPIS) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vCOFINS > 0): ?>
                    <tr class="deducao-row">
                        <td class="label-col">(-) COFINS</td>
                        <td class="value-col"><?= fmtMoeda($vCOFINS) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vIRRF > 0): ?>
                    <tr class="deducao-row">
                        <td class="label-col">(-) IRRF</td>
                        <td class="value-col"><?= fmtMoeda($vIRRF) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vCSLL > 0): ?>
                    <tr class="deducao-row">
                        <td class="label-col">(-) CSLL</td>
                        <td class="value-col"><?= fmtMoeda($vCSLL) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vINSS > 0): ?>
                    <tr class="deducao-row">
                        <td class="label-col">(-) INSS/CPP</td>
                        <td class="value-col"><?= fmtMoeda($vINSS) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td class="label-col">Total Impostos</td>
                        <td class="value-col"><?= fmtMoeda($vTotImpostos) ?></td>
                    </tr>
                    <tr class="liquido-row">
                        <td class="label-col">VALOR LIQUIDO</td>
                        <td class="value-col"><?= fmtMoeda($vLiq > 0 ? $vLiq : ($vServ - $vTotImpostos)) ?></td>
                    </tr>
                </table>
            </div>

            <!-- Rodape -->
            <div class="footer">
                <div class="footer-left">
                    <?php if ($n->os_id): ?>
                        OS Nº <?= str_pad($n->os_id, 6, '0', STR_PAD_LEFT) ?>
                    <?php endif; ?>
                </div>
                <div class="footer-right">
                    Gerado em <?= date('d/m/Y \a\s H:i:s') ?>
                </div>
            </div>
            <div style="text-align:center; padding:6px 20px; font-size:9px; color:#d9534f; font-weight:bold; letter-spacing:1px; border-top:1px solid #ddd;">
                IMPORTADA DE OUTRO SISTEMA — CONSULTE A AUTENTICIDADE NO PORTAL DA PREFEITURA
            </div>
        </div>
    </div>
</body>
</html>
