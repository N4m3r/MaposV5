<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Application\Traits\ApiCrudTrait;

/**
 * Testes unitarios do ApiCrudTrait.
 *
 * Foca nos metodos puros (sem dependencia de DB):
 *   - _apiValidate  (validacao de regras)
 *   - _apiCheckRule (cada regra: required, email, min, max, numeric, integer, date, in)
 *   - _apiSanitize  (limpeza anti-XSS)
 *   - _apiHideFields (mascarar colunas sensiveis)
 *
 * Metodos que dependem de $this->db / $this->output nao sao testados aqui
 * — esses serao cobertos por testes de integracao com SQLite/mocks de DB.
 *
 * @covers \Application\Traits\ApiCrudTrait
 */
class ApiCrudTraitTest extends TestCase
{
    /** Host minimo que carrega o trait — permite acessar metodos privados via Reflection */
    private object $host;

    protected function setUp(): void
    {
        parent::setUp();
        $this->host = new class {
            use ApiCrudTrait;

            // Propriedades configuraveis por teste
            public ?array $api_rules = null;
            public ?array $api_hidden = null;

            public function setRules(array $r): void { $this->api_rules = $r; }
            public function setHidden(array $h): void { $this->api_hidden = $h; }

            // Wrappers publicos para chamar metodos private via Reflection
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
    }

    // ---------------------------------------------------------------------
    // _apiCheckRule — uma assertion por regra
    // ---------------------------------------------------------------------

    public function testRuleRequiredPassesWhenValueIsPresent(): void
    {
        $err = $this->host->callCheckRule('nome', 'Joao', 'required', false);
        $this->assertNull($err);
    }

    public function testRuleRequiredFailsWhenValueIsNull(): void
    {
        $err = $this->host->callCheckRule('nome', null, 'required', false);
        $this->assertNotNull($err);
        $this->assertStringContainsString('nome', $err);
        $this->assertStringContainsString('obrigatorio', $err);
    }

    public function testRuleRequiredFailsWhenValueIsEmptyString(): void
    {
        $err = $this->host->callCheckRule('nome', '', 'required', false);
        $this->assertNotNull($err);
    }

    public function testRuleRequiredFailsWhenValueIsEmptyArray(): void
    {
        $err = $this->host->callCheckRule('tags', [], 'required', false);
        $this->assertNotNull($err);
    }

    public function testRuleEmailPassesForValidEmail(): void
    {
        $err = $this->host->callCheckRule('email', 'joao@example.com', 'email', false);
        $this->assertNull($err);
    }

    public function testRuleEmailFailsForInvalidEmail(): void
    {
        $err = $this->host->callCheckRule('email', 'nao-eh-email', 'email', false);
        $this->assertNotNull($err);
    }

    public function testRuleEmailSkipsEmptyValue(): void
    {
        // Email NAO e required, entao null/vazio passa
        $err = $this->host->callCheckRule('email', null, 'email', false);
        $this->assertNull($err);
    }

    public function testRuleMinPassesForLongerString(): void
    {
        $err = $this->host->callCheckRule('senha', '123456', 'min:6', false);
        $this->assertNull($err);
    }

    public function testRuleMinFailsForShorterString(): void
    {
        $err = $this->host->callCheckRule('senha', '123', 'min:6', false);
        $this->assertNotNull($err);
    }

    public function testRuleMinAcceptsMultibyteStrings(): void
    {
        // 'cafe' tem 4 caracteres multibyte
        $err = $this->host->callCheckRule('nome', 'cafe', 'min:4', false);
        $this->assertNull($err);

        $err = $this->host->callCheckRule('nome', 'caf', 'min:4', false);
        $this->assertNotNull($err);
    }

    public function testRuleMaxPassesForShorterString(): void
    {
        $err = $this->host->callCheckRule('sigla', 'SP', 'max:2', false);
        $this->assertNull($err);
    }

    public function testRuleMaxFailsForLongerString(): void
    {
        $err = $this->host->callCheckRule('sigla', 'SPXX', 'max:2', false);
        $this->assertNotNull($err);
    }

    public function testRuleNumericPassesForNumberString(): void
    {
        $err = $this->host->callCheckRule('valor', '123.45', 'numeric', false);
        $this->assertNull($err);
    }

    public function testRuleNumericFailsForNonNumber(): void
    {
        $err = $this->host->callCheckRule('valor', 'abc', 'numeric', false);
        $this->assertNotNull($err);
    }

    public function testRuleIntegerPassesForInteger(): void
    {
        $err = $this->host->callCheckRule('qtd', 5, 'integer', false);
        $this->assertNull($err);
    }

    public function testRuleIntegerFailsForFloat(): void
    {
        $err = $this->host->callCheckRule('qtd', 5.5, 'integer', false);
        $this->assertNotNull($err);
    }

    public function testRuleDatePassesForIsoFormat(): void
    {
        $err = $this->host->callCheckRule('data', '2026-06-15', 'date', false);
        $this->assertNull($err);
    }

    public function testRuleDateFailsForBrazilianFormat(): void
    {
        $err = $this->host->callCheckRule('data', '15/06/2026', 'date', false);
        $this->assertNotNull($err);
    }

    public function testRuleInPassesForAllowedValue(): void
    {
        $err = $this->host->callCheckRule('status', 'Ativo', 'in:Ativo,Inativo,Pendente', false);
        $this->assertNull($err);
    }

    public function testRuleInFailsForDisallowedValue(): void
    {
        $err = $this->host->callCheckRule('status', 'Banana', 'in:Ativo,Inativo,Pendente', false);
        $this->assertNotNull($err);
    }

    public function testRuleInFailsIsCaseSensitive(): void
    {
        $err = $this->host->callCheckRule('status', 'ativo', 'in:Ativo,Inativo', false);
        $this->assertNotNull($err);
    }

    // ---------------------------------------------------------------------
    // _apiValidate — combinacao de regras
    // ---------------------------------------------------------------------

    public function testValidateReturnsEmptyArrayWhenNoRulesDefined(): void
    {
        $this->host->setRules([]);
        $errs = $this->host->callValidate(['nome' => 'x'], false);
        $this->assertSame([], $errs);
    }

    public function testValidateReturnsEmptyArrayWhenAllRulesPass(): void
    {
        $this->host->setRules([
            'nomeCliente' => ['required', 'min:3', 'max:100'],
            'email'       => ['email'],
        ]);
        $errs = $this->host->callValidate([
            'nomeCliente' => 'Joao da Silva',
            'email'       => 'joao@example.com',
        ], false);
        $this->assertSame([], $errs);
    }

    public function testValidateReportsErrorPerField(): void
    {
        $this->host->setRules([
            'nomeCliente' => ['required', 'min:3'],
            'email'       => ['email'],
        ]);
        $errs = $this->host->callValidate([
            'nomeCliente' => 'Jo',
            'email'       => 'invalido',
        ], false);
        $this->assertArrayHasKey('nomeCliente', $errs);
        $this->assertArrayHasKey('email', $errs);
    }

    public function testValidateReportsOnlyFirstErrorPerField(): void
    {
        $this->host->setRules([
            'nomeCliente' => ['required', 'min:3', 'max:100'],
        ]);
        // 'Jo' falha em 'required' e 'min:3' — so 1 deve ser reportado
        $errs = $this->host->callValidate(['nomeCliente' => 'Jo'], false);
        $this->assertCount(1, $errs['nomeCliente']);
    }

    public function testValidateSkipsMissingFieldsThatArentRequired(): void
    {
        $this->host->setRules([
            'observacoes' => ['max:500'],
        ]);
        $errs = $this->host->callValidate([], false);
        $this->assertSame([], $errs);
    }

    // ---------------------------------------------------------------------
    // _apiSanitize — limpeza anti-XSS basica
    // ---------------------------------------------------------------------

    public function testSanitizeTrimsWhitespaceFromStrings(): void
    {
        $result = $this->host->callSanitize(['nome' => '  Joao  ']);
        $this->assertSame('Joao', $result['nome']);
    }

    public function testSanitizeRemovesControlCharacters(): void
    {
        // 0x01 = start of heading; 0x7F = DEL
        $result = $this->host->callSanitize(['nome' => "Jo\x01ao\x7F"]);
        $this->assertSame('Joao', $result['nome']);
    }

    public function testSanitizePreservesRegularCharacters(): void
    {
        $result = $this->host->callSanitize([
            'nome'    => 'Maria Silva',
            'email'   => 'maria@example.com',
            'doc'     => '123.456.789-00',
        ]);
        $this->assertSame('Maria Silva', $result['nome']);
        $this->assertSame('maria@example.com', $result['email']);
        $this->assertSame('123.456.789-00', $result['doc']);
    }

    public function testSanitizeRecursesIntoArrays(): void
    {
        $result = $this->host->callSanitize([
            'itens' => [
                ['nome' => '  Produto A  '],
                ['nome' => "  Produto\x01B  "],
            ],
        ]);
        $this->assertSame('Produto A', $result['itens'][0]['nome']);
        $this->assertSame('Produto B', $result['itens'][1]['nome']);
    }

    public function testSanitizeLeavesNonStringValuesUntouched(): void
    {
        $result = $this->host->callSanitize([
            'id'    => 42,
            'valor' => 123.45,
            'ativo' => true,
        ]);
        $this->assertSame(42, $result['id']);
        $this->assertSame(123.45, $result['valor']);
        $this->assertSame(true, $result['ativo']);
    }

    // ---------------------------------------------------------------------
    // _apiHideFields — mascara colunas sensiveis
    // ---------------------------------------------------------------------

    public function testHideFieldsReturnsRowsUnchangedWhenHiddenIsEmpty(): void
    {
        $this->host->setHidden([]);
        $rows = [
            ['id' => 1, 'nome' => 'Joao', 'senha' => 'hash'],
        ];
        $result = $this->host->callHideFields($rows);
        $this->assertSame($rows, $result);
    }

    public function testHideFieldsReturnsRowsUnchangedWhenRowsAreEmpty(): void
    {
        $this->host->setHidden(['senha']);
        $result = $this->host->callHideFields([]);
        $this->assertSame([], $result);
    }

    public function testHideFieldsRemovesHiddenColumnFromAllRows(): void
    {
        $this->host->setHidden(['senha', 'token']);
        $rows = [
            ['id' => 1, 'nome' => 'Joao',  'senha' => 'h1', 'token' => 't1'],
            ['id' => 2, 'nome' => 'Maria', 'senha' => 'h2', 'token' => 't2'],
        ];
        $result = $this->host->callHideFields($rows);
        foreach ($result as $r) {
            $this->assertArrayNotHasKey('senha', $r);
            $this->assertArrayNotHasKey('token', $r);
        }
        // Campos nao-sensiveis permanecem
        $this->assertSame('Joao', $result[0]['nome']);
        $this->assertSame(2, $result[1]['id']);
    }

    public function testHideFieldsIgnoresFieldsNotPresentInRow(): void
    {
        $this->host->setHidden(['senha']);
        $rows = [
            ['id' => 1, 'nome' => 'Joao'],
        ];
        $result = $this->host->callHideFields($rows);
        $this->assertSame(['id' => 1, 'nome' => 'Joao'], $result[0]);
    }
}
