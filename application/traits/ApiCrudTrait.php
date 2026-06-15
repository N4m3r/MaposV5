<?php
/**
 * Trait ApiCrudTrait v2
 *
 * Endpoints REST genericos com validacao, sanitizacao, CSRF e auditoria:
 *   GET  /<controller>/api_list    - lista com busca, paginacao, ordenacao, filtros
 *   GET  /<controller>/api_get/$id - retorna 1 registro
 *   POST /<controller>/api_save    - insere/atualiza (com validacao)
 *   POST /<controller>/api_delete  - remove por id
 *
 * Configuracao no controller:
 *   use ApiCrudTrait;
 *
 *   protected $api_table = 'clientes';
 *   protected $api_pk = 'idClientes';                      // default: 'id'
 *   protected $api_search_fields = ['nomeCliente', 'documento'];
 *   protected $api_default_order = ['id', 'desc'];
 *   protected $api_required_permission = 'vCliente';
 *   protected $api_hidden = ['senha'];                     // colunas que NAO devem ir pro front
 *   protected $api_joins = [];                              // [['table'=>..., 'on'=>..., 'type'=>'left']]
 *
 *   // Validacao por campo (opcional):
 *   protected $api_rules = [
 *       'nomeCliente' => ['required', 'min:3', 'max:100'],
 *       'email'       => ['email'],
 *       'documento'   => ['max:20'],
 *   ];
 *
 *   // Sanitizacao (opcional, default true):
 *   protected $api_sanitize = true;
 *
 *   // CSRF check (opcional, default true se CSRF estiver habilitado):
 *   protected $api_csrf = true;
 *
 *   // Auditoria automatica (opcional, default false):
 *   protected $api_audit = true;
 *
 * Regras suportadas em $api_rules:
 *   - required
 *   - email
 *   - min:N / max:N
 *   - numeric / integer
 *   - date (YYYY-MM-DD)
 *   - in:a,b,c
 */

trait ApiCrudTrait
{
    /**
     * GET /index.php/<controller>/api_list?search=foo&page=1&limit=25&orderBy=id&orderDir=desc
     */
    public function api_list()
    {
        if (!$this->_apiGate('get')) return;

        try {
            $search   = trim((string) $this->input->get('search', true));
            $page     = max(1, (int) $this->input->get('page'));
            $limit    = min(100, max(1, (int) ($this->input->get('limit') ?: 25)));
            $offset   = ($page - 1) * $limit;
            $orderBy  = $this->input->get('orderBy') ?: ($this->api_default_order[0] ?? 'id');
            $orderDir = strtolower($this->input->get('orderDir') ?: ($this->api_default_order[1] ?? 'desc'));
            $orderDir = in_array($orderDir, ['asc', 'desc'], true) ? $orderDir : 'desc';

            // Validacao de field names (anti SQLi)
            if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $orderBy)) $orderBy = $this->api_default_order[0] ?? 'id';

            $table = $this->api_table;
            $this->db->from($table);

            if (!empty($this->api_joins) && is_array($this->api_joins)) {
                foreach ($this->api_joins as $j) {
                    if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $j['table'] ?? '')) continue;
                    $this->db->join($j['table'], $j['on'], $j['type'] ?? 'left', false);
                }
            }

            if ($search !== '' && !empty($this->api_search_fields)) {
                $this->db->group_start();
                foreach ($this->api_search_fields as $i => $f) {
                    if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $f)) continue;
                    $this->db->like($f, $search, 'both', false);
                    if ($i < count($this->api_search_fields) - 1) $this->db->or_like($f, $search, 'both', false);
                }
                $this->db->group_end();
            }

            foreach ($this->input->get() as $k => $v) {
                if (in_array($k, ['search', 'page', 'limit', 'orderBy', 'orderDir'], true)) continue;
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $k)) continue;
                $this->db->where($table . '.' . $k, $v);
            }

            $total = $this->db->count_all_results(null, false);

            $this->db->order_by($orderBy, $orderDir);
            $this->db->limit($limit, $offset);
            $rows = $this->db->get()->result_array();

            $rows = $this->_apiHideFields($rows);

            $this->_apiRespond([
                'success' => true,
                'data'    => $rows,
                'total'   => (int) $total,
                'page'    => $page,
                'limit'   => $limit,
            ]);
        } catch (Exception $e) {
            log_message('error', get_class($this) . '::api_list erro: ' . $e->getMessage());
            $this->_apiError(500, 'Erro interno ao listar');
        }
    }

    /**
     * GET /index.php/<controller>/api_get/$id
     */
    public function api_get($id = null)
    {
        if (!$this->_apiGate('get')) return;
        $id = (int) $id;
        if ($id <= 0) { $this->_apiError(404, 'ID invalido'); return; }

        $this->db->from($this->api_table);
        if (!empty($this->api_joins) && is_array($this->api_joins)) {
            foreach ($this->api_joins as $j) {
                $this->db->join($j['table'], $j['on'], $j['type'] ?? 'left', false);
            }
        }
        $row = $this->db->where($this->api_table . '.' . ($this->api_pk ?? 'id'), $id)->get()->row_array();
        if (!$row) { $this->_apiError(404, 'Registro nao encontrado'); return; }

        $row = $this->_apiHideFields([$row])[0];
        $this->_apiRespond(['success' => true, 'data' => $row]);
    }

    /**
     * POST /index.php/<controller>/api_save
     * Body: JSON ou form-urlencoded.
     * Para update, envie a PK dentro do payload (idOs, idVendas, etc).
     */
    public function api_save()
    {
        if (!$this->_apiGate('post')) return;

        $input = json_decode($this->input->raw_input_stream, true);
        if (!is_array($input)) $input = $this->input->post();
        if (!is_array($input) || empty($input)) {
            $this->_apiError(400, 'Payload vazio ou invalido');
            return;
        }

        $pk = $this->api_pk ?? 'id';
        $id = isset($input[$pk]) ? (int) $input[$pk] : (isset($input['id']) ? (int) $input['id'] : 0);
        $data = $input;
        unset($data['id'], $data[$pk]);

        // Filtra apenas campos que existem na tabela
        $fields = $this->db->list_fields($this->api_table);
        $data = array_intersect_key($data, array_flip($fields));

        // Validacao
        $errors = $this->_apiValidate($data, $id > 0);
        if (!empty($errors)) {
            $this->_apiError(422, 'Erro de validacao', $errors);
            return;
        }

        // Sanitizacao
        if ($this->api_sanitize ?? true) {
            $data = $this->_apiSanitize($data);
        }

        // Auditoria
        $userId = (int) $this->session->userdata('id');
        if ($this->api_audit ?? false) {
            $now = date('Y-m-d H:i:s');
            if ($id <= 0) {
                if (in_array('created_at', $fields)) $data['created_at'] = $now;
                if (in_array('created_by', $fields) && $userId > 0) $data['created_by'] = $userId;
            }
            if (in_array('updated_at', $fields)) $data['updated_at'] = $now;
            if (in_array('updated_by', $fields) && $userId > 0) $data['updated_by'] = $userId;
        }

        try {
            if ($id > 0) {
                $this->db->where($pk, $id)->update($this->api_table, $data);
            } else {
                $this->db->insert($this->api_table, $data);
                $id = (int) $this->db->insert_id();
            }
            $this->_apiRespond(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            log_message('error', get_class($this) . '::api_save erro: ' . $e->getMessage());
            $this->_apiError(500, 'Erro ao salvar no banco');
        }
    }

    /**
     * POST /index.php/<controller>/api_delete/$id
     */
    public function api_delete($id = null)
    {
        if (!$this->_apiGate('post')) return;
        $id = (int) ($id ?: $this->input->post('id'));
        if ($id <= 0) { $this->_apiError(404, 'ID invalido'); return; }
        $pk = $this->api_pk ?? 'id';
        try {
            $this->db->where($pk, $id)->delete($this->api_table);
            $this->_apiRespond(['success' => true]);
        } catch (Exception $e) {
            // Provavelmente FK constraint
            log_message('error', get_class($this) . '::api_delete erro: ' . $e->getMessage());
            $this->_apiError(409, 'Nao foi possivel excluir (registro pode estar sendo usado)');
        }
    }

    /* ====================================================== */
    /* ==== Hooks overridable nos controllers =============== */
    /* ====================================================== */

    /**
     * Hook chamado DEPOIS de salvar (insert ou update).
     * Sobrescreva no controller para logica custom.
     */
    protected function _apiAfterSave($id, $isNew, $data)
    {
    }

    /**
     * Hook chamado ANTES de excluir. Retorne false pra cancelar.
     */
    protected function _apiBeforeDelete($id): bool
    {
        return true;
    }

    /* ====================================================== */
    /* ==== Helpers internos ================================= */
    /* ====================================================== */

    /**
     * Gatekeeper: AJAX, permissao, CSRF.
     * @param string $method 'get' ou 'post'
     * @return bool true se passou, false se ja respondeu com erro
     */
    private function _apiGate(string $method): bool
    {
        if (!$this->input->is_ajax_request()) {
            $this->_apiError(400, 'Requer requisicao AJAX');
            return false;
        }
        if ($method === 'post' && ($this->api_csrf ?? true)) {
            $token = $this->input->post(config_item('csrf_token_name'))
                ?: $this->input->get(config_item('csrf_token_name'))
                ?: $this->input->get_request_header('X-CSRF-Token', true);
            if (!$token || $token !== $this->session->userdata('csrf_token')) {
                $this->_apiError(403, 'Token CSRF invalido');
                return false;
            }
        }
        if (!$this->_apiCheckPermission()) {
            $this->_apiError(403, 'Voce nao tem permissao para esta acao');
            return false;
        }
        return true;
    }

    private function _apiCheckPermission(): bool
    {
        if (empty($this->api_required_permission)) return true;
        $permissao = $this->session->userdata('permissao');
        return $this->permission->checkPermission($permissao, $this->api_required_permission)
            || $this->permission->checkPermission($permissao, 'cPermissao');
    }

    /**
     * Valida $data contra $api_rules. Retorna array de erros (vazio = OK).
     * @param bool $isUpdate true se for update (pk required fica dispensado)
     */
    private function _apiValidate(array $data, bool $isUpdate = false): array
    {
        $rules = $this->api_rules ?? [];
        if (empty($rules)) return [];
        $errors = [];
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            foreach ($fieldRules as $r) {
                $e = $this->_apiCheckRule($field, $value, $r, $isUpdate);
                if ($e !== null) {
                    $errors[$field][] = $e;
                    break; // 1 erro por campo
                }
            }
        }
        return $errors;
    }

    private function _apiCheckRule(string $field, $value, string $rule, bool $isUpdate): ?string
    {
        [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);
        switch ($name) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    return "O campo {$field} eh obrigatorio";
                }
                break;
            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "{$field} deve ser um e-mail valido";
                }
                break;
            case 'min':
                $n = (int) $arg;
                if (is_string($value) && mb_strlen($value) < $n) {
                    return "{$field} deve ter no minimo {$n} caracteres";
                }
                if (is_numeric($value) && $value < $n) {
                    return "{$field} deve ser no minimo {$n}";
                }
                break;
            case 'max':
                $n = (int) $arg;
                if (is_string($value) && mb_strlen($value) > $n) {
                    return "{$field} deve ter no maximo {$n} caracteres";
                }
                if (is_numeric($value) && $value > $n) {
                    return "{$field} deve ser no maximo {$n}";
                }
                break;
            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    return "{$field} deve ser numerico";
                }
                break;
            case 'integer':
                if ($value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    return "{$field} deve ser um numero inteiro";
                }
                break;
            case 'date':
                if ($value !== null && $value !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $value)) {
                    return "{$field} deve estar no formato AAAA-MM-DD";
                }
                break;
            case 'in':
                $allowed = array_map('trim', explode(',', (string) $arg));
                if ($value !== null && $value !== '' && !in_array((string) $value, $allowed, true)) {
                    return "{$field} deve ser um dos valores: " . implode(', ', $allowed);
                }
                break;
        }
        return null;
    }

    /**
     * Sanitizacao basica anti-XSS. Aplica htmlspecialchars em strings.
     */
    private function _apiSanitize(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = trim($v);
                // Nao escapa — deixa o React escapar no render. So remove controles.
                $data[$k] = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $data[$k]);
            } elseif (is_array($v)) {
                $data[$k] = $this->_apiSanitize($v);
            }
        }
        return $data;
    }

    private function _apiHideFields(array $rows): array
    {
        if (empty($this->api_hidden) || empty($rows)) return $rows;
        foreach ($rows as &$r) {
            foreach ($this->api_hidden as $f) unset($r[$f]);
        }
        return $rows;
    }

    private function _apiRespond(array $data, int $status = 200)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function _apiError(int $status, string $message, array $errors = null)
    {
        $body = ['success' => false, 'error' => $message];
        if ($errors) $body['errors'] = $errors;
        $this->_apiRespond($body, $status);
    }
}
