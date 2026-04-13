<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('usuarioscliente') ?>">Usuários Cliente</a> <span class="divider">/</span></li>
            <li class="active">Novo Usuário</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-user-plus"></i></span>
                <h5>Novo Usuário do Portal do Cliente</h5>
            </div>

            <div class="widget-content nopadding">
                <form action="<?= current_url() ?>" method="post" class="form-horizontal">

                    <!-- Dados Básicos -->
                    <div class="control-group">
                        <label class="control-label">Nome Completo: <span class="required">*</span></label>
                        <div class="controls">
                            <input type="text" name="nome" class="span6" value="<?= set_value('nome') ?>" required />
                            <?= form_error('nome') ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Email: <span class="required">*</span></label>
                        <div class="controls">
                            <input type="email" name="email" class="span6" value="<?= set_value('email') ?>" required />
                            <?= form_error('email') ?>
                            <span class="help-inline">Será usado para login</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Telefone:</label>
                        <div class="controls">
                            <input type="text" name="telefone" class="span4" value="<?= set_value('telefone') ?>" id="telefone" placeholder="(00) 00000-0000" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Senha: <span class="required">*</span></label>
                        <div class="controls">
                            <input type="password" name="senha" class="span4" required minlength="6" />
                            <?= form_error('senha') ?>
                            <span class="help-inline">Mínimo 6 caracteres</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Confirmar Senha: <span class="required">*</span></label>
                        <div class="controls">
                            <input type="password" name="confirmar_senha" class="span4" required />
                            <?= form_error('confirmar_senha') ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Cliente Vinculado:</label>
                        <div class="controls">
                            <select name="cliente_id" class="span6">
                                <option value="">-- Selecione (opcional) --</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c->idClientes ?>" <?= set_select('cliente_id', $c->idClientes) ?>>
                                        <?= htmlspecialchars($c->nomeCliente) ?> <?= $c->documento ? '(' . $c->documento . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-inline">Vincula automaticamente as OS deste cliente</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Ativo:</label>
                        <div class="controls">
                            <label class="checkbox inline">
                                <input type="checkbox" name="ativo" value="1" <?= set_checkbox('ativo', '1', true) ?> />
                                Usuário ativo pode fazer login
                            </label>
                        </div>
                    </div>

                    <hr>

                    <!-- CNPJs Vinculados -->
                    <div class="control-group">
                        <label class="control-label">CNPJs Vinculados:</label>
                        <div class="controls">
                            <div id="cnpjs-container">
                                <div class="cnpj-row" style="margin-bottom: 10px;">
                                    <div class="input-append">
                                        <input type="text" name="cnpjs[]" class="span4 cnpj-input" placeholder="00.000.000/0000-00" maxlength="18" />
                                        <button type="button" class="btn btn-info btn-consultar-cnpj" title="Consultar">
                                            <i class="bx bx-search"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-remover-cnpj" title="Remover" style="display: none;">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                    <input type="text" name="cnpjs_razao[]" class="span6 cnpj-razao" placeholder="Razão Social (preenchido automaticamente)" readonly style="margin-top: 5px;" />
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-mini" id="btn-adicionar-cnpj">
                                <i class="bx bx-plus"></i> Adicionar CNPJ
                            </button>
                            <span class="help-inline">O usuário terá acesso às OS de todos os CNPJs vinculados</span>
                        </div>
                    </div>

                    <hr>

                    <!-- Permissões -->
                    <div class="control-group">
                        <label class="control-label">Permissões:</label>
                        <div class="controls">
                            <div class="span8" style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd;">
                                <h5><i class="bx bx-shield"></i> Configurações de Acesso</h5>
                                <?php foreach ($permissoes_padrao as $chave => $valor_padrao): ?>
                                    <?php
                                    $label = '';
                                    switch ($chave) {
                                        case 'visualizar_os': $label = 'Visualizar Ordens de Serviço'; break;
                                        case 'visualizar_os_apenas_vinculadas': $label = 'Apenas OS vinculadas aos seus CNPJs (se desmarcado, vê todas)'; break;
                                        case 'visualizar_detalhes_os': $label = 'Ver detalhes da OS'; break;
                                        case 'visualizar_produtos_os': $label = 'Ver produtos da OS'; break;
                                        case 'visualizar_servicos_os': $label = 'Ver serviços da OS'; break;
                                        case 'visualizar_anexos_os': $label = 'Ver anexos da OS'; break;
                                        case 'visualizar_documentos_fiscais': $label = 'Ver documentos fiscais (boletos, NFS-e)'; break;
                                        case 'visualizar_financeiro': $label = 'Ver informações financeiras'; break;
                                        case 'visualizar_historico_pagamentos': $label = 'Ver histórico de pagamentos'; break;
                                        case 'imprimir_os': $label = 'Imprimir relatório da OS'; break;
                                        case 'editar_perfil': $label = 'Editar próprio perfil'; break;
                                        case 'solicitar_orcamento': $label = 'Solicitar novo orçamento'; break;
                                        case 'aprovar_os': $label = 'Aprovar/Reprovar OS'; break;
                                        case 'receber_notificacoes': $label = 'Receber notificações por email'; break;
                                        case 'acesso_mobile': $label = 'Acesso via dispositivos móveis'; break;
                                        default: $label = $chave;
                                    }
                                    ?>
                                    <label class="checkbox">
                                        <input type="checkbox" name="permissoes[<?= $chave ?>]" value="1" <?= set_checkbox('permissoes[' . $chave . ']', '1', $valor_padrao) ?> />
                                        <?= $label ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-save"></i> Salvar Usuário
                        </button>
                        <a href="<?= site_url('usuarioscliente') ?>" class="btn">Cancelar</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Máscara para telefone
    $('#telefone').mask('(00) 00000-0000');

    // Máscara para CNPJ
    $(document).on('input', '.cnpj-input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length <= 14) {
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            $(this).val(value);
        }
    });

    // Adicionar novo CNPJ
    $('#btn-adicionar-cnpj').click(function() {
        var newRow = `
            <div class="cnpj-row" style="margin-bottom: 10px;">
                <div class="input-append">
                    <input type="text" name="cnpjs[]" class="span4 cnpj-input" placeholder="00.000.000/0000-00" maxlength="18" />
                    <button type="button" class="btn btn-info btn-consultar-cnpj" title="Consultar">
                        <i class="bx bx-search"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-remover-cnpj" title="Remover">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
                <input type="text" name="cnpjs_razao[]" class="span6 cnpj-razao" placeholder="Razão Social (preenchido automaticamente)" readonly style="margin-top: 5px;" />
            </div>
        `;
        $('#cnpjs-container').append(newRow);
        updateRemoveButtons();
    });

    // Remover CNPJ
    $(document).on('click', '.btn-remover-cnpj', function() {
        $(this).closest('.cnpj-row').remove();
        updateRemoveButtons();
    });

    // Atualizar visibilidade dos botões de remover
    function updateRemoveButtons() {
        var rows = $('.cnpj-row');
        if (rows.length <= 1) {
            rows.find('.btn-remover-cnpj').hide();
        } else {
            rows.find('.btn-remover-cnpj').show();
        }
    }
    updateRemoveButtons();

    // Consultar CNPJ na ReceitaWS
    $(document).on('click', '.btn-consultar-cnpj', function() {
        var row = $(this).closest('.cnpj-row');
        var cnpjInput = row.find('.cnpj-input');
        var razaoInput = row.find('.cnpj-razao');
        var cnpj = cnpjInput.val().replace(/\D/g, '');

        if (cnpj.length !== 14) {
            alert('CNPJ inválido');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).find('i').removeClass('bx-search').addClass('bx-loader bx-spin');

        $.ajax({
            url: '<?= site_url("usuarioscliente/api_consultar_cnpj") ?>',
            type: 'GET',
            data: { cnpj: cnpj },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    razaoInput.val(response.data.nome);
                } else {
                    alert(response.error || 'Erro ao consultar CNPJ');
                }
            },
            error: function() {
                alert('Erro na consulta. Tente novamente.');
            },
            complete: function() {
                btn.prop('disabled', false).find('i').removeClass('bx-loader bx-spin').addClass('bx-search');
            }
        });
    });
});
</script>
