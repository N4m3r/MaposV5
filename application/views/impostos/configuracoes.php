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
.certificado-card {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border: 1px solid #a5d6a7;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}
.certificado-card.sem-certificado {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    border-color: #ffcc80;
}
.certificado-info {
    margin: 0;
    font-size: 13px;
    line-height: 1.8;
}
.certificado-info strong {
    display: inline-block;
    width: 140px;
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

<!-- Informações do Certificado Digital -->
<div class="row-fluid">
    <div class="span12">
        <div id="certificado-card" class="certificado-card <?= $certificado_info ? '' : 'sem-certificado' ?>">
            <?php if ($certificado_info): ?>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h5 style="margin: 0 0 8px 0;"><i class="fas fa-certificate" style="color: #2e7d32;"></i> Certificado Digital Ativo</h5>
                        <p class="certificado-info">
                            <strong>Razão Social:</strong> <?= htmlspecialchars($certificado_info['razao_social']) ?><br>
                            <strong>CNPJ:</strong> <?= htmlspecialchars($certificado_info['cnpj']) ?><br>
                            <strong>Tipo:</strong> <?= $certificado_info['tipo'] ?><br>
                            <?php if ($certificado_info['anexo_sugerido']): ?>
                                <strong>Anexo Sugerido:</strong> <span class="label label-success">Anexo <?= $certificado_info['anexo_sugerido'] ?></span>
                                <?php if ($certificado_info['cnae_descricao']): ?>
                                    <small style="color:#666;"> (<?= htmlspecialchars($certificado_info['cnae_descricao']) ?>)</small>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (isset($certificado_info['optante_simples']) && $certificado_info['optante_simples']): ?>
                                <br><strong>Simples Nacional:</strong> <span class="label label-info">Optante</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" id="btn-preencher-certificado">
                            <i class="fas fa-sync-alt"></i> Preencher Automaticamente
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h5 style="margin: 0 0 8px 0;"><i class="fas fa-exclamation-triangle" style="color: #e65100;"></i> Nenhum Certificado Digital Configurado</h5>
                        <p style="margin: 0; color: #666; font-size: 13px;">
                            Configure um certificado digital para preencher automaticamente os dados tributários.
                            <br>Cadastre em <a href="<?= site_url('certificado') ?>">Certificado Digital</a>.
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning" id="btn-buscar-certificado">
                            <i class="fas fa-search"></i> Buscar Certificado
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
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
                <form method="post" action="<?= site_url('impostos/configuracoes') ?>" class="form-horizontal" id="form-config-impostos">

                    <div class="control-group">
                        <label class="control-label">Anexo Simples:*</label>
                        <div class="controls">
                            <select name="anexo_padrao" id="select-anexo" class="span8" required>
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
                            <select name="faixa_atual" id="select-faixa" class="span4" required>
                                <option value="1" <?= $configs['faixa_atual'] == '1' ? 'selected' : '' ?>>1ª Faixa (até R$ 180.000)</option>
                                <option value="2" <?= $configs['faixa_atual'] == '2' ? 'selected' : '' ?>>2ª Faixa (até R$ 360.000)</option>
                                <option value="3" <?= $configs['faixa_atual'] == '3' ? 'selected' : '' ?>>3ª Faixa (até R$ 720.000)</option>
                                <option value="4" <?= $configs['faixa_atual'] == '4' ? 'selected' : '' ?>>4ª Faixa (até R$ 1.800.000)</option>
                                <option value="5" <?= $configs['faixa_atual'] == '5' ? 'selected' : '' ?>>5ª Faixa (até R$ 4.800.000)</option>
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
                            <span class="help-inline">Alíquota de ISS do município</span>
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
                            <input type="text" name="codigo_tributacao_nacional" id="input-codigo-nacional" class="span3" value="<?= htmlspecialchars($configs['codigo_tributacao_nacional']) ?>" required maxlength="10" />
                            <span class="help-inline">Código LC 116/2003 (ex: 010701)</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Cód. Tributação Municipal:*</label>
                        <div class="controls">
                            <input type="text" name="codigo_tributacao_municipal" id="input-codigo-municipal" class="span3" value="<?= htmlspecialchars($configs['codigo_tributacao_municipal']) ?>" required maxlength="10" />
                            <span class="help-inline">Código do serviço na cidade</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Descrição do Serviço:*</label>
                        <div class="controls">
                            <textarea name="descricao_servico" id="input-descricao" class="span8" rows="3" required><?= htmlspecialchars($configs['descricao_servico']) ?></textarea>
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
                    <li><strong>Anexo V:</strong> Comércio e indústria (materiais de construção, etc.)</li>
                </ul>

                <p><strong>Impostos calculados:</strong></p>
                <ul>
                    <li>IRPJ (Imposto de Renda Pessoa Jurídica)</li>
                    <li>CSLL (Contribuição Social sobre Lucro Líquido)</li>
                    <li>COFINS (Contribuição para Financiamento da Seguridade Social)</li>
                    <li>PIS (Programa de Integração Social)</li>
                    <li>ISS (Imposto Sobre Serviços) - conforme alíquota municipal</li>
                    <li>INSS/CPP (Contribuição Previdenciária)</li>
                </ul>

                <hr>

                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <strong>Configuração Atual de NFS-e:</strong><br>
                    <b>Código Nacional:</b> <?= htmlspecialchars($configs['codigo_tributacao_nacional']) ?><br>
                    <b>Código Municipal:</b> <?= htmlspecialchars($configs['codigo_tributacao_municipal']) ?><br>
                    <b>Descrição:</b> <?= htmlspecialchars($configs['descricao_servico']) ?>
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
                    <h5><?= $a->faixa ?>ª Faixa - Alíquota Nominal: <?= number_format($a->aliquota_nominal, 2, ',', '.') ?>%</h5>
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
                    <h5><?= $a->faixa ?>ª Faixa - Alíquota Nominal: <?= number_format($a->aliquota_nominal, 2, ',', '.') ?>%</h5>
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

<script>
$(document).ready(function() {
    // Preenchimento automático via certificado digital
    $('#btn-preencher-certificado, #btn-buscar-certificado').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Buscando...');

        $.ajax({
            url: '<?= site_url("impostos/buscar_certificado") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                btn.prop('disabled', false);
                if (data.success && data.certificado) {
                    var cert = data.certificado;

                    // Preencher anexo sugerido
                    if (cert.anexo_sugerido) {
                        $('#select-anexo').val(cert.anexo_sugerido);
                    }

                    // Preencher descrição baseada no CNAE
                    if (cert.cnae_descricao) {
                        $('#input-descricao').val(cert.cnae_descricao);
                    }

                    // Preencher código tributação nacional baseado no CNAE
                    if (cert.cnae_codigo) {
                        // Mapear CNAE para código LC 116
                        var cnaeMap = {
                            '6201501': '010701', // Desenvolvimento de programas
                            '6201502': '010701', // Desenvolvimento de programas sob encomenda
                            '6202300': '010701', // Desenvolvimento e licenciamento de software
                            '6204000': '010701', // Consultoria em TI
                            '6209100': '010701', // Suporte técnico em informática
                            '6311900': '010701', // Tratamento de dados
                            '4751201': '010701', // Comércio varejista de materiais de construção
                            '4321500': '0140301', // Instalação elétrica
                            '4322500': '0140302', // Instalação hidráulica
                            '4330400': '0140303', // Pintura
                        };
                        var codigo = cnaeMap[cert.cnae_codigo] || '010701';
                        $('#input-codigo-nacional').val(codigo);
                    }

                    // Alerta de sucesso
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
                btn.prop('disabled', false);
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