<?php
/**
 * Configuracoes de Notificacoes por Email
 */
?>

<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li><a href="<?= base_url('emails/dashboard') ?>">Emails</a><span class="divider">/</span></li>
            <li class="active">Configuracoes</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php if ($this->session->flashdata('success')) { ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <?= $this->session->flashdata('success') ?>
            </div>
        <?php } ?>
        <?php if ($this->session->flashdata('error')) { ?>
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <?= $this->session->flashdata('error') ?>
            </div>
        <?php } ?>
    </div>
</div>

<form action="<?= base_url('email/salvar_configuracoes') ?>" method="post">
    <div class="row-fluid">
        <!-- Toggle Geral -->
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-power-off"></i></span>
                    <h5>Ativacao Geral</h5>
                </div>
                <div class="widget-content">
                    <label class="checkbox">
                        <input type="checkbox" name="email_automatico_v5" <?= ($configs['email_automatico_v5'] ?? true) ? 'checked' : '' ?>>
                        <strong>Ativar envio automatico de emails (Sistema V5)</strong>
                    </label>
                    <p class="text-info" style="margin-top: 10px;">
                        <i class="fas fa-info-circle"></i>
                        Quando ativado, os emails serao enviados automaticamente sem necessidade de cron externo.
                        O sistema processa a fila durante as requisicoes normais do sistema.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <!-- Notificacoes de OS -->
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-wrench"></i></span>
                    <h5>Ordens de Servico</h5>
                </div>
                <div class="widget-content">
                    <label class="checkbox">
                        <input type="checkbox" name="email_notif_os_criada" <?= ($configs['email_notif_os_criada'] ?? true) ? 'checked' : '' ?>>
                        Enviar email quando uma OS for criada
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="email_notif_os_editada" <?= ($configs['email_notif_os_editada'] ?? true) ? 'checked' : '' ?>>
                        Enviar email quando uma OS for editada
                    </label>
                </div>
            </div>
        </div>

        <!-- Notificacoes de Vendas -->
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                    <h5>Vendas</h5>
                </div>
                <div class="widget-content">
                    <label class="checkbox">
                        <input type="checkbox" name="email_notif_venda" <?= ($configs['email_notif_venda'] ?? true) ? 'checked' : '' ?>>
                        Enviar email quando uma venda for iniciada
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <!-- Notificacoes de Cobrancas -->
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-dollar-sign"></i></span>
                    <h5>Cobrancas</h5>
                </div>
                <div class="widget-content">
                    <label class="checkbox">
                        <input type="checkbox" name="email_notif_cobranca" <?= ($configs['email_notif_cobranca'] ?? true) ? 'checked' : '' ?>>
                        Enviar email quando uma cobranca for gerada
                    </label>
                </div>
            </div>
        </div>

        <!-- Notificacoes de Obras -->
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-building"></i></span>
                    <h5>Obras</h5>
                </div>
                <div class="widget-content">
                    <label class="checkbox">
                        <input type="checkbox" name="email_notif_obra_nova" <?= ($configs['email_notif_obra_nova'] ?? true) ? 'checked' : '' ?>>
                        Enviar email quando uma obra for cadastrada
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="email_notif_obra_concluida" <?= ($configs['email_notif_obra_concluida'] ?? true) ? 'checked' : '' ?>>
                        Enviar email quando uma obra for concluida
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="email_notif_atividade_atrasada" <?= ($configs['email_notif_atividade_atrasada'] ?? true) ? 'checked' : '' ?>>
                        Enviar email quando atividade estiver atrasada
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="email_notif_impedimento" <?= ($configs['email_notif_impedimento'] ?? true) ? 'checked' : '' ?>>
                        Enviar email quando impedimento for registrado
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12" style="text-align: center; margin-top: 20px;">
            <button type="submit" class="btn btn-primary btn-large">
                <i class="fas fa-save"></i> Salvar Configuracoes
            </button>
        </div>
    </div>
</form>