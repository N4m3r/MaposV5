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

<script>
function verChecklist(id, nome) {
    // Implementar visualização modal
    alert('Visualizando: ' + nome + ' (ID: ' + id + ')');
}

function editarChecklist(id) {
    // Redirecionar para edição
    window.location.href = '<?= site_url('tecnicos_admin/editar_checklist/') ?>' + id;
}
</script>
