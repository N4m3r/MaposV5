<?php
/**
 * View: logs_conversa.php
 * Historico de conversas com o agente IA
 */
?>

<style>
.log-row td { font-size:0.85em; vertical-align:middle; }
.log-msg { max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.log-msg:hover { white-space:normal; overflow:visible; }
.badge-tipo { font-size:0.75em; padding:2px 6px; border-radius:3px; }
.tipo-entrada  { background:#d4edda; color:#155724; }
.tipo-saida     { background:#cce5ff; color:#004085; }
.tipo-sistema   { background:#fff3cd; color:#856404; }
.tipo-erro      { background:#f8d7da; color:#721c24; }
</style>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-message-square-detail iconX"></i></span>
                <h5>Logs de Conversa do Agente IA</h5>
                <div class="buttons">
                    <form method="get" class="form-inline" style="margin:0">
                        <input type="text" name="numero" placeholder="Numero..." class="input-small" value="<?php echo $_GET['numero'] ?? ''; ?>">
                        <select name="tipo" class="input-small">
                            <option value="">Todos</option>
                            <option value="entrada" <?php echo ($_GET['tipo'] ?? '')==='entrada'?'selected':''; ?>>Entrada</option>
                            <option value="saida" <?php echo ($_GET['tipo'] ?? '')==='saida'?'selected':''; ?>>Saida</option>
                            <option value="sistema" <?php echo ($_GET['tipo'] ?? '')==='sistema'?'selected':''; ?>>Sistema</option>
                            <option value="erro" <?php echo ($_GET['tipo'] ?? '')==='erro'?'selected':''; ?>>Erro</option>
                        </select>
                        <input type="date" name="data" class="input-small" value="<?php echo $_GET['data'] ?? ''; ?>">
                        <button type="submit" class="btn btn-mini"><i class="bx bx-search"></i></button>
                    </form>
                </div>
            </div>

            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Numero</th>
                            <th>Tipo</th>
                            <th>Intencao</th>
                            <th>Acao</th>
                            <th>Mensagem</th>
                            <th>Metadados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="8" class="center">Nenhum log encontrado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr class="log-row">
                                    <td><?php echo $log['id']; ?></td>
                                    <td><?php echo date('d/m H:i', strtotime($log['created_at'])); ?></td>
                                    <td><?php echo $log['numero_telefone']; ?></td>
                                    <td><span class="badge-tipo tipo-<?php echo $log['tipo']; ?>"><?php echo ucfirst($log['tipo']); ?></span></td>
                                    <td><?php echo $log['intencao_detectada'] ?? '-'; ?></td>
                                    <td><?php echo $log['acao_executada'] ?? '-'; ?></td>
                                    <td><div class="log-msg" title="<?php echo htmlspecialchars($log['mensagem']); ?>"><?php echo nl2br(htmlspecialchars(mb_strimwidth($log['mensagem'], 0, 120, '...'))); ?></div></td>
                                    <td><?php
                                        $meta = $log['metadados_json'] ?? null;
                                        if ($meta) {
                                            $d = json_decode($meta, true);
                                            echo '<span class="label">' . count($d ?? []) . ' campos</span>';
                                        } else { echo '-'; }
                                    ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (!empty($totalPages) && $totalPages > 1): ?>
            <div class="pagination alternate">
                <ul>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?
                        <li class="<?php echo ($p == ($page ?? 1)) ? 'active' : ''; ?>">
                            <a href="?page=<?php echo $p; ?>&amp;numero=<?php echo urlencode($_GET['numero'] ?? ''); ?>&amp;tipo=<?php echo urlencode($_GET['tipo'] ?? ''); ?>&amp;data=<?php echo urlencode($_GET['data'] ?? ''); ?>"><?php echo $p; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
