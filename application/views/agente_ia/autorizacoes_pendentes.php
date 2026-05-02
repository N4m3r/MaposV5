<?php
/**
 * View: autorizacoes_pendentes.php
 * Painel de autorizacoes pendentes do agente IA
 */
?>
<style>
.autorizacao-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: #fff;
    transition: box-shadow 0.2s;
}
.autorizacao-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.autorizacao-card .acao-label {
    font-weight: 600;
    font-size: 1.1em;
    color: #2c3e50;
}
.autorizacao-card .token-code {
    font-family: monospace;
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.9em;
}
.autorizacao-card .meta {
    font-size: 0.85em;
    color: #777;
    margin-top: 8px;
}
.autorizacao-card .nivel-badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.75em;
    font-weight: 600;
    text-transform: uppercase;
}
.nivel-1 { background: #d4edda; color: #155724; }
.nivel-2 { background: #fff3cd; color: #856404; }
.nivel-3 { background: #cce5ff; color: #004085; }
.nivel-4 { background: #f8d7da; color: #721c24; }
.nivel-5 { background: #6c757d; color: #fff; }
.status-pendente { border-left: 4px solid #ffc107; }
.status-aprovada { border-left: 4px solid #28a745; }
.status-rejeitada { border-left: 4px solid #dc3545; }
.status-expirada { border-left: 4px solid #6c757d; }
</style>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-shield iconX"></i></span>
                <h5>Autorizacoes do Agente IA</h5>
                <div class="buttons">
                    <a title="Todas" class="btn btn-mini" href="?status=">Todas</a>
                    <a title="Pendentes" class="btn btn-mini btn-warning" href="?status=pendente">Pendentes</a>
                    <a title="Aprovadas" class="btn btn-mini btn-success" href="?status=aprovada">Aprovadas</a>
                    <a title="Rejeitadas" class="btn btn-mini btn-danger" href="?status=rejeitada">Rejeitadas</a>
                    <a title="Expiradas" class="btn btn-mini" href="?status=expirada">Expiradas</a>
                </div>
            </div>
            <div class="widget-content">

                <div class="row-fluid" style="margin-bottom:15px">
                    <div class="span6">
                        <strong>Total:</strong> <?php echo $total ?? 0; ?> autorizacoes
                        <?php if (!empty($filtroStatus)): ?>
                            <span class="label label-info">Status: <?php echo ucfirst($filtroStatus); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="span6" style="text-align:right">
                        <a href="<?php echo site_url('agente_ia/permissoes'); ?>" class="btn btn-info btn-small">
                            <i class="bx bx-cog"></i> Gerenciar Permissoes
                        </a>
                    </div>
                </div>

                <?php if (empty($autorizacoes)): ?>
                    <div class="alert alert-info">Nenhuma autorizacao encontrada.</div>
                <?php else: ?>
                    <div class="row-fluid">
                        <?php foreach ($autorizacoes as $aut): ?>
                            <div class="span6">
                                <div class="autorizacao-card status-<?php echo $aut['status']; ?>">
                                    <div class="row-fluid">
                                        <div class="span8">
                                            <div class="acao-label">
                                                <?php echo nomeAmigavelAcao($aut['acao'] ?? $aut['acao']); ?>
                                                <span class="nivel-badge nivel-<?php echo $aut['nivel_criticidade'] ?? 1; ?>">
                                                    Nivel <?php echo $aut['nivel_criticidade'] ?? 1; ?>
                                                </span>
                                            </div>
                                            <div class="meta">
                                                <strong>Token:</strong> <span class="token-code"><?php echo $aut['token']; ?></span><br>
                                                <strong>Numero:</strong> <?php echo $aut['numero_telefone']; ?><br>
                                                <?php if (!empty($aut['usuarios_id'])): ?>
                                                    <strong>Usuario ID:</strong> <?php echo $aut['usuarios_id']; ?> <?php endif; ?>
                                                <?php if (!empty($aut['clientes_id'])): ?>
                                                    <strong>Cliente ID:</strong> <?php echo $aut['clientes_id']; ?> <?php endif; ?>
                                                <br>
                                                <strong>Metodo:</strong> <?php echo ucfirst($aut['metodo_autorizacao'] ?? 'whatsapp'); ?>
                                            </div>
                                        </div>
                                        <div class="span4" style="text-align:right">
                                            <div class="meta">
                                                <?php echo date('d/m/Y H:i', strtotime($aut['created_at'])); ?>
                                            </div>
                                            <?php if ($aut['status'] === 'pendente'): ?>
                                                <form method="post" action="<?php echo site_url('agente_ia/responder'); ?>" style="margin-top:8px">
                                                    <input type="hidden" name="autorizacao_id" value="<?php echo $aut['id']; ?>">
                                                    <button type="submit" name="resposta" value="aprovar" class="btn btn-mini btn-success">
                                                        <i class="bx bx-check"></i> Aprovar
                                                    </button>
                                                    <button type="submit" name="resposta" value="rejeitar" class="btn btn-mini btn-danger" style="margin-left:4px">
                                                        <i class="bx bx-x"></i> Rejeitar
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="label label-<?php
                                                    echo match($aut['status']) {
                                                        'aprovada' => 'success',
                                                        'rejeitada' => 'important',
                                                        'expirada' => 'default',
                                                        'executada' => 'info',
                                                        default => 'warning'
                                                    };
                                                ?>">
                                                    <?php echo ucfirst($aut['status']); ?>
                                                </span>
                                                <?php if (!empty($aut['executed_at'])): ?>
                                                    <div class="meta" style="margin-top:5px">
                                                        Executado em <br><?php echo date('d/m/Y H:i', strtotime($aut['executed_at'])); ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($totalPages) && $totalPages > 1): ?>
                    <div class="pagination alternate" style="margin-top:15px">
                        <ul>
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="<?php echo ($p == ($page ?? 1)) ? 'active' : ''; ?>">
                                    <a href="?page=<?php echo $p; ?>&amp;status=<?php echo urlencode($filtroStatus ?? ''); ?>"><?php echo $p; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php
// Helper local para nomes amigaveis
if (!function_exists('nomeAmigavelAcao')) {
function nomeAmigavelAcao(string $acao): string {
    $map = [
        'criar_os' => 'Criar Ordem de Servico',
        'aprovar_orcamento' => 'Aprovar Orcamento',
        'gerar_cobranca' => 'Gerar Cobranca',
        'gerar_boleto' => 'Gerar Boleto',
        'emitir_nfse' => 'Emitir Nota Fiscal de Servico',
        'atualizar_status_os' => 'Atualizar Status da OS',
        'registrar_atividade' => 'Registrar Atividade',
        'excluir_os' => 'Excluir Ordem de Servico',
    ];
    return $map[$acao] ?? ucwords(str_replace('_', ' ', $acao));
}
}
?>
