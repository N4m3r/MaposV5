<?php
// View parcial para integração de atividades do novo sistema na visualização da obra
$atividades_sistema = $atividades_sistema ?? [];
$estatisticas_atividades = $estatisticas_atividades ?? null;
$obra_id = $obra->id ?? 0;
$tipos_atividades = $tipos_atividades ?? [];
?>

<style>
.atividades-sistema-section {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
}

.atividades-sistema-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e9ecef;
}

.atividades-sistema-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.atividades-sistema-title i {
    color: #667eea;
}

.estatisticas-atividades {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin-bottom: 24px;
}

.stat-card-atividade {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
}

.stat-card-atividade.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-card-atividade.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card-atividade.info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-card-atividade .number {
    font-size: 24px;
    font-weight: bold;
}

.stat-card-atividade .label {
    font-size: 12px;
    opacity: 0.9;
}

.atividades-timeline {
    position: relative;
    padding-left: 30px;
}

.atividades-timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.atividade-timeline-item {
    position: relative;
    margin-bottom: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    border-left: 4px solid #667eea;
}

.atividade-timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 20px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #667eea;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #667eea;
}

.atividade-timeline-item.concluida {
    border-left-color: #27ae60;
}

.atividade-timeline-item.concluida::before {
    background: #27ae60;
    box-shadow: 0 0 0 2px #27ae60;
}

.atividade-timeline-item.em-andamento {
    border-left-color: #ffc107;
}

.atividade-timeline-item.em-andamento::before {
    background: #ffc107;
    box-shadow: 0 0 0 2px #ffc107;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.atividade-header-sistema {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.atividade-titulo-sistema {
    font-weight: 600;
    font-size: 15px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.atividade-titulo-sistema i {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.atividade-tempo-sistema {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    font-size: 12px;
}

.atividade-hora-box {
    background: #e9ecef;
    padding: 4px 10px;
    border-radius: 15px;
    margin-bottom: 4px;
}

.atividade-hora-box.destaque {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.atividade-info-sistema {
    font-size: 13px;
    color: #6c757d;
    margin-top: 8px;
}

.atividade-info-sistema p {
    margin: 4px 0;
}

.atividade-info-sistema i {
    width: 18px;
    color: #667eea;
}

.atividade-fotos {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.atividade-foto-thumb {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s;
}

.atividade-foto-thumb:hover {
    transform: scale(1.05);
}

.atividade-status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.atividade-status-badge.concluida {
    background: #d4edda;
    color: #155724;
}

.atividade-status-badge.em-andamento {
    background: #fff3cd;
    color: #856404;
}

.atividade-status-badge.pausada {
    background: #f8f9fa;
    color: #6c757d;
}

.btn-nova-atividade-obra {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-nova-atividade-obra:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.empty-atividades {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.empty-atividades i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.modal-foto-grande .modal-body {
    text-align: center;
    padding: 20px;
}

.modal-foto-grande img {
    max-width: 100%;
    max-height: 70vh;
    border-radius: 8px;
}
</style>

<div class="atividades-sistema-section">
    <div class="atividades-sistema-header">
        <div class="atividades-sistema-title">
            <i class="bx bx-timer"></i>
            Registro de Atividades (Hora Início/Fim)
        </div>
        <div>
            <a href="<?= site_url('atividades/wizard?obra_id=' . $obra_id) ?>" class="btn-nova-atividade-obra">
                <i class="bx bx-plus"></i> Nova Atividade
            </a>
        </div>
    </div>

    <!-- Estatísticas -->
    <?php if ($estatisticas_atividades): ?>
    <div class="estatisticas-atividades">
        <div class="stat-card-atividade">
            <div class="number"><?= $estatisticas_atividades['total_atividades'] ?></div>
            <div class="label">Total</div>
        </div>
        <div class="stat-card-atividade success">
            <div class="number"><?= $estatisticas_atividades['concluidas'] ?></div>
            <div class="label">Concluídas</div>
        </div>
        <div class="stat-card-atividade warning">
            <div class="number"><?= $estatisticas_atividades['em_andamento'] ?></div>
            <div class="label">Em Andamento</div>
        </div>
        <div class="stat-card-atividade info">
            <div class="number"><?= $estatisticas_atividades['tempo_total_horas'] ?>h</div>
            <div class="label">Horas Trabalhadas</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Timeline de Atividades -->
    <?php if (count($atividades_sistema) > 0): ?>
    <div class="atividades-timeline">
        <?php foreach ($atividades_sistema as $atv): ?>
        <?php
            $classe_status = '';
            $badge_status = '';
            $badge_text = '';

            if ($atv->status == 'finalizada' && $atv->concluida == 1) {
                $classe_status = 'concluida';
                $badge_status = 'concluida';
                $badge_text = 'Concluída';
            } elseif ($atv->status == 'em_andamento') {
                $classe_status = 'em-andamento';
                $badge_status = 'em-andamento';
                $badge_text = 'Em Andamento';
            } elseif ($atv->status == 'pausada') {
                $classe_status = 'pausada';
                $badge_status = 'pausada';
                $badge_text = 'Pausada';
            } else {
                $classe_status = 'concluida'; // Finalizada mas não concluída
                $badge_status = '';
                $badge_text = 'Finalizada';
            }

            $cor_tipo = $atv->tipo_cor ?? '#667eea';
            $icone_tipo = $atv->tipo_icone ?? 'bx-wrench';
        ?>
        <div class="atividade-timeline-item <?= $classe_status ?>">
            <div class="atividade-header-sistema">
                <div class="atividade-titulo-sistema">
                    <i class="bx <?= $icone_tipo ?>" style="background: <?= $cor_tipo ?>;"></i>
                    <div>
                        <div><?= htmlspecialchars($atv->tipo_nome ?? 'Atividade') ?></div>
                        <span class="atividade-status-badge <?= $badge_status ?>"><?= $badge_text ?></span>
                    </div>
                </div>
                <div class="atividade-tempo-sistema">
                    <div class="atividade-hora-box">
                        <i class="bx bx-play-circle"></i> <?= date('H:i', strtotime($atv->hora_inicio)) ?>
                    </div>
                    <?php if ($atv->hora_fim): ?>
                    <div class="atividade-hora-box destaque">
                        <i class="bx bx-stop-circle"></i> <?= date('H:i', strtotime($atv->hora_fim)) ?>
                    </div>
                    <div class="text-muted">
                        <?= formatar_duracao($atv->duracao_minutos) ?>
                    </div>
                    <?php else: ?>
                    <div class="atividade-hora-box" style="background: #ffc107; color: #000;">
                        <i class="bx bx-time"></i> Em andamento...
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="atividade-info-sistema">
                <?php if ($atv->equipamento): ?>
                <p><i class="bx bx-wrench"></i> <?= htmlspecialchars($atv->equipamento) ?></p>
                <?php endif; ?>

                <?php if ($atv->descricao): ?>
                <p><i class="bx bx-detail"></i> <?= htmlspecialchars($atv->descricao) ?></p>
                <?php endif; ?>

                <?php if ($atv->nome_tecnico): ?>
                <p><i class="bx bx-user"></i> Técnico: <?= htmlspecialchars($atv->nome_tecnico) ?></p>
                <?php endif; ?>

                <?php if ($atv->problemas_encontrados): ?>
                <p class="text-warning"><i class="bx bx-error"></i> Problemas: <?= htmlspecialchars($atv->problemas_encontrados) ?></p>
                <?php endif; ?>

                <?php if ($atv->solucao_aplicada): ?>
                <p class="text-success"><i class="bx bx-check-shield"></i> Solução: <?= htmlspecialchars($atv->solucao_aplicada) ?></p>
                <?php endif; ?>
            </div>

            <!-- Fotos -->
            <?php if (!empty($atv->fotos)): ?>
            <div class="atividade-fotos">
                <?php foreach ($atv->fotos as $foto): ?>
                <?php
                    $caminho = $foto->caminho_arquivo ?? '';
                    if ($caminho) {
                        $url = base_url('assets/atividades/fotos/' . $caminho);
                    } else {
                        $url = $foto->foto_base64 ?? '';
                    }
                ?>
                <?php if ($url): ?>
                <img src="<?= $url ?>" class="atividade-foto-thumb" onclick="abrirFoto('<?= $url ?>')" title="<?= $foto->descricao ?? 'Foto' ?>">
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Materiais -->
            <?php if (!empty($atv->materiais)): ?>
            <div class="atividade-materiais" style="margin-top: 10px;">
                <strong><i class="bx bx-package"></i> Materiais utilizados:</strong><br>
                <?php foreach ($atv->materiais as $mat): ?>
                <span class="badge badge-info"><?= $mat->quantidade ?> <?= $mat->unidade ?> - <?= $mat->produto_descricao ?? $mat->nome_produto ?></span><br>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-atividades">
        <i class="bx bx-clipboard"></i>
        <h4>Nenhuma atividade registrada</h4>
        <p>Inicie uma nova atividade para registrar Hora Início e Hora Fim</p>
        <a href="<?= site_url('atividades/wizard?obra_id=' . $obra_id) ?>" class="btn-nova-atividade-obra" style="margin-top: 15px;">
            <i class="bx bx-plus"></i> Iniciar Atividade
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Modal para visualizar foto grande -->
<div id="modal-foto-grande" class="modal hide fade modal-foto-grande" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Foto da Atividade</h3>
    </div>
    <div class="modal-body">
        <img id="foto-grande-img" src="" alt="Foto">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Fechar</button>
    </div>
</div>

<script>
function abrirFoto(url) {
    document.getElementById('foto-grande-img').src = url;
    $('#modal-foto-grande').modal('show');
}
</script>
