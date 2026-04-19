<div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-barcode"></i>
        </span>
        <h5>Meus Boletos</h5>
    </div>
    <div class="widget-content nopadding tab-content">
        <table id="tabela" class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data de Vencimento</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (empty($results)) {
                        echo '<tr>
                                <td colspan="6" style="text-align: center;">Nenhum boleto encontrado</td>
                            </tr>';
                    }
                    foreach ($results as $r) {
                        $dataVencimento = isset($r->expire_at) ? date(('d/m/Y'), strtotime($r->expire_at)) : (isset($r->data_vencimento) ? date(('d/m/Y'), strtotime($r->data_vencimento)) : '-');
                        $status = $r->status ?? 'pendente';

                        $statusColors = [
                            'pago' => 'success',
                            'Pago' => 'success',
                            'PAGO' => 'success',
                            'pendente' => 'warning',
                            'Pendente' => 'warning',
                            'vencido' => 'important',
                            'Vencido' => 'important',
                            'cancelado' => 'inverse',
                            'Cancelado' => 'inverse'
                        ];
                        $statusLabel = $statusColors[$status] ?? 'default';

                        echo '<tr>';
                        echo '<td>' . $r->idCobranca . '</td>';
                        echo '<td>' . $dataVencimento . '</td>';
                        echo '<td>' . htmlspecialchars($r->descricao ?? 'Boleto #' . $r->idCobranca) . '</td>';
                        echo '<td><span class="label label-' . $statusLabel . '" style="padding: 4px 8px;">' . ucfirst($status) . '</span></td>';
                        echo '<td>R$ ' . number_format(($r->total ?? $r->valor) / 100, 2, ',', '.') . '</td>';
                        echo '<td>';
                        if (!empty($r->link) || !empty($r->link_boleto)) {
                            $link = $r->link ?? $r->link_boleto ?? '#';
                            echo '<a href="' . $link . '" target="_blank" class="btn btn-mini btn-success" title="Visualizar Boleto"><i class="bx bx-barcode"></i> Ver Boleto</a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>
