<?php
/**
 * Repository Interface
 * Interface base para todos os repositórios
 */

namespace Interfaces;

interface RepositoryInterface
{
    /**
     * Busca registro por ID
     *
     * @param int $id
     * @return object|null
     */
    public function find(int $id): ?object;

    /**
     * Busca todos os registros com filtros opcionais
     *
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array;

    /**
     * Cria novo registro
     *
     * @param array $data
     * @return int ID do registro criado
     */
    public function create(array $data): int;

    /**
     * Atualiza registro existente
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Remove registro
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Conta total de registros com filtros
     *
     * @param array $filters
     * @return int
     */
    public function count(array $filters = []): int;
}
