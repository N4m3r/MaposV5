<?php
/**
 * View: Visualizar Detalhes do Usuário do Portal do Cliente
 */
?>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('usuarioscliente') ?>">Usuários Cliente</a> <span class="divider">/</span></li>
            <li class="active"><?= htmlspecialchars($usuario->nome) ?></li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span4">
        <!-- Informações do Usuário -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-user"></i></span>
                <h5>Dados do Usuário</h5>
                <div class="buttons">
                    <a href="<?= site_url('usuarioscliente/editar/' . $usuario->id) ?>" class="btn btn-mini btn-warning">
                        <i class="bx bx-edit"></i> Editar
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <p><strong>Nome:</strong> <?= htmlspecialchars($usuario->nome) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($usuario->email) ?></p>
                <p><strong>Telefone:</strong> <?= $usuario->telefone ? htmlspecialchars($usuario->telefone) : '-' ?></p>
                <p><strong>Status:</strong>
                    <?php if ($usuario->ativo): ?>
                        <span class="label label-success">Ativo</span>
                    <?php else: ?>
                        <span class="label label-important">Inativo</span>
                    <?php endif; ?>
                </p>
                <p><strong>Último Acesso:</strong> <?= $usuario->ultimo_acesso ? date('d/m/Y H:i', strtotime($usuario->ultimo_acesso)) : 'Nunca' ?></p>
                <p><strong>Cadastrado em:</strong> <?= date('d/m/Y H:i', strtotime($usuario->created_at)) ?></p>
            </div>
        </div>

        <!-- CNPJs Vinculados -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-buildings"></i></span>
                <h5>CNPJs Vinculados</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($cnpjs)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>CNPJ</th>
                                <th>Razão Social</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cnpjs as $cnpj): ?>
                            <tr>
                                <td><?= $cnpj->cnpj ?></td>
                                <td><?= htmlspecialchars($cnpj->razao_social ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 10px;">
                        Nenhum CNPJ vinculado.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-stats"></i></span>
                <h5>Estatísticas de OS</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span6">
                        <p><strong>Total:</strong> <span class="badge badge-info"><?= $stats['total'] ?></span></p>
                        <p><strong>Abertas:</strong> <span class="badge badge-warning"><?= $stats['Aberto'] ?></span></p>
                        <p><strong>Orçamento:</strong> <span class="badge"><?= $stats['Orçamento'] ?></span></p>
                    </div>
                    <div class="span6">
                        <p><strong>Finalizadas:</strong> <span class="badge badge-success"><?= $stats['Finalizado'] ?></span></p>
                        <p><strong>Faturadas:</strong> <span class="badge badge-inverse"><?= $stats['Faturado'] ?></span></p>
                        <p><strong>Canceladas:</strong> <span class="badge badge-important"><?= $stats['Cancelado'] ?></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="span8">
        <!-- Permissões -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-shield"></i></span>
                <h5>Permissões Configuradas</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <?php
                    $grupos = [
                        'Visualização' => ['visualizar_os', 'visualizar_detalhes_os', 'visualizar_produtos_os', 'visualizar_servicos_os', 'visualizar_anexos_os', 'visualizar_documentos_fiscais'],
                        'Financeiro' => ['visualizar_financeiro', 'visualizar_historico_pagamentos'],
                        'Ações' => ['imprimir_os', 'solicitar_orcamento', 'aprovar_os', 'editar_perfil'],
                        'Outros' => ['receber_notificacoes', 'acesso_mobile']
                    ];
                    ?>
                    <?php foreach ($grupos as $nome => $chaves): ?>
                    <div class="span3">
                        <h6><?= $nome ?></h6>
                        <?php foreach ($chaves as $chave): ?>
                            <?php if (isset($permissoes[$chave]) && $permissoes[$chave]): ?>
                                <span class="label label-success"><i class="bx bx-check"></i></span>
                            <?php else: ?>
                                <span class="label"><i class="bx bx-x"></i></span>
                            <?php endif; ?>
                            <small><?= str_replace(['visualizar_', '_'], ['', ' '], $chave) ?></small><br>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Ordens de Serviço -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-file"></i></span>
                <h5>Ordens de Serviço Vinculadas (<?= count($os) ?>)</h5>
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
                            <?php foreach (array_slice($os, 0, 20) as $o): ?>
                            <tr>
                                <td><?= sprintf('%04d', $o->idOs) ?></td>
                                <td><?= date('d/m/Y', strtotime($o->dataInicial)) ?></td>
                                <td><?= htmlspecialchars($o->nomeCliente) ?></td>
                                <td><?= $o->documento ?></td>
                                <td><span class="label"><?= $o->status ?></span></td>
                                <td class="text-center">
                                    <a href="<?= site_url('os/visualizar/' . $o->idOs) ?>" class="btn btn-mini btn-info" target="_blank">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (count($os) > 20): ?>
                        <div class="alert alert-info" style="margin: 10px;">
                            Exibindo 20 de <?= count($os) ?> OS. <a href="<?= site_url('os?cliente=' . ($usuario->cliente_id ?? '')) ?>">Ver todas</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 10px;">
                        Nenhuma ordem de serviço vinculada aos CNPJs deste usuário.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
