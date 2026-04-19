<!-- Executar OS - Portal do Técnico -->
<div id="content">
<style>
.portal-tecnico-content { margin-top: 15px !important; }
@media (max-width: 768px) { .portal-tecnico-content { margin-top: 10px !important; } }
</style>

<div class="row-fluid portal-tecnico-content">
    <div class="span12">

        <!-- Header -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-play-circle"></i></span>
                <h5>Executar OS #<?php echo $os->idOs; ?></h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Informações da OS -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="os-details-card">
                            <div class="os-detail-row">
                                <div class="os-detail-item">
                                    <span class="detail-label"><i class="bx bx-hash"></i> OS Nº</span>
                                    <span class="detail-value"><?php echo $os->idOs; ?></span>
                                </div>
                                <div class="os-detail-item">
                                    <span class="detail-label"><i class="bx bx-calendar"></i> Data</span>
                                    <span class="detail-value"><?php echo date('d/m/Y', strtotime($os->dataInicial)); ?></span>
                                </div>
                                <div class="os-detail-item">
                                    <span class="detail-label"><i class="bx bx-flag"></i> Status</span>
                                    <span class="detail-value status-badge status-<?php echo strtolower(str_replace(' ', '_', $os->status)); ?>"><?php echo $os->status; ?></span>
                                </div>
                            </div>
                            <?php if ($os->descricaoProduto): ?>
                                <div class="os-detail-row full-width">
                                    <div class="os-detail-item block">
                                        <span class="detail-label"><i class="bx bx-detail"></i> Descrição</span>
                                        <span class="detail-value descricao-texto"><?php echo $os->descricaoProduto; ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($os->defeito): ?>
                                <div class="os-detail-row full-width">
                                    <div class="os-detail-item block alert-item">
                                        <span class="detail-label"><i class="bx bx-error-circle"></i> Problema Relatado</span>
                                        <span class="detail-value"><?php echo $os->defeito; ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($os->observacoes): ?>
                                <div class="os-detail-row full-width">
                                    <div class="os-detail-item block observacao-item">
                                        <span class="detail-label"><i class="bx bx-note"></i> Observações</span>
                                        <span class="detail-value"><?php echo $os->observacoes; ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($os->laudoTecnico): ?>
                                <div class="os-detail-row full-width">
                                    <div class="os-detail-item block laudo-item">
                                        <span class="detail-label"><i class="bx bx-wrench"></i> Laudo Técnico</span>
                                        <span class="detail-value"><?php echo $os->laudoTecnico; ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Informações do Cliente -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="client-card">
                            <div class="client-avatar">
                                <i class="bx bx-user"></i>
                            </div>
                            <div class="client-info">
                                <h4><?php echo $cliente ? htmlspecialchars($cliente->nomeCliente, ENT_COMPAT | ENT_HTML5, 'UTF-8') : 'Cliente não encontrado'; ?></h4>
                                <?php if ($cliente): ?>
                                    <div class="client-meta">
                                        <span class="meta-item"><i class="bx bx-map"></i> <?php echo htmlspecialchars($cliente->endereco ?? 'Endereço não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                                    </div>
                                    <?php if (!empty($cliente->telefone)): ?>
                                        <div class="client-meta">
                                            <span class="meta-item"><i class="bx bx-phone"></i> <?php echo htmlspecialchars($cliente->telefone, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($cliente->celular)): ?>
                                        <div class="client-meta">
                                            <span class="meta-item"><i class="bx bx-mobile"></i> <?php echo htmlspecialchars($cliente->celular, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($cliente->email)): ?>
                                        <div class="client-meta">
                                            <span class="meta-item"><i class="bx bx-envelope"></i> <?php echo htmlspecialchars($cliente->email, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card de Permissões -->
                <div id="permissoesCard" class="widget-box" style="margin-bottom: 20px;">
                    <div class="widget-title">
                        <span class="icon"><i class="bx bx-shield-alt-2"></i></span>
                        <h5>Permissões Necessárias</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <p style="margin-bottom: 15px; color: #666;">
                            Para melhor experiência, permita o acesso à <strong>Câmera</strong> e <strong>Localização</strong>.
                            <br><small>(Você pode continuar mesmo sem as permissões)</small>
                        </p>

                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                            <div id="statusCamera" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; text-align: center;">
                                <i class="bx bx-camera" style="font-size: 32px; color: #dc3545;"></i>
                                <p>Câmera</p>
                                <button type="button" class="btn btn-small" onclick="ativarCamera()" id="btnAtivarCamera">
                                    Permitir
                                </button>
                            </div>

                            <div id="statusGPS" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; text-align: center;">
                                <i class="bx bx-map" style="font-size: 32px; color: #dc3545;"></i>
                                <p>Localização</p>
                                <button type="button" class="btn btn-small" onclick="ativarGPS()" id="btnAtivarGPS">
                                    Permitir
                                </button>
                            </div>
                        </div>

                        <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 4px; font-size: 12px; color: #856404; display: none;">
                            <i class="bx bx-info-circle"></i>
                            <strong>Bloqueado pelo navegador?</strong> Clique no ícone 🔒 ao lado da URL e permita acesso à Câmera e Localização.
                        </div>
                    </div>
                </div>

                <!-- Botão de Check-in -->
                <div id="checkinSection" class="action-card <?php echo $execucao ? 'hidden' : ''; ?>">
                    <h5><i class="bx bx-map-pin"></i> Iniciar Atendimento</h5>

                    <div class="camera-section">
                        <div class="camera-preview" id="checkinPreview">
                            <i class="bx bx-camera"></i>
                            <span>Foto de Check-in (opcional)</span>
                        </div>
                        <div class="foto-options" style="display: flex; gap: 10px; justify-content: center;">
                            <button type="button" class="btn btn-info" onclick="capturarFotoCheckin()" id="btnFotoCheckin">
                                <i class="bx bx-camera"></i> Tirar Foto
                            </button>
                            <label class="btn btn-default" style="cursor: pointer;">
                                <i class="bx bx-upload"></i> Selecionar Arquivo
                                <input type="file" id="fileCheckin" accept="image/*" style="display: none;" onchange="uploadFotoCheckin(this)">
                            </label>
                        </div>
                        <small style="display: block; margin-top: 5px; color: #666; text-align: center;">A foto é opcional - você pode iniciar sem ela</small>
                    </div>

                    <button type="button" class="btn btn-success btn-large btn-block" onclick="iniciarExecucao()" id="btnIniciar">
                        <span class="spinner"></span>
                        <i class="bx bx-play-circle"></i>
                        <span class="text">Iniciar Execução</span>
                    </button>
                </div>

                <!-- Execução em Andamento -->
                <div id="execucaoSection" class=" <?php echo $execucao ? '' : 'hidden'; ?>">

                    <!-- Progresso -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-task"></i></span>
                            <h5>Progresso da Execução</h5>
                            <span class="label label-success">Em Execução</span>
                        </div>
                        <div class="widget-content">
                            <div class="progress">
                                <div class="bar bar-success" id="progressBar" style="width: <?php echo $execucao ? $execucao->checklist_completude : 0; ?>%"></div>
                            </div>
                            <div class="text-center progress-text" id="progressText">
                                <?php echo $execucao ? $execucao->checklist_completude : 0; ?>% concluído
                            </div>
                        </div>
                    </div>


                    <!-- Galeria de Fotos -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-camera"></i></span>
                            <h5>Fotos do Serviço</h5>
                        </div>
                        <div class="widget-content">
                            <div class="gallery-grid" id="galleryGrid">
                                <div class="gallery-add" onclick="abrirCamera()">
                                    <i class="bx bx-plus"></i>
                                    <span>Adicionar</span>
                                </div>

                                <?php if ($execucao && isset($execucao->fotos_galeria_json) && $execucao->fotos_galeria_json): ?>
                                    <?php $fotos = json_decode($execucao->fotos_galeria_json, true); ?>
                                    <?php if ($fotos): ?>
                                        <?php foreach ($fotos as $foto): ?>
                                            <div class="gallery-item">
                                                <img src="<?php echo base_url($foto['caminho'] ?? ''); ?>" alt="Foto">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Produtos da OS -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-package"></i></span>
                            <h5>Produtos/Materiais da OS</h5>
                        </div>
                        <div class="widget-content">
                            <div id="materiaisContainer">
                                <?php if (!empty($produtos)): ?>
                                    <div class="produtos-list">
                                        <?php foreach ($produtos as $produto): ?>
                                            <div class="produto-item">
                                                <div class="produto-info">
                                                    <div class="produto-nome">
                                                        <?php echo htmlspecialchars($produto->descricao ?? 'Produto sem descrição', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                                    </div>
                                                    <div class="produto-detalhes">
                                                        <span class="produto-qtd">Qtd: <?php echo $produto->quantidade ?? 0; ?> <?php echo htmlspecialchars($produto->unidade ?? 'un', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                                                        <span class="produto-preco">R$ <?php echo number_format($produto->preco ?? 0, 2, ',', '.'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <div class="empty-text">Nenhum produto cadastrado nesta OS</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Serviços da OS -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-wrench"></i></span>
                            <h5>Serviços da OS - Checklist</h5>
                        </div>
                        <div class="widget-content">
                            <div id="servicosContainer">
                                <?php if (!empty($servicos)): ?>
                                    <div class="servicos-list">
                                        <?php foreach ($servicos as $index => $servico): ?>
                                            <div class="servico-item checklist-item pendente" data-servico-id="<?php echo $servico->servicos_id ?? $index; ?>">
                                                <div class="servico-info">
                                                    <div class="servico-header">
                                                        <div class="checklist-checkbox" onclick="toggleServicoStatus(<?php echo $servico->servicos_id ?? $index; ?>)">
                                                            <i class="bx bx-circle"></i>
                                                        </div>
                                                        <div class="servico-nome">
                                                            <?php echo htmlspecialchars($servico->servico_nome ?? 'Serviço', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                                        </div>
                                                    </div>
                                                    <?php if ($servico->servico_codigo): ?>
                                                        <div class="servico-codigo">Código: <?php echo htmlspecialchars($servico->servico_codigo, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="servico-actions">
                                                    <button type="button" class="btn btn-mini" data-status="pendente"
                                                            onclick="setServicoStatus(<?php echo $servico->servicos_id ?? $index; ?>, 'pendente')">
                                                        <i class="bx bx-circle"></i> Pendente
                                                    </button>
                                                    <button type="button" class="btn btn-mini btn-success" data-status="conforme"
                                                            onclick="setServicoStatus(<?php echo $servico->servicos_id ?? $index; ?>, 'conforme')">
                                                        <i class="bx bx-check"></i> OK
                                                    </button>
                                                    <button type="button" class="btn btn-mini btn-danger" data-status="nao_conforme"
                                                            onclick="setServicoStatus(<?php echo $servico->servicos_id ?? $index; ?>, 'nao_conforme')">
                                                        <i class="bx bx-x"></i> Não OK
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <div class="empty-text">Nenhum serviço cadastrado nesta OS</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Assinatura -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-pencil"></i></span>
                            <h5>Assinatura do Cliente</h5>
                        </div>
                        <div class="widget-content">
                            <canvas id="signaturePad" class="signature-pad"></canvas>

                            <div class="row-fluid">
                                <div class="span6">
                                    <button type="button" class="btn btn-block" onclick="limparAssinatura()">
                                        <i class="bx bx-trash"></i> Limpar
                                    </button>
                                </div>
                            </div>

                            <div class="control-group" style="margin-top: 15px;">
                                <label class="control-label">Nome de quem assina</label>
                                <div class="controls">
                                    <input type="text" id="nomeAssinante" placeholder="Nome completo" class="span12">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-note"></i></span>
                            <h5>Observações do Técnico</h5>
                        </div>
                        <div class="widget-content">
                            <textarea id="observacoes" rows="4" class="span12" placeholder="Descreva o que foi realizado, problemas encontrados, recomendações..."></textarea>
                        </div>
                    </div>

                    <!-- Finalização -->
                    <div class="action-card">
                        <h5><i class="bx bx-camera"></i> Foto de Finalização (opcional)</h5>
                        <div class="camera-section">
                            <div class="camera-preview" id="checkoutPreview">
                                <i class="bx bx-camera"></i>
                                <span>Foto de Check-out</span>
                            </div>
                            <div class="foto-options" style="display: flex; gap: 10px; justify-content: center;">
                                <button type="button" class="btn btn-info" onclick="capturarFotoCheckout()" id="btnFotoCheckout">
                                    <i class="bx bx-camera"></i> Tirar Foto
                                </button>
                                <label class="btn btn-default" style="cursor: pointer;">
                                    <i class="bx bx-upload"></i> Selecionar Arquivo
                                    <input type="file" id="fileCheckout" accept="image/*" style="display: none;" onchange="uploadFotoCheckout(this)">
                                </label>
                            </div>
                            <small style="display: block; margin-top: 5px; color: #666; text-align: center;">A foto é opcional - você pode finalizar sem ela</small>
                        </div>

                        <button type="button" class="btn btn-success btn-large btn-block" onclick="finalizarExecucao()" id="btnFinalizar">
                            <span class="spinner"></span>
                            <i class="bx bx-check-circle"></i>
                            <span class="text">Finalizar OS</span>
                        </button>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<!-- Modal da Câmera -->
<div class="modal hide" id="cameraModal">
    <div class="modal-header">
        <button type="button" class="close" onclick="fecharCamera()">×</button>
        <h3>Adicionar Foto</h3>
    </div>
    <div class="modal-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="fotoTabs" style="margin-bottom: 15px;">
            <li class="active"><a href="#tabCamera" data-toggle="tab" onclick="iniciarCamera()"><i class="bx bx-camera"></i> Câmera</a></li>
            <li><a href="#tabUpload" data-toggle="tab"><i class="bx bx-upload"></i> Arquivo</a></li>
        </ul>

        <div class="tab-content">
            <!-- Tab Câmera -->
            <div class="tab-pane active" id="tabCamera">
                <video id="video" autoplay playsinline style="width: 100%; border-radius: 8px;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div id="cameraPreview" style="display: none; text-align: center; cursor: pointer;" onclick="retomarCamera()">
                    <img id="previewImg" style="max-width: 100%; border-radius: 8px;">
                    <p style="margin-top: 10px; color: #667eea; font-size: 0.9rem;"><i class="bx bx-refresh"></i> Clique para tirar outra foto</p>
                </div>
                <div id="cameraMensagem" style="margin-top: 10px; color: #666; text-align: center; display: none;">
                    <i class="bx bx-info-circle"></i> Câmera não disponível. Use a opção "Arquivo".
                </div>
            </div>

            <!-- Tab Upload -->
            <div class="tab-pane" id="tabUpload">
                <div class="upload-area" id="dropArea" style="border: 2px dashed #ccc; border-radius: 8px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s;" onclick="document.getElementById('fileFotoServico').click()">
                    <i class="bx bx-image-add" style="font-size: 48px; color: #667eea; margin-bottom: 10px;"></i>
                    <p style="margin: 0; color: #666;">Clique para selecionar uma imagem</p>
                    <p style="margin: 5px 0 0; font-size: 0.85rem; color: #999;">ou arraste e solte aqui</p>
                    <input type="file" id="fileFotoServico" accept="image/*" style="display: none;" onchange="previewArquivoServico(this)">
                </div>
                <div id="uploadPreview" style="display: none; margin-top: 15px; text-align: center;">
                    <img id="uploadPreviewImg" style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 1px solid #ddd;">
                    <p style="margin-top: 10px; color: #28a745;"><i class="bx bx-check-circle"></i> Imagem selecionada</p>
                </div>
            </div>
        </div>

        <div class="control-group" style="margin-top: 15px;">
            <label>Tipo da foto</label>
            <select id="tipoFoto" class="span12">
                <option value="antes">Antes do serviço</option>
                <option value="depois">Depois do serviço</option>
                <option value="problema">Problema encontrado</option>
                <option value="detalhe">Detalhe técnico</option>
            </select>
        </div>

        <div class="control-group">
            <label>Descrição (opcional)</label>
            <input type="text" id="descricaoFoto" placeholder="Descreva a foto" class="span12">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" onclick="fecharCamera()">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="salvarFotoServico()">
            <i class="bx bx-save"></i> Salvar Foto
        </button>
    </div>
</div>

<style>
/* OS Details Card */
.os-details-card {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.os-detail-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.os-detail-row.full-width {
    width: 100%;
}

.os-detail-row:last-child {
    margin-bottom: 0;
}

.os-detail-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.os-detail-item.block {
    flex: 1;
    width: 100%;
}

.detail-label {
    font-size: 0.8rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-label i {
    margin-right: 5px;
    color: #667eea;
}

.detail-value {
    font-size: 1rem;
    color: #333;
    font-weight: 500;
}

.detail-value.descricao-texto {
    font-weight: normal;
    line-height: 1.6;
    color: #555;
}

.detail-value p {
    margin: 0 0 10px 0;
}

.detail-value p:last-child {
    margin-bottom: 0;
}

.detail-value br {
    display: block;
    content: "";
    margin-top: 5px;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-badge.status-aberto {
    background: #e3f2fd;
    color: #1976d2;
}

.status-badge.status-em_andamento {
    background: #fff3e0;
    color: #ef6c00;
}

.status-badge.status-finalizada {
    background: #e8f5e9;
    color: #2e7d32;
}

.alert-item {
    background: #ffebee;
    border-color: #ffcdd2;
}

.alert-item .detail-label {
    color: #c62828;
}

.observacao-item {
    background: #fff8e1;
    border-color: #ffecb3;
}

.laudo-item {
    background: #e8f5e9;
    border-color: #c8e6c9;
}

/* Cliente Card */
.client-card {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 20px;
}

.client-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
}

.client-info h4 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.2rem;
}

.client-meta {
    margin: 5px 0;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 0.95rem;
}

.meta-item i {
    color: #667eea;
}

/* Action Card */
.action-card {
    background: #f8f9fa;
    border: 2px dashed #ddd;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    text-align: center;
}

.action-card h5 {
    margin: 0 0 15px 0;
    color: #333;
}

.action-card h5 i {
    color: #667eea;
}

/* Camera Section */
.camera-section {
    margin-bottom: 15px;
}

.camera-preview {
    width: 200px;
    height: 200px;
    border-radius: 12px;
    background: #e0e0e0;
    margin: 0 auto 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
    overflow: hidden;
}

.camera-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.camera-preview i {
    font-size: 48px;
}

/* Checklist */
.checklist-item {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s;
}

.checklist-item:hover {
    border-color: #667eea;
}

.checklist-item.conforme {
    border-color: #4caf50;
    background: #f1f8e9;
}

.checklist-item.nao_conforme {
    border-color: #f44336;
    background: #ffebee;
}

.checklist-header {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
}

.checklist-checkbox {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #999;
}

.checklist-item.conforme .checklist-checkbox {
    background: #4caf50;
    color: white;
}

.checklist-item.nao_conforme .checklist-checkbox {
    background: #f44336;
    color: white;
}

.checklist-text h4 {
    margin: 0 0 3px 0;
    font-size: 0.95rem;
}

.checklist-servico {
    margin: 0;
    font-size: 0.8rem;
    color: #888;
}

.checklist-actions {
    display: flex;
    gap: 10px;
}

.checklist-actions .btn {
    flex: 1;
}

/* Gallery */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;
}

.gallery-item {
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    background: #e0e0e0;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-add {
    aspect-ratio: 1;
    border: 2px dashed #667eea;
    border-radius: 8px;
    background: rgba(102, 126, 234, 0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #667eea;
    transition: all 0.3s;
}

.gallery-add:hover {
    background: rgba(102, 126, 234, 0.1);
}

.gallery-add i {
    font-size: 24px;
    margin-bottom: 5px;
}

/* Produtos da OS */
.produtos-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.produto-item {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px;
    transition: all 0.3s;
}

.produto-item:hover {
    border-color: #667eea;
    background: #fff;
}

.produto-nome {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.produto-detalhes {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
    color: #666;
}

.produto-qtd {
    background: #e3f2fd;
    color: #1976d2;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.produto-preco {
    font-weight: 600;
    color: #2e7d32;
}

/* Serviços da OS - Checklist */
.servicos-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.servico-item {
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    transition: all 0.3s;
}

.servico-item:hover {
    border-color: #667eea;
}

.servico-item.conforme {
    border-color: #4caf50;
    background: #f1f8e9;
}

.servico-item.nao_conforme {
    border-color: #f44336;
    background: #ffebee;
}

.servico-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}

.servico-item .checklist-checkbox {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #999;
    cursor: pointer;
    transition: all 0.3s;
}

.servico-item .checklist-checkbox:hover {
    background: #e0e0e0;
}

.servico-item.conforme .checklist-checkbox {
    background: #4caf50;
    color: white;
}

.servico-item.nao_conforme .checklist-checkbox {
    background: #f44336;
    color: white;
}

.servico-nome {
    font-weight: 600;
    color: #333;
    flex: 1;
}

.servico-codigo {
    font-size: 0.8rem;
    color: #888;
    margin-left: 44px;
}

.servico-actions {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    margin-left: 44px;
}

.servico-actions .btn {
    flex: 1;
}

.servico-actions .btn.active {
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
}

/* Estoque do Técnico */
.estoque-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.estoque-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.estoque-info {
    flex: 1;
}

.estoque-nome {
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
}

.estoque-qtd {
    background: #667eea;
    color: white;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
}

/* Signature Pad */
.signature-pad {
    width: 100%;
    height: 200px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    margin-bottom: 15px;
    cursor: crosshair;
}

/* Spinner */
.spinner {
    display: none;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.btn.loading .spinner {
    display: inline-block;
}

.btn.loading .text,
.btn.loading i:not(.spinner) {
    display: none;
}

/* Estados vazios */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #999;
}

.empty-text {
    margin: 0;
}

.hidden {
    display: none !important;
}

/* Progress text */
.progress-text {
    margin-top: 10px;
    color: #666;
    font-size: 0.9rem;
}

/* Responsividade Mobile */
@media (max-width: 768px) {
    .client-card {
        flex-direction: column;
        text-align: center;
    }

    .client-avatar {
        margin: 0 auto;
    }

    .os-detail-row {
        flex-direction: column;
        gap: 10px;
    }

    .checklist-header {
        flex-direction: column;
    }

    .gallery-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .checklist-actions {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .signature-pad {
        height: 150px;
    }
}
</style>

<script>
let execucaoId = <?php echo $execucao ? $execucao->id : 'null'; ?>;
let osId = <?php echo $os->idOs; ?>;
let latitude, longitude;
let fotoCheckin = null;
let fotoCheckout = null;
let stream = null;

// Obter localização (opcional - silencia erros de permissão)
if ('geolocation' in navigator) {
    navigator.geolocation.watchPosition(
        (pos) => {
            latitude = pos.coords.latitude;
            longitude = pos.coords.longitude;
            permissaoGPS = true;
        },
        (err) => {
            permissaoGPS = false;
        },
        { enableHighAccuracy: true }
    );
}

// ============ SISTEMA DE PERMISSÕES ============
let permissaoCamera = false;
let permissaoGPS = false;

// Solicitar permissão de câmera explicitamente
async function solicitarPermissaoCamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        return false;
    }
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        stream.getTracks().forEach(track => track.stop());
        permissaoCamera = true;
        return true;
    } catch (err) {
        permissaoCamera = false;
        return false;
    }
}

// Solicitar permissão de GPS explicitamente
async function solicitarPermissaoGPS() {
    if (!('geolocation' in navigator)) {
        return false;
    }
    return new Promise((resolve) => {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                latitude = pos.coords.latitude;
                longitude = pos.coords.longitude;
                permissaoGPS = true;
                resolve(true);
            },
            (err) => {
                permissaoGPS = false;
                resolve(false);
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });
}

// Verificar status de permissões
async function verificarPermissoes() {
    if (navigator.permissions) {
        try {
            const camResult = await navigator.permissions.query({ name: 'camera' });
            permissaoCamera = camResult.state === 'granted';
            if (permissaoCamera) {
                atualizarCardCamera(true);
            }
            camResult.onchange = () => {
                permissaoCamera = camResult.state === 'granted';
                atualizarCardCamera(permissaoCamera);
            };
        } catch(e) {}
    }
    // Solicitar GPS
    const gpsOk = await solicitarPermissaoGPS();
    if (gpsOk) {
        atualizarCardGPS(true);
    }

    // Verificar se deve ocultar card
    verificarOcultarCard();
}

// Atualizar visual do card de câmera
function atualizarCardCamera(permitido) {
    const card = document.getElementById('statusCamera');
    const btn = document.getElementById('btnAtivarCamera');
    const icon = card.querySelector('i');

    if (permitido) {
        icon.style.color = '#28a745';
        btn.className = 'btn btn-small btn-success';
        btn.innerHTML = '<i class="bx bx-check"></i> Permitida';
        btn.disabled = true;
        card.style.borderColor = '#28a745';
        card.style.background = '#f0f9f4';
    }
}

// Atualizar visual do card de GPS
function atualizarCardGPS(permitido) {
    const card = document.getElementById('statusGPS');
    const btn = document.getElementById('btnAtivarGPS');
    const icon = card.querySelector('i');

    if (permitido) {
        icon.style.color = '#28a745';
        btn.className = 'btn btn-small btn-success';
        btn.innerHTML = '<i class="bx bx-check"></i> Permitida';
        btn.disabled = true;
        card.style.borderColor = '#28a745';
        card.style.background = '#f0f9f4';
    }
}

// Ocultar card de permissões se ambas concedidas
function verificarOcultarCard() {
    if (permissaoCamera && permissaoGPS) {
        const card = document.getElementById('permissoesCard');
        if (card) {
            card.style.display = 'none';
        }
    }
}

// Verificar permissões ao carregar
verificarPermissoes();

// Funções para ativar permissões pelo botão
async function ativarCamera() {
    const btn = document.getElementById('btnAtivarCamera');
    const card = document.getElementById('statusCamera');
    const icon = card.querySelector('i');

    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Solicitando...';
    btn.disabled = true;

    const resultado = await solicitarPermissaoCamera();

    if (resultado) {
        icon.style.color = '#28a745';
        btn.className = 'btn btn-small btn-success';
        btn.innerHTML = '<i class="bx bx-check"></i> Permitida';
        btn.disabled = true;
        card.style.borderColor = '#28a745';
        card.style.background = '#f0f9f4';
    } else {
        icon.style.color = '#dc3545';
        btn.className = 'btn btn-small btn-danger';
        btn.innerHTML = '<i class="bx bx-x"></i> Negada - Tentar';
        btn.disabled = false;
        card.style.borderColor = '#dc3545';
        card.style.background = '#fff5f5';
        alert('Permissão de câmera negada.\n\nPara permitir:\n1. Clique no 🔒 (cadeado) ao lado da URL\n2. Clique em "Permitir" para Câmera\n3. Recarregue a página');
    }
}

async function ativarGPS() {
    const btn = document.getElementById('btnAtivarGPS');
    const card = document.getElementById('statusGPS');
    const icon = card.querySelector('i');

    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Solicitando...';
    btn.disabled = true;

    const resultado = await solicitarPermissaoGPS();

    if (resultado) {
        icon.style.color = '#28a745';
        btn.className = 'btn btn-small btn-success';
        btn.innerHTML = '<i class="bx bx-check"></i> Permitida';
        btn.disabled = true;
        card.style.borderColor = '#28a745';
        card.style.background = '#f0f9f4';
    } else {
        icon.style.color = '#dc3545';
        btn.className = 'btn btn-small btn-danger';
        btn.innerHTML = '<i class="bx bx-x"></i> Negada - Tentar';
        btn.disabled = false;
        card.style.borderColor = '#dc3545';
        card.style.background = '#fff5f5';
        alert('Permissão de localização negada.\n\nPara permitir:\n1. Clique no 🔒 (cadeado) ao lado da URL\n2. Clique em "Permitir" para Localização\n3. Recarregue a página');
    }
}

// Canvas de assinatura
const canvas = document.getElementById('signaturePad');
const ctx = canvas.getContext('2d');
let isDrawing = false;

function resizeCanvas() {
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
}

window.addEventListener('load', resizeCanvas);
window.addEventListener('resize', resizeCanvas);

canvas.addEventListener('mousedown', startDrawing);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDrawing);
canvas.addEventListener('mouseout', stopDrawing);

canvas.addEventListener('touchstart', (e) => {
    e.preventDefault();
    startDrawing(e.touches[0]);
});
canvas.addEventListener('touchmove', (e) => {
    e.preventDefault();
    draw(e.touches[0]);
});
canvas.addEventListener('touchend', stopDrawing);

function startDrawing(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
}

function draw(e) {
    if (!isDrawing) return;
    const rect = canvas.getBoundingClientRect();
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
}

function stopDrawing() {
    isDrawing = false;
}

function limparAssinatura() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// Câmera - OPCIONAL
async function capturarFotoCheckin() {
    const preview = document.getElementById('checkinPreview');

    // Verificar se a API de câmera está disponível
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Câmera não disponível neste dispositivo. Use a opção de Selecionar Arquivo.');
        return;
    }

    // Se não tem permissão, solicitar primeiro
    if (!permissaoCamera) {
        const resultado = await solicitarPermissaoCamera();
        if (!resultado) {
            alert('Câmera não permitida.\n\nPara permitir:\n1. Clique no 🔒 ao lado da URL\n2. Permita Câmera\n3. Ou use o botão "Selecionar Arquivo"\n\nVocê pode continuar sem foto.');
            return;
        }
    }

    try {
        const mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        const video = document.createElement('video');
        video.srcObject = mediaStream;
        video.autoplay = true;

        await new Promise(resolve => video.onloadedmetadata = resolve);
        await new Promise(r => setTimeout(r, 500));

        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = video.videoWidth;
        tempCanvas.height = video.videoHeight;
        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.drawImage(video, 0, 0);

        fotoCheckin = tempCanvas.toDataURL('image/jpeg', 0.8);
        preview.innerHTML = `<img src="${fotoCheckin}">`;

        mediaStream.getTracks().forEach(track => track.stop());
    } catch (err) {
        // Foto opcional - mostra mensagem amigável
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            alert('Permissão de câmera negada. A foto é opcional - você pode continuar sem ela.');
        } else {
            alert('Câmera não disponível. Você pode continuar sem foto.');
        }
        console.log('Câmera opcional - erro silenciado:', err.message);
    }
}

let fotoServicoBase64 = null;
let abaAtiva = 'camera';

// Evento para mudança de aba
jQuery(document).on('shown', '#fotoTabs a[data-toggle="tab"]', function (e) {
    const target = jQuery(e.target).attr('href');
    if (target === '#tabUpload') {
        abaAtiva = 'upload';
        // Parar câmera para economizar recursos
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        setupDragDrop();
    } else {
        abaAtiva = 'camera';
        iniciarCamera();
    }
});

// Setup drag and drop
function setupDragDrop() {
    const dropArea = document.getElementById('dropArea');
    if (!dropArea) return;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    dropArea.addEventListener('drop', handleDrop, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    const dropArea = document.getElementById('dropArea');
    dropArea.style.borderColor = '#667eea';
    dropArea.style.background = 'rgba(102,126,234,0.05)';
}

function unhighlight(e) {
    const dropArea = document.getElementById('dropArea');
    dropArea.style.borderColor = '#ccc';
    dropArea.style.background = 'transparent';
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;

    if (files.length > 0) {
        const fileInput = document.getElementById('fileFotoServico');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(files[0]);
        fileInput.files = dataTransfer.files;
        previewArquivoServico(fileInput);
    }
}

function retomarCamera() {
    fotoServicoBase64 = null;
    document.getElementById('cameraPreview').style.display = 'none';
    document.getElementById('video').style.display = 'block';
    iniciarCamera();
}

async function iniciarCamera() {
    abaAtiva = 'camera';
    document.getElementById('cameraPreview').style.display = 'none';
    document.getElementById('video').style.display = 'block';

    // Se não tem permissão, tentar solicitar
    if (!permissaoCamera) {
        const resultado = await solicitarPermissaoCamera();
        if (!resultado) {
            document.getElementById('cameraMensagem').style.display = 'block';
            document.getElementById('cameraMensagem').innerHTML = '<i class="bx bx-info-circle"></i> Câmera não permitida. Use a aba "Arquivo" para enviar fotos.';
            document.getElementById('video').style.display = 'none';
            return;
        }
    }

    if (!stream) {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            document.getElementById('video').srcObject = stream;
            document.getElementById('cameraMensagem').style.display = 'none';
        } catch (err) {
            console.error('Erro ao abrir câmera:', err);
            document.getElementById('cameraMensagem').style.display = 'block';
            document.getElementById('cameraMensagem').innerHTML = '<i class="bx bx-error-circle"></i> Erro ao acessar câmera. Use a aba "Arquivo".';
            document.getElementById('video').style.display = 'none';
        }
    }
}

async function abrirCamera() {
    const modal = document.getElementById('cameraModal');
    modal.removeAttribute('aria-hidden');
    jQuery('#cameraModal').modal('show');
    fotoServicoBase64 = null;
    abaAtiva = 'camera';

    // Reset previews
    document.getElementById('cameraPreview').style.display = 'none';
    document.getElementById('video').style.display = 'block';
    document.getElementById('uploadPreview').style.display = 'none';
    document.getElementById('fileFotoServico').value = '';

    // Tentar iniciar câmera
    await iniciarCamera();
}

function fecharCamera() {
    jQuery('#cameraModal').modal('hide');
    document.getElementById('cameraModal').setAttribute('aria-hidden', 'true');
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    fotoServicoBase64 = null;
    document.getElementById('uploadPreview').style.display = 'none';
    document.getElementById('cameraPreview').style.display = 'none';
    document.getElementById('fileFotoServico').value = '';
}

function tirarFoto() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');

    if (!video.videoWidth) {
        alert('Câmera não está pronta. Aguarde ou use a opção Arquivo.');
        return;
    }

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);

    fotoServicoBase64 = canvas.toDataURL('image/jpeg', 0.8);

    // Mostrar preview
    document.getElementById('video').style.display = 'none';
    document.getElementById('previewImg').src = fotoServicoBase64;
    document.getElementById('cameraPreview').style.display = 'block';
}

function previewArquivoServico(input) {
    const file = input.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Por favor, selecione uma imagem válida.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        fotoServicoBase64 = e.target.result;
        document.getElementById('uploadPreviewImg').src = fotoServicoBase64;
        document.getElementById('uploadPreview').style.display = 'block';
        abaAtiva = 'upload';
    };
    reader.readAsDataURL(file);
}

async function salvarFotoServico() {
    if (!fotoServicoBase64) {
        if (abaAtiva === 'camera') {
            // Se estiver na aba câmera e não tiver foto, tenta tirar agora
            tirarFoto();
            if (!fotoServicoBase64) return;
        } else {
            alert('Selecione uma imagem primeiro.');
            return;
        }
    }

    const tipo = document.getElementById('tipoFoto').value;
    const descricao = document.getElementById('descricaoFoto').value;

    const csrf = getCsrfToken();
    const formData = new FormData();
    formData.append('execucao_id', execucaoId);
    formData.append('foto', fotoServicoBase64);
    formData.append('tipo', tipo);
    formData.append('descricao', descricao);
    formData.append('latitude', latitude || 0);
    formData.append('longitude', longitude || 0);
    formData.append(csrf.name, csrf.value);

    const btn = document.querySelector('#cameraModal .btn-primary');
    const btnOriginalText = btn.innerHTML;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Salvando...';
    btn.disabled = true;

    try {
        const response = await fetch('<?php echo site_url("tecnicos/adicionar_foto"); ?>', {
            method: 'POST',
            body: formData
        });

        // Verificar se resposta é OK
        if (!response.ok) {
            const text = await response.text();
            console.error('Erro HTTP:', response.status, text.substring(0, 500));
            alert('Erro do servidor: ' + response.status + '. Verifique o console.');
            return;
        }

        const data = await response.json();

        if (data.success) {
            const grid = document.getElementById('galleryGrid');
            const item = document.createElement('div');
            item.className = 'gallery-item';
            item.innerHTML = `<img src="${fotoServicoBase64}" alt="Foto">`;
            grid.insertBefore(item, grid.children[1]);

            fecharCamera();
            document.getElementById('descricaoFoto').value = '';
            fotoServicoBase64 = null;
        } else {
            alert('Erro ao salvar foto: ' + (data.message || 'Erro desconhecido'));
        }
    } catch (err) {
        console.error('Erro completo:', err);
        alert('Erro ao enviar foto: ' + err.message);
    } finally {
        btn.innerHTML = btnOriginalText;
        btn.disabled = false;
    }
}

async function capturarFotoCheckout() {
    const preview = document.getElementById('checkoutPreview');

    // Verificar se a API de câmera está disponível
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Câmera não disponível neste dispositivo. Você pode continuar sem foto.');
        return;
    }

    try {
        const mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        const video = document.createElement('video');
        video.srcObject = mediaStream;
        video.autoplay = true;

        await new Promise(resolve => video.onloadedmetadata = resolve);
        await new Promise(r => setTimeout(r, 500));

        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = video.videoWidth;
        tempCanvas.height = video.videoHeight;
        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.drawImage(video, 0, 0);

        fotoCheckout = tempCanvas.toDataURL('image/jpeg', 0.8);
        preview.innerHTML = `<img src="${fotoCheckout}">`;

        mediaStream.getTracks().forEach(track => track.stop());
    } catch (err) {
        // Foto opcional - mostra mensagem amigável
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            alert('Permissão de câmera negada. A foto é opcional - você pode continuar sem ela.');
        } else {
            alert('Câmera não disponível. Você pode continuar sem foto.');
        }
        console.log('Câmera opcional - erro silenciado:', err.message);
    }
}

// Upload de arquivo para Check-in
function uploadFotoCheckin(input) {
    const preview = document.getElementById('checkinPreview');
    const file = input.files[0];

    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Por favor, selecione uma imagem válida.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        fotoCheckin = e.target.result;
        preview.innerHTML = `<img src="${fotoCheckin}" style="max-width: 100%; max-height: 100%;">`;
    };
    reader.readAsDataURL(file);
}

// Upload de arquivo para Check-out
function uploadFotoCheckout(input) {
    const preview = document.getElementById('checkoutPreview');
    const file = input.files[0];

    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Por favor, selecione uma imagem válida.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        fotoCheckout = e.target.result;
        preview.innerHTML = `<img src="${fotoCheckout}" style="max-width: 100%; max-height: 100%;">`;
    };
    reader.readAsDataURL(file);
}

// Helper para obter CSRF token
function getCsrfToken() {
    const tokenName = '<?php echo $this->config->item('csrf_token_name'); ?>';
    const cookieName = '<?php echo $this->config->item('csrf_cookie_name'); ?>';
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim();
        if (cookie.indexOf(cookieName + '=') === 0) {
            return { name: tokenName, value: cookie.substring(cookieName.length + 1) };
        }
    }
    return { name: tokenName, value: '' };
}

// Execução
async function iniciarExecucao() {
    // Localização é opcional - usa valores padrão se não disponível
    const lat = latitude || 0;
    const lng = longitude || 0;

    const btn = document.getElementById('btnIniciar');
    btn.classList.add('loading');
    btn.disabled = true;

    const csrf = getCsrfToken();
    const formData = new FormData();
    formData.append('os_id', osId);
    formData.append('latitude', lat);
    formData.append('longitude', lng);
    formData.append('foto_checkin', fotoCheckin || '');
    formData.append('tipo', 'inicio_local');
    formData.append(csrf.name, csrf.value);

    console.log('Enviando requisição...', { os_id: osId, lat, lng, csrf_name: csrf.name });

    try {
        const response = await fetch('<?php echo site_url("tecnicos/iniciar_execucao"); ?>', {
            method: 'POST',
            body: formData
        });

        const responseText = await response.text();
        console.log('Resposta raw:', responseText.substring(0, 500));

        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Resposta não é JSON válido:', responseText);
            alert('Erro no servidor. Verifique o console para detalhes.');
            return;
        }

        if (data.success) {
            if (!data.execucao_id || data.execucao_id == 0) {
                alert('Erro: ID da execução não retornado corretamente. Por favor, recarregue a página.');
                console.error('execucao_id inválido:', data.execucao_id);
                return;
            }
            execucaoId = data.execucao_id;
            console.log('Execução iniciada com ID:', execucaoId);
            document.getElementById('checkinSection').classList.add('hidden');
            document.getElementById('execucaoSection').classList.remove('hidden');
            window.scrollTo(0, 0);
        } else {
            alert('Erro: ' + data.message);
        }
    } catch (err) {
        alert('Erro ao iniciar execução: ' + err.message);
        console.error('Erro:', err);
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
}

// Serviços Checklist
let servicosStatus = {};

function toggleServicoStatus(servicoId) {
    const item = document.querySelector(`[data-servico-id="${servicoId}"]`);
    if (!item) return;

    const currentStatus = servicosStatus[servicoId] || 'pendente';
    let newStatus;

    if (currentStatus === 'pendente') {
        newStatus = 'conforme';
    } else if (currentStatus === 'conforme') {
        newStatus = 'nao_conforme';
    } else {
        newStatus = 'pendente';
    }

    setServicoStatus(servicoId, newStatus);
}

function setServicoStatus(servicoId, status) {
    const item = document.querySelector(`[data-servico-id="${servicoId}"]`);
    if (!item) return;

    servicosStatus[servicoId] = status;

    // Update visual state
    item.classList.remove('pendente', 'conforme', 'nao_conforme');
    item.classList.add(status);

    // Update checkbox icon
    const checkbox = item.querySelector('.checklist-checkbox i');
    if (checkbox) {
        if (status === 'conforme') {
            checkbox.className = 'bx bx-check';
        } else if (status === 'nao_conforme') {
            checkbox.className = 'bx bx-x';
        } else {
            checkbox.className = 'bx bx-circle';
        }
    }

    // Update button states
    const buttons = item.querySelectorAll('.servico-actions .btn');
    buttons.forEach(btn => {
        btn.classList.remove('btn-success', 'btn-danger', 'active');
        const btnStatus = btn.getAttribute('data-status');
        if (btnStatus === status) {
            btn.classList.add('active');
            if (status === 'conforme') btn.classList.add('btn-success');
            if (status === 'nao_conforme') btn.classList.add('btn-danger');
        }
    });

    atualizarProgressoServicos();
}

function atualizarProgressoServicos() {
    const items = document.querySelectorAll('.servico-item[data-servico-id]');
    const total = items.length;
    let concluidos = 0;

    items.forEach(item => {
        const servicoId = item.getAttribute('data-servico-id');
        const status = servicosStatus[servicoId] || 'pendente';
        if (status === 'conforme' || status === 'nao_conforme') {
            concluidos++;
        }
    });

    const progresso = total > 0 ? Math.round((concluidos / total) * 100) : 0;

    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    if (progressBar) progressBar.style.width = progresso + '%';
    if (progressText) progressText.textContent = progresso + '% concluído';
}

// Checklist (mantido para compatibilidade)
async function salvarChecklistItem(itemId, status) {
    if (!execucaoId) return;

    const csrf = getCsrfToken();
    const formData = new FormData();
    formData.append('execucao_id', execucaoId);
    formData.append('item_id', itemId);
    formData.append('status', status);
    formData.append('observacao', '');
    formData.append('valor', '');
    formData.append(csrf.name, csrf.value);

    try {
        const response = await fetch('<?php echo site_url("tecnicos/salvar_checklist_item"); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            const item = document.querySelector(`[data-item-id="${itemId}"]`);
            if (!item) {
                console.error('Item do checklist nao encontrado:', itemId);
                return;
            }
            item.classList.remove('conforme', 'nao_conforme');
            item.classList.add(status === 'conforme' ? 'conforme' : 'nao_conforme');

            const checkbox = item.querySelector('.checklist-checkbox');
            if (checkbox) {
                checkbox.innerHTML = status === 'conforme' ? '<i class="bx bx-check"></i>' : '<i class="bx bx-x"></i>';
            }

            const botoes = item.querySelectorAll('.checklist-actions .btn');
            if (botoes.length >= 2) {
                botoes[0].className = 'btn btn-mini ' + (status === 'conforme' ? 'btn-success' : '');
                botoes[1].className = 'btn btn-mini ' + (status === 'nao_conforme' ? 'btn-danger' : '');
            }

            atualizarProgresso();
        }
    } catch (err) {
        console.error('Erro ao salvar item:', err);
    }
}

function atualizarProgresso() {
    const items = document.querySelectorAll('.checklist-item');
    const concluidos = document.querySelectorAll('.checklist-item.conforme, .checklist-item.nao_conforme').length;
    const progresso = items.length > 0 ? Math.round((concluidos / items.length) * 100) : 0;

    document.getElementById('progressBar').style.width = progresso + '%';
    document.getElementById('progressText').textContent = progresso + '% concluído';
}

// Finalização
async function finalizarExecucao() {
    if (execucaoId === null || execucaoId === undefined) {
        alert('Erro: Execução não iniciada. Por favor, recarregue a página e tente novamente.');
        return;
    }

    const nomeAssinante = document.getElementById('nomeAssinante').value;
    const observacoes = document.getElementById('observacoes').value;

    if (!nomeAssinante) {
        alert('Informe o nome de quem está assinando');
        return;
    }

    const btn = document.getElementById('btnFinalizar');
    btn.classList.add('loading');
    btn.disabled = true;

    const assinatura = canvas.toDataURL('image/png');

    // Localização é opcional
    const lat = latitude || 0;
    const lng = longitude || 0;

    const csrf = getCsrfToken();
    const formData = new FormData();
    formData.append('execucao_id', execucaoId);
    formData.append('latitude', lat);
    formData.append('longitude', lng);
    formData.append('foto_checkout', fotoCheckout || '');
    formData.append('assinatura_cliente', assinatura);
    formData.append('nome_cliente_assina', nomeAssinante);
    formData.append('observacoes', observacoes);
    formData.append(csrf.name, csrf.value);

    console.log('Finalizando execução...', { execucao_id: execucaoId });

    try {
        const response = await fetch('<?php echo site_url("tecnicos/finalizar_execucao"); ?>', {
            method: 'POST',
            body: formData
        });

        const responseText = await response.text();
        console.log('Resposta raw finalizar:', responseText.substring(0, 500));

        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Resposta não é JSON válido:', responseText);
            alert('Erro no servidor ao finalizar. Verifique o console.');
            return;
        }

        if (data.success) {
            alert('OS finalizada com sucesso! Tempo total: ' + Math.round(data.tempo_total * 100) / 100 + ' horas');
            window.location.href = '<?php echo site_url("tecnicos/dashboard"); ?>';
        } else {
            alert('Erro: ' + data.message);
        }
    } catch (err) {
        alert('Erro ao finalizar: ' + err.message);
        console.error('Erro:', err);
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
}

// Carregar estoque do técnico
// Funções removidas: carregarMeuEstoque, abrirModalMateriais
</script></div>
