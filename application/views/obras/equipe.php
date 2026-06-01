<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<div class="equipe-unified">
    <!-- Header -->
    <div class="equipe-header-card">
        <div class="equipe-header-content">
            <div class="equipe-title-section">
                <div class="equipe-subtitle">
                    <a href="<?php echo site_url('obras'); ?>"><i class="bx bx-arrow-back"></i> Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo $obra->nome; ?></a>
                </div>
                <h1><i class="bx bx-group"></i> Gerenciar Equipe</h1>
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
        <i class="bx bx-search" style="font-size: 22px; color: #11998e;"></i>
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
        <button class="btn-add-equipe" data-bs-toggle="modal" data-bs-target="#modalAdicionar">
            <i class="bx bx-plus"></i> Adicionar Técnico
        </button>
        <?php endif; ?>
    </div>

    <!-- Team Grid -->
    <div id="teamGrid">
        <?php if (count($equipe) > 0): ?>
            <div class="team-grid">
                <!-- Add New Card -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <div class="add-team-card" data-bs-toggle="modal" data-bs-target="#modalAdicionar">
                    <div class="add-team-icon">
                        <i class="bx bx-plus"></i>
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
                            <span class="team-detail-label"><i class="bx bx-calendar"></i> Data de Entrada</span>
                            <span class="team-detail-value"><?php echo date('d/m/Y', strtotime($membro->data_entrada)); ?></span>
                        </div>
                        <div class="team-detail-row">
                            <span class="team-detail-label"><i class="bx bx-refresh"></i> Status na Equipe</span>
                            <span class="team-detail-value" style="color: <?php echo $membro->ativo ? '#2e7d32' : '#666'; ?>;">
                                <?php echo $membro->ativo ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </div>
                        <?php if ($membro->nivel_tecnico): ?>
                        <div class="team-detail-row">
                            <span class="team-detail-label"><i class="bx bx-star"></i> Nível Técnico</span>
                            <span class="team-detail-value"><?php echo $membro->nivel_tecnico; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                    <div class="team-actions">
                        <a href="<?php echo site_url('obras/removerTecnico/' . $membro->id); ?>" class="team-btn team-btn-danger" onclick="return confirm('Tem certeza que deseja remover este técnico da equipe?')">
                            <i class="bx bx-x"></i> Remover da Equipe
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state-equipe">
                <i class="bx bx-group"></i>
                <h3>Nenhum técnico alocado</h3>
                <p>Adicione técnicos à equipe desta obra para começar a registrar atividades.</p>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <button class="btn-add-equipe" data-bs-toggle="modal" data-bs-target="#modalAdicionar">
                    <i class="bx bx-plus"></i> Adicionar Primeiro Técnico
                </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Adicionar - Design Melhorado -->
<div id="modalAdicionar" class="modal fade modal-equipe" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="modalAdicionarLabel">
            <i class="bx bx-plus-circle"></i> Adicionar Técnico à Equipe
        </h3>
    </div>

    <form action="<?php echo site_url('obras/adicionarTecnico'); ?>" method="post">
        <div class="modal-body">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">

            <!-- Seleção de Técnico -->
            <div class="form-group-equipe">
                <label class="form-label-equipe" for="tecnico_id">
                    <i class="bx bx-user"></i> Selecione o Técnico <span class="required">*</span>
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
                    <i class="bx bx-briefcase"></i> Função na Obra <span class="required">*</span>
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
                <i class="bx bx-info-circle"></i>
                <div class="alert-equipe-content">
                    <div class="alert-equipe-title">Importante</div>
                    <div class="alert-equipe-text">
                        Os técnicos adicionados à equipe poderão registrar atividades, fazer check-ins e participar do acompanhamento desta obra. Certifique-se de selecionar a função correta para cada profissional.
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
                <i class="bx bx-x"></i> Cancelar
            </button>
            <button type="submit" class="btn-modal-submit">
                <i class="bx bx-plus text-white"></i> Adicionar à Equipe
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
