<?php $this->load->view('tema/header.php'); ?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="icon-hdd"></i>
                </span>
                <h5>Gerenciamento de Backups</h5>
            </div>

            <div class="widget-content">
                <!-- Mensagens -->
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success">
                        <button class="close" data-dismiss="alert">×</button>
                        <i class="icon-check"></i> <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-error">
                        <button class="close" data-dismiss="alert">×</button>
                        <i class="icon-remove"></i> <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('warning')): ?>
                    <div class="alert alert-warning">
                        <button class="close" data-dismiss="alert">×</button>
                        <i class="icon-exclamation-sign"></i> <?php echo $this->session->flashdata('warning'); ?>
                    </div>
                <?php endif; ?>

                <!-- Informações do Banco -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span3">
                        <div class="stat-box" style="background: #5bc0de; color: white; padding: 15px; border-radius: 5px; text-align: center;">
                            <i class="icon-hdd" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                            <span style="font-size: 12px; opacity: 0.9;">Banco de Dados</span>
                            <strong style="display: block; font-size: 18px; margin-top: 5px;"><?php echo $database_info['nome']; ?></strong>
                        </div>
                    </div>
                    <div class="span3">
                        <div class="stat-box" style="background: #5cb85c; color: white; padding: 15px; border-radius: 5px; text-align: center;">
                            <i class="icon-table" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                            <span style="font-size: 12px; opacity: 0.9;">Tabelas</span>
                            <strong style="display: block; font-size: 18px; margin-top: 5px;"><?php echo $database_info['tabelas']; ?></strong>
                        </div>
                    </div>
                    <div class="span3">
                        <div class="stat-box" style="background: #f0ad4e; color: white; padding: 15px; border-radius: 5px; text-align: center;">
                            <i class="icon-backward" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                            <span style="font-size: 12px; opacity: 0.9;">Backups Disponíveis</span>
                            <strong style="display: block; font-size: 18px; margin-top: 5px;"><?php echo count($backups); ?></strong>
                        </div>
                    </div>
                    <div class="span3">
                        <div class="stat-box" style="background: #d9534f; color: white; padding: 15px; border-radius: 5px; text-align: center;">
                            <i class="icon-time" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                            <span style="font-size: 12px; opacity: 0.9;">Último Backup</span>
                            <strong style="display: block; font-size: 14px; margin-top: 5px;">
                                <?php echo $ultimo_backup ? date('d/m/Y H:i', strtotime($ultimo_backup['data'])) : 'Nunca'; ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Ações Principais -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span12">
                        <a href="#" id="btnNovoBackup" class="btn btn-success">
                            <i class="icon-plus icon-white"></i> Realizar Backup Agora
                        </a>
                        <a href="<?php echo site_url('backup/restaurar'); ?>" class="btn btn-warning">
                            <i class="icon-refresh icon-white"></i> Restaurar Backup
                        </a>
                    </div>
                </div>

                <!-- Progresso do Backup -->
                <div id="progressoBackup" style="display: none; margin-bottom: 20px;">
                    <div class="alert alert-info">
                        <i class="icon-spinner icon-spin"></i> Realizando backup, aguarde...
                    </div>
                    <div class="progress progress-striped active">
                        <div class="bar" style="width: 100%;"></div>
                    </div>
                </div>

                <!-- Lista de Backups -->
                <h4 style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-top: 30px;">
                    <i class="icon-list"></i> Backups Disponíveis
                </h4>

                <?php if (empty($backups)): ?>
                    <div class="alert alert-info">
                        <i class="icon-info-sign"></i> Nenhum backup encontrado. Clique em "Realizar Backup Agora" para criar seu primeiro backup.
                    </div>
                <?php else: ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="50"><i class="icon-file"></i></th>
                                <th>Nome do Arquivo</th>
                                <th width="150">Data</th>
                                <th width="100">Tamanho</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php if (strpos($backup['nome'], '.gz') !== false): ?>
                                        <i class="icon-compressed" title="Compactado"></i>
                                    <?php else: ?>
                                        <i class="icon-file-text" title="SQL"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($backup['nome']); ?></strong>
                                    <?php if (strpos($backup['nome'], 'auto_backup_pre_restore') !== false): ?>
                                        <span class="label label-warning">Auto (Pré-Restauração)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($backup['data'])); ?></td>
                                <td><?php echo $backup['tamanho']; ?></td>
                                <td>
                                    <a href="<?php echo site_url('backup/download/' . $backup['nome']); ?>"
                                       class="btn btn-mini btn-primary" title="Download">
                                        <i class="icon-download-alt icon-white"></i>
                                    </a>
                                    <a href="#"
                                       onclick="return verificarBackup('<?php echo $backup['nome']; ?>')"
                                       class="btn btn-mini btn-info" title="Verificar">
                                        <i class="icon-check icon-white"></i>
                                    </a>
                                    <a href="<?php echo site_url('backup/excluir/' . $backup['nome']); ?>"
                                       class="btn btn-mini btn-danger"
                                       onclick="return confirm('Tem certeza que deseja excluir este backup?')"
                                       title="Excluir">
                                        <i class="icon-trash icon-white"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <!-- Dicas -->
                <div class="alert alert-info" style="margin-top: 30px;">
                    <h5><i class="icon-info-sign"></i> Dicas Importantes</h5>
                    <ul style="margin-top: 10px;">
                        <li>Realize backups periodicamente (recomendado: diariamente)</li>
                        <li>Antes de qualquer restauração, é feito um backup automático de segurança</li>
                        <li>Mantenha backups em local seguro (fora do servidor)</li>
                        <li>Verifique sempre o arquivo antes de restaurar</li>
                        <li>Restaurações não podem ser desfeitas automaticamente</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal de Verificação -->
<div id="modalVerificacao" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><i class="icon-check"></i> Verificação de Backup</h3>
    </div>
    <div class="modal-body">
        <div id="verificacaoResultado">
            <p class="text-center"><i class="icon-spinner icon-spin" style="font-size: 24px;"></i> Verificando...</p>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Fechar</button>
    </div>
</div>

<script>
$(document).ready(function() {
    // Botão de novo backup
    $('#btnNovoBackup').click(function(e) {
        e.preventDefault();

        if (!confirm('Deseja realizar um novo backup agora?\n\nIsso pode levar alguns minutos dependendo do tamanho do banco.')) {
            return;
        }

        $('#progressoBackup').show();
        $('#btnNovoBackup').prop('disabled', true);

        $.ajax({
            url: '<?php echo site_url('backup/realizar_backup'); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Backup realizado com sucesso!\n\nArquivo: ' + response.arquivo + '\nTamanho: ' + response.tamanho);
                    location.reload();
                } else {
                    alert('Erro ao realizar backup: ' + response.message);
                    $('#progressoBackup').hide();
                    $('#btnNovoBackup').prop('disabled', false);
                }
            },
            error: function() {
                alert('Erro na comunicação. O backup pode estar sendo processado em segundo plano.');
                location.reload();
            }
        });
    });
});

// Verificar backup
function verificarBackup(arquivo) {
    $('#modalVerificacao').modal('show');
    $('#verificacaoResultado').html('<p class="text-center"><i class="icon-spinner icon-spin" style="font-size: 24px;"></i> Verificando...</p>');

    $.ajax({
        url: '<?php echo site_url('backup/verificar'); ?>',
        type: 'POST',
        data: { arquivo: arquivo },
        dataType: 'json',
        success: function(response) {
            var html = '';
            if (response.valido) {
                html += '<div class="alert alert-success"><i class="icon-ok"></i> <strong>Arquivo válido!</strong></div>';
                html += '<table class="table table-condensed">';
                html += '<tr><td><strong>Tamanho:</strong></td><td>' + response.tamanho + '</td></tr>';
                html += '<tr><td><strong>Estrutura:</strong></td><td>' + (response.tem_estrutura ? '<span class="label label-success">Sim</span>' : '<span class="label">Não</span>') + '</td></tr>';
                html += '<tr><td><strong>Dados:</strong></td><td>' + (response.tem_dados ? '<span class="label label-success">Sim</span>' : '<span class="label">Não</span>') + '</td></tr>';
                html += '</table>';
            } else {
                html = '<div class="alert alert-error"><i class="icon-remove"></i> <strong>Problema encontrado:</strong> ' + response.mensagem + '</div>';
            }
            $('#verificacaoResultado').html(html);
        },
        error: function() {
            $('#verificacaoResultado').html('<div class="alert alert-error">Erro ao verificar arquivo.</div>');
        }
    });

    return false;
}
</script>

<?php $this->load->view('tema/footer.php'); ?>
