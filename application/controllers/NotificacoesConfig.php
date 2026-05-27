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

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para acessar configuracoes de notificacoes.');
            redirect(base_url());
        }
    }

    public function index()
    {
        redirect('notificacoesConfig/configuracoes');
    }

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
                $this->session->set_flashdata('success', 'Configuracoes salvas com sucesso!');
            } else {
                $this->session->set_flashdata('error', 'Erro ao salvar configuracoes.');
            }

            redirect(current_url());
        }

        $statusConexao = null;
        if (isset($config->whatsapp_ativo) && $config->whatsapp_ativo && ($config->whatsapp_provedor ?? '') === 'evolution') {
            $this->load->library('WhatsAppService');
            $service = new WhatsAppService();
            $statusConexao = $service->verificarConexao();
        }

        $this->data['config'] = $config;
        $this->data['statusConexao'] = $statusConexao;
        $this->data['view'] = 'notificacoes/configuracoes';
        $this->data['menuConfiguracoes'] = 'Notificacoes';

        return $this->layout();
    }

    public function obter_qr()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->library('WhatsAppService');
        $service = new WhatsAppService();
        echo json_encode($service->obterQRCode());
    }

    public function verificar_status()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->library('WhatsAppService');
        $service = new WhatsAppService();
        echo json_encode($service->verificarConexao());
    }

    public function desconectar()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->library('WhatsAppService');
        $service = new WhatsAppService();
        echo json_encode($service->desconectar());
    }

    public function testar_envio()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $numero = $this->input->post('numero');
        $mensagem = $this->input->post('mensagem') ?: 'Teste de envio do sistema MAPOS!';

        if (empty($numero)) {
            echo json_encode(['success' => false, 'error' => 'Numero nao informado']);
            return;
        }

        $this->load->library('WhatsAppService');
        $service = new WhatsAppService();
        echo json_encode($service->enviarMensagem($numero, $mensagem));
    }

    public function templates()
    {
        $this->data['templates'] = $this->notificacoes_templates_model->listar();
        $this->data['categorias'] = [
            'os' => 'Ordens de Servico',
            'venda' => 'Vendas',
            'cobranca' => 'Cobrancas',
            'marketing' => 'Marketing',
            'sistema' => 'Sistema',
            'personalizado' => 'Personalizados',
        ];
        $this->data['view'] = 'notificacoes/templates';
        $this->data['menuConfiguracoes'] = 'Notificacoes';

        return $this->layout();
    }

    public function adicionar_template()
    {
        if ($this->input->post()) {
            $chave = $this->input->post('chave');

            if (empty($chave) || empty($this->input->post('nome')) || empty($this->input->post('mensagem'))) {
                $this->session->set_flashdata('error', 'Preencha todos os campos obrigatorios.');
                redirect(current_url());
            }

            if ($this->notificacoes_templates_model->getByChave($chave)) {
                $this->session->set_flashdata('error', 'Esta chave ja esta em uso.');
                redirect(current_url());
            }

            if (!preg_match('/^[a-z0-9_]+$/', $chave)) {
                $this->session->set_flashdata('error', 'A chave deve conter apenas letras minusculas, numeros e underline.');
                redirect(current_url());
            }

            $dados = [
                'chave' => $chave,
                'nome' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'categoria' => $this->input->post('categoria') ?: 'personalizado',
                'canal' => $this->input->post('canal') ?: 'whatsapp',
                'assunto' => $this->input->post('assunto'),
                'mensagem' => $this->input->post('mensagem'),
                'variaveis' => json_encode([]),
                'ativo' => $this->input->post('ativo') ? 1 : 0,
                'e_marketing' => $this->input->post('e_marketing') ? 1 : 0,
            ];

            if ($this->notificacoes_templates_model->salvar($dados)) {
                $this->session->set_flashdata('success', 'Template criado com sucesso!');
                redirect('notificacoesConfig/templates');
            } else {
                $this->session->set_flashdata('error', 'Erro ao criar template.');
            }
        }

        $this->data['categorias'] = [
            'os' => 'Ordens de Servico',
            'venda' => 'Vendas',
            'cobranca' => 'Cobrancas',
            'marketing' => 'Marketing',
            'sistema' => 'Sistema',
            'personalizado' => 'Personalizados',
        ];
        $this->data['variaveis_globais'] = $this->notificacoes_templates_model->getVariaveisGlobais();
        $this->data['view'] = 'notificacoes/templates_adicionar';
        $this->data['menuConfiguracoes'] = 'Notificacoes';

        return $this->layout();
    }

    public function editar_template($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Template nao especificado.');
            redirect('notificacoesConfig/templates');
        }

        $template = $this->notificacoes_templates_model->getById($id);
        if (!$template) {
            $this->session->set_flashdata('error', 'Template nao encontrado.');
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

            $chavesPadrao = $this->notificacoes_templates_model->getChavesPadrao();
            if (!in_array($template->chave, $chavesPadrao)) {
                $dados['categoria'] = $this->input->post('categoria');
                $dados['canal'] = $this->input->post('canal');
                $dados['e_marketing'] = $this->input->post('e_marketing') ? 1 : 0;
            }

            if ($this->notificacoes_templates_model->salvar($dados)) {
                $this->session->set_flashdata('success', 'Template atualizado com sucesso!');
            } else {
                $this->session->set_flashdata('error', 'Erro ao salvar template.');
            }

            redirect(current_url());
        }

        $this->data['template'] = $template;
        $this->data['variaveis'] = $this->notificacoes_templates_model->getVariaveis($template->chave);
        $this->data['categorias'] = [
            'os' => 'Ordens de Servico',
            'venda' => 'Vendas',
            'cobranca' => 'Cobrancas',
            'marketing' => 'Marketing',
            'sistema' => 'Sistema',
            'personalizado' => 'Personalizados',
        ];
        $this->data['variaveis_globais'] = $this->notificacoes_templates_model->getVariaveisGlobais();
        $this->data['view'] = 'notificacoes/templates_editar';
        $this->data['menuConfiguracoes'] = 'Notificacoes';

        return $this->layout();
    }

    public function toggle_template($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Template nao especificado.');
            redirect('notificacoesConfig/templates');
        }

        if ($this->notificacoes_templates_model->toggleAtivo($id)) {
            $this->session->set_flashdata('success', 'Status do template alterado!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao alterar status.');
        }

        redirect('notificacoesConfig/templates');
    }

    public function preview_template()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $chave = $this->input->post('chave');
        $variaveis = $this->input->post('variaveis');

        echo json_encode($this->notificacoes_templates_model->processarTemplate($chave, $variaveis));
    }

    public function logs()
    {
        $filtros = [
            'status' => $this->input->get('status'),
            'canal' => $this->input->get('canal'),
            'data_inicio' => $this->input->get('data_inicio'),
            'data_fim' => $this->input->get('data_fim'),
            'busca' => $this->input->get('busca'),
        ];

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
        $this->data['menuConfiguracoes'] = 'Notificacoes';

        return $this->layout();
    }

    public function estatisticas()
    {
        $this->data['estatisticas'] = $this->notificacoes_log_model->getEstatisticas(30);
        $this->data['view'] = 'notificacoes/estatisticas';
        $this->data['menuConfiguracoes'] = 'Notificacoes';

        return $this->layout();
    }

    public function enviar_manual()
    {
        if ($this->input->post()) {
            $telefone = $this->input->post('telefone');
            $mensagem = $this->input->post('mensagem');

            if (empty($telefone) || empty($mensagem)) {
                $this->session->set_flashdata('error', 'Telefone e mensagem sao obrigatorios.');
                redirect(current_url());
            }

            $this->load->library('WhatsAppService');
            $service = new WhatsAppService();
            $resultado = $service->enviarMensagem($telefone, $mensagem);

            if ($resultado['success']) {
                $this->session->set_flashdata('success', 'Mensagem enviada com sucesso!');
            } else {
                $this->session->set_flashdata('error', 'Erro ao enviar: ' . ($resultado['error'] ?? 'Erro desconhecido'));
            }

            redirect(current_url());
        }

        $this->data['clientes'] = $this->clientes_model->getAll();
        $this->data['view'] = 'notificacoes/enviar_manual';
        $this->data['menuConfiguracoes'] = 'Notificacoes';

        return $this->layout();
    }
}