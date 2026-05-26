<?php
/**
 * OS Controller - API v2
 * Endpoints para gerenciamento de Ordens de Serviço
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';
require_once APPPATH . 'Repositories/OsRepository.php';

use Repositories\OsRepository;
use Libraries\Scheduler\AutoEvents;

class OsController extends BaseController
{
    private OsRepository $repository;
    private AutoEvents $autoEvents;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new OsRepository();
        $this->autoEvents = new AutoEvents();
    }

    /**
     * GET /api/v2/os
     * Lista todas as OS
     */
    public function index(): void
    {
        $pagination = $this->getPaginationParams();
        $filters = $this->applyFilters(['status', 'idClientes', 'dataInicial', 'dataFinal']);

        $cacheKey = "os_{$pagination['page']}_" . md5(json_encode($filters));

        $result = $this->cache->remember($cacheKey, function() use ($pagination, $filters) {
            $data = $this->repository->findAll($filters, $pagination['per_page'], $pagination['offset']);
            $total = $this->repository->count($filters);

            return [
                'data' => $data,
                'total' => $total
            ];
        }, 300);

        $result['page'] = $pagination['page'];
        $result['per_page'] = $pagination['per_page'];
        $result['total_pages'] = (int) ceil($result['total'] / $pagination['per_page']);

        $this->success($result);
    }

    /**
     * GET /api/v2/os/{id}
     * Retorna uma OS específica
     */
    public function show(int $id = 0): void
    {
        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $os = $this->repository->find($id);

        if (!$os) {
            $this->notFound('Ordem de Serviço');
            return;
        }

        // Carrega produtos e serviços
        $ci = &get_instance();
        $ci->load->model('os_model');
        $os->produtos = $ci->os_model->getProdutos($id);
        $os->servicos = $ci->os_model->getServicos($id);

        $this->success($os);
    }

    /**
     * POST /api/v2/os
     * Cria uma nova OS
     */
    public function store(): void
    {
        $this->checkPermission('os_criar');

        $data = $this->getJsonInput();

        // Validação
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('idClientes', 'Cliente', 'required|integer');
        $this->form_validation->set_rules('descricaoProduto', 'Descrição', 'required');

        if (!$this->form_validation->run()) {
            $this->validationError([validation_errors()]);
            return;
        }

        $ci = &get_instance();
        $ci->load->model('os_model');

        $id = $ci->os_model->insert($data);

        if ($id) {
            // Agenda evento de lembrete
            if (!empty($data['dataFinal']) && !empty($data['emailCliente'])) {
                $this->autoEvents->scheduleOsVencendo(
                    $id,
                    $data['dataFinal'],
                    $data['emailCliente']
                );
            }

            $this->clearCache('os_*');
            $this->created(['id' => $id, 'message' => 'OS criada com sucesso']);
        } else {
            $this->error('Erro ao criar OS', 500);
        }
    }

    /**
     * PUT /api/v2/os/{id}
     * Atualiza uma OS
     */
    public function update(int $id = 0): void
    {
        $this->checkPermission('os_editar');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $os = $this->repository->find($id);
        if (!$os) {
            $this->notFound('Ordem de Serviço');
            return;
        }

        $data = $this->getJsonInput();
        unset($data['idOs']);

        $ci = &get_instance();
        $ci->load->model('os_model');

        $success = $ci->os_model->update($id, $data);

        if ($success) {
            $this->clearCache('os_*');
            $this->updated(['message' => 'OS atualizada com sucesso']);
        } else {
            $this->error('Erro ao atualizar OS', 500);
        }
    }

    /**
     * PATCH /api/v2/os/{id}/status
     * Atualiza status da OS
     */
    public function updateStatus(int $id = 0): void
    {
        $this->checkPermission('os_editar');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $data = $this->getJsonInput();
        $status = $data['status'] ?? null;

        if (!$status) {
            $this->validationError(['status' => 'Status é obrigatório']);
            return;
        }

        $ci = &get_instance();
        $ci->load->model('os_model');
        $success = $ci->os_model->update($id, ['status' => $status]);

        if ($success) {
            $this->clearCache('os_*');
            $this->updated(['message' => 'Status atualizado com sucesso']);
        } else {
            $this->error('Erro ao atualizar status', 500);
        }
    }

    /**
     * DELETE /api/v2/os/{id}
     * Remove uma OS
     */
    public function delete(int $id = 0): void
    {
        $this->checkPermission('os_excluir');

        if (!$id) {
            $id = (int) $this->uri->segment(4);
        }

        $os = $this->repository->find($id);
        if (!$os) {
            $this->notFound('Ordem de Serviço');
            return;
        }

        $ci = &get_instance();
        $ci->load->model('os_model');
        $success = $ci->os_model->delete($id);

        if ($success) {
            $this->clearCache('os_*');
            $this->deleted('OS removida com sucesso');
        } else {
            $this->error('Erro ao remover OS', 500);
        }
    }

    /**
     * GET /api/v2/os/vencendo
     * Retorna OS prestes a vencer
     */
    public function vencendo(): void
    {
        $dias = (int) $this->input->get('dias') ?: 2;
        $os = $this->repository->findOsVencendo($dias);

        $this->success([
            'data' => $os,
            'total' => count($os),
            'dias_para_vencer' => $dias
        ]);
    }

    /**
     * GET /api/v2/os/atrasadas
     * Retorna OS atrasadas
     */
    public function atrasadas(): void
    {
        $os = $this->repository->findOsAtrasadas();

        $this->success([
            'data' => $os,
            'total' => count($os)
        ]);
    }

    // ==============================
    // SUB-RECURSOS: Produtos na OS
    // ==============================

    public function produtos(int $osId = 0): void
    {
        $this->load->model('os_model');

        if (!$osId) {
            $osId = (int) $this->uri->segment(4);
        }

        if (strtolower($this->input->method()) === 'get') {
            $produtos = $this->os_model->getProdutos($osId);
            $this->success($produtos);
            return;
        }

        if (strtolower($this->input->method()) === 'post') {
            $this->checkPermission('os_editar');
            $data = $this->getJsonInput();

            if (empty($data['idProduto']) || empty($data['quantidade'])) {
                $this->validationError(['idProduto e quantidade sao obrigatorios']);
                return;
            }

            $produto = $this->db->where('idProdutos', $data['idProduto'])->get('produtos')->row();
            if (!$produto) {
                $this->notFound('Produto');
                return;
            }

            $insertData = [
                'os_id' => $osId,
                'produtos_id' => $data['idProduto'],
                'quantidade' => $data['quantidade'],
                'preco' => $data['preco'] ?? $produto->precoVenda,
                'subTotal' => ($data['quantidade']) * ($data['preco'] ?? $produto->precoVenda),
            ];

            $this->db->insert('produtos_os', $insertData);

            // Debitar estoque
            $this->db->where('idProdutos', $data['idProduto']);
            $this->db->set('estoque', 'estoque - ' . (int) $data['quantidade'], false);
            $this->db->update('produtos');

            $this->clearCache('os_*');
            $this->created(['message' => 'Produto adicionado a OS']);
            return;
        }
    }

    public function produtoUpdate(int $osId = 0, int $prodOsId = 0): void
    {
        $this->checkPermission('os_editar');
        $this->load->model('os_model');

        $data = $this->getJsonInput();

        $updateData = [];
        if (isset($data['quantidade'])) $updateData['quantidade'] = $data['quantidade'];
        if (isset($data['preco'])) $updateData['preco'] = $data['preco'];
        if (isset($data['quantidade']) && isset($data['preco'])) {
            $updateData['subTotal'] = $data['quantidade'] * $data['preco'];
        }

        $this->db->where('idProdutos_os', $prodOsId);
        $this->db->update('produtos_os', $updateData);

        $this->clearCache('os_*');
        $this->updated(['message' => 'Produto atualizado na OS']);
    }

    public function produtoDelete(int $osId = 0, int $prodOsId = 0): void
    {
        $this->checkPermission('os_editar');
        $this->load->model('os_model');

        $prodOs = $this->db->where('idProdutos_os', $prodOsId)->get('produtos_os')->row();
        if ($prodOs) {
            // Devolver estoque
            $this->db->where('idProdutos', $prodOs->produtos_id);
            $this->db->set('estoque', 'estoque + ' . (int) $prodOs->quantidade, false);
            $this->db->update('produtos');
        }

        $this->db->where('idProdutos_os', $prodOsId);
        $this->db->delete('produtos_os');

        $this->clearCache('os_*');
        $this->deleted('Produto removido da OS');
    }

    // ==============================
    // SUB-RECURSOS: Servicos na OS
    // ==============================

    public function servicos(int $osId = 0): void
    {
        $this->load->model('os_model');

        if (!$osId) {
            $osId = (int) $this->uri->segment(4);
        }

        if (strtolower($this->input->method()) === 'get') {
            $servicos = $this->os_model->getServicos($osId);
            $this->success($servicos);
            return;
        }

        if (strtolower($this->input->method()) === 'post') {
            $this->checkPermission('os_editar');
            $data = $this->getJsonInput();

            if (empty($data['idServico'])) {
                $this->validationError(['idServico e obrigatorio']);
                return;
            }

            $servico = $this->db->where('idServicos', $data['idServico'])->get('servicos')->row();
            if (!$servico) {
                $this->notFound('Servico');
                return;
            }

            $insertData = [
                'os_id' => $osId,
                'servicos_id' => $data['idServico'],
                'quantidade' => $data['quantidade'] ?? 1,
                'preco' => $data['preco'] ?? $servico->preco,
                'subTotal' => ($data['quantidade'] ?? 1) * ($data['preco'] ?? $servico->preco),
            ];

            $this->db->insert('servicos_os', $insertData);

            $this->clearCache('os_*');
            $this->created(['message' => 'Servico adicionado a OS']);
            return;
        }
    }

    public function servicoUpdate(int $osId = 0, int $servOsId = 0): void
    {
        $this->checkPermission('os_editar');
        $data = $this->getJsonInput();

        $updateData = [];
        if (isset($data['quantidade'])) $updateData['quantidade'] = $data['quantidade'];
        if (isset($data['preco'])) $updateData['preco'] = $data['preco'];
        if (isset($data['quantidade']) && isset($data['preco'])) {
            $updateData['subTotal'] = $data['quantidade'] * $data['preco'];
        }

        $this->db->where('idServicos_os', $servOsId);
        $this->db->update('servicos_os', $updateData);

        $this->clearCache('os_*');
        $this->updated(['message' => 'Servico atualizado na OS']);
    }

    public function servicoDelete(int $osId = 0, int $servOsId = 0): void
    {
        $this->checkPermission('os_editar');

        $this->db->where('idServicos_os', $servOsId);
        $this->db->delete('servicos_os');

        $this->clearCache('os_*');
        $this->deleted('Servico removido da OS');
    }

    // ==============================
    // SUB-RECURSOS: Anotacoes
    // ==============================

    public function anotacoes(int $osId = 0): void
    {
        $this->load->model('os_model');

        if (!$osId) {
            $osId = (int) $this->uri->segment(4);
        }

        if (strtolower($this->input->method()) === 'get') {
            $anotacoes = $this->db->where('os_id', $osId)->get('anotacoes_os')->result();
            $this->success($anotacoes);
            return;
        }

        if (strtolower($this->input->method()) === 'post') {
            $this->checkPermission('os_editar');
            $data = $this->getJsonInput();

            if (empty($data['anotacao'])) {
                $this->validationError(['anotacao e obrigatoria']);
                return;
            }

            $userName = $this->currentUser->name ?? 'Sistema';
            $insertData = [
                'os_id' => $osId,
                'anotacao' => '<b>' . $userName . ':</b> ' . $data['anotacao'],
                'data_hora' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('anotacoes_os', $insertData);

            $this->clearCache('os_*');
            $this->created(['message' => 'Anotacao adicionada']);
            return;
        }
    }

    public function anotacaoDelete(int $osId = 0, int $anotId = 0): void
    {
        $this->checkPermission('os_editar');

        $this->db->where('idAnotacoes', $anotId);
        $this->db->delete('anotacoes_os');

        $this->clearCache('os_*');
        $this->deleted('Anotacao removida');
    }

    // ==============================
    // SUB-RECURSOS: Anexos
    // ==============================

    public function anexos(int $osId = 0): void
    {
        $this->load->model('os_model');

        if (!$osId) {
            $osId = (int) $this->uri->segment(4);
        }

        if (strtolower($this->input->method()) === 'get') {
            $anexos = $this->db->where('os_id', $osId)->get('anexos')->result();
            $this->success($anexos);
            return;
        }

        if (strtolower($this->input->method()) === 'post') {
            $this->checkPermission('os_editar');

            if (empty($_FILES['userfile'])) {
                $this->validationError(['Arquivo e obrigatorio']);
                return;
            }

            $uploadPath = FCPATH . 'assets/anexos/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = '*';
            $config['max_size'] = 10240;
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('userfile')) {
                $this->validationError([$this->upload->display_errors()]);
                return;
            }

            $uploadData = $this->upload->data();
            $insertData = [
                'os_id' => $osId,
                'arquivo' => $uploadData['file_name'],
                'descricao' => $this->input->post('descricao') ?: $uploadData['orig_name'],
                'data' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('anexos', $insertData);

            $this->clearCache('os_*');
            $this->created(['message' => 'Anexo adicionado', 'arquivo' => $uploadData['file_name']]);
            return;
        }
    }

    public function anexoDelete(int $osId = 0, int $anexoId = 0): void
    {
        $this->checkPermission('os_editar');

        $anexo = $this->db->where('idAnexos', $anexoId)->get('anexos')->row();
        if ($anexo) {
            $filePath = FCPATH . 'assets/anexos/' . $anexo->arquivo;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->db->where('idAnexos', $anexoId);
        $this->db->delete('anexos');

        $this->clearCache('os_*');
        $this->deleted('Anexo removido');
    }

    // ==============================
    // Desconto
    // ==============================

    public function desconto(int $osId = 0): void
    {
        $this->checkPermission('os_editar');
        $this->load->model('os_model');

        $data = $this->getJsonInput();

        if (empty($data['tipo_desconto']) || !isset($data['desconto'])) {
            $this->validationError(['tipo_desconto e desconto sao obrigatorios']);
            return;
        }

        $os = $this->repository->find($osId);
        if (!$os) {
            $this->notFound('OS');
            return;
        }

        $valorTotal = floatval($os->valorTotal ?? 0);
        $desconto = floatval($data['desconto']);
        $tipoDesconto = $data['tipo_desconto'];

        $valorDesconto = $tipoDesconto === 'porcento'
            ? $valorTotal * ($desconto / 100)
            : $desconto;

        $updateData = [
            'tipo_desconto' => $tipoDesconto,
            'desconto' => $desconto,
            'valor_desconto' => $valorDesconto,
        ];

        $this->db->where('idOs', $osId);
        $this->db->update('os', $updateData);

        $this->clearCache('os_*');
        $this->updated(['message' => 'Desconto aplicado', 'valor_desconto' => $valorDesconto]);
    }

    // ==============================
    // Tecnico
    // ==============================

    public function listarTecnicos(): void
    {
        $this->checkPermission('os_editar');

        $tecnicos = $this->db
            ->select('idUsuarios, nome, email, telefone')
            ->where('is_tecnico', 1)
            ->where('situacao', 1)
            ->get('usuarios')->result();

        $this->success($tecnicos);
    }

    public function tecnico(int $osId = 0): void
    {
        $this->load->model('os_model');

        $tecnico = $this->db
            ->select('usuarios.idUsuarios, usuarios.nome, usuarios.email, usuarios.telefone, os_tecnicos.atribuido_em')
            ->join('usuarios', 'usuarios.idUsuarios = os_tecnicos.tecnico_id')
            ->where('os_tecnicos.os_id', $osId)
            ->where('os_tecnicos.ativo', 1)
            ->get('os_tecnicos')->row();

        $this->success($tecnico);
    }

    public function atribuirTecnico(): void
    {
        $this->checkPermission('os_editar');
        $data = $this->getJsonInput();

        if (empty($data['os_id']) || empty($data['tecnico_id'])) {
            $this->validationError(['os_id e tecnico_id sao obrigatorios']);
            return;
        }

        // Desativar tecnico anterior
        $this->db->where('os_id', $data['os_id'])->where('ativo', 1)
            ->update('os_tecnicos', ['ativo' => 0, 'removido_em' => date('Y-m-d H:i:s')]);

        // Atribuir novo tecnico
        $insertData = [
            'os_id' => $data['os_id'],
            'tecnico_id' => $data['tecnico_id'],
            'atribuido_em' => date('Y-m-d H:i:s'),
            'ativo' => 1,
        ];

        $this->db->insert('os_tecnicos', $insertData);

        // Atualizar OS
        $this->db->where('idOs', $data['os_id'])
            ->update('os', ['tecnico_responsavel' => $data['tecnico_id']]);

        $this->clearCache('os_*');
        $this->created(['message' => 'Tecnico atribuido com sucesso']);
    }

    public function removerTecnico(): void
    {
        $this->checkPermission('os_editar');
        $data = $this->getJsonInput();

        if (empty($data['os_id'])) {
            $this->validationError(['os_id e obrigatorio']);
            return;
        }

        $this->db->where('os_id', $data['os_id'])->where('ativo', 1)
            ->update('os_tecnicos', ['ativo' => 0, 'removido_em' => date('Y-m-d H:i:s')]);

        $this->db->where('idOs', $data['os_id'])
            ->update('os', ['tecnico_responsavel' => null]);

        $this->clearCache('os_*');
        $this->deleted('Tecnico removido da OS');
    }

    public function historicoTecnico(int $osId = 0): void
    {
        $historico = $this->db
            ->select('os_tecnicos.*, usuarios.nome as tecnico_nome')
            ->join('usuarios', 'usuarios.idUsuarios = os_tecnicos.tecnico_id', 'left')
            ->where('os_tecnicos.os_id', $osId)
            ->order_by('os_tecnicos.atribuido_em', 'DESC')
            ->get('os_tecnicos')->result();

        $this->success($historico);
    }
}
