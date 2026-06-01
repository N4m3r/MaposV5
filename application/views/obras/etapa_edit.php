<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<div class="etapa-edit-modern">
    <!-- Header -->
    <div class="etapa-edit-header">
        <div class="etapa-edit-header-content">
            <div class="etapa-edit-header-left">
                <div class="etapa-edit-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><i class="bx bx-arrow-back"></i> Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo htmlspecialchars($obra->nome); ?></a> &raquo;
                    <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>">Etapas</a> &raquo;
                    <span>Editar</span>
                </div>
                <h1><i class="bx bx-edit"></i> Editar Etapa</h1>
                <div class="etapa-edit-subtitle">Modifique os dados da etapa #<?php echo $etapa->numero_etapa; ?> - <?php echo htmlspecialchars($etapa->nome); ?></div>
            </div>
            <div class="etapa-edit-actions">
                <a href="<?php echo site_url('obras/etapas/' . $obra->id); ?>" class="etapa-edit-btn etapa-edit-btn-secondary">
                    <i class="bx bx-arrow-back"></i> Voltar para Etapas
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="etapa-edit-form-container">
        <div class="etapa-edit-form-header">
            <i class="bx bx-list-check"></i>
            <h2>Informações da Etapa</h2>
        </div>

        <form action="<?php echo site_url('obras/editarEtapa/' . $etapa->id); ?>" method="post">
            <div class="etapa-edit-form-row">
                <div class="etapa-edit-form-group">
                    <label class="etapa-edit-form-label" for="numero_etapa">
                        <i class="bx bx-sort-by-order"></i> Número da Etapa <span class="required">*</span>
                    </label>
                    <input type="number" name="numero_etapa" id="numero_etapa" class="etapa-edit-form-input" value="<?php echo $etapa->numero_etapa; ?>" min="1" required>
                    <div class="etapa-edit-form-hint">Ordem de execução desta etapa</div>
                </div>

                <div class="etapa-edit-form-group">
                    <label class="etapa-edit-form-label" for="nome">
                        <i class="bx bx-tag"></i> Nome da Etapa <span class="required">*</span>
                    </label>
                    <input type="text" name="nome" id="nome" class="etapa-edit-form-input" value="<?php echo htmlspecialchars($etapa->nome); ?>" maxlength="100" placeholder="Ex: Fundação, Estrutura, Acabamento..." required>
                </div>
            </div>

            <div class="etapa-edit-form-group">
                <label class="etapa-edit-form-label" for="especialidade">
                    <i class="bx bx-briefcase"></i> Especialidade
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
                    <i class="bx bx-align-left"></i> Descrição
                </label>
                <textarea name="descricao" id="descricao" class="etapa-edit-form-textarea" rows="3" placeholder="Descreva os detalhes desta etapa..."><?php echo htmlspecialchars($etapa->descricao ?? ''); ?></textarea>
            </div>

            <div class="etapa-edit-form-row">
                <div class="etapa-edit-form-group">
                    <label class="etapa-edit-form-label" for="data_inicio_prevista">
                        <i class="bx bx-calendar"></i> Data Início Prevista
                    </label>
                    <input type="date" name="data_inicio_prevista" id="data_inicio_prevista" class="etapa-edit-form-input" value="<?php echo $etapa->data_inicio_prevista; ?>">
                </div>

                <div class="etapa-edit-form-group">
                    <label class="etapa-edit-form-label" for="data_fim_prevista">
                        <i class="bx bx-calendar-check"></i> Data Término Prevista
                    </label>
                    <input type="date" name="data_fim_prevista" id="data_fim_prevista" class="etapa-edit-form-input" value="<?php echo $etapa->data_fim_prevista; ?>">
                </div>
            </div>

            <div class="etapa-edit-form-group">
                <label class="etapa-edit-form-label">
                    <i class="bx bx-info-circle"></i> Status da Etapa
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
                    <i class="bx bx-x"></i> Cancelar
                </a>
                <button type="submit" class="etapa-edit-btn-submit">
                    <i class="bx bx-save"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
