<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url() ?>">Dashboard</a><span class="divider">/</span></li>
            <li><a href="<?php echo site_url('certificado') ?>">Certificado Digital</a><span class="divider">/</span></li>
            <li class="active">NFS-e Importadas</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-receipt"></i></span>
                <h5>NFS-e Importadas</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('certificado/importar_nfse') ?>" class="btn btn-mini btn-success">
                        <i class="bx bx-upload"></i> Importar Nova
                    </a>
                </div>
            </div>

            <div class="widget-content nopadding">
                <?php if (empty($notas)): ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="bx bx-info-circle"></i> Nenhuma NFS-e importada.
                        <a href="<?php echo site_url('certificado/importar_nfse') ?>">Importar agora</a>.
                    </div>
                <?php else: ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Número</th>
                                <th>Prestador</th>
                                <th>Data Emissão</th>
                                <th class="text-right">Valor Total</th>
                                <th class="text-right">Impostos</th>
                                <th>Status</th>
                                <th>OS Vinculada</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notas as $nota): ?>
                                <tr>
                                    <td><?php echo $nota->id; ?></td>
                                    <td><?php echo $nota->numero_nota; ?></td>
                                    <td><?php echo $nota->prestador_nome ?? 'N/A'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($nota->data_emissao)); ?></td>
                                    <td class="text-right">
                                        R$ <?php echo number_format($nota->valor_total, 2, ',', '.'); ?>
                                    </td>
                                    <td class="text-right">
                                        R$ <?php echo number_format($nota->valor_impostos ?? 0, 2, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'label';
                                        switch ($nota->situacao) {
                                            case 'Normal':
                                            case 'Autorizada':
                                                $statusClass .= ' label-success';
                                                break;
                                            case 'Cancelada':
                                                $statusClass .= ' label-important';
                                                break;
                                            default:
                                                $statusClass .= ' label-info';
                                        }
                                        ?>
                                        <span class="<?php echo $statusClass; ?>"><?php echo $nota->situacao; ?></span>
                                    </td>
                                    <td>
                                        <?php if ($nota->os_id): ?>
                                            <a href="<?php echo site_url('os/visualizar/' . $nota->os_id); ?>" class="btn btn-mini btn-info">
                                                OS #<?php echo $nota->os_id; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Não vinculada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($nota->caminho_xml)): ?>
                                            <a href="<?php echo base_url($nota->caminho_xml); ?>" target="_blank" class="btn btn-mini btn-inverse" title="XML">
                                                <i class="bx bx-code"></i> XML
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="pagination" style="margin: 20px; text-align: center;">
                        <?php echo $this->pagination->create_links(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
