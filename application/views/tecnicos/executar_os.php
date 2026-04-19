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
                                <?php
                                // Verifica se cliente existe e tem nome válido
                                if (isset($cliente) && is_object($cliente) && !empty($cliente->nomeCliente)):
                                ?>
                                    <h4>
                                        <?php echo htmlspecialchars($cliente->nomeCliente, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                    </h4>

                                    <!-- Endereço -->
                                    <div class="client-meta">
                                        <span class="meta-item">
                                            <i class="bx bx-map"></i>
                                            <?php
                                            $endereco = isset($cliente->endereco) && !empty($cliente->endereco)
                                                ? $cliente->endereco
                                                : 'Endereço não informado';
                                            echo htmlspecialchars($endereco, ENT_COMPAT | ENT_HTML5, 'UTF-8');
                                            ?>
                                        </span>
                                    </div>

                                    <?php
                                    // Prepara contatos
                                    $contatos = [];
                                    $telefone = isset($cliente->telefone) ? trim($cliente->telefone) : '';
                                    $celular = isset($cliente->celular) ? trim($cliente->celular) : '';
                                    $email = isset($cliente->email) ? trim($cliente->email) : '';

                                    if (!empty($telefone) && $telefone !== '-') {
                                        $contatos[] = '<i class="bx bx-phone"></i> ' . htmlspecialchars($telefone, ENT_COMPAT | ENT_HTML5, 'UTF-8');
                                    }
                                    if (!empty($celular) && $celular !== '-') {
                                        $contatos[] = '<i class="bx bx-mobile"></i> ' . htmlspecialchars($celular, ENT_COMPAT | ENT_HTML5, 'UTF-8');
                                    }
                                    if (!empty($email)) {
                                        $contatos[] = '<i class="bx bx-envelope"></i> ' . htmlspecialchars($email, ENT_COMPAT | ENT_HTML5, 'UTF-8');
                                    }

                                    if (!empty($contatos)):
                                    ?>
                                        <div class="client-meta">
                                            <span class="meta-item">
                                                <?php echo implode(' <span style="margin: 0 8px; color: #ccc;">|</span> ', $contatos); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Documento -->
                                    <?php if (isset($cliente->documento) && !empty($cliente->documento)): ?>
                                        <div class="client-meta">
                                            <span class="meta-item">
                                                <i class="bx bx-id-card"></i> CPF/CNPJ: <?php echo htmlspecialchars($cliente->documento, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <h4><span style="color: #dc3545;"><i class="bx bx-error-circle"></i> Cliente não encontrado</span></h4>
                                    <div class="client-meta" style="color: #856404; background: #fff3cd; padding: 10px; border-radius: 6px; margin-top: 10px;">
                                        <span class="meta-item">
                                            <i class="bx bx-info-circle"></i> Não foi possível carregar os dados do cliente. Verifique se o cliente está vinculado corretamente à OS.
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botão de Check-in -->
                <div id="checkinSection" class="action-card <?php echo $execucao ? 'hidden' : ''; ?>">
                    <h5><i class="bx bx-map-pin"></i> Iniciar Atendimento</h5>

                    <!-- Assinatura do Técnico -->
                    <div class="assinatura-section" style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h6><i class="bx bx-pen"></i> Assinatura do Técnico</h6>
                        <canvas id="assinaturaTecnico"></canvas>
                        <div style="margin-top: 10px; text-align: center;">
                            <button type="button" class="btn btn-mini" onclick="limparAssinaturaTecnico()">
                                <i class="bx bx-trash"></i> Limpar Assinatura
                            </button>
                        </div>
                        <small style="display: block; margin-top: 5px; color: #666; text-align: center;">Sua assinatura é obrigatória para iniciar</small>
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

                    <!-- Atendimento -->
                    <div class="widget-box wizard-container">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-list-check"></i></span>
                            <h5>Atendimento</h5>
                            <div class="buttons">
                                <span class="wizard-step-indicator">
                                    <span id="stepIndicator">Etapa 1 de 5</span>
                                </span>
                            </div>
                        </div>
                        <div class="widget-content">
                            <!-- Progresso do Wizard -->
                            <div class="wizard-progress">
                                <div class="wizard-progress-bar" id="wizardProgressBar"></div>
                                <div class="wizard-steps">
                                    <div class="wizard-step active" data-step="1">
                                        <div class="step-number">1</div>
                                        <div class="step-label">Check-in</div>
                                    </div>
                                    <div class="wizard-step" data-step="2">
                                        <div class="step-number">2</div>
                                        <div class="step-label">Serviços</div>
                                    </div>
                                    <div class="wizard-step" data-step="3">
                                        <div class="step-number">3</div>
                                        <div class="step-label">Fotos</div>
                                    </div>
                                    <div class="wizard-step" data-step="4">
                                        <div class="step-number">4</div>
                                        <div class="step-label">Observações</div>
                                    </div>
                                    <div class="wizard-step" data-step="5">
                                        <div class="step-number">5</div>
                                        <div class="step-label">Check-out</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Conteúdo das Etapas -->
                            <div class="wizard-content">
                                <!-- Etapa 1: Check-in -->
                                <div class="wizard-step-content active" data-step="1">
                                    <h4><i class="bx bx-log-in"></i> Iniciar Atendimento</h4>
                                    <p>Confirme o início do atendimento na OS #<?php echo $os->idOs; ?></p>
                                    <div class="checklist-confirmacao">
                                        <label class="checklist-item-label">
                                            <input type="checkbox" id="checkConfirmarLocal" class="checklist-checkbox-input">
                                            <span class="checklist-text">
                                                <i class="bx bx-map-pin"></i> Estou no local do atendimento
                                            </span>
                                        </label>
                                        <label class="checklist-item-label">
                                            <input type="checkbox" id="checkConfirmarCliente" class="checklist-checkbox-input">
                                            <span class="checklist-text">
                                                <i class="bx bx-user-check"></i> Cliente está presente/conectado
                                            </span>
                                        </label>
                                        <label class="checklist-item-label">
                                            <input type="checkbox" id="checkConfirmarEquipamento" class="checklist-checkbox-input">
                                            <span class="checklist-text">
                                                <i class="bx bx-wrench"></i> Equipamentos necessários disponíveis
                                            </span>
                                        </label>
                                    </div>
                                    <div class="wizard-actions">
                                        <button type="button" class="btn btn-primary" onclick="wizardProximo()">
                                            <i class="bx bx-right-arrow-alt"></i> Iniciar e Prosseguir
                                        </button>
                                    </div>
                                </div>

                                <!-- Etapa 2: Execução dos Serviços -->
                                <div class="wizard-step-content" data-step="2">
                                    <h4><i class="bx bx-wrench"></i> Execução dos Serviços</h4>
                                    <p>Marque o status de cada serviço executado:</p>

                                    <?php if (!empty($servicos)): ?>
                                        <div class="wizard-servicos-list">
                                            <?php foreach ($servicos as $index => $servico): ?>
                                                <div class="wizard-servico-item" data-servico-id="<?php echo $servico->idServicos_os ?? $index; ?>">
                                                    <div class="servico-info-wizard">
                                                        <div class="servico-nome-wizard">
                                                            <i class="bx bx-wrench"></i>
                                                            <?php echo htmlspecialchars($servico->servico_nome ?? $servico->nome ?? 'Serviço', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                                        </div>
                                                        <?php if (!empty($servico->servico_codigo) || !empty($servico->codigo)): ?>
                                                            <small class="servico-codigo-wizard">Código: <?php echo htmlspecialchars($servico->servico_codigo ?? $servico->codigo ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="servico-status-selector">
                                                        <button type="button" class="btn-status" data-status="pendente"
                                                                onclick="setWizardServicoStatus(<?php echo $servico->idServicos_os ?? $index; ?>, 'pendente')">
                                                            <i class="bx bx-circle"></i> Pendente
                                                        </button>
                                                        <button type="button" class="btn-status btn-status-ok" data-status="conforme"
                                                                onclick="setWizardServicoStatus(<?php echo $servico->idServicos_os ?? $index; ?>, 'conforme')">
                                                            <i class="bx bx-check"></i> Executado
                                                        </button>
                                                        <button type="button" class="btn-status btn-status-nok" data-status="nao_conforme"
                                                                onclick="setWizardServicoStatus(<?php echo $servico->idServicos_os ?? $index; ?>, 'nao_conforme')">
                                                            <i class="bx bx-x"></i> Não Executado
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="wizard-servicos-resumo" id="wizardServicosResumo">
                                            <span class="resumo-pendente" id="resumoPendente"></span>
                                            <span class="resumo-executado" id="resumoExecutado"></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <div class="empty-text">Nenhum serviço cadastrado nesta OS</div>
                                            <small>Continue para as próximas etapas</small>
                                        </div>
                                    <?php endif; ?>

                                    <div class="wizard-actions">
                                        <button type="button" class="btn" onclick="wizardAnterior()">
                                            <i class="bx bx-left-arrow-alt"></i> Voltar
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="wizardProximo()">
                                            <i class="bx bx-right-arrow-alt"></i> Prosseguir
                                        </button>
                                    </div>
                                </div>

                                <!-- Etapa 3: Fotos do Servico -->
                                <div class="wizard-step-content" data-step="3">
                                    <h4><i class="bx bx-camera"></i> Registro Fotografico</h4>
                                    <p>Adicione fotos do servico realizado:</p>

                                    <div class="wizard-fotos-container">
                                        <!-- Tabs de tipo de foto -->
                                        <div class="foto-tabs">
                                            <button type="button" class="foto-tab active" data-tipo="antes" onclick="selecionarTipoFoto('antes')">
                                                <i class="bx bx-time-before"></i> Antes
                                            </button>
                                            <button type="button" class="foto-tab" data-tipo="durante" onclick="selecionarTipoFoto('durante')">
                                                <i class="bx bx-loader"></i> Durante
                                            </button>
                                            <button type="button" class="foto-tab" data-tipo="depois" onclick="selecionarTipoFoto('depois')">
                                                <i class="bx bx-check-circle"></i> Depois
                                            </button>
                                            <button type="button" class="foto-tab" data-tipo="detalhe" onclick="selecionarTipoFoto('detalhe')">
                                                <i class="bx bx-detail"></i> Detalhe
                                            </button>
                                        </div>

                                        <input type="hidden" id="tipoFotoWizard" value="antes">

                                        <!-- Area de upload moderna -->
                                        <div class="upload-area-modern" id="uploadAreaModern" onclick="abrirSeletorFoto()">
                                            <div class="upload-content">
                                                <i class="bx bx-cloud-upload"></i>
                                                <span class="upload-title">Adicionar Foto</span>
                                                <span class="upload-subtitle">Toque para camera ou escolher arquivo</span>
                                            </div>
                                        </div>

                                        <!-- Input file escondido -->
                                        <input type="file" id="wizardFotoInput" accept="image/*" style="display: none;" onchange="processarFotoWizard(this)">

                                        <!-- Galeria de fotos -->
                                        <div class="fotos-galeria-modern" id="wizardFotosPreview">
                                            <!-- Fotos serao adicionadas aqui via JS -->
                                        </div>

                                        <!-- Contador de fotos -->
                                        <div class="fotos-contador" id="fotosContador">
                                            <i class="bx bx-images"></i> <span id="contadorTexto">0 fotos</span>
                                        </div>
                                    </div>

                                    <div class="wizard-actions">
                                        <button type="button" class="btn" onclick="wizardAnterior()">
                                            <i class="bx bx-left-arrow-alt"></i> Voltar
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="wizardProximo()">
                                            <i class="bx bx-right-arrow-alt"></i> Prosseguir
                                        </button>
                                    </div>
                                </div>

                                <!-- Etapa 4: Observações -->
                                <div class="wizard-step-content" data-step="4">
                                    <h4><i class="bx bx-note"></i> Observações do Atendimento</h4>
                                    <p>Descreva o que foi realizado:</p>

                                    <div class="wizard-observacoes">
                                        <textarea id="wizardObservacoes" rows="6" class="span12"
                                            placeholder="Descreva:
- O que foi realizado
- Problemas encontrados
- Recomendações ao cliente
- Materiais utilizados"></textarea>

                                        <div class="observacoes-checklist">
                                            <h6><i class="bx bx-check-square"></i> Checklist de Conclusão</h6>
                                            <label class="checklist-item-label">
                                                <input type="checkbox" id="checkServicoConcluido" class="checklist-checkbox-input">
                                                <span class="checklist-text">Serviço concluído conforme solicitado</span>
                                            </label>
                                            <label class="checklist-item-label">
                                                <input type="checkbox" id="checkClienteOrientado" class="checklist-checkbox-input">
                                                <span class="checklist-text">Cliente orientado sobre o serviço realizado</span>
                                            </label>
                                            <label class="checklist-item-label">
                                                <input type="checkbox" id="checkLocalLimpo" class="checklist-checkbox-input">
                                                <span class="checklist-text">Local de trabalho foi limpo/organizado</span>
                                            </label>
                                            <label class="checklist-item-label">
                                                <input type="checkbox" id="checkEquipamentosOk" class="checklist-checkbox-input">
                                                <span class="checklist-text">Equipamentos testados e funcionando</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="wizard-actions">
                                        <button type="button" class="btn" onclick="wizardAnterior()">
                                            <i class="bx bx-left-arrow-alt"></i> Voltar
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="wizardProximo()">
                                            <i class="bx bx-right-arrow-alt"></i> Prosseguir
                                        </button>
                                    </div>
                                </div>

                                <!-- Etapa 5: Check-out e Assinatura -->
                                <div class="wizard-step-content" data-step="5">
                                    <h4><i class="bx bx-log-out"></i> Finalização - Check-out</h4>
                                    <p>Revise e finalize o atendimento:</p>

                                    <div class="wizard-resumo-final" id="wizardResumoFinal">
                                        <!-- Resumo preenchido via JS -->
                                    </div>

                                    <div class="wizard-assinatura-section">
                                        <h6><i class="bx bx-pencil"></i> Assinatura do Cliente</h6>
                                        <div class="assinatura-container" id="assinaturaContainer">
                                            <canvas id="assinaturaCliente" class="signature-pad-wizard"></canvas>
                                            <button type="button" class="btn-fullscreen" onclick="toggleFullscreenAssinatura()" title="Tela Cheia">
                                                <i class="bx bx-fullscreen"></i>
                                            </button>
                                            <!-- Botão de salvar que aparece apenas no fullscreen -->
                                            <button type="button" class="btn-salvar-fullscreen" onclick="salvarEFecharFullscreen()">
                                                <i class="bx bx-check"></i> Salvar e Voltar
                                            </button>
                                        </div>
                                        <div class="assinatura-botoes">
                                            <button type="button" class="btn btn-mini" onclick="limparAssinaturaCliente()">
                                                <i class="bx bx-trash"></i> Limpar Assinatura
                                            </button>
                                            <button type="button" class="btn btn-mini btn-info" onclick="toggleFullscreenAssinatura()">
                                                <i class="bx bx-fullscreen"></i> Tela Cheia
                                            </button>
                                        </div>

                                        <div class="control-group" style="margin-top: 15px;">
                                            <label>Nome de quem assina:</label>
                                            <input type="text" id="wizardNomeAssinante" placeholder="Nome completo" class="span12">
                                        </div>

                                        <label class="checklist-item-label" style="margin-top: 15px;">
                                            <input type="checkbox" id="checkConfirmarAssinatura" class="checklist-checkbox-input">
                                            <span class="checklist-text">Confirmo que o serviço foi realizado e aceito as condições</span>
                                        </label>
                                    </div>

                                    <div class="wizard-actions">
                                        <button type="button" class="btn" onclick="wizardAnterior()">
                                            <i class="bx bx-left-arrow-alt"></i> Voltar
                                        </button>
                                        <button type="button" class="btn btn-success btn-large" onclick="finalizarWizardAtendimento()">
                                            <i class="bx bx-check-double"></i> Finalizar Atendimento
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Finalização -->
                    <div class="action-card">
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

<!-- SweetAlert2 para o Wizard -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

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
    position: relative;
}

.gallery-item .foto-link {
    display: block;
    width: 100%;
    height: 100%;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.gallery-item:hover img {
    transform: scale(1.05);
}

/* Botão remover foto */
.gallery-item {
    position: relative;
}

.btn-remover-foto {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(244, 67, 54, 0.9);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s;
    z-index: 10;
}

.gallery-item:hover .btn-remover-foto {
    opacity: 1;
}

.btn-remover-foto:hover {
    background: #d32f2f;
    transform: scale(1.1);
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

/* Assinatura do Técnico (Check-in) */
#assinaturaTecnico {
    width: 100%;
    height: 150px;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: white;
    cursor: crosshair;
    touch-action: none;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
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

/* ========================================
   CAMERA MODAL STYLES
   ======================================== */

/* Modal Container */
.camera-modal {
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
}

.camera-modal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px 12px 0 0;
    padding: 15px 20px;
    border-bottom: none;
}

.camera-modal .modal-header h3 {
    color: white;
    margin: 0;
    font-size: 1.2rem;
    font-weight: 500;
}

.camera-modal .modal-header h3 i {
    margin-right: 8px;
}

.camera-modal .modal-header .close {
    color: white;
    opacity: 0.8;
    font-size: 24px;
    text-shadow: none;
}

.camera-modal .modal-header .close:hover {
    opacity: 1;
}

.camera-modal .modal-body {
    padding: 20px;
    background: #fafafa;
}

/* Modal Tabs */
.modal-tabs {
    margin: -20px -20px 20px -20px;
    padding: 0 20px;
    background: white;
    border-bottom: 2px solid #e0e0e0;
}

.modal-tabs > li {
    margin-bottom: -2px;
}

.modal-tabs > li > a {
    padding: 12px 20px;
    color: #666;
    font-weight: 500;
    border: none;
    border-bottom: 2px solid transparent;
    background: transparent;
    transition: all 0.3s;
}

.modal-tabs > li > a:hover {
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

.modal-tabs > li.active > a,
.modal-tabs > li.active > a:hover {
    color: #667eea;
    border-bottom-color: #667eea;
    background: transparent;
}

.modal-tabs > li > a i {
    margin-right: 6px;
}

/* Tab Content */
.modal-tab-content {
    background: white;
    border-radius: 8px;
    padding: 0;
    min-height: 300px;
}

.modal-tab-content .tab-pane {
    padding: 15px;
}

/* Camera Viewport */
.camera-viewport {
    position: relative;
    width: 100%;
    height: 280px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.camera-viewport video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.camera-viewport #canvas {
    display: none;
}

/* Captured Preview */
.camera-preview-captured {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #1a1a2e;
    cursor: pointer;
}

.camera-preview-captured.active {
    display: flex;
}

.camera-preview-captured img {
    max-width: 90%;
    max-height: 80%;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
}

.camera-retake-hint {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.85rem;
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.camera-retake-hint i {
    font-size: 1rem;
}

/* Camera Message (when camera unavailable) */
.camera-message {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: rgba(255, 255, 255, 0.7);
    display: none;
}

.camera-message.active {
    display: block;
}

.camera-message i {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.5;
}

.camera-message p {
    margin: 0 0 5px 0;
    font-size: 1rem;
}

.camera-message small {
    opacity: 0.7;
}

/* Upload Area */
.upload-area {
    border: 2px dashed #667eea;
    border-radius: 12px;
    padding: 40px 30px;
    text-align: center;
    background: rgba(102, 126, 234, 0.03);
    cursor: pointer;
    transition: all 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    background: rgba(102, 126, 234, 0.08);
    border-color: #764ba2;
}

.upload-area.dragover {
    background: rgba(102, 126, 234, 0.15);
    border-color: #667eea;
    border-style: solid;
}

.upload-area i {
    font-size: 48px;
    color: #667eea;
    margin-bottom: 15px;
}

.upload-title {
    color: #333;
    font-weight: 500;
    margin: 0 0 5px 0;
    font-size: 1rem;
}

.upload-hint {
    color: #888;
    margin: 0;
    font-size: 0.85rem;
}

.upload-area input[type="file"] {
    display: none;
}

/* Upload Preview */
.upload-preview {
    display: none;
    text-align: center;
    padding: 20px;
}

.upload-preview.active {
    display: block;
}

.upload-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    margin-bottom: 15px;
}

.upload-success-msg {
    color: #4caf50;
    font-weight: 500;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.upload-success-msg i {
    font-size: 1.2rem;
}

/* Modal Form Elements */
.modal-form-group {
    margin-top: 15px;
}

.modal-form-group:first-of-type {
    margin-top: 20px;
}

.modal-label {
    display: block;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.modal-label i {
    color: #667eea;
    margin-right: 6px;
}

.modal-select,
.modal-input {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s;
    background: white;
}

.modal-select:focus,
.modal-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.modal-select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 35px;
}

.modal-input::placeholder {
    color: #aaa;
}

/* Modal Footer */
.modal-footer-custom {
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    border-radius: 0 0 12px 12px;
    padding: 15px 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.modal-footer-custom .btn {
    padding: 8px 20px;
    font-weight: 500;
}

.modal-footer-custom .btn i {
    margin-right: 6px;
}

/* Responsive adjustments for modal */
@media (max-width: 768px) {
    .camera-modal {
        width: 95%;
        margin: 10px auto;
    }

    .camera-viewport {
        height: 240px;
    }

    .upload-area {
        padding: 30px 20px;
    }

    .modal-tabs > li > a {
        padding: 10px 15px;
        font-size: 0.9rem;
    }

    .modal-footer-custom {
        flex-direction: column;
    }

    .modal-footer-custom .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .camera-viewport {
        height: 200px;
    }

    .upload-area i {
        font-size: 36px;
    }
}

/* ============================================
   WIZARD DE ATENDIMENTO
   ============================================ */
.wizard-container {
    border: 2px solid #667eea;
}

.wizard-step-indicator {
    background: #667eea;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Progress Bar */
.wizard-progress {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    position: relative;
}

.wizard-progress-bar {
    position: absolute;
    top: 40px;
    left: 10%;
    width: 80%;
    height: 4px;
    background: #e0e0e0;
    z-index: 1;
}

.wizard-progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    width: 0%;
    transition: width 0.3s ease;
}

.wizard-progress-bar.step-1::after { width: 0%; }
.wizard-progress-bar.step-2::after { width: 25%; }
.wizard-progress-bar.step-3::after { width: 50%; }
.wizard-progress-bar.step-4::after { width: 75%; }
.wizard-progress-bar.step-5::after { width: 100%; }

.wizard-steps {
    display: flex;
    justify-content: space-between;
    position: relative;
    z-index: 2;
}

.wizard-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s;
}

.wizard-step .step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.3s;
    border: 3px solid transparent;
}

.wizard-step.active .step-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.wizard-step.completed .step-number {
    background: #4caf50;
    color: white;
}

.wizard-step .step-label {
    margin-top: 8px;
    font-size: 0.75rem;
    color: #666;
    font-weight: 500;
}

.wizard-step.active .step-label {
    color: #667eea;
    font-weight: 600;
}

/* Step Content */
.wizard-step-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.wizard-step-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

.wizard-step-content h4 {
    color: #2d335b;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.wizard-step-content h4 i {
    color: #667eea;
    font-size: 1.5rem;
}

/* Checklist de Confirmação */
.checklist-confirmacao {
    margin: 20px 0;
}

.checklist-item-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px;
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.checklist-item-label:hover {
    border-color: #667eea;
    background: #f0f4ff;
}

.checklist-item-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-top: 2px;
    accent-color: #667eea;
}

.checklist-item-label input[type="checkbox"]:checked + .checklist-text {
    color: #667eea;
    font-weight: 500;
}

.checklist-text {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    color: #333;
}

.checklist-text i {
    color: #667eea;
    font-size: 1.2rem;
}

/* Serviços no Wizard */
.wizard-servicos-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin: 20px 0;
}

.wizard-servico-item {
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s;
}

.wizard-servico-item:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

.wizard-servico-item.status-conforme {
    border-color: #4caf50;
    background: #f1f8e9;
}

.wizard-servico-item.status-nao_conforme {
    border-color: #f44336;
    background: #ffebee;
}

.servico-info-wizard {
    margin-bottom: 15px;
}

.servico-nome-wizard {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d335b;
    display: flex;
    align-items: center;
    gap: 10px;
}

.servico-nome-wizard i {
    color: #667eea;
    font-size: 1.3rem;
}

.servico-codigo-wizard {
    color: #888;
    font-size: 0.85rem;
    margin-left: 30px;
}

.servico-status-selector {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-status {
    flex: 1;
    min-width: 120px;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    font-size: 0.9rem;
}

.btn-status:hover {
    border-color: #667eea;
    background: #f0f4ff;
}

.btn-status.active {
    border-color: #667eea;
    background: #667eea;
    color: white;
}

.btn-status-ok:hover,
.btn-status-ok.active {
    border-color: #4caf50;
    background: #4caf50;
    color: white;
}

.btn-status-nok:hover,
.btn-status-nok.active {
    border-color: #f44336;
    background: #f44336;
    color: white;
}

.wizard-servicos-resumo {
    display: flex;
    gap: 20px;
    justify-content: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-top: 20px;
}

.resumo-pendente,
.resumo-executado {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.resumo-pendente {
    background: #fff3e0;
    color: #ef6c00;
}

.resumo-executado {
    background: #e8f5e9;
    color: #2e7d32;
}

/* Fotos no Wizard - Design Moderno */
.wizard-fotos-container {
    margin: 20px 0;
}

/* Tabs de tipo de foto */
.foto-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.foto-tab {
    flex: 1;
    min-width: 70px;
    padding: 12px 8px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: white;
    color: #666;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.foto-tab i {
    font-size: 1.3rem;
}

.foto-tab:hover {
    border-color: #667eea;
    color: #667eea;
}

.foto-tab.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: white;
}

/* Area de upload moderna */
.upload-area-modern {
    border: 2px dashed #ccc;
    border-radius: 16px;
    padding: 30px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: #fafafa;
    margin-bottom: 20px;
}

.upload-area-modern:hover {
    border-color: #667eea;
    background: #f0f4ff;
}

.upload-area-modern:active {
    transform: scale(0.98);
}

.upload-content i {
    font-size: 3rem;
    color: #667eea;
    margin-bottom: 10px;
}

.upload-title {
    display: block;
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.upload-subtitle {
    display: block;
    font-size: 0.85rem;
    color: #888;
}

/* Galeria de fotos moderna */
.fotos-galeria-modern {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 12px;
    margin: 20px 0;
}

.foto-card-modern {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.foto-card-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.foto-card-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.foto-card-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
}

.foto-card-badge.antes { background: #ff9800; }
.foto-card-badge.durante { background: #2196f3; }
.foto-card-badge.depois { background: #4caf50; }
.foto-card-badge.detalhe { background: #9c27b0; }

.foto-card-remove {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(244, 67, 54, 0.9);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.2s;
}

.foto-card-remove:hover {
    background: #f44336;
    transform: scale(1.1);
}

/* Contador de fotos */
.fotos-contador {
    text-align: center;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 10px;
    color: #666;
    font-size: 0.95rem;
}

.fotos-contador i {
    color: #667eea;
    margin-right: 5px;
}

/* CSS antigo mantido para compatibilidade */
.wizard-fotos-section {
    margin: 20px 0;
}

.foto-tipo-selector {
    margin-bottom: 20px;
}

.foto-tipo-selector label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.foto-tipo-selector select {
    width: 100%;
    padding: 10px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
}

.camera-section-wizard {
    margin-bottom: 20px;
}

.camera-preview-wizard {
    aspect-ratio: 16/9;
    max-height: 300px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.3s;
}

.camera-preview-wizard:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.camera-preview-wizard i {
    font-size: 48px;
    margin-bottom: 10px;
}

.fotos-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.foto-preview-item {
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    border: 2px solid #e0e0e0;
}

.foto-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.foto-preview-item .foto-tipo-tag {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 5px;
    font-size: 0.75rem;
    text-align: center;
}

.foto-preview-item .btn-remover-foto {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f44336;
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

/* Observações no Wizard */
.wizard-observacoes textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #4a5568;
    border-radius: 10px;
    font-size: 1rem;
    color: #1a202c;
    background-color: #ffffff;
    resize: vertical;
    min-height: 120px;
}

.wizard-observacoes textarea::placeholder {
    color: #4a5568;
    font-weight: 500;
    opacity: 1;
}

.wizard-observacoes textarea:focus {
    border-color: #667eea;
    background-color: #ffffff;
    color: #1a202c;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}

.wizard-step-content p {
    color: #2d3748;
    font-weight: 500;
}

.observacoes-checklist {
    margin-top: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
}

.observacoes-checklist h6 {
    margin: 0 0 15px 0;
    color: #2d335b;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Resumo Final */
.wizard-resumo-final {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.resumo-section {
    margin-bottom: 20px;
}

.resumo-section:last-child {
    margin-bottom: 0;
}

.resumo-section h6 {
    color: #667eea;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.resumo-servicos-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.resumo-servico-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: white;
    border-radius: 6px;
    font-size: 0.9rem;
}

.resumo-servico-item.status-ok {
    border-left: 3px solid #4caf50;
}

.resumo-servico-item.status-nok {
    border-left: 3px solid #f44336;
}

.resumo-servico-item.status-pendente {
    border-left: 3px solid #ff9800;
}

/* Assinatura no Wizard */
.wizard-assinatura-section {
    margin-top: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
}

.wizard-assinatura-section h6 {
    margin: 0 0 15px 0;
    color: #2d335b;
}

.signature-pad-wizard {
    width: 100%;
    height: 200px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: white;
    cursor: crosshair;
    margin-bottom: 10px;
    position: relative;
}

.signature-pad-wizard:empty::before {
    content: 'Clique e arraste para assinar';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #999;
    font-size: 14px;
    pointer-events: none;
}

/* Container da assinatura */
.assinatura-container {
    position: relative;
    margin-bottom: 10px;
}

.assinatura-container .btn-fullscreen {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(102, 126, 234, 0.9);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.2rem;
    z-index: 10;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.assinatura-container .btn-fullscreen:hover {
    background: rgba(102, 126, 234, 1);
    transform: scale(1.1);
}

/* Botões da assinatura */
.assinatura-botoes {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* Modo Fullscreen */
.assinatura-container.fullscreen {
    position: fixed !important;
    top: 0;
    left: 0;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 99999;
    background: white;
    margin: 0;
    padding: 20px;
    display: flex;
    flex-direction: column;
}

.assinatura-container.fullscreen .signature-pad-wizard {
    flex: 1;
    height: calc(100vh - 100px) !important;
    border: 3px solid #667eea;
    border-radius: 15px;
}

.assinatura-container.fullscreen .btn-fullscreen {
    top: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    font-size: 1.5rem;
    background: #667eea;
}

.assinatura-container.fullscreen .btn-fullscreen i::before {
    content: "\\ec0e"; /* bx-exit-fullscreen */
}

/* Botão salvar em fullscreen - escondido por padrão */
.btn-salvar-fullscreen {
    display: none;
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #28a745;
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    z-index: 100;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    align-items: center;
    gap: 8px;
}

.assinatura-container.fullscreen .btn-salvar-fullscreen {
    display: flex;
}

.btn-salvar-fullscreen:hover {
    background: #218838;
}

.btn-salvar-fullscreen i {
    font-size: 1.2rem;
}

/* Ações do Wizard */
.wizard-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.wizard-actions .btn {
    flex: 1;
    padding: 15px 25px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* Responsividade do Wizard */
@media (max-width: 768px) {
    .wizard-progress-bar {
        display: none;
    }

    .wizard-steps {
        flex-wrap: wrap;
        gap: 10px;
    }

    .wizard-step {
        flex: 1;
        min-width: 60px;
    }

    .wizard-step .step-label {
        font-size: 0.65rem;
    }

    .servico-status-selector {
        flex-direction: column;
    }

    .btn-status {
        width: 100%;
    }

    .wizard-actions {
        flex-direction: column;
    }

    .wizard-actions .btn {
        width: 100%;
    }
}

</style>

<script>
// Inicializar variaveis globais antes de qualquer codigo
window.execucaoId = <?php echo $execucao ? $execucao->id : 'null'; ?>;
window.osId = <?php echo $os->idOs; ?>;
window.latitude = undefined;
window.longitude = undefined;
window.fotoCheckin = null;
window.stream = null;

// Inicializar variaveis do wizard ANTES de qualquer funcao
window.wizardStepAtual = 1;
window.wizardTotalSteps = 5;
window.wizardServicosStatus = {};
window.wizardFotos = [];
window.wizardSignaturePad = null;

// Canvas de assinatura do cliente (wizard) - igual ao do técnico
window.canvasCliente = null;
window.ctxCliente = null;
window.isDrawingCliente = false;

// Obter localização (opcional - silencia erros de permissão)
if ('geolocation' in navigator) {
    navigator.geolocation.watchPosition(
        (pos) => {
            window.latitude = pos.coords.latitude;
            window.longitude = pos.coords.longitude;
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
                window.latitude = pos.coords.latitude;
                window.longitude = pos.coords.longitude;
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
// Verificar status de permissões (silencioso)
async function verificarPermissoes() {
    if (navigator.permissions) {
        try {
            const camResult = await navigator.permissions.query({ name: 'camera' });
            permissaoCamera = camResult.state === 'granted';
            camResult.onchange = () => { permissaoCamera = camResult.state === 'granted'; };
        } catch(e) {}
    }
    // Solicitar GPS silenciosamente
    await solicitarPermissaoGPS();
}

// Verificar permissões ao carregar
verificarPermissoes();

// Canvas de assinatura (verificar se existe)
const canvas = document.getElementById('signaturePad');
const ctx = canvas ? canvas.getContext('2d') : null;
let isDrawing = false;

function resizeCanvas() {
    if (!canvas || !ctx) return;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
}

if (canvas) {
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
}

function startDrawing(e) {
    if (!canvas || !ctx) return;
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
}

function draw(e) {
    if (!isDrawing || !canvas || !ctx) return;
    const rect = canvas.getBoundingClientRect();
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
}

function stopDrawing() {
    isDrawing = false;
}

function limparAssinatura() {
    if (ctx && canvas) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
}

// Canvas de assinatura do Técnico (para check-in)
window.canvasTecnico = null;
window.ctxTecnico = null;
window.isDrawingTecnico = false;

function initCanvasTecnico() {
    window.canvasTecnico = document.getElementById('assinaturaTecnico');

    if (!window.canvasTecnico) {
        console.error('Canvas assinaturaTecnico não encontrado');
        return;
    }

    window.ctxTecnico = window.canvasTecnico.getContext('2d');

    // Configurar dimensões
    const resizeCanvasTecnico = function() {
        const rect = window.canvasTecnico.getBoundingClientRect();
        window.canvasTecnico.width = rect.width > 0 ? rect.width : 300;
        window.canvasTecnico.height = 150;
        window.ctxTecnico.strokeStyle = '#000';
        window.ctxTecnico.lineWidth = 2;
        window.ctxTecnico.lineCap = 'round';
    };

    // Aguardar um tick para garantir que o layout está pronto
    setTimeout(resizeCanvasTecnico, 100);
    window.addEventListener('resize', resizeCanvasTecnico);

    // Eventos do mouse
    window.canvasTecnico.addEventListener('mousedown', function(e) {
        window.isDrawingTecnico = true;
        const rect = window.canvasTecnico.getBoundingClientRect();
        window.ctxTecnico.beginPath();
        window.ctxTecnico.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    });

    window.canvasTecnico.addEventListener('mousemove', function(e) {
        if (!window.isDrawingTecnico) return;
        const rect = window.canvasTecnico.getBoundingClientRect();
        window.ctxTecnico.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        window.ctxTecnico.stroke();
    });

    window.canvasTecnico.addEventListener('mouseup', function() {
        window.isDrawingTecnico = false;
    });

    window.canvasTecnico.addEventListener('mouseout', function() {
        window.isDrawingTecnico = false;
    });

    // Eventos touch
    window.canvasTecnico.addEventListener('touchstart', function(e) {
        e.preventDefault();
        window.isDrawingTecnico = true;
        const rect = window.canvasTecnico.getBoundingClientRect();
        const touch = e.touches[0];
        window.ctxTecnico.beginPath();
        window.ctxTecnico.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
    });

    window.canvasTecnico.addEventListener('touchmove', function(e) {
        e.preventDefault();
        if (!window.isDrawingTecnico) return;
        const rect = window.canvasTecnico.getBoundingClientRect();
        const touch = e.touches[0];
        window.ctxTecnico.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
        window.ctxTecnico.stroke();
    });

    window.canvasTecnico.addEventListener('touchend', function() {
        window.isDrawingTecnico = false;
    });
}

window.limparAssinaturaTecnico = function() {
    if (window.ctxTecnico && window.canvasTecnico) {
        window.ctxTecnico.clearRect(0, 0, window.canvasTecnico.width, window.canvasTecnico.height);
    }
};

// Inicializar quando DOM estiver pronto
window.iniciarCanvasAssinatura = function() {
    initCanvasTecnico();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.iniciarCanvasAssinatura);
} else {
    window.iniciarCanvasAssinatura();
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

        window.fotoCheckin = tempCanvas.toDataURL('image/jpeg', 0.8);
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
        if (window.stream) {
            window.stream.getTracks().forEach(track => track.stop());
            window.stream = null;
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
    document.getElementById('cameraPreview').classList.remove('active');
    document.getElementById('video').style.display = 'block';
    iniciarCamera();
}

async function iniciarCamera() {
    abaAtiva = 'camera';
    document.getElementById('cameraPreview').classList.remove('active');
    document.getElementById('video').style.display = 'block';

    // Se não tem permissão, tentar solicitar
    if (!permissaoCamera) {
        const resultado = await solicitarPermissaoCamera();
        if (!resultado) {
            document.getElementById('cameraMensagem').classList.add('active');
            document.getElementById('cameraMensagem').innerHTML = '<i class="bx bx-info-circle"></i><p>Câmera não permitida</p><small>Use a aba "Arquivo" para enviar fotos</small>';
            document.getElementById('video').style.display = 'none';
            return;
        }
    }

    if (!window.stream) {
        try {
            window.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            document.getElementById('video').srcObject = window.stream;
            document.getElementById('cameraMensagem').classList.remove('active');
        } catch (err) {
            console.error('Erro ao abrir câmera:', err);
            document.getElementById('cameraMensagem').classList.add('active');
            document.getElementById('cameraMensagem').innerHTML = '<i class="bx bx-error-circle"></i><p>Erro ao acessar câmera</p><small>Use a aba "Arquivo"</small>';
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
    document.getElementById('cameraPreview').classList.remove('active');
    document.getElementById('video').style.display = 'block';
    document.getElementById('uploadPreview').classList.remove('active');
    document.getElementById('fileFotoServico').value = '';

    // Tentar iniciar câmera
    await iniciarCamera();
}

function fecharCamera() {
    jQuery('#cameraModal').modal('hide');
    document.getElementById('cameraModal').setAttribute('aria-hidden', 'true');
    if (window.stream) {
        window.stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    fotoServicoBase64 = null;
    document.getElementById('uploadPreview').classList.remove('active');
    document.getElementById('cameraPreview').classList.remove('active');
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
    document.getElementById('cameraPreview').classList.add('active');
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
        document.getElementById('uploadPreview').classList.add('active');
        abaAtiva = 'upload';
    };
    reader.readAsDataURL(file);
}

// Função para remover foto
async function removerFoto(fotoId) {
    if (!confirm('Tem certeza que deseja remover esta foto?')) {
        return;
    }

    const csrf = getCsrfToken();
    const formData = new FormData();
    formData.append('foto_id', fotoId);
    formData.append(csrf.name, csrf.value);

    try {
        const response = await fetch('<?php echo site_url("tecnicos/remover_foto"); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Remove o elemento da galeria
            const fotoElement = document.getElementById('foto-item-' + fotoId);
            if (fotoElement) {
                fotoElement.remove();
            }
            // Opcional: mostrar mensagem de sucesso
            console.log('Foto removida com sucesso');
        } else {
            alert('Erro ao remover foto: ' + (data.message || 'Erro desconhecido'));
        }
    } catch (err) {
        console.error('Erro ao remover foto:', err);
        alert('Erro ao remover foto. Tente novamente.');
    }
}

async function salvarFotoServico() {
    // Debug info
    console.log('execucaoId:', window.execucaoId);
    console.log('fotoServicoBase64 existe:', !!fotoServicoBase64);
    console.log('fotoServicoBase64 tamanho:', fotoServicoBase64 ? fotoServicoBase64.length : 0);

    // Verificar se há foto capturada ou selecionada
    if (!fotoServicoBase64) {
        alert('Selecione ou tire uma foto primeiro.');
        return;
    }

    const tipo = document.getElementById('tipoFoto').value;
    const descricao = document.getElementById('descricaoFoto').value;

    const csrf = getCsrfToken();
    const formData = new FormData();
    formData.append('execucao_id', window.execucaoId);
    formData.append('foto', fotoServicoBase64);
    formData.append('tipo', tipo);
    formData.append('descricao', descricao);
    formData.append('latitude', window.latitude || 0);
    formData.append('longitude', window.longitude || 0);
    formData.append(csrf.name, csrf.value);

    console.log('Enviando execucao_id:', execucaoId);

    const btn = document.getElementById('btnSalvarFoto');
    if (!btn) {
        alert('Erro: botão não encontrado. Recarregue a página (Ctrl+F5).');
        return;
    }
    const btnOriginalText = btn.innerHTML;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Salvando...';
    btn.disabled = true;

    try {
        const response = await fetch('<?php echo site_url("tecnicos/adicionar_foto"); ?>', {
            method: 'POST',
            body: formData
        });

        // Verificar se resposta é OK
        const responseText = await response.text();
        console.log('Resposta bruta:', responseText.substring(0, 1000));

        if (!response.ok) {
            console.error('Erro HTTP:', response.status, responseText.substring(0, 500));
            alert('Erro do servidor: ' + response.status + '. Verifique o console (F12).');
            return;
        }

        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Resposta não é JSON válido:', responseText);
            alert('Erro: resposta inválida do servidor. Verifique os logs em application/logs/');
            return;
        }

        if (data.success) {
            const grid = document.getElementById('galleryGrid');
            const item = document.createElement('div');
            item.className = 'gallery-item';
            item.id = 'foto-item-' + data.foto_id;
            // Usa a URL do servidor (mesmo padrão do sistema de atendimento)
            const fotoUrl = data.url || fotoServicoBase64;
            item.innerHTML = `<a href="${fotoUrl}" target="_blank" class="foto-link"><img src="${fotoUrl}" alt="Foto"></a>
                <button type="button" class="btn-remover-foto" onclick="removerFoto(${data.foto_id})" title="Remover foto"><i class="bx bx-trash"></i></button>`;
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
        if (btn) {
            btn.innerHTML = btnOriginalText;
            btn.disabled = false;
        }
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

// Execução - Garantir que está no escopo global
window.iniciarExecucao = async function() {
    console.log('iniciarExecucao chamada');

    // Validar assinatura do técnico
    if (!window.canvasTecnico || !window.ctxTecnico) {
        console.error('Canvas não inicializado:', { canvasTecnico: window.canvasTecnico, ctxTecnico: window.ctxTecnico });
        alert('Erro: Canvas de assinatura não encontrado. Recarregue a página.');
        return;
    }

    // Verificar se o canvas tem desenho (assinatura)
    let hasDrawing = false;
    try {
        const pixelData = window.ctxTecnico.getImageData(0, 0, window.canvasTecnico.width, window.canvasTecnico.height).data;
        hasDrawing = pixelData.some(function(pixel, index) { return index % 4 === 3 && pixel > 0; });
    } catch (e) {
        console.error('Erro ao verificar assinatura:', e);
    }

    if (!hasDrawing) {
        alert('Por favor, assine antes de iniciar o atendimento.');
        return;
    }

    // Localização é opcional - usa valores padrão se não disponível
    const lat = window.latitude || 0;
    const lng = window.longitude || 0;

    const btn = document.getElementById('btnIniciar');
    if (!btn) {
        alert('Erro: Botão não encontrado');
        return;
    }

    btn.classList.add('loading');
    btn.disabled = true;

    // Coletar assinatura do técnico
    const assinaturaTecnico = window.canvasTecnico.toDataURL('image/png');

    const csrf = getCsrfToken();
    const formData = new FormData();
    formData.append('os_id', window.osId);
    formData.append('latitude', lat);
    formData.append('longitude', lng);
    formData.append('foto_checkin', window.fotoCheckin || '');
    formData.append('assinatura_tecnico', assinaturaTecnico);
    formData.append('tipo', 'inicio_local');
    formData.append(csrf.name, csrf.value);

    console.log('Enviando requisição...', { os_id: window.osId, lat, lng, csrf_name: csrf.name });

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
            window.execucaoId = data.execucao_id;
            console.log('Execução iniciada com ID:', window.execucaoId);
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
window.toggleServicoStatus = toggleServicoStatus;

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
window.setServicoStatus = setServicoStatus;

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
window.atualizarProgressoServicos = atualizarProgressoServicos;

// Checklist (mantido para compatibilidade)
async function salvarChecklistItem(itemId, status) {
    if (!window.execucaoId) return;

    const csrf = getCsrfToken();
    const formData = new FormData();
    formData.append('execucao_id', window.execucaoId);
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
    if (window.execucaoId === null || window.execucaoId === undefined) {
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
    formData.append('execucao_id', window.execucaoId);
    formData.append('latitude', lat);
    formData.append('longitude', lng);
    formData.append('assinatura_cliente', assinatura);
    formData.append('nome_cliente_assina', nomeAssinante);
    formData.append('observacoes', observacoes);
    formData.append(csrf.name, csrf.value);

    console.log('Finalizando execução...', { execucao_id: window.execucaoId });

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

// Inicializar eventos de drag and drop para upload
document.addEventListener('DOMContentLoaded', function() {
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('fileFotoServico');

    if (dropArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropArea.classList.add('dragover');
        }

        function unhighlight(e) {
            dropArea.classList.remove('dragover');
        }

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                previewArquivoServico(fileInput);
            }
        }
    }
});

// ============================================
// WIZARD DE ATENDIMENTO
// ============================================
// Inicializar variaveis do wizard no escopo global
window.wizardStepAtual = window.wizardStepAtual || 1;
window.wizardTotalSteps = 5;
window.wizardServicosStatus = window.wizardServicosStatus || {};
window.wizardFotos = window.wizardFotos || [];
window.wizardSignaturePad = window.wizardSignaturePad || null;

// Manter compatibilidade com codigo existente
var wizardStepAtual = window.wizardStepAtual;
var wizardTotalSteps = window.wizardTotalSteps;
var wizardServicosStatus = window.wizardServicosStatus;
var wizardFotos = window.wizardFotos;
var wizardSignaturePad = window.wizardSignaturePad;

// Inicializar Wizard quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    inicializarWizard();
});

function inicializarWizard() {
    // Inicializar status dos serviços
    const servicosItems = document.querySelectorAll('.wizard-servico-item');
    servicosItems.forEach(item => {
        const servicoId = item.getAttribute('data-servico-id');
        wizardServicosStatus[servicoId] = 'pendente';
    });

    atualizarResumoServicos();
}

// Navegacao do Wizard
function wizardProximo() {
    if (window.wizardStepAtual < window.wizardTotalSteps) {
        // Validar etapa atual
        if (!validarEtapa(window.wizardStepAtual)) {
            return;
        }

        window.wizardStepAtual++;
        atualizarWizardView();
    }
}
window.wizardProximo = wizardProximo;

function wizardAnterior() {
    if (window.wizardStepAtual > 1) {
        window.wizardStepAtual--;
        atualizarWizardView();
    }
}
window.wizardAnterior = wizardAnterior;

function irParaEtapa(etapa) {
    if (etapa >= 1 && etapa <= window.wizardTotalSteps) {
        window.wizardStepAtual = etapa;
        atualizarWizardView();
    }
}

function validarEtapa(etapa) {
    switch(etapa) {
        case 1:
            // Verificar se pelo menos um checklist está marcado
            const checks = [
                document.getElementById('checkConfirmarLocal'),
                document.getElementById('checkConfirmarCliente'),
                document.getElementById('checkConfirmarEquipamento')
            ];
            const algumMarcado = checks.some(cb => cb && cb.checked);
            if (!algumMarcado) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Por favor, confirme pelo menos um item para prosseguir.'
                });
                return false;
            }
            return true;

        case 2:
            // Verificar se todos os serviços foram avaliados
            const pendentes = Object.values(wizardServicosStatus).filter(s => s === 'pendente').length;
            if (pendentes > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Serviços Pendentes',
                    text: 'Ainda há ' + pendentes + ' serviço(s) sem status definido. Por favor, marque todos.'
                });
                return false;
            }
            return true;

        case 4:
            // Verificar se observações foram preenchidas
            const obs = document.getElementById('wizardObservacoes')?.value.trim();
            if (!obs) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Observações',
                    text: 'Por favor, preencha as observações do atendimento.'
                });
                return false;
            }
            return true;

        default:
            return true;
    }
}
window.validarEtapa = validarEtapa;

function atualizarWizardView() {
    // Atualizar indicador
    const stepIndicator = document.getElementById('stepIndicator');
    if (stepIndicator) {
        stepIndicator.textContent = 'Etapa ' + window.wizardStepAtual + ' de ' + window.wizardTotalSteps;
    }

    // Atualizar progress bar
    const progressBar = document.getElementById('wizardProgressBar');
    if (progressBar) {
        progressBar.className = 'wizard-progress-bar step-' + window.wizardStepAtual;
    }

    // Atualizar steps visuais
    document.querySelectorAll('.wizard-step').forEach(step => {
        const stepNum = parseInt(step.getAttribute('data-step'));
        step.classList.remove('active', 'completed');

        if (stepNum === window.wizardStepAtual) {
            step.classList.add('active');
        } else if (stepNum < window.wizardStepAtual) {
            step.classList.add('completed');
        }
    });

    // Mostrar conteudo da etapa atual
    document.querySelectorAll('.wizard-step-content').forEach(content => {
        content.classList.remove('active');
        if (parseInt(content.getAttribute('data-step')) === window.wizardStepAtual) {
            content.classList.add('active');
        }
    });

    // Se estiver na etapa 5, atualizar resumo e inicializar assinatura
    if (window.wizardStepAtual === 5) {
        atualizarResumoFinal();
        // Inicializar canvas nativo do cliente (igual ao técnico)
        if (!window.canvasCliente) {
            initCanvasCliente();
        }
    }
}
window.atualizarWizardView = atualizarWizardView;

// Inicializar/re-inicializar canvas de assinatura do wizard
// Inicializar canvas de assinatura do cliente (wizard) - igual ao técnico
function initCanvasCliente() {
    window.canvasCliente = document.getElementById('assinaturaCliente');

    if (!window.canvasCliente) {
        console.error('Canvas assinaturaCliente não encontrado');
        return;
    }

    window.ctxCliente = window.canvasCliente.getContext('2d');

    // Configurar dimensões
    const resizeCanvasCliente = function() {
        const rect = window.canvasCliente.getBoundingClientRect();
        window.canvasCliente.width = rect.width > 0 ? rect.width : 300;
        window.canvasCliente.height = 200;
        window.ctxCliente.strokeStyle = '#000';
        window.ctxCliente.lineWidth = 2;
        window.ctxCliente.lineCap = 'round';
    };

    // Aguardar um tick para garantir que o layout está pronto
    setTimeout(resizeCanvasCliente, 100);
    window.addEventListener('resize', resizeCanvasCliente);

    // Eventos do mouse
    window.canvasCliente.addEventListener('mousedown', function(e) {
        window.isDrawingCliente = true;
        const rect = window.canvasCliente.getBoundingClientRect();
        window.ctxCliente.beginPath();
        window.ctxCliente.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    });

    window.canvasCliente.addEventListener('mousemove', function(e) {
        if (!window.isDrawingCliente) return;
        const rect = window.canvasCliente.getBoundingClientRect();
        window.ctxCliente.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        window.ctxCliente.stroke();
    });

    window.canvasCliente.addEventListener('mouseup', function() {
        window.isDrawingCliente = false;
    });

    window.canvasCliente.addEventListener('mouseout', function() {
        window.isDrawingCliente = false;
    });

    // Eventos touch para mobile
    window.canvasCliente.addEventListener('touchstart', function(e) {
        e.preventDefault();
        window.isDrawingCliente = true;
        const rect = window.canvasCliente.getBoundingClientRect();
        const touch = e.touches[0];
        window.ctxCliente.beginPath();
        window.ctxCliente.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
    });

    window.canvasCliente.addEventListener('touchmove', function(e) {
        e.preventDefault();
        if (!window.isDrawingCliente) return;
        const rect = window.canvasCliente.getBoundingClientRect();
        const touch = e.touches[0];
        window.ctxCliente.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
        window.ctxCliente.stroke();
    });

    window.canvasCliente.addEventListener('touchend', function() {
        window.isDrawingCliente = false;
    });
}

// Limpar assinatura do cliente
function limparAssinaturaCliente() {
    if (window.ctxCliente && window.canvasCliente) {
        window.ctxCliente.clearRect(0, 0, window.canvasCliente.width, window.canvasCliente.height);
    }
}
window.limparAssinaturaCliente = limparAssinaturaCliente;

// Verificar se canvas tem desenho (assinatura)
function temAssinaturaCliente() {
    if (!window.canvasCliente || !window.ctxCliente) return false;
    try {
        const pixelData = window.ctxCliente.getImageData(0, 0, window.canvasCliente.width, window.canvasCliente.height).data;
        return pixelData.some(function(pixel, index) { return index % 4 === 3 && pixel > 0; });
    } catch (e) {
        return false;
    }
}
window.temAssinaturaCliente = temAssinaturaCliente;

// Controle de Serviços no Wizard
function setWizardServicoStatus(servicoId, status) {
    wizardServicosStatus[servicoId] = status;

    const item = document.querySelector('.wizard-servico-item[data-servico-id="' + servicoId + '"]');
    if (item) {
        // Remover classes anteriores
        item.classList.remove('status-conforme', 'status-nao_conforme');

        // Adicionar classe atual
        if (status !== 'pendente') {
            item.classList.add('status-' + status);
        }

        // Atualizar botões
        const botoes = item.querySelectorAll('.btn-status');
        botoes.forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-status') === status) {
                btn.classList.add('active');
            }
        });
    }

    atualizarResumoServicos();
}
window.setWizardServicoStatus = setWizardServicoStatus;

function atualizarResumoServicos() {
    const total = Object.keys(wizardServicosStatus).length;
    const executados = Object.values(wizardServicosStatus).filter(s => s === 'conforme').length;
    const naoExecutados = Object.values(wizardServicosStatus).filter(s => s === 'nao_conforme').length;
    const pendentes = total - executados - naoExecutados;

    const elPendente = document.getElementById('resumoPendente');
    const elExecutado = document.getElementById('resumoExecutado');

    if (elPendente) {
        elPendente.textContent = pendentes > 0 ? pendentes + ' pendente(s)' : '';
        elPendente.style.display = pendentes > 0 ? 'inline-block' : 'none';
    }

    if (elExecutado) {
        elExecutado.textContent = (executados + naoExecutados) + ' avaliado(s)';
    }
}
window.atualizarResumoServicos = atualizarResumoServicos;

// Fotos no Wizard
function abrirCameraWizard() {
    document.getElementById('wizardFotoInput').click();
}
window.abrirCameraWizard = abrirCameraWizard;

function processarFotoWizard(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const tipo = document.getElementById('tipoFotoWizard').value;

        const reader = new FileReader();
        reader.onload = function(e) {
            adicionarFotoWizard(e.target.result, tipo);
        };
        reader.readAsDataURL(file);
    }
}
window.processarFotoWizard = processarFotoWizard;

function adicionarFotoWizard(imagemBase64, tipo) {
    const foto = {
        id: Date.now(),
        imagem: imagemBase64,
        tipo: tipo,
        data: new Date().toISOString()
    };

    wizardFotos.push(foto);
    renderizarFotosWizard();
}
window.adicionarFotoWizard = adicionarFotoWizard;

function traduzirTipoFoto(tipo) {
    const tipos = {
        'antes': 'Antes',
        'durante': 'Durante',
        'depois': 'Depois',
        'detalhe': 'Detalhe'
    };
    return tipos[tipo] || tipo;
}
window.traduzirTipoFoto = traduzirTipoFoto;

function renderizarFotosWizard() {
    const container = document.getElementById('wizardFotosPreview');
    if (!container) return;

    // Atualizar contador
    const contadorTexto = document.getElementById('contadorTexto');
    if (contadorTexto) {
        const total = wizardFotos.length;
        contadorTexto.textContent = total + ' foto' + (total !== 1 ? 's' : '');
    }

    let html = '';
    wizardFotos.forEach(foto => {
        html += '<div class="foto-card-modern">' +
            '<img src="' + foto.imagem + '" alt="Foto">' +
            '<span class="foto-card-badge ' + foto.tipo + '" style="font-size: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80%;">' + traduzirTipoFoto(foto.tipo) + '</span>' +
            '<button type="button" class="foto-card-remove" onclick="removerFotoWizard(' + foto.id + ')">' +
                '<i class="bx bx-trash"></i>' +
            '</button>' +
        '</div>';
    });
    container.innerHTML = html;
}
window.renderizarFotosWizard = renderizarFotosWizard;

// Nova funcao para selecionar tipo de foto
function selecionarTipoFoto(tipo) {
    document.getElementById('tipoFotoWizard').value = tipo;

    // Atualizar tabs visuais
    document.querySelectorAll('.foto-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelector('.foto-tab[data-tipo="' + tipo + '"]').classList.add('active');
}
window.selecionarTipoFoto = selecionarTipoFoto;

// Nova funcao para abrir seletor de foto
function abrirSeletorFoto() {
    const input = document.getElementById('wizardFotoInput');

    // Em mobile, tentar usar camera diretamente
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        input.setAttribute('capture', 'environment');
    } else {
        input.removeAttribute('capture');
    }

    input.click();
}
window.abrirSeletorFoto = abrirSeletorFoto;

function removerFotoWizard(fotoId) {
    wizardFotos = wizardFotos.filter(f => f.id !== fotoId);
    renderizarFotosWizard();
}
window.removerFotoWizard = removerFotoWizard;

// Assinatura no Wizard - usar canvas nativo igual ao técnico
function limparAssinaturaWizard() {
    limparAssinaturaCliente();
}
window.limparAssinaturaWizard = limparAssinaturaWizard;

// Toggle Fullscreen para assinatura
function toggleFullscreenAssinatura() {
    const container = document.getElementById('assinaturaContainer');
    if (!container) return;

    const isFullscreen = container.classList.contains('fullscreen');

    if (isFullscreen) {
        // Sair do fullscreen
        container.classList.remove('fullscreen');
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    } else {
        // Entrar em fullscreen
        container.classList.add('fullscreen');
        if (container.requestFullscreen) {
            container.requestFullscreen();
        } else if (container.webkitRequestFullscreen) {
            container.webkitRequestFullscreen();
        } else if (container.msRequestFullscreen) {
            container.msRequestFullscreen();
        }
    }

    // Aguardar transição e redimensionar o canvas
    setTimeout(() => {
        if (window.canvasCliente && window.ctxCliente) {
            const rect = window.canvasCliente.getBoundingClientRect();
            window.canvasCliente.width = rect.width > 0 ? rect.width : 300;
            window.canvasCliente.height = isFullscreen ? 200 : (window.innerHeight - 150);
            window.ctxCliente.strokeStyle = '#000';
            window.ctxCliente.lineWidth = 2;
            window.ctxCliente.lineCap = 'round';
        }
    }, 300);
}
window.toggleFullscreenAssinatura = toggleFullscreenAssinatura;

// Salvar assinatura e fechar fullscreen
function salvarEFecharFullscreen() {
    // A assinatura já está salva no wizardSignaturePad
    // Apenas fechar o fullscreen
    const container = document.getElementById('assinaturaContainer');
    if (!container) return;

    // Mostrar feedback visual
    const btn = document.querySelector('.btn-salvar-fullscreen');
    if (btn) {
        btn.innerHTML = '<i class="bx bx-check-double"></i> Salvo!';
        btn.style.background = '#218838';
    }

    // Aguardar um momento para mostrar o feedback
    setTimeout(() => {
        // Sair do fullscreen
        container.classList.remove('fullscreen');
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }

        // Restaurar botão
        if (btn) {
            btn.innerHTML = '<i class="bx bx-check"></i> Salvar e Voltar';
            btn.style.background = '';
        }
    }, 500);
}
window.salvarEFecharFullscreen = salvarEFecharFullscreen;

// Listener para sair do fullscreen quando pressionar ESC
document.addEventListener('fullscreenchange', function() {
    const container = document.getElementById('assinaturaContainer');
    if (container && !document.fullscreenElement) {
        container.classList.remove('fullscreen');
        setTimeout(() => {
            inicializarAssinaturaWizard();
        }, 100);
    }
});

// Suporte para webkit browsers (iOS)
document.addEventListener('webkitfullscreenchange', function() {
    const container = document.getElementById('assinaturaContainer');
    if (container && !document.webkitFullscreenElement) {
        container.classList.remove('fullscreen');
        setTimeout(() => {
            inicializarAssinaturaWizard();
        }, 100);
    }
});

// Resumo Final
function atualizarResumoFinal() {
    const container = document.getElementById('wizardResumoFinal');
    if (!container) return;

    const observacoes = document.getElementById('wizardObservacoes')?.value || '';
    const totalFotos = wizardFotos.length;

    let servicosHtml = '';
    const servicosItems = document.querySelectorAll('.wizard-servico-item');
    servicosItems.forEach(item => {
        const servicoId = item.getAttribute('data-servico-id');
        const nomeEl = item.querySelector('.servico-nome-wizard');
        const nome = nomeEl ? nomeEl.textContent.trim() : 'Serviço';
        const status = wizardServicosStatus[servicoId] || 'pendente';

        let statusIcon = 'bx-circle';
        let statusClass = 'status-pendente';
        let statusText = 'Pendente';

        if (status === 'conforme') {
            statusIcon = 'bx-check';
            statusClass = 'status-ok';
            statusText = 'Executado';
        } else if (status === 'nao_conforme') {
            statusIcon = 'bx-x';
            statusClass = 'status-nok';
            statusText = 'Não Executado';
        }

        servicosHtml += '<div class="resumo-servico-item ' + statusClass + '">' +
            '<i class="bx ' + statusIcon + '"></i>' +
            '<span>' + nome + '</span>' +
            '<small>(' + statusText + ')</small>' +
        '</div>';
    });

    let html = '<div class="resumo-section">' +
        '<h6><i class="bx bx-wrench"></i> Serviços Executados</h6>' +
        '<div class="resumo-servicos-list">' +
            (servicosHtml || '<small>Nenhum serviço</small>') +
        '</div>' +
    '</div>' +
    '<div class="resumo-section">' +
        '<h6><i class="bx bx-camera"></i> Fotos</h6>' +
        '<p>' + totalFotos + ' foto(s) registrada(s)</p>' +
    '</div>';

    if (observacoes) {
        let obsResumo = observacoes.substring(0, 200);
        if (observacoes.length > 200) obsResumo += '...';
        html += '<div class="resumo-section">' +
            '<h6><i class="bx bx-note"></i> Observações</h6>' +
            '<p style="white-space: pre-wrap;">' + obsResumo + '</p>' +
        '</div>';
    }

    container.innerHTML = html;
}
window.atualizarResumoFinal = atualizarResumoFinal;

// Finalizar Wizard
function finalizarWizardAtendimento() {
    // Validar assinatura usando canvas nativo
    if (!window.canvasCliente || !temAssinaturaCliente()) {
        Swal.fire({
            icon: 'warning',
            title: 'Assinatura Obrigatória',
            text: 'Por favor, colete a assinatura do cliente.'
        });
        return;
    }

    const nomeAssinante = document.getElementById('wizardNomeAssinante')?.value.trim();
    if (!nomeAssinante) {
        Swal.fire({
            icon: 'warning',
            title: 'Nome Obrigatório',
            text: 'Por favor, informe o nome de quem está assinando.'
        });
        return;
    }

    const confirmarAssinatura = document.getElementById('checkConfirmarAssinatura');
    if (!confirmarAssinatura?.checked) {
        Swal.fire({
            icon: 'warning',
            title: 'Confirmação Necessária',
            text: 'Por favor, confirme que o serviço foi realizado e aceito.'
        });
        return;
    }

    // Coletar dados - usar canvas nativo igual ao técnico
    const assinatura = window.canvasCliente.toDataURL('image/png');
    console.log('Assinatura capturada:', assinatura.substring(0, 100) + '...');
    console.log('Tamanho da assinatura:', assinatura.length);

    const observacoes = document.getElementById('wizardObservacoes')?.value || '';
    const execucaoId = typeof window.execucaoId !== 'undefined' ? window.execucaoId : null;

    if (!execucaoId) {
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'ID da execução não encontrado. Por favor, recarregue a página e tente novamente.'
        });
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Salvando...',
        text: 'Enviando dados do atendimento',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Preparar serviços executados (apenas os marcados como conforme)
    const servicosExecutados = [];
    Object.entries(wizardServicosStatus).forEach(([id, status]) => {
        if (status === 'conforme') {
            servicosExecutados.push(id);
        }
    });

    // 1. Salvar todas as fotos primeiro
    const salvarFotos = async () => {
        const fotosSalvas = [];
        const csrf = getCsrfToken();

        for (const foto of wizardFotos) {
            try {
                const formData = new FormData();
                formData.append('execucao_id', execucaoId);
                formData.append('foto', foto.imagem);
                formData.append('descricao', foto.tipo ? 'Foto: ' + traduzirTipoFoto(foto.tipo) : 'Foto do atendimento');
                formData.append('tipo', foto.tipo || 'durante');
                formData.append(csrf.name, csrf.value);

                const response = await fetch('<?php echo site_url('tecnicos/adicionar_foto'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    fotosSalvas.push(result.foto_id);
                }
            } catch (error) {
                console.error('Erro ao salvar foto:', error);
            }
        }
        return fotosSalvas;
    };

    // 2. Finalizar execucao
    const finalizarExecucao = async () => {
        const csrf = getCsrfToken();
        const formData = new FormData();
        formData.append('execucao_id', execucaoId);
        formData.append('assinatura_cliente', assinatura);
        formData.append('nome_cliente_assina', nomeAssinante);
        formData.append('observacoes', observacoes);
        formData.append('servicos', JSON.stringify(servicosExecutados));
        formData.append('latitude', '0');
        formData.append('longitude', '0');
        formData.append(csrf.name, csrf.value);

        const response = await fetch('<?php echo site_url('tecnicos/finalizar_execucao'); ?>', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            if (response.status === 403) {
                throw new Error('Sessao expirada. Por favor, recarregue a pagina.');
            }
            throw new Error('Erro HTTP: ' + response.status);
        }

        const responseText = await response.text();
        try {
            return JSON.parse(responseText);
        } catch (e) {
            console.error('Resposta nao e JSON:', responseText);
            throw new Error('Erro no servidor. Resposta invalida.');
        }
    };

    // Executar sequência
    salvarFotos().then(() => {
        return finalizarExecucao();
    }).then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Atendimento Finalizado!',
                text: 'Todos os dados foram registrados com sucesso.',
                timer: 2000,
                showConfirmButton: false
            }).then(function() {
                window.location.href = '<?php echo site_url('tecnicos/relatorio_execucao/'); ?>' + window.osId;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: result.message || 'Erro ao finalizar atendimento'
            });
        }
    }).catch(error => {
        console.error('Erro:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Erro ao processar os dados. Tente novamente.'
        });
    });
}
window.finalizarWizardAtendimento = finalizarWizardAtendimento;

</script></div>
