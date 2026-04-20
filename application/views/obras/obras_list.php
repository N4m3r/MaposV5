<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Tema Moderno Obras -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
.obras-dashboard { padding: 20px; }
.stats-cards { display: flex; gap: 20px; margin-bottom: 25px; flex-wrap: wrap; }
.stat-card {
    flex: 1;
    min-width: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}
.stat-card:hover { transform: translateY(-3px); }
.stat-card.success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stat-card.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card.info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-number { font-size: 36px; font-weight: bold; margin-bottom: 5px; }
.stat-label { font-size: 14px; opacity: 0.9; }
.stat-icon { float: right; font-size: 40px; opacity: 0.3; margin-top: -10px; }

.filter-bar {
    background: var(--widget-box, #f8f9fa);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
    border: 1px solid rgba(0,0,0,0.05);
}
.filter-bar select, .filter-bar input {
    border-radius: 8px;
    border: 2px solid rgba(0,0,0,0.1);
    padding: 10px 15px;
    font-size: 14px;
    background: var(--widget-box, #fff);
    color: var(--title, #333);
}
.filter-bar .btn-filter {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}
.filter-bar .btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.obras-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}
.obra-card {
    background: var(--widget-box, white);
    border-radius: 15px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}
.obra-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.obra-header {
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
}
.obra-header.andamento { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.obra-header.concluida { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.obra-header.paralisada { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.obra-header.prospeccao { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
.obra-status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.25);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
}
.obra-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 5px;
    line-height: 1.3;
}
.obra-cliente {
    font-size: 14px;
    opacity: 0.9;
}
.obra-body { padding: 20px; }
.obra-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.obra-info-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.obra-info-label { color: var(--subtitle, #888); font-size: 13px; }
.obra-info-value { font-weight: 600; color: var(--title, #333); font-size: 14px; }
.obra-progress-section { margin: 20px 0; }
.obra-progress-bar {
    height: 10px;
    background: rgba(0,0,0,0.08);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 8px;
}
.obra-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    transition: width 0.5s ease;
}
.obra-progress-text {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: var(--subtitle, #666);
}
.obra-stats {
    display: flex;
    gap: 15px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(0,0,0,0.05);
}
.obra-stat {
    flex: 1;
    text-align: center;
}
.obra-stat-number {
    font-size: 22px;
    font-weight: 700;
    color: #667eea;
}
.obra-stat-label { font-size: 11px; color: var(--subtitle, #888); text-transform: uppercase; }
.obra-actions {
    display: flex;
    gap: 8px;
    padding: 15px 20px;
    background: rgba(0,0,0,0.02);
    border-top: 1px solid rgba(0,0,0,0.05);
    flex-wrap: wrap;
}
.obra-btn-action {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none;
    color: white;
}
.obra-btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: white;
    text-decoration: none;
}
.obra-btn-view { background: #667eea; }
.obra-btn-etapas { background: #9b59b6; }
.obra-btn-relatorio { background: #f39c12; }
.obra-btn-edit { background: #f093fb; color: #333; }
.obra-btn-delete { background: #f5576c; }

.empty-state-modern {
    text-align: center;
    padding: 60px 20px;
    background: var(--widget-box, white);
    border-radius: 15px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
}
.empty-state-icon {
    font-size: 80px;
    color: var(--subtitle, #ddd);
    margin-bottom: 20px;
}
.empty-state h3 { color: var(--title, #666); margin-bottom: 10px; }
.empty-state p { color: var(--subtitle, #999); margin-bottom: 25px; }

.view-toggle {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
.view-toggle-btn {
    padding: 10px 20px;
    border-radius: 8px;
    border: 2px solid #667eea;
    background: transparent;
    color: #667eea;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}
.view-toggle-btn.active {
    background: #667eea;
    color: white;
}

.obras-table-view { display: none; }
.obras-table-view.active { display: block; }
.obras-grid-view.active { display: grid; }

.quick-actions {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
}
.quick-actions-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-size: 24px;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.quick-actions-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 30px rgba(102, 126, 234, 0.5);
}

/* Dark theme compatibility */
body[data-theme="dark"] .filter-bar,
body[data-theme="dark"] .obra-card,
body[data-theme="dark"] .empty-state-modern {
    background: var(--dark-2, #272835);
    border-color: rgba(255,255,255,0.05);
}

body[data-theme="dark"] .filter-bar input,
body[data-theme="dark"] .filter-bar select {
    background: var(--dark-1, #14141a);
    border-color: rgba(255,255,255,0.1);
    color: var(--branco, #caced8);
}

body[data-theme="dark"] .obra-info-value {
    color: var(--branco, #caced8);
}

body[data-theme="dark"] .obra-progress-bar {
    background: rgba(255,255,255,0.1);
}
</style>

<div class="obras-modern-container obras-dashboard">
    <!-- Header com Ações Principais -->
    <div class="obra-card-modern animacao-entrada">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h2 style="margin: 0; font-size: 28px; color: inherit;">
                    <i class="icon-building" style="color: #667eea;"></i>
                    Gestão de Obras
                </h2>
                <p style="margin: 5px 0 0 0; opacity: 0.8;">Controle e acompanhamento de todas as obras</p>
            </div>
            <div class="acoes-rapidas" style="margin: 0;">
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')): ?>
                    <a href="<?php echo site_url('obras/adicionar'); ?>" class="btn-moderno btn-moderno-primary">
                        <i class="icon-plus"></i> Nova Obra
                    </a>
                <?php endif; ?>
                <a href="<?php echo site_url('obras/relatorios'); ?>" class="btn-moderno btn-moderno-warning">
                    <i class="icon-file-alt"></i> Relatórios
                </a>
                <a href="<?php echo site_url('tecnicos_admin'); ?>" class="acao-rapida-btn">
                    <i class="icon-group"></i> Equipe
                </a>
                <a href="<?php echo site_url('clientes'); ?>" class="acao-rapida-btn">
                    <i class="icon-user"></i> Clientes
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <i class="icon-building stat-icon"></i>
            <div class="stat-number"><?php echo count($results); ?></div>
            <div class="stat-label">Total de Obras</div>
        </div>
        <div class="stat-card info">
            <i class="icon-refresh stat-icon"></i>
            <div class="stat-number">
                <?php
                $em_andamento = array_filter($results ?? [], function($o) { return $o->status == 'Em Andamento'; });
                echo count($em_andamento);
                ?>
            </div>
            <div class="stat-label">Em Andamento</div>
        </div>
        <div class="stat-card success">
            <i class="icon-check stat-icon"></i>
            <div class="stat-number">
                <?php
                $concluidas = array_filter($results ?? [], function($o) { return in_array($o->status, ['Concluida', 'Concluída']); });
                echo count($concluidas);
                ?>
            </div>
            <div class="stat-label">Concluídas</div>
        </div>
        <div class="stat-card warning">
            <i class="icon-group stat-icon"></i>
            <div class="stat-number">
                <?php
                $total_progresso = 0;
                $count = count($results ?? []);
                foreach ($results ?? [] as $r) {
                    $total_progresso += ($r->percentual_concluido ?? 0);
                }
                echo $count > 0 ? round($total_progresso / $count) : 0;
                ?>%
            </div>
            <div class="stat-label">Progresso Médio</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <i class="icon-filter" style="font-size: 20px; color: #667eea;"></i>
        <select name="status" id="filterStatus" onchange="aplicarFiltros()">
            <option value="">Todos os Status</option>
            <option value="Prospeccao" <?php echo $this->input->get('status') == 'Prospeccao' ? 'selected' : ''; ?>>Prospecção</option>
            <option value="Em Andamento" <?php echo $this->input->get('status') == 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
            <option value="Paralisada" <?php echo $this->input->get('status') == 'Paralisada' ? 'selected' : ''; ?>>Paralisada</option>
            <option value="Concluida" <?php echo in_array($this->input->get('status'), ['Concluida', 'Concluída']) ? 'selected' : ''; ?>>Concluída</option>
        </select>
        <input type="text" id="filterCliente" placeholder="Buscar por cliente..." value="<?php echo $this->input->get('cliente'); ?>">
        <input type="text" id="filterObra" placeholder="Buscar por nome da obra..." value="<?php echo $this->input->get('obra'); ?>">
        <button class="btn-filter" onclick="aplicarFiltros()">
            <i class="icon-search"></i> Buscar
        </button>
        <?php if ($this->input->get()): ?>
            <a href="<?php echo site_url('obras'); ?>" class="btn" style="margin-left: auto;">
                <i class="icon-refresh"></i> Limpar Filtros
            </a>
        <?php endif; ?>
    </div>

    <!-- View Toggle -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: inherit;">
            <i class="icon-building" style="color: #667eea;"></i>
            Obras Cadastradas
        </h3>
        <div class="view-toggle">
            <button class="view-toggle-btn active" onclick="toggleView('grid')">
                <i class="icon-th-large"></i> Cards
            </button>
            <button class="view-toggle-btn" onclick="toggleView('table')">
                <i class="icon-list"></i> Lista
            </button>
        </div>
    </div>

    <!-- Grid View -->
    <div id="gridView" class="obras-grid-view active">
        <?php if (isset($results) && count($results) > 0): ?>
            <div class="obras-grid-modern">
                <?php foreach ($results as $r): ?>
                    <?php
                    $headerClass = 'prospeccao';
                    if ($r->status == 'Em Andamento') $headerClass = 'andamento';
                    elseif (in_array($r->status, ['Concluida', 'Concluída'])) $headerClass = 'concluida';
                    elseif ($r->status == 'Paralisada') $headerClass = 'paralisada';
                    ?>
                    <div class="obra-card animacao-entrada">
                        <div class="obra-header <?php echo $headerClass; ?>">
                            <span class="obra-status-badge"><?php echo $r->status; ?></span>
                            <div class="obra-title"><?php echo htmlspecialchars($r->nome); ?></div>
                            <div class="obra-cliente">
                                <i class="icon-user"></i> <?php echo htmlspecialchars($r->cliente_nome ?? 'Cliente não definido'); ?>
                            </div>
                        </div>
                        <div class="obra-body">
                            <div class="obra-info-row">
                                <div>
                                    <div class="obra-info-label">Previsão de Término</div>
                                    <div class="obra-info-value">
                                        <i class="icon-calendar"></i>
                                        <?php echo $r->data_fim_prevista ? date('d/m/Y', strtotime($r->data_fim_prevista)) : 'Não definida'; ?>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div class="obra-info-label">Valor do Contrato</div>
                                    <div class="obra-info-value" style="color: #667eea; font-size: 18px;">
                                        R$ <?php echo number_format($r->valor_contrato ?? 0, 2, ',', '.'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="obra-progress-section">
                                <div class="obra-progress-text">
                                    <span>Progresso</span>
                                    <span><?php echo $r->percentual_concluido ?? 0; ?>%</span>
                                </div>
                                <div class="obra-progress-bar">
                                    <div class="obra-progress-fill" style="width: <?php echo $r->percentual_concluido ?? 0; ?>%;"></div>
                                </div>
                            </div>

                            <div class="obra-stats">
                                <div class="obra-stat">
                                    <div class="obra-stat-number">
                                        <?php echo $r->total_etapas ?? 0; ?>
                                    </div>
                                    <div class="obra-stat-label">Etapas</div>
                                </div>
                                <div class="obra-stat">
                                    <div class="obra-stat-number">
                                        <?php echo $r->total_equipe ?? 0; ?>
                                    </div>
                                    <div class="obra-stat-label">Equipe</div>
                                </div>
                                <div class="obra-stat">
                                    <div class="obra-stat-number">
                                        <?php
                                        if ($r->data_fim_prevista) {
                                            $hoje = new DateTime();
                                            $previsto = new DateTime($r->data_fim_prevista);
                                            $dias = $hoje->diff($previsto)->format('%r%a');
                                            echo $dias > 0 ? $dias : 0;
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </div>
                                    <div class="obra-stat-label">Dias Restantes</div>
                                </div>
                            </div>
                        </div>
                        <div class="obra-actions">
                            <a href="<?php echo site_url('obras/visualizar/' . $r->id); ?>" class="obra-btn-action obra-btn-view" title="Visualizar">
                                <i class="icon-eye-open"></i>
                            </a>
                            <a href="<?php echo site_url('obras/etapas/' . $r->id); ?>" class="obra-btn-action obra-btn-etapas" title="Etapas">
                                <i class="icon-tasks"></i>
                            </a>
                            <a href="<?php echo site_url('obras/relatorioProgresso/' . $r->id); ?>" class="obra-btn-action obra-btn-relatorio" title="Relatório">
                                <i class="icon-file-alt"></i>
                            </a>
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                                <a href="<?php echo site_url('obras/editar/' . $r->id); ?>" class="obra-btn-action obra-btn-edit" title="Editar">
                                    <i class="icon-edit"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                                <button onclick="confirmarExclusao(<?php echo $r->id; ?>)" class="obra-btn-action obra-btn-delete" title="Excluir">
                                    <i class="icon-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state-modern">
                <div class="empty-state-icon">
                    <i class="icon-building"></i>
                </div>
                <h3>Nenhuma obra encontrada</h3>
                <p>Comece cadastrando sua primeira obra no sistema.</p>
                <a href="<?php echo site_url('obras/adicionar'); ?>" class="btn-moderno btn-moderno-primary" style="display: inline-flex; padding: 15px 30px;">
                    <i class="icon-plus"></i> Nova Obra
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Table View (Alternative) -->
    <div id="tableView" class="obras-table-view">
        <div class="obra-card-modern" style="padding: 0; overflow: hidden;">
            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped" style="margin: 0;">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th>Obra</th>
                            <th>Cliente</th>
                            <th>Status</th>
                            <th>Progresso</th>
                            <th>Previsão</th>
                            <th>Valor</th>
                            <th style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($results) && count($results) > 0): ?>
                            <?php foreach ($results as $r): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($r->nome); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($r->cliente_nome ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="label" style="font-size: 12px; padding: 6px 12px; background:
                                            <?php
                                            echo match($r->status) {
                                                'Em Andamento' => '#3498db',
                                                'Concluida', 'Concluída' => '#27ae60',
                                                'Paralisada' => '#e74c3c',
                                                'Prospeccao' => '#95a5a6',
                                                default => '#667eea'
                                            };
                                            ?>; color: white;">
                                            <?php echo $r->status; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="margin: 0; height: 8px; background: rgba(0,0,0,0.1);">
                                            <div class="bar" style="width: <?php echo $r->percentual_concluido ?? 0; ?>%; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
                                        </div>
                                        <small><?php echo $r->percentual_concluido ?? 0; ?>%</small>
                                    </td>
                                    <td><?php echo $r->data_fim_prevista ? date('d/m/Y', strtotime($r->data_fim_prevista)) : 'N/A'; ?></td>
                                    <td>R$ <?php echo number_format($r->valor_contrato ?? 0, 2, ',', '.'); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo site_url('obras/visualizar/' . $r->id); ?>" class="btn btn-mini btn-info" title="Visualizar">
                                                <i class="icon-eye-open icon-white"></i>
                                            </a>
                                            <a href="<?php echo site_url('obras/equipe/' . $r->id); ?>" class="btn btn-mini btn-success" title="Equipe">
                                                <i class="icon-group icon-white"></i>
                                            </a>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                                                <a href="<?php echo site_url('obras/editar/' . $r->id); ?>" class="btn btn-mini btn-primary" title="Editar">
                                                    <i class="icon-edit icon-white"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 40px;">
                                    Nenhuma obra encontrada.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (isset($results) && count($results) > 0): ?>
        <div style="margin-top: 20px; text-align: center;">
            <?php echo $this->pagination->create_links(); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Actions Button -->
<div class="quick-actions">
    <a href="<?php echo site_url('obras/adicionar'); ?>" class="quick-actions-btn" title="Nova Obra">
        <i class="icon-plus"></i>
    </a>
</div>

<!-- Delete Modal -->
<div id="modalExcluir" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white;">
        <button type="button" class="close" data-dismiss="modal" style="color: white;">×</button>
        <h3><i class="icon-trash"></i> Confirmar Exclusão</h3>
    </div>
    <div class="modal-body" style="padding: 30px;">
        <p style="font-size: 16px;">Tem certeza que deseja excluir esta obra?</p>
        <p class="text-warning"><i class="icon-warning-sign"></i> Esta ação não poderá ser desfeita.</p>
    </div>
    <div class="modal-footer">
        <form id="formExcluir" method="post" action="<?php echo site_url('obras/excluir'); ?>">
            <input type="hidden" name="id" id="obraIdExcluir">
            <button class="btn btn-large" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger btn-large">
                <i class="icon-trash icon-white"></i> Sim, Excluir
            </button>
        </form>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    document.getElementById('obraIdExcluir').value = id;
    $('#modalExcluir').modal('show');
}

function toggleView(view) {
    const gridView = document.getElementById('gridView');
    const tableView = document.getElementById('tableView');
    const buttons = document.querySelectorAll('.view-toggle-btn');

    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.closest('.view-toggle-btn').classList.add('active');

    if (view === 'grid') {
        gridView.classList.add('active');
        tableView.classList.remove('active');
    } else {
        gridView.classList.remove('active');
        tableView.classList.add('active');
    }
}

function aplicarFiltros() {
    const status = document.getElementById('filterStatus').value;
    const cliente = document.getElementById('filterCliente').value;
    const obra = document.getElementById('filterObra').value;

    let url = '<?php echo site_url("obras"); ?>?';
    if (status) url += 'status=' + encodeURIComponent(status) + '&';
    if (cliente) url += 'cliente=' + encodeURIComponent(cliente) + '&';
    if (obra) url += 'obra=' + encodeURIComponent(obra) + '&';

    window.location.href = url;
}

// Animate progress bars on load
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.obra-progress-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
</script>
