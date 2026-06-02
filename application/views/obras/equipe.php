<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.equipe-unified { padding: 20px; }

/* Header */
.equipe-header-card {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
}
.equipe-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.equipe-title-section h1 {
    margin: 0 0 5px 0;
    font-size: 32px;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.equipe-subtitle {
    opacity: 0.95;
    font-size: 16px;
    margin-bottom: 5px;
}
.equipe-subtitle a {
    color: white;
    text-decoration: none;
    font-weight: 500;
}
.equipe-subtitle a:hover {
    text-decoration: underline;
}
.equipe-stats-header {
    display: flex;
    gap: 20px;
}
.equipe-stat-header {
    background: rgba(255,255,255,0.25);
    padding: 20px 35px;
    border-radius: 15px;
    text-align: center;
    backdrop-filter: blur(10px);
}
.equipe-stat-number {
    font-size: 36px;
    font-weight: 700;
    line-height: 1;
}
.equipe-stat-label {
    font-size: 14px;
    font-weight: 500;
    opacity: 0.95;
    margin-top: 5px;
}

/* Filter Bar */
.filter-bar-equipe {
    background: white;
    border-radius: 15px;
    padding: 20px 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}
.filter-bar-equipe input, .filter-bar-equipe select {
    border-radius: 12px;
    border: 2px solid #e8e8e8;
    padding: 14px 18px;
    font-size: 15px;
    font-weight: 500;
    flex: 1;
    min-width: 200px;
    color: #333;
    background: #fafafa;
    transition: all 0.3s;
}
.filter-bar-equipe input:focus, .filter-bar-equipe select:focus {
    border-color: #11998e;
    background: white;
    outline: none;
    box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
}
.filter-bar-equipe input::placeholder {
    color: #999;
    font-weight: 400;
}
.btn-add-equipe {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}
.btn-add-equipe:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(17, 153, 142, 0.4);
}
.btn-add-equipe i {
    font-size: 18px;
}

/* Team Grid */
.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

/* Team Card */
.team-card {
    background: white;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.team-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.team-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #11998e, #38ef7d);
}
.team-card-header {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 22px;
}
.team-avatar {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}
.team-info { flex: 1; }
.team-name {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 6px;
    letter-spacing: -0.3px;
}
.team-role {
    display: inline-block;
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    color: #2e7d32;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}
.team-status {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #4caf50;
    box-shadow: 0 0 0 4px #e8f5e9;
    flex-shrink: 0;
}
.team-status.inativo {
    background: #9e9e9e;
    box-shadow: 0 0 0 4px #f5f5f5;
}

/* Team Details */
.team-details {
    background: linear-gradient(135deg, #f8f9fa, #f0f4f0);
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 20px;
}
.team-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    font-size: 15px;
}
.team-detail-row:last-child { margin-bottom: 0; }
.team-detail-label {
    color: #666;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}
.team-detail-label i {
    color: #11998e;
    font-size: 14px;
}
.team-detail-value {
    font-weight: 600;
    color: #1a1a1a;
    font-size: 15px;
}

/* Team Actions */
.team-actions {
    display: flex;
    gap: 12px;
}
.team-btn {
    flex: 1;
    padding: 12px;
    border-radius: 10px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.team-btn i {
    font-size: 16px;
}
.team-btn-danger {
    background: #fff5f5;
    color: #c53030;
    border: 2px solid #fed7d7;
}
.team-btn-danger:hover {
    background: #c53030;
    color: white;
    border-color: #c53030;
}

/* Add Team Card */
.add-team-card {
    background: linear-gradient(135deg, #f8faf9, #e8f5e9);
    border: 3px dashed #11998e;
    border-radius: 18px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 280px;
}
.add-team-card:hover {
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    border-style: solid;
    border-width: 3px;
}
.add-team-icon {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 45px;
    margin-bottom: 20px;
    transition: all 0.3s;
    box-shadow: 0 6px 25px rgba(17, 153, 142, 0.3);
}
.add-team-card:hover .add-team-icon {
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 8px 30px rgba(17, 153, 142, 0.4);
}
.add-team-text {
    font-size: 22px;
    font-weight: 700;
    color: #11998e;
    margin-bottom: 8px;
}
.add-team-subtext {
    font-size: 15px;
    color: #666;
    font-weight: 500;
}

/* Empty State */
.empty-state-equipe {
    text-align: center;
    padding: 80px 40px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.08);
}
.empty-state-equipe i {
    font-size: 100px;
    color: #ddd;
    margin-bottom: 30px;
}
.empty-state-equipe h3 {
    color: #333;
    font-size: 26px;
    margin-bottom: 15px;
    font-weight: 700;
}
.empty-state-equipe p {
    color: #666;
    font-size: 17px;
    margin-bottom: 30px;
    font-weight: 500;
}

/* ============ MODAL STYLES ============ */
.modal-equipe .modal-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 25px 30px;
    border: none;
    border-radius: 15px 15px 0 0;
}
.modal-equipe .modal-header h3 {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}
.modal-equipe .modal-header h3 i {
    font-size: 26px;
}
.modal-equipe .modal-header .close {
    color: white;
    opacity: 0.9;
    font-size: 32px;
    font-weight: 300;
    text-shadow: none;
    transition: all 0.3s;
}
.modal-equipe .modal-header .close:hover {
    opacity: 1;
    transform: scale(1.1);
}

.modal-equipe .modal-body {
    padding: 35px 30px;
    background: #fafafa;
}

/* Form Fields */
.form-group-equipe {
    margin-bottom: 28px;
}
.form-group-equipe:last-child {
    margin-bottom: 0;
}
.form-label-equipe {
    display: block;
    font-weight: 600;
    color: #1a1a1a;
    font-size: 16px;
    margin-bottom: 10px;
}
.form-label-equipe .required {
    color: #e53e3e;
    margin-left: 3px;
}
.form-select-equipe,
.form-input-equipe {
    width: 100%;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    padding: 16px 18px;
    font-size: 16px;
    font-weight: 500;
    color: #1a1a1a;
    background: white;
    transition: all 0.3s;
    box-sizing: border-box;
}
.form-select-equipe:focus,
.form-input-equipe:focus {
    border-color: #11998e;
    outline: none;
    box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
}
.form-select-equipe option {
    font-size: 15px;
    padding: 10px;
}
.form-select-equipe optgroup {
    font-weight: 600;
    color: #11998e;
}

/* Info Alert */
.alert-equipe {
    background: linear-gradient(135deg, #e6fffa, #ebf8ff);
    border: 2px solid #9ae6de;
    border-radius: 12px;
    padding: 20px;
    margin-top: 25px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}
.alert-equipe i {
    color: #11998e;
    font-size: 24px;
    flex-shrink: 0;
}
.alert-equipe-content {
    flex: 1;
}
.alert-equipe-title {
    font-weight: 700;
    color: #234e52;
    font-size: 16px;
    margin-bottom: 5px;
}
.alert-equipe-text {
    color: #2c7a7b;
    font-size: 15px;
    line-height: 1.5;
}

/* Modal Footer */
.modal-equipe .modal-footer {
    padding: 25px 30px;
    background: white;
    border-top: 1px solid #e8e8e8;
    border-radius: 0 0 15px 15px;
    display: flex;
    justify-content: flex-end;
    gap: 15px;
}
.btn-modal-cancel {
    padding: 14px 28px;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    background: white;
    color: #4a5568;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-modal-cancel:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
}
.btn-modal-submit {
    padding: 14px 32px;
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}
.btn-modal-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(17, 153, 142, 0.4);
}
.btn-modal-submit i {
    font-size: 18px;
}

/* Responsive */
@media (max-width: 768px) {
    .equipe-header-content { flex-direction: column; gap: 20px; }
    .equipe-stats-header { width: 100%; justify-content: center; }
    .filter-bar-equipe { flex-direction: column; }
    .filter-bar-equipe input, .filter-bar-equipe select { width: 100%; }
    .team-grid { grid-template-columns: 1fr; }
}

/* Badge for tecnicos already in team */
.tecnico-badge {
    display: inline-block;
    background: #fed7d7;
    color: #c53030;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 8px;
}
</style>

<div class="equipe-unified">
    <!-- Header -->
    <div class="equipe-header-card">
        <div class="equipe-header-content">
            <div class="equipe-title-section">
                <div class="equipe-subtitle">
                    <a href="<?php echo site_url('obras'); ?>"><i class="icon-arrow-left"></i> Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo $obra->nome; ?></a>
                </div>
                <h1><i class="icon-group"></i> Gerenciar Equipe</h1>
            </div>
            <div class="equipe-stats-header">
                <div class="equipe-stat-header">
                    <div class="equipe-stat-number"><?php echo count($equipe); ?></div>
                    <div class="equipe-stat-label">Membros na Equipe</div>
                </div>
                <div class="equipe-stat-header">
                    <div class="equipe-stat-number"><?php echo count($tecnicos) - count($equipe); ?></div>
                    <div class="equipe-stat-label">Técnicos Disponíveis</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar-equipe">
        <i class="icon-search" style="font-size: 22px; color: #11998e;"></i>
        <input type="text" id="searchEquipe" placeholder="Buscar técnico por nome..." onkeyup="filtrarEquipe()">

        <select id="filterFuncao" onchange="filtrarEquipe()">
            <option value="">Todas as Funções</option>
            <option value="Técnico">Técnico</option>
            <option value="Encarregado">Encarregado</option>
            <option value="Engenheiro">Engenheiro</option>
            <option value="Mestre de Obras">Mestre de Obras</option>
            <option value="Pedreiro">Pedreiro</option>
            <option value="Eletricista">Eletricista</option>
            <option value="Hidráulico">Hidráulico</option>
            <option value="Carpinteiro">Carpinteiro</option>
            <option value="Pintor">Pintor</option>
            <option value="Auxiliar">Auxiliar</option>
            <option value="Outro">Outro</option>
        </select>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
        <button class="btn-add-equipe" data-toggle="modal" data-target="#modalAdicionar">
            <i class="icon-plus"></i> Adicionar Técnico
        </button>
        <?php endif; ?>
    </div>

    <!-- Team Grid -->
    <div id="teamGrid">
        <?php if (count($equipe) > 0): ?>
            <div class="team-grid">
                <!-- Add New Card -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <div class="add-team-card" data-toggle="modal" data-target="#modalAdicionar">
                    <div class="add-team-icon">
                        <i class="icon-plus"></i>
                    </div>
                    <div class="add-team-text">Adicionar Técnico</div>
                    <div class="add-team-subtext">Clique para alocar novo membro à equipe</div>
                </div>
                <?php endif; ?>

                <!-- Team Members -->
                <?php foreach ($equipe as $membro): ?>
                <div class="team-card" data-nome="<?php echo strtolower($membro->tecnico_nome); ?>" data-funcao="<?php echo $membro->funcao; ?>">
                    <div class="team-card-header">
                        <div class="team-avatar">
                            <?php echo substr($membro->tecnico_nome, 0, 1); ?>
                        </div>
                        <div class="team-info">
                            <div class="team-name"><?php echo $membro->tecnico_nome; ?></div>
                            <div class="team-role"><?php echo $membro->funcao; ?></div>
                        </div>
                        <div class="team-status <?php echo $membro->ativo ? '' : 'inativo'; ?>" title="<?php echo $membro->ativo ? 'Ativo' : 'Inativo'; ?>"></div>
                    </div>

                    <div class="team-details">
                        <div class="team-detail-row">
                            <span class="team-detail-label"><i class="icon-calendar"></i> Data de Entrada</span>
                            <span class="team-detail-value"><?php echo date('d/m/Y', strtotime($membro->data_entrada)); ?></span>
                        </div>
                        <div class="team-detail-row">
                            <span class="team-detail-label"><i class="icon-refresh"></i> Status na Equipe</span>
                            <span class="team-detail-value" style="color: <?php echo $membro->ativo ? '#2e7d32' : '#666'; ?>;">
                                <?php echo $membro->ativo ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </div>
                        <?php if ($membro->nivel_tecnico): ?>
                        <div class="team-detail-row">
                            <span class="team-detail-label"><i class="icon-star"></i> Nível Técnico</span>
                            <span class="team-detail-value"><?php echo $membro->nivel_tecnico; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                    <div class="team-actions">
                        <a href="<?php echo site_url('obras/removerTecnico/' . $membro->id); ?>" class="team-btn team-btn-danger" onclick="return confirm('Tem certeza que deseja remover este técnico da equipe?')">
                            <i class="icon-remove"></i> Remover da Equipe
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state-equipe">
                <i class="icon-group"></i>
                <h3>Nenhum técnico alocado</h3>
                <p>Adicione técnicos à equipe desta obra para começar a registrar atividades.</p>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <button class="btn-add-equipe" data-toggle="modal" data-target="#modalAdicionar">
                    <i class="icon-plus"></i> Adicionar Primeiro Técnico
                </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Adicionar - Design Melhorado -->
<div id="modalAdicionar" class="modal hide fade modal-equipe" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="modalAdicionarLabel">
            <i class="icon-plus-sign"></i> Adicionar Técnico à Equipe
        </h3>
    </div>

    <form action="<?php echo site_url('obras/adicionarTecnico'); ?>" method="post">
        <div class="modal-body">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">

            <!-- Seleção de Técnico -->
            <div class="form-group-equipe">
                <label class="form-label-equipe" for="tecnico_id">
                    <i class="icon-user"></i> Selecione o Técnico <span class="required">*</span>
                </label>
                <select name="tecnico_id" id="tecnico_id" class="form-select-equipe" required>
                    <option value="" disabled selected>-- Escolha um técnico --</option>
                    <optgroup label="Técnicos Disponíveis">
                        <?php foreach ($tecnicos as $t): ?>
                            <?php
                            $ja_na_equipe = false;
                            foreach ($equipe as $membro) {
                                if ($membro->tecnico_id == $t->idUsuarios) {
                                    $ja_na_equipe = true;
                                    break;
                                }
                            }
                            ?>
                            <?php if (!$ja_na_equipe): ?>
                            <option value="<?php echo $t->idUsuarios; ?>">
                                <?php echo $t->nome; ?>
                                <?php if ($t->nivel_tecnico): ?> - <?php echo $t->nivel_tecnico; ?><?php endif; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>

            <!-- Seleção de Função -->
            <div class="form-group-equipe">
                <label class="form-label-equipe" for="funcao">
                    <i class="icon-briefcase"></i> Função na Obra <span class="required">*</span>
                </label>
                <select name="funcao" id="funcao" class="form-select-equipe" required>
                    <option value="" disabled selected>-- Selecione a função --</option>
                    <?php foreach ($funcoes_equipe as $f): ?>
                        <option value="<?php echo htmlspecialchars($f->nome); ?>"><?php echo htmlspecialchars($f->nome); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Alerta Informativo -->
            <div class="alert-equipe">
                <i class="icon-info-sign"></i>
                <div class="alert-equipe-content">
                    <div class="alert-equipe-title">Importante</div>
                    <div class="alert-equipe-text">
                        Os técnicos adicionados à equipe poderão registrar atividades, fazer check-ins e participar do acompanhamento desta obra. Certifique-se de selecionar a função correta para cada profissional.
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" data-dismiss="modal">
                <i class="icon-remove"></i> Cancelar
            </button>
            <button type="submit" class="btn-modal-submit">
                <i class="icon-plus icon-white"></i> Adicionar à Equipe
            </button>
        </div>
    </form>
</div>

<script>
function filtrarEquipe() {
    const search = document.getElementById('searchEquipe').value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    const funcao = document.getElementById('filterFuncao').value;
    const cards = document.querySelectorAll('.team-card');

    cards.forEach(card => {
        const nome = card.getAttribute('data-nome');
        const cardFuncao = card.getAttribute('data-funcao');

        const matchSearch = !search || nome.includes(search);
        const matchFuncao = !funcao || cardFuncao === funcao;

        card.style.display = matchSearch && matchFuncao ? 'block' : 'none';
    });

    // Check if no results
    const visibleCards = document.querySelectorAll('.team-card[style="display: block;"], .team-card:not([style*="display: none"])');
    const hasVisible = Array.from(cards).some(card => card.style.display !== 'none');
}

// Focus on select when modal opens
$('#modalAdicionar').on('shown.bs.modal', function () {
    $('#tecnico_id').focus();
});
</script>
