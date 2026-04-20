<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.form-unified { padding: 20px; max-width: 1200px; margin: 0 auto; }
.form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.form-header h1 { margin: 0; font-size: 28px; font-weight: 700; }
.form-header p { margin: 10px 0 0; opacity: 0.9; }

.form-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
}
.form-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.form-card-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.form-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
}

.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.form-group { margin-bottom: 0; }
.form-group.full-width { grid-column: span 2; }
.form-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}
.form-label .required { color: #e74c3c; }
.form-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s;
    box-sizing: border-box;
}
.form-input:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
.form-select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}
.form-select:focus { border-color: #667eea; outline: none; }
.form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    font-size: 14px;
    resize: vertical;
    min-height: 120px;
    box-sizing: border-box;
}
.form-textarea:focus { border-color: #667eea; outline: none; }

.form-row { display: flex; gap: 15px; }
.form-row .form-group { flex: 1; }

.checkbox-container {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 10px;
    cursor: pointer;
}
.checkbox-container input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #667eea;
}
.checkbox-container label {
    margin: 0;
    cursor: pointer;
    font-weight: 500;
}

.form-actions-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    padding: 20px 30px;
    box-shadow: 0 -5px 30px rgba(0,0,0,0.1);
    display: flex;
    justify-content: center;
    gap: 15px;
    z-index: 1000;
}
.form-btn {
    padding: 15px 40px;
    border-radius: 12px;
    border: none;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    transition: all 0.3s;
}
.form-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
.form-btn-primary {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
}
.form-btn-secondary {
    background: #f8f9fa;
    color: #666;
    border: 2px solid #e8e8e8;
}
.form-content-wrapper { padding-bottom: 100px; }

@media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
    .form-group.full-width { grid-column: span 1; }
    .form-row { flex-direction: column; }
}
</style>

<div class="form-unified">
    <!-- Header -->
    <div class="form-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1><i class="icon-building"></i> <?php echo isset($result) ? 'Editar' : 'Nova'; ?> Obra</h1>
                <p><i class="icon-arrow-left"></i> <a href="<?php echo site_url('obras'); ?>" style="color: white;">Voltar para lista</a></p>
            </div>
            <?php if (isset($result)): ?>
            <div style="text-align: right;">
                <div style="font-size: 14px; opacity: 0.8;">Código</div>
                <div style="font-size: 24px; font-weight: 700;">#<?php echo $result->id; ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-content-wrapper">
        <form method="post" action="">

            <!-- Dados Básicos -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon"><i class="icon-file-alt"></i></div>
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
                            <option value="">Selecione o cliente...</option>
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
                            <i class="icon-info-sign"></i> <span id="cliente_doc"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo de Obra</label>
                        <select name="tipo_obra" class="form-select">
                            <option value="Reforma" <?php echo (isset($result) && $result->tipo_obra == 'Reforma') ? 'selected' : ''; ?>>Reforma</option>
                            <option value="Construcao" <?php echo (isset($result) && $result->tipo_obra == 'Construcao') ? 'selected' : ''; ?>>Construção</option>
                            <option value="Instalacao" <?php echo (isset($result) && $result->tipo_obra == 'Instalacao') ? 'selected' : ''; ?>>Instalação</option>
                            <option value="Manutencao" <?php echo (isset($result) && $result->tipo_obra == 'Manutencao') ? 'selected' : ''; ?>>Manutenção</option>
                            <option value="Outro" <?php echo (isset($result) && $result->tipo_obra == 'Outro') ? 'selected' : ''; ?>>Outro</option>
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
                    <div class="form-card-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d);"><i class="icon-map-marker"></i></div>
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
                    <div class="form-card-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);"><i class="icon-user"></i></div>
                    <div class="form-card-title">Gestão</div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Gestor Responsável</label>
                        <select name="gestor_id" class="form-select">
                            <option value="">Selecione o gestor...</option>
                            <?php foreach ($tecnicos as $t): ?>
                                <option value="<?php echo $t->idUsuarios; ?>"
                                    <?php echo (isset($result) && $result->gestor_id == $t->idUsuarios) ? 'selected' : ''; ?>
                                    ><?php echo $t->nome; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Responsável Técnico</label>
                        <select name="responsavel_tecnico_id" class="form-select">
                            <option value="">Selecione o responsável técnico...</option>
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
003e
                        </div>
                    </div>

                    <?php if (isset($result)): ?>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Prospeccao" <?php echo $result->status == 'Prospeccao' ? 'selected' : ''; ?>>Prospecção</option>
                            <option value="Em Andamento" <?php echo $result->status == 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                            <option value="Paralisada" <?php echo $result->status == 'Paralisada' ? 'selected' : ''; ?>>Paralisada</option>
                            <option value="Concluida" <?php echo in_array($result->status, ['Concluida', 'Concluída']) ? 'selected' : ''; ?>>Concluída</option>
                            <option value="Cancelada" <?php echo $result->status == 'Cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Visibilidade</label>
                        <div class="checkbox-container">
                            <input type="checkbox" id="visivel_cliente" name="visivel_cliente" value="1"
                                <?php echo (isset($result) && $result->visivel_cliente) ? 'checked' : ''; ?>
                            >
                            <label for="visivel_cliente"><i class="icon-eye-open"></i> Permitir cliente acompanhar progresso</label>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Observações -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon" style="background: linear-gradient(135deg, #fa709a, #fee140);"><i class="icon-comment"></i></div>
                    <div class="form-card-title">Observações</div>
                </div>

                <div class="form-group">
                    <textarea name="observacoes" class="form-textarea" placeholder="Descreva detalhes importantes sobre a obra..."><?php echo isset($result) ? $result->observacoes : ''; ?></textarea>
                </div>
            </div>

        </form>
    </div>

    <!-- Fixed Actions Bar -->
    <div class="form-actions-bar">
        <button type="submit" class="form-btn form-btn-primary" onclick="document.querySelector('form').submit()">
            <i class="icon-save"></i> Salvar Obra
        </button>
        <a href="<?php echo site_url('obras'); ?>" class="form-btn form-btn-secondary">
            <i class="icon-remove"></i> Cancelar
        </a>
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
