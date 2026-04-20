<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-building"></i></span>
                <h5><?php echo isset($result) ? 'Editar' : 'Nova'; ?> Obra</h5>
            </div>

            <div class="widget-content nopadding">
                <form class="form-horizontal" method="post" action="">

                    <!-- Dados Básicos -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <h5>Dados Básicos</h5>
                        </div>
                        <div class="widget-content">
                            <div class="control-group">
                                <label class="control-label">Nome da Obra <span class="required">*</span></label>
                                <div class="controls">
                                    <input type="text" name="nome" class="span6" required
                                           value="<?php echo isset($result) ? $result->nome : ''; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Cliente <span class="required">*</span></label>
                                <div class="controls">
                                    <select name="cliente_id" class="span6" required <?php echo isset($result) ? 'disabled' : ''; ?>>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($clientes as $c): ?>
                                            <option value="<?php echo $c->idClientes; ?>"
                                                <?php echo (isset($result) && $result->cliente_id == $c->idClientes) ? 'selected' : ''; ?>>
                                                <?php echo $c->nomeCliente; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($result)): ?>
                                        <input type="hidden" name="cliente_id" value="<?php echo $result->cliente_id; ?>">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Tipo de Obra</label>
                                <div class="controls">
                                    <select name="tipo_obra" class="span6">
                                        <option value="Reforma" <?php echo (isset($result) && $result->tipo_obra == 'Reforma') ? 'selected' : ''; ?>>Reforma</option>
                                        <option value="Construcao" <?php echo (isset($result) && $result->tipo_obra == 'Construcao') ? 'selected' : ''; ?>>Construção</option>
                                        <option value="Instalacao" <?php echo (isset($result) && $result->tipo_obra == 'Instalacao') ? 'selected' : ''; ?>>Instalação</option>
                                        <option value="Manutencao" <?php echo (isset($result) && $result->tipo_obra == 'Manutencao') ? 'selected' : ''; ?>>Manutenção</option>
                                        <option value="Outro" <?php echo (isset($result) && $result->tipo_obra == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Valor do Contrato (R$)</label>
                                <div class="controls">
                                    <input type="text" name="valor_contrato" class="span3 money"
                                           value="<?php echo isset($result) ? number_format($result->valor_contrato ?? 0, 2, ',', '.') : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Localização -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <h5>Localização</h5>
                        </div>
                        <div class="widget-content">
                            <div class="control-group">
                                <label class="control-label">Endereço</label>
                                <div class="controls">
                                    <input type="text" name="endereco" class="span6"
                                           value="<?php echo isset($result) ? $result->endereco : ''; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Bairro</label>
                                <div class="controls">
                                    <input type="text" name="bairro" class="span3"
                                           value="<?php echo isset($result) ? $result->bairro : ''; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Cidade/UF</label>
                                <div class="controls">
                                    <input type="text" name="cidade" class="span3" placeholder="Cidade"
                                           value="<?php echo isset($result) ? $result->cidade : ''; ?>">
                                    <input type="text" name="estado" class="span1" placeholder="UF" maxlength="2"
                                           value="<?php echo isset($result) ? $result->estado : ''; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">CEP</label>
                                <div class="controls">
                                    <input type="text" name="cep" class="span2 cep"
                                           value="<?php echo isset($result) ? $result->cep : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gestão -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <h5>Gestão</h5>
                        </div>
                        <div class="widget-content">
                            <div class="control-group">
                                <label class="control-label">Gestor Responsável</label>
                                <div class="controls">
                                    <select name="gestor_id" class="span4">
                                        <option value="">Selecione...</option>
                                        <?php foreach ($tecnicos as $t): ?>
                                            <option value="<?php echo $t->idUsuarios; ?>"
                                                <?php echo (isset($result) && $result->gestor_id == $t->idUsuarios) ? 'selected' : ''; ?>>
                                                <?php echo $t->nome; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Responsável Técnico</label>
                                <div class="controls">
                                    <select name="responsavel_tecnico_id" class="span4">
                                        <option value="">Selecione...</option>
                                        <?php foreach ($tecnicos as $t): ?>
                                            <option value="<?php echo $t->idUsuarios; ?>"
                                                <?php echo (isset($result) && $result->responsavel_tecnico_id == $t->idUsuarios) ? 'selected' : ''; ?>>
                                                <?php echo $t->nome; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Data Início</label>
                                <div class="controls">
                                    <input type="date" name="data_inicio" class="span2"
                                           value="<?php echo isset($result) ? $result->data_inicio_contrato : ''; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Previsão Término</label>
                                <div class="controls">
                                    <input type="date" name="data_previsao_fim" class="span2"
                                           value="<?php echo isset($result) ? $result->data_fim_prevista : ''; ?>">
                                </div>
                            </div>

                            <?php if (isset($result)): ?>
                            <div class="control-group">
                                <label class="control-label">Status</label>
                                <div class="controls">
                                    <select name="status" class="span3">
                                        <option value="Prospeccao" <?php echo $result->status == 'Prospeccao' ? 'selected' : ''; ?>>Prospecção</option>
                                        <option value="Em Andamento" <?php echo $result->status == 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                                        <option value="Paralisada" <?php echo $result->status == 'Paralisada' ? 'selected' : ''; ?>>Paralisada</option>
                                        <option value="Concluida" <?php echo $result->status == 'Concluida' ? 'selected' : ''; ?>>Concluída</option>
                                        <option value="Cancelada" <?php echo $result->status == 'Cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Visível ao Cliente</label>
                                <div class="controls">
                                    <label class="checkbox">
                                        <input type="checkbox" name="visivel_cliente" value="1"
                                            <?php echo (isset($result) && $result->visivel_cliente) ? 'checked' : ''; ?>>
                                        Permitir cliente acompanhar progresso
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <h5>Observações</h5>
                        </div>
                        <div class="widget-content">
                            <div class="control-group">
                                <div class="controls" style="margin-left: 20px;">
                                    <textarea name="observacoes" class="span8" rows="4"><?php echo isset($result) ? $result->observacoes : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="icon-save icon-white"></i> Salvar
                        </button>
                        <a href="<?php echo site_url('obras'); ?>" class="btn">
                            <i class="icon-arrow-left"></i> Voltar
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.mask.min.js"></script>
<script>
$(document).ready(function() {
    $('.money').mask('000.000.000,00', {reverse: true});
    $('.cep').mask('00000-000');
});
</script>
