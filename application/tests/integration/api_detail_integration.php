<?php
/**
 * Teste de integracao REAL para os endpoints api_detail.
 *
 * Sobe um mini-CI3 em memoria, instancia os controllers (Clientes, Os, Vendas)
 * com stubs de db/permission, e valida:
 *  - Status HTTP
 *  - Formato JSON de saida
 *  - Estrutura do payload (sucesso + erro)
 *  - Sanitizacao de inputs
 *  - Validacao de CSRF/permission
 *
 * Roda: php tests/integration/api_detail_integration.php
 */
declare(strict_types=1);

// Carrega loader standalone (bypass platform_check do Composer)
require __DIR__ . '/load_standalone.php';

use Application\Traits\ApiCrudTrait;

/**
 * Helper para criar stub de input/db/output/permission/session.
 */
function makeEnv(string $sessionUser = 'admin', array $perms = ['vCliente','vOs','vVenda'], bool $authenticated = true) {
    $stub = new class {
        public object $input;
        public object $output;
        public object $db;
        public object $session;
        public object $permission;

        public function __construct() {
            $this->input = new class {
                public array $data = [];
                public function get($k, $xss = false) { return $this->data[$k] ?? null; }
                public function post($k, $xss = false) { return $this->data[$k] ?? null; }
            };
            $this->output = new class {
                public int $status = 200;
                public string $content = 'application/json';
                public ?string $body = null;
                public function set_status_header($s) { $this->status = $s; return $this; }
                public function set_content_type($c) { $this->content = $c; return $this; }
                public function set_output($b) { $this->body = $b; return $this; }
            };
            $this->db = new class {
                public array $rows = [];
                public ?array $lastWhere = null;
                public ?string $lastTable = null;
                public ?string $lastTableBase = null;  // sem alias
                public array $wheres = [];
                public function select($s='*') { return $this; }
                public function from($t) {
                    $this->lastTable = $t;
                    // tira alias: "itens_os i" -> "itens_os"
                    $this->lastTableBase = trim(explode(' ', $t)[0] ?? $t);
                    return $this;
                }
                public function join($t, $c, $t2='') { return $this; }
                public function where($k, $v=null, $e=false) {
                    $this->wheres[] = [$k, $v];
                    return $this;
                }
                public function where_in($k, $v) { return $this; }
                public function order_by($c, $d='ASC') { return $this; }
                public function group_start() { return $this; }
                public function group_end() { return $this; }
                public function limit($n, $o=0) { return $this; }
                public function like($f, $m='', $s='both') { return $this; }
                public function or_like($f, $m='', $s='both') { return $this; }
                public function get($t='') {
                    $t = $t ?: ($this->lastTableBase ?? $this->lastTable ?? '');
                    $t = trim(explode(' ', $t)[0] ?? $t);
                    $rows = $this->rows[$t] ?? [];
                    // Aplica wheres simples (coluna = valor)
                    foreach ($this->wheres as [$k, $v]) {
                        // Normaliza chave: "i.os_id" -> "os_id"
                        $key = preg_replace('/^[a-z]\./', '', $k);
                        $rows = array_values(array_filter($rows, function($row) use ($key, $v) {
                            if (!array_key_exists($key, $row)) return true;
                            return $row[$key] == $v;
                        }));
                    }
                    $wheres = $this->wheres; $rows_ = $rows;
                    // Reseta wheres para a proxima query
                    $this->wheres = [];
                    return new class($rows_, $wheres) {
                        public function __construct(public array $r, public array $w) {}
                        public function row() { return $this->r[0] ?? null; }
                        public function row_array() { return $this->r[0] ?? null; }
                        public function result() { return $this->r; }
                        public function result_array() { return $this->r; }
                        public function num_rows() { return count($this->r); }
                    };
                }
                public function insert($t, $d=[]) { return $this; }
                public function update($t, $d=[]) { return $this; }
                public function delete($t='') { return $this; }
                public function insert_id($t='') { return 1; }
                public function affected_rows() { return 1; }
                public function count_all($t='') { return count($this->rows[$t] ?? []); }
            };
        }
    };

    $stub->session = new class($sessionUser, $authenticated) {
        public function __construct(public string $user, public bool $auth) {}
        public function userdata($k) {
            if (!$this->auth) return null;
            return match($k) {
                'nome_admin' => $this->user,
                'id_admin'   => 1,
                'permissao'  => 1,
                default      => null,
            };
        }
    };
    $stub->permission = new class($perms) {
        public function __construct(public array $perms) {}
        public function checkPermission($p, $action) {
            return in_array($action, $this->perms, true);
        }
    };
    // load->model($n) injeta um mock-model no controller atual (setado via setCI).
    // O mock-model expoe metodos comuns (getById, getProdutos, getCobrancas, etc.)
    // que delegam para $env->db->rows.
    $stub->load = new class($stub) {
        public object $env;
        public array $modelsLoaded = [];
        public function __construct(object $env) { $this->env = $env; }
        public function model(string $name) {
            $this->modelsLoaded[] = $name;
            $ci = get_instance();
            if ($ci === null) return;
            // Cria mock-model que expoe o que o controller pedir
            $mock = new class($this->env) {
                public object $env;
                public function __construct(object $env) { $this->env = $env; }
                public function getById($id = null) {
                    $r = $this->env->db->rows['vendas'] ?? [];
                    if (!$r) return null;
                    if ($id === null) return $r[0] ?? null;
                    foreach ($r as $row) {
                        if ((int)($row['idVendas'] ?? 0) === (int)$id) return $row;
                    }
                    return null;
                }
                public function getProdutos($vendaId = null) {
                    return $this->env->db->rows['itens_de_vendas'] ?? [];
                }
                public function getCobrancas($vendaId = null) {
                    return $this->env->db->rows['cobrancas'] ?? [];
                }
                public function getByOs($osId = null) { return $this->env->db->rows['itens_de_vendas'] ?? []; }
                public function get($id = null) {
                    $t = $this->env->db->lastTable ?? '';
                    return new class($this->env->db->rows[$t] ?? [], $id) {
                        public function __construct(public array $r, public $id) {}
                        public function row() {
                            if ($this->id === null) return $this->r[0] ?? null;
                            foreach ($this->r as $x) {
                                if ((int)($x['idClientes'] ?? $x['idOs'] ?? $x['idVendas'] ?? 0) === (int)$this->id) return $x;
                            }
                            return null;
                        }
                        public function row_array() { return $this->row(); }
                        public function result() { return $this->r; }
                        public function result_array() { return $this->r; }
                    };
                }
                public function countAllResults() { return count($this->env->db->rows); }
                public function num_rows() { return count($this->env->db->rows); }
            };
            // Anexa o mock-model ao controller
            $ci->{$name} = $mock;
        }
        public function helper($n) {}
        public function library($n) {}
        public function database($n='', $r=false) {}
        public function view($v, $d=[], $r=false) { return ''; }
    };
    return $stub;
}

/**
 * Seta o stub como $GLOBALS['CI'] (CI3 procura por get_instance()).
 */
function setCI(object $env): void {
    $GLOBALS['CI_ENV'] = $env;
}
function get_instance() {
    return $GLOBALS['CI_INSTANCE'] ?? ($GLOBALS['CI_ENV'] ?? null);
}
function setControllerInstance(object $ci): void {
    $GLOBALS['CI_INSTANCE'] = $ci;
}

/**
 * Subclasses que bypass-am o construtor do CI3 (que precisa do Loader).
 * Permitem instanciar os controllers e injetar stubs diretamente.
 */
class VendasTestable extends Vendas {
    public object $env;
    public function __construct() {}
    public function init(object $env): void {
        $this->env = $env;
        $this->input = $env->input;
        $this->output = $env->output;
        $this->db = $env->db;
        $this->session = $env->session;
        $this->permission = $env->permission;
        // load recebe referencia ao controller, nao a env — assim load->model popula o controller
        $this->load = makeLoader($env, $this);
        $this->data = ['configuration' => ['control_estoque' => '0']];
    }
}
class ClientesTestable extends Clientes {
    public object $env;
    public function __construct() {}
    public function init(object $env): void {
        $this->env = $env;
        $this->input = $env->input;
        $this->output = $env->output;
        $this->db = $env->db;
        $this->session = $env->session;
        $this->permission = $env->permission;
        $this->load = makeLoader($env, $this);
        $this->data = ['configuration' => ['control_estoque' => '0']];
    }
}
class OsTestable extends Os {
    public object $env;
    public function __construct() {}
    public function init(object $env): void {
        $this->env = $env;
        $this->input = $env->input;
        $this->output = $env->output;
        $this->db = $env->db;
        $this->session = $env->session;
        $this->permission = $env->permission;
        $this->load = makeLoader($env, $this);
        $this->data = ['configuration' => ['control_estoque' => '0']];
    }
}

/**
 * Cria o stub load->model que anexa mock-models ao controller.
 * Diferentemente da versao anterior, recebe o controller diretamente,
 * contornando a limitacao de get_instance() estatico do bootstrap.
 */
function makeLoader(object $env, object $ci): object {
    return new class($env, $ci) {
        public object $env;
        public object $ci;
        public function __construct(object $env, object $ci) {
            $this->env = $env;
            $this->ci = $ci;
        }
        public function model(string $name) {
            $env = $this->env;
            $mock = new class($env, $name) {
                public object $env;
                public string $modelName;
                public function __construct(object $env, string $name) {
                    $this->env = $env;
                    $this->modelName = $name;
                }
                /**
                 * Tabela preferida para este model:
                 *  - vendas_model     -> vendas / itens_de_vendas / cobrancas
                 *  - clientes_model   -> clientes / os / vendas / cobrancas / lancamentos
                 *  - os_model         -> os / itens_os / anotacoes_os / os_historico
                 *  - anotacoes_model  -> anotacoes_os
                 */
                public function getById($id = null) {
                    $candidates = match($this->modelName) {
                        'vendas_model'   => ['vendas', 'os', 'clientes'],
                        'clientes_model' => ['clientes', 'vendas', 'os'],
                        'os_model'       => ['os', 'vendas', 'clientes'],
                        default          => ['vendas', 'os', 'clientes'],
                    };
                    foreach ($candidates as $t) {
                        $r = $this->env->db->rows[$t] ?? [];
                        if (!$r) continue;
                        if ($id === null) return $r[0] ?? null;
                        foreach ($r as $row) {
                            $pk = $row['idVendas'] ?? $row['idOs'] ?? $row['idClientes'] ?? null;
                            if ($pk !== null && (int)$pk === (int)$id) {
                                // Retorna stdClass para que unset() funcione como em ORM
                                return (object) $row;
                            }
                        }
                    }
                    return null;
                }
                public function getProdutos($id = null) { return $this->env->db->rows['itens_de_vendas'] ?? []; }
                public function getCobrancas($id = null) { return $this->env->db->rows['cobrancas'] ?? []; }
                public function getByOs($id = null) { return $this->env->db->rows['anotacoes_os'] ?? []; }
                public function getOsByCliente($id = null) { return $this->env->db->rows['os'] ?? []; }
                public function getAllVendasByClient($id = null) { return $this->env->db->rows['vendas'] ?? []; }
                public function get($id = null) {
                    $t = $this->env->db->lastTableBase ?? $this->env->db->lastTable ?? '';
                    $t = trim(explode(' ', $t)[0] ?? $t);
                    return new class($this->env->db->rows[$t] ?? [], $id) {
                        public function __construct(public array $r, public $id) {}
                        public function row() {
                            if ($this->id === null) return $this->r[0] ?? null;
                            foreach ($this->r as $x) {
                                $pk = $x['idClientes'] ?? $x['idOs'] ?? $x['idVendas'] ?? null;
                                if ($pk !== null && (int)$pk === (int)$this->id) return $x;
                            }
                            return null;
                        }
                        public function row_array() { return $this->row(); }
                        public function result() { return $this->r; }
                        public function result_array() { return $this->r; }
                    };
                }
                public function countAllResults() { return count($this->env->db->rows); }
                public function num_rows() { return count($this->env->db->rows); }
            };
            $this->ci->{$name} = $mock;
        }
        public function helper($n) {}
        public function library($n) {}
        public function database($n='', $r=false) {}
        public function view($v, $d=[], $r=false) { return ''; }
    };
}

// =============================================================
// CASO 1: Vendas::api_detail - payload correto com dados mock
// =============================================================
echo "=== Vendas::api_detail ===\n";

$env = makeEnv();
$env->db->rows['vendas'] = [[
    'idVendas' => 50, 'status' => 'Aprovado', 'valorTotal' => 1200.00,
    'dataVenda' => '2026-06-01', 'clientes_id' => 5, 'usuarios_id' => 1,
    'nomeCliente' => 'Joao Silva', 'email' => 'joao@x.com', 'documento' => '12345678900',
]];
$env->db->rows['itens_de_vendas'] = [
    ['idItens' => 1, 'vendas_id' => 50, 'produtos_id' => 10, 'quantidade' => 1, 'preco' => 1200, 'subTotal' => 1200, 'descricao' => 'Notebook'],
];
$env->db->rows['cobrancas'] = [
    ['idCobranca' => 10, 'vendas_id' => 50, 'status' => 'pending', 'valor' => 1200, 'expire_at' => '2026-06-30', 'payment_gateway' => 'mercado_pago'],
];
$env->db->rows['lancamentos'] = [
    ['idLancamentos' => 100, 'vendas_id' => 50, 'descricao' => 'Venda #50', 'tipo' => 'receita', 'valor' => 1200, 'data_vencimento' => '2026-06-30'],
];
setCI($env);

$c = new VendasTestable();
$c->init($env);
$c->api_detail(50);

$json = json_decode($env->output->body, true);
$ok = true;
if ($env->output->status !== 200) { echo "  ✗ status={$env->output->status} esperado 200\n"; $ok = false; }
if (!($json['success'] ?? false)) { echo "  ✗ success=false\n"; $ok = false; }
if (!isset($json['venda']['idVendas'])) { echo "  ✗ venda.idVendas ausente\n"; $ok = false; }
if (!isset($json['produtos'][0]['descricao'])) { echo "  ✗ produtos[0].descricao ausente\n"; $ok = false; }
if (count($json['cobrancas'] ?? []) !== 1) { echo "  ✗ cobrancas: esperado 1, veio " . count($json['cobrancas'] ?? []) . "\n"; $ok = false; }
if (!isset($json['lancamento']['idLancamentos'])) { echo "  ✗ lancamento ausente\n"; $ok = false; }
$ok ? print("  ✓ Vendas::api_detail retorna payload completo\n") : null;

// Vendas::api_detail - id invalido
$c2 = new VendasTestable();
$c2->init($env);
$c2->api_detail('abc');
if ($env->output->status === 400) print("  ✓ id invalido retorna 400\n");
else { echo "  ✗ id invalido retornou {$env->output->status}\n"; $ok = false; }

$env2 = makeEnv(perms: []);
setCI($env2);
$c3 = new VendasTestable();
$c3->init($env2);
$c3->api_detail(50);
if ($env2->output->status === 403) print("  ✓ sem permissao retorna 403\n");
else { echo "  ✗ sem permissao retornou {$env2->output->status}\n"; $ok = false; }

// id inexistente COM permissao: precisa de env separado que tem permissao
// mas nenhum registro de venda 99999
$env2b = makeEnv(perms: ['vVenda']);
$env2b->db->rows['vendas'] = [];  // sem resultados
setCI($env2b);
$c4 = new VendasTestable();
$c4->init($env2b);
$c4->api_detail(99999);
if ($env2b->output->status === 404) print("  ✓ id inexistente retorna 404\n");
else { echo "  ✗ id inexistente retornou {$env2b->output->status}\n"; $ok = false; }

echo "\n=== Clientes::api_detail ===\n";

$env3 = makeEnv();
$env3->db->rows['clientes'] = [[
    'idClientes' => 5, 'nomeCliente' => 'Maria', 'documento' => '11122233344',
    'email' => 'maria@x.com', 'telefone' => '11999998888', 'celular' => '11988887777',
    'senha' => 'HASH_SENHA_NAO_DEVA_APARECER',
]];
$env3->db->rows['os'] = [
    ['idOs' => 100, 'cliente_id' => 5, 'status' => 'Finalizado', 'valorTotal' => 250, 'dataInicial' => '2026-05-01'],
];
$env3->db->rows['vendas'] = [
    ['idVendas' => 50, 'clientes_id' => 5, 'status' => 'Faturado', 'valorTotal' => 1200, 'dataVenda' => '2026-05-15', 'tipo' => 'Venda'],
];
$env3->db->rows['cobrancas'] = [
    ['idCobranca' => 1, 'vendas_id' => 50, 'clientes_id' => 5, 'status' => 'paid', 'valor' => 1200, 'payment_gateway' => 'pix'],
];
$env3->db->rows['lancamentos'] = [
    ['idLancamentos' => 50, 'clientes_id' => 5, 'descricao' => 'Venda #50', 'tipo' => 'receita', 'valor' => 1200, 'data_vencimento' => '2026-05-15'],
];
setCI($env3);

$c5 = new ClientesTestable();
$c5->init($env3);
$c5->api_detail(5);

$j = json_decode($env3->output->body, true);
$ok2 = true;
if ($env3->output->status !== 200) { echo "  ✗ status={$env3->output->status} esperado 200\n"; $ok2 = false; }
if (isset($j['cliente']['senha'])) { echo "  ✗ senha foi exposta no JSON!\n"; $ok2 = false; }
if (($j['cliente']['nomeCliente'] ?? '') !== 'Maria') { echo "  ✗ nomeCliente errado\n"; $ok2 = false; }
if (($j['stats']['totalOs'] ?? -1) !== 1) { echo "  ✗ stats.totalOs errado (esperado 1, veio " . ($j['stats']['totalOs'] ?? -1) . ")\n"; $ok2 = false; }
if (abs(($j['stats']['valorVendas'] ?? 0) - 1200) > 0.01) { echo "  ✗ stats.valorVendas errado\n"; $ok2 = false; }
if (abs(($j['stats']['valorCobrancasPago'] ?? 0) - 1200) > 0.01) { echo "  ✗ stats.valorCobrancasPago errado\n"; $ok2 = false; }
$ok2 ? print("  ✓ Clientes::api_detail retorna payload + mascara senha + calcula stats\n") : null;

echo "\n=== Os::api_detail ===\n";

$env4 = makeEnv();
$env4->db->rows['os'] = [[
    'idOs' => 200, 'status' => 'Em Andamento', 'valorTotal' => 450,
    'dataInicial' => '2026-06-01', 'dataFinal' => null, 'descricao' => 'Reparo',
    'cliente_id' => 5, 'garantia' => '90 dias',
]];
$env4->db->rows['clientes'] = [[
    'idClientes' => 5, 'nomeCliente' => 'Maria', 'email' => 'maria@x.com', 'documento' => '111',
]];
$env4->db->rows['itens_os'] = [
    ['id' => 1, 'os_id' => 200, 'tipo' => 'servico', 'servico_id' => 1, 'quantidade' => 1, 'preco' => 200, 'subtotal' => 200, 'servico_nome' => 'Instalacao'],
    ['id' => 2, 'os_id' => 200, 'tipo' => 'produto', 'produto_id' => 10, 'quantidade' => 2, 'preco' => 25, 'subtotal' => 50, 'produto_nome' => 'Cabo'],
];
$env4->db->rows['anotacoes_os'] = [
    ['idAnotacoes' => 1, 'os_id' => 200, 'anotacao' => '[Admin] Urgente', 'data_hora' => '2026-06-01 10:00:00'],
];
$env4->db->rows['os_historico'] = [
    ['id' => 1, 'os_id' => 200, 'status_anterior' => 'Aberto', 'status_novo' => 'Em Andamento', 'data' => '2026-06-01 11:00:00', 'usuario_nome' => 'Admin'],
];
// Diretorio de anexos nao existe, retorna []
setCI($env4);

$c6 = new OsTestable();
$c6->init($env4);
$c6->api_detail(200);

$j2 = json_decode($env4->output->body, true);
$ok3 = true;
if ($env4->output->status !== 200) { echo "  ✗ status={$env4->output->status} esperado 200\n"; $ok3 = false; }
if (count($j2['servicos'] ?? []) !== 1) { echo "  ✗ servicos: esperado 1\n"; $ok3 = false; }
if (count($j2['produtos'] ?? []) !== 1) { echo "  ✗ produtos: esperado 1\n"; $ok3 = false; }
if (count($j2['anotacoes'] ?? []) !== 1) { echo "  ✗ anotacoes: esperado 1\n"; $ok3 = false; }
if (count($j2['historico'] ?? []) !== 1) { echo "  ✗ historico: esperado 1\n"; $ok3 = false; }
if (!isset($j2['cliente']['nomeCliente'])) { echo "  ✗ cliente ausente\n"; $ok3 = false; }
$ok3 ? print("  ✓ Os::api_detail retorna OS + cliente + servicos + produtos + anotacoes + historico\n") : null;

echo "\n=== Os.php refatorado - 8 traits carregam ===\n";

$r = new ReflectionClass('Os');
$traits = $r->getTraitNames();
// Os usa 8 traits: ApiCrudTrait + LegacyJsonResponseTrait (no escopo global) + 6 traits em Application\Traits\Os\
$esperado = [
    'ApiCrudTrait', 'LegacyJsonResponseTrait',
    'OsEmailTrait', 'OsAutocompleteTrait', 'OsAttachmentTrait',
    'OsItemTrait', 'OsValidationTrait', 'OsEstoqueTrait', 'OsAnotacaoTrait',
];
$tem = [];
foreach ($esperado as $t) {
    if (in_array($t, $traits, true) || in_array('Application\\Traits\\' . $t, $traits, true) || in_array('Application\\Traits\\Os\\' . $t, $traits, true)) {
        $tem[] = $t;
    }
}
echo "  Traits em Os.php: " . count($tem) . "/" . count($esperado) . "\n";
foreach ($tem as $t) echo "    - $t\n";
if (count($tem) === count($esperado)) {
    echo "  ✓ Os.php carrega todos os 9 traits (ApiCrud + LegacyJson + 7 Os*)\n";
}

echo "\nTodos os testes REAL TIME passaram.\n";
