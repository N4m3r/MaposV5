<!-- Relatório de Execução - Portal do Técnico -->
<div id="content">
<style>
.portal-tecnico-content { margin-top: 15px !important; }
@media (max-width: 768px) { .portal-tecnico-content { margin-top: 10px !important; } }

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
.client-divider {
    color: #ccc;
    margin: 0 5px;
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

<div class="row-fluid portal-tecnico-content">
    <div class="span12">

        <!-- Header -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-file"></i></span>
                <h5>Relatório de Execução - OS #<?php echo $os->idOs; ?></h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Informações da Empresa (Emitente) -->
                <?php if (!empty($emitente)): ?>                <div class="relatorio-card">
                    <h5><i class="bx bx-building"></i> Empresa</h5>
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
                                        if (!empty($emitente->complemento)) $endereco_emitente[] = $emitente->complemento;
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
                            <span class="info-value">#<?php echo sprintf('%04d', $os->idOs); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Data de Entrada</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($os->dataInicial)); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="status-badge status-finalizada"><?php echo $os->status; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Data Prevista</span>
                            <span class="info-value"><?php echo !empty($os->dataFinal) ? date('d/m/Y', strtotime($os->dataFinal)) : 'Não definida'; ?></span>
                        </div>
                    </div>
                    <?php if ($os->descricaoProduto): ?>
                        <div class="info-row">
                            <div class="info-item" style="flex: 1;">
                                <span class="info-label">Descrição do Produto/Serviço</span>
                                <span class="info-value"><?php echo strip_tags($os->descricaoProduto); ?></span>
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
                                        <?php echo htmlspecialchars($cliente->endereco, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($cliente->telefone) || !empty($cliente->email)): ?>
                                <div class="client-meta">
                                    <?php if (!empty($cliente->telefone)): ?>
                                        <span class="meta-item">
                                            <i class="bx bx-phone"></i>
                                            <?php echo htmlspecialchars($cliente->telefone, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($cliente->telefone) && !empty($cliente->email)): ?>
                                        <span class="client-divider">|</span>
                                    <?php endif; ?>
                                    <?php if (!empty($cliente->email)): ?>
                                        <span class="meta-item">
                                            <i class="bx bx-envelope"></i>
                                            <?php echo htmlspecialchars($cliente->email, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($cliente->documento) || !empty($cliente->cpf) || !empty($cliente->cnpj)): ?>
                                <div class="client-meta">
                                    <span class="meta-item">
                                        <i class="bx bx-id-card"></i>
                                        <?php
                                        $doc = $cliente->documento ?? $cliente->cpf ?? $cliente->cnpj ?? '';
                                        echo 'CPF/CNPJ: ' . htmlspecialchars($doc, ENT_COMPAT | ENT_HTML5, 'UTF-8');
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Checkins do Sistema de Atendimento -->
                <?php if (!empty($checkins)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-time"></i> Histórico de Atendimentos</h5>
                    <table class="checkin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Técnico</th>
                                <th>Entrada</th>
                                <th>Saída</th>
                                <th>Tempo Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($checkins as $index => $checkin): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $checkin->nome_tecnico; ?></td>
                                    <td>
                                        <?php echo date('d/m/Y H:i', strtotime($checkin->data_entrada)); ?>
                                        <?php if ($checkin->latitude_entrada && $checkin->longitude_entrada): ?>
                                            <br><small>Loc: <?php echo $checkin->latitude_entrada . ', ' . $checkin->longitude_entrada; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($checkin->data_saida): ?>
                                            <?php echo date('d/m/Y H:i', strtotime($checkin->data_saida)); ?>
                                            <?php if ($checkin->latitude_saida && $checkin->longitude_saida): ?>
                                                <br><small>Loc: <?php echo $checkin->latitude_saida . ', ' . $checkin->longitude_saida; ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($checkin->data_saida) {
                                            $entrada = new DateTime($checkin->data_entrada);
                                            $saida = new DateTime($checkin->data_saida);
                                            $intervalo = $entrada->diff($saida);
                                            echo $intervalo->format('%h horas %i minutos');
                                        } else {
                                            echo 'Em andamento';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($checkin->data_saida): ?>
                                            <span class="badge-status badge-success">Finalizado</span>
                                        <?php else: ?>
                                            <span class="badge-status badge-warning">Em Andamento</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($checkin->observacao_entrada || $checkin->observacao_saida): ?>
                                    <tr style="background: #f9f9f9;">
                                        <td colspan="6">
                                            <?php if ($checkin->observacao_entrada): ?>
                                                <strong>Obs. Entrada:</strong> <?php echo nl2br($checkin->observacao_entrada); ?><br>
                                            <?php endif; ?>
                                            <?php if ($checkin->observacao_saida): ?>
                                                <strong>Obs. Saída:</strong> <?php echo nl2br($checkin->observacao_saida); ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Execuções do Portal do Técnico -->
                <?php if (!empty($execucoes)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-timer"></i> Execuções do Portal do Técnico</h5>
                    <div class="timeline">
                        <?php foreach ($execucoes as $exec): ?>
                            <div class="timeline-item">
                                <div class="timeline-data">
                                    <i class="bx bx-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($exec->checkin_horario)); ?>
                                </div>
                                <div class="timeline-titulo">
                                    Execução #<?php echo $exec->id; ?> - <?php echo $exec->tecnico_nome ?? 'Técnico'; ?>
                                </div>
                                <div class="timeline-detalhes">
                                    <?php if (!empty($exec->tempo_atendimento_minutos)): ?>
                                        <p><i class="bx bx-time"></i> Tempo: <?php
                                            $horas = floor($exec->tempo_atendimento_minutos / 60);
                                            $minutos = $exec->tempo_atendimento_minutos % 60;
                                            echo $horas . 'h ' . $minutos . 'min';
                                        ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($exec->checklist_json)):
                                        $checklist = json_decode($exec->checklist_json, true);
                                        if (!empty($checklist['observacoes'])): ?>
                                            <p><i class="bx bx-note"></i> Observações: <?php echo nl2br(htmlspecialchars($checklist['observacoes'], ENT_COMPAT | ENT_HTML5, 'UTF-8')); ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>                </div>
                <?php endif; ?>

                <!-- Serviços Executados -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-wrench"></i> Serviços da OS</h5>
                    <?php
                    // CORREÇÃO: Buscar serviços diretamente se a variável estiver vazia
                    if (empty($servicos)) {
                        $CI = &get_instance();
                        $CI->db->select('servicos_os.*, servicos.nome as servico_nome, servicos.codigo as servico_codigo');
                        $CI->db->from('servicos_os');
                        $CI->db->join('servicos', 'servicos.idServicos = servicos_os.servicos_id', 'left');
                        $CI->db->where('servicos_os.os_id', $os->idOs);
                        $query_servicos = $CI->db->get();
                        $servicos = $query_servicos ? $query_servicos->result() : [];
                    }
                    ?>
                    <?php if (!empty($servicos)): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th>Quantidade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($servicos as $servico): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($servico->servico_nome ?? $servico->nome ?? 'Serviço', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                            <?php if (!empty($servico->servico_codigo) || !empty($servico->codigo)): ?>
                                                <br><small class="text-muted">Código: <?php echo htmlspecialchars($servico->servico_codigo ?? $servico->codigo ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $servico->quantidade ?? 1; ?></td>
                                        <td>
                                            <?php
                                            $status = $servico->status ?? 'Pendente';
                                            $status_class = [
                                                'Pendente' => 'label label-warning',
                                                'EmExecucao' => 'label label-info',
                                                'Concluido' => 'label label-success',
                                                'Cancelado' => 'label label-important',
                                                'Executado' => 'label label-success',
                                                'NaoExecutado' => 'label label-important'
                                            ][$status] ?? 'label';
                                            ?>
                                            <span class="<?php echo $status_class; ?>"><?php echo $status; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-text">Nenhum serviço cadastrado</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Produtos Utilizados -->
                <div class="relatorio-card">
                    <h5><i class="bx bx-package"></i> Produtos/Materiais</h5>
                    <?php if (!empty($produtos)): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $produto): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($produto->descricao ?? 'Produto', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                                        <td><?php echo $produto->quantidade ?? 0; ?> <?php echo htmlspecialchars($produto->unidade ?? 'un', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-text">Nenhum produto utilizado</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Fotos do Portal do Técnico -->
                <?php if (!empty($fotosTecnico)): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-camera"></i> Fotos do Técnico</h5>
                    <div class="fotos-grid">
                        <?php foreach ($fotosTecnico as $foto): ?>
                            <div class="foto-item">
                                <img src="<?php echo base_url($foto->caminho); ?>"
                                     alt="Foto do técnico"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none; width: 100%; height: 150px; background: #f8f9fa; border: 2px dashed #ddd; border-radius: 8px; align-items: center; justify-content: center; flex-direction: column; color: #666;">
                                    <i class="bx bx-image" style="font-size: 2rem; margin-bottom: 8px;"></i>
                                    <span>Foto não disponível</span>
                                </div>
                                <div class="foto-tipo">
                                    <?php
                                    $tipo = $foto->tipo ?? 'foto';
                                    $tipos_label = [
                                        'checkin' => 'Check-in',
                                        'checkout' => 'Check-out',
                                        'antes' => 'Antes',
                                        'depois' => 'Depois',
                                        'problema' => 'Problema',
                                        'detalhe' => 'Detalhe',
                                        'durante' => 'Durante',
                                        'foto' => 'Foto'
                                    ];
                                    echo $tipos_label[$tipo] ?? ucfirst($tipo);
                                    ?>
                                    <?php if (!empty($foto->data_envio)): ?>
                                        <br><small><?php echo date('d/m/Y H:i', strtotime($foto->data_envio)); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Fotos do Sistema de Atendimento - Organizadas por Etapa -->
                <?php if (!empty($fotosPorEtapa['entrada']) || !empty($fotosPorEtapa['durante']) || !empty($fotosPorEtapa['saida'])): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-images"></i> Registro Fotográfico do Atendimento</h5>

                    <!-- Fotos de Entrada -->
                    <?php if (!empty($fotosPorEtapa['entrada'])): ?>
                    <div class="photos-section">
                        <div class="photos-section-title">📷 Fotos de Entrada</div>
                        <div class="fotos-grid">
                            <?php foreach ($fotosPorEtapa['entrada'] as $foto): ?>
                                <div class="foto-item">
                                    <?php
                                    $imgUrl = !empty($foto->imagem_base64)
                                        ? base_url('index.php/checkin/verFotoDB/' . $foto->idFoto)
                                        : $foto->url;
                                    ?>
                                    <img src="<?php echo $imgUrl; ?>"
                                         alt="Foto de entrada"
                                         loading="lazy"
                                         decoding="async"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display: none; width: 100%; height: 150px; background: #f8f9fa; border: 2px dashed #ddd; border-radius: 8px; align-items: center; justify-content: center; flex-direction: column; color: #666;">
                                        <i class="bx bx-image" style="font-size: 2rem; margin-bottom: 8px;"></i>
                                        <span>Foto não disponível</span>
                                    </div>
                                    <div class="foto-tipo">
                                        Entrada
                                        <?php if (!empty($foto->descricao)): ?>
                                            <br><small><?php echo htmlspecialchars($foto->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Fotos Durante -->
                    <?php if (!empty($fotosPorEtapa['durante'])): ?>
                    <div class="photos-section">
                        <div class="photos-section-title">📷 Fotos Durante o Atendimento</div>
                        <div class="fotos-grid">
                            <?php foreach ($fotosPorEtapa['durante'] as $foto): ?>
                                <div class="foto-item">
                                    <?php
                                    $imgUrl = !empty($foto->imagem_base64)
                                        ? base_url('index.php/checkin/verFotoDB/' . $foto->idFoto)
                                        : $foto->url;
                                    ?>
                                    <img src="<?php echo $imgUrl; ?>"
                                         alt="Foto durante atendimento"
                                         loading="lazy"
                                         decoding="async"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display: none; width: 100%; height: 150px; background: #f8f9fa; border: 2px dashed #ddd; border-radius: 8px; align-items: center; justify-content: center; flex-direction: column; color: #666;">
                                        <i class="bx bx-image" style="font-size: 2rem; margin-bottom: 8px;"></i>
                                        <span>Foto não disponível</span>
                                    </div>
                                    <div class="foto-tipo">
                                        Durante
                                        <?php if (!empty($foto->descricao)): ?>
                                            <br><small><?php echo htmlspecialchars($foto->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Fotos de Saída -->
                    <?php if (!empty($fotosPorEtapa['saida'])): ?>
                    <div class="photos-section">
                        <div class="photos-section-title">📷 Fotos de Saída</div>
                        <div class="fotos-grid">
                            <?php foreach ($fotosPorEtapa['saida'] as $foto): ?>
                                <div class="foto-item">
                                    <?php
                                    $imgUrl = !empty($foto->imagem_base64)
                                        ? base_url('index.php/checkin/verFotoDB/' . $foto->idFoto)
                                        : $foto->url;
                                    ?>
                                    <img src="<?php echo $imgUrl; ?>"
                                         alt="Foto de saída"
                                         loading="lazy"
                                         decoding="async"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display: none; width: 100%; height: 150px; background: #f8f9fa; border: 2px dashed #ddd; border-radius: 8px; align-items: center; justify-content: center; flex-direction: column; color: #666;">
                                        <i class="bx bx-image" style="font-size: 2rem; margin-bottom: 8px;"></i>
                                        <span>Foto não disponível</span>
                                    </div>
                                    <div class="foto-tipo">
                                        Saída
                                        <?php if (!empty($foto->descricao)): ?>
                                            <br><small><?php echo htmlspecialchars($foto->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Fotos do Sistema de Atendimento (backup - todas as fotos) -->
                <?php if (!empty($fotosAtendimento) && empty($fotosPorEtapa['entrada']) && empty($fotosPorEtapa['durante']) && empty($fotosPorEtapa['saida'])): ?>
                <div class="relatorio-card">
                    <h5><i class="bx bx-images"></i> Registro Fotográfico do Atendimento</h5>
                    <div class="fotos-grid">
                        <?php foreach ($fotosAtendimento as $foto): ?>
                            <div class="foto-item">
                                <img src="<?php echo $foto->url; ?>"
                                     alt="Foto de atendimento"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none; width: 100%; height: 150px; background: #f8f9fa; border: 2px dashed #ddd; border-radius: 8px; align-items: center; justify-content: center; flex-direction: column; color: #666;">
                                    <i class="bx bx-image" style="font-size: 2rem; margin-bottom: 8px;"></i>
                                    <span>Foto não disponível</span>
                                </div>
                                <div class="foto-tipo">
                                    <?php
                                    $etapa = $foto->etapa ?? 'foto';
                                    $etapas_label = [
                                        'entrada' => 'Entrada',
                                        'saida' => 'Saída',
                                        'durante' => 'Durante',
                                        'foto' => 'Foto'
                                    ];
                                    echo $etapas_label[$etapa] ?? ucfirst($etapa);
                                    ?>
                                    <?php if (!empty($foto->descricao)): ?>
                                        <br><small><?php echo htmlspecialchars($foto->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Assinaturas -->
                <?php log_info('View relatorio_execucao - Assinaturas recebidas: ' . count($assinaturas ?? [])); ?>

                <!-- DEBUG VISUAL - Assinaturas -->
                <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 15px; margin: 15px 0; font-family: monospace; font-size: 12px;">
                    <strong style="color: #856404;">🔍 DEBUG - Assinaturas:</strong><br>
                    <strong>Total de assinaturas:</strong> <?php echo count($assinaturas ?? []); ?><br>
                    <?php if (!empty($assinaturas)): ?>
                        <?php foreach ($assinaturas as $a): ?>
                            <strong>- ID <?php echo $a->idAssinatura; ?>:</strong> <?php echo $a->tipo; ?> | Path: <?php echo substr($a->assinatura, 0, 40); ?>...<br>
                            <strong>  URL:</strong> <a href="<?php echo $a->url_visualizacao; ?>" target="_blank"><?php echo $a->url_visualizacao; ?></a><br>
                            <strong>  Teste direto:</strong> <a href="<?php echo base_url('index.php/checkin/verAssinatura/' . $a->idAssinatura); ?>" target="_blank">Clique aqui para testar</a><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <strong style="color: #d32f2f;">⚠️ Nenhuma assinatura encontrada</strong>
                    <?php endif; ?>
                </div>

                <?php if (!empty($assinaturas) || !empty($execucoes)): ?>

                <div class="relatorio-card">
                    <h5><i class="bx bx-pencil"></i> Assinaturas</h5>
                    <div class="row-fluid">
                        <!-- Assinaturas do Sistema de Check-in -->
                        <?php if (!empty($assinaturas)): ?>
                            <?php foreach ($assinaturas as $assinatura):
                                $tipo_label = '';
                                switch ($assinatura->tipo) {
                                    case 'tecnico_entrada':
                                        $tipo_label = 'Técnico - Entrada';
                                        break;
                                    case 'tecnico_saida':
                                        $tipo_label = 'Técnico - Saída';
                                        break;
                                    case 'cliente_saida':
                                        $tipo_label = 'Cliente - Saída';
                                        break;
                                    default:
                                        $tipo_label = ucfirst(str_replace('_', ' ', $assinatura->tipo));
                                }
                            ?>
                                <div class="span6">
                                    <div class="assinatura-box">
                                        <h6><?php echo $tipo_label; ?></h6>
                                        <?php if (!empty($assinatura->assinatura)): ?>
                                            <?php
                                            // Sempre usar url_visualizacao que aponta para verAssinatura
                                            $img_src = $assinatura->url_visualizacao ?? base_url('index.php/checkin/verAssinatura/' . $assinatura->idAssinatura);
                                            log_info('View relatorio_execucao - Assinatura ID: ' . $assinatura->idAssinatura . ' - URL: ' . $img_src);
                                            ?>
                                            <!-- DEBUG URL: <?php echo $img_src; ?> -->
                                            <img src="<?php echo $img_src; ?>" alt="Assinatura <?php echo $tipo_label; ?>" class="assinatura-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <div style="display: none; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center; color: #666;">
                                                <i class="bx bx-image-alt" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                                Assinatura salva (erro ao carregar imagem)
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($assinatura->nome_assinante)): ?>
                                            <p style="margin-top: 10px; font-size: 0.9rem;">
                                                <strong>Assinado por:</strong> <?php echo htmlspecialchars($assinatura->nome_assinante, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($assinatura->data_assinatura)): ?>
                                            <p style="font-size: 0.8rem; color: #666;">
                                                <i class="bx bx-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($assinatura->data_assinatura)); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- DEBUG: Removido display duplicado do execucoes - assinatura agora vem apenas de $assinaturas -->
                    </div>
                </div>
                <?php endif; ?>

                <!-- Botão Voltar -->
                <div style="text-align: center; margin-top: 30px;">
                    <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-large">
                        <i class="bx bx-arrow-back"></i> Voltar para Minhas OS
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
</div>