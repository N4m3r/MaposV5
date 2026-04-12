<?php
/**
 * API Response Trait
 * Trait para padronização de respostas da API v2
 */

trait ApiResponseTrait
{
    /**
     * Retorna resposta de sucesso
     */
    protected function success($data, string $message = 'Success', int $code = 200): void
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => $message,
                'data' => $data,
                'meta' => [
                    'timestamp' => date('c'),
                    'version' => 'v2'
                ]
            ]));
    }

    /**
     * Retorna resposta de erro
     */
    protected function error(string $message, int $code = 400, array $errors = []): void
    {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'timestamp' => date('c'),
                'version' => 'v2'
            ]
        ];

        if (empty($errors)) {
            unset($response['errors']);
        }

        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Retorna resposta paginada
     */
    protected function paginated(array $data, int $total, int $page, int $perPage): void
    {
        $totalPages = (int) ceil($total / $perPage);

        $this->success($data, 'Success', 200);
    }

    /**
     * Retorna resposta de recurso criado
     */
    protected function created($data, string $message = 'Created'): void
    {
        $this->success($data, $message, 201);
    }

    /**
     * Retorna resposta de recurso atualizado
     */
    protected function updated($data, string $message = 'Updated'): void
    {
        $this->success($data, $message, 200);
    }

    /**
     * Retorna resposta de recurso deletado
     */
    protected function deleted(string $message = 'Deleted'): void
    {
        $this->success(null, $message, 200);
    }

    /**
     * Retorna erro de validação
     */
    protected function validationError(array $errors): void
    {
        $this->error('Validation failed', 422, $errors);
    }

    /**
     * Retorna erro de não encontrado
     */
    protected function notFound(string $resource = 'Resource'): void
    {
        $this->error("{$resource} not found", 404);
    }

    /**
     * Retorna erro de não autorizado
     */
    protected function unauthorized(string $message = 'Unauthorized'): void
    {
        $this->error($message, 401);
    }

    /**
     * Retorna erro de proibido
     */
    protected function forbidden(string $message = 'Forbidden'): void
    {
        $this->error($message, 403);
    }
}
