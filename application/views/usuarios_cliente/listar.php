<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-user-check"></i></span>
                <h5>Usuários do Portal do Cliente</h5>
                <div class="buttons">
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuariosCliente')): ?>
                        <a href="<?= site_url('usuarioscliente/adicionar') ?>" class="btn btn-success btn-mini">
                            <i class="bx bx-plus"></i> Novo Usuário
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="widget-content nopadding">
                <?php if (count($usuarios) > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Cliente Vinculado</th>
                                <th>CNPJs</th>
                                <th>Status</th>
                                <th>Último Acesso</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= $u->id ?></td>
                                    <td><?= htmlspecialchars($u->nome) ?></td>
                                    <td><?= htmlspecialchars($u->email) ?></td>
                                    <td><?= $u->telefone ? htmlspecialchars($u->telefone) : '-' ?></td>
                                    <td><?= $u->cliente_nome ? htmlspecialchars($u->cliente_nome) : '-' ?></td>
                                    <td>
                                        <?= isset($u->total_cnpjs) && $u->total_cnpjs > 0 ? $u->total_cnpjs . ' CNPJ(s)' : '-' ?>
                                    </td>
                                    <td>
                                        <?php if ($u->ativo): ?>
                                            <span class="label label-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="label label-important">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $u->ultimo_acesso ? date('d/m/Y H:i', strtotime($u->ultimo_acesso)) : 'Nunca' ?></td>
                                    <td class="text-center">
                                        <a href="<?= site_url('usuarioscliente/visualizar/' . $u->id) ?>" class="btn btn-mini btn-info" title="Visualizar">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eUsuariosCliente')): ?>
                                            <a href="<?= site_url('usuarioscliente/editar/' . $u->id) ?>" class="btn btn-mini btn-warning" title="Editar">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dUsuariosCliente')): ?>
                                            <a href="<?= site_url('usuarioscliente/excluir/' . $u->id) ?>" class="btn btn-mini btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="bx bx-info-circle"></i> Nenhum usuário do portal do cliente cadastrado.
                        <br><br>
                        <a href="<?= site_url('usuarioscliente/adicionar') ?>" class="btn btn-success">
                            <i class="bx bx-plus"></i> Cadastrar Primeiro Usuário
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
