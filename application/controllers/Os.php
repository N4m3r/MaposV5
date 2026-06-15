<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . 'libraries/Webhooks/WebhookManager.php';
require_once APPPATH . 'traits/Os/OsEmailTrait.php';
require_once APPPATH . 'traits/Os/OsAutocompleteTrait.php';
require_once APPPATH . 'traits/Os/OsAttachmentTrait.php';
require_once APPPATH . 'traits/Os/OsItemTrait.php';
require_once APPPATH . 'traits/Os/OsValidationTrait.php';
require_once APPPATH . 'traits/LegacyJsonResponseTrait.php';
require_once APPPATH . 'traits/ApiCrudTrait.php';

use Libraries\Webhooks\WebhookManager;
use Application\Traits\LegacyJsonResponseTrait;
use Application\Traits\Os\OsEmailTrait;
use Application\Traits\Os\OsAutocompleteTrait;
use Application\Traits\Os\OsAttachmentTrait;
use Application\Traits\Os\OsItemTrait;
use Application\Traits\Os\OsValidationTrait;

class Os extends MY_Controller
{
    use ApiCrudTrait;

    protected $api_table = 'os';
    protected $api_search_fields = ['idOs'];
    protected $api_default_order = ['idOs', 'desc'];
    protected $api_required_permission = 'vOs';

    private WebhookManager $webhookManager;

    use OsEmailTrait;
    use OsAutocompleteTrait;
    use OsAttachmentTrait;
    use OsItemTrait;
    use OsValidationTrait;
    use LegacyJsonResponseTrait;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('os_model');
        $this->load->model('notificacoes_model');
        $this->notificacoes_model->ensureTableExists();
        $this->data['menuOs'] = 'OS';
        $this->webhookManager = new WebhookManager();
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->load->library('pagination');
        $this->load->model('mapos_model');

        $where_array = [];

        $pesquisa = $this->input->get('pesquisa');
        $status = $this->input->get('status');
        $inputDe = $this->input->get('data');
        $inputAte = $this->input->get('data2');

        if ($pesquisa) {
            $where_array['pesquisa'] = $pesquisa;
        }
        if ($inputDe) {
            $de = parseDateBr($inputDe);
            if ($de) {
                $where_array['de'] = $de;
            }
        }
        if ($inputAte) {
            $ate = parseDateBr($inputAte);
            if ($ate) {
                $where_array['ate'] = $ate;
            }
        }

        // Verificar se é técnico - se sim, filtrar apenas OS atribuídas a ele
        $permissao = $this->session->userdata('permissao');
        $idUsuario = $this->session->userdata('id_admin');

        // Se tem permissão de técnico específica mas NÃO tem permissão de OS geral (eOs)
        if ($this->permission->checkPermission($permissao, 'vTecnicoOS') &&
            !$this->permission->checkPermission($permissao, 'eOs')) {
            $where_array['tecnico_responsavel'] = $idUsuario;
        }

        // Quando filtro de status é aplicado, usa o valor direto
        if ($status) {
            $where_array['status'] = $status;
        }
        // Quando não há filtro de status, mostra TODAS as OS (sem filtro de status padrão)

        $this->data['configuration']['base_url'] = site_url('os/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->os_model->countOs($where_array);
        if(count($where_array) > 0) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}&status={$status}&data={$inputDe}&data2={$inputAte}";
            $this->data['configuration']['first_url'] = base_url("index.php/os/gerenciar")."\?pesquisa={$pesquisa}&status={$status}&data={$inputDe}&data2={$inputAte}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->os_model->getOs(
            'os',
            'os.*,
            COALESCE((SELECT SUM(produtos_os.preco * produtos_os.quantidade ) FROM produtos_os WHERE produtos_os.os_id = os.idOs), 0) totalProdutos,
            COALESCE((SELECT SUM(servicos_os.preco * servicos_os.quantidade ) FROM servicos_os WHERE servicos_os.os_id = os.idOs), 0) totalServicos',
            $where_array,
            $this->data['configuration']['per_page'],
            $this->uri->segment(3)
        );

        $this->data['texto_de_notificacao'] = $this->data['configuration']['notifica_whats'];
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['view'] = 'os/os';

        return $this->layout();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar O.S.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('os') == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {
            $dataInicial = $this->input->post('dataInicial');
            $dataFinal = $this->input->post('dataFinal');
            $termoGarantiaId = $this->input->post('termoGarantia');

            $dataInicial = parseDateBr($dataInicial, date('Y-m-d'));
            $dataFinal = $dataFinal ? parseDateBr($dataFinal, date('Y-m-d')) : date('Y-m-d');

            $termoGarantiaId = (! $termoGarantiaId == null || ! $termoGarantiaId == '')
                ? $this->input->post('garantias_id')
                : null;

            $data = [
                'dataInicial' => $dataInicial,
                'clientes_id' => $this->input->post('clientes_id'), //set_value('idCliente'),
                'usuarios_id' => $this->input->post('usuarios_id'), //set_value('idUsuario'),
                'dataFinal' => $dataFinal,
                'garantia' => set_value('garantia'),
                'garantias_id' => $termoGarantiaId,
                'descricaoProduto' => $this->input->post('descricaoProduto'),
                'defeito' => $this->input->post('defeito'),
                'status' => set_value('status'),
                'observacoes' => $this->input->post('observacoes'),
                'laudoTecnico' => $this->input->post('laudoTecnico'),
                'faturado' => 0,
            ];

            $this->db->trans_start();
            $id = $this->os_model->add('os', $data, true);
            $this->db->trans_complete();

            if ($this->db->trans_status() !== false && is_numeric($id)) {
                $this->load->model('mapos_model');
                $this->load->model('usuarios_model');

                $idOs = $id;
                $os = $this->os_model->getById($idOs);
                $emitente = $this->mapos_model->getEmitente();

                $tecnico = $this->usuarios_model->getById($os->usuarios_id);

                // Verificar configuração de notificação
                if ($this->data['configuration']['os_notification'] != 'nenhum' && $this->data['configuration']['email_automatico'] == 1) {
                    $remetentes = resolveEmailRecipients(
                        $this->data['configuration']['os_notification'],
                        $os->email,
                        $tecnico->email ?? '',
                        $emitente->email ?? ''
                    );
                    $this->enviarOsPorEmail($idOs, $remetentes, 'Ordem de Serviço - Criada');
                }

                // Enfileirar email via sistema V5
                if ($this->data['configuration']['email_automatico_v5'] ?? true) {
                    $this->db->where('config', 'email_template_os_criada');
                    $templateRow = $this->db->get('configuracoes')->row();
                    $template = $templateRow && !empty($templateRow->valor) ? $templateRow->valor : 'os_nova';
                    $this->queueEmailV5($os, $template, 'Ordem de Serviço - Criada', 2);

                    // Agenda lembrete de vencimento (2 dias antes)
                    if (!empty($os->dataFinal) && !empty($os->email)) {
                        try {
                            require_once APPPATH . 'libraries/Scheduler/AutoEvents.php';
                            $autoEvents = new \Libraries\Scheduler\AutoEvents();
                            $autoEvents->scheduleOsVencendo($id, $os->dataFinal, $os->email);
                        } catch (\Exception $e) {
                            log_message('error', '[AutoEvents] Erro ao agendar lembrete OS: ' . $e->getMessage());
                        }
                    }
                }

                $this->session->set_flashdata('success', 'OS adicionada com sucesso, você pode adicionar produtos ou serviços a essa OS nas abas de Produtos e Serviços!');
                log_info('Adicionou uma OS. ID: ' . $id);

                $this->notificacoes_model->notificarTodos([
                    'titulo' => 'Nova OS Criada',
                    'mensagem' => 'OS #' . $id . ' criada por ' . $this->session->userdata('nome_admin'),
                    'url' => site_url('os/editar/' . $id),
                    'icone' => 'bx-file',
                    'tipo' => 'info',
                ]);

                // Gatilho webhook: OS criada
                $this->webhookManager->trigger('os.created', [
                    'id' => $id,
                    'cliente_id' => $data['clientes_id'],
                    'tecnico_id' => $data['usuarios_id'],
                    'status' => $data['status'],
                    'dataInicial' => $data['dataInicial'],
                    'dataFinal' => $data['dataFinal'],
                ]);

                redirect(site_url('os/editar/') . $id);
            } else {
                $this->data['custom_error'] = '<div class="alert">Ocorreu um erro.</div>';
            }
        }

        $this->data['view'] = 'os/adicionarOs';

        return $this->layout();
    }

    public function editar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar O.S.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $this->data['texto_de_notificacao'] = $this->data['configuration']['notifica_whats'];

        $this->data['editavel'] = $this->os_model->isEditable($this->input->post('idOs'));
        if (! $this->data['editavel']) {
            $this->session->set_flashdata('error', 'Esta OS já e seu status não pode ser alterado e nem suas informações atualizadas. Por favor abrir uma nova OS.');

            redirect(site_url('os'));
        }

        if ($this->form_validation->run('os') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $dataInicial = $this->input->post('dataInicial');
            $dataFinal = $this->input->post('dataFinal');
            $termoGarantiaId = $this->input->post('garantias_id') ?: null;

            $dataInicial = parseDateBr($dataInicial, date('Y-m-d'));
            $dataFinal = parseDateBr($dataFinal, date('Y-m-d'));

            $data = [
                'dataInicial' => $dataInicial,
                'dataFinal' => $dataFinal,
                'garantia' => $this->input->post('garantia'),
                'garantias_id' => $termoGarantiaId,
                'descricaoProduto' => $this->input->post('descricaoProduto'),
                'defeito' => $this->input->post('defeito'),
                'status' => $this->input->post('status'),
                'observacoes' => $this->input->post('observacoes'),
                'laudoTecnico' => $this->input->post('laudoTecnico'),
                'usuarios_id' => $this->input->post('usuarios_id'),
                'clientes_id' => $this->input->post('clientes_id'),
            ];
            $os = $this->os_model->getById($this->input->post('idOs'));

            //Verifica para poder fazer a devolução do produto para o estoque caso OS seja cancelada.

            if (strtolower($this->input->post('status')) == 'cancelado' && strtolower($os->status) != 'cancelado') {
                $this->devolucaoEstoque($this->input->post('idOs'));
            }

            if (strtolower($os->status) == 'cancelado' && strtolower($this->input->post('status')) != 'cancelado') {
                $this->debitarEstoque($this->input->post('idOs'));
            }

            if ($this->os_model->edit('os', $data, 'idOs', $this->input->post('idOs')) == true) {
                $this->load->model('mapos_model');
                $this->load->model('usuarios_model');

                $idOs = $this->input->post('idOs');

                $os = $this->os_model->getById($idOs);
                $emitente = $this->mapos_model->getEmitente();
                $tecnico = $this->usuarios_model->getById($os->usuarios_id);

                // Integrar OS ao DRE automaticamente quando status for Finalizado ou Faturado
                $novoStatus = strtolower($this->input->post('status'));
                if (in_array($novoStatus, ['finalizado', 'faturado'])) {
                    $this->load->model('dre_model');
                    $this->dre_model->integrarOS($idOs);
                }

                // Verificar configuração de notificação
                if ($this->data['configuration']['os_notification'] != 'nenhum' && $this->data['configuration']['email_automatico'] == 1) {
                    $remetentes = resolveEmailRecipients(
                        $this->data['configuration']['os_notification'],
                        $os->email,
                        $tecnico->email ?? '',
                        $emitente->email ?? ''
                    );
                    $this->enviarOsPorEmail($idOs, $remetentes, 'Ordem de Serviço - Editada');
                }

                // Enfileirar email via sistema V5
                if ($this->data['configuration']['email_automatico_v5'] ?? true) {
                    $this->db->where('config', 'email_template_os_editada');
                    $templateRow = $this->db->get('configuracoes')->row();
                    $template = $templateRow && !empty($templateRow->valor) ? $templateRow->valor : 'os_atualizada';
                    $this->queueEmailV5($os, $template, 'Ordem de Serviço - Atualizada', 2);
                }

                $this->session->set_flashdata('success', 'Os editada com sucesso!');
                log_info('Alterou uma OS. ID: ' . $this->input->post('idOs'));

                $statusAnterior = isset($os->status) ? $os->status : '';

                $novoStatus = $this->input->post('status');

                // Gatilho webhook: OS atualizada
                $this->webhookManager->trigger('os.updated', [
                    'id' => $idOs,
                    'cliente_id' => $data['clientes_id'],
                    'tecnico_id' => $data['usuarios_id'],
                    'status' => $novoStatus,
                    'status_anterior' => $statusAnterior,
                    'dataInicial' => $data['dataInicial'],
                    'dataFinal' => $data['dataFinal'],
                ]);

                if ($novoStatus && $novoStatus !== $statusAnterior) {
                    $this->notificacoes_model->notificarTodos([
                        'titulo' => 'OS - Status Alterado',
                        'mensagem' => 'OS #' . $this->input->post('idOs') . ': ' . $statusAnterior . ' → ' . $novoStatus,
                        'url' => site_url('os/editar/' . $this->input->post('idOs')),
                        'icone' => 'bx-refresh',
                        'tipo' => 'warning',
                    ]);

                    // Notificar cliente via WhatsApp
                    $this->load->library('Whatsapp_notifier');
                    $this->whatsapp_notifier->notificarStatusOs($idOs, $novoStatus, $statusAnterior);

                    // Gatilho webhook: status alterado
                    $this->webhookManager->trigger('os.status_changed', [
                        'id' => $idOs,
                        'cliente_id' => $data['clientes_id'],
                        'tecnico_id' => $data['usuarios_id'],
                        'status' => $novoStatus,
                        'status_anterior' => $statusAnterior,
                    ]);
                }

                // Gatilho webhook: OS finalizada
                if (in_array(strtolower($novoStatus), ['finalizado', 'faturado'])) {
                    $this->webhookManager->trigger('os.finished', [
                        'id' => $idOs,
                        'cliente_id' => $data['clientes_id'],
                        'tecnico_id' => $data['usuarios_id'],
                        'status' => $novoStatus,
                    ]);

                    // Pesquisa de satisfacao via WhatsApp
                    $this->whatsapp_notifier->enviarPesquisaSatisfacao($idOs);
                }

                redirect(site_url('os/editar/') . $this->input->post('idOs'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));

        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));
        $this->data['anotacoes'] = $this->os_model->getAnotacoes($this->uri->segment(3));

        if ($return = $this->os_model->valorTotalOS($this->uri->segment(3))) {
            $this->data['totalServico'] = $return['totalServico'];
            $this->data['totalProdutos'] = $return['totalProdutos'];
        }

        $this->load->model('mapos_model');
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        $this->data['view'] = 'os/editarOs';

        return $this->layout();
    }

    public function visualizar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        $os_id = $this->uri->segment(3);

        // Verificar permissão básica
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        // Carregar dados da OS primeiro para verificar atribuição ao técnico
        $this->load->model('mapos_model');
        $this->load->model('checkin_model');
        $this->load->model('assinaturas_model');
        $this->load->model('fotosatendimento_model');
        $this->load->model('tec_os_model');

        $result = $this->os_model->getById($os_id);

        // Verificar se é técnico com permissão específica - só pode ver OS atribuídas a ele
        $permissao = $this->session->userdata('permissao');
        $idUsuario = $this->session->userdata('id_admin');

        // Se tem permissão de técnico específica (vTecnicoOS) mas NÃO tem permissão de OS geral (vOs completo)
        if ($this->permission->checkPermission($permissao, 'vTecnicoOS') &&
            !$this->permission->checkPermission($permissao, 'eOs')) {

            // Verificar se a OS está atribuída a este técnico
            if (!$result || $result->tecnico_responsavel != $idUsuario) {
                $this->session->set_flashdata('error', 'Você só pode visualizar ordens de serviço atribuídas a você.');
                redirect('tecnico');
            }
        }

        $this->data['custom_error'] = '';
        $this->data['texto_de_notificacao'] = $this->data['configuration']['notifica_whats'];

        $this->data['result'] = $result;
        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));
        $this->data['anotacoes'] = $this->os_model->getAnotacoes($this->uri->segment(3));
        $this->data['editavel'] = $this->os_model->isEditable($this->uri->segment(3));

        // Carregar documentos fiscais vinculados à OS (cobranças, NFS-e, impostos)
        $this->data['documentos_fiscais'] = $this->os_model->getDocumentosFiscais($this->uri->segment(3));

        // Dados do sistema de checkin
        $os_id = $this->uri->segment(3);
        $this->data['checkins'] = $this->checkin_model->getAllByOs($os_id);
        $this->data['checkinAtivo'] = $this->checkin_model->getCheckinAtivo($os_id);
        $this->data['assinaturas'] = $this->assinaturas_model->getByOs($os_id);
        log_info('OS Visualizar - Assinaturas carregadas: ' . count($this->data['assinaturas']));
        $this->data['fotosAtendimento'] = $this->fotosatendimento_model->getByOs($os_id);

        // Carregar execuções técnicas do portal (tec_os_execucao)
        $this->data['execucoesTecnicas'] = $this->tec_os_model->getExecucoesByOs($os_id);
        $this->data['fotosTecnico'] = $this->tec_os_model->getFotosByOs($os_id);
        $this->data['qrCode'] = $this->os_model->getQrCode(
            $this->uri->segment(3),
            $this->data['configuration']['pix_key'],
            $this->data['emitente']
        );
        $this->data['modalGerarPagamento'] = $this->load->view(
            'cobrancas/modalGerarPagamento',
            [
                'id' => $this->uri->segment(3),
                'tipo' => 'os',
            ],
            true
        );
        $this->data['view'] = 'os/visualizarOs';
        $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);

        if ($return = $this->os_model->valorTotalOS($this->uri->segment(3))) {
            $this->data['totalServico'] = $return['totalServico'];
            $this->data['totalProdutos'] = $return['totalProdutos'];
            // Garantir valor_desconto disponivel para wizard NFS-e
            $this->data['result']->valor_desconto = $return['valor_desconto'] ?? ($this->data['result']->valor_desconto ?? 0);
        }

        // Carregar dados de NFSe e Boleto
        $this->load->model('nfse_emitida_model');
        $this->load->model('boleto_os_model');
        $this->data['nfse_atual'] = $this->nfse_emitida_model->getByOsId($os_id);
        $this->data['boleto_atual'] = $this->boleto_os_model->getAtivoByOsId($os_id);
        $this->data['historico_nfse'] = $this->nfse_emitida_model->getAllByOsId($os_id);
        $this->data['historico_boleto'] = $this->boleto_os_model->getAllByOsId($os_id);

        // Verificar se existe NFSe importada (XML externo) vinculada a esta OS
        if ($this->db->table_exists('certificado_nfe_importada')) {
            $this->data['nfse_importada'] = $this->db->where('os_id', $os_id)->order_by('id', 'DESC')->get('certificado_nfe_importada')->row();
        } else {
            $this->data['nfse_importada'] = null;
        }

        // Se nao houver NFSe emitida pelo sistema, mas houver importada,
        // normalizar para formato compativel com a view nfse_content e boleto_content
        if (empty($this->data['nfse_atual']) && !empty($this->data['nfse_importada'])) {
            $imp = $this->data['nfse_importada'];
            $nf = new stdClass();
            $nf->id = $imp->id;
            $nf->numero_nfse = $imp->numero;
            $nf->data_emissao = $imp->data_emissao;
            $nf->chave_acesso = $imp->chave_acesso ?? null;
            $nf->protocolo = null;
            $nf->situacao = $imp->situacao ?: 'Autorizada';
            $nf->valor_servicos = floatval($imp->valor_total ?? 0);
            $nf->valor_liquido = floatval($imp->valor_total ?? 0);
            $nf->valor_total_impostos = floatval($imp->valor_impostos ?? 0);
            $nf->valor_total_retencao = 0.00;
            $nf->valor_retencao_iss = 0.00;
            $nf->valor_retencao_irrf = 0.00;
            $nf->valor_retencao_pis = 0.00;
            $nf->valor_retencao_cofins = 0.00;
            $nf->valor_retencao_csll = 0.00;
            $nf->competencia = $imp->data_emissao ? date('Y-m-01', strtotime($imp->data_emissao)) : date('Y-m-01');
            $nf->url_danfe = null;
            $nf->link_impressao = null;
            $nf->xml_dps = null;
            $nf->xml_nfse = null;
            $nf->is_importada = true;
            $this->data['nfse_atual'] = $nf;
        }

        // DEBUG: logar estado da NFSe para diagnosticar exibicao na view
        $nfseDebug = $this->data['nfse_atual'];
        log_message('debug', 'OS Visualizar NFSe Debug: os_id=' . $os_id . ' nfse_atual=' . ($nfseDebug ? 'ID=' . ($nfseDebug->id ?? '?') . ' situacao=' . ($nfseDebug->situacao ?? 'null') : 'NULL'));


        // Carregar dados tributários para wizard NFS-e
        $this->load->model('impostos_model');
        $this->data['tributacao'] = $this->impostos_model->getConfiguracaoTributacao();

        // Ambiente do certificado (homologação/produção)
        $this->load->model('certificado_model');
        $certificado_ativo = $this->certificado_model->getCertificadoAtivo();
        $this->data['ambiente_nfse'] = $certificado_ativo->ambiente ?? 'homologacao';

        return $this->layout();
    }

    public function imprimir()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $data = $this->loadOsPrintData($this->uri->segment(3));
        $this->data['result'] = $data['result'];
        $this->data['produtos'] = $data['produtos'];
        $this->data['servicos'] = $data['servicos'];
        $this->data['emitente'] = $data['emitente'];
        $this->data['assinaturas'] = $data['assinaturas'];
        $this->data['qrCode'] = $data['qrCode'];
        $this->data['chaveFormatada'] = $data['chaveFormatada'];
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));

        log_info('OS Imprimir - OS ID: ' . $this->uri->segment(3) . ' - Assinaturas: ' . count($this->data['assinaturas']));
        if (!empty($this->data['assinaturas'])) {
            foreach ($this->data['assinaturas'] as $assinatura) {
                log_info('OS Imprimir - Assinatura tipo: ' . $assinatura->tipo . ' - Caminho: ' . $assinatura->assinatura);
            }
        }

        $this->data['imprimirAnexo'] = isset($_ENV['IMPRIMIR_ANEXOS']) ? (filter_var($_ENV['IMPRIMIR_ANEXOS'] ?? false, FILTER_VALIDATE_BOOLEAN)) : false;
        $this->data['permissao_eOs'] = $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs');

        $this->load->view('os/imprimirOs', $this->data);
    }

    public function imprimirTermica()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $data = $this->loadOsPrintData($this->uri->segment(3));
        $this->data['result'] = $data['result'];
        $this->data['produtos'] = $data['produtos'];
        $this->data['servicos'] = $data['servicos'];
        $this->data['emitente'] = $data['emitente'];
        $this->data['qrCode'] = $data['qrCode'];
        $this->data['chaveFormatada'] = $data['chaveFormatada'];
        $this->data['permissao_eOs'] = $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs');

        $this->load->view('os/imprimirOsTermica', $this->data);
    }

    private function loadOsPrintData($osId)
    {
        $this->load->model('mapos_model');
        $this->load->model('assinaturas_model');

        $result = $this->os_model->getById($osId);
        $produtos = $this->os_model->getProdutos($osId);
        $servicos = $this->os_model->getServicos($osId);
        $emitente = $this->mapos_model->getEmitente();
        $assinaturas = $this->assinaturas_model->getByOs($osId);
        $qrCode = $this->os_model->getQrCode(
            $osId,
            $this->data['configuration']['pix_key'],
            $emitente
        );
        $chaveFormatada = $this->formatarChave($this->data['configuration']['pix_key']);

        return [
            'result' => $result,
            'produtos' => $produtos,
            'servicos' => $servicos,
            'emitente' => $emitente,
            'assinaturas' => $assinaturas,
            'qrCode' => $qrCode,
            'chaveFormatada' => $chaveFormatada,
        ];
    }

    public function enviar_email()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para enviar O.S. por e-mail.');
            redirect(base_url());
        }

        $this->load->model('mapos_model');
        $this->load->model('usuarios_model');
        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        if (! isset($this->data['result']->email)) {
            $this->session->set_flashdata('error', 'O cliente não tem e-mail cadastrado.');
            redirect(site_url('os'));
        }

        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        if (! isset($this->data['emitente']->email)) {
            $this->session->set_flashdata('error', 'Efetue o cadastro dos dados de emitente');
            redirect(site_url('os'));
        }

        $idOs = $this->uri->segment(3);

        $emitente = $this->data['emitente'];
        $tecnico = $this->usuarios_model->getById($this->data['result']->usuarios_id);

        // Verificar configuração de notificação
        $ValidarEmail = false;
        if ($this->data['configuration']['os_notification'] != 'nenhum') {
            $remetentes = resolveEmailRecipients(
                $this->data['configuration']['os_notification'],
                $this->data['result']->email,
                $tecnico->email ?? '',
                $emitente->email ?? ''
            );
            $ValidarEmail = in_array($this->data['result']->email, $remetentes);

            if ($ValidarEmail) {
                if (empty($this->data['result']->email) || ! filter_var($this->data['result']->email, FILTER_VALIDATE_EMAIL)) {
                    $this->session->set_flashdata('error', 'Por favor preencha o email do cliente');
                    redirect(site_url('os/visualizar/') . $this->uri->segment(3));
                }
            }

            $enviouEmail = $this->enviarOsPorEmail($idOs, $remetentes, 'Ordem de Serviço');

            if ($enviouEmail) {
                $this->session->set_flashdata('success', 'O email está sendo processado e será enviado em breve.');
                log_info('Enviou e-mail para o cliente: ' . $this->data['result']->nomeCliente . '. E-mail: ' . $this->data['result']->email);
                redirect(site_url('os'));
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao enviar e-mail.');
                redirect(site_url('os'));
            }
        }

        $this->session->set_flashdata('success', 'O sistema está com uma configuração ativada para não notificar. Entre em contato com o administrador.');
        redirect(site_url('os'));
    }

    private function devolucaoEstoque($id)
    {
        if ($produtos = $this->os_model->getProdutos($id)) {
            $this->load->model('produtos_model');
            if ($this->data['configuration']['control_estoque']) {
                foreach ($produtos as $p) {
                    $this->produtos_model->updateEstoque($p->produtos_id, $p->quantidade, '+');
                    log_info('ESTOQUE: Produto id ' . $p->produtos_id . ' voltou ao estoque. Quantidade: ' . $p->quantidade . '. Motivo: Cancelamento/Exclusão');
                }
            }
        }
    }

    private function debitarEstoque($id)
    {
        if ($produtos = $this->os_model->getProdutos($id)) {
            $this->load->model('produtos_model');
            if ($this->data['configuration']['control_estoque']) {
                foreach ($produtos as $p) {
                    $this->produtos_model->updateEstoque($p->produtos_id, $p->quantidade, '-');
                    log_info('ESTOQUE: Produto id ' . $p->produtos_id . ' baixa do estoque. Quantidade: ' . $p->quantidade . '. Motivo: Mudou status que já estava Cancelado para outro');
                }
            }
        }
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir O.S.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        $os = $this->os_model->getByIdCobrancas($id);
        if ($os == null) {
            $os = $this->os_model->getById($id);
            if ($os == null) {
                $this->session->set_flashdata('error', 'Erro ao tentar excluir OS.');
                redirect(base_url() . 'index.php/os/gerenciar/');
            }
        }

        if (isset($os->idCobranca) != null) {
            if ($os->status != 'canceled') {
                $this->session->set_flashdata('error', 'Existe uma cobrança associada a esta OS, deve cancelar e/ou excluir a cobrança primeiro!');
                redirect(site_url('os/gerenciar/'));
            }
        }

        $osStockRefund = $this->os_model->getById($id);
        //Verifica para poder fazer a devolução do produto para o estoque caso OS seja excluida.
        if (strtolower($osStockRefund->status) != 'cancelado') {
            $this->devolucaoEstoque($id);
        }

        $this->db->trans_start();

        if (isset($os->idCobranca) != null) {
            $this->os_model->delete('cobrancas', 'os_id', $id);
        }
        $this->os_model->delete('servicos_os', 'os_id', $id);
        $this->os_model->delete('produtos_os', 'os_id', $id);
        $this->os_model->delete('anexos', 'os_id', $id);
        $this->os_model->delete('os', 'idOs', $id);
        if ((int) $os->faturado === 1) {
            $this->os_model->delete('lancamentos', 'descricao', "Fatura de OS - #${id}");
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            log_message('error', 'Erro ao excluir OS. ID: ' . $id);
            $this->session->set_flashdata('error', 'Ocorreu um erro ao excluir a OS. As alterações foram revertidas.');
        } else {
            log_info('Removeu uma OS. ID: ' . $id);
            $this->session->set_flashdata('success', 'OS excluída com sucesso!');
        }
        redirect(site_url('os/gerenciar/'));
    }

    public function adicionarDesconto()
    {
        if ($this->input->post('desconto') == '') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['messages' => 'Campo desconto vazio']));
        } else {
            $idOs = $this->input->post('idOs');
            $data = [
                'tipo_desconto' => $this->input->post('tipoDesconto'),
                'desconto' => $this->input->post('desconto'),
                'valor_desconto' => $this->input->post('resultado'),
            ];
            $editavel = $this->os_model->isEditable($idOs);
            if (! $editavel) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false, 'messages' => 'Desconto nao pode ser adicionado. OS ja Faturada/Cancelada']));
            }
            if ($this->os_model->edit('os', $data, 'idOs', $idOs) == true) {
                log_info('Adicionou um desconto na OS. ID: ' . $idOs);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(201)
                    ->set_output(json_encode(['result' => true, 'messages' => 'Desconto adicionado com sucesso!']));
            } else {
                log_info('Ocorreu um erro ao tentar adicionar desconto na OS: ' . $idOs);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false, 'messages' => 'Ocorreu um erro ao tentar adicionar desconto a OS.']));
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(400)
            ->set_output(json_encode(['result' => false, 'messages' => 'Ocorreu um erro ao tentar adicionar desconto na OS.']));
    }

    public function faturar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('receita') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $vencimento = $this->input->post('vencimento');
            $recebimento = $this->input->post('recebimento');

            $vencimento = parseDateBr($vencimento, date('Y-m-d'));
            if ($recebimento != null) {
                $recebimento = parseDateBr($recebimento, date('Y-m-d'));
            }

            $os_id = $this->input->post('os_id');
            $valorTotalData = $this->os_model->valorTotalOS($os_id);

            $valorTotalServico = $valorTotalData['totalServico'];
            $valorTotalProduto = $valorTotalData['totalProdutos'];
            $valorDesconto = $valorTotalData['valor_desconto'];

            $valorTotal = $valorTotalServico + $valorTotalProduto;
            $valorTotalComDesconto = $valorTotal - $valorDesconto;

            $data = [
                'descricao' => set_value('descricao'),
                'valor' => $valorTotal,
                'tipo_desconto' => 'real',
                'desconto' => ($valorDesconto > 0) ? $valorTotalComDesconto : 0,
                'valor_desconto' => ($valorDesconto > 0) ? $valorDesconto : $valorTotal,
                'clientes_id' => $this->input->post('clientes_id'),
                'data_vencimento' => $vencimento,
                'data_pagamento' => $recebimento,
                'baixado' => $this->input->post('recebido') ?: 0,
                'cliente_fornecedor' => set_value('cliente'),
                'forma_pgto' => $this->input->post('formaPgto'),
                'tipo' => $this->input->post('tipo'),
                'observacoes' => set_value('observacoes'),
                'usuarios_id' => $this->session->userdata('id_admin'),
            ];

            $this->db->trans_start();

            $editavel = $this->os_model->isEditable($os_id);
            if (!$editavel) {
                $this->db->trans_rollback();
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false]));
            }

            if ($this->os_model->add('lancamentos', $data)) {
                $this->db->set('faturado', 1);
                $this->db->set('valorTotal', $valorTotal);

                if ($valorDesconto > 0) {
                    $this->db->set('desconto', $valorTotalComDesconto);
                    $this->db->set('valor_desconto', $valorDesconto);
                } else {
                    $this->db->set('desconto', 0);
                    $this->db->set('valor_desconto', $valorTotal);
                }

                $this->db->set('status', 'Faturado');
                $this->db->where('idOs', $os_id);
                $this->db->update('os');

                log_info('Faturou uma OS. ID: ' . $os_id);

                // Integrar OS ao DRE automaticamente
                $this->load->model('dre_model');
                $this->dre_model->integrarOS($os_id);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar faturar OS.');
                    $json = ['result' => false];
                } else {
                    $this->session->set_flashdata('success', 'OS faturada com sucesso!');
                    $json = ['result' => true];
                }
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar faturar OS.');
                $json = ['result' => false];
            }

            $this->output->set_content_type('application/json')->set_output(json_encode($json));
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar faturar OS.');
        $json = ['result' => false];
        $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode($json));
    }

    public function adicionarAnotacao()
    {
        $this->load->library('form_validation');
        if ($this->form_validation->run('anotacoes_os') == false) {
            $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(validation_errors()));
        } else {
            $data = [
                'anotacao' => '[' . $this->session->userdata('nome_admin') . '] ' . $this->input->post('anotacao'),
                'data_hora' => date('Y-m-d H:i:s'),
                'os_id' => $this->input->post('os_id'),
            ];

            if ($this->os_model->add('anotacoes_os', $data) == true) {
                log_info('Adicionou anotação a uma OS. ID (OS): ' . $this->input->post('os_id'));
                $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true]));
            } else {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false]));
            }
        }
    }

    public function excluirAnotacao()
    {
        $id = $this->input->post('idAnotacao');
        $idOs = $this->input->post('idOs');

        if ($this->os_model->delete('anotacoes_os', 'idAnotacoes', $id) == true) {
            log_info('Removeu anotação de uma OS. ID (OS): ' . $idOs);
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true]));
        } else {
            $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['result' => false]));
        }
    }

    /**
     * ETAPA 4: Interface de atribuição de técnico às OS
     * Tela para administradores gerenciarem a atribuição de técnicos
     */
    public function atribuir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para atribuir técnicos às OS.');
            redirect(base_url());
        }

        $this->load->model('tecnico_model');
        $this->load->library('pagination');
        $this->load->helper('text');
        $this->load->model('os_model');

        // Capturar filtros
        $filtro = $this->input->get('filtro');
        $busca_global = $this->input->get('busca_global');
        $status = $this->input->get('status');
        $tecnico = $this->input->get('tecnico');
        $data = $this->input->get('data');
        $data2 = $this->input->get('data2');
        $mostrar_finalizados = $this->input->get('mostrar_finalizados');

        // Configuração da paginação
        $config['base_url'] = site_url('os/atribuir');
        $config['per_page'] = 20;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        // Offset baseado na query string
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 0;

        // Preparar filtros para a busca
        $filtros = [];

        // Verificar se deve mostrar finalizados
        if (!$mostrar_finalizados) {
            $filtros['excluir_status'] = ['Finalizado', 'Cancelado', 'Faturado'];
        }

        // Filtro de status
        if ($status) {
            $filtros['status'] = $status;
            unset($filtros['excluir_status']);
        }

        // Filtro de data
        if ($data) {
            $data_formatada = parseDateBr($data);
            if ($data_formatada) {
                $filtros['data_inicio'] = $data_formatada;
            }
        }
        if ($data2) {
            $data_formatada2 = parseDateBr($data2);
            if ($data_formatada2) {
                $filtros['data_fim'] = $data_formatada2;
            }
        }

        // Filtro de técnico
        if ($filtro == 'sem_tecnico' || $tecnico == 'sem_tecnico') {
            $filtros['sem_tecnico'] = true;
        } elseif ($filtro == 'com_tecnico') {
            $filtros['com_tecnico'] = true;
        } elseif ($tecnico) {
            $filtros['tecnico_responsavel'] = $tecnico;
        }

        // Filtro de busca global
        if ($busca_global) {
            $filtros['busca_global'] = $busca_global;
        }

        // Buscar OS com filtros
        $ordens = $this->os_model->getOsAtribuicao($config['per_page'], $page, $filtros);
        $config['total_rows'] = $this->os_model->countOsAtribuicao($filtros);

        $this->data['ordens'] = $ordens ?: [];

        // Inicializar paginação
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        // Carregar lista de técnicos disponíveis
        $this->data['tecnicos'] = $this->tecnico_model->getTecnicosDisponiveis();

        // Ativar menu
        $this->data['menuAtribuir'] = 'Atribuir';
        $this->data['view'] = 'os/atribuir_tecnico';

        return $this->layout();
    }

    /**
     * Ação AJAX para atribuir técnico à OS
     */
    public function atribuirTecnicoAction()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para atribuir técnicos.');
            redirect('os/atribuir');
        }

        $os_id = $this->input->post('os_id');
        $tecnico_id = $this->input->post('tecnico_id');
        $observacao = $this->input->post('observacao');

        log_message('debug', "POST recebido - os_id: {$os_id}, tecnico_id: {$tecnico_id}");

        if (! $os_id || ! $tecnico_id) {
            $this->session->set_flashdata('error', 'Dados incompletos para atribuição. OS: ' . $os_id . ', Tecnico: ' . $tecnico_id);
            redirect('os/atribuir');
        }

        $this->load->model('tecnico_model');

        $atribuido_por = $this->session->userdata('idUsuarios');

        // Verificar OS atual
        $os_atual = $this->os_model->getById($os_id);
        log_message('debug', 'OS atual antes da atribuicao: ' . json_encode($os_atual));

        if ($this->tecnico_model->atribuirTecnico($os_id, $tecnico_id, $atribuido_por, $observacao)) {
            // Verificar se salvou
            $os_depois = $this->os_model->getById($os_id);
            log_message('debug', 'OS depois da atribuicao: ' . json_encode($os_depois));

            $this->session->set_flashdata('success', 'Técnico atribuído à OS #' . $os_id . ' com sucesso!');
            log_info('Atribuiu técnico ' . $tecnico_id . ' à OS #' . $os_id);

            // Enviar notificação ao técnico
            $this->load->model('notificacoes_model');
            $this->notificacoes_model->ensureTableExists();

            $url_os = site_url('tecnicos/ver_os/' . $os_id);
            $this->notificacoes_model->adicionar([
                'usuario_id' => $tecnico_id,
                'tipo_usuario' => 'tecnico',
                'titulo' => 'Nova OS Atribuída',
                'mensagem' => 'Você foi designado para a OS #' . $os_id . ($os_atual && $os_atual->nomeCliente ? ' - ' . $os_atual->nomeCliente : ''),
                'url' => $url_os,
                'tipo' => 'info',
                'icone' => 'bx-clipboard'
            ]);
        } else {
            $this->session->set_flashdata('error', 'Erro ao atribuir técnico. Verifique se já não está atribuído.');
        }

        redirect('os/atribuir');
    }

    /**
     * Ação para remover técnico da OS
     */
    public function removerTecnicoAction()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para remover técnicos das OS.');
            redirect('os/atribuir');
        }

        $os_id = $this->input->post('os_id');
        $motivo = $this->input->post('motivo');

        if (! $os_id) {
            $this->session->set_flashdata('error', 'OS não informada.');
            redirect('os/atribuir');
        }

        $this->load->model('tecnico_model');

        if ($this->tecnico_model->removerTecnico($os_id, $motivo)) {
            $this->session->set_flashdata('success', 'Técnico removido da OS #' . $os_id . ' com sucesso!');
            log_info('Removeu técnico da OS #' . $os_id);
        } else {
            $this->session->set_flashdata('error', 'Erro ao remover técnico da OS.');
        }

        redirect('os/atribuir');
    }

    /**
     * Visualizar histórico de atribuições de uma OS
     */
    public function historicoAtribuicoes($os_id = null)
    {
        if (! $os_id || ! is_numeric($os_id)) {
            $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['error' => 'OS inválida']));
            return;
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->output->set_content_type('application/json')->set_status_header(403)->set_output(json_encode(['error' => 'Sem permissão']));
            return;
        }

        $this->load->model('tecnico_model');

        $historico = $this->tecnico_model->getHistoricoAtribuicoes($os_id);

        $this->output->set_content_type('application/json')->set_output(json_encode($historico));
    }

}
