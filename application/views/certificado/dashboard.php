<?php
/**
 * Dashboard do Certificado Digital
 */
?>

<style>
.cert-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 20px;
}
.cert-card.valid {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.cert-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.cert-card.expired {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}
.cert-title {
    font-size: 18px;
    margin-bottom: 5px;
}
.cert-info {
    font-size: 14px;
    opacity: 0.9;
}
.status-badge {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    background: rgba(255,255,255,0.2);
}
.consulta-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}
.consulta-item:last-child {
    border-bottom: none;
}
.consulta-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}
.consulta-icon.success {
    background: #d4edda;
    color: #155724;
}
.consulta-icon.error {
    background: #f8d7da;
    color: #721c24;
}
</style>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li class="active">Certificado Digital</li>
        </ul>
    </div>
</div>

<!-- Status do Certificado -->
<div class="row-fluid">
    <div class="span8">
        <?php if ($certificado): ?>
            <?php
            $cardClass = 'valid';
            if ($validade['expirado']) {
                $cardClass = 'expired';
            } elseif ($validade['alerta']) {
                $cardClass = 'warning';
            }
            ?>
            <div class="cert-card <?= $cardClass ?>">
                <div class="row-fluid">
                    <div class="span8">
                        <div class="cert-title">
                            <i class="fas fa-certificate"></i> <?= $certificado->razao_social ?>
                        </div>
                        <div class="cert-info">
                            CNPJ: <?= maskCnpj($certificado->cnpj) ?> <br>
                            Tipo: <?= $certificado->tipo ?>
                            <?= $certificado->emissor ? ' • ' . $certificado->emissor : '' ?>
                        </div>
                    </div>
                    <div class="span4 text-right">
                        <div class="cert-info" style="font-size: 32px; font-weight: bold;">
                            <?= $validade['dias'] ?>
                        </div>
                        <div class="cert-info">dias restantes</div>
                        <div class="status-badge" style="margin-top: 10px;">
                            <?= $validade['expirado'] ? 'EXPIRADO' : ($validade['alerta'] ? 'RENOVAR' : 'VÁLIDO') ?>
                        </div>
                    </div>
                </div>

                <div class="row-fluid" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3);">
                    <div class="span6">
                        <div class="cert-info">
                            <strong>Válido até:</strong> <?= date('d/m/Y H:i', strtotime($certificado->data_validade)) ?>
                        </div>
                    </div>
                    <div class="span6 text-right">
                        <div class="cert-info">
                            <strong>Último acesso:</strong> <?= $certificado->ultimo_acesso ? date('d/m/Y H:i', strtotime($certificado->ultimo_acesso)) : 'Nunca' ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Nenhum certificado configurado!</strong><br>
                Configure seu certificado digital A1 ou A3 para integração automática com a Receita Federal e sistema de impostos.
                <br><br>
                <a href="<?= site_url('certificado/configurar') ?>" class="btn btn-primary">
                    <i class="fas fa-cog"></i> Configurar Certificado
                </a>
            </div>

        <?php endif; ?>
    </div>

    <div class="span4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-bolt"></i></span>
                <h5>Ações Rápidas</h5>
            </div>
            <div class="widget-content">
                <div class="btn-group-vertical">
                    <a href="<?= site_url('certificado/configurar') ?>" class="btn btn-block">
                        <i class="fas fa-cog"></i> Configurar Certificado
                    </a>
                    <?php if ($certificado): ?>
                    <a href="<?= site_url('certificado/consultar_simples') ?>" class="btn btn-block btn-info">
                        <i class="fas fa-sync"></i> Consultar Simples Nacional
                    </a>
                    <a href="<?= site_url('certificado/sincronizar_aliquotas') ?>" class="btn btn-block btn-success">
                        <i class="fas fa-calculator"></i> Sincronizar Alíquotas
                    </a>
                    <a href="<?= site_url('certificado/importar_nfse') ?>" class="btn btn-block btn-warning">
                        <i class="fas fa-file-import"></i> Importar NFS-e
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Consultas Realizadas -->
<div class="row-fluid">
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-history"></i></span>
                <h5>Últimas Consultas</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (empty($consultas)): ?>
                <div class="alert alert-info" style="margin: 15px;">Nenhuma consulta realizada ainda.</div>
                <?php else: ?>
                <?php foreach ($consultas as $c): ?>
                <div class="consulta-item">
                    <div class="consulta-icon <?= $c->sucesso ? 'success' : 'error' ?>">
                        <i class="fas fa-<?= $c->sucesso ? 'check' : 'times' ?>"></i>
                    </div>
                    <div style="flex: 1;">
                        <strong><?= str_replace('_', ' ', $c->tipo_consulta) ?></strong><br>
                        <small class="text-muted">
                            <?= date('d/m/Y H:i', strtotime($c->data_consulta)) ?>
                            <?= $c->error ? ' • Erro: ' . substr($c->erro, 0, 50) : '' ?>
                        </small>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="span6">
        <div class="widget-box"
            <div class="widget-title">
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                <h5>Informações de Integração</h5>
            </div>
            <div class="widget-content">
                <p><strong>O que é integrado automaticamente:</strong></p>
                <ul>
                    <li>Consulta de CNPJ na Receita Federal</li>
                    <li>Consulta Simples Nacional (anexo e situação)</li>
                    <li>Sincronização de alíquotas fiscais</li>
                    <li>Importação de notas fiscais de serviço (NFS-e)</li>
                    <li>Cálculo automático de impostos retidos</li>
                </ul>

                <div class="alert alert-info">
                    <i class="fas fa-shield-alt"></i> <strong>Segurança:</strong><br>
                    O certificado digital é armazenado de forma segura com criptografia AES-256.
                    A senha nunca é armazenada em texto plano.
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function maskCnpj($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '\1.\2.\3/\4-\5', $cnpj);
}
?>
