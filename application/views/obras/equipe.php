<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid">
    <div class="span12">
        <!-- Cabeçalho -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-group"></i></span>
                <h5>Equipe da Obra: <?php echo $obra->nome; ?></h5>
                <div class="buttons">
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="btn btn-mini btn-default">
                        <i class="icon-arrow-left"></i> Voltar para Obra
                    </a>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                    <button class="btn btn-mini btn-success" data-toggle="modal" data-target="#modalAdicionar">
                        <i class="icon-plus icon-white"></i> Adicionar Técnico
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="widget-content">
                <div class="row-fluid">
                    <!-- Informações da Obra -->
                    <div class="span6">
                        <h4><i class="icon-building"></i> <?php echo $obra->nome; ?></h4>
                        <p><strong>Cliente:</strong> <?php echo $obra->cliente_nome; ?></p>
                        <p><strong>Status:</strong>
                            <span class="label label-<?php
                                echo $obra->status == 'Concluida' ? 'success' :
                                    ($obra->status == 'Em Andamento' ? 'info' :
                                        ($obra->status == 'Paralisada' ? 'warning' : 'default'));
                            ?>">
                                <?php echo $obra->status; ?>
                            </span>
                        </p>
                    </div>
                    <!-- Estatísticas da Equipe -->
                    <div class="span6 text-right">
                        <div class="row-fluid">
                            <div class="span6">
                                <div style="padding: 15px; background: #f5f5f5; border-radius: 5px;">
                                    <h2 style="margin: 0; color: #28a745;"><?php echo count($equipe); ?></h2>
                                    <small>Técnicos na Equipe</small>
                                </div>
                            </div>
                            <div class="span6">
                                <div style="padding: 15px; background: #f5f5f5; border-radius: 5px;">
                                    <h2 style="margin: 0; color: #17a2b8;"><?php echo count($tecnicos); ?></h2>
                                    <small>Técnicos Disponíveis</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista da Equipe -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-group"></i></span>
                <h5>Técnicos Alocados</h5>
            </div>

            <div class="widget-content nopadding">
                <?php if (count($equipe) > 0): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Técnico</th>
                            <th>Função</th>
                            <th>Data de Entrada</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipe as $i => $membro): ?
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <strong><?php echo $membro->tecnico_nome; ?></strong>
                                <?php if ($membro->nivel_tecnico): ?>
                                    <br><small class="text-muted">Nível: <?php echo $membro->nivel_tecnico; ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="label label-info"><?php echo $membro->funcao; ?></span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($membro->data_entrada)); ?></td>
                            <td>
                                <span class="label label-<?php echo $membro->ativo ? 'success' : 'default'; ?>">
                                    <?php echo $membro->ativo ? 'Ativo' : 'Inativo'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?
003e
                                <a href="<?php echo site_url('obras/removerTecnico/' . $membro->id); ?>"
                                   class="btn btn-mini btn-danger"
                                   onclick="return confirm('Tem certeza que deseja remover este técnico da equipe?')"
                                   title="Remover da Equipe">
                                    <i class="icon-remove icon-white"></i> Remover
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?
003e
                <div class="alert alert-info" style="margin: 20px;">
                    <i class="icon-info-sign"></i>
                    <strong>Nenhum técnico alocado!</strong><br>
                    Clique no botão "Adicionar Técnico" para alocar técnicos a esta obra.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Guia Rápido -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-question-sign"></i></span>
                <h5>Como Gerenciar a Equipe</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span4 text-center">
                        <div style="padding: 20px;">
                            <i class="icon-plus-sign" style="font-size: 36px; color: #28a745;"></i>
                            <h4>1. Adicionar</h4>
                            <p>Clique em "Adicionar Técnico" e selecione o profissional e sua função na obra.</p>
                        </div>
                    </div>
                    <div class="span4 text-center">
                        <div style="padding: 20px;">
                            <i class="icon-tasks" style="font-size: 36px; color: #17a2b8;"></i>
                            <h4>2. Gerenciar</h4>
                            <p>Acompanhe todos os técnicos alocados e suas funções na tabela acima.</p>
                        </div>
                    </div>
                    <div class="span4 text-center">
                        <div style="padding: 20px;">
                            <i class="icon-remove-sign" style="font-size: 36px; color: #dc3545;"></i>
                            <h4>3. Remover</h4>
                            <p>Para remover um técnico, clique no botão "Remover" ao lado do nome.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Técnico -->
<div id="modalAdicionar" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Adicionar Técnico à Equipe</h3>
    </div>
    <form action="<?php echo site_url('obras/adicionarTecnico'); ?>" method="post">
        <div class="modal-body">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">

            <div class="control-group">
                <label>Técnico *</label>
                <select name="tecnico_id" class="span12" required>
                    <option value="">Selecione um técnico...</option>
                    <?php foreach ($tecnicos as $t): ?>
                        <?php
                        // Verificar se técnico já está na equipe
                        $ja_na_equipe = false;
                        foreach ($equipe as $membro) {
                            if ($membro->tecnico_id == $t->idUsuarios) {
                                $ja_na_equipe = true;
                                break;
                            }
                        }
                        ?>
                        <?php if (!$ja_na_equipe): ?>
                        <option value="<?php echo $t->idUsuarios; ?>">
                            <?php echo $t->nome; ?>
                            <?php if ($t->nivel_tecnico): ?> - <?php echo $t->nivel_tecnico; ?><?php endif; ?>
                        </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="control-group">
                <label>Função *</label>
                <select name="funcao" class="span12" required>
                    <option value="Técnico">Técnico</option>
                    <option value="Encarregado">Encarregado</option>
                    <option value="Engenheiro">Engenheiro</option>
                    <option value="Mestre de Obras">Mestre de Obras</option>
                    <option value="Pedreiro">Pedreiro</option>
                    <option value="Eletricista">Eletricista</option>
                    <option value="Hidráulico">Hidráulico</option>
                    <option value="Carpinteiro">Carpinteiro</option>
                    <option value="Pintor">Pintor</option>
                    <option value="Auxiliar">Auxiliar</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>

            <div class="alert alert-info">
                <i class="icon-info-sign"></i>
                Os técnicos adicionados à equipe poderão registrar atividades e check-ins nesta obra.
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">
                <i class="icon-plus icon-white"></i> Adicionar à Equipe
            </button>
        </div>
    </form>
</div>
