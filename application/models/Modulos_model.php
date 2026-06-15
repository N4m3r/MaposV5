<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model para gerenciamento de Modulos e Estatísticas de Código
 *
 * Centraliza a lista de modulos do sistema, timeline de desenvolvimento
 * e estatisticas. O controller Modulos consulta este model ao inves de
 * manter a lista hardcoded.
 *
 * @since 2026-06-14 - dados movidos do controller para o model
 */
class Modulos_model extends CI_Model
{
    /**
     * Lista canonica de modulos adicionados ao sistema.
     * Atualizado manualmente a cada novo modulo implementado.
     */
    private $modulos = [
        [
            'nome' => 'Sistema de Obras',
            'descricao' => 'Gestao completa de obras com etapas, atividades e acompanhamento de progresso',
            'icone' => 'bx bx-building',
            'status' => 'completed',
            'data' => '2025-04-15',
            'linhas' => 15420,
            'categoria' => 'obra'
        ],
        [
            'nome' => 'Portal do Tecnico',
            'descricao' => 'Interface mobile-friendly para tecnicos executarem OS e obras com geolocalizacao',
            'icone' => 'bx bx-mobile-alt',
            'status' => 'completed',
            'data' => '2025-04-10',
            'linhas' => 12350,
            'categoria' => 'tecnico'
        ],
        [
            'nome' => 'Wizard de Atendimento',
            'descricao' => 'Fluxo guiado para execucao de atividades em obras com check-in/check-out',
            'icone' => 'bx bx-walk',
            'status' => 'completed',
            'data' => '2025-04-18',
            'linhas' => 8650,
            'categoria' => 'tecnico'
        ],
        [
            'nome' => 'Gestao de Estoque',
            'descricao' => 'Controle de materiais e pecas por tecnico com baixa automatica',
            'icone' => 'bx bx-package',
            'status' => 'completed',
            'data' => '2025-04-05',
            'linhas' => 6800,
            'categoria' => 'estoque'
        ],
        [
            'nome' => 'Assinatura Digital',
            'descricao' => 'Captura de assinatura do cliente diretamente na tela para finalizacao de OS',
            'icone' => 'bx bx-pen',
            'status' => 'completed',
            'data' => '2025-04-08',
            'linhas' => 3200,
            'categoria' => 'os'
        ],
        [
            'nome' => 'Dashboard Moderno',
            'descricao' => 'Novo tema visual com cards, graficos e layout responsivo',
            'icone' => 'bx bx-layout',
            'status' => 'completed',
            'data' => '2025-03-28',
            'linhas' => 8900,
            'categoria' => 'tema'
        ],
        [
            'nome' => 'Notificacoes Push',
            'descricao' => 'Sistema de notificacoes em tempo real para tecnicos',
            'icone' => 'bx bx-bell',
            'status' => 'in-progress',
            'data' => '2025-04-20',
            'linhas' => 2100,
            'categoria' => 'notificacao'
        ],
        [
            'nome' => 'Relatorios de Execucao',
            'descricao' => 'Geracao de PDFs de atividades executadas com fotos e assinaturas',
            'icone' => 'bx bx-file',
            'status' => 'completed',
            'data' => '2025-04-12',
            'linhas' => 4500,
            'categoria' => 'relatorio'
        ],
        [
            'nome' => 'Geolocalizacao',
            'descricao' => 'Captura automatica de localizacao GPS em check-ins e fotos',
            'icone' => 'bx bx-map',
            'status' => 'completed',
            'data' => '2025-04-02',
            'linhas' => 3800,
            'categoria' => 'tecnico'
        ],
        [
            'nome' => 'Checklist Digital',
            'descricao' => 'Checklists dinamicos para execucao de servicos com validacao obrigatoria',
            'icone' => 'bx bx-list-check',
            'status' => 'completed',
            'data' => '2025-04-06',
            'linhas' => 5600,
            'categoria' => 'os'
        ],
        [
            'nome' => 'Dashboard de Metricas',
            'descricao' => 'Painel de indicadores de produtividade dos tecnicos',
            'icone' => 'bx bx-bar-chart-alt-2',
            'status' => 'in-progress',
            'data' => '2025-04-22',
            'linhas' => 1800,
            'categoria' => 'dashboard'
        ],
    ];

    /**
     * Timeline canonica de desenvolvimento
     */
    private $timeline = [
        ['data' => '2025-03-28', 'tipo' => 'feature', 'titulo' => 'Implementacao do Tema Moderno', 'descricao' => 'Novo layout responsivo com cards, gradientes e animacoes para melhorar UX', 'adicionadas' => 8900, 'removidas' => 1200],
        ['data' => '2025-04-02', 'tipo' => 'feature', 'titulo' => 'Sistema de Geolocalizacao', 'descricao' => 'Captura automatica de coordenadas GPS em check-ins e fotos de atendimento', 'adicionadas' => 3800],
        ['data' => '2025-04-05', 'tipo' => 'feature', 'titulo' => 'Gestao de Estoque por Tecnico', 'descricao' => 'Controle de materiais alocados aos tecnicos com baixa automatica em OS', 'adicionadas' => 6800],
        ['data' => '2025-04-06', 'tipo' => 'feature', 'titulo' => 'Checklist Digital', 'descricao' => 'Checklists configuraveis para validacao de etapas do atendimento', 'adicionadas' => 5600],
        ['data' => '2025-04-08', 'tipo' => 'feature', 'titulo' => 'Assinatura Digital', 'descricao' => 'Captura de assinatura do cliente via canvas para comprovacao de servico', 'adicionadas' => 3200],
        ['data' => '2025-04-10', 'tipo' => 'feature', 'titulo' => 'Portal do Tecnico', 'descricao' => 'Nova interface mobile-first para tecnicos executarem OS e acessarem obras', 'adicionadas' => 12350],
        ['data' => '2025-04-12', 'tipo' => 'feature', 'titulo' => 'Relatorios de Execucao', 'descricao' => 'Geracao de PDFs completos com fotos, assinaturas e timeline do atendimento', 'adicionadas' => 4500],
        ['data' => '2025-04-15', 'tipo' => 'feature', 'titulo' => 'Sistema de Obras', 'descricao' => 'Gestao de obras com etapas, atividades, progresso e equipe alocada', 'adicionadas' => 15420],
        ['data' => '2025-04-18', 'tipo' => 'feature', 'titulo' => 'Wizard de Atendimento', 'descricao' => 'Fluxo guiado para execucao de atividades em obras com validacoes em tempo real', 'adicionadas' => 8650],
        ['data' => '2025-04-20', 'tipo' => 'update', 'titulo' => 'Correcoes de Autenticacao', 'descricao' => 'Ajustes no MY_Controller para suportar sessoes de tecnico e admin simultaneamente', 'adicionadas' => 450, 'removidas' => 180],
        ['data' => '2025-04-22', 'tipo' => 'feature', 'titulo' => 'Pagina de Modulos', 'descricao' => 'Criacao do sistema de documentacao de evolucao do sistema', 'adicionadas' => 800],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retorna lista de todos os modulos
     */
    public function getModulos()
    {
        return $this->modulos;
    }

    /**
     * Retorna apenas modulos futuros (planejados)
     */
    public function getModulosFuturos()
    {
        return array_filter($this->modulos, fn($m) => $m['status'] === 'planned');
    }

    /**
     * Retorna timeline de desenvolvimento
     */
    public function getTimeline()
    {
        return $this->timeline;
    }

    /**
     * Retorna estatisticas agregadas dos modulos
     */
    public function getStats()
    {
        $stats = [
            'concluidos' => 0,
            'em_progresso' => 0,
            'planejados' => 0,
            'total_linhas' => 0,
        ];

        foreach ($this->modulos as $modulo) {
            switch ($modulo['status']) {
                case 'completed':
                    $stats['concluidos']++;
                    break;
                case 'in-progress':
                    $stats['em_progresso']++;
                    break;
                case 'planned':
                    $stats['planejados']++;
                    break;
            }
            $stats['total_linhas'] += (int)($modulo['linhas'] ?? 0);
        }

        return $stats;
    }

    /**
     * Estatisticas de codigo obtidas via git (com fallback estatico).
     * Tenta usar `git rev-list --count` e `git diff --stat` quando exec() disponivel.
     * Em caso de falha ou falta de permissao, retorna valores conhecidos.
     */
    public function getEstatisticasCodigo($fallback = [])
    {
        $defaults = [
            'arquivos' => 360,
            'adicionadas' => 92414,
            'removidas' => 14134,
        ];
        $stats = array_merge($defaults, $fallback);

        if (!function_exists('exec')) {
            return $stats;
        }

        // Tentar contar commits
        $output = [];
        $return = 0;
        @exec('git -C ' . FCPATH . ' rev-list --count HEAD 2>&1', $output, $return);
        if ($return === 0 && !empty($output[0])) {
            $stats['commits'] = (int)$output[0];
        }

        // Tentar diff stats
        $output = [];
        $return = 0;
        $commitInicial = '162ec5ec841a0efcd9fbd456d5e5b9d0ed67034c';
        @exec('git -C ' . FCPATH . ' diff --stat ' . $commitInicial . '..HEAD 2>&1 | tail -1', $output, $return);

        if ($return === 0 && !empty($output[0])) {
            preg_match('/([\d,]+)\s+insertions/', $output[0], $mAdd);
            preg_match('/([\d,]+)\s+deletions/', $output[0], $mRem);
            preg_match('/([\d,]+)\s+files?\s+changed/', $output[0], $mFiles);

            if (!empty($mAdd[1])) $stats['adicionadas'] = (int)str_replace(',', '', $mAdd[1]);
            if (!empty($mRem[1])) $stats['removidas'] = (int)str_replace(',', '', $mRem[1]);
            if (!empty($mFiles[1])) $stats['arquivos'] = (int)str_replace(',', '', $mFiles[1]);
        }

        return $stats;
    }
}
