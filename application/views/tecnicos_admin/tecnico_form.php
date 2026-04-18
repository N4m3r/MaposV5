<?php
/**
 * Formulário de Cadastro/Edição de Técnico
 * Suporta modo de criação e edição
 */

$is_edicao = isset($tecnico) && $tecnico;
$titulo = $is_edicao ? 'Editar Técnico' : 'Cadastrar Técnico';
?>
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="icon-user"></i>
                </span>
                <h5><?php echo $titulo; ?></h5>
            </div>

            <div class="widget-content nopadding">
                <form action="" method="POST" class="form-horizontal">

                    <div class="control-group">
                        <label class="control-label">Nome *</label>
                        <div class="controls">
                            <input type="text" name="nome" value="<?php echo $is_edicao ? $tecnico->nome : set_value('nome'); ?>" class="span8" required>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">E-mail *</label>
                        <div class="controls">
                            <input type="email" name="email" value="<?php echo $is_edicao ? $tecnico->email : set_value('email'); ?>" class="span8" required <?php echo $is_edicao ? 'readonly' : ''; ?>>
                            <?php if ($is_edicao): ?>
                                <small class="help-block">E-mail não pode ser alterado</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Senha <?php echo $is_edicao ? '' : '*'; ?></label>
                        <div class="controls">
                            <input type="password" name="senha" class="span8" <?php echo $is_edicao ? '' : 'required'; ?>>
                            <?php if ($is_edicao): ?>
                                <small class="help-block">Deixe em branco para manter a senha atual</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Telefone</label>
                        <div class="controls">
                            <input type="text" name="telefone" value="<?php echo $is_edicao ? $tecnico->telefone : set_value('telefone'); ?>" class="span4">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">CPF</label>
                        <div class="controls">
                            <input type="text" name="cpf" value="<?php echo $is_edicao ? ($tecnico->cpf ?? '') : set_value('cpf'); ?>" class="span4">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Nível do Técnico *</label>
                        <div class="controls">
                            <select name="nivel_tecnico" class="span4" required>
                                <option value="">Selecione...</option>
                                <option value="I" <?php echo ($is_edicao && $tecnico->nivel_tecnico == 'I') || set_select('nivel_tecnico', 'I') ? 'selected' : ''; ?>>I - Aprendiz</option>
                                <option value="II" <?php echo ($is_edicao && $tecnico->nivel_tecnico == 'II') || set_select('nivel_tecnico', 'II') ? 'selected' : ''; ?>>II - Técnico</option>
                                <option value="III" <?php echo ($is_edicao && $tecnico->nivel_tecnico == 'III') || set_select('nivel_tecnico', 'III') ? 'selected' : ''; ?>>III - Especialista</option>
                                <option value="IV" <?php echo ($is_edicao && $tecnico->nivel_tecnico == 'IV') || set_select('nivel_tecnico', 'IV') ? 'selected' : ''; ?>>IV - Coordenador</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Especialidades</label>
                        <div class="controls">
                            <input type="text" name="especialidades" value="<?php echo $is_edicao ? ($tecnico->especialidades ?? '') : set_value('especialidades'); ?>" class="span8"
                                   placeholder="CFTV, Alarmes, Redes, etc. (separados por vírgula)">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Veículo - Tipo</label>
                        <div class="controls">
                            <input type="text" name="veiculo_tipo" value="<?php echo $is_edicao ? ($tecnico->veiculo_tipo ?? '') : set_value('veiculo_tipo'); ?>" class="span4" placeholder="Ex: Moto, Carro, Van">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Veículo - Placa</label>
                        <div class="controls">
                            <input type="text" name="veiculo_placa" value="<?php echo $is_edicao ? ($tecnico->veiculo_placa ?? '') : set_value('veiculo_placa'); ?>" class="span4" placeholder="ABC-1234">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Coordenadas Base (Lat/Lng)</label>
                        <div class="controls">
                            <input type="text" name="coordenadas_base_lat" value="<?php echo $is_edicao ? ($tecnico->coordenadas_base_lat ?? '') : set_value('coordenadas_base_lat'); ?>" class="span4" placeholder="Latitude">
                            <input type="text" name="coordenadas_base_lng" value="<?php echo $is_edicao ? ($tecnico->coordenadas_base_lng ?? '') : set_value('coordenadas_base_lng'); ?>" class="span4" placeholder="Longitude">
                            <small class="help-block">Coordenadas da base para controle de raio de atuação</small>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Raio de Atuação (km)</label>
                        <div class="controls">
                            <input type="number" name="raio_atuacao_km" value="<?php echo $is_edicao ? ($tecnico->raio_atuacao_km ?? 0) : set_value('raio_atuacao_km', 0); ?>" class="span2">
                            <small class="help-block">0 = sem limite</small>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Plantão 24h</label>
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="plantao_24h" value="1" <?php echo ($is_edicao && ($tecnico->plantao_24h ?? 0)) || set_checkbox('plantao_24h', '1') ? 'checked' : ''; ?>>
                                Disponível para plantão
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="icon-ok icon-white"></i> Salvar
                        </button>
                        <a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>" class="btn">
                            <i class="icon-arrow-left"></i> Voltar
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
