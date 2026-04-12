<?php
/**
 * Checklist Model
 * Gerenciamento de checklists técnicos
 */

class Checklist_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obter templates de checklist disponíveis
     */
    public function getTemplates($categoria = null, $ativo = true)
    {
        if ($ativo) {
            $this->db->where('ativo', 1);
        }
        if ($categoria) {
            $this->db->where('categoria', $categoria);
        }
        $this->db->order_by('nome', 'ASC');
        return $this->db->get('checklist_templates')->result();
    }

    /**
     * Obter template por ID com seus itens
     */
    public function getTemplateById($template_id)
    {
        $this->db->where('id', $template_id);
        $template = $this->db->get('checklist_templates')->row();

        if ($template) {
            $this->db->where('template_id', $template_id);
            $this->db->order_by('ordem', 'ASC');
            $template->itens = $this->db->get('checklist_template_items')->result();
        }

        return $template;
    }

    /**
     * Criar checklist para uma OS a partir de um template
     */
    public function criarChecklistOs($os_id, $template_id = null, $custom_items = [])
    {
        // Se tem template, copia os itens
        if ($template_id) {
            $template = $this->getTemplateById($template_id);
            if ($template && !empty($template->itens)) {
                foreach ($template->itens as $item) {
                    $this->db->insert('os_checklist', [
                        'os_id' => $os_id,
                        'template_id' => $template_id,
                        'item_id' => $item->id,
                        'descricao' => $item->descricao,
                        'status' => 'pendente',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
                return true;
            }
        }

        // Se não tem template ou template vazio, usa itens customizados
        if (!empty($custom_items)) {
            foreach ($custom_items as $item) {
                $this->db->insert('os_checklist', [
                    'os_id' => $os_id,
                    'descricao' => $item['descricao'],
                    'status' => 'pendente',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            return true;
        }

        return false;
    }

    /**
     * Obter checklist de uma OS
     */
    public function getChecklistOs($os_id, $status = null)
    {
        $this->db->where('os_id', $os_id);
        if ($status) {
            $this->db->where('status', $status);
        }
        $this->db->order_by('id', 'ASC');
        return $this->db->get('os_checklist')->result();
    }

    /**
     * Atualizar status de um item do checklist
     */
    public function atualizarItem($item_id, $status, $observacao = null, $usuario_id = null, $foto = null)
    {
        $data = [
            'status' => $status,
            'observacao' => $observacao,
            'verificado_por' => $usuario_id,
            'verificado_at' => date('Y-m-d H:i:s')
        ];

        if ($foto) {
            $data['evidencia_foto'] = $foto;
        }

        $this->db->where('id', $item_id);
        return $this->db->update('os_checklist', $data);
    }

    /**
     * Adicionar item avulso ao checklist
     */
    public function adicionarItem($os_id, $descricao, $obrigatorio = false)
    {
        return $this->db->insert('os_checklist', [
            'os_id' => $os_id,
            'descricao' => $descricao,
            'status' => 'pendente',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Remover item do checklist
     */
    public function removerItem($item_id)
    {
        $this->db->where('id', $item_id);
        return $this->db->delete('os_checklist');
    }

    /**
     * Verificar se checklist está completo
     */
    public function isChecklistCompleto($os_id)
    {
        $this->db->where('os_id', $os_id);
        $this->db->where_in('status', ['pendente', 'com_problema']);
        $pendentes = $this->db->count_all_results('os_checklist');

        return $pendentes === 0;
    }

    /**
     * Obter estatísticas do checklist
     */
    public function getEstatisticas($os_id)
    {
        $this->db->select('status, COUNT(*) as total');
        $this->db->where('os_id', $os_id);
        $this->db->group_by('status');
        $result = $this->db->get('os_checklist')->result();

        $stats = [
            'total' => 0,
            'pendente' => 0,
            'ok' => 0,
            'nao_aplicavel' => 0,
            'com_problema' => 0,
            'percentual_concluido' => 0
        ];

        foreach ($result as $row) {
            $stats[$row->status] = (int) $row->total;
            $stats['total'] += $row->total;
        }

        if ($stats['total'] > 0) {
            $stats['percentual_concluido'] = round(
                (($stats['ok'] + $stats['nao_aplicavel']) / $stats['total']) * 100,
                1
            );
        }

        return $stats;
    }

    // ==================== TIMELINE ====================

    /**
     * Adicionar entrada na timeline
     */
    public function adicionarTimeline($os_id, $tipo, $titulo, $descricao = null, $usuario_id = null, $metadata = null)
    {
        $usuario_nome = null;
        if ($usuario_id) {
            $this->db->select('nome');
            $this->db->where('idUsuarios', $usuario_id);
            $user = $this->db->get('usuarios')->row();
            $usuario_nome = $user ? $user->nome : null;
        }

        return $this->db->insert('os_timeline', [
            'os_id' => $os_id,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'descricao' => $descricao,
            'usuario_id' => $usuario_id,
            'usuario_nome' => $usuario_nome,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Obter timeline da OS
     */
    public function getTimeline($os_id, $limite = 50)
    {
        $this->db->where('os_id', $os_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limite);
        $result = $this->db->get('os_timeline')->result();

        foreach ($result as $item) {
            if ($item->metadata) {
                $item->metadata = json_decode($item->metadata);
            }
        }

        return $result;
    }

    // ==================== PEÇAS/INSUMOS ====================

    /**
     * Adicionar peça utilizada
     */
    public function adicionarPeca($dados)
    {
        // Calcular valor total
        if (!isset($dados['valor_total']) && isset($dados['valor_unitario']) && isset($dados['quantidade'])) {
            $dados['valor_total'] = $dados['valor_unitario'] * $dados['quantidade'];
        }

        return $this->db->insert('os_pecas_utilizadas', $dados);
    }

    /**
     * Obter peças utilizadas na OS
     */
    public function getPecasUtilizadas($os_id)
    {
        $this->db->where('os_id', $os_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('os_pecas_utilizadas')->result();
    }

    /**
     * Remover peça
     */
    public function removerPeca($peca_id)
    {
        $this->db->where('id', $peca_id);
        return $this->db->delete('os_pecas_utilizadas');
    }

    /**
     * Marcar peça como instalada
     */
    public function marcarInstalada($peca_id, $tecnico_id)
    {
        $this->db->where('id', $peca_id);
        return $this->db->update('os_pecas_utilizadas', [
            'instalado_por' => $tecnico_id,
            'instalado_at' => date('Y-m-d H:i:s')
        ]);
    }

    // ==================== ETAPAS ====================

    /**
     * Criar etapas padrão para uma OS
     */
    public function criarEtapasPadrao($os_id)
    {
        $etapas = [
            ['etapa' => 'diagnostico', 'ordem' => 1, 'tempo_estimado' => 30],
            ['etapa' => 'orcamento', 'ordem' => 2, 'tempo_estimado' => 20],
            ['etapa' => 'aprovacao', 'ordem' => 3, 'tempo_estimado' => null],
            ['etapa' => 'reparo', 'ordem' => 4, 'tempo_estimado' => 60],
            ['etapa' => 'testes', 'ordem' => 5, 'tempo_estimado' => 15],
            ['etapa' => 'entrega', 'ordem' => 6, 'tempo_estimado' => 10]
        ];

        foreach ($etapas as $etapa) {
            $this->db->insert('os_etapas', [
                'os_id' => $os_id,
                'etapa' => $etapa['etapa'],
                'ordem' => $etapa['ordem'],
                'tempo_estimado_minutos' => $etapa['tempo_estimado'],
                'status' => 'pendente'
            ]);
        }

        return true;
    }

    /**
     * Obter etapas da OS
     */
    public function getEtapas($os_id)
    {
        $this->db->where('os_id', $os_id);
        $this->db->order_by('ordem', 'ASC');
        return $this->db->get('os_etapas')->result();
    }

    /**
     * Iniciar etapa
     */
    public function iniciarEtapa($etapa_id, $tecnico_id)
    {
        $this->db->where('id', $etapa_id);
        return $this->db->update('os_etapas', [
            'status' => 'em_andamento',
            'iniciado_at' => date('Y-m-d H:i:s'),
            'responsavel_id' => $tecnico_id
        ]);
    }

    /**
     * Concluir etapa
     */
    public function concluirEtapa($etapa_id)
    {
        $this->db->where('id', $etapa_id);
        $etapa = $this->db->get('os_etapas')->row();

        if ($etapa && $etapa->iniciado_at) {
            $tempo_real = (strtotime(date('Y-m-d H:i:s')) - strtotime($etapa->iniciado_at)) / 60;

            $this->db->where('id', $etapa_id);
            return $this->db->update('os_etapas', [
                'status' => 'concluida',
                'concluido_at' => date('Y-m-d H:i:s'),
                'tempo_real_minutos' => round($tempo_real)
            ]);
        }

        return false;
    }

    // ==================== COMPETÊNCIAS ====================

    /**
     * Obter competências de um técnico
     */
    public function getCompetenciasTecnico($usuario_id)
    {
        $this->db->where('usuario_id', $usuario_id);
        return $this->db->get('tecnico_competencias')->result();
    }

    /**
     * Adicionar competência ao técnico
     */
    public function adicionarCompetencia($usuario_id, $competencia, $nivel = 'basico')
    {
        return $this->db->insert('tecnico_competencias', [
            'usuario_id' => $usuario_id,
            'competencia' => $competencia,
            'nivel' => $nivel,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
