<div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-building-house"></i>
        </span>
        <h5>Minhas Obras</h5>
    </div>
    <div class="widget-content nopadding tab-content">
        <table id="tabela" class="table table-bordered">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Status</th>
                    <th>Progresso</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (empty($results)) {
                        echo '<tr>
                                <td colspan="6" style="text-align: center;">Nenhuma obra encontrada</td>
                            </tr>';
                    }
                    foreach ($results as $r) {
                        $status = $r->status ?? 'Em Andamento';

                        $statusColors = [
                            'Em Andamento' => 'info',
                            'Contratada' => 'warning',
                            'EmExecucao' => 'info',
                            'Concluída' => 'success',
                            'Concluida' => 'success',
                            'Paralisada' => 'important',
                            'Cancelada' => 'inverse',
                            'Prospeccao' => 'default'
                        ];
                        $statusLabel = $statusColors[$status] ?? 'default';
                        $progresso = $r->percentual_concluido ?? $r->progresso ?? 0;

                        echo '<tr>';
                        echo '<td><span class="label label-info">' . ($r->codigo ?? '#') . '</span></td>';
                        echo '<td><strong>' . htmlspecialchars($r->nome ?? 'Obra #' . $r->id) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($r->endereco ?? '-') . '</td>';
                        echo '<td><span class="label label-' . $statusLabel . '" style="padding: 4px 8px;">' . $status . '</span></td>';
                        echo '<td>';
                        echo '<div class="progress" style="margin: 0; height: 10px;"><div class="bar" style="width: ' . $progresso . '%;"></div></div>';
                        echo '<small>' . $progresso . '%</small>';
                        echo '</td>';
                        echo '<td>';
                        echo '<a href="' . site_url('mine/visualizarObra/' . $r->id) . '" class="btn btn-mini btn-info" title="Visualizar"><i class="bx bx-show"></i> Ver</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>
