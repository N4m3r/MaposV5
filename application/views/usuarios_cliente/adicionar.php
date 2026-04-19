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
                        <label class="control-label">Cliente / Fornecedor Vinculado:</label>
                        <div class="controls">
                            <select name="cliente_id" class="span6">
                                <option value="">-- Selecione (opcional) --</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c->idClientes ?>" <?= set_select('cliente_id', $c->idClientes) ?>>
                                        <?= htmlspecialchars($c->nomeCliente) ?> <?= $c->documento ? '(' . $c->documento . ')' : '' ?> <?= isset($c->fornecedor) && $c->fornecedor ? '[Fornecedor]' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-inline">Vincula automaticamente as OS deste cliente/fornecedor</span>
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

                    <!-- Buscar Cliente Cadastrado -->
                    <div class="control-group">
                        <label class="control-label">Buscar Cliente / Fornecedor Cadastrado:</label>
                        <div class="controls">
                            <div class="input-append">
                                <select id="buscar-cliente-cnpj" class="span6">
                                    <option value="">-- Selecione para preencher CNPJ --</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <?php if (!empty($c->documento)): ?>
                                            <option value="<?= htmlspecialchars($c->documento) ?>" data-razao="<?= htmlspecialchars($c->nomeCliente) ?>" data-id="<?= $c->idClientes ?>">
                                                <?= htmlspecialchars($c->nomeCliente) ?> - <?= $c->documento ?> <?= isset($c->fornecedor) && $c->fornecedor ? '[Fornecedor]' : '' ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-info" id="btn-buscar-cliente" title="Adicionar CNPJ do cliente selecionado">
                                    <i class="bx bx-plus"></i> Adicionar CNPJ
                                </button>
                            </div>
                            <span class="help-inline">Selecione um cliente cadastrado para preencher automaticamente o CNPJ</span>
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
                                        <button type="button" class="btn btn-info btn-consultar-cnpj" title="Consultar na ReceitaWS">
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
                                <i class="bx bx-plus"></i> Adicionar CNPJ Manual
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
                                <?php
                                // Agrupar permissões
                                $permissoesAgrupadasAdd = [
                                    'Visualização de OS' => ['visualizar_os', 'visualizar_detalhes_os', 'visualizar_produtos_os', 'visualizar_servicos_os', 'visualizar_anexos_os', 'visualizar_documentos_fiscais'],
                                    'Financeiro' => ['visualizar_financeiro', 'visualizar_historico_pagamentos', 'visualizar_cobrancas', 'visualizar_boletos', 'visualizar_notas_fiscais'],
                                    'Obras' => ['visualizar_obras', 'visualizar_detalhes_obra'],
                                    'Ações' => ['imprimir_os', 'editar_perfil', 'solicitar_orcamento', 'aprovar_os', 'visualizar_compras'],
                                    'Notificações' => ['receber_notificacoes', 'acesso_mobile'],
                                ];

                                $labelsAdd = [
                                    'visualizar_os' => 'Visualizar Ordens de Serviço',
                                    'visualizar_os_apenas_vinculadas' => 'Apenas OS vinculadas aos seus CNPJs',
                                    'visualizar_detalhes_os' => 'Ver detalhes da OS',
                                    'visualizar_produtos_os' => 'Ver produtos da OS',
                                    'visualizar_servicos_os' => 'Ver serviços da OS',
                                    'visualizar_anexos_os' => 'Ver anexos da OS',
                                    'visualizar_documentos_fiscais' => 'Ver documentos fiscais',
                                    'visualizar_financeiro' => 'Ver informações financeiras',
                                    'visualizar_historico_pagamentos' => 'Ver histórico de pagamentos',
                                    'visualizar_cobrancas' => 'Visualizar Cobranças',
                                    'visualizar_boletos' => 'Visualizar Boletos',
                                    'visualizar_notas_fiscais' => 'Visualizar Notas Fiscais (NFS-e)',
                                    'visualizar_obras' => 'Visualizar Obras',
                                    'visualizar_detalhes_obra' => 'Ver detalhes da Obra',
                                    'visualizar_compras' => 'Visualizar Compras',
                                    'imprimir_os' => 'Imprimir relatório da OS',
                                    'editar_perfil' => 'Editar próprio perfil',
                                    'solicitar_orcamento' => 'Solicitar novo orçamento',
                                    'aprovar_os' => 'Aprovar/Reprovar OS',
                                    'receber_notificacoes' => 'Receber notificações por email',
                                    'acesso_mobile' => 'Acesso via dispositivos móveis',
                                ];

                                foreach ($permissoesAgrupadasAdd as $grupo => $chaves): ?>
                                    <h6 style="margin: 15px 0 10px; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                        <i class="bx bx-folder-open"></i> <?= $grupo ?>
                                    </h6>
                                    <?php foreach ($chaves as $chave):
                                        // Pula se não existe no array de permissões padrão
                                        if (!isset($permissoes_padrao[$chave])) continue;
                                        $valor_padrao = $permissoes_padrao[$chave];
                                        $label = $labelsAdd[$chave] ?? $chave;
                                    ?>
                                        <label class="checkbox" style="margin-left: 15px;">
                                            <input type="checkbox" name="permissoes[<?= $chave ?>]" value="1" <?= set_checkbox('permissoes[' . $chave . ']', '1', $valor_padrao) ?> />
                                            <?= $label ?>
                                        </label>
                                    <?php endforeach; ?>
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

    // Buscar cliente cadastrado e adicionar CNPJ
    $('#btn-buscar-cliente').click(function() {
        var select = $('#buscar-cliente-cnpj');
        var selectedOption = select.find('option:selected');

        if (!select.val()) {
            alert('Selecione um cliente primeiro');
            return;
        }

        var cnpj = selectedOption.val();
        var razaoSocial = selectedOption.data('razao');
        var clienteId = selectedOption.data('id');

        // Verificar se CNPJ já não está adicionado
        var cnpjExistente = false;
        $('.cnpj-input').each(function() {
            if ($(this).val().replace(/\D/g, '') === cnpj.replace(/\D/g, '')) {
                cnpjExistente = true;
                return false;
            }
        });

        if (cnpjExistente) {
            alert('Este CNPJ já foi adicionado!');
            return;
        }

        // Formatar CNPJ
        var cnpjFormatado = cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');

        // Verificar se existe uma linha vazia
        var linhaVazia = null;
        $('.cnpj-row').each(function() {
            if ($(this).find('.cnpj-input').val() === '') {
                linhaVazia = $(this);
                return false;
            }
        });

        if (linhaVazia) {
            // Usar linha existente
            linhaVazia.find('.cnpj-input').val(cnpjFormatado);
            linhaVazia.find('.cnpj-razao').val(razaoSocial);
        } else {
            // Criar nova linha
            var newRow = `
                <div class="cnpj-row" style="margin-bottom: 10px;">
                    <div class="input-append">
                        <input type="text" name="cnpjs[]" class="span4 cnpj-input" placeholder="00.000.000/0000-00" maxlength="18" value="${cnpjFormatado}" />
                        <button type="button" class="btn btn-info btn-consultar-cnpj" title="Consultar na ReceitaWS">
                            <i class="bx bx-search"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-remover-cnpj" title="Remover">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                    <input type="text" name="cnpjs_razao[]" class="span6 cnpj-razao" placeholder="Razão Social (preenchido automaticamente)" readonly style="margin-top: 5px;" value="${razaoSocial}" />
                </div>
            `;
            $('#cnpjs-container').append(newRow);
        }

        updateRemoveButtons();

        // Limpar seleção
        select.val('');

        // Feedback visual
        alert('CNPJ adicionado com sucesso!\n\nCliente: ' + razaoSocial + '\nCNPJ: ' + cnpjFormatado);
    });

    // Ao selecionar cliente no dropdown principal, sugerir vincular
    $('select[name="cliente_id"]').change(function() {
        var clienteId = $(this).val();
        if (clienteId) {
            // Buscar o CNPJ do cliente selecionado
            var clienteOption = $(this).find('option:selected');
            var texto = clienteOption.text();
            var match = texto.match(/\(([^)]+)\)/);

            if (match && confirm('Deseja adicionar o CNPJ deste cliente aos CNPJs vinculados?')) {
                // Preencher o buscar-cliente-cnpj e clicar em adicionar
                var cnpj = match[1];
                $('#buscar-cliente-cnpj option').each(function() {
                    if ($(this).val() === cnpj) {
                        $(this).prop('selected', true);
                        $('#btn-buscar-cliente').click();
                        return false;
                    }
                });
            }
        }
    });
});
</script>
