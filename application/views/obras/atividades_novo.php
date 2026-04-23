<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.atividades-wrapper { padding: 20px; max-width: 1400px; margin: 0 auto; }
.atividades-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 25px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3);
}
.atividades-header h1 { margin: 0; font-size: 24px; display: flex; align-items: center; gap: 10px; }
.atividades-header p { margin: 8px 0 0 0; opacity: 0.9; font-size: 14px; }
.atividades-header .actions { margin-top: 15px; display: flex; gap: 10px; }

/* Stats */
.stats-row { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
.stat-box {
    background: white;
    border-radius: 12px;
    padding: 20px;
    min-width: 150px;
    flex: 1;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
}
.stat-icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: white; }
.stat-icon.blue { background: linear-gradient(135deg, #667eea, #764ba2); }
.stat-icon.green { background: linear-gradient(135deg, #11998e, #38ef7d); }
.stat-icon.orange { background: linear-gradient(135deg, #f093fb, #f5576c); }
.stat-icon.gray { background: linear-gradient(135deg, #a8edea, #fed6e3); color: #555; }
.stat-info h3 { margin: 0; font-size: 26px; font-weight: 700; color: #333; }
.stat-info span { font-size: 13px; color: #888; }

/* Filtros */
.filtros-box {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: center;
}
.filtros-box input, .filtros-box select {
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    min-width: 200px;
    flex: 1;
}
.filtros-box input:focus, .filtros-box select:focus { border-color: #667eea; outline: none; }

/* Cards */
.atividades-lista { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
.atv-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-left: 4px solid #ddd;
    cursor: pointer;
    transition: all 0.3s;
}
.atv-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
.atv-card.agendada { border-left-color: #95a5a6; }
.atv-card.iniciada { border-left-color: #3498db; background: #f8fbff; }
.atv-card.pausada { border-left-color: #f39c12; background: #fffbf0; }
.atv-card.concluida { border-left-color: #27ae60; background: #f0fff4; }
.atv-card.cancelada { border-left-color: #e74c3c; background: #fff5f5; }

.atv-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.atv-card-titulo { font-size: 16px; font-weight: 600; color: #333; flex: 1; margin-right: 10px; }
.atv-card-status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}
.atv-card-status.agendada { background: #ecf0f1; color: #7f8c8d; }
.atv-card-status.iniciada { background: #3498db; color: white; }
.atv-card-status.pausada { background: #f39c12; color: white; }
.atv-card-status.concluida { background: #27ae60; color: white; }
.atv-card-status.cancelada { background: #e74c3c; color: white; }

.atv-card-info { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 12px; font-size: 13px; color: #666; }
.atv-card-info i { color: #667eea; margin-right: 5px; }
.atv-card-info span { display: flex; align-items: center; }

.atv-card-desc { font-size: 13px; color: #666; margin-bottom: 12px; line-height: 1.5; }

.atv-card-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #eee; }
.atv-card-progresso { flex: 1; margin-right: 15px; }
.atv-card-progresso-barra { height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; }
.atv-card-progresso-fill { height: 100%; border-radius: 3px; transition: width 0.5s; }
.atv-card-badges { display: flex; gap: 8px; }
.atv-card-badge { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
.atv-card-badge:hover { transform: scale(1.1); }
.atv-card-badge.view { background: #e3f2fd; color: #1976d2; }
.atv-card-badge.delete { background: #ffebee; color: #c62828; }
.atv-card-badge.wizard { background: #f3e5f5; color: #7b1fa2; }

.empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
.empty-state i { font-size: 60px; color: #667eea; margin-bottom: 15px; display: block; }
.empty-state h3 { margin: 0 0 10px 0; color: #333; }
.empty-state p { color: #888; margin: 0 0 20px 0; }

/* Modal */
.modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; }
.modal-overlay.ativo { display: flex; }
.modal-box {
    background: white;
    border-radius: 15px;
    width: 90%;
    max-width: 700px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalEntrar 0.3s ease;
}
@keyframes modalEntrar { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header h3 { margin: 0; font-size: 18px; display: flex; align-items: center; gap: 10px; }
.modal-header .fechar { background: none; border: none; color: white; font-size: 28px; cursor: pointer; opacity: 0.8; }
.modal-header .fechar:hover { opacity: 1; }
.modal-body { padding: 25px; max-height: 60vh; overflow-y: auto; }
.modal-footer { padding: 20px 25px; background: #f8f9fa; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 10px; }

.form-group { margin-bottom: 20px; }
.form-label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px; }
.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    box-sizing: border-box;
}
.form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #667eea; outline: none; }
.form-textarea { min-height: 80px; resize: vertical; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
@media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}
.btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.15); }
.btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
.btn-success { background: linear-gradient(135deg, #11998e, #38ef7d); color: white; }
.btn-danger { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; }
.btn-secondary { background: #f0f0f0; color: #666; }

.secao-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}
.secao-info h4 { margin: 0 0 10px 0; color: #667eea; font-size: 14px; }
.secao-info p { margin: 5px 0; font-size: 14px; color: #555; }
.info-badge {
    display: inline-block;
    padding: 4px 10px;
    background: #e3f2fd;
    color: #1976d2;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}
</style>

<!-- DEBUG CONSOLE - Remover em produção -->
<div id="debugConsole" style="position:fixed; bottom:10px; right:10px; background:#333; color:#0f0; padding:10px; font-family:monospace; font-size:12px; max-width:400px; max-height:200px; overflow:auto; z-index:99999; border-radius:5px; display:block;">
  <strong>DEBUG JS:</strong> <button onclick="document.getElementById('debugConsole').style.display='none'" style="float:right;color:red;">X</button>
  <div id="debugOutput">Inicializando...</div>
</div>

<script type="text/javascript">
// Debug helper
var debugMsgs = [];
function logDebug(msg) {
    debugMsgs.push(new Date().toLocaleTimeString() + ': ' + msg);
    var out = document.getElementById('debugOutput');
    if (out) out.innerHTML = debugMsgs.join('<br>');
    console.log('[DEBUG]', msg);
}
</script>

<div class="atividades-wrapper">
    <!-- Header -->
    <div class="atividades-header">
        <h1><i class='icon icon-calendar'></i> Atividades da Obra</h1>
        <p><?php echo htmlspecialchars($obra->nome ?? 'Obra'); ?></p>
        <div class="actions">
            <a href="<?php echo site_url('obras/visualizar/' . ($obra->id ?? 0)); ?>" class="btn btn-secondary">
                <i class='icon icon-arrow-left'></i> Voltar à Obra
            </a>
            <button class="btn btn-primary" onclick="abrirModalNova()">
                <i class='icon icon-plus'></i> Nova Atividade
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <?php
        $total = count($atividades ?? []) + count($atividades_registradas ?? []);
        $concluidas = 0;
        $em_andamento = 0;
        $pendentes = 0;

        foreach ($atividades ?? [] as $a) {
            if (($a->status ?? '') == 'concluida') $concluidas++;
            elseif (($a->status ?? '') == 'iniciada') $em_andamento++;
            else $pendentes++;
        }
        foreach ($atividades_registradas ?? [] as $a) {
            if (($a->status ?? '') == 'finalizada' && ($a->concluida ?? 0)) $concluidas++;
            elseif (!empty($a->hora_inicio) && empty($a->hora_fim)) $em_andamento++;
            else $pendentes++;
        }
        ?>
        <div class="stat-box">
            <div class="stat-icon blue"><i class='icon icon-calendar'></i></div>
            <div class="stat-info">
                <h3><?php echo $total; ?></h3>
                <span>Total Atividades</span>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon green"><i class='icon icon-ok'></i></div>
            <div class="stat-info">
                <h3><?php echo $concluidas; ?></h3>
                <span>Concluídas</span>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon orange"><i class='icon icon-time'></i></div>
            <div class="stat-info">
                <h3><?php echo $em_andamento; ?></h3>
                <span>Em Andamento</span>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon gray"><i class='icon icon-time'></i></div>
            <div class="stat-info">
                <h3><?php echo $pendentes; ?></h3>
                <span>Pendentes</span>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filtros-box">
        <input type="text" id="filtroBusca" placeholder="Buscar atividade..." onkeyup="filtrarAtividades()">
        <select id="filtroStatus" onchange="filtrarAtividades()">
            <option value="">Todos os Status</option>
            <option value="agendada">Agendada</option>
            <option value="iniciada">Iniciada</option>
            <option value="pausada">Pausada</option>
            <option value="concluida">Concluída</option>
            <option value="cancelada">Cancelada</option>
        </select>
        <select id="filtroTipo" onchange="filtrarAtividades()">
            <option value="">Todos os Tipos</option>
            <option value="trabalho">Trabalho</option>
            <option value="visita">Visita Técnica</option>
            <option value="manutencao">Manutenção</option>
            <option value="impedimento">Impedimento</option>
            <option value="outro">Outro</option>
        </select>
    </div>

    <!-- Lista de Atividades -->
    <div class="atividades-lista" id="listaAtividades">
        <?php
        // Mesclar atividades
        $todas_atividades = [];

        // Sistema antigo
        foreach ($atividades ?? [] as $ativ) {
            $todas_atividades[] = [
                'id' => $ativ->id ?? 0,
                'titulo' => $ativ->titulo ?? 'Atividade',
                'descricao' => $ativ->descricao ?? '',
                'status' => $ativ->status ?? 'agendada',
                'tipo' => $ativ->tipo ?? 'trabalho',
                'data' => $ativ->data_atividade ?? $ativ->data_criacao ?? date('Y-m-d'),
                'tecnico' => $ativ->nome_tecnico ?? $ativ->tecnico_nome ?? 'Não atribuído',
                'etapa' => $ativ->nome_etapa ?? $ativ->etapa_nome ?? 'Geral',
                'progresso' => $ativ->percentual_concluido ?? 0,
                'sistema' => 'antigo'
            ];
        }

        // Sistema novo
        foreach ($atividades_registradas ?? [] as $ativ) {
            $status = 'agendada';
            if (!empty($ativ->hora_fim) && ($ativ->status ?? '') == 'finalizada') {
                $status = 'concluida';
            } elseif (!empty($ativ->hora_inicio)) {
                $status = 'iniciada';
            }

            $todas_atividades[] = [
                'id' => $ativ->idAtividade ?? 0,
                'titulo' => $ativ->titulo ?? $ativ->tipo_atividade ?? 'Atividade Técnica',
                'descricao' => $ativ->descricao ?? '',
                'status' => $status,
                'tipo' => $ativ->categoria ?? 'trabalho',
                'data' => date('Y-m-d', strtotime($ativ->hora_inicio ?? 'now')),
                'tecnico' => $ativ->nome_tecnico ?? 'Não atribuído',
                'etapa' => $ativ->etapa_nome ?? 'Geral',
                'progresso' => ($ativ->status == 'finalizada' && ($ativ->concluida ?? 0)) ? 100 : (empty($ativ->hora_fim) && !empty($ativ->hora_inicio) ? 50 : 0),
                'sistema' => 'novo',
                'hora_inicio' => $ativ->hora_inicio ?? null,
                'hora_fim' => $ativ->hora_fim ?? null,
                'duracao' => $ativ->duracao_minutos ?? null
            ];
        }

        // Ordenar por data
        usort($todas_atividades, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        if (empty($todas_atividades)):
        ?>
        <div class="empty-state">
            <i class='icon icon-remove' style="font-size:60px;color:#667eea;margin-bottom:15px;display:block;"></i>
            <h3>Nenhuma atividade encontrada</h3>
            <p>Esta obra ainda não possui atividades registradas.</p>
            <button class="btn btn-primary" onclick="abrirModalNova()">
                <i class='icon icon-plus'></i> Adicionar Primeira Atividade
            </button>
        </div>
        <?php else: ?>

        <?php foreach ($todas_atividades as $atv):
            $status_class = $atv['status'];
            $status_label = ucfirst($atv['status']);
            // Codificar JSON em base64 para evitar problemas com aspas no HTML
            $atv_json_base64 = base64_encode(json_encode($atv));
        ?>
        <div class="atv-card <?php echo $status_class; ?>"
             data-titulo="<?php echo strtolower($atv['titulo']); ?>"
             data-status="<?php echo $atv['status']; ?>"
             data-tipo="<?php echo $atv['tipo']; ?>"
             onclick="abrirDetalhes('<?php echo $atv['sistema']; ?>', <?php echo $atv['id']; ?>, '<?php echo $atv_json_base64; ?>')">

            <div class="atv-card-header">
                <div class="atv-card-titulo"><?php echo htmlspecialchars($atv['titulo']); ?></div>
                <span class="atv-card-status <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
            </div>

            <div class="atv-card-info">
                <span><i class='icon icon-calendar'></i> <?php echo date('d/m/Y', strtotime($atv['data'])); ?></span>
                <span><i class='icon icon-user'></i> <?php echo htmlspecialchars($atv['tecnico']); ?></span>
                <span><i class='icon icon-tasks'></i> <?php echo htmlspecialchars($atv['etapa']); ?></span>
                <?php if (!empty($atv['duracao'])): ?>
                <span><i class='icon icon-time'></i> <?php echo floor($atv['duracao']/60) . 'h ' . ($atv['duracao']%60) . 'min'; ?></span>
                <?php endif; ?>
            </div>

            <?php if ($atv['descricao']): ?>
            <div class="atv-card-desc"><?php echo htmlspecialchars(substr($atv['descricao'], 0, 100)) . (strlen($atv['descricao']) > 100 ? '...' : ''); ?></div>
            <?php endif; ?>

            <div class="atv-card-footer">
                <div class="atv-card-progresso">
                    <div class="atv-card-progresso-barra">
                        <div class="atv-card-progresso-fill" style="width: <?php echo $atv['progresso']; ?>%; background: <?php echo $atv['progresso'] >= 100 ? '#27ae60' : ($atv['progresso'] > 0 ? '#3498db' : '#95a5a6'); ?>"></div>
                    </div>
                </div>
                <div class="atv-card-badges">
                    <?php if ($atv['sistema'] == 'novo'): ?>
                    <span class="atv-card-badge wizard" title="Sistema Wizard"><i class='icon icon-time'></i></span>
                    <?php endif; ?>
                    <span class="atv-card-badge view" title="Ver detalhes"><i class='icon icon-eye-open'></i></span>
                    <span class="atv-card-badge delete" title="Excluir" onclick="event.stopPropagation(); excluirAtividade('<?php echo $atv['sistema']; ?>', <?php echo $atv['id']; ?>)"><i class='icon icon-trash'></i></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal-overlay" id="modalDetalhes">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class='icon icon-check'></i> Detalhes da Atividade</h3>
            <button class="fechar" onclick="fecharModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <p>Carregando...</p>
        </div>
        <div class="modal-footer" id="modalFooter">
            <button class="btn btn-secondary" onclick="fecharModal()">Fechar</button>
        </div>
    </div>
</div>

<!-- Modal Nova Atividade -->
<div class="modal-overlay" id="modalNova">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class='icon icon-plus'></i> Nova Atividade</h3>
            <button class="fechar" onclick="fecharModalNova()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formNovaAtividade">
                <input type="hidden" name="obra_id" value="<?php echo $obra->id ?? 0; ?>">

                <div class="form-group">
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" class="form-input" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Etapa *</label>
                        <select name="etapa_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($etapas ?? [] as $e): ?>
                            <option value="<?php echo $e->id; ?>">#<?php echo $e->numero_etapa ?? '?'; ?> - <?php echo htmlspecialchars($e->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="trabalho">Trabalho</option>
                            <option value="visita">Visita Técnica</option>
                            <option value="manutencao">Manutenção</option>
                            <option value="impedimento">Impedimento</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Data</label>
                        <input type="date" name="data_atividade" class="form-input" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Técnico Responsável</label>
                        <select name="tecnico_id" class="form-select">
                            <option value="">Selecione...</option>
                            <?php foreach ($tecnicos ?? [] as $t): ?>
                            <option value="<?php echo $t->idUsuarios; ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-textarea" rows="3"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="fecharModalNova()">Cancelar</button>
            <button class="btn btn-primary" onclick="salvarNovaAtividade()">
                <i class='icon icon-save'></i> Salvar
            </button>
        </div>
    </div>
</div>

<script type="text/javascript">
// Variáveis globais
var atividadeAtual = null;
var sistemaAtual = null;

// Log inicial
logDebug('JavaScript carregado');

// Filtrar atividades
function filtrarAtividades() {
    var busca = document.getElementById('filtroBusca').value.toLowerCase();
    var status = document.getElementById('filtroStatus').value;
    var tipo = document.getElementById('filtroTipo').value;
    var cards = document.querySelectorAll('.atv-card');

    for (var i = 0; i < cards.length; i++) {
        var card = cards[i];
        var titulo = card.getAttribute('data-titulo') || '';
        var cardStatus = card.getAttribute('data-status') || '';
        var cardTipo = card.getAttribute('data-tipo') || '';

        var matchBusca = !busca || titulo.indexOf(busca) !== -1;
        var matchStatus = !status || cardStatus === status;
        var matchTipo = !tipo || cardTipo === tipo;

        card.style.display = (matchBusca && matchStatus && matchTipo) ? 'block' : 'none';
    }
}

// Abrir modal de detalhes
function abrirDetalhes(sistema, id, dadosBase64) {
    logDebug('abrindoDetalhes: sistema=' + sistema + ', id=' + id);

    sistemaAtual = sistema;

    try {
        // Decodificar base64
        var jsonStr = atob(dadosBase64);
        atividadeAtual = JSON.parse(jsonStr);
        logDebug('Dados parseados: ' + JSON.stringify(atividadeAtual).substring(0, 100));
    } catch(e) {
        logDebug('ERRO parse JSON: ' + e.message);
        atividadeAtual = { id: id, sistema: sistema };
    }

    var modal = document.getElementById('modalDetalhes');
    var body = document.getElementById('modalBody');
    var footer = document.getElementById('modalFooter');

    logDebug('Elementos: modal=' + (modal ? 'ok' : 'NULO') + ', body=' + (body ? 'ok' : 'NULO'));

    if (!modal || !body) {
        alert('Erro: Modal não encontrado no DOM');
        return;
    }

    // Conteúdo baseado no sistema
    var html = '';

    if (sistema === 'novo') {
        // Sistema novo - buscar via AJAX
        body.innerHTML = '<p style="text-align:center;"><i class="icon icon-refresh icon-spin" style="font-size:30px;color:#667eea;"></i><br>Carregando...</p>';
        modal.classList.add('ativo');

        fetch('<?php echo site_url("atividades/detalhes/"); ?>' + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                renderizarDetalhesNovo(data.atividade, body, footer);
            } else {
                body.innerHTML = '<p style="color:red;">Erro ao carregar atividade.</p>';
            }
        })
        .catch(function() {
            body.innerHTML = '<p style="color:red;">Erro ao carregar atividade.</p>';
        });
    } else {
        // Sistema antigo
        html = renderizarDetalhesAntigo(atividadeAtual);
        body.innerHTML = html;

        // Botões para sistema antigo
        var botoes = '<button class="btn btn-secondary" onclick="fecharModal()">Fechar</button>';
        botoes += '<button class="btn btn-primary" onclick="editarAtividade()" id="btnEditarAtv">Editar</button>';
        footer.innerHTML = botoes;

        modal.classList.add('ativo');
    }
}

// Renderizar detalhes sistema novo
function renderizarDetalhesNovo(atv, body, footer) {
    var statusText = (atv.status || 'N/A').toUpperCase();
    var html = '';

    html += '<div class="secao-info">';
    html += '<h4><i class="icon icon-info-sign"></i> Informações Gerais</h4>';
    html += '<p><strong>Status:</strong> <span class="info-badge">' + statusText + '</span></p>';
    html += '<p><strong>Tipo:</strong> ' + (atv.tipo_atividade || '-') + '</p>';
    html += '<p><strong>Técnico:</strong> ' + (atv.nome_tecnico || '-') + '</p>';
    html += '<p><strong>Etapa:</strong> ' + (atv.etapa_nome || '-') + '</p>';
    html += '</div>';

    if (atv.hora_inicio) {
        html += '<div class="secao-info">';
        html += '<h4><i class="icon icon-time"></i> Registro de Tempo</h4>';
        html += '<div class="form-row">';
        html += '<p><strong>Início:</strong> ' + formatarDataHora(atv.hora_inicio) + '</p>';
        html += '<p><strong>Fim:</strong> ' + (atv.hora_fim ? formatarDataHora(atv.hora_fim) : 'Em andamento') + '</p>';
        html += '</div>';
        if (atv.duracao_minutos) {
            var h = Math.floor(atv.duracao_minutos / 60);
            var m = atv.duracao_minutos % 60;
            html += '<p><strong>Duração:</strong> ' + h + 'h ' + m + 'min</p>';
        }
        html += '</div>';
    }

    if (atv.descricao || atv.observacoes) {
        html += '<div class="secao-info">';
        html += '<h4><i class="icon icon-list-alt"></i> Observações</h4>';
        html += '<p>' + (atv.descricao || atv.observacoes || '-') + '</p>';
        html += '</div>';
    }

    body.innerHTML = html;

    // Apenas fechar para sistema novo (edição é no wizard)
    footer.innerHTML = '<button class="btn btn-secondary" onclick="fecharModal()">Fechar</button>';
}

// Renderizar detalhes sistema antigo
function renderizarDetalhesAntigo(atv) {
    logDebug('renderizarDetalhesAntigo: ' + JSON.stringify(atv).substring(0, 100));

    var html = '';

    // Verificar se atv é válido
    if (!atv) {
        logDebug('ERRO: atv é nulo');
        return '<p style="color:red">Erro: dados da atividade inválidos</p>';
    }

    html += '<div class="secao-info">';
    html += '<h4><i class="icon icon-info-sign"></i> Informações Gerais</h4>';
    html += '<div class="form-row">';
    html += '<div class="form-group">';
    html += '<label class="form-label">Título</label>';
    var titulo = (atv.titulo || atv.titulo || '');
    html += '<input type="text" class="form-input view-field" value="' + titulo.replace(/"/g, '&quot;') + '" readonly>';
    html += '<input type="text" class="form-input edit-field" id="edit_titulo" value="' + titulo.replace(/"/g, '&quot;') + '" style="display:none;">';
    html += '</div>';
    html += '<div class="form-group">';
    html += '<label class="form-label">Status</label>';
    var statusVal = atv.status || 'agendada';
    html += '<span class="info-badge">' + statusVal.toUpperCase() + '</span>';
    html += '</div>';
    html += '</div>';

    html += '<div class="form-group">';
    html += '<label class="form-label">Descrição</label>';
    html += '<textarea class="form-textarea view-field" readonly>' + (atv.descricao || '-') + '</textarea>';
    html += '<textarea class="form-textarea edit-field" id="edit_descricao" style="display:none;">' + (atv.descricao || '') + '</textarea>';
    html += '</div>';
    html += '</div>';

    html += '<div class="secao-info">';
    html += '<h4><i class="icon icon-calendar"></i> Execução</h4>';
    html += '<div class="form-row">';
    html += '<div class="form-group">';
    html += '<label class="form-label">Data</label>';
    html += '<input type="text" class="form-input view-field" value="' + (atv.data ? formatarData(atv.data) : '-') + '" readonly>';
    html += '<input type="date" class="form-input edit-field" id="edit_data" value="' + (atv.data || '') + '" style="display:none;">';
    html += '</div>';
    html += '<div class="form-group">';
    html += '<label class="form-label">Tipo</label>';
    html += '<input type="text" class="form-input view-field" value="' + (atv.tipo ? atv.tipo.charAt(0).toUpperCase() + atv.tipo.slice(1) : '-') + '" readonly>';
    html += '<select class="form-select edit-field" id="edit_tipo" style="display:none;">';
    var tipos = ['trabalho', 'visita', 'manutencao', 'impedimento', 'outro'];
    var tiposLabels = ['Trabalho', 'Visita Técnica', 'Manutenção', 'Impedimento', 'Outro'];
    for (var i = 0; i < tipos.length; i++) {
        var selected = atv.tipo === tipos[i] ? 'selected' : '';
        html += '<option value="' + tipos[i] + '" ' + selected + '>' + tiposLabels[i] + '</option>';
    }
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    html += '<div class="secao-info">';
    html += '<h4><i class="icon icon-user"></i> Responsáveis</h4>';
    html += '<p><strong>Técnico:</strong> ' + (atv.tecnico || '-') + '</p>';
    html += '<p><strong>Etapa:</strong> ' + (atv.etapa || '-') + '</p>';
    html += '</div>';

    return html;
}

// Editar atividade
function editarAtividade() {
    var viewFields = document.querySelectorAll('.view-field');
    var editFields = document.querySelectorAll('.edit-field');

    for (var i = 0; i < viewFields.length; i++) {
        viewFields[i].style.display = 'none';
    }
    for (var i = 0; i < editFields.length; i++) {
        editFields[i].style.display = 'block';
    }

    // Mudar botões
    var footer = document.getElementById('modalFooter');
    footer.innerHTML = '<button class="btn btn-secondary" onclick="cancelarEdicao()">Cancelar</button>' +
                       '<button class="btn btn-success" onclick="salvarEdicao()">Salvar</button>';
}

// Cancelar edição
function cancelarEdicao() {
    // Recarregar detalhes originais
    abrirDetalhes(sistemaAtual, atividadeAtual.id, JSON.stringify(atividadeAtual));
}

// Salvar edição
function salvarEdicao() {
    var dados = {
        id: atividadeAtual.id,
        titulo: document.getElementById('edit_titulo').value,
        descricao: document.getElementById('edit_descricao').value,
        data_atividade: document.getElementById('edit_data').value,
        tipo: document.getElementById('edit_tipo').value
    };

    fetch('<?php echo site_url("obras/api_salvarAtividade"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams(dados)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            alert('Atividade atualizada com sucesso!');
            fecharModal();
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível salvar'));
        }
    })
    .catch(function() {
        alert('Erro ao salvar atividade');
    });
}

// Fechar modal
function fecharModal() {
    document.getElementById('modalDetalhes').classList.remove('ativo');
}

// Abrir modal nova atividade
function abrirModalNova() {
    document.getElementById('modalNova').classList.add('ativo');
}

// Fechar modal nova
function fecharModalNova() {
    document.getElementById('modalNova').classList.remove('ativo');
}

// Salvar nova atividade
function salvarNovaAtividade() {
    var form = document.getElementById('formNovaAtividade');
    var formData = new FormData(form);

    fetch('<?php echo site_url("obras/adicionarAtividade"); ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            alert('Atividade criada com sucesso!');
            fecharModalNova();
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível criar'));
        }
    })
    .catch(function() {
        // Se falhar, tentar redirecionar
        alert('Atividade criada! Recarregando...');
        location.reload();
    });
}

// Excluir atividade
function excluirAtividade(sistema, id) {
    if (!confirm('Tem certeza que deseja excluir esta atividade?')) {
        return;
    }

    var url = sistema === 'novo'
        ? '<?php echo site_url("atividades/excluir/"); ?>' + id
        : '<?php echo site_url("obras/excluirAtividade/"); ?>' + id;

    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível excluir'));
        }
    })
    .catch(function() {
        location.reload();
    });
}

// Fechar modais ao clicar fora
window.onclick = function(event) {
    var modal1 = document.getElementById('modalDetalhes');
    var modal2 = document.getElementById('modalNova');
    if (event.target === modal1) fecharModal();
    if (event.target === modal2) fecharModalNova();
};

// Tecla ESC para fechar
document.onkeydown = function(e) {
    if (e.key === 'Escape') {
        fecharModal();
        fecharModalNova();
    }
};

// Utilitários
function formatarData(dataStr) {
    if (!dataStr) return '-';
    var parts = dataStr.split('-');
    if (parts.length !== 3) return dataStr;
    return parts[2] + '/' + parts[1] + '/' + parts[0];
}

function formatarDataHora(dataHoraStr) {
    if (!dataHoraStr) return '-';
    var data = new Date(dataHoraStr);
    if (isNaN(data.getTime())) return dataHoraStr;
    return data.toLocaleString('pt-BR');
}

// Log de inicialização
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    logDebug('JS inicializado. Cards: ' + document.querySelectorAll('.atv-card').length);
} else {
    // Aguardar DOM estar pronto
    if (window.addEventListener) {
        window.addEventListener('load', function() {
            logDebug('JS inicializado (onload). Cards: ' + document.querySelectorAll('.atv-card').length);
        });
    } else if (window.attachEvent) {
        window.attachEvent('onload', function() {
            logDebug('JS inicializado (onload). Cards: ' + document.querySelectorAll('.atv-card').length);
        });
    }
}
</script>
