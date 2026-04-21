<style>
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
    }
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #007bff;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 13px;
        color: #6c757d;
    }
    .filter-section {
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }
    .filter-group {
        flex: 1;
        min-width: 150px;
    }
    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 13px;
        font-weight: 500;
        color: #495057;
    }
    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
    }
    .filter-group input:focus,
    .filter-group select:focus {
        border-color: #80bdff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }
    .btn-filter {
        padding: 8px 20px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-filter:hover {
        background: #0056b3;
    }
    .btn-clear {
        padding: 8px 20px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-clear:hover {
        background: #545b62;
    }
    .logs-table {
        width: 100%;
        border-collapse: collapse;
    }
    .logs-table th,
    .logs-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }
    .logs-table th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 13px;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .logs-table tr:hover {
        background: #f8f9fa;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-pendente { background: #fff3cd; color: #856404; }
    .status-enviando { background: #d1ecf1; color: #0c5460; }
    .status-enviado { background: #d4edda; color: #155724; }
    .status-entregue { background: #c3e6cb; color: #155724; }
    .status-lido { background: #d1ecf1; color: #0c5460; }
    .status-falha { background: #f8d7da; color: #721c24; }
    .canal-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        color: #6c757d;
    }
    .message-preview {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 13px;
        color: #495057;
    }
    .date-info {
        font-size: 12px;
        color: #6c757d;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        color: #dee2e6;
    }
    .pagination {
        padding: 20px;
        display: flex;
        justify-content: center;
    }
    .pagination a, .pagination strong {
        padding: 8px 12px;
        margin: 0 4px;
        border-radius: 4px;
        text-decoration: none;
    }
    .pagination strong {
        background: #007bff;
        color: white;
    }
    .pagination a {
        background: #f8f9fa;
        color: #007bff;
    }
    .pagination a:hover {
        background: #e9ecef;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="bx bx-history"></i>
                </span>
                <h5>Histórico de Notificações</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('notificacoesConfig/configuracoes'); ?>" class="btn btn-mini">
                        <i class="bx bx-cog"></i> Configurações
                    </a>
                    <a href="<?php echo site_url('notificacoesConfig/templates'); ?>" class="btn btn-mini">
                        <i class="bx bx-message-square-dots"></i> Templates
                    </a>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $estatisticas['total_hoje']; ?></div>
                    <div class="stat-label">Enviadas Hoje</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $estatisticas['total_periodo']; ?></div>
                    <div class="stat-label">Últimos 30 dias</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $estatisticas['taxa_sucesso']; ?>%</div>
                    <div class="stat-label">Taxa de Sucesso</div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filter-section">
                <form method="get" action="<?php echo site_url('notificacoesConfig/logs'); ?>" class="filter-form">
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">Todos</option>
                            <option value="pendente" <?php echo $filtros['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                            <option value="enviando" <?php echo $filtros['status'] == 'enviando' ? 'selected' : ''; ?>>Enviando</option>
                            <option value="enviado" <?php echo $filtros['status'] == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                            <option value="entregue" <?php echo $filtros['status'] == 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                            <option value="lido" <?php echo $filtros['status'] == 'lido' ? 'selected' : ''; ?>>Lido</option>
                            <option value="falha" <?php echo $filtros['status'] == 'falha' ? 'selected' : ''; ?>>Falha</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Canal</label>
                        <select name="canal">
                            <option value="">Todos</option>
                            <option value="whatsapp" <?php echo $filtros['canal'] == 'whatsapp' ? 'selected' : ''; ?>>WhatsApp</option>
                            <option value="email" <?php echo $filtros['canal'] == 'email' ? 'selected' : ''; ?>>E-mail</option>
                            <option value="sms" <?php echo $filtros['canal'] == 'sms' ? 'selected' : ''; ?>>SMS</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Data Início</label>
                        <input type="date" name="data_inicio" value="<?php echo $filtros['data_inicio']; ?>">
                    </div>

                    <div class="filter-group">
                        <label>Data Fim</label>
                        <input type="date" name="data_fim" value="<?php echo $filtros['data_fim']; ?>">
                    </div>

                    <div class="filter-group">
                        <label>Busca</label>
                        <input type="text" name="busca" placeholder="Telefone, email..." value="<?php echo $filtros['busca']; ?>">
                    </div>

                    <div class="filter-group">
                        <button type="submit" class="btn-filter">
                            <i class="bx bx-search"></i> Filtrar
                        </button>
                        <a href="<?php echo site_url('notificacoesConfig/logs'); ?>" class="btn-clear">
                            <i class="bx bx-x"></i> Limpar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Lista de Logs -->
            <div class="widget-content">
                <?php if (empty($logs)): ?>
                    <div class="empty-state">
                        <i class="bx bx-inbox"></i>
                        <h4>Nenhuma notificação encontrada</h4>
                        <p>As notificações enviadas aparecerão aqui.</p>
                    </div>
                <?php else: ?>
                    <table class="logs-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Destinatário</th>
                                <th>Template</th>
                                <th>Mensagem</th>
                                <th>Canal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?
                                <tr>
                                    <td>#<?php echo $log->id; ?></td>
                                    <td>
                                        <div class="date-info">
                                            <?php echo date('d/m/Y', strtotime($log->created_at)); ?><br>
                                            <?php echo date('H:i:s', strtotime($log->created_at)); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($log->cliente_id): ?>
                                            <a href="<?php echo site_url('clientes/visualizar/' . $log->cliente_id); ?>">
                                                #<?php echo $log->cliente_id; ?>
                                            </a><br>
                                        <?php endif; ?>
                                        <?php if ($log->telefone): ?>
                                            <?php echo formatar_telefone($log->telefone); ?>
                                        <?php elseif ($log->email): ?
                                            <?php echo $log->email; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $log->template_chave; ?></td>
                                    <td>
                                        <div class="message-preview" title="<?php echo htmlspecialchars($log->mensagem_processada ?: $log->mensagem); ?>">
                                            <?php echo $log->mensagem_processada ?: $log->mensagem; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="canal-badge">
                                            <i class="bx <?php echo $log->canal == 'whatsapp' ? 'bxl-whatsapp' : ($log->canal == 'email' ? 'bx-envelope' : 'bx-message'); ?>"></i>
                                            <?php echo ucfirst($log->canal); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $log->status; ?>">
                                            <i class="bx <?php
                                                echo $log->status == 'enviado' ? 'bx-check' :
                                                    ($log->status == 'falha' ? 'bx-x' :
                                                        ($log->status == 'entregue' ? 'bx-check-double' :
                                                            ($log->status == 'lido' ? 'bx-check-double' : 'bx-time'))); ?>"></i>
                                            <?php echo ucfirst($log->status); ?>
                                        </span>
                                        <?php if ($log->error): ?>
                                            <br>
                                            <span style="font-size:11px;color:#dc3545;"><?php echo substr($log->error, 0, 50) . '...'; ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <?php echo $this->pagination->create_links(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
