<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.etapa-edit-modern {
    padding: 20px;
    max-width: 900px;
    margin: 0 auto;
}

/* Header */
.etapa-edit-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.etapa-edit-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}
.etapa-edit-header-left {
    flex: 1;
}
.etapa-edit-breadcrumb {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 10px;
}
.etapa-edit-breadcrumb a {
    color: white;
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.3s;
}
.etapa-edit-breadcrumb a:hover {
    opacity: 1;
    text-decoration: underline;
}
.etapa-edit-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}
.etapa-edit-header h1 i {
    font-size: 32px;
}
.etapa-edit-subtitle {
    margin-top: 8px;
    opacity: 0.9;
    font-size: 15px;
}

.etapa-edit-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.etapa-edit-btn {
    padding: 12px 24px;
    border-radius: 12px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}
.etapa-edit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
.etapa-edit-btn-secondary {
    background: rgba(255,255,255,0.2);
    color: white;
}
.etapa-edit-btn-secondary:hover {
    background: rgba(255,255,255,0.3);
}

/* Form Container */
.etapa-edit-form-container {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.etapa-edit-form-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}
.etapa-edit-form-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #333;
}
.etapa-edit-form-header i {
    color: #667eea;
    font-size: 24px;
}

/* Form Fields */
.etapa-edit-form-group {
    margin-bottom: 20px;
}
.etapa-edit-form-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}
.etapa-edit-form-label .required {
    color: #e74c3c;
}
.etapa-edit-form-input,
.etapa-edit-form-select,
.etapa-edit-form-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    color: #333;
    background: white;
    transition: all 0.3s;
    box-sizing: border-box;
}
.etapa-edit-form-input:focus,
.etapa-edit-form-select:focus,
.etapa-edit-form-textarea:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}
.etapa-edit-form-textarea {
    resize: vertical;
    min-height: 100px;
}
.etapa-edit-form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.etapa-edit-form-hint {
    font-size: 12px;
    color: #888;
    margin-top: 6px;
}

/* Status Badge Selector */
.etapa-edit-status-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.etapa-edit-status-option {
    display: none;
}
.etapa-edit-status-label {
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid #e0e0e0;
    background: white;
    color: #666;
}
.etapa-edit-status-option:checked + .etapa-edit-status-label.pendente {
    background: #ecf0f1;
    border-color: #95a5a6;
    color: #7f8c8d;
}
.etapa-edit-status-option:checked + .etapa-edit-status-label.em_andamento {
    background: #3498db;
    border-color: #3498db;
    color: white;
}
.etapa-edit-status-option:checked + .etapa-edit-status-label.concluida {
    background: #27ae60;
    border-color: #27ae60;
    color: white;
}
.etapa-edit-status-option:checked + .etapa-edit-status-label.atrasada {
    background: #e74c3c;
    border-color: #e74c3c;
    color: white;
}

/* Form Actions */
.etapa-edit-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #f0f0f0;
}
.etapa-edit-btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 14px 32px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.etapa-edit-btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}
.etapa-edit-btn-cancel {
    background: #f5f5f5;
    color: #666;
    padding: 14px 28px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}
.etapa-edit-btn-cancel:hover {
    background: #e0e0e0;
}

/* Responsive */
@media (max-width: 768px) {
    .etapa-edit-form-row {
        grid-template-columns: 1fr;
    }
    .etapa-edit-header-content {
        flex-direction: column;
    }
}
</style>

<div class="etapa-edit-modern">
    <!-- Header -->
    <div class="etapa-edit-header">
        <div class="etapa-edit-header-content">
            <div class="etapa-edit-header-left">
                <div class="etapa-edit-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><i class="icon-arrow-left"></i> Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo htmlspecialchars($obra->nome); ?></a> &raquo;
                    <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>">Etapas</a> &raquo;
                    <span>Editar</span>
                </div>
                <h1><i class="icon-edit"></i> Editar Etapa</h1>
                <div class="etapa-edit-subtitle">Modifique os dados da etapa #<?php echo $etapa->numero_etapa; ?> - <?php echo htmlspecialchars($etapa->nome); ?></div>
            </div>
            <div class="etapa-edit-actions">
                <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>" class="etapa-edit-btn etapa-edit-btn-secondary">
                    <i class="icon-arrow-left"></i> Voltar para Etapas
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="etapa-edit-form-container">
        <div class="etapa-edit-form-header">
            <i class="icon-tasks"></i>
            <h2>Informações da Etapa</h2>
        </div>

        <form action="<?php echo site_url('obras/editarEtapa/' . $etapa->id); ?>" method="post">
            <div class="etapa-edit-form-row">
                <div class="etapa-edit-form-group">
                    <label class="etapa-edit-form-label" for="numero_etapa">
                        <i class="icon-sort-by-order"></i> Número da Etapa <span class="required">*</span>
                    </label>
                    <input type="number" name="numero_etapa" id="numero_etapa" class="etapa-edit-form-input" value="<?php echo $etapa->numero_etapa; ?>" min="1" required>
                    <div class="etapa-edit-form-hint">Ordem de execução desta etapa</div>
                </div>

                <div class="etapa-edit-form-group">
                    <label class="etapa-edit-form-label" for="nome">
                        <i class="icon-tag"></i> Nome da Etapa <span class="required">*</span>
                    </label>
                    <input type="text" name="nome" id="nome" class="etapa-edit-form-input" value="<?php echo htmlspecialchars($etapa->nome); ?>" maxlength="100" placeholder="Ex: Fundação, Estrutura, Acabamento..." required>
                </div>
            </div>

            <div class="etapa-edit-form-group">
                <label class="etapa-edit-form-label" for="especialidade">
                    <i class="icon-briefcase"></i> Especialidade
                </label>
                <select name="especialidade" id="especialidade" class="etapa-edit-form-select">
                    <option value="">Selecione uma especialidade...</option>
                    <?php foreach ($especialidades as $esp): ?>
                    <option value="<?php echo htmlspecialchars($esp->nome); ?>" <?php echo ($etapa->especialidade == $esp->nome) ? 'selected' : ''; ?>><?php echo htmlspecialchars($esp->nome); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="etapa-edit-form-group">
                <label class="etapa-edit-form-label" for="descricao">
                    <i class="icon-align-left"></i> Descrição
                </label>
                <textarea name="descricao" id="descricao" class="etapa-edit-form-textarea" rows="3" placeholder="Descreva os detalhes desta etapa..."><?php echo htmlspecialchars($etapa->descricao ?? ''); ?></textarea>
            </div>

            <div class="etapa-edit-form-row">
                <div class="etapa-edit-form-group">
                    <label class="etapa-edit-form-label" for="data_inicio_prevista">
                        <i class="icon-calendar"></i> Data Início Prevista
                    </label>
                    <input type="date" name="data_inicio_prevista" id="data_inicio_prevista" class="etapa-edit-form-input" value="<?php echo $etapa->data_inicio_prevista; ?>">
                </div>

                <div class="etapa-edit-form-group">
                    <label class="etapa-edit-form-label" for="data_fim_prevista">
                        <i class="icon-calendar-check"></i> Data Término Prevista
                    </label>
                    <input type="date" name="data_fim_prevista" id="data_fim_prevista" class="etapa-edit-form-input" value="<?php echo $etapa->data_fim_prevista; ?>">
                </div>
            </div>

            <div class="etapa-edit-form-group">
                <label class="etapa-edit-form-label">
                    <i class="icon-info-sign"></i> Status da Etapa
                </label>
                <select name="status" class="etapa-edit-form-select">
                    <?php foreach ($status_obra as $s): ?>
                        <option value="<?php echo htmlspecialchars($s->nome); ?>"
                            <?php echo ($etapa->status ?? '') == $s->nome ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s->nome); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="etapa-edit-form-actions">
                <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>" class="etapa-edit-btn-cancel">
                    <i class="icon-remove"></i> Cancelar
                </a>
                <button type="submit" class="etapa-edit-btn-submit">
                    <i class="icon-save"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
