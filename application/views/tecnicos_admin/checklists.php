<?php
/**
 * Templates de Checklists
 */
$checklists = $checklists ?? [];
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-check"></i></span>
                <h5>Templates de Checklist</h5>
                <div class="buttons">
                    <a href="#" class="btn btn-mini btn-success">
                        <i class="icon-plus icon-white"></i> Novo Template
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($checklists)): ?>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Itens</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($checklists as $checklist): ?>
                                <tr>
                                    <td><?php echo $checklist->id; ?></td>
                                    <td><?php echo $checklist->nome; ?></td>
                                    <td><?php echo $checklist->categoria ?? '-'; ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo isset($checklist->itens) ? count($checklist->itens) : '0'; ?> itens
                                        </span>
                                    </td>
                                    <td><?php echo isset($checklist->criado_em) ? date('d/m/Y', strtotime($checklist->criado_em)) : '-'; ?></td>
                                    <td class="button-tip">
                                        <a href="#" class="btn btn-mini btn-info" title="Visualizar">
                                            <i class="icon-eye-open icon-white"></i>
                                        </a>
                                        <a href="#" class="btn btn-mini btn-warning" title="Editar">
                                            <i class="icon-edit icon-white"></i>
                                        </a>
                                        <a href="#" class="btn btn-mini btn-danger" title="Excluir" onclick="return confirm('Confirma exclusão?')">
                                            <i class="icon-trash icon-white"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="icon-info-sign"></i> Nenhum template de checklist cadastrado.
                    </div>

                    <!-- Checklist padrão de exemplo -->
                    <div class="span6">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="icon-check"></i></span>
                                <h5>Checklist Padrão - Instalação CFTV</h5>
                            </div>
                            <div class="widget-content">
                                <ol>
                                    <li>Verificar integridade das câmeras</li>
                                    <li>Testar cabeamento e conexões</li>
                                    <li>Configurar gravação no DVR/NVR</li>
                                    <li>Ajustar ângulos de visão</li>
                                    <li>Testar acesso remoto</li>
                                    <li>Treinar cliente no uso do sistema</li>
                                    <li>Limpar local de instalação</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="span6">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="icon-check"></i></span>
                                <h5>Checklist Padrão - Manutenção</h5>
                            </div>
                            <div class="widget-content">
                                <ol>
                                    <li>Diagnóstico do problema relatado</li>
                                    <li>Verificar fontes de alimentação</li>
                                    <li>Testar equipamentos</li>
                                    <li>Substituir componentes defeituosos</li>
                                    <li>Realizar testes finais</li>
                                    <li>Preencher relatório de serviço</li>
                                    <li>Coletar assinatura do cliente</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
