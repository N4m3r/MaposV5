<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers Usuarios_model
 */
class UsuariosModelTest extends TestCase
{
    private Usuarios_model $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new Usuarios_model();
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
     * The safeColumns property used by getById/getAll/getByEmail must
     * exclude 'senha' so that passwords are never leaked to the front-end.
     */
    public function testSafeColumnsExcludeSenha(): void
    {
        $reflection = new ReflectionClass($this->model);
        $prop = $reflection->getProperty('safeColumns');
        $prop->setAccessible(true);

        $safeColumns = $prop->getValue($this->model);

        $this->assertIsString($safeColumns, 'safeColumns should be a string');
        $this->assertStringNotContainsStringIgnoringCase(
            'senha',
            $safeColumns,
            'safeColumns must not include the senha column'
        );
    }
}