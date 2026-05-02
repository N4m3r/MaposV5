<?php
/**
 * Impressao A4 da NFSe Importada
 */
$fmtMoney = function ($v) {
    return 'R$ ' . number_format(floatval($v), 2, ',', '.');
};

$fmtDoc = function ($doc) {
    $doc = preg_replace('/\D/', '', $doc);
    if (strlen($doc) == 14) {
        return substr($doc, 0, 2) . '.' . substr($doc, 2, 3) . '.' . substr($doc, 5, 3) . '/' . substr($doc, 8, 4) . '-' . substr($doc, 12, 2);
    }
    if (strlen($doc) == 11) {
        return substr($doc, 0, 3) . '.' . substr($doc, 3, 3) . '.' . substr($doc, 6, 3) . '-' . substr($doc, 9, 2);
    }
    return $doc;
};

$e = $emitente ?? null;
$c = $cliente ?? null;
$n = $nota ?? null;
$logo = $logo_url ?? base_url('assets/img/logo.png');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>NFS-e <?= htmlspecialchars($n->numero ?? '') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <style>
        * { box-sizing: border-box; }
        body {
            background: #e5e5e5;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        .header-top {
            border: 1px solid #333;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .header-top .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-top .logo-area img {
            max-height: 60px;
            max-width: 120px;
        }
        .header-top .title-area {
            text-align: center;
            flex: 1;
        }
        .header-top .title-area h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header-top .title-area h3 {
            margin: 4px 0 0;
            font-size: 14px;
            font-weight: bold;
            color: #555;
        }
        .header-top .numero-area {
            text-align: right;
        }
        .header-top .numero-area .label-num {
            font-size: 10px;
            color: #666;
        }
        .header-top .numero-area .numero {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }
        .box {
            border: 1px solid #333;
            margin-bottom: 6px;
        }
        .box-header {
            background: #f5f5f5;
            border-bottom: 1px solid #333;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .box-body {
            padding: 8px;
        }
        .row-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .col-6 { flex: 1 1 48%; }
        .col-4 { flex: 1 1 31%; }
        .col-3 { flex: 1 1 23%; }
        .col-12 { flex: 1 1 100%; }
        .field { margin-bottom: 4px; }
        .field label {
            display: block;
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        .field span {
            font-size: 12px;
            font-weight: bold;
            color: #000;
        }
        .table-valores {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .table-valores th, .table-valores td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        .table-valores th {
            background: #f5f5f5;
            font-size: 11px;
        }
        .table-valores td {
            font-size: 12px;
        }
        .text-right { text-align: right !important; }
        .total-box {
            border: 1px solid #333;
            padding: 10px;
            text-align: right;
            margin-top: 6px;
        }
        .total-box .label-total {
            font-size: 11px;
            color: #666;
        }
        .total-box .valor-total {
            font-size: 20px;
            font-weight: bold;
            color: #000;
        }
        .footer-info {
            margin-top: 12px;
            font-size: 10px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        .chave-box {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
            font-family: monospace;
            font-size: 11px;
            letter-spacing: 1px;
            margin-top: 6px;
            word-break: break-all;
        }
        .status-box {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #333;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
                padding: 10mm;
            }
            .no-print { display: none !important; }
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
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir
    </button>

    <div class="page">
        <!-- HEADER -->
        <div class="header-top">
            <div class="logo-area">
                <img src="<?= $logo ?>" alt="Logo" onerror="this.style.display='none'">
                <div>
                    <strong style="font-size:13px"><?= htmlspecialchars($e->nome ?? $e->razaosocial ?? 'PRESTADOR') ?></strong><br>
                    <small style="color:#666"><?= !empty($e->cnpj) ? 'CNPJ: ' . $fmtDoc($e->cnpj) : '' ?></small>
                </div>
            </div>
            <div class="title-area">
                <h2>NOTA FISCAL DE SERVICO</h2>
                <h3>ELETRONICA - NFS-e</h3>
            </div>
            <div class="numero-area">
                <div class="label-num">Numero</div>
                <div class="numero"><?= htmlspecialchars($n->numero ?? '---') ?></div>
                <div style="margin-top:4px">
                    <span class="status-box"><?= htmlspecialchars($n->situacao ?? 'Importada') ?></span>
                </div>
            </div>
        </div>

        <!-- PRESTADOR -->
        <div class="box">
            <div class="box-header">Dados do Prestador de Servicos</div>
            <div class="box-body">
                <div class="row-flex">
                    <div class="col-6">
                        <div class="field">
                            <label>Razao Social / Nome</label>
                            <span><?= htmlspecialchars($e->nome ?? $e->razaosocial ?? '---') ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>CNPJ</label>
                            <span><?= !empty($e->cnpj) ? $fmtDoc($e->cnpj) : '---' ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>Inscricao Municipal</label>
                            <span><?= htmlspecialchars($e->ie ?? $e->inscricao_municipal ?? '---') ?></span>
                        </div>
                    </div>
                </div>
                <div class="row-flex" style="margin-top:4px">
                    <div class="col-6">
                        <div class="field">
                            <label>Endereco</label>
                            <span>
                                <?= htmlspecialchars(trim(($e->rua ?? '') . ', ' . ($e->numero ?? '') . ' - ' . ($e->bairro ?? ''))) ?>
                                <?= !empty($e->cidade) ? ' - ' . htmlspecialchars($e->cidade) : '' ?>
                                <?= !empty($e->uf) ? '/' . htmlspecialchars($e->uf) : '' ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>CEP</label>
                            <span><?= htmlspecialchars($e->cep ?? '---') ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>Telefone</label>
                            <span><?= htmlspecialchars($e->telefone ?? '---') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOMADOR -->
        <div class="box">
            <div class="box-header">Dados do Tomador de Servicos</div>
            <div class="box-body">
                <div class="row-flex">
                    <div class="col-6">
                        <div class="field">
                            <label>Razao Social / Nome</label>
                            <span><?= htmlspecialchars($c->nomeCliente ?? $c->nome ?? '---') ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>CPF / CNPJ</label>
                            <span><?= !empty($c->documento ?? $c->cnpj ?? '') ? $fmtDoc($c->documento ?? $c->cnpj) : '---' ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>Inscricao Municipal</label>
                            <span><?= htmlspecialchars($c->inscricao_municipal ?? '---') ?></span>
                        </div>
                    </div>
                </div>
                <div class="row-flex" style="margin-top:4px">
                    <div class="col-6">
                        <div class="field">
                            <label>Endereco</label>
                            <span>
                                <?= htmlspecialchars(trim(($c->rua ?? '') . ', ' . ($c->numero ?? '') . ' - ' . ($c->bairro ?? ''))) ?>
                                <?= !empty($c->cidade) ? ' - ' . htmlspecialchars($c->cidade) : '' ?>
                                <?= !empty($c->estado ?? $c->uf ?? '') ? '/' . htmlspecialchars($c->estado ?? $c->uf) : '' ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>CEP</label>
                            <span><?= htmlspecialchars($c->cep ?? '---') ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>Telefone / E-mail</label>
                            <span><?= htmlspecialchars(($c->telefone ?? '') . (!empty($c->email) ? ' / ' . $c->email : '')) ?: '---' ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NOTA -->
        <div class="box">
            <div class="box-header">Dados da Nota Fiscal de Servico</div>
            <div class="box-body">
                <div class="row-flex">
                    <div class="col-3">
                        <div class="field">
                            <label>Numero da NFS-e</label>
                            <span><?= htmlspecialchars($n->numero ?? '---') ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>Serie</label>
                            <span><?= htmlspecialchars($n->serie ?? '---') ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>Data Emissao</label>
                            <span><?= !empty($n->data_emissao) ? date('d/m/Y', strtotime($n->data_emissao)) : '---' ?></span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="field">
                            <label>Data Importacao</label>
                            <span><?= !empty($n->data_importacao) ? date('d/m/Y H:i', strtotime($n->data_importacao)) : '---' ?></span>
                        </div>
                    </div>
                </div>
                <?php if (!empty($n->chave_acesso)): ?>
                <div class="chave-box">
                    <label style="font-size:9px;color:#666;display:block;margin-bottom:2px">CHAVE DE ACESSO</label>
                    <?= htmlspecialchars($n->chave_acesso) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- VALORES -->
        <div class="box">
            <div class="box-header">Discriminacao dos Servicos e Valores</div>
            <div class="box-body">
                <table class="table-valores">
                    <thead>
                        <tr>
                            <th style="width:60%">Descricao</th>
                            <th class="text-right" style="width:20%">Valor dos Servicos</th>
                            <th class="text-right" style="width:20%">Valor dos Impostos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Importacao de XML de NFS-e vinculada a OS #<?= htmlspecialchars($n->os_id ?? '---') ?></td>
                            <td class="text-right"><?= $fmtMoney($n->valor_total ?? 0) ?></td>
                            <td class="text-right"><?= $fmtMoney($n->valor_impostos ?? 0) ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="total-box">
                    <div class="label-total">VALOR TOTAL DA NOTA</div>
                    <div class="valor-total"><?= $fmtMoney($n->valor_total ?? 0) ?></div>
                </div>
                <div style="margin-top:6px; text-align:right; font-size:11px; color:#555">
                    <strong>Valor Liquido:</strong> <?= $fmtMoney(floatval($n->valor_total ?? 0) - floatval($n->valor_impostos ?? 0)) ?>
                </div>
            </div>
        </div>

        <!-- RODAPE -->
        <div class="footer-info">
            Documento gerado eletronicamente. Consulte a autenticidade no portal da prefeitura ou no site da NFS-e Nacional.<br>
            <?php if (!empty($e->nome ?? $e->razaosocial)): ?>
                <?= htmlspecialchars($e->nome ?? $e->razaosocial) ?> &mdash;
            <?php endif; ?>
            Emitido em <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>
</body>
</html>
