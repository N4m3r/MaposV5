<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// Garantir que a variável $obras exista (controller pode passar como $results)
$obras = isset($obras) ? $obras : (isset($results) ? $results : []);

// Debug: log para verificar dados
if (empty($obras)) {
    log_message('debug', 'obras_list.php: Nenhuma obra encontrada para exibição');
} else {
    log_message('debug', 'obras_list.php: ' . count($obras) . ' obras carregadas');
}
?>

<!-- Tema Moderno Obras - CSS Unificado -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
/* Dynamic status colors from DB */
<?php foreach ($status_obra as $s): ?>
.obra-card-header.<?php echo strtolower(preg_replace('/[^a-z]/', '', $s->nome)); ?> {
    background: linear-gradient(135deg, <?php echo $s->cor ?? '#667eea'; ?> 0%, <?php echo $s->cor ?? '#667eea'; ?> 100%) !important;
}
<?php endforeach; ?>
</style>

<div class="obras-unified-container">
    <!-- Header Principal -->
    <div class="obras-main-header">
        <div class="obras-header-content">
            <div class="obras-header-title">
                <h1><?= svg_icon('building', 28, 28) ?> Gerenciamento de Obras</h1>
                <p>Acompanhe e gerencie todas as obras do sistema</p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <a href="<?php echo site_url('obras/adicionar'); ?>" class="obras-filter-btn obras-add-btn">
                    <?= svg_icon('plus', 16, 16) ?> Nova Obra
                </a>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cObras')): ?>
                <a href="<?php echo site_url('obras/configuracoes'); ?>" class="obras-filter-btn" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white;">
                    <?= svg_icon('cog', 16, 16) ?> Configurações
                </a>
                <?php endif; ?>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <button type="button" class="obras-filter-btn" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;" onclick="atualizarTodosProgressos()">
                    <?= svg_icon('refresh', 16, 16) ?> Recalcular Progressos
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="obras-stats-row">
        <div class="obras-stat-card">
            <div class="obras-stat-icon blue">
                <?= svg_icon('building', 24, 24) ?>
            </div>
            <div class="obras-stat-info">
                <div class="obras-stat-value"><?php echo isset($total_obras) ? $total_obras : count($obras); ?></div>
                <div class="obras-stat-label">Total de Obras</div>
            </div>
        </div>

        <div class="obras-stat-card">
            <div class="obras-stat-icon green">
                <?= svg_icon('play-circle', 24, 24) ?>
            </div>
            <div class="obras-stat-info">
                <div class="obras-stat-value"><?php echo isset($obras_em_andamento) ? $obras_em_andamento : count(array_filter($obras ?? [], function($o) { return ($o->status ?? '') == 'em-andamento'; })); ?></div>
                <div class="obras-stat-label">Em Andamento</div>
            </div>
        </div>

        <div class="obras-stat-card">
            <div class="obras-stat-icon cyan">
                <?= svg_icon('calendar', 24, 24) ?>
            </div>
            <div class="obras-stat-info">
                <div class="obras-stat-value"><?php echo isset($obras_contratadas) ? $obras_contratadas : count(array_filter($obras ?? [], function($o) { return ($o->status ?? '') == 'contratada'; })); ?></div>
                <div class="obras-stat-label">Contratadas</div>
            </div>
        </div>

        <div class="obras-stat-card">
            <div class="obras-stat-icon orange">
                <?= svg_icon('check-circle', 24, 24) ?>
            </div>
            <div class="obras-stat-info">
                <div class="obras-stat-value"><?php echo isset($obras_concluidas) ? $obras_concluidas : count(array_filter($obras ?? [], function($o) { return ($o->status ?? '') == 'concluida'; })); ?></div>
                <div class="obras-stat-label">Concluídas</div>
            </div>
        </div>
    </div>


    <!-- CSS dinâmico para cores de status configuradas -->
    <style>
    <?php foreach ($status_obra as $s): ?>
    .obra-card-header.<?php echo strtolower(preg_replace('/[^a-z]/', '', $s->nome)); ?> {
        background: linear-gradient(135deg, <?php echo $s->cor ?? '#667eea'; ?> 0%, <?php echo $s->cor ?? '#667eea'; ?> 100%) !important;
    }
    <?php endforeach; ?>
    </style>

    <!-- Filtros -->
    <div class="obras-filter-bar">
        <div class="obras-filter-group">
            <label><?= svg_icon('search', 16, 16) ?> Buscar:</label>
            <input type="text" id="searchObra" class="obras-filter-input" placeholder="Nome da obra..." onkeyup="filtrarObras()">
        </div>

        <div class="obras-filter-group">
            <label><?= svg_icon('filter', 16, 16) ?> Status:</label>
            <select id="filterStatus" class="obras-filter-select" onchange="filtrarObras()">
                <option value="">Todos</option>
                <?php foreach ($status_obra as $s): ?>
                    <option value="<?php echo htmlspecialchars(strtolower(preg_replace('/[^a-z]/', '', $s->nome))); ?>"><?php echo htmlspecialchars($s->nome); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="obras-filter-btn secondary" onclick="limparFiltros()">
            <?= svg_icon('refresh', 16, 16) ?> Limpar
        </button>
    </div>

    <!-- Grid de Obras -->
    <?php if (!empty($obras)): ?>
    <div class="obras-cards-grid" id="obrasGrid">
        <?php foreach ($obras as $obra): ?>
        <?php
        // Debug: garantir que objeto tenha todas as propriedades
        if (!is_object($obra)) continue;

        // Construir mapa de status para lookup rápido (idealmente feito fora do loop)
        static $statusMapList = null;
        if ($statusMapList === null) {
            $statusMapList = [];
            foreach ($status_obra as $s) {
                $key = strtolower(preg_replace('/[^a-z]/', '', $s->nome));
                $statusMapList[$key] = $s;
            }
        }

        $status_class = '';
        $status_label = '';
        $status_normalized = ''; // Valor normalizado para o filtro
        $status_cor = '';

        $status_lower = strtolower(trim($obra->status ?? ''));
        $status_norm_key = strtolower(preg_replace('/[^a-z]/', '', $obra->status ?? ''));

        if (isset($statusMapList[$status_norm_key])) {
            $s = $statusMapList[$status_norm_key];
            $status_label = $s->nome;
            $status_class = $status_norm_key;
            $status_normalized = $status_norm_key;
            $status_cor = $s->cor ?? '';
        } else {
            // Fallback hardcoded para compatibilidade
            switch ($status_lower) {
                case 'em-andamento':
                case 'em_execucao':
                case 'em execucao':
                case 'emexecucao':
                case 'execucao':
                    $status_class = 'andamento';
                    $status_label = 'Em Andamento';
                    $status_normalized = 'em-andamento';
                    break;
                case 'concluida':
                case 'concluída':
                case 'finalizada':
                case 'entregue':
                case 'concluido':
                    $status_class = 'concluida';
                    $status_label = 'Concluída';
                    $status_normalized = 'concluida';
                    break;
                case 'paralisada':
                case 'pausada':
                case 'suspensa':
                    $status_class = 'paralisada';
                    $status_label = 'Paralisada';
                    $status_normalized = 'paralisada';
                    break;
                case 'prospeccao':
                case 'prospecção':
                case 'prospectacao':
                case 'novo':
                case 'nova':
                    $status_class = 'prospeccao';
                    $status_label = 'Prospecção';
                    $status_normalized = 'prospeccao';
                    break;
                case 'contratada':
                case 'aprovada':
                case 'iniciada':
                    $status_class = 'contratada';
                    $status_label = 'Contratada';
                    $status_normalized = 'contratada';
                    break;
                case 'cancelada':
                case 'cancelado':
                case 'encerrada':
                    $status_class = 'cancelada';
                    $status_label = 'Cancelada';
                    $status_normalized = 'cancelada';
                    break;
                default:
                    $status_class = '';
                    $status_label = ucfirst($obra->status);
                    $status_normalized = $obra->status;
            }
        }
        $progresso = $obra->percentual_concluido ?? 0;
        ?>
        <div class="obra-item-card" data-nome="<?php echo strtolower($obra->nome); ?>" data-status="<?php echo $status_normalized; ?>">
            <div class="obra-card-header <?php echo $status_class; ?>">
                <span class="obra-card-status-badge"><?php echo $status_label; ?></span>
                <h3 class="obra-card-title"><?php echo htmlspecialchars($obra->nome); ?></h3>
                <div class="obra-card-cliente">
                    <?= svg_icon('user', 16, 16) ?> <?php echo htmlspecialchars($obra->cliente_nome ?? 'Sem cliente'); ?>
                </div>
            </div>

            <div class="obra-card-body">
                <div class="obra-card-info-row">
                    <span class="obra-card-info-label">
                        <?= svg_icon('map', 14, 14) ?> Endereço
                    </span>
                    <span class="obra-card-info-value">
                        <?php echo htmlspecialchars($obra->endereco ?? 'Não informado'); ?>
                    </span>
                </div>

                <div class="obra-card-info-row">
                    <span class="obra-card-info-label">
                        <?= svg_icon('calendar', 14, 14) ?> Início
                    </span>
                    <span class="obra-card-info-value">
                        <?php echo $obra->data_inicio_contrato ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : 'Não definido'; ?>
                    </span>
                </div>

                <div class="obra-card-info-row">
                    <span class="obra-card-info-label">
                        <?= svg_icon('flag-checkered', 14, 14) ?> Previsão
                    </span>
                    <span class="obra-card-info-value">
                        <?php echo $obra->data_fim_prevista ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : 'Não definido'; ?>
                    </span>
                </div>

                <!-- Progresso -->
                <div class="obra-card-progress">
                    <div class="obra-card-progress-header">
                        <span class="obra-card-progress-label">Progresso</span>
                        <span class="obra-card-progress-value"><?php echo $progresso; ?>%</span>
                    </div>
                    <div class="obra-card-progress-bar">
                        <div class="obra-card-progress-fill" style="width: <?php echo $progresso; ?>%"></div>
                    </div>
                </div>

                <!-- Stats rápidas -->
                <div class="obra-card-stats">
                    <div class="obra-card-stat">
                        <span class="obra-card-stat-value"><?php echo $obra->total_etapas ?? 0; ?></span>
                        <span class="obra-card-stat-label">Etapas</span>
                    </div>
                    <div class="obra-card-stat">
                        <span class="obra-card-stat-value"><?php echo $obra->total_atividades ?? 0; ?></span>
                        <span class="obra-card-stat-label">Atividades</span>
                    </div>
                    <div class="obra-card-stat">
                        <span class="obra-card-stat-value"><?php echo $obra->total_equipe ?? 0; ?></span>
                        <span class="obra-card-stat-label">Equipe</span>
                    </div>
                </div>
            </div>

            <div class="obra-card-actions">
                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="obra-card-btn view">
                    <?= svg_icon('eye', 16, 16) ?> Visualizar
                </a>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <a href="<?php echo site_url('obras/editar/' . $obra->id); ?>" class="obra-card-btn edit">
                    <?= svg_icon('edit', 16, 16) ?> Editar
                </a>
                <?php endif; ?>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                <form action="<?php echo site_url('obras/excluir'); ?>" method="post" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta obra?');">
                    <input type="hidden" name="id" value="<?php echo $obra->id; ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <button type="submit" class="obra-card-btn" style="background: #e74c3c;">
                        <?= svg_icon('trash', 16, 16) ?> Excluir
                    </button>
                </form>
                <?php endif; ?>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <!-- Botão de Ações Rápidas -->
                <div class="obra-quick-actions">
                    <button type="button" class="obra-card-btn quick-action-toggle" onclick="toggleQuickMenu(<?php echo $obra->id; ?>)">
                        <?= svg_icon('chevron-down', 16, 16) ?> Ações
                    </button>
                    <div class="obra-quick-menu" id="quickMenu_<?php echo $obra->id; ?>">
                        <div class="obra-quick-menu-header">
                            <?= svg_icon('bolt', 16, 16) ?> Ações Rápidas
                        </div>
                        <div class="obra-quick-menu-item" onclick="atualizarStatusRapido(<?php echo $obra->id; ?>, 'prospeccao')">
                            <span class="obra-status-dot" style="background: #a8edea;"></span> Prospecção
                        </div>
                        <div class="obra-quick-menu-item" onclick="atualizarStatusRapido(<?php echo $obra->id; ?>, 'contratada')">
                            <span class="obra-status-dot" style="background: #f39c12;"></span> Contratada
                        </div>
                        <div class="obra-quick-menu-item" onclick="atualizarStatusRapido(<?php echo $obra->id; ?>, 'em-andamento')">
                            <span class="obra-status-dot" style="background: #4facfe;"></span> Em Andamento
                        </div>
                        <div class="obra-quick-menu-item" onclick="atualizarStatusRapido(<?php echo $obra->id; ?>, 'paralisada')">
                            <span class="obra-status-dot" style="background: #ff6b6b;"></span> Paralisada
                        </div>
                        <div class="obra-quick-menu-item" onclick="atualizarStatusRapido(<?php echo $obra->id; ?>, 'concluida')">
                            <span class="obra-status-dot" style="background: #11998e;"></span> Concluída
                        </div>
                        <div class="obra-quick-menu-item" onclick="atualizarStatusRapido(<?php echo $obra->id; ?>, 'cancelada')">
                            <span class="obra-status-dot" style="background: #636e72;"></span> Cancelada
                        </div>
                        <div class="obra-quick-menu-divider"></div>
                        <a href="<?php echo site_url('obras/relatorioGeral/' . $obra->id); ?>" class="obra-quick-menu-item">
                            <?= svg_icon('file-text', 16, 16, '', 'color: #667eea;') ?> Relatório Geral
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <?php if (isset($pagination)): ?>
    <div class="obras-pagination">
        <?php echo $pagination; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <!-- Empty State -->
    <div class="obras-empty-state">
        <div class="obras-empty-icon">
            <?= svg_icon('building', 48, 48, '', 'color:var(--cinza0,#9aa6b3)') ?>
        </div>
        <h3 class="obras-empty-title">Nenhuma obra encontrada</h3>
        <p class="obras-empty-desc">Comece cadastrando uma nova obra para gerenciar seus projetos.</p>
        <a href="<?php echo site_url('obras/adicionar'); ?>" class="obras-filter-btn">
            <?= svg_icon('plus', 16, 16) ?> Cadastrar Nova Obra
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
// Filtro de obras
function filtrarObras() {
    const search = document.getElementById('searchObra').value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    const status = document.getElementById('filterStatus').value;
    const cards = document.querySelectorAll('.obra-item-card');

    cards.forEach(card => {
        const nome = card.getAttribute('data-nome');
        const cardStatus = card.getAttribute('data-status');

        const matchSearch = !search || nome.includes(search);
        const matchStatus = !status || cardStatus === status;

        card.style.display = matchSearch && matchStatus ? 'flex' : 'none';
    });
}

function limparFiltros() {
    document.getElementById('searchObra').value = '';
    document.getElementById('filterStatus').value = '';
    filtrarObras();
}

// Animação de entrada
$(document).ready(function() {
    $('.obra-item-card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });
});

// Menu de Ações Rápidas - Toggle
function toggleQuickMenu(obraId) {
    const menu = document.getElementById('quickMenu_' + obraId);
    const allMenus = document.querySelectorAll('.obra-quick-menu');

    // Fechar todos os outros menus
    allMenus.forEach(function(m) {
        if (m !== menu && m.classList.contains('active')) {
            m.classList.remove('active');
        }
    });

    // Toggle do menu atual
    if (menu) {
        menu.classList.toggle('active');
    }

    // Fechar menu ao clicar fora
    function closeMenu(e) {
        if (!e.target.closest('.obra-quick-actions')) {
            allMenus.forEach(function(m) {
                m.classList.remove('active');
            });
            document.removeEventListener('click', closeMenu);
        }
    }

    // Adicionar listener após um pequeno delay para não fechar imediatamente
    setTimeout(function() {
        document.addEventListener('click', closeMenu);
    }, 100);
}

// Atualizar status via AJAX
function atualizarStatusRapido(obraId, novoStatus) {
    // Fechar menu
    const menu = document.getElementById('quickMenu_' + obraId);
    if (menu) {
        menu.classList.remove('active');
    }

    // Mostrar loading
    mostrarToast('Atualizando...', 'Alterando status da obra', 'info');

    // Enviar requisição AJAX
    $.ajax({
        url: '<?php echo site_url("obras/ajax_atualizar_status"); ?>',
        method: 'POST',
        dataType: 'json',
        data: {
            obra_id: obraId,
            status: novoStatus,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            if (response.success) {
                mostrarToast('Sucesso!', 'Status atualizado com sucesso', 'success');
                // Recarregar cards após 1 segundo
                setTimeout(function() {
                    if (typeof atualizarCardsManual === 'function') {
                        atualizarCardsManual();
                    } else {
                        location.reload();
                    }
                }, 1000);
            } else {
                mostrarToast('Erro!', response.message || 'Erro ao atualizar status', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao atualizar status:', error);
            mostrarToast('Erro!', 'Falha na comunicação com o servidor', 'error');
        }
    });
}

// Função para mostrar toast de notificação
// Recalcular progressos de todas as obras visíveis
function atualizarTodosProgressos() {
    mostrarToast('Recalculando...', 'Atualizando progresso de todas as obras', 'info');

    $.ajax({
        url: '<?php echo site_url("obras/api_atualizarProgressoGeral"); ?>',
        method: 'POST',
        dataType: 'json',
        data: {
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            if (response.success) {
                mostrarToast('Sucesso!', 'Progressos atualizados. Recarregando...', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                mostrarToast('Erro!', response.message || 'Erro ao recalcular progressos', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao recalcular progressos:', error);
            mostrarToast('Erro!', 'Falha na comunicação com o servidor', 'error');
        }
    });
}

function mostrarToast(titulo, mensagem, tipo) {
    // Remover toasts anteriores
    const toastsAnteriores = document.querySelectorAll('.obra-toast');
    toastsAnteriores.forEach(function(t) {
        t.remove();
    });

    // Criar novo toast
    const toast = document.createElement('div');
    toast.className = 'obra-toast ' + tipo;

    let iconClass = 'bx bx-info-circle';
    if (tipo === 'success') iconClass = 'bx bx-check';
    if (tipo === 'error') iconClass = 'bx bx-x';
    // SVG icon replacements
    let svgIcon = '';
    if (tipo === 'success') svgIcon = '<?= svg_icon("check-circle", 20, 20, "", "color:#27ae60;") ?>';
    else if (tipo === 'error') svgIcon = '<?= svg_icon("x", 20, 20, "", "color:#e74c3c;") ?>';
    else svgIcon = '<?= svg_icon("info-circle", 20, 20, "", "color:#667eea;") ?>';

    toast.innerHTML = `
        <div class="obra-toast-icon">
            ${svgIcon}
        </div>
        <div class="obra-toast-content">
            <h4 class="obra-toast-title">${titulo}</h4>
            <p class="obra-toast-message">${mensagem}</p>
        </div>
    `;

    document.body.appendChild(toast);

    // Animar entrada
    setTimeout(function() {
        toast.classList.add('show');
    }, 10);

    // Remover após 3 segundos (se não for tipo info)
    if (tipo !== 'info') {
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 3000);
    }
}
</script>
