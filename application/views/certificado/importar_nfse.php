<?php
/**
 * Importação de NFS-e
 */
?>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('certificado') ?>">Certificado</a> <span class="divider">/</span></li>
            <li class="active">Importar NFS-e</li>
        </ul>
    </div>
</div>

<!-- Importação Manual -->
<div class="row-fluid">
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-upload"></i></span>
                <h5>Importar XML da NFS-e</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= site_url('certificado/importar_nfse') ?>" enctype="multipart/form-data">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Como importar:</strong><br>
                        1. Acesse o sistema da prefeitura<br>
                        2. Baixe o XML da nota fiscal emitida<br>
                        3. Selecione o arquivo aqui e clique em Importar
                    </div>

                    <div class="control-group">
                        <label>Arquivo XML da NFS-e:</label>
                        <input type="file" name="xml_nfse" class="span12" accept=".xml" required />
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Importar Nota Fiscal
                    </button>

                </form>
            </div>
        </div>
    </div>

    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-link"></i></span>
                <h5>Integração Automática</h5>            </div>
            <div class="widget-content">
                <p><strong>O que acontece ao importar uma NFS-e:</strong></p>
                <ol>
                    <li>O sistema extrai os dados da nota fiscal</li>
                    <li>Identifica o valor bruto e os impostos retidos</li>
                    <li>Cria automaticamente um lançamento no sistema de impostos</li>
                    <li>Integra com o DRE como dedução de impostos</li>
                </ol>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Atenção:</strong><br>
                    Certifique-se de que o XML é da NFS-e emitida (venda) e não de NFS-e recebida (compra).
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notas Importadas -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-list"></i></span>
                <h5>Notas Fiscais Importadas</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (empty($notas['notas'])): ?>
                <div class="alert alert-info" style="margin: 20px;">
                    <i class="fas fa-info-circle"></i> Nenhuma nota fiscal importada no período atual.
                </div>
                <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Data Emissão</th>
                            <th>Data Importação</th>
                            <th class="text-right">Valor Total</th>
                            <th class="text-right">Impostos</th>
                            <th class="text-center">Situação</th>
                            <th class="text-center">Integrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notas['notas'] as $nota): ?>
                        <tr>
                            <td><?= $nota->numero ?></td>
                            <td><?= date('d/m/Y', strtotime($nota->data_emissao)) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($nota->data_importacao)) ?></td>
                            <td class="text-right">R$ <?= number_format($nota->valor_total, 2, ',', '.') ?></td>
                            <td class="text-right">R$ <?= number_format($nota->valor_impostos, 2, ',', '.') ?></td>
                            <td class="text-center">
                                <span class="label label-success"><?= $nota->situacao ?></span>
                            </td>
                            <td class="text-center">
                                <?= $nota->imposto_integrado ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-muted"></i>' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
