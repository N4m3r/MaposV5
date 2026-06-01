<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

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
                <h1><i class="bx bx-edit"></i> Editar Atividade</h1>
            </div>
        </div>
    </div>

    <!-- Mensagens -->
    <?php if ($this->session->flashdata('success')): ?>
    <div style="background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="bx bx-check" style="font-size: 20px;"></i>
        <strong><?php echo $this->session->flashdata('success'); ?></strong>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="bx bx-x" style="font-size: 20px;"></i>
        <strong><?php echo $this->session->flashdata('error'); ?></strong>
    </div>
    <?php endif; ?>

    <form method="post" action="" id="formEditarAtividade">
        <!-- Dados da Atividade -->
        <div class="edit-card">
            <div class="edit-card-header">
                <div class="edit-card-icon"><i class="bx bx-file-alt"></i></div>
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
                <i class="bx bx-save"></i> Salvar Alterações
            </button>

            <a href="<?php echo site_url('obras/visualizarAtividade/' . $atividade->id); ?>" class="form-btn form-btn-secondary" style="text-decoration: none;">
                <i class="bx bx-x"></i> Cancelar
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
