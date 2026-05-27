<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class='bx bxl-whatsapp'></i> Configuracoes de Notificacoes WhatsApp</h5>
            </div>
            <div class="card-body">
                <?php if ($statusConexao): ?>
                <div class="alert alert-<?= $statusConexao['connected'] ? 'success' : 'warning' ?>">
                    <i class='bx bx-<?= $statusConexao['connected'] ? 'check-circle' : 'error-circle' ?>'></i>
                    Status da conexao: <strong><?= $statusConexao['message'] ?? $statusConexao['status'] ?></strong>
                </div>
                <?php endif; ?>

                <form method="post" action="<?= current_url() ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mt-3 mb-3"><i class='bx bx-cog'></i> Provedor</h6>
                            <div class="form-group">
                                <label>Provedor WhatsApp</label>
                                <select name="whatsapp_provedor" class="form-control">
                                    <option value="desativado" <?= ($config->whatsapp_provedor ?? '') === 'desativado' ? 'selected' : '' ?>>Desativado</option>
                                    <option value="evolution" <?= ($config->whatsapp_provedor ?? '') === 'evolution' ? 'selected' : '' ?>>Evolution API</option>
                                    <option value="zapi" <?= ($config->whatsapp_provedor ?? '') === 'zapi' ? 'selected' : '' ?>>Z-API</option>
                                    <option value="meta" <?= ($config->whatsapp_provedor ?? '') === 'meta' ? 'selected' : '' ?>>Meta (Facebook)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="whatsapp_ativo" value="1" <?= ($config->whatsapp_ativo ?? 0) ? 'checked' : '' ?>>
                                    WhatsApp Ativo
                                </label>
                            </div>

                            <h6 class="mt-4 mb-3"><i class='bx bx-server'></i> Evolution API</h6>
                            <div class="form-group">
                                <label>URL da API</label>
                                <input type="text" name="evolution_url" class="form-control" value="<?= $config->evolution_url ?? '' ?>" placeholder="https://seu-servidor.com:8080">
                            </div>
                            <div class="form-group">
                                <label>API Key</label>
                                <input type="password" name="evolution_apikey" class="form-control" value="<?= $config->evolution_apikey ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Instancia</label>
                                <input type="text" name="evolution_instance" class="form-control" value="<?= $config->evolution_instance ?? 'mapos' ?>">
                            </div>

                            <h6 class="mt-4 mb-3"><i class='bx bx-message'></i> Meta / Z-API</h6>
                            <div class="form-group">
                                <label>Meta Phone Number ID</label>
                                <input type="text" name="meta_phone_number_id" class="form-control" value="<?= $config->meta_phone_number_id ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Meta Access Token</label>
                                <input type="password" name="meta_access_token" class="form-control" value="<?= $config->meta_access_token ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Z-API URL</label>
                                <input type="text" name="z_api_url" class="form-control" value="<?= $config->z_api_url ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Z-API Token</label>
                                <input type="password" name="z_api_token" class="form-control" value="<?= $config->z_api_token ?? '' ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mt-3 mb-3"><i class='bx bx-bell'></i> Eventos de Notificacao</h6>
                            <?php
                            $eventos = [
                                'os_criada' => 'OS Criada',
                                'os_atualizada' => 'OS Atualizada',
                                'os_pronta' => 'OS Pronta (Finalizada)',
                                'os_orcamento' => 'Orcamento Disponivel',
                                'venda_realizada' => 'Venda Realizada',
                                'cobranca_gerada' => 'Cobranca Gerada',
                                'cobranca_vencimento' => 'Cobranca Vencendo',
                                'lembrete_aniversario' => 'Lembrete de Aniversario',
                            ];
                            foreach ($eventos as $campo => $label): ?>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="notificacao_<?= $campo ?>" value="1" <?= ($config->{'notificacao_' . $campo} ?? 0) ? 'checked' : '' ?>>
                                    <?= $label ?>
                                </label>
                            </div>
                            <?php endforeach; ?>

                            <h6 class="mt-4 mb-3"><i class='bx bx-time-five'></i> Horario de Envio</h6>
                            <div class="form-group">
                                <label>Inicio</label>
                                <input type="time" name="horario_envio_inicio" class="form-control" value="<?= $config->horario_envio_inicio ?? '08:00' ?>">
                            </div>
                            <div class="form-group">
                                <label>Fim</label>
                                <input type="time" name="horario_envio_fim" class="form-control" value="<?= $config->horario_envio_fim ?? '18:00' ?>">
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="enviar_fim_semana" value="1" <?= ($config->enviar_fim_semana ?? 0) ? 'checked' : '' ?>>
                                    Enviar fim de semana
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="respeitar_horario" value="1" <?= ($config->respeitar_horario ?? 0) ? 'checked' : '' ?>>
                                    Respeitar horario de envio
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Salvar Configuracoes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>