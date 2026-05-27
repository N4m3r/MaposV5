<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class='bx bx-bar-chart-alt-2'></i> Estatisticas de Notificacoes</h5>
                <a href="<?= site_url('notificacoes/logs') ?>" class="btn btn-sm btn-secondary float-end"><i class='bx bx-history'></i> Ver Logs</a>
            </div>
            <div class="card-body">
                <?php $stats = $estatisticas ?? (object) ['total' => 0, 'enviados' => 0, 'falhas' => 0, 'pendentes' => 0, 'hoje' => 0, 'sucesso_percentual' => 0]; ?>
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card bg-primary text-white text-center">
                            <div class="card-body">
                                <h3><?= $stats->total ?></h3>
                                <small>Total (30d)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h3><?= $stats->enviados ?></h3>
                                <small>Enviados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-danger text-white text-center">
                            <div class="card-body">
                                <h3><?= $stats->falhas ?></h3>
                                <small>Falhas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning text-dark text-center">
                            <div class="card-body">
                                <h3><?= $stats->pendentes ?></h3>
                                <small>Pendentes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white text-center">
                            <div class="card-body">
                                <h3><?= $stats->hoje ?></h3>
                                <small>Hoje</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-dark text-white text-center">
                            <div class="card-body">
                                <h3><?= $stats->sucesso_percentual ?>%</h3>
                                <small>Taxa Sucesso</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>