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

    <!-- ABA 1: Ativacao Geral -->
    <div class="row-fluid">
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

    <!-- ABA 2: Servidor SMTP -->
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-server"></i></span>
                    <h5>Servidor de Email (SMTP)</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span4">
                            <label for="email_smtp_host">Servidor SMTP</label>
                            <input type="text" id="email_smtp_host" name="email_smtp_host" class="span12" value="<?= htmlspecialchars($configs['email_smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                        </div>
                        <div class="span2">
                            <label for="email_smtp_port">Porta</label>
                            <input type="number" id="email_smtp_port" name="email_smtp_port" class="span12" value="<?= htmlspecialchars($configs['email_smtp_port'] ?? '587') ?>" placeholder="587">
                        </div>
                        <div class="span2">
                            <label for="email_smtp_crypto">Criptografia</label>
                            <select id="email_smtp_crypto" name="email_smtp_crypto" class="span12">
                                <option value="tls" <?= ($configs['email_smtp_crypto'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                <option value="ssl" <?= ($configs['email_smtp_crypto'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            </select>
                        </div>
                        <div class="span4">
                            <label for="email_from">Email de Envio (From)</label>
                            <input type="email" id="email_from" name="email_from" class="span12" value="<?= htmlspecialchars($configs['email_from'] ?? '') ?>" placeholder="contato@empresa.com">
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-top: 10px;">
                        <div class="span4">
                            <label for="email_smtp_user">Usuario SMTP</label>
                            <input type="text" id="email_smtp_user" name="email_smtp_user" class="span12" value="<?= htmlspecialchars($configs['email_smtp_user'] ?? '') ?>" placeholder="usuario@gmail.com">
                        </div>
                        <div class="span4">
                            <label for="email_smtp_pass">Senha SMTP</label>
                            <input type="password" id="email_smtp_pass" name="email_smtp_pass" class="span12" value="<?= htmlspecialchars($configs['email_smtp_pass'] ?? '') ?>" placeholder="********">
                        </div>
                        <div class="span4">
                            <label for="email_from_name">Nome de Exibicao</label>
                            <input type="text" id="email_from_name" name="email_from_name" class="span12" value="<?= htmlspecialchars($configs['email_from_name'] ?? 'Sistema') ?>" placeholder="Minha Empresa">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ABA 3: Ajuste de Tempo (Poor Man's Cron) -->
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-clock"></i></span>
                    <h5>Frequencia de Disparo (Poor Man's Cron)</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span6">
                            <label for="email_queue_interval">Intervalo entre processamentos (segundos)</label>
                            <input type="number" id="email_queue_interval" name="email_queue_interval" class="span12" value="<?= (int)($configs['email_queue_interval'] ?? 60) ?>" min="10" max="3600">
                            <span class="help-block">Quanto tempo o sistema espera entre cada verificacao da fila. Minimo 10s.</span>
                        </div>
                        <div class="span6">
                            <label for="email_batch_size">Emails por processamento</label>
                            <input type="number" id="email_batch_size" name="email_batch_size" class="span12" value="<?= (int)($configs['email_batch_size'] ?? 3) ?>" min="1" max="50">
                            <span class="help-block">Quantidade maxima de emails enviados em cada ciclo. Recomendado: 3-10.</span>
                        </div>
                    </div>
                    <p class="text-info" style="margin-top: 15px;">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Dica:</strong> Se o servidor for lento ou tiver muitos acessos, aumente o intervalo.
                        Se precisar enviar emails rapidamente, diminua o intervalo e aumente o batch size.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ABA 4: Gatilhos de Envio -->
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-bolt"></i></span>
                    <h5>Gatilhos de Envio Automatico</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <!-- OS -->
                        <div class="span3">
                            <h6><i class="fas fa-wrench"></i> Ordens de Servico</h6>
                            <label class="checkbox">
                                <input type="checkbox" name="email_notif_os_criada" <?= ($configs['email_notif_os_criada'] ?? true) ? 'checked' : '' ?>>
                                OS Criada
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" name="email_notif_os_editada" <?= ($configs['email_notif_os_editada'] ?? true) ? 'checked' : '' ?>>
                                OS Editada
                            </label>
                        </div>
                        <!-- Vendas -->
                        <div class="span3">
                            <h6><i class="fas fa-shopping-cart"></i> Vendas</h6>
                            <label class="checkbox">
                                <input type="checkbox" name="email_notif_venda" <?= ($configs['email_notif_venda'] ?? true) ? 'checked' : '' ?>>
                                Venda Iniciada
                            </label>
                        </div>
                        <!-- Cobrancas -->
                        <div class="span3">
                            <h6><i class="fas fa-dollar-sign"></i> Cobrancas</h6>
                            <label class="checkbox">
                                <input type="checkbox" name="email_notif_cobranca" <?= ($configs['email_notif_cobranca'] ?? true) ? 'checked' : '' ?>>
                                Cobranca Gerada
                            </label>
                        </div>
                        <!-- Obras -->
                        <div class="span3">
                            <h6><i class="fas fa-building"></i> Obras</h6>
                            <label class="checkbox">
                                <input type="checkbox" name="email_notif_obra_nova" <?= ($configs['email_notif_obra_nova'] ?? true) ? 'checked' : '' ?>>
                                Obra Cadastrada
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" name="email_notif_obra_concluida" <?= ($configs['email_notif_obra_concluida'] ?? true) ? 'checked' : '' ?>>
                                Obra Concluida
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" name="email_notif_atividade_atrasada" <?= ($configs['email_notif_atividade_atrasada'] ?? true) ? 'checked' : '' ?>>
                                Atividade Atrasada
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" name="email_notif_impedimento" <?= ($configs['email_notif_impedimento'] ?? true) ? 'checked' : '' ?>>
                                Impedimento Registrado
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ABA 5: Templates por Evento -->
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-file-code"></i></span>
                    <h5>Templates por Evento</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span4">
                            <label for="email_template_os_criada">OS Criada</label>
                            <select id="email_template_os_criada" name="email_template_os_criada" class="span12">
                                <?php foreach ($templates as $t): ?>
                                    <option value="<?= $t ?>" <?= ($configs['email_template_os_criada'] ?? 'os_nova') === $t ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="span4">
                            <label for="email_template_os_editada">OS Editada</label>
                            <select id="email_template_os_editada" name="email_template_os_editada" class="span12">
                                <?php foreach ($templates as $t): ?>
                                    <option value="<?= $t ?>" <?= ($configs['email_template_os_editada'] ?? 'os_atualizada') === $t ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="span4">
                            <label for="email_template_venda">Venda Realizada</label>
                            <select id="email_template_venda" name="email_template_venda" class="span12">
                                <?php foreach ($templates as $t): ?>
                                    <option value="<?= $t ?>" <?= ($configs['email_template_venda'] ?? 'venda_realizada') === $t ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-top: 10px;">
                        <div class="span4">
                            <label for="email_template_cobranca">Cobranca Gerada</label>
                            <select id="email_template_cobranca" name="email_template_cobranca" class="span12">
                                <?php foreach ($templates as $t): ?>
                                    <option value="<?= $t ?>" <?= ($configs['email_template_cobranca'] ?? 'cobranca') === $t ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="span4">
                            <label for="email_template_obra_nova">Obra Cadastrada</label>
                            <select id="email_template_obra_nova" name="email_template_obra_nova" class="span12">
                                <?php foreach ($templates as $t): ?>
                                    <option value="<?= $t ?>" <?= ($configs['email_template_obra_nova'] ?? 'obra_nova') === $t ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="span4">
                            <label for="email_template_obra_concluida">Obra Concluida</label>
                            <select id="email_template_obra_concluida" name="email_template_obra_concluida" class="span12">
                                <?php foreach ($templates as $t): ?>
                                    <option value="<?= $t ?>" <?= ($configs['email_template_obra_concluida'] ?? 'obra_concluida') === $t ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ABA 6: Destinatarios Customizados -->
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-users"></i></span>
                    <h5>Destinatarios Customizados (CC / BCC)</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span6">
                            <label for="email_cc_default">CC Padrao (copia)</label>
                            <input type="text" id="email_cc_default" name="email_cc_default" class="span12" value="<?= htmlspecialchars($configs['email_cc_default'] ?? '') ?>" placeholder="email1@empresa.com, email2@empresa.com">
                            <span class="help-block">Emails em copia separados por virgula.</span>
                        </div>
                        <div class="span6">
                            <label for="email_bcc_default">BCC Padrao (copia oculta)</label>
                            <input type="text" id="email_bcc_default" name="email_bcc_default" class="span12" value="<?= htmlspecialchars($configs['email_bcc_default'] ?? '') ?>" placeholder="email1@empresa.com, email2@empresa.com">
                            <span class="help-block">Emails em copia oculta separados por virgula.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ABA 7: Blacklist -->
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-ban"></i></span>
                    <h5>Blacklist de Emails</h5>
                </div>
                <div class="widget-content">
                    <label for="email_blacklist">Emails bloqueados</label>
                    <textarea id="email_blacklist" name="email_blacklist" class="span12" rows="4" placeholder="Digite um email por linha&#10;exemplo@spam.com&#10;outro@bloqueado.com"><?= htmlspecialchars($configs['email_blacklist'] ?? '') ?></textarea>
                    <span class="help-block">Emails nesta lista nao receberao notificacoes automaticas. Um email por linha.</span>
                    <?php
                    $blacklist = array_filter(array_map('trim', explode("\n", $configs['email_blacklist'] ?? '')));
                    if (!empty($blacklist)):
                    ?>
                        <p class="text-warning"><strong><?= count($blacklist) ?></strong> email(s) bloqueado(s).</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ABA 8: Teste de Envio -->
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-paper-plane"></i></span>
                    <h5>Teste de Envio</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span8">
                            <label for="email_teste">Email de destino para teste</label>
                            <input type="email" id="email_teste" name="email_teste" class="span12" placeholder="seu@email.com">
                        </div>
                        <div class="span4" style="padding-top: 25px;">
                            <button type="submit" formaction="<?= base_url('email/testar_envio') ?>" class="btn btn-success span12">
                                <i class="fas fa-paper-plane"></i> Enviar Email de Teste
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12" style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
            <button type="submit" class="btn btn-primary btn-large">
                <i class="fas fa-save"></i> Salvar Configuracoes
            </button>
            <a href="<?= base_url('emails/dashboard') ?>" class="btn btn-large">
                <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>
</form>
