<style>
    .template-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #ddd);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.2s;
    }
    .template-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .template-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .template-title {
        font-weight: 600;
        font-size: 15px;
        color: var(--heading-color, #333);
    }
    .template-chave {
        font-size: 12px;
        color: var(--text-muted, #666);
        font-family: monospace;
        background: #f0f0f0;
        padding: 2px 8px;
        border-radius: 4px;
    }
    .template-desc {
        font-size: 13px;
        color: var(--text-color, #666);
        margin-bottom: 10px;
    }
    .template-message {
        background: #f8f9fa;
        border-left: 3px solid #28a745;
        padding: 10px;
        border-radius: 0 4px 4px 0;
        font-size: 13px;
        color: #333;
        white-space: pre-wrap;
        max-height: 100px;
        overflow-y: auto;
    }
    .template-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border-color, #eee);
    }
    .template-badges {
        display: flex;
        gap: 8px;
    }
    .badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 12px;
        font-weight: 500;
    }
    .badge-success {
        background: #d4edda;
        color: #155724;
    }
    .badge-secondary {
        background: #e2e3e5;
        color: #383d41;
    }
    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
    }
    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }
    .template-actions {
        display: flex;
        gap: 8px;
    }
    .btn-icon {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.2s;
    }
    .btn-edit {
        background: #007bff;
        color: white;
    }
    .btn-edit:hover {
        background: #0056b3;
    }
    .btn-toggle {
        background: #6c757d;
        color: white;
    }
    .btn-toggle.active {
        background: #28a745;
    }
    .btn-toggle:hover {
        opacity: 0.8;
    }
    .category-section {
        margin-bottom: 30px;
    }
    .category-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--heading-color, #333);
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--border-color, #ddd);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .category-title i {
        font-size: 22px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="bx bx-message-square-dots"></i>
                </span>
                <h5>Templates de Mensagens</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('notificacoesConfig/configuracoes'); ?>" class="btn btn-mini">
                        <i class="bx bx-cog"></i> Configurações
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding">

                <?php
                // Agrupa templates por categoria
                $porCategoria = [];
                foreach ($templates as $template) {
                    $cat = $template->categoria;
                    if (!isset($porCategoria[$cat])) {
                        $porCategoria[$cat] = [];
                    }
                    $porCategoria[$cat][] = $template;
                }

                $iconesCategorias = [
                    'os' => 'bx bx-wrench',
                    'venda' => 'bx bx-cart',
                    'cobranca' => 'bx bx-money',
                    'marketing' => 'bx bx-bullhorn',
                    'sistema' => 'bx bx-cog',
                ];

                foreach ($porCategoria as $categoria => $itens):
                    $nomeCat = $categorias[$categoria] ?? ucfirst($categoria);
                    $icone = $iconesCategorias[$categoria] ?? 'bx bx-message';
                ?>
                    <div class="category-section" style="padding: 20px;">
                        <div class="category-title">
                            <i class="<?php echo $icone; ?>"></i>
                            <?php echo $nomeCat; ?>
                        </div>

                        <div class="row-fluid">
                            <?php foreach ($itens as $template): ?>
                                <div class="span6">
                                    <div class="template-card">
                                        <div class="template-header">
                                            <div>
                                                <div class="template-title"><?php echo htmlspecialchars($template->nome); ?></div>
                                                <span class="template-chave"><?php echo $template->chave; ?></span>
                                            </div>
                                        </div>

                                        <?php if ($template->descricao): ?>
                                            <div class="template-desc"><?php echo htmlspecialchars($template->descricao); ?></div>
                                        <?php endif; ?>

                                        <div class="template-message"><?php echo nl2br(htmlspecialchars(substr($template->mensagem, 0, 200))); ?><?php echo strlen($template->mensagem) > 200 ? '...' : ''; ?></div>

                                        <div class="template-footer">
                                            <div class="template-badges">
                                                <span class="badge badge-info"><?php echo strtoupper($template->canal); ?></span>
                                                <?php if ($template->ativo): ?>
                                                    <span class="badge badge-success">Ativo</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Inativo</span>
                                                <?php endif; ?>
                                                <?php if ($template->e_marketing): ?>
                                                    <span class="badge badge-warning">Marketing</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="template-actions">
                                                <a href="<?php echo site_url('notificacoesConfig/editar_template/' . $template->id); ?>" class="btn-icon btn-edit" title="Editar">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <a href="<?php echo site_url('notificacoesConfig/toggle_template/' . $template->id); ?>"
                                                   class="btn-icon btn-toggle <?php echo $template->ativo ? 'active' : ''; ?>" title="Ativar/Desativar">
                                                    <i class="bx bx-power-off"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($templates)): ?>
                    <div style="padding: 40px; text-align: center; color: #666;">
                        <i class="bx bx-message-square" style="font-size: 48px; display: block; margin-bottom: 15px; color: #ddd;"></i>
                        Nenhum template encontrado.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
