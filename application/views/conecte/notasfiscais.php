<div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-receipt"></i>
        </span>
        <h5>Minhas Notas Fiscais de Serviço (NFS-e)</h5>
    </div>
    <div class="widget-content nopadding tab-content">
        <table id="tabela" class="table table-bordered">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Data de Emissão</th>
                    <th>OS #</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (empty($results)) {
                        echo '<tr>
                                <td colspan="6" style="text-align: center;">Nenhuma nota fiscal encontrada</td>
                            </tr>';
                    }
                    foreach ($results as $r) {
                        $dataEmissao = isset($r->data_emissao) ? date(('d/m/Y'), strtotime($r->data_emissao)) : (isset($r->created_at) ? date(('d/m/Y'), strtotime($r->created_at)) : '-');
                        $status = $r->situacao ?? $r->status ?? 'Pendente';

                        $statusColors = [
                            'Emitida' => 'success',
                            'Pendente' => 'warning',
                            'Cancelada' => 'important',
                            'Processando' => 'info'
                        ];
                        $statusLabel = $statusColors[$status] ?? 'default';

                        echo '<tr>';
                        echo '<td><strong>' . ($r->numero_nfse ?? $r->numero ?? '-') . '</strong></td>';
                        echo '<td>' . $dataEmissao . '</td>';
                        echo '<td>#' . ($r->os_id ?? '-') . '</td>';
                        echo '<td>R$ ' . number_format($r->valor_servicos ?? $r->valor ?? 0, 2, ',', '.') . '</td>';
                        echo '<td><span class="label label-' . $statusLabel . '" style="padding: 4px 8px;">' . $status . '</span></td>';
                        echo '<td>';
                        if (!empty($r->link_impressao) || !empty($r->pdf_url) || !empty($r->link_pdf)) {
                            $link = $r->link_impressao ?? $r->pdf_url ?? $r->link_pdf;
                            echo '<a href="' . $link . '" target="_blank" class="btn btn-mini btn-success" title="Download PDF"><i class="bx bx-download"></i> PDF</a>';
                        }
                        echo '<a href="' . site_url('mine/visualizarOs/' . ($r->os_id ?? 0)) . '" class="btn btn-mini btn-info" style="margin-left: 5px;" title="Ver OS"><i class="bx bx-show"></i> OS</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>
