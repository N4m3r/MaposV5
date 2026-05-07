<?php

namespace Libraries\Cache;

class CacheManager
{
    protected $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->driver('cache', ['adapter' => 'file', 'backup' => 'dummy']);
    }

    public function remember(string $key, callable $callback, int $ttl = 300): mixed
    {
        $cached = $this->ci->cache->get($key);
        if ($cached !== false) {
            return $cached;
        }

        $value = $callback();
        $this->ci->cache->save($key, $value, $ttl);
        return $value;
    }

    public function flush(): void
    {
        $this->ci->cache->clean();
    }
}
