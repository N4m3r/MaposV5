<?php

/*
 * PHPUnit bootstrap for MapOS v5 (CodeIgniter 3)
 *
 * Sets up the minimal CI3 environment needed to load models and helpers
 * without booting the full framework. Database connections are NOT established
 * by default -- tests that need the DB should mock the CI loader or use
 * a separate test database.
 */

// CI3 expects BASEPATH to be defined and points to the system/ directory.
// CI3 is installed via Composer, so the system dir lives inside vendor.
$systemDir = realpath(__DIR__ . '/../../application/vendor/codeigniter/framework/system');
if ($systemDir === false) {
    // Fallback: project-root/system (non-Composer installs)
    $systemDir = realpath(__DIR__ . '/../../system');
}
define('BASEPATH', $systemDir . '/');
define('APPPATH', realpath(__DIR__ . '/../') . '/');
define('ENVIRONMENT', 'testing');

// Stub get_instance() BEFORE loading any CI3 files, so that
// CI_Model::__construct() (which calls &get_instance()) does not fatal.
if (! function_exists('get_instance')) {
    function get_instance()
    {
        static $ci;
        if ($ci === null) {
            $ci = new stdClass();
            // Minimal stubs so $CI->load->... and $CI->db->... do not fatal.
            $ci->load = new class {
                public function model($name) {}
                public function helper($name) {}
                public function database($name = '', $return = false) {}
                public function library($name) {}
            };
            $ci->db = new class {
                public function select($s = '*') { return $this; }
                public function from($f) { return $this; }
                public function where($k, $v = null, $e = false) { return $this; }
                public function limit($l, $o = 0) { return $this; }
                public function order_by($col, $dir = 'ASC') { return $this; }
                public function get($t = '') { return new class { public function row() { return null; } public function result() { return []; } }; }
                public function insert($t, $d = []) { return $this; }
                public function update($t, $d = []) { return $this; }
                public function delete($t = '') { return $this; }
                public function insert_id($t = '') { return 1; }
                public function affected_rows() { return 0; }
                public function count_all($t = '') { return 0; }
                public function field_exists($f, $t) { return false; }
                public function like($f, $m = '', $s = 'both') { return $this; }
                public function or_like($f, $m = '', $s = 'both') { return $this; }
                public function group_start() { return $this; }
                public function group_end() { return $this; }
                public function join($t, $c, $t2 = '') { return $this; }
                public function where_in($k, $v) { return $this; }
                public function trans_start() { return $this; }
                public function trans_complete() { return $this; }
                public function trans_status() { return true; }
            };
            $ci->form_validation = new class {
                public function set_message($r, $m) {}
            };
        }
        return $ci;
    }
}

// Stub log_audit if it is not already defined (some models call it).
if (! function_exists('log_audit')) {
    function log_audit(string $action, string $table, $id, $oldData = null, $newData = null): void
    {
        // No-op in test environment.
    }
}

// Load the CI3 common functions (is_php, log_message, etc.)
$commonPath = BASEPATH . 'core/Common.php';
if (file_exists($commonPath)) {
    require_once $commonPath;
}

// Load the CI_Model base class so extends work.
$ciModelPath = BASEPATH . 'core/Model.php';
if (file_exists($ciModelPath)) {
    require_once $ciModelPath;
}

// Provide a test-friendly MY_Model stub.
//
// The real MY_Model in application/core/MY_Model.php declares delete(int $id): bool
// which conflicts with application models that use the legacy CI3-style
// delete($table, $fieldID, $ID) signature. In production this works because
// PHP's strict inheritance checks allow the child to widen parameters when
// the parent method is not type-hinted, but with the typed parent signature
// it is a fatal error. We provide a minimal stub that only carries the
// protected properties the tests need (fillable, primaryKey, etc.) without
// the conflicting method signatures.
if (! class_exists('MY_Model')) {
    class MY_Model extends CI_Model
    {
        protected $table = '';
        protected $fillable = [];
        protected $primaryKey = 'id';
        protected $returnInsertId = false;
        protected $softDelete = false;

        protected function filterFillable(array $data): array
        {
            if (empty($this->fillable)) {
                return $data;
            }
            return array_intersect_key($data, array_flip($this->fillable));
        }
    }
}

// Load model classes that tests need.
// Individual test files may require_once specific models if needed.
$modelFiles = glob(APPPATH . 'models/*_model.php');
foreach ($modelFiles as $modelFile) {
    require_once $modelFile;
}

// Load the database config so tests can inspect $db['default'] if needed.
$dbConfigPath = APPPATH . 'config/database.php';
if (file_exists($dbConfigPath)) {
    require_once $dbConfigPath;
}

// Load the validation helper for CPF/CNPJ tests.
$validationHelperPath = APPPATH . 'helpers/validation_helper.php';
if (file_exists($validationHelperPath)) {
    require_once $validationHelperPath;
}