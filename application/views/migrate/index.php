<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('dashboard'); ?>">Dashboard</a> <span class="divider">/</span></li>
            <li class="active">Gerenciador de Migrações</li>
        </ul>
    </div>
</div>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-database"></i></span>
                <h5>Gerenciador de Migrações do Banco de Dados</h5>
            </div>

            <div class="widget-content">
                <!-- Alertas -->
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <!-- Informações -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span6">
                        <div class="alert alert-info">
                            <strong>Versão Atual:</strong> <span id="current-version"><?php echo $current_version ?: 'Nenhuma'; ?></span><br>
                            <strong>Total de Migrações:</strong> <?php echo count($migrations); ?><br>
                            <strong>Pendentes:</strong> <span id="pending-count">0</span>
                        </div>
                    </div>
                    <div class="span6" style="text-align: right;">
                        <button id="btn-run-all" class="btn btn-success btn-large">
                            <i class="fas fa-play"></i> Executar Todas as Migrações
                        </button>
                    </div>
                </div>

                <!-- Tabela de Migrações -->
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="fas fa-list"></i></span>
                        <h5>Lista de Migrações</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="100">Versão</th>
                                    <th>Nome</th>
                                    <th width="100">Status</th>
                                    <th width="150">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($migrations as $migration): ?>
                                    <tr class="<?php echo $migration['applied'] ? 'success' : ''; ?>">
                                        <td><?php echo $migration['version']; ?></td>
                                        <td><?php echo $migration['name']; ?></td>
                                        <td>
                                            <?php if ($migration['applied']): ?>
                                                <span class="label label-success"><i class="fas fa-check"></i> Aplicada</span>
                                            <?php else: ?>
                                                <span class="label label-warning"><i class="fas fa-clock"></i> Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!$migration['applied']): ?>
                                                <button class="btn btn-mini btn-success btn-run-version" data-version="<?php echo $migration['version']; ?>">
                                                    <i class="fas fa-play"></i> Executar
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-mini" disabled>
                                                    <i class="fas fa-check"></i> Aplicada
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Scripts SQL -->
                <div class="row-fluid" style="margin-top: 20px;">
                    <div class="span12">
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Migração Manual via SQL</h5>
                            <p>Se as migrações automáticas não funcionarem, você pode executar o script SQL manualmente:</p>
                            <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;">
# Acesse seu banco de dados MySQL e execute:
# mysql -u usuario -p banco_de_dados < updates/update_checkin_tabelas.sql

# Ou use o phpMyAdmin para importar o arquivo
# updates/update_checkin_tabelas.sql
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Obtém token CSRF
    function getCookie(name) {
        var value = '; ' + document.cookie;
        var parts = value.split('; ' + name + '=');
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content') || 'MAPOS_CSRF_TOKEN';
    var csrfCookieName = $('meta[name="csrf-cookie-name"]').attr('content') || 'MAPOS_CSRF_COOKIE';

    // Atualiza contador de pendentes
    function updatePendingCount() {
        var pending = $('span.label-warning').length;
        $('#pending-count').text(pending);
    }
    updatePendingCount();

    // Executar todas as migrações
    $('#btn-run-all').on('click', function() {
        if (!confirm('Tem certeza que deseja executar todas as migrações pendentes?')) {
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Executando...');

        $.ajax({
            url: '<?php echo site_url("migrate/latest"); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                [csrfTokenName]: getCookie(csrfCookieName)
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Erro: ' + response.message);
                    $btn.prop('disabled', false).html('<i class="fas fa-play"></i> Executar Todas as Migrações');
                }
            },
            error: function(xhr, status, error) {
                alert('Erro na requisição: ' + error);
                $btn.prop('disabled', false).html('<i class="fas fa-play"></i> Executar Todas as Migrações');
            }
        });
    });

    // Executar migração específica
    $('.btn-run-version').on('click', function() {
        var version = $(this).data('version');

        if (!confirm('Executar migração ' + version + '?')) {
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Executando...');

        $.ajax({
            url: '<?php echo site_url("migrate/version/"); ?>' + version,
            type: 'POST',
            dataType: 'json',
            data: {
                [csrfTokenName]: getCookie(csrfCookieName)
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Erro: ' + response.message);
                    $btn.prop('disabled', false).html('<i class="fas fa-play"></i> Executar');
                }
            },
            error: function(xhr, status, error) {
                alert('Erro na requisição: ' + error);
                $btn.prop('disabled', false).html('<i class="fas fa-play"></i> Executar');
            }
        });
    });
});
</script>
