<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-hard-hat"></i>
        </span>
        <h5>Gerenciar Técnicos</h5>
    </div>

    <!-- Estatísticas Cards -->
    <div class="row-fluid" style="margin: 15px 0 20px;">
        <div class="span3">
            <div class="card-stats bg-primary">
                <div class="card-stats-icon">
                    <i class="bx bx-group"></i>
                </div>
                <div class="card-stats-info">
                    <span class="card-stats-number"><?= count($tecnicos ?? []) ?></span>
                    <span class="card-stats-label">Total de Técnicos</span>
                </div>
            </div>
        </div>
        <div class="span3">
            <div class="card-stats bg-success">
                <div class="card-stats-icon">
                    <i class="bx bx-check-circle"></i>
                </div>
                <div class="card-stats-info">
                    <span class="card-stats-number" id="countAtivos">-</span>
                    <span class="card-stats-label">Ativos</span>
                </div>
            </div>
        </div>
        <div class="span3">
            <div class="card-stats bg-warning">
                <div class="card-stats-icon">
                    <i class="bx bx-car"></i>
                </div>
                <div class="card-stats-info">
                    <span class="card-stats-number" id="countComVeiculo">-</span>
                    <span class="card-stats-label">Com Veículo</span>
                </div>
            </div>
        </div>
        <div class="span3">
            <div class="card-stats bg-info">
                <div class="card-stats-icon">
                    <i class="bx bx-star"></i>
                </div>
                <div class="card-stats-info">
                    <span class="card-stats-number" id="countNivelIII">-</span>
                    <span class="card-stats-label">Nível III</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Ações -->
    <div class="row-fluid" style="margin-bottom: 15px;">
        <div class="span12" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
                <a href="<?= site_url('tecnicos_admin/adicionar_tecnico') ?>" class="button btn btn-success">
                    <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                    <span class="button__text2">Novo Técnico</span>
                </a>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <div class="input-append" style="margin-bottom: 0;">
                    <input type="text" id="searchTecnicos" placeholder="Buscar técnico por nome, email..."
                           class="span12" style="min-width: 280px; height: 32px; border-radius: 4px 0 0 4px;">
                    <span class="add-on" style="height: 32px; line-height: 32px; padding: 0 12px;">
                        <i class="bx bx-search"></i>
                    </span>
                </div>
                <select id="filterStatus" class="span12" style="width: 120px; margin-bottom: 0;">
                    <option value="">Todos</option>
                    <option value="ativo">Ativos</option>
                    <option value="inativo">Inativos</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabela de Técnicos -->
    <div class="widget-box" style="border-radius: 8px; overflow: hidden;">
        <div class="widget-content nopadding">
            <?php if (isset($tecnicos) && !empty($tecnicos)): ?>
                <table class="table table-bordered table-hover" id="tabelaTecnicos">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <th style="width: 50px; text-align: center;">ID</th>
                            <th style="min-width: 200px;">
                                <i class="bx bx-user" style="margin-right: 5px;"></i>Técnico
                            </th>
                            <th style="width: 100px; text-align: center;">
                                <i class="bx bx-certification" style="margin-right: 5px;"></i>Nível
                            </th>
                            <th style="min-width: 180px;">
                                <i class="bx bx-car" style="margin-right: 5px;"></i>Veículo
                            </th>
                            <th style="width: 100px; text-align: center;">
                                <i class="bx bx-toggle-right" style="margin-right: 5px;"></i>Status
                            </th>
                            <th style="width: 160px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tecnicos as $tecnico):
                            $id = $tecnico->idUsuarios ?? $tecnico->id;
                            $ativo = $tecnico->ativo ?? 1;
                            $nivel = $tecnico->nivel_tecnico ?? 'II';
                            $veiculoTipo = $tecnico->veiculo_tipo ?? '';
                            $veiculoPlaca = $tecnico->veiculo_placa ?? '';

                            // Cores do nível
                            $nivelColors = [
                                'I' => ['bg' => '#e3f2fd', 'text' => '#1976d2', 'icon' => 'bx-star'],
                                'II' => ['bg' => '#fff3e0', 'text' => '#f57c00', 'icon' => 'bxs-star'],
                                'III' => ['bg' => '#fce4ec', 'text' => '#c2185b', 'icon' => 'bxs-star-half'],
                                'Master' => ['bg' => '#f3e5f5', 'text' => '#7b1fa2', 'icon' => 'bxs-crown']
                            ];
                            $nivelStyle = $nivelColors[$nivel] ?? $nivelColors['II'];
                        ?>
                            <tr class="tecnico-row" data-status="<?= $ativo ? 'ativo' : 'inativo' ?>" data-nivel="<?= $nivel ?>">
                                <td style="text-align: center; vertical-align: middle; font-weight: 600; color: #666;">
                                    #<?= $id ?>
                                </td>
                                <td style="vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="user-avatar" style="
                                            width: 42px;
                                            height: 42px;
                                            border-radius: 50%;
                                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            color: white;
                                            font-weight: 600;
                                            font-size: 16px;
                                            flex-shrink: 0;
                                        ">
                                            <?= strtoupper(substr($tecnico->nome, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #333; font-size: 14px;">
                                                <?= htmlspecialchars($tecnico->nome) ?>
                                            </div>
                                            <div style="font-size: 12px; color: #888; display: flex; align-items: center; gap: 4px;">
                                                <i class="bx bx-envelope" style="font-size: 11px;"></i>
                                                <?= htmlspecialchars($tecnico->email) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <span class="badge-nivel" style="
                                        background: <?= $nivelStyle['bg'] ?>;
                                        color: <?= $nivelStyle['text'] ?>;
                                        padding: 6px 14px;
                                        border-radius: 20px;
                                        font-size: 12px;
                                        font-weight: 600;
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 4px;
                                    ">
                                        <i class="bx <?= $nivelStyle['icon'] ?>"></i>
                                        Nível <?= $nivel ?>
                                    </span>
                                </td>
                                <td style="vertical-align: middle;">
                                    <?php if ($veiculoTipo): ?>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="bx bx-car" style="font-size: 18px; color: #667eea;"></i>
                                            <div>
                                                <div style="font-weight: 500; color: #333; font-size: 13px;">
                                                    <?= htmlspecialchars($veiculoTipo) ?>
                                                </div>
                                                <div style="font-size: 12px; color: #888; font-family: monospace; letter-spacing: 1px;">
                                                    <?= htmlspecialchars($veiculoPlaca ?: 'Sem placa') ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #aaa; font-style: italic; font-size: 13px;">
                                            <i class="bx bx-walk" style="margin-right: 4px;"></i>Sem veículo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <span class="badge-status <?= $ativo ? 'ativo' : 'inativo' ?>" style="
                                        padding: 6px 12px;
                                        border-radius: 20px;
                                        font-size: 12px;
                                        font-weight: 600;
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 4px;
                                    ">
                                        <i class="bx <?= $ativo ? 'bx-check-circle' : 'bx-x-circle' ?>"></i>
                                        <?= $ativo ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <div style="display: flex; gap: 5px; justify-content: center;">
                                        <a href="<?= site_url('tecnicos_admin/ver_tecnico/' . $id) ?>"
                                           class="btn-action btn-view" title="Ver detalhes">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="<?= site_url('tecnicos_admin/editar_tecnico/' . $id) ?>"
                                           class="btn-action btn-edit" title="Editar">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <a href="<?= site_url('tecnicos_admin/estoque_tecnico/' . $id) ?>"
                                           class="btn-action btn-stock" title="Gerenciar Estoque">
                                            <i class="bx bx-package"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Empty State -->
                <div id="emptyState" style="display: none; padding: 40px; text-align: center;">
                    <div style="font-size: 48px; color: #ddd; margin-bottom: 15px;">
                        <i class="bx bx-search-alt"></i>
                    </div>
                    <h4 style="color: #666; font-weight: 500;">Nenhum técnico encontrado</h4>
                    <p style="color: #999;">Tente ajustar os filtros ou adicione um novo técnico.</p>
                </div>
            <?php else: ?>
                <div style="padding: 60px; text-align: center;">
                    <div style="font-size: 64px; color: #e0e0e0; margin-bottom: 20px;">
                        <i class="bx bx-hard-hat"></i>
                    </div>
                    <h3 style="color: #666; font-weight: 400; margin-bottom: 10px;">Nenhum técnico cadastrado</h3>
                    <p style="color: #999; margin-bottom: 25px;">Comece adicionando o primeiro técnico ao sistema.</p>
                    <a href="<?= site_url('tecnicos_admin/adicionar_tecnico') ?>" class="button btn btn-success btn-large">
                        <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                        <span class="button__text2">Adicionar Técnico</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Card Stats */
.card-stats {
    border-radius: 12px;
    padding: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}
.card-stats:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
.card-stats.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.card-stats.bg-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.card-stats.bg-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.card-stats.bg-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

.card-stats-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.card-stats-info {
    display: flex;
    flex-direction: column;
}

.card-stats-number {
    font-size: 28px;
    font-weight: 700;
    line-height: 1;
}

.card-stats-label {
    font-size: 13px;
    opacity: 0.9;
    margin-top: 4px;
}

/* Action Buttons */
.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
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
}

.btn-edit {
    background: #fff3e0;
    color: #f57c00;
}
.btn-edit:hover {
    background: #f57c00;
    color: white;
}

.btn-stock {
    background: #e8f5e9;
    color: #388e3c;
}
.btn-stock:hover {
    background: #388e3c;
    color: white;
}

/* Badge Status */
.badge-status {
    transition: all 0.2s;
}
.badge-status.ativo {
    background: #e8f5e9 !important;
    color: #2e7d32 !important;
}
.badge-status.inativo {
    background: #ffebee !important;
    color: #c62828 !important;
}

/* Table Hover */
#tabelaTecnicos tbody tr {
    transition: background-color 0.15s;
}
#tabelaTecnicos tbody tr:hover {
    background-color: #f8f9ff !important;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.tecnico-row {
    animation: fadeIn 0.3s ease forwards;
}
</style>

<script>
$(document).ready(function() {
    // Calcular estatísticas
    let ativos = 0, comVeiculo = 0, nivelIII = 0;
    $('.tecnico-row').each(function() {
        const status = $(this).data('status');
        const nivel = $(this).data('nivel');
        const veiculo = $(this).find('td:eq(3)').text().trim();

        if (status === 'ativo') ativos++;
        if (nivel === 'III' || nivel === 'Master') nivelIII++;
        if (veiculo && !veiculo.includes('Sem veículo')) comVeiculo++;
    });

    $('#countAtivos').text(ativos);
    $('#countComVeiculo').text(comVeiculo);
    $('#countNivelIII').text(nivelIII);

    // Busca em tempo real
    $('#searchTecnicos').on('keyup', function() {
        const termo = $(this).val().toLowerCase();
        filterTable(termo, $('#filterStatus').val());
    });

    // Filtro de status
    $('#filterStatus').on('change', function() {
        filterTable($('#searchTecnicos').val().toLowerCase(), $(this).val());
    });

    function filterTable(termo, status) {
        let visiveis = 0;

        $('.tecnico-row').each(function() {
            const rowStatus = $(this).data('status');
            const texto = $(this).text().toLowerCase();

            const matchTermo = texto.includes(termo);
            const matchStatus = !status || rowStatus === status;

            if (matchTermo && matchStatus) {
                $(this).show();
                visiveis++;
            } else {
                $(this).hide();
            }
        });

        // Mostrar/esconder empty state
        if (visiveis === 0) {
            $('#emptyState').show();
        } else {
            $('#emptyState').hide();
        }
    }
});
</script>
