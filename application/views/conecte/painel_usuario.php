<?php
/**
 * View: Painel do Usuário do Portal do Cliente
 * Com suporte a permissões para cobranças, boletos, notas fiscais e obras
 */

// Carregar helper de permissões
$CI =& get_instance();
$CI->load->helper('cliente_permissions');

// Verificar permissões
$podeVerOS = clienteHasPermission('visualizar_os');
$podeVerCompras = clienteHasPermission('visualizar_compras');
$podeVerCobrancas = clienteHasPermission('visualizar_cobrancas');
$podeVerBoletos = clienteHasPermission('visualizar_boletos');
$podeVerNotasFiscais = clienteHasPermission('visualizar_notas_fiscais');
$podeVerObras = clienteHasPermission('visualizar_obras');
$podeVerFinanceiro = clienteHasPermission('visualizar_financeiro');
$podeEditarPerfil = clienteHasPermission('editar_perfil');
$podeSolicitarOrcamento = clienteHasPermission('solicitar_orcamento');

// Contadores
$totalCobrancas = count($cobrancas ?? []);
$totalBoletos = count($boletos ?? []);
$totalNotasFiscais = count($notasFiscais ?? []);
$totalObras = count($obras ?? []);

// Função helper para cores de status
if (!function_exists('getPainelUsuarioStatusColor')) {
    function getPainelUsuarioStatusColor($status) {
        $colors = [
            'Aberto' => '#e74c3c',
            'Orçamento' => '#f39c12',
            'Negociação' => '#9b59b6',
            'Aprovado' => '#3498db',
            'Em Andamento' => '#2ecc71',
            'Aguardando Peças' => '#e67e22',
            'Finalizado' => '#27ae60',
            'Faturado' => '#34495e',
            'Cancelado' => '#95a5a6'
        ];
        return $colors[$status] ?? '#95a5a6';
    }
}
?>

<style>
    /* Cards de Acesso Rápido */
    .quick-access-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .quick-card {
        background: white;
        border-radius: 12px;
        padding: 20px 15px;
        text-align: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
        display: block;
    }

    .quick-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .quick-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        text-decoration: none;
        color: inherit;
    }

    .quick-card:hover::before {
        transform: scaleX(1);
    }

    .quick-card .icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 1.5rem;
        color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: transform 0.3s ease;
    }

    .quick-card:hover .icon-wrapper {
        transform: scale(1.1);
    }

    .quick-card .title {
        font-weight: 600;
        font-size: 0.95rem;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .quick-card .subtitle {
        font-size: 0.75rem;
        color: #7f8c8d;
    }

    .quick-access-row {
        margin-bottom: 20px;
    }

    .quick-access-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #eee;
    }

    /* Status Badges Modernos */
    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Cards de estatísticas */
    .stat-card-modern {
        background: white;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .stat-card-modern .number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 5px;
        line-height: 1;
    }

    .stat-card-modern .label {
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    /* Widget Cards */
    .widget-card-modern {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .widget-header-modern {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafafa;
    }

    .widget-header-modern h5 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .widget-header-modern h5 i {
        color: #667eea;
    }

    /* Tabela Moderna */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }

    .modern-table thead th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 12px 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
    }

    .modern-table tbody tr {
        transition: background 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background: #f8f9fa;
    }

    .modern-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.85rem;
        color: #495057;
        vertical-align: middle;
    }

    .modern-table td:last-child {
        text-align: right;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .quick-access-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .quick-access-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-home"></i></span>
                <h5>Bem-vindo, <?= htmlspecialchars($usuario->nome) ?>!</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span8">
                        <p><strong>CNPJs vinculados:</strong></p>
                        <?php if (!empty($cnpjs)): ?>
                            <?php foreach ($cnpjs as $cnpj): ?>
                                <span class="label label-info" style="margin-right: 5px; display: inline-block; margin-bottom: 5px;">
                                    <i class="bx bx-buildings"></i> <?= $cnpj->cnpj ?>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="label">Nenhum CNPJ vinculado</span>
                        <?php endif; ?>
                    </div>
                    <div class="span4 text-right">
                        <?php if ($podeSolicitarOrcamento): ?>
                            <a href="<?= site_url('mine/adicionarOs') ?>" class="btn btn-small btn-success" style="margin-right: 5px;">
                                <i class="bx bx-plus"></i> Nova OS
                            </a>
                        <?php endif; ?>
                        <?php if ($podeEditarPerfil): ?>
                            <a href="<?= site_url('mine/conta') ?>" class="btn btn-small btn-info" style="margin-right: 5px;">
                                <i class="bx bx-user"></i> Perfil
                            </a>
                        <?php endif; ?>
                        <a href="<?= site_url('mine/sair_usuario') ?>" class="btn btn-small btn-danger">
                            <i class="bx bx-log-out"></i> Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Acesso Rápido -->
<div class="row-fluid">
    <div class="span12">
        <div class="quick-access-row">
            <div class="quick-access-title">
                <i class="bx bx-grid-alt"></i> Acesso Rápido
            </div>
            <div class="quick-access-grid">
                <?php if ($podeVerOS): ?>
                <a href="<?= site_url('mine/os') ?>" class="quick-card">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                        <i class='bx bx-file'></i>
                    </div>
                    <div class="title">Ordens de Serviço</div>
                    <div class="subtitle"><?= $stats['total'] ?? 0 ?> total</div>
                </a>
                <?php endif; ?>

                <?php if ($podeVerCobrancas): ?>
                <a href="<?= site_url('mine/cobrancas') ?>" class="quick-card">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%)">
                        <i class='bx bx-credit-card-front'></i>
                    </div>
                    <div class="title">Cobranças</div>
                    <div class="subtitle"><?= $totalCobrancas ?> cobrança(s)</div>
                </a>
                <?php endif; ?>

                <?php if ($podeVerObras): ?>
                <a href="<?= site_url('mine/obras') ?>" class="quick-card">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%)">
                        <i class='bx bx-building-house'></i>
                    </div>
                    <div class="title">Obras</div>
                    <div class="subtitle"><?= $totalObras ?> obra(s)</div>
                </a>
                <?php endif; ?>

                <?php if ($podeVerCompras): ?>
                <a href="<?= site_url('mine/compras') ?>" class="quick-card">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%)">
                        <i class='bx bx-cart-alt'></i>
                    </div>
                    <div class="title">Compras</div>
                    <div class="subtitle">Ver histórico</div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($podeVerOS): ?>
<!-- Estatísticas -->
<div class="row-fluid">
    <div class="span3">
        <div class="stat-card-modern">
            <div class="number"><?= $stats['total'] ?? 0 ?></div>
            <div class="label">Total de OS</div>
        </div>
    </div>
    <div class="span3">
        <div class="stat-card-modern">
            <div class="number" style="color: #e74c3c;"><?= ($stats['Aberto'] ?? 0) + ($stats['Orçamento'] ?? 0) ?></div>
            <div class="label">Em Aberto</div>
        </div>
    </div>
    <div class="span3">
        <div class="stat-card-modern">
            <div class="number" style="color: #3498db;"><?= $stats['Em Andamento'] ?? 0 ?></div>
            <div class="label">Em Andamento</div>
        </div>
    </div>
    <div class="span3">
        <div class="stat-card-modern">
            <div class="number" style="color: #27ae60;"><?= $stats['Finalizado'] ?? 0 ?></div>
            <div class="label">Finalizadas</div>
        </div>
    </div>
</div>

<!-- Ordens de Serviço Recentes -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-card-modern">
            <div class="widget-header-modern">
                <h5><i class='bx bx-file'></i> Ordens de Serviço Recentes</h5>
                <a href="<?= site_url('mine/os') ?>" class="btn btn-mini btn-info">
                    <i class="bx bx-list-ul"></i> Ver Todas
                </a>
            </div>
            <div class="widget-content" style="padding: 0">
                <?php if (!empty($os)): ?>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>OS #</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>CNPJ</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($os, 0, 10) as $o): ?>
                            <tr>
                                <td><strong><?= sprintf('%04d', $o->idOs) ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($o->dataInicial)) ?></td>
                                <td><?= htmlspecialchars($o->nomeCliente) ?></td>
                                <td><?= $o->documento ?></td>
                                <td>
                                    <span class="status-badge" style="background-color: <?= getPainelUsuarioStatusColor($o->status) ?>20; color: <?= getPainelUsuarioStatusColor($o->status) ?>">
                                        <?= $o->status ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('mine/visualizarOs/' . $o->idOs) ?>" class="btn btn-mini btn-info" title="Visualizar">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="bx bx-info-circle"></i> Nenhuma ordem de serviço encontrada para seus CNPJs vinculados.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($podeVerFinanceiro && ($totalCobrancas > 0 || $totalBoletos > 0)): ?>
<!-- Seção Financeira -->
<div class="row-fluid">
    <div class="span12">
        <div class="quick-access-row">
            <div class="quick-access-title">
                <i class="bx bx-money"></i> Resumo Financeiro
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <?php if ($podeVerCobrancas && $totalCobrancas > 0): ?>
    <div class="span6">
        <div class="widget-card-modern">
            <div class="widget-header-modern">
                <h5><i class='bx bx-credit-card-front'></i> Cobranças Recentes</h5>
                <a href="<?= site_url('mine/cobrancas') ?>" class="btn btn-mini btn-info">Ver Todas</a>
            </div>
            <div class="widget-content" style="padding: 0">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($cobrancas ?? [], 0, 5) as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c->descricao ?? 'Cobrança #' . $c->idCobranca) ?></td>
                            <td><?= isset($c->expire_at) ? date('d/m/Y', strtotime($c->expire_at)) : '-' ?></td>
                            <td>R$ <?= number_format($c->total ?: $c->valor, 2, ',', '.') ?></td>
                            <td>
                                <?php
                                $statusCobranca = $c->status ?? 'pendente';
                                $corCobranca = [
                                    'pago' => '#27ae60',
                                    'Pago' => '#27ae60',
                                    'PAGO' => '#27ae60',
                                    'pendente' => '#f39c12',
                                    'Pendente' => '#f39c12',
                                    'vencido' => '#e74c3c',
                                    'Vencido' => '#e74c3c',
                                    'cancelado' => '#95a5a6',
                                    'Cancelado' => '#95a5a6'
                                ][$statusCobranca] ?? '#7f8c8d';
                                ?>
                                <span class="status-badge" style="background-color: <?= $corCobranca ?>20; color: <?= $corCobranca ?>">
                                    <?= ucfirst($statusCobranca) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($podeVerBoletos && $totalBoletos > 0): ?>
    <div class="span6">
        <div class="widget-card-modern">
            <div class="widget-header-modern">
                <h5><i class='bx bx-barcode'></i> Boletos Recentes</h5>
                <a href="<?= site_url('mine/boletos') ?>" class="btn btn-mini btn-info">Ver Todos</a>
            </div>
            <div class="widget-content" style="padding: 0">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($boletos ?? [], 0, 5) as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b->descricao ?? 'Boleto #' . $b->id) ?></td>
                            <td><?= isset($b->data_vencimento) ? date('d/m/Y', strtotime($b->data_vencimento)) : (isset($b->expire_at) ? date('d/m/Y', strtotime($b->expire_at)) : '-') ?></td>
                            <td>R$ <?= number_format($b->valor ?: $b->total, 2, ',', '.') ?></td>
                            <td>
                                <?php
                                $statusBoleto = $b->status ?? 'pendente';
                                $corBoleto = [
                                    'pago' => '#27ae60',
                                    'Pago' => '#27ae60',
                                    'PAGO' => '#27ae60',
                                    'pendente' => '#f39c12',
                                    'Pendente' => '#f39c12',
                                    'vencido' => '#e74c3c',
                                    'Vencido' => '#e74c3c',
                                    'cancelado' => '#95a5a6',
                                    'Cancelado' => '#95a5a6'
                                ][$statusBoleto] ?? '#7f8c8d';
                                ?>
                                <span class="status-badge" style="background-color: <?= $corBoleto ?>20; color: <?= $corBoleto ?>">
                                    <?= ucfirst($statusBoleto) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($podeVerObras && $totalObras > 0): ?>
<!-- Seção de Obras -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-card-modern">
            <div class="widget-header-modern">
                <h5><i class='bx bx-building-house'></i> Obras</h5>
                <a href="<?= site_url('mine/obras') ?>" class="btn btn-mini btn-info">Ver Todas</a>
            </div>
            <div class="widget-content" style="padding: 0">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Obra</th>
                            <th>Endereço</th>
                            <th>Status</th>
                            <th>Progresso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($obras ?? [], 0, 5) as $obra): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($obra->nome ?? $obra->descricao ?? 'Obra #' . $obra->id) ?></strong></td>
                            <td><?= htmlspecialchars($obra->endereco ?? '-') ?></td>
                            <td>
                                <?php
                                $statusObra = $obra->status ?? 'Em Andamento';
                                $corObra = [
                                    'Em Andamento' => '#3498db',
                                    'Concluída' => '#27ae60',
                                    'Pausada' => '#f39c12',
                                    'Cancelada' => '#e74c3c',
                                    'Planejada' => '#9b59b6'
                                ][$statusObra] ?? '#7f8c8d';
                                ?>
                                <span class="status-badge" style="background-color: <?= $corObra ?>20; color: <?= $corObra ?>">
                                    <?= $statusObra ?>
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="margin: 0; height: 8px;">
                                    <div class="bar" style="width: <?= $obra->progresso ?? 0 ?>%; background-color: <?= $corObra ?>"></div>
                                </div>
                                <small style="color: #7f8c8d;"><?= $obra->progresso ?? 0 ?>%</small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($podeVerNotasFiscais && $totalNotasFiscais > 0): ?>
<!-- Seção de Notas Fiscais -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-card-modern">
            <div class="widget-header-modern">
                <h5><i class='bx bx-receipt'></i> Notas Fiscais de Serviço (NFS-e)</h5>
                <a href="<?= site_url('mine/notasfiscais') ?>" class="btn btn-mini btn-info">Ver Todas</a>
            </div>
            <div class="widget-content" style="padding: 0">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Data Emissão</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($notasFiscais ?? [], 0, 5) as $nf): ?>
                        <tr>
                            <td><strong><?= $nf->numero_nfse ?? $nf->numero ?? '-' ?></strong></td>
                            <td><?= isset($nf->data_emissao) ? date('d/m/Y', strtotime($nf->data_emissao)) : (isset($nf->created_at) ? date('d/m/Y', strtotime($nf->created_at)) : '-') ?></td>
                            <td>R$ <?= number_format($nf->valor_total ?? $nf->valor ?? 0, 2, ',', '.') ?></td>
                            <td>
                                <?php
                                $statusNf = $nf->status ?? 'Emitida';
                                $corNf = [
                                    'Emitida' => '#27ae60',
                                    'Cancelada' => '#e74c3c',
                                    'Pendente' => '#f39c12',
                                    'Processando' => '#3498db'
                                ][$statusNf] ?? '#7f8c8d';
                                ?>
                                <span class="status-badge" style="background-color: <?= $corNf ?>20; color: <?= $corNf ?>">
                                    <?= $statusNf ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($nf->link_pdf) || !empty($nf->pdf_url)): ?>
                                    <a href="<?= $nf->link_pdf ?? $nf->pdf_url ?>" target="_blank" class="btn btn-mini btn-success" title="Download PDF">
                                        <i class="bx bx-download"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
