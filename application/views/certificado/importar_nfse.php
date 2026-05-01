<?php
/**
 * Importar NFS-e via XML
 * Interface moderna com drag-and-drop, preview e vinculacao a OS
 */

$notas = $notas ?? [];
$oss = $oss ?? [];
?>

<style>
.nfse-import-wrapper { max-width: 900px; margin: 0 auto; padding: 20px 0; }
.nfse-import-header { text-align: center; margin-bottom: 30px; }
.nfse-import-header h2 { color: var(--title,#d4d8e0); font-size: 24px; margin: 0 0 8px 0; }
.nfse-import-header h2 i { color: #52459f; }
.nfse-import-subtitle { color: var(--dark-cinz,#8788a4); font-size: 14px; margin: 0; }

.nfse-import-card {
    background: var(--wid-dark,#1c1d26);
    border: 1px solid var(--dark-2,#272835);
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 20px;
}
.nfse-import-card-title {
    display: flex; align-items: center; gap: 10px;
    font-size: 16px; font-weight: bold; color: var(--title,#d4d8e0);
    margin-bottom: 20px;
}
.nfse-step-number {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; background: #52459f; color: #fff;
    border-radius: 50%; font-size: 13px; font-weight: bold;
}

.nfse-dropzone {
    border: 2px dashed var(--dark-2,#272835); border-radius: 8px;
    padding: 40px 20px; text-align: center; cursor: pointer;
    transition: all .2s ease; background: var(--dark-0,#191a22);
}
.nfse-dropzone:hover, .nfse-dropzone.dragover {
    border-color: #52459f; background: rgba(82,69,159,.08);
}
.nfse-dropzone-icon { font-size: 48px; color: #52459f; margin-bottom: 12px; }
.nfse-dropzone-text { font-size: 15px; color: var(--branco,#caced8); margin-bottom: 6px; }
.nfse-dropzone-hint { font-size: 12px; color: var(--dark-cinz,#8788a4); }

.nfse-file-info {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.nfse-file-tag {
    display: flex; align-items: center; gap: 8px;
    background: rgba(82,69,159,.15); border: 1px solid rgba(82,69,159,.3);
    color: var(--branco,#caced8); padding: 8px 14px; border-radius: 6px; font-size: 13px;
}
.nfse-file-tag i { color: #52459f; }
.nfse-file-size { color: var(--dark-cinz,#8788a4); font-size: 11px; }

.nfse-preview-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
@media(max-width:768px){ .nfse-preview-grid { grid-template-columns: repeat(2,1fr); } }

.nfse-preview-item {
    background: var(--dark-0,#191a22); border: 1px solid var(--dark-2,#272835);
    border-radius: 6px; padding: 14px;
}
.nfse-preview-label {
    font-size: 11px; color: var(--dark-cinz,#8788a4);
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
}
.nfse-preview-value {
    font-size: 15px; font-weight: bold; color: var(--branco,#caced8); word-break: break-all;
}
.nfse-preview-value-highlight { color: #62eba6; font-size: 18px; }

.nfse-preview-alert {
    margin-top: 16px; padding: 12px 16px;
    background: rgba(252,157,15,.12); border: 1px solid rgba(252,157,15,.25);
    border-radius: 6px; color: #fc9d0f; font-size: 13px;
    display: flex; align-items: center; gap: 8px;
}

.nfse-import-actions { display: flex; justify-content: center; gap: 12px; margin-top: 10px; }
.nfse-import-actions form { display: flex; gap: 12px; }
.nfse-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: bold;
    border: none; cursor: pointer; text-decoration: none; transition: opacity .2s;
}
.nfse-btn:hover { opacity: .9; text-decoration: none; }
.nfse-btn-primary { background: #26a38e; color: #fff; }
.nfse-btn-secondary { background: var(--dark-2,#272835); color: var(--branco,#caced8); border: 1px solid var(--dark-2,#272835); }

.nfse-import-loading { text-align: center; padding: 40px; color: var(--dark-cinz,#8788a4); }
.nfse-spinner {
    width: 40px; height: 40px; border: 3px solid var(--dark-2,#272835);
    border-top-color: #52459f; border-radius: 50%;
    animation: nfse-spin 1s linear infinite; margin: 0 auto 16px;
}
@keyframes nfse-spin { to { transform: rotate(360deg); } }

.nfse-os-select { width: 100%; }
</style>

<div class="row-fluid nfse-import-wrapper">
    <div class="span12">

        <!-- Header -->
        <div class="nfse-import-header">
            <h2><i class="fas fa-file-import"></i> Importar NFS-e via XML</h2>
            <p class="nfse-import-subtitle">Importe notas emitidas externamente e vincule a uma ordem de servico</p>
        </div>

        <!-- Flash messages -->
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= $this->session->flashdata('success') ?>
        </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= $this->session->flashdata('error') ?>
        </div>
        <?php endif; ?>

        <!-- Passo 1: Upload -->
        <div class="nfse-import-card" id="card-upload">
            <div class="nfse-import-card-title">
                <span class="nfse-step-number">1</span>
                <span>Selecione o arquivo XML</span>
            </div>

            <div class="nfse-dropzone" id="dropzone">
                <div class="nfse-dropzone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                <div class="nfse-dropzone-text"><strong>Arraste o XML aqui</strong> ou clique para selecionar</div>
                <div class="nfse-dropzone-hint">Aceita arquivos .xml da NFS-e Nacional (DPS) ou padrões municipais (ABRASF, GINFES)</div>
                <input type="file" id="xml-input" accept=".xml" style="display:none">
            </div>

            <div class="nfse-file-info" id="file-info" style="display:none">
                <div class="nfse-file-tag">
                    <i class="fas fa-file-code"></i>
                    <span id="file-name">---</span>
                    <span class="nfse-file-size" id="file-size"></span>
                </div>
                <button type="button" class="nfse-btn nfse-btn-secondary" id="btn-remover-arquivo">
                    <i class="fas fa-times"></i> Remover
                </button>
            </div>
        </div>

        <!-- Passo 2: Preview + OS -->
        <div class="nfse-import-card" id="card-preview" style="display:none">
            <div class="nfse-import-card-title">
                <span class="nfse-step-number">2</span>
                <span>Confirme os dados e vincule a uma OS</span>
            </div>

            <div class="nfse-preview-grid">
                <div class="nfse-preview-item">
                    <div class="nfse-preview-label">Numero da NFS-e</div>
                    <div class="nfse-preview-value" id="preview-numero">---</div>
                </div>
                <div class="nfse-preview-item">
                    <div class="nfse-preview-label">Chave de Acesso</div>
                    <div class="nfse-preview-value" id="preview-chave">---</div>
                </div>
                <div class="nfse-preview-item">
                    <div class="nfse-preview-label">Codigo Verificacao</div>
                    <div class="nfse-preview-value" id="preview-codigo">---</div>
                </div>
                <div class="nfse-preview-item">
                    <div class="nfse-preview-label">Data de Emissao</div>
                    <div class="nfse-preview-value" id="preview-data">---</div>
                </div>
                <div class="nfse-preview-item">
                    <div class="nfse-preview-label">Valor dos Servicos</div>
                    <div class="nfse-preview-value nfse-preview-value-highlight" id="preview-valor">---</div>
                </div>
                <div class="nfse-preview-item">
                    <div class="nfse-preview-label">Valor Liquido</div>
                    <div class="nfse-preview-value nfse-preview-value-highlight" id="preview-liquido">---</div>
                </div>
            </div>

            <div class="nfse-preview-alert" id="preview-alert" style="display:none">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="preview-alert-text"></span>
            </div>

            <!-- Selecionar OS -->
            <div style="margin-top:20px; padding:16px; background:var(--dark-0,#191a22); border:1px solid var(--dark-2,#272835); border-radius:6px;">
                <label style="font-size:13px; color:var(--title,#d4d8e0); font-weight:bold; margin-bottom:8px; display:block;">
                    <i class="fas fa-link" style="color:#52459f"></i> Vincular a Ordem de Servico
                </label>
                <select name="os_id" id="os-select" class="span12 nfse-os-select" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8);">
                    <option value="">-- Selecione uma OS --</option>
                    <?php foreach ($oss as $o): ?>
                        <option value="<?= $o->idOs ?>">#<?= str_pad($o->idOs, 6, '0', STR_PAD_LEFT) ?> — <?= htmlspecialchars($o->nomeCliente ?? '') ?> — <?= date('d/m/Y', strtotime($o->dataInicial ?? 'now')) ?></option>
                    <?php endforeach; ?>
                </select>
                <small style="color:var(--dark-cinz,#8788a4); display:block; margin-top:4px;">
                    A NFS-e sera vinculada a OS selecionada e aparecera na aba Servicos (NFS-e).
                </small>
            </div>
        </div>

        <!-- Passo 3: Acoes -->
        <div class="nfse-import-actions" id="card-actions" style="display:none">
            <form method="post" action="<?= site_url('certificado/salvar_importacao') ?>" id="form-salvar">
                <input type="hidden" name="os_id" id="form-os-id" value="">
                <input type="hidden" name="xml_base64" id="xml-base64">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

                <button type="submit" class="nfse-btn nfse-btn-primary" id="btn-confirmar">
                    <i class="fas fa-check-circle"></i> Confirmar e Salvar
                </button>
                <a href="<?= site_url('certificado/importar_nfse') ?>" class="nfse-btn nfse-btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </form>
        </div>

        <!-- Loading -->
        <div class="nfse-import-loading" id="loading" style="display:none">
            <div class="nfse-spinner"></div>
            <p>Lendo XML e extraindo dados...</p>
        </div>

        <!-- Lista de importadas -->
        <?php if (!empty($notas['notas'])): ?>
        <div class="nfse-import-card" style="margin-top:30px">
            <div class="nfse-import-card-title">
                <span class="nfse-step-number" style="background:#1086dd">#</span>
                <span>Notas Importadas no Periodo</span>
            </div>
            <table class="table table-bordered table-striped" style="background:transparent; border-color:var(--dark-2,#272835); color:var(--branco,#caced8);">
                <thead>
                    <tr style="background:var(--dark-1,#14141a)">
                        <th style="border-color:var(--dark-2,#272835)">Numero</th>
                        <th style="border-color:var(--dark-2,#272835)">Data Emissao</th>
                        <th style="border-color:var(--dark-2,#272835)">Importacao</th>
                        <th style="border-color:var(--dark-2,#272835)" class="text-right">Valor Total</th>
                        <th style="border-color:var(--dark-2,#272835)" class="text-center">Situacao</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notas['notas'] as $nota): ?>
                    <tr>
                        <td style="border-color:var(--dark-2,#272835)"><?= $nota->numero ?></td>
                        <td style="border-color:var(--dark-2,#272835)"><?= $nota->data_emissao ? date('d/m/Y', strtotime($nota->data_emissao)) : '---' ?></td>
                        <td style="border-color:var(--dark-2,#272835)"><?= $nota->data_importacao ? date('d/m/Y H:i', strtotime($nota->data_importacao)) : '---' ?></td>
                        <td style="border-color:var(--dark-2,#272835)" class="text-right">R$ <?= number_format($nota->valor_total, 2, ',', '.') ?></td>
                        <td style="border-color:var(--dark-2,#272835)" class="text-center">
                            <span class="label" style="background:#26a38e; color:#fff"><?= $nota->situacao ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
(function() {
    const dropzone = document.getElementById('dropzone');
    const xmlInput = document.getElementById('xml-input');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const btnRemover = document.getElementById('btn-remover-arquivo');
    const cardPreview = document.getElementById('card-preview');
    const cardActions = document.getElementById('card-actions');
    const loading = document.getElementById('loading');
    const xmlBase64 = document.getElementById('xml-base64');
    const osSelect = document.getElementById('os-select');
    const formOsId = document.getElementById('form-os-id');
    const btnConfirmar = document.getElementById('btn-confirmar');

    let currentFile = null;

    dropzone.addEventListener('click', () => xmlInput.click());
    dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragover'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
    dropzone.addEventListener('drop', e => {
        e.preventDefault(); dropzone.classList.remove('dragover');
        if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
    });
    xmlInput.addEventListener('change', e => { if (e.target.files.length) handleFile(e.target.files[0]); });
    btnRemover.addEventListener('click', resetUpload);

    osSelect.addEventListener('change', function() {
        formOsId.value = this.value;
    });

    function resetUpload() {
        currentFile = null; xmlInput.value = '';
        fileInfo.style.display = 'none'; dropzone.style.display = 'block';
        cardPreview.style.display = 'none'; cardActions.style.display = 'none';
        xmlBase64.value = ''; osSelect.value = ''; formOsId.value = '';
    }

    function handleFile(file) {
        if (!file.name.toLowerCase().endsWith('.xml')) { alert('Selecione um arquivo XML.'); return; }
        currentFile = file;
        fileName.textContent = file.name;
        fileSize.textContent = '(' + (file.size / 1024).toFixed(1) + ' KB)';
        dropzone.style.display = 'none'; fileInfo.style.display = 'flex';
        uploadAndPreview(file);
    }

    function uploadAndPreview(file) {
        loading.style.display = 'flex';
        const formData = new FormData();
        formData.append('xml_nfse', file);
        const csrf = document.querySelector('input[name="<?= $this->security->get_csrf_token_name() ?>"]');
        if (csrf) formData.append(csrf.name, csrf.value);

        fetch('<?= site_url("certificado/preview_importar_ajax") ?>', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            if (!data.success) { alert(data.message || 'Erro ao processar XML.'); resetUpload(); return; }

            document.getElementById('preview-numero').textContent = data.numero_nfse || 'Nao identificado';
            document.getElementById('preview-chave').textContent = data.chave_acesso || 'Nao identificado';
            document.getElementById('preview-codigo').textContent = data.codigo_verificacao || 'Nao identificado';
            document.getElementById('preview-data').textContent = data.data_emissao ? formatarDataBR(data.data_emissao) : 'Nao identificado';
            document.getElementById('preview-valor').textContent = data.valor_servicos > 0 ? 'R$ ' + formatarMoeda(data.valor_servicos) : 'Nao identificado';
            document.getElementById('preview-liquido').textContent = data.valor_liquido > 0 ? 'R$ ' + formatarMoeda(data.valor_liquido) : 'Nao identificado';

            const alertBox = document.getElementById('preview-alert');
            const alertText = document.getElementById('preview-alert-text');
            let alertas = [];
            if (!data.numero_nfse) alertas.push('numero da nota nao identificado');
            if (!data.valor_servicos) alertas.push('valor dos servicos nao identificado');
            if (!data.data_emissao) alertas.push('data de emissao nao identificada');
            if (alertas.length) {
                alertText.textContent = 'Atenção: ' + alertas.join(', ') + '. Os dados serao salvos com fallback.';
                alertBox.style.display = 'flex';
            } else { alertBox.style.display = 'none'; }

            xmlBase64.value = data.xml_base64 || '';
            cardPreview.style.display = 'block'; cardActions.style.display = 'flex';
        })
        .catch(err => {
            loading.style.display = 'none'; console.error(err);
            alert('Erro de comunicacao ao processar o XML.'); resetUpload();
        });
    }

    document.getElementById('form-salvar').addEventListener('submit', function(e) {
        if (!formOsId.value) {
            alert('Selecione uma Ordem de Servico para vincular a NFS-e.');
            osSelect.focus();
            e.preventDefault();
            return false;
        }
        btnConfirmar.disabled = true;
        btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    });

    function formatarDataBR(dt) { const d = dt.split(' ')[0].split('-'); return d.length === 3 ? d[2]+'/'+d[1]+'/'+d[0] : dt; }
    function formatarMoeda(v) { return parseFloat(v).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}); }
})();
</script>
