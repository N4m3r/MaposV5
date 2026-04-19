<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Checkin extends MY_Controller
{
    public function __construct()
    {
        // Verifica se o método atual é de visualização pública (fotos/assinaturas)
        // Estes métodos podem ser acessados por técnicos logados (tec_id) sem permissão admin
        // Usa o URI segment para determinar o método
        $uri_path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $uri_segments = explode('/', trim($uri_path, '/'));

        // Procura pelo nome do controller e pega o próximo segmento (método)
        // Também verifica se o segmento contém 'checkin' (para URLs como index.php/checkin/...)
        $metodo_atual = '';
        foreach ($uri_segments as $index => $segment) {
            if (strpos($segment, 'checkin') !== false && isset($uri_segments[$index + 1])) {
                $metodo_atual = $uri_segments[$index + 1];
                break;
            }
        }

        $metodos_visualizacao_publica = ['verFotoDB', 'verAssinatura', 'verFoto'];

        if (!in_array($metodo_atual, $metodos_visualizacao_publica)) {
            // Para outros métodos, requer autenticação admin normal
            parent::__construct();
        } else {
            // Para métodos de visualização, chama apenas o construtor base CI_Controller
            // sem a verificação de login do MY_Controller
            CI_Controller::__construct();
            $this->load->library('session');
            $this->load->library('permission');
            $this->load->database();

            // Log para debug
            log_message('debug', 'Checkin: Método de visualização público detectado: ' . $metodo_atual);
        }

        $this->load->model('checkin_model');
        $this->load->model('assinaturas_model');
        $this->load->model('fotosatendimento_model');
        $this->load->model('os_model');
        $this->load->model('mapos_model');
        $this->load->model('tec_os_model');
        $this->load->helper('date');
    }

    /**
     * Obtém status do check-in de uma OS (JSON)
     */
    public function status()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        $os_id = $this->input->post('os_id');

        if (!$os_id) {
            echo json_encode(['success' => false, 'message' => 'OS não informada']);
            return;
        }

        // Verifica se existe check-in ativo
        $checkin = $this->checkin_model->getCheckinAtivo($os_id);

        // Log para debug
        if ($checkin) {
            log_info('Status checkin - OS: ' . $os_id . ', Checkin ativo ID: ' . $checkin->idCheckin);
        } else {
            // Verifica se existe checkin "preso" para informar no log
            $ultimo = $this->checkin_model->getUltimoCheckin($os_id);
            if ($ultimo) {
                log_info('Status checkin - OS: ' . $os_id . ', Último checkin: ' . $ultimo->idCheckin . ' Status: ' . ($ultimo->data_saida ? 'Finalizado' : 'Preso (sem data saída)'));
            } else {
                log_info('Status checkin - OS: ' . $os_id . ', Sem checkin');
            }
        }

        $assinaturas = $this->assinaturas_model->getByOs($os_id);
        $fotos = $this->fotosatendimento_model->getByOs($os_id);

        // Organiza assinaturas por tipo
        $assinaturas_formatadas = [];
        foreach ($assinaturas as $assinatura) {
            $assinaturas_formatadas[$assinatura->tipo] = $assinatura;
        }

        // Organiza fotos por etapa
        $fotos_formatadas = [
            'entrada' => [],
            'durante' => [],
            'saida' => []
        ];
        foreach ($fotos as $foto) {
            // Garante que a URL aponta para o endpoint correto se imagem está em base64
            if (!empty($foto->imagem_base64)) {
                $foto->url = base_url('index.php/checkin/verFotoDB/' . $foto->idFoto);
                $foto->url_visualizacao = $foto->url;
            }
            $fotos_formatadas[$foto->etapa][] = $foto;
        }

        echo json_encode([
            'success' => true,
            'checkin' => $checkin,
            'assinaturas' => $assinaturas_formatadas,
            'fotos' => $fotos_formatadas,
            'em_atendimento' => ($checkin !== null)
        ]);
    }

    /**
     * Inicia atendimento (check-in)
     */
    public function iniciar()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        // Verifica permissão - aceita permissão de OS geral OU permissão de técnico específica
        $permissao = $this->session->userdata('permissao');
        if (!$this->permission->checkPermission($permissao, 'eOs') &&
            !$this->permission->checkPermission($permissao, 'eTecnicoCheckin')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para iniciar atendimento.']);
            return;
        }

        // Lê dados POST
        $os_id = $this->input->post('os_id');
        $observacao = $this->input->post('observacao');
        // Usar FALSE no terceiro parametro para evitar XSS filter que corrompe base64
        $assinatura_tecnico = $this->input->post('assinatura', false);
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');

        // Log para debug
        log_info('Checkin recebido - OS: ' . $os_id);
        log_info('Checkin recebido - Assinatura tamanho: ' . strlen($assinatura_tecnico));
        log_info('Checkin recebido - Primeiros 100 chars: ' . substr($assinatura_tecnico, 0, 100));

        if (!$os_id) {
            echo json_encode(['success' => false, 'message' => 'OS não informada']);
            return;
        }

        // Dados do usuário logado
        $usuario_id = $this->session->userdata('id_admin');

        // Se é técnico (tem permissão específica mas não permissão geral de OS),
        // verificar se a OS está atribuída a ele
        if (!$this->permission->checkPermission($permissao, 'eOs') &&
            $this->permission->checkPermission($permissao, 'eTecnicoCheckin')) {
            $this->load->model('tecnico_model');
            $os = $this->tecnico_model->getOsById($os_id, $usuario_id);
            if (!$os) {
                echo json_encode(['success' => false, 'message' => 'OS não encontrada ou não está designada a você']);
                return;
            }
        }

        // Verifica se já existe check-in ativo
        if ($this->checkin_model->hasCheckinAtivo($os_id)) {
            echo json_encode(['success' => false, 'message' => 'Já existe um atendimento em andamento para esta OS']);
            return;
        }

        // Verifica se existe check-in "preso" (antigo sem data de saída)
        $ultimo_checkin = $this->checkin_model->getUltimoCheckin($os_id);
        if ($ultimo_checkin && empty($ultimo_checkin->data_saida)) {
            // Auto-finaliza checkins presos com mais de 24 horas
            if (strtotime($ultimo_checkin->data_entrada) < strtotime('-24 hours')) {
                $this->checkin_model->finalizarAtendimento($ultimo_checkin->idCheckin, [
                    'data_saida' => date('Y-m-d H:i:s'),
                    'observacao_saida' => 'Finalizado automaticamente (atendimento anterior não concluído)',
                    'status' => 'Finalizado'
                ]);
                log_info('Auto-finalizado checkin preso da OS: ' . $os_id);
            } else {
                echo json_encode(['success' => false, 'message' => 'Existe um atendimento iniciado recentemente. Finalize-o ou aguarde 24h para iniciar um novo.']);
                return;
            }
        }

        // Dados do check-in
        $data_checkin = [
            'os_id' => $os_id,
            'usuarios_id' => $usuario_id,
            'data_entrada' => date('Y-m-d H:i:s'),
            'latitude_entrada' => $latitude ?: null,
            'longitude_entrada' => $longitude ?: null,
            'observacao_entrada' => $observacao,
            'status' => 'Em Andamento'
        ];
        log_info('Checkin iniciar - Dados checkin: ' . print_r($data_checkin, true));

        // Insere check-in
        // Insere check-in
        $checkin_id = $this->checkin_model->add($data_checkin, true);

        if (!$checkin_id) {
            echo json_encode(['success' => false, 'message' => 'Erro ao iniciar atendimento']);
            return;
        }

        // Salva assinatura do técnico
        if ($assinatura_tecnico) {
            log_info('Checkin - Iniciando salvamento da imagem. Tamanho: ' . strlen($assinatura_tecnico));
            $imagem = $this->assinaturas_model->salvarImagem($assinatura_tecnico, $os_id, 'tecnico_entrada');
            log_info('Checkin - Imagem salva: ' . print_r($imagem, true));
            if ($imagem) {
                $data_assinatura = [
                    'os_id' => $os_id,
                    'checkin_id' => $checkin_id,
                    'tipo' => 'tecnico_entrada',
                    'assinatura' => $imagem['path'],
                    'nome_assinante' => $this->session->userdata('nome'),
                    'data_assinatura' => date('Y-m-d H:i:s'),
                    'ip_address' => $this->input->ip_address()
                ];
                $result = $this->assinaturas_model->add($data_assinatura);
                log_info('Checkin - Assinatura salva no BD: ' . ($result ? 'Sim' : 'Não'));
            } else {
                log_info('Checkin - Erro ao salvar imagem da assinatura');
            }
        } else {
            log_info('Checkin - Nenhuma assinatura do técnico recebida');
        }

        // Atualiza status da OS para "Em Andamento"
        $this->os_model->edit('os', ['status' => 'Em Andamento'], 'idOs', $os_id);

        // Cria execução no portal do técnico (integração)
        $dados_execucao = [
            'os_id' => $os_id,
            'tecnico_id' => $usuario_id,
            'checkin_horario' => $data_checkin['data_entrada'],
            'checkin_latitude' => $data_checkin['latitude_entrada'],
            'checkin_longitude' => $data_checkin['longitude_entrada'],
            'tipo_servico' => 'Manutenção',
            'status' => 'em_execucao'
        ];
        $this->tec_os_model->iniciarExecucao($dados_execucao);
        log_info('Execução no portal do técnico iniciada para OS: ' . $os_id);

        // Log
        log_info('Iniciou atendimento da OS. ID: ' . $os_id);

        echo json_encode([
            'success' => true,
            'message' => 'Atendimento iniciado com sucesso',
            'checkin_id' => $checkin_id
        ]);
    }

    /**
     * Finaliza atendimento (check-out)
     */
    public function finalizar()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        // Verifica permissão - aceita permissão de OS geral OU permissão de técnico específica
        $permissao = $this->session->userdata('permissao');
        if (!$this->permission->checkPermission($permissao, 'eOs') &&
            !$this->permission->checkPermission($permissao, 'eTecnicoCheckout')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para finalizar atendimento.']);
            return;
        }

        // Lê dados POST (FormData) - usar FALSE no terceiro parametro para evitar XSS filter
        $os_id = $this->input->post('os_id');
        $observacao = $this->input->post('observacao');
        $assinatura_tecnico = $this->input->post('assinatura_tecnico', false);
        $assinatura_cliente = $this->input->post('assinatura_cliente', false);

        // Log para debug das assinaturas recebidas
        log_info('Checkin::finalizar - Assinatura técnico recebida (primeiros 100 chars): ' . substr($assinatura_tecnico ?? '', 0, 100));
        log_info('Checkin::finalizar - Assinatura cliente recebida (primeiros 100 chars): ' . substr($assinatura_cliente ?? '', 0, 100));
        $nome_cliente = $this->input->post('nome_cliente');
        $documento_cliente = $this->input->post('documento_cliente');
        $fotos = $this->input->post('fotos');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');

        if (!$os_id) {
            echo json_encode(['success' => false, 'message' => 'OS não informada']);
            return;
        }

        // Dados do usuário logado
        $usuario_id = $this->session->userdata('id_admin');

        // Se é técnico (tem permissão específica mas não permissão geral de OS),
        // verificar se a OS está atribuída a ele
        if (!$this->permission->checkPermission($permissao, 'eOs') &&
            $this->permission->checkPermission($permissao, 'eTecnicoCheckout')) {
            $this->load->model('tecnico_model');
            $os = $this->tecnico_model->getOsById($os_id, $usuario_id);
            if (!$os) {
                echo json_encode(['success' => false, 'message' => 'OS não encontrada ou não está designada a você']);
                return;
            }
        }

        // Obtém check-in ativo (com auto-finalização de checkins expirados)
        $checkin = $this->checkin_model->getCheckinAtivoComAutoFinalizacao($os_id);

        if (!$checkin) {
            // Verifica se existe checkin mais antigo sem data de saída ("preso")
            $ultimo_checkin = $this->checkin_model->getUltimoCheckin($os_id);
            if ($ultimo_checkin && empty($ultimo_checkin->data_saida)) {
                // Se chegou aqui, o checkin tem menos de 24h (senão teria sido auto-finalizado)
                if (strtotime($ultimo_checkin->data_entrada) > strtotime('-24 hours')) {
                    $checkin = $ultimo_checkin;
                    log_info('Usando checkin preso para finalização da OS: ' . $os_id);
                } else {
                    // Isso não deveria acontecer devido à auto-finalização, mas mantemos por segurança
                    echo json_encode(['success' => false, 'message' => 'O atendimento anterior expirou (mais de 24h). Inicie um novo atendimento.']);
                    return;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Não existe atendimento em andamento para esta OS. Verifique se já foi finalizado.']);
                return;
            }
        }

        // Dados do check-out
        $data_checkout = [
            'data_saida' => date('Y-m-d H:i:s'),
            'latitude_saida' => $latitude ?: null,
            'longitude_saida' => $longitude ?: null,
            'observacao_saida' => $observacao,
            'status' => 'Finalizado',
            'data_atualizacao' => date('Y-m-d H:i:s')
        ];

        // Atualiza check-in
        $resultado = $this->checkin_model->finalizarAtendimento($checkin->idCheckin, $data_checkout);

        if (!$resultado) {
            echo json_encode(['success' => false, 'message' => 'Erro ao finalizar atendimento']);
            return;
        }

        // Salva assinatura do técnico na saída
        if ($assinatura_tecnico) {
            $imagem = $this->assinaturas_model->salvarImagem($assinatura_tecnico, $os_id, 'tecnico_saida');
            log_info('Checkout - Imagem técnico salva: ' . print_r($imagem, true));

            if ($imagem) {
                $data_assinatura = [
                    'os_id' => $os_id,
                    'checkin_id' => $checkin->idCheckin,
                    'tipo' => 'tecnico_saida',
                    'assinatura' => $imagem['path'],
                    'nome_assinante' => $this->session->userdata('nome'),
                    'data_assinatura' => date('Y-m-d H:i:s'),
                    'ip_address' => $this->input->ip_address()
                ];
                $result = $this->assinaturas_model->add($data_assinatura);
                log_info('Checkout - Assinatura técnico salva no BD: ' . ($result ? 'Sim' : 'Não'));
            }
        }

        // Salva assinatura do cliente
        if ($assinatura_cliente) {
            $imagem = $this->assinaturas_model->salvarImagem($assinatura_cliente, $os_id, 'cliente_saida');
            log_info('Checkout - Imagem cliente salva: ' . print_r($imagem, true));

            if ($imagem) {
                $data_assinatura = [
                    'os_id' => $os_id,
                    'checkin_id' => $checkin->idCheckin,
                    'tipo' => 'cliente_saida',
                    'assinatura' => $imagem['path'],
                    'nome_assinante' => $nome_cliente,
                    'documento_assinante' => $documento_cliente,
                    'data_assinatura' => date('Y-m-d H:i:s'),
                    'ip_address' => $this->input->ip_address()
                ];
                $result = $this->assinaturas_model->add($data_assinatura);
                log_info('Checkout - Assinatura cliente salva no BD: ' . ($result ? 'Sim' : 'Não'));
            }
        }

        // Salva fotos de saída
        if ($fotos && is_array($fotos)) {
            foreach ($fotos as $foto_base64) {
                $resultado = $this->fotosatendimento_model->salvarFotoBase64(
                    $foto_base64,
                    $os_id,
                    $usuario_id,
                    $checkin->idCheckin,
                    'saida'
                );

                if (!isset($resultado['error'])) {
                    $data_foto = [
                        'os_id' => $os_id,
                        'checkin_id' => $checkin->idCheckin,
                        'usuarios_id' => $usuario_id,
                        'arquivo' => $resultado['arquivo'],
                        'path' => $resultado['path'],
                        'url' => $resultado['url'],
                        'etapa' => 'saida',
                        'tamanho' => $resultado['tamanho'],
                        'tipo_arquivo' => $resultado['tipo'],
                        'imagem_base64' => $resultado['imagem_base64'],
                        'mime_type' => $resultado['mime_type']
                    ];
                    $this->fotosatendimento_model->add($data_foto);
                }
            }
        }

        // Atualiza status da OS para "Finalizado"
        $this->os_model->edit('os', ['status' => 'Finalizado'], 'idOs', $os_id);

        // Finaliza execução no portal do técnico (integração)
        $execucao_atual = $this->tec_os_model->getExecucaoAtual($os_id, $usuario_id);
        if ($execucao_atual) {
            $data_checkin_dt = new DateTime($execucao_atual->checkin_horario);
            $data_checkout_dt = new DateTime();
            $intervalo = $data_checkin_dt->diff($data_checkout_dt);
            $tempo_total_minutos = ($intervalo->h * 60) + $intervalo->i + ($intervalo->days * 24 * 60);

            $dados_finalizacao = [
                'checkout_horario' => date('Y-m-d H:i:s'),
                'checkout_latitude' => $latitude ?: null,
                'checkout_longitude' => $longitude ?: null,
                'tempo_atendimento_minutos' => $tempo_total_minutos,
                'checklist_completude' => 100,
                'status' => 'concluida'
            ];
            $this->tec_os_model->finalizarExecucao($execucao_atual->id, $dados_finalizacao);
            log_info('Execução no portal do técnico finalizada para OS: ' . $os_id);
        }

        // Log
        log_info('Finalizou atendimento da OS. ID: ' . $os_id);

        echo json_encode([
            'success' => true,
            'message' => 'Atendimento finalizado com sucesso'
        ]);
    }

    /**
     * Adiciona fotos durante o atendimento
     */
    public function adicionarFoto()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        // Aumenta o limite de memória para processar imagens grandes
        ini_set('memory_limit', '256M');

        // Verifica permissão - aceita permissão de OS geral OU permissão de técnico para fotos
        $permissao = $this->session->userdata('permissao');
        if (!$this->permission->checkPermission($permissao, 'eOs') &&
            !$this->permission->checkPermission($permissao, 'eTecnicoFotos')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão.']);
            return;
        }

        $os_id = $this->input->post('os_id');
        $checkin_id = $this->input->post('checkin_id');
        // Usar FALSE para evitar XSS filter que corrompe base64
        $foto_base64 = $this->input->post('foto', false);
        $descricao = $this->input->post('descricao');

        if (!$os_id || !$foto_base64) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }

        // Log para debug
        log_info('Checkin::adicionarFoto - OS: ' . $os_id . ', Tamanho dados: ' . strlen($foto_base64));

        // Se é técnico, verificar se a OS está atribuída a ele
        if (!$this->permission->checkPermission($permissao, 'eOs') &&
            $this->permission->checkPermission($permissao, 'eTecnicoFotos')) {
            $this->load->model('tecnico_model');
            $os = $this->tecnico_model->getOsById($os_id, $this->session->userdata('id_admin'));
            if (!$os) {
                echo json_encode(['success' => false, 'message' => 'OS não encontrada ou não está designada a você']);
                return;
            }
        }

        $usuario_id = $this->session->userdata('id_admin');

        // Salva foto
        $resultado = $this->fotosatendimento_model->salvarFotoBase64(
            $foto_base64,
            $os_id,
            $usuario_id,
            $checkin_id,
            'durante',
            $descricao
        );

        if (isset($resultado['error'])) {
            log_info('Checkin::adicionarFoto - Erro: ' . $resultado['error']);
            echo json_encode(['success' => false, 'message' => $resultado['error']]);
            return;
        }

        log_info('Checkin::adicionarFoto - Imagem salva: ' . $resultado['arquivo']);

        $data_foto = [
            'os_id' => $os_id,
            'checkin_id' => $checkin_id,
            'usuarios_id' => $usuario_id,
            'arquivo' => $resultado['arquivo'],
            'path' => $resultado['path'],
            'url' => $resultado['url'],
            'descricao' => $descricao,
            'etapa' => 'durante',
            'tamanho' => $resultado['tamanho'],
            'tipo_arquivo' => $resultado['tipo'],
            'imagem_base64' => $resultado['imagem_base64'],
            'mime_type' => $resultado['mime_type']
        ];

        $foto_id = $this->fotosatendimento_model->add($data_foto, true);

        if ($foto_id) {
            echo json_encode([
                'success' => true,
                'message' => 'Foto adicionada com sucesso',
                'foto_id' => $foto_id,
                'url' => base_url('index.php/checkin/verFotoDB/' . $foto_id)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar foto']);
        }
    }

    /**
     * Remove uma foto
     */
    public function removerFoto()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        // Verifica permissão - aceita permissão de OS geral OU permissão de técnico para fotos
        $permissao = $this->session->userdata('permissao');
        if (!$this->permission->checkPermission($permissao, 'eOs') &&
            !$this->permission->checkPermission($permissao, 'eTecnicoFotos')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão.']);
            return;
        }

        $foto_id = $this->input->post('foto_id');

        if (!$foto_id) {
            echo json_encode(['success' => false, 'message' => 'Foto não informada']);
            return;
        }

        $resultado = $this->fotosatendimento_model->delete($foto_id);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Foto removida com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao remover foto']);
        }
    }

    /**
     * Obtém lista de fotos
     */
    public function listarFotos()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        $os_id = $this->input->post('os_id');
        $etapa = $this->input->post('etapa');

        if (!$os_id) {
            echo json_encode(['success' => false, 'message' => 'OS não informada']);
            return;
        }

        $fotos = $this->fotosatendimento_model->getByOs($os_id, $etapa);

        // Prepara dados para retorno (remove imagem_base64 do retorno para economizar banda)
        $fotos_formatadas = [];
        foreach ($fotos as $foto) {
            $fotos_formatadas[] = [
                'idFoto' => $foto->idFoto,
                'os_id' => $foto->os_id,
                'checkin_id' => $foto->checkin_id,
                'arquivo' => $foto->arquivo,
                'url' => $foto->url,
                'descricao' => $foto->descricao,
                'etapa' => $foto->etapa,
                'tamanho' => $foto->tamanho,
                'tipo_arquivo' => $foto->tipo_arquivo,
                'data_upload' => $foto->data_upload,
                'nome_usuario' => $foto->nome_usuario ?? null
            ];
        }

        echo json_encode([
            'success' => true,
            'fotos' => $fotos_formatadas
        ]);
    }

    /**
     * Upload tradicional de arquivo (multipart/form-data)
     */
    public function uploadArquivo()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        // Verifica permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão.']);
            return;
        }

        $os_id = $this->input->post('os_id');
        $checkin_id = $this->input->post('checkin_id');
        $etapa = $this->input->post('etapa') ?: 'durante';
        $descricao = $this->input->post('descricao');

        if (!$os_id) {
            echo json_encode(['success' => false, 'message' => 'OS não informada']);
            return;
        }

        if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado ou erro no upload']);
            return;
        }

        $usuario_id = $this->session->userdata('id_admin');

        // Realiza upload
        $resultado = $this->fotosatendimento_model->uploadFoto(
            $_FILES['arquivo'],
            $os_id,
            $usuario_id,
            $checkin_id,
            $etapa,
            $descricao
        );

        if (isset($resultado['error'])) {
            echo json_encode(['success' => false, 'message' => $resultado['error']]);
            return;
        }

        // Salva no banco
        $data_foto = [
            'os_id' => $os_id,
            'checkin_id' => $checkin_id,
            'usuarios_id' => $usuario_id,
            'arquivo' => $resultado['arquivo'],
            'path' => $resultado['path'],
            'url' => $resultado['url'],
            'descricao' => $descricao,
            'etapa' => $etapa,
            'tamanho' => $resultado['tamanho'],
            'tipo_arquivo' => $resultado['tipo'],
            'imagem_base64' => $resultado['imagem_base64'],
            'mime_type' => $resultado['mime_type']
        ];

        $foto_id = $this->fotosatendimento_model->add($data_foto, true);

        if ($foto_id) {
            echo json_encode([
                'success' => true,
                'message' => 'Arquivo enviado com sucesso',
                'foto_id' => $foto_id,
                'url' => base_url('index.php/checkin/verFotoDB/' . $foto_id),
                'nome' => $resultado['arquivo']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar foto no banco']);
        }
    }

    /**
     * Upload múltiplo de arquivos
     */
    public function uploadMultiplo()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        // Verifica permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão.']);
            return;
        }

        $os_id = $this->input->post('os_id');
        $checkin_id = $this->input->post('checkin_id');
        $etapa = $this->input->post('etapa') ?: 'durante';

        if (!$os_id) {
            echo json_encode(['success' => false, 'message' => 'OS não informada']);
            return;
        }

        if (!isset($_FILES['arquivos'])) {
            echo json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado']);
            return;
        }

        $usuario_id = $this->session->userdata('id_admin');
        $fotos_salvas = [];
        $erros = [];

        // Processa múltiplos arquivos
        $quantidade = count($_FILES['arquivos']['name']);

        for ($i = 0; $i < $quantidade; $i++) {
            // Prepara arquivo individual
            $arquivo = [
                'name' => $_FILES['arquivos']['name'][$i],
                'type' => $_FILES['arquivos']['type'][$i],
                'tmp_name' => $_FILES['arquivos']['tmp_name'][$i],
                'error' => $_FILES['arquivos']['error'][$i],
                'size' => $_FILES['arquivos']['size'][$i]
            ];

            // Pula arquivos com erro
            if ($arquivo['error'] !== UPLOAD_ERR_OK) {
                $erros[] = $arquivo['name'] . ': Erro no upload';
                continue;
            }

            $resultado = $this->fotosatendimento_model->uploadFoto(
                $arquivo,
                $os_id,
                $usuario_id,
                $checkin_id,
                $etapa
            );

            if (isset($resultado['error'])) {
                $erros[] = $arquivo['name'] . ': ' . $resultado['error'];
                continue;
            }

            // Salva no banco
            $data_foto = [
                'os_id' => $os_id,
                'checkin_id' => $checkin_id,
                'usuarios_id' => $usuario_id,
                'arquivo' => $resultado['arquivo'],
                'path' => $resultado['path'],
                'url' => $resultado['url'],
                'etapa' => $etapa,
                'tamanho' => $resultado['tamanho'],
                'tipo_arquivo' => $resultado['tipo'],
                'imagem_base64' => $resultado['imagem_base64'],
                'mime_type' => $resultado['mime_type']
            ];

            $foto_id = $this->fotosatendimento_model->add($data_foto, true);

            if ($foto_id) {
                $fotos_salvas[] = [
                    'foto_id' => $foto_id,
                    'url' => base_url('index.php/checkin/verFotoDB/' . $foto_id),
                    'nome' => $resultado['arquivo']
                ];
            } else {
                $erros[] = $arquivo['name'] . ': Erro ao salvar no banco';
            }
        }

        echo json_encode([
            'success' => count($fotos_salvas) > 0,
            'message' => count($fotos_salvas) . ' foto(s) enviada(s) com sucesso' . (count($erros) > 0 ? '. Erros: ' . implode(', ', $erros) : ''),
            'fotos' => $fotos_salvas,
            'erros' => $erros
        ]);
    }

    /**
     * Visualiza foto do banco de dados (base64)
     * URL: index.php/checkin/verFotoDB/{id}
     */
    public function verFotoDB($foto_id = null)
    {
        if (!$foto_id) {
            $foto_id = $this->input->get('id');
        }

        if (!$foto_id) {
            show_404();
            return;
        }

        // Verificar permissão - admin com vOs OU permissão vTecnicoFotos OU técnico logado (tec_id)
        $permissao = $this->session->userdata('permissao');
        $isTecnico = $this->session->userdata('tec_id') ? true : false;

        // Log para debug
        log_message('debug', 'verFotoDB: Acesso - foto_id=' . $foto_id . ', isTecnico=' . ($isTecnico ? 'sim' : 'nao') . ', permissao=' . $permissao);

        // Permitir acesso se tem permissão vOs OU permissão vTecnicoFotos OU é técnico logado
        $temPermissao = $this->permission->checkPermission($permissao, 'vOs') ||
                        $this->permission->checkPermission($permissao, 'vTecnicoFotos') ||
                        $isTecnico;
        if (!$temPermissao) {
            log_message('error', 'verFotoDB: Acesso negado - foto_id=' . $foto_id);
            show_404();
            return;
        }

        $foto = $this->fotosatendimento_model->getImagemBase64($foto_id);

        if (!$foto) {
            log_message('error', 'verFotoDB: Foto nao encontrada. ID: ' . $foto_id);
            show_404();
            return;
        }

        // Se nao tem imagem_base64, tenta ler do arquivo fisico
        if (empty($foto->imagem_base64)) {
            log_message('debug', 'verFotoDB: imagem_base64 vazia, tentando arquivo fisico. ID: ' . $foto_id);

            // Tenta ler o arquivo fisico
            $path = !empty($foto->path) ? $foto->path : null;

            // Se path eh relativo, converte para absoluto
            if ($path && !file_exists($path)) {
                $path = FCPATH . $path;
            }

            if ($path && file_exists($path)) {
                $mime = $foto->mime_type ?: mime_content_type($path) ?: 'image/jpeg';
                header('Content-Type: ' . $mime);
                header('Content-Length: ' . filesize($path));
                header('Cache-Control: public, max-age=86400');
                readfile($path);
                exit;
            }

            log_message('error', 'verFotoDB: Arquivo fisico nao encontrado. ID: ' . $foto_id . ', Path: ' . ($foto->path ?? 'null'));
            show_404();
            return;
        }

        log_message('debug', 'verFotoDB: Foto encontrada. Tamanho: ' . strlen($foto->imagem_base64));

        // Extrair MIME type e dados base64
        $base64_raw = $foto->imagem_base64;
        $mime_type = 'image/jpeg'; // padrao
        $base64_data = $base64_raw;

        // Verifica se tem o formato data:image/...;base64,
        if (preg_match('/^data:(image\/\w+);base64,(.+)/', $base64_raw, $matches)) {
            $mime_type = $matches[1];
            $base64_data = $matches[2];
            log_message('debug', 'verFotoDB: Formato data: encontrado. MIME: ' . $mime_type);
        } else {
            // Se não tem o prefixo, usa o mime_type do banco ou assume jpeg
            $mime_type = $foto->mime_type ?: 'image/jpeg';
            log_message('debug', 'verFotoDB: Formato data: NAO encontrado. Usando MIME: ' . $mime_type);
        }

        // Limpa espacos em branco
        $base64_data = trim($base64_data);

        // Decodifica base64
        $image_data = base64_decode($base64_data, true);

        if ($image_data === false) {
            log_message('error', 'verFotoDB: Falha ao decodificar base64. Primeiros 100 chars: ' . substr($base64_data, 0, 100));
            show_error('Erro ao decodificar imagem', 500);
            return;
        }

        log_message('debug', 'verFotoDB: Imagem decodificada. Tamanho: ' . strlen($image_data));

        // Envia headers e imagem
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . strlen($image_data));
        header('Cache-Control: public, max-age=86400');
        echo $image_data;
        exit;
    }

    /**
     * Download de foto
     */
    public function downloadFoto($foto_id = null)
    {
        if (!$foto_id) {
            $foto_id = $this->input->get('id');
        }

        if (!$foto_id) {
            show_error('Foto não informada', 400);
            return;
        }

        $foto = $this->fotosatendimento_model->getById($foto_id);

        if (!$foto) {
            show_error('Foto não encontrada', 404);
            return;
        }

        // Tenta obter do banco de dados (base64)
        $imagem_db = $this->fotosatendimento_model->getImagemBase64($foto_id);

        if ($imagem_db && !empty($imagem_db->imagem_base64)) {
            // Extrair MIME type e dados base64
            if (preg_match('/^data:(image\/\w+);base64,/', $imagem_db->imagem_base64, $matches)) {
                $ext = str_replace('image/', '', $matches[1]);
                $base64_data = substr($imagem_db->imagem_base64, strlen($matches[0]));
            } else {
                $ext = $foto->tipo_arquivo ?: 'jpg';
                $base64_data = preg_replace('/^data:image\/\w+;base64,/', '', $imagem_db->imagem_base64);
            }

            $image_data = base64_decode($base64_data, true);

            if ($image_data !== false) {
                $this->load->helper('download');
                $nome_download = 'OS_' . $foto->os_id . '_' . $foto->etapa . '_' . $foto->idFoto . '.' . $ext;
                force_download($nome_download, $image_data);
                return;
            }
        }

        // Fallback: verifica se arquivo existe no sistema de arquivos (para fotos antigas)
        if (!empty($foto->path) && file_exists($foto->path)) {
            $this->load->helper('download');
            $nome_download = 'OS_' . $foto->os_id . '_' . $foto->etapa . '_' . $foto->arquivo;
            force_download($nome_download, file_get_contents($foto->path));
            return;
        }

        show_error('Arquivo não encontrado', 404);
    }

    /**
     * Atualiza descrição da foto
     */
    public function atualizarDescricao()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        // Verifica permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão.']);
            return;
        }

        $foto_id = $this->input->post('foto_id');
        $descricao = $this->input->post('descricao');

        if (!$foto_id) {
            echo json_encode(['success' => false, 'message' => 'Foto não informada']);
            return;
        }

        $resultado = $this->fotosatendimento_model->edit(
            ['descricao' => $descricao],
            $foto_id
        );

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Descrição atualizada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar descrição']);
        }
    }

    /**
     * Visualizar foto (redireciona para verFotoDB)
     */
    public function verFoto($foto_id = null)
    {
        if (!$foto_id) {
            $foto_id = $this->input->get('id');
        }

        if (!$foto_id) {
            show_404();
            return;
        }

        // Redireciona para visualização do banco de dados
        redirect('index.php/checkin/verFotoDB/' . $foto_id);
    }

    /**
     * Obtém estatísticas de fotos
     */
    public function estatisticasFotos()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        $os_id = $this->input->post('os_id');

        if (!$os_id) {
            echo json_encode(['success' => false, 'message' => 'OS não informada']);
            return;
        }

        $total = $this->fotosatendimento_model->count($os_id);
        $entrada = $this->fotosatendimento_model->countByEtapa($os_id, 'entrada');
        $durante = $this->fotosatendimento_model->countByEtapa($os_id, 'durante');
        $saida = $this->fotosatendimento_model->countByEtapa($os_id, 'saida');

        echo json_encode([
            'success' => true,
            'estatisticas' => [
                'total' => $total,
                'entrada' => $entrada,
                'durante' => $durante,
                'saida' => $saida
            ]
        ]);
    }

    /**
     * Remove uma assinatura
     */
    public function removerAssinatura()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }

        // Verifica permissão - aceita permissão de OS geral OU permissão de técnico
        $permissao = $this->session->userdata('permissao');
        if (!$this->permission->checkPermission($permissao, 'eOs') &&
            !$this->permission->checkPermission($permissao, 'eTecnico')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão.']);
            return;
        }

        $assinatura_id = $this->input->post('assinatura_id');

        if (!$assinatura_id) {
            echo json_encode(['success' => false, 'message' => 'Assinatura não informada']);
            return;
        }

        $resultado = $this->assinaturas_model->delete($assinatura_id);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Assinatura removida com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao remover assinatura']);
        }
    }

    /**
     * Visualiza uma assinatura salva (arquivo ou base64 do banco)
     * URL: index.php/checkin/verAssinatura/{id}
     */
    public function verAssinatura($assinatura_id = null)
    {
        if (!$assinatura_id) {
            $assinatura_id = $this->input->get('id');
        }

        if (!$assinatura_id) {
            show_404();
            return;
        }

        // Verificar permissão - admin com vOs OU permissão vTecnicoAssinaturas OU técnico logado (tec_id)
        $permissao = $this->session->userdata('permissao');
        $isTecnico = $this->session->userdata('tec_id') ? true : false;

        // Log para debug
        log_message('debug', 'verAssinatura: Acesso - assinatura_id=' . $assinatura_id . ', isTecnico=' . ($isTecnico ? 'sim' : 'nao') . ', permissao=' . $permissao);

        // Permitir acesso se tem permissão vOs OU permissão vTecnicoAssinaturas OU é técnico logado
        $temPermissao = $this->permission->checkPermission($permissao, 'vOs') ||
                        $this->permission->checkPermission($permissao, 'vTecnicoAssinaturas') ||
                        $isTecnico;
        if (!$temPermissao) {
            log_message('error', 'verAssinatura: Acesso negado - assinatura_id=' . $assinatura_id);
            show_404();
            return;
        }

        $assinatura = $this->assinaturas_model->getById($assinatura_id);

        if (!$assinatura) {
            log_message('error', 'verAssinatura: Assinatura nao encontrada. ID: ' . $assinatura_id);
            show_404();
            return;
        }

        log_message('debug', 'verAssinatura: Assinatura encontrada. ID: ' . $assinatura_id . ', Tipo: ' . ($assinatura->tipo ?? 'N/A') . ', Path: ' . substr($assinatura->assinatura ?? 'null', 0, 100));

        // Se é base64 no banco
        if (isset($assinatura->assinatura) && strpos($assinatura->assinatura, 'BASE64:') === 0) {
            $base64_data = substr($assinatura->assinatura, 7); // Remove "BASE64:"

            // Extrair MIME type e dados
            if (preg_match('/^data:(image\/\w+);base64,/', $base64_data, $matches)) {
                $mime_type = $matches[1];
                $base64_data = substr($base64_data, strlen($matches[0]));
            } else {
                $mime_type = 'image/png';
            }

            // Decodificar e exibir
            $image_data = base64_decode($base64_data, true);

            if ($image_data === false) {
                show_error('Erro ao decodificar imagem', 500);
                return;
            }

            // Enviar headers e imagem
            header('Content-Type: ' . $mime_type);
            header('Content-Length: ' . strlen($image_data));
            header('Cache-Control: public, max-age=86400');
            echo $image_data;
            exit;
        }

        // Se é arquivo no disco
        $caminho_arquivo = $assinatura->assinatura;

        log_message('debug', 'verAssinatura: Caminho original: ' . $caminho_arquivo);

        // Se o caminho for relativo, converte para absoluto usando FCPATH
        if (!file_exists($caminho_arquivo)) {
            $caminho_absoluto = FCPATH . $caminho_arquivo;
            log_message('debug', 'verAssinatura: Tentando caminho absoluto: ' . $caminho_absoluto);
            if (file_exists($caminho_absoluto)) {
                $caminho_arquivo = $caminho_absoluto;
                log_message('debug', 'verAssinatura: Arquivo encontrado no caminho absoluto');
            }
        } else {
            log_message('debug', 'verAssinatura: Arquivo encontrado no caminho original');
        }

        if (file_exists($caminho_arquivo)) {
            $mime = mime_content_type($caminho_arquivo);
            log_message('debug', 'verAssinatura: Retornando arquivo. MIME: ' . $mime . ', Tamanho: ' . filesize($caminho_arquivo));
            header('Content-Type: ' . $mime);
            header('Content-Length: ' . filesize($caminho_arquivo));
            header('Cache-Control: public, max-age=86400');
            readfile($caminho_arquivo);
            exit;
        }

        log_message('error', 'verAssinatura: Arquivo nao encontrado. ID: ' . $assinatura_id . ', Path: ' . $assinatura->assinatura);
        log_message('error', 'verAssinatura: Caminho tentado: ' . $caminho_arquivo);
        log_message('error', 'verAssinatura: FCPATH: ' . FCPATH);
        show_404();
    }

    /**
     * Imprime relatório de atendimento da OS
     */
    public function imprimir($os_id = null)
    {
        if (!$os_id) {
            $os_id = $this->uri->segment(3);
        }

        if (!$os_id) {
            show_error('OS não informada', 400);
            return;
        }

        // Verifica permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar este relatório.');
            redirect(base_url());
        }

        // Carrega dados da OS
        $this->load->model('clientes_model');
        $this->load->model('usuarios_model');

        $os = $this->os_model->getById($os_id);
        if (!$os) {
            show_error('OS não encontrada', 404);
            return;
        }

        // Dados do cliente
        $cliente = $this->clientes_model->getById($os->clientes_id);

        // Dados do emitente
        $emitente = $this->mapos_model->getEmitente();

        // Checkins da OS
        $checkins = $this->checkin_model->getAllByOs($os_id);

        // Assinaturas
        $assinaturas = $this->assinaturas_model->getByOs($os_id);

        // Organiza assinaturas por tipo
        $assinaturasPorTipo = [];
        if (!empty($assinaturas)) {
            foreach ($assinaturas as $assinatura) {
                $assinaturasPorTipo[$assinatura->tipo] = $assinatura;
            }
        }

        // Fotos
        $fotos = $this->fotosatendimento_model->getByOs($os_id);

        // Organiza fotos por etapa
        $fotosPorEtapa = [
            'entrada' => [],
            'durante' => [],
            'saida' => []
        ];
        foreach ($fotos as $foto) {
            $fotosPorEtapa[$foto->etapa][] = $foto;
        }

        // Execucoes tecnicas do portal (tec_os_execucao)
        $execucoesTecnicas = $this->tec_os_model->getExecucoesByOs($os_id);
        $fotosTecnico = $this->tec_os_model->getFotosByOs($os_id);

        // Produtos e Serviços da OS
        $produtos = $this->os_model->getProdutos($os_id);
        $servicos = $this->os_model->getServicos($os_id);

        // Prepara dados para a view
        $data = [
            'os' => $os,
            'cliente' => $cliente,
            'emitente' => $emitente,
            'checkins' => $checkins,
            'assinaturas' => $assinaturasPorTipo,
            'fotosPorEtapa' => $fotosPorEtapa,
            'execucoesTecnicas' => $execucoesTecnicas,
            'fotosTecnico' => $fotosTecnico,
            'produtos' => $produtos,
            'servicos' => $servicos,
            'titulo' => 'Relatório de Atendimento - OS #' . sprintf('%04d', $os_id)
        ];

        $this->load->view('checkin/imprimirCheckin', $data);
    }
}
