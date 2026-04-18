<?php
/**
 * Simulador de Impostos Simples Nacional
 */
?>

<style>
.resultado-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 8px;
    margin-top: 20px;
}
.resultado-box h3 {
    margin: 0 0 15px 0;
    text-align: center;
}
.valor-principal {
    font-size: 32px;
    font-weight: bold;
    text-align: center;
    margin: 10px 0;
}
.valor-secundario {
    font-size: 14px;
    opacity: 0.9;
}
.imposto-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}
.imposto-item:last-child {
    border-bottom: none;
}
.aliquota-info {
    font-size: 11px;
    opacity: 0.8;
}
</style>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('impostos') ?>">Impostos</a> <span class="divider">/</span></li>
            <li class="active">Simulador</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <!-- Formulário -->
    <div class="span5">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-calculator"></i></span>
                <h5>Simulador de Impostos</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= site_url('impostos/simulador') ?>" class="form-horizontal">

                    <div class="control-group">
                        <label class="control-label">Valor do Serviço:*</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">R$</span>
                                <input type="text" name="valor_bruto" class="span6" required placeholder="0,00" value="<?= $this->input->post('valor_bruto') ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Anexo:*</label>
                        <div class="controls">
                            <select name="anexo" class="span8" required>
                                <?php foreach ($anexos as $key => $nome): ?>
                                <option value="<?= $key ?>" <?= $this->input->post('anexo') == $key ? 'selected' : '' ?>><?= $nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Faixa:*</label>
                        <div class="controls">
                            <select name="faixa" class="span8" required>
                                <option value="1" <?= $this->input->post('faixa') == '1' ? 'selected' : '' ?>>1ª Faixa</option>
                                <option value="2" <?= $this->input->post('faixa') == '2' ? 'selected' : '' ?>>2ª Faixa</option>
                                <option value="3" <?= $this->input->post('faixa') == '3' ? 'selected' : '' ?>>3ª Faixa</option>
                                <option value="4" <?= $this->input->post('faixa') == '4' ? 'selected' : '' ?>>4ª Faixa</option>
                                <option value="5" <?= $this->input->post('faixa') == '5' ? 'selected' : '' ?>>5ª Faixa</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calculator"></i> Calcular Impostos
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Resultado -->
    <div class="span7">
        <?php if ($resultado): ?>
        <div class="resultado-box">
            <h3>Resultado da Simulação</h3>

            <div class="row-fluid">
                <div class="span6">
                    <div class="valor-principal">
                        R$ <?= number_format($resultado['valor_bruto'], 2, ',', '.') ?>
                        <div class="valor-secundario">VALOR BRUTO</div>
                    </div>
                </div>
                <div class="span6">
                    <div class="valor-principal">
                        R$ <?= number_format($resultado['valor_liquido'], 2, ',', '.') ?>
                        <div class="valor-secundario">VALOR LÍQUIDO (A RECEBER)</div>
                    </div>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.3); margin: 20px 0;">

            <h4 style="margin-bottom: 15px;">Desconto de Impostos: R$ <?= number_format($resultado['total_impostos'], 2, ',', '.') ?></h4>

            <div class="row-fluid">
                <div class="span6">
                    <div class="imposto-item">
                        <span>IRPJ <span class="aliquota-info">(<?= $resultado['aliquota_irpj'] ?>%)</span></span>
                        <span>R$ <?= number_format($resultado['irpj_valor'], 2, ',', '.') ?></span>
                    </div>
                    <div class="imposto-item">
                        <span>CSLL <span class="aliquota-info">(<?= $resultado['aliquota_csll'] ?>%)</span></span>
                        <span>R$ <?= number_format($resultado['csll_valor'], 2, ',', '.') ?></span>
                    </div>
                    <div class="imposto-item">
                        <span>COFINS <span class="aliquota-info">(<?= $resultado['aliquota_cofins'] ?>%)</span></span>
                        <span>R$ <?= number_format($resultado['cofins_valor'], 2, ',', '.') ?></span>
                    </div>
                </div>
                <div class="span6">
                    <div class="imposto-item">
                        <span>PIS <span class="aliquota-info">(<?= $resultado['aliquota_pis'] ?>%)</span></span>
                        <span>R$ <?= number_format($resultado['pis_valor'], 2, ',', '.') ?></span>
                    </div>
                    <div class="imposto-item">
                        <span>ISS <span class="aliquota-info">(<?= $resultado['aliquota_iss'] ?>%)</span></span>
                        <span>R$ <?= number_format($resultado['iss_valor'], 2, ',', '.') ?></span>
                    </div>
                    <div class="imposto-item" style="font-weight: bold;">
                        <span>TOTAL</span>
                        <span>R$ <?= number_format($resultado['total_impostos'], 2, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.3); margin: 20px 0;">

            <div class="text-center">
                <div style="font-size: 18px;">
                    <strong>Alíquota Nominal:</strong> <?= $resultado['aliquota_nominal'] ?>%
                </div>
                <div style="font-size: 12px; margin-top: 10px; opacity: 0.8;">
                    Base de cálculo: Simples Nacional - Anexo <?= $this->input->post('anexo') ?>, <?= $this->input->post('faixa') ?>ª Faixa
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="widget-box">
            <div class="widget-content">
                <div class="text-center" style="padding: 40px; color: #666;">
                    <i class="fas fa-calculator" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
                    <p>Preencha o formulário ao lado para simular o cálculo de impostos.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Exemplos -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-lightbulb"></i></span>
                <h5>Exemplos de Valores</h5>
            </div>
            <div class="widget-content">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Valor do Serviço</th>
                            <th>Alíquota</th>
                            <th>Total Impostos</th>
                            <th>Valor Líquido</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $exemplos = [500, 1000, 2500, 5000, 10000];
                        $aliquota_padrao = 6.0;
                        foreach ($exemplos as $exemplo):
                            $impostos = $exemplo * ($aliquota_padrao / 100);
                            $liquido = $exemplo - $impostos;
                        ?>
                        <tr>
                            <td>R$ <?= number_format($exemplo, 2, ',', '.') ?></td>
                            <td><?= $aliquota_padrao ?>%</td>
                            <td>R$ <?= number_format($impostos, 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($liquido, 2, ',', '.') ?></td>
                            <td>
                                <a href="<?= site_url('impostos/simulador') ?>" class="btn btn-mini">Usar este valor</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
