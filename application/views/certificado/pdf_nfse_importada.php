<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>NFS-e <?= htmlspecialchars($nfNumero) ?></title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #2d2d2d;
            line-height: 1.4;
        }
        .container {
            width: 100%;
            max-width: 180mm;
            margin: 0 auto;
        }

        /* Cabecalho */
        .header {
            border: 2px solid #1a237e;
            border-radius: 4px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            margin-bottom: 8px;
        }
        .header-logo {
            width: 80px;
            text-align: center;
        }
        .header-logo img {
            max-width: 70px;
            max-height: 60px;
        }
        .header-title {
            flex: 1;
            text-align: center;
            padding: 0 10px;
        }
        .header-title h1 {
            font-size: 14px;
            color: #1a237e;
            margin: 0;
            letter-spacing: 1px;
        }
        .header-title h2 {
            font-size: 11px;
            color: #3949ab;
            margin: 2px 0 0;
            font-weight: normal;
        }
        .header-number {
            text-align: right;
            min-width: 100px;
        }
        .header-number .num-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
        }
        .header-number .num-value {
            font-size: 18px;
            font-weight: bold;
            color: #1a237e;
        }
        .header-number .num-status {
            display: inline-block;
            padding: 2px 8px;
            background: #4caf50;
            color: #fff;
            font-size: 8px;
            border-radius: 3px;
            margin-top: 2px;
        }

        /* Secoes */
        .section {
            border: 1px solid #c5cae9;
            border-radius: 4px;
            margin-bottom: 8px;
            overflow: hidden;
        }
        .section-header {
            background: #1a237e;
            color: #fff;
            padding: 6px 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-body {
            padding: 8px 10px;
            background: #fff;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -4px;
        }
        .col-6 {
            width: 50%;
            padding: 0 4px;
        }
        .col-4 {
            width: 33.33%;
            padding: 0 4px;
        }
        .col-3 {
            width: 25%;
            padding: 0 4px;
        }
        .field {
            margin-bottom: 4px;
        }
        .field-label {
            font-size: 7px;
            color: #666;
            text-transform: uppercase;
            display: block;
            margin-bottom: 1px;
        }
        .field-value {
            font-size: 9px;
            font-weight: bold;
            color: #2d2d2d;
        }

        /* Chave de acesso */
        .chave-box {
            border: 1px dashed #3949ab;
            border-radius: 4px;
            padding: 8px 10px;
            text-align: center;
            background: #e8eaf6;
            margin-top: 6px;
        }
        .chave-box .chave-label {
            font-size: 7px;
            color: #1a237e;
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
            font-weight: bold;
        }
        .chave-box .chave-value {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
            color: #1a237e;
            letter-spacing: 0.5px;
            word-break: break-all;
        }

        /* Tabela de valores */
        .valores-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .valores-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e8eaf6;
            font-size: 9px;
        }
        .valores-table .label-col {
            width: 60%;
            color: #555;
        }
        .valores-table .value-col {
            width: 40%;
            text-align: right;
            font-weight: bold;
        }
        .valores-table .deducao .value-col {
            color: #c62828;
        }
        .valores-table .total td {
            border-top: 2px solid #1a237e;
            border-bottom: 2px solid #1a237e;
            background: #e8eaf6;
            font-size: 10px;
            font-weight: bold;
        }
        .valores-table .liquido td {
            background: #1a237e;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
        }
        .valores-table .liquido .value-col {
            color: #fff;
        }

        /* QR Code */
        .qr-section {
            text-align: center;
            padding: 10px;
            border-top: 1px solid #c5cae9;
        }
        .qr-section img {
            width: 100px;
            height: 100px;
        }
        .qr-section .qr-text {
            font-size: 7px;
            color: #666;
            margin-top: 4px;
        }

        /* Rodape */
        .footer {
            border-top: 2px solid #1a237e;
            padding: 6px 0;
            text-align: center;
            font-size: 7px;
            color: #666;
        }
        .footer strong {
            color: #1a237e;
        }

        /* Status badge */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #4caf50; color: #fff; }
        .badge-info { background: #2196f3; color: #fff; }
    </style>
</head>
<body>
    <div class="container">

        <!-- CABECALHO -->
        <div class="header">
            <div class="header-logo">
                <?php if (!empty($logoPath) && file_exists($logoPath)): ?>
                    <img src="<?= $logoPath ?>" alt="Logo">
                <?php endif; ?>
            </div>
            <div class="header-title">
                <h1>NOTA FISCAL DE SERVICOS ELETRONICA</h1>
                <h2>NFS-e</h2>
            </div>
            <div class="header-number">
                <div class="num-label">Numero</div>
                <div class="num-value"><?= htmlspecialchars($nfNumero ?: '---') ?></div>
                <span class="num-status"><?= htmlspecialchars($situacao) ?></span>
            </div>
        </div>

        <!-- PRESTADOR -->
        <div class="section">
            <div class="section-header">Prestador de Servico</div>
            <div class="section-body">
                <div class="row">
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Nome / Razao Social</span>
                            <span class="field-value"><?= htmlspecialchars($pNome ?: '---') ?></span>
                        </div>
                        <div class="field">
                            <span class="field-label">CNPJ</span>
                            <span class="field-value"><?= !empty($pCnpj) ? $fmtDoc($pCnpj) : '---' ?></span>
                        </div>
                        <?php if ($pIM): ?>
                        <div class="field">
                            <span class="field-label">Inscricao Municipal</span>
                            <span class="field-value"><?= htmlspecialchars($pIM) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Endereco</span>
                            <span class="field-value"><?= htmlspecialchars(trim($pRua . ', ' . $pNumero . ' - ' . $pBairro)) ?: '---' ?></span>
                        </div>
                        <div class="field">
                            <span class="field-label">Cidade / UF</span>
                            <span class="field-value"><?= htmlspecialchars(trim($pCidade . '/' . $pUf . ' - CEP: ' . $pCep)) ?: '---' ?></span>
                        </div>
                        <?php if ($pTel || $pEmail): ?>
                        <div class="field">
                            <span class="field-label">Contato</span>
                            <span class="field-value"><?= htmlspecialchars(($pTel ?: '') . ($pTel && $pEmail ? ' / ' : '') . ($pEmail ?: '')) ?: '---' ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOMADOR -->
        <div class="section">
            <div class="section-header">Tomador de Servico</div>
            <div class="section-body">
                <div class="row">
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Nome / Razao Social</span>
                            <span class="field-value"><?= htmlspecialchars($tNome ?: '---') ?></span>
                        </div>
                        <div class="field">
                            <span class="field-label">CPF / CNPJ</span>
                            <span class="field-value"><?= !empty($tDoc) ? $fmtDoc($tDoc) : '---' ?></span>
                        </div>
                        <?php if ($tIM): ?>
                        <div class="field">
                            <span class="field-label">Inscricao Municipal</span>
                            <span class="field-value"><?= htmlspecialchars($tIM) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Endereco</span>
                            <span class="field-value"><?= htmlspecialchars(trim($tRua . ', ' . $tNumero . ' - ' . $tBairro)) ?: '---' ?></span>
                        </div>
                        <div class="field">
                            <span class="field-label">Cidade / UF</span>
                            <span class="field-value"><?= htmlspecialchars(trim($tCidade . '/' . $tUf . ' - CEP: ' . $tCep)) ?: '---' ?></span>
                        </div>
                        <?php if ($tTel || $tEmail): ?>
                        <div class="field">
                            <span class="field-label">Contato</span>
                            <span class="field-value"><?= htmlspecialchars(($tTel ?: '') . ($tTel && $tEmail ? ' / ' : '') . ($tEmail ?: '')) ?: '---' ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- DADOS DA NOTA -->
        <div class="section">
            <div class="section-header">Dados da Nota Fiscal</div>
            <div class="section-body">
                <div class="row">
                    <div class="col-3">
                        <div class="field">
                            <span class="field-label">Numero</span>
                            <span class="field-value"><?= htmlspecialchars($nfNumero ?: '---') ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <span class="field-label">Serie</span>
                            <span class="field-value"><?= htmlspecialchars($nfSerie ?: '---') ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <span class="field-label">Data Emissao</span>
                            <span class="field-value"><?= !empty($nfDataEmi) ? date('d/m/Y', strtotime($nfDataEmi)) : '---' ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <span class="field-label">Competencia</span>
                            <span class="field-value"><?= htmlspecialchars($nfCompetencia ?: (!empty($nfDataEmi) ? date('m/Y', strtotime($nfDataEmi)) : '---')) ?></span>
                        </div>
                    </div>
                </div>
                <?php if ($nfCodVerif): ?>
                <div class="row" style="margin-top:4px;">
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Codigo Verificacao</span>
                            <span class="field-value"><?= htmlspecialchars($nfCodVerif) ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($nfChave): ?>
                <div class="chave-box">
                    <span class="chave-label">Chave de Acesso da NFS-e</span>
                    <span class="chave-value"><?= htmlspecialchars($nfChave) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SERVICO PRESTADO / TRIBUTACAO -->
        <div class="section">
            <div class="section-header">Servico Prestado</div>
            <div class="section-body">
                <?php if ($nfTribNac || $nfTribMun): ?>
                <div class="row">
                    <?php if ($nfTribNac): ?>
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Cod. Tributacao Nacional</span>
                            <span class="field-value"><?= htmlspecialchars($nfTribNac) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($nfTribMun): ?>
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Cod. Tributacao Municipal</span>
                            <span class="field-value"><?= htmlspecialchars($nfTribMun) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($nfLocPrest || $nfLocEmi || $nfPais): ?>
                <div class="row" style="margin-top:4px;">
                    <?php if ($nfLocPrest || $nfLocEmi): ?>
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Local da Prestacao</span>
                            <span class="field-value"><?= htmlspecialchars($nfLocPrest ?: $nfLocEmi ?: '---') ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($nfPais): ?>
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Pais da Prestacao</span>
                            <span class="field-value"><?= htmlspecialchars($nfPais) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($nfRegime || $nfNatureza): ?>
                <div class="row" style="margin-top:4px;">
                    <?php if ($nfRegime): ?>
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Regime Tributario</span>
                            <span class="field-value"><?= htmlspecialchars($nfRegime) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($nfNatureza): ?>
                    <div class="col-6">
                        <div class="field">
                            <span class="field-label">Natureza Operacao</span>
                            <span class="field-value"><?= htmlspecialchars($nfNatureza) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($nfDesc): ?>
                <div style="margin-top:6px; border-top:1px solid #e8eaf6; padding-top:6px;">
                    <span class="field-label">Discriminacao dos Servicos</span>
                    <p style="font-size:9px; color:#2d2d2d; margin-top:2px;"><?= htmlspecialchars($nfDesc) ?></p>
                </div>
                <?php endif; ?>

                <?php if ($nfAliq): ?>
                <div style="margin-top:4px;">
                    <span class="field-label">Aliquota ISS</span>
                    <span class="field-value"><?= htmlspecialchars($nfAliq) ?>%</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- VALORES -->
        <div class="section">
            <div class="section-header">Valores</div>
            <div class="section-body">
                <table class="valores-table">
                    <tr>
                        <td class="label-col">Valor dos Servicos</td>
                        <td class="value-col"><?= $fmtMoeda($vServ) ?></td>
                    </tr>
                    <?php if ($vDed > 0): ?>
                    <tr class="deducao">
                        <td class="label-col">(-) Deducoes</td>
                        <td class="value-col"><?= $fmtMoeda($vDed) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="label-col">Base de Calculo</td>
                        <td class="value-col"><?= $fmtMoeda($vServ - $vDed) ?></td>
                    </tr>
                    <?php if ($vISS > 0): ?>
                    <tr class="deducao">
                        <td class="label-col">(-) ISS</td>
                        <td class="value-col"><?= $fmtMoeda($vISS) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vPIS > 0): ?>
                    <tr class="deducao">
                        <td class="label-col">(-) PIS</td>
                        <td class="value-col"><?= $fmtMoeda($vPIS) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vCOFINS > 0): ?>
                    <tr class="deducao">
                        <td class="label-col">(-) COFINS</td>
                        <td class="value-col"><?= $fmtMoeda($vCOFINS) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vIRRF > 0): ?>
                    <tr class="deducao">
                        <td class="label-col">(-) IRRF</td>
                        <td class="value-col"><?= $fmtMoeda($vIRRF) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vCSLL > 0): ?>
                    <tr class="deducao">
                        <td class="label-col">(-) CSLL</td>
                        <td class="value-col"><?= $fmtMoeda($vCSLL) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($vINSS > 0): ?>
                    <tr class="deducao">
                        <td class="label-col">(-) INSS / CPP</td>
                        <td class="value-col"><?= $fmtMoeda($vINSS) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="total">
                        <td class="label-col">Total Impostos</td>
                        <td class="value-col"><?= $fmtMoeda($vTotImpostos) ?></td>
                    </tr>
                    <tr class="liquido">
                        <td class="label-col">VALOR LIQUIDO</td>
                        <td class="value-col"><?= $fmtMoeda($vLiq > 0 ? $vLiq : ($vServ - $vTotImpostos)) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- QR CODE + RODAPE -->
        <?php if ($qrBase64): ?>
        <div class="qr-section">
            <img src="data:image/png;base64,<?= $qrBase64 ?>" alt="QR Code">
            <div class="qr-text">
                Escaneie para consultar a autenticidade no portal nacional
            </div>
        </div>
        <?php endif; ?>

        <div class="footer">
            <?php if ($n->os_id): ?>
            <strong>OS Nº <?= str_pad($n->os_id, 6, '0', STR_PAD_LEFT) ?></strong> &mdash;
            <?php endif; ?>
            Documento gerado eletronicamente &mdash;
            Emitido em <?= date('d/m/Y \\a\\s H:i:s') ?>
        </div>

    </div>
</body>
</html>
