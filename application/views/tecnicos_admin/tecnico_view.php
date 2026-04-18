<?php
/**
 * View de Detalhes do Técnico
 * Exibe informações completas, estatísticas, OS recentes e estoque
 */

if (!isset($tecnico) || !$tecnico) {
    echo '<div class="alert alert-error">Técnico não encontrado.</div>';
    return;
}

$id = $tecnico->idUsuarios ?? $tecnico->id;
$estatisticas = $estatisticas ?? ['os_concluidas' => 0, 'horas_trabalhadas' => 0, 'material_utilizado' => 0];
$os_recentes = $os_recentes ?? [];
$estoque = $estoque ?? [];
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-user"></i></span>
                <h5>Detalhes do Técnico</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos_admin/editar_tecnico/' . $id); ?>" class="btn btn-mini btn-warning">
                        <i class="icon-edit icon-white"></i> Editar
                    </a>
                    <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="btn btn-mini">
                        <i class="icon-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Informações Básicas -->
                <div class="row-fluid">
                    <div class="span4">
                        <h4><i class="icon-user"></i> <?php echo $tecnico->nome; ?></h4>
                        <table class="table table-bordered">
                            <tr><td><strong>E-mail:</strong></td><td><?php echo $tecnico->email; ?></td></tr>
                            <tr><td><strong>Telefone:</strong></td><td><?php echo $tecnico->telefone ?: '-'; ?></td></tr>
                            <tr><td><strong>CPF:</strong></td><td><?php echo $tecnico->cpf ?: '-'; ?></td></tr>
                            <tr><td><strong>Nível:</strong></td><td><?php echo $tecnico->nivel_tecnico ?: 'II'; ?></td></tr>
                            <tr><td><strong>Especialidades:</strong></td><td><?php echo $tecnico->especialidades ?: '-'; ?></td></tr>
                        </table>
                    </div>
                    <div class="span4">
                        <h4><i class="icon-car"></i> Veículo</h4>
                        <table class="table table-bordered">
                            <tr><td><strong>Tipo:</strong></td><td><?php echo $tecnico->veiculo_tipo ?: '-'; ?></td></tr>
                            <tr><td><strong>Placa:</strong></td><td><?php echo $tecnico->veiculo_placa ?: '-'; ?></td></tr>
                            <tr><td><strong>Raio Atuação:</strong></td><td><?php echo ($tecnico->raio_atuacao_km ?? 0) . ' km'; ?></td></tr>
                            <tr><td><strong>Plantão 24h:</strong></td><td>
                                <span class="badge badge-<?php echo ($tecnico->plantao_24h ?? 0) ? 'success' : 'default'; ?>">
                                    <?php echo ($tecnico->plantao_24h ?? 0) ? 'Sim' : 'Não'; ?>
                                </span>
                            </td></tr>
                            <tr><td><strong>Status:</strong></td><td>
                                <span class="badge badge-<?php echo ($tecnico->ativo ?? 1) ? 'success' : 'important'; ?>">
                                    <?php echo ($tecnico->ativo ?? 1) ? 'Ativo' : 'Inativo'; ?>
                                </span>
                            </td></tr>
                        </table>
                    </div>
                    <div class="span4">
                        <h4><i class="icon-dashboard"></i> Estatísticas (Mês)</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>OS Concluídas:</strong></td>
                                <td><span class="badge badge-success"><?php echo $estatisticas['os_concluidas']; ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Horas Trabalhadas:</strong></td>
                                <td><span class="badge badge-info"><?php echo $estatisticas['horas_trabalhadas']; ?>h</span></td>
                            </tr>
                            <tr>
                                <td><strong>Material Utilizado:</strong></td>
                                <td><span class="badge badge-warning"><?php echo $estatisticas['material_utilizado']; ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- OS Recentes -->
                <div class="row-fluid">
                    <div class="span8">
                        <h4><i class="icon-tasks"></i> Ordens de Serviço Recentes</h4>
                        <?php if (!empty($os_recentes)): ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>OS #</th>
                                        <th>Cliente</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($os_recentes, 0, 10) as $os): ?>
                                        <tr>
                                            <td><?php echo $os->idOs ?? $os->os_id; ?></td>
                                            <td><?php echo $os->cliente_nome ?? 'N/A'; ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo ($os->status == 'concluida' || $os->status == 'Finalizada') ? 'success' : 'warning'; ?>">
                                                    <?php echo $os->status; ?>
                                                </span>
                                            </td>
                                            <td><?php echo isset($os->data_checkin) ? date('d/m/Y', strtotime($os->data_checkin)) : '-'; ?></td>
                                            <td>
                                                <a href="<?php echo site_url('os/visualizar/' . ($os->idOs ?? $os->os_id)); ?>" class="btn btn-mini btn-info">
                                                    <i class="icon-eye-open icon-white"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">Nenhuma OS encontrada para este técnico.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Estoque -->
                    <div class="span4">
                        <h4><i class="icon-shopping-cart"></i> Estoque no Veículo</h4>
                        <a href="<?php echo site_url('tecnicos_admin/estoque_tecnico/' . $id); ?>" class="btn btn-mini btn-success pull-right">
                            <i class="icon-plus icon-white"></i> Gerenciar Estoque
                        </a>
                        <div class="clearfix"></div>
                        <?php if (!empty($estoque)): ?>
                            <table class="table table-bordered table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Qtd</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($estoque, 0, 10) as $item): ?>
                                        <tr>
                                            <td><?php echo $item->produto_nome; ?></td>
                                            <td><span class="badge"><?php echo $item->quantidade; ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">Nenhum item em estoque.</div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
