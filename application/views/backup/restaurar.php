<style>
    .upload-area {
        border: 3px dashed #ddd;
        border-radius: 10px;
        padding: 50px 20px;
        text-align: center;
        background: #fafafa;
        cursor: pointer;
        transition: all 0.3s;
    }

    .upload-area:hover {
        border-color: #5bc0de;
        background: #f0f8ff;
    }

    .upload-area.dragover {
        border-color: #5cb85c;
        background: #f0fff0;
    }

    .upload-area i {
        font-size: 48px;
        color: #999;
        margin-bottom: 15px;
    }

    .upload-area:hover i {
        color: #5bc0de;
    }

    .warning-box {
        background: #fff3cd;
        border: 1px solid #ffc107;
        color: #856404;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .warning-box h4 {
        margin-top: 0;
        color: #856404;
    }

    .warning-box ul {
        margin-bottom: 0;
    }

    #fileInfo {
        display: none;
        margin-top: 20px;
        padding: 15px;
        background: #e8f5e9;
        border-radius: 5px;
    }
</style>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="icon-refresh"></i>
                </span>
                <h5>Restaurar Backup</h5>
            </div>

            <div class="widget-content">

                <!-- Alerta de Segurança -->
                <div class="warning-box">
                    <h4><i class="icon-exclamation-sign"></i> Atenção - Operação Crítica!</h4>
                    <ul>
                        <li><strong>Esta operação não pode ser desfeita.</strong></li>
                        <li>Todos os dados atuais serão substituídos pelos dados do backup.</li>
                        <li>Um backup de segurança será criado automaticamente antes da restauração.</li>
                        <li>Recomendado fazer esta operação em horário de baixo movimento.</li>
                        <li>Verifique se o arquivo de backup é válido antes de prosseguir.</li>
                    </ul>
                </div>

                <!-- Mensagens -->
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-error">
                        <button class="close" data-dismiss="alert">×</button>
                        <i class="icon-remove"></i> <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <?php echo validation_errors('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button><i class="icon-remove"></i> ', '</div>'); ?>

                <!-- Formulário de Upload -->
                <form action="<?php echo site_url('backup/processar_restauracao'); ?>" method="POST" enctype="multipart/form-data" id="formRestauracao">

                    <!-- Área de Upload -->
                    <div class="upload-area" id="uploadArea">
                        <i class="icon-cloud-upload"></i>
                        <h4>Clique ou arraste o arquivo SQL aqui</h4>
                        <p class="text-muted">Formatos aceitos: .sql, .gz, .zip<br>Tamanho máximo: 100MB</p>
                        <input type="file" name="arquivo_sql" id="arquivo_sql" accept=".sql,.gz,.zip" style="display: none;" required>
                    </div>

                    <!-- Informações do Arquivo -->
                    <div id="fileInfo">
                        <h5><i class="icon-file"></i> Arquivo Selecionado</h5>
                        <div class="row-fluid">
                            <div class="span6">
                                <strong>Nome:</strong> <span id="fileName"></span>
                            </div>
                            <div class="span6">
                                <strong>Tamanho:</strong> <span id="fileSize"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Backups Disponíveis -->
                    <?php if (!empty($backups_disponiveis)): ?>
                    <h5 style="margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <i class="icon-hdd"></i> Ou selecione um backup do servidor:
                    </h5>

                    <div class="row-fluid" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach (array_slice($backups_disponiveis, 0, 10) as $backup): ?>
                        <div class="span6" style="margin-bottom: 10px;">
                            <label class="radio" style="padding: 10px; background: #f5f5f5; border-radius: 5px;">
                                <input type="radio" name="backup_existente" value="<?php echo $backup['nome']; ?>"
                                       onclick="selecionarBackup('<?php echo $backup['nome']; ?>', '<?php echo $backup['tamanho']; ?>')">
                                <strong><?php echo $backup['nome']; ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo $backup['tamanho']; ?> - <?php echo date('d/m/Y H:i', strtotime($backup['data'])); ?>
                                </small>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Confirmação -->
                    <div style="background: #fff3cd; padding: 20px; border-radius: 5px; margin-top: 30px;">
                        <label class="checkbox" style="font-size: 16px; color: #856404;">
                            <input type="checkbox" name="confirmacao" value="1" required>
                            <strong>Eu entendo que todos os dados atuais serão substituídos e confirmo a restauração.</strong>
                        </label>
                    </div>

                    <!-- Botões -->
                    <div class="form-actions" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-large btn-warning" id="btnRestaurar" disabled>
                            <i class="icon-refresh icon-white"></i> Iniciar Restauração
                        </button>
                        <a href="<?php echo site_url('backup'); ?>" class="btn btn-large">Cancelar</a>
                    </div>

                </form>

                <!-- Progresso da Restauração -->
                <div id="progressoRestauracao" style="display: none; margin-top: 20px;">
                    <div class="alert alert-warning">
                        <i class="icon-spinner icon-spin"></i>
                        <strong>Restaurando banco de dados, aguarde...</strong>
                        <br>Não feche esta janela durante o processo.
                    </div>
                    <div class="progress progress-striped active">
                        <div class="bar bar-warning" style="width: 100%;"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Upload area click
    $('#uploadArea').click(function(e) {
        if ($(e.target).is('#arquivo_sql')) return;
        $('#arquivo_sql').click();
    });

    // File input change
    $('#arquivo_sql').change(function() {
        var file = this.files[0];
        if (file) {
            mostrarInfoArquivo(file);
        }
    });

    // Drag and drop
    var uploadArea = document.getElementById('uploadArea');

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');

        var files = e.dataTransfer.files;
        if (files.length > 0) {
            $('#arquivo_sql')[0].files = files;
            mostrarInfoArquivo(files[0]);
        }
    });

    // Habilitar botão ao confirmar
    $('input[name="confirmacao"]').change(function() {
        $('#btnRestaurar').prop('disabled', !this.checked);
    });

    // Form submit
    $('#formRestauracao').submit(function() {
        if (!$('input[name="arquivo_sql"]')[0].files.length && !$('input[name="backup_existente"]:checked').length) {
            alert('Selecione um arquivo de backup ou um backup existente.');
            return false;
        }

        if (!confirm('ATENÇÃO!\n\nTem certeza absoluta que deseja restaurar este backup?\n\nTodos os dados atuais serão substituídos permanentemente.\n\nRecomenda-se fazer um backup atual antes de prosseguir.')) {
            return false;
        }

        $('#progressoRestauracao').show();
        $('#btnRestaurar').prop('disabled', true).text('Processando...');
        return true;
    });
});

function mostrarInfoArquivo(file) {
    // Validar extensão
    var ext = file.name.split('.').pop().toLowerCase();
    var allowed = ['sql', 'gz', 'zip'];

    if (allowed.indexOf(ext) === -1) {
        alert('Extensão não permitida. Use apenas: .sql, .gz ou .zip');
        $('#arquivo_sql').val('');
        return;
    }

    // Validar tamanho (100MB)
    if (file.size > 100 * 1024 * 1024) {
        alert('Arquivo muito grande. Tamanho máximo: 100MB');
        $('#arquivo_sql').val('');
        return;
    }

    // Mostrar informações
    $('#fileName').text(file.name);
    $('#fileSize').text(formatarTamanho(file.size));
    $('#fileInfo').show();
}

function selecionarBackup(nome, tamanho) {
    $('#fileName').text(nome);
    $('#fileSize').text(tamanho);
    $('#fileInfo').show();
    $('#arquivo_sql').val('');
}

function formatarTamanho(bytes) {
    if (bytes === 0) return '0 B';
    var k = 1024;
    var sizes = ['B', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
