<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers validar_cpf
 * @covers validar_cnpj
 */
class ValidationHelperTest extends TestCase
{
    /**
     * Known valid CPF: 529.982.247-25
     */
    public function testValidarCpfValid(): void
    {
        $this->assertTrue(
            validar_cpf('52998224725'),
            '52998224725 is a valid CPF'
        );
    }

    /**
     * All-zeros CPF must be rejected.
     */
    public function testValidarCpfInvalid(): void
    {
        $this->assertFalse(
            validar_cpf('00000000000'),
            '00000000000 is an invalid CPF'
        );
    }

    /**
     * Known valid CNPJ: 11.444.777/0001-61
     */
    public function testValidarCnpjValid(): void
    {
        $this->assertTrue(
            validar_cnpj('11444777000161'),
            '11444777000161 is a valid CNPJ'
        );
    }

    /**
     * All-zeros CNPJ must be rejected.
     */
    public function testValidarCnpjInvalid(): void
    {
        $this->assertFalse(
            validar_cnpj('00000000000000'),
            '00000000000000 is an invalid CNPJ'
        );
    }
}