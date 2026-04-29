<?php
/**
 * Configuracoes de Impostos Simples Nacional
 * Adaptado ao tema escuro MapOS v5
 */

// Helper para formatar CNPJ
function fmtCnpj($cnpj) {
    $c = preg_replace('/\D/', '', $cnpj ?? '');
    if (strlen($c) === 14) {
        return substr($c, 0, 2) . '.' . substr($c, 2, 3) . '.' . substr($c, 5, 3) . '/' . substr($c, 8, 4) . '-' . substr($c, 12, 2);
    }
    return $cnpj;
}
?&gt;

<style>
.impostos-container .aliquota-card {
    background: var(--dark-0, #191a22);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    border-left: 4px solid #1086dd;
    border: 1px solid var(--dark-2, #272835);
}
.impostos-container .aliquota-card.ativo {
    border-left: 4px solid #28a745;
    background: rgba(40, 167, 69, 0.08);
}
.impostos-container .aliquota-card h5 {
    margin: 0 0 10px 0;
    color: var(--title, #d4d8e0);
    font-size: 14px;
}
.impostos-container .aliquota-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
}
.impostos-container .aliquota-item {
    text-align: center;
    padding: 10px;
    background: var(--dark-1, #14141a);
    border-radius: 4px;
    font-size: 12px;
    border: 1px solid var(--dark-2, #272835);
}
.impostos-container .aliquota-item .label {
    display: block;
    color: var(--branco, #caced8);
    font-size: 10px;
    margin-bottom: 3px;
    opacity: 0.7;
}
.impostos-container .aliquota-item .value {
    font-weight: bold;
    color: var(--title, #d4d8e0);
}
.impostos-container .certificado-card {
    background: linear-gradient(135deg, rgba(16,134,221,0.15) 0%, rgba(16,134,221,0.05) 100%);
    border: 1px solid rgba(16,134,221,0.3);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    color: var(--branco, #caced8);
}
.impostos-container .certificado-card.sem-certificado {
    background: linear-gradient(135deg, rgba(255,193,7,0.15) 0%, rgba(255,193,7,0.05) 100%);
    border-color: rgba(255,193,7,0.3);
}
.impostos-container .certificado-info {
    margin: 0;
    font-size: 13px;
    line-height: 1.8;
}
.impostos-container .certificado-info strong {
    display: inline-block;
    width: 140px;
    color: var(--title, #d4d8e0);
}
.impostos-container .badge-anexo {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    background: #28a745;
    color: #fff;
}
.impostos-container .badge-optante {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    background: #1086dd;
    color: #fff;
}
.impostos-container .section-title {
    color: var(--title, #d4d8e0);
    margin-top: 20px;
    margin-bottom: 10px;
    font-size: 16px;
}
.impostos-container .widget-box {
    background: var(--wid-dark, #1c1d26);
    border-color: var(--dark-2, #272835);
}
.impostos-container .widget-title {
    background: var(--dark-0, #191a22);
    border-bottom: 1px solid var(--dark-2, #272835);
    color: var(--title, #d4d8e0);
}
.impostos-container .widget-content {
    background: var(--wid-dark, #1c1d26);
    color: var(--branco, #caced8);
}
.impostos-container .control-label {
    color: var(--branco, #caced8);
}
.impostos-container .help-inline {
    color: var(--branco, #caced8);
    opacity: 0.7;
}
.impostos-container input, .impostos-container select, .impostos-container textarea {
    background: var(--dark-1, #14141a) !important;
    border-color: var(--dark-2, #272835) !important;
    color: var(--branco, #caced8) !important;
}
.impostos-container .add-on {
    background: var(--dark-1, #14141a);
    border-color: var(--dark-2, #272835);
    color: var(--branco, #caced8);
}
.impostos-container .alert-info {
    background: rgba(16,134,221,0.15);
    border-color: rgba(16,134,221,0.3);
    color: #1086dd;
}
.impostos-container .alert-success {
    background: rgba(40,167,69,0.15);
    border-color: rgba(40,167,69,0.3);
    color: #28a745;
}
.impostos-container .table {
    background: var(--dark-0, #191a22);
    border-color: var(--dark-2, #272835);
    color: var(--branco, #caced8);
}
.impostos-container .table th {
    border-color: var(--dark-2, #272835);
    color: var(--title, #d4d8e0);
    background: var(--dark-1, #14141a);
}
.impostos-container .table td {
    border-color: var(--dark-2, #272835);
    color: var(--branco, #caced8);
}
.impostos-container .faixa-badge {
    display: inline-block;
    background: #1086dd;
    color: #fff;
    padding: 1px 6px;
    border-radius: 3px;
    font-size: 10px;
    margin-left: 8px;
    vertical-align: middle;
}
</style>

<div class="impostos-container">

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('impostos') ?>">Impostos</a> <span class="divider">/</span></li>
            <li class="active">Configuracoes</li>
        </ul>
    </div>
</div>

<!-- Informacoes do Certificado Digital -->
<div class="row-fluid">
    <div class="span12">
        <div id="certificado-card" class="certificado-card <?= $certificado_info ? '' : 'sem-certificado' ?>">
            <?php if ($certificado_info): ?>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                    <div>
                        <h5 style="margin: 0 0 8px 0; color: var(--title, #d4d8e0);">
                            <i class="fas fa-certificate" style="color: #28a745;"></i> Certificado Digital Ativo
                        </h5>
                        <p class="certificado-info">
                            <strong>Razao Social:</strong> <?= htmlspecialchars($certificado_info['razao_social']) ?><br>
                            <strong>CNPJ:</strong> <?= fmtCnpj($certificado_info['cnpj']) ?><br>
                            <strong>Tipo:</strong> <?= $certificado_info['tipo'] ?>
                            <?php if (!empty($certificado_info['validade'])): ?> <span style="opacity:0.7;">| Validade: <?= date('d/m/Y', strtotime($certificado_info['validade'])) ?></span>
                            <?php endif; ?><br>
                            <?php if (!empty($certificado_info['anexo_sugerido'])): ?>
                                <strong>Anexo Sugerido:</strong> <span class="badge-anexo">Anexo <?= $certificado_info['anexo_sugerido'] ?></span>
                                <?php if (!empty($certificado_info['cnae_descricao'])): ?>
                                    <span style="opacity:0.7;"> (<?= htmlspecialchars($certificado_info['cnae_descricao']) ?>)</span>
                                <?php endif; ?><br>
                            <?php endif; ?>
                            <?php if (isset($certificado_info['optante_simples']) && $certificado_info['optante_simples']): ?>
                                <strong>Simples Nacional:</strong> <span class="badge-optante">Optante</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div style="margin-top: 10px;">
                        <button type="button" class="btn btn-success" id="btn-preencher-certificado">
                            <i class="fas fa-sync-alt"></i> Preencher Automaticamente
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                    <div>
                        <h5 style="margin: 0 0 8px 0; color: var(--title, #d4d8e0);">
                            <i class="fas fa-exclamation-triangle" style="color: #ffc107;"></i> Nenhum Certificado Digital Configurado
                        </h5>
                        <p style="margin: 0; color: var(--branco, #caced8); opacity: 0.7; font-size: 13px;">
                            Configure um certificado digital para preencher automaticamente os dados tributarios.
                            <br>Cadastre em <a href="<?= site_url('certificado') ?>">Certificado Digital</a>.
                        </p>
                    </div>
                    <div style="margin-top: 10px;">
                        <a href="<?= site_url('certificado') ?>" class="btn btn-warning">
                            <i class="fas fa-search"></i> Buscar Certificado
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Configuracoes Principais -->
<div class="row-fluid">
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-cogs"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Configuracoes do Sistema</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= site_url('impostos/configuracoes') ?>" class="form-horizontal" id="form-config-impostos">

                    <div class="control-group">
                        <label class="control-label">Anexo Simples:*</label>
                        <div class="controls">
                            <select name="anexo_padrao" id="select-anexo" class="span8" required>
                                <?php foreach ($anexos as $key => $nome): ?
                                <option value="<?= $key ?>" <?= $configs['anexo_padrao'] == $key ? 'selected' : '' ?>><?= $nome ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-inline">Anexo do Simples Nacional da empresa</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Faixa Atual:*</label>
                        <div class="controls">
                            <select name="faixa_atual" id="select-faixa" class="span4" required>
                                <option value="1" <?= $configs['faixa_atual'] == '1' ? 'selected' : '' ?>>1a Faixa (ate R$ 180.000)</option>
                                <option value="2" <?= $configs['faixa_atual'] == '2' ? 'selected' : '' ?>>2a Faixa (ate R$ 360.000)</option>
                                <option value="3" <?= $configs['faixa_atual'] == '3' ? 'selected' : '' ?>>3a Faixa (ate R$ 720.000)</option>
                                <option value="4" <?= $configs['faixa_atual'] == '4' ? 'selected' : '' ?>>4a Faixa (ate R$ 1.800.000)</option>
                                <option value="5" <?= $configs['faixa_atual'] == '5' ? 'selected' : '' ?>>5a Faixa (ate R$ 4.800.000)</option>
                            </select>
                            <span class="help-inline">Faixa de faturamento anual</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">ISS Municipal:*</label>
                        <div class="controls">
                            <div class="input-append">
                                <input type="number" name="iss_municipal" id="input-iss" class="span3" step="0.01" min="0" max="5" value="<?= htmlspecialchars($configs['iss_municipal']) ?>" required />
                                <span class="add-on">%</span>
                            </div>
                            <span class="help-inline">Aliquota de ISS do municipio</span>
                        </div>
                    </div>

                    <hr style="margin: 20px 0; border-color: var(--dark-2, #272835);">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Codigos de Tributacao para NFS-e</strong><br>
                        Configuracao especifica para emissao de Nota Fiscal de Servicos Eletronica (NFS-e).
                    </div>

                    <div class="control-group">
                        <label class="control-label">Cod. Tributacao Nacional:*</label>
                        <div class="controls">
                            <input type="text" name="codigo_tributacao_nacional" id="input-codigo-nacional" class="span3" value="<?= htmlspecialchars($configs['codigo_tributacao_nacional']) ?>" required maxlength="10" />
                            <span class="help-inline">Codigo LC 116/2003 (ex: 010701)</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Cod. Tributacao Municipal:*</label>
                        <div class="controls">
                            <input type="text" name="codigo_tributacao_municipal" id="input-codigo-municipal" class="span3" value="<?= htmlspecialchars($configs['codigo_tributacao_municipal']) ?>" required maxlength="10" />
                            <span class="help-inline">Codigo do servico na cidade</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Descricao do Servico:*</label>
                        <div class="controls">
                            <textarea name="descricao_servico" id="input-descricao" class="span8" rows="3" required><?= htmlspecialchars($configs['descricao_servico']) ?></textarea>
                            <span class="help-inline">Descricao completa para a nota fiscal</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox" style="color: var(--branco, #caced8);">
                                <input type="checkbox" name="retencao_automatica" value="1" <?= $configs['retencao_automatica'] ? 'checked' : '' ?> />
                                <strong>Habilitar retencao automatica em novos boletos</strong>
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox" style="color: var(--branco, #caced8);">
                                <input type="checkbox" name="dre_integracao" value="1" <?= $configs['dre_integracao'] ? 'checked' : '' ?> />
                                <strong>Integrar retencoes automaticamente com DRE</strong>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions" style="background: var(--dark-0, #191a22); border-color: var(--dark-2, #272835);">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Configuracoes
                        </button>
                        <a href="<?= site_url('impostos') ?>" class="btn" style="background: var(--dark-1, #14141a); border-color: var(--dark-2, #272835); color: var(--branco, #caced8);">Voltar</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Informacoes</h5>
            </div>
            <div class="widget-content">
                <p><strong>Como funciona a retencao automatica:</strong></p>
                <ol>
                    <li>Na geracao de um novo boleto, o sistema calcula automaticamente os impostos sobre o valor do servico</li>
                    <li>O valor bruto e lancado no DRE como receita</li>
                    <li>Os impostos calculados sao retidos e lancados como deducoes no DRE</li>
                    <li>O cliente paga o valor liquido (bruto menos impostos)</li>
                </ol>

                <hr style="border-color: var(--dark-2, #272835);">

                <p><strong>Anexos do Simples Nacional:</strong></p>
                <ul>
                    <li><strong>Anexo III:</strong> Prestadores de servicos em geral, incluindo manutencao, assistencia tecnica e consultoria</li>
                    <li><strong>Anexo IV:</strong> Construcao civil e empresas que recolhem ISS para o municipio proprio</li>
                    <li><strong>Anexo V:</strong> Comercio e industria (materiais de construcao, etc.)</li>
                </ul>

                <p><strong>Impostos calculados:</strong></p>
                <ul>
                    <li>IRPJ (Imposto de Renda Pessoa Juridica)</li>
                    <li>CSLL (Contribuicao Social sobre Lucro Liquido)</li>
                    <li>COFINS (Contribuicao para Financiamento da Seguridade Social)</li>
                    <li>PIS (Programa de Integracao Social)</li>
                    <li>ISS (Imposto Sobre Servicos) - conforme aliquota municipal</li>
                    <li>INSS/CPP (Contribuicao Previdenciaria)</li>
                </ul>

                <hr style="border-color: var(--dark-2, #272835);">

                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <strong>Configuracao Atual de NFS-e:</strong><br>
                    <b>Codigo Nacional:</b> <?= htmlspecialchars($configs['codigo_tributacao_nacional']) ?><br>
                    <b>Codigo Municipal:</b> <?= htmlspecialchars($configs['codigo_tributacao_municipal']) ?><br>
                    <b>Descricao:</b> <?= htmlspecialchars($configs['descricao_servico']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Aliquotas Anexo III -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-table"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Aliquotas Simples Nacional - Anexo III (Servicos)</h5>
            </div>
            <div class="widget-content">
                <?php foreach ($aliquotas_iii as $a): ?
                <div class="aliquota-card <?= ($configs['anexo_padrao'] == 'III' && $configs['faixa_atual'] == $a->faixa) ? 'ativo' : '' ?>">
                    <h5>
                        <?= $a->faixa ?>a Faixa - Aliquota Nominal: <?= number_format($a->aliquota_nominal, 2, ',', '.') ?>%
                        <?php if ($configs['anexo_padrao'] == 'III' && $configs['faixa_atual'] == $a->faixa): ?> <span class="faixa-badge">ATIVA</span>
                        <?php endif; ?>
                    </h5>
                    <div class="aliquota-grid">
                        <div class="aliquota-item">
                            <span class="label">IRPJ</span>
                            <span class="value"><?= number_format($a->irpj, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">CSLL</span>
                            <span class="value"><?= number_format($a->csll, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">COFINS</span>
                            <span class="value"><?= number_format($a->cofins, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">PIS</span>
                            <span class="value"><?= number_format($a->pis, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">CPP/INSS</span>
                            <span class="value"><?= number_format($a->cpp, 2, ',', '.') ?>%</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Aliquotas Anexo IV -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-table"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Aliquotas Simples Nacional - Anexo IV (Construcao e ISS Proprio)</h5>
            </div>
            <div class="widget-content">
                <?php foreach ($aliquotas_iv as $a): ?
                <div class="aliquota-card <?= ($configs['anexo_padrao'] == 'IV' && $configs['faixa_atual'] == $a->faixa) ? 'ativo' : '' ?>" style="border-left-color: #e67e22;">
                    <h5>
                        <?= $a->faixa ?>a Faixa - Aliquota Nominal: <?= number_format($a->aliquota_nominal, 2, ',', '.') ?>%
                        <?php if ($configs['anexo_padrao'] == 'IV' && $configs['faixa_atual'] == $a->faixa): ?> <span class="faixa-badge">ATIVA</span>
                        <?php endif; ?>
                    </h5>
                    <div class="aliquota-grid">
                        <div class="aliquota-item">
                            <span class="label">IRPJ</span>
                            <span class="value"><?= number_format($a->irpj, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">CSLL</span>
                            <span class="value"><?= number_format($a->csll, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">COFINS</span>
                            <span class="value"><?= number_format($a->cofins, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">PIS</span>
                            <span class="value"><?= number_format($a->pis, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">CPP/INSS</span>
                            <span class="value"><?= number_format($a->cpp, 2, ',', '.') ?>%</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Aliquotas Anexo V -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-table"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Aliquotas Simples Nacional - Anexo V (Comercio e Industria)</h5>
            </div>
            <div class="widget-content">
                <?php foreach ($aliquotas_v as $a): ?
                <div class="aliquota-card <?= ($configs['anexo_padrao'] == 'V' && $configs['faixa_atual'] == $a->faixa) ? 'ativo' : '' ?>" style="border-left-color: #9b59b6;">
                    <h5>
                        <?= $a->faixa ?>a Faixa - Aliquota Nominal: <?= number_format($a->aliquota_nominal, 2, ',', '.') ?>%
                        <?php if ($configs['anexo_padrao'] == 'V' && $configs['faixa_atual'] == $a->faixa): ?> <span class="faixa-badge">ATIVA</span>
                        <?php endif; ?>
                    </h5>
                    <div class="aliquota-grid">
                        <div class="aliquota-item">
                            <span class="label">IRPJ</span>
                            <span class="value"><?= number_format($a->irpj, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">CSLL</span>
                            <span class="value"><?= number_format($a->csll, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">COFINS</span>
                            <span class="value"><?= number_format($a->cofins, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">PIS</span>
                            <span class="value"><?= number_format($a->pis, 2, ',', '.') ?>%</span>
                        </div>
                        <div class="aliquota-item">
                            <span class="label">CPP/INSS</span>
                            <span class="value"><?= number_format($a->cpp, 2, ',', '.') ?>%</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

</div> <!-- /impostos-container -->

<script>
$(document).ready(function() {
    // Preenchimento automatico via certificado digital
    $('#btn-preencher-certificado').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Buscando...');

        $.ajax({
            url: '<?= site_url("impostos/buscar_certificado") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Preencher Automaticamente');
                if (data.success && data.certificado) {
                    var cert = data.certificado;

                    // Preencher anexo sugerido
                    if (cert.anexo_sugerido) {
                        $('#select-anexo').val(cert.anexo_sugerido);
                    }

                    // Preencher descricao baseada no CNAE
                    if (cert.cnae_descricao) {
                        $('#input-descricao').val(cert.cnae_descricao);
                    }

                    // Preencher codigo tributacao nacional baseado no CNAE
                    if (cert.cnae_codigo) {
                        var cnaeMap = {
                            '6201501': '010701',
                            '6201502': '010701',
                            '6202300': '010701',
                            '6204000': '010701',
                            '6209100': '010701',
                            '6311900': '010701',
                            '4751201': '010701',
                            '4321500': '0140301',
                            '4322500': '0140302',
                            '4330400': '0140303',
                        };
                        var codigo = cnaeMap[cert.cnae_codigo] || '010701';
                        $('#input-codigo-nacional').val(codigo);
                    }

                    // Sugerir faixa baseada no faturamento (se disponivel)
                    if (cert.faixa_sugerida) {
                        $('#select-faixa').val(String(cert.faixa_sugerida));
                    }

                    var msg = 'Dados preenchidos com base no certificado digital.';
                    if (cert.razao_social) {
                        msg += '\n\nEmpresa: ' + cert.razao_social;
                    }
                    if (cert.anexo_sugerido) {
                        msg += '\nAnexo sugerido: ' + cert.anexo_sugerido;
                    }
                    alert(msg);
                } else {
                    alert(data.message || 'Nenhum certificado digital encontrado.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Preencher Automaticamente');
                var msg = 'Erro ao buscar dados do certificado.';
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.message) msg = resp.message;
                } catch(e) {}
                alert(msg);
            }
        });
    });
});
</script>
