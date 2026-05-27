<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class='bx bx-plus'></i> Novo Template</h5>
                <a href="<?= site_url('notificacoes/templates') ?>" class="btn btn-sm btn-secondary float-end"><i class='bx bx-arrow-back'></i> Voltar</a>
            </div>
            <div class="card-body">
                <form method="post" action="<?= current_url() ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Chave *</label>
                                <input type="text" name="chave" class="form-control" required pattern="[a-z0-9_]+" placeholder="ex: os_criada">
                                <small class="text-muted">Apenas letras minusculas, numeros e underline</small>
                            </div>
                            <div class="form-group">
                                <label>Nome *</label>
                                <input type="text" name="nome" class="form-control" required placeholder="Nome do template">
                            </div>
                            <div class="form-group">
                                <label>Descricao</label>
                                <textarea name="descricao" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Categoria</label>
                                <select name="categoria" class="form-control">
                                    <?php foreach ($categorias as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Canal</label>
                                <select name="canal" class="form-control">
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="email">E-mail</option>
                                    <option value="sms">SMS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assunto (email)</label>
                                <input type="text" name="assunto" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Mensagem *</label>
                                <textarea name="mensagem" class="form-control" rows="8" required placeholder="Use {variavel} para placeholders"></textarea>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="ativo" value="1" checked>
                                    Ativo
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="e_marketing" value="1">
                                    Marketing
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($variaveis_globais)): ?>
                    <div class="mt-3">
                        <h6>Variaveis disponiveis:</h6>
                        <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($variaveis_globais as $key => $desc): ?>
                            <span class="badge bg-info" title="<?= e($desc) ?>">{<?= $key ?>}</span>
                        <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Criar Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>