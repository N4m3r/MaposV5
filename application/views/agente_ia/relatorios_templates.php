<?php
/**
 * View: relatorios_templates.php
 * Gerenciamento de templates de relatorio do Agente IA
 */
?>

<style>
.template-card {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 12px;
    background: #fff;
    transition: box-shadow 0.2s;
}
.template-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.template-card .titulo {
    font-weight: 700;
    font-size: 1.05em;
    color: #2d3436;
    margin-bottom: 4px;
}
.template-card .tipo-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75em;
    font-weight: 600;
    color: #fff;
    margin-right: 6px;
}
.badge-os { background: #4facfe; }
.badge-financeiro { background: #11998e; }
.badge-vendas { background: #f2994a; }
.badge-clientes { background: #a18cd1; }
.badge-estoque { background: #636e72; }
.badge-tecnico { background: #eb3349; }
.badge-inadimplencia { background: #f45c43; }
.badge-satisfacao { background: #6C5CE7; }
.template-card .desc {
    font-size: 0.85em;
    color: #636e72;
    margin: 6px 0;
}
.template-card .acoes {
    margin-top: 8px;
    text-align: right;
}
.preview-box {
    background: #f8f9fa;
    border-left: 3px solid #4facfe;
    padding: 10px 12px;
    margin-top: 8px;
    font-size: 0.82em;
    color: #495057;
    font-family: monospace;
    white-space: pre-wrap;
}
.form-template textarea {
    font-family: monospace;
    min-height: 200px;
}
</style>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-file iconX"></i></span>
                <h5>Templates de Relatorio - Agente IA</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('agente_ia'); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                    <a href="#modalNovoTemplate" data-toggle="modal" class="btn btn-success btn-mini">
                        <i class="bx bx-plus"></i> Novo Template
                    </a>
                </div>
            </div>

            <div class="widget-content">
                <div class="alert alert-info">
                    <strong>Variaveis disponiveis nos templates:</strong>
                    <code>{{cliente_nome}}</code>, <code>{{periodo_inicio}}</code>, <code>{{periodo_fim}}</code>,
                    <code>{{total}}</code>, <code>{{valor}}</code>, <code>{{data_geracao}}</code>,
                    <code>{{empresa_nome}}</code>, <code>{{logo_url}}</code>.
                    Use <code>{{#each itens}} ... {{/each}}</code> para loops de tabelas.
                </div>

                <?php if (empty($templates)): ?>
                    <div class="alert alert-warning">
                        Nenhum template cadastrado. Clique em <strong>Novo Template</strong> para criar o primeiro.
                    </div>
                <?php else: ?>
                    <div class="row-fluid">
                        <?php foreach ($templates as $t): ?>
                            <div class="span6">
                                <div class="template-card">
                                    <div class="titulo">
                                        <span class="tipo-badge badge-<?php echo $t['tipo']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $t['tipo'])); ?>
                                        </span>
                                        <?php echo htmlspecialchars($t['nome']); ?>
                                        <?php if (!empty($t['ativo'])): ?>
                                            <span class="label label-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="label label-inverse">Inativo</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="desc"><?php echo htmlspecialchars($t['descricao'] ?? ''); ?></div>
                                    <div class="preview-box"><?php echo nl2br(htmlspecialchars(substr($t['conteudo_html'] ?? '', 0, 250))) . (strlen($t['conteudo_html'] ?? '') > 250 ? '...' : ''); ?></div>
                                    <div class="acoes">
                                        <a href="<?php echo site_url('agente_ia/editar_template/' . $t['id']); ?>" class="btn btn-mini btn-info">
                                            <i class="bx bx-edit"></i> Editar
                                        </a>
                                        <?php if (!empty($t['ativo'])): ?>
                                            <a href="<?php echo site_url('agente_ia/desativar_template/' . $t['id']); ?>" class="btn btn-mini btn-warning" onclick="return confirm('Desativar este template?');">
                                                <i class="bx bx-hide"></i> Desativar
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo site_url('agente_ia/ativar_template/' . $t['id']); ?>" class="btn btn-mini btn-success">
                                                <i class="bx bx-show"></i> Ativar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Template -->
<div id="modalNovoTemplate" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><i class="bx bx-plus"></i> Novo Template de Relatorio</h3>
    </div>
    <form method="post" action="<?php echo site_url('agente_ia/salvar_template'); ?>">
        <div class="modal-body form-template">
            <div class="control-group">
                <label>Nome do Template</label>
                <input type="text" name="nome" class="span12" required placeholder="Ex: Relatorio de OS Mensal - Layout 1">
            </div>
            <div class="control-group">
                <label>Tipo de Relatorio</label>
                <select name="tipo" class="span12" required>
                    <option value="os_diario">OS Diario</option>
                    <option value="os_mensal">OS Mensal</option>
                    <option value="financeiro">Financeiro</option>
                    <option value="vendas">Vendas</option>
                    <option value="clientes">Clientes</option>
                    <option value="estoque">Estoque</option>
                    <option value="tecnico">Produtividade Tecnico</option>
                    <option value="inadimplencia">Inadimplencia</option>
                    <option value="satisfacao">Satisfacao (NPS)</option>
                    <option value="historico_cliente">Historico do Cliente</option>
                </select>
            </div>
            <div class="control-group">
                <label>Descricao</label>
                <input type="text" name="descricao" class="span12" placeholder="Breve descricao do template">
            </div>
            <div class="control-group">
                <label>Conteudo HTML do Template</label>
                <textarea name="conteudo_html" class="span12" required placeholder="&lt;table&gt;...&lt;/table&gt;">{{cliente_nome}} - {{periodo_inicio}} ate {{periodo_fim}}</textarea>
            </div>
            <div class="control-group">
                <label class="checkbox">
                    <input type="checkbox" name="ativo" value="1" checked> Ativo
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar Template</button>
        </div>
    </form>
</div>
