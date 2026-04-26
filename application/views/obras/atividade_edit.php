<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.atividade-edit {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Header */
.atividade-edit-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.atividade-edit-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}
.atividade-edit-header-left { flex: 1; }
.atividade-edit-breadcrumb {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 10px;
}
.atividade-edit-breadcrumb a {
    color: white;
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.3s;
}
.atividade-edit-breadcrumb a:hover {
    opacity: 1;
    text-decoration: underline;
}
.atividade-edit-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Form Card */
.edit-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
}
.edit-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.edit-card-icon {
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
.edit-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.form-group {
    margin-bottom: 20px;
}
.form-group.full-width {
    grid-column: span 2;
}
.form-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}
.form-label .required {
    color: #e74c3c;
}
.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    font-size: 14px;
    color: #333;
    background: white;
    transition: all 0.3s;
    box-sizing: border-box;
}
.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
.form-textarea {
    resize: vertical;
    min-height: 100px;
}
.form-hint {
    font-size: 12px;
    color: #888;
    margin-top: 5px;
}

/* Checkbox */
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

/* Progress Input */
.progress-input-group {
    display: flex;
    align-items: center;
    gap: 15px;
}
.progress-input {
    flex: 1;
}
.progress-value-display {
    font-size: 24px;
    font-weight: 700;
    color: #667eea;
    min-width: 60px;
    text-align: right;
}

/* Buttons */
.form-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    padding: 20px 0;
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
.form-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
.form-btn-primary {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
}
.form-btn-secondary {
    background: #f8f9fa;
    color: #666;
    border: 2px solid #e8e8e8;
}

/* Responsive */
@media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
    .form-group.full-width { grid-column: span 1; }
    .form-actions { flex-direction: column; }
    .form-btn { width: 100%; justify-content: center; }
}
</style>

<div class="atividade-edit">
    <!-- Header -->
    <div class="atividade-edit-header">
        <div class="atividade-edit-header-content">
            <div class="atividade-edit-header-left">
                <div class="atividade-edit-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>">Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo $obra->nome; ?></a> &raquo;
                    <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>">Atividades</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizarAtividade/' . $atividade->id); ?>">Visualizar</a> &raquo;
                    <span>Editar</span>
                </div>
                <h1><i class="icon-edit"></i> Editar Atividade</h1>
            </div>
        </div>
    </div>

    <!-- Mensagens -->
    <?php if ($this->session->flashdata('success')): ?>
    <div style="background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="icon-ok" style="font-size: 20px;"></i>
        <strong><?php echo $this->session->flashdata('success'); ?></strong>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="icon-remove" style="font-size: 20px;"></i>
        <strong><?php echo $this->session->flashdata('error'); ?></strong>
    </div>
    <?php endif; ?>

    <form method="post" action="" id="formEditarAtividade">
        <!-- Dados da Atividade -->
        <div class="edit-card">
            <div class="edit-card-header">
                <div class="edit-card-icon"><i class="icon-file-alt"></i></div>
                <div class="edit-card-title">Dados da Atividade</div>
            </div>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">Título <span class="required">*</span></label>
                    <input type="text" name="titulo" class="form-input" required
                           value="<?php echo htmlspecialchars($atividade->titulo ?? ''); ?>"
                           placeholder="Ex: Instalação elétrica, Reunião com cliente...">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-textarea" placeholder="Descreva os detalhes desta atividade..."><?php echo htmlspecialchars($atividade->descricao ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Data da Atividade <span class="required">*</span></label>
                    <input type="date" name="data_atividade" class="form-input" required
                           value="<?php echo $atividade->data_atividade ?? date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <?php foreach ($tipos_atividades as $t): ?>
                            <option value="<?php echo htmlspecialchars($t->nome); ?>"
                                <?php echo ($atividade->tipo ?? '') == $t->nome ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t->nome); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach ($status_atividade as $s): ?>
                            <option value="<?php echo htmlspecialchars($s->nome); ?>"
                                <?php echo ($atividade->status ?? '') == $s->nome ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s->nome); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Técnico Responsável</label>
                    <select name="tecnico_id" class="form-select">
                        <option value="">Selecione um técnico...</option>
                        <?php foreach ($tecnicos as $t): ?>
                        <option value="<?php echo $t->idUsuarios; ?>" <?php echo (int)($atividade->tecnico_id ?? 0) == (int)$t->idUsuarios ? 'selected' : ''; ?>
                            data-id="<?php echo $t->idUsuarios; ?>">
                            <?php echo $t->nome; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Etapa Relacionada</label>
                    <select name="etapa_id" class="form-select">
                        <option value="">Selecione uma etapa...</option>
                        <?php if (isset($etapas) && !empty($etapas)): ?>
                            <?php foreach ($etapas as $e): ?>
                            <option value="<?php echo $e->id; ?>" <?php echo (int)($atividade->etapa_id ?? 0) == (int)$e->id ? 'selected' : ''; ?>
                                data-id="<?php echo $e->id; ?>">
                                #<?php echo $e->numero_etapa; ?> - <?php echo $e->nome; ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Progresso (%)</label>
                    <div class="progress-input-group">
                        <input type="range" name="percentual_concluido" class="form-input progress-input" id="progressInput"
                               min="0" max="100" value="<?php echo $atividade->percentual_concluido ?? 0; ?>">
                        <div class="progress-value-display" id="progressValue"><?php echo $atividade->percentual_concluido ?? 0; ?>%</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-container" style="margin-top: 28px;">
                        <input type="checkbox" name="visivel_cliente" value="1" <?php echo ($atividade->visivel_cliente ?? 0) ? 'checked' : ''; ?>>
                        <label>Visível ao cliente</label>
                    </label>
                    <div class="form-hint">Marque para que o cliente possa ver esta atividade</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="submit" class="form-btn form-btn-primary">
                <i class="icon-save"></i> Salvar Alterações
            </button>

            <a href="<?php echo site_url('obras/visualizarAtividade/' . $atividade->id); ?>" class="form-btn form-btn-secondary" style="text-decoration: none;">
                <i class="icon-remove"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Atualizar display do progresso
const progressInput = document.getElementById('progressInput');
const progressValue = document.getElementById('progressValue');

if (progressInput && progressValue) {
    progressInput.addEventListener('input', function() {
        progressValue.textContent = this.value + '%';
    });
}
</script>
