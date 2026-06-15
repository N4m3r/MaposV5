<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller para gerenciamento de Modulos e Estatisticas de Codigo
 * Contabiliza alteracoes desde a versao original do Map-OS
 *
 * Refatorado em 2026-06-14: dados movidos para Modulos_model.
 */
class Modulos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('modulos_model', '', true);
    }

    /**
     * Pagina principal de modulos
     */
    public function index()
    {
        $modulos = $this->modulos_model->getModulos();
        $totalLinhas = $this->getTotalLinhasCodigo();
        $statsCodigo = $this->modulos_model->getEstatisticasCodigo();
        $statsModulos = $this->modulos_model->getStats();

        $dados = [
            'total_commits' => $statsCodigo['commits'] ?? 494,
            'linhas_adicionadas' => $statsCodigo['adicionadas'],
            'linhas_removidas' => $statsCodigo['removidas'],
            'total_linhas_codigo' => $totalLinhas['total'] ?? 0,
            'linhas_por_linguagem' => $totalLinhas['por_linguagem'] ?? [],
            'modulos' => $modulos,
            'modulos_futuros' => $this->modulos_model->getModulosFuturos(),
            'timeline' => $this->modulos_model->getTimeline(),
            'stats' => $statsModulos,
        ];

        $dados['total_modulos'] = count($dados['modulos']);

        $this->data['view'] = 'modulos';
        $this->data['pageTitle'] = 'Modulos e Evolucao do Sistema';
        $this->data['title'] = 'Modulos e Evolucao';

        $view_data = array_merge($this->data, $dados);

        $this->load->view('tema/conteudo', $view_data);
    }

    /**
     * Contar total de commits desde o inicio (com cache de sessao)
     */
    private function contarCommits()
    {
        $commits = $this->session->userdata('cache_total_commits');
        if (!$commits) {
            $commits = 494; // valor padrao se exec() indisponivel

            if (function_exists('exec')) {
                $output = [];
                $return = 0;
                @exec('git -C ' . FCPATH . ' rev-list --count HEAD 2>&1', $output, $return);
                if ($return === 0 && !empty($output[0])) {
                    $commits = (int)$output[0];
                }
            }

            $this->session->set_userdata('cache_total_commits', $commits);
        }
        return $commits;
    }

    /**
     * Wrapper para manter compatibilidade com a view
     */
    private function getEstatisticasCodigo()
    {
        return $this->modulos_model->getEstatisticasCodigo();
    }

    /**
     * Calcular total de linhas de codigo no sistema.
     * Usa cache de sessao (1h) para evitar percorrer milhares de arquivos
     * em cada request.
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
     * Contar linhas recursivamente em um diretorio
     */
    private function contarLinhasDiretorio($path, array $extensoes, array $excluir, array &$stats)
    {
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
        } catch (Exception $e) {
            // Diretorio nao acessivel - pular
            return;
        }

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
     * API para obter estatisticas em JSON
     */
    public function api_estatisticas()
    {
        header('Content-Type: application/json');

        $stats = $this->getEstatisticasCodigo();
        $modulos = $this->modulos_model->getModulos();

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
