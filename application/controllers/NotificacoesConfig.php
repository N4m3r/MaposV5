<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class NotificacoesConfig extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notificacoes_config_model');
        $this->load->model('notificacoes_templates_model');
        $this->load->model('notificacoes_log_model');
        $this->load->model('clientes_model');
        $this->load->helper('notificacoes');
        $this->load->service('WhatsAppService');

        // Verificar permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar configurações de notificações.');
            redirect(base_url());
        }
    }

    /**
     * Página principal - redireciona para configurações
     */
    public function index()
    {
        redirect('notificacoesConfig/configuracoes');
    }

    /**
     * Configurações do WhatsApp
     */
    public function configuracoes()
    {
        $config = $this->notificacoes_config_model->getConfig();

        if ($this->input->post()) {
            $dados = [
                'whatsapp_provedor' => $this->input->post('whatsapp_provedor'),
                'whatsapp_ativo' => $this->input->post('whatsapp_ativo') ? 1 : 0,
                'evolution_url' => $this->input->post('evolution_url'),
                'evolution_apikey' => $this->input->post('evolution_apikey'),
                'evolution_instance' => $this->input->post('evolution_instance') ?: 'mapos',
                'meta_phone_number_id' => $this->input->post('meta_phone_number_id'),
                'meta_access_token' => $this->input->post('meta_access_token'),
                'z_api_url' => $this->input->post('z_api_url'),
                'z_api_token' => $this->input->post('z_api_token'),
                'notificacao_os_criada' => $this->input->post('notificacao_os_criada') ? 1 : 0,
                'notificacao_os_atualizada' => $this->input->post('notificacao_os_atualizada') ? 1 : 0,
                'notificacao_os_pronta' => $this->input->post('notificacao_os_pronta') ? 1 : 0,
                'notificacao_os_orcamento' => $this->input->post('notificacao_os_orcamento') ? 1 : 0,
                'notificacao_venda_realizada' => $this->input->post('notificacao_venda_realizada') ? 1 : 0,
                'notificacao_cobranca_gerada' => $this->input->post('notificacao_cobranca_gerada') ? 1 : 0,
                'notificacao_cobranca_vencimento' => $this->input->post('notificacao_cobranca_vencimento') ? 1 : 0,
                'notificacao_lembrete_aniversario' => $this->input->post('notificacao_lembrete_aniversario') ? 1 : 0,
                'horario_envio_inicio' => $this->input->post('horario_envio_inicio') ?: '08:00:00',
                'horario_envio_fim' => $this->input->post('horario_envio_fim') ?: '18:00:00',
                'enviar_fim_semana' => $this->input->post('enviar_fim_semana') ? 1 : 0,
                'respeitar_horario' => $this->input->post('respeitar_horario') ? 1 : 0,
            ];

            if ($this->notificacoes_config_model->salvar($dados)) {
                $this->session->set_flashdata('success', 'Configurações salvas com sucesso!');
                log_info('Atualizou configurações de notificações');
            } else {
                $this->session->set_flashdata('error', 'Erro ao salvar configurações.');
            }

            redirect(current_url());
        }

        // Verificar status da conexão
        $statusConexao = null;
        if ($config->whatsapp_ativo && $config->whatsapp_provedor == 'evolution') {
            $service = new WhatsAppService();
            $statusConexao = $service->verificarConexao();
        }

        $this->data['config'] = $config;
        $this->data['statusConexao'] = $statusConexao;
        $this->data['view'] = 'notificacoes/configuracoes';
        $this->data['menuConfiguracoes'] = 'Notificações';

        return $this->layout();
    }

    /**
     * Obter QR Code (Evolution API)
     */
    public function obter_qr()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $service = new WhatsAppService();
        $resultado = $service->obterQRCode();

        echo json_encode($resultado);
    }

    /**
     * Verificar status da conexão
     */
    public function verificar_status()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $service = new WhatsAppService();
        $resultado = $service->verificarConexao();

        echo json_encode($resultado);
    }

    /**
     * Desconectar WhatsApp (Evolution)
     */
    public function desconectar()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $service = new WhatsAppService();
        $resultado = $service->desconectar();

        echo json_encode($resultado);
    }

    /**
     * Testar envio de mensagem
     */
    public function testar_envio()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $numero = $this->input->post('numero');
        $mensagem = $this->input->post('mensagem') ?: 'Teste de envio do sistema MAPOS!';

        if (empty($numero)) {
            echo json_encode(['success' => false, 'error' => 'Número não informado']);
            return;
        }

        $service = new WhatsAppService();
        $resultado = $service->enviarMensagem($numero, $mensagem);

        echo json_encode($resultado);
    }

    /**
     * Listar templates
     */
    public function templates()
    {
        $this->data['templates'] = $this->notificacoes_templates_model->listar();
        $this->data['categorias'] = [
            'os' => 'Ordens de Serviço',
            'venda' => 'Vendas',
            'cobranca' => 'Cobranças',
            'marketing' => 'Marketing',
            'sistema' => 'Sistema'
        ];
        $this->data['view'] = 'notificacoes/templates';
        $this->data['menuConfiguracoes'] = 'Notificações';

        return $this->layout();
    }

    /**
     * Editar template
     */
    public function editar_template($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Template não especificado.');
            redirect('notificacoesConfig/templates');
        }

        $template = $this->notificacoes_templates_model->getById($id);

        if (!$template) {
            $this->session->set_flashdata('error', 'Template não encontrado.');
            redirect('notificacoesConfig/templates');
        }

        if ($this->input->post()) {
            $dados = [
                'id' => $id,
                'nome' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'mensagem' => $this->input->post('mensagem'),
                'assunto' => $this->input->post('assunto'),
                'ativo' => $this->input->post('ativo') ? 1 : 0,
            ];

            if ($this->notificacoes_templates_model->salvar($dados)) {
                $this->session->set_flashdata('success', 'Template atualizado com sucesso!');
                log_info('Editou template de notificação: ' . $template->chave);
            } else {
                $this->session->set_flashdata('error', 'Erro ao salvar template.');
            }

            redirect(current_url());
        }

        $this->data['template'] = $template;
        $this->data['variaveis'] = $this->notificacoes_templates_model->getVariaveis($template->chave);
        $this->data['view'] = 'notificacoes/templates_editar';
        $this->data['menuConfiguracoes'] = 'Notificações';

        return $this->layout();
    }

    /**
     * Toggle ativo/inativo do template
     */
    public function toggle_template($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Template não especificado.');
            redirect('notificacoesConfig/templates');
        }

        if ($this->notificacoes_templates_model->toggleAtivo($id)) {
            $this->session->set_flashdata('success', 'Status do template alterado!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao alterar status.');
        }

        redirect('notificacoesConfig/templates');
    }

    /**
     * Preview do template
     */
    public function preview_template()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $chave = $this->input->post('chave');
        $variaveis = $this->input->post('variaveis');

        $processado = $this->notificacoes_templates_model->processarTemplate($chave, $variaveis);

        echo json_encode($processado);
    }

    /**
     * Log de notificações
     */
    public function logs()
    {
        $filtros = [
            'status' => $this->input->get('status'),
            'canal' => $this->input->get('canal'),
            'data_inicio' => $this->input->get('data_inicio'),
            'data_fim' => $this->input->get('data_fim'),
            'busca' => $this->input->get('busca'),
        ];

        // Paginação
        $this->load->library('pagination');
        $config['base_url'] = site_url('notificacoesConfig/logs');
        $config['total_rows'] = $this->notificacoes_log_model->contar($filtros);
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $this->data['logs'] = $this->notificacoes_log_model->listar($filtros, $config['per_page'], $page);
        $this->data['estatisticas'] = $this->notificacoes_log_model->getEstatisticas(30);
        $this->data['filtros'] = $filtros;
        $this->data['view'] = 'notificacoes/logs';
        $this->data['menuConfiguracoes'] = 'Notificações';

        return $this->layout();
    }

    /**
     * Estatísticas
     */
    public function estatisticas()
    {
        $this->data['estatisticas'] = $this->notificacoes_log_model->getEstatisticas(30);
        $this->data['view'] = 'notificacoes/estatisticas';
        $this->data['menuConfiguracoes'] = 'Notificações';

        return $this->layout();
    }

    /**
     * Enviar mensagem manual
     */
    public function enviar_manual()
    {
        if ($this->input->post()) {
            $cliente_id = $this->input->post('cliente_id');
            $telefone = $this->input->post('telefone');
            $mensagem = $this->input->post('mensagem');

            if (empty($telefone) || empty($mensagem)) {
                $this->session->set_flashdata('error', 'Telefone e mensagem são obrigatórios.');
                redirect(current_url());
            }

            $this->load->helper('notificacoes');
            $resultado = notificar_whatsapp('manual', [
                'mensagem' => $mensagem
            ], [
                'cliente_id' => $cliente_id,
                'telefone' => $telefone
            ]);

            if ($resultado['success']) {
                $this->session->set_flashdata('success', 'Mensagem enviada com sucesso!');
                log_info('Enviou mensagem WhatsApp manual para: ' . $telefone);
            } else {
                $this->session->set_flashdata('error', 'Erro ao enviar: ' . $resultado['error']);
            }

            redirect(current_url());
        }

        $this->data['clientes'] = $this->clientes_model->getAll();
        $this->data['view'] = 'notificacoes/enviar_manual';
        $this->data['menuConfiguracoes'] = 'Notificações';

        return $this->layout();
    }
}
