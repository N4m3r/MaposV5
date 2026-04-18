<?php
/**
 * View: Editar Usuário do Portal do Cliente - Versão Intuitiva
 */
?>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('usuarioscliente') ?>">Usuários Cliente</a> <span class="divider">/</span></li>
            <li class="active">Editar: <?= htmlspecialchars($usuario->nome, ENT_QUOTES, 'UTF-8') ?></li>
        </ul>
    </div>
</div>

<!-- Cabeçalho com Resumo -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
            <div class="widget-content" style="padding: 20px; color: white;">
                <div class="row-fluid">
                    <div class="span8">
                        <h3 style="margin: 0; color: white;"><i class="bx bx-user-circle"></i> <?= htmlspecialchars($usuario->nome) ?></h3>
                        <p style="margin: 10px 0 0; opacity: 0.9;">
                            <i class="bx bx-envelope"></i> <?= $usuario->email ?> &nbsp;|&nbsp;
                            <i class="bx bx-phone"></i> <?= $usuario->telefone ?: 'N/A' ?> &nbsp;|&nbsp;
                            <span class="label <?= $usuario->ativo ? 'label-success' : 'label-important' ?>" style="background: <?= $usuario->ativo ? '#2ecc71' : '#e74c3c' ?>;">
                                <?= $usuario->ativo ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </p>
                    </div>
                    <div class="span4 text-right">
                        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; display: inline-block;">
                            <span style="font-size: 24px; font-weight: bold;"><?= count($cnpjs) ?></span><br>
                            <small>CNPJ(s) Vinculado(s)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="<?= current_url() ?>" method="post" class="form-horizontal" id="form-usuario">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

    <!-- Debug: Mostrar erros de validação -->
    <?php if (validation_errors()): ?>
    <div class="row-fluid" style="margin-top: 20px;">
        <div class="span12">
            <div class="alert alert-error">
                <strong>Erros de validação:</strong>
                <?= validation_errors() ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

<div class="row-fluid" style="margin-top: 20px;">
    <!-- Coluna Esquerda: Dados do Usuário -->
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title" style="background: #f8f9fa;">
                <span class="icon"><i class="bx bx-user-pin" style="color: #667eea;"></i></span>
                <h5>Dados do Usuário</h5>
            </div>
            <div class="widget-content">

                    <div class="control-group">
                        <label class="control-label">Nome Completo: <span class="required" style="color: #e74c3c;">*</span></label>
                        <div class="controls">
                            <input type="text" name="nome" class="span12" value="<?= set_value('nome', $usuario->nome) ?>" required />
                            <?= form_error('nome') ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Email: <span class="required" style="color: #e74c3c;">*</span></label>
                        <div class="controls">
                            <input type="email" name="email" class="span12" value="<?= set_value('email', $usuario->email) ?>" required />
                            <?= form_error('email') ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Telefone:</label>
                        <div class="controls">
                            <input type="text" name="telefone" class="span12" value="<?= set_value('telefone', $usuario->telefone) ?>" id="telefone" placeholder="(00) 00000-0000" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Nova Senha:</label>
                        <div class="controls">
                            <input type="password" name="senha" class="span12" minlength="6" />
                            <?= form_error('senha') ?>
                            <span class="help-inline" style="color: #666; font-size: 12px;">
                                <i class="bx bx-info-circle"></i> Deixe em branco para manter a senha atual
                            </span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Confirmar Senha:</label>
                        <div class="controls">
                            <input type="password" name="confirmar_senha" class="span12" />
                            <?= form_error('confirmar_senha') ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Cliente Vinculado:</label>
                        <div class="controls">
                            <select name="cliente_id" class="span12" id="select-cliente-principal">
                                <option value="">-- Nenhum --</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c->idClientes ?>"
                                            data-documento="<?= htmlspecialchars($c->documento ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-razao="<?= htmlspecialchars($c->nomeCliente ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            <?= set_select('cliente_id', $c->idClientes, $usuario->cliente_id == $c->idClientes) ?>>
                                        <?= htmlspecialchars($c->nomeCliente, ENT_QUOTES, 'UTF-8') ?>
                                        <?= $c->documento ? '(' . $c->documento . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-inline" style="color: #666; font-size: 12px;">
                                <i class="bx bx-link"></i> Vincula automaticamente todas as OS deste cliente
                            </span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Status:</label>
                        <div class="controls">
                            <label class="checkbox inline" style="padding-top: 5px;">
                                <input type="checkbox" name="ativo" value="1" <?= set_checkbox('ativo', '1', $usuario->ativo == 1) ?> />
                                <span style="color: <?= $usuario->ativo ? '#27ae60' : '#e74c3c' ?>;">
                                    <i class="bx <?= $usuario->ativo ? 'bx-check-circle' : 'bx-x-circle' ?>"></i>
                                    Usuário <?= $usuario->ativo ? 'ativo' : 'inativo' ?> - pode fazer login
                                </span>
                            </label>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Permissões -->
        <div class="widget-box" style="margin-top: 20px;">
            <div class="widget-title" style="background: #f8f9fa;">
                <span class="icon"><i class="bx bx-shield" style="color: #9b59b6;"></i></span>
                <h5>Permissões de Acesso</h5>
            </div>
            <div class="widget-content">
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php
                    $permissoesAgrupadas = [
                        'Visualização' => ['visualizar_os', 'visualizar_detalhes_os', 'visualizar_produtos_os', 'visualizar_servicos_os', 'visualizar_anexos_os', 'visualizar_documentos_fiscais'],
                        'Financeiro' => ['visualizar_financeiro', 'visualizar_historico_pagamentos'],
                        'Ações' => ['imprimir_os', 'editar_perfil', 'solicitar_orcamento', 'aprovar_os'],
                        'Notificações' => ['receber_notificacoes', 'acesso_mobile'],
                    ];

                    $labels = [
                        'visualizar_os' => 'Visualizar Ordens de Serviço',
                        'visualizar_detalhes_os' => 'Ver detalhes da OS',
                        'visualizar_produtos_os' => 'Ver produtos da OS',
                        'visualizar_servicos_os' => 'Ver serviços da OS',
                        'visualizar_anexos_os' => 'Ver anexos da OS',
                        'visualizar_documentos_fiscais' => 'Ver documentos fiscais',
                        'visualizar_financeiro' => 'Ver informações financeiras',
                        'visualizar_historico_pagamentos' => 'Ver histórico de pagamentos',
                        'imprimir_os' => 'Imprimir relatório da OS',
                        'editar_perfil' => 'Editar próprio perfil',
                        'solicitar_orcamento' => 'Solicitar novo orçamento',
                        'aprovar_os' => 'Aprovar/Reprovar OS',
                        'receber_notificacoes' => 'Receber notificações',
                        'acesso_mobile' => 'Acesso via dispositivos móveis',
                    ];

                    foreach ($permissoesAgrupadas as $grupo => $chaves):
                    ?>
                        <div style="margin-bottom: 15px;">
                            <h6 style="margin: 0 0 10px; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                <i class="bx bx-folder-open"></i> <?= $grupo ?>
                            </h6>
                            <?php foreach ($chaves as $chave):
                                if (!isset($permissoes_padrao[$chave])) continue;
                                // Obtém o valor atual da permissão (do banco ou padrão)
                                $valor_salvo = isset($permissoes[$chave]) ? $permissoes[$chave] : null;
                                // Se tem valor salvo no banco, usa ele. Senão, usa o padrão
                                $valor_atual = ($valor_salvo !== null) ? $valor_salvo : $permissoes_padrao[$chave];
                                // Converte para booleano para garantir que está correto
                                $valor_atual = ($valor_atual === true || $valor_atual === '1' || $valor_atual === 1);
                            ?>
                                <label class="checkbox" style="margin-left: 15px; margin-bottom: 8px;">
                                    <input type="checkbox" name="permissoes[<?= $chave ?>]" value="1" <= $valor_atual ? 'checked="checked"' : '' ?> />
                                    <?= $labels[$chave] ?? $chave ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna Direita: Gerenciamento de CNPJs -->
    <div class="span6">
        <div class="widget-box">
            <div class="widget-title" style="background: #f8f9fa;">
                <span class="icon"><i class="bx bx-buildings" style="color: #27ae60;"></i></span>
                <h5>CNPJs Vinculados</h5>
                <div class="buttons">
                    <span class="label label-info" id="contador-cnpjs"><?= count($cnpjs) ?> CNPJ(s)</span>
                </div>
            </div>
            <div class="widget-content">

                <!-- Alerta Informativo -->
                <div class="alert alert-info" style="background: #e8f4f8; border-color: #bee5eb; color: #0c5460;">
                    <i class="bx bx-info-circle" style="font-size: 20px; vertical-align: middle;"></i>
                    <strong>O usuário terá acesso às OS de todos os CNPJs vinculados.</strong>
                </div>

                <!-- Lista de CNPJs Vinculados -->
                <div id="cnpjs-lista" style="margin-bottom: 20px;">
                    <?php if (!empty($cnpjs)): ?>
                        <?php foreach ($cnpjs as $index => $cnpj): ?>
                        <div class="cnpj-card" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 10px; position: relative;">
                            <div class="row-fluid">
                                <div class="span9">
                                    <div style="font-weight: bold; color: #2c3e50; font-size: 16px;">
                                        <i class="bx bx-building"></i> <span class="cnpj-numero"><?= $cnpj->cnpj ?></span>
                                    </div>
                                    <div style="color: #666; margin-top: 5px; font-size: 13px;">
                                        <i class="bx bx-user"></i>
                                        <span class="cnpj-razao"><?= htmlspecialchars($cnpj->razao_social ?: 'Razão Social não informada', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <input type="hidden" name="cnpjs[]" value="<?= $cnpj->cnpj ?>">
                                    <input type="hidden" name="cnpjs_razao[]" value="<?= htmlspecialchars($cnpj->razao_social ?: '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                                <div class="span3 text-right">
                                    <button type="button" class="btn btn-small btn-danger btn-remover-cnpj-card" title="Remover CNPJ">
                                        <i class="bx bx-trash"></i> Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div id="sem-cnpjs" class="alert" style="background: #fff3cd; border-color: #ffeaa7; color: #856404;">
                            <i class="bx bx-error-circle"></i> Nenhum CNPJ vinculado. Adicione pelo menos um CNPJ para o usuário acessar as OS.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Área de Adicionar Novo CNPJ -->
                <div style="background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px; padding: 20px;">
                    <h6 style="margin: 0 0 15px; color: #495057;">
                        <i class="bx bx-plus-circle"></i> Adicionar Novo CNPJ
                    </h6>

                    <!-- Opção 1: Selecionar de Clientes Cadastrados -->
                    <div class="control-group" style="margin-bottom: 15px;">
                        <label style="font-weight: normal; color: #666;">
                            <i class="bx bx-search"></i> Buscar cliente cadastrado:
                        </label>
                        <div class="controls" style="margin-left: 0;">
                            <div class="input-append" style="width: 100%;">
                                <select id="select-cliente-cnpj" class="span10">
                                    <option value="">-- Selecione um cliente --</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <?php if (!empty($c->documento)): ?>
                                            <option value="<?= htmlspecialchars($c->documento, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-razao="<?= htmlspecialchars($c->nomeCliente, ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($c->nomeCliente, ENT_QUOTES, 'UTF-8') ?> - <?= $c->documento ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-info" id="btn-adicionar-cliente" style="border-radius: 0 4px 4px 0;">
                                    <i class="bx bx-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Opção 2: Digitar Manualmente -->
                    <div class="control-group" style="margin-bottom: 0;">
                        <label style="font-weight: normal; color: #666;">
                            <i class="bx bx-pencil"></i> Ou digite o CNPJ manualmente:
                        </label>
                        <div class="controls" style="margin-left: 0;">
                            <div class="input-append" style="width: 100%;">
                                <input type="text" id="input-cnpj-manual" class="span8" placeholder="00.000.000/0000-00" maxlength="18" />
                                <input type="text" id="input-razao-manual" class="span4" placeholder="Razão Social" />
                                <button type="button" class="btn btn-success" id="btn-adicionar-manual">
                                    <i class="bx bx-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="form-actions" style="margin-top: 20px; margin-bottom: 0; padding-bottom: 0;">
                    <button type="submit" class="btn btn-success btn-large">
                        <i class="bx bx-save"></i> Salvar Alterações
                    </button>
                    <a href="<?= site_url('usuarioscliente') ?>" class="btn btn-large">Cancelar</a>
                </div>
            </div>
        </div>

        <!-- Dicas -->
        <div class="widget-box" style="margin-top: 20px; background: #e8f5e9; border-color: #c8e6c9;">
            <div class="widget-content" style="padding: 15px;">
                <h6 style="margin: 0 0 10px; color: #2e7d32;">
                    <i class="bx bx-lightbulb"></i> Dicas
                </h6>
                <ul style="margin: 0; padding-left: 20px; color: #555; font-size: 13px;">
                    <li>O usuário verá apenas as OS dos CNPJs vinculados</li>
                    <li>Você pode adicionar múltiplos CNPJs para um mesmo usuário</li>
                    <li>Se o CNPJ não estiver cadastrado, digite-o manualmente</li>
                    <li>O CNPJ do cliente principal é adicionado automaticamente</li>
                </ul>
            </div>
        </div>
    </div>
</div>

</form> <!-- Fechamento do form -->

<script>
$(document).ready(function() {
    // Máscara para telefone
    if ($.fn.mask) {
        $('#telefone').mask('(00) 00000-0000');
    }

    // Contador de CNPJs
    function atualizarContador() {
        var total = $('.cnpj-card').length;
        $('#contador-cnpjs').text(total + ' CNPJ(s)');

        if (total === 0) {
            $('#sem-cnpjs').show();
        } else {
            $('#sem-cnpjs').hide();
        }
    }

    // Função para verificar CNPJ duplicado
    function cnpjJaExiste(cnpj) {
        var cnpjLimpo = cnpj.replace(/\D/g, '');
        var existe = false;

        $('.cnpj-card').each(function() {
            var cnpjExistente = $(this).find('.cnpj-numero').text().replace(/\D/g, '');
            if (cnpjExistente === cnpjLimpo) {
                existe = true;
                return false;
            }
        });

        return existe;
    }

    // Função para formatar CNPJ
    function formatarCNPJ(cnpj) {
        var cnpjLimpo = cnpj.replace(/\D/g, '');
        if (cnpjLimpo.length !== 14) return cnpj;
        return cnpjLimpo.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }

    // Função para criar card de CNPJ
    function criarCardCNPJ(cnpj, razaoSocial) {
        var cnpjFormatado = formatarCNPJ(cnpj);

        if (cnpjJaExiste(cnpj)) {
            Swal.fire({
                icon: 'warning',
                title: 'CNPJ já vinculado',
                text: 'Este CNPJ já está na lista de CNPJs vinculados.',
                confirmButtonColor: '#667eea'
            });
            return false;
        }

        var cardHtml = `
            <div class="cnpj-card" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 10px; position: relative; animation: slideIn 0.3s ease;">
                <div class="row-fluid">
                    <div class="span9">
                        <div style="font-weight: bold; color: #2c3e50; font-size: 16px;">
                            <i class="bx bx-building"></i> <span class="cnpj-numero">${cnpjFormatado}</span>
                        </div>
                        <div style="color: #666; margin-top: 5px; font-size: 13px;">
                            <i class="bx bx-user"></i>
                            <span class="cnpj-razao">${razaoSocial || 'Razão Social não informada'}</span>
                        </div>
                        <input type="hidden" name="cnpjs[]" value="${cnpjFormatado}">
                        <input type="hidden" name="cnpjs_razao[]" value="${razaoSocial || ''}">
                    </div>
                    <div class="span3 text-right">
                        <button type="button" class="btn btn-small btn-danger btn-remover-cnpj-card" title="Remover CNPJ">
                            <i class="bx bx-trash"></i> Remover
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#cnpjs-lista').append(cardHtml);
        atualizarContador();

        // Feedback visual
        var novoCard = $('#cnpjs-lista .cnpj-card').last();
        novoCard.hide().fadeIn(300);

        return true;
    }

    // Adicionar CNPJ de cliente selecionado
    $('#btn-adicionar-cliente').click(function() {
        var select = $('#select-cliente-cnpj');
        var selectedOption = select.find('option:selected');

        if (!select.val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Selecione um cliente',
                text: 'Por favor, selecione um cliente da lista.',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        var cnpj = selectedOption.val();
        var razao = selectedOption.data('razao');

        if (criarCardCNPJ(cnpj, razao)) {
            select.val('').trigger('change');

            Swal.fire({
                icon: 'success',
                title: 'CNPJ Adicionado',
                text: 'O CNPJ foi vinculado com sucesso!',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });

    // Adicionar CNPJ manual
    $('#btn-adicionar-manual').click(function() {
        var cnpjInput = $('#input-cnpj-manual');
        var razaoInput = $('#input-razao-manual');
        var cnpj = cnpjInput.val().trim();
        var razao = razaoInput.val().trim();

        if (!cnpj) {
            Swal.fire({
                icon: 'warning',
                title: 'CNPJ obrigatório',
                text: 'Por favor, digite o número do CNPJ.',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        var cnpjLimpo = cnpj.replace(/\D/g, '');
        if (cnpjLimpo.length !== 14) {
            Swal.fire({
                icon: 'error',
                title: 'CNPJ inválido',
                text: 'O CNPJ deve ter 14 dígitos.',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        if (criarCardCNPJ(cnpj, razao)) {
            cnpjInput.val('');
            razaoInput.val('');

            Swal.fire({
                icon: 'success',
                title: 'CNPJ Adicionado',
                text: 'O CNPJ foi vinculado com sucesso!',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });

    // Remover CNPJ
    $(document).on('click', '.btn-remover-cnpj-card', function() {
        var card = $(this).closest('.cnpj-card');

        Swal.fire({
            title: 'Remover CNPJ?',
            text: 'Deseja realmente remover este CNPJ vinculado?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                card.fadeOut(300, function() {
                    card.remove();
                    atualizarContador();
                });
            }
        });
    });

    // Máscara para CNPJ manual
    $('#input-cnpj-manual').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length <= 14) {
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            $(this).val(value);
        }
    });

    // Adicionar CNPJ automático ao selecionar cliente principal
    $('#select-cliente-principal').change(function() {
        var clienteId = $(this).val();
        if (clienteId) {
            var option = $(this).find('option:selected');
            var documento = option.data('documento');
            var razao = option.data('razao');

            if (documento && !cnpjJaExiste(documento)) {
                Swal.fire({
                    title: 'Adicionar CNPJ do cliente?',
                    text: 'Deseja vincular o CNPJ ' + documento + ' a este usuário?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#27ae60',
                    cancelButtonColor: '#95a5a6',
                    confirmButtonText: 'Sim, adicionar',
                    cancelButtonText: 'Não'
                }).then((result) => {
                    if (result.isConfirmed) {
                        criarCardCNPJ(documento, razao);
                    }
                });
            }
        }
    });

    // CSS para animação
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `)
        .appendTo('head');

    // Inicializar contador
    atualizarContador();
});
</script>
