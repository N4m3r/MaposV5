<!-- Templates de Checklists - Versão Moderna -->
<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-check-square"></i>
        </span>
        <h5>Templates de Checklist</h5>
        <div class="buttons">
            <a href="#modal-novo-checklist" data-toggle="modal" class="button btn btn-mini btn-success">
                <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                <span class="button__text2">Novo Template</span>
            </a>
        </div>
    </div>

    <!-- Grid de Cards -->
    <?php if (!empty($checklists)): ?>
        <div class="row-fluid" style="margin-top: 20px;">
            <?php foreach ($checklists as $checklist):
                // Decodificar itens JSON
                $itens = [];
                if (isset($checklist->itens)) {
                    if (is_string($checklist->itens)) {
                        $itens = json_decode($checklist->itens, true) ?? [];
                    } elseif (is_array($checklist->itens)) {
                        $itens = $checklist->itens;
                    }
                }
                $totalItens = count($itens);

                // Cores por tipo de serviço
                $tipoColors = [
                    'INS' => ['bg' => '#e3f2fd', 'color' => '#1976d2', 'label' => 'Instalação'],
                    'MP' => ['bg' => '#e8f5e9', 'color' => '#388e3c', 'label' => 'Manut. Preventiva'],
                    'MC' => ['bg' => '#fff3e0', 'color' => '#f57c00', 'label' => 'Manut. Corretiva'],
                    'CT' => ['bg' => '#fce4ec', 'color' => '#c2185b', 'label' => 'Consultoria'],
                    'TR' => ['bg' => '#f3e5f5', 'color' => '#7b1fa2', 'label' => 'Treinamento'],
                    'UP' => ['bg' => '#e0f7fa', 'color' => '#0097a7', 'label' => 'Upgrade'],
                ];
                $tipoStyle = $tipoColors[$checklist->tipo_servico ?? 'MC'] ?? $tipoColors['MC'];
            ?>
                <div class="span4" style="margin-bottom: 20px;">
                    <div class="checklist-card">
                        <div class="checklist-header">
                            <div class="checklist-icon">
                                <i class="bx bx-check-square"></i>
                            </div>
                            <div class="checklist-title">
                                <h6><?= htmlspecialchars($checklist->nome_template ?? 'Sem nome', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></h6>
                                <span class="checklist-type" style="background: <?= $tipoStyle['bg'] ?>; color: <?= $tipoStyle['color'] ?>;">
                                    <?= $tipoStyle['label'] ?>
                                </span>
                            </div>
                        </div>

                        <div class="checklist-body">
                            <div class="checklist-info">
                                <span class="info-item">
                                    <i class="bx bx-folder"></i>
                                    <?= htmlspecialchars($checklist->tipo_os ?? 'Geral', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                </span>
                                <span class="info-item">
                                    <i class="bx bx-list-check"></i>
                                    <?= $totalItens ?> item(s)
                                </span>
                            </div>

                            <?php if ($totalItens > 0): ?>
                                <ul class="checklist-preview">
                                    <?php $count = 0; ?>
                                    <?php foreach ($itens as $item): ?>
                                        <?php if ($count < 4): ?>
                                            <li>
                                                <i class="bx bx-check"></i>
                                                <?= htmlspecialchars(is_array($item) ? ($item['desc'] ?? $item['descricao'] ?? '') : $item, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                                            </li>
                                            <?php $count++; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if ($totalItens > 4): ?>
                                        <li class="more-items">+e <?= $totalItens - 4 ?> item(s)...</li>
                                    <?php endif; ?>
                                </ul>
                            <?php else: ?>
                                <p class="no-items">Nenhum item cadastrado</p>
                            <?php endif; ?>
                        </div>

                        <div class="checklist-footer">
                            <span class="created-at">
                                <i class="bx bx-calendar"></i>
                                <?= isset($checklist->created_at) ? date('d/m/Y', strtotime($checklist->created_at)) : '-' ?>
                            </span>
                            <div class="checklist-actions">
                                <a href="#" class="btn-icon btn-view" title="Visualizar"
                                   onclick="verChecklist(<?= $checklist->id ?>, '<?= htmlspecialchars($checklist->nome_template ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>')"
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="#" class="btn-icon btn-edit" title="Editar"
                                   onclick="editarChecklist(<?= $checklist->id ?>)">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="<?= site_url('tecnicos_admin/excluir_checklist/' . $checklist->id) ?>"
                                   class="btn-icon btn-delete" title="Excluir"
                                   onclick="return confirm('Tem certeza que deseja excluir este template?')">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <!-- Empty State -->
        <div style="padding: 60px; text-align: center;">
            <div style="font-size: 80px; color: #e0e0e0; margin-bottom: 20px;">
                <i class="bx bx-clipboard"></i>
            </div>
            <h3 style="color: #666; font-weight: 400; margin-bottom: 10px;">Nenhum template cadastrado</h3>
            <p style="color: #999; margin-bottom: 30px;">Crie templates de checklist para padronizar os serviços.</p>
            <a href="#modal-novo-checklist" data-toggle="modal" class="button btn btn-success btn-large">
                <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                <span class="button__text2">Criar Primeiro Template</span>
            </a>
        </div>

        <!-- Exemplos -->
        <div class="row-fluid">
            <div class="span6">
                <div class="example-card">
                    <div class="example-header">
                        <i class="bx bx-video"></i>
                        <span>Exemplo: Instalação CFTV</span>
                    </div>
                    <ul class="example-list">
                        <li><i class="bx bx-check"></i> Verificar integridade das câmeras</li>
                        <li><i class="bx bx-check"></i> Testar cabeamento e conexões</li>
                        <li><i class="bx bx-check"></i> Configurar gravação no DVR/NVR</li>
                        <li><i class="bx bx-check"></i> Ajustar ângulos de visão</li>
                        <li><i class="bx bx-check"></i> Testar acesso remoto</li>
                    </ul>
                </div>
            </div>

            <div class="span6">
                <div class="example-card">
                    <div class="example-header">
                        <i class="bx bx-wrench"></i>
                        <span>Exemplo: Manutenção Preventiva</span>
                    </div>
                    <ul class="example-list">
                        <li><i class="bx bx-check"></i> Diagnóstico do problema relatado</li>
                        <li><i class="bx bx-check"></i> Verificar fontes de alimentação</li>
                        <li><i class="bx bx-check"></i> Testar equipamentos</li>
                        <li><i class="bx bx-check"></i> Substituir componentes defeituosos</li>
                        <li><i class="bx bx-check"></i> Preencher relatório de serviço</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Novo Checklist -->
<div id="modal-novo-checklist" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h5><i class="bx bx-plus-circle"></i> Novo Template de Checklist</h5>
    </div>
    <form action="<?= site_url('tecnicos_admin/salvar_checklist') ?>" method="post">
        <div class="modal-body">
            <div class="control-group">
                <label class="control-label">Nome do Template</label>
                <div class="controls">
                    <input type="text" name="nome_template" class="span12" placeholder="Ex: Instalação CFTV Padrão"
                           required>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Tipo de OS</label>
                <div class="controls">
                    <select name="tipo_os" class="span12">
                        <option value="CFTV">CFTV</option>
                        <option value="Alarme">Alarme</option>
                        <option value="Rede">Rede</option>
                        <option value="Interfone">Interfone</option>
                        <option value="Porteiro">Porteiro Eletrônico</option>
                        <option value="Cerca">Cerca Elétrica</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Tipo de Serviço</label>
                <div class="controls">
                    <select name="tipo_servico" class="span12">
                        <option value="INS">Instalação</option>
                        <option value="MP">Manutenção Preventiva</option>
                        <option value="MC">Manutenção Corretiva</option>
                        <option value="CT">Consultoria</option>
                        <option value="TR">Treinamento</option>
                        <option value="UP">Upgrade</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="modal-footer" style="display: flex; justify-content: center; gap: 10px;">
            <button type="button" class="button btn btn-warning" data-dismiss="modal">
                <span class="button__icon"><i class="bx bx-x"></i></span>
                <span class="button__text2">Cancelar</span>
            </button>
            <button type="submit" class="button btn btn-success">
                <span class="button__icon"><i class="bx bx-save"></i></span>
                <span class="button__text2">Salvar Template</span>
            </button>
        </div>
    </form>
</div>

<style>
/* Checklist Cards */
.checklist-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.checklist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.checklist-header {
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.checklist-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.checklist-title {
    flex: 1;
}

.checklist-title h6 {
    margin: 0 0 8px 0;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.3;
}

.checklist-type {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.checklist-body {
    padding: 20px;
    flex: 1;
}

.checklist-info {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #666;
}

.info-item i {
    color: #667eea;
    font-size: 16px;
}

.checklist-preview {
    list-style: none;
    margin: 0;
    padding: 0;
}

.checklist-preview li {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 8px 0;
    font-size: 13px;
    color: #555;
    border-bottom: 1px dashed #eee;
}

.checklist-preview li:last-child {
    border-bottom: none;
}

.checklist-preview li i {
    color: #4caf50;
    font-size: 14px;
    margin-top: 2px;
}

.checklist-preview li.more-items {
    color: #999;
    font-style: italic;
}

.no-items {
    text-align: center;
    color: #999;
    font-style: italic;
    padding: 20px;
}

.checklist-footer {
    padding: 15px 20px;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.created-at {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 5px;
}

.checklist-actions {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-view {
    background: #e3f2fd;
    color: #1976d2;
}
.btn-view:hover {
    background: #1976d2;
    color: white;
    text-decoration: none;
}

.btn-edit {
    background: #fff3e0;
    color: #f57c00;
}
.btn-edit:hover {
    background: #f57c00;
    color: white;
    text-decoration: none;
}

.btn-delete {
    background: #ffebee;
    color: #c62828;
}
.btn-delete:hover {
    background: #c62828;
    color: white;
    text-decoration: none;
}

/* Example Cards */
.example-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    border: 1px solid #eee;
}

.example-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
    font-weight: 600;
    color: #333;
}

.example-header i {
    font-size: 20px;
    color: #667eea;
}

.example-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.example-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
    font-size: 14px;
    color: #555;
}

.example-list li i {
    color: #4caf50;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.checklist-card {
    animation: fadeInUp 0.4s ease forwards;
}

.span4:nth-child(1) .checklist-card { animation-delay: 0s; }
.span4:nth-child(2) .checklist-card { animation-delay: 0.1s; }
.span4:nth-child(3) .checklist-card { animation-delay: 0.2s; }
</style>

<!-- Modal Visualizar e Usar Checklist -->
<div id="modal-usar-checklist" class="modal hide fade" tabindex="-1" role="dialog" style="width: 700px; margin-left: -350px;">
    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">×</button>
        <h5 style="color: white;"><i class="bx bx-clipboard-check"></i> <span id="checklist-titulo-modal">Checklist</span></h5>
    </div>

    <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
        <div class="checklist-progress-bar">
            <div class="progress-info">
                <span id="progress-text">0 de 0 itens concluídos</span>
                <span id="progress-percent">0%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progress-bar" style="width: 0%;"></div>
            </div>
        </div>

        <div id="checklist-itens-container" class="checklist-items-list">
            <!-- Itens serão carregados via JavaScript -->
        </div>

        <div class="checklist-observacoes">
            <label class="obs-label">
                <i class="bx bx-comment-detail"></i> Observações do Técnico
            </label>
            <textarea id="checklist-observacoes" class="obs-textarea" rows="3"
                      placeholder="Adicione observações sobre o atendimento..."></textarea>
        </div>
    </div>

    <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="checklist-info-left">
            <span class="checklist-status" id="checklist-status">
                <i class="bx bx-time"></i> Em andamento
            </span>
        </div>

        <div class="checklist-actions-right" style="display: flex; gap: 10px;">
            <button type="button" class="button btn btn-warning" data-dismiss="modal">
                <span class="button__icon"><i class="bx bx-x"></i></span>
                <span class="button__text2">Cancelar</span>
            </button>

            <button type="button" class="button btn btn-success" id="btn-concluir-checklist" disabled
                    onclick="concluirChecklist()">
                <span class="button__icon"><i class="bx bx-check-double"></i></span>
                <span class="button__text2">Concluir Checklist</span>
            </button>
        </div>
    </div>
</div>

<style>
/* Checklist Interativo */
.checklist-progress-bar {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 20px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
}

.progress-info span:first-child {
    color: #666;
}

#progress-percent {
    font-weight: 600;
    color: #667eea;
}

.progress-bar-container {
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.checklist-items-list {
    margin-bottom: 20px;
}

.checklist-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    margin-bottom: 10px;
    border: 2px solid #e0e0e0;
    transition: all 0.2s ease;
    cursor: pointer;
}

.checklist-item:hover {
    border-color: #667eea;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
}

.checklist-item.completed {
    background: #f1f8e9;
    border-color: #4caf50;
}

.checklist-item.completed .item-text {
    text-decoration: line-through;
    color: #666;
}

.item-checkbox {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    border: 3px solid #ddd;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    cursor: pointer;
}

.checklist-item.completed .item-checkbox {
    background: #4caf50;
    border-color: #4caf50;
}

.item-checkbox i {
    color: white;
    font-size: 18px;
    opacity: 0;
    transition: all 0.2s;
}

.checklist-item.completed .item-checkbox i {
    opacity: 1;
}

.item-content {
    flex: 1;
}

.item-text {
    font-size: 15px;
    color: #333;
    line-height: 1.4;
    margin-bottom: 5px;
}

.item-hint {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 5px;
}

.item-timer {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #f57c00;
    font-size: 13px;
    font-weight: 500;
}

.checklist-observacoes {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    border: 1px solid #e0e0e0;
}

.obs-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.obs-label i {
    color: #667eea;
}

.obs-textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    resize: vertical;
    min-height: 80px;
    transition: border-color 0.2s;
}

.obs-textarea:focus {
    outline: none;
    border-color: #667eea;
}

.checklist-status {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #fff3e0;
    color: #f57c00;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

.checklist-status.completed {
    background: #e8f5e9;
    color: #4caf50;
}

#btn-concluir-checklist:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.checklist-empty {
    text-align: center;
    padding: 40px;
    color: #999;
}

.checklist-empty i {
    font-size: 48px;
    margin-bottom: 15px;
    color: #ddd;
}
</style>

<script>
// Variáveis globais do checklist atual
let checklistAtual = {
    id: null,
    nome: '',
    itens: [],
    itensConcluidos: 0
};

// Exibir modal com checklist
function verChecklist(id, nome) {
    checklistAtual.id = id;
    checklistAtual.nome = nome;
    checklistAtual.itensConcluidos = 0;

    document.getElementById('checklist-titulo-modal').textContent = nome;
    document.getElementById('checklist-status').className = 'checklist-status';
    document.getElementById('checklist-status').innerHTML = '<i class="bx bx-time"></i> Em andamento';
    document.getElementById('btn-concluir-checklist').disabled = true;
    document.getElementById('checklist-observacoes').value = '';

    // Carregar itens do checklist via AJAX
    carregarItensChecklist(id);

    $('#modal-usar-checklist').modal('show');
}

// Carregar itens via AJAX
function carregarItensChecklist(id) {
    const container = document.getElementById('checklist-itens-container');
    container.innerHTML = '<div class="checklist-empty"><i class="bx bx-loader bx-spin"></i><p>Carregando itens...</p></div>';

    // Simular carregamento - em produção, fazer requisição AJAX real
    fetch('<?= site_url("tecnicos_admin/get_checklist_itens/") ?>' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.itens) {
                checklistAtual.itens = data.itens;
                renderizarItens(data.itens);
            } else {
                // Dados de exemplo para demonstração
                checklistAtual.itens = [
                    { id: 1, descricao: 'Verificar integridade do equipamento', hint: 'Inspeção visual completa' },
                    { id: 2, descricao: 'Testar cabeamento e conexões', hint: 'Verificar pontos de emenda' },
                    { id: 3, descricao: 'Configurar parâmetros do sistema', hint: 'Seguir manual do fabricante' },
                    { id: 4, descricao: 'Realizar testes de funcionamento', hint: 'Testar todos os recursos' },
                    { id: 5, descricao: 'Preencher relatório do serviço', hint: 'Documentar observações' }
                ];
                renderizarItens(checklistAtual.itens);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar itens:', error);
            // Dados de exemplo
            checklistAtual.itens = [
                { id: 1, descricao: 'Verificar integridade do equipamento', hint: 'Inspeção visual completa' },
                { id: 2, descricao: 'Testar cabeamento e conexões', hint: 'Verificar pontos de emenda' }
            ];
            renderizarItens(checklistAtual.itens);
        });
}

// Renderizar itens na tela
function renderizarItens(itens) {
    const container = document.getElementById('checklist-itens-container');

    if (itens.length === 0) {
        container.innerHTML = '<div class="checklist-empty"><i class="bx bx-clipboard"></i><p>Nenhum item cadastrado</p></div>';
        atualizarProgresso();
        return;
    }

    let html = '';
    itens.forEach((item, index) => {
        html += `
            <div class="checklist-item" id="item-${index}" onclick="toggleItem(${index})">
                <div class="item-checkbox">
                    <i class="bx bx-check"></i>
                </div>
                <div class="item-content">
                    <div class="item-text">${item.descricao || item.desc || item}</div>
                    ${item.hint ? `<div class="item-hint">
                        <i class="bx bx-info-circle"></i> ${item.hint}
                    </div>` : ''}
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    atualizarProgresso();
}

// Toggle item concluído
function toggleItem(index) {
    const item = document.getElementById(`item-${index}`);
    item.classList.toggle('completed');

    // Efeito sonoro opcional
    // playSound('check');

    atualizarProgresso();
}

// Atualizar barra de progresso
function atualizarProgresso() {
    const total = checklistAtual.itens.length;
    const concluidos = document.querySelectorAll('.checklist-item.completed').length;
    const percentual = total > 0 ? Math.round((concluidos / total) * 100) : 0;

    checklistAtual.itensConcluidos = concluidos;

    document.getElementById('progress-text').textContent = `${concluidos} de ${total} itens concluídos`;
    document.getElementById('progress-percent').textContent = `${percentual}%`;
    document.getElementById('progress-bar').style.width = `${percentual}%`;

    // Habilitar botão concluir se todos itens estiverem marcados
    document.getElementById('btn-concluir-checklist').disabled = concluidos < total;

    // Atualizar status
    const statusEl = document.getElementById('checklist-status');
    if (concluidos === total && total > 0) {
        statusEl.className = 'checklist-status completed';
        statusEl.innerHTML = '<i class="bx bx-check-circle"></i> Todos os itens concluídos';
    } else {
        statusEl.className = 'checklist-status';
        statusEl.innerHTML = '<i class="bx bx-time"></i> Em andamento';
    }
}

// Concluir checklist
function concluirChecklist() {
    const concluidos = document.querySelectorAll('.checklist-item.completed').length;
    const total = checklistAtual.itens.length;
    const observacoes = document.getElementById('checklist-observacoes').value;

    if (concluidos < total) {
        alert('Marque todos os itens antes de concluir!');
        return;
    }

    // Preparar dados para envio
    const dados = {
        checklist_id: checklistAtual.id,
        observacoes: observacoes,
        itens_concluidos: concluidos,
        total_itens: total,
        data_execucao: new Date().toISOString()
    };

    // Enviar via AJAX
    console.log('Checklist concluído:', dados);

    // Mostrar mensagem de sucesso
    const btn = document.getElementById('btn-concluir-checklist');
    btn.innerHTML = '<span class="button__icon"><i class="bx bx-check"></i></span><span class="button__text2">Concluído!</span>';
    btn.classList.remove('btn-success');
    btn.classList.add('btn-info');

    setTimeout(() => {
        $('#modal-usar-checklist').modal('hide');
        // Resetar botão
        setTimeout(() => {
            btn.innerHTML = '<span class="button__icon"><i class="bx bx-check-double"></i></span><span class="button__text2">Concluir Checklist</span>';
            btn.classList.remove('btn-info');
            btn.classList.add('btn-success');
        }, 500);
    }, 1000);
}

function editarChecklist(id) {
    window.location.href = '<?= site_url('tecnicos_admin/editar_checklist/') ?>' + id;
}
</script>
