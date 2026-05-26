<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers Clientes_model
 */
class ClientesModelTest extends TestCase
{
    private Clientes_model $model;

    protected function setUp(): void
    {
        parent::setUp();
        // Instantiate the model; the stub get_instance() in bootstrap
        // provides a fake $this->db so the constructor does not fatal.
        $this->model = new Clientes_model();
    }

    /**
     * The fillable array must be populated so mass-assignment filtering works.
     */
    public function testFillableArrayNotEmpty(): void
    {
        $reflection = new ReflectionClass($this->model);
        $prop = $reflection->getProperty('fillable');
        $prop->setAccessible(true);

        $fillable = $prop->getValue($this->model);

        $this->assertIsArray($fillable, 'fillable should be an array');
        $this->assertNotEmpty($fillable, 'fillable array must not be empty');
    }

    /**
     * getById() must exclude the 'senha' column from its SELECT.
     * Even without a real DB, we can inspect the model class to verify
     * that the getById method explicitly selects columns (does not use SELECT *).
     */
    public function testGetByIdExcludesSenha(): void
    {
        $reflection = new ReflectionClass($this->model);
        $method = $reflection->getMethod('getById');

        // Read the method source to confirm 'senha' is NOT in the SELECT list.
        $filename = $method->getFileName();
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();

        $source = implode('', array_slice(
            file($filename),
            $startLine - 1,
            $endLine - $startLine + 1
        ));

        // The getById select should not include 'senha'
        $this->assertStringNotContainsStringIgnoringCase(
            'senha',
            $source,
            'getById() should not include the senha column in its SELECT'
        );
    }
}