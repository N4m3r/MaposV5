<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        .invoice-box {
            max-width: 1100px;
            margin: auto;
            padding: 10px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .payment-section {
            background-color: #f8f9fa;
            border: 2px solid #0073b7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .payment-title {
            color: #0073b7;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .pix-section {
            background-color: #f8f9fa;
            border: 2px solid #00bfa5;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .pix-title {
            color: #00bfa5;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .qr-code {
            max-width: 250px;
            margin: 15px auto;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .codigo-barras {
            background-color: #fff;
            border: 1px dashed #0073b7;
            padding: 10px;
            margin: 15px 0;
            font-family: monospace;
            font-size: 14px;
            word-break: break-all;
            text-align: center;
        }

        .pix-code {
            background-color: #fff;
            border: 1px dashed #00bfa5;
            padding: 10px;
            margin: 15px 0;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
        }

        .instructions {
            color: #666;
            font-size: 14px;
            margin-top: 15px;
        }

        .btn-pdf {
            display: inline-block;
            background-color: #0073b7;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }

        .description-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin: 15px 0;
            font-size: 14px;
            white-space: pre-line;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }

        .justify {
            text-align: justify;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="<?= $emitente->url_logo; ?>" style="width:100%; max-width:120px;">
                            </td>
                            <td style="text-align: right">
                                <?php if ($cobranca->payment_method === 'boleto') : ?>
                                    Cobrança Boleto #<?= $cobranca->idCobranca ?>
                                <?php elseif ($cobranca->payment_method === 'pix') : ?>
                                    Cobrança PIX #<?= $cobranca->idCobranca ?>
                                <?php else : ?>
                                    Cobrança #<?= $cobranca->idCobranca ?>
                                <?php endif; ?>
                                <br>
                                Vencimento: <?= $cobranca->expire_at ? date('d/m/Y', strtotime($cobranca->expire_at)) : ''; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                <strong>Cliente:</strong> <?= $cobranca->nomeCliente ?><br>
                                <?= $cobranca->rua ?>, <?= $cobranca->numero ?>, <?= $cobranca->bairro ?><br>
                                <?= $cobranca->cidade ?> - <?= $cobranca->estado ?> <br>
                                <?= $cobranca->email ?> <br>
                            </td>

                            <td style="text-align: right">
                                <strong>Emitente:</strong><br>
                                <?= $emitente->nome; ?> <br>
                                <?php if ($emitente->cnpj) : ?>
                                    CNPJ: <?= $emitente->cnpj; ?><br>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Descrição detalhada -->
            <?php if (!empty($cobranca->message)) : ?>
            <tr>
                <td colspan="4">
                    <strong>Descrição:</strong>
                    <div class="description-box">
                        <?= nl2br(htmlspecialchars($cobranca->message)); ?>
                    </div>
                </td>
            </tr>
            <?php endif; ?>

            <!-- Seção Boleto Bancário -->
            <?php if ($cobranca->payment_method === 'boleto' || $cobranca->pdf) : ?>
            <tr>
                <td colspan="4">
                    <div class="payment-section">
                        <div class="payment-title"><i class="bx bx-barcode"></i> Pagamento via Boleto Bancário</div>

                        <?php if ($cobranca->linha_digitavel) : ?>
                            <div class="codigo-barras">
                                <strong>Linha Digitável:</strong><br>
                                <?= $cobranca->linha_digitavel ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($cobranca->pdf) : ?>
                            <a href="<?= $cobranca->pdf ?>" target="_blank" class="btn-pdf">
                                📄 Visualizar / Imprimir Boleto
                            </a>
                        <?php endif; ?>

                        <div class="instructions">
                            <p><strong>Como pagar:</strong></p>
                            <ol style="text-align: left; display: inline-block;">
                                <li>Imprima o boleto ou use a linha digitável</li>
                                <li>Pague em qualquer banco ou casa lotérica</li>
                                <li>Também pode pagar pelo internet banking</li>
                                <li>O pagamento pode levar até 2 dias úteis para ser compensado</li>
                            </ol>
                            <p style="margin-top: 15px; color: #d9534f;">
                                <strong>Atenção: O boleto vence em <?= date('d/m/Y', strtotime($cobranca->expire_at)); ?></strong>
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endif; ?>

            <!-- Seção PIX -->
            <?php if ($cobranca->pix_code || ($cobranca->payment_method === 'pix' && $cobranca->link)) : ?>
            <tr>
                <td colspan="4">
                    <div class="pix-section">
                        <div class="pix-title"><i class="bx bx-qr-scan"></i> Pagamento via PIX</div>

                        <?php if ($cobranca->link) : ?>
                            <div>
                                <img src="<?= $cobranca->link ?>" alt="QR Code PIX" class="qr-code">
                            </div>
                        <?php endif; ?>

                        <?php if ($cobranca->pix_code) : ?>
                            <div class="pix-code">
                                <strong>Código PIX (Copia e Cola):</strong><br>
                                <?= $cobranca->pix_code ?>
                            </div>
                        <?php elseif ($cobranca->barcode && $cobranca->payment_method === 'pix') : ?>
                            <div class="pix-code">
                                <strong>Código PIX (Copia e Cola):</strong><br>
                                <?= $cobranca->barcode ?>
                            </div>
                        <?php endif; ?>

                        <div class="instructions">
                            <p><strong>Como pagar:</strong></p>
                            <ol style="text-align: left; display: inline-block;">
                                <li>Abra o aplicativo do seu banco</li>
                                <li>Escolha a opção PIX ou Pagamento</li>
                                <li>Escaneie o QR Code acima ou cole o código PIX</li>
                                <li>Confirme o pagamento</li>
                            </ol>
                            <p style="margin-top: 15px; color: #00bfa5;">
                                <strong>O pagamento será confirmado em poucos segundos!</strong>
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endif; ?>

            <tr class="heading">
                <td colspan="3"></td>
                <td style="text-align: center">
                    <strong>Total: R$ <?= number_format($cobranca->total / 100, 2, ',', '.') ?></strong>
                </td>
            </tr>

            <!-- Informações adicionais -->
            <tr>
                <td colspan="4" style="text-align: center; padding-top: 20px; font-size: 12px; color: #666;">
                    <p>
                        Pagamento processado por <strong>Banco Cora</strong><br>
                        ID da transação: <?= $cobranca->charge_id ?>
                    </p>
                    <p>
                        Em caso de dúvidas, entre em contato com <?= $emitente->nome; ?><br>
                        E-mail: <?= $emitente->email; ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>