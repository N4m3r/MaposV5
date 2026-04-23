<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Grid de Atividades -->
<div class="atividades-grid" id="atividadesGrid">
    <?php
    // Mesclar atividades do sistema antigo e novo
    $todas_atividades = [];

    // Atividades do sistema antigo (agendadas)
    if (!empty($atividades)) {
        foreach ($atividades as $ativ) {
            $todas_atividades[] = [
                'id' => $ativ->id ?? $ativ->idAtividade ?? 0,
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
    }

    // Atividades do novo sistema (registradas com Hora Início/Fim)
    if (!empty($atividades_registradas)) {
        foreach ($atividades_registradas as $ativ) {
            // Determinar o status baseado nos dados
            $status = 'agendada';
            if (!empty($ativ->hora_fim)) {
                $status = 'concluida';
            } elseif (!empty($ativ->hora_inicio)) {
                $status = 'iniciada';
            }

            $todas_atividades[] = [
                'id' => $ativ->idAtividade ?? 0,
                'titulo' => $ativ->titulo ?? $ativ->tipo_atividade ?? 'Atividade Técnica',
                'descricao' => $ativ->descricao ?? '',
                'status' => $ativ->status ?? $status,
                'tipo' => $ativ->categoria ?? 'trabalho',
                'data' => date('Y-m-d', strtotime($ativ->hora_inicio ?? 'now')),
                'tecnico' => $ativ->nome_tecnico ?? 'Não atribuído',
                'etapa' => $ativ->etapa_nome ?? 'Geral',
                'progresso' => ($ativ->status == 'finalizada' && $ativ->concluida) ? 100 : 0,
                'hora_inicio' => $ativ->hora_inicio ?? null,
                'hora_fim' => $ativ->hora_fim ?? null,
                'duracao' => $ativ->duracao_minutos ?? null,
                'sistema' => 'novo'
            ];
        }
    }

    // Ordenar por data (mais recente primeiro)
    usort($todas_atividades, function($a, $b) {
        return strtotime($b['data']) - strtotime($a['data']);
    });

    if (!empty($todas_atividades)):
        foreach ($todas_atividades as $atividade):
            $status_class = $atividade['status'];
            $status_label = ucfirst($atividade['status']);
    ?>
    <div class="atividade-card <?php echo $status_class; ?>"
         data-titulo="<?php echo strtolower(str_replace('"', '', $atividade['titulo'])); ?>"
         data-status="<?php echo $atividade['status']; ?>"
         data-tipo="<?php echo $atividade['tipo']; ?>"
         data-sistema="<?php echo $atividade['sistema']; ?>">

        <div class="atividade-card-header">
            <div class="atividade-card-title-section">
                <div class="atividade-card-date">
                    <i class="bx bx-calendar"></i>
                    <?php echo date('d/m/Y', strtotime($atividade['data'])); ?>
                    <?php if ($atividade['sistema'] == 'novo' && !empty($atividade['hora_inicio'])): ?>
                        <span style="margin-left: 5px; color: #667eea;">
                            <i class="bx bx-time"></i>
                            <?php echo date('H:i', strtotime($atividade['hora_inicio'])); ?>
                            <?php if (!empty($atividade['hora_fim'])): ?>
                                - <?php echo date('H:i', strtotime($atividade['hora_fim'])); ?>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h3 class="atividade-card-title"><?php echo htmlspecialchars($atividade['titulo']); ?></h3>
            </div>
            <span class="atividade-status-badge <?php echo $status_class; ?>">
                <?php echo $status_label; ?>
            </span>
        </div>

        <div class="atividade-card-body">
            <?php if (!empty($atividade['descricao'])): ?>
            <div class="atividade-card-desc">
                <?php echo htmlspecialchars($atividade['descricao']); ?>
            </div>
            <?php endif; ?>

            <div class="atividade-card-meta">
                <div class="atividade-meta-item">
                    <i class="bx bx-user"></i>
                    <?php echo htmlspecialchars($atividade['tecnico']); ?>
                </div>
                <div class="atividade-meta-item">
                    <i class="bx bx-layer"></i>
                    <?php echo htmlspecialchars($atividade['etapa']); ?>
                </div>
                <?php if ($atividade['tipo']): ?>
                <div class="atividade-meta-item tipo-<?php echo $atividade['tipo']; ?>">
                    <i class="bx bx-wrench"></i>
                    <?php echo ucfirst($atividade['tipo']); ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($atividade['duracao'])): ?>
                <div class="atividade-meta-item">
                    <i class="bx bx-time"></i>
                    <?php
                    $horas = floor($atividade['duracao'] / 60);
                    $minutos = $atividade['duracao'] % 60;
                    echo $horas . 'h ' . $minutos . 'min';
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="atividade-card-footer">
            <div class="atividade-progress-section">
                <div class="atividade-progress-header">
                    <span class="atividade-progress-label">Progresso</span>
                    <span class="atividade-progress-value"><?php echo $atividade['progresso']; ?>%</span>
                </div>
                <div class="atividade-progress-bar">
                    <div class="atividade-progress-fill" style="width: <?php echo $atividade['progresso']; ?>%; background: <?php echo $atividade['progresso'] >= 100 ? '#27ae60' : ($atividade['progresso'] > 50 ? '#3498db' : '#f39c12'); ?>;"></div>
                </div>
            </div>

            <div class="atividade-card-actions">
                <a href="javascript:void(0)" class="atividade-card-btn atividade-card-btn-view" title="Ver detalhes"
                   onclick="verDetalhesAtividade(<?php echo $atividade['id']; ?>, '<?php echo $atividade['sistema']; ?>')">
                    <i class="bx bx-eye"></i>
                </a>
                <?php if ($this->session->userdata('permissao') == 1): ?>
                <a href="javascript:void(0)" class="atividade-card-btn atividade-card-btn-delete" title="Excluir"
                   onclick="excluirAtividade(<?php echo $atividade['id']; ?>, '<?php echo $atividade['sistema']; ?>')">
                    <i class="bx bx-trash"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($atividade['sistema'] == 'novo'): ?>
        <div class="atividade-visibilidade visivel" title="Sistema Novo - Registro de Tempo">
            <i class="bx bx-timer"></i>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

<?php else: ?>

    <!-- Empty State -->
    <div class="atividades-empty">
        <div class="atividades-empty-icon">
            <i class="bx bx-calendar-x"></i>
        </div>
        <h3>Nenhuma atividade encontrada</h3>
        <p>Esta obra ainda não possui atividades registradas.</p>
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
        <button onclick="$('#modalAdicionar').modal('show')" class="atividades-btn atividades-btn-primary">
            <i class="bx bx-plus"></i> Adicionar Primeira Atividade
        </button>
        <?php endif; ?>
    </div>

<?php endif; ?>
</div>

<script>
// Função para ver detalhes da atividade
function verDetalhesAtividade(id, sistema) {
    if (sistema === 'novo') {
        // Abrir em nova aba a visualização da atividade no sistema novo
        window.open('<?php echo site_url('atividades/detalhes/'); ?>' + id, '_blank');
    } else {
        // Para sistema antigo, mostrar alerta com informações básicas
        alert('Atividade do sistema antigo (ID: ' + id + ')');
    }
}

// Função para excluir atividade
function excluirAtividade(id, sistema) {
    if (confirm('Tem certeza que deseja excluir esta atividade?')) {
        var url = sistema === 'novo'
            ? '<?php echo site_url('atividades/excluir/'); ?>' + id
            : '<?php echo site_url('obras/excluirAtividade/'); ?>' + id;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Não foi possível excluir'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir atividade');
        });
    }
}
</script>
