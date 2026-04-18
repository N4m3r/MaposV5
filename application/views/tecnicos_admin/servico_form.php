<?php
/**
 * Formulário de Cadastro de Serviço no Catálogo
 */
$tipos = $tipos ?? ['INS' => 'Instalação', 'MP' => 'Manutenção Preventiva', 'MC' => 'Manutenção Corretiva', 'CT' => 'Consultoria', 'TR' => 'Treinamento', 'UP' => 'Upgrade', 'URG' => 'Urgência'];
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-wrench"></i></span>
                <h5>Cadastrar Serviço</h5>
            </div>
            <div class="widget-content nopadding">
                <form action="" method="POST" class="form-horizontal">

                    <div class="control-group">
                        <label class="control-label">Código *</label>
                        <div class="controls">
                            <input type="text" name="codigo" value="<?php echo set_value('codigo'); ?>" class="span4" required>
                            <small class="help-block">Código único do serviço (ex: INST-CFTV-001)</small>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Nome *</label>
                        <div class="controls">
                            <input type="text" name="nome" value="<?php echo set_value('nome'); ?>" class="span8" required>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Descrição</label>
                        <div class="controls">
                            <textarea name="descricao" class="span8" rows="3"><?php echo set_value('descricao'); ?></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Tipo *</label>
                        <div class="controls">
                            <select name="tipo" class="span4" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($tipos as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo set_select('tipo', $key); ?>>
                                        <?php echo $key; ?> - <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Categoria</label>
                        <div class="controls">
                            <input type="text" name="categoria" value="<?php echo set_value('categoria'); ?>" class="span4" placeholder="Ex: CFTV, Alarmes, Redes">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Tempo Estimado (horas)</label>
                        <div class="controls">
                            <input type="number" name="tempo_estimado_horas" value="<?php echo set_value('tempo_estimado_horas'); ?>" class="span2" step="0.5" min="0">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Checklist Padrão</label>
                        <div class="controls">
                            <div id="checklist-items">
                                <div class="checklist-item row-fluid" style="margin-bottom: 10px;">
                                    <div class="span6">
                                        <input type="text" name="checklist[0][descricao]" class="span12" placeholder="Descrição do item">
                                    </div>
                                    <div class="span2">
                                        <select name="checklist[0][tipo]" class="span12">
                                            <option value="checkbox">Checkbox</option>
                                            <option value="texto">Texto</option>
                                            <option value="numero">Número</option>
                                            <option value="foto">Foto</option>
                                        </select>
                                    </div>
                                    <div class="span2">
                                        <label class="checkbox">
                                            <input type="checkbox" name="checklist[0][obrigatorio]" value="1"> Obrigatório
                                        </label>
                                    </div>
                                    <div class="span2">
                                        <button type="button" class="btn btn-danger btn-mini" onclick="removerItem(this)">
                                            <i class="icon-remove icon-white"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-info btn-small" onclick="adicionarItem()">
                                <i class="icon-plus icon-white"></i> Adicionar Item
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="icon-ok icon-white"></i> Salvar
                        </button>
                        <a href="<?php echo site_url('tecnicos_admin/servicos_catalogo'); ?>" class="btn">
                            <i class="icon-arrow-left"></i> Voltar
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = 1;

function adicionarItem() {
    const container = document.getElementById('checklist-items');
    const html = `
        <div class="checklist-item row-fluid" style="margin-bottom: 10px;">
            <div class="span6">
                <input type="text" name="checklist[${itemCount}][descricao]" class="span12" placeholder="Descrição do item">
            </div>
            <div class="span2">
                <select name="checklist[${itemCount}][tipo]" class="span12">
                    <option value="checkbox">Checkbox</option>
                    <option value="texto">Texto</option>
                    <option value="numero">Número</option>
                    <option value="foto">Foto</option>
                </select>
            </div>
            <div class="span2">
                <label class="checkbox">
                    <input type="checkbox" name="checklist[${itemCount}][obrigatorio]" value="1"> Obrigatório
                </label>
            </div>
            <div class="span2">
                <button type="button" class="btn btn-danger btn-mini" onclick="removerItem(this)">
                    <i class="icon-remove icon-white"></i>
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    itemCount++;
}

function removerItem(btn) {
    btn.closest('.checklist-item').remove();
}
</script>
