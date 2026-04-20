<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Tema Moderno Obras -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
    .obra-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-radius: 12px;
        padding: 24px;
        color: white;
        margin-bottom: 24px;
    }
    .obra-header h2 {
        margin: 0 0 8px 0;
        font-size: 24px;
    }
    .obra-header p {
        margin: 0;
        opacity: 0.9;
    }
    .obra-status {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 12px;
    }

    .progress-section {
        margin: 20px 0;
    }
    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .progress-bar {
        height: 10px;
        background: rgba(255,255,255,0.2);
        border-radius: 5px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: white;
        border-radius: 5px;
        transition: width 0.5s ease;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 16px;
        color: #333;
    }

    .etapa-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
        border-left: 4px solid #ddd;
    }
    .etapa-item.pendente { border-left-color: #f39c12; }
    .etapa-item.em-andamento { border-left-color: #3498db; }
    .etapa-item.concluida { border-left-color: #27ae60; }

    .etapa-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .etapa-nome {
        font-weight: 600;
        font-size: 15px;
    }
    .etapa-status {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .etapa-status.pendente { background: #fff3cd; color: #856404; }
    .etapa-status.em-andamento { background: #d1ecf1; color: #0c5460; }
    .etapa-status.concluida { background: #d4edda; color: #155724; }

    .btn-acao {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-acao:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }
    .btn-primary-tec {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .btn-primary-tec:hover {
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    .btn-success-tec {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    .btn-success-tec:hover {
        color: white;
        box-shadow: 0 4px 12px rgba(17, 153, 142, 0.4);
    }

    .os-lista {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .os-item {
        display: flex;
        align-items: center;
        padding: 14px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .os-numero {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 14px;
    }
    .os-info {
        flex: 1;
    }
    .os-cliente {
        font-weight: 600;
    }
    .os-data {
        font-size: 13px;
        color: #888;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #888;
    }
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    /* Formulario de atividade */
    .form-atividade {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        border-color: #11998e;
        outline: none;
    }
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    .btn-submit {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
    }
    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Alertas */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        display: none;
    }
    .alert.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .alert.show {
        display: block;
    }

    /* Loading */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 8px;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Dark mode support */
    body[data-theme="dark"] .section-title { color: #e8e8e8; }
    body[data-theme="dark"] .etapa-item { background: #252a3a; }
    body[data-theme="dark"] .etapa-nome { color: #e8e8e8; }
    body[data-theme="dark"] .os-item { background: #252a3a; }
    body[data-theme="dark"] .os-cliente { color: #e8e8e8; }
    body[data-theme="dark"] .os-data { color: #888; }
    body[data-theme="dark"] .form-atividade { background: #252a3a; }
    body[data-theme="dark"] .form-group label { color: #e8e8e8; }
    body[data-theme="dark"] .form-group input,
    body[data-theme="dark"] .form-group textarea,
    body[data-theme="dark"] .form-group select {
        background: #1a1d29;
        border-color: #3a3f4f;
        color: #e8e8e8;
    }
</style>

<!-- Header da Obra -->
<div class="obra-header">
    <h2><i class='bx bx-building'></i> <?= htmlspecialchars($obra->nome ?? 'Obra') ?></h2>
    <p><i class='bx bx-user'></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Cliente nao informado') ?></p>
    <span class="obra-status"><?= $obra->status ?? 'N/A' ?></span>

    <div class="progress-section">
        <div class="progress-header">
            <span>Progresso</span>
            <span><?= $obra->percentual_concluido ?? 0 ?>%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $obra->percentual_concluido ?? 0 ?>%"></div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">

    <!-- Coluna Esquerda: Etapas e Atividades -->
    <div>
        <h3 class="section-title"><i class='bx bx-list-check'></i> Etapas da Obra</h3>

        <?php if (!empty($etapas)): ?>
            <?php foreach ($etapas as $etapa): ?>
                <?php
                $statusClass = strtolower(str_replace(' ', '-', $etapa->status));
                $statusLabel = $etapa->status;
                ?>
                <div class="etapa-item <?= $statusClass ?>">
                    <div class="etapa-header">
                        <span class="etapa-nome"><?= htmlspecialchars($etapa->nome) ?></span>
                        <span class="etapa-status <?= $statusClass ?>"><?= $statusLabel ?></span>
                    </div>
                    <?php if ($etapa->descricao): ?>
                        <p style="margin: 8px 0 0 0; font-size: 13px; color: #666;">
                            <?= htmlspecialchars($etapa->descricao) ?>
                        </p>
                    <?php endif; ?>
                    <?php if (($etapa->percentual_concluido ?? 0) > 0): ?>
                        <div style="margin-top: 10px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                                <span>Progresso</span>
                                <span><?= $etapa->percentual_concluido ?? 0 ?>%</span>
                            </div>
                            <div style="height: 6px; background: #e0e0e0; border-radius: 3px;">
                                <div style="width: <?= $etapa->percentual_concluido ?? 0 ?>%; height: 100%; background: linear-gradient(90deg, #667eea, #764ba2); border-radius: 3px;"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class='bx bx-list-ul'></i>
                <p>Nenhuma etapa cadastrada</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Coluna Direita: Minhas OS e Registrar Atividade -->
    <div>
        <h3 class="section-title"><i class='bx bx-clipboard'></i> Minhas OS nesta Obra</h3>

        <?php if (!empty($minhas_os)): ?>
            <div class="os-lista">
                <?php foreach ($minhas_os as $os): ?>
                    <div class="os-item">
                        <div class="os-numero">#<?= $os->idOs ?></div>
                        <div class="os-info">
                            <div class="os-cliente"><?= htmlspecialchars($os->nomeCliente) ?></div>
                            <div class="os-data">
                                <?= date('d/m/Y', strtotime($os->dataInicial)) ?> • <?= $os->status ?>
                            </div>
                        </div>
                        <a href="<?= site_url('tecnicos/executar_os/' . $os->idOs) ?>" class="btn-acao btn-primary-tec">
                            <i class='bx bx-play'></i> Executar
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class='bx bx-clipboard'></i>
                <p>Voce nao tem OS nesta obra</p>
            </div>
        <?php endif; ?>

        <!-- Formulario de Registrar Atividade -->
        <div class="form-atividade">
            <h4 class="section-title"><i class='bx bx-plus-circle'></i> Registrar Nova Atividade</h4>

            <div id="alertSuccess" class="alert success">
                <i class='bx bx-check-circle'></i> Atividade registrada com sucesso!
            </div>
            <div id="alertError" class="alert error">
                <i class='bx bx-error-circle'></i> <span id="errorMessage">Erro ao registrar atividade.</span>
            </div>

            <form id="formAtividade">
                <input type="hidden" name="obra_id" value="<?= $obra->id ?>">

                <div class="form-group">
                    <label for="etapa_id">Etapa (opcional)</label>
                    <select name="etapa_id" id="etapa_id">
                        <option value="">-- Selecione uma etapa --</option>
                        <?php if (!empty($etapas)): foreach ($etapas as $etapa): ?>
                            <option value="<?= $etapa->id ?>"><?= htmlspecialchars($etapa->nome) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descricao">Descricao da Atividade *</label>
                    <textarea name="descricao" id="descricao" placeholder="Descreva o que foi realizado..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo de Atividade</label>
                    <select name="tipo" id="tipo">
                        <option value="execucao">Execucao</option>
                        <option value="problema">Problema/Impedimento</option>
                        <option value="observacao">Observacao</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="percentual_concluido">Percentual Concluido (%)</label>
                    <input type="number" name="percentual_concluido" id="percentual_concluido" min="0" max="100" value="0">
                </div>

                <button type="submit" class="btn-submit" id="btnSubmit">
                    <i class='bx bx-save'></i> Salvar Atividade
                </button>
            </form>
        </div>
    </div>

</div>

<script>
// Animar barra de progresso ao carregar
document.addEventListener('DOMContentLoaded', function() {
    const progressFill = document.querySelector('.progress-fill');
    if (progressFill) {
        const width = progressFill.style.width;
        progressFill.style.width = '0%';
        setTimeout(() => {
            progressFill.style.width = width;
        }, 300);
    }

    // Submissao do formulario
    const form = document.getElementById('formAtividade');
    const btnSubmit = document.getElementById('btnSubmit');
    const alertSuccess = document.getElementById('alertSuccess');
    const alertError = document.getElementById('alertError');
    const errorMessage = document.getElementById('errorMessage');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Esconder alertas
        alertSuccess.classList.remove('show');
        alertError.classList.remove('show');

        // Desabilitar botao
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="loading"></span> Salvando...';

        // Coletar dados
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Enviar AJAX
        fetch('<?= site_url("tecnicos/api_registrar_atividade_obra") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alertSuccess.classList.add('show');
                form.reset();
                // Recarregar pagina apos 2 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                errorMessage.textContent = result.message || 'Erro ao registrar atividade.';
                alertError.classList.add('show');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorMessage.textContent = 'Erro de conexao. Tente novamente.';
            alertError.classList.add('show');
        })
        .finally(() => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class=\'bx bx-save\'></i> Salvar Atividade';
        });
    });
});
</script>
