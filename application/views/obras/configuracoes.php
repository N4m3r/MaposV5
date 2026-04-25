<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid" style="padding:20px;">
    <div class="span12">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius:12px; padding:24px; color:white; margin-bottom:24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div>
                    <h2 style="margin:0; font-size:24px;"><i class="bx bx-cog"></i> Configuracoes do Sistema de Obras</h2>
                    <p style="margin:6px 0 0 0; opacity:0.9;">Gerencie tipos, status, especialidades, funcoes e preferencias</p>
                </div>
                <a href="<?php echo site_url('obras'); ?>" class="btn btn-inverse" style="background:rgba(255,255,255,0.2); border:none; color:white;">
                    <i class="bx bx-arrow-back"></i> Voltar as Obras
                </a>
            </div>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <i class="bx bx-check-circle"></i> <?php echo htmlspecialchars($this->session->flashdata('success')); ?>
        </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-error">
            <i class="bx bx-error-circle"></i> <?php echo htmlspecialchars($this->session->flashdata('error')); ?>
        </div>
        <?php endif; ?>

        <!-- BOTOES RAPIDOS -->
        <div style="text-align:center; margin-bottom:24px;">
            <button type="button" class="btn btn-large btn-primary" onclick="abrirPainel()">
                <i class="bx bx-cog" style="font-size:18px; vertical-align:middle; margin-right:6px;"></i>
                Abrir Painel de Configuracoes
            </button>
        </div>

        <!-- CARDS RESUMO -->
        <div class="row-fluid">
            <div class="span3" style="margin-bottom:16px;">
                <div style="background:#f8f9fa; border-radius:10px; padding:20px; border:2px solid transparent; cursor:pointer;" onmouseover="this.style.borderColor='#667eea'" onmouseout="this.style.borderColor='transparent'" onclick="abrirPainel('tab-tipos-obra')">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <div style="width:48px; height:48px; background:#667eea20; color:#667eea; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:24px;">
                            <i class="bx bx-building-house"></i>
                        </div>
                        <div>
                            <div style="font-weight:600; color:#333;">Tipos de Obra</div>
                            <div style="font-size:22px; font-weight:700; color:#667eea;"><?php echo count($tipos_obra); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="span3" style="margin-bottom:16px;">
                <div style="background:#f8f9fa; border-radius:10px; padding:20px; border:2px solid transparent; cursor:pointer;" onmouseover="this.style.borderColor='#764ba2'" onmouseout="this.style.borderColor='transparent'" onclick="abrirPainel('tab-tipos-atividade')">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <div style="width:48px; height:48px; background:#764ba220; color:#764ba2; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:24px;">
                            <i class="bx bx-task"></i>
                        </div>
                        <div>
                            <div style="font-weight:600; color:#333;">Tipos de Atividade</div>
                            <div style="font-size:22px; font-weight:700; color:#764ba2;"><?php echo count($tipos_atividades); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="span3" style="margin-bottom:16px;">
                <div style="background:#f8f9fa; border-radius:10px; padding:20px; border:2px solid transparent; cursor:pointer;" onmouseover="this.style.borderColor='#3498db'" onmouseout="this.style.borderColor='transparent'" onclick="abrirPainel('tab-status-obra')">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <div style="width:48px; height:48px; background:#3498db20; color:#3498db; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:24px;">
                            <i class="bx bx-flag"></i>
                        </div>
                        <div>
                            <div style="font-weight:600; color:#333;">Status de Obra</div>
                            <div style="font-size:22px; font-weight:700; color:#3498db;"><?php echo count($status_obra); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="span3" style="margin-bottom:16px;">
                <div style="background:#f8f9fa; border-radius:10px; padding:20px; border:2px solid transparent; cursor:pointer;" onmouseover="this.style.borderColor='#e74c3c'" onmouseout="this.style.borderColor='transparent'" onclick="abrirPainel('tab-especialidades')">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <div style="width:48px; height:48px; background:#e74c3c20; color:#e74c3c; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:24px;">
                            <i class="bx bx-hard-hat"></i>
                        </div>
                        <div>
                            <div style="font-weight:600; color:#333;">Especialidades</div>
                            <div style="font-size:22px; font-weight:700; color:#e74c3c;"><?php echo count($especialidades); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= PAINEL MODAL ================= -->
<div id="painelModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.6); z-index:9999; overflow-y:auto;">
    <div style="background:white; border-radius:12px; max-width:1200px; width:95%; margin:30px auto; min-height:80vh; position:relative;">
        <!-- HEADER -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:20px 24px; border-radius:12px 12px 0 0; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; color:white; font-size:18px;"><i class="bx bx-cog"></i> Configuracoes do Sistema de Obras</h3>
            <button type="button" onclick="fecharPainel()" style="background:rgba(255,255,255,0.2); border:none; color:white; width:36px; height:36px; border-radius:8px; cursor:pointer; font-size:20px;">
                <i class="bx bx-x"></i>
            </button>
        </div>

        <!-- CORPO COM ABAS -->
        <div style="display:flex; min-height:600px;">
            <!-- SIDEBAR -->
            <div style="width:260px; background:#f8f9fa; border-right:1px solid #e0e0e0; padding:16px 0;">
                <div class="aba-btn active" data-aba="tab-geral" onclick="ativarAba('tab-geral')" style="padding:12px 20px; cursor:pointer; display:flex; align-items:center; gap:10px; color:#666; border-left:3px solid transparent;">
                    <i class="bx bx-slider"></i> <span>Geral</span>
                </div>
                <div class="aba-btn" data-aba="tab-tipos-obra" onclick="ativarAba('tab-tipos-obra')" style="padding:12px 20px; cursor:pointer; display:flex; align-items:center; gap:10px; color:#666; border-left:3px solid transparent;">
                    <i class="bx bx-building-house"></i> <span>Tipos de Obra</span>
                    <span style="margin-left:auto; background:#e0e0e0; padding:2px 8px; border-radius:10px; font-size:11px;"><?php echo count($tipos_obra); ?></span>
                </div>
                <div class="aba-btn" data-aba="tab-tipos-atividade" onclick="ativarAba('tab-tipos-atividade')" style="padding:12px 20px; cursor:pointer; display:flex; align-items:center; gap:10px; color:#666; border-left:3px solid transparent;">
                    <i class="bx bx-task"></i> <span>Tipos de Atividade</span>
                    <span style="margin-left:auto; background:#e0e0e0; padding:2px 8px; border-radius:10px; font-size:11px;"><?php echo count($tipos_atividades); ?></span>
                </div>
                <div class="aba-btn" data-aba="tab-status-obra" onclick="ativarAba('tab-status-obra')" style="padding:12px 20px; cursor:pointer; display:flex; align-items:center; gap:10px; color:#666; border-left:3px solid transparent;">
                    <i class="bx bx-flag"></i> <span>Status de Obra</span>
                    <span style="margin-left:auto; background:#e0e0e0; padding:2px 8px; border-radius:10px; font-size:11px;"><?php echo count($status_obra); ?></span>
                </div>
                <div class="aba-btn" data-aba="tab-status-atividade" onclick="ativarAba('tab-status-atividade')" style="padding:12px 20px; cursor:pointer; display:flex; align-items:center; gap:10px; color:#666; border-left:3px solid transparent;">
                    <i class="bx bx-check-circle"></i> <span>Status de Atividade</span>
                    <span style="margin-left:auto; background:#e0e0e0; padding:2px 8px; border-radius:10px; font-size:11px;"><?php echo count($status_atividade); ?></span>
                </div>
                <div class="aba-btn" data-aba="tab-especialidades" onclick="ativarAba('tab-especialidades')" style="padding:12px 20px; cursor:pointer; display:flex; align-items:center; gap:10px; color:#666; border-left:3px solid transparent;">
                    <i class="bx bx-hard-hat"></i> <span>Especialidades</span>
                    <span style="margin-left:auto; background:#e0e0e0; padding:2px 8px; border-radius:10px; font-size:11px;"><?php echo count($especialidades); ?></span>
                </div>
                <div class="aba-btn" data-aba="tab-funcoes" onclick="ativarAba('tab-funcoes')" style="padding:12px 20px; cursor:pointer; display:flex; align-items:center; gap:10px; color:#666; border-left:3px solid transparent;">
                    <i class="bx bx-group"></i> <span>Funcoes da Equipe</span>
                    <span style="margin-left:auto; background:#e0e0e0; padding:2px 8px; border-radius:10px; font-size:11px;"><?php echo count($funcoes_equipe); ?></span>
                </div>
                <div class="aba-btn" data-aba="tab-notificacoes" onclick="ativarAba('tab-notificacoes')" style="padding:12px 20px; cursor:pointer; display:flex; align-items:center; gap:10px; color:#666; border-left:3px solid transparent;">
                    <i class="bx bx-bell"></i> <span>Notificacoes</span>
                </div>
            </div>

            <!-- CONTEUDO -->
            <div style="flex:1; padding:24px; overflow-y:auto; max-height:75vh;">

                <!-- ABA GERAL -->
                <div id="tab-geral" class="aba-conteudo" style="display:block;">
                    <h4 style="margin:0 0 16px 0;"><i class="bx bx-slider" style="color:#667eea;"></i> Configuracoes Gerais</h4>
                    <form method="post" action="<?php echo site_url('obras/salvarConfiguracao'); ?>">
                        <div class="control-group">
                            <label class="control-label">Nome do Sistema de Obras</label>
                            <div class="controls">
                                <input type="text" name="nome_sistema" class="span6" value="<?php echo htmlspecialchars($config['nome_sistema'] ?? 'Gestao de Obras'); ?>">
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <label class="control-label">Prazo Padrao para Inicio (dias)</label>
                                    <div class="controls">
                                        <input type="number" name="prazo_inicio_padrao" class="span6" value="<?php echo (int)($config['prazo_inicio_padrao'] ?? 7); ?>" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <label class="control-label">Prazo Padrao para Execucao (dias)</label>
                                    <div class="controls">
                                        <input type="number" name="prazo_execucao_padrao" class="span6" value="<?php echo (int)($config['prazo_execucao_padrao'] ?? 30); ?>" min="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">
                        <h5 style="margin:0 0 12px 0;">Funcionalidades</h5>
                        <table class="table table-condensed">
                            <tr>
                                <td>Sistema de Atividades</td>
                                <td><input type="checkbox" name="habilitar_atividades" <?php echo ($config['habilitar_atividades'] ?? true) ? 'checked' : ''; ?>></td>
                            </tr>
                            <tr>
                                <td>Sistema de Etapas</td>
                                <td><input type="checkbox" name="habilitar_etapas" <?php echo ($config['habilitar_etapas'] ?? true) ? 'checked' : ''; ?>></td>
                            </tr>
                            <tr>
                                <td>Check-in/Check-out</td>
                                <td><input type="checkbox" name="habilitar_checkin" <?php echo ($config['habilitar_checkin'] ?? true) ? 'checked' : ''; ?>></td>
                            </tr>
                            <tr>
                                <td>Geolocalizacao</td>
                                <td><input type="checkbox" name="habilitar_gps" <?php echo ($config['habilitar_gps'] ?? true) ? 'checked' : ''; ?>></td>
                            </tr>
                            <tr>
                                <td>Reatendimento</td>
                                <td><input type="checkbox" name="habilitar_reatendimento" <?php echo ($config['habilitar_reatendimento'] ?? true) ? 'checked' : ''; ?>></td>
                            </tr>
                            <tr>
                                <td>Portal do Tecnico</td>
                                <td><input type="checkbox" name="habilitar_portal_tecnico" <?php echo ($config['habilitar_portal_tecnico'] ?? true) ? 'checked' : ''; ?>></td>
                            </tr>
                        </table>
                        <div style="text-align:right; margin-top:16px;">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Salvar Configuracoes</button>
                        </div>
                    </form>
                </div>

                <!-- ABA TIPOS DE OBRA -->
                <div id="tab-tipos-obra" class="aba-conteudo" style="display:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h4 style="margin:0;"><i class="bx bx-building-house" style="color:#667eea;"></i> Tipos de Obra</h4>
                        <button class="btn btn-success" onclick="abrirModal('tipo-obra', null)"><i class="bx bx-plus"></i> Novo Tipo</button>
                    </div>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i> Tipos de Obra categorizam as obras no cadastro e relatorios.
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th style="width:40px;">Cor</th><th>Nome</th><th>Descricao</th><th style="width:100px;">Acoes</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tipos_obra as $tipo): ?>
                            <tr data-id="<?php echo (int)$tipo->id; ?>">
                                <td><span style="display:inline-block; width:20px; height:20px; border-radius:50%; background:<?php echo htmlspecialchars($tipo->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($tipo->icone); ?>"></i> <?php echo htmlspecialchars($tipo->nome); ?></td>
                                <td><?php echo htmlspecialchars($tipo->descricao ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-mini" onclick="abrirModal('tipo-obra', <?php echo (int)$tipo->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-mini btn-danger" onclick="excluirItem('tipo-obra', <?php echo (int)$tipo->id; ?>, '<?php echo htmlspecialchars($tipo->nome); ?>')"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ABA TIPOS DE ATIVIDADE -->
                <div id="tab-tipos-atividade" class="aba-conteudo" style="display:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h4 style="margin:0;"><i class="bx bx-task" style="color:#764ba2;"></i> Tipos de Atividade</h4>
                        <button class="btn btn-success" onclick="abrirModal('tipo-atividade', null)"><i class="bx bx-plus"></i> Novo Tipo</button>
                    </div>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i> Tipos de Atividade definem as categorias de trabalho nas obras.
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th style="width:40px;">Cor</th><th>Nome</th><th>Categoria</th><th>Descricao</th><th style="width:100px;">Acoes</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tipos_atividades as $tipo): ?>
                            <tr data-id="<?php echo (int)($tipo->idTipo ?? $tipo->id); ?>">
                                <td><span style="display:inline-block; width:20px; height:20px; border-radius:50%; background:<?php echo htmlspecialchars($tipo->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($tipo->icone); ?>"></i> <?php echo htmlspecialchars($tipo->nome); ?></td>
                                <td><span class="label"><?php echo htmlspecialchars($tipo->categoria ?? 'outro'); ?></span></td>
                                <td><?php echo htmlspecialchars($tipo->descricao ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-mini" onclick="abrirModal('tipo-atividade', <?php echo (int)($tipo->idTipo ?? $tipo->id); ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-mini btn-danger" onclick="excluirItem('tipo-atividade', <?php echo (int)($tipo->idTipo ?? $tipo->id); ?>, '<?php echo htmlspecialchars($tipo->nome); ?>')"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ABA STATUS DE OBRA -->
                <div id="tab-status-obra" class="aba-conteudo" style="display:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h4 style="margin:0;"><i class="bx bx-flag" style="color:#3498db;"></i> Status de Obra</h4>
                        <button class="btn btn-success" onclick="abrirModal('status-obra', null)"><i class="bx bx-plus"></i> Novo Status</button>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bx bx-error-circle"></i> <strong>Atenção:</strong> Alterar status padrao pode afetar relatorios existentes.
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th style="width:40px;">Cor</th><th>Nome</th><th>Ordem</th><th>Finalizado</th><th>Descricao</th><th style="width:100px;">Acoes</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status_obra as $status): ?>
                            <tr data-id="<?php echo (int)$status->id; ?>">
                                <td><span style="display:inline-block; width:20px; height:20px; border-radius:50%; background:<?php echo htmlspecialchars($status->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($status->icone); ?>"></i> <?php echo htmlspecialchars($status->nome); ?></td>
                                <td><?php echo (int)$status->ordem; ?></td>
                                <td><?php echo ($status->finalizado ?? false) ? '<span class="label label-success">Sim</span>' : '<span class="label">Nao</span>'; ?></td>
                                <td><?php echo htmlspecialchars($status->descricao ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-mini" onclick="abrirModal('status-obra', <?php echo (int)$status->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <?php if (!in_array($status->nome, ['Prospeccao', 'Em Andamento', 'Concluida'])): ?>
                                    <button class="btn btn-mini btn-danger" onclick="excluirItem('status-obra', <?php echo (int)$status->id; ?>, '<?php echo htmlspecialchars($status->nome); ?>')"><i class="bx bx-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ABA STATUS DE ATIVIDADE -->
                <div id="tab-status-atividade" class="aba-conteudo" style="display:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h4 style="margin:0;"><i class="bx bx-check-circle" style="color:#27ae60;"></i> Status de Atividade</h4>
                        <button class="btn btn-success" onclick="abrirModal('status-atividade', null)"><i class="bx bx-plus"></i> Novo Status</button>
                    </div>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i> Fluxo padrao: Agendada -> Iniciada -> Pausada (opcional) -> Concluida/Cancelada
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th style="width:40px;">Cor</th><th>Nome</th><th>Fluxo</th><th>Descricao</th><th style="width:100px;">Acoes</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status_atividade as $status): ?>
                            <tr data-id="<?php echo (int)$status->id; ?>">
                                <td><span style="display:inline-block; width:20px; height:20px; border-radius:50%; background:<?php echo htmlspecialchars($status->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($status->icone); ?>"></i> <?php echo htmlspecialchars($status->nome); ?></td>
                                <td><span class="label"><?php echo htmlspecialchars($status->fluxo ?? 'normal'); ?></span></td>
                                <td><?php echo htmlspecialchars($status->descricao ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-mini" onclick="abrirModal('status-atividade', <?php echo (int)$status->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <?php if (!in_array($status->nome, ['Agendada', 'Iniciada', 'Concluida'])): ?>
                                    <button class="btn btn-mini btn-danger" onclick="excluirItem('status-atividade', <?php echo (int)$status->id; ?>, '<?php echo htmlspecialchars($status->nome); ?>')"><i class="bx bx-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ABA ESPECIALIDADES -->
                <div id="tab-especialidades" class="aba-conteudo" style="display:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h4 style="margin:0;"><i class="bx bx-hard-hat" style="color:#e74c3c;"></i> Especialidades (Etapas)</h4>
                        <button class="btn btn-success" onclick="abrirModal('especialidade', null)"><i class="bx bx-plus"></i> Nova Especialidade</button>
                    </div>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i> Especialidades classificam as etapas da obra (Ex: Eletrica, Hidraulica, Acabamento).
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th style="width:40px;">Cor</th><th>Nome</th><th>Descricao</th><th style="width:100px;">Acoes</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($especialidades as $esp): ?>
                            <tr data-id="<?php echo (int)$esp->id; ?>">
                                <td><span style="display:inline-block; width:20px; height:20px; border-radius:50%; background:<?php echo htmlspecialchars($esp->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($esp->icone); ?>"></i> <?php echo htmlspecialchars($esp->nome); ?></td>
                                <td><?php echo htmlspecialchars($esp->descricao ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-mini" onclick="abrirModal('especialidade', <?php echo (int)$esp->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-mini btn-danger" onclick="excluirItem('especialidade', <?php echo (int)$esp->id; ?>, '<?php echo htmlspecialchars($esp->nome); ?>')"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ABA FUNCOES DA EQUIPE -->
                <div id="tab-funcoes" class="aba-conteudo" style="display:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h4 style="margin:0;"><i class="bx bx-group" style="color:#f39c12;"></i> Funcoes da Equipe</h4>
                        <button class="btn btn-success" onclick="abrirModal('funcao', null)"><i class="bx bx-plus"></i> Nova Funcao</button>
                    </div>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i> Funcoes definem os papeis dos membros da equipe na obra.
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th>Nome</th><th>Nivel</th><th>Descricao</th><th style="width:100px;">Acoes</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($funcoes_equipe as $funcao):
                                $nivelCor = ['alto' => '#e74c3c', 'medio' => '#f39c12', 'baixo' => '#27ae60'][($funcao->nivel ?? 'baixo')] ?? '#95a5a6';
                            ?>
                            <tr data-id="<?php echo (int)$funcao->id; ?>">
                                <td><?php echo htmlspecialchars($funcao->nome); ?></td>
                                <td><span class="label" style="background:<?php echo $nivelCor; ?>; color:white;"><?php echo htmlspecialchars($funcao->nivel ?? 'baixo'); ?></span></td>
                                <td><?php echo htmlspecialchars($funcao->descricao ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-mini" onclick="abrirModal('funcao', <?php echo (int)$funcao->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-mini btn-danger" onclick="excluirItem('funcao', <?php echo (int)$funcao->id; ?>, '<?php echo htmlspecialchars($funcao->nome); ?>')"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ABA NOTIFICACOES -->
                <div id="tab-notificacoes" class="aba-conteudo" style="display:none;">
                    <h4 style="margin:0 0 16px 0;"><i class="bx bx-bell" style="color:#e74c3c;"></i> Configuracoes de Notificacoes</h4>
                    <form method="post" action="<?php echo site_url('obras/salvarConfiguracaoNotificacoes'); ?>">
                        <h5>Eventos que geram notificacoes</h5>
                        <table class="table table-condensed">
                            <tr><td>Nova obra cadastrada</td><td><input type="checkbox" name="notif_nova_obra" <?php echo ($config_notif['nova_obra'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            <tr><td>Obra concluida</td><td><input type="checkbox" name="notif_obra_concluida" <?php echo ($config_notif['obra_concluida'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            <tr><td>Atividade atrasada</td><td><input type="checkbox" name="notif_atividade_atrasada" <?php echo ($config_notif['atividade_atrasada'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            <tr><td>Atividade reaberta</td><td><input type="checkbox" name="notif_atividade_reaberta" <?php echo ($config_notif['atividade_reaberta'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            <tr><td>Check-in do tecnico</td><td><input type="checkbox" name="notif_checkin" <?php echo ($config_notif['checkin'] ?? false) ? 'checked' : ''; ?>></td></tr>
                            <tr><td>Impedimento registrado</td><td><input type="checkbox" name="notif_impedimento" <?php echo ($config_notif['impedimento'] ?? true) ? 'checked' : ''; ?>></td></tr>
                        </table>
                        <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">
                        <h5>Canais de Notificacao</h5>
                        <table class="table table-condensed">
                            <tr><td>E-mail</td><td><input type="checkbox" name="canal_email" <?php echo ($config_notif['canal_email'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            <tr><td>WhatsApp</td><td><input type="checkbox" name="canal_whatsapp" <?php echo ($config_notif['canal_whatsapp'] ?? false) ? 'checked' : ''; ?>></td></tr>
                            <tr><td>Notificacao no Sistema</td><td><input type="checkbox" name="canal_sistema" <?php echo ($config_notif['canal_sistema'] ?? true) ? 'checked' : ''; ?>></td></tr>
                        </table>
                        <div style="text-align:right; margin-top:16px;">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Salvar Configuracoes</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL GENERICO PARA EDICAO ================= -->
<div id="modalEditar" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:10000;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modalEditarTitulo">Editar Item</h3>
    </div>
    <div class="modal-body" id="modalEditarBody">
        <!-- Formulario dinamico -->
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-primary" onclick="salvarModal()"><i class="bx bx-save"></i> Salvar</button>
    </div>
</div>

<!-- ================= DEBUG VISUAL ================= -->
<div id="debugPanel" style="position:fixed; bottom:10px; right:10px; background:#222; color:#0f0; padding:10px; border-radius:8px; z-index:99999; font-family:monospace; font-size:12px; max-width:350px; max-height:250px; overflow:auto; box-shadow:0 0 10px rgba(0,0,0,0.5); display:none;">
    <div style="color:#ff0; font-weight:bold; margin-bottom:5px;">DEBUG</div>
    <div id="debugLog">Iniciando...</div>
</div>

<script type="text/javascript">
// ==============================
// UTILITARIOS DE DEBUG
// ==============================
var debugLogEl = document.getElementById('debugLog');
function dbg(msg, tipo) {
    var cor = (tipo === 'erro') ? '#f44' : (tipo === 'aviso') ? '#fa0' : '#0f0';
    var hora = new Date().toLocaleTimeString();
    var linha = '[' + hora + '] ' + msg;
    if (debugLogEl) {
        debugLogEl.innerHTML = debugLogEl.innerHTML + '<div style="color:' + cor + '; border-bottom:1px solid #333; padding:2px 0;">' + linha + '</div>';
    }
    if (tipo === 'erro') console.error(linha);
    else if (tipo === 'aviso') console.warn(linha);
    else console.log(linha);
}

// Mostrar painel de debug se houver erro
window.onerror = function(msg, url, linha) {
    var p = document.getElementById('debugPanel');
    if (p) p.style.display = 'block';
    dbg('ERRO JS: ' + msg + ' (linha ' + linha + ')', 'erro');
};

dbg('Script carregado com sucesso', 'info');

// ==============================
// CONTROLE DE ABAS
// ==============================
function abrirPainel(abaId) {
    dbg('abrirPainel(' + (abaId || 'null') + ')', 'info');
    var painel = document.getElementById('painelModal');
    if (!painel) { dbg('ERRO: painelModal nao encontrado', 'erro'); return; }
    painel.style.display = 'block';
    document.body.style.overflow = 'hidden';
    ativarAba(abaId || 'tab-geral');
}

function fecharPainel() {
    var painel = document.getElementById('painelModal');
    if (painel) {
        painel.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function ativarAba(abaId) {
    dbg('ativarAba(' + abaId + ')', 'info');
    if (!abaId) abaId = 'tab-geral';
    if (abaId.charAt(0) === '#') abaId = abaId.substring(1);

    // Desativar todos os botoes
    var botoes = document.querySelectorAll('.aba-btn');
    for (var i = 0; i < botoes.length; i++) {
        botoes[i].classList.remove('active');
        botoes[i].style.background = 'transparent';
        botoes[i].style.color = '#666';
        botoes[i].style.borderLeftColor = 'transparent';
    }
    // Ativar botao correto
    var btn = document.querySelector('.aba-btn[data-aba="' + abaId + '"]');
    if (btn) {
        btn.classList.add('active');
        btn.style.background = '#f0f4ff';
        btn.style.color = '#667eea';
        btn.style.borderLeftColor = '#667eea';
    }

    // Esconder todos os conteudos
    var conteudos = document.querySelectorAll('.aba-conteudo');
    for (var j = 0; j < conteudos.length; j++) {
        conteudos[j].style.display = 'none';
    }
    // Mostrar conteudo correto
    var alvo = document.getElementById(abaId);
    if (alvo) {
        alvo.style.display = 'block';
        dbg('Aba ' + abaId + ' ativada', 'info');
    } else {
        dbg('Aba ' + abaId + ' NAO encontrada!', 'erro');
    }
}

// Fechar ao clicar fora
document.getElementById('painelModal').onclick = function(e) {
    if (e.target === this) fecharPainel();
};

// Fechar com ESC
document.onkeydown = function(e) {
    if (e.keyCode === 27) fecharPainel();
};

// ==============================
// VARIAVEIS GLOBAIS
// ==============================
var TIPO_ATUAL = null;
var ITEM_EDITANDO = null;

var URLS = {
    'tipo-obra': '<?php echo site_url("obras/salvarTipoObra"); ?>',
    'tipo-atividade': '<?php echo site_url("obras/salvarTipoAtividade"); ?>',
    'status-obra': '<?php echo site_url("obras/salvarStatusObra"); ?>',
    'status-atividade': '<?php echo site_url("obras/salvarStatusAtividade"); ?>',
    'especialidade': '<?php echo site_url("obras/salvarEspecialidade"); ?>',
    'funcao': '<?php echo site_url("obras/salvarFuncao"); ?>'
};

var URLS_EXCLUIR = {
    'tipo-obra': '<?php echo site_url("obras/excluirTipoObra"); ?>',
    'tipo-atividade': '<?php echo site_url("obras/excluirTipoAtividade"); ?>',
    'status-obra': '<?php echo site_url("obras/excluirStatusObra"); ?>',
    'status-atividade': '<?php echo site_url("obras/excluirStatusAtividade"); ?>',
    'especialidade': '<?php echo site_url("obras/excluirEspecialidade"); ?>',
    'funcao': '<?php echo site_url("obras/excluirFuncao"); ?>'
};

// ==============================
// ABERTURA DO MODAL DE EDICAO
// ==============================
function abrirModal(tipo, id) {
    dbg('abrirModal(' + tipo + ', ' + id + ')', 'info');
    TIPO_ATUAL = tipo;
    ITEM_EDITANDO = id;

    var titulo = document.getElementById('modalEditarTitulo');
    var body = document.getElementById('modalEditarBody');
    var html = '';
    var nomeItem = '';
    var descricaoItem = '';
    var corItem = '#3498db';
    var iconeItem = 'bx-building';
    var categoriaItem = 'outro';
    var duracaoItem = 30;
    var ordemItem = 1;
    var finalizadoItem = false;
    var fluxoItem = 'normal';
    var nivelItem = 'baixo';

    // Se for edicao, buscar dados da linha da tabela
    if (id !== null) {
        var linha = document.querySelector('tr[data-id="' + id + '"]');
        if (linha) {
            var tds = linha.getElementsByTagName('td');
            if (tds.length > 1) nomeItem = (tds[1].innerText || tds[1].textContent).replace(/^\s+|\s+$/g, '');
            if (tds.length > 2) {
                if (tipo === 'status-obra') {
                    ordemItem = parseInt((tds[2].innerText || tds[2].textContent), 10) || 1;
                    finalizadoItem = (tds[3].innerText || tds[3].textContent).indexOf('Sim') !== -1;
                    descricaoItem = (tds[4].innerText || tds[4].textContent).replace(/^\s+|\s+$/g, '');
                } else if (tipo === 'status-atividade') {
                    fluxoItem = (tds[2].innerText || tds[2].textContent).replace(/^\s+|\s+$/g, '').toLowerCase();
                    descricaoItem = (tds[3].innerText || tds[3].textContent).replace(/^\s+|\s+$/g, '');
                } else if (tipo === 'funcao') {
                    nivelItem = (tds[1].innerText || tds[1].textContent).replace(/^\s+|\s+$/g, '').toLowerCase();
                    descricaoItem = (tds[2].innerText || tds[2].textContent).replace(/^\s+|\s+$/g, '');
                } else {
                    descricaoItem = (tds[2].innerText || tds[2].textContent).replace(/^\s+|\s+$/g, '');
                }
            }
            // Tentar extrair cor do span colorido
            var spanCor = linha.querySelector('span[style*="background"]');
            if (spanCor) {
                var estilo = spanCor.getAttribute('style') || '';
                var match = estilo.match(/background:\s*([^;]+)/);
                if (match) corItem = match[1].replace(/^\s+|\s+$/g, '');
            }
        }
    }

    // Montar formulario conforme tipo
    if (tipo === 'tipo-obra') {
        titulo.innerHTML = (id ? '<i class="bx bx-edit"></i> Editar' : '<i class="bx bx-plus"></i> Novo') + ' Tipo de Obra';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="control-group"><label class="control-label">Nome</label><div class="controls"><input type="text" id="f_nome" class="span12" value="' + escapeHtml(nomeItem) + '" required></div></div>' +
            '<div class="control-group"><label class="control-label">Descricao</label><div class="controls"><textarea id="f_descricao" class="span12" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div></div>' +
            '<div class="row-fluid"><div class="span6"><div class="control-group"><label class="control-label">Cor</label><div class="controls"><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div></div></div><div class="span6"><div class="control-group"><label class="control-label">Icone</label><div class="controls"><select id="f_icone" class="span12">' + opcoesIcone(iconeItem) + '</select></div></div></div></div>';
    } else if (tipo === 'tipo-atividade') {
        titulo.innerHTML = (id ? '<i class="bx bx-edit"></i> Editar' : '<i class="bx bx-plus"></i> Novo') + ' Tipo de Atividade';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="control-group"><label class="control-label">Nome</label><div class="controls"><input type="text" id="f_nome" class="span12" value="' + escapeHtml(nomeItem) + '" required></div></div>' +
            '<div class="control-group"><label class="control-label">Descricao</label><div class="controls"><textarea id="f_descricao" class="span12" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div></div>' +
            '<div class="row-fluid"><div class="span4"><div class="control-group"><label class="control-label">Categoria</label><div class="controls"><select id="f_categoria" class="span12"><option value="execucao"' + (categoriaItem === 'execucao' ? ' selected' : '') + '>Execucao</option><option value="visita"' + (categoriaItem === 'visita' ? ' selected' : '') + '>Visita</option><option value="manutencao"' + (categoriaItem === 'manutencao' ? ' selected' : '') + '>Manutencao</option><option value="impedimento"' + (categoriaItem === 'impedimento' ? ' selected' : '') + '>Impedimento</option><option value="outro"' + (categoriaItem === 'outro' ? ' selected' : '') + '>Outro</option></select></div></div></div><div class="span4"><div class="control-group"><label class="control-label">Duracao (min)</label><div class="controls"><input type="number" id="f_duracao" class="span12" value="' + duracaoItem + '" min="5"></div></div></div><div class="span4"><div class="control-group"><label class="control-label">Cor</label><div class="controls"><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div></div></div></div>' +
            '<div class="control-group"><label class="control-label">Icone</label><div class="controls"><select id="f_icone" class="span12">' + opcoesIcone(iconeItem) + '</select></div></div>';
    } else if (tipo === 'status-obra') {
        titulo.innerHTML = (id ? '<i class="bx bx-edit"></i> Editar' : '<i class="bx bx-plus"></i> Novo') + ' Status de Obra';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="control-group"><label class="control-label">Nome</label><div class="controls"><input type="text" id="f_nome" class="span12" value="' + escapeHtml(nomeItem) + '" required></div></div>' +
            '<div class="control-group"><label class="control-label">Descricao</label><div class="controls"><textarea id="f_descricao" class="span12" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div></div>' +
            '<div class="row-fluid"><div class="span4"><div class="control-group"><label class="control-label">Cor</label><div class="controls"><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div></div></div><div class="span4"><div class="control-group"><label class="control-label">Ordem</label><div class="controls"><input type="number" id="f_ordem" class="span12" value="' + ordemItem + '" min="1"></div></div></div><div class="span4"><div class="control-group"><label class="control-label">Finalizado?</label><div class="controls"><input type="checkbox" id="f_finalizado"' + (finalizadoItem ? ' checked' : '') + '></div></div></div></div>' +
            '<div class="control-group"><label class="control-label">Icone</label><div class="controls"><select id="f_icone" class="span12">' + opcoesIcone(iconeItem) + '</select></div></div>';
    } else if (tipo === 'status-atividade') {
        titulo.innerHTML = (id ? '<i class="bx bx-edit"></i> Editar' : '<i class="bx bx-plus"></i> Novo') + ' Status de Atividade';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="control-group"><label class="control-label">Nome</label><div class="controls"><input type="text" id="f_nome" class="span12" value="' + escapeHtml(nomeItem) + '" required></div></div>' +
            '<div class="control-group"><label class="control-label">Descricao</label><div class="controls"><textarea id="f_descricao" class="span12" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div></div>' +
            '<div class="row-fluid"><div class="span6"><div class="control-group"><label class="control-label">Cor</label><div class="controls"><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div></div></div><div class="span6"><div class="control-group"><label class="control-label">Fluxo</label><div class="controls"><select id="f_fluxo" class="span12"><option value="inicial"' + (fluxoItem === 'inicial' ? ' selected' : '') + '>Inicial</option><option value="normal"' + (fluxoItem === 'normal' ? ' selected' : '') + '>Normal</option><option value="pausa"' + (fluxoItem === 'pausa' ? ' selected' : '') + '>Pausa</option><option value="final"' + (fluxoItem === 'final' ? ' selected' : '') + '>Final</option></select></div></div></div></div>' +
            '<div class="control-group"><label class="control-label">Icone</label><div class="controls"><select id="f_icone" class="span12">' + opcoesIcone(iconeItem) + '</select></div></div>';
    } else if (tipo === 'especialidade') {
        titulo.innerHTML = (id ? '<i class="bx bx-edit"></i> Editar' : '<i class="bx bx-plus"></i> Nova') + ' Especialidade';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="control-group"><label class="control-label">Nome</label><div class="controls"><input type="text" id="f_nome" class="span12" value="' + escapeHtml(nomeItem) + '" required></div></div>' +
            '<div class="control-group"><label class="control-label">Descricao</label><div class="controls"><textarea id="f_descricao" class="span12" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div></div>' +
            '<div class="row-fluid"><div class="span6"><div class="control-group"><label class="control-label">Cor</label><div class="controls"><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div></div></div><div class="span6"><div class="control-group"><label class="control-label">Icone</label><div class="controls"><select id="f_icone" class="span12">' + opcoesIcone(iconeItem) + '</select></div></div></div></div>';
    } else if (tipo === 'funcao') {
        titulo.innerHTML = (id ? '<i class="bx bx-edit"></i> Editar' : '<i class="bx bx-plus"></i> Nova') + ' Funcao';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="control-group"><label class="control-label">Nome</label><div class="controls"><input type="text" id="f_nome" class="span12" value="' + escapeHtml(nomeItem) + '" required></div></div>' +
            '<div class="control-group"><label class="control-label">Descricao</label><div class="controls"><textarea id="f_descricao" class="span12" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div></div>' +
            '<div class="control-group"><label class="control-label">Nivel</label><div class="controls"><select id="f_nivel" class="span12"><option value="baixo"' + (nivelItem === 'baixo' ? ' selected' : '') + '>Baixo</option><option value="medio"' + (nivelItem === 'medio' ? ' selected' : '') + '>Medio</option><option value="alto"' + (nivelItem === 'alto' ? ' selected' : '') + '>Alto</option></select></div></div>';
    }

    body.innerHTML = html;
    $('#modalEditar').modal('show');
}

function opcoesIcone(selecionado) {
    var icones = [
        ['bx-building', 'Predio'], ['bx-home', 'Casa'], ['bx-brush', 'Pincel'], ['bx-wrench', 'Ferramenta'],
        ['bx-plug', 'Plug'], ['bx-box', 'Caixa'], ['bx-hard-hat', 'Capacete'], ['bx-bolt-circle', 'Raio'],
        ['bx-flag', 'Bandeira'], ['bx-calendar', 'Calendario'], ['bx-check-circle', 'Check'], ['bx-search', 'Lupa'],
        ['bx-cog', 'Engrenagem'], ['bx-block', 'Bloqueio'], ['bx-task', 'Tarefa'], ['bx-user', 'Usuario'],
        ['bx-group', 'Grupo'], ['bx-bell', 'Sino'], ['bx-star', 'Estrela'], ['bx-heart', 'Coracao']
    ];
    var opts = '';
    for (var i = 0; i < icones.length; i++) {
        opts += '<option value="' + icones[i][0] + '"' + (icones[i][0] === selecionado ? ' selected' : '') + '>' + icones[i][1] + '</option>';
    }
    return opts;
}

function escapeHtml(texto) {
    if (!texto) return '';
    return texto.toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// ==============================
// SALVAR VIA AJAX
// ==============================
function salvarModal() {
    if (!TIPO_ATUAL) { alert('Nenhum tipo selecionado'); return; }

    var dados = {
        id: document.getElementById('f_id').value,
        nome: document.getElementById('f_nome').value,
        descricao: document.getElementById('f_descricao').value
    };
    if (!dados.nome) { alert('Nome e obrigatorio'); return; }

    if (document.getElementById('f_cor')) dados.cor = document.getElementById('f_cor').value;
    if (document.getElementById('f_icone')) dados.icone = document.getElementById('f_icone').value;
    if (document.getElementById('f_categoria')) dados.categoria = document.getElementById('f_categoria').value;
    if (document.getElementById('f_duracao')) dados.duracao = document.getElementById('f_duracao').value;
    if (document.getElementById('f_ordem')) dados.ordem = document.getElementById('f_ordem').value;
    if (document.getElementById('f_finalizado')) dados.finalizado = document.getElementById('f_finalizado').checked ? 1 : 0;
    if (document.getElementById('f_fluxo')) dados.fluxo = document.getElementById('f_fluxo').value;
    if (document.getElementById('f_nivel')) dados.nivel = document.getElementById('f_nivel').value;

    dbg('Enviando AJAX para ' + TIPO_ATUAL, 'info');

    $.ajax({
        url: URLS[TIPO_ATUAL],
        type: 'POST',
        data: dados,
        dataType: 'json',
        success: function(resp) {
            dbg('Resposta AJAX: ' + JSON.stringify(resp), 'info');
            if (resp && resp.success) {
                $('#modalEditar').modal('hide');
                location.reload();
            } else {
                alert('Erro: ' + (resp && resp.message ? resp.message : 'Erro ao salvar'));
            }
        },
        error: function(xhr) {
            dbg('Erro AJAX status ' + xhr.status, 'erro');
            alert('Erro ao salvar. Verifique o console.');
        }
    });
}

// ==============================
// EXCLUIR VIA AJAX
// ==============================
function excluirItem(tipo, id, nome) {
    if (!confirm('Tem certeza que deseja excluir "' + nome + '"?')) return;
    dbg('Excluindo ' + tipo + ' ID ' + id, 'aviso');
    $.ajax({
        url: URLS_EXCLUIR[tipo],
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(resp) {
            if (resp && resp.success) {
                location.reload();
            } else {
                alert('Erro: ' + (resp && resp.message ? resp.message : 'Erro ao excluir'));
            }
        },
        error: function(xhr) {
            dbg('Erro AJAX excluir status ' + xhr.status, 'erro');
            alert('Erro ao excluir. Verifique o console.');
        }
    });
}
</script>
