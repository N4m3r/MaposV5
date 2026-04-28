<?php
/**
 * Configuração do Certificado Digital
 */
?>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('certificado') ?>">Certificado</a> <span class="divider">/</span></li>
            <li class="active">Configurar</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span8 offset2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-certificate"></i></span>
                <h5>Configurar Certificado Digital</h5>
            </div>
            <div class="widget-content">
                <form method="post" action="<?= site_url('certificado/configurar') ?>" class="form-horizontal" enctype="multipart/form-data">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Certificado Digital — Simples Nacional (DAS)</strong><br>
                        Configure seu certificado A1 (arquivo .pfx) ou A3 (token/smartcard) para integração automática com a Receita Federal.
                        Após salvar, o sistema detectará automaticamente o Anexo do Simples Nacional e as alíquotas do DAS.
                    </div>

                    <div class="control-group">
                        <label class="control-label">Tipo de Certificado:*</label>
                        <div class="controls">
                            <label class="radio inline">
                                <input type="radio" name="tipo" value="A1" checked onchange="toggleTipo('A1')" />
                                <strong>A1</strong> (Arquivo .pfx)
                            </label>
                            <label class="radio inline">
                                <input type="radio" name="tipo" value="A3" onchange="toggleTipo('A3')" />
                                <strong>A3</strong> (Token/Smartcard)
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">CNPJ:*</label>
                        <div class="controls">
                            <input type="text" name="cnpj" class="span6" id="cnpj" required
                                   placeholder="00.000.000/0000-00" maxlength="18"
                                   value="<?= $certificado ? maskCnpj($certificado->cnpj) : '' ?>" />
                            <a href="#" class="btn btn-small btn-info" onclick="consultarCNPJ(); return false;">
                                <i class="fas fa-search"></i> Consultar
                            </a>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Razão Social:*</label>
                        <div class="controls">
                            <input type="text" name="razao_social" class="span8" id="razao_social" required
                                   value="<?= $certificado ? $certificado->razao_social : '' ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Nome Fantasia:</label>
                        <div class="controls">
                            <input type="text" name="nome_fantasia" class="span8" id="nome_fantasia"
                                   value="<?= $certificado ? $certificado->nome_fantasia : '' ?>" />
                        </div>
                    </div>

                    <!-- Campos para A1 -->
                    <div id="campos-a1">
                        <div class="control-group">
                            <label class="control-label">Arquivo PFX:*</label>
                            <div class="controls">
                                <input type="file" name="certificado" class="span6" accept=".pfx,.p12" />
                                <span class="help-inline">Arquivo .pfx ou .p12</span>
                            </div>
                        </div>
                    </div>

                    <!-- Campos para A3 -->
                    <div id="campos-a3" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Certificado A3</strong><br>
                            Para certificados A3 (token/smartcard), o sistema usará integração via driver instalado no servidor.
                            Certifique-se de que o token está conectado no momento da consulta.
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Senha do Certificado:*</label>
                        <div class="controls">
                            <input type="password" name="senha" class="span6" required
                                   placeholder="Senha do certificado" />
                            <span class="help-inline">Será criptografada com AES-256</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Ambiente:</label>
                        <div class="controls">
                            <select name="ambiente" class="span6">
                                <option value="homologacao" <?= (!$certificado || $certificado->ambiente == 'homologacao') ? 'selected' : '' ?>>
                                    Homologação (Testes)
                                </option>
                                <option value="producao" <?= ($certificado && $certificado->ambiente == 'producao') ? 'selected' : '' ?>>
                                    Produção
                                </option>
                            </select>
                            <span class="help-inline">
                                <i class="fas fa-info-circle"></i>
                                Use <strong>Homologação</strong> para testes. <strong>Produção</strong> emite notas reais.
                            </span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Certificado
                        </button>
                        <a href="<?= site_url('certificado') ?>" class="btn">Cancelar</a>
                    </div>

                </form>

                <hr style="margin:25px 0; border-top:1px solid #ddd">

                <div class="alert" style="background:rgba(16,134,221,0.1); border-color:rgba(16,134,221,0.3); color:#0d6efd">
                    <i class="fas fa-magic"></i> <strong>Não foi detectado automaticamente?</strong><br>
                    Se a consulta da Receita não identificou o Simples Nacional, você pode forçar a configuração manualmente.
                </div>
                <form method="post" action="<?= site_url('certificado/forcar_simples_nacional') ?>" class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">Anexo Simples:*</label>
                        <div class="controls">
                            <select name="anexo_forcado" class="span6" required>
                                <option value="III">Anexo III — Serviços em geral</option>
                                <option value="IV">Anexo IV — Construção / ISS próprio</option>
                                <option value="V">Anexo V — Comércio / Indústria</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions" style="padding-left:180px">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-check-circle"></i> Forçar Simples Nacional
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Resultado da Consulta -->
<div id="resultado-consulta" class="row-fluid" style="display: none;">
    <div class="span8 offset2">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-building"></i></span>
                <h5>Dados do CNPJ</h5>
            </div>
            <div class="widget-content" id="dados-cnpj">
                <!-- Preenchido via JS -->
            </div>
        </div>
    </div>
</div>

<script>
function toggleTipo(tipo) {
    document.getElementById('campos-a1').style.display = tipo === 'A1' ? 'block' : 'none';
    document.getElementById('campos-a3').style.display = tipo === 'A3' ? 'block' : 'none';
}

function consultarCNPJ() {
    var cnpj = document.getElementById('cnpj').value.replace(/[^0-9]/g, '');

    if (cnpj.length !== 14) {
        alert('CNPJ inválido');
        return;
    }

    $.get('<?= site_url("certificado/api_consulta") ?>?acao=cnpj&cnpj=' + cnpj, function(data) {
        if (data.success) {
            document.getElementById('razao_social').value = data.data.nome || '';
            document.getElementById('nome_fantasia').value = data.data.fantasia || '';

            // Mostrar resultados
            var html = '<table class="table table-bordered">';
            html += '<tr><td><strong>Razão Social:</strong></td><td>' + (data.data.nome || 'N/A') + '</td></tr>';
            html += '<tr><td><strong>Nome Fantasia:</strong></td><td>' + (data.data.fantasia || 'N/A') + '</td></tr>';
            html += '<tr><td><strong>Situação:</strong></td><td>' + (data.data.situacao || 'N/A') + '</td></tr>';
            html += '<tr><td><strong>Atividade Principal:</strong></td><td>' + (data.data.atividade_principal ? data.data.atividade_principal[0].text : 'N/A') + '</td></tr>';
            html += '<tr><td><strong>Logradouro:</strong></td><td>' + (data.data.logradouro || '') + ', ' + (data.data.numero || '') + '</td></tr>';
            html += '<tr><td><strong>Bairro/Cidade:</strong></td><td>' + (data.data.bairro || '') + ' - ' + (data.data.municipio || '') + '/' + (data.data.uf || '') + '</td></tr>';
            html += '</table>';

            document.getElementById('dados-cnpj').innerHTML = html;
            document.getElementById('resultado-consulta').style.display = 'block';
        } else {
            alert('Erro: ' + (data.error || 'Não foi possível consultar'));
        }
    });
}

// Máscara de CNPJ
document.getElementById('cnpj').addEventListener('input', function(e) {
    var value = e.target.value.replace(/\D/g, '');
    if (value.length <= 14) {
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        e.target.value = value;
    }
});
</script>

<?php
function maskCnpj($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '\1.\2.\3/\4-\5', $cnpj);
}
?>
