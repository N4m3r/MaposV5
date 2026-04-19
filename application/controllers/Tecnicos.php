<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Sistema de Gestão de Técnicos - Mapos OS
 *
 * Controller principal para o portal do técnico com:
 * - Login com foto e geolocalização
 * - Execução de OS com checklists
 * - Check-in/check-out com GPS
 * - Documentação fotográfica
 * - Assinatura digital
 */
class Tecnicos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tecnicos_model');
        $this->load->model('tec_os_model');
        $this->load->model('os_model');
        $this->load->model('mapos_model');
        $this->load->model('fotosatendimento_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->database();

        // Carregar configurações do sistema
        $this->_load_configuration();

        // Verificar autenticação para métodos protegidos
        $metodos_publicos = ['login', 'autenticar', 'logout', 'api_login', 'api_verificar'];
        if (!in_array($this->router->method, $metodos_publicos)) {
            $this->_verificar_autenticacao();
        }
    }

    /**
     * Carregar configurações do sistema
     */
    private function _load_configuration()
    {
        // Configurações padrão
        $this->data['configuration'] = [
            'app_name' => 'Map-OS',
            'app_theme' => 'default',
            'per_page' => 10,
            'control_datatable' => '1',
        ];

        // Buscar configurações do banco
        try {
            $configuracoes = $this->db->get('configuracoes')->result();
            foreach ($configuracoes as $c) {
                $this->data['configuration'][$c->config] = $c->valor;
            }
        } catch (Exception $e) {
            // Se falhar, mantém configurações padrão
        }
    }

    /**
     * Verificar se técnico está autenticado
     */
    private function _verificar_autenticacao()
    {
        if (!$this->session->userdata('tec_id')) {
            redirect('tecnicos/login');
        }

        // Verificar se token ainda é válido
        $tecnico = $this->tecnicos_model->getById($this->session->userdata('tec_id'));
        if (!$tecnico || !$tecnico->app_tecnico_instalado) {
            $this->logout();
        }
    }

    /**
     * Página de login do técnico
     */
    public function login()
    {
        if ($this->session->userdata('tec_id')) {
            redirect('tecnicos/dashboard');
        }

        $this->load->view('tecnicos/login');
    }

    /**
     * Autenticar técnico com foto e localização
     */
    public function autenticar()
    {
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
        $this->form_validation->set_rules('senha', 'Senha', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', 'Dados incompletos. Verifique e-mail e senha.');
            redirect('tecnicos/login');
        }

        $email = $this->input->post('email');
        $senha = $this->input->post('senha');
        $latitude = $this->input->post('latitude') ?: null;
        $longitude = $this->input->post('longitude') ?: null;
        $foto_login = $this->input->post('foto_login');

        // Buscar técnico
        $tecnico = $this->tecnicos_model->getByEmail($email);

        if (!$tecnico || !password_verify($senha, $tecnico->senha)) {
            $this->session->set_flashdata('error', 'E-mail ou senha incorretos.');
            redirect('tecnicos/login');
        }

        if (!$tecnico->is_tecnico && !$tecnico->app_tecnico_instalado) {
            $this->session->set_flashdata('error', 'Usuário não possui permissão de técnico.');
            redirect('tecnicos/login');
        }

        // Verificar se técnico está dentro do raio de atuação (se configurado)
        if ($tecnico->raio_atuacao_km > 0 && $tecnico->coordenadas_base_lat && $tecnico->coordenadas_base_lng && $latitude && $longitude) {
            $distancia = $this->_calcular_distancia(
                $tecnico->coordenadas_base_lat,
                $tecnico->coordenadas_base_lng,
                $latitude,
                $longitude
            );

            if ($distancia > $tecnico->raio_atuacao_km) {
                log_message('error', "Técnico {$tecnico->idUsuarios} tentou login fora do raio de atuação. Distância: {$distancia}km");
                // Opcional: bloquear login ou apenas alertar
                // $this->session->set_flashdata('warning', 'Você está fora do raio de atuação configurado.');
            }
        }

        // Salvar foto de login se fornecida
        $caminho_foto = null;
        if ($foto_login) {
            $caminho_foto = $this->_salvar_foto_base64($foto_login, 'logins', $tecnico->idUsuarios);
        }

        // Registrar acesso
        $this->tecnicos_model->registrar_acesso($tecnico->idUsuarios, $latitude, $longitude, $caminho_foto);

        // Criar sessão
        $sessao = [
            'tec_id' => $tecnico->idUsuarios,
            'tec_nome' => $tecnico->nome,
            'tec_nivel' => $tecnico->nivel_tecnico,
            'tec_email' => $tecnico->email,
            'tec_foto' => $tecnico->foto_tecnico,
            'nome_admin' => $tecnico->nome, // Para compatibilidade com o tema
            'url_image_user_admin' => $tecnico->foto_tecnico, // Para compatibilidade
            'logged_in' => true,
        ];
        $this->session->set_userdata($sessao);

        // Atualizar token
        $token = bin2hex(random_bytes(32));
        $this->tecnicos_model->atualizar_token($tecnico->idUsuarios, $token);

        redirect('tecnicos/dashboard');
    }

    /**
     * Logout do técnico
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('tecnicos/login');
    }

    /**
     * Dashboard do técnico
     */
    public function dashboard()
    {
        $tecnico_id = $this->session->userdata('tec_id');

        $this->data['tecnico'] = $this->tecnicos_model->getById($tecnico_id);
        $this->data['os_hoje'] = $this->tec_os_model->getOsDoDia($tecnico_id);
        $this->data['os_pendentes'] = $this->tec_os_model->getOsPendentes($tecnico_id);
        $this->data['os_concluidas'] = $this->tec_os_model->getOsConcluidasSemana($tecnico_id);
        $this->data['estoque'] = $this->tecnicos_model->getEstoqueVeiculo($tecnico_id);
        $this->data['menuDashboard'] = 'active';

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('tecnicos/dashboard', $this->data);
        $this->load->view('tema/rodape', $this->data);
    }

    /**
     * Listar OS atribuídas ao técnico
     */
    public function minhas_os()
    {
        $tecnico_id = $this->session->userdata('tec_id');
        $status = $this->input->get('status') ?: 'todos';

        $this->data['os_list'] = $this->tec_os_model->getOsPorTecnico($tecnico_id, $status);
        $this->data['status_atual'] = $status;

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('tecnicos/minhas_os', $this->data);
        $this->load->view('tema/rodape', $this->data);
    }

    /**
     * Visualizar detalhes da OS e executar
     */
    public function executar_os($os_id = null)
    {
        if (!$os_id) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('tecnicos/minhas_os');
        }

        $tecnico_id = $this->session->userdata('tec_id');

        // Verificar se OS pertence ao técnico (campo tecnico_responsavel)
        $os = $this->tec_os_model->getOsById($os_id);
        if (!$os || $os->tecnico_responsavel != $tecnico_id) {
            $this->session->set_flashdata('error', 'Você não tem permissão para executar esta OS.');
            redirect('tecnicos/minhas_os');
        }

        // Se OS já estiver finalizada, redirecionar para o relatório
        if ($os->status == 'Finalizada' || $os->status == 'Finalizado') {
            redirect('tecnicos/relatorio_execucao/' . $os_id);
        }

        $this->data['os'] = $os;
        $this->data['cliente'] = $this->tec_os_model->getClienteByOs($os_id);
        $this->data['produtos'] = $this->tec_os_model->getProdutosOs($os_id);
        $this->data['servicos'] = $this->tec_os_model->getServicosOs($os_id);
        $this->data['execucao'] = $this->tec_os_model->getExecucaoAtual($os_id, $tecnico_id);
        $this->data['checklist'] = $this->tec_os_model->getChecklistExecucao($os_id);

        // Carregar fotos do sistema de atendimento (mesmo padrão de os/visualizar)
        $this->load->model('fotosatendimento_model');
        $this->data['fotosAtendimento'] = $this->fotosatendimento_model->getByOs($os_id);

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('tecnicos/executar_os', $this->data);
        $this->load->view('tema/rodape', $this->data);
    }

    /**
     * Iniciar execução da OS (check-in)
     */
    public function iniciar_execucao()
    {
        header('Content-Type: application/json');

        $os_id = $this->input->post('os_id');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        $foto_checkin = $this->input->post('foto_checkin');
        $tipo = $this->input->post('tipo'); // 'inicio_dia' ou 'inicio_local'

        // Apenas OS é obrigatório - latitude/longitude podem ser 0 (GPS opcional)
        if (!$os_id) {
            echo json_encode(['success' => false, 'message' => 'OS não informada']);
            return;
        }

        // Garantir que latitude/longitude sejam numéricos (padrão 0 se não enviado)
        $latitude = is_numeric($latitude) ? (float)$latitude : 0;
        $longitude = is_numeric($longitude) ? (float)$longitude : 0;

        $tecnico_id = $this->session->userdata('tec_id');

        // Verificar se já existe execução em andamento
        $execucao_atual = $this->tec_os_model->getExecucaoAtual($os_id, $tecnico_id);
        if ($execucao_atual) {
            echo json_encode(['success' => false, 'message' => 'Já existe uma execução em andamento']);
            return;
        }

        // Salvar foto de check-in
        $caminho_foto = null;
        if ($foto_checkin) {
            $caminho_foto = $this->_salvar_foto_base64($foto_checkin, 'checkin', $tecnico_id);
        }

        // Calcular distância até o cliente
        $os = $this->tec_os_model->getOsById($os_id);
        $cliente = $this->tec_os_model->getClienteByOs($os_id);
        $distancia_cliente = null;

        if ($cliente && isset($cliente->lat) && isset($cliente->lng) && $cliente->lat && $cliente->lng) {
            $distancia_cliente = $this->_calcular_distancia(
                $cliente->lat,
                $cliente->lng,
                $latitude,
                $longitude
            );
        }

        // Criar registro de execução - usando nomes corretos da tabela
        $dados = [
            'os_id' => $os_id,
            'tecnico_id' => $tecnico_id,
            'checkin_horario' => date('Y-m-d H:i:s'),
            'checkin_latitude' => $latitude,
            'checkin_longitude' => $longitude,
            'checkin_foto' => $caminho_foto,
            'tipo_servico' => 'MC', // Manutenção Corretiva como padrão
            'checkin_distancia_metros' => $distancia_cliente ? round($distancia_cliente) : null,
        ];

        $execucao_id = $this->tec_os_model->iniciarExecucao($dados);

        log_message('info', 'Tecnicos::iniciar_execucao - Execucao ID retornado: ' . var_export($execucao_id, true));
        log_message('info', 'Tecnicos::iniciar_execucao - Dados inseridos: ' . print_r($dados, true));

        if (!$execucao_id || $execucao_id == 0) {
            log_message('error', 'Tecnicos::iniciar_execucao - Falha ao obter insert_id');
            echo json_encode(['success' => false, 'message' => 'Erro ao criar execução. ID retornado: ' . $execucao_id]);
            return;
        }

        // Atualizar status da OS
        $this->os_model->edit('os', ['status' => 'Em Andamento'], 'idOs', $os_id);

        // Criar checkin na tabela os_checkin (integração com painel admin)
        $this->load->model('checkin_model');
        $data_checkin = [
            'os_id' => $os_id,
            'usuarios_id' => $tecnico_id,
            'data_entrada' => date('Y-m-d H:i:s'),
            'latitude_entrada' => $latitude ?: null,
            'longitude_entrada' => $longitude ?: null,
            'observacao_entrada' => 'Iniciado via portal do técnico',
            'status' => 'Em Andamento'
        ];
        $this->checkin_model->add($data_checkin);
        log_info('Checkin criado via portal do técnico - OS: ' . $os_id);

        echo json_encode([
            'success' => true,
            'execucao_id' => $execucao_id,
            'message' => 'Execução iniciada com sucesso',
        ]);
    }

    /**
     * Finalizar execução da OS (check-out)
     */
    public function finalizar_execucao()
    {
        header('Content-Type: application/json');

        $execucao_id = $this->input->post('execucao_id');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        $foto_checkout = $this->input->post('foto_checkout');
        $assinatura_cliente = $this->input->post('assinatura_cliente');
        $nome_cliente_assina = $this->input->post('nome_cliente_assina');
        $observacoes = $this->input->post('observacoes');
        $servicos_executados = $this->input->post('servicos'); // array de IDs

        // Apenas execucao_id é obrigatório - GPS é opcional
        if (!$execucao_id) {
            echo json_encode(['success' => false, 'message' => 'Execução não informada']);
            return;
        }

        // Garantir que latitude/longitude sejam numéricos (padrão 0 se não enviado)
        $latitude = is_numeric($latitude) ? (float)$latitude : 0;
        $longitude = is_numeric($longitude) ? (float)$longitude : 0;

        $tecnico_id = $this->session->userdata('tec_id');

        // Verificar execução
        $execucao = $this->tec_os_model->getExecucaoById($execucao_id);
        if (!$execucao || $execucao->tecnico_id != $tecnico_id) {
            echo json_encode(['success' => false, 'message' => 'Execução não encontrada']);
            return;
        }

        // Salvar foto de check-out
        $caminho_foto = null;
        if ($foto_checkout) {
            $caminho_foto = $this->_salvar_foto_base64($foto_checkout, 'checkout', $tecnico_id);
        }

        // Salvar assinatura
        $caminho_assinatura = null;
        if ($assinatura_cliente) {
            $caminho_assinatura = $this->_salvar_foto_base64($assinatura_cliente, 'assinatura', $tecnico_id);
        }

        // Calcular tempo total
        $data_checkin = new DateTime($execucao->checkin_horario);
        $data_checkout = new DateTime();
        $intervalo = $data_checkin->diff($data_checkout);
        $tempo_total_horas = $intervalo->h + ($intervalo->i / 60) + ($intervalo->days * 24);
        $tempo_atendimento_minutos = round($tempo_total_horas * 60);

        // Preparar dados do checklist com assinatura e observações
        $checklist_data = [
            'assinatura_cliente' => $caminho_assinatura,
            'nome_cliente_assina' => $nome_cliente_assina,
            'observacoes' => $observacoes,
            'servicos_executados' => $servicos_executados,
        ];

        // Atualizar execução - usando nomes corretos da tabela
        $dados = [
            'checkout_horario' => date('Y-m-d H:i:s'),
            'checkout_latitude' => $latitude,
            'checkout_longitude' => $longitude,
            'checkout_foto' => $caminho_foto,
            'tempo_atendimento_minutos' => $tempo_atendimento_minutos,
            'checklist_json' => json_encode($checklist_data),
            'checklist_completude' => 100, // Marca como 100% completo ao finalizar
        ];

        $this->tec_os_model->finalizarExecucao($execucao_id, $dados);

        // Atualizar OS para Finalizada
        $this->os_model->edit('os', ['status' => 'Finalizada'], 'idOs', $execucao->os_id);

        // Finalizar checkin na tabela os_checkin (integração com painel admin)
        $this->load->model('checkin_model');
        $checkin_ativo = $this->checkin_model->getCheckinAtivo($execucao->os_id);
        if ($checkin_ativo) {
            $data_checkout = [
                'data_saida' => date('Y-m-d H:i:s'),
                'latitude_saida' => $latitude ?: null,
                'longitude_saida' => $longitude ?: null,
                'observacao_saida' => $observacoes,
                'status' => 'Finalizado',
                'data_atualizacao' => date('Y-m-d H:i:s')
            ];
            $this->checkin_model->finalizarAtendimento($checkin_ativo->idCheckin, $data_checkout);
            log_info('Checkin finalizado via portal do técnico - OS: ' . $execucao->os_id);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Execução finalizada com sucesso',
            'tempo_total' => $tempo_total_horas,
        ]);
    }

    /**
     * Ver relatório de execução da OS
     */
    public function relatorio_execucao($os_id = null)
    {
        if (!$os_id) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            redirect('tecnicos/minhas_os');
        }

        $tecnico_id = $this->session->userdata('tec_id');
        $permissao = $this->session->userdata('permissao');

        // Verificar se OS existe
        $os = $this->tec_os_model->getOsById($os_id);
        if (!$os) {
            $this->session->set_flashdata('error', 'OS não encontrada.');
            if ($this->permission->checkPermission($permissao, 'vOs')) {
                redirect('os');
            } else {
                redirect('tecnicos/minhas_os');
            }
        }

        // Verificar permissão: técnico dono da OS OU admin com permissão vOs
        $isTecnicoDono = ($tecnico_id && $os->tecnico_responsavel == $tecnico_id);
        $isAdmin = $this->permission->checkPermission($permissao, 'vOs');

        if (!$isTecnicoDono && !$isAdmin) {
            $this->session->set_flashdata('error', 'Você não tem permissão para ver esta OS.');
            if ($isAdmin) {
                redirect('os');
            } else {
                redirect('tecnicos/minhas_os');
            }
        }

        // Buscar dados da execução
        $this->data['os'] = $os;
        $this->data['cliente'] = $this->tec_os_model->getClienteByOs($os_id);
        $this->data['produtos'] = $this->tec_os_model->getProdutosOs($os_id);
        $this->data['servicos'] = $this->tec_os_model->getServicosOs($os_id);
        $this->data['execucoes'] = $this->tec_os_model->getExecucoesByOs($os_id);

        // Carregar fotos do sistema de atendimento (checkin)
        $this->load->model('fotosatendimento_model');
        $this->data['fotosAtendimento'] = $this->fotosatendimento_model->getByOs($os_id);

        // Carregar assinaturas do sistema de checkin
        $this->load->model('assinaturas_model');
        $this->data['assinaturas'] = $this->assinaturas_model->getByOs($os_id);

        // Carregar fotos do portal do técnico (tec_os_fotos)
        $this->data['fotosTecnico'] = $this->tec_os_model->getFotosByOs($os_id);

        $this->load->view('tema/topo', $this->data);

        // Carregar menu apropriado: menu padrão para admin, menu portal para técnico
        if ($isAdmin) {
            $this->load->view('tema/menu', $this->data);
        } else {
            $this->load->view('tema/menu_portal_tecnico', $this->data);
        }

        $this->load->view('tecnicos/relatorio_execucao', $this->data);
        $this->load->view('tema/rodape', $this->data);
    }

    /**
     * Adicionar foto à OS - Usa o mesmo padrão do sistema de atendimento
     */
    public function adicionar_foto()
    {
        header('Content-Type: application/json');

        // Aumenta o limite de memória para processar imagens grandes
        ini_set('memory_limit', '256M');

        try {
            $execucao_id = $this->input->post('execucao_id');
            // Usar FALSE para evitar XSS filter que corrompe base64
            $foto = $this->input->post('foto', false);
            $descricao = $this->input->post('descricao');
            $tipo = $this->input->post('tipo'); // 'antes', 'depois', 'problema', 'detalhe'
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');

            log_message('info', 'Tecnicos::adicionar_foto - Requisicao recebida. execucao_id: ' . $execucao_id);
            log_message('info', 'Tecnicos::adicionar_foto - Tamanho da foto: ' . strlen($foto) . ' caracteres');

            if (!$execucao_id || !$foto) {
                log_message('error', 'Tecnicos::adicionar_foto - Dados incompletos');
                echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
                return;
            }

            $tecnico_id = $this->session->userdata('tec_id');
            log_message('info', 'Tecnicos::adicionar_foto - Tecnico: ' . $tecnico_id);

            // Verificar execução
            $execucao = $this->tec_os_model->getExecucaoById($execucao_id);
            if (!$execucao || $execucao->tecnico_id != $tecnico_id) {
                log_message('error', 'Tecnicos::adicionar_foto - Execucao nao encontrada ou nao pertence ao tecnico');
                echo json_encode(['success' => false, 'message' => 'Execução não encontrada']);
                return;
            }

            $os_id = $execucao->os_id;

            // Usa o mesmo padrão do sistema de atendimento - salvar no banco com base64
            $resultado = $this->fotosatendimento_model->salvarFotoBase64(
                $foto,
                $os_id,
                $tecnico_id,
                null, // checkin_id
                'durante', // etapa
                $descricao
            );

            if (isset($resultado['error'])) {
                log_message('error', 'Tecnicos::adicionar_foto - Erro ao processar foto: ' . $resultado['error']);
                echo json_encode(['success' => false, 'message' => $resultado['error']]);
                return;
            }

            log_message('info', 'Tecnicos::adicionar_foto - Foto processada: ' . $resultado['arquivo']);

            // Dados para salvar no banco
            $data_foto = [
                'os_id' => $os_id,
                'checkin_id' => null,
                'usuarios_id' => $tecnico_id,
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

            // Salva no banco usando o model de fotos de atendimento
            $foto_id = $this->fotosatendimento_model->add($data_foto, true);

            if (!$foto_id) {
                log_message('error', 'Tecnicos::adicionar_foto - Erro ao salvar no banco');
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar foto no banco']);
                return;
            }

            log_message('info', 'Tecnicos::adicionar_foto - Foto adicionada com sucesso. ID: ' . $foto_id);

            echo json_encode([
                'success' => true,
                'foto_id' => $foto_id,
                'url' => base_url('index.php/checkin/verFotoDB/' . $foto_id),
                'message' => 'Foto adicionada com sucesso',
            ]);
        } catch (Exception $e) {
            log_message('error', 'Tecnicos::adicionar_foto - Excecao: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Remover foto do atendimento
     */
    public function remover_foto()
    {
        header('Content-Type: application/json');

        $foto_id = $this->input->post('foto_id');

        if (!$foto_id) {
            echo json_encode(['success' => false, 'message' => 'ID da foto não informado']);
            return;
        }

        $tecnico_id = $this->session->userdata('tec_id');

        // Buscar a foto para verificar se pertence a uma OS do técnico
        $this->load->model('fotosatendimento_model');
        $foto = $this->fotosatendimento_model->getById($foto_id);

        if (!$foto) {
            echo json_encode(['success' => false, 'message' => 'Foto não encontrada']);
            return;
        }

        // Verificar se a OS pertence ao técnico
        $os = $this->tec_os_model->getOsById($foto->os_id);
        if (!$os || $os->tecnico_responsavel != $tecnico_id) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para remover esta foto']);
            return;
        }

        // Remover a foto
        $resultado = $this->fotosatendimento_model->delete($foto_id);

        if ($resultado) {
            log_message('info', 'Tecnicos::remover_foto - Foto ' . $foto_id . ' removida pelo tecnico ' . $tecnico_id);
            echo json_encode(['success' => true, 'message' => 'Foto removida com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao remover foto']);
        }
    }

    /**
     * Salvar item de checklist
     */
    public function salvar_checklist_item()
    {
        $execucao_id = $this->input->post('execucao_id');
        $item_id = $this->input->post('item_id');
        $status = $this->input->post('status'); // 'conforme', 'nao_conforme', 'nao_aplicavel'
        $observacao = $this->input->post('observacao');
        $valor = $this->input->post('valor'); // para campos numéricos ou texto

        if (!$execucao_id || !$item_id) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }

        $tecnico_id = $this->session->userdata('tec_id');

        // Verificar execução
        $execucao = $this->tec_os_model->getExecucaoById($execucao_id);
        if (!$execucao || $execucao->tecnico_id != $tecnico_id) {
            echo json_encode(['success' => false, 'message' => 'Execução não encontrada']);
            return;
        }

        $this->tec_os_model->salvarChecklistItem($execucao_id, $item_id, [
            'status' => $status,
            'observacao' => $observacao,
            'valor' => $valor,
            'data_hora' => date('Y-m-d H:i:s'),
        ]);

        echo json_encode(['success' => true, 'message' => 'Item salvo com sucesso']);
    }

    /**
     * Página de estoque do veículo
     */
    public function meu_estoque()
    {
        $tecnico_id = $this->session->userdata('tec_id');

        $this->data['estoque'] = $this->tecnicos_model->getEstoqueVeiculo($tecnico_id);
        $this->data['historico'] = $this->tecnicos_model->getHistoricoEstoque($tecnico_id, 30);

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('tecnicos/meu_estoque', $this->data);
        $this->load->view('tema/rodape', $this->data);
    }

    /**
     * API - Obter estoque do técnico em JSON
     */
    public function obter_estoque_json()
    {
        header('Content-Type: application/json');

        $tecnico_id = $this->session->userdata('tec_id');

        if (!$tecnico_id) {
            echo json_encode(['success' => false, 'message' => 'Não autenticado']);
            return;
        }

        $estoque = $this->tecnicos_model->getEstoqueVeiculo($tecnico_id);

        echo json_encode(['success' => true, 'estoque' => $estoque]);
    }

    /**
     * Registrar uso de material
     */
    public function registrar_uso_material()
    {
        $produto_id = $this->input->post('produto_id');
        $quantidade = $this->input->post('quantidade');
        $os_id = $this->input->post('os_id');
        $observacao = $this->input->post('observacao');

        if (!$produto_id || !$quantidade) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }

        $tecnico_id = $this->session->userdata('tec_id');

        // Verificar saldo
        $estoque = $this->tecnicos_model->getEstoqueItem($tecnico_id, $produto_id);
        if (!$estoque || $estoque->quantidade < $quantidade) {
            echo json_encode(['success' => false, 'message' => 'Quantidade insuficiente em estoque']);
            return;
        }

        // Registrar uso
        $this->tecnicos_model->registrarUsoMaterial($tecnico_id, [
            'produto_id' => $produto_id,
            'quantidade' => $quantidade,
            'os_id' => $os_id,
            'observacao' => $observacao,
            'data_hora' => date('Y-m-d H:i:s'),
        ]);

        echo json_encode(['success' => true, 'message' => 'Uso registrado com sucesso']);
    }

    /**
     * Perfil do técnico
     */
    public function perfil()
    {
        $tecnico_id = $this->session->userdata('tec_id');
        $this->data['tecnico'] = $this->tecnicos_model->getById($tecnico_id);

        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu_portal_tecnico', $this->data);
        $this->load->view('tecnicos/perfil', $this->data);
        $this->load->view('tema/rodape', $this->data);
    }

    /**
     * Atualizar foto do técnico
     */
    public function atualizar_foto()
    {
        $foto = $this->input->post('foto');

        if (!$foto) {
            echo json_encode(['success' => false, 'message' => 'Foto não fornecida']);
            return;
        }

        $tecnico_id = $this->session->userdata('tec_id');

        // Salvar foto
        $caminho_foto = $this->_salvar_foto_base64($foto, 'perfil', $tecnico_id);

        // Atualizar no banco
        $this->tecnicos_model->update($tecnico_id, ['foto_tecnico' => $caminho_foto]);

        // Atualizar sessão
        $this->session->set_userdata('tec_foto', $caminho_foto);

        echo json_encode(['success' => true, 'caminho' => $caminho_foto]);
    }

    /**
     * API - Login mobile
     */
    public function api_login()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? null;
        $senha = $input['senha'] ?? null;
        $latitude = $input['latitude'] ?? null;
        $longitude = $input['longitude'] ?? null;
        $push_token = $input['push_token'] ?? null;

        if (!$email || !$senha) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'E-mail e senha são obrigatórios']);
            return;
        }

        $tecnico = $this->tecnicos_model->getByEmail($email);

        if (!$tecnico || !password_verify($senha, $tecnico->senha) || !$tecnico->is_tecnico) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Credenciais inválidas']);
            return;
        }

        // Gerar token JWT (simplificado - em produção usar biblioteca JWT)
        $token = bin2hex(random_bytes(32));
        $token_expira = date('Y-m-d H:i:s', strtotime('+7 days'));

        $this->tecnicos_model->update($tecnico->idUsuarios, [
            'token_app' => $token,
            'token_expira' => $token_expira,
            'push_token' => $push_token,
            'ultimo_acesso_app' => date('Y-m-d H:i:s'),
        ]);

        echo json_encode([
            'success' => true,
            'token' => $token,
            'tecnico' => [
                'id' => $tecnico->idUsuarios,
                'nome' => $tecnico->nome,
                'email' => $tecnico->email,
                'nivel' => $tecnico->nivel_tecnico,
                'foto' => $tecnico->foto_tecnico,
            ],
        ]);
    }

    /**
     * API - Verificar token
     */
    public function api_verificar()
    {
        header('Content-Type: application/json');

        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;

        if (!$token) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token não fornecido']);
            return;
        }

        $tecnico = $this->tecnicos_model->getByToken(str_replace('Bearer ', '', $token));

        if (!$tecnico || strtotime($tecnico->token_expira) < time()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token inválido ou expirado']);
            return;
        }

        echo json_encode(['success' => true, 'tecnico_id' => $tecnico->idUsuarios]);
    }

    /**
     * Calcular distância entre dois pontos GPS (Haversine)
     */
    private function _calcular_distancia($lat1, $lng1, $lat2, $lng2)
    {
        $raio_terra = 6371; // km

        $lat1_rad = deg2rad($lat1);
        $lat2_rad = deg2rad($lat2);
        $delta_lat = deg2rad($lat2 - $lat1);
        $delta_lng = deg2rad($lng2 - $lng1);

        $a = sin($delta_lat / 2) * sin($delta_lat / 2) +
             cos($lat1_rad) * cos($lat2_rad) *
             sin($delta_lng / 2) * sin($delta_lng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $raio_terra * $c;
    }

    /**
     * Salvar foto em base64 para arquivo
     */
    private function _salvar_foto_base64($base64_string, $tipo, $tecnico_id)
    {
        log_message('info', '_salvar_foto_base64 - Iniciando salvamento para tecnico: ' . $tecnico_id);
        log_message('info', '_salvar_foto_base64 - Tamanho recebido: ' . strlen($base64_string) . ' caracteres');
        log_message('info', '_salvar_foto_base64 - Primeiros 100 caracteres: ' . substr($base64_string, 0, 100));

        if (empty($base64_string)) {
            log_message('error', '_salvar_foto_base64 - String base64 vazia');
            return false;
        }

        // Extrair dados da string base64
        if (strpos($base64_string, ',') !== false) {
            $data = explode(',', $base64_string);
            $base64_data = isset($data[1]) ? $data[1] : $data[0];
        } else {
            $base64_data = $base64_string;
        }

        // Limpar base64
        $base64_data = preg_replace('/[^a-zA-Z0-9+\/=]/', '', $base64_data);
        log_message('info', '_salvar_foto_base64 - Tamanho apos limpeza: ' . strlen($base64_data));

        // Decodificar
        $imagem = base64_decode($base64_data, true);

        if ($imagem === false) {
            log_message('error', '_salvar_foto_base64 - Falha ao decodificar base64. Tamanho: ' . strlen($base64_data));
            return false;
        }
        log_message('info', '_salvar_foto_base64 - Imagem decodificada: ' . strlen($imagem) . ' bytes');

        // Validar se é imagem
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            log_message('error', '_salvar_foto_base64 - Nao foi possivel abrir finfo');
            return false;
        }
        $mime_type = finfo_buffer($finfo, $imagem);
        finfo_close($finfo);

        log_message('info', '_salvar_foto_base64 - MIME type detectado: ' . $mime_type);

        if (!in_array($mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            log_message('error', '_salvar_foto_base64 - Tipo invalido: ' . $mime_type);
            return false;
        }

        // Determinar extensao
        $extensao = 'jpg';
        switch ($mime_type) {
            case 'image/png': $extensao = 'png'; break;
            case 'image/gif': $extensao = 'gif'; break;
            case 'image/webp': $extensao = 'webp'; break;
        }

        // Gerar nome único
        $nome_arquivo = $tipo . '_' . $tecnico_id . '_' . time() . '_' . uniqid() . '.' . $extensao;
        $diretorio = FCPATH . 'assets/tecnicos/fotos/' . date('Y/m');

        // Criar diretório se não existir
        log_message('info', '_salvar_foto_base64 - Diretorio alvo: ' . $diretorio);
        if (!is_dir($diretorio)) {
            if (!mkdir($diretorio, 0755, true)) {
                log_message('error', '_salvar_foto_base64 - Falha ao criar diretorio: ' . $diretorio . ' - erro: ' . error_get_last()['message']);
                return false;
            }
            log_message('info', '_salvar_foto_base64 - Diretorio criado: ' . $diretorio);
        }

        if (!is_writable($diretorio)) {
            log_message('error', '_salvar_foto_base64 - Diretorio sem permissao de escrita: ' . $diretorio);
            return false;
        }

        $caminho_completo = $diretorio . '/' . $nome_arquivo;
        $caminho_relativo = 'assets/tecnicos/fotos/' . date('Y/m') . '/' . $nome_arquivo;

        // Salvar arquivo
        log_message('info', '_salvar_foto_base64 - Tentando salvar em: ' . $caminho_completo);
        $resultado = file_put_contents($caminho_completo, $imagem);

        if ($resultado === false || $resultado === 0) {
            log_message('error', '_salvar_foto_base64 - Falha ao salvar arquivo: ' . $caminho_completo . ' - resultado: ' . var_export($resultado, true));
            return false;
        }
        log_message('info', '_salvar_foto_base64 - Bytes escritos: ' . $resultado);

        log_message('info', '_salvar_foto_base64 - Foto salva com sucesso: ' . $caminho_relativo);
        return $caminho_relativo;
    }
}
