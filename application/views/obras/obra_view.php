<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid">
    <div class="span12">
        <!-- Cabeçalho -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-building"></i></span>
                <h5><?php echo $resumo['obra']->nome; ?></h5>
                <div class="buttons">
                    <a href="<?php echo site_url('obras/editar/' . $resumo['obra']->id); ?>" class="btn btn-mini btn-primary">
                        <i class="icon-edit icon-white"></i> Editar
                    </a>
                    <a href="<?php echo site_url('obras/relatorioProgresso/' . $resumo['obra']->id); ?>" class="btn btn-mini btn-info">
                        <i class="icon-print icon-white"></i> Relatório
                    </a>
                </div>
            </div>

            <div class="widget-content">
                <div class="row-fluid">
                    <!-- Progresso -->
                    <div class="span4">
                        <h4>Progresso Geral</h4>
                        <div class="progress" style="height: 30px;">
                            <div class="bar bar-success" style="width: <?php echo $resumo['obra']->percentual_concluido ?? 0; ?>%;">
                                <strong><?php echo $resumo['obra']->percentual_concluido ?? 0; ?>%</strong>
                            </div>
                        </div>
                        <p>
                            <strong>Status:</strong>
                            <span class="label label-info"><?php echo $resumo['obra']->status; ?></span>
                        </p>
                        <p>
                            <strong>Dias Restantes:</strong>
                            <?php if ($resumo['dias_restantes'] !== null): ?>
                                <?php if ($resumo['dias_restantes'] >= 0): ?>
                                    <span class="text-success"><?php echo $resumo['dias_restantes']; ?> dias</span>
                                <?php else: ?>
                                    <span class="text-error"><?php echo abs($resumo['dias_restantes']); ?> dias em atraso</span>
                                <?php endif; ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Informações -->
                    <div class="span4">
                        <h4>Informações</h4>
                        <p><strong>Cliente:</strong> <?php echo $resumo['obra']->cliente_nome; ?></p>
                        <p><strong>Endereço:</strong> <?php echo $resumo['obra']->endereco; ?></p>
                        <p><strong>Gestor:</strong> <?php echo $resumo['obra']->gestor_nome ?? 'Não definido'; ?></p>
                        <p><strong>Previsão:</strong> <?php echo $resumo['obra']->data_fim_prevista ? date('d/m/Y', strtotime($resumo['obra']->data_fim_prevista)) : 'N/A'; ?></p>
                    </div>

                    <!-- Estatísticas -->
                    <div class="span4">
                        <h4>Estatísticas</h4>
                        <div class="row-fluid">
                            <div class="span6 text-center" style="border-right: 1px solid #ddd;">
                                <h3><?php echo $resumo['total_etapas']; ?></h3>
                                <small>Etapas</small>
                            </div>
                            <div class="span6 text-center">
                                <h3><?php echo $estatisticas['total_atividades'] ?? 0; ?></h3>
                                <small>Atividades</small>
                            </div>
                        </div>
                        <hr style="margin: 10px 0;">
                        <div class="row-fluid">
                            <div class="span6 text-center" style="border-right: 1px solid #ddd;">
                                <h3><?php echo $estatisticas['concluidas'] ?? 0; ?></h3>
                                <small>Concluídas</small>
                            </div>
                            <div class="span6 text-center">
                                <h3><?php echo $estatisticas['total_horas'] ?? 0; ?>h</h3>
                                <small>Horas Trabalhadas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Etapas -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-tasks"></i></span>
                <h5>Etapas da Obra</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('obras/etapas/' . $resumo['obra']->id); ?>" class="btn btn-mini btn-primary">
                        <i class="icon-list icon-white"></i> Gerenciar
                    </a>
                </div>
            </div>

            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Status</th>
                            <th>Progresso</th>
                            <th>Atividades</th>
                            <th>Previsão</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($etapas) > 0): ?>
                            <?php foreach ($etapas as $etapa): ?>
                                <tr>
                                    <td><?php echo $etapa->numero_etapa; ?></td>
                                    <td><?php echo $etapa->nome; ?></td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'Nao Iniciada' => 'default',
                                            'Em Andamento' => 'info',
                                            'Concluida' => 'success',
                                            'Atrasada' => 'important',
                                            'Paralisada' => 'warning'
                                        ][$etapa->status] ?? 'default';
                                        ?>
                                        <span class="label label-<?php echo $status_class; ?>"><?php echo $etapa->status; ?></span>
                                    </td>
                                    <td>
                                        <div class="progress" style="margin-bottom: 0;">
                                            <div class="bar" style="width: <?php echo $etapa->percentual_concluido ?? 0; ?>%;"></div>
                                        </div>
                                        <small><?php echo $etapa->percentual_concluido ?? 0; ?>%</small>
                                    </td>
                                    <td>
                                        <?php echo ($etapa->atividades_concluidas ?? 0); ?> / <?php echo ($etapa->total_atividades ?? 0); ?>
                                    </td>
                                    <td>
                                        <?php echo $etapa->data_fim_prevista ? date('d/m/Y', strtotime($etapa->data_fim_prevista)) : 'N/A'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhuma etapa cadastrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Equipe -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-group"></i></span>
                <h5>Equipe</h5>
            </div>

            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Técnico</th>
                            <th>Função</th>
                            <th>Entrada</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($equipe) > 0): ?>
                            <?php foreach ($equipe as $membro): ?>
                                <tr>
                                    <td><?php echo $membro->tecnico_nome; ?></td>
                                    <td><?php echo $membro->funcao; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($membro->data_entrada)); ?></td>
                                    <td>
                                        <span class="label label-<?php echo $membro->ativo ? 'success' : 'default'; ?>">
                                            <?php echo $membro->ativo ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Nenhum técnico alocado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- OS Vinculadas -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-file-alt"></i></span>
                <h5>Ordens de Serviço Vinculadas</h5>
            </div>

            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>OS #</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Responsável</th>
                            <th>Status</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($os_vinculadas) > 0): ?>
                            <?php foreach ($os_vinculadas as $os): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo site_url('os/visualizar/' . $os->idOs); ?>" target="_blank">
                                            <?php echo $os->idOs; ?>
                                        </a>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($os->dataInicial)); ?></td>
                                    <td><?php echo $os->nomeCliente; ?></td>
                                    <td><?php echo $os->responsavel ?? 'N/A'; ?></td>
                                    <td>
                                        <?php
                                        $status_os = [
                                            'Aberto' => 'important',
                                            'Em Andamento' => 'info',
                                            'Finalizado' => 'success',
                                            'Cancelado' => 'inverse',
                                            'Aguardando Peças' => 'warning'
                                        ][$os->status] ?? 'default';
                                        ?>
                                        <span class="label label-<?php echo $status_os; ?>"><?php echo $os->status; ?></span>
                                    </td>
                                    <td>R$ <?php echo number_format($os->valorTotal ?? 0, 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhuma OS vinculada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Links Rápidos -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-link"></i></span>
                <h5>Links Rápidos</h5>
            </div>

            <div class="widget-content">
                <a href="<?php echo site_url('obras/atividades/' . $resumo['obra']->id); ?>" class="btn btn-info">
                    <i class="icon-tasks icon-white"></i> Atividades
                </a>
                <a href="<?php echo site_url('obras/relatorioDiario/' . $resumo['obra']->id); ?>" class="btn btn-warning">
                    <i class="icon-calendar icon-white"></i> Diário de Obra
                </a>
                <a href="<?php echo site_url('obras/relatorioProgresso/' . $resumo['obra']->id); ?>" class="btn btn-success">
                    <i class="icon-bar-chart icon-white"></i> Relatório de Progresso
                </a>
            </div>
        </div>
    </div>
</div>
