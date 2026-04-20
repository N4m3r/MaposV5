<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.equipe-unified { padding: 20px; }
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
    font-size: 28px;
    font-weight: 700;
}
.equipe-subtitle {
    opacity: 0.9;
    font-size: 16px;
}
.equipe-stats-header {
    display: flex;
    gap: 30px;
    text-align: center;
}
.equipe-stat-header {
    background: rgba(255,255,255,0.2);
    padding: 15px 30px;
    border-radius: 15px;
}
.equipe-stat-number {
    font-size: 32px;
    font-weight: 700;
}
.equipe-stat-label {
    font-size: 13px;
    opacity: 0.9;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}
.team-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.team-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
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
    gap: 15px;
    margin-bottom: 20px;
}
.team-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
}
.team-info { flex: 1; }
.team-name {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}
.team-role {
    display: inline-block;
    background: #e8f5e9;
    color: #11998e;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}
.team-status {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #4caf50;
    box-shadow: 0 0 0 3px #e8f5e9;
}
.team-status.inativo {
    background: #ccc;
    box-shadow: 0 0 0 3px #f0f0f0;
}
.team-details {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}
.team-detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
}
.team-detail-row:last-child { margin-bottom: 0; }
.team-detail-label { color: #888; }
.team-detail-value { font-weight: 600; color: #333; }
.team-actions {
    display: flex;
    gap: 10px;
}
.team-btn {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    text-decoration: none;
}
.team-btn-danger {
    background: #ffebee;
    color: #c62828;
}
.team-btn-danger:hover {
    background: #c62828;
    color: white;
}

.add-team-card {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: 2px dashed #11998e;
    border-radius: 15px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 250px;
}
.add-team-card:hover {
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    border-style: solid;
}
.add-team-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #11998e;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    margin-bottom: 15px;
    transition: all 0.3s;
}
.add-team-card:hover .add-team-icon {
    transform: scale(1.1);
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
}
.add-team-text {
    font-size: 18px;
    font-weight: 600;
    color: #11998e;
}
.add-team-subtext {
    font-size: 14px;
    color: #888;
    margin-top: 5px;
}

.filter-bar-equipe {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}
.filter-bar-equipe input, .filter-bar-equipe select {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 12px 15px;
    font-size: 14px;
    flex: 1;
    min-width: 200px;
}
.btn-add-equipe {
    background: #11998e;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-add-equipe:hover {
    background: #0d8379;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
}

.empty-state-equipe {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
}
.empty-state-equipe i {
    font-size: 80px;
    color: #ddd;
    margin-bottom: 20px;
}
.empty-state-equipe h3 { color: #666; margin-bottom: 10px; }
.empty-state-equipe p { color: #888; margin-bottom: 25px; }

@media (max-width: 768px) {
    .equipe-header-content { flex-direction: column; gap: 20px; }
    .equipe-stats-header { width: 100%; justify-content: space-around; }
}
</style>

<div class="equipe-unified">
    <!-- Header -->
    <div class="equipe-header-card">
        <div class="equipe-header-content">
            <div class="equipe-title-section">
                <div class="equipe-subtitle">
                    <a href="<?php echo site_url('obras'); ?>" style="color: white; opacity: 0.8;"><i class="icon-arrow-left"></i> Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" style="color: white; opacity: 0.8;"><?php echo $obra->nome; ?></a>
                </div>
                <h1><i class="icon-group"></i> Gerenciar Equipe</h1>
            </div>
            <div class="equipe-stats-header">
                <div class="equipe-stat-header">
                    <div class="equipe-stat-number"><?php echo count($equipe); ?></div>
                    <div class="equipe-stat-label">Membros</div>
                </div>
                <div class="equipe-stat-header">
                    <div class="equipe-stat-number"><?php echo count($tecnicos); ?></div>
                    <div class="equipe-stat-label">Disponíveis</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar-equipe">
        <i class="icon-search" style="font-size: 20px; color: #11998e;"></i>
        <input type="text" id="searchEquipe" placeholder="Buscar técnico..." onkeyup="filtrarEquipe()">

        <select id="filterFuncao" onchange="filtrarEquipe()">
            <option value="">Todas as Funções</option>
            <option value="Técnico">Técnico</option>
            <option value="Encarregado">Encarregado</option>
            <option value="Engenheiro">Engenheiro</option>
            <option value="Mestre de Obras">Mestre de Obras</option>
        </select>

        <button class="btn-add-equipe" data-toggle="modal" data-target="#modalAdicionar">
            <i class="icon-plus"></i> Adicionar Técnico
        </button>
    </div>

    <!-- Team Grid -->
    <div id="teamGrid">
        <?php if (count($equipe) > 0): ?>
            <div class="team-grid">
                <!-- Add New Card -->
                <div class="add-team-card" data-toggle="modal" data-target="#modalAdicionar">
                    <div class="add-team-icon">
                        <i class="icon-plus"></i>
                    </div>
                    <div class="add-team-text">Adicionar Técnico</div>
                    <div class="add-team-subtext">Clique para alocar novo membro</div>
                </div>

                <!-- Team Members -->
                <?php foreach ($equipe as $membro): ?
003e
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
                            <span class="team-detail-label">Data de Entrada</span>
                            <span class="team-detail-value"><?php echo date('d/m/Y', strtotime($membro->data_entrada)); ?></span>
                        </div>
                        <div class="team-detail-row">
                            <span class="team-detail-label">Status</span>
                            <span class="team-detail-value" style="color: <?php echo $membro->ativo ? '#11998e' : '#888'; ?>;">
                                <i class="icon-circle"></i> <?php echo $membro->ativo ? 'Ativo na equipe' : 'Inativo'; ?>
                            </span>
                        </div>
                        <?php if ($membro->nivel_tecnico): ?>
                        <div class="team-detail-row">
                            <span class="team-detail-label">Nível</span>
                            <span class="team-detail-value"><?php echo $membro->nivel_tecnico; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="team-actions">
                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?
003e
                        <a href="<?php echo site_url('obras/removerTecnico/' . $membro->id); ?>" class="team-btn team-btn-danger" onclick="return confirm('Tem certeza que deseja remover este técnico da equipe?')">
                            <i class="icon-remove"></i> Remover da Equipe
                        </a>
                        <?php endif; ?
003e
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?
003e
            <div class="empty-state-equipe">
                <i class="icon-group"></i>
                <h3>Nenhum técnico alocado</h3>
                <p>Adicione técnicos à equipe desta obra para começar.</p>
                <button class="btn-add-equipe" data-toggle="modal" data-target="#modalAdicionar">
                    <i class="icon-plus"></i> Adicionar Primeiro Técnico
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Adicionar -->
<div id="modalAdicionar" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header" style="background: #11998e; color: white;">
        <button type="button" class="close" data-dismiss="modal" style="color: white;">×</button>
        <h3><i class="icon-plus-sign"></i> Adicionar Técnico à Equipe</h3>
    </div>
    <form action="<?php echo site_url('obras/adicionarTecnico'); ?>" method="post">
        <div class="modal-body" style="padding: 30px;">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">

            <div class="control-group">
                <label style="font-weight: 600; color: #333;">Técnico *</label>
                <select name="tecnico_id" class="span12" required style="border-radius: 10px; padding: 12px;">
                    <option value="">Selecione um técnico...</option>
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
                        <?php if (!$ja_na_equipe): ?
003e
                        <option value="<?php echo $t->idUsuarios; ?>">
                            <?php echo $t->nome; ?>
                            <?php if ($t->nivel_tecnico): ?> - <?php echo $t->nivel_tecnico; ?><?php endif; ?>
                        </option>
                        <?php endif; ?
003e
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="control-group" style="margin-top: 20px;">
                <label style="font-weight: 600; color: #333;">Função *</label>
                <select name="funcao" class="span12" required style="border-radius: 10px; padding: 12px;">
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
            </div>

            <div class="alert alert-info" style="margin-top: 20px; border-radius: 10px;">
                <i class="icon-info-sign"></i>
                Os técnicos adicionados à equipe poderão registrar atividades e check-ins nesta obra.
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-large" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success btn-large">
                <i class="icon-plus icon-white"></i> Adicionar à Equipe
            </button>
        </div>
    </form>
</div>

<script>
function filtrarEquipe() {
    const search = document.getElementById('searchEquipe').value.toLowerCase();
    const funcao = document.getElementById('filterFuncao').value;
    const cards = document.querySelectorAll('.team-card');

    cards.forEach(card => {
        const nome = card.getAttribute('data-nome');
        const cardFuncao = card.getAttribute('data-funcao');

        const matchSearch = !search || nome.includes(search);
        const matchFuncao = !funcao || cardFuncao === funcao;

        card.style.display = matchSearch && matchFuncao ? 'block' : 'none';
    });
}
</script>
