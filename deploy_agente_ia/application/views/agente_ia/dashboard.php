<?php
/**
 * View: dashboard.php
 * Dashboard geral do Agente IA no MapOS
 */
?>

<style>
.metric-card {
    text-align: center;
    padding: 20px;
    border-radius: 8px;
    color: #fff;
    margin-bottom: 15px;
}
.metric-card .number {
    font-size: 2.2em;
    font-weight: 700;
    display: block;
}
.metric-card .label-text {
    font-size: 0.9em;
    text-transform: uppercase;
    opacity: 0.9;
}
.card-green  { background: linear-gradient(135deg, #11998e, #38ef7d); }
.card-blue   { background: linear-gradient(135deg, #4facfe, #00f2fe); }
.card-orange { background: linear-gradient(135deg, #f2994a, #f2c94c); color:#333; }
.card-red    { background: linear-gradient(135deg, #eb3349, #f45c43); }
.card-purple { background: linear-gradient(135deg, #a18cd1, #fbc2eb); color:#333; }
.dash-section { margin-top: 20px; }
</style>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-bot iconX"></i></span>
                <h5>Dashboard do Agente IA</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('agente_ia/configuracoes'); ?>" class="btn btn-warning btn-mini">
                        <i class="bx bx-cog"></i> Configuracoes
                    </a>
                    <a href="<?php echo site_url('agente_ia/permissoes'); ?>" class="btn btn-info btn-mini">
                        <i class="bx bx-lock-alt"></i> Permissoes
                    </a>
                    <a href="<?php echo site_url('agente_ia/logs'); ?>" class="btn btn-mini">
                        <i class="bx bx-list-ul"></i> Logs
                    </a>
                    <a href="<?php echo site_url('agente_ia/relatorios_templates'); ?>" class="btn btn-mini btn-success">
                        <i class="bx bx-file"></i> Templates
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Cards de metricas -->
                <div class="row-fluid">
                    <div class="span2">
                        <div class="metric-card card-green">
                            <span class="number"><?php echo $stats['aut_pendentes'] ?? 0; ?></span>
                            <span class="label-text">Pendentes</span>
                        </div>
                    </div>
                    <div class="span2">
                        <div class="metric-card card-blue">
                            <span class="number"><?php echo $stats['aut_executadas'] ?? 0; ?></span>
                            <span class="label-text">Executadas Hoje</span>
                        </div>
                    </div>
                    <div class="span2">
                        <div class="metric-card card-orange">
                            <span class="number"><?php echo $stats['interacoes_hoje'] ?? 0; ?></span>
                            <span class="label-text">Interacoes Hoje</span>
                        </div>
                    </div>
                    <div class="span2">
                        <div class="metric-card card-red">
                            <span class="number"><?php echo $stats['aut_expiradas'] ?? 0; ?></span>
                            <span class="label-text">Expiradas</span>
                        </div>
                    </div>
                    <div class="span2">
                        <div class="metric-card card-purple">
                            <span class="number"><?php echo $stats['numeros_vinculados'] ?? 0; ?></span>
                            <span class="label-text">Numeros Vinculados</span>
                        </div>
                    </div>
                    <div class="span2">
                        <div class="metric-card" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff">
                            <span class="number"><?php echo $stats['taxa_aprovacao'] ?? '0%'; ?></span>
                            <span class="label-text">Taxa Aprovacao</span>
                        </div>
                    </div>
                </div>

                <!-- Lista de pendentes rapida -->
                <div class="dash-section">
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-time-five iconX"></i></span>
                            <h5>Autorizacoes Pendentes Rápidas</h5>
                            <a href="<?php echo site_url('agente_ia'); ?>" class="btn btn-mini">Ver todas</a>
                        </div>
                        <div class="widget-content nopadding">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Token</th>
                                        <th>Acao</th>
                                        <th>Numero</th>
                                        <th>Expira em</th>
                                        <th>Nivel</th>
                                        <th width="120">Acoes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($pendentes)): ?>
                                        <tr><td colspan="6" class="center">Nenhuma autorizacao pendente.</td></tr>
                                    <?php else: ?>
                                        <?php foreach (array_slice($pendentes, 0, 10) as $p): ?>
                                            <tr>
                                                <td><span class="label label-info"><?php echo $p['token']; ?></span></td>
                                                <td><?php echo ucwords(str_replace('_', ' ', $p['acao'])); ?></td>
                                                <td><?php echo $p['numero_telefone']; ?></td>
                                                <td><?php
                                                    $min = (strtotime($p['expires_at']) - time()) / 60;
                                                    echo $min > 0 ? round($min) . ' min' : 'Expirou';
                                                ?></td>
                                                <td><span class="label"><?php echo $p['nivel_criticidade']; ?></span></td>
                                                <td>
                                                    <form method="post" action="<?php echo site_url('agente_ia/responder'); ?>" style="margin:0">
                                                        <input type="hidden" name="autorizacao_id" value="<?php echo $p['id']; ?>">
                                                        <button type="submit" name="resposta" value="aprovar" class="btn btn-mini btn-success"><i class="bx bx-check"></i></button>
                                                        <button type="submit" name="resposta" value="rejeitar" class="btn btn-mini btn-danger"><i class="bx bx-x"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Ultimos logs -->
                <div class="dash-section">
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-message-detail iconX"></i></span>
                            <h5>Ultimas Interacoes</h5>
                            <a href="<?php echo site_url('agente_ia/logs'); ?>" class="btn btn-mini">Ver todos</a>
                        </div>
                        <div class="widget-content nopadding">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Numero</th>
                                        <th>Tipo</th>
                                        <th>Intencao</th>
                                        <th>Mensagem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($ultimosLogs)): ?>
                                        <tr><td colspan="5" class="center">Nenhum log recente.</td></tr>
                                    <?php else: ?>
                                        <?php foreach (array_slice($ultimosLogs, 0, 8) as $log): ?>
                                            <tr>
                                                <td><?php echo date('H:i', strtotime($log['created_at'])); ?></td>
                                                <td><?php echo $log['numero_telefone']; ?></td>
                                                <td><span class="label label-<?php
                                                    switch ($log['tipo']) {
                                                        case 'entrada': echo 'success'; break;
                                                        case 'saida': echo 'info'; break;
                                                        case 'sistema': echo 'warning'; break;
                                                        case 'erro': echo 'important'; break;
                                                        default: echo 'default';
                                                    }
                                                ?>"><?php echo ucfirst($log['tipo']); ?></span></td>
                                                <td><?php echo $log['intencao_detectada'] ?? '-'; ?></td>
                                                <td><?php echo htmlspecialchars(mb_strimwidth($log['mensagem'], 0, 60, '...')); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
