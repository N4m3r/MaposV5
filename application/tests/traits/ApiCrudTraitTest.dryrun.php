<?php
/**
 * Validacao manual (sem phpunit) do ApiCrudTraitTest.
 * Simula os mesmos casos de teste do arquivo PHPUnit.
 *
 * Roda: php tests/traits/ApiCrudTraitTest.dryrun.php
 */
declare(strict_types=1);

// Carrega autoload do Composer (sem phpunit)
require __DIR__ . '/../../vendor/autoload.php';

// Carrega bootstrap
require __DIR__ . '/../bootstrap.php';

// Carrega o trait
require_once __DIR__ . '/../../traits/ApiCrudTrait.php';

// Implementacao host igual a do teste
$host = new class {
    use \Application\Traits\ApiCrudTrait;

    public ?array $api_rules = null;
    public ?array $api_hidden = null;

    public function callValidate(array $data, bool $isUpdate = false): array
    {
        $r = new \ReflectionMethod($this, '_apiValidate');
        $r->setAccessible(true);
        return $r->invoke($this, $data, $isUpdate);
    }

    public function callCheckRule(string $field, $value, string $rule, bool $isUpdate = false): ?string
    {
        $r = new \ReflectionMethod($this, '_apiCheckRule');
        $r->setAccessible(true);
        return $r->invoke($this, $field, $value, $rule, $isUpdate);
    }

    public function callSanitize(array $data): array
    {
        $r = new \ReflectionMethod($this, '_apiSanitize');
        $r->setAccessible(true);
        return $r->invoke($this, $data);
    }

    public function callHideFields(array $rows): array
    {
        $r = new \ReflectionMethod($this, '_apiHideFields');
        $r->setAccessible(true);
        return $r->invoke($this, $rows);
    }
};

// Conjunto de casos de teste
$cases = [
    ['name' => 'required: null fails', 'fn' => fn() => $host->callCheckRule('nome', null, 'required', false), 'expected' => 'error'],
    ['name' => 'required: "Joao" passes', 'fn' => fn() => $host->callCheckRule('nome', 'Joao', 'required', false), 'expected' => 'null'],
    ['name' => 'email: valid', 'fn' => fn() => $host->callCheckRule('email', 'a@b.com', 'email', false), 'expected' => 'null'],
    ['name' => 'email: invalid', 'fn' => fn() => $host->callCheckRule('email', 'nope', 'email', false), 'expected' => 'error'],
    ['name' => 'min:6 ok', 'fn' => fn() => $host->callCheckRule('senha', '123456', 'min:6', false), 'expected' => 'null'],
    ['name' => 'min:6 fail', 'fn' => fn() => $host->callCheckRule('senha', '123', 'min:6', false), 'expected' => 'error'],
    ['name' => 'max:2 ok', 'fn' => fn() => $host->callCheckRule('uf', 'SP', 'max:2', false), 'expected' => 'null'],
    ['name' => 'max:2 fail', 'fn' => fn() => $host->callCheckRule('uf', 'SPX', 'max:2', false), 'expected' => 'error'],
    ['name' => 'numeric: "123.45" ok', 'fn' => fn() => $host->callCheckRule('valor', '123.45', 'numeric', false), 'expected' => 'null'],
    ['name' => 'numeric: "abc" fail', 'fn' => fn() => $host->callCheckRule('valor', 'abc', 'numeric', false), 'expected' => 'error'],
    ['name' => 'integer: 5 ok', 'fn' => fn() => $host->callCheckRule('qtd', 5, 'integer', false), 'expected' => 'null'],
    ['name' => 'integer: 5.5 fail', 'fn' => fn() => $host->callCheckRule('qtd', 5.5, 'integer', false), 'expected' => 'error'],
    ['name' => 'date: ISO ok', 'fn' => fn() => $host->callCheckRule('data', '2026-06-15', 'date', false), 'expected' => 'null'],
    ['name' => 'date: BR fail', 'fn' => fn() => $host->callCheckRule('data', '15/06/2026', 'date', false), 'expected' => 'error'],
    ['name' => 'in: ok', 'fn' => fn() => $host->callCheckRule('status', 'Ativo', 'in:Ativo,Inativo', false), 'expected' => 'null'],
    ['name' => 'in: fail', 'fn' => fn() => $host->callCheckRule('status', 'X', 'in:Ativo,Inativo', false), 'expected' => 'error'],
];

$pass = 0; $fail = 0;
foreach ($cases as $c) {
    $r = $c['fn']();
    $actual = $r === null ? 'null' : 'error';
    $ok = $actual === $c['expected'];
    if ($ok) { $pass++; echo "  ✓ {$c['name']}\n"; }
    else { $fail++; echo "  ✗ {$c['name']} (expected {$c['expected']}, got {$actual})\n"; }
}

// Sanitize
$s = $host->callSanitize(['nome' => '  Joao  ']);
$ok = $s['nome'] === 'Joao';
if ($ok) { $pass++; echo "  ✓ sanitize: trim\n"; } else { $fail++; echo "  ✗ sanitize: trim\n"; }

$s = $host->callSanitize(['x' => "A\x01B\x7F"]);
$ok = $s['x'] === 'AB';
if ($ok) { $pass++; echo "  ✓ sanitize: remove controls\n"; } else { $fail++; echo "  ✗ sanitize: remove controls ({$s['x']})\n"; }

$s = $host->callSanitize(['itens' => [['n' => '  P1  '], ['n' => 'P2']]]);
$ok = $s['itens'][0]['n'] === 'P1' && $s['itens'][1]['n'] === 'P2';
if ($ok) { $pass++; echo "  ✓ sanitize: recurse\n"; } else { $fail++; echo "  ✗ sanitize: recurse\n"; }

echo "\nTotal: $pass passed, $fail failed\n";
exit($fail === 0 ? 0 : 1);
