<?php
/**
 * Abstract Repository
 * Classe base para todos os repositórios
 */

namespace Repositories;

use CI_Model;
use Interfaces\RepositoryInterface;

abstract class AbstractRepository implements RepositoryInterface
{
    protected CI_Model $model;
    protected string $primaryKey = 'id';
    protected string $tableName = '';

    public function __construct(CI_Model $model)
    {
        $this->model = $model;
    }

    /**
     * Busca registro por ID
     */
    public function find(int $id): ?object
    {
        return $this->model->getById($id);
    }

    /**
     * Busca todos os registros com filtros opcionais
     */
    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $this->model->db->limit($limit, $offset);

        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $this->model->db->where_in($field, $value);
            } else {
                $this->model->db->where($field, $value);
            }
        }

        return $this->model->getAll();
    }

    /**
     * Cria novo registro
     */
    public function create(array $data): int
    {
        $this->model->insert($data);
        return $this->model->db->insert_id();
    }

    /**
     * Atualiza registro existente
     */
    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    /**
     * Remove registro
     */
    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    /**
     * Conta total de registros com filtros
     */
    public function count(array $filters = []): int
    {
        foreach ($filters as $field => $value) {
            $this->model->db->where($field, $value);
        }

        return $this->model->db->count_all_results($this->tableName);
    }
}
