<?php

class WhoopsHook
{
    public function bootWhoops()
    {
        $whoops = new \Whoops\Run;

        // Para requisições API v2, usa handler JSON em vez de HTML
        if (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/v2') !== false) {
            $whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler());
        } else {
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        }

        $whoops->register();
    }
}
