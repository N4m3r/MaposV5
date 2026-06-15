<?php
/**
 * Trait ApiCrudTrait
 *
 * Adiciona endpoints REST genericos para qualquer controller:
 *   GET  /api_list    - lista com busca, paginacao e ordenacao
 *   GET  /api_get/$id - retorna 1 registro
 *   POST /api_save    - insere/atualiza
 *   POST /api_delete  - remove por id
 *
 * Como usar no controller:
 *   use ApiCrudTrait;
 *   protected $api_table = 'clientes';
 *   protected $api_search_fields = ['nomeCliente', 'documento', 'email'];
 *   protected $api_default_order = ['id', 'desc'];
 *   protected $api_hidden = ['senha'];           // colunas que NAO devem ir pro front
 *   protected $api_joins = [];                    // [['table'=>..., 'on'=>..., 'select'=>..., 'type'=>'left']]
 *
 * Os metodos exigem AJAX. Permissao: herda do controller ou usa $api_required_permission.
 */

trait ApiCrudTrait
{
    /**
     * GET /index.php/<controller>/api_list?search=foo&page=1&limit=25&orderBy=id&orderDir=desc
     */
    public function api_list()
    {
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(400);
            return;
        }
        if (!$this->_apiCheckPermission()) {
            $this->_apiForbidden();
            return;
        }

        try {
            $search     = trim((string) $this->input->get('search', true));
            $page       = max(1, (int) $this->input->get('page'));
            $limit      = min(100, max(1, (int) ($this->input->get('limit') ?: 25)));
            $offset     = ($page - 1) * $limit;
            $orderBy    = $this->input->get('orderBy') ?: ($this->api_default_order[0] ?? 'id');
            $orderDir   = strtolower($this->input->get('orderDir') ?: ($this->api_default_order[1] ?? 'desc'));
            $orderDir   = in_array($orderDir, ['asc', 'desc'], true) ? $orderDir : 'desc';

            $table = $this->api_table;
            $this->db->from($table);

            // Joins opcionais
            if (!empty($this->api_joins) && is_array($this->api_joins)) {
                foreach ($this->api_joins as $j) {
                    $this->db->join($j['table'], $j['on'], $j['type'] ?? 'left', false);
                }
            }

            // Busca
            if ($search !== '' && !empty($this->api_search_fields)) {
                $this->db->group_start();
                foreach ($this->api_search_fields as $i => $f) {
                    if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $f)) continue; // anti SQLi em field names
                    $this->db->like($f, $search, 'both', false);
                    if ($i < count($this->api_search_fields) - 1) $this->db->or_like($f, $search, 'both', false);
                }
                $this->db->group_end();
            }

            // Filtros extras via query string: ?status=Aberto&cliente_id=5
            foreach ($this->input->get() as $k => $v) {
                if (in_array($k, ['search','page','limit','orderBy','orderDir'], true)) continue;
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $k)) continue;
                $this->db->where($table.'.'.$k, $v);
            }

            $total = $this->db->count_all_results(null, false);

            $this->db->order_by($orderBy, $orderDir);
            $this->db->limit($limit, $offset);
            $rows = $this->db->get()->result_array();

            $rows = $this->_apiHideFields($rows);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'data'    => $rows,
                    'total'   => (int) $total,
                    'page'    => $page,
                    'limit'   => $limit,
                ]));
        } catch (Exception $e) {
            log_message('error', get_class($this).'::api_list erro: '.$e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'error' => 'Erro interno']));
        }
    }

    /**
     * GET /index.php/<controller>/api_get/$id
     */
    public function api_get($id = null)
    {
        if (!$this->input->is_ajax_request()) { $this->output->set_status_header(400); return; }
        if (!$this->_apiCheckPermission()) { $this->_apiForbidden(); return; }
        $id = (int) $id;
        if ($id <= 0) { $this->_apiNotFound(); return; }

        $this->db->from($this->api_table);
        if (!empty($this->api_joins) && is_array($this->api_joins)) {
            foreach ($this->api_joins as $j) {
                $this->db->join($j['table'], $j['on'], $j['type'] ?? 'left', false);
            }
        }
        $row = $this->db->where($this->api_table.'.id', $id)->get()->row_array();
        if (!$row) { $this->_apiNotFound(); return; }

        $row = $this->_apiHideFields([$row])[0];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true, 'data' => $row]));
    }

    /**
     * POST /index.php/<controller>/api_save
     */
    public function api_save()
    {
        if (!$this->input->is_ajax_request()) { $this->output->set_status_header(400); return; }
        if (!$this->_apiCheckPermission()) { $this->_apiForbidden(); return; }

        $input = json_decode($this->input->raw_input_stream, true) ?: $this->input->post();
        if (!is_array($input) || empty($input)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'error' => 'Payload vazio']));
            return;
        }

        $id = isset($input['id']) ? (int) $input['id'] : 0;
        $data = $input;
        unset($data['id']);

        // Filtra apenas campos que existem na tabela
        $fields = $this->db->list_fields($this->api_table);
        $data = array_intersect_key($data, array_flip($fields));

        if ($id > 0) {
            $this->db->where('id', $id)->update($this->api_table, $data);
        } else {
            $this->db->insert($this->api_table, $data);
            $id = (int) $this->db->insert_id();
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true, 'id' => $id]));
    }

    /**
     * POST /index.php/<controller>/api_delete/$id
     */
    public function api_delete($id = null)
    {
        if (!$this->input->is_ajax_request()) { $this->output->set_status_header(400); return; }
        if (!$this->_apiCheckPermission()) { $this->_apiForbidden(); return; }
        $id = (int) ($id ?: $this->input->post('id'));
        if ($id <= 0) { $this->_apiNotFound(); return; }
        $this->db->where('id', $id)->delete($this->api_table);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true]));
    }

    /* ---------- helpers ---------- */

    private function _apiCheckPermission(): bool
    {
        if (empty($this->api_required_permission)) return true;
        $permissao = $this->session->userdata('permissao');
        return $this->permission->checkPermission($permissao, $this->api_required_permission)
            || $this->permission->checkPermission($permissao, 'cPermissao');
    }

    private function _apiForbidden()
    {
        $this->output
            ->set_status_header(403)
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => false, 'error' => 'Sem permissao']));
    }

    private function _apiNotFound()
    {
        $this->output
            ->set_status_header(404)
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => false, 'error' => 'Nao encontrado']));
    }

    private function _apiHideFields(array $rows): array
    {
        if (empty($this->api_hidden) || empty($rows)) return $rows;
        foreach ($rows as &$r) {
            foreach ($this->api_hidden as $f) unset($r[$f]);
        }
        return $rows;
    }
}
