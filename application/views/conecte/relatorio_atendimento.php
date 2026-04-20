<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- Relatório de Atendimento - Área do Cliente -->
<div id="content">
<style>
.cliente-relatorio-content { margin-top: 15px !important; }
@media (max-width: 768px) { .cliente-relatorio-content { margin-top: 10px !important; } }

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
}
.status-badge.status-finalizada {
    background: #e8f5e9;
    color: #2e7d32;
}

/* Relatório Card */
.relatorio-card {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.relatorio-card h5 {
    margin: 0 0 15px 0;
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.info-row {
    display: flex;
    gap: 20px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.8rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    color: #333;
    font-weight: 500;
}

/* Timeline de Execução */
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #667eea;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding: 15px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 20px;
    width: 12px;
    height: 12px;
    background: #667eea;
    border-radius: 50%;
    border: 2px solid white;
}
.timeline-data {
    font-size: 0.85rem;
    color: #888;
    margin-bottom: 5px;
}
.timeline-titulo {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}
.timeline-detalhes {
    font-size: 0.9rem;
    color: #666;
}

/* Fotos Grid */
.fotos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
}
.foto-item {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
}
.foto-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}
.foto-tipo {
    padding: 8px;
    background: #f8f9fa;
    font-size: 0.8rem;
    text-align: center;
    color: #666;
}

/* Assinatura */
.assinatura-box {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: white;
}
.assinatura-img {
    max-width: 100%;
    max-height: 200px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #999;
}
.empty-text {
    margin: 0;
}

/* Checkin Table */
.checkin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.checkin-table th,
.checkin-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    font-size: 0.85rem;
}
.checkin-table th {
    background: #f5f5f5;
    font-weight: bold;
    color: #333;
}
.checkin-table tr:nth-child(even) {
    background: #fafafa;
}
.checkin-table small {
    color: #888;
    font-size: 0.75rem;
}
.badge-status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.75rem;
    font-weight: bold;
}
.badge-success {
    background: #d4edda;
    color: #155724;
}
.badge-warning {
    background: #fff3cd;
    color: #856404;
}

/* Photos by Stage */
.photos-section {
    margin-top: 15px;
}
.photos-section-title {
    font-size: 0.95rem;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #eee;
}

/* Client Card Responsive */
.client-card {
    display: flex;
    gap: 15px;
    align-items: flex-start;
    flex-wrap: wrap;
}
.client-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #667eea;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.client-info {
    flex: 1;
    min-width: 250px;
}
.client-info h4 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.1rem;
    word-break: break-word;
}
.client-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 20px;
    margin-bottom: 8px;
}
.client-meta .meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #666;
    font-size: 0.9rem;
    word-break: break-word;
}
.client-meta .meta-item i {
    color: #667eea;
    font-size: 1rem;
}

@media (max-width: 768px) {
    .client-card {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .client-info {
        min-width: 100%;
    }
    .client-meta {
        justify-content: center;
    }
    .client-meta .meta-item {
        flex: 1 1 100%;
        justify-content: center;
    }
}
</style>

<div class="row-fluid cliente-relatorio-content">
    <?php if (empty($os) || !is_object($os)): ?>
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-error"></i></span>
                <h5>Erro ao carregar relatório</h5>
            </div>
            <div class="widget-content">
                <div class="alert alert-error">
                    <strong>Erro:</strong> Não foi possível carregar os dados da OS. Por favor, tente novamente.
                </div>
                <a href="<?php echo base_url('index.php/mine/os'); ?>" class="btn btn-primary">
                    <i class="bx bx-arrow-back"></i> Voltar para Minhas OS
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
<div class="span12">
        <!-- Header -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-file"></i></span>
                <h5>Relatório de Atendimento - OS #<?php echo $os->idOs ?? 'N/A'; ?></h5>
                <div class="buttons">
                    <a href="<?php echo base_url('index.php/mine/visualizarOs/' . ($os->idOs ?? '')); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Voltar para OS
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Informações da Empresa (Emitente) -->
                <?php if (!empty($emitente)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-building"></i> Empresa Responsável</h5>
                    <div class="client-card">
                        <div class="client-info">
                            <h4><?php echo $emitente->nome ?? 'Empresa'; ?></h4>
                            <?php if (!empty($emitente->cnpj)): ?>
                                <div class="client-meta">
                                    <span class="meta-item">
                                        <i class="bx bx-id-card"></i>
                                        CNPJ: <?php echo $emitente->cnpj; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($emitente->rua) || !empty($emitente->cidade)): ?>
                                <div class="client-meta">
                                    <span class="meta-item">
                                        <i class="bx bx-map"></i>
                                        <?php
                                        $endereco_emitente = [];
                                        if (!empty($emitente->rua)) $endereco_emitente[] = $emitente->rua;
                                        if (!empty($emitente->numero)) $endereco_emitente[] = $emitente->numero;
                                        if (!empty($emitente->bairro)) $endereco_emitente[] = $emitente->bairro;
                                        echo implode(', ', $endereco_emitente);
                                        if (!empty($emitente->cidade)) {
                                            echo (!empty($endereco_emitente) ? ' - ' : '') . $emitente->cidade;
                                            if (!empty($emitente->uf)) echo '/' . $emitente->uf;
                                        }
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($emitente->telefone) || !empty($emitente->email)): ?>
                                <div class="client-meta">
                                    <?php if (!empty($emitente->telefone)): ?>
                                        <span class="meta-item">
                                            <i class="bx bx-phone"></i>
                                            <?php echo $emitente->telefone; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($emitente->email)): ?>
                                        <span class="meta-item">
                                            <i class="bx bx-envelope"></i>
                                            <?php echo $emitente->email; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Informações da OS -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-clipboard"></i> Informações da OS</h5>
                    <div class="info-row">
                        <div class="info-item">
                            <span class="info-label">OS Nº</span>
                            <span class="info-value">#<?php echo sprintf('%04d', $os->idOs ?? 0); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Data de Entrada</span>
                            <span class="info-value"><?php echo !empty($os->dataInicial) ? date('d/m/Y', strtotime($os->dataInicial)) : '-'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="status-badge status-finalizada"><?php echo $os->status ?? 'N/A'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Data de Finalização</span>
                            <span class="info-value"><?php echo !empty($os->dataFinal) ? date('d/m/Y', strtotime($os->dataFinal)) : 'Não definida'; ?></span>
                        </div>
                    </div>
                    <?php if (!empty($os->descricaoProduto)): ?>
                        <div class="info-row">
                            <div class="info-item" style="flex: 1;">
                                <span class="info-label">Descrição do Produto/Serviço</span>
                                <span class="info-value"><?php echo strip_tags($os->descricaoProduto ?? ''); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Informações do Cliente -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-user"></i> Cliente</h5>
                    <div class="client-card">
                        <div class="client-avatar">
                            <i class="bx bx-user"></i>
                        </div>
                        <div class="client-info">
                            <h4><?php echo htmlspecialchars($cliente->nomeCliente ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></h4>

                            <?php if (!empty($cliente->endereco)): ?>
                                <div class="client-meta">
                                    <span class="meta-item">
                                        <i class="bx bx-map"></i>
                                        <?php
                                        $endereco_cliente = [];
                                        if (!empty($cliente->endereco)) $endereco_cliente[] = $cliente->endereco;
                                        if (!empty($cliente->numero)) $endereco_cliente[] = $cliente->numero;
                                        if (!empty($cliente->bairro)) $endereco_cliente[] = $cliente->bairro;
                                        echo implode(', ', $endereco_cliente);
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($cliente->cidade) || !empty($cliente->estado) || !empty($cliente->cep)): ?>
                                <div class="client-meta">
                                    <span class="meta-item">
                                        <i class="bx bx-location-plus"></i>
                                        <?php
                                        $cidade_estado = [];
                                        if (!empty($cliente->cidade)) $cidade_estado[] = $cliente->cidade;
                                        if (!empty($cliente->estado)) $cidade_estado[] = $cliente->estado;
                                        if (!empty($cliente->cep)) $cidade_estado[] = 'CEP: ' . $cliente->cep;
                                        echo implode(' - ', $cidade_estado);
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($cliente->telefone) || !empty($cliente->celular)): ?>
                                <div class="client-meta">
                                    <span class="meta-item">
                                        <i class="bx bx-phone"></i>
                                        <?php
                                        $telefones = [];
                                        if (!empty($cliente->telefone)) $telefones[] = $cliente->telefone;
                                        if (!empty($cliente->celular)) $telefones[] = $cliente->celular;
                                        echo implode(' / ', $telefones);
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($cliente->email)): ?>
                                <div class="client-meta">
                                    <span class="meta-item">
                                        <i class="bx bx-envelope"></i>
                                        <?php echo $cliente->email; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Produtos -->
                <?php if (!empty($produtos)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-package"></i> Produtos Utilizados</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($produto->descricao ?? $produto->nome ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                                    <td><?php echo $produto->quantidade ?? 1; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Serviços -->
                <?php if (!empty($servicos)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-wrench"></i> Serviços Executados</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th width="120">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($servicos as $servico):
                                    $status = $servico->status ?? 'Pendente';
                                    $badge_class = 'badge-warning';
                                    if ($status == 'Executado' || $status == 'Concluido') $badge_class = 'badge-success';
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($servico->nome ?? $servico->descricao ?? 'Serviço', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                                    <td><span class="badge-status <?php echo $badge_class; ?>"><?php echo $status; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Timeline de Execuções -->
                <?php if (!empty($execucoes)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-time"></i> Histórico de Execução</h5>

                    <div class="timeline">
                        <?php foreach ($execucoes as $execucao): ?>
                        <div class="timeline-item">
                            <div class="timeline-data">
                                <i class="bx bx-log-in"></i> Check-in: <?php echo date('d/m/Y H:i', strtotime($execucao->checkin_horario)); ?>
                                <?php if ($execucao->checkout_horario): ?>
                                    <br><i class="bx bx-log-out"></i> Check-out: <?php echo date('d/m/Y H:i', strtotime($execucao->checkout_horario)); ?>
                                <?php endif; ?>
                            </div>

                            <?php if ($execucao->tempo_atendimento_minutos): ?>
                            <div class="timeline-detalhes">
                                <strong>Tempo de Atendimento:</strong> <?php echo floor($execucao->tempo_atendimento_minutos / 60) . 'h ' . ($execucao->tempo_atendimento_minutos % 60) . 'min'; ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($execucao->checklist_json):
                                $checklist = json_decode($execucao->checklist_json, true);
                                if (!empty($checklist['observacoes'])): ?>
                            <div class="timeline-detalhes">
                                <strong>Observações do Técnico:</strong><br>
                                <?php echo nl2br(htmlspecialchars($checklist['observacoes'])); ?>
                            </div>
                            <?php endif; endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Check-ins do Sistema -->
                <?php if (!empty($checkins)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-calendar-check"></i> Registros de Check-in</h5>
                    <div class="table-responsive">
                        <table class="checkin-table">
                            <thead>
                                <tr>
                                    <th>Data Entrada</th>
                                    <th>Data Saída</th>
                                    <th>Status</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($checkins as $checkin): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($checkin->data_entrada)); ?></td>
                                    <td><?php echo $checkin->data_saida ? date('d/m/Y H:i', strtotime($checkin->data_saida)) : '---'; ?></td>
                                    <td><span class="badge-status badge-success"><?php echo $checkin->status ?? 'Em Andamento'; ?></span></td>
                                    <td><?php echo htmlspecialchars($checkin->observacao_saida ?? $checkin->observacao_entrada ?? '-', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Fotos do Atendimento -->
                <?php if (!empty($fotosPorEtapa['entrada']) || !empty($fotosPorEtapa['durante']) || !empty($fotosPorEtapa['saida'])): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-camera"></i> Registro Fotográfico do Atendimento</h5>

                    <?php if (!empty($fotosPorEtapa['entrada'])): ?>
                    <div class="photos-section">
                        <div class="photos-section-title">📷 Fotos de Entrada</div>
                        <div class="fotos-grid">
                            <?php foreach ($fotosPorEtapa['entrada'] as $foto): ?>
                            <div class="foto-item">
                                <?php if (!empty($foto->imagem_base64)): ?>
                                    <img src="<?php echo 'data:image/jpeg;base64,' . $foto->imagem_base64; ?>" alt="Foto de entrada" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <img src="<?php echo $foto->url ?? $foto->path ?? ''; ?>" alt="Foto de entrada" loading="lazy" decoding="async">
                                <?php endif; ?>
                                <div class="foto-tipo">Entrada</div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($fotosPorEtapa['durante'])): ?>
                    <div class="photos-section">
                        <div class="photos-section-title">📷 Fotos Durante o Atendimento</div>
                        <div class="fotos-grid">
                            <?php foreach ($fotosPorEtapa['durante'] as $foto): ?>
                            <div class="foto-item">
                                <?php if (!empty($foto->imagem_base64)): ?>
                                    <img src="<?php echo 'data:image/jpeg;base64,' . $foto->imagem_base64; ?>" alt="Foto durante atendimento" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <img src="<?php echo $foto->url ?? $foto->path ?? ''; ?>" alt="Foto durante atendimento" loading="lazy" decoding="async">
                                <?php endif; ?>
                                <div class="foto-tipo">Durante</div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($fotosPorEtapa['saida'])): ?>
                    <div class="photos-section">
                        <div class="photos-section-title">📷 Fotos de Saída</div>
                        <div class="fotos-grid">
                            <?php foreach ($fotosPorEtapa['saida'] as $foto): ?>
                            <div class="foto-item">
                                <?php if (!empty($foto->imagem_base64)): ?>
                                    <img src="<?php echo 'data:image/jpeg;base64,' . $foto->imagem_base64; ?>" alt="Foto de saída" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <img src="<?php echo $foto->url ?? $foto->path ?? ''; ?>" alt="Foto de saída" loading="lazy" decoding="async">
                                <?php endif; ?>
                                <div class="foto-tipo">Saída</div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Fotos do Portal do Técnico -->
                <?php if (!empty($fotosTecnico)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-images"></i> Fotos do Técnico</h5>
                    <div class="fotos-grid">
                        <?php foreach ($fotosTecnico as $foto): ?>
                        <div class="foto-item">
                            <img src="<?php echo base_url($foto->caminho ?? $foto->foto ?? ''); ?>" alt="Foto do técnico" loading="lazy" decoding="async">
                            <div class="foto-tipo"><?php echo htmlspecialchars($foto->descricao ?? $foto->tipo ?? 'Foto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Assinaturas -->
                <?php if (!empty($assinaturasPorTipo)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-pen"></i> Assinaturas</h5>

                    <div class="row-fluid">
                        <?php if (isset($assinaturasPorTipo['tecnico_entrada'])): ?>
                        <div class="span6">
                            <div class="assinatura-box">
                                <h6>Assinatura do Técnico (Entrada)</h6>
                                <img src="<?php echo base_url($assinaturasPorTipo['tecnico_entrada']->assinatura ?? ''); ?>" alt="Assinatura do técnico" class="assinatura-img">
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($assinaturasPorTipo['cliente_saida'])): ?>
                        <div class="span6">
                            <div class="assinatura-box">
                                <h6>Assinatura do Cliente (Saída)</h6>
                                <img src="<?php echo base_url($assinaturasPorTipo['cliente_saida']->assinatura ?? ''); ?>" alt="Assinatura do cliente" class="assinatura-img">
                                <?php if (!empty($assinaturasPorTipo['cliente_saida']->nome_assinante)): ?>
                                    <p style="margin-top: 10px;"><strong>Assinado por:</strong> <?php echo htmlspecialchars($assinaturasPorTipo['cliente_saida']->nome_assinante, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<?php endif; ?>
