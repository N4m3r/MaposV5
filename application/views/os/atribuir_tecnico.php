<?php if ($this->session->flashdata('success') != null) { ?>
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo $this->session->flashdata('success'); ?>
</div>
<?php } ?>

<?php if ($this->session->flashdata('error') != null) { ?>
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo $this->session->flashdata('error'); ?>
</div>
<?php } ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>

<style>
    /* === ESTILOS ADAPTÁVEIS AOS TEMAS === */

    /* Tema Claro (padrão) - Fundo claro, texto escuro */
    .page-atribuir .table td,
    .page-atribuir .table th {
        color: var(--title, #2c3e50) !important;
    }

    .page-atribuir .table td small {
        color: var(--cinza, #555) !important;
    }

    .page-atribuir .table td a {
        color: var(--dark-azul, #1086dd) !important;
        font-weight: 500;
    }

    .page-atribuir .table td a:hover {
        color: var(--dark-viol, #52459f) !important;
    }

    /* Inputs - Usam cor do tema */
    .filtro-box select,
    .filtro-box input[type="text"] {
        background-color: #fff;
        color: #333;
        border: 1px solid #ccc;
    }

    /* Labels dos filtros - SEMPRE ESCUROS para melhor legibilidade */
    .filtro-box label {
        color: #2c3e50 !important;
        font-weight: 600;
        font-size: 12px;
        margin-bottom: 5px;
        display: inline-block;
    }

    .filtro-box label i {
        color: #1086dd !important;
        margin-right: 4px;
    }

    /* Checkbox label */
    .filtro-box .checkbox,
    .filtro-box .checkbox-wrapper label {
        color: #2c3e50 !important;
        font-weight: 600;
    }

    /* Mensagem vazia */
    .page-atribuir .text-center i.bx-inbox {
        color: var(--cinza0, #9aa6b3) !important;
    }

    .page-atribuir .text-center {
        color: var(--cinza0, #666) !important;
    }

    /* Modal - Fundo branco, texto escuro */
    .modal-body {
        color: #333 !important;
        background-color: #fff !important;
    }

    .modal-header h5 {
        color: var(--title, #333) !important;
    }

    /* === TEMAS ESCUROS === */

    /* Dark Violet - Fundo escuro, texto claro */
    body[data-theme="dark-violet"] .page-atribuir .table td,
    body[data-theme="dark-violet"] .page-atribuir .table th {
        color: var(--dark-violet-tit, #c3b2e9) !important;
    }

    body[data-theme="dark-violet"] .page-atribuir .table td small {
        color: var(--branco, #caced8) !important;
    }

    body[data-theme="dark-violet"] .page-atribuir .table td a {
        color: var(--dark-violet-tit2, #c3b2e9) !important;
    }

    body[data-theme="dark-violet"] .page-atribuir .table td a:hover {
        color: var(--white, #fff) !important;
    }

    /* Filtros mantêm texto escuro em todos os temas */
    body[data-theme="dark-violet"] .filtro-box label,
    body[data-theme="dark-violet"] .filtro-box .checkbox,
    body[data-theme="dark-violet"] .filtro-box .checkbox-wrapper label {
        color: #2c3e50 !important;
    }

    body[data-theme="dark-violet"] .filtro-box label i {
        color: #1086dd !important;
    }

    body[data-theme="dark-violet"] .page-atribuir .text-center i.bx-inbox,
    body[data-theme="dark-violet"] .page-atribuir .text-center {
        color: var(--cinza0, #9aa6b3) !important;
    }

    /* Inputs nos temas escuros */
    body[data-theme="dark-violet"] .filtro-box select,
    body[data-theme="dark-violet"] .filtro-box input[type="text"] {
        background-color: var(--dark-violet-cont, #1b1239);
        color: var(--branco, #caced8);
        border-color: var(--dark-violet-side, #6b29f8);
    }

    body[data-theme="dark-violet"] .modal-body {
        background-color: var(--dark-violet-cont, #1b1239) !important;
        color: var(--branco, #caced8) !important;
    }

    body[data-theme="dark-violet"] .modal-header h5 {
        color: var(--dark-violet-tit, #c3b2e9) !important;
    }

    /* Pure Dark - Fundo escuro, texto claro */
    body[data-theme="pure-dark"] .page-atribuir .table td,
    body[data-theme="pure-dark"] .page-atribuir .table th {
        color: var(--branco, #caced8) !important;
    }

    body[data-theme="pure-dark"] .page-atribuir .table td small {
        color: var(--cinza0, #9aa6b3) !important;
    }

    body[data-theme="pure-dark"] .page-atribuir .table td a {
        color: var(--dark-azul, #1086dd) !important;
    }

    body[data-theme="pure-dark"] .page-atribuir .table td a:hover {
        color: var(--white, #fff) !important;
    }

    /* Filtros mantêm texto escuro em todos os temas */
    body[data-theme="pure-dark"] .filtro-box label,
    body[data-theme="pure-dark"] .filtro-box .checkbox,
    body[data-theme="pure-dark"] .filtro-box .checkbox-wrapper label {
        color: #2c3e50 !important;
    }

    body[data-theme="pure-dark"] .filtro-box label i {
        color: #1086dd !important;
    }

    body[data-theme="pure-dark"] .page-atribuir .text-center i.bx-inbox,
    body[data-theme="pure-dark"] .page-atribuir .text-center {
        color: var(--cinza0, #9aa6b3) !important;
    }

    body[data-theme="pure-dark"] .filtro-box select,
    body[data-theme="pure-dark"] .filtro-box input[type="text"] {
        background-color: var(--dark-1, #14141a);
        color: var(--branco, #caced8);
        border-color: var(--dark-2, #272835);
    }

    body[data-theme="pure-dark"] .modal-body {
        background-color: var(--dark-1, #14141a) !important;
        color: var(--branco, #caced8) !important;
    }

    body[data-theme="pure-dark"] .modal-header h5 {
        color: var(--branco, #caced8) !important;
    }

    /* === COMPONENTES ESPECÍFICOS === */

    /* Badges de técnico */
    .tecnico-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .tecnico-atribuido {
        background: linear-gradient(135deg, #00cd00, #02b470);
        color: #fff !important;
        box-shadow: 0 2px 4px rgba(0,205,0,0.3);
    }

    .tecnico-pendente {
        background: linear-gradient(135deg, #ffeeba, #ffc107);
        color: #856404 !important;
        border: 1px solid #ffc107;
    }

    /* Box de filtros - usa cor do tema */
    .filtro-box {
        background: var(--widget-box, #e6e9f3);
        border: 1px solid var(--cinza0, #dee2e6);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    /* Botões de filtro - texto PRETO */
    .filtro-box .button,
    .filtro-box .button .button__text2 {
        color: #000 !important;
    }

    .filtro-box .button:hover,
    .filtro-box .button:hover .button__text2 {
        color: #000 !important;
    }

    /* Filtros rápidos */
    .filtro-btn {
        margin-right: 8px;
        margin-bottom: 8px;
        border-radius: 20px;
        padding: 6px 15px;
        transition: all 0.3s ease;
        color: #333 !important;
        background: #f5f5f5;
        border: 1px solid #ddd;
    }

    .filtro-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        color: #333 !important;
    }

    .filtro-btn.active {
        background: linear-gradient(135deg, #1086dd, #0467fc);
        color: #fff !important;
        border-color: #1086dd;
        box-shadow: 0 4px 8px rgba(16,134,221,0.3);
    }

    /* Período - inputs lado a lado */
    .periodo-inputs {
        display: flex;
        gap: 5px;
    }

    .periodo-inputs input {
        flex: 1;
        width: calc(50% - 2.5px) !important;
    }

    /* Tabela */
    .table thead th {
        background: var(--widget-box, #e6e9f3);
        color: var(--title, #2c3e50) !important;
        font-weight: 600;
        border-bottom: 2px solid var(--cinza0, #dee2e6);
        padding: 12px 8px;
    }

    .table tbody tr:hover {
        background-color: rgba(16,134,221,0.05);
    }

    /* Status badge */
    .badge {
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    /* Botões de ação */
    .btn-acao {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin: 0 2px;
        transition: all 0.2s ease;
    }

    .btn-acao:hover {
        transform: scale(1.1);
    }

    /* Info paginação */
    .info-paginacao {
        text-align: center;
        font-size: 12px;
        margin-top: 10px;
        color: var(--cinza0, #6c757d);
    }

    /* Grid responsivo para filtros */
    .filtro-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
        align-items: end;
    }

    /* Checkbox customizado */
    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: rgba(16,134,221,0.1);
        border-radius: 6px;
        border: 1px solid rgba(16,134,221,0.2);
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin: 0;
    }

    /* Checkbox nos temas escuros */
    body[data-theme="dark-violet"] .checkbox-wrapper {
        background: rgba(107,41,248,0.15);
        border-color: rgba(107,41,248,0.3);
    }

    body[data-theme="pure-dark"] .checkbox-wrapper {
        background: rgba(16,134,221,0.1);
        border-color: rgba(16,134,221,0.2);
    }

    /* Dark themes adjustments - mantém cor do tema */
    body[data-theme="dark-violet"] .filtro-box {
        background: var(--dark-violet-widg, #291a57);
        border-color: var(--dark-violet-side, #6b29f8);
    }

    body[data-theme="pure-dark"] .filtro-box {
        background: var(--wid-dark, #1c1d26);
        border-color: var(--dark-2, #272835);
    }

    /* Cores dos labels e botões nos temas escuros */
    body[data-theme="dark-violet"] .filtro-box label,
    body[data-theme="dark-violet"] .filtro-box .checkbox,
    body[data-theme="dark-violet"] .filtro-box .checkbox-wrapper label {
        color: var(--branco, #caced8) !important;
    }

    body[data-theme="pure-dark"] .filtro-box label,
    body[data-theme="pure-dark"] .filtro-box .checkbox,
    body[data-theme="pure-dark"] .filtro-box .checkbox-wrapper label {
        color: var(--branco, #caced8) !important;
    }

    body[data-theme="dark-violet"] .filtro-box label i,
    body[data-theme="pure-dark"] .filtro-box label i {
        color: var(--dark-azul, #1086dd) !important;
    }

    /* Botões nos temas escuros */
    body[data-theme="dark-violet"] .filtro-box .button,
    body[data-theme="dark-violet"] .filtro-box .button .button__text2,
    body[data-theme="dark-violet"] .filtro-btn {
        color: var(--branco, #caced8) !important;
    }

    body[data-theme="pure-dark"] .filtro-box .button,
    body[data-theme="pure-dark"] .filtro-box .button .button__text2,
    body[data-theme="pure-dark"] .filtro-btn {
        color: var(--branco, #caced8) !important;
    }

    body[data-theme="dark-violet"] .filtro-btn.active,
    body[data-theme="pure-dark"] .filtro-btn.active {
        color: #fff !important;
    }

    body[data-theme="dark-violet"] .table thead th {
        background: var(--dark-violet-widg, #291a57);
        color: var(--dark-violet-tit2, #c3b2e9) !important;
        border-bottom-color: var(--dark-violet-side, #6b29f8);
    }

    body[data-theme="pure-dark"] .table thead th {
        background: var(--wid-dark, #1c1d26);
        color: var(--branco, #caced8) !important;
        border-bottom-color: var(--dark-2, #272835);
    }

    body[data-theme="dark-violet"] .checkbox-wrapper,
    body[data-theme="pure-dark"] .checkbox-wrapper {
        background: rgba(107,41,248,0.1);
        border-color: rgba(107,41,248,0.3);
    }

    /* Remover background-color das linhas ímpares da tabela */
    .table-striped tbody > tr:nth-child(odd) > td,
    .table-striped tbody > tr:nth-child(odd) > th {
        background-color: transparent !important;
    }
</style>

<div class="new122 page-atribuir">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-user-cog"></i>
        </span>
        <h5>Atribuir Técnico às OS</h5>
    </div>

    <!-- Filtros Aprimorados -->
    <div class="span12 filtro-box" style="margin-left: 0; margin-top: 15px;">
        <form method="get" action="<?php echo base_url(); ?>index.php/os/atribuir" id="formFiltros">
            <div class="row-fluid">
                <!-- Linha 1: Busca e Filtros principais -->
                <div class="span4">
                    <label><i class='fas fa-search'></i> Buscar OS</label>
                    <input type="text" name="busca_global" id="busca_global" placeholder="N° OS, cliente, descrição, defeito, observações, telefone..." class="span12" value="<?= $this->input->get('busca_global') ?>">
                </div>

                <div class="span2">
                    <label><i class='fas fa-flag'></i> Status</label>
                    <select name="status" id="status" class="span12">
                        <option value="">Todos</option>
                        <option value="Aberto" <?= $this->input->get('status') == 'Aberto' ? 'selected' : '' ?>>Aberto</option>
                        <option value="Orçamento" <?= $this->input->get('status') == 'Orçamento' ? 'selected' : '' ?>>Orçamento</option>
                        <option value="Negociação" <?= $this->input->get('status') == 'Negociação' ? 'selected' : '' ?>>Negociação</option>
                        <option value="Em Andamento" <?= $this->input->get('status') == 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                        <option value="Aguardando Peças" <?= $this->input->get('status') == 'Aguardando Peças' ? 'selected' : '' ?>>Aguardando Peças</option>
                        <option value="Aprovado" <?= $this->input->get('status') == 'Aprovado' ? 'selected' : '' ?>>Aprovado</option>
                        <option value="Finalizado" <?= $this->input->get('status') == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                        <option value="Faturado" <?= $this->input->get('status') == 'Faturado' ? 'selected' : '' ?>>Faturado</option>
                        <option value="Cancelado" <?= $this->input->get('status') == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>

                <div class="span2">
                    <label><i class='fas fa-user'></i> Técnico</label>
                    <select name="tecnico" id="tecnico" class="span12">
                        <option value="">Todos</option>
                        <option value="sem_tecnico" <?= $this->input->get('tecnico') == 'sem_tecnico' ? 'selected' : '' ?>>⚠ Sem Técnico</option>
                        <?php if (!empty($tecnicos) && is_array($tecnicos)): ?>
                            <?php foreach ($tecnicos as $t): ?>
                                <?php if (is_object($t)): ?>
                                    <option value="<?php echo $t->idUsuarios; ?>" <?= $this->input->get('tecnico') == $t->idUsuarios ? 'selected' : '' ?>>
                                        <?php echo $t->nome; ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="span2">
                    <label><i class='fas fa-calendar'></i> Período</label>
                    <div class="periodo-inputs">
                        <input type="text" name="data" autocomplete="off" id="data" placeholder="Início" class="datepicker" value="<?= $this->input->get('data') ?>">
                        <input type="text" name="data2" autocomplete="off" id="data2" placeholder="Fim" class="datepicker" value="<?= $this->input->get('data2') ?>">
                    </div>
                </div>

                <div class="span2">
                    <label>&nbsp;</label>
                    <div style="display: flex; gap: 5px;">
                        <button type="submit" class="button btn btn-mini btn-success" style="flex: 1;">
                            <span class="button__icon"><i class='fas fa-search'></i></span>
                            <span class="button__text2">Filtrar</span>
                        </button>

                        <a href="<?php echo base_url(); ?>index.php/os/atribuir" class="button btn btn-mini" style="flex: 1;">
                            <span class="button__icon"><i class='fas fa-undo'></i></span>
                            <span class="button__text2">Limpar</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Linha 2: Opções adicionais -->
            <div class="row-fluid" style="margin-top: 10px;">
                <div class="span12">
                    <div class="checkbox-wrapper" style="display: inline-block;">
                        <input type="checkbox" name="mostrar_finalizados" id="mostrar_finalizados" value="1" <?= $this->input->get('mostrar_finalizados') ? 'checked' : '' ?>>
                        <label for="mostrar_finalizados" style="margin: 0; cursor: pointer;">Mostrar OS Finalizadas/Canceladas/Faturadas</label>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabela de OS -->
    <div class="widget-box" style="margin-top: 8px">
        <div class="widget-content nopadding">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 50px;">N°</th>
                            <th>Cliente</th>
                            <th>Descrição</th>
                            <th style="width: 90px;">Data</th>
                            <th style="width: 110px;">Status</th>
                            <th style="width: 140px;">Técnico</th>
                            <th style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!isset($ordens) || !is_array($ordens)) {
                            $ordens = [];
                        }
                        ?>
                        <?php if (empty($ordens)): ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 40px;">
                                    <i class='fas fa-inbox' style="font-size: 3em; display: block; margin-bottom: 15px; opacity: 0.5;"></i>
                                    <span style="font-size: 14px;">Nenhuma OS encontrada com os filtros selecionados</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ordens as $os): ?>
                                <?php
                                $cor = '#E0E4CC';
                                switch ($os->status) {
                                    case 'Aberto':
                                        $cor = '#00cd00';
                                        break;
                                    case 'Em Andamento':
                                        $cor = '#436eee';
                                        break;
                                    case 'Orçamento':
                                        $cor = '#CDB380';
                                        break;
                                    case 'Negociação':
                                        $cor = '#AEB404';
                                        break;
                                    case 'Cancelado':
                                        $cor = '#CD0000';
                                        break;
                                    case 'Finalizado':
                                        $cor = '#256';
                                        break;
                                    case 'Faturado':
                                        $cor = '#B266FF';
                                        break;
                                    case 'Aguardando Peças':
                                        $cor = '#FF7F00';
                                        break;
                                    case 'Aprovado':
                                        $cor = '#808080';
                                        break;
                                }
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <strong>#<?php echo $os->idOs; ?></strong>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url(); ?>index.php/clientes/visualizar/<?php echo $os->clientes_id; ?>" title="Ver cliente">
                                            <?php echo $os->nomeCliente; ?>
                                        </a><br>
                                        <small><i class='fas fa-phone' style="font-size: 10px;"></i> <?php echo $os->telefone ?: 'N/A'; ?></small>
                                    </td>
                                    <td>
                                        <?php echo character_limiter(strip_tags($os->descricaoProduto), 45); ?>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($os->dataInicial)); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: <?php echo $cor; ?>; border-color: <?php echo $cor; ?>; color: #fff;">
                                            <?php echo $os->status; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($os->tecnico_responsavel): ?>
                                            <span class="tecnico-badge tecnico-atribuido">
                                                <i class='fas fa-user'></i> <?php echo $os->nome_tecnico; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="tecnico-badge tecnico-pendente">
                                                <i class='fas fa-user-times'></i> Pendente
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo base_url(); ?>index.php/os/visualizar/<?php echo $os->idOs; ?>"
                                           class="btn-nwe btn-acao" title="Visualizar OS">
                                            <i class='fas fa-eye'></i>
                                        </a>
                                        <button class="btn-nwe3 btn-acao btn-atribuir"
                                                data-os="<?php echo $os->idOs; ?>"
                                                data-cliente="<?php echo htmlspecialchars($os->nomeCliente); ?>"
                                                data-tecnico-atual="<?php echo $os->tecnico_responsavel; ?>"
                                                data-tecnico-nome="<?php echo htmlspecialchars($os->nome_tecnico ?? ''); ?>"
                                                title="<?php echo $os->tecnico_responsavel ? 'Trocar Técnico' : 'Atribuir Técnico'; ?>">
                                            <i class='<?php echo $os->tecnico_responsavel ? 'fas fa-exchange-alt' : 'fas fa-user-plus'; ?>'></i>
                                        </button>
                                        <?php if ($os->tecnico_responsavel): ?>
                                            <button class="btn-nwe4 btn-acao btn-remover"
                                                    data-os="<?php echo $os->idOs; ?>"
                                                    data-cliente="<?php echo htmlspecialchars($os->nomeCliente); ?>"
                                                    title="Remover Técnico">
                                                <i class='fas fa-user-times'></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    <?php if (isset($pagination) && $pagination): ?>
        <div class="pagination" style="text-align: center; margin-top: 15px; margin-bottom: 20px;">
            <?php echo $pagination; ?>
        </div>
    <?php endif; ?>

    <!-- Info de paginação -->
    <?php if (!empty($ordens)): ?>
        <div class="info-paginacao">
            <i class='fas fa-info-circle'></i>
            Mostrando <?php echo count($ordens); ?> OS por página
            <?php if ($this->input->get('pesquisa') || $this->input->get('status') || $this->input->get('tecnico')): ?>
                | Filtros ativos
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Atribuir Técnico -->
<div id="modalAtribuir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalAtribuirLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h5 id="modalAtribuirLabel">
            <i class='fas fa-user-plus'></i> Atribuir Técnico
        </h5>
    </div>
    <form action="<?php echo base_url(); ?>index.php/os/atribuirTecnicoAction" method="POST" id="formAtribuir">
        <div class="modal-body">
            <input type="hidden" name="os_id" id="os_id_atribuir">

            <div class="control-group">
                <label class="control-label" style="font-weight: 600;">
                    OS #<span id="os_numero"></span> - <span id="os_cliente"></span>
                </label>
            </div>

            <div class="control-group">
                <label class="control-label" for="tecnico_id">Técnico Responsável:</label>
                <div class="controls">
                    <select name="tecnico_id" id="tecnico_id" class="span12" required>
                        <option value="">Selecione um técnico...</option>
                        <?php if (!empty($tecnicos) && is_array($tecnicos)): ?>
                            <?php foreach ($tecnicos as $t): ?>
                                <?php if (is_object($t)): ?>
                                    <option value="<?php echo $t->idUsuarios; ?>">
                                        <?php echo $t->nome; ?> (<?php echo $t->email; ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Nenhum técnico disponível</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="observacao">Observação:</label>
                <div class="controls">
                    <textarea name="observacao" id="observacao" class="span12" rows="3" placeholder="Motivo da atribuição (opcional)"></textarea>
                </div>
            </div>

            <div id="tecnico-atual-info" class="alert alert-info hide" style="margin-top: 10px;">
                <strong><i class='fas fa-info-circle'></i> Técnico atual:</strong> <span id="tecnico-atual-nome"></span><br>
                <small>Ao atribuir um novo técnico, o atual será substituído.</small>
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="fas fa-times"></i></span>
                <span class="button__text2">Cancelar</span>
            </button>
            <button type="submit" class="button btn btn-success">
                <span class="button__icon"><i class='fas fa-check'></i></span>
                <span class="button__text2">Confirmar</span>
            </button>
        </div>
    </form>
</div>

<!-- Modal Remover Técnico -->
<div id="modalRemover" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalRemoverLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h5 id="modalRemoverLabel">
            <i class='fas fa-user-times'></i> Remover Técnico
        </h5>
    </div>
    <form action="<?php echo base_url(); ?>index.php/os/removerTecnicoAction" method="POST" id="formRemover">
        <div class="modal-body">
            <input type="hidden" name="os_id" id="os_id_remover">

            <div class="alert alert-warning">
                <p><i class='fas fa-exclamation-circle'></i> Tem certeza que deseja remover o técnico da OS #<strong id="os_numero_remover"></strong>?</p>
                <p>Cliente: <strong id="os_cliente_remover"></strong></p>
            </div>

            <div class="control-group">
                <label class="control-label" for="motivo">Motivo da remoção:</label>
                <div class="controls">
                    <textarea name="motivo" id="motivo" class="span12" rows="3" placeholder="Informe o motivo (opcional)"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="fas fa-times"></i></span>
                <span class="button__text2">Cancelar</span>
            </button>
            <button type="submit" class="button btn btn-danger">
                <span class="button__icon"><i class='fas fa-trash'></i></span>
                <span class="button__text2">Confirmar</span>
            </button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Botão Atribuir/Trocar Técnico
        $('.btn-atribuir').click(function() {
            var osId = $(this).data('os');
            var cliente = $(this).data('cliente');
            var tecnicoAtual = $(this).data('tecnico-atual');
            var tecnicoNome = $(this).data('tecnico-nome');

            $('#os_id_atribuir').val(osId);
            $('#os_numero').text(osId);
            $('#os_cliente').text(cliente);

            if (tecnicoAtual) {
                $('#tecnico-atual-nome').text(tecnicoNome);
                $('#tecnico-atual-info').removeClass('hide');
                $('#modalAtribuirLabel').html('<i class="fas fa-exchange-alt"></i> Trocar Técnico');
            } else {
                $('#tecnico-atual-info').addClass('hide');
                $('#modalAtribuirLabel').html('<i class="fas fa-user-plus"></i> Atribuir Técnico');
            }

            $('#modalAtribuir').modal('show');
        });

        // Botão Remover Técnico
        $('.btn-remover').click(function() {
            var osId = $(this).data('os');
            var cliente = $(this).data('cliente');

            $('#os_id_remover').val(osId);
            $('#os_numero_remover').text(osId);
            $('#os_cliente_remover').text(cliente);

            $('#modalRemover').modal('show');
        });

        // Validação do formulário de atribuição
        $('#formAtribuir').submit(function(e) {
            if (!$('#tecnico_id').val()) {
                e.preventDefault();
                Swal.fire({
                    type: 'warning',
                    title: 'Atenção',
                    text: 'Selecione um técnico para atribuir.'
                });
                return false;
            }
        });

        // Datepicker
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });

        // Atualizar select de técnico quando mudar o filtro rápido
        $('a[href*="filtro="]').click(function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var filtro = href.split('filtro=')[1];

            if (filtro === 'sem_tecnico') {
                $('#tecnico').val('sem_tecnico');
            } else if (filtro === 'com_tecnico') {
                $('#tecnico').val('');
            }

            $('#formFiltros').submit();
        });
    });
</script>
