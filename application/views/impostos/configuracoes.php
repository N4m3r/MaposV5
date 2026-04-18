<?php
/**
 * Configurações de Impostos Simples Nacional
 */
?>

<style>
.aliquota-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    border-left: 4px solid #3498db;
}
.aliquota-card h5 {
    margin: 0 0 10px 0;
    color: #2c3e50;
}
.aliquota-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
}
.aliquota-item {
    text-align: center;
    padding: 10px;
    background: white;
    border-radius: 4px;
    font-size: 12px;
}
.aliquota-item .label {
    display: block;
    color: #666;
    font-size: 10px;
    margin-bottom: 3px;
}
.aliquota-item .value {
    font-weight: bold;
    color: #2c3e50;
}
</style>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('impostos') ?>">Impostos</a> <span class="divider">/</span></li>
            <li class="active">Configurações</li>
        </ul>
    </div>
</div>

<!-- Configurações Principais -->
<div class="row-fluid">
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-cogs"></i></span>
                <h5>Configurações do Sistema</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= site_url('impostos/configuracoes') ?>" class="form-horizontal">

                    <div class="control-group">
                        <label class="control-label">Anexo Simples:*</label>
                        <div class="controls">
                            <select name="anexo_padrao" class="span8" required>
                                <?php foreach ($anexos as $key => $nome): ?>
                                <option value="<?= $key ?>" <?= $configs['anexo_padrao'] == $key ? 'selected' : '' ?>><?= $nome ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-inline">Anexo do Simples Nacional da empresa</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Faixa Atual:*</label>
                        <div class="controls">
                            <select name="faixa_atual" class="span4" required>
                                <option value="1" <?= $configs['faixa_atual'] == '1' ? 'selected' : '' ?>>1ª Faixa (até R$ 180.000)</option>
                                <option value="2" <?= $configs['faixa_atual'] == '2' ? 'selected' : '' ?>>2ª Faixa (até R$ 360.000)</option>
                                <option value="3" <?= $configs['faixa_atual'] == '3' ? 'selected' : '' ?>>3ª Faixa (até R$ 720.000)</option>
                                <option value="4" <?= $configs['faixa_atual'] == '4' ? 'selected' : '' ?>>4ª Faixa (até R$ 1.800.000)</option>
                                <option value="5" <?= $configs['faixa_atual'] == '5' ? 'selected' : '' ?>>5ª Faixa (até R$ 4.800.000)</option>
                            </select>
                            <span class="help-inline">Faixa de faturamento atual</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">ISS Municipal:*</label>
                        <div class="controls">
                            <div class="input-append">
                                <input type="number" name="iss_municipal" class="span3" step="0.01" min="0" max="99" value="<?= htmlspecialchars($configs['iss_municipal']) ?>" required />
                                <span class="add-on">%</span>
                            </div>
                            <span class="help-inline">Alíquota de ISS da cidade</span>
                        </div>
                    </div>

                    <hr style="margin: 20px 0;">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Códigos de Tributação para NFS-e</strong><br>
                        Configuração específica para emissão de Nota Fiscal de Serviços Eletrônica (NFS-e).
                    </div>

                    <div class="control-group">
                        <label class="control-label">Cód. Tributação Nacional:*</label>
                        <div class="controls">
                            <input type="text" name="codigo_tributacao_nacional" class="span3" value="<?= $configs['codigo_tributacao_nacional'] ?>" required maxlength="10" />
                            <span class="help-inline">Código LC 116/2003 (ex: 010701)</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Cód. Tributação Municipal:*</label>
                        <div class="controls">
                            <input type="text" name="codigo_tributacao_municipal" class="span3" value="<?= $configs['codigo_tributacao_municipal'] ?>" required maxlength="10" />
                            <span class="help-inline">Código do serviço na cidade</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Descrição do Serviço:*</label>
                        <div class="controls">
                            <textarea name="descricao_servico" class="span8" rows="3" required><?= $configs['descricao_servico'] ?></textarea>
                            <span class="help-inline">Descrição completa para a nota fiscal</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="retencao_automatica" value="1" <?= $configs['retencao_automatica'] ? 'checked' : '' ?> />
                                <strong>Habilitar retenção automática em novos boletos</strong>
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="dre_integracao" value="1" <?= $configs['dre_integracao'] ? 'checked' : '' ?> />
                                <strong>Integrar retenções automaticamente com DRE</strong>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                        <a href="<?= site_url('impostos') ?>" class="btn">Voltar</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                <h5>Informações</h5>
            </div>
            <div class="widget-content">
                <p><strong>Como funciona a retenção automática:</strong></p>
                <ol>
                    <li>Na geração de um novo boleto, o sistema calcula automaticamente os impostos sobre o valor do serviço</li>
                    <li>O valor bruto é lançado no DRE como receita</li>
                    <li>Os impostos calculados são retidos e lançados como deduções no DRE</li>
                    <li>O cliente paga o valor líquido (bruto menos impostos)</li>
                </ol>

                <hr>

                <p><strong>Anexos do Simples Nacional:</strong></p>
                <ul>
                    <li><strong>Anexo III:</strong> Prestadores de serviços em geral, incluindo manutenção, assistência técnica e consultoria</li>
                    <li><strong>Anexo IV:</strong> Construção civil e empresas que recolhem ISS para o município próprio</li>
                </ul>

                <p><strong>Impostos calculados:</strong></p>
                <ul>
                    <li>IRPJ (Imposto de Renda Pessoa Jurídica)</li>
                    <li>CSLL (Contribuição Social sobre Lucro Líquido)</li>
                    <li>COFINS (Contribuição para Financiamento da Seguridade Social)</li>
                    <li>PIS (Programa de Integração Social)</li>
                    <li>ISS (Imposto Sobre Serviços) - conforme alíquota municipal</li>
                </ul>

                <hr>

                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <strong>Configuração Atual de NFS-e:</strong><br>
                    <b>Código Nacional:</b> <?= $configs['codigo_tributacao_nacional'] ?><br>
                    <b>Código Municipal:</b> <?= $configs['codigo_tributacao_municipal'] ?><br>
                    <b>Descrição:</b> <?= $configs['descricao_servico'] ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alíquotas Anexo III -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-table"></i></span>
                <h5>Alíquotas Simples Nacional - Anexo III (Serviços)</h5>
            </div>
            <div class="widget-content">
                <?php foreach ($aliquotas_iii as $a): ?>
                <div class="aliquota-card">
                    <h5><?= $a->faixa ?>ª Faixa - Alíquota: <?= number_format($a->aliquota_nominal, 2) ?>%</h5>
                    <div class="aliquota-grid">
                        <div class="aliquota-item">
                            <span class="label">IRPJ</span>
                            <span class="value"><?= number_format($a->irpj, 2) ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">CSLL</span>
                            <span class="value"><?= number_format($a->csll, 2) ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">COFINS</span>
                            <span class="value"><?= number_format($a->cofins, 2) ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">PIS</span>
                            <span class="value"><?= number_format($a->pis, 2) ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">ISS</span>
                            <span class="value"><?= number_format($a->iss, 2) ?>%</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Alíquotas Anexo IV -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-table"></i></span>
                <h5>Alíquotas Simples Nacional - Anexo IV (Construção e ISS Próprio)</h5>
            </div>
            <div class="widget-content">
                <?php foreach ($aliquotas_iv as $a): ?>
                <div class="aliquota-card" style="border-left-color: #e67e22;">
                    <h5><?= $a->faixa ?>ª Faixa - Alíquota: <?= number_format($a->aliquota_nominal, 2) ?>%</h5>
                    <div class="aliquota-grid">
                        <div class="aliquota-item">
                            <span class="label">IRPJ</span>
                            <span class="value"><?= number_format($a->irpj, 2) ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">CSLL</span>
                            <span class="value"><?= number_format($a->csll, 2) ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">COFINS</span>
                            <span class="value"><?= number_format($a->cofins, 2) ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">PIS</span>
                            <span class="value"><?= number_format($a->pis, 2) ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">ISS</span>
                            <span class="value"><?= number_format($a->iss, 2) ?>%</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
