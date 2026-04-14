<!-- View temporária simplificada para o formulário de técnico -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="icon-user"></i>
                </span>
                <h5>Cadastro de Técnico</h5>
            </div>

            <div class="widget-content nopadding">
                <form action="" method="POST" class="form-horizontal">

                    <div class="control-group">
                        <label class="control-label">Nome *</label>
                        <div class="controls">
                            <input type="text" name="nome" value="<?php echo set_value('nome'); ?>" class="span8" required>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">E-mail *</label>
                        <div class="controls">
                            <input type="email" name="email" value="<?php echo set_value('email'); ?>" class="span8" required>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Senha *</label>
                        <div class="controls">
                            <input type="password" name="senha" class="span8" <?php echo isset($tecnico) ? '' : 'required'; ?>>
                            <?php if (isset($tecnico)): ?>
                                <small class="help-block">Deixe em branco para manter a senha atual</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Telefone</label>
                        <div class="controls">
                            <input type="text" name="telefone" value="<?php echo set_value('telefone'); ?>" class="span4">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">CPF</label>
                        <div class="controls">
                            <input type="text" name="cpf" value="<?php echo set_value('cpf'); ?>" class="span4">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Nível do Técnico *</label>
                        <div class="controls">
                            <select name="nivel_tecnico" class="span4" required>
                                <option value="">Selecione...</option>
                                <option value="I" <?php echo set_select('nivel_tecnico', 'I'); ?>>I - Aprendiz</option>
                                <option value="II" <?php echo set_select('nivel_tecnico', 'II'); ?>>II - Técnico</option>
                                <option value="III" <?php echo set_select('nivel_tecnico', 'III'); ?>>III - Especialista</option>
                                <option value="IV" <?php echo set_select('nivel_tecnico', 'IV'); ?>>IV - Coordenador</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Especialidades</label>
                        <div class="controls">
                            <input type="text" name="especialidades" value="<?php echo set_value('especialidades'); ?>" class="span8"
                                   placeholder="CFTV, Alarmes, Redes, etc. (separados por vírgula)">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Veículo</label>
                        <div class="controls">
                            <input type="text" name="veiculo_tipo" value="<?php echo set_value('veiculo_tipo'); ?>" class="span4" placeholder="Tipo">
                            <input type="text" name="veiculo_placa" value="<?php echo set_value('veiculo_placa'); ?>" class="span4" placeholder="Placa">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Raio de Atuação (km)</label>
                        <div class="controls">
                            <input type="number" name="raio_atuacao_km" value="<?php echo set_value('raio_atuacao_km', 0); ?>" class="span2">
                            <small class="help-block">0 = sem limite</small>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Plantão 24h</label>
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="plantao_24h" value="1" <?php echo set_checkbox('plantao_24h', '1'); ?>>
                                Disponível para plantão
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="btn">Voltar</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
