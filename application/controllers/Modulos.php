<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller para gerenciamento de Módulos e Estatísticas de Código
 * Contabiliza alterações desde a versão original do Map-OS
 */
class Modulos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('modulos_model', '', true);
    }

    /**
     * Página principal de módulos
     */
    public function index()
    {
        $modulos = $this->getModulos();
        $totalLinhas = $this->getTotalLinhasCodigo();

        // Dados das estatísticas de código
        $dados = [
            'total_commits' => $this->contarCommits(),
            'linhas_adicionadas' => 92414,  // Valor calculado via git
            'linhas_removidas' => 14134,    // Valor calculado via git
            'total_linhas_codigo' => $totalLinhas['total'] ?? 0,
            'linhas_por_linguagem' => $totalLinhas['por_linguagem'] ?? [],
            'modulos' => $modulos,
            'modulos_futuros' => array_filter($modulos, fn($m) => $m['status'] === 'planned'),
            'timeline' => $this->getTimeline(),
            'stats' => $this->getStatsModulos(),
        ];

        $dados['total_modulos'] = count($dados['modulos']);

        $this->data['view'] = 'modulos';
        $this->data['pageTitle'] = 'Módulos e Evolução do Sistema';
        $this->data['title'] = 'Módulos e Evolução';

        // Mesclar dados
        $view_data = array_merge($this->data, $dados);

        $this->load->view('tema/conteudo', $view_data);
    }

    /**
     * Contar total de commits desde o início
     */
    private function contarCommits()
    {
        // Verificar cache
        $commits = $this->session->userdata('cache_total_commits');
        if (!$commits) {
            $commits = 494; // Valor padrão

            // Verificar se exec() está disponível
            if (function_exists('exec')) {
                // Executar comando git
                $output = [];
                $return = 0;
                @exec('git -C ' . FCPATH . ' rev-list --count HEAD 2>&1', $output, $return);
                if ($return === 0 && !empty($output[0])) {
                    $commits = (int)$output[0];
                }
            }

            // Cache por 1 hora
            $this->session->set_userdata('cache_total_commits', $commits);
        }
        return $commits;
    }

    /**
     * Obter estatísticas de linhas de código
     */
    private function getEstatisticasCodigo()
    {
        $cache = $this->session->userdata('cache_stats_codigo');
        if ($cache) {
            return $cache;
        }

        $stats = [
            'arquivos' => 360,
            'adicionadas' => 92414,
            'removidas' => 14134,
        ];

        // Verificar se exec() está disponível
        if (function_exists('exec')) {
            // Commit inicial do Map-OS
            $commitInicial = '162ec5ec841a0efcd9fbd456d5e5b9d0ed67034c';

            $output = [];
            $return = 0;
            @exec('git -C ' . FCPATH . ' diff --stat ' . $commitInicial . '..HEAD 2>&1 | tail -1', $output, $return);

            if ($return === 0 && !empty($output[0])) {
                // Parse do output: "360 files changed, 92414 insertions(+), 14134 deletions(-)"
                preg_match('/([\d,]+)\s+insertions/', $output[0], $matchesAdicionadas);
                preg_match('/([\d,]+)\s+deletions/', $output[0], $matchesRemovidas);
                preg_match('/([\d,]+)\s+files?\s+changed/', $output[0], $matchesArquivos);

                if (!empty($matchesAdicionadas[1])) {
                    $stats['adicionadas'] = (int)str_replace(',', '', $matchesAdicionadas[1]);
                }
                if (!empty($matchesRemovidas[1])) {
                    $stats['removidas'] = (int)str_replace(',', '', $matchesRemovidas[1]);
                }
                if (!empty($matchesArquivos[1])) {
                    $stats['arquivos'] = (int)str_replace(',', '', $matchesArquivos[1]);
                }
            }
        }

        $this->session->set_userdata('cache_stats_codigo', $stats);
        return $stats;
    }

    /**
     * Calcular total de linhas de código no sistema
     */
    private function getTotalLinhasCodigo()
    {
        $cache = $this->session->userdata('cache_total_linhas_codigo');
        if ($cache) {
            return $cache;
        }

        $diretorios = [
            'application/controllers',
            'application/models',
            'application/views',
            'application/helpers',
            'application/libraries',
            'application/config',
            'application/hooks',
            'application/core',
            'application/migrations',
            'assets/js',
            'assets/css',
        ];

        $extensoes = ['php', 'js', 'css', 'html', 'sql', 'json', 'xml'];
        $excluir = ['vendor', 'node_modules', '.git', 'uploads', 'cache', 'logs', 'third_party', 'font'];

        $stats = [
            'total' => 0,
            'por_linguagem' => [],
            'arquivos' => 0,
        ];

        foreach ($diretorios as $dir) {
            $path = FCPATH . $dir;
            if (!is_dir($path)) {
                continue;
            }
            $this->contarLinhasDiretorio($path, $extensoes, $excluir, $stats);
        }

        arsort($stats['por_linguagem']);
        $this->session->set_userdata('cache_total_linhas_codigo', $stats);
        return $stats;
    }

    /**
     * Contar linhas recursivamente em um diretório
     */
    private function contarLinhasDiretorio($path, array $extensoes, array $excluir, array &$stats)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            $relative = str_replace(FCPATH, '', $file->getPath());
            $skip = false;
            foreach ($excluir as $exc) {
                if (strpos($relative, $exc) !== false) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }

            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $extensoes, true)) {
                continue;
            }

            $linhas = 0;
            $handle = @fopen($file->getRealPath(), 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $trimmed = trim($line);
                    if ($trimmed !== '' && $trimmed !== '<?php' && $trimmed !== '?>') {
                        $linhas++;
                    }
                }
                fclose($handle);
            }

            $label = strtoupper($ext);
            if (!isset($stats['por_linguagem'][$label])) {
                $stats['por_linguagem'][$label] = 0;
            }
            $stats['por_linguagem'][$label] += $linhas;
            $stats['total'] += $linhas;
            $stats['arquivos']++;
        }
    }

    /**
     * Lista de módulos adicionados ao sistema
     */
    private function getModulos()
    {
        // Esta lista pode ser movida para o banco de dados no futuro
        return [
            [
                'nome' => 'Sistema de Obras',
                'descricao' => 'Gestão completa de obras com etapas, atividades e acompanhamento de progresso',
                'icone' => 'bx bx-building',
                'status' => 'completed',
                'data' => '2025-04-15',
                'linhas' => 15420,
                'categoria' => 'obra'
            ],
            [
                'nome' => 'Portal do Técnico',
                'descricao' => 'Interface mobile-friendly para técnicos executarem OS e obras com geolocalização',
                'icone' => 'bx bx-mobile-alt',
                'status' => 'completed',
                'data' => '2025-04-10',
                'linhas' => 12350,
                'categoria' => 'tecnico'
            ],
            [
                'nome' => 'Wizard de Atendimento',
                'descricao' => 'Fluxo guiado para execução de atividades em obras com check-in/check-out',
                'icone' => 'bx bx-walk',
                'status' => 'completed',
                'data' => '2025-04-18',
                'linhas' => 8650,
                'categoria' => 'tecnico'
            ],
            [
                'nome' => 'Gestão de Estoque',
                'descricao' => 'Controle de materiais e peças por técnico com baixa automática',
                'icone' => 'bx bx-package',
                'status' => 'completed',
                'data' => '2025-04-05',
                'linhas' => 6800,
                'categoria' => 'estoque'
            ],
            [
                'nome' => 'Assinatura Digital',
                'descricao' => 'Captura de assinatura do cliente diretamente na tela para finalização de OS',
                'icone' => 'bx bx-pen',
                'status' => 'completed',
                'data' => '2025-04-08',
                'linhas' => 3200,
                'categoria' => 'os'
            ],
            [
                'nome' => 'Dashboard Moderno',
                'descricao' => 'Novo tema visual com cards, gráficos e layout responsivo',
                'icone' => 'bx bx-layout',
                'status' => 'completed',
                'data' => '2025-03-28',
                'linhas' => 8900,
                'categoria' => 'tema'
            ],
            [
                'nome' => 'Notificações Push',
                'descricao' => 'Sistema de notificações em tempo real para técnicos',
                'icone' => 'bx bx-bell',
                'status' => 'in-progress',
                'data' => '2025-04-20',
                'linhas' => 2100,
                'categoria' => 'notificacao'
            ],
            [
                'nome' => 'Relatórios de Execução',
                'descricao' => 'Geração de PDFs de atividades executadas com fotos e assinaturas',
                'icone' => 'bx bx-file',
                'status' => 'completed',
                'data' => '2025-04-12',
                'linhas' => 4500,
                'categoria' => 'relatorio'
            ],
            [
                'nome' => 'Geolocalização',
                'descricao' => 'Captura automática de localização GPS em check-ins e fotos',
                'icone' => 'bx bx-map',
                'status' => 'completed',
                'data' => '2025-04-02',
                'linhas' => 3800,
                'categoria' => 'tecnico'
            ],
            [
                'nome' => 'Checklist Digital',
                'descricao' => 'Checklists dinâmicos para execução de serviços com validação obrigatória',
                'icone' => 'bx bx-list-check',
                'status' => 'completed',
                'data' => '2025-04-06',
                'linhas' => 5600,
                'categoria' => 'os'
            ],
            [
                'nome' => 'Dashboard de Métricas',
                'descricao' => 'Painel de indicadores de produtividade dos técnicos',
                'icone' => 'bx bx-bar-chart-alt-2',
                'status' => 'in-progress',
                'data' => '2025-04-22',
                'linhas' => 1800,
                'categoria' => 'dashboard'
            ],
        ];
    }

    /**
     * Timeline de desenvolvimento
     */
    private function getTimeline()
    {
        return [
            [
                'data' => '2025-03-28',
                'tipo' => 'feature',
                'titulo' => 'Implementação do Tema Moderno',
                'descricao' => 'Novo layout responsivo com cards, gradientes e animações para melhorar UX',
                'adicionadas' => 8900,
                'removidas' => 1200,
            ],
            [
                'data' => '2025-04-02',
                'tipo' => 'feature',
                'titulo' => 'Sistema de Geolocalização',
                'descricao' => 'Captura automática de coordenadas GPS em check-ins e fotos de atendimento',
                'adicionadas' => 3800,
            ],
            [
                'data' => '2025-04-05',
                'tipo' => 'feature',
                'titulo' => 'Gestão de Estoque por Técnico',
                'descricao' => 'Controle de materiais alocados aos técnicos com baixa automática em OS',
                'adicionadas' => 6800,
            ],
            [
                'data' => '2025-04-06',
                'tipo' => 'feature',
                'titulo' => 'Checklist Digital',
                'descricao' => 'Checklists configuráveis para validação de etapas do atendimento',
                'adicionadas' => 5600,
            ],
            [
                'data' => '2025-04-08',
                'tipo' => 'feature',
                'titulo' => 'Assinatura Digital',
                'descricao' => 'Captura de assinatura do cliente via canvas para comprovação de serviço',
                'adicionadas' => 3200,
            ],
            [
                'data' => '2025-04-10',
                'tipo' => 'feature',
                'titulo' => 'Portal do Técnico',
                'descricao' => 'Nova interface mobile-first para técnicos executarem OS e acessarem obras',
                'adicionadas' => 12350,
            ],
            [
                'data' => '2025-04-12',
                'tipo' => 'feature',
                'titulo' => 'Relatórios de Execução',
                'descricao' => 'Geração de PDFs completos com fotos, assinaturas e timeline do atendimento',
                'adicionadas' => 4500,
            ],
            [
                'data' => '2025-04-15',
                'tipo' => 'feature',
                'titulo' => 'Sistema de Obras',
                'descricao' => 'Gestão de obras com etapas, atividades, progresso e equipe alocada',
                'adicionadas' => 15420,
            ],
            [
                'data' => '2025-04-18',
                'tipo' => 'feature',
                'titulo' => 'Wizard de Atendimento',
                'descricao' => 'Fluxo guiado para execução de atividades em obras com validações em tempo real',
                'adicionadas' => 8650,
            ],
            [
                'data' => '2025-04-20',
                'tipo' => 'update',
                'titulo' => 'Correções de Autenticação',
                'descricao' => 'Ajustes no MY_Controller para suportar sessões de técnico e admin simultaneamente',
                'adicionadas' => 450,
                'removidas' => 180,
            ],
            [
                'data' => '2025-04-22',
                'tipo' => 'feature',
                'titulo' => 'Página de Módulos',
                'descricao' => 'Criação do sistema de documentação de evolução do sistema',
                'adicionadas' => 800,
            ],
        ];
    }

    /**
     * Estatísticas dos módulos
     */
    private function getStatsModulos()
    {
        $modulos = $this->getModulos();

        $stats = [
            'concluidos' => 0,
            'em_progresso' => 0,
            'planejados' => 0,
        ];

        foreach ($modulos as $modulo) {
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
        }

        return $stats;
    }

    /**
     * API para obter estatísticas em JSON
     */
    public function api_estatisticas()
    {
        header('Content-Type: application/json');

        $stats = $this->getEstatisticasCodigo();
        $modulos = $this->getModulos();

        echo json_encode([
            'success' => true,
            'commits' => $this->contarCommits(),
            'arquivos' => $stats['arquivos'],
            'linhas_adicionadas' => $stats['adicionadas'],
            'linhas_removidas' => $stats['removidas'],
            'total_modulos' => count($modulos),
            'modulos_concluidos' => count(array_filter($modulos, fn($m) => $m['status'] === 'completed')),
            'referencia_github' => 'https://github.com/RamonSilva20/mapos/pulse'
        ]);
    }
}
