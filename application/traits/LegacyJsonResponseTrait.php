<?php

defined('BASEPATH') or exit('No direct script access allowed');

namespace Application\Traits;

trait LegacyJsonResponseTrait
{
    /**
     * Return a success JSON response.
     */
    protected function jsonSuccess($data = [], $message = '', int $code = 200)
    {
        $response = array_merge(['result' => true], $data);
        if ($message !== '') {
            $response['message'] = $message;
        }
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($code)
            ->set_output(json_encode($response));
    }

    /**
     * Return an error JSON response.
     */
    protected function jsonError(string $message, int $code = 400, array $data = [])
    {
        $response = array_merge(['result' => false], $data, ['messages' => $message]);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($code)
            ->set_output(json_encode($response));
    }
}