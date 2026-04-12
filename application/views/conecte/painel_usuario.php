<?php
/**
 * View: Painel do Usuário do Portal do Cliente
 */
?>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-home"></i></span>
                <h5>Bem-vindo, <?= htmlspecialchars($usuario->nome) ?>!</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span8">
                        <p><strong>CNPJs vinculados:</strong></p>
                        <?php if (!empty($cnpjs)): ?>
                            <?php foreach ($cnpjs as $cnpj): ?>
                                <span class="label label-info" style="margin-right: 5px; display: inline-block; margin-bottom: 5px;">
                                    <i class="bx bx-buildings"></i> <?= $cnpj->cnpj ?>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="label">Nenhum CNPJ vinculado</span>
                        <?php endif; ?>
                    </div>
                    <div class="span4 text-right">
                        <a href="<?= site_url('mine/perfil') ?>" class="btn btn-small btn-info">
                            <i class="bx bx-user"></i> Meu Perfil
                        </a>
                        <a href="<?= site_url('mine/sair_usuario') ?>" class="btn btn-small btn-danger">
                            <i class="bx bx-log-out"></i> Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row-fluid">
    <div class="span3">
        <div class="widget-box" style="background: #f39c12; color: #fff;">
            <div class="widget-content" style="text-align: center; padding: 20px;">
                <h1 style="margin: 0; font-size: 48px;"><?= $stats['total'] ?></h1>
                <p style="margin: 10px 0 0; font-size: 16px;">Total de OS</p>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="widget-box" style="background: #e74c3c; color: #fff;">
            <div class="widget-content" style="text-align: center; padding: 20px;">
                <h1 style="margin: 0; font-size: 48px;"><?= $stats['Aberto'] + $stats['Orçamento'] ?></h1>
                <p style="margin: 10px 0 0; font-size: 16px;">Em Aberto</p>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="widget-box" style="background: #3498db; color: #fff;">
            <div class="widget-content" style="text-align: center; padding: 20px;">
                <h1 style="margin: 0; font-size: 48px;"><?= $stats['Em Andamento'] ?></h1>
                <p style="margin: 10px 0 0; font-size: 16px;">Em Andamento</p>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="widget-box" style="background: #2ecc71; color: #fff;">
            <div class="widget-content" style="text-align: center; padding: 20px;">
                <h1 style="margin: 0; font-size: 48px;"><?= $stats['Finalizado'] ?></h1>
                <p style="margin: 10px 0 0; font-size: 16px;">Finalizadas</p>
            </div>
        </div>
    </div>
</div>

<!-- Ordens de Serviço Recentes -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-file"></i></span>
                <h5>Ordens de Serviço Recentes</h5>
                <div class="buttons">
                    <a href="<?= site_url('mine/os') ?>" class="btn btn-mini btn-info">
                        <i class="bx bx-list-ul"></i> Ver Todas
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($os)): ?>
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>OS #</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>CNPJ</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($os as $o): ?>
                            <tr>
                                <td><?= sprintf('%04d', $o->idOs) ?></td>
                                <td><?= date('d/m/Y', strtotime($o->dataInicial)) ?></td>
                                <td><?= htmlspecialchars($o->nomeCliente) ?></td>
                                <td><?= $o->documento ?></td>
                                <td>
                                    <span class="label" style="background-color: <?= getStatusColor($o->status) ?>">
                                        <?= $o->status ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('mine/visualizarOs/' . $o->idOs) ?>" class="btn btn-mini btn-info" title="Visualizar">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="bx bx-info-circle"></i> Nenhuma ordem de serviço encontrada para seus CNPJs vinculados.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    $colors = [
        'Aberto' => '#e74c3c',
        'Orçamento' => '#f39c12',
        'Negociação' => '#9b59b6',
        'Aprovado' => '#3498db',
        'Em Andamento' => '#2ecc71',
        'Aguardando Peças' => '#e67e22',
        'Finalizado' => '#27ae60',
        'Faturado' => '#34495e',
        'Cancelado' => '#95a5a6'
    ];
    return $colors[$status] ?? '#95a5a6';
}
?>
