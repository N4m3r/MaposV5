<?php

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class ClientPortalController extends BaseController
{
    private $currentClient;

    public function __construct()
    {
        parent::__construct();

        // Verifica se o JWT contem claim client_id
        if (!isset($this->currentUser->client_id) && !isset($this->currentUser->type)) {
            $this->error('Acesso exclusivo para clientes', 403);
            exit;
        }

        if (($this->currentUser->type ?? '') !== 'client') {
            $this->error('Token invalido para acesso de cliente', 403);
            exit;
        }

        $clientId = $this->currentUser->client_id ?? $this->currentUser->sub;
        $this->load->model('clientes_model');
        $this->currentClient = $this->clientes_model->getById($clientId);

        if (!$this->currentClient) {
            $this->error('Cliente nao encontrado', 404);
            exit;
        }
    }

    public function os(int $id = 0): void
    {
        $this->load->model('conecte_model');
        $clientId = $this->currentClient->idClientes;

        if (strtolower($this->input->method()) === 'get') {
            if ($id) {
                $os = $this->conecte_model->getById($id);
                if (!$os || $os->clientes_id != $clientId) {
                    $this->notFound('OS');
                    return;
                }
                $this->success($os);
                return;
            }

            $pagination = $this->getPaginationParams();
            $allOs = $this->conecte_model->getOs('os', 'os.*, usuarios.nome', ['clientes_id' => $clientId], $pagination['per_page'], $pagination['offset'], false, true, $clientId);
            $total = $this->conecte_model->count('os', $clientId);

            $this->paginated($allOs, [
                'total' => $total,
                'page' => $pagination['page'],
                'per_page' => $pagination['per_page'],
            ]);
            return;
        }

        if (strtolower($this->input->method()) === 'post') {
            $data = $this->getJsonInput();

            $insertData = [
                'clientes_id' => $clientId,
                'descricaoProduto' => $data['descricaoProduto'] ?? '',
                'defeito' => $data['defeito'] ?? '',
                'observacoes' => $data['observacoes'] ?? '',
                'status' => 'Aberto',
                'dataInicial' => date('Y-m-d'),
                'dataFinal' => $data['dataFinal'] ?? date('Y-m-d', strtotime('+7 days')),
                'garantia' => $data['garantia'] ?? 0,
            ];

            // Auto-atribuir ao tecnico com menos OS
            $tecnico = $this->db
                ->select('usuarios.idUsuarios, COUNT(os_tecnicos.id) as os_count')
                ->join('os_tecnicos', 'os_tecnicos.tecnico_id = usuarios.idUsuarios AND os_tecnicos.ativo = 1', 'left')
                ->where('usuarios.is_tecnico', 1)
                ->where('usuarios.situacao', 1)
                ->group_by('usuarios.idUsuarios')
                ->order_by('os_count', 'ASC')
                ->limit(1)
                ->get('usuarios')->row();

            if ($tecnico) {
                $insertData['tecnico_responsavel'] = $tecnico->idUsuarios;
            }

            $this->db->insert('os', $insertData);
            $osId = $this->db->insert_id();

            $this->created(['id' => $osId, 'message' => 'OS criada com sucesso']);
            return;
        }
    }

    public function compras(int $id = 0): void
    {
        $this->load->model('conecte_model');
        $clientId = $this->currentClient->idClientes;

        if ($id) {
            $fields = 'vendas.*, usuarios.nome';
            $venda = $this->conecte_model->getCompras('vendas', $fields, ['idVendas' => $id], 1, 0, true, true, $clientId);
            if (!$venda) {
                $this->notFound('Venda');
                return;
            }
            $this->success($venda);
            return;
        }

        $pagination = $this->getPaginationParams();
        $compras = $this->conecte_model->getCompras('vendas', 'vendas.*, usuarios.nome', [], $pagination['per_page'], $pagination['offset'], false, true, $clientId);
        $total = $this->conecte_model->count('vendas', $clientId);

        $this->paginated($compras, [
            'total' => $total,
            'page' => $pagination['page'],
            'per_page' => $pagination['per_page'],
        ]);
    }

    public function cobrancas(): void
    {
        $this->load->model('conecte_model');
        $clientId = $this->currentClient->idClientes;

        $pagination = $this->getPaginationParams();
        $fields = 'cobrancas.*, clientes.nomeCliente';
        $cobrancas = $this->conecte_model->getCobrancas('cobrancas', $fields, [], $pagination['per_page'], $pagination['offset'], false, true, $clientId);

        $this->success($cobrancas);
    }
}