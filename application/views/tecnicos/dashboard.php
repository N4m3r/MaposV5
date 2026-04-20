<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
/* Dashboard Mobile-First */
.dashboard-container { padding: 15px; max-width: 100%; }

/* Header do Técnico */
.header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 25px 20px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    display: flex;
    align-items: center;
    gap: 15px;
}
.tec-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    flex-shrink: 0;
}
.tec-info h2 {
    margin: 0 0 5px 0;
    font-size: 20px;
    font-weight: 700;
}
.tec-info p {
    margin: 0;
    opacity: 0.9;
    font-size: 13px;
}
.tec-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    margin-top: 8px;
}

/* Grid de Stats */
.stats-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.stat-box {
    background: white;
    border-radius: 15px;
    padding: 20px 15px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-left: 4px solid #3498db;
    transition: all 0.3s;
}
.stat-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}
.stat-box.success { border-left-color: #11998e; }
.stat-box.warning { border-left-color: #f39c12; }
.stat-box.danger { border-left-color: #e74c3c; }
.stat-box.obras { border-left-color: #667eea; }
.stat-numero {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}
.stat-label {
    font-size: 12px;
    color: #888;
    text-transform: uppercase;
    font-weight: 600;
}

/* Card de Ação Principal - Obras */
.obras-destaque {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 25px;
    color: white;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.obras-destaque::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}
.obras-destaque-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    position: relative;
}
.obras-destaque-titulo {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
}
.obras-destaque-contagem {
    background: rgba(255,255,255,0.2);
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}
.obras-destaque-lista {
    position: relative;
}
.obra-destaque-item {
    background: rgba(255,255,255,0.15);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.obra-destaque-item:last-child {
    margin-bottom: 0;
}
.obra-destaque-nome {
    font-weight: 600;
    font-size: 14px;
}
.obra-destaque-progresso {
    font-size: 18px;
    font-weight: 700;
}
.btn-ver-obras {
    display: block;
    width: 100%;
    margin-top: 15px;
    padding: 15px;
    background: white;
    color: #11998e;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s;
}
.btn-ver-obras:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    text-decoration: none;
    color: #11998e;
}

/* Menu Principal */
.menu-principal {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.menu-item {
    background: white;
    border-radius: 15px;
    padding: 25px 15px;
    text-align: center;
    text-decoration: none;
    color: #333;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border: 2px solid transparent;
    transition: all 0.3s;
}
.menu-item:hover {
    border-color: #11998e;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    text-decoration: none;
    color: #333;
}
.menu-item i {
    font-size: 32px;
    margin-bottom: 10px;
    display: block;
}
.menu-item.os i { color: #3498db; }
.menu-item.obras i { color: #11998e; }
.menu-item.estoque i { color: #9b59b6; }
.menu-item.perfil i { color: #f39c12; }
.menu-item span {
    font-size: 13px;
    font-weight: 600;
}

/* Seção de OS de Hoje */
.section-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.06);
}
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f5f5f5;
}
.section-titulo {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-titulo i { color: #3498db; }
.btn-ver-tudo {
    font-size: 12px;
    color: #3498db;
    text-decoration: none;
    font-weight: 600;
}
.btn-ver-tudo:hover {
    text-decoration: underline;
}

/* Lista de OS */
.os-lista {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.os-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s;
}
.os-item:hover {
    background: #e8f4f8;
}
.os-numero {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
}
.os-info {
    flex: 1;
    min-width: 0;
}
.os-cliente {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.os-detalhes {
    font-size: 12px;
    color: #888;
    margin-top: 3px;
}
.os-status {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
}
.os-status.aberto { background: #d4edda; color: #155724; }
.os-status.andamento { background: #fff3cd; color: #856404; }
.os-status.pendente { background: #f8d7da; color: #721c24; }
.os-status.finalizada { background: #d1ecf1; color: #0c5460; }
.os-acao {
    width: 40px;
    height: 40px;
    background: #11998e;
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    flex-shrink: 0;
}
.os-acao:hover {
    background: #0d7a6e;
    text-decoration: none;
    color: white;
}

/* Estoque Preview */
.estoque-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}
.estoque-item:last-child {
    border-bottom: none;
}
.estoque-nome {
    font-size: 14px;
    color: #333;
}
.estoque-qtd {
    background: #e8f4f8;
    color: #3498db;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #888;
}
.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}
.empty-state p {
    font-size: 14px;
}

/* Desktop Responsive */
@media (min-width: 768px) {
    .dashboard-container { padding: 30px; max-width: 1200px; margin: 0 auto; }
    .stats-row { grid-template-columns: repeat(4, 1fr); }
    .menu-principal { grid-template-columns: repeat(4, 1fr); }
    .header-card { padding: 30px; }
}
</style>

<div class="dashboard-container">

    <!-- Header do Técnico -->
    <div class="header-card">
        <div class="tec-avatar">
            <?php echo strtoupper(substr($tecnico->nome ?? 'T', 0, 1)); ?>
        </div>
        <div class="tec-info">
            <h2>Olá, <?php echo htmlspecialchars($tecnico->nome ?? 'Técnico'); ?></h2>
            <p><?php echo date('d/m/Y'); ?> •
                <?php
                $dias = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                echo $dias[date('w')];
                ?>
            </p>
            <span class="tec-badge">
                <i class="icon-star"></i> Nível <?php echo $tecnico->nivel_tecnico ?? 'II'; ?>
            </span>
        </div>
    </div>

    <!-- Grid de Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-numero"><?php echo count($os_hoje ?? []); ?></div>
            <div class="stat-label">OS Hoje</div>
        </div>
        <div class="stat-box warning">
            <div class="stat-numero"><?php echo count($os_pendentes ?? []); ?></div>
            <div class="stat-label">Pendentes</div>
        </div>
        <div class="stat-box success">
            <div class="stat-numero"><?php echo $os_concluidas ?? 0; ?></div>
            <div class="stat-label">Concluídas</div>
        </div>
        <div class="stat-box obras">
            <div class="stat-numero"><?php echo count($minhas_obras ?? []); ?></div>
            <div class="stat-label">Obras</div>
        </div>
    </div>

    <?php if (!empty($minhas_obras)): ?>
    <!-- Card Destaque: Obras -->
    <div class="obras-destaque">
        <div class="obras-destaque-header">
            <h3 class="obras-destaque-titulo">
                <i class="icon-building"></i> Minhas Obras
            </h3>
            <span class="obras-destaque-contagem"><?php echo count($minhas_obras); ?> ativa(s)</span>
        </div>

        <div class="obras-destaque-lista">
            <?php foreach (array_slice($minhas_obras, 0, 2) as $obra): ?>
            <div class="obra-destaque-item">
                <span class="obra-destaque-nome"><?php echo htmlspecialchars($obra->nome); ?></span>
                <span class="obra-destaque-progresso"><?php echo $obra->percentual_concluido ?? 0; ?>%</span>
            </div>
            <?php endforeach; ?>
        </div>

        <a href="<?php echo site_url('tecnicos/minhas_obras'); ?>" class="btn-ver-obras">
            Ver Todas as Obras <i class="icon-arrow-right"></i>
        </a>
    </div>
    <?php endif; ?>

    <!-- Menu Principal -->
    <div class="menu-principal">
        <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="menu-item os">
            <i class="icon-clipboard"></i>
            <span>Minhas OS</span>
        </a>
        <a href="<?php echo site_url('tecnicos/minhas_obras'); ?>" class="menu-item obras">
            <i class="icon-building"></i>
            <span>Minhas Obras</span>
        </a>
        <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="menu-item estoque">
            <i class="icon-package"></i>
            <span>Meu Estoque</span>
        </a>
        <a href="<?php echo site_url('tecnicos/perfil'); ?>" class="menu-item perfil">
            <i class="icon-user"></i>
            <span>Meu Perfil</span>
        </a>
    </div>

    <!-- OS de Hoje -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-titulo">
                <i class="icon-calendar"></i> OS de Hoje
            </div>
            <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn-ver-tudo">Ver todas</a>
        </div>

        <?php if (!empty($os_hoje)): ?>
            <div class="os-lista">
                <?php foreach ($os_hoje as $os): ?>
                <?php
                $statusClass = 'aberto';
                if ($os->status == 'Em Andamento') $statusClass = 'andamento';
                elseif ($os->status == 'Pendente') $statusClass = 'pendente';
                elseif ($os->status == 'Finalizada') $statusClass = 'finalizada';
                ?>
                <div class="os-item">
                    <div class="os-numero">#<?php echo $os->idOs; ?></div>
                    <div class="os-info">
                        <div class="os-cliente"><?php echo htmlspecialchars($os->cliente_nome ?? 'N/A'); ?></div>
                        <div class="os-detalhes">
                            <?php if (isset($os->hora_inicial)): ?>
                                <i class="icon-time"></i> <?php echo $os->hora_inicial; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="os-status <?php echo $statusClass; ?>"><?php echo $os->status; ?></span>
                    <a href="<?php echo site_url('tecnicos/executar_os/' . $os->idOs); ?>" class="os-acao" title="Executar OS">
                        <i class="icon-play"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="icon-calendar"></i>
                <p>Nenhuma OS agendada para hoje</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Estoque -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-titulo">
                <i class="icon-package" style="color: #9b59b6;"></i> Meu Estoque
            </div>
            <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="btn-ver-tudo">Ver tudo</a>
        </div>

        <?php if (!empty($estoque)): ?>
            <?php foreach (array_slice($estoque, 0, 3) as $item): ?>
            <div class="estoque-item">
                <span class="estoque-nome">
                    <i class="icon-package" style="color: #27ae60; margin-right: 8px;"></i>
                    <?php echo htmlspecialchars($item->produto_nome ?? 'Produto'); ?>
                </span>
                <span class="estoque-qtd"><?php echo $item->quantidade; ?> <?php echo $item->unidade ?? ''; ?></span>
            </div>
            <?php endforeach; ?>
            <?php if (count($estoque) > 3): ?>
            <div style="text-align: center; margin-top: 15px;">
                <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="btn-ver-tudo">+ <?php echo count($estoque) - 3; ?> itens</a>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="icon-package"></i>
                <p>Nenhum item em estoque</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
// Animação nos cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.stat-box, .menu-item');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
