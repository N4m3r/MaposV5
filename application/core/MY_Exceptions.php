<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Exceptions extends CI_Exceptions
{
    /**
     * Verifica se a requisicao atual e da API v2
     */
    protected function isApiV2(): bool
    {
        return !empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/v2') !== false;
    }

    /**
     * Retorna resposta JSON padronizada para erros da API v2
     */
    protected function apiV2ErrorResponse(string $message, int $code = 500, array $details = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);

        $response = [
            'success' => false,
            'message' => $message,
            'meta' => [
                'timestamp' => date('c'),
                'version' => 'v2'
            ]
        ];

        if (!empty($details)) {
            $response['errors'] = $details;
        }

        echo json_encode($response);
    }

    public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        if ($this->isApiV2()) {
            $this->apiV2ErrorResponse(
                is_array($message) ? implode('; ', $message) : $message,
                $status_code,
                ['heading' => $heading]
            );
            exit;
        }

        return parent::show_error($heading, $message, $template, $status_code);
    }

    public function show_exception($exception)
    {
        if ($this->isApiV2()) {
            $this->apiV2ErrorResponse(
                $exception->getMessage(),
                500,
                [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()
                ]
            );
            exit;
        }

        return parent::show_exception($exception);
    }

    public function show_404($page = '', $log_error = true)
    {
        if ($this->isApiV2()) {
            $this->apiV2ErrorResponse('Endpoint nao encontrado', 404, ['page' => $page]);
            exit;
        }

        return parent::show_404($page, $log_error);
    }

    public function show_php_error($severity, $message, $filepath, $line)
    {
        if ($this->isApiV2()) {
            $this->apiV2ErrorResponse(
                $message,
                500,
                [
                    'severity' => $this->levels[$severity] ?? $severity,
                    'file' => $filepath,
                    'line' => $line
                ]
            );
            exit;
        }

        return parent::show_php_error($severity, $message, $filepath, $line);
    }
}
