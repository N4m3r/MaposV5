<?php

use Piggly\Pix\StaticPayload;

class Os_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields . ',clientes.nomeCliente, clientes.celular as celular_cliente');
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->limit($perpage, $start);
        $this->db->order_by('idOs', 'desc');
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getOs($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $lista_clientes = [];
        if ($where) {
            if (array_key_exists('pesquisa', $where)) {
                $this->db->select('idClientes');
                $this->db->like('nomeCliente', $where['pesquisa']);
                $this->db->or_like('documento', $where['pesquisa']);
                $this->db->limit(25);
                $clientes_query = $this->db->get('clientes');

                // Verificar se query falhou
                if ($clientes_query === false) {
                    log_message('error', 'Erro ao buscar clientes na pesquisa: ' . print_r($this->db->error(), true));
                    return [];
                }

                $clientes = $clientes_query->result();

                foreach ($clientes as $c) {
                    array_push($lista_clientes, $c->idClientes);
                }
            }
        }

        // Limpar o query builder para garantir estado limpo
        $this->db->reset_query();

        $this->db->select($fields . ',clientes.idClientes, clientes.nomeCliente, clientes.celular as celular_cliente, usuarios.nome, garantias.*');
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = os.garantias_id', 'left');
        // NOTA: Não fazer JOIN com produtos_os ou servicos_os aqui pois causam duplicatas
        // Os totais são calculados via subqueries no SELECT

        // condicionais da pesquisa

        // condicional de status
        if (array_key_exists('status', $where) && !empty($where['status'])) {
            $this->db->where_in('status', $where['status']);
        }

        // condicional de status padrão do sistema
        if (array_key_exists('os_status_list', $where) && is_array($where['os_status_list'])) {
            $this->db->where_in('status', $where['os_status_list']);
        }

        // condicional de clientes
        if (array_key_exists('pesquisa', $where)) {
            if ($lista_clientes != null) {
                $this->db->where_in('os.clientes_id', $lista_clientes);
            }
        }

        // condicional data inicial
        if (array_key_exists('de', $where)) {
            $this->db->where('dataInicial >=', $where['de']);
        }
        // condicional data final
        if (array_key_exists('ate', $where)) {
            $this->db->where('dataFinal <=', $where['ate']);
        }
        // condicional técnico responsável
        if (array_key_exists('tecnico_responsavel', $where)) {
            $this->db->where('os.tecnico_responsavel', $where['tecnico_responsavel']);
        }

        $this->db->limit($perpage, $start);
        $this->db->order_by('os.idOs', 'desc');

        $query = $this->db->get();

        // Verificar se a query falhou
        if ($query === false) {
            $error = $this->db->error();
            log_message('error', 'Erro na query getOs: ' . $error['message'] . ' | Query: ' . $this->db->last_query());
            return [];
        }

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->select('os.*, clientes.*, clientes.celular as celular_cliente, clientes.telefone as telefone_cliente, clientes.contato as contato_cliente, garantias.refGarantia, garantias.textoGarantia, usuarios.telefone as telefone_usuario, usuarios.email as email_usuario, usuarios.nome');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = os.garantias_id', 'left');
        $this->db->where('os.idOs', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getByIdCobrancas($id)
    {
        $this->db->select('os.*, clientes.*, clientes.celular as celular_cliente, garantias.refGarantia, garantias.textoGarantia, usuarios.telefone as telefone_usuario, usuarios.email as email_usuario, usuarios.nome,cobrancas.os_id,cobrancas.idCobranca,cobrancas.status');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
        $this->db->join('cobrancas', 'cobrancas.os_id = os.idOs');
        $this->db->join('garantias', 'garantias.idGarantias = os.garantias_id', 'left');
        $this->db->where('os.idOs', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getProdutos($id = null)
    {
        $this->db->select('produtos_os.*, produtos.*');
        $this->db->from('produtos_os');
        $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
        $this->db->where('os_id', $id);

        $query = $this->db->get();

        if ($query === false) {
            log_message('error', 'Erro ao buscar produtos da OS ' . $id . ': ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result();
    }

    public function getServicos($id = null)
    {
        $this->db->select('servicos_os.*, servicos.nome, servicos.preco as precoVenda');
        $this->db->from('servicos_os');
        $this->db->join('servicos', 'servicos.idServicos = servicos_os.servicos_id');
        $this->db->where('os_id', $id);

        $query = $this->db->get();

        if ($query === false) {
            log_message('error', 'Erro ao buscar serviços da OS ' . $id . ': ' . print_r($this->db->error(), true));
            return [];
        }

        $result = $query->result();
        log_message('error', 'DEBUG Os_model::getServicos - OS ' . $id . ' - Query: ' . $this->db->last_query());
        log_message('error', 'DEBUG Os_model::getServicos - OS ' . $id . ' - ' . count($result) . ' serviços encontrados');
        foreach ($result as $r) {
            log_message('error', 'DEBUG Os_model::getServicos - Servico: id=' . $r->idServicos_os . ', status=' . ($r->status ?? 'NULL') . ', nome=' . ($r->nome ?? 'NULL'));
        }
        return $result;
    }

    public function add($table, $data, $returnId = false)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            if ($returnId == true) {
                return $this->db->insert_id($table);
            }

            return true;
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }

    /**
     * Contar OS com filtros aplicados
     */
    public function countOs($where = [])
    {
        $lista_clientes = [];
        if ($where) {
            if (array_key_exists('pesquisa', $where)) {
                $this->db->select('idClientes');
                $this->db->like('nomeCliente', $where['pesquisa']);
                $this->db->or_like('documento', $where['pesquisa']);
                $this->db->limit(25);
                $clientes_query = $this->db->get('clientes');

                // Verificar se query falhou
                if ($clientes_query === false) {
                    log_message('error', 'Erro ao buscar clientes na pesquisa (countOs): ' . print_r($this->db->error(), true));
                    return 0;
                }

                $clientes = $clientes_query->result();

                foreach ($clientes as $c) {
                    array_push($lista_clientes, $c->idClientes);
                }
            }
        }

        // Limpar o query builder
        $this->db->reset_query();

        // Usar DISTINCT para evitar duplicatas causadas pelos JOINs
        $this->db->select('COUNT(DISTINCT os.idOs) as total');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');

        // condicional de status
        if (array_key_exists('status', $where) && !empty($where['status'])) {
            $this->db->where_in('status', $where['status']);
        }

        // condicional de status padrão do sistema
        if (array_key_exists('os_status_list', $where) && is_array($where['os_status_list'])) {
            $this->db->where_in('status', $where['os_status_list']);
        }

        // condicional de clientes
        if (array_key_exists('pesquisa', $where)) {
            if ($lista_clientes != null) {
                $this->db->where_in('os.clientes_id', $lista_clientes);
            } else {
                // Se pesquisou mas não encontrou clientes, retorna 0
                return 0;
            }
        }

        // condicional data inicial
        if (array_key_exists('de', $where)) {
            $this->db->where('dataInicial >=', $where['de']);
        }
        // condicional data final
        if (array_key_exists('ate', $where)) {
            $this->db->where('dataFinal <=', $where['ate']);
        }
        // condicional técnico responsável
        if (array_key_exists('tecnico_responsavel', $where)) {
            $this->db->where('os.tecnico_responsavel', $where['tecnico_responsavel']);
        }

        $query = $this->db->get();

        // Verificar se query falhou
        if ($query === false) {
            $error = $this->db->error();
            log_message('error', 'Erro na query countOs: ' . $error['message'] . ' | Query: ' . $this->db->last_query());
            return 0;
        }

        $result = $query->row();
        return $result ? (int) $result->total : 0;
    }

    public function autoCompleteProduto($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('codDeBarra', $q);
        $this->db->or_like('descricao', $q);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['descricao'] . ' | Preço: R$ ' . $row['precoVenda'] . ' | Estoque: ' . $row['estoque'], 'estoque' => $row['estoque'], 'id' => $row['idProdutos'], 'preco' => $row['precoVenda']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteProdutoSaida($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('codDeBarra', $q);
        $this->db->or_like('descricao', $q);
        $this->db->where('saida', 1);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['descricao'] . ' | Preço: R$ ' . $row['precoVenda'] . ' | Estoque: ' . $row['estoque'], 'estoque' => $row['estoque'], 'id' => $row['idProdutos'], 'preco' => $row['precoVenda']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteCliente($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('nomeCliente', $q);
        $this->db->or_like('telefone', $q);
        $this->db->or_like('celular', $q);
        $this->db->or_like('documento', $q);
        $query = $this->db->get('clientes');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nomeCliente'] . ' | Telefone: ' . $row['telefone'] . ' | Celular: ' . $row['celular'] . ' | Documento: ' . $row['documento'], 'id' => $row['idClientes']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteUsuario($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('nome', $q);
        $this->db->where('situacao', 1);
        $query = $this->db->get('usuarios');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nome'] . ' | Telefone: ' . $row['telefone'], 'id' => $row['idUsuarios']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteTermoGarantia($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('LOWER(refGarantia)', $q);
        $query = $this->db->get('garantias');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['refGarantia'], 'id' => $row['idGarantias']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteServico($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('nome', $q);
        $query = $this->db->get('servicos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nome'] . ' | Preço: R$ ' . $row['preco'], 'id' => $row['idServicos'], 'preco' => $row['preco']];
            }
            echo json_encode($row_set);
        }
    }

    public function anexar($os, $anexo, $url, $thumb, $path)
    {
        $this->db->set('anexo', $anexo);
        $this->db->set('url', $url);
        $this->db->set('thumb', $thumb);
        $this->db->set('path', $path);
        $this->db->set('os_id', $os);

        return $this->db->insert('anexos');
    }

    public function getAnexos($os)
    {
        $this->db->where('os_id', $os);

        return $this->db->get('anexos')->result();
    }

    public function getAnotacoes($os)
    {
        $this->db->where('os_id', $os);
        $this->db->order_by('idAnotacoes', 'desc');

        return $this->db->get('anotacoes_os')->result();
    }

    public function getCobrancas($id = null)
    {
        $this->db->select('cobrancas.*');
        $this->db->from('cobrancas');
        $this->db->where('os_id', $id);

        return $this->db->get()->result();
    }

    /**
     * Obter documentos fiscais vinculados à OS (cobranças + NFS-e)
     */
    public function getDocumentosFiscais($os_id = null)
    {
        $documentos = [];

        // Buscar cobranças/boletos
        $this->db->select('cobrancas.*, clientes.nomeCliente');
        $this->db->from('cobrancas');
        $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id', 'left');
        $this->db->where('cobrancas.os_id', $os_id);
        $cobrancas = $this->db->get()->result();

        foreach ($cobrancas as $cobranca) {
            $documentos[] = [
                'tipo' => 'cobranca',
                'tipo_label' => 'Boleto/Pagamento',
                'tipo_icon' => 'bx-dollar-circle',
                'id' => $cobranca->idCobranca,
                'numero' => $cobranca->charge_id,
                'data' => $cobranca->created_at,
                'valor' => $cobranca->total,
                'status' => $cobranca->status,
                'link' => $cobranca->payment_url,
                'barcode' => $cobranca->barcode,
                'pdf' => $cobranca->pdf,
                'gateway' => $cobranca->payment_gateway,
                'payment_method' => $cobranca->payment_method,
            ];
        }

        // Buscar NFS-e importadas vinculadas à OS
        $nfse = [];
        try {
            $this->db->select('certificado_nfe_importada.*');
            $this->db->from('certificado_nfe_importada');
            $this->db->where('os_id', $os_id);
            $query = $this->db->get();
            if ($query) {
                $nfse = $query->result();
            }
        } catch (Exception $e) {
            // Tabela não existe
            $nfse = [];
        }

        foreach ($nfse as $nota) {
            $documentos[] = [
                'tipo' => 'nfse',
                'tipo_label' => 'NFS-e',
                'tipo_icon' => 'bx-file',
                'id' => $nota->id,
                'numero' => $nota->numero_nota,
                'data' => $nota->data_emissao,
                'valor' => $nota->valor_total,
                'status' => $nota->situacao,
                'impostos' => $nota->valor_impostos,
                'xml_path' => $nota->caminho_xml,
                'prestador' => $nota->prestador_nome,
            ];
        }

        // Buscar retenções de impostos vinculadas à OS
        $impostos = [];
        try {
            $this->db->select('impostos_retidos.*');
            $this->db->from('impostos_retidos');
            $this->db->where('os_id', $os_id);
            $this->db->where('status', 'Retido');
            $query = $this->db->get();
            if ($query) {
                $impostos = $query->result();
            }
        } catch (Exception $e) {
            // Tabela não existe
            $impostos = [];
        }

        foreach ($impostos as $imposto) {
            $documentos[] = [
                'tipo' => 'imposto',
                'tipo_label' => 'Retenção Impostos',
                'tipo_icon' => 'bx-calculator',
                'id' => $imposto->id,
                'numero' => 'RET-' . str_pad($imposto->id, 4, '0', STR_PAD_LEFT),
                'data' => $imposto->data_retencao,
                'valor' => $imposto->total_impostos,
                'valor_bruto' => $imposto->valor_bruto,
                'status' => $imposto->status,
                'aliquota' => $imposto->aliquota_aplicada,
                'nota_fiscal' => $imposto->nota_fiscal,
            ];
        }

        // Ordenar por data decrescente
        usort($documentos, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return $documentos;
    }

    /**
     * Vincular NFS-e importada à OS
     */
    public function vincularNfseOs($nfse_id, $os_id)
    {
        $this->db->where('id', $nfse_id);
        return $this->db->update('certificado_nfe_importada', [
            'os_id' => $os_id,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function criarTextoWhats($textoBase, $troca)
    {
        $procura = ['{CLIENTE_NOME}', '{NUMERO_OS}', '{STATUS_OS}', '{VALOR_OS}', '{DESCRI_PRODUTOS}', '{EMITENTE}', '{TELEFONE_EMITENTE}', '{OBS_OS}', '{DEFEITO_OS}', '{LAUDO_OS}', '{DATA_FINAL}', '{DATA_INICIAL}', '{DATA_GARANTIA}'];
        $textoBase = str_replace($procura, $troca, $textoBase);
        $textoBase = strip_tags($textoBase);
        $textoBase = htmlentities(urlencode($textoBase));

        return $textoBase;
    }

    public function valorTotalOS($id = null)
    {
        $totalServico = 0;
        $totalProdutos = 0;
        $valorDesconto = 0;
        if ($servicos = $this->getServicos($id)) {
            foreach ($servicos as $s) {
                $preco = $s->preco ?: $s->precoVenda;
                $totalServico = $totalServico + ($preco * ($s->quantidade ?: 1));
            }
        }
        if ($produtos = $this->getProdutos($id)) {
            foreach ($produtos as $p) {
                $totalProdutos = $totalProdutos + $p->subTotal;
            }
        }
        if ($valorDescontoBD = $this->getById($id)) {
            $valorDesconto = $valorDescontoBD->valor_desconto;
        }

        return ['totalServico' => $totalServico, 'totalProdutos' => $totalProdutos, 'valor_desconto' => $valorDesconto];
    }

    public function isEditable($id = null)
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            return false;
        }
        if ($os = $this->getById($id)) {
            $osT = (int) ($os->status === 'Faturado' || $os->status === 'Cancelado' || $os->faturado == 1);
            if ($osT) {
                return $this->data['configuration']['control_editos'] == '1';
            }
        }

        return true;
    }

    public function getQrCode($id, $pixKey, $emitente)
    {
        if (empty($id) || empty($pixKey) || empty($emitente)) {
            return;
        }

        $result = $this->valorTotalOS($id);
        $amount = $result['valor_desconto'] != 0 ? round(floatval($result['valor_desconto']), 2) : round(floatval($result['totalServico'] + $result['totalProdutos']), 2);

        if ($amount <= 0) {
            return;
        }

        $pix = (new StaticPayload())
            ->setAmount($amount)
            ->setTid($id)
            ->setDescription(sprintf('%s OS %s', substr($emitente->nome, 0, 18), $id), true)
            ->setPixKey(getPixKeyType($pixKey), $pixKey)
            ->setMerchantName($emitente->nome)
            ->setMerchantCity($emitente->cidade);

        return $pix->getQRCode();
    }

    /**
     * Gerar QR Code PIX com valor customizado (para preview NFS-e)
     */
    public function getQrCodeCustom($amount, $id, $pixKey, $emitente)
    {
        if (empty($amount) || $amount <= 0 || empty($pixKey) || empty($emitente)) {
            return null;
        }

        $pix = (new StaticPayload())
            ->setAmount(round(floatval($amount), 2))
            ->setTid($id)
            ->setDescription(sprintf('%s OS %s', substr($emitente->nome, 0, 18), $id), true)
            ->setPixKey(getPixKeyType($pixKey), $pixKey)
            ->setMerchantName($emitente->nome)
            ->setMerchantCity($emitente->cidade);

        return $pix->getQRCode();
    }

    /**
     * Obter OS sem técnico atribuído (para atribuição)
     */
    public function getOsSemTecnico($limite = 20, $offset = 0)
    {
        $this->db->select('os.*, clientes.nomeCliente, clientes.telefone, usuarios.nome as nome_tecnico');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.tecnico_responsavel', 'left');
        $this->db->where('os.tecnico_responsavel IS NULL');
        $this->db->where_not_in('os.status', ['Finalizado', 'Cancelado', 'Faturado']);
        $this->db->order_by('os.dataInicial', 'DESC');
        $this->db->limit($limite, $offset);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    /**
     * Obter OS com técnico atribuído
     */
    public function getOsComTecnico($limite = 20, $offset = 0)
    {
        $this->db->select('os.*, clientes.nomeCliente, clientes.telefone, usuarios.nome as nome_tecnico');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.tecnico_responsavel', 'left');
        $this->db->where('os.tecnico_responsavel IS NOT NULL');
        $this->db->where_not_in('os.status', ['Finalizado', 'Cancelado', 'Faturado']);
        $this->db->order_by('os.dataInicial', 'DESC');
        $this->db->limit($limite, $offset);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    /**
     * Obter OS pendentes para atribuição (todas exceto finalizadas)
     */
    public function getOsPendentesAtribuicao($limite = 20, $offset = 0)
    {
        $this->db->select('os.*, clientes.nomeCliente, clientes.telefone, usuarios.nome as nome_tecnico');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.tecnico_responsavel', 'left');
        $this->db->where_not_in('os.status', ['Finalizado', 'Cancelado', 'Faturado']);
        $this->db->order_by('os.dataInicial', 'DESC');
        $this->db->limit($limite, $offset);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    /**
     * Obter OS para atribuição com filtros
     */
    public function getOsAtribuicao($limite = 20, $offset = 0, $filtros = [])
    {
        $this->db->select('os.*, clientes.nomeCliente, clientes.telefone, clientes.idClientes, usuarios.nome as nome_tecnico');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.tecnico_responsavel', 'left');

        // Aplicar filtros
        if (!empty($filtros['pesquisa'])) {
            $this->db->like('clientes.nomeCliente', $filtros['pesquisa']);
        }

        // Busca global - pesquisa em múltiplos campos
        if (!empty($filtros['busca_global'])) {
            $busca = $filtros['busca_global'];
            $this->db->group_start();
            $this->db->like('os.idOs', $busca);
            $this->db->or_like('clientes.nomeCliente', $busca);
            $this->db->or_like('os.descricaoProduto', $busca);
            $this->db->or_like('os.descricaoServico', $busca);
            $this->db->or_like('os.defeito', $busca);
            $this->db->or_like('os.observacoes', $busca);
            $this->db->or_like('clientes.telefone', $busca);
            $this->db->or_like('clientes.celular', $busca);
            $this->db->group_end();
        }

        if (!empty($filtros['status'])) {
            $this->db->where('os.status', $filtros['status']);
        }

        if (!empty($filtros['excluir_status'])) {
            $this->db->where_not_in('os.status', $filtros['excluir_status']);
        }

        if (!empty($filtros['tecnico_responsavel'])) {
            $this->db->where('os.tecnico_responsavel', $filtros['tecnico_responsavel']);
        }

        if (!empty($filtros['sem_tecnico'])) {
            $this->db->where('os.tecnico_responsavel IS NULL');
        }

        if (!empty($filtros['com_tecnico'])) {
            $this->db->where('os.tecnico_responsavel IS NOT NULL');
        }

        if (!empty($filtros['data_inicio'])) {
            $this->db->where('os.dataInicial >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $this->db->where('os.dataInicial <=', $filtros['data_fim']);
        }

        $this->db->order_by('os.dataInicial', 'DESC');
        $this->db->limit($limite, $offset);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    /**
     * Contar OS para atribuição com filtros
     */
    public function countOsAtribuicao($filtros = [])
    {
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id', 'left');

        // Aplicar filtros
        if (!empty($filtros['pesquisa'])) {
            $this->db->like('clientes.nomeCliente', $filtros['pesquisa']);
        }

        // Busca global - pesquisa em múltiplos campos
        if (!empty($filtros['busca_global'])) {
            $busca = $filtros['busca_global'];
            $this->db->group_start();
            $this->db->like('os.idOs', $busca);
            $this->db->or_like('clientes.nomeCliente', $busca);
            $this->db->or_like('os.descricaoProduto', $busca);
            $this->db->or_like('os.descricaoServico', $busca);
            $this->db->or_like('os.defeito', $busca);
            $this->db->or_like('os.observacoes', $busca);
            $this->db->or_like('clientes.telefone', $busca);
            $this->db->or_like('clientes.celular', $busca);
            $this->db->group_end();
        }

        if (!empty($filtros['status'])) {
            $this->db->where('os.status', $filtros['status']);
        }

        if (!empty($filtros['excluir_status'])) {
            $this->db->where_not_in('os.status', $filtros['excluir_status']);
        }

        if (!empty($filtros['tecnico_responsavel'])) {
            $this->db->where('os.tecnico_responsavel', $filtros['tecnico_responsavel']);
        }

        if (!empty($filtros['sem_tecnico'])) {
            $this->db->where('os.tecnico_responsavel IS NULL');
        }

        if (!empty($filtros['com_tecnico'])) {
            $this->db->where('os.tecnico_responsavel IS NOT NULL');
        }

        if (!empty($filtros['data_inicio'])) {
            $this->db->where('os.dataInicial >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $this->db->where('os.dataInicial <=', $filtros['data_fim']);
        }

        return $this->db->count_all_results();
    }
}
