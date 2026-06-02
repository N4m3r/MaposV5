<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<div class="form-unified">
    <!-- Header -->
    <div class="form-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1><?= svg_icon('building', 28, 28) ?> <?php echo isset($result) ? 'Editar' : 'Nova'; ?> Obra</h1>
                <p><?= svg_icon('chevron-left', 16, 16) ?> <a href="<?php echo site_url('obras'); ?>" style="color: white;">Voltar para lista</a></p>
            </div>
            <?php if (isset($result)): ?>
            <div style="text-align: right;">
                <div style="font-size: 14px; opacity: 0.8;">Código</div>
                <div style="font-size: 24px; font-weight: 700;">#<?php echo $result->id; ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

        <form method="post" action="<?php echo isset($result) ? site_url('obras/editar/' . $result->id) : site_url('obras/adicionar'); ?>" id="formObra">

            <!-- Dados Básicos -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon"><?= svg_icon('file-text', 22, 22) ?></div>
                    <div class="form-card-title">Dados Básicos</div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Nome da Obra <span class="required">*</span></label>
                        <input type="text" name="nome" class="form-input" required
                               value="<?php echo isset($result) ? $result->nome : ''; ?>"
                               placeholder="Digite o nome da obra...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cliente <span class="required">*</span></label>
                        <select name="cliente_id" id="cliente_select" class="form-select" required <?php echo isset($result) ? 'disabled' : ''; ?>
                            style="<?php echo isset($result) ? 'background: #f5f5f5;' : ''; ?>">
                            <option value="" disabled <?php echo (!isset($result) || empty($result->cliente_id)) ? 'selected' : ''; ?>>Selecione o cliente...</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?php echo $c->idClientes; ?>"
                                    data-documento="<?php echo $c->documento ?? ''; ?>"
                                    data-endereco="<?php echo $c->rua ?? ''; ?>"
                                    data-numero="<?php echo $c->numero ?? ''; ?>"
                                    data-bairro="<?php echo $c->bairro ?? ''; ?>"
                                    data-cidade="<?php echo $c->cidade ?? ''; ?>"
                                    data-estado="<?php echo $c->estado ?? ''; ?>"
                                    data-cep="<?php echo $c->cep ?? ''; ?>"
                                    <?php echo (isset($result) && $result->cliente_id == $c->idClientes) ? 'selected' : ''; ?>
                                    ><?php echo $c->nomeCliente; ?> <?php echo !empty($c->documento) ? '(' . $c->documento . ')' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($result)): ?>
                        <input type="hidden" name="cliente_id" value="<?php echo $result->cliente_id; ?>">
                        <?php endif; ?>
                        <div id="cliente_info" style="margin-top: 10px; font-size: 13px; color: #667eea; display: none;">
                            <?= svg_icon('info-circle', 14, 14) ?> <span id="cliente_doc"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo de Obra</label>
                        <select name="tipo_obra" class="form-select">
                            <option value="" disabled <?php echo (!isset($result) || empty($result->tipo_obra)) ? 'selected' : ''; ?>>Selecione o tipo...</option>
                            <?php foreach ($tipos_obra as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo->nome); ?>"
                                    <?php echo (isset($result) && $result->tipo_obra == $tipo->nome) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo->nome); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Valor do Contrato (R$)</label>
                        <input type="text" name="valor_contrato" class="form-input money"
                               value="<?php echo isset($result) ? number_format($result->valor_contrato ?? 0, 2, ',', '.') : ''; ?>"
                               placeholder="0,00">
                    </div>
                </div>
            </div>

            <!-- Localização -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d);"><?= svg_icon('map', 22, 22) ?></div>
                    <div class="form-card-title">Localização</div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="endereco" id="endereco" class="form-input"
                               value="<?php echo isset($result) ? $result->endereco : ''; ?>"
                               placeholder="Rua, número, complemento...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bairro</label>
                        <input type="text" name="bairro" id="bairro" class="form-input"
                               value="<?php echo isset($result) ? $result->bairro : ''; ?>"
                               placeholder="Bairro">
                    </div>

                    <div class="form-row" style="display: contents;">
                        <div class="form-group">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="cidade" id="cidade" class="form-input"
                                   value="<?php echo isset($result) ? $result->cidade : ''; ?>"
                                   placeholder="Cidade">
                        </div>

                        <div class="form-group">
                            <label class="form-label">UF</label>
                            <input type="text" name="estado" id="estado" class="form-input" maxlength="2"
                                   value="<?php echo isset($result) ? $result->estado : ''; ?>"
                                   placeholder="UF">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">CEP</label>
                        <input type="text" name="cep" id="cep" class="form-input cep"
                               value="<?php echo isset($result) ? $result->cep : ''; ?>"
                               placeholder="00000-000">
                    </div>
                </div>
            </div>

            <!-- Gestão -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);"><?= svg_icon('user', 22, 22) ?></div>
                    <div class="form-card-title">Gestão</div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Gestor Responsável</label>
                        <select name="gestor_obra_id" class="form-select">
                            <option value="" disabled <?php echo (!isset($result) || empty($result->gestor_obra_id)) ? 'selected' : ''; ?>>Selecione o gestor...</option>
                            <?php foreach ($tecnicos as $t): ?>
                                <option value="<?php echo $t->idUsuarios; ?>"
                                    <?php echo (isset($result) && $result->gestor_obra_id == $t->idUsuarios) ? 'selected' : ''; ?>
                                    ><?php echo $t->nome; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Responsável Técnico</label>
                        <select name="responsavel_tecnico_id" class="form-select">
                            <option value="" disabled <?php echo (!isset($result) || empty($result->responsavel_tecnico_id)) ? 'selected' : ''; ?>>Selecione o responsável técnico...</option>
                            <?php foreach ($tecnicos as $t): ?>
                                <option value="<?php echo $t->idUsuarios; ?>"
                                    <?php echo (isset($result) && $result->responsavel_tecnico_id == $t->idUsuarios) ? 'selected' : ''; ?>
                                    ><?php echo $t->nome; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row" style="display: contents;">
                        <div class="form-group">
                            <label class="form-label">Data de Início</label>
                            <input type="date" name="data_inicio" class="form-input"
                                   value="<?php echo isset($result) ? $result->data_inicio_contrato : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Previsão de Término</label>
                            <input type="date" name="data_previsao_fim" class="form-input"
                                   value="<?php echo isset($result) ? $result->data_fim_prevista : ''; ?>"
                                   >
                        </div>
                    </div>

                    <?php if (isset($result)): ?>
                    <?php
                    // Normalizar status atual para seleção correta
                    $status_atual_lower = strtolower(trim($result->status ?? ''));
                    $status_atual_norm = preg_replace('/[^a-z]/', '', $status_atual_lower);
                    $status_selecionado = null;
                    foreach ($status_obra as $s) {
                        $s_norm = strtolower(preg_replace('/[^a-z]/', '', $s->nome));
                        if ($status_atual_norm === $s_norm) {
                            $status_selecionado = $s->nome;
                            break;
                        }
                    }
                    // Fallback para compatibilidade com dados antigos
                    if (!$status_selecionado) {
                        if (in_array($status_atual_lower, ['em-andamento', 'em_execucao', 'em execucao', 'emexecucao', 'execucao'])) {
                            $status_selecionado = 'Em Andamento';
                        } elseif (in_array($status_atual_lower, ['contratada', 'aprovada', 'iniciada'])) {
                            $status_selecionado = 'Contratada';
                        } elseif (in_array($status_atual_lower, ['concluida', 'concluída', 'finalizada', 'entregue', 'concluido'])) {
                            $status_selecionado = 'Concluída';
                        } elseif (in_array($status_atual_lower, ['paralisada', 'pausada', 'suspensa'])) {
                            $status_selecionado = 'Paralisada';
                        } elseif (in_array($status_atual_lower, ['cancelada', 'cancelado', 'encerrada'])) {
                            $status_selecionado = 'Cancelada';
                        } else {
                            $status_selecionado = 'Prospecção';
                        }
                    }
                    ?>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" id="statusSelect">
                            <?php foreach ($status_obra as $s): ?>
                                <option value="<?php echo htmlspecialchars($s->nome); ?>"
                                    <?php echo ($status_selecionado == $s->nome) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s->nome); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small style="display: block; margin-top: 5px; color: #888; font-size: 12px;">
                            <?= svg_icon('info-circle', 14, 14) ?> Status atual no banco: <strong><?php echo htmlspecialchars($result->status); ?></strong>
                            <span style="color: #667eea;">(mapeado para: <?php echo $status_selecionado; ?>)</span>
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Visibilidade</label>
                        <div class="checkbox-container">
                            <input type="checkbox" id="visivel_cliente" name="visivel_cliente" value="1"
                                <?php echo (isset($result) && $result->visivel_cliente) ? 'checked' : ''; ?>
                            >
                            <label for="visivel_cliente"><?= svg_icon('eye', 16, 16) ?> Permitir cliente acompanhar progresso</label>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Observações -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon" style="background: linear-gradient(135deg, #fa709a, #fee140);"><?= svg_icon('comment', 22, 22) ?></div>
                    <div class="form-card-title">Observações</div>
                </div>

                <div class="form-group">
                    <textarea name="observacoes" class="form-textarea" placeholder="Descreva detalhes importantes sobre a obra..."><?php echo isset($result) ? $result->observacoes : ''; ?></textarea>
                </div>
            </div>

            <!-- Actions dentro do form -->
            <div class="form-card" style="background: transparent; box-shadow: none; border: none;">
                <div style="display: flex; justify-content: center; gap: 15px; padding: 20px 0;">
                    <button type="submit" class="form-btn form-btn-primary">
                        <?= svg_icon('save', 16, 16) ?> Salvar Obra
                    </button>

                    <a href="<?php echo site_url('obras'); ?>" class="form-btn form-btn-secondary" style="text-decoration: none;">
                        <?= svg_icon('x', 16, 16) ?> Cancelar
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.mask.min.js"></script>
<script>
$(document).ready(function() {
    $('.money').mask('000.000.000,00', {reverse: true});
    $('.cep').mask('00000-000');

    // Auto-preenchimento de endereço ao selecionar cliente
    $('#cliente_select').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var clienteId = $(this).val();

        if (clienteId) {
            // Pegar dados do data-attribute
            var documento = selectedOption.data('documento');
            var endereco = selectedOption.data('endereco');
            var numero = selectedOption.data('numero');
            var bairro = selectedOption.data('bairro');
            var cidade = selectedOption.data('cidade');
            var estado = selectedOption.data('estado');
            var cep = selectedOption.data('cep');

            // Montar endereço completo
            var enderecoCompleto = [];
            if (endereco) enderecoCompleto.push(endereco);
            if (numero) enderecoCompleto.push(numero);

            // Preencher campos
            $('#endereco').val(enderecoCompleto.join(', '));
            $('#bairro').val(bairro || '');
            $('#cidade').val(cidade || '');
            $('#estado').val(estado || '');
            $('#cep').val(cep || '');

            // Mostrar documento
            if (documento) {
                $('#cliente_info').show();
                $('#cliente_doc').text('CNPJ/CPF: ' + documento);
            } else {
                $('#cliente_info').hide();
            }
        } else {
            // Limpar campos se nenhum cliente selecionado
            $('#endereco').val('');
            $('#bairro').val('');
            $('#cidade').val('');
            $('#estado').val('');
            $('#cep').val('');
            $('#cliente_info').hide();
        }
    });

    // Trigger change se já tiver cliente selecionado (edição)
    if ($('#cliente_select').val()) {
        $('#cliente_select').trigger('change');
    }
});
</script>
