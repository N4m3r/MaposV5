<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.obras-cliente-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 25px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.obras-cliente-header h2 {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
}
.obras-cliente-header p {
    margin: 10px 0 0;
    opacity: 0.9;
    font-size: 15px;
}

.obra-card-cliente {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
    transition: all 0.3s ease;
    margin-bottom: 20px;
}
.obra-card-cliente:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.obra-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}
.obra-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0;
    line-height: 1.3;
}
.obra-card-endereco {
    color: #666;
    font-size: 13px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.obra-status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.obra-progress-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    margin: 15px 0;
}
.obra-progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.obra-progress-label {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}
.obra-progress-value {
    font-size: 22px;
    font-weight: 700;
    color: #667eea;
}
.obra-progress-bar-container {
    background: #e0e0e0;
    border-radius: 10px;
    height: 10px;
    overflow: hidden;
}
.obra-progress-bar-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.5s ease;
}
.obra-progress-bar-fill.baixo { background: linear-gradient(90deg, #ff6b6b, #ee5a52); }
.obra-progress-bar-fill.medio { background: linear-gradient(90deg, #feca57, #ff9f43); }
.obra-progress-bar-fill.alto { background: linear-gradient(90deg, #1dd1a1, #10ac84); }

.obra-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}
.obra-info-item {
    text-align: center;
}
.obra-info-label {
    font-size: 12px;
    color: #888;
    margin-bottom: 4px;
}
.obra-info-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.btn-acompanhar {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 15px;
    text-decoration: none;
}
.btn-acompanhar:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.empty-state-cliente {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    margin: 20px;
}
.empty-state-cliente i {
    font-size: 80px;
    color: #ddd;
    margin-bottom: 20px;
}
.empty-state-cliente h3 {
    color: #666;
    margin-bottom: 10px;
}
.empty-state-cliente p {
    color: #888;
}
</style>

<div class="widget-box">
    <!-- Header -->
    <div class="obras-cliente-header">
        <h2><i class="bx bx-building-house"></i> Minhas Obras</h2>
        <p>Acompanhe o progresso e status de todas as suas obras em um só lugar</p>
    </div>

    <div class="widget-content nopadding tab-content">

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success" style="margin: 20px;"><i class="icon-ok"></i> <?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger" style="margin: 20px;"><i class="icon-remove"></i> <?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>

        <?php if (isset($results) && count($results) > 0): ?>
            <!-- Card View -->
            <div class="row-fluid" style="padding: 15px;">
                <?php $count = 0; ?>
                <?php foreach ($results as $r): ?>
                    <?php
                    $status = $r->status ?? 'Em Andamento';
                    $statusColors = [
                        'Em Andamento' => 'info',
                        'Contratada' => 'warning',
                        'EmExecucao' => 'info',
                        'Concluída' => 'success',
                        'Concluida' => 'success',
                        'Paralisada' => 'important',
                        'Cancelada' => 'inverse',
                        'Prospeccao' => 'default',
                        'Prospecção' => 'default'
                    ];
                    $statusLabel = $statusColors[$status] ?? 'default';
                    $progresso = $r->percentual_concluido ?? $r->progresso ?? 0;
                    $dias_restantes = $r->data_fim_prevista ? ceil((strtotime($r->data_fim_prevista) - time()) / 86400) : null;

                    // Determinar cor da barra de progresso
                    if ($progresso < 30) {
                        $progressoClass = 'baixo';
                    } elseif ($progresso < 70) {
                        $progressoClass = 'medio';
                    } else {
                        $progressoClass = 'alto';
                    }
                    ?>

                    <div class="span4" style="margin-bottom: 15px;">
                        <div class="obra-card-cliente">
                            <div class="obra-card-header">
                                <div>
                                    <h4 class="obra-card-title"><?php echo htmlspecialchars($r->nome ?? 'Obra #' . $r->id); ?></h4>
                                    <div class="obra-card-endereco">
                                        <i class="bx bx-map"></i> <?php echo htmlspecialchars($r->endereco ?? 'Endereço não informado'); ?>
                                    </div>
                                </div>
                                <span class="obra-status-badge label label-<?php echo $statusLabel; ?>"><?php echo $status; ?></span>
                            </div>

                            <!-- Progresso -->
                            <div class="obra-progress-section">
                                <div class="obra-progress-header">
                                    <span class="obra-progress-label">Progresso da Obra</span>
                                    <span class="obra-progress-value"><?php echo $progresso; ?>%</span>
                                </div>
                                <div class="obra-progress-bar-container">
                                    <div class="obra-progress-bar-fill <?php echo $progressoClass; ?>" style="width: <?php echo $progresso; ?>%;"></div>
                                </div>
                            </div>

                            <!-- Info adicional -->
                            <div class="obra-info-grid">
                                <div class="obra-info-item">
                                    <div class="obra-info-label">Data de Início</div>
                                    <div class="obra-info-value">
                                        <i class="bx bx-calendar"></i> <?php echo $r->data_inicio_contrato ? date('d/m/Y', strtotime($r->data_inicio_contrato)) : 'N/A'; ?>
                                    </div>
                                </div>
                                <div class="obra-info-item">
                                    <div class="obra-info-label">Previsão de Término</div>
                                    <div class="obra-info-value">
                                        <?php if ($dias_restantes !== null && $dias_restantes >= 0): ?>
                                            <span style="color: #1dd1a1;"><i class="bx bx-time"></i> <?php echo date('d/m/Y', strtotime($r->data_fim_prevista)); ?></span>
                                        <?php elseif ($dias_restantes < 0): ?>
                                            <span style="color: #ee5a52;"><i class="bx bx-error-circle"></i> Atrasada</span>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Botão -->
                            <a href="<?php echo site_url('mine/visualizarObra/' . $r->id); ?>" class="btn-acompanhar">
                                <i class="bx bx-show"></i> Ver Detalhes da Obra
                            </a>
                        </div>
                    </div>

                    <?php if (++$count % 3 == 0): ?>
                        </div><div class="row-fluid" style="padding: 0 15px;">
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="empty-state-cliente">
                <i class="bx bx-building-house"></i>
                <h3>Nenhuma obra encontrada</h3>
                <p>Você ainda não possui obras vinculadas à sua conta.<br>Entre em contato conosco para mais informações.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php if (isset($results) && count($results) > 10): ?>
    <?php echo $this->pagination->create_links(); ?>
<?php endif; ?>
